<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $Total = $override->getCount('clients', 'status', 1);
        $enrolled_Total = $override->getCount1('clients', 'status', 1, 'enrolled', 1);
        $data = $override->getNews('clients', 'status', 1, 'enrolled', 1);
    } catch (Exception $e) {
        die($e->getMessage());
    }
} else {
    Redirect::to('index.php');
}


$title = 'NIMREGENIN REPORT_' . date('Y-m-d');

$pdf = new Pdf();

// $title = 'NIMREGENIN SUMMARY REPORT_'. date('Y-m-d');
$file_name = $title . '.pdf';

$output = ' ';

// if ($_GET['group'] == 2) {
if ($data) {

    $output .= '
            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>DATE  ' . date('Y-m-d') . '</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>' . $title . '</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="18" align="center" style="font-size: 18px">
                        <b>Total Registered ( ' . $Total . ' ):  Total Enrolled( ' . $enrolled_Total . ' )</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="18">                        
                        <br />
                        <table width="100%" border="1" cellpadding="5" cellspacing="0">
                            <td width="5%">#</td>
                            <th width="50%">ParticipantID</th>
                            <th width="25%">SITE</th>
                            <th width="20%">STATUS</th>
            ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($data as $row) {
        $site = $override->get('site', 1, 'id', $row['site_id'])[0];


        $crf1 = $override->countData('crf1', 'patient_id', $row['id'], 'status', 1);
        $crf2 = $override->countData('crf2', 'patient_id', $row['id'], 'status', 1);
        $crf3 = $override->countData('crf3', 'patient_id', $row['id'], 'status', 1);
        $crf4 = $override->countData('crf4', 'patient_id', $row['id'], 'status', 1);
        $crf5 = $override->countData('crf5', 'patient_id', $row['id'], 'status', 1);
        $crf6 = $override->countData('crf6', 'patient_id', $row['id'], 'status', 1);
        $crf7 = $override->countData('crf7', 'patient_id', $row['id'], 'status', 1);

        $Total_visit_available = 0;
        $Total_CRF_available = 0;
        $Total_CRF_required = 0;
        $progress = 0;

        $Total_visit_available = intval($override->getCountNot('visit', 'client_id', $row['id'], 'visit_code', 'AE', 'END'));
        if ($Total_visit_available < 1) {
            $Total_visit_available = 0;
            $Total_CRF_available = 0;
            $Total_CRF_required = 0;
        } elseif ($Total_visit_available == 1) {
            $Total_visit_available = intval($Total_visit_available);

            $Total_CRF_available = intval(intval($crf1) + intval($crf2) + intval($crf3) + intval($crf4) + intval($crf5) + intval($crf6) + intval($crf7));

            $Total_CRF_required = intval(intval($Total_visit_available) * 5);
        } elseif ($Total_visit_available > 1) {
            $Total_visit_available = intval(intval($Total_visit_available) - 1);

            $Total_CRF_available = intval(intval($crf2) + intval($crf3) + intval($crf4) + intval($crf7));


            $Total_CRF_required = intval((intval($Total_visit_available) * 4) + 6);
        }

        $client_progress = intval(intval($Total_CRF_available) / intval($Total_CRF_required) * 100);

        $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['name']  . '</td>
                    <td>' . $site['name'] . '</td>
                    <td>
                    <span class="badge badge-warning right">'
                    . $Total_CRF_available . ' out ' . $Total_CRF_required .
                    '</span>
                    <span class="badge badge-warning right">'
                    . $client_progress .'%'.
                    '</span>
                    </td>
                </tr>
            ';
        $x += 1;
    }

    $output .= '
           
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
