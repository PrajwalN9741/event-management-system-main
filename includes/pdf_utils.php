<?php
// includes/pdf_utils.php
require_once 'vendor/autoload.php'; // Dompdf should be here
use Dompdf\Dompdf;
use Dompdf\Options;

function generate_quotation_pdf($event, $inventory_usages, $output_path) {
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    
    $dompdf = new Dompdf($options);
    
    // Preparation for template
    $flowers = json_decode($event['flower_items_json'] ?: '[]', true);
    $flower_total = 0;
    foreach ($flowers as $f) $flower_total += (float)$f['price'] * (int)$f['qty'];

    $inventory_total = 0;
    foreach ($inventory_usages as $ui) $inventory_total += $ui['quantity_used'] * $ui['price_per_unit'];

    $grand_total = $flower_total + $inventory_total;
    
    // Capture template output
    ob_start();
    include 'templates/quotation/quotation_pdf.php';
    $html = ob_get_clean();
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    file_put_contents($output_path, $dompdf->output());
    return $output_path;
}
?>
