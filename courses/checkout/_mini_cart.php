<?php

$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
    $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
    $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
    $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
    ?>

    <li class="woocommerce-mini-cart-item <?php echo esc_attr(apply_filters('woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key)); ?>">

        <?php
        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            'woocommerce_cart_item_remove_link',
            sprintf(
                '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
                esc_url(wc_get_cart_remove_url($cart_item_key)),
                esc_attr__('Remove this item', 'woocommerce'),
                esc_attr($product_id),
                esc_attr($cart_item_key),
                esc_attr($_product->get_sku())
            ),
            $cart_item_key
        );
        ?>
        <?php if (empty($product_permalink)) : ?>
            <?php echo $thumbnail . wp_kses_post($product_name); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php else : ?>
            <a href="<?php echo esc_url($product_permalink); ?>">
                <?php echo $thumbnail . wp_kses_post($product_name); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
        <?php endif; ?>
        <?php
        echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) . '</span>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </li>
    <?php
}
/**
 * to override woocommerce cart contents count
 * @retun int
 */
if (!function_exists('override_woocommerce_cart_contents_count')) {

    function override_woocommerce_cart_contents_count() {
        $cart_items = WC()->cart->get_cart();
        list($cart_items, $service_cart_items) = get_services_from_cart($cart_items);
        $total_parent_courses = [];
        foreach ($cart_items as $key => $cart_item) {
            $_product = $cart_item["_product"];
            $id = $_product->get_id();
            if (!$cart_item['is_optional']) {
                $course_qty = $cart_item["quantity"];
                $total_parent_courses[$id] = ['qty' => $course_qty];
            }
        }
        return array_sum(array_column($total_parent_courses, 'qty'));
    }
}
add_filter('woocommerce_cart_contents_count', 'override_woocommerce_cart_contents_count', 10);


if (!function_exists('override_get_cart_subtotal')) {
    /**
     *  function to override cart subtotal
     * @retun string
     */

    function override_get_cart_subtotal($compound = false) {
        if ($compound) {
            $cart_subtotal = wc_price(WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total() + WC()->cart->get_taxes_total(false, false));

        } elseif (WC()->cart->display_prices_including_tax()) {
            $cart_subtotal = wc_price(WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax());

            if (WC()->cart->get_subtotal_tax() > 0 && !wc_prices_include_tax()) {
                //$cart_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                $cart_subtotal .= ' <small class="tax_label">' . '(incl.&nbsp;' . WC()->cart->get_cart_tax() . '&nbsp;btw)' . '</small>';
            }
        } else {
            $cart_subtotal = wc_price(WC()->cart->get_subtotal());

            if (WC()->cart->get_subtotal_tax() > 0 && wc_prices_include_tax()) {
                $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
            }
        }
        return apply_filters('woocommerce_cart_subtotal', $cart_subtotal, $compound, WC()->cart);
    }
}


if (!function_exists('override_woocommerce_widget_shopping_cart_subtotal')) {
    /**
     * function to override widget shopping cart subtotal
     *
     */
    function override_woocommerce_widget_shopping_cart_subtotal() {
        echo '<strong>' . esc_html__('Total:', 'woocommerce') . '</strong>' . override_get_cart_subtotal();
    }
}
remove_action('woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal');
add_action('woocommerce_widget_shopping_cart_total', 'override_woocommerce_widget_shopping_cart_subtotal', 10);