<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('data_importer_loans')) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->updateImportedClientAccBal($_POST);
    // var_dump($res);
    // exit;
    if ($res) {
        setSessionMessage();
    } else {
        setSessionMessage(false);
    }

    RedirectCurrent();
    exit;
}

$request = ['id' => decrypt_data($_GET['id']), 'type' => 'client'];
$client = $response->getDataImporterRecordDetails($request);


?>
<?php
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Data Importer Clients | Edit <?= ucwords($client['client_type']) ?> A/C Balance
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form method="post">
                                            <input type="hidden" name="client_id" value="<?= @$client['id'] ?>">
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format(@$client['freezed_amount']) ?>" name="FreezedAmount" min="0" class="form-control comma_separated">
                                                        <label for="">Imported Freezed Amount :</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format(@$client['account_balance']) ?>" name="orig_bal" class="form-control comma_separated">
                                                        <label for="">Imported Balance</label>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row mt-3">


                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" name="new_bal" class="form-control comma_separated">
                                                        <label for="">Right Balance</label>
                                                    </div>
                                                </div>
                                            </div>





                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit mt-4">
                                                Update A/C Balance
                                            </button>
                                            <!--end form-->
                                        </form>
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