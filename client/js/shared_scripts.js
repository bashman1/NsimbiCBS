$("select.select2").each(function () {
  $(this).select2({ dropdownParent: $(this).parents("modal") });
});

$(".lightgallery").lightGallery({
  pager: true,
  mode: "lg-fade",
  thumbnail: true,
});

$(".dataTable:not(#example3)").dataTable({
  language: {
    paginate: {
      next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
      previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
    },
  },
});
$(".reportDataTable").dataTable({
  language: {
    paginate: {
      next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
      previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
    },
  },
  iDisplayLength: 100,
});

$(".export-datatable").each(function () {
  $(this).dataTable({
    dom: "Bfrtip",
    order: [
      [
        $(this).data("order-column") || 0,
        $(this).data("order-direction") || "desc",
      ],
    ],
    // buttons: ["copy", "csv", "excel", "print"],
    buttons: [
      {
        extend: "copyHtml5",
        title: $(this).data("title"),
        footer: true,
        exportOptions: {
          columns: ":not(.not-export)",
        },
      },
      {
        extend: "csvHtml5",
        title: $(this).data("title"),
        footer: true,
        exportOptions: {
          columns: ":not(.not-export)",
        },
      },

      {
        extend: "pdfHtml5",
        orientation: $(this).data("orientation") || "portrait",
        title: $(this).data("title"),
        pageSize: "LEGAL",
        footer: true,
        exportOptions: {
          columns: ":not(.not-export)",
        },
      },

      {
        extend: "excelHtml5",
        title: $(this).data("title"),
        footer: true,
        exportOptions: {
          columns: ":not(.not-export)",
        },
      },
      {
        extend: "print",
        title: $(this).data("title"),
        footer: true,
        exportOptions: {
          columns: ":not(.not-export)",
        },
      },
    ],
    language: {
      paginate: {
        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
      },
    },
  });
});

$(".timepicker").timepicker({
  timeFormat: "h:mm a",
  interval: 5,
  minTime: "7",
  defaultTime: "",
  maxTime: "6:00pm",
  startTime: "7:00am",
  dynamic: false,
  dropdown: true,
  scrollbar: true,
});

$("body").on("click", ".delete-trxn", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "Deleting a transaction completely removes it from the system without any trace!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});
$("body").on("click", ".undo_disburse", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "This will delete all the disbursement transactions (inclusive on_disbursement fees) attached to this loan and deduct the disbursed amount from the customer's account. So be sure that the customer has enough funds on account to complete this reversal!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});
$("body").on("click", ".reschedule_loan", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "This will redirect you to the page where you can adjust loan terms?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});

$("body").on("click", ".delete_member", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "This will delete permanently the member & affiliated trxns from the system",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});

$("body").on("click", ".reactivate_loan", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "This action shall restore the previously closed loan to an active loan and valid status, allowing the borrower to resume regular payments against the loan.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});
$("body").on("click", ".edit-trxn", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "This will open a page that enables you to change the transaction details.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});
$("body").on("click", ".reverse-trxn", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "A reverse transaction which nullifies the impact of this transaction will be created while maintaining a record of the original transaction",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});

$("body").on("click", ".un_freeze_acc", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "This will un-block the freezed amount on this account, and it will be available for withdrawal at clients' will",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});

$("body").on("click", ".delete-record", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "You won't be able to revert this action!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});
$("body").on("click", ".disburse-loan", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "Hope you've checked for all the necessary information (like Guarantors, Collaterals) & attachments before performing this action!\n If not, then please do thorough checking to avoid disbursement of loans with missing attachments or details. Note that incase of any missing details, you will be responsible for this action!\n\n",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes I Confirm!",
    cancelButtonText: "No, Let me Check first!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      // window.location.href = url;
      alert_close();
      $(".modal").modal("hide");
      $(".bd-example-modal-lg4").modal("show");
    }
  });
});

