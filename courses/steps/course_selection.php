<?php

function render_buy_process($course_id, $event_id, $cart_item = [], $cart_key = "")
{

    $action = isset($_GET["action"]) ? $_GET["action"] : "";
    if (is_admin() && $action == "edit") {
        return "";
    }

    $cart_courses = getCartCourses();
    $all_courses = getAllCourses($course_id, $cart_courses);
    $eduq_course = [];

    $product = [];

    if ($course_id > 0) {
        $eduq_course = getCourses($course_id);
        $product = wc_get_product($course_id);
        $event = fetchCurrentEvent($eduq_course, $event_id);
        $current_date = date("Y-m-d", $event->date);
    } else {

        $event = [];
        $current_date = date("Y-m-d");
    }
    $cart_url = wc_get_cart_url() . "?course_id=" . $course_id . "&event_id=" . $event_id . "&cart_item_key=" . $cart_key;


    $form_action = get_permalink(CHECKOUT_PAGE_ID) . "?process=buy&course_id=" . $course_id . "&event_id=" . $event_id . "&cart=1" . "&cart_item_key=" . $cart_key;
    $bread_crumbs = renderCourseEventJson($eduq_course) . renderCheckOutBreadCrumb($product);

    $args = [
        "post_type" => "product",
        "post_name__in" => OPTIONAL_COURSES,
        'orderby' => 'ID',
        'order' => 'ASC',
    ];
    $optional_courses = wc_get_products($args);

    list($_, $no_of_students) = getFormCursist(true, $cart_item);

    //$cartinfo = cartinfo();
    $sidebar = renderSideBar($product, $optional_courses, $cart_courses, $no_of_students);

    $is_mobile = false;
    $useragent = $_SERVER['HTTP_USER_AGENT'];

    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
        $is_mobile = true;
    }

