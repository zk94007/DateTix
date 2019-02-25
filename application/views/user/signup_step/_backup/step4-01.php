<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script type="text/javascript">
var base_url = '<?php echo base_url() ?>';
var user_id = '<?php echo $this->session->userdata('user_id');?>';
var prevInsertCompany = '';
$(document).ready(function () {
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
    
    
	$(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});
	auto_complete_company();
});
function scrollToDiv(id)
{
	$('body').scrollTo($('#'+id),800,{'axis':'y'});
}
</script>
<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="signupForm"
			action="<?php echo base_url().url_city_name() . '/'.$this->singup_name.'-4.html';?>"
			method="post" enctype="multipart/form-data">
			
			<label class="input-hint error"><?php echo $this->session->flashdata('edit_profile_msg_error');?></label>
			
			
			<div id="career">
					<div class="Apply-Step1-a-main">
						<!-- FORM Header start (Progress) -->
					<!--<div class="A-step-partM">
							<div class="step-backBG">
								<div class="step-BOX-Main">
									<div class="step-bg-selected">
										<span>1</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Describe Yourself')?>
									</div>
								</div>
								<div class="step-BOX-Main mar-auto">
									<div class="step-bg-Unselected">
										<span>2</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Your Dating Preferences')?>
									</div>
								</div>
								<div class="step-BOX-Main fr wh-clr">
									<div class="step-bg-Unselected">
										<span>3</span>
									</div>
									<div class="step-ttle">
									<?php echo translate_phrase('Submit Application')?>
									</div>
								</div>
							</div>
						</div>-->
						<!-- Header complete -->

						<div class="step-form-Main">
							<div class="step-form-Part">
								<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field')?></div>
								<div class="edu-main">
									<!--<div class="aps-d-top">
										<h2> <?php echo translate_phrase('Where are you in your career')?> ?<span class="Redstar">*</span></h2>
										<div class="care-boxM">
										<?php echo form_dt_dropdown('career_stage_id',$carrier_stage,'','class="dropdown-dt rangedowndomain"',translate_phrase('Select career stage '),"hiddenfield");?>
											<label id="careerStageError" class="input-hint error"></label>
										</div>
									</div>-->
									<?php if($annual_income_range):?>
									<div class="aps-d-top">
										<h2> <?php echo translate_phrase('What is your annual income range')?> ? </h2>
										<div class="comn-top-mar fl">
											<?php foreach($annual_income_range as $id=>$value):?>
											<a href="javascript:;" class="rdo_div" key="<?php echo $id;?>"><span class="disable-butn"><?php echo $value;?></span></a>
											<?php endforeach;?>
											<input type="hidden" name="annual_income_range_id" id="annual_income_range_id" value="">
										</div>
									</div>
									<?php endif; ?>
									<div class="edu-ystudy">
										<h2> <?php echo translate_phrase('What kind of work do you do')?> ? </h2>
										<?php
										if($company_count>0){
											$company_show        = 'style="display: none;"';
											$company_button_show = 'style="display: block;"';
										} else{
											$company_show        = 'style="display: block;"';
											$company_button_show = 'style="display: none;"';
										}
										?>
										<div class="school-inner-container" id="list_company_main"
										<?php echo $company_button_show;?>>
											<div class="study-innr-M" id="list_company">
											<?php

											foreach($user_company_id as $row ){
												$language_id        = 1;
												$company_details    = $this->model_user->get_company_details($row,$language_id);
												echo $list_company  = $this->model_user->list_company_details($row,$company_details,$language_id);
											}
											?>
											</div>
											<div <?php echo $company_button_show;?> class="Edit-Button01"
												id="add_company_button">
												<a onclick="show_div_company();" href="javascript:;"><?php echo translate_phrase('Add Another Job')?>
												</a>
											</div>
										</div>
									</div>
								</div>
								<div class="last-bor"></div>
								<div id="add_companies" class="fl" <?php echo $company_show;?>>
									<span class="suc-msg" id="school_company"></span>

									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Company name')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<div class="drop-down-wrapper-full">
												<dl class="schooldowndomain">
													<dt>
														<span> <input class="Degree-input company-name"
															id="company_name" name="company_name" type="text"
															onkeyup="auto_complete_company();"
															onblur="show_company_logo();show_company_industry();show_company_domain();" />
														</span>
													</dt>
													<!-- autocomplete dd -->
													<dd id="auto-company-container" class="autosuggestfull"></dd>
												</dl>
												<label id="company_name_err" class="input-hint error"></label>
												<input type="hidden" id="selectedFromAvailableCompanies"
													value="no">
											</div>

											<div class="sch-logoR" id="company_logo"></div>

											<div class="M-topBut">
												<div class="skil-check-area-01">
													<ul>
														<li><span> <input type="checkbox" checked="checked"
																value="1" id="show_company_name" class="field checkbox">
																<label class="choice" for="show_company_name"><?php echo translate_phrase('Show company name to your matches')?>
															</label> </span>
														</li>
													</ul>
												</div>
											</div>
										</div>

									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Company industry')?>
											:
										</div>
										<div class="sfp-1-Right" id="company_industry">
										<?php echo form_dt_dropdown('industry_id',$industry,'','class="dropdown-dt majordowndomain company_industry_dd_id"',translate_phrase(' Select company industry '),"hiddenfield");?>
										</div>
									</div>
									<!--<div class="sfp-1-main">
										<div class="sfp-1-Left">
											<?php echo translate_phrase('Job function')?>
											:
										</div>
										<div class="sfp-1-Right" id="job_function_dd">
										<?php echo form_dt_dropdown('job_function_id',$job_functions,'','class="dropdown-dt majordowndomain"',translate_phrase('Select job function'),"hiddenfield");?>
										</div>
									</div>-->
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
											<?php echo translate_phrase('Job title')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<div class="post-input-wrap">
												<input id="job_title" type="text" class="Degree-input"
													placeholder="e.g. Software Engineer" /> <label
													id="job_title_err" class="input-hint error"></label>
											</div>
										</div>
									</div>
									
									<!--<div class="sfp-1-main">
									<?php
									//from years
									for($i=date('Y');$i>=1910;$i--){
										$company_years[$i] = $i;
									}

									//to years
									for($i=date('Y');$i>=1910;$i--){
										$company_years_to[$i] = $i;
									}

									?>
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Years worked')?>
											:
										</div>
										<div class="sfp-1-Right">
										<?php
										$job_year_end['9999'] = 'Present';
										for ($i = date('Y'); $i >= 1910; $i--)
										{
											$job_year_end[$i] = $i;
										}
										?>
										<?php echo form_dt_dropdown('years_worked_start', $school_years, '', 'id="years_worked_start_dl" class="dropdown-dt drop-start-year"', ' - - -', "hiddenfield"); ?>
											<div class="centimeter">
											<?php echo translate_phrase('to') ?>
											</div>
											<?php echo form_dt_dropdown('years_worked_end', $job_year_end, '', 'id="years_worked_end_dl" class="dropdown-dt drop-end-year"', ' - - -', "hiddenfield"); ?>
											<label id="year_work_err" class="input-hint error"></label>
										</div>
									</div>-->
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Company Email')?>
											:
										</div>
										<div class="sfp-1-Right">
											<div class="post-input-wrap">
												<input id="company_email_address"
													name="company_email_address" type="text"
													class="post-input" /> <label id="company_email_error"
													class="input-hint error"></label>
											</div>

											<div class="drop-down-wrapper-school_domain">
												<div class="sel-emailR" id="company_domain"></div>
												<label id="company_domain_err" class="input-hint error"></label>
											</div>																																	
										</div>
									</div>
																		
									<div class="sfp-1-Right">
										<input type="hidden" value="" name="user_company_id"
											id="user_company_id">
										<div class="Edit-Button01">
											<a onclick="add_company();" href="javascript:;"
												id="company_button"><?php echo translate_phrase('Add Job')?>
											</a>
										</div>
										<div class="Delete-Photo01">
											<a href="javascript:;" onclick="cancel_company();"
												id="company_cancel_button"><?php echo translate_phrase('Cancel')?>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="Nex-mar">
							<a href="javascript:;" id="ureg_sub"
								onclick="add_company('next_step');" class="Next-butM"><?php echo translate_phrase("You're Almost Done!")?>
							</a>
						</div>
					</div>
				</div>
				<!--*********Apply-Step1-D-Page close*********-->			
		</form>
	</div>
