<?php

use Elementor\Modules\WpCli\Update;

add_action('wp_enqueue_scripts', 'eikra_child_course_script', 19);

function eikra_child_course_script()
{
    wp_enqueue_script('custome-courses-script', get_stylesheet_directory_uri() . '/js/custom.js', array(), '1.3', true);
}

function getCourses($product = null)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "courses";

    if (!empty($product)) {
        //        $rows = $wpdb->get_results("SELECT location,date from $table_name WHERE course=" . $product . " AND date <= " . time());
        // echo "SELECT * from ".$table_name." WHERE course=". $product .'"';

        if (is_array($product)) {
            $in_cond = implode(",", $product);
            $rows = $wpdb->get_results("SELECT * from " . $table_name . " WHERE course IN ($in_cond) order by date ASC");
        } else {
            $rows = $wpdb->get_results("SELECT * from " . $table_name . " WHERE course=" . $product . " order by date ASC");
        }
    } else {

        $rows = $wpdb->get_results("SELECT location,date from $table_name AND date >= " . time());
    }

    return $rows;
}

?>
<?php
//add_action('init', 'courses_subscription_redirect');

function courses_subscription_redirect()
{
    if (!is_admin()) {
        global $woocommerce;
        if (isset($_POST['eventform']) && $_POST['eventform'] == 1) {
            $woocommerce->session->set('subscription', $_POST);
            $noCursists = $_POST['noCursists'];
            $product_id = $_POST['courseid'];
            $woocommerce->cart->add_to_cart($product_id, $noCursists);
        }
    }
}

function cloudways_save_extra_checkout_fields($order_id, $posted)
{
    $adress = $posted['billing_street'];
    $is_company = $_POST['iscompany'];
    if ($is_company == "1") {
        $meta_data = [
            '_iscompany' => 'Commercial',
            'company_name' => $_POST['companyname'],
            '_first_name' => $_POST['firstname'],
            '_last_name' => $_POST['lastname'],
            '_email_adress' => $_POST['email'],
            '_phone_number' => $_POST['billing_Phone_number'],
            '_billing_custom_email' => $_POST['billing_custom_email']
        ];
    } else {
        $meta_data = [
            '_iscompany' => 'Private',
            '_first_name' => $_POST['firstname'],
            '_last_name' => $_POST['lastname'],
            '_email_adress' => $_POST['email'],
            '_billing_custom_email' => $_POST['billing_custom_email']
        ];
    }
    update_post_meta($order_id, 'billing_email', $_POST['billing_custom_email']);
    update_post_meta($order_id, '_billing_address_1', $adress);
    update_post_meta($order_id, '_user_info', $meta_data);
}

add_action('woocommerce_checkout_update_order_meta', 'cloudways_save_extra_checkout_fields', 10, 2);
add_action('wp_loaded', 'redirect');

function redirect()
{
    if (isset($_POST['eventform']) && $_POST['eventform'] == 1) {
        $url = get_permalink(get_option('woocommerce_checkout_page_id'));
        header('Location:' . $url);
        exit();
    }
}

// WooCommerce Rename Checkout Fields
add_filter('woocommerce_checkout_fields', 'custom_rename_wc_checkout_fields', 2000);

// Change placeholder and label text
function custom_rename_wc_checkout_fields($fields)
{
    $fields['billing']['billing_first_name']['placeholder'] = 'Voorletters';
    $fields['billing']['billing_first_name']['label'] = 'Voornaam';
    $fields['billing']['billing_middlename_name'] = array(
        'label' => __('Tussenv.', 'woocommerce'),
        'required' => false,
    );
    $fields['billing']['billing_last_name']['placeholder'] = 'Achternaam';
    $fields['billing']['billing_last_name']['label'] = 'Achternaam';
    $fields['billing']['billing_phone']['placeholder'] = 'Telefoon';
    $fields['billing']['billing_phone']['label'] = 'Telefoon';
    $fields['billing']['billing_company']['placeholder'] = 'Bedrijfsnaam';
    // $fields['billing']['billing_company']['required'] = true;
    $fields['billing']['billing_company']['label'] = 'Bedrijfsnaam';
    $fields['billing']['billing_postcode']['placeholder'] = 'Postcode';
    $fields['billing']['billing_postcode']['label'] = 'Postcode';
    $fields['billing']['billing_city']['placeholder'] = 'Plaats';
    $fields['billing']['billing_city']['label'] = 'Plaats';

    $fields['billing']['billing_email']['placeholder'] = 'E-mail';



    $fields['billing']['billing_custom_email'] = $fields['billing']['billing_email'];
    $fields['billing']['billing_custom_email']["label"] = "E-mailadres";
    $fields['billing']['billing_custom_email']["type"] = "email";
    $fields['billing']['billing_custom_email']["required"] = true;
    $fields['billing']['billing_custom_email']["class"][0] = "form-row-first";
    $fields['billing']['billing_custom_email']["autocomplete"] = "email";
    $fields['billing']['billing_custom_email']["priority"] = 110;
    $fields['billing']['billing_custom_email']['placeholder'] = 'E-mail';

    $fields['billing']['billing_email']['required'] = false;
    unset($fields['billing']['billing_email']['validate']);
    //    unset($fields['billing']['billing_email']);

    return $fields;
}

add_filter('woocommerce_checkout_fields', 'override_checkoutfields', 2001);

