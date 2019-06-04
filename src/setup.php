<?php

use MiniOrange\Helper\DB;

if (!isset($_SESSION)) {
    session_id("connector");
    session_start();
}

if (isset($_SESSION['authorized']) && !empty($_SESSION['authorized'])) {
    if ($_SESSION['authorized'] != true) {
        header('Location: admin_login.php');
        exit();
    }
}

if (isset($_POST['option']) && $_POST['option'] == 'save_connector_settings') {
    $saml_identity_name = '';
    $idp_entity_id = '';
    $saml_login_url = '';
    $saml_login_binding_type = '';
    $saml_logout_url = '';
    $saml_x509_certificate = '';
    $force_authentication = '';
    $force_sso = '';
    $relaystate_url = '';
    $site_logout_url = '';
    $sp_base_url = '';
    $sp_entity_id = '';
    $acs_url = '';
    $single_logout_url = '';
    if (mo_saml_check_empty_or_null($_POST['idp_name']) || mo_saml_check_empty_or_null($_POST['saml_login_url']) || mo_saml_check_empty_or_null($_POST['idp_entity_id'])) {
        DB::update_option('mo_saml_message', 'All the fields are required. Please enter valid entries.');
        mo_saml_show_error_message();
        return;
    } else if (!preg_match("/^\w*$/", $_POST['idp_name'])) {
        DB::update_option('mo_saml_message', 'Please match the requested format for Identity Provider Name. Only alphabets, numbers and underscore is allowed.');
        mo_saml_show_error_message();
        return;
    } else {
        $saml_identity_name = trim($_POST['idp_name']);
        $saml_login_url = trim($_POST['saml_login_url']);
        if (array_key_exists('login_binding_type', $_POST))
            $saml_login_binding_type = $_POST['login_binding_type'];

        if (array_key_exists('saml_logout_url', $_POST)) {
            $saml_logout_url = trim($_POST['saml_logout_url']);
        }
        $idp_entity_id = trim($_POST['idp_entity_id']);
        $saml_x509_certificate = sanitize_certificate($_POST['x509_certificate']);
        if (isset($_POST['force_authn']) && !empty($_POST['force_authn'])) {
            $force_authentication = true;
        } else {
            $force_authentication = false;
        }
        if (isset($_POST['force_sso']) && !empty($_POST['force_sso'])) {
            $force_sso = true;
        } else {
            $force_sso = false;
        }

        $sp_base_url = trim($_POST['site_base_url']);
        $sp_entity_id = trim($_POST['sp_entity_id']);
        $acs_url = trim($_POST['acs_url']);
        $single_logout_url = trim($_POST['slo_url']);
        $relaystate_url = trim($_POST['relaystate_url']);
        $site_logout_url = trim($_POST['site_logout_url']);

        DB::update_option('saml_identity_name', $saml_identity_name);
        DB::update_option('idp_entity_id', $idp_entity_id);
        DB::update_option('saml_login_url', $saml_login_url);
        DB::update_option('saml_login_binding_type', $saml_login_binding_type);
        DB::update_option('saml_logout_url', $saml_logout_url);
        DB::update_option('saml_x509_certificate', $saml_x509_certificate);
        DB::update_option('force_authentication', $force_authentication);
        DB::update_option('force_sso', $force_sso);
        DB::update_option('sp_base_url', $sp_base_url);
        DB::update_option('sp_entity_id', $sp_entity_id);
        DB::update_option('acs_url', $acs_url);
        DB::update_option('single_logout_url', $single_logout_url);
        DB::update_option('relaystate_url', $relaystate_url);
        DB::update_option('site_logout_url', $site_logout_url);

        DB::update_option('mo_saml_message', 'Settings saved successfully.');
        mo_saml_show_success_message();
        if (empty($saml_x509_certificate)) {
            DB::update_option("mo_saml_message", 'Invalid Certificate:Please provide a certificate');
            mo_saml_show_error_message();
        }

        $saml_x509_certificate = sanitize_certificate($saml_x509_certificate);
        if (!@openssl_x509_read($saml_x509_certificate)) {
            DB::update_option('mo_saml_message', 'Invalid certificate: Please provide a valid certificate.');
            mo_saml_show_error_message();
            DB::delete_option('saml_x509_certificate');
        }
    }
}

if (isset($_POST['option']) && $_POST['option'] == 'attribute_mapping') {
    if (isset($_POST['saml_am_email']) && !empty($_POST['saml_am_email'])) {
        DB::update_option('saml_am_email', $_POST['saml_am_email']);
    } else {
        DB::update_option('saml_am_email', 'NameID');
    }
    if (isset($_POST['saml_am_username']) && !empty($_POST['saml_am_username'])) {
        DB::update_option('saml_am_username', $_POST['saml_am_username']);
    } else {
        DB::update_option('saml_am_username', 'NameID');
    }
    if (isset($_POST['attribute_name']) && isset($_POST['attribute_value'])) {
        $key = $_POST['attribute_name'];
        $value = $_POST['attribute_value'];
        $custom_attrs = array_combine($key, $value);
        $custom_attrs = array_filter($custom_attrs);
        $custom_attrs = serialize($custom_attrs);
        DB::update_option('mo_saml_custom_attrs_mapping', $custom_attrs);
    }
    DB::update_option('mo_saml_message', 'Attribute Mapping details saved successfully');
    mo_saml_show_success_message();
}
?>
    