$("body").on("click", ".approve-loan", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Are you sure?",
    text: "Hope you've checked for all the necessary information (like Guarantors, Collaterals) & attachments (appraisal report, e.t.c) before performing this action!\n If not, then please do thorough checking to avoid approval of loans with missing attachments or details. Note that incase of any missing details, you will be responsible for this action!\n\n",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes I Confirm!",
    cancelButtonText: "No, Let me Check first!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      // window.location.href = url;
      //  $(".bd-example-modal-lg3").show();

      alert_close();
      $(".modal").modal("hide");
      $(".bd-example-modal-lg3").modal("show");
    }
  });
});
$("body").on("click", ".committee-loan", function (e) {
  e.preventDefault();
  let url = $(this).attr("href") || $(this).data("href");
  swal({
    title: "Befor Continuing?",
    text: "Download this report, print it out and present it to the loan committee members to be guided and to capture their comments and approvals.\nAfter scan and upload the pdf document or png under attachments against this loan.\n",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Download",
    cancelButtonText: "Cancel",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = url;
    }
  });
});
$("body").on("click", ".export-excel", function () {
  // var exportType = $(this).data("type") || "excel";
  var exportType = "excel";
  var content = $(this).data("content");
  $(`#${content}`).tableExport({
    type: exportType,
    escape: "false",
    ignoreColumn: [],
  });
});

$("body").on("change", ".select-branch", function () {
  let branchName = $(this).children("option:selected").data("branch-name");
  // console.log("branchName", branchName);
  $(this).parents("form").find(".branch-name").val(branchName);
});

$("body").on("change", ".select-frequency", function (event) {
  $(".frequency-singular").html("");
  $(".frequency-plural").html("");

  if ($(this).val()) {
    let frequency_singular = $(this)
      .children("option:selected")
      .data("frequency-singular");
    let frequency_plural = $(this)
      .children("option:selected")
      .data("frequency-plural");
    $(".frequency-singular").html(frequency_singular);
    $(".frequency-plural").html(frequency_plural);
  }
});
$("body").on("click", ".confirm-action", function (e) {
  e.preventDefault();
  let url = $(this).data("href") || $(this).attr("href");
  swal({
    title: "Are you sure?",
    text: "Click Yes Confirm to Proceed!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Yes Confirm!",
    cancelButtonText: "No, cancel!",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      alert_loading();
      window.location.href = url;
    }
  });
});

$("body").on("change", ".set-permissions", function () {
  let child_permissions = $(this).data("child-permissions");
  if ($(this).prop("checked")) {
    $(`.${child_permissions}`).prop("checked", true);
  } else {
    $(`.${child_permissions}`).prop("checked", false);
  }
});

$("body").on("change", ".child-permission", function () {
  let parent_permission = $(this).data("parent-permission");
  let parent_ele = $(`#${parent_permission}`);
  parent_ele.prop("checked", true);

  let child_permissions = parent_ele.data("child-permissions");

  if ($(`input.${child_permissions}:checked`).length == 0) {
    parent_ele.prop("checked", false);
  }
});

