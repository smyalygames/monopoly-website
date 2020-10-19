<?php

require 'dbh.inc.php';

$userEmail = mysqli_real_escape_string($conn, $_POST['email']);

if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
	echo "Wrong Email";
	exit();
} else {

	$selector = bin2hex(random_bytes(8));
	$token = random_bytes(32);

	$url = "monopoly.smyalygames.com/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);

	$expires = date("U") + 900;

	$sql = "SELECT * FROM users WHERE user_email=?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		echo "There was an error";
		exit();
	} else {
		mysqli_stmt_bind_param($stmt, "s", $userEmail);
		mysqli_stmt_execute($stmt);
		$verifyEmail = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
	}

	if ($verifyEmail['user_email'] == $userEmail) {

		$sql = "DELETE FROM password WHERE user_id=?;";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			echo "There was an error";
			exit();
		} else {
			mysqli_stmt_bind_param($stmt, "s", $verifyEmail['user_id']);
			mysqli_stmt_execute($stmt);
		}

		$sql = "INSERT INTO password (user_id, pwd_selector, pwd_token, pwd_expires) VALUES (?, ?, ?, ?);";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			echo "There was an error";
			exit();
		} else {
			$hashedToken = password_hash($token, PASSWORD_ARGON2ID);
			mysqli_stmt_bind_param($stmt, "isss", $verifyEmail['user_id'], $selector, $hashedToken, $expires);
			mysqli_stmt_execute($stmt);
		}

		mysqli_stmt_close($stmt);
		mysqli_close($conn);


		$to = $verifyEmail['user_email'];

		$subject = 'Reset your password for Monopoly.';

		$message = '<p>We received a password reset request. The link to reset your password has been sent below. If you did not make this request, you can ignore this email.</p>';
		$message .= '<p>Here is your password reset link: </br>';
		$message .= '<a href="' . $url . '">' . $url . '</a></p>';

		$headers = "From: smyalygames <no-reply@smyalygames.com>\r\n";
		$headers .= "Reply-To: no-reply@smyalygames.com\r\n";
		$headers .= "Content-type: text/html\r\n";

		mail($to, $subject, $message, $headers);
		echo 'Success';
		exit();
	}

}

echo 'Error';
exit();