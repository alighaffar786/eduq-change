<?php

/**
 * Shows an order item
 *
 * @package WooCommerce\Admin
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 */
global $woocommerce;
defined('ABSPATH') || exit;

$product      = $item->get_product();
if (!isset($product_link)) {
    $product_link = $product ? admin_url('post.php?post=' . $item->get_product_id() . '&action=edit') : '';
}
$thumbnail    = $product ? apply_filters('woocommerce_admin_order_item_thumbnail', $product->get_image('thumbnail', array('title' => ''), false), $item_id, $item) : '';
$row_class    = apply_filters('woocommerce_admin_html_order_item_class', !empty($class) ? $class : '', $item, $order);
$is_optional = isset($optional_courses[$product->get_ID()]) ? true : false;
?>
<tr class="item <?php echo esc_attr($row_class); ?> " data-order_item_id="<?php echo esc_attr($item_id); ?>">
    <?php if (!isset($hideisoption)) { ?>
        <td class="thumb <?php echo $css_class_service; ?>">
            <?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post($thumbnail) . '</div>'; ?>
        </td>
    <?php } ?>
    <!-- course name  -->
    <td style="<?php echo $css_class_service; ?>" class="name" data-sort-value="<?php echo esc_attr($item->get_name()); ?>">
        <?php
        $string = "text before + sign + text after";
        $parts = explode("+", wp_kses_post($item->get_name()));
        $new_string = trim($parts[0]);
        // echo ;
        // die;
        echo $product_link ? '<a style="text-decoration:none;" href="' . esc_url($product_link) . '" class="wc-order-item-name">' . wp_kses_post($item->get_name()) . '</a>' : '<div class="wc-order-item-name">' . $new_string . '</div>';
        if (!isset($hideisoption)) {
            if ($product && $product->get_sku()) {
                echo '<div class="wc-order-item-sku"><strong>' . esc_html__('SKU:', 'woocommerce') . '</strong> ' . esc_html($product->get_sku()) . '</div>';
            }
        }
        if ($item->get_variation_id()) {
            echo '<div class="wc-order-item-variation"><strong>' . esc_html__('Variation ID:', 'woocommerce') . '</strong> ';
            if ('product_variation' === get_post_type($item->get_variation_id())) {
                echo esc_html($item->get_variation_id());
            } else {
                /* translators: %s: variation id */
                printf(esc_html__('%s (No longer exists)', 'woocommerce'), esc_html($item->get_variation_id()));
            }
            echo '</div>';
        }
        ?>
        <input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr($item_id); ?>" />
        <input type="hidden" name="order_item_tax_class[<?php echo absint($item_id); ?>]" value="<?php echo esc_attr($item->get_tax_class()); ?>" />

        <?php if (!isset($hideisoption)) {
            do_action('woocommerce_before_order_itemmeta', $item_id, $item, $product);
        } ?>
        <?php if (!isset($hideisoption)) {
            require EDUQ_WC_PLUGIN_ADMIN_META_BOX_BASE . '/html-order-item-meta.php';
        } ?>
        <?php if (!isset($hideisoption)) {
            do_action('woocommerce_after_order_itemmeta', $item_id, $item, $product);
        } ?>
    </td>



    <td style='text-align: center;'><?php echo $courseDate ?></td>
    <?php do_action('woocommerce_admin_order_item_values', $product, $item, absint($item_id)); ?>
    <td style='text-align: right;' class="item_cost" data-sort-value="<?php echo esc_attr($order->get_item_subtotal($item, false, true)); ?>">
        <div class="view">
            <?php
            echo wc_price($order->get_item_subtotal($item, false, true), array('currency' => $order->get_currency())); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            ?>
        </div>
    </td>
    <td style='text-align: center;' class="quantity">
        <div class="view">
            <?php
            $quantity =  $item->get_quantity();
            if ($is_optional && !empty($parent_item)) {
                $quantity =  $parent_item->get_quantity();
            }
            echo '<small class="times">&times;</small> ' . esc_html($quantity);

            $refunded_qty = -1 * $order->get_qty_refunded_for_item($item_id);

            if ($refunded_qty) {
                echo '<small class="refunded">' . esc_html($refunded_qty * -1) . '</small>';
            }
            ?>
        </div>
        <?php
        $step = apply_filters('woocommerce_quantity_input_step', '1', $product);

        /**
         * Filter to change the product quantity stepping in the order editor of the admin area.
         *
         * @since   5.8.0
         * @param   string      $step    The current step amount to be used in the quantity editor.
         * @param   WC_Product  $product The product that is being edited.
         * @param   string      $context The context in which the quantity editor is shown, 'edit' or 'refund'.
         */
        $step_edit   = apply_filters('woocommerce_quantity_input_step_admin', $step, $product, 'edit');
        $step_refund = apply_filters('woocommerce_quantity_input_step_admin', $step, $product, 'refund');

        /**
         * Filter to change the product quantity minimum in the order editor of the admin area.
         *
         * @since   5.8.0
         * @param   string      $step    The current minimum amount to be used in the quantity editor.
         * @param   WC_Product  $product The product that is being edited.
         * @param   string      $context The context in which the quantity editor is shown, 'edit' or 'refund'.
         */
        $min_edit   = apply_filters('woocommerce_quantity_input_min_admin', '0', $product, 'edit');
        $min_refund = apply_filters('woocommerce_quantity_input_min_admin', '0', $product, 'refund');
        ?>
        <div class="edit" style="display: none;">
            <input type="number" step="<?php echo esc_attr($step_edit); ?>" min="<?php echo esc_attr($min_edit); ?>" autocomplete="off" name="order_item_qty[<?php echo absint($item_id); ?>]" placeholder="0" value="<?php echo esc_attr($item->get_quantity()); ?>" data-qty="<?php echo esc_attr($item->get_quantity()); ?>" size="4" class="quantity" />
        </div>
        <div class="refund" style="display: none;">
            <input type="number" step="<?php echo esc_attr($step_refund); ?>" min="<?php echo esc_attr($min_refund); ?>" max="<?php echo absint($item->get_quantity()); ?>" autocomplete="off" name="refund_order_item_qty[<?php echo absint($item_id); ?>]" placeholder="0" size="4" class="refund_order_item_qty" />
        </div>
    </td>
    <?php
    $tax_data = wc_tax_enabled() ? $item->get_taxes() : false;
    if ($tax_data) {
        foreach ($order_taxes as $tax_item) {
            $tax_item_id       = $tax_item->get_rate_id();
            $tax_item_total    = isset($tax_data['total'][$tax_item_id]) ? $tax_data['total'][$tax_item_id] : '';
            $tax_item_subtotal = isset($tax_data['subtotal'][$tax_item_id]) ? $tax_data['subtotal'][$tax_item_id] : '';

            if ('' !== $tax_item_subtotal) {
                $round_at_subtotal = 'yes' === get_option('woocommerce_tax_round_at_subtotal');
                $tax_item_total    = wc_round_tax_total($tax_item_total, $round_at_subtotal ? wc_get_rounding_precision() : null);
                $tax_item_subtotal = wc_round_tax_total($tax_item_subtotal, $round_at_subtotal ? wc_get_rounding_precision() : null);
            }
    ?>
            <td style='text-align: right;' class="line_tax">
                <div class="view">
                    <?php
                    if ('' !== $tax_item_total) {
                        $total_price =  $quantity * $order->get_item_subtotal($item, false, true);
                        $taxTotal = (21 / 100) * $total_price;
                        // echo $OKHA;
                        echo wc_price(wc_round_tax_total($taxTotal), array('currency' => $order->get_currency())); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    } else {
                        $taxTotal = 0;
                        echo '&ndash;';
                    }

                    $refunded = -1 * $order->get_tax_refunded_for_item($item_id, $tax_item_id);

                    if ($refunded) {
                        echo '<small class="refunded">' . wc_price($refunded, array('currency' => $order->get_currency())) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                </div>
                <?php
                if (!$is_optional) :
                ?>
                    <div class="edit" style="display: none;">
                        <div class="split-input">
                            <div class="input">
                                <label><?php esc_attr_e('Before discount', 'woocommerce'); ?></label>
                                <input type="text" name="line_subtotal_tax[<?php echo absint($item_id); ?>][<?php echo esc_attr($tax_item_id); ?>]" placeholder="<?php echo esc_attr(wc_format_localized_price(0)); ?>" value="<?php echo esc_attr(wc_format_localized_price($tax_item_subtotal)); ?>" class="line_subtotal_tax wc_input_price" data-subtotal_tax="<?php echo esc_attr(wc_format_localized_price($tax_item_subtotal)); ?>" data-tax_id="<?php echo esc_attr($tax_item_id); ?>" />
                            </div>
                            <div class="input">
                                <label><?php esc_attr_e('Total', 'woocommerce'); ?></label>
                                <input type="text" name="line_tax[<?php echo absint($item_id); ?>][<?php echo esc_attr($tax_item_id); ?>]" placeholder="<?php echo esc_attr(wc_format_localized_price(0)); ?>" value="<?php echo esc_attr(wc_format_localized_price($tax_item_total)); ?>" class="line_tax wc_input_price" data-total_tax="<?php echo esc_attr(wc_format_localized_price($tax_item_total)); ?>" data-tax_id="<?php echo esc_attr($tax_item_id); ?>" />
                            </div>
                        </div>
                    </div>
                <?php
                endif;
                ?>
                <div class="refund" style="display: none;">
                    <input type="text" name="refund_line_tax[<?php echo absint($item_id); ?>][<?php echo esc_attr($tax_item_id); ?>]" placeholder="<?php echo esc_attr(wc_format_localized_price(0)); ?>" class="refund_line_tax wc_input_price" data-tax_id="<?php echo esc_attr($tax_item_id); ?>" />
                </div>
            </td>
    <?php
        }
    }
    ?>
    <td style='text-align: right;' class="line_cost" data-sort-value="<?php echo esc_attr($item->get_total()); ?>">
        <div class="view">
            <?php
            // $item->get_total()
            $total_price =  $quantity * $order->get_item_subtotal($item, false, true);
            $total_price += $taxTotal;
            // if ($is_optional && !empty($parent_item)) {
            //     $total_price =  ;
            // }
            echo  wc_price($total_price, array('currency' => $order->get_currency())); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped


            if ($item->get_subtotal() !== $item->get_total()) {
                /* translators: %s: discount amount */
                echo '<span class="wc-order-item-discount">' . sprintf(esc_html__('%s discount', 'woocommerce'), wc_price(wc_format_decimal($item->get_subtotal() - $item->get_total(), ''), array('currency' => $order->get_currency()))) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            $refunded = -1 * $order->get_total_refunded_for_item($item_id);

            if ($refunded) {
                echo '<small class="refunded">' . wc_price($refunded, array('currency' => $order->get_currency())) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            ?>
        </div>
        <div class="edit" style="display: none;">
            <div class="split-input">
                <div class="input">
                    <label><?php esc_attr_e('Before discount', 'woocommerce'); ?></label>
                    <input type="text" name="line_subtotal[<?php echo absint($item_id); ?>]" placeholder="<?php echo esc_attr(wc_format_localized_price(0)); ?>" value="<?php echo esc_attr(wc_format_localized_price($item->get_subtotal())); ?>" class="line_subtotal wc_input_price" data-subtotal="<?php echo esc_attr(wc_format_localized_price($item->get_subtotal())); ?>" />
                </div>
                <div class="input">
                    <label><?php esc_attr_e('Total', 'woocommerce'); ?></label>
                    <input type="text" name="line_total[<?php echo absint($item_id); ?>]" placeholder="<?php echo esc_attr(wc_format_localized_price(0)); ?>" value="<?php echo esc_attr(wc_format_localized_price($item->get_total())); ?>" class="line_total wc_input_price" data-tip="<?php esc_attr_e('After pre-tax discounts.', 'woocommerce'); ?>" data-total="<?php echo esc_attr(wc_format_localized_price($item->get_total())); ?>" />
                </div>
            </div>
        </div>
        <div class="refund" style="display: none;">
            <input type="text" name="refund_line_total[<?php echo absint($item_id); ?>]" placeholder="<?php echo esc_attr(wc_format_localized_price(0)); ?>" class="refund_line_total wc_input_price" />
        </div>
    </td>
    <?php
    if (!isset($hideisoption)) {
    ?>
        <td class="wc-order-edit-line-item">
            <?php
            if (!$is_optional) :
            ?>
                <div class="wc-order-edit-line-item-actions">
                    <?php if ($order->is_editable()) : ?>
                        <a style="text-decoration:none;" class="edit-order-item tips" href="#" data-tip="<?php esc_attr_e('Edit item', 'woocommerce'); ?>"></a><a style="text-decoration:none;" class="delete-order-item tips" href="#" data-tip="<?php esc_attr_e('Delete item', 'woocommerce'); ?>"></a>
                    <?php endif; ?>
                </div>
            <?php
            endif;
            ?>
        </td>
    <?php
    }
    ?>
</tr>