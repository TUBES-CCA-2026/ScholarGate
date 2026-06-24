<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\StudentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminApplicationController extends Controller
{
    private const APPLICATION_STATUSES = 'submitted,in_review,revision,approved,rejected,completed';
    private const DOCUMENT_STATUSES = 'missing,submitted,valid,invalid';

    public function index(Request $request): View
    {
        $applications = StudentApplication::with(['user', 'documentType'])
            ->latest()
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($query) => $this->applyKeywordFilter($query, (string) $request->input('q')))
            ->paginate(12)
            ->withQueryString();

        return view('admin.applications.index', compact('applications'));
    }

    public function show(StudentApplication $studentApplication): View
    {
        $studentApplication->load(['user', 'documentType', 'documents.requirement']);

        return view('admin.applications.show', compact('studentApplication'));
    }

    public function updateStatus(Request $request, StudentApplication $studentApplication): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . self::APPLICATION_STATUSES],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $studentApplication->update($validated);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function updateDocument(
        Request $request,
        StudentApplication $studentApplication,
        ApplicationDocument $applicationDocument
    ): RedirectResponse {
        abort_unless($applicationDocument->student_application_id === $studentApplication->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:' . self::DOCUMENT_STATUSES],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $applicationDocument->update($validated);
        $this->markApplicationAsRevisionWhenDocumentIsInvalid($studentApplication, $validated);

        return back()->with('success', 'Status dokumen berhasil diperbarui.');
    }

    private function applyKeywordFilter($query, string $keyword): void
    {
        $query->whereHas('user', function ($userQuery) use ($keyword): void {
            $userQuery->where('name', 'like', "%{$keyword}%")
                ->orWhere('nim', 'like', "%{$keyword}%");
        });
    }

    private function markApplicationAsRevisionWhenDocumentIsInvalid(StudentApplication $application, array $validated): void
    {
        if ($validated['status'] !== 'invalid') {
            return;
        }

        $application->update([
            'status' => 'revision',
            'admin_note' => $validated['note'] ?: 'Terdapat berkas yang perlu direvisi.',
        ]);
    }
}
