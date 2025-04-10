<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/User.php';
include_once '../../models/Transaction.php';
include_once '../../models/AuditTrail.php';
require_once '../ApiResponser.php';

try {
    $ApiResponser = new ApiResponser();

    // echo $ApiResponser::SuccessMessage();
    // return;
    //code...

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new User($db);
    $item2 = new Transaction($db);
    $item3 = new AuditTrail($db);

    $data = json_decode(file_get_contents("php://input"));

    $item->bankId = @$data->bank;
    $item->region = $data->region ?? '';
    $item->marital = $data->marital ?? '';
    $item->gender = @$data->gender;
    $item->branchId = @$data->branch;
    $item->education_level = @$data->education_level;
    $item->other_attachments = $data->otherattach ?? '';
    $item->profilePhoto = $data->passport ?? '';
    $item->sign = $data->signature ?? '';
    $item->fingerprint = $data->fing ?? '';
    $item->actype = $data->actype ?? 0;
    $item->message_consent = $data->message ?? '';
    $item->firstName = $data->fname ?? $data->name ?? '';
    $item->firstName = $data->fname ?? '';
    $item->profession = $data->prof ?? '';
    $item->lastName = $data->lname ?? '';
    $item->addressLine1 = $data->address ?? '';
    $item->addressLine2 = $data->address2 ?? '';
    $item->country = $data->country ?? '';
    $item->district = $data->district ?? '';
    $item->subcounty = $data->subcounty ?? '';
    $item->parish = $data->parish ?? '';
    $item->village = $data->village ?? '';
    $item->primaryCellPhone = $data->primaryCellPhone ?? '';
    $item->secondaryCellPhone = $data->secondaryCellPhone ?? '';
    $item->email = $data->email ?? '';
    $item->nin = $data->nin ?? '';
    $item->dateOfBirth = @$data->dob;
    $item->bname = $data->bname ?? '';
    $item->registration_status = $data->registration_status ?? '';
    $item->bregno = $data->businessreg ?? '';
    $item->baddress = $data->baddress ?? '';
    $item->baddress2 = $data->baddress2 ?? '';
    $item->bcity = $data->businessCity ?? '';
    $item->bcountry = $data->businesscountry ?? '';
    $item->spouseName = $data->kname ?? '';
    $item->spouseCell = $data->kphone ?? '';
    $item->spouseNin = $data->knin ?? '';
    $item->krelationship = $data->relationship ?? '';
    $item->kaddress = $data->kphysicaladdress ?? '';
    $item->notes = $data->notes ?? '';
    $item->confirmed = true;
    $item->status = 'ACTIVE';
    $item->serialNumber = 100;
    $item->identificationNumber = '100';

    $item->entered_by = $data->uid ?? 0;
    $item->income = $data->income ?? 0;



    $item->disability_desc = $data->disability_status ?? '';
    $item->disability_status = $data->disability_category ?? '';
    $item->disability_cat = $data->disabled_cat_other ?? '';
    $item->disability_others = $data->disability_details ?? '';

    $item->occupation_type_id = $data->occupation_type_id ?? 0;
    $item->client_type = $data->client_type ?? '';
    $item->is_registered = $data->is_registered ?? '';
    $item->business_type = $data->business_type ?? '';
    $item->business_type_other = $data->business_type_other ?? '';
    $item->business_nature_description = $data->business_nature_description ?? '';
    $item->name = $data->name ?? '';
    $item->otherCellPhone = $data->otherCellPhone ?? '';
    $item->number_of_members = $data->number_of_members ?? '';


    $item->occupation_category = $data->ocategory ?? 0;
    $item->occupation_sub_category = $data->oscategory ?? 0;
    $item->occupation_sector = $data->ocsector ?? 0;
    $item->other_cat = $data->cat_other ?? '';
    $item->other_sect = $data->sect_other ?? '';
    $item->other_sub_cat = $data->sub_other ?? '';

    $item->sms_phone_numbers = [];
    if (@$data->phone_1_send_sms && $item->primaryCellPhone) {
        array_push($item->sms_phone_numbers, $item->primaryCellPhone);
    }

    if (@$data->phone_2_send_sms && $item->secondaryCellPhone) {
        array_push($item->sms_phone_numbers, $item->secondaryCellPhone);
    }

    if (@$data->phone_3_send_sms && $item->otherCellPhone) {
        array_push($item->sms_phone_numbers, $item->otherCellPhone);
    }

    $item->sms_phone_numbers = count($item->sms_phone_numbers) ? $item->sms_phone_numbers : null;


    $ret = '';
    $cidd = 0;
    $acc_use_no = '';

    // check if the client is member or non-member
    if ($data->actype != 0) {
        // is member generate account number

        // get account number length && filler character in the account number of the bank
        $getAccValues = $item->getBankAccLength($data->branch);


        // separate the return merge separated by / , i.e acc-length and filler character
        $myArray = explode('/', $getAccValues);
        $accLength = (int)$myArray[0];
        $paddValue = $myArray[1];

        // get the saving product code 
        $accCode = $item->getAccountCode($data->actype);
        $branchCode = $item->getBranchCode($data->branch);
        $codelength = strlen($accCode['ucode']);
        $uselength = $accLength - $codelength;



        // insert client -- to get the userid of the client
        $rett = $item->createClient();
        $cidd = $rett;

        // generate the account number now
        $take = $rett;
        $padd = sprintf('%' . $paddValue . '' . $uselength . 'd', $take);
        $acc_use_no = $branchCode . '-' . $accCode['ucode'] . '-' . $take;

        // echo $acc_use_no;


        // update the client and set the  generated account number
        $ret = $item->setClientAccountNumber($acc_use_no, $rett);

        // echo $acc_use_no;
        // return;
    } else {
        // non-members account_number =0
        $item->mno = 0;
        // create the client 
        $ret = $item->createClient();
        $cidd = $ret;
    }

    // insert into the audit trail table -- for creating a client

    // generate and organise audit trail info

    $which_client = $acc_use_no == '' ? 'Non-Member' : 'Member';
    $audit_no = $item->mno == 0 ? $rett : 'A/C No. ' . $item->mno;
    $audit_info  = array(
        "action" => 'Account Opening - ' . @$which_client,
        "log_desc" => 'Created New Account for: - ' . @$data->fname . ' ' . @$data->lname . ' ' . @$data->name . ': ' . @$audit_no,
        "uid" => @$data->uid,
        "branch" => $data->branch,
        "bank" => NULL,
        "ip" => '',
        "status" => $ret != '' ? 'success' : 'failed',
    );

    // insert into audit trail
    $item->insertAuditTrail($audit_info);

    // check for account opening sms subscription and sms balance , then send sms
    $smstype = $item->getBankSMStypeStatus($data->branch, 'account_opening');

    if ($smstype != 0 && $smstype['s_status'] == 1) {


        //  start on account_opening sms sending process

        $sms_price = 0;
        $senderid = '';
        // check sacco branch sms balance first 
        $sms_bal = $item2->checkBranchSMSBalance($data->branch);
        $prices = $item2->checkBankSMSPrice($data->branch);

        // check for senderid used , and get sms price
        $senderid = $item2->getBranchSenderid($data->branch);
        if ($senderid != '') {

            $sms_price = $prices['sms_sender_id_price'];
        } else {
            $sms_price = $prices['sms_price'];
        }
        if ($sms_bal > $sms_price || $sms_bal == $sms_price) {

            // fill temp_body tags with the right info

            $clientDetails  = array(
                "fname" => @$data->fname . @$data->name,
                "lname" => @$data->lname.' ',
                "branch" => @$data->branch,
                "othername" => '',
                "phone" => @$data->primaryCellPhone,
                "acno" => $acc_use_no,
                "actype" => $accCode['name'],
            );

            $sms = $item->decryptSMS($smstype['temp_body'], 'account_opening', $clientDetails);

            // check if client is individual or group , institution

            if ($data->client_type == 'individual') {
                // check if phone number has country code or not --use 256 by default
                if ($data->primaryCellPhone[0] == "0" || $data->primaryCellPhone[0] == 0 || $data->primaryCellPhone[0] == "7") {
                    if ($data->primaryCellPhone[0] == "0" || $data->primaryCellPhone[0] == 0) {
                        $data->primaryCellPhone = '256' . substr($data->primaryCellPhone, 1);
                    } else {
                        $data->primaryCellPhone = '256' . $data->primaryCellPhone;
                    }
                }
                // send sms
                $res =  $item2->SendSMS($senderid, $data->primaryCellPhone, $sms);
            } else {
                if ($item->sms_phone_numbers) {
                    foreach ($item->sms_phone_numbers as $value) {
                        // check if phone number has country code or not --use 256 by default
                        if ($value[0] == "0" || $value[0] == 0 || $value[0] == "7") {
                            if ($value[0] == "0" || $value[0] == 0) {
                                $value = '256' . substr($value, 1);
                            } else {
                                $value = '256' . $value;
                            }
                        }
                        // send sms
                        $res =  $item2->SendSMS($senderid, $value, $sms);
                    }

                    $sms_price = $sms_price * count($item->sms_phone_numbers);
                    $smstype['charge'] = (int)$smstype['charge'] * count($item->sms_phone_numbers);
                }
            }


            // check if sms sent successfully or not
            // if success, then do the steps down , if false , then just insert into sms_outbox
            if ($res = 'OK') {

                if ($sms_price > 0) {


                    // offset from sacco branch balance
                    $item2->chargeBranchSMS($sms_price, $data->branch);
                }
                if ($smstype['charge'] > 0 && !is_null($rett) && $smstype['charged_to'] == 'client') {
                    // offset from client account (if charge >0 ) 
                    $item2->chargeClientSMS($rett, $smstype['charge']);

                    // get the chart account id , then create trxn in table transactions --- t_type = SMS
                    $acid =  $item2->getBranchSMSChargesAcc($data->branch);

                    // engange the create sms trxn method
                    $item2->createSMSChargeTrxn(
                        $smstype['charge'],
                        0,
                        'SMS',
                        'A/C Opening SMS Charge',
                        $data->uid ?? 0,
                        $data->primaryCellPhone,
                        $data->primaryCellPhone,
                        $data->primaryCellPhone,
                        $rett ?? 0,
                        $data->uid ?? 0,
                        $data->branch,
                        1,
                        $acid,
                        'saving',
                        1
                    );
                }

                if ($item->sms_phone_numbers && $data->client_type != 'individual') {
                    foreach ($item->sms_phone_numbers as $value) {
                        $my_charge =    $smstype['charge'] / count($item->sms_phone_numbers);
                        // insert into sms_outbox for record purposes  with status sent
                        $item2->insertSMSOutBox($value, $sms, $senderid, (int)$rett ?? 0, (int)$my_charge, 'sent', 1, $data->branch, 'Account Opening SMS');
                    }
                } else {
                    $item2->insertSMSOutBox($data->primaryCellPhone, $sms, $senderid, (int)$rett ?? 0, (int)$smstype['charge'], 'sent', 1, $data->branch, 'Account Opening SMS');
                }
            } else {


                if ($item->sms_phone_numbers && $data->client_type != 'individual') {
                    foreach ($item->sms_phone_numbers as $value) {
                        $my_charge =    $smstype['charge'] / count($item->sms_phone_numbers);
                        // insert into sms_outbox for record purposes  with not sent status
                        $item2->insertSMSOutBox($value, $sms, $senderid, (int)$rett ?? 0, (int)$my_charge, 'failed', 1, $data->branch, $res);
                    }
                } else {
                    // insert into sms_outbox for record purposes  with not sent status
                    $item2->insertSMSOutBox($data->primaryCellPhone, $sms, $senderid, (int)$rett ?? 0, (int)$smstype['charge'], 'failed', 1, $data->branch, $res);
                }
            }
        }
    }

    echo $ApiResponser::SuccessMessage($cidd);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
