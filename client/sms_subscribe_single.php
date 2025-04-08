<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

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
                                    All Clients
                                </h4>
                            </div>


                            <div class="card-body">

                                <h4 class="mt-0 header-title">Account Search Query </h4>
                                <p class="text-muted mb-3">Search by Name | Client No | Account No</p>
                          

                                <form method="post" action="<?=BACKEND_BASE_URL?>Bank/sms_sub_search_clients.php?branch=<?php echo $user[0]['branchId']; ?>&bank=<?php echo $user[0]['bankId']; ?>&act=<?php echo $_GET['act'] ?>">
                                    <div class="form-group">
                                        <input type="text" value="" class="form-control" onkeyup="makeLiveSearch2($(this))" name="search_term">
                                    </div>
                                </form>

                                <div class="search_results table-responsive">
                                    <i>Results Appear Here</i>
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
        <!-- <script src="./js/styleSwitcher.js"></script> -->
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
            //form handler
            function makeLiveSearch2(input_field, event) {
                var form = input_field.closest('form');

                form.submit(function(event) {
                    event.preventDefault();
                });

                var value = input_field.val();
                var search_results = form.next('.search_results');

                if (value.length <= 2)
                    return search_results.html('Insert Atleast (3) characters');

                search_results.html('<img src="images/preloaderImages/loading.gif"> searching.....');

                //run ajax call
                $.get(form.attr('action'), {
                    term: value,
                    requestType: 'ajax',
                    live: 1
                }, function(data) {
                    // var content = $.parseJSON(data);
                    data.redirect > 0 ? search_results.html(data.data) : search_results.html(data.message);

                });
            }


            //form handler
            function makeLiveSearch(liveSearchForm, $value, event) {
                var $search_form = $('#' + liveSearchForm);
                $search_form.submit(function(event) {
                    event.preventDefault();
                });

                var $search_results = $search_form.next('.search_results');
                var $button = $search_form.find('button');
                //get action url
                var $action_url = $search_form.attr('action');

                if ($value.length <= 3) {
                    $button.removeAttr('disabled');
                    $button.text('Manual Search');
                    $search_results.html('Insert Atleast (4) characters');
                    return;
                }

                $search_results.html('<img src="images/preloaderImages/loading.gif"> searching.....');

                //run ajax call
                $.get($action_url, {
                    term: $value,
                    requestType: 'ajax',
                    live: 1
                }, function(data) {
                    var content = $.parseJSON(data);
                    if (content.redirect > 0) {
                        handleAjaxPageRedirect(content);
                    } else {
                        $search_results.html(content.message);
                    }
                });
            }
        </script>

</body>

</html>