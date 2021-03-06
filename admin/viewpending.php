<?php
include('../functions.php');
if (!isAdmin()) {
    $_SESSION['msg'] = "You must be an admin to access this page";
    header('location: ../index.php');
}
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
                                    </div>
                                </li>
                                <?php if (isAdmin()) : ?>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="../admin/create_user.php" style="color: red;">Create
                                            User</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="../admin/viewpending.php" style="color: red;">View
                                            Pending Restaurants</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="employee.php" style="color: red;">View
                                            Pending Orders</a>
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
<!--============================= DETAIL =============================-->
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7 responsive-wrap">
                <div class="row detail-filter-wrap">
                    <div class="col-md-4 featured-responsive">
                        <div class="detail-filter-text">
                            <?php if (!(isset($_GET['search']))) : ?>
                                <?php
                                global $db;
                                $query = "SELECT COUNT(*)
                                          FROM restaurant
                                          WHERE pending = '1'";
                                $query = mysqli_query($db, $query);
                                $query = mysqli_fetch_array($query);
                                echo "<p>$query[0] Results For <span>Pending Restaurants</span></p>";
                                ?>
                            <?php else: ?>
                                <?php
                                global $temp;
                                $temp = $_GET['search'];
                                $query = "SELECT COUNT(*) FROM restaurant WHERE name LIKE '%" . $temp . "%'";
                                $query = mysqli_query($db, $query);
                                $query = mysqli_fetch_array($query);
                                echo "<p>$query[0] Results For <span>{$_GET['search']}</span></p>";
                                ?>
                            <?php endif ?>
                        </div>
                        <form action="../search.php" method="GET">
                            <input type="text" placeholder="Find Users" class="btn-group1" name="search"/>
                            <input type="submit" class="btn" value="Search"/>
                        </form>
                    </div>
                    <div class="col-md-8 featured-responsive">
                        <div class="detail-filter">
                            <p>Filter by</p>
                            <form class="filter-dropdown" action="viewpending.php" method="get" id="filter-dropdown">
                                <select name="filter_by" class="custom-select mb-2 mr-sm-2 mb-sm-0"
                                        id="inlineFormCustomSelect" onchange="this.form.submit()">
                                    <option disabled selected value> -- select an option --</option>
                                    <option value="cost" <?= isset($_GET['filter_by']) && $_GET['filter_by'] == 'cost' ? ' selected="selected"' : ''; ?>>
                                        Cost
                                    </option>
                                    <option value="rating" <?= isset($_GET['filter_by']) && $_GET['filter_by'] == 'rating' ? ' selected="selected"' : ''; ?>>
                                        Rating
                                    </option>
                                    <option value="open" <?= isset($_GET['filter_by']) && $_GET['filter_by'] == 'open' ? ' selected="selected"' : ''; ?>>
                                        Open Now
                                    </option>
                                    <option value="category" <?= isset($_GET['filter_by']) && $_GET['filter_by'] == 'category' ? ' selected="selected"' : ''; ?>>
                                        Category
                                    </option>
                                </select>
                                <select name="order_by" class="custom-select mb-2 mr-sm-2 mb-sm-0"
                                        id="inlineFormCustomSelect" onchange="this.form.submit()">
                                    <option disabled selected value></option>
                                    <?php if (isset($_GET['filter_by']) && ($_GET['filter_by'] == 'cost'
                                            || $_GET['filter_by'] == 'rating')) : ?>
                                        <option value="ASC" <?= ($_GET['filter_by'] == 'cost' && !isset($_GET['order_by']))
                                        || (isset($_GET['order_by']) && $_GET['order_by'] == 'ASC') ? ' selected="selected"' : ''; ?>>
                                            Ascending
                                        </option>
                                        <option value="DESC" <?= ($_GET['filter_by'] == 'rating' && !isset($_GET['order_by']))
                                        || (isset($_GET['order_by']) && $_GET['order_by'] == 'DESC') ? ' selected="selected"' : ''; ?>>
                                            Descending
                                        </option>
                                    <?php endif ?>
                                    <?php if (isset($_GET['filter_by']) && ($_GET['filter_by'] == 'category')) {
                                        category_filter();
                                    } ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row detail-checkbox-wrap">
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" form="filter-dropdown" name="wifi"
                                   onchange="this.form.submit()" <?php echo(isset($_GET['wifi']) && $_GET['wifi'] == 'on' ? 'checked' : ''); ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Wifi</span>
                        </label>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" form="filter-dropdown" name="delivery"
                                   onchange="this.form.submit()" <?php echo(isset($_GET['delivery']) && $_GET['delivery'] == 'on' ? 'checked' : ''); ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Delivery</span>
                        </label>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" form="filter-dropdown" name="alcohol"
                                   onchange="this.form.submit()" <?php echo(isset($_GET['alcohol']) && $_GET['alcohol'] == 'on' ? 'checked' : ''); ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Alcohol</span>
                        </label>
                    </div>
                </div>
                <div class="row light-bg detail-options-wrap">
                    <?php if (!(isset($_GET['search']))) : ?>
                        <?php
                        if (isset($_GET['filter_by']) && isset($_GET['order_by'])) {
                            generate_restaurants(NULL, true, false, $_GET['filter_by'], $_GET['order_by']);
                        } else if (isset($_GET['filter_by'])) {
                            generate_restaurants(NULL, true, false, $_GET['filter_by']);
                        } else {
                            generate_restaurants(NULL, true, false);
                        }
                        ?>
                    <?php else: ?>
                        <?php generate_restaurants($temp, true, false) ?>
                    <?php endif ?>
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
<script src="../js/jquery-3.2.1.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>

</body>

</html>
