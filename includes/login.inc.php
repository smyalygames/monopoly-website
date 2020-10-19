<?php

require 'dbh.inc.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

$data = array(
	'user' => null,
	'success' => false,
	'errors' => null
);

function OutputResult($data) {
	return json_encode($data);
}

if (empty($username) || empty($password)) {
	$data['errors'] = "Empty Fields";
	echo OutputResult($data);
	exit();
} else {
	$sql = "SELECT * FROM users WHERE user_username=? OR user_email=?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		$data['errors'] = "SQL Error";
		echo OutputResult($data);
		exit();
	} else {
		mysqli_stmt_bind_param($stmt, "ss", $username, $username);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if ($row = mysqli_fetch_assoc($result)) {
			$pwdCheck = password_verify($password, $row['user_password']);
			if (!$pwdCheck) {
				$data['errors'] = "Wrong Password";
				echo OutputResult($data);
				exit();
			} else if ($pwdCheck) {
				/*
				session_start();
				$_SESSION['user_ID'] = $row['user_id'];
				$_SESSION['user_UID'] = $row['user_uid'];*/

				$data['success'] = true;
				$data['user'] = $row['user_username'];
				echo OutputResult($data);
				exit();
			} else {
				$data['errors'] = "Wrong Password";
				echo OutputResult($data);
				exit();
			}
		} else {
			$data['errors'] = "Wrong Password";
			echo OutputResult($data);
			exit();
		}
	}
}
