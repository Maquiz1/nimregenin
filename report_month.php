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

$title = 'NIMREGENIN YEARLY REPORT_' . date('Y-m-d');
$pdf = new Pdf();
$file_name = $title . '.pdf';

$output = '';

if ($site_data) {
    $output .= '
        <table width="100%" border="1" cellpadding="5" cellspacing="0">
            <tr>
                <td colspan="3" align="center" style="font-size: 18px">
                    <b>' . $title . '</b>
                </td>
            </tr>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Count</th>
            </tr>
    ';

    // Fetch data grouped by year and month
    $years = $override->getDistinctYears('clients', 'clinic_date'); // Assuming 'clinic_date' is the date column
    foreach ($years as $year) {
        $months = $override->getMonthsByYear('clients', 'clinic_date', $year['year']);
        foreach ($months as $month) {
            $count = $override->countRowsByMonth('clients', 'clinic_date', $year['year'], $month['month']);
            $output .= '
                <tr>
                    <td>' . $year['year'] . '</td>
                    <td>' . date("F", mktime(0, 0, 0, $month['month'], 10)) . '</td>
                    <td>' . $count . '</td>
                </tr>
            ';
        }
    }

    $output .= '</table>';
}

$pdf->loadHtml($output);
$pdf->setPaper('A4', 'portrait');
$pdf->render();
$pdf->stream($file_name, array("Attachment" => false));
