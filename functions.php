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

function generatePhotos($rid, $isEdit)
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
        if ($isEdit) {
            echo "<button type='button'>Delete</button>";
        }
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
    $temp = mysqli_fetch_array($query, MYSQLI_ASSOC);
    foreach ($temp as $cname => $cvalue) {
        if (($cname == 'wifi' or $cname == 'delivery' or $cname == 'alcohol') and $cvalue == 1) {
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

function review_button($r_id)
{
    if (isset($_SESSION['user'])) {
        echo "href='review.php?r_id=$r_id'";
    } else {
        echo "href = 'login.php'";
    }
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
        $open_time = date("g:ia", strtotime($temp['open_time']));
        $close_time = date("g:ia", strtotime($temp['close_time']));
        echo "<p>";
        echo date('l', mktime(0, 0, 0, 0, $i, 0)) . " " . $open_time . "-" . $close_time;
        echo "<br>";
        $i++;
    }
    if (is_open($r_id)) {
        echo "<span class=\"open-now\">OPEN NOW</span>";
    } else {
        echo "<span class=\"closed-now\">CLOSED NOW</span>";
    }
    echo "</p>";
}

function is_open($r_id)
{
    global $db;
    date_default_timezone_set('America/Phoenix');
    $day_of_week = date('N');
    $time = date('H:i:s');
    $query = "SELECT COUNT(*)
              FROM business_hours
              WHERE day_of_week = $day_of_week AND
              '$time' > open_time AND '$time' < close_time
               AND r_id = $r_id";

    $query = mysqli_query($db, $query) or die(mysqli_error($db));

    if ($query && mysqli_fetch_array($query)[0] == 1) {
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
				  VALUES('$review_content', $review_rating + 1, $r_id, '$logged_in_user_id',
				   $review_cost)";
        $query = mysqli_query($db, $query);

        if ($query) {
            header('location: detail.php?r_id=' . $r_id);
        } else {
            array_push($errors, "Error occurred");
        }
    }
}

/*
 * =========================================================
 * LISTING FUNCTIONS
 * =========================================================
 */

function generate_restaurants($find_pending)
{
    global $db;
    $pendingpath = "";

    if ($find_pending) {
        $query = "SELECT *
                  FROM restaurant
                  WHERE pending = 0x1";
        $pendingpath = "../";
    } else {
        $query = "SELECT *
                  FROM restaurant
                  WHERE pending = '0'";
    }

    $query = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($query)) {
        $r_id = $temp['r_id'];
        $name = $temp['name'];
        $directory = $pendingpath . "images/restaurants/$r_id/";
        $files = scandir($directory);
        $firstFile = $directory . $files[2];// because [0] = "." [1] = ".."
        $avg_review = generate_avg_review($r_id);
        $num_reviews = get_num_reviews($r_id);
        $location = $temp['location'];
        $phone_num = $temp['phone_num'];
        $website = $temp['website'];
        echo <<< EOT
                    <div class="col-sm-6 col-lg-12 col-xl-6 featured-responsive" style="margin-bottom: 15px">
                        <div class="featured-place-wrap">
EOT;
        $href = $pendingpath . "detail.php?r_id=$r_id";
        echo "<a href=$href>";
        echo <<< EOT
                                <img src="$firstFile" class="img-fluid" alt="#">
                                <span class="featured-rating-orange ">$avg_review</span>
                                <div class="featured-title-box">
                                    <h6>$name</h6>
                                    <p>Restaurant </p> <span>• </span>
                                    <p>$num_reviews Reviews</p> <span> • </span>
EOT;
        generate_avg_cost($r_id);
        generate_categories($r_id);

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
EOT;
        if (is_open($r_id)) {
            echo "<div class=\"open-now\">OPEN NOW</div>";
        } else {
            echo "<div class=\"closed-now\">CLOSED NOW</div>";
        }
        if ($find_pending) {
            echo "<form method = 'post' action='viewpending.php'>";
            echo "<button type='submit' class='btn' name='accept_res' style='margin: 10px'>Accept</button>";
            echo "</form>";
        }
        echo <<< EOT
                                        <span class="ti-bookmark"></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
EOT;

    }
}

