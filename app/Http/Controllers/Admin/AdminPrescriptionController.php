<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;

class AdminPrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['patient', 'doctor']);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $prescriptions = $query->latest()->paginate(10);
        $doctors = \App\Models\Doctor::all();

        return view('admin.prescriptions.index', compact('prescriptions', 'doctors'));
    }

    public function show($id)
    {
        $prescription = Prescription::with(['patient', 'items.medicine', 'dispensedBy'])
            ->findOrFail($id);

        return view('admin.prescriptions.show', compact('prescription'));
    }

    public function print($id)
    {
        $prescription = Prescription::with(['patient', 'items.medicine', 'doctor'])
            ->findOrFail($id);
            
        return view('doctor.prescriptions.print', compact('prescription'));
    }

    public function destroy($id)
    {
        $prescription = Prescription::findOrFail($id);
        $prescription->delete();

        return redirect()->route('admin.prescriptions.index')
            ->with('success', 'Prescription deleted successfully');
    }
}
