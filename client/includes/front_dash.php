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

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Individuals Today</h4>
                                                               <span class="fs-14 font-w700"><a href="" id="ind_today"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Groups Today</h4>
                                                               <span class="fs-14 font-w700"><a href="" id="group_today"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Institutions Today</h4>
                                                               <span class="fs-14 font-w700"><a href="inst_today.php" id="inst_today"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Total Individuals</h4>
                                                               <span class="fs-14 font-w700"><a href="individual_clients.php" id="individuals"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Total Groups </h4>
                                                               <span class="fs-14 font-w700"><a href="group_clients.php" id="groups"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Total Institutions</h4>
                                                               <span class="fs-14 font-w700"><a href="institution_clients.php" id="institutions"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Saving Accounts</h4>
                                                               <span class="fs-14 font-w700"><a href="report_client.php" id="clients"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>

                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">
                                                           <div class="ms-3">
                                                               <span class="fs-14 font-w700"><a href="report_client.php" class="btn btn-primary light btn-xs mb-1">View Clients' Report</a></span>
                                                           </div>
                                                           <div class="ms-3">
                                                               <span class="fs-14 font-w700"><a href="report_membership_schedule.php" class="btn btn-primary light btn-xs mb-1">View Membership Schedule</a></span>
                                                           </div>
                                                           <div class="ms-3">
                                                               <span class="fs-14 font-w700"><a href="birth_day.php" class="btn btn-primary light btn-xs mb-1">View Birth-Days Today -
                                                                        <a href="birth_day.php" id="birth_days"><img src="images/loader.gif" alt="" class="content-loader sm"></a>
                                                                   </a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>

                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Mobile Banking</h4>
                                                               <span class="fs-14 font-w700"><a href="mobile_banking_reports.php" id="mobile_banking"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">SMS Banking</h4>
                                                               <span class="fs-14 font-w700"><a href="sms_tab" id="sms"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>

                                           </div>
                                       </div>
                                   </div>
                               </div>



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