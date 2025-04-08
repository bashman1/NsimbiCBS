<span id="api_base_url" data-value="<?= 'https://app.ucscucbs.net/backend/api/' ?>"></span>

<!--**********************************
        Scripts
    ***********************************-->

<!-- Apex Chart -->

<!-- Dashboard 1 -->
<!-- <script src="./js/dashboard/cards-center.js"></script> -->


<!-- Required vendors -->


<script src="./vendor/global/global.min.js"></script>
<script src="./js/custom.min.js"></script>
<script src="./js/dlabnav-client-init.js"></script>
<script src="./vendor/chart.js/Chart.bundle.min.js"></script>
<!-- Apex Chart -->
<script src="./vendor/apexchart/apexchart.js"></script>

<script src="./vendor/chart.js/Chart.bundle.min.js"></script>

<!-- Chart piety plugin files -->
<script src="./vendor/peity/jquery.peity.min.js"></script>
<script src="./vendor/nouislider/nouislider.min.js"></script>
<script src="./vendor/wnumb/wNumb.js"></script>


<!-- <script src="./js/demo.js"></script> -->

<!-- Datatable -->
<script src="./js/app-scripts.min.js"></script>
<script src="./js/plugins-init/datatables.init.js"></script>
<script src="./js/plugins-init/jquery.validate-init.js"></script>
<script src="./vendor/clockpicker/js/bootstrap-clockpicker.min.js"></script>
<script src="./js/jquery.timepicker.min.js"></script>
<script src="./js/dlabnav-client-init.js"></script>
<script src="./vendor/select2/js/select2.full.min.js"></script>
<script src="./js/plugins-init/select2-init.js"></script>

<script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
<script src="./vendor/jquery-validation/jquery.validate.min.js"></script>
<!-- Form validate init -->
<script src="./js/plugins-init/jquery.validate-init.js"></script>

<script src="./vendor/lightgallery/js/lightgallery-all.min.js"></script>


<!-- Form Steps -->
<script src="./vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js"></script>
<script src="./vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

<script src="./js/tableExport/tableExport.js"></script>
<script type="text/javascript" src="./js/tableExport/jquery.base64.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<!-- <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script> -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="./js/buttons.html5.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<!-- PrintThis Js -->
<script type="text/javascript" src="./js/printThis.js"></script>


<!-- datable export -->
<!-- <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.0/html2canvas.min.js"></script> -->


<script>
    var api_base_url = "<?= BACKEND_BASE_URL; ?>";
    var session_user_id = "<?= $_SESSION['user']['userId']; ?>";
    var session_bank_id = "<?= $_SESSION['user']['bankId']; ?>";
    var session_branch_id = "<?= $_SESSION['user']['branchId']; ?>";

    let datatable_language = {
        paginate: {
            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
        }
    }

    var confirmButtonColor = '#44814E';
    var cancelButtonColor = '#f72b50';
</script>
<!-- Dashboard 1 -->
<script src="./js/dashboard/dashboard-1.js"></script>
<script src="./js/dashboard/my-wallet.js"></script>

<script src="./js/shared_scripts.js"></script>
<script src="./js/data_importer.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#smartwizard').smartWizard();


    });
