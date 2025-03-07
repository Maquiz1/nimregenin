<?php
require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Get year from request or use current year
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

$title = "NIMREGENIN SUMMARY REPORT - $year";
$pdf = new Pdf();
$file_name = $title . '.pdf';
$output = '';

if ($site_data) {
    $output .= '
        <table width="100%" border="1" cellpadding="5" cellspacing="0">
            <tr>
                <td colspan="18" align="center" style="font-size: 18px">
                    <b>' . $title . '</b>
                </td>
            </tr>
            <tr>
                <td colspan="18" align="center" style="font-size: 18px">
                    <b>Total Recruited (' . $Total . '): Total Enrolled (' . $data_enrolled . ')</b>
                </td>
            </tr>
            <tr>
                <th>No.</th>
                <th>Month</th>
                <th>Recruited</th>
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

    $monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    for ($month = 1; $month <= 12; $month++) {
        $registered = $override->getCountByMonthYear('clients', 'status', 1, $month, $year);
        $screened = $override->getCountByMonthYear('clients', 'status', 1, $month, $year, 'screened', 1);
        $breast_cancer = $override->getCountByMonthYear('screening', 'status', 1, $month, $year, 'breast_cancer', 1);
        $brain_cancer = $override->getCountByMonthYear('screening', 'status', 1, $month, $year, 'brain_cancer', 1);
        $cervical_cancer = $override->getCountByMonthYear('screening', 'status', 1, $month, $year, 'cervical_cancer', 1);
        $prostate_cancer = $override->getCountByMonthYear('screening', 'status', 1, $month, $year, 'prostate_cancer', 1);
        $eligible = $override->getCountByMonthYear('clients', 'status', 1, $month, $year, 'eligible', 1);
        $enrolled = $override->getCountByMonthYear('clients', 'status', 1, $month, $year, 'enrolled', 1);
        $end_study = $override->getCountByMonthYear('clients', 'status', 1, $month, $year, 'end_study', 1);

        $output .= "
            <tr>
                <td align='center'>$month</td>
                <td>{$monthNames[$month - 1]}</td>
                <td align='right'>$registered</td>
                <td align='right'>$screened</td>
                <td align='right'>$breast_cancer</td>
                <td align='right'>$brain_cancer</td>
                <td align='right'>$cervical_cancer</td>
                <td align='right'>$prostate_cancer</td>
                <td align='right'>$eligible</td>
                <td align='right'>$enrolled</td>
                <td align='right'>$end_study</td>
            </tr>
        ";
    }

    $output .= '</table>';
}

$pdf->loadHtml($output);
$pdf->render();
$pdf->stream($file_name, array("Attachment" => false));
