<?php
session_start();
include 'dbconnect.php';

if (isset($_SESSION['userID'])) {
	// Store the userID in a temporary variable
	$tempUserID = $_SESSION['userID'];

	// Clear all session variables
	session_unset();

	// Restore the userID
	$_SESSION['userID'] = $tempUserID;
}
// user engagement count
$eventType = isset($_SESSION['userID']) ? "login" : "visit";
$sql = "INSERT INTO user_engagement (event_type, userId) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $eventType, $_SESSION['userID']);
$stmt->execute();

// date for date picker
$today = date("Y-m-d");

$apiUrl = 'https://newsapi.org/v2/everything?apiKey=c1440b57c68c4a419497642de1a65677&q="travel"+AND+vacation+OR+Budget+travel+tips&pageSize=7';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Set the User-Agent header
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	'User-Agent: YourAppName/1.0'
]);

$response = curl_exec($ch);

// Check if there was a CURL error
if ($response === false) {
	echo 'Curl error: ' . curl_error($ch);
}

curl_close($ch);

// Decode the JSON response
$data = json_decode($response, true);

$articles = []; // Initialize the articles array

if (isset($data['status']) && $data['status'] === 'ok') {
	// Get all fetched articles
	$allArticles = $data['articles'];

	// Check if there are at least 3 articles
	if (count($allArticles) >= 3) {
		// Shuffle the array to randomize the order
		shuffle($allArticles);

		// Select the first 3 articles after shuffling
		$articles = array_slice($allArticles, 0, 3);
	} else {
		// If there are less than 3 articles, use them all
		$articles = $allArticles;
	}
} else {
	$articles = []; // Set articles to an empty array if fetching fails
}

if (isset($_SESSION['userID'])) {

	$userID = $_SESSION['userID'];
	$userDataQuery = "SELECT * FROM user WHERE userID = ?";
	$stmt = $conn->prepare(query: $userDataQuery);
	$stmt->bind_param("s", $userID);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$userData = $result->fetch_assoc();
		$username = $userData['username'];
	} else {
		$username = null;
	}
}

unset($_SESSION['hotel_data']);
unset($_SESSION['form_data']);

?>


<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
	<!-- META DATA -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<!--font-family-->
	<link href="https://fonts.googleapis.com/css?family=Rufina:400,700" rel="stylesheet" />

	<link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet" />

	<!-- TITLE OF SITE -->
	<title>Travel</title>

	<!-- favicon img -->
	<link rel="shortcut icon" type="image/icon" href="assets/logo/favicon.png" />

	<!--font-awesome.min.css-->
	<link rel="stylesheet" href="assets/css/font-awesome.min.css" />

	<!--animate.css-->
	<link rel="stylesheet" href="assets/css/animate.css" />

	<!--hover.css-->
	<link rel="stylesheet" href="assets/css/hover-min.css">

	<!--datepicker.css-->
	<link rel="stylesheet" href="assets/css/datepicker.css">

	<!--owl.carousel.css-->
	<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="assets/css/owl.theme.default.min.css" />

	<!-- range css-->
	<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />

	<!--bootstrap.min.css-->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css" />

	<!-- bootsnav -->
	<link rel="stylesheet" href="assets/css/bootsnav.css" />

	<!--style.css-->
	<link rel="stylesheet" href="assets/css/style.css" />

	<!--responsive.css-->
	<link rel="stylesheet" href="assets/css/responsive.css" />

	<link rel="stylesheet" href="assets/css/custom.css">


	<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

	

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>

