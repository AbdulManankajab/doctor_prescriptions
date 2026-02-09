@extends('layouts.pharmacy')

@section('title', 'Pharmacy Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Pending Prescriptions</h4>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <form action="{{ route('pharmacy.dashboard') }}" method="GET" class="d-flex" style="max-width: 400px;">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search PR #..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-pharmacy"><i class="bi bi-search"></i></button>
                        </form>
                        <a href="{{ route('scan.index') }}" class="btn btn-teal text-white" style="background-color: #0d9488;">
                            <i class="bi bi-qr-code-scan"></i> Scan
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>PR #</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prescriptions as $prescription)
                                <tr>
                                    <td><span class="fw-bold">{{ $prescription->prescription_number }}</span></td>
                                    <td>{{ $prescription->patient->name }}</td>
                                    <td>{{ $prescription->doctor->name }}</td>
                                    <td>
                                        @if($prescription->status === 'sent')
                                            <span class="badge bg-warning text-dark">Ready to Dispense</span>
                                        @elseif($prescription->status === 'dispensed')
                                            <span class="badge bg-success">Dispensed</span>
                                        @endif
                                    </td>
                                    <td>{{ $prescription->sent_at ? $prescription->sent_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('pharmacy.prescriptions.show', $prescription->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
                                        @if($prescription->status === 'sent')
                                            <form action="{{ route('pharmacy.prescriptions.dispense', $prescription->id) }}" method="POST" class="d-inline ml-2">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('Mark as dispensed?')">
                                                    <i class="bi bi-check-circle me-1"></i> Dispense
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox text-muted fs-1 mb-3 d-block"></i>
                                        <p class="text-muted">No prescriptions found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $prescriptions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
