<?php
$error = '';
$email        = ($this->session->userdata('userEmail')) ? $this->session->userdata('userEmail') : '';
$userDetails = ($this->session->userdata('userDetails')) ? $this->session->userdata('userDetails') : '';
?>
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="step-form-Main">
				<h2>
				<?php echo translate_phrase('Apply for ').get_assets('name','DateTix').translate_phrase(' Membership') ?>
				</h2>
				<div class="cityTxt error-msg">
				<?php echo translate_phrase($error);?>
				</div>
				<div class="cityTxt" style="color: darkgreen; margin-top: 10px">
				<?php

				echo translate_phrase('We have sent a verification email to');
				echo '&nbsp;<b>'.$email.'</b><br>';
				echo translate_phrase('Please click on the verification link found in that email to verify your email address. If you cant find that email, please check your junk mailbox or click on the below button to have another verification email sent to you')?>
				</div>


				<div class="btn-group left" style="margin-top: 10px">
					<input id="resendButton" type="button"
						onclick="resendVerificationEmail('<?php echo $userDetails ?>')"
						class="btn btn-blue"
						value="<?php echo translate_phrase('Re-send Verification Email')?>">
				</div>
				<div id="responseText" class="cityTxt"
					style="clear: left; padding-top: 10px; color: #FF499A !important;"></div>
			</div>

		</div>
	</div>
</div>
<script>
    function resendVerificationEmail(userDetails)
    {
        
        if(userDetails != "")
        {
            var url   = "<?php echo base_url().'home/resend_email_verification/'?>" + userDetails;
            var type  = 'json';
            jQuery('#resendButton').attr('disabled','disabled');
            jQuery('#resendButton').val("<?php echo translate_phrase('Please Wait...')?>");
            jQuery.post(url,'',function(response){
                if(response.actionStatus == 'ok')
                {
                    jQuery('#responseText').text(response.responseText)
                }
                jQuery('#resendButton').removeAttr('disabled');
                jQuery('#resendButton').val("<?php echo translate_phrase('Re-send Verification Email')?>");
            },type);
        }
    }
</script>
