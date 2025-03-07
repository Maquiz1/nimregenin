document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#labForm_clinic");

    function validateDates() {
        const today = new Date();
        const dateCollected = new Date(document.getElementById("date_collected_input").value);
        const dateReceived = new Date(document.getElementById("date_received_input").value);
        const enrollmentDate = new Date(document.getElementById("enrollment_date_hidded").value);
        const afbADate = new Date(document.getElementById("afb_a_date").value);
        const afbBDate = new Date(document.getElementById("afb_date_b").value);
        const xpertDate = new Date(document.getElementById("xpert_date").value);
        const ctValue = parseFloat(document.getElementById("ct_value").value);

        let isValid = true;

        function setError(elementId, message) {
            const errorElement = document.getElementById(elementId + "_error");
            if (errorElement) {
                errorElement.textContent = message;
            }
            isValid = false;
        }

        if (dateCollected < enrollmentDate) setError("date_collected", "Date collected cannot be before enrollment date.");
        if (dateCollected > today) setError("date_collected", "Date collected cannot be a future date.");
        if (dateReceived > today) setError("date_received", "Date received cannot be a future date.");
        if (dateCollected && dateReceived && dateCollected > dateReceived) setError("date_collected", "Date collected cannot be after date received.");
        if (afbADate && afbADate < dateReceived) setError("afb_a_date", "AFB A date cannot be before date received.");
        if (afbBDate && afbBDate < dateReceived) setError("afb_date_b", "AFB B date cannot be before date received.");
        if (afbADate > today) setError("afb_a_date", "AFB A date cannot be a future date.");
        if (afbBDate > today) setError("afb_date_b", "AFB B date cannot be a future date.");
        if (xpertDate && xpertDate < dateReceived) setError("xpert_date", "Xpert date cannot be before date received.");
        if (xpertDate > today) setError("xpert_date", "Xpert date cannot be a future date.");
        if (ctValue && ctValue > 5) setError("ct_value", "Ct value cannot be greater than 5.");

        return isValid;
    }

    function preventInvalidSubmission(event) {
        if (!validateDates()) event.preventDefault();
    }

    // Attach event listeners
    form.addEventListener("submit", preventInvalidSubmission);

    ["date_collected_input", "date_received_input", "afb_a_date", "afb_date_b", "xpert_date", "ct_value"]
        .forEach(id => document.getElementById(id)?.addEventListener("change", validateDates));
});
