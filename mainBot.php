<?php

// the main bot setup
include('./config.php');
include('./httpful.phar');
include('./functions.php');

echo "hello";

$bot_token = $mainBot_token;

$cont = file_get_contents("php://input");
$json = json_decode($cont);
$mainGroup_id = $json->group_id;
if (!file_exists('./maingroup.txt')) { 
	file_put_contents('./maingroup.txt', $mainGroup_id);
	echo "main: $mainGroup_id";
}

include('./index.php');

?>