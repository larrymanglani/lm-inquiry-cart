# Larry Manglani's Inquiry Cart

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/lm-inquiry-cart?label=WordPress.org)](https://wordpress.org/plugins/lm-inquiry-cart/)
[![License](https://img.shields.io/github/license/larrymanglani/lm-inquiry-cart)](LICENSE)
[![WordPress Tested](https://img.shields.io/wordpress/v/lm-inquiry-cart)](https://wordpress.org/plugins/lm-inquiry-cart/)

**Secure WordPress plugin for B2B product inquiries and lead generation.**

## Features

Larry Manglani's Inquiry Cart transforms your WordPress site into a powerful lead generation tool, allowing visitors to build a customized inquiry by adding multiple products to a virtual cart before submitting a single, comprehensive form.

- Virtual Inquiry Cart: Let visitors add multiple products and submit one comprehensive inquiry instead of multiple forms.
- Enterprise Security: Fully patched against CSRF vulnerabilities with WordPress nonce verification.
- Cookie-Based System: Lightweight cart that persists across pages without bloating your database.
- Cross-Tab Sync: Real-time cart updates across browser tabs for seamless user experience.
- Simple Shortcodes: Easy-to-use shortcodes work with any theme or page builder.
- Mobile Responsive: Works seamlessly on desktop and mobile devices.
- Caching Compatible: Works perfectly with WP Rocket, W3 Total Cache, LiteSpeed, and other caching solutions.

## Screenshots

(Place your screenshot-1.png, screenshot-2.png, and screenshot-3.png files in an assets/ folder at the root of the repository to display these.)

1. The "Add to Inquiry" button on a product listing page.
2. Inquiry Cart form showing selected items and submission fields.
3. Plugin settings page for configuring email and messages.

## Installation

### From WordPress.org
1. Go to Plugins > Add New in your WordPress admin.
2. Search for "Larry Manglani's Inquiry Cart".
3. Click Install Now then Activate.

### Manual Installation
1. Download the plugin from WordPress.org.
2. Upload the lm-inquiry-cart folder to /wp-content/plugins/.
3. Activate through the Plugins menu in WordPress.
4. Configure settings in Settings > Inquiry Cart.

## Usage

### Add Products to Cart
Use the [lm_add_to_inquiry] shortcode anywhere on your site (pages, posts, widgets).

[lm_add_to_inquiry title="Product Name" price="$99" sku="PCL-001"]

Available Parameters:
- title (required): Product or item name.
- price (optional): Product price.
- sku (optional): Product SKU or unique identifier.
- class (optional): Custom CSS class for styling.

### Display Cart Form
Use the [lm_inquiry_cart] shortcode on the page where you want the inquiry form and cart summary to appear.

[lm_inquiry_cart]

## Documentation

For detailed documentation, tutorials, and Pro version features (Email Templates, CRM Sync, Analytics, etc.), visit:
larrymanglani.com

## Contributing

Contributions are welcome! Please follow these guidelines:
1. Fork the repository.
2. Create a feature branch (git checkout -b feature/AmazingFeature).
3. Commit your changes (git commit -m 'Add some AmazingFeature').
4. Push to the branch (git push origin feature/AmazingFeature).
5. Open a Pull Request.

## Development

### Requirements
- WordPress 5.0+
- PHP 7.0+
- jQuery

### Coding Standards
This plugin follows WordPress Coding Standards.

## Changelog

### 1.0.0 (2026-05-03)
- Initial public release.
- Fixed critical CSRF security vulnerability with proper nonce implementation.
- Added lm_ic_ namespacing to prevent function conflicts.
- Improved cookie handling with modern SameSite=Lax and automatic cleanup on submission.
- Added cross-tab synchronization for real-time cart updates.
- Optimized JavaScript for better performance and cross-browser compatibility.

## License

This plugin is licensed under GPLv2 or later.

## Author

Larry Manglani
- Website: larrymanglani.com
- GitHub: @larrymanglani

## Acknowledgments

- Original Inquiry Cart plugin (forked and modernized for security and performance).
- WordPress community for inspiration and support.
