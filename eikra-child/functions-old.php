<?php

add_action('wp_enqueue_scripts', 'eikra_child_styles', 18);

function eikra_child_styles() {
    wp_enqueue_style('child-style', get_stylesheet_uri());
}

/**
 * Remove related products output
 */
function add_custom_script() {
    wp_register_script('custom_script', home_url() . '/wp-content/themes/eikra-child/js/custom.js', array('jquery'));
    wp_enqueue_script('custom_script');
}

add_action('wp_enqueue_scripts', 'add_custom_script');
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
add_action('woocommerce_after_checkout_billing_form', 'woocommerce_checkout_login_form');
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
add_action('wp_loaded', 'add_product_to_cart');

add_action('init', 'proces_eventform');

function proces_eventform() {

    if (!is_admin()) {
        global $woocommerce;
        if ($_POST['eventform'] == 1) {
            $woocommerce->session->set('subscription', $_POST);
            $noCursists = $_POST['noCursists'];
            $product_id = $_POST['courseid'];
            $woocommerce->cart->add_to_cart($product_id, $noCursists);
        }
    }
}

//https://www.cloudways.com/blog/how-to-edit-delete-fields-and-email-in-woocommerce-custom-checkout-fields/ 

function cloudways_save_extra_checkout_fields($order_id, $posted) {
    $adress = $posted['billing_street'] . ' ' . $posted['billing_housenumber'];
    update_post_meta($order_id, '_billing_address_1', $adress);
}

add_action('woocommerce_checkout_update_order_meta', 'cloudways_save_extra_checkout_fields', 10, 2);
add_action('wp_loaded', 'redirect');

function redirect() {
    if ($_POST['eventform'] == 1) {
        $url = get_permalink(get_option('woocommerce_checkout_page_id'));
        header('Location:' . $url);
        exit();
    }
}

remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
add_action('woocommerce_after_checkout_billing_form', 'woocommerce_checkout_login_form');
add_filter('woocommerce_available_payment_gateways', 'unset_gateway');

function unset_gateway($available_gateways) {
    global $woocommerce;
    $subscription = $woocommerce->session->get('subscription');
    if ($subscription['iscompany'] != '1') {

        if ($subscription['payment'] == 0) {

            unset($available_gateways['bacs']);
        } elseif ($subscription['payment'] == 1) {

            unset($available_gateways['multisafepay_ideal']);
        }
        unset($available_gateways['bacs']);
    } else {
        if ($subscription['paymentcompany'] == 0) {

            unset($available_gateways['bacs']);
        } elseif ($subscription['paymentcompany'] == 1) {

            unset($available_gateways['multisafepay_ideal']);
        }
    }

    return $available_gateways;
}

add_filter('woocommerce_checkout_fields', 'override_checkoutfields');

