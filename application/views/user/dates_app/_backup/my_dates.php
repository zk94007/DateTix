<script type="text/javascript" src="<?php echo base_url();?>assets/fancybox/jquery.fancybox.js?v=2.1.5"></script>
<link
	rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>assets/fancybox/jquery.fancybox.css?v=2.1.5"
	media="screen" />

<!-- Add mousewheel plugin (this is optional) -->
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

<!-- Add Button helper (this is optional) -->
<link
	rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>

<!-- Add Thumbnail helper (this is optional) -->
<link
	rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

<!-- Add Media helper (this is optional) -->
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
	
<script src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script src="<?php echo base_url()?>assets/js/general.js"></script>
<style>
	.user-photo-slider{text-align:center;}
	.img-slide img{max-width:100%;}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$('#my_dates').easytabs();
	$('#my_dates').bind('easytabs:after', function(tab, panel, data){
  		var target_tab_id = panel[0].id;
  		loadPhotoSlider();
    });
	
	$(".popupLink").fancybox({
            maxWidth    : 300,
            maxHeight    : 600,
            width        : '70%',
            height       : '70%',
            afterClose: function() {
            },
    });
    $(".fancybox-thumb").fancybox({
			prevEffect : 'none',
			nextEffect : 'none',
			closeBtn : false,
			helpers : {
				title : {
					type : 'inside'
				},
				thumbs : {
					width : 50,
					height : 50
				}
			}
		});
    loadPhotoSlider();
});
var photoSlidersObjs = [];
function loadPhotoSlider()
{
	$.each($('.user-photo-slider'),function(i,item){
		var userBxSliderId = $(item).attr('id');
		if($(item).attr('lang') == '1')
		{
			photoSlidersObjs[userBxSliderId].reloadSlider();
		}
		else
		{
			var userBxSlider = $(item).find('.bxslider_profile');
			if(userBxSlider.length > 0)
			{
				var bxPager = $(item).find('.bx-pager').attr('id');
		    	var nextLink = $(item).find('.slider-next').attr('id');
		    	var prevLink = $(item).find('.slider-prev').attr('id');
		    	
				var slider = userBxSlider.bxSlider({
					   pager: false,
					  infiniteLoop:false,
					  nextSelector: "#"+nextLink,
			          prevSelector: "#"+prevLink,
					  nextText: '<img src="<?php echo base_url()?>assets/images/l-arw-img.png" alt="" />',
					  prevText: '<img src="<?php echo base_url()?>assets/images/r-arw-img.png" alt="" />'
				});
				
				//Slider is created..
				$(item).attr('lang','1');        	
				photoSlidersObjs[userBxSliderId] = slider;	
			}
		}		
    });
}
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div id="page-msg-box" class="page-msg-box left" style="padding-bottom: 10px;">
				<span class="DarkGreen-color"><?php echo $this->session->flashdata('page_msg_success');?></span>
				<span class="Red-color"><?php echo $this->session->flashdata('page_msg_error');?></span>					
			</div>
			<div class="emp-B-tabing-prt">				
				<div class="emp-B-tabing-M" id="my_dates">
					<ul class='etabs'>
						<li class='tab tab-nav'>
							<span></span>
							<a id="upcomingTab" href="#upcoming"><?php echo translate_phrase('Upcoming');?></a>
						</li>
						<li class='tab tab-nav'>
							<span></span>
							<a id="pastTab" href="#past"><?php echo translate_phrase('Past');?></a>
						</li>
					</ul>
					<div id="upcoming" class="step-form-Main Mar-top-none Top-radius-none">
						<div class="step-form-Part">
							<div id="upcoming_container">
								<?php $this->load->view('user/dates_app/include/date_upcoming');?>
							</div>
						</div>
					</div>
					<div id="past" class="step-form-Main Mar-top-none Top-radius-none">
						<div class="step-form-Part">
							<div id="expired_container">
								<?php $this->load->view('user/dates_app/include/date_expired');?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>