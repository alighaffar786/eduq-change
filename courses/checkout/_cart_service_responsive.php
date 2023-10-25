<?php
$cart_item_parent = $cart_item;
$cart_item = $cart_items[$service_cart_items[$service]];
$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

if ($_product->get_price() > 0) {
    $taxes = $_tax->get_rates($_product->get_tax_class());
    $rate = array_shift($taxes);
} else {
    $rate = ['rate' => 0, 'label' => '0%', 'shipping' => 'yes', 'compound' => 'no'];
}


$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

$_edit_link = "";
$_slug = trim($_product->slug);
$_href_status = false;
$cart_item['quantity'] = $cart_item_parent["quantity"];

if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
    $product_permalink = "";
    include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_cart_item_responsive.php');
} elseif ($_product->get_price() == 0) {
    $course_quantity = "";
}