function override_checkoutfields($fields) {
    global $woocommerce;

    $subscription = $woocommerce->session->get('subscription');

    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['account']);
    unset($fields['billing']['billing_address_1']);

    $fields['billing']['billing_postcode']['required'] = true;
    $fields['billing']['billing_address_1']['required'] = false;
    $fields['billing']['billing_city']['required'] = true;


    $fields['billing']['billing_company']['default'] = $subscription['companyname'];
    if ($subscription['iscompany'] != '1') {
        unset($fields['billing']['billing_company']);
        $fields['order']['order_comments']['default'] = 'Klant is ' . $type[$subscription['iscompany']] . ' en wil de opleiding ' . $subscription['course'];
    } else {

        $fields['order']['order_comments']['default'] = 'Klant is ' . $type[$subscription['iscompany']] . ' met de branche ' . $subscription['course'] . ' en wil de opleiding ' . $subscription['course'];
    }

    $type = array(0 => "Particulier", 1 => "Zakelijk");

    $fields['billing']['billing_email']['default'] = $subscription['email'];
    $fields['billing']['billing_first_name']['default'] = $subscription['firstname'];
    $fields['billing']['billing_last_name']['default'] = $subscription['middlename'] . $subscription['lastname'];
    //$fields['billing']['billing_address_1']['default'] = $subscription['address'];
    $fields['billing']['billing_postcode']['default'] = $subscription['zip'];
    $fields['billing']['billing_phone']['default'] = $subscription['phone'];
    $fields['billing']['billing_city']['default'] = $subscription['city'];

    if ($subscription['iscompany'] == '1') {
        $fields['billing']['billing_company']['required'] = true;
        $fields['billing']['billing_company']['default'] = $subscription['companyname'];
    }

    $fields['order']['order_comments']['default'] = 'Klant is ' . $type[$subscription['iscompany']] . ' en wil de opleiding ' . $subscription['course'];


    $noCursists = $subscription['noCursists'];
    $cursists = array();
    for ($i = 1; $i <= $noCursists; $i++) {
        $cursists[$i]['name'] = $subscription['firstname_cursist' . $i] . ' ' . $subscription['middlename_cursist' . $i] . ' ' . $subscription['lastname_cursist' . $i];
        $cursists[$i]['bsn'] = $subscription['BSN_cursist' . $i];
        $cursists[$i]['placeofbirth'] = $subscription['placeofbrith_cursist' . $i];
        $cursists[$i]['birthdata'] = $subscription['birtdate_cursist' . $i];
        //$cursists .= " Deelnemer".$i.": Naam:".$name." BSN: ".$bsn." geboorteplaats :".$placeofbrith." geboortedatum: ".$birtdate."<br/>";  
    }

    $woocommerce->session->set('info_cursists', $cursists);

    $fields['order']['order_comments']['default'] .= $cursists;

    if (!is_null($survey_email)) {
        $fields['billing']['billing_email']['default'] = 'gerardvanhattem@live.nl';
    }

    $fields['billing']['billing_street'] = array(
        'label' => __('Straat', 'woocommerce'),
        'placeholder' => _x('Straat', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-first'),
        'clear' => true
    );

    $fields['billing']['billing_housenumber'] = array(
        'label' => __('Huisnummer', 'woocommerce'),
        'placeholder' => _x('Huisnummer', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-first'),
        'clear' => true
    );

    /* Rendering settings */
    $fields['billing']['billing_email']['class'][0] = "form-row-first";
    $fields['billing']['billing_postcode']['class'][0] = "form-row-first";
    $fields['billing']['billing_phone']['class'][0] = "form-row-first";
    $fields['billing']['billing_city']['class'][0] = "form-row-first";
    $fields['billing']['billing_last_name']['class'][0] = "form-row-first";
    unset($fields['billing']['billing_address_1']);


    $order = array(
        "billing_first_name",
        "billing_last_name",
        "billing_street",
        "billing_housenumber",
        "billing_postcode",
        "billing_city",
        "billing_phone",
        "billing_email",
    );

    if ($subscription['iscompany'] == '1') {
        $order[] = 'billing_company';
        array_unshift($order, 'billing_company');
    }


    foreach ($order as $field) {
        $ordered_fields[$field] = $fields["billing"][$field];
    }
    $fields["billing"] = $ordered_fields;

    return $fields;
}


function sc_eventlist() {
    global $product;
    $id = $product->get_id();
    $courses = getCourses($id);

    if (!empty($courses)) {
        $html = '<table><tr><th>Datum</th><th>Locatie</th></tr>';
        foreach ($courses as $course) {
            $html .= '<tr><td>' . date('d-m-Y', $course->date) . '</td><td>' . $course->location . '</td></tr>';
        }
        $html .= '</table>';
    } else {
        $html = "Er zijn momenteel geen data bekend voor deze opleiding.</br>";
        $html .= "Neem <a href='/contact/'>contact met ons op. ";
    }

    return $html;
}

add_shortcode('sc_eventlist', 'sc_eventlist');


function sc_subscription2() {

    global $product;
    $id = $product->get_id();

    $courses = getCourses($id);

    // if (empty($courses)) {
    //     $html = "Er zijn momenteel geen data bekend voor deze opleiding.</br>";
    //     $html .= "Neem <a href='/contact/'>contact met ons op.</a>";
    //     return $html;
    // }

    $options = "";
    $trainingradio = "";
    foreach ($courses as $course) {
        $options .= '<option value="' . date('d-m-Y', $course->date) . ' - ' . $course->location . '">' . date('d-m-Y', $course->date) . ' - ' . $course->location . '</option>';
        $trainingradio .= '<label class="label-training-form"><input type="radio" name ="course"   required value="' . date('d-m-Y', $course->date) . ' - ' . $course->location . '" />' . date('d-m-Y', $course->date) . ' - ' . $course->location . '</label>';
    }

    $form = '<form name="form_subscription" id="form_subscription" method="POST"><div class="vc_row-fluid rowform">
                                        <div class="vc_column_container vc_col-sm-12 formheader">Opleiding</div>
	<div class="form-group">
			<label class="vc_column_container  vc_col-sm-3 control-label">Trainingsdatum *</label>
			<div class="vc_column_container vc_col-sm-9">
				   ' . $trainingradio . '
			</div>
		</div>
                <div class="clear"></div>
		<div class="vc_column_container vc_col-sm-12 formheader">Klantgegevens</div>
		<div class="form-group">
			<label class="vc_column_container  vc_col-sm-3 control-label">Aanmeldingstype</label>
			<div class="vc_column_container  vc_col-sm-9">
				<label class="radio-inline"> <input class="radio_type" type="radio" name="iscompany" value="0" CHECKED>Particulier</label>
				<label class="radio-inline"> <input class="radio_type" type="radio" name="iscompany" value="1" >Zakelijk</label>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="business">
		<div class="vc_row-fluid rowform">
			<div class="form-group">
				<label class="vc_column_container  vc_col-sm-3 control-label">Bedrijfsnaam*</label>
				<div class="vc_column_container  vc_col-sm-9">
					<input type ="text" name="companyname" class="form-control businessfield" placeholder="Bedrijfsnaam" required="true"/>
				</div>
			</div>
		</div>
		<div class="clear"></div>
<!--
		<div class="vc_row-fluid rowform">
			<div class="form-group">
				<label class="vc_column_container  vc_col-sm-3 control-label">Branche</label>
				<div class="vc_column_container  vc_col-sm-9">
					    <select class="form-control businessfield" name="branche">
						    <option value="">Maak een keuze</option>
                            <option value="ZZP" >ZZP</option>
                            <option value="Asbest" >Asbest</option>
                            <option value="Bouw" >Bouw</option>
                            <option value="Evenementen" >Evenementen</option>
                            <option value="Groothandel" >Groothandel</option>
                            <option value="GW&W" >GW&W</option>
                            <option value="Haven & Overslag" >Haven & Overslag</option>
                            <option value="Horeca" >Horeca</option>
                            <option value="Opleiden" >Opleiden</option>
                            <option value="Overheid" >Overheid</option>
                            <option value="Reïntegratie" >Reïntegratie</option>
                            <option value="Steigerbouw" >Steigerbouw</option>
                            <option value="Transport & Logistiek" >Transport & Logistiek</option>
                            <option value="Uitzenden" >Uitzenden</option>
                            <option value="Overige" >Overige</option>
					    </select>
				</div>
			</div>
		</div>
-->
	</div>

	<div class="clear"></div>
	<div class="vc_row-fluid rowform">
		<div class="form-group">
			<label class="vc_column_container  vc_col-sm-3 control-label">Contactpersoon*</label>
			<div class="vc_column_container  vc_col-sm-9">
				<div class="vc_col-sm-4" style="padding-left:0px;">
					<input type ="text" name="firstname" class="form-control"  placeholder="Voorletters" required="true"/>
				</div>
				<div class="vc_col-sm-3">
					<input type ="text" name="middlename" class="form-control " placeholder="Tussenv." />
				</div>
				<div class="vc_col-sm-5" style="padding-right:0px;">
					<input type ="text" name="lastname" class="form-control " placeholder="Achternaam" required="true"/>
				</div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="vc_row-fluid rowform">
		<div class="form-group">
			<label class="vc_column_container  vc_col-sm-3 control-label">Telefoon*</label>
			<div class="vc_column_container  vc_col-sm-9">
				<input type ="tel" name="phone" class="form-control number" placeholder="Telefoonnummer" required="true"/>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="vc_row-fluid rowform">
		<div class="form-group">
			<label class="vc_column_container  vc_col-sm-3 control-label">Email*</label>
			<div class="vc_column_container  vc_col-sm-9">
				<input type = "email" name="email" class="form-control" placeholder="Email" required="true"/>
			</div>
		</div>
	</div>
	<div id="cursist">
		<div class="vc_column_container vc_col-sm-12 formheader">Cursist(en) gegevens</div>
		<div class="form-group">
			<label class="vc_column_container  vc_col-sm-3 control-label">Aantal *</label>
			<div class="vc_column_container  vc_col-sm-9"> 
				<input id="amount_cursists" name ="noCursists" class="form-control" type="number" min="1"  required/> 
			</div>
		</div>
		<div class="clear"></div>
		<div id="cusistswrapper">  
		</div>
	</div>
	<div class="clear"></div>
	<div id="payment" class="private">
		<div class="vc_row-fluid rowform">
			<div class="vc_column_container vc_col-sm-12 formheader">Betalen & versturen</div>
			<div class="form-group">
				<label class="vc_column_container  vc_col-sm-3 control-label">Betalen via</label>
				<div class="vc_column_container  vc_col-sm-9">
					<label class="radio-inline"> <input class="radio_type2 privatefield" type="radio" name="payment" value="0" CHECKED>iDeal</label>
                                                                                                <label class="radio-inline"> <input class="radio_type2 privatefield" type="radio" name="payment" value="1">factuur</label>
				</div>
			</div>
		</div>
	</div>
	<div id="paymentcompanies" class="business">
		<div class="vc_row-fluid rowform">
			<div class="vc_column_container vc_col-sm-12 formheader">Betalen & versturen</div>
			<div class="form-group">
				<label class="vc_column_container  vc_col-sm-3 control-label">Betalen via</label>
				<div class="vc_column_container  vc_col-sm-9">
					<label class="radio-inline"> <input class="radio_type2 businessfield" type="radio" name="paymentcompany" value="0" CHECKED>iDeal</label>
					<label class="radio-inline"> <input class="radio_type2 businessfield" type="radio" name="paymentcompany" value="1" >Factuur</label>
				</div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="vc_row-fluid rowform"> 
		<div class="vc_column_container vc_col-sm-12"><input type="submit" class="btn btn-primary" id="submit_subscription" value="Inschrijven"></input></div>
	</div>
	<input type="hidden" name="eventform" value="1"></input>
	<input type="hidden" name="courseid" value="' . $id . '"></input>
	</form>
	';
    $error = "<p class='error' style='color:red;'>Dit veld is vereist</p>";
    $form .= '<script>
		jQuery(document).ready(function(){
			
			jQuery(".number").keydown(function (e) {
        
			if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
				(e.keyCode >= 35 && e.keyCode <= 40)) {        
					return;
				}
      
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		
			});
			
			jQuery(document).on("keydown",".number",function (e) {
			
			if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190,191]) !== -1 ||
				(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
				(e.keyCode >= 35 && e.keyCode <= 40)) {        
					return;
				}
      
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		
			});
			
			
			jQuery("#submit_subscription").click(function(){
				jQuery("input").css("border-color","#ccc");
				jQuery(".error").remove();
				jQuery("form#form_subscription :input").each(function(){
					
					if(jQuery(this).prop("required") && jQuery(this).val()==""){ 
						jQuery(this).css("border-color","red");
						jQuery(this).parent().append( "' . $error . '" );  
					}
				})
				
				 
			});
				 
		});
	</script>';

    return $form;
}

