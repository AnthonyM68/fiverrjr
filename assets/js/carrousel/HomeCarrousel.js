
(function () {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('=> HomeCarrousel.js loaded');
        $('.ui.carousel.slider').slick({
            slidesToShow: 1,
            autoplay: true,
            autoplaySpeed: 5000,
            dots: true,
            infinite: true,
            adaptiveHeight: true,
            prevArrow: '<button type="button" class="slick-prev"><i class="angle left icon"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="angle right icon"></i></button>'
        });
    });
})();