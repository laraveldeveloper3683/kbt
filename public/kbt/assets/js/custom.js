var sw = $(window).width();
var sh = $(window).height();

$(window).on('load', function () {

    /*setTimeout(function() {
        $(".loader-first").fadeOut("slow");
        $('html').removeClass('loadjs');
    }, 1000);

    if($(document).find('img').hasClass('svg-convert')) {
        $('.svg-convert').svgConvert({onComplete: function() {
            }
        });
    }*/

    setTimeout(function () {

        /* ---------------- In View Animation -------------------- */
        $('.animate').bind('inview', function (event, isInView) {
            if (isInView) {
                var animate = $(this).attr('data-animation');
                var speedDuration = $(this).attr('data-duration');
                var $t = $(this);
                setTimeout(function () {
                    $t.removeClass('animate');
                    $t.addClass(animate + ' animated');
                }, speedDuration);
            }
        });

        $('img').each(function () {
            var $element = $(this);
            if (sw > 767) {
                if ($element[0].hasAttribute('data-desktop-src')) {
                    var imageUrl = $element.attr("data-desktop-src");
                    $element.attr("src", imageUrl);
                    $element.removeAttr('data-desktop-src');
                    $element.removeAttr('data-mobile-src');
                } else if ($element[0].hasAttribute('data-both-src')) {
                    var imageUrl = $element.attr("data-both-src");
                    $element.attr("src", imageUrl);
                    $element.removeAttr('data-both-src');
                }
            } else {
                if ($element[0].hasAttribute('data-mobile-src')) {
                    var imageUrl = $element.attr("data-mobile-src");
                    $element.attr("src", imageUrl);
                    $element.removeAttr('data-desktop-src');
                    $element.removeAttr('data-mobile-src');
                } else if ($element[0].hasAttribute('data-both-src')) {
                    var imageUrl = $element.attr("data-both-src");
                    $element.attr("src", imageUrl);
                    $element.removeAttr('data-both-src');
                }
            }
        });

        $('.js-hero .hero__img').each(function () {
            var $element = $(this);
            if (sw > 767) {
                if ($element[0].hasAttribute('data-desktop-bg')) {
                    var imageUrl = $element.attr("data-desktop-bg");
                    $element.attr("style", "background-image:url(" + imageUrl + ")");
                    $element.removeAttr('data-desktop-bg');
                    $element.removeAttr('data-mobile-bg');
                }
            } else {
                if ($element[0].hasAttribute('data-mobile-bg')) {
                    var imageUrl = $element.attr("data-mobile-bg");
                    $element.attr("style", "background-image:url(" + imageUrl + ")");
                    /*$element.removeAttr('data-bgimg');
                    $element.removeAttr('data-bgimg-mob');*/
                }
            }
        });

    }, 200);

});