function override_checkoutfields($fields)
{
    global $woocommerce;

    $subscription = $woocommerce->session->get('subscription');
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['account']);
    unset($fields['billing']['billing_address_1']);

    $fields['billing']['billing_postcode']['required'] = true;
    $fields['billing']['billing_address_1']['required'] = false;
    $fields['billing']['billing_city']['required'] = true;

    $subscription_company = isset($subscription['companyname']) ?  $subscription['companyname'] : "";
    $fields['billing']['billing_company']['default'] = $subscription_company;
    if ($subscription_company != '1') {
        unset($fields['billing']['billing_company']);
        //$fields['order']['order_comments']['default'] = 'Klant is ' . $type[$subscription['iscompany']] . ' en wil de opleiding ' . $subscription['course'];
    } else {

        //$fields['order']['order_comments']['default'] = 'Klant is ' . $type[$subscription['iscompany']] . ' met de branche ' . $subscription['course'] . ' en wil de opleiding ' . $subscription['course'];
    }

    $type = array(0 => "Particulier", 1 => "Zakelijk");

    //    $fields['billing']['billing_email']['default'] = $subscription['email'];
    $fields['billing']['billing_first_name']['default'] = isset($subscription['firstname']) ? $subscription['firstname'] : "";

    $fields['billing']['billing_middlename_name']['default'] =  isset($subscription['middlename']) ? $subscription['middlename'] : "";

    $fields['billing']['billing_last_name']['default'] =  isset($subscription['lastname']) ?  $subscription['lastname'] : "";
    //$fields['billing']['billing_address_1']['default'] = $subscription['address'];
    $fields['billing']['billing_postcode']['default'] = isset($subscription['zip']) ?  $subscription['zip'] : "";
    $fields['billing']['billing_phone']['default'] = isset($subscription['phone']) ?  $subscription['phone'] : "";
    $fields['billing']['billing_city']['default'] = isset($subscription['city']) ?  $subscription['city'] : "";

    $subscription_iscompany = isset($subscription['iscompany']) ? $subscription['iscompany'] : "";
    if ($subscription_iscompany == '1') {
        $fields['billing']['billing_company']['required'] = true;
        $fields['billing']['billing_company']['default'] = $subscription_company;
    }

    //$fields['order']['order_comments']['default'] = 'Klant is ' . $type[$subscription_iscompany] . ' en wil de opleiding ' . $subscription['course'];


    $noCursists = $subscription['noCursists'];
    $cursists = array();
    for ($i = 1; $i <= $noCursists; $i++) {
        $cursists[$i]['name'] = $subscription['firstname_cursist' . $i] . ' ' . $subscription['middlename_cursist' . $i] . ' ' . $subscription['lastname_cursist' . $i];
        //$cursists[$i]['bsn'] = $subscription['BSN_cursist' . $i];
        //$cursists[$i]['placeofbirth'] = $subscription['placeofbrith_cursist' . $i];
        $cursists[$i]['birthdata'] = $subscription['birtdate_cursist' . $i];
        //$cursists .= " Deelnemer".$i.": Naam:".$name." BSN: ".$bsn." geboorteplaats :".$placeofbrith." geboortedatum: ".$birtdate."<br/>";
    }

    $woocommerce->session->set('info_cursists', $cursists);
    unset($fields['order']['order_comments']);
    //    if (!is_null($survey_email)) {
    //        $fields['billing']['billing_email']['default'] = 'gerardvanhattem@live.nl';
    //    }

    $fields['billing']['billing_street'] = array(
        'label' => __('Straat & Huisnummer', 'woocommerce'),
        'placeholder' => _x('Straat & Huisnummer', 'placeholder', 'woocommerce'),
        'required' => true,
        'class' => array('form-row-first'),
        'clear' => true
    );

    //    $fields['billing']['billing_housenumber'] = array(
    //        'label' => __('Huisnummer', 'woocommerce'),
    //        'placeholder' => _x('Huisnummer', 'placeholder', 'woocommerce'),
    //        'required' => true,
    //        'class' => array('form-row-first'),
    //        'clear' => true
    //    );

    $fields['billing']['noCursists']['default'] = $noCursists;

    /* Rendering settings */
    $fields['billing']['billing_email']['class'][0] = "form-row-first";
    $fields['billing']['billing_postcode']['class'][0] = "form-row-first";
    $fields['billing']['billing_phone']['class'][0] = "form-row-first";
    $fields['billing']['billing_company']['class'][0] = "form-row-first";
    $fields['billing']['billing_city']['class'][0] = "form-row-first";
    $fields['billing']['billing_last_name']['class'][0] = "form-row-first";
    $fields['billing']['billing_street']['priority'] = 111;
    $fields['billing']['billing_company']['priority'] = 115;
    // $fields['billing']['billing_housenumber']['priority'] = 112;
    $fields['billing']['billing_city']['priority'] = 63;

    unset($fields['billing']['billing_address_1']);

    $fields['billing']['billing_custom_email']  =  $fields['billing']['billing_email'];
    $fields['billing']['billing_custom_email']["label"] = "E-mailadres";
    $fields['billing']['billing_custom_email']["type"] = "email";
    $fields['billing']['billing_custom_email']["required"] = true;
    $fields['billing']['billing_custom_email']["class"][0] = "form-row-first";
    $fields['billing']['billing_custom_email']["autocomplete"] = "email";
    $fields['billing']['billing_custom_email']["priority"] = 110;
    $fields['billing']['billing_custom_email']['placeholder'] = 'E-mail';

    $fields['billing']['billing_email']['required'] = false;
    unset($fields['billing']['billing_email']['validate']);
    //    unset($fields['billing']['billing_email']);



    $order = array(
        "billing_first_name",
        "billing_last_name",
        "billing_street",
        "billing_postcode",
        "billing_city",
        "billing_phone",
        "billing_custom_email",
        //        "billing_email",
    );

    if ($subscription_iscompany == '1') {
        $order[] = 'billing_company';
        array_unshift($order, 'billing_company');
    }

    foreach ($order as $field) {
        $ordered_fields[$field] = $fields["billing"][$field];
    }
    $fields["billing"] = $ordered_fields;
    //    print_r($fields["billing"] );

    return $fields;
}

/**
 * @param $return
 * @param $cart_item
 * @return array|void
 */
