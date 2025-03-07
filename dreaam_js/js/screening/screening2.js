// Function to toggle consent_date visibility
function toggleConsentDate() {
    const consentYes = document.querySelector('input[name="conset"]:checked');
    const consentDateContainer = document.getElementById('conset_date_container');
    if (consentYes && consentYes.value === '1') { // Assuming '1' is the value for 'Yes'
        consentDateContainer.style.display = 'block';
    } else {
        consentDateContainer.style.display = 'none';
    }
}

// Add event listeners to consent radio buttons
document.querySelectorAll('input[name="conset"]').forEach(function (radio) {
    radio.addEventListener('change', toggleConsentDate);
});

// Initial check to set consent_date visibility on page load
toggleConsentDate();

// Function to check if PID exists in the database
async function checkIfPidExists(pid) {
    alert('Server response0:', pid); // Debugging line
    try {
        alert('Server response1:', pid); // Debugging line
        const response = await fetch('check_pid.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ pid: pid }),
        });
        alert('Server response2:', pid); // Debugging line
        const data = await response.json();
        alert('Server response3:', data); // Debugging line
        alert('Server response4:', data.exists); // Debugging line
        return data.exists; // Assume the server returns { exists: true/false }
    } catch (error) {
        console.error('Error checking PID:', error);
        return false;
    }
}

// Form submission validation
document.getElementById('screening').addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent form submission until all validations are complete

    let isValid = true;

    // Clear previous error messages
    document.querySelectorAll('.text-danger').forEach(function (error) {
        error.style.display = 'none';
        error.textContent = '';
    });

    // Retrieve PID1 and PID2 values
    const pid1 = document.getElementById('pid1').value.trim();
    const pid2 = document.getElementById('pid2').value.trim();

    // Check if PID1 and PID2 match
    if (pid1 !== pid2) {
        document.getElementById('pid1_error').textContent = 'PID1 and PID2 do not match.';
        document.getElementById('pid1_error').style.display = 'block';
        document.getElementById('pid2_error').textContent = 'PID1 and PID2 do not match.';
        document.getElementById('pid2_error').style.display = 'block';
        isValid = false;
    }

    // Check if PID1 already exists in the database
    if (isValid && pid1) {
        const pidExists = await checkIfPidExists(pid1);
        if (pidExists) {
            document.getElementById('pid1_error').textContent = 'PID already exists in the database. Please use a different PID.';
            document.getElementById('pid1_error').style.display = 'block';
            isValid = false;
        }
    }

    // Validate screening_date
    const screeningDateInput = document.getElementById('screening_date');
    const screeningDate = new Date(screeningDateInput.value);
    screeningDate.setHours(0, 0, 0, 0); // Normalize time to midnight
    const startDate = new Date('2025-01-20'); // Start date
    startDate.setHours(0, 0, 0, 0); // Normalize time to midnight
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Normalize time to midnight

    if (screeningDate < startDate || screeningDate > today) {
        document.getElementById('screening_date_error').textContent = 'Screening date must be between 2025-01-20 and today.';
        document.getElementById('screening_date_error').style.display = 'block';
        isValid = false;
    }

    // Validate consent_date (if consent is "Yes")
    const consentYes = document.querySelector('input[name="conset"]:checked');
    if (consentYes && consentYes.value === '1') { // Assuming '1' is the value for 'Yes'
        const consentDateInput = document.getElementById('conset_date');
        const consentDate = new Date(consentDateInput.value);
        consentDate.setHours(0, 0, 0, 0); // Normalize time to midnight

        if (!consentDateInput.value) {
            // If consent date is empty
            document.getElementById('conset_date_error').textContent = 'Consent date is required if consent is selected as "Yes".';
            document.getElementById('conset_date_error').style.display = 'block';
            isValid = false;
        } else if (consentDate < screeningDate) {
            // If consent date is earlier than screening date
            document.getElementById('conset_date_error').textContent = 'Consent date cannot be earlier than the screening date.';
            document.getElementById('conset_date_error').style.display = 'block';
            isValid = false;
        } else if (consentDate > today) {
            // If consent date is in the future
            document.getElementById('conset_date_error').textContent = 'Consent date cannot be in the future.';
            document.getElementById('conset_date_error').style.display = 'block';
            isValid = false;
        } else {
            // If consent date is valid (between screening date and today)
            document.getElementById('conset_date_error').style.display = 'none';
        }
    }

    // Submit the form if all validations pass
    if (isValid) {
        console.log('Form is valid. Submitting...'); // Debugging line
        event.target.submit(); // Submit the form
    } else {
        console.log('Form is invalid. Please fix the errors.'); // Debugging line
    }
});