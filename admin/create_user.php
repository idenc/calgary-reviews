<?php
include('../functions.php');
if (!isAdmin()) {
    $_SESSION['msg'] = "You must be an admin to access this page";
    header('location: ../index.php');
}
/**
 * Code adapted from http://codewithawa.com/posts/admin-and-user-login-in-php-and-mysql-database
 * User: Iden
 * Date: 11/18/2018
 * Time: 9:14 PM
 */?>

<!DOCTYPE html>
<html>
<head>
	<title>Registration system PHP and MySQL - Create user</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<style>
		.header {
			background: #003366;
		}
		button[name=register_btn] {
			background: #003366;
		}
	</style>
</head>
<body>
	<div class="header">
		<h2>Admin - create user</h2>
	</div>

	<form method="post" action="create_user.php" class="register">

		<?php echo display_error(); ?>

		<div class="reg-input">
			<label>Username</label>
			<input type="text" name="username" value="<?php echo $username; ?>">
		</div>
		<div class="reg-input">
			<label>User type</label>
			<select name="user_type" id="user_type" >
				<option value=""></option>
				<option value="admin">Admin</option>
				<option value="user">User</option>
			</select>
		</div>
		<div class="reg-input">
			<label>Password</label>
			<input type="password" name="password_1">
		</div>
		<div class="reg-input">
			<label>Confirm password</label>
			<input type="password" name="password_2">
		</div>
		<div class="reg-input">
			<button type="submit" class="btn" name="register_btn"> + Create user</button>
		</div>
	</form>
</body>
</html>