$("body").on("change", ".activate-sections", function () {
  let active_sections =
    $(this).data("activate-sections") ||
    $(this).data("sections") ||
    $(this).children("option:selected").data("sections") ||
    "";

  let inactive_sections = $(this).data("deactivate-sections") || "";
  // console.log("active_sections", active_sections);

  let active_sections_array = active_sections ? active_sections.split(",") : [];
  let inactive_sections_array = inactive_sections
    ? inactive_sections.split(",")
    : [];
  // console.log("inactive_sections_array", inactive_sections_array);

  let activate =
    $(this).data("activate") ||
    $(this).children("option:selected").data("activate");
  let is_checked = $(this).prop("checked");

  if (!activate && inactive_sections_array.length == 0) {
    inactive_sections_array = active_sections_array;
    active_sections_array = [];
  }

  // console.log("activate", activate);
  // console.log("is_checked", is_checked);
  // console.log("active_sections_array", active_sections_array);

  for (let sections_class of active_sections_array) {
    $(`.section-${sections_class}`).each(function () {
      $(this).addClass("hide");
      if (activate || is_checked) {
        $(this).removeClass("hide");
      }

      $(this)
        .find("input,select,textarea")
        .each(function () {
          $(this).removeAttr("required");

          if (
            $(this).attr("type") == "checkbox" ||
            $(this).attr("type") == "radio"
          ) {
            $(this).prop("checked", false);
          }

          if ((activate || is_checked) && $(this).data("is-required")) {
            $(this).attr("required", "required");
          }
        });
    });
  }

  for (let sections_class of inactive_sections_array) {
    $(`.section-${sections_class}`).each(function () {
      $(this).addClass("hide");
      $(this)
        .find("input,select,textarea")
        .each(function () {
          $(this).removeAttr("required");

          if (
            $(this).attr("type") == "checkbox" ||
            $(this).attr("type") == "radio"
          ) {
            $(this).prop("checked", false);
          } else {
            /**
             * if input is a number
             */
            if (
              $(this).attr("type") == "number" ||
              $(this).hasClass("comma_separated")
            ) {
              var default_value = $(this).data("default-value") || 0;
              $(this).val(default_value);
            } else {
              $(this).val("");
            }
          }
        });
    });
  }
});

$("body").on("change", ".enable-disable", function () {
  let sections =
    $(this).data("enable") ||
    $(this).children("option:selected").data("enable") ||
    "";
  let sections_array = sections.split(",");

  let enabled = false;
  if ($(this).prop("checked")) {
    enabled = true;
  }

  for (let sections_class of sections_array) {
    $(`.${sections_class}`).each(function () {
      // console.log($(this));
      $(this).removeAttr("disabled");

      if (enabled) {
        $(this).val($(this).data("previous-value"));
      } else {
        $(this).attr("disabled", "disabled");
        $(this).val("");
      }
    });
  }
});

$("body").on("keyup change", ".comma_separated", function (evt) {
  var $input = $(this);
  $input.parent("div").find("span.input-error").remove();
  var max = $(this).data("max");
  max = max ? parseFloat(max) : undefined;

  var min = $(this).data("min");
  min = min ? parseFloat(min) : undefined;

  var $value = $(this).val();
  var charCode = evt.which ? evt.which : event.keyCode;
  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    $value = $input
      .val(
        $input
          .val()
          .replace(/[^\d]+/g, "")
          .replace(/,/g, "")
      )
      .val();

    //       let regex = /([+-]?[0-9|^.|^,]+)[\.|,]([0-9]+)$/igm
    // let result = regex.exec($value);
    // console.log("Regex result: " + result);
    // $value = result? result[1].replace(/[.,]/g, "")+ "." + result[2] : $value.replace(/[^0-9-+]/g, "");

    // console.log("Value Start ::: ", $value);
    // return;
  } else {
    $value = $value != null ? $value.replace(/,/g, "") : $value;
  }
  $value = parseFloat($value);
  // console.log("value Final :: ", $value);

  // return;
  if (isNaN($value)) {
    $input.val(0);
  } else {
    if (max && $value > max) {
      alert_info(`Can not be greater than ${number_format(max)}`);
      $input.val(0);
      return;
    }

    if (min && $value < min) {
      // alert_info(`Can not be less than ${number_format(min)}`);

      $input
        .parent("div")
        .append(
          `<span class="input-error error"> Can not be less than ${number_format(
            min
          )} </span>`
        );

      // $input.val(number_format(min));
      // return;
    }

    // console.log("Final Value Final :: ", $value);
    $(this).val(number_format(parseFloat($value)));
    if (!parseFloat($value)) $value = 0;
  }
});

