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
<!--*********Page start*********-->

<div class="wrapper">
	<div class="content-part">
			
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">
				<div class="div-row jen-name">
					<a class="lin-hght34" href="<?php echo base_url('dates/my_dates')?>"><?php echo translate_phrase('Back');?></a>
				</div>
				<div class="step-form-Main">
				
					<div class="girl-msg-main">
						<ul class="detail-list">
							<li>
								<?php echo print_date_day($date_info['date_time']).' @ '.date('h:i A', strtotime($date_info['date_time']));?>		
							</li>
							<li>
								<?php echo $date_info['date_type']. translate_phrase(' at '). $date_info['name'];?>										
								<a target="_blank" href="http://maps.google.com/?q=<?php echo $date_info['address'];?>" ><?php echo translate_phrase('View Map');?></a>
									
							</li>
							<li><?php echo translate_phrase('Looking for').' '.$date_info['gender'].translate_phrase(' for ').' '. $date_info['intention_type'];?></li>
						</ul>
					</div>
					
				
				<div class="step-form-Part">
						<div class="datesArea">
							<?php if($date_applications):?>
								<?php foreach($date_applications as $key=>$date_info):
									$date_id = $date_info['date_id'];
									?>
								<div class="dateRow" id="date_<?php echo $date_info['date_id'];?>">
									<div class="userBoxLeft user-photo-slider" id="photo_slider_<?php echo $date_info['date_id'];?>">				
										<?php if($date_info['user_photos']): ?>
										<div class="img-left-box">
											<ul class="bxslider_profile">
												<?php foreach ($date_info['user_photos'] as $photo): ?>
												<li class="img-slide">
													<a class="fancybox-thumb" rel="gallery<?php echo $date_id;?>" href="<?php echo $photo['url'] ;?>"> <img style="height: 180px" src="<?php echo $photo['url'] ?>" alt="<?php echo $photo['photo'] ?>" /></a>
												</li>
												<?php endforeach; ?>
											</ul>
											<!-- Following Code is used for generate Next-Prev Link, We can put this div at outside of bxSlider but i have put here for don't messup design :( -->
											<div class="outside">
												<div id="slider-next_<?php echo $date_id;?>" class="l-arw-img slider-next"></div>
												<div id="slider-prev_<?php echo $date_id;?>" class="r-arw-img slider-prev"></div>
											</div>
										</div>
										<?php else:?>
										<?php echo translate_phrase('No photos added yet');?>
										<?php endif; ?>	
									</div>	
									<div class="userBoxRight">
										<?php if($key != 0):?>
										<div class="divider"></div>
										<?php endif;?>
										<p><?php //echo ;?></p>
									
										<div class="mar-top2">
											<div class="column-50">
												<div><?php echo $date_info['applicant_by_name'].', '.$date_info['age'];?></div>
												<div class="userbox-innr comn-top-mar">
													<p class="DarkGreen-color">Offering 4 date tickets</p>
													<p class="font-italic"><?php echo translate_phrase('Applied').' '.$date_info['applied_time'];?></p>												
												</div>	
											</div>
											<div class="column-50">
												
											</div>
										</div>
										
									</div>		
								</div>
								<?php endforeach;?>
							<?php else:?>
								<p><span class="no-rows"><?php echo translate_phrase('You currently have no applicants for this date.')?></span></p>
							<?php endif;?>	
						</div>
					</div>
				</div>
			</div>
			<!--*********Apply-Step1-E-Page close*********-->		
	</div>
</div>
<!--*********Page close*********-->
