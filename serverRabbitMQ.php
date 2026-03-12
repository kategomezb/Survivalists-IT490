#!/usr/bin/php
<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require_once('includes/path.inc');
require_once('includes/get_host_info.inc');
require_once('includes/rabbitMQLib.inc');
require '../vendor/autoload.php';

// teacher had this on the orginal code
//function login($user,$pass){
	//TODO validate user credentials
	//return true;
//}

	//server to MongoDB connection
	$uri = 'mongodb://100.105.160.23:27017/';
	$client = new MongoDB\Client($uri);
	$db = $client->survivalists_db;

//This is the function for the session key of each login -- keep in mind we will need to link this to the DB as soon as it is done.
//I used the following links as a reference on how to do it: 
//https://stackoverflow.com/questions/48628985/is-it-cryptographically-secure-to-use-bin2hexrandom-bytesstr
//https://www.php.net/manual/en/function.bin2hex.php
function createSessionKey($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// this one confirms that the request works (same idea as register)
function login($username, $password) {
	global $db;
	
	$epochTime = time();
    file_put_contents("debugLogin.txt", "$username login at $epochTime\n", FILE_APPEND);

	$userData = $db->reg_users;

	// For this part i want to check if the user name exists on our db. For the findOne i found the reference here: 
	// https://www.mongodb.com/docs/php-library/current/reference/method/MongoDBCollection-findOne/?msockid=1d1f71cd47f763db031160e646de6252
	$foundUser = $userData->findOne(['username' => $username]);

	if ($foundUser === null) {
        $messageError = "The user doesn't exist. Maybe register first.";
         $numberResult = 1;
        $statusMessage = "error";
        return array(
            "message" => $messageError,
            "resultNumber" => $numberResult,
            "status" => $statusMessage
        );
    }

    $storedPassword = $foundUser['password'];

    if (password_verify($password, $storedPassword) === false) {
        $messageError = "Wrong password. Please try again.";
        $codeFailed = 2;
         $statusMessage = "error";

        return array(
            "resultNumber" => $codeFailed,
            "status" => $statusMessage,
            "message" => $messageError
        );
    }
	
	// this is use to generate the unique session key for the login
	 $ourSessionKey = createSessionKey();
	
     $userData->updateOne(
        ['username' => $username],
        ['$set' => ['session_key' => $ourSessionKey]]
    );

    // last thing is the response for the successful login
    $messageSuccess = "Good job! " . $username . " was able to login.";
    $numberResult = 0;
    $statusMessage = "success";

    return array(
        "status" => $statusMessage,
        "message" => $messageSuccess,
        "session_key" => $ourSessionKey,
        "resultNumber" => $numberResult
    );
}

//This is to prove that my register request reaches the server and comes back correctly.
function register($username, $password) {
	global $db;
	
	$collection = $db->reg_users;
	$hashed_password = password_hash($password, PASSWORD_DEFAULT);

	$result = $collection->insertOne(
		[
		'username' => $username,
		'password' => $hashed_password
		]
	);
    // TEMP: just confirm the request works
    return array(
		"status" => "success",
        "return_code" => 0,
        "message" => "The register request was received for " . $username
    );
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
		case "login":
			return login($req['username'], $req['password']);
		//tells rabbitmq server what function to run when a registration request comes in
		case "register":
            return register($req['username'], $req['password']);
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
