<?php
session_start();
include "dbconnect.php";
include "fn_triptelly.php";

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

// Check if attraction data exists in the session
if (!isset($_SESSION['attraction_data'])) {
	echo "No search results found.";
	exit();
}

$form_data = $_SESSION['form_data'];
$attraction_data = $_SESSION['attraction_data'];

// Toast controller
$toastMessage = '';
$toastClass = '';

if (isset($_SESSION['success_msg'])) {
	$toastMessage = $_SESSION['success_msg'];
	$toastClass = 'bg-success';
	unset($_SESSION['success_msg']);
} elseif (isset($_SESSION['error_msg'])) {
	$toastMessage = $_SESSION['error_msg'];
	$toastClass = 'bg-danger';
	unset($_SESSION['error_msg']);
}


$placeID = isset($_GET['placeID']) ? $_GET['placeID'] : null;
$placeData = fetchPlaceDetail($placeID);

$photos = $placeData['photos'];
$reviews = $placeData['reviews'];
$apiKey = "AIzaSyBpHdMS0pMIrrjewOeEpo5z-ykG0FMYbiQ";


$firstPhoto = "";
$otherPhoto = [];
foreach ($photos as $photo) {
	$firstPhoto = $photo['photo_reference'];
	$otherPhoto[] = $photo['photo_reference'];
}


// var_dump($placeData);
?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>

	<!--style.css-->
	<link rel="stylesheet" href="assets/css/custom.css">
	<link rel="stylesheet" href="assets/css/placeDetail.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
		integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<title>Attractions Result</title>
	<link rel="stylesheet" href="assets/css/searchResult.css">
	<link rel="shortcut icon" type="image/icon" href="assets/logo/favicon.png" />
	<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
		integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
		crossorigin="anonymous"></script>

</head>

