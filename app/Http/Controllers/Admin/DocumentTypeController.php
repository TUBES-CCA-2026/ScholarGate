<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class DocumentTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.document-types.index', [
            'documentTypes' => DocumentType::with('requirements')->latest()->get(),
        ]);
    }

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

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $imagePath = $documentType->image_path;

        $documentType->delete();
        $this->deletePublicFile($imagePath);

        return back()->with('success', 'Jenis pengajuan berhasil dihapus.');
    }

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
            'requirements.*.name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    private function createRequirementsFromText(DocumentType $documentType, ?string $text): void
    {
        collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->each(fn (string $line) => $documentType->requirements()->create($this->requirementPayload($line)));
    }

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

    private function deleteRemovedRequirements($existingRequirements, $submittedIds): void
    {
        $requirementsToRemove = $existingRequirements->reject(
            fn ($requirement) => $submittedIds->contains($requirement->id)
        );

        $usedRequirement = $requirementsToRemove->first(
            fn ($requirement) => $requirement->application_documents_count > 0
        );

        if ($usedRequirement) {
            throw ValidationException::withMessages([
                'requirements' => "Syarat '{$usedRequirement->name}' tidak dapat dihapus karena sudah digunakan dalam pengajuan mahasiswa. Syarat tersebut tetap dapat diganti namanya.",
            ]);
        }

        $requirementsToRemove->each->delete();
    }

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

    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