?>
    <?php
    $contact_page = get_permalink(get_page_by_path('contact'));
    if (!$is_mobile) {


        $step = $bread_crumbs . '
        <!-- Button trigger modal -->
        
        <div class="row hidden" id="form_error">
            <div class="col-lg-7">
                ' . renderBuyErrorMessage() . '
              
            </div>
        </div>
        <form name="form_subscription" id="form_subscription" method="POST" action="' . $form_action . '">
            <input type="hidden" id="cart_url" value="' . $cart_url . '" />
            <input type="hidden" id="cart_item_key" value="' . $cart_key . '" />
            <input type="hidden" name="noCursists" id="noCursists" value="1" />
            <input type="hidden" name="previous_course" id="previous_course" value="' . $course_id . '" />
            <input type="hidden" name="courseName" id="courseName" value="" />
            <div class="row">

              <div class="col-lg-7">
                <div class="panel panel-default ">
                  <div class="panel-heading">CURSUSOPTIES</div>
                  <div class="panel-body">
                    <div class="form-group">
                        <label class="control-label" for="course_lb_select">' . COURSE_DETAIL_LBL['course_lb_select'] . ' <span>*</span></label>
                        <select class="form-control" name="courseid" id="courseid">
                            <optgroup label= "' . MOST_CHOSEN . '"  > 
                            ' . prepareCourseSelectList($all_courses['featureCourses'], $course_id) . '
                            </optgroup>
                            <optgroup label="' . OTHER . '">
                            ' . prepareCourseSelectList($all_courses['alphabeticalCourses'], $course_id) . '
                            </optgroup>
                        </select>
                    </div>
                    <div id="optional_course">
                        ' . renderOptionalCourses($course_id, $optional_courses, $cart_courses, $cart_item) . '
                    </div> 
                  </div>
                </div>
                ' . renderCourseInfo($event, $eduq_course, $event_id, $current_date) . renderStudentInfo($cart_item) . '

              </div><!-- /.col-lg-7 -->
              <div id="course_sidebar" class="col-lg-5 course_sidebar ">
                 ' . $sidebar . '
               </div>
            </div><!-- /.row -->
            <div class="clear"></div>
           
            
            <div class="row "> 
                 <div class="col-lg-1"></div>
                <div class="col-lg-7 text-right ">
                    <div class="vc_column_container vc_col-sm-12 submit_btn_for_mobile"><input type="submit" class="btn btn-primary btn-primary-two"  id="submit_subscription" value="' . COURSE_DETAIL_LBL['submit_btn'] . '"></input></div>

                </div>
            </div>
        </form>
        
';
    } else {
        $step = $bread_crumbs . '
        <!-- Button trigger modal -->
        
        <div class="row hidden" id="form_error">
            <div class="col-lg-1"></div>
            <div class="col-lg-7">
                ' . renderBuyErrorMessage() . '
              
            </div>
        </div>
        <form name="form_subscription" id="form_subscription" method="POST" action="' . $form_action . '">
            <input type="hidden" id="cart_url" value="' . $cart_url . '" />
            <input type="hidden" id="cart_item_key" value="' . $cart_key . '" />
            <input type="hidden" name="previous_course" id="previous_course" value="' . $course_id . '" />
            <input type="hidden" name="courseName" id="courseName" value="" />
            <input type="hidden" name="noCursists" id="noCursists" value="1" />
            <div class="row">
              <div class="col-lg-1"></div>
              <div class="col-lg-7">
                <div class="panel panel-default ">
                  <div class="panel-heading">CURSUSOPTIES</div>
                  <div class="panel-body">
                    <div class="form-group">
                        <label class="control-label" for="course_lb_select">' . COURSE_DETAIL_LBL['course_lb_select'] . ' <span>*</span></label>
                        <select class="form-control" name="courseid" id="courseid">
                        <optgroup label="' . MOST_CHOSEN . '"> 
                        ' . prepareCourseSelectList($all_courses['featureCourses'], $course_id) . '
                        </optgroup>
                        <optgroup label="' . OTHER . '">
                        ' . prepareCourseSelectList($all_courses['alphabeticalCourses'], $course_id) . '
                        </optgroup>
                        </select>
                    </div>
                    <div id="optional_course">
                        ' . renderOptionalCourses($course_id, $optional_courses, $cart_courses, $cart_item) . '
                    </div> 
                  </div>
                </div>
                <label id="no_event" clas="smallsacreen-error">
                       Er zijn momenteel geen data bekend voor deze opleiding. </br> Neem 
                        <a id="redirect_btn" href="' . $contact_page . '" >contact</a> met ons op voor de mogelijkheden.
                    </label>
                   </div><!-- /.col-lg-7 -->
              <div id="course_sidebar" class="col-lg-4 course_sidebar ">
                 ' . $sidebar . '
               </div>
            </div><!-- /.row -->
            <div class="clear"></div>
            
                ' . renderCourseInfo($event, $eduq_course, $event_id, $current_date) . renderStudentInfo($cart_item) . '
            <div class="row "> 
                 
                <div class="col-lg-12 text-right submit_btn_for_mobile ">
                    <input type="submit" class="btn btn-primary btn-primary-two"  id="submit_subscription" value="' . COURSE_DETAIL_LBL['submit_btn'] . '" />

                </div>
            </div>
        </form>
        
';
    }
    return $step;
}


/**
 * Courses would be displayed in dropdown
 * @param $course_id
 * @param $cart_courses
 * @return array
 */


function getAllCourses($course_id, $cart_courses = [])
{
    //getting optional courses ids to remove from list of products
    $optional_courses = getOptionalCoursesFromWp();
    $optional_courses_ids = array_keys($optional_courses);

    $args = array('posts_per_page' => -1, 'exclude' => $optional_courses_ids, 'orderby' => 'name', 'order' => 'ASC');
    $posts = wc_get_products($args);
    $ids = [];
    $list = [];
    // this will make sure the current course if in the cart should be available
    unset($cart_courses[$course_id]);

    foreach ($posts as $post) {

        $product_id = $post->get_id();
        if (!isset($cart_courses[$product_id])) {
            $price = WC()->cart->get_product_price($post);
            $list[$product_id] = ["name" => $post->get_title(), "price" => $price, "has_event" => 0];
            $ids[$product_id] = $product_id;
        }
    }

    $related_events = getCourses($ids); //events

    $filtered_group_list = filterCoursesNotHavePassed($list, $related_events, $cart_courses);

    return $filtered_group_list;
}

