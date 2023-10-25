<?php
/*
 * SC_TEAM MEMBER
 * SHORTCODE [team member="4"]
 */

function sb_team($atts) {
    extract(
            shortcode_atts(array(
        'member' => $member,
                    ), $atts)
    );
    ob_start();
    ?>
    <?php $sb_team = new WP_Query(array('post_type' => 'team', 'posts_per_page' => $member)); ?>
    <?php if ($sb_team->have_posts()) : ?>
        <div class="container team-container">
            <div class="vc_row wpb_row vc_row-fluid">
                <?php while ($sb_team->have_posts()) : $sb_team->the_post(); ?>
                    <?php
                    $team_member_id = get_the_id();
                    $team_member = get_post($team_member_id);
                    $team_member_name = $team_member->post_title;
                    $team_member_position = get_field('position', $team_member_id);
                    $team_member_mobile = get_field('mobile', $team_member_id);
                    $team_member_email = get_field('email', $team_member_id);
                    $team_member_buttontext = get_field('buttontext', $team_member_id);
                    $team_member_buttonlink = get_field('buttonlink', $team_member_id);
                    ?>
                    <div class="wpb_column team-member-wrapper vc_col-sm-3 vc_col-xs-6">
                        <div class="team-member">
                            <?php echo get_the_post_thumbnail($team_member_id, 'large', array('class' => 'img-fluid')); ?>
                            <div class="name"><?php echo $team_member_name; ?></div>
                            <p class="position"><?php echo $team_member_position; ?></p>
                            <p class="phone"><i class="fa fa-phone"></i><?php echo $team_member_mobile; ?></p>
                            <p class="email"><i class="fa fa-envelope"></i><a href="mailto:<?php echo $team_member_email; ?>"><?php echo $team_member_email; ?></a></p>
                        </div>
                    </div>

                <?php endwhile; ?>
                <?php wp_reset_query(); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}

add_shortcode('team', 'sb_team');
?>
<?php
/*
 * SC_TEAM MEMBER
 * SHORTCODE [team_member id=""]
 */

function sc_team_member($atts) {
    extract(
            shortcode_atts(array(
        'id' => '',
                    ), $atts)
    );
    ob_start();
    $team_member = get_post($id);
//     echo '<pre>';
//     print_r($team_member);
//    echo '</pre>';
    $team_member_id = $team_member->ID;
    $team_member_name = $team_member->post_title;
    $team_member_position = get_field('position', $team_member_id);
    $team_member_mobile = get_field('mobile', $team_member_id);
    $team_member_email = get_field('email', $team_member_id);
    $team_member_buttontext = get_field('buttontext', $team_member_id);
    $team_member_buttonlink = get_field('buttonlink', $team_member_id);
    ?>
    <!--<div id="wrapper_contact" data-spy="affix" data-offset-top="250" class="affix-top">-->
    <div id="wrapper_contact">
    <div class="team-member">
        <?php echo get_the_post_thumbnail($team_member_id, 'large', array('class' => 'img-fluid')); ?>
        <div class="name"><?php echo $team_member_name; ?></div>
        <p class="position"><?php echo $team_member_position; ?></p>
        <p class="phone"><i class="fa fa-phone"></i><?php echo $team_member_mobile; ?></p>
        <p class="email"><i class="fa fa-envelope"></i><a href="mailto:<?php echo $team_member_email; ?>"><?php echo $team_member_email; ?></a></p>
        <div class="quotation">
            <a href="<?php echo $team_member_buttonlink; ?>"><?php echo $team_member_buttontext; ?></a>
        </div>
    </div>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('team_member', 'sc_team_member');
?>
<?php

// Register Custom Post Type
function custom_post_type_team() {

    $labels = array(
        'name' => _x('Teams', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Team', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('Team', 'text_domain'),
        'name_admin_bar' => __('Post Type', 'text_domain'),
        'archives' => __('Item Archives', 'text_domain'),
        'attributes' => __('Item Attributes', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'all_items' => __('All Items', 'text_domain'),
        'add_new_item' => __('Add New Item', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New Item', 'text_domain'),
        'edit_item' => __('Edit Item', 'text_domain'),
        'update_item' => __('Update Item', 'text_domain'),
        'view_item' => __('View Item', 'text_domain'),
        'view_items' => __('View Teams', 'text_domain'),
        'search_items' => __('Search Item', 'text_domain'),
        'not_found' => __('Not found', 'text_domain'),
        'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
        'featured_image' => __('Team Image', 'text_domain'),
        'set_featured_image' => __('Set Team image', 'text_domain'),
        'remove_featured_image' => __('Remove Team image', 'text_domain'),
        'use_featured_image' => __('Use as Team image', 'text_domain'),
        'insert_into_item' => __('Insert into item', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
        'items_list' => __('Items list', 'text_domain'),
        'items_list_navigation' => __('Items list navigation', 'text_domain'),
        'filter_items_list' => __('Filter items list', 'text_domain'),
    );
    $args = array(
        'label' => __('Team', 'text_domain'),
        'description' => __('Team Post Type Description', 'text_domain'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail'),
        'taxonomies' => array('team_cat'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );
    register_post_type('team', $args);
}

add_action('init', 'custom_post_type_team', 0);
?>