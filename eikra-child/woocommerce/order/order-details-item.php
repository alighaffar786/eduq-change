<?php

/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
    return;
}
?>
<tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order)); ?>">

    <td class="woocommerce-table__product-name product-name <?php echo $css_class; ?> <?php echo $product->get_name(); ?>">
        <?php

        $is_visible = $product && $product->is_visible();
        $product_title = $product->get_name();
        // works when product is a service product
        if ($product->get_purchase_note() != '' && $optional_item_id > 0) {

            $product_title = $product->get_purchase_note();
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . "courses";
            $getCourseID = $item->get_meta('info');
            $courseDate = '';
            if (isset($getCourseID['course'])) {
                $course_id = (int) $getCourseID['course'];
                if ($course_id > 0) {
                    $rows = $wpdb->get_results("SELECT date from $table_name where id = $course_id");
                    $courseDate =  getDutchMonth(date("l, j F Y", $rows[0]->date));
        ?>
                    <p style="font-size:18px; font-weight: bold; color:#007dc5;"><?php echo $courseDate ?></p>

        <?php


                }
            }
        }

        $product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);
        //        $qty = $item->get_quantity();

        $qty = isset($item_qty) ? $item_qty : $item->get_quantity();

        if ($optional_item_id > 0) {
            if ($item->get_product()->price == 0) {

                echo wp_kses_post(apply_filters('woocommerce_order_item_name', $product_title));
            } else {
                echo wp_kses_post(apply_filters('woocommerce_order_item_name', $product_title, $item) . '&nbsp;');
            }
        } else {
            echo wp_kses_post(apply_filters('woocommerce_order_item_name', $product_permalink ? sprintf('<a href="%s">%s</a>', $product_permalink, $item->get_name()) : $item->get_name(), $item, $is_visible));
        }
        $refunded_qty = $order->get_qty_refunded_for_item($item_id);

        if ($refunded_qty) {
            $qty_display = '<del>' . esc_html($qty) . '</del> <ins>' . esc_html($qty - ($refunded_qty * -1)) . '</ins>';
        } else {
            $qty_display = esc_html($qty);
        }

        if ($item->get_product()->price > 0) {
            echo apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $qty_display) . '</strong>', $item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        }
        do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);

        wc_display_item_meta($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
        ?>
    </td>

    <td class="woocommerce-table__product-total product-total">
        <?php
        if ($item->get_product()->price > 0) {
            //custom code
            $_product_sub_total_wc = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($product, $item['quantity']), $item); // PHPCS: XSS ok.;
            if (!$_product_sub_total_wc) {
            }
        }
        if ($optional_item_id > 0 && $item->get_product()->price > 0) {
            $_product_sub_total_wc = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($product, $qty), $item); // PHPCS: XSS ok.;

        }
        if (!empty($_product_sub_total_wc)) {
            echo $_product_sub_total_wc;
        } else {
            echo "<span class='woocommerce-Price-amount amount'>--</span>";
        }

        ?>

    </td>
</tr>

<?php if (($show_purchase_note && $purchase_note) && $order->payment_method_title == "Factuur") : ?>

    <tr class="woocommerce-table__product-purchase-note product-purchase-note">

        <td colspan="2"><?php echo wpautop(do_shortcode(wp_kses_post($purchase_note))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                        ?></td>

    </tr>

<?php endif; ?>