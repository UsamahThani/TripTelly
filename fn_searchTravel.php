<?php
session_start();
include 'dbconnect.php';
include 'fn_triptelly.php';

if (!isset($_SESSION['userID'])) {
    $_SESSION['errorMsg'] = "Login to search for travel";
    $_SESSION['form_data'] = $_POST;
    $_SESSION['user_login'] = false;
    header("Location: signin.php");
    exit();
}

if (isset($_SESSION['cart_empty'])) {
    unset($_SESSION['cart_empty']);
    header('index.php');
}

$userID = $_SESSION['userID']; // Assuming userID is stored in the session

// Handle the deletion of the cart and related data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_cart') {
    $deleteCartSql = "DELETE FROM cart WHERE userID = ?";
    $deleteHotelSql = "DELETE FROM cart_hotel WHERE cartID IN (SELECT cartID FROM cart WHERE userID = ?)";
    $deleteAttractionSql = "DELETE FROM cart_attractions WHERE cartID IN (SELECT cartID FROM cart WHERE userID = ?)";

    $conn->begin_transaction();
    try {
        $stmt1 = $conn->prepare($deleteHotelSql);
        $stmt1->bind_param("s", $userID);
        $stmt1->execute();

        $stmt2 = $conn->prepare($deleteAttractionSql);
        $stmt2->bind_param("s", $userID);
        $stmt2->execute();

        $stmt3 = $conn->prepare($deleteCartSql);
        $stmt3->bind_param("s", $userID);
        $stmt3->execute();

        $conn->commit();
        echo "success"; // Send success response to the JavaScript
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
    exit();
}

