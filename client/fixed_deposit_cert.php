<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_all_deposits')) {
    return $permissions->isNotPermitted(true);
}
$title = 'FIXED DEPOSIT CERTIFICATE';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();
$fd_details = $response->getFixedDepDetails($_GET['id'])[0];

$amount = str_replace(",", "", $fd_details['amount']);
$interest_rate = $fd_details['int_rate'];
$period = $fd_details['per'];
$period_type = $fd_details['ptype'];
$frequency = $fd_details['freqtype'];
$wht_rate = $fd_details['wht'];

$interest = 0;
$freq_days = 1;
$total_period_interest = 0;
$inst_interest = 0;
$no_times = 0;


if ($period_type == 'y') {
    $period_no_days = $period * 360;
    $daily_rate  = $interest_rate / 36000;
    $daily_interest = $amount * $daily_rate;
    $total_period_interest = round($daily_interest * $period_no_days);

    if ($frequency == 'm') {
        $freq_days = 30;
    } else  if ($frequency == 'q') {
        $freq_days = 90;
    } else  if ($frequency == 'h') {
        $freq_days = 180;
    } else  if ($frequency == 'y') {
        $freq_days = 360;
    }

    $no_times = $period_no_days / $freq_days;

    $inst_interest = round($total_period_interest / $no_times);
} else if ($period_type == 'm') {
    $period_no_days = $period * 30;
    $daily_rate  = $interest_rate / 36000;
    $daily_interest = $amount * $daily_rate;
    $total_period_interest = round($daily_interest * $period_no_days);

    if ($frequency == 'm') {
        $freq_days = 30;
    } else  if ($frequency == 'q') {
        $freq_days = 90;
    } else  if ($frequency == 'h') {
        $freq_days = 180;
    } else  if ($frequency == 'y') {
        $freq_days = 360;
    }

    $no_times = $period_no_days / $freq_days;

    $inst_interest = round($total_period_interest / $no_times);
} else if ($period_type == 'd') {
    $period_no_days = $period;
    $daily_rate  = $interest_rate / 36000;
    $daily_interest = $amount * $daily_rate;
    $total_period_interest = round($daily_interest * $period_no_days);

    if ($frequency == 'm') {
        $freq_days = 30;
    } else  if ($frequency == 'q') {
        $freq_days = 90;
    } else  if ($frequency == 'h') {
        $freq_days = 180;
    } else  if ($frequency == 'y') {
        $freq_days = 360;
    }

    $no_times = $period_no_days / $freq_days;

    $inst_interest = round($total_period_interest / $no_times);
}

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



                    <div class="card">
                        <div class="card-body">

                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | <a href="javascript:void(0);" onclick="h_print_div('fixed_deposit_certificate');" class="btn btn-sm btn-soft-primary"><i class="ti-printer"></i> Print</a> | <a href="export_report.php?exportFile=fixed_deposit_cert&id=<?= @$_REQUEST['id'] ?>" target="_blank" class="btn btn-primary light btn-xs">
                                <i class="fas fa-file-pdf"></i>&nbsp;Print PDF
                            </a>
                            <hr class="hr-dashed">

                            <div id="fixed_deposit_certificate" style="margin: auto;border: 5px solid;border-style: double;padding: 20px;text-align: center;font-family: 'Patua One',cursives;font-size: 17px;">

                                <table style="margin: 0 auto; text-align: center;">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h1 style="margin: 0;padding: 10px;width: 600px;margin-bottom: 20px;font-size: 20px;">
                                                    <strong>FIXED DEPOSIT CERTIFICATE</strong>
                                                </h1>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <h1>I</h1>
                                <p><strong><u><?= $fd_details['client']['firstName'] . ' ' . $fd_details['client']['lastName']; ?> </u></strong></p>
                                <p><i>member no: (<?= strtoupper($fd_details['client']['membership_no']); ?>)</i></p>

                                <p>
                                    Certify that on <u>(<i><?= normal_date($fd_details['open_date']); ?></i>)</u>
                                </p>
                                <p>
                                    Fixed a total amounting to <u>UGX <?= number_format($fd_details['amount']); ?> (<?= h_convert_number_to_words((int)number_format($fd_details['amount'])); ?>)</u> On A/C : <u><?= $fd_details['client']['membership_no'] ?></u>
                                </p>

                                <p>
                                    The fixed deposit will mature after (<?= $fd_details['period']; ?>), From the date of
                                    placement and in any case not later than (<u><?= normal_date($fd_details['close_date']); ?></u>) The
                                    fixed deposit interest is at the rate of <?= number_format($fd_details['int_rate']); ?> % per Annum ( <?= number_format((float)($fd_details['int_rate'] / 12), 2, '.', ''); ?> per Month).
                                </p>

                                <br>
                                <p>
                                    The total amount payable is (<u>Principal: <?= number_format($fd_details['amount']); ?> | Interest: <?= number_format($no_times * $inst_interest); ?></u>)
                                </p>

                                <br>

                                <p>
                                <table style="margin: 0 auto; text-align: left;    font-size: 16px;">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p><strong>Issuing officer </strong></p>
                                            </td>

                                            <td>
                                                <p><strong>Client/Member </strong></p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding-bottom: 10px;">
                                                <p style="padding-right: 30px;"><strong>Name: …………………………………………… </strong></p>
                                            </td>

                                            <td style="padding-bottom: 10px;">
                                                <p><strong>Name: …………………………………………… </strong></p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <p style="padding-right: 30px;padding-bottom: 10px;"><strong>Sign: …………………………………………… </strong></p>
                                            </td>

                                            <td>
                                                <p><strong>Sign: …………………………………………… </strong></p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <p style="padding-right: 30px;"><strong>Date: …………………………………………… </strong></p>
                                            </td>

                                            <td>
                                                <p><strong>Date: …………………………………………… </strong></p>
                                            </td>
                                        </tr>


                                    </tbody>
                                </table>
                                </p>

                                <div class="row show_on_printa">
                                    <div class="col-md-4" style="width: 369px;float: left; text-align: left;">
                                    </div>

                                    <div class="col-md-4" style="width: 360px"></div>
                                    <div class="col-md-4" style="width: 369px;float: right;">

                                        <div style="width: 313px;height: 96px;border: 1px solid;"></div>
                                        <br>

                                        <i>Official Use Only</i>

                                    </div>

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



</body>

</html>