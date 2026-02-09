@extends('layouts.doctor')

@section('title', 'Prescription Details')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('doctor.dashboard') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
            <div class="btn-group">
                <a href="{{ route('doctor.prescriptions.print', $prescription->id) }}" target="_blank" class="btn btn-outline-primary shadow-none">
                    <i class="bi bi-printer me-1"></i> Print
                </a>
                <a href="{{ route('doctor.patient.history', $prescription->patient_id) }}" class="btn btn-outline-info shadow-none">
                    <i class="bi bi-clock-history me-1"></i> History
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">
                    Prescription <span class="text-primary">#{{ $prescription->prescription_number }}</span>
                </h4>
                <div>
                    @if($prescription->status === 'draft')
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">Draft</span>
                    @elseif($prescription->status === 'sent')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Sent to Pharmacy</span>
                    @elseif($prescription->status === 'dispensed')
                        <span class="badge bg-success px-3 py-2 rounded-pill">Dispensed</span>
                    @endif
                </div>
            </div>
            
            <div class="card-body p-4 pt-0">
                <hr class="mt-0 mb-4">
                
                <div class="row mb-4">
                    <div class="col-md-5">
                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Patient Information</h6>
                        <p class="mb-1"><span class="fw-semibold">Name:</span> {{ $prescription->patient->name }}</p>
                        <p class="mb-1"><span class="fw-semibold">Age/Gender:</span> {{ $prescription->patient->age }}Y / {{ $prescription->patient->gender }}</p>
                        <p class="mb-1"><span class="fw-semibold">Phone:</span> {{ $prescription->patient->phone }}</p>
                    </div>
                    <div class="col-md-5">
                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Submission Details</h6>
                        <p class="mb-1"><span class="fw-semibold">Created At:</span> {{ $prescription->created_at->format('d M Y, h:i A') }}</p>
                        @if($prescription->sent_at)
                            <p class="mb-1"><span class="fw-semibold">Sent to Pharmacy:</span> {{ $prescription->sent_at->format('d M Y, h:i A') }}</p>
                        @endif
                    </div>
                    <div class="col-md-2 text-end">
                        @if($prescription->qr_token)
                            <div class="d-inline-block p-1 border rounded bg-white shadow-sm">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($prescription->qr_token) !!}
                            </div>
                            <div style="font-size: 8px; margin-top: 5px; color: #888;" class="text-center">SECURE QR</div>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Diagnosis</h6>
                    <div class="p-3 bg-light rounded-3">
                        {{ $prescription->diagnosis }}
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Medications</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Medicine</th>
                                    <th>Type</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                    <th>Instructions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescription->items as $item)
                                    <tr>
                                        <td class="fw-semibold">{{ $item->medicine->name }}</td>
                                        <td>{{ ucfirst($item->type) }}</td>
                                        <td>{{ $item->dosage }}</td>
                                        <td>{{ $item->duration }}</td>
                                        <td>{{ $item->instructions }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($prescription->notes)
                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Additional Instructions</h6>
                    <div class="p-3 border-start border-4 border-primary bg-light" style="white-space: pre-line;">{{ $prescription->notes }}</div>
                </div>
                @endif
            </div>

            @if($prescription->status === 'draft')
            <div class="card-footer bg-white p-4 border-0">
                <form action="{{ route('doctor.prescriptions.send', $prescription->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2" onclick="return confirm('Send to Hospital Pharmacy?')">
                        <i class="bi bi-send me-2"></i> Send to Pharmacy
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
