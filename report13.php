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

        $DATE = '2023-03-31';
        $site_data = $override->getData('site');
        $registered_Total = $override->getCount4('clients', 'status', 1,'clinic_date', $DATE);
        $enrolled_Total = $override->getCount5('clients', 'status', 1, 'enrolled', 1, 'clinic_date', $DATE);

        $all_required = $override->AllFollowUpRequired($DATE);
        $all_available = $override->AllFollowUpAvailable($DATE);
        $all_missing = $override->AllFollowUpMissing($DATE);

        $all_requiredDay0 = $override->AllFollowUpRequiredDay($DATE, 'D0');
        $all_availableDay0 = $override->AllFollowUpAvailableDay($DATE, 'D0');
        $all_missingDay0 = $override->AllFollowUpMissingDay($DATE, 'D0');


        $all_requiredDay7 = $override->AllFollowUpRequiredDay($DATE, 'D7');
        $all_availableDay7 = $override->AllFollowUpAvailableDay($DATE, 'D7');
        $all_missingDay7 = $override->AllFollowUpMissingDay($DATE, 'D7');

        $all_requiredDay14 = $override->AllFollowUpRequiredDay($DATE, 'D14');
        $all_availableDay14 = $override->AllFollowUpAvailableDay($DATE, 'D14');
        $all_missingDay14 = $override->AllFollowUpMissingDay($DATE, 'D14');

        $all_requiredDay30 = $override->AllFollowUpRequiredDay($DATE, 'D30');
        $all_availableDay30 = $override->AllFollowUpAvailableDay($DATE, 'D30');
        $all_missingDay30 = $override->AllFollowUpMissingDay($DATE, 'D30');

        $all_requiredDay60 = $override->AllFollowUpRequiredDay($DATE, 'D60');
        $all_availableDay60 = $override->AllFollowUpAvailableDay($DATE, 'D60');
        $all_missingDay60 = $override->AllFollowUpMissingDay($DATE, 'D60');

        $all_requiredDay90 = $override->AllFollowUpRequiredDay($DATE, 'D90');
        $all_availableDay90 = $override->AllFollowUpAvailableDay($DATE, 'D90');
        $all_missingDay90 = $override->AllFollowUpMissingDay($DATE, 'D90');

        $all_requiredDay120 = $override->AllFollowUpRequiredDay($DATE, 'D120');
        $all_availableDay120 = $override->AllFollowUpAvailableDay($DATE, 'D120');
        $all_missingDay120 = $override->AllFollowUpMissingDay($DATE, 'D120');

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


$title = 'NIMREGENIN VISIT\'s FOLLOW UP STATUS AS OF ' . $DATE;

$pdf = new Pdf();

// $title = 'NIMREGENIN SUMMARY REPORT_'. date('Y-m-d');
$file_name = $title . '.pdf';

$output = ' ';

