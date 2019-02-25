<script  type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$.validator.setDefaults({ ignore: '' });
		$('#event_form').easytabs().bind('easytabs:after', function(tab, panel, data) {
			 
			 if(panel[0].hash == '#details')
			 {
				//loadCkeditor();
			 }
		});
		
		if(window.location.hash == '#details' || window.location.hash == '')
		{
			loadCkeditor();
		}
	});
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title;
				
				$event_list_url = base_url($this->admin_url);
				if(isset($seleccted_city_id) && $seleccted_city_id)
				{
					$event_list_url .= '?city_id='.$seleccted_city_id;
				}
				 ?>
					
					<a class="blue-colr" href="<?php echo $event_list_url?>"> < <?php echo translate_phrase('Back to Event List');?></a>	
				</h1>
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="event_form">
					<ul class='etabs'>								
						<li class='tab tab-nav' id="tab_details"><span></span><a href="#details"><?php echo translate_phrase('Details');?></a></li>
						<?php if(isset($event_id) && $event_id):?>
						<li class='tab tab-nav' id="tab_parner"><span></span><a href="#partner"><?php echo translate_phrase('Partners');?></a></li>
						<li class='tab tab-nav' id="tab_prepaidlist"><span></span><a href="#prepaidlist"><?php echo translate_phrase('Prepaid List');?></a></li>
							<?php if(isset($is_past_event)&&$is_past_event):?>
							<li class='tab tab-nav' id="tab_photos"><span></span><a href="#photos"><?php echo translate_phrase('Photos');?></a></li>						
							<?php endif;?>
						<?php endif;?>
					</ul>
										
					<div class="step-form-Main Mar-top-none Top-radius-none" id="details">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/add_edit_event_form');?>
						</div>	
					</div>
					
					<?php if(isset($event_id) && $event_id):?>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="partner">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/event_partners');?>
						</div>
					</div>
					
					<?php endif;?>
					<?php if(isset($event_id) && $event_id):?>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="prepaidlist">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/event_prepaid_list');?>
						</div>
					</div>					
					<?php endif;?>
					
					<?php if(isset($is_past_event)&&$is_past_event):?>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="photos">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/event_photos');?>
						</div>
					</div><br />
					<?php endif;?>					
				</div>
			</div>			
		</div>
	</div>
</div>
<script type="text/javascript">
	function loadCkeditor(action){
		<?php if(isset($is_past_event)&&$is_past_event):?>
		<?php else:?>
		$.each($('.ckeditor'),function(i,item){			
			var name = $(item).attr('id');
			
			var editor = CKEDITOR.instances[name];
		    if (editor) { editor.destroy(true); }
		    CKEDITOR.replace(name);
		     	
		});
    	<?php endif;?>
	}
</script>