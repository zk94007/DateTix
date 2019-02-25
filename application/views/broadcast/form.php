<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script type="text/javascript">
$(document).ready(function(){});
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title?></h1>
			</div>
				
			<div class="step-form-Main Mar-top-none" id="broadcastMsg">
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>

				
				<div class="step-form-Part">
					<form action="<?php echo base_url('broadcast/process').'/'.$this -> session -> userdata('broadcast_token');?>" method="post" >
						
					
					<div class="page-msg-box"> Write Message : </div>
					<div class="div-row">
						<textarea name="email_body" id="broadcastMsgText" class="input-full" style="height: 160px;"></textarea>
					</div>
					<div class="btn-group right">
						<button type="submit" class="btn btn-blue" id="broadcast" ><?php echo translate_phrase("Broadcast");?></button>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