</script>
<script>
    swal.setDefaults({
        backdrop: "rgb(113 176 230 / 40%)"
    });
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

    $("#select2").select2({
        placeholder: "",
        allowClear: true
    });

    $("#osector").select2({
        placeholder: "",
        allowClear: true
    });
    $("#ocategory").select2({
        placeholder: "",
        allowClear: true
    });
    $("#oscategory").select2({
        placeholder: "",
        allowClear: true
    });
    $("#disability").select2({
        placeholder: "",
        allowClear: true
    });





    $("#authby").select2({
        placeholder: "",
        allowClear: true
    });
    $("#exp_account").select2({
        placeholder: "",
        allowClear: true
    });

    $("#branchselect").select2({
        placeholder: "",
        allowClear: true
    });

    $("#reserveacc").select2({
        placeholder: "",
        allowClear: true
    });
    $("#bankacc").select2({
        placeholder: "",
        allowClear: true
    });
    $("#bank_acc").select2({
        placeholder: "",
        allowClear: true
    });
    $("#payment_methods").select2({
        placeholder: "",
        allowClear: true
    });
    $("#cash_trans").select2({
        placeholder: "",
        allowClear: true
    });
    $("#cash_acc").select2({
        placeholder: "",
        allowClear: true
    });
    $("#credit_account").select2({
        placeholder: "",
        allowClear: true
    });
    $("#journalacc").select2({
        placeholder: "",
        allowClear: true
    });
    $("#clientsselect").select2({
        placeholder: "",
        allowClear: true
    });


    // Export table data to Excel 
    // function exportToExcel(tableId, filename) {
    //     var downloadLink;
    //     var dataType = 'application/vnd.ms-excel';
    //     var tableSelect = document.getElementById(tableId);
    //     var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    //     // Specify file name 
    //     filename = filename ? `${filename}.xls` : 'export.xls';
    //     // Create download link element 
    //     downloadLink = document.createElement('a');
    //     document.body.appendChild(downloadLink);
    //     if (navigator.msSaveOrOpenBlob) {
    //         var blob = new Blob(['\ufeff', tableHTML], {
    //             type: dataType
    //         });
    //         navigator.msSaveOrOpenBlob(blob, filename);
    //     } else {
    //         // Create a link to the file
    //         downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    //         // Setting the file name 
    //         downloadLink.download = filename;
    //         // Triggering the function
    //         downloadLink.click();
    //     }
    // }
    // // Export table data to PDF 
    // function exportToPDF(tableId, filename) {
    //     var doc = new jsPDF('p', 'pt', 'letter');
    //     var table = document.getElementById(tableId);
    //     // Set the table width and align headers 
    //     var tableWidth = doc.internal.pageSize.width - 80;
    //     var tableHeight = doc.internal.pageSize.height - 80;
    //     var content = {
    //         startY: 60,
    //         head: [],
    //         body: []
    //     };
    //     // Generate table headers 
    //     for (var j = 0; j < table.rows[0].cells.length; j++) {
    //         content.head.push(table.rows[0].cells[j].textContent);
    //     }
    //     // Generate table rows
    //     for (var i = 1; i < table.rows.length; i++) {
    //         var rowData = [];
    //         for (var j = 0; j < table.rows[i].cells.length; j++) {
    //             rowData.push(table.rows[i].cells[j].textContent);
    //         }
    //         content.body.push(rowData);
    //     }
    //     // Output table as PDF
    //     doc.autoTable(content);
    //     doc.save(filename ? `${filename}.pdf` : 'export.pdf');
    // }



    $(document).ready(function() {

        $(document).on("click", '.clickable_ref_no', function(e) {
            e.preventDefault();
            handle_trx_ref_click_options($(this));
        });

    });


    function handle_trx_ref_click_options(item) {
        var ref_no = item.attr('ref-no');
        var tid = item.attr('tid');
        $(".transaction-custom-menu").remove();

        var options = "<ul class='transaction-custom-menu'>" +
            "<li class='dropdown-header dropdown-item'>-- " + ref_no + " --</li>" +
            "<li data-action='view' ref-no = '" + ref_no + "' tid = '" + tid + "' class='dropdown-item'>View More Details</li>" +
            // "<li data-action='edit' ref-no = '"+ref_no+"'>Edit Transaction</li>"+
            // "<li data-action='delete' ref-no = '"+ref_no+"'>Delete Transaction</li>"+
            "</ul>";

        item.append(options);
        $(".transaction-custom-menu").show();

        // If the menu element is clicked
        $(".transaction-custom-menu li").click(function() {

            // This is the triggered action name
            var action = $(this).attr("data-action");
            var ref_no = $(this).attr("ref-no");
            var tid = $(this).attr("tid");
            // console.log(`TRXN ID: ${tid}`);
            // console.log(`TRXN REF: ${ref_no}`);
            // console.log(`TRXN ACTION: ${action}`);

            if (action) {
                // Hide it AFTER the action was triggered
                $(".transaction-custom-menu").hide(100);
                $.ajax({
                    url: `<?= BACKEND_BASE_URL ?>Bank/get_trxn_details_general.php?tid=${tid}`,
                    type: 'POST',
                    data: {
                        action: action,
                        ref_no: ref_no,
                        tid: tid,
                    },
                    beforeSend: function() {
                        $(".transaction-custom-menu").hide(100);
                        alert_loading('Retrieving transaction info ...', false);

                    },
                    cache: false,
                    error: function(data) {
                        handleError('Error processing request...');
                    },
                    success: function(response) {
                        // $("#divtoaddresult").html(response);
                        // console.log(response);
                        // console.log(response['data'][0]);
                        //success loading content
                        showAjaxModalPage(response['data'][0]);
                    }
                });

            }


        });

        // If the document is clicked somewhere
        $(document).bind("mousedown", function(e) {

            // If the clicked element is not the menu
            if (!$(e.target).parents(".transaction-custom-menu").length > 0) {
                // Hide it
                $(".transaction-custom-menu").hide(100);
            }
        });

    }
