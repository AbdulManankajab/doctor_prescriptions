<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::latest()->get();
        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Clinic,Hospital,Polyclinic',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('facilities', 'public');
        }

        Facility::create($data);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility created successfully.');
    }

    public function edit(Facility $facility)
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Clinic,Hospital,Polyclinic',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            if ($facility->logo_path) {
                Storage::disk('public')->delete($facility->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('facilities', 'public');
        }

        $facility->update($data);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated successfully.');
    }

    public function destroy(Facility $facility)
    {
        if ($facility->doctors()->exists()) {
            return back()->with('error', 'Cannot delete facility with associated doctors.');
        }
        
        if ($facility->logo_path) {
            Storage::disk('public')->delete($facility->logo_path);
        }
        
        $facility->delete();
        return redirect()->route('admin.facilities.index')->with('success', 'Facility deleted successfully.');
    }
}
