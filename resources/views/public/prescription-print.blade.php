@extends('layouts.public')

@section('title', 'Print Prescription')

@section('styles')
<style>
    @media print {
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            background: white !important;
        }
        
        .print-area {
            width: 100%;
            max-width: 100%;
        }
    }
    
    .prescription-header {
        border-bottom: 3px solid #667eea;
        margin-bottom: 20px;
        padding-bottom: 15px;
    }
    
    .clinic-name {
        font-size: 2rem;
        font-weight: bold;
        color: #667eea;
    }
    
    .prescription-table {
        border: 2px solid #dee2e6;
    }
    
    .prescription-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row no-print mb-3">
        <div class="col-12">
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="bi bi-printer"></i> Print Prescription
            </button>
            <a href="{{ route('home') }}" class="btn btn-secondary btn-lg">
                <i class="bi bi-house"></i> Back to Home
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card print-area">
                <div class="card-body p-5">
                    <!-- Clinic Header -->
                    <div class="prescription-header text-center">
                        <div class="clinic-name">
                            <i class="bi bi-hospital"></i> Medical Clinic
                        </div>
                        <p class="mb-0 text-muted">Address: Your Clinic Address Here | Phone: +1234567890</p>
                    </div>

                    <!-- Prescription Header -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <p class="mb-1"><strong>Prescription No:</strong> {{ $prescription->prescription_number }}</p>
                            <p class="mb-1"><strong>Date:</strong> {{ $prescription->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1"><strong>Patient No:</strong> {{ $prescription->patient->patient_number }}</p>
                        </div>
                    </div>

                    <!-- Patient Information -->
                    <div class="border rounded p-3 mb-4" style="background-color: #f8f9fa;">
                        <h5 class="mb-3">
                            <i class="bi bi-person"></i> Patient Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Name:</strong> {{ $prescription->patient->name }}</p>
                                <p class="mb-2"><strong>Age:</strong> {{ $prescription->patient->age }} years</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Gender:</strong> {{ $prescription->patient->gender }}</p>
                                <p class="mb-2"><strong>Phone:</strong> {{ $prescription->patient->phone }}</p>
                            </div>
                            @if($prescription->patient->address)
                            <div class="col-12">
                                <p class="mb-0"><strong>Address:</strong> {{ $prescription->patient->address }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Diagnosis -->
                    <div class="mb-4">
                        <h5 class="mb-2">
                            <i class="bi bi-clipboard2-pulse"></i> Diagnosis
                        </h5>
                        <p class="border rounded p-3 mb-0">{{ $prescription->diagnosis }}</p>
                    </div>

                    <!-- Medicines -->
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="bi bi-capsule"></i> Prescribed Medicines
                        </h5>
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
                                    <td><strong>{{ $item->medicine->name }}</strong></td>
                                    <td>{{ ucfirst($item->type) }}</td>
                                    <td>{{ $item->dosage }}</td>
                                    <td>{{ $item->duration }}</td>
                                    <td>{{ $item->instructions }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Notes -->
                    @if($prescription->notes)
                    <div class="mb-4">
                        <h5 class="mb-2">
                            <i class="bi bi-sticky"></i> Additional Notes
                        </h5>
                        <p class="border rounded p-3 mb-0">{{ $prescription->notes }}</p>
                    </div>
                    @endif

                    <!-- Doctor Signature -->
                    <div class="mt-5 pt-4 text-end">
                        <div style="border-top: 2px solid #000; width: 200px; margin-left: auto; margin-top: 60px;">
                            <p class="mb-0 mt-2"><strong>Doctor's Signature</strong></p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-5 pt-3" style="border-top: 1px solid #dee2e6;">
                        <small class="text-muted">
                            This is a computer-generated prescription. For any queries, please contact the clinic.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
