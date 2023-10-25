<?php

define('EDUQ_COURSE__FILE__', __FILE__);
define('EDUQ_COURSE_URL', plugins_url('/', EDUQ_COURSE__FILE__));
define('EDUQ_COURSE_ASSETS_URL', EDUQ_COURSE_URL . 'assets/');
define('EDUQ_COURSE_ASSETS_CALENDAR_URL', EDUQ_COURSE_ASSETS_URL . 'simple-calendar/dist/');
define('EDUQ_COURSE_PLUGIN_BASE', plugin_basename(EDUQ_COURSE__FILE__));
define('EDUQ_COURSE_PLUGIN_PATH', plugin_dir_path(EDUQ_COURSE__FILE__));
define('EDUQ_COURSE_PLUGIN_WIZARD', EDUQ_COURSE_PLUGIN_PATH . 'wizard.php');
define('EDUQ_COURSE_PLUGIN_CALENDAR', EDUQ_COURSE_PLUGIN_PATH . 'calendar.php');

define('EDUQ_COURSE_PLUGIN_WIZARD_BASE', EDUQ_COURSE_PLUGIN_PATH . "steps/");
define('EDUQ_COURSE_PLUGIN_CHECKOUT_BASE', EDUQ_COURSE_PLUGIN_PATH . "checkout/");
define('EDUQ_COURSE_PLUGIN_FRONTEND_BASE', EDUQ_COURSE_PLUGIN_PATH . "frontend/");
define('EDUQ_COURSE_PLUGIN_TEMPLATE_BASE', EDUQ_COURSE_PLUGIN_PATH . "templates/");
define('EDUQ_COURSE_PLUGIN_INCLUDES_BASE', EDUQ_COURSE_PLUGIN_PATH . "includes/");
define('EDUQ_COURSE_PLUGIN_VIEWS_BASE', EDUQ_COURSE_PLUGIN_PATH . "views/");
define('EDUQ_WC_PLUGIN_BASE', WP_PLUGIN_DIR . '/woocommerce');
define('EDUQ_WC_PLUGIN_ADMIN_META_BOX_BASE', EDUQ_WC_PLUGIN_BASE . '/includes/admin/meta-boxes/views');


$domain = "woocommerce";
//$text= 'Invalid payment method.';

