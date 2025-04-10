<?php
include('get_authorization_token.php');

$data = json_decode(file_get_contents("php://input"));
$tid = $data->id;


//  get request to verify payment by ussd status
        $endpoint = "https://openapi.airtel.africa/standard/v1/payments/".$tid;
        $url = $endpoint;


  
        $options = array(
            'http' => array(
                'method'  => 'GET',
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
            "airtel_money_id": "18395651726",
            "id": "890007655444445",
            "message": "Initiator is invalid",
            "status": "TF"
        }
    },
    "status": {
        "response_code": "DP00800001010",
        "code": "200",
        "success": true,
        "result_code": "ESB000010",
        "message": "SUCCESS"
    }
}
**/
        // $message = $feedback['data']['transaction']['message'];
       
     echo json_encode($ussdfeedback);
    
?>