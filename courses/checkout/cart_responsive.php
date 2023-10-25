<form class="woocommerce-cart-form mobile-view" action="<?php echo $checkout_action; ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>

    <table class="shop_table shop_table_edu_responsive shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
            <tr class="mobile-res-tr">
                <th class="product-remove  product-edit">&nbsp;</th>
                <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
                <th class="product-subtotal"><?php esc_html_e('Total', 'woocommerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php do_action('woocommerce_before_cart_contents'); ?>

            <?php
            $_tax = new WC_Tax();
            foreach ($cart_items as $cart_item_key => $cart_item) {
                if (!isset($cart_services[$cart_item['product_id']])) {
                    include(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_cart_responsive.php');
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
                    <?php if (wc_coupons_enabled()) { ?>
                        <div class="coupon">
                            <label for="coupon_code"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
                            <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
                            <?php do_action('woocommerce_cart_coupon'); ?>
                        </div>
                    <?php } ?>
                </td>
            </tr>

            <?php do_action('woocommerce_after_cart_contents'); ?>
        </tbody>
    </table>
    <?php do_action('woocommerce_after_cart_table'); ?>
</form>