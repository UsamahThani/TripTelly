<?php
session_start();
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escape input data to prevent SQL injection
    $reviewID = mysqli_real_escape_string($conn, $_POST['reviewID']);
    $reviewText = mysqli_real_escape_string($conn, $_POST['reviewText']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $userID = $_SESSION['userID']; // Assuming userID is stored in session

    // Prepare the SQL query using placeholders for values
    $sql = "UPDATE review 
            SET reviewText = ?, 
                reviewTimestamp = NOW(), 
                reviewRating = ? 
            WHERE reviewID = ? AND userID = ?";

    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind parameters to the statement
        mysqli_stmt_bind_param($stmt, 'siss', $reviewText, $rating, $reviewID, $userID);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Set success message and redirect
            $_SESSION['success_msg'] = "Review updated successfully!";
        } else {
            // Set error message and redirect
            $_SESSION['error_msg'] = "Error updating review: " . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle statement preparation error
        $_SESSION['error_msg'] = "SQL preparation failed: " . mysqli_error($conn);
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER'] . "#" . $reviewID);
    exit();
}
?>
