<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();

$title = 'ADD GUARANTOR';
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

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Loan Guarantor Form
                                </h4>

                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" enctype="multipart/form-data" action="https://eaoug.org/add_guarantor.php">

                                        <div class="mb-3">
                                            <label class="text-label form-label">Is the Guarantor a Client*</label>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">


                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="is_client" value="1" id="1" onClick="setDown()" checked>
                                                    <label class="form-check-label">
                                                        Yes
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="is_client" value="0" id="0" onClick="setUp()">
                                                    <label class="form-check-label">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br />



                                        <div class="mb-3" id="member">
                                            <label class="text-label form-label">Search for the Guarantor's Account
                                                *</label>

                                            <select id="clientsselect" class="form-control select2x" name="mid">

                                            </select>
                                        </div>
                                        <div class="mb-3" id="non_member" style="display:none; ">
                                            <label class="text-label form-label">Enter the Guarantor's Details e.g Names , Contact, Address
                                                *</label>
                                            <textarea name="non_member" class="form-control input-rounded" cols="5"></textarea>
                                        </div>
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="lid" value="<?php echo $_GET['id']; ?>">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="uid" value="<?php echo $user[0]['userId']; ?>">



                                        <div class="mb-3">
                                            <label class="text-label form-label">Attachment of the Guarantorship Form or any record of Proof </label>

                                            <input type="file" class="form-control input-rounded" placeholder="" name="attach">
                                        </div>


                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Add
                                            Guarantor</button>
                                        <!-- </div> -->

                                    </form>
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
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>
        <script>
            function setUp() {
                var x = document.getElementById("is_client");
                var y = document.getElementById("non_member");
                var z = document.getElementById("member");
                y.style.display = "block";

                z.style.display = "none";
            }


            function setDown() {
                var x = document.getElementById("is_client");
                var y = document.getElementById("non_member");
                var z = document.getElementById("member");
                z.style.display = "block";

                y.style.display = "none";
            }
        </script>
        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>
        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });


            $(document).ready(function() {
                $("select.select2x").select2({
                    ajax: {
                        url: "<?php echo BACKEND_BASE_URL ?>User/get_all_bank_clients_search.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>",
                        dataType: 'json',
                        data: (params) => {
                            return {
                                q: params.term,
                            }
                        },

                        processResults: (data, params) => {
                            const results = data.data.map(item => {
                                return {
                                    id: item.userId,
                                    text: item.accno + ' : ' + item.name + ' - UGX ' + item.tot_balance + '  - Branch: ' + item.branchName,
                                };
                            });
                            return {
                                results: results,
                            }
                        },
                    },
                });
            })
        </script>

</body>

</html>