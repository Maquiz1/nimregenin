<?php
require_once 'php/core/init.php';

use Dompdf\Dompdf;

$pdf = new Dompdf();
$pdf->loadHtml('<h1>Hello, Dompdf!</h1>');
$pdf->setPaper('A4', 'portrait');
$pdf->render();
$pdf->stream('test.pdf', array("Attachment" => false));