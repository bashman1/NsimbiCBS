<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasPermissions('clients')) {
    return $permissions->isNotPermitted(true);
}
$title = 'DEACTIVATED CLIENTS';
include_once('includes/response.php');
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
$actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);

// print_r($_SESSION['session_user']);
// exit;

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

                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Clients</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Bank Clients</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <!-- <div class="row"> -->

                <!-- <div class="col-12"> -->
                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>


                        <form class="ajax_results_form" method="GET">

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="branch" style="display: none;">
                                                <option value=""> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branch'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branch'] == $row['id'] ? "selected" : "";
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
                                <div class="col-md-2">
                                    <div class="form-group">

                                        <label class="text-label form-label">Client Type *</label>

                                        <select name="client_type" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">All </option>
                                            <option value="member" <?= @$_REQUEST['client_type'] == "member" ? "selected" : "" ?>> Members</option>
                                            <option value="non-member" <?= @$_REQUEST['client_type'] == "non-member" ? "selected" : "" ?>>Non-Members</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">

                                        <label class="text-label form-label">Gender *</label>

                                        <select name="gender" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">Both </option>
                                            <option value="Male" <?= @$_REQUEST['gender'] == "Male" ? "selected" : "" ?>>Male</option>
                                            <option value="Female" <?= @$_REQUEST['gender'] == "Female" ? "selected" : "" ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Select Savings Account *</label>

                                        <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="actype" style="display: none;">

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
                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Registration Start
                                            Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?= @$_REQUEST['start_date'] ?? null; ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Registration End
                                            Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?? null; ?>" id="exampleInputEmail4" placeholder="End Date">
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
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            Deactivated Accounts
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

                        ?>



                    </div>
                    <div class="card-body">
                        <div class="table-responsive">

                            <table id="clients_datatable" class="table table-striped fixed-layout" style="min-width: 845px;">

                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Saving Product</th>
                                        <th>Name</th>
                                        <th>A/C Balance</th>
                                        <th>Gender</th>
                                        <th>Status</th>
                                        <th>Contacts</th>
                                        <th>Branch</th>
                                        <th>Client Type</th>
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
                <!-- </div> -->



                <!-- </div> -->
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
    <?php
    include('includes/bottom_scripts.php');
    ?>

    <script type="text/javascript">
        $(document).ready(function() {

            bindtoDatatable();

        });


        function bindtoDatatable() {

            var table = $('#clients_datatable').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searchable: true,
                pageLength: 10,
                paging: true,
                ajax: {

                    url: `<?= BACKEND_BASE_URL ?>User/get_all_bank_clients_datatables_deactivated.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>&branchId=<?= @$_REQUEST['branch'] ?>&client_type=<?= @$_REQUEST['client_type'] ?>&gender=<?= @$_REQUEST['gender'] ?>&actype=<?= @$_REQUEST['actype'] ?>&start_date=<?= @$_REQUEST['start_date'] ?>&end_date=<?= @$_REQUEST['end_date'] ?>&client_type_section=all`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        var datatable_data = [];
                        for (let record of data) {
                            var registered_date = new Date(record.ccreatedat).toLocaleDateString('en-GB', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            }).split(' ').join('-');

                            datatable_data.push({
                                'image': `<img class="rounded-circle" width="35" src="${record.profilePhoto}"  onerror="this.onerror=null; this.src='images/account.png';" alt="">`,
                                'accno': `<a class="text-primary" href='client_profile_page.php?id=${encrypt_data(record.userId)}'>${record.actype == '0'?'-':record.membership_no || '-' }</a>`,
                                'actype': record.client_type,
                                'save_name': record.save_name ?? '',
                                'name': `<a class="text-primary" href='client_profile_page.php?id=${encrypt_data(record.userId)}'>${record.firstName} ${record.lastName ? record.lastName : ''} ${record.shared_name ? record.shared_name : ''}</a> `,
                                'acc_balance': `<a class="text-primary" href="member_statement_range.php?id=${record.userId}"> ${number_format(record.acc_balance)} </a>`,
                                'gender': record.gender,
                                'status': `<span class="badge badge-rounded badge-${record.status == 'ACTIVE'? 'success': 'danger'}"> ${record.status} </span>`,
                                'contact': `${record.primaryCellPhone} ${record.secondaryCellPhone ? '/' + record.secondaryCellPhone : ''}`,
                                'branch': `${record.branch_name ? record.branch_name : ''} ${record.branch_location ? '-'+record.branch_location : ''}`,
                                'openingdate': registered_date,
                                'action': `
                                    <div class="dropdown custom-dropdown mb-0">
                                        <div class="btn sharp btn-primary tp-btn"
                                            data-bs-toggle="dropdown">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                                                height="18px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none"
                                                    fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"></rect>
                                                    <circle fill="#000000" cx="12" cy="5" r="2">
                                                    </circle>
                                                    <circle fill="#000000" cx="12" cy="12" r="2">
                                                    </circle>
                                                    <circle fill="#000000" cx="12" cy="19" r="2">
                                                    </circle>
                                                </g>
                                            </svg>
                                        </div>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item"
                                                href="client_profile_page.php?id=${encrypt_data(record.userId)}"> <i class="fa fa-eye"></i> View Account Profile </a>
                                        </div>
                                    </div>
                                `,
                            })
                        }

                        // console.log("return_data ::: ", return_data);

                        return datatable_data;
                    },
                },
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-left" aria-hidden="true"></i>'
                    }
                },

                "columns": [{
                        "data": "image",
                        "width": "30px"
                    },
                    {
                        "data": "accno",
                        "width": "80px"
                    },
                    {
                        "data": "save_name",
                        "width": "100px",
                    },
                    {
                        "data": "name",
                        "width": "170px",
                    },
                    {
                        "data": "acc_balance",
                        "width": "104px",
                    },
                    {
                        "data": "gender",
                        "width": "70px",
                    },
                    {
                        "data": "status",
                        "width": "70px",
                    },
                    {
                        "data": "contact",
                        "width": "200px",
                    },
                    {
                        "data": "branch",
                        "width": '400px',
                    },
                    {
                        "data": "actype",
                        "width": "100px",
                    },
                    {
                        "data": "openingdate",
                        "width": "100px",
                    },
                    {
                        "data": "action",
                        "width": "60px",
                    },
                ],
            })

        }
    </script>

</body>

</html>