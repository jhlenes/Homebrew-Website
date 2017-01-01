
$(document).ready(function() {
    var numFields = 4;
    var maxFields = 12;
    var wrapper = $(".insert_fields");

    $(".add_field").click(function(e){  // If "Add point" button is clicked
        e.preventDefault();
        if(numFields < maxFields){
          numFields++;
          // Add input field
          $(wrapper).append('<div class="3u 12u(mobile)"><input type="text" name="point' + numFields + '" id="point' + numFields + '" placeholder="<Hours>,<Temp>"/></div>');
        }
    });

    $(".remove_field").click(function(e){ // If "Remove point" button is clicked
      e.preventDefault();
      if (numFields > 2) {
        numFields--;
        wrapper.children().last().remove();
      }
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
      type: 'line'
    },
    title: {
      text: 'Type'
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
          name: 'Set curve',
          data: []
        }
    ],
    credits: {
      enabled: false
    },
    exporting: {
      enabled: false
    }
  });

  $('form').on("blur", "input[id*='type']", function () {
    chart.setTitle({
      text: document.getElementById($(this).attr('id')).value
    });
  });

  var currentDate =  parseInt(+ new Date() / 1000); // Reduce resolution from milliseconds to seconds

  // This function is called everytime focus goes away from a insert field
  $('form').on("blur", "input[id^='point']", function () {
    var num = parseInt($(this).attr('id').substring(5));  // Point number
    var point = document.getElementById($(this).attr('id')).value;
    var splitted = point.split(",");
    if (splitted.length == 2) {
      var time = 1000*(+ currentDate + parseInt(splitted[0])*3600);
      var temp = parseFloat(splitted[1]);

      if (typeof chart.series[0].data[num-1] !== 'undefined') { // if point already exists, update point
        chart.series[0].data[num-1].update({
          x: time,
          y: temp
        });
      } else {  // Add point
        chart.series[0].addPoint([time, temp]);
      }
    }
  });

});
