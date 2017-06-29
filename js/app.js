function squarize() {
  $('.squarize').each(function() {
    $(this).css('height', $(this).width() );
  });
}

function scrollBlur() {
  var scroll = $(window).scrollTop();
  var opacityValue = (scroll / 800);

  $('.blurred-image').css('opacity',opacityValue);
}

$(window).resize(function(e){
  squarize();
});

$(document).ready(function(e){
  squarize();
});

$(window).load(function(e){
  $(document).foundation();
});