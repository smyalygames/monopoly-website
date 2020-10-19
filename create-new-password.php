<?php

$selector = $_GET["selector"];
$validator = $_GET["validator"];

if (empty($selector) || empty($validator)) {
	echo "<p class='error'>Could not validate your request!</p>";
	exit();
} else {
	if (ctype_xdigit($selector) == false || ctype_xdigit($validator) == false) {
		exit();
	}
}

?>

<html>
	<head>
		<title>Reset Password</title>
	</head>
	
	<body>
		<main>
			<h1>Reset Password</h1>
			<form action="includes/reset-password.inc.php" method="post">
				<input type="hidden" name="selector" value="<?php echo $selector; ?>">
				<input type="hidden" name="validator" value="<?php echo $validator; ?>">
				<h3>New password:</h3>
				<input type="password" name="password" placeholder="Password" required>
				<br>
				<h3>Repeat new password:</h3>
				<input type="password" name="password-repeat" placeholder="Repeat new password" required>
				<br>
				<br>
				<button type="submit" name="reset-password-submit">Reset password</button>
			</form>
		</main>
	</body>
</html>