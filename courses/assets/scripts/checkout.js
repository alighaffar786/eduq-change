jQuery(document).ready(function () {
  jQuery("#no_event").addClass("hidden");
  courseFullyBooked();
  jQuery("#courseFullyBookedMsg").addClass("hidden");
  courseFullyBookedMessage();
  var cartdataPopup = 0;
  jQuery('#datetimepicker1').click(function () {
    cartdataPopup = 1;
  });
  if (cartdataPopup == 0) {
    cartCheck();
  }

  makeselctTolist();
  let no_of_students = parseInt(jQuery("#no_of_students").html().match(/(\d+)/));
  registerAddRemoveStudent();
  registerDropDownChanges();
  registerOptionalClickEvents();

  /**
   * Need to work on it
   * registerOptionalPriceChange();
   * */
  renderDatePicker(prepareEnabledDates(JSON.parse(events_json_by_date)), JSON.parse(events_json_by_date));
  registerFormSubmitHandler();
  registerclosePopUpHandler();
  jQuery('[data-toggle="tooltip"]').tooltip({html: true});
  for (let i = 1; i <= no_of_students; i++) {
    applyMask(i);
    applyFlag(i);
  }

  jQuery("#courseName").val(jQuery('#courseid').find(":selected").data("title"));


});

function renderDatePicker(enabled_dates, event_groups) {
  var current_date = moment();
  let enabled_dates_filtered = [];
  let past_dates_filtered = [];
  for (let d in enabled_dates) {
    let enable_date = enabled_dates[d];
    let days_diff = enable_date.diff(current_date, "days");
    if (days_diff >= 0) {
      enabled_dates_filtered.push(enable_date);
    } else {
      past_dates_filtered.push(enable_date);
    }

  }
  console.log(past_dates_filtered);

  let picker_options = {
    format: 'yyyy-MM-DD',
    enabledDates: enabled_dates_filtered,
    locale: 'nl'
  };
  if (enabled_dates.length > 0) {
    picker_options["minDate"] = enabled_dates[0].format('yyyy-MM-DD');
  } else {
    picker_options["enabledDates"] = [moment()];
    picker_options["minDate"] = moment();
  }
  // picker_options["beforeShowDay"] = function(date){
  //     return {
  //         classes: 'activeClass'
  //     };
  // }
  jQuery('#datetimepicker1').datetimepicker(picker_options).on("dp.change", function (e) {
    var selected_date = e.date.format('yyyy-MM-DD');
    // console.log(event_groups);
    // console.log(selected_date);
    // console.log(event_groups[selected_date]);

    jQuery("#course").val(event_groups[selected_date]);
    jQuery("#course").trigger("change");
  }).on("dp.show", function (e) {
    console.log("extend", e);
    disabledOldDates(current_date, past_dates_filtered);
    courseFullyBooked();

  }).on("dp.update", function (e) {
    disabledOldDates(current_date, past_dates_filtered);
    courseFullyBooked();

  });

}

function courseFullyBooked() {
  const FullyBooked = events_json_by_date_fullybooked;
  let FullyBookedDates = Object.keys(events_json_by_date_fullybooked);
  FullyBookedDates.forEach(dt => {
    if (FullyBooked[dt] == 1) {
      let moment_dates = moment(dt);
      let day_date_format = moment_dates.format("DD-MM-YYYY");
      let element = `.table-condensed tr td.day[data-day=${day_date_format}]`;
      jQuery(element).addClass("fully-booked").prop('title', "Cursus Vol");
      // jQuery(element)
    }

  });
}

function disabledOldDates(current_date, past_dates_filtered) {
  for (let d in past_dates_filtered) {
    let day_date = past_dates_filtered[d];
    let day_date_format = day_date.format("DD-MM-YYYY");
    let days_diff = day_date.diff(current_date, "days");
    let element = `.table-condensed tr td.day[data-day=${day_date_format}]`;
    console.log("event", element);
    if (days_diff <= 0) {
      // jQuery(this).addClass("past");
      jQuery(element).addClass("past");
    }

  }
  // jQuery(".table-condensed tr td.day:not(.disabled)").each(function(){
  //     let day_arr = jQuery(this).attr("data-day").split("-");
  //     let day = `${day_arr[2]}-${day_arr[1]}-${day_arr[0]}`;
  //     let day_date = moment(day);
  //     let days_diff = day_date.diff(current_date,"days");
  //     if(days_diff<=0){
  //         jQuery(this).addClass("past");
  //     }
  //     // console.log(days);
  // })
}

