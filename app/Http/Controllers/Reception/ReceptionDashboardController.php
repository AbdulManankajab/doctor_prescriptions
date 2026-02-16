<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceptionDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with(['patient', 'doctor'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('visit_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                  });
        }

        $visits = $query->paginate(15);
        $doctors = Doctor::where('status', 1)->get();

        return view('reception.dashboard', compact('visits', 'doctors'));
    }

    public function searchPatient(Request $request)
    {
        $search = $request->get('query');
        if (strlen($search) < 3) {
            return response()->json([]);
        }

        $patients = Patient::where('name', 'LIKE', "%{$search}%")
            ->orWhere('phone', 'LIKE', "%{$search}%")
            ->orWhere('patient_number', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($patients);
    }

    public function storeVisit(Request $request)
    {
        $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'name' => 'required_without:patient_id|string|max:255',
            'age' => 'required_without:patient_id|integer|min:1',
            'gender' => 'required_without:patient_id|in:Male,Female,Other',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'assigned_doctor_id' => 'required|exists:doctors,id',
        ]);

        DB::beginTransaction();
        try {
            $patientId = $request->patient_id;

            if (!$patientId) {
                // Check if patient exists by phone
                $patient = Patient::where('phone', $request->phone)->first();
                if ($patient) {
                    $patientId = $patient->id;
                    // Update patient details if provided
                    $patient->update([
                        'name' => $request->name,
                        'age' => $request->age,
                        'gender' => $request->gender,
                        'address' => $request->address,
                    ]);
                } else {
                    $patient = Patient::create([
                        'name' => $request->name,
                        'age' => $request->age,
                        'gender' => $request->gender,
                        'phone' => $request->phone,
                        'address' => $request->address,
                    ]);
                    $patientId = $patient->id;
                }
            } else {
                // Update existing patient data to latest if needed (optional but good for corrections at reception)
                $patient = Patient::find($patientId);
                $patient->update([
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            }

            // Prevent duplicate open visits for same patient on same day
            $existingVisit = Visit::where('patient_id', $patientId)
                ->whereDate('visit_date', today())
                ->where('status', 'open')
                ->first();

            if ($existingVisit) {
                return back()->with('error', 'Patient already has an open visit for today.')->withInput();
            }

            $visit = Visit::create([
                'patient_id' => $patientId,
                'assigned_doctor_id' => $request->assigned_doctor_id,
                'visit_date' => today(),
                'status' => 'open',
                'reception_user_id' => Auth::guard('reception')->id(),
            ]);

            DB::commit();
            return redirect()->route('reception.dashboard')->with('success', 'Visit created successfully! Visit Number: ' . $visit->visit_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating visit: ' . $e->getMessage())->withInput();
        }
    }
    public function printToken($id)
    {
        $visit = Visit::with(['patient', 'doctor'])->findOrFail($id);
        return view('reception.print_token', compact('visit'));
    }
}
