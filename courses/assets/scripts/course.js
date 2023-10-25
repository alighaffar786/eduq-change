var $calendar;
jQuery(document).ready(function () {
  console.log("----Ready----------");
  //jQuery("h1.vc_custom_heading").hide();
  if (jQuery("#course_availability").val() == 1) {
    populateCalendar();
  }
});

function populateCalendar() {
  let calendar_events = JSON.parse(CalendarOptions.events);
  let course_available = jQuery(".course-available").data("course-availability");
  // if(calendar_events.length>0){
  if (course_available == 1) {
    renderCalendar(calendar_events);
    adjustCalendarHeight();
  }
}

function renderCalendar(calendar_events) {
  let events = formatEvents(calendar_events);
  // console.log("event-", events);
  let container = jQuery("#edu_calendar2").simpleCalendar({
    fixedStartDay: 0, // begin weeks by sunday
    disableEmptyDetails: true,
    events: events,
    onDateSelect: function (date, events) {
      // console.log('dsfdsa', events);
      if (events.length > 0) {
        let event = events[0];
     
        jQuery("#redirect_btn").attr("href", event.redirect);
        if (event.isFullyBooked == 1) {
          // isFullyBookedhtml
          jQuery(".event").hide();
          jQuery(".event-wrapper").html(event.isFullyBookedhtml);
          console.log("sdsds", event.isFullyBookedhtml);
        } else {
          jQuery(".courseFullyBooked").hide();
        }
      }
      if (jQuery(".event-summary .avaialblle-0").length > 0) {
        jQuery(".calendar_sect_main").removeClass("calendar-1");
        jQuery(".calendar_sect_main").addClass("calendar-0");
      } else {
        jQuery(".calendar_sect_main").addClass("calendar-1");
        jQuery(".calendar_sect_main").removeClass("calendar-0");
      }
    },
    onEventSelect: function () {
      let event = $(this).data('event');
      $("#course_id").val(event.course_id);
      let course_desc = [event.startDate, event.summary].join(' ');
    },
    onMonthChange: function (month, year) {
      console.log("change month");
      adjustCalendarHeight();
    }
  });
  $calendar = container.data('plugin_simpleCalendar');
}

function formatEvents(events) {
  // alert("here")
  var options = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'};
  let formated_events = [];
  for (i in events) {
    var event = events[i];
    let event_date = new Date(event.startDate);
    var event_date_string = new Date(event.startDate).toLocaleDateString("en-US", options);
    let course_desc = [event.startDate, event.summary].join(' ');
    let redirect = event.redirect;
    let summary = "";
    let summary_arr = "";
    if (event.summary) {
      summary_arr = event.summary.split("(");
      summary = summary_arr[0];
      if (summary_arr.length > 1) {
        summary += "</br>(" + summary_arr[1];
      }
      summary = summary_arr.length > 1 ? summary_arr[0] + "</br>(" + summary_arr[1] : summary_arr[0];
    } else {
      summary_arr = [];
      summary = "";
    }
    let href = "<a href='" + redirect + "'>Inschrijven</a>";
    let today_date = new Date;
    let model_css_class = 'avaialblle-1';

    if (event_date < today_date) {
      href = "<p class='red'>Deze cursusdatum is voorbij\n</p>";
      model_css_class = 'avaialblle-0';
    }

    model = {
      startDate: event_date_string,
      endDate: event_date_string,
      location: event.location,
      inclusive: event.inclusive,
      duration: event.duration,
      isFullyBooked: event.isFullyBooked,
      isFullyBookedhtml: event.isFullyBookedhtml,
      summary: "<p class='" + model_css_class + "'>" + summary + "</p><div class='clear'></div>" + href,
      event_id: event.event_id,
      redirect: redirect,
      course_id: event.course_id,
    }
    formated_events.push(model);
  }
  return formated_events;
}


function validateStep2() {
  let input_data = [jQuery("#email").val()];
  let is_error = false;
  if (jQuery("#iscompany").val() == 1) {
    input_data.push(jQuery("#companyname").val());
    input_data = input_data.filter(function (el) {
      return el != '';
    });
    if (input_data.length < 2) {
      is_error = true;
    }

  } else {
    input_data.push(jQuery("#firstname").val());
    input_data.push(jQuery("#lastname").val());
    input_data = input_data.filter(function (el) {
      return el != '';
    });
    if (input_data.length < 3) {
      is_error = true;
    }
  }

  if (is_error) {
    jQuery("#personal_info").show();
  } else {
    jQuery("#personal_info").hide();
  }
  return !is_error;
}

function adjustCalendarHeight() {
  let isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
  if (!isMobile) {
    let left_cal_height = jQuery(".calendar_left_sect").height();
    let right_cal_height = jQuery(".calendar_right_sect").height();
    jQuery(".calendar_right_sect").css("height", `${left_cal_height}px`);
  }
}