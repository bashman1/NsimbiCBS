<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$item->bank = $_GET['bank'];
$item->branch = $_GET['branch'];
$item->user = $_GET['user'];



$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";

$deposits = $item->user == '' ? 0 : $item->getUserTotalDeposits();
$withdraws = $item->user == '' ? 0 : $item->getUserTotalWithdraws();
$disbs = $item->user == '' ? 0 : $item->getUserTotalDisbursements();
$agent_deposits = $item->user == '' ? 0 : $item->getUserTotalPendingDeposits();
$agent_repayments = $item->user == '' ? 0 : $item->getUserTotalPendingRepayments();
$loan_rps = $item->user == '' ? 0 : $item->getUserTotalRepayments();
$cash_balance = $item->user == '' ? 0 : $item->getUserCashBalance();
$expenses = $item->user == '' ? 0 : $item->getUserTotalExpenses();
$incomes = $item->user == '' ? 0 : $item->getUserTotalIncomes();
$liabilities = $item->user == '' ? 0 : $item->getUserTotalLiabilities();
$assets = $item->user == '' ? 0 : $item->getUserTotalAssets();
$shares_daily = $item->user == '' ? 0 : $item->getUserTotalSharePurchases();

$use_deposits = $item->bank == '' ? $deposits : $item->getBankDepositsToday();
$use_withdraws = $item->bank == '' ? $withdraws : $item->getBankWithdrawsToday();
$use_disbs = $item->bank == '' ? $disbs : $item->getBankDisbursementsToday();
$use_agds = $item->bank == '' ? $agent_deposits : $item->getBankAgentDepositsToday();
$use_agrs = $item->bank == '' ? $agent_repayments : $item->getBankAgentRepaymentsToday();
$use_loan_rps = $item->bank == '' ? $loan_rps : $item->getBankRepaymentsToday();
$use_expenses = $item->bank == '' ? $expenses : $item->getBankExpensesToday();
$use_incomes = $item->bank == '' ? $incomes : $item->getBankIncomesToday();
$use_liabilities = $item->bank == '' ? $liabilities : $item->getBankLiabilitiesToday();
$use_assets = $item->bank == '' ? $assets : $item->getBankAssetsToday();
$use_cash_balance = $item->bank == '' ? $cash_balance : $item->getBankCashBalances();

