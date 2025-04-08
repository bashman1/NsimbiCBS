<?php
require_once '../../config/constants.php';
require_once '../../config/functions.php';
require_once '../../api/DatatableSearchHelper.php';
require_once 'AuditTrail.php';

class MobileApp
{
    // DB stuff
    private $conn;
    private $db_table = '';

    public $phone_number;
    public $app_mpin;
    public $macno;
    public $mid;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function getBankName2($id, $type)
    {
        if (
            $type == 'branch'
        ) {
            $sqlQueryn = 'SELECT  public."Bank".trade_name AS bname  FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $id);
            $stmtn->execute();

            $row = $stmtn->fetch();

            return $row['bname'];
        } else if ($type == 'bank') {
            $sqlQueryn = 'SELECT name  FROM public."Bank"  WHERE public."Bank".id=:id ';
            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $id);
            $stmtn->execute();

            $row = $stmtn->fetch();

            return $row['name'];
        }


        return '';
    }
    // login for clients in the mobile app using phone number and mPIN
    public function loginClientApp()
    {
        $sqlQuery = 'SELECT * FROM  public."Client" WHERE mpin=:mno';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':mno', $this->app_mpin);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            foreach ($stmt as $row) {

                // check for phone number
                $sqlQueryn = 'SELECT * FROM  public."User" WHERE id=:mno';

                $stmtn = $this->conn->prepare($sqlQueryn);

                $stmtn->bindParam(':mno', $row['userId']);

                $stmtn->execute();
                $rown = $stmtn->fetch();

                $use_phone1 = "0" . $this->phone_number;
                $use_phone2 = "256" . $this->phone_number;
                $use_phone3 = "+256" . $this->phone_number;

                if ($rown['primaryCellPhone'] == $this->phone_number || $rown['primaryCellPhone'] == $use_phone1 || $rown['primaryCellPhone'] == $use_phone2 || $rown['primaryCellPhone'] == $use_phone3) {

                    $sqlQuery = 'SELECT * FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id  = public."Client".actype   WHERE public."User".id=:mno';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':mno', $row['userId']);

                    $stmt->execute();

                    return $stmt;
                }
            }
        }
        return '';
    }
    // register client for mobile banking
    public function registerMobileBanking()
    {
        $sqlQuery = 'SELECT * FROM public."Client" WHERE public."Client".membership_no::text=:mno OR public."Client"."userId"::text=:mno ORDER BY id ASC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':mno', $this->macno);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            // check for phone number
            $sqlQueryn = 'SELECT * FROM  public."User" WHERE id=:mno ORDER BY id ASC LIMIT 1';

            $stmtn = $this->conn->prepare($sqlQueryn);

            $stmtn->bindParam(':mno', $row['userId']);

            $stmtn->execute();

            $rown = $stmtn->fetch();

            $use_phone1 = "0" . $this->phone_number;
            $use_phone2 = "256" . $this->phone_number;
            $use_phone3 = "+256" . $this->phone_number;

            // compare phone number
            if ($rown['primaryCellPhone'] == $this->phone_number || $rown['primaryCellPhone'] == $use_phone1 || $rown['primaryCellPhone'] == $use_phone2 || $rown['primaryCellPhone'] == $use_phone3) {

                // check for already subscribed guys
                if ($row['mpin'] > 0) {
                    $mpin  = $row['mpin'];

                    // SEND SMS
                    $this->sendMobileBankingSMS($this->phone_number, $mpin, 2, $row['membership_no'], $rown['firstName'] . $rown['shared_name']);

                    $sqlQuery = 'SELECT * FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:mno';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':mno', $row['userId']);

                    $stmt->execute();

                    return $stmt;
                } else {
                    // generate mpin
                    $mpin = rand(1231, 7879);
                    $mpinenc = md5($mpin);

                    // UPDATE CLIENT TO SET MPIN

                    $sqlQueryx = 'UPDATE  public."Client" SET mpin=:mpin,mpin_enc=:pinenc WHERE public."Client"."userId"=:mno';

                    $stmtx = $this->conn->prepare($sqlQueryx);

                    $stmtx->bindParam(':mno', $row['userId']);
                    $stmtx->bindParam(':mpin', $mpin);
                    $stmtx->bindParam(':pinenc', $mpinenc);

                    $stmtx->execute();

                    // SEND SMS
                    $this->sendMobileBankingSMS($this->phone_number, $mpin, 1, $row['membership_no'], $rown['firstName'] . $rown['shared_name']);

                    $sqlQuery = 'SELECT * FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:mno';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':mno', $row['userId']);

                    $stmt->execute();

                    return $stmt;
                }
            } else if ($rown['primaryCellPhone'] == $this->phone_number || $rown['primaryCellPhone'] == $use_phone1 || $rown['primaryCellPhone'] == $use_phone2 || $rown['primaryCellPhone'] == $use_phone3) {

                // UPDATE CLIENT TO SET MPIN, phone number & SEND SMS
                // check for already subscribed guys
                if ($row['mpin'] > 0) {
                    $mpin  = $row['mpin'];


                    // update client's mobile_number
                    $sqlQueryx = 'UPDATE  public."User" SET "primaryCellPhone"=:mpin WHERE id=:mno';

                    $stmtx = $this->conn->prepare($sqlQueryx);
                    $myphone = '256' . $this->phone_number;
                    $stmtx->bindParam(':mno', $row['userId']);
                    $stmtx->bindParam(':mpin', $myphone);

                    $stmtx->execute();


                    // SEND SMS
                    $this->sendMobileBankingSMS($this->phone_number, $mpin, 2, $row['membership_no'], $rown['firstName'] . $rown['shared_name']);

                    $sqlQuery = 'SELECT * FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:mno';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':mno', $row['userId']);

                    $stmt->execute();

                    return $stmt;
                } else {
                    // generate mpin
                    $mpin = rand(1231, 7879);
                    $mpinenc = md5($mpin);

                    // UPDATE CLIENT TO SET MPIN

                    $sqlQueryx = 'UPDATE  public."Client" SET mpin=:mpin,mpin_enc=:pinenc WHERE public."Client"."userId"=:mno';

                    $stmtx = $this->conn->prepare($sqlQueryx);

                    $stmtx->bindParam(':mno', $row['userId']);

                    $stmtx->execute();

                    // update client's mobile_number
                    $sqlQueryx = 'UPDATE  public."User" SET "primaryCellPhone"=:mpin WHERE id=:mno';

                    $stmtx = $this->conn->prepare($sqlQueryx);
                    $myphone = '256' . $this->phone_number;
                    $stmtx->bindParam(':mno', $row['userId']);
                    $stmtx->bindParam(':mpin', $myphone);

                    $stmtx->execute();


                    // SEND SMS
                    $this->sendMobileBankingSMS($this->phone_number, $mpin, 2, $row['membership_no'], $rown['firstName'] . $rown['shared_name']);

                    $sqlQuery = 'SELECT * FROM  public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:mno';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':mno', $row['userId']);

                    $stmt->execute();

                    return $stmt;
                }
            }
        }
        return '';
    }

    public function getClientShares($uid)
    {
        $sqlQuery = 'SELECT SUM(no_shares) AS tot FROM  public."share_register" WHERE public."share_register".userid=:mno';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':mno', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }

    public function getClientShareAmount($uid)
    {
        $sqlQuery = 'SELECT SUM(share_amount) AS tot FROM  public."share_register" WHERE public."share_register".userid=:mno';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':mno', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }

    public function sendMobileBankingSMS($phone, $mpin, $mode, $mno, $fname)
    {

        $uupho = '256' . $phone;

        if ($mode == 1) {
            $mes = 'Dear ' . $fname . ' , your account has been activated for Mobile Banking. Your mPIN is ' . $mpin . ', use this to access your account ( ' . $mno . ' ) via our Client\'s App. If you didn\'t request for this action Call: +256789167884 .
Thank you for Saving With Us!  MOYO SACCO.';
        } else {
            $mes = 'Dear ' . $fname . ' , your mobile Banking mPIN is ' . $mpin . ', use this to login to your account ( ' . $mno . ' ) via our Client\'s App. If you didn\'t request for this action Call: +256789167884 .
Thank you for Saving With Us!  MOYO SACCO.';
        }



        // start

        $data = array(
            'method' => 'SendSms',
            'userdata' => array(
                'username' => 'myaccount', // Egosms Username
                'password' => 'dibscore'
                //Egosms Password  
            ),
            'msgdata' => array(
                array(
                    'number' => $uupho,
                    'message' => $mes,
                    'senderid' => 'INFOSMS'
                )
            )

        );

        //encode the array into json
        $json_builder = json_encode($data);
        //use curl to post the the json encoded information
        $ch = curl_init('https://www.egosms.co/api/v1/json/');

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_builder);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ch_result = curl_exec($ch);
        curl_close($ch);
        //print an array that is json decoded
        // print_r(json_decode($ch_result, true));
        $ress = json_decode($ch_result, true);
    }

    public function getAllCustomerTransAppLatest()
    {
        // Create query
        $query = 'SELECT *, public."transactions".date_created AS date_created2,public."User"."createdAt" AS mdate FROM public."transactions" LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."transactions".mid LEFT JOIN public."Branch" ON public."transactions"._branch =public."Branch".id WHERE public."transactions".mid=:id  ORDER BY public."transactions".tid DESC LIMIT 5';

        // Prepare statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->mid);
        // $stmt->bindParam(":st", $this->start);
        // $stmt->bindParam(":eng", $this->endd);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    public function getAllCustomerTransAppSixMonth()
    {
        // Create query
        $query = 'SELECT *, public."transactions".date_created AS date_created2,public."User"."createdAt" AS mdate FROM public."transactions" LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."transactions".mid LEFT JOIN public."Branch" ON public."transactions"._branch =public."Branch".id WHERE public."transactions".mid=:id  ORDER BY public."transactions".tid DESC';

        // Prepare statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->mid);
        // $stmt->bindParam(":st", $this->start);
        // $stmt->bindParam(":eng", $this->endd);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    public function getAllCustomerLoanTransApp($lid)
    {
        // Create query
        $query = 'SELECT *, public."transactions".date_created AS date_created2,public."User"."createdAt" AS mdate FROM public."transactions" LEFT JOIN public."User" ON public."transactions"._authorizedby=public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."transactions".mid LEFT JOIN public."Branch" ON public."transactions"._branch =public."Branch".id WHERE public."transactions".loan_id=:id  ORDER BY public."transactions".tid DESC';

        // Prepare statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $lid);
        // $stmt->bindParam(":st", $this->start);
        // $stmt->bindParam(":eng", $this->endd);

        // Execute query
        $stmt->execute();

        return $stmt;
    }
}
