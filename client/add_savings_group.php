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
    $res = $response->createSavingAccount($_POST['name'], $_POST['ucode'], $_POST['rate'], $_POST['interestDuration'], $user[0]['bankId'], $_POST['interestDisbursement'], $_POST['withdraw'], $_POST['pform'], $_POST['account_id'], $_POST['parent_id'],$_POST['opening']);
    if ($res) {
        setSessionMessage(true, 'Saving Product Created Successfully!');
        header('location:all_saving_groups.php');
        exit;
    } else {
        setSessionMessage(false, 'Saving Product not Created! Try again.');
        header('location:all_saving_groups.php');
        exit;
    }
}

$title = 'CREATE SAVING PRODUCT';
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Savings Accounts</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">New Savings Account</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Create New Savings Product
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">

                                        <div class="mb-3">
                                            <label class="text-label form-label">Unique Code*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="ucode">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Account Name*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="name">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Interest Rate *</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="rate">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Interest Rate is per *</label>

                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="interestDuration" style="display: none;">
                                                <option selected></option>
                                                <option value="0">None</option>
                                                <option value="1">Annum</option>
                                                <option value="2">Monthly</option>
                                                <option value="3">Daily</option>
                                                <option value="4">Weekly</option>
                                                <option value="5">Quarterly</option>
                                                <option value="6">Twice a year (After every 6 Months)</option>

                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Accumulated Interest is Disbursed Per *</label>

                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="interestDisbursement" style="display: none;">
                                                <option selected></option>
                                                <option value="0">None</option>
                                                <option value="1">Annum</option>
                                                <option value="2">Monthly</option>
                                                <option value="3">Daily</option>
                                                <option value="4">Weekly</option>
                                                <option value="5">Quarterly</option>
                                                <option value="6">Twice a year (After every 6 Months)</option>

                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-label form-label">Minimum Account Balance (Affects Withdraw Limit)*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="withdraw">
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-label form-label">Opening Balance *</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="opening" value="0">
                                        </div>

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

                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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
        <?php
        include('includes/bottom_scripts.php');
        ?>
        <!-- <script src="./js/styleSwitcher.js"></script> -->
        <script>
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