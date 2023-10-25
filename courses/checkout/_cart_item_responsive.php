<?php
$hide_price_tag = false;
$css_side_class_rate = "";
if ($_product->price == 0) {
    $hide_price_tag = true;
    $css_side_class_rate = "to_hidden";
}
if (!$_product->is_taxable() || $rate["rate"] == 0) {
    $css_side_class_rate = "to_hidden";
}
$course_quantity = $cart_item['quantity'];
if ($_product->get_price() == 0) {
    $course_quantity = "";
}
?>

<tr class="woocommerce-cart-form__cart-item mobile-ctr <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

    <td class=" product-edit" data-slug="<?php echo $_slug; ?>">
        <span class="product-remove">
            <?php

            // @codingStandardsIgnoreLine
            $css_class_service = "";
            if ($_href_status) {
                echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                    __('Remove this item', 'woocommerce'),
                    esc_attr($product_id),
                    esc_attr($_product->get_sku())
                ), $cart_item_key);
            } else {
                $css_class_service = "service-item";
            }
            ?>
        </span>
        <?php
        if ($_href_status) :
        ?>
            <span class="product-edit">
                <a href="<?php echo $_edit_link; ?>&edit=true">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>
            </span>
        <?php
        endif;
        ?>
    </td>
    <td class="product-name <?php echo $css_class_service; ?>">
        <?php
        if (!$_href_status) {
            $product_title = $_product->get_name();
            if ($_product->get_purchase_note() != '') {
                $product_title = $_product->get_purchase_note();
            }
            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', "- " . $product_title, $cart_item, $cart_item_key)) . '&nbsp;'; ?>
            <?php if (!$_product->get_price() == 0) {
                echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $course_quantity) . '</strong>', $cart_item, $cart_item_key);
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
            <?php } else echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . $course_quantity . '</strong>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped }
            ?>
            <?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
            ?>
            <?php } else
            global $wpdb;
        $table_name = $wpdb->prefix . "courses";
        if (isset($cart_item['info']['course'])) {
            $course_id = (int) $cart_item['info']['course'];
            if ($course_id > 0) {
                $rows = $wpdb->get_results("SELECT date from $table_name where id = $course_id");
                $courseDate   =   getDutchMonth(date("l, j F Y", $rows[0]->date));
            ?>
                <p style="margin-bottom: 0; font-weight: bold; color:#007dc5;"><?php echo $courseDate ?></p>

        <?php
            }
        }

        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name() . sprintf('&nbsp;&times;&nbsp;%s', $course_quantity)), $cart_item, $cart_item_key));
        ?>
    </td>
    <td class="product-subtotal" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>">
        <?php

        echo "<span class='$css_side_class_rate'>";
        if (!$hide_price_tag) {
            $_product_sub_total = WC()->cart->get_product_subtotal($_product, $cart_item['quantity']);
            $_product_sub_total_wc = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.;
            if ($_product->is_taxable() && !empty($rate["label"])) {

                echo str_replace('(incl. btw)', '(incl. ' . $rate["label"] . ' btw)', $_product_sub_total_wc);
            } else if (!$_href_status) {
                echo get_without_tax($_product_sub_total);
            } else {
                echo $_product_sub_total_wc;
            }
        } else {
            echo "&nbsp;";
        }

        echo "</span>";
        ?>
    </td>
</tr>