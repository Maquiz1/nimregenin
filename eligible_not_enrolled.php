<?php

require 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $site = 1;

        $eligible_counts = $override->eligible_counts($site);
        $eligible = $override->eligible($site);

        $eligible_enrolled_counts = $override->eligible_enrolled_counts($site);
        $eligible_enrolled = $override->eligible_enrolled($site);

        $eligible_not_enrolled_counts = $override->eligible_not_enrolled_counts($site);
        $eligible_not_enrolled = $override->eligible_not_enrolled($site);



        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

$title = 'MNH - Eligible Clients but not_enrolled ' . date('Y-m-d');

$pdf = new Pdf();

// $title = 'NIMREGENIN SUMMARY REPORT_'. date('Y-m-d');
$file_name = $title . '.pdf';

$output = ' ';


$output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="9" align="center" style="font-size: 18px">
                        <b>' . $title . '</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="9" align="center" style="font-size: 18px">
                        <b>Total Eligible ( ' . $eligible_counts . ' ):  Total Enrolled( ' . $eligible_enrolled_counts . ' ):  Not Enrolled( ' . $eligible_not_enrolled_counts . ' )</b>
                    </td>
                </tr>    
                <tr>
                    <th colspan="1">No.</th>
                    <th colspan="2">Date</th>
                    <th colspan="2">Study ID</th>
                    <th colspan="2">ELIGIBILTY</th>        
                    <th colspan="2">Reason</th>
                </tr>
    
     ';

// Load HTML content into dompdf
$x = 1;
foreach ($eligible_not_enrolled as $client) {
    if ($client['eligible'] == 1) {
        $eligible = 'ELIGIBLE';
    } else if ($site == 2) {
        $eligible = 'NOT ELIGIBLE';
    } else {
        $eligible = 'NOT DONE';
    }

    if ($client['enrolled'] == 1) {
        $enrolled = 'Enrolled';
    }else if ($client['enrolled'] == 2) {
        $enrolled = 'NOT Enrolled';
    } else {
        $enrolled = 'NOT DONE';
    }

    $screening = $override->get('screening', 'client_id', $client['id'])[0];


    if ($site == 1) {
        $site = 'MNH';
    }else if ($site == 2) {
        $site = 'ORCI';
    } else {
        $site = 'No Site Asigned';
    }

    $output .= '
         <tr>
            <td colspan="1">' . $x . '</td>
            <td colspan="2">' . $client['study_id'] . '</td>
            <td colspan="2">' . $client['id_number'] . '</td>
            <td colspan="2">' . $client['firstname'] . '</td>
            <td colspan="2">' . $client['middlename'] . '</td>
            <td colspan="2">' . $client['lastname'] . '</td>
            <td colspan="2">' . $eligible . '</td>
            <td colspan="2">' . $enrolled . '</td>
            <td colspan="2">' . $site . '</td>
        </tr>
        ';

    $x += 1;
}

$output .= '
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
