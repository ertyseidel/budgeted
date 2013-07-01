<?php
	session_start();

	require_once("./db.php");

	if (preg_match('/^[0-9A-Za-z_]+$/', $_POST['username']) == 0) {
		die(json_encode(array("status" => "failure", "info" => "Username contained invalid characters")));
	}

	$db = db_connect();

	$db_login = $db->prepare("SELECT * FROM users LEFT JOIN orgs_users ON users.id = orgs_users.userid LEFT JOIN orgs ON orgs_users.orgid = orgs.id WHERE users.username = :username LIMIT 1");

	$success = $db_login->execute(array('username' => $_POST['username']));

	$raw_user = $db_login->fetchAll(PDO::FETCH_ASSOC);

	if($success && $db_login->rowCount()){
		$user = array(
			'id' => $raw_user[0]['id'],
			'username' => $raw_user[0]['username'],
			'orgs' => array(),
			'activeOrg'=> 0 //TODO
		);

		foreach($raw_user as $org){
			$g = array();
			$g['id'] = $org['id'];
			$g['orgname'] = $org['orgname'];
			$user['orgs'][] = $g;
		}

		if((crypt($_POST['username'] . $_POST['password'], $raw_user[0]['password']) == $raw_user[0]['password'])){
			$_SESSION['user'] = $user;
			echo(json_encode(array("status" => "success", "info" => json_encode($_SESSION['user']))));
		} else{
			echo(json_encode(array("status" => "failure", "info" => "login_failure")));
		}
	} else{
		echo(json_encode(array("status" => "failure", "info" => "login_failure")));
	}


	db_close();
