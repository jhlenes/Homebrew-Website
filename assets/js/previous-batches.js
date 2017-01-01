
$(document).ready(function() {

  $("#batchesTable").tablesorter({
    dateFormat: "dd-mm-yyyy"
  });

  $('#batchesTable tbody').on("click", "tr", function () {
    var batchId = parseInt($(this).children().first().text());
    window.location.href = "previous-batches.php?id=" + batchId;
  });

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
