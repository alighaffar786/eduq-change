<?php
global $product;
$id = $product->get_id();
$eduq_all_courses = getCourses($id);
$eduq_courses = [];
foreach ($eduq_all_courses as $course) {
    $calendar_date = date("Y-m-d", $course->date);
    if ($calendar_date > date("Y-m-d")) {
        $eduq_courses[] = $course;
    }
}
$latest_course = $eduq_courses[count($eduq_courses) - 1];
$course_availability = 0;
if (date("Y-m-d", $latest_course->date) >= date("Y-m-d")) {
    $course_availability = 1;
}

$categories = implode(", ", array_column(get_the_terms($product->get_id(), 'product_cat'), "name"));
$post_id = $product->get_id();
$eduq_location = get_post_meta($post_id, "_eduq_location");
$eduq_Cursusduur = get_post_meta($post_id, "_eduq_Cursusduur");
$eduq_Inclusief = get_post_meta($post_id, "_eduq_Inclusief");
$eduq_Opties = get_post_meta($post_id, "_eduq_Opties");

$course_event = $eduq_courses[0];
$cart_items = getCartCourses();
// print_r($course_event->course);
// die;
if (isset($cart_items[$course_event->course])) {
    $cart_course =  "&cart_course=1";
} else {
    $cart_course =  "&cart_course=0";
}
$location = !empty($course_event) ? explode("-", $course_event->location)[1] : "";
$redirect_link = get_permalink(CHECKOUT_PAGE_ID) . "?process=buy&course_id=" . $course_event->course . "&event_id=0" . $cart_course;
$link_lbl = COURSE_DETAIL_LBL["buy_course_lbl"];

if ($course_availability == 0) {
    $page = get_page_by_path('contact');
    $redirect_link = get_permalink($page->ID);
    $link_lbl = COURSE_DETAIL_CALENDAR_LBL["course_not_available_lbl"];
    $location = NOT_AVAILABLE_LBL;
    if (!empty($course_event)) {
        $course_event->duration = NOT_AVAILABLE_LBL;
        $course_event->inclusive = NOT_AVAILABLE_LBL;
    }
}

$calendar_css_class = !empty($eduq_courses) ? 1 : 0;

$calendar_right_css = "col-md-8 calendar_right_sect";
//if ($course_availability == 0) {
//    $calendar_right_css = "col-md-12 calendar_right_sect";
//
//}
?>
    <div class="wpb_column vc_column_container vc_col-sm-12 course-available" data-course-availability="<?php echo $course_availability; ?>">
        <div class="vc_column-inner">
            <div class="calendar_sect_main calendar-<?php echo $calendar_css_class; ?>">
                <div class="course_detail">
                    <?php
                    if ($course_availability == 1) { ?>
                        <div class="col-sm-12 col-md-4 calendar_left_sect">
                            <div id="edu_calendar2" class="calendar-container"></div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="col-sm-12 col-md-4 course_unavailable_text">
                            <p><?php echo CALENDAR_ERROR_MESSAGES; ?></p>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="col-sm-12 <?php echo $calendar_right_css; ?>">
                        <div class="row">
                            <div class="col-sm-4">
                                <h4><?php echo COURSE_DETAIL_CALENDAR_LBL["location"]; ?></h4>
                                <p id="course_location"><?php echo $eduq_location[0]; ?></p>
                            </div>
                            <div class="col-sm-4 calendar_right_sect_border">
                                <h4><?php echo COURSE_DETAIL_CALENDAR_LBL["duration"]; ?></h4>
                                <p id="course_duration">
                                    <?php echo  $eduq_Cursusduur[0]; ?></p>
                                <!--                                <p>1 dag ( from 9-17:00)</p>-->
                            </div>
                            <div class="col-sm-4 calendar_right_sect_border">
                                <h4><?php echo COURSE_DETAIL_CALENDAR_LBL["price"]; ?></h4>
                                <p>€ <span id="course_price"><?php echo $product->get_regular_price(); ?></span> (Euro
                                    excl. BTW)</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <h4><?php echo COURSE_DETAIL_CALENDAR_LBL["inclusive"]; ?></h4>
                                <p id="course_inclusive"><?php echo $eduq_Inclusief[0]; ?></p>
                                <!--                                <p>-->
                                <?php //echo getTariff($product);
                                ?><!--</p>-->
                            </div>
                            <div class="col-sm-4 calendar_right_sect_border">
                                <h4><?php echo COURSE_DETAIL_CALENDAR_LBL["options"]; ?></h4>
                                <p><?php echo $eduq_Opties[0]; ?></p>
                            </div>
                            <div class="col-sm-4 col-redirect-btn">
                                <a id="redirect_btn" href="<?php echo $redirect_link; ?>" class="btn btn-primary"><?php echo $link_lbl; ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

<?php

wp_localize_script('calendar_events', 'CalendarEvents', $events);