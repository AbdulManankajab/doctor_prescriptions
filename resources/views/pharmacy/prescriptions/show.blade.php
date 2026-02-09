@extends('layouts.pharmacy')

@section('title', 'Prescription Details')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="mb-3">
            <a href="{{ route('pharmacy.dashboard') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">
                    Prescription <span class="text-pharmacy">#{{ $prescription->prescription_number }}</span>
                </h4>
                <div>
                    @if($prescription->status === 'sent')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Ready to Dispense</span>
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
                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Doctor Information</h6>
                        <p class="mb-1"><span class="fw-semibold">Doctor:</span> {{ $prescription->doctor->name }}</p>
                        <p class="mb-1"><span class="fw-semibold">Sent At:</span> {{ $prescription->sent_at ? $prescription->sent_at->format('d M Y, h:i A') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-2 text-end">
                        @if($prescription->qr_token)
                            <div class="d-inline-block p-1 border rounded bg-white">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($prescription->qr_token) !!}
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
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Prescribed Medicines</h6>
                    <div class="table-responsive">
                        <table class="table border">
                            <thead class="bg-light">
                                <tr>
                                    <th>Medicine</th>
                                    <th>Type</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                    <th>Timing</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescription->items as $item)
                                    <tr>
                                        <td class="fw-semibold">{{ $item->medicine->name }}</td>
                                        <td><span class="badge bg-outline-secondary border text-dark">{{ ucfirst($item->type) }}</span></td>
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
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Clinical Instructions</h6>
                    <div class="p-3 border-start border-4 border-info bg-light">
                        {{ $prescription->notes }}
                    </div>
                </div>
                @endif

                @if($prescription->status === 'dispensed')
                <div class="alert alert-soft-success border-0 bg-light p-3">
                    <div class="d-flex">
                        <i class="bi bi-info-circle-fill text-success fs-5 me-3"></i>
                        <div>
                            <p class="mb-0 fw-bold text-success">Dispensing Information</p>
                            <p class="mb-0 small text-muted">Dispensed on {{ $prescription->dispensed_at->format('d M Y, h:i A') }}</p>
                            <p class="mb-0 small text-muted">Dispensed by: {{ $prescription->dispensedBy->name }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="card-footer bg-white p-4 border-0 d-flex justify-content-between">
                <a href="{{ route('pharmacy.prescriptions.print', $prescription->id) }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4">
                    <i class="bi bi-printer me-2"></i> Print Prescription
                </a>
                
                @if($prescription->status === 'sent')
                    <form action="{{ route('pharmacy.prescriptions.dispense', $prescription->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-pharmacy rounded-pill px-5" onclick="return confirm('Confirm dispensing this prescription?')">
                            <i class="bi bi-check-all me-2"></i> Confirm Dispense
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
