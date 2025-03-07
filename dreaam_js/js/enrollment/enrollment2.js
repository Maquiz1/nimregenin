// Function to fetch screening_date and consent_date from the database
function fetchDates() {
    const urlParams = new URLSearchParams(window.location.search);
    clientId = urlParams.get('sid'); // "sid" should match the URL parameter name

    fetch(`fetch_dates.php?client_id=${clientId}`) // Replace with your PHP endpoint
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.screeningDate = new Date(data.screening_date);
                window.consentDate = new Date(data.consent_date);
            } else {
                console.error('Failed to fetch dates:', data.message);
            }
        })
        .catch(error => console.error('Error fetching dates:', error));
}

// Fetch dates when the form is loaded
fetchDates();

// Function to calculate age from date of birth
function updateAge() {
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');
    const dobError = document.getElementById('dob_error');

    if (dobInput.value) {
        const dob = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        if (age < 18) {
            dobError.textContent = "Age cannot be less than 18 years.";
            ageInput.value = '';
        } else {
            dobError.textContent = '';
            ageInput.value = age;
        }
    }
}

// Function to calculate date of birth from age
function updateDob() {
    const ageInput = document.getElementById('age');
    const dobInput = document.getElementById('dob');
    const ageError = document.getElementById('age_error');

    if (ageInput.value) {
        const age = parseInt(ageInput.value);
        if (age < 18) {
            ageError.textContent = "Age cannot be less than 18 years.";
            dobInput.value = '';
        } else {
            ageError.textContent = '';
            const today = new Date();
            const birthYear = today.getFullYear() - age;
            dobInput.value = `${birthYear}-01-01`; // Default to January 1st of the calculated year
        }
    }
}

// Function to validate enrollment date
function validateEnrollmentDate() {
    const enrollmentDateInput = document.getElementById('enrollment_date');
    const enrollmentDateError = document.getElementById('enrollment_date_error');

    if (enrollmentDateInput.value && window.screeningDate && window.consentDate) {
        const enrollmentDate = new Date(enrollmentDateInput.value);

        if (enrollmentDate < window.consentDate) {
            enrollmentDateError.textContent = "Enrollment date cannot be less than consent date.";
            return false;
        } else if (enrollmentDate > new Date()) {
            enrollmentDateError.textContent = "Enrollment date cannot be more than today's date.";
            return false;
        } else {
            enrollmentDateError.textContent = '';
            return true;
        }
    }
    return false;
}

// Function to validate date_information_collected
function validateInformationDate() {
    const informationDateInput = document.getElementById('date_information_collected');
    const enrollmentDateInput = document.getElementById('enrollment_date');
    const informationDateError = document.getElementById('information_date_error');
    const today = new Date(); // Get current date

    if (informationDateInput.value && enrollmentDateInput.value) {
        const informationDate = new Date(informationDateInput.value);
        const enrollmentDate = new Date(enrollmentDateInput.value);

        // Reset error message
        informationDateError.textContent = '';

        // Check if information date is in the future
        if (informationDate > today) {
            informationDateError.textContent = "Information collected date cannot be in the future.";
            return false;
        }
        // Check if information date is before enrollment date
        else if (informationDate < enrollmentDate) {
            informationDateError.textContent = "Information collected date cannot be earlier than enrollment date.";
            return false;
        }
        // Valid case
        else {
            return true;
        }
    }
    return false;
}


document.addEventListener("DOMContentLoaded", function () {
    const yearInput = document.getElementById("tx_year");
    const monthInput = document.getElementById("tx_month");
    const unknownYearCheckbox = document.getElementById("tx_unknown_year");
    const unknownMonthCheckbox = document.getElementById("tx_unknown_month");

    function toggleInputs() {
        if (unknownYearCheckbox.checked) {
            // Disable and clear both inputs when Year is unknown
            yearInput.disabled = true;
            yearInput.value = '';
            monthInput.disabled = true;
            monthInput.value = '';
            unknownMonthCheckbox.checked = false;
            unknownMonthCheckbox.disabled = true;
        } else {
            // Enable Year input and allow toggling Month
            yearInput.disabled = false;
            unknownMonthCheckbox.disabled = false;

            if (unknownMonthCheckbox.checked) {
                monthInput.disabled = true;
                monthInput.value = '';
            } else {
                monthInput.disabled = false;
            }
        }
    }

    // Event Listeners
    unknownYearCheckbox.addEventListener("change", function () {
        toggleInputs();
        // Clear year input when checkbox is unchecked
        if (!this.checked) {
            yearInput.value = '';
        }
    });

    unknownMonthCheckbox.addEventListener("change", function () {
        monthInput.disabled = this.checked;
        // Clear month input when checkbox is unchecked
        if (!this.checked) {
            monthInput.value = '';
        }
    });

    // Initialize on page load
    toggleInputs();
});


