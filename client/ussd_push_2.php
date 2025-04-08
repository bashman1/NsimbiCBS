<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('add_deposit')) {
    return $permissions->isNotPermitted(true);
}
$title = 'MM DEPOSIT';
require_once('includes/head_tag.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $amount = str_replace(",", "", $_POST['amount']);
    // make loan repayment
    $endpoint = "https://app.ucscucbs.net/backend/api/mobile_money/yo_deposit.php";
    $url = $endpoint;
    $data = array(
        'amount'      => $amount,
        'phone'      => $_POST['phone'],
        'narr'      => $_POST['notes'],


    );

    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => json_encode($data),
            'header' =>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
        )
    );

    $context  = stream_context_create($options);
    $responsen = file_get_contents($url, false, $context);
    $data = json_decode($responsen, true);

    setSessionMessage(true, 'Deposit Initiated Successfully!');
}


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
                                <?php
                             
                                ?>

                                <h4 class="mt-0 header-title">
                                    <a href="search_client.php" class="btn btn-primary light btn-xs mb-1 "><i class="fa fa-arrow-left"></i> Back</a> | Mobile Money Deposit Form
                                </h4>

                                <p class="text-muted mb-3"><?= $is_inter_branch; ?></p>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <form method="post" class="submit_with_ajax">

                                            <div class="form-group">
                                                <label for="projectName">Deposit Amount :</label>
                                                <input type="text" value="0" name="amount" min="0" class="form-control comma_separated" required data-type="amount">
                                            </div>
                                            <div class="form-group">
                                                <label for="projectName">Phone Number :</label>
                                                <input type="text" name="phone" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="projectName">Description :</label>
                                                <input type="text" name="notes" class="form-control" required>
                                            </div>




                                            <br /><br />

                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit">Process
                                                Transaction</button>
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
        <?php
        include('includes/bottom_scripts.php');
        ?>



</body>

</html>