<?php
session_start();

function course_update()
{
    includeSelect2LibForAdmin();

    global $wpdb;
    $table_name = $wpdb->prefix . "courses";
    $id = $_GET["id"];
    $location = $_POST["location"];
    $course = $_POST["course"];
    $date = strtotime($_POST["date"]);
    $inclusive = $_POST["inclusive"];
    $duration = $_POST["duration"];
    $isFullyBooked = $_POST["isFullyBooked"] ?? '0';
    $course_log_table_name = $wpdb->prefix . 'update_courses_logs';

    //update
    if (isset($_POST['update'])) {
        $rows = $wpdb->get_results("SELECT * from $table_name where NOT (id = '$id') AND (date = $date And course = $course)");
        if (isset($rows) && !empty($rows)) {
            $_SESSION["update"] = ["error" => getDutchMonth(date("l, j F Y", $date)) . " bestaat al voor deze cursus."];
        } else {
            createCourseLog("update", $id);
            $wpdb->update(
                $table_name, //table
                array('location' => $location, 'inclusive' => $inclusive, 'duration' => $duration, 'isFullyBooked' => $isFullyBooked, 'date' => $date, 'course' => $course), //data
                array('ID' => $id), //where
                array('%s', '%s', '%s', '%s', '%s', '%s'), //data format
                array('%s') //where format
            );
            $_SESSION["update"] = ["success" => "Training bijwerken"];
        }
        if (isset($_POST['postID']) && $_POST['postID'] > 0) {
            $return =     admin_url('admin.php?page=course_update&id=' . $id . '&postID=' . $_POST['postID']);
        } else {
            $return =     admin_url('admin.php?page=course_update&id=' . $id);
        }

        header('location:' . $return);
    } //delete
    else if (isset($_POST['delete'])) {
        createCourseLog("delete", $id);
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
        $_SESSION["course_delete"] = "true";
        if (isset($_POST['postID']) && $_POST['postID'] > 0) {
            header('location:' . admin_url('post.php?post=' . $_POST['postID'] . '&action=edit&sacroll=true'));
            // echo $hello
        } else {
            header('location:' . admin_url('admin.php?page=delete_course'));
        }
    } else if (isset($_POST['back'])) {
        header('location:' . admin_url('post.php?post=' . $_POST['postID'] . '&action=edit&sacroll=true'));
    } else { //selecting value to update	
        $courses = $wpdb->get_results($wpdb->prepare("SELECT id,location,isFullyBooked,date,course from $table_name where id=%s", $id));
        foreach ($courses as $c) {
            $location = $c->location;
            $date = date('Y-m-d', $c->date);
            $course = $c->course;
            $isFullyBooked = $c->isFullyBooked;
        }
        $optional_courses = getOptionalCoursesFromWp();
        $optional_courses_ids = array_keys($optional_courses);
        $args = array('post_type' => 'product', 'posts_per_page' => -1, 'exclude' => $optional_courses_ids,);
        $posts = wc_get_products($args);
        $list = '';

        foreach ($posts as $product) {
            $selected = "";
            if ($product->get_id() == $course) {
                $selected = "SELECTED";
            }
            $list = $list . '<option ' . $selected . ' value="' . $product->get_id() . '">' . $product->get_title() . '</option>';
        }
    }
?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/courses/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>VCA Training</h2>

        <?php if ($_POST['delete']) { ?>
            <div class="updated">
                <p>Training verwijderen</p>
            </div>
            <a href="<?php echo admin_url('admin.php?page=course_list') ?>">&laquo; Trainingsoverzicht</a>

        <?php } else if ($_POST['update']) { ?>
            <div class="updated">
                <p>Training bijwerken</p>
            </div>
            <a href="<?php echo admin_url('admin.php?page=course_list') ?>">&laquo; Trainingsoverzicht</a>

            <?php } else {
            global $wpdb;
            $table_name = $wpdb->prefix . "courses";
            $courseList = $wpdb->get_results("select * from $table_name where id = {$_GET['id']}");
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
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

                <table style="margin-bottom: 20px;" class='wp-list-table ss widefat fixed'>
                    <tr>
                        <th>Product</th>
                        <td>
                            <select SELECTED="<?= $course ?>" id="course" name="course"><?= $list ?></select>
                        </td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <td><input type="date" id="datepicker" name="date" value="<?= $date ?>" /></td>
                    </tr>

                    <tr>
                        <th>cursusvol</th>
                        <td><input type="checkbox" name="isFullyBooked" value="1" <?php if ($isFullyBooked == '1') echo "checked='checked'"; ?> />
                        </td>
                    </tr>
                </table>
                <input type='submit' name="update" value='Save' class='button'> &nbsp;&nbsp;
                <?php
                if (isset($_GET['postID'])) {
                    $delete = $_GET['postID'];
                } else {
                    $delete = "delete";
                }
                ?>
                <input hidden name="postID" type="text" value="<?php echo $delete; ?>">&nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Weet je het zeker dat je deze training wilt verwijderen?')">&nbsp;&nbsp;
                <?php if (isset($_GET['postID']) || isset($_POST['postID'])) { ?>
                    <input type='submit' name="back" value='<-- back' class='button'>
                <?php } ?>
            </form>
        <?php } ?>

    </div>
    <?php
    $newID = $_SESSION['update_log'];
    $rows = $wpdb->get_results("SELECT * from $course_log_table_name where course_id = '$id' order by created_at desc");
    if (isset($rows) && !empty($rows)) {
        foreach ($rows as $result) {
            list($diff, $current_time, $timestamp_now) =  created_at_field($result->created_at);
            $alt = $result->created_at . "--" . $current_time . "--" . $timestamp_now;
    ?>
            <div class="loog-wrapper">
                <div class="loog-content">
                    <span><?php echo $result->logs ?></span>
                </div>
                <div class="loog-date">
                    <span title="<?php echo $alt; ?>" alt="<?php echo $alt; ?>">
                        <?php
                        echo created_at_field($result->created_at);
                        ?>
                    </span>
                </div>
            </div>
    <?php }
    }    // unset($_SESSION['update_log']);
    ?>
<?php
}
