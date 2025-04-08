<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_client_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'Clients Report';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'ClientReport', 'ReportModelMethod' => 'getClientScheduleReport'];

if (@$_REQUEST['filtered']) {
    $request_data['branchId'] = @$_REQUEST['branchId'];
    $request_data['gender'] = @$_REQUEST['gender'];
    $request_data['region'] = @$_REQUEST['region'];
    $request_data['district'] = @$_REQUEST['district'];
    $request_data['village'] = @$_REQUEST['village'];
    $request_data['actype'] = @$_REQUEST['actype'];
    $request_data['loan_officer_id'] = @$_REQUEST['loan_officer_id'];
    $request_data['start_date'] = @$_REQUEST['start_date'];
    $request_data['end_date'] = @$_REQUEST['end_date'];
}
$_REQUEST['start_date'] = @$_REQUEST['start_date'] ?? date('Y-m-d');
$_REQUEST['end_date'] = @$_REQUEST['end_date'] ?? date('Y-m-d');
$report_reponse = $ReportService->generateReport(@$request_data);
$members = @$report_reponse['data'];
// var_dump($response);
// exit;
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
$actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);
$staff = $response->getBankStaff2($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);

// var_dump($members);
// exit;

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search(@$_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}

$account_type_name = '';
if (@$_REQUEST['actype']) {
    $key = array_search(@$_REQUEST['actype'], array_column($actypes, 'id'));
    $account_type_name = $actypes[$key]['ucode'] . '-' . $actypes[$key]['name'];
}

$staff_names = '';
if (@$_REQUEST['loan_officer_id']) {
    $key = array_search($_REQUEST['loan_officer_id'], array_column($staff, 'id'));
    $staff_names = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}

?>
<?php
$title = 'CLIENTS REPORT';
require_once('includes/head_tag.php');
require_once('includes/reports_css.php');
?>
<style>
    table {
        width: 100%;
        table-layout: fixed;
    }

    th,
    td {
        word-wrap: break-word;
        overflow: hidden;
    }
