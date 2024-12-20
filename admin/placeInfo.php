<?php
session_start();
include 'fn_adminTelly.php';
include 'dbconnect.php';

$placeType = $_GET['placeType'];
$placeState = $_GET['placeState'];
$placeID = $_GET['placeID'];
$placeData = fetchPlacesDataWithID($placeID);
$pageTitle = $placeData['name'];

if (!isset($_SESSION["adminID"])) {
    header("../index.php");
}

if (isset($_SESSION['reviewURL'])) {
    unset($_SESSION['reviewURL']);
}

$adminData = fetchCurrentAdminData($_SESSION['adminID']);

//place data get here
$photos = $placeData['photos'];
$reviews = $placeData['reviews'];
$firstPhoto = "";
$otherPhoto = [];
if (!empty($photos)) {
    foreach ($photos as $photo) {
        $firstPhoto = $photo['photo_reference'];
        $otherPhoto[] = $photo['photo_reference'];
    }
}

?>
<html lang="en">

<head>
    <?php include "view_head.php" ?>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <?php include "view_adminNavbar.php" ?>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include "view_adminSidebar.php" ?>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <?php
                // for testing purpose. comment when not use
                // var_dump($reviews);
                if (isset($_GET['reviewURL'])) {
                    $_SESSION['delete_msg'] = "This review will be deleted";
                    $_SESSION['reviewURL'] = $_GET['reviewURL'];
                    include "view_alertToaster.php";
                }
                if (isset($_SESSION['success_msg']) || isset($_SESSION['error_msg'])) {
                    include "view_toaster.php";
                }

                ?>
                <div class="container-fluid px-4">
                    <h1 class="mt-4"><?= $placeData['name'] ?></h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Product</li>
                        <li class="breadcrumb-item"><?= $placeType ?> </li>
                        <li class="breadcrumb-item"><?= $placeState ?></li>
                        <li class="breadcrumb-item active"><?= $placeData['name'] ?></li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="place-data w-100 d-flex justify-content-between">
                                <div class="img-container w-40">
                                    <div class="main-img w-100 d-flex justify-content-center">
                                        <?php if (!empty($firstPhoto)) { ?>
                                            <img id="primary-img"
                                                src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=<?= $firstPhoto ?>&key=<?= googleApiKey() ?>"
                                                class="primary-img">
                                        <?php } else { ?>
                                            <img id="primary-img"
                                                src="https://ih1.redbubble.net/image.4905811447.8675/flat,750x,075,f-pad,750x1000,f8f8f8.jpg"
                                                class="primary-img">
                                        <?php } ?>
                                    </div>
                                    <div class="sub-img d-flex justify-content-center">
                                        <?php
                                        for ($i = 0; $i < 5; $i++) {
                                            if (!empty($otherPhoto[$i])) {
                                                ?>
                                                <img class="secondary-img" data-photo-reference="<?= $otherPhoto[$i] ?>"
                                                    src="https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=<?= $otherPhoto[$i] ?>&key=<?= googleApiKey() ?>">
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
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Maps and Street View</h4>
                        </div>
                        <div class="card-body">
                            <div class="map-container w-100 mb-3">
                                <iframe loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps/embed/v1/place?key=<?= googleApiKey() ?>&q=place_id:<?= htmlspecialchars($placeID) ?>">
                                </iframe>
                            </div>
                            <div class="street-container w-100">
                                <div class="street-view w-100" id="street-view"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Reviews</h4>
                        </div>
                        <div class="card-body">
                            <div class="w-100">
                                <div class="review-container">
                                    <?php if (!empty($reviews)): ?>
                                        <?php foreach ($reviews as $review): ?>
                                            <div class="review-card mb-3 w-80">
                                                <div class="row g-0">
                                                    <div class="col">
                                                        <div class="card-body">
                                                            <div class="review-header d-flex justify-content-between">
                                                                <h5 class="card-title d-flex align-items-center">
                                                                    <i
                                                                        class='bx bxs-user-circle user-icon'></i><?= htmlspecialchars($review['author_name']) ?>
                                                                </h5>
                                                                <div class="more-option">
                                                                    <a
                                                                        href="placeInfo.php?<?= http_build_query(array_merge($_GET, ['reviewURL' => $review['author_url']])) ?>">
                                                                        <i class='bx bx-x dlt-btn'></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <p class="card-text fs-6">
                                                                <?= htmlspecialchars($review['text']) ?>
                                                            </p>
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
                                </div>
                            </div>
                        </div>
                    </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <?php include "view_adminFooter.php" ?>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
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
                    primaryImg.src = `https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=${imageReferences[currentIndex]}&key=<?= googleApiKey() ?>`;
                }

                setInterval(changePrimaryImage, 5000); // Change every 5 seconds
            } else {
                console.warn("No valid photo references available for image rotation.");
            }

            // Initialize Street View
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=<?= googleApiKey() ?>&libraries=places&callback=initializeStreetView`;
            script.async = true;
            script.defer = true;
            document.body.appendChild(script);
        });

    </script>
</body>

</html>