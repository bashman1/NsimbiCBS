<?php
include('../backend/config/session.php');
?>
<?php


include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $details = $response->getAccountDetails($_GET['id'])[0];
} else {
    setSessionMessage(false, 'Please select an existing account');
    header('location:accounting_tab');
    exit;
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

                    <div class="card">
                        <div class="card-body">

                            <h4 class="mt-0 header-title"><a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | <a href="add_sub_sub_account?id=<?= $details['id'] ?>" class="btn btn-sm btn-soft-primary load_via_ajax"><i class="ti-arrow-circle-left"></i> Add Sub Account</a> | Chart Of Account Details</h4>
                            <p class="text-muted mb-3"> </p>

                            <div class="row">
                                <div class="col-md-4">
                                    <h4 class="mt-0 header-title">Account Name</h4>
                                    <p class="text-muted mb-3"><?= $details['aname'] ?> &nbsp;<?= $details['issys'] ? '<span class="badge light badge-danger">System Generated</span>' : '<span class="badge light badge-success">Not System Generated</span>' ?></p>
                                    <h4 class="mt-0 header-title">Account Description</h4>
                                    <p class="text-muted mb-3"><?= $details['description'] ?></p>
                                    <h4 class="mt-0 header-title">Account Balance</h4>
                                    <p class="text-muted mb-3"><?= number_format($details['balance'] ?? 0) ?></p>
                                    <h4 class="mt-0 header-title">Account Type</h4>
                                    <p class="text-muted mb-3"><?= $details['type'] ?></p>
                                    <h4 class="mt-0 header-title">Branch</h4>
                                    <p class="text-muted mb-3"><?= $details['bname'] ?></p>

                                </div>
                                <div class="col-md-8">
                                    <h4 class="mt-0 header-title">Sub Accounts</h4>
                                    <?php
                                    $sub_accs = $response->getSubSubAccounts($details['id']);
                                    if ($sub_accs != '') {
                                        foreach ($sub_accs  as $sb) {
                                            echo '
                                            <div class="accordion" id="packages">

                                        <div class="card border mb-1 shadow-none checkbox_group">
                                            <div class="card-header"><a href="#" class="text-dark collapsed" data-toggle="collapse" data-target="#hhh" aria-expanded="false" aria-controls="collapseOne"> <b>' . $sb['name'] . ' - Balance:  ' . $sb['balance'] . '</b> <i class="caret"> </i></a> |

                                                   <div class="dropdown ms-auto text-end">
                                                                                            <div class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                                                                        <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                                                                                        <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                                                                                        <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                                                                                    </g>
                                                                                                </svg>
                                                                                            </div>
                                                                                            <div class="dropdown-menu dropdown-menu-end" style="margin: 0px;">
                                                                                                <a class="dropdown-item" href="add_sub_sub_account?id=' . $sb['id'] . '">Add Sub Account</a>
                                                                                                <a class="dropdown-item" href="view_account_details?id=' . $sb['id'] . '">View Details</a>
                                                                                                <a class="dropdown-item text-danger confirm-action" data-href="trash_account?id=' . $sb['id'] . '">Trash Account</a>
                                                                                            </div>
                                                                                        </div>
                                            </div>
                                            <div id="hhh" class="collapse" aria-labelledby="headingOne" data-parent="#packages" style="">
                                                <div class="card-body"></div>
                                            </div>
                                        </div>
                                    </div>
                                            
                                            
                                            ';
                                        }
                                    }
                                    ?>


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
        <?php
        include('includes/bottom_scripts.php');
        ?>
        <!-- <script src="./js/styleSwitcher.js"></script> -->
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


</body>

</html>