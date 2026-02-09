// Use a global object to avoid conflicts and ensure state persistence
window.PrescriptionWorkflow = {
    currentStep: 1,
    totalSteps: 5,
    patientAllergies: (typeof patientAllergies !== 'undefined') ? patientAllergies : [],

    init: function () {
        console.log("Initializing Prescription Workflow...");
        this.updateUI();
        this.bindEvents();
    },

    updateUI: function () {
        const step = this.currentStep;
        console.log("Updating UI for step:", step);

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
        $('#step-title').html(`<i class="bi ${icons[step - 1]} text-primary me-2"></i> ${titles[step - 1]}`);

        if (step === 4) this.refreshAllergySummary();
    },

    changeStep: function (n) {
        console.log("Changing step by:", n);
        if (n === 1 && !this.validateCurrentStep()) {
            console.warn("Validation failed for step", this.currentStep);
            return;
        }

        const nextStep = this.currentStep + n;
        if (nextStep < 1 || nextStep > this.totalSteps) return;

        this.currentStep = nextStep;
        this.updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    validateCurrentStep: function () {
        let isValid = true;
        const $section = $(`#step-section-${this.currentStep}`);

        // Find all elements that should be required
        $section.find('input, select, textarea').each(function () {
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

    refreshAllergySummary: function () {
        const $list = $('#allergy-list-display');
        $list.empty();

        let allergies = Array.isArray(this.patientAllergies) ? [...this.patientAllergies] : [];

        $('.allergy-name-input').each(function () {
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

    bindEvents: function () {
        const self = this;

        // Use event delegation for buttons to be safe
        $(document).on('click', '#nextBtn', function (e) {
            e.preventDefault();
            console.log("Next button clicked via delegation");
            self.changeStep(1);
        });

        $(document).on('click', '#prevBtn', function (e) {
            e.preventDefault();
            console.log("Prev button clicked via delegation");
            self.changeStep(-1);
        });

        // Direct binding as backup
        $('#nextBtn').on('click', function (e) {
            e.preventDefault();
            console.log("Next button clicked via direct binding");
            self.changeStep(1);
        });

        // Diagnosis Badges
        $(document).on('click', '.diag-badge', function () {
            $('#primary_diagnosis').val($(this).data('diag')).removeClass('is-invalid');
        });

        // File Selection
        $(document).on('change', '#examination_files', function () {
            const $fileList = $('#file-list');
            $fileList.empty();
            if (this.files) {
                for (let i = 0; i < this.files.length; i++) {
                    $fileList.append(`<div><i class="bi bi-file-earmark-check me-2"></i> ${this.files[i].name}</div>`);
                }
            }
        });

        // Allergy rows
        let allergyIdx = 100; // start high to avoid collision
        $(document).on('click', '#add-allergy-btn', function () {
            const $template = $('.allergy-row:first').clone();
            $template.find('input').val('').attr('name', `allergies[${allergyIdx}][name]`);
            $template.find('select').val('medicine').attr('name', `allergies[${allergyIdx}][type]`);
            $template.find('.remove-allergy').removeClass('d-none');
            $('#allergies-input-container').append($template);
            allergyIdx++;
        });

        $(document).on('click', '.remove-allergy', function () {
            if ($('.allergy-row').length > 1) {
                $(this).closest('.allergy-row').remove();
            } else {
                $(this).closest('.allergy-row').find('input').val('');
            }
        });

        // Medicine row management
        $(document).on('click', '#add-medicine-bottom', function () {
            const idx = $('.medicine-row').length;
            const $newRow = $('.medicine-row:first').clone();

            $newRow.find('input, select').each(function () {
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

        $(document).on('click', '.remove-medicine', function () {
            if ($('.medicine-row').length > 1) {
                $(this).closest('.medicine-row').remove();
                self.reindexMedicineRows();
                self.runAllergyCrossCheck();
            }
        });

        $(document).on('change', '.medicine-select', function () {
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

        $(document).on('change', '.medicine-dosage', function () {
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

    reindexMedicineRows: function () {
        $('.medicine-row').each(function (index) {
            $(this).find('input, select').each(function () {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/medicines\[\d+\]/, `medicines[${index}]`);
                    $(this).attr('name', newName);
                }
            });
        });
    },

    runAllergyCrossCheck: function () {
        let allergies = Array.isArray(this.patientAllergies) ? [...this.patientAllergies] : [];
        $('.allergy-name-input').each(function () {
            const v = $(this).val().trim().toLowerCase();
            if (v && !allergies.includes(v)) allergies.push(v);
        });

        let conflicts = [];
        $('.medicine-select').each(function () {
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
        } else {
            $('#medicine-allergy-alert').addClass('d-none');
        }
    }
};

// Start the engine
$(document).ready(function () {
    console.log("Document ready - initializing script.");
    PrescriptionWorkflow.init();
});
