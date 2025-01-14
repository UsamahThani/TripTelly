<?php
include "dbconnect.php";
function fetchCurrentAdminData($adminID)
{
    global $conn;

    $adminDataQuery = "SELECT * FROM admin WHERE adminID = ?";
    $stmt = $conn->prepare(query: $adminDataQuery);
    $stmt->bind_param("s", $adminID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $adminData = $result->fetch_assoc();
        return $adminData;
    } else {
        return null;
    }
}

function fetchAllAdminData()
{
    global $conn;

    // Query to fetch all admin data
    $adminSQL = "SELECT * FROM admin";

    // Execute the query
    $result = mysqli_query($conn, $adminSQL);

    // Check if the query was successful
    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        return [];
    }

    // Fetch all rows as an associative array
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    // Free the result set
    mysqli_free_result($result);

    return $data;
}

function fetchCurrentCustomerData($customerID)
{
    global $conn;

    $customerDataQuery = "SELECT * FROM user WHERE userID = ?";
    $stmt = $conn->prepare(query: $customerDataQuery);
    $stmt->bind_param("s", $customerID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customerData = $result->fetch_assoc();
        return $customerData;
    } else {
        return null;
    }
}

function fetchAllCustomerData()
{
    global $conn;

    // Query to fetch all admin data
    $adminSQL = "SELECT * FROM user";

    // Execute the query
    $result = mysqli_query($conn, $adminSQL);

    // Check if the query was successful
    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        return [];
    }

    // Fetch all rows as an associative array
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    // Free the result set
    mysqli_free_result($result);

    return $data;
}


function countTotalSaleOfWeek()
{
    global $conn;

    // SQL query to calculate the total sales from the past 7 days
    $query = "
        SELECT SUM(totalPrice) AS total_sales
        FROM payment
        WHERE paymentDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Fetch the result
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_sales'] ? $row['total_sales'] : 0; // Return total sales or 0 if null
    } else {
        // Handle query execution error
        die("Query failed: " . mysqli_error($conn));
    }
}

function fetchMostPopularLocation()
{
    global $conn;

    // SQL query to find the most picked destinationLocation
    $query = "
        SELECT destinationLocation, COUNT(destinationLocation) AS location_count
        FROM payment
        GROUP BY destinationLocation
        ORDER BY location_count DESC
        LIMIT 1
    ";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Fetch the result
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return ucwords($row['destinationLocation']);
    } else {
        // Handle no results found
        return "No popular destination found";
    }
}


function countTotalVisitor()
{
    global $conn;

    // SQL query to count all rows in the user_engagement table
    $query = "SELECT COUNT(*) AS total_visitors FROM user_engagement";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Fetch the result
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_visitors']; // Return the total count
    } else {
        // Handle query failure
        return 0;
    }
}


function countTotalBooked()
{
    global $conn;

    // SQL query to count all rows in the payment table
    $query = "SELECT COUNT(*) AS total_payments FROM payment";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Fetch the result
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_payments']; // Return the total count
    } else {
        // Handle query failure
        return 0;
    }
}

function fetchCartAndBook()
{
    global $conn;

    // SQL query to combine cart and payment data
    $query = "
        SELECT 
            CONVERT(userID USING utf8mb4) AS userID, 
            NULL AS totalPrice, 
            CONVERT(fromLocation USING utf8mb4) AS fromLocation, 
            CONVERT(destinationLocation USING utf8mb4) AS destinationLocation, 
            departureDate, 
            returnDate, 
            member AS person, 
            'Incomplete' AS status
        FROM 
            cart
        UNION
        SELECT 
            CONVERT(userID USING utf8mb4) AS userID, 
            totalPrice, 
            CONVERT(fromLocation USING utf8mb4) AS fromLocation, 
            CONVERT(destinationLocation USING utf8mb4) AS destinationLocation, 
            departureDate, 
            returnDate, 
            person, 
            'Completed' AS status
        FROM 
            payment;
    ";

    // Execute the query
    $result = $conn->query($query);

    if (!$result) {
        die("Error fetching data: " . $conn->error);
    }

    // Fetch and return data
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $user = fetchCurrentCustomerData($row['userID']);
        $userName = $user['username'];
        $row['userName'] = $userName;
        $data[] = $row;
    }

    return $data;
}

function placeNameAndValue()
{
    return [
        ['value' => 'johor', 'name' => 'Johor'],
        ['value' => 'kedah', 'name' => 'Kedah'],
        ['value' => 'kelantan', 'name' => 'Kelantan'],
        ['value' => 'melaka', 'name' => 'Melaka'],
        ['value' => 'negeri sembilan', 'name' => 'Negeri Sembilan'],
        ['value' => 'pahang', 'name' => 'Pahang'],
        ['value' => 'penang', 'name' => 'Penang'],
        ['value' => 'perak', 'name' => 'Perak'],
        ['value' => 'perlis', 'name' => 'Perlis'],
        ['value' => 'selangor', 'name' => 'Selangor'],
        ['value' => 'terengganu', 'name' => 'Terengganu'],
        ['value' => 'kuala lumpur', 'name' => 'Wilayah Persekutuan Kuala Lumpur'],
        ['value' => 'putrajaya', 'name' => 'Wilayah Persekutuan Putrajaya'],
    ];
}

function calcTotalPriceWithDest($destination)
{
    global $conn;

    $totalPriceSQL = "SELECT SUM(totalPrice) as total FROM payment WHERE destinationLocation = ?";
    $stmt = $conn->prepare($totalPriceSQL);
    $stmt->bind_param("s", $destination);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total'] ?? 0;
}

