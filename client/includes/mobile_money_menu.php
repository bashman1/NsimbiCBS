<li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
        <i class="fas fa-mobile-alt"></i>
        <span class="nav-text">Mobile Money</span>
    </a>
    <ul aria-expanded="false">
        <?php if ($menu_permission->hasSubPermissions('view_mobile_money_transactions') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="mm_wallet_stmt.php">Mobile Money Transactions</a></li>
            <li><a href="ussd_push.php">USSD Push</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('initiate_ussd_push')) { ?>
            <!-- <li><a href="ussd_push.php">USSD Push</a></li> -->
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('view_mobile_money_wallet') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="mm_wallet.php">Wallet Status</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('view_mobile_money_log') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="mm_logs.php">Mobile Money Logs</a></li>
        <?php } ?>




    </ul>
</li>