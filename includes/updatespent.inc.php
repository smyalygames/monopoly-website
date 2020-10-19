<?php

require 'dbh.inc.php';

$user = mysqli_real_escape_string($conn, $_POST['id']);
$money = mysqli_real_escape_string($conn, $_POST['money']);

$data = array(
	"success" => false,
	"errors" => null
);

function OutputResult($data) {
	return json_encode($data);
}

if (empty($user) || empty($money)) {
	$data['errors'] = "Empty fields";
	echo OutputResult($data);
	exit();
}

$sql = "SELECT * FROM users WHERE user_id=?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	$data["errors"] = "SQL Error";
	echo OutputResult($data);
	exit();
} else {
	mysqli_stmt_bind_param($stmt, "i", $user);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_assoc($result);
	if (empty($row)) {
		$data['errors'] = "User not found";
		echo OutputResult($data);
		exit();
	}
	
}

//Found user, so will continue.

$sql = "SELECT * FROM money_spent WHERE user_id=?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	$data["errors"] = "SQL Error";
	echo OutputResult($data);
	exit();
} else {
	mysqli_stmt_bind_param($stmt, "i", $user);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_assoc($result);
	if (empty($row)) {
		//If the user doesn't exist then create a user...
		$sql = "INSERT INTO money_spent (user_id, user_spent) VALUES (?, 0);";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			$data["errors"] = "SQL Error";
			echo OutputResult($data);
			exit();
		} else {
			mysqli_stmt_bind_param($stmt, "i", $user);
			mysqli_stmt_execute($stmt);
		}
	}
}

//Update the table

$sql = "UPDATE money_spent SET user_spent = user_spent + ? WHERE user_id=?";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	$data["errors"] = "SQL Error";
	echo OutputResult($data);
	exit();
} else {
	mysqli_stmt_bind_param($stmt, "ii", $money, $user);
	mysqli_stmt_execute($stmt);
	$data['success'] = true;
	echo OutputResult($data);
	exit();
}