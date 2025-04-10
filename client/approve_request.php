<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('waive_penalty')) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $_POST['amount'] = str_replace(',', '', @$_POST['amount']);
    // var_dump($res);
    // exit;
    $res = $response->approveBranchRequest($_POST);
    if ($res) {
        setSessionMessage(true, "Inter-Branch Request Approved successfully!");
        header('location: inter_branch_requests.php');
        exit;
    } else {
        setSessionMessage(false, $res['message']);
        header('location:' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
        exit;
    }
}

$req_id = $_GET['id'];
$selected_req = @$response->getRequestDetails($req_id)[0];

?>
<?php
include('includes/head_tag.php');
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
                    <div class="card-body">
                        <h4 class="mt-0 header-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                        </h4>

                        <p class="text-muted mb-3"> Inter-Branch Requisition Processing Form</p>

                        <form method="post" class="submit_with_ajax" action="">
                            <input type="hidden" name="req_id" value="<?= @$selected_req['id'] ?>">
                            <input type="hidden" name="to_id" value="<?= @$selected_req['to'] ?>">
                            <input type="hidden" name="from_id" value="<?= @$selected_req['from'] ?>">
                            <input type="hidden" name="bank" value="<?= $user[0]['bankId']; ?>" />
                            <input type="hidden" name="bid" value="<?= $user[0]['branchId']; ?>" />
                            <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>" />

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Amount</label>
                                        <input id="total_amount" type="text" value="<?= number_format(@$selected_req['amount']) ?>" min="0" name="amount" class="form-control comma_separated" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Affected Cash A/C <i>*</i>: </label>
                                        <select class="form-control" name="cash_acc" id="bankacc">
                                            <?php
                                            $cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
                                            if ($_SESSION['user']['bankId']) {
                                                foreach ($cash_accounts as $cash_account) {
                                            ?>
                                                    <option value="<?= $cash_account['cid'] ?>">
                                                        <?= $cash_account['acname'] ?> Balance: <?= $cash_account['balance'] ?>
                                                    </option>
                                            <?php }
                                            } else {
                                                foreach ($cash_accounts as $c_acc) {
                                                    if ($c_acc['userid'] == $user[0]['userId']) {
                                                        echo '<option value="' . $c_acc['cid'] . '"> ' . $c_acc['acname'] . '  Balance: ' . $c_acc['balance'] . '</option>';
                                                    }
                                                }
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Comment <i>*</i>: </label>
                                        <input type="text" class="form-control" name="comment" placeholder="" required="">
                                    </div>
                                    <br /><br />
                                    <button type="submit" name="submit" class="btn btn-primary"> Process Request </button>
                                </div>

                            </div>

                        </form>

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
    <?php include('includes/bottom_scripts.php'); ?>




</body>

</html>