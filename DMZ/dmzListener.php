#!/usr/bin/php
<?php


error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require '../../vendor/autoload.php';
require_once('api/tidalAPI.php');

$uri = 'mongodb://100.105.160.23:27017/';
$mongoClient = new MongoDB\Client($uri); // connect to MongoDB
$database = $mongoClient->survivalists_db; // database used by Survivalists project
$tidalDatabase = $database->tidal_db; // collection to cache Tidal search results

function searchRequest($userInput, $userFilters = null) {

    global $tidalDatabase;

    $tidalDb = $tidalDatabase;

    $searchInfo = $tidalDb->findOne(array('userInput' => $userInput));

    if ($searchInfo && (time() - $searchInfo['time']) < 604800) { // hold data for 7 days
        $result = $searchInfo['results'];
    } else {
        $apiResult = userSearch($userInput);

        if ($searchInfo) {
            $tidalDb->updateOne(array('userInput' => $userInput),
               ['$set' => array('results' => $apiResult, 'time' => time())]);

        } else {
	    $tidalDb->insertOne(array('userInput' => $userInput, 'results' => $apiResult, 'time' => time()));

	}

	$result = $apiResult;
    }

    /*
    if ($userFilters && isset($result['data']['relationships'][$userFilters]['data'])) {
	$dataFiltered = $result['data']['relationships'][$userFilters]['data'];
	$finalResults = [];

    "what was breaking this before was that the isset only checks if 'data', 'relationships','data' keys in the json exist
    but it doesnt check for if they are actually populated or not so it breaks the code as it goes when we
    get those outlier cases" 

*/

    if ($userFilters) {
        // lowercase incasee api is case sensitive extra precaution wouldnt hurt :) 
        $userFiltersLower = strtolower($userFilters);
        $finalResults = array();

        if (isset($result['data']['relationships'][$userFiltersLower]['data'])) { //checks if these keys exist in the json 
            $dataFiltered = $result['data']['relationships'][$userFiltersLower]['data'];
            foreach ($dataFiltered as $data) { 
                if (!isset($data['id'])) { //if the item doesn't have an id this block will skip it 
                    continue; 
                }

                if (isset($result['included'])) { //skip items that dont have an included section 
                    foreach ($result['included'] as $includedData) {
                        // chekc that included data has an id then makes sure it matches the data id
                        if (isset($includedData['id']) && $includedData['id'] == $data['id']) {
                            $finalResults[] = $includedData;
                        }
                    }
                }
            }
        }

        // if relationships list is empty ,or filter type is missing , relationship array is empty scan included list to make sure we dont miss anything 
        if (count($finalResults) === 0 && isset($result['included'])) {
            foreach ($result['included'] as $includedData) {
                if (isset($includedData['type']) && $includedData['type'] == $userFiltersLower) {
                    $finalResults[] = $includedData;
                }
            }
        }

        // replace result with filtered subset
        $result = $finalResults;
    }

	print_r(array("returnCode" => '0', "userInput" => $userInput, "results" => $result));
	return(array("returnCode" => '0', "userInput" => $userInput, "results" => $result)); 
}


function requestProcessor($request) {

    if (!isset($request['type'])) {
        return array("returnCode" => '1', "message" => "This is an invalid request type.");
    }

    switch ($request['type']) {
        case "searchRequest":
            $userFilters = null;
            if (isset($request['userFilters'])) {
                $userFilters = $request['userFilters']; 
            }
            return searchRequest($request['userInput'], $userFilters);
    }

    return array("returnCode" => '0', "message" => "Server received request and processed");
}

$dmzServer = new rabbitMQServer("testRabbitMQ.ini", "testDMZ");

echo "dmzRabbitMQServer BEGIN".PHP_EOL;
$dmzServer->process_requests('requestProcessor');
echo "dmzRabbitMQServer END".PHP_EOL;
exit();
?>  
