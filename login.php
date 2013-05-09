<?php
	define('CS810', true);
	//This is the order all files must be included in each .PHP file
	include('includes/config.php');
	include('includes/functions.php');
	include('includes/settings.php');
	include('includes/sessions.php');
	include('includes/template.php');
	
	$SETTINGS["PAGE_TITLE"] = "Login Page";
	
	//Init the template (You can echo as many templates as listed in the filenames)
	$main_body = new Template($SETTINGS["TEMPLATE_DIR"]);
	$main_body->set_filenames(array(
	 'login_body' => 'login.htm',
	 'simple_body' => 'simple_body.htm'
	));
	
	if(isset($_POST['login']))
	{
		//Default error message
		$my_text = 'Invalid login information, please go back and try again.<br /><br /><a href="login.php">Return</a>';
		if(isset($_POST['user']) && isset($_POST['pass']))
		{

			//Get the user/pass from the POST data
			$username = addslashes($_POST['user']);
			$password = addslashes($_POST['pass']);
			//Connect to the DB and find the user
			$query = array('username'=>$username, 'password'=>md5($password));
			$row = $db->users;//->findOne($query);
			die(var_dump($row));
			if($row != NULL)
			{
				
				//If the row exists, update the DB session log to match this user
				$_SESSION['user_id'] = $row["user_id"];
				$query = array('session_id'=>session_id());
				$db->sessions->update($query,array('session_user_id'=>$_SESSION['user_id'], 
					"session_logged_in"=>1));
				//Update the actual session data
				fetchSessionData(session_id());
				$my_text = 'Welcome, ' . $_SESSION["username"] . "!";
			}
			
		}
		$main_body->assign_vars(array(
			'MESSAGE' => $my_text,
		));	 
		include('includes/header.php');
		$main_body->pparse('simple_body');
		include('includes/footer.php');
		//header("Location: calendar.php");
	} else if (isset($_GET['logout']))
	{
		
		mysql_query("DELETE FROM ". $SETTINGS["TABLE_PREFIX"] . "sessions WHERE session_id = " . session_id());
		mysql_query("DELETE FROM ". $SETTINGS["TABLE_PREFIX"] . "sessions WHERE session_user_id = " . $_SESSION["user_id"]);
		resetSessionData();
		
		$my_text = 'Logout successful.<br /><br /><a href="index.php">Return</a>';
		$main_body->assign_vars(array(
			'MESSAGE' => $my_text,
		));	 
		include('includes/header.php');
		$main_body->pparse('simple_body');
		include('includes/footer.php');
	} else if ((bool) $_SESSION['is_logged_in'])
	{
		header("Location: calendar.php");
	} else
	{
		//Echo everything
		include('includes/header.php');
		$main_body->pparse('login_body');
		include('includes/footer.php');			 
		
	}
	
?>