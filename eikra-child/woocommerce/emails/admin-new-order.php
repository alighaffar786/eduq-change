<?php

/**
 * Admin new order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 3.5.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email);
?>

<?php if ($order->has_status('pending')) : ?>
    <p>
        <?php
        printf(
            wp_kses(
                /* translators: %1s item is the name of the site, %2s is a html link */
                __('An order has been created for you on %1$s. %2$s', 'woocommerce'),
                array(
                    'a' => array(
                        'href' => array(),
                    ),
                )
            ),
            esc_html(get_bloginfo('name', 'display')),
            '<a style="text-decoration: none;" href="' . esc_url($order->get_checkout_payment_url()) . '">' . esc_html__('Pay for this order', 'woocommerce') . '</a>'
        );
        ?>
    </p>
<?php endif; ?>
<?php
global $woocommerce;
if (!empty($woocommerce->session)) {
    $subscription = $woocommerce->session->get('subscription');
    $course = $subscription['course'];
    $cursists = $woocommerce->session->get('info_cursists');
    $type = array(0 => "Particulier", 1 => "Zakelijk");
}
?>
<p>Bedankt voor je inschrijving bij EDUQ Opleidingen.<br>
    Onze BHV, VCA en Heftruck cursussen vinden plaats op de Waarderweg 19 te Haarlem. Alle cursussen starten om 8.00 uur. De VCA cursussen duren tot 17.15 en de BHV Basis en Heftruck cursussen duren tot 16.00 uur. Voor BHV Herhaal is de eindtijd 14.30.
    <br><br>
    Je ontvangt nog een bevestigingsmail met meer informatie over de cursusdag. Een factuur van je bestelling wordt gestuurd naar het opgegeven factuuradres.<br />
</p>
<?php
// echo "<pre>";
// print_r($woocommerce->session);
// die;

if (isset($course) && !empty($course)) {
    $coursedata = explode(' - ', $course);
    $coursetitle = explode('- ', $coursedata[1]);
}
?>
<br />
<h2 style="color: rgb(245, 125, 48); display: block; font-family:Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0px 0px 18px; text-align: left;">Cursist(en) gegevens</h2>
<table cellspacing="0" cellpadding="6" border="1" style="color:#737973;    border-color: #e5e5e5; ;vertical-align:middle;width:800px;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
    <tbody id="order_line_items">
        <?php
        $line_items = $order->get_items(apply_filters('woocommerce_admin_order_item_types', 'line_item'));
        $optional_courses = getOptionalCoursesFromWp();
        $total_no_students = 0;
        foreach ($line_items as $item_id => $item) {
            $info = wc_get_order_item_meta($item_id, "info", true);
            $line_items[$item_id]["info"] = $info;
            if (isset($info["noCursists"])) {
                $total_no_students = $total_no_students + $info["noCursists"];
            }
        }
        // get_permalink($item->get_product_id())
        // $item->get_name()

        foreach ($line_items as $item_id => $item) {
            if (isset($item["info"])) {
                $product = $item->get_product();
                $info = $item["info"];
                if (!isset($optional_courses[$product->get_ID()])) {
                    echo "<tr style='font-weight: bold'>";
                    echo "<td  colspan='3'><a style='text-decoration: none;' href='" . get_permalink($item->get_product_id()) . "'>" . $item->get_name() . "</a></td>";
                    echo "</tr><tr  style='font-weight: bold'>";
                    echo   "<td style='text-align: center;'>Voornaam</td>
                                    <td style='text-align: center;'>Achternaam</td>
                                    <td style='text-align: center;'>Geboortedatum</td>
                                </tr>";
                    for ($i = 1; $i <= $info["noCursists"]; $i++) {
                        echo "<tr>";
                        echo "<td style='text-align: center;'>" . $info["firstname_cursist" . $i] . "</td>";
                        echo "<td style='text-align: center;'>" . $info["lastname_cursist" . $i] . "</td>";
                        echo "<td style='text-align: center;'>" . $info["birtdate_cursist" . $i] . "</td>";
                        echo "</tr>";
                    }
                }
            }
        }
        ?>
    </tbody>
