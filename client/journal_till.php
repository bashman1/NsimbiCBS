<?php
include('../backend/config/session.php');
require_once('includes/functions.php');
$title = 'JOURNAL A/C TILL SHEET';
require_once('includes/head_tag.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
include_once('includes/response.php');
$response = new Response();

// $_GET['id'] = parsed_id($_GET['id']);

$sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);

$journal_account = '';
if (@$_REQUEST['acid']) {
    $key = array_search($_REQUEST['acid'], array_column($sub_accs, 'id'));
    $journal_account = $sub_accs[$key]['name'] . ' - ' . $sub_accs[$key]['branch'];
}
?>


<body>

    <!--*******************
 Preloader start
 ********************-->
    <?php include_once('includes/preloader.php'); ?>
    <!--*******************
 Preloader end
 ********************-->


    <!--**********************************
 Main wrapper start
 ***********************************-->
    <div id="main-wrapper">

        <?php
        include_once('includes/nav_bar.php');
        include_once('includes/side_bar.php');
        ?>
        <!--**********************************
 Content body start
 ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Journal A/Cs Till Sheet
                                </h4>
                            </div>
                            <div style="padding: 0.9rem 1.875rem 0.25rem;">
                                <h1 style="font-size: 16px"> <small>Filter By Journal Account & Date
                                        Range </small></h1>
                                <br>

                                <form class="form-inlines select_datess ajax_results_form" method="post" id="filterBydates">

                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="text-label form-label">Journal Account*</label>
                                                <select id="clientsselect" tabindex="-1" class="me-sm-2 default-select form-control wide" aria-hidden="true" name="acid">
                                                    <option value=""> All </option>
                                                    <?php
                                                    if ($sub_accs !== '') {
                                                        foreach ($sub_accs as $row) { ?>
                                                            <option value="<?= $row['id'] ?>" <?= @$_REQUEST['acid'] == $row['id'] ? 'selected' : '' ?>>
                                                                <?= $row['name'] . ' - ' . $row['branch'] ?>
                                                            </option>
                                                        <?php }
                                                    } else { ?>
                                                        <option readonly>No Journal Accounts yet</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php


                                        echo '
                                    <div class="col-md-3">


                                        <div class="form-group">
                                            <label class="sr-onlys" for="exampleInputEmail3">Start
                                                Date</label>
                                            <input type="date" class="form-control" name="from_date" value="' . (isset($_POST['from_date']) ? $_POST['from_date'] : date('Y-m-d')) . '" id="exampleInputEmail3" placeholder="Start Date">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="sr-onlys" for="exampleInputPassword3">End
                                                Date</label>
                                            <input type="date" class="form-control" name="to_date" value="' . (isset($_POST['to_date']) ? $_POST['to_date'] : date('Y-m-d')) . '" id="exampleInputPassword3" placeholder="End Date">
                                        </div>
                                    </div>

                                        ';

                                        ?>


                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="sr-onlys" for="fetch">&nbsp;&nbsp;</label>
                                                <button type="submit" class="btn btn-primary light btn-xs mb-1 form-control" name="submit">Fetch
                                                    Entries</button>
                                            </div>
                                        </div>

                                    </div>

                                </form>
                            </div>

                            <div>

                                <div class="card">
                                    <div class="card-header">



                                        <a class="btn btn-primary btn-sm" onclick="PrintContent('exreportn')">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                    </div>
                                    <div class="card-body" id="exreportn">
                                        <!--<div class="table-responsive">-->
                                        <?php
                                        $start = $_POST['from_date'] ?? date('Y-m-d');
                                        $end = $_POST['to_date'] ?? date('Y-m-d');
                                        $staff = $_POST['acid'] ?? 0;


                                        if (@$staff) {
                                            $details = $response->getJournalTillEntries($start, $end, $staff);
                                        } else {
                                            $details = '';
                                        }



                                        ?>

                                        <center style="font-size:15px">

                                            <br /><br />
                                            <p class=" text-primary"><?= @$journal_account ?? '' ?>
                                            </p>
                                        </center><br /><br />



                                        <br />
                                        <?php
                                        if ($details) {
                                        ?>


                                            <table id="staff" class="table table-striped" style="min-width: 845px">
                                                <thead>

                                                    <tr>
                                                        <th colspan="7" style="text-align:center;">
                                                            <h4 class="page-title">Till Sheet Journal Entries Report:
                                                                <?php echo date('Y-m-d', strtotime($_POST['from_date'] ?? date('Y-m-d'))) . '   -   ' . date('Y-m-d', strtotime($_POST['to_date'] ?? date('Y-m-d'))) ?>
                                                            </h4>
                                                        </th>
                                                    </tr>

                                                    <tr style="text-align: center !important;">
                                                        <th>#</th>
                                                        <th>Description:</th>
                                                        <th>DATE:</th>
                                                        <th>REFERENCE NO:</th>
                                                        <th>DR:</th>
                                                        <th>CR:</th>
                                                        <th>BALANCE:</th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $dess = '';
                                                    $count = 1;
                                                    $ccount = 0;
                                                    $dcount = 0;
                                                    $val = 0;
                                                    $ctotal = 0;
                                                    $dtotal = 0;


                                                    $val = $details[0]['bf'] ?? 0;
                                                    echo '
                                                    <tr style="color: blue !important">
                                                    <td>' . $count++ . '</td>
                                                    <td>(Opening Balance)</td>
                                                    <td>' . date('Y-m-d', strtotime($_POST['from_date'])) . '</td>
                                                    <td></td>
                                                    <td> ' . number_format($val) . '</td>
                                                    <td></td>

                                                    <td>' . number_format($val) . '</td>

                                                    </tr>
                                                    ';
                                                    $debit = 0;
                                                    $credit = 0;
                                                    foreach ($details as $deposit) {
                                                        $trxn_date = date('Y-m-d', strtotime($deposit['_date_created']));

                                                        // if ($deposit['cash_acc_details'] == 'INCOMES' || $deposit['cash_acc_details'] == 'CAPITAL' || $deposit['cash_acc_details'] == 'LIABILITIES') {
                                                        //     $credit = number_format($deposit['_amount']);
                                                        //     $debit = "-";
                                                        //     $val = $val - $deposit['_amount'];
                                                        //     $dtotal = $dtotal + $deposit['_amount'];
                                                        //     $ccount++;
                                                        // } else {
                                                        if ($deposit['type'] == 'ASS') {
                                                            if ($deposit['cr_dr'] == 'credit') {
                                                                $debit = number_format($deposit['_amount']);
                                                                $credit = "-";
                                                                $val = $val +  $deposit['_amount'];
                                                                $ctotal = $ctotal + $deposit['_amount'];
                                                                $dcount++;
                                                            } else {
                                                                $credit = number_format($deposit['_amount']);
                                                                $debit = "-";
                                                                $val = $val -  $deposit['_amount'];
                                                                $dtotal = $dtotal + $deposit['_amount'];
                                                                $ccount++;
                                                            }
                                                        } else {
                                                            $debit = number_format($deposit['_amount']);
                                                            $credit = "-";
                                                            $val = $val +  $deposit['_amount'];
                                                            $ctotal = $ctotal + $deposit['_amount'];
                                                            $dcount++;
                                                        }

                                                        // }

                                                        $dess = '';

                                                        echo '
                                                    <tr>
                                        <td>' . $count++ . '</td>
                                        <td>' . $dess . $deposit['_reason'] . '</td>
                                          <td>' . $trxn_date . '</td>
                                           <td class="no_print clickable_ref_no" ref-no="' . $deposit['ref'] . '" tid="' . $deposit['_did'] . '">' . $deposit['ref'] . '</td>
                                       
                                          <td>' . $debit . '</td>
                                        <td>' . $credit . '</td>
                                        <td> ' . number_format($val) . '</td>
                                       
                                       
                                      
                                   
                                        </tr>
                                        ';
                                                    }
                                                    //   here totals were here

                                                    ?>

                                                </tbody>

                                            </table>
                                            <table class="table table-striped" style="min-width: 845px">
                                                <tbody>
                                                    <?php
                                                    echo '
                                                        <tr>
                                                        <td></td>
                                                        <td><b>Totals<b></td>
                                                       
                                                          <td><b>DR: ' . number_format($ctotal) . '</b></td>
                                                        <td><b>CR: ' . number_format($dtotal) . '</b></td>

                                                        <td> <b>BALANCE: ' . number_format($val) . '<b></td>

                                                        <td></td>
                                                        <td></td>
                                                   
                                                        </tr>
                                                        ';
                                                    ?>
                                                </tbody>
                                            </table>

                                            <div class="row show_on_print">
                                                <div class="col-md-4" style="width: 369px;float: left;">

                                                    <h4><small>Generated By:<br />
                                                        </small><b>-------------------------------------</b></h4>

                                                    <br>

                                                    <h4><small>SIGNATURE:</small><b>
                                                            ------------------------</b></h4>

                                                </div>

                                                <div class="col-md-4" style="width: 369px"></div>
                                                <div class="col-md-4" style="width: 369px;float: right;">

                                                    <div style="width: 313px;height: 96px;border: 1px solid;">
                                                    </div>
                                                    <br>
                                                    <i>Official Use Only</i>
                                                </div>
                                            </div>
                                        <?php
                                        } else {
                                            echo '<div class="col-md-4"><div class="alert alert-warning"><span class="semibold">Caution: </span>No Journal Entries found' . @$_POST['acid'] . '</div></div>';
                                        }
                                        ?>

                                        <!--</div>-->
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
                <div class="modal fade" id="pageGeneralModal">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body">

                            </div>

                        </div>
                    </div>
                </div>

                <?php include('includes/bottom_scripts.php'); ?>


                <script type="text/javascript">
                    var table = $('#staff').dataTable({
                        destroy: true,
                        lengthMenu: [
                            [10, 25, 50, 100, 500, -1], // Options for number of records displayed
                            [10, 25, 50, 100, 500, "All"] // Labels for each option
                        ],
                        pageLength: 25, // Default number of records to display
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                    })
                </script>

</body>

</html>