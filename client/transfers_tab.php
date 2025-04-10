<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->hasSubPermissions('create_transfer')) {
    return $permissions->isNotPermitted(true);
}
$title = 'CASH TRANSFERS';
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
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <button type="button" class="btn btn-primary card-title"><span
                                        class="btn-icon-start text-primary"><i class="fa fa-arrow-left"></i>
                                    </span>Back</button> -->
                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Savings' Transfers
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item"><a href="#onetoone" data-bs-toggle="tab" class="nav-link  <?= !in_array(@$_REQUEST['current_tab'], ['onetomany']) ? 'active' : '' ?> ">Customer Balances' Transfers</a>
                                        </li>



                                    </ul>
                                    <div class="tab-content">
                                        <div id="products" class="tab-pane fade <?= !in_array(@$_REQUEST['current_tab'], ['onetomany']) ? 'show active' : '' ?>" role="tabpanel">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Inter-Account Transfers</h4>
                                                    <a href="one_to_one_transfer.php" class="btn btn-primary light btn-xs mb-1">One to One Transfer</a>
                                                    <a href="one_to_many_transfer.php" class="btn btn-primary light btn-xs mb-1">One to Many Transfer</a>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="onetoone_table" class="table table-striped" style="width: 100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Sender</th>
                                                                    <th>Receiver</th>
                                                                    <th>Amount</th>
                                                                    <th>Notes</th>
                                                                    <th>Authorised by</th>
                                                                    <th>Branch</th>
                                                                    <th>Date Created</th>
                                                                    <th>Actions</th>
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

        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                $.ajax({
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_cash_transfers.php?branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>&bank=<?php echo $user[0]['bankId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        getOneToMany(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function getOneToMany(data) {

                var table = $('#onetomany_table').dataTable({
                    destroy: true,
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },

                    "aaData": data,
                    "columns": [{
                            "data": "tid"
                        }, {
                            "data": "sender"
                        }, {
                            "data": "receiver"
                        }, {
                            "data": "amount"
                        },
                        {
                            "data": "notes"
                        }, {
                            "data": "auth"
                        }, {
                            "data": "branch"
                        }, {
                            "data": "date"
                        }, {
                            "data": "actions",
                        }
                    ]
                })

            }
        </script>



</body>

</html>