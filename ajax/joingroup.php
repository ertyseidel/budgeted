<?php
	session_start();

	if(!isset($_SESSION['user'])) die(json_encode(array('status' => 'failure', 'info' => 'User is not logged in.')));
	if(!isset($_POST['group']) || ((int)$_POST['group']) != $_POST['group']) die(json_encode(array('status' => 'failure', 'info' => 'No group specified to join')));

	require_once("./db.php");

	$db = db_connect();

	$db_get_group = $db->prepare("SELECT groupname FROM groups WHERE id = :id");
	$db_get_group->execute(array('id' => $_POST['group']));

	if($db_get_group->rowCount() == 0) die(json_encode(array('status' => 'failure', 'info' => 'Group with that ID not found.')));

	$db_link = $db->prepare("INSERT INTO groups_users (userid, groupid, powerlevel) VALUES (:userid, :groupid, 'admin')");

	$success = $db_link->execute(array('userid' => $_SESSION['user']['id'], 'groupid' => $_POST['group']));

	$db_initial_budgets = $db->prepare("INSERT INTO budgets (name, groupid, year, term, amount) VALUES (:name, :groupid, :year, :term, :amount)");

	$initial_budgets = array(
		array(
			'name' => 'On Campus Programming',
			'groupid' => (int)$_POST['group'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Conventions',
			'groupid' => (int)$_POST['group'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Break Trips',
			'groupid' => (int)$_POST['group'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Off Campus Programming',
			'groupid' => (int)$_POST['group'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Nonconsumables',
			'groupid' => (int)$_POST['group'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Office Supplies',
			'groupid' => (int)$_POST['group'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
		array(
			'name' => 'Other',
			'groupid' => (int)$_POST['group'],
			'year' => date('Y'),
			'term' => 'a',
			'amount' => 0
		),
	);

	foreach($initial_budgets as $i_b){
		$db_initial_budgets->execute($i_b);
	}

	if($success){
		$_SESSION['user']['groups'][] = array(
			'id' => (int)$_POST['group'],
			'groupname' => $db_get_group->fetch()['groupname']
		);
		echo(json_encode(array('status' => 'success', 'info' => json_encode($_SESSION['user']))));
	} else {
		echo(json_encode(array('status' => 'failure', 'info' => 'query_error')));
	}

	db_close();
