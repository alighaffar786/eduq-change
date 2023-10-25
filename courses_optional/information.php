<?php
global $post;
$post_id = $post->ID;
$eduq_location = get_post_meta($post_id, "_eduq_location");
$eduq_Cursusduur = get_post_meta($post_id, "_eduq_Cursusduur");
$eduq_Inclusief = get_post_meta($post_id, "_eduq_Inclusief");
$eduq_Opties = get_post_meta($post_id, "_eduq_Opties");
// die($product_cats_ids);
?>
<!-- $product_cats_ids = wc_get_product_term_ids($post_id, 'product_cat');
      die($product_cats_ids);  -->
<p class="form-field _eduq_location_field ">
    <label for="_eduq_location">Locatie:</label>
    <input type="text" class="short" value="<?php echo $eduq_location[0] ?>" name="_eduq_location" id="_eduq_location" placeholder="Locatie">
</p>

<p class="form-field _eduq_Cursusduur_field ">
    <label for="_eduq_cursusduur">Cursusduur:</label>
    <input type="text" class="short" value="<?php echo $eduq_Cursusduur[0] ?>" name="_eduq_Cursusduur" id="_eduq_Cursusduur" placeholder="Cursusduur">
</p>


<p class="form-field _eduq_Inclusief_field ">
    <label for="_eduq_Inclusief">Inclusief:</label>
    <input type="text" class="short" value="<?php echo $eduq_Inclusief[0] ?>" name="_eduq_Inclusief" id="_eduq_Inclusief" placeholder="Inclusief">
</p>
<p class="form-field _eduq_Opties_field ">
    <label for="_eduq_Opties">Opties:</label>
    <input type="text" class="short" value="<?php echo $eduq_Opties[0] ?>" name="_eduq_Opties" id="_eduq_Opties" placeholder="Opties">
</p>
<?php
// updateAllCoursesInfo();
?>
<!-- retrive -->