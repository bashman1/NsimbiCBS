$("body").on(
  "change",
  "#individual_clients_upload,#institution_clients_upload, #group_clients_upload, #transactions_upload,#loans_upload,#fds_upload,#shares_register_upload,#share_purchases_upload,#share_transfers_upload",
  function (event) {
    // console.log("selected");
    validateFile($(this));
  }
);

function validateFile(ele) {
  var fileId = ele.attr("id");
  console.log("FIELD ID VALIDATE :: ", fileId);
  var files = document.getElementById(fileId).files;
  if (files.length == 0) {
    ele.val("");
    alert_error("Please choose any file...");
    return;
  }
  var filename = files[0].name;
  var filename_array = filename.split(".");
  console.log("filename_array ::: ", filename_array);
  let filename_without_extension = filename_array[0];
  console.log("filename_without_extension ::: ", filename_without_extension);
  var opening_brackets_index = filename.indexOf("(");

  var actual_file_name = filename_without_extension;
  if (opening_brackets_index >= 0) {
    actual_file_name = filename.split("(")[0];
    actual_file_name = actual_file_name.trim();
  }

  filename_array = actual_file_name.split("_");
  console.log("new filename_array ::: ", filename_array);

  for (let item of filename_array) {
    if (filename_array[filename_array.length - 1] == "") filename_array.pop();
  }

  console.log("new filename_array final::: ", filename_array);
  actual_file_name = filename_array.join("_");
  console.log("actual_file_name :: ", actual_file_name);
  // return;

  var extension = filename.substring(filename.lastIndexOf(".")).toUpperCase();
  if (extension == ".XLS" || extension == ".XLSX") {
    //Here calling another method to read excel file into array
    excelFileToArray(files[0], fileId, actual_file_name);
    ele.val("");
  } else {
    ele.val("");
    alert_error("Please select a valid excel file.");
  }
}

async function excelFileToArray(file, fileId, filename) {
  try {
    let filename_parts = filename.split("|");
    let max_records = 900;
    console.log("fieldId", fileId);
    let date_fields = ["RegistrationDate", "DateOfBirth", "DateCreated"];
    let number_fields = [
      "Amount",
      "AmountPaid",
      "LoanAmount",
      "AccountBalance",
      "FreezedAmount",
      "LoanWallet",
      "MembershipFee",
      "TrxnAmount",
      "TrxnCharge",
      "InterestAmount",
      "NumberOfShares",
      "CurrentShareValue",
      "PrincipleInArrears",
      "PrincipleBalance",
      "InterestInArrears",
      "InterestBalance",
      "PenaltyBalance",
    ];

    let percentage_fields = ["InterestRate"];

    var form = $(`#${fileId}`).parents("form");
    $(`#${fileId}_data_actions`).addClass("hide");
    let data_importer_totals_el = $(`#${fileId}_data_importer_totals`);
    if (data_importer_totals_el.length) data_importer_totals_el.html("");

    let upload_section = $(`#${fileId}_data_section`);
    upload_section.html("");

    /**
     * validate file being imported. Users should upload the exact templates that have been vailed by
     * UCSCU CBS
     */
    let accepted_files = form.find(".accepted_files").val();
    let accepted_files_array = accepted_files.split("|");
    for (let file_part of accepted_files_array)
      if (accepted_files_array.indexOf(filename) < 0) {
        alert_info(
          "Invalid Excel Importer. Please upload the exact template you downloaded!"
        );
        return;
      }

    alert_loading("Loading Data please wait...");

    var sheet_data = [];

    var reader = new FileReader();
    reader.readAsArrayBuffer(file);
    reader.onload = async function (e) {
      e.preventDefault();
      var data = new Uint8Array(reader.result);
      var work_book = XLSX.read(data, {
        type: "array",
        cellDates: true,
        dateNF: "yyyy/mm/dd;@",
        raw: true,
      });
      var sheet_name = work_book.SheetNames;

      sheet_data = XLSX.utils.sheet_to_json(work_book.Sheets[sheet_name[0]], {
        header: 1,
        raw: false,
      });

      let head_row = 1;
      let datatable_data = [];
      let columns = sheet_data[head_row].map((ele) =>
        ele.trim().replace(/\*/g, "")
      );

      let calculate_loan_totals = false;
      if (columns.indexOf("LoanNumber") >= 0) calculate_loan_totals = true;

      let is_detailed_version = false;
      let is_short_version = false;

      if (calculate_loan_totals) {
        if (columns.indexOf("AmountPaid") >= 0) {
          is_short_version = true;
        } else {
          is_detailed_version = true;
        }
      }

      // console.log("columns:: ", columns);

      sheet_data = sheet_data.splice(2);
      sheet_data = sheet_data.filter(function (data) {
        return data.length > 0;
      });

      if (sheet_data.length > max_records) {
        alert_info(
          `You can only upload a maximum of ${max_records} records in a single batch`
        );
        return;
      }

      // console.log(sheet_data);

      let i = 0;
      // let total_loan_amount = 0;
      // let total_penalty_balance = 0;
      // let total_principal_balance = 0;
      // let total_interest_balance = 0;
      // let total_principal_arrears = 0;
      // let total_interest_arrears = 0;

      let lookup = {};

      for (var row = 0; row < sheet_data.length; row++) {
        datatable_data[i] = {};
        var row_data = {};
        for (var cell = 0; cell < sheet_data[row].length; cell++) {
          let columnName = columns[cell].trim();
          // if()
          let columnIndex = columns.indexOf(columnName);
          let columnValue = sheet_data[row][columnIndex];

          // console.log(`${columnName} = ${columnValue}`)

          if (columnValue !== undefined) {
            // if (date_fields.indexOf(columnName) >= 0) {
            //   datatable_data[i][columnName] = ExcelDateToJSDate(columnValue);
            // } else
            if (number_fields.indexOf(columnName) >= 0) {
              datatable_data[i][columnName] =
                number_format_data_importer(columnValue);
              lookup[columns[cell]] =
                (lookup[columns[cell]] || 0) +
                parseInt(columnValue.replace(/,/g, ""));
            } else if (percentage_fields.indexOf(columnName) >= 0) {
              datatable_data[i][columnName] =
                parseFloat(columnValue).toFixed(1);
            } else {
              datatable_data[i][columnName] = columnValue;
            }
          } else {
            datatable_data[i][columnName] = "";
          }
        }
        i++;
      }

      console.log("lookup ::: ", lookup);

      /**
       * geenerate datatable head
       */
      let table = `<table class="table display" id="${fileId}_datatable">`;
      var datatable_columns = [];
      table += "<thead>";
      table += "<tr>";
      for (let column of columns) {
        table += ` <td>${column}</td> `;
        datatable_columns.push({
          data: `${column}`,
        });
      }
      table += "</tr>";
      table += "</thead>";

      // $(`#${fileId}_datatable`).append(table_header);
      upload_section.append(table);

      /**
       * generate datatable body
       */
      var datatable_id = `#${fileId}_datatable`;
      // console.log("datatable_id:: ", datatable_id);
      var datatable = $(datatable_id);
      datatable.dataTable({
        destroy: true,
        language: {
          paginate: {
            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
            previous:
              '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
          },
        },
        aaData: datatable_data,
        columns: datatable_columns,
      });

      if (datatable_data.length) {
        $(`#${fileId}_data_actions`).removeClass("hide");
      }

      form.find(".actual_data").val(JSON.stringify(datatable_data));
      let file_name_input = `<input type="hidden" name="file_name" value="${filename}" >`;

      /**
       * calculate totals if any
       */

      if (data_importer_totals_el) {
        let data_importer_totals_el_data = ``;
        for (let key in lookup) {
          data_importer_totals_el_data += `<button type="button" class="btn btn-rounded btn-secondary me-3 mb-2">
          Total ${split_pacal_case(key)}: <strong> ${number_format(
            lookup[key]
          )} </strong> <span></span>
      </button>`;
        }

        data_importer_totals_el.html(data_importer_totals_el_data);
      }

      form.append(file_name_input);
      alert_close();
    };
  } catch (e) {
    console.error(e);
  }
}

