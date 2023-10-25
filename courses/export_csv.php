<?php
function export_csv()
{
    $filename = ABSPATH . "wp-content/uploads/vca-courses.csv";
    $file = fopen($filename, 'w');
    // Set the data for the CSV file
    $data = ['course', 'date', 'isFullyBooked'];
    fputcsv($file, $data);
    global $wpdb;
    $table_name = $wpdb->prefix . "courses";
    $sql = "SELECT course,date,isFullyBooked from $table_name ";
    $rows = $wpdb->get_results($sql);
    $optional_courses = getOptionalCoursesFromWp();
    $optional_courses_ids = array_keys($optional_courses);
    $args = ['post_type' => 'product', 'posts_per_page' => -1, 'exclude' => $optional_courses_ids,];
    $posts = get_posts($args);
    $posts = array_column($posts, 'post_title', 'ID');
    foreach ($rows as $row) {
        $date = date($row->date);
        $new_date_str = date("Y-m-d", $date);
        $course_name  = $posts[$row->course];
        $data = ["$course_name", "$new_date_str", "$row->isFullyBooked"];
        fputcsv($file, $data);
    }
    $base_url = home_url();
    $esport_url = $base_url . "/wp-content/uploads/vca-courses.csv";
    header('location: ' . $esport_url);
}
