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
    
       <style>
        /* I picked this font but we can change it later on */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');
        
        /* I picked this color because I thought it looked cool, but we can change it. */
        body {
            background-color: #FDF5DF; 
            font-family: 'Playfair Display', serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* This is the main container for the login */
        .card {
            background: white;
            display: flex;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 5px 5px 10px #5EBEC4; /* this gives the shiny effect on the right side */
            align-items: center;
            gap: 60px;
        }

        /* This is going to be on the left side where the image and rings will be */
        .image {
            position: relative;
            width: 300px; /* i added more space because the picture was super small */
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Here Im gonna put an image with the circle shape */
        .profile {
            width: 240px; /* i incremented the size of the image */
            height: 240px;
            border-radius: 50%;
            overflow: hidden; /* this will help me to keep the image as a circle */
            z-index: 5;
            border: 4px solid white;
        }

        .profile img {
            width: 100%;
            height: 100%;
            object-fit: cover; 
        }

        /* I got the logic for these pulsing rings from this tutorial:
           https://youtu.be/louY7uT_AW8?si=4JitNOO2aZpilJ4I 
        */
        .outerGlow {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 1px solid #F92C85;
            border-radius: 50%;
            animation: ring-pulse 3.5s linear infinite;
        }

        /* this basically makes the circles actually move... it starts very small and solid then grows and fades out. I used this as a reference:
            https://www.w3schools.com/cssref/atrule_keyframes.php
        */
        @keyframes ring-pulse {
            0% { transform: scale(0.7); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }

        /* This is for the right side */
        .form {
            display: flex;
            flex-direction: column;
        }

        .form h2 {
            font-weight: normal;
            margin-bottom: 25px;
            font-size: 26px;
        }

        .input {
            margin-bottom: 18px;
        }

        .labels {
            display: block;
            font-size: 15px;
            color: #000000;
            margin-bottom: 5px;
        }

        .field-style {
            width: 240px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fcfcfc;
            font-size: 12px;
        }

        .loginButton {
            width: 100%;
            padding: 12px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.2s;
        }

        .loginButton:hover {
            background: #F92C85;
        }

        .signup {
            text-decoration: none;
            font-size: 14px;
            color: #1a1a1a;
            text-align: center;
            margin-top: 25px;
        }

        .signup:hover {
            color: #F92C85;
        }
    </style>
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