function getFormCursist($return = false, $cart_item = [])
{

    global $woocommerce;
    $number = 1;
    $noCursist = 0;
    //in case of edit
    $_no_of_students = isset($cart_item["info"]["noCursists"]) && $cart_item["info"]["noCursists"] > 0 ? $cart_item["info"]["noCursists"] : 0;

    $subscription = $woocommerce->session->get('subscription');
    // print_r($subscription);


    if (!$return && !empty(isset($_POST['number']))) {
        $number = isset($_POST['number']) ? $_POST['number'] : 1;
        $noCursist = isset($_POST['noCursist']) ? $_POST['noCursist'] : 1;
        //$subscription = [];
    } else if ($_no_of_students > 0) { //edit cart
        $number = $_no_of_students;
        $subscription = $cart_item["info"];
    } else if ($cart = end($woocommerce->cart->get_cart())) {
        $number = $cart["quantity"];
        //client wants if number is greater every time when user came if any thing in the cart. this no should be 1
        $number = 1;
    }
    $loopStart = ($noCursist + 1);


    $html = "";
    for ($i = $loopStart; $i <= $number; $i++) {

        $field_values = prepare_student_values($subscription, $i);

        if (strpos($field_values['phone_cursist'], "+31") === false) {
            //$field_values['phone_cursist'] = "+31".$field_values['phone_cursist'];
        }


        $html = $html . '<div class="cursist student_info" id="cursist' . $i . '">
	    <div class="vc_row-fluid rowform">
            <div class="form-group">
                <div class="vc_column_container  vc_col-sm-9">
                    <div class="col-sm-9">
                            <div class="formsubheader">' . COURSE_STUDENTS_LBL["title"] . $i . '</div>
                    </div>
                </div>
            </div>
            <div class="clefar"></div>
            <div class="vc_row-fluid rowform">
                <div class="form-group">
                    <label class="vc_column_container  vc_col-sm-3 control-label hidden">' . COURSE_STUDENTS_LBL["name"] . '</label>
                    <div class="vc_column_container  vc_col-sm-9">
                        <div class="col-sm-6" style="padding-left:0px;">
                            <input type="text" name="firstname_cursist' . $i . '"
                            class="form-control firstname_cursist"
                            data-error_lbl = "firstname_cursist"
                            data-index="' . $i . '"
                            placeholder="' . COURSE_STUDENTS_LBL["firstname_cursist"] . '"  value="' . $field_values['firstname_cursist'] . '"/>
                        </div>
                        <div class="vc_col-sm-4 hidden">
                            <input type="hidden" name="middlename_cursist' . $i . '"
                             data-index="' . $i . '"
                            class="form-control middlename_cursist" placeholder="Tussenv." />
                        </div>
                        <div class="col-sm-6" style="padding-right:0px;">
                            <input type="text" name="lastname_cursist' . $i . '"
                            class="form-control lastname_cursist"
                            data-error_lbl = "lastname_cursist"
                            data-index="' . $i . '"
                            placeholder="' . COURSE_STUDENTS_LBL["lastname_cursist"] . '"  value="' . $field_values['lastname_cursist'] . '"  />
                        </div>
                    </div>
                </div>
            </div>
		    <div class="clear"></div>
            <div  class="vc_row-fluid rowform">
                <div class="form-group" >
                <!--    <label class="vc_column_container  vc_col-sm-3 control-label hidden">Geboorteplaats</label> -->
                    

                <div class="col-sm-6" style="padding-left:0px" >
                    
                    <input type="text" placeholder="' . COURSE_STUDENTS_LBL["birtdate_cursist_lbl"] . '"
                    name="birtdate_cursist' . $i . '"
                    data-error_lbl = "birtdate_cursist"
                    data-index="' . $i . '"
                    class="form-control number birtdate_cursist"
                    value="' . $field_values['birtdate_cursist'] . '"  />
                    <span id="helpAccountId" class="help-block hidden">' . COURSE_STUDENTS_LBL['birtdate_cursist'] . '</span>
                </div>
                   
                    
                </div>
            </div>
            <div class="clear"></div>
            <div style = "margin:0 !important;" class="vc_row-fluid rowform">
                <div style = "margin:0 !important;" class="form-group">
                   <!-- <label class="vc_column_container  vc_col-sm-3 control-label hidden">Geboortedatum</label>
                    
                    <div class="col-sm-6" style="padding-left:0px;">
                        <input type="text" name="placeofbrith_cursist' . $i . '"
                            class="form-control placeofbrith_cursist"
                            data-error_lbl = "placeofbrith_cursist"
                            data-index="' . $i . '"
                            placeholder="' . COURSE_STUDENTS_LBL["placeofbrith_cursist"] . '" value="' . $field_values['placeofbrith_cursist'] . '"  />
                    </div>
                   -->
                   
                   <div class="col-sm-6 pr-0" style="padding-left:0px;display:none" >
                        <input type="hidden" maxlength="' . CURSIST_PHONE_LENGTH . '" name="phone_cursist' . $i . '"
                        class="form-control phone_cursist"
                        data-error_lbl = "phone_cursist"
                        data-index="' . $i . '"
                        placeholder="' . COURSE_STUDENTS_LBL["phone_cursist"] . '" value=""/>
                    </div>
                    <div class="col-sm-6" style="padding-left:0px;display:none">
                        <input type="hidden" name="email_cursist' . $i . '"
                        class="form-control email_cursist"
                        data-error_lbl = "email_cursist"
                        data-index="' . $i . '"
                        placeholder="' . COURSE_STUDENTS_LBL["email_cursist"] . '" value="" />
                    </div>
                    
                   
                </div>
            </div>
            <div class="clear"></div>
              <div class="form-group mobile-form-group">
               <div class="vc_column_container  vc_col-sm-9">
                    <div class="col-sm-9 lp_add_student_col">
                           <div class="lp_add_student_col">
                                <a id="add_student_' . $i . '" class="lp_add_student" type="button">+ Cursist toevoegen </a>
                            </div>
                    </div>
                    
                    <div class="col-sm-3 lp_remove_student_col">
                      <a id="remove_student_' . $i . '" data-index="' . $i . '" href="javascript:void(0);" class="lp_remove_student click-me">' . COURSE_STUDENTS_LBL["delete_button"] . '</a>
                       <button id="remove_student_' . $i . '" data-index="' . $i . '"
                        class="lp_remove_student btn btn-danger btn-block click-me hidden" type="button" >' . COURSE_STUDENTS_LBL["delete_button"] . '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                    </div>
                   <div class="col-sm-3 lp_clear_student_field_col">
                      <a id="clear_student_fields_' . $i . '" data-index="' . $i . '" href="javascript:void(0);" class="lp_clear_student_fields click-me">' . COURSE_STUDENTS_LBL["clear_fields"] . '</a>
                       <button id="clear_student_fields_' . $i . '" data-index="' . $i . '"
                        class="lp_clear_student_fields btn btn-danger btn-block click-me hidden" type="button" >' . COURSE_STUDENTS_LBL["clear_fields"] . '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                    </div>
                    
                </div>
            </div>
	    </div>
	</div>';
    }

    if ($return) {
        //server side
        return [$html, $number];
    } else {
        //ajax
        echo $html;
        wp_die();
    }
}

/***
 * @param $subscription
 * @param $counter
 * @return void
 */
function prepare_student_values($subscription, $counter)
{

    $model = [
        "firstname_cursist" => isset($subscription["firstname_cursist" . $counter]) ? $subscription["firstname_cursist" . $counter] : "",
        "lastname_cursist" => isset($subscription["lastname_cursist" . $counter]) ? $subscription["lastname_cursist" . $counter] : "",
        "email_cursist" => isset($subscription["email_cursist" . $counter]) ? $subscription["email_cursist" . $counter] : "",
        "phone_cursist" => isset($subscription["phone_cursist" . $counter]) ? $subscription["phone_cursist" . $counter] : "",
        "placeofbrith_cursist" => isset($subscription["placeofbrith_cursist" . $counter]) ? $subscription["placeofbrith_cursist" . $counter] : "",
        "birtdate_cursist" => isset($subscription["birtdate_cursist" . $counter]) ? $subscription["birtdate_cursist" . $counter] : "",

    ];
    return $model;
}

