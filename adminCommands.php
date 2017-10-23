<?php

switch ($cmd) {
	case 'say': // say something
		$sCommand = preg_split( '/\s+/', $json->text, 2);
		say($sCommand[1]);
		break;            
	
	case 'tsay': // say something
		$sCommand = preg_split( '/\s+/', $json->text, 3);
		tsay($sCommand[1], $sCommand[2]);
		break;            
	
	case 'config': // returns config info
		if (!array_key_exists(1, $command) || $command[1] == "") {
			$command[1] = "all";
		}
		sendConfig($command[1]);
		break;
		
	case 'group': // returns config info
		if (!array_key_exists(1, $command) || $command[1] == "") {
			$command[1] = "all";
		}
		sendGroup($command[1]);
		break;
		
	case 'set': // set a value in the xml file
				// use this to set info, config, and twitch names
		// use case-sensitive msg text for values
		$sCommand = preg_split( '/\s+/', $json->text);
		setCommand(array_slice($sCommand, 1));
		break;
	
	case 'cg': // create user group
	case 'eg': // or edit group
		$sCommand = preg_split( '/\s+/', $json->text, 3);
		if (!array_key_exists(2, $sCommand)) {
				$sCommand[2] = "";
			}
		createGroup($sCommand[1], $sCommand[2]);
		break;
		
	case 'rg': // remove user group
		$sCommand = preg_split( '/\s+/', $json->text);
		deleteGroup($sCommand[1]);
		break;
		
	case 'eui': // edit user info
		editUserInfo($msgText);
		break;
		
	case 'ru': // remove user
		$sCommand = preg_split( '/\s+/', $json->text);
		deleteUser($sCommand[1]);
		break;
		
	case 'm':
		getMembers();
		break;
		
	default:
		if(array_key_exists($cmd, $xmlArr["alias"])) {
			if (!array_key_exists(1, $command)) {
				$command[1] = "";
			}
			doAlias($cmd, array_slice($command, 1));
		}
		else {
			sendMsg(sprintf("Invalid command. send \"%shelp\" for help", $cmd_prefix));
		}
		break;
}

?>