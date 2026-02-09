@extends('layouts.public')

@section('title', 'Create Prescription')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-file-earmark-medical"></i> Create New Prescription
                    </h3>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('prescription.store') }}" method="POST" id="prescriptionForm">
                        @csrf

                        <!-- Patient Information -->
                        <h5 class="mb-3">
                            <i class="bi bi-person-badge"></i> Patient Information
                        </h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Patient Name *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="{{ $patient->name ?? old('name') }}" 
                                       required>
                            </div>
                            <div class="col-md-3">
                                <label for="age" class="form-label">Age *</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="age" 
                                       name="age" 
                                       value="{{ $patient->age ?? old('age') }}" 
                                       min="1" 
                                       required>
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select...</option>
                                    <option value="Male" {{ (isset($patient) && $patient->gender == 'Male') || old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ (isset($patient) && $patient->gender == 'Female') || old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ (isset($patient) && $patient->gender == 'Other') || old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ $patient->phone ?? old('phone') }}" 
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="address" 
                                       name="address" 
                                       value="{{ $patient->address ?? old('address') }}">
                            </div>
                        </div>

                        <!-- Prescription Details -->
                        <h5 class="mb-3">
                            <i class="bi bi-clipboard2-pulse"></i> Prescription Details
                        </h5>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="diagnosis" class="form-label">Diagnosis *</label>
                                <textarea class="form-control" 
                                          id="diagnosis" 
                                          name="diagnosis" 
                                          rows="2" 
                                          required>{{ old('diagnosis') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <div class="mb-2">
                                    <small class="text-muted">Quick select default notes:</small>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        @foreach($defaultNotes as $note)
                                            <button type="button" class="btn btn-sm btn-outline-secondary default-note-btn" data-text="{{ $note->detail_text }}">
                                                {{ Str::limit($note->detail_text, 30) }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <textarea class="form-control" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Medicines -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-capsule"></i> Medicines</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-medicine">
                                <i class="bi bi-plus-circle"></i> Add Another Medicine
                            </button>
                        </div>

                        <div id="medicinesContainer">
                            <div class="medicine-row border rounded p-3 mb-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Medicine Name *</label>
                                        <select name="medicines[0][medicine_id]" class="form-select medicine-select" required>
                                            <option value="">Select Medicine</option>
                                            @foreach($medicines as $medicine)
                                                <option value="{{ $medicine->id }}" data-type="{{ $medicine->type }}" data-dosages="{{ $medicine->dosage_options }}">
                                                    {{ $medicine->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Type *</label>
                                        <select name="medicines[0][type]" class="form-select medicine-type" required>
                                            <option value="tablet">Tablet</option>
                                            <option value="syrup">Syrup</option>
                                            <option value="capsule">Capsule</option>
                                            <option value="injection">Injection</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Dosage *</label>
                                        <select name="medicines[0][dosage]" class="form-select medicine-dosage" required>
                                            <option value="">Dosage</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Duration *</label>
                                        <select name="medicines[0][duration]" class="form-select" required>
                                            <option value="3 days">3 days</option>
                                            <option value="5 days">5 days</option>
                                            <option value="7 days" selected>7 days</option>
                                            <option value="10 days">10 days</option>
                                            <option value="14 days">14 days</option>
                                            <option value="1 month">1 month</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="w-100">
                                            <label class="form-label">Timing *</label>
                                            <select name="medicines[0][instructions]" class="form-select" required>
                                                <option value="Before meal">Before meal</option>
                                                <option value="After meal" selected>After meal</option>
                                                <option value="During meal">During meal</option>
                                                <option value="Empty stomach">Empty stomach</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger ms-2 remove-medicine" style="display: none; height: 38px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Save & Print Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/prescription.js') }}"></script>
@endsection