add_action('wp_ajax_getFormCursist', 'getFormCursist');
add_action('wp_ajax_nopriv_getFormCursist', 'getFormCursist');
?>
<?php
/*
   * SUBSCRIPTION
   */
add_action('woocommerce_short_description', 'modify_short_description', 100);
//add_action('woocommerce_before_single_product','edu_woocommerce_before_single_product',100);
function modify_short_description($short)
{
    return renderCourseDetail() . $short;
}

function sc_subscription2()
{
    $process = isset($_REQUEST['process']) ? $_REQUEST['process'] : false;
    if (!$process) {
        //return renderCourseDetail();
    } else if ($process == 'buy') {
        return renderBuyCourse();
    }
}

add_shortcode('sc_subscription2', 'sc_subscription2');
function buy_course()
{
    return renderBuyCourse();
}

add_shortcode('buy_course', 'buy_course');

function getCoursesEvents()
{

    $id = $_POST['post_id'];
    $eduq_courses = getCourses($id);
    $events = get_events($eduq_courses);


    echo $events;
    die;
}

add_action('wp_ajax_getCoursesEvents', 'getCoursesEvents');
add_action('wp_ajax_nopriv_getCoursesEvents', 'getCoursesEvents');

function getCoursesEventDropDown()
{

    $id = $_POST['post_id'];
    $no_of_students = $_POST["no_of_students"];
    $previous_course = $_POST["previous_course"];


    $product = wc_get_product($id);
    $cart_courses = getCartCourses();
    $cart_item = isset($cart_courses[$id]) ? $cart_courses[$id] : [];
    $args = [
        "post_type" => "product",
        "post_name__in" => OPTIONAL_COURSES,
        'orderby' => 'ID',
        'order' => 'ASC',
    ];
    $optional_courses = wc_get_products($args);

    $eduq_course = getCourses($id);
    $event_list_html = prepareEventSelectList($eduq_course, -1);
    $optional_courses_html = renderOptionalCourses($id, $optional_courses, $cart_courses, $cart_item);
    $sidebar_html = renderSideBar($product, $optional_courses, $cart_courses, $no_of_students, $previous_course);
    $response = ["list" => $event_list_html, "fullybookedDate" => $eduq_course, "optional_courses" => $optional_courses_html, "sidebar" => $sidebar_html];
    echo json_encode($response);
    wp_die();
}

add_action('wp_ajax_getCoursesEventDropDown', 'getCoursesEventDropDown');
add_action('wp_ajax_nopriv_getCoursesEventDropDown', 'getCoursesEventDropDown');

function populateCourseDetail()
{

    $id = $_POST['post_id'];
    $course_availability = (int)$_POST['course_availability'];
    $eduq_courses = getCourses($id);
    $product = wc_get_product($id);
    //    include_once(EDUQ_COURSE_PLUGIN_WIZARD);
    include_once(EDUQ_COURSE_PLUGIN_CALENDAR);
    die;
}

add_action('wp_ajax_populateCourseDetail', 'populateCourseDetail');
add_action('wp_ajax_nopriv_populateCourseDetail', 'populateCourseDetail');

/**
 * Short cord
 * @return false|string
 *
 */
function render_calendar()
{ {
        ob_start();
        include_once(EDUQ_COURSE_PLUGIN_CALENDAR);
        return ob_get_clean();
        wp_die();
    }
}
add_shortcode('render_calendar', 'render_calendar');

//add to post_id
function addToCartViaSubmit()
{
    //existing cart
    $cart_items = getCartCourses();
    global $woocommerce;


    $product_id = $_POST['post_id'];

    $cart_item_key = isset($_POST["cart_item_key"]) ? $_POST["cart_item_key"] : "";
    $noCursists = $_POST['no_of_students'];
    $_POST["form"]["noCursists"] = $noCursists;

    $optional_courses = [];
    if (isset($_POST["optional_courses"])) {
        foreach ($_POST["optional_courses"] as $course_id) {
            $optional_courses[$course_id] = $course_id;
        }
    }

    if (!empty($_POST["form"])) {
        $woocommerce->session->set('subscription', $_POST["form"]);
    }

    //    $myfile = fopen("logs.txt", "w") or die("Unable to open file!");
    //    $txt = print_r($_POST,true);
    //    fwrite($myfile, "\n". $txt);


    $services = ["info" => $_POST["form"], "optional_courses" => $optional_courses];
    //    echo "<pre>";
    //    print_r($_POST);
    //    print_r($services);
    if (!empty($cart_item_key)) {

        if ($cart_items[$cart_item_key]["product_id"] == $product_id) {
            $woocommerce->cart->cart_contents[$cart_item_key]['info'] = $services["info"];
            $woocommerce->cart->cart_contents[$cart_item_key]['optional_courses'] = $services["optional_courses"];
            $woocommerce->cart->set_quantity($cart_item_key, $noCursists);
        }
        $woocommerce->cart->remove_cart_item($cart_item_key);
        $res = edu_add_to_cart($product_id, $noCursists, $services);
        //        fwrite($myfile, "\n". $txt);
    } else {
        //        print_r($product_id);
        //        print_r($noCursists);
        //        print_r($services);

        $res = edu_add_to_cart($product_id, $noCursists, $services);
        $txt = print_r($res, true);
        //        fwrite($myfile, "\n". $txt);
    }
    //    fclose($myfile);
    echo "";
    die;
}


//save custom option to make sure courses
add_action('woocommerce_add_order_item_meta', 'add_edu_order_item_meta', 10, 2);
function add_edu_order_item_meta($item_id, $values)
{

    wc_update_order_item_meta($item_id, 'optional_courses', $values["optional_courses"]);
    wc_update_order_item_meta($item_id, "info", $values["info"]);
}

/**
 * @param $product_id
 * @param $noCursists
 * @param $services
 * @return void
 */
function edu_add_to_cart($product_id, $noCursists, $services)
{
    global $woocommerce;

    $cart = $woocommerce->cart->add_to_cart($product_id, $noCursists, 0, [], $services);
    //    print_r($cart);

    $surcharge_price = isset($_POST['surcharge_price']) ? $_POST['surcharge_price'] : 0;
    $surcharge_price_name = isset($_POST['surcharge_price_name']) ? $_POST['surcharge_price_name'] : "";

    if ($surcharge_price > 0) {
        $woocommerce->cart->add_fee($surcharge_price_name, $surcharge_price);
    }
}

/**
 * @param $cart_items
 * @return void
 */