function filterCoursesNotHavePassed($list, $related_events, $cart_courses)
{
    $featuredCourses = [70 => 70, 426 => 426, 445 => 445, 447 => 447, 458 => 458];
    //filter the above courses if any one in the cart don't show

    $filtered_list = [];
    foreach ($list as $id => $course) {
        foreach ($related_events as $event) {
            $calendar_date = date("Y-m-d", $event->date);
            if ($event->course == $id) {
                if ($calendar_date >= date("Y-m-d")) {
                    $list[$id]['has_event'] = 1;
                } else {
                    $list[$id]['has_event'] = 0;
                }
            }

            if (isset($featuredCourses[$id])) {
                $filtered_list['featureCourses'][$id] = $list[$id];
            } else {
                $filtered_list['alphabeticalCourses'][$id] = $list[$id];
            }
        }
    }

    $alpha_keys = array_keys($filtered_list['alphabeticalCourses']);
    array_multisort(
        array_column($filtered_list['alphabeticalCourses'], 'name'),
        $filtered_list['alphabeticalCourses'],
        $alpha_keys
    );
    $filtered_list['alphabeticalCourses'] = array_combine($alpha_keys, $filtered_list['alphabeticalCourses']);


    $keys = array_keys($filtered_list['featureCourses']);
    array_multisort(
        array_column($filtered_list['featureCourses'], 'name'),
        $filtered_list['featureCourses'],
        $keys
    );
    $filtered_list['featureCourses'] = array_combine($keys, $filtered_list['featureCourses']);
    //    echo '<pre>';
    //    print_r($filtered_list['alphabeticalCourses']);
    //    echo "</pre>";
    //    die;
    foreach ($featuredCourses as $key) {
        if (isset($filtered_list['featureCourses'][$key])) {
            $orderedArray[$key] = $filtered_list['featureCourses'][$key];
        }
    }
    $filtered_list['featureCourses'] = $orderedArray;
    return $filtered_list;
}


function prepareCourseSelectList($courses, $course_id)
{
    //    echo "<pre>";
    //    print_r($courses);
    $list = "";
    if ($course_id == 0) {
        $list = "<option value='' selected=''>--" . COURSE_DETAIL_LBL['select_first_option'] . "--</option>";
    }
    foreach ($courses as $id => $course) {
        $name = $course["name"];
        $price = $course["price"];
        $has_event = $course["has_event"];

        if ($id == $course_id) {
            $list .= "<option value='" . $id . "' selected='selected' data-title='" . $name . "' data-price='" . $price . "' data-has-event='" . $has_event . "'>" . $name . "</option>";
        } else {
            $list .= "<option value='" . $id . "' data-title='" . $name . "' data-price='" . $price . "' data-has-event='" . $has_event . "'>" . $name . "</option>";
        }
    }
    return $list;
}

function prepareEventSelectList($events, $event_id)
{

    $list = "=";
    if (!empty($events)) {
        $list = "<option value='' selected=''>--Select--</option>";
    } else {
        $list = "<option value='' data-value='' selected=''>--Select--</option>";
    }

    foreach ($events as $event) {

        $courseFullyBookedMsg = '<div class ="fullyBooked" style="color:red">' . FULLY_BOOKED_COURSE_MESSAGE . '</div>';
        $isFullyBooked = $event->isFullyBooked;

        $event_date = getDutchMonth(date("l, j F Y", $event->date));
        $calendar_date = date("Y-m-d", $event->date);
        $bookedClass = '';
        if ($calendar_date >= date("Y-m-d")) {
            if ($isFullyBooked == 1) {
                $bookedClass = "to_show";
                $event_date = getDutchMonth(date("l, j F Y", $event->date));
                $event_date .= $courseFullyBookedMsg;
            }
            $location = !empty($event) ? explode("-", $event->location)[1] : "";
            $eduq_location = get_post_meta($event->course, "_eduq_location");
            if ($event->id == $event_id) {
                $list .= "<option  class = '" . $bookedClass . "' data-location='" . $eduq_location[0] . "' value='" . $event->id . "' selected='selected' data-date='" . $calendar_date . "' data-isFullyBooked='" . $isFullyBooked . "'>" . $event_date . "</option>";
            } else {
                $list .= "<option class = '" . $bookedClass . "'   data-location='" . $eduq_location[0] . "' value='" . $event->id . "' data-date='" . $calendar_date . "' data-isFullyBooked='" . $isFullyBooked . "'>" . $event_date . "</option>";
            }
        }
    }

    return $list;
}

