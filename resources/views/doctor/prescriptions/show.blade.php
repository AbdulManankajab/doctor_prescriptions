@extends('layouts.doctor')

@section('title', 'Prescription Command Center')

@section('content')
<div class="row">
    <div class="col-lg-11 mx-auto">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('doctor.dashboard') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
            <div class="btn-group">
                @if($prescription->status !== 'draft')
                <a href="{{ route('doctor.prescriptions.print', $prescription->id) }}" target="_blank" class="btn btn-outline-primary shadow-none">
                    <i class="bi bi-printer me-1"></i> Print
                </a>
                @endif
                <a href="{{ route('doctor.patient.history', $prescription->patient_id) }}" class="btn btn-outline-info shadow-none">
                    <i class="bi bi-clock-history me-1"></i> Patient History
                </a>
            </div>
        </div>

        <!-- Workflow Status Tracker -->
        <div class="card border-0 shadow-sm mb-4 bg-light overflow-hidden">
            <div class="card-body p-0">
                <div class="d-flex text-center workflow-tracker">
                    <div class="flex-fill p-3 {{ $prescription->status === 'draft' ? 'bg-primary text-white shadow' : 'text-muted' }} position-relative">
                        <i class="bi bi-file-earmark-text d-block fs-4 mb-1"></i>
                        <span class="small fw-bold text-uppercase">1. Draft</span>
                        @if($prescription->status === 'draft') <div class="arrow-indicator"></div> @endif
                    </div>
                    <div class="flex-fill p-3 {{ $prescription->status === 'final' ? 'bg-info text-white shadow' : 'text-muted' }} position-relative">
                        <i class="bi bi-check2-square d-block fs-4 mb-1"></i>
                        <span class="small fw-bold text-uppercase">2. Finalized</span>
                        @if($prescription->status === 'final') <div class="arrow-indicator"></div> @endif
                    </div>
                    <div class="flex-fill p-3 {{ $prescription->status === 'sent' ? 'bg-warning text-dark shadow' : 'text-muted' }} position-relative">
                        <i class="bi bi-send d-block fs-4 mb-1"></i>
                        <span class="small fw-bold text-uppercase">3. Sent to Pharmacy</span>
                        @if($prescription->status === 'sent') <div class="arrow-indicator"></div> @endif
                    </div>
                    <div class="flex-fill p-3 {{ $prescription->status === 'dispensed' ? 'bg-success text-white shadow' : 'text-muted' }} position-relative">
                        <i class="bi bi-capsule d-block fs-4 mb-1"></i>
                        <span class="small fw-bold text-uppercase">4. Dispensed</span>
                        @if($prescription->status === 'dispensed') <div class="arrow-indicator"></div> @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Side: Information & Investigations -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white p-4 border-0">
                        <h5 class="fw-bold mb-0">Clinical Overview</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Patient</label>
                                <p class="fw-bold mb-0">{{ $prescription->patient->name }}</p>
                                <span class="text-muted small">{{ $prescription->patient->age }}Y / {{ $prescription->patient->gender }}</span>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Prescription ID</label>
                                <p class="fw-bold mb-0 text-primary">#{{ $prescription->prescription_number }}</p>
                                <span class="text-muted small">Created: {{ $prescription->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-2">Provisional Diagnosis</label>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                                {{ $prescription->diagnosis }}
                            </div>
                        </div>

                        @if($prescription->examination && $prescription->examination->notes)
                        <div class="mb-0">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-2">Initial Examination Notes</label>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-info small">
                                {{ $prescription->examination->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Investigation Hub -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Investigation Hub</h5>
                        @if($prescription->status === 'draft')
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm rounded-pill px-3 dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-plus-circle me-1"></i> Add Investigation
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                <li><a class="dropdown-item" href="{{ route('doctor.radiology.create', ['patient' => $prescription->patient->id, 'prescription_id' => $prescription->id]) }}"><i class="bi bi-x-diamond-fill me-2 text-primary"></i> Radiology (X-Ray)</a></li>
                                <li><a class="dropdown-item" href="{{ route('doctor.laboratory.create', ['patient' => $prescription->patient->id, 'prescription_id' => $prescription->id]) }}"><i class="bi bi-water me-2 text-teal"></i> Laboratory (Lab)</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                    <div class="card-body p-4 pt-0">
                        @php
                            $totalInvestigations = $prescription->radiologyRequests->count() + $prescription->laboratoryRequests->count();
                            $completedInvestigations = $prescription->radiologyRequests->where('status', 'Completed')->count() + $prescription->laboratoryRequests->where('status', 'Completed')->count();
                        @endphp

                        @if($totalInvestigations > 0)
                            <div class="mb-4">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-2">Completion Status</label>
                                <div class="progress rounded-pill shadow-sm" style="height: 10px;">
                                    <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $totalInvestigations > 0 ? ($completedInvestigations / $totalInvestigations) * 100 : 0 }}%"></div>
                                </div>
                                <div class="text-end mt-1 small text-muted">{{ $completedInvestigations }}/{{ $totalInvestigations }} Completed</div>
                            </div>

                            <!-- Radiology List -->
                            @foreach($prescription->radiologyRequests as $rad)
                            <div class="investigation-item p-3 border rounded-4 mb-3 position-relative {{ $rad->status === 'Completed' ? 'border-success bg-success-light' : 'bg-white' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="icon-box bg-primary text-white rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="bi bi-x-diamond-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $rad->test_name }}</h6>
                                            <span class="badge {{ $rad->status === 'Completed' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill small">{{ $rad->status }}</span>
                                            <span class="text-muted small ms-2">Priority: {{ $rad->priority }}</span>
                                        </div>
                                    </div>
                                    @if($rad->status === 'Completed')
                                    <button class="btn btn-link btn-sm text-decoration-none view-results-btn" data-bs-toggle="collapse" data-bs-target="#radResults-{{ $rad->id }}">
                                        View Results <i class="bi bi-chevron-down ms-1"></i>
                                    </button>
                                    @endif
                                </div>
                                
                                <div class="collapse mt-3" id="radResults-{{ $rad->id }}">
                                    <div class="p-3 bg-white border rounded-3 small">
                                        <h6 class="fw-bold mb-2">Findings:</h6>
                                        <p class="mb-3 text-dark">{{ $rad->report ?: 'No descriptive report available.' }}</p>
                                        
                                        @if($rad->files->count() > 0)
                                        <h6 class="fw-bold mb-2">Attachments:</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($rad->files as $file)
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-outline-light btn-sm text-dark border">
                                                <i class="bi bi-file-earmark-image me-1 text-primary"></i> {{ $file->file_name }}
                                            </a>
                                            @endforeach
                                        </div>
                                        @endif
                                        <div class="mt-3 text-muted x-small">Reported by: {{ $rad->completedBy->name }} on {{ $rad->completed_at->format('d M Y, h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <!-- Laboratory List -->
                            @foreach($prescription->laboratoryRequests as $lab)
                            <div class="investigation-item p-3 border rounded-4 mb-3 position-relative {{ $lab->status === 'Completed' ? 'border-success bg-success-light' : 'bg-white' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="icon-box bg-teal text-white rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="bi bi-water fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $lab->requested_tests }}</h6>
                                            <span class="badge {{ $lab->status === 'Completed' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill small">{{ $lab->status }}</span>
                                            <span class="text-muted small ms-2">Priority: {{ $lab->priority }}</span>
                                        </div>
                                    </div>
                                    @if($lab->status === 'Completed')
                                    <button class="btn btn-link btn-sm text-decoration-none view-results-btn" data-bs-toggle="collapse" data-bs-target="#labResults-{{ $lab->id }}">
                                        View Results <i class="bi bi-chevron-down ms-1"></i>
                                    </button>
                                    @endif
                                </div>

                                <div class="collapse mt-3" id="labResults-{{ $lab->id }}">
                                    <div class="p-3 bg-white border rounded-3 small">
                                        <h6 class="fw-bold mb-2">Lab Report:</h6>
                                        <p class="mb-3 text-dark">{{ $lab->report ?: 'No descriptive report available.' }}</p>
                                        
                                        @if($lab->files->count() > 0)
                                        <h6 class="fw-bold mb-2">Attachments:</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($lab->files as $file)
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-outline-light btn-sm text-dark border">
                                                <i class="bi bi-file-earmark-medical me-1 text-teal"></i> {{ $file->file_name }}
                                            </a>
                                            @endforeach
                                        </div>
                                        @endif
                                        <div class="mt-3 text-muted x-small">Signed by: {{ $lab->completedBy->name }} on {{ $lab->completed_at->format('d M Y, h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-search fs-1 text-muted opacity-25 d-block mb-3"></i>
                                <p class="text-muted">No diagnostic investigations requested yet.</p>
                                @if($prescription->status === 'draft')
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <a href="{{ route('doctor.radiology.create', ['patient' => $prescription->patient->id, 'prescription_id' => $prescription->id, 'visit_id' => $prescription->visit_id]) }}" class="btn btn-outline-primary btn-sm rounded-pill"><i class="bi bi-plus-circle me-1"></i> Radiology</a>
                                    <a href="{{ route('doctor.laboratory.create', ['patient' => $prescription->patient->id, 'prescription_id' => $prescription->id, 'visit_id' => $prescription->visit_id]) }}" class="btn btn-outline-info btn-sm rounded-pill"><i class="bi bi-plus-circle me-1"></i> Laboratory</a>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: Finalization & Medications -->
            <div class="col-md-5">
                @if($prescription->status === 'draft')
                <!-- Finalization Form -->
                <div class="card border-0 shadow-sm border-top border-5 border-success position-sticky" style="top: 20px;">
                    <div class="card-header bg-white p-4 border-0">
                        <h5 class="fw-bold mb-0">Finalize Prescription</h5>
                        <p class="text-muted small mb-0 mt-1">Add medications to complete the workflow.</p>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @if($totalInvestigations > 0 && $completedInvestigations < $totalInvestigations)
                        <div class="alert alert-warning border-0 small mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Heads up!</strong> Some investigations are still pending results. Reviewing results first is recommended.
                        </div>
                        @endif

                        <form action="{{ route('doctor.prescriptions.update', $prescription->id) }}" method="POST" id="finalizeForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="final">

                            <div class="mb-4">
                                <label class="fw-bold small text-uppercase text-muted d-block mb-3">Medicines</label>
                                <div id="medicinesContainer">
                                    <div class="medicine-entry p-3 bg-light rounded-4 mb-3 border position-relative">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <select name="medicines[0][medicine_id]" class="form-select select2-med" required>
                                                    <option value="">Select Medicine...</option>
                                                    @foreach(\App\Models\Medicine::all() as $med)
                                                        <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->type }})</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="medicines[0][type]" value="Tablet">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="medicines[0][dosage]" class="form-control form-control-sm" placeholder="Dosage (e.g. 1-0-1)" required>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="medicines[0][duration]" class="form-control form-control-sm" placeholder="Duration (e.g. 5 days)" required>
                                            </div>
                                            <div class="col-12">
                                                <input type="text" name="medicines[0][instructions]" class="form-control form-control-sm" placeholder="Instructions (e.g. After Meal)" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm w-100 rounded-pill" id="addMedicineBtn">
                                    <i class="bi bi-plus-circle me-1"></i> Add Another Medicine
                                </button>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold small text-uppercase text-muted d-block mb-2">Final Instructions</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Additional advice for the patient...">{{ $prescription->notes }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow">
                                <i class="bi bi-check2-circle fs-5 me-2"></i> Finalize Prescription
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <!-- Read-only Medication View -->
                <div class="card border-0 shadow-sm border-top border-5 border-primary position-sticky" style="top: 20px;">
                    <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Medications</h5>
                        @if($prescription->status === 'final')
                        <span class="badge bg-info-light text-info rounded-pill px-3 py-2">Ready</span>
                        @endif
                    </div>
                    <div class="card-body p-4 pt-2">
                        @foreach($prescription->items as $item)
                        <div class="p-3 bg-light rounded-4 mb-3 border-start border-4 border-primary">
                            <h6 class="fw-bold mb-1 text-primary">{{ $item->medicine->name }}</h6>
                            <div class="row g-2 mt-1">
                                <div class="col-6">
                                    <small class="text-muted d-block">Dosage</small>
                                    <span class="fw-semibold small">{{ $item->dosage }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Duration</small>
                                    <span class="fw-semibold small">{{ $item->duration }}</span>
                                </div>
                                <div class="col-12 mt-2">
                                    <small class="text-muted d-block">Instructions</small>
                                    <span class="fw-semibold small">{{ $item->instructions }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($prescription->notes)
                        <div class="mt-4 p-3 bg-white border rounded-3 small">
                            <label class="text-muted fw-bold d-block mb-1">Doctor's Advice:</label>
                            <p class="mb-0">{{ $prescription->notes }}</p>
                        </div>
                        @endif

                        @if($prescription->status === 'final')
                        <form action="{{ route('doctor.prescriptions.send', $prescription->id) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-lg w-100 rounded-pill fw-bold text-dark py-3 shadow-sm border-0" onclick="return confirm('Send this prescription to the Pharmacy?')">
                                <i class="bi bi-send-fill me-2"></i> Send to Pharmacy
                            </button>
                        </form>
                        @elseif($prescription->status === 'sent')
                        <div class="alert alert-warning border-0 d-flex align-items-center mt-4 rounded-4 px-4 py-3">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Sent to Pharmacy</h6>
                                <p class="small mb-0">Waiting for dispensing...</p>
                            </div>
                        </div>
                        @elseif($prescription->status === 'dispensed')
                        <div class="alert alert-success border-0 d-flex align-items-center mt-4 rounded-4 px-4 py-3">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Successfully Dispensed</h6>
                                <p class="small mb-0">Patient has received medications.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Medical Theme */
.workflow-tracker .flex-fill {
    transition: all 0.3s ease;
    border-right: 1px solid rgba(0,0,0,0.05);
}
.workflow-tracker .arrow-indicator {
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 10px solid #0d6efd; /* bg-primary color */
}
.bg-info .arrow-indicator { border-top-color: #0dcaf0; }
.bg-warning .arrow-indicator { border-top-color: #ffc107; }
.bg-success .arrow-indicator { border-top-color: #198754; }

.bg-success-light { background-color: rgba(25, 135, 84, 0.05); }
.bg-info-light { background-color: rgba(13, 202, 240, 0.08); }
.text-teal { color: #0d9488; }
.bg-teal { background-color: #0d9488; }

.investigation-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.investigation-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.x-small { font-size: 0.75rem; }

/* Custom Form Styles */
.form-control-sm { border-radius: 8px; border: 1px solid #eee; padding: 0.5rem 0.75rem; }
.form-control-sm:focus { border-color: #0d6efd; box-shadow: none; }
.medicine-entry { border-style: dashed; }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    let medIdx = 1;
    $('#addMedicineBtn').click(function() {
        const template = `
        <div class="medicine-entry p-3 bg-light rounded-4 mb-3 border position-relative">
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 border-0 remove-med"><i class="bi bi-trash"></i></button>
            <div class="row g-2">
                <div class="col-12">
                    <select name="medicines[${medIdx}][medicine_id]" class="form-select" required>
                        <option value="">Select Medicine...</option>
                        @foreach(\App\Models\Medicine::all() as $med)
                            <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->type }})</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="medicines[${medIdx}][type]" value="Tablet">
                </div>
                <div class="col-6">
                    <input type="text" name="medicines[${medIdx}][dosage]" class="form-control form-control-sm" placeholder="Dosage" required>
                </div>
                <div class="col-6">
                    <input type="text" name="medicines[${medIdx}][duration]" class="form-control form-control-sm" placeholder="Duration" required>
                </div>
                <div class="col-12">
                    <input type="text" name="medicines[${medIdx}][instructions]" class="form-control form-control-sm" placeholder="Instructions" required>
                </div>
            </div>
        </div>`;
        $('#medicinesContainer').append(template);
        medIdx++;
    });

    $(document).on('click', '.remove-med', function() {
        $(this).closest('.medicine-entry').remove();
    });
});
</script>
@endpush
@endsection
