<?php
	if (!isset($_COOKIE['SessionKey'])) { // WEB REFERENCE USED: https://www.geeksforgeeks.org/php/php-cookies/
		header('Location: login.html');
		exit();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survivalists - Dashboard</title>
    <link rel ="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <div class="profileUser">
                <!-- i got the image from here: https://unsplash.com/photos/collection-of-various-music-album-covers-998pvuxqK6Y -->
                <img src="images/dashboardImage.jpg" alt="User">
                <h1>Welcome back, Survivalist!</h1>
            </div>
            <span class="status">SESSION ACTIVE</span>
        </div>
        <div class="content">
            <p>This is our dashboard. I made the box extra big so there's actually room for the stuff we're supposed to add. 
               Remember that we can change everything if you don't like it.</p>
               <a href="review.html" class="reviewLink">Leave a Review &rarr;</a>
               <a href="recommendationsRequest.php" class="actionLink">Get Recommendations &rarr;</a>
        </div>
        <!-- I added the arrow effect on the login, register and dashboard because i saw it in one website and i thought it looked good and modern. I got the link from:
        https://www.w3schools.com/charsets/ref_utf_arrows.asp -->
        <a href="home.html" class="logoutButton">Log out &rarr;</a>
    </div>
</body>
</html>
