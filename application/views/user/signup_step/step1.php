<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script type="text/javascript">
var base_url = '<?php echo base_url() ?>';
var user_id = '<?php echo $this->session->userdata('user_id');?>';
$(document).ready(function () {
    
        <?php if($SelectedCountry !='0'):?>
                
                $.ajax({ 
                        url: '<?php echo base_url(); ?>' +"signup/get_city_by_country", 
                        type:"post",
                        data:{id:<?php echo $SelectedCountry;?>},
                        cache: false,
                        success: function (data) {
                            stop_loading();
                            $("#current_city_dropdown").html(data);
                        }     
                    });
                    
        <?php endif;?>
	// Drop down js
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
    
    
     $("#ckbox_ul li a").live('click',function(){				
		if($(this).find('span').hasClass('disable-butn') == true)
		{
			//Add Selected [checked]
			$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
			$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
		}
		else
		{
			//Remove Selected [checked]
			$(this).find('span').removeClass('appr-cen').addClass('disable-butn');
			$(this).parent().find(':input[type="hidden"]').val('');
		}		
	});
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
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});
	
	$('#current_country ul li a').live('click',function(){
		loading();
		$.ajax({ 
                        url: '<?php echo base_url(); ?>' +"signup/get_city_by_country", 
                        type:"post",
                        data:{id:$(this).attr('key')},
                        cache: false,
                        success: function (data) {
                            stop_loading();
                            $("#current_city_dropdown").html(data);
                        }     
                    });
	});
});

