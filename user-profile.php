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

<!-- video ref used for basic page template: https://www.youtube.com/watch?v=NljIHlZRTTE (PT 1) -->

<!-- video ref used for basic page template: https://www.youtube.com/watch?v=RrWUAmh93r4 (PT 2) -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Friend Profile | SocialTune</title>
    <link rel="stylesheet" href="/user/style.css">
    <script src="https://kit.fontawesome.com/95d0fccd5e.js" crossorigin="anonymous"></script>
</head>

<body>

    <nav>
        <div class="nav-left">
            <img src="../images/sample.png" alt="logo" class="logo">

            <!-- will eventually adjust nav items accordingly for future deliverables AKA these are just placeholders for now -->
            <!-- will use fontawesome icons for navbars -->

            <ul>
                <li><img src="images/notification.jpg" alt="notifications"></li>
                <li><img src="images/inbox.png" alt="inbox"></li>
                <li><img src="images/video.png" alt="video"></li>
            </ul>
        </div>
        <div class="nav-right">
            <div class="search-box">
                <img src="../images/search.png" alt="search icon">
                <input type="text" placeholder="Search">
            </div>
            <div class="nav-user-icon online">
                <i class="fa-solid fa-circle-user"></i>
            </div>
        </div>

        <!-- settings -->
        <!-- the original tutorial utilized javascript so I found a resource that utilizes a hidden checkbox to show dropdown -->
        <!-- reference: https://codepen.io/markcaron/pen/wdVmpB -->

        <!-- <div class="dropdown"> -->
        <!-- <input type="checkbox" id="settings-dropdown" value="" name="settings-checkbox"> -->
        <!-- <label for="settings-dropdown"> <img src="../images/profile-pic.png"
                    alt="profile-pic">
            </label> -->
        <!-- <div class="settings-menu">
            <div class="settings-menu-inner">
                <div class="user-profile">
                    <img src="../images/profile-pic.png" alt="profile-pic">
                    <div>
                        <p>Bruce Wayne</p>
                        <a href="#">See your profile</a>
                    </div>
                </div>
                <hr>
                <div class="user-profile">
                    <img src="../images/feedback.png" alt="feedback">
                    <div>
                        <p>Give Feedback</p>
                        <a href="#">Help us improve your experience</a>
                    </div>
                </div>
                <hr>
                <div class="settings-links">
                    <img src="../images/settings.png" alt="settings" class="settings-icon">
                    <a href="#">Settings & Privacy <img src="../images/arrow.png" alt="arrow" width="10px" ;></a>
                </div>
                <div class="settings-links">
                    <img src="../images/help.png" alt="settings" class="settings-icon">
                    <a href="#">Help & Support <img src="../images/arrow.png" alt="arrow" width="10px" ;></a>
                </div>
                <div class="settings-links">
                    <img src="../images/display.png" alt="settings" class="settings-icon">
                    <a href="#">Display & Accessibility <img src="../images/arrow.png" alt="arrow" width="10px"></a>
                </div>
                <div class="settings-links">
                    <img src="../images/logout.png" alt="settings" class="settings-icon">
                    <a href="#">Logout <img src="../images/arrow.png" alt="arrow" width="10px"></a>
                </div>
            </div>
        </div>
        </div> -->
    </nav>

    <!-- profile page -->
    <div class="profile-container">
        <!-- <img src="../images/cover.png" alt="cover photo" class="cover-img"> -->
        <div class="profile-details">
            <div class="pd-left">
                <div class="pd-row">
                    <i class="fa-solid fa-circle-user"></i>
                    <div>
                        <?php
                            echo "<h3>";
                            
                            // RETRIEVE USERNAME BY LOOKING UP STORED SESSION KEY
                            $username = $user['username'];
                            echo $username;
                            
                            echo "</h3>";
                        ?>
                        <?php
                            echo "<p>";

                            // <p>RETRIEVE FOLLOWER COUNTER FROM DATABASE</p>
                            $followerCount = count($user['followers']);
                            
                            if($followerCount == null) {
                                echo "No followers yet.";
                            } else {
                                echo "Followed by $followingCount other(s)";
                            }

                            echo "</p>";

                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-info">

            <div class="info-col">
                <div class="profile-intro">
                    <div class="title-box">
                        <h3>Following</h3>
                        <a href="#">View Following</a>
                    </div>

                    <?php
                        echo "<p>";

                        // <p>RETRIEVE COUNTER OF FOLLOWING FROM DATABASE</p>
                        $followingCount = count($user['following']);
                        if($followingCount == null) {
                                echo "Not following anyone yet.";
                            } else {
                                echo $followingCount;
                            }
                        echo "</p>";
                    
                        echo "<div class='friends-box'>";

                            // RETRIEVE USERNAMES OF PEOPLE USER IS FOLLOWING
                            // will use foreach loop to retrieve each username of the items in the logged in User's follower array
                            
                            $following = $user['following'];

                            foreach($following as $document) {
                            // echo "<i class='fa-solid fa-user'>";
                            // echo "</i>";

                                echo "<span>";
                                echo htmlspecialchars($document);
                                echo "</span>";
                            };

                        echo "</div>";
                    ?>
                </div>
                <div class="profile-intro">
                    <div class="title-box">
                        <h3>Favorite Tracks</h3>
                        <a href="#">View Tracks</a>
                    </div>
                    <!-- <p>RETRIEVE TRACKS FROM USER_LIBRARY DATABASE</p> -->
                    <?php
                        echo "<div class='friends-box'>";

                        // will use foreach loop to retrieve each username of the items in the logged in User's follower array
                        $favoriteTracks = $user['library']['favoriteTracks'];

                            foreach($favoriteTracks as $document) {
                            // echo "<i class='fa-solid fa-user'>";
                            // echo "</i>";

                                echo "<span>";
                                echo json_encode($document);
                                echo "</span>";
                            };

                        echo "</div>";
                    ?>
                </div>
                <div class="profile-intro">
                    <div class="title-box">
                        <h3>Favorite Artists</h3>
                        <a href="#">View Artists</a>
                    </div>
                    <p>RETRIEVE ARTISTS FROM USER_LIBRARY DATABASE</p>
                    <div class="friends-box">
                        <!-- if statement that adds users to box when user has followers/friends -->
                    </div>
                </div>
                <div class="profile-intro">
                    <div class="title-box">
                        <h3>Favorite Albums</h3>
                        <a href="#">View Albums</a>   
                    </div>
                    <p>RETRIEVE ALBUMS FROM USER_LIBRARY DATABASE</p>
                    <div class="friends-box">
                        <!-- if statement that adds users to box when user has followers/friends -->
                    </div>
                </div>
            </div>
            <div class="post-col">
                <div class="write-post-container">
                    <div class="user-profile">
                        <i class="fa-solid fa-circle-user"></i>                    
                    </div>

                    <div class="post-input-container">
                        <!-- should be some sort of search text box that drops down and populates with related seach results -->
                        <div class="post-search-box">
                            <img src="../images/search.png" alt="search icon">
                            <input type="text" placeholder="What are you listening to, $USERNAME?">
                        </div>
                        <textarea rows="3" placeholder="Say something..."></textarea>
                <button type="button" id="submit-btn">Submit</button>


                        <div class="add-post-links"> <!-- figure out the embed logic for playback -->
                            <a href="#"><img src="../images/video.png" alt="video">Live Video</a>
                        </div>
                    </div>
                </div>

                <div class="post-container">
                    <div class="post-row">
                        <div class="user-profile">
                            <i class="fa-solid fa-circle-user"></i>
                            <!-- will modify to user icons instead of images -->
                            <div>
                                <p>RETRIEVE USERNAME FROM POST DATABASE</p>
                                <span>RETRIEVE DATE OBJECT FROM POST DATABASE</span>
                            </div>
                        </div>
                        <a href="#"><i class="fas fa-ellipsis-v"></i></a>
                    </div>
                    <p class="post-text">RETRIEVE USERNAME FROM POST DATABASE</p>
                    <div class="post-row">
                        <div class="activity-icons">
                            <div><img src="../images/like.png" alt="like"> RETRIEVE COUNTER OBJECT FROM POST DATABASE
                            </div>
                            <div><img src="../images/comments.png" alt="comments"> RETRIEVE COUNTER OBJECT FROM POST
                                DATABASE</div>
                            <div><img src="../images/share.png" alt="shares"> RETRIEVE COUNTER OBJECT FROM POST DATABASE
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy SocialTune, Inc. All rights reserved.</p>
    </div>

</body>

</html>