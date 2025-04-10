<?php
require_once('Fee.php');
require_once('AuditTrail.php');
require_once '../../config/functions.php';
class MobileMoney
{
    public $conn;
    public $bank_id;
    public $branch_id;
    public $amount;
    public $details;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function depositAirtelMM($msisdn, $amount, $tid, $channel, $token)
    {



        //  post request to initiate payment by ussd
        $endpoint = "https://openapi.airtel.africa/merchant/v1/payments/";
        $url = $endpoint;

        $data = array(
            'reference'      => $channel,
            'subscriber'      => array(
                'country' => "UG",
                'currency' => "UGX",
                'msisdn' => $msisdn
            ),
            'transaction'      => array(
                'amount' => $amount,
                'country' => "UG",
                'id' => $tid
            )

        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n" .
                    "X-Country: UG\r\n" .
                    "X-Currency: UGX\r\n" .
                    "Authorization: Bearer " . $token . "\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $ussdfeedback = json_decode($response, true);

        return $ussdfeedback;
    }
}
