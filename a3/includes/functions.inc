<?php
/**
 * functions.inc - small helpers shared by every page:
 * sessions, escaping, flash messages, login helpers, CSRF tokens and image uploads.
 * (Compatible with PHP 7.4 and newer.)
 */

// ---- Session (started once, with safer cookie settings) -----------------------------
if (session_status() === PHP_SESSION_NONE) {
    // Coreteaching servers host many student sites on ONE domain, so give this site its
    // own cookie name and limit the cookie to this folder.
    $cookie_path = rtrim(str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/')), '/') . '/';
    session_name('bookverse_session');
    session_set_cookie_params(array(
        'lifetime' => 0,
        'path'     => $cookie_path,
        'httponly' => true,                // JavaScript cannot read the cookie
        'samesite' => 'Lax',               // basic protection against cross-site requests
        'secure'   => !empty($_SERVER['HTTPS']),
    ));
    session_start();
}

// ---- Settings used in several places ---------------------------------------------------
const COVER_DIR = __DIR__ . '/../assets/images/covers/';   // folder on disk
const COVER_URL = 'assets/images/covers/';                  // same folder as a web path
const PLACEHOLDER_IMG = 'assets/images/placeholder.svg';
const ALLOWED_EXTENSIONS = array('jpg', 'jpeg', 'png', 'gif', 'webp');
const ALLOWED_MIME_TYPES = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
const MAX_IMAGE_BYTES = 5242880;                            // 5 MB
const BOOK_CONDITIONS = array('New', 'Gently Used', 'Fair');
const BOOK_STATUSES = array('Available', 'Reserved', 'Sold');
const BOOK_GENRES = array(
    'Fiction', 'Non-Fiction', 'Science Fiction', 'Fantasy', 'Dystopian', 'Romance', 'Mystery',
    'Thriller', 'Horror', 'Memoir', 'Biography', 'History', 'Self-Help', 'Children', 'Other',
);

// ---- Escaping --------------------------------------------------------------------------
/** Escape ANY value before it is printed in HTML (protects against XSS). */
function e($value)
{
    return htmlspecialchars((string) ($value === null ? '' : $value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

// ---- Flash messages (one-time messages kept in $_SESSION) ----------------------------------
/** $type is a Bootstrap alert colour: success, danger, warning or info. */
function set_flash($type, $message)
{
    $_SESSION['flash'][] = array('type' => $type, 'message' => $message);
}

/** Print the pending flash messages once, then remove them from the session. */
function display_flash()
{
    if (empty($_SESSION['flash'])) {
        return;
    }
    $allowed = array('success', 'danger', 'warning', 'info');
    echo '<div class="flash-container container-fluid" id="flash">';
    foreach ($_SESSION['flash'] as $flash) {
        $type = in_array($flash['type'], $allowed, true) ? $flash['type'] : 'info';
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
        echo nl2br(e($flash['message']));
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    echo '</div>';
    unset($_SESSION['flash']);              // shown once, so a page reload no longer shows it
}

// ---- Login helpers ---------------------------------------------------------------------------
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/** Send anonymous visitors to the login page (used by protected pages). */
function require_login($message = 'Please log in to continue.')
{
    if (!isset($_SESSION['user_id'])) {
        set_flash('warning', $message);
        redirect('login.php');
    }
}

/** Store the user in the session (also used right after registration). */
function log_in_user($user_id, $username)
{
    session_regenerate_id(true);            // new session id = prevents session fixation
    $_SESSION['user_id'] = (int) $user_id;
    $_SESSION['username'] = $username;
}

// ---- CSRF protection (add / edit / delete forms) -------------------------------------------------
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** True when the submitted token matches the one stored in the session. */
function csrf_is_valid()
{
    $sent = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    return is_string($sent) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
}

// ---- Display helpers ----------------------------------------------------------------------------------
function status_class($status)
{
    $classes = array('Available' => 'status-available', 'Reserved' => 'status-reserved', 'Sold' => 'status-sold');
    return isset($classes[$status]) ? $classes[$status] : 'status-sold';
}

/** Web path of a cover image; shows a placeholder when the file is missing. */
function cover_url($file)
{
    $file = basename((string) $file);       // basename() blocks "../" tricks
    if ($file !== '' && is_file(COVER_DIR . $file)) {
        return COVER_URL . rawurlencode($file);
    }
    return PLACEHOLDER_IMG;
}

// ---- Image upload helpers --------------------------------------------------------------------------------
/** Was a file chosen in the upload field? */
function image_was_uploaded($field = 'image_path')
{
    return isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE;
}

/** Check an uploaded image. Returns an error message, or null when it is fine. */
function check_image_upload($field = 'image_path')
{
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'The image could not be uploaded (error code ' . (int) $file['error'] . ').';
    }
    if ($file['size'] > MAX_IMAGE_BYTES) {
        return 'The image must be smaller than 5 MB.';
    }
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS, true)) {
        return 'Only jpg, jpeg, png, gif and webp images are allowed.';
    }
    // Look at the real file content too - the extension alone can be faked
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (!in_array($finfo->file($file['tmp_name']), ALLOWED_MIME_TYPES, true) || @getimagesize($file['tmp_name']) === false) {
        return 'The selected file is not a valid image.';
    }
    return null;
}

/**
 * Save the uploaded image in assets/images/covers/ under a NEW server-generated name
 * (the original file name is never trusted or reused). Returns the new file name or null.
 */
function save_cover_image($field = 'image_path')
{
    $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!is_dir(COVER_DIR) && !mkdir(COVER_DIR, 0777, true)) {
        return null;
    }
    $new_name = 'cover_' . uniqid() . mt_rand(100, 999) . '.' . $extension;   // e.g. cover_65f1c2a9e3b4a512.jpg
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], COVER_DIR . $new_name)) {
        return null;
    }
    return $new_name;
}

/** Delete a cover file from disk (used when a book is edited or deleted). */
function delete_cover_image($file)
{
    $file = basename((string) $file);
    if ($file !== '' && is_file(COVER_DIR . $file)) {
        unlink(COVER_DIR . $file);
    }
}
