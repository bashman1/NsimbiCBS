<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
include_once('includes/response.php');
$response = new Response();
$details = $response->getLoanDetails($_GET['id']);
// $actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);


if (isset($_POST['submit'])) {

    $res = $response->updateLoan($_POST['client'], $_POST['product'], $_POST['disbursedate'], $_POST['amount'], $_POST['duration'], $_POST['notes'], $user[0]['bankId'], $user[0]['branchId'], $user[0]['userId'], $_POST['lno']);
    if ($res) {
setSessionMessage(true, 'Loan Details Updated Successfully!');
        header('location:loan_applications.php');
        // exit;
    } else {
setSessionMessage(false,'Something went wrong! Try again to update loan');
        header('location:edit_loan.php?id=' . $_POST['lno']);
        // exit;
    }
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
                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Loans</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Edit Loan Application</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Update Loan Application
                                </h4>
                                <?php
                                if (isset($_GET['success'])) {
                                    echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                                    // unset($_SESSION['success']);
                                }
                                if (isset($_GET['error'])) {
                                    echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                                }

                                ?>
                            </div>
                            <div class="card-body">

                                <div id="smartwizard" class="form-wizard order-create">
                                    <ul class="nav nav-wizard">
                                        <li><a class="nav-link" href="#wizard_Service">
                                                <span>1</span>Edit Application
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_confirm">
                                                <span>✓</span>Confirm & Submit
                                            </a></li>
                                    </ul>
                                    <form method="POST">
                                        <input type="hidden" name="lno" class="form-control" value="<?php echo $_GET['id']; ?>">
                                        <input type="hidden" name="client" class="form-control" value="<?php echo $details[0]['client']['userId']; ?>">

                                        <div class="tab-content">
                                            <div id="wizard_Service" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">General Information</h4>

                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Client
                                                                *</label>
                                                            <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="client2" required disabled>
                                                                <option selected value="<?php echo $details[0]['client']['userId']; ?>"><?php echo $details[0]['client']['firstName'] . ' ' . $details[0]['client']['lastName']; ?></option>
                                                              
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Loan Product *</label>
                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="product" style="display: none;" required>
                                                                <option selected value="<?php echo $details[0]['product']['type_id']; ?>"><?php echo $details[0]['product']['type_name']; ?></option>
                                                                <?php
                                                                foreach ($response->getAllBankLoanProducts($user[0]['bankId'], $user[0]['branchId']) as $row) {
                                                                    echo '
                                                    <option value="' . $row['id'] . '">' . $row['name'] . '  - ' . $row['rate'] . '</option>
                                                    ';
                                                                }
                                                                ?>

                                                            </select>
                                                        </div>
                                                    </div><br />
                                                    <h4 class="card-title " style="color:#005a4b;">Loan Terms</h4><br />

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Requested Disbursement
                                                                Date*</label>
                                                            <input type="date" name="disbursedate" class="form-control" placeholder="" required value=" <?php echo date('m/d/Y', strtotime($details[0]['loan']['requesteddisbursementdate'])); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Requested Loan
                                                                Amount*</label>
                                                            <input type="number" name="amount" class="form-control" placeholder="" required min="0" value="<?php echo $details[0]['loan']['approvedamount']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Requested Duration in
                                                                Days*</label>
                                                            <input type="number" name="duration" class="form-control" placeholder="" required min="1" value="<?php echo $details[0]['loan']['approved_loan_duration']; ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Notes or
                                                                Comments*</label>
                                                            <textarea class="form-control" name="notes" placeholder="Type your Comments about this loan application here..."><?php echo $details[0]['loan']['notes']; ?></textarea>
                                                        </div>
                                                    </div>



                                                </div>
                                            </div>

                                            <div id="wizard_confirm" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">Confirm & Submit Changes</h4>


                                                <div class="row ">
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <button type="submit" name="submit" class="btn btn-primary">Edit Loan Application</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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
        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });



            function myFunction() {
                let x = document.getElementById("amount");

                document.getElementById("amount").innerHTML = x.value;
            }
        </script>

</body>

</html>