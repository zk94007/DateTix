<!-- Add FlexSlider JS and CSS files -->
<link rel='stylesheet'  href="<?php echo base_url();?>assets/FlexSlider/flexslider.css" />
<script src="<?php echo base_url();?>assets/FlexSlider/jquery.flexslider.js"></script>
<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<script>
	$(window).load(function() {
	  // The slider being synced must be initialized first
	  $('#carousel').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		itemWidth: 210,
		itemMargin: 5,
		asNavFor: '#slider',
		start: function (slider) {
	       // lazy load
	       $(slider).find("img.lazy").each(function () {
	          var src = $(this).attr("data-src");
	          $(this).attr("src", src).removeAttr("data-src");
	       });
	     }
	  });
	   
	  $('#slider').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: true,
		slideshow: true,
		sync: "#carousel",
		start: function (slider) {
	       // lazy load
	       $(slider).find("img.lazy").each(function () {
	          var src = $(this).attr("data-src");
	          $(this).attr("src", src).removeAttr("data-src");
	       });
	     }
	  });
	});
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">				
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="step-form-Main Mar-top-none Top-radius-none" id="details">
				<?php if($event_info):?>
				<div class="step-form-Part">
					<div class="userBox-wrap">
				          <div class="userTop">
							<span class="fl">
								<div class="selectDateHed martop-edit"><?php echo $event_info['event_name'];?></div>
								<div class="dateRow"><br/><?php echo date(DATE_FORMATE,strtotime($event_info['event_start_time']));?></div>
								<div class="dateRow"><?php echo date('g:ia',strtotime($event_info['event_start_time'])).translate_phrase(" to ").date('g:ia',strtotime($event_info['event_end_time']));?></div>
							</span>
							
							<div class="dateRow"><?php echo $event_info['name'];?></div>
								<div class="locationArea Mar-top-none">
									<p>
										<?php 
											if($event_info['address'])
												echo $event_info['address'];
											if($event_info['neighborhood_name'])
												echo ', '.$event_info['neighborhood_name'];?>
										&nbsp; &nbsp;<a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=event_photos.html'.$form_para.'&venue='.$this->utility->encode($event_info['venue_id']);?>"><?php echo translate_phrase('View Map')?></a></p>
									<p><?php 
											if($event_info['city_name'])
												echo $event_info['city_name'];?></p>
									<p><?php echo $event_info['phone_number'];?></p>
									
									<?php if($event_info['website_url'] != ''):?>
									<p><a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo $event_info['website_url'];?>"><?php echo $event_info['website_url'];?></a></p>
									<?php endif;?>
								</div>
							</div>				                
						</div>
					</div>
					<?php if($event_photos):?>
					<div class="last-bor"></div>
					<div class="userBox-wrap">
						<div id="slider" class="flexslider">
							<ul class="slides">
								<?php foreach($event_photos as $photos):
								$img_url = base_url('event_photos/'.$event_info['event_id'].'/'.$photos); ?>
								<li><img class="lazy" style="max-height:98%; max-width:100%;" src="<?php echo base_url('assets/images/load_event.jpg')?>" data-src="<?php echo $img_url;?>" /></li>
								<?php endforeach;?>
							</ul>
						</div>
						<div id="carousel" class="flexslider mar-top2">
							<ul class="slides">
								<?php foreach($event_photos as $photos):
								$img_url = base_url('event_photos/'.$event_info['event_id'].'/'.$photos); ?>
								<li><img class="lazy" style="max-height:98%; max-width:100%;" src="<?php echo base_url('assets/images/load_event.jpg')?>" data-src="<?php echo $img_url;?>" /></li>
								<?php endforeach;?>
							</ul>
						</div>				
					</div>
					<?php else:?>
						<div class="Edit-p-top1"><?php echo translate_phrase("No Photos added Yet");?>.</div>									
					<?php endif;?>
				</div>
				<?php else:?>
					<div class="Edit-p-top1"><?php echo translate_phrase("There are currently no upcoming events scheduled in "). get_current_city() ;?>.</div>					
				<?php endif;?>
			</div>
		</div>			
	</div>
</div>
