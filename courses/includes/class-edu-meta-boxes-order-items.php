<?php

class EDU_WC_Meta_Box_Order_Items
{

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {
        global $post, $thepostid, $theorder;

        if ( ! is_int( $thepostid ) ) {
            $thepostid = $post->ID;
        }

        if ( ! is_object( $theorder ) ) {
            $theorder = wc_get_order( $thepostid );
        }

        $order = $theorder;
        $data  = get_post_meta( $post->ID );



        include EDUQ_COURSE_PLUGIN_VIEWS_BASE . 'html-order-items.php';
    }
}