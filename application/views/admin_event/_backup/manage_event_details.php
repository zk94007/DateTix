<script  type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$.validator.setDefaults({ ignore: '' });
		$('#event_form').easytabs();
	});
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title?></h1>
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="event_form">
					<ul class='etabs'>												
						<li class='tab tab-nav' id="tab_details"><span></span><a href="#details"><?php echo translate_phrase('Details');?></a></li>
						<li class='tab tab-nav' id="tab_parner"><span></span><a href="#partner"><?php echo translate_phrase('Partners');?></a></li>
						<li class='tab tab-nav' id="tab_prepaidlist"><span></span><a href="#prepaidlist"><?php echo translate_phrase('Prepaid List');?></a></li>
						<li class='tab tab-nav' id="tab_photos"><span></span><a href="#photos"><?php echo translate_phrase('Photos');?></a></li>						
					</ul>
										
					<div class="step-form-Main Mar-top-none Top-radius-none" id="details">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/add_edit_event_form');?>
						</div>	
					</div>
					
					<div class="step-form-Main Mar-top-none Top-radius-none" id="partner">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/event_partners');?>
						</div>
					</div>
					
					<div class="step-form-Main Mar-top-none Top-radius-none" id="prepaidlist">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/event_prepaid_list');?>
						</div>
					</div>
					
					<div class="step-form-Main Mar-top-none Top-radius-none" id="photos">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/event_photos');?>
						</div>
					</div>
					
				</div>
			</div>			
		</div>
	</div>
</div>