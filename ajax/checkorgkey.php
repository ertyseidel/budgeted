<?php
	session_start();

	if(!isset($_GET['key']) || ((int)$_GET['key']) != $_GET['key']) die(json_encode(array('status' => 'failure', 'info' => 'no_key_specified')));

	require_once("./db.php");

	$db = db_connect();

	$db_get_org = $db->prepare("SELECT orgid FROM budgeted.keys WHERE budgeted.keys.key = :key"); //why do I need to specify the database here??????

	$db_get_org->execute(array('key' => $_GET['key']));

	$org = $db_get_org->fetch();

	if($org) echo(json_encode(array('status' => 'success', 'info' => $org['orgid'])));
	else echo(json_encode(array('status' => 'failure', 'info' => 'bad_org_key')));