function renderCourseEventJson($events)
{
    $events_json_by_date = [];
    $events_json_by_id = [];
    $events_json_by_date_fullybooked = [];
    if (!empty($events)) {

        foreach ($events as $event) {
            $event_date = date("Y-m-d", $event->date);
            $events_json_by_date[$event_date] = $event->id;
            $events_json_by_date_fullybooked[$event_date] = $event->isFullyBooked;
            $events_json_by_id[$event->id] = $event_date;
        }
    }
    $json_by_date_fullybooked = json_encode($events_json_by_date_fullybooked);
    $json_by_date = json_encode($events_json_by_date);
    $json_by_id = json_encode($events_json_by_id);
    $error_messages = json_encode(COURSE_ERROR_MESSAGES);
    $criteria_selection = json_encode(COURSES_SELECTION_CRITERIA);
    $course_criteria_checked = json_encode(COURSE_CHECKED);
    $course_students_lbl = json_encode(COURSE_STUDENTS_LBL);

    $s1 = "<script type='text/javascript'>var events_json_by_date = '" . $json_by_date . "'</script>";
    $s2 = "<script type='text/javascript'>var events_json_by_id = '" . $json_by_id . "'</script>";
    $s3 = "<script type='text/javascript'>var error_messages_json = JSON.parse('" . $error_messages . "')</script>";
    $s4 = "<script type='text/javascript'>var CURSIST_PHONE_LENGTH = " . CURSIST_PHONE_LENGTH . "</script>";
    $s5 = "<script type='text/javascript'>var COURSES_SELECTION_CRITERIA = " . $criteria_selection . "</script>";
    $s6 = "<script type='text/javascript'>var COURSE_STUDENTS_LBL = " . $course_students_lbl . "</script>";
    $s7 = "<script type='text/javascript'>var COURSE_CHECKED_CRITERIA = " . $course_criteria_checked . "</script>";

    $s8 = "<script type='text/javascript'>var CURSIST_PHONE_MIN_LENGTH = " . CURSIST_PHONE_MIN_LENGTH . "</script>";
    $s9 = "<script type='text/javascript'>var events_json_by_date_fullybooked = " . $json_by_date_fullybooked . "</script>";

    return $s1 . $s2 . $s3 . $s4 . $s5 . $s6 . $s7 . $s8 . $s9;
}

function fetchCurrentEvent($events, $event_id)
{
    $event = [];
    foreach ($events as $event) {
        if ($event->id == $event_id) {
            return $event;
        }
    }
    return false;
}

function renderEventLocation($event, $events)
{
    $event_checked = !empty($event) ? 'checked' : '';
    if (empty($event)) {
        $event = $events[0];
    }
    // global $post;
    $id =  $_GET['course_id'];
    // global $product;
    // $id = $product->get_id();
    // $post_id = $post->ID;
    // echo $id;
    $eduq_location = get_post_meta($id, "_eduq_location");
    $location = !empty($event) ? explode("-", $event->location)[1] : "";
    $html = '<div class="form-group">
                    <label for="location">Locatie</label>
                    <label class="radio-inline"><input type="hidden" id="location" name="location" value=' . $location . '/>
                        <span id="location_text">
                            ' . $eduq_location[0] . '
                        </span>
                        
                    </label>
                </div>';
    return $html;
}

function renderCheckOutBreadCrumb($product)
{

    if (!empty($product)) {
        return renderCheckoutStepBar() . renderCursesTitle() . '
        <ul class="breadcrumb">
            <li><a href="/">Home</a></li>
            <li><a href="' . get_permalink($product->get_id()) . '">' . $product->get_title() . '</a></li>
            <li class="active">Cursusselectie</li>
        </ul>';
    } else {
        return renderCheckoutStepBar() . '
        <ul class="breadcrumb">
            <li><a href="/">Home</a></li>
            <li class="active">Cursusselectie</li>
        </ul>';
    }
}

function renderCursesTitle()
{
    $empty_div = '<div class="vc_row wpb_row vc_row-fluid"><div class="wpb_column vc_column_container vc_col-sm-12"><div class="vc_column-inner"><div class="wpb_wrapper"><div class="vc_empty_space" style="height: 32px"><span class="vc_empty_space_inner"></span></div></div></div></div></div>';
    $title_div = '<div class="wpb_wrapper"><h2 style="color: #007dc5;text-align: left" class="vc_custom_heading">' . COURSE_DETAIL_LBL['buy_course_title'] . '</h2></div>';
    return $empty_div . $title_div;
}

