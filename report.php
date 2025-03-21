<?php

require_once 'pdf.php';

$user = new User();
$override = new OverideData();

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
                <td colspan="18">
                    <br />
                    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                        <tr>
                            <th rowspan="2">No.</th>
                            <th rowspan="2">SITE</th>
                            <th rowspan="2">Recruited</th>
                            <th rowspan="2">SCREENED</th>
                            <th rowspan="2">ELIGIBLE</th>
                            <th rowspan="2">ENROLLED</th>
                            <th rowspan="2">END</th>
                            <th colspan="4">CANCER (INCLUSION)</th>
                        </tr>
                        <tr>
                            <th>Breast</th>
                            <th>Brain</th>
                            <th>Cervical</th>
                            <th>Prostate</th>
                        </tr>
    ';

    $x = 1;
    foreach ($site_data as $row) {
        $registered = $override->countData('clients', 'status', 1, 'site_id', $row['id']);
        $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $row['id']);
        $eligible = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $row['id']);
        $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $row['id']);
        $end_study = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $row['id']);
        $breast_cancer = $override->countData2('screening', 'status', 1, 'breast_cancer', 1, 'site_id', $row['id']);
        $brain_cancer = $override->countData2('screening', 'status', 1, 'brain_cancer', 1, 'site_id', $row['id']);
        $cervical_cancer = $override->countData2('screening', 'status', 1, 'cervical_cancer', 1, 'site_id', $row['id']);
        $prostate_cancer = $override->countData2('screening', 'status', 1, 'prostate_cancer', 1, 'site_id', $row['id']);

        $output .= '
            <tr>
                <td>' . $x . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . $registered . '</td>
                <td align="right">' . $screened . '</td>
                <td align="right">' . $eligible . '</td>
                <td align="right">' . $enrolled . '</td>
                <td align="right">' . $end_study . '</td>
                <td align="right">' . $breast_cancer . '</td>
                <td align="right">' . $brain_cancer . '</td>
                <td align="right">' . $cervical_cancer . '</td>
                <td align="right">' . $prostate_cancer . '</td>
            </tr>
        ';

        $x++;
    }

    $output .= '
            </table>
        </td>
    </tr>
</table>
    ';
}

$pdf->loadHtml($output);
$pdf->setPaper('A4', 'landscape');
$pdf->render();
$pdf->stream($file_name, array("Attachment" => false));
