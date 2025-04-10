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

                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Accounting</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Transactions</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>        
                                All Transactions </h4>

                                <!-- <div class="btn-group" role="group"> -->
                               

                                <!-- </div> -->
                            </div>
                            <div class="card-body">
                            <div class="table-responsive">
                                    <table id="example2" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Account</th>
                                                <th>Vendor</th>
                                                <th>Entry Type</th>
                                                <th>Entered by</th>
                                                <th>Branch</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                          
                                           
                                           
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                            <th>#</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Amount ( UGX )</th>
                                                <th>Account</th>
                                                <th>Vendor</th>
                                                <th>Entry Type</th>
                                                <th>Entered by</th>
                                                <th>Branch</th>
                                            </tr>
                                        </tfoot>
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
            url: '<?=BACKEND_BASE_URL?>Bank/get_all_bank_transactions.php?branch=<?php echo $user[0]['branchId']; ?>&bank=<?php echo $user[0]['bankId'];?>',
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
                "data": "id"
            }, {
                "data": "date"
            }, {
                "data": "description"
            }, {
                "data": "amount"
            }, {
                "data": "account"
            }, {
                "data": "vendor"
            }, {
                "data": "type"
            }, {
                "data": "auth"
            }, {
                "data": "branch"
            }]
        })

    }
    </script>

</body>

</html>