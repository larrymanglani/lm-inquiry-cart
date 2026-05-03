<?php
/**
 * Settings Page Template for Larry Manglani's Inquiry Cart
 * 
 * This file is included from the main plugin file.
 * Security: The parent file already checks user capabilities and verifies nonces.
 */

// Safety: Ensure this file isn't accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get current options from database (with fallback defaults)
$defaults = array(
    'email_to'      => get_option('admin_email'),
    'email_subject' => get_bloginfo('name') . ' - New Inquiry',
    'msg_sent'      => '<p>Thank you! Your inquiry has been sent.</p>',
    'msg_fail'      => '<p>Sorry, your inquiry could not be sent. Please try again.</p>',
);
$options = wp_parse_args(get_option('lm_ic_options', array()), $defaults);

// Handle form submission (only if nonce was already verified in parent file)
if (isset($_POST['lm_ic_save_settings'])) {
    
    // Sanitize and validate incoming data
    $new_options = array(
        'email_to'      => sanitize_email($_POST['lm_ic_email_to']),
        'email_subject' => sanitize_text_field($_POST['lm_ic_email_subject']),
        'msg_sent'      => wp_kses_post($_POST['lm_ic_msg_sent']),   // Allows safe HTML like <p>, <strong>
        'msg_fail'      => wp_kses_post($_POST['lm_ic_msg_fail']),
    );
    
    // Validate required fields
    if (empty($new_options['email_to']) || !is_email($new_options['email_to'])) {
        echo '<div class="notice notice-error"><p>' . 
             __('Please enter a valid email address for inquiries.', 'lm-inquiry-cart') . 
             '</p></div>';
        // Keep old values if validation fails
        $display_options = $options;
    } else {
        // Save validated options
        update_option('lm_ic_options', $new_options);
        $display_options = $new_options;
        echo '<div class="notice notice-success"><p>' . 
             __('✅ Settings saved successfully!', 'lm-inquiry-cart') . 
             '</p></div>';
    }
} else {
    // First load: display current saved options
    $display_options = $options;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <!-- Shortcode Reference Section -->
    <div class="card" style="max-width: 800px; margin: 20px 0; padding: 15px;">
        <h2>📋 How to Use This Plugin</h2>
        
        <h3>➕ Add an item to the inquiry cart</h3>
        <p>Use this shortcode in any post or page:</p>
        <pre style="background: #f0f0f1; padding: 10px; border-left: 4px solid #0073aa;">[lm_add_to_inquiry title="Your Product Name"]</pre>
        <p><em>Example:</em> <code>[lm_add_to_inquiry title="Premium Widget"]</code></p>
        
        <h3>🛒 Display the inquiry cart form</h3>
        <p>Place this shortcode on the page where you want the cart to appear:</p>
        <pre style="background: #f0f0f1; padding: 10px; border-left: 4px solid #0073aa;">[lm_inquiry_cart]</pre>
    </div>
    
    <!-- Settings Form -->
    <div class="card" style="max-width: 800px; padding: 20px;">
        <h2>⚙️ Plugin Settings</h2>
        <p>Configure where inquiries are sent and what messages users see.</p>
        
        <form method="post" action="">
            <!-- WordPress security fields (nonce + referer) -->
            <?php wp_nonce_field('lm_ic_save_settings', 'lm_ic_nonce'); ?>
            <?php settings_fields('lm_ic_settings_group'); // Optional: for settings API compatibility ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lm_ic_email_to">Send inquiries to *</label>
                    </th>
                    <td>
                        <input 
                            type="email" 
                            id="lm_ic_email_to" 
                            name="lm_ic_email_to" 
                            value="<?php echo esc_attr($display_options['email_to']); ?>" 
                            class="regular-text" 
                            required
                        >
                        <p class="description">Email address that will receive inquiry notifications.</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="lm_ic_email_subject">Email subject line</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="lm_ic_email_subject" 
                            name="lm_ic_email_subject" 
                            value="<?php echo esc_attr($display_options['email_subject']); ?>" 
                            class="regular-text"
                        >
                        <p class="description">Subject line for inquiry emails. You can use plain text only.</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="lm_ic_msg_sent">Success message</label>
                    </th>
                    <td>
                        <textarea 
                            id="lm_ic_msg_sent" 
                            name="lm_ic_msg_sent" 
                            rows="3" 
                            class="large-text code"
                        ><?php echo esc_textarea($display_options['msg_sent']); ?></textarea>
                        <p class="description">Message shown to users after a successful inquiry. Basic HTML allowed (&lt;p&gt;, &lt;strong&gt;, etc.).</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="lm_ic_msg_fail">Error message</label>
                    </th>
                    <td>
                        <textarea 
                            id="lm_ic_msg_fail" 
                            name="lm_ic_msg_fail" 
                            rows="3" 
                            class="large-text code"
                        ><?php echo esc_textarea($display_options['msg_fail']); ?></textarea>
                        <p class="description">Message shown if the inquiry fails to send. Basic HTML allowed.</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <?php submit_button('Save Settings', 'primary', 'lm_ic_save_settings', false); ?>
            </p>
        </form>
    </div>
    
    <!-- Helpful Tip -->
    <div class="notice notice-info" style="max-width: 800px; margin-top: 20px;">
        <p>
            <strong>💡 Pro Tip:</strong> After saving settings, test your inquiry form by adding a product to the cart and submitting a test inquiry. Check that the email arrives at the address above.
        </p>
    </div>
</div>