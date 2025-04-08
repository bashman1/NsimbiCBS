<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_all_deposits')) {
    return $permissions->isNotPermitted(true);
}
$title = 'FIXED DEPOSIT PROFILE';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();


$fd_details = $response->getFixedDepDetails($_GET['id'])[0];
// $fd_sch_data = $response->getFixedDepSchData($_GET['id'], $fd_details['amount'], $fd_details['int_rate'], $fd_details['period'], $fd_details['ptype'], $fd_details['freqtype'])[0];
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


                <div class="row">


                    <div class="card">
                        <div class="card-body">

                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Fixed Deposit Profile

                            <hr class="hr-dashed">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="mt-0 header-title">Account Details</h4>
                                            <p class="text-muted mb-3"></p>
                                            <div class="text-center">
                                                <div class="met-profile-main-pic">
                                                    <img src="<?= is_null($fd_details['client']['profilePhoto']) ? 'icons/favicon.png' : $fd_details['client']['profilePhoto'] ?>" onerror="this.onerror=null; this.src='icons/favicon.png'" alt="" height="100" width="100" class="rounded-circle">
                                                </div>

                                                <div class="">
                                                    <h5 class="mb-0"><?= $fd_details['client']['firstName'] . ' ' . $fd_details['client']['lastName'] ?></h5>
                                                    <small class="text-muted">Mem No: <?= $fd_details['client']['membership_no'] ?> | OLD NO : <?= $fd_details['client']['old_membership_no'] ?></small>
                                                </div>

                                                <div class="btc-price">

                                                    <hr class="hr-dashed">
                                                    <p class="text-muted mb-3">Summary</p>

                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <span class="text-muted">Principal</span>
                                                            <h3 class="mt-0">UGX <?= number_format($fd_details['amount']) ?></h3>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <span class="text-muted">Interest Paid</span>
                                                            <h3 class="mt-0">UGX <span id="int_top"><?= number_format($fd_details['int_paid']) ?></span></h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3 pricingTable1">
                                                    <hr class="hr-dashed">

                                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                                        <li><b>A/C TYPE: </b><?= $fd_details['client']['name'] ?></li>
                                                        <li><b>A/C: </b><?= $fd_details['client']['membership_no'] ?></li>
                                                        <li><b>A/C NAME: </b><?= $fd_details['client']['firstName'] . ' ' . $fd_details['client']['lastName'] ?></li>
                                                        <li><b>CURRENCY: </b>UGANDA SHILLINGS</li>
                                                        <li><b>STATUS: </b><?= $fd_details['client']['status'] ?></li>
                                                        <li><b>LAST TRANSACTED: </b><?= $fd_details['last_transaction']; ?></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </div>

                                <div class="col-md-8">

                                    <div class="card">
                                        <div class="card-body">

                                            <div class="met-profile">
                                                <div class="row">
                                                    <div class="col-lg-6">

                                                        <h4 class="mt-0 header-title">Fixed Deposit Details</h4>
                                                        <p class="text-muted mb-3"></p>
                                                        <ul class="list-unstyled personal-detail">
                                                            <li class="mt-2"><i class="ti-money mr-2 text-info font-22 align-middle"></i> <b>Amount Fixed </b>: <?= number_format($fd_details['amount']) ?></li>

                                                            <li class="mt-2"><i class="ti-check-box mr-2 text-info font-22 align-middle"></i> <b>Interest (%) </b>: <?= number_format($fd_details['int_rate']) ?> %</li>

                                                            <li class="mt-2"><i class="ti-check-box mr-2 text-info font-22 align-middle"></i> <b>WHT Tax (%) </b>: <?= number_format($fd_details['wht']) ?> %</li>

                                                            <li class="mt-2"><i class="ti-calendar mr-2 text-info font-22 align-middle"></i> <b> Period </b>: <?= $fd_details['period'] ?></li>

                                                            <li class="mt-2"><i class="ti-check-box mr-2 text-info font-22 align-middle"></i> <b>Interest Frequency </b>: <?= $fd_details['freq'] ?></li>

                                                            <li class="mt-2"><i class="ti-calendar mr-2 text-info font-22 align-middle"></i> <b> Deposit Date </b>: <?= normal_date($fd_details['open_date']) ?></li>
                                                            <li class="mt-2"><i class="ti-calendar mr-2 text-info font-22 align-middle"></i> <b> Maturity Date </b>: <?= normal_date($fd_details['close_date']) ?></li>

                                                            <li class="mt-2"><i class="ti-calendar mr-2 text-info font-22 align-middle"></i> <b> Descr </b>: <?= $fd_details['fd_notes'] ?></li>
                                                        </ul>

                                                    </div>
                                                    <!--end col-->

                                                    <div class="col-lg-6">
                                                        <h4 class="mt-0 header-title">Progress Summary</h4>
                                                        <p class="text-muted mb-3"></p>

                                                        <ul class="list-unstyled personal-detail">

                                                            <li class="mt-2"><i class="ti-money mr-2 text-info font-22 align-middle"></i> <b>Interest Expected </b>: <span id="int_expect"></span></li>
                                                            <li class="mt-2"><i class="ti-money mr-2 text-info font-22 align-middle"></i> <b>Interest Due </b>: <span id="int_taken"><?= number_format($fd_details['int_due']) ?></span></li>

                                                            <li class="mt-2"><i class="ti-money mr-2 text-info font-22 align-middle"></i> <b>WHT Tax Due </b>: <span id="wht_taken"><?= number_format($fd_details['wht_due']) ?></span></li>


                                                            <li class="mt-2"><i class="ti-money mr-2 text-info font-22 align-middle"></i> <b>Interest Paid Out </b>: <?= number_format($fd_details['int_paid']) ?></li>

                                                            <li class="mt-2"><i class="ti-money mr-2 text-info font-22 align-middle"></i> <b>WHT Tax Paid Out </b>: <?= number_format($fd_details['wht_paid']) ?></li>
                                                            <li class="mt-2"><i class="ti-calendar mr-2 text-info font-22 align-middle"></i> <b>Closure Date </b>: <?= normal_date($fd_details['closure_date']) ?></li>

                                                            <li class="mt-2"><i class="ti-check-box mr-2 text-info font-22 align-middle"></i> <b> Progress </b>:
                                                                <br />
                                                                <?php
                                                                if ($fd_details['tot_int'] > 0) {
                                                                    $percentage_completed = round(($fd_details['tot_int'] / $fd_details['int_paid']) * 100, 2);
                                                                } else {
                                                                    $percentage_completed = round(($fd_details['int_paid'] / 1) * 100, 2);
                                                                }

                                                                $percentage_completed = $percentage_completed > 100  ? 100 : $percentage_completed;
                                                                ?>
                                                                <!-- Percentage Completed -->
                                                                <div class="progress finbyz-fadeinup" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="<?= $percentage_completed ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percentage_completed ?>%"> <?= $percentage_completed ?>%
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>

                                                    </div>
                                                    <!--end col-->

                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end f_profile-->
                                            <br /><br />
                                            <div class="profile-skills mb-5">
                                                <a href="fixed_deposit_cert.php?id=<?= $fd_details['id'] ?>" class="btn btn-primary light btn-xs mb-1" target="_blank">View Certificate</a>
                                                <?php
                                                if ($fd_details['fd_st'] == 1) {
                                                    echo ' 
                                                    <a href="undo_fixed_closure.php?id=' . $fd_details['id'] . '" class="btn btn-danger light btn-xs mb-1" target="_blank">Undo A/C Closure</a>
                                                    ';
                                                }
                                                ?>
                                                <?php
                                                if ($fd_details['fd_st'] == 0) :
                                                ?>
                                                    <a href="update_fixed_deposit.php?id=<?= $fd_details['id'] ?>&t=<?= $fd_details['user_id'] ?>" class="btn btn-primary light btn-xs mb-1">Update Fixed Deposit</a>
                                                    <?php
                                                    $currentDaten = strtotime(date('Y-m-d'));
                                                    $startDate = strtotime(date('Y-m-d', strtotime($fd_details['close_date'])));


                                                    if ($startDate <= $currentDaten) {
                                                        echo ' <a class="btn btn-primary light btn-xs mb-1 me-2" data-bs-toggle="modal" data-bs-target="#closefdModal">Close Account</a>';
                                                    } else {
                                                        echo ' <a class="btn btn-primary light btn-xs mb-1 me-2" data-bs-toggle="modal" data-bs-target="#closefdModal2">Close Before Maturity</a>';
                                                    }

                                                    ?>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="closefdModal">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Close Fixed Deposit A/C</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="<?= BACKEND_BASE_URL ?>Accounting/close_fixed_account.php" class="custom-form" id="close_fixed_acc_form" data-reload-page="1" data-confirm-action="1">
                                                                        <div class="row">

                                                                            <input type="hidden" name="client_id" value="<?= @$fd_details['user_id']; ?>">
                                                                            <input type="hidden" name="fd_id" value="<?= @$fd_details['id']  ?>">
                                                                            <input type="hidden" name="fd_branch" value="<?= @$fd_details['fd_branch']  ?>">
                                                                            <input type="hidden" name="int_take" value="0" id="int_take2">
                                                                            <input type="hidden" name="wht_take" value="0" id="wht_take2">
                                                                            <input type="hidden" name="fd_amount" value="<?= @$fd_details['amount'] ?>">

                                                                            <div class="col-md-12">
                                                                                Confirm & submit the form below to update Close this Fixed Deposit A/C.
                                                                                <br>

                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-floating">
                                                                                    <label for="">Amount Deposited: <b><?= number_format(@$fd_details['amount'] ?? 0); ?></b></label>
                                                                                </div>
                                                                            </div><br /><br />
                                                                            <div class="col-md-12">
                                                                                <div class="form-floating">
                                                                                    <label for="">Interest Due: <b id="int_take"></b></label>
                                                                                </div>
                                                                            </div><br /><br />
                                                                            <div class="col-md-12">
                                                                                <div class="form-floating">
                                                                                    <label for="">WHT Due: <b id="wht_take"></b></label>
                                                                                </div>
                                                                            </div><br /><br />
                                                                            <div class="col-md-12">
                                                                                <div class="form-floating">
                                                                                    <label for="">Total Amount to be Disbursed (Via Client's Savings A/C): <b id="total_take"></b></label>
                                                                                </div>
                                                                            </div><br /><br />

                                                                            <!-- <br /> -->
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label class="text-label form-label"> </label>
                                                                                    <button type="submit" class="btn btn-primary form-control">Close Account </button>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <!-- <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button> -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="closefdModal2">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Close Fixed Deposit A/C Before Maturity</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="<?= BACKEND_BASE_URL ?>Accounting/close_fixed_account_before_maturity.php" class="custom-form" id="close_fixed_acc_form" data-reload-page="1" data-confirm-action="1">
                                                                        <div class="row">

                                                                            <input type="hidden" name="client_id" value="<?= @$fd_details['user_id']; ?>">
                                                                            <input type="hidden" name="fd_id" value="<?= @$fd_details['id']  ?>">
                                                                            <input type="hidden" name="fd_branch" value="<?= @$fd_details['fd_branch']  ?>">

                                                                            <input type="hidden" name="fd_amount" value="<?= @$fd_details['amount'] ?>">

                                                                            <div class="col-md-12">
                                                                                Confirm & submit the form below to update Close this Fixed Deposit A/C.
                                                                                <br>

                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-floating">
                                                                                    <label for="">Amount Deposited: <b><?= number_format(@$fd_details['amount'] ?? 0); ?></b></label>

                                                                                </div>
                                                                                <br />
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-floating">
                                                                                    <label for="">Interest Offered: </label>
                                                                                    <input type="text" value="0" name="int_given" class="form-control" />
                                                                                </div>
                                                                                <br />
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="form-floating">
                                                                                    <label for="">WHT Charged (Specify Amount not %ge): </label>
                                                                                    <input type="text" value="0" name="wht_charged" class="form-control" />
                                                                                </div>
                                                                                <br />
                                                                            </div>
                                                                            <!-- <br /><br /> -->

                                                                            <!-- <br /> -->
                                                                            <div class="col-md-12">
                                                                                <div class="form-group">
                                                                                    <label class="text-label form-label"> </label>
                                                                                    <button type="submit" class="btn btn-primary form-control">Close Account </button>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <!-- <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button> -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="delete_fixed_acc.php" class="btn btn-danger light btn-xs mb-1">Delete Fixed Account</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                    <!--end card-->


                                    <!--end form-->



                                </div>
                                <!--end col-->
                            </div>
                            <div class="card" style="padding: 50px !important;">
                                <div class="card-title">
                                    <h4 class="mt-0 header-title"> Fixed Deposit Schedule
                                        <span class=""> </span>
                                    </h4>
                                </div>
                                <div class="card-body">

                                    <div class="table-responsive">
                                        <table id="fd_register" class="table table-striped export-datatable" style="min-width: 845px;" data-title="Fixed Deposit Statement">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Opening Balance</th>
                                                    <th>Interest</th>
                                                    <th>WHT Tax</th>
                                                    <th>Closing Balance</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php


                                                $amount = str_replace(",", "", $fd_details['amount']);
                                                $interest_rate = $fd_details['int_rate'];
                                                $period = $fd_details['per'];
                                                $period_type = $fd_details['ptype'];
                                                $frequency = $fd_details['freqtype'];
                                                $wht_rate = $fd_details['wht'];

                                                $interest = 0;
                                                $freq_days = 1;
                                                $total_period_interest = 0;
                                                $inst_interest = 0;
                                                $no_times = 0;


                                                if ($period_type == 'y') {
                                                    $period_no_days = $period * 360;
                                                    $daily_rate  = $interest_rate / 36000;
                                                    $daily_interest = $amount * $daily_rate;
                                                    $total_period_interest = round($daily_interest * $period_no_days);

                                                    if ($frequency == 'm') {
                                                        $freq_days = 30;
                                                    } else  if ($frequency == 'q') {
                                                        $freq_days = 90;
                                                    } else  if ($frequency == 'h') {
                                                        $freq_days = 180;
                                                    } else  if ($frequency == 'y') {
                                                        $freq_days = 360;
                                                    }

                                                    $no_times = $period_no_days / $freq_days;

                                                    $inst_interest = round($total_period_interest / $no_times);
                                                } else if ($period_type == 'm') {
                                                    $period_no_days = $period * 30;
                                                    $daily_rate  = $interest_rate / 36000;
                                                    $daily_interest = $amount * $daily_rate;
                                                    $total_period_interest = round($daily_interest * $period_no_days);

                                                    if ($frequency == 'm') {
                                                        $freq_days = 30;
                                                    } else  if ($frequency == 'q') {
                                                        $freq_days = 90;
                                                    } else  if ($frequency == 'h') {
                                                        $freq_days = 180;
                                                    } else  if ($frequency == 'y') {
                                                        $freq_days = 360;
                                                    }

                                                    $no_times = $period_no_days / $freq_days;

                                                    $inst_interest = round($total_period_interest / $no_times);
                                                } else if ($period_type == 'd') {
                                                    $period_no_days = $period;
                                                    $daily_rate  = $interest_rate / 36000;
                                                    $daily_interest = $amount * $daily_rate;
                                                    $total_period_interest = round($daily_interest * $period_no_days);

                                                    if ($frequency == 'm') {
                                                        $freq_days = 30;
                                                    } else  if ($frequency == 'q') {
                                                        $freq_days = 90;
                                                    } else  if ($frequency == 'h') {
                                                        $freq_days = 180;
                                                    } else  if ($frequency == 'y') {
                                                        $freq_days = 360;
                                                    }

                                                    $no_times = $period_no_days / $freq_days;

                                                    $inst_interest = round($total_period_interest / $no_times);
                                                }


                                                // end calculation



                                                $currentDate = strtotime(date('Y-m-d'));
                                                $count = 1;
                                                $open_bal = $amount;
                                                $int = $inst_interest;
                                                $wht = $int * ($wht_rate / 100);
                                                $usedate = $fd_details['open_date'];
                                                $btn = '';
                                                if ($no_times < 1 && $no_times > 0) {
                                                    $no_times = 1;
                                                }
                                                $nn = $no_times;
                                                while ($no_times) {
                                                    if ($fd_details['freqtype'] == 'y') {
                                                        $usedate = date('Y-m-d', strtotime($usedate . ' + 365 days'));
                                                    } else if ($fd_details['freqtype']  == 'm') {
                                                        $usedate = date('Y-m-d', strtotime($usedate . ' + 30 days'));
                                                    } else if ($fd_details['freqtype']  == 'q') {
                                                        $usedate = date('Y-m-d', strtotime($usedate . ' + 120 days'));
                                                    } else if ($fd_details['freqtype']  == 'h') {
                                                        $usedate = date('Y-m-d', strtotime($usedate . ' + 180 days'));
                                                    }

                                                    $startDate = strtotime(date('Y-m-d', strtotime($usedate)));

                                                    if ($fd_details['fd_st'] == 1) {
                                                        $btn = '<span class="badge light badge-success">Paid</span>';
                                                    } else {
                                                        if ($startDate <= $currentDate) {
                                                            $btn = '<span class="badge light badge-warning">Due</span>';
                                                        } else {
                                                            $btn = '<span class="badge light badge-danger">Running</span>';
                                                        }
                                                    }

                                                    echo '
                                                <tr>
                                                    <td>' . $count . '</td>
                                                    <td>' . normal_date($usedate) . '</td>
                                                    <td>' . number_format($open_bal) . '</td>
                                                    <td>' . number_format($int) . '</td>
                                                    <td>' . number_format($wht) . '</td>
                                                    <td>' . number_format($open_bal + $int) . '</td>
                                                    <td>' . $btn . '</td>
                                                    <td></td>
                                                </tr>
    
                                                        ';

                                                    $open_bal = $open_bal + $int;
                                                    $count++;
                                                    $no_times--;
                                                }




                                                ?>



                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <script>
                                    let mint_expect = <?= $int * $nn ?>;
                                    let mwht_take = <?= $wht * $nn ?>;
                                    let mtotal_take = <?= (($int * $nn) + $amount) - ($wht * $nn) ?>;

                                    // console.log(mint_expect);
                                    document.getElementById('total_take').innerHTML = mtotal_take;
                                    document.getElementById('int_expect').innerHTML = mint_expect;
                                    document.getElementById('int_take').innerHTML = mint_expect;
                                    document.getElementById('int_taken').innerHTML = mint_expect;
                                    document.getElementById('wht_take').innerHTML = mwht_take;
                                    document.getElementById('wht_taken').innerHTML = mwht_take;
                                    document.getElementById('wht_take2').value = mwht_take;
                                    document.getElementById('int_take2').value = mint_expect;
                                    document.getElementById('int_top').value = mint_expect;
                                </script>

                                <!-- <div class="row show_on_print">
                                    <div class="col-md-4" style="width: 369px;float: left;">
                                        <h4><small>ACCOUNT HOLDER</small></h4>
                                        <h4><b> NAME:</b> Namiiro Oliver</h4>
                                        <br>
                                        <h4>SIGNATURE: -----------------------------------------</h4>
                                    </div>

                                    <div class="col-md-4" style="width: 369px"></div>
                                    <div class="col-md-4" style="width: 369px;float: right;">
                                        <div style="width: 313px;height: 96px;border: 1px solid;"></div>
                                        <br>

                                        <i>Official Use Only</i>

                                    </div>

                                </div> -->
                            </div>


                        </div>
                    </div>




                </div>
            </div>
        </div>





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
    <?php include('includes/bottom_scripts.php'); ?>



</body>

</html>