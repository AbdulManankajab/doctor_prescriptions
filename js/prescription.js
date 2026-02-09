$(document).ready(function () {
    // Shared function for adding medicine row
    function addMedicineRow() {
        const rowCount = $('.medicine-row').length;
        const newRow = $('.medicine-row:first').clone();

        // Update name attributes for all inputs/selects
        newRow.find('input, select').each(function () {
            const name = $(this).attr('name');
            if (name) {
                // Ensure we don't accidentally rename the dummy ones badly
                const baseName = name.replace(/medicines\[\d+\]/, 'medicines[' + rowCount + ']');
                $(this).attr('name', baseName);
            }
        });

        // Reset values
        newRow.find('input').val('').hide(); // Hide custom inputs in new row
        newRow.find('select').each(function () {
            $(this).val($(this).find('option:first').val());
        });

        // Reset dosage select and hide its custom input
        const dosageSelect = newRow.find('.medicine-dosage');
        dosageSelect.empty().append('<option value="">Select Dosage</option><option value="custom_entry">Other (Type custom)...</option>');
        dosageSelect.attr('name', 'medicines[' + rowCount + '][dosage]');

        const customInput = newRow.find('.custom-dosage-input');
        customInput.attr('name', '').hide().val('');

        // Add row to container
        $('#medicinesContainer').append(newRow);

        // Show all remove buttons
        $('.remove-medicine').show();

        return newRow;
    }

    // Add medicine row - Only bottom button now exists in HTML
    $('#add-medicine-bottom').click(addMedicineRow);
    // Support top button if it still exists (fallback)
    $('#add-medicine').click(addMedicineRow);

    // Remove medicine row
    $(document).on('click', '.remove-medicine', function () {
        if ($('.medicine-row').length > 1) {
            $(this).closest('.medicine-row').remove();
            updateRowIndices();

            if ($('.medicine-row').length === 1) {
                $('.remove-medicine').hide();
            }
        }
    });

    // Handle medicine selection change
    $(document).on('change', '.medicine-select', function () {
        const row = $(this).closest('.medicine-row');
        const selectedOption = this.options[this.selectedIndex];

        const dosageSelect = row.find('.medicine-dosage');
        const customInput = row.find('.custom-dosage-input');

        // Reset dosage
        dosageSelect.empty().append('<option value="">Select Dosage</option><option value="custom_entry">Other (Type custom)...</option>');
        dosageSelect.val('');
        customInput.hide().val('').attr('name', '');

        // Ensure name is back on select
        const index = row.index();
        dosageSelect.attr('name', `medicines[${index}][dosage]`);

        if (!selectedOption || !selectedOption.value) return;

        const type = $(selectedOption).data('type');
        const dosages = $(selectedOption).data('dosages');

        if (type) row.find('.medicine-type').val(type);

        if (dosages && dosages.trim() !== "") {
            const dosageArray = dosages.toString().split(',').map(s => s.trim());

            dosageArray.forEach(d => {
                if (d) {
                    dosageSelect.append(`<option value="${d}">${d}</option>`);
                }
            });

            if (dosageArray.length === 1 && dosageArray[0]) {
                dosageSelect.val(dosageArray[0]);
            }
        }
    });

    // Handle Dosage Selection (Other logic)
    $(document).on('change', '.medicine-dosage', function () {
        const row = $(this).closest('.medicine-row');
        const customInput = row.find('.custom-dosage-input');
        const index = $('.medicine-row').index(row);

        if ($(this).val() === 'custom_entry') {
            customInput.show().focus();
            // Swap names so input is submitted instead of select
            customInput.attr('name', `medicines[${index}][dosage]`);
            $(this).attr('name', `medicines[${index}][dosage_select_dummy]`);
        } else {
            customInput.hide().val('').attr('name', '');
            $(this).attr('name', `medicines[${index}][dosage]`);
        }
    });

    // Handle default notes buttons
    $(document).on('click', '.default-note-btn', function () {
        const noteText = $(this).data('text');
        const notesTextArea = $('#notes');
        const currentText = notesTextArea.val();

        if (currentText) {
            notesTextArea.val(currentText + '\n' + noteText);
        } else {
            notesTextArea.val(noteText);
        }

        notesTextArea.focus();
    });

    // Function to update indices after removal
    function updateRowIndices() {
        $('.medicine-row').each(function (index) {
            $(this).find('input, select').each(function () {
                const name = $(this).attr('name');
                if (name) {
                    // Be careful with the dummy names
                    let newName = '';
                    if (name.includes('dosage_select_dummy')) {
                        newName = `medicines[${index}][dosage_select_dummy]`;
                    } else {
                        newName = name.replace(/medicines\[\d+\]/, 'medicines[' + index + ']');
                    }
                    $(this).attr('name', newName);
                }
            });
        });
    }
});
