<?php

// get xml config file
$xmlFile = "config.xml";
$xml = simplexml_load_file($xmlFile);
if (!$xml) {
	echo "Failed loading XML\n";
	foreach(libxml_get_errors() as $error) {
		echo "<p>", $error->message, "</p>";
	}
}
// convert to array
$xmlArr = xml2array($xml);

// easy to access config vars
$cmd_prefix = $xmlArr["config"]["cmdprefix"];
$base_url = $xmlArr["config"]["baseurl"];
$league = $xmlArr["config"]["league"];
$search_limit = $xmlArr["config"]["pslimit"];
$char_limit = $xmlArr["config"]["charlimit"];

$msgText = "";
$iMsgText = "";
$senderID = "";
$json = "";
//$cont = "";
if (isset($_REQUEST['iMsgText'])) {
	$msgText = strtolower($_REQUEST['iMsgText']);
	$iMsgText = $_REQUEST['iMsgText'];
	$senderID = "system";
}
else {
	// get the message as json
	$cont = file_get_contents("php://input");
	$json = json_decode($cont);
	// lowercase makes it easier to recognize commands
	$msgText = strtolower( $json->text );
	$iMsgText = $json->text;
	$senderID = $json->user_id;
}
$msgText = trim($msgText);
$iMsgText = trim($iMsgText);
// break into array
$command = preg_split('/\s+/', $msgText);
// check for command prefix
if (substr($command[0], 0, strlen($cmd_prefix)) == $cmd_prefix){
	// get the command
	$cmd = substr($command[0], strlen($cmd_prefix));
	switch ($cmd){
		case 'tag': // tag a group
			tag($command[1]);
			break;
		
		case 'au': // add a user
			addUser($msgText);
			break;
			
		case 'user':
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			sendUser(array_slice($command, 1));
			break;
			
		case 'us':
			if (!array_key_exists(1, $command)) {
				$command[1] = "";
			}
			if (!array_key_exists(2, $command)) {
				$command[2] = "";
			}
			sendUserSearch($command[1], $command[2]);
			break;
		
		case "tl": // get a link to a team
			sendTeamLink($command[1]);
			break;

		case "ps": // do a player search
			sendPlayerSearch(array_slice($command, 1));
			break;

		case "tws": // team week score
			sendTeamWeekScore($command[1], $command[2]);
			break;

		case "sync": // get scores for 1 week
			sendLeagueScoresForWeek($command[1]);
			break;

		case "unplayed": // get unplayed games for 1 week
			sendLeagueScoresForWeek($command[1], TRUE);
			break;

		case "week": // get current week
			sendCurrentWeek();
			break;

		case "twitch": // get Twitch link
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "list";
			}
			sendTwitchLink(array_slice($command, 1));
			break;
			
		case "info": // get info
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			sendInfo($command[1]);
			break;
			
		case "rings": // get rings
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			sendRings($command[1]);
			break;
			
		case "ghostrings": // get ghost rings
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			sendGhostRings($command[1]);
			break;
		case "img": // get img
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			sendImg($command[1]);
			break;
			
		case "youtube": // get youtube
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			sendYoutube($command[1]);
			break;
			
		case "custom": // get custom
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			sendCustom($command[1]);
			break;
			
		case "emoji": // get emoji
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			sendEmoji($command[1]);
			break;
			
		case "alias": // get alias
			if (!array_key_exists(1, $command) || $command[1] == "") {
				$command[1] = "all";
			}
			doAlias($cmd, array_slice($command, 1));
			break;
			
		case "8ball": // get 8ball
			send8Ball();
			break;

		case "help": // get help
			sendHelp();
			break;

		case "setup": // do setup
			doSetup();
			break;

		default:
			if(array_key_exists($cmd, $xmlArr["custom"])) {
				sendCustom($cmd);
			} 
			elseif (array_key_exists($cmd, $xmlArr["info"])) {
				sendInfo($cmd);
			}
			elseif(array_key_exists($cmd, $xmlArr["img"])) {
				sendImg($cmd);
			}
			elseif(array_key_exists($cmd, $xmlArr["youtube"])) {
				sendYoutube($cmd);
			}
			elseif(array_key_exists($cmd, $xmlArr["emoji"])) {
				sendEmoji($cmd);
			}
			elseif ($isAdmin) { // is admin chat?
				include("adminCommands.php");
			}
			elseif(array_key_exists($cmd, $xmlArr["alias"])) {
				if (!array_key_exists(1, $command)) {
					$command[1] = "";
				}
				doAlias($cmd, array_slice($command, 1));
			}
			else { // main chat
				sendMsg(sprintf("Invalid command. send \"%shelp\" for help", $cmd_prefix));
			}
			break;
	}
}
else if (preg_match('/.+ changed name to .*/', $iMsgText)){ // && $senderID == "system") {
	$userChanged = FALSE;
	$matches = NULL;
	preg_match('/^(.+) changed name to (.*)/', $iMsgText, $matches);
	foreach ($xmlArr['users'] as $key => $value) {
		if ($value['tagname'] == $matches[1]) {
			$xml->users->{$key}->tagname = $matches[2];
			$xml->asXml($xmlFile);
		}
	}
}
else if (!$msgText && !$iMsgText && !$json) { // testing zone
	//sendTeamWeekScore("ten", "1");
	//sendLeagueScoresForWeek("11", TRUE);
	//sendPlayerSearch(array("marcus mariota"));
	//sendCurrentWeek();
	//sendMsg("Rule Book: $rules");
	//sendTwitchLink(array("nyj", "P"));
	//setCommand(array("info", "test"));
	//sendImg($xmlArr["img"]["sscalp"]);
	//setCustom(array("testing", "this is a test"));
	//sendRings("all");
	//doAlias("contact", array());
	//send8Ball();
	//getMembers();
	//sendGroup("members");
	//tsay("members", "this is a test");
}
?>