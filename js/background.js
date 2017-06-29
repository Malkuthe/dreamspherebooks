$(window).scroll(function(e){
    scrollBlur();
    var fromTop = $(window).scrollTop();
    var menuHeight = $('.menu-container').innerHeight();
    $('.off-canvas-content').toggleClass("down", (fromTop > menuHeight));
    setTimeout( function() {
        $('.off-canvas-content').toggleClass("slide", (fromTop > menuHeight));
    }, 1);
});