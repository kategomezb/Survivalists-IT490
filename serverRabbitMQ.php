#!/usr/bin/php
<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require '../vendor/autoload.php';

$uri = 'mongodb://100.105.160.23:27017/';
$mongoClient = new MongoDB\Client($uri);
$database = $mongoClient->survivalists_db;

//This is the function for the session key of each login -- keep in mind we will need to link this to the DB as soon as it is done.
//I used the following links as a reference on how to do it: 
//https://stackoverflow.com/questions/48628985/is-it-cryptographically-secure-to-use-bin2hexrandom-bytesstr
//https://www.php.net/manual/en/function.bin2hex.php
function createSessionKey($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

function registration($username, $password) {
    global $database;

    $userCollection =  $database->reg_users;
    
    $existingUser = $userCollection->findOne(array('username' => $username));
    if ($existingUser) {
        return array("returnCode" => '1', "message" => "The username already exists.");
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    

    try { 
        $userCollection->insertOne(array(
        "username" => $username,
        "password" => $hashedPassword,
        "keySession" => null,
        "sessionExpiration" => null,
        "library" => [
                "favoriteTracks" => [ // needs to actively populate array as more tracks are added to favorites
                    // [
                    //     "title" => null, 
                    //     "artist" => null
                    // ] 
                ],
                "favoriteArtists" => [ // needs to actively populate array as more tracks are added to favorites
                    // [
                    //     "artist" => null
                    // ] 
                ],
                "favoriteAlbums" => [ // needs to actively populate array as more tracks are added to favorites
                    // [
                    //     "album" => null, "year" => null
                    // ]
                ]
        ],
        "posts" => [], 
        "following" => [],
        "followers" => []
    ));

	print_r(array('returnCode' => '0', 'message' => 'The user was registered.', 'username' => $username, 'password' => $password));

    return array("returnCode" => '0', "message" => "The user was registered.");
    } catch(Exception $e) {
        print_r(array('returnCode' => '1', 'message' => 'The user was not registered.', 'username' => $username, 'password' => $password));

    return array("returnCode" => '1', $e->getMessage());
    }
}

function login($username, $password) {
	global $database;

	$userCollection = $database->reg_users;

	$query = array('username' => $username); // 'password' => $password);
	$user = $userCollection->findOne($query);

	if($user && password_verify($password, $user['password'])) { 
		echo "User was successfully logged in.";

		$session_key = createSessionKey(); // session_key is the variable holding generated key
		$expiration = time() + 3600; // Professor Kehoe said to utilize epoch

		$userCollection->updateOne(
			["username" => $username],
        		['$set' => [
				"keySession" => $session_key, // NOTE: keySession is database's session key variable,  session_key is server's variable
        			"sessionExpiration" => $expiration
			]]
		);
                print_r(array('returnCode' => '0', 'message' => 'User was logged in successfully.', 'username' => $username, 'session_key' => $session_key));

    	return array("returnCode" => '0', "session_key" => $session_key, "message" => "User was logged in successfully.");

	} else {

                print_r(array("returnCode" => '1', "message" => "Invalid login."));

        return array("returnCode" => '1', "message" => "Invalid login.");

	}
}

// populates postCollection everytime a user posts something
// FIXED: missing media parameter
function createPost($session_key, $media, $content, $postedAt) {
    global $database;

    $postCollection = $database->posts_db;


	// logic from login method: $query = array('username' => $username); // 'password' => $password);
	//                          $user = $userCollection->findOne($query);

    $userCollection = $database->reg_users; // need to update the posts array in the user object

    
    // use session key to identify uniquely logged in user
    // will search database by session_key and retrieve attached user's username/id
    $query = array('keySession' => $session_key);
	$user = $userCollection->findOne($query);

    $username = $user['username'];

    // post Object instantiated
    $post = [
        "username" => $username,
        "media" => $media,
        "content" => $content,
        "postedAt" => time()
    ];

    // post Object populated into postCollection (will be used for the master feed)
    $postCollection->insertOne($post);

    // adds created post into the unique poster's post array (used for user-profile)
    $userCollection->updateOne(
			["username" => $username],
        		['$push' => [
				"posts" => $post
			]]
		);

    print_r(array('returnCode' => '0', 'message' => 'The post was created.'));

    return array("returnCode" => '0', "message" => 'The post was created.');
}

// user profile curation with albums, artists, tracks

// passing $session_key to access user object
function addFavoriteTrack($session_key, $title, $artist) {
    global $database;

    $userCollection = $database->reg_users;

    // access stored session key
    // find corresponding User object in reg_users database w/ that session key
    // access that User object and store its username

    $query = array('keySession' => $session_key);
    print_r(array('query' => $query)); // stack tracing for NULL error

	$user = $userCollection->findOne($query);
    print_r(array('user' => $user)); // stack tracing for NULL error

    // stack tracing for NULL error
    if(!$user) {
        print_r(array('message' => "session_key cannot be traced back to user"));
    } else {
        print_r(array('message' => "user's session key authenticated"));
    }

    $username = $user['username'];
    print_r(array('username' => $username)); // stack tracing for NULL error


    // mongoDB ref for appending array elements: https://www.mongodb.com/docs/manual/reference/operator/update/push/

    $userCollection->updateOne(
        ["username" => $username],
        [ 
            '$push' => [
                    "library.favoriteTracks" => [
                                                    "title" => $title,
                                                    "artist" => $artist
                                                ]
                    ]   
        ]
    );

    print_r(array('returnCode' => '0', 'message' => "$title by $artist was successfully added to favorites."));

    return array("returnCode" => '0', 'message' => "$title by $artist was successfully added to favorites.");
}

function addFavoriteArtist($session_key, $artist) {
    global $database;

    $userCollection = $database->reg_users;

    // access stored session key
    // find corresponding User object in reg_users database w/ that session key
    // access that User object and store its username

    $query = array('keySession' => $session_key);
    print_r(array('query' => $query)); // stack tracing for NULL error

	$user = $userCollection->findOne($query);
    print_r(array('user' => $user)); // stack tracing for NULL error

    // stack tracing for NULL error
    if(!$user) {
        print_r(array('message' => "session_key cannot be traced back to user"));
    } else {
        print_r(array('message' => "user's session key authenticated"));
    }

    $username = $user['username'];
    print_r(array('username' => $username)); // stack tracing for NULL error


    // mongoDB ref for appending array elements: https://www.mongodb.com/docs/manual/reference/operator/update/push/

    $userCollection->updateOne(
        ["username" => $username],
        [ 
            '$push' => [
                    "library.favoriteArtists" => [
                                                    "artist" => $artist
                                                ]
                    ]   
        ]
    );

    print_r(array('returnCode' => '0', 'message' => "$artist was successfully added to favorites."));

    return array("returnCode" => '0', 'message' => "$artist was successfully added to favorites.");
}

function addFavoriteAlbum($session_key, $album, $artist) {
    global $database;

    $userCollection = $database->reg_users;

    // access stored session key
    // find corresponding User object in reg_users database w/ that session key
    // access that User object and store its username

    $query = array('keySession' => $session_key);
    print_r(array('query' => $query)); // stack tracing for NULL error

	$user = $userCollection->findOne($query);
    print_r(array('user' => $user)); // stack tracing for NULL error

    // stack tracing for NULL error
    if(!$user) {
        print_r(array('message' => "session_key cannot be traced back to user"));
    } else {
        print_r(array('message' => "user's session key authenticated"));
    }

    $username = $user['username'];
    print_r(array('username' => $username)); // stack tracing for NULL error


    // mongoDB ref for appending array elements: https://www.mongodb.com/docs/manual/reference/operator/update/push/

    $userCollection->updateOne(
        ["username" => $username],
        [ 
            '$push' => [
                    "library.favoriteAlbums" => [
                                                    "album" => $album,
                                                    "artist" => $artist
                                                ]
                    ]   
        ]
    );

    print_r(array('returnCode' => '0', 'message' => "$album was successfully added to favorites."));

    return array("returnCode" => '0', 'message' => "$album was successfully added to favorites.");
}

// feedCollection for viewing posts of friends
// REF: https://stackoverflow.com/questions/8163850/how-to-create-a-feed-of-files-from-people-that-a-user-is-following

// {
//     "_id": (some id)
//     "UserId": (id of the user who 'owns', i.e. reads this feed)
//     "FriendId": (if of the friend who posted the file)
//     "FriendName": "John Doe" (name of the fried, denormalized)
//     "Timestamp": ...
// }

// iterate through the signed in user's following array
// retrieve and add all of those posts to a temporary array 
// function getFeed() {
//     global $database;

//     $userCollection = $database->reg_users;

//     // access stored session key
//     // find corresponding User object in reg_users database w/ that session key
//     // access that User object and store its username

//     $query = array('keySession' => $session_key);
//     print_r(array('query' => $query)); // stack tracing for NULL error

// 	$user = $userCollection->findOne($query);
//     print_r(array('user' => $user)); // stack tracing for NULL error

//     // stack tracing for NULL error
//     if(!$user) {
//         print_r(array('message' => "session_key cannot be traced back to user"));
//     } else {
//         print_r(array('message' => "user's session key authenticated"));
//     }

//     $username = $user['username'];
//     print_r(array('username' => $username)); // stack tracing for NULL error


// }

// // iterate through the posts array of the signed in user
// // retrieve and have a for loop that creates a post-container for each post element in array
// function getUserFeed() {
//     global $database;

//     $userCollection = $database->reg_users;

//     // access stored session key
//     // find corresponding User object in reg_users database w/ that session key
//     // access that User object and store its username

//     $query = array('keySession' => $session_key);
//     print_r(array('query' => $query)); // stack tracing for NULL error

// 	$user = $userCollection->findOne($query);
//     print_r(array('user' => $user)); // stack tracing for NULL error

//     // stack tracing for NULL error
//     if(!$user) {
//         print_r(array('message' => "session_key cannot be traced back to user"));
//     } else {
//         print_r(array('message' => "user's session key authenticated"));
//     }

//     $username = $user['username'];
//     print_r(array('username' => $username)); // stack tracing for NULL error

    
// }

function requestProcessor($request) {
    if (!isset($request['type'])) {
        return array("returnCode" => '1', "message" => "This is an invalid request type.");

    }

    // FIXED: null username field updated $request[username] to session_key 

    switch ($request['type']) {
        case "registration":
            return registration($request['username'], $request['password']);

	    case "login":
	        return login($request['username'],$request['password']); 

        case "createPost": // will generate new post entry for user and populate post collection
            return createPost($request['session_key'],$request['media'], $request['content'], $request['postedAt']);

        // will search track library and populate selected track to user_library
        case "addMedia";

        case "addFavoriteTrack": 
            return addFavoriteTrack($request['session_key'],$request['title'], $request['artist']);

        case "addFavoriteArtist": // will search artist library and populate selected artist to user_library
            return addFavoriteArtist($request['session_key'],$request['artist']);

        case "addFavoriteAlbum": // will search album library and populate selected album to user_library
            return addFavoriteAlbum($request['session_key'],$request['album'], $request['artist']);

        case "getFeed":
            return getFeed($request['session_key'],$request['media'], $request['content'], $request['postedAt']);
    }
    return array("returnCode" => '0', "message" => "Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>
