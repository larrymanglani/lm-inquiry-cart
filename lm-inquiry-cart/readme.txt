=== Larry Manglani's Inquiry Cart ===
Contributors: larrymanglani
Tags: inquiry, cart, quote request, product inquiry, lead generation, contact form
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a secure inquiry cart to your website that allows users to ask questions about specific posts or products.

== Description ==
Larry Manglani's Inquiry Cart is a secure, modernized fork of the original Inquiry Cart plugin. It allows visitors to add items to a "virtual cart" as they browse your site and then submit a single inquiry for all selected items.

It's perfect for B2B websites, wholesalers, manufacturers, and service providers who want to generate leads rather than just process sales.

**Key Features:**
* 🛡️ **Secure:** Fully patched against CSRF vulnerabilities with proper nonce verification.
* 🍪 **Cookie-Based:** Lightweight cart that works across all pages without bloating your database.
* ✨ **Modern UI:** Clean shortcodes for "Add to Cart" buttons and the final inquiry form.
* 📱 **Responsive:** Works seamlessly on desktop and mobile devices.
* 📧 **Configurable:** Custom email recipients, subjects, and user messages.
* 🔄 **Cross-Tab Sync:** Real-time cart updates across browser tabs.
* ⚡ **Caching Compatible:** Works perfectly with WP Rocket, W3 Total Cache, LiteSpeed, etc.

== Installation ==
1. Upload the `lm-inquiry-cart` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings → Inquiry Cart** to configure your email address.
4. Use the shortcode `[lm_add_to_inquiry]` to add items and `[lm_inquiry_cart]` to display the cart form.

== Frequently Asked Questions ==
= How do I add a product to the cart? =
Use the shortcode `[lm_add_to_inquiry title="Product Name"]` anywhere on your site. You can also add `price="..."` or `sku="..."` for additional details.

= Where do the inquiries go? =
By default, inquiries are emailed to your site's admin email. You can change the recipient address in **Settings → Inquiry Cart**.

= Does it work with caching plugins? =
Yes. The cart uses client-side cookies and JavaScript, so it works perfectly with WP Rocket, W3 Total Cache, LiteSpeed, and other caching solutions.

= Can I require users to log in? =
The free version allows guest inquiries. User role restrictions can be added via custom code or in a future Pro version.

= Is my data secure? =
Yes. The plugin uses WordPress nonce verification for all form submissions and AJAX requests, protecting against CSRF attacks.

== Screenshots ==
1. The "Add to Inquiry" button on a product listing page.
2. Inquiry Cart form showing selected items and submission fields.
3. Plugin settings page for configuring email and messages.

== Changelog ==
= 1.0.0 =
* Initial public release of the Larry Manglani fork.
* Fixed critical CSRF security vulnerability with proper nonce implementation.
* Added `lm_ic_` namespacing to prevent function conflicts.
* Improved cookie handling with modern `SameSite=Lax` and automatic cleanup on submission.
* Added cross-tab synchronization for real-time cart updates.
* Optimized JavaScript for better performance and cross-browser compatibility.

== Upgrade Notice ==
= 1.0.0 =
First release. Please test on a staging site before deploying to production.
