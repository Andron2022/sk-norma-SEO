
// JavaScript Document
$(document).ready(function() {

// window type change
$(".window-type1-current, .window-type2-current, .window-type3-current, .window-type4-current").click(function() {
	  $(".types-list").slideUp('fast');
			for ( i=1; i<5; i++)
			{
				 var blankInfo = $(".window-type"+i).find('.type-blank').html();
				 $(".window-type"+i+"-current").html(blankInfo);
				} 
				$(".window-types").find('input').prop( "checked", false );
				$(".window-types label").removeClass('active');
    $(this).siblings(".types-list").slideDown('fast');
})

$(".types-list span").click(function() {
				$(this).parents(".types-list").find('label').removeClass('active');
				$('.window-image-large').attr('src', '/tpl/sknorma/img/window/' + $(this).parent().attr('class') + '.png');
				$(this).parent().addClass('active');
				var currentChoise = $(this).parent().parent().html();
				$(this).parents(".types-list").siblings('div').html( currentChoise );
    $(this).parents(".types-list").slideUp('fast');
})


// window description slide  - prev
$(".block-win .block-prev").click(function() {
	 var currentSlide = $(this).siblings('.slides-pos').find('.block-win-slide.active');
	  if(currentSlide.is(':first-child') == true)
			{
			  $(currentSlide).removeClass('active').hide();
					$(currentSlide).siblings('.block-win-slide:last-child').fadeIn().addClass('active');
			}
			else {
				$(currentSlide).removeClass('active').hide().prev().fadeIn().addClass('active');
			}
})
// window description slide  - next
$(".block-win .block-next").click(function() {
	 var currentSlide = $(this).siblings('.slides-pos').find('.block-win-slide.active');
	  if(currentSlide.is(':last-child') == true)
			{
			  $(currentSlide).removeClass('active').hide();
					$(currentSlide).siblings('.block-win-slide:first-child').fadeIn().addClass('active');
			}
			else {
				$(currentSlide).removeClass('active').hide().next().fadeIn().addClass('active');
			}
})

//radiobuttons copying 
$(".item .bottom-line span").click(function() {
    $(".item-duplicate .bottom-line label").find('input').prop( "checked", false );
				$(".item-duplicate .bottom-line label").eq($(this).parent().index()).find('input').prop( "checked", true );
})
$(".item-duplicate .bottom-line span").click(function() {
    $(".item .bottom-line label").find('input').prop( "checked", false );
				$(".item .bottom-line label").eq($(this).parent().index()).find('input').prop( "checked", true );
})

});


