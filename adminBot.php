<?php

// the admin bot setup

include('./config.php');
include('./httpful.phar');
include('./functions.php');

$bot_token = $adminBot_token;

//if (!file_exists('./admingroup.txt')) {
//	$cont = file_get_contents("php://input");
//	$json = json_decode($cont);
//	$adminGroup_id = $json->group_id; 
//	file_put_contents('./admingroup.txt', $adminGroup_id);
//}

$mainGroup_id = "";
if (file_exists('./maingroup.txt')) {
	 $mainGroup_id = file_get_contents('./maingroup.txt');
}

echo "mg: $mainGroup_id<br>";

// adds admin commands 
$isAdmin = TRUE;
include('adminFunctions.php');

include('./index.php');

?>