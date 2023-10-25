<?php

add_action('wp_enqueue_scripts', 'eikra_child_styles', 18);

function eikra_child_styles()
{
	wp_enqueue_style('child-style', get_stylesheet_uri(), false, '3.6.4');
	wp_register_script('stikcy-resizer', get_stylesheet_directory_uri() . '/js/sticky-resizer.js', array('jquery'));
	wp_register_script('stikcy-sidebar', get_stylesheet_directory_uri() . '/js/sticky.js', array('jquery'));

	wp_enqueue_style('slick-style', get_stylesheet_directory_uri() . '/assets/css/slick.css');
	wp_enqueue_script('slick-js', get_stylesheet_directory_uri() . '/assets/js/slick.min.js', array(), '', true);
	wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array(), '', true);

	wp_enqueue_script('stikcy-resizer');
	wp_enqueue_script('stikcy-sidebar');
}
add_action('after_setup_theme', 'eikra_child_theme_setup');

function eikra_child_theme_setup()
{
	load_child_theme_textdomain('eikra', get_stylesheet_directory() . '/languages');
}

function ekira_counter($atts)
{
	$content_post = get_post($atts['page_id']);
	$content = $content_post->post_content;
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}
add_shortcode('ekira_counter', 'ekira_counter');
require(get_stylesheet_directory() . '/woocommerce/custom-functions.php');
require(get_stylesheet_directory() . '/courses/custom-functions.php');
require(get_stylesheet_directory() . '/team/team-functions.php');

function cs_search_form_widget($form)
{
	$form = '<form role="search" method="get" id="search-form" class="search-form" action="' . home_url('/') . '">
	<label><span class="screen-reader-text">Zoeken..</span><input type="search" class="search-field" placeholder="Zoeken.." value="' . get_search_query() . '" name="s"></label>
	<input type="submit" class="search-submit" value="Zoek">
	</form>';
	return $form;
}

add_filter('get_search_form', 'cs_search_form_widget');

/**
 * Changes the redirect URL for the Return To Shop button in the cart.
 *
 * @return string
 */
function wc_empty_cart_redirect_url()
{
	return 'https://www.cre8tive.online';
}

add_filter('woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url');

function my_text_strings($translated_text, $text, $domain)
{
	switch ($translated_text) {
		case 'Waardebon toepassen':
			$translated_text = __('Couponcode Toepassen', 'woocommerce');
			break;
	}
	return $translated_text;
}
add_filter('gettext', 'my_text_strings', 20, 3);

add_action('woocommerce_thankyou', 'thankyou_custom_redirect');
function thankyou_custom_redirect($order_id)
{
	$order = wc_get_order($order_id);
	$order_key = $order->get_order_key();
	$page = get_page_by_title('bevestiging', '', 'page');
	$pageID = $page->ID;
	$url = get_permalink($pageID) . "order-received/$order_id/?key=$order_key";
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug == 'factuurgegevens' && !$order->has_status('failed')) {
		wp_safe_redirect($url);
		exit;
	}
}