add_shortcode('sc_subscription2', 'sc_subscription2');

function sc_team($atts, $content = null) {
    $values = shortcode_atts(array(
        'image' => '',
        'float' => '',
        'name' => '',
        'title' => '',
        'phone' => '',
        'email' => '',
        'linkedin' => '#',
        'facebook' => '#',
        'img' => '#'
            ), $atts);

    if (!empty($values['float'])) {
        $margin = 'style="margin:0px"';
        $html .= '<div id="wrapper_contact" data-spy="affix" data-offset-top="250">';
    }

    $html .= '<div class="card" ' . $margin . '>';
    $html = $html . '<img src="' . get_site_url() . '/' . esc_attr($values['img']) . '" style="width:100%;filter: brightness(1.8);filter: contrast(0.9);">';
    $html = $html . '<div class="teammember">' . esc_attr($values['name']) . '</div>';
    $html = $html . '<p class="title">' . esc_attr($values['title']) . '</p>';
    $html = $html . '<p class="phone"><i class="fa fa-phone"></i><a href="tel:' . esc_attr($values['phone']) . '"> ' . esc_attr($values['phone']) . '</a></p>';
    $html = $html . '<p class="email"><i class="fa fa-envelope"></i><a href="mailto:' . esc_attr($values['email']) . '"> ' . esc_attr($values['email']) . '</a></p>';
    $html = $html . '</div>';

    if (!empty($values['float'])) {
        $html .= '</div>';
    }

    return $html;
}

