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
                       <div class="col-xl-12 col-xxl-12">
                           <div class="row">
                               <div class="col-xl-12">
                                   <div class="card">
                                       <div class="card-body">
                                           <div class="row align-items-center">
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">
                                                           <!-- <div class="d-inline-block position-relative donut-chart-sale">
                                                               <span class="donut1" data-peity='{ "fill": ["rgb(172, 57, 212)", "rgba(238, 238, 238, 1)"],   "innerRadius": 20, "radius": 10}'>5/8</span>
                                                           </div> -->
                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Deposits Today</h4>
                                                               <span class="fs-14 font-w700"><a href="day_book_report.php?filtered=1&branchId=<?= $user[0]['branchId'] ?>&transaction_type=D&authorized_by_id=<?= $user[0]['user_id'] ?>&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="deposits"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">
                                                           <!-- <div class="d-inline-block position-relative donut-chart-sale">
                                                               <span class="donut1" data-peity='{ "fill": ["rgb(64, 212, 168)", "rgba(238, 238, 238, 1)"],   "innerRadius": 20, "radius": 10}'>4/5</span>
                                                           </div> -->
                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Withdraws Today</h4>
                                                               <span class="fs-14 font-w700"><a href="day_book_report.php?filtered=1&branchId=<?= $user[0]['branchId'] ?>&transaction_type=W&authorized_by_id=<?= $user[0]['user_id'] ?>&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="withdraws"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Incomes Today</h4>
                                                               <span class="fs-14 font-w700"><a href="day_book_report.php?filtered=1&branchId=<?= $user[0]['branchId'] ?>&transaction_type=I&authorized_by_id=<?= $user[0]['user_id'] ?>&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="incomes_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Expenses Today</h4>
                                                               <span class="fs-14 font-w700"><a href="day_book_report.php?filtered=1&branchId=<?= $user[0]['branchId'] ?>&transaction_type=E&authorized_by_id=<?= $user[0]['user_id'] ?>&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="expenses_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Liabilities Today</h4>
                                                               <span class="fs-14 font-w700"><a href="day_book_report.php?filtered=1&branchId=<?= $user[0]['branchId'] ?>&transaction_type=LIA&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="liabilities_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Assets Registered Today</h4>
                                                               <span class="fs-14 font-w700"><a href="day_book_report.php?filtered=1&branchId=<?= $user[0]['branchId'] ?>&transaction_type=ASS&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="assets_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Cash Balance</h4>
                                                               <span class="fs-14 font-w700"><a href="teller_till_sheet.php" id="cash_balance"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>

                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">
                                                           <div class="ms-3">
                                                               <span class="fs-14 font-w700"><a href="day_book_report.php?filtered=1&branchId=<?= $user[0]['branchId'] ?>&transaction_type=&authorized_by_id=<?= $user[0]['user_id'] ?>&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" class="btn btn-primary light btn-xs mb-1">View Day Book</a></span>
                                                           </div>
                                                           <div class="ms-3">
                                                               <span class="fs-14 font-w700"><a href="teller_till_sheet.php" class="btn btn-primary light btn-xs mb-1">View Till Sheet</a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>

                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Share Purchases Today</h4>
                                                               <span class="fs-14 font-w700"><a href="day_book_report.php?filtered=1&branchId=<?= $user[0]['branchId'] ?>&transaction_type=ASS&authorized_by_id=&actype=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" id="shares_daily"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <!-- <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">
                                                           <div class="ms-3">
                                                               <span class="fs-14 font-w700"><a href="teller_till_sheet" class="btn btn-primary light btn-xs mb-1">View Till Sheet</a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div> -->

                                               <!-- <div class="col-xl-3 col-xxl-6 col-6">
                                           <div class="mb-3">
                                               <a href="javascript:void(0);" class="btn btn-primary btn-lg">+New Spends</a>
                                           </div>
                                       </div> -->
                                           </div>
                                       </div>
                                   </div>
                               </div>

                               <!-- 
                               <div class="col-xl-12">
                                   <div class="card">
                                       <div class="card-header d-block d-sm-flex border-0 transactions-tab">
                                           <div class="me-3">
                                               <h4 class="card-title mb-2 font-w700">Transaction History</h4>
                                               <span class="fs-12">Your Last five Transaction record</span>
                                           </div>
                                           <div class="card-tabs mt-3 mt-sm-0">
                                               <ul class="nav nav-tabs" role="tablist">
                                                   <li class="nav-item">
                                                       <a class="nav-link active" data-bs-toggle="tab" href="#monthly" role="tab">Deposits</a>
                                                   </li>
                                                   <li class="nav-item">
                                                       <a class="nav-link" data-bs-toggle="tab" href="#Weekly" role="tab">Withdraws</a>
                                                   </li>
                                                   <li class="nav-item">
                                                       <a class="nav-link" data-bs-toggle="tab" href="#Today" role="tab">Journal Entries</a>
                                                   </li>
                                               </ul>
                                           </div>
                                       </div>
                                       <div class="card-body tab-content p-0">
                                           <div class="tab-pane fade active show" id="monthly" role="tabpanel"> -->
                               <?php
                                //     $trxns = $response->getLastFiveTrxns($user[0]['user_id']);

                                //     foreach ($trxns['deposits'] as $dep) {
                                //         echo '
                                //      <div id="accordion-one" class="accordion style-1">
                                //        <div class="accordion-item">
                                //            <div class="accordion-header collapsed" data-bs-toggle="collapse" data-bs-target="#default_collapseOne1">
                                //                <div class="d-flex align-items-center">

                                //                    <div class="user-info">
                                //                        <h6 class="fs-16 font-w700 mb-0"><a href="javascript:void(0)">' . $dep['client_name'] . '</a></h6>
                                //                        <span class="fs-14">' . $dep['tid'] . '</span>
                                //                    </div>
                                //                </div>
                                //                <span>' . $dep['date_created'] . '</span>
                                //                <span>' . number_format($dep['amount']) . '</span>
                                //                <span>' . $dep['pay_method'] . '</span>
                                //                <a class="btn btn-danger light" href="javascript:void(0);">' . $dep['_status'] . '</a>
                                //                <span class="accordion-header-indicator"></span>
                                //            </div>

                                //        </div>

                                //    </div>

                                //     ';
                                //     }
                                ?>

                               <!-- </div>
                                           <div class="tab-pane fade" id="Weekly" role="tabpanel">
                                               <div id="accordion-one1" class="accordion style-1"> -->
                               <?php

                                //         foreach ($trxns['withdraws'] as $dep) {
                                //             echo '
                                //      <div id="accordion-one" class="accordion style-1">
                                //        <div class="accordion-item">
                                //            <div class="accordion-header collapsed" data-bs-toggle="collapse" data-bs-target="#default_collapseOne1">
                                //                <div class="d-flex align-items-center">

                                //                    <div class="user-info">
                                //                        <h6 class="fs-16 font-w700 mb-0"><a href="javascript:void(0)">' . $dep['name'] . '</a></h6>
                                //                        <span class="fs-14">' . $dep['tid'] . '</span>
                                //                    </div>
                                //                </div>
                                //                <span>' . $dep['date_created'] . '</span>
                                //                <span>' . number_format($dep['amount']) . '</span>
                                //                <span>' . $dep['method'] . '</span>
                                //                <a class="btn btn-danger light" href="javascript:void(0);">' . $dep['status'] . '</a>
                                //                <span class="accordion-header-indicator"></span>
                                //            </div>

                                //        </div>

                                //    </div>

                                //     ';
                                //         }
                                ?>

                               <!-- </div>
                                           </div>
                                           <div class="tab-pane fade" id="Today" role="tabpanel">
                                               <div id="accordion-one2" class="accordion style-1">



                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div> -->
                           </div>
                       </div>




                       <!-- ended -->
                   </div>
               </div>
           </div>
       </div>
   </div>
   <!--**********************************
            Content body end
        ***********************************-->