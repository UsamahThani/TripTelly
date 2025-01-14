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

$placeType = $_GET["placeType"];

if ($placeType == "Hotel") {
	// Fetch the cartID associated with this user
	$cartQuery = "SELECT cartID FROM cart WHERE userID = ?";
	$stmt = $conn->prepare($cartQuery);
	$stmt->bind_param("s", $userID);
	$stmt->execute();
	$result = $stmt->get_result();

	$userCarts = [];
	while ($row = $result->fetch_assoc()) {
		$userCarts[] = $row['cartID'];
		$_SESSION['cartID'] = $row['cartID'];
	}

	// Check if any hotel is already booked
	$hasBookedHotel = false;
	if (!empty($userCarts)) {
		$cartPlaceholders = implode(',', array_fill(0, count($userCarts), '?'));
		$cartHotelQuery = "SELECT COUNT(*) AS bookedCount FROM cart_hotel WHERE cartID IN ($cartPlaceholders)";
		$stmt = $conn->prepare($cartHotelQuery);
		$stmt->bind_param(str_repeat('s', count($userCarts)), ...$userCarts);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($row = $result->fetch_assoc()) {
			$hasBookedHotel = $row['bookedCount'] > 0;
		}
	}
} else {
	// Fetch current cartID
	$cartQuery = "SELECT cartID FROM cart WHERE userID = ?";
	$stmt = $conn->prepare($cartQuery);
	$stmt->bind_param("s", $userID);
	$stmt->execute();
	$result = $stmt->get_result();

	$cartID = null;
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$cartID = $row['cartID'];
	}

	// Fetch attractions already in the cart
	$bookedAttractions = [];
	if ($cartID) {
		$bookedQuery = "SELECT attID FROM cart_attractions WHERE cartID = ?";
		$stmt = $conn->prepare($bookedQuery);
		$stmt->bind_param("s", $cartID);
		$stmt->execute();
		$result = $stmt->get_result();

		while ($row = $result->fetch_assoc()) {
			$bookedAttractions[] = $row['attID'];
		}
	}

	// Check if the GET['placeID'] exists in bookedAttractions
	$placeID = $_GET['placeID'] ?? null; // Get placeID from the URL
	$isBooked = false;
	if ($placeID && in_array($placeID, $bookedAttractions)) {
		$isBooked = true;
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
$apiKey = googleApiKey();

$firstPhoto = "";
$otherPhoto = [];
foreach ($photos as $photo) {
	$firstPhoto = $photo['photo_reference'];
	$otherPhoto[] = $photo['photo_reference'];
}

// var_dump(value: $_SESSION['attraction_data']);
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
			<a href="index.php" style="text-decoration: none">
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
				<ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
							data-bs-toggle="dropdown"
							aria-expanded="false"><?php echo htmlspecialchars($username) ?></a>
						<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
							<li><a class="dropdown-item"
									href="userEdit.php?editUserID=<?php echo $_SESSION['userID']; ?>">Account
									Setting</a></li>
							<li><a class="dropdown-item" href="purchaseHistory.php">Purchase History</a></li>
							<li>
								<hr class="dropdown-divider" />
							</li>
							<li><a class="dropdown-item" href="fn_signout.php">Logout</a></li>
						</ul>
					</li>
				</ul>
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
							<?php if (!empty($firstPhoto)) { ?>
								<img id="primary-img"
									src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=<?= $firstPhoto ?>&key=<?= $apiKey ?>"
									class="primary-img">
							<?php } else { ?>
								<img id="primary-img"
									src="https://ih1.redbubble.net/image.4905811447.8675/flat,750x,075,f-pad,750x1000,f8f8f8.jpg"
									class="primary-img">
							<?php } ?>
						</div>
						<div class="sub-img">
							<?php
							for ($i = 0; $i < 5; $i++) {
								if (!empty($otherPhoto[$i])) {
									?>
									<img class="secondary-img" data-photo-reference="<?= $otherPhoto[$i] ?>"
										src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=<?= $otherPhoto[$i] ?>&key=<?= $apiKey ?>">
									<?php
								} else {
									?>
									<img id="secondary-img"
										src="https://ih1.redbubble.net/image.4905811447.8675/flat,750x,075,f-pad,750x1000,f8f8f8.jpg"
										class="secondary-img">
									<?php
								}
							}
							?>
						</div>
					</div>
					<div class="info-container w-100 ms-2">
						<table class="table table-success table-hover table-striped h-100 place-info-table">
							<tr>
								<th>Name</th>
								<td>:</td>
								<td><?= htmlspecialchars($placeData['name']) ?></td>
							</tr>
							<tr>
								<th>Address</th>
								<td>:</td>
								<td><?= htmlspecialchars($placeData['vicinity']) ?></td>
							</tr>
							<tr>
								<th>Rating</th>
								<td>:</td>
								<td><?= isset($placeData['rating']) ? htmlspecialchars($placeData['rating']) : 'N/A' ?>
								</td>
							</tr>
							<tr>
								<th>Total Reviews</th>
								<td>:</td>
								<td><?= isset($placeData['user_ratings_total']) ? htmlspecialchars($placeData['user_ratings_total']) : '0' ?>
									Reviews</td>
							</tr>
							<tr>
								<th>Type</th>
								<td>:</td>
								<td><?= isset($placeData['types']) ? htmlspecialchars(implode(", ", $placeData['types'])) : 'N/A' ?>
								</td>
							</tr>
							<tr>
								<th>Opening Hours</th>
								<td>:</td>
								<td><?= isset($placeData['opening_hours']) ? (isset($placeData['opening_hours']['open_now']) && $placeData['opening_hours']['open_now'] ? 'Open Now' : 'Closed') : 'N/A' ?>
								</td>
							</tr>
							<tr>
								<th>Phone Number</th>
								<td>:</td>
								<td><?= isset($placeData['formatted_phone_number']) ? htmlspecialchars($placeData['formatted_phone_number']) : 'N/A' ?>
								</td>
							</tr>
							<tr>
								<th>Website</th>
								<td>:</td>
								<td>
									<a href="<?= isset($placeData['website']) ? htmlspecialchars($placeData['website']) : '#' ?>"
										target="_blank"><?= isset($placeData['website']) ? 'Visit Website' : 'N/A' ?></a>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<div class="d-flex justify-content-center align-items-center">
										<?php
											if ($placeType == "Hotel") {
												?>
												<form action='fn_bookHotel.php' method='post' class="mt-auto">
													<input type='hidden' name='hotel_name'
														value='<?php echo htmlspecialchars($placeData['name']) ?>'>
													<input type='hidden' name='hotel_address'
														value='<?php echo htmlspecialchars($_GET['placeAdd']) ?>'>
													<input type='hidden' name='hotel_rating'
														value='<?php echo htmlspecialchars($_GET['placeRating'] ?? 'N/A') ?>'>
													<input type='hidden' name='hotel_price'
														value='<?php echo number_format($_GET['placePrice'], 2) ?>'>
													<input type='hidden' name='place_id'
														value='<?php echo htmlspecialchars(string: $placeID) ?>'>
													<?php
														if ($hasBookedHotel) {
															echo "<button type='button' class='btn btn-secondary booked-btn'
																title='You can only book one hotel'>In Cart</button>"
															;
														} else {
															echo "<button type='submit' class='btn btn-success'>Add to cart</button>";
														}
													?>
												</form>
												<?php
											} else {
												?>
												<form action='fn_bookAttractions.php' method='post' class="mt-auto">
													<input type='hidden' name='place_name'
														value='<?php echo htmlspecialchars($placeData['name']) ?>'>
													<input type='hidden' name='place_address'
														value='<?php echo htmlspecialchars($_GET['placeAdd']) ?>'>
													<input type='hidden' name='place_rating'
														value='<?php echo htmlspecialchars($_GET['placeRating'] ?? 'N/A') ?>'>
													<input type='hidden' name='place_price'
														value='<?php echo number_format($_GET['placePrice'], 2) ?>'>
													<input type='hidden' name='place_id'
														value='<?php echo htmlspecialchars(string: $placeID) ?>'>
														<?php
														if ($isBooked) {
															echo "<button type='button' class='btn btn-secondary' disabled title='Already in cart'>In Cart</button>";
														} else {
															echo "<button type='submit' class='btn btn-success'>Add to cart</button>";
														}
														?>
												</form>
												<?php
											}
										?>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>


			</div>

			<div class="detail-content mt-5">
				<div class="icard-header">
					<h4>Maps and Street View</h4>
				</div>
				<div class="icard-body">
					<div class="map-container w-100 mb-3">
						<iframe loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
							style="height: 350px"
							src="https://www.google.com/maps/embed/v1/place?key=<?= $apiKey ?>&q=place_id:<?= htmlspecialchars($placeID) ?>">
						</iframe>
					</div>
					<div class="street-container w-100">
						<div id="street-view" style="width: 100%; height: 350px;"></div>
					</div>
				</div>
			</div>

			<div class="detail-content mt-5">
				<div class="review-container">
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
					<hr>
					
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

	<script>
		// Google Maps API callback initialization
		function initializeStreetView() {
			const placeLatLng = {
				lat: <?= htmlspecialchars($placeData['geometry']['location']['lat'] ?? 'null') ?>,
				lng: <?= htmlspecialchars($placeData['geometry']['location']['lng'] ?? 'null') ?>
			};
			if (!placeLatLng.lat || !placeLatLng.lng) {
				console.error("Invalid coordinates:", placeLatLng);
			}

			// Check if coordinates are valid
			if (isNaN(placeLatLng.lat) || isNaN(placeLatLng.lng)) {
				console.error("Invalid coordinates for Street View:", placeLatLng);
				return;
			}

			// Initialize Street View
			const panorama = new google.maps.StreetViewPanorama(
				document.getElementById('street-view'), {
				position: placeLatLng,
				pov: { heading: 0, pitch: 0 },  // Adjust these for a better view
				zoom: 1,
				maxZoom: 5,  // Max zoom for better quality
				minZoom: 1   // Min zoom to avoid too much zooming out
			}
			);
		}

		document.addEventListener('DOMContentLoaded', function () {
			const primaryImg = document.getElementById('primary-img');
			const secondaryImgs = document.querySelectorAll('.secondary-img');
			const imageReferences = Array.from(secondaryImgs)
				.map(img => img.getAttribute('data-photo-reference'))
				.filter(photoRef => photoRef !== null && photoRef.trim() !== ""); // Filter out empty or null photo references

			// Only proceed if there are valid photo references
			if (imageReferences.length > 0) {
				let currentIndex = 0;

				function changePrimaryImage() {
					currentIndex = (currentIndex + 1) % imageReferences.length;
					primaryImg.src = `https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=${imageReferences[currentIndex]}&key=<?= $apiKey ?>`;
				}

				setInterval(changePrimaryImage, 5000); // Change every 5 seconds
			} else {
				console.warn("No valid photo references available for image rotation.");
			}

			// Initialize Street View
			const script = document.createElement('script');
			script.src = `https://maps.googleapis.com/maps/api/js?key=<?= $apiKey ?>&libraries=places&callback=initializeStreetView`;
			script.async = true;
			script.defer = true;
			document.body.appendChild(script);
		});
		document.addEventListener("DOMContentLoaded", function () {
    const mainImg = document.getElementById("primary-img");
    const subImgs = document.querySelectorAll(".sub-img img");

    subImgs.forEach((img) => {
        img.addEventListener("mouseenter", function () {
            const newSrc = img.getAttribute("src");
            mainImg.setAttribute("src", newSrc);
        });
    });
});

	</script>

</body>

</html>