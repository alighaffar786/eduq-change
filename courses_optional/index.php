<?php
/*
  Plugin Name: Optional Courses for
  Plugin URI: https://not.yet/
  Description: adding few products in wo-commerce
  Version: 1.0
  Author: Ali
  Author URI: https://office.com/
  License: GPLv2 or later
  Text Domain: ali
  */

define('EDUQ_COURSE_OPTIONAL_FILE__', __FILE__);
define('EDUQ_COURSE_OPTIONAL_DIR', plugin_dir_path(EDUQ_COURSE_OPTIONAL_FILE__));
function plugin_courses_add_products()
{

  $products = [
    'luisterexamen' => [
      'post_name' => 'luisterexamen',
      'post_title' => 'Luisterexamen + €50,00',
      'post_content' => 'Luisterexamen + €50,00',
      'post_status' => 'publish',
      '_purchase_note' => 'Luisterexamen',
      'post_type' => "product",
      "_price" => '50',
    ],
    'ccv-registratie-voor-code-95' => [
      'post_name' => 'ccv-registratie-voor-code-95',
      'post_title' => 'CCV registratie voor CODE 95 + €24,95',
      'post_content' => 'De CCV registratie van nascholingsuren is voor beroepschauffeurs.',
      'post_status' => 'publish',
      '_purchase_note' => 'CCV registratie voor CODE 95',
      'post_type' => "product",
      "_price" => '24,95',
      "_tax_status" => "none"
    ],
    'licentie-lesmateriaal-ccv' => [
      'post_name' => 'licentie-lesmateriaal-ccv',
      'post_title' => 'Licentie lesmateriaal CCV + €14,95',
      'post_content' => 'Verplicht bij volgen van cursus voor CODE 95.',
      'post_status' => 'publish',
      '_purchase_note' => 'Licentie lesmateriaal CCV',
      'post_type' => "product",
      "_price" => '14,95',
      "_tax_status" => "taxable"
    ],
    'soob-subbsidie-klik-hier' => [
      'post_name' => 'soob-subbsidie-klik-hier',
      'post_title' => 'SOOB-SUBSIDIE?',
      'post_content' => 'Werk jij in de sector transport en logistiek? Wie weet voldoe jij aan de voorwaarden om opleidingsbudget aan te vragen via SOOB. EDUQ Opleidingen kan je daarover adviseren.',
      'post_status' => 'publish',
      "_purchase_note" => 'SOOB Subsidie – bekijk voor mij de mogelijkheden',
      'post_type' => "product",
      "_price" => '0',
    ]

  ];

  $optional_courses = getOptionalCoursesFromWp("slug");

  foreach ($products as $slug => $product) {
    if (empty($optional_courses[$slug])) {
      $post_id = wp_insert_post($product);
      edu_update_post_meta($post_id, $product);
    } else {
      $course = $optional_courses[$slug];
      $post_id = $course->get_ID();
      edu_update_post_meta($post_id, $product);
    }
  }
  updateCourseLocation();
  edu_pages_title_update();
  add_columns_vgg_courses();
  updateCourseDurationInclusive();
  createThankYouPage();
  make_location_column_nullable();
  updateAllCoursesInfo();
  addColumnInCourses();
  createUpdateCourses();

  //    $page_bevestiging = get_page_by_title('bevestiging', '', 'page');
  //    $page_name = $page_bevestiging->post_name;
  //    if($page_name != 'bevestiging'){
  //        createThankYouPage();
  //    }
}

function updateCourseLocation()
{
  global $wpdb;
  $table_name = $wpdb->prefix . "courses";
  $rows = $wpdb->get_results("SELECT * from " . $table_name);
  foreach ($rows as $row) {
    $location = str_replace('Waarderweg 19', "Waarderweg 19,", $row->location);
    $location = preg_replace("/,+/", ",", $location);
    $query = "UPDATE {$table_name} SET location = '$location' WHERE id = %s";
    $wpdb->query($wpdb->prepare($query, $row->id));
  }
}

