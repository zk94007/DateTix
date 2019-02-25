<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script type="text/javascript">
var base_url = '<?php echo base_url() ?>';
var smsUsed = false;
<?php if(isset($mobile_phone_verification_code_sent) && $mobile_phone_verification_code_sent == 1):?>
var sms_sent = false;
<?php else:?>
var sms_sent = true;
<?php endif;?>

var validateOthersTextBox = false;//for friends name and others textbox in 'how_you_heard_about_us';

jQuery(document).ready(function(){
	
	// Drop down js
	$(".dropdown-dt").find('dt a').live('click',function () {
		$(this).parent().parent().find('ul').toggle();
	});
	
	$(".dropdown-dt dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
    	$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
	});
		
	$(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("dropdown-dt"))
        	$(".dropdown-dt dd ul").hide();
    });
		
	$(".heardAboutUsDD dd ul li a").live('click',function (){
		var heardAboutUsValue = jQuery(this).text();

		if(heardAboutUsValue == '<?php echo translate_phrase('Other')?>')
		{
			$('#friendsName').attr('placeholder','Let us know how you heard about us');
		}
		else
		{
			$('#friendsName').attr('placeholder','Enter friend\'s name(s)');
		}
		
		if(heardAboutUsValue == '<?php echo translate_phrase('Friends')?>' || heardAboutUsValue == '<?php echo translate_phrase('Other')?>')
		{
			//alert('fadeIn');
			//validateOthersTextBox = true
			//jQuery('#friendsNameDiv').fadeIn();
		}
		else
		{
			validateOthersTextBox = false;
			jQuery('#friendsName').val('');
			jQuery('#friendsNameDiv').fadeOut();
		}
	});

	$('.upload-profile-pic').live('click',function(){
		$("#photo_id_or_passport").trigger('click');
	});
        
        $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});
});

function check_terms(){
	
	var mobileNumber = $.trim(jQuery('#mobile_phone_number').val());
	var regex = /^[0-9]{8,15}$/ ;
	if(mobileNumber == '' || !(regex.test(mobileNumber)))
	{
		jQuery('#mobileNumberError').text('<?php echo translate_phrase("Please enter a valid mobile phone number");?>.');
		goToScroll('mobile_phone_number');
		return false;
	}
	else
	{
		jQuery('#mobileNumberError').text('');
	}
	
	if(jQuery('#heared_abou_us').val() == '' || (validateOthersTextBox == true && jQuery('#friendsName').val() == ''))
	{
		jQuery('#heardAboutUsError').text('<?php echo translate_phrase("Please tell us how you heard about ".get_assets('name','DateTix'))?>');
		goToScroll('heardAboutUsError');
		return false;
	}
	else
	{
		$("#signupForm").submit();			
	}	
}

function validatePhone(fld) {
	var error = "1";
	var stripped = fld.replace(/[\(\)\.\-\ ]/g, '');     
   if (fld == "") {
		error = '<?php echo translate_phrase("Please enter your mobile number");?>'
	} else if (isNaN(parseInt(stripped))) {
		error = '<?php echo translate_phrase("Your mobile number contains illegal characters");?>';
	} else if (!(stripped.length >= 8) || !(stripped.length <= 15)) {
		error = '<?php echo translate_phrase("Your mobile number  must be between 8 to 15 digits");?>';
	} 
	return error;
}

