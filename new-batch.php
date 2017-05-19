
<!-- Database connection -->
<?php
	date_default_timezone_set("Europe/Oslo");

	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "homebrew";

	$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $conn->prepare("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'homebrew' AND TABLE_NAME = 'batch'");
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);

	$num = $row[0];
	$dateNow = date("d-m-Y");

	// -------------------- Handle submit --------------------
	if (isset($_GET["type"])) {
		$type = $_GET["type"];

		$timeNow = time();
		$i = 1;
		while (isset($_GET["point" . $i])) {

			// Get X and Y value from the point
			$point = explode(',', $_GET["point" . $i++]);

			$hours[] = intval($point[0]);

			$temp[] = $point[1];
		}

		// Create new batch
		$stmt = $conn->prepare("INSERT INTO `batch`(`id`, `type`, `date`, `is_running`) VALUES (NULL, :type, FROM_UNIXTIME(:timeNow), 1)");
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':timeNow', $timeNow);
		$stmt->execute();

		// Add points to batch
		$stmt = $conn->prepare("INSERT INTO `point`(`hours`, `temp`, `batch_id`) VALUES (:hours, :temp, :batchid)");
		$stmt->bindParam(':batchid', $num);
		for ($j = 1; $j < $i; $j++) {
			$stmt->bindParam(':hours', $hours[$j-1]);
			$stmt->bindParam(':temp', $temp[$j-1]);
			$stmt->execute();
		}

		// Go to index.php
		header("Location: /");
		exit;

	}

	// Include HTML for top of site
	$title = "New Batch | Homebrew";
	$additionalScripts = "
	<script src=\"assets/js/highcharts.js\"></script>
	<script src=\"assets/js/highcharts.modules.exporting.js\"></script>
	<script type=\"text/javascript\">var chart = 0;</script>
	<script src=\"assets/js/new-batch.js\"></script>
	<script type=\"text/javascript\">
		$(function () {
			chart.setSubtitle({
				text: 'Batch #$num: $dateNow'
			});
		});
	</script>";

	include("top.php");
?>

		<!-- Main -->
		<article id="main">

			<header class="special container">
				<span class="icon fa-beer"></span>
				<h2>New batch</h2>
			</header>

			<!-- One -->
			<section class="wrapper style4 container">

				<!-- Content -->
				<div class="content">
					<section>

						<!--<a href="#" class="image featured"><img src="images/pic04.jpg" alt="" /></a>-->
						<div id="highcharts featured" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

					</section>

					<section>
						<form onsubmit="return confirm('Are you sure you want to start this batch?');">
              <div class="row 50%">
                <div class="12u">
									<h4>Beer type</h4>
                  <input type="text" name="type" id="type" placeholder="e.g. Amber ale" />
                </div>
              </div>
							<br>
							<h4>Setpoints</h4>
							<div class="insert_fields row 50%">
								<div class="3u 12u(mobile)">
									<input type="text" name="point1" id="point1" placeholder="<Hours>,<Temp>" />
								</div>
								<div class="3u 12u(mobile)">
									<input type="text" name="point2" id="point2" placeholder="<Hours>,<Temp>" />
								</div>
								<div class="3u 12u(mobile)">
									<input type="text" name="point3" id="point3" placeholder="<Hours>,<Temp>" />
								</div>
								<div class="3u 12u(mobile)">
									<input type="text" name="point4" id="point4" placeholder="<Hours>,<Temp>" />
								</div>
							</div>
							<div class="row">
								<div class="12u">
									<ul class="buttons">
										<li><input type="button" class="add_field" value="Add point" /></li>
										<li><input type="button" class="remove_field" value="Remove point" /></li>
                    <li><input type="submit" class="special" value="Start brewing" /></li>
									</ul>
								</div>
							</div>
						</form>
            <div class="row 50%"><div class="12u"></div><div class="12u"></div></div>
					</section>

					<section>
						<header>
							<h3>Control your future</h3>
						</header>
						<p>What's a future without beer? Make sure your brewing process is perfect.</p>
					</section>
				</div>

			</section>

		</article>

<?php include("bottom.php"); ?>
