<?php
/**
 * logout.php - ends the session and returns to the home page with a message.
 * (No database needed, so db_connect.inc is not included.)
 */
require_once 'includes/functions.inc';      // starts the session

$_SESSION = array();                        // forget the user data
if (ini_get('session.use_cookies')) {       // delete the session cookie in the browser
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();                          // destroy the session on the server

// A brand-new session is needed to carry the "logged out" flash message
session_start();
session_regenerate_id(true);
set_flash('success', 'You have been logged out successfully.');
redirect('index.php');
