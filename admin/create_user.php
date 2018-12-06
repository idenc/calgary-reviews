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
 */ ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Colorlib">
    <meta name="description" content="#">
    <meta name="keywords" content="#">
    <!-- Favicons -->
    <link rel="shortcut icon" href="#">
    <!-- Page Title -->
    <title>Browse</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500,700,900" rel="stylesheet">
    <!-- Simple line Icon -->
    <link rel="stylesheet" href="../css/simple-line-icons.css">
    <!-- Themify Icon -->
    <link rel="stylesheet" href="../css/themify-icons.css">
    <!-- Hover Effects -->
    <link rel="stylesheet" href="../css/set1.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<!--============================= HEADER =============================-->
<div class="dark-bg sticky-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="../index.php">Calgary Reviews</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-menu"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                        <ul class="navbar-nav">
                            <li class="nav-item active">
                                <a class="nav-link" href="../listing.php">Browse</a>
                            </li>
                            <?php if (isset($_SESSION['user'])) : ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false">
                                        <?php echo $_SESSION['user']['username']; ?>
                                        <small>
                                            <i style="color: #888;">(<?php echo ucfirst($_SESSION['user']['user_type']); ?>
                                                )</i>
                                        </small>
                                        <span class="icon-arrow-down"></span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <a class="dropdown-item"
                                           href="../showuser.php?username=<?php echo $_SESSION['user']['username'] ?>">Profile</a>
                                        <a class="dropdown-item"
                                           href="../lists.php?username=<?php echo $_SESSION['user']['username'] ?>">Lists</a>
                                        <a class="dropdown-item"
                                           href="../viewuserphotos.php?username=<?php echo $_SESSION['user']['username'] ?>">Photos</a>
                                        <a class="dropdown-item" href="../vieworder.php">Orders</a>
                                    </div>
                                </li>
                                <?php if (isAdmin()) : ?>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="../admin/create_user.php" style="color: red;">Create
                                            User</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="../admin/viewpending.php" style="color: red;">View
                                            Pending</a>
                                    </li>
                                <?php endif ?>
                                <li class="nav-item active">
                                    <a class="nav-link" href="../index.php?logout='1'" style="color: red;">Logout</a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item active">
                                    <a class="nav-link" href="../register.php">Register</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="../login.php">Login</a>
                                </li>
                            <?php endif ?>
                            <li><a href="../addlisting.php" class="btn btn-outline-light top-btn"><span
                                            class="ti-plus"></span> Add
                                    Listing</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>
<!--//END HEADER -->
<div class="header">
    <h2>Admin - create user</h2>
</div>

<form method="post" action="create_user.php" class="register">

    <?php echo display_error(); ?>

    <div class="reg-input">
        <form method="post" action="">
            <label for="user_type">User type</label>
            <select name="user_type" id="user_type" onchange="this.form.submit()">
                <option value=""></option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
                <option value="employee" <?= isset($_POST['user_type']) &&
                $_POST['user_type'] == 'employee' ? 'selected' : ""; ?>>Employee
                </option>
            </select>
        </form>
        <?php if (isset($_POST['user_type']) && $_POST['user_type'] == 'employee') : ?>
            <label for="ssn">SSN</label>
            <input type="text" name="ssn" required>
            <label for="fname">First name</label>
            <input type="text" name="fname" required>
            <label for="lname">Last name</label>
            <input type="text" name="lname" required>
            <label for="email">Email</label>
            <input type="email" name="email" required>
            <label for="phone">Phone</label>
            <input type="tel" name="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
            <span style="color: white">Format: 123-456-7890</span>
            <div class="reg-input">
                <button type="submit" class="btn" name="employee_btn" style="color: white; background: darkblue;"> +
                    Create Employee
                </button>
            </div>
        <?php else : ?>
            <label>Username
                <input type="text" name="username" value="<?php echo $username; ?>">
            </label>
            <label>Password
                <input type="password" name="password_1">
            </label>
            <label>Confirm password
                <input type="password" name="password_2">
            </label>
            <div class="reg-input">
                <button type="submit" class="btn" name="register_btn" style="color: white; background: darkblue"> +
                    Create user
                </button>
            </div>
        <?php endif ?>
    </div>
</form>
</body>
</html>