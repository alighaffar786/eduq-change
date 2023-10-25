<?php

function renderCourseInfo($event, $eduq_course, $event_id, $current_date)
{

    $dropdown_list = prepareEventSelectList($eduq_course, $event_id);

    list($dd_css_class, $lbl_css_class) = manage_css_classes($eduq_course);
    $panel_css_class = empty($eduq_course) ? 'hidden' : '';
    $contact_page = get_permalink(get_page_by_path('contact'));
    $step = '        
        <div class="row ' . $panel_css_class . '" id="course-dates-panel">
          
          <div class="col-lg-12">
            <div class="panel panel-default calendar-selection">
              <div class="panel-heading" id="locationDatum">LOCATIE EN DATUM</div>
              <div class="panel-body">
              <div id="eventLocation">
                ' . renderEventLocation($event, $eduq_course) . '
                 </div>
                <div class="loader" id="event-loader"></div>    
                <div class="form-group" id="event-group">
                    <label class="control-label" id="date-text" for="first_name">Gewenste datum 
                            <span>*</span></label>
                    <div id="event-detail" >
                        <div class="col-md-6 field_offset_0">
                            <select class="form-control ' . $dd_css_class . '" id="course" name="course" >
                                    ' . $dropdown_list . '
                            </select>
                        </div>
                        <div class="col-md-1 field_offset_0 ' . $dd_css_class . '" id="calendar_picker">
                             <div class="input-group date" id="datetimepicker1">
                                <input type="text" id="event_date" class="form-control hidden" value=' . $current_date . '  />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                             </div>
                        </div>
                    </div>
                    
                </div>
           
              </div>
               
            </div>
             <div class="clear"></div>
                    <label id="no_event" class="bigsacreen-error">
                       Er zijn momenteel geen data bekend voor deze opleiding. </br> Neem 
                        <a id="redirect_btn" href="' . $contact_page . '" >contact</a> met ons op voor de mogelijkheden.
                    </label>
          </div><!-- /.col-lg-7 -->
          <div class="col-lg-4">
          </div><!-- /.col-lg-4 -->
        </div><!-- /.row -->';

    return $step;
}

/**
 * @param $events
 * @return bool
 */
function manage_css_classes($events)
{
    $has_any_event = false;
    foreach ($events as $event) {
        $calendar_date = date("Y-m-d", $event->date);
        if ($calendar_date >= date("Y-m-d")) {
            $has_any_event = true;
            break;
        }
    }
    $lbl_css_class = $has_any_event ? "hidden" : "";
    $dd_css_class = $has_any_event ? "" : "hidden";
    return [$dd_css_class, $lbl_css_class];
}
