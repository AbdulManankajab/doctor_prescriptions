<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\DefaultPrescriptionDetail;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicPrescriptionController extends Controller
{
    public function index()
    {
        return view('public.index');
    }

    public function searchPatient(Request $request)
    {
        $query = $request->input('search');
        
        $patients = Patient::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('patient_number', 'LIKE', "%{$query}%")
            ->with(['prescriptions' => function($q) {
                $q->latest();
            }])
            ->get();

        return response()->json($patients);
    }

    public function createPrescriptionForm($patientId = null)
    {
        $patient = null;
        if ($patientId) {
            $patient = Patient::findOrFail($patientId);
        }
        
        $medicines = Medicine::all();
        $defaultNotes = DefaultPrescriptionDetail::all();
        
        return view('public.prescription-form', compact('patient', 'medicines', 'defaultNotes'));
    }

    public function storePrescription(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'diagnosis' => 'required|string',
            'notes' => 'nullable|string',
            'medicines' => 'required|array|min:1',
            'medicines.*.medicine_id' => 'required|exists:medicines,id',
            'medicines.*.type' => 'required|string',
            'medicines.*.dosage' => 'required|string',
            'medicines.*.duration' => 'required|string',
            'medicines.*.instructions' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Check if patient exists by phone
            $patient = Patient::where('phone', $request->phone)->first();

            if (!$patient) {
                // Create new patient
                $patient = Patient::create([
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            } else {
                // Update patient info if exists
                $patient->update([
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'address' => $request->address,
                ]);
            }

            // Create prescription
            $prescription = Prescription::create([
                'patient_id' => $patient->id,
                'diagnosis' => $request->diagnosis,
                'notes' => $request->notes,
            ]);

            // Create prescription items
            foreach ($request->medicines as $item) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $item['medicine_id'],
                    'type' => $item['type'],
                    'dosage' => $item['dosage'],
                    'duration' => $item['duration'],
                    'instructions' => $item['instructions'],
                ]);
            }

            DB::commit();

            return redirect()->route('prescription.print', $prescription->id)
                ->with('success', 'Prescription created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error creating prescription: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function printPrescription($id)
    {
        $prescription = Prescription::with(['patient', 'items.medicine'])->findOrFail($id);
        return view('public.prescription-print', compact('prescription'));
    }

    public function patientHistory($id)
    {
        $patient = Patient::with(['prescriptions.items.medicine'])->findOrFail($id);
        return view('public.patient-history', compact('patient'));
    }
}
