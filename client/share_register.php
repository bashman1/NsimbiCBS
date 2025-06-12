<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'SHARE REGISTER';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

$branches = $response->getBankBranches($user[0]['bankId']);
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

                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Share Register

                            <hr class="hr-dashed">

                            <div class="row pricingTable1">
                                <div class="col-md-6">
                                    <?php
                                    $details = $response->getBankSharesDetails($user[0]['bankId'], $user[0]['bankId'] == '' ? $user[0]['branchId'] : '')[0];
                                    ?>

                                    <h4 class="mt-0 header-title">Shares Summary</h4>
                                    <p class="text-muted mb-3"></p>

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li><b>Total Share Holders: </b><?= number_format(@$details['share_holders'] ?? 0) ?></li>
                                        <li><b>Total Shares: </b><?= number_format(@$details['shares'], 2, '.', '') ?></li>
                                        <li><b>Total Share Amount: </b><?= number_format(@$details['shareamount'] ?? 0) ?></li>
                                    </ul>

                                </div>
                                <div class="col-md-6">

                                    <h4 class="mt-0 header-title">Dividends Details</h4>
                                    <p class="text-muted mb-3"></p>

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li><b>Total Savings Dividends: </b><?= number_format(@$details['savings']) ?></li>
                                        <li><b>Profits Shares Dividends: </b><?= number_format(@$details['sharesdividends']) ?></li>
                                    </ul>

                                </div>
                            </div>
                            <form>
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-label form-label" for="exampleInputEmail3">Start Date *</label>
                                            <input type="date" name="start_date" class="form-control" value="<?= @$_REQUEST['start_date'] ?>" placeholder="Start Date">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-label form-label" for="exampleInputEmail4">End Date *</label>
                                            <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?>" placeholder="End Date">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-label form-label">Branch *</label>
                                            <?php if ($_SESSION['session_user']['branchName']) { ?>
                                                <div>
                                                    <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                                </div>
                                            <?php } else { ?>
                                                <select class="me-sm-2 default-select form-control wide" id="payment_methods" name="branch">
                                                    <option value="0"> All</option>
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
                                            <label class="text-label form-label"> </label>
                                            <button type="submit" class="btn btn-primary form-control">Filter Entries</button>
                                        </div>
                                    </div>

                                    <!-- </div> -->

                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="share_search_client.php" class="btn btn-primary light btn-xs mb-1">Purchase Shares</a>
                                </h4>




                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="share_register" class="table table-striped dataTable" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>A/C No</th>
                                                <th>A/C Name</th>
                                                <th>No. Of Shares</th>
                                                <th>Share Amount</th>
                                                <th>Savings Dividends</th>
                                                <th>Shares Dividends</th>
                                                <th>Branch</th>
                                                <th> Date Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>




                                        </tbody>
                                    </table>
                                </div>
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

    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $.ajax({
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_share_holders.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= @$_REQUEST['branch'] ?? $user[0]['branchId'] ?>&start_date=<?= @$_REQUEST['start_date'] ?>&end_date=<?= @$_REQUEST['end_date'] ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    bindtoDatatable(data.data);
                    // console.log(data.data);
                }
            });

        });

        function bindtoDatatable(data) {

            var table = $('#share_register').dataTable({
                destroy: true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5'
                ],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                "aaData": data,

                "columns": [{
                        "data": "id"
                    }, {
                        "data": "acc"
                    }, {
                        "data": "client"
                    }, {
                        "data": "shares"
                    },
                    {
                        "data": "shareamount"
                    }, {
                        "data": "savingsdivids"
                    },
                    {
                        "data": "sharesdivids"
                    }, {
                        "data": "branch"
                    }, {
                        "data": "dateCreated"
                    }
                ]
            })

        }
    </script>


    <script>
        // $(document).ready(function() {
        //     $('#share_register').DataTable({
        //         destroy: true,
        //         // "pageLength": -1, // Show all rows by default
        //         "lengthMenu": [
        //             [-1, 10, 25, 50],
        //             ["All", 10, 25, 50]
        //         ], // Options for the length menu

        //         dom: 'Bfrtip',
        //         buttons: [
        //             'excelHtml5'
        //         ]
        //     });
        // });
    </script>

</body>

</html>