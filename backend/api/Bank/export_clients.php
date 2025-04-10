<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);

$stmt = $item->getExportableClients(@$_GET['bank'], @$_GET['branch'], @$_GET['st'], @$_GET['end'], @$_GET['product'], @$_GET['type']);

$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$header = array_keys($data[0]);
$sheet->fromArray($header, NULL, 'A1');

// Set data rows
$sheet->fromArray($data, NULL, 'A2');

// Write to an Excel file
$writer = new Xlsx($spreadsheet);
$filename = 'export_clients_data.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit();





// $userArr = array();
// $userArr["data"] = array();
// $userArr["success"] = true;
// $userArr['statusCode'] = "200";
// $userArr['count'] = 0;

// // Fetch and write the data
// // $rowNumber = 1; // Start in the first row
// while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
// // $columnLetter = 'A'; // Start in the first column
// // foreach ($row as $columnValue) {
// // $sheet->setCellValue($columnLetter . $rowNumber, $columnValue);
// // $columnLetter++;
// // }
// // $rowNumber++;
// array_push($userArr['data'], $row);
// }

// http_response_code(200);
// echo json_encode($userArr['data']);

// Set the headers to download the file
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment; filename="exported_clients_data.xlsx"');
// header('Cache-Control: max-age=0');

// // Write the spreadsheet to the output
// $writer = new Xlsx($spreadsheet);
// $writer->save('php://output');

// exit;