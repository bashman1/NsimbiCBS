<?php
require_once(__DIR__ . '../../middleware/PermissionMiddleware.php');
$menu_permission = new PermissionMiddleware();
?>
<!--**********************************
            Sidebar start
        ***********************************-->
<div class="dlabnav">
    <div class="dlabnav-scroll">

        <ul class="metismenu" id="menu">
            <?php
            // super user routes
            if ($menu_permission->IsSuperAdmin()) { ?>
                <li><a class="" href="index.php" aria-expanded="false">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li><a class="" href="all_banks.php" aria-expanded="false">
                        <i class="fas fa-clone"></i>
                        <span class="nav-text">Institutions</span>
                    </a>
                </li>
                <li><a class="" href="all_bank_admins.php" aria-expanded="false">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Institution Admins</span>
                    </a>
                </li>
                <li><a class="" href="sms_manage.php" aria-expanded="false">
                        <i class="fas fa-sms"></i>
                        <span class="nav-text">SMS Management</span>
                    </a>
                </li>
                <li><a class="" href="audit_trail.php" aria-expanded="false">
                        <i class="fas fa-file-alt"></i>
                        <span class="nav-text">Audit Trail</span>
                    </a>
                </li>

                <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-trash"></i>
                        <span class="nav-text">Trash</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#">Institutions</a></li>
                        <li><a href="#">Institutions Admins</a></li>
                    </ul>
                </li>

                <!-- '; -->

            <?php
                //  Bank Level Admin routes

            } else if ($menu_permission->IsBankAdmin()  || $menu_permission->hasSubPermissions('view_everything')) { ?>
                <li><a class="" href="index.php" aria-expanded="false">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Clients</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="individual_clients.php">Individuals</a></li>
                        <li><a href="institution_clients.php">Institutions</a></li>
                        <li><a href="group_clients.php">Groups</a></li>
                        <li><a href="deactivated_accs.php">Deactivated Accounts</a></li>
                        <?php
                        if ($menu_permission->IsBankAdmin()) :
                        ?>
                            <li><a href="convert_clients.php">Convert Client</a></li>
                        <?php endif; ?>
                        <li><a href="search_general_client.php">Search Client</a></li>
                        <li><a href="new_client.php">New Client</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-clone"></i>
                        <span class="nav-text">Loans</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="loans_search_general.php">Search Client Loans</a></li>
                        <li><a href="active_loans.php">View Active Loans</a></li>
                        <li><a href="approved_loans.php">Loan Disbursement</a></li>
                        <li><a href="loan_applications.php">Loan Applications</a></li>
                        <!-- <li><a href="">Enter Loan Application</a></li> -->
                        <li><a href="declined_loans.php">Declined Loans</a></li>

                        <li><a href="closed_loans.php">Closed Loans</a></li>
                        <li><a href="loan_calc.php">Loan Calculator</a></li>
                        <li><a href="collateral_register.php">Collateral Register</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-wallet"></i>
                        <span class="nav-text">Savings</span>
                    </a>
                    <ul aria-expanded="false">
                        <?php
                        if ($menu_permission->IsBankAdmin()) :
                        ?>
                            <li><a href="search_client.php">Enter Deposit</a></li>
                            <li><a href="withdraw_search_client.php">Enter Withdraw</a></li>
                        <?php endif; ?>
                        <li><a href="overdrafts.php">Over-Drafts</a></li>
                        <li><a href="fixed_deposits.php">Fixed Deposits</a></li>
                        <li><a href="transfers_tab.php">Transfers</a></li>
                        <li><a href="freezed_accounts.php">Freezed Accounts</a></li>
                        <li><a href="all_deposits.php">View All Deposits</a></li>
                        <li><a href="all_withdraws.php">View All Withdraws</a></li>
                        <li><a href="forced_savings.php">Forced Savings</a></li>
                        <li><a href="saving_interest.php">Interest On Savings</a></li>



                    </ul>
                </li>
                <?php require_once('shares_menu.php');
                require_once('school_fees_menu.php') ?>
                <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-calculator"></i>
                        <span class="nav-text">Accounting</span>
                    </a>
                    <ul aria-expanded="false">

                        <li><a href="accounting_tab.php">Accounting</a></li>
                        <li><a href="trxn_accounts.php">Transaction Accounts</a></li>
                        <li><a href="trxn_search.php">Search for Trxn</a></li>
                        <?php if ($menu_permission->hasSubPermissions('register_staff_shortfalls') || $menu_permission->hasSubPermissions('view_everything')) { ?>
                            <li><a href="staff_shortfall.php">Staff Shortfalls </a></li>
                        <?php } ?>
                        <?php if ($menu_permission->hasSubPermissions('register_staff_excess')) { ?>
                            <li><a href="staff_excess.php">Staff Excess Cash Register </a></li>
                        <?php } ?>
                        <?php if ($menu_permission->hasSubPermissions('view_teller_till_sheet')) { ?>
                            <li><a href="teller_till_sheet.php">Staff Till Sheet </a></li>
                        <?php } ?>
                        <li><a href="journal_entries.php">Journal Entries </a></li>
                        <li><a href="bulk_entries.php">Bulk Entries </a></li>

                        <!-- <li><a href="satellite_entry.php">Satellite Trxn </a></li> -->
                        <!-- <li><a href="satellite_deposit.php">Satellite Deposit </a></li> -->
                        <!-- <li><a href="satellite_withdraw.php">Satellite Withdraw </a></li> -->
                        <li><a href="general_fees.php">General Fees </a></li>
                    </ul>

                </li>
                <li><a class="" href="sms_tab.php" aria-expanded="false">
                        <i class="fas fa-wallet"></i>
                        <span class="nav-text">SMS Banking</span>
                    </a>
                </li>
                <?php

                require_once('mobile_money_menu.php');

                ?>

                <?php require_once('field_agents_menu.php') ?>
                <?php require_once('mobile_banking_menu.php') ?>
                <?php require_once('menu_data_importer.php') ?>
                <?php require_once('menu_reports.php') ?>
                <?php require_once('trash_menu.php') ?>

                <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-cog"></i>
                        <span class="nav-text">Admin</span>
                    </a>
                    <ul aria-expanded="false">
                        <?php
                        if ($menu_permission->IsBankAdmin()) :
                        ?>
                            <li><a href="bank_settings.php">Bank Settings</a></li>
                        <?php endif; ?>
                        <li><a href="all_branches.php">Branches</a></li>
                        <li><a href="roles.php">Roles & Permissions</a></li>
                        <li><a href="all_bank_staff.php">Staff</a></li>
                        <?php
                        if ($menu_permission->IsBankAdmin()) :
                        ?>
                            <li><a href="fees_tab.php">Fees Settings</a></li>
                            <li><a href="loan_settings.php">Loan Settings</a></li>
                            <li><a href="all_saving_groups.php">Savings Settings</a></li>
                            <li><a href="shares_settings.php">Shares Settings</a></li>
                            <li><a href="working_hours.php">Working Hours</a></li>
                        <?php endif; ?>
                        <li><a href="trash_can.php">Trash</a></li>
                    </ul>
                </li>


            <?php }

            // Bank stuff user routes
            else { ?>
                <li><a class="" href="index.php" aria-expanded="false">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <?php
                /**
                 * if user is permitted access clients 
                 */
                if ($menu_permission->hasPermissions('clients')) { ?>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-users"></i>
                            <span class="nav-text">Clients</span>
                        </a>
                        <ul aria-expanded="false">
                            <?php if ($menu_permission->hasPermissions('clients')) { ?>
                                <!-- <li><a href="all_clients">View All Clients</a></li> -->
                                <li><a href="individual_clients.php">Individuals</a></li>
                                <li><a href="institution_clients.php">Institutions</a></li>
                                <li><a href="group_clients.php">Groups</a></li>
                                <li><a href="deactivated_accs.php">Deactivated Accounts</a></li>
                                <?php if ($menu_permission->hasSubPermissions('update_client_type')) { ?>
                                    <li><a href="convert_clients.php">Convert Client</a></li>
                                <?php } ?>
                                <li><a href="search_general_client.php">Search Client</a></li>
                                <li><a href="new_client.php">New Client</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php

                /**
                 * if user is permitted to access loans
                 */
                if ($menu_permission->hasPermissions('loans')) { ?>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-clone"></i>
                            <span class="nav-text">Loans</span>
                        </a>
                        <ul aria-expanded="false">
                            <?php if ($menu_permission->hasSubPermissions('view_active_loans')) { ?>
                                <li><a href="loans_search_general.php">Search Client Loans</a></li>
                                <li><a href="active_loans.php">View Active Loans</a></li>
                            <?php } ?>

                            <?php if ($menu_permission->hasSubPermissions(['view_awaiting_disburse'])) { ?>
                                <li><a href="approved_loans.php">Loan Disbursement</a></li>
                            <?php } ?>

                            <?php if ($menu_permission->hasSubPermissions('review_loan_application')) { ?>
                                <li><a href="loan_applications.php">Loan Applications</a></li>
                            <?php } ?>

                            <?php if ($menu_permission->hasSubPermissions('view_declined_loans')) { ?>
                                <li><a href="declined_loans.php">Declined Loans</a></li>
                            <?php } ?>

                            <?php if ($menu_permission->hasSubPermissions('view_closed_loans')) { ?>
                                <li><a href="closed_loans.php">Closed Loans</a></li>
                            <?php } ?>

                            <?php if ($menu_permission->hasSubPermissions('view_collateral_register')) { ?>
                                <li><a href="collateral_register.php">Collateral Register</a></li>
                            <?php } ?>

                            <li><a href="loan_calc.php">Loan Calculator</a></li>
                        </ul>
                    </li>

                <?php } ?>

                <?php

                /**
                 * if user has access to savings
                 */
                if ($menu_permission->hasPermissions('savings')) { ?>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-wallet"></i>
                            <span class="nav-text">Savings</span>
                        </a>
                        <ul aria-expanded="false">

                            <?php if ($menu_permission->hasSubPermissions('add_deposit')) { ?>
                                <li><a href="search_client.php">Enter Deposit</a></li>
                            <?php } ?>
                            <li><a href="add_deposit_agent.php">Agent Deposit</a></li>
                            <?php if ($menu_permission->hasSubPermissions('add_withdraw')) { ?>
                                <li><a href="withdraw_search_client.php">Enter Withdraw</a></li>
                            <?php } ?>
                            <?php if ($menu_permission->hasSubPermissions('create_overdraft_application')) { ?>
                                <li><a href="overdrafts.php">Over-Drafts</a></li>
                            <?php } ?>
                            <?php if ($menu_permission->hasSubPermissions('create_fixed_deposits')) { ?>
                                <li><a href="fixed_deposits.php">Fixed Deposits</a></li>
                            <?php } ?>
                            <?php if ($menu_permission->hasSubPermissions('create_transfer')) { ?>
                                <li><a href="transfers_tab.php">Transfers</a></li>
                            <?php } ?>
                            <?php if ($menu_permission->hasSubPermissions('view_freezed_accounts')) { ?>
                                <li><a href="freezed_accounts.php">Freezed Accounts</a></li>
                            <?php } ?>
                            <?php if ($menu_permission->hasSubPermissions('view_all_deposits')) { ?>
                                <li><a href="all_deposits.php">View All Deposits</a></li>
                            <?php } ?>

                            <?php if ($menu_permission->hasSubPermissions('view_all_withdraws')) { ?>
                                <li><a href="all_withdraws.php">View All Withdraws</a></li>
                                <li><a href="forced_savings.php">Forced Savings</a></li>
                            <?php } ?>


                            <!-- <li><a href="fixed_calc.php">Fixed Deposits</a></li> -->
                        </ul>
                    </li>
                <?php } ?>

                <?php
                if ($menu_permission->hasPermissions('shares')) {
                    require_once('shares_menu.php');
                }
                ?>
                <?php
                if ($menu_permission->hasPermissions('school_fees')) {
                    require_once('school_fees_menu.php');
                }
                ?>

                <?php if ($menu_permission->hasPermissions('accounting')) { ?>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-calculator"></i>
                            <span class="nav-text">Accounting</span>
                        </a>
                        <ul aria-expanded="false">

                            <li><a href="accounting_tab.php">Accounting</a></li>
                            <li><a href="trxn_accounts.php">Transaction Accounts</a></li>
                            <li><a href="journal_entries.php">Journal Entries </a></li>
                            <li><a href="bulk_entries.php">Bulk Entries </a></li>
                            <li><a href="trxn_search.php">Search for Trxn</a></li>
                            <!-- <li><a href="satellite_deposit.php">Satellite Deposit </a></li> -->
                            <!-- <li><a href="satellite_withdraw.php">Satellite Withdraw </a></li> -->
                        </ul>

                    </li>
                <?php } ?>

                <?php if ($menu_permission->hasPermissions('sms_banking')) { ?>
                    <li><a class="" href="sms_tab.php" aria-expanded="false">
                            <i class="fas fa-sms"></i>
                            <span class="nav-text">SMS Banking</span>
                        </a>

                    </li>
                <?php } ?>
                <?php
                if ($menu_permission->hasPermissions('mobile_money')) {
                    require_once('mobile_money_menu.php');
                }
                ?>
                <?php
                if ($menu_permission->hasPermissions('agent_banking')) {
                    require_once('field_agents_menu.php');
                }
                ?>
                <?php
                if ($menu_permission->hasPermissions('mobile_banking')) {
                    require_once('mobile_banking_menu.php');
                }
                ?>

                <?php if ($menu_permission->hasPermissions('working_hours')) { ?>
                    <li><a class="" href="working_hours.php" aria-expanded="false">
                            <i class="fas fa-clock"></i>
                            <span class="nav-text">Working Hours</span>
                        </a>

                    </li>
                <?php } ?>

                <?php

                if ($menu_permission->hasPermissions('data_importer')) {
                    require_once('menu_data_importer.php');
                }

                if ($menu_permission->hasPermissions('reports')) {
                    require_once('menu_reports.php');
                }
                ?>
            <?php } ?>

        </ul>

        <div class="copyright">
            <p><strong>NSIMBI Central Banking System</strong> © <?php echo date('Y'); ?> All Rights Reserved</p>
            <p class="fs-12">Designed & Developed by <a href="https://nsimbi.io/" style="color: #ec2a35;" target="_blank">NSIMBI</a> </p>
        </div>
    </div>
</div>
<!--**********************************
            Sidebar end
        ***********************************-->