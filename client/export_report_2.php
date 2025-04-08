<?php
// var_dump($_REQUEST);
require_once('../backend/config/session.php');
require_once('./includes/functions.php');
require_once './includes/constants.php';

require __DIR__ . '../../vendor/autoload.php';

ob_start();
// use Spipu\Html2Pdf\Html2Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;

// if($_REQUEST[''])

// var_dump($_REQUEST['exportFile']);

try {
    if (@$_REQUEST['useFile']) {
        include(@$_REQUEST['exportFile'] . '.php');
    } else {
        include(@$_REQUEST['exportFile'] . '_export.php');
    }
    $content = ob_get_clean();
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($content);
    $dompdf->setPaper('legal', @$_REQUEST['orientation'] ?? 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream('doc', array("Attachment" => false));
} catch (\Throwable $th) {
    echo $th->getmessage();
}
