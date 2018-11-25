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
    if (isset($_SESSION[$message])) {
        echo $_SESSION[$message];
        unset($_SESSION[$message]);
    }

}

function isLoggedIn()
{
    if (isset($_SESSION['user'])) {
        return true;
    } else {
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
function login()
{
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
                $_SESSION['success'] = "You are now logged in";
                header('location: index.php');
            } else {
                $_SESSION['user'] = $logged_in_user;
                $_SESSION['success'] = "You are now logged in";

                header('location: index.php');
            }
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}

function isAdmin()
{
    if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] == 'admin') {
        return true;
    } else {
        return false;
    }
}

/*
 * =========================================================
 * RESTAURANT FUNCTIONS
 * =========================================================
 */

function generatePhotos($rid)
{
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

function generateTicks($rid)
{
    global $db;

    $query = "SELECT wifi, delivery, alcohol
              FROM restaurant
              WHERE r_id = $rid";
    $query = mysqli_query($db, $query);
    $temp = mysqli_fetch_array($query);
    foreach ($temp as $cname => $cvalue) {
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

function get_num_reviews($r_id)
{
    global $db;

    $query = "SELECT COUNT(*)
              FROM review
              WHERE r_id = $r_id";
    $query = mysqli_query($db, $query);
    $query = mysqli_fetch_array($query);
    return $query[0];
}

function generate_avg_cost($r_id)
{
    global $db;

    $query = "SELECT AVG(cost)
              FROM review
              WHERE r_id = $r_id";
    $query = mysqli_query($db, $query);
    $query = mysqli_fetch_array($query);
    $cost = ceil($query[0]);
    generate_cost($cost);

}

function generate_cost($cost)
{
    $rem = 3 - $cost;
    echo "<p>";
    echo "<span>";
    for ($i = 0; $i < $cost; $i++) {
        echo "$";
    }
    echo "</span>";
    for ($i = 0; $i < $rem; $i++) {
        echo "$";
    }
    echo "</p>";
}

function get_reviews($r_id)
{
    global $db;

    $query = "SELECT *
              FROM review
              WHERE r_id = $r_id";
    $query = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($query)) {
        $user_id = $temp['user_id'];
        $cost = $temp['cost'];
        $date_posted = date_format(date_create($temp['date_posted']), "F j, Y");
        $rating = $temp['rating'];
        $content = $temp['content'];

        $query2 = "SELECT COUNT(*)
                   FROM review
                   WHERE user_id = '$user_id'";
        $num_reviews = mysqli_query($db, $query2);
        $num_reviews = mysqli_fetch_array($num_reviews);
        $num_reviews = $num_reviews[0];
        echo <<< EOT
                    <hr>
                    <div class="customer-review_wrap">
                        <div class="customer-img">
                            <p>$user_id</p>
                            <span>$num_reviews Reviews</span>
                        </div>
                        <div class="customer-content-wrap">
                            <div class="customer-content">
                                <div class="customer-review">
EOT;
        generate_cost($cost);
        echo <<< EOT
                                    <p>Reviewed $date_posted</p>
                                </div>
                                <div class="customer-rating">$rating / 5</div>
                            </div>
                            <p class="customer-text">$content</p>          
                        </div>
                    </div>
                    <hr>
EOT;

    }
}

function get_info($r_id)
{
    global $db;

    $query = "SELECT *
              FROM restaurant
              WHERE r_id = $r_id";
    $query = mysqli_query($db, $query);
    $query = mysqli_fetch_array($query);
    return $query;
}

function generate_avg_review($r_id)
{
    global $db;

    $query = "SELECT AVG(rating)
              FROM review
              WHERE r_id = $r_id";
    $query = mysqli_query($db, $query);
    $query = mysqli_fetch_array($query);
    $rating = round($query[0], 1);
    return $rating - 1;
}

function generate_hours($r_id)
{
    global $db;

    $query = "SELECT open_time, close_time
              FROM business_hours
              WHERE r_id = $r_id
              ORDER BY day_of_week ASC";
    $query = mysqli_query($db, $query);

    $i = -1;
    while ($temp = mysqli_fetch_assoc($query)) {
        $open_time = date('H:i', mktime($temp['open_time']));
        $close_time = $temp['close_time'];
        echo "<p>";
        echo date('l', mktime(0, 0, 0, 0, $i, 0)) . " " . $open_time . "AM" . "-" . $close_time . "PM";
        echo "</p>";
        $i++;
    }
}

function is_open($r_id) {
    global $db;

    $day_of_week = date('N', strtotime('Monday'));
    $time = date('H:i:s');
    $query = "SELECT COUNT(*)
              FROM business_hours
              WHERE day_of_week = $day_of_week AND $time > open_time AND $time < close_time";
    $query = mysqli_query($db, $query);

    if (mysqli_num_rows($query) == 1) {
        return true;
    } else {
        return false;
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

function submit_review()
{
    global $db, $errors;
    $r_id = $_GET['r_id'];
    $review_content = e($_POST['review_content']);
    $review_rating = e($_POST['review_rating']);
    $review_cost = e($_POST['review_cost']);

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
            header('location: detail.php?r_id=' . $r_id);
        } else {
            echo $query;
            array_push($errors, "Error occurred");
        }
    }
}

/*
 * =========================================================
 * LISTING FUNCTIONS
 * =========================================================
 */

function generate_restaurants()
{
    global $db;

    $query = "SELECT *
              FROM restaurant";
    $query = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($query)) {
        $r_id = $temp['r_id'];
        $name = $temp['name'];
        $directory = "images/restaurants/$r_id/";
        $files = scandir($directory);
        $firstFile = $directory . $files[2];// because [0] = "." [1] = ".."
        $avg_review = generate_avg_review($r_id);
        $num_reviews = get_num_reviews($r_id);
        $location = $temp['location'];
        $phone_num = $temp['phone_num'];
        $website = $temp['website'];
        echo <<< EOT
                    <div class="col-sm-6 col-lg-12 col-xl-6 featured-responsive">
                        <div class="featured-place-wrap">
                            <a href="detail.php?r_id=$r_id">
                                <img src="$firstFile" class="img-fluid" alt="#">
                                <span class="featured-rating-orange ">$avg_review</span>
                                <div class="featured-title-box">
                                    <h6>$name</h6>
                                    <p>Restaurant </p> <span>• </span>
                                    <p>$num_reviews Reviews</p> <span> • </span>
EOT;
                                    generate_avg_cost($r_id);
        echo <<< EOT
                                    <ul>
                                        <li><span class="icon-location-pin"></span>
                                            <p>$location</p>
                                        </li>
                                        <li><span class="icon-screen-smartphone"></span>
                                            <p>$phone_num</p>
                                        </li>
                                        <li><span class="icon-link"></span>
                                            <a style="color: #9fa9b9" href="$website">Visit their website!</a>
                                        </li>

                                    </ul>
                                    <div class="bottom-icons">
                               
                                        <span class="ti-heart"></span>
                                        <span class="ti-bookmark"></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
EOT;

    }
}