$("body").on(
  "submit",
  "form:not(.custom-form,.confirm-form-submission,#activate,#deactivate,.data-importer-form, #delete_member)",
  async function (e) {
    var form = $(this);
    var form_id = form.attr("id");
    var submit_btn = form.find('button[type="submit"]');
    var confirm_submission = false;
    if (form.hasClass("confirm-submission")) confirm_submission = true;

    var form_elements = form.find("input, select, textarea");

    // await form_elements.each(function () {

    // });

    // alert_loading(null, false);
    // return true;

    if (form.hasClass("valid-form")) {
      alert_loading(null, false);
      return true;
    } else {
      let has_error = false;
      await form_elements.each(function () {
        var ele = $(this);
        form.removeClass("valid-form");
        var ele_value = $(this).val();
        ele.parent("div").find("span.input-error").remove();
        var label_text = $(this).data("error-label");
        label_text = label_text || $(this).parent("div").find("label").text();

        if (ele.attr("required") && ele_value == "") {
          ele
            .parent("div")
            .append(
              `<span class="input-error text-danger">This field is required</span>`
            );
          return false;
        }

        if ($(this).data("max")) {
          ele_value = convert_to_number(ele_value);
          var max_value = convert_to_number($(this).data("max"));
          if (ele_value > max_value) {
            alert_error(`${label_text} can not be greater than ${max_value}`);
            has_error = true;
            return false;
          }
        }

        if ($(this).data("min")) {
          ele_value = convert_to_number(ele_value);
          var min_value = convert_to_number($(this).data("min"));
          if (ele_value < min_value) {
            alert_error(`${label_text} can not be less than ${min_value}`);
            has_error = true;
            return false;
          }
        }

        if ($(this).hasClass("phone-number")) {
          if (ele_value && !validPhone(ele_value)) {
            ele
              .parent("div")
              .append(
                `<span class="input-error text-danger">Invalid Phone Number</span>`
              );
            alert_error(`${label_text}: Invalid Phone Number`);
            has_error = true;
            return false;
          }
        }

        if ($(this).hasClass("nin") && ele_value && !validNiN(ele_value)) {
          ele
            .parent("div")
            .append(
              `<span class="input-error text-danger">Invalid NIN Number</span>`
            );
          alert_error(`${label_text}: Invalid NIN Number`);
          has_error = true;
          return false;
        }
      });

      if (has_error) {
        e.preventDefault();
      } else {
        if (confirm_submission) {
          e.preventDefault();
          let confirmed = await swal({
            title: "",
            text: "Click Yes Confirm to Proceed!",
            type: "warning",
            confirmButtonColor,
            cancelButtonColor,
            showCancelButton: true,
            confirmButtonText: "Yes Confirm!",
            cancelButtonText: "No, cancel!",
          });

          if (confirmed.value) {
            form.addClass("valid-form");
            submit_btn.trigger("click");
            return false;
          } else {
            return false;
          }
        }

        alert_loading(null, false);
        form.addClass("valid-form");
        // // form.submit();
        // if (form_id) {
        //   // $(`form#${form_id}`).submit();
        // } else {
        // }
        // submit_btn.trigger("click");
      }
    }

    return true;
  }
);

