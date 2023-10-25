<?php
//Replace order
$cart_item_parent = $cart_item;
$cart_item = $cart_items[$service_cart_items[$service]];

$_product = $cart_item["_product"];
$_slug = trim($_product->get_slug());
$is_optional_product = $cart_item['is_optional'];
$payment_price_class = "";
$course_quantity = $cart_item['quantity'];
if ($_product->get_price() == 0) {
    $payment_price_class = "hidden";
    $payment_rate_class = "to_hidden";
    $course_quantity = "";
}
if ($_product->get_price() > 0) {
    $taxes = $_tax->get_rates($_product->get_tax_class());
    $rate = array_shift($taxes);
} else {

    $rate = ['rate' => 0, 'label' => '0%', 'shipping' => 'yes', 'compound' => 'no'];
}
$_product_total = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
//echo "<pre>";
//print_r($_product_total);


if ($_product->is_taxable() && !empty($rate["label"])) {
    $_product_total = str_replace('(incl. btw)', '(incl. ' . $rate["label"] . ' btw)', $_product_total);
} else {
    $_product_total = get_without_tax($_product_total);
}
$cart_item['quantity'] = $cart_item_parent["quantity"];

if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
    include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_review_order_item.php');
}
