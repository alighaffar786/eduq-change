<?php
function renderStudentInfo($cart_item = [])
{
    list($students_info, $no_of_students) = getFormCursist(true, $cart_item);
    $contact_page = get_permalink(get_page_by_path('contact'));
    $step = ' 
       
        <div class="no_event_hide row student-row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">' . COURSE_DETAIL_LBL['student_panel_heading'] . '</div>
                    <div class="panel-body">
                        <div class="clear"></div>
                        <div class="row">
                            <div id="cusistswrapper">
                                     ' . $students_info . '   
                            </div>
                        </div>
                        <div class="row btn-row" >
                            <div class="col-md-3 col-sm-3 col-lg-3 col-no-margin-right">
                                <button id="add_student" class="btn btn-primary btn-block hidden" type="button">Cursist toevoegen </button>
                            </div>                       
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <div class="no_event_hide total_students_submit_btn row">
        <div class="col-md-12">
            <div class="col-md-6" >
                <div id="submit_subscription_btn">
                    <input type="submit" name="submit" class="btn btn-primary btn-primary-two"  id="submit_subscription" value="' . COURSE_DETAIL_LBL['submit_btn'] . '">
                 </div>
            </div>
            <div class="col-md-6 total_student" >
                <a id="numrirrjeshtave" class="" >Aantal cursisten: <span id="no_of_students">' . $no_of_students . '</span></a>
            </div>
            
      
       </div>
    </div>
    
                 <div class="courseFullyBookedMsg no_event_hide">
                     <label id="courseFullyBookedMsg" >
                        Sorry, op deze datum is de cursus volgeboekt. Selecteer een beschikbare datum of </br> neem  
                        <a id="redirect_btn" href="' . $contact_page . '" >contact</a> met ons op voor de mogelijkheden.
                    </label>

                </div>  
          

<!-- Modal -->
<div class="modal fade" id="editWarning" tabindex="-1" role="dialog"  aria-labelledby="editWarningLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button id="modelClose" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
       <p>' . EDIT_WARNING_TEXT . '</p>
      </div>

    </div>
  </div>
</div>';

    return $step;
}
