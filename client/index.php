<?php
require_once(__DIR__ . '../../backend/config/session.php');
require_once(__DIR__ . '/middleware/PermissionMiddleware.php');
require_once(__DIR__ . '/includes/functions.php');

$permissions = new PermissionMiddleware();
$permissions->checkIsAuthenticated(true);

$title = "DASHBOARD";
require_once(__DIR__ . '/includes/response.php');

$response = new Response();
if ($user[0]['bankName'] != '') {
    // $_SESSION['success'] = "UCSCU Core Banking System";
    $_SESSION['success'] = "Welcome to " . $user[0]['bankName'] . " Management System";
} else {
    if ($user[0]['roleId'] == 'becedad5-8159-4543-911f-da4805e29f77') {
        $_SESSION['success'] = "Welcome to NSIMBI Core Banking Platform Admin Panel";
    } else {
        $_SESSION['success'] = "Welcome to your Institution's Management System";
    }
}
// var_dump($_SESSION['working_hours_start_at']);
// var_dump($_SESSION['sub_permissions']);
// exit;

checkWorkingHoursLogin(@$_SESSION['working_hours_start_at']);

// var_dump($_SESSION);
// exit;

?>

<?php include('includes/head_tag.php'); ?>
<style>
    .css-3ci3ci {
        margin-bottom: 0px;
        margin-top: 0px;
        font-size: 24px;
        font-weight: 600;
        line-height: 1.5;
        text-transform: none;
        white-space: normal;
    }

    .css-1ibm745 {
        margin-bottom: 0px;
        margin-top: 0px;
        font-size: 13px;
        color: rgb(148, 164, 196);
        font-weight: 500;
        text-transform: none;
        white-space: normal;
    }

    .css-1z6qsq {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: end;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .css-w0pj6f {
        overflow: hidden;
        pointer-events: none;
        position: absolute;
        z-index: 0;
        inset: 0px;
        border-radius: inherit;
    }

    .css-170pyj5 {
        display: inline-flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
        position: relative;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
        background-color: transparent;
        cursor: pointer;
        user-select: none;
        vertical-align: middle;
        appearance: none;
        font-weight: 600;
        font-family: Gilroy, sans-serif;
        font-size: 0.875rem;
        line-height: 1.75;
        min-width: 64px;
        text-transform: none;
        color: inherit;
        box-shadow: none;
        outline: 0px;
        border-width: 0px;
        border-style: initial;
        border-color: initial;
        border-image: initial;
        margin: 0px;
        text-decoration: none;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1), box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1), border-color 250ms cubic-bezier(0.4, 0, 0.2, 1), color 250ms cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
    }

    .css-8tq7my {
        line-height: 1.5;
        text-transform: none;
        white-space: normal;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .css-1t8mnmp {
        user-select: none;
        width: 1em;
        height: 1em;
        display: inline-block;
        fill: currentcolor;
        flex-shrink: 0;
        font-size: 20px;
        transition: fill 200ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .css-2tc4nl {
        display: inline-flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
        position: relative;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
        cursor: pointer;
        user-select: none;
        vertical-align: middle;
        appearance: none;
        font-weight: 600;
        font-family: Gilroy, sans-serif;
        font-size: 0.875rem;
        line-height: 1.75;
        text-transform: none;
        box-shadow: none;
        color: rgb(31, 90, 45);
        width: 2.2rem;
        height: 2.2rem;
        min-width: 2rem;
        background-color: white;
        outline: 0px;
        margin: 0px;
        text-decoration: none;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1), box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1), border-color 250ms cubic-bezier(0.4, 0, 0.2, 1), color 250ms cubic-bezier(0.4, 0, 0.2, 1);
        border-width: 1px;
        border-style: solid;
        border-image: initial;
        border-color: rgb(31, 90, 45);
        border-radius: 50%;
        padding: 0px !important;
    }

    .css-1z6qsq .filterInputs {
        margin-right: 0.8px;
        display: flex;
        -webkit-box-pack: justify;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .css-70qvj9 {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
    }

    .css-57kesc {
        user-select: none;
        width: 1em;
        height: 1em;
        display: inline-block;
        fill: currentcolor;
        flex-shrink: 0;
        font-size: 16px;
        transition: fill 200ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .css-xrqk3y {
        line-height: 1.5;
        text-transform: none;
        white-space: normal;
        display: block;
        font-weight: 500;
        margin-left: 5px;
    }

    .css-mxldl9 {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        line-height: 1.5;
        border-radius: 1.2rem;
        padding: 0.5rem 0.75rem;
        border: 0.0625rem solid transparent;
        cursor: pointer;
        color: rgb(31, 90, 45);
        background-color: rgb(234, 235, 235);
        font-size: 14px;
    }

    .css-xrqk3y {
        line-height: 1.5;
        text-transform: none;
        white-space: normal;
        display: block;
        font-weight: 500;
        margin-left: 5px;
    }

    .css-1xynra6 {
        background-color: rgb(255, 255, 255);
        color: rgb(29, 36, 56);
        background-image: none;
        box-shadow: none;
        display: flex;
        -webkit-box-pack: justify;
        justify-content: space-between;
        -webkit-box-align: center;
        align-items: center;
        transition: box-shadow 300ms cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border-width: 1px;
        border-style: solid;
        border-color: rgb(229, 234, 242);
        border-image: initial;
        border-radius: 8px;
        padding: 1rem 1.5rem;
    }

    @media (max-width: 599.95px) {
        .css-1xynra6 {
            flex-direction: column;
            padding: 2rem;
            align-items: flex-start;
        }
    }

    @media (min-width: 960px) {
        .css-6xchgi {
            flex-basis: 100%;
            -webkit-box-flex: 0;
            flex-grow: 0;
            max-width: 100%;
        }
    }

    @media (min-width: 600px) {
        .css-6xchgi {
            flex-basis: 100%;
            -webkit-box-flex: 0;
            flex-grow: 0;
            max-width: 100%;
        }
    }

    @media (min-width: 400px) {
        .css-6xchgi {
            flex-basis: 100%;
            -webkit-box-flex: 0;
            flex-grow: 0;
            max-width: 100%;
        }
    }

    .css-6xchgi {
        box-sizing: border-box;
        margin: 0px;
        flex-direction: row;
        flex-basis: 100%;
        -webkit-box-flex: 0;
        flex-grow: 0;
        max-width: 100%;
    }

    .css-1d3bbye {
        box-sizing: border-box;
        display: flex;
        flex-flow: wrap;
        width: 100%;
    }

    @media (max-width: 599.95px) {
        .css-1z6qsq {
            width: 100%;
            flex-direction: column;
            -webkit-box-pack: center;
            justify-content: center;
            align-items: flex-start;
            position: relative;
            margin-top: 1rem;
        }
    }

    @media (max-width: 599.95px) {
        .css-1z6qsq .filterButton {
            position: absolute;
            top: -25px;
            right: -25px;
        }
    }

    @media (max-width: 599.95px) {
        .css-1z6qsq .filterInputs {
            width: 100%;
            flex-direction: column;
        }
    }


    .ct-series-a .ct-slice-pie {
        fill: rgb(238, 60, 60) !important;
        /* Custom color for first segment */
    }

    .ct-series-b .ct-slice-pie {
        fill: #44814e !important;
        /* Custom color for second segment */
    }

    .ct-series-c .ct-slice-pie {
        fill: rgb(255, 92, 0) !important;
        /* Custom color for third segment */
    }

    .ct-series-d .ct-slice-pie {
        fill: #d70206 !important;
        /* Custom color for fourth segment */
    }
</style>

<body>

    <?php include('includes/preloader.php'); ?>

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php
        include('includes/nav_bar.php');
        include('includes/side_bar.php');
        if ($user[0]['roleId'] == 'becedad5-8159-4543-911f-da4805e29f77') {
            include('includes/admin_dash.php');
        } else if ($permissions->hasPermissions('dashboard')) {
            if ($permissions->hasSubPermissions('view_admin_dashboard')) {
                include('includes/res_dash.php');
            } else if ($permissions->hasSubPermissions('view_teller_dashboard')) {
                include('includes/teller_dash.php');
            } else if ($permissions->hasSubPermissions('view_branch_manager_dashboard')) {
                include('branch_manager_dash.php');
            } else if ($permissions->hasSubPermissions('view_credit_officer_dashboard')) {
                include('includes/credit_dash.php');
            } else if ($permissions->hasSubPermissions('view_customer_care_dashboard')) {
                include('includes/front_dash.php');
            }
        } else {
            include('includes/other_dash.php');
        }
        ?>

        <?php include('includes/footer.php'); ?>


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
    <div class="modal fade" id="dash_filter_form">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dashboard Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <form class="custom-form" id="dash_form" data-reload-page="0" data-confirm-action="0">
                        <div class="row">

                            <div class="col-md-12 mt-4">
                                <label class="text-label form-label">Period</label>
                                <select id="period-dash" class="form-control" name="period">
                                    <option value="today"> Today </option>
                                    <option value="7_days"> Last 7 Days </option>
                                    <option value="30_days"> Last 30 Days </option>
                                    <option value="last_year"> Last Year </option>
                                    <option value="this_year"> This Year </option>
                                    <option value="since_start" selected> Since Start </option>
                                    <option value="custom"> Custom </option>

                                </select>
                            </div>

                            <div class="col-md-12 mt-4" id="start" style="display: none;">
                                <label class="text-label form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="1900-01-01" max="<?= date('Y-m-d'); ?>" id="start-date">
                            </div>
                            <div class="col-md-12 mt-4" id="end" style="display: none;">
                                <label class="text-label form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>" id="end-date">
                            </div>

                            <div class="col-md-12 mt-4">
                                <?php
                                if (!$user[0]['branchId']) {
                                    $branches = $response->getBankBranches($user[0]['bankId']);

                                    echo
                                    '
                          <div class="form-group">
                         
                              <label class="text-label form-label">Branch </label>
                              <select id="branch-dash" aria-hidden="true" name="branch">
                              <option value="all" selected> All </option>
                                  ';
                                    if ($branches !== '') {
                                        foreach ($branches as $row) {
                                            echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . '</option>';
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

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" id="branch-dash" >

                            
                            ';
                                }
                                ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-label form-label"> </label>
                                    <button type="button" class="btn form-control css-170pyj5" id="clearButton">Clear</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-label form-label"> </label>
                                    <button type="button" class="btn btn-primary form-control" data-bs-dismiss="modal" id="filterBtn" onclick="loadCharts()">Apply</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <?php
    include('includes/bottom_scripts.php');
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.js"></script>

    <script type="text/javascript">
        // Initial load
        $(document).ready(function() {
            filter_format_change();
            $.ajax({
                url: '<?php echo BACKEND_BASE_URL; ?>Bank/get_all_dashboard_values.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>&user=<?php echo $user[0]['userId']; ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // console.log(data['data'][0]['shares_daily']);
                    let clients = data['data'][0]['clients'] || 0;
                    let disb_today = data['data'][0]['disb_today'] || 0;
                    let mobile_banking = data['data'][0]['mobile_banking'] || 0;
                    let sms = data['data'][0]['sms'] || 0;
                    let birth_days = data['data'][0]['birth_days'] || 0;
                    let int_collected = data['data'][0]['int_collected'] || 0;
                    let loan_arrears = data['data'][0]['loan_arrears'] || 0;
                    let loan_arrears_amount = data['data'][0]['loan_arrears_amount'] || 0;
                    let amount_due = data['data'][0]['amount_due'] || 0;
                    let over_drafts = data['data'][0]['over_drafts'] || 0;
                    let int_waived = data['data'][0]['int_waived'] || 0;
                    let individuals = data['data'][0]['individuals'] || 0;
                    let ind_daily = data['data'][0]['ind_daily'] || 0;
                    let groups = data['data'][0]['groups'] || 0;
                    let group_daily = data['data'][0]['groups'] || 0;
                    let institutions = data['data'][0]['institutions'] || 0;
                    let inst_daily = data['data'][0]['institutions'] || 0;
                    let shares = data['data'][0]['shares'] || 0;
                    let shares_daily = data['data'][0]['shares_daily'] || 0;
                    let active_loans = data['data'][0]['active_loans'] || 0;
                    let active_credit = data['data'][0]['active_credit'] || 0;
                    let apply_credit = data['data'][0]['apply_credit'] || 0;
                    let due_credit = data['data'][0]['due_credit'] || 0;
                    let overdue_credit = data['data'][0]['overdue_credit'] || 0;
                    let closed_credit = data['data'][0]['closed_credit'] || 0;
                    let await_credit = data['data'][0]['await_credit'] || 0;
                    let declined_credit = data['data'][0]['declined_credit'] || 0;
                    let cleared_loans = data['data'][0]['cleared_loans'] || 0;
                    let due_loans = data['data'][0]['due_loans'] || 0;
                    let creditors_due = data['data'][0]['creditors_due'] || 0;
                    let debtors_due = data['data'][0]['debtors_due'] || 0;
                    let pending_loans = data['data'][0]['pending_loans'] || 0;
                    let pending_loan_amount = data['data'][0]['pending_loan_amount'] || 0;
                    let approved_loans = data['data'][0]['approved_loans'] || 0;
                    let repayments = data['data'][0]['repayments'] || 0;
                    let portifolio = data['data'][0]['portifolio'] || 0;
                    let portifolio_principal = data['data'][0]['portifolio_principal'] || 0;
                    let portifolio_interest = data['data'][0]['portifolio_interest'] || 0;
                    let deposits = data['data'][0]['deposits'] || 0;
                    let withdraws = data['data'][0]['withdraws'] || 0;

                    let agent_deposits = data['data'][0]['agent_deposits'] || 0;
                    let agent_repayments = data['data'][0]['agent_repayments'] || 0;
                    let loan_rps = data['data'][0]['loan_rps'] || 0;
                    let expenses = data['data'][0]['expenses'] || 0;
                    let incomes = data['data'][0]['incomes'] || 0;
                    let overdue_credit_amount = data['data'][0]['overdue_credit_amount'] || 0;
                    let portifolio_credit = data['data'][0]['portifolio_credit'] || 0;
                    let liabilities = data['data'][0]['liabilities'] || 0;
                    let cash_balance = data['data'][0]['cash_balance'] || 0;
                    let cash_assigned = data['data'][0]['cash_assigned'] || 0;
                    let sav_bal = data['data'][0]['sav_bal'] || 0;
                    let freezed_bal = data['data'][0]['freezed_bal'] || 0;
                    let share_amount = data['data'][0]['share_amount'] || 0;
                    let progres_label = data['data'][0]['progres_label'] || 0;
                    let saccs = data['data'][0]['clients'] || 0;
                    let p_inv = data['data'][0]['p_inv'] || 0;
                    let c_inv = data['data'][0]['inv_cap'] || 0;
                    let dec_prof = data['data'][0]['dec_prof'] || 0;
                    let inv_due = data['data'][0]['inv_due'] || 0;
                    let dormant_accs = data['data'][0]['dormant_accs'] || 0;
                    let inv_due_amount = data['data'][0]['inv_due_amount'] || 0;
                    let online_withdraws = data['data'][0]['online_withdraws'] || 0;
                    let online_deposits = data['data'][0]['online_deposits'] || 0;
                    let online_bal = data['data'][0]['online_bal'] || 0;
                    let mature_fixed = data['data'][0]['mature_fixed'] || 0;
                    let assets_daily = data['data'][0]['assets'] || 0;
                    $('#clients').html(numberWithCommas(clients));
                    $('#disb_today').html(numberWithCommas(disb_today));
                    $('#creditors_due').html(numberWithCommas(creditors_due));
                    $('#debtors_due').html(numberWithCommas(debtors_due));
                    $('#over_drafts').html(numberWithCommas(over_drafts));
                    $('#clients2').html(numberWithCommas(clients));
                    $('#loan_rps').html(numberWithCommas(loan_rps));
                    $('#mobile_banking').html(numberWithCommas(mobile_banking));
                    $('#sms').html(numberWithCommas(sms));
                    $('#birth_days').html(numberWithCommas(birth_days));
                    $('#ind_daily').html(numberWithCommas(ind_daily));
                    $('#group_daily').html(numberWithCommas(group_daily));
                    $('#inst_daily').html(numberWithCommas(inst_daily));
                    $('#int_waived').html(numberWithCommas(int_waived));
                    $('#mature_fixed').html(numberWithCommas(mature_fixed));
                    $('#loan_arrears').html(numberWithCommas(loan_arrears));
                    $('#loan_arrears_amount').html(numberWithCommas(loan_arrears_amount));
                    $('#amount_due').html(numberWithCommas(amount_due));
                    $('#int_collected').html(numberWithCommas(int_collected));
                    $('#online_withdraws').html(numberWithCommas(online_withdraws));
                    $('#portifolio_credit').html(numberWithCommas(portifolio_credit));
                    $('#portifolio_principal').html(numberWithCommas(portifolio_principal));
                    $('#portifolio_interest').html(numberWithCommas(portifolio_interest));
                    $('#online_deposits').html(numberWithCommas(online_deposits));
                    $('#online_bal').html(numberWithCommas(online_bal));
                    $('#inv_due_amount').html(numberWithCommas(inv_due_amount));
                    $('#p_inv').html(numberWithCommas(p_inv));
                    $('#c_inv').html(numberWithCommas(c_inv));
                    $('#dec_prof').html(numberWithCommas(dec_prof));
                    $('#inv_due').html(numberWithCommas(inv_due));
                    $('#dormant_accs').html(numberWithCommas(dormant_accs));
                    $('#sav_bal').html(numberWithCommas(sav_bal));
                    $('#saccs').html(numberWithCommas(saccs));
                    $('#freezed_bal').html(numberWithCommas(freezed_bal));
                    $('#share_amount').html(numberWithCommas(share_amount));
                    $('#progres_label').html(progres_label + '% Loan Collections');
                    $('#progres_dist').attr('style', 'width: ' + progres_label + '%; height:20px;');
                    // $('#progres_dist').style.width = progres_label+'%';
                    $('#individuals').html(numberWithCommas(individuals));
                    $('#groups').html(numberWithCommas(groups));
                    $('#institutions').html(numberWithCommas(institutions));
                    $('#shares').html(numberWithCommas(shares));
                    $('#shares_daily').html(shares_daily);
                    $('#active_loans').html(numberWithCommas(active_loans));
                    $('#active_credit').html(numberWithCommas(active_credit));
                    $('#closed_credit').html(numberWithCommas(closed_credit));
                    $('#declined_credit').html(numberWithCommas(declined_credit));
                    $('#loan_arrears_amount_credit').html(numberWithCommas(overdue_credit_amount));
                    $('#apply_credit').html(numberWithCommas(apply_credit));
                    $('#await_credit').html(numberWithCommas(await_credit));
                    $('#due_credit').html(numberWithCommas(due_credit));
                    $('#overdue_credit').html(numberWithCommas(overdue_credit));
                    $('#cleared_loans').html(numberWithCommas(cleared_loans));
                    $('#due_loans').html(numberWithCommas(due_loans));
                    $('#pending_loans').html(numberWithCommas(pending_loans));
                    $('#pending_loan_amount').html(numberWithCommas(pending_loan_amount));
                    $('#approved_loans').html(numberWithCommas(approved_loans));
                    $('#repayments').html(numberWithCommas(repayments));
                    $('#portifolio').html(numberWithCommas(portifolio));
                    $('#deposits').html(numberWithCommas(deposits));
                    $('#withdraws').html(numberWithCommas(withdraws));
                    $('#agent_deposits').html(numberWithCommas(agent_deposits));
                    $('#agent_repayments').html(numberWithCommas(agent_repayments));
                    $('#lps_today').html(numberWithCommas(loan_rps));
                    $('#expenses_daily').html(numberWithCommas(expenses));
                    $('#incomes_daily').html(numberWithCommas(incomes));
                    $('#liabilities_daily').html(numberWithCommas(liabilities));
                    $('#cash_balance').html(numberWithCommas(cash_balance));
                    $('#cash_assigned').html(numberWithCommas(cash_assigned));
                    $('#assets_daily').html(numberWithCommas(assets_daily));
                }
            });

            $('#filterBtn').trigger('click');
        });

        function filter_format_change() {
            var filter_method = $('#period-dash');
            var start_input = $('#start');
            var end_input = $('#end');

            if (filter_method.val() == 'custom') {
                start_input.show();
                end_input.show();
            } else {
                start_input.hide();
                end_input.hide();
            }
        }

        document.getElementById('clearButton').addEventListener('click', function() {
            // Get the form element
            const form = document.getElementById('dash_form');

            // Clear all input fields
            form.querySelectorAll('input').forEach(input => {
                if (input.type === 'date' || input.type === 'text' || input.type === 'number') {
                    input.value = ''; // Clear the value
                }
            });

            // Reset all select dropdowns to their default selected option
            form.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0; // Reset to default selected
            });
        });

        function loadCharts() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            const branch = document.getElementById('branch-dash').value;
            const period = document.getElementById('period-dash').value;

            fetch(`<?= BACKEND_BASE_URL ?>Bank/get_all_dashboard_charts.php?start_date=${startDate}&end_date=${endDate}&branch=${branch}&period=${period}&bank=<?= @$user[0]['bankId'] ?>&user=<?= @$user[0]['userId'] ?>`)
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Debugging JSON structure
                    createMorrisDonutChart('gender-chart', data.gender, 'gender');
                    createMorrisDonutChart('age-charts', data.age, 'age_group');
                    createMorisBarChart('occupation-chart', data.occupation, 'occupation');
                    createMorisLineChart('education-chart', data.education, 'education_level');
                    createMorisBarChartBranch('membership_branch-chart', data.membership_branch, 'membership_branch');
                    createMorisBarChartProduct('membership_product-chart', data.membership_product, 'membership_product');
                });
        }

        function createMorisLineChart(containerId, data, labelKey) {
            if (!Array.isArray(data)) {
                console.error(`Invalid data passed to createMorisLineChart:`, data);
                return; // Exit if data is not valid
            }

            document.getElementById(containerId).innerHTML = '';

            // Render the chart
            new Morris.Area({
                element: containerId,
                data: data,
                xkey: 'month',
                ykeys: ['incomes', 'expenses', 'net_income'],
                labels: ['Incomes', 'Expenses', 'Net Income'],
                pointSize: 3,
                fillOpacity: 0,
                pointStrokeColors: ['#EE3C3C', '#ffaa2b', '#44814e'],
                behaveLikeLine: true,
                // gridLineColor: 'transparent',
                lineWidth: 3,
                hideHover: 'auto',
                lineColors: ['rgb(238, 60, 60)', 'rgb(0, 171, 197)', '#44814e'],
                resize: true
            });
        }


        function createMorrisDonutChart(containerId, data, labelKey) {
            // Validate and process the data
            if (!Array.isArray(data)) { 
                console.error(`Invalid data passed to createMorrisDonutChart:`, data);
                return; // Exit if data is not valid
            }

            document.getElementById(containerId).innerHTML = '';

            const chartData = data.map(item => ({
                label: item[labelKey], // Use the specified label key for labels
                value: parseInt(item.count, 10) || 0 // Parse count as integer and default to 0 if invalid
            }));

            // Render the Morris Donut chart
            new Morris.Donut({
                element: containerId, // Target container ID
                data: chartData, // Processed chart data
                resize: true, // Allow resizing for responsive design
                redraw: true, // Allow redrawing
                colors: ['#44814e', 'rgb(255, 92, 0)', '#ffaa2b', '#3432a8'], // Define colors
            });
        }

        function createMorisBarChartProduct(containerId, data, labelKey) {
            if (!Array.isArray(data)) {
                console.error(`Invalid data passed to createMorisBarChartProduct:`, data);
                return; // Exit if data is not valid
            }

            document.getElementById(containerId).innerHTML = '';

            // Transform data into Morris-compatible format
            const chartData = data.map(item => ({
                branch_name: item.branch_name,
                member_count: parseInt(item.member_count, 10) || 0 // Default to 0 if count is missing or invalid
            }));

            // Render the chart
            new Morris.Bar({
                element: containerId,
                data: chartData,
                xkey: 'branch_name',
                ykeys: ['member_count'],
                labels: ['Count'],
                barColors: ['#44814e'],
                hideHover: 'auto',
                // gridLineColor: 'transparent',
                resize: true,
                barSizeRatio: 0.5
            });
        }

        function createMorisBarChartBranch(containerId, data, labelKey) {
            if (!Array.isArray(data)) {
                console.error(`Invalid data passed to createMorisBarChartBranch:`, data);
                return; // Exit if data is not valid
            }

            document.getElementById(containerId).innerHTML = '';
            // Transform data into Morris-compatible format
            const chartData = data.map(item => ({
                branch_name: item.branch_name,
                total_members: parseInt(item.total_members, 10) || 0 // Default to 0 if count is missing or invalid
            }));

            // Render the chart
            new Morris.Bar({
                element: containerId,
                data: chartData,
                xkey: 'branch_name',
                ykeys: ['total_members'],
                labels: ['Count'],
                barColors: ['#44814e'],
                hideHover: 'auto',
                // gridLineColor: 'transparent',
                resize: true,
                barSizeRatio: 0.5
            });
        }


        function createMorisBarChart(containerId, data, labelKey) {
            if (!Array.isArray(data)) {
                console.error(`Invalid data passed to createMorisBarChart:`, data);
                return; // Exit if data is not valid
            }
            document.getElementById(containerId).innerHTML = '';

            // Transform data into Morris-compatible format
            const chartData = data.map(item => ({
                profession: item.profession,
                count: parseInt(item.count, 10) || 0 // Default to 0 if count is missing or invalid
            }));

            // Render the chart
            new Morris.Bar({
                element: containerId,
                data: chartData,
                xkey: 'profession',
                ykeys: ['count'],
                labels: ['Count'],
                barColors: ['#44814e'],
                hideHover: 'auto',
                // gridLineColor: 'transparent',
                resize: true,
                barSizeRatio: 0.5
            });
        }

        function createPieChart(containerId, data, labelKey) {
            const labels = data.map(item => item[labelKey]);
            const series = data.map(item => parseInt(item.count));

            new Chartist.Pie(`#${containerId}`, {
                labels: labels,
                series: series
            });
        }

        function createBarChart(containerId, data, labelKey) {

            const labels = data.map(item => item.occupation);
            const series = data.map(item => parseInt(item.count));
            // Destroy previous chart instance if it exists
            if (window.occupationChartInstance) {
                window.occupationChartInstance.destroy();
            }

            const ctx = document.getElementById(containerId).getContext('2d');
            window.occupationChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Members',
                        data: series,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Occupation'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Members'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        }


        function numberWithCommas(x) {
            //return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return x;
        }
    </script>

</body>

</html>