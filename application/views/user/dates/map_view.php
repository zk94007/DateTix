<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script
	src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=en"></script>
<script type="text/javascript">
function initialize() {
	
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode(
	    	{'address': "<?php echo $venue_data['address'];?>"}, 
	    	function(results, status)
	    	{
	        	if (status == google.maps.GeocoderStatus.OK) 
	        	{ 
	            	location1 = results[0].geometry.location;
	            	var lat = location1.lat();
	            	var lng = location1.lng();

	             	var myLatlng = new google.maps.LatLng(lat,lng);
		          	  var mapOptions = {
		          	    zoom: 19,
		          	    center: myLatlng
		          	  }
	            	var map = new google.maps.Map(document.getElementById('googleMap'), mapOptions);

	            	var marker = new google.maps.Marker({
	                position: myLatlng,
	                map: map,
	                title: "<?php echo trim($venue_data['name']);?>"
	            	});
	            }
	        	else
	        	{
		        	alert('sorry.. No lat long found.')
			  		 return;
		        }
	        }
	);	
	
	
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
<!--*********Suggest date ideal Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box Mar-top-none popupBig">
				<div class="popup-header">
					<h1>
					<?php echo $heading_txt;?>
					</h1>
					<p class="cityTxt">
						<span class="Black-color"><?php echo $venue_data['name'];?> </span>
						-
						<?php echo $venue_data['address'];?>
					</p>
				</div>

				<div class="how-it-works-main" id="googleMap"></div>
				<div class="Nex-mar">
					<a href="<?php echo base_url().url_city_name().'/'.$return_url?>"
						class="Next-butM">Ok</a>
				</div>

			</div>
		</div>
	</div>
</div>
<!--*********Suggest date ideal -Page close*********-->
