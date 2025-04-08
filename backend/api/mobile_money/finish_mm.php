<?php
require_once __DIR__ . '../../RequestHeaders.php';
try {

    $handler = new DbHandler();

    $mm = new MobileMoney($db);

    $amount = amount_to_integer(@$_GET['amount']);
    $bal = amount_to_integer(@$_GET['bal']);
    $tid = @$_GET['tid'];
    $acc_no = @$_GET['acc_no'];
    $phone = @$_GET['phone'];
    $status = 1;
    $bal_new = $bal + $amount;
    // update trxn status 
    $handler->update('transactions', [
        '_status' => $status,
    ], 'tid', $tid);

    // update customer balance
    $handler->update('Client', [
        'acc_balance' => $bal_new,
    ], 'membership_no', $acc_no);


    // redirect user to success page
    header("Location: mm_deposit_success.php?name=" . @$_GET["acc_name"] . "&amount=" . $_GET["amount"] . "&acc=" . $_GET["acc_no"] . "&bal=" . $bal_new);
    exit;

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
