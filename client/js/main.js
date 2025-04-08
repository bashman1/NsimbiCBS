$(document).ready(function () {
  $("#accesscamera").on("click", function () {
    Webcam.reset();
    Webcam.set({
      width: 320,
      height: 240,
      image_format: "jpeg",
      jpeg_quality: 90,
    });
    Webcam.on("error", function () {
      // console.log(error);
      $("#photoModal").modal("hide");
      swal({
        title: "Warning",
        text: "Please give permission to access your webcam",
        icon: "warning",
      });
    });
    Webcam.attach("#my_camera");
  });

  // $("#takephoto").on("click", take_snapshot);

  $("#retakephoto").on("click", function () {
    $("#my_camera").addClass("d-block");
    $("#my_camera").removeClass("d-none");

    $("#results").addClass("d-none");

    $("#takephoto").addClass("d-block");
    $("#takephoto").removeClass("d-none");

    $("#retakephoto").addClass("d-none");
    $("#retakephoto").removeClass("d-block");

    $("#uploadphoto").addClass("d-none");
    $("#uploadphoto").removeClass("d-block");
  });

  // $("#photoForm").on("submit", function (e) {
  //   e.preventDefault();
  //   $.ajax({
  //     url: "photoUpload.php",
  //     type: "POST",
  //     data: new FormData(this),
  //     contentType: false,
  //     processData: false,
  //     success: function (data) {
  //       if (data == "success") {
  //         Webcam.reset();

  //         $("#my_camera").addClass("d-block");
  //         $("#my_camera").removeClass("d-none");

  //         $("#results").addClass("d-none");

  //         $("#takephoto").addClass("d-block");
  //         $("#takephoto").removeClass("d-none");

  //         $("#retakephoto").addClass("d-none");
  //         $("#retakephoto").removeClass("d-block");

  //         $("#uploadphoto").addClass("d-none");
  //         $("#uploadphoto").removeClass("d-block");

  //         $("#photoModal").modal("hide");

  //         swal({
  //           title: "Success",
  //           text: "Photo uploaded successfully",
  //           icon: "success",
  //           buttons: false,
  //           closeOnClickOutside: false,
  //           closeOnEsc: false,
  //           timer: 2000,
  //         });
  //       } else {
  //         swal({
  //           title: "Error",
  //           text: "Something went wrong",
  //           icon: "error",
  //         });
  //       }
  //     },
  //   });
  // });
});


