<?php

function say($msg) {
	sendMsg($msg, 1);
}

function tsay($group, $msg) {
	global $xmlArr;
	$th = '[{"loci":[';
	$loc = "[0, ";
	$loc .= strlen($group)+1 ."]";
	$loci = "";
	$count = 0;
	$tags = '],"type":"mentions","user_ids":['; // }]'
	$ot = "";  
	if (!$group || $group == "all") {
		foreach ($xmlArr["users"] as $u => $v) {
			$tags .= '"' . substr($u, 1) . '",';
			$loci .= $loc . ",";
		}
	}//*
	elseif (key_exists($group, $xmlArr["groups"])) {
		foreach ($xmlArr["users"] as $u => $v) {
			if (preg_match("/\b$group\b/", $v["groups"])) {
				$tags .= '"' . substr($u, 1) . '",';
				if ($count == 0) {
					$loci .= $loc . ",";
					$count = strlen($group) + 2;
				}
				else {
					$loci .= "[";
					$loci .= $count;
					$loci .= ",";
					$loci .= $count+1;
					$loci .= "],";
					$count += 2;
				}
				print($tags . "<br>");
			}
		}
	}// */
	else {
		$msg = "$group is not a group name";
	}
	if ($ot != $tags) {
		print($tags . "<br>");
		$tags = $th . substr($loci, 0, strlen($loci)-1) . substr($tags, 0, strlen($tags)-1) . ']}]';
		print($tags . "<br> ");
		sendMsg('@' . $group . ' ' . $msg, 1, $tags); 
	}
	else {
		//print("message to send: $msg");
		sendMsg($msg); 
	}
}

function getMembers() {
	//sendMsg("Getting members...");
	global $bot_token, $user_token, $char_limit, $xml, $xmlFile, $xmlArr, $mainGroup_id;
	$url = "https://api.groupme.com/v3/groups/".$mainGroup_id."?token=$user_token";
	$scriptname = '';
	$body = '{}';
	$res = \Httpful\Request::get( $url )->sendsJson( )->body( $body )->send( );
	
	$members = $res->body->response->members;
	//*
	foreach ($members as $key => $value) {
		echo $value->nickname;
		echo "\n<br><br>\n";
		$uid = "u" . $value->user_id;
		if (!array_key_exists($uid, $xmlArr['users'])) {
			$xml->users->{$uid}->name = strtolower(explode(" ",$value->nickname)[0]);
		}
		$xml->users->{$uid}->tagname = $value->nickname;
	}
	$msg = '';
	if ($xml->asXml($xmlFile)) {
		$msg = "Members updated successully.";
	}
	else {
		$msg = "Members NOT updated successully.";
	}
	sendMsg($msg);
	 // */
}

function sendConfig($option) {
	global $xmlArr;
	$msg = "";
	if (!$option || $option == "all") {
		foreach ($xmlArr["config"] as $key => $value) {
			$msg .= "$key: $value\n";
		}
		$msg = substr($msg, 0, -1);
	}
	elseif (key_exists($option, $xmlArr["coinfig"])) {
		$msg = "$option: " . $xmlArr["config"][$option];
	}
	else {
		$msg = "$option is not a config setting";
	}
	sendMsg($msg);
}

function setCommand($options) {
	if (!$options) {
		sendMsg("no command specified");
		return;
	}
	global $xmlArr;
	$command = $options[0];
	$options = array_slice($options, 1);
	$setWorked = false;
	if (array_key_exists($command, $xmlArr)) {
		if ($command == "config") {
			$setWorked = setConfig($options);
		}
		elseif ($command == "info") {
			$setWorked = setInfo($options);
		}
		elseif ($command == "rings") {
			$setWorked = setRings($options);
		}
		elseif ($command == "youtube") {
			$setWorked = setYoutube($options);
		}
		elseif ($command == "custom") {
			$setWorked = setCustom($options);
		}
		elseif ($command == "img") {
			$setWorked = setImg($options);
		}
		elseif ($command == "alias") {
			$setWorked = setAlias($options);
		}
		elseif ($command == "twitch") {
			$setWorked = setTwitch($options);
		}
	}
}

function createGroup($name, $description = "") {
	global $xml, $xmlFile;
	$found = false;
	$msg = "";
	
	$xml->groups->{$name}->description = $description;
	
	if ($xml->asXml($xmlFile)) {
		$msg = "Group update successful";
	}
	else {
		$msg = "Group update unsuccessful";
	}
	sendMsg($msg);
}

function deleteGroup($name) {
	global $xml, $xmlFile;
	$found = false;
	$msg = "";
	
	unset($xml->groups->{$name});
	//$xml->groups->{$name}->description = $description;
	
	if ($xml->asXml($xmlFile)) {
		$msg = "Group delete successful";
	}
	else {
		$msg = "Group delete unsuccessful";
	}
	sendMsg($msg);
}

