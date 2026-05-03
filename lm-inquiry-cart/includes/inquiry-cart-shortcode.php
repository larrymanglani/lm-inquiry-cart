<?php
/**
 * Shortcode: [lm_inquiry_cart]
 * Displays the inquiry cart form with selected items and handles submission
 *
 * Usage: [lm_inquiry_cart]
 */
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register the shortcode
add_shortcode('lm_inquiry_cart', 'lm_ic_render_cart_form');

/**
 * Main function: Render cart form or process submission
 */
function lm_ic_render_cart_form() {
    // Get plugin settings with fallback defaults
    $defaults = array(
        'email_to'      => get_option('admin_email'),
        'email_subject' => get_bloginfo('name') . ' - New Inquiry',
        'msg_sent'      => '<p>Thank you! Your inquiry has been sent.</p>',
        'msg_fail'      => '<p>Sorry, your inquiry could not be sent. Please try again.</p>',
    );
    $options = wp_parse_args(get_option('lm_ic_options', array()), $defaults);

    // Handle form submission
    if (isset($_POST['lm_ic_form_submitted']) && 'y' === $_POST['lm_ic_form_submitted']) {
        // Security: Verify nonce 🔐
        if (!isset($_POST['lm_ic_form_nonce']) || !wp_verify_nonce($_POST['lm_ic_form_nonce'], 'lm_ic_form_submit')) {
            return '<div class="lm-ic-error">' . __('Security check failed. Please try again.', 'lm-inquiry-cart') . '</div>';
        }

        // Sanitize form inputs 🧹
        $user_name    = sanitize_text_field($_POST['lm_ic_user_name']);
        $user_email   = sanitize_email($_POST['lm_ic_user_email']);
        $user_message = sanitize_textarea_field($_POST['lm_ic_user_message']);

        // Validate required fields ✅
        $errors = array();
        if (empty($user_name) || strlen($user_name) < 2) {
            $errors[] = __('Please enter your name.', 'lm-inquiry-cart');
        }
        if (empty($user_email) || !is_email($user_email)) {
            $errors[] = __('Please enter a valid email address.', 'lm-inquiry-cart');
        }
        if (empty($user_message) || strlen($user_message) < 10) {
            $errors[] = __('Please enter an inquiry with at least 10 characters.', 'lm-inquiry-cart');
        }

        // Get cart items from cookie (sent by JavaScript) 🛒
        $cart_items = array();
        if (isset($_COOKIE['lm_ic_cart'])) {
            $decoded = json_decode(stripslashes($_COOKIE['lm_ic_cart']), true);
            if (is_array($decoded) && !is_null($decoded)) {
                $cart_items = $decoded;
            }
        }

        // If errors exist, show them and re-display form
        if (!empty($errors)) {
            $error_html = '<div class="lm-ic-errors"><ul>';
            foreach ($errors as $error) {
                $error_html .= '<li>' . esc_html($error) . '</li>';
            }
            $error_html .= '</ul></div>';
            return $error_html . lm_ic_generate_form_html($options, $user_name, $user_email, $user_message, $cart_items);
        }

        // Prepare and send email ✉
        $email_to      = $options['email_to'];
        $email_subject = $options['email_subject'];
        
        // Format cart items for email
        $items_list = "";
        foreach ($cart_items as $item_id => $item_data) {
            $item_title = is_array($item_data) ? $item_data['title'] : $item_data;
            $item_price = is_array($item_data) && !empty($item_data['price']) ? ' (' . $item_data['price'] . ')' : '';
            $items_list .= "• " . $item_title . $item_price . "\n";
        }

        // Build email message
        $email_message = sprintf(
            __("New inquiry from %s (%s)\nMessage:\n%s\nItems inquired about:\n%s", 'lm-inquiry-cart'),
            $user_name,
            $user_email,
            $user_message,
            $items_list
        );

        // Set email headers
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
            'Reply-To: ' . $user_name . ' <' . $user_email . '>',
        );

        // Send the email
        $sent = wp_mail($email_to, $email_subject, $email_message, $headers);

        // Clear cart cookie on success 🔹
        if ($sent) {
            setcookie('lm_ic_cart', '', time() - 3600, '/');
            setcookie('lm_ic_cart', '', time() - 3600, '/', COOKIE_DOMAIN);
            setcookie('lm_ic_cart', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
            echo '<script>
            document.cookie = "lm_ic_cart=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "lm_ic_cart=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' . esc_js($_SERVER['HTTP_HOST']) . ';";
            </script>';
            return $options['msg_sent'];
        } else {
            error_log("Larry Manglani's Inquiry Cart: wp_mail failed for inquiry from " . $user_email);
            return $options['msg_fail'];
        }
    } else {
        return lm_ic_generate_form_html($options);
    }
}

