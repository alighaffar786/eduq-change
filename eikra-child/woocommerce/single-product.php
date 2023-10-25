<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop');
?>
<?php while (have_posts()) : the_post(); ?>
    <div class="container single-product-edit">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <?php //wc_get_template_part('content', 'single-product'); ?>
                <div id="product-<?php the_ID(); ?>" <?php wc_product_class('custom-product', $product); ?>>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="woocommerce-product-content">
                                <?php
                                global $product;
                                $short_description = apply_filters('woocommerce_short_description', $product->description);
                                echo $short_description;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; // end of the loop.  ?>
<?php
get_footer('shop');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
