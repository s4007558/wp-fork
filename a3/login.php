<?php
/**
 * login.php - sign in. The form posts to this same page.
 * Steps: find the user (prepared statement) -> password_verify() against the stored hash
 *        -> keep user_id + username in $_SESSION -> flash message -> home page.
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

if (isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) (isset($_POST['username']) ? $_POST['username'] : ''));
    $password = (string) (isset($_POST['password']) ? $_POST['password'] : '');

    if ($username === '' || $password === '') {
        set_flash('danger', 'Please enter your username and password.');
    } else {
        // The user can type either the username or the email address
        $stmt = mysqli_prepare($conn,
            "SELECT user_id, username, password FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        // password_verify() compares the typed password with the stored bcrypt hash
        if ($user && password_verify($password, $user['password'])) {
            log_in_user($user['user_id'], $user['username']);
            set_flash('success', 'Logged in successfully. Welcome back, ' . $user['username'] . '!');
            redirect('index.php');
        }

        // The same message for "unknown user" and "wrong password" - do not reveal which
        set_flash('danger', 'Invalid username or password.');
    }
}

$page_title = 'Login';
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <div class="container page-section">
        <section class="panel-dark auth-card" aria-labelledby="loginHeading">
            <h1 id="loginHeading"><span class="material-icons" aria-hidden="true">login</span> Login to BookVerse</h1>

            <form method="post" action="login.php" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                           placeholder="Enter your username" value="<?= e($username) ?>"
                           required maxlength="100" autocomplete="username">
                    <div class="invalid-feedback">Please enter your username.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Enter your password" required autocomplete="current-password">
                    <div class="invalid-feedback">Please enter your password.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <span class="material-icons" aria-hidden="true">check_circle</span> Login
                </button>
            </form>

            <p class="auth-switch">Don't have an account? <a href="register.php">Register here</a></p>
        </section>
    </div>
</main>
<?php include 'includes/footer.inc'; ?>
