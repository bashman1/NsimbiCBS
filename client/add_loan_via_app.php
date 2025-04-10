<?php
include_once('includes/response.php');
$response = new Response();


if (isset($_POST['submit'])) {
    $st = $_POST['lst'];
    $amount = str_replace(",", "", $_POST['amount']);
    $res = $response->addLoan($_POST['client'], $_POST['product'], $_POST['disbursedate'], $amount, $_POST['duration'], $_POST['notes'], '', $_POST['branch'], $_POST['uid'], $_POST['freq']);
    if ($res['success']) {
        if ($st == 2) {
            // redirect manual payment form
            header('location:add_loan_via_agent.php?success2');
            exit;
        } else {
            setSessionMessage(true, 'Loan Application Created Successfully!');
            header('location:add_loan_via_agent.php');
            exit;
        }
    } else {
        setSessionMessage(false, 'Loan Application not Created! Something went wrong. Try again');
        header('location:add_loan_via_agent.php');
        exit;
    }
}
$title = 'LOAN APPLICATION';
require_once('includes/head_tag.php');

$member_info = $response->getMemberDetails($_GET['id'])[0];
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
        // include('includes/nav_bar.php');
        // include('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Apply for a Loan
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
                                                <span>1</span>Fill Out the form
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_confirm">
                                                <span>✓</span>Confirm & Submit
                                            </a></li>
                                    </ul>
                                    <form method="POST">
                                        <input type="hidden" name="lst" class="form-control" value="<?php echo @$_GET['l']; ?>">
                                        <input type="hidden" name="uid" class="form-control" value="<?php echo @$_REQUEST['id'] ?>">
                                        <input type="hidden" name="branch" class="form-control" value="<?php echo @$member_info['branchId'] ?>">
                                        <input type="hidden" name="client" class="form-control" value="<?php echo @$member_info['userId'] ?>">
                                        <div class="tab-content">
                                            <div id="wizard_Service" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">General Information</h4>

                                                <div class="row">



                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Loan Product *</label>
                                                            <select id="single-select2" data-select2-id="single-select2" tabindex="-1" class="select2-hidden-accessible loan-product" aria-hidden="true" name="product" required>
                                                                <option value=""></option>
                                                                <?php
                                                                $lps = $response->getAllBankLoanProducts('', @$member_info['branchId']);
                                                                foreach ($lps as $row) {
                                                                    echo '
                                                    <option value="' . $row['id'] . '" data-duration="' . $row['frequency'] . '">' . $row['name'] . '  - ' . $row['rate'] . ' - ' . $row['method'] . '</option>
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
                                                            <input type="date" name="disbursedate" class="form-control" placeholder="" required value="<?php echo date('Y-m-d'); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Requested Loan
                                                                Amount*</label>
                                                            <input type="text" name="amount" class="form-control" placeholder="" required min="0" value="0" data-type="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Requested Repayment Frequency *</label>

                                                            <select class="me-sm-2 default-select form-control wide" id="freqType" name="freq" style="display: none;" required>

                                                                <option value="1">Daily</option>
                                                                <option value="2">Weekly</option>
                                                                <option value="3">Monthly</option>
                                                                <option value="4">Bi-Monthly</option>
                                                                <option value="5">Yearly</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Requested Duration
                                                                <label id="dtype"> - </label>
                                                                *</label>
                                                            <input type="number" name="duration" class="form-control" placeholder="" required min="1">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Notes or
                                                                Comments</label>
                                                            <textarea class="form-control" name="notes" placeholder="Type your Comments about this loan application here..."></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="row "> -->
                                                        <div class="col-lg-12 mb-3">
                                                            <div class="mb-3">
                                                                <button type="submit" id="submit_btn" name="submit" class="btn btn-primary">Submit Loan Application</button>

                                                            <!-- </div> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- <div id="wizard_confirm" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">Confirm & Submit</h4>



                                                <div class="row ">
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary">Submit Loan Application</button>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
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
            $("#single-select2").select2({
                placeholder: "",
                allowClear: true
            });
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
        <script>
            $('.loan-product').change(function() {
                var selected = $(this).children('option:selected');
                $('#dtype').text(`(${selected.data('duration')})`);
                // if ($(this).find('option:selected').attr('class') == 'DAILY') {
                //     $('#dtype').html(' DAYS');
                // } else if ($(this).find('option:selected').attr('class') == 'WEEKLY') {
                //     $('#dtype').html(' WEEKS');

                // } else if ($(this).find('option:selected').attr('class') == 'MONTHLY') {
                //     $('#dtype').html(' MONTHS');

                // } else if ($(this).find('option:selected').attr('class') == 'YEARLY') {
                //     $('#dtype').html(' YEARS');
                // } else {
                //     $('#dtype').html(' DAYS');
                // }

            });
        </script>
        <script>
            $('#freqType').change(function() {

                if ($(this).find('option:selected').val() == 1) {
                    $('#dtype').html(' DAYS');
                } else if ($(this).find('option:selected').val() == 2) {
                    $('#dtype').html(' WEEKS');

                } else if ($(this).find('option:selected').val() == 3) {
                    $('#dtype').html(' MONTHS');

                } else if ($(this).find('option:selected').val() == 4) {
                    $('#dtype').html(' DAYS');

                } else if ($(this).find('option:selected').val() == 5) {
                    $('#dtype').html(' YEARS');
                } else {
                    $('#dtype').html(' DAYS');
                }

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