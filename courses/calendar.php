<?php

if(!is_eduq_edit_page()){
    include_once(EDUQ_COURSE_PLUGIN_PATH.'_calendar.php');
}
function renderCourseDetailBreadCrumb($product)
{

    return renderCourseDetailStepBar() . '<ul class="breadcrumb">
        <li><a href="/">Home</a></li>
        <li>' . $product->get_title() . '</li>
        </ul>';
}

function renderCourseDetailStepBar()
{
    return
        '<div id="step_bar">
            <div class="row line">
                  <div class="col-xs-3 stepText1">Cursus</div>
                  <div class="col-xs-3 stepText2">Gegevens</div>
                  <div class="col-xs-3 stepText3">Besteloverzicht</div>
                  <div class="col-xs-3 stepText3">Winkelwagen</div>
            </div>
            <div class="row">
              <div class="col-xs-3"><div class="step step1"></div></div>
              <div class="col-xs-3"><div class="step step2"></div></div>
              <div class="col-xs-3"><div class="step step3"></div></div>  
               <div class="col-xs-3"><div class="step step4"></div></div>  
           </div>
    </div>';
}
function is_eduq_edit_page($new_edit = null){
    global $pagenow;
    //make sure we are on the backend
    if (!is_admin()) return false;


    if($new_edit == "edit")
        return in_array( $pagenow, array( 'post.php',  ) );
    elseif($new_edit == "new") //check for new post page
        return in_array( $pagenow, array( 'post-new.php' ) );
    else //check for either new or edit
        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}