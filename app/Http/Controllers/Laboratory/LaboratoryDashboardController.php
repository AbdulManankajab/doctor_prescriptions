<?php

namespace App\Http\Controllers\Laboratory;

use App\Http\Controllers\Controller;
use App\Models\LaboratoryRequest;
use App\Models\RequestFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaboratoryDashboardController extends Controller
{
    public function index()
    {
        $requests = LaboratoryRequest::with(['patient', 'doctor'])
            ->latest()
            ->paginate(15);
            
        return view('laboratory.dashboard', compact('requests'));
    }

    public function show(LaboratoryRequest $laboratoryRequest)
    {
        $laboratoryRequest->load(['patient', 'doctor', 'files']);
        return view('laboratory.show', compact('laboratoryRequest'));
    }

    public function updateStatus(Request $request, LaboratoryRequest $laboratoryRequest)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        $laboratoryRequest->update(['status' => $request->status]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function complete(Request $request, LaboratoryRequest $laboratoryRequest)
    {
        $request->validate([
            'report' => 'required|string',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $laboratoryRequest->update([
            'report' => $request->report,
            'status' => 'Completed',
            'completed_by' => Auth::guard('laboratory')->id(),
            'completed_at' => now(),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('laboratory/' . $laboratoryRequest->id, 'public');
                RequestFile::create([
                    'request_id' => $laboratoryRequest->id,
                    'request_type' => LaboratoryRequest::class,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'uploaded_by_id' => Auth::guard('laboratory')->id(),
                    'uploaded_by_type' => \App\Models\LaboratoryStaff::class,
                ]);
            }
        }

        return redirect()->route('laboratory.dashboard')->with('success', 'Laboratory request completed.');
    }
}
