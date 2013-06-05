<?php

session_start();

require_once('./db.php');

$db = db_connect();

$validGroup = false;
foreach($_SESSION['user']['groups'] as $group){
	if($group['id'] == $_GET['groupid']) $validGroup = true;
}
if(!$validGroup) die(json_encode(array('status'=>'failure', 'info'=>'Invalid group id')));

$db_expenditures = $db->prepare("SELECT * FROM budgets RIGHT JOIN expenditures ON budgets.id = expenditures.budgetid WHERE budgets.groupid = :groupid AND year = :year");

$db_expenditures->execute(array('groupid' => $_GET['groupid'], 'year' => isset($_GET['year']) ? $_GET['year'] : date('Y')));

$expenditures = $db_expenditures->fetchAll(PDO::FETCH_ASSOC);

foreach($expenditures as &$budget){
	$budget['year'] = $budget['year'] . '-' . ((int)substr($budget['year'], 2) + 1);
}

echo(json_encode(array('status' => 'success', 'info' => json_encode($expenditures))));

db_close();