<body>
	<!--[if lte IE 9]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade
			your browser</a> to improve your experience and security.</p>
		<![endif]-->

	<!-- main-menu Start -->
	<header class="top-area">
		<div class="header-area">
			<div class="container">
				<div class="row">
					<div class="col-sm-2">
						<div class="logo">
							<a href="index.php">
								trip<span>Telly</span>
							</a>
						</div><!-- /.logo-->
					</div><!-- /.col-->
					<div class="col-sm-10">
						<div class="main-menu">

							<!-- Brand and toggle get grouped for better mobile display -->
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse"
									data-target=".navbar-collapse">
									<i class="fa fa-bars"></i>
								</button><!-- / button-->
							</div><!-- /.navbar-header-->
							<div class="collapse navbar-collapse">
								<ul class="nav navbar-nav navbar-right">
									<li class="smooth-menu"><a href="#home">home</a></li>
									<li class="smooth-menu"><a href="#gallery">Destination</a></li>
									<li class="smooth-menu"><a href="#testemonial">Reviews </a></li>
									<li class="smooth-menu"><a href="#blog">blog</a></li>
									<?php
									if (isset($_SESSION['userID']) && isset($username)) {
										?>
										<li>
											<a href="cart.php"><i class="bx bxs-cart"></i></a>
										</li>
										<?php
									}
									?>
									<li>
										<?php
										if (isset($_SESSION['userID']) && isset($username)) {
											echo '
        <div class="dropdown">
            <button class="book-btn" id="btn-user" onclick="toggleDropdown()">' . htmlspecialchars($username) . '</button>
            <div class="dropdown-content" id="dropdown-content">
                <a href="userEdit.php?editUserID=' . $_SESSION['userID'] . '"">Account Settings</a>
                <a href="purchaseHistory.php">Purchase History</a>
                <a href="fn_signout.php">Log Out</a>
            </div>
        </div>';
										} else {
											echo '<button class="book-btn" onclick="window.location.href = \'signin.php\'">Sign In</button>';
										}
										?>
									</li>

								</ul>
							</div><!-- /.navbar-collapse -->
						</div><!-- /.main-menu-->
					</div><!-- /.col-->
				</div><!-- /.row -->
				<div class="home-border"></div><!-- /.home-border-->
			</div><!-- /.container-->
		</div><!-- /.header-area -->

	</header><!-- /.top-area-->
	<!-- main-menu End -->


	<!--about-us start -->
	<section id="home" class="about-us">
		<div class="container">
			<div class="about-us-content">
				<div class="row">
					<div class="col-sm-12">
						<div class="single-about-us">
							<div class="about-us-txt">
								<h2>
									Explore the Beauty of the Beautiful World
								</h2>
								<div class="about-btn">
									<button id="scrollButton" class="about-view">
										explore now
									</button>
								</div><!--/.about-btn-->
							</div><!--/.about-us-txt-->
						</div><!--/.single-about-us-->
					</div><!--/.col-->
					<div class="col-sm-0">
						<div class="single-about-us">

						</div><!--/.single-about-us-->
					</div><!--/.col-->
				</div><!--/.row-->
			</div><!--/.about-us-content-->
		</div><!--/.container-->

	</section><!--/.about-us-->
	<!--about-us end -->

	<!--travel-box start-->
	<section class="travel-box">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="single-travel-boxes">
						<div id="desc-tabs" class="desc-tabs">

							<ul class="nav nav-tabs" role="tablist">

								<li role="presentation" class="active">
									<a href="#tours" aria-controls="tours" role="tab" data-toggle="tab">
										<i class="fa fa-tree"></i>
										tours
									</a>
								</li>

								<li role="presentation">
									<a href="#hotels" aria-controls="hotels" role="tab" data-toggle="tab">
										<i class="fa fa-building"></i>
										hotels
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">

								<div role="tabpanel" class="tab-pane active fade in" id="tours">
									<div class="tab-para">
										<form action="fn_searchTravel.php" id="travel-form" method="post">
											<div class="row">
												<div class="col-lg-4 col-md-4 col-sm-12">
													<div class="single-tab-select-box">

														<h2>destination</h2>

														<div class="travel-select-icon">
															<select class="form-control " name="from_loc" required>

																<option value="">enter your origin location</option>
																<!-- /.option-->
																<option value="johor">Johor</option>
																<option value="kedah">Kedah</option>
																<option value="kelantan">Kelantan</option>
																<option value="melaka">Melaka</option>
																<option value="negeri sembilan">Negeri Sembilan</option>
																<option value="pahang">Pahang</option>
																<option value="penang">Penang</option>
																<option value="perak">Perak</option>
																<option value="perlis">Perlis</option>
																<option value="selangor">Selangor</option>
																<option value="terengganu">Terengganu</option>
																<option value="kuala lumpur">Wilayah Persekutuan Kuala
																	Lumpur</option>
																<option value="labuan">Wilayah Persekutuan Labuan
																</option>
																<option value="putrajaya">Wilayah Persekutuan Putrajaya
																</option>

															</select><!-- /.select-->
														</div><!-- /.travel-select-icon -->

														<div class="travel-select-icon">
															<select class="form-control " name="destination_loc"
																required>

																<option value="">enter your destination location
																</option><!-- /.option-->
																<option value="johor">Johor</option>
																<option value="kedah">Kedah</option>
																<option value="kelantan">Kelantan</option>
																<option value="melaka">Melaka</option>
																<option value="negeri sembilan">Negeri Sembilan</option>
																<option value="pahang">Pahang</option>
																<option value="penang">Penang</option>
																<option value="perak">Perak</option>
																<option value="perlis">Perlis</option>
																<option value="selangor">Selangor</option>
																<option value="terengganu">Terengganu</option>
																<option value="kuala lumpur">Wilayah Persekutuan Kuala
																	Lumpur</option>
																<option value="labuan">Wilayah Persekutuan Labuan
																</option>
																<option value="putrajaya">Wilayah Persekutuan Putrajaya
																</option>

															</select><!-- /.select-->
														</div><!-- /.travel-select-icon -->

													</div><!--/.single-tab-select-box-->
												</div><!--/.col-->

												<div class="col-lg-2 col-md-3 col-sm-4">
													<div class="single-tab-select-box">
														<h2>Departure</h2>
														<div class="travel-check-icon">
														<input type="date" name="departure_date" id="departureDate"
   														 class="form-control" min="<?php echo $today; ?>"
   														 placeholder="Select Departure Date" required>
														</div><!-- /.travel-check-icon -->
													</div><!--/.single-tab-select-box-->
												</div><!--/.col-->

												<div class="col-lg-2 col-md-3 col-sm-4">
													<div class="single-tab-select-box">
														<h2>Return</h2>
														<div class="travel-check-icon">
														<input type="date" name="return_date" id="returnDate"
    													class="form-control" min="<?php echo $today; ?>"
   								                        placeholder="Select Return Date" required>
														</div><!-- /.travel-check-icon -->
													</div><!--/.single-tab-select-box-->
												</div><!--/.col-->

												<div class="col-lg-2 col-md-1 col-sm-4">
													<div class="single-tab-select-box">
														<h2>person</h2>
														<div class="travel-select-icon">
															<select class="form-control" name="people_num" required>
																<option value="">Select</option>
																<!-- /.option-->
																<option value="1">1</option>
																<option value="2">2</option>
																<option value="3">3</option>
																<option value="4">4</option>
																<option value="5">5</option>
																<option value="6">6</option>
																<option value="7">7</option>
																<option value="8">8</option>
																<option value="9">9</option>
																<option value="10">10</option>
																<!-- /.option-->
															</select>
														</div><!-- /.travel-select-icon -->
													</div><!--/.single-tab-select-box-->
												</div><!--/.col-->

											</div><!--/.row-->

											<div class="row">
												<div class="col-sm-5">
													<div class="travel-budget">
														<div class="row">
															<div class="col-md-3 col-sm-4">
																<h3>budget </h3>
															</div><!--/.col-->
															<div class="col-sm-9">
																<div class="travel-filter-container">
																	<div class="travel-filter">
																		<div class="money_input">
																			<span>RM</span>
																			<input type="number" name="max_budget"
																				class="form-control" min="200"
																				step="1.00" required
																				placeholder="RM 300">
																		</div>
																	</div>
																</div><!--/.travel-filter-->
															</div><!--/.col-->
														</div><!--/.row-->
													</div><!--/.travel-budget-->
												</div><!--/.col-->
												<div class="clo-sm-7">
													<div class="about-btn travel-mrt-0 pull-right">
														<button type="submit" form="travel-form"
															class="about-view travel-btn">
															search
														</button><!--/.travel-btn-->
													</div><!--/.about-btn-->
												</div><!--/.col-->

											</div><!--/.row-->
										</form>
									</div><!--/.tab-para-->

								</div><!--/.tabpannel-->
							</div><!--/.tab content-->
						</div><!--/.desc-tabs-->
					</div><!--/.single-travel-box-->
				</div><!--/.col-->
			</div><!--/.row-->
		</div><!--/.container-->

	</section><!--/.travel-box-->
	<!--travel-box end-->

	<!--service start-->
	<section id="service" class="service" style="display: none">
		<div class="container">

			<div class="service-counter text-center">

				<div class="col-md-4 col-sm-4">
					<div class="single-service-box">
						<div class="service-img">
							<img src="assets/images/service/s1.png" alt="service-icon" />
						</div><!--/.service-img-->
						<div class="service-content">
							<h2>
								<a href="#">
									amazing tour packages
								</a>
							</h2>
							<p>Duis aute irure dolor in velit esse cillum dolore eu fugiat nulla.</p>
						</div><!--/.service-content-->
					</div><!--/.single-service-box-->
				</div><!--/.col-->

				<div class="col-md-4 col-sm-4">
					<div class="single-service-box">
						<div class="service-img">
							<img src="assets/images/service/s2.png" alt="service-icon" />
						</div><!--/.service-img-->
						<div class="service-content">
							<h2>
								<a href="#">
									book top class hotel
								</a>
							</h2>
							<p>Duis aute irure dolor in velit esse cillum dolore eu fugiat nulla.</p>
						</div><!--/.service-content-->
					</div><!--/.single-service-box-->
				</div><!--/.col-->

				<div class="col-md-4 col-sm-4">
					<div class="single-service-box">
						<div class="statistics-img">
							<img src="assets/images/service/s3.png" alt="service-icon" />
						</div><!--/.service-img-->
						<div class="service-content">

							<h2>
								<a href="#">
									online flight booking
								</a>
							</h2>
							<p>Duis aute irure dolor in velit esse cillum dolore eu fugiat nulla.</p>
						</div><!--/.service-content-->
					</div><!--/.single-service-box-->
				</div><!--/.col-->

			</div><!--/.statistics-counter-->
		</div><!--/.container-->

	</section><!--/.service-->
	<!--service end-->

	<!--galley start-->
	<section id="gallery" class="gallery">
		<div class="container">
			<div class="gallery-details">
				<div class="gallary-header text-center">
					<h2>
						top destination
					</h2>
					<p>
					Explore the beauty and diversity of Malaysia's top destinations.
					</p>
				</div><!--/.gallery-header-->
				<div class="gallery-box">
					<div class="gallery-content">
						<div class="filtr-container">
							<div class="row">

								<div class="col-md-6">
									<div class="filtr-item">
										<img src="assets/images/state/resized/masjid.jpg" alt="portfolio image" />
										<div class="item-title">
											<a href="#">
												Kelantan
											</a>
											<!-- <p><span>20 tours</span><span>15 places</span></p> -->
										</div><!-- /.item-title -->
									</div><!-- /.filtr-item -->
								</div><!-- /.col -->

								<div class="col-md-6">
									<div class="filtr-item">
										<img src="assets/images/state/resized/118.jpg" alt="portfolio image" />
										<div class="item-title">
											<a href="#">
												Kuala Lumpur
											</a>
											<!-- <p><span>12 tours</span><span>9 places</span></p> -->
										</div> <!-- /.item-title-->
									</div><!-- /.filtr-item -->
								</div><!-- /.col -->

								<div class="col-md-4">
									<div class="filtr-item">
										<img src="assets/images/state/resized/cl.jpg" alt="portfolio image" />
										<div class="item-title">
											<a href="#">
												Perak
											</a>
											<!-- <p><span>25 tours</span><span>10 places</span></p> -->
										</div><!-- /.item-title -->
									</div><!-- /.filtr-item -->
								</div><!-- /.col -->

								<div class="col-md-4">
									<div class="filtr-item">
										<img src="assets/images/state/resized/penang.jpg" alt="portfolio image" />
										<div class="item-title">
											<a href="#">
												Penang
											</a>
											<!-- <p><span>18 tours</span><span>9 places</span></p> -->
										</div> <!-- /.item-title-->
									</div><!-- /.filtr-item -->
								</div><!-- /.col -->

								<div class="col-md-4">
									<div class="filtr-item">
										<img src="assets/images/state/resized/melaka.jpg" alt="portfolio image" />
										<div class="item-title">
											<a href="#">
												Melaka
											</a>
											<!-- <p><span>14 tours</span><span>12 places</span></p> -->
										</div> <!-- /.item-title-->
									</div><!-- /.filtr-item -->
								</div><!-- /.col -->

								<div class="col-md-8">
									<div class="filtr-item">
										<img src="assets/images/state/resized/drawbridge.jpg" alt="portfolio image" />
										<div class="item-title">
											<a href="#">
												Terengganu
											</a>
											<!-- <p><span>14 tours</span><span>6 places</span></p> -->
										</div> <!-- /.item-title-->
									</div><!-- /.filtr-item -->
								</div><!-- /.col -->

							</div><!-- /.row -->

						</div><!-- /.filtr-container-->
					</div><!-- /.gallery-content -->
				</div><!--/.galley-box-->
			</div><!--/.gallery-details-->
		</div><!--/.container-->

	</section><!--/.gallery-->
	<!--gallery end-->



	<!-- testemonial Start -->
	<section class="testemonial" id="testemonial">
		<?php
		// Load and decode JSON data
		$jsonData = file_get_contents('reviews.json');
		$reviews = json_decode($jsonData, true);
		?>

		<div class="container">
			<div class="gallary-header text-center">
				<h2>Clients Reviews</h2>
				<p>Reviews on popular destinations and hotels in Malaysia</p>
			</div><!--/.gallery-header-->

			<div class="owl-carousel owl-theme" id="testemonial-carousel">
				<?php foreach ($reviews as $review): ?>
					<div class="home1-testm item">
						<div class="home1-testm-single text-center">
							<div class="home1-testm-img">
								<img src="<?php echo $review['profileImg']; ?>" alt="Client Image" />
							</div><!--/.home1-testm-img-->
							<div class="home1-testm-txt">
								<span class="icon section-icon">
									<i class="fa fa-quote-left" aria-hidden="true"></i>
								</span>
								<p><?php echo $review['review']; ?></p>
								<h3><a href="#"><?php echo $review['name']; ?></a></h3>
								<h4><?php echo $review['address']; ?></h4>
							</div><!--/.home1-testm-txt-->
						</div><!--/.home1-testm-single-->
					</div><!--/.item-->
				<?php endforeach; ?>
			</div><!--/.testemonial-carousel-->
		</div><!--/.container-->


	</section><!--/.testimonial-->
	<!-- testemonial End -->


	<!--blog start-->
	<section id="blog" class="blog">
		<div class="container">
			<div class="blog-details">
				<div class="gallary-header text-center">
					<h2>
						latest news
					</h2>
					<p>
						Travel News from all over the world
					</p>
				</div><!--/.gallery-header-->
				<div class="blog-content">

					<div class="row">
						<?php if (!empty($articles)): ?>
							<?php foreach ($articles as $article): ?>
								<div class="col-sm-4 col-md-4">
									<div class="thumbnail">
										<h2>Trending News
											<span><?php echo date("d F Y", strtotime($article['publishedAt'])); ?></span>
										</h2>
										<div class="thumbnail-img">
											<img src="<?php echo $article['urlToImage']; ?>" alt="blog-img">
											<div class="thumbnail-img-overlay"></div><!--/.thumbnail-img-overlay-->
										</div><!--/.thumbnail-img-->

										<div class="caption">
											<div class="blog-txt">
												<h4>
													<a href="<?php echo $article['url']; ?>">
														<?php echo substr($article['title'], 0, 100) . "..."; ?>
													</a>
												</h4>
												<p>
													<?php echo substr($article['content'], 0, 100) . "..."; ?>
												</p>
												<a href="<?php echo $article['url']; ?>">Read More</a>
											</div><!--/.blog-txt-->
										</div><!--/.caption-->
									</div><!--/.thumbnail-->
								</div><!--/.col-->
							<?php endforeach; ?>
						<?php else: ?>
							<p>No articles found.</p>
						<?php endif; ?>
					</div><!--/.row-->
				</div><!--/.blog-content-->
			</div><!--/.blog-details-->
		</div><!--/.container-->

	</section><!--/.blog-->
	<!--blog end-->



	<!-- footer-copyright start -->
	<footer class="footer-copyright">
		<div class="container">

			<hr>
			<div class="foot-icons ">
				<ul class="footer-social-links list-inline list-unstyled" style="display:none">
					<li><a href="#" target="_blank" class="foot-icon-bg-1"><i class="fa fa-facebook"></i></a></li>
					<li><a href="#" target="_blank" class="foot-icon-bg-2"><i class="fa fa-twitter"></i></a></li>
					<li><a href="#" target="_blank" class="foot-icon-bg-3"><i class="fa fa-instagram"></i></a></li>
				</ul>
				<p>&copy; 2024 <a href="#">TripTelly</a>. All Right Reserved</p>

			</div><!--/.foot-icons-->
			<div id="scroll-Top">
				<i class="fa fa-angle-double-up return-to-top" id="scroll-top" data-toggle="tooltip"
					data-placement="top" title="" data-original-title="Back to Top" aria-hidden="true"></i>
			</div><!--/.scroll-Top-->
		</div><!-- /.container-->

	</footer><!-- /.footer-copyright-->
	<!-- footer-copyright end -->




	<script src="assets/js/jquery.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->

	<!--modernizr.min.js-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>


	<!--bootstrap.min.js-->
	<script src="assets/js/bootstrap.min.js"></script>

	<!-- bootsnav js -->
	<script src="assets/js/bootsnav.js"></script>

	<!-- jquery.filterizr.min.js -->
	<script src="assets/js/jquery.filterizr.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

	<!--jquery-ui.min.js-->
	<script src="assets/js/jquery-ui.min.js"></script>

	<!-- counter js -->
	<script src="assets/js/jquery.counterup.min.js"></script>
	<script src="assets/js/waypoints.min.js"></script>

	<!--owl.carousel.js-->
	<script src="assets/js/owl.carousel.min.js"></script>

	<!-- jquery.sticky.js -->
	<script src="assets/js/jquery.sticky.js"></script>

	<!--datepicker.js-->
	<script src="assets/js/datepicker.js"></script>

	<!--Custom JS-->
	<script src="assets/js/custom.js"></script>

	<script>
		document.getElementById("scrollButton").addEventListener("click", function () {
			let totalScrollDistance = 700; // Total pixels to scroll down (100 pixels x 7 times)
			let scrollStep = 5;  // Small increments of pixels for smooth scrolling
			let currentScroll = 0;  // Tracks how much has been scrolled

			function smoothScroll() {
				if (currentScroll < totalScrollDistance) {
					window.scrollBy(0, scrollStep);
					currentScroll += scrollStep;
					requestAnimationFrame(smoothScroll);  // Continue smooth scrolling
				}
			}

			requestAnimationFrame(smoothScroll);  // Start smooth scrolling
		});
	</script>
	<script>
		// JavaScript to update the 'min' date for return_date based on selected departure_date
		document.getElementById('departureDate').addEventListener('change', function () {
			// Get selected departure date
			const departureDate = this.value;

			// Set the min attribute of return_date to be the selected departure date
			document.getElementById('returnDate').min = departureDate;
		});
	</script>
	<script>
		function toggleDropdown() {
			const dropdownContent = document.getElementById('dropdown-content');
			dropdownContent.style.display =
				dropdownContent.style.display === 'block' ? 'none' : 'block';
		}

		// Close dropdown if clicked outside
		window.addEventListener('click', function (e) {
			const dropdownContent = document.getElementById('dropdown-content');
			const btnUser = document.getElementById('btn-user');
			if (!btnUser.contains(e.target) && !dropdownContent.contains(e.target)) {
				dropdownContent.style.display = 'none';
			}
		});
	</script>
	<script>
    // Update min tarikh return_date berdasarkan departure_date
    document.getElementById('departureDate').addEventListener('change', function () {
        const departureDate = this.value; // Tarikh bertolak yang dipilih
        const returnDateInput = document.getElementById('returnDate');
        returnDateInput.min = departureDate; // Tetapkan minimum tarikh pulang
    });
</script>



</body>

</html>