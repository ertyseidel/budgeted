<?php

session_start();

require_once('./db.php');

$db = db_connect();

$validOrg = false;
foreach($_SESSION['user']['orgs'] as $org){
	if($org['id'] == $_GET['orgid']) $validOrg = true;
}
if(!$validOrg) die(json_encode(array('status'=>'failure', 'info'=>'Invalid org id')));

$db_expenditures = $db->prepare("SELECT * FROM budgets RIGHT JOIN expenditures ON budgets.id = expenditures.budgetid WHERE budgets.orgid = :orgid AND year = :year");

$db_expenditures->execute(array('orgid' => $_GET['orgid'], 'year' => isset($_GET['year']) ? $_GET['year'] : date('Y')));

$expenditures = $db_expenditures->fetchAll(PDO::FETCH_ASSOC);

foreach($expenditures as &$budget){
	$budget['year'] = $budget['year'] . '-' . ((int)substr($budget['year'], 2) + 1);
}

echo(json_encode(array('status' => 'success', 'info' => json_encode($expenditures))));

db_close();