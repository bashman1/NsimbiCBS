<?php
require_once('ResponserHelper.php');
class ReportService
{
    public $responserHelper;
    public function __construct()
    {
        $this->responserHelper = new ResponserHelper();
    }

    public function getMembershipScheduleReport($data)
    {
        return $this->responserHelper->get('Reports/get_membership_schedule_report.php', $data);
    }

    public function generateReport($data = [])
    {
        return $this->responserHelper->get('Reports/generate_report.php', $data);
    }

    public function generateIncomeStatementReport($data = [])
    {
        return $this->responserHelper->get('Reports/generate_income_statement.php', $data);
    }
    public function generateIncomeStatementReportSubAccounts($data = [])
    {
        return $this->responserHelper->get('Reports/generate_income_statement_sub_accounts.php', $data);
    }

    public function generateBalanceSheet($data = [])
    {
        return $this->responserHelper->get('Reports/generate_balance_sheet.php', $data);
    }

    public function generateBalanceSheetSubAccounts($data = [])
    {
        return $this->responserHelper->get('Reports/generate_balance_sheet_sub_accounts.php', $data);
    }

    public function generateTrialBalance($data = [])
    {
        return $this->responserHelper->get('Reports/generate_trial_balance.php', $data);
    }

    public function generateTrialBalanceSubAccounts($data = [])
    {
        return $this->responserHelper->get('Reports/generate_trial_balance_sub_accounts.php', $data);
    }
}
