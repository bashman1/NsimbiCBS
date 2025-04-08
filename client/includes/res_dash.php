   <!--**********************************
            Content body start
        ***********************************-->
   <div class="content-body">
       <!-- row -->
       <div class="container-fluid">
           <div class="row">
               <?php
                if (isset($_SESSION['success']) && $_SESSION['success'] !== "") {

                    unset($_SESSION['success']);
                }
                if (isset($_SESSION['error']) && $_SESSION['error'] !== "") {
                    echo '
                <div class="alert alert-danger solid alert-square">
                ' . $_SESSION['error'] . '
                </div>
                ';
                }
                unset($_SESSION['error']);

                ?>
               <div class="col-xl-12 col-xxl-12 col-lg-12 col-sm-12 css-1d3bbye">
                   <div class="widget-stat card css-6xchgi">
                       <div class="card-body p-4 css-1xynra6">
                           <div class="">

                               <h1 class="MuiBox-root css-3ci3ci">NSIMBI Core Banking System</h1>
                               <!-- <p class="MuiBox-root css-1ibm745">Sound NSIMBI for a sustainable NSIMBI.</p> -->
                           </div>
                           <div class="MuiBox-root css-1z6qsq">
                               <div class="filterInputs MuiBox-root css-0">
                                   <div class="MuiBox-root css-70qvj9"><svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-57kesc" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="AccountBalanceOutlinedIcon">
                                           <path d="M6.5 10h-2v7h2zm6 0h-2v7h2zm8.5 9H2v2h19zm-2.5-9h-2v7h2zm-7-6.74L16.71 6H6.29zm0-2.26L2 6v2h19V6z"></path>
                                       </svg><span class="MuiBox-root css-xrqk3y">Branch: All</span></div>
                                   <div class="MuiBox-root css-mxldl9"><svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-57kesc" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="CalendarMonthOutlinedIcon">
                                           <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2m0 16H5V10h14zm0-12H5V6h14zM9 14H7v-2h2zm4 0h-2v-2h2zm4 0h-2v-2h2zm-8 4H7v-2h2zm4 0h-2v-2h2zm4 0h-2v-2h2z"></path>
                                       </svg><span class="MuiBox-root css-xrqk3y"> Since Start </span></div>
                               </div><button class="MuiButtonBase-root MuiButton-root MuiButton-outlined MuiButton-outlinedPrimary MuiButton-sizeMedium MuiButton-outlinedSizeMedium MuiButton-colorPrimary MuiButton-root MuiButton-outlined MuiButton-outlinedPrimary MuiButton-sizeMedium MuiButton-outlinedSizeMedium MuiButton-colorPrimary filterButton css-2tc4nl" tabindex="0" type="button"><svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-1t8mnmp" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="FilterAltOutlinedIcon" data-bs-toggle="modal" data-bs-target="#dash_filter_form">
                                       <path d="M7 6h10l-5.01 6.3zm-2.75-.39C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39c.51-.66.04-1.61-.79-1.61H5.04c-.83 0-1.3.95-.79 1.61"></path>
                                   </svg><span class="MuiTouchRipple-root css-w0pj6f"></span></button>
                           </div>


                       </div>
                   </div>
               </div>
               <div class="col-xl-12 dashboard">
                   <div class="row">
                       <!-- added -->

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-primary text-primary">
                                           <!-- <i class="ti-user"></i> -->
                                           <svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                               <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                               <circle cx="12" cy="7" r="4"></circle>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Individuals</p>
                                           <span class="text-black fs-18 font-w700"><a href="individual_clients.php" id="individuals"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                           <!-- <h4 class="mb-0">3280</h4> -->
                                           <!-- <span class="badge badge-primary">+3.5%</span> -->
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-warning text-warning">
                                           <svg id="icon-orders" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                               <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                               <polyline points="14 2 14 8 20 8"></polyline>
                                               <line x1="16" y1="13" x2="8" y2="13"></line>
                                               <line x1="16" y1="17" x2="8" y2="17"></line>
                                               <polyline points="10 9 9 9 8 9"></polyline>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Groups</p>
                                           <!-- <h4 class="mb-0">2570</h4> -->
                                           <!-- <span class="badge badge-warning">+3.5%</span> -->
                                           <span class="text-black fs-18 font-w700"><a href="group_clients.php" id="groups"> <img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body  p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-danger text-danger">
                                           <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                                               <line x1="12" y1="1" x2="12" y2="23"></line>
                                               <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Institutions</p>
                                           <!-- <h4 class="mb-0">364.50K</h4> -->
                                           <!-- <span class="badge badge-danger">-3.5%</span> -->
                                           <span class="text-black fs-18 font-w700"><a href="institution_clients.php" id="institutions"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-primary text-primary">
                                           <!-- <i class="ti-user"></i> -->
                                           <i class="la la-users"></i>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Members</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_client.php" id="clients2"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                           <!-- <h4 class="mb-0">3280</h4> -->
                                           <!-- <span class="badge badge-primary">+3.5%</span> -->
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <i class="flaticon-381-user-7"></i>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Saving A/Cs</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_client.php" id="clients"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                           <!-- <h4 class="mb-0">364.50K</h4>
                                           <span class="badge badge-success">-3.5%</span> -->
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>





                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Savings Balance</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_membership_schedule.php" id="sav_bal"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <!-- start of the piecharts -->



                       <div class="col-xl-6 col-lg-12">
                           <div class="card">
                               <div class="card-header">
                                   <h4 class="card-title">Member Distribution by Gender</h4>
                               </div>
                               <div class="card-body">
                                   <div class="morris_chart_height" id="gender-chart"></div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-6 col-lg-12">
                           <div class="card">
                               <div class="card-header">
                                   <h4 class="card-title">Member Distribution by Age Group</h4>
                               </div>
                               <div class="card-body">
                                   <div class="morris_chart_height" id="age-charts"></div>

                               </div>
                           </div>
                       </div>

                       <div class="col-xl-6 col-lg-12">
                           <div class="card">
                               <div class="card-header">
                                   <h4 class="card-title">Member Distribution by Occupation</h4>
                               </div>
                               <div class="card-body">

                                   <div id="occupation-chart" class="morris_chart_height"></div>

                               </div>
                           </div>
                       </div>
                       <div class="col-xl-6 col-lg-12">
                           <div class="card">
                               <div class="card-header">
                                   <h4 class="card-title">Income vs Expenses (Last 12 Months)</h4>
                               </div>
                               <div class="card-body">
                                   <div class="morris_chart_height" id="education-chart"></div>

                               </div>
                           </div>
                       </div>


                       <!-- end of the piecharts -->
                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-danger text-danger">
                                           <i class="flaticon-381-diamond"></i>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Freezed Balances</p>
                                           <span class="text-black fs-18 font-w700"><a href="freezed_accounts.php" id="freezed_bal"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Saving OverDrafts</p>
                                           <span class="text-black fs-18 font-w700"><a href="all_over_drafts.php" id="over_drafts"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Deposits Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&transaction_type=D&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="deposits"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Withdraws Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&transaction_type=W&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="withdraws"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Loan Disbursements Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="loan_disbursements_report.php?loan_product_id=&loan_officer_id=&branchId=<?= @$user[0]['branchId'] ?>&disbursement_start_date=<?= date('Y-m-d') ?>&disbursement_end_date=<?= date('Y-m-d') ?>" id="disb_today"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Agent Deposits Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&transaction_type=W&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="agent_deposits"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Agents Repayments Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&transaction_type=W&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="agent_repayments"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>


                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Loan Repayments Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_loan_repayments.php" id="lps_today"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Expenses Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&transaction_type=E&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="expenses_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Income Registered Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&transaction_type=I&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="incomes_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Liabilities Registered Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&transaction_type=LIA&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="liabilities_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Online Deposits Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="mm_wallet_stmt.php" id="online_deposits"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Online Withdraws Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&transaction_type=WO&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="online_withdraws"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Share Holders</p>
                                           <span class="text-black fs-18 font-w700"><a href="share_register.php" id="shares"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Share Amount</p>
                                           <span class="text-black fs-18 font-w700"><a href="share_register.php" id="share_amount"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Loan Portifolio</p>
                                           <span class="text-black fs-18 font-w700"><a href="active_loans.php" id="portifolio"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Active Loans</p>
                                           <span class="text-black fs-18 font-w700"><a href="active_loans.php" id="active_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Loan Repayments</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_loan_repayments.php" id="repayments"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="progress default-progress">
                           <div class="progress-bar bg-gradient5 progress-animated" role="progressbar" id="progres_dist">
                               <span class="" id="progres_label">0% Loan Collections</span>
                           </div>
                       </div>
                       <br /><br />


                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Loan Applications</p>
                                           <span class="text-black fs-18 font-w700"><a href="loan_applications.php" id="pending_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>&nbsp; | &nbsp;

                                           <span class="text-black fs-18 font-w700"><a href="loan_applications.php" id="pending_loan_amount"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">INTEREST COLLECTED - LOANS</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_loan_repayments.php" id="int_collected"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Loans In Arrears</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_loan_arrears.php" id="loan_arrears"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Total Amount in Arrears</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_loan_arrears.php" id="loan_arrears_amount"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>



                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Loans Due Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="active_loans.php?branchId=<?= $user[0]['branchId'] ?>&loan_status=3&loan_product_id=&next_due_date=&disbursement_start_date=&disbursement_end_date=" id="due_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Total Amount Due Today</p>
                                           <span class="text-black fs-18 font-w700"><a href="active_loans.php?branchId=<?= $user[0]['branchId'] ?>&loan_status=3&loan_product_id=&next_due_date=&disbursement_start_date=&disbursement_end_date=" id="amount_due"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>


                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Cleared Loans</p>
                                           <span class="text-black fs-18 font-w700"><a href="closed_loans.php" id="cleared_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Interest Waived </p>
                                           <span class="text-black fs-18 font-w700"><a href="interest_waiver_stmt.php" id="int_waived"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>


                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Fixed Deposits</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_running_fds.php" id="p_inv"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>


                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Matured Fixed A/Cs</p>
                                           <span class="text-black fs-18 font-w700"><a href="all_fixed_deposits.php" id="mature_fixed"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">FD Interest Due</p>
                                           <span class="text-black fs-18 font-w700"><a href="all_fixed_deposits.php" id="c_inv"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">FD Interest Paid Out</p>
                                           <span class="text-black fs-18 font-w700"><a href="report_closed_fds.php" id="inv_due"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Fixed A/Cs </p>
                                           <span class="text-black fs-18 font-w700"><a href="all_fixed_deposits.php" id="dec_prof"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Receivables Due</p>
                                           <span class="text-black fs-18 font-w700"><a href="receivables_report.php" id="debtors_due"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="col-xl-4 col-xxl-4 col-lg-4 col-sm-4">
                           <div class="widget-stat card">
                               <div class="card-body p-4">
                                   <div class="media ai-icon">
                                       <span class="me-3 bgl-success text-success">
                                           <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
                                               <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                               <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                               <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                           </svg>
                                       </span>
                                       <div class="media-body">
                                           <p class="mb-1" style="margin-bottom: 0px;
    margin-top: 0px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    color: rgb(148, 164, 196);
    text-transform: none;
    white-space: normal;">Payables Due</p>
                                           <span class="text-black fs-18 font-w700"><a href="payables_report.php" id="creditors_due"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>


                       <div class="col-xl-12 col-lg-12">
                           <div class="card">
                               <div class="card-header">
                                   <h4 class="card-title">Membership Statistics - Branch Level</h4>
                               </div>
                               <div class="card-body">

                                   <div id="membership_branch-chart" class="morris_chart_height"></div>

                               </div>
                           </div>
                       </div>


                       <div class="col-xl-12 col-lg-12">
                           <div class="card">
                               <div class="card-header">
                                   <h4 class="card-title">Membership Statistics - Product Level</h4>
                               </div>
                               <div class="card-body">

                                   <div id="membership_product-chart" class="morris_chart_height"></div>

                               </div>
                           </div>
                       </div>






                   </div>





               </div>


           </div>
       </div>
   </div>
   <!--**********************************
            Content body end
        ***********************************-->