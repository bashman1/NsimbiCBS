<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../models/Account.php';
require_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
$records = json_decode($data['actual_data'], true);
$ApiResponser = new ApiResponser();

try {
    $db_handler = new DbHandler();
    $client = $db_handler->fetch('Client', 'userId', $data['client_id']);
    if (!@$client) {
        echo $ApiResponser::ErrorResponse("Client Not found");
        return;
    }

    // upload photo
    $files = $_FILES;
    $fingerprint_photo = $_FILES["fingerprint"];

  


    $passport_photo_name =  $_POST['profilePic'];
    if ($passport_photo['name']) {
        $target_path_passport = "images/passport_photo";
        if (!is_dir($target_path_passport)) {
            mkdir($target_path_passport, 0755, true);
        }
        try {
            $temp = explode(".", $passport_photo["name"]);
            $newfilename = uniqid('', true) . '.' . end($temp);
            $passport_photo_name = $target_path_passport . "/" . $newfilename;
            move_uploaded_file($passport_photo["tmp_name"], $passport_photo_name);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    $signature_photo_name =  $_POST['signature'];
    if ($signature_photo['name']) {
        $target_path_signature = "images/signatures";
        if (!is_dir($target_path_signature)) {
            mkdir($target_path_signature, 0755, true);
        }
        try {
            $temp = explode(".", $signature_photo["name"]);
            $newfilename = uniqid('', true) . '.' . end($temp);
            $signature_photo_name = $target_path_signature . "/" . $newfilename;
            move_uploaded_file($signature_photo["tmp_name"], $signature_photo_name);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /**
     * update account fingerprint to the new attachment
     */
  
        $db_handler->update('Client', ['fingerprint' => $data['fingerprint'],], 'userId', $client['userId']);
    
   
    echo $ApiResponser::SuccessMessage("Client Biometrics Enrolled Succesfully!");

    // if ($results === true) {
    //     return;
    // }
    // echo $ApiResponser::ErrorResponse($results);

} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
