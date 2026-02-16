@extends('layouts.reception')

@section('title', 'Reception Dashboard')

@section('content')
<div class="row g-4">
    <!-- Patient Search & Visit Creation -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-plus-fill me-2"></i> New Patient Visit</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="{{ route('reception.visit.store') }}" method="POST" id="visitForm">
                    @csrf
                    <input type="hidden" name="patient_id" id="patient_id">
                    
                    <div class="mb-3 position-relative">
                        <label class="form-label small fw-bold">Search Patient (Name/Phone)</label>
                        <div class="input-group">
                            <input type="text" id="patient_search" class="form-control" placeholder="Type to search..." autocomplete="off">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        </div>
                        <div id="search-results" class="list-group position-absolute w-100 shadow-lg d-none mt-1" style="z-index: 1000; max-height: 250px; overflow-y: auto;"></div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Patient Name *</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Age *</label>
                            <input type="number" name="age" id="age" class="form-control" required placeholder="Years">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Gender *</label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Phone Number *</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="0123456789" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Address</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Locality, City">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary">Assign Doctor *</label>
                        <select name="assigned_doctor_id" class="form-select border-primary" required>
                            <option value="">Select Doctor...</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">Dr. {{ $doctor->name }} ({{ $doctor->specialization ?: 'General' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 shadow">
                        <i class="bi bi-check2-circle me-1"></i> Confirm Visit & Generate Number
                    </button>
                    
                    <button type="button" class="btn btn-link text-muted w-100 mt-2 btn-sm text-decoration-none" id="clearBtn">
                        <i class="bi bi-x-circle me-1"></i> Clear Form
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Today's Visits List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Recent Visits</h5>
                <form action="{{ route('reception.dashboard') }}" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Visit # or Patient Name" value="{{ request('search') }}">
                        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Visit Number</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visits as $visit)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $visit->visit_number }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $visit->patient->name }}</div>
                                        <div class="small text-muted">{{ $visit->patient->phone }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">Dr. {{ $visit->doctor->name }}</div>
                                        <div class="x-small text-muted">{{ $visit->doctor->specialization }}</div>
                                    </td>
                                    <td>
                                        <div class="small">{{ $visit->created_at->format('h:i A') }}</div>
                                        <div class="x-small text-muted">{{ $visit->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td>
                                        <span class="visit-status-{{ $visit->status }}">
                                            {{ ucfirst($visit->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('reception.visit.print', $visit->id) }}" 
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary rounded-pill">
                                            <i class="bi bi-printer"></i> Token
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-calendar-x fs-1 text-muted opacity-25"></i>
                                        <p class="text-muted mt-2">No visits found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $visits->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.75rem; }
    #search-results .list-group-item { cursor: pointer; }
    #search-results .list-group-item:hover { background-color: #f8f9fa; }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    let searchTimer;
    const $searchInput = $('#patient_search');
    const $results = $('#search-results');

    $searchInput.on('input', function() {
        clearTimeout(searchTimer);
        const query = $(this).val();
        
        if (query.length < 3) {
            $results.addClass('d-none');
            return;
        }

        searchTimer = setTimeout(() => {
            $.get('{{ route("reception.patient.search") }}', { query: query }, function(data) {
                $results.empty();
                if (data.length > 0) {
                    data.forEach(p => {
                        $results.append(`
                            <a href="#" class="list-group-item list-group-item-action patient-select" 
                               data-id="${p.id}" 
                               data-name="${p.name}" 
                               data-age="${p.age}" 
                               data-gender="${p.gender}" 
                               data-phone="${p.phone}" 
                               data-address="${p.address || ''}">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">${p.name}</span>
                                    <span class="small text-muted">${p.phone}</span>
                                </div>
                                <div class="x-small text-muted">${p.patient_number}</div>
                            </a>
                        `);
                    });
                    $results.removeClass('d-none');
                } else {
                    $results.addClass('d-none');
                }
            });
        }, 300);
    });

    $(document).on('click', '.patient-select', function(e) {
        e.preventDefault();
        const p = $(this).data();
        $('#patient_id').val(p.id);
        $('#name').val(p.name);
        $('#age').val(p.age);
        $('#gender').val(p.gender);
        $('#phone').val(p.phone);
        $('#address').val(p.address);
        
        $results.addClass('d-none');
        $searchInput.val(p.name);
        $('.card-header h5').html('<i class="bi bi-person-check-fill me-2"></i> Create Visit for Existing Patient');
    });

    $('#clearBtn').click(function() {
        $('#visitForm')[0].reset();
        $('#patient_id').val('');
        $searchInput.val('');
        $('.card-header h5').html('<i class="bi bi-person-plus-fill me-2"></i> New Patient Visit');
    });

    // Close search results on click outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $results.addClass('d-none');
        }
    });
});
</script>
@endsection
@endsection
