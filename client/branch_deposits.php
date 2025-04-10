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
    <?php include('includes/preloader.php');?>

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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Deposits</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Branch Deposits</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>        
                                All Deposits</h4>
                              
                              
                                    <button type="button" class="btn btn-primary "><a href="create_deposit.php" style="color:#fff;">Add New Deposit</a></button>
                                    
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>ID</th>
                                                <th>Client Type</th>
                                                <th>Name</th>
                                                <th>Gender</th>
                                                <th>Status</th>
                                                <th>Contacts</th>
                                                <th>Branch</th>
                                               
                                                <th>Joining Date</th>
                                                <th>Action</th>
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
        <?php  include('includes/footer.php');?>

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
    <?php  include('includes/bottom_scripts.php');?>

    <script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: '<?=BACKEND_BASE_URL?>User/get_all_bank_clients.php?bank=<?php echo $user[0]['bankId'];?>&branch=<?php echo $user[0]['branchId'];?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                bindtoDatatable(data.data);
                // console.log(data.data);
            }
        });

    });

    function bindtoDatatable(data) {

        var table = $('#example3').dataTable({
            destroy: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            "aaData": data,
            "columns": [{
                "data": "image"
            }, {
                "data": "accno"
            }, {
                "data": "actype"
            }, {
                "data": "name"
            }, {
                "data": "gender"
            }, {
                "data": "status"
            }, {
                "data": "contact",
            },
            {
                "data": "branchName",
            },
            {
                "data": "openingdate",
            }, {
                "data": "actions",
            },
        
        ]
        })

    }
    </script>

</body>

</html>