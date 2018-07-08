<?php
/**
 * Description of class
 *
 * @author nur
 */
class SwpmFbInstaller {
    static function activate() {
        global $wpdb;

        $field_table = $wpdb->prefix . 'swpm_form_builder_fields';
        $form_table = $wpdb->prefix . 'swpm_form_builder_forms';
        $custom_table = $wpdb->prefix . 'swpm_form_builder_custom';

        // Explicitly set the character set and collation when creating the tables
        $charset = ( defined('DB_CHARSET' && '' !== DB_CHARSET) ) ? DB_CHARSET : 'utf8';
        $collate = ( defined('DB_COLLATE' && '' !== DB_COLLATE) ) ? DB_COLLATE : 'utf8_general_ci';

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $field_sql = "CREATE TABLE $field_table (
				field_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				form_id BIGINT(20) NOT NULL,
				field_key VARCHAR(255) NOT NULL,
				field_type VARCHAR(25) NOT NULL,
				field_options TEXT,
				field_description TEXT,
				field_name TEXT NOT NULL,
				field_sequence BIGINT(20) DEFAULT '0',
				field_parent BIGINT(20) DEFAULT '0',
				field_validation VARCHAR(25),
				field_required VARCHAR(25),
				field_size VARCHAR(25) DEFAULT 'medium',
				field_css VARCHAR(255),
				field_layout VARCHAR(255),
				field_default TEXT,
                field_adminonly TINYINT DEFAULT 0,
                field_readonly TINYINT DEFAULT 0,
                reg_field_id BIGINT(20) NOT NULL DEFAULT 0,
				PRIMARY KEY  (field_id)
			) DEFAULT CHARACTER SET $charset COLLATE $collate;";

        $form_sql = "CREATE TABLE $form_table (
				form_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				form_key TINYTEXT NOT NULL,
				form_title TEXT NOT NULL,
                                form_type TINYINT DEFAULT 0,
                                form_membership_level INT DEFAULT 0,
				form_success_type VARCHAR(25) DEFAULT 'text',
				form_success_message TEXT,
				form_notification_setting VARCHAR(25),
				form_notification_email_name VARCHAR(255),
				form_notification_subject VARCHAR(255),
				form_notification_message TEXT,
				form_label_alignment VARCHAR(25),
                UNIQUE KEY form_unique_key_id (form_type,form_membership_level),
				PRIMARY KEY  (form_id)
			) DEFAULT CHARACTER SET $charset COLLATE $collate;";

        $custom_sql = "CREATE TABLE $custom_table (
				value_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				field_id BIGINT(20) NOT NULL,
                user_id INT NOT NULL,
				value TEXT,
				PRIMARY KEY  (value_id)
			) DEFAULT CHARACTER SET $charset COLLATE $collate;";

        // Create or Update database tables
        dbDelta($field_sql);
        dbDelta($form_sql);
        dbDelta($custom_sql);
    }
}
