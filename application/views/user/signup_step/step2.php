<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script src="<?php echo base_url()?>assets/js/jquery.bxslider.min.js"></script>
<script type="text/javascript">
	var base_url = '<?php echo base_url() ?>';
	var slider = null;
	var user_id = '<?php echo $this->session->userdata('user_id');?>';
	var docWidth = jQuery(document).width();
	var currnetSchoolName = '';
	var currnetCompanyName = '';

$(document).ready(function () {
   
   $(".dropdown-dt").find('dt a').live('click',function () {
		$(this).parent().parent().find('ul').toggle();
	});
	
	$(".dropdown-dt dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
    	$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
	});
		
	$(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("dropdown-dt"))
        	$(".dropdown-dt dd ul").hide();
    });
    
    
    $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});
	
   var total_groups = $(".bxslider_profile").length;
   if(total_groups  > 0)
   {
	   if($(".bxslider_profile li").length  > 3)
		{
			jQuery('#slider-next,#slider-prev').fadeIn(); 
		}
            
		var i = 0; // counts how many galleries are initted
		slider = $('.bxslider_profile').bxSlider({
				  pagerCustom: '#bx-pager',
				  nextSelector: '#slider-next',
				  prevSelector: '#slider-prev',
				  slideWidth: 151,
				  minSlides: 1,
				  maxSlides: 3,
				  infiniteLoop:false,
				  nextText: '<img src="<?php echo base_url()?>assets/images/p-right-arw.jpg" alt="" />',
				  prevText: '<img src="<?php echo base_url()?>assets/images/p-left-arw.jpg" alt="" />',
				  onSliderLoad: function() {
					i++;                        
			  }
		});
	}
    
    if(docWidth <= 360)
    {
		if($(".bxslider_profile li").length  > 1)
		{
			jQuery('#slider-next,#slider-prev').fadeIn(); 
		}
    }

	if(docWidth <= 768)
	{
		if($(".bxslider_profile li").length  > 2)
		{
			jQuery('#slider-next,#slider-prev').fadeIn(); 
		}
	}
	
	$("#add_profile_photo").fileupload({
            dataType: 'json',
            add: function (e, data) {
	        	 
        	    if(isMobileView == 'No' || isMobileView != 'No')
                {
        	    	var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
	   	        	var uploadErrors = [];
	   	            if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
	   	                 uploadErrors.push(invalidImgType);
	   	            }
	   	             //in bytes
		   	        if(data.originalFiles[0]['size'] > 10000000) {
			   	        uploadErrors.push(invalidImgSize);
		   	        }
                    if(uploadErrors.length > 0) {
                    	if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
                    	{
                    		$(this).parent().parent().parent().append('<label class="input-hint mobile-error error-msg">'+uploadErrors.join("\n")+'</label>');
                    	}
                    }
                    else
                    {
                    	$('body').css('cursor', 'wait');
                    	data.submit();
                   	}
                	
                }
                else
                {
                	if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
                	{
                		$(this).parent().parent().parent().append(mobileErrorMsg);
                	}
            		return false;
                }
            },
            done: function (e, data) {
                if (data.result.success === 1) {
				var photo_id = data.result.id;
				var img_url = data.result.url;
				var liHTML ='';
				if($(this).parent().parent().parent().find('.mobile-error').length > 0)
				   {
							$(this).parent().parent().parent().find('.mobile-error').text('');
				   }

			   if($(".bxslider_profile").length == 0)
				{
					liHTML += '<ul class="bxslider_profile">';
                        }
                        liHTML += '<li class="photo_'+photo_id+'">';
                        liHTML += '<div class="upload-part"><img height="150"  src="'+img_url+'" alt="img" /></div>';
                        if(data.result.set_primary == '1')
                        {
                        	liHTML += '<div class="Photo-dwn-btn"><div class="primary-btn-text"><label lang="'+photo_id+'" class="primary-action" data-url="'+base_url+'user/primary_profile_photo/" ><?php echo translate_phrase("Set as Primary")?></label></div>';
                        }
                        else
                        {
                         	liHTML += '<div class="Photo-dwn-btn"><div class="Primary-Button01"><label lang="'+photo_id+'" class="primary-action" data-url="'+base_url+'user/primary_profile_photo/"  onclick="make_primary_photo('+photo_id+',this)" ><?php echo translate_phrase("Set as Primary")?></label></div>';
                        }
                       liHTML += '<div class="Delete-Primary"><a href="javascript:;" onclick="delete_photos('+photo_id+',this)" data-url="'+base_url+'user/delete_profile_photo/"><?php echo translate_phrase("Delete")?></a></div>';
                        liHTML += '</div></li>';
                        
                        if($(".bxslider_profile").length == 0)
                        {
                            liHTML += '</ul>';

                            $("#user-photo-container").html(liHTML);

                            slider = $('.bxslider_profile').bxSlider({
                                    pagerCustom: '#bx-pager',
                                    nextSelector: '#slider-next',
                                    prevSelector: '#slider-prev',
                                    slideWidth: 151,
                                    minSlides: 1,
                                    maxSlides: 3,
                                    infiniteLoop:false,
                                    nextText: '<img src="<?php echo base_url()?>assets/images/p-right-arw.jpg" alt="" />',
                                    prevText: '<img src="<?php echo base_url()?>assets/images/p-left-arw.jpg" alt="" />',
                            });
                        }
                        else
                        {
                                $("ul.bxslider_profile").append(liHTML);
                                slider.reloadSlider();
                        }
                        if($(".bxslider_profile li").length  > 3)
                        {
                            jQuery('#slider-next,#slider-prev').fadeIn(); 
                        }
                        resizeSlider();
                        $('body').css('cursor', 'auto');
              }
              else
              {
				if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
	 			{
					$(this).parent().parent().parent().append('<label class="input-hint mobile-error error-msg">'+data.result.success+'</label>');
                }
              }
              $('body').css('cursor', 'auto');
            }
	});

	$("#submit_button").click(function(){
		$('#signupForm').submit();
	})
});

