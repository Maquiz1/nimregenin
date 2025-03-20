<?php

require 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $site_id =1;

        $screened_counts = $override->screened_counts($site_id);
        $screened = $override->screened($site_id);

        $eligible_counts = $override->eligible_counts($site_id);
        $eligible = $override->eligible($site_id);

        $not_eligible_counts = $override->not_eligible_counts($site_id);
        $not_eligible = $override->not_eligible($site_id);

        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

if($site_id==1){
    $title = 'MNH - Screend Clients but not Eligible ' . date('Y-m-d');
}elseif($site_id==2){        
    $title = 'ORCI - Screend Clients but not Eligible ' . date('Y-m-d');
}else{
    $title = 'All Sites - Screend Clients but not Eligible ' . date('Y-m-d');
}

$pdf = new Pdf();

// $title = 'NIMREGENIN SUMMARY REPORT_'. date('Y-m-d');
$file_name = $title . '.pdf';

$output = ' ';


$output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="25" align="center" style="font-size: 18px">
                        <b>' . $title . '</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="25" align="center" style="font-size: 18px">
                        <b>Total Screened ( ' . $screened_counts . ' ):  Eligible( ' . $eligible_counts . ' ):  Not Eligible( ' . $not_eligible_counts . ' ) </b>
                    </td>
                </tr>    
                <tr>
                    <th colspan="1">No.</th>
                    <th colspan="2">Study ID</th>
                    <th colspan="2">ID NUMBER</th>
                    <th colspan="2">First Name</th>
                    <th colspan="2">Middle Name</th>
                    <th colspan="2">Last Name</th>  
                    <th colspan="2">SCREE NING</th>                                
                    <th colspan="2">INCLU SION</th>        
                    <th colspan="2">EXCLU SION</th>        
                    <th colspan="2">ELIGI BILTY</th>   
                    <th colspan="2">ENROLL MENT</th>   
                    <th colspan="2">SITE</th>   
                    <th colspan="2">STA TUS</th>   
                </tr>
    
     ';

// Load HTML content into dompdf
$x = 1;
foreach ($not_eligible as $client) {
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

    if ($client['screened'] == 1) {
        $screened = 'DONE';
    }else if ($client['screened'] == 2) {
        $screened = 'NOT DONE';
    } else {
        $screened = 'NOT FOUND';
    }


    $output .= '
         <tr>
            <td colspan="1">' . $x . '</td>
            <td colspan="2">' . $client['study_id'] . '</td>
            <td colspan="2">' . $client['id_number'] . '</td>
            <td colspan="2">' . $client['firstname'] . '</td>
            <td colspan="2">' . $client['middlename'] . '</td>
            <td colspan="2">' . $client['lastname'] . '</td>
            <td colspan="2">' . $screened . '</td>
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
