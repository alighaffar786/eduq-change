<?php

/**
 * @param $product
 * @param $optional_courses
 * @param $cart_courses
 * @param $no_of_students
 * @return string
 */


function getChangedItemInCartSummary($product, $optional_courses, $cart_courses, $no_of_students): string
{
    $model = [
        'title' => !empty($product) ? $product->get_title() : "",
        'cart_price' => !empty($product) ? WC()->cart->get_product_price($product) : 0,
        "cart_total" => !empty($product) ? WC()->cart->get_product_price($product) : 0,
        "extra_price" => 0
    ];
    $product_id = !empty($product) ? $product->get_ID() : "";
    $optional_courses_html = '';
    if (!empty($product)) {
        $optional_courses_html = renderOptionalCouresesSideBar($product, $optional_courses, $cart_courses, $no_of_students, false);
    }
    $cart_product_price_tag = $model["cart_price"];
    $replace_str = '<span class="woocommerce-Price-currencySymbol">€</span>';

    if (!empty($product)) {
        $cart_product_price = utf8_encode(str_replace(["€", ","], ["", "."], strip_tags($cart_product_price_tag)));
    } else {
        $cart_product_price = "";
    }


    $_tax = new WC_Tax();

    if (!empty($product) && $product->is_taxable()) {
        $taxes = $_tax->get_rates($product->get_tax_class());
        $rate = array_shift($taxes);
    } else {
        $taxes = $_tax->get_rates($product->get_tax_class());
        $rate = array_shift($taxes);
        // $rate = ['rate' => 0, 'label' => '0%', 'shipping' => 'yes', 'compound' => 'no'];
    }
    $tax_label = ' <small class="tax_label">(incl. ' . $rate["label"] . ' btw)</small>';

    $course_quantity = !empty($product) ? '<strong class="product-quantity">&nbsp;×' . $no_of_students . '</strong>' : "";
    $step1 = '
      
            <div parent_id="' . $product_id . '"  class="row extra_price_row extra_price_current">
                <div class="col-lg-11 col-md-12">
                    <div class="col-md-6 course_label" id="course_title" >
                     <strong>' . $model["title"] . ' ' . $course_quantity . ' </strong></div>
                    <div class="col-md-6 text-right data-price-calculate">
                    <span id="cart_price" class="product_price" data-price="' . $cart_product_price . '" data-tax-rate="' . $rate["rate"] . '">' . $cart_product_price_tag . '</span>' . $tax_label . '</div>
                </div>
            </div>
            <div parent_id="' . $product_id . '">
                <div class="col-lg-11">
                    <hr/>
                </div>
            </div>' . $optional_courses_html . '
            <div class="extra_price_row row hidden">
                <div class="col-lg-11">
                    <div class="col-md-6" id="cart_extra_price_label"></div>
                    <div class="col-md-6 text-right data-price-calculate" ><span id="cart_extra_price">' . $model["extra_price"] . '</span></div>
                </div>
            </div>
             <div class="extra_price_row row hidden">
                <dv class="col-lg-11">
                    <hr/>
                </dv>
     
          
        </div>';
    return $step1;
}

/**
 * @param $product
 * @param $optional_courses
 * @param $cart_courses
 * @param $no_of_students
 * @param $current_course
 * @return string
 */
function renderSideBar($product, $optional_courses, $cart_courses, $no_of_students = 1, $previous_course = 0)
{


    $model = [
        'title' => !empty($product) ? $product->get_title() : "",
        'cart_price' => !empty($product) ? WC()->cart->get_product_price($product) : 0,
        "cart_total" => WC()->cart->get_total(),
        //  "cart_tax" => WC()->cart->get_tax_totals(),
        "extra_price" => 0
    ];
    //    $total_tax = floatval(preg_replace('#[^\d.]#', '', WC()->cart->get_cart_total())) - WC()->cart->get_total_ex_tax();

    $cart_product_price_tag = $model["cart_price"];
    $replace_str = '<span class="woocommerce-Price-currencySymbol">€</span>';

    if (!empty($product)) {
        $cart_product_price = utf8_encode(str_replace(["€", ","], ["", "."], strip_tags($cart_product_price_tag)));
    } else {
        $cart_product_price = "";
    }


    $_tax = new WC_Tax();

    if (!empty($product) && $product->is_taxable()) {
        $taxes = $_tax->get_rates($product->get_tax_class());
        $rate = array_shift($taxes);
    } else {
        $rate = ['rate' => 0, 'label' => '0%', 'shipping' => 'yes', 'compound' => 'no'];
    }
    $tax_label = ' <small class="tax_label">(incl. ' . $rate["label"] . ' btw)</small>';

    $course_quantity = !empty($product) ? '<strong class="product-quantity">&nbsp;×' . $no_of_students . '</strong>' : "";
    $attributes = [$model, $course_quantity, $cart_product_price, $_tax, $rate, $cart_product_price_tag, $tax_label, $product, $previous_course];
    $cart_items_html = renderCartItems($attributes, $optional_courses, $cart_courses);

    $changed_item_html = !empty($product) ? getChangedItemInCartSummary($product, $optional_courses, $cart_courses, $no_of_students) : "";

    $step = '
        <input type="hidden" name="extra_price" id="extra_price" value="0" />
            ' . $cart_items_html . '
            ' . $changed_item_html . '

            <div class="extra_price_row row hidden">
                <div class="col-lg-11">
                    <div class="col-md-6" id="cart_extra_price_label"></div>
                    <div class="col-md-6 text-right data-price-calculate" ><span id="cart_extra_price">' . $model["extra_price"] . '</span></div>
                </div>
            </div>
             <div class="extra_price_row row hidden">
                <div class="col-lg-11">
                    <hr/>
                </div>
            </div>
             <div class="row">
                <div class="col-lg-11">
                    <div class="col-md-6 total_label"> <strong>' . translate('Total', 'woocommerce') . '</strong> </div>
                    <div class="col-md-6 text-right data-price-total" >
                        <span id="cart_total">' . $model["cart_total"] . '</span>
                        <span id="cart_tax"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-11">
                    <hr/>
                </div>
            </div>
            <div class="no_event_hide row sidebar_btn_row">
                <div class="col-lg-4">
                    <button class="btn"  type="submit">' . COURSE_DETAIL_LBL['submit_btn'] . '</button>
                </div>
        </div>';
    return $step;
}

