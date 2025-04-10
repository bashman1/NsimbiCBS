<li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
        <i class="fas fa-landmark"></i>
        <span class="nav-text">Agent Banking</span>
    </a>
    <ul aria-expanded="false">
        <?php if ($menu_permission->hasSubPermissions('view_agent_transactions') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="agent_list.php">Agent Collections</a></li>
        <?php } ?>

        <?php if ($menu_permission->hasSubPermissions('view_agent_general_report') || $menu_permission->hasSubPermissions('view_everything')) { ?>
            <li><a href="agent_report.php">Performance Report</a></li>
        <?php } ?>

       




    </ul>
</li>