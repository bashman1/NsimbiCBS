<?php

require_once('YoAPI.php');

class MomoLibrary
{

    /**
     * Required.
     * This is used to Stored errors .
     * @var string
     */
    private $err = null;
    /**
     * Required.
     * This is used to Stored the Main YoAPI onbject.
     * @var string
     */
    private $pay = null;

    /*
	* This is used to Check for Blanks
	*/
    public function isEmpty($str = "")
    {
        if ($str === "0" || $str === 0) {
            return false;
        } else {
            return empty($str) || !isset($str) || is_null($str);
        }
    }

    /**
     * The External Reference variable
     * Optional.
     * An External Reference is something which yourself and the beneficiary agree upon
     * e.g. an invoice number
     * Default: NULL
     * @var string
     */
    private $external_reference = NULL;


    /**
     * MOMO constructor.
     * @param string $username
     * @param string $password
     */
    public function __construct($username = "", $password = "")
    {
        $err = array();
        if ($this->isEmpty($username) || $this->isEmpty($password)) {
            if ($this->isEmpty($username)) {
                $err[] = "Username";
            }
            if ($this->isEmpty($password)) {
                $err[] = "Password";
            }
            $this->err = "Missing API " . implode(" or ", $err);
        } else {
            $this->pay = new YoAPI($username, $password);
        }
    }

    /**
     * Set the External Reference
     * @param string $external_reference Used when submitting payment requests
     * @return void
     */
    public function set_external_reference($external_reference)
    {
        $this->external_reference = $external_reference;
    }

    /**
     * Get the current balance of your Yo! Payments Account
     * Returned array contains an array of balances (including airtime)
     * @return array
     */
    public function CheckAccountBalance()
    {
        try {
            if ($this->isEmpty($this->err)) {
                $sql = $this->pay->ac_acct_balance();
                // throw new Exception(json_encode($sql), 1);
                if ($sql["Status"] == "OK") {
                    $result = array(
                        "Status"  => 0,
                        "Message" => "QUERY OK",
                        "Rows"       => count($sql["balance"]),
                        "Values"  => $sql["balance"],
                    );
                } else {
                    throw new Exception("You cannot checked for the balance at the moment", 1);
                }
            } else {
                throw new Exception($this->err, 1);
            }
        } catch (Exception $ex) {
            $result = array("Status" => $ex->getCode(), "Message" => $ex->getMessage());
        }
        return $result;
    }


