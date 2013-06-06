<?php

	require_once("./db.php");

	$db = db_connect();

	$db_check = $db->prepare("SELECT * FROM users WHERE username = :username");

	if(!isset($_GET['name'])) die(json_encode(array("status" => "failure", "info" => "name variable not set.")));

	if (preg_match('/^[0-9A-Za-z_]+$/', $_GET['name']) == 0) {
		die(json_encode(array("status" => "failure", "info" => "Username contained invalid characters")));
	}

	$success = $db_check->execute(array('username' => $_GET['name']));

	//TODO actually check the result!
	if($db_check->rowCount() == 0){
		echo(json_encode(array("status" => "success", "info" => "")));
	} else{
		echo(json_encode(array("status" => "failure", "info" => "username_already_registered")));
	}

	db_close();