function updateCourseDurationInclusive()
{
  global $wpdb;
  $table_name = $wpdb->prefix . "courses";
  $rows = $wpdb->get_results("SELECT * from " . $table_name);

  foreach ($rows as $row) {
    $id = $row->id;
    $executable = false;
    $sql = "UPDATE $table_name SET ";
    if (empty($row->duration)) {
      $sql .= " duration = '1 dag ( from 9-17:00)',";
      $executable = true;
    }
    if (empty($row->inclusive)) {
      $sql .= "inclusive = 'lunch, koffie en thee' ";
      $executable = true;
    }
    if ($executable) {
      $sql = rtrim($sql, ",");
      $sql .= "WHERE id = $id";
      $wpdb->query($sql);
    }
  }
}

function edu_update_post_meta($post_id, $product)
{
  wp_update_post(['ID' => $post_id, 'post_title' => $product["post_title"]]);
  if ($product["post_type"] == "product") {
    wp_set_object_terms($post_id, 'simple', 'product_type');
    update_post_meta($post_id, '_price', $product["_price"]);
    update_post_meta($post_id, '_featured', 'yes');
    update_post_meta($post_id, '_sku', $product["post_name"]);

    if (!empty($product["_tax_status"])) {
      update_post_meta($post_id, '_tax_status', $product["_tax_status"]);
    }
    if (!empty($product["_purchase_note"])) {
      update_post_meta($post_id, '_purchase_note', $product["_purchase_note"]);
    }
  }
}

function edu_pages_title_update()
{
  $page_meta = [
    'post_title' => COURSE_DETAIL_LBL['buy_course_title'],
    'post_name' => 'buy-process',
    'post_content' => '[buy_course]',
    'post_status' => 'publish',
    'post_type' => 'page',
    'post_author' => 1
  ];
  $args = [
    "post_type" => $page_meta["post_type"],
    "post_name__in" => [$page_meta["post_name"]],
    'orderby' => 'ID',
    'order' => 'ASC',
  ];
  $pages = get_posts($args);
  if (!empty($pages)) {
    foreach ($pages as $page) {
      $post_id = $page->ID;
      wp_update_post(['ID' => $post_id, 'post_title' => $page_meta["post_title"]]);
    }
  } else {
    foreach ($pages as $page) {
      $post_id = wp_insert_post($page_meta);
    }
  }

  //wp_update_post(['ID' => 5, 'post_title' => "","slug"=>""]);

}
//  date('Y-M-D H:i')
function add_columns_vgg_courses(): void
{
  global $wpdb;
  $table_name = $wpdb->prefix . "courses";
  $result = $wpdb->get_results("SHOW COLUMNS FROM " . $table_name . " where FIELD IN ('inclusive','duration')");
  if (empty($result)) {
    $query = " ALTER TABLE $table_name ";
    $query .= "ADD COLUMN inclusive VARCHAR(200) DEFAULT NULL AFTER location, ";
    $query .= "ADD COLUMN duration VARCHAR(200) DEFAULT NUll AFTER inclusive";
    $wpdb->query($query);
  }
  $result = $wpdb->get_results("SHOW COLUMNS FROM " . $table_name . " where FIELD ='isFullyBooked'");
  if (empty($result)) {
    $query = " ALTER TABLE $table_name ";
    $query .= "ADD COLUMN isFullyBooked Boolean DEFAULT FALSE AFTER duration ";
    $wpdb->query($query);
  }
}

function make_location_column_nullable(): void
{
  global $wpdb;
  $table_name = $wpdb->prefix . "courses";
  $result = $wpdb->get_results("SHOW COLUMNS FROM " . $table_name . " where FIELD ='location'");
  if (!empty($result)) {
    $query = " ALTER TABLE $table_name ";
    $query .= "modify location varchar(150) null";
    $wpdb->query($query);
  }
}


function get_zero_price($price)
{

  return '<span class="woocommerce-Price-amount amount">
            <bdi>' . wc_price($price) . '</bdi>
            </span>
            <small class="tax_label">(incl. 0% btw)</small>';
}

function get_without_tax($total)
{

  return '<span class="woocommerce-Price-amount amount">
            <bdi>' . $total . '</bdi>
            </span> 
            <small class="tax_label">(incl. 0% btw)</small>';
}

global $wpdb;
$table = $wpdb->prefix . "courses";
$results = $wpdb->get_results("select * from " . $table);
foreach ($results as $result) {
  $inclusive = $result->inclusive;
  $duration = $result->duration;
}

