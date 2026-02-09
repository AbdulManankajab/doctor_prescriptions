<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefaultPrescriptionDetail;
use Illuminate\Http\Request;

class AdminPrescriptionDefaultsController extends Controller
{
    public function index()
    {
        $defaults = DefaultPrescriptionDetail::latest()->paginate(10);
        return view('admin.defaults.index', compact('defaults'));
    }

    public function create()
    {
        return view('admin.defaults.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'detail_text' => 'required|string',
        ]);

        DefaultPrescriptionDetail::create($request->all());

        return redirect()->route('admin.defaults.index')
            ->with('success', 'Default note added successfully');
    }

    public function edit($id)
    {
        $default = DefaultPrescriptionDetail::findOrFail($id);
        return view('admin.defaults.edit', compact('default'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'detail_text' => 'required|string',
        ]);

        $default = DefaultPrescriptionDetail::findOrFail($id);
        $default->update($request->all());

        return redirect()->route('admin.defaults.index')
            ->with('success', 'Default note updated successfully');
    }

    public function destroy($id)
    {
        $default = DefaultPrescriptionDetail::findOrFail($id);
        $default->delete();

        return redirect()->route('admin.defaults.index')
            ->with('success', 'Default note deleted successfully');
    }
}
