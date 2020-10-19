<?php

if (!isset($_POST["reset-password-submit"])) {
	header("Location: ../index.php");
	exit();
}
	
require 'dbh.inc.php';

$selector = mysqli_real_escape_string($conn, $_POST["selector"]);
$validator = mysqli_real_escape_string($conn, $_POST["validator"]);
$password = mysqli_real_escape_string($conn, $_POST["password"]);
$passwordRepeat = mysqli_real_escape_string($conn, $_POST["password-repeat"]);

if (empty($selector) || empty($validator)) {
	header("Location: ../signup.php");
	exit();
} else if ($password != $passwordRepeat) {
	header("Location: ../create-new-password.php?error=pwdnotsame&selector=" . $selector . "&validator=" . $validator);
	exit();
}

$currentDate = date("U");

$sql = "SELECT * FROM password WHERE pwd_selector=? AND pwd_expires>=?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
	echo "There was an error!";
	header("Location: ../create-new-password.php?error=sql&" . $selector . "&" . $validator);
	exit();
} else {
	mysqli_stmt_bind_param($stmt, "ss", $selector, $currentDate);
	mysqli_stmt_execute($stmt);

	$result = mysqli_stmt_get_result($stmt);
	if (!$row = mysqli_fetch_assoc($result)) {
		echo "You need to re-submit your reset request.";
		exit();
	} else {

		$tokenBin = hex2bin($validator);
		$tokenCheck = password_verify($tokenBin, $row["pwd_token"]);

		if ($tokenCheck === false) {
			echo "You need to re-submit your reset request.";
			exit();
		} else if ($tokenCheck === true) {
			
			$tokenID = $row["user_id"];

			$sql = "SELECT * FROM users WHERE user_id=?;";
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)) {
				echo "There was an error!";
				header("Location: ../create-new-password.php?error=sql1&" . $selector . "&" . $validator);
				exit();
			} else {
				mysqli_stmt_bind_param($stmt, "i", $tokenID);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if (!$row = mysqli_fetch_assoc($result)) {
					echo "There was an error!";
					header("Location: ../create-new-password.php?error=sql2&" . $selector . "&" . $validator);
					exit();
				} else {

					$sql = "UPDATE users SET user_password=? WHERE user_id=?;";
					$stmt = mysqli_stmt_init($conn);
					if (!mysqli_stmt_prepare($stmt, $sql)) {
						echo "There was an error!";
						header("Location: ../create-new-password.php?error=sql3&" . $selector . "&" . $validator);
						exit();
					} else {
						$newPwdHash = password_hash($password, PASSWORD_ARGON2ID);
						mysqli_stmt_bind_param($stmt, "si", $newPwdHash, $tokenID);
						mysqli_stmt_execute($stmt);

						$sql =  "DELETE FROM password WHERE user_id=?;";
						$stmt = mysqli_stmt_init($conn);
						if (!mysqli_stmt_prepare($stmt, $sql)) {
							echo "There was an error!";
							header("Location: ../create-new-password.php?error=sql4&" . $selector . "&" . $validator);
							exit();
						} else {
							mysqli_stmt_bind_param($stmt, "i", $tokenID);
							mysqli_stmt_execute($stmt);
							header("Location: ../index.php?newpwd=passwordupdated");
						}

					}

				}

			}

		}

	}
}