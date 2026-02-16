@extends('layouts.laboratory')

@section('title', 'Manage Lab Request - ' . optional($laboratoryRequest->patient)->name ?? 'Unknown Patient')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="mb-3">
            <a href="{{ route('laboratory.dashboard') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Laboratory Request Details</h4>
                <div>
                    @if($laboratoryRequest->status === 'Pending')
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">Pending</span>
                    @elseif($laboratoryRequest->status === 'In Progress')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">In Progress</span>
                    @else
                        <span class="badge bg-success px-3 py-2 rounded-pill">Completed</span>
                    @endif
                </div>
            </div>
            
            <div class="card-body p-4 pt-0">
                <hr class="mt-0 mb-4">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Patient Information</h6>
                        <p class="mb-1"><span class="fw-semibold">Name:</span> {{ optional($laboratoryRequest->patient)->name ?? 'Unknown Patient' }}</p>
                        <p class="mb-1"><span class="fw-semibold">Age/Gender:</span> {{ optional($laboratoryRequest->patient)->age ?? 'N/A' }}Y / {{ optional($laboratoryRequest->patient)->gender ?? 'N/A' }}</p>
                        <p class="mb-0"><span class="fw-semibold">Phone:</span> {{ optional($laboratoryRequest->patient)->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Doctor Information</h6>
                        <p class="mb-1"><span class="fw-semibold">Requested By:</span> Dr. {{ optional($laboratoryRequest->doctor)->name ?? 'Unknown Doctor' }}</p>
                        <p class="mb-1"><span class="fw-semibold">Date:</span> {{ optional($laboratoryRequest->created_at)->format('d M Y, h:i A') ?? 'N/A' }}</p>
                        <p class="mb-0"><span class="fw-semibold">Priority:</span> 
                            <span class="{{ $laboratoryRequest->priority === 'Urgent' ? 'text-danger fw-bold' : '' }}">{{ $laboratoryRequest->priority }}</span>
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Requested Test(s)</h6>
                    <div class="p-3 bg-light rounded-3 fw-bold border-start border-4 border-teal" style="border-color: #0d9488 !important;">
                        {{ $laboratoryRequest->requested_tests }}
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Clinical Indication</h6>
                    <div class="p-3 border rounded-3 bg-light text-break">
                        {{ $laboratoryRequest->clinical_notes ?: 'No clinical notes provided.' }}
                    </div>
                </div>

                @if($laboratoryRequest->status !== 'Completed')
                <hr class="my-4">
                
                <div class="d-flex gap-2 mb-4">
                    <form action="{{ route('laboratory.update-status', $laboratoryRequest->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="status" value="In Progress">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 {{ $laboratoryRequest->status === 'In Progress' ? 'disabled' : '' }}">
                            <i class="bi bi-play-circle me-1"></i> Mark In Progress
                        </button>
                    </form>
                </div>

                <form action="{{ route('laboratory.complete', $laboratoryRequest->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="report" class="form-label fw-bold text-laboratory">Laboratory Results / Findings *</label>
                        <textarea name="report" id="report" class="form-control" rows="8" placeholder="Enter structured results (e.g. Hb: 13.5 g/dL, WBC: 7.2 x 10^9/L)..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Lab Reports (PDF / Images)</label>
                        <input type="file" name="files[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
                        <div class="small text-muted mt-1">Maximum file size: 10MB per file.</div>
                    </div>

                    <div class="d-grid shadow-sm">
                        <button type="submit" class="btn btn-laboratory btn-lg rounded-3" onclick="return confirm('Ensure all results are correct. Lab reports cannot be edited after completion.')">
                            <i class="bi bi-check2-circle me-2"></i> Complete & Finalize Lab Results
                        </button>
                    </div>
                </form>
                @else
                <hr class="my-4">
                <div class="alert alert-success border-0 shadow-sm" style="background-color: #f0fdfa; color: #0d9488;">
                    <h6 class="fw-bold"><i class="bi bi-check-circle-fill me-2"></i> This lab request has been completed.</h6>
                    <p class="mb-0 small">Completion Date: {{ optional($laboratoryRequest->completed_at)->format('d M Y, h:i A') ?? 'N/A' }}</p>
                    <p class="mb-0 small">Lab Technician: {{ optional($laboratoryRequest->completedBy)->name ?? 'Unknown Staff' }}</p>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Final Laboratory Findings</h6>
                    <div class="p-4 border rounded-3 bg-white shadow-sm font-monospace" style="font-size: 0.95rem; line-height: 1.6;">
                        {!! nl2br(e($laboratoryRequest->report)) !!}
                    </div>
                </div>

                @if($laboratoryRequest->files->count() > 0)
                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Attachment(s)</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($laboratoryRequest->files as $file)
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill">
                                <i class="bi bi-paperclip me-1"></i> {{ $file->file_name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
