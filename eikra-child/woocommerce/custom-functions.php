<?php
/*
 * Single Product Edit Template
 */

function get_single_product_template($single_template) {
    global $post;
    if ($post->post_type == 'product') {
        $single_template = get_stylesheet_directory_uri() . '/woocommerce/single-product-edit.php';
    }
    return $single_template;
}

add_filter('single_template', 'get_single_product_template');
?>