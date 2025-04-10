<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('share_transfer')) {
    return $permissions->isNotPermitted(true);
}
$title = 'SHARE TRANSFER';
require_once('includes/head_tag.php');
$response = new Response();

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
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Shares Transfer Form
                                </h4>

                                <p class="text-muted mb-3">Search by Name | Member No | Account No</p>

                                <form method="post" class="submit_with_ajax" action="share_transfer_2.php">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Search Query : Transferer</label>
                                                <select id="clientsselect" class="form-control select2x" name="send" required>
                                                    <!-- <option selected></option> -->
                                                    <?php
                                                    // foreach ($response->getAllBankClients($user[0]['bankId'], $user[0]['branchId']) as $row) {
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
                                                <label>Search Query : Receiver</label>
                                                <select id="single-select2" class="form-control select2xx" name="receive" required>
                                                    <!-- <option selected></option> -->
                                                    <?php
                                                    // foreach ($response->getAllBankClients($user[0]['bankId'], $user[0]['branchId']) as $row) {
                                                    //     echo '
                                                    // <option value="' . $row['userId'] . '">' . $row['accno'] . '  - ' . $row['name'] . '   - UGX: ' . number_format($row['acc_balance'] + $row['loan_wallet']) . '   -  Branch: ' . $row['branchName'] . '</option>
                                                    // ';
                                                    // }
                                                    ?>
                                                </select>
                                            </div>


                                        </div>
                                    </div>
                                    <br /><br />
                                    <div class="row">
                                        <div class="col-md-3">

                                            <button type="submit" name="continue" class="btn btn-primary">Proceed <i class="ti-arrow-circle-right"></i></button>
                                        </div>
                                    </div>

                                </form>

                            </div>
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
        <!-- Required vendors -->
        <?php include('includes/bottom_scripts.php'); ?>
        <!-- <script src="./js/styleSwitcher.js"></script> -->
        <script>
            $("#single-select2").select2({
                placeholder: "",
                allowClear: true
            });


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
            })

            $(document).ready(function() {
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
        </script>


</body>

</html>