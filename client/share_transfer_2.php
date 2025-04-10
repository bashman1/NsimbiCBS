<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}


$response = new Response();

if (isset($_POST['submit'])) {
    // $amount = str_replace(",", "", $_POST['amount']);
    $res = $response->transferShares($_POST);
    if ($res) {
        setSessionMessage(true, 'Shares Transferred Successfully!');
        header('location:share_transfer_trxns.php');
        exit;
    } else {
        setSessionMessage(false, 'Share Transfer failed! Try again.');
        header('location:share_transfer_trxns.php');
        exit;
    }
}

require_once('includes/head_tag.php');
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
                    <?php
                    $sendDetails = $response->getMemberDetails($_POST['send'])[0];
                    $receiveDetails = $response->getMemberDetails($_POST['receive'])[0];

                    ?>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">

                                <h4 class="mt-0 header-title">Transferer</h4>
                                <p class="text-muted mb-3"></p>

                                <div class="text-center">
                                    <div class="met-profile-main-pic">
                                        <img src="<?= is_null($sendDetails['profilePhoto']) ? 'icons/favicon.png' : $sendDetails['profilePhoto'] ?>" onerror="this.onerror=null; this.src='icons/favicon.png'" alt="" height="100" width="100" class="rounded-circle">
                                    </div>

                                    <div class="">
                                        <h5 class="mb-0"><?= $sendDetails['firstName'] . ' ' . $sendDetails['lastName'] ?> </h5>
                                        <small class="text-muted">A/C No: <?= $sendDetails['mno'] ?> | A/C BAL : <?= number_format($sendDetails['balance']) ?></small>
                                    </div>
                                    <div class="mb-3">
                                        <a href="#" class="mr-3 text-warning"><?= $sendDetails['shares'] ?> Shares</a> |
                                        <a href="#" class="text-warning"><?= number_format($sendDetails['sharesamount']) ?> Amount</a>
                                    </div>
                                    <a href="client_profile_page?id=<?php echo $_POST['send']; ?>" class="btn btn-sm btn-primary load_supplement_ajax">View Profile</a>
                                </div>
                            </div>
                            <!--end card-body-->
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">

                                <h4 class="mt-0 header-title">Receiver</h4>
                                <p class="text-muted mb-3"></p>

                                <div class="text-center">
                                    <div class="met-profile-main-pic">
                                        <img src="<?= is_null($receiveDetails['profilePhoto']) ? 'icons/favicon.png' : $receiveDetails['profilePhoto'] ?>" onerror="this.onerror=null; this.src='icons/favicon.png'" alt="" height="100" width="100" class="rounded-circle">
                                    </div>

                                    <div class="">
                                        <h5 class="mb-0"><?= $receiveDetails['firstName'] . ' ' . $receiveDetails['lastName'] ?> </h5>
                                        <small class="text-muted">A/C No: <?= $receiveDetails['mno'] ?> | A/C BAL : <?= number_format($receiveDetails['balance']) ?></small>
                                    </div>
                                    <div class="mb-3">
                                        <a href="#" class="mr-3 text-warning"><?= $receiveDetails['shares'] ?> Shares</a> |
                                        <a href="#" class="text-warning"><?= number_format($receiveDetails['sharesamount']) ?> Amount</a>
                                    </div>

                                    <a href="client_profile_page.php?id=<?php echo $_POST['send']; ?>" class="btn btn-sm btn-primary load_supplement_ajax">View Profile</a>
                                </div>
                            </div>
                            <!--end card-body-->
                        </div>

                    </div>
                    <!--end col-->

                    <div class="col-md-6">

                        <div class="card">
                            <div class="card-body">
                                <h4 class="mt-0 header-title">Shares transfer Form</h4>
                                <p class="text-muted mb-3"></p>

                                <form method="post" class="submit_with_ajax">
                                    <input type="hidden" class="form-control" name="send" value="<?php echo $_POST['send']; ?>">
                                    <input type="hidden" class="form-control" name="receive" value="<?php echo $_POST['receive']; ?>">
                                    <input type="hidden" class="form-control" name="user" value="<?php echo $user[0]['userId']; ?>">
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
                                    ?><br />
                                    <div class="form-group">
                                        <div class="row">

                                            <div class="col-lg-6 col-6">
                                                <label for="projectName">No of shares :</label>
                                                <input type="number" value="" step="any" min="0" max="<?php echo $sendDetails['shares']; ?>" name="shares" class="form-control" placeholder="Enter No of Shares">
                                            </div>
                                            <!--end col-->

                                            <div class="col-lg-6 col-6 mb-2 mb-lg-0">
                                                <label>Trxn Date</label>
                                                <input type="date" class="form-control" name="record_date" value="<?php echo date('Y-m-d'); ?>" placeholder="Enter trxn date">
                                            </div>
                                            <!--end col-->

                                        </div>
                                        <!--end row-->
                                    </div>
                                    <!--end form-group-->

                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control" rows="2" name="notes" placeholder="writing here.."></textarea>
                                    </div><br /><br />
                                    <!--end form-group-->
                                    <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm">Process Transaction</button>

                                </form>
                            </div>
                        </div>
                        <!--end form-->
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
        <?php include('includes/bottom_scripts.php'); ?>

      


</body>

</html>