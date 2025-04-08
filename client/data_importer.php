<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasPermissions('data_importer')) {
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
        header('Location:fees_tab?current_tab=account_opening_fees');
    } else {
        setSessionMessage(false);
        header('Location:fees_tab?current_tab=account_opening_fees');
    }
    exit;
}


// var_dump($resources);
// exit;
?>

<?php
include('includes/head_tag.php');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.min.js"></script>

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

                                <a href="export_report?exportFile=export_data_importer_resources&useFile=1" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Data Importer Resources
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <ul class="nav nav-tabs" role="tablist">
                                                <?php if ($permissions->hasSubPermissions('data_importer_clients')) { ?>
                                                    <li class="nav-item">
                                                        <a href="#clients" data-bs-toggle="tab" class="nav-link  active">Clients</a>
                                                    </li>
                                                <?php } ?>

                                                <?php if ($permissions->hasSubPermissions('data_importer_loans')) { ?>
                                                    <li class="nav-item">
                                                        <a href="#loans" data-bs-toggle="tab" class="nav-link">Loans</a>
                                                    </li>
                                                <?php } ?>

                                                <?php if ($permissions->hasSubPermissions('data_importer_transactions')) { ?>
                                                    <li class="nav-item">
                                                        <a href="#transactions" data-bs-toggle="tab" class="nav-link">Transactions</a>
                                                    </li>
                                                <?php } ?>

                                                <?php if ($permissions->hasSubPermissions('data_importer_shares')) { ?>
                                                    <li class="nav-item">
                                                        <a href="#shares" data-bs-toggle="tab" class="nav-link">Shares</a>
                                                    </li>
                                                <?php } ?>

                                                <?php if ($permissions->hasSubPermissions('data_importer_coa_tb')) { ?>
                                                    <li class="nav-item">
                                                        <a href="#coa_tb" data-bs-toggle="tab" class="nav-link">Chart of Accounts & Trial Balance</a>
                                                    </li>
                                                <?php } ?>
                                            </ul>

                                            <div class="tab-content">
                                                <?php if ($permissions->hasSubPermissions('data_importer_clients')) { ?>
                                                    <div id="clients" class="tab-pane fade active show">
                                                        <?php require_once('data_importer_clients.php') ?>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($permissions->hasSubPermissions('data_importer_loans')) { ?>
                                                    <div id="loans" class="tab-pane fade">
                                                        <?php require_once('data_importer_loans.php') ?>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($permissions->hasSubPermissions('data_importer_transactions')) { ?>
                                                    <div id="transactions" class="tab-pane fade">
                                                        <?php require_once('data_importer_transactions.php') ?>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($permissions->hasSubPermissions('data_importer_shares')) { ?>
                                                    <div id="shares" class="tab-pane fade">
                                                        <p>Shares</p>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($permissions->hasSubPermissions('data_importer_coa_tb')) { ?>
                                                    <div id="coa_tb" class="tab-pane fade">
                                                        <p> Chart of Account & Trial Balance </p>
                                                    </div>
                                                <?php } ?>
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