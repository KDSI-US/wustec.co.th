"use strict";
    
var $ = jQuery.noConflict();

//=========== preloader  ===========/
$(window).on('load', function () {
    var $preloader = $('.loader'),
        $spinner   = $preloader.find('.spinner');
    $spinner.fadeOut();
    $preloader.delay(500).fadeOut('slow');
});
//=========== /preloader  ===========/



// Parallax

jQuery(function($) {
    "use strict";
    if ($('.content--parallax, .carusel--parallax').length) {
        $('.content--parallax, .carusel--parallax').each(function() {
            var attr = $(this).attr('data-image');     
            $(this).css({'background-image': 'url('+attr+')'});
        })
    }
    
});

jQuery(function($) { 
    "use strict";
     if ($('.menu').length > 0) {
          $('.menu li').hover(function(){
            $(this).addClass('hover');          
          },function(){
            $(this).removeClass('hover');         
          });                 
          
     }
});


jQuery(function($j) {
	
    "use strict";
	
	$j('.panel')
	  .on('show.bs.collapse', function(e) {
		$j(e.target).prev('.panel-heading').addClass('active');
	  })
	  .on('hide.bs.collapse', function(e) {
		$j(e.target).prev('.panel-heading').removeClass('active');
	});

});



$(document).ready(function() {
	//=========== menu  ===========//
	var touch 	= $('#touch-menu');
	var menu 	= $('.menu');

	$(touch).on('click', function(e) {
		e.preventDefault();
		menu.slideToggle();
	});
	
	$(window).resize(function(){
		var w = $(window).width();
		if(w > 823 && menu.is(':hidden')) {
			menu.removeAttr('style');
		}
	});
	//=========== /finish menu  ===========//

	 //=========== counter  ===========//
	 var time = 2, cc = 1;
		$(window).scroll(function(){
		  $('#counter').each(function(){
		    var
		    cPos = $(this).offset().top,
		    topWindow = $(window).scrollTop();
		    if(cPos < topWindow + 300) {      
		      if(cc < 2){
		        $(".number").addClass("viz");
		        $('.number').each(function(){
		          var 
		          i = 1,
		          num = $(this).data('num'),
		          step = 500 * time / num,
		          that = $(this),
		          int = setInterval(function(){
		            if (i <= num) {
		              that.html(i);
		            }
		            else {
		              cc= cc+2;
		              clearInterval(int);
		            }
		            i++;
		          },step);
		        });
		      }      
		    }    
		  });
		})
	 //=========== /counter  ===========//

	if($('.carousel-blog').length) {
		$('.carousel-blog').slick({
			dots: true,
			infinite: true,
			arrows: false,
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 3000
		});
	};

	if($('.carousel-blog').length) {
		$('.product-box-mobile').slick({
		  
		  slidesToShow: 3,
		  dots: true,
		   infinite: true,
		  responsive: [
		    {
		      breakpoint: 991,
		      settings: {
		        arrows: false,    
		      
		        slidesToShow: 1,
		      }
		    }
		    
		  ]
		});
	};

	if($('.services-box-mobile').length) {
		$('.services-box-mobile').slick({
		 
		  slidesToShow: 3,
		  dots: true,
		  infinite: true,
		  responsive: [
		    {
		      breakpoint: 991,
		      settings: {
		        arrows: false,	      
		       
		        slidesToShow: 1
		         
		      }
		    }
		    
		  ]
		});
	};

	if($('.permission-box-mobile').length) {
		var currentW = window.innerWidth || $(window).width();
		if (currentW < 991) {
		  	$('.permission-box-mobile').slick({		 
				slidesToShow: 6,
		  		dots: true,
		  		infinite: true,
		  		responsive: [
		    		{
		      			breakpoint: 991,
		      			settings: {
		        			arrows: false,	      		       
		        			slidesToShow: 1		         
		      			}
		    		}
		    
		  		]
			})
		}		
	};


});
