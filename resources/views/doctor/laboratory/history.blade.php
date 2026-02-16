@extends('layouts.doctor')

@section('title', 'Laboratory History - ' . $patient->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('doctor.prescription.create', $patient->id) }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to Patient Profile
                </a>
                <h3 class="fw-bold mt-2">Laboratory History</h3>
                <p class="text-muted">Patient: {{ $patient->name }} | {{ $patient->gender }}, {{ $patient->age }} years</p>
            </div>
            <a href="{{ route('doctor.laboratory.create', $patient->id) }}" class="btn btn-teal text-white rounded-pill px-4" style="background-color: #0d9488;">
                <i class="bi bi-plus-circle me-1"></i> New Request
            </a>
        </div>

        @forelse($requests as $request)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Lab Request: {{ Str::limit($request->requested_tests, 50) }}</h5>
                        <div class="small text-muted mt-1">
                            Requested by Dr. {{ $request->doctor->name }} on {{ $request->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>
                    <div>
                        @if($request->status === 'Pending')
                            <span class="badge bg-secondary rounded-pill px-3">Pending</span>
                        @elseif($request->status === 'In Progress')
                            <span class="badge bg-warning text-dark rounded-pill px-3">In Progress</span>
                        @else
                            <span class="badge bg-success rounded-pill px-3">Completed</span>
                        @endif
                        
                        @if($request->priority === 'Urgent')
                            <span class="badge bg-danger rounded-pill px-3 ms-2">URGENT</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4 pt-0">
                    <hr class="mt-0 mb-4">
                    <div class="row">
                        <div class="col-md-6 text-break">
                            <h6 class="fw-bold small text-uppercase text-muted mb-2">Requested Tests</h6>
                            <p class="mb-3">{{ $request->requested_tests }}</p>
                            
                            <h6 class="fw-bold small text-uppercase text-muted mb-2">Clinical Notes</h6>
                            <p>{{ $request->clinical_notes ?: 'No clinical notes provided.' }}</p>
                        </div>
                        <div class="col-md-6 border-start">
                            <h6 class="fw-bold small text-uppercase text-muted mb-2">Laboratory Results</h6>
                            @if($request->status === 'Completed')
                                <div class="bg-light p-3 rounded-3 mb-3">
                                    {!! nl2br(e($request->report)) !!}
                                </div>
                                <div class="small text-muted">
                                    Reported by: {{ $request->completedBy->name }} on {{ $request->completed_at->format('d M Y, h:i A') }}
                                </div>
                            @else
                                <div class="text-muted italic p-3 border rounded-3 bg-light">
                                    <i class="bi bi-hourglass-split me-2"></i> Results are pending...
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($request->files->count() > 0)
                        <div class="mt-4">
                            <h6 class="fw-bold small text-uppercase text-muted mb-3">Attachment(s)</h6>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($request->files as $file)
                                    <div class="border rounded p-2 bg-white d-flex align-items-center" style="width: 200px;">
                                        @if(in_array($file->file_type, ['jpg', 'jpeg', 'png']))
                                            <i class="bi bi-file-earmark-image fs-3 text-primary me-2"></i>
                                        @else
                                            <i class="bi bi-file-earmark-pdf fs-3 text-danger me-2"></i>
                                        @endif
                                        <div class="overflow-hidden">
                                            <div class="small text-truncate fw-bold">{{ $file->file_name }}</div>
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="small text-decoration-none">View / Download</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm p-5 text-center">
                <i class="bi bi-folder2-open text-muted mb-3" style="font-size: 4rem;"></i>
                <h4 class="text-muted">No laboratory history found.</h4>
                <p class="text-muted">New requests will appear here once they are submitted.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
