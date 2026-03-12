<?php

require_once ('api/tidalAPI.php');

// test for data retrieval from the API
$input = readline("Enter an artist, album, or track to search: ");

$result = userSearch($input);
print_r($result);

?>
