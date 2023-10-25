<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */
defined('ABSPATH') || exit;

global $product;
//echo '<pre>';
//print_r($product);
//echo '</pre>';
$short_description = apply_filters('woocommerce_short_description', $product->description);

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('custom-product', $product); ?>>
    <div class="row">
        <div class="col-md-12">
            <div class="woocommerce-product-details__short-description">
                <?php echo $short_description; ?>
            </div>
        </div>
    </div>
</div>
<?php
echo do_shortcode('[sc_team_member name="Member Name" position="Opleidingsadviseur" mobile="+31(0)20 - 2441500" email="janet@eduq.nl" buttontext="Vraag direct offerte aan" buttonlink="/offerte/"]');
?>
<?php do_action('woocommerce_after_single_product'); ?>
