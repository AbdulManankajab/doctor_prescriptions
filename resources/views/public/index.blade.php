@extends('layouts.public')

@section('title', 'Doctor Prescription System')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <h2 class="card-title mb-4">
                        <i class="bi bi-search text-primary"></i> Patient Search
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-10">
                            <input type="text" 
                                   id="searchInput" 
                                   class="form-control form-control-lg" 
                                   placeholder="Search by patient name, phone number, or patient number...">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="searchPatient()">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>

                    <div id="searchResults" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center p-5">
                    <i class="bi bi-file-earmark-medical display-1 text-primary mb-3"></i>
                    <h3 class="mb-3">Create New Prescription</h3>
                    <p class="text-muted mb-4">Start a new prescription for a patient</p>
                    <a href="{{ route('prescription.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> New Prescription
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function searchPatient() {
    const query = document.getElementById('searchInput').value;
    
    if (query.length < 2) {
        alert('Please enter at least 2 characters');
        return;
    }

    fetch('{{ route("patient.search") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ search: query })
    })
    .then(response => response.json())
    .then(data => {
        displayResults(data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error searching patients');
    });
}

function displayResults(patients) {
    const resultsDiv = document.getElementById('searchResults');
    
    if (patients.length === 0) {
        resultsDiv.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No patients found. 
                <a href="{{ route('prescription.create') }}" class="alert-link">Create a new prescription</a>
            </div>
        `;
        return;
    }

    let html = '<h5 class="mb-3">Search Results:</h5><div class="list-group">';
    
    patients.forEach(patient => {
        html += `
            <div class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1"><i class="bi bi-person"></i> ${patient.name}</h6>
                        <p class="mb-1 text-muted">
                            <small>
                                <i class="bi bi-hash"></i> ${patient.patient_number} | 
                                <i class="bi bi-telephone"></i> ${patient.phone} | 
                                <i class="bi bi-gender-${patient.gender.toLowerCase()}"></i> ${patient.gender}, ${patient.age} years
                            </small>
                        </p>
                        <p class="mb-0 text-muted">
                            <small><i class="bi bi-file-medical"></i> ${patient.prescriptions.length} prescription(s)</small>
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('prescription/create') }}/${patient.id}" class="btn btn-primary btn-sm me-2">
                            <i class="bi bi-plus"></i> New Prescription
                        </a>
                        <a href="{{ url('patient/history') }}/${patient.id}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-clock-history"></i> History
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    resultsDiv.innerHTML = html;
}

// Allow search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchPatient();
    }
});
</script>
@endsection