function sendGroup($option) {
	global $xmlArr;
	$msg = "";  
	if (!$option || $option == "all") {
		foreach ($xmlArr["groups"] as $g => $v) {
			$msg .= $g . ": " . $v["description"] . "\n";
		}
		$msg = substr($msg, 0, -1);
	}//*
	elseif (key_exists($option, $xmlArr["groups"])) {
		$msg = "$option: " . $xmlArr["groups"][$option]["description"];
		$msg .= "\ngroup members: ";
		$om = $msg;
		foreach ($xmlArr["users"] as $u) {
			if (preg_match("/\b$option\b/", $u["groups"])) {
				$msg .= $u["name"] . ", ";
			}
		}
		if ($om == $msg) {
			$msg .= "None";
		}
		else {
			$msg = substr($msg, 0, strlen($msg)-2);
		} 
	}// */
	else {
		$msg = "$option is not a group name";
	}
	sendMsg($msg);
}

function deleteUser($opt) {
	global $xml, $xmlFile, $xmlArr;
	$importantInfo = $xmlArr["users"];
	//sendMsg(print_r($importantInfo, 1));
	$msg = "";
	$exists = false;
	$uid = "";
	if ($opt[0] == "@") {
		//sendMsg("tag");
		global $json;
		$uid = "u" . $json->attachments[0]->user_ids[0];
		//sendMsg("uid: " . $uid);
		if (array_key_exists($uid, $importantInfo)) {;
			$exists = true;
		}
	}
	else {
		foreach ($importantInfo as $k => $v) {
			if ($v["name"] == $opt) {
				$exists = true;
				$uid = $k;
				break;
			}
		}
	}
	if ($exists) {
		//sendMsg($uid);
		unset($xml->users->{$uid});
	}
	else {
		$msg = "user not found";
	}
	if ($exists && $xml->asXml($xmlFile)) {
		$msg = "User delete successful";
	}
	elseif ($exists) {
		$msg = "User delete unsuccessful";
	}
	sendMsg($msg);
}

function editUserInfo($command) {
	global $xmlArr, $xml, $xmlFile;
	$importantInfo = $xmlArr["users"];
	//$opt = strtolower($opt);
	$command2 = preg_split('/\s+/', $command, 2)[1];
	//sendMsg("cmd: $command");
	$msg = "";
	$exists = false;
	$opt = "";
	$val = "";
	$uid = "";
	if ($command2[0] == "@") {
		global $json;
		$uid = "u" . $json->attachments[0]->user_ids[0];
		if (array_key_exists($uid, $importantInfo)) {
			$exists = true;
			$nameStart = $json->attachments[0]->loci[0][0] + $json->attachments[0]->loci[0][1];
			$command = preg_split('/\s+/', trim(substr($command, $nameStart)), 2);
			$opt = $command[0];
			$val = $command[1];
		}
	}
	else {
		$username = preg_split('/\s+/', $command2, 2)[0];
		//sendMsg("user: $username");
		foreach ($importantInfo as $k => $v) {
			if ($v["name"] == $username) {
				$exists = true;
				$uid = $k;
				$command = preg_split('/\s+/', $command, 4);
				$opt = $command[2];
				$val = $command[3];
				break;
			}
		}
	}
	//sendMsg("uid: $uid - opt: $opt - val: $val");
	if ($exists) {
		//$msg = $uid;
		if ($opt == "groups" || $opt == "group") {
			$groups = preg_split('/\s+/', $val);
			$groupstring = "";
			foreach ($groups as $k => $v) {
				if (array_key_exists($v, $xmlArr["groups"])) {
					$groupstring .= "$v, ";
				}
			}
			if ($groupstring != "") {
				$groupstring = substr($groupstring, 0, -2);
			}
			$xml->users->{$uid}->groups = $groupstring;
		}
		else {
			//sendMsg("not group");
			if ($val == "") {
				unset($xml->users->{$uid}->{$opt});
			}
			else {
				$xml->users->{$uid}->{$opt} = $val;
			}
		}
		if ($xml->asXml($xmlFile)) {
			$msg = "User update was successful";
		}
		else {
			$msg = "User update was unsuccessful";
		}
	}
	else {
		$msg = "user not found";
	}
	sendMsg($msg);
}


function setConfig($options) {
	global $xml, $xmlFile;
	if (count($options) > 1) {
		$options[1] = join(" ", array_slice($options, 1));
		$xml->config->{strtolower($options[0])} = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
	}
	else {
		unset($xml->config->{strtolower($options[0])});
	}
	if ($xml->asXml($xmlFile)) {
		sendMsg("Config update successful");
	}
	else {
		sendMsg("Config update unsuccessful");
	}
}

