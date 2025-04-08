<?php
//  post request to get access token
$endpoint = "https://openapiuat.airtel.africa/auth/oauth2/token";
$url = $endpoint;

$data = array(
    'client_id'      => "db5c9cdb-d38e-4b5f-9620-cc626776a1c0",
    'client_secret'      => "****************************",
    'grant_type'      => "client_credentials"
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
$feedback = json_decode($response, true);

/** reponse sample
{
    "token_type": "bearer",
    "access_token": "69GS6FHvPcu8hy52xAsRvbLkIIJJOHMO",
    "expires_in": 180
}
 **/
$token = $feedback['access_token'];
