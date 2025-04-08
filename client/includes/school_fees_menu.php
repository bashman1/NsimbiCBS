<li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
        <i class="fas fa-user-graduate"></i>
        <span class="nav-text">School Pay</span>
    </a>
    <ul aria-expanded="false">
        <?php if ($menu_permission->hasSubPermissions('manage_fees_subscriptions') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="fees_subscriptions.php">Subscriptions</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('create_fees_payments')) { ?>
            <li><a href="pay_school_fees_search.php">Pay School Fees</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('view_fees_payments') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="view_fees_payments.php">View Fees Payments</a></li>
        <?php } ?>






    </ul>
</li>