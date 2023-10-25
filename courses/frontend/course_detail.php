<?php
function renderCourseDetail()
{

    $slug = get_permalink(get_the_ID());
    if (str_contains($slug, 'factuurgegevens')) {
        return false;
    }

    global $post;
    $id = $post->ID;

    $cart_items = getCartCourses();
    if (isset($cart_items[$id])) {
        $cart_item = $cart_items[$id];
        $_course_url = get_permalink(CHECKOUT_PAGE_ID) . "?process=buy&course_id=" . $cart_item["product_id"] . "&event_id=" . $cart_item["info"]["course"];
        $_course_url .= "&cart_key=" . $cart_item["key"];

        // header('Location:' . $_course_url);
        // exit();
    }

    $product = wc_get_product($id);
    $eduq_all_courses = getCourses($id);

    $eduq_courses = [];
    foreach ($eduq_all_courses as $course) {
        $calendar_date = date("Y-m-d", $course->date);
        if ($calendar_date > date("Y-m-d")) {
            $eduq_courses[] = $course;
        }
    }


    if (empty($eduq_courses)) {
        $html = "Er zijn momenteel geen data bekend voor deze opleiding.</br>";
        $html .= "Neem <a href='/contact/'>contact met ons op.</a>";

        //return $html;
    } else {
    }
    $slug = get_permalink(get_the_ID());
    if (str_contains($slug, 'factuurgegevens')) {
        return false;
    } else {

        wp_enqueue_style('edu_wizard_css', EDUQ_COURSE_ASSETS_URL . 'styles/jquery.steps.css', false, '5.3');

        // Register the JS file with a unique handle, file location, and an array of dependencies
        wp_register_script("courses_events", EDUQ_COURSE_ASSETS_URL . 'scripts/courses_events.js');
        wp_register_script("calendar_options", EDUQ_COURSE_ASSETS_URL . 'scripts/calendar_options.js');
        // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
        wp_localize_script('courses_events', 'CoursesAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

        $events = get_events($eduq_courses);
        $calendar_options = [
            'days' => array_values(DAYS),
            'order_days' => array_values(ORDER_DAYS),
            'start_from_day' => START_FROM_DAY,
            'months' => array_values(MONTHS),
            "events" => $events,
        ];
        wp_localize_script('calendar_options', 'CalendarOptions', $calendar_options);

        // enqueue jQuery library and the script you registered above

        wp_enqueue_script('courses_events');
        wp_enqueue_script('calendar_options');
        //$s1 = "<script type='text/javascript'>var events_json_by_date = '" . $json_by_date . "'</script>";


        wp_enqueue_style('simple_calendar_css', EDUQ_COURSE_ASSETS_CALENDAR_URL . 'simple-calendar.css', false, '2.3');
        wp_enqueue_script('simple_calendar_js', EDUQ_COURSE_ASSETS_CALENDAR_URL . 'jquery.simple-calendar.js', false, '2.3');

        wp_enqueue_script('edu_course_js', EDUQ_COURSE_ASSETS_URL . 'scripts/course.js', false, '2.5', true);
        wp_enqueue_style('edu_wizard_responsive', EDUQ_COURSE_ASSETS_URL . 'styles/responsive.css', false, '1.4');
    }


    return getCourseDetail($eduq_courses, $id, $product);
}
