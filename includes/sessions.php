<?php
if (!defined('CS810')) {
	die("Nice try, no hacking allowed.");
}

//Get IP and check session data and decide what to do
$ip = encode_ip($_SERVER['REMOTE_ADDR']);
session_name();
session_start();

//Prune for old sessions
connect();


$query = array('session_user_id' => array('$lte'=> 0), 'session_time' < 0);
$db->sessions->remove($query);

$query = array('session_user_id' => array('$gt'=> 0),'session_time' < 0);
$db->sessions->remove($query);

//Get session data
fetchSessionData(session_id());

//Attempt to get session data
$query = array('session_user_id' => session_id());

//$sql = mysql_query("SELECT * FROM ". $SETTINGS["TABLE_PREFIX"] . "sessions WHERE session_id = '" . session_id() . "'");

//Makes a new session if session does not exist, the id is null, or the session time length exceeds $SETTINGS["SESSION_LENGTH"] vars
if($db->sessions->count($query) == 0 || session_id() == '' || (time() - $_SESSION['session_time'] > $SETTINGS["SESSION_LENGTH_IN"] && $_SESSION['is_logged_in']) || ((time() - $_SESSION['session_time'] > $SETTINGS["SESSION_LENGTH_OUT"] && !$_SESSION['is_logged_in'])))
{
	//Create session data
	//resetSessionData();
	//Add session data to the database
	$query = array("session_id"=>session_id(), "session_user_id" => $_SESSION['user_id'], "session_start"=>time(), "session_time"=>time(), session_ip => $ip, "session_logged_in"=>$_SESSION['is_logged_in']);
	$db->sessions->insert($query);
		
	//mysql_query("INSERT INTO ". $SETTINGS["TABLE_PREFIX"] . "sessions (session_id, session_user_id, session_start, session_time, session_ip,  session_logged_in) VALUES ('" . session_id() . "', " . $_SESSION['user_id'] . ", " . time() . ", " . time() . ", '" . $ip . "', " . $_SESSION['is_logged_in'] . ")");
} else
{
	//Session is ok, just update the time
	$_SESSION['session_time'] = time();
}
if ((bool) $_SESSION['is_logged_in'])
{
	$sid_query = array('session_id'=> session_id());
	$sessData = $db->sessions->findOne($sid_query);
	//$sessData = mysql_fetch_assoc(mysql_query("SELECT * FROM ". $SETTINGS["TABLE_PREFIX"] . "sessions WHERE session_id = '" . session_id() . "'"));
	$usr_query = array('user_id' => $_SESSION['user_id']);
	$userData = $db->users->findOne($usr_query);	
	//Make sure they didn't hack the session data

	if($_SESSION['user_id'] != $sessData["session_user_id"] || $sessData["session_logged_in"] == 0 || $userData["account_verified"] == 0)
	{
		$db->sessions->remove($sid_query);
		$query = array("session_user_id"=>$userData["user_id"]);
		$db->sessions->remove($query);
		resetSessionData();
		//Re-create the session
		$query = array("session_id"=>session_id(), "session_user_id" => $_SESSION['user_id'], "session_start"=>time(), "session_time"=>time(), session_ip => $ip, "session_logged_in"=>$_SESSION['is_logged_in']);
		$db->sessions->insert($query);
		
	} else
	{
		//Update their last active time
		//mysql_query("UPDATE ". $SETTINGS["TABLE_PREFIX"] . "users SET user_session_time = " . time() . " WHERE user_id = " . $_SESSION['user_id']);
	}
}

?>