function renderCheckoutStepBar()
{
    return
        '<div id="step_bar">
        <div class="row line">
            ' . initalizeStepBar() . '
        </div>
        <div class="row">
          <div class="col-xs-3 step-col-1"><div class="step step1 active"></div></div>
          <div class="col-xs-3 step-col-2"><div class="step step2"></div></div>
          <div class="col-xs-3 step-col-3"><div class="step step3"></div></div>  
          <div class="col-xs-3 step-col-4"><div class="step step4"></div></div>  
       </div>
    </div>';
}

/**
 * @param $product_id
 * @param $optional_courses
 * @param $cart_courses
 * @param $cart_item
 * @return string
 */
function renderOptionalCourses($product_id, $optional_courses, $cart_courses, $cart_item)
{
    $output = "";
    $is_checked = "";

    //    $output .= "<pre>";
    //    $output .= print_r($optional_courses, true);
    //    $output .= "</pre>";

    foreach ($optional_courses as $course) {
        $display_optional_course = "hidden";

        if (isset(COURSES_SELECTION_CRITERIA[$product_id]) && is_array(COURSES_SELECTION_CRITERIA[$product_id])) {
            $checked_criteria = COURSES_SELECTION_CRITERIA[$product_id];
            $_config = array_keys(COURSES_SELECTION_CRITERIA[$product_id]);
            if (in_array($course->slug, $_config)) {
                $display_optional_course = "";

                if (!isset($cart_courses[$product_id]) && $checked_criteria[$course->slug]) {
                    $is_checked = " checked='checked' case='1' ";
                } else if (isset($cart_item['optional_courses'][$course->id])) {
                    $is_checked = " checked='checked' case='2' ";
                } else {
                    $is_checked = " case='3' ";
                }


                $output .= '   <div class="form-group optional_courses css-' . $course->slug . ' ' . $display_optional_course . '" id="course_' . $course->slug . '">
                        <div class="checkbox">
                         
                                <input type="checkbox"
                                    name="' . $course->slug . '" id="' . $course->slug . '" value="' . $course->id . '"
                                    data-label="' . $course->title . '"
                                    ' . $is_checked . '
                                    data-price=' . $course->price . '>' . $course->name . '
                                    <input type="hidden" 
                                            name="optional[' . $course->slug . ']"
                                            id="' . $course->slug . '" value="" />
                                    ' . renderToolTip($course) . '
                        
                          
                        </div>
                    </div>';
            }
        }
    }
    return $output;
}

function renderToolTip($course)
{
    $tool_tip_text = $course->description;
    if (!isset(NO_TOOL_TIP[$course->slug])) {
        return '<span title="' . $tool_tip_text . '" class="glyphicon glyphicon-info-sign"></span>';
    }
    return '';
}

/**
 * @param $date
 * @return string
 */
function getDutchMonth($date): string
{
    $date = str_replace(array_keys(MONTHS), array_values(MONTHS), $date);
    return str_replace(array_keys(DAYS), array_values(DAYS), $date);
}

/**
 * Get All the courses in the cart in hashmap
 * @return array
 */
function getCartCourses()
{
    $action = isset($_GET["action"]) ? $_GET["action"] : "";
    if (is_admin() && $action == "edit") {
        return [];
    }
    $cart_courses = [];
    foreach (WC()->cart->get_cart() as $cart) {
        $cart_courses[$cart["product_id"]] = $cart;
    }
    return $cart_courses;
}


//function courseFullyBookedMsg() {
//    $courseFullyBookedHtml = '<div class="courseFullyBookedMsg no_event_hide">
//                     <label id="courseFullyBookedMsg" >
//        Sorry, op deze datum is de cursus volgeboekt. Selecteer een beschikbare datum of </br> neem
//                        <a id="redirect_btn" href="https://eduq.lucrative.ai/contact/" >contact</a> met ons op voor de mogelijkheden.
//                    </label>
//                   </div>';
//    return $courseFullyBookedHtml;
//}
