<script src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script
	src="<?php echo base_url()?>assets/js/general.js"></script>

<script type="text/javascript">
var base_url = '<?php echo base_url() ?>';
var slider = null;
var user_id = '<?php echo $this->session->userdata('user_id');?>';
var docWidth = jQuery(document).width();
var currnetSchoolName = '';
var currnetCompanyName = '';
</script>
<?php $this->load->view('template/datetix_js');?>

<script
	src="<?php echo base_url()?>assets/js/jquery.bxslider.min.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
        // Drop down js
	//When click on dropdown then his ul is open..
	$(".dropdown-dt").find('dt a').live('click',function () {
            $(this).parent().parent().find('ul').toggle();
        });

	//When select a option..
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
	// END Dropdown

	/* Dynamic Checkbox */
	$('.ckbox_multi-select-gender').live('click',function(){
		var lang = $(this).find('img').attr('src');
		$(this).find('img').attr('src',$(this).attr('lang'));
		$(this).attr('lang',lang);
		if($(this).attr('isselected') == 'no')
		{
			$(this).siblings(':input[type="hidden"]').val($(this).attr('key'));
			$(this).attr('isselected','yes');
		}
		else
		{
			$(this).siblings(':input[type="hidden"]').val('');
			$(this).attr('isselected','no');
		}

	});
	/* End Checkbox*/
	/*  Dynamic Checkbox based on selecting a Div element*/
	$(".ckb_div_lookfor").live('click',function(){
            if($(this).find('span').hasClass('disable-butn') == true)
            {
                    //Add Selected [checked]
                    $(this).find('span').removeClass('disable-butn').addClass('appr-cen');
                    $(this).find(':input[type="hidden"]').val($(this).attr('key'));
            }
            else
            {
                    //Remove Selected [checked]
                    $(this).find('span').removeClass('appr-cen').addClass('disable-butn');
                    $(this).find(':input[type="hidden"]').val('');
            }
	});

	$(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).find(':input[type="hidden"]').val($(this).attr('key'));
	});
        
        $('#your-personality ul li a').click(function(e) {
	        e.preventDefault();
	        var li = $(this).parent();
	        if ($(li).hasClass('selected')) {
	          // remove
	          var ids           = new Array();
	          var desc_id       = $('#descriptive_word_id').val(); 
	          ids               = desc_id.split(',');
	          var index         = ids.indexOf(this.id);
	          ids.splice(index, 1);
	          var descriptive_id      = ids.join(); 
	          $("#descriptive_word_id").val(descriptive_id);
	          $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
	        } else {
	          // check before adding
	          if ($('#your-personality ul li.selected').length < 5) {
	            var descriptive_id   = $('#descriptive_word_id').val();
	            if(descriptive_id!="")
	                var dsc_id       = descriptive_id+','+this.id; 
	            else
	                var dsc_id       = this.id;

	            $("#descriptive_word_id").val(dsc_id);
	            $(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
	          }
	        }
	   });
	/* End Checkbox */
	/*---------------for managing interests------------------*/
        $('#hobbiesAndInterest ul li a').click(function(e) {
	        e.preventDefault();
	        var li = $(this).parent();
	        if ($(li).hasClass('selected')) {
	          // remove
	          var ids           = new Array();
	          var desc_id       = $('#interestWordId').val(); 
	          ids               = desc_id.split(',');
	          var index         = ids.indexOf(this.id);
	          ids.splice(index, 1);
	          var descriptive_id      = ids.join(); 
	          $("#interestWordId").val(descriptive_id);
	          $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
	        } else {
	          // check before adding
	          //if ($('#your-personality ul li.selected').length < 5) {
	            var descriptive_id   = $('#interestWordId').val();
	            if(descriptive_id!="")
	                var dsc_id       = descriptive_id+','+this.id; 
	            else
	                var dsc_id       = this.id;

	            $("#interestWordId").val(dsc_id);
	            $(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
	          //}
	        }
	   });
        /*---------------for managing interests------------------*/
	/* Dynamic Radio Button */
	$('#rdo_select-gender-female').live('click',function(){
		if($(this).attr('isselected') == 'no')
		{	
                    var lang = $(this).find('img').attr('src');
                    $(this).find('img').attr('src',$(this).attr('lang'));
                    $(this).attr('lang',lang);
                    $(this).attr('isselected','yes');

                    if($("#rdo_select-gender-male").attr('isselected') == 'yes' )
                    {
                            $("#rdo_select-gender-male").attr('isselected','no');
                            var otherFieldLang = $("#rdo_select-gender-male").find('img').attr('src');
                            $("#rdo_select-gender-male").find('img').attr('src',$("#rdo_select-gender-male").attr('lang'));
                            $("#rdo_select-gender-male").attr('lang',otherFieldLang);
                    }
                    $("#gender").val($(this).attr('key'));
		}
	});

	//gender
	$('#rdo_select-gender-male').live('click',function(){
		if($(this).attr('isselected') == 'no')
		{	
                    var lang = $(this).find('img').attr('src');
                    $(this).find('img').attr('src',$(this).attr('lang'));
                    $(this).attr('lang',lang);
                    $(this).attr('isselected','yes');

                    if($("#rdo_select-gender-female").attr('isselected') == 'yes' )
                    {
                            var otherFieldLang = $("#rdo_select-gender-female").find('img').attr('src');
                            $("#rdo_select-gender-female").attr('isselected','no');
                            $("#rdo_select-gender-female").find('img').attr('src',$("#rdo_select-gender-female").attr('lang'));
                            $("#rdo_select-gender-female").attr('lang',otherFieldLang);
                    }
                    $("#gender").val($(this).attr('key'));
		}
	});


	$('#edit-profile').bind('easytabs:after', function(tab, panel, data){
            
	    //var target_tab_id = panel[0].id;
            
            var target_tab_id = $('#edit-profile ul li.active').attr('id');
            //alert(current_tab_id);
	    if(target_tab_id == 'educationTab')
	    {
	    	goToScroll('school_name');
                
	    }

	    if(target_tab_id == 'basicsTab')
	    {
	    	goToScroll('first_name');
	    } 

	    if(target_tab_id == 'careerTab')
	    {
	    	goToScroll('company_name');
	    }

	    if(target_tab_id == 'othersTab')
	    {
	        $("#city_lived").focus();
	    }
	    
	});

	
	/* End Radio Button*/
	
	/*
	$('.upload-profile-pic').live('click',function(){
            $("#fileToUpload").trigger('click');
        });
        $('.upload-company-pic').live('click',function(){
            $("#photo_business_card").trigger('click');
        });

        $('.upload-school-pic').live('click',function(){
            $("#photo_diploma").trigger('click');
        });
	*/

        //tabbing js
	if($(".bxslider_profile").length == 0)
	{
            $('#edit-profile').easytabs();
	}
	
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
                        if (i >= total_groups) {
                            $('#edit-profile').easytabs();
                        }
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
        
        
        $("#add_profile_photo").fileupload({
            dataType: 'json',
            add: function (e, data) {
                $('body').css('cursor', 'wait');
                data.submit();
            },
            done: function (e, data) {
                if (data.result.success === 1) {
			var photo_id = data.result.id;
			var img_url = data.result.url;
			var liHTML ='';


                        if($(".bxslider_profile").length == 0)
			{
				liHTML += '<ul class="bxslider_profile">';
                        }
                        liHTML += '<li class="photo_'+photo_id+'">';
                        liHTML += '<div class="upload-part"><img height="150"  src="'+img_url+'" alt="img" /></div>';
                        if(data.result.set_primary == '1')
                        {
                        	liHTML += '<div class="Photo-dwn-btn"><div class="primary-btn-text"><a href="javascript:;" lang="'+photo_id+'" class="primary-action" data-url="'+base_url+'user/primary_profile_photo/" ><?php echo translate_phrase("Set As Primary")?></a></div>';
                        }
                        else
                        {
                         	liHTML += '<div class="Photo-dwn-btn"><div class="Primary-Button01"><a href="javascript:;" lang="'+photo_id+'" class="primary-action" data-url="'+base_url+'user/primary_profile_photo/"  onclick="make_primary_photo('+photo_id+',this)" ><?php echo translate_phrase("Set As Primary")?></a></div>';
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
                        $('body').css('cursor', 'auto');
              } 
              else
              {
                console.log(data.result.success);
              }
            }
      });
    });
    
    
         jQuery(window).resize(function(){
            
                //slider.reloadSlider();
                var documentWidth = jQuery(document).width(); 
                if(documentWidth <= 360)
                {
                    if($(".bxslider_profile li").length  > 1)
                    {
                       jQuery('#slider-next,#slider-prev').fadeIn(); 
                    }
                }
                else
                {
                    if(documentWidth <= 640)
                    {
	   		if($(".bxslider_profile li").length  < 3)
                        {
                            jQuery('#slider-next,#slider-prev').fadeOut(); 
                        }
                    }
                    else
                    {

                        if($(".bxslider_profile li").length  < 4)
                        {
                            jQuery('#slider-next,#slider-prev').fadeOut(); 
                        }
                    } 
                 /*   if($(".bxslider_profile li").length  < 2)
                    {
                       jQuery('#slider-next,#slider-prev').fadeOut(); 
                    }*/
                }
               
        });
</script>

<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="signup"
			action="<?php echo base_url().url_city_name() . '/signup-step-1.html';?>"
			method="post" enctype="multipart/form-data">
			<div id="edit-profile" class="edit-profile-content">
				<ul class='etabs' style="display: none;">
					<li class='tab' id="basicsTab"><a href="#basics" title="Basics"><?php echo translate_phrase('Basics')?>
					</a></li>
					<li class='tab' id="photosTab"><a href="#photos" title="Photos"><?php echo translate_phrase('Photos')?>
					</a></li>
					<li class='tab' id="educationTab"><a href="#education"
						title="Education"><?php echo translate_phrase('Education')?> </a>
					</li>
					<li class='tab' id="careerTab"><a href="#career" title="Career"><?php echo translate_phrase('Career')?>
					</a></li>
					<li class='tab' id="personalityTab"><a href="#personality"
						title="Personality"><?php echo translate_phrase('Personality')?> </a>
					</li>
					<li class='tab' id="othersTab"><a href="#others" title="Others"><?php echo translate_phrase('Others')?>
					</a></li>
				</ul>

				<!--*********Apply-Step1-A-Page basics start*********-->
				<div id="basics" class="Apply-Step1-a-main">
					<!--<div class="subttle">&nbsp;</div>-->
					<div class="A-step-partM">
						<div class="step-backBG">
							<div class="step-BOX-Main">
								<div class="step-bg-selected">
									<span>1</span>
								</div>
								<div class="step-ttle">
								<?php echo translate_phrase('Describe Yourself')?>
								</div>
							</div>
							<div class="step-BOX-Main mar-auto">
								<div class="step-bg-Unselected">
									<span>2</span>
								</div>
								<div class="step-ttle">
								<?php echo translate_phrase('Your Dating Preferences')?>
								</div>
							</div>
							<div class="step-BOX-Main fr wh-clr">
								<div class="step-bg-Unselected">
									<span>3</span>
								</div>
								<div class="step-ttle">
								<?php echo translate_phrase('Submit Application')?>
								</div>
							</div>
						</div>
					</div>
					<div class="Thanks-verify">
						<span class="Th-highlight"> <?php if (isset($is_return_apply) && !$is_return_apply): echo translate_phrase($this->session->userdata('succ_email_verify')); endif?>
						<?php echo translate_phrase(" Please start by telling us a bit about yourself. ")?>
						</span>
					</div>
					<div class="step-form-Main">
						<div class="step-form-Part">
							<div class="Indicate-top">
								*&nbsp;
								<?php echo translate_phrase('Indicates required field')?>
							</div>
							<div class="sfp-1-main">
								<div class="sfp-1-Left">
								<?php echo translate_phrase("I'm:")?>
									<span>*</span>
								</div>
								<div class="sfp-1-Right">
								<?php
								foreach($gender as $row){
									$gender_id   = $fb_user_data['gender']?$fb_user_data['gender']:set_value('gender');?>
									<?php if($row['gender_id'] == 2 ):?>
									<?php if($row['gender_id'] == $gender_id):?>
									<div class="male-icn-but">
										<a href="javascript:;" key="<?php echo $row['gender_id']?>"
											id="rdo_select-gender-female" isselected="yes"
											lang="<?php echo base_url() ?>assets/images/femail-icn.png"><img
											src="<?php echo base_url() ?>assets/images/femail-icn-selected.png"
											alt="" /> </a>
									</div>
									<?php else:?>
									<div class="male-icn-but">
										<a href="javascript:;" key="<?php echo $row['gender_id']?>"
											id="rdo_select-gender-female" isselected="no"
											lang="<?php echo base_url() ?>assets/images/femail-icn-selected.png"><img
											src="<?php echo base_url() ?>assets/images/femail-icn.png"
											alt="" /> </a>
									</div>
									<?php endif;?>
									<?php endif;?>

									<?php if($row['gender_id'] == 1 ):?>
									<?php if($row['gender_id']==$gender_id):?>
									<div class="male-icn-but">
										<a href="javascript:;" key="<?php echo $row['gender_id']?>"
											id="rdo_select-gender-male" isselected="yes"
											lang="<?php echo base_url() ?>assets/images/male-icn.png"><img
											src="<?php echo base_url() ?>assets/images/male-icn-selected.png"
											alt="" /> </a>
									</div>
									<?php else:?>
									<div class="male-icn-but">
										<a href="javascript:;" key="<?php echo $row['gender_id']?>"
											id="rdo_select-gender-male" isselected="no"
											lang="<?php echo base_url() ?>assets/images/male-icn-selected.png"><img
											src="<?php echo base_url() ?>assets/images/male-icn.png"
											alt="" /> </a>
									</div>
									<?php endif;?>
									<?php endif;?>

									<?php } ?>
									<input type="hidden" name="gender" id="gender"
										value="<?php echo $row['gender_id']?>">
									<div class="sfp-1-Desktop">
									<?php
									$ethnicity_id = $this->input->post('ethnicity')?$this->input->post('ethnicity'):"";
									echo form_dt_dropdown('ethnicity',$ethnicity,$ethnicity_id,'id="ethnicityId" class="dropdown-dt domaindropdown"',translate_phrase('Select ethnicity'),"hiddenfield");
									?>
										<label id="genderError"
											class="input-hint error error_indentation error_msg"></label>
										<label id="ethnicityError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
							</div>
							<div class="sfp-1-main">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I wwerwerant to date:')?>
									<span>*</span>
								</div>
								<div class="sfp-1-Right">

								<?php
								foreach($gender as $row){

									if(!empty($fb_user_data['want_date']))
									{
										if(in_array($row['gender_id'],$fb_user_data['want_date']))
										{$checked= "checked";}
										else
										{$checked= "";}
									}
									else
									{
										$want_to_date_id    = $this->input->post('want_to_date')?$this->input->post('want_to_date'):array();
										if(in_array($row['gender_id'],$want_to_date_id))
										{$checked= "checked";}
										else
										{$checked= "";}
									}
									?>

									<?php if($row['gender_id'] == 2 ):?>
									<?php
									if($checked == 'checked')
									{
										$lang   = base_url().'assets/images/femail-icn.png';
										$imgSrc = base_url().'assets/images/femail-icn-selected.png';
										$isSelected = 'yes';
									}
									else
									{
										$lang   = base_url().'assets/images/femail-icn-selected.png';
										$imgSrc = base_url().'assets/images/femail-icn.png';
										$isSelected = 'no';
									}
									?>

									<!--               	    	<div class="male-icn-but">
                                <a href="javascript:;" key="<?php echo $row['gender_id']?>" class="ckbox_multi-select-gender" isselected="no" lang="<?php echo base_url() ?>assets/images/femail-icn-selected.png"><img src="<?php echo base_url() ?>assets/images/femail-icn.png" alt="" /></a>
                                <input type="hidden" name="want_to_date[]" value="<?php echo ($checked != "")?$row['gender_id']:'';?>">
                                </div>-->

									<div class="male-icn-but">
										<a href="javascript:;" key="<?php echo $row['gender_id']?>"
											class="ckbox_multi-select-gender"
											isselected="<?php echo $isSelected?>"
											lang="<?php echo $lang ?>"><img src="<?php echo $imgSrc?>"
											alt="" /> </a> <input type="hidden" name="want_to_date[]"
											value="<?php echo ($checked != "")?$row['gender_id']:'';?>">
									</div>
									<?php endif;?>

									<?php if($row['gender_id'] == 1 ):?>
									<?php
									if($checked == 'checked')
									{
										$lang   = base_url().'assets/images/male-icn.png';
										$imgSrc = base_url().'assets/images/male-icn-selected.png';
										$isSelected = 'yes';
									}
									else
									{
										$lang   = base_url().'assets/images/male-icn-selected.png';
										$imgSrc = base_url().'assets/images/male-icn.png';
										$isSelected = 'no';
									}
									?>
									<div class="male-icn-but">
										<a href="javascript:;" key="<?php echo $row['gender_id']?>"
											class="ckbox_multi-select-gender"
											isselected="<?php echo $isSelected ?>"
											lang="<?php echo $lang ?>"><img src="<?php echo $imgSrc ?>"
											alt="" /> </a> <input type="hidden" class="ckb_want_to_date"
											name="want_to_date[]"
											value="<?php echo ($checked != "")?$row['gender_id']:'';?>">
									</div>
									<?php endif;?>
									<?php } ?>
									<label id="wantToDateError"
										class="input-hint error error_indentation error_msg"></label>
								</div>
							</div>
							<div class="sfp-1-main">
								<div class="sfp-1-Left">
								<?php echo translate_phrase("I'm looking for")?>
									<span>*</span>
								</div>
								<div class="sfp-1-Right">
								<?php foreach($relationship_type as $row){
									$looking_for_id    = $this->input->post('looking_for')?$this->input->post('looking_for'):array();
									if(in_array($row['relationship_type_id'],$looking_for_id)){$checked_class= "appr-cen";}else{$checked_class= "disable-butn";}
									?>
									<a href="javascript:;" class="ckb_div_lookfor"
										key="<?php echo $row['relationship_type_id'];?>"> <span
										class="<?php echo $checked_class?>"><?php echo translate_phrase(ucfirst($row['description']));?>
									</span> <input type="hidden" name="looking_for[]" value=""> </a>
									<?php } ?>
									<label id="lookingForError"
										class="input-hint error error_indentation error_msg"></label>
								</div>

							</div>
							<div class="sfp-1-main">
								<div class="sfp-1-Left">
								<?php echo translate_phrase("I currently live in:")?>
									<span>*</span>
								</div>
								<?php
								/*$current_country      = $fb_user_data['location']['country']?$fb_user_data['location']['country'] :$country_name;
								 $city                 = $fb_user_data['location']['city']?$fb_user_data['location']['city'] :$city_name;
								 if($current_country=="" && $city!="")
								 $current_country  = $this->model_user->get_country_by_city($city);*/
								?>

								<div class="sfp-1-Right">
									<label
										style="color: #4b4b4b; float: left; padding-top: 6px; font-family: 'Conv_MyriadPro-Regular'"><?php echo translate_phrase($city_name.',&nbsp;'.$country_name); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
									<!--<dl class="dropdown-dt filterdropdown">
                  <dt>
                  	<a href="javascript:;"><span><?php echo translate_phrase($city).', '.translate_phrase($current_country);?></span></a>
                  	 <input type="hidden" name="current_location" id="current_location" value="">
                  </dt>
                  <dd>
                    <ul>
                      <li><a href="javascript:;" key="<?php echo $city;?>"><?php echo $city;?></a></li>
                    </ul>
                  </dd>
                </dl>-->
									<div class="LivingElse">
										<a
											href="<?php echo base_url() . url_city_name() ?>/change-city.html?return_to=<?php echo return_url() ?>">
											<?php echo translate_phrase('Living elsewhere')?>?</a>
									</div>
									<label id="liveInError"
										class="input-hint error error_indentation error_msg"></label>
								</div>

							</div>
							<div class="sfp-1-main">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('Neighborhood')?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								if($has_district==0)
								$show_district  = 'style="display: none;"';
								else
								$show_district  = 'style="display: block;"';

								$district_id = $this->input->post('district')?$this->input->post('district'):"";
								echo form_dt_dropdown('district',$district,$district_id,'class="dropdown-dt reqdropdown"',translate_phrase('Select district'),"hiddenfield");
								?>
								</div>
							</div>
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
							<div class="sfp-1-main" <?php echo $show_postal_code?>>
								<div class="sfp-1-Left">
								<?php echo translate_phrase($zipLabel)?>
									:
								</div>
								<div class="sfp-1-Right">
									<input id="postal_code" class="post-input" name="postal_code"
										type="text" style="width: 100px">
								</div>
							</div>

						</div>
					</div>
					<div class="Nex-mar">
						<a href="javascript:;" id="ureg_sub" onclick="next_step1();"
							class="Next-butM"><?php echo translate_phrase('Next')?> </a>
					</div>
				</div>
				<!--*********Apply-Step1-A-Page close*********-->

				<!--*********Apply-Step1-B-Page photos start*********-->
				<div id="photos">
					<div class="Apply-Step1-a-main">
						<!-- <div class="subttle">&nbsp;</div>-->
						<!-- FORM Header start (Progress) -->
						<div class="A-step-partM">
							<div class="step-backBG">
								<div class="step-BOX-Main">
									<div class="step-bg-selected">
										<span>1</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Describe Yourself')?>
									</div>
								</div>
								<div class="step-BOX-Main mar-auto">
									<div class="step-bg-Unselected">
										<span>2</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Your Dating Preferences')?>
									</div>
								</div>
								<div class="step-BOX-Main fr wh-clr">
									<div class="step-bg-Unselected">
										<span>3</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Submit Application')?>
									</div>
								</div>
							</div>
						</div>
						<!-- Header complete -->
						<div class="Thanks-verify">
							<span class="Th-highlight"><?php echo translate_phrase("Tell us a bit more about yourself to help us better find great matches for you")?>.</span>
						</div>
						<div class="step-form-Main">
							<div class="step-form-Part">
								<div class="Indicate-top">
									*&nbsp;
									<?php echo translate_phrase('Indicates required field')?>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('I was born on')?>
										:<span>*</span>
									</div>
									<div class="sfp-1-Right">
									<?php
									if(!empty($fb_user_data['dob'])){
										$syear  = $fb_user_data['dob']['y']?$fb_user_data['dob']['y']:set_value('year');
										$smonth = $fb_user_data['dob']['m']?$fb_user_data['dob']['m']:set_value('month');
										$sdate  = $fb_user_data['dob']['d']?$fb_user_data['dob']['d']:set_value('date');
									}else{
										$syear  = $this->input->post('year')?$this->input->post('year'):"";
										$smonth = $this->input->post('month')?$this->input->post('month'):"";
										$sdate  = $this->input->post('date')?$this->input->post('date'):"";
									}
									echo form_dt_dropdown('yearId',$year,$syear,'id="year" class="dropdown-dt"',translate_phrase('Year'),"hiddenfield");
									echo form_dt_dropdown('monthId',$month,$smonth,'id="month" class="dropdown-dt dd-menu-mar" ',translate_phrase('Month'),"hiddenfield");
									echo form_dt_dropdown('dateId',$date,$sdate,'id="day" class="dropdown-dt dd-menu-mar" ',translate_phrase('Day'),"hiddenfield");
									?>
										<label id="dobError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
										My height is:<span>*</span>
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
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('My body type is:')?>
										<span>*</span>
									</div>
									<div class="sfp-1-Right">
									<?php
									$body_type_id   = $this->input->post('body_type')?$this->input->post('body_type'):"";
									echo form_dt_dropdown('bodyTypeId',$body_type,$body_type_id,'id="body_type" class="dropdown-dt dropdownfull"','Select body type',"hiddenfield");
									?>
										<label id="bodyTypeError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('I believe I look:')?>
										<span>*</span>
									</div>
									<div class="sfp-1-Right">

									<?php
									$looks_id   = $this->input->post('looks')?$this->input->post('looks'):"";
									//echo form_dropdown('looks',$looks,$looks_id,'id="looks"');
									echo form_dt_dropdown('lookId',$looks,$looks_id,'id="looks" class="dropdown-dt dropdownfull"','',"hiddenfield");
									?>
										<label id="looksError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>

								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('My relationship status is')?>
										:
									</div>
									<div class="sfp-1-Right">

									<?php foreach($relationship_status as $row){
										$relationship_status  = $fb_user_data['relationship_status']?$fb_user_data['relationship_status']:set_value('relationship_status');
										if($relationship_status==$row['relationship_status_id'])
										$checked_class="appr-cen";
										else
										$checked_class= "disable-butn";

										?>
										<a href="javascript:;" class="rdo_div"
											key="<?php echo $row['relationship_status_id'];?>"> <span
											class="<?php echo $checked_class?>"><?php echo translate_phrase(ucfirst($row['description']));?>
										</span> <input type="hidden" name="relationship_status"
											value=""> </a>
											<?php } ?>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase("I'm")?>
										:<span>*</span>
									</div>
									<div class="sfp-1-Right">
									<?php
									//$religious_belief_id   = $this->input->post('religious_belief')?$this->input->post('religious_belief'):"";
									$religious_belief_id   = isset($fb_user_data['religionId']) ? $fb_user_data['religionId'] :'';
									echo form_dt_dropdown('religiousBeliefId',$religious_belief,$religious_belief_id,'id="religious_belief" class="dropdown-dt dropdownfull religionDD"','Select religion',"hiddenfield");
									?>
										<label id="religiousBeliefError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('Upload profile photos')?>
										:
									</div>
									<div class="sfp-1-Right file-upload1">
										<!--  <div class="profile-phot-M"> -->
										<div class="Pleft-arw" id="slider-prev" style="display: none"></div>
										<div class="step1-photo-slider-wrapper"
											id="user-photo-container">
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
															<a href="javascript:;"
																lang="<?php echo $photo['user_photo_id'] ?>"
																class="primary-action"
																data-url="<?php echo base_url() ?>user/primary_profile_photo/"><?php echo translate_phrase('Set As Primary') ?>
															</a>
														</div>
														<?php else: ?>
														<div class="Primary-Button01">
															<a href="javascript:;"
																lang="<?php echo $photo['user_photo_id'] ?>"
																class="primary-action"
																onclick="make_primary_photo('<?php echo $photo['user_photo_id'] ?>',this)"
																data-url="<?php echo base_url() ?>user/primary_profile_photo/"><?php echo translate_phrase('Set As Primary') ?>
															</a>
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

										<div class="Pf-btnM file-upload">
											<div class="Next-butM" style="margin-top: 10px; height: 32px">
												<span class="upload-button"> <label><?php echo translate_phrase('Add Photo...') ?>
												</label> <input type="file"
													data-url="<?php echo base_url() ?>user/upload/fileToUpload"
													id="add_profile_photo" name="fileToUpload"> </span>
											</div>
										</div>
										<!--  </div> old div sfp-1-Right file-upload -->
									</div>
								</div>
							</div>
						</div>
						<div class="Nex-mar">
							<a href="javascript:;" id="ureg_sub" onclick="next_step1();"
								class="Next-butM"><?php echo translate_phrase('Next')?> </a>
						</div>
					</div>
				</div>
				<!--*********Apply-Step1-B-Page close*********-->
				<!--*********Apply-Step1-C-Page education start*********-->
				<div id="education">
					<div class="Apply-Step1-a-main">
						<!-- FORM Header start (Progress) -->
						<div class="A-step-partM">
							<div class="step-backBG">
								<div class="step-BOX-Main">
									<div class="step-bg-selected">
										<span>1</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Describe Yourself')?>
									</div>
								</div>
								<div class="step-BOX-Main mar-auto">
									<div class="step-bg-Unselected">
										<span>2</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Your Dating Preferences')?>
									</div>
								</div>
								<div class="step-BOX-Main fr wh-clr">
									<div class="step-bg-Unselected">
										<span>3</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Submit Application')?>
									</div>
								</div>
							</div>
						</div>
						<!-- Header complete -->

						<div class="step-form-Main">
							<div class="step-form-Part">
								<div class="Indicate-top">
									*&nbsp;
									<?php echo translate_phrase('Indicates required field')?>
								</div>
								<div class="edu-main">
									<h2>
									<?php echo translate_phrase('What education degrees/certificates have you achieved or are working towards')?>
										?<span class="Redstar">*</span>
									</h2>
									<div class="skill-select">
									<?php foreach($education_level as $row){
										$relationship_status  = (isset($fb_user_data['education_level']) && $fb_user_data['education_level'])?$fb_user_data['education_level']:set_value('education_level');

										if($relationship_status==$row['education_level_id'])
										$checked_class="appr-cen";
										else
										$checked_class= "disable-butn";
										?>
										<a href="javascript:;" class="ckb_div_lookfor"
											key="<?php echo $row['education_level_id'];?>"> <span
											class="<?php echo $checked_class?>"><?php echo translate_phrase(ucfirst($row['description']));?>
										</span> <input type="hidden" name="education_level[]" value="">
										</a>
										<?php } ?>
										<label id="education_level_err" class="input-hint error"></label>
									</div>
									<div class="skill-select">
										<h2>
										<?php echo translate_phrase('Where and what did you study')?>
											?<span class="Redstar">*</span>
										</h2>
										<label id="schoolReqError" class="input-hint error"></label>
									</div>
									<?php if($school_count>0){
										$schol_show         = 'style="display: none;"';
										$schhol_button_show = 'style="display: block;"';
									} else{
										$schol_show          = 'style="display: block;"';
										$schhol_button_show  = 'style="display: none;"';
									}
									?>
									<div class="school-inner-container" id="list_school_main"
									<?php echo $schhol_button_show;?>>
										<div class="study-innr-M" id="list_school">
										<?php
										foreach($user_school_id as $row ){
											$language_id        = 1;
											$school_details     = $this->model_user->get_school_details($row,$language_id);
											echo $list_school   = $this->model_user->list_school_details($row,$school_details,$language_id);
										}
										?>
										</div>
										<div class="Edit-Button01" id="add_school_button">
											<a onclick="show_div();" href="javascript:;">Add Another
												School</a>
										</div>
									</div>

								</div>

								<div class="last-bor"></div>
								<div id="add_schools" class="fl">
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('School name')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<div class="drop-down-wrapper-full">
												<dl class="schooldowndomain">
													<dt>
														<span> <input id="school_name"
															class="Degree-input school-name" name="school_name"
															type="text" value="" onkeyup="auto_complete_school();"
															onblur="show_logo();show_school_domain();get_school_id();">
														</span>
													</dt>
													<!-- autocomplete dd -->
													<dd id="auto-school-container"></dd>
												</dl>
												<label id="schoo_name_err" class="input-hint error"></label>
												<label id="school_error" class="input-hint error"></label> <input
													type="hidden" id="selectedFromAvailableSchools" value="no">
											</div>

											<div class="sch-logoR" id="school_logo"></div>
										</div>
									</div>

									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Degree')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<input id="degree_name" class="Degree-input"
												name="degree_name" type="text"
												placeholder="<?php echo translate_phrase("e.g. Bachelor of Arts");?>">
											<div class="completed-ch">
												<div class="skil-check-area-01">
													<ul>
														<li><span> <input type="checkbox" checked="checked"
																name="is_degree_completed" id="is_degree_completed"
																value="1"> <label class="choice"
																for="is_degree_completed"><?php echo translate_phrase('Completed');?>
															</label> </span>
														</li>
													</ul>
												</div>
											</div>
											<label id="degree_err" class="input-hint error"></label>
										</div>

									</div>

									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Major(s)')?>
											:
										</div>
										<div class="sfp-1-Right">
											<div class="M-topBut">
												<ul id="add_major" class="list_rows"></ul>
											</div>

											<div class="drop-down-wrapper-full">
											<?php   echo form_dt_dropdown('major_id',$school_subject,'','id="major_id" class="dropdown-dt majordowndomain"',translate_phrase("Select majors"));?>
												<label id="major_err" class="input-hint error"></label>
											</div>

											<div class="add-butM">
												<input type="hidden" name="majors_id" id="majors_id"
													value=""> <a href="javascript:;" onclick="add_majors();"
													class="Edit-Button01"><?php echo translate_phrase('Add')?>
												</a>
											</div>

										</div>
									</div>

									<div class="sfp-1-main">

										<div class="sfp-1-Left">
										<?php echo translate_phrase('Minors(s)')?>
											:
										</div>
										<div class="sfp-1-Right">
											<div class="M-topBut">
												<ul id="add_minor" class="list_rows"></ul>
											</div>
											<div class="drop-down-wrapper-full">
											<?php echo form_dt_dropdown('major_id',$school_subject,'','id="minor_id" class="dropdown-dt majordowndomain"',translate_phrase("Select minors"));?>
												<label id="minor_err" class="input-hint error"></label>
											</div>
											<div class="add-butM">
												<a href="javascript:;" onclick="add_minors();"
													class="Edit-Button01"><?php echo translate_phrase('Add')?>
												</a> <input type="hidden" name="minors_id" id="minors_id"
													value="">
											</div>
										</div>
									</div>

									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Years Attended')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">

										<?php
										//from years
										for($i=date('Y');$i>=1910;$i--){
											$school_years[$i] = $i;
										}

										//to years
										for($i=date('Y')+5;$i>=1910;$i--){
											$school_years_to[$i] = $i;
										}

										?>
										<?php echo form_dt_dropdown('years_attended_start',$school_years,'','id="attended_start" class="dropdown-dt"','---',"hiddenfield");?>
											<div class="centimeter">
											<?php echo translate_phrase('to')?>
											</div>
											<?php echo form_dt_dropdown('years_attended_end',$school_years_to,'','id="attended_end" class="dropdown-dt "','---',"hiddenfield");?>
											<div class="centimeter">
											<?php echo translate_phrase('(or expected graduation year)');?>
											</div>
											<label id="years_attended_error" class="input-hint error"></label>
										</div>
									</div>
									<div class="edu-ystudy padB-none">
										<h2>
										<?php echo translate_phrase('Optional verification information');?>
										</h2>
										<div class="study-innr-M">
											<div class="sfp-1-main">
												<div class="sfp-1-Left">
													<a href="#"><img
														src="<?php echo base_url() ?>assets/images/question-mark.png"
														class="que-mark" alt="" /> </a>School Email:
												</div>
												<div class="sfp-1-Right">
													<div class="post-input-wrap">
														<input id="school_email_address"
															name="school_email_address" type="text"
															class="post-input" /> <label id="school_email_error"
															class="input-hint error"></label>
													</div>

													<div class="drop-down-wrapper-school_domain">
														<div class="sel-emailR" id="school_domain"></div>
														<label id="school_domain_err" class="input-hint error"></label>
													</div>

												</div>
											</div>
											<div class="sfp-1-main">
												<div class="sfp-1-Left">
												<?php echo translate_phrase('Take a photo of your diploma or school ID and send it to us')?>
													:
												</div>
												<div class="sfp-1-Right file-upload">
													<ul class="img-container">
														<!--<li class="upload-part upload-school-pic"> <img src="" alt="profile-photo" data-src="<?php if ($fb_user_data['photo']): ?><?php echo $fb_user_data['photo'] ?><?php else: echo base_url() ?>assets/images/photo01.jpg<?php endif ?>"></li>-->
														<li class="upload-part upload-school-pic"><img src=""
															data-src="<?php if (isset($fb_user_data['photo'])) echo $fb_user_data['photo'] ?>">
														</li>
													</ul>
													<div class="upload-Button-main">
														<span class="upload-button"> <label><?php echo translate_phrase('Upload Photo...') ?>
														</label> <input type="file" name="photo_diploma"
															id="photo_diploma"
															data-url="<?php echo base_url() ?>user/upload/photo_diploma">
														</span>
														<div class="Delete-Photo01">
															<a href="javascript:;"
																data-url="<?php echo base_url() ?>user/delete_edu_temp_photo/"><?php echo translate_phrase('Delete') ?>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="New-add-s">
										<input type="hidden" value="" name="user_school_id"
											id="user_school_id">
										<div class="Edit-Button01">
											<a onclick="add_school();" href="javascript:;"
												id="school_button"><?php echo translate_phrase('Add School')?>
											</a>
										</div>
										<div class="Delete-Photo01">
											<a href="javascript:;" onclick="cancel_school();"
												id="cancel_button"><?php echo translate_phrase('Cancel')?> </a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="Nex-mar">
							<a href="javascript:;" id="ureg_sub" onclick="next_step1();"
								class="Next-butM"><?php echo translate_phrase('Next')?> </a>
						</div>

					</div>
				</div>
				<!--*********Apply-Step1-C-Page close*********-->

				<!--*********Apply-Step1-D-Page career start*********-->
				<div id="career">
					<div class="Apply-Step1-a-main">
						<!-- FORM Header start (Progress) -->
						<div class="A-step-partM">
							<div class="step-backBG">
								<div class="step-BOX-Main">
									<div class="step-bg-selected">
										<span>1</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Describe Yourself')?>
									</div>
								</div>
								<div class="step-BOX-Main mar-auto">
									<div class="step-bg-Unselected">
										<span>2</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Your Dating Preferences')?>
									</div>
								</div>
								<div class="step-BOX-Main fr wh-clr">
									<div class="step-bg-Unselected">
										<span>3</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Submit Application')?>
									</div>
								</div>
							</div>
						</div>
						<!-- Header complete -->

						<div class="step-form-Main">
							<div class="step-form-Part">
								<div class="Indicate-top">
									*&nbsp;
									<?php echo translate_phrase('Indicates required field')?>
								</div>
								<div class="edu-main">
									<div class="aps-d-top">
										<h2>
										<?php echo translate_phrase('Where are you in your career')?>
											?<span class="Redstar">*</span>
										</h2>
										<div class="care-boxM">
											<!--Change by Hannan-->
										<?php echo form_dt_dropdown('career_stage_id',$carrier_stage,'','class="dropdown-dt rangedowndomain"',translate_phrase('Select career stage '),"hiddenfield");?>
											<label id="careerStageError"
												class="input-hint error error_indentation error_msg"></label>
											<!--Change by Hannan-->
										</div>
									</div>
									<div class="aps-d-top padB-none">
										<h2>
										<?php echo translate_phrase('What is your annual income range')?>
											?
										</h2>
										<div class="care-boxM">
										<?php echo form_dt_dropdown('annual_income_range_id',$annual_income_range,'','class="dropdown-dt rangedowndomain"',translate_phrase('Select annual income range'),"hiddenfield");?>
										</div>
									</div>
									<div class="edu-ystudy">
										<h2>
										<?php echo translate_phrase('What kind of work do you do')?>
											?<span class="Redstar">*</span>
										</h2>
										<?php
										if($company_count>0){
											$company_show        = 'style="display: none;"';
											$company_button_show = 'style="display: block;"';
										} else{
											$company_show        = 'style="display: block;"';
											$company_button_show = 'style="display: none;"';
										}
										?>
										<div class="school-inner-container" id="list_company_main"
										<?php echo $company_button_show;?>>
											<div class="study-innr-M" id="list_company">
											<?php

											foreach($user_company_id as $row ){
												$language_id        = 1;
												$company_details    = $this->model_user->get_company_details($row,$language_id);
												echo $list_company  = $this->model_user->list_company_details($row,$company_details,$language_id);
											}
											?>
											</div>
											<div <?php echo $company_button_show;?> class="Edit-Button01"
												id="add_company_button">
												<a onclick="show_div_company();" href="javascript:;"><?php echo translate_phrase('Add Another Job')?>
												</a>
											</div>
										</div>
									</div>
								</div>
								<div class="last-bor"></div>
								<div id="add_companies" class="fl">
									<span class="suc-msg" id="school_company"></span>

									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Company name')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<div class="drop-down-wrapper-full">
												<dl class="schooldowndomain">
													<dt>
														<span> <input class="Degree-input company-name"
															id="company_name" name="company_name" type="text"
															onkeyup="auto_complete_company();"
															onblur="show_company_logo();show_company_industry();show_company_domain();" />
														</span>
													</dt>
													<!-- autocomplete dd -->
													<dd id="auto-company-container" class="autosuggestfull"></dd>
												</dl>
												<label id="company_name_err" class="input-hint error"></label>
												<input type="hidden" id="selectedFromAvailableCompanies"
													value="no">
											</div>

											<div class="sch-logoR" id="company_logo"></div>

											<div class="M-topBut">
												<div class="skil-check-area-01">
													<ul>
														<li><span> <input type="checkbox" checked="checked"
																value="1" id="show_company_name" class="field checkbox">
																<label class="choice" for="show_company_name"><?php echo translate_phrase('Show company name to potential dates')?>
															</label> </span>
														</li>
													</ul>
												</div>
											</div>
										</div>

									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Company industry')?>
											:
										</div>
										<div class="sfp-1-Right" id="company_industry">
										<?php echo form_dt_dropdown('industry_id',$industry,'','class="dropdown-dt majordowndomain company_industry_dd_id"',translate_phrase(' Select industry '),"hiddenfield");?>
										</div>
									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
											<a href="#"><img
												src="<?php echo base_url()?>assets/images/question-mark.png"
												class="que-mark" alt="" /> </a>
												<?php echo translate_phrase('Job function')?>
											:
										</div>
										<div class="sfp-1-Right" id="job_function_dd">
										<?php echo form_dt_dropdown('job_function_id',$job_functions,'','class="dropdown-dt majordowndomain"',translate_phrase('Select Job function'),"hiddenfield");?>
										</div>
									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
											<a href="#"><img
												src="<?php echo base_url()?>assets/images/question-mark.png"
												class="que-mark" alt="" /> </a>
												<?php echo translate_phrase('Job title')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<div class="post-input-wrap">
												<input id="job_title" type="text" class="Degree-input"
													placeholder="e.g. Software Engineer" /> <label
													id="job_title_err" class="input-hint error"></label>
											</div>
										</div>
									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Job location')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<dl class="schooldowndomain">
												<dt>
													<span>
														<div class="post-input-wrap">
															<input id="job_city" name="job_city" type="text"
																class="Degree-input"
																placeholder="<?php echo translate_phrase('City name, country name')?>"
																onclick="auto_complete_city(this)" /> <label
																id="job_city_id_err" class="input-hint error"></label>
														</div> </span>
												</dt>
												<!-- autocomplete dd -->
												<dd id="auto-city-containter" class="autosuggestfull"></dd>
											</dl>

										</div>
									</div>
									<div class="sfp-1-main">
									<?php
									//from years
									for($i=date('Y');$i>=1910;$i--){
										$company_years[$i] = $i;
									}

									//to years
									for($i=date('Y');$i>=1910;$i--){
										$company_years_to[$i] = $i;
									}

									?>
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Years Worked')?>
											:
										</div>
										<div class="sfp-1-Right">
										<?php echo form_dt_dropdown('years_worked_start', $school_years, '', 'id="years_worked_start_dl" class="dropdown-dt drop-start-year"', ' - - -', "hiddenfield"); ?>
											<div class="centimeter">
											<?php echo translate_phrase('to') ?>
											</div>
											<?php echo form_dt_dropdown('years_worked_end', $school_years, '', 'id="years_worked_end_dl" class="dropdown-dt drop-end-year"', ' - - -', "hiddenfield"); ?>

										</div>
									</div>
									<div class="edu-ystudy padB-none">
										<h2>
										<?php echo translate_phrase('Optional Verification Information')?>
										</h2>
										<div class="study-innr-M">
											<div class="sfp-1-main">
												<div class="sfp-1-Left">
													<a href="#"><img
														src="<?php echo base_url()?>assets/images/question-mark.png"
														class="que-mark" alt="" /> </a>Company Email:
												</div>

												<div class="sfp-1-Right">
													<div class="post-input-wrap">
														<input id="company_email_address"
															name="company_email_address" type="text"
															class="post-input" /> <label id="company_email_error"
															class="input-hint error"></label>
													</div>

													<div class="drop-down-wrapper-school_domain">
														<div class="sel-emailR" id="company_domain"></div>
														<label id="company_domain_err" class="input-hint error"></label>
													</div>
												</div>

											</div>
											<div class="sfp-1-main">
												<div class="sfp-1-Left">
												<?php echo translate_phrase('Take a photo of your business card and upload it here:')?>
												</div>
												<div class="sfp-1-Right file-upload">
													<ul class="img-container">
														<li class="upload-part upload-company-pic"><img src=""
															data-src=""></li>
													</ul>
													<div class="upload-Button-main">
														<span class="upload-button"> <label><?php echo translate_phrase('Upload Photo...') ?>
														</label> <input type="file" name="photo_business_card"
															id="photo_business_card"
															data-url="<?php echo base_url()?>user/upload/photo_business_card">
														</span>
														<div class="Delete-Photo01">
															<a href="javascript:;"
																data-url="<?php echo base_url() ?>user/delete_edu_temp_photo/"><?php echo translate_phrase('Delete') ?>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="New-add-s">
										<input type="hidden" value="" name="user_company_id"
											id="user_company_id">
										<div class="Edit-Button01">
											<a onclick="add_company();" href="javascript:;"
												id="company_button"><?php echo translate_phrase('Add Job')?>
											</a>
										</div>
										<div class="Delete-Photo01">
											<a href="javascript:;" onclick="cancel_company();"
												id="company_cancel_button"><?php echo translate_phrase('Cancel')?>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="Nex-mar">
							<a href="javascript:;" id="ureg_sub"
								onclick="go_next_to_company();" class="Next-butM"><?php echo translate_phrase('Next')?>
							</a>
						</div>
					</div>
				</div>
				<!--*********Apply-Step1-D-Page close*********-->

				<!--*********Apply-Step1-E-Page personality start*********-->
				<div id="personality">
					<div class="Apply-Step1-a-main">

						<!-- <div class="subttle">&nbsp;</div>-->
						<div class="A-step-partM">
							<div class="step-backBG">
								<div class="step-BOX-Main">
									<div class="step-bg-selected">
										<span>1</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Describe Yourself')?>
									</div>
								</div>
								<div class="step-BOX-Main mar-auto">
									<div class="step-bg-Unselected">
										<span>2</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Your Dating Preferences')?>
									</div>
								</div>
								<div class="step-BOX-Main fr wh-clr">
									<div class="step-bg-Unselected">
										<span>3</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Submit Application')?>
									</div>
								</div>
							</div>
						</div>
						<div class="step-form-Main">
							<div class="step-form-Part">
								<div class="edu-main">
									<div class="aps-d-top">
										<h2>
										<?php echo translate_phrase('What five words would your friends use to describe you')?>
											?
										</h2>
										<div class="f-decrMAIN" id="your-personality">
											<div class="f-decr">
												<ul>
												<?php foreach($descriptive_word as $row){?>
													<li id="<?php echo $row['descriptive_word_id'];?>"><a
														class="disable-butn" href="javascript:;"
														id="<?php echo $row['descriptive_word_id'];?>"><?php echo ucfirst($row['description']);?>
													</a></li>
													<?php }?>
												</ul>
												<input type="hidden" id="descriptive_word_id"
													name="descriptive_word_id" value="">
											</div>
										</div>
									</div>
									<div class="last-bor"></div>
									<!-----------------------NEW ADDITION--------------------------->
									<div class="aps-d-top">

									<?php
									if(!empty($interests))
									{
										echo '<h2>'.translate_phrase('What are your interests and hobbies').'</h2>';
										foreach ($interests['parentDetails'] as $id => $catName)
										{
											echo '<div class="f-decrMAIN" id=hobbiesAndInterest>
                                       <h3>'.$catName.'</h3>
                                       <div class="f-decr">
                                       <ul>';
											foreach ($interests['childDetails'][$id] as $key => $value)
											{
												echo '<li id ="'.$value->interest_id.'"><a class="disable-butn" href="javascript:;" id="'.$value->interest_id.'">'.$value->description.'</a></li>';
											}

											echo '</ul></div>
                                      </div>';
										}

										echo '<input type="hidden" id="interestWordId" name="interests">';
									}
									?>

									</div>

								</div>
							</div>
							<div class="Nex-mar">
								<a href="javascript:;" id="ureg_sub" onclick="next_step1();"
									class="Next-butM"><?php echo translate_phrase('Next')?> </a>
							</div>
						</div>
					</div>
				</div>
				<!--*********Apply-Step1-E-Page close*********-->

				<!--*********Apply-Step1-F-Page others start*********-->
				<div id="others">
					<div class="Apply-Step1-a-main">


						<div class="A-step-partM">
							<div class="step-backBG">
								<div class="step-BOX-Main">
									<div class="step-bg-selected">
										<span>1</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Describe Yourself')?>
									</div>
								</div>
								<div class="step-BOX-Main mar-auto">
									<div class="step-bg-Unselected">
										<span>2</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Your Dating Preferences')?>
									</div>
								</div>
								<div class="step-BOX-Main fr wh-clr">
									<div class="step-bg-Unselected">
										<span>3</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Submit Application')?>
									</div>
								</div>
							</div>
						</div>
						<div class="Thanks-verify">
							<span class="Th-highlight"><?php echo translate_phrase('You are almost done! Just write a very brief intro summary of yourself that you would like to tell your potential dates')?>.</span>
						</div>
						<div class="step-form-Main">
							<div class="step-form-Part">
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('My self summary')?>
										:
									</div>
									<div class="sfp-1-Right">
										<textarea name="self_summary" cols="" rows=""
											class="as-E-textarea"></textarea>
									</div>
								</div>

							</div>
						</div>
						<div class="Nex-mar">
							<!--<button id="submit_button" type="submit" name="submit" onclick="return next_step1();" class="Next-butM"><?php //echo translate_phrase('Complete My Profile')?></button>-->
							<input id="submit_button" type="submit" name="submit"
								onclick="return next_step1();" class="Next-butM"
								value="<?php echo translate_phrase('Complete My Profile')?>">
						</div>
					</div>
				</div>
				<!--*********Apply-Step1-F-Page close*********-->

			</div>
			<!-- TAB Container Complete -->
		</form>
	</div>
</div>

<script>
    function get_city(country,city,error){
        var country_id = document.getElementById(country).value;
        $( "#"+city ).select();
        if(country_id!=""){
            $.ajax({ 
                url: '<?php echo base_url(); ?>' +"user/autocomplete_city/", 
                dataType: "json", 
                type:"post",
                data:"id="+country_id,
                cache: false,
                success: function (data) {
                    var i="0";
                    var availableTags=new Array();
                    $.each(data,function(id,description) {
                        availableTags[i]= description  ;
                        i= parseInt(i)+parseInt(1);
                    });
                    $( "#"+city ).autocomplete({
                        source: availableTags
                    });
                }     
            });
         }
    }
    function add_languages(){
        var lang                  = document.getElementById('spoken_language').value;
        var prof                  = document.getElementById('proficiency').value;
        var spoken_language       = document.getElementById('spoken_language').options[document.getElementById('spoken_language').selectedIndex].text;
        var proficiency           = document.getElementById('proficiency').options[document.getElementById('proficiency').selectedIndex].text;
        
        var language_id           = document.getElementById('spoken_language_id').value;
        var language_level_id     = document.getElementById('spoken_language_level_id').value;
        $('#spk_lang_err').removeClass('error_indentation error_msg');
        $('#prof_err').removeClass('error_indentation error_msg');
        
        if(lang!="" && prof!=""){
            
            $('#spk_lang_err').html('');
            $('#prof_err').html('');
            
            
            if(language_id!="") 
                var spoken_language_id    = language_id+','+lang; 
            else  
                var  spoken_language_id   = lang;
            
            if(language_level_id!="")   
                var spoken_language_level_id    = language_level_id+','+prof; 
            else    
                var  spoken_language_level_id   = prof;
            
            if (language_id.indexOf(lang) == -1) {
              $("#spoken_language_id").val(spoken_language_id);
              $("#spoken_language_level_id").val(spoken_language_level_id);
              $("#add_lang").append('<li id="lang'+lang+'" class="delete_list"><span class="plain-text" >'+spoken_language+' ('+proficiency+')</span><span><a href="javascript:remove_language('+"'"+lang+"','"+prof+"'"+');" title="Remove"><img src="<?php echo base_url() ?>assets/images/delete.png"></a></span></li>');
            };
       }else{ 
            if(lang==""){
                $('#spk_lang_err').addClass('error_indentation error_msg');
                $('#spk_lang_err').html('<?php echo translate_phrase("Language is required.")?>');
            }
            if(prof==""){
                $('#prof_err').addClass('error_indentation error_msg');
                $('#prof_err').html('<?php echo translate_phrase("Proficiency is required.")?>');
            }    
       }
    }
    function remove_language(language_id,language_level_id){
        var sp_language_id             = new Array();
        var sp_lang_level_id           = new Array();
        var spoken_language_id         = document.getElementById('spoken_language_id').value;
        var spoken_language_level_id   = document.getElementById('spoken_language_level_id').value;
        
        //remove from language hidden field
        var sp_language_id             = spoken_language_id.split(','); 
        var lang_index                 = sp_language_id.indexOf(language_id);
        sp_language_id.splice(lang_index, 1);
        var spoken_language_id         = sp_language_id.join(); 
        
        //remove from proficiency hidden field
        var sp_lang_level_id           = spoken_language_level_id.split(','); 
        var lang_level_index           = sp_lang_level_id.indexOf(language_level_id);
        sp_lang_level_id.splice(lang_level_index, 1);
        var spoken_language_level_id   = sp_lang_level_id.join(); 
        
        $('#lang'+language_id).remove();
        $("#spoken_language_id").val(spoken_language_id);
        $("#spoken_language_level_id").val(spoken_language_level_id);
        
    }
    function add_living_country(){
        var id             = $("#country_lived").val().trim();
        var country_lived  = document.getElementById('country_lived').options[document.getElementById('country_lived').selectedIndex].text;
        var city_lived     = $("#city_lived").val().trim();
        lived_country_id   = $("#lived_country_id").val().trim();
        lived_city_id      = $("#lived_city_id").val().trim();
        
        if(id!="" && city_lived!=""){
            $('#country_lived_err').html('');
            $('#city_liv_err').html(''); 
            
            if(lived_country_id!="") 
                var country_id    = lived_country_id+','+id; 
            else  
                var  country_id   = id;
           

            //city add
            if(lived_city_id!="") 
                var city_id    = lived_city_id+','+city_lived; 
            else  
                var  city_id   = city_lived;
           
            if (((lived_city_id.toLowerCase()).indexOf(city_lived.toLowerCase()) == -1) || (lived_country_id.indexOf(id) == -1)) {
                
                $("#lived_country_id").val(country_id);
                $("#lived_city_id").val(city_id);
                var city_lived_id  = city_lived.split(' ').join('');
                $("#add_living").append('<li id="living_'+id+'_'+city_lived_id+'" class="delete_list"><span class="plain-text" >'+city_lived+', '+country_lived+'</span><span><a href="javascript:remove_lived_city('+"'"+id+"','"+city_lived+"'"+');" title="Remove"><img src="<?php echo base_url() ?>assets/images/delete.png"></a></span></li>');
            }


       }else{ 
            if(id=="")
                $('#country_lived_err').html('<div style="float: left; width: 328px;color:#FD2080; height: 8px; margin-left:180px;"><?php echo translate_phrase("Country is required.")?></div>');
            if(city_lived=="")
                $('#city_liv_err').html('<div style="float: left; width: 328px;color:#FD2080; height: 8px; margin-left:180px;"><?php echo translate_phrase("City is required.")?></div>');
       }
    }
    function remove_lived_city(country_id,city_id){
        var lived_country_array           = new Array();
        var lived_city_array              = new Array();
        var lived_country_id              = document.getElementById('lived_country_id').value;
        var lived_city_id                 = document.getElementById('lived_city_id').value;
        
        //remove from language hidden field
        var lived_country_array           = lived_country_id.split(','); 
        var country_index                 = lived_country_array.indexOf(country_id);
        lived_country_array.splice(country_index, 1);
        var lived_country_id              = lived_country_array.join(); 
        
        //remove from proficiency hidden field
        var lived_city_array              = lived_city_id.split(','); 
        var city_index                    = lived_city_array.indexOf(city_id);
        lived_city_array.splice(city_index, 1);
        var lived_city_id                 = lived_city_array.join(); 
       
        city_id  = city_id.split(' ').join('');
        $('#living_'+country_id+'_'+city_id).remove();
        $("#lived_city_id").val(lived_city_id);
        $("#lived_country_id").val(lived_country_id);

    }
    function add_nationality(){
        var nationality_id   = document.getElementById('nationality').value;
        var nationality      = document.getElementById('nationality').options[document.getElementById('nationality').selectedIndex].text;
        var added_id         = document.getElementById('nationality_id').value;
        
        if(nationality_id!=""){
            $('#nationality_err').html('');
            if(added_id!="") 
                var nationality_ids    = added_id+','+nationality_id; 
            else  
                var  nationality_ids   = nationality_id;
            if (added_id.indexOf(nationality_id) == -1) {
              $("#nationality_id").val(nationality_ids);
              $("#add_nationality").append('<li id="nationality'+nationality_id+'" class="delete_list"><span class="plain-text" >'+nationality+'</span><span><a href="javascript:remove_nationality('+"'"+nationality_id+"'"+');" title="Remove"><img src="<?php echo base_url() ?>assets/images/delete.png"></a></span></li>');
            };
            
       }else{ 
             $('#nationality_err').html('<div style="float: left; width: 328px;color:#FD2080; height: 8px; margin-left:180px;"><?php echo translate_phrase("Nationality is required")?></div>');
       }
    }
    function remove_nationality(id){
        var nationality_array     = new Array();
        var added_id              = document.getElementById('nationality_id').value;
        
        //remove from language hidden field
        var nationality_array     = added_id.split(','); 
        var nation_index          = nationality_array.indexOf(id);
        nationality_array.splice(nation_index, 1);
        var added_id              = nationality_array.join(); 
        $('#nationality'+id).remove();
        $("#nationality_id").val(added_id);
    }
    function add_interests(){
        var interest      = $('#interest').val().trim();
        var added_id      = $('#interest_id').val();
        if(interest!=""){
            if(added_id!="") 
                var interesr_ids    = added_id+','+interest; 
            else  
                var  interesr_ids   = interest;
            interest_id  = interest.split(' ').join('');

            if (added_id.toLowerCase().indexOf(interest.toLowerCase()) == -1) {
              $("#interest_id").val(interesr_ids);
              $("#add_interest").append('<li id="interest'+interest_id+'" class="delete_list"><span class="plain-text" >'+interest+'</span><span><a href="javascript:remove_interest('+"'"+interest+"'"+');" title="Remove"><img src="<?php echo base_url() ?>assets/images/delete.png"></a></span></li>');
            };

            $("#interest").val('');
       }
    }
    function remove_interest(interest){
        var interest_array     = new Array();
        var added_id           = document.getElementById('interest_id').value;
        var interest_array     = added_id.split(','); 
        var interest_index     = interest_array.indexOf(interest);
        interest_array.splice(interest_index, 1);
        var added_id              = interest_array.join(); 
        interest_id  = interest.split(' ').join('');
        $('#interest'+interest_id).remove();
        $("#interest_id").val(added_id);
    }
    function add_descriptive_word(decriptive_word_id){
        var desc   = $('#descriptive_hidden'+decriptive_word_id).val();
        if(desc=='0'){
            $('#descriptive_hidden'+decriptive_word_id).val('1'); 
            url = '<?php echo base_url(); ?>' +"user/add_descriptive_word/";
        }if(desc=='1'){
            $('#descriptive_hidden'+decriptive_word_id).val('0'); 
            url = '<?php echo base_url(); ?>' +"user/remove_descriptive_word/";
        }
        
        $.ajax({ 
            url:url, 
            type:"post",
            data:"decriptive_word_id="+decriptive_word_id+'&keyword=',
            cache: false,
            success: function (data) {
            }     
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

    
    function remove_photo(id,type){
        $.ajax({ 
            url: '<?php echo base_url(); ?>' +"user/remove_photo/", 
            type:"post",
            data:"id="+id+'&type='+type+'&name='+name,
            cache: false,
            success: function (data) {
                 if(type=='school_photo')
                     $('#school_ph_id').remove();
                 else if(type=='company_photo') 
                     $('#company_ph_id').remove();
                 else 
                     $('#list_photo').remove();
            }     
        });
    }
    function go_next_to_school(){
        if( $('#school_name').val()!="" || $('#degree_name').val()!="" || $('#majors_id').val()!="" || $('#minors_id').val()!="" || $('#years_attended_start').val()!="" ||$('#years_attended_end').val()!="" || $('#school_email_address').val()!="")
            add_school();
        else {
            next_step1();
        }
   }
    function go_next_to_company(){
       if( $('#company_name').val() !="" || $('#job_title').val()!="" || $('#job_city').val()!="" )
            add_company();
       else
            next_step1();
   } 
   function make_primary_photo(id,btn){
   	$.post($(btn).attr('data-url'), {'id': id}, function(data) {
   		if(data == '1')
   		{
   			$.each($(".step1-photo-slider-wrapper .primary-btn-text").find("a"),function(i,item){
   				$(item).parent().removeClass('primary-btn-text').addClass('Primary-Button01');
   				$(item).attr('onclick',"make_primary_photo('"+$(item).attr('lang')+"',this)");
   			})
   			
   			$(".photo_"+id).find(".Primary-Button01").removeClass('Primary-Button01').addClass('primary-btn-text');
   			$(".photo_"+id).find(".primary-action").attr("onclick","");
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
                /*$(".photo_"+id).fadeOut('slow',function(){
                alert(jQuery(".bxslider_profile li").length);    
                if(jQuery(".bxslider_profile li").length == 0)
                {
                    jQuery('#slider-next,#slider-prev').fadeOut();
                    jQuery(".bx-viewport").parent().fadeOut();
                    $(this).remove();
                }
                    
   		});*/
   	});
   }
</script>
