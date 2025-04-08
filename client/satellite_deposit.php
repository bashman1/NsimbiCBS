<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {


    $res = $response->registerLiability($_POST);
    if ($res) {
        setSessionMessage(true, 'Liability Registered Successfully!');
        header('location:satellite_deposit.php');
    } else {
        setSessionMessage(false, 'Liabiltiy Registration failed');
        header('location:satellite_deposit.php');
    }

    exit;
}
$title = ' REGISTER LIABILITY';
require_once('includes/head_tag.php');
$cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);

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
                            Liability Journal Entry
                        </h4>


                        <!-- <p class="text-mutesd mb-3">Till Cash Balance: <b></b></p> -->

                        <hr class="hr-dashed">

                        <form method="post" class="submit_with_ajax">
                            <input type="hidden" name="user" value="<?= @$user[0]['userId']; ?>" />
                            <input type="hidden" name="bank" value="<?= @$user[0]['bankId']; ?>" />
                            <input type="hidden" name="bid" value="<?= @$user[0]['branchId']; ?>" />
                            <div class="row">
                                <div class="col-md-4">

                                    <div class="form-group">
                                        <label control-label> Entry Description: </label>
                                        <input type="text" id="heading" class="form-control" name="heading" placeholder="" required="">
                                    </div>

                                    <div class="form-group">
                                        <label>Entry Amount: </label>
                                        <input type="text" id="total_amount" class="form-control comma_separated" name="amount" placeholder="" required="">
                                    </div>

                                    <?php
                                    if (!$user[0]['branchId']) {
                                        $branches = $response->getBankBranches($user[0]['bankId']);

                                        echo '
                          <div class="form-group ">
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
                          
                          ';
                                    } else {
                                        echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label>Select Journal Account: </label>
                                        <select name="main_acc" class="form-control select2" id="journalacc">
                                            <option value="">Select....</option>
                                            <?php
                                            $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                            if ($sub_accs) {

                                                foreach ($sub_accs as $acc) {
                                                    if ($acc['type'] == 'LIABILITIES') {

                                                        echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  Branch: ' . $acc['branch'] . ' -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Payment Method: </label>
                                        <select id="payment_methods" name="pay_method" class="form-control select2">
                                            <option value="cash_increase" selected>Credit Cash (Increases)</option>
                                            <!-- <option value="cash_decrease" selected>Debit Cash (Decreases)</option> -->
                                            <option value="cheque_increase" selected>Credit Cheque/Bank (Increases)</option>
                                            <!-- <option value="cheque_decrease" selected>Debit Cheque/Bank (Decreases)</option> -->
                                            <option value="saving_increase" selected>Credit Member's Savings (Increases)</option>
                                            <!-- <option value="saving_decrease" selected>Offset from Member's Savings (Decreases)</option> -->
                                        </select>

                                        </select>
                                    </div>

                                    <div class="form-group" id="dest_bank" style="display: none;">
                                        <label> Bank Account: </label>
                                        <select id="bank_acc" name="bank_acc" class="form-control select2">
                                            <?php
                                            if ($bank_accounts) {

                                                foreach ($bank_accounts as $b_acc) {
                                                    echo '<option value="' . $b_acc['cid'] . '">' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . ' - Bank: ' . $b_acc['bank_name'] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group" id="offset_savings" style="display: none;">
                                        <label>Select Savings A/C</label>
                                        <select class="me-sm-2 default-select form-control wide select2x" id="clientsselect" name="account_id">


                                        </select>
                                    </div>

                                    <div class="form-group" id="dest_cash_acc" style="display: none;">
                                        <label> Cash Account: </label>
                                        <select id="cash_acc" name="cash_acc" class="form-control select2">


                                            <?php
                                            if ($_SESSION['user']['bankId']) {
                                                foreach ($cash_accounts as $cash_account) {
                                            ?>
                                                    <option value="<?= $cash_account['cid'] ?>">
                                                        <?= $cash_account['acname'] ?>
                                                    </option>
                                            <?php }
                                            } else {
                                                foreach ($cash_accounts as $c_acc) {
                                                    if ($c_acc['userid'] == $user[0]['userId']) {
                                                        echo '<option value="' . $c_acc['cid'] . '"> ' . $c_acc['acname'] . '</option>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>



                                </div>

                                <div class="col-md-4">

                                    <div class="form-group" id="insert_cheque_no" style="display: none;">
                                        <label>Cheque No: </label>
                                        <input id="cheque_no" name="cheque_no" class="form-control" value="">
                                    </div>

                                    <div class="form-group">
                                        <label>Record Date: </label>
                                        <input type="date" class="form-control" name="date_of_p" value="<?= date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="" required="">
                                    </div>

                                    <div class="form-group">
                                        <label>Comment <i>optional</i>: </label>
                                        <textarea id="comment" rows="4" class="form-control" name="comment" placeholder=""></textarea>
                                    </div>

                                    <br /><br />

                                    <button type="submit" class="btn btn-primary btn-block" name="submit">Register Liability</button>

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
        <script type="text/javascript">
            $(document).ready(function() {
                pay_method_change();
            });
        </script>

        <script>
            function pay_method_change() {
                var pay_method = $('#payment_methods');
                var dest_bank = $('#dest_bank');
                var insert_cheque_no = $('#insert_cheque_no');
                var dest_cash_acc = $('#dest_cash_acc');
                var offset_savings = $('#offset_savings');
                var payable_account = $('#payable_account');


                if (pay_method.val() == 'cash_increase' || pay_method.val() == 'cash_decrease' || pay_method.val() == 'cr_cash' || pay_method.val() == 'dr_cash') {
                    dest_bank.hide();
                    payable_account.hide();
                    insert_cheque_no.hide();
                    offset_savings.hide();
                    dest_cash_acc.show();
                } else if (pay_method.val() == 'cheque_increase' || pay_method.val() == 'cheque_decrease' || pay_method.val() ==
                    'cr_cheque' || pay_method.val() == 'dr_cheque') {
                    dest_cash_acc.hide();
                    payable_account.hide();
                    offset_savings.hide();
                    dest_bank.show();
                    insert_cheque_no.show();
                } else if (pay_method.val() == 'saving_increase' || pay_method.val() == 'saving_decrease') {
                    dest_bank.hide();
                    payable_account.hide();
                    insert_cheque_no.hide();
                    dest_cash_acc.hide();
                    offset_savings.show();
                } else if (pay_method.val() == 'on_credit') {
                    dest_bank.hide();
                    payable_account.show();
                    insert_cheque_no.hide();
                    dest_cash_acc.hide();
                    offset_savings.hide();
                } else {
                    dest_cash_acc.hide();
                    payable_account.hide();
                    dest_bank.hide();
                    offset_savings.hide();
                    insert_cheque_no.hide();
                }

                pay_method.on('change', function() {
                    if (pay_method.val() == 'cash_increase' || pay_method.val() == 'cash_decrease' || pay_method.val() ==
                        'cr_cash') {
                        dest_bank.hide();
                        payable_account.hide();
                        insert_cheque_no.hide();
                        offset_savings.hide();
                        dest_cash_acc.show();
                    } else if (pay_method.val() == 'cheque_increase' || pay_method.val() == 'cheque_decrease' || pay_method.val() == 'cr_cheque') {

                        dest_cash_acc.hide();
                        payable_account.hide();
                        offset_savings.hide();
                        dest_bank.show();
                        insert_cheque_no.show();
                    } else if (pay_method.val() == 'saving_increase' || pay_method.val() == 'saving_decrease') {
                        dest_bank.hide();
                        payable_account.hide();
                        insert_cheque_no.hide();
                        dest_cash_acc.hide();
                        offset_savings.show();
                    } else if (pay_method.val() == 'on_credit') {
                        dest_bank.hide();
                        payable_account.show();
                        insert_cheque_no.hide();
                        dest_cash_acc.hide();
                        offset_savings.hide();
                    } else {
                        dest_cash_acc.hide();
                        payable_account.hide();
                        dest_bank.hide();
                        offset_savings.hide();
                        insert_cheque_no.hide();
                    }

                });
            }
            // -------------end --------------


            $(document).ready(function() {
                $("select.select2x").select2({
                    ajax: {
                        url: "<?php echo BACKEND_BASE_URL ?>User/get_all_bank_clients_search.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>",
                        dataType: 'json',
                        data: (params) => {
                            return {
                                q: params.term,
                            }
                        },

                        processResults: (data, params) => {
                            const results = data.data.map(item => {
                                return {
                                    id: item.userId,
                                    text: item.accno + ' : ' + item.name + ' - UGX ' + item.tot_balance + '  - Branch: ' + item.branchName,
                                };
                            });
                            return {
                                results: results,
                            }
                        },
                    },
                });
            })
        </script>

</body>

</html>