/**
 * Helper: Generate the HTML form with cart items
 */
function lm_ic_generate_form_html($options, $saved_name = '', $saved_email = '', $saved_message = '', $cart_items = null) {
    if ($cart_items === null) {
        $cart_items = array();
        if (isset($_COOKIE['lm_ic_cart'])) {
            $decoded = json_decode(stripslashes($_COOKIE['lm_ic_cart']), true);
            if (is_array($decoded) && !is_null($decoded)) {
                $cart_items = $decoded;
            }
        }
    }

    ob_start();
    ?>
    <div class="lm-ic-cart-form">
        <div class="lm-ic-cart-items">
            <h3><?php _e('Items in your inquiry:', 'lm-inquiry-cart'); ?></h3>
            <?php if (empty($cart_items)) : ?>
                <p><em><?php _e('No specific items selected. Please describe your inquiry in the message field.', 'lm-inquiry-cart'); ?></em></p>
            <?php else : ?>
                <ul class="lm-ic-items-list">
                    <?php foreach ($cart_items as $item_id => $item_data) :
                        $item_title = is_array($item_data) ? $item_data['title'] : $item_data;
                        $item_price = is_array($item_data) && !empty($item_data['price']) ? '<span class="lm-ic-price">' . esc_html($item_data['price']) . '</span>' : '';
                        ?>
                        <li class="lm-ic-item">
                            <span class="lm-ic-item-title"><?php echo esc_html($item_title); ?></span>
                            <?php echo $item_price; ?>
                            <button type="button" class="lm-ic-remove-item button small" data-item-id="<?php echo esc_attr($item_id); ?>">
                                <?php _e('Remove', 'lm-inquiry-cart'); ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <form method="post" action="" class="lm-ic-form" novalidate>
            <?php wp_nonce_field('lm_ic_form_submit', 'lm_ic_form_nonce'); ?>
            <input type="hidden" name="lm_ic_form_submitted" value="y" />
            <p class="lm-ic-field">
                <label for="lm_ic_user_name"><?php _e('Your name *', 'lm-inquiry-cart'); ?></label><br />
                <input type="text" id="lm_ic_user_name" name="lm_ic_user_name" value="<?php echo esc_attr($saved_name); ?>" class="regular-text" required aria-required="true" />
            </p>
            <p class="lm-ic-field">
                <label for="lm_ic_user_email"><?php _e('Your email address *', 'lm-inquiry-cart'); ?></label><br />
                <input type="email" id="lm_ic_user_email" name="lm_ic_user_email" value="<?php echo esc_attr($saved_email); ?>" class="regular-text" required aria-required="true" />
            </p>
            <p class="lm-ic-field">
                <label for="lm_ic_user_message"><?php _e('Your inquiry *', 'lm-inquiry-cart'); ?></label><br />
                <textarea id="lm_ic_user_message" name="lm_ic_user_message" rows="6" cols="50" class="large-text" required aria-required="true"><?php echo esc_textarea($saved_message); ?></textarea>
            </p>
            <p class="lm-ic-submit">
                <?php submit_button(__('Send your inquiry', 'lm-inquiry-cart'), 'primary', 'lm_ic_submit', false); ?>
            </p>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue inline script for remove-item buttons
 */
add_action('wp_footer', 'lm_ic_cart_inline_script', 999);
function lm_ic_cart_inline_script() {
    // ✅ FIXED: Safe post check to prevent warnings on archive pages
    $post = get_post();
    if (!is_admin() && $post && has_shortcode($post->post_content, 'lm_inquiry_cart')) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.lm-ic-remove-item').on('click', function() {
                let itemId = $(this).data('item-id');
                
                // 🔐 Safe cookie parser (Same logic as cart.js to prevent [null] bugs)
                function getCookie(name) {
                    try {
                        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                        if (match) {
                            let val = JSON.parse(decodeURIComponent(match[2]));
                            // If cookie is null, array, or not an object, treat as empty
                            if (val === null || Array.isArray(val) || typeof val !== 'object') {
                                return {};
                            }
                            return val;
                        }
                        return {};
                    } catch (e) {
                        return {};
                    }
                }

                let cart = getCookie('lm_ic_cart');
                if (cart.hasOwnProperty(itemId)) {
                    delete cart[itemId];
                }

                // Delete cookie if empty, otherwise save updated cart
                if (Object.keys(cart).length === 0) {
                    document.cookie = 'lm_ic_cart=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Lax';
                } else {
                    document.cookie = 'lm_ic_cart=' + encodeURIComponent(JSON.stringify(cart)) + '; path=/; max-age=' + (60*60*24) + '; SameSite=Lax';
                }
                
                location.reload();
            });
        });
        </script>
        <?php
    }
}