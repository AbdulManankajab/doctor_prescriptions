<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PharmacyDashboardController extends Controller
{
    public function index(Request $request)
    {
        $pharmacyUser = Auth::guard('pharmacy')->user();
        $query = Prescription::query()
            ->with(['patient', 'doctor'])
            ->where('status', 'sent'); // Strictly show only sent prescriptions as per status rules

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('prescription_number', 'LIKE', "%{$search}%");
        }

        $prescriptions = $query->latest()->paginate(10);

        return view('pharmacy.dashboard', compact('prescriptions'));
    }

    public function show($id)
    {
        $prescription = Prescription::with(['patient', 'doctor', 'items.medicine', 'examination', 'dispensedBy'])
            ->whereIn('status', ['sent', 'final', 'dispensed'])
            ->findOrFail($id);

        return view('pharmacy.prescriptions.show', compact('prescription'));
    }

    public function dispense($id)
    {
        $prescription = Prescription::findOrFail($id);

        if ($prescription->status === 'dispensed') {
            return back()->with('error', 'This prescription has already been dispensed.');
        }

        if ($prescription->status !== 'sent') {
            return back()->with('error', 'This prescription is not ready for dispensing.');
        }

        $prescription->update([
            'status' => 'dispensed',
            'dispensed_at' => now(),
            'dispensed_by' => Auth::guard('pharmacy')->id(),
        ]);

        Log::info("Prescription #{$prescription->prescription_number} dispensed by Pharmacy User ID: " . Auth::guard('pharmacy')->id());

        return back()->with('success', 'Prescription marked as dispensed.');
    }

    public function print($id)
    {
        $prescription = Prescription::with(['patient', 'items.medicine', 'doctor'])
            ->findOrFail($id);
            
        return view('doctor.prescriptions.print', compact('prescription')); // Reusing existing print view
    }
}
