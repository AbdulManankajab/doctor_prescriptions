<?php

namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use App\Models\RadiologyRequest;
use App\Models\RequestFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RadiologyDashboardController extends Controller
{
    public function index()
    {
        $requests = RadiologyRequest::with(['patient', 'doctor'])
            ->latest()
            ->paginate(15);
            
        return view('radiology.dashboard', compact('requests'));
    }

    public function show(RadiologyRequest $radiologyRequest)
    {
        $radiologyRequest->load(['patient', 'doctor', 'files']);
        return view('radiology.show', compact('radiologyRequest'));
    }

    public function updateStatus(Request $request, RadiologyRequest $radiologyRequest)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        $radiologyRequest->update(['status' => $request->status]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function complete(Request $request, RadiologyRequest $radiologyRequest)
    {
        $request->validate([
            'report' => 'required|string',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,dicom|max:20480',
        ]);

        $radiologyRequest->update([
            'report' => $request->report,
            'status' => 'Completed',
            'completed_by' => Auth::guard('radiology')->id(),
            'completed_at' => now(),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('radiology/' . $radiologyRequest->id, 'public');
                RequestFile::create([
                    'request_id' => $radiologyRequest->id,
                    'request_type' => RadiologyRequest::class,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'uploaded_by_id' => Auth::guard('radiology')->id(),
                    'uploaded_by_type' => \App\Models\RadiologyStaff::class,
                ]);
            }
        }

        return redirect()->route('radiology.dashboard')->with('success', 'Radiology request completed.');
    }
}