$("body").on(
  "submit",
  "form#data_importer_individual_clients, form#data_importer_institution_clients, form#data_importer_group_clients, form#data_importer_transactions,#data_importer_shares_register,#data_importer_share_purchases,#data_importer_share_transfers,#data_importer_loans, #data_importer_fds",
  function (event) {
    event.preventDefault();
    let url = $(this).attr("action");
    let action = $(this).attr("action");
    let formId = $(this).attr("id");
    let reload_page = $(this).data("reload-page");
    reload_page = true;
    // let form = $(`#${formId}`);
    let form = document.getElementById(formId);
    let fileInput = $(this).find("input[type=input]");
    let fileId = fileInput.attr("id");

    $(`#${fileId}_data_section`).html("");
    console.log("action ", action);
    console.log("formId ", formId);
    event.preventDefault();
    swal({
      title: "Are you sure?",
      text: "Click Confirm to Import Data!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Confirm!",
    }).then(function (result) {
      console.log(result);
      if (result.value) {
        $.ajax({
          url: `${api_base_url}Bank/${url}.php?auth_id=${session_user_id}&bank_id=${session_bank_id}`,
          type: "POST",
          data: new FormData(form),
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
            alert_loading(
              "Importing Data. Please wait as this might take a while..."
            );
          },
          success: async function (response) {
            console.log(response);

            if (typeof response.message == "object") {
              response = response.message;
            }

            let records_with_errors = response.records_with_errors;
            let success_message = "Data successfully Imported";

            if (records_with_errors) {
              success_message += `<br> <small class="text-danger">However, ${records_with_errors} record(s) were recorded with errors. Click on the Import error log to track the errors </small> `;
            }
            if (response.success) {
              // alert_success(`${success_message}`);

              let confirmed = await swal({
                title: "",
                html: success_message,
                type: "success",
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                // confirmButtonText: "Yes Confirm!",
                // cancelButtonText: "No, cancel!",
              });

              // if (!confirmed.value) return;

              clear_data_importer_data(fileInput);

              if (reload_page) {
                // if (response.records_with_errors) return;
                setTimeout(() => {
                  window.location.reload();
                  // console.log("should reload");
                }, 200);
              }
            } else {
              alert_error(response.message);
            }
          },
          error: function (e) {
            alert_error("Something went wrong");
            console.log(e);
          },
        });
      }
    });
  }
);

$("body").on("click", ".clear_data_importer_data", async function () {
  let confirmed = await swal({
    title: "",
    text: "Click Confirm to Clear Imported Data as this action wont be reversed!",
    type: "warning",
    showCancelButton: true,
    confirmButtonText: "Confirm!",
  });

  if (!confirmed.value) return;

  clear_data_importer_data($(this));
});

async function clear_data_importer_data(ele) {
  let form = ele.parents("form");
  let card = ele.parents(".card-body");

  let file_id = form.find('.form-file-input, input[type="file"]').attr("id");
  form.trigger("reset");
  card.find(`#${file_id}_data_section`).html("");
  card.find(`#${file_id}_data_actions`).addClass("hide");
  card.find(`#${file_id}_data_importer_totals`).html("");
}