</table><br />
<?php
defined('ABSPATH') || exit;
global $wpdb;
$payment_gateway     = wc_get_payment_gateway_by_order($order);
$line_items          = $order->get_items(apply_filters('woocommerce_admin_order_item_types', 'line_item'));
$optional_courses = getOptionalCoursesFromWp();
list($line_items, $service_items) = extractOrderOptionalItems($line_items);
$discounts           = $order->get_items('discount');
$line_items_fee      = $order->get_items('fee');
$line_items_shipping = $order->get_items('shipping');
if (wc_tax_enabled()) {
    $order_taxes      = $order->get_taxes();
    $tax_classes      = WC_Tax::get_tax_classes();
    $classes_options  = wc_get_product_tax_class_options();
    $show_tax_columns = count($order_taxes) === 1;
}
?>
<h2 style="color: rgb(245, 125, 48); display: block; font-family:Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0px 0px 18px; text-align: left;"> Overzicht Cursus(sen)</h2>
<table id="custon-table1" cellspacing="0" cellpadding="6" border="1" style=" color:#737973; border-color: #e5e5e5;vertical-align:middle;width:800px;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
    <thead>
        <tr>
            <th class="item sortable" colspan="1" data-sort="string-ins">Cursus(sen)</th>
            <th style='text-align: center;' class="sortable" data-sort="date">Datum</th>
            <?php do_action('woocommerce_admin_order_item_headers', $order); ?>
            <th style='text-align: center;' class="item_cost sortable" data-sort="float"><?php esc_html_e('Cost', 'woocommerce'); ?></th>
            <th style='text-align: center;' class="quantity sortable" data-sort="int"><?php esc_html_e('Qty', 'woocommerce'); ?></th>
            <?php
            if (!empty($order_taxes)) :
                foreach ($order_taxes as $tax_id => $tax_item) :
                    $tax_class      = wc_get_tax_class_by_tax_id($tax_item['rate_id']);
                    $tax_class_name = isset($classes_options[$tax_class]) ? $classes_options[$tax_class] : __('Tax', 'woocommerce');
                    $column_label   = !empty($tax_item['label']) ? $tax_item['label'] : __('Tax', 'woocommerce');
                    /* translators: %1$s: tax item name %2$s: tax class name  */
                    $column_tip = sprintf(esc_html__('%1$s (%2$s)', 'woocommerce'), $tax_item['name'], $tax_class_name);
            ?>
                    <th style='text-align: center;' class="line_tax tips" data-tip="<?php echo esc_attr($column_tip); ?>">
                        Btw (<?php echo esc_attr($column_label); ?>)
                        <input type="hidden" class="order-tax-id" name="order_taxes[<?php echo esc_attr($tax_id); ?>]" value="<?php echo esc_attr($tax_item['rate_id']); ?>">
                        <?php if ($order->is_editable()) : ?>
                            <a class="delete-order-tax" href="#" data-rate_id="<?php echo esc_attr($tax_id); ?>"></a>
                        <?php endif; ?>
                    </th>
            <?php
                endforeach;
            endif;

            ?>
            <th style='text-align: center;' class="line_cost sortable" data-sort="float"><?php esc_html_e('Total', 'woocommerce'); ?></th>

        </tr>
    </thead>
    <tbody id="order_line_items">
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "courses";
        foreach ($line_items as $item_id => $item) {
            $getCourseID = $item->get_meta('info');
            $courseDate = '–';
            if (isset($getCourseID['course'])) {
                $course_id = (int) $getCourseID['course'];
                if ($course_id > 0) {
                    $rows = $wpdb->get_results("SELECT date from $table_name where id = $course_id");
                    $courseDate =  date('d-m-Y', $rows[0]->date);
                }
            }
            $css_class_service  = "";
            do_action('woocommerce_before_order_item_' . $item->get_type() . '_html', $item_id, $item, $order);
            $_product = $item->get_product();
            $item_optional_courses = wc_get_order_item_meta($item_id, 'optional_courses', true);
            $parent_item = null;
            $hideisoption = "true";
            $product_link = get_permalink($item->get_product_id());
            if (!isset($optional_courses[$_product->get_ID()])) {
                $product_link;
                $parent_item = $item;
                include EDUQ_COURSE_PLUGIN_VIEWS_BASE . '/html-order-item.php';
            }
            if (!empty($item_optional_courses)) {
                foreach ($item_optional_courses as $optional_item_id) {
                    $courseDate = "–";
                    $product_link = "";
                    $css_class_service  = "";
                    $datax = "service-item";
                    $item = $service_items[$optional_item_id]["item"];
                    $item_id = $service_items[$optional_item_id]["item_id"];
                    include EDUQ_COURSE_PLUGIN_VIEWS_BASE . '/html-order-item.php';
                }
            }


            do_action('woocommerce_order_item_' . $item->get_type() . '_html', $item_id, $item, $order);
        }
        do_action('woocommerce_admin_order_items_after_line_items', $order->get_id());

        ?>
        <tr>
            <td style="padding:12px; font-weight:bold">Besteltotaal</td>
            <td style="padding:12px;border-right: 0;"></td>
            <td style="padding:12px;border-left: 0;border-right: 0;"></td>
            <td style="padding:12px;border-left: 0;border-right: 0;"></td>
            <?php if (wc_tax_enabled()) : ?>
                <?php foreach ($order->get_tax_totals() as $code => $tax_total) : ?>
                    <td style="color: #f57d30; font-weight: bolder;padding:12px; text-align: right;" class="total">
                        <?php
                        // We use wc_round_tax_total here because tax may need to be round up or round down depending upon settings, whereas wc_price alone will always round it down.
                        echo wc_price(wc_round_tax_total($tax_total->amount), array('currency' => $order->get_currency())); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </td>
                <?php endforeach; ?>
            <?php endif; ?>
            <td style="color: #f57d30; font-weight: bolder;padding:12px; text-align: right;" class="total">
                <?php
                $string = wc_price($order->get_total(), array('currency' => $order->get_currency()));
                $clean_string = str_replace('.', '', $string);
                echo $clean_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>
            </td>

        </tr>


        <tr>
            <td style="padding:12px; font-weight:bold">Betaalmethode</td>
            <td style="padding:12px;border-right: 0;"></td>
            <td style="padding:12px;border-left: 0;border-right: 0;"></td>
            <td style="padding:12px;border-left: 0;border-right: 0;"></td>
            <td colspan="2" style="color: #f57d30; font-weight: bolder;padding:12px; text-align: center;" class="total">
                <?php echo $order->payment_method_title ?>
            </td>

        </tr>





    </tbody>
