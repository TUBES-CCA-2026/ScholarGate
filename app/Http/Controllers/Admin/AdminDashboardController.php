<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\StudentApplication;
use App\Models\User;
use Illuminate\View\View;

/**
 * Menyusun ringkasan operasional untuk dashboard admin prodi.
 */
class AdminDashboardController extends Controller
{
    /**
     * Menampilkan metrik utama dan enam pengajuan terbaru.
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            'summary' => $this->summary(),
            'latestApplications' => StudentApplication::with(['user', 'documentType'])->latest()->take(6)->get(),
        ]);
    }

    /**
     * Menghitung data agregat ringan yang ditampilkan pada kartu dashboard.
     */
    private function summary(): array
    {
        return [
            'students' => User::where('role', User::ROLE_STUDENT)->count(),
            'document_types' => DocumentType::count(),
            'submitted' => StudentApplication::where('status', StudentApplication::STATUS_SUBMITTED)->count(),
            'in_review' => StudentApplication::where('status', StudentApplication::STATUS_IN_REVIEW)->count(),
        ];
    }
}
