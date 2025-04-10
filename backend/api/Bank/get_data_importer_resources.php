<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../models/Loan.php';
include_once '../../models/Bank.php';
require_once '../ApiResponser.php';


$database = new Database();
$db = $database->connect();

$ApiResponse = new ApiResponser();

$data = json_decode(file_get_contents("php://input"), true);

try {
    $response = [];
    $loan = new Loan($db);
    $loan->createdById = @$data['bank_id'];
    $loan->branchId = @$data['branch'];
    $loan_products = @$data['bank_id'] ? $loan->getAllLoanProducts() : $loan->getAllBranchLoanProducts();


    $bank = new Bank($db);
    $bank->bank = @$data['bank_id'];
    $bank->id = @$data['bank_id'] ?? @$data['branch'];
    $bank->branch = @$data['branch'];
    $saving_products = @$data['bank_id'] ? $bank->getBankSavingAccount() : $bank->getBranchSavingAccounts();
    $credit_officers = @$data['bank_id'] ? $bank->getAllBankStaffs() : $bank->getAllBranchStaffs();
    $branches = @$data['bank_id'] ? $bank->getAllBranches()->fetchAll(PDO::FETCH_ASSOC) : $bank->getBranchDetails($bank->branch);

    // $branch = $bank->getBranchDetails($data->branch_id);

    $response['loan_products'] = $loan_products->fetchAll(PDO::FETCH_ASSOC);
    $response['saving_products'] = $saving_products->fetchAll(PDO::FETCH_ASSOC);
    $response['credit_officers'] = $credit_officers->fetchAll(PDO::FETCH_ASSOC);

    if ($bank->branch) {
        $branches = [$branches];
    }
    $response['branches'] = $branches;

    echo $ApiResponse::SuccessResponse($response);
} catch (\Throwable $th) {
    echo $ApiResponse::ErrorResponse($th->getMessage());
    // echo $ApiResponse::ErrorResponse("Error");
}
