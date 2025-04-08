<?php
include('../backend/config/session.php');
?>
<?php



include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->createSubAccount($_POST['name'], $_POST['cname'], $_POST['descr'], $_POST['branch'], $user[0]['bankId'], $user[0]['userId']);
    if ($res) {
        setSessionMessage(true, 'Account Created Successfully!');
        header('location:accounting_tab');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Account not created. Try again');
        header('location:add_sub_account');
        exit;
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

                <!-- row -->
                <div class="row">


                    <div class="card">
                        <div class="card-body">


                            <h4 class="mt-0 header-title"> <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> Add Sub Account Form</h4>
                            <p class="text-muted mb-3"></p>

                            <form method="post" class="panel form-horizontal form-bordered submit_with_ajax" action="">

                                <input type="hidden" name="cname" value="<?php echo $_GET['name']; ?>" class="form-control" required>


                                <div class="form-group">
                                    <label>Account Name:</label>
                                    <input type="text" name="name" value="" class="form-control" required>
                                </div>

                                <?php
                                if (!$user[0]['branchId']) {
                                    $branches = $response->getBankBranches($user[0]['bankId']);

                                    echo '
                          <div class="col-lg-6 mb-2">
                          <div class="mb-3">
                              <label class="text-label form-label">Branch *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                              <option value="0">None</option>
                                  ';
                                    if ($branches !== '') {
                                        foreach ($branches as $row) {
                                            echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                        }
                                    } else {
                                        echo '
                              <option readonly>No Branches Added yet</option>
                              ';
                                    }

                                    echo
                                    '
                          
                              </select>
                          </div>
                          </div>
                          
                          ';
                                } else {
                                    echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                }
                                ?>

                                <div class="form-group">
                                    <label>Account Description:</label>
                                    <textarea class="form-control" rows="6" name="descr" required></textarea>
                                </div>

                                <br /><br />

                                <button type="submit" class="btn btn-primary" name="submit">Save</button>
                            </form>
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