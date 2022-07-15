$(function() {
	$.fancybox.defaults.thumbs.autoStart = true;


	//главный слайдер
    $('.carousel-inner-new').slick({
        arrows: true,
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        //Создаст кастомные кнопки
        prevArrow: $('.left.carousel-control'), //Кнопки привязаны из старого слайдера
        nextArrow: $('.right.carousel-control'), //Кнопки привязаны из старого слайдера
        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });



    //1 фото
    $('.slider-solo').slick({
        arrows: true,
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        //Создаст кастомные кнопки <i class="fas fa-angle-left"></i>
		prevArrow: "<div class='prev-custom' ><i class='fa fa-chevron-left fa-3x'></i></div>",
		nextArrow: "<div class='next-custom' ><i class='fa fa-chevron-right fa-3x'></i></div>",
        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });


    //1 фото
    $('.slider-solo-3').slick({
        arrows: true,
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        //Создаст кастомные кнопки <i class="fas fa-angle-left"></i>
        prevArrow: "<div class='prev-custom' ><i class='fa fa-chevron-left fa-2x'></i></div>",
        nextArrow: "<div class='next-custom' ><i class='fa fa-chevron-right fa-2x'></i></div>",
        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });



    //2 фото в ширину, слайдер, если больше
    $('.slider-2').slick({
        arrows: true,
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 2,
        slidesToScroll: 1,
        //Создаст кастомные кнопки
        prevArrow: "<div class='prev-custom' ><i class='fa fa-chevron-left fa-2x'></i></div>",
        nextArrow: "<div class='next-custom' ><i class='fa fa-chevron-right fa-2x'></i></div>",

        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });



    //3 фото в ширину, слайдер, если больше
    $('.slider-3').slick({
        arrows: true,
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 1,
        //Создаст кастомные кнопки
        prevArrow: "<div class='prev-custom' ><i class='fa fa-chevron-left fa-2x'></i></div>",
        nextArrow: "<div class='next-custom' ><i class='fa fa-chevron-right fa-2x'></i></div>",
        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });




    //4 фото в ширину, слайдер, если больше
    $('.slider-4-row').slick({
        arrows: true,
        dots: false, // включает количество кадров в слайдере
        infinite: false,
        speed: 300,
        slidesToShow: 4, // отвечает за количество показаных слайдов
        slidesToScroll: 1, // Сколько слайдов одновременно прокрутить
        //Создаст кастомные кнопки
        prevArrow: "<div class='prev-custom' ><i class='fa fa-chevron-left fa-2x'></i></div>",
        nextArrow: "<div class='next-custom' ><i class='fa fa-chevron-right fa-2x'></i></div>",
        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });




    //6 фото в ширину, слайдер, если больше
    $('.slider-4').slick({
        arrows: true,
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 6,
        slidesToScroll: 1,
        //Создаст кастомные кнопки
        prevArrow: "<div class='prev-custom-m' ><i class='fa fa-chevron-left '></i></div>",
        nextArrow: "<div class='next-custom-m' ><i class='fa fa-chevron-right '></i></div>",
        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });



    //12 фото в ширину, слайдер, если больше
    $('.slider-5').slick({
        arrows: true,
        dots: false,
        infinite: false,
        autoplaySpeed: 1000, // Скорость прокрутки слайдера в милесекундах 1000 = 1 сек
        autoplay: false, //автоплей если false то не работает
        speed: 300,
        slidesToShow: 12, // показываем 12  слайдов
        slidesToScroll: 1, //прокручивать слайды по 1
        //Создаст кастомные кнопки
        prevArrow: "<div class='prev-custom-m' ><i class='fa fa-chevron-left '></i></div>",
        nextArrow: "<div class='next-custom-m' ><i class='fa fa-chevron-right '></i></div>",
        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });



    //Слайдер с автопрокруткой
    $('.slider-autoplay').slick({
        arrows: true,
        dots: false,
        infinite: false,
        autoplaySpeed: 1000, // Скорость прокрутки слайдера в милесекундах 1000 = 1 сек
        autoplay: true, //автоплей если false то не работает
        speed: 300,
        slidesToShow: 3, // показываем 12  слайдов
        slidesToScroll: 1, //прокручивать слайды по 1
        //Создаст кастомные кнопки
        prevArrow: "<div class='prev-custom' ><i class='fa fa-chevron-left fa-2x'></i></div>",
        nextArrow: "<div class='next-custom' ><i class='fa fa-chevron-right fa-2x'></i></div>",
        responsive: [
            {
                breakpoint: 1024, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                }
            },
            {
                breakpoint: 960, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
            {
                breakpoint: 480, // брейкпоинты на которых можно настроить состояние слайдера
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
    $(".slider-autoplay .slick-slide:not(.cloned) .slide a").fancybox({
        fancybox: true,
        infobar : true,
        toolbar : true,
        loop     : true
    });



//Листаем слайдер  колесиком  для  этого подключена библтотека  jquery-mousewheel.
// для запуска прокрутки колесиком добавте scroll-wheel к класу слайдера class="slider-4 scroll-wheel"
    $('.scroll-wheel').mousewheel(function(e) { // на прокрутку мышки добавляем переключение слайдов
        e.preventDefault();
        if (e.deltaY < 0) {
            $(this).slick('slickNext');
        } else {
            $(this).slick('slickPrev');
        }
    });


//боковое меню скрипт отработает когда прокрутим .sidebar-scrolled-z

   $(window).on("scroll", function() {
       if($(window).scrollTop() > 80) //сколько пикселей прокрутить чтоб сайдбар прилип работаю в custom.js
                     {
           $(".sidebar-scrolled-z").addClass("active");
       } else {
           $(".sidebar-scrolled-z").removeClass("active");
       }
   });


 // text-excerpt p все теги p которые обвернуты в text-excerpt будет работать скрипт
        let showChar = 200;  // Сколько символов показывать
        let ellipsestext = "...";
        let moretext = "показать еще >";
        let lesstext = "скрыть <";
        $('.text-excerpt p').each(function() {
            var content = $(this).html();

            if(content.length > showChar) {
                let c = content.substr(0, showChar);
                let h = content.substr(showChar, content.length - showChar);
                let html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
                $(this).html(html);
            }
        });

        $(".morelink").click(function(){
            if($(this).hasClass("less")) {
                $(this).removeClass("less");
                $(this).html(moretext);
            } else {
                $(this).addClass("less");
                $(this).html(lesstext);
            }
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return false;
        });



});