// Check if a cart exists for the current user
if (!isset($_SESSION['from_cart'])) {
    unset($_SESSION['from_cart']);
    $cartsql = "SELECT cartID FROM cart WHERE userID = ?";
    $stmt = $conn->prepare($cartsql);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If cart exists, send a JavaScript prompt to the user
        echo "<script>
            if (confirm('You already have items in your cart. Do you want to delete them?')) {
                // If user agrees to delete the cart
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '', true); // Send request to the same PHP file
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200 && xhr.responseText.trim() === 'success') {
                        alert('Your cart has been cleared.');
                        location.reload(); // Reload to proceed with the current request
                    } else {
                        alert('Error occurred while deleting the cart: ' + xhr.responseText);
                    }
                };
                xhr.send('action=delete_cart'); // Send POST request with action parameter
            } else {
                // If user declines, redirect to cart page
                window.location.href = 'cart.php';
            }
        </script>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_SESSION['form_data'])) {

    if (isset($_SESSION['form_data_cart'])) {
        $form_data = $_SESSION['form_data_cart'];
    } else {
        if (isset($_SESSION['form_data'])) {
            $form_data = $_SESSION['form_data'];
        } elseif (isset($_POST)) {
            $form_data = $_POST;
        }
    }

    // Validate input fields
    $from_loc = $form_data['from_loc'] ?? null;
    $destination_loc = $form_data['destination_loc'] ?? null;
    $departure_date = $form_data['departure_date'] ?? null;
    $return_date = $form_data['return_date'] ?? null;
    $people_num = $form_data['people_num'] ?? null;
    $max_budget = $form_data['max_budget'] ?? null;
    // Check for missing fields
    if (empty($from_loc) || empty($destination_loc) || empty($departure_date) || empty($people_num) || empty($max_budget)) {
        $_SESSION['errorMsg'] = "Please fill in all the required fields.";
        header("Location: index.php");
        exit();
    }
    $_SESSION['form_data'] = $_POST;
    // Google Places API key
    $apiKey = googleApiKey();

    // Step 1: Convert destination location to latitude and longitude using Geocoding API
    $geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($destination_loc) . "&key=$apiKey";

    // Make the API request for geocoding
    $geocodeResponse = file_get_contents($geocodeUrl);

    if ($geocodeResponse === FALSE) {
        die("Error occurred while fetching data from Google Geocoding API");
    }

    $geocodeData = json_decode($geocodeResponse, true);

    // Check if any results were returned
    if (isset($geocodeData['results'][0])) {
        $latitude = $geocodeData['results'][0]['geometry']['location']['lat'];
        $longitude = $geocodeData['results'][0]['geometry']['location']['lng'];

        // Step 2: Use the coordinates to search for hotels
        $radius = 50000; // Search within a 5 km radius
        $type = 'lodging'; // Filter for lodging type

        $placesUrl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$latitude,$longitude&radius=$radius&type=$type&key=$apiKey";

        // Make the API request for nearby places
        $placesResponse = file_get_contents($placesUrl);

        if ($placesResponse === FALSE) {
            die("Error occurred while fetching data from Google Places API");
        }

        $placesData = json_decode($placesResponse, true);

        if (!empty($placesData['results'])) {
            // Initialize an array to store hotel details
            $hotels = [];


            foreach ($placesData['results'] as $place) {
                $photoUrl = null; // Default to null if no photos are available
                if (!empty($place['photos'][0]['photo_reference'])) {
                    $photoReference = $place['photos'][0]['photo_reference'];
                    $photoUrl = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=$photoReference&key=$apiKey";
                }
                $hotels[] = [
                    'name' => $place['name'],
                    'place_id' => $place['place_id'],
                    'address' => $place['vicinity'] ?? 'test',
                    'rating' => $place['rating'] ?? 'N/A',
                    'price' => generateHotelPrice($max_budget),
                    'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                    'photo_url' => $photoUrl
                ];
            }

            // Shuffle the hotels array to randomize the order
            shuffle($hotels);

            // Store the hotel data in the session
            $_SESSION['hotel_data'] = $hotels;
            $_SESSION['form_data'] = [
                'from_loc' => $from_loc,
                'destination_loc' => $destination_loc,
                'departure_date' => $departure_date,
                'return_date' => $return_date,
                'max_budget' => $max_budget,
                'people_num' => $people_num
            ];


            $attractionTypes = 'museum|tourist_attraction|point_of_interest'; // POI types for attractions

            $attractionPlacesUrl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$latitude,$longitude&radius=$radius&type=$attractionTypes&key=$apiKey";
            $attractionPlacesResponse = file_get_contents($attractionPlacesUrl);

            if ($attractionPlacesResponse === FALSE) {
                die("Error occurred while fetching data from Google Places API");
            }

            $attractionPlacesData = json_decode($attractionPlacesResponse, true);

            if (!empty($attractionPlacesData['results'])) {
                $attractions = [];
                foreach ($attractionPlacesData['results'] as $place) {
                    $photoUrl = null; // Default to null if no photos are available
                    if (!empty($place['photos'][0]['photo_reference'])) {
                        $photoReference = $place['photos'][0]['photo_reference'];
                        $photoUrl = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=$photoReference&key=$apiKey";
                    }

                    $attractions[] = [
                        'name' => $place['name'],
                        'place_id' => $place['place_id'],
                        'address' => $place['vicinity'] ?? 'N/A',
                        'rating' => $place['rating'] ?? 'N/A',
                        'price' => generateAttractionPrice($max_budget),
                        'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                        'photo_url' => $photoUrl // Add the photo URL to the array
                    ];
                }

                shuffle($attractions); // Randomize attractions order
                $_SESSION['attraction_data'] = $attractions;
            } else {
                $_SESSION['errorMsg'] = "No tourist attractions found near the specified location.";
                header("Location: searchForm.php");
                exit();
            }

            // Redirect to searchResult.php
            if ($_GET['toAttraction'] == true) {
                header("Location: searchAttractions.php");
                exit;
            }
            header("Location: searchHotel.php");
            exit();
        } else {
            $_SESSION['errorMsg'] = "No hotels found near the specified location.";
            header("Location: searchForm.php");
            exit();
        }
    }
}


?>