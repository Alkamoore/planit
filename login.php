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

	if(isset($_GET['activate']))
	{
		$verify_key = addslashes($_GET['activate']);
		
		$row = $db->users->findOne(array('verified_key'=> $verify_key));
		//mysql_fetch_assoc(mysql_query("SELECT * FROM " . $SETTINGS["TABLE_PREFIX"] . "users WHERE verified_key = '" . $verify_key  . "'"));
		//Send mail
		sendMail($row['email'], 'PLANit! Account Activation', 'Click here to activate your account: http://' . $SETTINGS["DOMAIN"] . '/register.php?key='. $verify_key);
		$my_text = "Message sent";
		$main_body->assign_vars(array(
			'MESSAGE' => $my_text,
		));	 
		include('includes/header.php');
		$main_body->pparse('simple_body');
		include('includes/footer.php');
	}
	else if(isset($_POST['login']))
	{
		//Default error message
		$my_text = 'Invalid login information, please go back and try again.<br /><br /><a href="login.php">Return</a>';
		if(isset($_POST['user']) && isset($_POST['pass']))
		{

			//Get the user/pass from the POST data
			$username = strtolower(addslashes($_POST['user']));
			$password = addslashes($_POST['pass']);
			
			//Connect to the DB and find the user
			$query = array('username'=>$username, 'password'=>md5($password));
			$row = $db->users->findOne($query);
			if(!isset($row['account_verified']))
			{
				$my_text = "You have not activated your account. Please check your email. <br/> <a href='login.php?activate=".$row['verified_key']."'>But I didn't get an email can you resend it?</a>";
			}
			else if($row != NULL)
			{
				//If the row exists, update the DB session log to match this user
				$_SESSION['user_id'] = $row["user_id"];
				$query = array('session_id'=>session_id());
				$db->sessions->update($query,array('$set'=> array('session_user_id'=>$_SESSION['user_id'])));
				$db->sessions->update($query,array('$set'=> array('session_logged_in'=>'1')));
				//Update the actual session data

				fetchSessionData(session_id());
				header("Location: calendar.php");
			}
			
		}
		$main_body->assign_vars(array(
			'MESSAGE' => $my_text,
		));	 
		include('includes/header.php');
		$main_body->pparse('simple_body');
		include('includes/footer.php');
		
	} else if (isset($_GET['logout']))
	{
		
		$query = array('session_id' => session_id());
		$db->sessions->remove($query);

		$query = array('session_user_id' => $_SESSION['user_id']);
		$db->sessions->remove($query);
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