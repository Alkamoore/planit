<?php
	define('CS810', true);
	//This is the order all files must be included in each .PHP file
	include('includes/config.php');
	include('includes/functions.php');
	include('includes/settings.php');
	include('includes/sessions.php');
	include('includes/template.php');
	
	$SETTINGS["PAGE_TITLE"] = "PLANit!";
	
	//Init the template (You can echo as many templates as listed in the filenames)
	$main_body = new Template($SETTINGS["TEMPLATE_DIR"]);
	$main_body->set_filenames(array(
	 'plan_body' => 'plan.htm'
	));
	$SETTINGS["CAL"] = "plan";
	
	//Echo everything
	include('includes/header.php');
	$main_body->pparse('plan_body');
	include('includes/footer.php');
?>