function translateInvalidPaymentMethod($translation, $text, $domain)
{
    if ($text == INVALID_PAYMENT_TEXT) {
        $translation = TRANSLATED_TEXT;
    }
    return $translation;
}
add_filter("gettext_{$domain}", 'translateInvalidPaymentMethod', 10, 3);
/*
Plugin Name: Courses
Description: Beheer van VCA-Plus trainigen
Version: 1
Author: cms4biz.nl
Author URI: http://cms4biz.nl
*/
// function to create the DB / Options / Defaults					
function c_options_install()
{

    global $wpdb;

    $table_name = $wpdb->prefix . "courses";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` varchar(3) CHARACTER SET utf8 NOT NULL,
            `date` varchar(50) CHARACTER SET utf8 NOT NULL,
			`location` varchar(150) CHARACTER SET utf8 NOT NULL,
			`course` varchar(10) CHARACTER SET utf8 NOT NULL,
			`date` NOT NULL,
            PRIMARY KEY (`id`)
          ) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
function includeSelect2LibForAdmin()
{
    wp_enqueue_script("select2", "//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js");
    wp_enqueue_script("select2_execute", WP_PLUGIN_URL . "/courses/style-admin.js");
    wp_enqueue_style("select2_style", "//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css");
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'c_options_install');

//menu items
add_action('admin_menu', 'courses_modifymenu');
function courses_modifymenu()
{

    //this is the main item for the menu
    add_menu_page(
        'Trainingen', //page title
        'VCA Trainingen', //menu title
        'manage_options', //capabilities
        'course_list', //menu slug
        'course_list' //function
    );

    //this is a submenu
    add_submenu_page(
        'trainigen_list', //parent slug
        'Nieuw training', //page title
        'Toevoegen', //menu title
        'manage_options', //capability
        'course_create', //menu slug
        'course_create'
    ); //function

    add_submenu_page(
        null, //parent slug
        'upload csv file', //page title
        'upload', //menu title
        'manage_options', //capability
        'uploads_csv', //menu slug
        'uploads_csv'
    ); //function

    add_submenu_page(
        null, //parent slug
        'Export csv file', //page title
        'export', //menu title
        'manage_options', //capability
        'export_csv', //menu slug
        'export_csv'
    );

    add_submenu_page(
        null, //parent slug
        'Delete Courses loogs', //page title
        'delete courses loogs', //menu title
        'manage_options', //capability
        'delete_course', //menu slug
        'delete_course'
    ); //function




    //this submenu is HIDDEN, however, we need to add it anyways
    add_submenu_page(
        null, //parent slug
        'Update Training', //page title
        'Update', //menu title
        'manage_options', //capability
        'course_update', //menu slug
        'course_update'
    ); //function
}
define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'course-list.php');
require_once(ROOTDIR . 'course-create.php');
require_once(ROOTDIR . 'course-update.php');
require_once(ROOTDIR . 'uploads_csv.php');
require_once(ROOTDIR . 'delete-log.php');
require_once(ROOTDIR . 'export_csv.php');

require_once(ROOTDIR . 'wizard.php');

function add_buy_process_page()
{
    $args = ['name' => 'buy-process', 'post_type' => 'page'];
    $the_query = new WP_Query($args);

    if (!$the_query->have_posts()) {

        $my_post = [
            'post_title' => 'Cursus',
            'post_name' => 'buy-process',
            'post_content' => '[buy_course]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1
        ];
        $m = wp_insert_post($my_post);
        define("CHECKOUT_PAGE_ID", $m->ID);
    } else {
        // no posts found
        $posts = $the_query->get_posts();
        //        print_r($posts);
        //        die;
        define("CHECKOUT_PAGE_ID", $posts[0]->ID);
    }
}

add_action('init', 'add_buy_process_page');
function createCourseLog($action, $id)
{
    global $wpdb;
    $course = $_POST["course"];
    $date = strtotime($_POST["date"]);
    $isFullyBooked = $_POST["isFullyBooked"] ?? '0';
    $course_log_table_name = $wpdb->prefix . 'update_courses_logs';
    $table_name = $wpdb->prefix . "courses";
    $results = $wpdb->get_results("SELECT * from $table_name where id = '$id' ");
    foreach ($results as $result) {
        $check_course = $result->course;
        $check_date = $result->date;
        $check_isFullyBooked = $result->isFullyBooked;
    }
    $optional_courses = getOptionalCoursesFromWp();
    $optional_courses_ids = array_keys($optional_courses);
    $args = ['post_type' => 'product', 'posts_per_page' => -1, 'exclude' => $optional_courses_ids,];
    $posts = get_posts($args);
    $posts = array_column($posts, 'post_title', 'ID');

    $courseName = $posts[$check_course];
    $courseDate = getDutchMonth(date("l, j F Y", $check_date));
    // update course loog
    if ($action == "update") {

        $log = "Beheerder is bijgewerkt";
        if ($check_date != $date) {
            $log .= ", datum " . date('d-m-Y', $check_date) . " naar " . date('d-m-Y', $date);
        }
        if ($check_isFullyBooked != $isFullyBooked) {
            if ($isFullyBooked == "1") {
                $log .= ", natuurlijk vol nee tot ja";
            } else {
                $log .= ", cursus vol ja tot nee";
            }
        }
        if ($check_course != $course) {
            $log .= ", koers $check_course naar $course";
        }
        if ($log != "Beheerder is bijgewerkt") {
            date_default_timezone_set("Europe/Amsterdam"); // Set the timezone to Duch time
            $current_timestamp = date('Y-m-d H:i:s');
            $wpdb->query("INSERT INTO $course_log_table_name (product_id, course_id, logs, action, created_at)
            VALUES ('$course', '$id', '$log', '$action', '$current_timestamp');");
        }
        $_SESSION['update_log']  = $id;
    }
    // delete course loog
    elseif ($action == "delete") {
        // Beheerder heeft de $courseName verwijderd. welke datum $courseDate is
        $log  = "Beheerder heeft de $courseName verwijderd. welke datum $courseDate is";
        date_default_timezone_set("Europe/Amsterdam"); // Set the timezone to Duch time
        $current_timestamp = date('Y-m-d H:i:s');
        $wpdb->query("INSERT INTO $course_log_table_name (product_id, course_id, logs, action, created_at)
        VALUES ('$course', '$id', '$log', '$action', '$current_timestamp');");
    } elseif ($action == "create") {
        date_default_timezone_set("Europe/Amsterdam"); // Set the timezone to Duch time
        $current_timestamp = date('Y-m-d H:i:s');
        $log = "admin heeft een cursus gemaakt met id $id";
        date_default_timezone_set("Europe/Amsterdam"); // Set the timezone to Duch time
        $current_timestamp = date('Y-m-d H:i:s');
        $wpdb->query("INSERT INTO $course_log_table_name (product_id, course_id, logs, action, created_at)
        VALUES ('$course', '$id', '$log', '$action', '$current_timestamp');");
    }
}
function created_at_field($past_date)

{


    $timestamp_past = strtotime($past_date);
    $current_time = gmdate("Y-m-d\TH:i:s\Z");
    date_default_timezone_set("Europe/Amsterdam");
    $timestamp_now = time();
    $diff_seconds = $timestamp_now - $timestamp_past;
    $diff_minutes = floor($diff_seconds / 60);
    $diff_hours = floor($diff_seconds / 3600);
    $diff_days = floor($diff_seconds / 86400);

    $diff = "";
    if ($diff_minutes != "0" && ($diff_hours == "0" && $diff_days == "0")) {
        $diff =  "$diff_minutes min ago";
    } elseif ($diff_days == "0" && ($diff_hours != "0" && $diff_minutes != "0")) {
        $diff =  "$diff_hours hours ago";
    } elseif ($diff_days != "0" && $diff_hours != "0" && $diff_minutes != "0") {
        $diff =  "$diff_days days ago";
    } else {
        $diff = "$diff_seconds sec ago";
    }
    return $diff;
}