function make_primary_photo(id,btn){
   	$.post($(btn).attr('data-url'), {'id': id}, function(data) {
   		if(data == '1')
   		{
   			$.each($(".step1-photo-slider-wrapper .primary-btn-text").find("label"),function(i,item){
   				$(item).parent().removeClass('primary-btn-text').addClass('Primary-Button01');
   				$(item).attr('onclick',"make_primary_photo('"+$(item).attr('lang')+"',this)").text('Set as Primary');
   			})
   			
   			$(".photo_"+id).find(".Primary-Button01").removeClass('Primary-Button01').addClass('primary-btn-text');
   			$(".photo_"+id).find(".primary-action").attr("onclick","").text('Primary Photo');
                        jQuery('.photo_'+id).fadeOut(function(){
                            var firstLi = jQuery('.bxslider_profile').children().first();
                            jQuery(this).insertBefore(jQuery(firstLi));
                            slider.reloadSlider();
                        });
   		}
   	});
}

function delete_photos(id,btnDelete){
   
   	$.post($(btnDelete).attr('data-url'), {'id': id}, function(data) {                
		$(".photo_"+id).remove()		
		if(jQuery(".bxslider_profile li").length == 0)
		{
			jQuery('#slider-next,#slider-prev').fadeOut();
			jQuery(".bx-viewport").parent().fadeOut();
			
		}
		
		$(this).remove();
		slider.goToPrevSlide();
		var docWidth = jQuery(document).width();
		if(docWidth <= 360)
		{
			if(jQuery(".bxslider_profile li").length < 2)
			{
				jQuery('#slider-next,#slider-prev').fadeOut();
			}
		}
		else
		{
			if(jQuery(".bxslider_profile li").length < 4)
			{
				jQuery('#slider-next,#slider-prev').fadeOut();
			}
		}
		
		slider.reloadSlider();
	 
   	});
}
function get_height(value){
	if(value=='height'){ 
		var height  = $('#height').val();
		$('#user_height').val(height.trim());
	}
	if(value=='feet' || value=='inches'){
		var feet             = $('#feet').val();
		var inches           = $('#inches').val();
		if(feet!="" && inches!=""){
			var inch_to_feet = inches* 0.083333;
			feet             =  parseInt(feet)+inch_to_feet;
			var cm           = feet * 30.48;
			$('#user_height').val(Math.round(cm));
		   
		}else{
			 $('#user_height').val('');
		}
	}
}
    
