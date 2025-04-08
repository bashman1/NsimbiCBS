<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
require_once('includes/functions.php');
include('../backend/models/User.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

if (isset($_POST['submit'])){

    $uploads = uploadAttachments($_POST);

    var_dump($uploads);
    exit;
}




if (isset($_GET['success'])) {
    $pass_name = $_GET['pass_name'];
    $other_name = $_GET['other_name'];
    $sign_name = $_GET['sign_name'];
    $fing_name = $_GET['fing_name'];

    $uid = $_GET['uid'];
    $cid = $_GET['cid'];
    $res = $response->setIndividualAttachments($uid, $other_name, $pass_name, $sign_name, $fing_name);
    // update user to set attachments
    if ($res) {
        setSessionMessage(true, 'Attachments Uploaded Successfully!');
        header('Location: individual_clients.php');
    } else {
        setSessionMessage(false, 'Attachments Upload failed! Try again to upload attachments');
        header('Location: individual_clients_attachments.php?id=' . $cid);
    }
}

$title = 'INDIVIDUAL ATTACHMENTS';

require_once('includes/head_tag.php');

$details = $response->getClientDetailswithCID($_GET['id'])[0];
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
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Client Attachments
                                </h4>
                                <?php
                                if (isset($_GET['success#wizard_confirm'])) {
                                    echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                                    // unset($_SESSION['success']);
                                }
                                if (isset($_GET['error#wizard_confirm'])) {
                                    echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                                }

                                ?>
                            </div>
                            <div class="card-body">
                                <!-- <div id="smartwizard" class="form-wizard order-create"> -->
                                <!-- <ul class="nav nav-wizard"> -->
                                <!-- <li><a class="nav-link" href="#wizard_Payment">
                                                <span>Attachments</span>
                                            </a></li> -->


                                <!-- </ul> -->
                                <p class="text-muted mb-3">
                                    Using the form below, you can add / update client's account related attachments
                                </p>

                                <hr class="hr-dashed">

                                <div class="btc-price">
                                    <p class="text-muted mb-3">Client's Details</p>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <span class="text-muted">Names</span>
                                            <h6 class="mt-0">
                                                <?= @$details['name'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">A/C No.</span>
                                            <h6 class="mt-0">
                                                <?= @$details['accno'] ?>
                                            </h6>
                                        </div>

                                        <div class="col-lg-2">
                                            <span class="text-muted">A/C - Wallet - Balance (UGX)</span>
                                            <h6 class="mt-0">
                                                <?= number_format($details['accbalance'] ?? 0) ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Contacts</span>
                                            <h6 class="mt-0">
                                                <?= @$details['contact'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="text-muted">Branch</span>
                                            <h6 class="mt-0">
                                                <?= @$details['branchName'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-6 text-danger">
                                            <span>Address</span>
                                            <h6 class="mt-0">
                                                <?= @$details['address'] ?>
                                            </h6>
                                        </div>

                                    </div>

                                    <hr class="hr-dashed">

                                </div><br />
                                <!-- <form method="POST" enctype="multipart/form-data" action="https://eaoug.org/client_profile_page.php"> -->
                                    <form method="POST" enctype="multipart/form-data">

                                    <input type="hidden" name="cid" value="<?= @$_GET['id'] ?>">
                                    <input type="hidden" name="uid" value="<?= @$details['userId'] ?>">
                                    <input type="hidden" name="sign_orig" value="<?= @$details['sign'] ?>">
                                    <input type="hidden" name="pass_orig" value="<?= @$details['image']  ?>">
                                    <input type="hidden" name="other_orig" value="<?= @$details['otherattach']  ?>">
                                    <input type="hidden" name="fing_orig" value="<?= @$details['fingerprint']  ?>">

                                    <div class="tab-content">
                                        <!-- <div id="wizard_Payment" class="tab-pane" role="tabpanel"> -->
                                        <h4 class="card-title " style="color:#005a4b;">Attachments & Others</h4>
                                        <div class="row ">
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Client's Passport-Sized
                                                        Photo</label>
                                                    <!-- <input type="file" name="photo" accept="image/*" class="form-control" placeholder=""> -->
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">


                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="is_client" value="1" id="1" onClick="setDown()" checked required>
                                                        <label class="form-check-label">
                                                            Upload Photo from your Computer
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="is_client" value="0" id="0" onclick="setUp()" required>
                                                        <label class="form-check-label">
                                                            Take Photo from Camera
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="is_client" value="2" id="2" onclick="setUpn()" required>
                                                        <label class="form-check-label">
                                                            Skip
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br /><br />
                                            <div class="col-lg-12 mb-3" id="upload">
                                                <div class="mb-3">

                                                    <input type="file" class="form-control" name="photo" accept="image/*">
                                                    <input type="hidden" name="captured_image_data_2" id="captured_image_data_2">

                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-3" id="photo" style="display: none;">
                                                <div class="mb-3">
                                                    <a href="javascript:void(0);" class="btn btn-primary light me-1 px-3" data-bs-toggle="modal" data-bs-target="#photoModal" id="accesscamera"><i class="fa fa-camera m-0"></i> </a>
                                                </div>
                                            </div>

                                            <!-- start Modal-->
                                            <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Capture Photo</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="modalClos()">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div>
                                                                <div id="my_camera" class="d-block mx-auto rounded overflow-hidden"></div>
                                                                <input type="hidden" name="captured_image_data" id="captured_image_data">
                                                            </div>
                                                            <div id="results" class="d-none">
                                                                <img style="width: 320px;" class="after_capture_frame" src="images/avatar/1.png" />
                                                            </div>
                                                            <!-- <form method="post" id="photoForm">
                                                                        <input type="hidden" id="photoStore" name="photoStore" value="">
                                                                    </form> -->
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-warning mx-auto text-white" id="takephoto" onClick="take_snapshot()">Capture Photo</button>
                                                            <button type="button" class="btn btn-warning mx-auto text-white d-none" id="retakephoto">Retake</button>
                                                            <button type="submit" class="btn btn-warning mx-auto text-white d-none" id="uploadphoto" form="photoForm" onclick="saveSnap()">Upload</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end modal -->

                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Client's Scanned
                                                        Signature</label>
                                                    <input type="file" name="sign" class="form-control" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Client's Fingerprint Biometrics</label>
                                                    <input type="file" name="fingerprint" class="form-control" placeholder="">
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Any Other
                                                        Attachments</label>
                                                    <input type="file" name="otherattach" class="form-control" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                            <br /><br />
                                        </div>
                                        <!-- </div> -->

                                </form>
                                <!-- </div> -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('includes/footer.php'); ?>



        </div>

        <!-- Required vendors -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <!-- <script src="./js/webcamjs/webcam.js"></script> -->
        <script src="./js/main.js"></script>
        <?php
        include('includes/bottom_scripts.php');
        ?>
        <!-- <script src="./js/sweetalert/sweetalert.min.js"></script> -->

        <script>
            function setUp() {
                var x = document.getElementById("upload");
                var y = document.getElementById("photo");
                // var z = document.getElementById("skip");
                y.style.display = "block";

                // z.style.display = "none";
                x.style.display = "none";
            }

            function setUpn() {
                var x = document.getElementById("upload");
                var y = document.getElementById("photo");
                // var z = document.getElementById("skip");
                y.style.display = "none";

                x.style.display = "none";
                // z.style.display = "block";
            }

            function setDown() {
                var x = document.getElementById("upload");
                var y = document.getElementById("photo");
                // var z = document.getElementById("skip");
                x.style.display = "block";

                y.style.display = "none";
                // z.style.display = "none";
            }

            function modalClos() {
                $("#photoModal").modal("hide");
            }

            function take_snapshot() {
                // play sound effect
                //shutter.play();
                // take snapshot and get image data
                Webcam.snap(function(data_uri) {
                    // display results in page
                    document.getElementById("results").innerHTML =
                        '<img class="after_capture_frame" src="' + data_uri + '"/>';
                    $("#captured_image_data").val(data_uri);
                });

                $("#my_camera").removeClass("d-block");
                $("#my_camera").addClass("d-none");

                $("#results").removeClass("d-none");

                $("#takephoto").removeClass("d-block");
                $("#takephoto").addClass("d-none");

                $("#retakephoto").removeClass("d-none");
                $("#retakephoto").addClass("d-block");

                $("#uploadphoto").removeClass("d-none");
                $("#uploadphoto").addClass("d-block");
            }

            function saveSnap() {
                var base64data = $("#captured_image_data").val();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "photoUpload.php",
                    data: {
                        image: base64data
                    },
                    success: function(data) {
                        // alert(data);
                        $("#captured_image_data_2").val(data);
                        $("#my_camera").addClass("d-block");
                        $("#my_camera").removeClass("d-none");

                        $("#results").addClass("d-none");

                        $("#takephoto").addClass("d-block");
                        $("#takephoto").removeClass("d-none");

                        $("#retakephoto").addClass("d-none");
                        $("#retakephoto").removeClass("d-block");

                        $("#uploadphoto").addClass("d-none");
                        $("#uploadphoto").removeClass("d-block");

                        $("#photoModal").modal("hide");

                        alert_success('Photo uploaded successfully!');

                    },
                });
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
        <script type="text/javascript">
            $(document).ready(function() {
                $('#smartwizard').smartWizard();


            });
        </script>



</body>

</html>