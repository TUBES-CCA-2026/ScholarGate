<?php

namespace App\Http\Controllers;

use App\Models\ApplicationDocument;
use App\Models\DocumentType;
use App\Models\Requirement;
use App\Models\StudentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentApplicationController extends Controller
{
    private const DOCUMENT_MIMES = 'pdf,jpg,jpeg,png,doc,docx';
    private const MAX_DOCUMENT_SIZE_KB = 4096;

    public function index(Request $request): View
    {
        $applications = StudentApplication::whereBelongsTo($request->user())
            ->with(['documentType', 'documents.requirement'])
            ->latest()
            ->paginate(10);

        return view('student.applications.index', compact('applications'));
    }

    public function create(): View
    {
        $documentTypes = DocumentType::where('is_active', true)
            ->with('requirements')
            ->latest()
            ->get();

        return view('student.applications.create', compact('documentTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->storeRules());

        $documentType = DocumentType::with('requirements')->findOrFail($validated['document_type_id']);

        $application = DB::transaction(function () use ($request, $documentType, $validated): StudentApplication {
            $application = StudentApplication::create([
                'user_id' => $request->user()->id,
                'document_type_id' => $documentType->id,
                'application_code' => $this->generateApplicationCode(),
                'purpose' => $validated['purpose'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            $documentType->requirements->each(function (Requirement $requirement) use ($request, $application): void {
                $this->createApplicationDocument($request, $application, $requirement);
            });

            return $application;
        });

        return redirect()
            ->route('student.applications.show', $application)
            ->with('success', 'Pengajuan berhasil dikirim ke prodi.');
    }

    public function show(Request $request, StudentApplication $studentApplication): View
    {
        $this->authorizeView($request, $studentApplication);

        $studentApplication->load(['documentType', 'documents.requirement', 'user']);

        return view('student.applications.show', compact('studentApplication'));
    }

    public function reviseDocument(
        Request $request,
        StudentApplication $studentApplication,
        ApplicationDocument $applicationDocument
    ): RedirectResponse {
        $this->authorizeRevision($request, $studentApplication, $applicationDocument);

        $request->validate([
            'revision_file' => [
                'required',
                'file',
                'mimes:' . self::DOCUMENT_MIMES,
                'max:' . self::MAX_DOCUMENT_SIZE_KB,
            ],
        ]);

        $this->replaceDocumentFile($applicationDocument, $request->file('revision_file'));
        $this->resubmitApplicationWhenRevisionIsComplete($studentApplication);

        return back()->with('success', 'Berkas revisi berhasil diunggah.');
    }

    private function storeRules(): array
    {
        return [
            'document_type_id' => ['required', 'exists:document_types,id'],
            'purpose' => ['required', 'string', 'max:2000'],
            'requirement_files' => ['nullable', 'array'],
            'requirement_files.*' => [
                'nullable',
                'file',
                'mimes:' . self::DOCUMENT_MIMES,
                'max:' . self::MAX_DOCUMENT_SIZE_KB,
            ],
            'manual_checks' => ['nullable', 'array'],
        ];
    }

    private function generateApplicationCode(): string
    {
        return 'APP-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
    }

    private function createApplicationDocument(
        Request $request,
        StudentApplication $application,
        Requirement $requirement
    ): void {
        $uploadedFile = $request->file("requirement_files.{$requirement->id}");
        $isCheckedManual = in_array((string) $requirement->id, $request->input('manual_checks', []), true);

        ApplicationDocument::create([
            'student_application_id' => $application->id,
            'requirement_id' => $requirement->id,
            'file_path' => $uploadedFile?->store('application-documents', 'public'),
            'original_name' => $uploadedFile?->getClientOriginalName(),
            'is_checked_manual' => $isCheckedManual,
            'expired_at' => $this->calculateExpirationDate($requirement),
            'status' => $this->initialDocumentStatus($uploadedFile, $isCheckedManual),
        ]);
    }

    private function calculateExpirationDate(Requirement $requirement): ?\Illuminate\Support\Carbon
    {
        if (! $requirement->has_expiry || ! $requirement->valid_days) {
            return null;
        }

        return now()->addDays($requirement->valid_days);
    }

    private function initialDocumentStatus(?UploadedFile $uploadedFile, bool $isCheckedManual): string
    {
        return ($uploadedFile || $isCheckedManual) ? 'submitted' : 'missing';
    }

    private function replaceDocumentFile(ApplicationDocument $document, UploadedFile $file): void
    {
        $this->deletePublicFile($document->file_path);

        $document->update([
            'file_path' => $file->store('application-documents', 'public'),
            'original_name' => $file->getClientOriginalName(),
            'is_checked_manual' => false,
            'status' => 'submitted',
            'note' => null,
        ]);
    }

    private function resubmitApplicationWhenRevisionIsComplete(StudentApplication $application): void
    {
        $stillNeedRevision = $application->documents()
            ->whereIn('status', ['invalid', 'missing'])
            ->exists();

        if (! $stillNeedRevision) {
            $application->update(['status' => 'submitted']);
        }
    }

    private function authorizeView(Request $request, StudentApplication $application): void
    {
        abort_unless($application->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
    }

    private function authorizeRevision(
        Request $request,
        StudentApplication $application,
        ApplicationDocument $document
    ): void {
        abort_unless($application->user_id === $request->user()->id, 403);
        abort_unless($document->student_application_id === $application->id, 404);
        abort_unless($application->status === 'revision' && in_array($document->status, ['invalid', 'missing'], true), 403);
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
