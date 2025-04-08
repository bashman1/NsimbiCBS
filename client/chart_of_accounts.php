<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}
require_once('includes/head_tag.php');
?>


<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php include('includes/preloader.php'); ?>

    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php
        include('includes/nav_bar.php');
        include('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

               
                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>        
                                Chart of Accounts </h4>

                            </div>
                            <div class="card-body">
                            <div class="table-responsive">
                                    <table id="example2" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th>Branch</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                          
                                           
                                           
                                            
                                        </tbody>
                                        
                                    </table>
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
        

        <!--**********************************
            Footer start
        ***********************************-->
        <?php include('includes/footer.php'); ?>

        <!--**********************************
            Footer end
        ***********************************-->

        <!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <?php include('includes/bottom_scripts.php'); ?>

    <script type="text/javascript">
    $(document).ready(function() {

        $.ajax({
            url: '<?=BACKEND_BASE_URL?>Bank/get_all_bank_accounts.php?branch=<?php echo $user[0]['branchId']; ?>&bank=<?php echo $user[0]['bankId'];?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                bindtoDatatable(data.data);
                // console.log(data.data);
            }
        });

    });

    function bindtoDatatable(data) {

        var table = $('#example2').dataTable({
            destroy: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },

            "aaData": data,

            "columns": [{
                "data": "count"
            }, {
                "data": "name"
            }, {
                "data": "type"
            }, {
                "data": "description"
            }, {
                "data": "branch"
            }]
        })

    }
    </script>

</body>

</html>