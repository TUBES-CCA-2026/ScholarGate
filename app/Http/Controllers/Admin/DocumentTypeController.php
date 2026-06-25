<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

/**
 * Mengelola master beasiswa atau jenis pengajuan akademik.
 *
 * Master disimpan pada tabel document_types, sedangkan syarat dokumen disimpan
 * terpisah pada requirements. Pemisahan ini menjaga struktur data tetap normal
 * dan mencegah daftar syarat menjadi teks tidak terstruktur di tabel master.
 */
class DocumentTypeController extends Controller
{
    /**
     * Menampilkan daftar master beserta seluruh syarat dokumennya.
     */
    public function index(): View
    {
        return view('admin.document-types.index', [
            'documentTypes' => DocumentType::with('requirements')->latest()->get(),
        ]);
    }

    /**
     * Menyimpan master beasiswa baru dan memecah daftar syarat dari textarea.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->storeRules());
        $requirementsText = $validated['requirements'] ?? null;
        $storedImage = null;

        unset($validated['requirements'], $validated['image']);

        try {
            if ($request->hasFile('image')) {
                $storedImage = $request->file('image')->store('document-type-images', 'public');
                $validated['image_path'] = $storedImage;
            }

            DB::transaction(function () use ($validated, $requirementsText): void {
                $documentType = DocumentType::create($validated + ['is_active' => true]);
                $this->createRequirementsFromText($documentType, $requirementsText);
            });
        } catch (Throwable $exception) {
            $this->deletePublicFile($storedImage);

            throw $exception;
        }

        return back()->with('success', 'Jenis pengajuan berhasil ditambahkan.');
    }

    /**
     * Memperbarui master beasiswa, foto, status aktif, dan daftar syarat.
     *
     * Operasi data dilakukan dalam transaction. File lama baru dihapus setelah
     * transaction berhasil agar data database dan storage tetap konsisten.
     */
    public function update(Request $request, DocumentType $documentType): RedirectResponse
    {
        $validated = $request->validate($this->updateRules());
        $requirementRows = $validated['requirements'] ?? [];
        $oldImagePath = $documentType->image_path;
        $newImagePath = null;
        $removeImage = $request->boolean('remove_image');

        unset($validated['requirements'], $validated['image'], $validated['remove_image'], $validated['editing_id']);

        $validated['is_active'] = $request->boolean('is_active');

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')->store('document-type-images', 'public');
                $validated['image_path'] = $newImagePath;
            } elseif ($removeImage) {
                $validated['image_path'] = null;
            }

            DB::transaction(function () use ($documentType, $validated, $requirementRows): void {
                $documentType->update($validated);
                $this->syncRequirementRows($documentType, $requirementRows);
            });
        } catch (Throwable $exception) {
            $this->deletePublicFile($newImagePath);

            throw $exception;
        }

        if (($newImagePath || $removeImage) && $oldImagePath && $oldImagePath !== $newImagePath) {
            $this->deletePublicFile($oldImagePath);
        }

        return back()->with('success', 'Jenis pengajuan berhasil diperbarui.');
    }

    /**
     * Menghapus master beasiswa dan foto pendukungnya.
     *
     * Relasi syarat, bookmark, dan pengajuan akan mengikuti aturan cascade dari
     * migration sehingga tidak meninggalkan data yatim.
     */
    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $imagePath = $documentType->image_path;

        $documentType->delete();
        $this->deletePublicFile($imagePath);

        return back()->with('success', 'Jenis pengajuan berhasil dihapus.');
    }

    /**
     * Aturan validasi untuk pembuatan master baru.
     */
    private function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:100'],
            'provider' => ['nullable', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date'],
            'registration_link' => ['nullable', 'url', 'max:2048'],
            'requirements' => ['nullable', 'string', 'max:3000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    /**
     * Aturan validasi untuk pembaruan master dan daftar syarat.
     */
    private function updateRules(): array
    {
        return [
            'editing_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:100'],
            'provider' => ['nullable', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date'],
            'registration_link' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'requirements' => ['nullable', 'array', 'max:100'],
            'requirements.*.id' => ['nullable', 'integer'],
            'requirements.*.name' => ['nullable', 'string', 'max:255', 'distinct'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Mengubah input textarea menjadi beberapa baris requirements.
     */
    private function createRequirementsFromText(DocumentType $documentType, ?string $text): void
    {
        collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->unique()
            ->each(fn (string $line) => $documentType->requirements()->create($this->requirementPayload($line)));
    }

    /**
     * Menyinkronkan syarat yang dikirim dari modal edit master.
     *
     * Syarat yang sudah dipakai pada pengajuan mahasiswa tidak dihapus agar
     * riwayat dokumen lama tetap dapat dilacak.
     */
    private function syncRequirementRows(DocumentType $documentType, array $rows): void
    {
        $existingRequirements = $documentType->requirements()
            ->withCount('applicationDocuments')
            ->orderBy('id')
            ->get()
            ->keyBy('id');

        $submittedIds = collect();

        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $requirementId = isset($row['id']) && $row['id'] !== '' ? (int) $row['id'] : null;

            if ($requirementId) {
                $requirement = $existingRequirements->get($requirementId);

                if (! $requirement) {
                    throw ValidationException::withMessages([
                        'requirements' => 'Data syarat berkas tidak valid atau bukan milik master yang sedang diedit.',
                    ]);
                }

                $requirement->update($this->requirementPayload($name));
                $submittedIds->push($requirementId);
            } else {
                $documentType->requirements()->create($this->requirementPayload($name));
            }
        }

        $this->deleteRemovedRequirements($existingRequirements, $submittedIds);
    }

    /**
     * Menghapus syarat yang tidak lagi dikirim, kecuali sudah digunakan pada pengajuan.
     */
    private function deleteRemovedRequirements(Collection $existingRequirements, $submittedIds): void
    {
        $requirementsToRemove = $existingRequirements->reject(
            fn ($requirement): bool => $submittedIds->contains($requirement->id)
        );

        $usedRequirement = $requirementsToRemove->first(
            fn ($requirement): bool => $requirement->application_documents_count > 0
        );

        if ($usedRequirement) {
            throw ValidationException::withMessages([
                'requirements' => "Syarat '{$usedRequirement->name}' tidak dapat dihapus karena sudah digunakan dalam pengajuan mahasiswa. Syarat tersebut tetap dapat diganti namanya.",
            ]);
        }

        $requirementsToRemove->each->delete();
    }

    /**
     * Payload standar requirement yang dibuat dari input ringkas admin.
     */
    private function requirementPayload(string $name): array
    {
        return [
            'name' => $name,
            'description' => $name,
            'is_required' => true,
            'needs_file' => true,
            'has_expiry' => false,
        ];
    }

    /**
     * Menghapus file dari disk public jika path tersedia.
     */
    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
