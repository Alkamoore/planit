<?php
	define('CS810', true);
	//This is the order all files must be included in each .PHP file
	include('includes/config.php');
	include('includes/functions.php');
	include('includes/settings.php');
	include('includes/sessions.php');
	include('includes/template.php');
	
	$SETTINGS["PAGE_TITLE"] = "Index Page";
	
	//Init the template (You can echo as many templates as listed in the filenames)
	$main_body = new Template($SETTINGS["TEMPLATE_DIR"]);
	$main_body->set_filenames(array(
	 'index_body' => 'index.htm'
	));
	//Echo everything
	include('includes/header.php');
	$main_body->pparse('index_body');
	include('includes/footer.php');
?>