function generate_categories($r_id) {
    global $db;

    $cg = "SELECT category
           FROM restaurant_category
           WHERE r_id = $r_id";
    $rows = array();
    $cg = mysqli_query($db, $cg) or die(mysqli_error($db));

    while ($row = mysqli_fetch_array($cg, MYSQLI_NUM)) $rows[] = $row[0];
    echo "<ul style='padding-left: 0'>";
    for ($i = 0; $i < count($rows) - 1; $i++) {
        echo "<p style='padding-left: 0'>$rows[$i]</p>";
        echo "<span style='padding: 0 10px'>• </span>";
    }
    $last = end($rows);
    echo "<p style='padding-left: 0'>$last</p>";
    echo "</ul>";

}

function get_num_restaurants()
{
    global $db;

    $query = "SELECT COUNT(*)
              FROM restaurant";
    $query = mysqli_query($db, $query);
    $query = mysqli_fetch_array($query);
    return $query[0];
}

if (isset($_POST['accept_res'])) {
    accept_pending();
}

function accept_pending()
{
    global $db;

    $query1 = "UPDATE restaurant
               SET pending = 0";
    $query1 = mysqli_query($db, $query1) or die(mysqli_error($db));
    header('location: viewpending.php');
}

/*
 * =========================================================
 * ADD LISTING FUNCTIONS
 * =========================================================
 */

$r_name = "";
$location = "";
$phone_num = "";
$website = "";

// call the register() function if register_btn is clicked
if (isset($_POST['listing_btn'])) {
    add_listing();
}

function add_listing()
{
    // call these variables with the global keyword to make them available in function
    global $db, $errors, $r_name, $location, $phone_num, $website;

    $wifi = 0;
    $delivery = 0;
    $alcohol = 0;
    // receive all input values from the form. Call the e() function
    // defined below to escape form values
    $r_name = e($_POST['name']);
    $location = e($_POST['location']);
    if (isset($_POST['phone_num']))
        $phone_num = e($_POST['phone_num']);
    if (isset($_POST['wifi']))
        $wifi = 1;
    if (isset($_POST['delivery']))
        $delivery = 1;
    if (isset($_POST['alcohol']))
        $alcohol = 1;
    if (isset($_POST['website']))
        $website = e($_POST['website']);

    // form validation: ensure that the form is correctly filled
    if (empty($r_name)) {
        array_push($errors, "Restaurant name is required");
    }
    if (empty($location)) {
        array_push($errors, "Restaurant location is required");
    }

    if (count($errors) == 0) {

        if (isAdmin()) {
            $query = "INSERT INTO restaurant (name, location, wifi, delivery, alcohol, phone_num, website, pending) 
					  VALUES('$r_name', '$location', $wifi, $delivery, $alcohol, '$phone_num', '$website', 0)";
            $query = mysqli_query($db, $query);
            if ($query) {
                $_SESSION['listing_success'] = "New restaurant successfully created!!";
                $new_r_id = mysqli_insert_id($db);
                handle_images($new_r_id);
                handle_hours($new_r_id);
                if (count($errors) == 0) {
                    header('location: detail.php?r_id=' . $new_r_id);
                }
            } else {
                array_push($errors, mysqli_error($db));
            }
        } else {
            $query = "INSERT INTO restaurant (name, location, wifi, delivery, alcohol, phone_num, website, pending) 
					  VALUES('$r_name', '$location', $wifi, $delivery, $alcohol, '$phone_num', '$website', 1)";
            $query = mysqli_query($db, $query);

            if ($query) {
                $_SESSION['listing_pend_success'] = "Restaurant submitted for approval";
                if (isset($_SESSION['admin_success'])) {
                    echo '<script language="javascript">';
                    echo $_SESSION['listing_pend_success'];
                    echo '</script>';
                }
                header('location: index.php');
            } else {
                array_push($errors, "Error submitting restaurant");
            }
        }
    }
}

