<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
		<?php echo form_open(url_city_name() . '/forgot-password.html', array('id' => 'form-forgot-password')) ?>
			<div class="popup-box popupSmall" id="form-container">
				<h1>
				<?php echo translate_phrase('Forgot Password') ?>
				</h1>
				<br />
				<br />
				<div class="cityTxt error-msg" id="pageMsg">
				<?php echo $this->session->flashdata('invalidResetLink');?>
				</div>

				<div class="sfp-1-main">
					<div class="div-row">
						<input type="text"
							placeholder="<?php echo translate_phrase('Your email address') ?>"
							class="input-full" name="email" id="email" value=""> <label
							class="input-hint error" for="email"><?php echo form_error('email')?>
						</label>
					</div>
					<div class="div-row">
						<div class="btn-group left">
							<input type="submit" class="btn btn-pink"
								value="<?php echo translate_phrase('Reset Password') ?>" /> <input
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
<script type="text/javascript">
$(document).ready(function(){
	$("#form-forgot-password").submit(function(e) {
		e.preventDefault();
	        jQuery.ajax({
	          url: this.action,
	          type: 'POST',
	          dataType: 'json',
	          data: $(this).serialize(),
	          success: function(fp_data) {
		         if(fp_data.success == '1')
		         {
		        	 $("#pageMsg").addClass('DarkGreen-color').removeClass('error-msg');		        	 
				 }
		         else
		         { 
		        	 $("#pageMsg").addClass('error-msg').removeClass('DarkGreen-color');
			     }
		         $("#pageMsg").html(fp_data.message);
	          }
	        });        
	    });
});
</script>
