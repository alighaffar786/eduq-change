<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */


defined('ABSPATH') || exit;
wp_enqueue_style('edu_wizard_responsive', EDUQ_COURSE_ASSETS_URL . 'styles/cart-item.css', false, '1.6');
wp_enqueue_script('edu_wizard_flow_js', EDUQ_COURSE_ASSETS_URL . 'scripts/cart.js', false, '1.0', true);
wp_enqueue_style('edu_wizard_css', EDUQ_COURSE_ASSETS_URL . 'styles/jquery.steps.css', false, '2.9');
wp_enqueue_style('edu_cart_responsive', EDUQ_COURSE_ASSETS_URL . 'styles/cart-responsive.css', false, '1.9');

do_action('woocommerce_before_cart');

$checkout_action = esc_url(wc_get_cart_url());
if (isset($_GET["course_id"])) {
    $product = wc_get_product($_GET["course_id"]);
    echo renderCartPageBreadCrumb($product);
    $checkout_action .= "?course_id=" . $_GET["course_id"];
} else {
    echo renderCartStepBar();
}
$cart_items = WC()->cart->get_cart();


list($cart_services, $service_cart_items, $cart_items) = reload_cart_services($cart_items);

if (!function_exists('wc_cart_totals_order_total_html_override')) {
    function wc_cart_totals_order_total_html_override()
    {

        $value = '<strong>' . WC()->cart->get_total() . '</strong> ';
        //$value = '<strong>' . WC()->cart->get_subtotal() . '</strong> ';

        // If prices are tax inclusive, show taxes here.
        if (wc_tax_enabled() && WC()->cart->display_prices_including_tax()) {

            $tax_string_array = array();
            $cart_tax_totals = WC()->cart->get_tax_totals();

            if (get_option('woocommerce_tax_total_display') === 'itemized') {

                foreach ($cart_tax_totals as $code => $tax) {
                    if (strpos($tax->label, "%") !== false) {
                        $tax->label = "btw";
                    }
                    $tax_string_array[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
                }
            } elseif (!empty($cart_tax_totals)) {

                $tax_string_array[] = sprintf('%s %s', wc_price(WC()->cart->get_taxes_total(true, true)), WC()->countries->tax_or_vat());
            }

            if (!empty($tax_string_array)) {
                $taxable_address = WC()->customer->get_taxable_address();
                if (WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping()) {
                    $country = WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]];
                    /* translators: 1: tax amount 2: country name */
                    $tax_text = wp_kses_post(sprintf(__('(includes %1$s estimated for %2$s)', 'woocommerce'), implode(', ', $tax_string_array), $country));
                    $to_be_replaced = 'geschat voor';
                    $tax_text = str_replace($to_be_replaced,'',$tax_text);
                } else {
                    /* translators: %s: tax amount */

                    $tax_text = wp_kses_post(sprintf(__('(incl. %s)', 'woocommerce'), implode(', ', $tax_string_array)));
                }

                $value .= '<small class="includes_tax">' . $tax_text . '</small>';
            }
        }

        echo apply_filters('woocommerce_cart_totals_order_total_html', $value); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    add_filter('wc_cart_totals_order_total_html', 'wc_cart_totals_order_total_html_override');
}
$is_mobile = false;
$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

$content = $useragent;
$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/user_agent.txt", "wb");
fwrite($fp, substr($useragent, 0, 4));
fwrite($fp, "\n");
fwrite($fp, $useragent);

if (wp_is_mobile()) {
    $is_mobile = true;
}
fwrite($fp, "\n");
fwrite($fp, $is_mobile);
fclose($fp);

//if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
//    $is_mobile = true;
//}


?>
<?php
if (!$is_mobile) {
?>
    <form class="woocommerce-cart-form" action="<?php echo $checkout_action; ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>

        <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
            <thead>
                <tr>
                    <th class="product-remove">&nbsp;</th>
                    <!--<th class="product-thumbnail">&nbsp;</th>-->
                    <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
                    <th class="product-price"><?php esc_html_e('Price', 'woocommerce'); ?></th>
                    <th class="product-quantity"><?php esc_html_e(COURSE_CART_LBL["no_of_students_lbl"], 'woocommerce'); ?></th>
                    <th class="product-edit"></th>
                    <th class="product-subtotal"><?php esc_html_e('Total', 'woocommerce'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php do_action('woocommerce_before_cart_contents'); ?>

                <?php
                $_tax = new WC_Tax();
                foreach ($cart_items as $cart_item_key => $cart_item) {
                    if (!isset($cart_services[$cart_item['product_id']])) {
                        include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_cart.php');
                    }
                } // end of loop
                ?>

                <?php do_action('woocommerce_cart_contents'); ?>

                <tr>
                    <td colspan="6" class="actions">


                        <a href="<?php echo get_permalink(CHECKOUT_PAGE_ID) . "?process=buy&course_id=0&event_id=0" ?>" class="redirect-btn"><?php echo COURSE_CART_LBL["redirect_btn_lbl"]; ?></a>
                        <?php
                        if (!empty($_REQUEST["course_id"]) && !empty($_REQUEST["event_id"]) && !empty($_REQUEST["cart_item_key"])) {
                            $_back_btn_link = get_permalink(CHECKOUT_PAGE_ID) . "?process=buy&course_id=" . $_REQUEST["course_id"] . "&event_id=" . $_REQUEST["event_id"];
                            $_back_btn_link .= "&cart_key=" . $_REQUEST["cart_item_key"];
                            //echo "<a href='" . $_back_btn_link . "' class='button redirect-btn2'>" . COURSE_CART_LBL["back_btn_lbl"] . "<a/>";
                        }
                        ?>
                        <button type="submit" class="button hidden" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>

                        <?php do_action('woocommerce_cart_actions'); ?>

                        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                        <?php if (wc_coupons_enabled()) { ?>
                            <div class="coupon">
                                <label for="coupon_code"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
                                <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
                                <?php do_action('woocommerce_cart_coupon'); ?>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                <tr>

                </tr>

                <?php do_action('woocommerce_after_cart_contents'); ?>
            </tbody>
        </table>
        <?php do_action('woocommerce_after_cart_table'); ?>
    </form>
<?php
} else {
    include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . 'cart_responsive.php');
?>
<?php
}
?>
<div class="cart-collaterals">
    <?php
    /**
     * Cart collaterals hook.
     *
     * @hooked woocommerce_cross_sell_display
     * @hooked woocommerce_cart_totals - 10
     */
    do_action('woocommerce_cart_collaterals');
    ?>
</div>

<?php do_action('woocommerce_after_cart');
