<?php

/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see    https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.2
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!$order = wc_get_order($order_id)) {

    return;
}
wp_enqueue_style('edu_wizard_css', EDUQ_COURSE_ASSETS_URL . 'styles/order-detail.css', false, '1.2');
wp_enqueue_style('edu_wizard_css', EDUQ_COURSE_ASSETS_URL . 'styles/jquery.steps.css', false, '1.9');


$order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$show_purchase_note = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', array('completed', 'processing')));
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads = $order->get_downloadable_items();
$show_downloads = $order->has_downloadable_item() && $order->is_download_permitted();

if ($show_downloads) {
    wc_get_template('order/order-downloads.php', array('downloads' => $downloads, 'show_title' => true));
}
$optional_courses = getOptionalCoursesFromWp();
list($order_items, $service_items) = extractOrderOptionalItems($order_items);

add_filter('woocommerce_display_item_meta', 'woocommerce_display_item_meta_override', 10, 3);

function woocommerce_display_item_meta_override($formatted_meta, $item, $args)
{

    $html = "";
    foreach ($item->get_all_formatted_meta_data() as $meta_id => $meta) {
        if (!in_array($meta->key, array('is_optional'))) {
            $value = $args['autop'] ? wp_kses_post($meta->display_value) : wp_kses_post(make_clickable(trim($meta->display_value)));
            $strings[] = $args['label_before'] . wp_kses_post($meta->display_key) . $args['label_after'] . $value;
        }
    }

    if ($strings) {
        $html = $args['before'] . implode($args['separator'], $strings) . $args['after'];
    }
    return $html;
}
// add_filter('woocommerce_get_order_item_totals', 'remove_subtotal_from_orders_total_lines', 100, 1);
// function remove_subtotal_from_orders_total_lines($total_rows)
// {
//     unset($total_rows['cart_subtotal']);
//     return $total_rows;
// }








$total_rows = $order->get_order_item_totals();
add_filter('woocommerce_get_order_item_totals', 'reordering_order_item_totals', 10, 3);
function reordering_order_item_totals($total_rows)
{
    // remove subtotal row
    unset($total_rows['cart_subtotal']);

    // Only on "order received" thankyou page
    if (!is_wc_endpoint_url('order-received'))
        return $total_rows;

    $sorted_items_end  = array('order_total', 'payment_method');
    $sorted_total_rows = array(); // Initializing

    // Loop through sorted totals item keys
    foreach ($sorted_items_end as $item_key) {
        if (isset($total_rows[$item_key])) {
            $sorted_total_rows[$item_key] = $total_rows[$item_key]; // Save sorted data in a new array
            unset($total_rows[$item_key]); // Remove sorted data from default array
        }
    }

    return array_merge($total_rows, $sorted_total_rows); // merge arrays
}
?>
<section class="woocommerce-order-details">
    <?php do_action('woocommerce_order_details_before_order_table', $order); ?>

    <h2 class="woocommerce-order-details__title"><?php _e('Jouw bestelling', 'woocommerce'); ?></h2>

    <table id="tankyouTable" class="woocommerce-table woocommerce-table--order-details shop_table order_details">

        <thead>
            <tr>
                <th style="text-align: left;" class="woocommerce-table__product-name product-name"><?php _e('Product', 'woocommerce'); ?></th>
                <th style="text-align: right;" class="woocommerce-table__product-table product-total"><?php _e('Total', 'woocommerce'); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php
            do_action('woocommerce_order_details_before_order_table_items', $order);

            //            $optinal_order_items =

            foreach ($order_items as $item_id => $item) {

                $product = $item->get_product();

                $item_optional_courses = wc_get_order_item_meta($item_id, 'optional_courses', true);
                $css_class_service = "";
                $qty = 0;
                if (!isset($optional_courses[$product->get_ID()])) {
                    $qty = $item->get_quantity();
                    wc_get_template('order/order-details-item.php', [
                        'order' => $order,
                        'item_id' => $item_id,
                        'item' => $item,
                        'show_purchase_note' => $show_purchase_note,
                        'purchase_note' => $product ? $product->get_purchase_note() : '',
                        'product' => $product,
                        'optional_item_id' => 0,
                        'item_qty' => $qty,
                        "css_class" => $css_class_service
                    ]);
                }
                if (!empty($item_optional_courses)) {
                    foreach ($item_optional_courses as $optional_item_id) {
                        $css_class_service = "service-item";
                        $item = $service_items[$optional_item_id]["item"];
                        $item_id = $service_items[$optional_item_id]["item_id"];
                        $product = $item->get_product();
                        wc_get_template('order/order-details-item.php', [
                            'order' => $order,
                            'item_id' => $item_id,
                            'item' => $item,
                            'show_purchase_note' => $show_purchase_note,
                            'purchase_note' => $product ? $product->get_purchase_note() : '',
                            'product' => $product,
                            'optional_item_id' => $optional_item_id,
                            'item_qty' => $qty,
                            "css_class" => $css_class_service
                        ]);
                    }
                }
            }

            do_action('woocommerce_order_details_after_order_table_items', $order);
            ?>
        </tbody>

        <tfoot>
            <?php
            $search = array("inclusief", "21%");
            $replace = array("incl", "btw");

            foreach ($order->get_order_item_totals() as $key => $total) {
            ?>
                <tr>
                    <th scope="row"><?php echo $total['label']; ?></th>
                    <td class="details-total">
                        <?php echo ('payment_method' === $key) ? esc_html($total['value'])  : str_replace('inclusief', "incl", str_replace('21%', "btw", $total['value'])); ?>
                    </td>

                    <!-- <td class="details-total"><?php echo ('payment_method' === $key) ? esc_html($total['value'])  : str_replace('inclusief', "incl", str_replace('21%', "btw", $total['value'])); ?></td> -->
                </tr>
            <?php
            }
            ?>
            <?php if ($order->get_customer_note()) : ?>
                <tr>
                    <th><?php _e('Note:', 'woocommerce'); ?></th>
                    <td><?php echo wptexturize($order->get_customer_note()); ?></td>
                </tr>
            <?php endif; ?>
        </tfoot>
    </table>

    <?php do_action('woocommerce_order_details_after_order_table', $order); ?>
</section>

<?php
if ($show_customer_details) {
    wc_get_template('order/order-details-customer.php', array('order' => $order));
}
