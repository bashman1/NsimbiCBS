<?php
require_once(__DIR__ . '/includes/functions.php');


$title = "MEMBER PORTAL";

require_once(__DIR__ . '/includes/response.php');

$response = new Response();
$user = $response->getClientPortalDetails($_GET['cid'], $_GET['uid']);

$response = new Response();
if ($user[0]['tname'] != '') {
    // $_SESSION['success'] = "Welcome to " . $user[0]['tname'] . " Client's Portal";
} else {

    // $_SESSION['success'] = "Welcome to your Institution's Client's Portal";
}
?>

<?php require_once('includes/head_tag.php'); ?>

<body>

    <?php require_once('includes/preloader.php'); ?>

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
        <script src="https://checkout.flutterwave.com/v3.js"></script>
        <?php
        // include('client_nav_bar.php');
        include('includes/client_sidebar.php');

        include('client_dash.php');

        ?>

        <?php include('includes/footer.php'); ?>


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

    <?php
    include('includes/client_bottom_scripts.php');
    ?>
    <script type="text/javascript">
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        $(document).ready(function() {

            $.ajax({
                url: '<?php echo BACKEND_BASE_URL; ?>Bank/get_all_dashboard_values.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    let clients = data['data'][0]['clients'] || 0;
                    let active_loans = data['data'][0]['active_loans'] || 0;
                    let repayments = data['data'][0]['repayments'] || 0;
                    let portifolio = data['data'][0]['portifolio'] || 0;
                    $('#clients').html(numberWithCommas(clients));
                    $('#active_loans').html(numberWithCommas(active_loans));
                    $('#repayments').html(numberWithCommas(repayments));
                    $('#portifolio').html(numberWithCommas(portifolio));
                }
            });

        });
    </script>

</body>

</html>