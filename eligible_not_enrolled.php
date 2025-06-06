<?php

require 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $site_id =1;

        $eligible_counts = $override->eligible_counts($site_id);
        $eligible = $override->eligible($site_id);

        $eligible_enrolled_counts = $override->eligible_enrolled_counts($site_id);
        $eligible_enrolled = $override->eligible_enrolled($site_id);

        $eligible_not_enrolled_counts = $override->eligible_not_enrolled_counts($site_id);
        $eligible_not_enrolled = $override->eligible_not_enrolled($site_id);



        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

if($site_id==1){
    $title = 'MNH - Eligible Clients but not enrolled ' . date('Y-m-d');
}elseif($site_id==2){        
    $title = 'ORCI - Eligible Clients but not enrolled ' . date('Y-m-d');
}else{
    $title = 'All Sites - Eligible Clients but not enrolled ' . date('Y-m-d');
}

$pdf = new Pdf();

// $title = 'NIMREGENIN SUMMARY REPORT_'. date('Y-m-d');
$file_name = $title . '.pdf';

$output = ' ';


$output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="23" align="center" style="font-size: 18px">
                        <b>' . $title . '</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="23" align="center" style="font-size: 18px">
                        <b>Total Eligible ( ' . $eligible_counts . ' ):  Total Enrolled( ' . $eligible_enrolled_counts . ' ):  Not Enrolled( ' . $eligible_not_enrolled_counts . ' )</b>
                    </td>
                </tr>    
                <tr>
                    <th colspan="1">No.</th>
                    <th colspan="2">Study ID</th>
                    <th colspan="2">ID NUMBER</th>
                    <th colspan="2">First Name</th>
                    <th colspan="2">Middle Name</th>
                    <th colspan="2">Last Name</th>                 
                    <th colspan="2">INCLUSION</th>        
                    <th colspan="2">EXCLUSION</th>        
                    <th colspan="2">ELIGIBILTY</th>   
                    <th colspan="2">ENROLLMENT</th>   
                    <th colspan="2">SITE</th>   
                    <th colspan="2">STATUS</th>   
                </tr>
    
     ';

// Load HTML content into dompdf
$x = 1;
foreach ($eligible_not_enrolled as $client) {
    if ($client['eligibility1'] == 1) {
        $eligibility1 = 'ELIGIBLE';
    } else if ($client['eligibility1'] == 2) {
        $eligibility1 = 'NOT ELIGIBLE';
    } else {
        $eligibility1 = 'NOT DONE';
    }

    if ($client['eligible'] == 1) {
        $eligible = 'ELIGIBLE';
    } else if ($client['eligible'] == 2) {
        $eligible = 'NOT ELIGIBLE';
    } else {
        $eligible = 'NOT DONE';
    }


    if ($client['eligibility2'] == 1) {
        $eligibility2 = 'ELIGIBLE';
    } else if ($client['eligibility2'] == 2) {
        $eligibility2 = 'NOT ELIGIBLE';
    } else {
        $eligibility2 = 'NOT DONE';
    }

    if ($client['enrolled'] == 1) {
        $enrolled = 'Enrolled';
    }else if ($client['enrolled'] == 2) {
        $enrolled = 'NOT Enrolled';
    } else {
        $enrolled = 'NOT Enrolled';
    }

    $screening = $override->get('screening', 'client_id', $client['id'])[0];


    if ($client['site_id'] == 1) {
        $site = 'MNH';
    }else if ($client['site_id'] == 2) {
        $site = 'ORCI';
    } else {
        $site = 'NOT Active';
    }

    if ($client['status'] == 1) {
        $status = 'Active';
    }else if ($client['status'] == 2) {
        $status = 'NOT Active';
    } else {
        $status = 'NOT Active';
    }


    $output .= '
         <tr>
            <td colspan="1">' . $x . '</td>
            <td colspan="2">' . $client['study_id'] . '</td>
            <td colspan="2">' . $client['id_number'] . '</td>
            <td colspan="2">' . $client['firstname'] . '</td>
            <td colspan="2">' . $client['middlename'] . '</td>
            <td colspan="2">' . $client['lastname'] . '</td>
            <td colspan="2">' . $eligibility1 . '</td>
            <td colspan="2">' . $eligibility2 . '</td>
            <td colspan="2">' . $eligible . '</td>
            <td colspan="2">' . $enrolled . '</td>
            <td colspan="2">' . $site . '</td>
            <td colspan="2">' . $status . '</td>
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
