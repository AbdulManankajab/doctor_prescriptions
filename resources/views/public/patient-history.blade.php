@extends('layouts.public')

@section('title', 'Patient History')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-clock-history"></i> Patient Prescription History
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Patient Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Name:</strong> {{ $patient->name }}</p>
                            <p class="mb-2"><strong>Patient Number:</strong> {{ $patient->patient_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Age/Gender:</strong> {{ $patient->age }} years / {{ $patient->gender }}</p>
                            <p class="mb-2"><strong>Phone:</strong> {{ $patient->phone }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Prescriptions -->
                    <h5 class="mb-3">Prescription History ({{ $patient->prescriptions->count() }} total)</h5>
                    
                    @if($patient->prescriptions->count() > 0)
                        <div class="accordion" id="prescriptionAccordion">
                            @foreach($patient->prescriptions->sortByDesc('created_at') as $index => $prescription)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#prescription{{ $prescription->id }}">
                                        <strong>{{ $prescription->prescription_number }}</strong> 
                                        &nbsp;-&nbsp; {{ $prescription->created_at->format('d M Y, h:i A') }}
                                        &nbsp;<span class="badge bg-primary">{{ $prescription->items->count() }} medicines</span>
                                    </button>
                                </h2>
                                <div id="prescription{{ $prescription->id }}" 
                                     class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                     data-bs-parent="#prescriptionAccordion">
                                    <div class="accordion-body">
                                        <p><strong>Diagnosis:</strong> {{ $prescription->diagnosis }}</p>
                                        
                                        @if($prescription->notes)
                                        <p><strong>Notes:</strong> {{ $prescription->notes }}</p>
                                        @endif

                                        <h6 class="mt-3 mb-2">Medicines:</h6>
                                                <table class="table table-sm table-bordered mt-2">
                                                    <thead>
                                                        <tr class="table-light">
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
                                                            <td>{{ $item->medicine->name }}</td>
                                                            <td>{{ ucfirst($item->type) }}</td>
                                                            <td>{{ $item->dosage }}</td>
                                                            <td>{{ $item->duration }}</td>
                                                            <td>{{ $item->instructions }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                        <a href="{{ route('prescription.print', $prescription->id) }}" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank">
                                            <i class="bi bi-printer"></i> Print
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            No prescriptions found for this patient.
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Home
                        </a>
                        <a href="{{ route('prescription.create', $patient->id) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> New Prescription
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
