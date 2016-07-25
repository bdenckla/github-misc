// init
$('.focused').focus();

// actual code
$(document).keydown(function(e) {
  if (e.keyCode == 37) { // left
    if ($('.focused').prev('.focusable').length)
      $('.focused').removeClass('focused').prev('.focusable').focus().addClass('focused');
  }
  if (e.keyCode == 39) { // right
    if ($('.focused').next('.focusable').length)
      $('.focused').removeClass('focused').next('.focusable').focus().addClass('focused');
  }
});
