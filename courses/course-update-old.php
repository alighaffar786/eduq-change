<?php

function course_update() {
    global $wpdb;
    $table_name = $wpdb->prefix . "courses";
    $id = $_GET["id"];
    $location = $_POST["location"];
	$date = strtotime($_POST["date"]);
//update
    if (isset($_POST['update'])) {
        $wpdb->update(
                $table_name, //table
                array('location' => $location,'date' => $date), //data
                array('ID' => $id), //where
                array('%s','%s'), //data format
                array('%s') //where format
        );
    }
//delete
    else if (isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
    } else {//selecting value to update	
        
		
		
		
		$courses = $wpdb->get_results($wpdb->prepare("SELECT id,location,date,course from $table_name where id=%s", $id));
        foreach ($courses as $c) {
            $location = $c->location;
			$date = date('Y-m-d',$c->date);
			$course = $c->course;
        }
		
		$args = array( 'post_type' => 'product', 'posts_per_page' => -1 );  
        $loop = new WP_Query( $args );
		$list ='';
        while ( $loop->have_posts() ) : $loop->the_post(); 
			global $product; 
			$selected = "";
			if($loop->post->ID == $course ){
				$selected = "SELECTED";
			}
			$list = $list.'<option '.$selected.' value="'.$loop->post->ID.'">'.$loop->post->post_title.'</option>';
		endwhile;
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/courses/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>VCA Training</h2>

        <?php if ($_POST['delete']) { ?>
            <div class="updated"><p>Training verwijderen</p></div>
            <a href="<?php echo admin_url('admin.php?page=course_list') ?>">&laquo; Trainingsoverzicht</a>

        <?php } else if ($_POST['update']) { ?>
            <div class="updated"><p>Training bijwerken</p></div>
            <a href="<?php echo admin_url('admin.php?page=course_list') ?>">&laquo; Trainingsoverzicht</a>

        <?php } else { ?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <table class='wp-list-table widefat fixed'>
                    <tr><th>Locatie</th><td><input type="text" name="location" value="<?php echo $location; ?>"/></td></tr>
                    <tr><th>Product</th><td><select SELECTED="<?=$course?>" name="course"><?=$list?></select></td></tr>
					<tr><th>Datum</th><td><input type="date" id="datepicker" name="date" value="<?=$date?>"/></td></tr>
                </table>
                <input type='submit' name="update" value='Save' class='button'> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Weet je het zeker dat je deze training wilt verwijderen?')">
            </form>
        <?php } ?>

    </div>
    <?php
}
