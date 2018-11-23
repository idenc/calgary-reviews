<?php
/**
 * Sections of code adapted from http://codewithawa.com/posts/admin-and-user-login-in-php-and-mysql-database
 * User: Iden
 */

session_start();

// connect to database
$db = mysqli_connect('localhost', 'root', '', 'calgary_reviews');

// variable declaration
$username = "";
$fname = "";
$lname = "";
$errors = array();

/*
 * =========================================================
 * USER REGISTRATION AND LOGIN FUNCTIONS
 * =========================================================
 */
// call the register() function if register_btn is clicked
if (isset($_POST['register_btn'])) {
    register();
}

// REGISTER USER
function register()
{
    // call these variables with the global keyword to make them available in function
    global $db, $errors, $username, $fname, $lname;

    // receive all input values from the form. Call the e() function
    // defined below to escape form values
    $username = e($_POST['username']);
    if (isset($_POST['fname']) && isset($_POST['lname'])) {
        $fname = e($_POST['fname']);
        $lname = e($_POST['lname']);
    }
    $password_1 = e($_POST['password_1']);
    $password_2 = e($_POST['password_2']);

    // form validation: ensure that the form is correctly filled
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password_1)) {
        array_push($errors, "Password is required");
    }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }

    // register user if there are no errors in the form
    if (count($errors) == 0) {
        $password = md5($password_1);//encrypt the password before saving in the database

        if (isset($_POST['user_type'])) {
            $user_type = e($_POST['user_type']);
            $query = "INSERT INTO user (username, user_type, password, fname, lname) 
					  VALUES('$username', '$user_type', '$password', '$fname', '$lname')";
            $query = mysqli_query($db, $query);
            if ($query) {
                $_SESSION['admin_success'] = "New user successfully created!!";
                header('location: ../index.php');
            } else {
                array_push($errors, "Username already exists");
            }
        } else {
            $query = "INSERT INTO user (username, user_type, password, fname, lname) 
					  VALUES('$username', 'user', '$password', '$fname', '$lname')";
            $query = mysqli_query($db, $query);

            if ($query) {
                // get id of the created user
                $logged_in_user_id = mysqli_insert_id($db);

                $_SESSION['user'] = getUserById($logged_in_user_id); // put logged in user in session
                $_SESSION['success'] = "You are now logged in";
                header('location: index.php');
            } else {
                array_push($errors, "Username already exists");
            }

        }
    }
}

// return user array from their id
function getUserById($id)
{
    global $db;
    $query = "SELECT * FROM user WHERE username=" . $id;
    $result = mysqli_query($db, $query);

    $user = mysqli_fetch_assoc($result);
    return $user;
}

// escape string
function e($val)
{
    global $db;
    return mysqli_real_escape_string($db, trim($val));
}

function display_error()
{
    global $errors;

    if (count($errors) > 0) {
        echo '<div class="error">';
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
        echo '</div>';
    }
}

function display_message($message)
{
    if(isset($_SESSION[$message])) {
        echo $_SESSION[$message];
        unset($_SESSION[$message]);
    }

}

function isLoggedIn()
{
    if (isset($_SESSION['user'])) {
        return true;
    }else{
        return false;
    }
}

// log user out if logout button clicked
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['user']);
    header("location: index.php");
}

// call the login() function if register_btn is clicked
if (isset($_POST['login_btn'])) {
    login();
}

// LOGIN USER
function login(){
    global $db, $username, $errors;

    // grab form values
    $username = e($_POST['username']);
    $password = e($_POST['password']);

    // make sure form is filled properly
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    // attempt login if no errors on form
    if (count($errors) == 0) {
        $password = md5($password);

        $query = "SELECT * FROM user WHERE username='$username' AND password='$password' LIMIT 1";
        $results = mysqli_query($db, $query);

        if (mysqli_num_rows($results) == 1) { // user found
            // check if user is admin or user
            $logged_in_user = mysqli_fetch_assoc($results);
            if ($logged_in_user['user_type'] == 'admin') {

                $_SESSION['user'] = $logged_in_user;
                $_SESSION['success']  = "You are now logged in";
                header('location: index.php');
            }else{
                $_SESSION['user'] = $logged_in_user;
                $_SESSION['success']  = "You are now logged in";

                header('location: index.php');
            }
        }else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}

function isAdmin()
{
    if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] == 'admin' ) {
        return true;
    }else{
        return false;
    }
}

/*
 * =========================================================
 * RESTAURANT FUNCTIONS
 * =========================================================
 */

function generatePhotos($rid) {
    global $db;

    $query = "SELECT p.file_path
              FROM photo AS p, uploads AS u
              WHERE p.photo_id = u.photoid AND u.r_id = $rid";
    $query = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($query)) {
        echo "<div class='swiper-slide'>";
        echo "<a href=$temp[0] class='grid image-link'>";
        echo "<img src=$temp[0] class='img-fluid' alt='#'>";
        echo "</a>";
        echo "</div>";
    }
}

function generateTitle($rid) {
    global $db;

    $query = "SELECT name
              FROM restaurant
              WHERE r_id = $rid";
    $query = mysqli_query($db, $query);
    $temp = mysqli_fetch_array($query);
    echo "<h5>$temp[0]</h5>";
}

function generateTicks($rid) {
    global $db;

    $query = "SELECT wifi, delivery, alcohol
              FROM restaurant
              WHERE r_id = $rid";
    $query = mysqli_query($db, $query);
    $temp = mysqli_fetch_array($query);
    foreach($temp as $cname => $cvalue) {
        if ($cvalue == 1 and ($cname == 'wifi' or $cname == 'delivery' or $cname == 'alcohol')) {
            echo "<div class='col-md-4'>";
            echo "<label class='custom-checkbox'>";
            echo "<span class='ti-check-box'></span>";
            echo "<span class='custom-control-description'>$cname</span>";
            echo "</label>";
            echo "</div>";
        }
    }
}

/*
 * =========================================================
 * REVIEW FUNCTIONS
 * =========================================================
 */

// call the login() function if register_btn is clicked
if (isset($_POST['review_btn'])) {
    submit_review();
}

function submit_review() {
    global $db, $errors;
    $r_id = $_GET['r_id'];
    $review_content = e($_POST['review_content']);
    $review_rating  = e($_POST['review_rating']);
    $review_cost    = e($_POST['review_cost']);

    // form validation: ensure that the form is correctly filled
    if (empty($review_content)) {
        array_push($errors, "Review is empty");
    }

    if (empty($review_rating)) {
        array_push($errors, "Rating is empty");
    }

    if (empty($review_cost)) {
        array_push($errors, "Cost Rating is empty");
    }

    if (empty($r_id)) {
        array_push($errors, "Error on restaurant");
    }

    $logged_in_user_id = $_SESSION['user']['username'];

    if (count($errors) == 0) {
        $query = "INSERT INTO review (content, rating, r_id, user_id, cost) 
				  VALUES('$review_content', $review_rating, $r_id, '$logged_in_user_id',
				   $review_cost)";
        $query = mysqli_query($db, $query);

        if ($query) {
            header('location: detail.php?r_id='.$r_id);
        } else {
            echo $query;
            array_push($errors, "Error occurred");
        }
    }
}