<?php
defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
class EDU_WC_Admin_Meta_Boxes {


    /**
     * Is meta boxes saved once?
     *
     * @var boolean
     */
    private static $saved_meta_boxes = false;

    /**
     * Meta box error messages.
     *
     * @var array
     */
    public static $meta_box_errors = array();

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 11 );
        add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 21 );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 31 );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

        /**
         * Save Order Meta Boxes.
         *
         * In order:
         *      Save the order items.
         *      Save the order totals.
         *      Save the order downloads.
         *      Save order data - also updates status and sends out admin emails if needed. Last to show latest data.
         *      Save actions - sends out other emails. Last to show latest data.
         */
        add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Items::save', 10 );
        add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Downloads::save', 30, 2 );
        add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Data::save', 40 );
        add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Actions::save', 50, 2 );

        // Save Product Meta Boxes.
        add_action( 'woocommerce_process_product_meta', 'WC_Meta_Box_Product_Data::save', 10, 2 );
        add_action( 'woocommerce_process_product_meta', 'WC_Meta_Box_Product_Images::save', 20, 2 );

        // Save Coupon Meta Boxes.
        add_action( 'woocommerce_process_shop_coupon_meta', 'WC_Meta_Box_Coupon_Data::save', 10, 2 );

        // Save Rating Meta Boxes.
        add_filter( 'wp_update_comment_data', 'WC_Meta_Box_Product_Reviews::save', 1 );

        // Error handling (for showing errors from meta boxes on next page load).
        add_action( 'admin_notices', array( $this, 'output_errors' ) );
        add_action( 'shutdown', array( $this, 'save_errors' ) );

        add_filter( 'theme_product_templates', array( $this, 'remove_block_templates' ), 10, 1 );
    }

    /**
     * Add an error message.
     *
     * @param string $text Error to add.
     */
    public static function add_error( $text ) {
        self::$meta_box_errors[] = $text;
    }

    /**
     * Save errors to an option.
     */
    public function save_errors() {
        update_option( 'woocommerce_meta_box_errors', self::$meta_box_errors );
    }

    /**
     * Show any stored error messages.
     */
    public function output_errors() {
        $errors = array_filter( (array) get_option( 'woocommerce_meta_box_errors' ) );

        if ( ! empty( $errors ) ) {

            echo '<div id="woocommerce_errors" class="error notice is-dismissible">';

            foreach ( $errors as $error ) {
                echo '<p>' . wp_kses_post( $error ) . '</p>';
            }

            echo '</div>';

            // Clear.
            delete_option( 'woocommerce_meta_box_errors' );
        }
    }

    /**
     * Add WC Meta boxes.
     */
    public function add_meta_boxes() {
        $screen    = get_current_screen();
        $screen_id = $screen ? $screen->id : '';

        // Products.
        add_meta_box( 'postexcerpt', __( 'Product short description', 'woocommerce' ), 'WC_Meta_Box_Product_Short_Description::output', 'product', 'normal' );
        add_meta_box( 'woocommerce-product-data', __( 'Product data', 'woocommerce' ), 'WC_Meta_Box_Product_Data::output', 'product', 'normal', 'high' );
        add_meta_box( 'woocommerce-product-images', __( 'Product gallery', 'woocommerce' ), 'WC_Meta_Box_Product_Images::output', 'product', 'side', 'low' );

        // Orders.

        foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
            $order_type_object = get_post_type_object( $type );
            /* Translators: %s order type name. */
            add_meta_box( 'woocommerce-order-data', sprintf( __( '%s data', 'woocommerce' ), $order_type_object->labels->singular_name ), 'EDU_WC_Admin_Meta_Box_Order_Data::output', $type, 'normal', 'high' );

            add_meta_box( 'woocommerce-order-items', __( 'Items', 'woocommerce' ), 'EDU_WC_Meta_Box_Order_Items::output', $type, 'normal', 'high' );
            /* Translators: %s order type name. */
            add_meta_box( 'woocommerce-order-notes', sprintf( __( '%s notes', 'woocommerce' ), $order_type_object->labels->singular_name ), 'WC_Meta_Box_Order_Notes::output', $type, 'side', 'default' );
            add_meta_box( 'woocommerce-order-downloads', __( 'Downloadable product permissions', 'woocommerce' ) . wc_help_tip( __( 'Note: Permissions for order items will automatically be granted when the order status changes to processing/completed.', 'woocommerce' ) ), 'WC_Meta_Box_Order_Downloads::output', $type, 'normal', 'default' );
            /* Translators: %s order type name. */
            add_meta_box( 'woocommerce-order-actions', sprintf( __( '%s actions', 'woocommerce' ), $order_type_object->labels->singular_name ), 'WC_Meta_Box_Order_Actions::output', $type, 'side', 'high' );
        }

        // Coupons.
        add_meta_box( 'woocommerce-coupon-data', __( 'Coupon data', 'woocommerce' ), 'WC_Meta_Box_Coupon_Data::output', 'shop_coupon', 'normal', 'high' );

        // Comment rating.
        if ( 'comment' === $screen_id && isset( $_GET['c'] ) && metadata_exists( 'comment', wc_clean( wp_unslash( $_GET['c'] ) ), 'rating' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            add_meta_box( 'woocommerce-rating', __( 'Rating', 'woocommerce' ), 'WC_Meta_Box_Product_Reviews::output', 'comment', 'normal', 'high' );
        }
    }

    /**
     * Remove bloat.
     */
    public function remove_meta_boxes() {
        remove_meta_box( 'postexcerpt', 'product', 'normal' );
        remove_meta_box( 'product_shipping_classdiv', 'product', 'side' );
        remove_meta_box( 'commentsdiv', 'product', 'normal' );
        remove_meta_box( 'commentstatusdiv', 'product', 'side' );
        remove_meta_box( 'commentstatusdiv', 'product', 'normal' );
        remove_meta_box( 'woothemes-settings', 'shop_coupon', 'normal' );
        remove_meta_box( 'commentstatusdiv', 'shop_coupon', 'normal' );
        remove_meta_box( 'slugdiv', 'shop_coupon', 'normal' );

        foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
            remove_meta_box( 'commentsdiv', $type, 'normal' );
            remove_meta_box( 'woothemes-settings', $type, 'normal' );
            remove_meta_box( 'commentstatusdiv', $type, 'normal' );
            remove_meta_box( 'slugdiv', $type, 'normal' );
            remove_meta_box( 'submitdiv', $type, 'side' );



            remove_meta_box( 'woocommerce-order-data', $type, 'normal' );
            remove_meta_box( 'woocommerce-order-items', $type, 'normal' );
            remove_meta_box( 'woocommerce-order-notes', $type, 'side' );
            remove_meta_box( 'woocommerce-order-downloads', $type, 'normal' );
            remove_meta_box( 'woocommerce-order-actions', $type, 'side' );
        }
    }

    /**
     * Rename core meta boxes.
     */
    public function rename_meta_boxes() {
        global $post;

        // Comments/Reviews.
        if ( isset( $post ) && ( 'publish' === $post->post_status || 'private' === $post->post_status ) && post_type_supports( 'product', 'comments' ) ) {
            remove_meta_box( 'commentsdiv', 'product', 'normal' );
            add_meta_box( 'commentsdiv', __( 'Reviews', 'woocommerce' ), 'post_comment_meta_box', 'product', 'normal' );
        }
    }

    /**
     * Check if we're saving, the trigger an action based on the post type.
     *
     * @param  int    $post_id Post ID.
     * @param  object $post Post object.
     */
    public function save_meta_boxes( $post_id, $post ) {
        $post_id = absint( $post_id );

        // $post_id and $post are required
        if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
            return;
        }

        // Dont' save meta boxes for revisions or autosaves.
        if ( Constants::is_true( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
            return;
        }

        // Check the nonce.
        if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
        if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
            return;
        }

        // Check user has permission to edit.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // We need this save event to run once to avoid potential endless loops. This would have been perfect:
        // remove_action( current_filter(), __METHOD__ );
        // But cannot be used due to https://github.com/woocommerce/woocommerce/issues/6485
        // When that is patched in core we can use the above.
        self::$saved_meta_boxes = true;

        // Check the post type.
        if ( in_array( $post->post_type, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
            do_action( 'woocommerce_process_shop_order_meta', $post_id, $post );
        } elseif ( in_array( $post->post_type, array( 'product', 'shop_coupon' ), true ) ) {
            do_action( 'woocommerce_process_' . $post->post_type . '_meta', $post_id, $post );
        }
    }

    /**
     * Remove block-based templates from the list of available templates for products.
     *
     * @param string[] $templates Array of template header names keyed by the template file name.
     *
     * @return string[] Templates array excluding block-based templates.
     */
    public function remove_block_templates( $templates ) {
        if ( count( $templates ) === 0 || ! function_exists( 'gutenberg_get_block_template' ) ) {
            return $templates;
        }

        $theme              = wp_get_theme()->get_stylesheet();
        $filtered_templates = array();

        foreach ( $templates as $template_key => $template_name ) {
            $gutenberg_template = gutenberg_get_block_template( $theme . '//' . $template_key );

            if ( ! $gutenberg_template ) {
                $filtered_templates[ $template_key ] = $template_name;
            }
        }

        return $filtered_templates;
    }

}