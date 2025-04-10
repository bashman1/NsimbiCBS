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
                       <div class="col-xl-3 col-sm-6 col-12">
                           <div class="card shadow border-0">
                               <div class="card-body">
                                   <div class="row">
                                       <div class="col">
                                           <span class="h6 font-semibold text-muted text-sm d-block mb-2">Institutions</span>
                                           <div class="h3 font-bold mb-0" id="invoices">
                                               <?php $total = $response->getAllBanks();
                                                echo number_format($total);
                                                ?>
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
                       </div>
                   </div>
               </div>


           </div>
       </div>
   </div>
   <!--**********************************
            Content body end
        ***********************************-->