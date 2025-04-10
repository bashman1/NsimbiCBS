<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/ClientReport.php';
include_once '../../models/Loan.php';
include_once '../../models/LoanReport.php';
include_once '../../models/TransactionReport.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"));
// echo 20000;
// return;
$ApiResponser = new ApiResponser();
$database = new Database();
$db = $database->connect();
$handler = new Handler();

$reportModel = $data->ReportModel;
$reportModelMethod = $data->ReportModelMethod;

try {
    $report_object = new $reportModel($db);
    $report_object->bankId = @$data->bank_id;
    $report_object->branch = @$data->branch ?? @$data->branchId;
    $report_object->filter_branch_id = @$data->branchId ?? @$data->branch;
    $report_object->filter_gender = @$data->gender;
    $report_object->filter_actype = @$data->actype;
    $report_object->is_client_type = @$data->is_client_type;
    $report_object->filter_client_type = @$data->client_type;
    $report_object->filter_start_date = @$data->start_date;
    $report_object->filter_end_date = @$data->end_date;
    $report_object->filter_district = @$data->district;
    $report_object->filter_subcounty = @$data->region;
    $report_object->filter_parish = @$data->parish;
    $report_object->filter_village = @$data->village;


    $report_object->filter_loan_status = @$data->loan_status;
    $report_object->filter_payment_from = @$data->payment_from;
    $report_object->filter_payment_to = @$data->payment_to;
    $report_object->filter_loan_product_id = @$data->loan_product_id;
    $report_object->filter_loan_officer_id = @$data->loan_officer_id;

    $report_object->filter_is_loan_arrears = @$data->is_loan_arrears;
    $report_object->filter_lpid = @$data->lpid;

    $report_object->filter_par_type = @$data->par_type;
    $report_object->filter_bankk = @$data->bankk;
    $report_object->filter_branch = @$data->branch;
    $report_object->filter_as_at_date = @$data->end_date;

    $report_object->filter_is_ageing_report = @$data->is_ageing_report;

    $report_object->filter_disbursement_start_date = @$data->disbursement_start_date;
    $report_object->filter_disbursement_end_date = @$data->disbursement_end_date;

    $report_object->filter_authorized_by_id = @$data->authorized_by_id;
    $report_object->filter_credit_officer = @$data->credit_officer;
    $report_object->filter_acid = @$data->acid;

    $report_object->filter_transaction_start_date = @$data->transaction_start_date;
    $report_object->filter_transaction_end_date = @$data->transaction_end_date;

    $report_object->is_savings_report = @$data->is_savings_report;
    $report_object->is_loan_report = @$data->is_loan_report;
    $report_object->is_loan_report2 = @$data->is_loan_report2;
    $report_object->is_disburse_report = @$data->is_disburse_report;
    $report_object->is_expense_report = @$data->is_expense_report;

    $report_object->filter_transaction_type = @$data->transaction_type;
    $report_object->filter_reg_renew = @$data->reg_renew;
    $report_object->filter_days_arrears = @$data->days_arrears;


    // $report_object->$db();
    $records = $report_object->$reportModelMethod();

    if (@$data->is_credit_officers_report) {
        $loan = new Loan($db);
        $records = array_map(function ($record) use ($loan) {
            $record['guarantors'] = $loan->getLoanGuarantorsText($record['loan_no']) ?? '';
            return $record;
        }, $records);
    }

    echo $ApiResponser::SuccessResponse($records);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
