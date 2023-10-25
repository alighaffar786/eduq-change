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
?>

<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

    <td class="product-remove">
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
    </td>

    <td class="product-name <?php echo $css_class_service; ?>" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
        <?php

        if (!$_href_status) {
            // echo "<pre>";
            // print_r($_GET['event_id']);
            // die;

            $_product_name = $_product->get_name();
            if ($_product->get_purchase_note() != '') {
                $_product_name = $_product->get_purchase_note();
            }
            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product_name, $cart_item, $cart_item_key) . '&nbsp;');
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . "courses";
            if (isset($cart_item['info']['course'])) {
                $course_id = (int) $cart_item['info']['course'];
                if ($course_id > 0) {
                    $rows = $wpdb->get_results("SELECT date from $table_name where id = $course_id");
                    $courseDate = getDutchMonth(date("l, j F Y", $rows[0]->date));
        ?>
                    <p style="margin-bottom: 0; font-weight: bold; color:#007dc5;"><?php echo $courseDate ?></p>

        <?php
                }
            }
            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
        }

        do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

        // Meta data.
        echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

        // Backorder notification.
        if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
            echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
        }

        // die;
        ?>
    </td>

    <td class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
        <?php
        if (!$hide_price_tag) {
            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
        } else {
            echo "&nbsp;";
        }
        ?>
    </td>

    <td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
        <?php

        if ($_product->is_sold_individually()) {
            $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
        } else {
            /*
            $product_quantity = woocommerce_quantity_input( array(
                'input_name'   => "cart[{$cart_item_key}][qty]",
                'input_value'  => $cart_item['quantity'],
                'max_value'    => $_product->get_max_purchase_quantity(),
                'min_value'    => '0',
                'readonly' => 'readonly',
                'product_name' => $_product->get_name(),
            ), $_product, false );
            */


            $product_quantity = sprintf($cart_item['quantity'] . ' <input type="hidden" name="cart[%s][qty]" value="' . $cart_item['quantity'] . '" />', $cart_item_key);
        }

        if (!$hide_price_tag) {
            echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
        } else {
            echo "&nbsp;";
        }
        ?>
    </td>
    <td class="product-edit" data-slug="<?php echo $_slug; ?>">
        <?php
        if ($_href_status) :
        ?>
            <a href="<?php echo $_edit_link; ?>&edit=true">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
        <?php
        else :
            echo "&nbsp;";
        endif;
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