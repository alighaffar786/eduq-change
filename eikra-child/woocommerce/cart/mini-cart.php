<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_mini_cart'); ?>

<?php if (!WC()->cart->is_empty()) : ?>

    <ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr($args['list_class']); ?>">
        <?php
        $cart_items = WC()->cart->get_cart();
        do_action('woocommerce_before_mini_cart_contents');
        list($cart_services, $service_cart_items, $cart_items) = reload_cart_services($cart_items);
        $_tax = new WC_Tax();
        foreach ($cart_items as $cart_item_key => $cart_item) {
            if (!isset($cart_services[$cart_item['product_id']])) {
                if (!isset($cart_services[$cart_item['product_id']])) {
                    include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_mini_cart.php');
                }
            }

        }
        do_action('woocommerce_mini_cart_contents');
        ?>
    </ul>

    <p class="woocommerce-mini-cart__total total">
        <?php
        /**
         * Hook: woocommerce_widget_shopping_cart_total.
         *
         * @hooked woocommerce_widget_shopping_cart_subtotal - 10
         */
       
        do_action('woocommerce_widget_shopping_cart_total');
        ?>
    </p>

    <?php do_action('woocommerce_widget_shopping_cart_before_buttons'); ?>

    <p class="woocommerce-mini-cart__buttons buttons"><?php do_action('woocommerce_widget_shopping_cart_buttons'); ?></p>

    <?php do_action('woocommerce_widget_shopping_cart_after_buttons'); ?>

<?php else : ?>

    <p class="woocommerce-mini-cart__empty-message"><?php esc_html_e('No products in the cart.', 'woocommerce'); ?></p>

<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>
