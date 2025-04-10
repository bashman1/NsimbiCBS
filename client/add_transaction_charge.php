<?php
include('../backend/config/session.php');
?>
<?php


include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $feeAmount = 0;

    if ($_POST['gridRadios'] == 'rate') {
        $feeAmount = $_POST['frate'];
    } else {
        $feeAmount = $_POST['famount'];
    }
    $res = $response->createTxnCharge($_POST['name'], $_POST['gridRadios'], $_POST['apply_to'], $feeAmount, $user[0]['bankId'], $_POST['account_id']);
    if ($res) {
        setSessionMessage(true, 'Transaction Charge Set Successfully!');
        header('location:fees_tab.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Trxn Charge not set. Try again');
        header('location:fees_tab.php');
        exit;
    }
}

if (isset($_POST['continue'])) {
    $name = $_POST['name'];
    $apply_to = $_POST['apply_to'];
    $counter = $_POST['counter'];
    header('location:complete_add_trans_charge?name=' . $name . '&apply=' . $apply_to . '&count=' . $counter);
}

$title = 'TRANSACTION CHARGES';
require_once('includes/head_tag.php');

$accounts = $response->getSubAccounts2($_SESSION['user']['branchId'], $_SESSION['user']['bankId']);
?>


<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php include('includes/preloader.php'); ?>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php
        include('includes/nav_bar.php');
        include('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Create New Transaction Charge
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="addChargeForm">

                                        <div class="mb-3">
                                            <label class="text-label form-label">Charge Name*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="name" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Charge Applies on*</label>

                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="apply_to" style="display: none;" required>
                                                <option value="withdraw" selected>Withdraw</option>
                                                <option value="deposit">Deposit</option>
                                                <option value="transfer">Savings Transfer</option>
                                                <option value="school_pay">School Fees Payment</option>

                                            </select>
                                        </div>
                                        <br /><br /><br />
                                        <div class="mb-3">
                                            <label class="text-label form-label">Charge Varies with Transaction
                                                Amount*</label>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="vary" value="vary_yes" onClick="vary_yes()">
                                                    <label class="form-check-label">
                                                        Yes (Charge varies based on the amount being transacted)
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="vary" value="vary_no" onClick="vary_no()" checked>
                                                    <label class="form-check-label">
                                                        No (Applies to all transactions regardless of the amount)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br />
                                        <div id="vary_no">
                                            <div class="mb-3">
                                                <label class="text-label form-label">Charge Type*</label>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="gridRadios" value="rate" id="rate" onClick="setDown()" checked>
                                                        <label class="form-check-label">
                                                            Rate
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="gridRadios" value="fixed" id="fixed" onclick="setUp()">
                                                        <label class="form-check-label">
                                                            Fixed Amount
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br />
                                            <div class="mb-3" id="headd">
                                                <label class="text-label form-label">Charge Rate *</label>

                                                <input type="number" class="form-control input-rounded" placeholder="" name="frate">
                                            </div>
                                            <div class="mb-3" id="headf" style="display: none;">
                                                <label class="text-label form-label">Charge Fixed Amount *</label>

                                                <input type="number" class="form-control input-rounded" placeholder="" name="famount">
                                            </div>
                                        </div>

                                        <div class="mb-3" id="vary_yes" style="display:none;">
                                            <label class="text-label form-label">How many ranges are they*</label>
                                            <input type="number" class="form-control input-rounded" placeholder="" name="counter" min="1" value="1" id="counter">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Select Associated Chart Account *</label>

                                            <select id="oscategory" class="form-control" name="account_id" required>
                                                <option> Select </option>
                                                <?php foreach ($accounts as $account) { ?>
                                                    <option value="<?= $account['id'] ?>">
                                                        <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>



                                        <br /><br /><br />
                                        <div id="no_btn">

                                            <button type="submit" name="submit" class="btn btn-primary">Create
                                                Charge</button>
                                        </div>
                                        <div id="yes_btn" style="display:none;">

                                            <button type="submit" name="continue" class="btn btn-primary">Continue</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--**********************************
            Content body end
        ***********************************-->


            <!--**********************************
            Footer start
        ***********************************-->
            <?php include('includes/footer.php'); ?>
            <!--**********************************
            Footer end
        ***********************************-->

            <!--**********************************
           Support ticket button start
        ***********************************-->

            <!--**********************************
           Support ticket button end
        ***********************************-->


        </div>
        <!--**********************************
        Main wrapper end
    ***********************************-->

        <!--**********************************
        Scripts
    ***********************************-->
        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>
        <script>
            function setUp() {
                var x = document.getElementById("headd");
                var y = document.getElementById("headf");
                y.style.display = "block";

                x.style.display = "none";
            }


            function setDown() {
                var x = document.getElementById("headd");
                var y = document.getElementById("headf");
                x.style.display = "block";

                y.style.display = "none";
            }

            function vary_yes() {
                var yes = document.getElementById("vary_yes");
                var no = document.getElementById("vary_no");
                var yes_btn = document.getElementById("yes_btn");
                var no_btn = document.getElementById("no_btn");
                yes.style.display = "block";
                yes_btn.style.display = "block";

                no.style.display = "none";
                no_btn.style.display = "none";
            }

            function vary_no() {
                var yes = document.getElementById("vary_yes");
                var no = document.getElementById("vary_no");
                yes.style.display = "none";
                yes_btn.style.display = "none";

                no.style.display = "block";
                no_btn.style.display = "block";
            }
        </script>



</body>

</html>