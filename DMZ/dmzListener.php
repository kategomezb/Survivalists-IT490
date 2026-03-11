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


    if ($userFilters) {
        // lowercase incasee api is case sensitive
        $userFiltersLower = strtolower($userFilters);
        $finalResults = array();

        // 1) Try relationship-based filtering (exact IDs from search relationships)
        if (isset($result['data']['relationships'][$userFiltersLower]['data']) && is_array($result['data']['relationships'][$userFiltersLower]['data'])) {
            $dataFiltered = $result['data']['relationships'][$userFiltersLower]['data'];
            foreach ($dataFiltered as $data) {
                if (!isset($data['id'])) {
                    continue; 
                }

                if (isset($result['included']) && is_array($result['included'])) {
                    foreach ($result['included'] as $includedItem) {
                        // match each included item by ID
                        if (isset($includedItem['id']) && $includedItem['id'] == $data['id'];) {
                            $finalResults[] = $includedItem;
                        }
                    }
                }
            }
        }

        // 2) Fallback: if relationships list is empty, use type-based included filtering
        if (count($finalResults) === 0 && isset($result['included']) && is_array($result['included'])) {
            foreach ($result['included'] as $includedItem) {
                if (isset($includedItem['type']) && $includedItem['type'] == $userFiltersLower) {
                    $finalResults[] = $includedItem;
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