function handle_images($r_id)
{
    global $errors, $db;


    //Code taken from https://www.w3schools.com/php/php_file_upload.asp
    for ($i = 0; $i < 3; $i++) {
        $target_dir = "images\\restaurants\\$r_id\\";
        $target_file = $target_dir . basename($_FILES["pic$i"]["name"]);
        $uploadOk = 1;
        $category = $_POST["category$i"];

        if (!file_exists($_FILES["pic$i"]['tmp_name']) || !is_uploaded_file($_FILES["pic$i"]['tmp_name'])) {
            continue;
        }

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Check if image file is a actual image or fake image
        if (isset($_POST["pic$i"])) {
            $check = getimagesize($_FILES["pic$i"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                array_push($errors, "File is not an image.");
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            array_push($errors, "Sorry, file already exists.");
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["pic$i"]["size"] > 500000) {
            array_push($errors, "Sorry, your file is too large.");
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            array_push($errors, "Sorry, your file was not uploaded.");
            // if everything is ok, try to upload file
        } else {
            $filename = basename($_FILES["pic$i"]["name"]);
            $filepath = addslashes($target_file);
            $query = "INSERT INTO photo (title, category, file_path) 
					  VALUES('$filename', '$category', '$filepath')";
            $query = mysqli_query($db, $query);

            if (!$query) {
                array_push($errors, "Insertion into photos failed: " . mysqli_error($db));
            }

            $photo_id = mysqli_insert_id($db);
            $logged_in_user = $_SESSION['user']['username'];
            $query = "INSERT INTO uploads (user_id, photoid, r_id) 
					  VALUES('$logged_in_user', $photo_id, $r_id)";
            $query = mysqli_query($db, $query);

            if (!$query) {
                array_push($errors, "Insertion into uploads failed" . mysqli_error($db));
            }
            if (move_uploaded_file($_FILES["pic$i"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["pic$i"]["name"]) . " has been uploaded.";
            } else {
                array_push($errors, "Sorry, there was an error uploading your file.");
            }
        }
    }
}

function handle_hours($r_id)
{
    global $db, $errors;

    for ($i = -1; $i < 6; $i++) {
        $day = strtolower(date('l', mktime(0, 0, 0, 0, $i, 0)));
        $open = $_POST[$day . "_open"];
        $close = $_POST[$day . '_close'];
        $day_num = $i + 2;
        $query = "INSERT INTO business_hours (r_id, day_of_week, open_time, close_time)
                  VALUES ($r_id, $day_num, '$open', '$close')
                      ON DUPLICATE KEY UPDATE
                      `open_time` = IF('" . $open . "' = '', open_time, '$open'),
                      `close_time` = IF('" . $close . "' = '', close_time, '$close')";

        $query = mysqli_query($db, $query);
        if (!$query) {
            array_push($errors, "Insertion into hours failed" . mysqli_error($db));
        }
    }

}

/*
 * =========================================================
 * EDIT FUNCTIONS
 * =========================================================
 */

function get_ticks($r_id)
{
    global $db;

    $query = "SELECT wifi, delivery, alcohol
              FROM restaurant
              WHERE r_id = $r_id";
    $query = mysqli_query($db, $query) or die(mysqli_error($db));
    $query = mysqli_fetch_assoc($query);
    return $query;
}

if (isset($_POST['edit_listing_btn'])) {
    edit_listing();
}

$category = "";


function edit_listing()
{
    // call these variables with the global keyword to make them available in function
    global $db, $errors, $r_name, $location, $phone_num, $website, $category;

    $r_id = $_GET['r_id'];

    // receive all input values from the form. Call the e() function
    // defined below to escape form values
    if (isset($_POST['r_name']))
        $r_name = e($_POST['name']);
    if (isset($_POST['location']))
        $location = e($_POST['location']);
    if (isset($_POST['phone_num']))
        $phone_num = e($_POST['phone_num']);
    if (isset($_POST['wifi']))
        $wifi = 1;
    else
        $wifi = 0;
    if (isset($_POST['delivery']))
        $delivery = 1;
    else
        $delivery = 0;
    if (isset($_POST['alcohol']))
        $alcohol = 1;
    else
        $alcohol = 0;
    if (isset($_POST['website']))
        $website = e($_POST['website']);
    if (isset($_POST['category']))
        $category = e($_POST['category']);

    if (count($errors) == 0) {
        //Handle restaurant info update
        $query1 = "UPDATE restaurant
                  SET
                  `name` = IF('" . $r_name . "' = '', name, '$r_name'), 
                  `location` = IF('" . $location . "' = '', location, '$location'), 
                  `wifi` = $wifi, 
                  `delivery` = $delivery,
                  `alcohol` = $alcohol,
                  `phone_num` = IF('" . $phone_num . "' = '', phone_num, '$phone_num'),
                  `website` = IF('" . $website . "' = '', website, '$website')
                  WHERE r_id = $r_id";
        $query1 = mysqli_query($db, $query1) or die(mysqli_error($db));

        //Handle adding category
        if (!empty($category)) {
            $query2 = "INSERT INTO `restaurant_category` (`r_id`, `category`)
                       VALUES ($r_id, '$category')";

            $query2 = mysqli_query($db, $query2) or die(mysqli_error($db));
        }

        handle_hours($r_id);
        handle_images($r_id);
        if ($query1) {
            $_SESSION['edit_success'] = "Restaurant successfully edited!!";
            echo $_SESSION['edit_success'];
        } else {
            array_push($errors, mysqli_error($db));
        }
    }
}

/*
 * =========================================================
 * USER FUNCTIONS
 * =========================================================
 */


function show_user()
{
    global $db;

    $query = "SELECT * FROM user WHERE username = '{$_SESSION['user']['username']}'";
    $result = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($result)){
        $usern = $temp['username'];
        $date_joined = $temp['date_joined'];
        $first_name = $temp['fname'];
        $last_name = $temp['lname'];

        echo <<< EOT
        <h4> User info: </h4>
        <hr>
            <p>Username: $usern</p>
            <br>
            <p>Date joined: $date_joined</p>
            <br>
            <p>First Name: $first_name</p>
            <br>
            <p>Last Name: $last_name</p>
        <hr>

EOT;

    }
}

function get_profile_reviews() 
{
    global $db;

    $query = "SELECT * FROM review WHERE user_id = '{$_SESSION['user']['username']}'";
    $result = mysqli_query($db, $query);
    

    while ($temp = mysqli_fetch_array($result)){
        $user_review = $temp['content'];
        $user_rating = $temp['rating'];
        $user_review_date = $temp['date_posted'];
        $user_review_rid = $temp['r_id'];
        $user_review_cost = $temp['cost'];
        $reviewer = $temp['user_id'];

        // get how manyy reviews the use has reviewed
        $query2 = "SELECT COUNT(*)
                   FROM review
                   WHERE user_id = '{$_SESSION['user']['username']}'";
        $num_reviews = mysqli_query($db, $query2);
        $num_reviews = mysqli_fetch_array($num_reviews);
        $num_reviews = $num_reviews[0];

        echo <<< EOT
                    <h4> User Reviews: <h4>
                    <hr>
                    <div class="customer-review_wrap">
                        <div class="customer-img">
                            <p>$reviewer</p>
                            <span>$num_reviews Reviews</span>
                        </div>
                        <div class="customer-content-wrap">
                            <div class="customer-content">
                                <div class="customer-review">
EOT;
        generate_cost($user_review_cost);
        echo <<< EOT
                    <br>
                    <p>Reviewed $user_review_date</p>
                    </div>
                    <div class="customer-rating">$user_rating / 5</div>
                    </div>
                    <p class="customer-text">$user_review</p>          
                    </div>
                    </div>
                    <hr>
EOT;

        

    }
} 

function generateUserPhotos($isEdit)
{
    global $db;

    $query = "SELECT p.file_path
              FROM photo AS p, uploads AS u
              WHERE p.photo_id = u.photoid AND u.user_id = '{$_SESSION['user']['username']}'";
    $query = mysqli_query($db, $query);
    echo "<h4> Photos uploaded by user: </h4>";

    while ($temp = mysqli_fetch_array($query)) {

        echo <<< EOT
        "<a href=$temp[0] class='grid image-link'>";
        "<img src=$temp[0] class='img-fluid' alt='#'>";
        "</a>";
        

EOT;
        if ($isEdit) {
            echo "<button type='button'>Delete</button>";
        }
        echo "</div>";
    }
}