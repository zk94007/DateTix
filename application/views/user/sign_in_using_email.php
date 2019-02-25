
<script
	src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url() ?>assets/js/general.js"></script>

<script type="text/javascript">
$(document).ready(function(){

	//*************************  Login box  ***************************//
        $.validator.addMethod('isEmail',function(value){
            //var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            var regex = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;
            var email = jQuery.trim(value);
            return regex.test(email);
        },'Please enter valid email address');
	$('#form-signin').validate({
	    rules: { 
			email: {required: true,isEmail:true},
			password: {required: true}
                   },
		    highlight: function(element) {
			    $(element).closest('.div-row').removeClass('success').addClass('error');
			},
			success: function(element) {
				element.closest('.div-row').removeClass('error').addClass('success');
			},
			submitHandler: function() { 
				var ajax_url = $("#form-signin").attr('action');
				jQuery.ajax({
		              url: ajax_url,
		              type: 'POST',
		              dataType: 'json',
		              data: $("#form-signin").serialize(),
		              success: function(signin) {
		                  if (signin.success == 1) {
		                    window.location.assign(signin.redirectUrl);
		                  } else {
		                      //$(".cityTxt").addClass('error-msg').text(signin.message);
                                        $("#ctTxt").addClass('error-msg').text(signin.message);
                                        
		                  }
		              }
		          });
			}
	});
	var para = "<?php echo $this->input->get('highlight');?>";
	if(para == 1)
	{
		$("#email").focus();
	}
	
});



</script>
<?php
$return = $this->session->flashdata("returnErrorData");
$first_name = (isset($return['first_name']))?$return['first_name']:set_value('first_name');
$last_name = (isset($return['last_name']))?$return['last_name']:set_value('last_name');
$email = (isset($return['email']))?$return['email']:set_value('email');
?>
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
		<?php echo form_open(url_city_name() . '/signin.html', array('id' => 'form-signin'));?>
			<div class="popup-box popupSmall" id="form-container">
				<div class="sfp-1-main">
					<div class="cityTxt" style="margin-top: 0px">
					<?php
					//$this->session->set_flashdata('dispMessage','test');
					//echo $this->session->flashdata('dispMessage');?>
					</div>
					
					<h1>
					<?php echo translate_phrase('Sign In Using Facebook'); ?></h1>
					<div class="apply-privatly" style="padding-top: 10px">
						<a class="xl-fb-btn" href="javascript:;" onclick="fb_login();return false;"><img src="<?php echo base_url().'assets/images/fb-icn-big.jpg'?>" /><?php echo translate_phrase('Sign in using <b>Facebook</b>') ?></a>
					</div>
					<div class="user-Top fl">
						<div class="L-post-anyThing">
						<span class="Red-color"><b><?php echo translate_phrase("Not yet a member? ");?><a class="blu-color" href="<?php echo base_url().''.url_city_name() ?>/apply.html?src=<?php echo $this->session->userdata('ad_id')?>"><?php echo translate_phrase("Apply for a free membership now");?></a>!</b></span>
						</div>						
					</div>
					
					<div class="last-bor"></div>
					
					<div class="div-row">
					<h1><span><?php echo translate_phrase('Sign In Using Email') ?></span></h1>
					</div>
					
					<div class="div-row" id="ctTxt">
						<?php echo $this->session->flashdata('isAlreadyVerifiedMsg');?>
						<?php echo $this->session->userdata('fb_login_error'); $this->session->unset_userdata('fb_login_error');?>
					</div>
					<div class="div-row">
						<input type="text"
							placeholder="<?php echo translate_phrase('Your email address') ?>"
							class="input-full" name="email" id="email"
							value="<?php echo $email;?>"> <label class="input-hint error"
							for="email"><?php echo form_error('email')?> </label>
					</div>

					<div class="div-row">
						<input type="password" placeholder="<?php echo translate_phrase('Enter password')?>" class="input-full"
							name="password"> <label class="input-hint error" for="password"><?php echo form_error('email')?>
						</label>
					</div>

					<div class="div-row">
						<div class="remember">
							<input tabindex="3" checked="checked" value="1" name="rememberme"
								id="rememberme" class="checkbox" type="checkbox"> <label
								for="rememberme"><?php echo translate_phrase('Remember me') ?> </label>&nbsp;&nbsp;&nbsp;
						</div>
						<a
							href="<?php echo base_url().''.url_city_name() ?>/forgot-password.html"
							id="POPUP_forgot_password"
							title="<?php echo translate_phrase('Forgot Password') ?>"
							class="forgot-pass"> <img style="vertical-align: -3px;"
							src="<?php echo base_url()?>assets/images/lock.jpg"><span
							style="color: #65bae6;"><?php echo translate_phrase('Forgot Password') ?>?</span>
						</a>

					</div>

					<div class="div-row">
						<div class="btn-group left">
							<input type="submit" class="btn btn-pink"
								value="<?php echo translate_phrase('Sign In') ?>" /> <input
								type="button" onclick="history.back();" class="btn btn-blue"
								value="<?php echo translate_phrase('Cancel') ?>" />
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- 
<div id="signin">
    <?php echo form_open(url_city_name() . '/signin.html?highlight=1', array('id' => 'form-signin'));?>
      <input type="text" placeholder="Email address" class="e-mail" name="email">
      <input type="password" placeholder="Password" class="password" name="password">
      <a href="<?php echo url_city_name() ?>/forgot-password.html" id="POPUP_forgot_password" title="<?php echo translate_phrase('Forgot Password') ?>" class="forgot-pass"><span></span><?php echo translate_phrase('Forgot password?') ?></a>
      <fieldset>
        <input type="checkbox" tabindex="3" checked="checked" value="1" name="rememberme" id="rememberme" class="checkbox">
        <label for="rememberme"><?php echo translate_phrase('Remember me') ?></label>
      </fieldset>
      <button type="submit" title="Sign In" class="button darkblue"><?php echo translate_phrase('Sign In') ?></button>
    <?php echo form_close(); ?>
</div>

 -->
