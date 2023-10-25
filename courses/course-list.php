<?php

function course_list($product_id = 0)
{
?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/courses/style-admin.css" rel="stylesheet" />

    <div class="wrap">
        <?php
        // successfully delete course message
        if (isset($_SESSION["course_delete"])) { ?>
            <div class="updated">
                <p>Training verwijderen</p>
            </div>
        <?php
            unset($_SESSION["course_delete"]);
        } ?>
        <?php
        // successfully import csv file message
        if (isset($_SESSION["import_csv"])) { ?>
            <div class="updated">
                <p>csv-bestand is succesvol geïmporteerd.</p>
            </div>
        <?php
            unset($_SESSION["import_csv"]);
        } ?>






        <h1>VCA-PLUS Trainingen</h1>
        <a class="page-title-action" href="<?php echo admin_url('admin.php?page=course_create&postID=' . $product_id); ?>">Training
            toevoegen</a>
        <hr class="wp-header-end" />
        <div style="display: flex; justify-content:space-between;" class="tablenav top">
            <div class="alignleft actions">
                <a class="page-title-action" href="<?php echo admin_url('admin.php?page=course_create&postID=' . $product_id); ?>">Training
                    toevoegen</a>
            </div>
            <div class="alignleft actions">
                <?php
                $course_list = $_GET['page'];
                if ($course_list == "course_list") {
                ?>
                    <a class="page-title-action" href="<?php echo admin_url('admin.php?page=uploads_csv&postID=' . $product_id); ?>">Impotr</a>
                    <a class="page-title-action" href="<?php echo admin_url('admin.php?page=export_csv&postID=' . $product_id); ?>">exporteren</a>
                    <a class="page-title-action" href="<?php echo admin_url('admin.php?page=delete_course&postID=' . $product_id); ?>">Cursusgeschiedenis verwijderen</a>
                <?php
                }
                ?>
            </div>
            <!-- <br class="clear"> -->
        </div>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "courses";
        $sql = "SELECT id,course,location,date,inclusive,duration,isFullyBooked from $table_name ";
        if ($product_id > 0) {
            $sql .= "where  course = " . $product_id;
        }

        $order_mapping = [
            "" => "desc",
            "asc" => "desc",
            "desc" => "asc",
        ];

        if (isset($_GET["order_date"])) {
            $sql .= " order by date " . $_GET["order_date"];
        } else if (isset($_GET["isFullyBooked"])) {
            $sql .= " order by isFullyBooked " . $_GET["isFullyBooked"];
        } elseif (isset($_GET['order_id'])) {
            $sql .= " order by id " . $_GET['order_id'];
        } else {
            $sql .= " order by date DESC";
        }
        $rows = $wpdb->get_results($sql);

        ?>
        <table id="vca_training" style="width:100%" class='wp-list-table widefat fixed striped posts'>
            <thead>
                <tr>
                    <?php
                    if (isset($_GET['post'])) {
                        // product page
                        $base_href = admin_url('post.php?post=' . $_GET['post'] . '&action=edit&scroll');
                    } else {
                        // vca traning page
                        $base_href = admin_url('admin.php?page=course_list');
                    }
                    // id order
                    $id_order = isset($_GET["order_id"]) ? $_GET["order_id"] : "";
                    $id_order_href = $base_href . "&order_id=" . $order_mapping[$id_order];

                    // date order 
                    $date_order = isset($_GET["order_date"]) ? $_GET["order_date"] : "";
                    $date_href = $base_href . "&order_date=" . $order_mapping[$date_order];

                    // fullybooked order 
                    $isFullyBooked_order = isset($_GET["isFullyBooked"]) ? $_GET["isFullyBooked"] : "";
                    $isFullyBooked_order_href = $base_href . "&isFullyBooked=" . $order_mapping[$isFullyBooked_order];

                    ?>
                    <th class="manage-column ss-list-width sorted <?php echo $order_mapping[$id_order]; ?>">
                        <a style="color:#2c3338; <?php echo ($order_mapping[$id_order] == "asc") ? " font-weight : bold;" : "" ?> " href="<?php echo $id_order_href; ?>">

                            <span>id</span><span class="sorting-indicator"></span></a>
                        </a>
                    </th>
                    <?php if ($product_id == 0) {
                    ?>
                        <th class="manage-column ss-list-width">Cursus</th>
                    <?php }


                    ?>
                    <th class="manage-column ss-list-width sorted <?php echo $order_mapping[$date_order]; ?>">
                        <a style="color:#2c3338; <?php echo ($order_mapping[$date_order] == "asc") ? " font-weight : bold;" : "" ?> " href="<?php echo $date_href; ?>">
                            <span>Datum</span><span class="sorting-indicator"></span></a>
                        </a>
                    </th>
                    <th class="manage-column ss-list-width sorted <?php echo $order_mapping[$isFullyBooked_order]; ?>">
                        <a style="color:#2c3338; <?php echo ($order_mapping[$isFullyBooked_order] == "asc") ? " font-weight : bold;" : "" ?> " href="<?php echo $isFullyBooked_order_href; ?>">
                            <span>cursusvol</span><span class="sorting-indicator">

                        </a>
                    </th>
                    <th class="manage-column ss-list-width">Actie</th>
                </tr>
            </thead>

            <?php foreach ($rows as $row) { ?>

                <tr>
                    <td class="manage-column ss-list-width "><?php echo $row->id; ?></td>
                    <?php if ($product_id == 0) {
                    ?>
                        <td style="font-weight: bold;" class="manage-column ss-list-width ">
                            <?php
                            $id = $row->id;

                            $course = $row->course;
                            $post = get_post($course);

                            ?>
                            <a href="<?php echo get_permalink($post); ?>" target="_blank"><?php echo $post->post_title; ?></a>

                        </td>
                    <?php }
                    ?>
                    <td class="manage-column ss-list-width"><?php echo date('d-m-Y', $row->date); ?></td>
                    <td class="manage-column ss-list-width">
                        <?php
                        if ($row->isFullyBooked == 1) {
                            echo "Yes";
                        } else {
                            echo "No";
                        }
                        ?></td>
                    <td>
                        <a href="
                        <?php
                        $postID =  $product_id > 0 ? "&postID=" . $product_id : "";
                        echo admin_url('admin.php?page=course_update&id=' . $row->id . $postID); ?>">Update</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php

    if (isset($_GET['scroll']) && isset($_GET["action"])) { ?>
        <script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/courses/style-admin.js"></script>
<?php
    }
}
?>