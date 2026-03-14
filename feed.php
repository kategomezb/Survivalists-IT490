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

            <!-- will eventually documentadjust nav items accordingly for future deliverables AKA these are just placeholders for now -->
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

    </nav>

    <!-- + button that will redirect user to search page -->
    <a href="/searchBar.php" class="href">Make a new post</a>


    <!-- profile page -->
    <div class="profile-container">
        <?php
        // need to access each of the users in the logged in user's following array
        $followingList = $user['following'];

        // for each user in the following array
        foreach ($followingList as $userFollowed) {

            // get the current user's posts array that the pointer is pointing at during iterating round
            // following feed in this case will not include the logged in user's personal posts
            $userPosts = $userFollowed['posts'];

            foreach ($userPosts as $postsMadeByUserFollowed) {
                echo "<div class='post-container'>";
                echo "<div class='post-row'>";
                echo "<div class='user-profile'>";
                echo "<i class='fa-solid fa-circle-user'>";
                echo "</i>";
                // <!-- will modify to user icons instead of images -->
                echo "<div>";
                // <!-- <p>RETRIEVE USERNAME FROM POST DATABASE</p> -->

                echo "<p>";

                // RETRIEVE USERNAME BY LOOKING UP STORED SESSION KEY
                $posterUsername = $postsMadeByUserFollowed->username;
                $media = $postsMadeByUserFollowed->media;
                $content = $postsMadeByUserFollowed->content;
                $postedAt = $postsMadeByUserFollowed->postedAt;

                echo $posterUsername;

                echo "</p>";

                echo "<span>";

                echo $postedAt;

                echo "</span>";

                // RETRIEVE DATE OBJECT FROM LOGGED IN USER'S POSTS ARRAY BY ITERATING W/ FOREACH LOOP


                echo "<p class='post-text'>";
                echo $media;
                echo $content;
                echo "</p>";

                echo "</div>";
                echo "</div>";
                echo "<a href='#'>";
                echo "<i class='fas fa-ellipsis-v'></i></a>";
                echo "</div>";

                // <!-- <p class="post-text">RETRIEVE USERNAME FROM POST DATABASE</p> -->

                // <div class="post-row">
                //     <div class="activity-icons">
                //         <div><img src="../images/like.png" alt="like"> RETRIEVE COUNTER OBJECT FROM POST DATABASE
                //         </div>
                //         <div><img src="../images/comments.png" alt="comments"> RETRIEVE COUNTER OBJECT FROM POST
                //             DATABASE</div>
                //         <div><img src="../images/share.png" alt="shares"> RETRIEVE COUNTER OBJECT FROM POST DATABASE
                //         </div>

                //     </div>
                // </div>
                echo "</div>";
            };
        };
        ?>
    </div>

    <div class="footer">
        <p>&copy SocialTune, Inc. All rights reserved.</p>
    </div>

</body>

</html>