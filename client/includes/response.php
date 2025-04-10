<?php
include('constants.php');
require_once('ResponserHelper.php');
class Response
{
    public $responserHelper;
    public function __construct()
    {
        $this->responserHelper = new ResponserHelper();
    }

    function getMySaccos($phone)
    {
        $endpoint = "Bank/get_my_saccos.php?phone=" . $phone;
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getClientTrxns($uid, $range)
    {
        $endpoint = "Bank/get_client_trxns.php?uid=" . $uid . "&range=" . $range;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        } else {
            return '';
        }
    }
    function verifyClient($cid, $mpin)
    {
        $endpoint = "Bank/verify_client_portal.php?cid=" . $cid . "&mpin=" . $mpin;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        } else {
            return '';
        }
    }

    function verifyClient2($mpin, $phone, $acc)
    {
        $endpoint = "Bank/verify_client_mpin.php?mpin=" . $mpin . "&phone=" . $phone . '&acc=' . $acc;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        } else {
            return '';
        }
    }

    function verifyClient3($cid, $uid, $mpin)
    {
        $endpoint = "Bank/set_client_mpin.php?cid=" . $cid . "&uid=" . $uid . '&mpin=' . $mpin;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }
    function getClientFeesTrxns($uid, $range)
    {
        $endpoint = "Bank/get_client_fees_trxns.php?uid=" . $uid . "&range=" . $range;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        } else {
            return '';
        }
    }

    function setClientDefaultMpin($phone, $acc_no)
    {
        $endpoint = "Bank/set_client_default_mpin.php?phone=" . $phone . "&acc=" . $acc_no;
        $url = BACKEND_BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function getClientPortalDetails($cid, $uid)
    {
        $endpoint = "Bank/get_portal_client_details.php?cid=" . $cid . "&uid=" . $uid;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        } else {
            return '';
        }
    }

    function getClientSaccoDetails($cid)
    {
        $endpoint = "Bank/get_sacco_details.php?cid=" . $cid;
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getTotalClients($bankId, $branchId)
    {
        $endpoint = "Bank/get_total_clients.php";
        $url = BACKEND_BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankId,
            'branch'      => $branchId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getOccupationOptions($bank, $branch, $type)
    {
        $endpoint = "Bank/get_occupation_options.php?branch=" . $branch . '&bank=' . $bank . '&type=' . $type;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getTotalSMSClients($bankId, $branchId)
    {
        $endpoint = "Bank/get_total_sms_clients.php";
        $url = BACKEND_BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankId,
            'branch'      => $branchId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getBranchSMSBalance($bank, $branchId)
    {
        $endpoint = "Bank/get_branch_sms_balance.php";
        $url = BACKEND_BASE_URL . $endpoint;

        $data = array(
            'branch'      => $branchId,
            'bank'      => $bank,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getTotalSystemSMSClients()
    {
        $endpoint = "Bank/get_total_system_sms_clients.php";
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getTotalSystemClients()
    {
        $endpoint = "Bank/get_total_system_clients.php";
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getBankSMSWalletDetails($branchId)
    {
        $endpoint = "Bank/get_sms_wallet_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'branch'      => $branchId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getBankSMSDetails($bankId)
    {
        $endpoint = "Bank/get_bank_sms_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }
    function subscribe_bank_sms($bankId, $st, $type)
    {
        $endpoint = "Bank/subscribe_bank_sms.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankId,
            'st'      => $st,
            'type'      => $type,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function getSystemSMSWalletDetails()
    {
        $endpoint = "Bank/get_overall_sms_wallet_details.php";
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getMainAccounts()
    {
        $endpoint = "Bank/get_main_accounts.php";
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getAllMainAccounts()
    {
        $response = $this->responserHelper->post('Bank/get_all_main_accounts.php');
        return @$response['data'];
    }

    function getTotalLoans($bankId, $branchId)
    {
        $endpoint = "Bank/get_total_loans.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankId,
            'branch'      => $branchId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getAllSubAccounts($branch, $bank)
    {
        $endpoint = "Bank/get_all_sub_accs.php?bank=" . $bank . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function resendEmail($uid, $email)
    {
        $endpoint = "Bank/resend_mail.php?id='.$uid.'&email='.$email.'";
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }


    function getSubAccounts2($branchId, $bankId)

    {
        $endpoint = "Bank/get_all_bank_accounts.php?branch=" . $branchId . '&bank=' . $bankId;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getChartAccountsLedger($bank, $branch, $acc)

    {
        $endpoint = "Bank/get_ledger_chart_accounts.php?branch=" . $branch . '&bank=' . $bank . '&acc=' . $acc;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getAccountTrxns($bank, $branch, $acc)

    {
        $endpoint = "Bank/get_ledger_chart_account_trxns.php?branch=" . $branch . '&bank=' . $bank . '&acc=' . $acc;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getAccountDetails($account_id)

    {
        $endpoint = "Accounting/get_account_details.php";
        $url = BASE_URL . $endpoint;



        $data = array(
            'account_id'      => $account_id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getSubSubAccounts($aid)
    {
        $endpoint = "Accounting/get_all_sub_accounts.php?id=" . $aid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getLoanPortifolio($bankId, $branchId)
    {
        $endpoint = "Bank/get_total_loan_portifolio.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankId,
            'branch'      => $branchId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getTotalLoanRepayments($bankId, $branchId)
    {
        $endpoint = "Bank/get_total_loan_repayments.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankId,
            'branch'      => $branchId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }
    function createDepositAgent($data)
    {
        // $endpoint = "Bank/create_loan_repay.php";
        $response = $this->responserHelper->get('field_agents/create_deposit_agent_2.php', $data);
        return @$response;
    }

    function getClientDetails($id)
    {
        $endpoint = "Bank/get_client_details.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }
    function getClientDetailswithCID($id)
    {
        $endpoint = "Bank/get_client_details_with_cid.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function setIndividualAttachments($uid, $other_name, $pass_name, $sign_name, $fing_name)
    {
        $endpoint = "Bank/update_individual_client_attachs.php?id=" . $uid . '&other=' . $other_name . '&pass=' . $pass_name . '&sign=' . $sign_name . '&fing=' . $fing_name;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return true;
    }

    function setGroupAttachments($uid, $other_name, $pass_name, $sign_name, $fing_name, $fing_name2, $fing_name3)
    {
        $endpoint = "Bank/update_group_client_attachs.php?id=" . $uid . '&other=' . $other_name . '&pass=' . $pass_name . '&sign=' . $sign_name . '&fing=' . $fing_name . '&fing_name2=' . $fing_name2 . '&fing_name3=' . $fing_name3;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return true;
    }


    function getOverDraftProducts($bank, $branch)
    {
        $endpoint = "Bank/get_over_draft_products.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }
    function getBankSharesDetails($bank, $branch)
    {
        $endpoint = "Bank/get_bank_share_details.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getBankFDDetails($bank, $branch)
    {
        $endpoint = "Bank/get_bank_fd_details.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return @$data['data'];
    }

    function getBankShareValue($bank, $branch)
    {
        $endpoint = "Bank/get_bank_share_value.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }


    function getBankSMSAcids($bank, $branch)
    {
        $endpoint = "Bank/get_bank_sms_acids.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getBankFDAcids($branch)
    {
        $endpoint = "Bank/get_bank_fd_acids.php?branch=" . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function updateShareValue($amount, $bank, $acid)
    {
        $endpoint = "Bank/update_bank_share_value.php?bank=" . $bank . '&amount=' . $amount . '&acid=' . $acid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function getAllCashAccounts($bankid, $branchid)
    {
        $endpoint = "Bank/get_bank_cash_accounts.php?bank=" . $bankid . '&branch=' . $branchid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }
    function getAllBranchCashAccounts($bankid, $branchid)
    {
        $endpoint = "Bank/get_branch_cash_accounts.php?bank=" . $bankid . '&branch=' . $branchid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getAllBranchReserveAccounts($bankid, $branchid)
    {
        $endpoint = "Bank/get_branch_reserve_accounts.php?bank=" . $bankid . '&branch=' . $branchid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getAllBankAccounts($bankid, $branchid)
    {
        $endpoint = "Bank/get_bank_bank_accounts.php?bank=" . $bankid . '&branch=' . $branchid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getAllBankMobileAccounts($bankid, $branchid)
    {
        $endpoint = "Bank/get_bank_mobile_accounts.php?bank=" . $bankid . '&branch=' . $branchid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }
    function getRightSchedule($rate, $period, $amount, $date, $method, $grace_period, $frequency, $ftype, $grace_type, $refine)
    {
        $endpoint = "Bank/loan_schedule.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => 1,
            "rate" => $rate,
            "period" => $period,
            "amount" => $amount,
            "date" => $date,
            "int_method" => $method,
            "grace_period" => $grace_period,
            "frequency" => $frequency,
            "ftype" => $ftype,
            "grace_type" => $grace_type,
            "refine" => $refine
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data;
    }

    function rescheduleLoan($data)
    {
        $response = $this->responserHelper->post('Bank/reschedule_loan.php', $data);
        return @$response;
    }

    function createSubAccount($name, $cname, $descr, $branchId, $bankId, $userId)
    {
        $endpoint = "Bank/create_sub_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "name" => $name,
            "cname" => $cname,
            "descr" => $descr,
            "branchId" => $branchId,
            "bankid" => $bankId,
            "userid" => $userId
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function createSubSubAccount($details)
    {
        $endpoint = "Accounting/create_sub_sub_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "data" => $details,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function createExpense($heading, $amount, $main_acc, $pay_method, $bank_acc, $cash_acc, $account_id, $cheque_no, $date_of_p, $comment, $bankId, $branchId, $userId)
    {
        $endpoint = "Bank/create_expense.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "descri" => $heading,
            "amount" => $amount,
            "exp_acc" => $main_acc,
            "pay_method" => $pay_method,
            "bank_acc" => $bank_acc,
            "cash_acc" => $cash_acc,
            "account_id" => $account_id,
            "cheque" => $cheque_no,
            "date_of_p" => $date_of_p,
            "comment" => $comment,
            "branchId" => $branchId,
            "bankId" => $bankId,
            "userId" => $userId
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function createCapital($heading, $amount, $main_acc, $pay_method, $bank_acc, $cash_acc, $account_id, $cheque_no, $date_of_p, $comment, $bankId, $branchId, $userId)
    {
        $endpoint = "Bank/create_capital.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "descri" => $heading,
            "amount" => $amount,
            "exp_acc" => $main_acc,
            "pay_method" => $pay_method,
            "bank_acc" => $bank_acc,
            "cash_acc" => $cash_acc,
            "account_id" => $account_id,
            "cheque" => $cheque_no,
            "date_of_p" => $date_of_p,
            "comment" => $comment,
            "branchId" => $branchId,
            "bankId" => $bankId,
            "userId" => $userId
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }


    function getBanks()
    {
        $endpoint = "Bank/get_all_banks.php";
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return $data['data'];
        } else {
            return '';
        }
    }

    function createIncome($heading, $amount, $main_acc, $pay_method, $bank_acc, $cash_acc, $account_id, $cheque_no, $date_of_p, $comment, $bankId, $branchId, $userId)
    {
        $endpoint = "Bank/create_income.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "descri" => $heading,
            "amount" => $amount,
            "exp_acc" => $main_acc,
            "pay_method" => $pay_method,
            "bank_acc" => $bank_acc,
            "cash_acc" => $cash_acc,
            "account_id" => $account_id,
            "cheque" => $cheque_no,
            "date_of_p" => $date_of_p,
            "comment" => $comment,
            "branchId" => $branchId,
            "bankId" => $bankId,
            "userId" => $userId
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }


    function registerAsset($details)
    {
        $endpoint = "Bank/register_asset.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "data" => $details,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function registerSatelliteTrxn($details)
    {
        $endpoint = "Bank/register_satellite_trxn.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "data" => $details,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function registerLiability($details)
    {
        $endpoint = "Bank/register_liability.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "data" => $details,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function registerLiability2($details)
    {
        $endpoint = "Bank/register_liability2.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "data" => $details,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }


    function purchaseSMS($bid, $amount, $pay_method, $branch)
    {
        $endpoint = "Bank/manual_sms_purchase.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "bid" => $bid,
            "amount" => $amount,
            "pay_method" => $pay_method,
            "branch" => $branch,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function approvepurchaseSMS($id)
    {
        $endpoint = "Bank/approve_sms_purchase.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function subscribe_client_sms($id)
    {
        $endpoint = "Bank/sms_subscribe_client.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function subscribe_to_sms_types($id)
    {
        $endpoint = "Bank/sms_subscribe_to_all_types.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function deleteBank($id, $bank, $branch, $uid)
    {
        $endpoint = "Bank/delete_bank.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
            "bank" => $bank,
            "branch" => $branch,
            "uid" => $uid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }


    function reverseClosedLoan($id, $bank, $branch, $uid)
    {
        $endpoint = "Bank/reverse_closed_loan.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
            "bank" => $bank,
            "branch" => $branch,
            "uid" => $uid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }


    function deleteCashTransfer($id, $bank, $branch, $uid)
    {
        $endpoint = "Bank/delete_cash_transfer.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
            "bank" => $bank,
            "branch" => $branch,
            "uid" => $uid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function getAccBalValue($lpid, $acid, $bid, $start, $end, $type = 0)
    {
        $endpoint = "Bank/get_acc_bal_val.php?lpid=" . $lpid . "&bid=" . $bid . "&acid=" . $acid . "&type=" . $type . "&start=" . $start . "&end=" . $end;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => '',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['bal'] ?? 0;
    }
    function getAccBalValue2($lpid, $acid, $bid, $type = 0)
    {
        $endpoint = "Bank/get_acc_bal_val2.php?lpid=" . $lpid . "&bid=" . $bid . "&acid=" . $acid . "&type=" . $type;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => '',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['bal'] ?? 0;
    }
    function deleteBranch($id, $bank, $branch, $uid)
    {
        $endpoint = "Bank/delete_branch.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
            "bank" => $bank,
            "branch" => $branch,
            "uid" => $uid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function editSMSType($sid, $body, $charge, $charge_to, $bid)
    {
        $endpoint = "Bank/edit_sms_type.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "sid" => $sid,
            "body" => $body,
            "charge" => $charge,
            "charge_to" => $charge_to,
            "bid" => $bid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }


    function setSMSACIDS($income, $exp, $bid)
    {
        $endpoint = "Bank/set_sms_acids.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "income" => $income,
            "exp" => $exp,
            "bank" => $bid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function setFDACIDS($income, $exp, $wht, $bid)
    {
        $endpoint = "Bank/set_fd_acids.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "income" => $income,
            "exp" => $exp,
            "wht" => $wht,
            "bank" => $bid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function addSMSType($body, $charge, $charge_to, $charge_on, $name, $uid, $bid)
    {
        $endpoint = "Bank/add_sms_type.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "name" => $name,
            "body" => $body,
            "charge" => $charge,
            "charge_to" => $charge_to,
            "charge_on" => $charge_on,
            "uid" => $uid,
            "bid" => $bid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function subscribe_to_sms_type($id)
    {
        $endpoint = "Bank/sms_subscribe_to_all_type.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function getSMSTypeDetails($id)
    {
        $endpoint = "Bank/get_sms_type_details.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return $data['data'];
        }

        return '';
    }
    function unsubscribe_to_sms_type($id)
    {
        $endpoint = "Bank/sms_unsubscribe_to_all_type.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function unsubscribe_to_sms_types($id)
    {
        $endpoint = "Bank/sms_unsubscribe_to_all_types.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function subscribe_all_client_sms($id)
    {
        $endpoint = "Bank/sms_subscribe_all_client.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function subscribe_all_bank_client_sms($id)
    {
        $endpoint = "Bank/sms_subscribe_all_bank_client.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function unsubscribe_all_bank_client_sms($id)
    {
        $endpoint = "Bank/sms_unsubscribe_all_bank_client.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function unsubscribe_all_client_sms($id)
    {
        $endpoint = "Bank/sms_unsubscribe_all_client.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function unsubscribe_client_sms($id)
    {
        $endpoint = "Bank/sms_unsubscribe_client.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function declinepurchaseSMS($id)
    {
        $endpoint = "Bank/decline_sms_purchase.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function declineBranchRequest($id)
    {
        $endpoint = "Bank/decline_branch_request.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function deactivateStaff($id)
    {
        $endpoint = "Bank/deactivate_staff.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function activateStaff($id)
    {
        $endpoint = "Bank/activate_staff.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function trashOverDraft($id)
    {
        $endpoint = "Bank/trash_over_draft.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function declineOverDraft($id)
    {
        $endpoint = "Bank/decline_over_draft.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function approveOverDraft($id)
    {
        $endpoint = "Bank/approve_over_draft.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function deactivateBankAccount($id)
    {
        $endpoint = "Bank/deactivate_bank_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function activateBankAccount($id)
    {
        $endpoint = "Bank/activate_bank_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function deactivateRole($id)
    {
        $endpoint = "Bank/deactivate_role.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function activateRole($id)
    {
        $endpoint = "Bank/activate_role.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function convertClient($id, $to)
    {
        $endpoint = "Bank/convert_client.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
            "to" => $to,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function undoLoanDisbursement($id)
    {
        $endpoint = "Bank/undo_loan_disbursement.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['message'];
    }

    function approveAllAgentDeposits($id, $uid, $amount)
    {
        $endpoint = "Bank/approve_all_agent_entries.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
            "uid" => $uid,
            "amount" => $amount,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function unfreezeaccount($id, $amount)
    {
        $endpoint = "Bank/unfreeze_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
            "amount" => $amount,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function requestpurchaseSMS($branch, $amount)
    {
        $endpoint = "Bank/request_sms_purchase.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "amount" => $amount,
            "branch" => $branch,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }

    function smsMakePayment($id, $amount, $phone, $reason, $bank, $branch, $acid)
    {
        $endpoint = "Bank/sms_make_payment.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "amount" => $amount,
            "id" => $id,
            "phone" => $phone,
            "reason" => $reason,
            "bank" => $bank,
            "branch" => $branch,
            "acid" => $acid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return $data['TreffID'];
        }

        return '';
    }

    function getSMSRequestDetails($id)
    {
        $endpoint = "Bank/get_sms_req_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return $data['data'];
        }

        return '';
    }
    function createLoanTopup($data)
    {
        $response = $this->responserHelper->post('Bank/topup_loan.php');
        return @$response;

        // $data = array(
        //     "lno" => $lno,
        //     "amount" => $amount,
        //     "record_date" => $date,
        //     "duration" => $duration,
        //     "send_sms" => $send_sms,
        //     "comments" => $comments,
        //     "userId" => $userId
        // );
    }

    function addBank($name, $tname, $location, $contact, $refered,$auto_chart)
    {
        $endpoint = "Bank/create_bank.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'tname'      => $tname,
            'location'      => $location,
            'contact'      => $contact,
            'refered' => $refered,
            'auto_chart' => $auto_chart
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }
    function applyAmortizationSchedule($repay_cycle_id, $principal, $monthly_interest_rate, $approved_loan_duration)
    {

        $cycle = $repay_cycle_id;

        $loan_amount     = (float) $principal;
        $term_years     = 1;
        $interest     = (float) $monthly_interest_rate;

        if ($cycle == 1) {
            $myduration = $approved_loan_duration;
        } else if ($cycle == 2) {
            $myduration = floor($approved_loan_duration / 7);
        } else if ($cycle == 3) {
            $myduration = $approved_loan_duration / 30;
        } else if ($cycle == 4) {
            $myduration = $approved_loan_duration / 15;
        }


        $terms         = (int)$myduration;

        $terms = ($terms == 0) ? 1 : $terms;

        $period = $terms * $term_years;
        $interest = (($interest) / 100);


        $schedule = array();

        $i = 1;
        while ($i <= $terms) {
            $deno = 1 - 1 / pow((1 + $interest), $period);

            $term_pay = ($loan_amount * $interest) / $deno;
            $interest = $loan_amount * $interest;

            $principal = $term_pay - $interest;
            $balance = $loan_amount - $principal;

            $myArray = array(
                'payment'     => $term_pay,
                'interest'     => $interest,
                'principal' => $principal,
                'balance'     => $balance
            );
            array_push($schedule, $myArray);
            $loan_amount = $balance;
            $period--;
            $i++;
        }



        // $usedate = $date_of_first_pay;

        // foreach ($schedule as $term_detail) {

        //     $sqlQueryx = 'INSERT INTO public."loan_schedule" (loan_id,amount,interest,principal,balance,date_of_payment) VALUES(:lid,:amount,:inter,:principal,:bal,:dop)';

        //     $stmtx = $this->conn->prepare($sqlQueryx);
        //     $stmtx->bindParam(':lid', $this->loan_id);
        //     $stmtx->bindParam(':amount', round($term_detail['payment']));
        //     $stmtx->bindParam(':inter', round($term_detail['interest']));
        //     $stmtx->bindParam(':principal', round($term_detail['principal']));
        //     $stmtx->bindParam(':bal', round($term_detail['balance']));
        //     $stmtx->bindParam(':dop', date('Y-m-d : H:i:s', strtotime($usedate)));

        //     $stmtx->execute();

        //     if ($cycle == 1) {
        //         $usedate = date('Y-m-d', strtotime($usedate . ' + 1 days'));
        //     } else if ($cycle == 2) {
        //         $usedate = date('Y-m-d', strtotime($usedate . ' + 7 days'));
        //     } else if ($cycle == 3) {
        //         $usedate = date('Y-m-d', strtotime($usedate . ' + 30 days'));
        //     } else if ($cycle == 4) {
        //         $usedate = date('Y-m-d', strtotime($usedate . ' + 15 days'));
        //     }
        // }

        return $schedule;
    }

    function addBranch($name, $id, $location, $bcode, $userId)
    {
        $endpoint = "Bank/create_branch.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'bid'      => $id,
            'location'      => $location,
            'bcode'      => $bcode,
            'user'      => $userId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        // var_dump($response);
        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function subscribe_bank_to_sms($id, $status)
    {
        $endpoint = "Bank/subscribe_bank_to_sms.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'st'      => $status,
            'bid'      => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function addBankAccount($name, $id, $bname, $acno, $branch, $pid)
    {
        $endpoint = "Bank/create_bank_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'bid'      => $id,
            'bname'      => $bname,
            'acno'      => $acno,
            'branch'      => $branch,
            'pid'      => $pid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function addCashAccount($name, $staff, $pid)
    {
        $endpoint = "Bank/create_cash_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'staff'      => $staff,
            'pid'      => $pid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function addSafeAccount($name, $staff, $pid)
    {
        $endpoint = "Bank/create_safe_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'branch'      => $staff,
            'pid'  => $pid
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function getLoanProductDetails($pid)
    {
        $endpoint = "Bank/get_loan_product_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'pid'      => $pid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getLastFiveTrxns($staff)
    {
        $endpoint = "Bank/get_staff_trxns.php?id=" . $staff;
        $url = BASE_URL . $endpoint;

        $data = array();
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getStaffTillEntries($start, $end, $staff)
    {
        $endpoint = "Bank/get_staff_till_entries.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'start'      => $start,
            'end'      => $end,
            'staff'      => $staff
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getJournalTillEntries($start, $end, $staff)
    {
        $endpoint = "Bank/get_journal_till_entries.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'start'      => $start,
            'end'      => $end,
            'staff'      => $staff
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }


    function getJournalAccBfs($lpid, $branch, $acid, $start_date, $end_date, $bank)
    {
        $endpoint = "Bank/get_journal_acc_bfs.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'start'      => $start_date,
            'end'      => $end_date,
            'lpid'      => $lpid,
            'branch'      => $branch,
            'acid'      => $acid,
            'bank'      => $bank,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getSafeTillEntries($start, $end, $staff)
    {
        $endpoint = "Bank/get_safe_till_entries.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'start'      => $start,
            'end'      => $end,
            'staff'      => $staff
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getCashAccBranding($staff)
    {
        $endpoint = "Bank/get_cash_acc_branding.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'staff'      => $staff
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getBankAccEntries($start, $end, $staff)
    {
        $endpoint = "Bank/get_bank_acc_entries.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'start'      => $start,
            'end'      => $end,
            'staff'      => $staff
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getBankStaff($bank, $branch)
    {
        $endpoint = "Bank/get_bank_staff.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'branch'      => $branch
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getBankStaff2($bank, $branch)
    {
        $endpoint = "Bank/get_bank_staff2.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'branch'      => $branch
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function setPassword($password, $uid)
    {
        $endpoint = "User/set_password.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'uid'      => $uid,
            'password'      => $password,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function updateBankDetails($bank, $name, $email, $address, $contact, $logo, $tname)
    {
        $endpoint = "Bank/update_bank_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'name'      => $name,
            'email'      => $email,
            'address'      => $address,
            'contact'      => $contact,
            'logo'      => $logo,
            'tname'      => $tname,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function createStaff(
        $branch,
        $role,
        $passport_photo_name,
        $title,
        $fname,
        $lname,
        $address,
        $address2,
        $country,
        $district,
        $subcounty,
        $parish,
        $village,
        $phone,
        $other_phone,
        $email,
        $nin,
        $dob,
        $kname,
        $kinphone,
        $kphysicaladdress,
        $knin,
        $relationship,
        $gender,
        $is_supervisor,
        $bid,
        $b_level
    ) {
        $endpoint = "User/create_bank_staff.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'branch'      => $branch,
            'role'      => $role,
            'title'      => $title,
            'passport' => $passport_photo_name,
            'fname' => $fname,
            'lname' => $lname,
            'address' => $address,
            'address2' => $address2,
            'country' => $country,
            'district' => $district,
            'subcounty' => $subcounty,
            'parish' => $parish,
            'village' => $village,
            'phone' => $phone,
            'other_phone' => $other_phone,
            'email' => $email,
            'nin' => $nin,
            'dob' => $dob,
            'kname' => $kname,
            'kphone' => $kinphone,
            'kphysicaladdress' => $kphysicaladdress,
            'knin' => $knin,
            'relationship' => $relationship,
            'gender' => $gender,
            'is_supervisor' => $is_supervisor,
            'bid' => $bid,
            'b_level' => $b_level,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        // var_dump($response);
        $data = json_decode($response, true);

        // if ($data['success']) {
        //     return $data['success'];
        // }
        return $data['success'];
    }


    function editStaff(
        $uid,
        $id,
        $branch,
        $role,
        $passport_photo_name,
        $title,
        $fname,
        $lname,
        $address,
        $address2,
        $country,
        $district,
        $subcounty,
        $parish,
        $village,
        $phone,
        $other_phone,
        $email,
        $nin,
        $dob,
        $kname,
        $kinphone,
        $kphysicaladdress,
        $knin,
        $relationship,
        $gender
    ) {
        $endpoint = "User/edit_bank_staff.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'uid'      => $uid,
            'id'      => $id,
            'branch'      => $branch,
            'role'      => $role,
            'title'      => $title,
            'passport' => $passport_photo_name,
            'fname' => $fname,
            'lname' => $lname,
            'address' => $address,
            'address2' => $address2,
            'country' => $country,
            'district' => $district,
            'subcounty' => $subcounty,
            'parish' => $parish,
            'village' => $village,
            'phone' => $phone,
            'other_phone' => $other_phone,
            'email' => $email,
            'nin' => $nin,
            'dob' => $dob,
            'kname' => $kname,
            'kphone' => $kinphone,
            'kphysicaladdress' => $kphysicaladdress,
            'knin' => $knin,
            'relationship' => $relationship,
            'gender' => $gender
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function trashTrxn($tid, $uid, $date, $comments)
    {
        $endpoint = "Bank/trash_trxn.php?id=" . urlencode($tid) .
            "&uid=" . urlencode($uid) .
            "&date=" . urlencode($date) .
            "&comments=" . urlencode($comments);
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        $data = json_decode($response, true);


        return $data['success'] ?? 0;
    }

    function reverseTrxn($id)
    {
        $endpoint = "Bank/reverse_trxn.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function closeFixed($id)
    {
        $endpoint = "Bank/close_fixed.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function trashWithdrawTrxn($tid, $uid, $date, $comments)
    {
        $endpoint = "Bank/trash_withdraw_trxn.php?id=" . urlencode($tid) .
            "&uid=" . urlencode($uid) .
            "&date=" . urlencode($date) .
            "&comments=" . urlencode($comments);
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new Exception("Failed to call API: " . error_get_last()['message']);
        }

        $data = json_decode($response, true);
        if (!isset($data['success'])) {
            throw new Exception("Invalid API response: " . $response);
        }

        return $data['success'];
    }


    function getBankBranches($bankid)
    {
        $endpoint = "Bank/get_all_branches.php?id=" . $bankid;
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $bankid,
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getClientRegions($bankid, $branch)
    {
        $endpoint = "Bank/get_all_regions.php?bank=" . $bankid . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankid,
            'branch'      => $branch,
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getClientVillages($bankid, $branch)
    {
        $endpoint = "Bank/get_all_villages.php?bank=" . $bankid . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankid,
            'branch'      => $branch,
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getClientParishes($bankid, $branch)
    {
        $endpoint = "Bank/get_all_parishes.php?bank=" . $bankid . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankid,
            'branch'      => $branch,
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getClientDistricts($bankid, $branch)
    {
        $endpoint = "Bank/get_all_districts.php?bank=" . $bankid . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bankid,
            'branch'      => $branch,
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        // if (@$data['success']) {
        return $data['data'];
        // }
        // return '';
    }

    function getBankBranches2($bankid, $branchid)
    {
        $endpoint = "Bank/get_all_branches_2.php?bank=" . $bankid . '&branch=' . $branchid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getPublicHolidays()
    {
        $year = date('Y');
        $endpoint = "https://date.nager.at/api/v3/publicholidays/" . $year . "/ZA";
        $url =  $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data;
    }

    function getBankDetails($bankid = null)
    {
        $bankid = $bankid ?? $_SESSION['session_user']['bankId'];
        $endpoint = "Bank/get_all_bank_details.php?id=" . $bankid;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getBranchName($bid = null)
    {
        $endpoint = "Bank/get_branch_name.php?id=" . $bid;
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getDataImporterResources()
    {
        $response = $this->responserHelper->get('Bank/get_data_importer_resources.php');
        return @$response['data'];
    }

    function setBankMembershipFeeSettings()
    {
        $response = $this->responserHelper->post('Bank/set_bank_membership_fee_settings.php');
        return @$response['success'];
    }

    function createAccountOpeningFee($data)
    {
        $response = $this->responserHelper->post('Fees/create_account_opening_fee.php', $data);
        return @$response['success'];
    }

    function getAccountOpeningFee($id)
    {
        $response = $this->responserHelper->post('Fees/get_account_opening_fee.php', ['fee_id' => $id]);
        return @$response['data'];
    }

    function deleteAccountOpeningFee($id)
    {
        return $this->responserHelper->post('Fees/delete_fee.php', ['fee_id' => $id]);
    }

    function getBranchDetails($id)
    {
        $response = $this->responserHelper->post('Bank/get_branch_details.php', ['branch_id' => $id]);
        return @$response['data'];
    }

    function setBranchWorkingHours($data)
    {
        $response = $this->responserHelper->post('Bank/set_working_hours.php', $data);
        return @$response['success'];
    }

    function getLoanBatchDetails($id)
    {
        $response = $this->responserHelper->get('Bank/get_data_importer_batch_loan_details.php', ['batch_id' => $id]);
        return @$response['data'];
    }

    function getBatchLoanDetails($id)
    {
        $response = $this->responserHelper->post('Bank/get_batch_loan_details.php', ['loan_id' => $id]);
        return @$response['data'];
    }

    function deleteBatchLoan($id)
    {
        $response = $this->responserHelper->post('Bank/delete_batch_loan.php', ['loan_id' => $id]);
        return @$response['success'];
    }

    function deleteLoanBatch($id)
    {
        $response = $this->responserHelper->post('Bank/delete_loan_batch.php', ['batch_id' => $id]);
        return @$response['success'];
    }

    function deleteDataImporterRecord($data)
    {
        $response = $this->responserHelper->post('Bank/delete_data_importer_record.php', $data);
        return @$response;
    }

    function deleteDataImporterBatch($data)
    {
        $response = $this->responserHelper->post('Bank/delete_data_importer_batch.php', $data);
        return @$response;
    }

    function import_batch_to_main_database($data)
    {
        $response = $this->responserHelper->post('Bank/import_batch_to_main_database.php', $data);
        return @$response;
    }

    function import_batch_record_to_main_database($data)
    {
        $response = $this->responserHelper->post('Bank/import_batch_record_to_main_database.php', $data);
        return @$response;
    }

    function get_data_importer_batch_records($data)
    {
        $response = $this->responserHelper->post('Bank/get_data_importer_batch_records.php', $data);
        return @$response;
    }

    function updateBatchLoan($data)
    {
        $response = $this->responserHelper->post('Bank/update_batch_loan.php', $data);
        return @$response['success'];
    }

    function approveLoanBatch($id)
    {
        $response = $this->responserHelper->post('Bank/approve_loan_batch.php', ['batch_id' => $id]);
        return @$response['success'];
    }

    function approveBatchLoan($id)
    {
        // return $id;
        $response = $this->responserHelper->post('Bank/approve_batch_loan.php', ['loan_id' => $id]);
        return @$response;
    }
    function approveTrxn($id, $uid)
    {
        // return $id;
        $response = $this->responserHelper->post('Bank/approve_trxn.php', ['tid' => $id, 'uid' => $uid]);
        return @$response;
    }

    function editDeposit($client, $amount, $comment, $depositor_name, $record_date, $pay_method, $bank_acc, $cheque_no, $cash_acc, $send_sms, $branchId, $userId, $tid, $orig_amount, $orig_acid)
    {
        $endpoint = "Bank/edit_deposit_final.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'client'      => $client,
            'amount'      => $amount,
            'reason'      => $comment,
            'deposited'      => $depositor_name,
            'branch'      => $branchId,
            'user'      => $userId,
            'date'      => $record_date,
            'pay_method'      => $pay_method,
            'bank_acc'      => $bank_acc,
            'cheque_no'      => $cheque_no,
            'cash_acc'      => $cash_acc,
            'send_sms'      => $send_sms,
            'tid'      => $tid,
            'orig_amount'      => $orig_amount,
            'orig_acid'      => $orig_acid,
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }


    function editWithdraw($client, $amount, $comment, $depositor_name, $record_date, $pay_method, $bank_acc, $cheque_no, $cash_acc, $send_sms, $branchId, $userId, $tid, $orig_amount)
    {
        $endpoint = "Bank/edit_withdraw_final.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'client'      => $client,
            'amount'      => $amount,
            'reason'      => $comment,
            'deposited'      => $depositor_name,
            'branch'      => $branchId,
            'user'      => $userId,
            'date'      => $record_date,
            'pay_method'      => $pay_method,
            'bank_acc'      => $bank_acc,
            'cheque_no'      => $cheque_no,
            'cash_acc'      => $cash_acc,
            'send_sms'      => $send_sms,
            'tid'      => $tid,
            'orig_amount'      => $orig_amount,
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function getTrxnDetails($id)
    {
        $endpoint = "Bank/get_trxn_details.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getDataImporterRecordDetails($data)
    {
        $response = $this->responserHelper->post('Bank/get_data_importer_record_details.php', $data);
        return @$response['data'];
    }

    function updateDataImporterRecordDetails($data)
    {
        $response = $this->responserHelper->post('Bank/update_data_importer_record_details.php', $data);
        return @$response;
    }

    function updateImportedClientAccBal($data)
    {
        $response = $this->responserHelper->post('Bank/update_imported_client_acc_bal.php', $data);
        return @$response;
    }

    function updateClientBatch($data)
    {
        $response = $this->responserHelper->post('Bank/data_importer_update_client_batch.php', $data);
        return @$response['success'];
    }

    function getDayStatus($bankid, $day)
    {
        $endpoint = "Bank/get_day_status.php?id=" . $bankid . '&day=' . $day;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return 0;
    }

    function deleteChartAccount($aid, $branch, $bank, $user)
    {
        $endpoint = "Accounting/delete_chart_account.php?id=" . $aid . '&branch=' . $branch . '&bank=' . $bank . '&user=' . $user;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        return $data;
    }

    function getHolidayStatus($bankid, $day)
    {
        $endpoint = "Bank/get_holiday_status.php?id=" . $bankid . '&day=' . $day;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return 0;
    }

    function closeDay($bankid, $day)
    {
        $endpoint = "Bank/closeDay.php?id=" . $bankid . '&day=' . $day;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return false;
    }

    function closeHoliday($bankid, $day)
    {
        $endpoint = "Bank/closeHoliday.php?id=" . $bankid . '&day=' . $day;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return false;
    }
    function openHoliday($bankid, $day)
    {
        $endpoint = "Bank/openHoliday.php?id=" . $bankid . '&day=' . $day;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return false;
    }
    function openDay($bankid, $day)
    {
        $endpoint = "Bank/openDay.php?id=" . $bankid . '&day=' . $day;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return false;
    }

    function getBankRoles($bankid)
    {
        $endpoint = "Bank/get_all_bank_roles.php?id=" . $bankid;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getStaffDetails($id)
    {
        $endpoint = "User/staff_details.php";
        $url = BASE_URL . $endpoint;
        $data = array(
            'id'      => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );



        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getPermissions()
    {
        $endpoint = "User/get_permissions.php";
        $url = BACKEND_BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getStaffPermissions($staff_id)
    {
        $response = $this->responserHelper->get('User/get_staff_permissions.php', ['staff_id' => $staff_id]);
        return @$response['data'];
    }

    function getRole($role_id)
    {
        $response = $this->responserHelper->get('User/get_role.php', ['role_id' => $role_id]);
        return @$response['data'];
    }

    function getAllPermissions()
    {
        $endpoint = "Bank/get_all_permissions.php";
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }


    function getAllSubPermissions($id)
    {
        $endpoint = "Bank/get_all_sub_permissions.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function updatePassword($password, $id)
    {
        $endpoint = "Bank/set_password.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'pass'      => $password,
            'id'      => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function saveRole($data)
    {
        // return $data;
        $endpoint = "User/create_role.php";
        $url = BASE_URL . $endpoint;
        $data['bank_id'] = $_SESSION['session_user']['bankId'];
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        return $data;

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function setStaffPermissions($data)
    {
        $response = $this->responserHelper->post('User/set_staff_permissions.php', $data);
        return @$response['data']['success'];
    }

    function createSavingAccount($name, $ucode, $rate, $duration, $bank, $rate_disburse, $withdraw, $pform, $acid, $pid, $opening)
    {
        $endpoint = "Bank/create_savings_group.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'ucode'      => $ucode,
            'rate'      => $rate,
            'duration'      => $duration,
            'bank'      => $bank,
            'disburse'      => $rate_disburse,
            'withdraw'      => $withdraw,
            'pform'      => $pform,
            'acid'      => $acid,
            'pid'      => $pid,
            'opening'      => $opening,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function getAllBanks()
    {
        $endpoint = "Bank/get_all_banks.php";
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['count'];
        }
        return $data['count'];
    }

    function getAllBanksList()
    {
        $endpoint = "Bank/get_all_banks.php";
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getAge($dob1)
    {
        $bday = new DateTime($dob1);
        $today = new DateTime(date('m.d.y'));
        if ($bday > $today) {
            return 'You are not born yet';
        }
        $diff = $today->diff($bday);
        return $diff->y;
    }
    function read_date($str)
    {
        if ($str)
            return date('F j, Y, g:i:s a', strtotime($str));
        else
            return null;
    }

    function read_date2($str)
    {
        if ($str)
            return date('F j, Y', strtotime($str));
        else
            return null;
    }
    function read_time($str)
    {
        if ($str)
            return date('g:i:s a', strtotime($str));
        else
            return null;
    }
    function getAllSavingsAccounts($bankid, $branch)
    {
        $endpoint = "User/get_all_bank_saving_accounts.php?bank=" . $bankid . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getCreditorDetails($id)
    {
        $endpoint = "Bank/get_creditor_details.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getDebtorDetails($id)
    {
        $endpoint = "Bank/get_debtor_details.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function editBank($name, $location, $contact, $refered, $id)
    {
        $endpoint = "Bank/edit_bank.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'location'      => $location,
            'contact'      => $contact,
            'id' => $id,
            'refered' => $refered
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function createPayable($data)
    {
        $endpoint = "Bank/register_payable.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function createReceivable($data)
    {
        $endpoint = "Bank/register_receivable.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        // var_dump($response);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function payReceivable($data)
    {
        $endpoint = "Bank/pay_receivable.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function createCreditor($data)
    {
        $endpoint = "Bank/register_creditor.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function onetooneTransfer($data)
    {
        $endpoint = "Bank/one_to_one_transfer.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return false;
    }

    function registerExcess($data)
    {
        $endpoint = "Bank/register_excess.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return false;
    }

    function registerShortfall($data)
    {
        $endpoint = "Bank/register_shortfall.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return false;
    }

    function clearShortfall($data)
    {
        $endpoint = "Bank/clear_shortfall.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return false;
    }

    function freezeAccount($data)
    {
        $endpoint = "Bank/freeze_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function createDebtor($data)
    {
        $endpoint = "Bank/register_debtor.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function addClient($data)
    {
        // return $data;
        $response = $this->responserHelper->post('User/create_client.php', $data);
        // $response = json_decode($response, true);
        return @$response;

        // $endpoint = "User/create_client.php";
        // $url = BASE_URL . $endpoint;

        // $data = array(
        //     'data'      => $data
        // );
        // $options = array(
        //     'http' => array(
        //         'method'  => 'POST',
        //         'content' => json_encode($data),
        //         'header' =>  "Content-Type: application/json\r\n" .
        //         "Accept: application/json\r\n"
        //     )
        // );

        // $context  = stream_context_create($options);
        // $response = file_get_contents($url, false, $context);
        // $data = json_decode($response, true);
        // var_dump($data);
        // exit;
        // return $data;
    }

    function addBankAdmin(
        $bank,
        $passport_photo_name,
        $title,
        $fname,
        $lname,
        $address,
        $address2,
        $country,
        $district,
        $subcounty,
        $parish,
        $village,
        $phone,
        $other_phone,
        $email,
        $nin,
        $dob,
        $kname,
        $kinphone,
        $kphysicaladdress,
        $knin,
        $relationship,
        $gender
    ) {
        $endpoint = "User/create_bank_admin.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'title'      => $title,
            'passport' => $passport_photo_name,
            'fname' => $fname,
            'lname' => $lname,
            'address' => $address,
            'address2' => $address2,
            'country' => $country,
            'district' => $district,
            'subcounty' => $subcounty,
            'parish' => $parish,
            'village' => $village,
            'phone' => $phone,
            'other_phone' => $other_phone,
            'email' => $email,
            'nin' => $nin,
            'dob' => $dob,
            'kname' => $kname,
            'kphone' => $kinphone,
            'kphysicaladdress' => $kphysicaladdress,
            'knin' => $knin,
            'relationship' => $relationship,
            'gender' => $gender
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function editBankAdmin(
        $sid,
        $bank,
        $passport_photo_name,
        $title,
        $fname,
        $lname,
        $address,
        $address2,
        $country,
        $district,
        $subcounty,
        $parish,
        $village,
        $phone,
        $other_phone,
        $email,
        $nin,
        $dob,
        $kname,
        $kinphone,
        $kphysicaladdress,
        $knin,
        $relationship,
        $gender
    ) {
        $endpoint = "User/edit_bank_admin.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'sid'      => $sid,
            'bank'      => $bank,
            'title'      => $title,
            'passport' => $passport_photo_name,
            'fname' => $fname,
            'lname' => $lname,
            'address' => $address,
            'address2' => $address2,
            'country' => $country,
            'district' => $district,
            'subcounty' => $subcounty,
            'parish' => $parish,
            'village' => $village,
            'phone' => $phone,
            'other_phone' => $other_phone,
            'email' => $email,
            'nin' => $nin,
            'dob' => $dob,
            'kname' => $kname,
            'kphone' => $kinphone,
            'kphysicaladdress' => $kphysicaladdress,
            'knin' => $knin,
            'relationship' => $relationship,
            'gender' => $gender
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }



    function editBranch($name, $location, $id)
    {
        $endpoint = "Bank/edit_branch.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'location'      => $location,
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }


    function getFreezedAccounts($bank, $branch)
    {
        $endpoint = "Bank/get_freezed_accounts.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getStaffShortfalls($bank, $branch)
    {
        $endpoint = "Bank/get_staff_shortfalls.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getExcess($bank, $branch)
    {
        $endpoint = "Bank/get_staff_excess.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getLoanDetails($id)
    {
        $endpoint = "Bank/get_loan_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getLoanFees($id)
    {
        $endpoint = "Bank/get_loan_fees.php?id=" . $id;
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getWalletDetails($bank)
    {
        $endpoint = "mobile_money/check_balance.php?id=" . $bank;
        $url = BASE_URL . $endpoint;
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        // if ($data['success']) {
        //     return $data['data'][0];
        // }
        return $data;
    }

    function getAllAgentTrxns($id)
    {
        $endpoint = "Bank/get_agent_transactions.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getRequestDetails($id)
    {
        $endpoint = "Bank/get_request_details.php?id=" . $id;
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function deleteLoanProduct($id)
    {
        $endpoint = "Bank/delete_loan_product.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function deleteLoanCollateral($id)
    {
        $endpoint = "Bank/delete_loan_collateral.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }
    function deleteSMSType($id)
    {
        $endpoint = "Bank/delete_sms_type.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function deleteLoanGuarantor($id)
    {
        $endpoint = "Bank/delete_loan_guarantor.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function deleteLoan($id)
    {
        $endpoint = "Bank/delete_loan.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }


    function sendLoanToCleared($id)
    {
        $endpoint = "Bank/send_loan_to_cleared.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function rectifyLoanBalances($id)
    {
        $endpoint = "Bank/set_actype.php?lno=" . $id;
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function deleteSharePurchase($id)
    {
        $endpoint = "Bank/delete_share_purchase.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function rectifyLoan($id)
    {
        $endpoint = "Bank/rectify_loan.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function rectifyPrincipal($id)
    {
        $endpoint = "Bank/rectify_principal.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function getFixedDepDetails($id)
    {
        $endpoint = "Bank/get_fixed_dep_details.php?id=" . $id;
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getOverDraftDetails($id)
    {
        $endpoint = "Bank/get_over_draft_details.php?id=" . $id;
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function getFixedDepSchData($id, $amount, $int_rate, $period, $ptype, $freqtype)
    {
        $endpoint = "Bank/get_fixed_deposit_schedule_data.php?id=" . $id . "&amount=" . $amount . "&interest=" . $int_rate . "&period=" . $period . "&period_type=" . $ptype . "&frequency=" . $freqtype;
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['data'];
    }

    function deleteIncome($tid, $uid, $date, $comments)
    {
        $endpoint = "Bank/delete_income.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $tid,
            'uid' => $uid,
            'date' => $date,
            'comments' => $comments,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function deleteGroupMember($id)
    {
        $endpoint = "Bank/delete_group_member.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function deleteShortfall($id)
    {
        $endpoint = "Bank/delete_shortfall.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }
    function deleteLoanRepayment($tid, $uid, $date, $comments)
    {
        $endpoint = "Bank/delete_loan_repayment.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $tid,
            'uid' => $uid,
            'date' => $date,
            'comments' => $comments,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function deleteExcess($id)
    {
        $endpoint = "Bank/delete_excess.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function getLoanRepayments($id)
    {
        $endpoint = "Bank/get_loan_repayments.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id' => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getMemberDetails($id)
    {
        $endpoint = "User/get_member_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return $data['data'];
    }

    function enrollBiometrics($cid, $fingerprint)
    {
        $endpoint = "User/enroll_biometrics.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $cid,
            'fingerprint'      => $fingerprint,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function getGroupMembers($id)
    {
        $endpoint = "User/get_group_members.php?id=" . $id;
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getLoanAttachments($id)
    {
        $endpoint = "User/get_loan_attachments.php?id=" . $id;
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function subscribeSchoolPay($id)
    {
        $endpoint = "Bank/subscribe_school_pay.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }


    function createDepositFees($client, $amount, $comment, $depositor_name, $record_date, $pay_method, $bank_acc, $cheque_no, $cash_acc, $send_sms, $branchId, $userId, $sno, $sname, $sclass, $depositor_contact, $send_sms_school, $term)
    {
        $endpoint = "Bank/create_deposit_final_fees.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'client'      => $client,
            'term'      => $term,
            'amount'      => $amount,
            'reason'      => $comment,
            'deposited'      => $depositor_name,
            'branch'      => $branchId,
            'user'      => $userId,
            'date'      => $record_date,
            'pay_method'      => $pay_method,
            'bank_acc'      => $bank_acc,
            'cheque_no'      => $cheque_no,
            'cash_acc'      => $cash_acc,
            'send_sms'      => $send_sms,
            'sno'      => $sno,
            'sname'      => $sname,
            'sclass'      => $sclass,
            'deposited_phone'      => $depositor_contact,
            'send_sms_school'      => $send_sms_school,
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        //   var_dump($response);
        //   exit;

        return $data;

        // return $data['data'];
    }
    function unsubscribeSchoolPay($id)
    {
        $endpoint = "Bank/unsubscribe_school_pay.php?id=" . $id;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return true;
        }

        return false;
    }
    function setClientPassword($pass, $id)
    {
        $endpoint = "Bank/set_client_password.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'pass'      => $pass,
            'id'      => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }
    function getCustomerTransFees($id, $start, $end)
    {
        $endpoint = "User/getClientTransactionsFees.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'start'      => $start,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function updateClientDetails($details)
    {

        // return $details;
        // $response = $this->responserHelper->post('User/update_member_details.php', $details);
        // return @$response['data']['success'];

        $endpoint = "User/update_member_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        // var_dump($response);
        return $data['success'];
    }

    function editScheduleDate($details)
    {
        $endpoint = "User/update_schedule_date.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }

    function editLoanSavingAcc($details)
    {
        $endpoint = "User/update_loan_saving_acc.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }

    function editImporterLoanSavingAcc($details)
    {
        $endpoint = "User/update_importer_loan_saving_acc.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }

    function registerBulkTransfer($details)
    {

        $endpoint = "User/bulk_transfer.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data;
    }

    function editCreditOfficer($details)
    {
        $endpoint = "User/update_credit_officer.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }

    function editLoanBranch($details)
    {
        $endpoint = "User/update_loan_branch.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }

    function registerFixedDeposit($details)
    {

        // return $details;
        // $response = $this->responserHelper->post('User/update_member_details.php', $details);
        // return @$response['data']['success'];

        $endpoint = "User/register_fixed_deposit.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }

    function updateFixedDeposit($details)
    {



        $endpoint = "User/update_fixed_deposit.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }

    function createOverDraftProduct($details)
    {

        $endpoint = "User/create_over_draft_product.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }

    function createOverDraft($details)
    {
        $endpoint = "User/create_over_draft.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data['success'];
    }
    function purchaseShares($details)
    {
        $endpoint = "Bank/share_purchase.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        // var_dump($response);

        return @$data['success'];
    }

    function withdrawShares($details)
    {
        $endpoint = "Bank/share_withdraw.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function transferShares($details)
    {
        $endpoint = "Bank/share_transfer.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }
    function scheduleBulkSMS($details)
    {
        $endpoint = "User/schedule_sms.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $details
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function createClientAccountResources($id)
    {
        $endpoint = "User/create_client_account_resources.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        return $data = json_decode($response, true);
    }

    function updateAccountStatus($id, $status)
    {
        $endpoint = "User/update_member_status.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'status'      => $status,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        return $data = json_decode($response, true);
    }

    function deleteCustomerAcc($id, $user, $branch, $bank)
    {
        $endpoint = "User/delete_customer_acc.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'user'      => $user,
            'branch'      => $branch,
            'bank'      => $bank,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        return $data = json_decode($response, true);
    }


    function createClientAccount($data)
    {
        $endpoint = "User/create_client_account.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        // var_dump($response);
        // exit;
        $data = json_decode($response, true);


        return @$data['success'];
    }

    function addGroupMember($data)
    {
        $endpoint = "User/add_group_member.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        // var_dump($response);
        // exit;
        $data = json_decode($response, true);


        return @$data['success'];
    }

    function getAmort($lam, $rate, $dur, $cycle, $sr)
    {

        $endpoint = "Bank/get_amort.php";
        $url = BASE_URL . $endpoint;
        $data = array(
            'lam'      => $lam,
            'rate'      => $rate,
            'dur'      => $dur,
            'cycle'      => $cycle,
            'sr'      => $sr,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        // if ($data['success']) {
        return $data['schedule'];
        // }
        // return "";
    }

    function getAmort2($lam, $rate, $dur, $cycle, $sr)
    {

        $endpoint = "Bank/get_amort.php";
        $url = BASE_URL . $endpoint;
        $data = array(
            'lam'      => $lam,
            'rate'      => $rate,
            'dur'      => $dur,
            'cycle'      => $cycle,
            'sr'      => $sr,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        // if ($data['success']) {
        return $data['summary'];
        // }
        // return "";
    }

    function hasLoan($id)
    {
        $endpoint = "User/has_active_loan.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return $data['data'];
    }

    function getCustomerTrans($id, $start, $end, $by_tid, $by_date)
    {
        $endpoint = "User/getClientTransactions.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'start'      => $start,
            'end'      => $end,
            'by_tid'      => $by_tid,
            'by_date'      => $by_date,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getCustomerShareTrans($id, $start, $end)
    {
        $endpoint = "User/getClientShareTransactions.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'start'      => $start,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getCustomerShareTrans2($id, $start, $end)
    {
        $endpoint = "User/getClientShareTransactions2.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'start'      => $start,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getShareAccDetails($id)
    {
        $endpoint = "User/get_share_acc_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getCustomerShareTrans3($id, $start, $end)
    {
        $endpoint = "User/getClientShareTransactions3.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'start'      => $start,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getCustomerTransBalBF($id, $start, $end)
    {
        $endpoint = "User/getClientTransactionsBF.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'start'      => $start,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return 0;
    }

    function getCustomerSharesBalBF($id, $start, $end, $filtered)
    {
        $endpoint = "User/getClientSharesBF.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'start'      => $start,
            'end'      => $end,
            'filtered'      => $filtered,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return 0;
    }

    function getCustomerTransBalBFEnd($id, $start, $end)
    {
        $endpoint = "User/getClientTransactionsBFEnd.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'start'      => $start,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return 0;
    }

    function createFee($name, $ftype, $ptype, $feeAmount, $bankId, $pform, $acid, $pid)
    {
        $endpoint = "Bank/create_fee.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'ftype'      => $ftype,
            'ptype'      => $ptype,
            'feeamount'      => $feeAmount,
            'bank'      => $bankId,
            'pform'      => $pform,
            'acid'      => $acid,
            'pid'      => $pid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function setStaffPassword($pass, $id)
    {
        $endpoint = "Bank/set_password.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'pass'      => $pass,
            'id'      => $id,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }
    function setStaffPassword2($pass, $old_pass, $id)
    {
        $endpoint = "Bank/set_password_2.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'pass'      => $pass,
            'id'      => $id,
            'old_pass'      => $old_pass,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function createLoanProduct($data)
    {
        $response = $this->responserHelper->get('Bank/create_loan_product.php', $data);
        return @$response;
    }

    function createLoanProduct_old(
        $name,
        $intrate,
        $freq,
        $interestMethod,
        $gridRadios,
        $fee,
        $prate,
        $pfamount,
        $gracedays,
        $maxdays,
        $bankId,
        $auto_repay,
        $auto_penalty,
        $round_off,
        $gracetype,
        $penaltybased
    ) {
        $endpoint = "Bank/create_loan_product.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'intrate'      => $intrate,
            'freq'      => $freq,
            'interestMethod'      => $interestMethod,
            'penalty'      => $gridRadios,
            'fee'      => $fee,
            'prate'      => $prate,
            'pfamount'      => $pfamount,
            'gracedays'      => $gracedays,
            'maxdays'      => $maxdays,
            'bank'      => $bankId,
            'auto_repay'      => $auto_repay,
            'auto_penalty'      => $auto_penalty,
            'round_off'      => $round_off,
            'gracetype'      => $gracetype,
            'penaltybased'      => $penaltybased,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function editLoanProduct(
        $lpid,
        $name,
        $intrate,
        $freq,
        $interestMethod,
        $gridRadios,
        $fee,
        $prate,
        $pfamount,
        $gracedays,
        $maxdays,
        $bankId,
        $auto_repay,
        $auto_penalty,
        $round_off,
        $gracetype,
        $penaltybased
    ) {
        $endpoint = "Bank/edit_loan_product.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'lpid'      => $lpid,
            'name'      => $name,
            'intrate'      => $intrate,
            'freq'      => $freq,
            'interestMethod'      => $interestMethod,
            'penalty'      => $gridRadios,
            'fee'      => $fee,
            'prate'      => $prate,
            'pfamount'      => $pfamount,
            'gracedays'      => $gracedays,
            'maxdays'      => $maxdays,
            'bank'      => $bankId,
            'auto_repay'      => $auto_repay,
            'auto_penalty'      => $auto_penalty,
            'round_off'      => $round_off,
            'gracetype'      => $gracetype,
            'penaltybased'      => $penaltybased,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }


    function addLoan($client, $product, $disbursedate, $amount, $duration, $notes, $bankId, $branchId, $user, $freq)
    {
        $endpoint = "Bank/create_loan_application.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'client'      => $client,
            'product'      => $product,
            'disbursedate'      => $disbursedate,
            'startdate'      => $disbursedate,
            'amount'      => $amount,
            'duration'      => $duration,
            'notes'      => $notes,
            'bank'      => $bankId,
            'branch'      => $branchId,
            'user'      => $user,
            'freq'      => $freq,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        // var_dump($response);
        $data = json_decode($response, true);

        // if ($data['success']) {
        return $data;
        // }
        // return $data['success'];
    }

    function updateLoan($client, $product, $disbursedate, $amount, $duration, $notes, $bankId, $branchId, $user, $lno)
    {
        $endpoint = "Bank/update_loan_application.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'client'      => $client,
            'product'      => $product,
            'disbursedate'      => $disbursedate,
            'startdate'      => $disbursedate,
            'amount'      => $amount,
            'duration'      => $duration,
            'notes'      => $notes,
            'bank'      => $bankId,
            'branch'      => $branchId,
            'user'      => $user,
            'lno'      => $lno,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function getAllBankFees($bankId)
    {
        $endpoint = "Bank/get_all_bank_fees.php?bank=" . $bankId;
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function createLoanComments($comments, $bankid, $branchid, $userid, $lno)
    {
        $endpoint = "Bank/add_loan_comment.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'comments'      => $comments,
            'bankid'      => $bankid,
            'branchid'      => $branchid,
            'userid'      => $userid,
            'lno'      => $lno
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return false;
    }


    function createInterBranchRequest($data)
    {
        $endpoint = "Bank/create_inter_branch_request.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return false;
    }

    function cashTransfer($data)
    {
        $endpoint = "Bank/add_cash_transfer.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return false;
    }

    function advancedJournalEntry($data)
    {
        $endpoint = "Bank/add_advanced_journal_entry.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'data'      => $data,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return false;
    }

    function getCashTransferTrxns($bank, $branch, $start, $end)
    {
        $endpoint = "Bank/get_all_cash_transfers.php?bank=" . $bank . '&branch=' . $branch . "&start=" . $start . "&end=" . $end;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getInterBranchRequests($bank, $branch)
    {
        $endpoint = "Bank/get_all_inter_branch_requests.php?bank=" . $bank . '&branch=' . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getLoanSchedule($lid)
    {
        $endpoint = "Bank/get_loan_schedule.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $lid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getAllActiveLoans2($bank, $branch, $lpid, $officer, $st, $end)
    {
        $endpoint = "Bank/get_all_active_loans_details2.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'branch'      => $branch,

            'lpid'      => $lpid,
            'officer'      => $officer,
            'st'      => $st,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getAllActiveLoans($bank, $branch, $lpid, $officer, $st, $end)
    {
        $endpoint = "Bank/get_all_active_loans_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'branch'      => $branch,

            'lpid'      => $lpid,
            'officer'      => $officer,
            'st'      => $st,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getAllLoanDisbursements($bank, $branch, $lpid, $officer, $st, $end)
    {
        $endpoint = "Bank/get_all_loan_dibsbursements.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'branch'      => $branch,

            'lpid'      => $lpid,
            'officer'      => $officer,
            'st'      => $st,
            'end'      => $end,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }


    function print_sub_account_data($major)
    {
        $endpoint = "Bank/print_sub_account_data.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'major'      => $major
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function getBankMajorAccounts($bank, $branch)
    {
        $endpoint = "Bank/get_bank_major_accounts.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'branch'      => $branch,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getLoanGuarantors($lid)
    {
        $endpoint = "Bank/get_loan_guarantors.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $lid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function createRangeCharge($name, $apply, $bankId, $min, $max, $charge)
    {
        $endpoint = "Bank/create_range_charge.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'apply'      => $apply,
            'charge'      => $charge,
            'bank'      => $bankId,
            'min'      => $min,
            'max'      => $max,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function createTxnCharge($name, $mode, $apply_to, $feeAmount, $bankId, $acid)
    {
        $endpoint = "Bank/create_trxn_charge.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'name'      => $name,
            'mode'      => $mode,
            'apply'      => $apply_to,
            'fee_amount'      => $feeAmount,
            'bank'      => $bankId,
            'acid'      => $acid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);


        return $data['success'];
    }

    function getLoanCollaterals($lid)
    {
        $endpoint = "Bank/get_loan_collaterals.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $lid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }
    function getLoanIncomeSources($lid)
    {
        $endpoint = "Bank/get_loan_income_sources.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $lid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }


    function addCollateralCategory($bank, $name, $descri)
    {
        $endpoint = "Bank/create_collateral_category.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'name'      => $name,
            'descri'      => $descri,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function getCollateralCategories($bank, $branch)
    {
        $endpoint = "Bank/get_collateral_categories.php?bank=" . $bank . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;


        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function addCollateral($catid, $lid, $name, $location, $mv, $fv, $link_name, $rby)
    {
        $endpoint = "Bank/create_collateral.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'cat'      => $catid,
            'lid'      => $lid,
            'name'      => $name,
            'location'      => $location,
            'mv'      => $mv,
            'fv'      => $fv,
            'link'      => $link_name,
            'rby'      => $rby,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function addGuarantor($mid, $lid, $non_member, $is_client, $attach)
    {
        $endpoint = "Bank/create_guarantor.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'mid'      => $mid,
            'lid'      => $lid,
            'non_member'      => $non_member,
            'is_client'      => $is_client,
            'attach'      => $attach,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function getReceiptDetails($tid)
    {
        $endpoint = "Bank/get_transaction_details.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'tid'      => $tid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function approveLoan($id, $rate, $notes, $duration, $amount, $userId, $freq, $ddate)
    {
        $endpoint = "Bank/approve_loan.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'rate'      => $rate,
            'notes'      => $notes,
            'duration'      => $duration,
            'amount'      => $amount,
            'uid'      => $userId,
            'freq'      => $freq,
            'ddate'      => $ddate,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function uploadAttachment($id, $link, $name)
    {
        $endpoint = "Bank/upload_attachment.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'link'      => $link,
            'name'      => $name,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function addIncomeSource($id, $name, $return, $details, $passport_photo_name)
    {
        $endpoint = "Bank/add_income_source.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'link'      => $passport_photo_name,
            'name'      => $name,
            'returns'      => $return,
            'details'      => $details,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }


    function declineLoan($id, $reason, $userId)
    {
        $endpoint = "Bank/deny_loan.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'reason'      => $reason,
            'uid'      => $userId,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function getBranchClientsCount($bid)
    {
        $endpoint = "Bank/get_branch_clients_count.php?id=" . $bid;
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $bid,
        );
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'][0]['count'];
        }
        return '0' . ' - ' . '0';
    }

    function rectifyShareMoyo($data)
    {
        $response = $this->responserHelper->get('Bank/rectify_share_amount_moyo.php', $data);
        return @$response;
    }

    function createLoanRepay($data)
    {
        // $endpoint = "Bank/create_loan_repay.php";
        $response = $this->responserHelper->get('Bank/create_loan_repay.php', $data);
        return @$response;

        // $endpoint = "Bank/create_loan_repay.php";
        // $url = BASE_URL . $endpoint;

        // // $data = array(
        // //     'id'      => $bid,
        // // );
        // $options = array(
        //     'http' => array(
        //         'method'  => 'GET',
        //         'content' => json_encode($data),
        //         'header' =>  "Content-Type: application/json\r\n" .
        //         "Accept: application/json\r\n"
        //     )
        // );

        // $context  = stream_context_create($options);
        // $response = file_get_contents($url, false, $context);
        // $data = json_decode($response, true);

        // if ($data['success']) {
        //     return $data['success'];
        // }
        // return false;
    }
    function createLoanRepayPI($data)
    {
        // $endpoint = "Bank/create_loan_repay.php";
        $response = $this->responserHelper->get('Bank/create_loan_repay_pi.php', $data);
        return @$response;

        // $endpoint = "Bank/create_loan_repay.php";
        // $url = BASE_URL . $endpoint;

        // // $data = array(
        // //     'id'      => $bid,
        // // );
        // $options = array(
        //     'http' => array(
        //         'method'  => 'GET',
        //         'content' => json_encode($data),
        //         'header' =>  "Content-Type: application/json\r\n" .
        //         "Accept: application/json\r\n"
        //     )
        // );

        // $context  = stream_context_create($options);
        // $response = file_get_contents($url, false, $context);
        // $data = json_decode($response, true);

        // if ($data['success']) {
        //     return $data['success'];
        // }
        // return false;
    }
    function createSavingInitiation($data)
    {
        // $endpoint = "Bank/create_loan_repay.php";
        $response = $this->responserHelper->get('Bank/create_saving_initiation.php', $data);
        return @$response;
    }
    function updateTrxnAccountBalance($data)
    {
        $response = $this->responserHelper->get('Bank/update_trxn_acc_balance.php', $data);
        return @$response;
    }
    function updateTrxnAccountName($data)
    {
        $response = $this->responserHelper->get('Bank/update_trxn_acc_name.php', $data);
        return @$response;
    }
    function createGeneralFeesInitiation($data)
    {
        // $endpoint = "Bank/create_loan_repay.php";
        $response = $this->responserHelper->get('Bank/create_general_fees_initiation.php', $data);
        return @$response;
    }
    function createShareInitiation($data)
    {
        // $endpoint = "Bank/create_loan_repay.php";
        $response = $this->responserHelper->get('Bank/create_share_initiation.php', $data);
        return @$response;
    }

    function waivePenalty($data)
    {
        $response = $this->responserHelper->post('Loan/waive_penalty.php', $data);
        return @$response;
    }

    function write_off_loan($data)
    {
        $response = $this->responserHelper->post('Loan/write_off_loan.php', $data);
        return @$response;
    }

    function waiveInterest($data)
    {
        $response = $this->responserHelper->post('Loan/waive_interest.php', $data);
        return @$response;

        // $endpoint = "Loan/waive_interest.php";
        // $url = BASE_URL . $endpoint;

        // $data = array(
        //     'data'      => $data
        // );
        // $options = array(
        //     'http' => array(
        //         'method'  => 'POST',
        //         'content' => json_encode($data),
        //         'header' =>  "Content-Type: application/json\r\n" .
        //         "Accept: application/json\r\n"
        //     )
        // );

        // $context  = stream_context_create($options);
        // $response = file_get_contents($url, false, $context);
        // $data = json_decode($response, true);
        // return $data;
    }

    function approveBranchRequest($data)
    {
        $response = $this->responserHelper->post('Loan/process_branch_request.php', $data);
        return @$response;
    }


    function disburseLoan($id, $famount, $ddate, $sdate, $userId, $mode, $amount, $auth, $lpid, $auto_pay, $bacc, $cash_acc, $cheque_no)
    {
        $endpoint = "Bank/disburse_loan.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'id'      => $id,
            'freeze'      => $famount,
            'ddate'      => $ddate,
            'sdate'      => $ddate,
            'mode'      => $mode,
            'uid'      => $userId,
            'amount'      => $amount,
            'auth'      => $auth,
            'lpid'      => $lpid,
            'auto_pay'      => $auto_pay,
            'bacc'      => $bacc,
            'cash_acc'      => $cash_acc,
            'cheque'      => $cheque_no,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        // var_dump($response);
        return @$data['success'];
        // if ($data['success']) {
        // return $data['success'];
        // }
        // return $data['success'];
    }

    function getAllBankLoanProducts($bankId, $branchId)
    {
        $endpoint = "Bank/get_all_loan_products.php?bank=" . $bankId . "&branch=" . $branchId;
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function SMSAccountBalance($method, $username, $password)
    {

        $url = "www.egosms.co/api/v1/plain/?";

        $parameters = "method=[method]&username=[username]&password=[password]";
        $parameters = str_replace("[method]", urlencode($method), $parameters);
        $parameters = str_replace("[username]", urlencode($username), $parameters);
        $parameters = str_replace("[password]", urlencode($password), $parameters);
        $live_url = "https://" . $url . $parameters;
        $parse_url = file($live_url);
        $response = $parse_url[0];
        return $response;
    }

    function getAllBankClients($bank, $branch)
    {
        $endpoint = "User/get_all_bank_clients.php?bank=" . $bank . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }


    function getSMSUniqueKeys($bank, $branch)
    {
        $endpoint = "Bank/get_all_sms_unique_keys.php?bank=" . $bank . "&branch=" . $branch;
        $url = BASE_URL . $endpoint;

        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function loginStaff($email, $password)
    {
        $endpoint = "User/login_staff.php";
        $url = BASE_URL . $endpoint;

        // var_dump($url);
        // exit;

        $data = array(
            'email'      => $email,
            'password'      => $password
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if (@$data['success']) {
            return $data['data'];
        }
        return '';
    }

    function sendSingleSMS($send_to, $sms_phone, $client, $sms_text, $branchId, $userId, $charge, $senderid)
    {
        $endpoint = "Bank/send_single_sms.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'send_to'      => $send_to,
            'sms_phone'      => $sms_phone,
            'clientid'      => $client,
            'sms_text'      => $sms_text,
            'branch'      => $branchId,
            'user'      => $userId,
            'charge'      => $charge,
            'sid'      => $senderid,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        return $data['success'];
    }

    function getBankSenderIds($bank, $branch)
    {
        $endpoint = "Bank/get_bank_sender_ids.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'bank'      => $bank,
            'branch'      => $branch,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        if ($data['success']) {
            return $data['data'];
        }
        return '';
    }

    function createDeposit($client, $amount, $comment, $depositor_name, $record_date, $pay_method, $bank_acc, $cheque_no, $cash_acc, $send_sms, $branchId, $userId, $phone)
    {
        $endpoint = "Bank/create_deposit_final.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'client'      => $client,
            'amount'      => $amount,
            'reason'      => $comment,
            'deposited'      => $depositor_name,
            'branch'      => $branchId,
            'user'      => $userId,
            'date'      => $record_date,
            'pay_method'      => $pay_method,
            'bank_acc'      => $bank_acc,
            'cheque_no'      => $cheque_no,
            'cash_acc'      => $cash_acc,
            'send_sms'      => $send_sms,
            'phone'      => $phone,
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        // var_dump($response);
        if ($data['success']) {
            return $data;
        }
        return $data;
    }

    function createWithdraw($client, $amount, $comment, $depositor_name, $record_date, $pay_method, $bank_acc, $cheque_no, $cash_acc, $send_sms, $branchId, $userId, $make_charges, $is_verified, $phone)
    {
        $endpoint = "Bank/create_withdraw_final.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'is_verified'      => $is_verified,
            'client'      => $client,
            'amount'      => $amount,
            'reason'      => $comment,
            'deposited'      => $depositor_name,
            'branch'      => $branchId,
            'user'      => $userId,
            'date'      => $record_date,
            'pay_method'      => $pay_method,
            'bank_acc'      => $bank_acc,
            'cheque_no'      => $cheque_no,
            'cash_acc'      => $cash_acc,
            'send_sms'      => $send_sms,
            'make_charges'      => $make_charges,
            'phone'      => $phone,
        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data;
        }
        return $data;
    }





    function updateStaff(
        $fname,
        $lname,
        $gender,
        $email,
        $address1,
        $address2,
        $district,
        $subcounty,
        $parish,
        $village,
        $primaryCellPhone,
        $secondaryCellPhone,
        $dob,
        $nin,
        $spousename,
        $spouseNin,
        $spousePhone,
        $id,
        $country
    ) {
        $endpoint = "User/update_staff.php";
        $url = BASE_URL . $endpoint;

        $data = array(
            'email'      => $email,
            'fname'      => $fname,
            'lname'      => $lname,
            'gender'      => $gender,
            'address1'      => $address1,
            'address2'      => $address2,
            'district'      => $district,
            'subcounty'      => $subcounty,
            'parish'      => $parish,
            'village'      => $village,
            'primaryCell'      => $primaryCellPhone,
            'secondaryCell'      => $secondaryCellPhone,
            'dob'      => $dob,
            'nin'      => $nin,
            'spousename'      => $spousename,
            'spousenin'      => $spouseNin,
            'spousePhone'      => $spousePhone,
            'id'      => $id,
            'country'      => $country,

        );
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data['success']) {
            return $data['success'];
        }
        return $data['success'];
    }

    function dataImporterClients($data)
    {
        // return $response = $this->responserHelper->post('Bank/data_importer_clients.php');
        // return @$response['data'];
    }
}
