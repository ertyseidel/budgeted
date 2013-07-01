<?php
	session_start();

	if(!isset($_SESSION['user'])) die(json_encode(array('status' => 'failure', 'info' => 'User is not logged in.')));
	if(!isset($_POST['org']) || ((int)$_POST['org']) != $_POST['org']) die(json_encode(array('status' => 'failure', 'info' => 'No org specified to join')));

	require_once("./db.php");

	$db = db_connect();

	$db_get_org = $db->prepare("SELECT orgname FROM orgs WHERE id = :id");
	$db_get_org->execute(array('id' => $_POST['org']));

	if($db_get_org->rowCount() == 0) die(json_encode(array('status' => 'failure', 'info' => 'Orginization with that ID not found.')));

	$db_link = $db->prepare("INSERT INTO orgs_users (userid, orgid, powerlevel) VALUES (:userid, :orgid, 'admin')");

	$success = $db_link->execute(array('userid' => $_SESSION['user']['id'], 'orgid' => $_POST['org']));

	$db_initial_budgets = $db->prepare("INSERT INTO budgets (name, orgid, year, term, amount) VALUES (:name, :orgid, :year, :term, :amount)");

	$initial_budgets = array(
		array(
			'name' => 'On Campus Programming',
			'orgid' => (int)$_POST['org'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Conventions',
			'orgid' => (int)$_POST['org'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Break Trips',
			'orgid' => (int)$_POST['org'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Off Campus Programming',
			'orgid' => (int)$_POST['org'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Nonconsumables',
			'orgid' => (int)$_POST['org'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Office Supplies',
			'orgid' => (int)$_POST['org'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Other',
			'orgid' => (int)$_POST['org'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
	);

	foreach($initial_budgets as $i_b){
		$db_initial_budgets->execute($i_b);
	}

	if($success){
		$_SESSION['user']['orgs'][] = array(
			'id' => (int)$_POST['org'],
			'orgname' => $db_get_org->fetch()['orgname']
		);
		echo(json_encode(array('status' => 'success', 'info' => json_encode($_SESSION['user']))));
	} else {
		echo(json_encode(array('status' => 'failure', 'info' => 'query_error')));
	}

	db_close();
