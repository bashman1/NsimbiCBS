   <!--**********************************
            Content body start
        ***********************************-->
   <div class="content-body">
       <!-- row -->
       <div class="container-fluid">
           <div class="row">
               <?php
                if (isset($_SESSION['success']) && $_SESSION['success'] !== "") {
                    echo '
              <div class="alert alert-primary solid alert-square" style="background: #343a40 !important; border-color:#343a40 !important">
              ' . $_SESSION['success'] . '
              </div>
              ';
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
               <div class="col-xl-12 dashboard">
                   <div class="row">
                       <!-- added -->
                       <div class="col-xl-12">
                           <div class="card">
                               <div class="card-header flex-wrap border-0 pb-0 align-items-end">
                                   <div class="mb-3 me-3">
                                       <h5 class="fs-14 mb-1 font-w700">Individuals</h5>
                                       <span class="text-black fs-18 font-w700"><a href="individual_clients.php" id="individuals"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Groups</p>
                                       <span class="text-black fs-18 font-w700"><a href="group_clients.php" id="groups"> <img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Institutions</p>
                                       <span class="text-black fs-18 font-w700"><a href="institution_clients.php" id="institutions"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Saving A/Cs</p>
                                       <span class="text-black fs-18 font-w700"><a href="report_client.php" id="clients"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Share Holders </p>
                                       <span class="text-black fs-18 font-w700"><a href="share_register.php" id="shares"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>

                               </div>
                               <div class="card-header flex-wrap border-0 pb-0 align-items-end">
                                   <div class="mb-3 me-3">
                                       <h5 class="fs-14 mb-1 font-w700">Savings Balance</h5>
                                       <span class="text-black fs-18 font-w700"><a href="report_membership_schedule.php" id="sav_bal"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Freezed Balances</p>
                                       <span class="text-black fs-18 font-w700"><a href="freezed_accounts.php" id="freezed_bal"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Share Amount</p>
                                       <span class="text-black fs-18 font-w700"><a href="share_register.php" id="share_amount"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Loan Portifolio</p>
                                       <span class="text-black fs-18 font-w700"><a href="report_loan_status.php" id="portifolio"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Active Loans</p>
                                       <span class="text-black fs-18 font-w700"><a href="active_loans.php" id="active_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                               </div>
                               <div class="progress default-progress">
                                   <div class="progress-bar bg-gradient5 progress-animated" role="progressbar" id="progress_dist">
                                       <span class="" id="progress_label"></span>
                                   </div>
                               </div>
                               <div class="card-header flex-wrap border-0 pb-0 align-items-end">
                                   <div class="mb-3 me-3">
                                       <h5 class="fs-14 mb-1 font-w700">Loan Repayments</h5>
                                       <span class="text-black fs-18 font-w700"><a href="report_loan_repayments.php" id="repayments"><img src="images/loader.gif" alt="" class="content-loader sm"><a></span>
                                   </div>
                                   <div class="mb-3 me-3">
                                       <h5 class="fs-14 mb-1 font-w700">Interest Collected - Loans</h5>
                                       <span class="text-black fs-18 font-w700"><a href="report_loan_repayments.php" id="int_collected"><img src="images/loader.gif" alt="" class="content-loader sm"><a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Loan Applications</p>
                                       <span class="text-black fs-18 font-w700"><a href="loan_applications.php" id="pending_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Awaiting Disbursement</p>
                                       <span class="text-black fs-18 font-w700"><a href="approved_loans.php" id="approved_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Loans In Arrears </p>
                                       <span class="text-black fs-18 font-w700"><a href="report_loan_arrears.php" id="loan_arrears"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>

                               </div>
                               <div class="card-header flex-wrap border-0 pb-0 align-items-end">

                                   <div class="mb-3 me-3">
                                       <h5 class="fs-14 mb-1 font-w700">Total Amount in Arrears</h5>
                                       <span class="text-black fs-18 font-w700"><a href="report_loan_arrears.php" id="loan_arrears_amount"><img src="images/loader.gif" alt="" class="content-loader sm"><a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Loans Due Today</p>
                                       <span class="text-black fs-18 font-w700"><a href="active_loans.php?branchId=<?= $user[0]['branchId'] ?>&loan_status=3&loan_product_id=&next_due_date=&disbursement_start_date=&disbursement_end_date=" id="due_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Total Amount Due Today</p>
                                       <span class="text-black fs-18 font-w700"><a href="active_loans.php?branchId=<?= $user[0]['branchId'] ?>&loan_status=3&loan_product_id=&next_due_date=&disbursement_start_date=&disbursement_end_date=" id="amount_due"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Cleared Loans</p>
                                       <span class="text-black fs-18 font-w700"><a href="closed_loans.php" id="cleared_loans"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Interest Waived </p>
                                       <span class="text-black fs-18 font-w700"><a href="interest_waiver_stmt.php" id="int_waived"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>

                               </div>
                               <div class="progress default-progress">
                                   <div class="progress-bar bg-gradient5 progress-animated" role="progressbar" id="progress_dist">
                                       <span class="" id="progress_label"></span>
                                   </div>
                               </div>
                               <div class="card-header flex-wrap border-0 pb-0 align-items-end">

                                   <div class="mb-3 me-3">
                                       <h5 class="fs-14 mb-1 font-w700">Fixed A/Cs</h5>
                                       <span class="text-black fs-18 font-w700"><a href="all_fixed_deposits.php" id="dec_prof"><img src="images/loader.gif" alt="" class="content-loader sm"><a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Fixed Deposits</p>
                                       <span class="text-black fs-18 font-w700"><a href="all_fixed_deposits.php" id="p_inv"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">Matured Fixed A/Cs</p>
                                       <span class="text-black fs-18 font-w700"><a href="all_fixed_deposits.php" id="mature_fixed"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">FD Interest Due</p>
                                       <span class="text-black fs-18 font-w700"><a href="all_fixed_deposits.php" id="c_inv"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 me-3">
                                       <p class="fs-14 mb-1 font-w700">FD Interest Paid Out </p>
                                       <span class="text-black fs-18 font-w700" id="inv_due"><img src="images/loader.gif" alt="" class="content-loader sm"></span>
                                   </div>

                               </div>
                               <div class="card-header flex-wrap border-0 pb-0 align-items-end">
                                   <div class="mb-3 me-3">
                                       <h5 class="fs-14 mb-1 font-w700">Deposits Today</h5>
                                       <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&branchId=&transaction_type=D&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="deposits"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Withdraws Today</p>
                                       <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&branchId=&transaction_type=W&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="withdraws"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Expenses Today</p>
                                       <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&branchId=&transaction_type=E&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="expenses_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Income Registered Today </p>
                                       <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&branchId=&transaction_type=I&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="incomes_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Liabilities Registered Today</p>
                                       <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&branchId=&transaction_type=LIA&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="liabilities_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                               </div>


                               <div class="progress default-progress">
                                   <div class="progress-bar bg-gradient5 progress-animated" role="progressbar" id="progre_dist">
                                       <span class="" id="progre_label"></span>
                                   </div>
                               </div>

                               <div class="card-header flex-wrap border-0 pb-0 align-items-end">
                                   <div class="mb-3 me-3">
                                       <h5 class="fs-14 mb-1 font-w700">Online Deposits Today</h5>
                                       <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&branchId=&transaction_type=DO&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="online_deposits"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Online Withdraws Today</p>
                                       <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&branchId=&transaction_type=WO&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="online_withdraws"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>
                                   <div class="me-3 mb-3">
                                       <p class="fs-14 mb-1 font-w700">Wallet Balance</p>
                                       <span class="text-black fs-18 font-w700"><a href="day_book_report.php?filtered=1&branchId=&transaction_type=E&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="online_bal"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                   </div>


                               </div>
                               <div class="card-body">
                                   <div class="progress default-progress">
                                       <div class="progress-bar bg-gradient5 progress-animated" role="progressbar" id="progres_dist">
                                           <span class="" id="progres_label">0% Loan Collections</span>
                                       </div>
                                   </div>
                                   <div class="row mt-4 pt-3">
                                       <div class="col-xl-4 col-xxl-7 col-md-6">
                                           <h4 class="card-title font-w700">Weekly Summary</h4>
                                           <div class="row align-items-center">
                                               <div class="col-sm-6 col-5">
                                                   <canvas id="pieChart"></canvas>
                                               </div>
                                               <div class="col-sm-6 col-7">
                                                   <ul class="card-list mt-3">
                                                       <li class="mb-2"><span class="bg-success circle"></span><span class="me-4">
                                                               <svg class="me-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                   <rect x="-0.00012207" width="16" height="16" rx="8" fill="#8df05f" />
                                                               </svg>
                                                               Disbursed</span><span class="text-black fs-14 font-w700"></span></li>
                                                       <li class="mb-2"><span class="bg-danger circle"></span><span class="me-4">
                                                               <svg class="me-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                   <rect x="-0.00012207" y="3.05176e-05" width="16" height="16" rx="8" fill="#ff4b4b" />
                                                               </svg>
                                                               Repayments</span><span class="text-black fs-14 font-w700"></span></li>

                                                   </ul>
                                               </div>

                                           </div>
                                       </div>
                                       <div class="col-xl-8 col-xxl-5 col-md-6">
                                           <div id="line-chart" class="bar-chart"></div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <!-- <div class="col-xl-12">
                           <div class="card">
                               <div class="card-body">
                                   <div class="row align-items-center">
                                       <div class="col-xl-3 col-xxl-6 col-6">
                                           <div>
                                               <div class="d-flex align-items-center mb-3">
                                                   <div class="d-inline-block position-relative donut-chart-sale">
                                                       <span class="donut1" data-peity='{ "fill": ["rgb(172, 57, 212)", "rgba(238, 238, 238, 1)"],   "innerRadius": 20, "radius": 10}'>5/8</span>
                                                   </div>
                                                   <div class="ms-3">
                                                       <h4 class="fs-18 font-w700 mb-0">Individuals</h4>
                                                       <span class="fs-14 font-w700" id="individuals"><img src="images/loader.gif" alt="" class="content-loader sm"></span>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                       <div class="col-xl-3 col-xxl-6 col-6">
                                           <div>
                                               <div class="d-flex align-items-center mb-3">
                                                   <div class="d-inline-block position-relative donut-chart-sale">
                                                       <span class="donut1" data-peity='{ "fill": ["rgb(64, 212, 168)", "rgba(238, 238, 238, 1)"],   "innerRadius": 20, "radius": 10}'>4/5</span>
                                                   </div>
                                                   <div class="ms-3">
                                                       <h4 class="fs-18 font-w700 mb-0">Groups</h4>
                                                       <span class="fs-14 font-w700" id="groups"><img src="images/loader.gif" alt="" class="content-loader sm"></span>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                       <div class="col-xl-3 col-xxl-6 col-6">
                                           <div>
                                               <div class="d-flex align-items-center mb-3">
                                                   <div class="d-inline-block position-relative donut-chart-sale">
                                                       <span class="donut1" data-peity='{ "fill": ["rgb(70, 30, 231)", "rgba(238, 238, 238, 1)"],   "innerRadius": 20, "radius": 10}'>4/8</span>
                                                   </div>
                                                   <div class="ms-3">
                                                       <h4 class="fs-18 font-w700 mb-0">Institutions</h4>
                                                       <span class="fs-14 font-w700" id="institutions"><img src="images/loader.gif" alt="" class="content-loader sm"></span>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                       <div class="col-xl-3 col-xxl-6 col-6">
                                           <div>
                                               <div class="d-flex align-items-center mb-3">
                                                   <!-- <div class="d-inline-block position-relative donut-chart-sale">
                                                       <span class="donut1" data-peity='{ "fill": ["rgb(70, 30, 231)", "rgba(238, 238, 238, 1)"],   "innerRadius": 20, "radius": 10}'>4/8</span>
                                                   </div> -->
                       <!-- <div class="ms-3">
                                                       <h4 class="fs-18 font-w700 mb-0">Total Clients</h4>
                                                       <span class="fs-14 font-w700" id="clients"><img src="images/loader.gif" alt="" class="content-loader sm"></span>
                                                   </div>
                                               </div>
                                           </div>
                                       </div> -->

                       <!-- <div class="col-xl-3 col-xxl-6 col-6">
                                           <div class="mb-3">
                                               <a href="javascript:void(0);" class="btn btn-primary btn-lg">+New Spends</a>
                                           </div>
                                       </div> -->
                       <!-- </div>
                               </div>
                           </div>
                       </div> -->

                       <!-- added -->

                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Share Holders</span>
                                           <div class="h3 font-bold mb-0" id="shares">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                               <i class="bi bi-people"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total No. of Share Holders
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Clients</span>
                                           <div class="h3 font-bold mb-0" id="clients">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                               <i class="bi bi-people"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total No. of Clients
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Individuals</span>
                                           <div class="h3 font-bold mb-0" id="individuals">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                               <i class="bi bi-people"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total No. of Individuals
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Groups</span>
                                           <div class="h3 font-bold mb-0" id="groups">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                               <i class="bi bi-people"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total No. of Groups
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Institutions</span>
                                           <div class="h3 font-bold mb-0" id="institutions">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                               <i class="bi bi-people"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total No. of Institutions
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->

                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">
                                               Loan Applications
                                           </span>
                                           <div class="h3 font-bold mb-0" id="pending_loans">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-danger text-white text-lg rounded-circle">
                                               <i class="bi bi-briefcase"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total No. of Loan Applications
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">
                                               Loans Awaiting Disbursement
                                           </span>
                                           <div class="h3 font-bold mb-0" id="approved_loans">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-danger text-white text-lg rounded-circle">
                                               <i class="bi bi-briefcase"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total No. of Loans Awaiting Disbursement
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">
                                               Active Loans
                                           </span>
                                           <div class="h3 font-bold mb-0" id="active_loans">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-danger text-white text-lg rounded-circle">
                                               <i class="bi bi-briefcase"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total No. of Active Loans
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Loan Portifolio</span>
                                           <div class="h3 font-bold mb-0" id="portifolio">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-info text-white text-lg rounded-circle">
                                               <i class="bi bi-briefcase"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Loan Portifolio
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total Repayments</span>
                                           <div class="h3 font-bold mb-0" id="repayments">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                               <i class="bi bi-minecart-loaded"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           Total Loan Repayments
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->

                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Savings Accounts</span>
                                           <div class="h3 font-bold mb-0" id="saccs">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                               <i class="bi bi-minecart-loaded"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           <a href="teller_deposits">All Saving Accounts</a>
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Deposits Today</span>
                                           <div class="h3 font-bold mb-0" id="deposits">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                               <i class="bi bi-minecart-loaded"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           <a href="teller_deposits">View Transactions</a>
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->
                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Withdraws Today</span>
                                           <div class="h3 font-bold mb-0" id="withdraws">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                               <i class="bi bi-minecart-loaded"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           <a href="teller_withdraws">View Transactions</a>
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->

                       <!-- <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Expenses Today</span>
                                           <div class="h3 font-bold mb-0" id="expenses_daily">
                                               <img src="images/loader.gif" alt="" class="content-loader sm">
                                           </div>
                                       </div>
                                       <div class="col-auto">
                                           <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                               <i class="bi bi-minecart-loaded"></i>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="mt-2 mb-0 text-sm">
                                       <span class="badge badge-pill bg-soft-success text-success me-2">
                                           <i class="bi bi-arrow-up me-1"></i>
                                       </span>
                                       <span class="text-nowrap text-xs text-muted">
                                           <a href="expense_ledger">View Transactions</a>
                                       </span>
                                   </div>
                               </div>
                           </div>
                       </div> -->

                   </div>
                

                   <div class="row">

                       <div class="col-xl-6">
                           <div class="card">
                               <div class="card-header border-0 pb-0">
                                   <div>
                                       <h4 class="fs-20 font-w700 mb-2">Membership Statistics - Branch Level</h4>
                                       <!-- <span class="fs-14">Lorem ipsum dolor sit amet, consectetur</span> -->
                                   </div>
                               </div>
                               <div class="card-body pt-3">
                                   <!-- <p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tem</p> -->
                                   <ul class="specifics-list" style="padding: 10px !important;">
                                       <?php
                                        if ($user[0]['bankId']) {
                                            $branches = $response->getBankBranches($user[0]['bankId']);
                                            if ($branches !== '') {
                                                foreach ($branches as $row) {

                                                    echo '
                                                          <li>
                                           <div>
                                               <span class="text-black fs-18 font-w700"><a href="report_client.php?filtered=1&branchId=' . $row['id'] . '&region=&gender=&age_group=&income_level=&disability_status=&actype=&start_date=2000-01-26&end_date=2024-01-26">' . number_format($row['clients']) . '<span class="badge badge-pill bg-soft-success text-success me-0">
                                           (
                                       Total )</span></a>  <a href="report_client.php?filtered=1&branchId=' . $row['id'] . '&region=&gender=&age_group=&income_level=&disability_status=&actype=&start_date=' . date('Y-m-d') . '&end_date=' . date('Y-m-d') . '">' . number_format($row['clients_today']) . '<span class="badge badge-pill bg-soft-danger text-danger me-0">
                                       ( Today )</span></a></span><br/>
                                               <span class="text-nowrap text-xs text-muted">' . $row['name'] . '</span>
                                           </div>
                                       </li>
                                                        ';
                                                }
                                            } else {
                                                echo '<span class="fs-14">No Branches added yet</span>';
                                            }
                                        } else {
                                            echo '
                                                          <li>
                                           <div>
                                               <span class="text-black fs-18 font-w700"><a href="report_client.php?filtered=1&branchId=' . $user[0]['branchId'] . '&region=&gender=&age_group=&income_level=&disability_status=&actype=&start_date=2000-01-26&end_date=2024-01-26">' . $response->getBranchClientsCount($user[0]['branchId']) . '</a></span><br/>
                                               <span class="text-nowrap text-xs text-muted">' . $user[0]['branchName'] . '</span>
                                           </div>
                                       </li>
                                                        ';
                                        }
                                        ?>


                                   </ul>
                               </div>
                           </div>
                       </div>

                       <div class="col-xl-6">
                           <div class="card">
                               <div class="card-header border-0 pb-0">
                                   <div>
                                       <h4 class="fs-20 font-w700 mb-2">Membership Statistics - Saving Product</h4>
                                       <!-- <span class="fs-14">Lorem ipsum dolor sit amet, consectetur</span> -->
                                   </div>
                               </div>
                               <div class="card-body pt-3">
                                   <!-- <p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tem</p> -->
                                   <ul class="specifics-list" style="padding: 10px !important;">
                                       <?php

                                        $actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);
                                        if ($actypes !== '') {
                                            foreach ($actypes as $rown) {

                                                echo '
                                                          <li>
                                           <div>
                                               <span class="text-black fs-18 font-w700"><a href="report_client.php?filtered=1&branchId=&client_type=&region=&gender=&age_group=&income_level=&disability_status=&actype=' . $rown['id'] . '&start_date=&end_date=">' . number_format($rown['clients']) . '<span class="badge badge-pill bg-soft-success text-success me-0">
                                           (
                                       Total )</span></a>  <a href="report_client.php?filtered=1&branchId=&region=&gender=&age_group=&income_level=&disability_status=&client_type=&gender=&actype=' . $rown['id'] . '&start_date=' . date('Y-m-d') . '&end_date=' . date('Y-m-d') . '">' . number_format($rown['clients_today']) . '<span class="badge badge-pill bg-soft-danger text-danger me-0">
                                       ( Today )</span></a></span><br/>
                                               <span class="text-nowrap text-xs text-muted">' . $rown['ucode'] . ' - ' . $rown['name'] . '</span>
                                           </div>
                                       </li>
                                                        ';
                                            }
                                        } else {
                                            echo '<span class="fs-14">No Saving Products added yet</span>';
                                        }

                                        ?>


                                   </ul>
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