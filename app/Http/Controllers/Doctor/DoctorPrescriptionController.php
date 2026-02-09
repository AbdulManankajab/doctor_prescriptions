<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\DefaultPrescriptionDetail;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Examination;
use App\Models\ExaminationFile;
use App\Models\Diagnosis;
use App\Models\PatientAllergy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DoctorPrescriptionController extends Controller
{
    public function searchPatient(Request $request)
    {
        $query = $request->input('search');
        $doctorId = Auth::guard('doctor')->id();
        
        $patients = Patient::where('doctor_id', $doctorId)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%")
                  ->orWhere('patient_number', 'LIKE', "%{$query}%");
            })
            ->with(['prescriptions' => function($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId)->latest();
            }])
            ->get();

        return response()->json($patients);
    }

    public function create($patientId = null)
    {
        $doctorId = Auth::guard('doctor')->id();
        $patient = null;
        if ($patientId) {
            $patient = Patient::where('doctor_id', $doctorId)
                ->with(['allergies', 'examinations.files', 'diagnoses'])
                ->findOrFail($patientId);
        }
        
        $medicines = Medicine::all();
        $defaultNotes = DefaultPrescriptionDetail::all();
        
        return view('doctor.prescriptions.create', compact('patient', 'medicines', 'defaultNotes'));
    }

    public function store(Request $request)
    {
        $doctor = Auth::guard('doctor')->user();
        $doctorId = $doctor->id;

        // Check for active facility
        if ($doctor->facility_id) {
            $facility = $doctor->facility;
            if (!$facility->status) {
                return back()->withErrors(['error' => 'Your assigned facility is currently inactive. You cannot issue prescriptions until it is reactivated.'])
                    ->withInput();
            }
            $facilitySnapshot = $facility->toArray();
        } else {
            $facilitySnapshot = null;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'examination_notes' => 'nullable|string',
            'examination_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'primary_diagnosis' => 'nullable|string|max:255',
            'secondary_diagnosis' => 'nullable|string|max:255',
            'allergies' => 'nullable|array',
            'allergies.*.name' => 'nullable|string|max:255',
            'allergies.*.type' => 'required_with:allergies.*.name|in:medicine,food,other',
            'medicines' => 'required|array|min:3',
            'medicines.*.medicine_id' => 'required|exists:medicines,id',
            'medicines.*.type' => 'required|string',
            'medicines.*.dosage' => 'required|string',
            'medicines.*.duration' => 'required|string',
            'medicines.*.instructions' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Check if patient exists by phone AND doctor_id
            $patient = Patient::where('phone', $request->phone)
                ->where('doctor_id', $doctorId)
                ->first();

            if (!$patient) {
                // Create new patient for this doctor
                $patient = Patient::create([
                    'doctor_id' => $doctorId,
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            } else {
                // Update patient info
                $patient->update([
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'address' => $request->address,
                ]);
            }

            // Save/Update Allergies
            if ($request->has('allergies')) {
                foreach ($request->allergies as $allergyData) {
                    if (empty($allergyData['name'])) continue;
                    
                    PatientAllergy::updateOrCreate(
                        [
                            'patient_id' => $patient->id,
                            'allergy_name' => $allergyData['name']
                        ],
                        ['allergy_type' => $allergyData['type']]
                    );
                }
            }

            // Create Examination
            $examination = Examination::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'notes' => $request->examination_notes,
            ]);

            // Handle Examination Files
            if ($request->hasFile('examination_files')) {
                foreach ($request->file('examination_files') as $file) {
                    $path = $file->store('examinations/' . $examination->id, 'public');
                    ExaminationFile::create([
                        'examination_id' => $examination->id,
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                    ]);
                }
            }

            // Create Diagnosis
            $diagnosisObj = Diagnosis::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'primary_diagnosis' => $request->primary_diagnosis,
                'secondary_diagnosis' => $request->secondary_diagnosis,
            ]);

            // Create prescription linked to doctor, examination and diagnosis
            $diagnosisSummary = $request->primary_diagnosis ?: 'No specific diagnosis';
            if ($request->secondary_diagnosis) {
                $diagnosisSummary .= ' / ' . $request->secondary_diagnosis;
            }

            $prescription = Prescription::create([
                'doctor_id' => $doctorId,
                'patient_id' => $patient->id,
                'examination_id' => $examination->id,
                'diagnosis_id' => $diagnosisObj->id,
                'diagnosis' => $diagnosisSummary,
                'notes' => $request->notes,
                'facility_snapshot' => $facilitySnapshot,
            ]);

            // Create items
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

            return redirect()->route('doctor.prescriptions.print', $prescription->id)
                ->with('success', 'Prescription created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error creating prescription: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function sendToPharmacy($id)
    {
        $doctorId = Auth::guard('doctor')->id();
        $prescription = Prescription::where('doctor_id', $doctorId)->findOrFail($id);

        if ($prescription->status !== 'draft') {
            return back()->with('error', 'This prescription has already been sent or dispensed.');
        }

        $prescription->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        Log::info("Prescription #{$prescription->prescription_number} sent to pharmacy by Doctor ID: {$doctorId}");

        return back()->with('success', 'Prescription sent to hospital pharmacy successfully!');
    }

    public function print($id)
    {
        $doctorId = Auth::guard('doctor')->id();
        $prescription = Prescription::where('doctor_id', $doctorId)
            ->with(['patient', 'items.medicine'])
            ->findOrFail($id);
            
        return view('doctor.prescriptions.print', compact('prescription'));
    }

    public function history($id)
    {
        $doctorId = Auth::guard('doctor')->id();
        $patient = Patient::where('doctor_id', $doctorId)
            ->with(['prescriptions' => function($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId)->with('items.medicine')->latest();
            }])
            ->findOrFail($id);
            
        return view('doctor.prescriptions.history', compact('patient'));
    }

    public function show($id)
    {
        $doctorId = Auth::guard('doctor')->id();
        $prescription = Prescription::where('doctor_id', $doctorId)
            ->with(['patient', 'items.medicine', 'doctor'])
            ->findOrFail($id);
            
        return view('doctor.prescriptions.show', compact('prescription'));
    }
}
