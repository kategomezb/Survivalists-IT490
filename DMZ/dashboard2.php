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
            /*margin: 0;*/
            overflow: hidden;
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
            position: relative;
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

        .content p {
	   margin-top:100px;
        }

        .searchBar {
            display: flex;
            flex-direction: row; 
            flex-wrap: wrap; 
            gap: 10px;
            width: 700px;
            position: absolute;
            top: 145px;  
            left: 50px;     
        }

        .searchInput {
            padding: 8px;
            width: 50%;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .searchButton {
            padding: 10px 20px;
            background-color: #5EBEC4;
            color: white;
            border: none;
            border-radius: 9px;
            font-weight: bold;
        }

        .searchButton:hover {
            background-color: #4daeb4; 
        }

        .checkBoxes {
            margin-top: -3px;
            width: 100%;
            font-size: 14px;
            display: block;;
        }
         
        .checkBoxes label {
            margin-right: 15px;
            cursor: pointer;
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
                <h1>Welcome back, Survivalist!</h1>
            </div>
            <span class="status">SESSION ACTIVE</span>
        </div>
        <div class="content">
            <p>This is our dashboard. I made the box extra big so there's actually room for the stuff we're supposed to add. 
               Remember that we can change everything if you don't like it.</p>

            <form class="searchBar" method="POST" action="">
                <input type="text" name="userInput" class="searchInput" placeholder="Search for songs, albums..." required>
                <button type="submit" class="searchButton">Find</button>

                <div class="checkBoxes">
                    <span>Filter by: </span>
                    <label><input type="checkbox" name="userFilters[]" value="artists" checked> Artists</label>
                    <label><input type="checkbox" name="userFilters[]" value="albums"> Albums</label>
                    <label><input type="checkbox" name="userFilters[]" value="tracks"> Tracks</label>
                </div>
            </form>
        </div>
        <!-- I added the arrow effect on the login, register and dashboard because i saw it in one website and i thought it looked good and modern. I got the link from:
        https://www.w3schools.com/charsets/ref_utf_arrows.asp -->
        <a href="home.html" class="logoutButton">Log out &rarr;</a>
    </div>
</body>
</html>


<script>

// here im trying to select all the checkboxes inputs that are on the userFilters.
// To understand querySelectorAll i refrenced: https://developer.mozilla.org/en-US/docs/Web/API/Document/querySelectorAll
document.querySelectorAll("input[name='userFilters[]']").forEach(cb => {

    // this listener will run everytime a checkbox is changed. 
    cb.addEventListener("change", function () {

        if (this.checked) {
	    // for now i just want one filter can be selected.
            document.querySelectorAll("input[name='userFilters[]']").forEach(other => {
                if (other !== this) {
                    other.checked = false;
                }
            });
        }

    });
});


</script>
