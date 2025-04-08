<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('data_importer_coa_tb')) {
    return $permissions->isNotPermitted(true);
}

$responser = new Response();

$details = $responser->getOverDraftDetails($_GET['id'])[0];

        // Define the two dates
        $date1 = new DateTime(date('Y-m-d', strtotime($details['approval_date'])));
        $date2 = new DateTime(date('Y-m-d'));

        // Calculate the difference
        $interval = $date1->diff($date2);

        // Get the number of days
        $no_days =  $interval->days;

?>

<?php
$title = 'OVER-DRAFT PAYMENT';
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
                                    Over Draft Payment
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a href="#transactions" data-bs-toggle="tab" class="nav-link  active">Over-Draft</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div id="transactions" class="tab-pane fade active show">
                                                    <h3 class="mt-3 mb-2">
                                                        <?=
                                                        $details['client_name'] . ' ( ' . $details['acno'] . ' )' ?>
                                                    </h3>

                                                    <div class="basic-form">
                                                        <form action="<?= BACKEND_BASE_URL ?>Accounting/close_overdraft.php" class="custom-form" data-reload-page="1" data-confirm-action="1">
                                                            <div class="row">
                                                                <input type="hidden" name="acc_balance" value="<?= $details['acc_balance'] ?>">
                                                                <input type="hidden" name="authby" value="<?= $details['authby'] ?>">
                                                                <input type="hidden" name="daily_rate" value="<?= $details['daily_rate'] ?>">
                                                                <input type="hidden" name="approval_date" value="<?= $details['approval_date'] ?>">
                                                                <input type="hidden" name="branch" value="<?= $details['branch'] ?>">
                                                                <input type="hidden" name="duration" value="<?= $details['duration'] ?>">
                                                                <input type="hidden" name="acid" value="<?= $details['acc_id_affected'] ?>">
                                                                <input type="hidden" name="client_id" value="<?= $details['userId']?>">
                                                                <input type="hidden" name="oid" value="<?=$details['odid']?>">
                                                                <input type="hidden" name="income_id" value="<?= $details['income_acid'] ?>">

                                                                <div class="col-md-12">
                                                                    Overdraft Payment Form
                                                                    <br>

                                                                </div>


                                                                <div class="col-md-12">
                                                                    <div class="form-floating">
                                                                        <input type="text" name="dur" class="form-control" required disabled value="<?= $details['duration'] ?>">
                                                                        <label for="amount">Duration (Days)</label>
                                                                    </div>
                                                                    <div class="form-floating">
                                                                        <input type="text" name="dr" class="form-control" required disabled value="<?= $details['daily_rate'] ?>">
                                                                        <label for="amount">Daily Interest Rate (%)</label>
                                                                    </div>
                                                                    <div class="form-floating">
                                                                        <input type="text" name="amount" class="form-control amount" placeholder=" " required disabled value="<?= $details['amount'] ?>">
                                                                        <label for="amount">Principal Amount</label>
                                                                    </div>
                                                                </div><br /><br /><br />
                                                                <div class="col-md-12">
                                                                    <div class="form-floating">
                                                                        <input type="date" name="md" class="form-control" placeholder=" " required disabled value="<?= date('Y-m-d', strtotime($details['approval_date'] . ' + ' . $details['duration'] . ' days')) ?>">
                                                                        <label for="date">Maturity Date</label>
                                                                    </div>
                                                                    <div class="form-floating">
                                                                        <input type="text" name="md" class="form-control" placeholder=" " required disabled value="<?= $no_days ?>">
                                                                        <label for="date">Utilized Days</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">

                                                                    <div class="form-floating">
                                                                        <input type="text" name="int_due" class="form-control amount" placeholder=" " required value="<?= ($details['amount'] * ($details['daily_rate'] / 100) * $no_days) ?>">
                                                                        <label for="int_due">Interest Due</label>
                                                                    </div>


                                                                </div><br /><br /><br />

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="text-label form-label"> </label>
                                                                        <button type="submit" class="btn btn-primary form-control">Make Payment</button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </form>

                                                    </div>
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