</div>
<script>
var autocompany = true;
function auto_complete_company(){
	if(autocompany)
	{
		loading();
		$.ajax({
			url: '<?php echo base_url(); ?>' +"user/auto_complete_company/", 
			dataType: "json", 
			type:"post",
			cache: false,
			success: function (data) {
				autocompany = false;
				stop_loading();
				var i="0";
				var availableTags=new Array();
				$.each(data,function(id,description) {
					availableTags[i]= description  ;
					i= parseInt(i)+parseInt(1);
				});
				$( "#company_name" ).autocomplete({
					appendTo:"#auto-company-container",
						source: availableTags,
						minLength: 1,
						select : function(event, ui){ 
						show_company_logo(ui.item.value);
						show_company_domain(ui.item.value)
						show_company_industry(ui.item.value)
						jQuery('#selectedFromAvailableCompanies').val('yes')
					}
				});
			}     
		});
	}   	
}
var domainSelectedCompany = '';
function show_company_domain(company_name){
	if(typeof(company_name) == 'undefined' || company_name == '')
	{
		var company_name      = $('#company_name').val();
	}
	
	if(company_name != domainSelectedCompany)
	{
		$.ajax({ 
			url: '<?php echo base_url(); ?>' +"user/show_company_domain/", 
			type:"post",
			data:'company_name='+company_name,
			cache: false,
			success: function (data) {
				domainSelectedCompany = company_name;
				$('#company_domain').html(data);
				if (data.length > 0) {
					$('#com_atsymbol').show();
					$('#company_email_address').removeClass("medium");
				} else{
					$('#com_atsymbol').hide();
					$('#company_email_address').addClass("medium");
				}   
			}     
		});
	}	
}
function remove_company(user_company_id){
	 $("#user_company"+user_company_id).remove();
	 $.ajax({ 
		url:'<?php echo base_url("user/remove_company")?>', 
		type:"post",
		data:"user_company_id="+user_company_id,
		cache: false,
		success: function (data) {
			if(data=="0"){
				prevInsertCompany = '';
				$('#list_company_main').fadeOut();
				$('#add_companies').fadeIn();
				scrollToDiv('add_companies');
				$("#company_name").focus();
			}
		}     
   });
}
    
