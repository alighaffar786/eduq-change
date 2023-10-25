<?php

/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */
defined('ABSPATH') || exit;
wp_enqueue_style('edu_wizard_css', EDUQ_COURSE_ASSETS_URL . 'styles/jquery.steps.css', false, '2.9',);
?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<?php

// flag input
wp_register_script("utilsInput", EDUQ_COURSE_ASSETS_URL . 'scripts/utils-mock.js');

wp_enqueue_script('edu_jquery_flagInput', EDUQ_COURSE_ASSETS_URL . 'scripts/intlTelInput.js', false, '1.7', true);
wp_enqueue_script('edu_jquery_latest',  'https://code.jquery.com/jquery-latest.min.js', false, '1.4', true);

wp_localize_script('utilsInput', 'UtilsJs', EDUQ_COURSE_ASSETS_URL . 'scripts/utils.js');
wp_enqueue_script('utilsInput');
wp_enqueue_style('edu_flagInput', EDUQ_COURSE_ASSETS_URL . 'styles/intlTelInput.css', false, '1.4');

wp_enqueue_script('edu_wizard_flow_js', EDUQ_COURSE_ASSETS_URL . 'scripts/payment.js', false, '3.4', true);

?>


<?php

global $woocommerce;
$subscription = $woocommerce->session->get('subscription');

$firstEverStduent = reset($woocommerce->session->cart)['info'];
$noCursists = 0;
$no_of_courses = 0;
$optional_courses = getOptionalCoursesFromWp();
foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) {
  if (!isset($optional_courses[$cart_item['product_id']])) {
    $no_of_courses++;
  }
  $noCursists += $cart_item['quantity'];
}


$fields = $checkout->get_checkout_fields('billing');

$subscription_companyname = isset($subscription['companyname']) ? $subscription['companyname'] : "";

add_filter('woocommerce_billing_fields', 'custom_billing_fields');
function custom_billing_fields($fields)
{
  // Only on checkout page
  if (is_checkout() && !is_wc_endpoint_url()) {
    $fields['billing_birth_date']['required'] = false;
  }
  return $fields;
}

