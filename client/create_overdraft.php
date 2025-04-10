<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('create_overdraft_application')) {
    return $permissions->isNotPermitted(true);
}
require_once('includes/head_tag.php');
$response = new Response();

if (isset($_POST['submit'])) {

    $res = $response->createOverDraft($_POST);
    if ($res) {
        setSessionMessage(true, 'Over-Draft Created Successfully!');
        header('location:search_client_over_draft.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to Create Over-Draft.');
        header('location:search_client_over_draft.php');
        exit;
    }
}
$member = [];
$client_id = @$_GET['id'] = parsed_id(@$_GET['id']);
if (isset($_GET['id'])) {
    $member = $response->getClientDetails($_GET['id'])[0];
}

$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);
$sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">


                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Over Draft Form
                                </h4>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <form method="post" class="submit_with_ajax">
                                            <input type="hidden" name="client" value="<?= $member['userId']; ?>">
                                            <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>">

                                            <?php
                                            if (!$user[0]['branchId']) {
                                                $branches = $response->getBankBranches($user[0]['bankId']);

                                                echo '
                          <div class="form-group">
                         
                              <label class="text-label form-label">Associated Branch *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                             
                                  ';
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                                    }
                                                } else {
                                                    echo '
                              <option readonly>No Branches Added yet</option>
                              ';
                                                }

                                                echo
                                                '
                          
                              </select>
                          </div>
                       
                          
                          ';
                                            } else {
                                                echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label>Select Over-draft Principal Journal Account: </label>
                                                <select name="main_acc" class="form-control " id="journalacc">
                                                    <option value="">Select....</option>
                                                    <?php

                                                    if ($sub_accs) {

                                                        foreach ($sub_accs as $acc) {
                                                            if ($acc['type'] == 'ASSETS') {

                                                                echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  Branch: ' . $acc['branch'] . ' -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Select Over-draft Interest Journal Account: </label>
                                                <select name="income_id" class="form-control " id="journalacc">
                                                    <option value="">Select....</option>
                                                    <?php

                                                    if ($sub_accs) {

                                                        foreach ($sub_accs as $acc) {
                                                            if ($acc['type'] == 'INCOMES') {

                                                                echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  Branch: ' . $acc['branch'] . ' -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>


                                            <!-- <div class="form-group" id="product">
                                                <label>Select Over-Draft Product:</label>
                                                <select id="over_product" name="product" class="form-control product">

                                                    <?php
                                                    // $over_draft_products = $response->getOverDraftProducts($user[0]['bankId'], $user[0]['branchId']);
                                                    // if ($over_draft_products) {

                                                    //     foreach ($over_draft_products as $b_acc) {
                                                    //         echo '<option value="' . $b_acc['id'] . '" data-duration="' . $b_acc['period_type'] . '">' . $b_acc['name'] . ' - Max. Amount ' . number_format($b_acc['max_amount']) . ' - Interest: ' . $b_acc['interest'] . ' - Penalty: ' . $b_acc['penalty'] . '</option>';
                                                    //     }
                                                    // }
                                                    ?>
                                                </select>
                                            </div> -->

                                            <div class="form-group">
                                                <label for="projectName">Over-Draft Amount :</label>
                                                <input type="text" value="0" name="amount" min="0" class="form-control comma_separated" required data-type="amount">
                                            </div>
                                            <div class="form-group">
                                                <label for="projectName">Daily Interest Rate (%) :</label>
                                                <input type="text" value="0" name="daily_rate" min="0" class="form-control" required>
                                            </div>
                                            <!--end form-group-->
                                            <div class="form-group">

                                                <label>Record Date</label>
                                                <input type="date" name="record_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required max="<?= date('Y-m-d') ?>">

                                            </div>
                                            <!--end form-group-->

                                            <div class="form-group account_no_insert">
                                                <label>Over Draft Period ( in Days ) <i>*</i>: </label>
                                                <input id="depositor_name" type="number" value="" name="period" required="" class="form-control" min="0">
                                            </div>

                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" rows="5" name="comment" placeholder="write here.."></textarea>
                                            </div>

                                            <br />
                                            <input type="hidden" class="form-control" id="deposit_sms" name="send_sms" value="1">

                                            <br /><br />

                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit">Process
                                                Transaction</button>
                                            <!--end form-->
                                    </div>
                                    <!--end col-->

                                    <div class="col-lg-6 align-self-center">
                                        <div class="card">
                                            <div class="card-body btc-price">

                                                <h4 class="mt-0 header-title">Account Balance</h4>
                                                <p class="text-muted mb-3">Summary</p>

                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Available Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['acc_balance'] - $member['freezed']) ?>
                                                        </h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Freezed Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['freezed']) ?>
                                                        </h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Actual Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['acc_balance']); ?></h3>
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Over-Draft</span>
                                                        <h3 class="mt-0"><?= number_format($member['over_draft']); ?></h3>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="text-center">
                                                    <div class="met-profile-main-pic">
                                                        <img src="<?= is_null($member['image']) ? 'icons/favicon.png' : $member['image'] ?>" onerror="this.onerror=null; this.src='icons/favicon.png'" alt="" height="100" width="100" class="rounded-circle">
                                                    </div>

                                                    <div class="">
                                                        <h5 class="mb-0"><?= $member['name'] ?></h5>
                                                        <small class="text-muted">A/C No: <?= $member['accno']; ?>
                                                            | CLIENT TYPE : <?= ($member['actype']); ?></small>
                                                    </div>
                                                    <div class="mb-3 pricingTable1">

                                                        <hr class="hr-dashed">
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">SAVING PRODUCT: </span>
                                                                <h6 class="mt-0"><?= $member['savingaccount']; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">A/C NAME: </span>
                                                                <h6 class="mt-0"><?= $member['name']; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">CURRENCY: </span>
                                                                <h6 class="mt-0"><?= 'UGANDA SHILLINGS'; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">SAVINGS OFFICER: </span>
                                                                <h6 class="mt-0"><?= $member['savings_officer']; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">LAST TRANSACTED: </span>
                                                                <h6 class="mt-0"><?= $member['last_transaction']; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">STATUS: </span>
                                                                <h6 class="mt-0"><?= $member['status']; ?>
                                                                </h6>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <a href="<?= 'client_profile_page.php?id=' . $member['userId']; ?>" class="btn btn-primary light btn-xs mb-1">View Client's
                                                        Profile</a>
                                                </div>
                                            </div>
                                            <!--end card-body-->
                                        </div>


                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->


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



</body>

</html>