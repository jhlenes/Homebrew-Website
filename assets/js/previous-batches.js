
$(document).ready(function() {

  $("#batchesTable").tablesorter({
    dateFormat: "dd-mm-yyyy"
  });

  $('#batchesTable tbody').on("click", "tr", function () {
    var batchId = parseInt($(this).children().first().text());
    window.location.href = "previous-batches?id=" + batchId;
  });

});
