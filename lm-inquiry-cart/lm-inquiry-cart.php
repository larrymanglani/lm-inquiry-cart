<?php
/**
 * Plugin Name: Larry Manglani's Inquiry Cart
 * Plugin URI: https://wordpress.org/plugins/lm-inquiry-cart/
 * Author: Larry Manglani
 * Author URI: https://larrymanglani.com
 * Version: 1.0.0
 * Description: Add a secure inquiry cart to your website that allows users to ask questions about posts or products.
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: lm-inquiry-cart
 */

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin activation: Set default options
 */
register_activation_hook(__FILE__, 'lm_ic_activate');
function lm_ic_activate() {
    $defaults = array(
        'email_to'      => get_option('admin_email'),
        'email_subject' => get_bloginfo('name') . ' - New Inquiry',
        'msg_fail'      => '<p>Sorry, your inquiry could not be sent. Please try again.</p>',
        'msg_sent'      => '<p>Thank you! Your inquiry has been sent.</p>',
    );
    if (!get_option('lm_ic_options')) {
        add_option('lm_ic_options', $defaults);
    }
}

/**
 * Plugin deactivation: Optional cleanup
 */
register_deactivation_hook(__FILE__, 'lm_ic_deactivate');
function lm_ic_deactivate() {
    // Keep options on deactivate; use uninstall.php for full cleanup
}

/**
 * Load plugin text domain for translations
 */
add_action('plugins_loaded', 'lm_ic_load_textdomain');
function lm_ic_load_textdomain() {
    load_plugin_textdomain('lm-inquiry-cart', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

/**
 * Add Settings Menu Page
 */
add_action('admin_menu', 'lm_ic_add_admin_menu');
function lm_ic_add_admin_menu() {
    add_options_page(
        __('Inquiry Cart Settings', 'lm-inquiry-cart'),
        __('Inquiry Cart', 'lm-inquiry-cart'),
        'manage_options',
        'lm-inquiry-cart-settings',
        'lm_ic_render_settings_page'
    );
}

/**
 * Render the Settings Page
 * Note: Security checks are done here. Saving is handled by the included template.
 */
function lm_ic_render_settings_page() {
    // 1. Check Permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'lm-inquiry-cart'));
    }

    // 2. Verify Nonce (if saving)
    if (isset($_POST['lm_ic_save_settings'])) {
        if (!isset($_POST['lm_ic_nonce']) || !wp_verify_nonce($_POST['lm_ic_nonce'], 'lm_ic_save_settings')) {
            wp_die(__('Security check failed.', 'lm-inquiry-cart'));
        }
    }

    // 3. Include the settings template
    $settings_file = plugin_dir_path(__FILE__) . 'includes/settings-page.php';
    if (file_exists($settings_file)) {
        include_once $settings_file;
    }
}

/**
 * Enqueue frontend scripts
 */
add_action('wp_enqueue_scripts', 'lm_ic_enqueue_frontend_assets');
function lm_ic_enqueue_frontend_assets() {
    wp_enqueue_script(
        'lm-ic-cart-js',
        plugin_dir_url(__FILE__) . 'assets/js/cart.js',
        array('jquery'),
        '1.0.0',
        true
    );

    $scripts_path = plugin_dir_path(__FILE__) . 'assets/js/scripts/';
    if (is_dir($scripts_path)) {
        $files = glob($scripts_path . '*.js');
        if ($files) {
            foreach ($files as $file) {
                $filename = basename($file);
                $handle = 'lm-ic-extra-' . sanitize_title($filename);
                wp_enqueue_script(
                    $handle,
                    plugin_dir_url(__FILE__) . 'assets/js/scripts/' . $filename,
                    array('jquery', 'lm-ic-cart-js'),
                    '1.0.0',
                    true
                );
            }
        }
    }

    wp_localize_script('lm-ic-cart-js', 'lmIcData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('lm_ic_cart_nonce'),
    ));
}

/**
 * Include shortcode handlers
 */
$add_remove_file = plugin_dir_path(__FILE__) . 'includes/add-remove-shortcode.php';
if (file_exists($add_remove_file)) {
    include_once $add_remove_file;
}

$cart_file = plugin_dir_path(__FILE__) . 'includes/inquiry-cart-shortcode.php';
if (file_exists($cart_file)) {
    include_once $cart_file;
}

// ✅ AJAX handler is loaded from includes/add-remove-shortcode.php