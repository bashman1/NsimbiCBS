<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_transactions')) {
    return $permissions->isNotPermitted(true);
}
$title = 'CASH TRANSFERS';
include_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

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

                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Savings</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Deposits</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <!-- <div class="row">

                    <div class="col-12"> -->
                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>

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
                                            <select class="form-control " id="branchselect" name="branchId">
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

                                        <label class="text-label form-label">Transfer Type *</label>

                                        <select name="deposit_method" class="form-control " id="cash_trans">
                                            <option value=""> All </option>
                                            <option value="TTS" <?= @$_REQUEST['deposit_method'] == 'TTS' ? "selected" : "" ?>>Teller to Safe</option>
                                            <option value="STT" <?= @$_REQUEST['deposit_method'] == 'STT' ? "selected" : "" ?>>Safe to Teller</option>
                                            <option value="TTT" <?= @$_REQUEST['deposit_method'] == 'TTT' ? "selected" : "" ?>>Teller to Teller</option>
                                            <option value="STS" <?= @$_REQUEST['deposit_method'] == 'STS' ? "selected" : "" ?>>Safe to Safe</option>
                                            <option value="STB" <?= @$_REQUEST['deposit_method'] == 'STB' ? "selected" : "" ?>>Safe to Bank</option>
                                            <option value="BTB" <?= @$_REQUEST['deposit_method'] == 'BTB' ? "selected" : "" ?>>Bank to Bank</option>
                                            <option value="BTS" <?= @$_REQUEST['deposit_method'] == 'BTS' ? "selected" : "" ?>>Bank to Safe</option>
                                            <option value="BRTBR" <?= @$_REQUEST['deposit_method'] == 'BRTBR' ? "selected" : "" ?>>Inter-Branch</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Authorized By *</label>

                                        <select id="authby" class=" form-control" name="approved_by_id">
                                            <option value=""> All </option>
                                            <?php
                                            if ($staffs !== '') {
                                                foreach ($staffs as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['approved_by_id'] == $row['id'] ? 'selected' : '' ?>>
                                                        <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php  }
                                            } else { ?>
                                                <option readonly>No Staff Added yet</option>
                                            <?php }
                                            ?>
                                        </select>


                                    </div>
                                </div>
                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Start Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?php @$_REQUEST['start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">End Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?php @$_REQUEST['end_date'] ?>" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch
                                            Entries</button>
                                    </div>
                                </div>

                            </div>

                        </form>


                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            All Cash Transfers
                        </h4>


                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cashtrans2" class="table table-striped dataTable" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Account Name</th>
                                        <th>Notes</th>
                                        <th>DR</th>
                                        <th>CR</th>
                                        <th>REF</th>
                                        <th>Trxn Date</th>
                                        <th>Authorised by</th>
                                        <th>Branch</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $trxns  = $response->getCashTransferTrxns($user[0]['bankId'], $user[0]['bankId'] == '' ? $user[0]['branchId'] : '',@$_REQUEST['start_date'],@$REQUEST['end_date']);

                                    if ($trxns != '') {
                                        $count = 1;
                                        foreach ($trxns as $trxn) {
                                            echo '
                                            <tr>
                                            <td>' . $count++ . '</td>
                                            <td>' . $trxn['dr'] . '</td>
                                            <td>' . $trxn['descr'] . '</td>
                                            <td>' . number_format($trxn['amount']) . '</td>
                                            <td>0</td>
                                            <td>' . $trxn['ref'] . '</td>
                                            <td>' . $trxn['date'] . '</td>
                                            <td>' . $trxn['auth_by'] . '</td>
                                            <td>' . $trxn['bname'] . '</td>
                                            <td>' . $trxn['actions'] . '</td>
                                            </tr>
                                             <tr>
                                            <td>' . $count++ . '</td>
                                            <td>' . $trxn['cr'] . '</td>
                                            <td>' . $trxn['descr'] . '</td>
                                            <td>0</td>
                                            <td>' . number_format($trxn['amount']) . '</td>
                                            <td>' . $trxn['ref'] . '</td>
                                            <td>' . $trxn['date'] . '</td>
                                            <td>' . $trxn['auth_by'] . '</td>
                                            <td>' . $trxn['bname'] . '</td>
                                            <td>' . $trxn['actions'] . '</td>
                                            
                                            </tr>
                                            
                                            ';
                                        }
                                    }
                                    ?>



                                </tbody>
                            </table>
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
        <?php require_once('includes/footer.php'); ?>

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
    <?php require_once('includes/bottom_scripts.php'); ?>


</body>

</html>