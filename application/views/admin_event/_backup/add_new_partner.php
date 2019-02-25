<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){		
	//validate Hidden Fields
	$.validator.setDefaults({ ignore: '' });
	$('#add_partner').validate();
});
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
									<input value="<?php echo isset($name)?$name:'';?>" id="name" name="name" class="FDates-input" type="text" style="height: 40px;" required="">
								</div>
							</div>
							
							<div class="sfp-1-main">
								<div class="sfp-1-Left bold"><?php echo translate_phrase('City')?>: <span>*</span></div>
								<div class="sfp-1-Right">
									<?php echo form_dt_dropdown('city_id',$cities,"",'class="dropdown-dt domaindropdown"',translate_phrase('Select City'),"hiddenfield"); ?>
								</div>
							</div>
							
							<div class="sfp-1-main">
								<div class="sfp-1-Left bold"><?php echo translate_phrase('Default event ticketing URL')?>: <span>*</span></div>
								<div class="sfp-1-Right">
									<input value="<?php echo isset($default_event_url)?$default_event_url:'';?>" id="default_event_url" name="default_event_url" class="FDates-input" type="text" style="height: 40px;" required="">
								</div>
							</div>
							
							<div class="sfp-1-main">
								<div class="sfp-1-Left bold"><?php echo translate_phrase('Default Language')?>: <span>*</span></div>
								<div class="sfp-1-Right">
									
									<?php 
									$lanaguage_id = $this->language_id;
									echo form_dt_dropdown('default_language_id',$languages,$lanaguage_id,'class="dropdown-dt domaindropdown"',translate_phrase('Select City'),"hiddenfield"); ?>
								</div>
							</div>
							
							<div class="sfp-1-main mar-top2">
								<div class="sfp-1-Left bold"></div>
								<div class="sfp-1-Right btn-group left">
									<input type="submit" class="btn btn-pink" value="<?php echo translate_phrase('Save');?>"> 
									<input type="button" onclick="history.back();" class="btn btn-blue" value="Cancel">
								</div>
							</div>
							
						</form>				
				</div>
			</div>
		</div>
	</div>
</div>