function registerFormSubmitHandler() {
  /**
   * Submit form
   */
  jQuery('#form_subscription').submit(function () {
    jQuery("form#form_subscription .btn[type=submit]").prop('disabled', true);
    let allow_submit = validateSubmitForm();
    if (!allow_submit) {
      jQuery("#form_error").removeClass("hidden");
      jQuery([document.documentElement, document.body]).animate({
        scrollTop: $("#step_bar").offset().top
      }, 2000);
      jQuery("form#form_subscription .btn[type=submit]").prop('disabled', false);

    } else {
      jQuery("#form_error").addClass("hidden");
    }

    if (allow_submit) {
      addToCartViaSubmit();
    }
    return false;
  });
}

function registerclosePopUpHandler() {
  jQuery("#modelClose").click(function () {
    jQuery('#editWarning').removeClass('showModel');
    jQuery('#editWarning').removeClass('in');
    jQuery('body').removeClass('hidescrool');
  });
}

function validateSubmitForm() {
  let allow_submit = true;
  let error_messages = [];

  if (jQuery("#courseid").val() == '') {
    allow_submit = false;
    error_messages.push(prepare_error_message(error_messages_json['ERRORS']['course_info']))
  }
  if (jQuery("#course").val() == '') {
    allow_submit = false;
    error_messages.push(prepare_error_message(error_messages_json['ERRORS']['course_date_info']));
  }
  jQuery("#cusistswrapper input[type=text]").each(function () {
    var error_lbl_key = $(this).data("error_lbl");
    var field_val = jQuery(this).val();
    var field_index = $(this).data("index");
    var message_cursist_str = '';

    if (field_val == '') {
      allow_submit = false;
      message_cursist_str = error_messages_json['ERRORS'][error_lbl_key].replace("{{index}}", field_index);
      error_messages.push(prepare_error_message(message_cursist_str));
    } else {

      if (error_lbl_key == 'email_cursist' && !validate_email(field_val)) {
        message_cursist_str = error_messages_json['ERRORS'][error_lbl_key + "_valid"].replace("{{index}}", field_index);
        error_messages.push(prepare_error_message(message_cursist_str));
        allow_submit = false;
      }
      if (error_lbl_key == 'phone_cursist' && !validate_phone(field_val)) {
        message_cursist_str = error_messages_json['ERRORS'][error_lbl_key + "_valid"].replace("{{index}}", field_index);
        error_messages.push(prepare_error_message(message_cursist_str));
        allow_submit = false;
      }
      if (error_lbl_key == 'phone_cursist' && validate_zero(field_val)) {
        message_cursist_str = error_messages_json['ERRORS'][error_lbl_key + "_valid1"].replace("{{index}}", field_index);
        error_messages.push(prepare_error_message(message_cursist_str));
        allow_submit = false;
      }
      if (error_lbl_key == 'placeofbrith_cursist' && !validate_text(field_val)) {
        message_cursist_str = error_messages_json['ERRORS'][error_lbl_key + "_valid"].replace("{{index}}", field_index);
        error_messages.push(prepare_error_message(message_cursist_str));
        allow_submit = false;
      }
      if (error_lbl_key == 'firstname_cursist' && !validate_text(field_val)) {
        message_cursist_str = error_messages_json['ERRORS'][error_lbl_key + "_valid"].replace("{{index}}", field_index);
        error_messages.push(prepare_error_message(message_cursist_str));
        allow_submit = false;
      }
      if (error_lbl_key == 'lastname_cursist' && !validate_text(field_val)) {
        message_cursist_str = error_messages_json['ERRORS'][error_lbl_key + "_valid"].replace("{{index}}", field_index);
        error_messages.push(prepare_error_message(message_cursist_str));
        allow_submit = false;
      }
      if (error_lbl_key == 'birtdate_cursist' && !validate_birthdate(field_val)) {
        message_cursist_str = error_messages_json['ERRORS'][error_lbl_key + "_valid"].replace("{{index}}", field_index);
        error_messages.push(prepare_error_message(message_cursist_str));
        allow_submit = false;
      }
    }
  });
  console.log(error_messages);
  if (error_messages.length > 0) {
    jQuery("#buy_process_error").html(error_messages.join(''));
  }
  //------------------
  return allow_submit;
}

function validate_email(email) {
  return email.match(
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  );
}

function validate_phone(phone) {
  return (phone.length == CURSIST_PHONE_LENGTH || phone.length == CURSIST_PHONE_MIN_LENGTH);
}


function validate_zero(phone) {
  var zero_regex = /^0+(?!$)/;
  return zero_regex.test(phone);
}


