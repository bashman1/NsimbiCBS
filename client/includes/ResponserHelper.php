<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class ResponserHelper
{
    public $session_data = [];
    public function __construct()
    {
        $data = [];
        if (isset($_SESSION['user'])) {

            if ($_SESSION['user']['bankId']) {
                $data['bank_id'] = $_SESSION['user']['bankId'];
                $data['bankId'] = $_SESSION['user']['bankId'];
            } else {
                $data['branch'] = $_SESSION['user']['branchId'];
                $data['branchId'] = $_SESSION['user']['branchId'];
            }

            $data['auth_id'] = $_SESSION['user']['userId'];
        }
        $this->session_data = array_merge($data, $_REQUEST);
    }

    public function get($url, $data = [])
    {
        $data = array_merge($this->session_data, $data);
        // var_dump($data);
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => BASE_URL . '/' . $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ));

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);
            // var_dump($response);
            return $response;
        } catch (\Throwable $th) {
            // return $data;
            return $th->getMessage();
        }
    }

    public function get2($url, $data = [])
    {
        $data = array_merge($this->session_data, $data);
        // var_dump($data);
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => BASE_URL . '/' . $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ));

            // $response = json_decode(curl_exec($curl), true);
            var_dump(curl_exec($curl));
            curl_close($curl);
            exit;
           
            // return $response;
        } catch (\Throwable $th) {
            // return $data;
            return $th->getMessage();
        }
    }


    public function post($url, $data = [])
    {
        $data = array_merge($this->session_data, $data);

        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => BACKEND_BASE_URL . '/' . $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ));
            // var_dump(curl_exec($curl));
            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            // var_dump($response);
            return $response;
        } catch (\Throwable $th) {
            // return $data;
            return $th->getMessage();
        }
    }

    public function post2($url, $data = [])
    {
        $data = array_merge($this->session_data, $data);

        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => BACKEND_BASE_URL . '/' . $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ));
            var_dump(curl_exec($curl));
            // $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            // var_dump($response);
            // return $response;
        } catch (\Throwable $th) {
            // return $data;
            return $th->getMessage();
        }
    }
}
