<!--**********************************
            Nav header start
        ***********************************-->
<div class="nav-header">
	<a href="/" class="brand-logo">

		<img src="<?php echo is_null($user[0]['blogo']) ? 'icons/favicon.png' : $user[0]['blogo']; ?>" onerror="this.onerror=null; this.src='icons/favicon.png'" height="57" width="57" />
		<div class="brand-title">
			<span class="brand-sub-title font-w800" style="display: -webkit-box;
-webkit-line-clamp: 2;
-webkit-box-orient: vertical;
overflow: hidden;
text-overflow: ellipsis; font-weight:800 !important;"><?php echo is_null($user[0]['tname']) ? strtoupper($user[0]['bankName']) : strtoupper($user[0]['bankName']); ?></span>

		</div>

	</a>
	<div class="nav-control">
		<div class="hamburger">
			<span class="line"></span><span class="line"></span><span class="line"></span>
		</div>
	</div>
</div>
<!--**********************************
            Nav header end
        ***********************************-->

<!--**********************************
            Header start
        ***********************************-->
<div class="header border-bottom bg-sky-950">
	<div class="header-content">
		<nav class="navbar navbar-expand">
			<div class="collapse navbar-collapse justify-content-between">
				<div class="header-left">
					<div class="dashboard_bar" style="font-size: 1rem !important;">
						<a href="/" class="">Dashboard</a>
					</div>
				</div>
				<ul class="navbar-nav header-right">
					<?php if (isset($_SESSION['working_hours_end_at'])) { ?>
						<li>
							<div href="">
								<section class="countdown">
									<div class="timer">
										<div class="counter">
											<div class="counter__box sky-blue">
												<p class="counter__time" id="hours">2</p>
												<p class="counter__duration">hours</p>
											</div>
											<p class="dots">:</p>
											<div class="counter__box sky-blue">
												<p class="counter__time" id="minutes">30</p>
												<p class="counter__duration">minutes</p>
											</div>
											<p class="dots">:</p>
											<div class="counter__box sky-blue">
												<p class="counter__time" id="seconds">06</p>
												<p class="counter__duration">seconds</p>
											</div>

										</div>
									</div>
								</section>
							</div>
						</li>
					<?php } ?>

					<li class="nav-item dropdown notification_dropdown">

						<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
							<img src="<?php echo is_null($user[0]['photo']) ? 'images/account.png' : $user[0]['photo']; ?>" onerror="this.onerror=null; this.src='images/account.png'" alt="" height="50" width="50" class="rounded-circle" />
							<!-- <span class="badge light text-white bg-warning rounded-circle">1</span> -->
						</a>
						<a class="nav-link " href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
							<div class="header-info2 d-flex align-items-center">

								<div class="d-flex align-items-center sidebar-info p-2">
									<div>
										<span class="font-w800 d-block mb-2" style="font-size: medium !important;  display: inline-block;width: 180px;white-space: nowrap;overflow: hidden !important;text-overflow: ellipsis; color: #343a40 !important;"><?php
																																																															echo $user[0]['firstName'] . ' ' . $user[0]['lastName'];
																																																															?></span>
										<small class="text-end font-w800" style="color: #ec2a35 !important;"><?php
																												echo $user[0]['positionTitle'];
																												?></small>
									</div>
								</div>

							</div>
						</a>
						<div class="dropdown-menu dropdown-menu-end">
							<div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3">
								<ul class="timeline">
									<li>
										<a href="profile.php" class="dropdown-item ai-icon ">
											<svg xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
												<circle cx="12" cy="7" r="4"></circle>
											</svg>
											<span class="ms-2">View My Profile </span>
										</a>

									</li>
									<li>

										<a href="staff_set_password.php?id=<?= $user[0]['userId'] ?>" class="dropdown-item ai-icon">
											<svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
												<polyline points="22,6 12,13 2,6"></polyline>
											</svg>
											<span class="ms-2">Change Password </span>

										</a>
										<!-- chnage password modal -->
										<!-- <div class="modal fade pass_change" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog modal-lg">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title">Change Password</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal">
														</button>
													</div>
													<form method="POST" action="change_password.php">

														<div class="modal-body">
															<div class="mb-3">
																<label class="text-label form-label">Current Password*
																</label>
																<input type="text" name="password" class="form-control" placeholder="">
															</div>
															<div class="mb-3">
																<label class="text-label form-label">New Password*
																</label>
																<input type="text" name="newpass" class="form-control" placeholder="" id="password">
															</div>
															<div class="mb-3">
																<label class="text-label form-label">Repeat New Password*
																</label>
																<input type="text" name="repeat" class="form-control" placeholder="" id="confirm_password">
																<span id='message'></span>
															</div>

														</div>

														<div class="modal-footer">

															<button type="submit" name="submit" class="btn btn-primary">Update Password</button>
														</div>
													</form>

												</div>
											</div>
										</div> -->
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