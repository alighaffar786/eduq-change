<?php
function course_create()
{
    includeSelect2LibForAdmin();

    $id = $_POST['id'];

    $location = $_POST["location"];
    //insert
    if (isset($_POST['insert'])) {
        //        echo "<pre>";
        //        print_r($_POST);
        //        die;
        global $wpdb;

        $table_name = $wpdb->prefix . "courses";
        $date = strtotime($_POST["date"]);
        $id = $_POST["id"];
        $location = $_POST["location"];
        $inclusive = $_POST["inclusive"];
        $duration = $_POST["duration"];
        $isFullyBooked = $_POST["isFullyBooked"] ?? '0';

        $course = $_POST["course"];
        $rows = $wpdb->get_results("SELECT id from $table_name where date = $date AND course = $course");
        if (isset($rows) && !empty($rows)) {
            $_SESSION["update"] = ["error" => getDutchMonth(date("l, j F Y", $date)) . " bestaat al voor deze cursus."];
        } else {
            $wpdb->insert(
                $table_name, //table
                array('location' => $location, 'inclusive' => $inclusive, 'duration' => $duration, 'isFullyBooked' => $isFullyBooked, 'date' => $date, 'course' => $course), //data
                array('%s', '%s', '%s', '%s', '%s', '%d', '%s') //data format
            );
            // $courses_id = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 1;");
            $courses_data =  $wpdb->get_results("SELECT MAX(id) id FROM $table_name");
            foreach ($courses_data as $course_data) {
                $course_id = $course_data->id;
            }
            createCourseLog("create", $course_id);
            // die();
            // createCourseLog("create", $id);
            $_SESSION["update"] = ["success" => "Training bijwerken"];

            // $message .= "Training aangemaakt";
        }
        // print_r($rows);
        // die;





        //$id = Null;

    }

    $optional_courses = getOptionalCoursesFromWp();
    $optional_courses_ids = array_keys($optional_courses);
    $args = array('post_type' => 'product', 'posts_per_page' => -1, 'exclude' => $optional_courses_ids,);

    $posts = wc_get_products($args);
    $list = '';
    $postID = isset($_GET['postID']) ? $_GET['postID'] : "0";
    // while ($loop->have_posts()) : $loop->the_post();
    foreach ($posts as $product) {
        $selected = "";
        if ($product->get_id() == (int)$postID) {
            $selected = "selected";
        }
        $list = $list . '<option ' . $selected  . ' value="' . $product->get_id() . '">' . $product->get_title() . '</option>';
    }

    if (isset($_SESSION["update"])) {
        $message = "";
        $css_class = "";
        if (isset($_SESSION["update"]["success"])) {
            $message = $_SESSION["update"]["success"];
            $css_class = "updated";
        } else if (isset($_SESSION["update"]["error"])) {
            $message = $_SESSION["update"]["error"];
            $css_class = "error";
        } else if (isset($_SESSION["update"]["warning"])) {
            $message = $_SESSION["update"]["warning"];
            $css_class = "notice notice-warning";
        }
?>
        <div class="<?php echo $css_class; ?>">
            <p><?php echo  $message ?></p>
        </div>
    <?php
        unset($_SESSION['update']);
    }


    ?>

    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/courses/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>VCA training toevoegen</h2>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <table style="margin-bottom:20px" class='wp-list-table widefat fixed'>
                <tr>
                    <th class="ss-th-width">Product</th>
                    <td><select id="course" name="course"><?= $list ?></select>
                </tr>
                <tr>
                    <th class="ss-th-width">Datum</th>
                    <td><input type="date" id="datepicker" name="date" value="" class="ss-field-width" /></td>
                </tr>
                <tr>
                    <th class="ss-th-width">cursusvol</th>
                    <td><input type="checkbox" name="isFullyBooked" value="1" class="ss-field-width" />
                    </td>
                </tr>
            </table>
            <input type='submit' name="insert" value='Opslaan' class='button'>
        </form>
    </div>
<?php
}
