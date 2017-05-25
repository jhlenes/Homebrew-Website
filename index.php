
<?php
	date_default_timezone_set("Europe/Oslo");
	$timeOffset = timezone_offset_get(timezone_open("Europe/Oslo"), new DateTime());

	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "homebrew";

	$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Get measurements
	$stmt = $conn->prepare("SELECT temp, UNIX_TIMESTAMP(time) FROM measurement WHERE batch_id = (SELECT MAX(id) FROM batch)");
	$stmt->execute();
	$data = array();
	while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		$temp = $row[0];
		$time = $row[1] + $timeOffset;	// Convert to correct timezone
		$time *= 1000; // convert from Unix timestamp to JavaScript time
		$data[] = "[$time, $temp]";
	}

	// Get batch data
	$stmt = $conn->prepare("SELECT id, type, UNIX_TIMESTAMP(date), is_running FROM batch WHERE id = (SELECT MAX(id) FROM batch)");
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
	$number = $row[0];
	$type = $row[1];
	$startTime = $row[2] + $timeOffset;	// Convert to correct timezone
	$running = $row[3];
	$startTimeFormatted = date("d-m-Y", $startTime);

	// Get set points for set curve
	$stmt = $conn->prepare("SELECT temp, hours FROM point WHERE batch_id = (SELECT MAX(id) FROM batch)");
	$stmt->execute();
	$setCurve = array();
	while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		$setTemp = $row[0];
		$setTime = $startTime + $row[1]*3600;
		$setTime *= 1000; // convert from Unix timestamp to JavaScript time
		$setCurve[] = "[$setTime, $setTemp]";
	}

	// Get current temp and heating status
	$stmt = $conn->prepare("SELECT current_temp, is_heating FROM status WHERE id = 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
    $current_temp = $row[0];
    $heating = $row[1];

	// Include HTML for top of site
	$title = "Homebrew";
	$additionalScripts = "
	<script src=\"assets/js/highcharts.js\"></script>
	<script src=\"assets/js/highcharts.modules.exporting.js\"></script>

	<!-- Set chart options -->
	<script type=\"text/javascript\">var chart = 0;</script>
	<script src=\"assets/js/index.js\"></script>
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


	include("top.php");
?>

		<!-- Main -->
		<article id="main">

			<header class="special container">
				<span class="icon fa-beer"></span>
				<h2>Homebrew</h2>
			</header>

			<!-- One -->
			<section class="wrapper style4 container">

				<!-- Content -->
				<div class="content">
					<section>
						<h2 id="brewstatustitle" <?php if ($running) echo "style=\"display:none\"";?>>LAST BATCH</h2>
						<div id="brewstatusbar" class="row 50%" <?php if (!$running) echo "style=\"display:none\"";?>>
							<div class="9u 12u(mobile)">
								<h2>
									<span class="icon fa-thermometer-half"></span>
									<span  id="current_temp" ><?php echo "$current_temp"; ?></span>&deg;C
									&nbsp;&nbsp;&nbsp;&nbsp;<span class="icon fa-fire"></span>
									<span  id="is_heating" <?php if ($heating) echo "style=\"color:green\" class=\"icon fa-check\""; else echo "style=\"color:red\" class=\"icon fa-times\""; ?>></span>
								</h2>
							</div>
							<div class="3u 12u(mobile)">
								<a href="/send?status=0" onclick="return confirm('Are you sure you want to abort this batch?')"><input type="submit" class="special abortbutton" value="Abort batch"/></a>
							</div>
						</div>
						
						<!--<a href="#" class="image featured"><img src="images/pic04.jpg" alt="" /></a>-->
						<div id="highcharts featured" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

					</section>

					<section>
						<header>
							<h3>Watch your beer</h3>
						</header>
						<p>Monitor your brewing in realtime, because nothing is more exciting.</p>
					</section>
				</div>

			</section>

		</article>

<?php include("bottom.php"); ?>