add_shortcode('sc_team', 'sc_team');

function sc_sell($atts, $content = null) {
    $values = shortcode_atts(array(
        'image' => '',
        'float' => '',
        'name' => '',
        'title' => '',
        'phone' => '',
        'email' => '',
        'linkedin' => '#',
        'facebook' => '#',
        'img' => '#',
            ), $atts);
    $margin = 'style="margin:0px"';


    $html .= '<div id="wrapper_contact" data-spy="affix" data-offset-top="250">';
    $html .= '<div class="card" ' . $margin . '>';
    $html = $html . '<img src="' . get_site_url() . '/' . esc_attr($values['img']) . '" style="width:100%">';
    $html = $html . '<div class="teammember">' . esc_attr($values['name']) . '</div>';
    $html = $html . '<p class="title">' . esc_attr($values['title']) . '</p>';
    $html = $html . '<p class="phone"><i class="fa fa-phone"></i> ' . esc_attr($values['phone']) . '</p>';
    $html = $html . '<p class="email"><i class="fa fa-envelope"></i><a href="mailto:' . esc_attr($values['email']) . '"> ' . esc_attr($values['email']) . '</a></p>';
    $html = $html . '<div class="quotation">';
    $html = $html . '<span class="requestquotation"><a href="/offerte/">Vraag direct offerte aan</a></span>';
    $html .= '</div>';
    $html = $html . '</div>';

    $html .= '</div>';


    return $html;
}