// Function to toggle visibility and required attributes based on tx_previous selection
document.addEventListener("DOMContentLoaded", function () {
    const txPreviousInputs = document.querySelectorAll("input[name='tx_previous']");
    const conditionalFields = [
        "tx_number1", "dr_ds", "tb_category", "ltf_months_section",
        "tb_regimen", "regimen_months", "tb_otcome1_1_1_1"
    ];
    const requiredInputs = document.querySelectorAll(
        "#tx_month, #tx_year, input[name='dr_ds'], input[name='tb_category'], " +
        "#ltf_months, input[name='tb_regimen'], #regimen_months, #tb_otcome"
    );

    function toggleFields() {
        const isTxPreviousYes = document.querySelector("input[name='tx_previous']:checked")?.value === "1";

        conditionalFields.forEach(id => {
            const field = document.getElementById(id);
            if (field) {
                field.style.display = isTxPreviousYes ? "block" : "none";
            }
        });

        requiredInputs.forEach(input => {
            input.required = isTxPreviousYes;
        });
    }

    txPreviousInputs.forEach(input => {
        input.addEventListener("change", toggleFields);
    });

    toggleFields(); // Initialize on page load
});


// Function to toggle visibility of fields based on other_diseases selection
document.addEventListener("DOMContentLoaded", function () {
    const otherDiseasesInputs = document.querySelectorAll("input[name='other_diseases']");
    const diseasesMedicalSection = document.getElementById("diseases_medical_section");
    const diseasesMedicalInputs = document.querySelectorAll("input[name='diseases_medical']");
    const diseasesSpecifyInput = document.getElementById("diseases_specify");

    function toggleFields() {
        const selectedValue = document.querySelector("input[name='other_diseases']:checked")?.value;
        const isOtherChecked = [...diseasesMedicalInputs].some(input => input.checked && input.value === "Other");

        // Show diseases_medical_section if "Yes" is selected, hide otherwise
        if (selectedValue === "Yes") {
            diseasesMedicalSection.style.display = "block";
        } else {
            diseasesMedicalSection.style.display = "none";
            diseasesSpecifyInput.style.display = "none";
            diseasesSpecifyInput.required = false;
            diseasesSpecifyInput.value = ""; // Clear input when hidden
        }

        // Show/hide diseases_specify based on "Other" checkbox selection
        if (isOtherChecked) {
            diseasesSpecifyInput.style.display = "block";
            diseasesSpecifyInput.required = true;
        } else {
            diseasesSpecifyInput.style.display = "none";
            diseasesSpecifyInput.required = false;
            diseasesSpecifyInput.value = ""; // Clear input when hidden
        }
    }

    otherDiseasesInputs.forEach(input => {
        input.addEventListener("change", toggleFields);
    });

    diseasesMedicalInputs.forEach(input => {
        input.addEventListener("change", toggleFields);
    });

    toggleFields(); // Initialize on page load
});


document.addEventListener("DOMContentLoaded", function () {
    const tbCategoryInputs = document.querySelectorAll("input[name='tb_category']");
    const ltfMonthsSection = document.getElementById("ltf_months_section");
    const tbCategorySpecifyInput = document.getElementById("tb_category_specify");

    function toggleTbCategoryFields() {
        const selectedValue = document.querySelector("input[name='tb_category']:checked")?.value;

        // Show ltf_months_section if tb_category is 3
        if (selectedValue === "3") {
            ltfMonthsSection.style.display = "block";
        } else {
            ltfMonthsSection.style.display = "none";
        }

        // Show tb_category_specify if tb_category is 96 and make it required
        if (selectedValue === "96") {
            tbCategorySpecifyInput.style.display = "block";
            tbCategorySpecifyInput.required = true;
        } else {
            tbCategorySpecifyInput.style.display = "none";
            tbCategorySpecifyInput.required = false;
            tbCategorySpecifyInput.value = ""; // Clear input when hidden
        }
    }

    tbCategoryInputs.forEach(input => {
        input.addEventListener("change", toggleTbCategoryFields);
    });

    toggleTbCategoryFields(); // Initialize on page load
});


document.addEventListener("DOMContentLoaded", function () {
    const tbRegimenInputs = document.querySelectorAll("input[name='tb_regimen']");
    const tbRegimenSpecifyInput = document.getElementById("tb_regimen_1_specify");

    function toggleTbRegimenSpecify() {
        const selectedValue = document.querySelector("input[name='tb_regimen']:checked")?.value;

        if (selectedValue === "7") {
            tbRegimenSpecifyInput.style.display = "block";
            tbRegimenSpecifyInput.required = true;
        } else {
            tbRegimenSpecifyInput.style.display = "none";
            tbRegimenSpecifyInput.required = false;
            tbRegimenSpecifyInput.value = ""; // Clear input when hidden
        }
    }

    tbRegimenInputs.forEach(input => {
        input.addEventListener("change", toggleTbRegimenSpecify);
    });

    toggleTbRegimenSpecify(); // Initialize on page load
});


// Form submission validation
document.getElementById('enrollment').addEventListener('submit', function (event) {
    if (!validateEnrollmentDate()) {
        event.preventDefault();
    }

    if (!validateInformationDate()) {
        event.preventDefault();
    }

    const ageInput = document.getElementById('age');
    if (ageInput.value && ageInput.value < 18) {
        document.getElementById('age_error').textContent = "Age cannot be less than 18 years.";
        event.preventDefault();
    }
});