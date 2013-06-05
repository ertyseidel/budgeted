<?php
	session_start();

	if(!isset($_SESSION['user'])) die(json_encode(array('status' => 'failure', 'info' => 'User is not logged in.')));

	require_once("./db.php");

	$db = db_connect();

	$db_groupid = $db->prepare('SELECT groupid FROM budgets WHERE id = :budgetid');
	$db_groupid->execute(array('budgetid' => $_POST['budgetid']));
	$groupid = $db_groupid->fetch(PDO::FETCH_ASSOC)['groupid'];

	$validGroup = false;
	foreach($_SESSION['user']['groups'] as $group){
		if($groupid == $group['id']) $validGroup = true;
	}
	if(!$validGroup) die(json_encode(array('status'=>'failure', 'info'=>'Invalid group id')));
	if(!isset($_POST['budgetname'])) die(json_encode(array('status'=> 'failure', 'info'=>'No budget name set.')));
	if(!isset($_POST['budgetamount'])) die(json_encode(array('status'=> 'failure', 'info'=>'No budget amount set.')));
	if(!is_numeric($_POST['budgetamount'])) die(json_encode(array('status'=> 'failure', 'info'=> 'Non-numeric budget amount')));

	$db_budget = $db->prepare('UPDATE budgets SET name = :budgetname, amount = :budgetamount WHERE id = :budgetid');

	$success = $db_budget->execute(array('budgetname' => $_POST['budgetname'], 'budgetamount' => $_POST['budgetamount'], 'budgetid' => $_POST['budgetid']));

	if($success){
		echo(json_encode(array('status' => 'success', 'info' => 'success')));
	} else {
		echo(json_encode(array('status' => 'failure', 'info' => 'Query Failure!')));
	}

	db_close();
