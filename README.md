# Daddyleagues Groupme Bot V2

[JasonLBogle.com](http://jasonlbogle.com)

[Donate on PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LZV3AVMN5EK4Q)

[Donate on Venmo](https://venmo.com/Jason-L-Bogle)

This is an unofficial Groupme bot that you can use 
in conjuction with a Madden league on Daddyleagues. 

There are actually 2 bots. 1 for the main groupme, 
and 1 for and admin groupme. They use mostly the same
code, but the admin bot adds a couple of commands. 

---

### Dependencies

This bot depends on the httpful library. Download it at 
http://phphttpclient.com/
and simply upload the httpful.phar file to the same directory
as the bots. 

---

### Installation

1. Edit the config.xml file to your needs. 
  * config attributes
    * league: the daddyleagues directory of your league 
	(example: league website is "http://daddyleagues.com/TSL", 
	use "TSL")
    * cmdprefix: what will preceed each command
    * pslimit: the maximum numberr of players a player 
    search will return
    * charlimit: If a message by the bot exceeds this, the bot will send a link
    to get.php that will generate the message when visited
  * info attributes
    * league: the name of the league
    * commish: the league's commisioner
    * rules: a link to the rules (or you could just put the 
    rules there)
    * owners: a link to the owner list (or you could just put 
    the owner list there)
    * advance: the next scheduled advance
    * draft: the next scheduled draft
	  * botdev: I ask that you leave this setting as-is
  * custom: this is for info you don't want in the info section for when someone
  asks for all info
  * rings: the list of people with Super Bowl victories and how many they have
  * alias: all aliases - an alias is a command that executes another command
  * twitch attributes
    * put the twitch user name for each team's owner 
  * magic8ball: all the 8ball answers
  * users: all of the users the bot knows about - let the bot handle this
  * groupd: groups users can be put it
2. Upload the files to a server.
3. Create a bot for the main groupme at https://dev.groupme.com/bots
  * select the desired group 
  * paste the mainBot.php url into the Callback URL field
4. Copy the bot id and paste it into the $bot_token variable
in "config.php" where it says `$mainBot_token = "";` then reupload it.
5. Repeat steps 3 and 4, but for the admin bot. 

---

### Using Commands

Notes: 
* everything is case-insensitive unlesss otherwise noted.
* parameters in [brackets] are optional.
* paramaters in {curly braces} are required

#### Basic

##### Daddyleagues-Based Commands

* tl: Gets a link to the team on Daddyleagues
* ps [options]: performs a player search baseds on options 
  * options: all are optional
    * name: player name of the player
	* position: the position (qb, hb, wr, ...)
	* team: can be abbreviation or team name (ten or titans)
	* rookie: search only rookies (r or rookie)
	* injured: search only injured players (i or inj or injured)
	* not specifying any options will return the top players in the league
* tws {team} {week}: Returns the score for the specified team and week
  * team: can be abbreviation or team name (ten or titans)
  * week: 1-17, wc, dr, cc, sb
* sync {week}: Gets the unplayed games for a specified week
  * week: 1-17, wc, dr, cc, sb
* unplayed {week}: Gets the scores/schedule for a specified week
  * week: 1-17, wc, dr, cc, sb
* week: Gets the current week according to daddyleagues
* help: Returns a help message

##### XML-Based Commands

These commands get info from config.xml

* user [user] [attribute]: Returns info about a user
  * user: the "name" of a user; specifies the user to get
  * attribute: returns the value of a specific attribute of the specified user
* us {attribute} [value]: searches for users with a specific attribute and value (if specified)
  * attribute: the user attribute to search for
  * value: the value to match
* twitch [team or user]: Returns a link to the team's twitch
  * team: can be abbreviation or team name (ten or titans); if "list",
  a list of the twitch names is returned
  * user: a name of a user with a twitch link in their profile
* rules: Returns the rules, assuming it is setup
* info [key]: Returns info based on the key 
  * key: a key that exists in the info portion of config.xml
  * if no key is specified, it returns all info
* img [key]: Returns an image based on the key 
  * key: a key that exists in the img portion of config.xml
  * if no key is specified, it returns a list of images
* youtube [key]: Returns youtube info based on the key 
  * key: a key that exists in the youtube portion of config.xml
  * if no key is specified, it returns all youtube info\
* rings [key]: Returns ring (Super Bowl wins) count based on the key
  * key: the player's game handle (psn name or xbox live name or just whatever 
  you call them)
  * if no key is specified, it returns a list of the people with rings in order
  of most to least
* custom [key]: Returns custom info based on the key 
  * key: a key that exists in the custoim portion of config.xml
  * if no key is specified, it returns all custom info
* 8ball [question]: returns one of the 20 magic 8-ball replies
* simscores [key]: Returns a simscore based on the key
  * key: a key that exists in the simscore portion of config.xml
  * if no key is specified, it returns all simscore keys sorted high to low
* key: shorthand for "custom key", "info key", "img key", "youtube key"
  * searches in the following order:
    * custom
    * info
    * img
    * youtube
* help: returns a help message

#### Admin

* config [key]: Returns config info based on the key 
  * key: a key that exists in the config portion of config.xml
  * if no key is specified, it returns all config info. 
* set {key1} {key2} [value]: sets the value of an attribute
  * key1: options:
    * config
	* info
	* img
	* rings
	* custom
	* emoji
	* youtube
	* twitch
	* alias**
  * key2: an  existing or new attribute in the key1 portion of config.xml
  * value: case-sensitive. the value. if not specified, key2 is delted from key1
* m: update all users from the main group; this lets the bot recognize any user in the 
  main group
* eui {user} {attribute} [value] : set's an attribute on a user's profile
  * user: the "name" of the user
  * attribute: the attribute to set
  * value: the value to give the attribute; if value is left off, the attribute is 
    removed
* ru {user}: remove a user from the bot (does not remove from the group)
* group [group]: show group details
  * group: the group to see; if left off, it shows a list of groups
* cg {group} {description}: create a group
  * group: the group name
  * descriptiom: a short description of the group
* rg {group}: remove a group
  * group: the group name
* eg {group} {description}: edit a group
  * group: the group name
  * descriptiom: a short description of the group
* say {message}: have the bot say something in the main group
  * message: the message to say
* tsay {all or group} {message}: have the bot say something in the main group and tag 
  people
  * all or group:
    * all: the bot tags everyone that it has a user profile for
    * group: the bot tags everyone in the specified group (i.e. !tsay members hi members)
  * message: the message to say
  
\*\*alias: you have the ability to add an alias to any command. For example, 
"advance" is stored in the "info" portion of the xml. To display it, you can 
send "!info advance" or just "!advance" normall (and assuming "!" is the prefix.
But maybe you want to be able to just send "!adv" or "!sim" instead. You can set 
it up that way! In the chat with the admin bot, just send "!set alias adv info 
advance" or "!set alias adv advance", either way will work. 

---

## LICENSE

This plugin is being made available under the MIT License as found in the 
repository.

If you use it in your own project, please let me know. While you are not 
required to letr me know you are using it, I think it would be cool to see what 
others are using it for. 
