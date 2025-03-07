<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $site_data = $override->getData('site');
        $Total = $override->getCount('clients', 'status', 1);
        $data_enrolled = $override->getCount1('clients', 'status', 1, 'enrolled', 1);
        $successMessage = 'Report Successfully Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

$title = 'NIMREGENIN SUMMARY REPORT_' . date('Y-m-d');
$pdf = new Pdf();
$file_name = $title . '.pdf';
$output = '';

if ($site_data) {
    $output .= '
        <table width="100%" border="1" cellpadding="5" cellspacing="0">
            <tr>
                <td colspan="11" align="center" style="font-size: 18px">
                    <b>' . $title . '</b>
                </td>
            </tr>
            <tr>
                <td colspan="11" align="center" style="font-size: 18px">
                    <b>Total Registered (' . $Total . '): Total Enrolled (' . $data_enrolled . ')</b>
                </td>
            </tr>
    ';

    // Get unique years based on clinic_date or screening_date
    $years = $override->getUniqueYears('clients', 'clinic_date', 'screening', 'screening_date');

    // Loop through each year
    foreach ($years as $year) {
        $output .= '
            <tr>
                <td colspan="11" style="font-size: 16px; font-weight: bold; background-color: #f2f2f2;">
                    Year: ' . $year . '
                </td>
            </tr>
            <tr>
                <th>No.</th>
                <th>Site</th>
                <th>Registered</th>
                <th>Screened</th>
                <th>Breast Cancer</th>
                <th>Brain Cancer</th>
                <th>Cervical Cancer</th>
                <th>Prostate Cancer</th>
                <th>Eligible</th>
                <th>Enrolled</th>
                <th>End Study</th>
            </tr>
        ';

        $x = 1;
        // Track missing year values or empty date fields for each site
        $missing_years_sites = [];
        $missing_date_sites = [];

        foreach ($site_data as $row) {
            // Get date values
            $clinic_date = $row['clinic_date'];  // assuming clinic_date is stored as date string
            $screening_date = $row['screening_date'];  // assuming screening_date is stored as date string

            // Extract year from date if it exists
            $clinic_year = $clinic_date ? date('Y', strtotime($clinic_date)) : null;
            $screening_year = $screening_date ? date('Y', strtotime($screening_date)) : null;

            // Check if year is missing in the date columns
            if ($clinic_year !== $year && $screening_year !== $year) {
                $missing_years_sites[] = $row['name'];  // Add the site to missing years list
            }

            // Check for records missing the entire date (NULL or empty)
            if (empty($clinic_date) || empty($screening_date)) {
                $missing_date_sites[] = $row['name'];  // Add the site to missing date list
            }

            // Get counts for each column (similar to previous code)
            $registered = $override->countDataByYear('clients', 'clinic_date', $year, 'status', 1, 'site_id', $row['id']);
            $screened = $override->countDataByYear('clients', 'screening_date', $year, 'status', 1, 'site_id', $row['id']);
            $breast_cancer = $override->countDataByYear('screening', 'screening_date', $year, 'breast_cancer', 1, 'site_id', $row['id']);
            $brain_cancer = $override->countDataByYear('screening', 'screening_date', $year, 'brain_cancer', 1, 'site_id', $row['id']);
            $cervical_cancer = $override->countDataByYear('screening', 'screening_date', $year, 'cervical_cancer', 1, 'site_id', $row['id']);
            $prostate_cancer = $override->countDataByYear('screening', 'screening_date', $year, 'prostate_cancer', 1, 'site_id', $row['id']);
            $eligible = $override->countDataByYear('clients', 'clinic_date', $year, 'eligible', 1, 'site_id', $row['id']);
            $enrolled = $override->countDataByYear('clients', 'clinic_date', $year, 'enrolled', 1, 'site_id', $row['id']);
            $end_study = $override->countDataByYear('clients', 'clinic_date', $year, 'end_study', 1, 'site_id', $row['id']);

            $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['name'] . '</td>
                    <td align="right">' . $registered . '</td>
                    <td align="right">' . $screened . '</td>
                    <td align="right">' . $breast_cancer . '</td>
                    <td align="right">' . $brain_cancer . '</td>
                    <td align="right">' . $cervical_cancer . '</td>
                    <td align="right">' . $prostate_cancer . '</td>
                    <td align="right">' . $eligible . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $end_study . '</td>
                </tr>
            ';

            $x++;
        }

        // Print missing year values for the sites
        if (count($missing_years_sites) > 0) {
            $output .= '
                <tr>
                    <td colspan="11" style="font-size: 14px; color: red; font-weight: bold;">
                        <b>Missing Year Data for Year ' . $year . ':</b> ' . implode(', ', $missing_years_sites) . '
                    </td>
                </tr>
            ';
        }

        // Print missing date fields for the sites
        if (count($missing_date_sites) > 0) {
            $output .= '
                <tr>
                    <td colspan="11" style="font-size: 14px; color: red; font-weight: bold;">
                        <b>Missing Date Data for Year ' . $year . ':</b> ' . implode(', ', $missing_date_sites) . '
                    </td>
                </tr>
            ';
        }
    }

    $output .= '</table>';
}

$pdf->loadHtml($output);
$pdf->setPaper('A4', 'landscape');
$pdf->render();
$pdf->stream($file_name, array("Attachment" => false));
?>