</style>

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




                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>


                        <form class="ajax_results_form" method="GET">
                            <input type="hidden" name="filtered" value="1">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if (@$_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= @$_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select id="bankacc" class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
                                                <option value="0"> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branchId'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branchId'] == $row['id'] ? "selected" : "";
                                                ?>
                                                        <option value="<?= @$row['id'] ?>" <?= $is_seleceted ?>>
                                                            <?= $row['name'] ?>
                                                        </option>
                                                <?php }
                                                } ?>

                                            </select>
                                        <?php } ?>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Savings Officer*</label>

                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="loan_officer_id">
                                            <option value="0"> All </option>
                                            <?php
                                            if ($staff !== '') {
                                                foreach ($staff as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['loan_officer_id'] == $row['id'] ? 'selected' : '' ?>>
                                                        <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php }
                                            } else { ?>
                                                <option readonly>No Staff Added yet</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Gender *</label>

                                        <select name="gender" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">All</option>
                                            <option value="Male" <?= @$_REQUEST['gender'] == "Male" ? "selected" : "" ?>>Male</option>
                                            <option value="Female" <?= @$_REQUEST['gender'] == "Female" ? "selected" : "" ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Age Group *</label>

                                        <select name="age_group" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">All</option>
                                            <option value="17" <?= @$_REQUEST['age_group'] == "17" ? "selected" : "" ?>>0 to 17 Yrs</option>
                                            <option value="18+" <?= @$_REQUEST['age_group'] == "18+" ? "selected" : "" ?>>18 to 35 Yrs</option>
                                            <option value="35+" <?= @$_REQUEST['age_group'] == "35+" ? "selected" : "" ?>>Above 35 Yrs</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Income Level *</label>

                                        <select name="income_level" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">All</option>
                                            <option value="500000" <?= @$_REQUEST['income_level'] == "500000" ? "selected" : "" ?>>0 to 500,000</option>
                                            <option value="1m" <?= @$_REQUEST['income_level'] == "1m" ? "selected" : "" ?>>500,001 to 1 Million</option>
                                            <option value="1+" <?= @$_REQUEST['income_level'] == "1+" ? "selected" : "" ?>>Above 1 Million</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Disability Status *</label>

                                        <select name="disability_status" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">All</option>
                                            <option value="normal" <?= @$_REQUEST['disability_status'] == "normal" ? "selected" : "" ?>>Normal</option>
                                            <option value="disabled" <?= @$_REQUEST['disability_status'] == "disabled" ? "selected" : "" ?>>Disabled</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">District *</label>

                                        <select name="district" class="me-sm-2 default-select form-control wide" id="district">
                                            <option value="">All</option>
                                            <option value="Abim">Abim</option>
                                            <option value="Adjumani">Adjumani</option>
                                            <option value="Agago">Agago</option>
                                            <option value="Alebtong">Alebtong</option>
                                            <option value="Amolatar">Amolatar</option>
                                            <option value="Amudat">Amudat</option>
                                            <option value="Amuria">Amuria</option>
                                            <option value="Amuru">Amuru</option>
                                            <option value="Apac">Apac</option>
                                            <option value="Arua">Arua</option>
                                            <option value="Budaka">Budaka</option>
                                            <option value="Bududa">Bududa</option>
                                            <option value="Bugiri">Bugiri</option>
                                            <option value="Bugweri">Bugweri</option>
                                            <option value="Buhweju">Buhweju</option>
                                            <option value="Buikwe">Buikwe</option>
                                            <option value="Bukedea">Bukedea</option>
                                            <option value="Bukomansimbi">Bukomansimbi</option>
                                            <option value="Bukwo">Bukwo</option>
                                            <option value="Bulambuli">Bulambuli</option>
                                            <option value="Buliisa">Buliisa</option>
                                            <option value="Bundibugyo">Bundibugyo</option>
                                            <option value="Bunyangabu">Bunyangabu</option>
                                            <option value="Bushenyi">Bushenyi</option>
                                            <option value="Busia">Busia</option>
                                            <option value="Butaleja">Butaleja</option>
                                            <option value="Butambala">Butambala</option>
                                            <option value="Butebo">Butebo</option>
                                            <option value="Buvuma">Buvuma</option>
                                            <option value="Buyende">Buyende</option>
                                            <option value="Dokolo">Dokolo</option>
                                            <option value="Gomba">Gomba</option>
                                            <option value="Gulu">Gulu</option>
                                            <option value="Hoima">Hoima</option>
                                            <option value="Ibanda">Ibanda</option>
                                            <option value="Iganga">Iganga</option>
                                            <option value="Isingiro">Isingiro</option>
                                            <option value="Jinja">Jinja</option>
                                            <option value="Kaabong">Kaabong</option>
                                            <option value="Kabale">Kabale</option>
                                            <option value="Kabarole">Kabarole</option>
                                            <option value="Kaberamaido">Kaberamaido</option>
                                            <option value="Kagadi">Kagadi</option>
                                            <option value="Kakumiro">Kakumiro</option>
                                            <option value="Kalangala">Kalangala</option>
                                            <option value="Kaliro">Kaliro</option>
                                            <option value="Kalungu">Kalungu</option>
                                            <option value="Kampala">Kampala</option>
                                            <option value="Kamuli">Kamuli</option>
                                            <option value="Kamwenge">Kamwenge</option>
                                            <option value="Kanungu">Kanungu</option>
                                            <option value="Kapchorwa">Kapchorwa</option>
                                            <option value="Kapelebyong">Kapelebyong</option>
                                            <option value="Karenga">Karenga</option>
                                            <option value="Kasanda">Kasanda</option>
                                            <option value="Kasese">Kasese</option>
                                            <option value="Katakwi">Katakwi</option>
                                            <option value="Kayunga">Kayunga</option>
                                            <option value="Kazo">Kazo</option>
                                            <option value="Kibaale">Kibaale</option>
                                            <option value="Kiboga">Kiboga</option>
                                            <option value="Kibuku">Kibuku</option>
                                            <option value="Kiruhura">Kiruhura</option>
                                            <option value="Kiryandongo">Kiryandongo</option>
                                            <option value="Kisoro">Kisoro</option>
                                            <option value="Kitagwenda">Kitagwenda</option>
                                            <option value="Kitgum">Kitgum</option>
                                            <option value="Koboko">Koboko</option>
                                            <option value="Kole">Kole</option>
                                            <option value="Kotido">Kotido</option>
                                            <option value="Kumi">Kumi</option>
                                            <option value="Kwania">Kwania</option>
                                            <option value="Kween">Kween</option>
                                            <option value="Kyankwanzi">Kyankwanzi</option>
                                            <option value="Kyegegwa">Kyegegwa</option>
                                            <option value="Kyenjojo">Kyenjojo</option>
                                            <option value="Kyotera">Kyotera</option>
                                            <option value="Lamwo">Lamwo</option>
                                            <option value="Lira">Lira</option>
                                            <option value="Luuka">Luuka</option>
                                            <option value="Luwero">Luwero</option>
                                            <option value="Lwengo">Lwengo</option>
                                            <option value="Lyantonde">Lyantonde</option>
                                            <option value="Manafwa">Manafwa</option>
                                            <option value="Maracha">Maracha</option>
                                            <option value="Masaka">Masaka</option>
                                            <option value="Masindi">Masindi</option>
                                            <option value="Mayuge">Mayuge</option>
                                            <option value="Mbale">Mbale</option>
                                            <option value="Mbarara">Mbarara</option>
                                            <option value="Mitooma">Mitooma</option>
                                            <option value="Mityana">Mityana</option>
                                            <option value="Moroto">Moroto</option>
                                            <option value="Moyo">Moyo</option>
                                            <option value="Mpigi">Mpigi</option>
                                            <option value="Mubende">Mubende</option>
                                            <option value="Mukono">Mukono</option>
                                            <option value="Nabilatuk">Nabilatuk</option>
                                            <option value="Nakapiripirit">Nakapiripirit</option>
                                            <option value="Nakaseke">Nakaseke</option>
                                            <option value="Nakasongola">Nakasongola</option>
                                            <option value="Namayingo">Namayingo</option>
                                            <option value="Namisindwa">Namisindwa</option>
                                            <option value="Namutumba">Namutumba</option>
                                            <option value="Napak">Napak</option>
                                            <option value="Nebbi">Nebbi</option>
                                            <option value="Ngora">Ngora</option>
                                            <option value="Ntoroko">Ntoroko</option>
                                            <option value="Ntungamo">Ntungamo</option>
                                            <option value="Nwoya">Nwoya</option>
                                            <option value="Obongi">Obongi</option>
                                            <option value="Omoro">Omoro</option>
                                            <option value="Otuke">Otuke</option>
                                            <option value="Oyam">Oyam</option>
                                            <option value="Pader">Pader</option>
                                            <option value="Pakwach">Pakwach</option>
                                            <option value="Pallisa">Pallisa</option>
                                            <option value="Rakai">Rakai</option>
                                            <option value="Rubanda">Rubanda</option>
                                            <option value="Rubirizi">Rubirizi</option>
                                            <option value="Rukiga">Rukiga</option>
                                            <option value="Rukungiri">Rukungiri</option>
                                            <option value="Sembabule">Sembabule</option>
                                            <option value="Serere">Serere</option>
                                            <option value="Sheema">Sheema</option>
                                            <option value="Sironko">Sironko</option>
                                            <option value="Soroti">Soroti</option>
                                            <option value="Tororo">Tororo</option>
                                            <option value="Wakiso">Wakiso</option>
                                            <option value="Yumbe">Yumbe</option>
                                            <option value="Zombo">Zombo</option>


                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Education Level *</label>

                                        <select name="village" class="me-sm-2 default-select form-control wide" id="osector">

                                            <option value="" disabled selected>-- Choose an option --</option>
                                            <option value="Primary Level">Primary Level</option>
                                            <option value="Ordinary Level">O-Level (Ordinary Level)</option>
                                            <option value="Advanced Level">A-Level (Advanced Level)</option>
                                            <option value="Diploma">Diploma</option>
                                            <option value="Bachelors">Bachelor's Degree</option>
                                            <option value="Masters">Master's Degree</option>
                                            <option value="Doctorate">Doctorate (PhD)</option>
                                            <option value="No Formal Education">No Formal Education</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Region *</label>

                                        <select name="region" class=" form-control wide" id="credit_account">
                                            <option value="">All </option>

                                            <option value="Central">Central</option>
                                            <option value="Eastern">Eastern</option>
                                            <option value="Northern">Northern</option>
                                            <option value="Western">Western</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Village *</label>

                                        <select name="village" class="me-sm-2 default-select form-control wide" id="village">
                                            <option value="">All</option>
                                           

                                        </select>
                                    </div>
                                </div> -->
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Select Savings Account *</label>

                                        <select class="me-sm-2 default-select form-control wide" id="sprod" name="actype">

                                            <option value=""> All</option>

                                            <?php

                                            foreach ($actypes as $row) {
                                                $selected = @$_REQUEST['actype'] == $row['id'] ? "selected" : "";
                                            ?>
                                                <option value="<?= $row['id']; ?>" <?= $selected; ?>>
                                                    <?= $row['ucode'] . ' - ' .
                                                        $row['name'] ?>
                                                </option>

                                            <?php }
                                            ?>

                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Registration Start
                                            Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?= @$_REQUEST['start_date'] ?? date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Registration End
                                            Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?? date('Y-m-d'); ?>" id="exampleInputEmail4" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch Entries</button>
                                    </div>
                                </div>
                            </div>


                        </form>


                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn"><i class="fa fa-arrow-left"></i> Back</a>
                            Client Schedule Report
                        </h4>

                        <?php if (count($members)) :
                            $request_string = 'branchName=' . @$branch_name . '&accountName=' . @$account_type_name;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>

                                <a class="btn btn-primary light btn-xs" onclick="h_print_divImages('exreportn');">
                                    <i class="fas fa-file-pdf"></i>&nbsp;PDF
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> Client Schedule Report: </strong> </td>
                            </tr>
                        </table>


                        <table>
                            <?php if (@$_REQUEST['branchId']) : ?>
                                <tr>
                                    <td width="18%"> Branch:</td>
                                    <td> <strong> <?= @$branch_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['actype']) : ?>
                                <tr>
                                    <td width="18%"> Savings Account:</td>
                                    <td> <strong> <?= @$account_type_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>
                            <?php if (@$_REQUEST['village']) : ?>
                                <tr>
                                    <td width="18%"> Education Level:</td>
                                    <td> <strong> <?= @$_REQUEST['village']; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['loan_officer_id']) : ?>
                                <tr>
                                    <td width="18%"> Entered by:</td>
                                    <td> <strong> <?= @$staff_names; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['region']) : ?>
                                <tr>
                                    <td width="18%"> Client Region:</td>
                                    <td> <strong> <?= strtoupper(@$_REQUEST['region']); ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['gender']) : ?>
                                <tr>
                                    <td width="18%"> Gender:</td>
                                    <td> <strong> <?= @$_REQUEST['gender']; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                            From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                                            To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>
                        <table class="report_table">
                            <thead>
                                <th>S/No.</th>
                                <th>A/C No.</th>
                                <th>Full Name</th>
                                <th>Type</th>
                                <th>NIN</th>
                                <th>Nationality</th>
                                <th>Date of Birth</th>
                                <th>Address</th>
                                <th>Email Address</th>
                                <th>Telephone No.</th>
                                <th>Occupation</th>
                                <th>Registration Date</th>

                            </thead>
                            <tbody>

                                <?php
                                $counter = 0;
                                foreach ($members as $member) {
                                    $client_contacts = '';
                                    $use_name_value = '';
                                    if ($member['primaryCellPhone']) {
                                        $client_contacts = $member['primaryCellPhone'];
                                    }


                                ?>
                                    <tr>
                                        <td> <?= ++$counter ?> </td>
                                        <td> <?= @$member['membership_no'] == 0 ? '-' : @$member['membership_no']; ?> </td>
                                        <td> <?= strtoupper(@$member['client_names']); ?> </td>
                                        <td> <?= strtoupper(@$member['client_type']); ?> </td>
                                        <td> <?= @$member['nin']; ?> </td>
                                        <td> <?= @$member['country']; ?> </td>
                                        <td> <?= normal_date_short(@$member['dateOfBirth']) ?> </td>
                                        <td> <?= @$member['village']  ?> </td>
                                        <td> <?= @$member['email']; ?> </td>

                                        <td> <?= @$client_contacts; ?> </td>

                                        <td> <?= @$member['profession']; ?> </td>


                                        <td> <?= normal_date_short(@$member['client_created_at']) ?> </td>


                                    </tr>
                                <?php
                                    // ++$total_count;
                                } ?>
                                <tr>
                                    <th colspan="11">Total Clients</th>
                                    <th class="text-center"> <?= number_format($counter) ?> </th>
                                </tr>
                            </tbody>
                        </table>

                        <?php
                        if (!count($members)) {
                            require_once('./not_records_found.php');
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php
        include('includes/bottom_scripts.php');
        ?>

</body>

</html>