function validate_text(text) {
  var city_regex = /^\s*[a-zA-Z]{1}[0-9a-zA-Z][0-9a-zA-Z '-.=#]*$/;
  return city_regex.test(text);
}

function validate_birthdate(date) {
  var date_regex = /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[13-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/;
  return date_regex.test(date);
}


//end

function prepare_error_message(m) {
  return "<li id='error_course_date'>" + m + "</li>";
}

function addToCartViaSubmit() {
  let cart_url = jQuery("#cart_url").val();
  // alert(cart_url);
  let form_values = jQFormSerializeArrToJson(jQuery("#form_subscription").serializeArray());
  let no_of_students = parseInt(jQuery("#no_of_students").html().match(/(\d+)/));
  let cart_key = jQuery("#cart_item_key").val();
  let course_id = jQuery("#courseid").val();

  let urls = getUrlVars();
  if (urls['course_id'] > 0 && urls['course_id'] != course_id && urls['cart_course'] != undefined) {
    cart_key = "";
  }

  var model = {
    action: "addToCartViaSubmit",
    "no_of_students": no_of_students,
    "post_id": course_id,
    "cart_item_key": cart_key,
    "form": form_values,
    optional_courses: []
    //"surcharge_price":jQuery("f#extra_price").val(),
    //"surcharge_price_name":jQuery("input[name='optradio']:checked").data("label"),
  };
  jQuery(".optional_courses input:hidden").each(function () {
    console.log(jQuery(this).val());
    if (jQuery(this).val() != '') {
      model.optional_courses.push(jQuery(this).val());
    }
  });
  jQuery("#submit_subscription").val("Toevoegen...");
  console.log(model);
  jQuery.ajax({
    type: "post",
    url: CoursesAjax.ajaxurl,
    data: model,
    success: function (response) {
      console.log(response);
      console.log("redirecting...");
      window.location = cart_url;
    },
    error: function (error) {
      console.log(error);
      window.location = cart_url;
    },
  });
}

function jQFormSerializeArrToJson(formSerializeArr) {
  var jsonObj = {};
  jQuery.map(formSerializeArr, function (n, i) {
    jsonObj[n.name] = n.value;
  });
  return jsonObj;
}

function prepareEnabledDates(event_groups) {
  event_dates = Object.keys(event_groups);
  // alert(event_date);
  var moment_dates = [];
  for (i in event_dates) {
    var date_val = event_dates[i];
    moment_dates.push(moment(date_val));
  }
  return moment_dates;
}

function registerAddRemoveStudent() {
  let no_of_students = parseInt(jQuery("#no_of_students").html().match(/(\d+)/));
  let parent_id = jQuery("#courseid").val() == '' ? '0' : jQuery("#courseid").val();

  manageStudentRemoveLink();
  manageClearStudentFields();

  jQuery(document).on("click", ".lp_add_student", function (e) {
    addStudent(e);
  });

  jQuery(document).on("click", ".lp_clear_student_fields", function () {
    jQuery("input[name=firstname_cursist1]").val('');
    jQuery("input[name=lastname_cursist1]").val('');
    jQuery("input[name=email_cursist1]").val('');
    jQuery("input[name=phone_cursist1]").val('');
    jQuery("input[name=placeofbrith_cursist1]").val('');
    jQuery("input[name=birtdate_cursist1]").val('');
    manageClearStudentFields();
    enableAddStudentButton();
  });

  jQuery(document).on("click", '.lp_remove_student', function () {
    parent_id = jQuery("#courseid").val() == '' ? '0' : jQuery("#courseid").val();

    let index = $(this).data("index");
    let elem_id = `#cursist${index}`;
    jQuery(elem_id).remove();
    let no_of_students = calucateNoOfStudents();
    if (no_of_students > 0) {
      jQuery(".cursist").each(function (i) {
        let new_index = i + 1;
        updateStudentsPanelMarkUp($(this), new_index);
      });

    }
    console.log('P', parent_id);
    calculate_data_price(parent_id);
    manageStudentRemoveLink();
    manageClearStudentFields();
  });

}

function manageStudentRemoveLink() {
  let no_of_students = parseInt(jQuery("#no_of_students").html().match(/(\d+)/));
  console.log('no ', no_of_students);
  if (no_of_students == 1) {
    jQuery("#remove_student_1").hide();
  } else {
    jQuery("#remove_student_1").show();
  }
}

function manageClearStudentFields() {
  let no_of_students = parseInt(jQuery("#no_of_students").html().match(/(\d+)/));
  if (no_of_students == 1) {
    jQuery(".lp_clear_student_fields").show();
  } else {
    jQuery(".lp_clear_student_fields").hide();
  }
}


function updateStudentsPanelMarkUp(elem, i) {
  elem.attr("id", `cursist${i}`);
  elem.find(".formsubheader").html(`${COURSE_STUDENTS_LBL.title} ${i}`);
  elem.find(".lp_remove_student").attr("id", `remove_student_${i}`);
  elem.find(".lp_remove_student").attr("data-index", `${i}`);
  elem.find(".lp_clear_student_fields").attr("data-index", `${i}`);

  $.each(COURSE_STUDENTS_LBL.fields, function (index, value) {
    let field_elem = elem.find(`.${value}`);
    field_elem.attr("name", `${value}${i}`);
    field_elem.attr("data-index", `${i}`);
  });
}

function calucateNoOfStudents() {

  var no_of_students = jQuery(".cursist").length;
  jQuery("#no_of_students").html(no_of_students);
  return no_of_students;
}

function addStudent(e) {
  //disableAddStudentButton(e);

  e.stopPropagation();
  e.preventDefault();


  let number_of_students = jQuery(".cursist").length + 1;
  let total_students = jQuery(".cursist").length;
  let parent_id = jQuery("#courseid").val() == '' ? '0' : jQuery("#courseid").val();
  jQuery.ajax({
    type: "post",
    url: CoursesAjax.ajaxurl,
    data: {action: "getFormCursist", number: number_of_students, noCursist: total_students},
    success: function (response) {
      /*
      min_height = min_height + (constant_increment * (total_students));
      jQuery(".wizard>.content").css("min-height",`${min_height}em`);
      jQuery(".wizard>.content").attr("data-min-height",`${min_height}`);

      */
      jQuery("#cusistswrapper").append(response);
      var no_of_students = jQuery(".cursist").length;
      jQuery("#no_of_students").html(no_of_students);
      jQuery("#noCursists").val(no_of_students);
      jQuery("#remove_student").prop('disabled', false);
      enableAddStudentButton(e);
      applyMask(no_of_students);
      applyFlag(no_of_students);
      calculate_data_price(parent_id);
      manageStudentRemoveLink();
      manageClearStudentFields();

    },
    error: function (error) {
      console.log("--error");
      console.log(error);
      jQuery(e.currentTarget).prop('disabled', false);
    },
  });
}

function disableAddStudentButton(e) {
  if (e != undefined) {
    jQuery(e.currentTarget).prop('disabled', true);
  }
}

function enableAddStudentButton(e) {
  if (e != undefined) {
    jQuery(e.currentTarget).prop('disabled', false);
  }
}

function applyMask(current_student) {
  let element_id = `#cursist${current_student}`;
  jQuery(`${element_id} .birtdate_cursist`).mask("99/99/9999", {autoclear: false});
}

function applyFlag(current_student) {
  let element_id = `#cursist${current_student}`;
  window.intlTelInput(document.querySelector(`${element_id} .phone_cursist`), {
    utilsScript: UtilsJs,
  });
}


function removeDialCode(student_id) {
  var phone_number = document.getElementsByName(`phone_cursist${student_id}`)[0].value;
  console.log(phone_number);
  let first_three = phone_number.substring(0, 3);
  if (first_three === '+31') {
    let phone_number_updated = phone_number.substring(3);
    document.getElementsByName(`phone_cursist${student_id}`)[0].value = phone_number_updated;
  } else {
    let phone_number_updated = makeNineDigit(phone_number);
    document.getElementsByName(`phone_cursist${student_id}`)[0].value = phone_number_updated;
  }
}

function makeNineDigit(phone_number) {
  let updated_phone_number = phone_number;
  if (phone_number.substring(0, 3) == '010' && phone_number.length == CURSIST_PHONE_LENGTH) {
    updated_phone_number = phone_number.substr(1, phone_number.length);
  } else if (phone_number.substring(0, 3) == '016' && phone_number.length >= CURSIST_PHONE_LENGTH) {
    updated_phone_number = phone_number.substr(2, phone_number.length);
    console.log('phone', phone_number, updated_phone_number);
  } else if (phone_number.substring(0, 2) == '16' && phone_number.length >= CURSIST_PHONE_LENGTH
  ) {
    updated_phone_number = phone_number.substr(1, phone_number.length);
    console.log('phone', phone_number, updated_phone_number);
  } else if (phone_number.substring(0, 2) == '06' && phone_number.length >= CURSIST_PHONE_LENGTH
  ) {
    updated_phone_number = phone_number.substr(1, phone_number.length);
    console.log('phone', phone_number, updated_phone_number);
  } else if (phone_number.substring(0, 1) == '0' && phone_number.length == CURSIST_PHONE_LENGTH) {
    updated_phone_number = phone_number.substr(1, phone_number.length);
    console.log('phone', phone_number, updated_phone_number);
  }
  return updated_phone_number;
}


function registerDropDownChanges() {
  jQuery("#courseid").change(function () {


    let has_event = jQuery(this).data('has-event');
    //sidebar
    renderSideBar(has_event);
    jQuery("#courseName").val(jQuery('#courseid').find(":selected").data("title"));
    populateEventDropdown(jQuery(this).val());
    updateFormUrl('course_id', jQuery(this).val());
    updateCartUrl(jQuery(this).val());
    //optional courses
    renderOptionalCourses(this);
    courseFullyBooked();
  });
  jQuery("#course").change(function () {
    courseFullyBooked();
    courseFullyBookedMessage();

    jQuery("#location_text").html(jQuery('#course :selected').data("location"));

    updateFormUrl('event_id', jQuery(this).val());
  });
}

function registerOptionalPriceChange() {
  jQuery("input[name='optradio']").change(function () {
    if (jQuery(this).val() == 'code-95') {
      jQuery(".extra_price_row").removeClass("hidden");
      let price = jQuery(this).data("price")
      let price_label = '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">€</span>' + price + '</bdi></span>';
      jQuery('#cart_extra_price').html(price_label);
      jQuery('#cart_extra_price_label').html(jQuery(this).data("label"));
      jQuery("#extra_price").val(price);

    } else {
      jQuery(".extra_price_row").addClass("hidden");
      jQuery('#cart_extra_price').html("");
      jQuery('#cart_extra_price_label').html("");
      jQuery("#extra_price").val(0);
    }
  });
}

function populateEventDropdown(id) {
  courseFullyBooked();
  jQuery("#no_event").addClass("hidden");
  jQuery('.smallsacreen-error').addClass("hidden");
  let no_of_students = parseInt(jQuery("#no_of_students").html().match(/(\d+)/));
  let previous_course = jQuery("#previous_course").val();
  registerNoCourseSelected();
  jQuery("#event-loader").show();
  let has_event = jQuery('#courseid').find(":selected").data('has-event');
  if (has_event == 0) {
    jQuery(".calendar-selection").addClass("hidden");
    jQuery("#no_event").removeClass("hidden");
    jQuery(".smallsacreen-error").removeClass("hidden");

    var cart_count = $(".cart-icon-num").html();
    if (cart_count == 0) {
      jQuery('#course_sidebar').hide();
    }
    jQuery('.no_event_hide').hide();
    jQuery('.submit_btn_for_mobile').hide();
    jQuery('#optional_course').hide();


  } else {
    jQuery('#optional_course').show();
    jQuery('#course_sidebar').show();
    jQuery('.no_event_hide').show();
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
      jQuery('.submit_btn_for_mobile').show();
    } else {
      jQuery('.submit_btn_for_mobile').hide();
    }
    jQuery(".calendar-selection").removeClass("hidden");
    let cart_course_check = getUrlVars()['cart_course']
    let course_id = jQuery(".row.extra_price_row.extra_price_current").attr("parent_id");
    if(typeof (cart_course_check !=undefined) && cart_course_check==1 && course_id !=id){
      previous_course =0;
    }
    jQuery.ajax({
      type: "post",
      url: CoursesAjax.ajaxurl,
      dataType: "json",
      data: {
        action: "getCoursesEventDropDown",
        post_id: id,
        no_of_students: no_of_students,
        previous_course: previous_course
      },
      success: function (response) {


        jQuery("#course").html(response["list"]);
        jQuery("#optional_course").html(response["optional_courses"]);
        jQuery("#course_sidebar").html(response["sidebar"]);
        var fullybookedDate = response["fullybookedDate"];
        var dateArray = [];
        fullybookedDate.forEach(value => {
          if (value['isFullyBooked'] == 1) {


            var date = new Date(value['date'] * 1000).toISOString();
            dateArray.push(date);
          }
        })
        events_json_by_date_fullybooked = {};

        for (var i = 0; i < dateArray.length; i++) {
          events_json_by_date_fullybooked[dateArray[i]] = "1";
        }

        if (jQuery.trim(response["optional_courses"]) != '') {
          registerOptionalClickEvents();
        }
        // if (jQuery.trim(response["sidebar"]) != '') {
        //     jQuery('[data-toggle="tooltip"]').tooltip({html: true});
        // }
        var calendar_dates = [];
        var event_groups = {};


        jQuery("#course > option").each(function () {
          if (jQuery(this).attr("data-date") != undefined) {
            let event_date = jQuery(this).attr("data-date");

            event_groups[event_date] = jQuery(this).val();

            calendar_dates.push(moment(event_date));
          }
        });

        var pos = response["list"].search("data-location");
        if (pos == -1) {

          jQuery("#course").addClass("hidden");
          jQuery("#calendar_picker").addClass("hidden");
          jQuery("#eventLocation").addClass("hidden");
          jQuery(".control-label").addClass("hidden");

          jQuery("#no_event").removeClass("hidden");
          jQuery(".smallsacreen-error").removeClass("hidden");


        } else {


          jQuery("#course").removeClass("hidden");
          jQuery("#calendar_picker").removeClass("hidden");
          jQuery("#eventLocation").removeClass("hidden");
          jQuery(".control-label").removeClass("hidden");


          jQuery("#no_event").addClass("hidden");
          jQuery(".smallsacreen-error").addClass("hidden");

          jQuery('#datetimepicker1').datetimepicker("destroy");

          renderDatePicker(calendar_dates, event_groups);


        }

        jQuery("#event-loader").hide();
        calculate_data_price(id);
      },
      error: function (error) {
        jQuery("#event-loader").hide();
      },
    });
  }
}

function updateFormUrl(param_name, param_val) {
  let url = jQuery("#form_subscription").attr("action");
  url = removeURLParameter(url, param_name);
  url += "&" + param_name + "=" + param_val;

  jQuery("#form_subscription").attr("action", url);
}

function updateCartUrl(course_id) {
  let url = jQuery("#cart_url").val();
  url = removeURLParameter(url, "course_id");
  url += "?course_id=" + course_id;
  jQuery("#cart_url").val(url);
}

function removeURLParameter(url, parameter) {
  //prefer to use l.search if you have a location/link object
  var urlparts = url.split('?');
  if (urlparts.length >= 2) {

    var prefix = encodeURIComponent(parameter) + '=';
    var pars = urlparts[1].split(/[&;]/g);

    //reverse iteration as may be destructive
    for (var i = pars.length; i-- > 0;) {
      //idiom for string.startsWith
      if (pars[i].lastIndexOf(prefix, 0) !== -1) {
        pars.splice(i, 1);
      }
    }

    return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
  }
  return url;
}

function renderSideBar(has_event) {

  if (has_event) {
    jQuery("#course_title").html(jQuery('#courseid :selected').data("title"));
    jQuery("#cart_price").html(jQuery('#courseid :selected').data("price"));
    jQuery("#cart_total").html(jQuery('#courseid :selected').data("price"));
  } else {
    var course_id = jQuery(".row.extra_price_row.extra_price_current").attr("parent_id");
    var course = jQuery(`[parent_id='${course_id}']`);
    jQuery(course).remove();
    calculate_data_price(0);
  }
}

function renderOptionalCourses(obj) {

  let has_event = jQuery(obj).data('has-event');
  if (has_event) {
    if (typeof (COURSES_SELECTION_CRITERIA[jQuery(obj).val()]) != "undefined") {


      jQuery(".optional_courses").addClass("hidden");
      jQuery('.optional_courses input').prop("checked", false);
      jQuery(".extra_price_row").addClass("hidden");

      let optional_courses = COURSES_SELECTION_CRITERIA[jQuery(obj).val()];
      let course_checked = COURSE_CHECKED_CRITERIA[jQuery(obj).val()];

      for (let c in optional_courses) {
        let option_course = optional_courses[c];
        // console.log(option_course);
        jQuery(`#course_${option_course}`).removeClass("hidden");
        //
        if (typeof (course_checked) != "undefined") {
          jQuery('.optional_courses input:checkbox').prop("checked", true);
        }
      }
    } else {

      jQuery(".optional_courses").addClass("hidden");
      jQuery('.optional_courses input:checkbox').prop("checked", false);
      jQuery(".extra_price_row").addClass("hidden");
    }
  }

}

function registerOptionalClickEvents() {

  let parent_id = jQuery("#courseid").val() == '' ? '0' : jQuery("#courseid").val();
  jQuery('.optional_courses input:checkbox').click(function () {
    let selected_optional_course = jQuery(this).attr('name');
    if (jQuery(this).is(':checked')) {
      console.log("selected");
      jQuery(`#sidebar_${selected_optional_course}_${parent_id}`).removeClass("hidden");
      jQuery(`input:hidden[id=${selected_optional_course}]`).val(jQuery(this).val());
    } else {
      jQuery(`#sidebar_${selected_optional_course}_${parent_id}`).addClass("hidden");
      jQuery(`input:hidden[id=${selected_optional_course}]`).val('');
    }
    calculate_data_price(parent_id);
  });
  // auto load the courses

  jQuery("#courseName").val(jQuery('#courseid').find(":selected").data("title"));
  jQuery('.optional_courses input:checkbox').each(function () {
    let selected_optional_course = jQuery(this).attr('name');
    if (jQuery(this).is(':checked')) {
      jQuery(`#sidebar_${selected_optional_course}_${parent_id}`).removeClass("hidden");
      jQuery(`input:hidden[id=${selected_optional_course}]`).val(jQuery(this).val());
    } else {
      jQuery(`#sidebar_${selected_optional_course}_${parent_id}`).addClass("hidden");
      jQuery(`input:hidden[id=${selected_optional_course}]`).val('');
    }

    calculate_data_price(parent_id);
    mandatoryCheckbox();
  });
}

function calculate_data_price(parent_id) {

  var total_price = 0;
  let no_of_students = parseInt(jQuery("#no_of_students").html().match(/(\d+)/));

  let replace_str = '<span class="woocommerce-Price-currencySymbol">€</span>';
  // tax_amount = price - ( price / ( (21 / 100 ) + 1 ) )
  let tax_amount = 0;
  let i = 0;
  jQuery("div.extra_price_row:not(.hidden) div.data-price-calculate bdi").each(function () {
    //  let object = jQuery(this).parent().parent(".product_price");
    let object = jQuery(this).parent().parent(".product_price");
    // let cart_quantity = jQuery(object).data('cart-quantity');
    let child_course_id = jQuery(object).data('course-id');
    // if(!has_event){
    //     object.data("price") = "€0";
    // }
    // alert(object.data("price"));
    let price_data = object.data("price").replace(replace_str, "").replace("€", "");
    let tax_rate = object.data("tax-rate");


    let price_with_students = 0;

    let course_price = parseFloat(price_data) * parseInt(no_of_students);
    const cart_quantity = jQuery(object).data('cart-quantity');
    if (typeof (cart_quantity) != "undefined" && cart_quantity != 0) {
      console.log("inside undefined if condition", i++, cart_quantity,);
      course_price = parseFloat(price_data) * parseInt(cart_quantity);

      price_with_students = parseFloat(price_data) * parseInt(parseInt(cart_quantity));
      if (tax_rate > 0) {
        tax_amount = tax_amount + (price_with_students - (price_with_students / ((tax_rate / 100) + 1)))
      }
    } else {
      price_with_students = parseFloat(price_data) * parseInt(no_of_students);
      if (tax_rate > 0) {
        tax_amount = tax_amount + (price_with_students - (price_with_students / ((tax_rate / 100) + 1)))
      }
    }


    let price_html = `<span class="woocommerce-Price-currencySymbol">€</span>${String(price_with_students.toFixed(2)).replace('.', ',')}`;
    // alert();
    //jQuery(`div.extra_price_current div.data-price-calculate bdi`).html(price_html);
    if (child_course_id != undefined && parent_id != 0) {
      //product_price
      jQuery(`div.extra_price_row:not(.hidden) div.data-price-calculate[child_id=${child_course_id}] .bdi_${parent_id}`).html(price_html);
    }


    total_price = (total_price + course_price);
    // alert(total_price);
    console.log("total_price", parent_id, child_course_id, price_data, total_price, cart_quantity, no_of_students);

  });
  // only current parent course price calculation
  jQuery(`div[parent_id=${parent_id}].extra_price_row div.data-price-calculate bdi`).each(function () {
    let object = jQuery(this).parent().parent(".product_price");
    let price_data = object.data("price").replace(replace_str, "").replace("€", "");
    let price_with_students = parseFloat(price_data) * no_of_students;
    let price_html = `<span class="woocommerce-Price-currencySymbol">€</span>${String(price_with_students.toFixed(2)).replace('.', ',')}`;
    jQuery(this).html(price_html);
  });

  jQuery(`div[parent_id=${parent_id}].extra_price_row div.course_label strong.product-quantity`).each(function () {
    let not_students_html = `×${no_of_students}`;
    jQuery(this).html(not_students_html);
  });

  //total_price = no_of_students * total_price;

  total_price = String(total_price.toFixed(2)).replace(".", ",");
  tax_amount = String(tax_amount.toFixed(2)).replace(".", ",");
  let total_html = `<span class='woocommerce-Price-currencySymbol'>€</span>${total_price}`;
  let tax_amount_html = `<small class="includes_tax">(incl. <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">€</span>${tax_amount}</span> btw)</small>`

  //calculate grand total
  jQuery("span#cart_total bdi").html(total_html);
  jQuery("span#cart_tax").html(tax_amount_html);
}

function registerNoCourseSelected() {
  let selected_course = jQuery('#courseid').find(":selected").val();
  //console.log(selected_course);
  if (selected_course == '') {
    jQuery("#course-dates-panel").addClass("hidden");
  } else {
    jQuery("#course-dates-panel").removeClass("hidden");
  }
}

function mandatoryCheckbox() {
  let parent_id = jQuery("#courseid").val() == '' ? '0' : jQuery("#courseid").val();
  let courseName = jQuery('#courseName').val();
  // if (courseName.match(/^CODE 95:/)) {
  jQuery(".optional_courses input[name = 'ccv-registratie-voor-code-95']").click(function () {
    if (jQuery("input[name = 'ccv-registratie-voor-code-95']").is(':checked')) {
      jQuery("input[name = 'licentie-lesmateriaal-ccv']").prop("checked", true);
      jQuery(`#sidebar_licentie-lesmateriaal-ccv_${parent_id}`).removeClass("hidden");
      calculate_data_price(parent_id);
      jQuery(`input:hidden[id='licentie-lesmateriaal-ccv']`).val(jQuery("input[name = 'licentie-lesmateriaal-ccv']").val());
    } else {
      jQuery("input[name = 'licentie-lesmateriaal-ccv']").prop("checked", false);
      jQuery(`#sidebar_licentie-lesmateriaal-ccv_${parent_id}`).addClass("hidden");
      jQuery(`input:hidden[id='licentie-lesmateriaal-ccv']`).val('');
      calculate_data_price(parent_id);
    }
  })
  jQuery(".optional_courses input[name = 'licentie-lesmateriaal-ccv']").click(function () {
    if (jQuery("input[name = 'licentie-lesmateriaal-ccv']").is(':checked')) {
      jQuery("input[name = 'ccv-registratie-voor-code-95']").prop("checked", true);
      jQuery(`#sidebar_ccv-registratie-voor-code-95_${parent_id}`).removeClass("hidden");
      calculate_data_price(parent_id);
      jQuery(`input:hidden[id='ccv-registratie-voor-code-95']`).val(jQuery("input[name = 'ccv-registratie-voor-code-95']").val());
    } else {
      jQuery("input[name = 'ccv-registratie-voor-code-95']").prop("checked", false);
      jQuery(`#sidebar_ccv-registratie-voor-code-95_${parent_id}`).addClass("hidden");
      jQuery(`input:hidden[id='ccv-registratie-voor-code-95']`).val('');
      calculate_data_price(parent_id);
    }

  })
}

// to hide mini calender on click
jQuery(document).mouseup(function (e) {
  var calendar = $(".bootstrap-datetimepicker-widget.dropdown-menu");
  if (!calendar.is(e.target) && calendar.has(e.target).length === 0) {
    calendar.hide();
  }
});

function courseFullyBookedMessage() {
  let isFullyBooked = jQuery('#course').find(":selected").data("isfullybooked");
  let htmlCheck = jQuery('#course :selected').html();
  if(htmlCheck == "--Select--" || htmlCheck == undefined ){
    // alert()
    jQuery("#location_text").html(" ");
  }
  // --Select--
  if (isFullyBooked == 1) {
    jQuery(".sidebar_btn_row").addClass("hidden");
    jQuery(".student-row").addClass("hidden");
    jQuery(".total_students_submit_btn").addClass("hidden");
    jQuery(".submit_btn_for_mobile").addClass("hidden");
    jQuery("#courseFullyBookedMsg").removeClass("hidden");
  } else {
    jQuery(".sidebar_btn_row").removeClass("hidden");
    jQuery(".student-row").removeClass("hidden");
    jQuery(".total_students_submit_btn").removeClass("hidden");
    jQuery(".submit_btn_for_mobile").removeClass("hidden");
    jQuery("#courseFullyBookedMsg").addClass("hidden");
  }
}

// get current url
function getUrlVars() {
  var vars = [], hash;
  var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
  for (var i = 0; i < hashes.length; i++) {
    hash = hashes[i].split('=');
    vars.push(hash[0]);
    vars[hash[0]] = hash[1];
  }
  return vars;
}

// show model when course is already in cart
function cartCheck() {
  // alert(getUrlVars()["cart_course"]);

  let urlVars = getUrlVars();
  if (urlVars["cart_course"] == 1 && (urlVars["removed_item"] == undefined)) {
    showEditWarning();
  }

}

function showEditWarning() {
  jQuery('#editWarning').addClass('showModel');
  jQuery('#editWarning').addClass('in');
  jQuery('body').addClass("hidescrool");
}

function makeselctTolist() {
  jQuery("#courseid").select2();
  jQuery("#course").select2();
  // auto sacrool top
  jQuery('#courseid').on('select2:open', () => {
    setTimeout(() => {
      jQuery('.select2-results__options').scrollTop(0);
    }, 10);
  });
  jQuery('#course').on('select2:open', () => {
    setTimeout(() => {
      jQuery('.select2-results__options').scrollTop(0);
      jQuery("li.select2-results__option:contains(Cursus Vol)").css("color", "#F57D30");
    }, 10);

  });
}