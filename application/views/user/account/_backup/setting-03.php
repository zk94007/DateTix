<script  type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script
	 type="text/javascript"
	src="<?php echo base_url()?>assets/js/general.js"></script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,3);
?>
<script type="text/javascript">
var user_id ="<?php echo $user_id ?>";
var isValidate = false;

var smsUsed = false;
<?php if($user_data['mobile_phone_verification_code_sent'] == 1):?>
var sms_sent = false;
<?php else:?>
var sms_sent = true;
<?php endif;?>
$(document).ready(function(){
	$('#active_expire').easytabs();
	/*--------------------validations---------------------------------------------*/
	$('#active_expire').bind('easytabs:before', function(tab, panel, data){
		var current_tab_id = $('#active_expire ul li.active a').attr('id');
	    var target_tab_id = panel[0].id;
	    $("#current_tab").val(target_tab_id);
	    
	});

	setTimeout(function(){
		var current_tab_id = $('#active_expire ul li.active a').attr('id');
		$("#current_tab").val(current_tab_id);	  
	},500);    

	
	$("#autorenewToggleBut").live('click',function(){
		if($(this).hasClass('appr-cen'))
		{
			$(this).removeClass('appr-cen').addClass('disable-butn');
			$(this).siblings(':input[type="hidden"]').val('0');
		}
		else
		{
			$(this).removeClass('disable-butn').addClass('appr-cen');
			$(this).siblings(':input[type="hidden"]').val('1');
		}
	});

	$("#AddMobileNumberLink").live('click',function(){
		$("#addNewMobile").slideToggle('slow', function() {
	         $("#new_mobile_no").focus();
	    });
	}); 
		
	$(".privacy-toggleBtn").live('click',function(){

		if($(this).find('span').hasClass('appr-cen'))
		{
			$(this).find('span').removeClass('appr-cen').addClass('disable-butn');
			$(this).siblings(':input[type="hidden"]').val('HIDE');
		}
		else
		{
			$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
			$(this).siblings(':input[type="hidden"]').val('SHOW');
		}
	});
	
	/*----------------CUSTOM Select Tag-------------------------------*/
	$('.customSelectTag ul li a').live('click',function(e) {
    	e.preventDefault();
        var ele = jQuery(this);
        var li = ele.parent();
        var hiddenField = jQuery(li).parent().parent().find('input[type="hidden"]');
        var errorField = jQuery(li).parent().parent().find('label');
        var newHiddenFieldValues = '';

		if ($(li).find('a').hasClass('appr-cen'))
		{
			var ids                  = new Array();
          	var hiddenFieldValues    = $(li).parent().parent().find('input[type="hidden"]').val(); 
          	ids                      = hiddenFieldValues.split(',');
          	var index                = ids.indexOf(ele.attr('id'));
			ids.splice(index, 1);
	        newHiddenFieldValues = ids.join(); 
	        jQuery(hiddenField).val(newHiddenFieldValues);
	        $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
        } 
       	else
		{
        	var prefrencesId   = jQuery(hiddenField).val();
        	if(prefrencesId !="")
        		newHiddenFieldValues = prefrencesId+','+ele.attr('id'); 
          	else
          		newHiddenFieldValues= ele.attr('id');

        	$(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
        }
        if(newHiddenFieldValues == '')
        {
			$(errorField).text($(errorField).attr('error_msg'));
        }
        else
        {
        	$(errorField).text('');
        }
		$(hiddenField).val(newHiddenFieldValues);
   	});

   	$('.importance ul li a').live('click',function(e) {
		e.preventDefault();
	    var ele = jQuery(this);
        var prefrenceHiddenField = ele.parent().parent().parent().prev().find("input[type='hidden']").val();
	    if(prefrenceHiddenField =="")
	    {    
	    	return false; 
	    }
		
        var checkid = ele.parent().parent().parent().prev().attr('id');
	    var parentUl = ele.parent().parent();
		var li = jQuery(parentUl).find('li.Intro-Button-sel');
		
	    $(li).removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
	    $(ele).parent().addClass('Intro-Button-sel').find('a').removeClass('Intro-Button');
		
	    var selectedImportance = jQuery(parentUl).find('li.Intro-Button-sel a').attr('importanceVal');
	    parentUl.parent().find('input[type="hidden"]').val(selectedImportance);
	});
});


function validate_settings()
{
	var currentTab = $("#current_tab").val();
}

//Add New address 
function add_user_email(){
	$("#Email_error").text('');
	var new_mail = $("#txt_new_email").val();
	if(new_mail != '')
	{
		loading();
        $.ajax({ 
            url: base_url +"account/add_email", 
            type:"post",
            data:'new_email='+new_mail,
            cache: false,
            dataType:'json',
            success: function (data) {
            	stop_loading();
            	if(data.type == '1')
            	{
            		$("#txt_new_email").val(' ');
            		$("#new_email_error").html('');

            		//emailLists
            		var liHTML = '<li lang="'+data.user_email_id+'" class="emailRow">'+
                			'<div class="emailLeft">'+new_mail+' <span class="is_verify_email"><span class="emailunverified">*<?php echo translate_phrase("Unverified")?>*</span></span></div>'+
                			'<div class="emailCenter"><div class="Edit-Button01"> <a onclick="set_contact_mail('+data.user_email_id+')" href="javascript:;"><?php echo translate_phrase("Set as Contact Email") ?></a> </div></div>'+
                			'<div class="emailRight"><a href="javascript:;" onclick="remove_email('+data.user_email_id+')" ><span class="appr-cen">Remove</span></a></div>'+
                			'</li>';
	        			$("#emailLists").append($(liHTML).fadeIn());
	        			
	        			var varificationDIV = '<div class="email_verify_section" id="user_email_'+data.user_email_id+'"><div class="mobileRow"><p><?php echo translate_phrase("We have sent a verification email to "); ?>'+new_mail+'<span> <?php echo translate_phrase(" Please click on the verification link found in that email or simply enter the verification code in the email into the textbox below"); ?>:</span></p></div><div class="mobileRow"><div class="mobileRight"><div class="Left-coll01"><?php echo translate_phrase("Verification code found in yours verification email"); ?>:</div><input class="post-input email_verify_code" type="text"><div class="verifyBtn"><div class="Verification-Button"><a onclick="verify_email('+data.user_email_id+')" href="javascript:;"><?php echo translate_phrase("Verify Email");?></a></div></div></div><label class="input-hint error"></label></div></div>';
        				$("#add_email_section").after(varificationDIV);
                }
            	else
            	{
                	$("#new_email_error").html(data.msg);
                }
            }   
        });
	}
	else
	{
		$("#new_email_error").text("<?php echo translate_phrase('Please enter a new email address.')?>");		
	}
	$("#txt_new_email").focus();
} 


//Add New address 
function set_contact_mail(user_email_id){
	$("#Email_error").text('');
	if(user_email_id != '')
	{
		loading();
      	$.ajax({ 
          url: base_url +"account/set_contact_mail", 
          type:"post",
          data:'user_email_id='+user_email_id,
          cache: false,
          dataType:'json',
          success: function (data) {
          	stop_loading();
          	if(data.type == '1')
          	{
              	$.each($("#emailLists li"),function(i,item){
                  	if($(item).find('.emailCenter .Edit-Button01').length == 0)
                  	{
                  		$(item).find('.emailCenter').html('<div class="Edit-Button01"> <a onclick="set_contact_mail('+$(item).attr('lang')+')" href="javascript:;"><?php echo translate_phrase("Set as Contact Email")?></a> </div>');
                    }
                });
				$("#emailLists").find('li[lang="'+user_email_id+'"] .emailCenter').html('<?php echo translate_phrase("Current Contact Email")?>');
          	}
          	
          }
      });
	}
}

//Add New address 
function verify_email(user_email_id){
	var mainDiv = '#user_email_'+user_email_id;
	var verification_code = $(mainDiv).find('.email_verify_code').val();
	$("#Email_error").text('');
	if(user_email_id != '' && verification_code !='')
	{
		loading();
      	$.ajax({ 
          url: base_url +"account/verify_email", 
          type:"post",
          data:{user_email_id:user_email_id, verification_code:verification_code},
          cache: false,
          dataType:'json',
          success: function (data) {
          	stop_loading();
          	if(data.type == '1')
          	{
					$(mainDiv).slideUp('slow',function(){
   					$("#emailLists").find('li[lang="'+user_email_id+'"] .is_verify_email').html('<img class="mar-verify" src="<?php echo base_url()?>assets/images/verified.png" alt="verified email">');	
   					$(mainDiv).remove();
   				});
          	}
          	else
            {
                $(mainDiv).find('.error').text(data.msg);
            }
          }
      });
	}
	else
	{
		$(mainDiv).find('.email_verify_code').focus();
		$(mainDiv).find('.error').text('<?php echo translate_phrase("Please enter verification code from Email.");?>');
	}
}
//Add New address 
function remove_email(user_email_id){

	if(user_email_id != '')
	{
		loading();
        $.ajax({ 
            url: base_url +"account/remove_email", 
            type:"post",
            data:'user_email_id='+user_email_id,
            cache: false,
            dataType:'json',
            success: function (data) {
            	stop_loading();
            	if(data.type == '1')
            	{
            		$("#emailLists").find('li[lang="'+user_email_id+'"]').slideUp(function(){
            			$(this).remove();					         		
            		});
            		$('#user_email_'+user_email_id).slideUp(function(){$(this).remove();});
                }
               else
            	{
               	$("#Email_error").text(data.msg);
            	}        	
            }   
        });
	}
} 

//Add New address 
function change_phone_no_with_varification(){
	 
	var new_mobile_no = $("#new_mobile_no").val();
	var validateNumber = validatePhone(new_mobile_no);

 	if(new_mobile_no != '')
	{
		if(validateNumber == '1')
		{
			loading();
	      	$.ajax({
	          url: base_url +"account/change_phone_number", 
	          type:"post",
	          data:'new_mobile_no='+new_mobile_no,
	          cache: false,
	          dataType:'json',
	          success: function (data) {
	          	stop_loading();
	          	if(data.type == '1')
	          	{
	          		$("#mobileNumberError").html('');
	          	  	$("#new_mobile_error").html('');
	          	  	if($("#mobileDiv").css('display') == 'none')
	          	  	{
	          	  		$("#mobileDiv").fadeIn();
	          	  	}
	          	  
	          		$(".mobileNumber span").text(formate_mobile_number(new_mobile_no));
	          		$("#is_mobile_verify").html('<span class="emailunverified">*<?php echo translate_phrase('Unverified')?>*</span>');
	            }
	          	else
	          	{
	              	$("#new_mobile_error").html(data.msg);
	            }
	          }   
	      });
		}
		else{
	        $('#new_mobile_error').html(validateNumber);
	    }
	}
	else
	{
		$("#new_mobile_error").text("<?php echo translate_phrase('Please enter mobile number.')?>");		
	}
	$("#new_mobile_no").focus();
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

function send_sms(country_code,mobile_number,objLink){
    var url = '<?php echo base_url("user/send_verification_sms")?>';
    var validateNumber = validatePhone(mobile_number);
    if(validateNumber == '1' && country_code!="0") {
        if(sms_sent)
        {
	        if(smsUsed == false)
	        {
	        	loading();
	            $.ajax({ 
                    url:url, 
                    type:"post",
                    data:{country_code:country_code,mobile_number:mobile_number},
                    cache: false,
                    success: function (data) {
                    	stop_loading();
                		$('#mobileNumberError').html('');
                		if (data.indexOf("error_msg") === -1){
		            	 	jQuery('#verificationCodeDiv').fadeIn('fast',function(){
		            	 		$('#verification_msg').html(data);
		                	});
		                	
             		 		$(objLink).html('<?php echo translate_phrase("Re-send Verification SMS");?>');
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
            			$('#mobileNumberError').html('<?php echo translate_phrase("Please email support@datetix.com with your email address and mobile number and we will manually verify your account.");?>');
                    }     
                });
            }

         }
        else
        {
			$('#mobileNumberError').html('<?php echo translate_phrase("Please email support@datetix.com with your email address and mobile number and we will manually verify your account.");?>');
        }
    }
   	else{
        $('#mobileNumberError').html(validateNumber);
    }
}

function view_photo_request(status,user_photo_request_id)
{
	var oldStatus = $('#list_'+user_photo_request_id).attr('lang');
	
	var url = '<?php echo base_url("account/photo_request")?>';
	if(oldStatus != status)
	{
		loading();
		$.ajax({ 
			url:url+"/"+user_photo_request_id, 
			type:"post",
			data:'status='+status,
			cache: false,
			dataType:'json',
			success: function (data) {
				stop_loading();
				if(data.type == "1")
				{
					if(oldStatus == "0")
					{
						if(status == '1')
						{
							$('#list_'+user_photo_request_id).find('.approveBtn').toggle();	
							$('#list_'+user_photo_request_id).find('.approveLbl').toggle();
					
						}
						else if(status == '2'){
							
							$('#list_'+user_photo_request_id).find('.declineLbl').toggle();	
							$('#list_'+user_photo_request_id).find('.declineBtn').toggle();			
						}
					}
					else
					{
						$('#list_'+user_photo_request_id).find('.declineLbl').toggle();	
						$('#list_'+user_photo_request_id).find('.declineBtn').toggle();
						
						$('#list_'+user_photo_request_id).find('.approveBtn').toggle();	
						$('#list_'+user_photo_request_id).find('.approveLbl').toggle();
					}
					$('#list_'+user_photo_request_id).attr('lang',status);
				}
				else
				{
					$("#privacy_error").text(data.msg);
				}
			}  
		});
	}
	else{
		return false;
	}
}


function verify_sms(){
	
	var verification_code = $('#verification_code').val();
    var url = '<?php echo base_url("user/sms_verification")?>';
    if(verification_code!="") {
    	loading();
        $.ajax({ 
            url:url, 
            type:"post",
            data:'verification_code='+verification_code,
            cache: false,
            success: function (data) {
            	stop_loading();
                if(data=="1"){
                	jQuery('#verificationCodeDiv').slideUp('slow',function(){
                		$("#is_mobile_verify").html('<img class="mar-verify" src="<?php echo base_url()?>assets/images/verified.png" alt="verified email">');	
                    });
                }
                else 
                {
                	$('#verificationCodeDiv').find('label.error-msg').html(data);
                }
            }  
        });
   	}
    else
	{
		$('#verificationCodeDiv').find('label.error-msg').html('<p style="color:#FD2080;"><?php echo translate_phrase("Please enter verification code found in the SMS");?></p>');
   }
}
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title?></h1>
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<form
				action="<?php echo base_url().url_city_name() . '/setting.html';?>"
				method="post" enctype="multipart/form-data">
				<input type="hidden" id="current_tab" name="current_tab"
					value="accountTab" />
				<div class="emp-B-tabing-prt">
					<div class="emp-B-tabing-M-short" id="active_expire">
						<ul class='etabs'>
							<li class='tab tab-nav'><span></span><a id="accountTab"
								href="#account"><?php echo translate_phrase('Account') ?> </a></li>
							<li class='tab tab-nav'><span></span><a id="datingTab"
								href="#dating"><?php echo translate_phrase('Dating') ?> </a></li>
							<li class='tab tab-nav'><span></span><a id="privacyTab"
								href="#privacy"><?php echo translate_phrase('Privacy') ?> </a></li>
						</ul>

						<div class="step-form-Main Mar-top-none Top-radius-none"
							id="account">
							<div class="step-form-Part">
								<h2>
								<?php echo translate_phrase('Account Information');?>
								</h2>
								<div class="accountRow">
									<div class="accountRowLeft">
									<?php echo translate_phrase("Your Account Upgrades")?>
										:
									</div>
									<div class="accountRowRight">
									<?php if($user_membership_option):?>
									<?php foreach ($user_membership_option as $member_option):?>
										<div class="f-decr">
											<a href="javascript:;"><span class="appr-cen"><?php echo $member_option['description']?>
											</span> </a> <span class="expires-on unver-top-mar fl"> <?php if (isset($member_option['expiry_date'])){
												echo (date("Y-m-d",strtotime($member_option['expiry_date'])) < SQL_DATE)?translate_phrase('(already expired on '):translate_phrase('(expires on ');
												echo date(DATE_FORMATE,strtotime($member_option['expiry_date'])).')';
											}
											?> </span>
										</div>
										<?php endforeach;?>
										<?php endif;?>

										<div class="f-decr">
											<div class="yellow-btn Mar-top-none">
												<a
													href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase('Upgrade Account')?>
												</a>
											</div>
										</div>
										
										<div class="f-decr unver-top-mar">
											<a href="javascript:;" id="autorenewToggleBut" class="<?php if($user_data['auto_renew'] == 1):?>appr-cen<?php else:?>disable-butn <?php endif;?>"><?php echo translate_phrase("Auto-Renew Upgrades")?></a>
											<input type="hidden" name="auto_renew" value="<?php echo $user_data['auto_renew'];?>">
										</div>
									</div>
								</div>

								<div class="accountRow">
									<div class="accountRowLeft">
									<?php echo translate_phrase("Account Status")?>
										:
									</div>
									<div class="acntRightDown">
										<dl id="year" class="dropdown-dt common-dropdown">
											<dt>
												<a href="javascript:;"><span><?php echo $user_account['description']?></span> </a>
												<input name="account_status_id" value="<?php echo $user_data['account_status_id'];?>" type="hidden">
											</dt>
											<dd>
												<ul>
												<?php if($account_data):?>
													<?php foreach ($account_data as $account):?><li>
													<li><a href="javascript:;" key="<?php echo $account['account_status_id'];?>"><?php echo $account['description']?></a></li>
													<?php endforeach;?>
												<?php endif;?>
												</ul>
											</dd>
										</dl>
									</div>
								</div>

								<div class="upload-Button-main">
									<div class="Edit-Button01">
										<a
											href="<?php echo base_url(url_city_name() .'/change-password.html');?>"><?php echo translate_phrase("Change Password")?>
										</a>
									</div>
								</div>
								<div class="sortby">
									<h2><?php echo translate_phrase('Mobile Phone Number');?></h2>
									
									<div class="mobileRow" style="display: <?php echo $user_data['mobile_phone_number']?'block':'none';?>;" id="mobileDiv">
										<div class="mobileLeft">
											
											<div class="mobileNumber"> (+<?php echo $country_code;?>) <span><?php echo formate_mobile_number($user_data['mobile_phone_number'])?></span></div>
											<div class="unver-top-mar fl" id="is_mobile_verify">
												<?php if($user_data['mobile_phone_is_verified']):?>
													<img class="mar-verify" src="<?php echo base_url()?>assets/images/verified.png" alt="verified email">
												<?php else:?>
													<span class="emailunverified">*<?php echo translate_phrase('Unverified')?>*</span>
												<?php endif;?>
											</div>
										</div>

										<div class="mobileRight">
											<div class="Edit-Button01">
												<a href="javascript:;" onclick="send_sms('<?php echo $country_code;?>','<?php echo $user_data['mobile_phone_number']?>',this)"><?php echo ($user_data['mobile_phone_verification_code_sent'])?translate_phrase('Re-send Verification SMS'):translate_phrase('Send Verification SMS');?></a>
											</div>
										</div>
										<label id="mobileNumberError" class="input-hint error-msg unver-top-mar"></label>
									</div>
									
									<div class="mobileRow">
										<div class="mobileRight">
											<div class="Edit-Button01">
												<a id="AddMobileNumberLink" href="javascript:;"><?php echo $user_data['mobile_phone_number']?translate_phrase('Change Mobile Phone Number'):translate_phrase('Add Mobile Phone Number')?></a>
											</div>
										</div>
									</div>

									<div class="mobileRow" id="addNewMobile" style="display: <?php echo $user_data['mobile_phone_number']?'none':'block';?>;">
										<div class="mobileLeft">
											<div class="Left-coll01"><?php echo translate_phrase('Your new mobile phone number')?>:</div>
											<div class="mobileNumber">
												<input class="mobileInput ten-char-width" type="text" value="" id="new_mobile_no" name="new_mobile_no">
											</div>
										</div>

										<div class="mobileRight">
											<div class="Edit-Button01">
												<a href="javascript:;" onclick="change_phone_no_with_varification()"><?php echo translate_phrase('Send Verification SMS')?></a>
											</div>
										</div>
										<label id="new_mobile_error" class="input-hint error-msg unver-top-mar"></label>
									</div>

									<div id="verificationCodeDiv" style="display: <?php echo $user_data['mobile_phone_number'] && $user_data['mobile_phone_is_verified'] != 1 && $user_data['mobile_phone_verification_code_sent']?'block':'none';?>">
										<div class="mobileRow">
											<p><?php echo translate_phrase('We have sent you a SMS with a verification code to')?><span class="phone-num2">(+<?php echo $country_code;?>) <?php echo $user_data['mobile_phone_number']?></span>.
												<span><?php echo translate_phrase('Please enter the verification code in the SMS into the textbox below')?>:</span>
											</p>
										</div>
	
										<div class="mobileRow">
											<div class="mobileRight">
												<div class="Left-coll01"><?php echo translate_phrase('Verification code found in yours SMS')?>:</div>
												<input class="post-input ten-char-width" type="text" id="verification_code">
												<div class="verifyBtn">
													<div class="Verification-Button"><a href="javascript:;" onclick="verify_sms()"><?php echo translate_phrase('Verify')?></a></div>
												</div>
											</div>
										</div>
										<label class="input-hint error-msg unver-top-mar"></label>
									</div>
								</div>
								
								<div class="userBox">
									<h2><?php echo translate_phrase('Email Addresses')?>:</h2>
									<label id="Email_error" class="input-hint error-msg unver-top-mar"></label>
									<?php $verify_section_html = '';?>
									<ul class="mail-list" id="emailLists">
									<?php if($user_emails):?>
									<?php foreach ($user_emails as $email):?>
										<li class="emailRow"
											lang="<?php echo $email['user_email_id']?>">
											<div class="emailLeft">
											<?php echo $email['email_address'];?>
											<span class="is_verify_email">
											<?php if($email['is_verified']):?>
												<img class="mar-verify"
													src="<?php echo base_url()?>assets/images/verified.png"
													alt="">
											<?php else:?>
												<span class="emailunverified">*<?php echo translate_phrase('Unverified')?>*</span>												
											<?php $verify_section_html .= '
											<div class="email_verify_section" id="user_email_'.$email['user_email_id'].'">
												<div class="mobileRow">
													<p>'.translate_phrase('We have sent a verification email to ').$email['email_address'].'. <span>'.translate_phrase(' Please click on the verification link found in that email or simply enter the verification code in the email into the textbox below').':</span></p>
												</div>
																					
												<div class="mobileRow">
													<div class="mobileRight">
														<div class="Left-coll01">'.translate_phrase('Verification code found in yours verification email').':</div>
														<input class="post-input email_verify_code" type="text">
														<div class="verifyBtn">
															<div class="Verification-Button">
																<a onclick="verify_email('.$email['user_email_id'].')" href="javascript:;">'.translate_phrase('Verify Email').'</a>
															</div>
														</div>
													</div>
													<label class="input-hint error"></label>
												</div>
											</div>';											
											?>
											<?php endif;?>
											</span> 
											</div>
											<div class="emailCenter">
											<?php if($email['is_contact']):?>
											<?php echo translate_phrase('Current Contact Email');?>
											<?php else:?>
												<div class="Edit-Button01">
													<a
														onclick="set_contact_mail(<?php echo $email['user_email_id']?>)"
														href="javascript:;"><?php echo translate_phrase('Set as Contact Email')?>
													</a>
												</div>
												<?php endif;?>
											</div>
											<div class="emailRight">
												<a href="javascript:;"
													onclick="remove_email(<?php echo $email['user_email_id']?>)"><span
													class="appr-cen"><?php echo translate_phrase('Remove')?> </span>
												</a>
											</div>
										</li>
										<?php endforeach;?>
										<?php endif;?>
									</ul>

									<div class="emailRow" id="add_email_section">
										<div class="emailLeft">
											<div class="mobileNumber">
												<input class="mobileInput" type="text"
													placeholder="<?php echo translate_phrase('Enter a new email address')?>"
													name="new_email" id="txt_new_email"> <label
													class="input-hint error" id="new_email_error"></label>
											</div>
										</div>
										<div class="mobileRight">
											<div class="Edit-Button01">
												<a onclick="add_user_email()" href="javascript:;"><?php echo translate_phrase('Add Email Address to Account')?>
												</a>
											</div>
										</div>
									</div>
									<?php echo $verify_section_html;?>							
								</div>

								<div class="sortby">
									<h2><?php echo translate_phrase('Other Verification Information')?>:</h2>

									<div class="infoRow">
										<div class="infoRowLeft">
											<div class="fbTxt">
												<?php echo translate_phrase('Your WeChat ID')?>:<img
													src="<?php echo base_url()?>assets/images/wechat-logo.jpg" />
											</div>
										</div>
										<div class="infoRowRight">
											<input name="wechat_id"
												value="<?php echo isset($user_data['wechat_id'])?$user_data['wechat_id']:''?>"
												type="text" class="post-input" placeholder="<?php echo translate_phrase('e.g. john123')?>" />
											<p>
											</p>
										</div>
									</div>
									
									<div class="infoRow">
										<div class="infoRowLeft">
											<div class="fbTxt">
												<?php echo translate_phrase('Your Facebook page')?>:<img
													src="<?php echo base_url()?>assets/images/fb-logo.jpg" />
											</div>
										</div>
										<div class="infoRowRight">
											<input name="facebook_page"
												value="<?php echo isset($user_data['facebook_page'])?$user_data['facebook_page']:''?>"
												type="text" class="infoInput"
												placeholder="<?php echo translate_phrase('e.g. http://www.facebook.com/johnsmith')?>" />

											<p>
												(
												<?php echo translate_phrase('Sign in to Facebook. Click on your name in the top right corner. Copy web address in your browser and paste it here.')?>
												)
											</p>
										</div>
									</div>

									<div class="infoRow">
										<div class="infoRowLeft">
											<div class="linkedTxt">
												<?php echo translate_phrase('Your LinkedIn page')?>: <img
													src="<?php echo base_url()?>assets/images/linked-logo.jpg" />
											</div>
										</div>
										<div class="infoRowRight">
											<input name="linkedin_page"
												value="<?php echo isset($user_data['linkedin_page'])?$user_data['linkedin_page']:''?>"
												type="text" class="infoInput"
												placeholder="<?php echo translate_phrase('e.g. http://www.linkedin.com/in/johnsmith')?>" />
											<p>
												(
												<?php echo translate_phrase('Sign in to Linkedin. Click on Profile -> View Profile. Copy the URL link you see below your photo and paste it here')?>
												.)
											</p>
										</div>
									</div>

									<div class="infoRow">
										<div class="infoRowLeft">
											<div class="twitterTxt">
											<?php echo translate_phrase('Your Twitter username')?>
												: <img
													src="<?php echo base_url()?>assets/images/twitter-logo.jpg" />
											</div>
										</div>
										<div class="infoRowRight">
											<input name="twitter_username"
												value="<?php echo isset($user_data['twitter_username'])?$user_data['twitter_username']:''?>"
												type="text" class="infoInput"
												placeholder="<?php echo translate_phrase('e.g. johnsmith')?>" />
										</div>
									</div>

									<div class="infoRow">
										<div class="infoRowLeft">
										<?php echo translate_phrase('Take a photo of any government issued photo ID (e.g. your ID card or passport page):')?>
										</div>
										<div class="infoRowRight file-upload">
											<ul class="img-container">
												<li class="upload-part upload-profile-pic"><img
													src="<?php echo $user_data['photo_id']?base_url('user_photos/user_'.$user_data['user_id'].'/'.$user_data['photo_id']):''?>"
													data-src=""></li>
											</ul>
											<div class="upload-Button-main">
												<span class="upload-button"> <label><?php echo translate_phrase('Upload Photo')?>...</label>
													<input type="file"
													data-url="<?php echo base_url() ?>user/upload/photo_id_or_passport"
													name="photo_id_or_passport" id="photo_id_or_passport"> </span>
												<div class="Delete-Photo01">
													<a class="btn_fl_upload_photo" href="javascript:;"
														data-url="<?php echo base_url() ?>user/delete_photo_id/"><?php echo translate_phrase('Delete')?>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="step-form-Main Mar-top-none Top-radius-none"
							id="dating">
							<div class="step-form-Part">
								<h2>
									<span class="Red-color">*</span><?php echo translate_phrase('Which days of the week do you
									prefer to have first dates?')?>
								</h2>

								<div class="f-decrMAIN customSelectTag">
								<?php $pref_date_days = explode(',', $user_data['preferred_date_days']);?>
									<ul>
										<li><a id="1" href="javascript:;"
											class="<?php echo (in_array('1', $pref_date_days))?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Mondays')?>
										</a></li>
									</ul>
									<ul>
										<li><a id="2" href="javascript:;"
											class="<?php echo (in_array('2', $pref_date_days))?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Tuesdays')?>
										</a></li>
									</ul>
									<ul>
										<li><a id="3" href="javascript:;"
											class="<?php echo (in_array('3', $pref_date_days))?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Wednesdays')?>
										</a></li>
									</ul>
									<ul>
										<li><a id="4" href="javascript:;"
											class="<?php echo (in_array('4', $pref_date_days))?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Thursdays')?>
										</a>
										
										<li>
									
									</ul>
									<ul>
										<li><a id="5" href="javascript:;"
											class="<?php echo (in_array('5', $pref_date_days))?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Fridays')?>
										</a></li>
									</ul>
									<ul>
										<li><a id="6" href="javascript:;"
											class="<?php echo (in_array('6', $pref_date_days))?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Saturdays')?>
										</a></li>
									</ul>
									<ul>
										<li><a id="7" href="javascript:;"
											class="<?php echo (in_array('6', $pref_date_days))?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Sundays')?>
										</a></li>
									</ul>
									<input type="hidden" name="daysPreference" id="daysPreference"
										value="<?php echo $user_data['preferred_date_days'];?>"> <label
										class="input-hint error"
										error_msg="<?php echo translate_phrase('Field is required.')?>"></label>
								</div>

								<h2>
									<span class="Red-color">*</span>
									<?php echo translate_phrase('What type(s) of first dates do you prefer?')?>
								</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag" id="dateType">
									<?php foreach ($date_type as $key => $value) :?>
										<ul>
											<li><a id="<?php echo $value['date_type_id'];?>"
												class="<?php echo (in_array($value['date_type_id'], $user_prefered_date_type_ids))?'appr-cen':'disable-butn';?>"
												href="javascript:;"><?php echo $value['description'];?> </a>
											</li>
										</ul>
										<?php endforeach;?>
										<input type="hidden" name="dateTypePreference"
											id="dateTypePreference"
											value="<?php echo implode(',', $user_prefered_date_type_ids)?>">
										<label class="input-hint error"
											error_msg="<?php echo translate_phrase('Field is required.')?>"></label>
									</div>

									<div class="f-decrMAIN">
										<div class="FDates-MAin">
											<input name="otherDateTypePrefrence"
												id="otherDateTypePrefrence" type="text" class="FDates-input"
												placeholder="Describe any other preferred first date ideas"
												value="<?php echo $user_prefered_date_type_other['date_type_other']?>" />
										</div>
									</div>
								</div>
								<div class="sortby">
									<div class="mobileRow">
										<h2>
											<span class="Red-color">*</span>
											<?php echo translate_phrase('How would you prefer to be contacted by DateTix when we find high quality matches for you')?>
											?
										</h2>
										<div class="f-decrMAIN customSelectTag" id="contactMethod">
										<?php foreach ($contact_method as $key => $value) :?>
											<ul>
												<li><a id="<?php echo $value['contact_method_id'];?>"
													class="<?php echo (in_array($value['contact_method_id'], $user_preferred_contact_method_ids))?'appr-cen':'disable-butn';?>"
													href="javascript:;"><?php echo $value['description'];?> </a>
												</li>
											</ul>
											<?php endforeach;?>
											<input type="hidden" name="contactMethodPreference"
												id="contactMethodPreference"
												value="<?php echo implode(',', $user_preferred_contact_method_ids)?>">
											<label class="input-hint error"
												error_msg="<?php echo translate_phrase('Field is required.')?>"></label>
										</div>
									</div>

									<div class="mobileRow">
										<h2>
										<?php echo translate_phrase('How selective should DateTix be in introducing you to potential matches?')?>
										</h2>
										<div class="f-decrMAIN importance" id="matchMaker">
											<ul>
												<li class="<?php if($user_data['matchmaking_selectivity'] == 5):?>Intro-Button-sel<?php endif;?>">
													<a 
													class="<?php if($user_data['matchmaking_selectivity'] != 5):?>Intro-Button<?php endif;?>"
													href="javascript:;" importanceVal="5"><?php echo translate_phrase('Not at all')?></a>
												</li>
												<li class="<?php if($user_data['matchmaking_selectivity'] == 4):?>Intro-Button-sel<?php endif;?>">
													<a 
													class="<?php if($user_data['matchmaking_selectivity'] != 4):?>Intro-Button<?php endif;?>"
													href="javascript:;" importanceVal="4"><?php echo translate_phrase('Slightly')?></a>
												</li>
												<li class="<?php if($user_data['matchmaking_selectivity'] == 3):?>Intro-Button-sel<?php endif;?>">
													<a 
													class="<?php if($user_data['matchmaking_selectivity'] != 3):?>Intro-Button<?php endif;?>"
													href="javascript:;" importanceVal="3"><?php echo translate_phrase('Average')?></a>
												</li>
												<li class="<?php if($user_data['matchmaking_selectivity'] == 2):?>Intro-Button-sel<?php endif;?>">
													<a 
													class="<?php if($user_data['matchmaking_selectivity'] != 2):?>Intro-Button<?php endif;?>"
													href="javascript:;" importanceVal="2"><?php echo translate_phrase('Very')?></a>
												</li>
												<li class="<?php if($user_data['matchmaking_selectivity'] == 1):?>Intro-Button-sel<?php endif;?>">
													<a 
													class="<?php if($user_data['matchmaking_selectivity'] != 1):?>Intro-Button<?php endif;?>"
													href="javascript:;" importanceVal="1"><?php echo translate_phrase('Extremely')?></a>
												</li>
												<input type="hidden" name="matchmaking_selectivity" id="matchMakerImportance" value="<?php echo $user_data['matchmaking_selectivity'];?>"/>
												<label id="matchMakerError" class="input-hint error_msg"></label>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="step-form-Main Mar-top-none Top-radius-none"
							id="privacy">
							
							<div class="step-form-Part">
								<h2><?php echo translate_phrase('What profile information could we show when we introduce you to potential dates?')?></h2>
								<div class="f-decrMAIN">
									<div class="f-decr">
										<a href="javascript:;" class="privacy-toggleBtn" ><span class="<?php echo $user_data['privacy_first_name'] == 'SHOW'?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Your first name')?></span></a>
										<input type="hidden" name="privacy_first_name" value="<?php echo $user_data['privacy_first_name'] == 'SHOW'?'SHOW':'HIDE';?>">
									</div>
									
									<div class="f-decr">
										<a href="javascript:;" class="privacy-toggleBtn" ><span class="<?php echo $user_data['privacy_photos'] == 'SHOW'?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Your photos')?></span></a>
										<input type="hidden" name="privacy_photos" value="<?php echo $user_data['privacy_photos'] == 'SHOW'?'SHOW':'HIDE';?>">
									</div>
									
									<div class="f-decr">
										<a href="javascript:;" class="privacy-toggleBtn"><span class="<?php echo $user_data['privacy_contact_email'] == 'SHOW'?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Your contact email address')?></span></a>
										<input type="hidden" name="privacy_contact_email" value="<?php echo $user_data['privacy_contact_email'] == 'SHOW'?'SHOW':'HIDE';?>">
									</div>
									
									<div class="f-decr">
										<a href="javascript:;" class="privacy-toggleBtn"> <span class="<?php echo $user_data['privacy_mobile_phone'] == 'SHOW'?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Your mobile phone number')?></span></a>
										<input type="hidden" name="privacy_mobile_phone" value="<?php echo $user_data['privacy_mobile_phone'] == 'SHOW'?'SHOW':'HIDE';?>">
									</div>
									
									<div class="f-decr">
										<a href="javascript:;" class="privacy-toggleBtn"> <span class="<?php echo $user_data['privacy_wechat'] == 'SHOW'?'appr-cen':'disable-butn';?>"><?php echo translate_phrase('Your WeChat ID')?></span></a>
										<input type="hidden" name="privacy_wechat" value="<?php echo $user_data['privacy_wechat'] == 'SHOW'?'SHOW':'HIDE';?>">
									</div>
									
								</div>
							</div>
							
							<?php if($user_photo_request):?>
							<div class="step-form-Part sortby">
								<h2><?php echo translate_phrase('Requests to view your photos')?></h2>
								<label id="privacy_error" class="input-hint error-msg unver-top-mar"></label>
								<ul class="applications cms-list comn-top-mar"">
									<?php foreach($user_photo_request as $user):?>
									<li class="list">
										<span><?php echo $user['first_name'].' '.$user['last_name']?></span>
										<?php 
											$btn_appr_txt = translate_phrase("Approve");
											$btn_appr_toggle_txt = translate_phrase("Approved");
											
											$btn_dcln_txt = translate_phrase("Decline");
											$btn_dcln_toggle_txt = translate_phrase("Declined");
										?>
										
										<span class="btns"  id="list_<?php echo $user['user_photo_request_id'];?>" lang="<?php echo $user['status'];?>">
											<?php if($user['status'] == '1'):?>
											<span style="display: inline-block" class="pink-colr approveLbl"><?php echo $btn_appr_toggle_txt;?></span>
											<button style="display: none" onclick="view_photo_request('1','<?php echo $user['user_photo_request_id'];?>')" type="button" class="btn btn-pink btn-small approveBtn"><?php echo $btn_appr_txt;?></button>
											<?php else:?>
											<span style="display: none" class="pink-colr approveBtn"><?php echo $btn_appr_toggle_txt;?></span>
											<button style="display: inline-block" onclick="view_photo_request('1','<?php echo $user['user_photo_request_id'];?>')" type="button" class="btn btn-pink btn-small approveBtn"><?php echo $btn_appr_txt;?></button>
											<?php endif;?>
											
											<?php if($user['status'] == '2'):?>
											<span style="display: inline-block" class="pink-colr declineLbl"><?php echo $btn_dcln_toggle_txt;?></span>
											<button style="display: none" onclick="view_photo_request('2','<?php echo $user['user_photo_request_id'];?>')" type="button" class="btn btn-blue btn-small declineBtn"><?php echo $btn_dcln_txt;?></button>
											<?php else:?>
											<span style="display: none" class="pink-colr declineLbl"><?php echo $btn_dcln_toggle_txt;?></span>
											<button style="display: inline-block" onclick="view_photo_request('2','<?php echo $user['user_photo_request_id'];?>')" type="button" class="btn btn-blue btn-small declineBtn"><?php echo $btn_dcln_txt;?></button>
											<?php endif;?>
										</span>
										
										<p><?php echo date('M j, Y - H:i',strtotime($user['request_time']))?> </p>
									</li>									
									<?php endforeach;?>							
								</ul>
							</div>
							<?php endif;?>
							
						</div>
					</div>
				</div>

				<div class="Nex-mar">
					<button id="submit_button" class="Next-butM" value="Submit"
						name="submit" type="submit" onclick="return validate_settings();">Save
						Changes</button>
				</div>
			</form>
		</div>
	</div>
</div>
