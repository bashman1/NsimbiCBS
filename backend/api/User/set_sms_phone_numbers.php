<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../config/database.php';
require_once '../../models/Bank.php';
// require_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';

$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();
    $handler = new DbHandler();

    // $users = $handler->fetchAll('User', 'client_type', 'individual');

    $sqlQuery = 'SELECT *, public."User".id AS user_id FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id WHERE public."Client".client_type=:client_type';
    $stmt = $db->prepare($sqlQuery);

    $stmt->execute([':client_type' => 'individual']);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $sms_phone_numbers = [];
        if ($user['primaryCellPhone']) {
            $sms_phone_numbers = [$user['primaryCellPhone']];
        } else if ($user['secondaryCellPhone']) {
            $sms_phone_numbers = [$user['secondaryCellPhone']];
        }

        // $handler->update('User', ['sms_phone_numbers' => null], 'id', $user['user_id']);

        if ($user['message_consent']) {
            $handler->update('User', ['sms_phone_numbers' => json_encode($sms_phone_numbers)], 'id', $user['user_id']);
        }
    }

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