add_shortcode('sc_sell', 'sc_sell');
//
//function sv_conditional_email_recipient($recipient, $order) {
//
//    $page = $_GET['page'] = isset($_GET['page']) ? $_GET['page'] : '';
//    if ('wc-settings' === $page) {
//        return $recipient;
//    }
//
//    $recipient = $order->get_billing_email();
//
//
//
//    return $recipient;
//}
//

function customer_email_heading_order($email_heading, $email) {

    $email_heading = "Bestelling ontvangen";
    return $email_heading;
}

add_filter('woocommerce_email_header', 'customer_email_heading_order');

function getCourses($product = null) {

    global $wpdb;
    $table_name = $wpdb->prefix . "courses";
    if (!empty($product)) {
        $rows = $wpdb->get_results("SELECT location,date from $table_name WHERE course=" . $product . " AND date >= " . time());
    } else {
        $rows = $wpdb->get_results("SELECT location,date from $table_name AND date >= " . time());
    }
    return $rows;
}

function getFormCursist() {

    $number = $_POST['number'];
    $noCursist = $_POST['noCursist'];

    $html = "";

    for ($i = $noCursist + 1; $i <= $number; $i++) {
        $html = $html . '<div class="cursist" id="cursist' . $i . '"> 
						<div class="vc_row-fluid rowform">
							<div class="form-group">
								<label class="vc_column_container  vc_col-sm-3 control-label"></label>
								<div class="vc_column_container  vc_col-sm-9">
									<div class="formsubheader">Deelnemer ' . $i . '	
								</div>
							</div>
						</div>
					<div class="clear"></div>
					<div class="vc_row-fluid rowform">
						<div class="form-group">
							<label class="vc_column_container  vc_col-sm-3 control-label">Naam*</label>
							<div class="vc_column_container  vc_col-sm-9">
								<div class="vc_col-sm-4" style="padding-left:0px;">
									<input type ="text" name="firstname_cursist' . $i . '" class="form-control"  placeholder="Voorletters" required="true"/>
								</div>
								<div class="vc_col-sm-3">
									<input type ="text" name="middlename_cursist' . $i . '" class="form-control " placeholder="Tussenv." />
								</div>
								<div class="vc_col-sm-5" style="padding-right:0px;">
									<input type ="text" name="lastname_cursist' . $i . '" class="form-control " placeholder="Achternaam" required="true"/>
								</div>
							</div>
						</div>
					</div>
					<div class="clear"></div>
						<div class="vc_row-fluid rowform">
							<div class="form-group">
								<label class="vc_column_container  vc_col-sm-3 control-label">Geboorteplaats</label>
								<div class="vc_column_container  vc_col-sm-9">
									<input type ="text" name="placeofbrith_cursist' . $i . '" class="form-control" placeholder="Geboorteplaats" />
								</div>
							</div>
						</div>
					<div class="clear"></div>
						<div class="vc_row-fluid rowform">
							<div class="form-group">
								<label class="vc_column_container  vc_col-sm-3 control-label">Geboortedatum</label>
								<div class="vc_column_container  vc_col-sm-9">
									<input type ="text" placeholder="dd/mm/yyyy" name="birtdate_cursist' . $i . '" class="form-control number" />
								</div>
							</div>
						</div>
					<div class="clear"></div>
					</div>';
    }
    echo $html;
    wp_die();
}

add_action('wp_ajax_getFormCursist', 'getFormCursist');
add_action('wp_ajax_nopriv_getFormCursist', 'getFormCursist');
remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
add_filter('woocommerce_cart_item_thumbnail', '__return_false');

function ekira_counter($atts) {
    $content_post = get_post($atts['page_id']);
    $content = $content_post->post_content;
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    //echo $content;
    return $content;
}

add_shortcode('ekira_counter', 'ekira_counter');
require( get_stylesheet_directory() . '/team/team-functions.php');