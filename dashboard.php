<?php
	require 'vendor/autoload.php'; 

    if (!isset($_COOKIE['SessionKey'])) { // WEB REFERENCE USED: https://www.geeksforgeeks.org/php/php-cookies/
		header('Location: login.html');
		exit();
	} else {
        $uri = "mongodb://100.105.160.23:27017/";
    
        $client = new MongoDB\Client($uri);
        $database = $client->survivalists_db;
        $userCollection = $database->reg_users;
        
        $user = $userCollection->findOne(['keySession' => $_COOKIE['SessionKey']]);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survivalists | Dashboard</title>
    <style>
        /* I picked this font but we can change it later on. Link that i accessed:
        https://fonts.google.com/specimen/Playfair+Display
        */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');
        /* I did this design because I thought it would look good, but we can change colors and font. I got the background colors from here:
        https://designshack.net/articles/trends/best-website-color-schemes/
        */
        body {
            background-color: #FDF5DF; 
            font-family: 'Playfair Display', serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        /* I made the dashboard box bigger (width) so we have space to add more stuff, but we can change this value later on */
        .dashboard {
            background: white;
            width: 800px; 
            min-height: 450px;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 5px 5px 10px #5EBEC4; /* this gives the shiny effect on the right side */
            display: flex;
            flex-direction: column;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ffffff;;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .profileUser {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .profileUser img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #F92C85;
            object-fit: cover;
        }
        /* here i might want to add the green dot to confirm that the session is active. */
        .status {
            color: #F92C85;
            font-size: 12px;
            font-weight: bold;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1 {
            margin: 0;
            font-size: 28px;
            color: #000000;
        }
        
        .content {
            flex-grow: 1; /* this part helps to push the logout button to the bottom */
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #000000;
        }
        
        .logoutButton {
            align-self: flex-start;
            margin-top: 20px;
            text-decoration: none;
            color: #F92C85;
            font-weight: bold;
            font-size: 14px;
        }
        .logoutButton:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <div class="profileUser">
                <!-- i got the image from here: https://unsplash.com/photos/collection-of-various-music-album-covers-998pvuxqK6Y -->
                <img src="images/dashboardImage.jpg" alt="User">
                <h1>
                    <?php
                        echo "Hello, ";
                        $username = $user['username'];
                        echo $username;
                        echo "!"
                    ?>
                </h1>
            </div>
            <span class="status">SESSION ACTIVE</span>
        </div>
        <div class="content">
                <a href="addFavoriteTrack.php" class="href">Add a favorite track</a>
                <br>
                <a href="addFavoriteArtist.php" class="href">Add a favorite artist</a>
                <br>
                <a href="addFavoriteAlbum.php" class="href">Add a favorite album</a>
                <br>
                <a href="user-profile.php" class="href">Profile</a>
                <br>
                <a href="createPost.php" class="href">Create post</a>
        </div>
        <!-- I added the arrow effect on the login, register and dashboard because i saw it in one website and i thought it looked good and modern. I got the link from:
        https://www.w3schools.com/charsets/ref_utf_arrows.asp -->
        <a href="home.html" class="logoutButton">Log out &rarr;</a>
    </div>
</body>
</html>
