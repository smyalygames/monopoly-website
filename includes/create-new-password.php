<?php
	require "header.php";
?>

	<main>

		<?php
			$selector = $_GET["selector"];
			$validator = $_GET["validator"];

			if (empty($selector) || empty($validator)) {
				echo "<p class='error'>Could not validate your request!</p>";
			} else {
				if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) {
					?>

					<h1>Reset password</h1>
					<?php
						if (isset($_GET['error'])) {
							if ($_GET['error'] == "pwdnotsame") {
								echo "<p class='error'>Your passwords do not match!</p>";
							} else if ($_GET['error'] == "passwordtooweak") {
								include_once 'includes/pwd-strength.inc.php';
								echo printPassword($_GET['passworderror']);
							}
						}
						?>
					<form action="includes/reset-password.inc.php" method="post">
						<input type="hidden" name="selector" value="<?php echo $selector; ?>">
						<input type="hidden" name="validator" value="<?php echo $validator; ?>">
						<h3>New password:</h3>
						<input type="password" name="pwd" placeholder="Enter a new password..." required>
						<br>
						<h3>Repeat new password:</h3>
						<input type="password" name="pwd-repeat" placeholder="Repeat new password..." required>
						<br>
						<br>
						<button type="submit" name="reset-password-submit">Reset password</button>
					</form>

					<?php
				}
			}
		?>

	</main>

<?php
	require "footer.php";
?>