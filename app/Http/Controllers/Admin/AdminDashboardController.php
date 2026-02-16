<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Doctor;
use App\Models\Examination;
use App\Models\Diagnosis;
use App\Models\Visit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalDoctors = Doctor::count();
        $totalPatients = Patient::count();
        $totalPrescriptions = Prescription::count();
        $totalExaminations = Examination::count();
        $totalVisits = Visit::count();
        $totalPharmacyUsers = \App\Models\PharmacyUser::count();
        
        $pendingPrescriptions = Prescription::where('status', 'sent')->count();
        $dispensedPrescriptions = Prescription::where('status', 'dispensed')->count();

        $topDiagnoses = Diagnosis::select('primary_diagnosis', \DB::raw('count(*) as count'))
            ->groupBy('primary_diagnosis')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        $recentPrescriptions = Prescription::with(['patient', 'doctor'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalDoctors', 
            'totalPatients', 
            'totalPrescriptions', 
            'totalExaminations', 
            'totalPharmacyUsers',
            'pendingPrescriptions',
            'dispensedPrescriptions',
            'totalVisits',
            'topDiagnoses', 
            'recentPrescriptions'
        ));
    }

    public function exportAnonymized(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="anonymized_medical_data_'.date('Y-m-d').'.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'Age', 'Gender', 'Diagnosis', 'Notes_Summary']);

            $data = Prescription::with(['patient', 'diagnosisRecord', 'examination'])
                ->latest()
                ->get();

            foreach ($data as $row) {
                fputcsv($file, [
                    'MED-' . str_pad($row->id, 6, '0', STR_PAD_LEFT),
                    $row->created_at->format('Y-m-d'),
                    $row->patient->age ?? 'N/A',
                    $row->patient->gender ?? 'N/A',
                    $row->diagnosisRecord->primary_diagnosis ?? $row->diagnosis,
                    Str::limit($row->examination->notes ?? 'N/A', 100)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
