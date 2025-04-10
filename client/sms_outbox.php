<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
// if (!$permissions->IsBankStuff()) {
//     return $permissions->isNotPermitted(true);
// }
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

$branches = $response->getBankBranches($user[0]['bankId']);
$banks = $response->getAllBanksList();
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


                <!-- <div class="col-12">  -->
                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>

                        <form class="ajax_results_form" method="post">
                            <div class="row">
                                <?php
                                if ($user[0]['roleId'] == 'becedad5-8159-4543-911f-da4805e29f77') {
                                    echo '
                                            <div class="col-lg-6 mb-2">
                                            <div class="mb-3">
                                                <label class="text-label form-label">Associated Bank *</label>
                                                <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="bank">
                                            
                                                    ';
                                    if ($banks !== '') {
                                        foreach ($banks as $row) {
                                            echo '
                                                <option value="' . $row['bid'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                                                
                                                ';
                                        }
                                    } else {
                                        echo '
                                                <option readonly>No Banks Added yet</option>
                                                ';
                                    }

                                    echo
                                    '
                                            
                                                </select>
                                            </div>
                                            </div>
                                            
                                            ';
                                }
                                ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-label form-label">Branch *</label>
                                        <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="branch" style="display: none;" required>
                                            <?php

                                            if ($user[0]['branchId']) {
                                                echo '
                                <option value="' . $user[0]['branchId'] . '" selected>' . $user[0]['branchName'] . '</option>
                                ';
                                            } else {
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        echo '
                                  <option value="' . $row['id'] . '">' . $row['name'] . '</option>
                                  
                                  ';
                                                    }
                                                }
                                            }

                                            ?>

                                        </select>




                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label class="text-label form-label">Unique Key *</label>

                                        <select name="method" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;" required>
                                            <option value="all" selected>--- All ---</option>
                                            <?php
                                            $keys = $response->getSMSUniqueKeys($user[0]['bankId'], $user[0]['branchId']);
                                            if ($keys != '') {
                                                foreach ($keys as $row) {
                                                    echo '
                                                    <option value="' . $row['name'] . '">' . $row['name'] . '</option>
                                                    ';
                                                }
                                            } else {
                                                echo '<option value="0">--- No Keys found ---</option>';
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>


                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Start
                                            Date *</label>
                                        <input type="date" class="form-control" name="from_date" value="<?php echo date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">End
                                            Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?php echo date('Y-m-d'); ?>" id="exampleInputEmail4" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch
                                            Entries</button>
                                    </div>
                                </div>

                            </div>

                        </form>


                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            SMS OutBox
                        </h4>

                        <?php
                        if (isset($_GET['success'])) {
                            echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                            // unset($_SESSION['success']);
                        }
                        if (isset($_GET['error'])) {
                            echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                        }

                        // unset($_SESSION['error']);

                        ?>
                        <!-- <div class="btn-group" role="group"> -->


                        <!-- </div> -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Contact</th>
                                        <th>Key</th>
                                        <th>Body</th>
                                        <th>Status</th>
                                        <th>Bank</th>
                                        <th>Branch</th>
                                        <th>Sender ID</th>
                                        <th>Reason</th>
                                        <th>Auto-Generated</th>
                                        <th>Date Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>




                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>



            <!-- </div>
            </div> -->
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
                url: '<?=BACKEND_BASE_URL?>Bank/get_all_sms_outbox.php?branch=<?php echo $user[0]['branchId']; ?>&bank=<?php echo $user[0]['bankId']; ?>',
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
                        "data": "id"
                    }, {
                        "data": "phone"
                    }, {
                        "data": "key"
                    }, {
                        "data": "body"
                    },
                    {
                        "data": "status"
                    }, {
                        "data": "bname"
                    }, {
                        "data": "branchname"
                    }, {
                        "data": "senderid"
                    }, {
                        "data": "reason"
                    },
                    {
                        "data": "gen"
                    },
                    {
                        "data": "dateCreated"
                    }, {
                        "data": "actions",
                    }
                ]
            })

        }
    </script>

</body>

</html>