<html>

<head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>UCSCU CBS </title>
    <link rel="shortcut icon" type="image/png" href="icons/favicon.png" />
    <!-- Add icon library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<style>
    body {
        text-align: center;
        padding: 40px 0;
        background: #EBF0F5;
    }

    h1 {
        color: #091394;
        font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
        font-weight: 900;
        font-size: 40px;
        margin-bottom: 10px;
    }

    p {
        color: #404F5E;
        font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
        font-size: 20px;
        margin: 0;
    }

      i {
        color: #091394;
        /*font-size: 1200px;*/
        line-height: 900px;
        margin-left: -15px;
        margin-top: 40%;
    }

    .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
    }

    .button {
        background-color: #091394;
        /* Green */
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

<body>
    <div class="card">
        <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
          <h1> <i class="fa fa-spinner fa-spin"></i></h1>
        </div>
        <h1>Processing...</h1>
       <p>Click Submit after confirming the transaction on your Phone </p>
        <button class="button"><a href="finish_mm.php?phone=<?php echo $_GET['phone'];?>&tid=<?php echo $_GET['tid'];?>&amount=<?php echo $_GET['amount'];?>&acc_no=<?php echo $_GET['acc'];?>&reason=<?php echo $_GET['reason'];?>&acc_name=<?php echo $_GET['name'];?>&bal=<?= $_GET['bal']?>" style="color:white !important;">Submit</a></button>
    </div>
</body>

</html>