function send_verification_mail(user_school_id,mail_for,email){
	var url = '<?php echo base_url("user/send_verification_email")?>';
	loading();
		$.ajax({ 
			url:url, 
			type:"post",
			data:'id='+user_school_id+'&mail_for='+mail_for+'&email='+email,
			cache: false,
			success: function (data) {
			stop_loading();
				if(data==1){
					if(mail_for=='school'){
						$('#error'+user_school_id).html('');
						//$('#link'+user_school_id).remove();
						$('#verified'+user_school_id).remove();
						var varifyDiv = '<div id="verified'+user_school_id+'" class="verfiy-message" ><div class="varify-text"><?php echo translate_phrase("A verification email has been sent to ");?> '+email+'</div><div class="inline-form"><label class="input-label"><?php echo translate_phrase("Enter the verification code in the verification email you just received");?>:</label><div class="input-wrapper"><input class="Degree-input" name="school_verification_code'+user_school_id+'" id="school_verification_code'+user_school_id+'"><label class="input-hint error" id="email_error'+user_school_id+'" ></label></div><a class="Edit-Button01" href="javascript:;" onclick="verify_email('+user_school_id+',\'school\');">Verify</a></div></div>'
						//$('#user_school'+user_school_id).append(varifyDiv);
						$('#user_school'+user_school_id).find('.Verification-Button').after(varifyDiv);
						$('#sc_verified'+user_school_id).find('a').text("<?php echo translate_phrase('Re-send Verification Code')?>");
					}
					if(mail_for=='company'){
						$('#error_com'+user_school_id).html('');
						  var varifyDiv = '<div id="company_verified'+user_school_id+'" class="verfiy-message" ><div class="varify-text"><?php echo translate_phrase("A verification email has been sent to ");?> '+email+'</div><div class="inline-form"><label class="input-label"><?php echo translate_phrase("Enter the verification code in the verification email you just received");?>:</label><div class="input-wrapper"><input class="Degree-input" name="company_verification_code'+user_school_id+'" id="company_verification_code'+user_school_id+'"><label class="input-hint error" id="company_error'+user_school_id+'" ></label></div><a class="Edit-Button01" href="javascript:;" onclick="verify_email('+user_school_id+',\'company\');">Verify</a></div></div>'
						  //$('#comp_email_verified'+user_school_id).append(varifyDiv);
						  var varbut = '<div class="Verification-Button" id="com_verified'+user_school_id+'">'+$('#comp_email_verified'+user_school_id).find('.Verification-Button').html()+'</div>';
						  $('#comp_email_verified'+user_school_id).html(varbut+varifyDiv);
					  
						
						//$('#link_com'+user_school_id).remove();
						//$('#company_verified'+user_school_id).remove();
						//$('#comp_email_verified'+user_school_id).after('<div id="company_verified'+user_school_id+'"><span>'+'<?php echo translate_phrase("A verification email has been sent to ");?><b>'+email+'</b></span><div><label>'+'<?php echo translate_phrase("Enter the verification code found in the verification email you just received");?>'+':</label><span><input name="company_verification_code'+user_school_id+'" style="margin-right:14px;" id="company_verification_code'+user_school_id+'"><button type="button" value="Verify Email" class="button darkblue" onclick="verify_email('+user_school_id+',\'company\');">'+'<?php echo translate_phrase("Verify Email");?>'+'</button><div id="company_error'+user_school_id+'" style="padding-left:7px;color: #FD2080;"></div></span></div></li>');
						 $('#com_verified'+user_school_id).find('a').text("<?php echo translate_phrase('Re-send Verification Code')?>");
						  
					}
					 //$('#sc_verified'+user_school_id).find('a').text("<?php echo translate_phrase('Re-send Verification Email')?>");
				}else{  
					if(mail_for=='school')
						$('#error'+user_school_id).html(data);
					else
						$('#error'+user_school_id).html(data);
				}
			} 
		});
}

