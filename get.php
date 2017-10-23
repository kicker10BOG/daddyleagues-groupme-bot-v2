<?php



include("functions.php");

// rewrite sendMsg
defun("sendMsg", 
	function($msg){
		echo "<pre>{$msg}</pre>";
	});

include("index.php");



?>