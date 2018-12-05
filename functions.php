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
                $query2 = "SELECT * FROM user WHERE username='$username' LIMIT 1";
                $results2 = mysqli_query($db, $query2);
                $logged_in_reguser = mysqli_fetch_assoc($results2);

                $_SESSION['user'] = $logged_in_reguser; // put logged in user in session
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

    $query = "SELECT p.file_path, p.photo_id
              FROM photo AS p, uploads AS u
              WHERE p.photo_id = u.photoid AND u.r_id = $rid";
    $query = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($query)) {
        $pic_id = $temp['photo_id'];
        echo "<div class='swiper-slide'>";
        echo "<a href=$temp[0] class='grid image-link' id='edit_pics'>";
        echo "<img src=$temp[0] class='img-fluid' alt='#'>";
        echo "</a>";
        if ($isEdit) {
            echo "<label for='pic_delete[]' style='vertical-align: middle'>Delete</label>";
            echo "<input type='checkbox' name='pic_delete[]' value='$pic_id'>";
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

function require_login($r_id, $page)
{
    if (isset($_SESSION['user'])) {
        echo "href='$page.php?r_id=$r_id'";
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

function generate_reviews($r_id)
{
    global $db;

    $query = "SELECT *
              FROM review
              WHERE r_id = $r_id";
    $query = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($query)) {
        $user_id = $temp['user_id'];
        $cost = $temp['cost'];
        $date_value = $temp['date_posted'];
        $date_posted = date_format(date_create($temp['date_posted']), "F j, Y");
        $rating = $temp['rating'];
        $content = $temp['content'];

        $query2 = "SELECT COUNT(*)
                   FROM review
                   WHERE user_id = '$user_id'";
        $num_reviews = mysqli_query($db, $query2);
        $num_reviews = mysqli_fetch_array($num_reviews);
        $num_reviews = $num_reviews[0];
        $href = "showuser.php?username=$user_id";
        echo <<< EOT
                    <hr>
                    <div class="customer-review_wrap">
                        <div class="customer-img">
                            <a href=$href> <p>$user_id</p> </a>
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
EOT;
        if (isAdmin()) {
            echo <<< EOT
                        <form method = 'post' action='detail.php?r_id=$r_id'>
                            <button type='submit' class='btn' name='delete_review' value='$date_value;$r_id;$user_id'
                             style='margin: 10px; color: red'>Delete</button>
                        </form>
EOT;
        }
        echo <<< EOT
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

if (isset($_POST['delete_review'])) {
    delete_review();
}

function delete_review()
{
    global $db;
    $info = explode(';', $_POST['delete_review']);

    $query = "DELETE FROM review WHERE date_posted = '$info[0]' AND r_id = $info[1] AND user_id = '$info[2]'";
    $query = mysqli_query($db, $query);
    if ($query) {
        echo "Review successfully deleted!";
    } else {
        echo "Error deleting review!";
    }
}

/*
 * =========================================================
 * LISTING FUNCTIONS
 * =========================================================
 */

function generate_restaurants($name, $find_pending, $featured, $filter_by = -1, $order_by = -1)
{
    global $db;
    $pendingpath = "";
    $div_class = "col-sm-6 col-lg-12 col-xl-6 featured-responsive";

    if (isset($_GET['wifi']))
        $wifi = e($_GET['wifi']);
    if (isset($_GET['delivery']))
        $delivery = e($_GET['delivery']);
    if (isset($_GET['alcohol']))
        $alcohol = e($_GET['alcohol']);

    //Determine how to query restaurants
    if ($find_pending and $name == NULL) {
        $query = "SELECT *
                  FROM restaurant AS res
                  WHERE res.pending = 0x1";
        $pendingpath = "../";
    } else if ($featured and $name == NULL) {
        $query = "SELECT res.*
                    FROM restaurant AS res, review AS rev
                    WHERE res.pending = '0' AND res.r_id = rev.r_id
                    GROUP BY rev.r_id
                    ORDER BY AVG(rev.rating) DESC
                    LIMIT 3";
        $div_class = "col-md-4 featured-responsive";
    } else if ($filter_by != -1 && ($filter_by != 'category' || $order_by != -1) and $name == NULL) {
        if ($filter_by == 'cost') {
            if ($order_by == -1) {
                $order_by = 'ASC';
            }
            $query = "SELECT res.*
                        FROM restaurant AS res, review AS rev
                        WHERE res.pending = '0' AND res.r_id = rev.r_id
                        GROUP BY rev.r_id
                        ORDER BY AVG(rev.cost) $order_by";
        } else if ($filter_by == 'rating') {
            if ($order_by == -1) {
                $order_by = 'DESC';
            }
            $query = "SELECT res.*
                        FROM restaurant AS res, review AS rev
                        WHERE res.pending = '0' AND res.r_id = rev.r_id
                        GROUP BY rev.r_id
                        ORDER BY AVG(rev.rating) $order_by";
        } else if ($filter_by == 'open') {
            date_default_timezone_set('America/Phoenix');
            $day_of_week = date('N');
            $time = date('H:i:s');
            $query = "SELECT res.*
                      FROM restaurant AS res, business_hours AS bh
                      WHERE bh.day_of_week = $day_of_week AND res.pending = '0' AND
                      '$time' > bh.open_time AND '$time' < bh.close_time
                       AND res.r_id = bh.r_id";
        } else if ($filter_by == 'category') {
            $query = "SELECT *
                      FROM restaurant AS res, restaurant_category AS cg
                      WHERE res.pending = '0' AND res.r_id = cg.r_id AND cg.category = '$order_by'";
        }
    } else if ($name != NULL) {
        $query = "SELECT *
        FROM restaurant AS res
        WHERE res.pending = '0' and res.name LIKE '%" . $name . "%'";
    } else {
        $query = "SELECT *
                  FROM restaurant AS res
                  WHERE res.pending = '0'";
    }

    if (isset($_GET['wifi']) && $wifi == 'on') {
        $pos = strpos($query, 'WHERE');
        $str = " res.wifi = 1 AND";
        $query = substr_replace($query, $str, $pos + 5, 0);
    }
    if (isset($_GET['delivery']) && $delivery == 'on') {
        $pos = strpos($query, 'WHERE');
        $str = " res.delivery = 1 AND";
        $query = substr_replace($query, $str, $pos + 5, 0);
    }
    if (isset($_GET['alcohol']) && $alcohol == 'on') {
        $pos = strpos($query, 'WHERE');
        $str = " res.alcohol = 1 AND";
        $query = substr_replace($query, $str, $pos + 5, 0);
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
                    <div class="$div_class" style="margin-bottom: 15px">
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

function generate_categories($r_id)
{
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

function category_filter()
{
    global $db;
    $cg = "SELECT category
            FROM restaurant_category";
    $cg = mysqli_query($db, $cg);
    if (isset($_GET['order_by'])) {
        $category = $_GET['order_by'];
        echo "<option value=$category selected='selected'>$category</option>";
    } else {
        echo "<option disabled selected value> -- select an option --</option>";
    }
    while ($row = mysqli_fetch_array($cg, MYSQLI_NUM)) {

        echo "<option value=$row[0]>$row[0]</option>";

    }
}

if (isset($_GET['filter_by'])) {
    if (isset($_SESSION['prev_filter']) && $_SESSION['prev_filter'] != $_GET['filter_by'])
        unset($_GET['order_by']);
    $_SESSION['prev_filter'] = $_GET['filter_by'];
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

function handle_images($r_id, $is_food = false, $food_name = '', $food_price = '', $food_calories = '')
{
    global $errors, $db;


    //Code taken from https://www.w3schools.com/php/php_file_upload.asp
    for ($i = 0; $is_food ? $i < 1 : $i < 3; $i++) {
        $target_dir = "images\\restaurants\\$r_id\\";
        $target_file = $target_dir . basename($_FILES["pic$i"]["name"]);
        $uploadOk = 1;
        if (!$is_food)
            $category = $_POST["category$i"];
        else
            $category = 'food';

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

            if ($is_food) {
                $query = "INSERT INTO food_item (food_item_name, price, picture_path, calories, r_id) 
					  VALUES('$food_name', $food_price, '$filepath', $food_calories, $r_id)";
                $query = mysqli_query($db, $query) or die(mysqli_error($db));
            }

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

    if (!empty($_POST['pic_delete'])) {
        $good = true;
        foreach ($_POST['pic_delete'] as $id) {
            $query = "DELETE FROM uploads WHERE photoid = $id";
            $query = mysqli_query($db, $query);
            if (!$query) {
                $good = false;
                echo "There was an error deleting a photo";
                continue;
            }
            $username = $_SESSION['user']['username'];
            $query = "INSERT INTO deletes (admin_user, r_id, photoid)
                      VALUES ('$username', $r_id, $id)";
            $query = mysqli_query($db, $query);

        }
        if ($good) {
            echo "Photos deleted from restaurant successfully";
        } else {
            echo "There was an error deleting photos";
        }
    }

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
            $username = $_SESSION['user']['username'];
            $query = "INSERT INTO edits (admin_user, r_id)
                        VALUES ('$username', $r_id)";
            $query = mysqli_query($db, $query);
            $_SESSION['edit_success'] = "<br>Restaurant successfully edited!!";
            echo $_SESSION['edit_success'];
        } else {
            array_push($errors, mysqli_error($db));
        }
    }
}

/*
 * =========================================================
 * FOOD FUNCTIONS
 * =========================================================
 */

function generate_food($r_id)
{
    global $db;
    $query = "SELECT *
                FROM food_item
                WHERE r_id = $r_id";
    $query = mysqli_query($db, $query);
    while ($temp = mysqli_fetch_array($query)) {
        $food_name = $temp['food_item_name'];
        $price = $temp['price'];
        $file_path = $temp['picture_path'];
        $calories = $temp['calories'];
        echo <<< EOT
        <div>
            <div class="customer-review_wrap" style="width: auto; margin-bottom: 15px">
                <a href="$file_path" class="grid image-link" id="edit_pics" style="display: inline-block; vertical-align: top">
                    <img src="$file_path" class="img-fluid" alt="#" id="food_pics">
                </a>
            </div>
            <div style="display: inline-block; float:right">
                <h3>$food_name</h3>
                <h4>$$price</h4>
                <h4>$calories Calories</h4>
                <label>Quantity</label>
                <input form="food_form" type="number" name="quantity_$food_name" min="0" value="0" style="width:40px">
            </div>
        </div>
        <hr>
EOT;
    }
}

if (isset($_POST['add_food_btn'])) {
    add_food();
}

function add_food()
{
    // call these variables with the global keyword to make them available in function
    global $db, $errors;

    // receive all input values from the form. Call the e() function
    // defined below to escape form values
    $food_name = e($_POST['name']);
    $price = e($_POST['price']);
    $calories = e($_POST['calories']);

    // form validation: ensure that the form is correctly filled
    if (empty($food_name)) {
        array_push($errors, "Food name is required");
    }
    if (empty($price)) {
        array_push($errors, "Food price is required");
    }
    if (empty($calories)) {
        array_push($errors, "Food calorie count is required");
    }
    if (!file_exists($_FILES["pic0"]['tmp_name']) || !is_uploaded_file($_FILES["pic0"]['tmp_name'])) {
        array_push($errors, "Food picture is required");
    }

    if (count($errors) == 0) {
        handle_images($_GET['r_id'], true, $food_name, $price, $calories);
        if (count($errors) == 0) {
            echo "Food Item Created!";
        }
    } else {
        array_push($errors, "There was an error adding food item");
    }
}

/*
 * =========================================================
 * ORDER FUNCTIONS
 * =========================================================
 */

function generate_order($r_id)
{
    global $db;
    $order_items = array();
    $query = "SELECT food_item_name, price
                FROM food_item
                WHERE r_id = $r_id";
    $query = mysqli_query($db, $query);
    echo "<div style='color: white'>";
    echo "<h6>Order information:</h6>";
    while ($temp = mysqli_fetch_array($query)) {
        $food_name = $temp[0];
        $quantity = $_POST['quantity_' . $food_name];
        if ($quantity == 0)
            continue;
        $price = $temp[1];
        array_push($order_items, array("name" => $food_name, "quantity" => $quantity, "price" => $price));
        echo "<p style='display: inline-block'>$food_name [$quantity]</p>";
        echo "<p style='float: right'>$$price</p>";
        echo "<br>";
    }
    echo "</div>";
    $_SESSION['order_rid'] = $r_id;
    $serialized = htmlspecialchars(serialize($order_items));
    echo "<input type=\"hidden\" name=\"ArrayData\" value=\"$serialized\"/>";

}

if (isset($_POST['confirm_order_btn'])) {
    add_order();
}

function add_order()
{
    // call these variables with the global keyword to make them available in function
    global $db, $errors;
    $order_items =  unserialize($_POST['ArrayData']);
    // receive all input values from the form. Call the e() function
    // defined below to escape form values
    $address = "";
    $email = "";

    if (isset($_POST['address']))
        $address = $_POST['address'];
    if (isset($_POST['email']))
        $email = $_POST['email'];

    // form validation: ensure that the form is correctly filled
    if (empty($address)) {
        array_push($errors, "Address is required");
    }
    if (empty($email)) {
        array_push($errors, "Email is required");
    }

    if (count($errors) == 0) {
        $user_id = $_SESSION['user']['username'];
        $price = 0;
        foreach ($order_items as $item) {
            $price += $item['price'];
        }
        $query = "INSERT INTO `order` (total_price, email, address, user_id)
                  VALUES ($price, '$email', '$address', '$user_id') ";
        $query = mysqli_query($db, $query) or die(mysqli_error($db));
        if ($query) {
            $order_id = mysqli_insert_id($db);
            $r_id = $_SESSION['order_rid'];
            unset($_SESSION['order_rid']);
            foreach ($order_items as $item) {
                $food_name = $item['name'];
                $quantity = $item['quantity'];
                $query = "INSERT INTO made_from (food_item_name, orderid, r_id, quantity)
                          VALUES ('$food_name', $order_id, $r_id, $quantity)";
                $query = mysqli_query($db, $query) or die(mysqli_error($db));
            }
        }
        echo "Order Placed!";
    } else {
        array_push($errors, "There was an error making order");
    }
}

/*
 * =========================================================
 * USER FUNCTIONS
 * =========================================================
 */

// Used to display another user's profile
function show_user()
{
    global $db;

    $query = "SELECT * FROM user WHERE username = '{$_GET['username']}'";
    $result = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($result)) {
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


// Used to display information for the logged in users profile 
function view_profile()
{
    global $db;

    $query = "SELECT * FROM user WHERE username = '{$_SESSION['user']['username']}'";
    $result = mysqli_query($db, $query);

    while ($temp = mysqli_fetch_array($result)) {
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

function show_user_reviews()
{
    global $db;

    $query = "SELECT rev.*, res.name FROM review AS rev, restaurant AS res WHERE rev.user_id = '{$_GET['username']}' AND res.r_id = rev.r_id";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    // get how many reviews the user has posted
    $query2 = "SELECT COUNT(*)
               FROM review
               WHERE user_id = '{$_GET['username']}'";
    $num_reviews = mysqli_query($db, $query2);
    $num_reviews = mysqli_fetch_array($num_reviews);
    $num_reviews = $num_reviews[0];

    echo "<h4> User Reviews ($num_reviews): </h4>";
    while ($temp = mysqli_fetch_array($result)) {
        $user_review = $temp['content'];
        $user_rating = $temp['rating'];
        $user_review_date = $temp['date_posted'];
        $r_id = $temp['r_id'];
        $rev_res_name = $temp['name'];
        $user_review_cost = $temp['cost'];
        $reviewer = $temp['user_id'];


        echo <<< EOT
                    <hr>
                    <h6>
                        <a href='detail.php?r_id=$r_id'>
                            $rev_res_name
                        </a>
                    </h6>
                    <div class="customer-review_wrap">
                        <div class="customer-img">
                            <p>$reviewer</p>
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


function get_profile_reviews()
{
    global $db;

    $query = "SELECT rev.*, res.name FROM review AS rev, restaurant AS res WHERE rev.user_id = '{$_SESSION['user']['username']}' AND res.r_id = rev.r_id";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    // get how many reviews the user has posted
    $query2 = "SELECT COUNT(*)
               FROM review
               WHERE user_id = '{$_SESSION['user']['username']}'";
    $num_reviews = mysqli_query($db, $query2);
    $num_reviews = mysqli_fetch_array($num_reviews);
    $num_reviews = $num_reviews[0];

    echo "<h4> User Reviews ($num_reviews): </h4>";
    while ($temp = mysqli_fetch_array($result)) {
        $user_review = $temp['content'];
        $user_rating = $temp['rating'];
        $user_review_date = $temp['date_posted'];
        $r_id = $temp['r_id'];
        $rev_res_name = $temp['name'];
        $user_review_cost = $temp['cost'];
        $reviewer = $temp['user_id'];


        echo <<< EOT
                    <hr>
                    <h6>
                        <a href='detail.php?r_id=$r_id'>
                            $rev_res_name
                        </a>
                    </h6>
                    <div class="customer-review_wrap">
                        <div class="customer-img">
                            <p>$reviewer</p>
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
                    <form method = 'post' action='profile.php?'>
                    <button type='submit' class='btn' name='delete_review' value='$user_review_date;$r_id;$reviewer'
                    style='margin: 10px; color: red'>Delete</button>
                    </form>
                    <hr>

EOT;


    }
}

function viewUserPhotos($username)
{
    global $db;

    $query = "SELECT p.file_path, p.photo_id
              FROM photo AS p, uploads AS u
              WHERE p.photo_id = u.photoid AND u.user_id = '$username'";
    $query = mysqli_query($db, $query);
    echo "<h4>Photos uploaded by $username:</h4>";
    echo "<form method = 'post' action='viewuserphotos.php?username=$username'>";
    while ($temp = mysqli_fetch_array($query)) {

        echo <<< EOT
        <div>
            <div class="customer-review_wrap" style="width: auto; margin-bottom: 15px">
                <a href=$temp[0] class='grid image-link' id='edit_pics' style="display: inline-block; vertical-align: top">
                    <img src=$temp[0] class='img-fluid' alt='#' id="food_pics">
                </a>
            </div>
            <div style="display: inline-block; float:right">
                <label for='pic_delete[]' style='vertical-align: middle'>Delete</label>
                <input type='checkbox' name='pic_delete[]' value='$temp[1]' style="width: 25px; height: 25px">
            </div>
        </div>
        <hr>
            
EOT;
    }
    echo <<< EOT
            <div class="reg-input">
                <button type="submit" class="btn" name="edit_photos">Submit</button>
            </div>
        </form>
EOT;
}

if (isset($_POST['edit_photos'])) {
    delete_user_photo();
}

function delete_user_photo()
{
    global $db;

    if (!empty($_POST['pic_delete'])) {
        $good = true;
        foreach ($_POST['pic_delete'] as $id) {
            $query = "DELETE FROM uploads WHERE photoid = $id";
            $query = mysqli_query($db, $query);
            if (!$query)
                $good = false;
            $query = "DELETE FROM photo WHERE photo_id = $id";
            $query = mysqli_query($db, $query);
            if (!$query)
                $good = false;
        }
        if ($good) {
            echo "Photos deleted successfully";
        } else {
            echo "There was an error deleting photos";
        }
    }
}

$name = "";
$num_likes = "";
$num_restaurants = "";
$user_id = "";


function create_list()
{

    global $db;
    $listname = e($_POST['listname']);
    $user_id = $_SESSION['user']['username'];
    $query = "INSERT INTO list (name, num_likes, num_restaurants, user_id) 
			  VALUES('$listname', 0, 0, '$user_id')";
    $query = mysqli_query($db, $query) or die(mysqli_error($db));

}


function delete_list()
{
    global $db;
    $info = explode(';', $_POST['delete_list']);
    $list = $info[0];
    $user = $info[1];
    // Delete from child table first to prevent froeign key constraint being violated
    $query1 = "DELETE 
               FROM adds_to
               WHERE list_name = '$list' AND list_user = '$user'";
    $query2 = "DELETE 
               FROM list 
               WHERE name = '$list' AND user_id = '$user'";
    $query1 = mysqli_query($db, $query1) or die(mysqli_error($db));
    $query2 = mysqli_query($db, $query2) or die(mysqli_error($db));
    if ($query1 && $query2) {
        echo "List successfully deleted!";
    } else {
        echo "Error deleting list!";
    }
}


function view_list_info()
{
    global $db;
    $user_id = ($_SESSION['user']['username']);
    $query = "SELECT * FROM list WHERE user_id = '{$_GET['username']}'";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
    while ($temp = mysqli_fetch_array($result)) {
        $name = $temp['name'];
        $date_created = $temp['date_created'];
        $num_likes = $temp['num_likes'];
        //$num_restaurants = $temp['num_restaurants'];
        $num_restaurants = num_restaurants_in_list($name);
        if ($_GET['username'] == ($_SESSION['user']['username'])) {
            echo <<< EOT
                        <form method = 'post' action='lists.php?username=$user_id'>
                            <button type='submit' class='btn' name='delete_list' value='$name;$user_id'
                             style='margin: 10px; color: red'>Delete</button>
                        </form>

                        <form method = 'post' action=''>
                             <input type='text' name='new_name'>
                             <button type='submit' class='btn' name='edit_list_name' value='$name'
                             style='margin: 10px; color: red'>Edit List Name</button>
                        </form>
EOT;
        }

        echo <<< EOT
        <h4> $name </h4>
        <hr>
            <p>Date created: $date_created</p>
            <br>
            <p>Number of likes: $num_likes</p>
            <br>
            <p>Number of restaurants: $num_restaurants</p>
            <br>
            <p>Restaurants:</p>
        

EOT;
        generate_list_restaurants($name);

    }
}


if (isset($_POST['delete_list'])) {
    delete_list();
}

if (isset($_POST['edit_list_name'])) {
    $new_name = $_POST['new_name'];
    edit_list_name($new_name);
}

function edit_list_name($new_name)
{
    global $db;
    $list = $_POST['edit_list_name'];
    // Update adds_to first so that foreign key constraint is not voilated
    $query1 = "UPDATE adds_to SET list_name = '$new_name' WHERE list_name = '$list' ";
    $query2 = "UPDATE list SET name = '$new_name' WHERE name = '$list' ";
    $result1 = mysqli_query($db, $query1) or die(mysqli_error($db));
    $result2 = mysqli_query($db, $query2) or die(mysqli_error($db));
    if ($result1 && $result2) {
        echo "List name edited successfully!";
    } else {
        echo "Error editing list name";
    }
}


// Generates the restaurants in a given list
function generate_list_restaurants($name)
{
    global $db;
    $query = "SELECT DISTINCT r.name 
              FROM restaurant AS r, adds_to AS a, list AS l
              WHERE r.r_id = a.r_id AND a.list_name = '$name'";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
    while ($temp = mysqli_fetch_array($result)) {

        echo "<p>$temp[0]</p><br>";

    }
    echo "<hr>";
}

// Generate lists for drop down select
function generate_lists()
{
    global $db;
    $query = "SELECT name FROM list WHERE user_id = '{$_SESSION['user']['username']}'";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
    return $result;
}

if (isset($_POST['add_list_btn'])) {
    create_list();
}

// Get the number of restaurants that are in a list
function num_restaurants_in_list($name)
{
    global $db;

    $query = "SELECT COUNT(*)
              FROM adds_to
              WHERE list_name = '$name'";
    $query = mysqli_query($db, $query) or die(mysqli_error($db));
    $query = mysqli_fetch_array($query);
    return $query[0];
}

// Adds info to adds_to
function add_to_list($r_id)
{
    global $db;
    if (isset($_POST['selectedlist'])) {
        $selectedlist = $_POST['selectedlist'];
    }
    $user_id = $_SESSION['user']['username'];
    $query = "INSERT INTO adds_to (user_id, list_name, list_user, r_id) 
    VALUES('$user_id', '$selectedlist', '$user_id', '$r_id')";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
}





