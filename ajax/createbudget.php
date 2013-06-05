<?php
	session_start();

	if(!isset($_SESSION['user'])) die(json_encode(array('status' => 'failure', 'info' => 'User is not logged in.')));

	require_once("./db.php");

	$db = db_connect();

	$validGroup = false;

	foreach($_SESSION['user']['groups'] as $group){
		if($_POST['groupid'] == $group['id']) $validGroup = true;
	}
	if(!$validGroup) die(json_encode(array('status'=>'failure', 'info'=>'Invalid group id')));

	$db_budget = $db->prepare('INSERT INTO budgets (name, groupid, year, amount) VALUES (\'New Budget\', :groupid, ' . date('Y') . ', 0.00)');

	$success = $db_budget->execute(array('groupid' => $_POST['groupid']));

	if($success){
		echo(json_encode(array('status' => 'success', 'info' => json_encode(array('id' => $db->lastInsertId(), 'name' => 'New Budget', 'amount' => '0.00')))));
	} else {
		echo(json_encode(array('status' => 'failure', 'info' => 'Query Failure!')));
	}

	db_close();
