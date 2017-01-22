$(document).ready(function() {

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

            var x = parseInt(response.substring(0, index1));
            var y = parseFloat(response.substring(index1 + 1, index2));
            chart.series[0].addPoint([x, y]);

            response = response.substring(index2 + 1);
            index1 = response.indexOf(',');
          }

        }
    });
  };
  setInterval(updater, 10000);  // Call every 10 seconds
});

$(function () {
  Highcharts.setOptions({
    global: {
      timezoneOffset: -1 * 60
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
        text: 'Temperatur (°C)'
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
      name: 'Temperatur',
      data: []
    }, {
      type: 'line',
      name: 'Set curve',
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
