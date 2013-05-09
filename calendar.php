<?php
	define('CS810', true);
	//This is the order all files must be included in each .PHP file
	include('includes/config.php');
	include('includes/functions.php');
	include('includes/settings.php');
	include('includes/sessions.php');
	include('includes/template.php');
	
	$SETTINGS["PAGE_TITLE"] = "My Calendar";
	
	//Init the template (You can echo as many templates as listed in the filenames)
	$main_body = new Template($SETTINGS["TEMPLATE_DIR"]);
	$main_body->set_filenames(array(
	 'calendar_body' => 'calendar.htm'
	));
	
	$user_email=$db->users->findOne(array('user_id'=>$_SESSION['user_id']));
	$user_email=$user_email['email'];

	$events_js = "[";
	$events = $db->events->find();
	foreach($events as $e) 
	{
		if(in_array($user_email, $e['event_people']))
		{
			$dates = $db->dates->find(array('event_id'=>$e['event_id']));
			foreach($dates as $d)
			{
				$events_js.="{Title: '{$e['event_title']}', Date: new Date('{$d['date']}'), Time: '".implode(", ", $d['time'])."', Place: '{$e['event_place']}', ;Comments:'{$e['event_comments']}' },";	
			}
		}
	}

	$events_js = substr($events_js,0,-1);
	$events_js .= "]";

	$main_body->assign_vars(array('EVENTS'=>$events_js));

	$SETTINGS["CAL"] = "cal";
	//Echo everything
	include('includes/header.php');
	$main_body->pparse('calendar_body');
	include('includes/footer.php');
?>