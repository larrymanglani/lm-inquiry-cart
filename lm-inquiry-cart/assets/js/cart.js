/**
Larry Manglani's Inquiry Cart - Frontend JavaScript (FINAL FIX)
Handles add/remove buttons via cookies with cross-page sync
*/
jQuery(document).ready(function($) {

// 🔐 Safe cookie parser - Fixed to handle [null] and invalid data
function getCookie(name) {
    try {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) {
            let val = JSON.parse(decodeURIComponent(match[2]));
            // If cookie is null, array, or not an object, treat as empty cart
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

// 💾 Save cart to cookie - FIXED: Deletes cookie when empty
function setCookie(name, value, days = 1) {
    // If cart is empty, DELETE the cookie entirely to prevent "stuck" states
    if (Object.keys(value).length === 0) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Lax';
        return;
    }

    let expires = '';
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toUTCString();
    }
    // Using path=/ ensures the cookie works across all pages
    document.cookie = name + '=' + encodeURIComponent(JSON.stringify(value)) + expires + '; path=/; SameSite=Lax';
}

// 🔄 Sync ALL toggle buttons to match cart state
function updateButtonStates() {
    let cart = getCookie('lm_ic_cart');
    let count = Object.keys(cart).length;
    
    // Update cart count displays
    $('#cartCount, .lm-ic-cart-count').text(count);

    // Toggle the visibility of the green "View Inquiry Cart" box
    if (count > 0) {
        $('#viewCartLink, .lm-ic-view-cart').css('display', 'inline-flex');
    } else {
        $('#viewCartLink, .lm-ic-view-cart').hide();
    }

    // Sync all "Add to inquiry" buttons
    $('.lm-ic-toggle-btn').each(function() {
        let $btn = $(this);
        let itemId = $btn.data('item-id');
        let $text = $btn.find('.lm-ic-btn-text');
        let $status = $btn.siblings('.lm-ic-status');

        if (!itemId) return;

        if (cart[itemId]) {
            $text.text('Remove from inquiry');
            $btn.addClass('in-cart');
            if ($status.length) $status.text('✓ In cart').css('color', '#46b450');
        } else {
            $text.text('Add to inquiry');
            $btn.removeClass('in-cart');
            if ($status.length) $status.text('').css('color', '');
        }
    });

    // Sync remove buttons on contact page
    $('.lm-ic-cart-remove, .lm-ic-remove-btn, [data-lm-ic-remove]').each(function() {
        let $btn = $(this);
        let itemId = $btn.data('item-id') || $btn.closest('[data-item-id]').data('item-id');

        if (itemId && !cart[itemId]) {
            $btn.closest('.lm-ic-cart-item, tr, li, div[data-item-id]').fadeOut(200, function() {
                $(this).remove();
                // Re-check empty state
                if (Object.keys(getCookie('lm_ic_cart')).length === 0) {
                    $('.lm-ic-cart-empty').show();
                    $('.lm-ic-cart-items').hide();
                }
            });
        }
    });
}

// ➕➖ Handle Add/Remove clicks
$(document).on('click', '.lm-ic-toggle-btn', function(e) {
    e.preventDefault();
    let $btn = $(this);
    let itemId = $btn.data('item-id');
    let itemTitle = $btn.data('item-title') || 'Item';
    let itemPrice = $btn.data('item-price') || '';
    let $status = $btn.siblings('.lm-ic-status');
    
    if (!itemId) return;

    let cart = getCookie('lm_ic_cart');
    let action = cart[itemId] ? 'remove' : 'add';

    if (action === 'add') {
        cart[itemId] = { title: itemTitle, price: itemPrice, added: new Date().toISOString() };
        if ($status.length) $status.text('✓ Added!').css('color', '#46b450');
    } else {
        delete cart[itemId];
        if ($status.length) $status.text('✓ Removed').css('color', '#dc3232');
    }

    setCookie('lm_ic_cart', cart);

    // Re-sync UI
    setTimeout(updateButtonStates, 50);

    // Optional AJAX sync for server-side tracking
    if (typeof lmIcData !== 'undefined') {
        $.post(lmIcData.ajax_url, {
            action: 'lm_ic_ajax_toggle',
            nonce: lmIcData.nonce,
            item_id: itemId,
            item_title: itemTitle,
            action_type: action
        });
    }
});

// 🚀 Start on load
updateButtonStates();

// 🔁 Sync across tabs
$(window).on('storage', function(e) {
    if (e.originalEvent.key === 'lm_ic_cart') {
        updateButtonStates();
    }
});
});