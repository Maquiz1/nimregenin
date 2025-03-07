$(document).ready(function () {
    // Function to toggle consent_date visibility
    function toggleConsentDate() {
        const consentYes = $('input[name="consent"]:checked').val();
        $('#consent_date_container').toggle(consentYes === '1');
    }

    // Add event listeners to consent radio buttons
    $('input[name="consent"]').on('change', toggleConsentDate);
    toggleConsentDate(); // Initial check

    // PID validation pattern
    const pidPattern = /^\d{3}$/;

    // Function to check if PID exists in the database
    async function checkIfPidExists(pid) {
        try {
            const response = await fetch('check_pid.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pid: pid }),
            });
            const data = await response.json();
            return data.exists;
        } catch (error) {
            console.error('Error checking PID:', error);
            return false;
        }
    }

    // Form submission validation
    $('#screening').on('submit', async function (event) {
        let isValid = true;

        // Clear previous errors
        $('.text-danger').hide().text('');

        // Validate PID fields
        const pid1 = $('#pid1').val().trim();
        const pid2 = $('#pid2').val().trim();

        if (!pidPattern.test(pid1) || !pidPattern.test(pid2)) {
            $('#pid1_error, #pid2_error').text('PID must be exactly 3 digits').show();
            isValid = false;
        }

        if (pid1 !== pid2) {
            $('#pid1_error, #pid2_error').text('PID values do not match').show();
            isValid = false;
        }

        // Check PID existence
        if (isValid && pid1) {
            const pidExists = await checkIfPidExists(pid1);
            if (pidExists) {
                $('#pid1_error').text('PID already exists').show();
                isValid = false;
            }
        }

        // Validate screening date
        const screeningDateInput = $('#screening_date').val();
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const startDate = new Date('2025-01-20');
        startDate.setHours(0, 0, 0, 0);

        if (!screeningDateInput) {
            $('#screening_date_error').text('Screening date is required').show();
            isValid = false;
        } else {
            const screeningDate = new Date(screeningDateInput);
            screeningDate.setHours(0, 0, 0, 0);

            if (isNaN(screeningDate.getTime())) {
                $('#screening_date_error').text('Invalid date format').show();
                isValid = false;
            } else if (screeningDate < startDate || screeningDate > today) {
                $('#screening_date_error').text('Date must be between 2025-01-20 and today').show();
                isValid = false;
            }
        }

        // Validate consent date if needed
        const consentYes = $('input[name="consent"]:checked').val() === '1';
        if (consentYes) {
            const consentDateInput = $('#consent_date').val();
            const screeningDate = new Date($('#screening_date').val());
            screeningDate.setHours(0, 0, 0, 0);

            if (!consentDateInput) {
                $('#consent_date_error').text('Consent date is required').show();
                isValid = false;
            } else {
                const consentDate = new Date(consentDateInput);
                consentDate.setHours(0, 0, 0, 0);

                if (isNaN(consentDate.getTime())) {
                    $('#consent_date_error').text('Invalid date format').show();
                    isValid = false;
                } else if (consentDate < screeningDate) {
                    $('#consent_date_error').text('Cannot be before screening date').show();
                    isValid = false;
                } else if (consentDate > today) {
                    $('#consent_date_error').text('Cannot be in the future').show();
                    isValid = false;
                }
            }
        }

        // **Prevent form from resetting inputs**
        if (!isValid) {
            event.preventDefault(); // Stop form submission
            return;
        }

        // **Submit form naturally if valid**
        this.submit();
    });
});
