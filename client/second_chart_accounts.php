<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();


$major_accounts = $response->getBankMajorAccounts($user[0]['bankId'], $user[0]['branchId']);

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
                        <li class="breadcrumb-item active"><a href="index.php">Back</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Chart of Accounts</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-md-12">

                        <div class="card">
                            <div class="card-body checkbox_group">

                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Chart Of Accounts
                                </h4>
                                <p class="text-muted mb-3">All accounts</p>

                                <hr class="hr-dashed">

                                <div class="accordion" id="packages">
                                    <?php
                                    $accounts_string = '';
                                    //validate to avoid errors in the for each loop
                                    if ($major_accounts <> null) {
                                        $i = 1;
                                        $act = 0;
                                        //create tab navigation
                                        foreach ($major_accounts as $major) {
                                            $active = !$act ? 'active' : '';

                                            $dataTarget = 's' . str_replace(' ', '', $major['account_code']);
                                            //decrypt code for the urls
                                            $encrypted_code1 = $major['account_code'];
                                            $type_of_account = 'major';

                                            // header
                                            echo '<div class="card border mb-1 shadow-none checkbox_group">';

                                            echo ' <div class="card-header">';
                                            echo '<a href="#" class="text-dark" data-toggle="collapse" data-target="#' . $dataTarget . '" aria-expanded="true" aria-controls="collapseOne"> <b>' . strtoupper($major['account_name']) . '</b> <i class="caret"> </i></a> | 

														<a href="add_sub_account.php?c=' . $encrypted_code1 . '&t=' . $type_of_account . '" class="load_via_ajax"><i class="ti-plus"></i> Add</a>
														';
                                            echo '</div>';

                                            // body
                                            echo '<div id="' . $dataTarget . '" class="collapse" aria-labelledby="headingOne" data-parent="#packages">
																<div class="card-body">';
                                            echo $response->print_sub_account_data($major);
                                            echo '</div>';
                                            echo '</div>';

                                            echo '</div>';
                                            // close parent div
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
        <!-- Required vendors -->
        <?php include('includes/bottom_scripts.php'); ?>

</body>

</html>