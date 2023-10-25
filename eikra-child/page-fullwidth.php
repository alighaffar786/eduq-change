<?php
/**
  Template Name: FULL WIDTH
 */
 
?>
<?php get_header(); ?>
<div id="primary" class="content-area">
    <div class="container-fluid">  
        <div class="row"> 
            <div class="<?php echo esc_attr($rdtheme_layout_class); ?>">
                <main id="main" class="site-main">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/content', 'page'); ?>
                        <?php
                        if (comments_open() || get_comments_number()) {
                            if (!RDTheme_Helper::lp_is_archive()) {
                                comments_template();
                            }
                        }
                        ?>
                    <?php endwhile; ?>
                </main>
            </div>
            
        </div>
    </div>
</div>
<?php get_footer(); ?>