<?php

require 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $clients = $override->getNewsNULL('clients', 'status', 1, 'pt_type');
        $Total = $override->getCountNULL('clients', 'status', 1, 'pt_type');
        // $clients = $override->getNews('clients', 'status', 1, 'pt_type',0);
        // $Total = $override->getCount1('clients', 'status', 1, 'pt_type',0);
        // $clients = $override->getNewsNULL('clients', 'status', 1, 'treatment_type');
        // $Total = $override->getCountNULL('clients', 'status', 1, 'treatment_type');
        $data_enrolled = $override->getCount1('clients', 'status', 1, 'enrolled', 1);
        $successMessage = 'Report Successful Created';
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}

$span0 = 23;
$span1 = 12;
$span2 = 11;




$title = 'LIST OF CLIENTS WHICH MISSING PATIENT TYPE IF IS NEW OR ALREADY ENROLLED OR CONSETED TO USE NIMREGENIN DURING INCLUSION AND TREATMENT TYPE DURING ELIGIBILITY' . date('Y-m-d');

$pdf = new Pdf();

// $title = 'NIMREGENIN SUMMARY REPORT_'. date('Y-m-d');
$file_name = $title . '.pdf';

$output = ' ';

$output .= '
<html>
    <head>
        <style>
            @page { margin: 50px;}
            header { position: fixed; top: -50px; left: 0px; right: 0px; height: 100px;}
            footer { position: fixed; bottom: -50px; left: 0px; right: 0px; height: 50px; }
            

            .tittle {
                position: fixed;
                right: 20px;
                top: -30px;
             }

            .reportTitle {
                position: fixed;
                left: 20px;
                top: -30px;
             }             

            .period {
                position: fixed;
                right: 470px;
                top: -30px;
                color: blue;
             }
            .reviewed {
                position: fixed;
                right: 270px;
                top: -1px;
             }

            .menuTitle {
                position: center;
                top: -30px;
            }
            .content {
                margin-top: 50px;
            }

            .sufficientStock {
                color: green;
             }
            .outStock {
                color: red;
             }
            .lowStock {
                color: yellow;
             }
            .CheckedStock {
                color: green;
             }
            .NotCheckedStock {
                color: red;
             }
            .ExpiredStock {
                color: red;
             }
            .NotExpiredStock {
                color: green;
             }
        </style>
    </head>
    <body>
        <header>
            <div><span class="page"></span></div>
            <div class="reportTitle">e-NIMREGENIN Report</div>
            <div class="tittle">National Institute For Medical Research (NIMR)</div>
            <div class="period">' . date('Y-m-d') . '</div>
        </header>
        <footer>
            <div>SOP CODE:- Version 1:</div>
            <div class="reviewed">Checked By  .................Reviewed By  .................( INITIALS )</div>
        </footer>
';

$output .=
'
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
 <tr>
                    <td colspan="' . $span0 . '" align="center" style="font-size: 18px">
                            <b>DATE  ' . date('Y-m-d') . '</b>
                        </td>
                    </tr>


                <tr>
                <td colspan="' . $span0 . '" align="center" style="font-size: 18px">
                        <b>TABLE 2 </b>
                    </td>
                </tr>

                <tr>
                <td colspan="' . $span0 . '" align="center" style="font-size: 18px">
                        <b>' . $title . '</b>
                    </td>
                </tr>
                <tr>
                <td colspan="' . $span0 . '" align="center" style="font-size: 18px">
                        <b>Total Missing ( ' . $Total . ' ):  Total Enrolled( ' . $data_enrolled . ' )</b>
                    </td>
                </tr>
    
                <tr>
                    <th colspan="1">No.</th>
                    <th colspan="3">Date</th>
                    <th colspan="2">Study ID</th>
                    <th colspan="2">Patient Type</th>
                    <th colspan="2">Treatment Type</th>
                    <th colspan="2">Previous Date</th>
                    <th colspan="2">Total Cycle</th>
                    <th colspan="2">Consent To Use Nimregenin</th>
                    <th colspan="3">ELIGIBILTY</th>        
                    <th colspan="2">Consented To Be Part Of Study</th>
                    <th colspan="2">SITE</th>
                </tr>
    
     ';

if ($_GET['group'] == 1 || $_GET['group'] == 3 || $_GET['group'] == 4) {

    $output .= '
                    <th colspan="2">Expiry Date</th>
                                ';
}
$output .= '
                        <th colspan="2">Status</th>
                        <th colspan="2">Remarks</th>
                    </tr>
                ';

// Load HTML content into dompdf
$x = 1;
$status = '';
$balance_status = '';

// print_r($data[0]['next_check']);

