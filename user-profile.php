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

                        if ($followerCount == null || $followerCount == 0) {
                            echo "No followers yet.";
                        } else {
                            echo "Followed by $followerCount other(s)";
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
                    if ($followingCount == null) {
                        echo "Not following anyone yet.";
                    } else {
                        echo $followingCount;
                    }
                    echo "</p>";

                    echo "<div class='friends-box'>";

                    // RETRIEVE USERNAMES OF PEOPLE USER IS FOLLOWING
                    // will use foreach loop to retrieve each username of the items in the logged in User's follower array
                    // REF for foreach loop w/ arrays: https://www.php.net/manual/en/control-structures.foreach.php
                    // REF for pulling logged in User object data from Mongo: https://stackoverflow.com/questions/26716035/display-mongodb-collections-using-html-file
                    $following = $user['following'];

                    foreach ($following as $document) {

                        echo "<div class='user-curation'>";
                        echo "<i class='fa-solid fa-user'>";
                        echo "&nbsp";
                        echo ($document);
                        echo "</i>";
                        echo "</div>";
                    };

                    echo "</div>";
                    ?>
                </div>
                <div class="profile-intro">
                    <div class="title-box">
                        <h3>Favorite Tracks</h3>
                        <a href="addFavoriteTrack.php">Add a track</a>
                    </div>
                    <!-- <p>RETRIEVE TRACKS FROM USER_LIBRARY DATABASE</p> -->
                    <?php
                    echo "<div class='friends-box'>";

                    // will use foreach loop to retrieve each track of the items in the logged in User's favoriteTracks
                    $favoriteTracks = $user['library']['favoriteTracks'];
                    // $favTrackTitle = json_encode($favoriteTracks['title']);
                    // $favTrackArtist = json_encode($favoriteTracks['artist']);

                    foreach ($favoriteTracks as $document) {
                        // echo "<i class='fa-solid fa-user'>";
                        // echo "</i>";

                        echo "<div class='user-curation'>";

                        $favTrackTitle = ($document['title']);
                        $favTrackArtist = ($document['artist']);
                        echo "$favTrackTitle by $favTrackArtist";
                        echo "</div>";
                    };

                    echo "</div>";
                    ?>
                </div>
                <div class="profile-intro">
                    <div class="title-box">
                        <h3>Favorite Artists</h3>
                        <a href="addFavoriteArtist.php">Add an artist</a>
                    </div>
                    <!-- <p>RETRIEVE ARTISTS FROM USER_LIBRARY DATABASE</p> -->
                    <div class="friends-box">
                        <!-- if statement that adds users to box when user has followers/friends -->
                        <?php
                        echo "<div class='friends-box'>";

                        // will use foreach loop to retrieve each artist in the logged in User's favoriteArtist array
                        $favoriteArtists = $user['library']['favoriteArtists'];
                        // $favTrackArtist = json_encode($favoriteArtists['artist']);

                        foreach ($favoriteArtists as $document) {
                            // echo "<i class='fa-solid fa-user'>";
                            // echo "</i>";

                            echo "<div class='user-curation'>";

                            $favArtist = ($document['artist']);
                            echo "$favArtist";
                            echo "</div>";
                        };

                        echo "</div>";
                        ?>
                    </div>
                </div>
                <div class="profile-intro">
                    <div class="title-box">
                        <h3>Favorite Albums</h3>
                        <a href="addFavoriteAlbum.php">Add an album</a>
                    </div>
                    <!-- <p>RETRIEVE ALBUMS FROM USER_LIBRARY DATABASE</p> -->
                    <div class="friends-box">
                        <!-- if statement that adds users to box when user has followers/friends -->
                        <?php
                        echo "<div class='friends-box'>";

                        // will use foreach loop to retrieve each album in the logged in User's favoriteAlbum array
                        $favoriteAlbums = $user['library']['favoriteAlbums'];
                        // $favTrackAlbum = json_encode($favoriteTracks['album']);

                        foreach ($favoriteAlbums as $document) {
                            // echo "<i class='fa-solid fa-user'>";
                            // echo "</i>";

                            echo "<div class='user-curation'>";

                            $favAlbum = ($document['album']);
                            $favArtist = ($document['artist']);
                            echo "$favAlbum by $favArtist";
                            echo "</div>";
                        };

                        echo "</div>";
                        ?>
                    </div>
                </div>
                <div class="profile-intro">
                    <div class="title-box">
                        <!-- follow recommendations (temporarily will be on left side bar but will update in the future to be on the right </p> -->

                        <h3>Who to Follow</h3>
                    </div>
                    <div class="friends-box">
                        <?php

                        // FOREACH LOOP THAT WILL GO THROUGH THE DIFFERENT REGISTERED USER OBJECTS' USERNAMES IN REG_USERS COLLECTION (MINUS THE LOGGED IN USER)
                        // show top 5 results
                        // REF: https://www.tutorialspoint.com/php_mongodb/php_mongodb_limit_records.htm

                        $filter = [];

                        $options = ['limit' => 5];
                        
                        // goes through reg_users database and limits five
                        $users = $userCollection->find($filter, $options);

                        // loop through each of the five users
                        foreach ($users as $document) {
                            // echo "<i class='fa-solid fa-user'>";
                            // echo "</i>";

                            echo "<div class='user-curation'>";

                            // for the current user in the list of five, retrieve and store their username
                            $recommendUsername = $document['username'];

                            // check to make sure one of the usernames are not the logged in user's
                            if ($recommendUsername != $username) {
                                echo $recommendUsername;
                                echo "&nbsp";
                                echo "<a>";
                                
                                // follow button
                                // if clicked update the User's following list to include the current user object it recommended
                                
                                if(isset($_POST['btn-follow'])) {
                                    $user->updateOne(
                                        ["username" => $username],
                                            ['$addToSet' => [ // ensures multiple follow-btn clicks won't add the current user multiple times in the logged in user's following 
                                                "following" => $recommendUsername
                                                ]
                                            ]
                                    );
                                }

                                echo "<form method='post'>";
                                echo "<input type='submit' name='btn-follow' value='Follow'>"; // will need to change this to a button that calls followUser() in serverRabbitMQ.php
                                echo "</form>";

                                // if button is clicked, add the recommendUsername to the signed in User's following array
                                

                            
                                echo "</a>";
                            } else {
                            }
                            echo "</div>";
                        };

                        ?>
                    </div>
                </div>
            </div>
            <div class="post-col">
                <!-- commented out the write post container because searchBar.php will be implemented from kate, so media and content can be populated and inserted on that page instead -->

                <!-- <div class="write-post-container">
                    <div class="user-profile">
                        <i class="fa-solid fa-circle-user"></i>                    
                    </div>

                </div> -->

                <!-- integrate kate's search bar here -->
                <a href="mediaSearch.php"><button>+</button></a>

                <?php
                $userPosts = $user['posts'];
                foreach ($userPosts as $document) {
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
                    $username = $user['username'];

                    // user posts are populated as objects into posts array will need to access them as strings or arrays to get the postDetails
                    // ref (went with the accessing of the MongoDB document's properties): https://www.mongodb.com/community/forums/t/accessing-object-value-from-nested-objects-in-mongodb-with-php/200733


                    $media = $document->media;
                    $content = $document->content;
                    $postedAt = $document->postedAt;

                    echo $username;

                    echo "</p>";

                    echo "<span>";

                    echo $postedAt;

                    echo "</span>";

                    // RETRIEVE DATE OBJECT FROM LOGGED IN USER'S POSTS ARRAY BY ITERATING W/ FOREACH LOOP


                    echo "<p class='post-text'>";
                    echo $media;
                    echo "&nbsp";
                    echo $content;
                    echo "</p>";

                    echo "</div>";
                    echo "</div>";
                    echo "<a href='#'>";
                    echo "<i class='fas fa-ellipsis-v'></i></a>";
                    echo "</div>";

                    echo "</div>";
                };
                ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy SocialTune, Inc. All rights reserved.</p>
    </div>

</body>

</html>