function reload_cart_services($cart_items)
{
    global $woocommerce;
    $services = [];
    $service_cart_items = [];
    refreshServiceCart($cart_items);

    $optional_courses = [];
    foreach ($cart_items as $cart_item_key => $item) {
        $_product = apply_filters('woocommerce_cart_item_product', $item['data'], $item, $cart_item_key);

        if (!empty($item['optional_courses'])) {
            $optional_courses[$cart_item_key] = [$_product->get_name(), $item['optional_courses'], $item["quantity"]];
            foreach ($item['optional_courses'] as $option) {
                if (!empty($services[$option])) {
                    $services[$option] = $services[$option] + $item["quantity"];
                } else {
                    $services[$option] = $item["quantity"];
                }
            }
        }
    }
    //    echo  "<pre>";
    //    print_r($optional_courses);
    //    print_r($services);
    //    die;
    if (!empty($services)) {
        foreach ($services as $service => $qty) {
            $cart_item_key = $woocommerce->cart->add_to_cart($service, $qty);
            $service_cart_items[$service] = $cart_item_key;
        }
    }
    return [$services, $service_cart_items, WC()->cart->get_cart()];
}

function get_services_from_cart($cart_items)
{
    $service_cart_items = [];
    foreach ($cart_items as $cart_item_key => $cart_item) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        $_slug = trim($_product->get_slug());
        $cart_items[$cart_item_key] = $cart_item;
        $cart_items[$cart_item_key]["_product"] = $_product;
        if (in_array($_slug, OPTIONAL_COURSES)) {
            $cart_items[$cart_item_key]["is_optional"] = true;
            $service_cart_items[$cart_item['product_id']] = $cart_item_key;
        } else {
            $cart_items[$cart_item_key]["is_optional"] = false;
        }
    }
    return [$cart_items, $service_cart_items];
}

function extractOrderOptionalItems($order_items)
{
    $service_cart_items = [];
    foreach ($order_items as $item_id => $item) {
        $_product = $item->get_product();
        $_slug = trim($_product->slug);
        $order_items[$item_id]["_product"] = $_product;

        if (in_array($_slug, OPTIONAL_COURSES)) {
            $order_items[$item_id]["is_optional"] = true;
            $service_cart_items[$_product->get_ID()] = ["item" => $item, "item_id" => $item_id];
        } else {
            $service_cart_items[$item_id]["is_optional"] = false;
        }
    }
    return [$order_items, $service_cart_items];
}

/**
 * Get Total icon
 * @return int|mixed
 */
function get_cart_icon_number()
{
    $total = 0;
    $cart_items = WC()->cart->get_cart();
    if (!empty($cart_items)) {
        list($cart_services, $service_cart_items, $cart_items) = reload_cart_services($cart_items);
        foreach ($cart_items as $cart_item) {
            if (!isset($cart_services[$cart_item['product_id']])) {
                $total = $total + $cart_item['quantity'];
            }
        }
    }
    return $total;
}

/**
 * Remove all services
 * @param $cart_items
 * @return void
 */
function refreshServiceCart($cart_items)
{
    $optional_courses = getOptionalCoursesFromWp();
    foreach ($optional_courses as $course) {
        $course_id = $course->get_ID();
        foreach ($cart_items as $cart_item_key => $cart_item) {
            if ($course_id == $cart_item['product_id']) {
                WC()->cart->remove_cart_item($cart_item_key);
                break;
            }
        }
    }
}

/**
 * @return array|stdClass
 */
function getOptionalCoursesFromWp($key = "id")
{
    $args = [
        "post_type" => "product",
        "post_name__in" => OPTIONAL_COURSES,
        'orderby' => 'ID',
        'order' => 'ASC',
    ];

    $optional_courses = [];
    foreach (wc_get_products($args) as $product) {
        $course_id = $key == "id" ? $product->get_ID() : $product->slug;
        $optional_courses[$course_id] = $product;
    }
    return $optional_courses;
}


add_action('wp_ajax_addToCartViaSubmit', 'addToCartViaSubmit');
add_action('wp_ajax_nopriv_addToCartViaSubmit', 'addToCartViaSubmit');

