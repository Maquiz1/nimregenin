<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        // switch (Input::get('report')) {
        //     case 1:
        //         $data = $override->searchBtnDate3('batch', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
        //         $data_count = $override->getCountReport('batch', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
        //         break;
        //     case 2:
        //         $data = $override->searchBtnDate3('check_records', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
        //         $data_count = $override->getCountReport('check_records', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
        //         break;
        //     case 3:
        //         $data = $override->searchBtnDate3('batch_records', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
        //         $data_count = $override->getCountReport('batch_records', 'create_on', $_GET['start'], 'create_on', $_GET['end'], 'use_group', $_GET['group']);
        //         break;
        // }

        $site_data = $override->getData('site');
        $registered_Total = $override->getCount('clients', 'status', 1);
        $enrolled_Total = $override->getCount1('clients', 'status', 1, 'enrolled', 1);
        // $enrolled = $override->getCount2('clients', 'status', 1, 'enrolled', 1);
        // $name = $override->get('user', 'status', 1, 'screened', $user->data()->id);
        // $data_count = $override->getCount2('clients', 'status', 1, 'screened',1, 'site_id', $ussite_dataer->data()->site_id);

        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

if ($_GET['group'] == 1) {
    $title = 'Medicines';
} elseif ($_GET['group'] == 2) {
    $title = 'Medical Equipments';
} elseif ($_GET['group'] == 3) {
    $title = 'Accessories';
} elseif ($_GET['group'] == 4) {
    $title = 'Supplies';
}



$title = 'NIMREGENIN SUMMARY REPORT_' . date('Y-m-d');

$pdf = new Pdf();

// $title = 'NIMREGENIN SUMMARY REPORT_'. date('Y-m-d');
$file_name = $title . '.pdf';

$output = ' ';

// if ($_GET['group'] == 2) {
if ($site_data) {

    $output .= '
            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>DATE  ' . date('Y-m-d') . '</b>
                    </td>
                </tr>


                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>TABLE 2 </b>
                    </td>
                </tr>

                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>' . $title . '</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>Total Registered ( ' . $registered_Total . ' ):  Total Enrolled( ' . $enrolled_Total . ' )</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="18">                        
                        <br />
                        <table width="100%" border="1" cellpadding="5" cellspacing="0">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">SITE</th>
                                <th colspan="2">CRF 1</th>
                                <th colspan="2">CRF 2</th>
                                <th colspan="2">CRF 3</th>
                                <th colspan="2">CRF 4</th>
                                <th colspan="2">CRF 5</th>
                                <th colspan="2">CRF 6</th>
                                <th colspan="2">CRF 7</th>
                            </tr>
                            <tr>
                                <th>Req.</th>
                                <th>Ava.</th>
                                <th>Req.</th>
                                <th>Ava.</th>
                                <th>Req.</th>
                                <th>Ava.</th>
                                <th>Req.</th>
                                <th>Ava.</th>
                                <th>Req.</th>
                                <th>Ava.</th>
                                <th>Req.</th>
                                <th>Ava.</th>
                                <th>Req.</th>
                                <th>Ava.</th>
                            </tr>
            ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($site_data as $row) {
        $enrolled = $override->countData1('clients', 'status', 1, 'enrolled', 1,'site_id', $row['id']);
        $crf1 = $override->countData('crf1', 'status', 1, 'site_id', $row['id']);
        $crf1_Total = $override->getCount('crf1', 'status', 1);
        $crf2 = $override->countData('crf2', 'status', 1, 'site_id', $row['id']);
        $crf2_Total = $override->getCount('crf2', 'status', 1);
        $crf3 = $override->countData('crf3', 'status', 1, 'site_id', $row['id']);
        $crf3_Total = $override->getCount('crf3', 'status', 1);
        $crf4 = $override->countData('crf4', 'status', 1, 'site_id', $row['id']);
        $crf4_Total = $override->getCount('crf4', 'status', 1);
        $crf5 = $override->countData('crf5', 'status', 1, 'site_id', $row['id']);
        $crf5_Total = $override->getCount('crf5', 'status', 1);
        $crf6 = $override->countData('crf6', 'status', 1, 'site_id', $row['id']);
        $crf6_Total = $override->getCount('crf6', 'status', 1);
        $crf7 = $override->countData('crf7', 'status', 1, 'site_id', $row['id']);
        $crf7_Total = $override->getCount('crf7', 'status', 1);

        $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['name']  . '</td>
                    <td>' . $enrolled . '</td>
                    <td>' . $crf1 . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $crf2 . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $crf3 . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $crf4 . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $crf5 . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $crf6 . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $crf7 . '</td>
                </tr>
            ';

        $x += 1;
    }

    $output .= '
                <tr>
                    <td align="right" colspan="2"><b>Total</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $crf1_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $crf2_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $crf3_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $crf4_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $crf5_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $crf6_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $crf7_Total . '</b></td>
                </tr>  

    '
    ;

    $output .= '
            </table>    
                <tr>
                    <td colspan="9" align="center" style="font-size: 18px">
                        <br />
                        <br />
                        <br />
                        <br />
                        <br />
                        <br />
                        <p align="right">----'.$user->data()->firstname. ' '.$user->data()->lastname.'-----<br />Prepared By</p>
                        <br />
                        <br />
                        <br />
                    </td>

                    <td colspan="9" align="center" style="font-size: 18px">
                        <br />
                        <br />
                        <br />
                        <br />
                        <br />
                        <br />
                        <p align="right">-----'.date('Y-m-d').'-------<br />Date Prepared</p>
                        <br />
                        <br />
                        <br />
                    </td>
                </tr>
        </table>    
';
}

// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// SetPaper the HTML as PDF
// $pdf->setPaper('A4', 'portrait');
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