function add_company(step){

	if($("#add_companies").css('display') == 'none'&& typeof(step) != 'undefined' && step=='next_step')
	{
		//form is not open
		if(career_validaion())
		{
			$("#signupForm").submit();
		}
	}
	var company_name      = $('#company_name').val();
	var industry_id       = $('#industry_id').val();
	var job_functions     = $('#job_function_id').val();
	var job_title         = $('#job_title').val();
	
	var show_company_name = '';
	if(jQuery('#show_company_name').is(':checked') == true)
	{
		show_company_name = 1;
	}
	else
	{
		show_company_name = 0;
	}
	//console.log(show_company_name);
	var job_city_id       = $('#job_city').val();
	var year_work_start   = $('#years_worked_start').val();
	var year_work_end     = $('#years_worked_end').val();
	var company_email     = $('#company_email_address').val();
	var company_domain    = $('#company_email_domain_id').val();
	var user_company_id   = $('#user_company_id').val();

	if(company_domain!='undefined')
		var domain        = company_domain;
	else 
		var domain        = "";


	if(year_work_start == '' && year_work_end != '')
	{
		$('#year_work_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Please specify both start and end years")?></div>');
		return false;
	}
	else if(year_work_start != '' && year_work_end == '')
	{
		$('#year_work_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Please specify both start and end years")?></div>');
		return false;
	}
	else if(year_work_start > year_work_end)
	{
		$('#year_work_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("End year must be equal to or after start year")?></div>');
		return false;
	}
	else
	{
		$('#year_work_err').html('');
	}
	
	if(job_title !="" && job_city_id !="" && company_name !=""){
		loading();
		 $.ajax({ 
				 url:'<?php echo base_url("user/add_company_applyPage")?>', 
				 type:"post",
				 data:{"company_name":company_name,'industry_id':industry_id,'job_function_id':job_functions,'job_title':job_title,'show_company_name':show_company_name,'job_city_id':job_city_id,'year_work_start':year_work_start,'year_work_end':year_work_end,'company_email':company_email,'company_domain':domain,'user_company_id':user_company_id},
				 cache: false,
				 success: function (data) { 
					 stop_loading();
//                         if(data==0){
//                             $("#school_company").html('<?php echo translate_phrase("Company already added")?>'); 
//                         } 
					 if(data==1)
						 $("#company_email_error").html('<div class="error_indentation error_msg"><?php echo translate_phrase("Please enter a valid company email address")?></div>');
					 else{

					if(user_company_id!="")
						 $("#user_company"+user_company_id).remove();
						 //$("#list_company").append(data);
						 $("#list_company").html(data);
						 $("#school_company").html('');    
						 $('#add_companies').hide();
						 $('#company_button').html('<?php echo translate_phrase("Add Job")?>');
						if(typeof(step) != 'undefined' && step=='next_step')
						{
							if(career_validaion())
							{
								$("#signupForm").submit();
							}
						}
					 }
						 
					 if(data!=1){
						 
						 $("#year_work_err").html('');
						 $("#job_title_err").html('');
						 $("#job_city_id_err").html('');
						 $("#company_name_err").html('');
						 $("#time_period_err").html('');
						 $("#company_name").val('');
						 $("#company_email_address").val('');
						 $("#job_title").val('');
						 $("#job_city").val('');
						 
						 $('#show_company_name').prop('checked',false);
						 $("#add_companies select").prop("selectedIndex","");

						$('#add_companies input[type=text]').val('');
						$('#add_companies input[type=hidden]').val('');
						 //$("#add_companies ul").empty();

						 $("#company_email_error").html('');
						 $('#company_logo').html('');
						 $('#add_companies .file-upload img').attr('src', '');
						 $('#add_companies .file-upload .Delete-Photo01').fadeOut();
						 
						 $('#list_company').fadeIn();
						 $('#list_company_main').fadeIn();
						 $('#add_company_button').fadeIn();
						 scrollToDiv('list_company_main');
						 
						 $('#user_company_id').val('');
					 }
					 return false;
				 }     
		 });
	}
	else{
		 if(company_name=="")
		 {
			$('#company_name_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Company name is required")?></div>');
		 }
		 else
		 {
			 $('#company_name_err').html('');
		 }
			 
		 if(job_title=="")
		 {
			 $('#job_title_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Job title is required")?></div>');
		 }
		 else
		 {
			 $('#job_title_err').html('');
		 }
			 
		 if(job_city_id=="")
		 {
			 $('#job_city_id_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Job location is required")?></div>');
		 }
			 
		 else
		 {
			 $('#job_city_id_err').html('');
		 }
		 
		  return true;
	 }
 }
     
