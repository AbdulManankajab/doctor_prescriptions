<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorProfileController extends Controller
{
    public function index()
    {
        $doctor = Auth::guard('doctor')->user();
        return view('doctor.profile.index', compact('doctor'));
    }

    public function update(Request $request)
    {
        $doctor = Auth::guard('doctor')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only([
            'name', 'phone', 'specialization', 'qualification', 
            'experience_years', 'address', 'bio'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($doctor->profile_picture) {
                Storage::disk('public')->delete($doctor->profile_picture);
            }
            $path = $request->file('profile_picture')->store('doctor_profiles', 'public');
            $data['profile_picture'] = $path;
        }

        $doctor->update($data);

        return redirect()->route('doctor.profile.index')->with('success', 'Profile updated successfully!');
    }
}
