jQuery(document).ready(function($) {

	'use strict';

	//***************************
    // Silk Slider Functions
    //***************************
	  jQuery('.wm-banner').slick({
		  slidesToShow: 1,
		  slidesToScroll: 1,
		  autoplay: true,
		  autoplaySpeed: 7000,
		  infinite: true,
		  fade: true,
		  dots: true,
		  arrows: false,
    	  responsive: [
			    {
			      breakpoint: 1024,
			      settings: {
			        slidesToShow: 1,
			        slidesToScroll: 1,
			        infinite: true,
			      }
			    },
			    {
			      breakpoint: 800,
			      settings: {
			        slidesToShow: 1,
			        slidesToScroll: 1
			      }
			    },
			    {
			      breakpoint: 400,
			      settings: {
			        slidesToShow: 1,
			        slidesToScroll: 1
			      }
			    }
			  ]
		});



    //***************************
    // Click to Top Button
    //***************************
    jQuery('.backtop-btn').on("click", function() {
        jQuery('html, body').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

});
