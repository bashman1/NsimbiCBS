<li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
        <i class="fas fa-mobile"></i>
        <span class="nav-text">Mobile Banking</span>
    </a>
    <ul aria-expanded="false">
        <?php if ($menu_permission->hasSubPermissions('view_app_subscriptions') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="app_subscriptions.php">Subscriptions</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('review_loan_applications_via_app') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <!-- <li><a href="app_loans">Loan Applications</a></li> -->
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('review_membership_applications_via_app') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <!-- <li><a href="app_membership">Membership Applications</a></li> -->
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_transactions_via_app') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <!-- <li><a href="app_trxns">Transactions Via App</a></li> -->
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('update_general_app_settings')) { ?>
            <!-- <li><a href="app_settings">General Settings</a></li> -->
        <?php } ?>




    </ul>
</li>