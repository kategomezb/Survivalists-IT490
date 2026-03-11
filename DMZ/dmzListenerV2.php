<?php 

require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require '../../vendor/autoload.php';


$uri = 'mongodb://100.105.160.23:27017/';
$mongoClient = new MongoDB\Client($uri);
$database = $mongoClient->survivalists_db;
$tidalCollection = $database->tidal_db;

function userToken() {

$clientID = "TNi2hY6txPCXnDAA"; // need to add to env eventually for security
$clientSecret = "xYgoYGl4KVYvvPpGJ8Fb4Ljucjvv8KzTfmyRBLrj52A=";

$authCredentials = base64_encode($clientID . ":" . $clientSecret); // required for oauth2

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://auth.tidal.com/v1/oauth2/token");

// POST method
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); // ref: TIDAL API - generate and grant credentials

$headers = [
            // "accept: application/vnd.tidal.v1+json",
            "Authorization: Basic $authCredentials",
            // "Content-Type: application/x-www-form-urlencoded"
           ];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

// ref: ZEND (https://www.zend.com/blog/curl-in-php#top) - error checking
if($response === false) {
    error_log("cURL error: " . curl_error($ch));
    exit("Sorry! An error occurred.");
}

curl_close($ch);

// ref: ZEND - process the response
$decodedResponse = json_decode($response, true);
//print_r($decodedResponse);

return $decodedResponse['access_token'];

} 

function userSearch($userInput) {

    $tidalToken = userToken();
    $query = urlencode($userInput);

    $url = "https://openapi.tidal.com/v2/searchResults/$query?explicitFilter=INCLUDE&countryCode=US&include=artists,albums,tracks";
    // $url = "https://openapi.tidal.com/v2/searchResults/$query?explicitFilter=INCLUDE&countryCode=US&include=artists&include=albums&include=tracks";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        "accept: application/vnd.api+json",
        "Authorization: Bearer $tidalToken"
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response, true);
}

// im using this ref to improve my code and understand better the comparision operators: 
// https://www.php.net/manual/en/language.operators.comparison.php 
function filterArtists($data){

    $artists = [];

    if(isset($data['included']) && is_array($data['included'])){

        foreach($data['included'] as $item){

            // making sure the item is an array and type is just artists
            // ref i used: https://www.php.net/manual/en/function.is-array.php
            if(is_array($item) && isset($item['type']) && $item['type'] == 'artists'){
                
                 
                $artists[] = [
                    "id"=>$item['id'] ?? '',
                    "name"=>$item['attributes']['name'] ?? '',
                    "popularity"=>$item['attributes']['popularity'] ?? 0
                ];

            }

        }

    }

    return $artists;
}

// i used the same logic and i used the same ref
// https://www.php.net/manual/en/language.operators.comparison.php 

function filterAlbums($data){

    $albums = [];

    // checking iif  the data exists
    if(isset($data['data']) && is_array($data['data'])){

        foreach($data['data'] as $item){

            if(is_array($item) && isset($item['type']) && $item['type'] == 'albums'){

                 // adding the album info
                $albums[] = [
                    "id"=>$item['id'] ?? '',
                    "title"=>$item['attributes']['title'] ?? ''
                 ];

             }

        }

    }

     // i need to include thsi part because something the info in included 
    if(isset($data['included']) && is_array($data['included'])){

        foreach($data['included'] as $item){

            if(is_array($item) && isset($item['type']) && $item['type'] == 'albums'){

                $albums[] = [
                    "id"=>$item['id'] ?? '',
                    "title"=>$item['attributes']['title'] ?? ''
                ];

            }

        }

    }

    return $albums;
}

// i used the same logic and i used the same ref
// https://www.php.net/manual/en/language.operators.comparison.php 
function filterTracks($data){

    $tracks = [];

    if(isset($data['data']) && is_array($data['data'])){

        foreach($data['data'] as $item){

            if(is_array($item) && isset($item['type']) && $item['type'] == 'tracks'){

		 // adding the track info 
                $tracks[] = [
                    "id"=>$item['id'] ?? '',
                    "title"=>$item['attributes']['title'] ?? ''
                  ];

             }

       }

    }

   // checking the include info again
    if(isset($data['included']) && is_array($data['included'])){

        foreach($data['included'] as $item){

            if(is_array($item) && isset($item['type']) && $item['type'] == 'tracks'){

                $tracks[] = [
                    "id"=>$item['id'] ?? '',
                    "title"=>$item['attributes']['title'] ?? ''
                ];

            }

        }

    }

    /return $tracks;
}


$server = new rabbitMQServer("testRabbitMQ.ini","testDMZ");

echo "RabbitMQ Server Started".PHP_EOL;

$server->process_requests('requestProcessor');

exit();

?>
