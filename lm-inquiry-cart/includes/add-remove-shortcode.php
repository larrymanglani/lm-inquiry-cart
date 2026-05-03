<?php
/**
 * Shortcode: [lm_add_to_inquiry]
 * Adds a button to include/exclude a product in the inquiry cart
 *
 * Usage: [lm_add_to_inquiry title="Product Name" price="$99"]
 */
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register the shortcode
add_shortcode('lm_add_to_inquiry', 'lm_ic_add_remove_shortcode');

function lm_ic_add_remove_shortcode($atts) {
    // Define default attributes and merge with user input
    $atts = shortcode_atts(
        array(
            'title' => 'Untitled Item',
            'price' => '',
            'sku'   => '',
            'class' => '',
        ),
        $atts,
        'lm_add_to_inquiry'
    );

    // Sanitize output values (security: prevent XSS)
    $title = esc_html($atts['title']);
    $price = !empty($atts['price']) ? ' <span class="lm-ic-price">' . esc_html($atts['price']) . '</span>' : '';
    $sku   = !empty($atts['sku']) ? esc_attr($atts['sku']) : '';
    $extra_class = sanitize_html_class($atts['class']);

    // Create a unique item ID for the cookie (safe for JS)
    $item_id = !empty($sku) ? $sku : sanitize_title($title);

    // Build the button HTML
    // Note: JavaScript will check the cookie and toggle the button state on page load
    $output = sprintf(
        '<span class="lm-ic-button-wrap %s">
            <button
                type="button"
                class="lm-ic-toggle-btn button %s"
                data-item-id="%s"
                data-item-title="%s"
                data-item-price="%s"
                aria-label="%s">
                <span class="lm-ic-btn-text">Add to inquiry</span>
            </button>
            <span class="lm-ic-status" aria-live="polite"></span>
        </span>',
        esc_attr($extra_class),
        esc_attr($extra_class),
        esc_attr($item_id),
        esc_attr($title),
        esc_attr($atts['price']),
        esc_attr__('Toggle item in inquiry cart', 'lm-inquiry-cart')
    );

    return $output;
}

/**
 * AJAX fallback handler (if JavaScript is disabled)
 * This is a progressive enhancement - the JS version is preferred.
 */
add_action('wp_ajax_lm_ic_ajax_toggle', 'lm_ic_ajax_toggle_item');
add_action('wp_ajax_nopriv_lm_ic_ajax_toggle', 'lm_ic_ajax_toggle_item');

function lm_ic_ajax_toggle_item() {
    // Verify security nonce
    check_ajax_referer('lm_ic_cart_nonce', 'nonce');

    // Get and sanitize input
    $item_id    = sanitize_text_field($_POST['item_id']);
    $item_title = sanitize_text_field($_POST['item_title']);
    $action     = $_POST['action_type'] === 'add' ? 'add' : 'remove';

    // Get current cart from cookie (sent by JS)
    $cart_cookie = isset($_COOKIE['lm_ic_cart']) ? json_decode(stripslashes($_COOKIE['lm_ic_cart']), true) : array();
    
    if (!is_array($cart_cookie)) {
        $cart_cookie = array();
    }

    // Update cart
    if ($action === 'add') {
        $cart_cookie[$item_id] = array(
            'title' => $item_title,
            'price' => isset($_POST['item_price']) ? sanitize_text_field($_POST['item_price']) : '',
            'added' => current_time('mysql')
        );
        $message = __('Added to inquiry cart', 'lm-inquiry-cart');
    } else {
        unset($cart_cookie[$item_id]);
        $message = __('Removed from inquiry cart', 'lm-inquiry-cart');
    }

    // 🔹 FIXED: Standardized cookie settings to prevent duplicates
    // Set cookie with explicit path=/ and empty domain (current domain only)
    setcookie('lm_ic_cart', wp_json_encode($cart_cookie), array(
        'expires'  => time() + DAY_IN_SECONDS,
        'path'     => '/',
        'domain'   => '',  // Empty = current domain only (no subdomain issues)
        'secure'   => is_ssl(),
        'httponly' => false,
        'samesite' => 'Lax'
    ));

    // Return JSON response
    wp_send_json_success(array(
        'message'    => $message,
        'cart_count' => count($cart_cookie)
    ));
}