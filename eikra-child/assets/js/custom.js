'use strict';
(function ($) {

    $(document).ready(function () {

  //   	$('.slick-slider').slick({
		// 	autoplay: true,
		// 	speed: 1000,
		// 	lazyLoad: 'progressive',
		// 	dots: true,
		// 	prevArrow: '<button type="button" class="slick-prev"></button>',
		// 	nextArrow: '<button type="button" class="slick-next"></button>'
		// });
		tableActive();

		$('.btn-col-featured').on('click', function(){
			$('.btn-col-featured.btn-active').removeClass('btn-active');
			$('.featured-list .col-featured.featured-active').removeClass('featured-active');

			$(this).addClass('btn-active');
			var datType = $(this).attr('data-type');
			$('.featured-list .col-featured[data-type='+ datType +']').addClass('featured-active');
		})
    });


	$(window).resize(function() {
		tableActive();
	});

	function tableActive(){
		if ($(window).width() < 768) {
		    $('.featured-list .col-featured').removeClass('featured-active');
		    $('.featured-list .col-featured:first-child').addClass('featured-active');
		    $('.btn-col-featured:first-child').addClass('btn-active');
		     
		}else {
			$(".featured-list .col-featured").mouseover(function(){
				$(".featured-list .col-featured").removeClass("featured-active");
				$(this).addClass('featured-active');
		    });

		    // $(".featured-list .col-featured").mouseout(function(){
		    // 	$(this).removeClass("featured-active");
		    // });
		    
		}
	}

})(jQuery)
