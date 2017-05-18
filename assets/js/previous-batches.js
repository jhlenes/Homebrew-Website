
$(document).ready(function() {

  $("#batchesTable").tablesorter({
    dateFormat: "dd-mm-yyyy",
    sortList: [[0,1]] // Sort on first column, order descending.
  });

  $('#batchesTable tbody').on("click", "tr", function () {
    var batchId = parseInt($(this).children().first().text());
    window.location.href = "previous-batches.php?id=" + batchId;
  });

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