function setInfo($options) {
	global $xml, $xmlFile;
	if (count($options) > 1) {
		$options[1] = join(" ", array_slice($options, 1));
		$xml->info->{strtolower($options[0])} = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
	}
	else {
		unset($xml->info->{strtolower($options[0])});
	};
	if ($xml->asXml($xmlFile)) {
		sendMsg("Info update successful");
	}
	else {
		sendMsg("Info update unsuccessful");
	}
}

function setRings($options) {
	global $xml, $xmlFile;
	if (count($options) > 1) {
		$options[1] = join(" ", array_slice($options, 1));
		$xml->rings->{strtolower($options[0])} = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
	}
	else {
		unset($xml->rings->{strtolower($options[0])});
	};
	if ($xml->asXml($xmlFile)) {
		sendMsg("Ring update successful");
	}
	else {
		sendMsg("Ring update unsuccessful");
	}
}

function setImg($options) {
	global $xml, $xmlFile;
	if (count($options) > 1) {
		$options[1] = join(" ", array_slice($options, 1));
		$xml->img->{strtolower($options[0])} = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
	}
	else {
		unset($xml->img->{strtolower($options[0])});
	};
	if ($xml->asXml($xmlFile)) {
		sendMsg("Img update successful");
	}
	else {
		sendMsg("Img update unsuccessful");
	}
}

function setYoutube($options) {
	global $xml, $xmlFile;
	if (count($options) > 1) {
		$options[1] = join(" ", array_slice($options, 1));
		$xml->youtube->{strtolower($options[0])} = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
	}
	else {
		unset($xml->youtube->{strtolower($options[0])});
	};
	if ($xml->asXml($xmlFile)) {
		sendMsg("Youtube info update successful");
	}
	else {
		sendMsg("Youtube info update unsuccessful");
	}
}

function setCustom($options) {
	global $xml, $xmlFile;
	
	//sendMsg(print_r($options, TRUE));
	//exit;
	
	if (count($options) > 1) {
		if ($options[1] == "text") {
			$options[2] = join(" ", array_slice($options, 2));
			$xml->custom->{strtolower($options[0])}->text = htmlspecialchars($options[2], ENT_XML1, 'UTF-8');
		}
		elseif ($options[1] == "groups") {
			$options[2] = join(" ", array_slice($options, 2));
			$groups = preg_split('/\s+/', $options[2]);
			$groupstring = "";
			foreach ($groups as $k => $v) {
				if (array_key_exists($v, $xmlArr["groups"])) {
					$groupstring .= "$v, ";
				}
			}
			if ($groupstring != "") {
				$groupstring = substr($groupstring, 0, -2);
			}
			sendMsg("gs: $groupstring");
			$xml->custom->{strtolower($options[0])}->groups = $groupstring;
		} 
		else {
			$options[1] = join(" ", array_slice($options, 1));
			$xml->custom->{strtolower($options[0])}->text = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
		}
		//sendMsg("groups2: ".$xml->custom->{strtolower($options[0])}->groups);
		if (empty($xml->custom->{strtolower($options[0])}->groups)) {
			$xml->custom->{strtolower($options[0])}->groups = "all";
		}
	}
	else {
		unset($xml->custom->{strtolower($options[0])});
	}
	
	if ($xml->asXml($xmlFile)) {
		sendMsg("Custom info update successful");
	}
	else {
		sendMsg("Custom info update unsuccessful");
	}
}

function setAlias($options) {
	global $xml, $xmlFile;
	if (count($options) > 1) {
		$options[1] = join(" ", array_slice($options, 1));
		$xml->alias->{strtolower($options[0])} = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
	}
	else {
		unset($xml->alias->{strtolower($options[0])});
	};
	if ($xml->asXml($xmlFile)) {
		sendMsg("Alias update successful");
	}
	else {
		sendMsg("Alias info update unsuccessful");
	}
}

function setEmoji($options) {
	global $xml, $xmlFile;
	if (count($options) > 1) {
		$options[1] = join(" ", array_slice($options, 1));
		$xml->emoji->{strtolower($options[0])} = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
	}
	else {
		unset($xml->emoji->{strtolower($options[0])});
	};
	if ($xml->asXml($xmlFile)) {
		sendMsg("Emoji update successful");
	}
	else {
		sendMsg("Emoji update unsuccessful");
	}
}

function setTwitch($options) {
	global $xml, $xmlFile;
	$team = "";
	$origiTeam;
	$team = getTeam(strtolower($options[0]));
	$origiTeam = $options[0];
	if (!$team) {
		return;
	} 
	$xml->twitch->{$team} = htmlspecialchars($options[1], ENT_XML1, 'UTF-8');
	if ($xml->asXml($xmlFile)) {
		sendMsg("Twitch update successful");
	}
	else {
		sendMsg("Twitch update unsuccessful");
	}
}
?>