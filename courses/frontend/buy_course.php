<?php

function renderBuyCourse()
{
  wp_enqueue_script("select2", "//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js");
  wp_enqueue_style("select2_style", "//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css");
  wp_enqueue_style('edu_bootstrap_select_css', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css');
  wp_enqueue_style('edu_wizard_css', EDUQ_COURSE_ASSETS_URL . 'styles/jquery.steps.css', false, '6.9');
  wp_enqueue_style('bootstrap-datetimepicker-css', EDUQ_COURSE_ASSETS_URL . 'datepicker/bootstrap-datetimepicker.css', false, '1.3');

  // enqueue jQuery library and the script you registered above
  wp_register_script("courses_events", EDUQ_COURSE_ASSETS_URL . 'scripts/courses_events.js');
  wp_register_script("utilsInput", EDUQ_COURSE_ASSETS_URL . 'scripts/utils-mock.js');

  // masked input
  wp_enqueue_script('edu_jquery_masked', EDUQ_COURSE_ASSETS_URL . 'scripts/jquery.masked_input.js', false, '1.3', true);
  // flag input
  wp_enqueue_script('edu_jquery_flagInput', EDUQ_COURSE_ASSETS_URL . 'scripts/intlTelInput.js', false, '1.7', true);
  // wp_enqueue_script('edu_jquery_latest', 'https://code.jquery.com/jquery-latest.min.js', false, '1.4', true);

  wp_localize_script('utilsInput', 'UtilsJs', EDUQ_COURSE_ASSETS_URL . 'scripts/utils.js');
  wp_enqueue_script('utilsInput');

  wp_enqueue_style('edu_flagInput', EDUQ_COURSE_ASSETS_URL . 'styles/intlTelInput.css', false, '1.4');


  wp_localize_script('courses_events', 'CoursesAjax', array('ajaxurl' => admin_url('admin-ajax.php')));


  wp_enqueue_script('courses_events');

  wp_enqueue_script("edu_bootstrap_select_js", EDUQ_COURSE_ASSETS_URL . 'scripts/bootstrap-select.min.js', false, '1.1', true);

  wp_enqueue_script("moment-with-locales-js", EDUQ_COURSE_ASSETS_URL . 'datepicker/moment-with-locales.min.js', false, '1.1', true);
  wp_enqueue_script("bootstrap-datetimepicker-js", EDUQ_COURSE_ASSETS_URL . 'datepicker/bootstrap-datetimepicker.min.js', false, '1.3', true);

  wp_enqueue_script('edu_jquery_ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', false, '2.5.8', true);


  wp_enqueue_script('edu_checkout_flow_js', EDUQ_COURSE_ASSETS_URL . 'scripts/checkout.js', false, '3.4.8', true);


  wp_enqueue_style('edu_wizard_responsive', EDUQ_COURSE_ASSETS_URL . 'styles/responsive-form.css', false, '1.7');
  wp_enqueue_style('edu_sidebar_responsive', EDUQ_COURSE_ASSETS_URL . 'styles/sidebar-responsive.css', false, '1.6');
?>

  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> -->

  <!-- <script>
    var $ = jQuery.noConflict();
  </script> -->
<?php
  $course_id = $_REQUEST["course_id"];
  $event_id = $_REQUEST["event_id"];
  $cart = !empty($_REQUEST["cart"]) ? $_REQUEST["cart"] : 0;
  //cart step
  if ($cart == 1) {
    if (!is_admin()) {
      global $woocommerce;


      if (isset($_POST['noCursists'])) {

        /**
         * if($existing_cart = $woocommerce->cart->get_cart()){
         * foreach ($existing_cart as $cart_item_key => $cart_item ) {
         * $woocommerce->cart->empty_cart();
         * $noCursists = (int) $cart_item['quantity'];
         * $product_id = $cart_item['product_id'];
         * $woocommerce->cart->add_to_cart($product_id, $noCursists);
         * }
         *
         * }
         * else {
         * $woocommerce->cart->empty_cart();
         * }
         */


        //                $url = get_permalink(get_option('woocommerce_checkout_page_id'));

        $url = wc_get_cart_url() . "?course_id=" . $course_id;
        header('Location:' . $url);
      }
    }
  } else {
    /**
     * wp_enqueue_script( "edu_submit_form",EDUQ_COURSE_ASSETS_URL.'scripts/submit-form.js',false,'1.7',true );
     */


    $cart_item = [];
    $cart_key = "";
    if (isset($_REQUEST["cart_key"])) {
      $cart_key = $_REQUEST["cart_key"];
      $cart_item = getEduNlCartInfo($cart_key);
    } else {
      $cart_items = getCartCourses();
      if (isset($cart_items[$course_id])) {
        $cart_item = $cart_items[$course_id];
        $cart_key = $cart_item["key"];
      }
    }
    return render_buy_process($course_id, $event_id, $cart_item, $cart_key);
  }
}
