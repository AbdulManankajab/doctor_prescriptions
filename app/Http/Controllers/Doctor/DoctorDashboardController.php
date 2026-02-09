<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctorId = Auth::guard('doctor')->id();
        
        $stats = [
            'total_patients' => Patient::where('doctor_id', $doctorId)->count(),
            'total_prescriptions' => Prescription::where('doctor_id', $doctorId)->count(),
            'recent_prescriptions' => Prescription::where('doctor_id', $doctorId)
                ->with('patient')
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('doctor.dashboard', compact('stats'));
    }
}
