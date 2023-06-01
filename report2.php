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
        // $data = $override->getNews('clients', 'status', 1, 'screened', 1);
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

$pdf = new Pdf();

$file_name = $title . '.pdf';

$output = ' ';

// if ($_GET['group'] == 2) {
if ($site_data) {

    $output .= '
            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>NIMREGENIN SUMMARY REPORT  ' . date('Y-m-d') . '</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>Report FOR ' . $title . ':  Total ( ' . $data_count . ' )</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="18">                        
                        <br />
                        <table width="100%" border="1" cellpadding="5" cellspacing="0">
                            <tr>
                                <th rowspan="2">No.</th>
                                <th rowspan="2">SITE</th>
                                <th rowspan="2">CRF 1</th>
                                <th rowspan="2">CRF 2</th>
                                <th rowspan="2">CRF 3</th>
                                <th rowspan="2">CRF 4</th>
                                <th rowspan="2">CRF 5</th>
                                <th rowspan="2">CRF 6</th>
                                <th rowspan="2">CRF 7</th>
                            </tr>
                        ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($site_data as $row) {
        $registered = $override->countData('clients', 'status', 1, 'site_id', $row['id']);
        $registered_Total = $override->getCount('clients', 'status', 1);
        $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $row['id']);
        $screened_Total = $override->countData('clients', 'status', 1, 'screened', 1);
        $breast_cancer = $override->countData2('screening', 'status', 1, 'breast_cancer', 1, 'site_id', $row['id']);
        $breast_cancer_Total = $override->countData('screening', 'status', 1, 'breast_cancer', 1);
        $brain_cancer = $override->countData2('screening', 'status', 1, 'brain_cancer', 1, 'site_id', $row['id']);
        $brain_cancer_Total = $override->countData('screening', 'status', 1, 'brain_cancer', 1);
        $cervical_cancer = $override->countData2('screening', 'status', 1, 'cervical_cancer', 1, 'site_id', $row['id']);
        $cervical_cancer_Total = $override->countData('screening', 'status', 1, 'cervical_cancer', 1);
        $prostate_cancer = $override->countData2('screening', 'status', 1, 'prostate_cancer', 1, 'site_id', $row['id']);
        $prostate_cancer_Total = $override->countData('screening', 'status', 1, 'prostate_cancer', 1);
        $biopsy = $override->countData2('screening', 'status', 1, 'biopsy', 1, 'site_id', $row['id']);
        $biopsy_Total = $override->countData('screening', 'status', 1, 'biopsy', 1);
        $eligible = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $row['id']);
        $eligible_Total = $override->countData('clients', 'status', 1, 'eligible', 1);
        $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $row['id']);
        $enrolled_Total = $override->countData('clients', 'status', 1, 'enrolled', 1);
        $end_study = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $row['id']);
        $end_study_Total = $override->countData('clients', 'status', 1, 'end_study', 1);

        $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['name']  . '</td>
                    <td align="right">' . $crf1 . '</td>
                    <td align="right">' . $crf2 . '</td>
                    <td align="right">' . $crf3 . '</td>
                    <td align="right">' . $crf4 . '</td>
                    <td align="right">' . $crf5 . '</td>
                    <td align="right">' . $crf6 . '</td>
                    <td align="right">' . $crf7 . '</td>
                </tr>
                
            ';

        $x += 1;
    }

    $output .= '
                <tr>
                    <td align="right" colspan="2"><b>Total</b></td>
                    <td align="right"><b>' . $crf1 . '</b></td>
                    <td align="right"><b>' . $crf2 . '</b></td>
                    <td align="right"><b>' . $crf3 . '</b></td>
                    <td align="right"><b>' . $crf4 . '</b></td>
                    <td align="right"><b>' . $crf5 . '</b></td>
                    <td align="right"><b>' . $crf6 . '</b></td>
                    <td align="right"><b>' . $crf7 . '</b></td>
                </tr>
                ';

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
                            <p align="right">----'.$user->data()->firstname. ' '.$user->data()->lastname.'-----<br />Printed By</p>
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
                            <p align="right">-----'.date('Y-m-d').'-------<br />Date Printed</p>
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
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
