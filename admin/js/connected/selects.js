$(document).ready(function () {
	$('#country_id').change(function () {
		var country_id = $(this).val();
		if (country_id == '0') {
			$('#region_id').html('<option>- выберите -</option>'); // регион 
			$('#region_id').attr('disabled', true);
			$('#city_id').html('<option>- выберите -</option>'); // город 
			$('#city_id').attr('disabled', true);
			return(false);
		}
		$('#region_id').attr('disabled', true);
		$('#region_id').html('<option>загрузка...</option>');
		
		var url = 'js/connected/get_regions.php?id='+option_id;		
		$.get(
			url,
			"country_id=" + country_id,
			function (result) {
				if (result.type == 'error') {
					//alert('error');
					return(false);
				}
				else {
					var options = ''; 
					
					$(result.regions).each(function() {
						//options += '<option value="' + $(this).attr('region_id') + '">' + $(this).attr('name') + '</option>';				option_value		
              options += '<option value="' + $(this).attr('region_id') + '">' + $(this).attr('name') + '</option>';
					});
					
					$('#region_id').html('<option>- выберите -</option>'+options); // регион 
					$('#region_id').attr('disabled', false);
					$('#city_id').html('<option>- выберите -</option>'); //  город
					$('#city_id').attr('disabled', true);  	
							
				}
			},
			"json"
		);
	});

$('#region_id').change(function () {
		var region_id = $(this).val(); //$('#region_id :selected').val();
		//alert (region_id);
		if (region_id == '0') {
			$('#city_id').html('<option>- выберите -</option>'); //  город 
			$('#city_id').attr('disabled', true);
			return(false);
		}
		$('#city_id').attr('disabled', true);
		$('#city_id').html('<option>загрузка...</option>');
		
		var url = 'js/connected/get_city.php?id='+option_id;
		
		$.get(
			url,
			"region_id=" + region_id,
			
			function (result) {
				if (result.type == 'error') {
					//alert('error');
					return(false);
				}
				else {
					var options = ''; 
					$(result.citys).each(function() {
						//options += '<option value="' + $(this).attr('city_id') + '">' + $(this).attr('name') + '</option>'; 
						options += '<option value="' + $(this).attr('city_id') + '">' + $(this).attr('name') + '</option>'; 
						
					});
					$('#city_id').html('<option>- выберите -</option>'+options); // город
					$('#city_id').attr('disabled', false);
					
$('#city_id').change(function(){
	var value = $('#city_id :selected').text();
	var city_id = $('#city_id :selected').val(); 
	if (city_id !== '0') {
	$('#selectBoxInfo').html(' '+ value).
	fadeIn(1000,function(){
	$(this).fadeOut(2000);
    });	
 } 
});					
	}
			},
			"json" 
		);
	});	
});
