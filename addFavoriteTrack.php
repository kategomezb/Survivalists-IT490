<?php
    // session authentication
	if (!isset($_COOKIE['SessionKey'])) { // WEB REFERENCE USED: https://www.geeksforgeeks.org/php/php-cookies/
		header('Location: login.html');
		exit();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survivalists - Add to Favorites Page</title>
    <link rel="stylesheet" href="css/addFavoriteTrack.css">
</head>
<body>

    <div class="card">
        <div class="image">
            <div class="outerGlow"></div>
            <!-- I left the animation delay in 1.5 because it looks more natural, and it fits the space -->
            <div class="outerGlow" style="animation-delay: 1.5s;"></div>
            
            <div class="profile">
                <img src="images/loginImage.jpg" alt="Profile Picture">
            </div>
        </div>

        <div class="form">
            <h2>Add to your favorites</strong></h2>
            
            <form method="post" action="addFavoriteTrackRequest.php">
                <div class="input">
                    <span class="labels">Title</span>
                    <input type="text" name="title" class="field-style" placeholder="Please type the title of the track." required>
                </div>

                <div class="input">
                    <span class="labels">Artist</span>
                    <input type="text" name="artist" class="field-style" placeholder="Please type the artist." required>
                </div>

                <input type="submit" value="Submit" class="loginButton">
                                <a href="dashboard.php" class="href">Back to dashboard</a>

            </form>
        </div>
    </div>

</body>
</html>