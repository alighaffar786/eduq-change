<?php
function uploads_csv()
{
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/courses/style-admin.css" rel="stylesheet" />
    <?php
    if (!empty($_FILES['csv-file']) && $_FILES['csv-file']['error'] == 0) {

        $file_name = $_FILES['csv-file']['name'];
        $file_size = $_FILES['csv-file']['size'];
        $file_tmp = $_FILES['csv-file']['tmp_name'];
        $file_type = $_FILES['csv-file']['type'];

        // Check if the file is a CSV file
        if ($file_type != 'text/csv') { ?>
            <div class="error">
                <p>Fout: Bestand moet een CSV-bestand zijn.</p>
            </div>
            <a class="back-button" href="<?php echo  admin_url('admin.php?page=uploads_csv&postID') ?>"> <-Back </a>

            <?php
            // echo "";
            exit;
        }

        // Check if the file size is less than or equal to 100KB
        if ($file_size > 100000) { ?>
            <div style="margin-left: 10px;" class="error">
                <p>Fout: de bestandsgrootte moet kleiner zijn dan of gelijk zijn aan 100 KB.</p>
            </div>
            <a href="<?php echo  admin_url('admin.php?page=uploads_csv&postID') ?>"> <-Back </a>
            <?php
            // echo "";
            exit;
        }

        // Process the CSV file
        $handle = fopen($file_tmp, "r");

        $courses_array = [];
        if ($handle) {
            ftruncate($handle, 0);
            $data = fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== false) {
                $keys = ['course', 'date', 'isFullyBooked'];
                $array = array_combine($keys, $data);
                $courses_array[] = $array;
            }
            fclose($handle);
        } else {
            ?>
            <div class="error">
                <p>Fout: Kan bestand niet openen.</p>
            </div>
            <?php
            exit;
        }
        // validation csv file
        $optional_courses = getOptionalCoursesFromWp();
        $optional_courses_ids = array_keys($optional_courses);
        $args = ['post_type' => 'product', 'posts_per_page' => -1, 'exclude' => $optional_courses_ids,];
        $posts = get_posts($args);
        $posts = array_column($posts, 'ID', 'post_title');
        $error = [];
        $i = -1;
        foreach ($courses_array as $key => &$csv_row) {

            $i++;
            //  course validation
            if (isset($posts[$csv_row['course']])) {
                $csv_row['course'] = $posts[$csv_row['course']];
            } else { ?>
                <div class="error">
                    <p><?php echo $error[$i]['course'] =  "controleer de cursusnaam op het regelnummer " . $key; ?></p>
                </div>
                <?php
            }
            //  date validation
            if (strtotime($csv_row['date']) !== false) {
                $csv_row['date'] = strtotime($csv_row['date']);
            } else { ?>
                <div class="error">
                    <p><?php echo $error[$i]['date'] =  "controleer de datum op het regelnummer " . $key; ?></p>
                </div>
                <?php
            }
            //  fullyBooked validation
            if ($csv_row['isFullyBooked'] === '1' || $csv_row['isFullyBooked'] === '0') {
            } else { ?>
                <div class="error">
                    <p><?php echo $error[$i]['isFullyBooked'] =  "controleer geboekte optie op regelnummer " . $key; ?></p>
                </div>
                <?php
            }
        }

        if (empty($error)) {
            global $wpdb;
            $table_name = $wpdb->prefix . "courses";
            // empty table
            $wpdb->query("TRUNCATE TABLE $table_name;");
            // insert csv file data into table
            foreach ($courses_array as $courses) {
                $wpdb->insert(
                    $table_name, //table
                    array('isFullyBooked' => $courses['isFullyBooked'], 'date' => $courses['date'], 'course' => $courses['course']), //data
                    array('%s', '%s', '%d', '%s') //data format
                );
            }
            $_SESSION["import_csv"] = true;
            header('location:' . admin_url('admin.php?page=course_list'));
        } else {
            ?>
            <a class="back-button" href="<?php echo  admin_url('admin.php?page=uploads_csv&postID') ?>"> <-Back </a>
            <?php
            exit;
        }
    }



    if (isset($_POST['uploads'])) {

        global $wpdb;

        $table_name = $wpdb->prefix . "courses";
        $date = strtotime($_POST["date"]);
        $id = $_POST["id"];
        $isFullyBooked = $_POST["isFullyBooked"] ?? '0';
        $course = $_POST["course"];
        $rows = $wpdb->get_results("SELECT id from $table_name where date = $date AND course = $course");
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
    ?>

    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/courses/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Import CSV File </h2>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
            <table style="margin-bottom: 10px;" class='wp-list-table widefat fixed'>
                <tr style="display: flex; flex-direction:column; padding:10px;">
                    <th style="font-weight:700; width:100%;" class="ss-th-width">Selecteer een bestand</th>
                    <td>
                        <input type="file" name="csv-file" id="csv-file" class="ss-field-width" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php

                        $base_url = home_url();
                        // echo $base_url;
                        ?>
                         <a class="back-button" target="_blank" href="<?php echo $base_url . "/wp-content/uploads/import_sample.csv" ?>">Download sample</a>
                    </td>
                </tr>
            </table>
            <input type='submit' name="uploads" value='Opslaan' class='button'>
        </form>
    </div>
    <?php
} //end of function
?>