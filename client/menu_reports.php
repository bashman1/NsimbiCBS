<li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
        <i class="fas fa-chart-line"></i>
        <span class="nav-text">Reports</span>
    </a>
    <ul aria-expanded="false">

        <?php if ($menu_permission->hasSubPermissions('view_client_reports') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="client_reports.php"> Clients</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_share_reports') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="share_reports.php"> Shares</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_saving_reports')   || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="saving_reports.php"> Savings</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_loan_reports') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="loan_reports.php"> Loans</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_financial_reports') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="financial_statements.php"> Financial Statements</a></li>
            <li><a href="umra_reports.php"> UMRA Reports</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_day_book_report') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="day_book_report.php">Day Book</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_teller_till_sheet') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="teller_till_sheet.php">Staff Till Sheet</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('view_mobile_money_reports') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="mobile_money_reports.php">Mobile Money</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_school_pay_reports') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="school_pay_reports.php">School Pay</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_mobile_banking_reports') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="mobile_banking_reports.php">Mobile Banking</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_agent_banking_reports') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="agent_banking_reports.php">Agent Banking</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('view_audit_trail') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="audit_trail.php">Audit Trail</a></li>
        <?php } ?>



    </ul>
</li>