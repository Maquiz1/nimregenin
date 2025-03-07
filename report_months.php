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
                <td colspan="14" align="center" style="font-size: 18px">
                    <b>' . $title . '</b>
                </td>
            </tr>
            <tr>
                <td colspan="14" align="center" style="font-size: 18px">
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
                <td colspan="14" style="font-size: 16px; font-weight: bold; background-color: #f2f2f2;">
                    Year: ' . $year . '
                </td>
            </tr>
            <tr>
                <th>Month</th>
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

        // Loop through each month
        for ($month = 1; $month <= 12; $month++) {
            foreach ($site_data as $row) {
                // Get counts for each column for the specific month and site
                $registered = $override->countDataByMonthYear('clients', 'clinic_date', $year, $month, 'status', 1, 'site_id', $row['id']);
                $screened = $override->countDataByMonthYear('clients', 'screening_date', $year, $month, 'status', 1, 'site_id', $row['id']);
                $breast_cancer = $override->countDataByMonthYear('screening', 'screening_date', $year, $month, 'breast_cancer', 1, 'site_id', $row['id']);
                $brain_cancer = $override->countDataByMonthYear('screening', 'screening_date', $year, $month, 'brain_cancer', 1, 'site_id', $row['id']);
                $cervical_cancer = $override->countDataByMonthYear('screening', 'screening_date', $year, $month, 'cervical_cancer', 1, 'site_id', $row['id']);
                $prostate_cancer = $override->countDataByMonthYear('screening', 'screening_date', $year, $month, 'prostate_cancer', 1, 'site_id', $row['id']);
                $eligible = $override->countDataByMonthYear('clients', 'clinic_date', $year, $month, 'eligible', 1, 'site_id', $row['id']);
                $enrolled = $override->countDataByMonthYear('clients', 'clinic_date', $year, $month, 'enrolled', 1, 'site_id', $row['id']);
                $end_study = $override->countDataByMonthYear('clients', 'clinic_date', $year, $month, 'end_study', 1, 'site_id', $row['id']);

                $output .= '
                    <tr>
                        <td>' . date('F', mktime(0, 0, 0, $month, 1)) . '</td>
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
            }
        }
    }

    $output .= '</table>';
}

$pdf->loadHtml($output);
$pdf->setPaper('A4', 'landscape');
$pdf->render();
$pdf->stream($file_name, array("Attachment" => false));
?>