<!--**********************************
            Nav header start
        ***********************************-->
<!-- <div class="nav-header">
	
	<div class="nav-control">
		<div class="hamburger">
			<span class="line"></span><span class="line"></span><span class="line"></span>
		</div>
	</div>
</div> -->
<!--**********************************
            Nav header end
        ***********************************-->

<!--**********************************
            Header start
        ***********************************-->
<div class="header border-bottom">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-center">
                    <div class="dashboard_bar">
                        INTERNET BANKING
                    </div>
                </div>
                <ul class="navbar-nav header-right">


                    <li class="nav-item dropdown notification_dropdown">

                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="<?php echo is_null($user[0]['photo']) ? 'images/account.png' : $user[0]['photo']; ?>" onerror="this.onerror=null; this.src='images/account.png'" alt="" height="50" width="50" class="rounded-circle" />
                            <!-- <span class="badge light text-white bg-warning rounded-circle">1</span> -->
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3">
                                <ul class="timeline">

                                    <li>
                                        <a href="set_mpin_client.php?id=<?= $user[0]['userId'] ?>&cid=<?= $_GET['cid'] ?>" class="dropdown-item ai-icon" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                <polyline points="22,6 12,13 2,6"></polyline>
                                            </svg>
                                            <span class="ms-2">Change mPIN </span>

                                        </a>
                                    </li>
                                    <li>

                                        <a href="logout.php" class="dropdown-item ai-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                <polyline points="16 17 21 12 16 7"></polyline>
                                                <line x1="21" y1="12" x2="9" y2="12"></line>
                                            </svg>
                                            <span class="ms-2">Logout </span>
                                        </a>
                                    </li>




                                </ul>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</div>