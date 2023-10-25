jQuery( document ).ready(function() {
    jQuery(".entry-content>div:first-child").before( jQuery("#step_bar"));
    jQuery("#step_bar").removeClass("hidden");
});