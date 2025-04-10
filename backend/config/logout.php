<?php
	session_start();
	session_destroy();

	header('location: ../../client/login.php');
?>