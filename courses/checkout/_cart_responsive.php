<?php

$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
$taxes = $_tax->get_rates($_product->get_tax_class());
$rate = array_shift($taxes);

$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

$_event_id = isset($cart_item["info"]["course"]) ? $cart_item["info"]["course"] : 0;
$_edit_link = get_permalink(CHECKOUT_PAGE_ID) . "?process=buy&course_id=" . $_product->id . "&event_id=" . $_event_id;
$_edit_link .= "&cart_key=" . $cart_item_key;

$_slug = trim($_product->slug);
$_href_status = true;

if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
    include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_cart_item_responsive.php');
    if (!empty($cart_item["optional_courses"])) {
        foreach ($cart_item["optional_courses"] as $service) {
            include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_cart_service_responsive.php');
        }
    }
}
?>