function sc_eventlist()
{
    global $post;
    $id = $post->ID;
    // 	echo $id.'-';
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

// SOMIN NEW CODE
add_filter('woocommerce_checkout_fields', 'sb_add_custom_checkout_field', 2002);

function sb_add_custom_checkout_field($fields)
{
    global $woocommerce;
    $subscription = $woocommerce->session->get('subscription');

    $noCursists = $subscription['noCursists'];
    $cursists = array();
    for ($i = 1; $i <= $noCursists; $i++) {
        // FIRST NAME
        $fields['billing']['firstname_cursist' . $i] = array(
            'label' => __('Voorletters', 'example'),
            'type' => 'text',
            'placeholder' => _x('Voorletters', 'placeholder', 'example'),
            'required' => false,
            'class' => array('firstname_cursist' . $i),
            'clear' => true
        );
        $fields['billing']['firstname_cursist' . $i]['default'] = $subscription['firstname_cursist' . $i];
        $fields['billing']['firstname_cursist' . $i]['priority'] = (120 + $i) * $i;

        // MIDDEL NAME
        $fields['billing']['middlename_cursist' . $i] = array(
            'label' => __('Tussenv.', 'example'),
            'type' => 'text',
            'placeholder' => _x('Tussenv.', 'placeholder', 'example'),
            'required' => false,
            'class' => array('middlename_cursist' . $i),
            'clear' => true
        );
        $fields['billing']['middlename_cursist' . $i]['default'] = $subscription['middlename_cursist' . $i];
        $fields['billing']['middlename_cursist' . $i]['priority'] = (121 + $i) * $i;


        // LAST NAME
        $fields['billing']['lastname_cursist' . $i] = array(
            'label' => __('Achternaam', 'example'),
            'type' => 'text',
            'placeholder' => _x('Achternaam', 'placeholder', 'example'),
            'required' => false,
            'class' => array('lastname_cursist' . $i),
        );
        $fields['billing']['lastname_cursist' . $i]['default'] = $subscription['lastname_cursist' . $i];
        $fields['billing']['lastname_cursist' . $i]['priority'] = (122 + $i) * $i;

        //        PLACE OF BIRTH
        //        $fields['billing']['placeofbrith_cursist' . $i] = array(
        //            'label' => __('Geboorteplaats', 'example'),
        //            'type' => 'text',
        //            'placeholder' => _x('Geboorteplaats', 'placeholder', 'example'),
        //            'required' => true,
        //            'class' => array('placeofbrith_cursist' . $i),
        //        );
        //               $fields['billing']['placeofbrith_cursist' . $i]['default'] = $subscription['placeofbrith_cursist' . $i];
        //        $fields['billing']['placeofbrith_cursist' . $i]['priority'] = (123 + $i) * $i;


        //   // DATE
        $fields['billing']['birtdate_cursist' . $i] = array(
            'label' => __('Geboortedatum', 'example'),
            'type' => 'text',
            'placeholder' => _x('Geboortedatum', 'placeholder', 'example'),
            'required' => false,
            'class' => array('birtdate_cursist' . $i),
        );
        $fields['billing']['birtdate_cursist' . $i]['default'] = $subscription['birtdate_cursist' . $i];
        $fields['billing']['birtdate_cursist' . $i]['priority'] = (124 + $i) * $i;
    }

    $fields['billing']['billing_email']['required'] = false;
    unset($fields['billing']['billing_email']['validate']);
    //    unset($fields['billing']['billing_email']);
    //    echo "<pre>";
    //    print_r($fields);
    return $fields;
}

add_action('woocommerce_checkout_update_order_meta', 'sb_checkout_field_update_order_meta');

function sb_checkout_field_update_order_meta($order_id)
{
    $noCursists = $_POST['billing_noCursists'];
    update_post_meta($order_id, 'billing_noCursists', sanitize_text_field($noCursists));

    for ($i = 1; $i <= $noCursists; $i++) {
        if (!empty($noCursists)) {
            update_post_meta($order_id, 'firstname_cursist' . $i, sanitize_text_field($_POST['firstname_cursist' . $i]));
            update_post_meta($order_id, 'middlename_cursist' . $i, sanitize_text_field($_POST['middlename_cursist' . $i]));
            update_post_meta($order_id, 'lastname_cursist' . $i, sanitize_text_field($_POST['lastname_cursist' . $i]));
            update_post_meta($order_id, 'placeofbrith_cursist' . $i, sanitize_text_field($_POST['placeofbrith_cursist' . $i]));
            update_post_meta($order_id, 'birtdate_cursist' . $i, sanitize_text_field($_POST['birtdate_cursist' . $i]));
        }
    }
}
function test_email()
{
    echo "<pre>";
    $order = wc_get_order('4746');
    getHmtl($order);
    // print_r($order);
    // echo "helo word";
    die;
    include "/home/syed/work/projects";
}
function getHmtl($order)
{
    define('BASE_PATH', getcwd());

    ob_start();
    // include ACF_GOOGLE_MAP_PLUGIN_DIR."/_map.php";

    // include BASE_PATH . "//wp-content/plugins/courses/fullyBooked.php";
    include "/home/syed/work/projects/eduq-live/wp-content/themes/eikra-child/woocommerce/emails/admin-new-order.php";
    return ob_get_clean();
    wp_die();
}
add_shortcode('email-test', 'test_email');

function sb_display_order_data_in_admin($order)
{
?>
    <div class="order_data_column1" style="display:none">
        <h4><?php _e('Cursist(en) gegevens:', 'woocommerce'); ?></h4>
        <div class="address">
            <?php $noCursists = get_post_meta($order->id, 'billing_noCursists', true); ?>

            <?php for ($i = 1; $i <= $noCursists; $i++) { ?>
                <?php
                echo '<div class="colum"><span class="strong" style="font-weight:bold; margin-right:5px; margin-bottom:5px;">' . __('First Name Cursist' . $i) . ':</span>' . get_post_meta($order->id, 'firstname_cursist' . $i, true) . '</div>';
                //echo '<div class="colum"><span class="strong" style="font-weight:bold; margin-right:5px; margin-bottom:5px;">' . __('Middel Name Cursist' . $i) . ':</span>' . get_post_meta($order->id, 'middlename_cursist' . $i, true) . '</div>';
                echo '<div class="colum"><span class="strong" style="font-weight:bold; margin-right:5px; margin-bottom:5px;">' . __('Last  Name Cursist' . $i) . ':</span>' . get_post_meta($order->id, 'lastname_cursist' . $i, true) . '</div>';
                echo '<div class="colum"><span class="strong" style="font-weight:bold; margin-right:5px; margin-bottom:5px;">' . __('Place of brith Cursist' . $i) . ':</span>' . get_post_meta($order->id, 'placeofbrith_cursist' . $i, true) . '</div>';
                echo '<div class="colum"><span class="strong" style="font-weight:bold; margin-right:5px; margin-bottom:5px;">' . __('Birtdate Cursist' . $i) . ':</span>' . get_post_meta($order->id, 'birtdate_cursist' . $i, true) . '</div>';
                ?>
            <?php } ?>
        </div>
    </div>
<?php
}

add_action('woocommerce_admin_order_data_after_shipping_address', 'sb_display_order_data_in_admin');

add_action('woocommerce_email_order_meta2', 'order_customer_Cursists_details', 5, 4);

function order_customer_Cursists_details($order, $sent_to_admin, $plain_text, $email)
{
    //$noCursists = get_post_meta($order->get_order_number(), 'billing_noCursists', true);
    $noCursists = get_post_meta($order->id, 'billing_noCursists', true);
    //$ono=$order->id;
    //echo "<p>In order_customer_Cursists_details function order no == ".$ono."</p>";
    // we won't display anything if it is not a gift
    if (empty($noCursists))
        return;
    echo '<h2 style="color:#f57d30;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">Cursist(en) gegevens:</h2>';
    echo '<table class="table" cellspacing="0" cellpadding="0" border="0" style="width:100%;vertical-align:top;margin-bottom:40px;padding:0">';
    echo '<thead>';
    echo '<tr>';
    echo '<th style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Aantal </th>';
    echo '<th style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Naam</th>';
    //     echo '<th> Middle Name </th>';
    //     echo '<th> last Name </th>';
    //    echo '<th style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Geboorteplaats</th>';
    echo '<th style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Geboortedatum</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    for ($i = 1; $i <= $noCursists; $i++) {
        $firstname_cursist = get_post_meta($order->id, 'firstname_cursist' . $i, true);
        $lastname_cursist = get_post_meta($order->id, 'lastname_cursist' . $i, true);
        $fullname_cursist = $firstname_cursist . ' ' . $lastname_cursist;
        $placeofbrith_cursist = get_post_meta($order->id, 'placeofbrith_cursist' . $i, true);
        $birtdate_cursist = get_post_meta($order->id, 'birtdate_cursist' . $i, true);
        echo '<tr>';
        echo '<td style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . $i . '</td>';
        echo '<td style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . $fullname_cursist . '</td>';
        echo '<td style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . $placeofbrith_cursist . '</td>';
        echo '<td style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . $birtdate_cursist . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

//add_action('woocommerce_email_order_meta_fields', 'add_Cursists_order_meta', 10, 3);
/*
   * @param $order_obj Order Object
   * @param $sent_to_admin If this email is for administrator or for a customer
   * @param $plain_text HTML or Plain text (can be configured in WooCommerce > Settings > Emails)
   */

function add_Cursists_order_meta($order_obj, $sent_to_admin, $order)
{
    //     print_r($order);
    // this order meta checks if order is marked as a gift
    if ($order->has_status('processing')) {
        $noCursists = get_post_meta($order->get_order_number(), 'billing_noCursists', true);
        // we won't display anything if it is not a gift
        if (empty($noCursists))
            return;
        echo '<h2 style="color:#f57d30;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">Cursist(en) gegevens:</h2>';
        echo '<table class="table" cellspacing="0" cellpadding="0" border="0" style="width:100%;vertical-align:top;margin-bottom:40px;padding:0">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Aantal </th>';
        echo '<th style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Naam</th>';
        //     echo '<th> Middle Name </th>';
        //     echo '<th> last Name </th>';
        //       echo '<th style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Geboorteplaats</th>';
        echo '<th style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">Geboortedatum</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        for ($i = 1; $i <= $noCursists; $i++) {
            $firstname_cursist = get_post_meta($order->get_order_number(), 'firstname_cursist' . $i, true);
            $lastname_cursist = get_post_meta($order->get_order_number(), 'lastname_cursist' . $i, true);
            $fullname_cursist = $firstname_cursist . ' ' . $lastname_cursist;
            $placeofbrith_cursist = get_post_meta($order->get_order_number(), 'placeofbrith_cursist' . $i, true);
            $birtdate_cursist = get_post_meta($order->get_order_number(), 'birtdate_cursist' . $i, true);
            echo '<tr>';
            echo '<td style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . $i . '</td>';
            echo '<td style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . $fullname_cursist . '</td>';
            echo '<td style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . $placeofbrith_cursist . '</td>';
            echo '<td style="color:#737973;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . $birtdate_cursist . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } elseif ($order->has_status('on-hold')) {
    }
}

//add_filter('woocommerce_available_payment_gateways', 'unset_gateway');

function unset_gateway($available_gateways)
{
    global $woocommerce;
    $subscription = $woocommerce->session->get('subscription');
    if ($subscription['payment'] == 0) {
        unset($available_gateways['bacs']);
    } elseif ($subscription['payment'] == 1) {
        unset($available_gateways['multisafepay_ideal']);
    }
    return $available_gateways;
}

add_action('woocommerce_email_customer_details2', 'order_customer_billing_details', 5, 4);

function order_customer_billing_details($order, $sent_to_admin, $plain_text, $email)
{
?>
    <style>
        .cursist-details {
            width: 100%;
            vertical-align: top;
            margin-bottom: 40px;
            padding: 0;
        }

        .bg-light {
            background: #f5f5f5;
        }
    </style>
<?php
    if (!empty($order->billing_company)) {
        $company = $order->billing_company . '<br/>';
    }
    echo '
	<table class="cursist-details" id="m_-7913608979401080979addresses" style="" cellspacing="0" cellpadding="0" border="0">
	<tbody><tr>
	<td style="text-align:left;border:0;padding:0" width="50%" valign="top">
	<h2 style="color:#f57d30;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">Factuuradres</h2>
                   <address class="m_-7913608979401080979address" style="padding:12px 12px 0;color:#737973;border:1px solid #e5e5e5">';
    echo $company;
    echo $order->billing_first_name . ' ' . $order->billing_last_name . '<br/>';
    echo $order->billing_address_1 . '<br/>';
    echo $order->billing_postcode . ' ';
    echo $order->billing_city . '<br/>';
    echo $order->billing_phone . '<br/>';
    echo $order->billing_email . '<br/><br/>';
    echo '</address><span class="HOEnZb"><font color="#888888">
		</font></span>
                </td>
                </tr>
                </tbody>
                </table>';
}


//function custom_new_order_email_recipient($recipient, $order)
//{
//    // Avoiding backend displayed error in Woocommerce email settings for undefined $order
//    if (!is_a($order, 'WC_Order'))
//        return $recipient;
//
//    // Check order items for a shipped product is in the order
//    foreach ($order->get_items() as $item) {
//        $product = $item->get_product(); // Get WC_Product instance Object
//        // When a product needs shipping we add the customer email to email recipients
//        if ($product->needs_shipping()) {
//            return $recipient . ',' . $order->get_billing_email();
//        }
//    }
//    return $recipient;
//}


/////#Add company name field Validation#/////
add_action('woocommerce_after_checkout_validation', 'add_validation_company_name', 10, 2);
function add_validation_company_name($data, $errors)
{
    $company_name = $_POST['companyname'];
    // company name validation
    if ($_POST["iscompany"] == 1) {
        if (empty($_POST['companyname'])) {
            $errors->add('company_name_validation', __(COMPANY_NAME_ERROR_MESSAGE), array('id' => "companyname"));
        } elseif (!preg_match("/^[a-zA-Z0-9 &+,@:\/='|()#.-]*$/", $company_name)) {
            $errors->add('company_name_validation', __(COMPANY_NAME_INVALID_ERROR_MESSAGE), array('id' => "companyname"));
        }
    }

    if (empty($_POST['billing_phone'])) {
        $errors->add('phone_validation', __(BILLING_PHONE_ERROR_MESSAGE), array('id' => "billing_phone"));
    } elseif (!preg_match('/^[0-9-\s]+$/', $_POST['billing_phone'])) {
        $errors->add('phone_validation', __(BILLING_PHONE_INVALID_ERROR_MESSAGE), array('id' => "billing_phone"));
    } else {
        $str_billing_phone_length = strlen($_POST['billing_phone']);
        if (!validate_phone($str_billing_phone_length)) {
            $errors->add('phone_validation', __(BILLING_PHONE_INVALID_ERROR_MESSAGE), array('id' => "billing_phone"));
        }
    }

    // first name validation

    if (empty($_POST['firstname'])) {
        $errors->add('firstname', __(USER_FIRST_NAME_ERROR_MESSAGE), array('id' => "firstname"));
    } elseif (!preg_match("/^[a-zA-Z][\w\s]*$/", $_POST['firstname'])) {
        $errors->add('firstname', __(USER_FIRST_NAME_INVALID_ERROR_MESSAGE), array('id' => "firstname"));
    }

    // second name validation

    if (empty($_POST['lastname'])) {
        $errors->add('last_name_validation', __(USER_LAST_NAME_ERROR_MESSAGE), array('id' => "lastname"));
    } elseif (!preg_match("/^[a-zA-Z][\w\s]*$/", $_POST['lastname'])) {
        $errors->add('last_name_validation', __(USER_LAST_NAME_INVALID_ERROR_MESSAGE), array('id' => "lastname"));
    }

    // email validation

    if (empty($_POST['email'])) {
        $errors->add('contact_email_validation', __(USER_EMAIL_ERROR_MESSAGE), array('id' => "email"));
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors->add('contact_email_validation', __(USER_EMAIL_INVALID_ERROR_MESSAGE), array('id' => "email"));
    }

    if (empty($_POST['billing_custom_email'])) {
        $errors->add('billing_custom_email', __(BILLING_CUSTOM_EMAIL_ERROR_MESSAGE), array('id' => "billing_custom_email"));
    } elseif (!filter_var($_POST['billing_custom_email'], FILTER_VALIDATE_EMAIL)) {
        $errors->add('billing_custom_email', __(BILLING_CUSTOM_EMAIL_INVALID_ERROR_MESSAGE), array('id' => "billing_custom_email"));
    }

    // phone number validaiton

    if (empty($_POST['billing_Phone_number'])) {
        $errors->add('contact_Phone', __(USER_PHONE_ERROR_MESSAGE));
    } elseif (!preg_match('/^[0-9-\s]+$/', $_POST['billing_Phone_number'])) {
        $errors->add('contact_Phone', __(USER_PHONE_INVALID_ERROR_MESSAGE), array('id' => "billing_Phone_number"));
    } else {
        $str_length = strlen($_POST['billing_Phone_number']);
        if (!validate_phone($str_length)) {
            $errors->add('contact_Phone', __(USER_PHONE_INVALID_ERROR_MESSAGE), array('id' => "billing_Phone_number"));
        }
    }

    if ($company_name  == 0 && $data["payment_method"] === "bacs") {
        //$errors->add('payment_method_validation', __(USER_PHONE_INVALID_ERROR_MESSAGE), array('id' => "payment_method"));
    }

    $multisafepay_ideal_issuer_id = isset($_POST["multisafepay_ideal_issuer_id"]) ? $_POST["multisafepay_ideal_issuer_id"] : "";

    if ($data["payment_method"] === "multisafepay_ideal" && $multisafepay_ideal_issuer_id == "") {
        $errors->add('multisafepay_ideal_validation', __(MULTISAFEPAY_IDEAL_VALID_MESSAGE));
    }
}


function validate_phone($str_length)
{
    return ($str_length < CURSIST_PHONE_MAX_LENGTH && $str_length > CURSIST_PHONE_MIN_LENGTH);
}

add_filter('woocommerce_email_recipient_new_order', 'eduq_email_recipient_filter_function_new', 10, 2);

add_filter('woocommerce_email_recipient_customer_on_hold_order', 'eduq_email_recipient_filter_function_hold', 10, 2);
add_filter('woocommerce_email_recipient_customer_processing_order', 'eduq_email_recipient_filter_function_processing', 10, 2);
//  add_filter('woocommerce_email_recipient_pending_order', 'eduq_email_recipient_filter_function', 10, 2);
function eduq_email_recipient_filter_function_new($recipient, $object)
{


    list($recepients_hash, $billing_email, $email) =  getRecepientHash($recipient, $object);



    unset($recepients_hash[$billing_email]);
    unset($recepients_hash[$email]);


    $recipient_changed = implode(",", $recepients_hash);

    // $order_id  = $object->get_id(); // Get the order ID
    // $order_number = trim(str_replace('#', '', $object->get_order_number())); //get the order status
    // $order_status  = $object->get_status(); // Get the order status (see the conditional method has_status() below)

    // $path = get_home_path();
    // $file = fopen($path . "order_log.log", "a");
    // $ara  = "method = new, order_id = " . $order_id . " , status = " . $order_status . ", receiptets = " . $recipient . ",order_number = " . $order_number . ",email= " . $recipient_changed . "\n";
    // fwrite($file, $ara);
    // fclose($file);

    return $recipient_changed;
}
function eduq_email_recipient_filter_function_processing($recipient, $object)
{

    //to escape # from order id


    list($recepients_hash, $billing_email, $email) =  getRecepientHash($recipient, $object);
    // $order_id  = $object->get_id(); // Get the order ID
    // $order_number = trim(str_replace('#', '', $object->get_order_number()));
    // $order_status  = $object->get_status(); // Get the order status (see the conditional method has_status() below)

    // $path = get_home_path();
    // $file = fopen($path . "order_log.log", "a");
    // $ara  = "method = processing,  order_id = " . $order_id . " , status = " . $order_status . ", receiptets = " . $recipient . ",order_number = " . $order_number . ",email= " . $email . "\n";
    // fwrite($file, $ara);

    $payment_method = "";
    if (!empty($_POST['payment_method'])) {
        $payment_method = $_POST['payment_method'];
    } else if (!empty($object)) {
        $payment_method = $object->get_payment_method();
    }
    // fclose($file);
    return $email;
}

function eduq_email_recipient_filter_function_hold($recipient, $object)
{

    //to escape # from order id



    list($recepients_hash, $billing_email, $email) =  getRecepientHash($recipient, $object);
    // $order_id  = $object->get_id(); // Get the order ID
    // $order_number = trim(str_replace('#', '', $object->get_order_number()));
    // $order_status  = $object->get_status(); // Get the order status (see the conditional method has_status() below)

    // $path = get_home_path();
    // $file = fopen($path . "order_log.log", "a");
    // $ara  = "method = hold,  order_id = " . $order_id . " , status = " . $order_status . ", receiptets = " . $recipient . ",order_number = " . $order_number . ",email= " . $email . "\n";
    // fwrite($file, $ara);

    $payment_method = "";
    if (!empty($_POST['payment_method'])) {
        $payment_method = $_POST['payment_method'];
    } else if (!empty($object)) {
        $payment_method = $object->get_payment_method();
    }
    // fclose($file);
    return $email;
}






function getRecepientHash($recipient, $object)
{
    $recepients_arr = explode(",", $recipient);
    $recepients_hash = [];
    foreach ($recepients_arr as $email_address) {
        $recepients_hash[$email_address] = $email_address;
    }


    if (!empty($_POST["email"])) {
        $email =  $_POST["email"];
    } else if (!empty($object)) {
        $email = $object->get_meta('_user_info')["_email_adress"];
    }
    $recepients_hash[$email] = $email;


    if (!empty($_POST["billing_email"])) {
        $billing_email = $_POST["billing_email"];
    } else  if (!empty($object)) {
        $billing_email = $object->get_meta('_user_info')["_billing_custom_email"];
    }

    return [$recepients_hash, $billing_email, $email];
}

add_filter('woocommerce_cart_calculate_fees', 'remove_woocommerce_discounts');

function remove_woocommerce_discounts($cart)
{
    $cart->remove_coupon($cart->get_applied_coupons());
}
?>