foreach ($data as $row) {
    $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
    $notify_quantity = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['notify_quantity'];
    $generic_balance = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['balance'];
    $maintainance = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['maintainance'];
    $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
    $batch_balance = $override->getNews('batch', 'id', $row['batch_id'], 'status', 1)[0]['balance'];
    $batch_no = $row['batch_no'];
    $last_check = $row['last_check'];
    $next_check = $row['next_check'];
    $expire_date = $row['expire_date'];


    // $desiredValue = date('Y-m-d');
    // $batch = $override->getNews('batch', 'generic_id', $row['id'], 'status', 1);

    // $found = false;
    // foreach ($batch as $value) {
    //     if ($value < $desiredValue) {
    //         $found = true;
    //         break;
    //     }
    // }

    // if ($found) {
    //     echo "There is a value in the array that is less than $desiredValue.";
    // } else {
    //     echo "No values in the array are less than $desiredValue.";
    // }



    if ($row['expire_date'] <= date('Y-m-d')) {
        $ExpireClass = 'ExpiredStock';
        $expire = 'Expired';
    } else {
        $ExpireClass = 'NotExpiredStock';
        $expire = 'Valid';
    }

    if ($row['balance'] <= 0) {
        $balance = 'Out of Stock';
        $StockClass = 'outStock';
    } elseif ($row['balance'] > 0 && $row['balance'] < $row['notify_quantity']) {
        $balance = 'Running Low';
        $StockClass = 'lowStock';
    } else {
        $balance = 'Sufficient';
        $StockClass = 'sufficientStock';
    }

    if ($row['last_check'] == '') {
        $LastcheckClass = 'NotCheckedStock';
        $Lastcheck = 'Not Checked!';
    } else {
        if ($row['next_check'] <= date('Y-m-d')) {
            $NextcheckClass = 'NotCheckedStock';
            $Nextcheck = 'Not Checked!';
        } else {
            $NextcheckClass = 'CheckedStock';
            $Nextcheck = 'Checked!';
        }
    }

    // if ($row['next_check'] <= date('Y-m-d')){
    //     $checkClass = 'NotCheckedStock';
    //     $check = 'Not Checked!';
    // } else {
    //     $checkClass = 'CheckedStock';
    //     $check = 'Checked!';
    // }

    if ($_GET['group'] == 1 || $_GET['group'] == 3 || $_GET['group'] == 4) {
        $status = $Nextcheck . ' - ' . $balance . ' - ' . $expire;
    } else {
        $status = $Nextcheck . ' - ' . $balance;
    }

    if ($ExpireClass == 'ExpiredStock' || $StockClass == 'outStock' || $StockClass == 'lowStock' || $LastcheckClass == 'NotCheckedStock' || $NextcheckClass == 'NotCheckedStock') {
        $classStatus = 'outStock';
    }

    $output .= '
         <tr>
            <td colspan="1">' . $x . '</td>
            <td colspan="2">' . $generic_name . '</td>
            <td colspan="2">' . $notify_quantity . '</td>
            <td colspan="2" class="' . $StockClass . '">' . $row['balance'] . '</td>
            <td colspan="2">' . $row['batch_no'] . '</td>
            <td colspan="2" class="' . $LastcheckClass . '">' . $last_check . '</td>
            <td colspan="2" class="' . $NextcheckClass . '">' . $next_check . '</td>
            ';

    if ($_GET['group'] == 1 || $_GET['group'] == 3 || $_GET['group'] == 4) {
        $output .= '
            <td colspan="2" class="' . $ExpireClass . '">' . $expire_date . '</td>

            ';
    }
    $output .= '
            <td colspan="2" class="' . $classStatus . '">' . $status . '</td>
            <td colspan="2">' . $row['remarks'] . '</td>
        </tr>
        ';
    $x += 1;
}

$output .= '
    <tr>
        <td colspan="' . $span0 . '" align="left" style="font-size: 18px">
            <br />
            <p align="left">General Comments</p>
            <br />
        </td>
    </tr>
</table>  
    ';


// Include the menu
ob_start();
include 'menu_title.php';
$menuHtml = ob_get_clean();

// Combine the menu HTML and main content HTML
// $output = $menuHtml . $output;


$pdf->loadHtml($output);

// // Include the CSS file
// $pdf->set_option('isRemoteEnabled', true);
// $pdf->set_option('isHtml5ParserEnabled', true);
// $pdf->set_option('isPhpEnabled', true);
// $pdf->set_option('defaultMediaType', 'print');
// $pdf->set_option('defaultPaperSize', 'A4');
// $pdf->set_option('isFontSubsettingEnabled', true);
// $pdf->set_option('isJavascriptEnabled', true);
// $pdf->set_option('dpi', 300);
// $pdf->set_option('fontHeightRatio', 1.1);
// $pdf->set_option('isFontSubsettingEnabled', true);
// $pdf->set_option('isFontSubsettingFixEnabled', true);

// // Load the external CSS file
// $pdf->add_stylesheet('styles.css');


// SetPaper the HTML as PDF
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();


$canvas = $pdf->getCanvas();
$canvas->page_text(700, 560, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));


// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
