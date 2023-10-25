jQuery(window).scroll(function () {
    var scroll = jQuery(window).scrollTop();
    if (scroll > 200) {
        jQuery("#masthead").addClass("fixed-header");

    } else {
        jQuery("#masthead").removeClass("fixed-header");

    }
});

jQuery(document).ready(function ($) {
    var coupon2 = $(".checkout_coupon.woocommerce-form-coupon");
    coupon2.insertAfter('.shop_table.woocommerce-checkout-review-order-table');
})


jQuery(document).ready(function () {

    if (jQuery(window).width() <= 2000) {


        var proef = jQuery('.tophead-contact ul li:last-child').html();
        jQuery('<li class="fa-chat" ><li class="topbar-icon-seperator">|</li><a class="w-chat" target="_blank" href="https://wa.me/+31641410390"><i class="fa fa-whatsapp"></i>' + 'Chat met ons' + '<span>').insertAfter('.tophead-contact ul li:last-child');


    }


    if (jQuery(window).width() <= 767) {
        jQuery("#meanmenu.mean-container").addClass("fixed-header");
        jQuery("#content").css("padding-top", "50px");
        var HeaderPhone = jQuery('#masthead #tophead .tophead-contact ul li:first-child').html();
        jQuery('<span class="m-phone" style="font-size: 9px;width: calc(100% - 85%);display: inline-block;text-align: center;">' + HeaderPhone + '<span>').insertAfter('#meanmenu .mean-bar a:first-child');
        var mail = $(".meanmenu-reveal").html();
        jQuery('<span class="m-whatup" style="font-size: 9px;width: calc(100% - 80%);display: inline-block;text-align: center;   text-transform: uppercase;"><a class="w-icon" target="_blank" href="https://wa.me/+31641410390"><i class="fab fa-whatsapp"></i>' + 'chat met ons' + '<span>').insertBefore('#meanmenu .mean-bar .meanmenu-reveal');
        var proef = $(".m-phone").html();
        jQuery('<span class="m-proef" style="font-size:9px;width: calc(100% - 85%);display: inline-block;text-align: center;   text-transform: uppercase;"><a class="w-prof" target="_blank" href="#"><i class="fab fa-exam"></i>  ' + 'VCA proef examen' + '<span>').insertBefore('#meanmenu .mean-bar .m-phone');
        var cart_html = $(".cart-icon-area").html();
        $(".m-whatup").after(`<div class="cart-icon-area">${cart_html}</div>`);
        jQuery('<span class="cart-icon-area" style="display: inline-block!important;font-size:9px;width:35px;text-align: center;   text-transform: uppercase;"> </span>');
        $(".cart-icon-area .cart-icon-products").remove();
    }


    jQuery(".participant-info label .optional").remove();
    jQuery('#amount_cursists').on('mousewheel', function (e) {
        jQuery(this).blur();
    });
    jQuery('#form_subscription').trigger("reset");
    jQuery('.business').css('display', 'none');
    jQuery('.businessfield').each(function () {
        jQuery(this).attr('disabled', true);
    });
    jQuery('.radio_type').change(function () {
        const type = this.value;
        if (type == 0) {
            jQuery('.business').css('display', 'none');
            jQuery('.businessfield').each(function () {
                jQuery(this).attr('disabled', true);
            })
            jQuery('.private').fadeIn();
            jQuery('.private').each(function () {
                jQuery(this).attr('disabled', false);
            })
        } else if (type == 1) {
            //            jQuery('.private').css('display', 'none');
            //            jQuery('.private').each(function () {
            //                jQuery(this).attr('disabled', true);
            //            })
            jQuery('.business').fadeIn();
            jQuery('.businessfield').each(function () {
                jQuery(this).attr('disabled', false);
            })
        }
    });
    jQuery('#amount_cursists').change(function () {
        var html = "";
        var noCursist = jQuery('.cursist').length;
        jQuery('#submit_subscription').prop('disabled', true);
        jQuery('#amount_cursists').prop('disabled', true);
        if (jQuery.isNumeric(this.value)) {
            if ((this.value - noCursist) > 0) {
                jQuery.ajax({
                    type: 'POST',
                    url: "/wp-admin/admin-ajax.php",
                    data: {
                        action: 'getFormCursist',
                        number: this.value,
                        noCursist: noCursist,
                    },
                    success: function (data) {
                        jQuery('#cusistswrapper').append(data);
                        jQuery('#submit_subscription').prop('disabled', false);
                        jQuery('#amount_cursists').prop('disabled', false);
                    }
                });
            } else {
                for (i = 0; i < noCursist - this.value; i++) {
                    var index = noCursist - i;
                    jQuery('#cursist' + index).remove();
                }
                jQuery('#amount_cursists').prop('disabled', false);
                jQuery('#submit_subscription').prop('disabled', false);
            }
        }
    }).change();

    // sticky sidebar
    
    if(jQuery('.single-product-edit #wrapper_contact').length > 0){
        jQuery('.single-product-edit #wrapper_contact').stickySidebar({
            topSpacing: 140,
            bottomSpacing: 0,
            containerSelector: '#content .single-product-edit',
            innerWrapperSelector: '.single-product-edit #wrapper_contact .team-member',
            resizeSensor: true,
        });
    }
   


});