/**
 * @param $attributes
 * @param $optional_courses
 * @param $cart_courses
 * @return string
 */
function renderCartItems($attributes, $optional_courses, $cart_courses)
{


    list($model, $course_quantity, $cart_product_price, $_tax, $rate, $cart_product_price_tag, $tax_label, $currentCourse, $previous_course) = $attributes;
    $cart_items = WC()->cart->get_cart();
    list($cart_items, $service_cart_items) = get_services_from_cart($cart_items);
    $html = '<div class="row">
                <div class="col-lg-11 sidebar-title">
                    <h5><b>' . COURSE_DETAIL_LBL['SUMMARY'] . '</b></h5>
                    <hr/>
                </div>
            </div>';
    foreach ($cart_items as $key => $cart_item) {
        $_product = $cart_item["_product"];
        if (!empty($currentCourse) && $currentCourse->get_id() == $_product->get_id()) {
            continue;
        } elseif ($previous_course == $_product->get_id()) {
             continue;
        }
        $_tax = new WC_Tax();
        $model["title"] = $_product->get_title();
        $product_id = $_product->get_id();
        $course_quantity = !empty($_product) ? '<strong class="product-quantity">&nbsp×' . $cart_item['quantity'] . '</strong>' : "";

        //        $course_quantity = $cart_item['quantity'];
        $replace_str = '<small class="tax_label">(incl. btw)</small>';
        $cart_item_price = WC()->cart->get_product_subtotal($_product, $cart_item['quantity']);
        $cart_item_single_price = WC()->cart->get_product_subtotal($_product, 1);

        $cart_product_price_tag = str_replace($replace_str, '', $cart_item_price);
        $cart_product_price = utf8_encode(str_replace(["€", ","], ["", "."], strip_tags($cart_product_price_tag)));


        $cart_product_single_price_tag = str_replace($replace_str, '', $cart_item_single_price);
        $cart_product_single_price = utf8_encode(str_replace(["€", ","], ["", "."], strip_tags($cart_product_single_price_tag)));

        $optional_courses_html = '';
        if (!empty($_product)) {
            $optional_courses_html = renderOptionalCouresesSideBar($_product, $optional_courses, $cart_courses, $cart_item['quantity'], true);
        }

        $taxes = $_tax->get_rates($_product->get_tax_class());
        $rate = array_shift($taxes);
        $tax_label = ' <small class="tax_label">(incl. ' . $rate["label"] . ' btw)</small>';

        if (!$cart_item['is_optional']) {
            $html .= '
            
            <div  data-cart-price="' . $cart_product_price . '" class="row extra_price_row extra_price_row_' . $product_id . '" data-id="' . $product_id . '">
                <input type="hidden" class="cart_quantity" id="cart_quantity_' . $product_id . '" value="' . $cart_item['quantity'] . '" />
                <div class="col-lg-11 col-md-12">
                    <div class="col-md-6 course_label" id="course_title"> <strong>' . $model["title"] . $course_quantity . ' </strong></div>
                    <div class="col-md-6 text-right data-price-calculate"><span id="cart_price" class="product_price" data-cart-quantity = "' . $cart_item['quantity'] . '" data-price="' . $cart_product_single_price . '" data-tax-rate="' . $rate["rate"] . '">' . $cart_product_price_tag . '</span>' . $tax_label . '</div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-11">
                    <hr/>
                </div>
            </div>' . $optional_courses_html;
        }
    }
    return $html;
}

