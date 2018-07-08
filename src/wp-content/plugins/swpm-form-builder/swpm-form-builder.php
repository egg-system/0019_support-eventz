<?php
/*
Plugin Name: Simple Membership Form Builder
Description: Simple Membership Addon to Dynamically Build Registration and Edit Profile Forms.
Plugin URI: https://simple-membership-plugin.com/simple-membership-form-builder-addon/
Author: wp.insider
Author URI: https://simple-membership-plugin.com/
Version: 4.6.0
*/

/* 
 * Helpful pointers for dev
 * 1) The 'includes/form-output.php' file outputs and renders the front-end form and fields.
 * 2) The 'classes/class.swpm-fb-form.php file has a save() function that saves the data to the DB when rego or profile forms are submitted on the front-end.
 * 3) Front-end form submission (both rego and edit profile) is handled using the following sequence:
 * class.swpm-form-builder.php => Swpm_Form_Builder -> init() -> process_submitted_form() -> validate_and_save() -> save() -> parent::save() -> confirmation_redirect();
 * 4) Profile updated message shown by the following:
 * class.swpm-form-builder.php => Swpm_Form_Builder -> profile_override() -> confirmation_text();
 * 5) See save_add_new_form() for after a new form is created in admin.
 */

define('SWPMFB_VERSION', '4.6.0' );
define('SWPMFB_SCRIPT_DEBUG', true);
define('SWPM_FORM_BUILDER_PATH', dirname(__FILE__) . '/');
define('SWPM_FORM_BUILDER_URL', plugins_url('',__FILE__));

require_once('classes/class.swpm-form-builder.php');
require_once('classes/class.swpm-fb-installer.php');
require_once('classes/class.swpm-fb-utils.php');
require_once('classes/class.swpm-fb-utils-custom-fields.php');
require_once('classes/class.swpm-fb-settings.php');

add_action('plugins_loaded', 'swpm_load_form_builder');
register_activation_hook( SWPM_FORM_BUILDER_PATH .'swpm-form-builder.php', 'SwpmFbInstaller::activate' );

function swpm_load_form_builder(){
    new Swpm_Form_Builder();
}