add_shortcode('slider-homepage', 'slider_homepage');
function slider_homepage()
{
	ob_start();

	$idhome = get_the_id();

	$background 		= get_field('background_slider', $idhome);

	$title 				= get_field('title', $idhome);
	$usp 				= get_field('usps', $idhome);
	$vca 				= get_field('vca', $idhome);
	$bhv 				= get_field('bhv', $idhome);
	$code_95 			= get_field('code_95', $idhome);
	$logistiek 			= get_field('logistiek', $idhome);

	if ($background) {
		$background = $background;
	} else {
		$background = '/wp-content/uploads/2021/10/thisisengineering-raeng-vEoMKBdUIzs-unsplash-2-scaled.jpg';
	}
?>

	<div class="custom-slider">
		<div class="slick-slider">
			<div class="bg-slider slide1" style="background-image: url(<?php echo $background; ?>);" data-lazy="<?php echo $background; ?>">
				<div class="inner-content">
					<div class="title-slide">
						<h2><?php echo $title; ?></h2>
					</div>
					<div class="usps-lists">
						<div class="col-usp">
							<div class="usp-list">
								<i class="fa-check" data-stylerecorder="true" style="text-align: left; letter-spacing: 0px; font-weight: 400;"></i>
								<span><?php echo $usp['usp_1']; ?></span>
							</div>
							<div class="usp-list">
								<i class="fa-check" data-stylerecorder="true" style="text-align: left; letter-spacing: 0px; font-weight: 400;"></i>
								<span><?php echo $usp['usp_2']; ?></span>
							</div>
						</div>
						<div class="col-usp">
							<div class="usp-list">
								<i class="fa-check" data-stylerecorder="true" style="text-align: left; letter-spacing: 0px; font-weight: 400;"></i>
								<span><?php echo $usp['usp_3']; ?></span>
							</div>
							<div class="usp-list">
								<i class="fa-check" data-stylerecorder="true" style="text-align: left; letter-spacing: 0px; font-weight: 400;"></i>
								<span><?php echo $usp['usp_4']; ?></span>
							</div>
						</div>
					</div>
					<div class="location-mobile">
						<i class="fa-map-marker"></i>
						Haarlem/ <br>Amsterdam Halfweg
					</div>

					<div class="featured-list">
						<div class="col-featured featured-active" data-type="vca">
							<div class="inner-col-featured">
								<h4>VCA</h4>
								<div class="price-feature"><span>Vanaf € <?php echo $vca['price']; ?>,-</span></div>
								<ul class="lists-featured">
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $vca['list_1_vca']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $vca['list_2_vca']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $vca['list_3_vca']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $vca['list_4_vca']; ?>
									</li>
								</ul>
							</div>
							<div class="cta-featured">
								<a href="<?php echo $vca['url_vca']; ?>">Inschrijven</a>
							</div>
						</div>
						<div class="col-featured" data-type="bhv">
							<div class="inner-col-featured">
								<h4>BHV</h4>
								<div class="price-feature"><span>Vanaf € <?php echo $bhv['price']; ?>,-</span></div>
								<ul class="lists-featured">
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $bhv['list_1_bhv']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $bhv['list_2_bhv']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $bhv['list_3_bhv']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $bhv['list_4_bhv']; ?>
									</li>
								</ul>
							</div>
							<div class="cta-featured">
								<a href="<?php echo $bhv['url_bhv']; ?>">Inschrijven</a>
							</div>
						</div>
						<div class="col-featured" data-type="code-95">
							<div class="inner-col-featured">
								<h4>CODE 95</h4>
								<div class="price-feature"><span>Vanaf € <?php echo $code_95['price']; ?>,-</span></div>
								<ul class="lists-featured">
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $code_95['list_1_code']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $code_95['list_2_code']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $code_95['list_3_code']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $code_95['list_4_code']; ?>
									</li>
								</ul>
							</div>
							<div class="cta-featured">
								<a href="<?php echo $code_95['url_code']; ?>">Inschrijven</a>
							</div>
						</div>
						<div class="col-featured" data-type="logistiek">
							<div class="inner-col-featured">
								<h4>Logistiek</h4>
								<div class="price-feature"><span>Vanaf € <?php echo $logistiek['price']; ?>,-</span></div>
								<ul class="lists-featured">
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $logistiek['list_1_logistiek']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $logistiek['list_2_logistiek']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $logistiek['list_3_logistiek']; ?>
									</li>
									<li>
										<i class="fa-check" data-stylerecorder="true"></i> <?php echo $logistiek['list_4_logistiek']; ?>
									</li>
								</ul>
							</div>
							<div class="cta-featured">
								<a href="<?php echo $logistiek['url_logistiek']; ?>">Inschrijven</a>
							</div>
						</div>
					</div>

					<div class="btn-featured featured-mobile">
						<div class="btn-col-featured" data-type="vca" style="background-image: url('/wp-content/uploads/2021/12/VCA-5.png');">
							<!-- <p>VCA</p> -->
						</div>
						<div class="btn-col-featured" data-type="bhv" style="background-image: url('/wp-content/uploads/2021/12/1-1.png');">
							<!-- <p>BHV</p> -->
						</div>
						<div class="btn-col-featured" data-type="code-95" style="background-image: url('/wp-content/uploads/2021/12/2-1.png');">
							<!-- <p>CODE 95</p> -->
						</div>
						<div class="btn-col-featured" data-type="logistiek" style="background-image: url('/wp-content/uploads/2021/12/3-1.png');">
							<!-- <p>LOGISTIEK</p> -->
						</div>
					</div>

					<div class="location-desktop">
						<div class="circle-white">
							<div class="map-image" style="background-image: url('/wp-content/uploads/2021/11/map.png')"></div>
						</div>
						<div class="text-location">
							Haarlem/ <br>Amsterdam Halfweg
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	return ob_get_clean();
}

/**
 * Change the default country on the checkout page
 */

//add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );

function change_default_checkout_country()
{
	return 'NL'; // Put Country code here
}

add_filter('woocommerce_order_get_billing_country', 'my_woocommerce_filter_for_billing_country');
add_filter('woocommerce_order_get_shipping_country', 'my_woocommerce_filter_for_billing_country');
function my_woocommerce_filter_for_billing_country($country)
{
	// Return the country code according with your business case. 
	return 'NL';
}

add_filter('woocommerce_billing_fields', 'ts_unrequire_wc_country_field', 99);
function ts_unrequire_wc_country_field($fields)
{
	$fields['billing_country']['required'] = false;
	return $fields;
}
function mtp_disable_mobile_messaging($mailer)
{

	remove_action('woocommerce_email_footer', array($mailer->emails['WC_Email_New_Order'], 'mobile_messaging'), 9);
}
add_action('woocommerce_email', 'mtp_disable_mobile_messaging');


/**
 * Add BCC to certain store emails.
 *
 * @param  string $headers
 * @param string $email_id
 * @return string
 */
function kia_add_bcc_to_specific_emails( $headers, $email_id ) {

    $bbc_these_emails = array(
        'new_order', // Add other emails as entries in this array.
    );

    if ( in_array( $email_id, $bbc_these_emails ) ) {
        $headers = array( 
            $headers,
            'Bcc: Farida <farida@eduq.nl>' ."\r\n", // Modify the email as needed.
			'Bcc: Ahmed <ahmed@twooaksgroup.com>' ."\r\n", // Modify the email as needed.
			'Bcc: Marjan <marjan@twooaksgroup.com>' ."\r\n", // Modify the email as needed.
        );
    }

    return $headers;
}
add_filter( 'woocommerce_email_headers', 'kia_add_bcc_to_specific_emails', 10, 2 );