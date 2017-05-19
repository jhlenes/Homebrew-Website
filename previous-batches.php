<?php
	date_default_timezone_set("Europe/Oslo");

  $timeOffset = timezone_offset_get(timezone_open( "Europe/Oslo" ), new DateTime());

  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "homebrew";

  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // -------------------- Get selected batch --------------------
  if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    // Get measurements
    $stmt = $conn->prepare("SELECT temp, UNIX_TIMESTAMP(time) FROM measurement WHERE batch_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
      $temp = $row[0];
      $time = $row[1] + $timeOffset;
      $time *= 1000; // convert from Unix timestamp to JavaScript time
      $data[] = "[$time, $temp]";
    }

    // Get batch data
    $stmt = $conn->prepare("SELECT id, type, UNIX_TIMESTAMP(date) FROM batch WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
    $number = $row[0];
    $type = $row[1];
    $startTime = $row[2] + $timeOffset;
    $startTimeFormatted = date("d-m-Y", $startTime);

    // Get set points for set curve
    $stmt = $conn->prepare("SELECT temp, hours FROM point WHERE batch_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $setCurve = array();
    while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
      $setTemp = $row[0];
      $setTime = $startTime + $row[1]*3600;
      $setTime *= 1000; // convert from Unix timestamp to JavaScript time
      $setCurve[] = "[$setTime, $setTemp]";
    }
  }

	// Include HTML for top of site
	$title = "Previous batches | Homebrew";
	$additionalScripts = "
	<script src=\"assets/js/jquery.tablesorter.js\"></script>
	<script src=\"assets/js/highcharts.js\"></script>
	<script src=\"assets/js/highcharts.modules.exporting.js\"></script>
	<script type=\"text/javascript\">var chart = 0;</script>
	<script src=\"assets/js/previous-batches.js\"></script>";
  if (isset($_GET["id"])) {
		$additionalScripts = $additionalScripts . "
	<script type=\"text/javascript\">
		$(function () {
			chart.setTitle({
				text: '$type'
			});
			chart.setSubtitle({
				text: 'Batch #$number: $startTimeFormatted'
			});
			chart.series[0].setData([" . join($data, ',') . "]);
			chart.series[1].setData([" . join($setCurve, ',') . "]);
		});
	</script>";
  };

	include("top.php");
?>

		<!-- Main -->
		<article id="main">

			<header class="special container">
				<span class="icon fa-beer"></span>
				<h2>Previous batches</h2>
			</header>

			<!-- One -->
			<section class="wrapper style4 container">

				<!-- Content -->
				<div class="content">

					<section>
            <div id="highcharts featured" style="min-width: 310px; height: 400px; margin: 0 auto<?php if (! isset($_GET["id"])) {echo "; display: none";} ?>"></div>
					</section>

          <section>
            <header>
              <h3>Select a batch</h3>
            </header>
            <table class="default batches" id="batchesTable">
              <thead>
                <th>Batch <i class="fa fa-sort" aria-hidden="true" style="color:#fff"></i></th>
                <th>Beer type <i class="fa fa-sort" aria-hidden="true" style="color:#fff"></i></th>
                <th>Date <i class="fa fa-sort" aria-hidden="true" style="color:#fff"></i></th>
              </thead>
              <tbody>
                <?php
                  // Get data for batches
                  $stmt = $conn->prepare("SELECT id, type, UNIX_TIMESTAMP(date) FROM batch");
                  $stmt->execute();
                  while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                    echo "<tr>";
                    echo   "<td>" . intval($row[0]) . "</td>";
                    echo   "<td>" . $row[1] . "</td>";
                    echo   "<td>" . date("d-m-Y", $row[2]) . "</td>";
                    echo "</tr>";
                  }
                ?>
              </tbody>
            </table>
					</section>

					<section>
						<header>
							<h3>Know your history</h3>
						</header>
						<p>Did you accidentally brew the world's best beer? Check out how, and make it again.</p>
					</section>
				</div>

			</section>

		</article>

<?php include("bottom.php"); ?>
