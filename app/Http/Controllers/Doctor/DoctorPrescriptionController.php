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
use App\Models\Visit;
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
        
        // Search by visit number first
        $visit = Visit::where('visit_number', $query)
            ->where('assigned_doctor_id', $doctorId)
            ->with('patient')
            ->first();

        if ($visit) {
            $patient = $visit->patient;
            $patient->visit_id = $visit->id; // Attach visit_id to patient object for frontend
            return response()->json([$patient]);
        }

        $patients = Patient::where(function($q) use ($query) {
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

    public function create(Request $request, $patientId = null)
    {
        $doctorId = Auth::guard('doctor')->id();
        $patient = null;
        $visitId = $request->query('visit_id');
        $visit = null;

        if ($visitId) {
            $visit = Visit::where('assigned_doctor_id', $doctorId)->findOrFail($visitId);
            $patientId = $visit->patient_id;
        }

        if ($patientId) {
            $patient = Patient::with(['allergies', 'examinations.files', 'diagnoses'])
                ->findOrFail($patientId);
        }
        
        $medicines = Medicine::all();
        $defaultNotes = DefaultPrescriptionDetail::all();
        
        return view('doctor.prescriptions.create', compact('patient', 'medicines', 'defaultNotes', 'visit'));
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
            'visit_id' => 'nullable|exists:visits,id',
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
            'status' => 'nullable|in:draft,final',
            // Medicines only required if status is final
            'medicines' => 'required_if:status,final|array',
            'medicines.*.medicine_id' => 'required_with:medicines|exists:medicines,id',
            'medicines.*.type' => 'required_with:medicines|string',
            'medicines.*.dosage' => 'required_with:medicines|string',
            'medicines.*.duration' => 'required_with:medicines|string',
            'medicines.*.instructions' => 'required_with:medicines|string',
        ], [
            'medicines.required_if' => 'Medicines must be added to finalize the prescription.'
        ]);

        DB::beginTransaction();
        try {
            $visitSelected = null;
            if ($request->visit_id) {
                $visitSelected = Visit::find($request->visit_id);
            }

            // Global patient lookup
            $patient = Patient::where('phone', $request->phone)->first();

            if (!$patient) {
                $patient = Patient::create([
                    'doctor_id' => $doctorId,
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            } else {
                if (!$patient->doctor_id) {
                    $patient->doctor_id = $doctorId;
                }
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
                        ['patient_id' => $patient->id, 'allergy_name' => $allergyData['name']],
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

            $diagnosisSummary = $request->primary_diagnosis ?: 'No specific diagnosis';
            if ($request->secondary_diagnosis) {
                $diagnosisSummary .= ' / ' . $request->secondary_diagnosis;
            }

            $status = $request->input('status', 'draft');

            $prescriptionData = [
                'doctor_id' => $doctorId,
                'patient_id' => $patient->id,
                'visit_id' => $request->visit_id,
                'examination_id' => $examination->id,
                'diagnosis_id' => $diagnosisObj->id,
                'diagnosis' => $diagnosisSummary,
                'notes' => $request->notes,
                'facility_snapshot' => $facilitySnapshot,
                'status' => $status,
            ];

            if ($visitSelected) {
                $prescriptionData['prescription_number'] = $visitSelected->visit_number;
            }

            $prescription = Prescription::create($prescriptionData);

            // If finalized, mark visit as completed
            if ($status === 'final' && $visitSelected) {
                $visitSelected->update(['status' => 'completed']);
            }

            // Create items (if any)
            if ($request->has('medicines')) {
                foreach ($request->medicines as $item) {
                    if (empty($item['medicine_id'])) continue;
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_id' => $item['medicine_id'],
                        'type' => $item['type'],
                        'dosage' => $item['dosage'],
                        'duration' => $item['duration'],
                        'instructions' => $item['instructions'],
                    ]);
                }
            }

            DB::commit();

            if ($status === 'final') {
                return redirect()->route('doctor.prescriptions.print', $prescription->id)
                    ->with('success', 'Prescription finalized successfully!');
            }

            return redirect()->route('doctor.prescriptions.show', $prescription->id)
                ->with('success', 'Draft prescription created successfully! You can now request investigations.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error creating prescription: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $doctorId = Auth::guard('doctor')->id();
        $prescription = Prescription::where('doctor_id', $doctorId)
            ->with(['patient', 'items.medicine', 'doctor', 'radiologyRequests.files', 'laboratoryRequests.files'])
            ->findOrFail($id);
            
        return view('doctor.prescriptions.show', compact('prescription'));
    }

    public function update(Request $request, $id)
    {
        $doctorId = Auth::guard('doctor')->id();
        $prescription = Prescription::where('doctor_id', $doctorId)->findOrFail($id);

        if ($prescription->status !== 'draft') {
            return back()->with('error', 'Only draft prescriptions can be updated.');
        }

        $request->validate([
            'notes' => 'nullable|string',
            'medicines' => 'required|array|min:1',
            'medicines.*.medicine_id' => 'required|exists:medicines,id',
            'medicines.*.type' => 'required|string',
            'medicines.*.dosage' => 'required|string',
            'medicines.*.duration' => 'required|string',
            'medicines.*.instructions' => 'required|string',
            'status' => 'required|in:final',
        ]);

        DB::beginTransaction();
        try {
            $prescription->update([
                'notes' => $request->notes,
                'status' => 'final',
            ]);

            if ($prescription->visit_id) {
                Visit::where('id', $prescription->visit_id)->update(['status' => 'completed']);
            }

            // Clear old items if any and add new ones
            $prescription->items()->delete();

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

            return redirect()->route('doctor.prescriptions.show', $prescription->id)
                ->with('success', 'Prescription finalized successfully! You can now send it to the pharmacy.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error updating prescription: ' . $e->getMessage()]);
        }
    }

    public function sendToPharmacy($id)
    {
        $doctorId = Auth::guard('doctor')->id();
        $prescription = Prescription::where('doctor_id', $doctorId)->findOrFail($id);

        if ($prescription->status !== 'final') {
            return back()->with('error', 'Only finalized prescriptions can be sent to the pharmacy.');
        }

        // Check if all investigations are complete (optional but good practice)
        $pendingRad = $prescription->radiologyRequests()->where('status', '!=', 'Completed')->count();
        $pendingLab = $prescription->laboratoryRequests()->where('status', '!=', 'Completed')->count();

        if ($pendingRad > 0 || $pendingLab > 0) {
            return back()->with('error', 'All pending radiology and laboratory investigations must be completed before sending to pharmacy.');
        }

        $prescription->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return redirect()->route('doctor.prescriptions.show', $prescription->id)
            ->with('success', 'Prescription has been sent to the pharmacy.');
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
}
