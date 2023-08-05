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
        $Total = $override->getCount('clients', 'status', 1);
        $data_enrolled = $override->getCount1('clients', 'status', 1, 'enrolled', 1);
        // $name = $override->get('user', 'status', 1, 'screened', $user->data()->id);
        // $data_count = $override->getCount2('clients', 'status', 1, 'screened',1, 'site_id', $ussite_dataer->data()->site_id);

        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

// if ($_GET['group'] == 1) {
//     $title = 'Medicines';
// } elseif ($_GET['group'] == 2) {
//     $title = 'Medical Equipments';
// } elseif ($_GET['group'] == 3) {
//     $title = 'Accessories';
// } elseif ($_GET['group'] == 4) {
//     $title = 'Supplies';
// }



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
                        <b>TABLE 0 (Screened With Controll)</b>
                    </td>
                </tr>

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
                                <th rowspan="2"> REG IST ERED </th>
                                <th rowspan="2"> SCRE ENED </th>
                                <th colspan="2"> USE NIMRE GENIN </th>
                                <th colspan="4"> CANCER ( INCLUSION )</th>
                                <th rowspan="2">ELIG IBLE</th>
                                <th rowspan="2">ENRO LLED </th>
                                <th colspan="2">PATI ENT TYPE ( NEW ?)</th>
                                <th colspan="2">USE NIMRE GENIN</th>
                                <th colspan="6">ENROLLED ( TREATMENT TYPE )</th>
                                <th rowspan="2">END</th>
                            </tr>

                            <tr>
                                <th>Y</th>
                                <th>N</th>
                                <th>Bre ast</th>
                                <th>Bra in</th>
                                <th>Cer vical </th>
                                <th>Pro state </th>
                                <th>Y</th>
                                <th>N</th>
                                <th>Y</th>
                                <th>N</th>
                                <th>Radi other apy</th>
                                <th>Chem other apy</th>
                                <th>Sur gery</th>
                                <th>Active Surve illance</th>
                                <th>Hormonal Therapy i.e ADT</th>
                                <th>Other</th>
                            </tr>
            ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($site_data as $row) {
        $registered = $override->countData('clients', 'status', 1, 'site_id', $row['id']);
        $registered_Total = $override->getCount('clients', 'status', 1);
        $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $row['id']);
        $screened_Total = $override->countData('clients', 'status', 1, 'screened', 1);
        $nimregenin1 = $override->countData2('clients', 'status', 1, 'consented_nimregenin', 1, 'site_id', $row['id']);
        $nimregenin_Total1 = $override->countData('clients', 'status', 1, 'consented_nimregenin', 1);
        $nimregenin2 = $override->countData2('clients', 'status', 1, 'consented_nimregenin', 2, 'site_id', $row['id']);
        $nimregenin_Total2 = $override->countData('clients', 'status', 1, 'consented_nimregenin', 2);
        $nimregenin_Total1_2 = $override->getNo0('clients', 'status', 1, 'consented_nimregenin', 1, 'consented_nimregenin', 2);
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
        $enrolled1 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 1, 'site_id', $row['id']);
        $enrolled_Total1 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 1,);
        $enrolled2 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 2, 'site_id', $row['id']);
        $enrolled_Total2 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 2,);
        $enrolled3 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 3, 'site_id', $row['id']);
        $enrolled_Total3 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 3,);
        $enrolled4 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 4, 'site_id', $row['id']);
        $enrolled_Total4 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 4,);
        $enrolled5 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 5, 'site_id', $row['id']);
        $enrolled_Total5 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 5,);
        $enrolled6 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 6, 'site_id', $row['id']);
        $enrolled_Total6 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'treatment_type', 6,);
        $typeTotal = $override->getNoType('clients', 'status', 1, 'enrolled', 1,'treatment_type',1, 'treatment_type',2, 'treatment_type',3, 'treatment_type',4, 'treatment_type',5, 'treatment_type',6, 'treatment_type', 96);
        $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $row['id']);
        $enrolled_Total = $override->countData('clients', 'status', 1, 'enrolled', 1);
        $new1 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'pt_type', 1, 'site_id', $row['id']);
        $new_Total1 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'pt_type', 1,);
        $new2 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'pt_type', 2, 'site_id', $row['id']);
        $new_Total2 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'pt_type', 2);
        $new_Total = $override->countData3('clients', 'status', 1, 'enrolled', 1, 'pt_type', 1, 'pt_type', 2);
        $nimregenin3 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'consented_nimregenin', 1, 'site_id', $row['id']);
        $nimregenin_Total3 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'consented_nimregenin', 1);
        $nimregenin4 = $override->countData4('clients', 'status', 1, 'enrolled', 1, 'consented_nimregenin', 2, 'site_id', $row['id']);
        $nimregenin_Total4 = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'consented_nimregenin', 2);
        $nimregenin_Total3_4 = $override->countData3('clients', 'status', 1, 'enrolled', 1, 'consented_nimregenin', 1, 'consented_nimregenin', 2);
        $end_study = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $row['id']);
        $end_study_Total = $override->countData('clients', 'status', 1, 'end_study', 1);

        $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['name']  . '</td>
                    <td align="right">' . $registered . '</td>
                    <td align="right">' . $screened . '</td>
                    <td align="right">' . $nimregenin1 . '</td>
                    <td align="right">' . $nimregenin2 . '</td>
                    <td align="right">' . $breast_cancer . '</td>
                    <td align="right">' . $brain_cancer . '</td>
                    <td align="right">' . $cervical_cancer . '</td>
                    <td align="right">' . $prostate_cancer . '</td>
                    <td align="right">' . $eligible . '</td>
                    <td align="right">' . $enrolled . '</td>
                    <td align="right">' . $nimregenin3 . '</td>
                    <td align="right">' . $nimregenin4 . '</td>
                    <td align="right">' . $new1 . '</td>
                    <td align="right">' . $new2 . '</td>
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
                    <td align="right" colspan="2"><b>Sub Total</b></td>
                    <td align="right"><b>' . $registered_Total . '</b></td>
                    <td align="right"><b>' . $screened_Total . '</b></td>
                    <td align="right"><b>' . $nimregenin_Total1 . '</b></td>
                    <td align="right"><b>' . $nimregenin_Total2 . '</b></td>
                    <td align="right"><b>' . $breast_cancer_Total . '</b></td>
                    <td align="right"><b>' . $brain_cancer_Total . '</b></td>
                    <td align="right"><b>' . $cervical_cancer_Total . '</b></td>
                    <td align="right"><b>' . $prostate_cancer_Total . '</b></td>
                    <td align="right"><b>' . $eligible_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="right"><b>' . $new_Total1 . '</b></td>
                    <td align="right"><b>' . $new_Total2 . '</b></td>
                    <td align="right"><b>' . $nimregenin_Total3 . '</b></td>
                    <td align="right"><b>' . $nimregenin_Total4 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total1 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total2 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total3 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total4 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total5 . '</b></td>
                    <td align="right"><b>' . $enrolled_Total6 . '</b></td>
                    <td align="right"><b>' . $end_study_Total . '</b></td>
                </tr>  

                 <tr>
                    <td align="right" colspan="2"><b>Grand Total</b></td>
                    <td align="right"><b>' . $registered_Total . '</b></td>
                    <td align="right"><b>' . $screened_Total . '</b></td>
                    <td align="center" colspan="2"><b>' . $nimregenin_Total1_2 . '</b></td>
                    <td align="right"><b>' . $breast_cancer_Total . '</b></td>
                    <td align="right"><b>' . $brain_cancer_Total . '</b></td>
                    <td align="right"><b>' . $cervical_cancer_Total . '</b></td>
                    <td align="right"><b>' . $prostate_cancer_Total . '</b></td>
                    <td align="right"><b>' . $eligible_Total . '</b></td>
                    <td align="right"><b>' . $enrolled_Total . '</b></td>
                    <td align="center" colspan="2"><b>' . $new_Total . '</b></td>
                    <td align="center" colspan="2"><b>' . $nimregenin_Total3_4 . '</b></td>
                    <td align="center" colspan="6"><b>' . $typeTotal . '</b></td>
                    <td align="right"><b>' . $end_study_Total . '</b></td>
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
                        <p align="right">----' . $user->data()->firstname . ' ' . $user->data()->lastname . '-----<br />Prepared By</p>
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
                        <p align="right">-----' . date('Y-m-d') . '-------<br />Date Prepared</p>
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
