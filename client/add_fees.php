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

if (isset($_POST['submit'])) {
    $feeAmount = 0;
    if ($_POST['gridRadios'] == 'INTEREST_RATE') {
        $feeAmount = $_POST['frate'];
    } else {
        $feeAmount = $_POST['famount'];
    }
    $res = $response->createFee($_POST['name'], $_POST['gridRadios'], $_POST['ptype'], $feeAmount, $user[0]['bankId'], $_POST['pform'], $_POST['account_id'], $_POST['parent_id']);
    if ($res) {
        setSessionMessage(true, 'Fees Created Successfully!');
        header('location:fees_tab.php');
        exit;
    } else {
        setSessionMessage(false, 'Fees Creation failed');
        header('location:fees_tab.php');
        exit;
    }
}

$title = 'ADD FEES';
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
                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Fees</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">New Fee</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Create New Fee
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Fee Name*</label>
                                            <input type="text" class="form-control input-rounded" placeholder="" name="name">
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-label form-label">Fee Type*</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gridRadios" value="INTEREST_RATE" id="rate" onClick="setDown()" checked>
                                                    <label class="form-check-label">
                                                        Rate
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gridRadios" value="FIXED_AMOUNT" id="fixed" onclick="setUp()">
                                                    <label class="form-check-label">
                                                        Fixed Amount
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br />
                                        <div class="mb-3" id="headd">
                                            <label class="text-label form-label">Fees Rate *</label>
                                            <input type="text" class="form-control input-rounded" placeholder="" name="frate">
                                        </div>
                                        <div class="mb-3" id="headf" style="display: none;">
                                            <label class="text-label form-label">Fees Fixed Amount *</label>
                                            <input type="number" class="form-control input-rounded" placeholder="" name="famount">
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-label form-label">Payment Type*</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ptype" value="UP_FRONT" checked>
                                                    <label class="form-check-label">
                                                        Upfront
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ptype" value="DISBURSEMENT">
                                                    <label class="form-check-label">
                                                        On Disbursement
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br />
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="pform" value="exist" onClick="setExist()" checked>
                                                    <label class="form-check-label">
                                                        Select Existing Chart Account
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="pform" value="create" onClick="setCreate()">
                                                    <label class="form-check-label">
                                                        Create new Chart Account for this fee
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br />
                                        <div class="mb-3" id="pc">
                                            <label class="text-label form-label">Select Chart Account *</label>

                                            <select id="oscategory" class="form-control" name="account_id" required>
                                                <option> Select </option>
                                                <?php foreach ($accounts as $account) { ?>
                                                    <option value="<?= $account['id'] ?>">
                                                        <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="mb-3" id="pc1" style="display: none;">
                                            <label class="text-label form-label">Select Parent Chart Account *</label>

                                            <select id="ocategory" class="form-control" name="parent_id" required>
                                                <option> Select </option>
                                                <?php foreach ($accounts as $account) { ?>
                                                    <option value="<?= $account['id'] ?>">
                                                        <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>


                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Create Fee</button>
                                        <!-- </div> -->

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
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>


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


            function setExist() {
                var x = document.getElementById("pc");
                var y = document.getElementById("pc1");
                x.style.display = "block";
                y.style.display = "none";
            }


            function setCreate() {
                var x = document.getElementById("pc");
                var y = document.getElementById("pc1");

                x.style.display = "none";
                y.style.display = "block";
            }
        </script>



</body>

</html>