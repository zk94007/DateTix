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
<script src="<?php echo base_url()?>assets/js/general.js"></script>
<style>
	.user-photo-slider{text-align:center;}
	.img-slide img{max-width:100%;}
	.img-left-box{position: relative; width: 200px; margin:0 auto; float:none; clear:both;}
	.thumb-list{position: relative; text-align:center; width: 300px;  margin:0 auto; float:none; clear:both;}
	.text-center{text-align: center;}
	.locationArea p,.locationArea a{font-size: 20px; line-height:24px}
</style>
<script type="text/javascript">
$(document).ready(function() {
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
			},
			buttons : {}
		}
	});	
	$('.bxslider_profile').bxSlider({
		  pagerCustom: '#bx-pager',
		  infiniteLoop:false,
		  nextSelector: '#slider-next',
                  prevSelector: '#slider-prev',
		  nextText: '<img src="<?php echo base_url()?>assets/images/l-arw-img.png" alt="" />',
		  prevText: '<img src="<?php echo base_url()?>assets/images/r-arw-img.png" alt="" />'
	});
});


function followMerchant(id,type){
loading();
        $.ajax({ 
	    url: '<?php echo base_url(); ?>' +"dates/follow_merchant", 
	    type:"post",
	    data:{merchant_id:id,type:type},
	    cache: false,
	    success: function (response) {
	    	stop_loading();
	        //window.location.reload();
                if(type=='1'){
                    $('#followButton').hide();
                    $('#unfollowButton').show();
                }else{
                    $('#unfollowButton').hide();
                    $('#followButton').show();
                }
	    }     
	});
        
}
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="left">
				<a class="font-18" href="javascript:;" onclick="history.back();" style='font-size: 20px'>
                                    <?php echo translate_phrase('< Back'); ?>
                                </a>
			</div>
			<div class="step-form-Main">
				<div class="step-form-Part">
					<div class="edu-main">							
						<div class="aps-d-top">		
								<?php if(isset($merchant_info['merchant_photos']) && $merchant_info['merchant_photos']): ?>
								<div class="img-left-box">
									<ul class="bxslider_profile">
										<?php foreach ($merchant_info['merchant_photos'] as $photo): ?>
										<li class="img-slide">
											<a class="fancybox-thumb" rel="gallery" href="<?php echo $photo['photo_url'];?>">
												<img style="height: 180px" src="<?php echo $photo['photo_url'] ?>" alt="<?php echo $photo['photo_url'] ?>"/>
											</a>
										</li>
										<?php endforeach; ?>
									</ul>
									<div class="outside">
										<div id="slider-next" class="l-arw-img slider-next"></div>
										<div id="slider-prev" class="r-arw-img slider-prev"></div>
									</div>
								</div>
									
								<div class="sml-img thumb-list" id="bx-pager">
								<?php if (isset($merchant_info['merchant_photos']) && $merchant_info['merchant_photos']): ?>
								<?php foreach ($merchant_info['merchant_photos'] as $key => $photo): ?>
									<a data-slide-index="<?php echo $key; ?>" href="javascript:;"> <img
										height="50" src="<?php echo $photo['photo_url']; ?>"
										alt="<?php echo $photo['photo_url'] ?>" /> </a>
										<?php endforeach; ?>
										<?php endif; ?>
								</div>
				
								<?php else:?>
								<?php echo translate_phrase('No photos added yet');?>
								<?php endif; ?>								
						</div>	
						<div class="aps-d-top">
							
							<h3 class="mar-top2 text-center"><?php echo $merchant_info['name']?></h3>
							
							<h3 class="text-center DarkGreen-color mar-top2"><?php echo $merchant_info['price_range'];?>  </h3>
							
							<div class="text-center mar-top2">3km Away</div>
							
							<div class="mar-top2  txt-center ">								                                                                    
									<?php echo $merchant_info['address'];?>  
									<a target="_blank" href="http://maps.google.com/?q=<?php echo $merchant_info['address'];?>" >
                                                                            <?php echo translate_phrase('View Map');?></a>									
									<br/>
									<?php echo $merchant_info['phone_number'];?>
                                                                        
								<?php if($merchant_info['website_url'] != ''):?>
									<p><a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo $merchant_info['website_url'];?>"><?php echo $merchant_info['website_url'];?></a></p>
								<?php endif;?>
								
							</div>
						</div>	
						<div class="aps-d-top">
                                                        <?php if(isset($merchant_info['merchant_date_types']) && $merchant_info['merchant_date_types']):?>
                                                       
                                                                <?php foreach($merchant_info['merchant_date_types'] as $key=>$row){?>											                                                                           
                                                                    <a href="javascript:;" class="inline-element"> 
                                                                        <span class="disable-butn" style="cursor: default"><?php echo $row['description']; ?></span>
                                                                    </a>
                                                                <?php }?>					

                                                        <?php endif;?>
                                                </div>
						
												<div class="aps-d-top ">
                                                    <!--<span class="Edit-Button01 mar-R left inline-element">
                                                        <a href="tel:<?php echo $merchant_info['phone_number'];?>">
                                                            <?php echo translate_phrase('Call')?>
                                                        </a>
                                                    </span>
                                                    -->    	
                                                    <div class="inline-element">
                                                        <a  class="left" style="display: <?php echo (empty($checkMerchantFollow)) ? 'block' : 'none' ?>" href="javascript:;" id="followButton" onclick="return followMerchant('<?php echo $merchant_info['merchant_id'];?>','1')">
                                                            <span class="appr-cen">Follow</span>
                                                        </a>
                                                        <a class="left" style="display: <?php echo (!empty($checkMerchantFollow)) ? 'block' : 'none' ?>" href="javascript:;" id="unfollowButton" onclick="return followMerchant('<?php echo $merchant_info['merchant_id'];?>','0')">
                                                            <span class="appr-cen btn-blue">Unfollow</span>
                                                        </a>
                                                    </div>
                                                </div>
					</div>
				</div>							
			</div>				
		</div>
	</div>
</div>
