<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_loans_review')) {
    return $permissions->isNotPermitted(true);
}

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');

$response = new Response();

if (isset($_POST['sndate'])) {


    $res = $response->editScheduleDate($_POST);
    if ($res) {

        setSessionMessage(true, 'Loan Schedule Updated Successfully!');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
    }
    header('location:loan_details_page.php?id=' . $_POST['id']);
    exit();
}

if (isset($_POST['change_account'])) {


    $res = $response->editLoanSavingAcc($_POST);
    if ($res) {

        setSessionMessage(true, 'Account Updated Successfully!');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
    }
    header('location:loan_details_page.php?id=' . $_POST['id']);
    exit();
}

if (isset($_POST['approve'])) {

    $amount = str_replace(",", "", $_POST['amount']);

    $res = $response->approveLoan($_POST['id'], $_POST['rate'], $_POST['notes'], $_POST['duration'], $amount, $user[0]['userId'], $_POST['freq'], $_POST['approve_date']);
    if ($res) {

        setSessionMessage(true, 'Loan Approved Successfully!');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
    }
    header('location:loan_details_page.php?id=' . $_POST['id']);
    exit();
}

if (isset($_POST['decline'])) {
    $res = $response->declineLoan($_POST['id'], $_POST['reason'], $user[0]['userId']);
    if ($res) {
        $_SESSION['success_message'] = 'Loan Declined Successfully!';
        header('location:loan_applications.php');
    } else {
        $_SESSION['error_message'] = 'Something went wrong! Try again';
        header('location:loan_details_page.php?id=' . $_POST['id']);
    }
    exit();
}

if (isset($_POST['disburse'])) {

    $famount = str_replace(",", "", $_POST['famount']);

    $auto_pay = isset($_POST['auto_pay']) ? 1 : 0;

    $res = $response->disburseLoan($_POST['id'], $famount, $_POST['ddate'], $_POST['ddate'], $_POST['cl'], $_POST['mode'], $_POST['amount'], $_POST['auth'], $_POST['lpid'], $auto_pay, $_POST['bank_acc'], $_POST['cash_acc'], $_POST['cheque_no']);
    if ($res) {
        $_SESSION['success_message'] = 'Loan No. ' . $_POST['id'] . ' Disbursed Successfully';
    } else {
        $_SESSION['error_message'] = 'Something went wrong! Try again';
    }

    header('location:loan_details_page.php?id=' . $_POST['id']);
    exit();
}


$details = $response->getLoanDetails($_GET['id']);
$loan_details = @$details[0]['loan'];
$loan_product = @$details[0]['product'];

$is_fixed_amount_penalty_loan = false;
$is_penalty_rate_loan = false;

// if (@$loan_product['penalty']) {
if ($loan_details['penalty_fixed_amount'] > 0) {
    $is_fixed_amount_penalty_loan = true;
} else if ($loan_details['penalty_interest_rate'] > 0) {
    $is_penalty_rate_loan = true;
}
// }


$string = $loan_details['fees_to_charge'] ?? '';
$array = explode(', ', $string);
$lps_text = $response->getLoanFees($loan_details['loanproductid']);


$cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);


$title = 'Loan Details';
require_once('includes/head_tag.php');
?>

