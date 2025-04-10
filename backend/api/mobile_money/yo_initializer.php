<?php

require_once "../../config/constants.php";
require_once "../../models/MoMoLibrary.php";


class Intializer
{

    protected $db;
    protected $momo;
    // protected $sms;
    // protected $mail;

    public $resizer;
    public $session;
    public $upload;
    public $stream;
    public $excell;

    public function __construct()
    {

        ini_set("display_errors", 1);
        ini_set("display_startup_errors", 1);

        // header('Content-Type: text/html; charset=utf-8');
        header('Content-Type: charset=utf-8');
        //error_reporting(-1);

        $this->db = new Database(array('DBHost' => trim(DBHOST), 'DBUser' => trim(DBUSER), 'DBPass' => trim(DBPASS), 'DBName' => trim(DBNAME)));
        // $this->momo = new MomoLibrary(array( 
        // 'Username'  	=> trim(MMUSER), 
        // 'Password'  	=> trim(MMPASS),
        // 'Callbackpath' 	=> trim(MMCALLBACKPATH),
        // 'Callbacks'		=> MMCALLBACKS,
        // )
        // );
        $this->momo = new MomoLibrary(MMUSER, MMPASS);
        $this->sms = new SMSLibrary(array('Username' => trim(SMSUSER), 'Password' => trim(SMSPASS), 'Title' => trim(SMSPROJECTTITLE), 'Sender' => '2889'));
        $this->mail = new MailLibrary(array('Username' => trim(MAILUSER), 'Password' => trim(MAILPASS)));
        $this->resizer = new ImageResizer();
        $this->session = new SessionLibrary();
        $this->upload = new UploadLibrary();
        $this->stream = new VideoStream();

        //Email Settings
        $this->mail->setSenderEmail(MAILSENDEREMAIL);
        $this->mail->setSenderNames(MAILSENDERNAME);

        //Session Settings
        $this->session->session_add_key("isLoggedin");
        $this->session->session_add_key("LoggedinData", true);
    }


    //Inputs
    public function InputsPath()
    {
        return $this->db->InputsPath();
    }

    public function InputsPathName()
    {
        return $this->db->InputsPathName();
    }

    public function getDB()
    {
        return $this->db;
    }


    public function TransactMoney()
    {
        try {
            if (func_num_args() != 1) {
                throw new Exception(__CLASS__ . "." . __FUNCTION__ . " Method Requires only a Single Argument", 1);
            } else {
                $provided = func_get_arg(0);
                $expected = array('action', 'phone', 'amount', 'narrative', 'sucessUrl', 'failedUrl');
                $parms = $this->db->ArrayChecks($expected, $provided);
                if ($parms["Status"] == 0) {
                    $provid = $parms["provid"];
                    $expect = $parms["expect"];

                    $modes = array('DEPOSIT', 'WITHDRAW');
                    $validate = array(
                        $expect[0] => array('display' => "Action", 'required' => true, 'in_array' => $modes),
                        $expect[1] => array('display' => "Phone",  'required' => true, 'inputtype' => 'phone'),
                        $expect[2] => array('display' => "amount",  'required' => true, 'inputtype' => 'number'),
                        $expect[3] => array('display' => "narrative",  'required' => true),
                        $expect[4] => array('display' => "Sucess Url", 'required' => true),
                        $expect[5] => array('display' => "Failed Url", 'required' => true),
                    );

                    $this->db->ValidateInputs($provid, $validate);
                    if ($this->db->passed()) {
                        $action   = $this->db->clean($provid[$expect[0]]);
                        $msisdn  = $this->momo->getPhone();
                        $amount  = $this->db->clean($provid[$expect[2]]);
                        $narrative = $this->db->clean($provid[$expect[3]]);
                        $sucessUrl = $this->db->clean($provid[$expect[4]]);
                        $failedUrl = $this->db->clean($provid[$expect[5]]);

                        $method = $action == $modes[0] ? "WithdrawFromMobile" : "DepositToMobile";
                        $result = $this->momo->$method($msisdn, $amount, $narrative);
                    } else {
                        throw new Exception(implode('<br/> ', $this->db->getErrors()), 1);
                    }
                } else {
                    throw new Exception($parms["Message"], 1);
                }
            }
        } catch (Exception $ex) {
            $result = array("Status" => 1, "Message" => $ex->getMessage());
        }
        return $result;
    }

    public function CheckTransaction()
    {
        try {
            if (func_num_args() != 1) {
                throw new Exception(__CLASS__ . "." . __FUNCTION__ . " Method Requires only a Single Argument", 1);
            } else {
                $provided = func_get_arg(0);
                $expected = array('transactionReff');
                $parms = $this->db->ArrayChecks($expected, $provided);
                if ($parms["Status"] == 0) {
                    $provid = $parms["provid"];
                    $expect = $parms["expect"];

                    $validate = array(
                        $expect[0] => array('display' => "transaction Reference", 'required' => true),
                    );

                    $this->db->ValidateInputs($provid, $validate);
                    if ($this->db->passed()) {
                        $transactionReff = $this->db->clean($provid[$expect[0]]);
                        $result = $this->momo->CheckTransactionStatus($transactionReff);
                    } else {
                        throw new Exception(implode('<br/> ', $this->db->getErrors()), 1);
                    }
                } else {
                    throw new Exception($parms["Message"], 1);
                }
            }
        } catch (Exception $ex) {
            $result = array("Status" => 1, "Message" => $ex->getMessage());
        }
        return $result;
    }

    public function CheckBalance()
    {
        try {
            $result = $this->momo->CheckAccountBalance();
        } catch (Exception $ex) {
            $result = array("Status" => 1, "Message" => $ex->getMessage());
        }
        return $result;
    }
}
