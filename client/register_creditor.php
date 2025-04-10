<?php
include('../backend/config/session.php');
?>
<?php

include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->createCreditor($_POST);
    if ($res) {
        setSessionMessage(true, 'Creditor Registered Successfully!');
        header('location:accounting_tab.php#payables');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to register the Creditor.');
        header('location:register_creditor.php');
        exit;
    }
}
$title ='ADD CREDITOR';
require_once('includes/head_tag.php');

$sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
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
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            Register Creditor Form
                        </h4>
                        <p class="text-muted mb-3"> A creditor is an entity that extends credit, giving another entity permission to borrow money to be repaid in the future. A business that provides supplies or services and does not demand immediate payment is also a creditor, as the client owes the business money for services already rendered.</p>

                        <hr class="hr-dashed">

                        <form class="submit_with_ajax" method="post">

                            <div class="row">
                                <div class="col-md-6">

                                    <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>" />
                                    <input type="hidden" name="bank" value="<?= $user[0]['bankId']; ?>" />
                                    <input type="hidden" name="bid" value="<?= $user[0]['branchId']; ?>" />

                                    <?php
                                    if (!$user[0]['branchId']) {
                                        $branches = $response->getBankBranches($user[0]['bankId']);

                                        echo '
                          <div class="form-group ">
                              <label class="text-label form-label">Branch *</label>
                              <select id="branchselect"  class="form-control"  name="branch" required>
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
                          
                          ';
                                    } else {
                                        echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label for="inputPassword" class=" control-label">Creditors Name : </label>
                                        <input type="text" class="form-control" name="cr_name" value="">
                                    </div>

                                    <div class="form-group">
                                        <label for="inputPassword" class=" control-label">Detailed Desecription : </label>
                                        <textarea class="form-control" rows="5" name="descr" minlength="5" maxlength="150" required=""></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="radio radio-success mb-4">
                                        <input type="radio" name="create_chart_account" value="yes" id="create_chart_account1">
                                        <label for="create_chart_account1">Create Chart Account For Creditor</label>
                                        <p class="text-muted mb-3">System will create a chart account for the creditor, this is important for repetitive creditors</p>
                                    </div>

                                    <div class="radio radio-success mb-4">
                                        <input type="radio" name="create_chart_account" value="existing" id="create_chart_account2">
                                        <label for="create_chart_account2"> Select Chart Account From Existing</label>
                                        <p class="text-muted mb-3">This allows you select an account from the existing chart accounts</p>
                                    </div>

                                    <div class="form-group" id="existing_div" style="padding-left: 20px; display: none;">
                                        <label>Select Payables Category/Account:<i>*</i> </label>
                                        <select name="account_code" class="form-control " id="exp_account">
                                            <?php

                                            if ($sub_accs) {

                                                foreach ($sub_accs as $acc) {
                                                    if ($acc['type'] == 'LIABILITIES') {

                                                        echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ': Branch:  '.$acc['branch'].'  -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                    }
                                                }
                                            }
                                            ?>

                                        </select>
                                    </div>


                                    <button type="submit" name="submit" class="btn  btn-primary"><i class="fa fa-send"></i> Save <span class="fa fa-send"></span></button>

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

        <script type="text/javascript">
            $(document).ready(function() {
                var existing_div = $('#existing_div');
                $('input[type=radio][name=create_chart_account]').change(function() {
                    $(this).val() == 'existing' ? existing_div.show() : existing_div.hide();
                });

            });
        </script>
</body>

</html>