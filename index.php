
<!--Connect to database and get data temperature data-->
<?php
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
		$time = $row[1];
		$time *= 1000; // convert from Unix timestamp to JavaScript time
		$data[] = "[$time, $temp]";
	}

	// Get batch data
	$stmt = $conn->prepare("SELECT id, type, UNIX_TIMESTAMP(date) FROM batch WHERE id = (SELECT MAX(id) FROM batch)");
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
	$number = $row[0];
	$type = $row[1];
	$startTime = $row[2];
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

?>

<!DOCTYPE HTML>
<!--
	Twenty by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
<head>
	<title>Homebrew by Henrik</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
	<link rel="stylesheet" href="assets/css/main.css" />
	<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
	<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->

	<!-- Scripts -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/jquery.dropotron.min.js"></script>
	<script src="assets/js/jquery.scrolly.min.js"></script>
	<script src="assets/js/jquery.scrollgress.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="assets/js/main.js"></script>

	<!-- My scripts-->
	<script src="assets/js/highcharts.js"></script>
	<script src="assets/js/highcharts.modules.exporting.js"></script>

	<!-- Set chart options -->
	<script type="text/javascript">var chart = 0;</script>
	<script src="assets/js/index.js"></script>
	<?php
		echo "<script type=\"text/javascript\">
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
	?>

</head>
<body class="no-sidebar">
	<div id="page-wrapper">

		<!-- Header -->
		<header id="header">
			<h1 id="logo"><a href="/">Homebrew <span>by HENRIK</span></a></h1>
			<nav id="nav">
				<ul>
					<li class="current"><a href="new-batch.php">New Batch</a></li>
					<li class="current"><a href="previous-batches.php">Previous batches</a></li>
					<li class="submenu">
						<a href="#">More stuff</a>
						<ul>
							<li><a href="old_index.html">Welcome</a></li>
							<li><a href="left-sidebar.html">Left Sidebar</a></li>
							<li><a href="right-sidebar.html">Right Sidebar</a></li>
							<li><a href="contact.html">Contact</a></li>
							<li class="submenu">
								<a href="#">Submenu</a>
								<ul>
									<li><a href="#">Dolore Sed</a></li>
									<li><a href="#">Consequat</a></li>
									<li><a href="#">Lorem Magna</a></li>
									<li><a href="#">Sed Magna</a></li>
									<li><a href="#">Ipsum Nisl</a></li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
			</nav>
		</header>

		<!-- Main -->
		<article id="main">

			<header class="special container">
				<span class="icon fa-beer"></span>
				<h2><strong>Homebrew</strong></h2>
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
						<header>
							<h3>Dolore Amet Consequa</h3>
						</header>
						<p>Aliquam massa urna, imperdiet sit amet mi non, bibendum euismod est. Curabitur mi justo, tincidunt vel eros ullamcorper, porta cursus justo. Cras vel neque eros. Vestibulum diam quam, mollis at consectetur non, malesuada quis augue. Morbi tincidunt pretium interdum. Morbi mattis elementum orci, nec dictum massa. Morbi eu faucibus massa. Aliquam massa urna, imperdiet sit amet mi non, bibendum euismod est. Curabitur mi justo, tincidunt vel eros ullamcorper, porta cursus justo. Cras vel neque eros. Vestibulum diam.</p>
						<p>Vestibulum diam quam, mollis at consectetur non, malesuada quis augue. Morbi tincidunt pretium interdum. Morbi mattis elementum orci, nec dictum porta cursus justo. Quisque ultricies lorem in ligula condimentum, et egestas turpis sagittis. Cras ac nunc urna. Nullam eget lobortis purus. Phasellus vitae tortor non est placerat tristique. Sed id sem et massa ornare pellentesque. Maecenas pharetra porta accumsan. </p>
						<p>In vestibulum massa quis arcu lobortis tempus. Nam pretium arcu in odio vulputate luctus. Suspendisse euismod lorem eget lacinia fringilla. Sed sed felis justo. Nunc sodales elit in laoreet aliquam. Nam gravida, nisl sit amet iaculis porttitor, risus nisi rutrum metus, non hendrerit ipsum arcu tristique est.</p>
					</section>
				</div>

			</section>

			<!-- Two -->
			<section class="wrapper style1 container special">
				<div class="row">
					<div class="4u 12u(narrower)">

						<section>
							<header>
								<h3>This is Something</h3>
							</header>
							<p>Sed tristique purus vitae volutpat ultrices. Aliquam eu elit eget arcu commodo suscipit dolor nec nibh. Proin a ullamcorper elit, et sagittis turpis. Integer ut fermentum.</p>
							<footer>
								<ul class="buttons">
									<li><a href="#" class="button small">Learn More</a></li>
								</ul>
							</footer>
						</section>

					</div>
					<div class="4u 12u(narrower)">

						<section>
							<header>
								<h3>Also Something</h3>
							</header>
							<p>Sed tristique purus vitae volutpat ultrices. Aliquam eu elit eget arcu commodo suscipit dolor nec nibh. Proin a ullamcorper elit, et sagittis turpis. Integer ut fermentum.</p>
							<footer>
								<ul class="buttons">
									<li><a href="#" class="button small">Learn More</a></li>
								</ul>
							</footer>
						</section>

					</div>
					<div class="4u 12u(narrower)">

						<section>
						<header>
							<h3>Probably Something</h3>
						</header>
						<p>Sed tristique purus vitae volutpat ultrices. Aliquam eu elit eget arcu commodo suscipit dolor nec nibh. Proin a ullamcorper elit, et sagittis turpis. Integer ut fermentum.</p>
						<footer>
							<ul class="buttons">
								<li><a href="#" class="button small">Learn More</a></li>
							</ul>
						</footer>
						</section>

					</div>
				</div>
			</section>

		</article>

		<!-- Footer -->
		<footer id="footer">

			<ul class="icons">
				<li><a href="#" class="icon circle fa-twitter"><span class="label">Twitter</span></a></li>
				<li><a href="#" class="icon circle fa-facebook"><span class="label">Facebook</span></a></li>
				<li><a href="#" class="icon circle fa-google-plus"><span class="label">Google+</span></a></li>
				<li><a href="#" class="icon circle fa-github"><span class="label">Github</span></a></li>
				<li><a href="#" class="icon circle fa-dribbble"><span class="label">Dribbble</span></a></li>
			</ul>

			<ul class="copyright">
				<li>&copy; Henrik Lenes</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
			</ul>

		</footer>

	</div>

</body>
</html>
