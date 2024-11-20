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
        $data = $override->getNews('visit', 'expected_date', date('Y-m-d'), 'status', 0);
        // $data = $override->getNews2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', $_GET['day']);
        $data_count = $override->getNo1('visit', 'expected_date', date('Y-m-d'), 'status', 0);
        // $data_count2 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code',$_GET['day']);
        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

// if ($_GET['day'] == 'D0') {
//     $title = 'Day 0';
// } elseif ($_GET['day'] == 'D7') {
//     $title = 'Day 7';
// } elseif ($_GET['day'] == 'D14') {
//     $title = 'Day 14';
// } elseif ($_GET['day'] == 'D30') {
//     $title = 'Day 30';
// } elseif ($_GET['day'] == 'D60') {
//     $title = 'Day 60';
// } elseif ($_GET['day'] == 'D90') {
//     $title = 'Day 90';
// } elseif ($_GET['day'] == 'D120') {
//     $title = 'Day 120';
// }

$title = 'Today Visits Schedule';


// print_r($_GET);
$pdf = new Pdf();

$file_name = $title . '.pdf';

$output = ' ';

// if ($data) {

    $output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">

        <tr>
            <td colspan="20" align="center" style="font-size: 18px">
                <b>Date ' . date('Y-m-d') . '</b>
            </td>
        </tr>
        <tr>
            <td colspan="20" align="center" style="font-size: 18px">
                <b>Table 6 </b>
            </td>
        </tr>
    
        <tr>
            <td colspan="20" align="center" style="font-size: 18px">
                <b>NIMREGENIN REPORT( Today Schedule Visits)</b>
            </td>
        </tr>
        <tr>
            <td colspan="20" align="center" style="font-size: 18px">
                <b>Report FOR ' . $title . ':  Total Tday Visits ( ' . $data_count . ' )</b>
            </td>
        </tr>
    
        <tr>
            <th colspan="2">No.</th>
            <th colspan="2">Study ID</th>
            <th colspan="2">Visit Name</th>        
            <th colspan="2">Expected Date</th>
            <th colspan="2">Visit Date</th>
            <th colspan="2">Name</th>
            <th colspan="2">Gender</th>
            <th colspan="2">Age</th>
            <th colspan="2">Phnone</th>
            <th colspan="2">Site</th>
        </tr>
    
     ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($data as $row) {
        $client_name = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0];
        $study_id = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0]['study_id'];
        $gender = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0]['gender'];
        $age = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0]['age'];
        $phone_number = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0]['phone_number'];
        $site = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0]['site_id'];
        $site_id = $override->get('site', 'id', $site)[0]['name'];

        $output .= '
         <tr>
            <td colspan="2">' . $x . '</td>
            <td colspan="2">' . $study_id . '</td>
            <td colspan="2">' . $row['visit_name'] . '</td>
            <td colspan="2">' . $row['expected_date'] . '</td>
            <td colspan="2">' . $row['visit_date'] . '</td>
            <td colspan="2">' . $client_name['firstname'] . '  -  '. $client_name['lastname'] . '</td>
            <td colspan="2">' . $gender . '</td>
            <td colspan="2">' . $age . '</td>
            <td colspan="2">' . $phone_number . '</td>
            <td colspan="2">' . $site_id . '</td>
        </tr>
        ';

        $x += 1;
    }

    $output .= '
        </table>  
    ';
// } 



// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// SetPaper the HTML as PDF
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
