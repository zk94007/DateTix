<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){		
	//validate Hidden Fields
	$.validator.setDefaults({ 
		ignore: [],
		rules:{
            'city_id': {required: true},
            'default_language_id': {required: true},
        }
	});
	$('#add_partner').validate();
	
	$("#cityList li a").live('click',function(){
		changeDatetixURL();
	});	
	
	//Load url when page load
	changeDatetixURL();
});
function changeDatetixURL()
{
	var city_id = $("#city_id").val();		
	var datetixURL = "";
	//Hongkong
	if(city_id == '260')
	{
		datetixURL = 'www.datetix.hk/';
	}
	else
	{
		datetixURL = 'www.datetix.com/';
	}
	$(".datetix-url").text(datetixURL)		
}
</script>
<style>
	label.error{
		width: 100%;
		float: left;
		clear: both;
	}
</style>

<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title?></h1>
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="step-form-Main Mar-top-none Top-radius-none active">
					<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field')?></div>
					<form id="add_partner" name="add_partner" action="" method="post">
						<input name="event_id" type="hidden" value="<?php echo isset($event_id)?$event_id:'';?>" />
						
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('Name')?>: <span>*</span></div>
							<div class="sfp-1-Right">
								<input value="<?php echo isset($name)?$name:'';?>" id="name" name="name" class="FDates-input" type="text" style="height: 40px; width:290px;" required="">
								<label for="name" class="error"><?php echo form_error('name'); ?></label>
							</div>
						</div>
						
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('City')?>: <span>*</span></div>
							<div class="sfp-1-Right" id="cityList">
								<?php echo form_dt_dropdown('city_id',$cities,$selected_city_id,'class="dropdown-dt domaindropdown"',translate_phrase('Select City'),"hiddenfield"); ?>
							</div>
						</div>
						
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('Default URL Shortcut')?>: <span>*</span></div>
							<div class="sfp-1-Right">
								<span class="currency datetix-url"></span>
								<input value="<?php echo isset($default_event_url)?$default_event_url:'';?>" id="default_event_url" name="default_event_url" class="FDates-input" type="text" style="height: 40px; width:120px;" required="">
								<label for="default_event_url" class="error"><?php echo form_error('default_event_url'); ?></label>
							</div>
						</div>
						
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('Default Display Language')?>: <span>*</span></div>
							<div class="sfp-1-Right">
								
								<?php 
								$lanaguage_id = $this->language_id;
								echo form_dt_dropdown('default_language_id',$languages,$lanaguage_id,'class="dropdown-dt domaindropdown"',translate_phrase('Select Language'),"hiddenfield"); ?>
							</div>
						</div>
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('Login')?>:</div>
							<div class="sfp-1-Right">
								<input value="<?php echo isset($login)?$login:'';?>" id="login" name="login" class="FDates-input" type="text" style="height: 40px; width:290px;">
								<label class="error"><?php echo isset($error_msg)&&$error_msg?$error_msg:'';?></label>
								
								
							</div>
						</div>
						
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('Password')?>:</div>
							<div class="sfp-1-Right">
								<input value="<?php echo isset($password)?$password:'';?>" id="password" name="password" class="FDates-input" type="text" style="height: 40px; width:290px;">
								<label class="error"><?php echo isset($error_msg_password)&&$error_msg_password?$error_msg_password:'';?></label>
								
							</div>
						</div>
						
						<div class="sfp-1-main mar-top2">
							<div class="sfp-1-Left bold"></div>
							<div class="sfp-1-Right btn-group left">
								<input type="submit" class="btn btn-blue" value="<?php echo translate_phrase('Save');?>"> 
								<a class="disable-butn cancel-link" href="javascript:;" onclick="history.back();"><?php echo translate_phrase('Cancel');?></a>
								
							</div>
						</div>							
					</form>		
				</div>
			</div>
		</div>
	</div>
</div>