function edit_company(company_id){
	$('#user_company_id').val(company_id);

	scrollToDiv('add_companies');
	$('#add_companies').fadeIn();
	$('#list_company').fadeOut();
	
	$('#com_atsymbol').hide();
	$('#company_cancel_button').show();
	$('#add_company_button').hide();
	$('#add_companies').find('.error_msg').html('');
	$('.mobile-error').remove();
	
	$("#company_domain").html('');
	$('#company_button').html('<?php echo translate_phrase("Update");?>');
	$.ajax({ 
		 url:'<?php echo base_url("user/edit_company")?>', 
		 type:"post",
		 dataType: "json", 
		 data:"user_company_id="+company_id,
		 cache: false,
		 success: function (data) {
			 $('#company_name').val(data.company_name).focus();
			 //show_company_industry(data.company_name,data.industry_id);
			 if(data.company_name == prevInsertCompany)
			 {	
				 var selectInd = $("#company_industry").find('dd li a[key='+data.industry_id+']').text();
				 if(selectInd == '')
				 {
					selectInd = 'Select company industry';
				}
				$("#company_industry").find('dt a span').text(selectInd);
			 }
			 else
			 {
				show_company_industry(data.company_name,data.industry_id);
				 prevInsertCompany = data.company_name;
			 }
			 
			 prevInsertCompany = data.company_name;
			 domainSelectedCompany = data.company_name;

			 if(data.show_company_name == 1){
				 $('#show_company_name').prop('checked',true);
			 }
			 else
			 {
					$('#show_company_name').prop('checked',false);
			 }
			 $('#industry_id').val(data.industry_id);
			 $('#job_function_id').val(data.job_function_id);
			 $('#job_title').val(data.job_title);
			 $('#job_city').val(data.job_city);

			 if(data.years_worked_start == 0)
			 {
				 $("#years_worked_start_dl").find('dt a span').text('---');
				 jQuery('#years_worked_start').val('');

			 }
			 else
			 {
				 $("#years_worked_start_dl").find('dt a span').text(data.years_worked_start);
				 jQuery('#years_worked_start').val(data.years_worked_start);
			 }

			if(data.years_worked_end == 0)
			  {
				 $("#years_worked_end_dl").find('dt a span').text('---');      
				 $("#years_worked_end").val('');      
			  }
			else if(data.years_worked_end == 9999)
			 {
				$("#years_worked_end_dl").find('dt a span').text('Present');      
				$("#years_worked_end").val('9999');      
			 }
			 else
			 {
				 $("#years_worked_end_dl").find('dt a span').text(data.years_worked_end);      
				 $("#years_worked_end").val(data.years_worked_end);      
			 }
		  
			 //$('#years_worked_start').val(data.years_worked_start);
			 //$('#years_worked_end').val(data.years_worked_end);
			 $('#company_email_address').val(data.adress);
			 
			 
			// var selectedIndustry = jQuery('#company_industry dl dd ul li a[key='+data.industry_id+']').text()
			 //jQuery('#company_industry dl dt a span').html(selectedIndustry);

			
			 
			var selectedJobFunction = jQuery('#job_function_dd dl dd ul li a[key='+data.job_function_id+']').text()
			if(selectedJobFunction == '')
			{
				jQuery('#job_function_dd dl dt a span').html('<?php echo translate_phrase("Select job function")?>');
			 }
			else
			{
				 jQuery('#job_function_dd dl dt a span').html(selectedJobFunction);
			}
			 if(data.domain)
			 {
				 Dropdownli = '';
				 var defultSelected = ''
				 $.each(data.domain,function(id,option) {
						Dropdownli +='<li><a key="'+id+'">'+option+'</a></li>';
						defultSelected='<dt><a key="'+id+'" href="javascript:;"><span>'+option+'</span></a><input type="hidden" value="'+id+'" name="company_email_domain_id" id="company_email_domain_id"></dt>';
				});
							
					var DropDownHTML ='<dl class="dropdown-dt" id="email_domain" name="drop_company_email_domain_id">'+defultSelected+'<dd><ul>';
					DropDownHTML +=Dropdownli + '</ul></dd></dl>';
					$('#company_domain').html(DropDownHTML);
						
				 $('#com_atsymbol').show();
				 $('#company_email_address').removeClass("medium");
			 }else{
				 $('#com_atsymbol').hide();
				 $('#company_email_address').addClass("medium");
			 }
			 if(data.photo_business_card){
				 var path  = '<?php echo base_url();?>'+'user_photos/user_'+'<?php echo $this->session->userdata('user_id');?>'+'/'+data.photo_business_card;
				 $('#add_companies .file-upload img').attr('src',path);
				 $('#add_companies .file-upload .Delete-Photo01').show();
			 }
			 $('#user_company_id').val(data.user_company_id);

			 if(data.company_logo)
			  {
				$("#company_logo").html('');
				 var url = '<?php echo base_url();?>company_logos/';
				 if(data.company_logo !="")
					  $("#company_logo").html('<img height="50" width="50" src="'+url+data.company_logo+'" >');
			 }
		 }     
	}); 
 }

