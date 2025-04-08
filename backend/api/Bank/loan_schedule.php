<?php
require_once __DIR__.'../../RequestHeaders.php';

// include_once '../../config/database.php';
include_once '../../models/LoanSchedule.php';

// $database = new Database();
// $db = $database->connect();


$item = new RepaymentScheduleLib();
$data = json_decode(file_get_contents("php://input"));


$loan_data = new stdClass();

// application id
$loan_data->id = $data->id;

// annual rate
	 $loan_data->int_rate = $data->rate;
    //  period or duration
	 $loan_data->loan_period = $data->period;

		 $loan_data->loan_amount = $data->amount;
        //  date of disbursement
	$loan_data->record_date = $data->date;
    // interest method --flat -flat rate --declining - declining balance --reducing - amortization method
	$loan_data->int_method = $data->int_method;
// grace period
	 $loan_data->grace_period = $data->grace_period;

    //  frequency -- d daily , m- monthly , w -weekly , y-yearly
		$loan_data->period_type = $data->frequency;

        // DAYS for daily , WEEKS  for weekly , MONTHS for Monthly , YEARS for yearly
	 $loan_data->frequency_type = $data->ftype;
    //  similar to duration
		 $loan_data->frequency = $data->period;
        //  grace type  pay_i , pay_none ,pay_p
		$loan_data->grace_period_type = $data->grace_type;
        // whether to round off to nearest currency --100
		 $loan_data->refineSchedule =$data->refine;

$stmt = $item->generateLoanSchedule($loan_data);


    // $userArr = array();
    // $userArr["data"] = array();
    // $userArr["success"] = true;
    // $userArr['statusCode'] = "200";

    //     array_push($userArr['data'], $stmt);

    http_response_code(200);
    echo json_encode($stmt);