/**
 * @param $product
 * @param $optional_courses
 * @param $cart_courses
 * @param $no_of_students
 * @param $is_cart_item
 * @return string
 */
function renderOptionalCouresesSideBar($product, $optional_courses, $cart_courses, $no_of_students = 1, $is_cart_item = false)
{
    $output = '';
    $product_id = $product->get_ID();


    if (empty(COURSES_SELECTION_CRITERIA[$product_id])) {
        return;
    }
    $_config = array_keys(COURSES_SELECTION_CRITERIA[$product_id]);
    // && (isset($cart_courses[$course_id]))
    $_tax = new WC_Tax();


    foreach ($optional_courses as $course) {

        $course_id = $course->get_ID();
        $course_name = $course->get_name();
        $course_quantity = '<strong class="product-quantity">×' . $no_of_students . '</strong>';
        if ($note = $course->get_purchase_note()) {
            $course_name = $note;
        }
        $css_class = "hidden";
        if (isset($cart_courses[$product_id]["optional_courses"][$course_id])) {
            $css_class = "";
        }
        if (isset(COURSES_SELECTION_CRITERIA[$product_id]) && is_array(COURSES_SELECTION_CRITERIA[$product_id])) {
            if (
                in_array($course->slug, $_config)
            ) {
                $data_price = wc_price($course->price);
                $_product_sub_total = wc_price($course->price);

                if ($course->is_taxable() && $course->price > 0) {
                    $data_price = wc_get_price_including_tax($course);
                    $row_price = wc_get_price_including_tax($course, ['qty' => $no_of_students]);
                    $_product_sub_total = wc_price($row_price);
                    $taxes = $_tax->get_rates($course->get_tax_class());
                    $rate = array_shift($taxes);
                } elseif (!$course->is_taxable() && $course->price > 0) {
                    $data_price = wc_get_price_including_tax($course);
                    $row_price = wc_get_price_including_tax($course, ['qty' => $no_of_students]);
                    $_product_sub_total = wc_price($row_price);
                    $rate = ['rate' => 0, 'label' => '0%', 'shipping' => 'yes', 'compound' => 'no'];
                } else {
                    $rate = ['rate' => 0, 'label' => '0%', 'shipping' => 'yes', 'compound' => 'no'];
                }
                //                echo "<pre>";
                //                print_r($data_price);
                //                echo("product price");
                //                echo $_product_sub_total;
                //                echo "</br>";

                $CurrencySymbol = "€";
                $cart_product_price = $CurrencySymbol . $data_price;

                $css_side_class_price = "";
                $css_side_class_rate = "";
                if ($course->price == 0) {
                    $cart_product_price = utf8_encode(str_replace(["€", ","], ["", "."], strip_tags($data_price)));
                    $css_side_class_price = "hidden";
                    $css_side_class_rate = "hidden";
                    $course_quantity = "";
                }
                if ($rate["rate"] == 0) {
                    $css_side_class_rate = "hidden";
                }


                $cart_product_price_tag = $_product_sub_total;

                $cart_product_price_tag = str_replace("<bdi>", "<bdi id='bdi_$product_id' class='bdi_$product_id'>", $cart_product_price_tag);
                $replace_str = '<span class="woocommerce-Price-currencySymbol">€</span>';

                $cart_quantity = $is_cart_item ? $no_of_students : 0;

                //  $cart_product_price = utf8_encode(str_replace(["€", ","], ["", "."], strip_tags($_product_sub_total)));

                //                echo "<pre>";
                //                print_r($cart_product_price1);
                //                echo("product price with tags");
                //                echo $cart_product_price;

                $tax_label = ' <small class="tax_label ' . $css_side_class_rate . '">(incl. ' . $rate["label"] . ' ' . 'btw)</small>';
                $element_id = "sidebar_" . $course->slug . "_" . $product_id;
                $output .= '
                    <div parent_id="' . $product_id . '" data-course-id="' . $course_id . '" class="extra_price_row  optional_row extra_price_row_' . $course->slug . ' ' . $css_class . '" id="' . $element_id . '">
                        <div class="row">
                            <div class="col-lg-11">
                                <div class="col-md-6 course_label optional_courses " id="cart_extra_price_label">' . '<strong></strong>' . $course_name . ' ' . $course_quantity . '</div>
                                <div class="col-md-6 text-right data-price-calculate"  child_id="' . $course_id . '"  >
                                        <span id="cart_extra_price" data-course-id="' . $course_id . '"  class="product_price ' . $css_side_class_price . '" data-cart-quantity = "' . $cart_quantity . '" data-price="' . $cart_product_price . '" data-tax-rate="' . $rate["rate"] . '">' . $cart_product_price_tag . '</span>'
                    . $tax_label .
                    '</div>
                            </div>
                        </div>
                        <div>
                            <div class="col-lg-11">
                                <hr/>
                            </div>
                        </div>  
                    </div>
                ';
            }
        }
    }

    //    $output = '';
    return $output;
}
