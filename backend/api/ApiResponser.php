<?php
class ApiResponser
{
    public $status_code;
    public $success;
    public $count;
    public $data;

    public static function SuccessResponse($data = [])
    {
        $response = array();
        $response['success'] = true;
        $response['statusCode'] = 200;
        $response['count'] = count($data && is_array($data) ? $data : []);
        $response['data'] = $data;

        http_response_code(200);
        echo json_encode($response);
    }

    public static function TrxnSuccessMessage($message = "Action successful",$tid=0)
    {
        $response = array();
        $response['success'] = true;
        $response['statusCode'] = 200;
        $response['message'] = $message;
        $response['tid'] = $tid;

        http_response_code(200);
        echo json_encode($response);
    }

    public static function SuccessMessage($message = "Action successful")
    {
        $response = array();
        $response['success'] = true;
        $response['statusCode'] = 200;
        $response['message'] = $message;

        http_response_code(200);
        echo json_encode($response);
    }

    public static function ErrorResponse($message = "Action failed")
    {
        $response = array();
        $response['success'] = false;
        $response['statusCode'] = 204;
        $response['message'] = $message;

        http_response_code(200);
        echo json_encode($response);
    }
}
