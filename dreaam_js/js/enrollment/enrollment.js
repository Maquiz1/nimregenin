document.addEventListener("DOMContentLoaded", function () {
    const txPreviousRadios = document.querySelectorAll("input[name='tx_previous']");
    const tbCategoryRadios = document.querySelectorAll("input[name='tb_category']");
    const otherDiseasesRadios = document.querySelectorAll("input[name='other_diseases']");
    const sputumCollectedRadios = document.querySelectorAll("input[name='sputum_collected']");
    const diseasesMedicalCheckboxes = document.querySelectorAll("input[name='diseases_medical[]']"); // Assuming checkbox is an array
    const tbRegimenRadios = document.querySelectorAll("input[name='tb_regimen']"); // Assuming tb_regimen is a radio button group

    const tbCategorySection = document.getElementById("tb_category_section");
    const txNumberSection = document.getElementById("tx_number_section");
    const txPreviousSection = document.getElementById("tx_previous_section");
    const diseasesMedicalSection = document.getElementById("diseases_medical_section");
    const sputumDateSection = document.getElementById("sputum_date_section");
    const sputumReasonsSection = document.getElementById("sputum_reasons_section");
    const diseasesSpecify = document.getElementById("diseases_specify");
    const tbCategorySpecify = document.getElementById("tb_category_specify");
    const ltfMonthsSection = document.getElementById("ltf_months_section"); // Assuming the section ID
    const tbRegimenSpecify = document.getElementById("tb_regimen_specify"); // The section to toggle based on tb_regimen

    function toggleTxSections() {
        let isTxPreviousYes = Array.from(txPreviousRadios).some(radio => radio.checked && radio.value === "1");

        tbCategorySection.style.display = isTxPreviousYes ? "block" : "none";
        txNumberSection.style.display = isTxPreviousYes ? "block" : "none";
        txPreviousSection.style.display = isTxPreviousYes ? "block" : "none";
    }

    function toggleTbCategorySpecify() {
        let isTbCategorySpecify = Array.from(tbCategoryRadios).some(radio => radio.checked && radio.value === "96");

        tbCategorySpecify.style.display = isTbCategorySpecify ? "block" : "none";
    }

    function toggleTbCategoryLtf() {
        let isTbCategoryLtf = Array.from(tbCategoryRadios).some(radio => radio.checked && (radio.value === "2" || radio.value === "3"));

        ltfMonthsSection.style.display = isTbCategoryLtf ? "block" : "none";
    }

    function toggleDiseaseSection() {
        let isOtherDiseasesYes = Array.from(otherDiseasesRadios).some(radio => radio.checked && radio.value === "1");

        diseasesMedicalSection.style.display = isOtherDiseasesYes ? "block" : "none";
    }

    function toggleSputumSection() {
        let isSputumCollectedYes = Array.from(sputumCollectedRadios).some(radio => radio.checked && radio.value === "1");

        sputumDateSection.style.display = isSputumCollectedYes ? "block" : "none";
        sputumReasonsSection.style.display = isSputumCollectedYes ? "none" : "block";
    }

    function toggleDiseasesSpecify() {
        let isDisease96Checked = Array.from(diseasesMedicalCheckboxes).some(checkbox => checkbox.checked && checkbox.value === "96");

        diseasesSpecify.style.display = isDisease96Checked ? "block" : "none";
    }

    function toggleTbRegimenSpecify() {
        let isTbRegimen7 = Array.from(tbRegimenRadios).some(radio => radio.checked && radio.value === "7");

        tbRegimenSpecify.style.display = isTbRegimen7 ? "block" : "none";
    }

    // Attach event listeners
    txPreviousRadios.forEach(radio => radio.addEventListener("change", toggleTxSections));
    tbCategoryRadios.forEach(radio => {
        radio.addEventListener("change", () => {
            toggleTbCategorySpecify();
            toggleTbCategoryLtf();
        });
    });
    otherDiseasesRadios.forEach(radio => radio.addEventListener("change", toggleDiseaseSection));
    sputumCollectedRadios.forEach(radio => radio.addEventListener("change", toggleSputumSection));
    diseasesMedicalCheckboxes.forEach(checkbox => checkbox.addEventListener("change", toggleDiseasesSpecify));
    tbRegimenRadios.forEach(radio => radio.addEventListener("change", toggleTbRegimenSpecify));

    // Run on page load to initialize the correct visibility
    toggleTxSections();
    toggleTbCategorySpecify();
    toggleTbCategoryLtf();
    toggleDiseaseSection();
    toggleSputumSection();
    toggleDiseasesSpecify();
    toggleTbRegimenSpecify();
});
