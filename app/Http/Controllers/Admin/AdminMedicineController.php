<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class AdminMedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::latest()->paginate(10);
        return view('admin.medicines.index', compact('medicines'));
    }

    public function create()
    {
        return view('admin.medicines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:medicines,name',
            'type' => 'required|string|in:tablet,syrup,capsule,injection',
            'dosage_options' => 'nullable|string',
        ]);

        Medicine::create($request->all());

        return redirect()->route('admin.medicines.index')
            ->with('success', 'Medicine added successfully');
    }

    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        return view('admin.medicines.edit', compact('medicine'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:medicines,name,' . $id,
            'type' => 'required|string|in:tablet,syrup,capsule,injection',
            'dosage_options' => 'nullable|string',
        ]);

        $medicine = Medicine::findOrFail($id);
        $medicine->update($request->all());

        return redirect()->route('admin.medicines.index')
            ->with('success', 'Medicine updated successfully');
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        return redirect()->route('admin.medicines.index')
            ->with('success', 'Medicine deleted successfully');
    }
}
