@extends('layouts.doctor')

@section('title', 'Create Prescription')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Progress Stepper -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between position-relative">
                    <div class="step-line position-absolute w-100 top-50 start-0 translate-middle-y bg-light" style="height: 2px; z-index: 0;"></div>
                    <div class="step-progress position-absolute top-50 start-0 translate-middle-y bg-primary transition-all" style="height: 2px; z-index: 1; width: 0%;"></div>
                    
                    @foreach(['Patient', 'Examination', 'Diagnosis', 'Allergies', 'Prescription'] as $index => $step)
                        <div class="step-item text-center position-relative" style="z-index: 2; width: 20%;">
                            <div class="step-icon mx-auto rounded-circle d-flex align-items-center justify-content-center border {{ $index == 0 ? 'bg-primary border-primary text-white' : 'bg-white border-light text-muted' }}" 
                                 style="width: 40px; height: 40px; transition: all 0.3s;"
                                 id="step-icon-{{ $index + 1 }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="step-label small mt-2 fw-semibold {{ $index == 0 ? 'text-primary' : 'text-muted' }}" id="step-label-{{ $index + 1 }}">
                                {{ $step }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white p-4 border-0">
                <h3 class="mb-0 fw-bold" id="step-title">
                    <i class="bi bi-person-badge text-primary me-2"></i> Patient Information
                </h3>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('doctor.prescription.store') }}" method="POST" id="prescriptionForm" enctype="multipart/form-data" novalidate>
                    @csrf

                    <!-- STEP 1: Patient Information -->
                    <div class="step-section" id="step-section-1">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Patient Name *</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $patient->name ?? old('name') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="age" class="form-label fw-semibold">Age *</label>
                                <input type="number" class="form-control" id="age" name="age" value="{{ $patient->age ?? old('age') }}" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label fw-semibold">Gender *</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select...</option>
                                    <option value="Male" {{ (isset($patient) && $patient->gender == 'Male') || old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ (isset($patient) && $patient->gender == 'Female') || old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ (isset($patient) && $patient->gender == 'Other') || old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number *</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $patient->phone ?? old('phone') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label fw-semibold">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ $patient->address ?? old('address') }}">
                            </div>
                        </div>
                        
                        <!-- History Selection (if patient exists) -->
                        @if(isset($patient) && $patient->examinations->count() > 0)
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-info"></i> Previous Examinations</h6>
                            <div class="list-group list-group-flush border rounded">
                                @foreach($patient->examinations->take(3) as $prevExam)
                                    <div class="list-group-item p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-muted small">{{ $prevExam->created_at->format('d M Y') }}</span>
                                                <p class="mb-0 text-truncate" style="max-width: 400px;">{{ Str::limit($prevExam->notes, 100) }}</p>
                                            </div>
                                            @if($prevExam->files->count() > 0)
                                                <span class="badge bg-light text-primary border rounded-pill">{{ $prevExam->files->count() }} files</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- STEP 2: Examination Results -->
                    <div class="step-section d-none" id="step-section-2">
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="examination_notes" class="form-label fw-semibold">Examination Findings (Optional)</label>
                                <textarea class="form-control" 
                                          id="examination_notes" 
                                          name="examination_notes" 
                                          rows="6" 
                                          placeholder="Enter symptoms, physical findings, vital signs, and clinical observations...">{{ old('examination_notes') }}</textarea>
                                <div class="form-text mt-2">Include: BP, Temp, Pulse, SpO2 if applicable.</div>
                            </div>
                            <div class="col-12 mt-4">
                                <label class="form-label fw-semibold">Upload Reports / Images (Optional)</label>
                                <div class="border-dashed-2 p-4 text-center rounded-4 bg-light">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-muted mb-3 d-block"></i>
                                    <input type="file" name="examination_files[]" id="examination_files" multiple class="d-none" accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('examination_files').click()">
                                        <i class="bi bi-paperclip me-2"></i> Choose Files
                                    </button>
                                    <p class="text-muted small mt-2 mb-0">Supported types: PDF, JPG, PNG (Max 10MB each)</p>
                                    <div id="file-list" class="mt-3 text-start small"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Diagnosis -->
                    <div class="step-section d-none" id="step-section-3">
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label for="primary_diagnosis" class="form-label fw-semibold">Primary Diagnosis (Optional)</label>
                                <input type="text" class="form-control form-control-lg" id="primary_diagnosis" name="primary_diagnosis" value="{{ old('primary_diagnosis') }}" placeholder="Enter primary condition...">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label for="secondary_diagnosis" class="form-label fw-semibold">Secondary Diagnosis (Optional)</label>
                                <input type="text" class="form-control" id="secondary_diagnosis" name="secondary_diagnosis" value="{{ old('secondary_diagnosis') }}" placeholder="Enter co-morbidities or secondary conditions...">
                            </div>
                        </div>
                        
                        @if(isset($patient) && $patient->diagnoses->count() > 0)
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3 text-muted">Past Diagnoses</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($patient->diagnoses->pluck('primary_diagnosis')->unique() as $prevDiag)
                                    <span class="badge bg-soft-info text-info border px-3 py-2 rounded-pill pointer hover-bg-primary diag-badge" data-diag="{{ $prevDiag }}">
                                        {{ $prevDiag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- STEP 4: Allergies -->
                    <div class="step-section d-none" id="step-section-4">
                        <div id="allergy-warning-box" class="alert alert-danger border-0 shadow-sm d-none mb-4">
                            <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> CRITICAL: Known Allergies!</h5>
                            <ul id="allergy-list-display" class="mb-0 fw-bold"></ul>
                        </div>

                        <div class="card border border-warning bg-soft-warning mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold text-warning-emphasis mb-3"><i class="bi bi-plus-circle me-2"></i> Manage Known Allergies</h6>
                                <div id="allergies-input-container">
                                    <div class="row g-2 mb-2 allergy-row">
                                        <div class="col-md-6">
                                            <input type="text" name="allergies[0][name]" class="form-control allergy-name-input" placeholder="Allergy Name (e.g. Penicillin)">
                                        </div>
                                        <div class="col-md-4">
                                            <select name="allergies[0][type]" class="form-select">
                                                <option value="medicine">Medicine</option>
                                                <option value="food">Food</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-allergy d-none"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-warning mt-2" id="add-allergy-btn">
                                    <i class="bi bi-plus me-1"></i> Add Another Allergy
                                </button>
                            </div>
                        </div>

                        <!-- Existing Allergies -->
                        @if(isset($patient) && $patient->allergies->count() > 0)
                        <div class="alert alert-info border-0 shadow-sm">
                            <h6 class="alert-heading fw-bold mb-2">Existing Allergies for this patient:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($patient->allergies as $allergy)
                                    <span class="badge bg-white text-dark border px-3 py-2 rounded-pill">
                                        {{ $allergy->allergy_name }} ({{ ucfirst($allergy->allergy_type) }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- STEP 5: Prescription -->
                    <div class="step-section d-none" id="step-section-5">
                        <div id="medicine-allergy-alert" class="alert alert-danger border-0 shadow-lg d-none mb-4 animate__animated animate__shakeX">
                            <div class="d-flex">
                                <i class="bi bi-shield-fill-exclamation fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">ALLERGY ALERT!</h6>
                                    <p class="mb-0" id="medicine-allergy-msg"></p>
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="confirmAllergyOverride">
                                            <label class="form-check-label small" for="confirmAllergyOverride">
                                                I confirm that I have reviewed the allergy alert and wish to proceed anyway.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medicines Section (From existing) -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 text-primary"><i class="bi bi-capsule me-2"></i> Prescribed Medicines</h5>
                        </div>

                        <div id="medicinesContainer">
                            @for($i = 0; $i < 3; $i++)
                            <div class="medicine-row border rounded-4 p-4 mb-4 bg-light border-0 shadow-sm">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Medicine Name *</label>
                                        <select name="medicines[{{ $i }}][medicine_id]" class="form-select medicine-select" required>
                                            <option value="">Select Medicine</option>
                                            @foreach($medicines as $medicine)
                                                <option value="{{ $medicine->id }}" 
                                                        data-name="{{ $medicine->name }}"
                                                        data-type="{{ $medicine->type }}" 
                                                        data-dosages="{{ $medicine->dosage_options }}">
                                                    {{ $medicine->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Type *</label>
                                        <select name="medicines[{{ $i }}][type]" class="form-select medicine-type" required>
                                            <option value="tablet">Tablet</option>
                                            <option value="syrup">Syrup</option>
                                            <option value="capsule">Capsule</option>
                                            <option value="injection">Injection</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Dosage *</label>
                                        <div class="dosage-wrapper">
                                            <select name="medicines[{{ $i }}][dosage]" class="form-select medicine-dosage" required>
                                                <option value="">Select Dosage</option>
                                                <option value="custom_entry">Other (Type custom)...</option>
                                            </select>
                                            <input type="text" class="form-control custom-dosage-input mt-2" style="display: none;" placeholder="e.g. 1.5 tabs">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Duration *</label>
                                        <select name="medicines[{{ $i }}][duration]" class="form-select" required>
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
                                            <label class="form-label fw-semibold">Timing *</label>
                                            <select name="medicines[{{ $i }}][instructions]" class="form-select" required>
                                                <option value="Before meal">Before meal</option>
                                                <option value="After meal" selected>After meal</option>
                                                <option value="During meal">During meal</option>
                                                <option value="Empty stomach">Empty stomach</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger ms-2 remove-medicine border-0" style="{{ $i < 3 ? 'display: none;' : '' }}"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>

                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4" id="add-medicine-bottom">
                                <i class="bi bi-plus-circle me-1"></i> Add Another Medicine
                            </button>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-semibold">Doctor's Clinical Instructions</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Rest for 2 days, avoid cold water, etc...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                        <button type="button" class="btn btn-light px-4 rounded-pill d-none" id="prevBtn">
                            <i class="bi bi-arrow-left me-2"></i> Previous
                        </button>
                        <a href="{{ route('doctor.dashboard') }}" class="btn btn-light px-4 rounded-pill" id="dashboardBtn">
                            <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
                        </a>
                        
                        <button type="button" class="btn btn-primary px-5 rounded-pill shadow" id="nextBtn">
                            Next <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow d-none" id="submitBtn">
                            <i class="bi bi-check2-circle me-2"></i> Finalize & Print
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.transition-all { transition: all 0.3s ease-in-out; }
.border-dashed-2 { border: 2px dashed #dee2e6; }
.bg-soft-warning { background-color: #fff9e6; }
.bg-soft-info { background-color: #e6f7ff; }
.text-warning-emphasis { color: #664d03; }
.pointer { cursor: pointer; }
.hover-bg-primary:hover { background-color: var(--bs-primary) !important; color: white !important; }
</style>

@php
    // Pass existing allergies to JS
    $existingAllergies = isset($patient) ? $patient->allergies->pluck('allergy_name')->toArray() : [];
@endphp

@section('scripts')
<script>
    var patientAllergies = @json($existingAllergies);

    // Embed prescription.js content directly for immediate resolution
    window.PrescriptionWorkflow = {
        currentStep: 1,
        totalSteps: 5,
        patientAllergies: (typeof patientAllergies !== 'undefined') ? patientAllergies : [],

        init: function() {
            console.log("INTERNAL: Initializing Prescription Workflow...");
            this.updateUI();
            this.bindEvents();
        },

        updateUI: function() {
            const step = this.currentStep;
            console.log("INTERNAL: Updating UI for step:", step);

            // Update Stepper Icons/Labels
            for (let i = 1; i <= this.totalSteps; i++) {
                const icon = $(`#step-icon-${i}`);
                const label = $(`#step-label-${i}`);
                
                if (i < step) {
                    icon.attr('class', 'step-icon mx-auto rounded-circle d-flex align-items-center justify-content-center border bg-success border-success text-white');
                    icon.html('<i class="bi bi-check-lg"></i>');
                    label.attr('class', 'step-label small mt-2 fw-semibold text-success');
                } else if (i === step) {
                    icon.attr('class', 'step-icon mx-auto rounded-circle d-flex align-items-center justify-content-center border bg-primary border-primary text-white');
                    icon.text(i);
                    label.attr('class', 'step-label small mt-2 fw-semibold text-primary');
                } else {
                    icon.attr('class', 'step-icon mx-auto rounded-circle d-flex align-items-center justify-content-center border bg-white border-light text-muted');
                    icon.text(i);
                    label.attr('class', 'step-label small mt-2 fw-semibold text-muted');
                }
            }

            // Progress Bar
            const progressWidth = ((step - 1) / (this.totalSteps - 1)) * 100;
            $('.step-progress').css('width', progressWidth + '%');

            // Section Visibility
            $('.step-section').addClass('d-none');
            $(`#step-section-${step}`).removeClass('d-none');

            // Button Visibility
            if (step === 1) {
                $('#prevBtn').addClass('d-none');
                $('#dashboardBtn').removeClass('d-none');
            } else {
                $('#prevBtn').removeClass('d-none');
                $('#dashboardBtn').addClass('d-none');
            }

            if (step === this.totalSteps) {
                $('#nextBtn').addClass('d-none');
                $('#submitBtn').removeClass('d-none');
            } else {
                $('#nextBtn').removeClass('d-none');
                $('#submitBtn').addClass('d-none');
            }

            // Title Update
            const titles = ['Patient Information', 'Examination Findings', 'Diagnosis', 'Allergy Review', 'Prescription Creation'];
            const icons = ['bi-person-badge', 'bi-clipboard-pulse', 'bi-search', 'bi-exclamation-triangle', 'bi-file-earmark-medical'];
            $('#step-title').html(`<i class="bi ${icons[step-1]} text-primary me-2"></i> ${titles[step-1]}`);

            if (step === 4) this.refreshAllergySummary();
        },

        changeStep: function(n) {
            console.log("INTERNAL: Changing step by:", n);
            if (n === 1 && !this.validateCurrentStep()) {
                console.warn("INTERNAL: Validation failed for step", this.currentStep);
                return;
            }

            const nextStep = this.currentStep + n;
            if (nextStep < 1 || nextStep > this.totalSteps) return;

            this.currentStep = nextStep;
            this.updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        validateCurrentStep: function() {
            let isValid = true;
            const $section = $(`#step-section-${this.currentStep}`);
            
            // Find all elements that should be required
            $section.find('input, select, textarea').each(function() {
                if ($(this).prop('required') || $(this).attr('required')) {
                    const val = $(this).val();
                    if (!val || val.toString().trim() === "") {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                }
            });

            if (!isValid) {
                const $firstError = $section.find('.is-invalid').first();
                if ($firstError.length) {
                    $('html, body').animate({ scrollTop: $firstError.offset().top - 150 }, 500);
                }
            }
            return isValid;
        },

        refreshAllergySummary: function() {
            const $list = $('#allergy-list-display');
            $list.empty();
            
            let allergies = Array.isArray(this.patientAllergies) ? [...this.patientAllergies] : [];
            
            $('.allergy-name-input').each(function() {
                const val = $(this).val().trim();
                if (val && !allergies.includes(val)) allergies.push(val);
            });

            if (allergies.length > 0) {
                $('#allergy-warning-box').removeClass('d-none');
                allergies.forEach(a => $list.append(`<li>${a}</li>`));
            } else {
                $('#allergy-warning-box').addClass('d-none');
            }
        },

        bindEvents: function() {
            const self = this;

            console.log("INTERNAL: Binding events...");

            // Main buttons - highest priority
            document.getElementById('nextBtn').addEventListener('click', function(e) {
                e.preventDefault();
                console.log("INTERNAL: Next button clicked (Vanilla)");
                self.changeStep(1);
            });

            document.getElementById('prevBtn').addEventListener('click', function(e) {
                e.preventDefault();
                console.log("INTERNAL: Prev button clicked (Vanilla)");
                self.changeStep(-1);
            });

            // Diagnosis Badges
            $(document).on('click', '.diag-badge', function() {
                $('#primary_diagnosis').val($(this).data('diag')).removeClass('is-invalid');
            });

            // File Selection
            $(document).on('change', '#examination_files', function() {
                const $fileList = $('#file-list');
                $fileList.empty();
                if (this.files) {
                    for (let i = 0; i < this.files.length; i++) {
                        $fileList.append(`<div><i class="bi bi-file-earmark-check me-2"></i> ${this.files[i].name}</div>`);
                    }
                }
            });

            // Allergy rows
            let allergyIdx = 100;
            $(document).on('click', '#add-allergy-btn', function() {
                const $template = $('.allergy-row:first').clone();
                $template.find('input').val('').attr('name', `allergies[${allergyIdx}][name]`);
                $template.find('select').val('medicine').attr('name', `allergies[${allergyIdx}][type]`);
                $template.find('.remove-allergy').removeClass('d-none');
                $('#allergies-input-container').append($template);
                allergyIdx++;
            });

            $(document).on('click', '.remove-allergy', function() {
                if ($('.allergy-row').length > 1) {
                    $(this).closest('.allergy-row').remove();
                } else {
                    $(this).closest('.allergy-row').find('input').val('');
                }
            });

            // Medicine row management
            $(document).on('click', '#add-medicine-bottom', function() {
                const idx = $('.medicine-row').length;
                const $newRow = $('.medicine-row:first').clone();
                
                $newRow.find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/medicines\[\d+\]/, `medicines[${idx}]`));
                    }
                    if ($(this).is('input')) $(this).val('');
                    if ($(this).is('select')) $(this).val($(this).find('option:first').val());
                });

                $newRow.find('.custom-dosage-input').hide();
                $newRow.find('.medicine-dosage').empty().append('<option value="">Select Dosage</option><option value="custom_entry">Other (Type custom)...</option>');
                $newRow.find('.remove-medicine').show();
                
                $('#medicinesContainer').append($newRow);
                self.runAllergyCrossCheck();
            });

            $(document).on('click', '.remove-medicine', function() {
                if ($('.medicine-row').length > 3) {
                    $(this).closest('.medicine-row').remove();
                    self.reindexMedicineRows();
                    self.runAllergyCrossCheck();
                }
            });

            $(document).on('change', '.medicine-select', function() {
                const selectedVal = $(this).val();
                if (selectedVal) {
                    let duplicate = false;
                    const $current = $(this);
                    
                    $('.medicine-select').each(function() {
                        if (this !== $current[0] && $(this).val() === selectedVal) {
                            duplicate = true;
                            return false;
                        }
                    });

                    if (duplicate) {
                        alert('This medicine is already added to the prescription.');
                        $(this).val('');
                        return;
                    }
                }

                self.runAllergyCrossCheck();
                const $row = $(this).closest('.medicine-row');
                const $opt = $(this).find('option:selected');
                const $dosage = $row.find('.medicine-dosage');
                const $type = $row.find('.medicine-type');

                $dosage.empty().append('<option value="">Select Dosage</option><option value="custom_entry">Other (Type custom)...</option>');
                
                if ($opt.val()) {
                    if ($opt.data('type')) $type.val($opt.data('type'));
                    const dosages = $opt.data('dosages');
                    if (dosages) {
                        dosages.toString().split(',').forEach(d => {
                            if (d.trim()) $dosage.append(`<option value="${d.trim()}">${d.trim()}</option>`);
                        });
                    }
                }
            });

            $(document).on('change', '.medicine-dosage', function() {
                const $row = $(this).closest('.medicine-row');
                const $custom = $row.find('.custom-dosage-input');
                const idx = $('.medicine-row').index($row);

                if ($(this).val() === 'custom_entry') {
                    $custom.show().focus().prop('required', true).attr('name', `medicines[${idx}][dosage]`);
                    $(this).attr('name', `medicines[${idx}][dosage_dummy]`);
                } else {
                    $custom.hide().val('').prop('required', false).attr('name', '');
                    $(this).attr('name', `medicines[${idx}][dosage]`);
                }
            });
        },

        reindexMedicineRows: function() {
            $('.medicine-row').each(function(index) {
                $(this).find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/medicines\[\d+\]/, `medicines[${index}]`);
                        $(this).attr('name', newName);
                    }
                });
            });
        },

        runAllergyCrossCheck: function() {
            let allergies = Array.isArray(this.patientAllergies) ? [...this.patientAllergies] : [];
            $('.allergy-name-input').each(function() {
                const v = $(this).val().trim().toLowerCase();
                if (v && !allergies.includes(v)) allergies.push(v);
            });

            let conflicts = [];
            $('.medicine-select').each(function() {
                const medName = $(this).find('option:selected').data('name');
                if (medName) {
                    const lowMed = medName.toLowerCase();
                    allergies.forEach(a => {
                        if (lowMed.includes(a.toLowerCase()) || a.toLowerCase().includes(lowMed)) {
                            if (!conflicts.includes(medName)) conflicts.push(medName);
                        }
                    });
                }
            });

            if (conflicts.length > 0) {
                $('#medicine-allergy-alert').removeClass('d-none');
                $('#medicine-allergy-msg').text(`WARNING: Selected medicines (${conflicts.join(', ')}) may conflict with patient allergies.`);
                $('#confirmAllergyOverride').prop('required', true);
            } else {
                $('#medicine-allergy-alert').addClass('d-none');
                $('#confirmAllergyOverride').prop('required', false).prop('checked', false);
            }
        }
    };

    $(document).ready(function() {
        console.log("INTERNAL: Document Ready...");
        PrescriptionWorkflow.init();

        // Final submission validation
        $('#prescriptionForm').on('submit', function(e) {
            if ($('#medicine-allergy-alert').is(':visible') && !$('#confirmAllergyOverride').is(':checked')) {
                e.preventDefault();
                $('#confirmAllergyOverride').addClass('is-invalid');
                alert('Please confirm that you have reviewed the allergy alert before finalizing.');
                return false;
            }
        });
    });
</script>
@endsection
@endsection