jQuery(window).resize(function(){
	resizeSlider();             
});
function resizeSlider()
{
	//slider.reloadSlider();    
	var documentWidth = jQuery(document).width(); 
	//320
	if(documentWidth <= 640)
	{
	   if($(".bxslider_profile li").length  > 1)
	   {
		  jQuery('#slider-next,#slider-prev').fadeIn(); 
	   }
	}
	else
	{
	   /*
		if($(".bxslider_profile li").length  < 5)
		{
			jQuery('#slider-next,#slider-prev').fadeOut(); 
		}
		*/
		if(documentWidth <= 768)
		{
			if($(".bxslider_profile li").length  > 2)
			{
				jQuery('#slider-next,#slider-prev').fadeIn(); 
			}
		}
	   /*
	   if(documentWidth <= 640)
	   {
		if($(".bxslider_profile li").length  < 3)
		{
		   jQuery('#slider-next,#slider-prev').fadeOut(); 
		}
	   }
	   else
	   {
				if($(".bxslider_profile li").length  < 5)
				{
					jQuery('#slider-next,#slider-prev').fadeOut(); 
				}
	   }
	   */
	}
}
function scrollToDiv(id)
{
	$('body').scrollTo($('#'+id),800,{'axis':'y'});
}


function photos_validaion(){
    var flag=1;
    var use_meters = jQuery('#use_meters').val();
    if(use_meters == 1)
    {
        if ($('#height').val()=='') {
        // show_val_message('Please specify your height', 'error', 'signup',
		// "height_err");
        showError('heightError','<?php echo translate_phrase("Height  is required");?>');
        flag=0;
        }
        else
        {
            jQuery('#heightError').text('');

        }
    }
    else
    {
        var feet = jQuery('#feet_id').val();
        var inch = jQuery('#inches_id').val();
        if(feet == "" || inch == "")
        {
            showError('heightError','<?php echo translate_phrase("Height  is required");?>');
            flag=0;
        }
        else
        {
            jQuery('#heightError').text('');
        }
    }
    
    
    if ($('#ethnicity').val()=='') {
      	showError('ethnicityError','<?php echo translate_phrase("Please specify your ethnicity");?>');
        flag=0;
    }
    else
    {
        jQuery('#ethnicityError').text('');
    }
    
    if ($('#bodyTypeId').val()=='') {
        showError('bodyTypeError','<?php echo translate_phrase("Body type is required");?>');
        flag=0;
    }
    else
    {
        jQuery('#bodyTypeError').text('');
        
    }
    
    if ($('#lookId').val()=='') {
        showError('looksError','<?php echo translate_phrase("Please select your looks preferences");?>');
        flag=0;
    }
    else
    {
        jQuery('#looksError').text('');
        
    }
    
    if ($('#district').val()=='') {
        showError('districtError',$('#districtError').attr('error_txt'));
        flag=0;
    }
    else
    {
        $('#districtError').text('');
    }
    
    
    if($(".bxslider_profile li").length == 0)
    {
    	flag = 0;
    	$("#user-photo-container").siblings('.file-upload').append('<label class="input-hint mobile-error error-msg"> <?php echo translate_phrase("Please upload profile picture");?>.</label>');    	
    }
    else
    {
    	$("#user-photo-container").siblings('.file-upload').find('label.mobile-error').slideUp();
    }
    
    if(flag==0)
        return false;
    else   
        return true;
}
function save_data()
{
	if(photos_validaion())
	{
		$("#signupForm").submit();
    }
}
</script>
<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="signupForm"
			action="<?php echo base_url().url_city_name() . '/'.$this->singup_name.'-2.html';?>"
			method="post" enctype="multipart/form-data">
			
			<label class="input-hint error"><?php echo $this->session->flashdata('edit_profile_msg_error');?></label>
			<div id="photos">
					<div class="Apply-Step1-a-main">
						<!-- Header complete -->
						<div class="Thanks-verify">
							<span class="Th-highlight"><?php echo translate_phrase("Please tell us a bit more about yourself.")?>
							</span>
						</div>
						<div class="step-form-Main">
							<div class="step-form-Part">
								<div class="Indicate-top">*&nbsp; <?php echo translate_phrase('Indicates required field')?></div>