function send_sms(country_code){
	var mobile_number   = $('#mobile_phone_number').val();
	var url = '<?php echo base_url("user/send_verification_sms")?>';
	var validateNumber = validatePhone(mobile_number);

	
	if(validateNumber == '1' && country_code!="0") {
		if(sms_sent)
		{
			if(smsUsed == false)
			{
					$.ajax({ 
						url:url, 
						type:"post",
						data:{country_code:country_code,mobile_number:mobile_number},
						cache: false,
						success: function (data) {

							$('#mobileNumberError').html('');

							jQuery('#verificationCodeDiv').fadeIn('fast',function(){
								$('#verification_msg').html(data);
							});

							if (data.indexOf("error_msg") === -1){
								jQuery('#verificationCodeDiv').find('.sfp-1-main').show();
								$('#sms_button').html('<?php echo translate_phrase("Re-send Verification SMS");?>');
								smsUsed = true;
							}
						}     
					});
			}
			else
			{

				mobile_number   = "+"+country_code+mobile_number;
				$.ajax({ 
					url:'<?php echo base_url("user/manual_verify_sms")?>', 
					type:"post",
					data:'mobile_number='+mobile_number,
					cache: false,
					success: function (data) {
						sms_sent = false;
						$('#mobileNumberError').html('<?php echo translate_phrase("Please email support@datetix.com with your email address and mobile number and we will manually verify your account");?>');
					}     
				});
			}

		 }
		else
		{
			$('#mobileNumberError').html('<?php echo translate_phrase("Please email support@datetix.com with your email address and mobile number and we will manually verify your account");?>');
		}
	}
	else{
		$('#mobileNumberError').html(validateNumber);
	}
}
function verify_sms(){
	var verification_code   = $('#verification_code').val();
	var url = '<?php echo base_url("user/sms_verification")?>';
	if(verification_code!="") {
		$.ajax({ 
			url:url, 
			type:"post",
			data:'verification_code='+verification_code,
			cache: false,
			success: function (data) {

					if(data=="1"){
						jQuery('#verificationCodeDiv').html('<p style="color:green;"><?php echo translate_phrase("Your mobile number has been sucessfully verified.");?></p>');
					}else 
					{
						$('#verificationCodeDiv').find('label.error').html(data);
					}
			}     
		});
   }else{
	   
	   $('#verificationCodeDiv').find('label.error').html('<p style="color:#FD2080;"><?php echo translate_phrase(" Please enter verification code found in the SMS");?></p>');
   }
}
</script>
<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="signupForm"
			action="<?php echo base_url().url_city_name() . '/'.$this->singup_name.'-7.html';?>"
			method="post" enctype="multipart/form-data">
			
			<label class="input-hint error"><?php echo $this->session->flashdata('edit_profile_msg_error');?></label>
			<div class="Apply-Step1-a-main" id="step2_b">
					<div class="step-form-Main">
						<div class="step-form-Part">
							<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field')?></div>
							<div class="edu-main">
								
								<?php if(isset($mobile_phone_verification_code_sent) && $mobile_phone_verification_code_sent == 1):?>
								<?php if(isset($mobile_phone_is_verified) && $mobile_phone_is_verified == 1):?>
									<div id="successfully_verified" class="sfp-1-main" style="padding-bottom:5px; <?php if(isset($mobile_phone_is_verified) && $mobile_phone_is_verified == 1):?>display:block;<?php else:?>display:none;<?php endif;?>">
										<div id="verifiy_msg" style="font-weight: bold;">
											<p style="color: green;"><?php echo translate_phrase('Your mobile number ').' <span style="color:#000 !important;">(+'.$country_code.') '.$mobile_phone_number.'</span>'. translate_phrase(' has been sucessfully verified')?>. </p>
										</div>
									</div>
									<?php endif;?>
									<?php else:?>
									<div class="sfp-1-main" id="main_phoneDiv">
										<div class="Left-coll01">
										<?php echo translate_phrase('Your mobile number:')?>
											<span>*</span>
										</div>
										<div class="Right-coll01">
											<?php if(isset($mobile_phone_verification_code_sent) && $mobile_phone_verification_code_sent == 1):?>
											<label><?php echo '(+'.$country_code.') '.$mobile_phone_number;?></label>
											<?php else:?>
											<div class="centimeter pad-L-none">( <?php echo "+".$country_code;?> )</div>
											<input type="text" class="PhoneNum-input" name="mobile_phone_number" value="<?php echo isset($form_data['mobile_phone_number'])?$form_data['mobile_phone_number']:''?>" id="mobile_phone_number" autofocus="autofocus" />
											
											<?php if($this->city_id =='2600000'):?>
											<div class="sch-logoR">
												<div class="Verification-Button">
													<a href="javascript:;" id="sms_button"
														onclick="send_sms('<?php echo $country_code;?>');"><?php echo translate_phrase('Send Verification SMS')?>
													</a>
												</div>
											</div>
											<?php endif;?>
											
										<?php endif;?>
										</div>
										
										<label id="mobileNumberError" class="input-hint error error_indentation error_msg"></label>
									</div>
									<?php endif;?>
                                                                        
