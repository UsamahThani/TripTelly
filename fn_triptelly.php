<?php
include "dbconnect.php";

function generateUserID($length = 8)
{
    return bin2hex(random_bytes($length)); // Generates a random string of the specified length
}

function generateCartID()
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-';
    $id = '';
    for ($i = 0; $i < 11; $i++) {
        $id .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $id;
}

function generateHotelPrice($maxHotelPrice)
{
    $hotel_budget = $maxHotelPrice * 0.25;
    return rand(100, $hotel_budget);
}

function generateAttractionPrice($maxAttractionBudget)
{
    $attraction_budget = $maxAttractionBudget * 0.6;
    // 5% chance to exceed 100
    if (rand(1, 100) <= 10) {
        // Ensure the price is more than 100 but within the maximum budget
        return rand(101, $attraction_budget);
    }

    // Otherwise, return a regular price between 3 and 100
    return rand(3, min(100, $attraction_budget));
}

function getTotalCartPrice()
{
    // Ensure userID is set in the session
    if (!isset($_SESSION['userID'])) {
        return 0; // Return 0 if no user ID is found
    }

    $userID = $_SESSION['userID'];
    $totalPrice = 0;

    // Ensure $conn is included from dbconnect.php (which uses mysqli)
    global $conn;

    // SQL query to calculate the total price for hotels and attractions for the current user
    $sql = "
    SELECT 
        (SELECT SUM(h.hotelPrice) 
         FROM cart_hotel h 
         WHERE h.cartID IN (SELECT cartID FROM cart WHERE userID = '$userID')) AS totalHotelPrice,
        (SELECT SUM(a.attPrice) 
         FROM cart_attractions a 
         WHERE a.cartID IN (SELECT cartID FROM cart WHERE userID = '$userID')) AS totalAttractionPrice
";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Fetch the result
        $row = mysqli_fetch_assoc($result);

        // Calculate the total price
        $totalPrice = ($row['totalHotelPrice'] ?? 0) + ($row['totalAttractionPrice'] ?? 0);
    } else {
        // Error handling for query execution
        echo "Error: " . mysqli_error($conn);
    }

    return $totalPrice;
}
function googleApiKey()
{
    return 'get_your_own_google_api_key';
}
function fetchPlaceDetail($placeID)
{
    // Your Google API Key
    $apiKey = googleApiKey();

    // Google Places Details API endpoint
    $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id=" . urlencode($placeID) . "&key=" . $apiKey;

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        curl_close($ch);
        return null;
    }

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response
    $placeDetails = json_decode($response, true);

    // Check if the request was successful
    if (isset($placeDetails['status']) && $placeDetails['status'] === 'OK') {
        $result = $placeDetails['result'];

        // Fetch local reviews from the database
        $localReviews = fetchLocalReviews($placeID);

        // Merge local reviews with Google reviews
        if (isset($result['reviews'])) {
            $result['reviews'] = array_merge($result['reviews'], $localReviews);
        } else {
            $result['reviews'] = $localReviews;
        }

        return $result;
    } else {
        // Log error for debugging
        if (isset($placeDetails['status'])) {
            echo 'Error fetchPlaceDetail: ' . $placeDetails['status'];
        }
        return null;
    }
}

function fetchLocalReviews($placeID)
{
    // Database connection (use your database connection details)
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch local reviews
    $sql = "SELECT userID, placeID, reviewID, reviewText, reviewRating FROM review WHERE placeID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $placeID);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $author = fetchUserData($row['userID']);
        $author_name = $author['username'];
        $reviews[] = [
            'author_name' => $author_name, // Map userID to author_name
            'author_id' => $row['userID'],
            'place_id' => $row['placeID'],
            'review_id' => $row['reviewID'],
            'text' => $row['reviewText'],
            'rating' => $row['reviewRating']
        ];
    }

    $stmt->close();
    $conn->close();

    return $reviews;
}


function fetchUserData($userID)
{
    global $conn;

    $userSQL = "SELECT * FROM user WHERE userID = ?";
    $stmt = $conn->prepare($userSQL);

    if ($stmt) {
        // Bind the parameter to the prepared statement
        $stmt->bind_param("i", $userID);

        // Execute the statement
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        // Check if user data is found
        if ($result->num_rows > 0) {
            // Fetch associative array
            $userData = $result->fetch_assoc();

            // Return user data
            return $userData;
        } else {
            // Return null if no data is found
            return null;
        }

    } else {
        // Handle SQL preparation error
        return null;
    }
}

function fetchTripData($userID)
{
    global $conn;

    $userSQL = "SELECT * FROM payment WHERE userID = ?";
    $stmt = $conn->prepare($userSQL);

    if ($stmt) {
        // Bind the parameter to the prepared statement
        $stmt->bind_param("s", $userID);

        // Execute the statement
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        // Check if user data is found
        if ($result->num_rows > 0) {
            // Fetch all rows into an associative array
            $tripData = [];
            while ($row = $result->fetch_assoc()) {
                $tripData[] = $row;
            }

            // Return all rows
            return $tripData;
        } else {
            // Return an empty array if no data is found
            return [];
        }
    } else {
        // Handle SQL preparation error
        return [];
    }
}

function fetchTripDataArrayPlace($paymentID)
{
    global $conn;

    $userSQL = "SELECT placeData, hotelData FROM payment WHERE paymentID = ?";
    $stmt = $conn->prepare($userSQL);

    if ($stmt) {
        // Bind the parameter
        $stmt->bind_param("i", $paymentID);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();

            // Decode placeData and hotelData if they are JSON
            $placeData = json_decode($data['placeData'], true) ?? [];
            $hotelData = json_decode($data['hotelData'], true) ?? [];

            // Merge placeData and hotelData
            $mergedData = array_merge($hotelData, $placeData);

            return $mergedData;
        }
    }

    // Return empty array if no data found or query fails
    return [];
}

function fetchTripDataWithPaymentID($paymentID)
{
    global $conn;

    $userSQL = "SELECT * FROM payment WHERE paymentID = ?";
    $stmt = $conn->prepare($userSQL);

    if ($stmt) {
        // Bind the parameter to the prepared statement
        $stmt->bind_param("i", $paymentID);

        // Execute the statement
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        // Check if user data is found
        if ($result->num_rows > 0) {
            // Fetch all rows into an associative array
            $tripData = $result->fetch_assoc();

            // Return all rows
            return $tripData;
        } else {
            // Return an empty array if no data is found
            return [];
        }
    } else {
        // Handle SQL preparation error
        return [];
    }
}

?>