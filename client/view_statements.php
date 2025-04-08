<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

$title = 'Member Statements';
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
                                    Statements
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#chart_of_accounts" data-bs-toggle="tab" class="nav-link active"><?= $_GET['name'] ?></a>
                                        </li>


                                    </ul>
                                    <div class="tab-content">

                                        <div id="chart_of_accounts" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <h4 class="text-primary mb-4">Bank Accounts </h4> -->

                                            <div class="col-md-3">
                                                <div class="card no_print">
                                                    <div class="card-body">

                                                        <h4 class="mt-0 header-title">Account Statements</h4>
                                                        <p class="text-muted mb-3"></p>

                                                        <ul class="list-group">
                                                            <a href="" class="list-group-item load_via_ajax">View Profile</a>
                                                            <a href="member_statement_range?id=<?= encrypt_data(@$_GET['id']); ?>" class="list-group-item load_via_ajax"> Account / General Statement</a>
                                                            <a href="saving_statement?id=<?= encrypt_data(@$_GET['id']); ?>" class="list-group-item load_via_ajax"> Savings Statement</a>
                                                            <a href="" class="list-group-item load_via_ajax"> Fixed Deposit Statement</a>
                                                            <a href="" class="list-group-item load_via_ajax"> Shares Statement</a>
                                                            <a href="over_draft_statement?id=<?= encrypt_data(@$_GET['id']); ?>" class="list-group-item load_via_ajax"> Over Drafts Statement</a>
                                                            <a href="" class="list-group-item load_via_ajax"> Loans Statement</a>
                                                            <a href="" class="list-group-item load_via_ajax"> Credit History</a>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <!-- </div> -->
                                    <!-- Modal -->

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


        <script type="text/javascript">

        </script>
</body>

</html>