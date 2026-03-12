<?php 

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

// general search 
function userSearch($userInput) {

    $tidalToken = userToken();

    $query = urlencode($userInput);

    // url template example: https://openapi.tidal.com/v2/searchResults/abba?explicitFilter=INCLUDE&countryCode=US&include=artists&include=albums&include=tracks
   // $url = "https://openapi.tidal.com/v2/searchResults/$query?explicitFilter=INCLUDE&countryCode=US&include=artists&include=albums&include=tracks";
    $url = "https://openapi.tidal.com/v2/searchResults/$query?explicitFilter=INCLUDE&countryCode=US&include=artists,albums,tracks";


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
                "accept: application/vnd.api+json", 
                "Authorization: Bearer $tidalToken",
                // "Content-Type: application/vnd.tidal.v1+json"
            ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    // ref: ZEND - error checking
    if($response === false) {
        error_log("cURL error: " . curl_error($ch));
        exit("Sorry! An error occurred.");
    }

    curl_close($ch);

    $decodedResponse = json_decode($response, true);
    //print_r($decodedResponse);

    return $decodedResponse;
}

?>