$("body").on(
  "submit",
  ".custom-form,.confirm-form-submission",
  async function (event) {
    event.preventDefault();
    let url = $(this).attr("action");
    let action = $(this).attr("action");
    let formId = $(this).attr("id");
    let confirm = $(this).data("confirm-action");
    let action_btn = $(this).find("button.action-btn");
    let reload_page = $(this).data("reload-page");
    let action_btn_text = action_btn.text();

    let form = $(this);
    // form.trigger("reset");
    // return;
    // console.log("action ", action);
    // console.log("formId ", formId);

    if (confirm) {
      let confirmed = await swal({
        title: "",
        text: "Click Yes Confirm to Proceed!",
        type: "warning",
        confirmButtonColor,
        cancelButtonColor,
        showCancelButton: true,
        confirmButtonText: "Yes Confirm!",
        cancelButtonText: "No, cancel!",
      });

      // console.log();
      if (!confirmed.value) return;
    }

    url += `?auth_id=${session_user_id}&bank_id=${session_bank_id}&branch_id=${session_branch_id}`;

    $.ajax({
      url,
      type: "POST",
      data: new FormData($("#" + formId)[0]),
      contentType: false,
      cache: false,
      processData: false,
      beforeSend: function () {
        action_btn.attr("disabled", "disabled").text("Processing...");
        alert_loading();
      },
      success: function (response) {
        action_btn.text(action_btn_text).removeAttr("disabled");
        // console.log(response);
        if (response.success) {
          alert_success(response.message);
          if (!form.data("retain-form-data")) {
            form.trigger("reset");
          }

          if (reload_page) {
            window.location.reload();
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
);

function convert_to_number(input) {
  if (input) {
    input = String(input);
    // console.log("input", input);
    input = input.replace(/UGX/g, "");
    input = input.replace(/ /g, "");
    input = input.replace(/,/g, "");
    input = parseFloat(input);
    return input;
  }
  return 0;
}

function number_format_data_importer(num) {
  if (num) {
    num = num.replace(/UGX/g, "");
    num = num.replace(/ /g, "");
    num = num.replace(/,/g, "");
    num = parseFloat(num);
    return num.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
  }
  return 0;
}

function number_format(num) {
  if (num) {
    // num = num.replace(/,/g, "");
    num = parseFloat(num);
    return num.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
  }
  return 0;
}

function split_pacal_case(string) {
  if (string) {
    return string.replace(/([a-z])([A-Z])/g, "$1 $2");
  }

  return "";
}

function to_normal_date(date) {
  if (!date) return "-- -- ----";
  return new Date(date)
    .toLocaleDateString("en-GB", {
      day: "numeric",
      month: "short",
      year: "numeric",
    })
    .split(" ")
    .join("-");
}

function ExcelDateToJSDate(date) {
  return to_normal_date(date);
}

function alert_success(message) {
  message = message || "Action Successful";
  swal({
    title: "Success",
    html: message,
    type: "success",
    allowOutsideClick: false,
    allowEscapeKey: false,
  });
}
function alert_success_with_confirm(message, tid, type) {
  message = message || "Action Successful";
  tid = tid || 0;
  type = type || "D";

  swal({
    title: "Success",
    text: message,
    type: "success",
    showCancelButton: true,
    confirmButtonColor,
    cancelButtonColor,
    confirmButtonText: "Print Receipt",
    cancelButtonText: "Close",
  }).then(function (result) {
    // console.log(result);
    if (result.value) {
      window.location.href = `receipt.php?id=${tid}&type=${type}`;
    } else {
      if (type === "D") {
        window.location.href = `search_client.php`;
      } else if (type === "W") {
        window.location.href = `withdraw_search_client.php`;
      } else {
        window.location.href = `pay_school_fees_search.php`;
      }
    }
  });
}

function alert_error(error, title = "Error") {
  error = error || "Action Failed";
  swal({
    title: title,
    text: error,
    type: "error",
    allowOutsideClick: false,
    allowEscapeKey: false,
  });
}

function alert_info(message) {
  swal({
    title: "",
    text: message,
    type: "info",
    allowOutsideClick: false,
    allowEscapeKey: false,
  });
}

function alert_loading(message, showConfirmButton = false) {
  message = message || "please wait...";
  let body = `<div>`;
  // body += `<div> <i class="fas fa-spinner fa-spin"></i> </div>`;
  body += `<div> <img src='images/ajax-loader-report.gif' width="50px" /> </div>`;
  body += `<div class="mt-1"> ${message} </div>`;
  body += `</div>`;
  swal({
    html: body,
    // icon: "/images/ajax-loader-report.gif",
    buttons: false,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton,
    //icon: "success"
  });
}
function handleError(message, showConfirmButton = true) {
  message =
    message ||
    "Sorry, looks like there are some errors detected, please try again.";

  swal({
    html: message,
    icon: "error",
    showConfirmButton,
    confirmButtonText: "Close!",
  });
}

function getJsonResponse(response) {
  try {
    return $.parseJSON(response);
  } catch (e) {
    handleError();
  }
}
function showAjaxModalPage(content) {
  // console.log(content);
  // console.log(content.id);

  var details = `
  <div class="card">
	<div class="card-body">

		<h4 class="mt-0 header-title">
			<a target="_blank" href="receipt.php?id=${content.id}&type=${
    content.type
  }" class="btn btn-outline-primary" ><i class="ti-print"></i> View Receipt</a>

	<a target="_blank" href="edit_trxn_general.php?tid=${
    content.id
  }&t=${encrypt_data(content.mid)}&type=${
    content.type
  }" class=" btn btn-outline-warning confirm edit-trxn"><i class="ti-pencil"></i> Edit</a>

					<a href="reverse_trxn.php?id=${
            content.id
          }"  class="btn btn-outline-info confirm reverse-trxn"><i class="ti-back-right"></i> Reverse</a>
				<a  href="trash_trxn_general.php?id=${content.id}&type=${content.type}&lid=${
    content.lid
  }" class="btn btn-outline-danger confirm delete-trxn"><i class="ti-trash"></i> Delete</a>		</h4>


		
		<hr class="hr-dashed">

		<div class="row pricingTable1">
			<div class="col-md-6">
				<h4 class="mt-0 header-title">Transaction Details</h4>
				<p class="text-muted mb-3">...</p>

				<hr class="hr-dashed">

				<ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>ACCOUNT NAME:</b> : ${
            content.name
          }</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>ACCOUNT NUMBER: </b> : ${
            content.acno
          }</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>ACCOUNT TYPE: </b> : ${
            content.actype
          }</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>TELLER NAME: </b> : ${
            content.auth
          }</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>TELLER BRANCH: </b> : ${
            content.branch
          }</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>DEPOSITED BY: </b> : ${
            content.actionby
          }</li>

					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>TRXN REF: </b> : ${
            content.ref
          }</li>
          <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>OLD TRXN REF: </b> : ${
            content.old_ref
          }</li>`;
  if (content.type == "L") {
    details =
      details +
      `<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>PRINCIPAL AMOUNT: </b> : ${content.amount} UGX</li>
  <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>INTEREST AMOUNT: </b> : ${content.interest} UGX</li>
  <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>PENALTY AMOUNT: </b> : ${content.penalty} UGX</li>`;
  } else {
    details =
      details +
      `<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>TRXN AMOUNT: </b> : ${content.amount} UGX</li>`;
  }
  details =
    details +
    `
					
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>TRXN DATE: </b> : ${content.d}</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>SESSION DATE: </b> : ${content.d}</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>LAST UPDATED: </b> : ----</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i><b>TRXN DESCRIPTION: </b> : ${content.description}</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>PAYMENT METHOD: </b> : ${content.meth}</li>
				</ul>
			</div>

			<div class="col-md-6">
				<h4 class="mt-0 header-title">Transaction Charges</h4>
				<p class="text-muted mb-3">...</p>

				<hr class="hr-dashed">

				<div class="alert alert-info">(${content.charges}) CHARGES FOUND</div>
        <p class="text-muted mb-3">...</p>

				<hr class="hr-dashed">
        	<ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
          
					 <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp; <b>TELLER CASH A/C:</b> : ${content.cash_acc}</li>
          <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp; <b>CHART A/C:</b> : ${content.acid}</li>
         
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>CREDITED A/C: </b> : ${content.cr_acid}</li>
					<li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp;<b>DEBIT A/C: </b> : ${content.dr_acid}</li>

				</ul>

        	<hr class="hr-dashed">
        	<ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
          
					 <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp; <b>Verified:</b> : ${content.verify}</li>
            <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>&nbsp;&nbsp; <b>Is Reversal:</b> : ${content.reversal}</li>
           </ul>
			</div>
		</div>


	</div>
</div>
  `;
  alert_close();
  $(".modal").modal("hide");
  var modal = $("#pageGeneralModal").modal("show");
  modal.find(".modal-title").text(content.title);
  modal.find(".modal-body").html(details);
}

function alert_close() {
  swal.close();
}

function get_cycle_text(repay_cycle_id) {
  if (repay_cycle_id == 1) return "DAYS";
  if (repay_cycle_id == 2) return "WEEKS";
  if (repay_cycle_id == 3) return "MONTHS";
  if (repay_cycle_id == 4) return "BI-MONTHLY";
  if (repay_cycle_id == 5) return "YEARS";
  if (repay_cycle_id == 6) return "MONTHS";

  return "UNKWOWN";
}

function to_number(num) {
  return parseInt(num) || 0;
}
function days_in_arrears(arr_date) {
  if (arr_date) {
    var today = new Date();
    var date_to_reply = new Date(arr_date);
    var timeinmilisec = today.getTime() - date_to_reply.getTime();
    return Math.floor(timeinmilisec / (1000 * 60 * 60 * 24));
  } else {
    return "0";
  }
}

function smart_record(string = null) {
  return string || "-";
}

function encrypt_data(string) {
  var times = 5;
  for (let i = 0; i < times; i++) {
    string = reverseString(btoa(string));
  }
  return string;
}

function decrypt_data(string) {
  var times = 5;
  for (let i = 0; i < times; i++) {
    string = btoa(reverseString(string));
  }
  return string;
}

// function to reverse a string
function reverseString(str) {
  return str.split("").reverse().join("");
}

// alert_loading(null, false);
// alert_loading(null, false);

function addRow(tableID) {
  var table = document.getElementById(tableID);
  var rowCount = table.rows.length;
  var cellContent = "";
  if (rowCount < 100) {
    /** limit the user from creating fields more than your limits*/
    var row = table.insertRow(rowCount);
    row.id = rowCount - 1 + "_rowdata";
    var colCount = table.rows[0].cells.length;
    for (var i = 0; i < colCount; i++) {
      cellContent = "";
      var newcell = row.insertCell(i);

      if (tableID == "dataTable3") {
        cellContent = table.rows[1].cells[i].innerHTML;
      } else if (tableID == "activity_dates_table" && i == 1) {
        cellContent = table.rows[1].cells[i].innerHTML;
        cellContent = cellContent.replace("[DAY]", "Day " + (rowCount - 1));
      } else if (tableID == "capacity_gaps_table") {
        cellContent = table.rows[1].cells[i].innerHTML;
        cellContent = cellContent.replace("[ROWID]", rowCount - 1);
      } else if (tableID == "none_gov_entities_table") {
        cellContent = table.rows[1].cells[i].innerHTML;
        cellContent = cellContent.replace(/\[ADD_NEW_ID]/g, rowCount - 1);
        $("#current_cell_count").val(rowCount - 1);
      } else {
        cellContent = table.rows[1].cells[i].innerHTML;
      }

      /**$(cellContent).find('.chosen-container').remove()*/
      /**$(cellContent).find('select').css('display','block')*/
      /**cellContent = cellContent*/

      newcell.innerHTML = cellContent;
    }
  } else {
    alert("Maximum is 100.");
  }
}

function deleteRow(tableID) {
  var table = document.getElementById(tableID);
  var rowCount = table.rows.length;
  for (var i = 1; i < rowCount; i++) {
    /**0*/
    var row = table.rows[i];
    var chkbox = row.cells[0].childNodes[0];
    if (null != chkbox && true == chkbox.checked) {
      if (rowCount <= 2) {
        /**limit the user from removing all the fields*/
        alert("Cannot Remove all.");
        break;
      }
      table.deleteRow(i);
      rowCount--;
      i--;
    }
  }
}

function validPhone(phone) {
  phone = phone.trim();
  // return /^\(\d{3}\) \d{3}\-\d{4}( x\d{1,6})?$/.test(phone);
  return /^\d{3}\d{3}\d{4}( x\d{1,6})?$/.test(phone);
  // return /^\+[0-9]{3}[0-9]{3}[0-9]{6}$/.test(phone)
  // return /^\+[0-9]{3}\s[0-9]{3}\s[0-9]{6}$/.test(phone)
}

function validNiN(nin) {
  var size = nin.length;
  return size == 14 && alphanumeric(nin) ? true : false;
}

// Function to check letters and numbers
function alphanumeric(inputtxt) {
  return /^[0-9a-zA-Z]+$/.test(inputtxt);
}
