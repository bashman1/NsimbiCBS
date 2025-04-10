<li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
        <i class="fas fa-user-check"></i>
        <span class="nav-text">Shares</span>
    </a>
    <ul aria-expanded="false">
        <?php if ($menu_permission->hasSubPermissions('view_share_register') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="share_register.php">Share Register</a></li>
            <li><a href="share_dividends.php">Dividends</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('share_purchase')) { ?>
            <li><a href="share_search_client.php">Purchase</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('share_transfer')) { ?>
            <li><a href="share_transfer.php">Transfer</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('share_withdraw')) { ?>
            <li><a href="share_withdraw_search_client.php">Withdraw</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('view_share_trxns') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="share_purchase_trxns.php">Purchase Transactions</a></li>
            <li><a href="share_transfer_trxns.php">Transfer Transactions</a></li>
            <li><a href="share_withdraw_trxns.php">Withdraw Transactions</a></li>
        <?php } ?>


    </ul>
</li>