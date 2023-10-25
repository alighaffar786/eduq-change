<?php
add_action('add_meta_boxes', 'business_post_custom_meta_box');
function business_post_custom_meta_box(): void
{
    add_meta_box(

        'vac_training_metabox',                 // Unique ID
        "vac_training",      // Box title
        "vac_training_metaboxhtml",  // Content callback, must be of type callable
        'product'                            // Post type
    );
}
function vac_training_metaboxhtml()
{
    //include_once("/home/syed/work/projects/eduq-live/wp-content/plugins/courses/course-list.php");
    course_list($_GET["post"]);
}

add_action('add_meta_boxes', 'user_info_custom_meta_box');
function user_info_custom_meta_box(): void
{
    add_meta_box(

        'user_info_metabox',                 // Unique ID
        "gebruikers informatie",      // Box title
        "user_info_metaboxhtml",  // Content callback, must be of type callable
        'shop_order'                            // Post type
    );
}
function user_info_metaboxhtml()
{
    include EDUQ_COURSE_PLUGIN_VIEWS_BASE . '/html-user-info.php';
}
