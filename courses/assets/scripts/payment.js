jQuery(document).ready(function () {
    jQuery(".entry-content>div:first-child").before(jQuery("#step_bar"));
    jQuery("#step_bar").removeClass("hidden");
    jQuery("input[name='payment_method']").val();
    show_factur_payment_method();


    jQuery(".company_selection .radio_type").click(function () {
        based_on_customer_payment_interactions(jQuery(this).val());
    });
    applyFlag();
    jQuery(document).on("input", '#billing_Phone_number,#billing_phone', function () {
        removeDialCode(jQuery(this).attr("id"));
    });

    jQuery(document).on("input", '#billing_custom_email', function () {
        let email = jQuery(this).val();
        if(validateEmail(email)!==null){
            jQuery("#billing_email").val(email);
        }
    });

    jQuery(document).on("input", '[name="payment_method"]', function () {
        let v =  jQuery(this).data("value");
        jQuery("input[name='payment_method']").val(v);
    });
});

function validateEmail(email)  {
    return email.match(
        /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    );
}

function resetCashOndelivery() {
    var selected_input = parseInt(jQuery(".company_selection input.radio_type[name='iscompany']:checked").val());
    console.log("S",selected_input);
    if (selected_input == 0 && jQuery("#payment_method_multisafepay_ideal").prop('checked') === false) {
        jQuery("input[name='payment_method']").val('');
        jQuery("input.input-radio#payment_method_bacs").val("");
        jQuery("input.input-radio#payment_method_bacs").prop('checked', false);
        jQuery("input.input-radio#payment_method_bacs").removeAttr('checked');
        jQuery("input.input-radio#payment_method_bacs").attr("value", "");
    } else if (jQuery("#payment_method_multisafepay_ideal").prop('checked') === true) {
        var multisafepay_ideal = jQuery("input.input-radio#payment_method_multisafepay_ideal").data("value");
        jQuery("input.input-radio#payment_method_multisafepay_ideal").val(multisafepay_ideal);
        jQuery("input.input-radio#payment_method_multisafepay_ideal").attr("value", multisafepay_ideal);

        if(selected_input == 1){
            jQuery("li.wc_payment_method.payment_method_bacs").removeClass('hidden');
        }
    } 
    

    else if(selected_input == 1 && jQuery("#payment_method_bacs").prop('checked') === true) {
        var bacs_value = jQuery("input.input-radio#payment_method_bacs").data("value");
        jQuery("input.input-radio#payment_method_bacs").val(bacs_value);
        jQuery("input.input-radio#payment_method_bacs").attr("value", bacs_value);

        jQuery("li.wc_payment_method.payment_method_bacs").removeClass('hidden');
    }
    else {
        var bacs_value = jQuery("input.input-radio#payment_method_bacs").data("value");
        jQuery("input.input-radio#payment_method_bacs").val(bacs_value);
        jQuery("input.input-radio#payment_method_bacs").attr("value", bacs_value);
    }
}

function applyFlag() {
    var input_contact_phone = document.getElementById("billing_Phone_number");
    window.intlTelInput(input_contact_phone, ({
        utilsScript: UtilsJs,
    }));

    var input_billing_phone = document.getElementById("billing_phone");
    window.intlTelInput(input_billing_phone, ({
        utilsScript: UtilsJs,
    }));

    // jQuery('#billing_phone').prop('maxlength', 15);
    // jQuery("#billing_phone").val("(0)");
    // jQuery("#billing_Phone_number").val("(0)");
    // jQuery("#billing_Phone_number,#billing_phone").on("input", function () {
    //     let userInput = $(this).val();
    //     userInput = "(0)" + userInput.substring(3);
    //     $(this).val(userInput); // set the modified value back to the input field
    // });
    // jQuery("#terms").prop('checked', true);

}

function removeDialCode(id) {
    var phone_number = document.getElementById(id).value;
    let char = phone_number.charAt(0);
    let first_three = phone_number.substring(0, 3);
    //  console.log(first_three);
    if (first_three === '+31') {
        //CURSIST_PHONE_LENGTH = CURSIST_PHONE_MAX_LENGTH;
        document.getElementById(id).value = phone_number.substring(3);
    } else {
        // let phone_number_updated = makeNineDigit(phone_number);
        // document.getElementsByName(`phone_cursist${id}`)[0].value = phone_number_updated;
        document.getElementById(id).value = phone_number;
    }
}

function makeNineDigit(phone_number) {
    let CURSIST_PHONE_LENGTH = '10';
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


function show_factur_payment_method(){
    let customer_type = parseInt(jQuery(".company_selection input.radio_type[name='iscompany']:checked").val());
    // if (selected_input == 1) { // company
    //     jQuery("li.wc_payment_method.payment_method_bacs").removeClass('hidden');
    // }
    // else if(selected_input == 0) { // individual
    //
    // }

    based_on_customer_payment_interactions(customer_type);
}

function based_on_customer_payment_interactions(customer_type){
    if (customer_type == 1) { //company
        jQuery("#company_group").removeClass("hidden");
        jQuery("li.wc_payment_method.payment_method_bacs").removeClass('hidden');

        /*
            jQuery("input[name='payment_method']").val("");
            jQuery("input.input-radio#payment_method_bacs").prop('checked', false);
            jQuery("input.input-radio#payment_method_multisafepay_ideal").prop('checked', false);
            jQuery("div.payment_box.payment_method_multisafepay_ideal").css("display","none");
         */

    } else {

        jQuery("#company_group").addClass("hidden");
        jQuery("li.wc_payment_method.payment_method_bacs").addClass('hidden');
        jQuery("input.input-radio#payment_method_bacs").prop('checked', false);


    }
    //making sure multisafepay_ideal would be default method
    let multisafepay_ideal = jQuery("input.input-radio#payment_method_multisafepay_ideal").data("value");
    jQuery("input[name='payment_method']").val(multisafepay_ideal);
    jQuery("input.input-radio#payment_method_multisafepay_ideal").prop('checked', true);
    jQuery("input.input-radio#payment_method_multisafepay_ideal").trigger("click");
}
