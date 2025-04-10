/**
 * Created by YIGA ELLY on March/15/2023.
 */
module.exports = function (grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON("package.json"),

    uglify: {
      options: {
        banner:
          '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
      },
      my_target: {
        files: {
          "client/js/app-scripts.min.js": [
            // "client/vendor/global/global.min.js",
            "client/vendor/chart.js/Chart.bundle.min.js",
            "client/vendor/jquery-nice-select/js/jquery.nice-select.min.js",
            "client/vendor/apexchart/apexchart.js",
            "client/vendor/peity/jquery.peity.min.js",
            "client/vendor/nouislider/nouislider.min.js",
            "client/vendor/wnumb/wNumb.js",
            // "client/js/dashboard/dashboard-1.js",
            "client/vendor/owl-carousel/owl.carousel.js",
            "client/vendor/raphael/raphael.min.js",
            "client/morris/morris.min.js",
            "client/plugins-init/morris-init.js",
            // "client/js/custom.min.js",
            "client/js/dlabnav-init.js",
            "client/js/sweetalert2.all.min.js",
            // "client/js/demo.js",
            "client/vendor/datatables/js/jquery.dataTables.min.js",
            // "client/js/plugins-init/datatables.init.js",
            // "client/",
            // "client/",
            // "client/",
          ],
        },
      },
    },
    cssmin: {
      options: {
        shortHandCompacting: false,
        roundingPrecision: -1,
      },
      target: {
        files: {
          "client/css/app-styles.min.css": [
            // "client/vendor/datatables/css/jquery.dataTables.min.css",
            // "client/vendor/jquery-nice-select/css/nice-select.css",
            // "client/vendor/select2/css/select2.min.css",
            // "client/vendor/jquery-smartwizard/dist/css/smart_wizard.min.css",
            // "client/css/style.css",
            "client/css/sweetalert2.min.css",
          ],
        },
      },
    },
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks("grunt-contrib-uglify");
  grunt.loadNpmTasks("grunt-contrib-cssmin");

  // Default task(s).
  grunt.registerTask("default", ["uglify", "cssmin"]);
  //grunt.registerTask('default', ['ngAnnotate']);
};
/**
 * Created by Yasira M on March/15/2023.
 */
