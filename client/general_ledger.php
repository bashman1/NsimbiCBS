<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();


?>
<?php
include('includes/head_tag.php');


$branches = $response->getBankBranches($user[0]['bankId']);
$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
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

                <div class="card">
                    <!-- <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> -->
                    <div class="card-body">

                        <form class="ajax_results_form" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class=" form-control" name="branchId" id="branchselect">
                                                <option value=""> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branchId'] == $row['id'] ? "selected" : "";
                                                ?>
                                                        <option value="<?= @$row['id'] ?>" <?= $is_seleceted ?>>
                                                            <?= $row['name'] ?>
                                                        </option>
                                                <?php }
                                                } ?>

                                            </select>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Select Chart Account</label>
                                        <select name="main_acc" class="form-control " id="journalacc">
                                            <option value="">Select....</option>
                                            <?php
                                            $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                            if ($sub_accs) {

                                                foreach ($sub_accs as $acc) {

                                                    echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Start Date</label>
                                        <input type="date" class="form-control" name="from_date" value="<?= date('Y-m-d') ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputPassword3">End Date</label>
                                        <input type="date" class="form-control" name="to_date" value="<?= date('Y-m-d') ?>" id="exampleInputPassword3" placeholder="End Date">
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputPassword3">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary form-control" name="submit">Fetch Entries</button>
                                    </div>
                                </div>



                            </div>

                        </form>


                    </div>
                </div>


                <div class="card">
                    <div class="card-body">

                        <div class="no_print">
                            <h4 class="mt-0 header-title">Journal Ledger</h4>
                            <p class="text-muted mb-3">
                                <?php
                                isset($_POST['from_date']) && $_POST['from_date'] != '' ? 'As at ' . normal_date($_POST['from_date'])  . ' to ' . normal_date($_POST['to_date']) : ''
                                ?>
                            </p>
                        </div>

                        <hr class="hr-dashed">
                        <div class="table-responsive">
                            <table id="ledger" class="display dataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>CHART ACCOUNT:</th>
                                        <th>DR:</th>
                                        <th>CR:</th>
                                        <th>BALANCE:</th>
                                        <th>REF.NO:</th>
                                        <th>DATE:</th>
                                        <th>AUTHORIZED BY:</th>
                                        <th>BRANCH:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cacc = 0;
                                    if (isset($_POST['main_acc']) && $_POST['main_acc'] != '') {
                                        $cacc = $_POST['main_acc'];
                                    }
                                    $accounts = $response->getChartAccountsLedger($user[0]['bankId'], $user[0]['branchId'], $cacc);

                                    if ($accounts != '') {

                                        foreach ($accounts as $acc) {
                                            echo '
                                            <tr>
                                            <td></td>
                                            <td><b>' . strtoupper($acc['name']) . ' ( ' . $acc['type'] . ')</b></td>
                                            <td></td>
                                            <td></td>
                                            <td><b>' . number_format($acc['balance'] ?? 0) . '</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>' . strtoupper($acc['branch']) . '</td>
                                        </tr>

';
                                            $cr_amount = 0;
                                            $cr_tot = 0;
                                            $dr_amount = 0;
                                            $dr_tot = 0;
                                            $balance = 0;

                                            $trxns  = $response->getAccountTrxns($user[0]['bankId'], $user[0]['bankId'] == '' ? $user[0]['branchId'] : '', $acc['id']);

                                            if ($trxns != '') {
                                                $count = 1;


                                                foreach ($trxns as $trxn) {
                                                    if ($trxn['type'] == 'D' || $trxn['type'] == 'R' || $trxn['type'] == 'W') {

                                                        if ($trxn['type'] == 'D' || $trxn['type'] == 'R') {
                                                            $cr_amount = $trxn['amount'] ?? 0;
                                                            $cr_tot = $cr_tot + $trxn['amount'] ?? 0;
                                                            $balance = $balance + $trxn['amount'] ?? 0;
                                                        }
                                                        if ($trxn['type'] == 'W') {
                                                            $dr_amount = $trxn['amount'] ?? 0;
                                                            $dr_tot = $dr_tot + $trxn['amount'] ?? 0;
                                                            $balance = $balance - $trxn['amount'] ?? 0;
                                                        }
                                                    } else {
                                                        if ($trxn['cr_acid'] && $trxn['cr_acid'] != '' && $trxn['cr_acid'] > 0  && $trxn['dr_acid'] && $trxn['dr_acid'] != '' && $trxn['dr_acid'] > 0) {
                                                            if ($trxn['cr_acid'] == $acc['id']) {
                                                                $cr_amount = $trxn['amount'] ?? 0;
                                                                $cr_tot = $cr_tot + $trxn['amount'] ?? 0;
                                                                $balance = $balance + $trxn['amount'] ?? 0;
                                                            }
                                                            if ($trxn['dr_acid'] == $acc['id']) {
                                                                $dr_amount = $trxn['amount'] ?? 0;
                                                                $dr_tot = $dr_tot + $trxn['amount'] ?? 0;
                                                                $balance = $balance - $trxn['amount'] ?? 0;
                                                            }
                                                        } else if ($trxn['cr_acid'] && $trxn['cr_acid'] != '' && $trxn['cr_acid'] > 0) {
                                                            $cr_amount = $trxn['amount'] ?? 0;
                                                            $cr_tot = $cr_tot + $trxn['amount'] ?? 0;
                                                            $balance = $balance + $trxn['amount'] ?? 0;
                                                        } else if ($trxn['dr_acid'] && $trxn['dr_acid'] != '' && $trxn['dr_acid'] > 0) {
                                                            $dr_amount = $trxn['amount'] ?? 0;
                                                            $dr_tot = $dr_tot + $trxn['amount'] ?? 0;
                                                            $balance = $balance - $trxn['amount'] ?? 0;
                                                        } else {
                                                            $cr_amount = $trxn['amount'] ?? 0;
                                                            $cr_tot = $cr_tot + $trxn['amount'] ?? 0;
                                                            $balance = $balance + $trxn['amount'] ?? 0;
                                                        }
                                                    }


                                                    echo '
                                                    <tr>
                                                    <td>' . $count++ . '</td>
                                                    <td>' . $trxn['descr'] . '</td>
                                                    <td>' . number_format($dr_amount) . '</td>
                                                    <td>' . number_format($cr_amount) . '</td>
                                                    <td>' . number_format($balance) . '</td>
                                                    <td>trxn-' . $trxn['type'] . '-' . $trxn['tid'] . '</td>
                                                    <td>' . normal_date($trxn['date']) . '</td>
                                                    <td>' . $trxn['auth'] . '</td>
                                                    <td>' . $trxn['branch'] . '</td>
                                                </tr>
                                            
                                            ';
                                                }
                                            }
                                        }
                                    }
                                    ?>



                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><b>TOTALS:</b></td>
                                        <td></td>
                                        <td><b><?= number_format($dr_tot ?? 0) ?></b></td>
                                        <td><b><?= number_format($cr_tot ?? 0) ?></b></td>
                                        <td><b><?= number_format($balance ?? 0) ?></b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
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
    <?php include('includes/bottom_scripts.php'); ?>


</body>

</html>