jQuery(document).ready(function () {
    if(jQuery("#vca_training").length >0){
        setTimeout
        jQuery('html, body').animate({
            scrollTop: jQuery("#vca_training").offset().top
        },10);
    }

    jQuery("#course").select2();
})