<?php

session_start();

require_once('./db.php');

$db = db_connect();

$db_budgets = $db->prepare("SELECT * FROM budgets WHERE groupid = :groupid AND year = :year");

$db_budgets->execute(array('groupid' => $_GET['groupid'], 'year' => isset($_GET['year']) ? $_GET['year'] : date('Y')));

$budgets = $db_budgets->fetchAll(PDO::FETCH_ASSOC);

foreach($budgets as &$budget){
	$budget['year'] = $budget['year'] . '-' . ((int)substr($budget['year'], 2) + 1);
}

echo(json_encode(array('status' => 'success', 'info' => json_encode($budgets))));

db_close();