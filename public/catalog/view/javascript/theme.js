(function($) {
    'use strict';

//Toggle Nav for mobile  menu
    $('.menu-toggle').click(function(){
        $('.menu-wrapper').toggleClass('active-nav');
    });
//Toggle Nav for mobile menu
$('.open_bar').click(function(){
$('nav').addClass('active-nav');
$('.menu-toggle').addClass('closee');
});
$('.close_bar').click(function(){
$('nav').removeClass('active-nav');
$('.menu-toggle').removeClass('closee');
});

// Loder //
    $(function () {
      $('body').addClass('loaded');
    });

    
    
	 // Slider Active
$('.slider_list').owlCarousel({
        loop: true,
        autoplay:true,
        smartSpeed:1500,
        autoplayTimeout:5000,
        dots:false,
        nav: true,
        navText: ["<i class='fas fa-angle-left''></i>", "<i class='fas fa-angle-right''></i>"],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            992: {
                items: 1
            },
            1000: {
                items: 1
            },
            1920: {
                items: 1
            }
        }
    });

    
         // Slider testimonial
$('.testimonial_list').owlCarousel({
        loop: true,
        smartSpeed:1500,
        autoplay:true,
        autoplayTimeout:5000,
        dots:true,
        nav: false,
        navText: ["<i class='fas fa-angle-left''></i>", "<i class='fas fa-angle-right''></i>"],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 2
            },
            1000: {
                items: 3
            },
            1920: {
                items: 3
            }
        }
    });

         // Slider testimonial
$('.testimonial_list2').owlCarousel({
        loop: true,
        smartSpeed:1500,
        autoplay:true,
        autoplayTimeout:5000,
        dots:true,
        nav: false,
        navText: ["<i class='fas fa-angle-left''></i>", "<i class='fas fa-angle-right''></i>"],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            992: {
                items: 1

            },
            1000: {
                items: 1
            },
            1920: {
                items: 1
            }
        }
    });



              // Slider brand
$('.brand_list').owlCarousel({
        loop: true,
        smartSpeed:1500,
        autoplay:true,
        autoplayTimeout:5000,
        dots:false,
        nav: false,
        navText: ["<i class='fas fa-angle-left''></i>", "<i class='fas fa-angle-right''></i>"],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 3
            },
            992: {
                items: 3
            },
            1000: {
                items: 4
            },
            1920: {
                items: 6
            }
        }
    });   
     
                  // Slider brand
$('.blog_list').owlCarousel({
        loop: true,
        smartSpeed:1500,
        autoplay:true,
        autoplayTimeout:5000,
        dots:false,
        nav: false,
        navText: ["<i class='fas fa-angle-left''></i>", "<i class='fas fa-angle-right''></i>"],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 2
            },
            1000: {
                items: 2
            },
             1366: {
                items: 3
            },
            1920: {
                items: 3
            }
        }
    });  

    $('.case_list').owlCarousel({
        loop: true,
        smartSpeed:1500,
        autoplay:true,
        autoplayTimeout:5000,
        dots:true,
        nav: false,
        navText: ["<i class='fas fa-angle-left''></i>", "<i class='fas fa-angle-right''></i>"],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 2
            },
            1000: {
                items: 2
            },
              1366: {
                items: 3
            },
            1920: {
                items: 3
            }
        }
    }); 

      $('.service_list').owlCarousel({
        loop: true,
        smartSpeed:1500,
        autoplay:true,
        autoplayTimeout:5000,
        dots:true,
        nav: false,
        navText: ["<i class='fas fa-angle-left''></i>", "<i class='fas fa-angle-right''></i>"],
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 2
            },
            1000: {
                items: 2
            },
            1920: {
                items: 3
            }
        }
    }); 



            /* Portfolio Isotope  *//* 
    $('.image_load').imagesLoaded(function() {

        if ($.fn.isotope) {

            var $portfolio = $('.image_load');

            $portfolio.isotope({

                itemSelector: '.grid-item',

                filter: '*',

                resizesContainer: true,

                layoutMode: 'masonry',

                transitionDuration: '0.8s'

            });
            $('.menu-filtering li').on('click', function() {

                $('.menu-filtering li').removeClass('current_menu_item');

                $(this).addClass('current_menu_item');

                var selector = $(this).attr('data-filter');

                $portfolio.isotope({

                    filter: selector,

                });

            });

        };

    });	 */
        // counterUp
    /* $('.counter').counterUp({
        delay: 10,
        time: 1000
    }); */

// sticky
    /* var wind = $(window);
    var sticky = $('#sticky-header');
    wind.on('scroll', function () {
        var scroll = wind.scrollTop();
        if (scroll < 200) {
            sticky.removeClass('sticky-nav');
        } else {
            sticky.addClass('sticky-nav');
        }
    }); */




  
         //Header Search
    if($('.search-box-outer').length) {
        $('.search-box-outer').on('click', function() {
            $('body').addClass('search-active');
        });
        $('.close-search').on('click', function() {
            $('body').removeClass('search-active');
        });
    }
 //======< scrollcue js >======
    /* $(function(){
          scrollCue.init({
          duration : 1500,
          interval : -0.7,
          percentage : 0.90,
          smartSpeed: 500 
          
        })
    }); */
    
// to top Jquery
$(window).scroll(function(){
if($(this).scrollTop() > 40){
$('#to-top').fadeIn();
} else{
$('#to-top').fadeOut();
}
})

$("#to-top").click(function(){
$('html , body').animate({scrollTop :0},800)
});



 $(function(){
         $("#tabs").tabs();
     });
// to wow Jquery
/* new WOW().init(); */
    

	
})(jQuery);

 