$(document).ready(function() {

  var offset = new Date().getTimezoneOffset();
  var timeNow = parseInt(+ new Date() / 1000);  // Reduce resolution from milliseconds to seconds

  var updater = function() {
    $.ajax({    //create an ajax request to new-data.php
        type: "GET",
        url: "new-data.php?time=" + timeNow.toString(),
        dataType: "html",   //expect html to be returned
        success: function(response){
          timeNow = parseInt(+ new Date() / 1000);  // Update time

          var index1 = response.indexOf(',');
          while (index1 > 0) {
            var index2 = response.indexOf(';')

            var x = parseInt(response.substring(0, index1)) - offset * 60000;
            var y = parseFloat(response.substring(index1 + 1, index2));
            chart.series[0].addPoint([x, y]);

            response = response.substring(index2 + 1);
            index1 = response.indexOf(',');
          }

        }
    });
  };
  setInterval(updater, 30000);  // Call every 30 seconds
});

$(function () {
  Highcharts.setOptions({
    global: {
      timezone: 'Europe/Oslo'
    }
  });
  chart = Highcharts.chart('highcharts featured', {
    chart: {
      type: 'spline'
    },
    title: {
      text: ''
    },
    subtitle: {
      text: ''
    },
    xAxis: {
      type: 'datetime',
      labels: {
        overflow: 'justify'
      }
    },
    yAxis: {
      title: {
        text: 'Temperature (°C)'
      }
    },
    tooltip: {
      valueSuffix: ' °C'
    },
    plotOptions: {
      line: {
        dataLabels: {
          enabled: false
        },
        enableMouseTracking: true
      }
    },
    series: [{
      name: 'Temperature',
      data: []
    }, {
      type: 'line',
      name: 'Setpoint',
      data: []
    }],
    credits: {
      enabled: false
    },
    exporting: {
      enabled: false
    }
  });
});