// if ($_GET['group'] == 2) {
if ($site_data) {

    $output .= '
            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="28" align="center" style="font-size: 18px">
                        <b> ' . $title . ' </b>
                    </td>
                </tr>

                <tr>
                    <td colspan="28" align="center" style="font-size: 18px">
                        <b>  Total REGISTERED ( ' . $registered_Total . ' )  Total Enrolled ( ' . $enrolled_Total . ' )</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="28">                        
                        <br />
                        <table width="100%" border="1" cellpadding="5" cellspacing="0">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">SITE</th>
                                <th colspan="3">All Visits</th>
                                <th colspan="3">Day 0</th>
                                <th colspan="3">Day 7</th>
                                <th colspan="3">Day 14</th>
                                <th colspan="3">Day 30</th>
                                <th colspan="3">Day 60</th>
                                <th colspan="3">Day 90</th>
                                <th colspan="3">Day 120</th>
                            </tr>
                            <tr>
                                <th>Rq.</th>
                                <th>Av.</th>
                                <th>Ms.</th>
                                <th>Rq.</th>
                                <th>Av.</th>
                                <th>Ms.</th>
                                <th>Rq.</th>
                                <th>Av.</th>
                                <th>Ms.</th>
                                <th>Rq.</th>
                                <th>Av.</th>
                                <th>Ms.</th>
                                <th>Rq.</th>
                                <th>Av.</th>
                                <th>Ms.</th>
                                <th>Rq.</th>
                                <th>Av.</th>
                                <th>Ms.</th>
                                <th>Rq.</th>
                                <th>Av.</th>
                                <th>Ms.</th>
                                <th>Rq.</th>
                                <th>Av.</th>
                                <th>Ms.</th>
                            </tr>
            ';

    $x = 1;
    foreach ($site_data as $row) {
        $sites_required = $override->SiteFollowUpRequired($DATE, $row['id']);
        $sites_available = $override->SiteFollowUpAvailable($DATE, $row['id']);
        $sites_missing = $override->SiteFollowUpMissing($DATE, $row['id']);

        $Day0R = $override->SiteFollowUpRequiredDay($DATE, $row['id'], 'D0');
        $Day0A = $override->SiteFollowUpAvailableDay($DATE, $row['id'], 'D0');
        $Day0M = $override->SiteFollowUpMissingDay($DATE, $row['id'], 'D0');

        $Day7R = $override->SiteFollowUpRequiredDay($DATE, $row['id'], 'D7');
        $Day7A = $override->SiteFollowUpAvailableDay($DATE, $row['id'], 'D7');
        $Day7M = $override->SiteFollowUpMissingDay($DATE, $row['id'], 'D7');

        $Day14R = $override->SiteFollowUpRequiredDay($DATE, $row['id'], 'D140');
        $Day14A = $override->SiteFollowUpAvailableDay($DATE, $row['id'], 'D14');
        $Day14M = $override->SiteFollowUpMissingDay($DATE, $row['id'], 'D140');

        $Day30R = $override->SiteFollowUpRequiredDay($DATE, $row['id'], 'D30');
        $Day30A = $override->SiteFollowUpAvailableDay($DATE, $row['id'], 'D30');
        $Day30M = $override->SiteFollowUpMissingDay($DATE, $row['id'], 'D30');

        $Day60R = $override->SiteFollowUpRequiredDay($DATE, $row['id'], 'D60');
        $Day60A = $override->SiteFollowUpAvailableDay($DATE, $row['id'], 'D60');
        $Day60M = $override->SiteFollowUpMissingDay($DATE, $row['id'], 'D60');


        $Day90R = $override->SiteFollowUpRequiredDay($DATE, $row['id'], 'D90');
        $Day90A = $override->SiteFollowUpAvailableDay($DATE, $row['id'], 'D90');
        $Day90M = $override->SiteFollowUpMissingDay($DATE, $row['id'], 'D90');

        $Day120R = $override->SiteFollowUpRequiredDay($DATE, $row['id'], 'D120');
        $Day120A = $override->SiteFollowUpAvailableDay($DATE, $row['id'], 'D120');
        $Day120M = $override->SiteFollowUpMissingDay($DATE, $row['id'], 'D120');


        $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['name']  . '</td>
                    <td>' . $sites_required . '</td>
                    <td>' . $sites_available . '</td>
                    <td>' . $sites_missing . '</td>
                    <td>' . $Day0R . '</td>
                    <td>' . $Day0A . '</td>
                    <td>' . $Day0M . '</td>
                    <td>' . $Day7R . '</td>
                    <td>' . $Day7A . '</td>
                    <td>' . $Day7M . '</td>
                    <td>' . $Day14R . '</td>
                    <td>' . $Day14A . '</td>
                    <td>' . $Day14M . '</td>
                    <td>' . $Day30R . '</td>
                    <td>' . $Day30A . '</td>
                    <td>' . $Day30M . '</td>
                    <td>' . $Day60R . '</td>
                    <td>' . $Day60A . '</td>
                    <td>' . $Day60M . '</td>
                    <td>' . $Day90R . '</td>
                    <td>' . $Day90A . '</td>
                    <td>' . $Day90M . '</td>
                    <td>' . $Day120R . '</td>
                    <td>' . $Day120A . '</td>
                    <td>' . $Day120M . '</td>
                </tr>
            ';
        $x += 1;
    }

    $output .= '
                <tr>
                    <td align="right" colspan="2"><b>Total</b></td>
                    <td align="right"><b>' . $all_required . '</b></td>
                    <td align="right"><b>' . $all_available . '</b></td>
                    <td align="right"><b>' . $all_missing . '</b></td>
                    <td align="right"><b>' . $all_requiredDay0 . '</b></td>
                    <td align="right"><b>' . $all_availableDay0 . '</b></td>
                    <td align="right"><b>' . $all_missingDay0 . '</b></td>
                    <td align="right"><b>' . $all_requiredDay7 . '</b></td>
                    <td align="right"><b>' . $all_availableDay7 . '</b></td>
                    <td align="right"><b>' . $all_missingDay7 . '</b></td>
                    <td align="right"><b>' . $all_requiredDay14 . '</b></td>
                    <td align="right"><b>' . $all_availableDay14 . '</b></td>
                    <td align="right"><b>' . $all_missingDay14 . '</b></td>
                    <td align="right"><b>' . $all_requiredDay30 . '</b></td>
                    <td align="right"><b>' . $all_availableDay30 . '</b></td>
                    <td align="right"><b>' . $all_missingDay30 . '</b></td>
                    <td align="right"><b>' . $all_requiredDay60 . '</b></td>
                    <td align="right"><b>' . $all_availableDay60 . '</b></td>
                    <td align="right"><b>' . $all_missingDay60 . '</b></td>
                    <td align="right"><b>' . $all_requiredDay90 . '</b></td>
                    <td align="right"><b>' . $all_availableDay90 . '</b></td>
                    <td align="right"><b>' . $all_missingDay90 . '</b></td>
                    <td align="right"><b>' . $all_requiredDay120 . '</b></td>
                    <td align="right"><b>' . $all_availableDay120 . '</b></td>
                    <td align="right"><b>' . $all_missingDay120 . '</b></td>
                </tr>  

    ';

    $output .= '
            </table>    
                <tr>
                    <td colspan="28" align="center" style="font-size: 18px">
                        <p align="left">-----<br />Rq : = Required to be done</p>
                        <p align="left">-----<br />Av : = Done Visits</p>
                        <p align="left">-----<br />Ms : = Not Done visits</p>
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
