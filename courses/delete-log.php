<?php
function delete_course()
{
    global $wpdb;
    $course_log_table_name = $wpdb->prefix . 'update_courses_logs';
    $rows = $wpdb->get_results("SELECT * from $course_log_table_name where action = 'delete' order by created_at desc");
    if (isset($rows) && !empty($rows)) {
?>
        <a class="back-button" href="<?php echo  admin_url('admin.php?page=course_list&postID') ?>"> <-Back </a>
                <?php
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
            }
        }
        ?>