<?php
/**
 * header.inc - the top of every page: <head> (Bootstrap, custom CSS, Google Fonts,
 * Material Icons, favicon), the opening <body> and the flash-message display.
 * The including page sets $page_title before including this file.
 */
$page_title = isset($page_title) ? $page_title : 'BookVerse';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BookVerse - an online marketplace for buying and selling books.">
    <title><?= e($page_title) ?> | BookVerse</title>
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:wght@400;500;600;700&amp;family=Righteous&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<a class="visually-hidden-focusable skip-link" href="#main">Skip to main content</a>
<?php display_flash(); /* flash messages are shown once, then cleared from the session (CSS 'order' places them under the navbar) */ ?>
