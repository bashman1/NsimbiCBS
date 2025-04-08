<li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
        <i class="fas fa-database"></i>
        <span class="nav-text">Data Importer</span>
    </a>
    <ul aria-expanded="false">
        <?php if ($menu_permission->hasSubPermissions('data_importer_clients') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="data_importer_clients.php">Import Clients</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('data_importer_loans') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="data_importer_loans.php">Import Loans</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('data_importer_shares')) { ?>
            <li><a href="data_importer_shares.php">Import Shares</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('data_importer_transactions')) { ?>
            <li><a href="data_importer_transactions.php">Import Transactions</a></li>
        <?php } ?>
        <?php if ($menu_permission->hasSubPermissions('data_importer_transactions')) { ?>
            <li><a href="data_importer_fds.php">Import Fixed Deposits</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('data_importer_coa_tb')) { ?>
            <li><a href="data_importer_coa_tb.php">Import Chart of Account & Trial Balance</a></li>
        <?php } ?>
    </ul>
</li>