$u = array(
    "clients" => $item->bank == '' ? number_format($item->getTotalBranchClients2()) : number_format($item->getTotalBankClients2()),
    "shares_daily" =>  $shares_daily,
    "dormant_accs" => 0,
    "inv_due" => number_format($item->bank == '' ? $item->getTotalBranchIntPaidFixedDeposits() : $item->getTotalBankIntPaidFixedDeposits()),
    "mature_fixed" => number_format($item->bank == '' ? $item->getTotalBranchMaturedFixedDeposits() : $item->getTotalBankMaturedFixedDeposits()),
    "loan_arrears" =>  number_format($item->bank == '' ? $item->getTotalBranchLoanArrears() : $item->getTotalBankLoanArrears()),
    "loan_arrears_amount" =>  number_format($item->bank == '' ? $item->getTotalBranchLoanAmountArrears() : $item->getTotalBankLoanAmountArrears()),
    "int_waived" =>  number_format($item->bank == '' ? $item->getTotalBranchLoanAmountWaived() : $item->getTotalBankLoanAmountWaived()),
    "amount_due" =>  number_format($item->bank == '' ? $item->getTotalBranchLoanAmountDue() : $item->getTotalBankLoanAmountDue()),
    "int_collected" =>  $item->bank == '' ? number_format($item->getTotalBranchLoanRepaymentsInterest()) : number_format($item->getTotalBankLoanRepaymentsInterest()),
    "individuals" => number_format($item->bank == '' ? $item->getTotalBranchTypeClients2('individual') : $item->getTotalBankTypeClients2('individual')),
    "ind_daily" => number_format($item->getTotalUserTypeClients('individual')),
    "group_daily" => number_format($item->getTotalUserTypeClients('group')),
    "inst_daily" => number_format($item->getTotalUserTypeClients('institution')),
    "sms" => $item->bank == '' ? $item->getTotalBranchSMSBanking() : $item->getTotalBankSMSBanking(),
    "mobile_banking" => $item->bank == '' ? $item->getTotalBranchMobileBanking() : $item->getTotalBankMobileBanking(),
    "birth_days" => $item->bank == '' ? $item->getTotalBranchBirthDays() : $item->getTotalBankBirthDays(),
    "groups" => number_format($item->bank == '' ? $item->getTotalBranchTypeClients2('group') : $item->getTotalBankTypeClients2('group')),
    "institutions" => number_format($item->bank == '' ? $item->getTotalBranchTypeClients2('institution') : $item->getTotalBankTypeClients2('institution')),
    "shares" => number_format($item->bank == '' ? $item->getTotalBranchShareHolders() : $item->getTotalBankShareHolders()),

    "over_drafts" => number_format($item->bank == '' ? $item->getTotalBranchOverDrafts() : $item->getTotalBankOverDrafts()),
    "share_amount" => number_format($item->bank == '' ? $item->getTotalBranchShareAmount() : $item->getTotalBankShareAmount()),
    "active_loans" => $item->bank == '' ? $item->getTotalBranchLoans() : $item->getTotalBankLoans(),

    "debtors_due" => $item->bank == '' ? $item->getTotalBranchDebtorsDue() : $item->getTotalBankDebtors(),
    "creditors_due" => $item->bank == '' ? $item->getTotalBranchCreditorsDue() : $item->getTotalBankCreditors(),


    "active_credit" => $item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(2),
    "await_credit" => $item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(1),
    "due_credit" => $item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(3),
    "apply_credit" => $item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(0),
    "overdue_credit" => number_format($item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(4)),
    "overdue_credit_amount" => number_format($item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(99)),
    "portifolio_credit" => number_format($item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(92)),
    "portifolio_principal" => number_format($item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(95)),
    "portifolio_interest" => number_format($item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(96)),
    "closed_credit" => $item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(5),
    "declined_credit" => $item->user == '' ? 0 : $item->getCreditOfficerStatusLoans(6),
    "due_loans" => $item->bank == '' ? $item->getTotalBranchStatusLoans(3) : $item->getTotalBankStatusLoans(3),
    "cleared_loans" => $item->bank == '' ? $item->getTotalBranchStatusLoans(5) : $item->getTotalBankStatusLoans(5),
    "pending_loans" => $item->bank == '' ? $item->getTotalBranchStatusLoans(0) : $item->getTotalBankStatusLoans(0),
    "pending_loan_amount" => $item->bank == '' ? $item->getTotalBranchStatusLoanAmounts(0) : $item->getTotalBankStatusLoanAmounts(0),
    "approved_loans" => $item->bank == '' ? $item->getTotalBranchStatusLoans(1) : $item->getTotalBankStatusLoans(1),
    "repayments" =>  $item->bank == '' ? number_format($item->getTotalBranchLoanRepayments()) : number_format($item->getTotalBankLoanRepayments()),
    "portifolio" => $item->bank == '' ? number_format($item->getTotalBranchLoanPortifolio()) : number_format($item->getTotalBankLoanPortifolio()),
    "deposits" => number_format($use_deposits),
    "disb_today" => number_format($use_disbs),
    "agent_deposits" => number_format($use_agds),
    "agent_repayments" => number_format($use_agrs),
    "loan_rps" => number_format($use_loan_rps),
    "sav_bal" => number_format($item->bank == '' ? $item->getTotalBranchClientsSavingBalances() : $item->getTotalBankClientsSavingBalances()),
    "p_inv" => number_format($item->bank == '' ? $item->getTotalBranchFixedDeposits() : $item->getTotalBankFixedDeposits()),
    "dec_prof" => number_format($item->bank == '' ? $item->getTotalBranchFixedDepositsCount() : $item->getTotalBankFixedDepositsCount()),
    "inv_cap" => number_format($item->bank == '' ? $item->getTotalBranchClientsSavingBalancesInv() : $item->getTotalBankClientsSavingBalancesInv()),
    "freezed_bal" => number_format($item->bank == '' ? $item->getTotalBranchClientsFreezedBalances() : $item->getTotalBankClientsFreezedBalances()),
    "progres_label" => $item->bank == '' ? $item->getTotalBranchLoanCollectionProgres() : $item->getTotalBankLoanCollectionProgres(),
    "withdraws" => number_format($use_withdraws),
    "online_withdraws" => number_format($item->bank == '' ? $item->getBranchOnlineWithdrawsToday() : $item->getBankOnlineWithdrawsToday()),
    "online_deposits" => number_format($item->bank == '' ? $item->getBranchOnlineDepositsToday() : $item->getBankOnlineDepositsToday()),
    "online_bal" => 0,
    "expenses" => number_format($use_expenses),
    "incomes" => number_format($use_incomes),
    "liabilities" => number_format($use_liabilities),
    "assets" => number_format($use_assets),
    "cash_balance" =>  number_format($use_cash_balance),
    "cash_assigned" => number_format($use_cash_balance + $use_withdraws + $use_expenses - $use_deposits  - $use_incomes),
);


array_push($userArr['data'], $u);


http_response_code(200);
echo json_encode($userArr);
