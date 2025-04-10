<?php
/**
 * Plugin Name: PPP Extension
 * Description: Extends the Public Post Preview plugin with custom functionality.
 * Version: 1.0.2
 * Author: Louie Sonugan
 * Author URI: https://louiesonugan.com/
 * License: GPLv2 or later
 */

// Add settings menu in the WordPress admin panel

if ( ! function_exists( 'pppex_add_admin_menu' ) ) {

    function pppex_add_admin_menu() {
        add_options_page(
            'PPP Extension Settings',
            'PPP Extension',
            'manage_options',
            'pppex-expiration',
            'pppex_settings_page'
        );
    }

}

add_action( 'admin_menu', 'pppex_add_admin_menu' );

// Create the settings page

if ( ! function_exists( 'pppex_settings_page' )) {

    function pppex_settings_page() {
        ?>
        <div class="wrap">
            <h1>PPP Extension Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'pppex_options' );
                do_settings_sections( 'pppex-expiration' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

}

// Register settings

if ( ! function_exists( 'pppex_settings_init' ) ) {

    function pppex_settings_init() {
        register_setting( 'pppex_options', 'pppex_expiration_time', 'pppex_sanitize' );

        add_settings_section(
            'pppex_expiration_section',
            'Expiration Time Settings for Public Post Preview',
            '__return_false',
            'pppex-expiration'
        );

        add_settings_field(
            'pppex_expiration_time',
            'Set Expiration Time (in minutes)',
            'pppex_expiration_time_field',
            'pppex-expiration',
            'pppex_expiration_section'
        );
    }

}

add_action( 'admin_init', 'pppex_settings_init' );

// Secure the input before saving

if ( ! function_exists( 'pppex_sanitize' ) ) {

    function pppex_sanitize( $input ) {
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

if ( ! function_exists( 'pppex_expiration_time_field' ) ) {

    function pppex_expiration_time_field() {
        $value = get_option( 'pppex_expiration_time', 30 ); // Default to 30 minutes
        echo '<input type="number" name="pppex_expiration_time" value="' . esc_attr( $value ) . '" min="1" max="43200" step="1"> minute(s)';
    }

}

// Modify nonce expiration dynamically

if ( ! function_exists( 'pppex_nonce_life' ) ) {

    function pppex_nonce_life( $nonce_life ) {
        $custom_expiration = get_option( 'pppex_expiration_time', 1800 ); // Default to 30 minutes
        return (int) $custom_expiration * 60;
    }

}

add_filter( 'ppp_nonce_life', 'pppex_nonce_life' );

?>