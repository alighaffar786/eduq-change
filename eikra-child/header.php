<?php

/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 2.6
 */
// wp_enqueue_style('slick-style', get_stylesheet_directory_uri() . '/assets/css/meanmenuoverwrite.css');

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>

	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11" />

	<!-- Google Tag Manager -->
	<script>
		(function(w, d, s, l, i) {
			w[l] = w[l] || [];
			w[l].push({
				'gtm.start': new Date().getTime(),
				event: 'gtm.js'
			});
			var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s),
				dl = l != 'dataLayer' ? '&l=' + l : '';
			j.async = true;
			j.src =
				'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
			f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer', 'GTM-NCCZKVT');
	</script>
	<!-- End Google Tag Manager -->

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-121543155-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}

		gtag('js', new Date());

		gtag('config', 'UA-121543155-1');
	</script>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NCCZKVT" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->

	<?php do_action('wp_body_open'); ?>
	<?php
	// Preloader
	if (RDTheme::$options['preloader']) {
		// check for learpress popup too
		if (!function_exists('learn_press_is_content_item_only') || !learn_press_is_content_item_only()) {
			if (!empty(RDTheme::$options['preloader_image']['url'])) {
				$preloader_img = RDTheme::$options['preloader_image']['url'];
			} else {
				$preloader_img = RDTHEME_IMG_URL . 'preloader.gif';
			}
			echo '<div id="preloader" style="background-image:url(' . esc_url($preloader_img) . ');"></div>';
		}
	}

	?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'eikra'); ?></a>
		<header id="masthead" class="site-header">
			<?php
			if (RDTheme::$top_bar == 1 || RDTheme::$top_bar == 'on') {
				get_template_part('template-parts/header/header-top', RDTheme::$top_bar_style);
			}
			get_template_part('template-parts/header/header', RDTheme::$header_style);
			?>
		</header>
		<div id="meanmenu"></div>
		<div id="content" class="site-content">
			<?php get_template_part('template-parts/content', 'banner'); ?>