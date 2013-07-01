<?php
	$__db;
	function db_connect(){
		global $__db;
		if(isset($__db) && $__db) return $__db;
		$__db = new PDO("mysql:host=localhost;dbname=budgeted", "budgeted", "xT3Msjh4SNWc3Jrm");
		return $__db;
	}

	function db_close(){
		global $__db;
		$__db = null;
	}

	function session_end(){
		unset($_SESSION['user']);
		session_destroy();
	}