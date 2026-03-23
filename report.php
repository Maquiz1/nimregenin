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
        $registeredTotal = $override->getCount('clients', 'status', 1);
        $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $row['id']);
        $screenedTotal = $override->countData('clients', 'status', 1, 'screened', 1);
        $eligible = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $row['id']);
        $eligibleTotal = $override->countData('clients', 'status', 1, 'eligible', 1);
        $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $row['id']);
        $enrolledTotal = $override->countData('clients', 'status', 1, 'enrolled', 1);
        $end_study = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $row['id']);
        $end_studyTotal = $override->countData('clients', 'status', 1, 'end_study', 1);
        $breast_cancer = $override->countData2('screening', 'status', 1, 'breast_cancer', 1, 'site_id', $row['id']);
        $breast_cancerTotal = $override->countData('screening', 'status', 1, 'breast_cancer', 1);
        $brain_cancer = $override->countData2('screening', 'status', 1, 'brain_cancer', 1, 'site_id', $row['id']);
        $brain_cancerTotal = $override->countData('screening', 'status', 1, 'brain_cancer', 1);
        $cervical_cancer = $override->countData2('screening', 'status', 1, 'cervical_cancer', 1, 'site_id', $row['id']);
        $cervical_cancerTotal = $override->countData('screening', 'status', 1, 'cervical_cancer', 1);
        $prostate_cancer = $override->countData2('screening', 'status', 1, 'prostate_cancer', 1, 'site_id', $row['id']);
        $prostate_cancerTotal = $override->countData('screening', 'status', 1, 'prostate_cancer', 1);

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
                <tr>
                    <td align="right" colspan="2"><b>Total</b></td>
                    <td align="right"><b>' . $registeredTotal . '</b></td>
                    <td align="right"><b>' . $screenedTotal . '</b></td>
                    <td align="right"><b>' . $eligibleTotal . '</b></td>
                    <td align="right"><b>' . $enrolledTotal . '</b></td>
                    <td align="right"><b>' . $end_studyTotal . '</b></td>
                    <td align="right"><b>' . $breast_cancerTotal . '</b></td>
                    <td align="right"><b>' . $brain_cancerTotal . '</b></td>
                    <td align="right"><b>' . $cervical_cancerTotal . '</b></td>
                    <td align="right"><b>' . $prostate_cancerTotal . '</b></td>
                </tr>

    '
    ;

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
