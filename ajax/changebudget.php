<?php
	session_start();

	if(!isset($_SESSION['user'])) die(json_encode(array('status' => 'failure', 'info' => 'User is not logged in.')));

	require_once("./db.php");

	$db = db_connect();

	$db_orgid = $db->prepare('SELECT orgid FROM budgets WHERE id = :budgetid');
	$db_orgid->execute(array('budgetid' => $_POST['budgetid']));
	$orgid = $db_orgid->fetch(PDO::FETCH_ASSOC)['orgid'];

	$validOrg = false;
	foreach($_SESSION['user']['orgs'] as $org){
		if($orgid == $org['id']) $validOrg = true;
	}
	if(!$validOrg) die(json_encode(array('status'=>'failure', 'info'=>'Invalid org id')));
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
