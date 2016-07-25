// init
$('.focused-syl').focus();

// actual code
$(document).keydown(function(e) {
  if (e.keyCode == 37) { // left
    if ($('.focused-syl').prev('.focusable').length)
      $('.focused-syl').removeClass('focused-syl').prev('.focusable').focus().addClass('focused-syl');
  }
  if (e.keyCode == 39) { // right
    if ($('.focused-syl').next('.focusable').length)
      $('.focused-syl').removeClass('focused-syl').next('.focusable').focus().addClass('focused-syl');
  }
});