    /**
     * Request Mobile Money User to deposit OR withdraw funds into your account
     * Shortly after you submit this request, the mobile money user receives an on-screen
     * @param string $msisdn the mobile money phone number in the format 256772123456
     * @param double $amount the amount of money to deposit into your account (floats are supported)
     * @param string $narrative the reason for the mobile money user to deposit funds 
     * @return array
     */
    private function WithdrawOrDespoist($activity = "", $msisdn = "", $amount = "", $narrative = "")
    {
        $codes = array("256");
        $activities = array(
            "withdraw" => "ac_withdraw_funds",
            "deposit" => "ac_deposit_funds",
        );
        $activity = strtolower($activity);
        try {
            if ($this->isEmpty($activity) || $this->isEmpty($msisdn) || $this->isEmpty($amount) ||  $this->isEmpty($narrative)) {

                $err = array();
                if ($this->isEmpty($activity)) {
                    $err[] = "Activity";
                }
                if ($this->isEmpty($msisdn)) {
                    $err[] = "Mobile Number";
                }
                if ($this->isEmpty($amount)) {
                    $err[] = "Amount";
                }
                if ($this->isEmpty($narrative)) {
                    $err[] = "Narration";
                }

                throw new Exception("Missing API " . implode(", ", $err), 1);
            } else if (!is_numeric($msisdn) || !is_numeric($amount)) {

                $err = array();
                if (!is_numeric($msisdn)) {
                    $err[] = "Mobile Number";
                }
                if (!is_numeric($amount)) {
                    $err[] = "Amount";
                }

                throw new Exception(implode(" and ", $err) . " MUST be NUMERIC", 1);
            } else if (strlen($msisdn) != 12 || !in_array(substr($msisdn, 0, 3), $codes)) {

                $err = array();
                if (strlen($msisdn) != 12) {
                    $err[] = "of 12 Digits";
                }
                if (!in_array(substr($msisdn, 0, 3), $codes)) {
                    $err[] = "starting with : " . implode(", ", $codes);
                }
                throw new Exception("Mobile Number SHOULD be " . implode(" and ", $err), 1);
            } else {
                if (!$this->isEmpty($this->err) || !array_key_exists($activity, $activities)) {

                    $err = array();
                    if (!$this->isEmpty($this->err)) {
                        $err[] = $this->err;
                    }
                    if (!array_key_exists($activity, $activities)) {
                        $err[] = "Activity MUST be in : " . implode(", ", array_keys($activities));
                    }
                    throw new Exception("Mobile Number SHOULD be " . implode(" and ", $err), 1);
                } else {

                    $method = $activities[$activity];

                    $this->pay->set_nonblocking("TRUE");
                    $this->pay->set_external_reference($this->external_reference);
                    $sql = $this->pay->{$method}($msisdn, $amount, $narrative);

                    if ($sql["Status"] == "OK") {
                        $stcode = $sql['StatusCode'];

                        $result = array(
                            "Status"    => $stcode == "0" ? "0" : "2",
                            "Tstatus"   => $stcode == "0" ? "SUCCEEDED" : ($stcode == "1" ? "PENDING" : "FAILED"),
                            "TreffID"   => isset($sql['TransactionReference']) ? $sql['TransactionReference'] : "",
                            "MomoID"    => isset($sql['MNOTransactionReferenceId']) ? $sql['MNOTransactionReferenceId'] : "",
                            "ReceiptID" => isset($sql['IssuedReceiptNumber']) ? $sql['IssuedReceiptNumber'] : "",
                            "Message"   => $stcode == "0"
                                ? "Your Transaction has successfully worked on."
                                : ($stcode == "1"
                                    ? "Your Transaction is in a pending state, please we shall get back to you after working on it."
                                    : "Sorry!! Your Transaction has failed, please try again later"
                                ),
                        );
                    } else {
                        if ($sql['StatusCode'] == "--") {
                            $result = array("Status"  => 1,  "Tstatus" => "FAILED", "Message" => $sql['StatusMessage']);
                        } else {
                            $result = array(
                                "Status"  => 1,
                                "Tstatus"   => isset($sql['TransactionStatus']) ? $sql['TransactionStatus'] : "FAILED",
                                "Message" => isset($sql['StatusMessage']) ? $sql['StatusMessage'] : ""
                            );
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            $result = array("Status" => $ex->getCode(), "Message" => $ex->getMessage());
        }
        return $result;
    }

    /**
     * Request Mobile Money User to deposit funds into your account
     * @param string $msisdn the mobile money phone number in the format 256772123456
     * @param double $amount the amount of money to deposit into your account (floats are supported)
     * @param string $narrative the reason for the mobile money user to deposit funds 
     * @return array
     */
    public function WithdrawFromMobile($msisdn, $amount, $narrative)
    {
        return $this->WithdrawOrDespoist("deposit", $msisdn, $amount, $narrative);
    }

    /**
     * Request get money from your account to deposit funds to Mobile Money User
     * @param string $msisdn the mobile money phone number in the format 256772123456
     * @param double $amount the amount of money to deposit into your account (floats are supported)
     * @param string $narrative the reason for the mobile money user to deposit funds 
     * @return array
     */
    public function DepositToMobile($msisdn, $amount, $narrative)
    {
        return $this->WithdrawOrDespoist("withdraw", $msisdn, $amount, $narrative);
    }


    /**
     * Check the status of a transaction that was earlier submitted for processing.
     * Its particularly useful where the NonBlocking is set to TRUE.
     * It can also be used to check on any other transaction on the system.
     * @param string $transaction_reference the response from the Yo! Payments Gateway that uniquely identifies the transaction whose status you are checking
     * @param string $private_transaction_reference The External Reference that was used to carry out a transaction
     * @return array
     */
    public function CheckTransactionStatus($transaction_reference = "", $private_transaction_reference = NULL)
    {
        try {
            if (!$this->isEmpty($this->err) || $this->isEmpty($transaction_reference)) {
                $err = array();
                if (!$this->isEmpty($this->err)) {
                    $err[] = $this->err;
                }
                if ($this->isEmpty($transaction_reference)) {
                    $err[] = "Transaction_reference  MUST be provided ";
                }
                throw new Exception(implode(" and ", $err), 1);
            } else {
                $sql = $this->pay->ac_transaction_check_status($transaction_reference, $private_transaction_reference);
                // throw new Exception(json_encode($sql), 1);
                if ($sql["Status"] == "OK") {
                    $result = array(
                        "Status"                      => isset($sql['StatusCode']) ? ($sql['StatusCode'] == 0 ? 0 : 2) : 1,
                        "Message"                     => isset($sql['StatusMessage']) ? $sql['StatusMessage'] : "",
                        "Tstatus"                   => isset($sql['TransactionStatus']) ? $sql['TransactionStatus'] : "PENDING",
                        "Amount"                     => isset($sql['Amount']) ? $sql['Amount'] : "0",
                        "AmountFormatted"             => isset($sql['AmountFormatted']) ? $sql['AmountFormatted'] : "",
                        "CurrencyCode"                 => isset($sql['CurrencyCode']) ? $sql['CurrencyCode'] : "",
                        "TransactionInitiationDate" => isset($sql['TransactionInitiationDate']) ? $sql['TransactionInitiationDate'] : "",
                        "TransactionCompletionDate" => isset($sql['TransactionCompletionDate']) ? $sql['TransactionCompletionDate'] : "",
                    );
                } else {
                    if ($sql['StatusCode'] == "--") {
                        $result = array("Status"  => 1,  "Tstatus" => "FAILED", "Message" => $sql['StatusMessage']);
                    } else {
                        $result = array(
                            "Status"  => 2,
                            "TcodeId" => isset($sql['StatusCode']) ? $sql['StatusCode'] : "",
                            "Tstatus" => isset($sql['TransactionStatus']) ? $sql['TransactionStatus'] : "",
                            "TreffID" => isset($sql['TransactionReference']) ? $sql['TransactionReference'] : "",
                            "Message" => isset($sql['StatusMessage']) ? $sql['StatusMessage'] : "",
                            "Description" => isset($sql['ErrorMessage']) ? $sql['ErrorMessage'] : "",
                        );
                    }
                }
            }
        } catch (Exception $ex) {
            $result = array("Status" => $ex->getCode(), "Message" => $ex->getMessage());
        }
        return $result;
    }

    public function ValidateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
    }

    public function ValidatePhone($phone)
    {
        $UG_codes = array('070', '075', '071', '077', '078', '079', '072', '074');
        $Country = array('256');

        $stat = substr($phone, 0, 3);
        if (is_numeric($phone)) {

            if (strlen($phone) == 10) {
                if (in_array($stat, $UG_codes)) { //UG
                    $formated = $Country[0] . substr($phone, 1);
                    $error = array("Status" => 0, "Message" => $formated);
                } else {
                    $error = array("Status" => 1, "Message" => "Phone Number starting with: [" . $stat . "] is not allowed. Must start with [256]");
                }
            } else if (strlen($phone) == 12) {
                $formated = !in_array($stat, $Country) ? "" : $phone;
                if (!empty($formated)) {
                    $error = array("Status" => 0, "Message" => $formated);
                } else {
                    $error = array("Status" => 1, "Message" => "Country Code: " . $stat . " is not allowed");
                }
            } else {
                $error = array("Status" => 1, "Message" => "Phone Number MUST have (10)Ten or (12)Tweleve Digits");
            }
        } else {
            $error = array("Status" => 1, "Message" => "Phone Number MUST be Numeric");
        }
        return $error;
    }

    public function getPhone($value)
    {
        $phone = $this->ValidatePhone($value);
        if ($phone["Status"] == 0) {

            return  $phone["Message"];
        }

        return 'Ivalid Phone Number';
    }
}
