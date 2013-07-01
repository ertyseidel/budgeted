<?php
	session_start();

	if(!isset($_SESSION['user'])) die(json_encode(array('status' => 'failure', 'info' => 'User is not logged in.')));

	require_once("./db.php");

	$db = db_connect();

	$validOrg = false;

	foreach($_SESSION['user']['orgs'] as $org){
		if($_POST['orgid'] == $org['id']) $validOrg = true;
	}
	if(!$validOrg) die(json_encode(array('status'=>'failure', 'info'=>'Invalid org id')));

	$db_budget = $db->prepare('INSERT INTO budgets (name, orgid, year, amount) VALUES (\'New Budget\', :orgid, ' . date('Y') . ', 0.00)');

	$success = $db_budget->execute(array('orgid' => $_POST['orgid']));

	if($success){
		echo(json_encode(array('status' => 'success', 'info' => json_encode(array('id' => $db->lastInsertId(), 'name' => 'New Budget', 'amount' => '0.00')))));
	} else {
		echo(json_encode(array('status' => 'failure', 'info' => 'Query Failure!')));
	}

	db_close();
