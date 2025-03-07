document.addEventListener('DOMContentLoaded', function () {
    const culturePerformedRadios = document.getElementsByName('culture_performed'); // Radio buttons for culture_performed
    const phenotypicPerformedRadios = document.getElementsByName('phenotypic_performed'); // Radio buttons for phenotypic_performed
    const cultureIsolateRadios = document.getElementsByName('culture_isolate'); // Radio buttons for culture_isolate
    const cultureResultsRadios = document.getElementsByName('culture_results'); // Radio buttons for culture_results
    const xpertXdrPerformedRadios = document.getElementsByName('xpert_xdr_performed'); // Radio buttons for xpert_xdr_performed
    const firstLineLpaRadios = document.getElementsByName('first_line_lpa'); // Radio buttons for first_line_lpa
    const secondLineLpaRadios = document.getElementsByName('second_line_lpa'); // Radio buttons for second_line_lpa
    const nanoporeDoneRadios = document.getElementsByName('nanopore_done'); // Radio buttons for nanopore_done
    const epi2meRadios = document.getElementsByName('EPI2ME'); // Radio buttons for EPI2ME

    const cultureMethodSection = document.getElementById('culture_method_section');
    const microscopyTypeSection = document.getElementById('microscopy_type_section');
    const culturePerformedSection = document.getElementById('culture_performed_section');
    const cultureIsolateSection = document.getElementById('culture_isolate_section');

    const phenotypicDatePerformedSection = document.getElementById('phenotypic_date_performed_section');
    const phenotypicDateResultsSection = document.getElementById('phenotypic_date_results_section');
    const phenotypicPerformedResultsSection = document.getElementById('phenotypic_performed_results_section');
    const phenotypicDstSection = document.getElementById('phenotypic_dst_section');

    const xpertXdrDatePerformedSection = document.getElementById('xpert_xdr_date_performed_section');
    const xpertXdrResultsSection = document.getElementById('xpert_xdr_results_section');

    const firstLineSection = document.getElementById('first_line_section');
    const secondLineSection = document.getElementById('second_line_section');

    const sequencingResultsSection = document.getElementById('sequencing_results_section'); // Sequencing results section
    const epi2meVersionSection = document.getElementById('EPI2ME_version_section'); // EPI2ME Version section
    const epi2meSection = document.getElementById('EPI2ME_section'); // EPI2ME section
    const isolateDateSection = document.getElementById('isolate_date_section'); // Isolate Date section

    function toggleCultureSections() {
        const culturePerformedValue = Array.from(culturePerformedRadios).find(radio => radio.checked)?.value;

        if (culturePerformedValue === '1') {
            cultureMethodSection.style.display = 'block';
            microscopyTypeSection.style.display = 'block';
            culturePerformedSection.style.display = 'block';
        } else {
            cultureMethodSection.style.display = 'none';
            microscopyTypeSection.style.display = 'none';
            culturePerformedSection.style.display = 'none';
        }
    }

    function togglePhenotypicSections() {
        const phenotypicPerformedValue = Array.from(phenotypicPerformedRadios).find(radio => radio.checked)?.value;

        if (phenotypicPerformedValue === '1') {
            phenotypicDatePerformedSection.style.display = 'block';
            phenotypicDateResultsSection.style.display = 'block';
            phenotypicPerformedResultsSection.style.display = 'block'; // Show when phenotypic_performed = 1
        } else {
            phenotypicDatePerformedSection.style.display = 'none';
            phenotypicDateResultsSection.style.display = 'none';
            phenotypicPerformedResultsSection.style.display = 'none'; // Hide when phenotypic_performed is not 1
        }
    }

    function toggleCultureIsolateSection() {
        const cultureResultsValue = Array.from(cultureResultsRadios).find(radio => radio.checked)?.value;
        const cultureIsolateValue = Array.from(cultureIsolateRadios).find(radio => radio.checked)?.value;

        // Handle culture_isolate section visibility based on culture_results
        if (cultureResultsValue === '1') {
            cultureIsolateSection.style.display = 'block';
        } else {
            cultureIsolateSection.style.display = 'none';
        }

        // Handle phenotypic sections based on culture_isolate value
        if (cultureIsolateValue === '1') {
            phenotypicDstSection.style.display = 'block';
            phenotypicPerformedResultsSection.style.display = 'block';
            isolateDateSection.style.display = 'block'; // Show isolate date section if culture_isolate = 1
        } else {
            phenotypicDstSection.style.display = 'none';
            phenotypicPerformedResultsSection.style.display = 'none';
            isolateDateSection.style.display = 'none'; // Hide isolate date section if culture_isolate = 0
        }
    }

    function toggleXpertXdrSections() {
        const xpertXdrPerformedValue = Array.from(xpertXdrPerformedRadios).find(radio => radio.checked)?.value;

        if (xpertXdrPerformedValue === '1') {
            xpertXdrDatePerformedSection.style.display = 'block';
            xpertXdrResultsSection.style.display = 'block';
        } else {
            xpertXdrDatePerformedSection.style.display = 'none';
            xpertXdrResultsSection.style.display = 'none';
        }
    }

    function toggleFirstLineLpaSection() {
        const firstLineLpaValue = Array.from(firstLineLpaRadios).find(radio => radio.checked)?.value;

        if (firstLineLpaValue === '1') {
            firstLineSection.style.display = 'block';
        } else {
            firstLineSection.style.display = 'none';
        }
    }

    function toggleSecondLineLpaSection() {
        const secondLineLpaValue = Array.from(secondLineLpaRadios).find(radio => radio.checked)?.value;

        if (secondLineLpaValue === '1') {
            secondLineSection.style.display = 'block';
        } else {
            secondLineSection.style.display = 'none';
        }
    }

    function toggleSequencingResultsSection() {
        const nanoporeDoneValue = Array.from(nanoporeDoneRadios).find(radio => radio.checked)?.value;

        if (nanoporeDoneValue === '1') {
            sequencingResultsSection.style.display = 'block';
            epi2meSection.style.display = 'block'; // Show EPI2ME_section if nanopore_done = 1
        } else {
            sequencingResultsSection.style.display = 'none';
            epi2meSection.style.display = 'none'; // Hide EPI2ME_section if nanopore_done = 0
        }
    }

    function toggleEPI2MESections() {
        const epi2meValue = Array.from(epi2meRadios).find(radio => radio.checked)?.value;

        if (epi2meValue === '1') {
            epi2meVersionSection.style.display = 'block'; // Show EPI2ME_version_section if EPI2ME = 1
        } else {
            epi2meVersionSection.style.display = 'none'; // Hide EPI2ME_version_section if EPI2ME != 1
        }
    }

    // Run toggle functions on page load
    toggleCultureSections();
    togglePhenotypicSections();
    toggleCultureIsolateSection();
    toggleXpertXdrSections();
    toggleFirstLineLpaSection();
    toggleSecondLineLpaSection();
    toggleSequencingResultsSection();
    toggleEPI2MESections(); // Call EPI2ME toggle function on page load

    // Add event listeners to update visibility when any radio button changes
    culturePerformedRadios.forEach(radio => {
        radio.addEventListener('change', toggleCultureSections);
    });

    phenotypicPerformedRadios.forEach(radio => {
        radio.addEventListener('change', togglePhenotypicSections);
    });

    cultureIsolateRadios.forEach(radio => {
        radio.addEventListener('change', toggleCultureIsolateSection);
    });

    xpertXdrPerformedRadios.forEach(radio => {
        radio.addEventListener('change', toggleXpertXdrSections);
    });

    firstLineLpaRadios.forEach(radio => {
        radio.addEventListener('change', toggleFirstLineLpaSection);
    });

    secondLineLpaRadios.forEach(radio => {
        radio.addEventListener('change', toggleSecondLineLpaSection);
    });

    nanoporeDoneRadios.forEach(radio => {
        radio.addEventListener('change', toggleSequencingResultsSection); // Add event listener for nanopore_done
    });

    epi2meRadios.forEach(radio => {
        radio.addEventListener('change', toggleEPI2MESections); // Add event listener for EPI2ME
    });
});