<?php /* 								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('My height is')?>
										:<span>*</span>
									</div>
									<div class="sfp-1-Right">

									<?php
									//$use_meters=0;
									if($use_meters=='1'){
										$show_height = "style='display:block;'";
										$show_feet   = "style='display:none;'";
									} else{
										$show_height = "style='display:none;'";
										$show_feet   = "style='display:block;'";
									}

									$feet_id   = $this->input->post('feet')?$this->input->post('feet'):"";
									$inch_id   = $this->input->post('inches')?$this->input->post('inches'):"";
									$cms_id   = $this->input->post('cms')?$this->input->post('cms'):"";
									echo form_dt_dropdown('height',$cms,$cms_id,'id="cm" class="dropdown-dt" '.$show_height,'',"hiddenfield");
									?>
										<input type="hidden" id="use_meters"
											value="<?php echo $use_meters?>">
										<div class="centimeter" <?php echo $show_height?>>cm</div>
										<?php echo form_dt_dropdown('feet_id',$feet,$cms_id,'id="feet" class="dropdown-dt" '.$show_feet,'',"hiddenfield");?>
										<div class="centimeter" <?php echo $show_feet?>>feet</div>
										<?php echo form_dt_dropdown('inches_id',$inches,$inch_id,'id="inches" class="dropdown-dt" '.$show_feet,'',"hiddenfield");?>
										<div class="centimeter pad-rightNone" <?php echo $show_feet?>>inches</div>
										<label id="heightError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
 
<?php */?>
							<?php if($ethnicity):?>
							<div class="sfp-1-main">
								<div class="sfp-1-Left"> <?php echo translate_phrase('My ethnicity is')?> :<span>*</span></div>
								<div class="sfp-1-Right">
									
									<?php
									$ethnicity_id = $this->input->post('ethnicity')?$this->input->post('ethnicity'):"";
									foreach($ethnicity as $id=>$value):?>
									<a href="javascript:;" class="rdo_div" key="<?php echo $id;?>"><span class="<?php echo ($id == $ethnicity_id)?'appr-cen':'disable-butn';?>"><?php echo $value;?></span></a>
									<?php endforeach;?>
									<input type="hidden" name="ethnicity" id="ethnicity" value="<?php echo $ethnicity_id?>">
									<label id="ethnicityError" class="input-hint error error_indentation error_msg"></label>									
								</div>
							</div>
							<?php endif; ?>
							
							<?php $selected_relationship_status  = $fb_user_data['relationship_status']?$fb_user_data['relationship_status']:set_value('relationship_status');?>									
							<input type="hidden" name="relationship_status" value="<?php echo $selected_relationship_status?>">
							
							<?php $religious_belief_id   = isset($fb_user_data['religionId']) ? $fb_user_data['religionId'] :'';?>
							<input type="hidden" id="religiousBeliefId" name="religiousBeliefId" value="<?php echo $religious_belief_id;?>">
							
							<?php if($district):?>
							<div class="sfp-1-main">
								<div class="sfp-1-Left"> <?php echo translate_phrase('I live in')?> :<span>*</span></div>
								<div class="sfp-1-Right">
								<?php if($has_district==0)
									$show_district  = 'style="display: none;"';
									else
									$show_district  = 'style="display: block;"';

									$district_id = $this->input->post('district')?$this->input->post('district'):"";
									echo form_dt_dropdown('district',$district,$district_id,'class="dropdown-dt reqdropdown"',translate_phrase('Select neighborhood'),"hiddenfield");?>
									<label id="districtError" class="input-hint error" error_txt="<?php echo translate_phrase("Please select neighborhood") ?>"></label>
								</div>
							</div>
							<?php endif;?>
								
							<?php
								if($postal_code_exist!="0")
								$show_postal_code  = 'style="display: block;"';
								else
								$show_postal_code  = 'style="display: none;"';
	
	
								if($country_name == 'United States')
								{
									$zipLabel = 'Zip Code';
								}
								else
								{
									$zipLabel = 'Postal Code';
								}
								?>
