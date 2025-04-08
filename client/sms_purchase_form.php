<?php
include('../backend/config/session.php');
?>
<?php

require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submitc'])) {
    $res = $response->purchaseSMS($_POST['bid'], $_POST['amount'], $_POST['pay_method'], $_POST['branch']);
    if ($res) {
setSessionMessage(true,'SMS Purchase Requisition Submitted Successfully! Reach out to UCSCU Finance Dep\'t (+256701601305) & Make Payment.');
        header('location:sms_manage.php');
        // exit;
    } else {
setSessionMessage(false,'Something went wrong! Try again to initiate the SMS purchase Requisition');
        header('location:sms_manage.php');
        // exit;
    }
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
                <div class="card">
                    <div class="card-body">
                     

                        <h4 class="mt-0 header-title">
                        <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>  
                        SMS Purchase Requisition Form</h4>


                        <!-- <p class="text-mutesd mb-3">Till Cash Balance: <b></b></p> -->

                        <hr class="hr-dashed">

                        <form method="post" class="submit_with_ajax">
                        <input type="hidden" id="bid" class="form-control" name="bid"
                                            placeholder="" required="" value="<?php echo $_POST['bid'];?>">
                            <div class="row">
                                <div class="col-md-4">

                                  

                                    <div class="form-group">
                                        <label>Entered Amount: </label>
                                        <input type="readonly" step="any" id="total_amount" class="form-control"
                                            name="amount" placeholder="" required="" value="<?php echo $_POST['amount'];?>">
                                    </div>


                                


                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Payment Method: </label>
                                        <select id="payment_methods" name="pay_method" class="form-control select2">
                                            <option value="cash" selected>Cash</option>
                                            <option value="cheque" >Cheque/Bank/Mobile Money</option>
                                            <option value="online" >Online Payment Methods (e.g Flutter-wave)</option>
                                        </select>

                                        </select>
                                    </div>

                                   
                                </div>

                                <div class="col-md-4">

                                   

                                <?php
                                if (isset($_POST['bid'])) {
                                    $branches = $response->getBankBranches($_POST['bid']);

                                    echo '
                        
                          <div class="mb-3">
                              <label class="text-label form-label">Select Branch to which the SMS bundles shall belong *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                            
                                  ';
                                    if ($branches !== '') {
                                        foreach ($branches as $row) {
                                            echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                        }
                                    } else {
                                        echo '
                              <option readonly>No Branches Attached to this Bank</option>
                              ';
                                    }

                                    echo
                                    '
                          
                              </select>
                          </div>
                         
                          
                          ';
                                }
                                ?>

<br/><br/>

                                    <button type="submit" class="btn btn-primary btn-block" name="submitc"> Purchase</button>

                                </div>
                            </div>
                        </form>
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