function edu_woocommerce_form_field($key, $args, $value = null)
{
  $args["return"] = true;
  $output = str_replace("abbr", "span", woocommerce_form_field($key, $args, $value));
  return $output;
}


function updateExistingAfrekenenPageId()
{
  $post_id = 180;
  wp_update_post(['ID' => $post_id, "post_title" => "Factuurgegevens", 'post_name' => 'factuurgegevens']);
  $post = get_post($post_id);
  return $post;
}

/*
  * Function for post duplication. Dups appear as drafts. User is redirected to the edit screen
  */
function createThankYouPage()
{

  global $wpdb;
  /*
    * get the original post id
    */
  $parent_post = updateExistingAfrekenenPageId();
  //    $query = "select * from wp_posts where post_name = 'bevestiging' && post_parent = $parent_post->ID";
  //    $existing_page = $wpdb->get_results($query);

  $args = ['post_type' => 'page', 'post_parent' => $parent_post->ID, "post_name" => "bevestiging"];

  $existing_page = get_posts($args);

  if (!empty($existing_page)) {
    wp_update_post(['ID' => $existing_page[0]->ID, "post_title" => "Bevestiging"]);
    return true;
  }
  /*
    * and all the original post data then
    */

  /*
    * if you don't want current user to be the new post author,
    * then change next couple of lines to this: $new_post_author = $post->post_author;
    */
  $current_user = wp_get_current_user();
  $new_post_author = $current_user->ID;

  /*
    * if post data exists, create the post duplicate
    */
  if (isset($parent_post) && $parent_post != null) {


    /*
      * new post data array
      */
    $args = [
      'post_name' => 'bevestiging',
      'post_status' => 'publish',
      'post_title' => 'Bevestiging',
      'comment_status' => $parent_post->comment_status,
      'ping_status' => $parent_post->ping_status,
      'post_author' => $new_post_author,
      'post_content' => $parent_post->post_content,
      'post_excerpt' => $parent_post->post_excerpt,
      'post_parent' => $parent_post->ID,
      'post_password' => $parent_post->post_password,
      'post_type' => $parent_post->post_type,
      'to_ping' => $parent_post->to_ping,
      'menu_order' => $parent_post->menu_order
    ];
    $post_id = $parent_post->ID;
    /*
      * insert the post by wp_insert_post() function
      */
    $new_post_id = wp_insert_post($args);
    /*
      * get all current post terms ad set them to the new post draft
      */
    $taxonomies = get_object_taxonomies($parent_post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
    foreach ($taxonomies as $taxonomy) {
      $post_terms = wp_get_object_terms($parent_post->ID, $taxonomy, array('fields' => 'slugs'));
      wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
    }

    /*
      * duplicate all post meta just in two SQL queries
      */
    $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$new_post_id");
    if (count($post_meta_infos) != 0) {
      $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
      foreach ($post_meta_infos as $meta_info) {
        $meta_key = $meta_info->meta_key;
        if ($meta_key == '_wp_old_slug') continue;
        $meta_value = addslashes($meta_info->meta_value);
        $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
      }
      $sql_query .= implode(" UNION ALL ", $sql_query_sel);
      $wpdb->query($sql_query);
    }
  } else {
    wp_die('Post creation failed, could not find original post: ' . $parent_post->ID);
  }
}

// add_action('woocommerce_new_product', 'updateAllCoursesInfo', 10, 1);

// add created_at and updated_at column in courses table
function addColumnInCourses()
{
  global $wpdb;
  $table_name = $wpdb->prefix . "courses";
  $created_at = $wpdb->query("SHOW COLUMNS FROM $table_name LIKE 'created_at';");
  $updated_at = $wpdb->query("SHOW COLUMNS FROM $table_name LIKE 'updated_at';");
  if ((!isset($updated_at) && !isset($created_at)) || (empty($updated_at) && empty($created_at))) {
    $wpdb->query("ALTER TABLE $table_name
    ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;");
  }
}
// create update courses log table 
function createUpdateCourses()
{

  global $wpdb;
  $table_name = $wpdb->prefix . 'update_courses_logs';
  // chack table exite in database
  $istable = $wpdb->query("SHOW TABLES LIKE '$table_name'");
  if (!isset($istable) || empty($istable)) {
    $wpdb->query("CREATE TABLE $table_name (
      id BIGINT NOT NULL AUTO_INCREMENT,
      product_id VARCHAR(50) NOT NULL,
      course_id VARCHAR(50) NOT NULL,
      action VARCHAR(50) NOT NULL,
      logs VARCHAR(250) NOT NULL,
      created_at TIMESTAMP NOT NULL,
      PRIMARY KEY (id)
    );");
  }
}

function updateAllCoursesInfo()
{
  // update_post_meta($post_id, '_eduq_location', 'sd');
  $args = array(
    'post_type'      => 'product',
    'posts_per_page' => 100,
  );
  $loop = new WP_Query($args);
  while ($loop->have_posts()) : $loop->the_post();
    // global $product;
    global $post;
    $post_id = $post->ID;

    $_eduq_location = get_post_meta($post_id, "_eduq_location");
    $_eduq_Cursusduur = get_post_meta($post_id, "_eduq_Inclusief");
    $_eduq_Inclusief = get_post_meta($post_id, "_eduq_Opties");
    $_eduq_Opties = get_post_meta($post_id, "_eduq_Opties");



    if (empty($_eduq_location) || empty($_eduq_location[0])) {
      update_post_meta($post_id, '_eduq_location', "Waarderweg 19, Haarlem (nabij Amsterdam/Halfweg)");
    }
    if (empty($_eduq_Cursusduur) || empty($_eduq_Cursusduur[0])) {
      update_post_meta($post_id, '_eduq_Cursusduur', "1 dag ( from 9-17:00)");
    }
    if (empty($_eduq_Inclusief) || empty($_eduq_Inclusief[0])) {
      update_post_meta($post_id, '_eduq_Inclusief', "lunch, koffie en thee");
    }
    if (empty($_eduq_Opties) || empty($_eduq_Opties[0])) {
      $categories = implode(", ", array_column(get_the_terms($post_id, 'product_cat'), "name"));
      update_post_meta($post_id, '_eduq_Opties', $categories);
    }


  endwhile;

  wp_reset_query();
}

//add_action( 'init', 'plugin_courses_add_products' );

/**
 * Activate the plugin.
 */
function plugin_courses_optional_activate()
{
  // Trigger our function that registers the custom post type plugin.
  plugin_courses_add_products();
  // Clear the permalinks after the post type has been registered.
  flush_rewrite_rules();
}



add_filter('woocommerce_product_data_tabs', 'wk_custom_product_tab', 10, 1);


function wk_custom_product_tab($default_tabs)
{
  $default_tabs['custom_tab'] = array(
    'label'   =>  __('Inofrmation', 'domain'),
    'target'  =>  'wk_custom_tab_data',
    'priority' => 60,
    'class'   => array()
  );
  return $default_tabs;
}

add_action('woocommerce_product_data_panels', 'wk_custom_tab_data');

function wk_custom_tab_data()

{

  echo
  '<div id="wk_custom_tab_data" class="panel woocommerce_options_panel">
      ' . getEduqInformationTab() . '
  
  </div>';
  // updateAllCoursesInfo();
  // global $post;
  // $post_id = $post->ID;
  // print_r($_POST['_eduq_location']."as");
}
function getEduqInformationTab()
{
  define('BASE_PATH', getcwd());
  $file = EDUQ_COURSE_OPTIONAL_DIR . "information.php";
  ob_start();
  include  $file;
  return ob_get_clean();
  wp_die();
}
register_activation_hook(__FILE__, 'plugin_courses_optional_activate');


add_action('woocommerce_new_product', 'eduq_save_custom_courses_meta', 10, 1);
add_action('woocommerce_update_product', 'eduq_save_custom_courses_meta', 10, 1);
//global $post;
//$post_id = $post->ID;
function eduq_save_custom_courses_meta($post_id)
{
  $product = wc_get_product($post_id);
  if ($product->get_type() == 'simple') {
    // echo "<pre>";
    update_post_meta($post_id, "_eduq_location", $_POST['_eduq_location']);
    update_post_meta($post_id, "_eduq_Cursusduur", $_POST['_eduq_Cursusduur']);
    update_post_meta($post_id, "_eduq_Inclusief", $_POST['_eduq_Inclusief']);
    update_post_meta($post_id, "_eduq_Opties", $_POST['_eduq_Opties']);
    // die(print_r($_POST['_eduq_location'], true));
  }
}
