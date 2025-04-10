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

    $res = $response->onetooneTransfer($_POST);
    if ($res) {

        setSessionMessage(true, 'Amount Transferred successfully!');
        header('location:one_to_one_transfer.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:one_to_one_transfer.php');
        exit;
    }
}
$title = 'ONE-TO-ONE TRANSFER';
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
                    <div class="col-xl-12 col-lg-12">
                        <!-- <div class="card">
                            <div class="card-body">

                                <h4 class="mt-0 header-title">Transfer Member Selection </h4>
                                <p class="text-muted mb-3">Search by Name | Member No | Account No</p>

                                <form method="post" class="submit_with_ajax">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Search Query : Transferer</label>
                                                <select class="ajaxSelectSearch form-control mb-3 custom-select select2-hidden-accessible" name="account_no1" url="https://app.ucscucbs.com/Search/SavingsAccounts/select2" tabindex="-1" aria-hidden="true">
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Search Query : Receiver</label>
                                                <select class="ajaxSelectSearch form-control mb-3 custom-select select2-hidden-accessible" name="account_no2" url="https://app.ucscucbs.com/Search/SavingsAccounts/select2" tabindex="-1" aria-hidden="true">
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-light">Proceed <i class="ti-arrow-circle-right"></i></button>
                                        </div>

                                    </div>

                                </form>

                            </div>
                        </div> -->

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    <!-- <h4 class="mt-0 header-title">One to One - Account Balance Transfer Form </h4> -->
                                    <p class="text-muted mb-3">One to One - Account Balance Transfer Form</p>
                                </h4>
                            </div>
                            <div class="card-body">

                                <form method="post" class="submit_with_ajax">
                                    <input type="hidden" name="bank" value="<?= $user[0]['bankId'] ?>" />
                                    <input type="hidden" name="branch" value="<?= $user[0]['branchId'] ?>" />
                                    <input type="hidden" name="user" value="<?= $user[0]['userId'] ?>" />
                                    <div class="row">
                                        <?php
                                        if (!$user[0]['branchId']) {
                                            $branches = $response->getBankBranches($user[0]['bankId']);

                                            echo '
                                            <div class="col-md-6">
                                          <div class="form-group">

                                              <label class="text-label form-label">Branch *</label>
                                              <select id="branchselect" class="form-control" name="branch" required>

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
                                          </div>


                                          ';
                                        } else {
                                            echo '

                                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >


                                            ';
                                        }
                                        ?>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Search Client : Transferer *</label>
                                                <select id="credit_account" name="sender" required class="form-control select2x">
                                                    <!-- <option selected></option> -->
                                                    <?php
                                                    // $clients =    $response->getAllBankClients($user[0]['bankId'], $user[0]['branchId']);
                                                    // foreach ($clients as $row) {
                                                    //     echo '
                                                    // <option value="' . $row['userId'] . '">' . $row['accno'] . '  - ' . $row['name'] . '   - UGX: ' . number_format($row['acc_balance'] + $row['loan_wallet']) . '   -  Branch: ' . $row['branchName'] . '</option>
                                                    // ';
                                                    // }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Search Client : Receiver *</label>
                                                <select id="clientsselect" name="receiver" required class="form-control select2xx">
                                                    <!-- <option selected></option> -->
                                                    <?php
                                                    // foreach ($clients as $row) {
                                                    //     echo '
                                                    // <option value="' . $row['userId'] . '">' . $row['accno'] . '  - ' . $row['name'] . '   - UGX: ' . number_format($row['acc_balance'] + $row['loan_wallet']) . '   -  Branch: ' . $row['branchName'] . '</option>
                                                    // ';
                                                    // }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Amount to Transfer</label>
                                                <input type="text" name="amount" class="form-control comma_separated" />

                                            </div>


                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <input type="text" name="reason" class="form-control " />

                                            </div>


                                        </div>
                                        <br /><br /><br />
                                        <div class="col-md-6" style="padding-top: 30px !important;">
                                            <div class="form-group">
                                                <button type="submit" name="submit" class="btn btn-primary">Proceed <i class="ti-arrow-circle-right"></i></button>
                                            </div>


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
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script>
            $(document).ready(function() {
                $("select.select2x").select2({
                    ajax: {
                        url: "<?php echo BACKEND_BASE_URL ?>User/get_all_bank_clients_search.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>",
                        dataType: 'json',
                        data: (params) => {
                            return {
                                q: params.term,
                            }
                        },

                        processResults: (data, params) => {
                            const results = data.data.map(item => {
                                return {
                                    id: item.userId,
                                    text: item.accno + ' : ' + item.name + ' - UGX ' + item.tot_balance + '  - Branch: ' + item.branchName,
                                };
                            });
                            return {
                                results: results,
                            }
                        },
                    },
                });

                $("select.select2xx").select2({
                    ajax: {
                        url: "<?php echo BACKEND_BASE_URL ?>User/get_all_bank_clients_search.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>",
                        dataType: 'json',
                        data: (params) => {
                            return {
                                q: params.term,
                            }
                        },

                        processResults: (data, params) => {
                            const results2 = data.data.map(item => {
                                return {
                                    id: item.userId,
                                    text: item.accno + ' : ' + item.name + ' - UGX ' + item.tot_balance + '  - Branch: ' + item.branchName,
                                };
                            });
                            return {
                                results: results2,
                            }
                        },
                    },
                });
            })

            // $(document).ready(function() {

            // })
        </script>

</body>

</html>