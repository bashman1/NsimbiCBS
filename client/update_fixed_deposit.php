<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('add_deposit')) {
    return $permissions->isNotPermitted(true);
}
$title = 'UPDATE FIXED DEPOSIT';
require_once('includes/head_tag.php');
$response = new Response();

if (isset($_POST['submit'])) {
    // $amount = str_replace(",", "", $_POST['amount']);
    // $send_sms = $_POST['send_sms'] ?? 0;
    $res = $response->updateFixedDeposit($_POST);
    if ($res) {
        setSessionMessage(true, 'Fixed Deposit Updated Successfully!');
        // header('location: receipt?id='.$res.'&type=D');
        // exit;
        header('location:fixed_deposit_details.php?id=' . $_POST['fid']);
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to update the deposit.');
        header('location:update_fixed_deposit.php?id=' . $_POST['fid']);
        exit;
    }
}
$member = [];
$client_id = @$_GET['t'] = parsed_id(@$_GET['t']);
if (isset($_GET['t'])) {
    $member = $response->getClientDetails($_GET['t'])[0];
}

$fd_details = $response->getFixedDepDetails($_GET['id'])[0];
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
                                    <a href="search_client" class="btn btn-primary light btn-xs mb-1 "><i class="fa fa-arrow-left"></i> Back</a> | Fixed Deposit Form
                                </h4>


                                <div class="row">
                                    <div class="col-lg-6">
                                        <form method="post" class="submit_with_ajax">
                                            <input type="hidden" name="client" value="<?= $member['userId']; ?>">
                                            <input type="hidden" name="mno" value="<?= $member['accno']; ?>">
                                            <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>">
                                            <input type="hidden" name="fid" value="<?= $_GET['id']; ?>">
                                            <input type="hidden" name="old_amount" value="<?= $fd_details['amount'] ?>">

                                            <?php
                                            if (!$user[0]['branchId']) {
                                                $branches = $response->getBankBranches($user[0]['bankId']);

                                                echo '
                          <div class="form-group">
                         
                              <label class="text-label form-label">Branch *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                             
                                  ';
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        if ($row['id'] == $fd_details['fd_branch']) {
                                                            echo '
                              <option value="' . $row['id'] . '" selected>' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                                        } else {
                                                            echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                                        }
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
                                                <label for="projectName">Fixed Deposit Amount :</label>
                                                <input type="text" value="<?= $fd_details['amount'] ?>" name="amount" min="0" class="form-control comma_separated" required data-type="amount">
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-lg-6 col-6 mb-2 mb-lg-0">
                                                        <label>Fixed Deposit Period</label>
                                                        <input type="text" name="fd_period" class="form-control" value="<?= $fd_details['per'] ?>" required>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6 col-6">
                                                        <label>Period Type</label>
                                                        <select id="period_type" name="period_type" class="form-control" required>

                                                            <option value="m" <?= $fd_details['ptype'] == 'm' ? 'selected' : '' ?>>Months</option>
                                                            <option value="d" <?= $fd_details['ptype'] == 'd' ? 'selected' : '' ?>>Days</option>
                                                            <option value="y" <?= $fd_details['ptype'] == 'y' ? 'selected' : '' ?>>Years</option>
                                                        </select>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-lg-4 ">
                                                        <label>Compounding Frequency *</label>
                                                        <select id="freq" name="freq" class="form-control" required>
                                                            <option value="m" <?= $fd_details['freqtype'] == 'm' ? 'selected' : '' ?>>Monthly</option>
                                                            <option value="q" <?= $fd_details['freqtype'] == 'q' ? 'selected' : '' ?>>Quarterly</option>
                                                            <option value="h" <?= $fd_details['freqtype'] == 'h' ? 'selected' : '' ?>>Half Yearly</option>
                                                            <option value="y" <?= $fd_details['freqtype'] == 'y' ? 'selected' : '' ?>>Annually</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-4  mb-2 mb-lg-0">
                                                        <label>Int Rate (%)/ Annum *</label>
                                                        <input type="text" name="rate" class="form-control" value="<?= $fd_details['int_rate'] ?>" required>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-4 ">
                                                        <label>(WHT) Withholding Tax *</label>
                                                        <input type="text" name="wht" class="form-control" value="<?= $fd_details['wht'] ?>" required>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end form-group-->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-lg-6 col-6 mb-2 mb-lg-0">
                                                        <label>Fixed Deposit Date</label>
                                                        <input type="date" name="record_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($fd_details['open_date'])); ?>" required>
                                                    </div>
                                                    <!--end col-->

                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end form-group-->

                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" rows="5" name="comment" placeholder="<?= $fd_details['fd_notes'] ?>"></textarea>
                                            </div>

                                            <br />
                                            <div class="custom-control custom-checkbox mb-4">
                                                <input type="checkbox" name="auto_payments" value="1" class="custom-control-input" id="auto_payments" data-parsley-multiple="groups" data-parsley-mincheck="2" <?= $fd_details['auto_pay'] ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="auto_payments">Turn on automatic payments</label>
                                                <p class="text-muted mb-3">Post interest on members account every time it's due automatically</p>
                                            </div>
                                            <div class="custom-control custom-checkbox mb-4">
                                                <input type="checkbox" name="auto_close" value="1" class="custom-control-input" id="auto_close" data-parsley-multiple="groups" data-parsley-mincheck="2" <?= $fd_details['auto_close'] ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="auto_close">Automatically Closure at Maturity Date</label>
                                                <p class="text-muted mb-3">Auto post due interest and close the fixed deposit when at maturity</p>
                                            </div>
                                            <input type="hidden" class="form-control" id="deposit_sms" name="send_sms" value="1">

                                            <br /><br />

                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit">Update Deposit</button>
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
                                                        <h3 class="mt-0"><?= is_null($member['acc_balance']??0) ? 0 : number_format($member['acc_balance']??0 - $member['freezed']??0 - $member['min_balance']??0) ?>
                                                        </h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Freezed Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['freezed'] ?? 0) ?>
                                                        </h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Actual Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['acc_balance'] ?? 0); ?></h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Over-Draft</span>
                                                        <h3 class="mt-0"><?= number_format($member['over_draft'] ?? 0); ?></h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Min. A/C Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['min_balance'] ?? 0); ?></h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Fixed Deposits</span>
                                                        <h3 class="mt-0"><a class="text-primary" href=""><?= number_format($member['fixed'] ?? 0); ?></a></h3>
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
                                                        <h5 class="mb-0"><?= $member['name'] ?? '' ?></h5>
                                                        <small class="text-muted">A/C No: <?= $member['accno'] ?? ''; ?>
                                                            | CLIENT TYPE : <?= ($member['actype'] ?? ''); ?></small>
                                                    </div>
                                                    <div class="mb-3 pricingTable1">

                                                        <hr class="hr-dashed">
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">SAVING PRODUCT: </span>
                                                                <h6 class="mt-0"><?= $member['savingaccount'] ?? ''; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">A/C NAME: </span>
                                                                <h6 class="mt-0"><?= $member['name'] ?? ''; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">CURRENCY: </span>
                                                                <h6 class="mt-0"><?= 'UGANDA SHILLINGS'; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">SAVINGS OFFICER: </span>
                                                                <h6 class="mt-0"><?= $member['savings_officer'] ?? ''; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">LAST TRANSACTED: </span>
                                                                <h6 class="mt-0"><?= $member['last_transaction'] ?? ''; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">STATUS: </span>
                                                                <h6 class="mt-0"><?= $member['status'] ?? ''; ?>
                                                                </h6>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <a href="<?= 'client_profile_page.php?id=' . $member['userId'] ?? 0; ?>" class="btn btn-primary light btn-xs mb-1">View Client's
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

        <script>
            $(document).ready(function() {
                $("input[data-type='amount']").keyup(function(event) {
                    // skip for arrow keys
                    if (event.which >= 37 && event.which <= 40) {
                        event.preventDefault();
                    }
                    var $this = $(this);
                    var num = $this.val().replace(/,/gi, "");
                    var num2 = num.split(/(?=(?:\d{3})+$)/).join(",");
                    // console.log(num2);
                    // the following line has been simplified. Revision history contains original.
                    $this.val(num2);
                });
            });

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

            // $('body').on('submit', 'form', function(event) {
            //     $(this).find('.btn-submit').text('Processing...').prop('disabled',true);
            //     return true;
            // });
        </script>


</body>

</html>