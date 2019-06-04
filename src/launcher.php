<?php
/*
 * Plugin Name: miniOrange PHP SAML 2.0 Connector
 * Version: 11.0.0
 * Author: miniOrange
 */

if (!isset($_SESSION)) {
    session_id('connector');
    session_start();
}
// check if the directory containing CSS,JS,Resources exists in the root folder of the site
if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/miniorange/sso')) {
    // copy miniorange css,js,images,etc assets to root folder of laravel app
    $file_paths_array = array(
        '/includes',
        '/resources'
    );
    foreach ($file_paths_array as $path) {
        $src = __DIR__ . $path;
        $dst = $_SERVER['DOCUMENT_ROOT'] . "/miniorange/sso" . $path;
        recurse_copy($src, $dst);
    }
}
if (session_id() == 'connector' || session_id() == 'attributes') {
    if (is_user_registered() == NULL) {
        header("Location: register.php"); // https://www.google.com
        exit();
    } else {
        header("Location: admin_login.php");
        exit();
    }
}

?>