add_filter('woocommerce_available_payment_gateways', 'payment_gateways_based_on_chosen_shipping_method', 200);
function payment_gateways_based_on_chosen_shipping_method($available_gateways)
{

  // Get chosen shipping methods
  $is_company = -1;
  if (!empty($_POST["post_data"])) {
    $explode = explode("&", $_POST["post_data"]);
    foreach ($explode as $d) {
      if (strpos($d, "iscompany") !== false) {
        $is_company = explode("=", $d)[1];
      }
    }
  }
  if ($is_company < 1) {
    unset($available_gateways['bacs']);
  }
  //  echo "here";
  //  echo print_r($available_gateways,true);
  return $available_gateways;
}
?>
<div class="woocommerce-billing-fields">
  <?php if (wc_ship_to_billing_address_only() && WC()->cart->needs_shipping()) : ?>

    <h3><?php esc_html_e('Billing &amp; Shipping', 'woocommerce'); ?></h3>

  <?php else : ?>

  <?php endif; ?>

  <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>
  <div class="row">
    <div class="col-lg-12 field_offset_0">
      <div class="panel panel-default">
        <div class="panel-heading">KLANT-/BEDRIJFSGEGEVENS</div>
        <div class="panel-body">
          <div class="form-group">
            <label class="col-sm-3 control-label field_offset_0">Aanmelden als</label>
            <div class="clear"></div>
            <div class="column_container col-sm-9 field_offset_0 company_selection">
              <label class="radio-inline">
                <input class="radio_type" type="radio" id="iscompany" name="iscompany" value="0" CHECKED>Particulier</label>
              <label class="radio-inline">
                <input class="radio_type company" type="radio" id="iscompany" name="iscompany" value="1">Zakelijk</label>
            </div>
          </div>
          <div class="clear"></div>
          <div class="form-group hidden" id="company_group">
            <label for="companyname" class="control-label">Bedrijfsnaam <span>*</span></label>
            <div class="clear"></div>
            <div class="col-sm-12 field_offset_0">
              <?php
              $company_args = [
                "label" => false,
                "required" => true,
                "input_class" => ["form-control", "fontrol-border-radius"],
                "placeholder" => "Bedrijfsnaam"
              ];
              $company_field =  edu_woocommerce_form_field('companyname', $company_args, null);

              echo $company_field;
              ?>
              <!-- <input style="border-radius: 0; height:40px;" type="text" id="companyname" name="companyname" class="form-control validate-required businessfield" placeholder="Bedrijfsnaam" required="true" value="" /> -->
            </div>
          </div>
          <div class="clear"></div>
          <div class="row">
            <div class="form-group">
              <label class="col-sm-3 control-label">Contactpersoon <span>*</span></label>
              <div class="clear"></div>
              <div class="col-sm-12">
                <div class="col-sm-6" style="padding-left:0px;">
                  <?php
                  $first_args = [
                    "label" => false,
                    "required" => true,
                    "input_class" => ["form-control", "fontrol-border-radius"],
                    "placeholder" => "Voorletters"
                  ];
                  $first_name_field =  edu_woocommerce_form_field('firstname', $first_args, $firstEverStduent['firstname_cursist1']);

                  echo $first_name_field;
                  ?>
                  <!-- <input style="border-radius: 0; height:40px;" type="text" id="firstname" name="firstname" class="form-control" placeholder="Voorletters" required="true" value="<?php echo $firstEverStduent['firstname_cursist1']; ?>" /> -->
                </div>
                <div class="col-sm-4 hidden">
                  <input type="hidden" name="middlename" class="form-control " placeholder="Tussenv." />
                </div>
                <div class="col-sm-6" style="padding-right:0px;">
                  <?php
                  $last_args = [
                    "label" => false,
                    "required" => true,
                    "input_class" => ["form-control", "fontrol-border-radius"],
                    "placeholder" => "Achternaam"
                  ];
                  $last_name_field =  edu_woocommerce_form_field('lastname', $last_args, $firstEverStduent['lastname_cursist1']);

                  echo $last_name_field;
                  ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row top-10">
            <div class="form-group contact-form">

              <div class="clear"></div>
              <div class="col-sm-12">
                <div class="col-sm-6" style="padding-left:0px;">
                  <?php
                  $email_args = [
                    "type" => "email",
                    "label" => false,
                    "required" => true,
                    "input_class" => ["form-control", "fontrol-border-radius"],
                    "placeholder" => "E-mail",
                    'validate'  => ['email'],
                  ];
                  $email_field =  edu_woocommerce_form_field('email', $email_args, null);

                  echo $email_field;
                  ?>


                  <!-- <input style="border-radius: 0; height:40px;" type="email" id="email" name="email" class="form-control" placeholder="E-mail *" required="true" value="<?php echo $firstEverStduent['email_cursist1']; ?>"> -->
                </div>

                <div class="col-sm-6" style="padding-right:0px;">
                  <?php
                  $phone_args = [
                    "type" => "tel",
                    'maxlength'         => 15,
                    "label" => false,
                    "required" => true,
                    "input_class" => ["form-control", "fontrol-border-radius"],
                    "placeholder" => "Telefoon",

                  ];
                  $phone_field =  edu_woocommerce_form_field('billing_Phone_number', $phone_args, null);

                  echo $phone_field;
                  ?>
                  <!-- <input style="border-radius: 0; height:40px;" type="tel" maxlength="<?php echo CURSIST_PHONE_LENGTH; ?>" name="phone" class="form-control number billing_Phone_number" id="billing_Phone_number" placeholder="Telefoon *" required="true" value="<?php echo "(0)" . $firstEverStduent['phone_cursist1']; ?>"> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.col-lg-12 -->
  </div>
  <script>
    jQuery(document).ready(function() {

      jQuery("ul.woocommerce-error li[data-id]").each(function(i, obj) {
        let field_id = jQuery(obj).attr("data-id");

        jQuery(`#${field_id}`).css({
          "border-color": "#b81c23"
        })
        // jQuery(`p  #${field_id}_field`).addClass("woocommerce-invalid");
      })
    })
  </script>
  <div class="row">

    <div class="panel panel-default">
      <div class="panel-heading"><?php _e('Factuurgegevens', 'woocommerce'); ?></div>
      <div class="panel-body">
        <div class="customer-info-override">

          <?php
          //set billing info
          $billing_info = [
            "billing_first_name" => !empty($checkout->get_value('billing_first_name')) ? $checkout->get_value('billing_first_name') : $firstEverStduent["firstname_cursist1"],
            "billing_last_name" => !empty($checkout->get_value('billing_last_name')) ? $checkout->get_value('billing_last_name') : $firstEverStduent["lastname_cursist1"],
            "billing_phone" => !empty($checkout->get_value('billing_phone')) ? $checkout->get_value('billing_phone') : $firstEverStduent["phone_cursist1"],
            "billing_email" => !empty($checkout->get_value('billing_email')) ? $checkout->get_value('billing_email') : $firstEverStduent["email_cursist1"],
            "billing_custom_email" => !empty($checkout->get_value('billing_custom_email')) ? $checkout->get_value('billing_email') : $firstEverStduent["email_cursist1"],
          ];
          ?>

          <?php if (!empty($subscription_companyname)) { ?>
            <div class="cs-form-row">
              <div class="vc_col-sm-12">
                <?php
                die(">>>>>>>>>>>>>>>>>>>>>>add_validation_company_name>>>");
                echo edu_woocommerce_form_field('billing_company', $fields['billing_company'], $checkout->get_value('billing_company'));
                ?>
              </div>
            </div>
          <?php } ?>
          <div class="cs-form-row c-fullname">
            <div class="vc_col-sm-6">
              <?php
              //Voornaam
              $billing_first_name  = $fields['billing_first_name'];
              $billing_first_name["placeholder"] = "Voornaam";
              echo edu_woocommerce_form_field('billing_first_name', $billing_first_name, $billing_info["billing_first_name"]);
              ?>
            </div>

            <div class="vc_col-sm-6">
              <?php
              echo edu_woocommerce_form_field('billing_last_name', $fields['billing_last_name'], $billing_info["billing_last_name"]);
              ?>
            </div>
          </div>


          <div class="cs-form-row">
            <div class="vc_col-sm-12">
              <?php
              echo edu_woocommerce_form_field('billing_street', $fields['billing_street'], $checkout->get_value('billing_street'));
              ?>
            </div>
            <!--                        <div class="vc_col-sm-6">-->
            <!--                            --><?php
                                                //                            echo edu_woocommerce_form_field('billing_housenumber', $fields ['billing_housenumber'], $checkout->get_value('billing_housenumber'));
                                                //
                                                ?>
            <!--                        </div>-->
          </div>
          <div class="cs-form-row">
            <div class="vc_col-sm-6">
              <?php
              echo edu_woocommerce_form_field('billing_postcode', $fields['billing_postcode'], $checkout->get_value('billing_postcode'));
              ?>
            </div>
            <div class="vc_col-sm-6">
              <?php
              echo edu_woocommerce_form_field('billing_city', $fields['billing_city'], $checkout->get_value('billing_city'));
              ?>
            </div>
            <div class="vc_col-sm-6 hidden">
              <?php
              $countries_obj = new WC_Countries();
              $countries = $countries_obj->__get('countries');
              echo '<label for="billing_country" class="">' . __('Countries') . '&nbsp;<abbr class="required" title="required">*</abbr></label>';
              echo '<span class="woocommerce-input-wrapper">';
              //                    echo edu_woocommerce_form_field('billing_country', array(
              //                        'type' => 'select',
              //                        'class' => array('input-text'),
              //                        'placeholder' => __('Enter something'),
              //                        'options' => $countries,
              //                        'default' => $checkout->get_value('billing_country')
              //                            )
              //                    );
              echo '</span>';
              ?>
            </div>
          </div>
          <div class="cs-form-row">
            <div class="vc_col-sm-6">
              <?php
              $fields['billing_custom_email']['validate'] = ['email'];
              $billing_field_email  = $fields['billing_custom_email'];
              echo edu_woocommerce_form_field('billing_custom_email', $billing_field_email, $billing_info["billing_custom_email"]);
              ?>
              <input type="hidden" id="billing_email" name="billing_email" value="eduq@eduq.com" />
            </div>

            <div class="vc_col-sm-6">
              <?php
              echo edu_woocommerce_form_field('billing_phone', $fields['billing_phone'], $billing_info["billing_phone"]);
              ?>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-default hidden">

      <div class="panel-body">
        <table class="table">
          <tr>
            <th><?php echo COURSE_CHECKOUT_LBL["no_of_courses"] ?></th>
            <td><?php echo $no_of_courses; ?></td>
          </tr>
        </table>
      </div>
    </div>


  </div>
  <div class="participants-info hidden">
    <div class="cs-form-row">
      <div class="col-md-12">
        <h3 class="field-heading"><?php _e('Cursist(en) gegevens', 'woocommerce'); ?></h3>
      </div>
    </div>
    <input type="hidden" class="input-text " name="billing_noCursists" id="billing_noCursists" value="<?php echo $noCursists; ?>">
    <?php
    for ($Cursists = 1; $Cursists <= $noCursists; $Cursists++) {
    ?>
      <div id="participant-<?php echo $Cursists; ?>" class="participant-info">
        <div class="cs-form-row">
          <div class="col-md-12 formsubheader">
            <h5 class=""><?php _e('Deelnemer ' . $Cursists, 'woocommerce'); ?></h5>
          </div>
        </div>
        <div class="cs-form-row c-fullname">
          <?php

          $cursist_full_name = array('firstname_cursist' . $Cursists, 'lastname_cursist' . $Cursists);
          for ($index = 0; $index <= 1; $index++) {
            $key = $cursist_full_name[$index];
            $field = $fields[$key];
            echo ' <div class="vc_col-sm-6">';
            echo edu_woocommerce_form_field($key, $field, $checkout->get_value($key));
            echo '</div>';
          }
          ?>
        </div>

      </div>
    <?php }
    ?>
  </div>

  <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>

