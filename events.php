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
	 'events_body' => 'event.htm'
	));
	
	if(isset($_GET['id']))
	{
		$event_id = $_GET['id'];
		$event_data = $db->events->findOne(array('event_id'=>$event_id));
echo $db->events->findOne(array('event_id'=>$event_id));

		$main_body->assign_vars(array('EVENT_TITLE'=>$event_data['event_title'], 'WHERE'=> $event_data['event_place'],
			'COMMENTS' => $event_data['event_comments']
			));
	}
	


	//Echo everything
	include('includes/header.php');
	$main_body->pparse('events_body');
	include('includes/footer.php');
?>