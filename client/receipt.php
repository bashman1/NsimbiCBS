<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
include_once('includes/response.php');
$response = new Response();
if (!empty($_GET['id']) && $_GET['id']) {
    $invoiceValues = $response->getReceiptDetails($_GET['id']);
    // 	$invoiceItems = $invoice->getInvoiceItems($_GET['invoice_id']);		
}
$invoiceDate = $invoiceValues[0]['d'];
$use_type = '';
?>

<?php
if ($_GET['type'] == "D") {
    $ty = "DEPOSIT";
    $use_type = 'Dep';
} else if ($_GET['type'] == "W") {
    $ty = "WITHDRAW";
    $use_type = 'With';
} else if ($_GET['type'] == "S") {
    $ty = "SCHOOL FEES PAYMENT";
    $use_type = 'Sch';
}
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/png" href="icons/favicon.png" />
    <link rel="stylesheet" href="style.css">
    <title>UCSCU CBS </title>

    <style>
        .button {
            background-color: #066b12;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        .button2 {
            background-color: #f23607;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <!--<img src="assets/images/kam_logo.jpg" alt="Logo">-->
        <p class="centered"><strong><?php echo $ty; ?> RECEIPT</strong>
            <hr>
            <br>A/C: <?php echo $invoiceValues[0]['acno']; ?>
            <br>Name: <?php echo strtoupper($invoiceValues[0]['name']); ?>
        </p>

        <?php
        if ($_GET['type'] == 'S') :
        ?>
            <p class="centered"><strong> STUDENT DETAILS</strong>
                <hr>
                <br>SNo.: <?php echo strtoupper($invoiceValues[0]['sno']); ?>
                <br>Name: <?php echo strtoupper($invoiceValues[0]['sname']); ?>
                <br>Class: <?php echo strtoupper($invoiceValues[0]['sclass']); ?> &nbsp;&nbsp; Term: <?php echo strtoupper($invoiceValues[0]['sterm']); ?>
            </p>
        <?php endif; ?>

        <p class="centered"><strong>TRXN DETAILS</strong>
            <hr>
            <br>REF : <?php echo $use_type . '-ref-' . $invoiceValues[0]['meth'] . '-' . $_GET['id']  ?>
            <br>Amount : UGX <?php echo $invoiceValues[0]['amount']; ?>
            <br>Reason :<?php echo $invoiceValues[0]['description']; ?>
            <br>Trxn Date : <?php echo $invoiceDate; ?>
        </p>
        <p class="centered">
            <br>Served by:
            <br> <?php echo strtoupper($invoiceValues[0]['auth']); ?>
        </p><br>
        <p class="centered">
            <br>Customer Signature:
            <br> <br> <br> ....................................................
        </p><br>
        <p class="centered">Thanks for saving with Us !
            <br><?php echo $invoiceValues[0]['bank']; ?>
        </p>
    </div>
    <button id="btnPrint" class="hidden-print button">Print</button>
    <br />
    <a href="javascript:;" onclick="history.back()" class="hidden-print button2">Back</a>
    <script src="script.js"></script>
</body>

</html>