function cancel_company(){
	$('.mobile-error').remove();
	$('#company_button').html('<?php echo translate_phrase("Add Job")?>');
	$('#user_company_id').val('');

	$('#add_companies').fadeOut();

	$('#add_company_button').show();         
	$("#year_work_err").html('');
	 $("#job_title_err").html('');
	$("#job_city_id_err").html('');
	$("#company_name_err").html('');
	$("#time_period_err").html('');
	$("#company_name").val('');
	$("#company_email_address").val('');
	$("#job_title").val('');
	$("#job_city").val('');
	$('#add_companies .file-upload img').attr('src', '');
	$('#add_companies .file-upload .Delete-Photo01').hide();
	$('#com_atsymbol').hide();
	$('#company_email_address').addClass('medium');
	$('#show_company_name').prop('checked',false);
	$("#add_companies select").prop("selectedIndex","");
	//$("#add_companies ul").empty();
	$("#add_companies dl dt a span").html('<?php echo translate_phrase("Select company industry");?>');
	$('#company_logo').html('');

	
	if($("#list_company").find('.Univ-logoSec').length > 0)
	{
		$("#list_company").fadeIn();
	}
	else
	{
		$("#list_company").fadeOut();
	}
	$('#list_company_main').fadeIn();
	
	scrollToDiv('list_company');
}
function show_div_company(){
	show_company_industry();
	$('.mobile-error').remove();
	$("#job_function_id").val('');
    $("#job_title_err").html('');
    $("#job_city_id_err").html('');
    $("#company_name_err").html('');
    $("#time_period_err").html('');

    $('#add_companies input[type=text]').val('');
    $('#add_companies input[type=hidden]').val('');
   
    $('#add_companies .file-upload img').attr('src', '');
    $('#add_companies .file-upload .Delete-Photo01').hide();
    $('#com_atsymbol').hide();
    $('#company_email_address').addClass('medium');
    $('#show_company_name').prop('checked',true);
    $("#add_companies select").prop("selectedIndex","");
    jQuery('.drop-start-year dt a span').text('--');
    jQuery('.drop-end-year dt a span').text('--');
    $('#company_domain').html('');
    
    
    //$("#add_companies ul").empty();
    $('#company_logo').html('');
    $('#add_companies').fadeIn();
    $('#list_company').fadeOut();
    $('#add_company_button').hide();
    $('#company_cancel_button').show();
    
    jQuery('#company_industry dl dt a span').html('<?php echo translate_phrase("Select company industry");?>');
    jQuery('#job_function_dd dl dt a span').html('<?php echo translate_phrase("Select job function");?>');
    //scrollToDiv('add_companies');
    $("#company_name").focus();
    //alert(jQuery('#company_industry_dd_id dt a span').html());
}

