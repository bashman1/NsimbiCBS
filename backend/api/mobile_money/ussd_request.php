<?php
include('get_authorization_token.php');

$data = json_decode(file_get_contents("php://input"));


$msisdn = $data->phone;
$amount = $data->amount;
$tid = $data->id;
$channel = $data->channel;

//  post request to initiate payment by ussd
        $endpoint = "https://openapiuat.airtel.africa/merchant/v1/payments/";
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
                    "Authorization: Bearer ".$token."\r\n"
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $ussdfeedback = json_decode($response, true);

/** reponse sample
{
    "data": {
        "transaction": {
            "id": "6868686868686",
            "status": "Success."
        }
    },
    "status": {
        "response_code": "DP00800001006",
        "code": "200",
        "success": true,
        "result_code": "ESB000010",
        "message": "Success."
    }
}
**/
        // $rtid = $ussdfeedback['data']['transaction']['status'];
       
         echo json_encode($ussdfeedback);
    
    
?>