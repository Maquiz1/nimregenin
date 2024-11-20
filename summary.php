<?php

require_once 'pdf.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

if ($user->isLoggedIn()) {
    try {
        $site_data = $override->getData('site');
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

$output .= '
        <table width="100%" border="1" cellpadding="5" cellspacing="0">
            <tr>
                <td colspan="28" align="center" style="font-size: 18px">
                    <b> ' . $title . ' </b>
                </td>
            </tr>

            <tr>
                <td colspan="28" align="center" style="font-size: 18px">
                    <b>  Total REGISTERED ( ' . $Total . ' )  Total Enrolled ( ' . $enrolled_Total . ' )</b>
                </td>
            </tr>

            <tr>
                <td colspan="28">                        
                    <br />
                    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">SITE</th>
                            <th colspan="6">DATA AVAILABLE</th>
                        </tr>
                        <tr>
                            <th>100%</th>
                            <th>Less than 80%</th>
                            <th>Less Than 50%</th>
                            <th>Less than30%</th>
                            <th>0%</th>
                            <th>0%</th>
                        </tr>
                        ';

                        $x = 1;
                        $progress100 = 0;
                        $progress80 = 0;
                        $progress50 = 0;
                        $progress30 = 0;
                        $progress0 = 0;
                        $progress00 = 0;

                        foreach ($site_data as $row) {
                            $clients = $override->get('clients', 'site_id', $row['id'])[0];

                            if($clients['progress'] == 100){
                                $progress100 = $progress100++;
                            }elseif($clients['progress'] >= 80 && $client_progress < 100){
                                $progress80 = $progress80++;
                            } elseif ($clients['progress'] >= 50 && $client_progress < 80) {
                                $progress50 = $progress50++;
                            } elseif ($clients['progress'] >= 30 && $client_progress < 50) {
                                $progress30 = $progress30++;
                            } elseif ($clients['progress'] > 0 && $client_progress < 30) {
                                $progress0 = $progress0++;
                            }else{
                                $progress00 = $progress00++;
                            }


                            $output .= '
                                    <tr>
                                        <td>' . $x . '</td>
                                        <td>' . $row['name']  . '</td>
                                        <td>' . $progress100 . '</td>
                                        <td>' . $progress80 . '</td>
                                        <td>' . $progress50 . '</td>
                                        <td>' . $progress30 . '</td>
                                        <td>' . $progress0 . '</td>
                                        <td>' . $progress00 . '</td>
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
                                        <td align="right"><b>' . $all_availableDay0 . '</b></td>
                                    </tr> 
                                </table>  
                            <tr>
                    </table>    
                    ';


                    // $output .= '

                    // <table width="100%" border="1" cellpadding="5" cellspacing="0">
                    //     <tr>
                    //         <th rowspan="2">No</th>
                    //         <th rowspan="2">SITE</th>
                    //         <th colspan="5">DATA AVAILABLE</th>
                    //     </tr>
                    //     <tr>
                    //         <th>100%</th>
                    //         <th>Less than 80%</th>
                    //         <th>Less Than 50%</th>
                    //         <th>Less than30%</th>
                    //         <th>0%</th>
                    //     </tr>
                    //     ';

                        $x = 1;
                        foreach ($site_data as $row) {
                            $sites_required = $override->SiteFollowUpRequired($DATE, $row['id']);
                            $sites_available = $override->SiteFollowUpAvailable($DATE, $row['id']);
                            $sites_missing = $override->SiteFollowUpMissing($DATE, $row['id']);

                            $Day0R = $override->SiteFollowUpRequiredDay($DATE, $row['id'], 'D0');
                            $Day0A = $override->SiteFollowUpAvailableDay($DATE, $row['id'], 'D0');
                            $Day0M = $override->SiteFollowUpMissingDay($DATE, $row['id'], 'D0');


                            // $output .= '
                            //         <tr>
                            //             <td>' . $x . '</td>
                            //             <td>' . $row['name']  . '</td>
                            //             <td>' . $sites_required . '</td>
                            //             <td>' . $sites_available . '</td>
                            //             <td>' . $sites_missing . '</td>
                            //             <td>' . $Day0R . '</td>
                            //             <td>' . $Day0A . '</td>
                            //         </tr>
                            //     ';
                            // $x += 1;
                        }

                        // $output .= '
                        //             <tr>
                        //                 <td align="right" colspan="2"><b>Total</b></td>
                        //                 <td align="right"><b>' . $all_required . '</b></td>
                        //                 <td align="right"><b>' . $all_available . '</b></td>
                        //                 <td align="right"><b>' . $all_missing . '</b></td>
                        //                 <td align="right"><b>' . $all_requiredDay0 . '</b></td>
                        //                 <td align="right"><b>' . $all_availableDay0 . '</b></td>
                        //             </tr> 
                    //             </table>  
                    //         <tr>
                    // </table>    
                    // ';

                    $output .=
                    '
                    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                        <tr>
                            <td colspan="18" align="center" style="font-size: 18px">
                                <br />
                                <br />
                                <br />
                                <br />
                                <br />
                                <br />
                            </td>
                        </tr>
                    </table>    
                    ';

                    $output .= '
                            <table width="100%" border="1" cellpadding="5" cellspacing="0">
                                <tr>
                                    <td colspan="18">                        
                                        <br />
                                        <table width="100%" border="1" cellpadding="5" cellspacing="0">
                                            <td width="5%">#</td>
                                            <th width="25%">ParticipantID</th>
                                            <th width="25%">SITE</th>
                                            <th width="25%">RECORDS</th>
                                            <th width="20%">COMPLETION STATUS</th>
                                            ';

                                            // Load HTML content into dompdf
                                            $row = array();
                                            $x = 1;
                                            foreach ($data as $row) {
                                                $site = $override->get('site', 'id', $row['site_id'])[0];

                                                // $row[] = $row;

                                                // usort($row, $row['cmp']);

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
                                                            <td>' . $row['study_id']  . '</td>
                                                            <td>' . $site['name'] . '</td>
                                                            <td>
                                                                <span class="badge badge-warning right">'
                                                                . $Total_CRF_available . ' out ' . $Total_CRF_required .
                                                                '</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-warning right">'
                                                                . $client_progress .'%'.
                                                                '</span>
                                                            </td>
                                                        </tr>
                                                    ';
                                                $x += 1;
                                            }
                                        '</table>  
                                    <tr>
                                </table>  
                            <tr>
                        </table>         
                </td>
            </tr>
        </table>           
    ';



// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// SetPaper the HTML as PDF
// $pdf->setPaper('A4', 'portrait');
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
