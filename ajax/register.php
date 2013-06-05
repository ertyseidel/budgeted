<?php
	session_start();

	require_once("./db.php");

	if (preg_match('/^[0-9A-Za-z_]+$/', $_POST['username']) == 0) {
		die(json_encode(array("status" => "failure", "info" => "Username contained invalid characters")));
	}

	$db = db_connect();

	$db_register = $db->prepare("INSERT INTO users (username, password) VALUES(:username, :password)");

	$pass = crypt($_POST['username'] . $_POST['password']);

	$success = $db_register->execute(array('username' => $_POST['username'], 'password' => $pass));

	$_SESSION['user'] = array(
		'id' => $db->lastInsertId(),
		"username" => $_POST['username'],
		'activeGroup'=> 0
	);

	if($success){
		echo(json_encode(array("status" => "success", "info" => json_encode($_SESSION['user']))));
	} else{
		echo(json_encode(array("status" => "failure", "info" => "Could not create the account!")));
		//todo have this figure out why it failed.
	}


	db_close();
