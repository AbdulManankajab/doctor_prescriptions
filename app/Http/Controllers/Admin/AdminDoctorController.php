<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::withCount(['prescriptions', 'patients'])
            ->latest()
            ->paginate(10);
            
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $facilities = Facility::where('status', 1)->get();
        return view('admin.doctors.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email',
            'password' => 'required|string|min:6|confirmed',
            'specialization' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:facilities,id',
        ]);

        Doctor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'specialization' => $request->specialization,
            'facility_id' => $request->facility_id,
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor account created successfully.');
    }

    public function edit($id)
    {
        $doctor = Doctor::findOrFail($id);
        $facilities = Facility::where('status', 1)->get();
        return view('admin.doctors.edit', compact('doctor', 'facilities'));
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'specialization' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:facilities,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'specialization' => $request->specialization,
            'facility_id' => $request->facility_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $doctor->update($data);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor account updated successfully.');
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'Doctor account deleted successfully.');
    }
}
