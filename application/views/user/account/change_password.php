<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	//*************************  Login box  ***************************//
	$('#form').validate({
	    rules: { 
			oldpassword	   : {required: true},
			newpassword : {required: true},
	    	repeatpassword : {required: true,equalTo: "#newpassword"}
		},
	    highlight: function(element) {
		    $(element).closest('.div-row').removeClass('success').addClass('error');
		},
       	success: function(element) {
			element.closest('.div-row').removeClass('error').addClass('success');
		},
        messages:{
        	repeatNewPassword : 'Passwords do not match. Please re-enter it.'
        }});
});
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
		<?php echo form_open(url_city_name() . '/change-password.html', array('id' => 'form'));?>
			<div class="popup-box popupSmall" id="form-container">

				<div class="page-msg-box Red-color left">
				<?php echo $this->session->flashdata('error_msg');?>
				</div>
				<div class="page-msg-box DarkGreen-color left">
				<?php echo $this->session->flashdata('success_msg');?>
				</div>

				<div class="sfp-1-main">
					<h1 style="padding-bottom: 20px; width: 100%">
					<?php echo translate_phrase('Change Password')?>
					</h1>

					<div class="div-row">
						<input type="password"
							placeholder="<?php echo translate_phrase('Enter current password')?>"
							class="input-full" id="oldpassword" name="oldpassword"> <label
							class="input-hint error" for="oldpassword"><?php echo form_error('oldpassword')?>
						</label>
					</div>

					<div class="div-row">
						<input type="password"
							placeholder="<?php echo translate_phrase('Enter new password')?>"
							class="input-full" id="newpassword" name="newpassword"> <label
							class="input-hint error" for="newpassword"><?php echo form_error('newpassword')?>
						</label>
					</div>

					<div class="div-row">
						<input type="password"
							placeholder="<?php echo translate_phrase('Confirm new password')?>"
							class="input-full" name="repeatpassword"> <label
							class="input-hint error" for="repeatpassword"><?php echo form_error('repeatpassword')?>
						</label>
					</div>

					<div class="div-row">
						<div class="btn-group left">
							<input type="submit" class="btn btn-pink"
								value="<?php echo translate_phrase('Confirm') ?>" /> <input
								type="button" onclick="history.back();" class="btn btn-blue"
								value="<?php echo translate_phrase('Cancel') ?>" />
						</div>
					</div>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
