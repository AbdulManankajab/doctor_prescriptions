@extends('layouts.doctor')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-md-6">
        <div class="card h-100 bg-white border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-people fs-1 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Total My Patients</h6>
                        <h2 class="mb-0 fw-bold">{{ $stats['total_patients'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 bg-white border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-file-earmark-medical fs-1 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Total My Prescriptions</h6>
                        <h2 class="mb-0 fw-bold">{{ $stats['total_prescriptions'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Search Patient Card -->
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-2"></i> Search My Patients</h5>
                <div class="row g-3">
                    <div class="col-md-8">
                        <input type="text" id="searchInput" class="form-control form-control-lg border-2" placeholder="Search by name, phone, or patient number...">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-lg w-100" id="searchBtn">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('scan.index') }}" class="btn btn-teal btn-lg w-100" style="background-color: #0d9488; color: white; border: none;">
                            <i class="bi bi-qr-code-scan me-1"></i> Scan QR
                        </a>
                    </div>
                </div>
                <div id="searchResults" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Search / New Prescription -->
    <div class="col-lg-12">
        <div class="card border-0">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Recent Prescriptions</h5>
                <a href="{{ route('doctor.prescription.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> New Prescription
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Prescription #</th>
                                <th>Patient Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_prescriptions'] as $prescription)
                            <tr>
                                <td class="ps-4 font-monospace text-muted">{{ $prescription->prescription_number }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $prescription->patient->name }}</div>
                                    <small class="text-muted">{{ $prescription->patient->phone }}</small>
                                </td>
                                <td>
                                    @if($prescription->status === 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif($prescription->status === 'sent')
                                        <span class="badge bg-warning text-dark">Sent to Pharmacy</span>
                                    @elseif($prescription->status === 'dispensed')
                                        <span class="badge bg-success">Dispensed</span>
                                    @endif
                                </td>
                                <td>{{ $prescription->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('doctor.prescriptions.show', $prescription->id) }}" class="btn btn-sm btn-outline-info shadow-none" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('doctor.prescriptions.print', $prescription->id) }}" class="btn btn-sm btn-outline-primary shadow-none" title="Print">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <a href="{{ route('doctor.patient.history', $prescription->patient_id) }}" class="btn btn-sm btn-outline-secondary shadow-none" title="Patient History">
                                            <i class="bi bi-clock-history"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    No prescriptions found yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
$(document).ready(function() {
    $('#searchBtn').click(function() {
        const query = $('#searchInput').val();
        if (query.length < 2) {
            alert('Please enter at least 2 characters');
            return;
        }

        $.ajax({
            url: '{{ route("doctor.patient.search") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                search: query
            },
            success: function(data) {
                displayResults(data);
            },
            error: function() {
                alert('Error searching patients');
            }
        });
    });

    $('#searchInput').keypress(function(e) {
        if (e.which == 13) {
            $('#searchBtn').click();
        }
    });

    function displayResults(patients) {
        const resultsDiv = $('#searchResults');
        
        if (patients.length === 0) {
            resultsDiv.html(`
                <div class="alert alert-info border-0 shadow-sm">
                    <i class="bi bi-info-circle me-2"></i> No patients found in your records. 
                    <a href="{{ route('doctor.prescription.create') }}" class="alert-link">Create a new prescription</a>
                </div>
            `);
            return;
        }

        let html = '<h6 class="mb-3 text-muted">Search Results:</h6><div class="list-group shadow-sm">';
        
        patients.forEach(patient => {
            html += `
                <div class="list-group-item list-group-item-action border-0 mb-1 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 fw-bold text-primary">${patient.name}</h6>
                            <p class="mb-0 text-muted small">
                                <span class="me-2"><i class="bi bi-hash"></i> ${patient.patient_number}</span>
                                <span class="me-2"><i class="bi bi-telephone"></i> ${patient.phone}</span>
                                <span><i class="bi bi-person"></i> ${patient.gender}, ${patient.age} years</span>
                            </p>
                        </div>
                        <div>
                            <a href="{{ url('doctor/prescription/create') }}/${patient.id}" class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-plus"></i> New Prescription
                            </a>
                            <a href="{{ url('doctor/patient/history') }}/${patient.id}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="bi bi-clock-history"></i> History
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        resultsDiv.html(html);
    }
});
</script>
@endsection
@endsection
