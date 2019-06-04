<?php

use MiniOrange\Helper\DB;
use MiniOrange\Helper\CustomerDetails as CD;

if (!isset($_SESSION) and isset($_REQUEST['option'])) {
    session_id("connector");
    session_start();
}

if (isset($_POST['option']) and $_POST['option'] == "mo_saml_register_customer") {
    mo_register_action();
}

if (isset($_POST['option']) and $_POST['option'] == "mo_saml_goto_login") {
    CD::delete_option('mo_saml_new_registration');
    CD::update_option('mo_saml_verify_customer', 'true');
}

if (isset($_POST['option']) and $_POST['option'] == "change_miniorange") {
    mo_saml_remove_account();
    DB::update_option('mo_saml_message', 'Logged out of miniOrange account');
    mo_saml_show_success_message();
    return;
}

if (isset($_POST['option']) and $_POST['option'] == "mo_saml_go_back") {
    mo_saml_remove_account();
}

if (isset($_POST['option']) and $_POST['option'] == "mo_saml_verify_customer") { // register the admin to miniOrange

    if (!mo_saml_is_curl_installed()) {
        CD::update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Login failed.');
        mo_saml_show_error_message();

        return;
    }

    $email = '';
    $password = '';
    if (mo_saml_check_empty_or_null($_POST['email']) || mo_saml_check_empty_or_null($_POST['password'])) {
        CD::update_option('mo_saml_message', 'All the fields are required. Please enter valid entries.');
        mo_saml_show_error_message();

        return;
    } else if (checkPasswordpattern(strip_tags($_POST['password']))) {
        CD::update_option('mo_saml_message', 'Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.');
        mo_saml_show_error_message();
        return;
    } else {
        $email = $_POST['email'];
        $password = stripslashes(strip_tags($_POST['password']));
    }

    CD::update_option('mo_saml_admin_email', $email);
    $customer = new CustomerSaml();
    $content = $customer->get_customer_key();
    $customerKey = json_decode($content, true);
    if (json_last_error() == JSON_ERROR_NONE) {
        CD::update_option('mo_saml_admin_customer_key', $customerKey['id']);
        CD::update_option('mo_saml_admin_api_key', $customerKey['apiKey']);
        CD::update_option('mo_saml_customer_token', $customerKey['token']);
        $certificate = DB::get_option('saml_x509_certificate');
        DB::update_option('mo_saml_message', 'Customer retrieved successfully');
        CD::update_option('mo_saml_registration_status', 'logged');
        mo_saml_show_success_message();
    } else {
        DB::update_option('mo_saml_message', 'Invalid username or password. Please try again.');
        mo_saml_show_error_message();
    }
}

if (isset($_POST['option']) && $_POST['option'] == 'mo_saml_verify_license') {

    if (mo_saml_check_empty_or_null($_POST['saml_license_key'])) {
        CD::update_option('mo_saml_message', 'All the fields are required. Please enter valid license key.');
        mo_saml_show_error_message();
        return;
    }

    $code = trim($_POST['saml_license_key']);
    $customer = new CustomerSaml();
    $content = json_decode($customer->check_customer_ln(), true);
    if (strcasecmp($content['status'], 'SUCCESS') == 0) {
        $content = json_decode($customer->mo_saml_vl($code, false), true);
        CD::update_option('vl_check_t', time());
        if (strcasecmp($content['status'], 'SUCCESS') == 0) {
            $key = CD::get_option('mo_saml_customer_token');
            CD::update_option('sml_lk', AESEncryption::encrypt_data($code, $key));
            CD::update_option('mo_saml_message', 'Your license is verified. You can now configure the connector.');
            $key = CD::get_option('mo_saml_customer_token');
            CD::update_option('site_ck_l', AESEncryption::encrypt_data("true", $key));
            CD::update_option('t_site_status', AESEncryption::encrypt_data("false", $key));
            mo_saml_show_success_message();
        } else if (strcasecmp($content['status'], 'FAILED') == 0) {
            if (strcasecmp($content['message'], 'Code has Expired') == 0) {
                CD::update_option('mo_saml_message', 'License key you have entered has already been used. Please enter a key which has not been used before on any other instance or if you have exhausted all your keys, then buy more.');
            } else {
                CD::update_option('mo_saml_message', 'You have entered an invalid license key. Please enter a valid license key.');
            }
            mo_saml_show_error_message();
        } else {
            CD::update_option('mo_saml_message', 'An error occured while processing your request. Please Try again.');
            mo_saml_show_error_message();
        }
    } else {
        $key = CD::get_option('mo_saml_customer_token');
        CD::update_option('site_ck_l', AESEncryption::encrypt_data("false", $key));
        CD::update_option('mo_saml_message', 'You have not upgraded yet. ');
        mo_saml_show_error_message();
    }
}
?>
