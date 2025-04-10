<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    exit();
}

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankAdmin()) {
    return $permissions->isNotPermitted(true);
}


$response = new Response();

if (isset($_POST['submit'])) {
    $amount = str_replace(",", "", $_POST['amount']);
    $res = $response->updateShareValue($amount, $user[0]['bankId'], $_POST['account_id']);
    if ($res) {
        setSessionMessage(true, 'Share Value Updated Successfully!');
        header('location:shares_settings');
        exit;
    } else {
        setSessionMessage(false, 'Share Value Update failed!');
        header('location:shares_settings');
        exit;
    }
}
$title = 'SHARES SETTINGS';
require_once('includes/head_tag.php');
$share_details = $response->getBankShareValue($user[0]['bankId'], $user[0]['branchId']);

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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">


                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Shares Settings Form
                                </h4>

                                <p class="text-muted mb-3"></p>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <form method="post" class="submit_with_ajax">

                                            <div class="form-group">
                                                <label for="projectName">Current Share Value :</label>
                                                <input type="text" value="<?php echo $share_details[0]['value'] ?? 0 ?>" name="amount" id="amount" min="0" class="form-control" required data-type="amount">
                                            </div><br />
                                            <!--end form-group-->
                                            <?php

                                            $accounts = $response->getSubAccounts2($_SESSION['user']['branchId'], $_SESSION['user']['bankId']);
                                            ?>
                                            <div class="form-group">
                                                <label class="text-label form-label">Select Chart Account associated with Member Shares *</label>

                                                <select id="oscategory" class="form-control" name="account_id" required>

                                                    <?php foreach ($accounts as $account) {
                                                        if ($account['id'] == $share_details[0]['share_acid']) {
                                                            echo '<option value="' . $account['id'] . '" selected>' . $account['name'] . '</option> ';
                                                        }
                                                    ?>
                                                        <option value="<?= $account['id'] ?>">
                                                            <?= $account['name'] ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <br />
                                            <br />

                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit">Update Value</button>
                                            <!--end form-->
                                    </div>
                                    <!--end col-->

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
            $(document).ready(function() {
                $("input[data-type='amount']").keyup(function(event) {
                    // skip for arrow keys
                    if (event.which >= 37 && event.which <= 40) {
                        event.preventDefault();
                    }
                    var $this = $(this);
                    var num = $this.val().replace(/,/gi, "");
                    var num2 = num.split(/(?=(?:\d{3})+$)/).join(",");
                    // console.log(num2);
                    // the following line has been simplified. Revision history contains original.
                    $this.val(num2);
                });
            });
        </script>



</body>

</html>