@extends('layouts.doctor')

@section('title', 'Request Radiology - ' . $patient->name)

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="mb-3">
            @if(isset($prescriptionId))
                <a href="{{ route('doctor.prescriptions.show', $prescriptionId) }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to Prescription Draft
                </a>
            @elseif(isset($visitId))
                <a href="{{ route('doctor.prescription.create', ['patientId' => $patient->id, 'visit_id' => $visitId]) }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to Consultation
                </a>
            @else
                <a href="{{ route('doctor.prescription.create', $patient->id) }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to Consultation
                </a>
            @endif
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white p-4 border-0">
                <h4 class="fw-bold mb-0">New Radiology Request</h4>
                <p class="text-muted small mb-0">Patient: {{ $patient->name }} ({{ $patient->patient_number }})</p>
            </div>
            <div class="card-body p-4 pt-0">
                <hr class="mt-0 mb-4">
                <form action="{{ route('doctor.radiology.store', $patient->id) }}" method="POST">
                    @csrf
                    @if(isset($prescriptionId))
                        <input type="hidden" name="prescription_id" value="{{ $prescriptionId }}">
                    @endif
                    @if(isset($visitId))
                        <input type="hidden" name="visit_id" value="{{ $visitId }}">
                    @endif
                    <div class="mb-3">
                        <label for="test_name" class="form-label fw-bold">Requested Test / Procedure *</label>
                        <input type="text" name="test_name" id="test_name" class="form-control form-control-lg" placeholder="e.g. Chest X-Ray PA View, MRI Lumbar Spine" required>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label fw-bold">Priority</label>
                        <select name="priority" id="priority" class="form-select">
                            <option value="Normal">Normal</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="clinical_notes" class="form-label fw-bold">Clinical Notes / Reason for Request</label>
                        <textarea name="clinical_notes" id="clinical_notes" class="form-control" rows="4" placeholder="Briefly describe symptoms or clinical suspicion..."></textarea>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                            <i class="bi bi-send-check me-2"></i> Submit Radiology Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
