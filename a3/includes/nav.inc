<?php
/**
 * nav.inc - the site header with the responsive Bootstrap navbar.
 * What is shown depends on whether the visitor is logged in.
 */
$current_page = basename($_SERVER['SCRIPT_NAME']);
$nav_active = function ($file) use ($current_page) {
    return $current_page === $file ? ' active" aria-current="page' : '';
};
?>
<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark site-nav" aria-label="Main navigation">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/logo.svg" alt="" width="30" height="30">BookVerse
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link<?= $nav_active('index.php') ?>" href="index.php"><span class="material-icons" aria-hidden="true">home</span> Home</a></li>
                    <li class="nav-item"><a class="nav-link<?= $nav_active('books.php') ?>" href="books.php"><span class="material-icons" aria-hidden="true">list</span> Browse Books</a></li>
                    <li class="nav-item"><a class="nav-link<?= $nav_active('gallery.php') ?>" href="gallery.php"><span class="material-icons" aria-hidden="true">photo_library</span> Gallery</a></li>
                    <li class="nav-item"><a class="nav-link<?= $nav_active('search.php') ?>" href="search.php"><span class="material-icons" aria-hidden="true">search</span> Search</a></li>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item"><a class="nav-link<?= $nav_active('add.php') ?>" href="add.php"><span class="material-icons" aria-hidden="true">add_circle</span> Add Book</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="material-icons" aria-hidden="true">account_circle</span> <?= e($_SESSION['username']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="seller.php?id=<?= (int) $_SESSION['user_id'] ?>">My Profile &amp; Books</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link<?= $nav_active('login.php') ?>" href="login.php"><span class="material-icons" aria-hidden="true">login</span> Login</a></li>
                        <li class="nav-item"><a class="nav-link<?= $nav_active('register.php') ?>" href="register.php"><span class="material-icons" aria-hidden="true">person_add</span> Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
