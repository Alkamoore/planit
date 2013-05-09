<?php
	define('CS810', true);
	//This is the order all files must be included in each .PHP file
	include('includes/config.php');
	include('includes/functions.php');
	include('includes/settings.php');
	include('includes/sessions.php');
	include('includes/template.php');
	if(!(bool)$_SESSION['is_logged_in'])
	{
		header("Location: index.php");
	}
	$SETTINGS["PAGE_TITLE"] = "PLANit!";
	
	//Init the template (You can echo as many templates as listed in the filenames)
	$main_body = new Template($SETTINGS["TEMPLATE_DIR"]);
	$main_body->set_filenames(array(
	 'plan_body' => 'plan.htm',
	 'simple_body'=>'simple_body.htm'
	));
	$SETTINGS["CAL"] = "plan";
	

	if(isset($_POST['plan']))
	{
		$errors="";
		$event_title = addslashes($_POST['what']);
		$event_dates = explode(',', $_POST['when']);
		$event_hours = $_POST['hour'];
		$event_min = $_POST['min'];
		$event_ap = $_POST['AP'];
		$event_place = addslashes($_POST['where']);
		$event_comments = addslashes($_POST['comments']);
		$event_people = explode(";",addslashes($_POST['who']));
		$event_id = next_event_id();
		$event_names="";
		$times = "";
		
		foreach($event_people as $people)
		{
			$people=trim($people);
			if(!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/', $people))
			{
				$errors .= $people." is an incorrect email. Please remove or fix. <br/>";
			}
			else
			{
				$row = $db->users->findOne(array('email'=>strtolower($people)));
				
				$event_names .= $row == NULL ? $people.", " : $row['first_name']." ".$row['last_name'].", ";
			}
		}
		
		$count=0;
		//die(var_dump($event_ap[0]));
		foreach($event_hours as $hour)
		{
			if($hour != 0)
			{
				$ap = $event_ap[$count] == "1"? "AM" : "PM";
				$min = $event_min[$count]< 10 ? $event_min[$count]."0": $event_min[$count];
				$event_times[] =$hour.":".$min." ".$ap;
				$times .= $hour.":".$min." ".$ap.", ";
				
			}
			$count ++;
		}


		if($errors == "")
		{
			$query = array('event_id'=>$event_id, 'event_title' => $event_title, 'event_place'=>$event_place, 'event_comments'=>$event_comments, 'event_people'=>$event_people, 'finalized'=>0);
			$db->events->insert($query);

			foreach($event_dates as $dates)
			{
				$query = array('event_id' => $event_id, 'date'=> $dates, 'time'=>$event_times);
				$db->dates->insert($query);
				$count++;
			}

			$invitee = $db->users->findOne(array('user_id'=>$_SESSION['user_id']));
			$times = substr($times, 0, -2);
			$event_names = substr($event_names, 0, -2);
			$invitee = $invitee['first_name']." ".$invitee['last_name'];
			$my_text = "<html><div style='text-align:center'><h2>".$invitee." has Invited You!</h2><hr/><br/><strong>What? </strong>".$event_title."<br/><strong>Where? </strong>".$event_place."<br/> <strong>When? You Decide! Possible Days: </strong>".$_POST['when']."<br/><strong>What time? You Decide!! Possible Times: </strong>".$times."<br/><strong>Who's going? </strong>".$event_names."<br/> <strong>Comments: </strong>".$event_comments."<hr/> <br/> Log on to http://".$SETTINGS["DOMAIN"]."/login.php<br/></div></html>";
			foreach($event_people as $people)
			{
				sendMail($people, 'You\'re Invited!', $my_text);
			}
		}		
		
		$my_text= $errors != '' ? $errors:'Event was created <br/> <a href ="calendar.php">Click here to view your calendar</a>';
		$main_body->assign_vars(array(
			'MESSAGE' => $my_text
		));
		//Echo everything
		include('includes/header.php');
		$main_body->pparse('simple_body');
		include('includes/footer.php');
		

	}
	else
	{
		//Echo everything
		include('includes/header.php');
		$main_body->pparse('plan_body');
		include('includes/footer.php');
	}
?>