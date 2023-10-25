<?php


include_once(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_config.php');
include_once(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . '_messages.php');
include_once(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . 'render_error_message.php');
include_once(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . 'course_information.php');
include_once(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . 'course_sidebar.php');
include_once(EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . 'student_information.php');
include_once(EDUQ_COURSE_PLUGIN_WIZARD_BASE . 'course_selection.php');
include_once(EDUQ_COURSE_PLUGIN_FRONTEND_BASE . 'course_detail.php');
include_once(EDUQ_COURSE_PLUGIN_FRONTEND_BASE . 'buy_course.php');
include_once(EDUQ_COURSE_PLUGIN_INCLUDES_BASE . 'class-edu-wc-admin-meta-box-order-data.php');
include_once(EDUQ_COURSE_PLUGIN_INCLUDES_BASE . 'class-edu-meta-boxes-order-items.php');
include_once(EDUQ_COURSE_PLUGIN_INCLUDES_BASE . 'class-edu-wc-admin-meta-boxes.php');
include_once(EDUQ_COURSE_PLUGIN_INCLUDES_BASE . '_vca_training_metabox.php');


function getCourseDetail($courses, $id, $product)
{
    $latest_course = $courses[count($courses) - 1];

    $content = '<input type="hidden" id="product_id" value="' . $id . '" /> ';
    if (date("Y-m-d", $latest_course->date) >= date("Y-m-d")) {

        $content .= '<input type="hidden" id="course_availability" value="1" /> ';
    } else {
        $content .= '<input type="hidden" id="course_availability" value="0" /> ';
    }
    return $content;
}

function getTariff($product)
{
    $regex = "/\(exclusief btw, inclusief lesmateriaal,.*\)/";
    $description = $product->get_description();
    preg_match($regex, $description, $matches);
    if (!empty($matches)) {
        $tariff = explode(",", str_replace(["(", ")"], ["", ""], $matches[0]));
        $count = count($tariff);
        return $tariff[$count - 2] . "," . $tariff[$count - 1];
    }
    return "";
}

function get_events($courses)
{

    $data = [];
    $current_date = date("Y-m-d");
    $cart_items = getCartCourses();
    // print_r($course->course);
    // die;
    foreach ($courses as $k => $course) {
        // getID = $course->course
        if (isset($cart_items[$course->course])) {
            $cart_course =  "&cart_course=1";
        } else {
            $cart_course =  "&cart_course=0";
        }
        $eduq_location = get_post_meta($course->course, "_eduq_location");
        $eduq_Cursusduur = get_post_meta($course->course, "_eduq_Cursusduur");
        $eduq_Inclusief = get_post_meta($course->course, "_eduq_Inclusief");
        $course_date = date("Y-m-d", $course->date);
        $location = $eduq_location[0];
        $inclusive = $eduq_Inclusief[0];
        $duration = $eduq_Cursusduur[0];
        $isFullyBooked = $course->isFullyBooked ?? '0';
        $isFullyBookedhtml = isFullyBookedhtml();
        if ($course_date > $current_date) {
            $data[$course_date] = [
                "startDate" => $course_date,
                "endDate" => $course_date,
                "location" => $location,
                "summary" => $location,
                "event_id" => $course->id,
                "inclusive" => $inclusive,
                "duration" => $duration,
                "isFullyBooked" => $isFullyBooked,
                "isFullyBookedhtml" => $isFullyBookedhtml,
                "redirect" => get_permalink(CHECKOUT_PAGE_ID) . "?process=buy&course_id=" . $course->course . "&event_id=" . $course->id . $cart_course,
                "course_id" => $course->course
            ];
        }
    }
    return json_encode($data);
}

function render_courses_events_json($eduq_events)
{
    //    echo "<pre>";
    //    print_r($eduq_events);
    //    die;
?>
    <script type="text/javascript">
        var courses_events = '<?php echo $eduq_events; ?>';
        var options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };

        function getCoursesEvents() {
            let events = JSON.parse(courses_events);
            let formated_events = [];
            for (i in events) {
                var event = events[i];
                var event_date = new Date(event.startDate).toLocaleDateString("en-US", options);
                let course_desc = event.summary ? [event.startDate, event.summary].join(' ') : "";
                model = {
                    startDate: event_date,
                    endDate: event_date,
                    location: event.location,
                    isFullyBooked: event.isFullyBooked,
                    inclusive: event.inclusive,
                    duration: event.duration,
                    summary: "<input type='checkbox' id='course' name='course' value='" + course_desc + "'  /> " + events[i].summary,
                    event_id: events[i].event_id,
                    course_id: events[i].course_id,
                }
                formated_events.push(model);
            }
            return formated_events;
        }
    </script><?php
            }

            function renderCartPageBreadCrumb($product)
            {

                return renderCartStepBar() . '<ul class="breadcrumb">
        <li><a href="/">Home</a></li>
        <li><a href="' . get_permalink($product->get_id()) . '">' . $product->get_title() . '</a></li>
        <li class="active">Besteloverzicht</li>
        </ul>';
            }

            function renderCheckoutPageBreadCrumb($product)
            {
                $url = wc_get_cart_url() . "?course_id=" . $product->get_id();
                return renderPaymentStepBar() . '<div class="col-lg-8"></div><ul class="breadcrumb">
        <li><a href="/">Home</a></li>
        <li><a href="' . get_permalink($product->get_id()) . '">' . $product->get_title() . '</a></li>
        <li><a href="' . $url . '">Besteloverzicht</a></li>
        <li class="active">Factuurgegevens</li>
        </ul></div>';
            }

            function initalizeStepBar()
            {
                return '<div class="col-xs-3 stepText1">Cursus</div>
          <div class="col-xs-3 stepText2">Besteloverzicht</div>
          <div class="col-xs-3 stepText3">Factuurgegevens</div>
          <div class="col-xs-3 stepText4">Afrekenen</div>';
            }

            function renderCartStepBar()
            {
                return
                    '<div id="step_bar" class="cart_step_bar hidden">
            <div class="row line">
                      ' . initalizeStepBar() . '
            </div>
            <div class="row">
              <div class="col-xs-3 step-col-1"><div class="step step1 active"></div></div>
              <div class="col-xs-3 step-col-2"><div class="step step2 active"></div></div>
              <div class="col-xs-3 step-col-3"><div class="step step3"></div></div>  
               <div class="col-xs-3 step-col-4"><div class="step step4"></div></div>  
           </div>
    </div>';
            }


            function renderPaymentStepBar()
            {
                return
                    '<div id="step_bar">
            <div class="row line">
                ' . initalizeStepBar() . '
            </div>
            <div class="row">
              <div class="col-xs-3 step-col-1"><div class="step step1 active"></div></div>
              <div class="col-xs-3 step-col-2"><div class="step step2 active"></div></div>
              <div class="col-xs-3 step-col-3"><div class="step step3 active"></div></div>  
               <div class="col-xs-3 step-col-4"><div class="step step4"></div></div>  
           </div>
    </div>';
            }

            function renderReceiptStepBar()
            {
                return
                    '<div id="step_bar">
            <div class="row line">
                     ' . initalizeStepBar() . '
            </div>
            <div class="row">
              <div class="col-xs-3 step-col-1"><div class="step step1 active"></div></div>
              <div class="col-xs-3 step-col-2"><div class="step step2 active"></div></div>
              <div class="col-xs-3 step-col-3"><div class="step step3 active"></div></div>  
               <div class="col-xs-3 step-col-4"><div class="step step4 active"></div></div>  
           </div>
    </div>';
            }

            /***
             * @param $cart_key
             * @return array|mixed
             */
            function getEduNlCartInfo($cart_key)
            {
                $data = [];
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    if ($cart_key == $cart_item_key) {
                        $data = $cart_item;
                        break;
                    }
                }
                return $data;
            }
            function isFullyBookedhtml()
            {
                define('BASE_PATH', getcwd());

                ob_start();
                // include ACF_GOOGLE_MAP_PLUGIN_DIR."/_map.php";

                // include BASE_PATH . "//wp-content/plugins/courses/fullyBooked.php";
                include EDUQ_COURSE_PLUGIN_CHECKOUT_BASE . "_fullyBooked.php";
                return ob_get_clean();
                wp_die();
            }


            $edu_admin_template = new EDU_WC_Admin_Meta_Boxes();
                ?>