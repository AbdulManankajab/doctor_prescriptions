<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PharmacyUser;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminPharmacyController extends Controller
{
    public function index()
    {
        $pharmacyUsers = PharmacyUser::with('facility')->latest()->paginate(10);
        return view('admin.pharmacy.index', compact('pharmacyUsers'));
    }

    public function create()
    {
        $facilities = Facility::where('status', true)->get();
        return view('admin.pharmacy.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pharmacy_users',
            'password' => 'required|string|min:8|confirmed',
            'facility_id' => 'required|exists:facilities,id',
        ]);

        PharmacyUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'facility_id' => $request->facility_id,
        ]);

        return redirect()->route('admin.pharmacy.index')->with('success', 'Pharmacy user created successfully.');
    }

    public function edit($id)
    {
        $pharmacyUser = PharmacyUser::findOrFail($id);
        $facilities = Facility::where('status', true)->get();
        return view('admin.pharmacy.edit', compact('pharmacyUser', 'facilities'));
    }

    public function update(Request $request, $id)
    {
        $pharmacyUser = PharmacyUser::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pharmacy_users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'facility_id' => 'required|exists:facilities,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'facility_id' => $request->facility_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pharmacyUser->update($data);

        return redirect()->route('admin.pharmacy.index')->with('success', 'Pharmacy user updated successfully.');
    }

    public function destroy($id)
    {
        $pharmacyUser = PharmacyUser::findOrFail($id);
        $pharmacyUser->delete();

        return redirect()->route('admin.pharmacy.index')->with('success', 'Pharmacy user deleted successfully.');
    }
}
