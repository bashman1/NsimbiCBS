<?php
require_once '../../config/constants.php';
require_once '../../config/functions.php';
require_once '../../api/DatatableSearchHelper.php';
require_once '../../config/DbHandler.php';
require_once 'AuditTrail.php';
require_once 'Loan.php';
require_once 'User.php';
require_once 'Transaction.php';


// require_once(__DIR__ . '../../../vendor/autoload.php');

// use Carbon\Carbon;

class Bank
{
    // DB stuff
    private $conn;
    private $db_table = 'Bank';


    //   table columns
    public $data_array;
    public $loan_object;
    public $name;
    public $location;
    public $contact_person_details;
    public $recommender;
    public $id;
    public $processing_fee_rate;
    public $serialNumber;
    public $countryCode;
    public $auto_chart;
    public $approval_date;
    public $lowestCurrencyValue;
    public $createdAt;
    public $deletedAt;
    public $updatedAt;
    public $identificationNumber;
    public $bank;
    public $branch;
    public $description;
    public $sv;
    public $pv;
    public $charge_penalty;
    public $loan;
    public $clear_penalty;
    public $filter_saving_officer;
    public $penalty_amount;
    public $loan_id;
    public $left_balance;
    public $wallet;
    public $details;

    public $user;
    public $bcode;


    public $loan_amount;
    public $term_years;
    public $interest;
    public $terms;
    public $period;
    public $wht;
    public $principal;
    public $balance;
    public $term_pay;
    public $amount;

    // 
    public $auto_repay;
    public  $auto_penalty;
    public $round_off;
    public $gracetype;
    public $penaltybased;

    public $subPermissionArray = array();
    public $mainPermissionArray = array();

    public $date_of_next_pay;
    public $collection_data;
    public $collection_date;
    public $pay_method;
    public $send_sms;
    public $bank_acc;
    public $cash_acc;
    public $cheque_no;
    public $charges_membership_fee;
    public $membership_fee_chanel;
    public $membership_fee_required;

    public $bankId;
    public $branchId;
    public $tname;
    public $auth_id;

    public $filter_search_string;
    public $filter_branch_id;
    public $filter_client_type;
    public $filter_gender;
    public $filter_actype;
    public $filter_start_date;
    public $filter_end_date;
    public $filter_per_page;
    public $filter_page;
    public $with_active_status = true;

    public $start_of_payment_date;
    public $client_type_section;

    public $acid;
    public $pid;

    public $account_id;
    public $int_id;
    public $p_id;
    public $check_st;
    public $affected_acid;
    public $wht_acid;
    public $int_acid;
    public $from_date;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        date_default_timezone_set("Africa/Kampala");
    }

    public function rectifyShareAmount($difference, $notes, $collection_date, $uid, $auth_id, $branch_id, $acid, $shares)
    {

        $sqlQuery = 'UPDATE share_register SET share_amount=share_amount-:diff, no_shares=:sh where userid=:uidd
';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':uidd', $uid);
        $stmt->bindParam(':diff', $difference);
        $stmt->bindParam(':sh', $shares);
        $stmt->execute();

        // dr_acid = acid

        $descri = 'Manual Share Rectification ' . $notes;
        $ttypee = 'LIA';
        $pm = 'cash';
        $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, cr_acid, pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:pm, :acid)
            ';

        $stmt = $this->conn->prepare($sqlQuery);
        $mid =  0;
        $stmt->bindParam(':amount', $difference);
        $stmt->bindParam(':dc', $collection_date);
        $stmt->bindParam(':ttype', $ttypee);
        $stmt->bindParam(':descri', $descri);
        $stmt->bindParam(':pm', $pm);
        $stmt->bindParam(':_auth', $auth_id);
        $stmt->bindParam(':mid', $mid);
        $stmt->bindParam(':acid', $acid);
        $stmt->bindParam(':apby', $auth_id);
        $stmt->bindParam(':bran', $branch_id);
        $stmt->bindParam(':crid', $acid);

        $stmt->execute();

        $sqlQuery = 'UPDATE "Account" SET balance=balance+:diff where id=:uidd
';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':uidd', $acid);
        $stmt->bindParam(':diff', $difference);
        $stmt->execute();


        return true;
    }
    public function payAgentLoanCollections()
    {

        $sqlQuery = 'SELECT * FROM transactions where _status=1 AND agent_loan_amount>0 AND loan_payment_status=0 AND t_type=\'D\' ORDER BY tid ASC LIMIT 50
';

        $bid = '2bc3ecbd-a622-4093-b9d2-afa527717354
';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        foreach ($stmt as $row) {

            $sqlQuery = 'SELECT * FROM loan where account_id=:cid AND status NOT IN(5,6,0)  ORDER BY loan_no ASC
';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':cid', $row['mid']);
            $stmt->execute();
            $rown = $stmt->fetch();


            // make loan repayment
            $endpoint = "https://app.ucscucbs.net/backend/api/Bank/create_loan_repay.php";
            $url = $endpoint;
            $data = array(
                'lno'      => $rown['loan_no'],
                'amount'      => $row['agent_loan_amount'] ?? 0,
                'date_of_next_pay'      =>  date('Y-m-d H:i:s'),
                'collection_date'      => $row['date_created'],
                'balance'      => max(($rown['current_balance'] ?? 0) - ($row['agent_loan_amount'] ?? 0), 0),
                'interest'      => 0,
                'clear_loan'      => 0,
                'uid'      => $row['mid'],
                'notes'      => $row['description'],
                'pay_method'      => 'cash',
                'bank_acc'      => $row['bacid'],
                'cash_acc'      => $row['cash_acc'],
                'cheque_no'      => $row['cheque_no'],
                'send_sms'      => 0,
                'auth_id'      => $row['_authorizedby'],
                'clear_penalty'      => 0,
                'penalty_amount'      => 0,
                'branch'      => $row['_branch'],

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
            $responsen = file_get_contents($url, false, $context);
            $data = json_decode($responsen, true);
            // if ($data['success']) {
            //     return true;
            // }

            $sqlQuery = 'UPDATE transactions SET loan_payment_status=1 WHERE tid=:id
';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['tid']);
            $stmt->execute();
        }



        return true;
    }
    public function verifyPortalClient()
    {
        $sqlQuery = 'SELECT public."Client".id AS cid, public."User".id AS uid, mpin_default FROM public."Client" LEFT JOIN public."User" ON public."User".id=public."Client"."userId" WHERE public."Client".id=:id AND public."Client".mpin=:mpin ';

        $stmt = $this->conn->prepare($sqlQuery);
        $this->id = (int)$this->id;
        $this->bank_acc = (int)$this->bank_acc;
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':mpin', $this->bank_acc);

        $stmt->execute();
        return $stmt;
    }

    public function verifyPortalClientmPin()
    {
        $sqlQuery = 'SELECT public."Client".id AS cid, public."User".id AS uid FROM public."Client" LEFT JOIN public."User" ON public."User".id=public."Client"."userId" WHERE  public."Client".mpin=:mpin AND public."Client".membership_no=:id AND public."User"."primaryCellPhone"=:ph';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':mpin', $this->bank_acc);
        $stmt->bindParam(':id', $this->cash_acc);
        $stmt->bindParam(':ph', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function setClientmPin()
    {
        $sqlQuery = 'UPDATE public."Client" SET  mpin=:mpin , mpin_default=:md WHERE id=:ph';
        $md = 0;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':mpin', $this->cash_acc);
        $stmt->bindParam(':md', $md);
        $stmt->bindParam(':ph', $this->id);

        $stmt->execute();
        return true;
    }

    public function setClientDefaultMpin()
    {
        $sqlQuery = 'SELECT public."User".id AS uid, public."Client".id AS cid, TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \', public."User".shared_name)) AS client_name, "branchId" FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id  WHERE public."Client".membership_no=:id AND public."User"."primaryCellPhone"=:ph AND mpin IS NULL ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->bank_acc);
        $stmt->bindParam(':ph', $this->id);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row =   $stmt->fetch();
            $md = 1;
            $mpin = 0;
            $digits = 4;
            $mpin = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);


            $sqlQuery = 'UPDATE public."Client" SET mpin=:mpin, mpin_default=:md WHERE public."Client".id=:id ';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['cid']);
            $stmt->bindParam(':mpin', $mpin);
            $stmt->bindParam(':md', $md);

            $stmt->execute();


            // check for sms and send mobile_banking sms
            // check for deposit sms subscription and sms balance , then send sms
            $user_instance = new User($this->conn);
            $trxn_instance = new Transaction($this->conn);
            $smstype = $user_instance->getBankSMStypeStatus($row['branchId'], 'on_internet_banking_pin_set');

            if ($smstype != 0 && $smstype['s_status'] == 1) {


                //  start on on_deposit sms sending process

                $sms_price = 0;
                $senderid = '';
                // check sacco branch sms balance first 
                $sms_bal = $trxn_instance->checkBranchSMSBalance($row['branchId']);
                $prices = $trxn_instance->checkBankSMSPrice($row['branchId']);

                // check for senderid used , and get sms price
                $senderid = $trxn_instance->getBranchSenderid($row['branchId']);
                if ($senderid != '') {

                    $sms_price = $prices['sms_sender_id_price'];
                } else {
                    $sms_price = $prices['sms_price'];
                }
                if ($sms_bal > $sms_price || $sms_bal == $sms_price) {

                    // fill temp_body tags with the right info
                    if ($smstype['charge'] > 0 && !is_null($row['uid']) && $smstype['charged_to'] == 'client') {
                        $added_sms_charge = $smstype['charge'];
                    } else {
                        $added_sms_charge = 0;
                    }
                    $trxnDetails  = array(
                        "name" => $row['client_name'],
                        "mpin" => $mpin,
                        "charge" => $added_sms_charge,
                    );

                    $sms = $user_instance->decryptSMS($smstype['temp_body'], 'on_internet_banking_pin_set', $trxnDetails);

                    // get client's primary phone number
                    $phone = $trxn_instance->getClientPhone($row['uid'], '');

                    if ($phone) {
                        /* phone number array hold numbers , iterate & send to each number */

                        foreach ($phone as $value) {
                            // check if phone number has country code or not --use 256 by default
                            if ($value[0] == "0" || $value[0] == 0 || $value[0] == "7") {
                                if ($value[0] == "0" || $value[0] == 0) {
                                    $value = '256' . substr($value, 1);
                                } else {
                                    $value = '256' . $value;
                                }
                            }
                            // send sms
                            $res =  $trxn_instance->SendSMS($senderid, $value, $sms);
                        }

                        $sms_price = $sms_price * count($phone);

                        $smstype['charge'] = $smstype['charge'] * count($phone);

                        // check if sms sent successfully or not
                        // if success, then do the steps down , if false , then just insert into sms_outbox
                        if ($res = 'OK') {

                            if ($sms_price > 0) {
                                // offset from sacco branch balance
                                $trxn_instance->chargeBranchSMS($sms_price, $row['branchId']);
                            }
                            if ($smstype['charge'] > 0 && !is_null($row['uid']) && $smstype['charged_to'] == 'client') {
                                // offset from client account (if charge >0 ) 
                                $trxn_instance->chargeClientSMS($row['uid'], $smstype['charge']);

                                // get the chart account id , then create trxn in table transactions --- t_type = SMS
                                $acid =  $trxn_instance->getBranchSMSChargesAcc($row['branchId']);

                                // engange the create sms trxn method
                                $trxn_instance->createSMSChargeTrxn(
                                    $smstype['charge'],
                                    0,
                                    'SMS',
                                    'Internet Banking Subscription SMS Charge',
                                    0,
                                    $phone[0],
                                    $phone[0],
                                    $phone[0],
                                    $row['uid'] ?? 0,
                                    0 ?? 0,
                                    $row['branchId'],
                                    1,
                                    $acid,
                                    'saving',
                                    1
                                );
                            }
                            // get charge per sms
                            $my_charge = $smstype['charge'] / count($phone);
                            foreach ($phone as $value) {

                                // insert into sms_outbox for record purposes  with status sent
                                $trxn_instance->insertSMSOutBox($value, $sms, $senderid, (int)$row['uid'] ?? 0, (int)$my_charge, 'sent', 1, $row['branchId'], 'Internet Banking Subscription SMS');
                            }
                        } else {
                            // get charge per sms
                            $my_charge = $smstype['charge'] / count($phone);
                            foreach ($phone as $value) {
                                // insert into sms_outbox for record purposes  with not sent status
                                $trxn_instance->insertSMSOutBox($value, $sms, $senderid, (int)$row['uid'] ?? 0, (int)$my_charge, 'failed', 1, $row['branchId'], $res);
                            }
                        }
                    } else {
                        // no phone number found
                        /* no sms sending & charging */
                    }
                }
            }


            return true;
        }

        return false;
    }

    public function setClientDefaultMpin2()
    {
        $sqlQuery = 'SELECT *, public."User".id AS uid, public."Client".id AS cid, TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \', public."User".shared_name)) AS client_name, "branchId" FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id  WHERE public."Client"."userId"=:id  ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->bank_acc);
        // $stmt->bindParam(':ph', $this->id);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row =   $stmt->fetch();

            $mpin = 0;
            $digits = 4;
            $mpin = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);


            $sqlQuery = 'UPDATE public."Client" SET mpin=:mpin WHERE public."Client".id=:id ';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['cid']);
            $stmt->bindParam(':mpin', $mpin);

            $stmt->execute();

            $sqlQuery = 'UPDATE public."User" SET "primaryCellPhone"=:mpin WHERE public."User".id=:id ';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['uid']);
            $stmt->bindParam(':mpin', $this->id);

            $stmt->execute();


            // check for sms and send mobile_banking sms
            // check for deposit sms subscription and sms balance , then send sms
            $user_instance = new User($this->conn);
            $trxn_instance = new Transaction($this->conn);
            $smstype = $user_instance->getBankSMStypeStatus($row['branchId'], 'on_internet_banking_pin_set');

            if ($smstype != 0 && $smstype['s_status'] == 1) {


                //  start on on_deposit sms sending process

                $sms_price = 0;
                $senderid = '';
                // check sacco branch sms balance first 
                $sms_bal = $trxn_instance->checkBranchSMSBalance($row['branchId']);
                $prices = $trxn_instance->checkBankSMSPrice($row['branchId']);

                // check for senderid used , and get sms price
                $senderid = $trxn_instance->getBranchSenderid($row['branchId']);
                if ($senderid != '') {

                    $sms_price = $prices['sms_sender_id_price'];
                } else {
                    $sms_price = $prices['sms_price'];
                }
                if ($sms_bal > $sms_price || $sms_bal == $sms_price) {

                    // fill temp_body tags with the right info
                    if ($smstype['charge'] > 0 && !is_null($row['uid']) && $smstype['charged_to'] == 'client') {
                        $added_sms_charge = $smstype['charge'];
                    } else {
                        $added_sms_charge = 0;
                    }
                    $trxnDetails  = array(
                        "name" => $row['client_name'],
                        "mpin" => $mpin,
                        "charge" => $added_sms_charge,
                    );

                    $sms = $user_instance->decryptSMS($smstype['temp_body'], 'on_internet_banking_pin_set', $trxnDetails);

                    // get client's primary phone number
                    $phone = $trxn_instance->getClientPhone($row['uid'], '');

                    if ($phone) {
                        /* phone number array hold numbers , iterate & send to each number */

                        foreach ($phone as $value) {
                            // check if phone number has country code or not --use 256 by default
                            if ($value[0] == "0" || $value[0] == 0 || $value[0] == "7") {
                                if ($value[0] == "0" || $value[0] == 0) {
                                    $value = '256' . substr($value, 1);
                                } else {
                                    $value = '256' . $value;
                                }
                            }
                            // send sms
                            $res =  $trxn_instance->SendSMS($senderid, $value, $sms);
                        }

                        $sms_price = $sms_price * count($phone);

                        $smstype['charge'] = $smstype['charge'] * count($phone);

                        // check if sms sent successfully or not
                        // if success, then do the steps down , if false , then just insert into sms_outbox
                        if ($res = 'OK') {

                            if ($sms_price > 0) {
                                // offset from sacco branch balance
                                $trxn_instance->chargeBranchSMS($sms_price, $row['branchId']);
                            }
                            if ($smstype['charge'] > 0 && !is_null($row['uid']) && $smstype['charged_to'] == 'client') {
                                // offset from client account (if charge >0 ) 
                                $trxn_instance->chargeClientSMS($row['uid'], $smstype['charge']);

                                // get the chart account id , then create trxn in table transactions --- t_type = SMS
                                $acid =  $trxn_instance->getBranchSMSChargesAcc($row['branchId']);

                                // engange the create sms trxn method
                                $trxn_instance->createSMSChargeTrxn(
                                    $smstype['charge'],
                                    0,
                                    'SMS',
                                    'Internet Banking Subscription SMS Charge',
                                    0,
                                    $phone[0],
                                    $phone[0],
                                    $phone[0],
                                    $row['uid'] ?? 0,
                                    0 ?? 0,
                                    $row['branchId'],
                                    1,
                                    $acid,
                                    'saving',
                                    1
                                );
                            }
                            // get charge per sms
                            $my_charge = $smstype['charge'] / count($phone);
                            foreach ($phone as $value) {

                                // insert into sms_outbox for record purposes  with status sent
                                $trxn_instance->insertSMSOutBox($value, $sms, $senderid, (int)$row['uid'] ?? 0, (int)$my_charge, 'sent', 1, $row['branchId'], 'Internet Banking Subscription SMS');
                            }
                        } else {
                            // get charge per sms
                            $my_charge = $smstype['charge'] / count($phone);
                            foreach ($phone as $value) {
                                // insert into sms_outbox for record purposes  with not sent status
                                $trxn_instance->insertSMSOutBox($value, $sms, $senderid, (int)$row['uid'] ?? 0, (int)$my_charge, 'failed', 1, $row['branchId'], $res);
                            }
                        }
                    } else {
                        // no phone number found
                        /* no sms sending & charging */
                    }
                }
            }


            return true;
        }

        return false;
    }


    public function registerAsset()
    {
        // sanitize amount to remove commas

        $amount = str_replace(",", "", $this->details['amount']);



        $crid = null;
        $drid = null;

        $ttypee = 'ASS';

        // save the payment method such that incase you need to fetch the transactions you can know whether it's chart account or saving account
        $pm = '';
        /* dr_acid = the sender
        cr_acid = the receiver
        */
        if ($this->details['pay_method'] == 'cash_increase' || $this->details['pay_method'] == 'cash_decrease') {
            $sender = $this->details['cash_acc'];
            $acid = $this->details['cash_acc'];
            $pm = 'cash';
            if ($this->details['pay_method'] == 'cash_increase') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['cash_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();


                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['main_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();


                $crid = 'debit';
            }

            if ($this->details['pay_method'] == 'cash_decrease') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['cash_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();


                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['main_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();

                $crid = 'credit';
            }
        } else if ($this->details['pay_method'] == 'cheque_increase' || $this->details['pay_method'] == 'cheque_decrease') {
            $sender = $this->details['bank_acc'];
            $acid = $this->details['bank_acc'];
            $pm = 'cheque';

            if ($this->details['pay_method'] == 'cheque_increase') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['bank_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();


                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['main_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();

                $crid = 'debit';
            }

            if ($this->details['pay_method'] == 'cheque_decrease') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['bank_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();

                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['main_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();

                $crid = 'credit';
            }
        } else {
            $sender = $this->details['account_id'];
            $pm = 'saving';
            $acid = null;

            if ($this->details['pay_method'] == 'saving_increase') {
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:amount WHERE "userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['account_id']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();

                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['main_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();



                $descri = 'ASSET REGISTERED   ' . $this->details['comment'];

                $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch,pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pmethod, :acid)
            ';

                $stmt = $this->conn->prepare($sqlQuery);
                $mid = $this->details['account_id'];
                $ty = 'D';
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':dc', $this->details['date_of_p']);
                $stmt->bindParam(':ttype', $ty);
                $stmt->bindParam(':pmethod', $pm);
                $stmt->bindParam(':descri', $descri);
                $stmt->bindParam(':_auth', $this->details['user']);
                $stmt->bindParam(':mid', $mid);
                $stmt->bindParam(':acid', $acid);
                $stmt->bindParam(':apby', $this->details['user']);
                $stmt->bindParam(':bran', $this->details['branch']);

                $stmt->execute();

                $crid = 'debit';
            }

            if ($this->details['pay_method'] == 'saving_decrease') {
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:amount WHERE "userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['account_id']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();


                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['main_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();



                $descri = 'ASSET REGISTERED    ' . $this->details['comment'];

                $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch,pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pmethod, :acid)
            ';

                $stmt = $this->conn->prepare($sqlQuery);
                $mid = $this->details['account_id'];
                $ty = 'W';
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':dc', $this->details['date_of_p']);
                $stmt->bindParam(':ttype', $ty);
                $stmt->bindParam(':pmethod', $pm);
                $stmt->bindParam(':descri', $descri);
                $stmt->bindParam(':_auth', $this->details['user']);
                $stmt->bindParam(':mid', $mid);
                $stmt->bindParam(':acid', $acid);
                $stmt->bindParam(':apby', $this->details['user']);
                $stmt->bindParam(':bran', $this->details['branch']);

                $stmt->execute();

                $crid = 'credit';
            }
        }
        $descri = 'ASSET REGISTERED    ' . $this->details['comment'];
        if (
            $this->details['pay_method'] == 'cheque_decrease' || $this->details['pay_method'] == 'cash_decrease' || $this->details['pay_method'] == 'saving_decrease'
        ) {
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, cr_acid,cr_dr,pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:drid,:pmethod, :acid)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = $this->details['account_id'] ?? 0;
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['date_of_p']);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':pmethod', $pm);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);
            $stmt->bindParam(':crid', $this->details['main_acc']);
            $stmt->bindParam(':drid', $crid);

            $stmt->execute();
        } else {
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, dr_acid,cr_dr,pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:drid,:pmethod, :acid)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = $this->details['account_id'] ?? 0;
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['date_of_p']);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':pmethod', $pm);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);
            $stmt->bindParam(':crid', $this->details['main_acc']);
            $stmt->bindParam(':drid', $crid);

            $stmt->execute();
        }
        // insert into audit trail

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = $descri;
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['branch'];

        $auditTrail->log_message = 'Asset Registered: using' . $this->details['pay_method'] . ' a/c ' . $sender;
        $auditTrail->create();

        return true;
    }

    public function registerSatelliteTrxn()
    {
        // sanitize amount to remove commas

        $amount = str_replace(",", "", $this->details['amount']);





        $ttypee = 'LIA';

        // save the payment method such that incase you need to fetch the transactions you can know whether it's chart account or saving account
        $pm = '';
        /* dr_acid = the sender
        cr_acid = the receiver
        */

        $sender = $this->details['account_id'];
        $pm = 'saving';
        $acid = null;

        if ($this->details['pay_method'] == 'saving_increase') {
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:amount WHERE "userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['account_id']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();


            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['main_acc']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();



            $descri = 'SATELLITE TRXN   ' . $this->details['comment'];

            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch,pay_method, acid,is_satellite)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pmethod, :acid, :is_satellite)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = $this->details['account_id'];
            $ty = 'D';
            $is_satellite = 1;
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['date_of_p']);
            $stmt->bindParam(':ttype', $ty);
            $stmt->bindParam(':pmethod', $pm);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':is_satellite', $is_satellite);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);

            $stmt->execute();
        }

        if ($this->details['pay_method'] == 'saving_decrease') {
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:amount WHERE "userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['account_id']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();

            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['main_acc']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();

            $descri = 'SATELLITE TRXN    ' . $this->details['comment'];

            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch,pay_method, acid,is_satellite)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pmethod, :acid,:is_satellite)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = $this->details['account_id'];
            $ty = 'W';
            $is_satellite = 1;
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['date_of_p']);
            $stmt->bindParam(':ttype', $ty);
            $stmt->bindParam(':pmethod', $pm);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':is_satellite', $is_satellite);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);

            $stmt->execute();
        }

        $descri = 'SATELLITE TRXN    ' . $this->details['comment'];

        $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, cr_acid,pay_method, acid, is_satellite)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:pmethod, :acid,:is_satellite)
            ';

        $stmt = $this->conn->prepare($sqlQuery);
        $mid = $this->details['account_id'] ?? 0;
        $is_satellite = 1;
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':dc', $this->details['date_of_p']);
        $stmt->bindParam(':ttype', $ttypee);
        $stmt->bindParam(':pmethod', $pm);
        $stmt->bindParam(':descri', $descri);
        $stmt->bindParam(':_auth', $this->details['user']);
        $stmt->bindParam(':mid', $mid);
        $stmt->bindParam(':acid', $acid);
        $stmt->bindParam(':apby', $this->details['user']);
        $stmt->bindParam(':bran', $this->details['branch']);
        $stmt->bindParam(':crid', $this->details['main_acc']);
        $stmt->bindParam(':is_satellite', $is_satellite);

        $stmt->execute();

        // insert into audit trail

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = $descri;
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['branch'];

        $auditTrail->log_message = 'Satellit Trxn Registered: using ' . $this->details['main_acc'] . ' a/c ' . $sender;
        $auditTrail->create();

        return true;
    }


    public function registerLiability()
    {
        // sanitize amount to remove commas

        $amount = str_replace(",", "", $this->details['amount']);
        if (
            $this->details['pay_method'] == 'cheque_decrease' || $this->details['pay_method'] == 'cash_decrease' || $this->details['pay_method'] == 'saving_decrease'
        ) {
            $sqlQuery = 'UPDATE public."Account" SET dr_bal=dr_bal+:amount,balance=balance-:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['main_acc']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();
        } else {
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['main_acc']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();
        }


        $ttypee = 'LIA';

        /* dr_acid = the sender
        cr_acid = the receiver
        */
        // save the payment method such that incase you need to fetch the transactions you can know whether it's chart account or saving account
        $pm = '';
        /* dr_acid = the sender
        cr_acid = the receiver
        */
        if (
            $this->details['pay_method'] == 'cash_increase' || $this->details['pay_method'] == 'cash_decrease'
        ) {
            $sender = $this->details['cash_acc'];
            $acid = $this->details['cash_acc'];
            $pm = 'cash';
            if ($this->details['pay_method'] == 'cash_increase') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['cash_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();
            }

            if ($this->details['pay_method'] == 'cash_decrease') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['cash_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();
            }
        } else if ($this->details['pay_method'] == 'cheque_increase' || $this->details['pay_method'] == 'cheque_decrease') {
            $sender = $this->details['bank_acc'];
            $acid = $this->details['bank_acc'];
            $pm = 'cheque';

            if ($this->details['pay_method'] == 'cheque_increase') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['bank_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();
            }

            if ($this->details['pay_method'] == 'cheque_decrease') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['bank_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();
            }
        } else {
            $sender = $this->details['account_id'];
            $pm = 'saving';
            $acid = null;

            if ($this->details['pay_method'] == 'saving_increase') {
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:amount WHERE "userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['account_id']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();


                $descri = 'LIABILITY REGISTERED     ' . $this->details['comment'];
                $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pm, :acid)
            ';

                $stmt = $this->conn->prepare($sqlQuery);
                $mid = $this->details['account_id'];
                $ty = 'D';
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':dc', $this->details['date_of_p']);
                $stmt->bindParam(':ttype', $ty);
                $stmt->bindParam(':descri', $descri);
                $stmt->bindParam(':pm', $pm);
                $stmt->bindParam(':_auth', $this->details['user']);
                $stmt->bindParam(':mid', $mid);
                $stmt->bindParam(':acid', $acid);
                $stmt->bindParam(':apby', $this->details['user']);
                $stmt->bindParam(':bran', $this->details['branch']);

                $stmt->execute();
            }

            if ($this->details['pay_method'] == 'saving_decrease') {
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:amount WHERE "userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['account_id']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();

                $descri = 'LIABILITY REGISTERED     ' . $this->details['comment'];

                $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pm, :acid)
            ';

                $stmt = $this->conn->prepare($sqlQuery);
                $mid = $this->details['account_id'];
                $ty = 'W';
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':dc', $this->details['date_of_p']);
                $stmt->bindParam(':ttype', $ty);
                $stmt->bindParam(':descri', $descri);
                $stmt->bindParam(':pm', $pm);
                $stmt->bindParam(':_auth', $this->details['user']);
                $stmt->bindParam(':mid', $mid);
                $stmt->bindParam(':acid', $acid);
                $stmt->bindParam(':apby', $this->details['user']);
                $stmt->bindParam(':bran', $this->details['branch']);

                $stmt->execute();
            }
        }
        $descri = 'LIABILITY REGISTERED     ' . $this->details['comment'];

        if (
            $this->details['pay_method'] == 'cheque_decrease' || $this->details['pay_method'] == 'cash_decrease' || $this->details['pay_method'] == 'saving_decrease'
        ) {
            $cr_dr = 'debit';
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, dr_acid, pay_method, acid,cr_dr)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:pm, :acid,:cr_dr)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = $this->details['account_id'] ?? 0;
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':cr_dr', $cr_dr);
            $stmt->bindParam(':dc', $this->details['date_of_p']);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':pm', $pm);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);
            $stmt->bindParam(':crid', $this->details['main_acc']);
            // $stmt->bindParam(':drid', $sender);

            $stmt->execute();
        } else {
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, cr_acid, pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:pm, :acid)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = $this->details['account_id'] ?? 0;
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['date_of_p']);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':pm', $pm);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);
            $stmt->bindParam(':crid', $this->details['main_acc']);
            // $stmt->bindParam(':drid', $sender);

            $stmt->execute();
        }



        // insert into audit trail

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = $descri;
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['branch'];

        $auditTrail->log_message = 'Liability Registered: using ' . $this->details['pay_method'] . ' a/c ' . $sender;
        $auditTrail->create();

        return true;
    }

    public function registerBank()
    {
        $sqlQuery = 'INSERT INTO public."Bank" (name,"serialNumber","countryCode","lowestCurrencyValue",contact_person_details,recommender,location,trade_name,auto_chart) VALUES
        (:name,:sn,:cc,:lcv,:cp,:rec,:loc,:tname,:auto_chart)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':auto_chart', $this->auto_chart);
        $stmt->bindParam(':sn', $this->serialNumber);
        $stmt->bindParam(':cc', $this->countryCode);
        $stmt->bindParam(':lcv', $this->lowestCurrencyValue);
        $stmt->bindParam(':cp', $this->contact_person_details);
        $stmt->bindParam(':rec', $this->recommender);
        $stmt->bindParam(':loc', $this->location);
        $stmt->bindParam(':tname', $this->tname);

        $stmt->execute();
        return true;
    }

    public function registerLiability2()
    {
        // sanitize amount to remove commas

        $amount = str_replace(",", "", $this->details['amount']);

        $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['main_acc']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        $ttypee = 'LIA';

        /* dr_acid = the sender
        cr_acid = the receiver
        */
        // save the payment method such that incase you need to fetch the transactions you can know whether it's chart account or saving account
        $pm = '';
        /* dr_acid = the sender
        cr_acid = the receiver
        */
        if (
            $this->details['pay_method'] == 'cash_increase' || $this->details['pay_method'] == 'cash_decrease'
        ) {
            $sender = $this->details['cash_acc'];
            $acid = $this->details['cash_acc'];
            $pm = 'cash';
            if ($this->details['pay_method'] == 'cash_increase') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['cash_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();
            }

            if ($this->details['pay_method'] == 'cash_decrease') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['cash_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();
            }
        } else if ($this->details['pay_method'] == 'cheque_increase' || $this->details['pay_method'] == 'cheque_decrease') {
            $sender = $this->details['bank_acc'];
            $acid = $this->details['bank_acc'];
            $pm = 'cheque';

            if ($this->details['pay_method'] == 'cheque_increase') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['bank_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();
            }

            if ($this->details['pay_method'] == 'cheque_decrease') {
                $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['bank_acc']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();
            }
        } else {
            $sender = $this->details['account_id'];
            $pm = 'saving';
            $acid = null;

            if ($this->details['pay_method'] == 'saving_increase') {
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:amount WHERE "userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['account_id']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();


                $descri = 'LIABILITY REGISTERED     ' . $this->details['comment'];
                $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pm, :acid)
            ';

                $stmt = $this->conn->prepare($sqlQuery);
                $mid = $this->details['account_id'];
                $ty = 'D';
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':dc', $this->details['date_of_p']);
                $stmt->bindParam(':ttype', $ty);
                $stmt->bindParam(':descri', $descri);
                $stmt->bindParam(':pm', $pm);
                $stmt->bindParam(':_auth', $this->details['user']);
                $stmt->bindParam(':mid', $mid);
                $stmt->bindParam(':acid', $acid);
                $stmt->bindParam(':apby', $this->details['user']);
                $stmt->bindParam(':bran', $this->details['branch']);

                $stmt->execute();
            }

            if ($this->details['pay_method'] == 'saving_decrease') {
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:amount WHERE "userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->details['account_id']);
                $stmt->bindParam(':amount', $amount);
                $stmt->execute();

                $descri = 'LIABILITY REGISTERED     ' . $this->details['comment'];

                $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pm, :acid)
            ';

                $stmt = $this->conn->prepare($sqlQuery);
                $mid = $this->details['account_id'];
                $ty = 'W';
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':dc', $this->details['date_of_p']);
                $stmt->bindParam(':ttype', $ty);
                $stmt->bindParam(':descri', $descri);
                $stmt->bindParam(':pm', $pm);
                $stmt->bindParam(':_auth', $this->details['user']);
                $stmt->bindParam(':mid', $mid);
                $stmt->bindParam(':acid', $acid);
                $stmt->bindParam(':apby', $this->details['user']);
                $stmt->bindParam(':bran', $this->details['branch']);

                $stmt->execute();
            }
        }
        $descri = 'SATELLITE WITHDRAW     ' . $this->details['comment'];

        $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, dr_acid, pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:pm, :acid)
            ';

        $stmt = $this->conn->prepare($sqlQuery);
        $mid = $this->details['account_id'] ?? 0;
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':dc', $this->details['date_of_p']);
        $stmt->bindParam(':ttype', $ttypee);
        $stmt->bindParam(':descri', $descri);
        $stmt->bindParam(':pm', $pm);
        $stmt->bindParam(':_auth', $this->details['user']);
        $stmt->bindParam(':mid', $mid);
        $stmt->bindParam(':acid', $acid);
        $stmt->bindParam(':apby', $this->details['user']);
        $stmt->bindParam(':bran', $this->details['branch']);
        $stmt->bindParam(':crid', $this->details['main_acc']);
        // $stmt->bindParam(':drid', $sender);

        $stmt->execute();


        // insert into audit trail

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = $descri;
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['branch'];

        $auditTrail->log_message = 'Satellite Withdraw: using ' . $this->details['pay_method'] . ' a/c ' . $sender;
        $auditTrail->create();

        return true;
    }



    public function getAllBranchCreditors()
    {
        $sqlQuery = 'SELECT *, public."Branch".name AS bname, public."Account".name AS cname FROM public."creditors" LEFT JOIN public."Account" ON public."Account".id::text=public."creditors".cred_chart_acc LEFT JOIN public."Branch" ON public."creditors".branch_id=public."Branch".id WHERE public."creditors".branch_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }
    public function getAllBranchDebtors()
    {
        $sqlQuery = 'SELECT *, public."Branch".name AS bname, public."Account".name AS cname FROM public."debtors" LEFT JOIN public."Account" ON public."Account".id::text=public."debtors".deb_chart_acc LEFT JOIN public."Branch" ON public."debtors".branch_id=public."Branch".id WHERE public."debtors".branch_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllDebtorReceivables()
    {
        $sqlQuery = 'SELECT *, public."Branch".name AS bname FROM public."receivables"  LEFT JOIN public."Branch" ON public."receivables".p_branch_id=public."Branch".id WHERE public."receivables".p_creditor=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }
    public function getCreditorsDetails()
    {
        $sqlQuery = 'SELECT *, public."Account".name AS cname, public."Branch".name AS bname FROM public."creditors" LEFT JOIN public."Account" ON public."Account".id::text=public."creditors".cred_chart_acc LEFT JOIN public."Branch" ON public."creditors".branch_id=public."Branch".id WHERE public."creditors".cred_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getDebtorsDetails()
    {
        $sqlQuery = 'SELECT *, public."Branch".name AS bname, public."Account".name AS cname FROM public."debtors" LEFT JOIN public."Account" ON public."Account".id=public."debtors".deb_chart_acc::uuid LEFT JOIN public."Branch" ON public."debtors".branch_id=public."Branch".id WHERE public."debtors".deb_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankCreditors()
    {
        $sqlQuery = 'SELECT *, public."Branch".name AS bname, public."Account".name AS cname FROM public."creditors" LEFT JOIN public."Account" ON public."Account".id::text=public."creditors".cred_chart_acc LEFT JOIN public."Branch" ON public."creditors".branch_id=public."Branch".id WHERE public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankDebtors()
    {
        $sqlQuery = 'SELECT *, public."Branch".name AS bname, public."Account".name AS cname FROM public."debtors" LEFT JOIN public."Account" ON public."Account".id::text=public."debtors".deb_chart_acc LEFT JOIN public."Branch" ON public."debtors".branch_id=public."Branch".id WHERE public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankShareValue($id)
    {
        $sqlQuery = 'SELECT * FROM public."Bank" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }
    public function deleteSMSType()
    {
        $sqlQuery = 'UPDATE public."sms_types" SET deleted=1 WHERE st_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        return true;
    }

    public function getAllBranchShareValue($id)
    {
        $sqlQuery = 'SELECT * FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllSystemBranchesSMSWallets()
    {
        $sqlQuery = 'SELECT * FROM public."Branch" ';

        $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }
    public function getBranchSMSIncome($branch)
    {
        $tt = 'SMS';
        $sqlQuery = 'SELECT SUM(amount) AS tot FROM public."transactions" WHERE t_type=:tt AND _branch=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $branch);
        $stmt->bindParam(':tt', $tt);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'] ?? 0;
    }
    public function getAllBankBranchesSMSWallets()
    {
        $sqlQuery = 'SELECT * FROM public."Branch"  WHERE public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function updateBankShareValue($amount, $bank, $acid)
    {
        $sqlQuery = 'UPDATE public."Bank" SET share_value=:sv, share_acid=:acid WHERE public."Bank".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $bank);
        $stmt->bindParam(':sv', $amount);
        $stmt->bindParam(':acid', $acid);

        $stmt->execute();
        return true;
    }

    public function createRole()
    {

        if ($this->branch == '') {
            $sqlQuery = 'INSERT INTO public."Role" (name,description,"branchId","bankId") VALUES
            (:name,:sn,:br,:ban)';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':sn', $this->description);
            $stmt->bindParam(':br', $this->branch);
            $stmt->bindParam(':ban', $this->bank);

            $stmt->execute();
            // $last_id= $this->conn->lastInsertId();


        } else {
            $sqlQuery = 'INSERT INTO public."Role" (name,description,"branchId") VALUES
            (:name,:sn,:br)';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':sn', $this->description);
            $stmt->bindParam(':br', $this->branch);

            $stmt->execute();
            // $last_id= $this->conn->lastInsertId();
        }

        $sqlQuery = 'SELECT * FROM public."Role" ORDER BY "createdAt" DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
        $roles = $stmt->fetch();
        $last_id = $roles['id'];


        if (count($this->subPermissionArray) > 0) {

            foreach ($this->subPermissionArray as $row) {
                $sqlQueryn = 'INSERT INTO public."PermissionRole" ("permissionId","roleId") VALUES
                (:pid,:rid)';

                $stmt = $this->conn->prepare($sqlQueryn);

                $stmt->bindParam(':pid', $row);
                $stmt->bindParam(':rid', $last_id);

                $stmt->execute();
            }
        }
        if (count($this->mainPermissionArray) > 0) {

            foreach ($this->mainPermissionArray as $row) {
                $sqlQueryn = 'INSERT INTO public."MainPermissionRole" ("mainPermissionId","roleId") VALUES
                (:pid,:rid)';

                $stmt = $this->conn->prepare($sqlQueryn);

                $stmt->bindParam(':pid', $row);
                $stmt->bindParam(':rid', $last_id);

                $stmt->execute();
            }
        }


        return true;
    }

    public function registerBankAccount()
    {

        $sqlQuery = 'INSERT INTO public."bank_accounts" (bank_name,acc_name,acc_no,branchid,bankid) VALUES
        (:bname,:aname,:ano,:bid,:bank)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bname', $this->location);
        $stmt->bindParam(':aname', $this->name);
        $stmt->bindParam(':ano', $this->identificationNumber);
        $stmt->bindParam(':bid', $this->serialNumber);
        $stmt->bindParam(':bank', $this->id);

        $stmt->execute();
        $last_id = $this->conn->lastInsertId();
        // bank a/c chart of accounts
        //     $sqlQuery = 'INSERT INTO public."Account"(
        //     type, "branchId",name, description, "isSystemGenerated",bank_account)
        //    VALUES (:typee,:bid,:nname,:descr,:isgen ,:ba)';
        //     $atype = 'ASSETS';
        //     $nname = strtoupper($this->name) . ' - BANK ACCOUNT';
        //     $descr = 'This account represents Bank A/C of ' . strtolower($this->identificationNumber);
        //     $isgen = true;
        //     $stmt = $this->conn->prepare($sqlQuery);
        //     $stmt->bindParam(':typee', $atype);
        //     $stmt->bindParam(':bid', $this->serialNumber);
        //     $stmt->bindParam(':nname', $nname);
        //     $stmt->bindParam(':descr', $descr);
        //     $stmt->bindParam(':isgen', $isgen);
        //     $stmt->bindParam(':ba', $last_id);

        //     $stmt->execute();

        // get account details 

        $miid = $this->createdAt;

        $sqlQueryn = 'SELECT  COUNT(*) AS tot FROM public."Account" WHERE main_account_id=:id';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $miid);
        $stmtn->execute();
        $rown = $stmtn->fetch();

        $lastused = (int)$rown['tot'] ?? 0;
        $lastused = $lastused + 1;

        $sqlQueryx = 'SELECT account_code_used FROM public."Account" WHERE id=:id';
        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':id', $miid);
        $stmtx->execute();
        $rowx = $stmtx->fetch();
        // 
        $sqlQuery = 'INSERT INTO public."Account"(
        type, "branchId",name, description, "isSystemGenerated",bank_account,is_sub_account,main_account_id,auto_gen,parent_id_code,account_code_used,last_code_used)
       VALUES (:typee,:bid,:nname,:descr,:isgen ,:ba,:issub,:mainacc,:autogen,:pidcode,:acccodeused,:lastused)';
        $atype = 'ASSETS';
        $nname = $this->name;
        $descr = 'This account represents  Bank A/C of ' . strtolower($this->name);
        $isgen = true;
        $ismainacc = 1;

        $pidd = $rowx['account_code_used'];
        $codeused = $rowx['account_code_used'] . '-' . $lastused;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':typee', $atype);
        $stmt->bindParam(':bid', $this->serialNumber);
        $stmt->bindParam(':nname', $nname);
        $stmt->bindParam(':descr', $descr);
        $stmt->bindParam(':isgen', $isgen);
        $stmt->bindParam(':ba', $last_id);
        $stmt->bindParam(':issub', $isgen);
        $stmt->bindParam(':mainacc', $miid);
        $stmt->bindParam(':autogen', $ismainacc);
        $stmt->bindParam(':pidcode', $pidd);
        $stmt->bindParam(':acccodeused', $codeused);
        $stmt->bindParam(':lastused', $lastused);

        $stmt->execute();


        // $sqlQuery = 'UPDATE  public."Account" SET last_code_used=:lc WHERE id=:id';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $miid);
        // $stmt->bindParam(':lc', $lastused);

        // $stmt->execute();



        return true;
    }

    public function registerCashAccount()
    {
        $sqlQuery = 'SELECT * FROM public."Staff" WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        $row = $stmt->fetch();



        $sqlQuery = 'INSERT INTO public."staff_cash_accounts" (acc_name,userid,branchid) VALUES
        (:bname,:uidd,:bid)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bname', $this->name);
        $stmt->bindParam(':uidd', $this->id);
        $stmt->bindParam(':bid', $row['branchId']);

        $stmt->execute();
        $last_id = $this->conn->lastInsertId();
        // cash a/c chart of accounts

        // get account details 

        $miid = $this->createdAt;

        $sqlQueryn = 'SELECT  COUNT(*) AS tot FROM public."Account" WHERE main_account_id=:id';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $miid);
        $stmtn->execute();
        $rown = $stmtn->fetch();

        $lastused = (int)$rown['tot'] ?? 0;
        $lastused = $lastused + 1;

        $sqlQueryx = 'SELECT account_code_used FROM public."Account" WHERE id=:id';
        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':id', $miid);
        $stmtx->execute();
        $rowx = $stmtx->fetch();
        // 
        $sqlQuery = 'INSERT INTO public."Account"(
        type, "branchId",name, description, "isSystemGenerated",is_cash_account,is_sub_account,main_account_id,auto_gen,parent_id_code,account_code_used,last_code_used)
       VALUES (:typee,:bid,:nname,:descr,:isgen ,:ba,:issub,:mainacc,:autogen,:pidcode,:acccodeused,:lastused)';
        $atype = 'ASSETS';
        $nname = $this->name;
        $descr = 'This account represents Staff Cash A/C of ' . strtolower($this->name);
        $isgen = true;
        $ismainacc = 1;

        $pidd = $rowx['account_code_used'];
        $codeused = $rowx['account_code_used'] . '-' . $lastused;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':typee', $atype);
        $stmt->bindParam(':bid', $row['branchId']);
        $stmt->bindParam(':nname', $nname);
        $stmt->bindParam(':descr', $descr);
        $stmt->bindParam(':isgen', $isgen);
        $stmt->bindParam(':ba', $last_id);
        $stmt->bindParam(':issub', $isgen);
        $stmt->bindParam(':mainacc', $miid);
        $stmt->bindParam(':autogen', $ismainacc);
        $stmt->bindParam(':pidcode', $pidd);
        $stmt->bindParam(':acccodeused', $codeused);
        $stmt->bindParam(':lastused', $lastused);

        $stmt->execute();


        // $sqlQuery = 'UPDATE  public."Account" SET last_code_used=:lc WHERE id=:id';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $miid);
        // $stmt->bindParam(':lc', $lastused);

        // $stmt->execute();

        return true;
    }

    public function registerSafeAccount()
    {
        $sqlQuery = 'INSERT INTO public."safe_accounts" (name,branchid) VALUES
        (:bname,:bid)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bname', $this->name);
        $stmt->bindParam(':bid', $this->id);

        $stmt->execute();
        $last_id = $this->conn->lastInsertId();
        // cash a/c chart of accounts
        //     $sqlQuery = 'INSERT INTO public."Account"(
        //     type, "branchId",name, description, "isSystemGenerated",reserve_account)
        //    VALUES (:typee,:bid,:nname,:descr,:isgen ,:ba)';
        //     $atype = 'ASSETS';
        //     $nname = strtoupper($this->name) . ' - SAFE ACCOUNT';
        //     $descr = 'This account represents Reserve Safe A/C of ' . strtolower($this->name);
        //     $isgen = true;
        //     $stmt = $this->conn->prepare($sqlQuery);
        //     $stmt->bindParam(':typee', $atype);
        //     $stmt->bindParam(':bid', $this->id);
        //     $stmt->bindParam(':nname', $nname);
        //     $stmt->bindParam(':descr', $descr);
        //     $stmt->bindParam(':isgen', $isgen);
        //     $stmt->bindParam(':ba', $last_id);

        //     $stmt->execute();

        // get account details 

        $miid = $this->createdAt;

        $sqlQueryn = 'SELECT  COUNT(*) AS tot FROM public."Account" WHERE main_account_id=:id';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $miid);
        $stmtn->execute();
        $rown = $stmtn->fetch();

        $lastused = (int)$rown['tot'] ?? 0;
        $lastused = $lastused + 1;

        $sqlQueryx = 'SELECT account_code_used FROM public."Account" WHERE id=:id';
        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':id', $miid);
        $stmtx->execute();
        $rowx = $stmtx->fetch();
        // 
        $sqlQuery = 'INSERT INTO public."Account"(
        type, "branchId",name, description, "isSystemGenerated",reserve_account,is_sub_account,main_account_id,auto_gen,parent_id_code,account_code_used,last_code_used)
       VALUES (:typee,:bid,:nname,:descr,:isgen ,:ba,:issub,:mainacc,:autogen,:pidcode,:acccodeused,:lastused)';
        $atype = 'ASSETS';
        $nname = $this->name;
        $descr = 'This account represents Reserve A/C of ' . strtolower($this->name);
        $isgen = true;
        $ismainacc = 1;

        $pidd = $rowx['account_code_used'];
        $codeused = $rowx['account_code_used'] . ' - ' . $lastused;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':typee', $atype);
        $stmt->bindParam(':bid', $this->id);
        $stmt->bindParam(':nname', $nname);
        $stmt->bindParam(':descr', $descr);
        $stmt->bindParam(':isgen', $isgen);
        $stmt->bindParam(':ba', $last_id);
        $stmt->bindParam(':issub', $isgen);
        $stmt->bindParam(':mainacc', $miid);
        $stmt->bindParam(':autogen', $ismainacc);
        $stmt->bindParam(':pidcode', $pidd);
        $stmt->bindParam(':acccodeused', $codeused);
        $stmt->bindParam(':lastused', $lastused);

        $stmt->execute();



        return true;
    }
    public function registerBranch()
    {

        $sqlQuery = 'INSERT INTO public."Branch" (name,"serialNumber","bankId","identificationNumber",location,bcode) VALUES
        (:name,:sn,:id,:idno,:loc,:bcode)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':sn', $this->serialNumber);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':idno', $this->identificationNumber);
        $stmt->bindParam(':loc', $this->location);
        $stmt->bindParam(':bcode', $this->bcode);

        $stmt->execute();

        $sqlQueryn = 'SELECT public."Branch".id AS branch_id, public."Bank".auto_chart FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch"."serialNumber" =:sno';

        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':sno', $this->serialNumber);

        $stmtn->execute();
        $rown = $stmtn->fetch();
        // if ($rown['auto_chart']) {

            $sqlQuery = 'SELECT * FROM public."Account" WHERE auto_gen=1 AND "branchId" IS NULL';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();

            foreach ($stmt as $row) {
                $sqlQuery = 'INSERT INTO public."Account"(
              type, "branchId", name, description, "isSystemGenerated", lpid, said, feeid, is_cash_account, bank_account, balance, main_account_id, is_inter_branch, acc_id, reserve_account, is_payable, is_receivable, auto_gen, parent_id_code, account_code_used, last_code_used)
             VALUES (:type,:branch,:name,:descr,:sysgen,:lpid,:said,:fid,:cashacc,:bacc,:balance,:mainacc,:isinter,:accid,:reserve,:ispay,:isrec,:autogen,:parentid,:acccode,:lastcode)';
                $stmt = $this->conn->prepare($sqlQuery);

                $issub =  is_null($row['main_account_id']) ? false : true;
                $stmt->bindParam(':type', $row['type']);
                $stmt->bindParam(':branch', $rown['branch_id']);
                $stmt->bindParam(':name', $row['name']);
                $stmt->bindParam(':descr', $row['description']);
                $stmt->bindParam(':sysgen', $row['isSystemGenerated'], PDO::PARAM_BOOL);
                $stmt->bindParam(':lpid', $row['lpid']);
                $stmt->bindParam(':said', $row['said']);
                $stmt->bindParam(':fid', $row['feeid']);
                $stmt->bindValue(':cashacc', $row['is_cash_account'] === 't' ? 1 : 0, PDO::PARAM_INT);
                $stmt->bindParam(':bacc', $row['bank_account']);
                $stmt->bindParam(':balance', $row['balance']);
                $stmt->bindParam(':mainacc', $row['main_account_id']);
                $stmt->bindValue(':isinter', $row['is_inter_branch'] === 't' ? 1 : 0, PDO::PARAM_INT);
                $stmt->bindParam(':accid', $row['acc_id']);
                $stmt->bindParam(':reserve', $row['reserve_account']);
                $stmt->bindParam(':ispay', $row['is_payable'], PDO::PARAM_BOOL);
                $stmt->bindValue(':isrec', $row['is_receivable'] === 't' ? 1 : 0, PDO::PARAM_INT);
                $stmt->bindValue(':autogen', $row['auto_gen'] === 't' ? 1 : 0, PDO::PARAM_INT);
                $stmt->bindParam(':parentid', $row['parent_id_code']);
                $stmt->bindParam(':acccode', $row['account_code_used']);
                $stmt->bindParam(':lastcode', $row['last_code_used']);
 
                $stmt->execute();
            }


            // branch cash account
            $sqlQuery = 'INSERT INTO public."Account"(
                 type, "branchId",name, description, "isSystemGenerated")
                VALUES (:typee,:bid,:nname,:descr,:isgen )';
            $atype = 'ASSETS';
            $nname = strtoupper($this->name) . ' - CASH ACCOUNT';
            $descr = 'This account holds Cash of ' . strtolower($this->name);
            $isgen = true;
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':typee', $atype);
            $stmt->bindParam(':bid', $rown['branch_id']);
            $stmt->bindParam(':nname', $nname);
            $stmt->bindParam(':descr', $descr);
            $stmt->bindParam(':isgen', $isgen);

            $stmt->execute();


            // branch data importer account
            $sqlQuery = 'INSERT INTO public."Account"(
                 type, "branchId",name, description, "isSystemGenerated")
                VALUES (:typee,:bid,:nname,:descr,:isgen )';
            $atype = 'ASSETS';
            $nname = 'DATA IMPORTER';
            $descr = 'This account is debited for all data importer accounting entries of ' . strtolower($this->name);
            $isgen = true;
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':typee', $atype);
            $stmt->bindParam(':bid', $rown['branch_id']);
            $stmt->bindParam(':nname', $nname);
            $stmt->bindParam(':descr', $descr);
            $stmt->bindParam(':isgen', $isgen);

            $stmt->execute();


            // inter-branch account
            $sqlQuery = 'INSERT INTO public."Account"(
                 type, "branchId",name, description, "isSystemGenerated",is_inter_branch)
                VALUES (:typee,:bid,:nname,:descr,:isgen,:isinterbranch )';
            $atype = 'ASSETS';
            $nname = 'Inter-Branch Transactions A/C';
            $descr = 'This account is used for all inter-branch transactions for ' . strtolower($this->name);
            $isgen = true;
            $isinterbranch = 1;
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':typee', $atype);
            $stmt->bindParam(':bid', $rown['branch_id']);
            $stmt->bindParam(':nname', $nname);
            $stmt->bindParam(':descr', $descr);
            $stmt->bindParam(':isgen', $isgen);
            $stmt->bindParam(':isinterbranch', $isinterbranch);

            $stmt->execute();
        // }
        return true;
    }

    public function getAllBranchAccounts()
    {
        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Account"."branchId"=:id AND public."Account".acc_deleted=0';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchSubAccounts()
    {
        $sqlQuery = 'SELECT *, account.name AS aname,public."Branch".name AS bname,account.id AS aid, (SELECT COUNT(*) FROM public."Account" WHERE public."Account".main_account_id=account.id) AS subs FROM public."Account" AS account LEFT JOIN public."Branch" ON account."branchId"=public."Branch".id WHERE account."branchId"=:id AND account.acc_deleted=0 AND account.main_account_id IS NOT NULL';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllChartAccountLedgerTrxns($acc)
    {
        // get client names,left balance
        $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id  WHERE public."transactions".acid::uuid=:id OR public."transactions".cr_acid::uuid=:id OR public."transactions".dr_acid::uuid=:id';
        // $tt = "'STS','STT','TTS','TTT','TTB','BTB','BTS','STB','BRTBR'";
        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->bindParam(':id', $acc);
        // $transactions->bindParam(':tt', $tt);
        $transactions->execute();
        return $transactions;
    }

    public function getAllBranchAccountsLedger($acc)
    {
        if ($acc != 0) {
            $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE  public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acc);

            $stmt->execute();
            return $stmt;
        } else {
            $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Account"."branchId"=:id AND public."Account".acc_deleted=0';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->id);

            $stmt->execute();
            return $stmt;
        }
    }

    public function getAllBranchAccounts2()
    {
        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Account"."branchId"=:id AND public."Account".is_sub_account=:st AND public."Account".acc_deleted=0';

        $stmt = $this->conn->prepare($sqlQuery);
        $st = 'false';
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':st', $st);

        $stmt->execute();
        return $stmt;
    }
    public function getAllBranchChartAccounts2()
    {
        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Account"."branchId"=:id AND public."Account".is_sub_account=:st AND public."Account".acc_deleted=0 AND public."Account".main_account_id IS NULL';

        $stmt = $this->conn->prepare($sqlQuery);
        $st = 'false';
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':st', $st);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankAccounts()
    {

        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:id AND public."Account".acc_deleted=0 ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getClientRangeTransactionsBFEnd2($id, $start, $end)
    {


        $sqlQuery = 'SELECT SUM(amount) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'D\',\'A\',\'LC\',\'E\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':transaction_start_date', $end);

        $stmt->execute();

        $row = $stmt->fetch();
        $debit = $row['tot1'] ?? 0;


        $sqlQuery = 'SELECT SUM(amount) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'W\',\'LE\',\'C\',\'CW\',\'CS\',\'SMS\',\'LP\',\'RC\',\'I\',\'R\',\'L\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':transaction_start_date', $end);

        $stmt->execute();

        $rown = $stmt->fetch();
        $credit = $rown['tot1'] ?? 0;

        $sqlQuery = 'SELECT SUM(loan_interest) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'L\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':transaction_start_date', $end);

        $stmt->execute();

        $rowx = $stmt->fetch();
        $credit2 = $rowx['tot1'] ?? 0;

        $total = $debit - $credit - $credit2;

        return $total ?? 0;
    }

    public function getLoanPrincipalPaidPeriod($id, $start, $end)
    {

        $sqlQuery = 'SELECT SUM(amount) AS princ_paid_month  FROM public."transactions" WHERE public."transactions".t_type=\'L\' AND public."transactions".loan_id=:id AND DATE(public."transactions".date_created) >= :filter_pay_start_date AND DATE(public."transactions".date_created) <= :filter_pay_end_date
        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':filter_pay_start_date', $start);
        $stmt->bindParam(':filter_pay_end_date', $end);

        $stmt->execute();

        $row = $stmt->fetch();
        $total = $row['princ_paid_month'] ?? 0;
        return $total;
    }

    public function hasCollateral($id)
    {

        $has_collateral = 'No';
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."collaterals" WHERE public."collaterals".loanid=:id 
        ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $row = $stmt->fetch();
        $total = $row['tot'] ?? 0;

        if ($total > 0) {
            $has_collateral = 'Yes';
        } else {
            $has_collateral = 'No';
        }

        return $has_collateral;
    }
    public function getLoanInterestPaidPeriod($id, $start, $end)
    {
        $sqlQuery = 'SELECT SUM(loan_interest) AS int_paid_month FROM public."transactions" WHERE public."transactions".t_type=\'L\' AND public."transactions".loan_id=:id AND DATE(public."transactions".date_created) >= :filter_pay_start_date AND DATE(public."transactions".date_created) <= :filter_pay_end_date
        ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':filter_pay_start_date', $start);
        $stmt->bindParam(':filter_pay_end_date', $end);

        $stmt->execute();

        $row = $stmt->fetch();
        $total = $row['int_paid_month'] ?? 0;
        return $total;
        return 0;
    }

    public function getChartAccValue($id, $start, $end, $bank, $branch)
    {

        $total_amount = 0;
        if ($id == 'allcash' || $id == 'allsafe') {
            if ($id == 'allcash') {
                $sqlQuery = 'SELECT SUM(balance) AS tot FROM public."Account" WHERE  public."Account".is_cash_account>0 AND "branchId" IN(SELECT id from "Branch" where "bankId"=:id) ';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $bank);

                $stmt->execute();
                $row = $stmt->fetch();

                $total_amount = $row['tot'];
            } else {
                $sqlQuery = 'SELECT SUM(balance) AS tot FROM public."Account" WHERE  public."Account".reserve_account>0 AND "branchId" IN(SELECT id from "Branch" where "bankId"=:id) ';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $bank);

                $stmt->execute();
                $row = $stmt->fetch();

                $total_amount = $row['tot'];
            }
        } else {
            $sqlQuery = 'SELECT * FROM public."Account" WHERE  public."Account".id=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $id);

            $stmt->execute();
            $row = $stmt->fetch();


            // ASSETS
            if ($row['type'] == 'ASSETS') {

                // cash accounts & safe accounts & bank accounts --- jus pick the account balance
                if (@$row['is_cash_account'] > 0 || @$row['reserve_account'] > 0 || @$row['bank_account'] > 0 || @$row['is_mobile_banking_wallet'] > 0 || @$row['is_receivable'] > 0) {

                    if (@$branch) {
                        $total_amount = @$row['balance'] ?? 0;
                    }

                    if (@$bank && (@$branch == '')) {

                        if (@$row['is_mobile_banking_wallet'] > 0) {
                            $sqlQuery = 'SELECT SUM(balance) AS tot FROM public."Account" WHERE  public."Account".is_mobile_banking_wallet>0 AND "branchId" IN(SELECT id from "Branch" where "bankId"=:id) ';

                            $stmt = $this->conn->prepare($sqlQuery);

                            $stmt->bindParam(':id', $bank);

                            $stmt->execute();
                            $rowm = $stmt->fetch();

                            $total_amount = $rowm['tot'];
                        }


                        if (@$row['bank_account'] > 0) {
                            $sqlQuery = 'SELECT SUM(balance) AS tot FROM public."Account" WHERE  public."Account".bank_account =:bc AND "branchId" IN(SELECT id from "Branch" where "bankId"=:id) ';

                            $stmt = $this->conn->prepare($sqlQuery);

                            $stmt->bindParam(':id', $bank);
                            $stmt->bindParam(':bc', $row['bank_account']);

                            $stmt->execute();
                            $rowb = $stmt->fetch();

                            $total_amount = $rowb['tot'];
                        }


                        if (@$row['is_receivable'] > 0) {
                            $sqlQuery = 'SELECT SUM(balance) AS tot FROM public."Account" WHERE  public."Account".is_receivable>0 AND "branchId" IN(SELECT id from "Branch" where "bankId"=:id) ';

                            $stmt = $this->conn->prepare($sqlQuery);

                            $stmt->bindParam(':id', $bank);

                            $stmt->execute();
                            $rowr = $stmt->fetch();

                            $total_amount = $rowr['tot'];
                        }
                    }
                } else if (@$row['is_over_draft'] > 0) {
                    if (@$branch) {
                        $total_amount = @$row['balance'] ?? 0;
                    }

                    if (@$bank && (@$branch == '')) {
                        $sqlQuery = 'SELECT SUM(balance) AS tot FROM public."Account" WHERE  public."Account".is_over_draft>0 AND "branchId" IN(SELECT id from "Branch" where "bankId"=:id) ';

                        $stmt = $this->conn->prepare($sqlQuery);

                        $stmt->bindParam(':id', $bank);

                        $stmt->execute();
                        $rowo = $stmt->fetch();

                        $total_amount = $rowo['tot'];
                    }
                }

                // for loan products under assets
                else if (@$row['lpid'] > 0) {
                    // get the total loan disbursement amount for a given product minus(-) the total principal amount paid on that given product
                    $binding_array = [];

                    // computed repaid principal
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions LEFT JOIN loan ON transactions.loan_id=loan.loan_no where loan.loan_type=:id  AND transactions.t_type=\'L\' ';

                    $binding_array[':id'] = $row['lpid'];

                    if (@$branch) {
                        $sqlQuery .= ' AND transactions._branch=:bid ';
                        $binding_array[':bid'] = $branch;
                    }

                    if (@$bank && (@$branch == '')) {
                        $sqlQuery .= ' AND transactions._branch IN(SELECT id FROM "Branch" WHERE "bankId"=:bk) ';
                        $binding_array[':bk'] = $bank;
                    }



                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $principal_paid = @$rown['tot3'] ?? 0;

                    // compute disbursed total
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions LEFT JOIN loan ON transactions.loan_id=loan.loan_no where loan.loan_type=:id  AND transactions.t_type=\'A\' ';

                    $binding_array[':id'] = $row['lpid'];
                    if (@$branch) {
                        $sqlQuery .= ' AND transactions._branch=:bid ';
                        $binding_array[':bid'] = $branch;
                    }

                    if (@$bank && (@$branch == '')) {
                        $sqlQuery .= ' AND transactions._branch IN(SELECT id FROM "Branch" WHERE "bankId"=:bk) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $disbursed_amount = @$rown['tot3'] ?? 0;

                    // compute total disbursed from imported loans
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(data_importer_loan_batch_records.loan_amount) AS tot3 from data_importer_loan_batch_records LEFT JOIN loan ON data_importer_loan_batch_records.id=loan.batch_record_id where loan.loan_type=:id AND data_importer_loan_batch_records.import_status=\'true\' ';

                    $binding_array[':id'] = $row['lpid'];
                    if (@$branch) {
                        $sqlQuery .= ' AND loan.branchid=:bid ';
                        $binding_array[':bid'] = $branch;
                    }

                    if (@$bank && (@$branch == '')) {
                        $sqlQuery .= ' AND loan.branchid IN(SELECT id FROM "Branch" WHERE "bankId"=:bk) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(data_importer_loan_batch_records.disbursement_date) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $disbursed_amount_importer = $rown['tot3'] ?? 0;

                    $total_amount = ($disbursed_amount + $disbursed_amount_importer) - $principal_paid;
                } else {
                    // fetch trxn balance
                    // ADJE, LIA, ASS

                    //  get all plus trxns total
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $plus_tot = $rown['tot3'] ?? 0;


                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.dr_acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $minus_tot = $rown['tot3'] ?? 0;

                    $total_amount = $plus_tot - $minus_tot;
                }
            }
            if ($row['type'] == 'INCOMES') {
                if ((@$row['lpid'] > 0) && (!str_contains(@$row['name'], 'Penalty'))) {
                    // get the total interest amount in the loan repayment trxns of the given loan product
                    $binding_array = [];

                    // computed repaid interest amount
                    $sqlQuery = 'SELECT SUM(loan_interest) AS tot3 from transactions LEFT JOIN loan ON transactions.loan_id=loan.loan_no where loan.loan_type=:id AND transactions.t_type=\'L\' ';

                    $binding_array[':id'] = $row['lpid'];
                    // $binding_array[':bid'] = $row['branchId'];
                    if (@$branch) {
                        $sqlQuery .= ' AND transactions._branch=:bid ';
                        $binding_array[':bid'] = @$branch;
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  AND (transactions._branch IN(SELECT public."Branch".id FROM public."Branch" WHERE "bankId"=:bk))  ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $interest_paid = @$rown['tot3'] ?? 0;


                    // fetch trxn balance
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot4 from transactions  where t_type=\'I\' AND  ';

                    // $binding_array[':id'] = $row['id'];
                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id OR transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }


                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $trxn_interest_paid_too = @$rown['tot4'] ?? 0;



                    $total_amount = $interest_paid + $trxn_interest_paid_too;
                } else if ((@$row['lpid'] > 0) && (str_contains(@$row['name'], 'Penalty'))) {
                    // fetch trxn balance
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where transactions.t_type=\'I\'  ';


                    if (@$branch) {
                        $sqlQuery .= ' AND (transactions.acid::text=:id OR transactions.cr_acid=:id OR transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  AND (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }



                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $total_amount = @$rown['tot3'] ?? 0;
                } else {
                    // fetch trxn balance
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot4 from transactions  where  ';

                    // $binding_array[':id'] = $row['id'];
                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id OR transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }


                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $total_amount = @$rown['tot4'] ?? 0;
                }
            }
            if ($row['type'] == 'CAPITAL') {

                if ($row['is_share_acc'] > 0) {
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(share_amount) AS tot3 from "share_register" where userid >0 ';


                    if (@$branch) {
                        $sqlQuery .= ' AND branch_id=:bid ';
                        $binding_array[':bid'] = @$branch;
                    }

                    if (@$bank && (@$branch == '' or @$branch == 0)) {
                        $sqlQuery .= ' AND branch_id IN(SELECT id from "Branch" where "bankId"=:bid) ';
                        $binding_array[':bid'] = $bank;
                    }


                    if (@$end) {
                        $sqlQuery .= '  AND DATE(date_added) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);

                    $rown = $stmt->fetch();

                    $total_amount = @$rown['tot3'] ?? 0;
                    // } else if ($row['is_retained_earning'] > 0) {

                    //     // fetch trxn balance
                    //     $binding_array = [];
                    //     $sqlQuery = 'SELECT SUM(amount) AS tot from transactions  where  ';

                    //     // $binding_array[':id'] = $row['id'];
                    //     if (@$branch) {
                    //         $sqlQuery .= ' (transactions.acid=:id OR transactions.cr_acid::uuid=:id) ';
                    //         $binding_array[':id'] = $row['id'];
                    //     }

                    //     if (@$bank && (@$branch == '')) {

                    //         $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                    //         $binding_array[':bk'] = $bank;
                    //     }


                    //     if (@$end) {
                    //         $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                    //         $binding_array[':transaction_end_date'] = @$end;
                    //     }

                    //     $stmt = $this->conn->prepare($sqlQuery);
                    //     $stmt->execute($binding_array);
                    //     $rowx = $stmt->fetch();

                    //     $tot1 = @$rowx['tot'] ?? 0;

                    //     // fetch trxn balance
                    //     $binding_array = [];
                    //     $sqlQuery = 'SELECT SUM(amount) AS tot from transactions  where  ';

                    //     // $binding_array[':id'] = $row['id'];
                    //     if (@$branch) {
                    //         $sqlQuery .= ' ( transactions.dr_acid::uuid=:id) ';
                    //         $binding_array[':id'] = $row['id'];
                    //     }

                    //     if (@$bank && (@$branch == '')) {

                    //         $sqlQuery .= '  ( transactions.dr_acid::uuid IN(SELECT public."Account".id FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                    //         $binding_array[':bk'] = $bank;
                    //     }


                    //     if (@$end) {
                    //         $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                    //         $binding_array[':transaction_end_date'] = @$end;
                    //     }

                    //     $stmt = $this->conn->prepare($sqlQuery);
                    //     $stmt->execute($binding_array);
                    //     $rowx = $stmt->fetch();

                    //     $tot2 = @$rowx['tot'] ?? 0;

                    //     $total_amount = $tot1 - $tot2;
                } else {

                    //  get all plus trxns total
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rowx = $stmt->fetch();

                    $plus_tot = $rowx['tot3'] ?? 0;


                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.dr_acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rownx = $stmt->fetch();

                    $minus_tot = $rownx['tot3'] ?? 0;

                    $total_amount = $plus_tot - $minus_tot;
                }
            }
            if ($row['type'] == 'LIABILITIES') {

                if ($row['said'] > 0) {
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(acc_balance + freezed_amount) AS tot3 from "Client" where actype=:id ';

                    $binding_array[':id'] = $row['said'];

                    if (@$branch) {
                        $sqlQuery .= ' AND "branchId"=:bid ';
                        $binding_array[':bid'] = $branch;
                    }

                    if (@$bank && (@$branch == '')) {
                        $sqlQuery .= ' AND "branchId" IN(SELECT id FROM "Branch" WHERE "bankId"=:bk) ';
                        $binding_array[':bk'] = $bank;
                    }


                    $stmt = $this->conn->prepare($sqlQuery);
                    // $stmt->bindParam(':id', $row['said']);
                    // $stmt->bindParam(':bid', $row['branchId']);
                    // $stmt->execute();
                    $stmt->execute($binding_array);

                    $rown = $stmt->fetch();

                    $total_amount = @$rown['tot3'] ?? 0;
                } else if ($row['is_fixed_acc'] > 0) {
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(fd_amount) AS tot3 from "fixed_deposits" where fd_status=0 ';

                    if (@$branch) {
                        $sqlQuery .= ' AND fd_branch=:bid ';
                        $binding_array[':bid'] = $row['branchId'];
                    }

                    if (@$bank && (@$branch == '')) {
                        $sqlQuery .= ' AND fd_branch IN(SELECT id FROM "Branch" WHERE "bankId"=:bk) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(fd_date) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);

                    $rown = $stmt->fetch();

                    $total_amount = @$rown['tot3'] ?? 0;
                } else if ($row['is_wht'] > 0) {
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(wht_paid) AS tot3 from "fixed_deposits" where fd_status=1 ';

                    if (@$branch) {
                        $sqlQuery .= ' AND fd_branch=:bid ';
                        $binding_array[':bid'] = $row['branchId'];
                    }

                    if (@$bank && (@$branch == '')) {
                        $sqlQuery .= ' AND fd_branch IN(SELECT id FROM "Branch" WHERE "bankId"=:bk) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(fd_maturity_date) >= :transaction_start_date AND DATE(fd_maturity_date) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);

                    $rown = $stmt->fetch();

                    $val1 = @$rown['tot3'] ?? 0;

                    //  get all plus trxns total
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where transactions.t_type IN(\'LIA\',\'W\') AND  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rowx = $stmt->fetch();

                    $plus_tot = $rowx['tot3'] ?? 0;


                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where transactions.t_type IN(\'D\',\'LIA\') AND ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.dr_acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rownx = $stmt->fetch();

                    $minus_tot = $rownx['tot3'] ?? 0;

                    $total_amount = ($plus_tot - $minus_tot) + $val1;
                } else {

                    //  get all plus trxns total
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rowx = $stmt->fetch();

                    $plus_tot = $rowx['tot3'] ?? 0;


                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.dr_acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rownx = $stmt->fetch();

                    $minus_tot = $rownx['tot3'] ?? 0;

                    $total_amount = $plus_tot - $minus_tot;
                }
            }
            if ($row['type'] == 'EXPENSES') {

                if ($row['is_fixed_int'] > 0) {
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(fd_int_paid) AS tot3 from "fixed_deposits" where fd_status=1 ';

                    if (@$branch) {
                        $sqlQuery .= ' AND fd_branch=:bid ';
                        $binding_array[':bid'] = $row['branchId'];
                    }

                    if (@$bank && (@$branch == '')) {
                        $sqlQuery .= ' AND fd_branch IN(SELECT id FROM "Branch" WHERE "bankId"=:bk) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(fd_maturity_date) >= :transaction_start_date AND DATE(fd_maturity_date) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);

                    $rown = $stmt->fetch();

                    $val1 = @$rown['tot3'] ?? 0;

                    //  get all plus trxns total
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where transactions.t_type IN(\'D\',\'E\') AND  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rowx = $stmt->fetch();

                    $plus_tot = $rowx['tot3'] ?? 0;


                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where transactions.t_type IN(\'W\',\'E\') AND ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.dr_acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rownx = $stmt->fetch();

                    $minus_tot = $rownx['tot3'] ?? 0;

                    $total_amount = ($plus_tot - $minus_tot) + $val1;
                } else  if ($row['is_saving_int'] > 0) {
                    $binding_array = [];


                    //  get all plus trxns total
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where transactions.t_type IN(\'D\',\'E\') AND  ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rowx = $stmt->fetch();

                    $plus_tot = $rowx['tot3'] ?? 0;


                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions  where transactions.t_type IN(\'W\',\'E\') AND ';

                    if (@$branch) {
                        $sqlQuery .= ' (transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (@$bank && (@$branch == '')) {

                        $sqlQuery .= '  (transactions.dr_acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rownx = $stmt->fetch();

                    $minus_tot = $rownx['tot3'] ?? 0;

                    $total_amount = ($plus_tot - $minus_tot);
                } else {

                    // fetch trxn balance
                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(amount) AS tot from transactions  where  ';

                    // $binding_array[':id'] = $row['id'];
                    if (@$branch) {
                        $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id OR transactions.dr_acid=:id) ';
                        $binding_array[':id'] = $row['id'];
                    }

                    if (
                        @$bank && (@$branch == '')
                    ) {

                        $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
                        $binding_array[':bk'] = $bank;
                    }


                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = @$start;
                        $binding_array[':transaction_end_date'] = @$end;
                    }

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rowx = $stmt->fetch();

                    $total_amount = @$rowx['tot'] ?? 0;
                }
            }
            if ($row['type'] == 'SUSPENSES') {
                if (@$branch) {
                    $total_amount = $row['balance'] ?? 0;
                }
                if (
                    @$bank && (@$branch == '')
                ) {

                    $sqlQuery = 'SELECT SUM(balance) AS tot_bal FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\' ';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':bk', $bank);

                    $stmt->execute();
                    $row = $stmt->fetch();

                    $total_amount = $row['tot_bal'] ?? 0;
                }
                // $total_amount = $row['balance'] ?? 0;


            }
        }




        return $total_amount;
    }

    public function getAllBankSubAccounts()
    {

        $sqlQuery = 'SELECT *, account.name AS aname,public."Branch".name AS bname,account.id AS aid, (SELECT COUNT(*) FROM public."Account" WHERE public."Account".main_account_id=account.id) AS subs FROM public."Account" AS account LEFT JOIN public."Branch" ON account."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:id AND account.acc_deleted=0 AND account.main_account_id IS NOT NULL';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankAccountsLedger($acc)
    {
        if ($acc != 0) {

            $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE  public."Account".id=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acc);

            $stmt->execute();
            return $stmt;
        } else {
            $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:id AND public."Account".acc_deleted=0 ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->id);

            $stmt->execute();
            return $stmt;
        }
    }
    public function getAllBankAccounts2()
    {

        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:id AND public."Account".is_sub_account =:st AND public."Account".acc_deleted=0 ';

        $stmt = $this->conn->prepare($sqlQuery);
        $st = 'false';
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':st', $st);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankChartAccounts2()
    {

        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:id AND public."Account".is_sub_account =:st AND public."Account".acc_deleted=0 AND  public."Account".main_account_id IS NULL ';

        $stmt = $this->conn->prepare($sqlQuery);
        $st = 'false';
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':st', $st);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchCashAccounts()
    {
        $sqlQuery = 'SELECT *, public."staff_cash_accounts".id AS sidd FROM public."staff_cash_accounts" LEFT JOIN public."Branch" ON public."staff_cash_accounts".branchid=public."Branch".id 
        WHERE public."staff_cash_accounts".branchid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankCashAccounts()
    {

        $sqlQuery = 'SELECT *, public."staff_cash_accounts".id AS sidd FROM public."staff_cash_accounts" LEFT JOIN public."Branch" ON public."staff_cash_accounts".branchid=public."Branch".id 
        WHERE public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankStaffCashAccounts()
    {

        $sqlQuery = 'SELECT *, public."staff_cash_accounts".id AS sidd, public."Branch".name AS bname FROM public."staff_cash_accounts" LEFT JOIN public."Branch" ON public."staff_cash_accounts".branchid=public."Branch".id 
        WHERE public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }



    public function getAllBankSafeAccounts()
    {

        $sqlQuery = 'SELECT *, public."safe_accounts".id AS sidd, public."safe_accounts".name AS acc_name,  public."Branch".name AS bname FROM public."safe_accounts" LEFT JOIN public."Branch" ON public."safe_accounts".branchid=public."Branch".id 
        WHERE public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchSafeAccounts()
    {

        $sqlQuery = 'SELECT *, public."safe_accounts".id AS sidd, public."safe_accounts".name AS acc_name, public."Account".id AS cid,  public."Branch".name AS bname FROM public."safe_accounts" LEFT JOIN public."Branch" ON public."safe_accounts".branchid=public."Branch".id LEFT JOIN public."Account" ON public."Account".bank_account = public."safe_accounts".id  
        WHERE public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchStaffCashAccounts()
    {


        $sqlQuery = 'SELECT *, public."staff_cash_accounts".id AS sidd, public."Branch".name AS bname FROM public."staff_cash_accounts" LEFT JOIN public."Branch" ON public."staff_cash_accounts".branchid=public."Branch".id 
        WHERE public."Branch"."bankId" IN(SELECT "bankId" FROM "Branch" WHERE id=:id)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getStaffDetails($id)
    {

        $sqlQuery = 'SELECT * FROM public."User" 
        WHERE public."User".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            return $row['firstName'] . ' ' . $row['lastName'];
        }
        return '';
    }

    public function getAllBranchBankAccounts()
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'SELECT *, public."bank_accounts".id AS bid, public."Branch".name AS bname FROM public."bank_accounts" LEFT JOIN public."Branch" ON public."Branch".id = public."bank_accounts".branchid WHERE ac_status=1  AND  (public."Branch"."bankId"=:id OR public."bank_accounts".bankid=:id)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $row['bankId']);

        $stmt->execute();
        return $stmt;
    }

    public function getAccountBalance($id)
    {
        $sqlQuery = 'SELECT SUM(balance) AS tot FROM public."Account"  WHERE bank_account=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['tot'] ?? 0;
    }
    public function getReserveAccountBalance($id)
    {
        $sqlQuery = 'SELECT balance FROM public."Account"  WHERE reserve_account=:id ORDER BY acc_id DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['balance'] ?? 0;
    }

    public function getCashAccountBalance($id)
    {
        $sqlQuery = 'SELECT balance FROM public."Account"  WHERE is_cash_account=:id ORDER BY acc_id DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['balance'] ?? 0;
    }
    public function checkIsMainAcc($id)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."Account"  WHERE main_account_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['tot'] ?? 0;
    }
    public function getAccountCid($id)
    {
        $sqlQuery = 'SELECT id FROM public."Account"  WHERE bank_account=:id ORDER BY acc_id DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['id'] ?? '';
    }
    public function getReserveAccountCid($id)
    {
        $sqlQuery = 'SELECT id FROM public."Account"  WHERE reserve_account=:id ORDER BY acc_id DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['id'] ?? '';
    }
    public function getCashAccountCid($id)
    {
        $sqlQuery = 'SELECT id FROM public."Account"  WHERE is_cash_account=:id ORDER BY acc_id DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['id'] ?? '';
    }

    public function getAllBranchMobileAccounts()
    {
        $sqlQuery = 'SELECT *, public."mobile_accounts".id AS bid FROM public."mobile_accounts" WHERE branchid=:id AND ac_status=1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankBankAccounts()
    {

        $sqlQuery = 'SELECT * , public."bank_accounts".id AS bid, public."Branch".name AS bname FROM public."bank_accounts" LEFT JOIN public."Branch" ON public."bank_accounts".branchid=public."Branch".id  
        WHERE public."bank_accounts".ac_status=1 AND  (public."Branch"."bankId"=:id OR public."bank_accounts".bankid=:id)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankMobileAccounts()
    {

        $sqlQuery = 'SELECT * , public."mobile_accounts".id AS bid FROM public."mobile_accounts" LEFT JOIN public."Branch" ON public."mobile_accounts".branchid=public."Branch".id
        WHERE public."Branch"."bankId"=:id OR public."mobile_accounts".bankid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getBankTransactionsDatatables()
    {
        $binding_array = [];
        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."Account" ON public."transactions".acid=public."Account".id
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id ';

        if (@$this->branchId) {
            $sqlQuery .= ' WHERE public."transactions"._branch=:branch_id ';
            $binding_array[':branch_id'] = $this->branchId;
        } else {
            $sqlQuery .= ' WHERE public."Branch"."bankId"=:bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
        }

        $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
        $binding_array[':limit'] = $this->filter_per_page;
        $binding_array[':offset'] = $this->filter_page;

        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBranchTransactions()
    {

        $branch_id = $this->id ?? $this->branchId;
        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."Account" ON public."transactions".acid=public."Account".id
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."transactions"._branch=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $branch_id);

        $stmt->execute();
        return $stmt;
    }


    public function getAllBankTransactions()
    {

        $bank_id = $this->id ?? $this->bankId;
        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."Account" ON public."transactions".acid=public."Account".id
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $bank_id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchTransactionsExpenses()
    {
        // $tt = 'I';
        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."transactions"._branch=:id AND public."transactions".t_type=\'E\'  ';
        $binding_array[':id'] = $this->id;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }
    public function getAllBranchTransactionsAssets()
    {
        // $tt = 'I';
        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."transactions"._branch=:id AND public."transactions".t_type=\'ASS\'  ';
        $binding_array[':id'] = $this->id;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }
    public function getAllBranchTransactionsLiabilities()
    {
        // $tt = 'I';
        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."transactions"._branch=:id AND public."transactions".t_type=\'LIA\'  ';
        $binding_array[':id'] = $this->id;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }
    public function getAllBranchTransactionsCapitals()
    {
        // $tt = 'I';
        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."transactions"._branch=:id AND public."transactions".t_type=\'CAP\'  ';
        $binding_array[':id'] = $this->id;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }


    public function getAllBankTransactionsExpenses($tt = 'E')
    {
        // $tt = 'I';

        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."Branch"."bankId"=:id AND public."transactions".t_type=:tt ';
        $binding_array[':id'] = $this->id;
        $binding_array[':tt'] = $tt;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }

    public function getAllBankTransactionsAssets($tt = 'ASS')
    {
        // $tt = 'I';

        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."Branch"."bankId"=:id AND public."transactions".t_type=:tt ';
        $binding_array[':id'] = $this->id;
        $binding_array[':tt'] = $tt;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }

    public function getAllBankTransactionsLiabilities($tt = 'LIA')
    {
        // $tt = 'I';

        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."Branch"."bankId"=:id AND public."transactions".t_type=:tt ';
        $binding_array[':id'] = $this->id;
        $binding_array[':tt'] = $tt;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }
    public function getAllBankTransactionsCapitals($tt = 'CAP')
    {
        // $tt = 'I';

        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."Branch"."bankId"=:id AND public."transactions".t_type=:tt ';
        $binding_array[':id'] = $this->id;
        $binding_array[':tt'] = $tt;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }

    public function getChartAccountName($id)
    {

        $sqlQuery = 'SELECT  public."Account".name AS aname FROM  public."Account" WHERE CONCAT(public."Account".id,\'\') = :id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['aname'] ?? '';
    }


    public function getAllBranchTransactionsIncomes()
    {
        // $tt = 'I';
        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."transactions"._branch=:id AND public."transactions".t_type=\'I\'  ';
        $binding_array[':id'] = $this->id;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }

    public function getAllBranchTransactionsAjes()
    {
        // $tt = 'I';
        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."transactions"._branch=:id AND public."transactions".t_type=\'AJE\'  ';
        $binding_array[':id'] = $this->id;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId OR public."transactions".dr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }

    public function getAllBranchTransactionsCharges()
    {

        $sqlQuery = 'SELECT * FROM public."transaction_charges" LEFT JOIN public."Branch" ON public."transaction_charges".bankid=public."Branch"."bankId"
        WHERE public."Branch".id=:id AND public."transaction_charges".c_status=1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }
    public function getAllBankTransactionsCharges()
    {


        $sqlQuery = 'SELECT * FROM public."transaction_charges" WHERE bankid=:id AND public."transaction_charges".c_status=1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }


    public function getAllBankTransactionsIncomes($tt = 'I')
    {
        // $tt = 'I';

        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."Branch"."bankId"=:id AND public."transactions".t_type=:tt ';
        $binding_array[':id'] = $this->id;
        $binding_array[':tt'] = $tt;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }

    public function getAllBankTransactionsAjes($tt = 'AJE')
    {
        // $tt = 'I';

        $sqlQuery = 'SELECT *, public."Branch".name AS bname,public."transactions".description AS tdescription FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
        LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id
        WHERE public."Branch"."bankId"=:id AND public."transactions".t_type=:tt ';
        $binding_array[':id'] = $this->id;
        $binding_array[':tt'] = $tt;

        /**
         * fitler by chart account
         */
        if (@$this->account_id) {
            // CONCAT(public."Account".id,\'\')
            $sqlQuery .= ' AND public."transactions".cr_acid=:MemberShipUserId OR public."transactions".dr_acid=:MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->account_id . '';
        }
        /**
         * filter by date
         */
        if ($this->createdAt && $this->deletedAt) {
            $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :from_date AND DATE(public."transactions".date_created) <= :end_date ';
            $binding_array[':from_date'] = $this->createdAt;
            $binding_array[':end_date'] = $this->deletedAt;
        }


        $sqlQuery .= ' ORDER BY public."transactions".tid DESC ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt;
    }

    public function registerSavingsAccount()
    {
        // create savings account
        $sqlQuery = 'INSERT INTO public."savingaccounts" (name,ucode,rate,rateper,bankid,rate_disbursement,min_balance,opening_balance) VALUES
        (:name,:ucode,:rate,:rateper,:bid,:disburse,:minb,:opening)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':ucode', $this->location);
        $stmt->bindParam(':rate', $this->contact_person_details);
        $stmt->bindParam(':rateper', $this->recommender);
        $stmt->bindParam(':bid', $this->id);
        $stmt->bindParam(':disburse', $this->createdAt);
        $stmt->bindParam(':minb', $this->updatedAt);
        $stmt->bindParam(':opening', $this->bcode);

        $stmt->execute();
        $sid = $this->conn->lastInsertId();

        // create account for the savings account
        if ($this->pv == 'exist') {
            $sqlQuery = 'SELECT * FROM public."Account" WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->deletedAt);

            $stmt->execute();
            $rown = $stmt->fetch();

            $sqlQuery = 'UPDATE public."Account" SET said=:fid WHERE account_code_used=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':fid', $sid);
            $stmt->bindParam(':id', $this->deletedAt);

            $stmt->execute();
        } else {

            $miid = $this->description;

            $sqlQueryn = 'SELECT  COUNT(*) AS tot FROM public."Account" WHERE main_account_id=:id';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $miid);
            $stmtn->execute();
            $rown = $stmtn->fetch();

            $lastused = (int)$rown['tot'] ?? 0;
            $lastused = $lastused + 1;

            $sqlQueryx = 'SELECT account_code_used FROM public."Account" WHERE id=:id';
            $stmtx = $this->conn->prepare($sqlQueryx);
            $stmtx->bindParam(':id', $miid);
            $stmtx->execute();
            $rowx = $stmtx->fetch();

            $codeused = $rowx['account_code_used'] . '-' . $lastused;

            // create account for the fees account
            $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->id);

            $stmt->execute();
            foreach ($stmt as $row) {

                // fees income account
                $sqlQuery = 'INSERT INTO public."Account"(
     type, "branchId",name, description, "isSystemGenerated",said, main_account_id, account_code_used)
    VALUES (:typee,:bid,:nname,:descr,:isgen,:said, :mainacc, :acode )';
                $atype = 'ASSETS';
                $nname = strtoupper($this->name);
                $descr = 'This account holds trxns amount for ' . strtolower($this->name);
                $isgen = true;
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':typee', $atype);
                $stmt->bindParam(':bid', $row['id']);
                $stmt->bindParam(':nname', $nname);
                $stmt->bindParam(':descr', $descr);
                $stmt->bindParam(':isgen', $isgen);
                $stmt->bindParam(':said', $sid);
                $stmt->bindParam(
                    ':mainacc',
                    $this->description
                );
                $stmt->bindParam(':acode', $codeused);

                $stmt->execute();
            }
        }


        return true;
    }

    public function createSubAccount()
    {


        $sqlQuery = 'INSERT INTO public."Account"(
    type, "branchId",name, description)
   VALUES (:typee,:bid,:nname,:descr )';
        $atype = $this->createdAt;
        $nname = $this->name;
        $descr = $this->location;
        // $isgen = false;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':typee', $atype);
        $stmt->bindParam(':bid', $this->contact_person_details);
        $stmt->bindParam(':nname', $nname);
        $stmt->bindParam(':descr', $descr);
        // $stmt->bindParam(':isgen', $isgen);

        $stmt->execute();


        // insert into audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Created Chart Account';
        $auditTrail->staff_id = $this->details['id'];
        $auditTrail->bank_id = $this->recommender;
        $auditTrail->branch_id = $this->contact_person_details;

        $auditTrail->log_message = 'Created Chart Account (' . $this->name . ') under: ' . $this->createdAt . ' - ' . $this->location;
        $auditTrail->create();




        return true;
    }
    public function getUserTotalPendingDeposits()
    {
        $tt = 'D';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:tt AND _authorizedby=:id AND _status=0 AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getUserTotalRepayments()
    {
        $tt = 'L';
        $sqlQuery = 'SELECT SUM(amount + loan_interest) AS total FROM public."transactions" WHERE t_type=:tt  AND _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        $tot = $row['total'] ?? 0;

        $sqlQuery = 'SELECT SUM(agent_loan_amount) AS total FROM public."transactions" WHERE  _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $rown = $stmt->fetch();
        $tot1 =   $rown['total'] ?? 0;

        return ($tot + $tot1);
    }

    public function getBankDepositsToday()
    {
        $tt = 'D';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt AND _status=1  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBankAgentDepositsToday()
    {
        $tt = 'D';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt AND _status=0  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getUserTotalPendingRepayments()
    {
        $tt = 'D';
        $sqlQuery = 'SELECT SUM(agent_loan_amount) AS total FROM public."transactions" WHERE t_type=:tt AND _authorizedby=:id AND _status=0 AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }


    public function getUserTotalDeposits()
    {
        $tt = 'D';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:tt  AND _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getChartGenderData($startDate, $endDate, $branch, $bank, $user, $period)
    {

        // Gender distribution
        $genderQuery = 'SELECT gender, COUNT(*) as count FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id WHERE public."User"."createdAt" BETWEEN :start AND :end';

        if ($branch && $branch != 'all') {
            $genderQuery .= ' AND public."Client"."branchId" = :branch';
        }

        if ($bank && $branch == 'all') {
            $genderQuery .= ' AND public."Client"."branchId" IN(SELECT id FROM public."Branch" WHERE "bankId"=:bank)';
        }

        $genderQuery .= " GROUP BY gender";

        $stmt = $this->conn->prepare($genderQuery);
        $stmt->bindParam(':start', $startDate);
        $stmt->bindParam(':end', $endDate);
        if ($branch && $branch != 'all') $stmt->bindParam(':branch', $branch);
        if ($bank && $branch == 'all') $stmt->bindParam(':bank', $bank);

        $stmt->execute();
        $genderData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $genderData;
    }

    public function getChartAgeData($startDate, $endDate, $branch, $bank, $user, $period)
    {
        // Age group distribution
        $ageQuery = '
        SELECT CASE 
            WHEN AGE(public."User"."dateOfBirth")::text < \'18 years\' THEN \'Under 18\'
            WHEN AGE(public."User"."dateOfBirth")::text BETWEEN \'18 years\' AND \'35 years\' THEN \'18-35\'
            WHEN AGE(public."User"."dateOfBirth")::text BETWEEN \'36 years\' AND \'60 years\' THEN \'36-60\'
            ELSE \'Above 60\' END as age_group, 
            COUNT(*) as count 
        FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id 
        WHERE public."User"."createdAt" BETWEEN :start AND :end';


        if ($branch && $branch != 'all') {
            $ageQuery .= ' AND public."Client"."branchId" = :branch';
        }

        if (
            $bank && $branch == 'all'
        ) {
            $ageQuery .= ' AND public."Client"."branchId" IN(SELECT id FROM public."Branch" WHERE "bankId"=:bank)';
        }
        $ageQuery .= " GROUP BY age_group";

        $stmt = $this->conn->prepare($ageQuery);
        $stmt->bindParam(':start', $startDate);
        $stmt->bindParam(':end', $endDate);
        if ($branch && $branch != 'all') $stmt->bindParam(':branch', $branch);
        if ($bank && $branch == 'all') $stmt->bindParam(':bank', $bank);

        $stmt->execute();
        $ageData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ageData;
    }

    public function getChartOccupationData($startDate, $endDate, $branch, $bank, $user, $period)
    {
        // Occupation distribution
        $occupationQuery = 'SELECT profession, COUNT(*) as count FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id WHERE public."User"."createdAt" BETWEEN :start AND :end';
        if ($branch && $branch != 'all') {
            $occupationQuery .= ' AND public."Client"."branchId" = :branch';
        }

        if (
            $bank && $branch == 'all'
        ) {
            $occupationQuery .= ' AND public."Client"."branchId" IN(SELECT id FROM public."Branch" WHERE "bankId"=:bank)';
        }

        $occupationQuery .= " GROUP BY profession ORDER BY count DESC LIMIT 12";

        $stmt = $this->conn->prepare($occupationQuery);
        $stmt->bindParam(':start', $startDate);
        $stmt->bindParam(':end', $endDate);
        if ($branch && $branch != 'all') $stmt->bindParam(':branch', $branch);
        if ($bank && $branch == 'all') $stmt->bindParam(':bank', $bank);
        $stmt->execute();
        $occupationData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $occupationData;
    }


    public function getCharteducationData($startDate, $endDate, $branch, $bank, $user, $period)
    {
        // Education level distribution
        $educationQuery = 'SELECT education_level, COUNT(*) as count FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id WHERE public."User"."createdAt" BETWEEN :start AND :end';


        if ($branch && $branch != 'all') {
            $educationQuery .= ' AND public."Client"."branchId" = :branch';
        }

        if (
            $bank && $branch == 'all'
        ) {
            $educationQuery .= ' AND public."Client"."branchId" IN(SELECT id FROM public."Branch" WHERE "bankId"=:bank)';
        }
        $educationQuery .= " GROUP BY education_level";

        $stmt = $this->conn->prepare($educationQuery);
        $stmt->bindParam(':start', $startDate);
        $stmt->bindParam(':end', $endDate);
        if ($branch && $branch != 'all') $stmt->bindParam(':branch', $branch);
        if ($bank && $branch == 'all') $stmt->bindParam(':bank', $bank);
        $stmt->execute();
        $educationData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $educationData;
    }

    public function getMembershipStatisticsProductData($startDate, $endDate, $branch, $bank, $user, $period)
    {
        $sqlQuery = '
        SELECT 
            b.name AS branch_name, 
            COUNT(m.id) AS member_count
        FROM 
            public."Client" m
        INNER JOIN 
            public.savingaccounts b ON m.actype = b.id
        WHERE 
            m."createdAt" >= :startDate 
            AND m."createdAt" <= :endDate 
    ';

        // Adding filters for branch and bank
        if ($branch && $branch != 'all') {
            $sqlQuery .= ' AND b.bankid = (SELECT "bankId" FROM public."Branch" WHERE public."Branch".id=:branch)::text ';
        }
        if ($bank) {
            $sqlQuery .= ' AND b.bankid = :bank';
        }

        $sqlQuery .= ' GROUP BY b.name ORDER BY b.name ASC';

        $stmt = $this->conn->prepare($sqlQuery);

        // Bind parameters
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        if ($branch && $branch != 'all') {
            $stmt->bindParam(':branch', $branch);
        }
        if ($bank) {
            $stmt->bindParam(':bank', $bank);
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }



    public function getMembershipStatisticsBranchData($startDate, $endDate, $branch, $bank, $user, $period)
    {
        $sqlQuery = '
        SELECT 
            b.name AS branch_name,
            COUNT(c.id) AS total_members 
        FROM 
            public."Client" c
        LEFT JOIN 
            public."Branch" b ON c."branchId" = b.id
        WHERE 
            DATE(c."createdAt") BETWEEN :start_date AND :end_date
    ';

        $bindingParams = [
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ];

        if (@$branch && @$branch != 'all') {
            $sqlQuery .= ' AND c."branchId" = :branch_id';
            $bindingParams[':branch_id'] = $branch;
        }

        if (@$bank && @$branch == 'all') {
            $sqlQuery .= ' AND b."bankId" = :bank_id';
            $bindingParams[':bank_id'] = $bank;
        }

        $sqlQuery .= ' GROUP BY b.name ORDER BY b.name';

        $stmt = $this->conn->prepare($sqlQuery);

        // foreach ($bindingParams as $key => $value) {
        //     $stmt->bindParam($key, $value);
        // }
        $stmt->execute($bindingParams);


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getChartIncomeExpenseData($startDate, $endDate, $branch, $bank, $user, $period)
    {
        // Education level distribution with updated income calculation
        $educationQuery = '
        SELECT 
            to_char(date_trunc(\'month\', date_created::timestamp), \'YYYY-MM\') AS month,
            SUM(
                CASE 
                    WHEN t_type IN (\'I\', \'R\') THEN amount
                    WHEN t_type = \'L\' THEN loan_interest
                    ELSE 0 
                END
            ) AS incomes,
            SUM(CASE WHEN t_type = \'E\' THEN amount ELSE 0 END) AS expenses,
            SUM(
                CASE 
                    WHEN t_type IN (\'I\', \'R\') THEN amount
                    WHEN t_type = \'L\' THEN loan_interest
                    ELSE 0 
                END
            ) - 
            SUM(CASE WHEN t_type = \'E\' THEN amount ELSE 0 END) AS net_income
        FROM 
            public.transactions
        WHERE 
            date_created::timestamp >= date_trunc(\'month\', CURRENT_DATE::timestamp) - INTERVAL \'12 months\'
    ';

        // Add branch filter if provided
        if ($branch && $branch != 'all') {
            $educationQuery .= ' AND public.transactions._branch = :branch';
        }

        // Add bank filter if provided
        if ($bank && $branch == 'all') {
            $educationQuery .= '
            AND public.transactions._branch IN (
                SELECT id 
                FROM public."Branch" 
                WHERE "bankId" = :bank
            )';
        }

        // Group and order by month
        $educationQuery .= '
        GROUP BY month
        ORDER BY month';

        // Prepare the query
        $stmt = $this->conn->prepare($educationQuery);

        // Bind parameters dynamically
        if ($branch && $branch != 'all') {
            $stmt->bindParam(':branch', $branch);
        }
        if ($bank && $branch == 'all') {
            $stmt->bindParam(':bank', $bank);
        }

        // Execute the query
        $stmt->execute();
        $educationData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $educationData;
    }


    public function getBankTotalMMDeposits()
    {

        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id WHERE public."Branch"."bankId"=:id AND  pay_method IN(\'mobile_money\',\'flutterwave\') AND t_type=\'D\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBankTotalMMWithdraws()
    {

        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id WHERE public."Branch"."bankId"=:id AND  pay_method IN(\'mobile_money\',\'flutterwave\') AND t_type=\'W\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBankAgentRepaymentsToday()
    {
        $tt = 'D';
        $sqlQuery = 'SELECT SUM(agent_loan_amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt AND _status=0  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBankRepaymentsToday()
    {
        $tt = 'L';
        $sqlQuery = 'SELECT SUM(amount + loan_interest) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }


    // public function getBankDepositsToday()
    // {
    //     $tt = 'D';
    //     $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
    //     $stmt = $this->conn->prepare($sqlQuery);
    //     $stmt->bindParam(':tt', $tt);
    //     $stmt->bindParam(':id', $this->bank);

    //     $stmt->execute();
    //     $row = $stmt->fetch();
    //     return $row['total'] ?? 0;
    // }

    public function getBankWithdrawsToday()
    {
        $tt = 'W';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBankDisbursementsToday()
    {
        $tt = 'A';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBankExpensesToday()
    {
        $tt = 'E';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBankIncomesToday()
    {
        $tt = 'I';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type IN(\'I\',\'R\')  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'SELECT SUM(loan_interest) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type IN(\'L\')  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $rown = $stmt->fetch();
        $total = ($row['total'] ?? 0) + ($rown['total'] ?? 0);
        return $total;
    }

    public function getUserTotalExpenses()
    {
        $tt = 'E';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:tt  AND _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getUserTotalIncomes()
    {
        $tt = 'I';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type IN(\'I\',\'R\')  AND _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getUserCashBalance()
    {

        $sqlQuery = 'SELECT *, public."Account".balance AS bal FROM public."staff_cash_accounts" LEFT JOIN public."Account" ON public."Account".is_cash_account=public."staff_cash_accounts".id 
    WHERE public."staff_cash_accounts".userid=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->user);
        // $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bal'] ?? 0;
    }

    public function getBankCashBalances()
    {

        $sqlQuery = 'SELECT *, public."Account".balance AS bal FROM public."staff_cash_accounts" LEFT JOIN public."Account" ON public."Account".is_cash_account=public."staff_cash_accounts".id  LEFT JOIN public."Branch" ON public."staff_cash_accounts".branchid = public."Branch".id 
    WHERE  public."Branch"."bankId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':id', $this->user);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bal'] ?? 0;
    }
    public function getUserTotalWithdraws()
    {
        $tt = 'W';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:tt  AND _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getUserTotalDisbursements()
    {
        $tt = 'A';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:tt  AND _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchClientsSavingBalances()
    {


        $sqlQuery = 'SELECT SUM(acc_balance) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getTotalBranchClientsFreezedBalances()
    {


        $sqlQuery = 'SELECT SUM(freezed_amount) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchLoanCollectionProgres()
    {

        $t1 = 0;
        $t2 = 0;
        $sqlQuery = 'SELECT SUM(amount + loan_interest) AS total FROM public."transactions" WHERE public."transactions"._branch=:bid AND t_type=\'L\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        $t1 =
            $row['total'] ?? 0;


        $sqlQuery = 'SELECT SUM(total_loan_amount) AS total FROM public."loan" WHERE public."loan".branchid=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        $t2 =
            $row['total'] ?? 0;
        if ($t2 == 0) {
            $t2 = 1;
        }
        return intval(($t1 / $t2) * 100);
    }

    public function getTotalBankLoanCollectionProgres()
    {

        $t1 = 0;
        $t2 = 0;
        $sqlQuery = 'SELECT SUM(amount + loan_interest) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE public."Branch"."bankId"=:bid AND t_type=\'L\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        $t1 =
            $row['total'] ?? 0;


        $sqlQuery = 'SELECT SUM(total_loan_amount) AS total FROM public."loan" LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id WHERE public."Branch"."bankId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        $t2 =
            $row['total'] ?? 0;
        if ($t2 == 0) {
            $t2 = 1;
        }
        return intval(($t1 / $t2) * 100);
    }

    public function getTotalBranchWeeklySavs($typ)
    {
        $sqlQuery = 'SELECT amount FROM public."transactions" WHERE t_type=:tt AND _branch=:bid  ORDER BY tid ASC LIMIT 7';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);
        $stmt->bindParam(':tt', $typ);

        $stmt->execute();
        // $row = $stmt->fetch();
        return $stmt;
    }

    public function getTotalBranchClients2()
    {
        // $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
        // $stmtn = $this->conn->prepare($sqlQueryn);
        // $stmtn->bindParam(':id', $this->branch);

        // $stmtn->execute();
        // $row = $stmtn->fetch();

        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
    public function getTotalBankClients2()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }

    public function getTotalBankWeeklySavs($typ)
    {
        $sqlQuery = 'SELECT amount FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt AND public."Branch"."bankId"=:bid  ORDER BY tid ASC LIMIT 7';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);
        $stmt->bindParam(':tt', $typ);

        $stmt->execute();
        // $row = $stmt->fetch();
        return $stmt;
    }


    public function getTotalBankClientsSavingBalances()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT SUM(acc_balance) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + ($row['total'] ?? 0);
        }

        return $mytot;
    }

    public function getTotalBankClientsFreezedBalances()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT SUM(freezed_amount) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + ($row['total'] ?? 0);
        }

        return $mytot;
    }



    public function getTotalBranchTypeClients2($type)
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid  AND client_type=:tt';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);
        $stmt->bindParam(':tt', $type);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getTotalUserTypeClients($type)
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client".savings_officer=:bid  AND client_type=:tt';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->user);
        $stmt->bindParam(':tt', $type);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }


    public function getTotalBranchSMSBanking()
    {
        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid AND public."Client".message_consent=1 ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);
        // $stmt->bindParam(':tt', $type);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchMobileBanking()
    {
        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid AND public."Client".mpin>\'0\' ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);
        // $stmt->bindParam(':tt', $type);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBankSMSBanking()
    {
        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid AND public."Client".message_consent=1 ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);
        // $stmt->bindParam(':tt', $type);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBankMobileBanking()
    {
        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid AND public."Client".mpin>\'0\' ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);
        // $stmt->bindParam(':tt', $type);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getTotalBankBirthDays()
    {
        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."User" ON public."User".id=public."Client"."userId" WHERE public."Branch"."bankId"=:bid AND DATE(public."User"."dateOfBirth")=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);
        // $stmt->bindParam(':tt', $type);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchBirthDays()
    {
        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client"  LEFT JOIN public."User" ON public."User".id=public."Client"."userId" WHERE public."Client"."branchId"=:bid AND DATE(public."User"."dateOfBirth")=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);
        // $stmt->bindParam(':tt', $type);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBankTypeClients2($type)
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid AND client_type=:tt';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);
            $stmt->bindParam(':tt', $type);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }


    public function getTotalBranchShareHolders()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."share_register" WHERE branch_id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
    public function getTotalBranchShareAmount()
    {


        $sqlQuery = 'SELECT SUM(share_amount) AS total FROM public."share_register" WHERE branch_id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBankShareHolders()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."share_register" WHERE branch_id=:bid ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }

    public function getTotalBranchOverDrafts()
    {
        $mytot = 0;

        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."over_drafts" WHERE status=1 AND branch=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        $mytot =  $row['total'] ?? 0;


        return $mytot ?? 0;
    }

    public function getTotalBankOverDrafts()
    {
        $mytot = 0;

        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."over_drafts" WHERE status=1 AND branch IN(select id from "Branch" where "bankId"=:bid) ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        $mytot =  $row['total'] ?? 0;


        return $mytot ?? 0;
    }

    public function getTotalBankShareAmount()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT SUM(share_amount) AS total FROM public."share_register" WHERE branch_id=:bid ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + ($row['total'] ?? 0);
        }

        return $mytot;
    }


    public function getTotalBranchSMSClients2()
    {
        // $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
        // $stmtn = $this->conn->prepare($sqlQueryn);
        // $stmtn->bindParam(':id', $this->branch);

        // $stmtn->execute();
        // $row = $stmtn->fetch();

        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid AND message_consent=1';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getTotalSystemSMSClients2()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE  message_consent=1';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getTotalSystemClients2()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client"';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }


    public function getBranchSMSWalletDetails()
    {
        $sqlQuery = 'SELECT * FROM public."Branch"  WHERE public."Branch".id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        return $stmt;
    }

    public function getBranchSMSWalletBalance()
    {
        $sqlQuery = 'SELECT sms_balance FROM public."Branch"  WHERE public."Branch".id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row['sms_balance'] ?? 0;
    }

    public function getBankSMSWalletBalance()
    {
        $sqlQuery = 'SELECT SUM(sms_balance) AS tot FROM public."Branch"  WHERE public."Branch"."bankId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetchAll();
        return $row['tot'] ?? 0;
    }

    public function getSystemSMSWalletDetails()
    {
        $sqlQuery = 'SELECT SUM(sms_amount_loaded) AS purchase, SUM(sms_amount_spent) AS used, SUM(sms_balance) AS balance FROM public."Branch" ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
        return $stmt;
    }

    public function getBankSMSWalletDetails()
    {


        $sqlQuery = 'SELECT * FROM public."Bank" WHERE id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        return $stmt;
    }

    public function getBankSenderIds($id)
    {


        $sqlQuery = 'SELECT * FROM public."senderids" WHERE bankid=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            return $row['sname'];
        } else {
            return 'Default';
        }
    }

    public function getTotalBankSMSClients2()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid AND message_consent=1';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }

    public function getTotalBranchLoans()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".branchid=:bid AND status IN(2,3,4)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getTotalBranchDebtorsDue()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."receivables" WHERE public."receivables".p_branch_id=:bid AND maturity_date=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getTotalBranchCreditorsDue()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."payables" WHERE public."payables".p_branch_id=:bid AND maturity_date=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getCreditOfficerStatusLoans($status)
    {

        if ($status == 2) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND status IN(2,3,4)';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }

        if ($status == 1) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND status IN(1)';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }

        if ($status == 5) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND status IN(5)';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }

        if ($status == 0) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND status IN(0)';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }
        if ($status == 3) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND status IN(3)';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }
        if ($status == 4) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND principal_arrears>0';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }

        if ($status == 99) {
            $sqlQuery = 'SELECT  SUM(principal_arrears + interest_arrears) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND principal_arrears>0';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }
        if ($status == 92) {
            $sqlQuery = 'SELECT  SUM(current_balance) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND public."loan".status IN(2,3,4)';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }

        if ($status == 95) {
            $sqlQuery = 'SELECT  SUM(principal_balance) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND public."loan".status IN(2,3,4) ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }
        if ($status == 96) {
            $sqlQuery = 'SELECT  SUM(interest_balance) AS total FROM public."loan" WHERE public."loan".loan_officer=:bid AND public."loan".status IN(2,3,4)';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $this->user);

            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'] ?? 0;
        }

        return 0;
    }
    public function getTotalBankLoans()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".branchid=:bid AND status IN(2,3,4)';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }

    public function getTotalBankDebtors()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."receivables" WHERE public."receivables".p_branch_id=:bid AND maturity_date = CURRENT_DATE';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }

    public function getTotalBankCreditors()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."payables" WHERE public."payables".p_branch_id=:bid AND maturity_date = CURRENT_DATE';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }

    public function getTotalBranchStatusLoans($st)
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".branchid=:bid AND status=:st';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);
        $stmt->bindParam(':st', $st);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
    public function getTotalBranchStatusLoanAmounts($st)
    {


        $sqlQuery = 'SELECT SUM(principal) AS total FROM public."loan" WHERE public."loan".branchid=:bid AND status=:st';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);
        $stmt->bindParam(':st', $st);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
    public function getTotalBankStatusLoans($st)
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" WHERE public."loan".branchid=:bid AND status=:st';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);
            $stmt->bindParam(':st', $st);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }

    public function getTotalBankStatusLoanAmounts($st)
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT SUM(principal) AS total FROM public."loan" WHERE public."loan".branchid=:bid AND status=:st';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);
            $stmt->bindParam(':st', $st);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }


    public function getTotalBranchLoanPortifolio()
    {


        $sqlQuery = 'SELECT SUM(principal_balance) AS total FROM public."loan" WHERE public."loan".branchid=:bid AND public."loan".status IN (2,3,4,5)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBankLoanPortifolio()
    {
        $mytot = 0;

        $sqlQuery = 'SELECT SUM(principal_balance) AS total FROM public."loan" LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id WHERE public."Branch"."bankId"=:bid AND public."loan".status IN (2,3,4)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        $mytot = $mytot + ($row['total'] ?? 0);


        return $mytot;
    }

    public function getTotalBranchLoanRepayments()
    {

        $sqlQuery = 'SELECT SUM(COALESCE(amount,0) + COALESCE(loan_interest,0)) AS total FROM public."transactions" WHERE t_type=:tt AND public."transactions"._branch=:bid AND _status=1';
        $stmt = $this->conn->prepare($sqlQuery);
        $ttt = 'L';
        $stmt->bindParam(':tt', $ttt);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchLoanRepaymentsInterest()
    {

        $sqlQuery = 'SELECT  SUM(COALESCE(loan_interest,0)) AS total FROM public."transactions" WHERE t_type=:tt AND public."transactions"._branch=:bid AND _status=1';
        $stmt = $this->conn->prepare($sqlQuery);
        $ttt = 'L';
        $stmt->bindParam(':tt', $ttt);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchLoanArrears()
    {

        $sqlQuery = 'SELECT  COUNT(*) AS total FROM public."loan" WHERE principal_arrears>0 AND public."loan".branchid=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        // $ttt = 'L';
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
    public function getTotalBranchLoanAmountArrears()
    {

        $sqlQuery = 'SELECT  SUM(principal_arrears + interest_arrears) AS total FROM public."loan" WHERE principal_arrears>0 AND public."loan".branchid=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        // $ttt = 'L';
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchLoanAmountWaived()
    {

        $sqlQuery = 'SELECT  SUM(amount ) AS total FROM public."transactions" WHERE t_type=\'WLI\' AND public."transactions"._branch=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        // $ttt = 'L';
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchLoanAmountDue()
    {

        $sqlQuery = 'SELECT  SUM(principal_due + interest_due) AS total FROM public."loan" WHERE principal_due>0 AND public."loan".branchid=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        // $ttt = 'L';
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBankLoanRepayments()
    {
        // $mytot = 0;
        // $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        // $stmtn = $this->conn->prepare($sqlQueryn);
        // $stmtn->bindParam(':id', $this->bank);

        // $stmtn->execute();
        // foreach ($stmtn as $row) {
        $sqlQuery = 'SELECT SUM(COALESCE(amount,0) + COALESCE(loan_interest,0)) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."Branch".id=public."transactions"._branch WHERE t_type=:tt AND public."Branch"."bankId"=:bid AND _status=1';
        $stmt = $this->conn->prepare($sqlQuery);
        $ttt = 'L';
        $stmt->bindParam(':tt', $ttt);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        //     $mytot = $mytot + $row['total'] ?? 0;
        // }

        return $row['total'] ?? 0;
    }

    public function getTotalBankLoanRepaymentsInterest()
    {
        // $mytot = 0;
        // $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        // $stmtn = $this->conn->prepare($sqlQueryn);
        // $stmtn->bindParam(':id', $this->bank);

        // $stmtn->execute();
        // foreach ($stmtn as $row) {
        $sqlQuery = 'SELECT SUM(COALESCE(loan_interest,0)) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."Branch".id=public."transactions"._branch WHERE t_type=:tt AND public."Branch"."bankId"=:bid AND _status=1';
        $stmt = $this->conn->prepare($sqlQuery);
        $ttt = 'L';
        $stmt->bindParam(':tt', $ttt);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        //     $mytot = $mytot + $row['total'] ?? 0;
        // }

        return $row['total'] ?? 0;
    }

    public function getTotalBankLoanArrears()
    {

        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."loan" LEFT JOIN public."Branch" ON public."Branch".id=public."loan".branchid WHERE principal_arrears>0 AND public."Branch"."bankId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();


        return $row['total'] ?? 0;
    }
    public function getTotalBankLoanAmountArrears()
    {

        $sqlQuery = 'SELECT SUM(principal_arrears + interest_arrears) AS total FROM public."loan" LEFT JOIN public."Branch" ON public."Branch".id=public."loan".branchid WHERE principal_arrears>0 AND public."Branch"."bankId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();


        return $row['total'] ?? 0;
    }
    public function getTotalBankLoanAmountWaived()
    {

        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."Branch".id=public."transactions"._branch WHERE t_type=\'WLI\' AND public."Branch"."bankId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();


        return $row['total'] ?? 0;
    }
    public function getTotalBankLoanAmountDue()
    {

        $sqlQuery = 'SELECT SUM(principal_due + interest_due) AS total FROM public."loan" LEFT JOIN public."Branch" ON public."Branch".id=public."loan".branchid WHERE principal_due>0 AND public."Branch"."bankId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();


        return $row['total'] ?? 0;
    }
    public function getTrxnDetails($id)
    {
        $sqlQuery = 'SELECT * FROM public."transactions" WHERE public."transactions".tid=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        return $stmt;
    }

    public function updateBank()
    {
        $sqlQuery = 'UPDATE public."Bank" SET name=:name,contact_person_details=:cp,recommender=:rec,location=:loc WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':cp', $this->contact_person_details);
        $stmt->bindParam(':rec', $this->recommender);
        $stmt->bindParam(':loc', $this->location);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return true;
    }

    public function updateBranch()
    {
        $sqlQuery = 'UPDATE public."Branch" SET name=:name,location=:loc WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':loc', $this->location);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return true;
    }

    public function createRangeCharge()
    {
        $tt = 'variable';
        if (count($this->branch) > 0) {
            for ($i = 0; $i < count($this->branch); $i++) {

                $sqlQuery = 'INSERT INTO public."transaction_charges" (cname, c_application, bankid, charge, c_type, min_amount, max_amount) 
                VALUES(:name,:appln,:bid,:charge,:type,:min,:max)';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':name', $this->name);
                $stmt->bindParam(':appln', $this->bank);
                $stmt->bindParam(':bid', $this->description);
                $stmt->bindParam(':charge', $this->branch[$i]);
                $stmt->bindParam(':type', $tt);
                $stmt->bindParam(':min', $this->pv[$i]);
                $stmt->bindParam(':max', $this->pay_method[$i]);

                $stmt->execute();
                $fid = $this->conn->lastInsertId();


                // create account for the fees account

                $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $this->description);

                $stmt->execute();
                foreach ($stmt as $row) {
                    // fees income account
                    $sqlQuery = 'INSERT INTO public."Account"(
type, "branchId",name, description, "isSystemGenerated",feeId)
VALUES (:typee,:bid,:nname,:descr,:isgen,:said )';
                    $atype = 'INCOMES';
                    $nname = strtoupper($this->name) . ' : ' . $this->pv[$i] . ' - ' . $this->pay_method[$i];
                    $descr = 'This account holds trxn charges collected from ' . $this->bank . ' trxns ' . $this->pv[$i] . ' - ' . $this->pay_method[$i];
                    $isgen = true;
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':typee', $atype);
                    $stmt->bindParam(':bid', $row['id']);
                    $stmt->bindParam(':nname', $nname);
                    $stmt->bindParam(':descr', $descr);
                    $stmt->bindParam(':isgen', $isgen);
                    $stmt->bindParam(':said', $fid);

                    $stmt->execute();
                }
            }
        }





        return true;
    }

    public function createTrxnCharge()
    {


        $sqlQuery = 'INSERT INTO public."transaction_charges" (cname, c_application, bankid, charge, charge_mode) 
                VALUES(:name,:appln,:bid,:charge,:mode)';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':appln', $this->bank);
        $stmt->bindParam(':bid', $this->description);
        $stmt->bindParam(':charge', $this->branch);
        $stmt->bindParam(':mode', $this->pv);

        $stmt->execute();
        $fid = $this->conn->lastInsertId();


        // create account for the fees account

        $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->description);

        $stmt->execute();
        foreach ($stmt as $row) {

            $sqlQuery = 'SELECT * FROM public."Account" WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->createdAt);

            $stmt->execute();
            $rown = $stmt->fetch();

            $sqlQuery = 'UPDATE public."Account" SET txn_charge=:fid WHERE account_code_used=:id AND "branchId" = :branch';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':fid', $fid);
            $stmt->bindParam(':id', $rown['account_code_used']);
            $stmt->bindParam(':branch', $row['id']);

            $stmt->execute();


            // fees income account
            //             $sqlQuery = 'INSERT INTO public."Account"(
            // type, "branchId",name, description, "isSystemGenerated",feeId)
            // VALUES (:typee,:bid,:nname,:descr,:isgen,:said )';
            //             $atype = 'INCOMES';
            //             $nname = strtoupper($this->name) . ' : General - ' . $fid;
            //             $descr = 'This account holds trxn charges collected from ' . $this->bank . ' trxns ';
            //             $isgen = true;
            //             $stmt = $this->conn->prepare($sqlQuery);
            //             $stmt->bindParam(':typee', $atype);
            //             $stmt->bindParam(':bid', $row['id']);
            //             $stmt->bindParam(':nname', $nname);
            //             $stmt->bindParam(':descr', $descr);
            //             $stmt->bindParam(':isgen', $isgen);
            //             $stmt->bindParam(':said', $fid);

            //             $stmt->execute();
        }




        return true;
    }

    public function createFee()
    {
        $sqlQuery = 'INSERT INTO public."Fee" ("bankId",type,"rateAmount",name,"paymentType",status) VALUES(:bid,:tt,:ra,:name,:pt,:st)';

        $stmt = $this->conn->prepare($sqlQuery);
        $stt = 1;
        $stmt->bindParam(':bid', $this->pv);
        $stmt->bindParam(':tt', $this->bank);
        $stmt->bindParam(':ra', $this->description);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':pt', $this->branch);
        $stmt->bindParam(':st', $stt);

        $stmt->execute();
        $fid = $this->conn->lastInsertId();


        if ($this->createdAt == 'exist') {
            $sqlQuery = 'SELECT * FROM public."Account" WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->deletedAt);

            $stmt->execute();
            $rown = $stmt->fetch();

            $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->pv);

            $stmt->execute();
            foreach ($stmt as $row) {

                $sqlQuery = 'UPDATE public."Account" SET feeid=:fid WHERE account_code_used=:id AND "branchId"=:branch';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':fid', $fid);
                $stmt->bindParam(':id', $rown['account_code_used']);
                $stmt->bindParam(':branch', $row['id']);

                $stmt->execute();
            }
        } else {

            $miid = $this->updatedAt;

            $sqlQueryn = 'SELECT  COUNT(*) AS tot FROM public."Account" WHERE main_account_id=:id';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $miid);
            $stmtn->execute();
            $rown = $stmtn->fetch();

            $lastused = (int)$rown['tot'] ?? 0;
            $lastused = $lastused + 1;

            $sqlQueryx = 'SELECT account_code_used FROM public."Account" WHERE id=:id';
            $stmtx = $this->conn->prepare($sqlQueryx);
            $stmtx->bindParam(':id', $miid);
            $stmtx->execute();
            $rowx = $stmtx->fetch();

            $codeused = $rowx['account_code_used'] . '-' . $lastused;

            // create account for the fees account
            $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->pv);

            $stmt->execute();
            foreach ($stmt as $row) {

                // fees income account
                $sqlQuery = 'INSERT INTO public."Account"(
     type, "branchId",name, description, "isSystemGenerated",feeid, main_account_id, account_code_used)
    VALUES (:typee,:bid,:nname,:descr,:isgen,:said, :mainacc, :acode )';
                $atype = 'INCOMES';
                $nname = strtoupper($this->name) . ' - INCOME ACCOUNT';
                $descr = 'This account holds fees income collected from ' . strtolower($this->name);
                $isgen = true;
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':typee', $atype);
                $stmt->bindParam(':bid', $row['id']);
                $stmt->bindParam(':nname', $nname);
                $stmt->bindParam(':descr', $descr);
                $stmt->bindParam(':isgen', $isgen);
                $stmt->bindParam(':said', $fid);
                $stmt->bindParam(':mainacc', $this->updatedAt);
                $stmt->bindParam(':acode', $codeused);

                $stmt->execute();
            }
        }


        return true;
    }

    public function createLoanProduct()
    {
        $sqlQuery = 'INSERT INTO public."loantypes" 
        (type_name,interestrate,penalty,numberofgraceperioddays,penaltyinterestrate,penaltyfixedamount,
        "bankId",frequency,interestmethod,maxnumberofpenaltydays,penalty_based_on,gracetype,auto_repay,auto_penalty,round_off
        ) 
        VALUES(:tname,:intrate,:penalty,:gracedays,:pintrate,:pfamount,:bid,:freq,:intmeth,:maxdays,:pbo,:gtype,:autorepay,:autopenalty,:roundof)';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tname', $this->name);
        $stmt->bindParam(':intrate', $this->bank);
        $stmt->bindParam(':penalty', $this->charge_penalty, PDO::PARAM_BOOL);
        $stmt->bindParam(':gracedays', $this->serialNumber);
        $stmt->bindParam(':pintrate', $this->deletedAt);
        $stmt->bindParam(':pfamount', $this->countryCode);
        $stmt->bindParam(':bid', $this->pv);
        $stmt->bindParam(':freq', $this->branch);
        $stmt->bindParam(':intmeth', $this->description);
        $stmt->bindParam(':maxdays', $this->identificationNumber);

        $stmt->bindParam(':pbo', $this->penaltybased);
        $stmt->bindParam(':gtype', $this->gracetype);
        $stmt->bindParam(':autorepay', $this->auto_repay);
        $stmt->bindParam(':autopenalty', $this->auto_penalty);
        $stmt->bindParam(':roundof', $this->round_off);

        $stmt->execute();
        $last_id = $this->conn->lastInsertId();



        if (sizeof($this->updatedAt) > 0) {
            foreach ($this->updatedAt as $selectedOption) {
                if ($selectedOption != 0) {
                    $sqlQuery = 'INSERT INTO public."loanproducttofee" 
                    (lp_id,fee_id
                    ) 
                    VALUES(:lpid,:fid)';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':lpid', $last_id);
                    $stmt->bindParam(':fid', $selectedOption);


                    $stmt->execute();
                }
            }

            // return true;
        }



        if ($this->check_st == 'exist') {
            $sqlQuery = 'UPDATE public."loantypes" SET princ_acid=:pid WHERE type_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':pid', $this->account_id);
            $stmt->bindParam(':id', $last_id);

            $stmt->execute();
            $sqlQuery = 'UPDATE public."Account" SET lpid=:pid WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':pid', $last_id);
            $stmt->bindParam(':id', $this->account_id);

            $stmt->execute();



            $sqlQuery = 'UPDATE public."loantypes" SET int_acid=:pid WHERE type_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':pid', $this->int_id);
            $stmt->bindParam(':id', $last_id);

            $stmt->execute();
            $sqlQuery = 'UPDATE public."Account" SET lpid=:pid WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':pid', $last_id);
            $stmt->bindParam(':id', $this->int_id);

            $stmt->execute();


            $sqlQuery = 'UPDATE public."loantypes" SET penalty_acid=:pid WHERE type_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':pid', $this->p_id);
            $stmt->bindParam(':id', $last_id);

            $stmt->execute();
            $sqlQuery = 'UPDATE public."Account" SET lpid=:pid WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':pid', $last_id);
            $stmt->bindParam(':id', $this->p_id);

            $stmt->execute();
            return true;
        } else {

            // create account for the loan Product account

            $miid = $this->pid;

            $sqlQueryn = 'SELECT  COUNT(*) AS tot FROM public."Account" WHERE main_account_id=:id';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $miid);
            $stmtn->execute();
            $rown = $stmtn->fetch();

            $lastused = (int)$rown['tot'] ?? 0;
            $lastused = $lastused + 1;

            $sqlQueryx = 'SELECT account_code_used FROM public."Account" WHERE id=:id';
            $stmtx = $this->conn->prepare($sqlQueryx);
            $stmtx->bindParam(':id', $miid);
            $stmtx->execute();
            $rowx = $stmtx->fetch();


            $miid1 = $this->acid;

            $sqlQueryn = 'SELECT  COUNT(*) AS tot FROM public."Account" WHERE main_account_id=:id';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $miid1);
            $stmtn->execute();
            $rown = $stmtn->fetch();

            $lastused1 = (int)$rown['tot'] ?? 0;
            $lastused1 = $lastused1 + 1;

            $sqlQueryx = 'SELECT account_code_used FROM public."Account" WHERE id=:id';
            $stmtx = $this->conn->prepare($sqlQueryx);
            $stmtx->bindParam(':id', $miid1);
            $stmtx->execute();
            $rowx1 = $stmtx->fetch();

            $miid2 = $this->createdAt;

            $sqlQueryn = 'SELECT  COUNT(*) AS tot FROM public."Account" WHERE main_account_id=:id';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $miid2);
            $stmtn->execute();
            $rown = $stmtn->fetch();

            $lastused2 = (int)$rown['tot'] ?? 0;
            $lastused2 = $lastused2 + 1;

            $sqlQueryx = 'SELECT account_code_used FROM public."Account" WHERE id=:id';
            $stmtx = $this->conn->prepare($sqlQueryx);
            $stmtx->bindParam(':id', $miid2);
            $stmtx->execute();
            $rowx2 = $stmtx->fetch();





            $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->pv);

            $stmt->execute();
            foreach ($stmt as $row) {
                // Loan Principal account
                $sqlQuery = 'INSERT INTO public."Account"(
     type, "branchId",name, description, "isSystemGenerated", lpid, main_account_id, account_code_used)
    VALUES (:typee,:bid,:nname,:descr,:isgen,:said , :mainac, :acode) RETURNING id';
                $atype = 'ASSETS';
                $nname = strtoupper($this->name) . ' - LOAN PRINCIPAL ACCOUNT';
                $descr = 'This account holds principal disbursed for ' . strtolower($this->name) . ' loans';
                $isgen = true;
                $codeused = $rowx['account_code_used'] . '-' . $lastused;
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':typee', $atype);
                $stmt->bindParam(':bid', $row['id']);
                $stmt->bindParam(':nname', $nname);
                $stmt->bindParam(':descr', $descr);
                $stmt->bindParam(':isgen', $isgen);
                $stmt->bindParam(':said', $last_id);
                $stmt->bindParam(':mainac', $miid);
                $stmt->bindParam(':acode', $codeused);

                $stmt->execute();
                $pacid = $stmt->fetch();
                // set principal_account_id

                $sqlQuery = 'UPDATE public."loantypes" SET princ_acid=:pid WHERE type_id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':pid', $pacid);
                $stmt->bindParam(':id', $last_id);

                $stmt->execute();

                // Loan interest income account
                $sqlQuery = 'INSERT INTO public."Account"(
                type, "branchId",name, description, "isSystemGenerated", lpid, main_account_id, account_code_used)
               VALUES (:typee,:bid,:nname,:descr,:isgen,:said , :mainac, :acode) RETURNING id';
                $atype = 'INCOMES';
                $nname = strtoupper($this->name) . ' - LOAN INTEREST INCOME ACCOUNT';
                $descr = 'This account holds loan interest income from ' . strtolower($this->name) . ' loans';
                $isgen = true;
                $codeused1 = $rowx1['account_code_used'] . '-' . $lastused1;
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':typee', $atype);
                $stmt->bindParam(':bid', $row['id']);
                $stmt->bindParam(':nname', $nname);
                $stmt->bindParam(':descr', $descr);
                $stmt->bindParam(':isgen', $isgen);
                $stmt->bindParam(':said', $last_id);
                $stmt->bindParam(':mainac', $miid1);
                $stmt->bindParam(':acode', $codeused1);

                $stmt->execute();

                $pacid = $stmt->fetch();
                // set principal_account_id

                $sqlQuery = 'UPDATE public."loantypes" SET int_acid=:pid WHERE type_id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':pid', $pacid);
                $stmt->bindParam(':id', $last_id);

                $stmt->execute();

                // Loan interest suspense account
                // $sqlQuery = 'INSERT INTO public."Account"(
                //     type, "branchId",name, description, "isSystemGenerated", lpid, main_account_id, account_code_used)
                //    VALUES (:typee,:bid,:nname,:descr,:isgen,:said , :mainac, :acode)';
                // $atype = 'LIABILITIES';
                // $nname = strtoupper($this->name) . ' - LOAN INTEREST SUSPENSE ACCOUNT';
                // $descr = 'This account holds loan interest suspense from ' . strtolower($this->name) . ' loans';
                // $isgen = true;
                // $codeused = $rowx['account_code_used'] . '-' . $lastused2;
                // $stmt = $this->conn->prepare($sqlQuery);
                // $stmt->bindParam(':typee', $atype);
                // $stmt->bindParam(':bid', $row['id']);
                // $stmt->bindParam(':nname', $nname);
                // $stmt->bindParam(':descr', $descr);
                // $stmt->bindParam(':isgen', $isgen);
                // $stmt->bindParam(':said', $last_id);
                // $stmt->bindParam(':mainac', $miid);
                // $stmt->bindParam(':acode', $codeused);

                // $stmt->execute();

                // Loan interest RECEIVABLE account
                // $sqlQuery = 'INSERT INTO public."Account"(
                //     type, "branchId",name, description, "isSystemGenerated", lpid, main_account_id, account_code_used)
                //    VALUES (:typee,:bid,:nname,:descr,:isgen,:said , :mainac, :acode)';
                // $atype = 'ASSETS';
                // $nname = strtoupper($this->name) . ' - LOAN INTEREST RECEIVABLE ACCOUNT';
                // $descr = 'This account holds loan interest receivable from ' . strtolower($this->name) . ' loans';
                // $isgen = true;
                // $codeused = $rowx['account_code_used'] . '-' . $lastused3;
                // $stmt = $this->conn->prepare($sqlQuery);
                // $stmt->bindParam(':typee', $atype);
                // $stmt->bindParam(':bid', $row['id']);
                // $stmt->bindParam(':nname', $nname);
                // $stmt->bindParam(':descr', $descr);
                // $stmt->bindParam(':isgen', $isgen);
                // $stmt->bindParam(':said', $last_id);
                // $stmt->bindParam(':mainac', $miid);
                // $stmt->bindParam(':acode', $codeused);

                // $stmt->execute();
                if ($this->createdAt) {
                    // Loan penalty income account
                    $sqlQuery = 'INSERT INTO public."Account"(
                type, "branchId",name, description, "isSystemGenerated", lpid, main_account_id, account_code_used)
               VALUES (:typee,:bid,:nname,:descr,:isgen,:said , :mainac, :acode) RETURNING id';
                    $atype = 'INCOMES';
                    $nname = strtoupper($this->name) . ' - LOAN PENALTY INCOME ACCOUNT';
                    $descr = 'This account holds loan penalty income from ' . strtolower($this->name) . ' loans';
                    $isgen = true;
                    $codeused2 = $rowx2['account_code_used'] . '-' . $lastused2;
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':typee', $atype);
                    $stmt->bindParam(':bid', $row['id']);
                    $stmt->bindParam(':nname', $nname);
                    $stmt->bindParam(':descr', $descr);
                    $stmt->bindParam(':isgen', $isgen);
                    $stmt->bindParam(':said', $last_id);
                    $stmt->bindParam(':mainac', $miid2);
                    $stmt->bindParam(':acode', $codeused2);

                    $stmt->execute();

                    $pacid = $stmt->fetch();
                    // set principal_account_id

                    $sqlQuery = 'UPDATE public."loantypes" SET penalty_acid=:pid WHERE type_id=:id';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':pid', $pacid);
                    $stmt->bindParam(':id', $last_id);

                    $stmt->execute();

                    // Loan penalty receivable account
                    //     $sqlQuery = 'INSERT INTO public."Account"(
                    //     type, "branchId",name, description, "isSystemGenerated", lpid, main_account_id, account_code_used)
                    //    VALUES (:typee,:bid,:nname,:descr,:isgen,:said , :mainac, :acode)';
                    //     $atype = 'ASSETS';
                    //     $nname = strtoupper($this->name) . ' - LOAN PENALTY RECEIVABLE ACCOUNT';
                    //     $descr = 'This account holds loan penalty receivable from ' . strtolower($this->name) . ' loans';
                    //     $isgen = true;
                    //     $codeused = $rowx['account_code_used'] . '-' . $lastused5;
                    //     $stmt = $this->conn->prepare($sqlQuery);
                    //     $stmt->bindParam(':typee', $atype);
                    //     $stmt->bindParam(':bid', $row['id']);
                    //     $stmt->bindParam(':nname', $nname);
                    //     $stmt->bindParam(':descr', $descr);
                    //     $stmt->bindParam(':isgen', $isgen);
                    //     $stmt->bindParam(':said', $last_id);
                    //     $stmt->bindParam(':mainac', $miid);
                    //     $stmt->bindParam(':acode', $codeused);

                    //     $stmt->execute();

                    // Loan penalty suspense account
                    //     $sqlQuery = 'INSERT INTO public."Account"(
                    //     type, "branchId",name, description, "isSystemGenerated", lpid, main_account_id, account_code_used)
                    //    VALUES (:typee,:bid,:nname,:descr,:isgen,:said , :mainac, :acode)';
                    //     $atype = 'LIABILITIES';
                    //     $nname = strtoupper($this->name) . ' - LOAN PENALTY SUSPENSE ACCOUNT';
                    //     $descr = 'This account holds loan penalty suspense from ' . strtolower($this->name) . ' loans';
                    //     $isgen = true;
                    //     $codeused = $rowx['account_code_used'] . '-' . $lastused6;
                    //     $stmt = $this->conn->prepare($sqlQuery);
                    //     $stmt->bindParam(':typee', $atype);
                    //     $stmt->bindParam(':bid', $row['id']);
                    //     $stmt->bindParam(':nname', $nname);
                    //     $stmt->bindParam(':descr', $descr);
                    //     $stmt->bindParam(':isgen', $isgen);
                    //     $stmt->bindParam(':said', $last_id);
                    //     $stmt->bindParam(':mainac', $miid);
                    //     $stmt->bindParam(':acode', $codeused);

                    //     $stmt->execute();
                }
            }
        }
        return true;
    }

    public function editLoanProduct()
    {
        $sqlQuery = 'UPDATE public."loantypes" SET type_name=:tname,interestrate=:intrate,penalty=:penalty,numberofgraceperioddays=:gracedays,penaltyinterestrate=:pintrate,penaltyfixedamount=:pfamount,"bankId"=:bid,frequency=:freq,interestmethod=:intmeth,maxnumberofpenaltydays=:maxdays,penalty_based_on=:pbo,gracetype=:gtype,auto_repay=:autorepay,auto_penalty=:autopenalty,round_off=:roundof WHERE type_id=:idd';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tname', $this->name);
        $stmt->bindParam(':intrate', $this->bank);
        $stmt->bindParam(':penalty', $this->createdAt);
        $stmt->bindParam(':gracedays', $this->serialNumber);
        $stmt->bindParam(':pintrate', $this->deletedAt);
        $stmt->bindParam(':pfamount', $this->countryCode);
        $stmt->bindParam(':bid', $this->pv);
        $stmt->bindParam(':freq', $this->branch);
        $stmt->bindParam(':intmeth', $this->description);
        $stmt->bindParam(':maxdays', $this->identificationNumber);

        $stmt->bindParam(':pbo', $this->penaltybased);
        $stmt->bindParam(':gtype', $this->gracetype);
        $stmt->bindParam(':autorepay', $this->auto_repay);
        $stmt->bindParam(':autopenalty', $this->auto_penalty);
        $stmt->bindParam(':roundof', $this->round_off);
        $stmt->bindParam(':idd', $this->id);

        $stmt->execute();
        $last_id = $this->id;



        $sqlQuery = 'DELETE FROM public."loanproducttofee" WHERE lp_id=:lpid';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lpid', $last_id);
        $stmt->execute();

        if (sizeof($this->updatedAt) > 0) {
            foreach ($this->updatedAt as $selectedOption) {
                if ($selectedOption != 0) {
                    $sqlQuery = 'INSERT INTO public."loanproducttofee" 
                    (lp_id,fee_id
                    ) 
                    VALUES(:lpid,:fid)';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':lpid', $last_id);
                    $stmt->bindParam(':fid', $selectedOption);


                    $stmt->execute();
                }
            }

            // return true;
        }



        return true;
    }


    public function updateLoanApplication()
    {

        $sqlQuery = 'SELECT * FROM public."loantypes" WHERE public."loantypes".type_id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->bank);
        $stmt->execute();
        $row = $stmt->fetch();
        $cycleid = 0;
        $methodd = 0;
        if ($row['frequency'] == 'DAILY') {
            $cycleid = 1;
        } else if ($row['frequency'] == 'WEEKLY') {
            $cycleid = 2;
        } else if ($row['frequency'] == 'MONTHLY') {
            $cycleid = 3;
        } else if ($row['frequency'] == 'BIMONTHLY') {
            $cycleid = 4;
        } else if ($row['frequency'] == 'ANNUALLY') {
            $cycleid = 5;
        }

        if ($row['interestmethod'] == 'FLAT') {
            $methodd = 1;
        } else if ($row['interestmethod'] == 'DECLINING_BALANCE') {
            $methodd = 2;
        }

        $sqlQuery = 'UPDATE public."loan" SET 
            loanproductid=:lpid,principal=:principal,requestedamount=:ra,current_balance=:cb,loan_type=:lt,status=:stat,
            application_date=:applydate,requesteddisbursementdate=:disbursedate,account_id=:acid,loan_officer=:loff,requested_loan_duration=:duration,
            repay_cycle_id=:rcid,date_of_first_pay=:startdate,interest_method_id=:imid,monthly_interest_rate=:rate,notes=:notes,
            approvedamount=:aa,approved_loan_duration=:ald WHERE loan_no=:lnoo';
        $cbb = 0;
        $stt = 0;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lpid', $this->bank);
        $stmt->bindParam(':principal', $this->createdAt);
        $stmt->bindParam(':ra', $this->createdAt);
        $stmt->bindParam(':cb', $cbb);
        $stmt->bindParam(':lt', $row['type_id']);
        $stmt->bindParam(':stat', $stt);

        $stmt->bindParam(':applydate', $this->branch);
        $stmt->bindParam(':disbursedate', $this->branch);
        $stmt->bindParam(':acid', $this->name);
        $stmt->bindParam(':loff', $this->pv);
        $stmt->bindParam(':duration', $this->updatedAt);


        $stmt->bindParam(':rcid', $cycleid);
        $stmt->bindParam(':startdate', $this->description);

        $stmt->bindParam(':imid', $methodd);

        $stmt->bindParam(':rate', $row['interestrate']);
        $stmt->bindParam(':notes', $this->deletedAt);
        $stmt->bindParam(':aa', $this->createdAt);
        $stmt->bindParam(':ald', $this->updatedAt);
        $stmt->bindParam(':lnoo', $this->identificationNumber);

        $stmt->execute();

        $last_id = $this->identificationNumber;


        $this->applyLoanSchedule($last_id);

        $this->updateTotalLoanAmount($last_id);
        return true;
    }

    public function createLoanApplication()
    {

        $sqlQuery = 'SELECT * FROM public."loantypes" WHERE public."loantypes".type_id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->bank);
        $stmt->execute();
        $row = $stmt->fetch();
        $cycleid = $this->sv;
        $methodd = 0;
        // if ($row['frequency'] == 'DAILY') {
        //     $cycleid = 1;
        // } else if ($row['frequency'] == 'WEEKLY') {
        //     $cycleid = 2;
        // } else if ($row['frequency'] == 'MONTHLY') {
        //     $cycleid = 3;
        // } else if ($row['frequency'] == 'BIMONTHLY') {
        //     $cycleid = 4;
        // }
        $fees = '';
        $sqlQuery = 'SELECT * FROM public."loanproducttofee" 
    LEFT JOIN public."loantypes" ON public."loanproducttofee".lp_id=public."loantypes".type_id
    LEFT JOIN public."Fee" ON public."loanproducttofee".fee_id=public."Fee".id
 WHERE public."loanproducttofee".lp_id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->bank);
        $stmt->execute();
        foreach ($stmt as $rown) {
            if ($fees != '') {
                $fees = $fees . ',' . $rown['fee_id'];
            } else {
                $fees = $rown['fee_id'];
            }
        }

        if ($row['interestmethod'] == 'FLAT') {
            $methodd = 1;
        } else if ($row['interestmethod'] == 'DECLINING_BALANCE') {
            $methodd = 2;
        }

        $sqlQuery = 'INSERT INTO public."loan" 
        (
            loanproductid,principal,requestedamount,branchid,current_balance,loan_type,status,
            application_date,requesteddisbursementdate,account_id,loan_officer,requested_loan_duration,
            repay_cycle_id,date_of_first_pay,interest_method_id,monthly_interest_rate,notes,
            approvedamount,approved_loan_duration,fees_to_charge,charge_penalty,penalty_interest_rate,penalty_fixed_amount,penalty_based_on,num_grace_periods,penalty_grace_type
        ) 
        VALUES(:lpid,:principal,:ra,:bid,:cb,:lt,:stat,:applydate,:disbursedate,:acid,:loff,:duration,:rcid,:startdate,:imid,:rate,:notes,:aa,:ald,:fees_to_charge,
        :charge_penalty,:penalty_interest_rate,:penalty_fixed_amount,:penalty_based_on,:num_grace_periods,:penalty_grace_type
        )';
        $cbb = 0;
        $stt = 0;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':penalty_grace_type', $row['gracetype']);
        $stmt->bindParam(':num_grace_periods', $row['numberofgraceperioddays']);
        $stmt->bindParam(':penalty_based_on', $row['penalty_based_on']);
        $stmt->bindParam(':penalty_fixed_amount', $row['penaltyfixedamount']);
        $stmt->bindParam(':penalty_interest_rate', $row['penaltyinterestrate']);
        $stmt->bindParam(':charge_penalty', $row['penalty']);

        $stmt->bindParam(':lpid', $this->bank);
        $stmt->bindParam(':principal', $this->createdAt);
        $stmt->bindParam(':ra', $this->createdAt);
        $stmt->bindParam(':bid', $this->serialNumber);
        $stmt->bindParam(':cb', $cbb);
        $stmt->bindParam(':lt', $row['type_id']);
        $stmt->bindParam(':stat', $stt);
        $stmt->bindParam(':fees_to_charge', $fees);

        $stmt->bindParam(':applydate', $this->branch);
        $stmt->bindParam(':disbursedate', $this->branch);
        $stmt->bindParam(':acid', $this->name);
        $stmt->bindParam(':loff', $this->pv);
        $stmt->bindParam(':duration', $this->updatedAt);


        $stmt->bindParam(':rcid', $cycleid);
        $stmt->bindParam(':startdate', $this->description);

        $stmt->bindParam(':imid', $methodd);

        $stmt->bindParam(':rate', $row['interestrate']);
        $stmt->bindParam(':notes', $this->deletedAt);
        $stmt->bindParam(':aa', $this->createdAt);
        $stmt->bindParam(':ald', $this->updatedAt);

        $stmt->execute();

        $last_id = $this->conn->lastInsertId();


        $this->applyLoanSchedule($last_id);

        $this->updateTotalLoanAmount($last_id);
        return $last_id;
    }

    public function generateTotalInterests()
    {
        $sqlQuery = 'SELECT * FROM data_importer_loan_batch_records';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        foreach ($stmt as $row) {
            $tot_interest = 0;
            $rate =    $row['interest_rate'] / 120000;
            $months = $row['duration'] / 30;
            $tot_interest = $row['loan_amount'] * $rate * $months;

            $tot_interest = (int)$tot_interest;

            $sqlQuery = 'UPDATE data_importer_loan_batch_records SET interest_amount=:im WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['id']);
            $stmt->bindParam(':im', $tot_interest);
            $stmt->execute();
        }

        return true;
    }

    public function reverseImportedLoans()
    {
        // 7938b57c-c2be-4af8-9158-e3d5c174d5be
        $sqlQuery = 'SELECT * FROM loan WHERE status=5';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        foreach ($stmt as $row) {

            $sqlQuery = 'UPDATE data_importer_loan_batch_records SET import_status=false WHERE loan_number=:id';
            $st = false;
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['external_loan_no']);
            // $stmt->bindParam(':st', $st);
            $stmt->execute();

            $sqlQuery = 'DELETE FROM loan_schedule WHERE loan_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['loan_no']);
            $stmt->execute();

            $sqlQuery = 'DELETE FROM transactions WHERE loan_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['loan_no']);
            $stmt->execute();

            $sqlQuery = 'DELETE FROM loan WHERE loan_no=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['loan_no']);
            $stmt->execute();
        }

        return true;
    }

    public function MembershipLedgerFeesRenewal()
    {

        // set ledger fees amount since it's general
        $ledger_fees = 12000;
        // membership fees varies
        $membership_fees = 0;


        // fetch all members per branch
        $sqlQuery = 'SELECT *  FROM public."Client" AS c LEFT JOIN public."User" AS u ON c."userId"=u.id WHERE c."branchId"=\'1690b215-1365-4c1b-b73f-bc6bd11b5232\' AND c.auto_status=0 ORDER BY c."userId" ASC LIMIT 1000';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $members = $stmt->fetchAll();
        foreach ($members as $member) {

            // Determine display name
            $displayName = !empty($member['shared_name']) ? $member['shared_name'] : $member['firstName'] . ' ' . $member['lastName'];
            $displayNameSMS = !empty($member['shared_name']) ? $member['shared_name'] : $member['firstName'];

            // check client saving product & set how much is supposed to pay
            if ($member['client_type'] == 'individual') {
                $membership_fees = 5000;
            }
            if ($member['client_type'] == 'group' || $member['client_type'] == 'institution') {
                $membership_fees = 10000;
            }
            $total_deductions = $ledger_fees + $membership_fees;

            if ($member['acc_balance'] >= $total_deductions) {

                // create income entry for membership fees
                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created,mid,cr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee,:mid,:cr)';

                $cr_acid = '81070456-87ba-4953-ada5-8646279f67cc';
                $des = 'Membership Renewal fees for the Year 2024';
                $auth = 547524;
                $ttype = 'I';
                $pay_meth = 'saving';
                $now = date('Y-m-d');
                $client_name = $displayName;

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':amount', $membership_fees);
                $stmt->bindParam(':cr', $cr_acid);
                $stmt->bindParam(':descri', $des);
                $stmt->bindParam(':autho', $auth);
                $stmt->bindParam(':actby', $auth);
                $stmt->bindParam(':accname', $client_name);
                $stmt->bindParam(':approv', $auth);
                $stmt->bindParam(':branc', $member['branchId']);
                $stmt->bindParam(':ttype', $ttype);
                $stmt->bindParam(':acid', $cr_acid);
                $stmt->bindParam(':pay_method', $pay_meth);
                $stmt->bindParam(':datee', $now);

                $stmt->bindParam(':mid', $member['userId']);

                $stmt->execute();

                // create income entry for ledger fees
                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created,mid,cr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee,:mid,:cr)';

                $cr_acid = '6f979f2b-0e36-433f-b9e9-0f2a05ce6f14';
                $des = 'Ledger fees Renewal for the Year 2024';
                $auth = 547524;
                $ttype = 'I';
                $pay_meth = 'saving';
                $now = date('Y-m-d');
                $client_name = $displayName;

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':amount', $ledger_fees);
                $stmt->bindParam(':cr', $cr_acid);
                $stmt->bindParam(':descri', $des);
                $stmt->bindParam(':autho', $auth);
                $stmt->bindParam(':actby', $auth);
                $stmt->bindParam(':accname', $client_name);
                $stmt->bindParam(':approv', $auth);
                $stmt->bindParam(':branc', $member['branchId']);
                $stmt->bindParam(':ttype', $ttype);
                $stmt->bindParam(':acid', $cr_acid);
                $stmt->bindParam(':pay_method', $pay_meth);
                $stmt->bindParam(':datee', $now);

                $stmt->bindParam(':mid', $member['userId']);

                $stmt->execute();

                // update client acc_balance
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac,auto_status=1 WHERE public."Client"."userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(
                    ':id',
                    $member['userId']
                );
                $stmt->bindParam(':ac', $total_deductions);
                $stmt->execute();


                // send sms for total deductions


            } else if ($member['acc_balance'] >= $membership_fees) {

                // create income entry for membership fees
                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created,mid,cr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee,:mid,:cr)';

                $cr_acid = '81070456-87ba-4953-ada5-8646279f67cc';
                $des = 'Membership Renewal fees for the Year 2024';
                $auth = 547524;
                $ttype = 'I';
                $pay_meth = 'saving';
                $now = date('Y-m-d');
                $client_name = $displayName;

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':amount', $membership_fees);
                $stmt->bindParam(':cr', $cr_acid);
                $stmt->bindParam(':descri', $des);
                $stmt->bindParam(':autho', $auth);
                $stmt->bindParam(':actby', $auth);
                $stmt->bindParam(':accname', $client_name);
                $stmt->bindParam(':approv', $auth);
                $stmt->bindParam(':branc', $member['branchId']);
                $stmt->bindParam(':ttype', $ttype);
                $stmt->bindParam(':acid', $cr_acid);
                $stmt->bindParam(':pay_method', $pay_meth);
                $stmt->bindParam(':datee', $now);

                $stmt->bindParam(':mid', $member['userId']);

                $stmt->execute();
                // update client acc_balance
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac,auto_status=1 WHERE public."Client"."userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(
                    ':id',
                    $member['userId']
                );
                $stmt->bindParam(':ac', $membership_fees);
                $stmt->execute();

                // send sms  -- membership fees only
            } else {
                // update client acc_balance
                $sqlQuery = 'UPDATE public."Client" SET auto_status=1 WHERE public."Client"."userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(
                    ':id',
                    $member['userId']
                );
                $stmt->execute();
            }
        }
        return true;
    }

    public function sendSMSAllMembers()
    {
        $sqlQuery = 'SELECT public."Client"."userId" FROM public."Client" WHERE public."Client"."branchId"=\'dff4bda4-a798-4f01-831e-85fba080a99c\' AND public."Client".auto_status=0 ORDER BY public."Client"."userId"';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $row) {

            // check and confirm number is right
            $phone = $this->getClientPhone($row['userId'], '');

            // send sms

            if ($phone && !is_null($phone)) {
                /* phone number array hold numbers , iterate & send to each number */
                $sms = 'RUJUMBURA MUSLIM SACCO. Sebo/ Nyabo nomanyisibwa kwija omurukiiko rwa sacco ebiro 19/01/2025 aha Sunday shaha ina(4) zakasheshe aha muzigiti Rukungiri. Webare';
                foreach ($phone as $value) {
                    // check if phone number has country code or not --use 256 by default
                    if ($value[0] == "0" || $value[0] == 0 || $value[0] == "7") {
                        if (
                            $value[0] == "0" || $value[0] == 0
                        ) {
                            $value = '256' . substr($value, 1);
                        } else {
                            $value = '256' . $value;
                        }
                    }
                    // send sms
                    $this->SendSMSAuto('INFOSMS', $value, $sms);
                }
            }

            $sqlQuery = 'UPDATE public."Client" SET auto_status=1 WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(
                ':id',
                $row['userId']
            );
            $stmt->execute();
        }
        return true;
    }

    public function rectifyDoublePostingInterest()
    {

        $sqlQuery = 'WITH repeated_mids AS (
    SELECT mid
    FROM transactions
    WHERE t_type = \'E\'
      AND description = \'Interest On Savings for the Year 2024\'
    GROUP BY mid
    HAVING COUNT(*) > 1
)
SELECT DISTINCT ON (t.mid) t.*
FROM transactions t
JOIN repeated_mids rm ON t.mid = rm.mid
WHERE t.t_type = \'E\'
  AND t.description = \'Interest On Savings for the Year 2024\'
ORDER BY t.mid, t.tid ASC 

';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        foreach ($stmt->fetchAll() as $trxn) {

            // update client acc_balance

            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(
                ':id',
                $trxn['mid']
            );
            $stmt->bindParam(
                ':ac',
                $trxn['amount']
            );
            $stmt->execute();

            // delete trxn
            $sqlQuery = 'DELETE FROM  public."transactions" WHERE public."transactions".tid=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(
                ':id',
                $trxn['tid']
            );
            $stmt->execute();
        }
        return true;
    }

    public function rectifyDoublePostingInterest2()
    {

        $sqlQuery = 'WITH repeated_mids AS (
    SELECT mid
    FROM transactions
    WHERE t_type = \'W\'
      AND description = \'WHT Charged On Interest On Savings for the Year 2024\'
    GROUP BY mid
    HAVING COUNT(*) > 1
)
SELECT DISTINCT ON (t.mid) t.*
FROM transactions t
JOIN repeated_mids rm ON t.mid = rm.mid
WHERE t.t_type = \'W\'
  AND t.description = \'WHT Charged On Interest On Savings for the Year 2024\'
ORDER BY t.mid, t.tid ASC

';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        foreach ($stmt->fetchAll() as $trxn) {

            // update client acc_balance
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(
                ':id',
                $trxn['mid']
            );
            $stmt->bindParam(
                ':ac',
                $trxn['amount']
            );
            $stmt->execute();

            // delete trxn
            $sqlQuery = 'DELETE FROM  public."transactions" WHERE public."transactions".tid=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(
                ':id',
                $trxn['tid']
            );
            $stmt->execute();
        }
        return true;
    }

    public function getClientRangeTransactionsBF($mid, $start, $end)
    {


        $sqlQuery = 'SELECT SUM(amount) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'D\',\'A\',\'LC\',\'E\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $mid);
        $stmt->bindParam(':transaction_start_date', $start);

        $stmt->execute();

        $row = $stmt->fetch();
        $debit = $row['tot1'] ?? 0;


        $sqlQuery = 'SELECT SUM(amount) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'W\',\'LE\',\'C\',\'CW\',\'CS\',\'SMS\',\'LP\',\'RC\',\'I\',\'R\',\'L\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $mid);
        $stmt->bindParam(':transaction_start_date', $start);

        $stmt->execute();

        $rown = $stmt->fetch();
        $credit = $rown['tot1'] ?? 0;

        $sqlQuery = 'SELECT SUM(loan_interest) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'L\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $mid);
        $stmt->bindParam(':transaction_start_date', $start);

        $stmt->execute();

        $rowx = $stmt->fetch();
        $credit2 = $rowx['tot1'] ?? 0;

        $total = $debit - $credit - $credit2;

        return $total ?? 0;
    }

    public function InterestExpenseOnSaving()
    {
        $min_bal = 100000;
        $wht = 15;
        // Define months for the current year
        $year = date('Y');
        $months = range(1, 11);

        // Define transaction types that increase and decrease balance
        $increase_types = ["D", "E", "CAP", "LC", "A"];
        $decrease_types = ["W", "LE", "C", "R", "I", "CW", "CS", "SMS", "LP", "RC"];
        $loan_type = "L";

        // Fetch all members 26044
        $membersQuery = 'SELECT u.id AS suid, c."userId", u."firstName", u."lastName", c.acc_balance,u.shared_name, c."branchId"
                 FROM public."Client" c
                 JOIN public."User" u ON u.id = c."userId" WHERE c.auto_status=0 AND c."branchId" IN(\'8fb667c6-b92e-4286-91b0-d31df32e5174\') ORDER BY c."userId" ASC LIMIT 500 ';
        $members = $this->conn->query($membersQuery)->fetchAll();

        foreach ($members as $row) {

            $yearlyTotal = 0;



            foreach ($months as $month) {
                // Fetch transactions for the member for the specific month
                $startDate = "$year-$month-01";
                $endDate = date("Y-m-t", strtotime($startDate));

                $query = "SELECT amount, t_type, loan_interest
                  FROM transactions
                  WHERE mid = :userId AND DATE(date_created)>= :startDate AND DATE(date_created)<=:endDate";

                $stmt = $this->conn->prepare($query);
                $stmt->execute(["userId" => $row['userId'], "startDate" => $startDate, "endDate" => $endDate]);

                $transactions = $stmt->fetchAll();

                // Calculate monthly closing balance
                $monthlyBalance = 0;

                foreach ($transactions as $transaction) {
                    $type = $transaction['t_type'];
                    $amount = $transaction['amount'] + $transaction['loan_interest'];

                    if (in_array($type, $increase_types)) {
                        $monthlyBalance += $amount;
                    } elseif (in_array($type, $decrease_types)) {
                        $monthlyBalance -= $amount;
                    } elseif ($type === $loan_type) {
                        $monthlyBalance -=  $amount;
                    }
                }

                $bf = $this->getClientRangeTransactionsBF($row['userId'], $startDate, $endDate);
                $monthlyBalance += $bf;
                if ($monthlyBalance < $min_bal) {
                    $monthlyBalance = 0;
                }

                $yearlyTotal += $monthlyBalance;
            }

            if ($yearlyTotal > 0) {
                // Compute general interest
                $generalInterest = $yearlyTotal * (0.003333);
            } else {
                $generalInterest = 0;
            }

            $generalInterest = round($generalInterest);

            $wht_amount = 0;
            $wht_amount = (int)(($wht / 100) * $generalInterest);

            $diff_amount = $generalInterest - $wht_amount;
            // Output results
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:bal, auto_status=1 where "userId"=:id ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['userId']);
            $stmt->bindParam(':bal', $diff_amount);
            $stmt->execute();


            $displayName = !empty($row['shared_name']) ? $row['shared_name'] : $row['firstName'] . ' ' . $row['lastName'];
            $displayNameSMS = !empty($row['shared_name']) ? $row['shared_name'] : $row['firstName'];



            // create interest trxn
            // create expense transaction via savings with chart acc id for corresponding branch
            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created,mid,cr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee,:mid,:cr)';

            $cr_acid = '8c80c301-1d89-4ad6-ba3a-8a786c5cd80f';
            $des = 'Interest On Savings for the Year 2024';
            $auth = 547524;
            $ttype = 'E';
            $pay_meth = 'saving';
            $now = date('Y-m-d');
            $client_name = $displayName;

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount', $generalInterest);
            $stmt->bindParam(':cr', $cr_acid);
            $stmt->bindParam(':descri', $des);
            $stmt->bindParam(':autho', $auth);
            $stmt->bindParam(':actby', $auth);
            $stmt->bindParam(':accname', $client_name);
            $stmt->bindParam(':approv', $auth);
            $stmt->bindParam(':branc', $row['branchId']);
            $stmt->bindParam(':ttype', $ttype);
            $stmt->bindParam(':acid', $cr_acid);
            $stmt->bindParam(':pay_method', $pay_meth);
            $stmt->bindParam(':datee', $now);

            $stmt->bindParam(':mid', $row['userId']);

            $stmt->execute();


            // wht trxn liabilty
            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created,mid,cr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee,:mid,:cr)';

            $cr_acid = '4621055a-2c5c-4c3c-bc96-4d4b9dc0bb0d';
            $des = 'WHT Charged On Interest On Savings for the Year 2024';
            $auth = 547524;
            $ttype = 'W';
            $pay_meth = 'saving';
            $now = date('Y-m-d');
            $client_name = $displayName;

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount', $wht_amount);
            $stmt->bindParam(':cr', $cr_acid);
            $stmt->bindParam(':descri', $des);
            $stmt->bindParam(':autho', $auth);
            $stmt->bindParam(':actby', $auth);
            $stmt->bindParam(':accname', $client_name);
            $stmt->bindParam(':approv', $auth);
            $stmt->bindParam(':branc', $row['branchId']);
            $stmt->bindParam(':ttype', $ttype);
            $stmt->bindParam(':acid', $cr_acid);
            $stmt->bindParam(':pay_method', $pay_meth);
            $stmt->bindParam(':datee', $now);

            $stmt->bindParam(':mid', $row['userId']);

            $stmt->execute();

            // send sms
            // $sms_body =  'Dear ' . $displayNameSMS . ', your 2024 savings interest is ' . number_format($diff_amount) . '. Merry Christmas & Happy New Year! MOYO SACCO.';

            // check and confirm number is right
            // $phone = $this->getClientPhone($row['userId'], '');

            // send sms


            // if ($phone && !is_null($phone)) {
            /* phone number array hold numbers , iterate & send to each number */
            // $value = $phone[0];
            // foreach ($phone as $value) {
            // check if phone number has country code or not --use 256 by default
            // if ($value[0] == "0" || $value[0] == 0 || $value[0] == "7") {
            // if (
            // $value[0] == "0" || $value[0] == 0
            // ) {
            // $value = '256' . substr($value, 1);
            // } else {
            // $value = '256' . $value;
            // }
            // }
            // send sms
            // $this->SendSMSAuto('INFOSMS', $value, $sms_body);
            // }
            // }
        }

        return true;
    }

    // public function getClientRangeTransactionsBF($mid, $start)
    // {
    //     $sqlQuery = '
    //     SELECT 
    //         SUM(CASE WHEN t_type IN (\'D\', \'A\', \'LC\', \'E\') THEN amount ELSE 0 END) AS total_debit,
    //         SUM(CASE WHEN t_type IN (\'W\', \'LE\', \'C\', \'CW\', \'CS\', \'SMS\', \'LP\', \'RC\', \'I\', \'R\', \'L\') THEN amount ELSE 0 END) AS total_credit,
    //         SUM(CASE WHEN t_type = \'L\' THEN loan_interest ELSE 0 END) AS total_loan_interest
    //     FROM public."transactions"
    //     WHERE mid = :id 
    //       AND DATE(date_created) < :transaction_start_date
    //       AND _status = 1
    //       AND entry_chanel = \'system\'';

    //     $stmt = $this->conn->prepare($sqlQuery);
    //     $stmt->bindParam(':id', $mid);
    //     $stmt->bindParam(':transaction_start_date', $start);
    //     $stmt->execute();

    //     $result = $stmt->fetch();

    //     $totalDebit = $result['total_debit'] ?? 0;
    //     $totalCredit = $result['total_credit'] ?? 0;
    //     $totalLoanInterest = $result['total_loan_interest'] ?? 0;

    //     // Calculate the balance
    //     $balance = $totalDebit - ($totalCredit + $totalLoanInterest);

    //     return $balance;
    // }


    // public function InterestExpenseOnSaving()
    // {
    //     $min_bal = 100000;
    //     $wht = 15;
    //     $year = date('Y');
    //     $months = range(
    //         1,
    //         11
    //     );

    //     // Fetch all members
    //     $membersQuery = 'SELECT u.id AS suid, c."userId", u."firstName", u."lastName", c.acc_balance, u.shared_name, c."branchId"
    //                  FROM public."Client" c
    //                  JOIN public."User" u ON u.id = c."userId" 
    //                  WHERE c."userId" IN(541079) AND  c.auto_status = 0 
    //                    AND c."branchId" IN (\'8fb667c6-b92e-4286-91b0-d31df32e5174\') 
    //                  ORDER BY c."userId" ASC 
    //                  LIMIT 500';
    //     $members = $this->conn->query($membersQuery)->fetchAll();

    //     foreach ($members as $row) {
    //         $yearlyTotal = 0;

    //         // Fetch all transactions for the user for the year
    //         $transactionsQuery = 'SELECT amount, t_type, loan_interest, DATE_PART(\'month\', date_created) AS txn_month
    //                           FROM transactions
    //                           WHERE mid = :userId 
    //                             AND DATE_PART(\'year\', date_created) = :year';
    //         $stmt = $this->conn->prepare($transactionsQuery);
    //         $stmt->execute(['userId' => $row['userId'], 'year' => $year]);
    //         $transactions = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

    //         $bf = $this->getClientRangeTransactionsBF($row['userId'], "$year-01-01");

    //         foreach ($months as $month) {
    //             $monthlyBalance = $bf;

    //             if (isset($transactions[$month])) {
    //                 foreach ($transactions[$month] as $transaction) {
    //                     $type = $transaction['t_type'];
    //                     $amount = $transaction['amount'] + $transaction['loan_interest'];

    //                     if (in_array($type, ["D", "E", "CAP", "LC", "A"])) {
    //                         $monthlyBalance += $amount;
    //                     } elseif (in_array($type, ["W", "LE", "C", "R", "I", "CW", "CS", "SMS", "LP", "RC"]) || $type === "L") {
    //                         $monthlyBalance -= $amount;
    //                     }
    //                 }
    //             }

    //             if ($monthlyBalance < $min_bal) {
    //                 $monthlyBalance = 0;
    //             }

    //             $yearlyTotal += $monthlyBalance;
    //         }

    //         if ($yearlyTotal > 0) {
    //             $generalInterest = round($yearlyTotal * 0.003333);
    //         } else {
    //             $generalInterest = 0;
    //         }

    //         $wht_amount = (int)(($wht / 100) * $generalInterest);
    //         $diff_amount = $generalInterest - $wht_amount;

    //         // Update balance and status
    //         $updateQuery = 'UPDATE public."Client" 
    //                     SET acc_balance = acc_balance + :bal, auto_status = 1 
    //                     WHERE "userId" = :id';
    //         $stmt = $this->conn->prepare($updateQuery);
    //         $stmt->execute(['id' => $row['userId'], 'bal' => $diff_amount]);

    //         // Create interest transaction
    //         $this->createTransaction($row['branchId'], $row['userId'], $generalInterest, 'Interest On Savings for the Year 2024', 'E');
    //         // Create WHT transaction
    //         $this->createTransaction($row['branchId'], $row['userId'], $wht_amount, 'WHT Charged On Interest On Savings for the Year 2024', 'W');
    //     }

    //     return true;
    // }

    private function createTransaction($branchId, $userId, $amount, $description, $type)
    {
        $sqlQuery = 'INSERT INTO public."transactions" 
                 (amount, description, _authorizedby, _actionby, acc_name, approvedby, _branch, t_type, acid, pay_method, date_created, mid, cr_acid) 
                 VALUES (:amount, :descri, :autho, :actby, :accname, :approv, :branch, :ttype, :acid, :pay_method, :datee, :mid, :cr)';
        $stmt = $this->conn->prepare($sqlQuery);

        $auth = 547524;
        $now = date('Y-m-d');
        $cr_acid = $type === 'E' ? '9eace84d-697d-4df9-9eae-92be40ca4be5' : 'f38ba859-fffc-453f-990c-53b12dc9eefd';

        $stmt->execute([
            'amount' => $amount,
            'descri' => $description,
            'autho' => $auth,
            'actby' => $auth,
            'accname' => "Branch $branchId User $userId",
            'approv' => $auth,
            'branch' => $branchId,
            'ttype' => $type,
            'acid' => $cr_acid,
            'pay_method' => 'saving',
            'datee' => $now,
            'mid' => $userId,
            'cr' => $cr_acid
        ]);
    }


    public function getClientPhone($id)
    {
        $sms_nos = [];

        $sqlQuery = 'SELECT "primaryCellPhone","secondaryCellPhone",sms_phone_numbers FROM public."User" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row1 = $stmt->fetch();


        $row1['sms_phone_numbers'] = json_decode($row1['sms_phone_numbers'] ?? '[]');
        if ($row1['sms_phone_numbers'] && count($row1['sms_phone_numbers']) > 0) {
            foreach ($row1['sms_phone_numbers'] as $value) {
                array_push($sms_nos, $value);
            }
        } else {
            if ($row1['primaryCellPhone'] && strlen($row1['primaryCellPhone']) >= 8) {
                array_push($sms_nos, $row1['primaryCellPhone']);
            }
            if ($row1['secondaryCellPhone'] && strlen($row1['secondaryCellPhone']) >= 8) {
                array_push($sms_nos, $row1['secondaryCellPhone']);
            }
        }

        return count($sms_nos) > 0 ? $sms_nos : null;
    }

    public function SendSMSAuto($sender, $number, $message)
    {
        $username = 'ucscucbs';
        $password = 'smsmanage';

        $url = "www.egosms.co/api/v1/plain/?";

        $parameters = "number=[number]&message=[message]&username=[username]&password=[password]&sender=[sender]";
        $parameters = str_replace("[message]", urlencode($message), $parameters);
        $parameters = str_replace("[sender]", urlencode($sender), $parameters);
        $parameters = str_replace("[number]", urlencode($number), $parameters);
        $parameters = str_replace("[username]", urlencode($username), $parameters);
        $parameters = str_replace("[password]", urlencode($password), $parameters);
        $live_url = "https://" . $url . $parameters;
        $parse_url = file($live_url);
        $response = $parse_url[0];
        return $response;
    }


    public function reverseAllLoans()
    {
        // 7938b57c-c2be-4af8-9158-e3d5c174d5be
        $sqlQuery = 'SELECT * FROM loan WHERE branchid=\'7938b57c-c2be-4af8-9158-e3d5c174d5be\'';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        foreach ($stmt as $row) {



            $sqlQuery = 'DELETE FROM loan_schedule WHERE loan_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['loan_no']);
            $stmt->execute();

            $sqlQuery = 'DELETE FROM transactions WHERE loan_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['loan_no']);
            $stmt->execute();

            $sqlQuery = 'DELETE FROM loan WHERE loan_no=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['loan_no']);
            $stmt->execute();
        }

        return true;
    }


    public function uploadAttach()
    {
        $sqlQuery = 'INSERT INTO public.loan_attachments(
	 attach_link, loan_id, attach_name)
	VALUES (:link, :lid, :name)';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':link', $this->updatedAt);
        $stmt->bindParam(':lid', $this->createdAt);
        $stmt->bindParam(':name', $this->location);
        $stmt->execute();
        return true;
    }

    public function addIncomeSource()
    {
        $sqlQuery = 'INSERT INTO public.loan_income_sources(
	 inc_name, inc_lid, inc_desc, inc_returns, inc_attach)
	VALUES (:name, :lid, :descr, :retur, :attach)';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':name', $this->location);
        $stmt->bindParam(':lid', $this->createdAt);
        $stmt->bindParam(':descr', $this->name);
        $stmt->bindParam(':retur', $this->deletedAt);
        $stmt->bindParam(':attach', $this->updatedAt);
        $stmt->execute();
        return true;
    }

    public function approveLoan()
    {

        $sqlQuery = 'UPDATE public."loan"  SET 
    approvedamount=:aa,reviewedbyid=:rbi,status=1,isapproved=true,approved_loan_duration=:ald,
    monthly_interest_rate=:mir,notes=:notes,principal=:prr,disbursedamount=:rreb,repay_cycle_id=:rcid, approval_date=:adate
 WHERE  public."loan".loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':adate', $this->approval_date);
        $stmt->bindParam(':aa', $this->location);
        $stmt->bindParam(':rreb', $this->location);
        $stmt->bindParam(':prr', $this->location);
        $stmt->bindParam(':rbi', $this->serialNumber);
        $stmt->bindParam(':ald', $this->name);
        $stmt->bindParam(':mir', $this->updatedAt);
        $stmt->bindParam(':notes', $this->deletedAt);
        $stmt->bindParam(':rcid', $this->loan_id);
        $stmt->execute();

        $this->applyLoanSchedule($this->createdAt);

        $this->updateTotalLoanAmount($this->createdAt);


        return true;
    }

    public function unsubscribeSMSClient()
    {

        $sqlQuery = 'UPDATE public."Client"  SET message_consent=0 WHERE  "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        return true;
    }

    public function subscribeSMSClient()
    {

        $sqlQuery = 'UPDATE public."Client"  SET message_consent=1 WHERE  "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        return true;
    }

    public function subscribeAllSMSTypes()
    {

        $sqlQuery = 'UPDATE public."sms_types"  SET s_status=1 WHERE  bank_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        return true;
    }

    public function editSMSType()
    {

        $sqlQuery = 'UPDATE public."sms_types"  SET charge=:charge,charged_to=:cto,temp_body=:tbody WHERE  st_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':charge', $this->bank);
        $stmt->bindParam(':cto', $this->branch);
        $stmt->bindParam(':tbody', $this->id);
        $stmt->bindParam(':id', $this->name);
        $stmt->execute();
        return true;
    }

    public function setBankSMSACIDS()
    {

        $sqlQuery = 'UPDATE public."Bank"  SET sms_income_acid=:charge,sms_exp_acid=:cto WHERE  id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':charge', $this->name);
        $stmt->bindParam(':cto', $this->id);
        $stmt->bindParam(':id', $this->description);
        $stmt->execute();
        return true;
    }

    public function setBankFDACIDS()
    {

        $sqlQuery = 'UPDATE public."Branch"  SET fd_princ_acid=:charge,fd_int_acid=:cto,fd_wht_acid=:wht WHERE  id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':charge', $this->name);
        $stmt->bindParam(':cto', $this->id);
        $stmt->bindParam(':id', $this->description);
        $stmt->bindParam(':wht', $this->wht);
        $stmt->execute();
        return true;
    }

    public function addLoanComments()
    {

        $sqlQuery = 'UPDATE public."loan" SET notes=:nn WHERE loan_no=:lno';
        $nn =  $this->name;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $this->auth_id);
        $stmt->bindParam(':nn', $nn);
        $stmt->execute();

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Commented on Loan No. ' . $this->auth_id;
        $auditTrail->staff_id = $this->branch;
        $auditTrail->bank_id = $this->id;
        $auditTrail->branch_id = $this->bank;

        $auditTrail->log_message = $this->name;

        // $auditTrail->staff_id = 1;
        // $auditTrail->branch_id = 1;
        $auditTrail->create();

        return true;
    }

    public function addSMSType()
    {

        $sqlQuery = 'INSERT INTO public."sms_types" ( charge, charged_to, sms_sent_on, action_name, act_desc, temp_body, added_by,bank_id) VALUES (

:charge,:cto,:sms_sent_on,:actname,:actdesc,:tbody,:adby,:bid

        )';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':charge', $this->bank);
        $stmt->bindParam(':cto', $this->branch);
        $stmt->bindParam(':tbody', $this->id);
        $stmt->bindParam(':sms_sent_on', $this->description);
        $stmt->bindParam(':actname', $this->name);
        $stmt->bindParam(':actdesc', $this->name);
        $stmt->bindParam(':adby', $this->location);
        $stmt->bindParam(':bid', $this->bank_acc);
        $stmt->execute();

        //         $sqlQuery = 'SELECT * FROM  public."Bank" WHERE  bank_status=1';

        //         $stmt = $this->conn->prepare($sqlQuery);
        //         $stmt->execute();
        //         foreach ($stmt as $row) {
        //             $sqlQuery = 'INSERT INTO public."sms_types" ( bank_id,charge, charged_to, sms_sent_on, action_name, act_desc, temp_body, added_by) VALUES (:bid,

        // :charge,:cto,:sms_sent_on,:actname,:actdesc,:tbody,:adby

        //         )';

        //             $stmt = $this->conn->prepare($sqlQuery);
        //             $stmt->bindParam(':charge', $this->bank);
        //             $stmt->bindParam(':cto', $this->branch);
        //             $stmt->bindParam(':tbody', $this->id);
        //             $stmt->bindParam(':sms_sent_on', $this->description);
        //             $stmt->bindParam(':actname', $this->name);
        //             $stmt->bindParam(':actdesc', $this->name);
        //             $stmt->bindParam(':adby', $this->location);
        //             $stmt->bindParam(':bid', $row['id']);
        //             $stmt->execute();
        //         }

        return true;
    }

    public function subscribeAllSMSType()
    {

        $sqlQuery = 'UPDATE public."sms_types"  SET s_status=1 WHERE  st_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        return true;
    }
    public function unsubscribeAllSMSType()
    {

        $sqlQuery = 'UPDATE public."sms_types"  SET s_status=0 WHERE  st_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        return true;
    }

    public function unsubscribeAllSMSTypes()
    {

        $sqlQuery = 'UPDATE public."sms_types"  SET s_status=0 WHERE  bank_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        return true;
    }

    public function subscribeSMSAllClient()
    {

        $sqlQuery = 'UPDATE public."Client"  SET message_consent=1 WHERE "branchId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        return true;
    }
    public function subscribeSMSAllBankClient()
    {
        $sqlQuery = 'SELECT  * FROM  public."Branch" WHERE "bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();

        foreach ($stmt as $row) {
            $sqlQuery = 'UPDATE public."Client"  SET message_consent=1 WHERE "branchId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['id']);
            $stmt->execute();
        }


        return true;
    }

    public function unsubscribeSMSAllBankClient()
    {
        $sqlQuery = 'SELECT  * FROM  public."Branch" WHERE "bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();

        foreach ($stmt as $row) {
            $sqlQuery = 'UPDATE public."Client"  SET message_consent=0 WHERE "branchId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['id']);
            $stmt->execute();
        }


        return true;
    }

    public function unsubscribeSMSAllClient()
    {

        $sqlQuery = 'UPDATE public."Client"  SET message_consent=0 WHERE "branchId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        return true;
    }

    public function approveSMSPurchase()
    {

        $sqlQuery = 'UPDATE public."sms_topup_transactions"  SET status=1 WHERE  public."sms_topup_transactions".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();

        $sqlQuery = 'SELECT * FROM public."sms_topup_transactions" WHERE  public."sms_topup_transactions".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'UPDATE public."Branch" SET  sms_amount_loaded=sms_amount_loaded+:ac, sms_balance=sms_balance+:ac
        WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $row['branchid']);
        $stmt->bindParam(':ac', $row['amount']);
        $stmt->execute();


        // $sqlQuery = 'SELECT *, public."Branch".name AS bname FROM  public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId" = public."Bank".id  WHERE public."Branch".id=:id';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $row['branchid']);
        // $stmt->execute();

        // $rown = $stmt->fetch();

        // $sqlQuery = 'SELECT * FROM  public."Account"  WHERE public."Account".id=:id';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $rown['sms_exp_acid']);
        // $stmt->execute();

        // $rowx = $stmt->fetch();

        // $sqlQuery = 'SELECT * FROM  public."Account"  WHERE account_code_used=:id AND "branchId"=:bid';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $rowx['account_code_used']);
        // $stmt->bindParam(':bid', $row['branchid']);
        // $stmt->execute();

        // $my = $stmt->fetch();

        // $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal  WHERE id=:id';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $my['id']);
        // $stmt->bindParam(':bal', $row['amount']);
        // $stmt->execute();


        // $tt_type = 'E';
        // $desc = 'SMS Purchase for - '.$rown['bname'];
        // $pmethod = 'cash';

        // $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        // acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,date_created,charges) VALUES
        //   (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:date_created,:charges)';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':amount', $row['amount']);
        // $stmt->bindParam(':descri', $desc);
        // $stmt->bindParam(':autho', $this->_authorizedby);
        // $stmt->bindParam(':actby', $this->_actionby);
        // $stmt->bindParam(':accname', $my['name']);
        // $stmt->bindParam(':mid', $this->mid);
        // $stmt->bindParam(':approv', $this->_authorizedby);
        // $stmt->bindParam(':branc', $this->_branch);
        // $stmt->bindParam(':leftbal', $this->left_balance);
        // $stmt->bindParam(':ttype', $tt_type);
        // $stmt->bindParam(':acid', $my['id']);
        // $stmt->bindParam(':pay_method', $pmethod);
        // $stmt->bindParam(':bacid', $this->bacid);
        // $stmt->bindParam(':cheque', $this->cheque_no);
        // $stmt->bindParam(':cash_acc', $this->cash_acc);
        // // $stmt->bindParam(':send_sms', $this->send_sms);
        // $stmt->bindParam(':date_created', $this->date_created);
        // $stmt->bindParam(':charges', $charges);


        // $stmt->execute();


        return true;
    }

    public function declineSMSPurchase()
    {

        $sqlQuery = 'UPDATE public."sms_topup_transactions"  SET status=3 WHERE  public."sms_topup_transactions".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();




        return true;
    }

    public function declineBranchRequest()
    {

        $sqlQuery = 'UPDATE public."inter_branch_requests"  SET req_status=3 WHERE  public."inter_branch_requests".req_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();




        return true;
    }

    public function deactivateStaff()
    {
        $stt = 'INACTIVE';
        $sqlQuery = 'UPDATE public."User"  SET status=:stt WHERE  public."User".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':stt', $stt);
        $stmt->execute();




        return true;
    }

    public function activateStaff()
    {
        $stt = 'ACTIVE';
        $sqlQuery = 'UPDATE public."User"  SET status=:stt WHERE  public."User".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':stt', $stt);
        $stmt->execute();




        return true;
    }

    public function trashOverDraft()
    {
        $sqlQuery = 'UPDATE public."over_drafts"  SET deleted_st=1 WHERE  public."over_drafts".odid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();




        return true;
    }

    public function declineOverDraft()
    {
        $sqlQuery = 'UPDATE public."over_drafts"  SET status=2 WHERE  public."over_drafts".odid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();




        return true;
    }

    public function approveOverDraft()
    {

        $sqlQuery = 'SELECT * FROM public."over_drafts" LEFT JOIN public."User" ON public."User".id=public."over_drafts".uid WHERE  public."over_drafts".odid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        $row = $stmt->fetch();

        // update acc_balance
        $sqlQuery = 'UPDATE public."Client"  SET over_draft=over_draft + :ac, acc_balance= acc_balance + :ac WHERE  public."Client"."userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $row['uid']);
        $stmt->bindParam(':ac', $row['amount']);
        $stmt->execute();

        // create disburse trxn
        $acc_name = $row['firstName'] . ' ' . $row['lastName'] . ' ' . $row['shared_name'];
        $t_type = 'D';
        $pay_method = 'over_draft';
        $des = 'Over-Draft Disbursement - D' . $row['odid'];

        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $row['amount']);
        $stmt->bindParam(':descri', $des);
        $stmt->bindParam(':autho', $row['authby']);
        $stmt->bindParam(':actby', $row['authby']);
        $stmt->bindParam(':accname', $acc_name);
        $stmt->bindParam(':mid', $row['uid']);
        $stmt->bindParam(':approv', $row['authby']);
        $stmt->bindParam(':branc', $row['branch']);
        $stmt->bindParam(':ttype', $t_type);
        $stmt->bindParam(':pay_method', $pay_method);
        $stmt->execute();


        // update over-draft status
        $dt = date('Y-m-d');
        $sqlQuery = 'UPDATE public."over_drafts"  SET status=1, approval_date=:dt WHERE  public."over_drafts".odid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':dt', $dt);
        $stmt->execute();




        return true;
    }

    public function deactivateRole()
    {
        $sqlQuery = 'UPDATE public."Role"  SET rolestatus=2 WHERE  public."Role".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();




        return true;
    }

    public function deactivateBankAccount()
    {
        $sqlQuery = 'UPDATE public."bank_accounts"  SET ac_status=0 WHERE  public."bank_accounts".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();




        return true;
    }
    public function activateBankAccount()
    {
        $sqlQuery = 'UPDATE public."bank_accounts"  SET ac_status=1 WHERE  public."bank_accounts".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();




        return true;
    }

    public function getAccBalVal($lpid, $bid, $acid, $start, $end, $type = 0)
    {
        $binding_array = [];
        $sqlQuery = 'SELECT * FROM public."Account" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $acid);
        $stmt->execute();
        $row = $stmt->fetch();



        if (str_contains($row['name'], 'Penalty')) {
            // for penalty income accounts

            $sqlQuery = 'SELECT SUM(amount) AS tot FROM transactions WHERE (transactions.acid=:acid OR transactions.cr_acid::uuid=:acid OR transactions.dr_acid::uuid=:acid) ';

            if (@$start && @$end) {
                $sqlQuery .= ' AND DATE(date_created) >= :transaction_start_date AND DATE(date_created) <= :transaction_end_date ';
                $binding_array[':transaction_start_date'] = @$start;
                $binding_array[':transaction_end_date'] = @$end;
            }
            $binding_array[':acid'] = @$row['id'];
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute($binding_array);
            $rown = $stmt->fetch();



            return $rown['tot'] ?? 0;
        } else {

            if ($row['type'] == 'INCOMES') {
                if ($lpid) {
                    $binding_array = [];
                    // for interest income accounts,
                    // get sum of interest in loan repayments (trxns) for all loans of this loan product
                    $sqlQuery = 'SELECT SUM(loan_interest) AS tot_int FROM transactions WHERE _branch=:bid AND loan_id IN(SELECT loan_no from loan where loan_type=:id) ';

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(date_created) >= :transaction_start_date AND DATE(date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = $start;
                        $binding_array[':transaction_end_date'] = $end;
                    }

                    $binding_array[':id'] = $lpid;
                    $binding_array[':bid'] = $row['branchId'];
                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->execute($binding_array);
                    $rown = $stmt->fetch();

                    $tot_to_take = $rown['tot_int'];

                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(transaction.amount) as tot2 FROM public.transactions AS transaction 
                WHERE transaction.entry_chanel=\'data_importer\' AND (transaction.acid=:id OR transaction.cr_acid::uuid=:id) ';

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transaction.date_created) >= :transaction_start_date AND DATE(transaction.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = $start;
                        $binding_array[':transaction_end_date'] = $end;
                    }
                    $binding_array[':id'] = $row['id'];
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rowx = $stmt->fetch();

                    $tot_to_take = $tot_to_take + $rowx['tot2'];


                    return $tot_to_take;
                } else {

                    $binding_array = [];
                    $sqlQuery = 'SELECT SUM(transaction.amount) as tot2 FROM public.transactions AS transaction 
                WHERE  (transaction.acid=:id OR transaction.cr_acid::uuid=:id) ';

                    if (@$start && @$end) {
                        $sqlQuery .= ' AND DATE(transaction.date_created) >= :transaction_start_date AND DATE(transaction.date_created) <= :transaction_end_date ';
                        $binding_array[':transaction_start_date'] = $start;
                        $binding_array[':transaction_end_date'] = $end;
                    }

                    $binding_array[':id'] = $row['id'];

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->execute($binding_array);
                    $rowx = $stmt->fetch();

                    $tot_to_take =  $rowx['tot2'];


                    return $tot_to_take;
                }
            } else {
                if ($type == 0) {
                    $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions LEFT JOIN loan ON transactions.loan_id=loan.loan_no where loan.loan_type=:id AND transactions._branch=:bid AND transactions.t_type=\'A\'';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $lpid);
                    $stmt->bindParam(':bid', $row['branchId']);
                    $stmt->execute();
                    $rown = $stmt->fetch();

                    $tot_to_take = $rown['tot3'];

                    $sqlQuery = 'SELECT SUM(transaction.amount) as tot2 FROM public.transactions AS transaction 
                WHERE transaction.entry_chanel=\'data_importer\' AND transaction.acid=:id OR transaction.cr_acid::uuid=:id';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $row['id']);
                    $stmt->execute();
                    $rowx = $stmt->fetch();

                    $tot_to_take = $tot_to_take + $rowx['tot2'];


                    return $tot_to_take;
                } else {
                    $sqlQuery = 'SELECT SUM(acc_balance) AS tot3 from "Client" where actype=:id AND "branchId"=:bid';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $lpid);
                    $stmt->bindParam(':bid', $row['branchId']);
                    $stmt->execute();
                    $rown = $stmt->fetch();

                    $tot_to_take = $rown['tot3'];

                    $sqlQuery = 'SELECT SUM(transaction.amount) as tot2 FROM public.transactions AS transaction 
                WHERE transaction.entry_chanel=\'data_importer\' AND transaction.acid=:id OR transaction.cr_acid::uuid=:id';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $row['id']);
                    $stmt->execute();
                    $rowx = $stmt->fetch();

                    $tot_to_take = $tot_to_take + $rowx['tot2'];


                    return $tot_to_take;
                }
            }
        }


        return 0;
    }

    public function getAccBalVal2($lpid, $bid, $acid, $type = 0)
    {
        $sqlQuery = 'SELECT * FROM public."Account" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $acid);
        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'SELECT SUM(amount) AS tot3 from transactions LEFT JOIN loan ON transactions.loan_id=loan.loan_no where loan.loan_type=:id AND transactions._branch=:bid AND transactions.t_type=\'L\'';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $lpid);
        $stmt->bindParam(':bid', $row['branchId']);
        $stmt->execute();
        $rown = $stmt->fetch();

        $tot_to_take = $rown['tot3'];


        $sqlQuery = 'SELECT SUM(transaction.amount) as tot2 FROM public.transactions AS transaction 
                WHERE transaction.entry_chanel=\'data_importer\' AND transaction.acid=:id OR transaction.cr_acid::uuid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $row['id']);
        $stmt->execute();
        $rowx = $stmt->fetch();

        $tot_to_take = $tot_to_take + $rowx['tot2'];


        return $tot_to_take;
    }


    public function activateRole()
    {
        $sqlQuery = 'UPDATE public."Role"  SET rolestatus=1 WHERE  public."Role".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();




        return true;
    }

    public function getAccBalancePerMonthlyAnnual($userid, $date)
    {

        $sqlQuery = 'SELECT 
    SUM(CASE 
        WHEN t_type IN (\'D\', \'A\', \'E\', \'LC\',\'CAP\') THEN amount 
        ELSE 0 
    END) AS plus_total,
    SUM(CASE 
        WHEN t_type IN (\'W\', \'I\', \'SMS\', \'LE\', \'C\', \'CS\', \'LP\', \'R\', \'RC\') THEN amount 
        WHEN t_type = \'L\' THEN loan_interest + amount
        ELSE 0 
    END) AS minus_total
FROM 
    public."transactions"
WHERE  mid = :id AND entry_chanel=\'system\' AND DATE(public."transactions".date_created) >= \'1900-01-01\' AND DATE(public."transactions".date_created) <= \'2024-11-30\'

      
        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $userid);

        $stmt->execute();

        $row = $stmt->fetch();

        $plus_tot = $row['plus_total'] ?? 0;
        $minus_tot = $row['minus_total'] ?? 0;


        $total = 0;

        $total = $plus_tot  - $minus_tot;

        return $total ?? 0;
    }

    public function resetActype()
    {
        return true;
    }

    public function MembershipRenewal($date, $bid, $pid, $fees_acid, $amount, $auth_id)
    {

        // SELECT * FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id  WHERE actype=:actyp AND "branchId"=:bid
        $sqlQuery = 'SELECT *, public."Client"."createdAt" AS odate FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id  WHERE  "branchId"=:bid AND rec_column=0 ORDER BY public."Client".id ASC';
        $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':actyp', $pid);
        $stmt->bindParam(':bid', $bid);
        $stmt->execute();

        foreach ($stmt as $row) {

            $year = 2023;
            $orig_year = (int)(date('Y', strtotime($date)));
            $diff = 0;
            if ($row['odate']) {
                $year = (int)(date('Y', strtotime($row['odate'])));
            }
            $diff = max(($orig_year - $year), 0);

            if ($diff >= 1) {

                if ($row['acc_balance'] >= $amount) {

                    $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:bal, membership_renewal_status=2,rec_column=1 where "userId"=:id ';
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $row['userId']);
                    $stmt->bindParam(':bal', $amount);
                    $stmt->execute();

                    // create interest trxn
                    $acc_name = $row['firstName'] . ' ' . $row['lastName'] . ' ' . $row['shared_name'];
                    $t_type = 'I';
                    $pm = 'saving';
                    $leftbal = 0;
                    $dess = 'Membership Renewal Fees for the Year: ' . $year;

                    $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:date_created)';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':amount', $amount);
                    $stmt->bindParam(':descri', $dess);
                    $stmt->bindParam(':autho', $auth_id);
                    $stmt->bindParam(':actby', $acc_name);
                    $stmt->bindParam(':accname', $acc_name);
                    $stmt->bindParam(':mid', $row['userId']);
                    $stmt->bindParam(':approv', $auth_id);
                    $stmt->bindParam(':branc', $bid);
                    $stmt->bindParam(':leftbal', $leftbal);
                    $stmt->bindParam(':ttype', $t_type);
                    $stmt->bindParam(':acid', $fees_acid);
                    $stmt->bindParam(':pay_method', $pm);
                    $stmt->bindParam(':date_created', $date);


                    $stmt->execute();
                } else {
                    $sqlQuery = 'UPDATE public."Client" SET membership_renewal_status=0,rec_column=1 where "userId"=:id ';
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $row['userId']);
                    $stmt->execute();
                }
            }
        }

        return true;
    }

    public function deleteClientsBranch($bid)
    {

        $sqlQuery = 'select * from "Client" where "branchId"=:lno';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $bid);
        $stmt->execute();

        foreach ($stmt as $row) {
            $sqlQuery = 'delete from "Client" where "userId"=:lno';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':lno', $row['userId']);
            $stmt->execute();

            $sqlQuery = 'delete from "User" where id=:lno';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':lno', $row['userId']);
            $stmt->execute();
        }

        return true;
    }

    public function sortNow($lid)
    {

        // $this->updateTotalLoanAmount(9475);

        $sqlQuery = 'select * from loan where loan_no=:lno';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $lid);
        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'select SUM(amount) AS p_paid from transactions where loan_id=:lno AND t_type=\'L\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $lid);
        $stmt->execute();
        $rown = $stmt->fetch();
        $p_paid = $rown['p_paid'] ?? 0;

        $sqlQuery = 'select SUM(loan_interest) AS i_paid from transactions where loan_id=:lno AND t_type=\'L\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $lid);
        $stmt->execute();
        $rown = $stmt->fetch();
        $i_paid = $rown['i_paid'] ?? 0;

        $p_bal = $row['principal'] - $p_paid;
        $i_bal = $row['interest_amount'] - $i_paid;
        $t_bal = $p_bal + $i_bal;
        $t_paid = $p_paid + $i_paid;

        $sqlQuery = 'update loan set principal_balance=:p_bal, interest_balance=:i_bal,current_balance=:t_bal,amount_paid=:t_paid where loan_no=:lno
';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $lid);
        $stmt->bindParam(':p_bal', $p_bal);
        $stmt->bindParam(':i_bal', $i_bal);
        $stmt->bindParam(':t_bal', $t_bal);
        $stmt->bindParam(':t_paid', $t_paid);
        $stmt->execute();

        return true;
    }

    public function shareDividendsCron()
    {
        $sqlQuery = 'select * from share_register where share_register.branch_id=\'faaaf847-1e1d-455b-b3d6-995efba6874c\' AND date_added<=\'2021-12-31\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        foreach ($stmt as $row) {
            // AMOUNT  ==  2983842
            // shares total = 7054
            $amount  = (int)(($row['no_shares'] / 48256) * 2983842);
        }
        return true;
    }

    public function InterestOnSavings()
    {

        $min_bal = 100000;
        $wht = 15;

        $sqlQuery = 'SELECT public."Client"."userId" , public."User"."firstName", public."User"."lastName", public."User".shared_name, public."Client"."branchId" FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id  WHERE "branchId"=\'8fb667c6-b92e-4286-91b0-d31df32e5174\' AND auto_status=0 ORDER BY public."Client"."userId" ASC LIMIT 100';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $row) {
            $int_amount = 0;
            $wht_amount = 0;
            $acc_bal = $this->getAccBalancePerMonthlyAnnual($row['userId'], '2024-11-30');
            if ($acc_bal >= $min_bal) {
                $int_amount = (int)((0.04) * $acc_bal);
                $wht_amount = (int)(($wht / 100) * $int_amount);
            }

            $diff_amount = (int)($int_amount - $wht_amount);
            $diff_amount = max($diff_amount, 0);
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:bal, auto_status=1 where "userId"=:id ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['userId']);
            $stmt->bindParam(':bal', $diff_amount);
            $stmt->execute();


            $displayName = !empty($row['shared_name']) ? $row['shared_name'] : $row['firstName'] . ' ' . $row['lastName'];
            $displayNameSMS = !empty($row['shared_name']) ? $row['shared_name'] : $row['firstName'];



            // create interest trxn
            // create expense transaction via savings with chart acc id for corresponding branch
            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created,mid,cr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee,:mid,:cr)';

            $cr_acid = '9eace84d-697d-4df9-9eae-92be40ca4be5';
            $des = 'Interest On Savings for the Year 2024';
            $auth = 547524;
            $ttype = 'E';
            $pay_meth = 'saving';
            $now = date('Y-m-d');
            $client_name = $displayName;

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount', $int_amount);
            $stmt->bindParam(':cr', $cr_acid);
            $stmt->bindParam(':descri', $des);
            $stmt->bindParam(':autho', $auth);
            $stmt->bindParam(':actby', $auth);
            $stmt->bindParam(':accname', $client_name);
            $stmt->bindParam(':approv', $auth);
            $stmt->bindParam(':branc', $row['branchId']);
            $stmt->bindParam(':ttype', $ttype);
            $stmt->bindParam(':acid', $cr_acid);
            $stmt->bindParam(':pay_method', $pay_meth);
            $stmt->bindParam(':datee', $now);

            $stmt->bindParam(':mid', $row['userId']);

            $stmt->execute();


            // wht trxn liabilty
            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created,mid,cr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee,:mid,:cr)';

            $cr_acid = 'f38ba859-fffc-453f-990c-53b12dc9eefd';
            $des = 'WHT Charged On Interest On Savings for the Year 2024';
            $auth = 547524;
            $ttype = 'W';
            $pay_meth = 'saving';
            $now = date('Y-m-d');
            $client_name = $displayName;

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount', $wht_amount);
            $stmt->bindParam(':cr', $cr_acid);
            $stmt->bindParam(':descri', $des);
            $stmt->bindParam(':autho', $auth);
            $stmt->bindParam(':actby', $auth);
            $stmt->bindParam(':accname', $client_name);
            $stmt->bindParam(':approv', $auth);
            $stmt->bindParam(':branc', $row['branchId']);
            $stmt->bindParam(':ttype', $ttype);
            $stmt->bindParam(':acid', $cr_acid);
            $stmt->bindParam(':pay_method', $pay_meth);
            $stmt->bindParam(':datee', $now);

            $stmt->bindParam(':mid', $row['userId']);

            $stmt->execute();

            // send sms
            $sms_body =  'Dear ' . $displayNameSMS . ', your 2024 savings interest is ' . number_format($diff_amount) . '. Merry Christmas & Happy New Year! MOYO SACCO.';

            // check and confirm number is right
            $phone = $this->getClientPhone($row['userId'], '');

            // send sms


            if ($phone && !is_null($phone)) {
                /* phone number array hold numbers , iterate & send to each number */
                $value = $phone[0];
                // foreach ($phone as $value) {
                // check if phone number has country code or not --use 256 by default
                if ($value[0] == "0" || $value[0] == 0 || $value[0] == "7") {
                    if (
                        $value[0] == "0" || $value[0] == 0
                    ) {
                        $value = '256' . substr($value, 1);
                    } else {
                        $value = '256' . $value;
                    }
                }
                // send sms
                $this->SendSMSAuto('INFOSMS', $value, $sms_body);
                // }
            }
        }
        return true;
    }

    public function SendSMS($sender, $number, $message)
    {
        $username = 'WINNERSSACCO';
        $password = 'MyTrustZero';

        $url = "www.egosms.co/api/v1/plain/?";

        $parameters = "number=[number]&message=[message]&username=[username]&password=[password]&sender=[sender]";
        $parameters = str_replace("[message]", urlencode($message), $parameters);
        $parameters = str_replace("[sender]", urlencode($sender), $parameters);
        $parameters = str_replace("[number]", urlencode($number), $parameters);
        $parameters = str_replace("[username]", urlencode($username), $parameters);
        $parameters = str_replace("[password]", urlencode($password), $parameters);
        $live_url = "https://" . $url . $parameters;
        $parse_url = file($live_url);
        $response = $parse_url[0];
        return $response;
    }

    public function getAllBankOverDraftProducts()
    {
        $sqlQuery = 'SELECT * FROM public."over_draft_products"  WHERE  public."over_draft_products".bankid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function getAllBankOverDrafts()
    {
        $sqlQuery = 'SELECT *,public."over_drafts".status AS ostatus  FROM public."over_drafts" LEFT JOIN public."Client" ON public."over_drafts".uid = public."Client"."userId" LEFT JOIN public."User" ON public."over_drafts".uid = public."User".id LEFT JOIN public."Branch" ON public."over_drafts".branch=public."Branch".id  WHERE  public."Branch"."bankId"=:id AND public."over_drafts".deleted_st=0';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function getOverDraftDetails()
    {
        $sqlQuery = 'SELECT *,public."over_drafts".status AS ostatus  FROM public."over_drafts" LEFT JOIN public."Client" ON public."over_drafts".uid = public."Client"."userId" LEFT JOIN public."User" ON public."over_drafts".uid = public."User".id LEFT JOIN public."Branch" ON public."over_drafts".branch=public."Branch".id  WHERE  public."over_drafts".odid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt;
    }
    public function getTotalBranchFixedDeposits()
    {


        $sqlQuery = 'SELECT SUM(fd_amount) AS total FROM public."fixed_deposits" WHERE public."fixed_deposits".fd_branch=:bid AND public."fixed_deposits".fd_status=0';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBranchMaturedFixedDeposits()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."fixed_deposits" WHERE public."fixed_deposits".fd_branch=:bid AND public."fixed_deposits".fd_status=0 AND DATE(public."fixed_deposits".fd_maturity_date) <= current_date ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
    public function getTotalBranchIntPaidFixedDeposits()
    {


        $sqlQuery = 'SELECT SUM(fd_int_paid) AS total FROM public."fixed_deposits" WHERE public."fixed_deposits".fd_branch=:bid  ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }



    public function getTotalBankMaturedFixedDeposits()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."fixed_deposits" LEFT JOIN public."Branch" ON public."fixed_deposits".fd_branch=public."Branch".id WHERE public."Branch"."bankId"=:bid AND public."fixed_deposits".fd_status=0 AND DATE(public."fixed_deposits".fd_maturity_date) <= current_date ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getTotalBankIntPaidFixedDeposits()
    {


        $sqlQuery = 'SELECT SUM(fd_int_paid) AS total FROM public."fixed_deposits" LEFT JOIN public."Branch" ON public."fixed_deposits".fd_branch=public."Branch".id WHERE public."Branch"."bankId"=:bid  ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getTotalBankFixedDeposits()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT SUM(fd_amount) AS total FROM public."fixed_deposits" WHERE public."fixed_deposits".fd_branch=:bid AND public."fixed_deposits".fd_status=0';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + ($row['total'] ?? 0);
        }

        return $mytot;
    }
    public function getTotalBranchDormantAccs()
    {

        // dormant accs --- no trxn for the last one month --- 30 days

        $sqlQuery = 'SELECT  COUNT(*) AS tot  FROM public."Client" WHERE public."Client"."branchId"=:bid AND (SELECT COUNT(*) AS tot FROM public."transactions" where public."transactions".mid=public."Client"."userId" AND DATE(public."transactions".date_created) >= NOW() - INTERVAL \'30 days\')>1';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
        // return 0;
    }

    public function getBankOnlineWithdrawsToday()
    {
        $tt = 'W';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE  AND public."transactions".pay_method IN( \'mobile_money\',\'flutterwave\')';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBankOnlineDepositsToday()
    {
        $tt = 'D';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE  AND public."transactions".pay_method IN( \'mobile_money\',\'flutterwave\')';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getBranchOnlineDepositsToday()
    {
        $tt = 'D';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."transactions"._branch=:id AND DATE(public."transactions".date_created)=CURRENT_DATE  AND public."transactions".pay_method IN( \'mobile_money\',\'flutterwave\')';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getBranchOnlineWithdrawsToday()
    {
        $tt = 'W';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."transactions"._branch=:id AND DATE(public."transactions".date_created)=CURRENT_DATE  AND public."transactions".pay_method IN( \'mobile_money\',\'flutterwave\')';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getTotalBankClientsSavingBalancesInv()
    {
        $mytot = 0;
        // $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        // $stmtn = $this->conn->prepare($sqlQueryn);
        // $stmtn->bindParam(':id', $this->bank);

        // $stmtn->execute();
        // foreach ($stmtn as $row) {
        //     $sqlQuery = 'SELECT SUM(acc_balance) AS total FROM public."Client" LEFT JOIN public."savingaccounts" ON public."Client".actype = public."savingaccounts".id WHERE public."Client"."branchId"=:bid AND public."savingaccounts".is_investment_acc=1';
        //     $stmt = $this->conn->prepare($sqlQuery);
        //     $stmt->bindParam(':bid', $row['id']);

        //     $stmt->execute();
        //     $row = $stmt->fetch();
        //     $mytot = $mytot + $row['total'];
        // }

        return $mytot;
    }
    public function getTotalBranchClientsSavingBalancesInv()
    {


        // $sqlQuery = 'SELECT SUM(acc_balance) AS total FROM public."Client" LEFT JOIN public."savingaccounts" ON public."Client".actype=public."savingaccounts".id WHERE public."Client"."branchId"=:bid AND public."savingaccounts".is_investment_acc=1';
        // $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':bid', $this->branch);

        // $stmt->execute();
        // $row = $stmt->fetch();
        // return $row['total'];
        return 0;
    }
    public function getTotalBankDormantAccs()
    {

        $sqlQuery = 'SELECT  COUNT(*) AS tot  FROM public."Client" LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid AND (SELECT COUNT(*) AS tot FROM public."transactions" where public."transactions".mid=public."Client"."userId" AND DATE(public."transactions".date_created) >= NOW() - INTERVAL \'30 days\')>1';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
    }
    public function getTotalBranchFixedDepositsCount()
    {


        $sqlQuery = 'SELECT COUNT(*) AS total FROM public."fixed_deposits" WHERE public."fixed_deposits".fd_branch=:bid AND public."fixed_deposits".fd_status=0';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getTotalBankFixedDepositsCount()
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->bank);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."fixed_deposits" WHERE public."fixed_deposits".fd_branch=:bid AND public."fixed_deposits".fd_status=0';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }
    public function getBankLiabilitiesToday()
    {
        $tt = 'LIA';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getBankAssetsToday()
    {
        $tt = 'ASS';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch = public."Branch".id WHERE t_type=:tt  AND public."Branch"."bankId"=:id AND DATE(public."transactions".date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->bank);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getUserTotalLiabilities()
    {
        $tt = 'LIA';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:tt  AND _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
    public function getUserTotalAssets()
    {
        $tt = 'ASS';
        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:tt  AND _authorizedby=:id AND DATE(date_created)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tt', $tt);
        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }

    public function getUserTotalSharePurchases()
    {

        $sqlQuery = 'SELECT SUM(amount) AS total, SUM(no_of_shares) AS sh FROM public."share_purchases" WHERE  added_by=:id AND DATE(record_date)=CURRENT_DATE ';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->user);

        $stmt->execute();
        $row = $stmt->fetch();
        $sh_amount = number_format($row['total'] ?? 0);
        $sh_shares = $row['sh'] ?? 0;

        return 'Amount: ' . $sh_amount . '  |  Shares: ' . $sh_shares;
    }

    public function getAllBranchOverDrafts()
    {
        $sqlQuery = 'SELECT *,public."over_drafts".status AS ostatus FROM public."over_drafts" LEFT JOIN public."Client" ON public."over_drafts".uid = public."Client"."userId" LEFT JOIN public."User" ON public."over_drafts".uid = public."User".id LEFT JOIN public."Branch" ON public."over_drafts".branch=public."Branch".id   WHERE  public."over_drafts".branch=:id AND public."over_drafts".deleted_st=0';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function getChartAccountDetails($id)
    {
        $sqlQuery = 'SELECT * FROM public."Account"  WHERE  public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['name'];
    }

    public function getAllBranchOverDraftProducts()
    {
        $sqlQuery = 'SELECT * FROM public."over_draft_products" LEFT JOIN public."Branch" ON public."over_draft_products".bankid=public."Branch"."bankId" WHERE  public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt;
    }

    public function convertClientType()
    {

        if ($this->deletedAt == 1) {
            $sqlQuery = 'UPDATE public."Client"  SET client_type=:ct WHERE  public."Client"."userId"=:id';
            $ct = 'individual';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':ct', $ct);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();

            $sqlQuery = 'SELECT * FROM public."User" WHERE  public."User".id=:id';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();
            $row = $stmt->fetch();

            $use_name = @$row['firstName'] . @$row['lastName'] . @$row['shared_name'];
            $share_n = '';

            $sqlQuery = 'UPDATE public."User"  SET "firstName"=:ctt,"lastName"=:cy, shared_name=:cy WHERE  public."User".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':ctt', $use_name);
            $stmt->bindParam(':cy', $share_n);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();
            return true;
        }

        if ($this->deletedAt == 2) {

            $sqlQuery = 'UPDATE public."Client"  SET client_type=:ct WHERE  public."Client"."userId"=:id';
            $ct = 'group';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':ct', $ct);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();

            $sqlQuery = 'SELECT * FROM public."User" WHERE  public."User".id=:id';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();
            $row = $stmt->fetch();

            $use_name =
                @$row['firstName'] . @$row['lastName'] . @$row['shared_name'];;
            $share_n = '';

            $sqlQuery = 'UPDATE public."User"  SET "firstName"=:ctt, "lastName"=:ctt, shared_name=:cy WHERE  public."User".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':ctt', $share_n);
            $stmt->bindParam(':cy', $use_name);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();
            return true;
        }

        if ($this->deletedAt == 3) {
            $sqlQuery = 'UPDATE public."Client"  SET client_type=:ct WHERE  public."Client"."userId"=:id';
            $ct = 'institution';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':ct', $ct);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();

            $sqlQuery = 'SELECT * FROM public."User" WHERE  public."User".id=:id';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();
            $row = $stmt->fetch();

            $use_name =
                @$row['firstName'] . @$row['lastName'] . @$row['shared_name'];;
            $share_n = '';

            $sqlQuery = 'UPDATE public."User"  SET "firstName"=:ctt, "lastName"=:ctt, shared_name=:cy WHERE  public."User".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':ctt', $share_n);
            $stmt->bindParam(':cy', $use_name);
            $stmt->bindParam(':id', $this->createdAt);
            $stmt->execute();
            return true;
        }





        return false;
    }

    public function undoLoanDisbursement()
    {
        $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."Client" ON public."loan".account_id=public."Client"."userId" WHERE  public."loan".loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $loan = $stmt->fetch();

            // check the mode of disiburment
            if ($loan['mode_of_disbursement'] == 'cheque' || $loan['mode_of_disbursement'] == 'cash') {
                // cheque process


                $sqlQueryn = 'SELECT * FROM public."transactions" WHERE  public."transactions".loan_id=:id AND t_type=:tt';
                $tt = 'A';
                $stmtn = $this->conn->prepare($sqlQueryn);
                $stmtn->bindParam(':id', $this->createdAt);
                $stmtn->bindParam(':tt', $tt);
                $stmtn->execute();
                if ($stmtn->rowCount() > 0) {
                    $row = $stmtn->fetch();

                    $acc_acid = 0;
                    // get bank account details or cash account details accordingly
                    if ($loan['mode_of_disbursement'] == 'cheque') {
                        $sqlQueryx = 'SELECT * FROM public."Account" WHERE  public."Account".id::text=:id';

                        $stmtx = $this->conn->prepare($sqlQueryx);
                        $stmtx->bindParam(':id', $row['bacid']);
                        $stmtx->execute();
                        $acc_d = $stmtx->fetch();

                        $acc_acid = $acc_d['id'];
                    }

                    if ($loan['mode_of_disbursement'] == 'cash') {
                        $sqlQueryx = 'SELECT * FROM public."Account" WHERE  public."Account".id::text=:id';

                        $stmtx = $this->conn->prepare($sqlQueryx);
                        $stmtx->bindParam(':id', $row['cash_acc']);
                        $stmtx->execute();
                        $acc_d = $stmtx->fetch();

                        $acc_acid = $acc_d['id'];
                    }


                    // check for any loan repayments
                    $sqlQueryx = 'SELECT COUNT(*) as tot FROM public."transactions" WHERE  public."transactions".loan_id=:id AND t_type=:tt';
                    $tt = 'L';
                    $stmtx = $this->conn->prepare($sqlQueryx);
                    $stmtx->bindParam(':id', $this->createdAt);
                    $stmtx->bindParam(':tt', $tt);
                    $stmtx->execute();
                    $rowx = $stmtx->fetch();
                    if ($rowx['tot'] > 0) {
                        return ' You can\'t undo-disbursement of a loan that has already started payments';
                    } else {
                        $sqlQuery = 'UPDATE  public."loan" SET status=0 WHERE  public."loan".loan_no=:id';

                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':id', $this->createdAt);
                        $stmt->execute();

                        $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal WHERE  public."Account".id=:id';

                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':id', $acc_acid);
                        $stmt->bindParam(':bal', $row['amount']);
                        $stmt->execute();

                        $sqlQuery = 'DELETE FROM   public."transactions" WHERE  public."transactions".loan_id=:id';

                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':id', $this->createdAt);
                        $stmt->execute();

                        return 'Undo-disbursement of the loan no:- ' . $this->createdAt . '  has been completed Successfully! Loan has been set back to loan application(pending review) status!';
                    }
                } else {
                    return 'Loan Disbursement Transaction not found! Kindly analyse clearly this loan first.';
                }
            }
            if ($loan['mode_of_disbursement'] == 'saving') {
                // saving process


                $sqlQueryn = 'SELECT amount FROM public."transactions" WHERE  public."transactions".loan_id=:id AND t_type=:tt';
                $tt = 'A';
                $stmtn = $this->conn->prepare($sqlQueryn);
                $stmtn->bindParam(':id', $this->createdAt);
                $stmtn->bindParam(':tt', $tt);
                $stmtn->execute();
                if ($stmtn->rowCount() > 0) {
                    $row = $stmtn->fetch();

                    if ($loan['acc_balance'] >= $row['amount']) {
                        // check for any loan repayments
                        $sqlQueryx = 'SELECT COUNT(*) as tot FROM public."transactions" WHERE  public."transactions".loan_id=:id AND t_type=:tt';
                        $tt = 'L';
                        $stmtx = $this->conn->prepare($sqlQueryx);
                        $stmtx->bindParam(':id', $this->createdAt);
                        $stmtx->bindParam(':tt', $tt);
                        $stmtx->execute();
                        $rowx = $stmtx->fetch();
                        if ($rowx['tot'] > 0) {
                            return ' You can\'t undo-disbursement of a loan that has already started payments';
                        } else {
                            $sqlQuery = 'UPDATE  public."loan" SET status=0 WHERE  public."loan".loan_no=:id';

                            $stmt = $this->conn->prepare($sqlQuery);
                            $stmt->bindParam(':id', $this->createdAt);
                            $stmt->execute();

                            $sqlQuery = 'UPDATE  public."Client" SET acc_balance=acc_balance-:bal WHERE  public."Client"."userId"=:id';

                            $stmt = $this->conn->prepare($sqlQuery);
                            $stmt->bindParam(':id', $loan['userId']);
                            $stmt->bindParam(':bal', $row['amount']);
                            $stmt->execute();

                            $sqlQuery = 'DELETE FROM   public."transactions" WHERE  public."transactions".loan_id=:id';

                            $stmt = $this->conn->prepare($sqlQuery);
                            $stmt->bindParam(':id', $this->createdAt);
                            $stmt->execute();

                            return 'Undo-disbursement of the loan no:- ' . $this->createdAt . '  has been completed Successfully! Loan has been set back to loan application(pending review) status!';
                        }
                    } else {
                        return ' Customer has Insufficient Balance, for this action!';
                    }
                } else {
                    return 'Loan Disbursement Transaction not found! Kindly analyse clearly this loan first.';
                }
            }
        } else {
            return 'Loan not found!';
        }

        return 'Something went wrong! Cross check the loan details and the client statement before re-doing the action.';
    }

    public function unfreezeAccount()
    {
        $sqlQuery = 'UPDATE  public."Client"  SET acc_balance=acc_balance+:bal, freezed_amount=freezed_amount-:bal WHERE "userId"=:id ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':bal', $this->amount);
        $stmt->execute();

        return true;
    }


    public function denyLoan()
    {

        $sqlQuery = 'UPDATE public."loan"  SET 
    denialreason=:aa,reviewedbyid=:rbi,status=6
 WHERE  public."loan".loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':aa', $this->updatedAt);
        $stmt->bindParam(':rbi', $this->serialNumber);

        $stmt->execute();
        return true;
    }

    // Helper to identify double precision columns
    private function getDoublePrecisionColumns()
    {
        return [
            'amount',
            'outstanding_amount',
            'outstanding_amount_total',
            'loan_interest',
            'outstanding_interest',
            'outstanding_interest_total',
            'left_balance',
            'charges',
            'loan_penalty'
        ];
    }

    // Helper to identify integer columns
    private function getIntegerColumns()
    {
        return [
            '_authorizedby',
            '_status',
            'mid',
            'loan_id',
            'approvedby',
            '_feeid',
            'schedule_id',
            'send_sms_parent',
            'send_sms_school',
            'is_transfer',
            'is_reversal',
            'reversal_tid',
            'agent_loan_amount',
            'loan_payment_status',
            'trashed_by'
        ];
    }

    // Helper to identify boolean columns
    private function getBooleanColumns()
    {
        return ['send_sms'];
    }





    public function deleteLoanRepayment($trashReason, $trashedBy, $trashedDate)
    {

        // Fetch the transaction to be deleted
        $sqlQuery = 'SELECT * FROM public."transactions" WHERE tid = :id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt, PDO::PARAM_INT); // tid is an integer
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) {
            // Prepare columns and placeholders
            $columns = array_keys($row);
            $columnsList = implode(', ', array_map(fn($col) => '"' . $col . '"', $columns)) . ', "trash_reason", "trashed_by", "trash_date"';
            $placeholders = implode(', ', array_map(fn($col) => ':' . $col, $columns)) . ', :trash_reason, :trashed_by, :trash_date';

            // Prepare the INSERT query
            $sqlQuery = 'INSERT INTO public."trash_transactions" (' . $columnsList . ') VALUES (' . $placeholders . ')';
            $stmt = $this->conn->prepare($sqlQuery);

            // Bind transaction values dynamically with type checks
            foreach ($row as $key => $value) {
                if (is_null($value)) {
                    $stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
                } elseif (in_array($key, $this->getDoublePrecisionColumns())) {
                    $stmt->bindValue(':' . $key, (float)$value, PDO::PARAM_STR);
                } elseif (in_array($key, $this->getIntegerColumns())) {
                    $stmt->bindValue(':' . $key, (int)$value, PDO::PARAM_INT);
                } elseif (in_array($key, $this->getBooleanColumns())) {
                    $stmt->bindValue(':' . $key, $value ? true : false, PDO::PARAM_BOOL);
                } else {
                    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }

            // Bind additional trash fields
            $stmt->bindValue(':trash_reason', $trashReason, PDO::PARAM_STR);
            $stmt->bindValue(':trashed_by', $trashedBy, PDO::PARAM_INT);
            $stmt->bindValue(':trash_date', $trashedDate, PDO::PARAM_STR);

            // Execute the INSERT query
            $stmt->execute();


            $princ_paid = $row['amount'];
            $int_paid = $row['loan_interest'];

            $sqlQuery = 'SELECT * FROM public."loan_schedule" WHERE loan_id=:id AND principal_paid>0 OR  interest_paid>0 ORDER BY schedule_id ASC';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $row['loan_id']);
            $stmt->execute();

            foreach ($stmt as $rown) {
                if ($princ_paid > 0 || $int_paid > 0) {
                    $principal_paid = 0;
                    $interest_paid = 0;
                    $schedule_status = 'active';
                    if ($princ_paid >= $rown['principal_paid']) {
                        $principal_paid = $rown['principal_paid'];
                        $princ_paid = $princ_paid - $principal_paid;
                    } else {
                        $principal_paid = $princ_paid;
                        $princ_paid = $princ_paid - $principal_paid;
                    }

                    if ($int_paid >= $rown['interest_paid']) {
                        $interest_paid = $rown['interest_paid'];
                        $int_paid = $int_paid - $interest_paid;
                    } else {
                        $interest_paid = $int_paid;
                        $int_paid = $int_paid - $interest_paid;
                    }



                    $sqlQueryn = 'UPDATE loan_schedule SET principal_paid=principal_paid-:principal_paid,interest_paid=interest_paid-:interest_paid, outstanding_principal=outstanding_principal+:principal_paid, outstanding_interest=outstanding_interest+:interest_paid,status=:status WHERE public."loan_schedule".schedule_id=:schedule_id ';
                    $stmtn = $this->conn->prepare($sqlQueryn);
                    $stmtn->bindParam(':schedule_id', $rown['schedule_id']);
                    $stmtn->bindParam(':principal_paid', $principal_paid);
                    $stmtn->bindParam(':interest_paid', $interest_paid);
                    $stmtn->bindParam(':status', $schedule_status);

                    $stmtn->execute();
                }
            }


            // delete the trxn entry
            $sqlQuery = 'DELETE  FROM public."transactions" WHERE tid=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['tid']);

            $stmt->execute();


            // update status back to active state if it was closed before
            $sqlQuery = 'UPDATE  public."loan" SET status=2 WHERE loan_no=:id AND status=5';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['loan_id']);

            $stmt->execute();

            // call to update loan amount method here
            $this->updateTotalLoanAmount($row['loan_id'], false);


            return true;
        }
        return false;
    }

    public function deleteShortfall()
    {

        $sqlQuery = 'SELECT * FROM public."staff_shortfalls" WHERE ss_id=:id AND status=1';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row  = $stmt->fetch();

            //  clear amount from cashier cash account
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $row['affected_cash_acid']);
            $stmt->bindParam(':ac', $row['amount']);
            $stmt->execute();

            // add shortfall to shortfall journal account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $row['affected_acid']);
            $stmt->bindParam(':ac', $row['amount']);
            $stmt->execute();

            // delete the trxn entry
            $sqlQuery = 'DELETE  FROM public."staff_shortfalls" WHERE ss_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['ss_id']);

            $stmt->execute();

            return true;
        }
        return false;
    }

    public function deleteExcess()
    {

        $sqlQuery = 'SELECT * FROM public."staff_excess" WHERE ss_id=:id AND status=1';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row  = $stmt->fetch();

            //  clear amount from cashier cash account
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $row['affected_cash_acid']);
            $stmt->bindParam(':ac', $row['amount']);
            $stmt->execute();

            // clear excess to excess journal account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $row['affected_acid']);
            $stmt->bindParam(':ac', $row['amount']);
            $stmt->execute();

            // delete the trxn entry
            $sqlQuery = 'DELETE  FROM public."staff_excess" WHERE ss_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['ss_id']);

            $stmt->execute();

            return true;
        }
        return false;
    }

    public function deleteLoanProduct()
    {

        $sqlQuery = 'UPDATE public."loantypes"  SET status=3 WHERE  type_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        return true;
    }


    public function deleteLoanCollateral()
    {

        $sqlQuery = 'UPDATE public."collaterals"  SET deleted=1 WHERE  _cid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        return true;
    }

    public function deleteLoanGuarantor()
    {

        $sqlQuery = 'UPDATE public."guarantors"  SET deleted=1 WHERE  gid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        return true;
    }

    public function deleteLoan()
    {

        $sqlQuery = 'DELETE FROM  public."loan" WHERE  loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();

        $sqlQuery = 'DELETE FROM  public."loan_schedule" WHERE  loan_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();

        $sqlQuery = 'DELETE FROM  public."transactions" WHERE  loan_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        return true;
    }

    public function sendToCleared()
    {

        $sqlQuery = 'UPDATE  public."loan" SET status=5, principal_arrears=0, interest_arrears=0, principal_due=0, interest_due=0 WHERE  loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();


        return true;
    }
    public function rectifyPrincipal()
    {
        $sqlQuery = 'SELECT * FROM  public."loan" WHERE  loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        $row = $stmt->fetch();

        // $sqlQuery = 'UPDATE  public."loan" SET current_balance=0, interest_balance=0, principal_balance=0, penalty_balance=0, status=5 WHERE  loan_no=:id';

        // $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':id', $this->createdAt);

        // $stmt->execute();

        $sqlQuery = 'SELECT SUM(outstanding_principal) AS op, SUM(outstanding_interest) AS oi FROM  public."loan_schedule" WHERE  loan_id=:id AND status=\'active\'';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        $rown = $stmt->fetch();

        $loan_is_fully_paid = true;


        /**
         * Create Loan transaction
         */
        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby, acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,date_created,outstanding_amount_total,loan_interest,outstanding_interest_total,pay_method) VALUES (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:dc,:oat,:li,:oit,:pay_m)';

        $stmt = $this->conn->prepare($sqlQuery);
        // $ooaa = 0;
        $ooii = 0;
        $namee = '';
        $pp = 'saving';
        $tt = 'L';
        $name = 'Repayment Rectification';
        $stmt->bindParam(':amount', $rown['op']);
        $stmt->bindParam(':descri', $name);
        $stmt->bindParam(':autho', $row['loan_officer']);
        $stmt->bindParam(':actby', $row['loan_officer']);
        $stmt->bindParam(':accname', $namee);
        $stmt->bindParam(':mid', $row['account_id']);
        $stmt->bindParam(':approv', $row['loan_officer']);
        $stmt->bindParam(':branc', $row['branchid']);
        $stmt->bindParam(':leftbal', $ooii);
        $stmt->bindParam(':lid', $this->createdAt);
        $stmt->bindParam(':ttype', $this->left_balance);
        $stmt->bindParam(':dc', $this->collection_date);
        $stmt->bindParam(':oat', $tt);
        $stmt->bindParam(':li', $rown['oi']);
        $stmt->bindParam(':oit', $ooii);

        $stmt->bindParam(':pay_m', $pp);

        //TODO uncomment this
        $stmt->execute();
        $this->updateTotalLoanAmount($this->createdAt, $loan_is_fully_paid);
    }

    public function rectifyLoan()
    {

        $sqlQuery = 'SELECT * FROM  public."loan" WHERE  loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        $row = $stmt->fetch();

        $sqlQuery = 'UPDATE  public."loan" SET current_balance=current_balance+:inte, interest_balance=:inte WHERE  loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':inte', $row['interest_amount']);

        $stmt->execute();

        $sqlQuery = 'UPDATE  public."loan_schedule" SET status=\'active\', principal_paid=0, interest_paid=0 WHERE  loan_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();

        $sqlQuery = 'DELETE FROM   public."transactions" WHERE  loan_id=:id AND t_type=\'L\'';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        return true;
    }


    public function deleteIncome($trashReason, $trashedBy, $trashedDate)
    {

        // Fetch the transaction to be deleted
        $sqlQuery = 'SELECT * FROM public."transactions" WHERE tid = :id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt, PDO::PARAM_INT); // tid is an integer
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $trxn = $stmt->fetch();

        if ($trxn) {
            // Prepare columns and placeholders
            $columns = array_keys($trxn);
            $columnsList = implode(', ', array_map(fn($col) => '"' . $col . '"', $columns)) . ', "trash_reason", "trashed_by", "trash_date"';
            $placeholders = implode(', ', array_map(fn($col) => ':' . $col, $columns)) . ', :trash_reason, :trashed_by, :trash_date';

            // Prepare the INSERT query
            $sqlQuery = 'INSERT INTO public."trash_transactions" (' . $columnsList . ') VALUES (' . $placeholders . ')';
            $stmt = $this->conn->prepare($sqlQuery);

            // Bind transaction values dynamically with type checks
            foreach ($trxn as $key => $value) {
                if (is_null($value)) {
                    $stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
                } elseif (in_array($key, $this->getDoublePrecisionColumns())) {
                    $stmt->bindValue(':' . $key, (float)$value, PDO::PARAM_STR);
                } elseif (in_array($key, $this->getIntegerColumns())) {
                    $stmt->bindValue(':' . $key, (int)$value, PDO::PARAM_INT);
                } elseif (in_array($key, $this->getBooleanColumns())) {
                    $stmt->bindValue(':' . $key, $value ? true : false, PDO::PARAM_BOOL);
                } else {
                    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }

            // Bind additional trash fields
            $stmt->bindValue(':trash_reason', $trashReason, PDO::PARAM_STR);
            $stmt->bindValue(':trashed_by', $trashedBy, PDO::PARAM_INT);
            $stmt->bindValue(':trash_date', $trashedDate, PDO::PARAM_STR);

            // Execute the INSERT query
            $stmt->execute();


            if ($trxn['_status'] == 1) {
                if ($trxn['pay_method'] == 'saving' && $trxn['t_type'] == 'E') {
                    // Adjust the client's account balance
                    $sqlQuery = 'UPDATE public."Client" SET acc_balance = acc_balance - :amount WHERE "userId" = :id';
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $trxn['mid'], PDO::PARAM_INT);
                    $stmt->bindParam(':amount', $trxn['amount'], PDO::PARAM_STR);
                    $stmt->execute();
                }
                if ($trxn['pay_method'] == 'saving' && $trxn['t_type'] == 'I') {
                    // Adjust the client's account balance
                    $sqlQuery = 'UPDATE public."Client" SET acc_balance = acc_balance + :amount WHERE "userId" = :id';
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $trxn['mid'], PDO::PARAM_INT);
                    $stmt->bindParam(':amount', $trxn['amount'], PDO::PARAM_STR);
                    $stmt->execute();
                }
                if ($trxn['pay_method'] == 'cash' && !empty($trxn['cash_acc']) && is_valid_uuid($trxn['cash_acc'])) {
                    if ($trxn['t_type'] == 'I') {
                        $sqlQuery = 'UPDATE public."Account" SET balance = balance - :amount WHERE id = :id';
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':id', $trxn['cash_acc'], PDO::PARAM_STR);
                        $stmt->bindParam(':amount', $trxn['amount'], PDO::PARAM_STR);
                        $stmt->execute();
                    }
                    if ($trxn['t_type'] == 'E') {
                        $sqlQuery = 'UPDATE public."Account" SET balance = balance + :amount WHERE id = :id';
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':id', $trxn['cash_acc'], PDO::PARAM_STR);
                        $stmt->bindParam(':amount', $trxn['amount'], PDO::PARAM_STR);
                        $stmt->execute();
                    }
                }
            }


            $sqlQuery = 'DELETE FROM  public."transactions"  WHERE  tid=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(
                ':id',
                $this->createdAt
            );

            $stmt->execute();
        }

        return true;
    }

    public function deleteGroupMember()
    {

        $sqlQuery = 'DELETE FROM  public."group_members"  WHERE  gmid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);

        $stmt->execute();
        return true;
    }

    public function createExpense()
    {
        // sanitize amount to remove commas

        $this->location = str_replace(",", "", $this->location);

        $treas = $this->name . ' ' . @$this->cheque_no;
        $ttypee = 'E';
        // create fee charge transaction entry and mark it with the fee id

        $mid = 0;
        $sender = null;
        if ($this->pay_method == 'cash') {
            $sender = $this->createdAt;
        } else if ($this->pay_method == 'cheque') {
            $sender = $this->id;
        }

        if ($this->pay_method == 'saving') {
            $mid = $this->gracetype;
        }
        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,cheque_no,cash_acc,bacid,date_created,mid,cr_acid,dr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:cheque,:cash_acc,:bacid,:datee,:mid,:cr,:dr)';


        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $this->location);
        $stmt->bindParam(':cr', $this->contact_person_details);
        $stmt->bindParam(':dr', $sender);
        $stmt->bindParam(':descri', $treas);
        $stmt->bindParam(':autho', $this->cash_acc);
        $stmt->bindParam(':actby', $this->cash_acc);
        $stmt->bindParam(':accname', $treas);
        $stmt->bindParam(':approv', $this->cash_acc);
        $stmt->bindParam(':branc', $this->send_sms);
        $stmt->bindParam(':ttype', $ttypee);
        $stmt->bindParam(':acid', $this->contact_person_details);
        $stmt->bindParam(':pay_method', $this->pay_method);
        $stmt->bindParam(':cheque', $this->penaltybased);
        $stmt->bindParam(':cash_acc', $this->createdAt);
        $stmt->bindParam(':bacid', $this->id);
        $stmt->bindParam(':datee', $this->recommender);

        $stmt->bindParam(':mid', $mid);

        $stmt->execute();

        // update account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->contact_person_details);
        $stmt->bindParam(':ac', $this->location);
        $stmt->execute();



        if ($this->pay_method == 'saving') {
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->gracetype);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();

            return true;
        } else if ($this->pay_method == 'cash') {
            // update account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->createdAt);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();
        } else if ($this->pay_method == 'cheque') {
            // update account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();
        }

        // insert into audit trail

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Registered Expense:- ' . $treas;
        $auditTrail->staff_id = $this->cash_acc;
        // $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->send_sms;

        $auditTrail->log_message = 'Expense Registered: using' . $this->recommender;
        $auditTrail->create();
        return true;
    }

    public function createCapital()
    {
        // sanitize amount to remove commas

        $this->location = str_replace(",", "", $this->location);

        $treas = $this->name;
        $ttypee = 'CAP';
        // create fee charge transaction entry and mark it with the fee id

        $mid = 0;
        $sender = null;
        if ($this->pay_method == 'cash') {
            $sender = $this->createdAt;
        } else if (
            $this->pay_method == 'cheque'
        ) {
            $sender = $this->bank_acc;
        }

        if ($this->pay_method == 'saving') {
            $mid = $this->gracetype;
        }

        if ($this->pay_method == 'saving') {
            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,pay_method,cheque_no,cash_acc,bacid,date_created,mid,cr_acid,dr_acid,cr_dr) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:pay_method,:cheque,:cash_acc,:bacid,:datee,:mid,:cr,:dr,:cr_dr)';


            $stmt = $this->conn->prepare($sqlQuery);

            $cr_dr = 'debit';
            $stmt->bindParam(':amount', $this->location);
            $stmt->bindParam(':dr', $this->contact_person_details);
            $stmt->bindParam(':cr', $sender);
            $stmt->bindParam(':descri', $treas);
            $stmt->bindParam(':autho', $this->cash_acc);
            $stmt->bindParam(':actby', $this->cash_acc);
            $stmt->bindParam(':accname', $treas);
            $stmt->bindParam(':approv', $this->cash_acc);
            $stmt->bindParam(':branc', $this->send_sms);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':cr_dr', $cr_dr);
            $stmt->bindParam(':pay_method', $this->recommender);
            $stmt->bindParam(':cheque', $this->penaltybased);
            $stmt->bindParam(':cash_acc', $this->createdAt);
            $stmt->bindParam(':bacid', $this->bank_acc);
            $stmt->bindParam(':datee', $this->pay_method);

            $stmt->bindParam(':mid', $mid);

            $stmt->execute();
        } else {
            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,cheque_no,cash_acc,bacid,date_created,mid,cr_acid,dr_acid,cr_dr) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:cheque,:cash_acc,:bacid,:datee,:mid,:cr,:dr,:cr_dr)';


            $stmt = $this->conn->prepare($sqlQuery);
            $cr_dr = 'credit';

            $stmt->bindParam(':amount', $this->location);
            $stmt->bindParam(':cr', $this->contact_person_details);
            $stmt->bindParam(':cr_dr', $cr_dr);
            $stmt->bindParam(':dr', $sender);
            $stmt->bindParam(':descri', $treas);
            $stmt->bindParam(':autho', $this->cash_acc);
            $stmt->bindParam(':actby', $this->cash_acc);
            $stmt->bindParam(':accname', $treas);
            $stmt->bindParam(':approv', $this->cash_acc);
            $stmt->bindParam(':branc', $this->send_sms);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':acid', $this->contact_person_details);
            $stmt->bindParam(':pay_method', $this->recommender);
            $stmt->bindParam(':cheque', $this->penaltybased);
            $stmt->bindParam(':cash_acc', $this->createdAt);
            $stmt->bindParam(':bacid', $this->bank_acc);
            $stmt->bindParam(':datee', $this->pay_method);

            $stmt->bindParam(':mid', $mid);

            $stmt->execute();
        }


        if ($this->pay_method == 'saving') {

            // update account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->contact_person_details);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();
        } else {
            // update account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->contact_person_details);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();
        }
        // insert into audit trail

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Registered Capital:- ' . $treas;
        $auditTrail->staff_id = $this->cash_acc;
        // $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->send_sms;

        $auditTrail->log_message = 'Capital Registered: using' . $this->recommender;
        $auditTrail->create();

        if ($this->pay_method == 'saving') {
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->gracetype);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();

            return true;
        } else if ($this->pay_method == 'cash') {
            // update account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->createdAt);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();
        } else if ($this->pay_method == 'cheque') {
            // update account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->bank_acc);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();
        }
        return true;
    }

    public function purchaseSMS()
    {

        $st = 1;
        $comm = 'Entry made by UCSCU Staff';
        $sqlQuery = 'INSERT INTO public.sms_topup_transactions(
             amount, bankid, status, branchid, comments, pay_method)
            VALUES (:amount,:bid,:st,:branch,:comm,:pmethod)';


        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $this->location);
        $stmt->bindParam(':bid', $this->name);
        $stmt->bindParam(':st', $st);
        $stmt->bindParam(':branch', $this->recommender);
        $stmt->bindParam(':comm', $comm);
        $stmt->bindParam(':pmethod', $this->contact_person_details);
        $stmt->execute();


        $sqlQuery = 'UPDATE public."Branch" SET  sms_amount_loaded=sms_amount_loaded+:ac, sms_balance=sms_balance+:ac
            WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->recommender);
        $stmt->bindParam(':ac', $this->location);
        $stmt->execute();


        return true;
    }


    public function requestpurchaseSMS()
    {

        $st = 0;
        $comm = 'Entry made by Client Via Requisition form';
        $pay_method = 'cash';

        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->recommender);
        $stmt->execute();
        $row = $stmt->fetch();

        $bid = $row['bankId'];


        $sqlQuery = 'INSERT INTO public.sms_topup_transactions(
             amount, bankid, status, branchid, comments, pay_method)
            VALUES (:amount,:bid,:st,:branch,:comm,:pmethod)';


        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $this->location);
        $stmt->bindParam(':bid', $bid);
        $stmt->bindParam(':st', $st);
        $stmt->bindParam(':branch', $this->recommender);
        $stmt->bindParam(':comm', $comm);
        $stmt->bindParam(':pmethod', $pay_method);
        $stmt->execute();



        return true;
    }

    public function createIncome()
    {
        // sanitize amount to remove commas

        $this->location = str_replace(",", "", $this->location);
        $treas = $this->name;
        $ttypee = 'I';
        // create fee charge transaction entry and mark it with the fee id

        $mid = 0;
        $sender = null;
        if ($this->recommender == 'cash') {
            $sender = $this->createdAt;
        } else if (
            $this->recommender == 'cheque'
        ) {
            $sender = $this->id;
        }

        if ($this->recommender == 'saving') {
            $mid = $this->gracetype;
        }
        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,cheque_no,cash_acc,bacid,date_created,mid,cr_acid,dr_acid) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:cheque,:cash_acc,:bacid,:datee,:mid,:cr,:dr)';


        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $this->location);
        $stmt->bindParam(':cr', $this->contact_person_details);
        $stmt->bindParam(':dr', $sender);
        $stmt->bindParam(':descri', $treas);
        $stmt->bindParam(':autho', $this->cash_acc);
        $stmt->bindParam(':actby', $this->cash_acc);
        $stmt->bindParam(':accname', $treas);
        $stmt->bindParam(':approv', $this->cash_acc);
        $stmt->bindParam(':branc', $this->send_sms);
        $stmt->bindParam(':ttype', $ttypee);
        $stmt->bindParam(':acid', $this->contact_person_details);
        $stmt->bindParam(':pay_method', $this->recommender);
        $stmt->bindParam(':cheque', $this->penaltybased);
        $stmt->bindParam(':cash_acc', $this->createdAt);
        $stmt->bindParam(':bacid', $this->id);
        $stmt->bindParam(':datee', $this->pay_method);

        $stmt->bindParam(':mid', $mid);



        $stmt->execute();

        // update account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->contact_person_details);
        $stmt->bindParam(':ac', $this->location);
        $stmt->execute();

        // insert into audit trail

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Registered Income:- ' . $treas;
        $auditTrail->staff_id = $this->cash_acc;
        // $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->send_sms;

        $auditTrail->log_message = 'Income Registered: using' . $this->recommender;
        $auditTrail->create();


        if ($this->recommender == 'saving') {
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->gracetype);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();

            return true;
        } else if ($this->pay_method == 'cash') {
            // update account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->createdAt);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();
        } else if ($this->pay_method == 'cheque') {
            // update account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':ac', $this->location);
            $stmt->execute();
        }
        return true;
    }

    public function disburseLoan()
    {
        //    check if it's freeze and freeze the set amount off a/c balance
        if ($this->updatedAt > 0) {
            $sqlQuery = 'UPDATE public."Client"  SET 
    acc_balance=acc_balance-:aa,freezed_amount=freezed_amount+:aa, freeze_cat=:fr 
 WHERE  public."Client"."userId"=:id';

            $fr = 'loan';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->serialNumber);
            $stmt->bindParam(':aa', $this->updatedAt);
            $stmt->bindParam(':fr', $fr);
            $stmt->execute();
        }

        // update table loan , loan_schedule details
        $sqlQuery = 'UPDATE public."loan"  SET 
    date_disbursed=:aa,status=2,date_of_first_pay=:notes,mode_of_disbursement=:mod,auto_pay=:autopay, requesteddisbursementdate=:aa
 WHERE  public."loan".loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':aa', $this->deletedAt);
        $stmt->bindParam(':notes', $this->name);
        $stmt->bindParam(':mod', $this->location);
        $stmt->bindParam(':autopay', $this->auto_repay);
        $stmt->execute();

        $this->applyLoanSchedule($this->createdAt);

        $sqlQuery = 'UPDATE public."loan"  SET date_of_first_pay=:date_of_first_pay, date_of_next_pay=:date_of_next_pay WHERE  public."loan".loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->bindParam(':date_of_first_pay', $this->start_of_payment_date);
        $stmt->bindParam(':date_of_next_pay', $this->start_of_payment_date);
        $stmt->execute();

        $this->updateTotalLoanAmount($this->createdAt);




        // insert into transaction table disbursement transaction
        $sqlQuery = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->serialNumber);
        $stmt->execute();
        $row = $stmt->fetch();


        // update chart account principal a/c
        // $sqlQuery = 'SELECT * FROM  public."loantypes" WHERE type_id=:id';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $this->contact_person_details);
        // $stmt->execute();
        // $lt = $stmt->fetch();

        $sqlQuery = 'SELECT * FROM  public."Account" WHERE lpid=:id AND "branchId"=:bid';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->contact_person_details);
        $stmt->bindParam(':bid', $row['branchId']);
        $stmt->execute();
        $accid = $stmt->fetch();

        // $sqlQuery = 'SELECT * FROM  public."Account" WHERE account_code_used=:id AND "branchId"=:bid';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $acc_t['account_code_used']);
        // $stmt->bindParam(':bid', $row['branchId']);
        // $stmt->execute();
        // $accid = $stmt->fetch();

        $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal WHERE id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $accid['id']);
        $stmt->bindParam(':bal', $this->amount);
        $stmt->execute();

        $trxn_mid = 0;
        if ($this->location == 'saving') {
            $trxn_mid =
                $this->serialNumber;
        }

        $this->left_balance = $row['acc_balance'] + $this->amount;
        $acc_name = $row['firstName'] . ' ' . $row['lastName'];
        $t_type = 'A';
        $descr = 'LOAN DISBURSEMENT - LN ' . $this->createdAt;
        $actb = 'LOANS DEPARTMENT';

        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,acid, cash_acc,bacid,pay_method, date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:acid,:cash_acc,:bacid,:pay_method,:date_created)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':date_created', $this->deletedAt);
        $stmt->bindParam(':cash_acc', $this->cash_acc);
        $stmt->bindParam(':bacid', $this->bank_acc);
        $stmt->bindParam(':pay_method', $this->location);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':descri', $descr);
        $stmt->bindParam(':descri', $descr);
        $stmt->bindParam(':acid', $accid['id']);
        $stmt->bindParam(':autho', $this->identificationNumber);
        $stmt->bindParam(':actby', $actb);
        $stmt->bindParam(':accname', $acc_name);
        $stmt->bindParam(':mid', $trxn_mid);
        $stmt->bindParam(':approv', $this->identificationNumber);
        $stmt->bindParam(':branc', $row['branchId']);
        $stmt->bindParam(':leftbal', $this->left_balance);
        $stmt->bindParam(':lid', $this->createdAt);
        $stmt->bindParam(':ttype', $t_type);

        $stmt->execute();

        $sqlQuery = 'SELECT fees_to_charge FROM public."loan" WHERE  public."loan".loan_no=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        $loan_text = $stmt->fetch();
        $string = $loan_text['fees_to_charge'] ?? '';
        // $string = preg_replace('/\.$/', '', $string); //Remove dot at end if exists
        // $array = explode(', ', $string);

        $chargeAmount = 0;
        $total_charge = 0;
        // check for disbursement fees and deduct them and create transaction for the fees collected
        $sqlQuery = 'SELECT * FROM  public."loanproducttofee" LEFT JOIN public."Fee" ON public."loanproducttofee".fee_id=public."Fee".id 
    WHERE public."loanproducttofee".lp_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->contact_person_details);
        $stmt->execute();
        // $num = $stmt->rowCount();
        // if ($num > 0) {
        // $rown = $stmt->fetch();
        foreach ($stmt as $rown) {
            if (str_contains($string, $rown['fee_id'])) {
                $feeamount = $rown['rateAmount'];
                $feetype = $rown['type'];
                $payType = $rown['paymentType'];
                if ($payType == 'DISBURSEMENT') {
                    if ($feetype == 'INTEREST_RATE') {

                        $chargeAmount = ($feeamount / 100) * $this->amount;
                        $this->left_balance = $this->left_balance - $chargeAmount;
                        $total_charge = $total_charge + $chargeAmount;
                    } else if ($feetype == 'FIXED_AMOUNT') {
                        $chargeAmount = $feeamount;
                        $this->left_balance = $this->left_balance - $chargeAmount;
                        $total_charge = $total_charge + $chargeAmount;
                    } else {
                        // shares
                        $shares_to_buy = 0;
                        // get share acid,
                        $current_share_value = $this->getClientShareValue($this->serialNumber);
                        $required_share = $this->getFeeShareValue($rown['fee_id'], $this->amount);
                        if ($current_share_value['no_shares'] < $required_share['required_shares']) {
                            $shares_to_buy = $required_share['required_shares'] - $current_share_value['no_shares'];
                        } else {
                            $shares_to_buy = $required_share['else_shares'];
                        }

                        $bank = $this->getAllBankDetails($rown['bankId'])->fetch(PDO::FETCH_ASSOC);


                        $shares_to_buy_amount = $shares_to_buy * $bank['share_value'];
                        $this->purchaseDefaultShares($this->serialNumber, $shares_to_buy_amount, $shares_to_buy, $row['branchId'], $bank['share_value'], $bank['share_acid']);
                    }
                    if (
                        $feetype == 'INTEREST_RATE' or $feetype == 'FIXED_AMOUNT'
                    ) {
                        $sqlQuery = 'SELECT * FROM  public."Account"  WHERE (feeid=:fid OR fee_id2=:fid OR fee_id3=:fid OR fee_id4=:fid OR fee_id5=:fid OR fee_id6=:fid) AND "branchId"=:branch ORDER BY "createdAt" DESC LIMIT 1';

                        $stmt = $this->conn->prepare($sqlQuery);

                        $stmt->bindParam(':fid', $rown['fee_id']);
                        $stmt->bindParam(':branch', $row['branchId']);

                        $stmt->execute();
                        $rox = $stmt->fetch();



                        $treas = 'ON DISBURSEMENT FEES - ' . $rown['name'];
                        // $this->left_balance = $this->left_balance - $chargeAmount;
                        $ttypee = 'I';
                        // create fee charge transaction entry and mark it with the fee id

                        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
                acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,_feeid,acid, cash_acc,bacid,pay_method, date_created) VALUES
                  (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:fid,:acid,:cash_acc,:bacid,:pay_method,:date_created)';

                        $stmt = $this->conn->prepare($sqlQuery);

                        $stmt->bindParam(':amount', $chargeAmount);
                        $stmt->bindParam(':date_created', $this->deletedAt);
                        $stmt->bindParam(':cash_acc', $this->cash_acc);
                        $stmt->bindParam(':bacid', $this->bank_acc);
                        $stmt->bindParam(':pay_method', $this->location);
                        $stmt->bindParam(':acid', $rox['id']);
                        $stmt->bindParam(':descri', $treas);
                        $stmt->bindParam(':autho', $this->identificationNumber);
                        $stmt->bindParam(':actby', $actb);
                        $stmt->bindParam(':accname', $acc_name);
                        $stmt->bindParam(':mid', $trxn_mid);
                        $stmt->bindParam(':approv', $this->identificationNumber);
                        $stmt->bindParam(':branc', $row['branchId']);
                        $stmt->bindParam(':leftbal', $this->left_balance);
                        $stmt->bindParam(':lid', $this->createdAt);
                        $stmt->bindParam(':ttype', $ttypee);
                        $stmt->bindParam(':fid', $rown['id']);

                        $stmt->execute();
                    }
                }
            }
        }
        // }

        // base on mode of disburse if its via a/c add money to customer acc
        if ($this->location == 'saving') {
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $takeAmount = $this->amount - $total_charge;
            $stmt->bindParam(':id', $this->serialNumber);
            $stmt->bindParam(':ac', $takeAmount);
            $stmt->execute();
            return true;
        }

        if ($this->location == 'cheque') {
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $takeAmount = $this->amount - $total_charge;
            $stmt->bindParam(':id', $this->bank_acc);
            $stmt->bindParam(':ac', $takeAmount);
            $stmt->execute();
            return true;
        }

        if ($this->location == 'cash') {
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $takeAmount = $this->amount - $total_charge;
            $stmt->bindParam(':id', $this->cash_acc);
            $stmt->bindParam(':ac', $takeAmount);
            $stmt->execute();
            return true;
        }

        $loan_instance = new Loan($this->conn);
        $loan_instance->ApplyLoanPenaltySettings($this->createdAt);


        return true;
    }

    public function purchaseDefaultShares($userid, $sa, $no_shares, $branch, $csv, $share_acid)
    {

        $sqlQuery = 'SELECT * FROM public."share_register" WHERE userid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $userid);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // update existing share holder details
            $sqlQuery = 'UPDATE public."share_register" SET share_amount=share_amount+:sa,no_shares=no_shares+:ns WHERE userid=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $userid);
            $stmt->bindParam(':sa',  $sa);
            $stmt->bindParam(':ns', $no_shares);
            $stmt->execute();
        } else {
            // create share holder
            $sqlQuery = 'INSERT INTO public.share_register(
	 userid, share_amount, no_shares, added_by, branch_id)
	VALUES (:uid,:sa,:ns,:adb,:bid)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':uid', $userid);
            $stmt->bindParam(':sa',  $sa);
            $stmt->bindParam(':adb', $this->identificationNumber);
            $stmt->bindParam(':bid', $branch);
            $stmt->bindParam(':ns', $no_shares);
            $stmt->execute();
        }



        $tt_type = 'W';
        $pay_method = 'saving';
        $descri = 'Share Purchase due to loan';
        $auth = 0;

        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount',  $sa);
        $stmt->bindParam(':descri', $descri);
        $stmt->bindParam(':autho', $auth);
        $stmt->bindParam(':actby', $userid);
        $stmt->bindParam(':accname', $userid);
        $stmt->bindParam(':mid', $userid);
        $stmt->bindParam(':approv', $auth);
        $stmt->bindParam(':branc', $branch);
        $stmt->bindParam(':ttype', $tt_type);
        $stmt->bindParam(':pay_method', $pay_method);
        $stmt->bindParam(':date_created', $this->deletedAt);


        $stmt->execute();



        // create share purchase trxn

        $sqlQuery = 'INSERT INTO public.share_purchases(
	user_id, decription, no_of_shares, current_share_value, amount, pay_method, notes, record_date, added_by, branch_id,pay_method_acid)
	VALUES (:uid,:descri,:ns,:csv,:sa,:paymeth,:notes,:trxndate,:adb,:bid,:acid)';

        $stmt = $this->conn->prepare($sqlQuery);
        $acid = $userid;
        $description = 'Share Purchase due to Loan';
        $stmt->bindParam(':uid', $userid);
        $stmt->bindParam(':sa',  $sa);
        $stmt->bindParam(':adb', $this->identificationNumber);
        $stmt->bindParam(':bid', $branch);
        $stmt->bindParam(':ns', $no_shares);
        $stmt->bindParam(':csv', $csv);
        $stmt->bindParam(':descri', $description);
        $stmt->bindParam(':notes', $description);
        $stmt->bindParam(':paymeth', $pay_method);
        $stmt->bindParam(':trxndate', $this->deletedAt);

        $stmt->bindParam(':acid', $acid);

        $stmt->execute();


        $sqlQuery = 'SELECT * FROM public."Account"  WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $share_acid);
        $stmt->execute();

        $rown = $stmt->fetch();

        $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal  WHERE public."Account".account_code_used=:id AND "branchId"=:bid';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $rown['account_code_used']);
        $stmt->bindParam(':bid', $branch);
        $stmt->bindParam(
            ':bal',
            $sa
        );
        $stmt->execute();
    }
    public function getClientShareValue($user_id = null)
    {
        $sqlQuery = 'SELECT no_shares, share_amount FROM share_register  WHERE userid=:branch_id';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':branch_id' => $user_id]);
        return $record->fetch(PDO::FETCH_ASSOC);
    }

    public function getFeeShareValue($fee_id, $amount)
    {
        $sqlQuery = 'SELECT * FROM loan_disbursement_shares  WHERE fee_id=:fid AND min_val<=:amount AND max_val>=:amount';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':fid' => $fee_id, ':amount' => $amount]);
        // $record->execute([':amount' => $amount]);
        // $record->execute([':amountn' => $amount]);
        return $record->fetch(PDO::FETCH_ASSOC);
    }

    public function createSavingInterestInitiation()
    {
        $sqlQuery = 'INSERT INTO public.saving_interest_initiations(
	 name, set_date, wht_rate, int_rate, branch, save_pdt, min_bal, send_sms, auth_by,wht_acid,int_acid,from_date)
	VALUES (:name, :set_date, :wht_rate, :int_rate, :branch, :save_pdt, :min_bal, :send_sms, :auth_by,:wht_acid,:int_acid,:from_date)';
        $record = $this->conn->prepare($sqlQuery);
        $name = $this->date_of_next_pay . '_' . $this->collection_date . '_init';
        $record->execute([':name' => $name, ':set_date' => $this->collection_date, ':wht_rate' => $this->interest, ':int_rate' => $this->serialNumber, ':branch' => $this->createdAt, ':save_pdt' => $this->date_of_next_pay, ':min_bal' => $this->amount, ':send_sms' => $this->identificationNumber, ':auth_by' => $this->loan_id, ':wht_acid' => $this->wht_acid, ':int_acid' => $this->int_acid, ':from_date' => $this->from_date]);


        return true;
    }

    public function updateTrxnAccName()
    {

        $now = date('Y-m-d');
        // set trxn account to the new name set
        $sqlQuery = 'UPDATE public."Account" SET name=:name WHERE id=:id';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':name' => $this->amount, ':id' => $this->deletedAt]);

        $sqlQuery = 'UPDATE public."staff_cash_accounts" SET acc_name=:name, old_name=acc_name,reason_name_change=:reas,last_changed=:ls WHERE id=:id';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':name' => $this->amount, ':id' => $this->loan_id, ':reas' => $this->location, ':ls' => $now]);

        return true;
    }

    public function updateTrxnAccBalance()
    {

        $sqlQuery = 'SELECT * FROM public."Account" WHERE id=:id';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':id' => $this->deletedAt]);
        $row = $record->fetch();


        // set trxn account to the new balance set
        $sqlQuery = 'UPDATE public."Account" SET balance=:bal WHERE id=:id';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':bal' => $this->amount, ':id' => $this->deletedAt]);

        // get the difference btn the old balance of trxn acc and the new balance set
        $difference = (@$this->createdAt - @$this->amount);

        // update balance of suspense account selected to add the difference
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:bal WHERE id=:id';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':bal' => $difference, ':id' => $this->updatedAt]);

        /* 
        todo sorted
           // TODO: consider adding the reconciliation trxn having amount equal to the difference between the orig_amount and the new_amount --- this will help to balance the till sheet as well
        */
        $cr_acid = '';
        $dr_acid = '';

        if ($difference > 0) {
            $cr_acid = $this->deletedAt;
            $dr_acid = $this->updatedAt;
        } else {
            $dr_acid = $this->deletedAt;
            $cr_acid = $this->updatedAt;
        }

        // trxn record


        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method,cr_acid,dr_acid) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:cr_acid,:dr_acid)';

        $stmt = $this->conn->prepare($sqlQuery);
        $mid = 0;
        $tt_type = 'AJE';
        $pm = 'cash';
        $stmt->bindParam(':amount', $difference);
        $stmt->bindParam(':cr_acid', $cr_acid);
        $stmt->bindParam(':dr_acid', $dr_acid);
        $stmt->bindParam(':descri', $this->location);
        $stmt->bindParam(':autho', $this->name);
        $stmt->bindParam(':actby', $this->name);
        $stmt->bindParam(':accname', $this->location);
        $stmt->bindParam(':mid', $mid);
        $stmt->bindParam(':approv', $this->name);
        $stmt->bindParam(':branc', $row['branchId']);
        $stmt->bindParam(':ttype', $tt_type);
        $stmt->bindParam(':pay_method', $pm);

        // $stmt->bindParam(':bacid', $this->bacid);
        // $stmt->bindParam(':cheque', $this->cheque_no);
        // $stmt->bindParam(':cash_acc', $this->cash_acc);
        // $stmt->bindParam(':date_created', $this->date_created);
        // $stmt->bindParam(':charges', $charges);


        $stmt->execute();



        return true;
    }

    public function createGeneralFeesInitiation()
    {
        $sqlQuery = 'INSERT INTO public.general_fees_initiations(
	 fees_name, _branch, sid, fee_id, collect_date, auth_by, send_sms, affected_acid)
	VALUES (:name, :branch, :sid, :fid, :c_date, :auth_by,:sms,:affected_acid)';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':name' => $this->serialNumber, ':branch' => $this->createdAt, ':sid' => $this->date_of_next_pay, ':fid' => $this->amount, ':c_date' => $this->collection_date, ':auth_by' => $this->loan_id, ':sms' => $this->identificationNumber, ':affected_acid' => $this->affected_acid]);


        return true;
    }

    public function createManualLoanRepay()
    {
        // $loan_object = new Loan($this->conn);
        $interest = (int) $this->interest;
        $loan_id = $this->loan_id;
        $total_installment = @$this->amount;
        $amount = @$this->amount;
        $subtract_amount = @$this->amount;
        $penalty_amount = @$this->penalty_amount;

        $loan = $this->getLoanDetails($loan_id);
        // return $loan['date_of_next_pay'];
        if (!$loan) return "Loan not found";


        // $update = $this->updateTotalLoanAmount($loan_id);
        // return $update;

        $p_due = $loan['principal_due'] ?? 0;
        $i_due = $loan['interest_due'] ?? 0;

        $p_ar = $loan['principal_arrears'] ?? 0;
        $i_ar = $loan['interest_arrears'] ?? 0;

        if ($this->clear_penalty && $penalty_amount > $loan['penalty_balance']) return false;
        if ($this->clear_penalty && $penalty_amount > $amount) return false;

        $this->createdAt = date("Y-m-d h:i:s", strtotime($this->createdAt));
        $this->date_of_next_pay = date("Y-m-d h:i:s", strtotime($this->date_of_next_pay));
        $this->collection_date = date("Y-m-d h:i:s", strtotime($this->collection_date));

        // get clients details
        $member_id = $loan['account_id'];

        $sqlQueryn = 'SELECT * FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

        $stmtn = $this->conn->prepare($sqlQueryn);

        $stmtn->bindParam(':id', $member_id);
        $stmtn->execute();
        $member = $stmtn->fetch(PDO::FETCH_ASSOC);

        if (!$member) return "Member not found";
        // return $member;
        $current_schedule = $this->getLoanCurrentSchedule($loan_id);
        // return $current_schedule;
        if (!$current_schedule) $current_schedule = $this->getLoanNextSchedule($loan_id);
        // return true;
        if (!$current_schedule) return "Loan Schedule not found";

        $schedule_status_active = 'active';
        $schedule_status_paid = 'paid';

        // print_r($member);
        // $total_installment = @$this->amount + $interest;

        $account_balance = $member['membership_no'] > 0 ? $member['acc_balance'] : $member['loan_wallet'];
        if ($this->pay_method == 'saving') {
            $pay_m = 'saving';
            $bacid = '';
            $cashacc = '';
            $chequeno = '';
            if ($this->amount > $account_balance) return "Insufficent Account Balance";
        } else if ($this->pay_method == 'cash') {
            $pay_m = 'cash';
            $bacid = '';
            $cashacc = $this->cash_acc;
            $chequeno = '';
        } else if ($this->pay_method == 'cheque') {
            $pay_m = 'cheque';
            $bacid = $this->bank_acc;
            $cashacc = '';
            $chequeno = '';
            if ($this->cheque_no > 0) {
                $chequeno = $this->cheque_no;
            }
        }

        // return "OOps";

        $_authorizedby = $this->auth_id;
        $_actionby = "Loans Department";

        $acc_name = $member['firstName'] . " " . $member['lastName'];
        $branch = $loan['branchid'];

        $loan_is_fully_paid = false;

        // if payment is meant to close loan and waive the interest balance
        if (@$this->identificationNumber) {
            $principal_components = $this->getLoanPrincipal($loan_id);
            $interest_components = $this->getLoanInterest($loan_id);
            // waive the interest balance
            $balance_for_interest = max(($amount - ($principal_components['scheduled_principal'] - $principal_components['amount_paid'])), 0);
            $int_balance = max(($interest_components['scheduled_interest'] - $interest_components['amount_paid']), 0);



            $data['amount'] = max(($int_balance - $balance_for_interest), 0);
            $data['date_of_waiver'] = db_date_format($this->collection_date);
            $data['loan_id'] = $loan_id;
            $data['auth_id'] = $this->auth_id;
            $data['description'] = 'Interest for the un-utilized period Waived ';

            // $loan_instance = new Loan($this->conn);
            $this->loan_object->data_array = @$data;
            $this->loan_object->waiveInterest2();
            $this->getScheduleAmountTotal($loan_id);
            $this->updateTotalLoanAmount($loan_id);



            $loan = $this->getLoanDetails($loan_id);

            // set is fully paid
            $loan_is_fully_paid = true;
        }


        $principal_components = $this->getLoanPrincipal($loan_id);
        $interest_components = $this->getLoanInterest($loan_id);
        // print_r($interest_components);
        // return true;
        if (
            $amount > 0
        ) {
            $amount_balance = $amount;
            // print_r($interest_components);
            // return true;
            /**
             * create loan repayment transaction
             */
            $loan_total = $principal_components['scheduled_principal'] + $interest_components['scheduled_interest'];
            $loan_total_paid = $principal_components['amount_paid'] + $interest_components['amount_paid'];

            $loan_balance = $loan_total - $loan_total_paid - $total_installment;

            // $this->updateTotalLoanAmount($loan_id);
            $loan_penalty = (int) $loan['penalty_balance'];
            $loan_current_balance = (int) $loan['current_balance'];
            $total_loan_balance = $loan_current_balance + $loan_penalty;

            $transaction_interest_balance = 0;
            $transaction_principal_balance = 0;
            $current_schedule_total = $current_schedule['interest'] + $current_schedule['principal'];

            $loan_status = 3;


            /**
             * if repayment amount is enough to clear off the loan
             */

            if ($amount >= $total_loan_balance) {
                $loan_is_fully_paid = true;
                //TODO uncomment this
            }

            /**
             * amount is not enough to clear off the loan
             */
            else {
                $total_amount_paid = 0;
                $penalty_paid = 0;
                if ($this->clear_penalty && $loan['penalty_balance'] > 0) {
                    if ($penalty_amount <= $loan['penalty_balance']) {
                        $penalty_paid = $penalty_amount;
                    } else {
                        $penalty_paid = $loan['penalty_balance'];
                    }
                }

                // return $penalty_paid;
                $amount -= $penalty_paid;
                $amount_balance = $amount;

                $count = 0;
                // return $count;
                $next_pay_dates_array = [$current_schedule['date_of_payment']];
                $has_backward_schedule = true;
                // $total_amount_paid += $penalty_paid;

                // return $amount_balance -= $penalty_paid;
                $schedule_ids = [];
                $today_date = strtotime(date('Y-m-d'));

                $tot_princ_paid = 0;
                $tot_int_paid = 0;
                // return $today_date = strtotime($current_schedule['date_of_payment']);
                // return [$today_date, strtotime(date('Y-m-d', strtotime($current_schedule['date_of_payment']))), strtotime($current_schedule['date_of_payment'])];

                while ($amount_balance > 0 && $current_schedule) {
                    $total_paid = 0;
                    $interest_paid = 0;
                    $principal_paid = 0;
                    $penalty_paid = ($count == 0) ? $penalty_paid : 0;

                    if ($current_schedule['outstanding_principal'] == 0 && $current_schedule['principal_paid'] == 0) {
                        $current_schedule['outstanding_principal'] = $current_schedule['principal'];
                    }

                    if ($current_schedule['outstanding_interest'] == 0 && $current_schedule['interest_paid'] == 0) {
                        $current_schedule['outstanding_interest'] = $current_schedule['interest'];
                    }

                    $current_schedule_outstanding_total = $current_schedule['outstanding_interest'] + $current_schedule['outstanding_principal'];

                    /**
                     * if amount can only clear interest
                     */
                    if ($amount_balance <= $current_schedule['outstanding_interest']) {
                        $interest_paid = $amount_balance;
                    } else if ($amount_balance > $current_schedule['outstanding_interest']) {
                        $interest_paid = $current_schedule['outstanding_interest'];

                        /**
                         * if amount can clear both interest and principal
                         */
                        $balance = $amount_balance - $interest_paid;
                        if ($balance <= $current_schedule['outstanding_principal']) {
                            $principal_paid = $balance;
                        } else {
                            $principal_paid = $current_schedule['outstanding_principal'];
                        }
                    }

                    $total_paid = $interest_paid + $principal_paid;
                    $amount_balance -= $total_paid;

                    $total_paid += $penalty_paid;
                    // $amount_balance ? -= $penalty_paid;
                    // $amount_balance = ($count == 0) $amount_balance -=$penalty_paid

                    $total_amount_paid += $total_paid;

                    $amount_balance = max($amount_balance, 0);

                    $loan_total_outstanding_principal = max($loan['principal_balance'] - $principal_paid, 0);
                    $loan_total_outstanding_interest = max($loan['interest_balance'] - $interest_paid, 0);
                    $loan_total_outstanding = $loan_total_outstanding_principal + $loan_total_outstanding_interest;

                    // return array(
                    //     'interest_paid' => $interest_paid,
                    //     'principal_paid' => $principal_paid,
                    //     'penalty_paid' => $penalty_paid,
                    //     'amount_balance' => $amount_balance,
                    //     'total_paid' => $total_paid,
                    //     // 'current_schedule_outstanding_total' => $current_schedule_outstanding_total,
                    // );

                    $transaction_principal_balance = $current_schedule['principal'] - ($principal_paid + $current_schedule['principal_paid']);
                    $transaction_interest_balance = $current_schedule['interest'] - ($interest_paid + $current_schedule['interest_paid']);

                    /**
                     * Create Loan transaction
                     */
                    $tot_int_paid = $tot_int_paid + $interest_paid;
                    $tot_princ_paid = $tot_princ_paid + $principal_paid;
                    /**
                     * get next schedule
                     */
                    $backward_schedule = null;
                    $forward_schedule = null;
                    if ($amount_balance > 0) {
                        /** get schedule with due balances (status active) 
                         * and can be cleared off now
                         * */

                        $sqlQuery = ' SELECT * FROM loan_schedule WHERE loan_id=:loan_id AND status=:status AND date_of_payment > :date_of_payment ORDER BY date_of_payment ASC';
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':loan_id', $loan_id);
                        $stmt->bindParam(':status', $schedule_status_active);
                        $stmt->bindParam(':date_of_payment', $current_schedule['date_of_payment']);
                        $stmt->execute();
                        $forward_schedule = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($forward_schedule) {
                            array_push($next_pay_dates_array, $forward_schedule['date_of_payment']);
                            // var_dump($forward_schedule['schedule_id']);
                        }

                        // return $forward_schedule;
                    }

                    /**
                     * update date of next payment
                     */

                    $sqlQueryn = 'UPDATE loan SET date_of_next_pay=:date_of_next_pay, next_due_date=:date_of_next_pay WHERE public."loan".loan_no=:loan_no';
                    $stmtn = $this->conn->prepare($sqlQueryn);

                    $stmtn->bindParam(':loan_no', $loan_id);

                    $stmtn->bindParam(':date_of_next_pay', $current_schedule['date_of_payment']);
                    //TODO uncomment this
                    $stmtn->execute();

                    // update loan penalty
                    if ($penalty_paid > 0) {
                        $sqlQueryn = 'UPDATE loan SET penalty_balance=:penalty_balance WHERE public."loan".loan_no=:loan_no';
                        $stmtn = $this->conn->prepare($sqlQueryn);
                        $stmtn->bindParam(':loan_no', $loan_id);
                        $penalty_balance = max($loan['penalty_balance'] - $penalty_paid, 0);
                        $stmtn->bindParam(':penalty_balance', $penalty_balance);
                        //TODO uncomment this
                        $stmtn->execute();
                    }

                    /**
                     * update schedule
                     */
                    $schedule_status = $total_paid >= $current_schedule_outstanding_total ? $schedule_status_paid : $schedule_status_active;

                    $schedule_payment_date = strtotime($current_schedule['date_of_payment']);

                    if ($schedule_payment_date > $today_date) {
                        $schedule_performance_status = 'post_paid';
                        $loan_status = 2;
                    } else if ($schedule_payment_date < $today_date) {
                        $loan_status = 4;
                        $schedule_performance_status = 'late';
                    } else {
                        $loan_status = 2;
                        $schedule_performance_status = 'on_time';
                    }

                    $sqlQueryn = 'UPDATE loan_schedule SET principal_paid=principal_paid+:principal_paid,interest_paid=interest_paid+:interest_paid, outstanding_principal=:outstanding_principal, outstanding_interest=:outstanding_interest,status=:status, performance_status=:performance_status WHERE public."loan_schedule".schedule_id=:schedule_id ';
                    $stmtn = $this->conn->prepare($sqlQueryn);
                    $stmtn->bindParam(':schedule_id', $current_schedule['schedule_id']);
                    $stmtn->bindParam(':principal_paid', $principal_paid);
                    $stmtn->bindParam(':interest_paid', $interest_paid);
                    $stmtn->bindParam(':outstanding_principal', $transaction_principal_balance);
                    $stmtn->bindParam(':outstanding_interest', $transaction_interest_balance);
                    $stmtn->bindParam(':performance_status', $schedule_performance_status);
                    $stmtn->bindParam(':status', $schedule_status);

                    //TODO uncomment this
                    $stmtn->execute();
                    // var_dump($current_schedule['schedule_id']);

                    // $current_schedule = $backward_schedule ? $backward_schedule : $forward_schedule;
                    array_push($schedule_ids, $current_schedule['schedule_id']);
                    $current_schedule = $forward_schedule;

                    // var_dump($current_schedule['schedule_id']);
                    // return [$current_schedule, $amount_balance];
                    $count++;
                }

                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby, acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,date_created,outstanding_amount_total,loan_interest,outstanding_interest_total,pay_method,bacid,cheque_no,cash_acc) VALUES (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:dc,:oat,:li,:oit,:pay_m,:bacid,:cheque,:cash_acc)';

                $stmt = $this->conn->prepare($sqlQuery);
                // $ooaa = 0;
                $ooii = 0;
                $ooiit = 0;
                $ooaat = 0;
                $stmt->bindParam(':amount', $tot_princ_paid);
                $stmt->bindParam(':descri', $this->description);
                $stmt->bindParam(':autho', $_authorizedby);
                $stmt->bindParam(':actby', $_actionby);
                $stmt->bindParam(':accname', $acc_name);
                $stmt->bindParam(':mid', $member_id);
                $stmt->bindParam(':approv', $_authorizedby);
                $stmt->bindParam(':branc', $branch);
                $stmt->bindParam(':leftbal', $ooii);
                $stmt->bindParam(':lid', $loan_id);
                $stmt->bindParam(':ttype', $this->left_balance);
                $stmt->bindParam(':dc', $this->collection_date);
                // $stmt->bindParam(':oa', $ooaa);
                $stmt->bindParam(':oat', $ooaat);
                $stmt->bindParam(':li', $tot_int_paid);
                // $stmt->bindParam(':oi', $ooii);
                $stmt->bindParam(':oit', $ooiit);

                $stmt->bindParam(':pay_m', $pay_m);
                $stmt->bindParam(':bacid', $bacid);
                $stmt->bindParam(':cheque', $chequeno);
                // $stmt->bindParam(':send_sms', $sendsms);
                $stmt->bindParam(':cash_acc', $cashacc);

                //TODO uncomment this
                $stmt->execute();

                $sqlQueryn = 'UPDATE loan SET principal_due=:pdue,interest_due=:idue,principal_arrears=:par,interest_arrears=:iar WHERE public."loan".loan_no=:loan_no';
                $stmtn = $this->conn->prepare($sqlQueryn);
                $pdue = max(($p_due - $tot_princ_paid), 0);
                $idue = max(($i_due - $tot_int_paid), 0);
                $par = max(($p_ar - $tot_princ_paid), 0);
                $iar = max(($i_ar - $tot_int_paid), 0);
                $stmtn->bindParam(':loan_no', $loan_id);
                $stmtn->bindParam(':pdue', $pdue);
                $stmtn->bindParam(':idue', $idue);
                $stmtn->bindParam(':par', $par);
                $stmtn->bindParam(':iar', $iar);

                $stmtn->execute();

                $this->updateTotalLoanAmount($loan_id, $loan_is_fully_paid);



                if (!$this->clear_penalty) {
                    if ($amount_balance > 0 && $loan['penalty_balance'] > 0) {
                        if ($amount_balance <= $loan['penalty_balance']) {
                            $penalty_paid = $amount_balance;
                        } else {
                            $penalty_paid = $loan['penalty_balance'];
                        }

                        /**
                         * update penalty balance
                         */
                        $sqlQueryn = 'UPDATE  public."loan" SET penalty_balance=:penalty_balance  WHERE public."loan".loan_no=:loan_no';
                        $stmtn = $this->conn->prepare($sqlQueryn);
                        $stmtn->bindParam(':loan_no', $loan_id);
                        $penalty_balance = $loan['penalty_balance'] - $penalty_paid;
                        $stmtn->bindParam(':penalty_balance', $penalty_balance);
                        $stmtn->execute();
                        $amount_balance = max($amount_balance - $penalty_paid, 0);

                        $total_amount_paid += $penalty_paid;
                    }
                }
            }

            $loan_status = $loan_is_fully_paid ? 5 : $loan_status;

            $sqlQueryn = 'UPDATE loan SET status=:loan_status WHERE loan_no=:loan_no';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':loan_no', $loan_id);
            $stmtn->bindParam(':loan_status', $loan_status);
            //TODO uncomment this
            $stmtn->execute();

            // return $current_schedule;

            /** 
             * update member account balance
             */


            if ($loan_is_fully_paid) {
                $amount_balance = max($amount_balance - $total_loan_balance, 0);
            }

            // return $amount_balance;

            if ($amount_balance > 0) {
                if ($member['membership_no'] > 0) {
                    $account_balance = $member['acc_balance'] + $amount_balance;
                    $sqlQueryn = 'UPDATE  public."Client" SET acc_balance=:cb  WHERE public."Client"."userId"=:id';
                } else {
                    $account_balance = $member['loan_wallet'] + $amount_balance;
                    $sqlQueryn = 'UPDATE  public."Client" SET loan_wallet=:cb  WHERE public."Client"."userId"=:id';
                }

                $stmtn = $this->conn->prepare($sqlQueryn);
                $stmtn->bindParam(':id', $member_id);
                $stmtn->bindParam(':cb', $account_balance);
                $stmtn->execute();
            }

            if ($this->pay_method == 'saving') {
                $amount_balance = max($amount_balance, 0);
                if ($member['membership_no'] > 0) {

                    $account_balance = $member['acc_balance'] - $subtract_amount;


                    $sqlQueryn = 'UPDATE  public."Client" SET acc_balance=:cb  WHERE public."Client"."userId"=:id';
                } else {
                    $account_balance = $member['loan_wallet']  - $total_amount_paid;
                    $sqlQueryn = 'UPDATE  public."Client" SET loan_wallet=:cb  WHERE public."Client"."userId"=:id';
                }

                $stmtn = $this->conn->prepare($sqlQueryn);
                $stmtn->bindParam(':id', $member_id);
                $stmtn->bindParam(':cb', $account_balance);
                $stmtn->execute();
            }

            // return true;

            // return [$next_pay_dates_array, max($next_pay_dates_array), $has_backward_schedule, $amount_balance];

            if ($loan_is_fully_paid) {

                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby, acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,date_created,outstanding_amount_total,loan_interest,outstanding_interest_total,pay_method,bacid,cheque_no,cash_acc) VALUES (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:dc,:oat,:li,:oit,:pay_m,:bacid,:cheque,:cash_acc)';

                $stmt = $this->conn->prepare($sqlQuery);
                // $ooaa = 0;
                $ooii = 0;
                $ooiit = (int) $interest_components['balance'] - $interest_paid;
                $ooaat = (int) $principal_components['balance'] - $principal_paid;
                $stmt->bindParam(':amount', $loan['principal_balance']);
                $stmt->bindParam(':descri', $this->description);
                $stmt->bindParam(':autho', $_authorizedby);
                $stmt->bindParam(':actby', $_actionby);
                $stmt->bindParam(':accname', $acc_name);
                $stmt->bindParam(':mid', $member_id);
                $stmt->bindParam(':approv', $_authorizedby);
                $stmt->bindParam(':branc', $branch);
                $stmt->bindParam(':leftbal', $ooii);
                $stmt->bindParam(':lid', $loan_id);
                $stmt->bindParam(':ttype', $this->left_balance);
                $stmt->bindParam(':dc', $this->collection_date);
                // $stmt->bindParam(':oa', $ooaa);
                $stmt->bindParam(':oat', $ooaat);
                $stmt->bindParam(':li', $loan['interest_balance']);
                // $stmt->bindParam(':oi', $ooii);
                $stmt->bindParam(':oit', $ooiit);

                $stmt->bindParam(':pay_m', $pay_m);
                $stmt->bindParam(':bacid', $bacid);
                $stmt->bindParam(':cheque', $chequeno);
                // $stmt->bindParam(':send_sms', $sendsms);
                $stmt->bindParam(':cash_acc', $cashacc);

                //TODO uncomment this
                $stmt->execute();


                $sqlQueryn = 'UPDATE loan SET principal_due=:pdue,interest_due=:idue,principal_arrears=:par,interest_arrears=:iar WHERE public."loan".loan_no=:loan_no';
                $stmtn = $this->conn->prepare($sqlQueryn);
                $pdue = 0;
                $idue = 0;
                $par = 0;
                $iar = 0;
                $stmtn->bindParam(':loan_no', $loan_id);
                $stmtn->bindParam(':pdue', $pdue);
                $stmtn->bindParam(':idue', $idue);
                $stmtn->bindParam(':par', $par);
                $stmtn->bindParam(':iar', $iar);

                $stmtn->execute();


                // $this->clearLoan($loan_id);
                $this->updateTotalLoanAmount($loan_id, true);
            }
        }

        /** 
         * re update loan amountsto generate correct figures
         */
        $this->updateTotalLoanAmount($loan_id);

        return true;
    }

    public function createManualLoanRepayPI()
    {
        // $loan_object = new Loan($this->conn);
        $interest = (int) $this->interest;
        $principal = (int) $this->principal;
        $loan_id = $this->loan_id;
        $total_installment = @$this->principal + @$this->interest;
        $amount = @$total_installment;
        $subtract_amount = @$total_installment;
        $penalty_amount = @$this->penalty_amount;

        $loan = $this->getLoanDetails($loan_id);
        // return $loan['date_of_next_pay'];
        if (!$loan) return "Loan not found";


        // $update = $this->updateTotalLoanAmount($loan_id);
        // return $update;

        $p_due = $loan['principal_due'] ?? 0;
        $i_due = $loan['interest_due'] ?? 0;

        $p_ar = $loan['principal_arrears'] ?? 0;
        $i_ar = $loan['interest_arrears'] ?? 0;

        if ($this->clear_penalty && $penalty_amount > $loan['penalty_balance']) return false;
        if ($this->clear_penalty && $penalty_amount > $amount) return false;

        $this->createdAt = date("Y-m-d h:i:s", strtotime($this->createdAt));
        $this->date_of_next_pay = date("Y-m-d h:i:s", strtotime($this->date_of_next_pay));
        $this->collection_date = date("Y-m-d h:i:s", strtotime($this->collection_date));

        // get clients details
        $member_id = $loan['account_id'];

        $sqlQueryn = 'SELECT * FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

        $stmtn = $this->conn->prepare($sqlQueryn);

        $stmtn->bindParam(':id', $member_id);
        $stmtn->execute();
        $member = $stmtn->fetch(PDO::FETCH_ASSOC);

        if (!$member) return "Member not found";
        // return $member;
        $current_schedule = $this->getLoanCurrentSchedule($loan_id);
        // return $current_schedule;
        if (!$current_schedule) $current_schedule = $this->getLoanNextSchedule($loan_id);
        // return true;
        if (!$current_schedule) return "Loan Schedule not found";

        $schedule_status_active = 'active';
        $schedule_status_paid = 'paid';

        // print_r($member);
        // $total_installment = @$this->amount + $interest;

        $account_balance = $member['membership_no'] > 0 ? $member['acc_balance'] : $member['loan_wallet'];
        if ($this->pay_method == 'saving') {
            $pay_m = 'saving';
            $bacid = '';
            $cashacc = '';
            $chequeno = '';
            if ($this->amount > $account_balance) return "Insufficent Account Balance";
        } else if ($this->pay_method == 'cash') {
            $pay_m = 'cash';
            $bacid = '';
            $cashacc = $this->cash_acc;
            $chequeno = '';
        } else if ($this->pay_method == 'cheque') {
            $pay_m = 'cheque';
            $bacid = $this->bank_acc;
            $cashacc = '';
            $chequeno = '';
            if ($this->cheque_no > 0) {
                $chequeno = $this->cheque_no;
            }
        }

        // return "OOps";

        $_authorizedby = $this->auth_id;
        $_actionby = "Loans Department";

        $acc_name = $member['firstName'] . " " . $member['lastName'];
        $branch = $loan['branchid'];

        $loan_is_fully_paid = false;



        $principal_components = $this->getLoanPrincipal($loan_id);
        $interest_components = $this->getLoanInterest($loan_id);
        // print_r($interest_components);
        // return true;
        if (
            $amount > 0
        ) {
            $amount_balance = $amount;
            // print_r($interest_components);
            // return true;
            /**
             * create loan repayment transaction
             */
            $loan_total = $principal_components['scheduled_principal'] + $interest_components['scheduled_interest'];
            $loan_total_paid = $principal_components['amount_paid'] + $interest_components['amount_paid'];

            $loan_balance = $loan_total - $loan_total_paid - $total_installment;

            // $this->updateTotalLoanAmount($loan_id);
            $loan_penalty = (int) $loan['penalty_balance'];
            $loan_current_balance = (int) $loan['current_balance'];
            $total_loan_balance = $loan_current_balance + $loan_penalty;

            $transaction_interest_balance = 0;
            $transaction_principal_balance = 0;
            $current_schedule_total = $current_schedule['interest'] + $current_schedule['principal'];

            $loan_status = 3;


            /**
             * if repayment amount is enough to clear off the loan
             */

            if ($amount >= $total_loan_balance) {
                $loan_is_fully_paid = true;
                //TODO uncomment this
            }

            /**
             * amount is not enough to clear off the loan
             */
            else {
                $total_amount_paid = 0;
                $penalty_paid = 0;
                if ($this->clear_penalty && $loan['penalty_balance'] > 0) {
                    if ($penalty_amount <= $loan['penalty_balance']) {
                        $penalty_paid = $penalty_amount;
                    } else {
                        $penalty_paid = $loan['penalty_balance'];
                    }
                }

                // return $penalty_paid;
                $amount -= $penalty_paid;
                $amount_balance = $amount;

                $princ_given = max($principal, 0);
                $int_given = max($interest, 0);

                $count = 0;
                $tot_princ_paid = 0;
                $tot_int_paid = 0;
                // return $count;
                $next_pay_dates_array = [$current_schedule['date_of_payment']];
                $has_backward_schedule = true;
                // $total_amount_paid += $penalty_paid;

                // return $amount_balance -= $penalty_paid;
                $schedule_ids = [];
                $today_date = strtotime(date('Y-m-d'));
                // return $today_date = strtotime($current_schedule['date_of_payment']);
                // return [$today_date, strtotime(date('Y-m-d', strtotime($current_schedule['date_of_payment']))), strtotime($current_schedule['date_of_payment'])];

                while ($amount_balance > 0 && $current_schedule) {
                    $total_paid = 0;
                    $interest_paid = 0;
                    $principal_paid = 0;
                    $penalty_paid = ($count == 0) ? $penalty_paid : 0;

                    if ($current_schedule['outstanding_principal'] == 0 && $current_schedule['principal_paid'] == 0) {
                        $current_schedule['outstanding_principal'] = $current_schedule['principal'];
                    }

                    if ($current_schedule['outstanding_interest'] == 0 && $current_schedule['interest_paid'] == 0) {
                        $current_schedule['outstanding_interest'] = $current_schedule['interest'];
                    }

                    $current_schedule_outstanding_total = $current_schedule['outstanding_interest'] + $current_schedule['outstanding_principal'];

                    /**
                     * if amount can only clear interest
                     */
                    if ($int_given <= $current_schedule['outstanding_interest']) {
                        $interest_paid = $int_given;
                    } else if ($int_given > $current_schedule['outstanding_interest']) {
                        $interest_paid = $current_schedule['outstanding_interest'];

                        /**
                         * if amount can clear both interest and principal
                         */
                        $balance = $amount_balance - $interest_paid;
                        $int_balancen = $int_given - $interest_paid;
                    }

                    if ($princ_given <= $current_schedule['outstanding_principal']) {
                        $principal_paid = $princ_given;
                    } else if ($princ_given > $current_schedule['outstanding_principal']) {
                        $principal_paid = $current_schedule['outstanding_principal'];

                        $balance = $amount_balance - $principal_paid;
                        $princ_balancen = $princ_given - $principal_paid;
                    }



                    $total_paid = $interest_paid + $principal_paid;
                    $amount_balance -= $total_paid;

                    $total_paid += $penalty_paid;
                    $princ_given -= $principal_paid;
                    $int_given -= $interest_paid;
                    // $amount_balance ? -= $penalty_paid;
                    // $amount_balance = ($count == 0) $amount_balance -=$penalty_paid

                    $total_amount_paid += $total_paid;

                    $amount_balance = max($amount_balance, 0);

                    $loan_total_outstanding_principal = max($loan['principal_balance'] - $principal_paid, 0);
                    $loan_total_outstanding_interest = max($loan['interest_balance'] - $interest_paid, 0);
                    $loan_total_outstanding = $loan_total_outstanding_principal + $loan_total_outstanding_interest;

                    // return array(
                    //     'interest_paid' => $interest_paid,
                    //     'principal_paid' => $principal_paid,
                    //     'penalty_paid' => $penalty_paid,
                    //     'amount_balance' => $amount_balance,
                    //     'total_paid' => $total_paid,
                    //     // 'current_schedule_outstanding_total' => $current_schedule_outstanding_total,
                    // );

                    $transaction_principal_balance = $current_schedule['principal'] - ($principal_paid + $current_schedule['principal_paid']);
                    $transaction_interest_balance = $current_schedule['interest'] - ($interest_paid + $current_schedule['interest_paid']);

                    /**
                     * Create Loan transaction
                     */
                    $tot_princ_paid = $tot_princ_paid + $principal_paid;
                    $tot_int_paid = $tot_int_paid + $interest_paid;

                    /**
                     * get next schedule
                     */
                    $backward_schedule = null;
                    $forward_schedule = null;
                    if ($amount_balance > 0) {
                        /** get schedule with due balances (status active) 
                         * and can be cleared off now
                         * */

                        $sqlQuery = ' SELECT * FROM loan_schedule WHERE loan_id=:loan_id AND status=:status AND date_of_payment > :date_of_payment ORDER BY date_of_payment ASC';
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':loan_id', $loan_id);
                        $stmt->bindParam(':status', $schedule_status_active);
                        $stmt->bindParam(':date_of_payment', $current_schedule['date_of_payment']);
                        $stmt->execute();
                        $forward_schedule = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($forward_schedule) {
                            array_push($next_pay_dates_array, $forward_schedule['date_of_payment']);
                            // var_dump($forward_schedule['schedule_id']);
                        }

                        // return $forward_schedule;
                    }

                    /**
                     * update date of next payment
                     */

                    $sqlQueryn = 'UPDATE loan SET date_of_next_pay=:date_of_next_pay WHERE public."loan".loan_no=:loan_no';
                    $stmtn = $this->conn->prepare($sqlQueryn);
                    $stmtn->bindParam(':loan_no', $loan_id);
                    $stmtn->bindParam(':date_of_next_pay', $current_schedule['date_of_payment']);
                    //TODO uncomment this
                    $stmtn->execute();

                    // update loan penalty
                    if ($penalty_paid > 0) {
                        $sqlQueryn = 'UPDATE loan SET penalty_balance=:penalty_balance WHERE public."loan".loan_no=:loan_no';
                        $stmtn = $this->conn->prepare($sqlQueryn);
                        $stmtn->bindParam(':loan_no', $loan_id);
                        $penalty_balance = max($loan['penalty_balance'] - $penalty_paid, 0);
                        $stmtn->bindParam(':penalty_balance', $penalty_balance);
                        //TODO uncomment this
                        $stmtn->execute();
                    }

                    /**
                     * update schedule
                     */
                    $schedule_status = $total_paid >= $current_schedule_outstanding_total ? $schedule_status_paid : $schedule_status_active;

                    $schedule_payment_date = strtotime($current_schedule['date_of_payment']);

                    if ($schedule_payment_date > $today_date) {
                        $schedule_performance_status = 'post_paid';
                        $loan_status = 2;
                    } else if ($schedule_payment_date < $today_date) {
                        $loan_status = 4;
                        $schedule_performance_status = 'late';
                    } else {
                        $loan_status = 2;
                        $schedule_performance_status = 'on_time';
                    }

                    $sqlQueryn = 'UPDATE loan_schedule SET principal_paid=principal_paid+:principal_paid,interest_paid=interest_paid+:interest_paid, outstanding_principal=:outstanding_principal, outstanding_interest=:outstanding_interest,status=:status, performance_status=:performance_status WHERE public."loan_schedule".schedule_id=:schedule_id ';
                    $stmtn = $this->conn->prepare($sqlQueryn);
                    $stmtn->bindParam(':schedule_id', $current_schedule['schedule_id']);
                    $stmtn->bindParam(':principal_paid', $principal_paid);
                    $stmtn->bindParam(':interest_paid', $interest_paid);
                    $stmtn->bindParam(':outstanding_principal', $transaction_principal_balance);
                    $stmtn->bindParam(':outstanding_interest', $transaction_interest_balance);
                    $stmtn->bindParam(':performance_status', $schedule_performance_status);
                    $stmtn->bindParam(':status', $schedule_status);

                    //TODO uncomment this
                    $stmtn->execute();
                    // var_dump($current_schedule['schedule_id']);

                    // $current_schedule = $backward_schedule ? $backward_schedule : $forward_schedule;
                    array_push($schedule_ids, $current_schedule['schedule_id']);
                    $current_schedule = $forward_schedule;

                    // var_dump($current_schedule['schedule_id']);
                    // return [$current_schedule, $amount_balance];
                    $count++;
                }
                //  record payment transaction
                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby, acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,date_created,outstanding_amount_total,loan_interest,outstanding_interest_total,pay_method,bacid,cheque_no,cash_acc) VALUES (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:dc,:oat,:li,:oit,:pay_m,:bacid,:cheque,:cash_acc)';

                $stmt = $this->conn->prepare($sqlQuery);
                // $ooaa = 0;
                $ooii = 0;
                $ooiit = 0;
                $ooaat = 0;
                $stmt->bindParam(':amount', $tot_princ_paid);
                $stmt->bindParam(':descri', $this->description);
                $stmt->bindParam(':autho', $_authorizedby);
                $stmt->bindParam(':actby', $_actionby);
                $stmt->bindParam(':accname', $acc_name);
                $stmt->bindParam(':mid', $member_id);
                $stmt->bindParam(':approv', $_authorizedby);
                $stmt->bindParam(':branc', $branch);
                $stmt->bindParam(':leftbal', $ooii);
                $stmt->bindParam(':lid', $loan_id);
                $stmt->bindParam(':ttype', $this->left_balance);
                $stmt->bindParam(':dc', $this->collection_date);
                // $stmt->bindParam(':oa', $ooaa);
                $stmt->bindParam(':oat', $ooaat);
                $stmt->bindParam(':li', $tot_int_paid);
                // $stmt->bindParam(':oi', $ooii);
                $stmt->bindParam(':oit', $ooiit);

                $stmt->bindParam(':pay_m', $pay_m);
                $stmt->bindParam(':bacid', $bacid);
                $stmt->bindParam(':cheque', $chequeno);
                // $stmt->bindParam(':send_sms', $sendsms);
                $stmt->bindParam(':cash_acc', $cashacc);

                //TODO uncomment this
                $stmt->execute();


                $sqlQueryn = 'UPDATE loan SET principal_due=:pdue,interest_due=:idue,principal_arrears=:par,interest_arrears=:iar WHERE public."loan".loan_no=:loan_no';
                $stmtn = $this->conn->prepare($sqlQueryn);
                $pdue = max(($p_due - $tot_princ_paid), 0);
                $idue = max(($i_due - $tot_int_paid), 0);
                $par = max(($p_ar - $tot_princ_paid), 0);
                $iar = max(($i_ar - $tot_int_paid), 0);
                $stmtn->bindParam(':loan_no', $loan_id);
                $stmtn->bindParam(':pdue', $pdue);
                $stmtn->bindParam(':idue', $idue);
                $stmtn->bindParam(':par', $par);
                $stmtn->bindParam(':iar', $iar);

                $stmtn->execute();

                $this->updateTotalLoanAmount($loan_id, $loan_is_fully_paid);


                if (!$this->clear_penalty) {
                    if ($amount_balance > 0 && $loan['penalty_balance'] > 0) {
                        if ($amount_balance <= $loan['penalty_balance']) {
                            $penalty_paid = $amount_balance;
                        } else {
                            $penalty_paid = $loan['penalty_balance'];
                        }

                        /**
                         * update penalty balance
                         */
                        $sqlQueryn = 'UPDATE  public."loan" SET penalty_balance=:penalty_balance  WHERE public."loan".loan_no=:loan_no';
                        $stmtn = $this->conn->prepare($sqlQueryn);
                        $stmtn->bindParam(':loan_no', $loan_id);
                        $penalty_balance = $loan['penalty_balance'] - $penalty_paid;
                        $stmtn->bindParam(':penalty_balance', $penalty_balance);
                        $stmtn->execute();
                        $amount_balance = max($amount_balance - $penalty_paid, 0);

                        $total_amount_paid += $penalty_paid;
                    }
                }
            }

            $loan_status = $loan_is_fully_paid ? 5 : $loan_status;

            $sqlQueryn = 'UPDATE loan SET status=:loan_status WHERE loan_no=:loan_no';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':loan_no', $loan_id);
            $stmtn->bindParam(':loan_status', $loan_status);
            //TODO uncomment this
            $stmtn->execute();

            // return $current_schedule;

            /** 
             * update member account balance
             */


            if ($loan_is_fully_paid) {
                $amount_balance = max($amount_balance - $total_loan_balance, 0);
            }

            // return $amount_balance;

            if ($amount_balance > 0) {
                if ($member['membership_no'] > 0) {
                    $account_balance = $member['acc_balance'] + $amount_balance;
                    $sqlQueryn = 'UPDATE  public."Client" SET acc_balance=:cb  WHERE public."Client"."userId"=:id';
                } else {
                    $account_balance = $member['loan_wallet'] + $amount_balance;
                    $sqlQueryn = 'UPDATE  public."Client" SET loan_wallet=:cb  WHERE public."Client"."userId"=:id';
                }

                $stmtn = $this->conn->prepare($sqlQueryn);
                $stmtn->bindParam(':id', $member_id);
                $stmtn->bindParam(':cb', $account_balance);
                $stmtn->execute();
            }

            if ($this->pay_method == 'saving') {
                $amount_balance = max($amount_balance, 0);
                if ($member['membership_no'] > 0) {

                    $account_balance = $member['acc_balance'] - $subtract_amount;


                    $sqlQueryn = 'UPDATE  public."Client" SET acc_balance=:cb  WHERE public."Client"."userId"=:id';
                } else {
                    $account_balance = $member['loan_wallet']  - $total_amount_paid;
                    $sqlQueryn = 'UPDATE  public."Client" SET loan_wallet=:cb  WHERE public."Client"."userId"=:id';
                }

                $stmtn = $this->conn->prepare($sqlQueryn);
                $stmtn->bindParam(':id', $member_id);
                $stmtn->bindParam(':cb', $account_balance);
                $stmtn->execute();
            }

            // return true;

            // return [$next_pay_dates_array, max($next_pay_dates_array), $has_backward_schedule, $amount_balance];

            if ($loan_is_fully_paid) {

                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby, acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,date_created,outstanding_amount_total,loan_interest,outstanding_interest_total,pay_method,bacid,cheque_no,cash_acc) VALUES (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:dc,:oat,:li,:oit,:pay_m,:bacid,:cheque,:cash_acc)';

                $stmt = $this->conn->prepare($sqlQuery);
                // $ooaa = 0;
                $ooii = 0;
                $ooiit = (int) $interest_components['balance'] - $interest_paid;
                $ooaat = (int) $principal_components['balance'] - $principal_paid;
                $stmt->bindParam(':amount', $loan['principal_balance']);
                $stmt->bindParam(':descri', $this->description);
                $stmt->bindParam(':autho', $_authorizedby);
                $stmt->bindParam(':actby', $_actionby);
                $stmt->bindParam(':accname', $acc_name);
                $stmt->bindParam(':mid', $member_id);
                $stmt->bindParam(':approv', $_authorizedby);
                $stmt->bindParam(':branc', $branch);
                $stmt->bindParam(':leftbal', $ooii);
                $stmt->bindParam(':lid', $loan_id);
                $stmt->bindParam(':ttype', $this->left_balance);
                $stmt->bindParam(':dc', $this->collection_date);
                // $stmt->bindParam(':oa', $ooaa);
                $stmt->bindParam(':oat', $ooaat);
                $stmt->bindParam(':li', $loan['interest_balance']);
                // $stmt->bindParam(':oi', $ooii);
                $stmt->bindParam(':oit', $ooiit);

                $stmt->bindParam(':pay_m', $pay_m);
                $stmt->bindParam(':bacid', $bacid);
                $stmt->bindParam(':cheque', $chequeno);
                // $stmt->bindParam(':send_sms', $sendsms);
                $stmt->bindParam(':cash_acc', $cashacc);

                //TODO uncomment this
                $stmt->execute();


                $sqlQueryn = 'UPDATE loan SET principal_due=:pdue,interest_due=:idue,principal_arrears=:par,interest_arrears=:iar WHERE public."loan".loan_no=:loan_no';
                $stmtn = $this->conn->prepare($sqlQueryn);
                $pdue = 0;
                $idue = 0;
                $par = 0;
                $iar = 0;
                $stmtn->bindParam(':loan_no', $loan_id);
                $stmtn->bindParam(':pdue', $pdue);
                $stmtn->bindParam(':idue', $idue);
                $stmtn->bindParam(':par', $par);
                $stmtn->bindParam(':iar', $iar);

                $stmtn->execute();
                // $this->clearLoan($loan_id);
                $this->updateTotalLoanAmount($loan_id, true);
            }
        }

        /** 
         * re update loan amountsto generate correct figures
         */
        $this->updateTotalLoanAmount($loan_id);

        return true;
    }


    /**
     * waive loan interest
     */
    public function waiveInterest()
    {
        $loan_id = $this->data_array['loan_id'];
        // $this->bank_object->updateTotalLoanAmount($loan_id);
        // return;
        // $loan = $this->conn->fetch('loan', 'loan_no', $loan_id);

        $sqlQueryn = 'SELECT * FROM  public."loan"  WHERE public."loan".loan_no=:id';

        $stmtn = $this->conn->prepare($sqlQueryn);

        $stmtn->bindParam(':id', $loan_id);
        $stmtn->execute();
        $loan = $stmtn->fetch(PDO::FETCH_ASSOC);



        if (!$loan) return "Loan not found";
        $amount = $this->data_array['amount'];
        $interest_balance = $loan['interest_balance'];

        // $client = $this->conn->fetch('Client', 'userId', $loan['account_id']);

        $sqlQueryn = 'SELECT * FROM  public."Client"  WHERE public."Client"."userId"=:id';

        $stmtn = $this->conn->prepare($sqlQueryn);

        $stmtn->bindParam(':id', $loan['account_id']);
        $stmtn->execute();
        $client = $stmtn->fetch(PDO::FETCH_ASSOC);


        if (!$client) return "Member not found";

        // $client_user = $this->conn->fetch('User', 'id', $client['userId']);


        $sqlQueryn = 'SELECT * FROM  public."User"  WHERE public."User".id=:id';

        $stmtn = $this->conn->prepare($sqlQueryn);

        $stmtn->bindParam(':id', $client['userId']);
        $stmtn->execute();
        $client_user = $stmtn->fetch(PDO::FETCH_ASSOC);

        $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];

        if ($amount > $interest_balance) return "Interest to waiver can not be greater than " . number_format($interest_balance);

        // $active_schedule = $this->conn->database->query('SELECT * FROM loan_schedule WHERE loan_id=? AND status=? AND interest >? ORDER BY date_of_payment ASC', $loan_id, 'active', 0)->fetchAll();

        $sqlQueryn = 'SELECT * FROM loan_schedule WHERE loan_id=:id AND status=\'active\' AND interest >0 ORDER BY date_of_payment ASC';

        $stmtn = $this->conn->prepare($sqlQueryn);

        $stmtn->bindParam(':id', $loan_id);
        $stmtn->execute();
        $active_schedule = $stmtn->fetch(PDO::FETCH_ASSOC);

        $amount_balance = $amount;
        $i = 0;

        while ($amount_balance > 0 && count($active_schedule) >= $i) {
            $current_schedule = $active_schedule[$i];
            // $amount_balance += (int)$current_schedule['interest_paid'];
            $interest_waivered = 0;
            $interest_paid = (int)$current_schedule['interest_paid'];
            $interest_to_pay = $current_schedule['interest'] - $interest_paid;
            $original_interest = $current_schedule['amount'] - $current_schedule['principal'];

            $new_interest_paid = 0;
            if ($amount_balance <= $current_schedule['interest']) {
                $new_interest = $current_schedule['interest'] - $amount_balance;
                $interest_waivered = $amount_balance;

                if ($interest_paid > $new_interest) {
                    // $balance = $interest_paid - $new_interest;
                    $new_interest_paid = $new_interest;
                    $amount_balance += ($interest_paid - $new_interest);
                    $outstanding_interest = 0;
                } else {
                    $outstanding_interest = $new_interest - $interest_paid;
                    $new_interest_paid = $interest_paid;
                }

                $amount_balance -= $interest_waivered;
            } else {
                $new_interest = 0;
                $outstanding_interest = 0;
                // $interest_waivered = $amount ;
                $balance = $amount_balance - $current_schedule['interest'] + $interest_paid;
                $interest_waivered = $original_interest - $interest_paid - $current_schedule['interest_waivered'];
                $amount_balance = $balance;
                $new_interest_paid = $interest_paid;
            }

            // return [
            //   'new_interest' => $new_interest,
            //   'interest_waivered' => $interest_waivered,
            //   'outstanding_interest' => $outstanding_interest,
            //   'amount_balance' => $amount_balance,
            // ];

            // $this->conn->update('loan_schedule', [
            //     'interest' => $new_interest,
            //     'interest_paid' => $new_interest_paid,
            //     'outstanding_interest' => $outstanding_interest,
            //     'interest_waivered' => $current_schedule['interest_waivered'] += $interest_waivered,
            // ], 'schedule_id', $current_schedule['schedule_id']);


            $sqlQueryn = 'UPDATE public."loan_schedule"  SET interest=:interest, interest_paid=:interest_paid, outstanding_interest=:outstanding_interest, interest_waivered=:interest_waivered  WHERE public."loan_schedule".schedule_id=:id';

            $stmtn = $this->conn->prepare($sqlQueryn);

            $stmtn->bindParam(':interest', $new_interest);
            $stmtn->bindParam(':interest_paid', $new_interest_paid);
            $stmtn->bindParam(':outstanding_interest', $outstanding_interest);
            $stmtn->bindParam(':interest_waivered', $current_schedule['interest_waivered'] += $interest_waivered);
            $stmtn->bindParam(':id', $current_schedule['schedule_id']);
            $stmtn->execute();

            $i++;
            // $current_schedule = $active_schedule[$i];
        }

        // $this->conn->update('loan', [
        //   'int_waivered' => ($loan['int_waivered'] ?? 0) + $amount
        // ], 'loan_no', $loan_id);


        /**
         * create waiver transaction
         */
        // $this->conn->insert('transactions', [
        //     'amount' => $amount,
        //     'description' => $this->data_array['description'] ?? 'Waive Loan interest',
        //     '_authorizedby' => $this->data_array['auth_id'],
        //     '_actionby' => $this->data_array['auth_id'],
        //     'acc_name' => $client_names,
        //     'mid' => $client['userId'],
        //     'approvedby' => $this->data_array['auth_id'],
        //     '_branch' => $client['branchId'],
        //     't_type' => 'WLI',
        //     'loan_id' => $loan_id,
        //     'date_created' => $this->data_array['date_of_waiver'],
        //     'outstanding_amount' => $loan['principal_balance'],
        //     'outstanding_amount_total' => $loan['principal_balance'],
        //     'loan_interest' => $loan['interest_amount'] - $amount,
        //     'outstanding_interest' => $loan['interest_balance'] - $amount,
        //     'outstanding_interest_total' => $loan['interest_balance'] - $amount,
        //     'pay_method' => 'cash',
        //     'loan_penalty' => $loan['penalty_balance']
        // ]);



        $sqlQueryn = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,loan_id,date_created,outstanding_amount,outstanding_amount_total,loan_interest,outstanding_interest,outstanding_interest_total,pay_method,loan_penalty) VALUES (:amount,:description,:_authorizedby,:_actionby,:acc_name,:mid,:approvedby,:_branch,:t_type,:loan_id,:date_created,:outstanding_amount,:outstanding_amount_total,:loan_interest,:outstanding_interest,:outstanding_interest_total,:pay_method,:loan_penalty)';

        $stmtn = $this->conn->prepare($sqlQueryn);
        $pm = 'cash';
        $ty = 'WLI';
        $des = $this->data_array['description'] ?? 'Waive Loan interest';

        $stmtn->bindParam(':amount', $amount);
        $stmtn->bindParam(':description', $des);
        $stmtn->bindParam(':_authorizedby', $this->data_array['auth_id']);
        $stmtn->bindParam(':_actionby', $this->data_array['auth_id']);
        $stmtn->bindParam(':acc_name', $client_names);
        $stmtn->bindParam(':mid', $client['userId']);
        $stmtn->bindParam(':approvedby', $this->data_array['auth_id']);
        $stmtn->bindParam(':_branch', $client['branchId']);
        $stmtn->bindParam(':t_type', $ty);
        $stmtn->bindParam(':loan_id', $loan_id);
        $stmtn->bindParam(':date_created', $this->data_array['date_of_waiver']);
        $stmtn->bindParam(':outstanding_amount', $loan['principal_balance']);
        $stmtn->bindParam(':outstanding_amount_total', $loan['principal_balance']);
        $stmtn->bindParam(':loan_interest', $loan['interest_amount'] - $amount);
        $stmtn->bindParam(':outstanding_interest', $loan['interest_balance'] - $amount);
        $stmtn->bindParam(':outstanding_interest_total', $loan['interest_balance'] - $amount);
        $stmtn->bindParam(':pay_method', $pm);
        $stmtn->bindParam(':loan_penalty', $loan['penalty_balance']);
        $stmtn->execute();

        $this->updateTotalLoanAmount($loan_id);

        return true;
    }

    // function 

    function clearLoan($loan_id, $amount = 0)
    {
        $loan = $this->getLoanDetails($loan_id);
        if ($loan) {
            /**
             * clear all loans balances
             */
            $sqlQuery = ' UPDATE public.loan SET current_balance=:current_balance,principal_balance=:principal_balance,interest_balance=:interest_balance,penalty_balance=:penalty_balance,principal_arrears=:principal_arrears,interest_arrears=:interest_arrears,status=:loan_status WHERE loan_no=:loan_no ';
            $reset_value = 0;
            $loan_status = 5;
            $penalty_balance = 0;

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':current_balance', $reset_value);
            $stmt->bindParam(':principal_balance', $reset_value);
            $stmt->bindParam(':interest_balance', $reset_value);
            $stmt->bindParam(':principal_arrears', $reset_value);
            $stmt->bindParam(':interest_arrears', $reset_value);
            $stmt->bindParam(':loan_status', $loan_status);
            $stmt->bindParam(':penalty_balance', $penalty_balance);
            $stmt->bindParam(':loan_no', $loan['loan_no']);
            $stmt->execute();

            /**
             * clear loan arrears if any
             */
            $paid_status = 'paid';
            $active_status = 'active';
            $sqlQuery = 'UPDATE public.loan_schedule SET principal_paid=amount, interest_paid=interest, outstanding_principal=:outstanding_principal,outstanding_interest=:outstanding_interest WHERE loan_id=:loan_id AND status=:active_status';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':outstanding_principal', $reset_value);
            $stmt->bindParam(':outstanding_interest', $reset_value);
            $stmt->bindParam(':loan_id', $loan['loan_no']);
            $stmt->bindParam(':active_status', $active_status);
            $stmt->execute();


            $sqlQuery = ' SELECT * FROM public.loan_schedule WHERE loan_id=:loan_id AND status=:active_status ORDER BY date_of_payment ASC ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':loan_id', $loan['loan_no']);
            $stmt->bindParam(':active_status', $active_status);
            $stmt->execute();
            $schedule_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // var_dump($schedule_records);

            $today_date = strtotime(date('Y-m-d'));
            foreach ($schedule_records as $schedule_record) {
                $schedule_payment_date = strtotime($schedule_record['date_of_payment']);
                if ($schedule_payment_date > $today_date) {
                    $schedule_performance_status = 'post_paid';
                } else if ($schedule_payment_date < $today_date) {
                    $schedule_performance_status = 'late';
                } else {
                    $schedule_performance_status = 'on_time';
                }

                $sqlQuery = 'UPDATE public.loan_schedule SET principal_paid=principal,interest_paid=interest, performance_status=:performance_status, status=:paid_status WHERE schedule_id=:schedule_id';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':schedule_id', $schedule_record['schedule_id']);
                $stmt->bindParam(':performance_status', $schedule_performance_status);
                $stmt->bindParam(':paid_status', $paid_status);
                $stmt->execute();
            }
        }
    }


    public function getLoanCurrentSchedule($loan_id, $active_first = false)
    {
        $loan = $this->getLoanDetails($loan_id);
        // $date_of_next_pay = date('Y-m-d', strtotime($loan['date_of_next_pay']));
        $sqlQuery = 'SELECT * FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=:loan_id AND status=:status  ORDER BY public."loan_schedule".schedule_id ASC';
        $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bindParam(':date_of_payment', $date_of_next_pay);

        $status = 'active';
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':loan_id', $loan_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLoanNextSchedule($loan_id)
    {
        $loan = $this->getLoanDetails($loan_id);
        $date_of_next_pay = date('Y-m-d', strtotime($loan['date_of_next_pay']));
        $sqlQuery = 'SELECT * FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=:loan_id AND status=:status AND DATE(date_of_payment) > :date_of_payment ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':date_of_payment', $date_of_next_pay);

        $status = 'active';
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':loan_id', $loan_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLoanDetails($id)
    {
        $sqlQuery = 'SELECT * FROM public."loan" WHERE public."loan".loan_no=:loan_no ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':loan_no', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLoanClientDetails($id)
    {
        $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."User" ON public."loan".account_id= public."User".id LEFT JOIN public."Client" ON public."loan".account_id= public."Client"."userId" LEFT JOIN public."loantypes" ON public."loan".loanproductid = public."loantypes".type_id WHERE public."loan".loan_no=:loan_no ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':loan_no', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllBanks()
    {
        $sqlQuery = 'SELECT * FROM public."Bank" WHERE bank_deleted=0 ORDER BY "createdAt" ASC';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt;
    }


    public function reactivateClosedLoan()
    {

        // take loan status back to active on time
        $sqlQuery = 'UPDATE public."loan" SET status=2  WHERE loan_no=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->name);

        $stmt->execute();

        // delete all repayments on the loan statement
        $sqlQuery = 'DELETE FROM public."transactions"  WHERE loan_id=:id AND t_type IN(\'L\',\'WLI\',\'WLP\')';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->name);

        $stmt->execute();
        //  update schedule to active 
        $sqlQuery = 'DELETE FROM public."loan_schedule"  WHERE loan_id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->name);

        $stmt->execute();

        // regenerate schedule

        //         $sqlQuery = 'UPDATE public."loan"  SET 
        //     approvedamount=:aa,reviewedbyid=:rbi,status=1,isapproved=true,approved_loan_duration=:ald,
        //     monthly_interest_rate=:mir,notes=:notes,principal=:prr,disbursedamount=:rreb,repay_cycle_id=:rcid
        //  WHERE  public."loan".loan_no=:id';

        //         $stmt = $this->conn->prepare($sqlQuery);
        //         $stmt->bindParam(':id', $this->createdAt);
        //         $stmt->bindParam(':aa', $this->location);
        //         $stmt->bindParam(':rreb', $this->location);
        //         $stmt->bindParam(':prr', $this->location);
        //         $stmt->bindParam(':rbi', $this->serialNumber);
        //         $stmt->bindParam(':ald', $this->name);
        //         $stmt->bindParam(':mir', $this->updatedAt);
        //         $stmt->bindParam(':notes', $this->deletedAt);
        //         $stmt->bindParam(':rcid', $this->loan_id);
        //         $stmt->execute();

        $this->applyLoanSchedule($this->name);


        $this->updateTotalLoanAmount($this->name);


        return true;
    }
    public function deleteBank()
    {
        $sqlQuery = 'UPDATE public."Bank" SET bank_deleted=1  WHERE id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->name);

        $stmt->execute();

        // insert audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Deleted Bank';
        $auditTrail->staff_id = $this->auth_id;
        $auditTrail->bank_id = $this->bank;
        $auditTrail->branch_id = $this->branch;

        $auditTrail->log_message = 'Deleted Bank No. ' . $this->name;

        $auditTrail->create();
        return true;
    }
    public function deleteCashTransfer()
    {
        $sqlQuery = 'DELETE FROM public."transactions"  WHERE tid=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->name);

        $stmt->execute();

        // insert audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Deleted Cash Transfer';
        $auditTrail->staff_id = $this->auth_id;
        $auditTrail->bank_id = $this->bank;
        $auditTrail->branch_id = $this->branch;

        $auditTrail->log_message = 'Deleted Cash Transfer REF. ' . $this->name;

        $auditTrail->create();
        return true;
    }
    public function deleteBranch()
    {
        $sqlQuery = 'UPDATE public."Branch" SET deleted=1  WHERE id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->name);

        $stmt->execute();

        // insert audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Deleted Branch';
        $auditTrail->staff_id = $this->auth_id;
        $auditTrail->bank_id = $this->bank;
        $auditTrail->branch_id = $this->branch;

        $auditTrail->log_message = 'Deleted Branch No. ' . $this->name;

        $auditTrail->create();

        return true;
    }

    public function getAllBranches()
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:bid AND public."Branch".deleted=0 ORDER BY "createdAt" ASC';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchRegions($id)
    {
        $sqlQuery = 'SELECT DISTINCT(lower(subcounty)) AS subcounty FROM public."User" LEFT JOIN public."Client" ON public."User".id= public."Client"."userId" WHERE public."Client"."branchId" =:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankRegions($id)
    {
        $sqlQuery = 'SELECT DISTINCT(lower(subcounty)) AS subcounty FROM public."User" LEFT JOIN public."Client" ON public."User".id= public."Client"."userId" LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Branch"."bankId" =:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankDistricts($id)
    {
        $sqlQuery = 'SELECT DISTINCT(lower(district)) AS district FROM public."User" LEFT JOIN public."Client" ON public."User".id= public."Client"."userId" LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Branch"."bankId" =:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }
    public function getAllBranchDistricts($id)
    {
        $sqlQuery = 'SELECT DISTINCT(lower(district)) AS district FROM public."User" LEFT JOIN public."Client" ON public."User".id= public."Client"."userId" LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Branch".id =:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankVillages($id)
    {
        $sqlQuery = 'SELECT DISTINCT(lower(village)) AS village FROM public."User" LEFT JOIN public."Client" ON public."User".id= public."Client"."userId" LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Branch"."bankId" =:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }
    public function getAllBranchVillages($id)
    {
        $sqlQuery = 'SELECT DISTINCT(lower(village)) AS village FROM public."User" LEFT JOIN public."Client" ON public."User".id= public."Client"."userId" LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Branch".id =:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }
    public function getAllBankParishes($id)
    {
        $sqlQuery = 'SELECT DISTINCT(lower(parish)) AS parish FROM public."User" LEFT JOIN public."Client" ON public."User".id= public."Client"."userId" LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Branch"."bankId" =:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }
    public function getAllBranchParishes($id)
    {
        $sqlQuery = 'SELECT DISTINCT(lower(parish)) AS parish FROM public."User" LEFT JOIN public."Client" ON public."User".id= public."Client"."userId" LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Branch".id =:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchBranches()
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->id);

        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:bid AND public."Branch".deleted=0 ORDER BY "createdAt" ASC';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $row['bankId']);

        $stmt->execute();
        return $stmt;
    }

    public function getAllSystemBranches()
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE public."Branch".deleted=0 ORDER BY "createdAt" ASC';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
        return $stmt;
    }

    public function getBankSavingAccount()
    {
        $sqlQuery = 'SELECT * FROM public."savingaccounts" WHERE bankid=:bid ORDER BY id ASC';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        $stmt->execute();
        return $stmt;
    }

    public function getAccountClients($id)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."Client" WHERE actype=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
    }
    public function getAccountClientsToday($id)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."Client" WHERE actype=:bid AND DATE("createdAt")=CURRENT_DATE';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
    }
    public function getBranchSavingAccounts()
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->branch);

        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQueryn = 'SELECT * FROM public."savingaccounts" WHERE bankId=:id ORDER BY id ASC';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $row['bankId']);

        $stmtn->execute();
        return $stmtn;
    }

    public function getGroupMembers($id)
    {
        $sqlQueryn = 'SELECT * FROM public."group_members" WHERE gm_uid=:id ORDER BY gmid ASC';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $id);

        $stmtn->execute();
        return $stmtn;
    }

    public function getLoanAttachments($id)
    {
        $sqlQueryn = 'SELECT * FROM public."loan_attachments" WHERE loan_id=:id ORDER BY attach_id ASC';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $id);

        $stmtn->execute();
        return $stmtn;
    }

    public function getBranchClients()
    {
        $status = 'ACTIVE';
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->branch);

        $stmtn->execute();
        $row = $stmtn->fetch();

        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt,public."Client".id AS cid FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
        LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid';

        if ($this->with_active_status) {
            $sqlQuery .= ' AND public."User".status=:st  ';
        }

        if ($this->client_type_section) {
            $sqlQuery .= ' AND public."Client".client_type=:client_type_section  ';
        }

        $sqlQuery .= ' ORDER BY public."Client"."createdAt" ASC ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $row['bankId']);

        if ($this->with_active_status) {
            $stmt->bindParam(':st', $status);
        }

        if ($this->client_type_section) {
            $stmt->bindParam(':client_type_section', $this->client_type_section);
        }

        $stmt->execute();
        return $stmt;
    }

    public function getBranchClients2()
    {
        $status = 'ACTIVE';
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->branch);

        $stmtn->execute();
        $row = $stmtn->fetch();

        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt,public."Client".id AS cid FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
        LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid ';

        if ($this->with_active_status) {
            $sqlQuery .= ' AND public."User".status=:st  ';
        }

        if ($this->client_type_section) {
            $sqlQuery .= ' AND public."Client".client_type=:client_type_section  ';
        }

        $sqlQuery .= ' ORDER BY public."User".id DESC LIMIT 10000 ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $row['bankId']);

        if ($this->with_active_status) {
            $stmt->bindParam(':st', $status);
        }

        if ($this->client_type_section) {
            $stmt->bindParam(':client_type_section', $this->client_type_section);
        }

        $stmt->execute();
        return $stmt;
    }


    public function getBranchClientsDeactivated()
    {
        $status = 'INACTIVE';
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $this->branch);

        $stmtn->execute();
        $row = $stmtn->fetch();

        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt,public."Client".id AS cid FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
        LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid ';

        if ($this->with_active_status) {
            $sqlQuery .= ' AND public."User".status=:st  ';
        }

        // if ($this->client_type_section) {
        //     $sqlQuery .= ' AND public."Client".client_type=:client_type_section  ';
        // }

        $sqlQuery .= ' ORDER BY public."User".id DESC LIMIT 10000 ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $row['bankId']);

        if ($this->with_active_status) {
            $stmt->bindParam(':st', $status);
        }

        // if ($this->client_type_section) {
        //     $stmt->bindParam(':client_type_section', $this->client_type_section);
        // }

        $stmt->execute();
        return $stmt;
    }
    public function getBankId($bid)
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $bid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['bankId'] ?? '';
    }

    public function getBankSectors($bid)
    {
        $sqlQuery = 'SELECT * FROM public."occupation_sector" WHERE bankid=:bid OR bankid IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $bid);

        $stmt->execute();

        return $stmt;
    }

    public function getBankCats($bid)
    {
        $sqlQuery = 'SELECT * FROM public."occupation_sub_categories" WHERE bankid=:bid OR bankid IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $bid);

        $stmt->execute();
        // $row = $stmt->fetch();

        return $stmt;
    }

    public function getBankSubCats($bid)
    {
        $sqlQuery = 'SELECT * FROM public."occupation_categories" WHERE bankid=:bid OR bankid IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $bid);

        $stmt->execute();
        // $row = $stmt->fetch();

        return $stmt;
    }

    public function getBranchSectors($bid)
    {

        $sqlQuery = 'SELECT * FROM public."occupation_sector" LEFT JOIN public."Branch" ON  public."Branch"."bankId"=public."occupation_sector".bankid WHERE public."Branch".id=:bid OR public."occupation_sector".bankid IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $bid);

        $stmt->execute();
        // $row = $stmt->fetch();

        return $stmt;
    }

    public function getBranchCats($bid)
    {

        $sqlQuery = 'SELECT * FROM public."occupation_sub_categories" LEFT JOIN public."Branch" ON  public."Branch"."bankId"=public."occupation_sub_categories".bankid WHERE public."Branch".id=:bid OR public."occupation_sub_categories".bankid IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $bid);

        $stmt->execute();
        // $row = $stmt->fetch();

        return $stmt;
    }

    public function getBranchSubCats($bid)
    {
        $sqlQuery = 'SELECT * FROM public."occupation_categories" LEFT JOIN public."Branch" ON  public."Branch"."bankId"=public."occupation_categories".bankid WHERE public."Branch".id=:bid OR public."occupation_categories".bankid IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $bid);

        $stmt->execute();
        // $row = $stmt->fetch();

        return $stmt;
    }

    public function getBankClientsDatatable()
    {
        $dataTableSearchHelper = new DatatableSearchHelper();

        $binding_array = [];
        $search_string_array = [];
        // $search_string = "";
        $search_string = $this->filter_search_string;

        /**
         * Split search string if found
         */
        // if ($this->filter_search_string) {
        // $search_string_array = StringToArray($search_string);
        // }
        $selected_branch = null;
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bank = $result['bankId'];
        }

        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt , public."Branch"."name" AS branch_name, public."Branch"."location" AS branch_location, public."User".shared_name AS shared_name, public.savingaccounts.name AS save_name FROM public."Client"
        LEFT JOIN public."User" ON public."User".id = public."Client"."userId"  
        LEFT JOIN public."savingaccounts" ON public."savingaccounts".id = public."Client".actype  
        LEFT JOIN public."Branch" ON public."Branch".id = public."Client"."branchId" ';

        if (@$this->bank) {
            $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        }


        if (!@$search_string) {
            $sqlQuery .= ' WHERE public."Bank".id=:bank_id AND';
        } else {
            $sqlQuery .= " WHERE ";
        }

        $sqlQuery .= ' EXISTS (SELECT 1 FROM public."User" WHERE public."User".id = public."Client"."userId") ';

        if (@$this->branch) {
            $sqlQuery .= ' AND public."Branch"."bankId" = :branch ';
            $binding_array[':branch'] = $selected_branch['bankId'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND public."Client"."branchId" = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        /**
         * filter by date
         */
        if ($this->filter_start_date && $this->filter_end_date) {
            $sqlQuery .= ' AND DATE(public."Client"."createdAt") >= :from_date AND DATE(public."Client"."createdAt") <= :end_date ';
            $binding_array[':from_date'] = $this->filter_start_date;
            $binding_array[':end_date'] = $this->filter_end_date;
        }

        /**
         * filter by gender
         */
        if (@$this->filter_gender) {
            $sqlQuery .= ' AND public."User".gender = :gender ';
            $binding_array[':gender'] = $this->filter_gender;
        }

        /**
         * filter by entered by -- staff
         */
        if (@$this->auth_id) {
            $sqlQuery .= ' AND public."User".entered_by = :entr ';
            $binding_array[':entr'] = $this->auth_id;
        }

        /**
         * filter by account type
         */
        if (@$this->filter_actype) {
            $sqlQuery .= ' AND public."Client".actype =:actype ';
            $binding_array[':actype'] = $this->filter_actype;
        }

        /**
         * fitler by client type
         */
        if (@$this->filter_client_type) {
            if ($this->filter_client_type == "member") {
                $MemberShipUserId = 0;
                $sqlQuery .= ' AND public."Client"."membership_no" > :MemberShipUserId ';
            } else {
                $MemberShipUserId = 0;
                $sqlQuery .= 'AND public."Client"."membership_no" = :MemberShipUserId ';
            }
            $binding_array[':MemberShipUserId'] = $MemberShipUserId;
        }

        if ($this->client_type_section) {
            $sqlQuery .= ' AND public."Client".client_type = :client_type_section ';
            $binding_array[':client_type_section'] = $this->client_type_section;
        }

        if ($this->filter_saving_officer) {
            $sqlQuery .= ' AND public."Client".savings_officer = :client_type_section ';
            $binding_array[':client_type_section'] = $this->filter_saving_officer;
        }

        /**
         * Handle/Filter client related data while user is searching
         */
        $clientSearch = $dataTableSearchHelper->search_client($this->filter_search_string);
        $sqlQuery .= $clientSearch['query'];
        $binding_array = array_merge($binding_array, $clientSearch['binding_array']);


        $binding_array[':bank_id'] = $this->bank;

        $sqlQuery .= ' ORDER BY public."Client"."id" desc ';

        /**
         * paginate datatables
         */
        $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
        $binding_array[':limit'] = $this->filter_per_page;
        $binding_array[':offset'] = $this->filter_page;


        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($binding_array);
        return $stmt;
    }


    public function getBankClientsDatatableDeactivated()
    {
        $dataTableSearchHelper = new DatatableSearchHelper();

        $binding_array = [];
        $search_string_array = [];
        // $search_string = "";
        $search_string = $this->filter_search_string;

        /**
         * Split search string if found
         */
        // if ($this->filter_search_string) {
        // $search_string_array = StringToArray($search_string);
        // }
        $selected_branch = null;
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bank = $result['bankId'];
        }

        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt , public."Branch"."name" AS branch_name, public."Branch"."location" AS branch_location, public."User".shared_name AS shared_name, public.savingaccounts.name AS save_name FROM public."Client"
        LEFT JOIN public."User" ON public."User".id = public."Client"."userId"  
        LEFT JOIN public."savingaccounts" ON public."savingaccounts".id = public."Client".actype  
        LEFT JOIN public."Branch" ON public."Branch".id = public."Client"."branchId" ';

        if (@$this->bank) {
            $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        }


        if (!@$search_string) {
            $sqlQuery .= ' WHERE public."Bank".id=:bank_id AND public."User".status=\'INACTIVE\' AND ';
        } else {
            $sqlQuery .= ' WHERE  public."User".status=\'INACTIVE\' AND  ';
        }

        $sqlQuery .= ' EXISTS (SELECT 1 FROM public."User" WHERE public."User".id = public."Client"."userId") ';

        if (@$this->branch) {
            $sqlQuery .= ' AND public."Branch"."bankId" = :branch ';
            $binding_array[':branch'] = $selected_branch['bankId'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND public."Client"."branchId" = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        /**
         * filter by date
         */
        if ($this->filter_start_date && $this->filter_end_date) {
            $sqlQuery .= ' AND DATE(public."Client"."createdAt") >= :from_date AND DATE(public."Client"."createdAt") <= :end_date ';
            $binding_array[':from_date'] = $this->filter_start_date;
            $binding_array[':end_date'] = $this->filter_end_date;
        }

        /**
         * filter by gender
         */
        if (@$this->filter_gender) {
            $sqlQuery .= ' AND public."User".gender = :gender ';
            $binding_array[':gender'] = $this->filter_gender;
        }

        /**
         * filter by entered by -- staff
         */
        if (@$this->auth_id) {
            $sqlQuery .= ' AND public."User".entered_by = :entr ';
            $binding_array[':entr'] = $this->auth_id;
        }

        /**
         * filter by account type
         */
        if (@$this->filter_actype) {
            $sqlQuery .= ' AND public."Client".actype =:actype ';
            $binding_array[':actype'] = $this->filter_actype;
        }

        /**
         * fitler by client type
         */
        if (@$this->filter_client_type) {
            if ($this->filter_client_type == "member") {
                $MemberShipUserId = 0;
                $sqlQuery .= ' AND public."Client"."membership_no" > :MemberShipUserId ';
            } else {
                $MemberShipUserId = 0;
                $sqlQuery .= 'AND public."Client"."membership_no" = :MemberShipUserId ';
            }
            $binding_array[':MemberShipUserId'] = $MemberShipUserId;
        }

        // if ($this->client_type_section) {
        //     $sqlQuery .= ' AND public."Client".client_type = :client_type_section ';
        //     $binding_array[':client_type_section'] = $this->client_type_section;
        // }

        /**
         * Handle/Filter client related data while user is searching
         */
        $clientSearch = $dataTableSearchHelper->search_client($this->filter_search_string);
        $sqlQuery .= $clientSearch['query'];
        $binding_array = array_merge($binding_array, $clientSearch['binding_array']);


        $binding_array[':bank_id'] = $this->bank;

        $sqlQuery .= ' ORDER BY public."Client"."id" desc ';

        /**
         * paginate datatables
         */
        $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
        $binding_array[':limit'] = $this->filter_per_page;
        $binding_array[':offset'] = $this->filter_page;


        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($binding_array);
        return $stmt;
    }
    public function getBankClients()
    {
        $status = 'ACTIVE';
        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
         LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid';

        if ($this->with_active_status) {
            $sqlQuery .= ' AND public."User".status=:st  ';
        }

        if ($this->client_type_section) {
            $sqlQuery .= ' AND public."Client".client_type=:client_type_section  ';
        }

        $sqlQuery .= ' ORDER BY public."Client"."createdAt" ASC ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        if ($this->with_active_status) {
            $stmt->bindParam(':st', $status);
        }

        if ($this->client_type_section) {
            $stmt->bindParam(':client_type_section', $this->client_type_section);
        }

        $stmt->execute();
        return $stmt;
    }


    public function getBankClients2()
    {
        $status = 'ACTIVE';
        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
         LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid ';

        if ($this->with_active_status) {
            $sqlQuery .= ' AND public."User".status=:st  ';
        }

        if ($this->client_type_section) {
            $sqlQuery .= ' AND public."Client".client_type=:client_type_section  ';
        }

        $sqlQuery .= ' ORDER BY public."User".id DESC LIMIT 10000 ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        if ($this->with_active_status) {
            $stmt->bindParam(':st', $status);
        }

        if ($this->client_type_section) {
            $stmt->bindParam(':client_type_section', $this->client_type_section);
        }

        $stmt->execute();
        return $stmt;
    }


    public function getBankClientsDeactivated()
    {
        $status = 'INACTIVE';
        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
         LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE public."Branch"."bankId"=:bid ';

        if ($this->with_active_status) {
            $sqlQuery .= ' AND public."User".status=:st  ';
        }

        // if ($this->client_type_section) {
        //     $sqlQuery .= ' AND public."Client".client_type=:client_type_section  ';
        // }

        $sqlQuery .= ' ORDER BY public."User".id DESC LIMIT 10000 ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->bank);

        if ($this->with_active_status) {
            $stmt->bindParam(':st', $status);
        }

        // if ($this->client_type_section) {
        //     $stmt->bindParam(':client_type_section', $this->client_type_section);
        // }

        $stmt->execute();
        return $stmt;
    }

    public function getClientDetails($id)
    {
        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
         LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."share_register" ON public."share_register".userid=public."User".id
         WHERE public."Client"."userId"=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        return $stmt;
    }

    public function getClientDetailswithCID($id)
    {
        $sqlQuery = 'SELECT *,public."Client"."createdAt" AS ccreatedAt FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
         LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."share_register" ON public."share_register".userid=public."User".id
         WHERE public."Client".id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        return $stmt;
    }

    public function updateIndividualAttachments($uid, $other_name, $pass_name, $sign_name, $fing_name)
    {
        // update attachment names

        $sqlQuery = 'UPDATE public."User" SET "profilePhoto"=:pp,"sign"=:sign,"other_attachments"=:oa,fingerprint=:fing  WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':pp', $pass_name);
        $stmt->bindParam(':sign', $sign_name);
        $stmt->bindParam(':oa', $other_name);
        $stmt->bindParam(':fing', $fing_name);
        $stmt->bindParam(':id', $uid);
        $stmt->execute();

        return true;
    }

    public function updateGroupAttachments($uid, $other_name, $pass_name, $sign_name, $fing_name, $fing2, $fing3)
    {
        // update attachment names

        $sqlQuery = 'UPDATE public."User" SET "profilePhoto"=:pp,"sign"=:sign,"other_attachments"=:oa,fingerprint=:fing,fingerprint_2=:fing2,fingerprint_3=:fing3  WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':pp', $pass_name);
        $stmt->bindParam(':sign', $sign_name);
        $stmt->bindParam(':oa', $other_name);
        $stmt->bindParam(':fing', $fing_name);
        $stmt->bindParam(':fing2', $fing2);
        $stmt->bindParam(':fing3', $fing3);
        $stmt->bindParam(':id', $uid);
        $stmt->execute();

        return true;
    }

    public function getAllBankShareDetails($bank)
    {
        $sqlQuery = 'SELECT COUNT(*) AS countern,SUM(COALESCE(no_shares,0)) AS counterr,SUM(COALESCE(share_amount,0)) AS counteramount,SUM(COALESCE(savings_dividends,0)) AS countersavings,SUM(COALESCE(shares_dividends,0)) AS countershares FROM public."share_register" LEFT JOIN public."Branch" ON public."share_register".branch_id=public."Branch".id WHERE public."Branch"."bankId"=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $bank);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchShareDetails($branch)
    {
        $sqlQuery = 'SELECT  COUNT(*) AS countern,SUM(COALESCE(no_shares,0)) AS counterr,SUM(COALESCE(share_amount,0)) AS counteramount,SUM(COALESCE(savings_dividends,0)) AS countersavings,SUM(COALESCE(shares_dividends,0)) AS countershares FROM public."share_register"  WHERE public."share_register".branch_id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $branch);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchFDDetails($branch)
    {
        $sqlQuery = 'SELECT  COUNT(*) AS countern,SUM(COALESCE(fd_amount,0)) AS counterr,SUM(COALESCE(fd_int_due,0)) AS counteramount,SUM(COALESCE(fd_int_paid,0)) AS countersavings,SUM(COALESCE(wht_due,0)) AS countershares, SUM(COALESCE(wht_paid,0)) AS countersharesX FROM public."fixed_deposits"  WHERE public."fixed_deposits".fd_branch=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $branch);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankFDDetails($bank)
    {
        $sqlQuery = 'SELECT  COUNT(*) AS countern,SUM(COALESCE(fd_amount,0)) AS counterr,SUM(COALESCE(fd_int_due,0)) AS counteramount,SUM(COALESCE(fd_int_paid,0)) AS countersavings,SUM(COALESCE(wht_due,0)) AS countershares, SUM(COALESCE(wht_paid,0)) AS countersharesX FROM public."fixed_deposits" LEFT JOIN public."Branch" ON public."fixed_deposits".fd_branch=public."Branch".id WHERE public."Branch"."bankId"=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $bank);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        return $stmt;
    }

    public function getBankSavingAccountName($id)
    {
        $sqlQuery = 'SELECT name FROM public."savingaccounts" 
         WHERE id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'];
    }

    public function getBankSavingMinBalance($id)
    {
        $sqlQuery = 'SELECT min_balance FROM public."savingaccounts" 
         WHERE id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['min_balance'] ?? 0;
    }

    public function getClientFixedDepositTotal($id)
    {
        $sqlQuery = 'SELECT SUM(fd_amount) AS tot FROM public."fixed_deposits" 
         WHERE fd_status=0 AND user_id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'] ?? 0;
    }

    public function getSavingsOfficerName($id)
    {
        $sqlQuery = 'SELECT * FROM public."User" 
         WHERE id=:bid ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['firstName'] . ' ' . $row['lastName'];
    }

    public function getLastUserTransaction($id)
    {
        $sqlQuery = 'SELECT * FROM public."transactions" 
         WHERE mid=:bid ORDER BY tid DESC LIMIT 1';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);
        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) {
            return date('Y-m-d', strtotime($row['date_created'])) . '  | ( UGX: ' . number_format($row['amount']) . ' - ' . $row['t_type'] . ' )';
        }
        return 'Not Transaction yet';
    }

    public function getTotalBankBranches($id)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."Branch" WHERE "bankId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
    }

    public function getTotalBankStaffs($id)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."Staff" WHERE "bankId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
    }

    public function getTotalBankClients($id)
    {
        $mytot = 0;
        $sqlQueryn = 'SELECT * FROM public."Branch" WHERE "bankId"=:id ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $id);

        $stmtn->execute();
        foreach ($stmtn as $row) {
            $sqlQuery = 'SELECT COUNT(*) AS total FROM public."Client" WHERE public."Client"."branchId"=:bid ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':bid', $row['id']);

            $stmt->execute();
            $row = $stmt->fetch();
            $mytot = $mytot + $row['total'];
        }

        return $mytot;
    }

    public function getBankAdminId($id)
    {

        $sqlQueryn = 'SELECT * FROM public."Staff" WHERE "bankId"=:id ORDER BY "createdAt" ASC LIMIT 1';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $id);

        $stmtn->execute();
        $row = $stmtn->fetch();

        return $row['userId'] ?? 0;
    }

    public function getTotalBranchStaffs($id)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."Staff" WHERE "branchId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
    }
    public function getAllBankAdmins()
    {
        $sqlQuery = 'SELECT *,public."Staff".id AS idx, public."Staff"."createdAt" AS screatedat FROM public."Staff" LEFT JOIN public."User" ON public."Staff"."userId"=public."User".id WHERE public."Staff".is_admin=true';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
        return $stmt;
    }
    public function getBankName($id)
    {
        $sqlQuery = 'SELECT name,location FROM public."Bank"  WHERE public."Bank".id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'];
    }

    public function getAllBankStaffs()
    {
        $sqlQuery = 'SELECT *,public."Staff".id AS idx, public."User".id AS suserid,public."User".status AS sstatus FROM public."Staff" LEFT JOIN public."User" ON public."Staff"."userId"=public."User".id
        LEFT JOIN public."Branch" ON public."Staff"."branchId" = public."Branch".id
         WHERE public."Staff"."bankId"=:bid OR public."Branch"."bankId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBankAgents()
    {
        $sqlQuery = 'SELECT *,public."Staff".id AS idx, public."User".id AS suserid,public."User".status AS sstatus FROM public."Staff" LEFT JOIN public."User" ON public."Staff"."userId"=public."User".id
        LEFT JOIN public."Branch" ON public."Staff"."branchId" = public."Branch".id
         WHERE public."Staff"."roleId" IN (\'4bbcbdc7-1902-4c1f-abb1-d2de53d1df99\',\'fe209aae-0c0d-46f2-ba0b-be5f50da8519\',\'32f187f5-38a4-42d9-a4fa-cf92b45356bb\',\'f50a502f-d5ec-4824-964c-212022aa7e36\',\'0c43f7eb-bce9-4a89-b569-19a947bcf62d\',\'b2b12075-af76-4a6a-8951-7288e5595d01\',\'fdef39e7-dc04-43b4-bfc4-3a6f004a1234\') AND public."Branch"."bankId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $rid = '4bbcbdc7-1902-4c1f-abb1-d2de53d1df99';
        // $stmt->bindParam(':rid', $rid);
        $stmt->bindParam(':bid', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchStaffs()
    {
        $sqlQuery = 'SELECT *,public."Staff".id AS idx,public."User".id AS suserid,public."User".status AS sstatus FROM public."Staff" LEFT JOIN public."User" ON public."Staff"."userId"=public."User".id WHERE public."Staff"."branchId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->id);

        $stmt->execute();
        return $stmt;
    }

    public function getAllBranchStaffs2()
    {

        $sqlQuery = 'SELECT * FROM "Branch" WHERE id=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $this->id);
        $stmt->execute();
        $row = $stmt->fetch();

        $sqlQuery = 'SELECT *,public."Staff".id AS idx, public."User".id AS suserid,public."User".status AS sstatus FROM public."Staff" LEFT JOIN public."User" ON public."Staff"."userId"=public."User".id
        LEFT JOIN public."Branch" ON public."Staff"."branchId" = public."Branch".id
         WHERE public."Staff"."bankId"=:bid OR public."Branch"."bankId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $row['bankId']);

        $stmt->execute();
        return $stmt;
    }
    public function getAgentTotalLoanRepays($uid)
    {
        $sqlQuery = 'SELECT SUM(agent_loan_amount) AS tot FROM public."transactions"  WHERE public."transactions"._authorizedby=:bid AND _status=0';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }
    public function getAgentTotalDeposits($uid)
    {
        $sqlQuery = 'SELECT SUM(amount) AS tot FROM public."transactions"  WHERE public."transactions"._authorizedby=:bid AND _status=0';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }

    public function getAgentCountDeposits($uid)
    {
        $sqlQuery = 'SELECT COUNT(DISTINCT(mid)) AS tot FROM public."transactions"  WHERE public."transactions"._authorizedby=:bid AND _status=0';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }

    public function getAgentActiveMembersToday($uid)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."User"  WHERE  entered_by=:idd AND DATE("createdAt") = current_date';
        $stmt = $this->conn->prepare($sqlQuery);

        // public."User".status=:bid AND
        $st = 'ACTIVE';
        // $stmt->bindParam(':bid', $st);
        $stmt->bindParam(':idd', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }

    public function getAgentMembershipCommision($uid)
    {
        $sqlQuery = 'SELECT SUM(amount) AS tot FROM public."transactions"  WHERE t_type=:tt AND  _authorizedby=:idd AND DATE(date_created) = current_date';
        $stmt = $this->conn->prepare($sqlQuery);

        // public."User".status=:bid AND
        $st = 'R';
        $stmt->bindParam(':tt', $st);
        $stmt->bindParam(':idd', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }

    public function getAllBranchAgents()
    {
        $sqlQuery = 'SELECT *,public."Staff".id AS idx,public."User".id AS suserid,public."User".status AS sstatus FROM public."Staff" LEFT JOIN public."User" ON public."Staff"."userId"=public."User".id WHERE public."Staff"."branchId"=:bid AND public."Staff"."roleId" IN (\'4bbcbdc7-1902-4c1f-abb1-d2de53d1df99\',\'fe209aae-0c0d-46f2-ba0b-be5f50da8519\',\'32f187f5-38a4-42d9-a4fa-cf92b45356bb\',\'f50a502f-d5ec-4824-964c-212022aa7e36\',\'0c43f7eb-bce9-4a89-b569-19a947bcf62d\',\'b2b12075-af76-4a6a-8951-7288e5595d01\')';
        $stmt = $this->conn->prepare($sqlQuery);
        $rid = '4bbcbdc7-1902-4c1f-abb1-d2de53d1df99';
        $stmt->bindParam(':bid', $this->id);
        // $stmt->bindParam(':rid', $rid);

        $stmt->execute();
        return $stmt;
    }
    public function getTotalBranchClientsToday($id)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."Client"  WHERE "branchId"=:bid AND DATE("createdAt")=CURRENT_DATE';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
    }

    public function getTotalBranchClients($id)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."Client"  WHERE "branchId"=:bid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $id);

        $stmt->execute();
        $row = $stmt->fetch();
        return $row['tot'];
    }

    public function getAllBankRoles()
    {
        $sqlQuery = 'SELECT *,public."Role".name AS rname,public."Role".id AS rid FROM public."Role" LEFT JOIN public."Branch" ON public."Role"."branchId"=public."Branch".id WHERE public."Role"."bankId"=:id OR public."Branch"."bankId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);


        $stmt->execute();
        return $stmt;
    }

    public function getBranchName($id)
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'] . ' - ' . $row['location'];
    }
    public function getBranchName2($id)
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'];
    }

    public function getBranchDetails($id = null)
    {
        $id = $id ?? $this->branchId;
        $sqlQuery = ' SELECT branch.id AS branch_id, branch.name AS branch_name, branch.bcode AS branch_code, bank.id AS bank_id, bank.name AS bank_name, branch."serialNumber", branch.location, branch.sms_balance, branch.sms_purchase_count, branch.sms_used_count, branch.sms_amount_loaded, branch.sms_amount_spent 
        FROM public."Branch" AS branch 
        INNER JOIN public."Bank" bank ON bank.id=branch."bankId" WHERE branch.id=:branch_id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':branch_id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllBankDetails($id)
    {
        $sqlQuery = 'SELECT * FROM public."Bank" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        return $stmt;
    }

    public function getDayStatus($id, $day)
    {
        $sqlQuery = 'SELECT COUNT(*) AS num FROM public."bankcloseddays" WHERE dayname=:dayy AND bankid=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':dayy', $day);


        $stmt->execute();
        $row = $stmt->fetch();
        if ($row['num'] > 0) {
            return 1;
        }
        return 0;
    }
    public function getHolidayStatus($id, $day)
    {
        $sqlQuery = 'SELECT COUNT(*) AS num FROM public."bankclosedholidays" WHERE daydate=:dayy AND bankid=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':dayy', $day);


        $stmt->execute();
        $row = $stmt->fetch();
        if ($row['num'] > 0) {
            return 1;
        }
        return 0;
    }

    public function openDay($id, $day)
    {
        $sqlQuery = 'DELETE FROM public."bankcloseddays" WHERE dayname=:dayy AND bankid=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':dayy', $day);


        $stmt->execute();

        return true;
    }

    public function openHoliday($id, $day)
    {
        $sqlQuery = 'DELETE FROM public."bankclosedholidays" WHERE daydate=:dayy AND bankid=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':dayy', $day);


        $stmt->execute();

        return true;
    }

    public function closeDay($id, $day)
    {
        $sqlQuery = 'INSERT INTO public."bankcloseddays"(dayname,bankid) VALUES(:dayy,:id)';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':dayy', $day);


        $stmt->execute();

        return true;
    }

    public function closeHoliday($id, $day)
    {
        $sqlQuery = 'INSERT INTO public."bankclosedholidays"(dayname,daydate,bankid) VALUES(:dname,:dayy,:id)';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':dayy', $day);
        $stmt->bindParam(':dname', $day);


        $stmt->execute();

        return true;
    }

    public function getAllBankSenderIDS($id)
    {
        $sqlQuery = 'SELECT * FROM public."senderids" WHERE bankid=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();

        return $stmt;
    }

    public function getAllBranchSenderIDS($id)
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();

        $sqlQuery = 'SELECT * FROM public."senderids" WHERE bankid=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $row['bankId']);


        $stmt->execute();

        return $stmt;
    }

    public function updateBankDetails()
    {
        $sqlQuery = 'UPDATE public."Bank"
        SET  name=:name, location=:location,bankmail=:mail, bankcontacts=:contact, logo=:logo,trade_name=:tname
        WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->bank);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':location', $this->description);
        $stmt->bindParam(':mail', $this->branch);
        $stmt->bindParam(':contact', $this->updatedAt);
        $stmt->bindParam(':logo', $this->createdAt);
        $stmt->bindParam(':tname', $this->pv);


        $stmt->execute();

        return true;
    }

    public function updateBankSMSStatus()
    {

        if ($this->location == 'main') {
            $sqlQuery = 'UPDATE public."Bank"
        SET  sms_sub_status=:st
        WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->bank);
            $stmt->bindParam(':st', $this->name);


            $stmt->execute();
            return true;
        } else if ($this->location == 'manual') {
            $sqlQuery = 'UPDATE public."Bank"
        SET  manual_sms_status=:st
        WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->bank);
            $stmt->bindParam(':st', $this->name);


            $stmt->execute();
            return true;
        }


        return false;
    }

    public function updateBankSMSStatusbyCBSAdmin()
    {

        if ($this->location == 'main') {
            $sqlQuery = 'UPDATE public."Bank"
        SET  sms_sub_status=:st
        WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->bank);
            $stmt->bindParam(':st', $this->name);


            $stmt->execute();

            $free_total_amount = 3000;
            $free_total_sms = 100;


            if ($this->name == 1) {
                //  get all the existing bank branches
                $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:id';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $this->bank);
                $stmt->execute();

                $tott = $stmt->rowCount();

                $sms_counter = (int) $free_total_sms / $tott;
                $amount_counter = (int) $free_total_amount / $tott;

                foreach ($stmt as $row) {
                    // distribute the free sms
                    $sqlQuery = 'UPDATE public."Branch"
        SET  sms_balance=sms_balance+:st,sms_purchase_count=sms_purchase_count+:count,sms_amount_loaded=sms_amount_loaded+:st
        WHERE id=:id';

                    $stmt = $this->conn->prepare($sqlQuery);


                    $stmt->bindParam(':id', $row['id']);
                    $stmt->bindParam(':st', $amount_counter);
                    $stmt->bindParam(':count', $sms_counter);


                    $stmt->execute();

                    // create income account for the sms charges 
                    $sqlQuery = 'INSERT INTO public."Account"(
     type, "branchId",name, description, "isSystemGenerated")
    VALUES (:typee,:bid,:nname,:descr,:isgen )';
                    $atype = 'INCOMES';
                    $nname =  'SMS CHARGES - INCOME ACCOUNT';
                    $descr = 'This account holds sms charges income collected from all sms sent';
                    $isgen = true;
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':typee', $atype);
                    $stmt->bindParam(':bid', $row['id']);
                    $stmt->bindParam(':nname', $nname);
                    $stmt->bindParam(':descr', $descr);
                    $stmt->bindParam(':isgen', $isgen);

                    $stmt->execute();

                    // create expense account for the sms charges 
                    $sqlQuery = 'INSERT INTO public."Account"(
     type, "branchId",name, description, "isSystemGenerated")
    VALUES (:typee,:bid,:nname,:descr,:isgen )';
                    $atype = 'EXPENSES';
                    $nname = 'SMS CHARGES - EXPENSE ACCOUNT';
                    $descr = 'This account holds sms charges paid to service provider ';
                    $isgen = true;
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':typee', $atype);
                    $stmt->bindParam(':bid', $row['id']);
                    $stmt->bindParam(':nname', $nname);
                    $stmt->bindParam(':descr', $descr);
                    $stmt->bindParam(':isgen', $isgen);

                    $stmt->execute();
                }
                return true;
            } else {
                //  get all the existing bank branches
                $sqlQuery = 'SELECT * FROM public."Branch" WHERE "bankId"=:id';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $this->bank);
                $stmt->execute();

                $tott = $stmt->rowCount();

                $sms_counter = $free_total_sms / $tott;
                $amount_counter = $free_total_amount / $tott;

                foreach ($stmt as $row) {
                    // distribute the free sms
                    $sqlQuery = 'UPDATE public."Branch"
        SET  sms_balance=sms_balance+:st,sms_purchase_count=sms_purchase_count+:count,sms_amount_loaded=sms_amount_loaded+:st
        WHERE id=:id';

                    $stmt = $this->conn->prepare($sqlQuery);


                    $stmt->bindParam(':id', $row['id']);
                    $stmt->bindParam(':st', $amount_counter);
                    $stmt->bindParam(':count', $sms_counter);


                    $stmt->execute();
                }
                return true;
            }


            return true;
        }


        return false;
    }


    public function getRoleSubPermissions($id)
    {
        $sqlQuery = 'SELECT * FROM public."PermissionRole" LEFT JOIN public."subpermissions" ON public."PermissionRole"."permissionId"=public."subpermissions".pid WHERE "roleId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        return $stmt;
    }

    public function getAllMainPermissions()
    {
        $sqlQuery = 'SELECT * FROM public."mainpermissions" ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt;
    }

    public function getAllSubPermissions()
    {
        $sqlQuery = 'SELECT * FROM public."subpermissions" WHERE mid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }



    //  loan schedule generating functions
    public function initializeLoanSchedule($loan_id)
    {
        // delete the existing loan schedule along side this loan
        $sqlQuery = 'DELETE FROM public."loan_schedule" WHERE loan_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $loan_id);

        $stmt->execute();


        // get the loan details from the loan table

        $sqlQueryn = 'SELECT * FROM public."loan" WHERE loan_no=:id';

        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $loan_id);

        $stmtn->execute();
        $this->loan = $stmtn->fetch();
    }

    public function rescheduleActiveLoans()
    {
        // $loans = $this->dbHandlerMysqli->QueryArray("loan", "*", " status=2 ");
        $sqlQueryn = 'SELECT * FROM public."loan" WHERE status=2';

        $stmtn = $this->conn->prepare($sqlQueryn);

        $stmtn->execute();
        foreach ($stmtn as $loan) {
            $this->applyLoanSchedule($loan['loan_no']);
        }
    }

    public function applyLoanSchedule($loan_id = null)
    {
        if (@$loan_id) {
            $this->loan_id = $loan_id;
            $this->initializeLoanSchedule($loan_id);
            if ($this->loan != null) {
                return $this->saveSchedule($this->loan['interest_method_id']);
            }
        }
        return false;
    }

    private function getRightSchedule($rate, $period, $amount, $date, $method, $grace_period, $frequency, $ftype, $grace_type, $refine)
    {
        // var_dump($amount);

        $endpoint = BACKEND_BASE_URL . "Bank/loan_schedule.php";

        $url =  $endpoint;

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

        // var_dump($data);
        return $data;
    }

    private function saveSchedule($interestMethod)
    {
        // var_dump($this->loan['approved_loan_duration']);
        // start
        $principal =  $this->loan['principal'];
        $duration = $this->loan['approved_loan_duration'];
        $cycle = $this->loan['repay_cycle_id'];
        $date_first = $this->loan['date_of_first_pay'];
        $rate = $this->loan['monthly_interest_rate'];
        $grace_period = 0;

        $grace_type = $this->loan['penalty_grace_type'] ?? 'pay_none';
        $refine = 1;
        $method = '';
        if ($interestMethod == 1) {
            $method = 'flat';
        } else if ($interestMethod == 2) {

            $method = 'declining';
        } else if ($interestMethod == 3) {

            $method = 'amortization';
        }

        if ($cycle == 1) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 1 days'));
            $frequency = 'd';
            $ftype = 'DAYS';
            $grace_period = $this->loan['num_grace_periods'] ?? 0;
        } else if ($cycle == 2) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 7 days'));
            $frequency = 'w';
            $ftype = 'WEEKS';
            $grace_period = ($this->loan['num_grace_periods'] ?? 0) / 7;
        } else if ($cycle == 3) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 30 days'));
            $frequency = 'm';
            $ftype = 'MONTHS';
            $grace_period = ($this->loan['num_grace_periods'] ?? 0) / 30;
        } else if ($cycle == 6) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 90 days'));
            $frequency = 'q';
            $ftype = 'MONTHS';
            $grace_period = ($this->loan['num_grace_periods'] ?? 0) / 90;
        } else if ($cycle == 4) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 15 days'));
            $frequency = 'd';
            $ftype = 'DAYS';
            $grace_period = ($this->loan['num_grace_periods'] ?? 0) / 15;
        } else if ($cycle == 5) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 360 days'));
            $frequency = 'y';
            $ftype = 'YEARS';
            $grace_period = ($this->loan['num_grace_periods'] ?? 0) / 360;
        }

        $this->start_of_payment_date = $use_date_first;

        $details = $this->getRightSchedule($rate, $duration, $principal, $use_date_first, $method, $grace_period, $frequency, $ftype, $grace_type, $refine);

        $usedate = $use_date_first;
        foreach ($details['all_payments'] as $row) {

            $sqlQueryx = 'INSERT INTO public."loan_schedule" (loan_id,amount,interest,principal,balance,date_of_payment,outstanding_interest,outstanding_principal) VALUES(:lid,:amount,:inter,:principal,:bal,:dop,:outstanding_interest,:outstanding_principal)';

            $total_payment = round($row['total_payment']);
            $brought_forward = round($row['brought_forward']);
            $interest_expected = round($row['interest_expected']);
            $principal_expected = round($row['principal_expected']);
            $dpp = date('Y-m-d : H:i:s', strtotime($usedate));
            $stmtx = $this->conn->prepare($sqlQueryx);
            $stmtx->bindParam(':lid', $this->loan_id);
            $stmtx->bindParam(':amount', $total_payment);
            $stmtx->bindParam(':inter', $interest_expected);
            $stmtx->bindParam(':principal', $principal_expected);
            $stmtx->bindParam(':outstanding_interest', $interest_expected);
            $stmtx->bindParam(':outstanding_principal', $principal_expected);
            $stmtx->bindParam(':bal', $brought_forward);
            $stmtx->bindParam(':dop', $dpp);

            $stmtx->execute();

            if ($cycle == 1) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 1 days'));
            } else if ($cycle == 2) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 7 days'));
            } else if ($cycle == 3) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 30 days'));
            } else if ($cycle == 4) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 15 days'));
            } else if ($cycle == 5) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 360 days'));
            } else if ($cycle == 6) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 90 days'));
            }
        }
        return true;
    }

    public function getLoanPrincipal($loan_id)
    {
        $sqlQueryx = 'SELECT SUM(principal) AS total FROM public."loan_schedule" WHERE loan_id=:lid';

        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':lid', $loan_id);

        $stmtx->execute();
        $row = $stmtx->fetch();
        $scheduled_principal = $row['total'];


        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:typ AND loan_id=:lid';
        $tyy = 'L';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':typ', $tyy);

        $stmt->execute();
        $rown = $stmt->fetch();
        $total_collection = $rown['total'];

        $balance = $scheduled_principal - $total_collection;

        /**
         * get total penalty waivered
         */
        $sqlQuery = 'SELECT SUM(amount) AS total_penalty_waivered FROM public."transactions" WHERE t_type=:typ AND loan_id=:lid';
        $tyy = 'WLP';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':typ', $tyy);
        $stmt->execute();
        $rown = $stmt->fetch();
        $total_penalty_waivered = $rown['total_penalty_waivered'];

        return [
            "scheduled_principal" => $scheduled_principal,
            "amount_paid" => $total_collection,
            "penalty_waivered" => $total_penalty_waivered,
            "balance" => max($balance, 0),
        ];
    }

    public function getLoanInterest($loan_id)
    {
        /**
         * get total interest
         */
        $sqlQueryx = 'SELECT SUM(interest) AS total FROM public."loan_schedule" WHERE loan_id=:lid';
        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':lid', $loan_id);
        $stmtx->execute();
        $row = $stmtx->fetch();
        $scheduled_interest = $row['total'];

        /**
         * get total interest paid
         */
        $sqlQuery = 'SELECT SUM(loan_interest) AS total FROM public."transactions" WHERE t_type=:typ AND loan_id=:lid';
        $tyy = 'L';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':typ', $tyy);

        $stmt->execute();
        $rown = $stmt->fetch();
        $total_collection = $rown['total'];

        $balance = $scheduled_interest - $total_collection;

        /**
         * get total interest waivered
         */
        $sqlQuery = 'SELECT SUM(amount) AS total_interest_waivered FROM public."transactions" WHERE t_type=:typ AND loan_id=:lid';
        $tyy = 'WLI';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':typ', $tyy);
        $stmt->execute();
        $rown = $stmt->fetch();
        $total_interest_waivered = $rown['total_interest_waivered'];


        /**
         * get total interest
         */
        $sqlQueryx = 'SELECT SUM(GREATEST(interest - interest_paid, 0)) AS schedule_balance FROM public."loan_schedule" WHERE loan_id=:lid AND status=:status';
        $stmtx = $this->conn->prepare($sqlQueryx);
        $status = 'active';
        $stmtx->bindParam(':lid', $loan_id);
        $stmtx->bindParam(':status', $status);
        $stmtx->execute();
        $row = $stmtx->fetch();
        $schedule_balance = $row['schedule_balance'];

        return [
            "scheduled_interest" => $scheduled_interest,
            "amount_paid" => $total_collection,
            "schedule_balance" => $schedule_balance,
            "interest_waivered" => $total_interest_waivered,
            "balance" => max($balance, 0),
        ];
    }


    public function getLoanScheduleOutStandingCurrent($loan_id)
    {
        $sqlQueryx = ' SELECT schedule_id, loan_id, outstanding_principal, outstanding_interest FROM public."loan_schedule" WHERE loan_id=:lid AND (outstanding_principal > 0 OR outstanding_interest > 0) ORDER BY schedule_id ASC ';

        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':lid', $loan_id);

        $stmtx->execute();
        $stmtx->fetch(PDO::FETCH_ASSOC);
    }

    public function getScheduleAmountTotal($lid)
    {
        $sqlQueryx = ' SELECT SUM(amount) AS tot FROM public."loan_schedule" WHERE loan_id=:lid  ';

        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':lid', $lid);

        $stmtx->execute();
        $row =  $stmtx->fetch();

        $tot_now = $row['tot'];


        $sqlQuery = 'SELECT * FROM public."loan_schedule" WHERE loan_id=:lid ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $lid);

        $stmt->execute();
        foreach ($stmt as $rown) {
            $tot_now = $tot_now - $rown['amount'];
            $sqlQuery = 'UPDATE public."loan_schedule" SET balance=:bal WHERE schedule_id=:lid ';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':lid', $rown['schedule_id']);
            $stmt->bindParam(':bal', $tot_now);

            $stmt->execute();
        }




        return true;
    }


    public function updateTotalLoanAmount($loan_id, $clear_loan = false)
    {
        $principal_setup = $this->getLoanPrincipal($loan_id);
        $interest_setup = $this->getLoanInterest($loan_id);

        $sqlQuery = 'UPDATE public."loan" SET total_loan_amount=:tla,interest_amount=:ia,current_balance=:cb,amount_paid=:ap,principal_balance=:pb,interest_balance=:ib,int_waivered=:interest_waivered, penalty_waivered=:penalty_waivered  WHERE loan_no=:lid';
        $tlaa = @$principal_setup["scheduled_principal"] + @$interest_setup["scheduled_interest"];

        // $interest_balance = @$interest_setup["balance"];
        // 43,600
        $interest_balance = @$interest_setup["schedule_balance"];
        $cbb = @$principal_setup["balance"] + $interest_balance;
        $app = @$principal_setup["amount_paid"] + @$interest_setup["amount_paid"];
        $iaa = $interest_setup['scheduled_interest'] ?? 0;
        $interest_waivered = @$interest_setup["interest_waivered"];
        $penalty_waivered = @$interest_setup["penalty_waivered"];
        $pb = @$principal_setup["balance"];
        // $ib = @$interest_setup["balance"];
        $ib = @$interest_setup["schedule_balance"];
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':tla', $tlaa);
        $stmt->bindParam(':ia', $iaa);
        $stmt->bindParam(':cb', $cbb);
        $stmt->bindParam(':ap', $app);
        $stmt->bindParam(':pb', $pb);
        $stmt->bindParam(':ib', $ib);
        $stmt->bindParam(':interest_waivered', $interest_waivered);
        $stmt->bindParam(':penalty_waivered', $penalty_waivered);

        $stmt->execute();

        if ($clear_loan) {
            // $loan_object = new Loan($this->conn);
            $this->clearLoan($loan_id);
        }
    }

    public function setBankMembershipFeeSettings()
    {
        /**
         * update bank memebership fee settings
         */
        $binding_array = [];
        $sqlQuery = ' UPDATE public."Bank" SET charges_membership_fee=:charges_membership_fee, membership_fee_chanel=:membership_fee_chanel, membership_fee_required=:membership_fee_required WHERE id=:bank_id ';
        $result = $this->conn->prepare($sqlQuery);

        $charges_membership_fee = $this->charges_membership_fee ? 1 : 0;
        if ($charges_membership_fee == 0) {
            $this->membership_fee_chanel = null;
            $this->membership_fee_required = null;
        }

        $binding_array[':charges_membership_fee'] = $charges_membership_fee;
        $binding_array[':membership_fee_chanel'] = $this->membership_fee_chanel;
        $binding_array[':membership_fee_required'] = $this->membership_fee_required;
        $binding_array[':bank_id'] = $this->bankId;
        $result->execute($binding_array);

        if ($charges_membership_fee == 0) {
            $this->id = $this->bankId;
            $bank_branches = $this->getAllBranches()->fetchAll(PDO::FETCH_ASSOC);
            $branches_ids = array_column($bank_branches, 'id');

            foreach ($branches_ids as $branch_id) {
                $deleteQuery = ' DELETE FROM  public.account_opening_fees WHERE public.account_opening_fees.branch_id=:branch_id ';
                $delete = $this->conn->prepare($deleteQuery);
                $delete->execute([':branch_id' => $branch_id]);
            }
        }

        // $bank = $this->getAllBankDetails($this->bankId)->fetch(PDO::FETCH_ASSOC);
    }
}
