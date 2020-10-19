<?php

require 'dbh.inc.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$passwordRepeat = mysqli_real_escape_string($conn, $_POST['password-repeat']);


if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
	echo "error 1";
	exit();
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {
	echo "error 2";
	exit();
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo "error 3";
	exit();
} else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
	echo "error 4";
	exit();
} else if ($password !== $passwordRepeat) {
	echo "error 5";
	exit();
} else {

	$sql = "SELECT user_username FROM users WHERE user_username=?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		echo "SQL error 1";
		exit();
	} else {
		mysqli_stmt_bind_param($stmt, "s", $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		$resultCheck = mysqli_stmt_num_rows($stmt);
		if ($resultCheck >> 0) {
			echo "error 6";
			exit();
		} else {
			$correct = 1;
		}

	$sql = "SELECT user_username FROM users WHERE user_email=?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		echo "SQL error 2";
		exit();
	} else {
		mysqli_stmt_bind_param($stmt, "s", $email);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		$resultCheck = mysqli_stmt_num_rows($stmt);
		if ($resultCheck >> 0) {
			echo "error 7";
			exit();
		} else {
			$correct = $correct+1;
		}
		
		if ($correct == 2) {

			$sql = "INSERT INTO users (user_username, user_email, user_password) VALUES (?, ?, ?);";
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)) {
				echo "SQL error 3";
				exit();
			} else {
				$hashedPwd = password_hash($password, PASSWORD_ARGON2ID);

				mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPwd);
				mysqli_stmt_execute($stmt);
				echo "User has registered!";
			}
			}
		}
	}
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit();
