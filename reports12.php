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

// Load HTML content into dompdf
$x = 1;
foreach ($clients as $client) {
    if ($client['eligible'] == 1) {
        $eligible = 'ELIGIBLE';
    } else {
        $eligible = 'NOT ELIGIBLE';
    }

    if ($client['site_id'] == 1) {
        $site = 'MNH';
    } elseif ($client['site_id'] == 2) {
        $site = 'ORCI';
    } else {
        $site = 'Not Answered';
    }


    $screening = $override->get('screening', 'client_id', $client['id'])[0];


    if ($screening['consented'] == 1) {
        $consent = 'Yes';
    } elseif ($screening['consented'] == 2) {
        $consent = 'No';
    } else {
        $consent = 'Not Answered';
    }


    if ($screening['consented_nimregenin'] == 1) {
        $consented_nimregenin = 'Yes';
    } elseif ($screening['consented_nimregenin'] == 2) {
        $consented_nimregenin = 'No';
    } else {
        $consented_nimregenin = 'Not Answered';
    }
    $output .= '
         <tr>
            <td colspan="1">' . $x . '</td>
            <td colspan="3">' . $client['clinic_date'] . '</td>
            <td colspan="2">' . $client['study_id'] . '</td>
            <td colspan="2">' . $client['pt_type'] . '</td>
            <td colspan="2">' . $client['treatment_type'] . '</td>
            <td colspan="2">' . $client['previous_date'] . '</td>
            <td colspan="2">' . $client['total_cycle'] . '</td>
            <td colspan="2"  class="' . $classStatus . '">' . $consented_nimregenin . '</td>
            <td colspan="3">' . $eligible . '</td>
            <td colspan="2">' . $consent . '</td>
            <td colspan="2">' . $site . '</td>
        </tr>
        ';

    $x += 1;
}
$output .= '
    <tr>
        <td colspan="' . $span1 . '" align="center" style="font-size: 18px">
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

        <td colspan="' . $span2 . '" align="center" style="font-size: 18px">
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

$canvas = $pdf->getCanvas();
$canvas->page_text(700, 560, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));

