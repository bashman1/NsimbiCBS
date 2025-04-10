<?php
include('../backend/config/session.php');
require_once('includes/functions.php');
$title = 'STAFF TILL SHEET';
require_once('includes/head_tag.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
include_once('includes/response.php');
$response = new Response();

// $_GET['id'] = parsed_id($_GET['id']);

$cash_accounts = $response->getAllCashAccounts($user[0]['bankId'], $user[0]['bankId'] == '' ? $user[0]['branchId'] : '');

?>


<body>

    <!--*******************
 Preloader start
 ********************-->
    <?php include_once('includes/preloader.php'); ?>
    <!--*******************
 Preloader end
 ********************-->


    <!--**********************************
 Main wrapper start
 ***********************************-->
    <div id="main-wrapper">

        <?php
        include_once('includes/nav_bar.php');
        include_once('includes/side_bar.php');
        ?>
        <!--**********************************
 Content body start
 ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Staff Till Sheet
                                </h4>
                            </div>
                            <div style="padding: 0.9rem 1.875rem 0.25rem;">
                                <h1 style="font-size: 16px"> <small>Filter By Teller Account & Date
                                        Range </small></h1>
                                <br>

                                <form class="form-inlines select_datess ajax_results_form" method="post" id="filterBydates">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="sr-onlys" for="single-select">Select
                                                    Teller Account
                                                    <i>*</i>: </label>
                                                <select id="bankacc" class=" form-control" name="cash_account">

                                                    <?php

                                                    if ($permissions->hasPermissions('view_teller_till_sheet') || $permissions->IsBankAdmin() || $permissions->hasPermissions('view_everything')) {

                                                        if ($cash_accounts) {
                                                            foreach ($cash_accounts as $c_acc) {
                                                                if ($c_acc['uid'] == $user[0]['userId']) {
                                                                    echo '<option value="' . $c_acc['cid'] . '" selected>' . $c_acc['acname'] . '  - Branch:  ' . $c_acc['branch'] . '</option>';
                                                                } else {
                                                                    if ($c_acc['cid'] == $_POST['cash_account']) {
                                                                        echo '<option value="' . $c_acc['cid'] . '" selected >' . $c_acc['acname'] . '  - Branch:  ' . $c_acc['branch'] . '</option>';
                                                                    } else {
                                                                        echo '<option value="' . $c_acc['cid'] . '" >' . $c_acc['acname'] . '  - Branch:  ' . $c_acc['branch'] . '</option>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else {

                                                        if ($cash_accounts) {
                                                            foreach ($cash_accounts as $c_acc) {
                                                                if ($c_acc['uid'] == $user[0]['userId']) {
                                                                    echo '<option value="' . $c_acc['cid'] . '" selected>' . $c_acc['acname'] . '  - Branch:  ' . $c_acc['branch'] . '</option>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                        if ($permissions->hasPermissions('view_teller_till_sheet') || $permissions->IsBankAdmin()) {

                                            echo '
                                    <div class="col-md-3">


                                        <div class="form-group">
                                            <label class="sr-onlys" for="exampleInputEmail3">Start
                                                Date</label>
                                            <input type="date" class="form-control" name="from_date" value="' . (isset($_POST['from_date']) ? $_POST['from_date'] : date('Y-m-d')) . '" id="exampleInputEmail3" placeholder="Start Date">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="sr-onlys" for="exampleInputPassword3">End
                                                Date</label>
                                            <input type="date" class="form-control" name="to_date" value="' . (isset($_POST['to_date']) ? $_POST['to_date'] : date('Y-m-d')) . '" id="exampleInputPassword3" placeholder="End Date">
                                        </div>
                                    </div>

                                        ';
                                        } else {
                                            echo '
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="sr-onlys" for="exampleInputEmail3">Start
                                                Date</label>
                                            <input type="date" class="form-control" name="from_date" value="' .  date('Y-m-d') . '" id="exampleInputEmail3" placeholder="Start Date">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="sr-onlys" for="exampleInputPassword3">End
                                                Date</label>
                                            <input type="date" class="form-control" name="to_date" value="' . date('Y-m-d') . '" id="exampleInputPassword3" placeholder="End Date">
                                        </div>
                                    </div>

                                        ';
                                        }
                                        ?>


                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="sr-onlys" for="fetch">&nbsp;&nbsp;</label>
                                                <button type="submit" class="btn btn-primary light btn-xs mb-1 form-control" name="submit">Fetch
                                                    Entries</button>
                                            </div>
                                        </div>

                                    </div>

                                </form>
                            </div>

                            <div>

                                <div class="card">
                                    <div class="card-header">



                                        <a target="_blank" class="btn btn-primary btn-sm" href="export_report.php?exportFile=export_teller_till_sheet&useFile=1&start=<?= @$_POST['from_date'] ?? date('Y-m-d') ?>&end=<?= @$_POST['to_date'] ?? date('Y-m-d') ?>&staff=<?= @$_POST['cash_account'] ?? 0 ?>">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                    </div>
                                    <div class="card-body" id="exreportn">
                                        <!--<div class="table-responsive">-->
                                        <?php
                                        $start = $_POST['from_date'] ?? date('Y-m-d');
                                        $end = $_POST['to_date'] ?? date('Y-m-d');
                                        $staff = $_POST['cash_account'] ?? 0;


                                        if (@$staff) {
                                            $details = $response->getStaffTillEntries($start, $end, $staff);
                                        } else {
                                            $details = '';
                                        }



                                        ?>

                                        <center style="font-size:15px">
                                            <img src="<?php echo is_null($user[0]['blogo']) ? 'icons/favicon.png' : $user[0]['blogo']; ?>" width="10%" onerror="this.onerror=null; this.src='icons/favicon.png'">
                                            <h4 style="line-height:1.0em"> <b>
                                                    <?= is_null($user[0]['bankName']) ? '' : strtoupper($user[0]['bankName']); ?>
                                                </b> </h4>
                                            <p style="line-height:1.0em;font-weight:bold">Location:
                                                <?php echo is_null($user[0]['blocation']) ? '' : $user[0]['blocation']; ?>
                                            </p>
                                            <p style="line-height:1.0em;font-weight:bold"> Tel:
                                                <?php echo is_null($user[0]['bcontacts']) ? '' : $user[0]['bcontacts']; ?>
                                            </p>
                                            <p style="line-height:1.0em;font-weight:bold"> Email:
                                                <?php echo is_null($user[0]['bemail']) ? '' : $user[0]['bemail']; ?>
                                            </p>
                                            <br /><br />
                                            <p class=" text-primary"><?= $details[0]['cash_acc_details'] ?? '' ?>
                                            </p>
                                        </center><br /><br />



                                        <br />
                                        <?php
                                        if ($details) {
                                        ?>


                                            <table id="staff" class="table table-striped" style="min-width: 845px">
                                                <thead>

                                                    <tr>
                                                        <th colspan="7" style="text-align:center;">
                                                            <h4 class="page-title">Till Sheet Journal Entries Report:
                                                                <?php echo date('Y-m-d', strtotime($_POST['from_date'] ?? date('Y-m-d'))) . '   -   ' . date('Y-m-d', strtotime($_POST['to_date'] ?? date('Y-m-d'))) ?>
                                                            </h4>
                                                        </th>
                                                    </tr>

                                                    <tr style="text-align: center !important;">
                                                        <th>#</th>
                                                        <th>CHART ACCOUNT:</th>

                                                        <th>DR:</th>
                                                        <th>CR:</th>
                                                        <th>BALANCE:</th>

                                                        <th>REFERENCE NO:</th>
                                                        <th>DATE:</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $dess = '';
                                                    $count = 1;
                                                    $ccount = 0;
                                                    $dcount = 0;
                                                    $val = 0;
                                                    $ctotal = 0;
                                                    $dtotal = 0;

                                                    $val = $val;
                                                    //             echo '
                                                    //             <tr style="color: blue !important">
                                                    // <td>' . $count++ . '</td>
                                                    // <td>(BF)</td>

                                                    //   <td></td>
                                                    // <td></td>
                                                    // <td> ' . number_format($val) . '</td>
                                                    // <td></td>

                                                    // <td>' . date('Y-m-d', strtotime($_POST['from_date'])) . '</td>

                                                    // </tr>
                                                    // ';
                                                    foreach ($details as $deposit) {
                                                        $trxn_date = date('Y-m-d', strtotime($deposit['_date_created']));

                                                        if (
                                                            $deposit['type'] == "W" or $deposit['type'] == "LE" or $deposit['type'] == "C" or $deposit['type'] == "CW" or $deposit['type'] == "CS" or $deposit['type'] == "SMS" or
                                                            $deposit['type'] == "LP" or
                                                            $deposit['type'] == "RC" or  $deposit['type'] == "E" || $deposit['type'] == "TTS"
                                                        ) {

                                                            $credit = number_format($deposit['_amount']);
                                                            $debit = "-";
                                                            $val = $val - $deposit['_amount'];
                                                            $dtotal = $dtotal + $deposit['_amount'];

                                                            if ($deposit['type'] == "W") {
                                                                if ($deposit['is_reversal']) {
                                                                    $dess = 'Reversal: (' . $deposit['_account_no'] . ') by ' . $deposit['_paidby_name'] . '  ';
                                                                } else {
                                                                    $dess = 'Withdraw: (' . $deposit['_account_no'] . ') by ' . $deposit['_paidby_name'] . '  ';
                                                                }
                                                            }
                                                            if ($deposit['type'] == "E") {
                                                                $dess = 'Expense: ';
                                                            }

                                                            if ($deposit['type'] == "TTS") {
                                                                $dess = 'Cash Transfer: ';
                                                            }


                                                            $ccount++;
                                                        }

                                                        if ($deposit['type'] == "TTT") {

                                                            $dess = 'Cash Transfer: ';


                                                            if ($deposit['cr_acid'] == $staff) {

                                                                $debit = number_format($deposit['_amount']);
                                                                $credit = "-";
                                                                $val = $val +  $deposit['_amount'];
                                                                $ctotal = $ctotal + $deposit['_amount'];
                                                            } else {

                                                                $credit = number_format($deposit['_amount']);
                                                                $debit = "-";
                                                                $val = $val - $deposit['_amount'];
                                                                $dtotal = $dtotal + $deposit['_amount'];
                                                            }
                                                        }

                                                        if (
                                                            $deposit['type'] == "L"
                                                        ) {
                                                            $credit = number_format($deposit['_amount'] + @$deposit['loan_interest']);
                                                            $debit = "-";
                                                            $val = $val - ($deposit['_amount'] + @$deposit['loan_interest']);
                                                            $dtotal = $dtotal + ($deposit['_amount'] + @$deposit['loan_interest']);
                                                            $dess = 'Loan Payment: (' . $deposit['_account_no'] . ') by ' . $deposit['_paidby_name'] . ' ';
                                                            $ccount++;
                                                        } else  if ($deposit['type'] == "D" or $deposit['type'] == "A" or $deposit['type'] == "LC" or  $deposit['type'] == "I" || $deposit['type'] == "R" || $deposit['type'] == "STT") {



                                                            $debit = number_format($deposit['_amount']);
                                                            $credit = "-";
                                                            $val = $val +  $deposit['_amount'];
                                                            $ctotal = $ctotal + $deposit['_amount'];

                                                            if ($deposit['type'] == "D") {
                                                                $dess = 'Deposit: (' . $deposit['_account_no'] . ') by ' . $deposit['_paidby_name'] . ' ';
                                                            }

                                                            if ($deposit['type'] == "STT") {
                                                                $dess = 'Cash Transfer: ';
                                                            }

                                                            if ($deposit['type'] == "I") {
                                                                $dess = 'Income: ';
                                                            }
                                                            if ($deposit['type'] == "R") {
                                                                $dess = 'Membership: ';
                                                            }



                                                            $dcount++;
                                                        } else if ($deposit['type'] == "ASS") {

                                                            if ($deposit['cr_dr'] == 'debit') {
                                                                $debit = number_format($deposit['_amount']);
                                                                $credit = "-";
                                                                $val = $val +  $deposit['_amount'];
                                                                $ctotal = $ctotal + $deposit['_amount'];
                                                                $dcount++;
                                                            } else {
                                                                $credit = number_format($deposit['_amount']);
                                                                $debit = "-";
                                                                $val = $val - $deposit['_amount'];
                                                                $dtotal = $dtotal + $deposit['_amount'];
                                                                $ccount++;
                                                            }

                                                            $dess = 'Asset Registered: ';
                                                        }


                                                        echo '
                                                    <tr>
                                        <td>' . $count++ . '</td>
                                        <td>' . $dess . $deposit['_reason'] . '</td>
                                       
                                          <td>' . $debit . '</td>
                                        <td>' . $credit . '</td>
                                        <td> ' . number_format($val) . '</td>
                                        <td class="no_print clickable_ref_no" ref-no="' . $deposit['ref'] . '" tid="' . $deposit['_did'] . '">' . $deposit['ref'] . '</td>
                                       
                                        <td>' . $trxn_date . '</td>
                                   
                                        </tr>
                                        ';
                                                    }
                                                    //   here totals were here

                                                    ?>

                                                </tbody>

                                            </table>
                                            <table class="table table-striped" style="min-width: 845px">
                                                <tbody>
                                                    <?php
                                                    echo '
                                                        <tr>
                                                        <td></td>
                                                        <td><b>Totals<b></td>
                                                       
                                                          <td><b>DR: ' . number_format($ctotal) . '</b></td>
                                                        <td><b>CR: ' . number_format($dtotal) . '</b></td>

                                                        <td> <b>BALANCE: ' . number_format($ctotal - $dtotal) . '<b></td>

                                                        <td></td>
                                                        <td></td>
                                                   
                                                        </tr>
                                                        ';
                                                    ?>
                                                </tbody>
                                            </table>

                                            <div class="row show_on_print">
                                                <div class="col-md-4" style="width: 369px;float: left;">

                                                    <h4><small>TELLER:
                                                        </small><b><?= $details[0]['cash_acc_details'] ?? '' ?></b></h4>

                                                    <br>

                                                    <h4><small>SIGNATURE:</small><b>
                                                            ------------------------</b></h4>

                                                </div>

                                                <div class="col-md-4" style="width: 369px"></div>
                                                <div class="col-md-4" style="width: 369px;float: right;">

                                                    <div style="width: 313px;height: 96px;border: 1px solid;">
                                                    </div>
                                                    <br>
                                                    <i>Official Use Only</i>
                                                </div>
                                            </div>
                                        <?php
                                        } else {
                                            echo '<div class="col-md-4"><div class="alert alert-warning"><span class="semibold">Caution: </span>No Journal Entries found' . @$_POST['cash_account'] . '</div></div>';
                                        }
                                        ?>

                                        <!--</div>-->
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
                <div class="modal fade" id="pageGeneralModal">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body">

                            </div>

                        </div>
                    </div>
                </div>

                <?php include('includes/bottom_scripts.php'); ?>


                <script type="text/javascript">
                    var table = $('#staff').dataTable({
                        destroy: true,
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                    })
                </script>

</body>

</html>