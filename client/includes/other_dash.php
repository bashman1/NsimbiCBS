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

                   </div>
               </div>


           </div>
       </div>
   </div>
   <!--**********************************
            Content body end
        ***********************************-->