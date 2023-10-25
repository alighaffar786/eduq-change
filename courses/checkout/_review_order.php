<?php

$cart_items = WC()->cart->get_cart();
list($cart_items, $service_cart_items) = get_services_from_cart($cart_items);

foreach ($cart_items as $cart_item_key => $cart_item) {

    $_product = $cart_item["_product"];
    $_slug = trim($_product->get_slug());
    $is_optional_product = $cart_item['is_optional'];
    $taxes = $_tax->get_rates($_product->get_tax_class());
    $rate = array_shift($taxes);


    if (!$is_optional_product && $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
        $course_quantity = $cart_item['quantity'];
        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);

        include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_review_order_item.php');
        if (!empty($cart_item["optional_courses"])) {
            foreach ($cart_item["optional_courses"] as $service) {
                include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_review_order_service.php');
            }
        }
    }
}




