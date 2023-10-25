<?php

/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     3.2.0
 */
if (!defined('ABSPATH')) {
    exit;
}
// thankyou paage title script

wp_enqueue_style('edu_wizard_css', EDUQ_COURSE_ASSETS_URL . 'styles/jquery.steps.css', false, '2.6',);
wp_enqueue_style('edu_wizard_responsive', EDUQ_COURSE_ASSETS_URL . 'styles/thankyou.css', false, '2.1');


//wp_register_script("edu_thankyou_heading", EDUQ_COURSE_ASSETS_URL . 'scripts/utils-mock.js');
//wp_localize_script('edu_thankyou_heading', 'thank_heading', THANKYOU_HEADING);
//wp_enqueue_script('edu_thankyou_heading');
//
//wp_enqueue_script('edu_thankyou_title', EDUQ_COURSE_ASSETS_URL . 'scripts/thankyou.js', false, '1.3', true);

echo renderReceiptStepBar();

?>
<div class="woocommerce-order child-theme">

    <?php if ($order) : ?>

        <?php if ($order->has_status('failed')) : ?>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php _e('Helaas kan uw bestelling niet worden verwerkt omdat de bank / verkoper uw transactie heeft geweigerd. Probeer uw aankoop opnieuw.', 'woocommerce'); ?></p>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay"><?php _e('Pay', 'woocommerce') ?></a>
                <?php if (is_user_logged_in()) : ?>
                    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="button pay"><?php _e('My account', 'woocommerce'); ?></a>
                <?php endif; ?>
            </p>

        <?php else : ?>
            <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_t
            ext', __('Bedankt voor het boeken van een cursus bij Eduq Opleidingen! <br/> Je ontvangt zo snel mogelijk een bevestiging en factuur in je inbox.', 'woocommerce'), $order); ?></p>
            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details ss">
                <li class="woocommerce-order-overview__order order">
                    <?php _e('Order number:', 'woocommerce'); ?>
                    <strong><?php echo $order->get_order_number(); ?></strong>
                </li>
                <li class="woocommerce-order-overview__date date">
                    <?php _e('Date:', 'woocommerce'); ?>
                    <strong><?php echo wc_format_datetime($order->get_date_created()); ?></strong>
                </li>
                <?php if (is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email()) : ?>
                    <li class="woocommerce-order-overview__email email" style="display: none;">
                        <?php _e('Email:', 'woocommerce'); ?>
                        <strong><?php echo $order->get_billing_email(); ?></strong>
                    </li>
                <?php endif; ?>
                <li class="woocommerce-order-overview__total total">
                    <?php _e('Total:', 'woocommerce'); ?>
                    <strong><?php echo $order->get_formatted_order_total(); ?></strong>
                </li>
                <?php if ($order->get_payment_method_title()) : ?>
                    <li class="woocommerce-order-overview__payment-method method">
                        <?php _e('Payment method:', 'woocommerce'); ?>
                        <strong><?php echo wp_kses_post($order->get_payment_method_title()); ?></strong>
                    </li>
                <?php endif; ?>

            </ul>

        <?php endif; ?>

        <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
        <?php do_action('woocommerce_thankyou', $order->get_id()); ?>

    <?php else : ?>

        <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_text', __('Bedankt voor het boeken van een cursus bij Eduq Opleidingen! Je ontvangt zo snel mogelijk een bevestiging en factuur in je inbox.', 'woocommerce'), null); ?></p>

    <?php endif; ?>

</div>