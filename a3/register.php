<?php
/**
 * register.php - create an account. The form posts to this same page.
 * Steps: validate -> are username/email free? -> password_hash() -> INSERT (prepared)
 *        -> log the new user in straight away -> flash message -> home page.
 */
require_once 'includes/db_connect.inc';
require_once 'includes/functions.inc';

// Logged-in users do not need this page
if (isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) (isset($_POST['username']) ? $_POST['username'] : ''));
    $email = trim((string) (isset($_POST['email']) ? $_POST['email'] : ''));
    $password = (string) (isset($_POST['password']) ? $_POST['password'] : '');
    $confirm = isset($_POST['confirm_password']) ? (string) $_POST['confirm_password'] : null;

    // ---- Server-side validation (the browser checks only help the user) ----
    $errors = array();
    if (!preg_match('/^[A-Za-z0-9_.\-]{3,50}$/', $username)) {
        $errors[] = 'Username must be 3-50 characters: letters, numbers, underscore, dot or hyphen.';
    }
    if ($email === '' || strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6 || strlen($password) > 72) {
        $errors[] = 'Password must be 6-72 characters long.';
    }
    if ($confirm !== null && $confirm !== $password) {
        $errors[] = 'The passwords do not match.';
    }

    // ---- Is the username or email already used? ----
    if (!$errors) {
        $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
        mysqli_stmt_execute($stmt);
        $taken = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($taken) > 0) {
            $errors[] = 'That username or email is already registered.';
        }
        mysqli_stmt_close($stmt);
    }

    // ---- Create the account ----
    if (!$errors) {
        // BCRYPT always produces a 60-character hash, which fits the CHAR(60) password column
        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $hash);
            mysqli_stmt_execute($stmt);
            $new_user_id = mysqli_insert_id($conn);          // user_id is AUTO_INCREMENT
            mysqli_stmt_close($stmt);

            log_in_user($new_user_id, $username);              // automatic login after registration
            set_flash('success', 'Welcome to BookVerse, ' . $username . '! Your account has been created.');
            redirect('index.php');
        } catch (mysqli_sql_exception $ex) {
            // e.g. two people registering the same name at the same moment (UNIQUE key)
            error_log('register.php insert failed: ' . $ex->getMessage());
            $errors[] = 'The account could not be created. The username or email may already be in use.';
        }
    }

    set_flash('danger', implode("\n", $errors));
}

$page_title = 'Register';
include 'includes/header.inc';
include 'includes/nav.inc';
?>
<main id="main">
    <div class="container page-section">
        <section class="panel-dark auth-card" aria-labelledby="registerHeading">
            <h1 id="registerHeading"><span class="material-icons" aria-hidden="true">person_add</span> Create Account</h1>

            <form method="post" action="register.php" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                           placeholder="Choose a username" value="<?= e($username) ?>"
                           required minlength="3" maxlength="50" pattern="[A-Za-z0-9_.\-]+" autocomplete="username">
                    <div class="invalid-feedback">Use 3-50 letters, numbers, underscores, dots or hyphens.</div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="Enter your email" value="<?= e($email) ?>"
                           required maxlength="100" autocomplete="email">
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Enter password" required minlength="6" maxlength="72" autocomplete="new-password">
                    <div class="invalid-feedback">The password needs at least 6 characters.</div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                           placeholder="Confirm password" required minlength="6" maxlength="72" autocomplete="new-password">
                    <div class="invalid-feedback" id="confirmFeedback">The passwords must match.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <span class="material-icons" aria-hidden="true">check_circle</span> Register
                </button>
            </form>

            <p class="auth-switch">Already have an account? <a href="login.php">Login here</a></p>
        </section>
    </div>
</main>
<?php include 'includes/footer.inc'; ?>
