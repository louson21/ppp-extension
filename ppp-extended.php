<?php
/**
 * Plugin Name: PPP Extended
 * Description: Extends the Public Post Preview plugin with custom functionality.
 * Version: 1.0.2
 * Author: Louie Sonugan
 * Author URI: https://louiesonugan.com/
 * License: GPLv2 or later
 */

// Add settings menu in the WordPress admin panel

if ( ! function_exists( 'publpopr_add_admin_menu' ) ) {

    function publpopr_add_admin_menu() {
        add_options_page(
            'PPP Extended Settings',
            'PPP Extended',
            'manage_options',
            'ppp-expiration',
            'publpopr_settings_page'
        );
    }

}

add_action( 'admin_menu', 'publpopr_add_admin_menu' );

// Create the settings page

if ( ! function_exists( 'publpopr_settings_page' )) {

    function publpopr_settings_page() {
        ?>
        <div class="wrap">
            <h1>Public Post Preview Expiration Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'publpopr_expiration_options' );
                do_settings_sections( 'ppp-expiration' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

}

// Register settings

if ( ! function_exists( 'publpopr_settings_init' ) ) {

    function publpopr_settings_init() {
        register_setting( 'publpopr_expiration_options', 'publpopr_expiration_time', 'publpopr_sanitize' );

        add_settings_section(
            'publpopr_expiration_section',
            'Public Post Preview Expiration Settings',
            '__return_false',
            'ppp-expiration'
        );

        add_settings_field(
            'publpopr_expiration_time',
            'Set Expiration Time (in minutes)',
            'publpopr_time_field',
            'ppp-expiration',
            'publpopr_expiration_section'
        );
    }

}

add_action( 'admin_init', 'publpopr_settings_init' );

// Secure the input before saving

if ( ! function_exists( 'publpopr_sanitize' ) ) {

    function publpopr_sanitize( $input ) {
        $input = intval( $input ); // Convert to an integer
        if ( $input < 1 ) {
            $input = 1; // Minimum 1 minute
        } elseif ( $input > 43200 ) {
            $input = 43200; //maximum 30 days
        }
        return $input;
    }

}

// Field for expiration time

if ( ! function_exists( 'publpopr_time_field' ) ) {

    function publpopr_time_field() {
        $value = get_option( 'publpopr_expiration_time', 30 ); // Default to 30 minutes
        echo '<input type="number" name="publpopr_expiration_time" value="' . esc_attr( $value ) . '" min="1" max="43200" step="1"> minute(s)';
    }

}

// Modify nonce expiration dynamically

if ( ! function_exists( 'publpopr_nonce_life' ) ) {

    function publpopr_nonce_life( $nonce_life ) {
        $custom_expiration = get_option( 'publpopr_expiration_time', 1800 ); // Default to 30 minutes
        return (int) $custom_expiration * 60;
    }

}

add_filter( 'ppp_nonce_life', 'publpopr_nonce_life' );

?>