function calcTripCountWithDest($destination)
{
    global $conn;

    $tripSQL = "SELECT COUNT(*) AS trip FROM payment WHERE destinationLocation = ?";
    $stmt = $conn->prepare($tripSQL);
    $stmt->bind_param("s", $destination);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['trip'] ?? 0;
}

function googleApiKey()
{
    return 'get_your_own_api_key';
}

function fetchPlacesDataWithState($state, $placeType, $max_budget)
{
    $apiKey = googleApiKey();

    // Step 1: Convert state to latitude and longitude using Geocoding API
    $geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($state) . "&key=$apiKey";

    $geocodeResponse = file_get_contents($geocodeUrl);
    if ($geocodeResponse === FALSE) {
        die("Error occurred while fetching data from Google Geocoding API");
    }

    $geocodeData = json_decode($geocodeResponse, true);

    if (!isset($geocodeData['results'][0])) {
        return [
            'error' => "Invalid location: Unable to fetch coordinates for $state."
        ];
    }

    $latitude = $geocodeData['results'][0]['geometry']['location']['lat'];
    $longitude = $geocodeData['results'][0]['geometry']['location']['lng'];

    // Step 2: Define place type and initial radius
    $type = $placeType === 'hotel' ? 'lodging' : 'museum|tourist_attraction|point_of_interest';
    $radius = 50000; // Start with a 50 km radius
    $places = [];
    $uniquePlaceIds = []; // Track unique place IDs

    do {
        $placesUrl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$latitude,$longitude&radius=$radius&type=$type&key=$apiKey";

        $placesResponse = file_get_contents($placesUrl);
        if ($placesResponse === FALSE) {
            die("Error occurred while fetching data from Google Places API");
        }

        $placesData = json_decode($placesResponse, true);

        if (!empty($placesData['results'])) {
            foreach ($placesData['results'] as $place) {
                if (in_array($place['place_id'], $uniquePlaceIds)) {
                    continue; // Skip duplicates
                }

                $photoUrl = null; // Default to null if no photos are available
                if (!empty($place['photos'][0]['photo_reference'])) {
                    $photoReference = $place['photos'][0]['photo_reference'];
                    $photoUrl = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=$photoReference&key=$apiKey";
                }

                $places[] = [
                    'name' => $place['name'],
                    'place_id' => $place['place_id'],
                    'address' => $place['vicinity'] ?? 'N/A',
                    'rating' => $place['rating'] ?? 'N/A',
                    'price' => $placeType === 'hotel' ? generateHotelPrice($max_budget) : generateAttractionPrice($max_budget),
                    'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                    'photo_url' => $photoUrl
                ];

                $uniquePlaceIds[] = $place['place_id']; // Mark as added
            }
        }

        $radius += 20000; // Increase radius by 20 km if places are less than 10
    } while (count($places) < 10 && $radius <= 200000); // Stop if we exceed a 200 km radius or have 10+ places

    if (count($places) < 10) {
        return $places;
    }

    shuffle($places); // Randomize the order of places

    return $places;
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

function countReview($placeId)
{
    global $conn;

    $sql = 'SELECT COUNT(*) AS reviewCount FROM review WHERE placeID = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $placeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['reviewCount'];
}

function fetchPlacesDataWithID($placeID)
{
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

        // Filter local reviews by removing those listed in the review_delete table
        $filteredLocalReviews = array_filter($localReviews, function ($review) {
            $deletedReview = fetchDeletedReviews($review['reviewID']); // Assuming local reviews have 'reviewID'
            return $deletedReview === null; // Keep only reviews not found in review_delete
        });

        // Merge local reviews with Google reviews
        if (isset($result['reviews'])) {
            $allReviews = array_merge($result['reviews'], $localReviews);

            // Filter reviews by removing those listed in the review_delete table
            $filteredReviews = array_filter($allReviews, function ($review) {
                $deletedReview = fetchDeletedReviews($review['author_url']);
                return $deletedReview === null; // Keep only reviews not found in review_delete
            });

            $result['reviews'] = array_values($filteredReviews); // Re-index the array
        } else {
            $result['reviews'] = array_values($filteredLocalReviews); // Use only local reviews if no Google reviews exist
        }

        return $result;
    } else {
        // Log error for debugging
        if (isset($placeDetails['status'])) {
            echo 'Error: ' . $placeDetails['status'];
        }
        return null;
    }
}

function fetchCustomerData($userID)
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

function fetchLocalReviews($placeID)
{
    // Database connection (use your database connection details)
    global $conn;

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch local reviews
    $sql = "SELECT reviewID, userID, reviewText, reviewRating FROM review WHERE placeID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $placeID);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $author = fetchCustomerData($row['userID']);
        $author_name = $author['username'];
        $reviews[] = [
            'author_name' => $author_name, // Map userID to author_name
            'text' => $row['reviewText'],
            'rating' => $row['reviewRating'],
            'author_url' => $row['reviewID']
        ];
    }

    return $reviews;
}

function fetchDeletedReviews($reviewURL)
{
    global $conn;
    $sql = 'SELECT * FROM review_delete WHERE reviewURL = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $reviewURL);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch associative array
        $deletedReview = $result->fetch_assoc();

        // Return deleted review data
        return $deletedReview;
    } else {
        // Return null if no review is found
        return null;
    }
}


?>