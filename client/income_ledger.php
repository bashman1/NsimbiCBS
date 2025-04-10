<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
$title = 'INCOME LEDGER';
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



                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>

                        <form class="ajax_results_form" method="get">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="me-sm-2 default-select form-control wide" id="payment_methods" name="branchId">
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
                                        <label>Select Journal Account: </label>
                                        <select name="main_acc" class="form-control" id="journalacc">
                                            <option value="">All</option>
                                            <?php
                                            $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                            if ($sub_accs) {

                                                foreach ($sub_accs as $acc) {
                                                    if ($acc['type'] == 'INCOMES') {
                                                        if ($acc['id'] == $_REQUEST['main_acc']) {
                                                            echo '<option value="' . $acc['id'] . '" selected>' . $acc['name'] . ':  Branch: ' . $acc['branch'] . ' -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                        } else {
                                                            echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  Branch: ' . $acc['branch'] . ' -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>




                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Trxn Start
                                            Date *</label>
                                        <input type="date" name="start_date" class="form-control" name="from_date" value="<?= @$_REQUEST['start_date'] ?? date('Y-m-d') ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Trxn End
                                            Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?? date('Y-m-d') ?>" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch Entries</button>
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
                            Income Ledger
                        </h4>


                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="income_ledger" class="table table-striped export-datatable" style="min-width: 845px;" data-title="Income Ledger">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Chart Account</th>
                                        <th>Payment Method</th>
                                        <th>DR:</th>
                                        <th>CR:</th>
                                        <th>REF. NO:</th>
                                        <th>Entered by</th>
                                        <th>Date</th>
                                        <th>Branch</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>




                                </tbody>
                                <tfoot>
                                    <tr class="totals">
                                        <td colspan="5" rowspan="1">Total</td>
                                        <td class="ledger_total" rowspan="1" colspan="1"></td>
                                        <td rowspan="1" colspan="1"></td>
                                        <td rowspan="1" colspan="1"></td>
                                        <td rowspan="1" colspan="1"></td>
                                        <td rowspan="1" colspan="1"></td>
                                        <td rowspan="1" colspan="1"></td>
                                    </tr>
                                </tfoot>
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
    <!--**********************************
        Scripts
    ***********************************-->
    <?php include('includes/bottom_scripts.php'); ?>

    <script type="text/javascript">
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        $(document).ready(function() {

            $.ajax({
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_transactions_incomes.php?branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] ?? $_REQUEST['branchId'] : '' ?>&bank=<?php echo $user[0]['bankId']; ?>&acid=<?= @$_REQUEST['main_acc'] ?>&start_date=<?= @$_REQUEST['start_date'] ?? date('Y-m-d') ?>&end_date=<?= $_REQUEST['end_date'] ?? date('Y-m-d') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    bindtoDatatable(data.data);
                    // console.log(data.data);
                }
            });

        });

        function bindtoDatatable(data) {

            let total_ledger = 0;
            for (let item of data) {
                total_ledger += parseFloat(item.actual_amount)
            }
            $('.ledger_total').text(numberWithCommas(total_ledger));


            var table = $('#income_ledger').dataTable({
                destroy: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                "aaData": data,

                "columns": [{
                    "data": "count"
                }, {
                    "data": "description"
                }, {
                    "data": "account"
                }, {
                    "data": "pay_method"
                }, {
                    "data": "dr"
                }, {
                    "data": "cr"
                }, {
                    "data": "ref_no"
                }, {
                    "data": "auth"
                }, {
                    "data": "date"
                }, {
                    "data": "branch"
                }, {
                    "data": "actions"
                }]
            })

        }
    </script>

</body>

</html>