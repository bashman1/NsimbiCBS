<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/FieldAgents.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new FieldAgents($db);


$phone = isset($_GET['username']) ? $_GET['username'] : die();
$item->email = $phone;
$unecrpted_password = isset($_GET['password']) ? $_GET['password'] : die();
$item->password = $handler->Encoding(md5($unecrpted_password));


$stmt = $item->LoginEmployee();
$itemCount = $stmt->rowCount();



if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "You've logged in successfully !";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $details = $item->getStaffDetails($id);
        $u = array(
            "id" => $id,
            "role" => strtoupper('Field Agent'),
            "email" => $email,
            "fname" => strtoupper($firstName),
            "lname" => strtoupper($lastName),
            "branch" => $item->getBranchName($details),
            "branch_id" => $details,
            "date_created" => $createdAt,
            "phone" => $primaryCellPhone,
            "nationality" => $country,
            "emp_gender" => $gender,
            "emp_dob" => $dateOfBirth,
            "emp_place_birth" => $addressLine1,
            "emp_photo" => '' . $profilePhoto,
            "deposits" => $item->getAgentTotalDeposits($id),
            "membership" =>  $item->getAgentActiveMembersToday($id),
            "loans" => 0,

        );

        array_push($userArr['data'], $u);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "401";
    $userArr['message'] = "Login failed !";

    http_response_code(200);
    echo json_encode($userArr);
}