<?php if (!is_user_logged_in() && $checkout->is_registration_enabled()) : ?>
  <div class="woocommerce-account-fields">
    <?php if (!$checkout->is_registration_required()) : ?>

      <p class="form-row form-row-wide create-account">
        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
          <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked((true === $checkout->get_value('createaccount') || (true === apply_filters('woocommerce_create_account_default_checked', false))), true); ?> type="checkbox" name="createaccount" value="1" />
          <span><?php esc_html_e('Create an account?', 'woocommerce'); ?></span>
        </label>
      </p>

    <?php endif; ?>

    <?php do_action('woocommerce_before_checkout_registration_form', $checkout); ?>


    <?php if ($checkout->get_checkout_fields('account')) : ?>

      <div class="create-account">
        <?php foreach ($checkout->get_checkout_fields('account') as $key => $field) : ?>
          <?php echo edu_woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
        <?php endforeach; ?>
        <div class="clear"></div>
      </div>

    <?php endif; ?>

    <?php do_action('woocommerce_after_checkout_registration_form', $checkout); ?>

  </div>
<?php endif;
	add_action( 'wp_footer', function(){
 
	// we need it only on our checkout page
	if( ! is_checkout() ) {
		return;
	}
 
	?>
	<script>
    jQuery(function($) {
      jQuery('body').on('blur change', 'form[name="checkout"]', function() {
        // const wrapper = jQuery(this).closest('#billing_phone');
         jQuery(this).removeAttr('style');
      });
    });
  </script>
	<?php
});

 ?>