$(document).ready(function () {

    if (sw > 1025) {
        if ((sw < 1400) && (sh > 900)) {

        } else {
            fontResizer();
        }
    }

    $(".submenu").mouseover(function () {
        if (sw > 1025) {
            $(this).find('.meagamenu_main').addClass('active');
        }
    });
    $(".submenu").mouseout(function () {
        if (sw > 992) {
            $(this).find('.meagamenu_main').removeClass('active');
        }
    });


    $('.submenu .chevron').click(function () {
        if ((sw < 1100) || ((sw < 1400) && (sw > 1300) && (sh > 900))) {
            var $sthis = $(this).parent().find('.mega--menu');
            if ($sthis.is(":visible")) {
                $sthis.slideUp();
            } else {
                $('.mega--menu').slideUp();
                $sthis.slideDown();
            }
        } else {
            $(this).parent().find('.meagamenu_main').addClass('active');
        }
    });

    $('.menu__sign').click(function () {
        if (sw < 992) {
            var $sthis = $(this).parent().find('.mega--menu');
            if ($sthis.is(":visible")) {
                $sthis.slideUp();
            } else {
                $('.mega--menu').slideUp();
                $sthis.slideDown();
            }
            if ($(this).hasClass('open')) {
                $('.menu__sign').removeClass('open');
            } else {
                $('.menu__sign').removeClass('open');
                $(this).addClass('open');
            }
        } else {
            $(this).parent().find('.meagamenu_main').addClass('active');
        }

    });

    var path = window.location.pathname.split("/").pop();
    var target = $('.navigation a[href="/' + path + '"]');
    target.addClass('active');

    setTimeout(function () {
        var interleaveOffset = 0.5;

        var swiperOptions = {
            loop: true,
            speed: 1000,
            watchSlidesProgress: true,
            navigation: {
                nextEl: ".js-heronav .swiper-button-next",
                prevEl: ".js-heronav .swiper-button-prev"
            },
            // autoplay: {
            //     disableOnInteraction: false,
            //     delay: 5000,
            // },
            on: {
                progress: function () {
                    var hswiper = this;
                    for (var i = 0; i < hswiper.slides.length; i++) {
                        var slideProgress = hswiper.slides[i].progress;
                        var innerOffset = hswiper.width * interleaveOffset;
                        var innerTranslate = slideProgress * innerOffset;
                        hswiper.slides[i].querySelector(".hero__img").style.transform =
                            "translate3d(" + innerTranslate + "px, 0, 0)";
                    }
                },
                touchStart: function () {
                    var hswiper = this;
                    for (var i = 0; i < hswiper.slides.length; i++) {
                        hswiper.slides[i].style.transition = "";
                    }
                },
                setTransition: function (speed) {
                    var hswiper = this;
                    for (var i = 0; i < hswiper.slides.length; i++) {
                        hswiper.slides[i].style.transition = speed + "ms";
                        hswiper.slides[i].querySelector(".hero__img").style.transition =
                            speed + "ms";
                    }
                }
            }
        };

        var heroSl = new Swiper(".js-hero .swiper-container", swiperOptions);

        // if ($(document).find('div').hasClass('mySwiper')) {
        var tokenomics = new Swiper('.mySwiper', {
            slidesPerView: 3,
            observer: true,
            observeParents: true,
            allowTouchMove: false,
            loop: true,
            //spaceBetween: 15,
            //effect: 'fade',
            speed: 800,
            autoplay: {
                pauseOnMouseEnter: false,
                delay: 4000,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
        //}

        if ($(document).find('div').hasClass('shop__slider')) {
            var exclusivelng = new Swiper('.shop__slider .swiper-container', {
                slidesPerView: '3',
                observer: true,
                observeParents: true,
                allowTouchMove: true,
                loop: true,
                spaceBetween: 32,
                speed: 800,
                autoplay: {
                    disableOnInteraction: false,
                    delay: 4000,
                },
                navigation: {
                    nextEl: ".shop__slider .swiper-button-next",
                    prevEl: ".shop__slider .swiper-button-prev"
                },
                breakpoints: {
                    1100: {
                        slidesPerView: 2,
                    },
                    992: {
                        slidesPerView: 2,
                    },
                    767: {
                        slidesPerView: 1,
                    }
                },
            });
        }

        if ($(document).find('div').hasClass('testimonial__slider')) {
            var testimonial = new Swiper('.testimonial__slider .swiper-container', {
                slidesPerView: '3',
                observer: true,
                observeParents: true,
                allowTouchMove: true,
                loop: true,
                spaceBetween: 32,
                speed: 800,
                autoplay: {
                    disableOnInteraction: false,
                    delay: 4000,
                },
                navigation: {
                    nextEl: ".testimonial__slider .swiper-button-next",
                    prevEl: ".testimonial__slider .swiper-button-prev"
                },
                breakpoints: {
                    1100: {
                        slidesPerView: 2,
                    },
                    992: {
                        slidesPerView: 2,
                    },
                    767: {
                        slidesPerView: 1,
                    }
                },
            });
        }
        if ($(document).find('div').hasClass('banner__slider')) {
            var bannerslider = new Swiper('.banner__slider .swiper-container', {
                slidesPerView: '1',
                spaceBetween: 0,
                loop: true,
                autoplay: {
                    disableOnInteraction: false,
                    delay: 8000,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });
        }

    }, 2000);
    $('.toggle-menu').click(function () {
        //$('html').toggleClass('open-menu');
        $(this).toggleClass('open');
        $('.navigation').toggleClass('active');
        $('body').toggleClass('active');
    });


    $('.js-anchor-link').click(function (e) {
        e.preventDefault();
        var target = $($(this).attr('href'));
        if (target.length) {
            var scrollTo = target.offset().top;
            $('body, html').animate({ scrollTop: scrollTo + 'px' }, 800);
        }
    });

    $('.navigation a').click(function () {
        $('.navigation').removeClass('active');
        $('.toggle-menu').removeClass('open');
        $('body').removeClass('active');
    });

    $('.hover__text').mouseenter(function () {
        $(this).text('Comingsoon');
    });
    $('.hover__text').mouseleave(function () {
        $(this).text('Bull’s Arena');
    });

});



function fontResizer() {
    var perc = parseInt(sw) / 120;
    $('body').css('font-size', perc);
}

$(window).on('resize orientation', function () {
    sw = $(window).width();
    sh = $(window).height();
    if (sh < 450 && sw > 480 && sw < 820) {
        $('#portrait-warnning').css('display', 'flex');
    } else {
        $('#portrait-warnning').css('display', 'none');
    }

    setTimeout(function () {
        if (sw > 1025) {

            if ((sw < 1400) && (sw > 1300) && (sh > 900)) {

            } else {
                fontResizer();
            }
        }
    }, 500);
});


$(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
        $('header').addClass('active');
    } else {
        $('header').removeClass('active');
    }
});