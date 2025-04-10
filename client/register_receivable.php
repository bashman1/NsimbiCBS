<?php
include('../backend/config/session.php');
?>
<?php

include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->createReceivable($_POST);
    if ($res) {
        setSessionMessage(true, 'Receivable Registered Successfully!');
        header('location:accounting_tab.php#receivables');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to register the Payable.');
        header('location:register_receivable.php?id=' . $_POST['id']);
        exit;
    }
}
require_once('includes/head_tag.php');

$details = $response->getDebtorDetails($_GET['id'])[0];

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

                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                        </h4>
                        <p class="text-muted mb-3">Register Receivable</p>

                        <hr class="hr-dashed">

                        <div class="row pricingTable1">
                            <div class="col-md-5">
                                <h4 class="mt-0 header-title">Debtor Details</h4>
                                <p class="text-muted mb-3"></p>

                                <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                    <li><b>NAMES </b> : <?= $details['name'] ?></li>
                                    <li><b>CHART ACCOUNT </b> : <?= $details['chart'] ?></li>
                                    <li><b>CREATED ON </b> : <?= normal_date($details['date_created']) ?></li>
                                    <li><b>Total Payable </b> : <?= $details['receivable'] ?></li>
                                    <li><b>Total Paid </b> : <?= $details['paid'] ?></li>
                                    <li><b>Oustanding </b> : <?= $details['oustanding'] ?></li>
                                </ul>
                            </div>
                            <div class="col-md-7">
                                <h4 class="mt-0 header-title">Register Receivables Form</h4>
                                <p class="text-muted mb-3"><?= $details['id'] . ' - ' . $details['name'] ?></p>

                                <form method="post" class="submit_with_ajax">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
                                            <input type="hidden" name="chartid" value="<?= $details['chart_id'] ?>">
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
                                                <label>Description: </label>
                                                <input type="text" id="heading" class="form-control" name="heading" value="" required="" placeholder="">
                                            </div>

                                            <div class="form-group">
                                                <label>Amount: </label>
                                                <input type="text" step="any" class="form-control comma_separated" name="amount" value="" required="">
                                            </div>

                                            <div class="form-group">
                                                <label>Select Payment Method: </label>
                                                <select id="payment_methods" name="pay_method" class="form-control select2">
                                                    <option value="cash" selected>Cash </option>

                                                    <option value="cheque"> Cheque/Bank</option>

                                                    <option value="saving">Credit Member's Savings </option>
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

                                        </div>

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label>Trxn Date: </label>
                                                <input type="date" class="form-control" name="record_date" value="<?= date('Y-m-d') ?>" required="">
                                            </div>

                                            <div class="form-group">
                                                <label>Maturity Date: <i>*</i> </label>
                                                <input type="date" class="form-control" name="maturity_date" value="" placeholder="" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Comment <i>optional</i>: </label>
                                                <textarea id="comment" rows="4" class="form-control" name="comment"></textarea>
                                            </div>
                                            <!-- <br /><br /> -->

                                            <!-- <div class="col-md-4"> -->



                                            <div class="form-group" id="insert_cheque_no" style="display: none;">
                                                <label>Cheque No: </label>
                                                <input id="cheque_no" name="cheque_no" class="form-control" value="">
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



                                            <!-- </div> -->
                                            <br /><br /><br />

                                            <button type="submit" class="btn btn-primary" name="submit">Register Receivable</button>
                                        </div>
                                    </div>

                                </form>
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


                if (pay_method.val() == 'cash' || pay_method.val() == 'dr_cash' || pay_method.val() == 'cr_cash') {
                    dest_bank.hide();
                    payable_account.hide();
                    insert_cheque_no.hide();
                    offset_savings.hide();
                    dest_cash_acc.show();
                } else if (pay_method.val() == 'cheque' || pay_method.val() == 'dr_cheque' || pay_method.val() ==
                    'cr_cheque') {
                    dest_cash_acc.hide();
                    payable_account.hide();
                    offset_savings.hide();
                    dest_bank.show();
                    insert_cheque_no.show();
                } else if (pay_method.val() == 'saving' || pay_method.val() == 'savings') {
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
                    if (pay_method.val() == 'cash' || pay_method.val() == 'dr_cash' || pay_method.val() ==
                        'cr_cash') {
                        dest_bank.hide();
                        payable_account.hide();
                        insert_cheque_no.hide();
                        offset_savings.hide();
                        dest_cash_acc.show();
                    } else if (pay_method.val() == 'cheque' || pay_method.val() == 'dr_cheque' || pay_method.val() == 'cr_cheque') {

                        dest_cash_acc.hide();
                        payable_account.hide();
                        offset_savings.hide();
                        dest_bank.show();
                        insert_cheque_no.show();
                    } else if (pay_method.val() == 'saving' || pay_method.val() == 'savings') {
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