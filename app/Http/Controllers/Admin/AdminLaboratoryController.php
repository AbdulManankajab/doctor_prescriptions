<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryStaff;
use App\Models\LaboratoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminLaboratoryController extends Controller
{
    public function index()
    {
        $staffMembers = LaboratoryStaff::latest()->paginate(10);
        return view('admin.laboratory.index', compact('staffMembers'));
    }

    public function createStaff()
    {
        return view('admin.laboratory.create_staff');
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:laboratory_staff',
            'password' => 'required|string|min:8|confirmed',
        ]);

        LaboratoryStaff::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.laboratory.index')->with('success', 'Laboratory staff created successfully.');
    }

    public function requests()
    {
        $requests = LaboratoryRequest::with(['patient', 'doctor', 'completedBy'])->latest()->paginate(20);
        return view('admin.laboratory.requests', compact('requests'));
    }
}
