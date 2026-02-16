<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\LaboratoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorLaboratoryController extends Controller
{
    public function create(Patient $patient, Request $request)
    {
        $prescriptionId = $request->query('prescription_id');
        $visitId = $request->query('visit_id');
        return view('doctor.laboratory.create', compact('patient', 'prescriptionId', 'visitId'));
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'requested_tests' => 'required|string',
            'clinical_notes' => 'nullable|string',
            'priority' => 'required|in:Normal,Urgent',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'visit_id' => 'nullable|exists:visits,id',
        ]);

        LaboratoryRequest::create([
            'patient_id' => $patient->id,
            'doctor_id' => Auth::guard('doctor')->id(),
            'prescription_id' => $request->prescription_id,
            'visit_id' => $request->visit_id,
            'requested_tests' => $request->requested_tests,
            'clinical_notes' => $request->clinical_notes,
            'priority' => $request->priority,
            'status' => 'Pending',
        ]);

        if ($request->prescription_id) {
            return redirect()->route('doctor.prescriptions.show', $request->prescription_id)
                ->with('success', 'Laboratory request linked to prescription successfully.');
        }

        return redirect()->route('doctor.prescription.create', $patient->id)
            ->with('success', 'Laboratory request created successfully.');
    }

    public function history(Patient $patient)
    {
        $requests = LaboratoryRequest::where('patient_id', $patient->id)
            ->with(['doctor', 'completedBy', 'files'])
            ->latest()
            ->get();
            
        return view('doctor.laboratory.history', compact('patient', 'requests'));
    }
}
