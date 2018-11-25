<?php include('functions.php')?>
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
    <title>Listing &amp; Directory Website Template</title>
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
                    <a class="navbar-brand" href="index.php">Browse</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-menu"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                        <ul class="navbar-nav">
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Browse</a>
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
<!--============================= DETAIL =============================-->
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7 responsive-wrap">
                <div class="row detail-filter-wrap">
                    <div class="col-md-4 featured-responsive">
                        <div class="detail-filter-text">
                            <p>34 Results For <span>Restaurant</span></p>
                        </div>
                    </div>
                    <div class="col-md-8 featured-responsive">
                        <div class="detail-filter">
                            <p>Filter by</p>
                            <form class="filter-dropdown">
                                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect">
                                    <option selected>Best Match</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </form>
                            <form class="filter-dropdown">
                                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect1">
                                    <option selected>Restaurants</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </form>
                            <div class="map-responsive-wrap">
                                <a class="map-icon" href="#"><span class="icon-location-pin"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row detail-checkbox-wrap">
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">

                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Bike Parking</span>
                        </label>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Wireless Internet  </span>
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">

                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Smoking Allowed  </span>
                        </label>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Street Parking</span>
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">

                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Special</span>
                        </label>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Accepts Credit cards</span>
                        </label>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">

                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Pets Friendly</span>
                        </label>

                    </div>
                </div>
                <div class="row light-bg detail-options-wrap">
                    <?php generate_restaurants()?>
                </div>
            </div>
        </div>
    </div>
</section>
<!--//END DETAIL -->
<!--============================= FOOTER =============================-->
<footer class="main-block dark-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="copyright">
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    <p>Copyright &copy; 2018 Listing. All rights reserved | This template is made with <i
                                class="ti-heart" aria-hidden="true"></i> by <a href="https://colorlib.com"
                                                                               target="_blank">Colorlib</a></p>
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
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

</body>

</html>
