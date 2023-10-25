<?php

/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment-method.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}
$is_company = -1;
$css_hidden_class = esc_attr($gateway->id) == 'bacs' ? ' hidden' : ' ';
if(!empty($_POST["post_data"])){
    $explode = explode("&",$_POST["post_data"]);
    foreach($explode as $d){
        if(strpos($d,"iscompany")!==false){
            $is_company = explode("=",$d)[1];
        }
    }
}
if($is_company == 1){
    $css_hidden_class = esc_attr($gateway->id) == 'bacs' ? ' ' : ' ';
}


?>
<li class="css_class wc_payment_method payment_method_<?php echo esc_attr($gateway->id);
                                                        echo $css_hidden_class; ?>">
    <input id="payment_method_<?php echo esc_attr($gateway->id); ?>" type="radio" class="input-radio" name="payment_method" data-value="<?php echo esc_attr($gateway->id); ?>" value="<?php echo esc_attr($gateway->id); ?>" <?php checked($gateway->chosen, true); ?> data-order_button_text="<?php echo esc_attr($gateway->order_button_text); ?>" />

    <label for="payment_method_<?php echo esc_attr($gateway->id); ?>">
        <?php echo $gateway->get_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?><?php echo $gateway->get_icon(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
    </label>
    <?php if ($gateway->has_fields() || $gateway->get_description()) : ?>
        <div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" <?php if (!$gateway->chosen) : /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>style="display:none;" <?php endif; /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>>
            <?php $gateway->payment_fields(); ?>
        </div>
    <?php endif; ?>
</li>
<script type="text/javascript">
    /*
    * Commenting all JS no need
    setTimeout(function() {

    }, 5000);

    jQuery("input#terms[type='checkbox']").click(function() {
        resetCashOndelivery();
    });
    jQuery("input#terms[type='checkbox']").trigger("click");

    jQuery("#payment_method_multisafepay_ideal").click(function() {
        if (jQuery("#payment_method_multisafepay_ideal").prop('checked') === true) {
            var multisafepay_ideal = jQuery("input.input-radio#payment_method_multisafepay_ideal").data("value");
            jQuery("input.input-radio#payment_method_multisafepay_ideal").val(multisafepay_ideal);
            jQuery("input.input-radio#payment_method_multisafepay_ideal").attr("value", multisafepay_ideal);
            jQuery("input[name='payment_method']").val(multisafepay_ideal);
        }
    });
    jQuery("#payment_method_bacs").click(function() {
        if (jQuery("#payment_method_bacs").prop('checked') === true) {
            var bacs_value = jQuery("input.input-radio#payment_method_bacs").data("value");
            jQuery("input.input-radio#payment_method_bacs").val(bacs_value);
            jQuery("input.input-radio#payment_method_bacs").attr("value", bacs_value);
        }
    });

     */
</script>