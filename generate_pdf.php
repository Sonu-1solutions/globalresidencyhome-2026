<?php
require 'vendor/autoload.php';
require_once "database.php";


use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$booking_no = $_POST['booking_no'];
$booking_id = $_POST['booking_id'];
$propertamt = $_POST['propertamt'];


ob_start();
include 'pdf_content.php';
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$canvas = $dompdf->getCanvas();
$font = $dompdf->getFontMetrics()->get_font("Arial", "normal");

$canvas->page_text(
    270, 790,
    "Page {PAGE_NUM} of {PAGE_COUNT}",
    $font,
    10,
    array(0, 0, 0)
);
$dompdf->stream($booking_no."_BBA.pdf", ["Attachment" => true]);
exit;