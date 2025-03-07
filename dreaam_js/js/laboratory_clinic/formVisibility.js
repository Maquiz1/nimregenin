document.addEventListener("DOMContentLoaded", function () {
    function toggleFields() {
        const isSampleReceivedYes = isChecked("sample_received", "1");
        const isSampleReceivedNo = isChecked("sample_received", "2");
        const isNewSampleYes = isChecked("new_sample", "1");

        // Show or hide specific fields
        const shouldShowSampleFields = isSampleReceivedYes || (isSampleReceivedNo && isNewSampleYes);
        ["date_collected", "date_received", "appearance", "sample_volume", "afb_microscopy"]
            .forEach(id => toggleElementVisibility(id, shouldShowSampleFields));

        toggleElementVisibility("sample_reason", !isSampleReceivedYes);
        toggleElementVisibility("new_sample", !isSampleReceivedYes);

        // Handle "other_reason" field visibility
        const shouldShowOtherReason = isSampleReceivedNo && isChecked("sample_reason", "96");
        toggleElementVisibility("other_reason", shouldShowOtherReason);

        // Handle "new_reason" field visibility
        toggleElementVisibility("new_reason", isSampleReceivedNo && !isNewSampleYes);

        // Toggle Xpert MTB/RIF test sections
        const shouldShowXpert = shouldShowSampleFields;
        ["xpert_rif_test_section", "xpert_rif_results_section", "xpert_date_section", "xpert_mtb", "xpert_rif", "ct_value_section"]
            .forEach(id => toggleElementVisibility(id, shouldShowXpert));

        document.getElementById("remarks")?.removeAttribute("required");

        handleCtValueRequirement();
    }

    function handleCtValueRequirement() {
        const ctValueInput = document.getElementById("ct_value");
        const ctNotApplicableCheckbox = document.getElementById("ct_na");

        if (ctNotApplicableCheckbox.checked) {
            ctValueInput.value = "";
            ctValueInput.disabled = true;
            ctValueInput.required = false;
        } else {
            ctValueInput.disabled = false;
            ctValueInput.required = !ctValueInput.value;
        }
    }

    function handleCtValueInput() {
        document.getElementById("ct_na").checked = false;
        handleCtValueRequirement();
    }

    // Add event listeners
    addEventListeners(document.getElementsByName("sample_received"), "change", toggleFields);
    addEventListeners(document.getElementsByName("sample_reason"), "change", toggleFields);
    addEventListeners(document.getElementsByName("new_sample"), "change", toggleFields);
    document.getElementById("ct_na")?.addEventListener("change", handleCtValueRequirement);
    document.getElementById("ct_value")?.addEventListener("input", handleCtValueInput);

    toggleFields();
});
