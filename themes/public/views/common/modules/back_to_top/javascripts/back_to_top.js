$(function() {
    $(window).on('scroll', function() {
        if($(document).scrollTop() > 500) {
            $('.back-to-top').fadeIn(300);
        } else {
            $('.back-to-top').fadeOut(300);
        }
    });
    $(document).on('click', '.back-to-top .to-top-btn', function() {
        $('html, body').animate({'scrollTop' : 0}, 400);
    });

    $(document).on('mouseenter', '.back-to-top .wechat-qr-btn',function() {
        $('.back-to-top .wechat-qr-toggle').fadeIn(200);
    }).on('mouseleave', '.back-to-top .wechat-qr-btn', function() {
            $('.back-to-top .wechat-qr-toggle').fadeOut(200);
        });
});