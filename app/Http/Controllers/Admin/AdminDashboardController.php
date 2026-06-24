<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\StudentApplication;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'summary' => $this->summary(),
            'latestApplications' => StudentApplication::with(['user', 'documentType'])->latest()->take(6)->get(),
        ]);
    }

    private function summary(): array
    {
        return [
            'students' => User::where('role', 'student')->count(),
            'document_types' => DocumentType::count(),
            'submitted' => StudentApplication::where('status', 'submitted')->count(),
            'in_review' => StudentApplication::where('status', 'in_review')->count(),
        ];
    }
}