<style>
    .progress {
        height: 23px;
        margin-bottom: 20px;
    }

    .progress-bar-striped {
        background-image: linear-gradient(-45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
    }

    .progress-bar {
        font-weight: bold;
        font-size: 14px;
        animation-direction: reverse;
    }
</style>

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
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">

                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Loan Details Page
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item"><a href="#client" data-bs-toggle="tab" class="nav-link  <?= !in_array(@$_REQUEST['current_tab'], ['client']) ? 'active' : '' ?> ">Client Details</a>
                                        </li>

                                        <!-- <li class="nav-item"><a href="#fees" data-bs-toggle="tab" class="nav-link active">Fees</a>
                                        </li> -->
                                        <li class="nav-item"><a href="#loan" data-bs-toggle="tab" class="nav-link">Loan Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#guarantors" data-bs-toggle="tab" class="nav-link <?= @$_REQUEST['current_tab'] == 'guarantors' ? 'active' : '' ?>">
                                                Guarantors
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#income_source" data-bs-toggle="tab" class="nav-link <?= @$_REQUEST['current_tab'] == 'income_source' ? 'active' : '' ?>">
                                                Income Sources
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#collaterals" data-bs-toggle="tab" class="nav-link <?= @$_REQUEST['current_tab'] == 'collaterals' ? 'active' : '' ?>">
                                                Collaterals
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#loan_schedule" data-bs-toggle="tab" class="nav-link <?= @$_REQUEST['current_tab'] == 'loan_schedule' ? 'active' : '' ?>">
                                                Loan Schedule
                                            </a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">
                                        <div id="client" class="tab-pane fade <?= !in_array(@$_REQUEST['current_tab'], ['client']) ? 'show active' : '' ?>" role="tabpanel">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">Client's Information</h4>
                                                    <?php
                                                    if ($details[0]['client']['freezed_amount'] > 0) {
                                                        echo '
                                                        
                                                         <a href="unfreeze_account.php?id=' . $details[0]['client']['userId'] . '&amount=' . $details[0]['client']['freezed_amount'] . '" class="btn btn-primary light btn-xs mb-1 confirm un_freeze_acc">Freezed Balance: ' . number_format($details[0]['client']['freezed_amount']) . '</a>
                                                        ';
                                                    } else {
                                                        echo '
                                                        <a href="freeze_account.php?t=' . encrypt_data($details[0]['client']['userId']) . '" class="btn btn-primary light btn-xs mb-1">Freeze Savings</a>
                                                        ';
                                                    }
                                                    ?>
                                                    <a href="#" class="btn btn-primary light btn-xs mb-1">View Credit
                                                        History</a>


                                                </div>
                                                <div class="card-body">

                                                    <div class="row">
                                                        <div class="col-lg-6 mb-2">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="text-center">
                                                                        <div class="met-profile-main-pic">
                                                                            <img src="<?= is_null($details[0]['client']['profilePhoto']) ? 'icons/favicon.png' : $details[0]['client']['profilePhoto'] ?>" onerror="this.onerror=null; this.src='icons/favicon.png'" alt="" height="100" width="100" class="rounded-circle">
                                                                        </div>

                                                                        <div class="">
                                                                            <h5 class="mb-0"><?= $details[0]['client']['firstName'] . ' ' . $details[0]['client']['lastName'] ?></h5>
                                                                            <small class="text-muted">A/C No: <a aria-expanded='false' data-bs-toggle='modal' data-bs-target='.lgacno1'><?= $details[0]['client']['membership_no']; ?></a>
                                                                                | CLIENT TYPE : <?= ($details[0]['client']['membership_no'] > 0 ? 'Member' : 'Non-Member'); ?></small>
                                                                        </div>
                                                                        <br />
                                                                        <a href="client_profile_page.php?id=<?php echo $details[0]['client']['userId']; ?>" class="btn btn-primary light btn-xs mb-1">View Client's Profile
                                                                            Page</a>
                                                                    </div>
                                                                </div>
                                                                <!--end card-body-->
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 mb-2">
                                                            <div class="card">
                                                                <div class="card-body btc-price">

                                                                    <h4 class="mt-0 header-title">Client Info</h4>
                                                                    <p class="text-muted mb-3">Summary</p>

                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Occupation</span>
                                                                            <h6 class="mt-0"><?= @$details[0]['client']['profession'] ?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Physical Address </span>
                                                                            <h6 class="mt-0"><?= @$details[0]['client']['addressLine1'] . ' , ' . @$details[0]['client']['addressLine2'] . ' , ' . @$details[0]['client']['country'] ?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Email: </span>
                                                                            <h6 class="mt-0"><?= @$details[0]['client']['email']; ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Contacts: </span>
                                                                            <h6 class="mt-0"><?= @$details[0]['client']['primaryCellPhone'] . ' / ' . @$details[0]['client']['secondaryCellPhone']; ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Business Info: </span>
                                                                            <h6 class="mt-0"><?= @$details[0]['business'] == '' ? '' : @$details[0]['business']['name']; ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">A/C Balance</span>
                                                                            <h6 class="mt-0"><?= number_format(@$details[0]['client']['acc_balance'] ?? 0) ?>
                                                                            </h6>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div id="guarantors" class="tab-pane fade">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">Guarantors' Information</h4>
                                                    <?php if ($permissions->hasSubPermissions('edit_guarantor')) : ?>
                                                        <a href="add_guarantor.php?id=<?php echo $details[0]['loan']['loan_no']; ?>&cl=<?php echo $details[0]['loan']['account_id']; ?>" class="btn btn-primary light btn-xs mb-1">Add
                                                            Guarantor</a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="example3" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:80px;"><strong>#</strong></th>
                                                                    <th><strong>Guarantors' A/C</strong></th>
                                                                    <th><strong>Names</strong></th>
                                                                    <th><strong>Status</strong></th>
                                                                    <th><strong>Attachment</strong></th>
                                                                    <th><strong>Date Created</strong></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $gats = $response->getLoanGuarantors($details[0]['loan']['loan_no']);

                                                                if ($gats != '') {

                                                                    foreach ($gats as $row) {
                                                                        echo '
                                                                        <tr>
                                                                        <td><strong>' . @$row['gid'] . '</strong></td>
                                                                        <td>' . @$row['membership_no'] . '</td>
                                                                        <td>' . @$row['name'] . '</td>
                                                                    
                                                                        <td><span
                                                                                class="badge light ' . ($row['_gstatus'] == 1 ? 'badge-success' : 'badge-danger') . '">' . ($row['_gstatus'] == 1 ? 'Active' : 'Released') . '</span>
                                                                        </td>
                                                                        
                                                                        <td><a href="https://eaoug.org/' . @$row['attachment'] . '" class="text-primary">View</a></td>
                                                                       
                                                                        <td>' . normal_date($row['_date_created']) . '</td>
                                                                        <td>
                                                                            <div class="dropdown">
                                                                                <button type="button"
                                                                                    class="btn btn-success light sharp"
                                                                                    data-bs-toggle="dropdown">
                                                                                    <svg width="20px" height="20px"
                                                                                        viewBox="0 0 24 24" version="1.1">
                                                                                        <g stroke="none" stroke-width="1"
                                                                                            fill="none" fill-rule="evenodd">
                                                                                            <rect x="0" y="0" width="24"
                                                                                                height="24"></rect>
                                                                                            <circle fill="#000000" cx="5"
                                                                                                cy="12" r="2"></circle>
                                                                                            <circle fill="#000000" cx="12"
                                                                                                cy="12" r="2"></circle>
                                                                                            <circle fill="#000000" cx="19"
                                                                                                cy="12" r="2"></circle>
                                                                                        </g>
                                                                                    </svg>
                                                                                </button>
                                                                                <div class="dropdown-menu">
                                                                                    <a class="dropdown-item"
                                                                                        href="edit_guarantor.php?id=' . @$row['gid'] . '">Edit</a>
                                                                                    <a class="dropdown-item"
                                                                                        href="delete_guarantor.php?id=' . @$row['gid'] . '&lno=' . $details[0]['loan']['loan_no'] . '">Trash</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                        
                                                                        ';
                                                                    }
                                                                }
                                                                ?>


                                                            </tbody>
                                                        </table>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                        <div id="loan" class="tab-pane fade <?= @$_REQUEST['current_tab'] == 'loan' ? 'show active' : '' ?>">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">Loan Details</h4>
                                                    <div>
                                                        <?php
                                                        if ($details[0]['loan']['status'] == 0) { ?>

                                                            <a href="export_report.php?exportFile=export_loan_committee_report&useFile=1&id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1 confirm committee-loan" target="_blank">Committee Report</a>
                                                            <a href="export_report.php?exportFile=export_loan_application_form&useFile=1&id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1" target="_blank">Loan Application Form</a>


                                                            <?php if ($menu_permission->hasSubPermissions(['approve_loan_applications'])) { ?>
                                                                <a type="button" class="btn btn-primary light btn-xs mb-1 confirm approve-loan">Approve</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['decline_loan_applications'])) { ?>
                                                                <a type="button" class="btn btn-danger light btn-xs mb-1" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lgD">Decline</a>
                                                            <?php } ?>
                                                            <?php if ($menu_permission->hasSubPermissions(['edit_loan'])) { ?>
                                                                <a type="button" href="delete_loan.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-danger light btn-xs mb-1 confirm delete-record">Trash</a>
                                                            <?php } ?>

                                                            <?php if ($permissions->hasSubPermissions(['edit_loan'])) { ?>
                                                                <a href="edit_loan.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-danger light btn-xs mb-1">Edit Loan</a>
                                                            <?php } ?>

                                                        <?php } else if ($details[0]['loan']['status'] == 1 && $permissions->hasSubPermissions(['disburse_loan'])) { ?>
                                                            <a href="export_report.php?exportFile=export_loan_committee_report&useFile=1&id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1 confirm committee-loan" target="_blank">Committee Report</a>

                                                            <?php if ($menu_permission->hasSubPermissions(['disburse_loan'])) { ?>
                                                                <a type="button" class="btn btn-primary light btn-xs mb-1 confirm disburse-loan">Disburse</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['decline_loan_applications'])) { ?>
                                                                <a type="button" class="btn btn-danger light btn-xs mb-1" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lgD">Decline</a>
                                                            <?php } ?>

                                                        <?php
                                                        } else if ($details[0]['loan']['status'] == 2) { ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['create_loan_repay'])) { ?>
                                                                <a class="btn btn-primary light btn-xs mb-1" href="collect_payment.php?id=<?= $details[0]['loan']['loan_no'] ?>">Make Payment</a>
                                                            <?php } ?>
                                                            <?php if ($menu_permission->hasSubPermissions(['undo_disbursement'])) { ?>
                                                                <a href="undo_disbursement.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-warning light btn-xs mb-1 confirm undo_disburse">Undo-Disbursement</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['reschedule_loans'])) { ?>
                                                                <a href="reschedule_loan.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-danger light btn-xs mb-1 confirm reschedule_loan">Reschedule</a>
                                                            <?php } ?>


                                                            <?php if ($menu_permission->hasSubPermissions(['create_loan_topup'])) { ?>
                                                                <a href="top_up_loan.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-danger light btn-xs mb-1">Make Top-Up</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['waive_interest'])) { ?>
                                                                <a href="waive_interest.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-danger light btn-xs mb-1">Waive Interest</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['waive_penalty'])) { ?>
                                                                <a href="waive_penalty.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">Waive Penalty</a>
                                                            <?php } ?>


                                                            <a href="loan_statement.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">View Loan Statement</a>

                                                        <?php } else if ($details[0]['loan']['status'] == 3) { ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['create_loan_repay'])) { ?>
                                                                <a class="btn btn-primary light btn-xs mb-1" href="collect_payment.php?id=<?= $details[0]['loan']['loan_no'] ?>">Make Payment</a>
                                                                <a href="undo_disbursement.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-warning light btn-xs mb-1 confirm undo_disburse">Undo-Disbursement</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['reschedule_loans'])) { ?>
                                                                <a href="reschedule_loan.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1 confirm reschedule_loan">Reschedule</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['create_loan_topup'])) { ?>
                                                                <a href="top_up_loan.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">Make Top-Up</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['waive_interest'])) { ?>
                                                                <a href="waive_interest.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">Waive Interest</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['waive_penalty'])) { ?>
                                                                <a href="waive_penalty.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">Waive Penalty</a>
                                                            <?php } ?>


                                                            <a href="loan_statement.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">View Loan Statement</a>

                                                        <?php } else if ($details[0]['loan']['status'] == 4) { ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['create_loan_repay'])) { ?>
                                                                <a class="btn btn-primary light btn-xs mb-1" href="collect_payment.php?id=<?= $details[0]['loan']['loan_no'] ?>">Make Payment</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['reschedule_loans'])) { ?>
                                                                <a href="reschedule_loan.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-danger light btn-xs mb-1 confirm reschedule_loan">Reschedule</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['create_loan_topup'])) { ?>
                                                                <a href="top_up_loan.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">Make Top-Up</a>
                                                            <?php } ?>


                                                            <?php if ($menu_permission->hasSubPermissions(['waive_interest'])) { ?>
                                                                <a class="btn btn-primary light btn-xs mb-1" href="waive_interest.php?id=<?= $details[0]['loan']['loan_no'] ?>">Waive Interest</a>
                                                            <?php } ?>

                                                            <?php if ($menu_permission->hasSubPermissions(['waive_penalty'])) { ?>
                                                                <a href="waive_penalty.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">Waive Penalty</a>
                                                            <?php } ?>

                                                            <a href="loan_statement.php?id=<?= $details[0]['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">View Loan Statement</a>


                                                        <?php } else if ($details[0]['loan']['status'] == 5) {
                                                            echo '

                                                             <a href="undo_closure.php?id=' . $details[0]['loan']['loan_no'] . '" class="btn btn-danger light btn-xs mb-1 confirm reactivate_loan">Loan Reactivation</a>

                                               <a href="loan_statement.php?id=' . $details[0]['loan']['loan_no'] . '" class="btn btn-primary light btn-xs mb-1">View Loan Statement</a>

                                                    
                                                    ';
                                                        } else if ($details[0]['loan']['status'] == 6) {
                                                            echo '
                                                                <a type="button" class="btn btn-primary light btn-xs mb-1" aria-expanded="false"
                                                                data-bs-toggle="modal" data-bs-target=".bd-example-modal-lgR">View Denial Reason</a>
                
                                                                    
                                                                    ';
                                                        }


                                                        ?>


                                                    </div>
                                                </div>
                                                <div class="card-body">

                                                    <div class="row">
                                                        <div class="col-lg-12 mb-2">
                                                            <div class="card">
                                                                <div class="card-body btc-price">

                                                                    <?php
                                                                    if ($details[0]['loan']['total_loan_amount'] > 0) {
                                                                        $percentage_completed = round(($details[0]['loan']['amount_paid'] / $details[0]['loan']['total_loan_amount']) * 100, 2);
                                                                    } else {
                                                                        $percentage_completed = round(($details[0]['loan']['amount_paid'] / 1) * 100, 2);
                                                                    }


                                                                    $percentage_completed = $percentage_completed > 100 || $details[0]['loan']['status'] == 5 ? 100 : $percentage_completed;
                                                                    ?>
                                                                    Percentage Completed


                                                                    <div class="progress finbyz-fadeinup" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="<?= $percentage_completed ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percentage_completed ?>%"> <?= $percentage_completed ?>%
                                                                        </div>
                                                                    </div>

                                                                    <h4 class="mt-0 header-title">
                                                                        Loan Product Information
                                                                        <?php if ($menu_permission->hasSubPermissions(['create_loan_repay'])) { ?>
                                                                            <button type="button" class="btn btn-primary light btn-xs" data-bs-toggle="modal" data-bs-target="#update_loan_product_details">Update Loan Product Details</button>
                                                                            <button type="button" class="btn btn-primary light btn-xs" data-bs-toggle="modal" data-bs-target="#update_loan_product_fees">Update Loan Fees Attached</button>

                                                                            <button type="button" class="btn btn-primary light btn-xs" data-bs-toggle="modal" data-bs-target="#update_forced_savings">Set Forced Savings</button>
                                                                            <button type="button" class="btn btn-primary light btn-xs" data-bs-toggle="modal" data-bs-target="#enconomic_sector">Enconomic Sector</button>
                                                                        <?php } ?>
                                                                    </h4>
                                                                    <p class="text-muted mb-3">Summary</p>

                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Name: </span>
                                                                            <h6 class="mt-0"><?php echo $details[0]['product']['type_name']; ?>
                                                                            </h6>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Interest Rate </span>
                                                                            <h6 class="mt-0"><?php echo $details[0]['loan']['monthly_interest_rate'] . '% PER ANNUM'; ?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Frequency: </span>
                                                                            <h6 class="mt-0"> <?php echo $details[0]['product']['frequency']; ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Interest Method: </span>
                                                                            <h6 class="mt-0"><?php echo $details[0]['product']['interestmethod']; ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Fees/Charges: </span>
                                                                            <?php
                                                                            // foreach ($lps_text as $row) :
                                                                            if ($lps_text) :
                                                                                if (str_contains($string, $lps_text[0]['fee_id'])) :
                                                                            ?>
                                                                                    <h6 class="mt-0">
                                                                                        <?= $lps_text[0]['name'] . ' - ' . $lps_text[0]['paymentType'] ?> <a class="text-primary" style="  text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#update_loan_product_fees">...view more</a>
                                                                                    </h6>
                                                                            <?php
                                                                                endif;
                                                                            endif;
                                                                            // endforeach;
                                                                            ?>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Penalty: </span>
                                                                            <h6 class="mt-0"><?php echo $details[0]['loan']['penalty_interest_rate'] > 0 ? $details[0]['loan']['penalty_interest_rate'] . ' %' : number_format($details[0]['loan']['penalty_fixed_amount'] ?? 0) . ' - FIXED AMOUNT'; ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Grace Period: </span>
                                                                            <h6 class="mt-0"><?php echo $details[0]['loan']['num_grace_periods'] ?? 0; ?> Days</h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Grace Period Applies When: </span>
                                                                            <h6 class="mt-0"><?php
                                                                                                if ($details[0]['loan']['grace_period_type'] == 0) {
                                                                                                    echo 'At the Begining of the Loan Term';
                                                                                                } else if ($details[0]['loan']['grace_period_type'] == 1) {
                                                                                                    echo 'During the Loan Term';
                                                                                                } else if ($details[0]['loan']['grace_period_type'] == 2) {
                                                                                                    echo 'At the End of the Loan Term';
                                                                                                } else {
                                                                                                    echo '---';
                                                                                                }
                                                                                                ?> </h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Grace Period Type: </span>
                                                                            <h6 class="mt-0"><?php
                                                                                                if ($details[0]['loan']['penalty_grace_type'] == 'pay_none') {
                                                                                                    echo 'Pays Nothing';
                                                                                                } else if ($details[0]['loan']['penalty_grace_type'] == 'pay_p') {
                                                                                                    echo 'Pays Principal Only';
                                                                                                } else if ($details[0]['loan']['penalty_grace_type'] == 'pay_i') {
                                                                                                    echo 'Pays Interest Only';
                                                                                                } else {
                                                                                                    echo '---';
                                                                                                }
                                                                                                ?> </h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Max. Penalty Period: </span>
                                                                            <h6 class="mt-0"><?php echo $details[0]['loan']['penalty_max_days'] ?? 0; ?> Days</h6>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="col-lg-12 mb-2">
                                                            <div class="card">
                                                                <div class="card-body btc-price">
                                                                    <?php
                                                                    if ($details[0]['loan']['status'] == 0) {
                                                                        $bt = ' <span class="badge badge-rounded badge-warning">PENDING REVIEW</span>';
                                                                    } else if ($details[0]['loan']['status'] == 1) {
                                                                        $bt = ' <span class="badge badge-rounded badge-danger">PENDING DISBURSEMENT</span>';
                                                                    } else if ($details[0]['loan']['status'] == 2) {
                                                                        $bt = ' <span class="badge badge-rounded badge-primary">ON TIME</span>';
                                                                    } else if ($details[0]['loan']['status'] == 3) {
                                                                        $bt = ' <span class="badge badge-rounded badge-primary">DUE</span>';
                                                                    } else if ($details[0]['loan']['status'] == 4) {
                                                                        $bt = ' <span class="badge badge-rounded badge-primary">OVERDUE</span>';
                                                                    } else if ($details[0]['loan']['status'] == 5) {
                                                                        $bt = ' <span class="badge badge-rounded badge-primary">COMPLETE</span>';
                                                                    } else if ($details[0]['loan']['status'] == 6) {
                                                                        $bt = ' <span class="badge badge-rounded badge-danger">DENIED</span>';
                                                                    }

                                                                    ?>
                                                                    <h4 class="mt-0 header-title">Loan Details </h4>

                                                                    <p class="text-muted mb-3">Status: <?= $bt ?> |
                                                                        Automatic Payments:
                                                                        <?= @$details[0]['loan']['auto_pay']  > 0 ? '<span class="badge badge-rounded badge-primary">ON</span>' : '<span class="badge badge-rounded badge-danger">OFF</span>'; ?>
                                                                    </p>

                                                                    <div class="row">

                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Loan No. </span>
                                                                            <h6 class="mt-0"><?php echo $details[0]['loan']['loan_no']; ?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted"><?php
                                                                                                        if ($details[0]['loan']['status'] == 0) {
                                                                                                            $btt = 'Requested Amount: ';
                                                                                                        } else if ($details[0]['loan']['status'] == 1) {
                                                                                                            $btt = 'Approved Amount: ';
                                                                                                        } else {
                                                                                                            $btt = 'Disbursed Amount: ';
                                                                                                        }

                                                                                                        echo $btt;

                                                                                                        ?> </span>
                                                                            <h6 class="mt-0"> <?php echo number_format($details[0]['loan']['principal']); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Outstanding Balance: </span>
                                                                            <h6 class="mt-0"><?php echo number_format($details[0]['loan']['current_balance']); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Penalty Balance: </span>
                                                                            <h6 class="mt-0"> <?php echo number_format($details[0]['loan']['penalty_balance'] ?? 0); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Interest Balance: </span>
                                                                            <h6 class="mt-0"> <?php echo number_format($details[0]['loan']['interest_balance'] ?? 0); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Principal Balance: </span>
                                                                            <h6 class="mt-0"> <?php echo number_format($details[0]['loan']['principal_balance'] ?? 0); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Total Amount Due: </span>
                                                                            <h6 class="mt-0 text-danger"> <?php echo number_format($details[0]['loan']['principal_due'] + $details[0]['loan']['interest_due']); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Principal Due: </span>
                                                                            <h6 class="mt-0 text-danger"> <?php echo number_format($details[0]['loan']['principal_due']); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Interest Due: </span>
                                                                            <h6 class="mt-0 text-danger"> <?php echo number_format($details[0]['loan']['interest_due'] ?? 0); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Interest Waived: </span>
                                                                            <h6 class="mt-0"> <a href="interest_waiver_stmt.php?id=<?php echo @$details[0]['loan']['loan_no']; ?>" class="text-primary"><?php echo number_format($details[0]['loan']['int_waivered'] ?? 0); ?></a></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Penalty Waived: </span>
                                                                            <h6 class="mt-0"><a href="penalty_waiver_stmt.php?id=<?php echo @$details[0]['loan']['loan_no']; ?>" class="text-primary"> <?php echo number_format($details[0]['loan']['penalty_waivered'] ?? 0); ?></a></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Total Waived: </span>
                                                                            <h6 class="mt-0"> <?php echo number_format($details[0]['loan']['penalty_waivered'] + $details[0]['loan']['int_waivered'] ?? 0); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Next Due Date: </span>
                                                                            <h6 class="mt-0"> <?php echo normal_date($details[0]['loan']['date_of_next_pay']); ?></h6>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Amount in Arrears: </span>
                                                                            <h6 class="mt-0 text-danger"> <?php echo number_format($details[0]['loan']['principal_arrears'] + $details[0]['loan']['interest_arrears']); ?></h6>
                                                                        </div>

                                                                        <?php if ($details[0]['loan']['arrears_collection_date']) { ?>
                                                                            <div class="col-lg-4">
                                                                                <span class="text-muted">Days in Arrears: </span>
                                                                                <h6 class="mt-0 text-danger"> <?= days_in_arrears(@$details[0]['loan']); ?></h6>
                                                                            </div>
                                                                        <?php } ?>

                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted"><?php
                                                                                                        if ($details[0]['loan']['status'] == 0) {
                                                                                                            $btt = 'Requested Duration';
                                                                                                        } else {
                                                                                                            $btt = 'Approved Duration';
                                                                                                        }

                                                                                                        echo $btt;

                                                                                                        ?>: / Frequency</span>
                                                                            <h6 class="mt-0"> <?php
                                                                                                if ($details[0]['loan']['repay_cycle_id'] == 1) {
                                                                                                    $ftype = 'DAYS';
                                                                                                } else if ($details[0]['loan']['repay_cycle_id']  == 2) {
                                                                                                    $ftype = 'WEEKS';
                                                                                                } else if ($details[0]['loan']['repay_cycle_id']  == 3) {
                                                                                                    $ftype = 'MONTHS';
                                                                                                } else if ($details[0]['loan']['repay_cycle_id']  == 4) {
                                                                                                    $ftype = 'DAYS';
                                                                                                } else if ($details[0]['loan']['repay_cycle_id']  == 5) {
                                                                                                    $ftype = 'YEARS';
                                                                                                } else if ($details[0]['loan']['repay_cycle_id']  == 6) {
                                                                                                    $ftype = 'MONTHS';
                                                                                                }

                                                                                                echo number_format(@$details[0]['loan']['approved_loan_duration']) . ' ' . $ftype; ?> / <?php

                                                                                                                                                                                        if ($details[0]['loan']['repay_cycle_id'] == 1) {
                                                                                                                                                                                            echo ' - DAILY';
                                                                                                                                                                                        } else if ($details[0]['loan']['repay_cycle_id'] == 2) {
                                                                                                                                                                                            echo ' - WEEKLY';
                                                                                                                                                                                        } else if ($details[0]['loan']['repay_cycle_id'] == 3) {
                                                                                                                                                                                            echo ' - MONTHLY';
                                                                                                                                                                                        } else if ($details[0]['loan']['repay_cycle_id'] == 4) {
                                                                                                                                                                                            echo  ' - DAILY';
                                                                                                                                                                                        } else if ($details[0]['loan']['repay_cycle_id'] == 5) {
                                                                                                                                                                                            echo ' - ANNUALLY';
                                                                                                                                                                                        } else if ($details[0]['loan']['repay_cycle_id'] == 6) {
                                                                                                                                                                                            echo ' - QUARTERLY';
                                                                                                                                                                                        }
                                                                                                                                                                                        ?></h6>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted"><?= ($details[0]['loan']['status'] == 0 || $details[0]['loan']['status'] == 6) ? 'Requested Disbursement Date:' : 'Disbursement Date:' ?></span>
                                                                            <h6 class="mt-0"> <?php echo normal_date(@$details[0]['loan']['requesteddisbursementdate']); ?></h6>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Mode of Disbursement: </span>
                                                                            <h6 class="mt-0"> <?php echo $details[0]['loan']['mode_of_disbursement'] ?? ''; ?></h6>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Credit Officer: </span>
                                                                            <h6 class="mt-0"> <?php echo @$details[0]['staff']['firstName'] . ' ' . @$details[0]['staff']['lastName'] . ' - ' . @$details[0]['staff']['positionTitle']; ?> &nbsp; &nbsp; <a class="text-primary" href="edit_credit_officer.php?lno=<?php echo $details[0]['loan']['loan_no']; ?>&staff=<?php echo $details[0]['loan']['loan_officer']; ?>"><i class="fa fa-edit"></i></a></h6>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Associated Branch: </span>
                                                                            <h6 class="mt-0"> <?php echo @$details[0]['branch']; ?> &nbsp; &nbsp; <a class="text-primary" href="edit_loan_branch.php?lno=<?php echo $details[0]['loan']['loan_no']; ?>&branch=<?php echo $details[0]['loan']['branchid']; ?>"><i class="fa fa-edit"></i></a></h6>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <span class="text-muted">Days in Arrears: </span>
                                                                            <h6 class="mt-0"> <?php echo days_in_arrears(@$details[0]['loan']) ?></h6>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <label class="text-label form-label">Notes / Comments:
                                                            <?php echo @$details[0]['loan']['notes'] . ', ' . @$details[0]['loan']['officer_change_reason']; ?> &nbsp;&nbsp;<a style="color: #44814E !important;" class="load_via_ajax" href="add_comment.php?t=<?php echo $details[0]['loan']['loan_no']; ?>&notes=<?php echo $details[0]['loan']['notes']; ?>"><i class="ti-settings"></i>Add Comment </a> </label>

                                                    </div>
                                                    <div class="row">
                                                        <label class="text-label form-label">Attachments: <a href="javascript:void(0);" style="color: #44814E !important;" data-bs-toggle="modal" data-bs-target="#cameraModal"><i class="fa fa-link m-0"></i> </a>
                                                        </label>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="cameraModal">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Upload Attachment</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="https://eaoug.org/loan_attachments.php" method="POST" enctype="multipart/form-data">
                                                                            <input type="hidden" name="lid" value="<?= $details[0]['loan']['loan_no'] ?>" />
                                                                            <div class="form-group mb-3">
                                                                                <label class="label-text">Attachment Name / Notes</label>

                                                                                <input type="text" name="name" class="form-control" required>

                                                                            </div>
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">Upload</span>
                                                                                <div class="form-file">
                                                                                    <input type="file" name="file" class="form-file-input form-control" required>
                                                                                </div>
                                                                            </div>
                                                                            <br />
                                                                            <button type="submit" name="attach" value="attach" class="btn btn-primary light btn-xs mb-1">Upload</button>
                                                                        </form>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        $gms = $response->getLoanAttachments($details[0]['loan']['loan_no']);
                                                        if ($gms != '') {


                                                            foreach ($gms as $gm) {
                                                                echo '
                                                                <label class="list-group-item text-danger"><a href="https://eaoug.org/' . $gm['link'] . '" target="_blank">' . $gm['name'] . '</a></label>
                                                  
                                                
                                                ';
                                                            }
                                                        }
                                                        ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div id="collaterals" class="tab-pane fade <?= @$_REQUEST['current_tab'] == 'collaterals' ? 'show active' : '' ?>">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">Loan Collateral
                                                        Information</h4>
                                                    <div>
                                                        <?php if ($permissions->hasSubPermissions('edit_collateral')) : ?>
                                                            <a href="add_collateral.php?id=<?php echo $details[0]['loan']['loan_no']; ?>" class="btn btn-primary light btn-xs mb-1">Add
                                                                Collateral</a>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
                                                <div class="card-body">

                                                    <div class="table-responsive">
                                                        <table id="example3" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:80px;"><strong>#</strong></th>
                                                                    <th><strong>Name</strong></th>
                                                                    <th><strong>Type</strong></th>
                                                                    <th><strong>Market Value</strong></th>
                                                                    <th><strong>Forced Sale Value</strong></th>
                                                                    <th><strong>Received by</strong></th>
                                                                    <th><strong>Attachment</strong></th>
                                                                    <th><strong>Status</strong></th>
                                                                    <th><strong>Date Added</strong></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $cott = $response->getLoanCollaterals($details[0]['loan']['loan_no']);


                                                                if ($cott != '') {

                                                                    foreach ($cott as $row) {
                                                                        echo '
                                                                        <tr>
                                                                        <td><strong>' . $row['_cid'] . '</strong></td>
                                                                        <td>' . $row['_collateral'] . '</td>
                                                                        <td>' . $row['_catname'] . '</td>
                                                                        <td>' . $row['_mvalue'] . '</td>
                                                                        <td>' . $row['_fvalue'] . '</td>
                                                                        <td>' . $row['_receivedby'] . '</td>
                                                                        <td><a href="https://eaoug.org/' . $row['_attachment'] . '" class="text-primary">View</a></td>
                                                                        <td><span
                                                                                class="badge light ' . ($row['_status'] == 0 ? 'badge-success' : 'badge-danger') . '">' . ($row['_status'] == 0 ? 'Active' : 'Released') . '</span>
                                                                        </td>
                                                                        
                                                                       
                                                                       
                                                                        <td>' . normal_date($row['_date_created']) . '</td>
                                                                        <td>
                                                                            <div class="dropdown">
                                                                                <button type="button"
                                                                                    class="btn btn-success light sharp"
                                                                                    data-bs-toggle="dropdown">
                                                                                    <svg width="20px" height="20px"
                                                                                        viewBox="0 0 24 24" version="1.1">
                                                                                        <g stroke="none" stroke-width="1"
                                                                                            fill="none" fill-rule="evenodd">
                                                                                            <rect x="0" y="0" width="24"
                                                                                                height="24"></rect>
                                                                                            <circle fill="#000000" cx="5"
                                                                                                cy="12" r="2"></circle>
                                                                                            <circle fill="#000000" cx="12"
                                                                                                cy="12" r="2"></circle>
                                                                                            <circle fill="#000000" cx="19"
                                                                                                cy="12" r="2"></circle>
                                                                                        </g>
                                                                                    </svg>
                                                                                </button>
                                                                                <div class="dropdown-menu">
                                                                                    <a class="dropdown-item"
                                                                                        href="edit_collateral.php?id=' . $row['_cid'] . '">Edit</a>
                                                                                    <a class="dropdown-item"
                                                                                        href="delete_collateral.php?id=' . $row['_cid'] . '">Delete</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                        
                                                                        ';
                                                                    }
                                                                }
                                                                ?>


                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div id="income_source" class="tab-pane fade <?= @$_REQUEST['current_tab'] == 'income_source' ? 'show active' : '' ?>">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">Income Sources
                                                        Information</h4>
                                                    <div>
                                                        <?php if ($permissions->hasSubPermissions('edit_collateral')) : ?>
                                                            <a class="btn btn-primary light btn-xs mb-1" data-bs-toggle="modal" data-bs-target="#add_income_source">Add
                                                                Income Source</a>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
                                                <div class="card-body">

                                                    <div class="table-responsive">
                                                        <table id="example3" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:80px;"><strong>#</strong></th>
                                                                    <th><strong>Income Source</strong></th>
                                                                    <th><strong>Expected Returns</strong></th>
                                                                    <th><strong>Description</strong></th>

                                                                    <th><strong>Attachment</strong></th>
                                                                    <th><strong>Date Added</strong></th>
                                                                    <th><strong>Actions</strong></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $incs = $response->getLoanIncomeSources($details[0]['loan']['loan_no']);


                                                                if ($incs != '') {

                                                                    foreach ($incs as $row) {
                                                                        echo '
                                                                        <tr>
                                                                        <td><strong>' . $row['id'] . '</strong></td>
                                                                        <td>' . $row['name'] . '</td>
                                                                        <td>' . $row['return'] . '</td>
                                                                        <td>' . $row['desc'] . '</td>
                                                                       <td><a href="https://eaoug.org/' . $row['attachment'] . '" class="text-primary">View</a></td>
                                                                        <td>' . normal_date($row['date_created']) . '</td>
                                                                        
                                                                        <td>
                                                                            <div class="dropdown">
                                                                                <button type="button"
                                                                                    class="btn btn-success light sharp"
                                                                                    data-bs-toggle="dropdown">
                                                                                    <svg width="20px" height="20px"
                                                                                        viewBox="0 0 24 24" version="1.1">
                                                                                        <g stroke="none" stroke-width="1"
                                                                                            fill="none" fill-rule="evenodd">
                                                                                            <rect x="0" y="0" width="24"
                                                                                                height="24"></rect>
                                                                                            <circle fill="#000000" cx="5"
                                                                                                cy="12" r="2"></circle>
                                                                                            <circle fill="#000000" cx="12"
                                                                                                cy="12" r="2"></circle>
                                                                                            <circle fill="#000000" cx="19"
                                                                                                cy="12" r="2"></circle>
                                                                                        </g>
                                                                                    </svg>
                                                                                </button>
                                                                                <div class="dropdown-menu">
                                                                                    <a class="dropdown-item"
                                                                                        href="edit_inc_source.php?id=' . $row['id'] . '">Edit</a>
                                                                                    <a class="dropdown-item"
                                                                                        href="delete_inc_source.php?id=' . $row['id'] . '">Delete</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                        
                                                                        ';
                                                                    }
                                                                }
                                                                ?>


                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div id="loan_schedule" class="tab-pane fade <?= @$_REQUEST['current_tab'] == 'loan_schedule' ? 'show active' : '' ?>">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">Loan Schedule</h4>
                                                    <div>
                                                        <!-- onclick="PrintContent('exreportn')" -->
                                                        <a class="btn btn-primary light btn-xs mb-1" href="export_report.php?exportFile=export_loan_schedule&useFile=1&id=<?= $details[0]['loan']['loan_no'] ?>" target="_blank">Print</a>
                                                        <a class="btn btn-primary light btn-xs mb-1" href="export_report.php?exportFile=export_loan_schedule_savings&useFile=1&id=<?= $details[0]['loan']['loan_no'] ?>" target="_blank">Print ( Forced Savings )</a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive" id="exreportn">
                                                        <table id="example3" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:2px;"><strong>#</strong></th>
                                                                    <th style="width:90px;"><strong>Due Date</strong></th>
                                                                    <th><strong>Interest Due</strong></th>
                                                                    <th><strong>Principal Due</strong></th>
                                                                    <th><strong>Total Amount Due</strong></th>
                                                                    <th><strong>Outstanding Balance</strong></th>
                                                                    <th style="width:108px"><strong>Status</strong></th>

                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $collates = $response->getLoanSchedule($details[0]['loan']['loan_no']);
                                                                if ($collates != '') {
                                                                    $count = 1;

                                                                    foreach ($collates as $row) {

                                                                        $show_status = '';
                                                                        $pay_status = '';

                                                                        // set statuses -- payment time status && payment status
                                                                        if ($row['pay_time_status'] == 'on_time') {
                                                                            $pay_status = '<span class="badge light badge-success">On Time</span>';
                                                                        }
                                                                        if ($row['pay_time_status'] == 'post_paid') {
                                                                            $pay_status = '<span class="badge light badge-success">Post</span>';
                                                                        }
                                                                        if ($row['pay_time_status'] == 'late') {
                                                                            $pay_status = '<span class="badge light badge-danger">Late</span>';
                                                                        }
                                                                        if ($row['status'] == 'paid') {
                                                                            $show_status = '<span class="badge light badge-success">Cleared</span>' . ' ' . $pay_status;
                                                                        } else {
                                                                            $show_status = '<span class="badge light badge-danger">Pending</span>';
                                                                        }
                                                                        echo '
                                                                        <tr>
                                                                        <td>' . $count++ . '</td>
                                                                        <td class="no_print clickable_lno_date" ref-no="' . $row['edited_date']  . '" lno="' . $row['schedule_id']  . '"  date_orig="' . $row['date_of_payment']  . '">' . normal_date($row['date_of_payment']) . '</td>
                                                                        <td>' . number_format($row['interest']) . '</td>
                                                                        <td>' . number_format($row['principal']) . '</td>
                                                                        <td>' . number_format($row['amount']) . '</td>
                                                                        <td>' . number_format($row['balance']) . '</td>
                                                                        <td>' . $show_status . '</td>

                                                                        
                                                                    </tr>
                                                                        
                                                                        ';
                                                                    }
                                                                    echo '
                                                                <tr>
                                                                <td colspan="2"><strong>TOTAL:</strong></td>
                                                                <td><strong>' . number_format($details[0]['loan']['interest_amount']) . '</strong></td>
                                                                <td><strong>' . number_format($details[0]['loan']['principal']) . '</strong></td>
                                                                <td><strong>' . number_format($details[0]['loan']['total_loan_amount']) . '</strong></td>
                                                                <td></td>
                                                                <td></td>
                                                                
                                                            </tr>
                                                                ';
                                                                }
                                                                ?>

                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--**********************************
            Content body end
        ***********************************-->




            <div class="modal fade bd-example-modal-lgD" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Decline Loan</h5>&nbsp;&nbsp;
                            <h6 class="modal-title text-primary"> - Denial Reason</h6>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">

                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['loan_no']; ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Reason for Declining this Loan*
                                    </label>
                                    <textarea name="reason" class="form-control" required><?php echo $details[0]['loan']['denialreason']; ?></textarea>
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="decline" class="btn btn-primary">Decline Loan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="add_income_source" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Income Source</h5>&nbsp;&nbsp;
                            <!-- <h6 class="modal-title text-primary"> - Denial Reason</h6> -->

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST" enctype="multipart/form-data" action="https://eaoug.org/loan_income.php">

                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['loan_no']; ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Income Source*
                                    </label>
                                    <input type="text" name="name" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Expected Returns*
                                    </label>
                                    <input type="number" name="return" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Details*
                                    </label>
                                    <textarea name="details" class="form-control" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Attachment*
                                    </label>
                                    <input type="file" name="attach" class="form-control" />
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="inc_s" class="btn btn-primary">Add</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="modal fade bd-example-modal-lgR" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Denial Reason</h5>&nbsp;&nbsp;
                            <h6 class="modal-title text-primary"> - Declined Loan</h6>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">

                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['loan_no']; ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Reason for Declining this Loan*
                                    </label>
                                    <textarea name="reason" class="form-control" required readonly><?php echo $details[0]['loan']['denialreason']; ?></textarea>
                                </div>

                            </div>

                            <!-- <div class="modal-footer">

                                <button type="submit" name="decline" class="btn btn-primary">Decline Loan</button>
                            </div> -->
                        </form>

                    </div>
                </div>
            </div>
            <div class="modal fade bd-example-modal-lg3" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Loan Approval</h5>&nbsp;&nbsp;
                            <h6 class="modal-title text-primary"> - Approved Loan Terms</h6>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">

                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['loan_no']; ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Approval Date*
                                    </label>
                                    <input type="date" name="approve_date" class="form-control" placeholder="" value="<?php echo date('Y-m-d', strtotime($details[0]['loan']['approval_date'])); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Approved Loan Amount*
                                    </label>
                                    <input type="text" name="amount" class="form-control comma_separated" placeholder="" value="<?php echo number_format($details[0]['loan']['approvedamount']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Frequency *</label>
                                    <?php
                                    $vd = '';
                                    if ($details[0]['loan']['repay_cycle_id'] == 1) {
                                        $vd = 'DAYS';
                                    } else if ($details[0]['loan']['repay_cycle_id'] == 2) {
                                        $vd = 'WEEKS';
                                    } else if ($details[0]['loan']['repay_cycle_id'] == 3) {
                                        $vd = 'MONTHS';
                                    } else if ($details[0]['loan']['repay_cycle_id'] == 4) {
                                        $vd = 'DAYS';
                                    } else if ($details[0]['loan']['repay_cycle_id'] == 5) {
                                        $vd = 'YEARS';
                                    } else if ($details[0]['loan']['repay_cycle_id'] == 6) {
                                        $vd = 'MONTHS';
                                    }
                                    ?>
                                    <select class="me-sm-2 default-select form-control wide" id="freqType" name="freq" style="display: none;" required>

                                        <option value="1" <?= $details[0]['loan']['repay_cycle_id'] == 1 ? 'selected' : '' ?>>Daily</option>
                                        <option value="2" <?= $details[0]['loan']['repay_cycle_id'] == 2 ? 'selected' : '' ?>>Weekly</option>
                                        <option value="3" <?= $details[0]['loan']['repay_cycle_id'] == 3 ? 'selected' : '' ?>>Monthly</option>
                                        <option value="4" <?= $details[0]['loan']['repay_cycle_id'] == 4 ? 'selected' : '' ?>>Bi-Monthly</option>
                                        <option value="5" <?= $details[0]['loan']['repay_cycle_id'] == 5 ? 'selected' : '' ?>>Yearly</option>

                                    </select>
                                </div>
                                <div class="mb-3">


                                    <label class="text-label form-label">Approved Duration in <label id="dtype"> <?php echo $vd; ?></label>*
                                    </label>
                                    <input type="text" name="duration" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['approved_loan_duration']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Approved Interest Rate (% PER ANNUM)*
                                    </label>
                                    <input type="text" name="rate" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['monthly_interest_rate']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Notes / Comments*
                                    </label>
                                    <textarea name="notes" class="form-control" required><?php echo $details[0]['loan']['notes']; ?></textarea>
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="approve" class="btn btn-primary">Approve Loan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>



            <div class="modal fade bd-example-modal-lg4" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Loan Disbursement</h5>&nbsp;&nbsp;
                            <h6 class="modal-title text-primary"> - Disbursement Loan Terms</h6>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST" id="disburseForm">

                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['loan_no']; ?>">
                            <input type="hidden" name="cl" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['account_id']; ?>">
                            <input type="hidden" name="amount" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['approvedamount']; ?>">
                            <input type="hidden" name="lpid" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['loanproductid']; ?>">
                            <input type="hidden" name="auth" class="form-control" placeholder="" value="<?php echo $user[0]['userId']; ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Disbursement Date*
                                    </label>
                                    <input type="date" name="ddate" class="form-control" placeholder="" value="<?php echo date('Y-m-d', strtotime($details[0]['loan']['requesteddisbursementdate'])); ?>" required max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <!-- <div class="mb-3">
                                    <label class="text-label form-label">Date of First Payment (Note that System will add frequency type i.e month, day, or week to this date) *
                                    </label>
                                    <input type="date" name="sdate" class="form-control" placeholder="" value="" required >
                                </div> -->
                                <!-- <div class="mb-3">
                                    <label class="text-label form-label">Loan Processing Fees Rate (in %)*
                                    </label>
                                    <input type="number" name="proc" class="form-control" min="0" value="3" required>
                                </div> -->

                                <div class="mb-3">
                                    <label class="text-label form-label">Freeze Account Balance*
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6">


                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gridRadios" value="true" onClick="setDown()" checked>
                                                <label class="form-check-label">
                                                    Yes
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gridRadios" value="false" onclick="setUp()">
                                                <label class="form-check-label">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="mb-3" id="headd">
                                    <label class="text-label form-label">Specify Amount to Freeze*
                                    </label>
                                    <input type="text" name="famount" class="form-control" placeholder="" value="0" data-type="amount">
                                </div>

                                <div class="mb-3" id="headd">
                                    <label class="text-label form-label">Prefered Mode of Disbursement*
                                    </label>
                                    <select class="default-select  form-control wide" style="display: none;" id="payment_methods" required name="mode">
                                        <option value="saving" selected>Via Client's Savings Account
                                        </option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Via Bank/Cheque/Mobile Money</option>
                                    </select>
                                </div>

                                <div class="form-group" id="dest_bank" style="display: none;">
                                    <label>Affected Bank Account:</label>
                                    <select id="bank_acc" name="bank_acc" class="form-control">

                                        <?php
                                        if ($bank_accounts) {

                                            foreach ($bank_accounts as $b_acc) {
                                                echo '<option value="' . $b_acc['cid'] . '">' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . ' - Bank: ' . $b_acc['bank_name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group" id="insert_cheque_no" style="display: none;">
                                    <label>Cheque No: </label>
                                    <input id="cheque_no" name="cheque_no" class="form-control">
                                </div>


                                <div class="form-group" id="dest_cash_acc" style="display: none;">
                                    <label>Affected Cash Account:</label>
                                    <select id="cash_acc" name="cash_acc" class="form-control">
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
                                <br /><br />
                                <br /><br />

                                <div class="mb-3">
                                    <div class="form-check custom-checkbox mb-3">
                                        <input type="checkbox" class="form-check-input" id="customCheckBox1" name="auto_pay" checked value="1">
                                        <label class="form-check-label" for="customCheckBox1">Enable Automatic Loan Repayments Over this loan</label>
                                        <p class="text-muted mb-3">If un-checked system won't attempt to
                                            make loan repayment collections from customer's account.</p>
                                    </div>
                                </div>

                                <!-- <div class="custom-control custom-checkbox mb-3"> -->
                                <input type="hidden" name="send_sms" value="1" class="custom-control-input" id="send_sms">
                                <!-- <label class="custom-control-label" for="send_sms">Send SMS to member</label>
                                    <p class="text-muted mb-3">If un-checked system won't attempt to send an sms</p>
                                </div> -->

                                <br /><br />


                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="disburse" class="btn btn-primary">Disburse Loan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="enconomic_sector">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Loan Enconomic Sector</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">
                            <form action="<?= BACKEND_BASE_URL ?>Loan/update_loan_enconomic_sector.php" class="custom-form" id="enconomic_sector_form" data-reload-page="1" data-confirm-action="1" data-retain-form-data="1">
                                <input type="hidden" value="<?= encrypt_data(@$loan_details['loan_no']) ?>" name="loan_id">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-label form-label">Specify Sector </label>
                                            <input type="text" class="form-control" name="sector" value="<?= @$loan_details['enconomic_sector'] ?? '' ?>" />
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-label form-label"> </label>
                                            <button type="submit" class="btn btn-primary form-control">
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="update_forced_savings">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Client Forced Savings</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">
                            <form action="<?= BACKEND_BASE_URL ?>Loan/update_loan_forced_savings.php" class="custom-form" id="update_forced_savings_form" data-reload-page="1" data-confirm-action="1" data-retain-form-data="1">
                                <input type="hidden" value="<?= encrypt_data(@$loan_details['loan_no']) ?>" name="loan_id">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-label form-label">Amount </label>
                                            <input type="text" class="form-control comma_separated" name="savings" value="<?= @$loan_details['forced_saving'] ?? 0 ?>" />
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-label form-label"> </label>
                                            <button type="submit" class="btn btn-primary form-control">
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="update_loan_product_fees">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Loan Attached Fees</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">
                            <form action="<?= BACKEND_BASE_URL ?>Loan/update_loan_product_fees.php" class="custom-form" id="update_loan_product_fees_form" data-reload-page="1" data-confirm-action="1" data-retain-form-data="1">
                                <input type="hidden" value="<?= encrypt_data(@$loan_details['loan_no']) ?>" name="loan_id">

                                <div class="row">
                                    <?php

                                    foreach ($lps_text as $row) :
                                        $tes = str_contains($string, $row['fee_id']) ? 'checked' : 'other';
                                    ?>
                                        <div class="mt-3">
                                            <div class="form-check custom-checkbox mb-3">
                                                <input type="checkbox" class="form-check-input" id="<?= $row['fee_id'] ?>" name="fees[]" <?php echo $tes;  ?> value="<?= $row['fee_id'] ?>">
                                                <label class="form-check-label" for="<?= $row['fee_id'] ?>"><?= $row['name'] ?></label>
                                                <p class="text-muted mb-3"><?= $row['type'] . ' ( ' . $row['rateAmount'] . ' ) - ' . $row['paymentType'] ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach ?>



                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-label form-label"> </label>
                                            <button type="submit" class="btn btn-primary form-control">
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="update_loan_product_details">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Loan Product Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">
                            <form action="<?= BACKEND_BASE_URL ?>Loan/update_loan_product_details.php" class="custom-form" id="update_loan_product_details_form" data-reload-page="1" data-confirm-action="1" data-retain-form-data="1">
                                <input type="hidden" value="<?= encrypt_data(@$loan_details['loan_no']) ?>" name="loan_id">

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input activate-sections" data-activate-sections="fixed-amount" data-deactivate-sections="penalty-rate" type="radio" name="penalty_type" value="fixed_amount" <?= @$is_fixed_amount_penalty_loan ? 'checked' : '' ?> id="fixed_amount_settings">
                                    <label class="form-check-label" for="fixed_amount_settings">
                                        Penalty Fixed Amount
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input activate-sections" data-activate-sections="penalty-rate" data-deactivate-sections="fixed-amount" data-activate="1" type="radio" name="penalty_type" value="rate" <?= $is_penalty_rate_loan ? 'checked' : '' ?> id="penalty_rate_settings">
                                    <label class="form-check-label" for="penalty_rate_settings">
                                        Penalty Rate
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 section-fixed-amount <?= @$is_fixed_amount_penalty_loan ? '' : 'hide' ?>">
                                        <div class="form-floating">
                                            <input type="text" name="penalty_fixed_amount" class="form-control comma_separated" placeholder=" " value="<?= number_format($loan_details['penalty_fixed_amount']) ?>" data-is-required="1">
                                            <label>Penalty Fixed Amount</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3 section-penalty-rate <?= @$is_penalty_rate_loan ? '' : 'hide' ?>">
                                        <div class="form-floating">
                                            <input type="number" name="penalty_interest_rate" class="form-control" placeholder=" " step=".01" value="<?= $loan_details['penalty_interest_rate'] ?>" data-is-required="1">
                                            <label>Penalty Interest Rate</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12  mt-3 section-penalty-rate <?= @$is_penalty_rate_loan ? '' : 'hide' ?>">
                                        <div class="form-floating">
                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="penalty_based_on">
                                                <option value="">Select</option>
                                                <option value="p" <?= $loan_details['penalty_based_on'] == 'p' ? 'selected' : '' ?>>Principal in Arrears</option>
                                                <option value="i" <?= $loan_details['penalty_based_on'] == 'i' ? 'selected' : '' ?>>Interest in Arrears</option>
                                                <option value="both" <?= $loan_details['penalty_based_on'] == 'both' ? 'selected' : '' ?>>Both Principal & Interest in Arrears</option>
                                            </select>
                                            <label class="text-label form-label">Calculate Penalty Based On *</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="form-floating">
                                            <input type="number" class="form-control input-rounded" placeholder=" " name="num_grace_periods" value="<?= $loan_details['num_grace_periods'] ?>">
                                            <label class="text-label form-label">Number of Grace Period DAYS*</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="form-floating">
                                            <select class="me-sm-2 default-select form-control wide" name="penalty_grace_type">
                                                <option value="">Select</option>
                                                <option value="pay_i" <?= $loan_details['penalty_grace_type'] == 'pay_i' ? 'selected' : '' ?>>Pay Interest Only</option>
                                                <option value="pay_p" <?= $loan_details['penalty_grace_type'] == 'pay_p' ? 'selected' : '' ?>>Pay Principal Only</option>
                                                <option value="pay_none" <?= $loan_details['penalty_grace_type'] == 'pay_none' ? 'selected' : '' ?>>Pay None (Client Pays doesn't
                                                    pay anything until Grace Period ends)</option>

                                            </select>
                                            <label class="text-label form-label">Grace Period Type *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <div class="form-floating">
                                            <select class="me-sm-2 default-select form-control wide" name="grace_applies">
                                                <option value="">Select</option>
                                                <option value="0" <?= $loan_details['grace_period_type'] == 0 ? 'selected' : '' ?>>At the Begining of the Loan Term</option>
                                                <option value="1" <?= $loan_details['grace_period_type'] == 1 ? 'selected' : '' ?>>During the Loan Term</option>
                                                <option value="2" <?= $loan_details['grace_period_type'] == 2 ? 'selected' : '' ?>>At the End of the Loan Term</option>

                                            </select>
                                            <label class="text-label form-label">Grace Period Applies When *</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="form-floating">
                                            <input type="number" class="form-control input-rounded" placeholder=" " name="penalty_max_days" value="<?= $loan_details['penalty_max_days'] ?>">
                                            <label class="text-label form-label">Maximum Penalty DAYS*</label>
                                        </div>
                                    </div>

                                    <br /><br /><br />

                                    <div class="mt-3">
                                        <div class="form-check custom-checkbox mb-3">
                                            <input type="checkbox" class="form-check-input" id="customCheckBox1" name="auto_pay" <?= @$loan_details['auto_pay'] ? 'checked' : '' ?> value="1">
                                            <label class="form-check-label" for="customCheckBox1">Activate Automatic
                                                Loan Payments</label>
                                            <p class="text-muted mb-3">Automatically deduct savings to pay due loans
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="form-check custom-checkbox mb-3">
                                            <input type="checkbox" class="form-check-input" id="customCheckBox1" name="auto_repay_penalty" <?= @$loan_details['auto_repay_penalty'] ? 'checked' : '' ?> value="1">
                                            <label class="form-check-label" for="customCheckBox1">Activate automatic
                                                penalty payments</label>
                                            <p class="text-muted mb-3">Automatically deduct savings to pay due
                                                penalty</p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-label form-label"> </label>
                                            <button type="submit" class="btn btn-primary form-control">
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade bd-example-modal-lgDate" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Loan Due Date Edit Form</h5>&nbsp;&nbsp;
                            <!-- <h6 class="modal-title text-primary"> - Denial Reason</h6> -->

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">

                            <input type="hidden" name="sid" class="form-control" placeholder="" id="schedule_id">
                            <input type="hidden" name="date_orig" class="form-control" placeholder="" id="date_orig">
                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['loan_no']; ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Due Date*
                                    </label>
                                    <input type="date" name="sdate" class="form-control" placeholder="" id="sdate">
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="sndate" class="btn btn-primary">Update Date</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>



            <div class="modal fade lgacno1" tabindex="-1" role="dialog" aria-hidden="true" id="ch_acc">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Change Account Form</h5>

                            <!-- <h6 class="modal-title text-primary"> - </h6> -->

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">

                            <input type="hidden" name="uid" class="form-control" placeholder="" value="<?php echo $details[0]['client']['userId']; ?>">
                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $details[0]['loan']['loan_no']; ?>">

                            <div class="modal-body">
                                <p style="padding: 5px;"> ** This will change the Client Account ID of the Loan and also update all trxns over this loan to this new Client ID. Incase of any Inconsistences in Statement Closing Balances of these two accounts, you're required to Update or Reconcile these manually by other means.</p>
                                <div class="mb-3">
                                    <label class="text-label form-label">Select the Affiliated Client Account*
                                    </label>
                                    <select id="clientsselectn" class="form-control select2x" name="clientacc" required></select>
                                </div>
                                <!-- <br /><br /> -->

                            </div>

                            <div class="modal-footer">
                                <button type="submit" name="change_account" class="btn btn-primary">Update Account</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

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

            datePickerId.max = new Date().toISOString().split("T")[0];
            $('#disburseForm').submit(function() {
                $(this).children('input[type=submit]').hide();
                // $(this).children('input[type=submit]').prop('disabled', true);
                // $(this).children('input[type=submit]').text('Processing...');
            });

            function setUp() {
                var x = document.getElementById("headd");


                x.style.display = "none";
            }


            function setDown() {
                var x = document.getElementById("headd");
                x.style.display = "block";

            }
        </script>


        <script>
            $(document).ready(function() {

                $(document).on("click", '.clickable_lno_date', function(e) {
                    e.preventDefault();
                    handle_ln_date_click_options($(this));
                });

            });


            function handle_ln_date_click_options(item) {
                var ref_no = item.attr('ref-no');
                var lno = item.attr('lno');
                var date_orig = item.attr('date_orig');
                $(".transaction-custom-menu").remove();

                document.getElementById("schedule_id").value = lno;
                document.getElementById("sdate").value = formatDate(date_orig);
                document.getElementById("date_orig").value = formatDate(ref_no);

                var $options = "<ul class='transaction-custom-menu'>" +
                    "<li class='dropdown-header dropdown-item'>-- " + ref_no + " --</li>" +
                    "<li data-action='edit' ref-no = '" + ref_no + "' lno = '" + lno + "'  date_orig = '" + date_orig + "' class='dropdown-item'><a aria-expanded='false' data-bs-toggle='modal' data-bs-target='.bd-example-modal-lgDate'>Edit this Due Date</a></li>" +
                    "</ul>";

                item.append($options);
                $(".transaction-custom-menu").show();

                // If the menu element is clicked
                $(".transaction-custom-menu li").click(function() {

                    // This is the triggered action name
                    var $action = $(this).attr("data-action");
                    var $ref_no = $(this).attr("ref-no");
                    var $lno = $(this).attr("lno");
                    var $date_orig = $(this).attr("date_orig");

                    if ($action) {



                        // $(".bd-example-modal-lgDate").show();
                    }

                    // Hide it AFTER the action was triggered
                    $(".transaction-custom-menu").hide(100);
                });

                // If the document is clicked somewhere
                $(document).bind("mousedown", function(e) {

                    // If the clicked element is not the menu
                    if (!$(e.target).parents(".transaction-custom-menu").length > 0) {
                        // Hide it
                        $(".transaction-custom-menu").hide(100);
                    }
                });

            }

            function formatDate(date) {
                var d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                return [year, month, day].join('-');
            }
        </script>

        <script>
            $(document).ready(function() {
                $("select.select2x").select2({
                    dropdownParent: $('#ch_acc'),
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
                } else if (pay_method.val() == 'offset' || pay_method.val() == 'credit') {
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
                    } else if (pay_method.val() == 'cheque' || pay_method.val() == 'dr_cheque' || pay_method
                        .val() == 'cr_cheque') {

                        dest_cash_acc.hide();
                        payable_account.hide();
                        offset_savings.hide();
                        dest_bank.show();
                        insert_cheque_no.show();
                    } else if (pay_method.val() == 'offset' || pay_method.val() == 'credit') {
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
        </script>
</body>

</html>