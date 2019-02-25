<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	//*************************  Login box  ***************************//
	$('#form-forgot-password').validate({
	    rules: { 	
                        newPassword    : {required: true},
                        repeatNewPassword : {equalTo: "#newPassword"}
		   },
	    highlight: function(element) {
			    $(element).closest('.div-row').removeClass('success').addClass('error');
			},
       	    success: function(element) {
				element.closest('.div-row').removeClass('error').addClass('success');
			},
            messages:{
                        repeatNewPassword : 'Passwords do not match. Please re-enter them.'
            },                    
	    submitHandler: function() {
                                
                                var url = $("#form-forgot-password").attr('action');
                                var data = $("#form-forgot-password").serialize();
                                var type = 'json';
                              jQuery.post(url,data,function(response){
                                      jQuery('#cityTxt').html(response.message);
                                      $(".div-row").fadeOut();
                              },type);
                            
			}
	});
});
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
		<?php echo form_open(url_city_name() . '/process-password-reset.html', array('id' => 'form-forgot-password'));?>
			<div class="popup-box popupSmall" id="form-container">
				<div class="sfp-1-main">
					<h1 style="padding-bottom: 10px; width: 100%">
					<?php echo translate_phrase('Reset Your Password') ?>
					</h1>
					<div class="cityTxt" id="cityTxt"
						style="margin-top: 0px; color: darkgreen; font-weight: bold"></div>

					<div class="div-row">
						<input type="password"
							placeholder="<?php echo translate_phrase('New Password')?>"
							class="input-full" id="newPassword" name="newPassword"> <label
							class="input-hint error" for="password"><?php echo form_error('newPassword')?>
						</label>
					</div>

					<div class="div-row">
						<input type="password"
							placeholder="<?php echo translate_phrase('Repeat Password')?>"
							class="input-full" name="repeatNewPassword"> <label
							class="input-hint error" for="password"><?php echo form_error('repeatNewPassword')?>
						</label>
					</div>

					<div class="div-row">
						<input type="hidden" name="resetText" value=<?php echo $user_id?>>
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
