<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {

        $date_end = '2023-12-31';
        $site_data = $override->getData('site');
        $Total = $override->getCount4('clients', 'status', 1, 'clinic_date', $date_end);
        $data_enrolled = $override->getCount5('clients', 'status', 1, 'enrolled', 1, 'clinic_date', $date_end);

        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}


$title = 'NIMREGENIN SUMMARY REPORT AS OF '.$date_end;

$pdf = new Pdf();

$file_name = $title . '.pdf';

$output = ' ';

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
                        <b>Total Registered ( ' . $Total . ' ):  Total Enrolled( ' . $data_enrolled . ' )</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="18">                        
                        <br />
                        <table width="100%" border="1" cellpadding="5" cellspacing="0">
                            <tr>
                                <th rowspan="2"> No. </th>
                                <th rowspan="2"> SITE </th>
                                <th rowspan="2"> REGIS TERED </th>
                                <th rowspan="2"> SCRE ENED </th>
                                <th colspan="4"> CANCER ( INCLUSION )</th>
                                <th rowspan="2">ELIG IBLE</th>
                                <th rowspan="2">ENRO LLED</th>
                                <th colspan="6">ENROLLED</th>
                                <th rowspan="2">END</th>
                            </tr>
                            <tr>
                                <th>Breast</th>
                                <th>Brain</th>
                                <th>Cervical </th>
                                <th>Prostate </th>
                                <th>Radio therapy</th>
                                <th>Chemo therapy</th>
                                <th>Surgery</th>
                                <th>Active Surve illance</th>
                                <th>Hormonal Therapy i.e ADT</th>
                                <th>Other</th>
                            </tr>
            ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($site_data as $row) {
        $registered = $override->getCount1August('clients', 'status', 1, 'site_id', $row['id'], 'clinic_date', $date_end);
        $registered_Total = $override->getCountAugust('clients', 'status', 1, 'clinic_date', $date_end);
        $screened = $override->getCount2August('clients', 'status', 1, 'screened', 1, 'site_id', $row['id'], 'clinic_date', $date_end);
        $screened_Total = $override->getCount1August('clients', 'status', 1, 'screened',1, 'clinic_date', $date_end);
        $breast_cancer = $override->getCount2August('screening', 'status', 1, 'breast_cancer', 1, 'site_id', $row['id'], 'created_on', $date_end);
        $breast_cancer_Total = $override->getCount1August('screening', 'status', 1, 'breast_cancer',1, 'created_on', $date_end);
        $brain_cancer = $override->getCount2August('screening', 'status', 1, 'brain_cancer', 1, 'site_id', $row['id'], 'created_on', $date_end);
        $brain_cancer_Total = $override->getCount1August('screening', 'status', 1, 'brain_cancer',1, 'created_on', $date_end);
        $cervical_cancer = $override->getCount2August('screening', 'status', 1, 'cervical_cancer', 1, 'site_id', $row['id'], 'created_on', $date_end);
        $cervical_cancer_Total = $override->getCount1August('screening', 'status', 1, 'cervical_cancer',1, 'created_on', $date_end);
        $prostate_cancer = $override->getCount2August('screening', 'status', 1, 'prostate_cancer', 1, 'site_id', $row['id'], 'created_on', $date_end);
        $prostate_cancer_Total = $override->getCount1August('screening', 'status', 1, 'prostate_cancer',1, 'created_on', $date_end);
        $biopsy = $override->getCount2August('screening', 'status', 1, 'biopsy', 1, 'site_id', $row['id'], 'created_on', $date_end);
        $biopsy_Total = $override->getCount1August('screening', 'status', 1, 'biopsy',1, 'created_on', $date_end);
        $eligible = $override->getCount2August('clients', 'status', 1, 'eligible', 1, 'site_id', $row['id'], 'clinic_date', $date_end);
        $eligible_Total = $override->getCount1August('clients', 'status', 1, 'eligible',1, 'clinic_date', $date_end);
        $enrolled1 = $override->getCount3August('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 1, 'site_id', $row['id'], 'clinic_date', $date_end);
        $enrolled_Total1 = $override->getCount2August('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 1,'clinic_date', $date_end);
        $enrolled2 = $override->getCount3August('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 2, 'site_id', $row['id'], 'clinic_date', $date_end);
        $enrolled_Total2 = $override->getCount2August('clients', 'status', 1, 'enrolled', 1, 'treatment_type',2, 'clinic_date', $date_end);
        $enrolled3 = $override->getCount3August('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 3, 'site_id', $row['id'], 'clinic_date', $date_end);
        $enrolled_Total3 = $override->getCount2August('clients', 'status', 1, 'enrolled', 1, 'treatment_type',3, 'clinic_date', $date_end);
        $enrolled4 = $override->getCount3August('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 4, 'site_id', $row['id'], 'clinic_date', $date_end);
        $enrolled_Total4 = $override->getCount2August('clients', 'status', 1, 'enrolled', 1, 'treatment_type',4, 'clinic_date', $date_end);
        $enrolled5 = $override->getCount3August('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 5, 'site_id', $row['id'], 'clinic_date', $date_end);
        $enrolled_Total5 = $override->getCount2August('clients', 'status', 1, 'enrolled', 1, 'treatment_type',5, 'clinic_date', $date_end);
        $enrolled6 = $override->getCount3August('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 6, 'site_id', $row['id'], 'clinic_date', $date_end);
        $enrolled_Total6 = $override->getCount2August('clients', 'status', 1, 'enrolled', 1, 'treatment_type',6, 'clinic_date', $date_end);
        $enrolled = $override->getCount2August('clients', 'status', 1, 'enrolled', 1, 'site_id', $row['id'], 'clinic_date', $date_end);
        $enrolled_Total = $override->getCount1August('clients', 'status', 1, 'enrolled',1, 'clinic_date', $date_end);
        $end_study = $override->getCount2August('clients', 'status', 1, 'end_study', 1, 'site_id', $row['id'], 'clinic_date', $date_end);
        $end_study_Total = $override->getCount1August('clients', 'status', 1, 'end_study',1, 'clinic_date', $date_end);

        $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['name']  . '</td>
                    <td align="right">' . $registered . '</td>
                    <td align="right">' . $screened . '</td>
                    <td align="right">' . $breast_cancer . '</td>
                    <td align="right">' . $brain_cancer . '</td>
                    <td align="right">' . $cervical_cancer . '</td>
                    <td align="right">' . $prostate_cancer . '</td>
                    <td align="right">' . $eligible . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $enrolled1 . '</td>
                    <td align="right">' . $enrolled2 . '</td>
                    <td align="right">' . $enrolled3 . '</td>
                    <td align="right">' . $enrolled4 . '</td>
                    <td align="right">' . $enrolled5 . '</td>
                    <td align="right">' . $enrolled6 . '</td>
                    <td align="right">' . $end_study . '</td>
                </tr>
            ';

        $x += 1;
    }

    $output .= '

                <tr>
                    <td align="right" colspan="2"><b>Total</b></td>
                    <td align="right"><b>' . $registered_Total . '</b></td>
                    <td align="right"><b>' . $screened_Total . '</b></td>
                    <td align="right"><b>' . $breast_cancer_Total . '</b></td>
                    <td align="right"><b>' . $brain_cancer_Total . '</b></td>
                    <td align="right"><b>' . $cervical_cancer_Total . '</b></td>
                    <td align="right"><b>' . $prostate_cancer_Total . '</b></td>
                    <td align="right"><b>' . $eligible_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total1 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total2 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total3 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total4 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total5 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total6 . '</b></td>
                    <td align="right"><b>' . $end_study_Total . '</b></td>
                </tr>  

    ';

    $output .= '
            
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
