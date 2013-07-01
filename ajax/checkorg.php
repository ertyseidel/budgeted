<?php
	session_start();

	if(!isset($_GET['org']) || ((int)$_GET['org']) != $_GET['org']) die(json_encode(array('status' => 'failure', 'info' => 'no_org_specified')));

	require_once("./db.php");

	$db = db_connect();

	$db_get_org = $db->prepare("SELECT id FROM orgs WHERE orgname LIKE TRIM(:orgname)");
	$db_get_org->execute(array('orgname' => $_GET['org']));

	if($db_get_org->rowCount() == 0) echo(json_encode(array('status' => 'success', 'info' => 'success')));
	else echo(json_encode(array('status' => 'failure', 'info' => 'bad_org_name')));

