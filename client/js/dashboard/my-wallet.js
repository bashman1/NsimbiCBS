(function ($) {
  /* "use strict" */

  var dlabChartlist = (function () {
    var screenWidth = $(window).width();
    let draw = Chart.controllers.line.__super__.draw; //draw shadow

    var pieChart = function () {
      if (jQuery("#pieChart").length > 0) {
        //doughut chart
        const pieChart = document.getElementById("pieChart").getContext("2d");
        // pieChart.height = 100;
        new Chart(pieChart, {
          type: "doughnut",
          data: {
            weight: 5,
            defaultFontFamily: "Poppins",
            datasets: [
              {
                data: [50, 30],
                labels: ["Disbursed", "Repayments"],
                borderWidth: 0,
                borderColor: "rgba(255,255,255,1)",
                backgroundColor: ["#8df05f", "#ff4b4b"],
                hoverBackgroundColor: ["#8df05f", "#ff4b4b"],
              },
            ],
          },
          options: {
            weight: 1,
            cutoutPercentage: 70,
            responsive: true,
            maintainAspectRatio: false,
          },
        });
      }
    };

    var lineChart = async function () {
      // fetch data
      const response = await fetch(
        `https://app.ucscucbs.net/backend/api/Bank/get_all_weekly_savs.php?bank=${session_bank_id}&branch=${session_branch_id}&user=${session_user_id}`
      );

	   const responsen = await fetch(
       `https://app.ucscucbs.net/backend/api/Bank/get_all_weekly_deposits.php?bank=${session_bank_id}&branch=${session_branch_id}&user=${session_user_id}`
     );
    //   console.log(response);
      const data = await response.json();
      const datan = await responsen.json();
    //   console.log(data);
      length = data.data.length;
    //   console.log(length);

      deps = [];
      withds = [];
      for (i = 0; i < length; i++) {
        deps.push(datan.data[i].amount);
		// console.log(data.data[i].amount);
        withds.push(data.data[i].amount);
      }

      var options = {
        series: [
          {
            name: "Deposits",
            // data: [10, 30, 20, 40, 20, 45, 10],
            data: deps,
          },
          {
            name: "Withdraws",
            // data: [10, 15, 10, 30, 15, 35, 5],
            data: withds,
          },
        ],
        chart: {
          height: 170,
          type: "line",
          toolbar: {
            show: false,
          },
          zoom: {
            enabled: false,
          },
        },
        colors: ["#68e365", "#ec2a35"],
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "smooth",
          width: 3,
        },
        legend: {
          show: false,
        },
        grid: {
          /* row: {
			colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
			opacity: 0.5
		  }, */
          xaxis: {
            lines: {
              show: true,
            },
          },
        },
        xaxis: {
          categories: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        },
        yaxis: {
          show: false,
        },
      };

      var chart = new ApexCharts(
        document.querySelector("#line-chart"),
        options
      );
      chart.render();
    };

    var donutChart1 = function () {
      $("span.donut1").peity("donut", {
        width: "60",
        height: "60",
      });
    };

    /* Function ============ */
    return {
      init: function () {},

      load: function () {
        pieChart();
        lineChart();
        donutChart1();
      },

      resize: function () {},
    };
  })();

  jQuery(window).on("load", function () {
    setTimeout(function () {
      dlabChartlist.load();
    }, 1000);
  });
})(jQuery);