</table>

<!-- tottal -->

<?php
$arr = [];

foreach ($order->get_meta_data() as $key => $value) {
    if ($value->key == '_user_info') {
        foreach ($value->value as $sub_key => $sub_value) {
            $arr[$sub_key] = $sub_value;
        }
    }
}
?>

<table style="width: 800px; border:none;">
    <tr style="border:none;">
        <td style="border:none; border-top:15px;width: 200px;">
            <div id="contact-details" style="width:100%; margin-right: 35px;" class="contact-details">
                <h2 style="color:#f57d30;display:block;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:18px 0;text-align:left;margin-top:0px;margin-bottom:5px;">Contactgegevens</h2>
                <table style="width:100%;border: 1px solid #e5e5e5; color: #737973;  margin: 30px 0; margin-top:0;">
                    <tbody>
                        <tr>
                            <td width="50%" valign="top" style="text-align:left;border:0; padding:5px;" align="left">
                                <address style="color:#737973;">
                                    <?php if (isset($arr['company_name'])) {
                                        echo $arr['company_name'];
                                    ?>
                                        <br><?php }
                                        echo $arr['_first_name'] . " " . $arr['_last_name'];
                                            ?>
                                    <br>
                                    <a href="tel:<?php echo "+31" . $order->get_billing_phone(); ?>"><?php echo "+31" . $order->get_billing_phone(); ?></a>
                                    <br>
                                    <a style="color: #15c;" href="mailto:<?php echo $arr['_email_adress']; ?>" target="_blank">
                                        <?php
                                        echo $arr['_email_adress'];
                                        ?>
                                    </a>
                                    <font color="#888888"><br><br></font>
                                </address>
                                <font color="#888888"></font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </td>
        <td style="border:none; border-top:15px;width: 200px;">
            <div id="contact-details" style="width:100%; margin-right: 35px;" class="contact-details">
                <h2 style="color:#f57d30;display:block;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:18px 0;text-align:left; margin-top:0px;margin-bottom:5px;">Factuuradres</h2>
                <table style="border: 1px solid #e5e5e5;width:100%; border-bottom: 1px solid #e5e5e5; color: #737973;  margin: 30px 0; margin-top:0;">
                    <tbody>
                        <tr>
                            <td width="50%" valign="top" style="text-align:left;border:0;padding:5px;" align="left">
                                <address style="color:#737973;">
                                    <?php
                                    echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                                    ?>
                                    <br>
                                    <?php
                                    echo $order->get_billing_address_1();
                                    ?>
                                    <br>
                                    <?php
                                    echo $order->get_billing_postcode() . ' ' . $order->get_billing_city();
                                    ?>
                                    <br>
                                    <a href="tel:<?php echo "+31" . $order->get_billing_phone(); ?>"><?php echo "+31" . $order->get_billing_phone(); ?></a>
                                    <br>
                                    <a style="color: #15c;" href="mailto:<?php echo $order->get_billing_email() ?>" target="_blank">
                                        <?php
                                        echo $order->get_billing_email();
                                        ?>
                                    </a>
                                </address>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>
<?php
/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */


// do_action('woocommerce_email_order_meta2', $order, $sent_to_admin, $plain_text, $email);
/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
// do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */


/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);
