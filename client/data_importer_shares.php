<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('data_importer_shares')) {
    return $permissions->isNotPermitted(true);
}

$responser = new Response();

if (isset($_POST['clients_data'])) {
    $data = ['files' => $_FILES, 'fields' => $_POST, 'start_counter' => 2];
    $res = $responser->dataImporterClients($data);

    // var_dump($data);
    var_dump($res);
    exit;
    if ($res) {
        setSessionMessage();
        header('Location:fees_tab.php?current_tab=account_opening_fees');
    } else {
        setSessionMessage(false);
        header('Location:fees_tab.php?current_tab=account_opening_fees');
    }
    exit;
}


// var_dump($resources);
// exit;
?>

<?php
$title = 'SHARES IMPORTER';
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Data Importer
                                </h4>

                                <a href="export_report.php?exportFile=export_data_importer_resources&useFile=1" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Data Importer Resources
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a href="#share_register" data-bs-toggle="tab" class="nav-link  active">Share Register</a>
                                                </li>

                                                <li class="nav-item">
                                                    <a href="#share_purchases" data-bs-toggle="tab" class="nav-link">Share Purchases</a>
                                                </li>

                                                <li class="nav-item">
                                                    <a href="#share_transfers" data-bs-toggle="tab" class="nav-link">Share Transfers</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div id="share_register" class="tab-pane fade active show">
                                                    <?php require_once('data_importer_share_register.php') ?>
                                                </div>

                                                <div id="share_purchases" class="tab-pane fade">
                                                    <?php require_once('data_importer_share_purchases.php') ?>
                                                </div>

                                                <div id="share_transfers" class="tab-pane fade">
                                                    <?php require_once('data_importer_share_transfers.php') ?>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->


            </div>
            <?php include('includes/footer.php'); ?>


        </div>
        <?php include('includes/bottom_scripts.php'); ?>
</body>

</html>