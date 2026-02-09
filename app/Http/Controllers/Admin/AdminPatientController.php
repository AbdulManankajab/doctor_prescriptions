<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class AdminPatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::withCount('prescriptions')->with('doctor');

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $patients = $query->latest()->paginate(10);
        $doctors = \App\Models\Doctor::all();

        return view('admin.patients.index', compact('patients', 'doctors'));
    }

    public function show($id)
    {
        $patient = Patient::with(['prescriptions.items'])
            ->findOrFail($id);

        return view('admin.patients.show', compact('patient'));
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('admin.patients.edit', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($request->all());

        return redirect()->route('admin.patients.show', $patient->id)
            ->with('success', 'Patient updated successfully');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return redirect()->route('admin.patients.index')
            ->with('success', 'Patient deleted successfully');
    }
}