function signup_step1_validaion(){
    var flag=1; 
    if($('#gender').val()==''){
        showError('genderError','<?php echo translate_phrase("Please specify your gender")?>');
        flag=0;
    }
    else
    {
        jQuery('#genderError').text('');
    }
    
    if(!check_checkbox(jQuery("input[name='want_to_date[]']"))){
       showError('wantToDateError','<?php echo translate_phrase("Please specify your gender preference(s)")?>');
        flag=0;
    }
    else
    {
        jQuery('#wantToDateError').text('');
    }
    
    if($('#yearId').val()=="" || $('#monthId').val()=="" || $('#dateId').val()==""){ 
        showError('dobError','<?php echo translate_phrase("Please provide correct and complete birth date")?>');
        flag=0;
    }
    else
    {
    	var year = $('#yearId').val();
    	var dayz = 28;
    	if(((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0))
    	{
    		dayz = 29;
    	}
    	
    	if(($('#monthId').val() == 2) && ($('#dateId').val() > dayz))
    	{
    		showError('dobError','<?php echo translate_phrase("Please enter a valid date for your birthdate")?>');
            flag=0;
    	}
    	else
    	{
            jQuery('#dobError').text('');
    	}
    }
   
    if(!check_checkbox(jQuery("input[name='looking_for[]']"))){
        showError('lookingForError','<?php echo translate_phrase("Please specify what type(s) of relationships you are looking for")?>');
        flag=0;
    }
    else
    {
        jQuery('#lookingForError').text('');
    }
    
    if ($('#country').val()=='' && $('#current_city_id').val()=='') {
        
        showError('liveInError','<?php echo translate_phrase("Please select the country you live in and Please select the city you live in");?>');
       	flag=0;
    }
    else if ($('#current_city_id').length == 0 || $('#current_city_id').val()=='') {
        
        showError('liveInCITYError','<?php echo translate_phrase("Please select the city you live in");?>');
       	flag=0;
    }
    else
    {
    	jQuery('#liveInCITYError').text('');
        jQuery('#liveInError').text('');
    }
    
    if(flag==0)
        return false;
    else   
        return true;
    
 }
function save_data()
{
	if(signup_step1_validaion())
	{
		$("#signupForm").submit();
    }
}
</script>
<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="signupForm"
			action="<?php echo base_url().url_city_name() . '/'.$this->singup_name.'-1.html';?>"
			method="post" enctype="multipart/form-data">
			
			<label class="input-hint error"><?php echo $this->session->flashdata('edit_profile_msg_error');?></label>
			<div id="basics" class="Apply-Step1-a-main">
					<div class="Thanks-verify">
						<span class="Th-highlight"> <?php if (isset($is_return_apply) && !$is_return_apply): echo translate_phrase($this->session->userdata('succ_email_verify')); endif?>
							<?php if(isset($event_info)) :?>
							<?php echo translate_phrase("To RSVP for the event, please tell us some basics about yourself so we can help you find the best people to meet at the event!"); ?>
							<?php else:?>
							<?php echo translate_phrase("Please tell us some basics about yourself.")?>
							<?php endif;?>							
						</span>
					</div>
					<div class="step-form-Main">
						<div class="step-form-Part">
							<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field')?></div>
							<div class="sfp-1-main">
								<div class="sfp-1-Left"><?php echo translate_phrase("I'm:")?><span>*</span></div>
								<div class="sfp-1-Right">
									<?php 
									$gender_id = $fb_user_data['gender']?$fb_user_data['gender']:set_value('gender');
									foreach($gender as $row):?>
									<a href="javascript:;" class="rdo_div" key="<?php echo $row['gender_id'];?>"><span class="<?php echo ($row['gender_id'] == $gender_id)?'appr-cen':'disable-butn';?>"><?php echo $row['description'];?></span></a>
									<?php endforeach;?>
									<input type="hidden" name="gender" id="gender" value="<?php echo $gender_id?>">
									<label id="genderError" class="input-hint error error_indentation error_msg"></label>
								</div>
							</div>
							<div class="sfp-1-main">
								<div class="sfp-1-Left"><?php echo translate_phrase('I want to date:')?><span>*</span></div>
								<div class="sfp-1-Right">
									<ul id="ckbox_ul">
									<?php foreach($gender as $row):?>
											<li><a href="javascript:;" key="<?php echo $row['gender_id']?>"/><span class="<?php echo (isset($fb_user_data['want_date']) && in_array($row['gender_id'],$fb_user_data['want_date']))?'appr-cen':'disable-butn';?>"><?php echo $row['description'];?></span></a> 
											<input type="hidden" class="ckb_want_to_date" name="want_to_date[]" value="<?php echo (isset($fb_user_data['want_date']) && in_array($row['gender_id'],$fb_user_data['want_date']))?$row['gender_id']:'';?>"></li>
										<?php endforeach; ?>
									</ul>
									<label id="wantToDateError" class="input-hint error error_indentation error_msg"></label>
								</div>
							</div>
							<div class="sfp-1-main">
								<div class="sfp-1-Left"> <?php echo translate_phrase('I was born on')?> :<span>*</span></div>
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
								<div class="sfp-1-Left"> <?php echo translate_phrase("I'm looking for")?> :<span>*</span> </div>
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
							
							<?php
								/*$current_country = $fb_user_data['location']['country'] ? $fb_user_data['location']['country'] : $country_name;
								$city = $fb_user_data['location']['city'] ? $fb_user_data['location']['city'] : $city_name;
								if ($current_country == "" && $city != "")
								$current_country = $this->model_user->get_country_by_city($city);
								echo $current_country;exit;*/
							?>
							<div class="sfp-1-main">
								<div class="sfp-1-Left"><?php echo translate_phrase("I currently live in:") ?><span>*</span></div>
								<div class="sfp-1-Right">									
									<div class="scemdowndomain menu-Rightmar">
										<?php echo form_dt_dropdown('country',$country,$SelectedCountry,'id="current_country" class="dropdown-dt scemdowndomain menu-Rightmar" ', translate_phrase('Select country'), "hiddenfield");?>	
										<label id="liveInError" class="input-hint error error_indentation error_msg"></label>
									</div>
									<div class="scemdowndomain">
										<div id="current_city_dropdown"></div>
										<label id="liveInCITYError" class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
							</div>							
						</div>
					</div>
					<div class="Nex-mar">
						<a href="javascript:;" id="ureg_sub" onclick="save_data();"
							class="Next-butM"><?php echo translate_phrase('Next')?> </a>
					</div>
				</div>
		</form>
	</div>
</div>