<!--								<div class="sfp-1-main" <?php echo $show_postal_code?>>
									<div class="sfp-1-Left">
									<?php echo translate_phrase($zipLabel)?> :
									</div>
									<div class="sfp-1-Right">
										<input id="postal_code" class="post-input" name="postal_code"
											type="text" style="width: 100px">
									</div>
								</div>																-->

								<div class="sfp-1-main">
									<div class="sfp-1-Left"><?php echo translate_phrase('Add profile photos')?> :<span>*</span></div>
									<div class="sfp-1-Right file-upload1">
										<!--  <div class="profile-phot-M"> -->
										<div class="Pleft-arw" id="slider-prev" style="display: none"></div>
										<div class="step1-photo-slider-wrapper" id="user-photo-container">
											<?php if ($user_photos || isset($fb_user_data['photo'])): ?>
											<ul class="bxslider_profile">
											<?php if ($user_photos):?>
											<?php foreach ($user_photos as $key => $photo): ?>
												<li class="photo_<?php echo $photo['user_photo_id'] ?>">
													<div class="upload-part">
														<img height="170" width="140"
															src="<?php if ($photo['url']): ?><?php echo $photo['url'] ?><?php else: echo base_url() ?>assets/images/default-profile.png<?php endif ?>"
															alt="<?php echo $photo['photo'] ?>" />
													</div>
													<div class="Photo-dwn-btn">
													<?php if ($photo['set_primary'] == '1'): ?>
														<div class="primary-btn-text">
															<label lang="<?php echo $photo['user_photo_id'] ?>"
																class="primary-action"
																data-url="<?php echo base_url() ?>user/primary_profile_photo/"><?php echo translate_phrase('Primary Photo') ?>
															</label>
														</div>
														<?php else: ?>
														<div class="Primary-Button01">
															<label lang="<?php echo $photo['user_photo_id'] ?>"
																class="primary-action"
																onclick="make_primary_photo('<?php echo $photo['user_photo_id'] ?>',this)"
																data-url="<?php echo base_url() ?>user/primary_profile_photo/"><?php echo translate_phrase('Set as Primary') ?>
															</label>
														</div>
														<?php endif; ?>
														<div class="Delete-Primary">
															<a href="javascript:;"
																onclick="delete_photos('<?php echo $photo['user_photo_id'] ?>',this)"
																data-url="<?php echo base_url() ?>user/delete_profile_photo/"><?php echo translate_phrase('Delete') ?>
															</a>
														</div>
													</div>
												</li>
												<?php endforeach; ?>
												<?php endif;?>
												<?php if(isset($fb_user_data['photo']) && $fb_user_data['photo']):?>
												<?php foreach($fb_user_data['photo'] as $photo) :?>
												<li>
													<div class="upload-part">
														<img height="170" width="140"
															src="<?php echo $photo['source']?>" />
													</div>
													<div class="Photo-dwn-btn" style="text-align: center">
														<a href="javascript:;" class="primary-action"><?php echo translate_phrase('Source: FB') ?>
														</a>
													</div>
												</li>
												<?php endforeach;?>
												<?php endif;?>
											</ul>
											<?php endif; ?>
										</div>
										<div class="Pright-arw" id="slider-next" style="display: none"></div>
										<!-- </div> Photo div edit profile complete -->
										<!--  
											<?php if($this->agent->is_mobile()):?>
											<div style="float:left;margin-top:15px;">
												<label class="input-hint error"> <?php echo translate_phrase('Photo uploading is not yet supported on mobile devices. Please upload photos using your desktop PC after we approve your application.');?></label>
											</div>
											<?php else:?>
											  <?php endif;?>
										-->
										
										<div class="Pf-btnM file-upload mar-top2">
											<span class="upload-button">
												<label><?php echo translate_phrase('Add Photo...') ?></label>
												<input type="file" data-url="<?php echo base_url() ?>user/upload/fileToUpload" id="add_profile_photo" name="fileToUpload">
											</span>
										</div>
									<?php if(!isset($fb_user_data) || !$fb_user_data):?>
									
									<div class="Edit-p-top2">
										<a class="facebook-import" href="<?php echo base_url().'fb_login/import_data/'.$this->singup_name.'-2'?>"><img src="<?php echo base_url().'assets/images/fb-btn-logo.jpg'?>" /> <?php echo translate_phrase('Import from Facebook');?></a>
									</div>
									<?php endif;?>
										<!--  </div> old div sfp-1-Right file-upload -->
									</div>
								</div>
							</div>
						</div>
						<div class="Nex-mar">
							<a href="javascript:;" id="ureg_sub" onclick="save_data();"
								class="Next-butM"><?php echo translate_phrase('Next')?> </a>
						</div>
					</div>
				</div>
		</form>
	</div>
</div>