<!--                                                                        <div class="sfp-1-main">
                                                                                    <div class="Left-coll01">
                                                                                    
                                                                                        <?php echo translate_phrase("Introduce me to relevant matches from other sources:")?>
                                                                                        <span class="Redstar">*</span>
                                                                                    
                                                                                    </div>
                                                                                    <div class="sfp-4-left">
                                                                                    <a href="javascript:;" class="rdo_div" key="1"><span class="appr-cen">Yes</span></a>
                                                                                    <a href="javascript:;" class="rdo_div" key="0"><span class="disable-butn">No</span></a>
                                                                                    <input type="hidden" name="external_intros" id="external_intros" value="1">
                                                                                    </div>
                                                                        </div>-->
                                                                        <input type="hidden" name="external_intros" id="external_intros" value="1">
									<div id="verificationCodeDiv" style="<?php if(isset($mobile_phone_verification_code_sent) && $mobile_phone_verification_code_sent == 1 && $mobile_phone_is_verified != 1):?>display:block;<?php else:?>display:none;<?php endif;?>">
										<div class="Thanks-verify Pad-BotAs3" id="verification_msg">
										<?php echo translate_phrase('We have sent a SMS verification code to').' &nbsp;(+'.$country_code.') '.$mobile_phone_number. translate_phrase('. Please <b>enter the code below</b>:')?>
										</div>
										<div class="sfp-1-main">
											<div class="Left-coll01">
											<?php echo translate_phrase('Verification code found in yours SMS:')?>
											</div>
											<div class="Right-coll01">
												<input name="verification_code" id="verification_code"
													type="text" class="SmsVerif-input" />
												<div class="sch-logoR">
													<div class="Verification-Button">
														<a href="javascript:;" onclick="verify_sms();"><?php echo translate_phrase('Verify')?>
														</a>
													</div>
												</div>
												<label class="input-hint error"></label>
											</div>
										</div>
									</div>
									<!-- Mobile Phone end-->
									<div class="sfp-1-main" >
										<div class="Left-coll01">
										<?php echo translate_phrase('Enter promo code (optional) :')?>
										</div>
										<div class="Right-coll01">
											<input type="text" class="PromoCode-input" name="promo_code" value="<?php echo isset($form_data['promo_code'])?$form_data['promo_code']:''?>" id="promo_code"  />											
										</div>										
										<label id="promoCodeError" class="input-hint error error_indentation error_msg"></label>
									</div>
                                                                        
									<div  class="sfp-1-main">
                                                                                <div class="Left-coll01">
                                                                                    <?php echo translate_phrase('How did you hear about us?')?><span class="Redstar">*</span>
										</div>
										<div class="Right-coll01">
											
												<?php
												echo form_dt_dropdown('heared_abou_us',$heardAboutUsList,set_value('heared_abou_us'),'class="dropdown-dt looksdowndomain heardAboutUsDD"',translate_phrase('Please select'),"hiddenfield");
												?>
												<?php
												if(isset($hear_about_place_holder)){
													$place_holder   = $hear_about_place_holder->show_text;
													$show_hear_text = 'style="display:block;"';
												}
												if(isset($form_data['heared_abou_us']) && ($form_data['heared_abou_us'] == '8' || $form_data['heared_abou_us'] == '1') )
												{
													$place_holder = "";
													$show_hear_text = 'style="display:block;"';
												}
												else{
													$show_hear_text = 'style="display:none;"';
													$place_holder   = "" ;
												}
												?>
													<div class="sel-emailR Media-pad" id="friendsNameDiv"
													<?php echo $show_hear_text;?>>
														<input name="heard_about_us_other" id="friendsName"
															type="text" class="FriendName-input"
															placeholder="<?php echo translate_phrase("Enter friend's name(s)")?>"
															value="<?php echo set_value('heard_about_us_other');?>" />
													</div>
													<label id="heardAboutUsError"
														class="input-hint error error_indentation error_msg"></label>
												
											
											<!-- 
											<h3><?php echo translate_phrase('Please enter the letters you see in the image below (CASE-SENSITIVE):')?></h3>
											<div class="sfp-1-main Pad-topAs3">
												<div class="captcha-Prt">
													<div id="captcha_img1">
													<?php echo $image;?>
													</div>
													&nbsp; <input name="captcha_word" id="captcha_word"
														type="text" class="post-input" value="" />
												</div>
												<div class="Right-coll01">
													<div class="Edit-Button01">
														<a onclick="get_captcha('')"><?php echo translate_phrase('Get Another Image')?>
														</a>
													</div>
												</div>
												<div style="clear: both"></div>

												<?php if(isset($captcha_error) && $captcha_error):?>
												<?php echo $captcha_error ;?>
												<script type="text/javascript">
														$(document).ready(function(){
																$("#captcha_word").focus();
															})
														</script>
												<?php endif;?>
											</div>
											
											
											<div class="captcha-img">
												<div class="skil-check-area-01">
													<ul>
														<li><span> <input type="checkbox" id="terms" name="terms"
																class="field checkbox" value="1" tabindex="4" /> <label
																class="choice" for="terms">&nbsp;</label> </span>
														</li>
													</ul>
												</div>
											</div>
											-->
                                                                                       </div> 
                                                                            </div> 
                                                                            <div  class="sfp-1-main">            
											<div class="Right-coll01 width-coll01">
												<div class="Thanks-verify">
												<?php echo translate_phrase('By clicking on the Submit Application button below, you are indicating that you have read, understood, and agree to our ')?>
                                                                                                        <a href="<?php echo base_url().'terms.html';?>" target="_blank">
                                                                                                            <?php echo translate_phrase('Terms of Use');?>
                                                                                                        </a>
                                                                                                            <?php echo translate_phrase('and');?> 
                                                                                                        <a href="<?php echo base_url().'privacy.html';?>" target="_blank">
                                                                                                            <?php echo translate_phrase('Privacy Policy');?>
                                                                                                        </a>
												</div>
												<div class="error_msg"
													style="text-align: left; color: #ed217c;" id="terms_error">
													<br /> <br />
												</div>
											</div>
										
										
									</div>	
							</div>
						</div>
					</div>
					
					<div class="Nex-mar">
						<?php if(isset($event_info)) :?>
						<input class="Next-butM" type="button" onclick="check_terms();" value="<?php echo translate_phrase('Complete RSVP')?>">
						<?php else:?>
						<input class="Next-butM" type="button" onclick="check_terms();" value="<?php echo translate_phrase('Submit Application')?>">
						<?php endif;?>							
					</div>
				</div>
		</form>
	</div>
</div>
