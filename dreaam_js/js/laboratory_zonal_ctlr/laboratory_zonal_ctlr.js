document.addEventListener("DOMContentLoaded", function () {
    const culturePerformedRadios = document.querySelectorAll('input[name="culture_performed"]');
    const cultureMethodSection = document.getElementById("culture_method_section");
    const microscopyTypeSection = document.getElementById("microscopy_type_section");
    const culturePerformedSection = document.getElementById("culture_performed_section");

    const ljResultsRadios = document.querySelectorAll('input[name="lj_results"]');
    const mgitResultsRadios = document.querySelectorAll('input[name="mgit_results"]');
    const cultureIsolateRadios = document.querySelectorAll('input[name="culture_isolate"]');
    const phenotypicPerformedRadios = document.querySelectorAll('input[name="phenotypic_performed"]');
    const xpertXdrPerformedRadios = document.querySelectorAll('input[name="xpert_xdr_performed"]');

    const firstLineLpaRadios = document.querySelectorAll('input[name="first_line_lpa"]');
    const secondLineLpaRadios = document.querySelectorAll('input[name="second_line_lpa"]');

    const cultureIsolateSection = document.getElementById("culture_isolate_section");
    const isolateDateSection = document.getElementById("isolate_date_section");
    const phenotypicDstSection = document.getElementById("phenotypic_dst_section");

    const phenotypicDatePerformedSection = document.getElementById("phenotypic_date_performed_section");
    const phenotypicDateResultsSection = document.getElementById("phenotypic_date_results_section");
    const phenotypicPerformedResultsSection = document.getElementById("phenotypic_performed_results_section");

    const xpertXdrDatePerformedSection = document.getElementById("xpert_xdr_date_performed_section");
    const xpertXdrResultsSection = document.getElementById("xpert_xdr_results_section");

    const firstLineSection = document.getElementById("first_line_section");
    const secondLineSection = document.getElementById("second_line_section");

    // Event listener for nanopore_done
    const nanoporeRadios = document.querySelectorAll('input[name="nanopore_done"]');
    const sequencingResultsSection = document.getElementById("sequencing_results_section");
    const nanoPoreResultsSection = document.getElementById("nano_pore_results");

    function toggleCultureSections() {
        const isCulturePerformed = document.querySelector('input[name="culture_performed"]:checked')?.value === "1";

        const displayStyle = isCulturePerformed ? "block" : "none";
        cultureMethodSection.style.display = displayStyle;
        microscopyTypeSection.style.display = displayStyle;
        culturePerformedSection.style.display = displayStyle;
    }

    function toggleCultureIsolateSection() {
        const ljResults = document.querySelector('input[name="lj_results"]:checked')?.value;
        const mgitResults = document.querySelector('input[name="mgit_results"]:checked')?.value;
        const showIsolate = ["1", "2", "3", "4"].includes(ljResults) || mgitResults === "1";

        cultureIsolateSection.style.display = showIsolate ? "block" : "none";
    }

    function toggleIsolateDetails() {
        const isCultureIsolate = document.querySelector('input[name="culture_isolate"]:checked')?.value === "1";

        const displayStyle = isCultureIsolate ? "block" : "none";
        isolateDateSection.style.display = displayStyle;
        phenotypicDstSection.style.display = displayStyle;
    }

    function togglePhenotypicSections() {
        const isPhenotypicPerformed = document.querySelector('input[name="phenotypic_performed"]:checked')?.value === "1";

        const displayStyle = isPhenotypicPerformed ? "block" : "none";
        phenotypicDatePerformedSection.style.display = displayStyle;
        phenotypicDateResultsSection.style.display = displayStyle;
        phenotypicPerformedResultsSection.style.display = displayStyle;
    }

    function toggleXpertXdrSections() {
        const isXpertXdrPerformed = document.querySelector('input[name="xpert_xdr_performed"]:checked')?.value === "1";

        const displayStyle = isXpertXdrPerformed ? "block" : "none";
        xpertXdrDatePerformedSection.style.display = displayStyle;
        xpertXdrResultsSection.style.display = displayStyle;
    }

    function toggleLpaSections() {
        const isFirstLineLpa = document.querySelector('input[name="first_line_lpa"]:checked')?.value === "1";
        const isSecondLineLpa = document.querySelector('input[name="second_line_lpa"]:checked')?.value === "1";

        firstLineSection.style.display = isFirstLineLpa ? "block" : "none";
        secondLineSection.style.display = isSecondLineLpa ? "block" : "none";
    }

    function toggleSequencingResults() {
        const isNanoporeDone = document.querySelector('input[name="nanopore_done"]:checked')?.value === "1";
        sequencingResultsSection.style.display = isNanoporeDone ? "block" : "none";
        nanoPoreResultsSection.style.display = isNanoporeDone ? "block" : "none"; // Added for nano_pore_results
    }

    // Initialize on page load
    toggleCultureSections();
    toggleCultureIsolateSection();
    toggleIsolateDetails();
    togglePhenotypicSections();
    toggleXpertXdrSections();
    toggleLpaSections();
    toggleSequencingResults(); // Added this line to handle nanopore_done logic

    // Add event listeners
    culturePerformedRadios.forEach(radio => {
        radio.addEventListener("change", toggleCultureSections);
    });

    ljResultsRadios.forEach(radio => {
        radio.addEventListener("change", toggleCultureIsolateSection);
    });

    mgitResultsRadios.forEach(radio => {
        radio.addEventListener("change", toggleCultureIsolateSection);
    });

    cultureIsolateRadios.forEach(radio => {
        radio.addEventListener("change", toggleIsolateDetails);
    });

    phenotypicPerformedRadios.forEach(radio => {
        radio.addEventListener("change", togglePhenotypicSections);
    });

    xpertXdrPerformedRadios.forEach(radio => {
        radio.addEventListener("change", toggleXpertXdrSections);
    });

    firstLineLpaRadios.forEach(radio => {
        radio.addEventListener("change", toggleLpaSections);
    });

    secondLineLpaRadios.forEach(radio => {
        radio.addEventListener("change", toggleLpaSections);
    });

    nanoporeRadios.forEach(radio => {
        radio.addEventListener("change", toggleSequencingResults);
    });
});
