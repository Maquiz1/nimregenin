<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $data = $override->MissingData1();
        $total = $override->MissingDataNo1();
        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

$title = 'MISSING CRFS';
$pdf = new Pdf();

$file_name = $title .' - '. date('Y-m-d'). '.pdf';

$output = ' ';

    $output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">

        <tr>
            <td colspan="24" align="center" style="font-size: 18px">
                <b>Date ' . date('Y-m-d') . '</b>
            </td>
        </tr>
    
        <tr>
            <td colspan="24" align="center" style="font-size: 18px">
                <b>NIMREGENIN REPORT( MISSING CRFS VISITS)</b>
            </td>
        </tr>
        <tr>
            <td colspan="24" align="center" style="font-size: 18px">
                <b>Report FOR ' . $title .':  Total Missing CRFS ( ' . $total . ' )</b>
            </td>
        </tr>
    
        <tr>
            <th colspan="2">No.</th>
            <th colspan="2">Study ID</th>
            <th colspan="2">Name</th>
            <th colspan="2">Visit Name</th>        
            <th colspan="2">Expected Date</th>
            <th colspan="2">CRF 1</th>
            <th colspan="2">CRF 2</th>
            <th colspan="2">CRF 3</th>
            <th colspan="2">CRF 4</th>
            <th colspan="2">CRF 7</th>
            <th colspan="2">Phnone</th>
            <th colspan="2">Site</th>
        </tr>
    
     ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($data as $row) {
        $client_name = $override->getNews('clients', 'study_id', $row['study_id'], 'status', 1)[0];
        // $study_id = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0]['study_id'];
        // $gender = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0]['gender'];
        // $age = $override->getNews('clients', 'id', $row['client_id'], 'status', 1)[0]['age'];
        $phone_number = $override->getNews('clients', 'study_id', $row['study_id'], 'status', 1)[0]['phone_number'];
        $site = $override->getNews('clients', 'study_id', $row['study_id'], 'status', 1)[0]['site_id'];
        $site_id = $override->get('site', 'id', $site)[0]['name'];

        $output .= '
         <tr>
            <td colspan="2">' . $x . '</td>
            <td colspan="2">' . $row['study_id'] . '</td>
            <td colspan="2">' . $client_name['firstname'] . '  -  ' . $client_name['lastname'] . '</td>
            <td colspan="2">' . $row['visit_code'] . '</td>
            <td colspan="2">' . $row['expected_date'] . '</td>
            <td colspan="2">' . $row['crf1'] . '</td>
            <td colspan="2">' . $row['crf2'] . '</td>
            <td colspan="2">' . $row['crf3'] . '</td>
            <td colspan="2">' . $row['crf4'] . '</td>
            <td colspan="2">' . $row['crf7'] . '</td>
            <td colspan="2">' . $phone_number . '</td>
            <td colspan="2">' . $site_id . '</td>
        </tr>
        ';

        $x += 1;
    }

    $output .= '
    <tr>
        <td colspan="12" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <p align="right">----' . $user->data()->firstname . ' ' . $user->data()->lastname . '-----<br />Printed By</p>
            <br />
            <br />
            <br />
        </td>

        <td colspan="12" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <p align="right">-----' . date('Y-m-d') . '-------<br />Date Printed</p>
            <br />
            <br />
            <br />
        </td>
    </tr>
        </table>  
    ';


// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// SetPaper the HTML as PDF
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
