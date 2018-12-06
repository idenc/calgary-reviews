<?php include('functions.php');
/**
 * Created by PhpStorm.
 * User: Iden
 * Date: 11/28/2018
 * Time: 10:30 PM
 */

global $r_id, $wifi, $delivery, $alcohol, $info;
if (isset($_GET['r_id'])) {
    $r_id = $_GET['r_id'];
}
$array = get_ticks($r_id);
$wifi = $array['wifi'];
$delivery = $array['delivery'];
$alcohol = $array['alcohol'];
$info = get_info($r_id);
?>

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
    <title>Calgary Reviews</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500,700,900" rel="stylesheet">
    <!-- Simple line Icon -->
    <link rel="stylesheet" href="css/simple-line-icons.css">
    <!-- Themify Icon -->
    <link rel="stylesheet" href="css/themify-icons.css">
    <!-- Hover Effects -->
    <link rel="stylesheet" href="css/set1.css">
    <!-- Swipper Slider -->
    <link rel="stylesheet" href="css/swiper.min.css">
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="css/magnific-popup.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<!--============================= HEADER =============================-->
<div class="dark-bg sticky-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="index.php">Calgary Reviews</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-menu"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                        <ul class="navbar-nav">
                            <li class="nav-item active">
                                <a class="nav-link" href="listing.php">Browse</a>
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
                                        <a class="dropdown-item" href="#">Profile</a>
                                        <a class="dropdown-item" href="#">Lists</a>
                                        <a class="dropdown-item" href="#">Photos</a>
                                    </div>
                                </li>
                                <?php if (isAdmin()) : ?>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="admin/create_user.php" style="color: red;">Create
                                            User</a>
                                    </li>
                                <?php endif ?>
                                <li class="nav-item active">
                                    <a class="nav-link" href="index.php?logout='1'" style="color: red;">Logout</a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item active">
                                    <a class="nav-link" href="register.php">Register</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="login.php">Login</a>
                                </li>
                            <?php endif ?>
                            <li><a href="#" class="btn btn-outline-light top-btn"><span class="ti-plus"></span> Add
                                    Listing</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>
