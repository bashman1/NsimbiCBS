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
              <div class="alert alert-primary solid alert-square">
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
                       <div class="card">
                           <div class="card-body pb-3">
                               <div class="row align-items-center">
                                   <div class="col-xl-3 mb-3">
                                       <p class="mb-2 fs-16 font-w600"><?= $user[0]['firstName'] ? $user[0]['firstName'] . ' ' . $user[0]['lastName'] : $user[0]['shared_name']  ?></p>
                                       <h4 class="mb-0 fs-18 font-w700"><?= $user[0]['acno'] ?></h4>
                                   </div>
                                   <div class="col-xl-9 d-flex flex-wrap justify-content-between align-items-center">
                                       <div class="d-flex me-3 mb-3 ms-2 align-items-start payment">
                                           <div>
                                               <p class="mb-2 fs-16 font-w600">Shares </p>
                                               <h4 class="mb-0 fs-18 font-w700"> <?= $user[0]['shares'] ?></h4>
                                           </div>
                                       </div>
                                       <div class="d-flex me-3 mb-3 ms-2 align-items-start payment">
                                           <div>
                                               <p class="mb-2 fs-16 font-w600">Share Amount </p>
                                               <h4 class="mb-0 fs-18 font-w700"> <?= $user[0]['share_amount'] ?></h4>
                                           </div>
                                       </div>
                                       <div class="d-flex me-3 mb-3 ms-2 align-items-start payment">
                                           <!-- <i class="fas fa-phone-alt me-4 mt-2 scale5"></i> -->
                                           <div>
                                               <p class="mb-2 fs-16 font-w600">Branch</p>
                                               <h4 class="mb-0 fs-18 font-w700"><?= $user[0]['branchName'] ?></h4>
                                           </div>
                                       </div>
                                       <div class="d-flex me-3 mb-3 ms-2 align-items-start payment">
                                           <!-- <i class="fas fa-envelope scale5 me-4 mt-2"></i> -->
                                           <div>
                                               <p class="mb-2 fs-16 font-w600">A/C Type</p>
                                               <h4 class="mb-0 fs-18 font-w700"><?= strtoupper($user[0]['client_type']) ?></h4>
                                           </div>
                                       </div>
                                       <div class="d-flex mb-3">
                                           <a target="_blank" class="btn btn-primary light btn-xs mb-1" href="client_portal_statement.php?id=<?= $user[0]['userId'] ?>&cid=<?= $_GET['cid'] ?>"><i class="las la-print me-3 scale5"></i>View Statement</a>
                                       </div>
                                       <?=
                                        $user[0]['fees'] == 1 ? ' <div class="d-flex mb-3">   <a target="_blank" href="fees_portal_statement.php?id=' . $user[0]['userId'] . '&cid=' . $_GET['cid'] . '" class="btn btn-primary light btn-xs mb-1"><i class="las la-download scale5 me-3"></i>Fees Payments</a></div>' : ''


                                        ?>


                                   </div>
                               </div>
                           </div>
                           <div class="card-body pb-3 transaction-details d-flex flex-wrap justify-content-between align-items-center">

                               <div class="amount-bx mb-3 border">
                                   <!-- <i class="fas fa-dollar-sign"></i> -->
                                   <div>
                                       <p class="mb-1">Balance </p>
                                       <h6 class="mb-0 text-primary">UGX: <?= number_format($user[0]['acbalance'] + $user[0]['loan_wallet']) ?></h6>
                                   </div>

                               </div>

                               <div class="me-3 mb-3">
                                   <!-- <p class="mb-2">Deposit</p> -->
                                   <!-- <h4 class="mb-0">MasterCard 404</h4> -->
                                   <a class="btn btn-primary light btn-xs mb-1" data-bs-toggle="modal" data-bs-target="#deposit_modal">Make Deposit</a>

                               </div>
                               <div class="me-3 mb-3">
                                   <!-- <p class="mb-2">Invoice Date</p>
                                   <h4 class="mb-0">April 29, 2020</h4> -->
                                   <a class="btn btn-primary light btn-xs mb-1" data-bs-toggle="modal" data-bs-target="#withdraw_modal">Make Withdraw</a>
                               </div>
                               <div class="me-3 mb-3">
                                   <a class="btn btn-primary light btn-xs mb-1" data-bs-toggle="modal" data-bs-target="#shares_modal"></i>Buy Shares</a>
                               </div>
                               <div class="me-3 mb-3">
                                   <a class="btn btn-primary light btn-xs mb-1" href="javascript:void(0);" onClick="openSheet()"></i>Loans</a>
                               </div>
                               <div class="me-3 mb-3">
                                   <a class="btn btn-primary light btn-xs mb-1" data-bs-toggle="modal" data-bs-target="#stmtsModal"></i>Donations</a>
                                   <!-- Modal -->
                                   <div class="modal fade" id="stmtsModal">
                                       <div class="modal-dialog modal-dialog-centered" role="document">
                                           <div class="modal-content">
                                               <div class="modal-header">
                                                   <h5 class="modal-title">Select an Option to Continue</h5>
                                                   <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                   </button>
                                               </div>
                                               <div class="modal-body">
                                                   <div class="row">

                                                       <a href="member_statement_range.php?id=<?= $user[0]['userId']; ?>" class="list-group-item load_via_ajax">Your Donation Statement</a>
                                                       <a href="saving_statement.php?id=<?= $user[0]['userId']; ?>" class="list-group-item load_via_ajax"> Donate Now</a>
                                                       <a href="" class="list-group-item load_via_ajax">View Ongoing Charity Activities</a>
                                                       <a href="" class="list-group-item load_via_ajax"> Request a call Back</a>


                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="me-3 mb-3">
                                   <!-- <p class="mb-2">Due Date</p>
                                   <h4 class="mb-0">June 5, 2020</h4> -->
                                   <?=
                                    $user[0]['fees'] == 1 ? '
                                   <a class="btn btn-primary light btn-xs mb-1" href="javascript:void(0);" onclick="openSheet()">Pay school Fees</a>
                                   ' : ''
                                    ?>
                               </div>


                               <!-- <div class="user-bx-2 me-3 mb-3">
                                   <!-- <img src="images/profile/pic2.jpg" class="rounded" alt=""> -->
                               <!-- <div> -->
                               <!-- <h3 class="fs-20 font-w700">Richard Michael</h3> -->
                               <!-- <span class="font-w400">@richardmichael</span> -->
                               <!-- </div> -->
                               <!-- </div>  -->

                           </div>


                       </div>

                       <!-- start card for trxns -->

                       <div class="card">
                           <div class="card-header d-block d-sm-flex border-0 flex-wrap transactions-tab">
                               <div class="me-3 mb-3">
                                   <h4 class="card-title mb-2">Latest Transactions</h4>
                                   <!-- <span class="fs-12"></span> -->
                               </div>
                               <div class="card-tabs mt-3 mt-sm-0 mb-3 ">
                                   <ul class="nav nav-tabs" role="tablist">
                                       <li class="nav-item">
                                           <a class="nav-link active" data-bs-toggle="tab" href="#today" role="tab">Today</a>
                                       </li>
                                       <li class="nav-item">
                                           <a class="nav-link" data-bs-toggle="tab" href="#week" role="tab">Week</a>
                                       </li>
                                       <li class="nav-item">
                                           <a class="nav-link" data-bs-toggle="tab" href="#month" role="tab">Month</a>
                                       </li>
                                   </ul>
                               </div>
                           </div>
                           <div class="card-body tab-content p-0">
                               <div class="tab-pane active show fade" id="today" role="tabpanel">
                                   <div class="table-responsive">
                                       <table class="table table-responsive-md card-table transactions-table">
                                           <tbody>

                                               <?php

                                                $trxns = $response->getClientTrxns($user[0]['userId'], 'today');

                                                if ($trxns != '') {

                                                    echo '
                                                      <tr>
                                                 <td>
                                                    TRXN DATE
                                                   </td>
                                                   <td>
                                                      DESCRIPTION
                                                   </td>
                                                   <td>
                                                      DEBIT
                                                   </td>
                                                   <td>
                                                   CREDIT
                                                   </td>
                                               </tr>
                                                    
                                                    ';

                                                    foreach ($trxns as $trxn) {
                                                        echo '
                                                            <tr>
                                                  
                                                   <td>
                                                       <h6 class="fs-10 font-w200 mb-0"><a href="javascript:void(0);" class="text-black">' . normal_date_short($trxn['trxn_date']) . '</a></h6>
                                                       <span class="fs-10"></span>
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-10 text-black font-w200 mb-0">' . $trxn['descrip'] . '</h6>
                                                       
                                                   </td>
                                                   <td><span class="fs-10 text-black font-w200">' . $trxn['dr_amount'] . '</span></td>
                                                    <td><span class="fs-10 text-black font-w200">' . $trxn['cr_amount'] . '</span></td>
                                                   
                                               </tr>
                                                        
                                                        ';
                                                    }
                                                }

                                                ?>



                                           </tbody>
                                       </table>
                                   </div>
                               </div>
                               <div class="tab-pane" id="week" role="tabpanel">
                                   <div class="table-responsive">
                                       <table class="table table-responsive-md card-table transactions-table">
                                           <tbody>
                                               <?php

                                                $trxns = $response->getClientTrxns($user[0]['userId'], 'week');

                                                if ($trxns != '') {
                                                    echo '
                                                     <tr>
                                                 <td>
                                                    TRXN DATE
                                                   </td>
                                                   <td>
                                                      DESCRIPTION
                                                   </td>
                                                   <td>
                                                      DEBIT
                                                   </td>
                                                   <td>
                                                   CREDIT
                                                   </td>
                                               </tr>
                                                    
                                                    ';
                                                    foreach ($trxns as $trxn) {
                                                        echo '
                                                            <tr>
                                                   
                                                   <td>
                                                       <h6 class="fs-10 font-w200 mb-0"><a href="javascript:void(0);" class="text-black">' . normal_date_short($trxn['trxn_date']) . '</a></h6>
                                                       <span class="fs-10"></span>
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-10 text-black font-w200 mb-0">' . $trxn['descrip'] . '</h6>
                                                     
                                                   </td>
                                                     <td><span class="fs-10 text-black font-w200">' . $trxn['dr_amount'] . '</span></td>
                                                    <td><span class="fs-10 text-black font-w200">' . $trxn['cr_amount'] . '</span></td>
                                                 
                                               </tr>
                                                        
                                                        ';
                                                    }
                                                }

                                                ?>


                                           </tbody>
                                       </table>
                                   </div>
                               </div>
                               <div class="tab-pane" id="month" role="tabpanel">
                                   <div class="table-responsive">
                                       <table class="table table-responsive-md card-table transactions-table">
                                           <tbody>
                                               <?php

                                                $trxns = $response->getClientTrxns($user[0]['userId'], 'month');

                                                if ($trxns != '') {
                                                    echo '
                                                      <tr>
                                                   <td>
                                                    TRXN DATE
                                                   </td>
                                                   <td>
                                                      DESCRIPTION
                                                   </td>
                                                   <td>
                                                      DEBIT
                                                   </td>
                                                   <td>
                                                   CREDIT
                                                   </td>
                                               </tr>
                                                    
                                                    ';
                                                    foreach ($trxns as $trxn) {
                                                        echo '
                                                            <tr>
                                                   
                                                   <td>
                                                       <h6 class="fs-10 font-w200 mb-0"><a href="javascript:void(0);" class="text-black">' . normal_date_short($trxn['trxn_date']) . '</a></h6>
                                                       <span class="fs-10"></span>
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-10 text-black font-w200 mb-0">' . $trxn['descrip'] . '</h6>
                                                       
                                                   </td>
                                                   <td><span class="fs-10 text-black font-w200">' . $trxn['dr_amount'] . '</span></td>
                                                    <td><span class="fs-10 text-black font-w200">' . $trxn['cr_amount'] . '</span></td>
                                                   
                                               </tr>
                                                        
                                                        ';
                                                    }
                                                }

                                                ?>


                                           </tbody>
                                       </table>
                                   </div>
                               </div>
                           </div>
                       </div>


                       <!-- end trxn card -->

                       <?php
                        if ($user[0]['fees']) :

                        ?>
                           <div class="card">
                               <div class="card-header d-block d-sm-flex border-0 flex-wrap transactions-tab">
                                   <div class="me-3 mb-3">
                                       <h4 class="card-title mb-2">Latest School Fees Payments</h4>
                                       <!-- <span class="fs-12"></span> -->
                                   </div>
                                   <div class="card-tabs mt-3 mt-sm-0 mb-3 ">
                                       <ul class="nav nav-tabs" role="tablist">
                                           <li class="nav-item">
                                               <a class="nav-link active" data-bs-toggle="tab" href="#todayn" role="tab">Today</a>
                                           </li>
                                           <li class="nav-item">
                                               <a class="nav-link" data-bs-toggle="tab" href="#weekn" role="tab">Week</a>
                                           </li>
                                           <li class="nav-item">
                                               <a class="nav-link" data-bs-toggle="tab" href="#monthn" role="tab">Month</a>
                                           </li>
                                       </ul>
                                   </div>
                               </div>
                               <div class="card-body tab-content p-0">
                                   <div class="tab-pane active show fade" id="todayn" role="tabpanel">
                                       <div class="table-responsive">
                                           <table class="table table-responsive-md card-table transactions-table">
                                               <tbody>
                                                   <?php

                                                    $trxns = $response->getClientFeesTrxns($user[0]['userId'], 'today');

                                                    if ($trxns != '') {
                                                        foreach ($trxns as $trxn) {
                                                            echo '
                                                            <tr>
                                                   <td>
' . $trxn['tid'] . '
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-16 font-w600 mb-0"><a href="javascript:void(0);" class="text-black">' . normal_date_short($trxn['trxn_date']) . '</a></h6>
                                                       <span class="fs-14"></span>
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-16 text-black font-w600 mb-0">' . $trxn['descrip'] . '</h6>
                                                   </td>
                                                   <td><span class="fs-16 text-black font-w600">' . number_format($trxn['amount']) . '</span></td>
                                                   <td>' . $trxn['status'] . '</td>
                                               </tr>
                                                        
                                                        ';
                                                        }
                                                    }

                                                    ?>


                                               </tbody>
                                           </table>
                                       </div>
                                   </div>
                                   <div class="tab-pane" id="weekn" role="tabpanel">
                                       <div class="table-responsive">
                                           <table class="table table-responsive-md card-table transactions-table">
                                               <tbody>

                                                   <?php

                                                    $trxns = $response->getClientFeesTrxns($user[0]['userId'], 'week');

                                                    if ($trxns != '') {
                                                        foreach ($trxns as $trxn) {
                                                            echo '
                                                            <tr>
                                                   <td>
' . $trxn['tid'] . '
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-16 font-w600 mb-0"><a href="javascript:void(0);" class="text-black">' . normal_date_short($trxn['trxn_date']) . '</a></h6>
                                                       <span class="fs-14"></span>
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-16 text-black font-w600 mb-0">' . $trxn['descrip'] . '</h6>
                                                       
                                                   </td>
                                                   <td><span class="fs-16 text-black font-w600">' . number_format($trxn['amount']) . '</span></td>
                                                   <td>' . $trxn['status'] . '</td>
                                               </tr>
                                                        
                                                        ';
                                                        }
                                                    }

                                                    ?>

                                               </tbody>
                                           </table>
                                       </div>
                                   </div>
                                   <div class="tab-pane" id="monthn" role="tabpanel">
                                       <div class="table-responsive">
                                           <table class="table table-responsive-md card-table transactions-table">
                                               <tbody>

                                                   <?php

                                                    $trxns = $response->getClientFeesTrxns($user[0]['userId'], 'month');

                                                    if ($trxns != '') {
                                                        foreach ($trxns as $trxn) {
                                                            echo '
                                                            <tr>
                                                   <td>
' . $trxn['tid'] . '
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-16 font-w600 mb-0"><a href="javascript:void(0);" class="text-black">' . normal_date_short($trxn['trxn_date']) . '</a></h6>
                                                       <span class="fs-14"></span>
                                                   </td>
                                                   <td>
                                                       <h6 class="fs-16 text-black font-w600 mb-0">' . $trxn['descrip'] . '</h6>
                                                       
                                                   </td>
                                                   <td><span class="fs-16 text-black font-w600">' . number_format($trxn['amount']) . '</span></td>
                                                   <td>' . $trxn['status'] . '</td>
                                               </tr>
                                                        
                                                        ';
                                                        }
                                                    }

                                                    ?>

                                               </tbody>
                                           </table>
                                       </div>
                                   </div>
                               </div>
                           </div>


                       <?php endif; ?>



                   </div>
               </div>


           </div>
       </div>
   </div>
   <!--**********************************
            Content body end
        ***********************************-->
   <div class="modal fade" id="deposit_modal">
       <div class="modal-dialog" role="document">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title">Deposit Form</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal">
                   </button>
               </div>
               <div class="modal-body">
                   <form class="custom-form" id="add_new_account_form" data-reload-page="0" data-confirm-action="1">
                       <div class="row">
                           <input type="hidden" value="<?= $user[0]['userId'] ?>" name="cid" id="cid_dep" />
                           <div class="col-md-12">
                               <div class="form-floating">
                                   <input type="text" value="0" name="amount" min="0" class="form-control" required id="amount_deposit">
                                   <label for="amount">Amount</label>
                               </div>
                           </div>
                           <div class="col-md-12 mt-4">
                               <div class="form-floating">
                                   <input type="text" name="name" class="form-control" required value="<?= $user[0]['firstName'] . ' ' . $user[0]['lastName'] . ' ' . $user[0]['shared_name'] ?>" id="name_dep">
                                   <label for="name">Name</label>
                               </div>
                           </div>
                           <div class="col-md-12 mt-4">
                               <div class="form-floating">
                                   <input type="text" name="email" class="form-control" required value="<?= $user[0]['email'] ?>" id="email_dep">
                                   <label for="email">Email</label>
                               </div>
                           </div>
                           <div class="col-md-12 mt-4">
                               <div class="form-floating">
                                   <input type="text" name="phone" class="form-control" required value="<?= $user[0]['primaryCellPhone'] ?>" id="phone_dep">
                                   <label for="phone">Phone Number</label>
                               </div>
                           </div>
                           <div class="col-md-12 mt-4">
                               <label class="text-label form-label">Select your Prefered Payment Method *</label>
                               <!-- <select id="journalacc" class="form-control" name="pay_meth" required>
                                   <option> Select Payment Method</option>
                                   <option value="mm_ug" selected>Mobile Money Uganda (Airtel / MTN)</option>
                                   <option value="other">Others</option>

                               </select> -->
                               <div class="row">
                                   <div class="form-check">
                                       <input class="form-check-input" type="radio" name="pay_meth" value="mm_ug">
                                       <label class="form-check-label">
                                           Mobile Money Uganda (Airtel / MTN)
                                       </label>
                                   </div>
                                   <div class="form-check">
                                       <input class="form-check-input" type="radio" name="pay_meth" value="other" checked="">
                                       <label class="form-check-label">
                                           Others
                                       </label>
                                   </div>
                               </div>
                           </div>
                           <div class="col-md-4">
                               <div class="form-group">
                                   <label class="text-label form-label"> </label>
                                   <button class="btn btn-primary form-control" id="start-payment-button" onClick="makePayment()">Proceed</button>
                               </div>
                           </div>

                       </div>
                   </form>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
               </div>
           </div>
       </div>
   </div>

   <div class="modal fade" id="withdraw_modal">
       <div class="modal-dialog" role="document">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title">Withdraw Form</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal">
                   </button>
               </div>
               <div class="modal-body">
                   <form class="custom-form">
                       <div class="row">
                           <input type="hidden" value="<?= $user[0]['userId'] ?>" name="cid" />
                           <div class="col-md-12">
                               <div class="form-floating">
                                   <input type="text" value="0" name="amount" min="0" class="form-control comma_separated" required data-type="amount" id="amountw">
                                   <label for="amount">Amount</label>
                               </div>
                           </div>
                           <div class="col-md-12 mt-4">
                               <div class="form-floating">
                                   <input type="text" name="name" class="form-control" required value="<?= $user[0]['firstName'] . ' ' . $user[0]['lastName'] . ' ' . $user[0]['shared_name'] ?>" id="namew">
                                   <label for="name">Name</label>
                               </div>
                           </div>
                           <div class="col-md-12 mt-4">
                               <div class="form-floating">
                                   <input type="text" name="email" class="form-control" required value="<?= $user[0]['email'] ?>" id="emailw">
                                   <label for="email">Email</label>
                               </div>
                           </div>
                           <div class="col-md-12 mt-4">
                               <div class="form-floating">
                                   <input type="text" name="phone" class="form-control" required value="<?= $user[0]['primaryCellPhone'] ?>" id="phonew">
                                   <label for="phone">Phone Number</label>
                               </div>
                           </div>
                           <div class="col-md-12 mt-4">
                               <label class="text-label form-label">Select your Prefered Payment Method *</label>

                               <div class="row">
                                   <div class="form-check">
                                       <input class="form-check-input" type="radio" name="pay_meth" value="mm_ug">
                                       <label class="form-check-label">
                                           Mobile Money Uganda (Airtel / MTN)
                                       </label>
                                   </div>

                               </div>
                           </div>
                           <div class="col-md-4">
                               <div class="form-group">
                                   <label class="text-label form-label"> </label>
                                   <button class="btn btn-primary form-control" id="start-payment-buttonw" onClick="makeWithdraw()">Proceed</button>
                               </div>
                           </div>

                       </div>
                   </form>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
               </div>
           </div>
       </div>
   </div>

   <div class="modal fade" id="shares_modal">
       <div class="modal-dialog" role="document">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title">Share Purchase Form</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal">
                   </button>
               </div>
               <div class="modal-body">
                   <form class="custom-form">
                       <div class="row">
                           <input type="hidden" value="<?= $user[0]['userId'] ?>" name="cid" />
                           <div class="col-md-12 mt-4">
                               <div class="form-floating">
                                   <input type="text" name="share_value" class="form-control" required value="20000" id="share_value" disabled>
                                   <label for="name">Current Share Value</label>
                               </div>
                           </div>
                           <div class="col-md-12">

                               <div class="form-floating">
                                   <input type="text" value="0" name="shares" min="0" class="form-control" required id="shares">
                                   <label for="shares">Number of Shares</label>
                               </div>
                           </div>


                           <div class="col-md-4">
                               <div class="form-group">
                                   <label class="text-label form-label"> </label>
                                   <button class="btn btn-primary form-control" id="start-payment-buttons" onClick="sharePurchase()">Purchase</button>
                               </div>
                           </div>

                       </div>
                   </form>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
               </div>
           </div>
       </div>
   </div>



   <script>
       function makePayment() {
           // console.log(document.getElementById("journalacc").value);
           let amount = document.getElementById("amount_deposit").value;
           let cid = document.getElementById("cid_dep").value;
           let email = document.getElementById("email_dep").value;
           let phone = document.getElementById("phone_dep").value;
           let name = document.getElementById("name_dep").value;
           let pay_meth = document.getElementById("journalacc").value;
           let tid = "titanic-48981487343MDI0NzM09";
           if (pay_meth == 'other') {
               FlutterwaveCheckout({
                   public_key: "FLWPUBK-6cce6bab6452833191c4672880e3b3e0-X",
                   tx_ref: tid,
                   amount: amount,
                   currency: "UGX",
                   //    payment_options: "card, banktransfer, ussd", 
                   redirect_url: `https://app.ucscucbs.net/backend/api/Accounting/flutter_wave_call_back.php?id=${cid}&amount=${amount}&name=${name}&phone=${phone}&email=${email}&cid=<?= $_GET['cid'] ?>&tid=${tid}`,
                   meta: {
                       consumer_id: cid,
                       consumer_mac: "92a3-912ba-1192a",
                   },
                   customer: {
                       email: email,
                       phone_number: phone,
                       name: name,
                   },
                   customizations: {
                       title: "UCSCU CBS",
                       description: "Online Deposit Form",
                       logo: "https://app.ucscucbs.net/client/images/ucscucbs.png",
                   },
               });
           } else {

           }

       }
   </script>