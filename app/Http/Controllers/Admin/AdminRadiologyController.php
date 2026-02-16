<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RadiologyStaff;
use App\Models\RadiologyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminRadiologyController extends Controller
{
    public function index()
    {
        $staffMembers = RadiologyStaff::latest()->paginate(10);
        return view('admin.radiology.index', compact('staffMembers'));
    }

    public function createStaff()
    {
        return view('admin.radiology.create_staff');
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:radiology_staff',
            'password' => 'required|string|min:8|confirmed',
        ]);

        RadiologyStaff::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.radiology.index')->with('success', 'Radiology staff created successfully.');
    }

    public function requests()
    {
        $requests = RadiologyRequest::with(['patient', 'doctor', 'completedBy'])->latest()->paginate(20);
        return view('admin.radiology.requests', compact('requests'));
    }
}
