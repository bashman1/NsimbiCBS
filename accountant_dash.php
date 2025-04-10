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
                                                               <h4 class="fs-18 font-w700 mb-0">Deposits Today</h4>
                                                               <span class="fs-14 font-w700"><a href="active_loans.php" id="active_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Withdraws Today</h4>
                                                               <span class="fs-14 font-w700"><a href="active_loans.php" id="due_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0"> Loans Repayments Today</h4>
                                                               <span class="fs-14 font-w700"><a href="report_loan_status.php" id="overdue_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Expenses Today</h4>
                                                               <span class="fs-14 font-w700"><a href="closed_loans.php" id="closed_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Loan Applications Today </h4>
                                                               <span class="fs-14 font-w700"><a href="declined_loans.php" id="declined_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Incomes Today</h4>
                                                               <span class="fs-14 font-w700"><a href="approved_loans" id="await_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Expenses Today</h4>
                                                               <span class="fs-14 font-w700"><a href="loan_applications.php" id="apply_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>

                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">
                                                           <div class="ms-3">
                                                               <span class="fs-14 font-w700"><a href="report_loan_repayments.php" class="btn btn-primary light btn-xs mb-1">View Till Sheets</a></span>
                                                           </div>
                                                           <div class="ms-3">
                                                               <span class="fs-14 font-w700"><a href="report_loan_status.php" class="btn btn-primary light btn-xs mb-1">View Day Book</a></span>
                                                           </div>

                                                       </div>
                                                   </div>
                                               </div>

                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Loan Portifolio (Institution)</h4>
                                                               <span class="fs-14 font-w700">Principal: <a href="report_credit_officers.php" id="portifolio_principal"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                               <br /><span class="fs-14 font-w700">Interest<a href="report_credit_officers.php" id="portifolio_interest"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                               <br /><span class="fs-14 font-w700">Total<a href="report_credit_officers.php" id="portifolio_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Assets Today</h4>
                                                               <span class="fs-14 font-w700"><a href="report_loan_arrears.php" id="loan_arrears_amount_credit"><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
                                                           </div>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-xl-3 col-xxl-6 col-6">
                                                   <div>
                                                       <div class="d-flex align-items-center mb-3">

                                                           <div class="ms-3">
                                                               <h4 class="fs-18 font-w700 mb-0">Cash Balances</h4>
                                                               <span class="fs-14 font-w700"><a href="" id=""><img src="images/loader.gif" alt="" class="content-loader sm"></a></span>
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