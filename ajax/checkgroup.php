<?php

require_once('./db.php');

$db = db_connect();

$group = $db->prepare('SELECT * FROM groups LEFT JOIN groups_users ON groups.id = groups_users.groupid WHERE groups.id = :groupid LIMIT 1');

$group->execute(array("groupid" => $_GET['groupid']));

if($group->rowCount() == 0){
	die(json_encode(array('status' => 'failure', 'info' => 'Could not find a group with that ID')));
}

$group = $group->fetchAll(PDO::FETCH_ASSOC);

if($group[0]['powerlevel']){
	die(json_encode(array('status' => 'success', 'info' => 'already_managed')));
} else {
	die(json_encode(array('status' => 'success', 'info' => 'success')));
}

db_close();