</script>


<script>
    // Set the date we're counting down to
    // var countDownDate = new Date("Dec 19, 2023 11:36:10").getTime();
    var countDownDate = new Date("<?= @$_SESSION['working_hours_end_at'] ?>").getTime();

    // Update the count down every 1 second
    var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();
        countDownDate = countDownDate || now

        if (countDownDate == now) {
            clearInterval(x);
            return;
        }

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        let countdownHours = document.getElementById("hours");
        let countdownMinutes = document.getElementById("minutes");
        let countdownSeconds = document.getElementById("seconds");

        if (!countdownHours) {
            clearInterval(x);
            return;
        }

        hours = hours || 0;
        minutes = minutes || 0;
        seconds = seconds || 0;
        countdownHours.innerHTML = hours;
        countdownMinutes.innerHTML = minutes;
        countdownSeconds.innerHTML = seconds;

        if (hours == 1 && minutes == 0 && seconds == 0) {
            alert_info("You only have 1 hour left");
        } else if (hours == 0 && minutes == 30 && seconds == 0) {
            alert_info("You only have 30 minutes left");
        } else if (hours == 0 && minutes == 10 && seconds == 0) {
            alert_info("You only have 10 minutes left");
        } else if (hours == 0 && minutes == 5 && seconds == 0) {
            alert_info("You only have 5 minutes left");
        }

        // If the count down is finished, reset count down to zero
        if (distance < 0) {
            clearInterval(x);
            countdownHours.innerHTML = '00';
            countdownMinutes.innerHTML = '00';
            countdownSeconds.innerHTML = '00';
            window.location.reload();
        }
    }, 1000);


    /* --------- print -------*/
    function h_print_div(div) {
        $("#" + div).printThis({
            importCSS: true,
            importStyle: true,
            loadCSS: ""
        });
    }

    function h_print_window() {
        $("#print-header-title").html($("#main-page-content-title.page-title").html());

        if (modalIsOpen()) {
            $("#pageGeneralModal").printThis({
                importCSS: true,
                importStyle: true,
                loadCSS: "",
                header: $(".print_header"),
                footer: $("#print_footer")
            });
        } else {
            $("#main-page-content-section").printThis({
                importCSS: true,
                importStyle: true,
                loadCSS: "",
                header: $(".print_header"),
                footer: $("#print_footer")
            });
        }
    }
</script>

<?php if (isset($_SESSION['success_message'])) { ?>
    <script>
        alert_success('<?= $_SESSION['success_message'] ?>');
    </script>
<?php
    unset($_SESSION['success_message']);
} ?>
<?php if (isset($_SESSION['success_message_confirm'])) { ?>
    <script>
        alert_success_with_confirm('<?= $_SESSION['success_message_confirm'] ?>', '<?= $_SESSION['success_message_tid'] ?>', '<?= $_SESSION['success_message_type'] ?>');
    </script>
<?php
    unset($_SESSION['success_message_confirm']);
    unset($_SESSION['success_message_tid']);
    unset($_SESSION['success_message_type']);
} ?>

<?php if (isset($_SESSION['error_message'])) { ?>
    <script>
        alert_error('<?= $_SESSION['error_message'] ?>');
    </script>
<?php
    unset($_SESSION['error_message']);
} ?>