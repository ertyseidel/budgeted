<?php

require_once('./db.php');

$db = db_connect();

$groups = $db->prepare("SELECT id as data, groupname as value FROM groups WHERE groupname LIKE :start");

$groups->execute(array('start' => '%' . $_GET['query'] . '%'));

$groups = $groups->fetchAll(PDO::FETCH_ASSOC);

echo('{"suggestions": ' . json_encode($groups) . '}');

db_close();