<!--//END HEADER -->
<!-- SLIDER -->
<div style="background: #3F3F3F; height: fit-content;">
    <form method="post" action="editrestaurant.php?r_id=<?php echo $r_id ?>" class="register"
          style="margin-bottom: 30%;"
          enctype="multipart/form-data">
        <?php display_error(); ?>
        <h5 style="color: white"><?php echo $info['name'] ?></h5>
        <div class="reg-input">
            <label>Restaurant Name</label>
            <input type="text" name="name" value="<?php echo $r_name; ?>">
        </div>
        <div class="reg-input">
            <label>Location</label>
            <input type="text" name="location" value="<?php echo $location; ?>">
        </div>
        <div class="reg-input">
            <label>Phone Number</label>
            <input type="text" name="phone_num" value="<?php echo $phone_num; ?>">
        </div>
        <div class="reg-input">
            <label>Website</label>
            <input type="text" name="website" value="<?php echo $website; ?>">
        </div>
        <div class="reg-input">
            <label>Add Category</label>
            <input type="text" name="category" value="<?php echo $category; ?>">
        </div>
        <div class="hours">
            <p style="color: white">Hours:</p>
            <div>
                <label for="monday" class="day">Monday</label>
                <label for="monday_open" style="margin-left: 50px">Open</label>
                <input type="time" name="monday_open">
                <label for="monday_close">Close</label>
                <input type="time" name="monday_close">
            </div>
            <div>
                <label for="tuesday" class="day">Tuesday</label>
                <label for="tuesday_open" style="margin-left: 50px">Open</label>
                <input type="time" name="tuesday_open">
                <label for="tuesday_close">Close</label>
                <input type="time" name="tuesday_close">
            </div>
            <div>
                <label for="wednesday" class="day">Wednesday</label>
                <label for="wednesday_open" style="margin-left: 50px">Open</label>
                <input type="time" name="wednesday_open">
                <label for="wednesday_close">Close</label>
                <input type="time" name="wednesday_close">
            </div>
            <div>
                <label for="thursday" class="day">Thursday</label>
                <label for="thursday_open" style="margin-left: 50px">Open</label>
                <input type="time" name="thursday_open">
                <label for="thursday_close">Close</label>
                <input type="time" name="thursday_close">
            </div>
            <div>
                <label for="friday" class="day">Friday</label>
                <label for="friday_open" style="margin-left: 50px">Open</label>
                <input type="time" name="friday_open">
                <label for="friday_close">Close</label>
                <input type="time" name="friday_close">
            </div>
            <div>
                <label for="saturday" class="day">Saturday</label>
                <label for="saturday_open" style="margin-left: 50px">Open</label>
                <input type="time" name="saturday_open">
                <label for="saturday_close">Close</label>
                <input type="time" name="saturday_close">
            </div>
            <div>
                <label for="sunday" class="day">Sunday</label>
                <label for="sunday_open" style="margin-left: 50px">Open</label>
                <input type="time" name="sunday_open">
                <label for="sunday_close">Close</label>
                <input type="time" name="sunday_close">
            </div>
            <br>
        </div>
        <h5 style="color:white">Current Photos</h5>
        <div class="editphotos">
            <?php generatePhotos($r_id, true) ?>
        </div>
        <div class="files">
            Upload up to 3 images for the restaurant:
            <input type="file" name="pic0" accept="image/*" style="padding: 5px 0 5px 0; width: 60%;">
            <label for="category0">Category: </label>
            <select name="category0">
                <option value="decor">Decor</option>
                <option value="restaurant">Restaurant</option>
                <option value="menu">Menu</option>
                <option value="food">Food</option>
                <option value="other">Other</option>
            </select>
            <input type="file" name="pic1" accept="image/*" style="padding: 5px 0 5px 0; width: 60%;">
            <label for="category1">Category: </label>
            <select name="category1">
                <option value="decor">Decor</option>
                <option value="restaurant">Restaurant</option>
                <option value="menu">Menu</option>
                <option value="food">Food</option>
                <option value="other">Other</option>
            </select>
            <input type="file" name="pic2" accept="image/*" style="padding: 5px 0 5px 0; width: 60%;">
            <label for="category2">Category: </label>
            <select name="category2" title="Category">
                <option value="decor">Decor</option>
                <option value="restaurant">Restaurant</option>
                <option value="menu">Menu</option>
                <option value="food">Food</option>
                <option value="other">Other</option>
            </select>
            <br>
        </div>

        <div class="reg-input">
            <label>Has Wifi?</label>
            <input id="listing_box" type="checkbox" name="wifi" <?php echo($wifi == 1 ? 'checked' : ''); ?>>
        </div>
        <div class="reg-input">
            <label>Has Delivery?</label>
            <input id="listing_box" type="checkbox" name="delivery" <?php echo($delivery == 1 ? 'checked' : ''); ?>>
        </div>
        <div class="reg-input">
            <label>Has Alcohol?</label>
            <input id="listing_box" type="checkbox" name="alcohol" <?php echo($alcohol == 1 ? 'checked' : ''); ?>>
        </div>
        <div class="reg-input">
            <button type="submit" class="btn" name="edit_listing_btn">Edit Restaurant</button>
        </div>
    </form>
</div>
<!--============================= FOOTER =============================-->
<footer class="main-block dark-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="copyright">
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    <p>Copyright &copy; 2018 Listing. All rights reserved | This template is made with <i
                                class="ti-heart" aria-hidden="true"></i> by <a href="https://colorlib.com"
                                                                               target="_blank">Colorlib</a>
                    </p>
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    <ul>
                        <li><a href="#"><span class="ti-facebook"></span></a></li>
                        <li><a href="#"><span class="ti-twitter-alt"></span></a></li>
                        <li><a href="#"><span class="ti-instagram"></span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--//END FOOTER -->


<!-- jQuery, Bootstrap JS. -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!-- Magnific popup JS -->
<script src="js/jquery.magnific-popup.js"></script>
<!-- Swipper Slider JS -->
<script src="js/swiper.min.js"></script>
</body>

</html>
