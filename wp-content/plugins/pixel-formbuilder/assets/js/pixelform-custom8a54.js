/*--------------------- Copyright (c) 2022 -----------------------
[Master Javascript]
Project: Pixel html
-------------------------------------------------------------------*/
(function ($) {
	"use strict";
	var LeadCapture = {
		initialised: false,
		version: 1.0,
		mobile: false,
		init: function () {
			if (!this.initialised) {
				this.initialised = true;
			}
			else {
				return;
			}
			/*--------------- Pixel Functions Calling ----------------*/
        this.lc_barber_tesmonial_slider(); 
        this.lc_digital_testimonial_slider(); 
        this.lc_tattoo_testimonial_gallery();
        this.lc_pestcontrol_testimonial_slider();
        this.lc_barclub_testimonial_slider();
        this.lc_barclub_datepicker();
        this.lc_autoinsurence_service_slider();
        this.lc_autoinsurence_testimonial_slider();
        this.lc_cursor_js();
        this.lc_bakery_testimonialslider();
        this.lc_bakery_countdowntimer();
        this.lc_charity_team_slider();
        this.lc_charity_tetimonial_Slider();
        this.lc_dance_testimonial_slider();
        this.lc_dentist_testimonial_slider();
        this.lc_medical_testimonial_slider();
        this.lc_construction_testimonial_slider();
        this.lc_corporate_gallery_popup();
        this.lc_electronic_arrival_slider();
        this.lc_electronic_arrival2_slider();
        this.lc_interior_banner_slider();
        this.lc_interior_testimonial_slider();
        this.lc_ebook_testimonial_slider();
        this.lc_restaurant_testimonial_slider();
        this.lc_fitness_testimonial_slider();
        this.lc_internet_marketing_slider();
        this.lc_realstate_testimonial_slider();
        this.lc_gardeners_service_slider();
        this.lc_gardeners_blog_slider();
        this.lc_event_slider();
        this.lc_flower_testimonial_slider();
        this.lc_fashion_testimonial_slider();
        this.lc_security_testimonial_slider();
        this.lc_security_counter();
        this.lc_education_testimonial_slider();
        this.lc_wedding_testimonial_slider();
        this.lc_startup_testimonial_slider();
        this.lc_meetup_testimonial_slider();
        this.lc_petshop_testimonial_slider();
        this.lc_singleproperty_testimonial_slider();
        this.lc_photographer_testimonial_slider();						
		this.lc_photographer_team_slider();	
		this.lc_poolcleaning_testimonial_slider();
        this.lc_single_property_banner_counter();
        this.lc_yogaclasses_testimonial_slider();
        this.lc_hotel_testimonial_slider();
        this.lc_solar_testimonial_slider();
        this.lc_solar_progress_bar();
        this.lc_fl_skill();
		},
      // barber testimonial slider
      lc_barber_tesmonial_slider: function(){
        var swiper = new Swiper(".lc-testimonial-slider", {
          slidesPerView: 2,
          spaceBetween: 20,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            768: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            992: {
              slidesPerView: 2,
              spaceBetween: 0,
            },
          },
        });
      },
      // digital-marketing testimonial slider
      lc_digital_testimonial_slider: function(){
        var swiper = new Swiper(".lc-testimonial-slider-style1", {
          slidesPerView: 2,
          spaceBetween: 0,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            768: {
              slidesPerView: 2,
              spaceBetween: 0,
            },
            992: {
              slidesPerView: 2,
              spaceBetween: 0,
            },
          },
        });
      },
      // Tattoo testimonial slider
      lc_tattoo_testimonial_gallery: function(){
      var galleryTop = new Swiper('.lc-gallery-top', {
        spaceBetween:6,
        speed:500,
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        pagination: {
                  el: ".swiper-pagination",
                  clickable: true,
                },
         loop: true,
        loopedSlides: 3
      });
      var galleryThumbs = new Swiper('.lc-gallery-thumbs', {
        spaceBetween: 6,
        freeMode: true,
        speed: 2000,
        centeredSlides: true,
        slidesPerView: 'auto',
        touchRatio: 0.2,
        slideToClickedSlide: true,
        loop: true,
        loopedSlides: 3,
      });
      galleryTop.controller.control = galleryThumbs;
      galleryThumbs.controller.control = galleryTop;
  
    },
     // Pest-Control testimonial slider
     lc_pestcontrol_testimonial_slider: function(){
      var swiper = new Swiper(".lc-testimonial-slider-style6", {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: true,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 30,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 30,
          },
          992: {
            slidesPerView: 3,
            spaceBetween: 30,
          },
        },
      });
    },
    // Bar-club testimonial slider
    lc_barclub_testimonial_slider: function() {
      var swiper = new Swiper(".lc-bc-swiper-container", {
        slidesPerView: 3,
        spaceBetween: 30,
        slidesPerGroup: 3,
        loop: true,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        pagination: {
          el: ".lc-bc-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        breakpoints: {
          320: {
            slidesPerView: 1,
            spaceBetween: 10
          },
          480: {
            slidesPerView: 2,
            spaceBetween: 20
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 30
          },
          991: {
            slidesPerView: 3,
            spaceBetween: 30
          }
        }
      });
    },
    // barclub fixed menu
    
    lc_barclub_datepicker: function(){
      if($('#datepicker').length > 0){
      $(function () {
        $('#datepicker').datepicker();
      });
    }
    },
    // Auto-insurence services slider
    lc_autoinsurence_service_slider: function(){
      var swiper = new Swiper(".lc-ai-services-container", {
        spaceBetween: 30,
        slidesPerGroup: 3,
        loop: true,
        loopFillGroupWithBlank: true,
        navigation: {
          nextEl: ".swiper-button-next1",
          prevEl: ".swiper-button-prev1",
        },
        breakpoints: {
          320: {
            slidesPerView: 1,
            spaceBetween: 10
          },
          480: {
            slidesPerView: 2,
            spaceBetween: 20
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 30
          },
          1200: {
            slidesPerView: 3,
            spaceBetween: 30
          }
        }
      });
    },
    // Auto insurence testimonial slider
    lc_autoinsurence_testimonial_slider: function(){
      var swiper = new Swiper(".lc-ai-test-contant ", {
        slidesPerView: 1,
        spaceBetween: 30,
        slidesPerGroup: 1,
        loop: true,
        loopFillGroupWithBlank: true,
        navigation: {
          nextEl: ".swiper-button-next ",
          prevEl: ".swiper-button-prev ",
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        breakpoints: {
          480: {
            spaceBetween: 10,
          }
        }
      });
    },
    // Autoinsurence fixed menu
    lc_bakery_testimonialslider:function(){
      var swiper = new Swiper(".lc-testimonial-slider-style7", {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: true,
        loopFillGroupWithBlank: true,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
  
      });
    },
    lc_bakery_countdowntimer:function(){
      if($('#countdown').length > 0){
        (function () {
          var seconds = $('#seconds').attr('data-seconds');
          var minutes = $('#minutes').attr('data-minutes');
          var hours = $('#hours').attr('data-hours');
          var days = $('#days').attr('data-days');
          const second = 1000,
          minute = second * 60,
          hour = minute * 60,
          day = hour * 24;
          //I'm adding this section so I don't have to keep updating this pen every year :-)
          //remove this if you don't need it
          let today = new Date(),
            dd = String(today.getDate()).padStart(2, "0"),
            mm = String(today.getMonth() + 1).padStart(2, "0"),
            yyyy = today.getFullYear(),
            nextYear = yyyy + 1,
            dayMonth = "09/30/index.html",
            birthday = dayMonth + yyyy;
          today = mm + "/" + dd + "/" + yyyy;
          if (today > birthday) {
            birthday = dayMonth + nextYear;
          }
          //end
          const countDown = new Date(birthday).getTime(),
            x = setInterval(function () {
              const now = new Date().getTime(),
                distance = countDown - now;
              document.getElementById("days").innerText = Math.floor(distance / (day)),
                document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
                document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);
              //do something later when date is reached
              if (distance < 0) {
                document.getElementById("headline").innerText = "It's my birthday!";
                document.getElementById("countdown").style.display = "none";
                document.getElementById("content").style.display = "block";
                clearInterval(x);
              }
              //seconds
            }, 0)
        }());
      }
    },
    lc_charity_team_slider:function(){
      var swiper = new Swiper(".lc-team-slider-style9", {
        slidesPerView: 4,
        spaceBetween: 20,
        loop: true,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 30,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 30,
          },
          992: {
            slidesPerView: 4,
            spaceBetween: 20,
          },
        },
      });
    },
    lc_charity_tetimonial_Slider:function(){
      var swiper = new Swiper(".lc-testimonial-slider-style9", {
        slidesPerView: 2,
        spaceBetween: 30,
        loop: true,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 10,
          },
          768: {
            slidesPerView: 1,
            spaceBetween: 20,
          },
          1200: {
            slidesPerView: 2,
            spaceBetween: 30,
          },
        },
      });
    },
    lc_dance_testimonial_slider:function(){
      var swiper = new Swiper(".lc-testimonial-slider-style14", {
        slidesPerView: 3,
        spaceBetween: 0,
        loop: true,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 0,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 0,
          },
          992: {
            slidesPerView: 2,
            spaceBetween: 0,
          },
          1200: {
            slidesPerView: 3,
            spaceBetween: 0,
          },
        },
      });
    },
    lc_dentist_testimonial_slider:function(){
      var swiper = new Swiper(".lc-testimonial-slider-style15", {
        slidesPerView: 3,
        spaceBetween: 20,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 0,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 0,
          },
          992: {
            slidesPerView: 2,
            spaceBetween: 0,
          },
          1200: {
            slidesPerView: 3,
            spaceBetween: 0,
          },
        },
      });
    },
    lc_medical_testimonial_slider:function(){
      var mySwiper = new Swiper(".lc-testimonial-slider-style16", {
        spaceBetween: 1,
        slidesPerView: 3,
        centeredSlides: true,
        roundLengths: true,
        loop: true,
        loopAdditionalSlides: 30,
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev"
        },
        pagination: {
              el: ".swiper-pagination",
              clickable: true,
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 0,
          },
          992: {
            slidesPerView: 1,
            spaceBetween: 0,
          },
          1200: {
            slidesPerView: 3,
            spaceBetween: 0,
          },
        },
      });
    },
    lc_construction_testimonial_slider:function(){
      var swiper = new Swiper(".lc-testimonial-slider-style11", {
        slidesPerView: 3,
        spaceBetween: 0,
        loop: true,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 0,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 0,
          },
          992: {
            slidesPerView: 3,
            spaceBetween: 0,
          },
        },
      });
    },   
    lc_corporate_gallery_popup:function(){
      if($("#lc-galler-popup-style12").length > 0){
      $(document).ready(function () {
        $('.lc-gallery-popup, .popup-gallery1, .popup-gallery2, .popup-gallery3').magnificPopup({
          delegate: 'a',
          type: 'image',
          tLoading: 'Loading image #%curr%...',
          mainClass: 'mfp-img-mobile',
          gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
          },
          image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function (item) {
              return item.el.attr('title') + '<small></small>';
            }
          }
        });
        });
      }
    },
    lc_electronic_arrival_slider:function(){
      var swiper = new Swiper(".lc-arrival-slider", {
        slidesPerView: 4,
        grid: {
          rows: 2,
        },
        spaceBetween: 0,
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 0,
          },
          576: {
            slidesPerView: 2,
            spaceBetween: 10,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 10,
          },
          992: {
            slidesPerView: 3,
            spaceBetween: 0,
          },
          1200: {
            slidesPerView: 4,
            spaceBetween: 0,
          },
        },
      });
    },
    lc_electronic_arrival2_slider:function(){
      var swiper = new Swiper(".lc-arrival-slider2", {
        slidesPerView: 4,
        loop: true,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        spaceBetween: 0,
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
            spaceBetween: 0,
          },
          576: {
            slidesPerView: 2,
            spaceBetween: 10,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 10,
          },
          992: {
            slidesPerView: 3,
            spaceBetween: 0,
          },
          1200: {
            slidesPerView: 4,
            spaceBetween: 0,
          },
        },
      });
    },
  lc_interior_banner_slider:function(){
    var swiper = new Swiper(".lc-bannerslider-style24", {
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
    });
  },
  lc_interior_testimonial_slider:function(){
    var swiper = new Swiper(".lc-testimonial-slider-style24", {
      slidesPerView: 2,
      spaceBetween: 30,
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        0: {
          slidesPerView: 1,
          spaceBetween: 10,
        },
        768: {
          slidesPerView: 1,
          spaceBetween: 20,
        },
        992: {
          slidesPerView: 2,
          spaceBetween: 30,
        },
        1200: {
          slidesPerView: 2,
          spaceBetween: 30,
        },
      },
    });
  },
    lc_wedding_testimonial_slider:function(){
        var swiper = new Swiper(".lc-testimonial-slider-wedding", {
          slidesPerView: 2,
          spaceBetween: 30,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          pagination: {
            el: ".lc-testimonial-wedding .swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 10,
            },
            768: {
              slidesPerView: 1,
              spaceBetween: 15,
            },
            992: {
              slidesPerView: 1,
              spaceBetween: 20,
            },
            1200: {
              slidesPerView: 1,
              spaceBetween: 20,
            },
            1400: {
              slidesPerView: 2,
              spaceBetween: 30,
            },
          },
        });
      },
    lc_ebook_testimonial_slider:function(){
      var swiper = new Swiper(".lc-testimonial-slider-style17", {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            // autoplay: {
            //   delay: 2500,
            //   disableOnInteraction: false,
            // },
            pagination: {
              el: ".swiper-pagination",
              clickable: true,
            },
            breakpoints: {
              0: {
                slidesPerView: 1,
                spaceBetween: 10,
              },
              768: {
                slidesPerView: 1,
                spaceBetween: 20,
              },
              992: {
                slidesPerView: 2,
                spaceBetween: 30,
              },
            },
          });
      },
      lc_restaurant_testimonial_slider:function(){
        var swiper = new Swiper(".lc-testimonial-slider-style27", {
          slidesPerView: 1,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
            },
          },
        });
      },
      lc_fitness_testimonial_slider:function(){
        var swiper = new Swiper(".lc-test-contant-style23", {
          slidesPerView: 1,
          spaceBetween: 30,
          slidesPerGroup: 1,
          loop: true,
          loopFillGroupWithBlank: true,
          navigation: {
            nextEl: ".swiper-button-next ",
            prevEl: ".swiper-button-prev ",
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            480: {
              spaceBetween: 10,
            }
          }
        });
      },
      lc_internet_marketing_slider:function(){
        var swiper = new Swiper(".lc-services-testimonial", {
          slidesPerView: 3,
          loop: true,
        //   autoplay: { 
        //     delay: 2500,
        //     disableOnInteraction: false,
        //   },
          navigation: {
            nextEl: ".lc-internet-testimonia-next ",
            prevEl: ".lc-internet-testimonia-prev ",
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            // 576: {
            //   slidesPerView: 2,
            //   spaceBetween: 0,
            // },
            1300: {
              slidesPerView: 3,
            },
            // 1200: {
            //   slidesPerView: 3,
            // },
          },
        });
      },
      lc_realstate_testimonial_slider:function(){
        var swiper = new Swiper(".lc-testimonial-slider-style28", {
          slidesPerView: 1,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
        //   navigation: {
        //     nextEl: ".swiper-button-next ",
        //     prevEl: ".swiper-button-prev ",
        //   },
          pagination: {
               el: ".lc-testimonial-slider-style28 .swiper-pagination",
            // el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
            },
          },
        });
      },
      lc_gardeners_service_slider:function(){
        var swiper = new Swiper(".lc-serviceslider-style20", {
          slidesPerView: 4,
          spaceBetween: 0,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          navigation: {
            nextEl: ".lc-servicebox-sliderbtn-style20 .swiper-button-next ",
            prevEl: ".lc-servicebox-sliderbtn-style20 .swiper-button-prev ",
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            576: {
              slidesPerView: 2,
              spaceBetween: 0,
            },
            992: {
              slidesPerView: 3,
            },
            1200: {
              slidesPerView: 4,
            },
          },
        });
      },
      lc_gardeners_blog_slider:function(){
        var swiper = new Swiper(".lc-blogslider-style20", {
          slidesPerView: 3,
          spaceBetween: 20,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          navigation: {
            nextEl: ".lc-blog-sliderbtn-style20 .swiper-button-next ",
            prevEl: ".lc-blog-sliderbtn-style20 .swiper-button-prev ",
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            768: {
              slidesPerView: 2,
              spaceBetween: 0,
            },
            992: {
              slidesPerView: 2,
              spaceBetween: 0,
            },
            1200: {
              slidesPerView: 3,
              spaceBetween: 0,
            },
          },
        });
      },
      lc_event_slider:function(){
        var mySwiper = new Swiper(".lc-eventslider-style21", {
          spaceBetween: 28,
          slidesPerView: 3,
          slidesPerGroup: 3,
          centeredSlides: true,
          roundLengths: true,
          loop: true,
          loopAdditionalSlides: 30,
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              // spaceBetween: 0,
            },
            768: {
              slidesPerView: 2,
              spaceBetween: 20,
            },
            991: {
              slidesPerView: 3,
              slidesPerGroup: 3,
            },
          },
        });
      },
      lc_flower_testimonial_slider:function(){
        var swiper = new Swiper(".lc-testimonial-slider-style19", {
          slidesPerView: 1,
          spaceBetween: 0,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            768: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            992: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
          },
        });
      },
      lc_fashion_testimonial_slider:function(){
        var swiper = new Swiper(".lc-testimonial-slider-style19", {
          slidesPerView: 1,
          spaceBetween: 0,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            768: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            992: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
          },
        });
      },
      lc_security_testimonial_slider:function(){
        var galleryTopStyle29 = new Swiper('lc-gallery-top lc-gallery-top-style29', {
          spaceBetween:10,
          speed:500,
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                  },
                  
           loop: true,
          loopedSlides: 3,
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            480: {
              slidesPerView: 3,
              spaceBetween: 0,
            },
          },
        });
        var galleryThumbsStyle29 = new Swiper('lc-gallery-thumbs lc-gallery-thumbs-style29', {
          spaceBetween: 10,
          freeMode: true,
          speed: 2000,
          centeredSlides: true,
          slidesPerView: 'auto',
          touchRatio: 0.2,
          slideToClickedSlide: true,
          loop: true,
          loopedSlides: 3,
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            480: {
              slidesPerView: 3,
              spaceBetween: 0,
            },
          },
        });
        galleryTopStyle29.controller.control = galleryThumbsStyle29;
        galleryThumbsStyle29.controller.control = galleryTopStyle29;
      },
      lc_security_counter:function(){
        $('.lc-counter').each(function() {
          var $this = $(this),
              countTo = $this.attr('data-count');
          $({ countNum: $this.text()}).animate({
            countNum: countTo
          },
          {
            duration: 8000,
            easing:'linear',
            step: function() {
              $this.text(Math.floor(this.countNum));
            },
            complete: function() {
              $this.text(this.countNum);
              //alert('finished');
            }
          });  
        });
      },
      lc_education_testimonial_slider:function(){
        var swiper = new Swiper(".lc-testimonial-slider-style26", {
          slidesPerView: 1,
          spaceBetween: 0,
          loop: true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            768: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            992: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
          },
        });
      },
    // cursor js
    lc_cursor_js:function (){
      if($('#cursor').length > 0){
      const cursor = document.querySelector('#cursor');
      let mouse = { x: 300, y: 300 };
      let pos = { x: 0, y: 0 };
      const speed = 0.1; // between 0 and 1
      const updatePosition = () => {
          pos.x += (mouse.x - pos.x) * speed;
          pos.y += (mouse.y - pos.y) * speed;
          cursor.style.transform = 'translate3d(' + pos.x + 'px ,' + pos.y + 'px, 0)';
      };
      const updateCoordinates = e => {
          mouse.x = e.clientX;
          mouse.y = e.clientY;
      }
      window.addEventListener('mousemove', updateCoordinates);
      function loop() {
          updatePosition();
          requestAnimationFrame(loop);
      }
      requestAnimationFrame(loop);
    }
    },
    // testimonial slider startup			
		lc_startup_testimonial_slider: function(){
			var swiper = new Swiper(".lc-testimonial-slider-startup", {
				slidesPerView: 2,
				spaceBetween: 30,
				loop: true,
				autoplay: {
				  delay: 2500,
				  disableOnInteraction: false,
				},
				pagination: {
				  el: ".swiper-pagination",
				  clickable: true,
				},
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},
				breakpoints: {
				  0: {
					slidesPerView: 1,
					spaceBetween: 10,
				  },
				  768: {
					slidesPerView: 1,
					spaceBetween: 20,
				  },
				  992: {
					slidesPerView: 2,
					spaceBetween: 30,
				  },
				  1200: {
					slidesPerView: 2,
					spaceBetween: 30,
				  },
				},
			  });
		  },
		// testimonial slider startup
		// testimonial slider meetup			
		lc_meetup_testimonial_slider: function(){
			var swiper = new Swiper(".lc-testimonial-slider-style30", {
				slidesPerView: 2,
				spaceBetween: 30,
				loop: true,
				autoplay: {
				  delay: 2500,
				  disableOnInteraction: false,
				},
				pagination: {
				  el: ".swiper-pagination",
				  clickable: true,
				},
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},
				breakpoints: {
				  0: {
					slidesPerView: 1,
					spaceBetween: 10,
				  },
				  768: {
					slidesPerView: 1,
					spaceBetween: 20,
				  },
				  992: {
					slidesPerView: 2,
					spaceBetween: 30,
				  },
				  1200: {
					slidesPerView: 2,
					spaceBetween: 30,
				  },
				},
			  });
		  },
		// testimonial slider meetup
		// testimonial slider pet shop
		lc_petshop_testimonial_slider: function(){
			var swiper = new Swiper(".lc-test-slider-parent-style31", {
				slidesPerView: 1,
				spaceBetween: 30,
				loop: true,
				autoplay: {
				  delay: 2500,
				  disableOnInteraction: false,
				},
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},
				breakpoints: {
				  0: {
					slidesPerView: 1,
					spaceBetween: 10,
				  },
				  768: {
					slidesPerView: 1,
					spaceBetween: 20,
				  },
				  992: {
					slidesPerView: 1,
					spaceBetween: 30,
				  },
				  1200: {
					slidesPerView: 1,
					spaceBetween: 30,
				  },
				},
			  });
		  },
		// testimonial slider pet shop
		// testimonial slider Single property
		lc_singleproperty_testimonial_slider: function(){
			var swiper = new Swiper(".lc-testmnl-slider-parent-style33", {
				slidesPerView: 2,
				spaceBetween: 0,
				loop: true,
				autoplay: {
				  delay: 2500,
				  disableOnInteraction: false,
				},				
				breakpoints: {
				  0: {
					slidesPerView: 1,
					spaceBetween: 0,
				  },
				  768: {
					slidesPerView: 1,
					spaceBetween: 0,
				  },
				  992: {
					slidesPerView: 2,
					spaceBetween: 0,
				  },
				  1200: {
					slidesPerView: 2,
					spaceBetween: 0,
				  },
				},
			  });
		  },
		// testimonial slider Single property
		// testimonial slider			
		lc_photographer_testimonial_slider: function(){
			var swiper = new Swiper(".lc-tesmnl-slider-style35", {
				slidesPerView: 1,
				spaceBetween: 0,
				loop: true,
				autoplay: {
				  delay: 2500,
				  disableOnInteraction: false,
				},	
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},			
				breakpoints: {
				  0: {
					slidesPerView: 1,
					spaceBetween: 0,
				  },
				  768: {
					slidesPerView: 1,
					spaceBetween: 0,
				  },
				  992: {
					slidesPerView: 1,
					spaceBetween: 0,
				  },
				  1200: {
					slidesPerView: 1,
					spaceBetween: 0,
				  },
				},
			  });
		  },
		// testimonial slider
		// team slider
		lc_photographer_team_slider: function(){
			var swiper = new Swiper(".lc-team-slider-parent-style35", {
				slidesPerView: 3,
				spaceBetween: 30,
				loop: true,
				autoplay: {
				  delay: 2500,
				  disableOnInteraction: false,
				},
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},
				breakpoints: {
				  0: {
					slidesPerView: 1,
					spaceBetween: 10,
				  },
				  768: {
					slidesPerView: 2,
					spaceBetween: 20,
				  },
				  992: {
					slidesPerView: 2,
					spaceBetween: 0,
				  },
				  1200: {
					slidesPerView: 3,
					spaceBetween: 0,
				  },
				},
			  });
		  },
		// team slider
		// testimonial slider			
		lc_poolcleaning_testimonial_slider: function(){
			var swiper = new Swiper(".lc-tesmnl-slider-style36", {
				slidesPerView: 2,
				spaceBetween: 30,
				loop: true,
				autoplay: {
				  delay: 2500,
				  disableOnInteraction: false,
				},	
				pagination: {
					el: ".swiper-pagination",
					clickable: true,
				},						
				breakpoints: {
				  0: {
					slidesPerView: 1,
					spaceBetween: 30,
				  },
				  768: {
					slidesPerView: 1,
					spaceBetween: 0,
				  },
				  992: {
					slidesPerView: 2,
					spaceBetween: 30,
				  },
				  1200: {
					slidesPerView: 2,
					spaceBetween: 30,
				  },
				},
			  });
		  },
        // testimonial slider
        
        // Single Property banner start
    	lc_single_property_banner_counter: function(){
            $('.count-no').each(function() {
                var $this = $(this),
                    countTo = $this.attr('data-count');
                    
                $({
                    countNum: $this.text()
                }).animate({
                    countNum: countTo
                }, {
                    duration: 5000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
            });
    	},
        // Single Property banner End
        // Yoga Classes Testimonial Slider Start
        lc_yogaclasses_testimonial_slider: function(){
			var swiper = new Swiper(".lc-temnl-main-parent-style38", {
				slidesPerView: 2,
				spaceBetween: 30,
				loop: true,
				autoplay: {
				  delay: 2500,
				  disableOnInteraction: false,
				},	
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},
				pagination: {
					el: ".swiper-pagination",
					clickable: true,
				},						
				breakpoints: {
				  0: {
					slidesPerView: 1,
					spaceBetween: 30,
				  },
				  768: {
					slidesPerView: 1,
					spaceBetween: 0,
				  },
				  992: {
					slidesPerView: 2,
					spaceBetween: 30,
				  },
				  1200: {
					slidesPerView: 2,
					spaceBetween: 30,
				  },
				},
			  });
		  },
        // Yoga Classes Testimonial Slider End
        // Hotel-resort Classes Testimonial Slider js start
        lc_hotel_testimonial_slider: function(){
            var swiper = new Swiper(".lc-testimonialslider-style34", {
                slidesPerView: 2,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                  0: {
                    slidesPerView: 1,
                    spaceBetween: 0
                  },
                  768: {
                    slidesPerView: 2,
                    spaceBetween: 30
                  },
                }
            });
        },
        // Hotel-resort Classes Testimonial Slider js end		
        // solar-installation Testimonial Slider js start
		lc_solar_testimonial_slider:function(){
		     var swiper = new Swiper(".lc-testimonialslider-style37", {
                slidesPerView: 2,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                0: {
                    slidesPerView: 1,
                    spaceBetween: 0
                },
                992: {
                    slidesPerView: 2,
                    spaceBetween: 30
              },
            }
        });
		},
		// solar-installation Testimonial Slider js start
		
		lc_solar_progress_bar:function(){
		   if ($('.it-progresbar-wrapper .progress-bar').length > 0) {
                $(document).ready(function() {
                    const time = 1500;

                    function calculateTime(time, dataCount) {
                        return time / dataCount;
                    }

                    $(".progress-bar").each(function(index) {
                        let count = 0;
                        let label = $(this).children('.label');
                        let line = $(this).children('.line');

                        let progressCount = parseInt(label.attr('data-count'));
                        let lineCount = line.children();
                        let runTime = calculateTime(time, progressCount);
                        setInterval(function() {
                                if (count < progressCount) {
                                    count++;
                                    label.text(count + '%');
                                    lineCount.css('width', count + '%');

                                    if (count < 30) {
                                        lineCount.css('background', '#26ac47');
                                    } else if (count < 70) {
                                        lineCount.css('background', '#26ac47');
                                    } else if (count === 100) {
                                        lineCount.css('background', '#26ac47');
                                    } else {
                                        lineCount.css('background', '#26ac47');
                                    }
                                }
                            },
                            runTime);
                    });
                });


            }
		},
		
	    /**
        *  Skill Counter
        */
        lc_fl_skill: function () {
             if($('.ts-fl-wrapper').length > 0){
                var a = 0;
                $(window).scroll(function() {
                    var topScroll = $('.ts-fl-wrapper').offset().top - window.innerHeight;
                    if (a == 0 && $(window).scrollTop() > topScroll) {
                    let options = {
                        startAngle: -1.50,
                        size: 100,
                        value: 0.10,
                        fill: {
                            gradient: ['#079596', '#079596']
                        }
                    }
                
                $(".circle .bar").circleProgress(options).on('circle-animation-progress',
                    function (event, progress, stepValue) {
                        
                        $(this).parent().find("span").text(String(stepValue.toFixed(2).substr(2)) + "%");
                    });
                    
                $('.circle .bar').each(function(){
                   
                   var num = $(this).attr('data-count');
                   var color = $(this).attr('data-color');
                       
                   $(".cs"+num+" .bar").circleProgress({
                        value: num/100,
                        fill: {
                           color: color,
                        }
                    });
                  
                });
                a = 1;
                    }
                });   
            }
        },	
		
		
    };
    LeadCapture.init();
}(jQuery));

// Bar-club dropdown-menu
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}
window.onclick = function (event) {
  if (!event.target.matches('.lc-bc-dropbtn')) {
    var dropdowns = document.getElementsByClassName("lc-bc-dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