<body>

	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="logo">
			<a href="index.php">
				trip<span>Telly</span>
			</a>
		</div>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
			aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<ul class="navbar-nav mr-auto" style="margin-left:10px">
					<li class="nav-item">
						<a class="nav-link" href="searchHotel.php">Hotels <span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="searchAttractions.php">Attractions</a>
					</li>
				</ul>
			</ul>
			<div class="action-btn">
				<button class="cart-btn position-relative">
					<a href="cart.php"><i class="bx bxs-cart"></i></a>
					<?php
					if (isset($_SESSION['cartID'])) {
						?>
						<span
							class="position-absolute bottom-60 start-70 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
						<?php
					}
					?>
				</button>
				<button type="button" class="btn btn-secondary"
					onclick="window.location.href='fn_signout.php'"><?php echo htmlspecialchars($username) ?></button>
			</div>
		</div>
	</nav>
	<main>
		<!-- toast container -->
		<?php if (!empty($toastMessage)): ?>
			<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
				<div id="liveToast" class="toast <?php echo $toastClass; ?>" role="alert" aria-live="assertive"
					aria-atomic="true" data-autohide="false">
					<div class="toast-header">
						<strong class="me-auto">Notification</strong>
						<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
					</div>
					<div class="toast-body" style="color: whitesmoke;">
						<?php echo htmlspecialchars($toastMessage); ?>
					</div>
				</div>
			</div>
		<?php endif;
		?>


		<div class="detail-container p-5 w-100 ">
			<div class="detail-title">
				<h3><?php echo $placeData['name'] ?></h3>
			</div>
			<div class="detail-content">
				<div class="place-data">
					<div class="img-container">
						<div class="main-img">
							<img src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=<?= $firstPhoto ?>&key=<?= $apiKey ?>"
								alt="" class="primary-img">
						</div>
						<div class="sub-img">
							<?php
							for ($i = 0; $i < 5; $i++) {
								?>
								<img src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=<?= $otherPhoto[$i] ?>&key=<?= $apiKey ?>"
									alt="" class="secondary-img">
								<?php
							}
							?>
						</div>
					</div>
					<div class="info-container">
						<p><strong>Name:</strong> <?= htmlspecialchars($placeData['name']) ?></p>
						<p><strong>Address:</strong> <?= htmlspecialchars($placeData['vicinity']) ?></p>
						<p><strong>Rating:</strong>
							<?= isset($placeData['rating']) ? htmlspecialchars($placeData['rating']) : 'N/A' ?> / 5</p>
						<p><strong>Total Reviews:</strong>
							<?= isset($placeData['user_ratings_total']) ? htmlspecialchars($placeData['user_ratings_total']) : '0' ?>
							Reviews</p>
						<p><strong>Type:</strong>
							<?= isset($placeData['types']) ? htmlspecialchars(implode(", ", $placeData['types'])) : 'N/A' ?>
						</p>
						<p><strong>Opening Hours:</strong>
							<?= isset($placeData['opening_hours']) ? (isset($placeData['opening_hours']['open_now']) && $placeData['opening_hours']['open_now'] ? 'Open Now' : 'Closed') : 'N/A' ?>
						</p>
						<p><strong>Phone Number:</strong>
							<?= isset($placeData['formatted_phone_number']) ? htmlspecialchars($placeData['formatted_phone_number']) : 'N/A' ?>
						</p>
						<p><strong>Website:</strong> <a
								href="<?= isset($placeData['website']) ? htmlspecialchars($placeData['website']) : '#' ?>"
								target="_blank"><?= isset($placeData['website']) ? 'Visit Website' : 'N/A' ?></a></p>
						<iframe loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
							src="https://www.google.com/maps/embed/v1/place?key=<?= $apiKey ?>&q=place_id:<?= htmlspecialchars($placeID) ?>">
						</iframe>
					</div>
				</div>
				<div class="review-container mt-5">
					<h4>Reviews</h4>
					<?php if (!empty($reviews)): ?>
						<?php foreach ($reviews as $review): ?>
							<div class="mb-3 w-80 review-card">
								<div class="row g-0">
									<div class="col">
										<div class="card-body">

											<h5 class="card-title d-flex align-items-center"><i
													class='bx bxs-user-circle user-icon'></i><?= htmlspecialchars($review['author_name']) ?>
											</h5>
											<p class="card-text fs-6"><?= htmlspecialchars($review['text']) ?></p>
											<p class="card-text">
												<span class="text-muted">Rating:
													<?= isset($review['rating']) ? htmlspecialchars($review['rating']) : 'N/A' ?>
													/ 5
												</span>
											</p>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<p>No reviews available for this attraction.</p>
					<?php endif; ?>
					<br>
					<hr>
					<br>
					<div class="mb-3 w-80">
						<form action="fn_review.php" method="POST" class="review-form">
							<h4>Send Review as <?= $username ?></h4>
							<div class="rating">
								<input type="radio" id="star5" name="rate" value="5" />
								<label for="star5" title="text"><svg viewBox="0 0 576 512" height="1em"
										xmlns="http://www.w3.org/2000/svg" class="star-solid">
										<path
											d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z">
										</path>
									</svg></label>
								<input type="radio" id="star4" name="rate" value="4" />
								<label for="star4" title="text"><svg viewBox="0 0 576 512" height="1em"
										xmlns="http://www.w3.org/2000/svg" class="star-solid">
										<path
											d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z">
										</path>
									</svg></label>
								<input type="radio" id="star3" name="rate" value="3" />
								<label for="star3" title="text"><svg viewBox="0 0 576 512" height="1em"
										xmlns="http://www.w3.org/2000/svg" class="star-solid">
										<path
											d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z">
										</path>
									</svg></label>
								<input checked="" type="radio" id="star2" name="rate" value="2" />
								<label for="star2" title="text"><svg viewBox="0 0 576 512" height="1em"
										xmlns="http://www.w3.org/2000/svg" class="star-solid">
										<path
											d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z">
										</path>
									</svg></label>
								<input type="radio" id="star1" name="rate" value="1" />
								<label for="star1" title="text"><svg viewBox="0 0 576 512" height="1em"
										xmlns="http://www.w3.org/2000/svg" class="star-solid">
										<path
											d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z">
										</path>
									</svg></label>
							</div>

							<textarea name="review_text" class="review-text"></textarea>
							<input type="hidden" name="userID" value="<?= $userID ?>">
							<input type="hidden" name="placeID" value="<?= $placeID ?>">
							<div class="btn-container">
								<button class="ui-btn" type="submit" name="submit">
									<div class="svg-wrapper-1">
										<div class="svg-wrapper">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
												height="24">
												<path fill="none" d="M0 0h24v24H0z"></path>
												<path fill="currentColor"
													d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z">
												</path>
											</svg>
										</div>
									</div>
									<span>Send</span>
								</button>
							</div>

						</form>
					</div>
				</div>

			</div>

		</div>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
		crossorigin="anonymous"></script>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			var toastEl = document.getElementById('liveToast');
			if (toastEl) {
				var toast = new bootstrap.Toast(toastEl);
				toast.show();
			}
		});
	</script>

</body>

</html>