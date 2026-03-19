#!/usr/bin/php
<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require_once('includes/path.inc');
require_once('includes/get_host_info.inc');
require_once('includes/rabbitMQLib.inc');
require '../vendor/autoload.php';

	//server to MongoDB connection
	$uri = 'mongodb://100.105.160.23:27017/'; // for local testing: 127.0.0.1
	$client = new MongoDB\Client($uri);
	$db = $client->survivalists_db;

//This is the function for the session key of each login -- keep in mind we will need to link this to the DB as soon as it is done.
//I used the following links as a reference on how to do it: 
//https://stackoverflow.com/questions/48628985/is-it-cryptographically-secure-to-use-bin2hexrandom-bytesstr
//https://www.php.net/manual/en/function.bin2hex.php
function createSessionKey($length = 32) {
    return bin2hex(random_bytes($length / 2));
}


function registration($username, $password) {
    global $db;

    $userCollection =  $db->reg_users;
    
    $existingUser = $userCollection->findOne(array('username' => $username));
    if ($existingUser) {
        return array("returnCode" => '1', "message" => "The username already exists.");
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    

    try { 
        $userCollection->insertOne(array(
        "username" => $username,
        "password" => $hashedPassword,
        "session_key" => null,
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
        "posts" => []
    ));

	print_r(array('returnCode' => '0', 'message' => 'The user was registered.', 'username' => $username, 'password' => $password));

    return array("returnCode" => '0', "message" => "The user was registered.");
    } catch(Exception $e) {
        print_r(array('returnCode' => '1', 'message' => 'The user was not registered.', 'username' => $username, 'password' => $password));

    return array("returnCode" => '1', $e->getMessage());
    }
}

function login($username, $password) {
	global $db;

	$userCollection = $db->reg_users;

	$query = array('username' => $username); // 'password' => $password);
	$user = $userCollection->findOne($query);

	if($user && password_verify($password, $user['password'])) { 
		echo "User was successfully logged in.";

		$session_key = createSessionKey(); // session_key is the variable holding generated key
		$expiration = time() + 3600; // Professor Kehoe said to utilize epoch

		$userCollection->updateOne(
			["username" => $username],
        		['$set' => [
				"session_key" => $session_key, // NOTE: keySession is database's session key variable,  session_key is server's variable
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
function createPost($session_key, $content, $postedAt) {
    global $db;

    $postCollection = $db->posts_db;

    // use session key to identify uniquely logged in user
    // will search database by session_key and retrieve attached user's username/id

	// logic from login method: $query = array('username' => $username); // 'password' => $password);
	                         // $user = $userCollection->findOne($query);

    $query = array('session_key' => $session_key);
	$user = $userCollection->findOne($query);

    $username = $user['username'];

    $postCollection->insertOne(array(
        "username" => $username,
        "media" => $media,
        "content" => $content,
        "postedAt" => $postedAt,
    ));

    print_r(array('returnCode' => '0', 'message' => 'The post was created.'));

    return array("returnCode" => '0', "message" => 'The post was created.');
}

// user profile curation with albums, artists, tracks

// passing $session_key to access user object
function addFavoriteTrack($session_key, $title, $artist) {
    global $db;

    $userCollection = $db->reg_users;

    // access stored session key
    // find corresponding User object in reg_users database w/ that session key
    // access that User object and store its username

    $query = array('session_key' => $session_key);
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
    global $db;

    $userCollection = $db->reg_users;

    // access stored session key
    // find corresponding User object in reg_users database w/ that session key
    // access that User object and store its username

    $query = array('session_key' => $session_key);
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
    global $db;

    $userCollection = $db->reg_users;

    // access stored session key
    // find corresponding User object in reg_users database w/ that session key
    // access that User object and store its username

    $query = array('session_key' => $session_key);
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

// Just to simulate the validation for now -- this will be replace with db
function validate($session_id) {
    global $db;
	
	$sessionValidator = $db->reg_users;

	$session = $sessionValidator->findOne(['session_key' => $session_id]);

	if ($session) {
		return [
			"status" => "success",
			"message" => "User validated for session: " . $session['username']
			];
	} else {
		return [
			"status" => "error",
			"message" => "User not validated for session access."
			];
	}
}

// review login
function review($session_key, $item_id, $item_type, $rating, $review_text) {
	global $db;

	// validate the session to get the username
	$users = $db->reg_users;
	$user = $users->findOne(['session_key' => $session_key]);

	if($user === null) {
		return [
			"status" => "error",
			"message" => "You must be logged in to leave a review."
		];
	}

	$username =$user['username'];

	// store the review in 'reviews' collection
	$reviews = $db->reviews;
	$reviews->insertOne([
		'username' => $username,
		'item_id' => $item_id,
		'item_type' => $item_type,
		'rating' => (int)$rating,
		'review_text' => $review_text,
		'timestamp' => time()
	]);
	return [
		"status" => "success",
		"message" => "Review submitted successfully!"
	];
}

// recommendation function
function recommendation($session_key) {
	global $db;

	// validate the session to get the username
	$users = $db->reg_users;
	$user = $users->findOne(['session_key' => $session_key]);

	if($user === null) {
		return [
			"status" => "error",
			"message" => "You must be logged in to get recommendations."
		];
	}

	$username = $user['username'];

	// get all reviews by this user
	$reviews = $db->reviews;
	$userReviews = $reviews->find(['username' => $username]);

	// collect review item names to search
	$searchTerms = [];
    foreach ($userReviews as $review) {
        // use only highly rated items (4+ stars) as basis for recommendations
        if($review['rating'] >= 4) {
            $searchTerms[] = $review['item_id'];
        }
    }
    // if there are no highly rated reviews, use all of them
    if (empty($searchTerms)) {
        $userReviews = $reviews->find(['username' => $username]);
        foreach ($userReviews as $review) {
            $searchTerms[] = $review['item_id'];
        }
    }
    // if still no reviews at all just return empty
    if (empty($searchTerms)) {
        return [
            "status" => "success",
            "recommendations" => [],
            "message" => "No reviews found. Leave some to get recommendations!"
        ];
    }

    // send search requests to the DMZ for each reviewed item
    $dmzClient = new rabbitMQClient("testRabbitMQ.ini", "testDMZ");
    $allResults = [];

    foreach ($searchTerms as $term) {
        $dmzRequest = [
            'type' => 'searchRequest',
            'userInput' => $term,
            'userFilters' => 'artists'
        ];
        $dmzResponse = $dmzClient->send_request($dmzRequest);

        if (isset($dmzResponse['results']) && is_array($dmzResponse['results'])) {
            foreach ($dmzResponse['results'] as $result) {
                // avoid duplicates by using Tidal id as key
                if (isset($result['id']) && !isset($allResults[$result['id']])) {
                    $allResults[$result['id']] = [
                        'id' => $result['id'],
                        'name' => $result['attributes']['name'] ?? 'Unknown',
                        'type' => $result['type'] ?? 'artists',
                        'popularity' => $result['attributes']['popularity'] ?? 0,
                        'tidalUrl' => $result['attributes']['externalLinks'][0]['href'] ?? '',
                        'reason' => 'Based on your reviews'
                    ];
                }
            }
        }
    }
    // sort by popularity, highest first
    usort($allResults, function($a, $b) {
        return $b['popularity'] <=> $a['popularity'];
    });
    // return top 10 recommendations
    $recommendations = array_slice(array_values($allResults), 0, 10);
    return [
        "status" => "success",
        "recommendations" => $recommendations
    ];
}

function request_processor($req){
	//echo "Received Request".PHP_EOL;
	//echo "<pre>" . var_dump($req) . "</pre>";
	if(!isset($req['type'])){
		return "Error: unsupported message type";
	}
	//Handle message type
	
	global $db;

	$type = $req['type'];
	switch($type){
		case "registration":
			return registration($req['username'], $req['password']);

		case "login":
			return login($req['username'], $req['password']);

		case "createPost":  // will generate new post entry for user and populate post collectio
			return createPost($req['username'], $req['media'], $req['content'], $req['postedAt']);
		
		// will search track library and populate selected track to user_library
        // FIXED: null username field updated $request[username] to session_key 
        case "addFavoriteTrack": 
            return addFavoriteTrack($req['session_key'],$req['title'], $req['artist']);

        case "addFavoriteArtist": // will search artist library and populate selected artist to user_library
            return addFavoriteArtist($req['session_key'],$req['artist']);

        case "addFavoriteAlbum": // will search album library and populate selected album to user_library
            return addFavoriteAlbum($req['session_key'],$req['album'], $req['artist']);

        case "getFeed":
            return getFeed($req['session_key'],$req['media'], $req['content'], $req['postedAt']);
		
		//tells rabbitmq server what function to run when a registration request comes in
		case "validate_session":
			return validate($req['session_id']);
		
		// review case
		case "review":
			return review(
				$req['session_key'],
				$req['item_id'],
				$req['item_type'],
				$req['rating'],
				$req['review_text'] ?? ''
			);

		// recommendation case
		case "recommendation":
			return recommendation($req['session_key']);
		case "echo":
			return array("return_code"=>'0', "message"=>"Echo: " .$req["message"]);
	}
	return array("return_code" => '0',
		"message" => "Server received request and processed it");
}

$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "Rabbit MQ Server Start" . PHP_EOL;
$server->process_requests('request_processor');
echo "Rabbit MQ Server Stop" . PHP_EOL;
exit();
?>
