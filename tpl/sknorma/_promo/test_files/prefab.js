function adjustHeightToMax(selector) {
    var maxHeight = 0;

    jQuery(selector).each(function(){
      var currentHeight = jQuery(this).height();
      maxHeight = currentHeight > maxHeight ? currentHeight : maxHeight;
    });
    
    jQuery(selector).height(maxHeight);
}




$(document).ready(function() {

  	    $("#immersive_slider").immersive_slider({
  	      container: ".main"
  	    });

});




$(window).load(function() {

adjustHeightToMax(".products .inside .one_product");



  $('.flexslider').flexslider({
    animation: "fade",
	directionNav: false
  });
});