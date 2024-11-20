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

        $data = $override->getNews('clients', 'status', 1, 'screened', 1);
        $data_count = $override->getCount2('clients', 'status', 1, 'screened',1, 'site_id', $user->data()->site_id);


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

$pdf = new Pdf();

$file_name = $title . '.pdf';

$output = ' ';

// if ($_GET['group'] == 2) {
    if ($data) {


    $output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="2" align="center" style="font-size: 18px">
                        <b>Invoice</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <table width="100%" cellpadding="5">
                        <tr>
                            <td width="65%">
                                To,<br />
                                    <b>RECEIVER (BILL TO)</b><br />
                                    Name : ' . $row["id"] . '<br />
                                    Billing Address : ' . $row["id"] . '<br />
                            </td>
                            <td width="35%">
                                Reverse Charge<br />
                                Invoice No : ' . $row["id"] . '<br />
                                Invoice Date : ' . $row["create_on"] . '<br />
                            </td>
                        </tr>
                    </table>
                    <br />
                    <table width="100%" border="1" cellpadding="5" cellspacing="0">
                    <tr>
                        <th rowspan="2">Sr No.</th>
                        <th rowspan="2">Product</th>
                        <th rowspan="2">Quantity</th>
                        <th rowspan="2">Price</th>
                        <th rowspan="2">Actual Amt.</th>
                        <th colspan="2">Tax (%)</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
                        <th>Rate</th>
                        <th>Amt.</th>
                    </tr>
               ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($data as $row) {
        // $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
        // $brand_name = $override->getNews('brand', 'id', $row['brand_id'], 'status', 1)[0]['name'];
        // $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
        // $staff = $override->get('user', 'id', $row['staff_id'])[0];
        // $batch_no = $row['batch_no'];


        $output .= '
                <tr>
                    <td>' . $x . '</td>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row["id"] . '</td>
                    <td align="right">' . $row['id'] . '</td>
                    <td align="right">' . $data_count . '</td>
                    <td align="right">' . $data_count . '%</td>
                    <td align="right">' . $data_count . '</td>
                    <td align="right">' . $data_count . '</td>
                </tr>
            ';

        $x += 1;
    }

    $output .= '
    <tr>
        <td align="right" colspan="4"><b>Total</b></td>
        <td align="right"><b>' . $data_count . '</b></td>
        <td>&nbsp;</td>
        <td align="right"><b>' . $data_count . '</b></td>
        <td align="right"><b>' . $data_count . '</b></td>
    </tr>
    ';

$output .= '
        </table>
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <p align="right">---------------------------------------<br />Receiver Signature</p>
            <br />
            <br />
            <br />
        </td>
    </tr>
</table>    
';
} else {

    $output .= '
    <table width="100%" border="1" cellpadding="5" cellspacing="0">

        <tr>
            <td colspan="15" align="center" style="font-size: 18px">
                <b>NIMREGENIN SUMMARY REPORT  </b>
            </td>
        </tr>
        <tr>
            <td colspan="15" align="center" style="font-size: 18px">
            <b>' . date('Y-m-d') . '</b>
        </td>
    </tr>
        <tr>
            <td colspan="15" align="center" style="font-size: 18px">
                <b>Report FOR ' . $title . ':  Total ( ' . $data_count . ' )</b>
            </td>
        </tr>

        <tr>
        <td colspan="15" align="center" style="font-size: 18px">
            <b>For Period ' . date('Y-m-d') . ' to ' . date('Y-m-d') . '</b>
        </td>
        </tr>

        <tr>
            <th colspan="1">SITE.</th>
            <th colspan="2">REGISTERED</th>
            <th colspan="2">USE NIMREGENIN</th>
            <th colspan="2">SCREENED</th>        
            <th colspan="2">CANCER ( INCLUSION )</th>
            <th colspan="2">ELIGIBLE</th>
            <th colspan="2">ENROLLED</th>
            <th colspan="2">ENROLLED</th>
        </tr>

    ';

    // Load HTML content into dompdf
    $x = 1;
    foreach ($data as $row) {
        // $generic_name = $override->getNews('generic', 'id', $row['generic_id'], 'status', 1)[0]['name'];
        // $brand_name = $override->getNews('brand', 'id', $row['brand_id'], 'status', 1)[0]['name'];
        // $category_name = $override->get('drug_cat', 'id', $row['category'])[0]['name'];
        // $staff = $override->get('user', 'id', $row['staff_id'])[0];
        // $batch_no = $row['batch_no'];


        $output .= '
        <tr>
            <td colspan="1">' . $x . '</td>
            <td colspan="2">' . $row['create_on'] . '</td>
            <td colspan="2">' . $row . '</td>
            <td colspan="2">' . $row . '</td>
            <td colspan="2">' . $row . '</td>
            <td colspan="2">' . $row['id'] . '</td>
            <td colspan="2">' . $row . '</td>
            <td colspan="2">' . $row['id'] . '</td>
        </tr>
        ';

        $x += 1;
    }

    $output .= '
    <tr>
        <td colspan="7" align="center" style="font-size: 18px">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <p align="right">----' . $user->data()->site_id . ' ' . $user->data()->site_id . '-----<br />Printed By</p>
            <br />
            <br />
            <br />
        </td>

        <td colspan="8" align="center" style="font-size: 18px">
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
}




// $output = '<html><body><h1>Hello, dompdf!' . $row . '</h1></body></html>';
$pdf->loadHtml($output);

// SetPaper the HTML as PDF
$pdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$pdf->render();

// Output the generated PDF
$pdf->stream($file_name, array("Attachment" => false));
