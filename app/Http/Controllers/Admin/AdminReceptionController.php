<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReceptionStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminReceptionController extends Controller
{
    public function index()
    {
        $receptionists = ReceptionStaff::latest()->paginate(15);
        return view('admin.reception.index', compact('receptionists'));
    }

    public function create()
    {
        return view('admin.reception.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:reception_staff,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        ReceptionStaff::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'status' => 1,
        ]);

        return redirect()->route('admin.reception.index')->with('success', 'Receptionist account created successfully.');
    }

    public function edit($id)
    {
        $receptionist = ReceptionStaff::findOrFail($id);
        return view('admin.reception.edit', compact('receptionist'));
    }

    public function update(Request $request, $id)
    {
        $receptionist = ReceptionStaff::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:reception_staff,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $receptionist->update($data);

        return redirect()->route('admin.reception.index')->with('success', 'Receptionist account updated successfully.');
    }

    public function destroy($id)
    {
        $receptionist = ReceptionStaff::findOrFail($id);
        $receptionist->delete();

        return redirect()->route('admin.reception.index')->with('success', 'Receptionist account deleted successfully.');
    }
}
