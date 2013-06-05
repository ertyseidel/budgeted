<?php

session_start();

if(isset($_SESSION['user'])){
	echo(json_encode(array('status' => 'success', 'info' => json_encode($_SESSION['user']))));
} else {
	echo(json_encode(array('status' => 'failure', 'info' => 'not_logged_in')));
}