function auto_complete_city(obj){
	loading();
	var term = $(obj).val();
	$.ajax({ 
		url: '<?php echo base_url(); ?>'+"user/job_location_autocomplete/"+ term, 
		dataType: "json", 
		type:"post",
		cache: false,
		success: function (data) {
			stop_loading();
			var i="0";
			var availableTags=new Array();
			$.each(data,function(id,description) {
				availableTags[i]= description  ;
				i= parseInt(i)+parseInt(1);
			});
			$( "#job_city" ).autocomplete({
				appendTo: "#auto-city-containter",
				minLength: 1,
				source: availableTags
			});
		}     
	});
}

function verify_email(id,value){
	if(value=="school")
		var verification_code  = $('#school_verification_code'+id).val();
	else
		var verification_code  = $('#company_verification_code'+id).val();
	if(verification_code!=""){
		loading();
		$.ajax({ 
			url: '<?php echo base_url(); ?>' +"user/verify_email/", 
			type:"post",
			data:'verification_code='+verification_code+'&value='+value+'&id='+id,
			cache: false,
			success: function (data) {
				stop_loading();
				if(data=='1')
				{
					if(value=="school"){
						$('#sc_verified'+id).hide();
						$('#sc_verified_label'+id).find('span').html('<img class="mar-verify" alt="" src="<?php echo base_url()?>assets/images/verified.png">');
						$('#link'+id).remove();
						$('#verified'+id).remove();
					} else{
						$('#com_verified'+id).hide();
						$('#com_verified_label'+id).find('span').html('<img class="mar-verify" alt="" src="<?php echo base_url()?>assets/images/verified.png">');
						
						$('#company_verified'+id).remove();
					}    
				}else{
					if(value=="school")
						$('#email_error'+id).html('<?php echo translate_phrase('Invalid verification code. Please check your verification email again')?>');
					else
						$('#company_error'+id).html('<?php echo translate_phrase('Invalid verification code. Please check your verification email again')?>');
				}
			}     
		});
	}
	else
	{
		if(value=="school")
			$('#email_error'+id).html('<?php echo translate_phrase('Please enter verification code')?>');
		else
			$('#company_error'+id).html('<?php echo translate_phrase('Please enter verification code')?>');
	}
}
function show_company_logo(company_name){
	if(typeof(company_name) == 'undefined' || company_name == '')
	{
		var company_name      = $('#company_name').val();
	}
	$.ajax({ 
		url: '<?php echo base_url(); ?>' +"user/show_company_logo/", 
		type:"post",
		data:'company_name='+company_name,
		cache: false,
		success: function (data) {
			if (data.length > 0) {
			  var url = '<?php echo base_url();?>company_logos/'+data; 
			  $('#company_logo').html('<img src="'+url+'" height="37" width="50">');
			}else
				 $('#company_logo').html(''); 
		}     
	});
}

function show_company_industry(company_name,industry_id){

	if(typeof(company_name) == 'undefined' || company_name == '')
	{
		var company_name      = $('#company_name').val();
	}

	if(typeof(industry_id) == 'undefined' || industry_id == '')
	{
		var industry_id = 0;
	}
	
	if(company_name != prevInsertCompany)
	{
		$.ajax({ 
			url: '<?php echo base_url(); ?>' +"user/show_company_industry/", 
			type:"post",
			data:'company_name='+company_name,
			cache: false,
			success: function (data) {
				if(data!="0")
				{
					$('#company_industry').html(data);
					var sel_industry = $(data).find('dd li a[key='+industry_id+']').text()
					if(industry_id != 0 && sel_industry != '')
					{
						$('#industry_id').val(industry_id);
						$("#company_industry").find('dt a span').text($(data).find('dd li a[key='+industry_id+']').text())
					}
					prevInsertCompany = company_name;
				}
			}     
		});
	}
}
</script>
