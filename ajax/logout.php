<?php
	
	session_start();
	unset($_SESSION['user']);
	session_destroy();

	echo(json_encode(array('status' => 'success', 'info' => 'success')));

