@extends('layouts.radiology')

@section('title', 'Manage Request - ' . ($radiologyRequest->patient->name ?? 'Unknown'))

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="mb-3">
            <a href="{{ route('radiology.dashboard') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Radiology Request Details</h4>
                <div>
                    @if($radiologyRequest->status === 'Pending')
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">Pending</span>
                    @elseif($radiologyRequest->status === 'In Progress')
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
                        <p class="mb-1"><span class="fw-semibold">Name:</span> {{ $radiologyRequest->patient->name ?? 'Unknown' }}</p>
                        <p class="mb-1"><span class="fw-semibold">Age/Gender:</span> {{ $radiologyRequest->patient->age ?? 'N/A' }}Y / {{ $radiologyRequest->patient->gender ?? 'N/A' }}</p>
                        <p class="mb-0"><span class="fw-semibold">Phone:</span> {{ $radiologyRequest->patient->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Doctor Information</h6>
                        <p class="mb-1"><span class="fw-semibold">Requested By:</span> Dr. {{ $radiologyRequest->doctor->name ?? 'Unknown' }}</p>
                        <p class="mb-1"><span class="fw-semibold">Date:</span> {{ $radiologyRequest->created_at ? $radiologyRequest->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
                        <p class="mb-0"><span class="fw-semibold">Priority:</span> 
                            <span class="{{ $radiologyRequest->priority === 'Urgent' ? 'text-danger fw-bold' : '' }}">{{ $radiologyRequest->priority }}</span>
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Requested Test</h6>
                    <div class="p-3 bg-light rounded-3 fw-bold border-start border-4 border-primary">
                        {{ $radiologyRequest->test_name }}
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Clinical Notes</h6>
                    <div class="p-3 border rounded-3 bg-light">
                        {{ $radiologyRequest->clinical_notes ?: 'No clinical notes provided.' }}
                    </div>
                </div>

                @if($radiologyRequest->status !== 'Completed')
                <hr class="my-4">
                
                <div class="d-flex gap-2 mb-4">
                    <form action="{{ route('radiology.update-status', $radiologyRequest->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="status" value="In Progress">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 {{ $radiologyRequest->status === 'In Progress' ? 'disabled' : '' }}">
                            <i class="bi bi-play-circle me-1"></i> Mark In Progress
                        </button>
                    </form>
                </div>

                <form action="{{ route('radiology.complete', $radiologyRequest->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="report" class="form-label fw-bold text-radiology">Radiology Report / Findings *</label>
                        <textarea name="report" id="report" class="form-control" rows="6" placeholder="Type the radiology report here..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Images / DICOM / PDF</label>
                        <input type="file" name="files[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                        <div class="small text-muted mt-1">Maximum file size: 20MB per file.</div>
                    </div>

                    <div class="d-grid shadow-sm">
                        <button type="submit" class="btn btn-radiology btn-lg rounded-3" onclick="return confirm('Ensure all findings are correct. Reports cannot be edited after completion.')">
                            <i class="bi bi-check2-circle me-2"></i> Complete & Finalize Report
                        </button>
                    </div>
                </form>
                @else
                <hr class="my-4">
                <div class="alert alert-success border-0 shadow-sm">
                    <h6 class="fw-bold"><i class="bi bi-check-circle-fill me-2"></i> This request has been completed.</h6>
                    <p class="mb-0 small">Completion Date: {{ $radiologyRequest->completed_at ? $radiologyRequest->completed_at->format('d M Y, h:i A') : 'N/A' }}</p>
                    <p class="mb-0 small">Staff Member: {{ $radiologyRequest->completedBy->name ?? 'Unknown' }}</p>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Final Report</h6>
                    <div class="p-3 border rounded-3 bg-white shadow-sm">
                        {!! nl2br(e($radiologyRequest->report)) !!}
                    </div>
                </div>

                @if($radiologyRequest->files->count() > 0)
                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Attachment(s)</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($radiologyRequest->files as $file)
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

