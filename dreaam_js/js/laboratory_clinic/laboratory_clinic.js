document.addEventListener("DOMContentLoaded", function () {
    const sampleReceivedRadios = document.querySelectorAll("input[name='sample_received']");
    const newSampleRadios = document.querySelectorAll("input[name='new_sample']");
    const sampleReasonRadios = document.querySelectorAll("input[name='sample_reason']");
    const numberReceivedRadios = document.querySelectorAll("input[name='number_received']");
    const afbMicroscopyRadios = document.querySelectorAll("input[name='afb_microscopy_conducted']");
    const xpertMtbRifRadios = document.querySelectorAll("input[name='xpert_mtb_rif_conducted']");
    const xpertMtbRadios = document.querySelectorAll("input[name='xpert_mtb']");
    const xpertRifOptions = document.querySelectorAll("#xpert_rif option");

    // Sections
    const sampleReceivedSection = document.getElementById("sample_received_section");
    const numberReceivedSection = document.getElementById("number_received_section");
    const sampleReasonSection = document.getElementById("sample_reason_section");
    const newSampleSection = document.getElementById("new_sample_section");
    const otherNewReasonSection = document.getElementById("other_new_reason_section");
    const otherReasonSection = document.getElementById("other_reason");
    const sample1Section = document.getElementById("sample1_section");
    const sample2Section = document.getElementById("sample2_section");
    const afbMicroscopySection = document.getElementById("afb_microscopy_section");
    const xpertMtbRifSection = document.getElementById("xpert_mtb_rif_section");
    const errorCodeSection = document.getElementById("error_code");
    const xpertRifSection = document.getElementById("xpert_rif_section");

    function toggleSampleSections() {
        let sampleReceivedValue = getCheckedValue(sampleReceivedRadios);

        if (sampleReceivedValue === "2") {
            sampleReasonSection.style.display = "block";
            newSampleSection.style.display = "block";
        } else {
            sampleReasonSection.style.display = "none";
            newSampleSection.style.display = "none";
            otherNewReasonSection.style.display = "none";
        }

        toggleOtherNewReasonSection();
    }

    function toggleOtherNewReasonSection() {
        let newSampleValue = getCheckedValue(newSampleRadios);

        otherNewReasonSection.style.display = newSampleValue === "2" ? "block" : "none";

        toggleNumberReceivedSection();
        toggleSampleReceivedSection();
        toggleSampleNumberSections();
    }

    function toggleNumberReceivedSection() {
        let sampleReceivedValue = getCheckedValue(sampleReceivedRadios);
        let newSampleValue = getCheckedValue(newSampleRadios);

        numberReceivedSection.style.display =
            sampleReceivedValue === "1" || (sampleReceivedValue === "2" && newSampleValue === "1")
                ? "block"
                : "none";

        toggleSampleNumberSections();
    }

    function toggleSampleReceivedSection() {
        let sampleReceivedValue = getCheckedValue(sampleReceivedRadios);
        let newSampleValue = getCheckedValue(newSampleRadios);

        sampleReceivedSection.style.display =
            sampleReceivedValue === "1" || (sampleReceivedValue === "2" && newSampleValue === "1")
                ? "block"
                : "none";
    }

    function toggleSampleNumberSections() {
        let numberReceivedValue = getCheckedValue(numberReceivedRadios);

        sample1Section.style.display = ["1", "2"].includes(numberReceivedValue) ? "block" : "none";
        sample2Section.style.display = numberReceivedValue === "2" ? "block" : "none";
    }

    function toggleOtherReasonSection() {
        let isSampleReason96 = Array.from(sampleReasonRadios).some(radio => radio.checked && radio.value === "96");

        otherReasonSection.style.display = isSampleReason96 ? "block" : "none";
    }

    function toggleAfbMicroscopySection() {
        let afbMicroscopyValue = getCheckedValue(afbMicroscopyRadios);

        afbMicroscopySection.style.display = afbMicroscopyValue === "1" ? "block" : "none";
    }

    function toggleXpertMtbRifSection() {
        let xpertMtbRifValue = getCheckedValue(xpertMtbRifRadios);

        xpertMtbRifSection.style.display = xpertMtbRifValue === "1" ? "block" : "none";
    }

    function toggleErrorCodeSection() {
        let xpertMtbValue = getCheckedValue(xpertMtbRadios);

        errorCodeSection.style.display = xpertMtbValue === "8" ? "block" : "none";
        xpertRifSection.style.display = ["2", "3", "4", "5", "6"].includes(xpertMtbValue) ? "block" : "none";

        filterXpertRifOptions(xpertMtbValue);
    }

    function filterXpertRifOptions() {
        const xpertMtbValue = getCheckedValue(xpertMtbRadios);
        console.log("xpertMtbValue:", xpertMtbValue); // Log the selected value

        xpertMtbRifRadios.forEach(radio => radio.closest('label').style.display = "block"); // Show all radios

        // If selected value is in the range of "3", "4", "5", "6", hide radio "4"
        if (["3", "4", "5", "6"].includes(xpertMtbValue)) {
            hideOption("4");
        }

        // If selected value is "2", hide radios "1", "2", and "3"
        if (xpertMtbValue === "2") {
            ["1", "2", "3"].forEach(hideOption);
        }
    }

    function hideOption(value) {
        const radio = Array.from(xpertMtbRifRadios).find(radio => radio.value === value);
        if (radio) {
            radio.closest('label').style.display = "none"; // Hide the label (which includes the radio button)
        }
    }


    function getCheckedValue(radioNodeList) {
        return Array.from(radioNodeList).find(radio => radio.checked)?.value;
    }

    sampleReceivedRadios.forEach(radio => radio.addEventListener("change", toggleSampleSections));
    newSampleRadios.forEach(radio => radio.addEventListener("change", toggleOtherNewReasonSection));
    numberReceivedRadios.forEach(radio => radio.addEventListener("change", toggleSampleNumberSections));
    sampleReasonRadios.forEach(radio => radio.addEventListener("change", toggleOtherReasonSection));
    afbMicroscopyRadios.forEach(radio => radio.addEventListener("change", toggleAfbMicroscopySection));
    xpertMtbRifRadios.forEach(radio => radio.addEventListener("change", toggleXpertMtbRifSection));
    xpertMtbRadios.forEach(radio => radio.addEventListener("change", toggleErrorCodeSection));

    toggleSampleSections();
    toggleOtherNewReasonSection();
    toggleNumberReceivedSection();
    toggleSampleReceivedSection();
    toggleSampleNumberSections();
    toggleOtherReasonSection();
    toggleAfbMicroscopySection();
    toggleXpertMtbRifSection();
    toggleErrorCodeSection();
});
