<?php
$css_class_service = ($is_optional_product) ? "service-item" : "";
$payment_price_class = "";
$payment_rate_class = "";
$parent_products_class = (!$is_optional_product) ? "parent-item" : "";
$course_quantity = $cart_item['quantity'];
if ($_product->get_price() == 0) {
    $payment_price_class = "hidden";
    $payment_rate_class = "to_hidden";
    $course_quantity = "";
}

if (!$_product->is_taxable() || $rate["rate"] == 0) {
    $payment_rate_class = "to_hidden";
}


$_product_total = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
if ($_product->is_taxable() && !empty($rate["label"])) {
    $_product_total = str_replace('(incl. btw)', '(incl. ' . $rate["label"] . ' btw)', $_product_total);
}

?>
<tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?> ">
    <td class="product-name  <?php echo $css_class_service;
                                echo $parent_products_class; ?>">
        <?php
        $product_title = $_product->get_name();
        if ($_product->get_purchase_note() != '' && !empty($css_class_service)) {

            $product_title = $_product->get_purchase_note();
        }
        ?>
        <?php
        if (!$is_optional_product) {
            global $wpdb;
            $table_name = $wpdb->prefix . "courses";
            if (isset($cart_item['info']['course'])) {
                $course_id = (int) $cart_item['info']['course'];
                if ($course_id > 0) {
                    $rows = $wpdb->get_results("SELECT date from $table_name where id = $course_id");
                    $courseDate =  getDutchMonth(date("l, j F Y", $rows[0]->date));
        ?>
                    <p style="margin-bottom: 0; font-weight: bold; color:#007dc5;"><?php echo $courseDate ?></p>

        <?php
                }
            }
            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $product_title,), $cart_item, $cart_item_key) . '&nbsp;');
        } else

            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $product_title, $cart_item, $cart_item_key)) . '&nbsp;'; ?>
        <?php
        if (!$_product->get_price() == 0) {
            echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $course_quantity) . '</strong>', $cart_item, $cart_item_key);
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>

        <?php } else echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . $course_quantity . '</strong>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped }
        ?>
        <?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>
    </td>
    <td class="product-total">
        <?php
        echo "<span class='" . $payment_price_class . " " . $payment_rate_class . "'>";
        echo $_product_total; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo "</span>";
        ?>
    </td>
</tr>