
<script
	src="<?php echo base_url() ?>assets/js/general.js"></script>
<script type="text/javascript">
var staticText = '<?php echo $static_text = translate_phrase(" premium membership for ")?>';
var isDiscountApplied = false;
var isAjaxCall = false;
$(document).ready(function(){

	$("#optionValuePerYear").html($("#optionValuePerYearText").html());
	
	//When click on dropdown then his ul is open..
	$(".dropdown-dt").find('dt a').live('click',function () {
		$(this).parent().parent().find('ul').toggle();
		console.log($(this).parent().parent().find('ul'));
    });

	//When select a option..
    $(".dropdown-dt dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());

		$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'));
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
	});

    $(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("dropdown-dt"))
        	$(".dropdown-dt dd ul").hide();
    });

    $("#applyDiscount").live('click',function(){

    	if(isDiscountApplied == false)
    	{
    		$(this).addClass('appr-cen').removeClass('disable-butn');
    		$("#planInfo input[type='hidden']").val(' ');
    		$("#selectedPlanTxt").text("None");
    		if(isAjaxCall === false)
    		{
    			isAjaxCall = true;
	        	loading();
	    		$.ajax({ 
	                url: '<?php echo base_url(); ?>' +"account/apply_membership_discount", 
	                type:"post",
	                success: function (response) {
	                	isAjaxCall = false;
	                	stop_loading();
	                	isDiscountApplied = true;
	                	$("#optionValuePerYear").html($(response).find("#optionValuePerYearText").html());
	                	$("#choosePlan").html($(response).fadeIn("slow"));
	                }
	        	});
    		}
        }
    });

	$("#choosePlan li a").live('click',function(){
		$("#selectedPlanError").text('');
		
		//selected Class
		$(this).addClass('selected');
		$(this).parent().siblings().find('a').removeClass('selected');

		//Fetch Value from DOM
		var plan_name = $(this).find('.plan_name').text();
		var plan_amount = $(this).find('.plan_amount').text();
    	var currency = $(this).find('.currency').text();
    	var currency_id = $(this).attr('currency_id');

    	
    	//Set Values in Cart
    	$("#no_of_unit").val($(this).find('.plan_name').attr('lang'));
    	$("#plan_name").val(plan_name + ' premium membership');
    	$("#plan_amount").val(plan_amount);
    	$("#currency").val(currency);
    	$("#currency_id").val(currency_id);
		
    	var selectedPlanTxt = plan_name  +staticText + currency + plan_amount;
    	$("#selectedPlanTxt").text(selectedPlanTxt);
    });
    
    $('.checkboxDiv ul li a').live('click',function(e) {
		if(isAjaxCall === false)
		{
			var hiddenEle = $("#membership_options_id");
			var oldVal       = hiddenEle.val();
			var curVal = $(this).attr('key');
			var li = $(this).parent();


	        if ($(li).find('a').hasClass('appr-cen')) 
	        {
	        	var valArr = oldVal.split(',');
	        	valArr.splice(valArr.indexOf(curVal), 1);
	          	var newVal      = valArr.join();
	        } 
	        else 
			{
	            if(oldVal!="")
					var newVal =  oldVal+','+curVal; 
				else
	                var newVal = curVal;
	         }
	        $(hiddenEle).val(newVal);
	        
			isAjaxCall = true;    
			console.log('ajax_CALL::'+isAjaxCall);
			loading();
			$.ajax({ 
	            url: '<?php echo base_url(); ?>' +"account/apply_membership_option", 
	            type:"post",
	            data:{membership_options_id:newVal},
	            success: function (response) {
	            	stop_loading();
	            	isAjaxCall = false;
	            	console.log('ajax_CALL::'+isAjaxCall);
	            	if ($(li).find('a').hasClass('appr-cen')) 
	                {
	                	//$("#option_"+curVal).slideUp();
	                	
	                  	$(li).find('a').removeClass('appr-cen').addClass('disable-butn');
	                } 
	                else 
	        		{
	                	//$("#option_"+curVal).slideDown();
	                    $(li).find('a').addClass('appr-cen').removeClass('disable-butn');
	                }
	                var temp = $("#option_"+curVal).find('.upgrade_option span').text();
	                $("#option_"+curVal).find('.upgrade_option span').text($("#option_"+curVal).find('.upgrade_option').attr('lang'));
	                $("#option_"+curVal).find('.upgrade_option').attr('lang',temp);
	                
	            	$("#optionValuePerYear").html($(response).find("#optionValuePerYearText").html());
	            	$("#planInfo input[type='hidden']").val(' ');
	        		$("#selectedPlanTxt").text("None");
	            	$("#choosePlan").html($(response).fadeIn("slow"));
	            }
	    	});
		} 	
   });    
});

function add_upgrade(member_option_id)
{
	$('.checkboxDiv ul li a[key="'+member_option_id+'"]').click();
	/*
	if($('.checkboxDiv ul li a[key="'+member_option_id+'"]').hasClass('disable-butn'))
	{
		$('.checkboxDiv ul li a[key="'+member_option_id+'"]').click();
	} 
	*/
}
function choose_gatway(obj)
{
	if($(obj).attr('id') == 'paypal')
	{
		$("#card_details").addClass('hidden');		
	}
	else
	{
		$("#card_details").removeClass('hidden');
		alert('Please select Paypal. currently We will accept card payment only though the Paypal ');
	}
}
function make_payment()
{
	var plan_name = $("#plan_name").val();
	var plan_amount = $("#plan_amount").val();
	var currency = $("#currency").val();
	var option_ids = $.trim($("#membership_options_id").val())
	
	if(option_ids == '')
	{
		$("#membership_optionsError").text('<?php echo translate_phrase('Please select at least one membership option!')?>');
		$('body').scrollTo($("#membershipt_opt_div"),800);
	}
	else if(plan_name == '' || plan_amount =='' || currency == '')
	{
		$("#selectedPlanError").text('<?php echo translate_phrase('Please First choose a plan!')?>');
		$('body').scrollTo($("#payment_form"),800);
	}
	else
	{
		$("#payment_form").submit();
	}	
}
</script>
<?php
//$selected_key = $this->session->userdata('default_selected_key');
$selected_key = 3;
$eligible_for_discount = false;
if(isset($user_data['user_age']) && $user_data['user_age'])
{
	//$birthDate = explode("-", $user_data['birth_date']);
	//$user_age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));

	//$y= date('Y',strtotime($user_data['birth_date']));
	//$user_age = date('Y')-$y;

	$user_age = $user_data['user_age'];

	if($user_age <= 23)
	{
		$eligible_for_discount = TRUE;
	}
}
?>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="Edit-pge-Main">
				<div class="Edit-p-top1">
					<h1>
					<?php echo $page_title;?>
					</h1>
				</div>
			</div>

			<div class="emp-B-tabing-prt">
				<div class="error-msg left">
				<?php echo $this->session->flashdata('paypal');?>
				</div>
				<div class="step-form-Main Mar-top-none Top-radius-none">
					<div class="step-form-Part">
						<div class="upgrade-top-txt">
						<?php echo translate_phrase('Upgrade your account to enjoy')?>
							<span><?php echo translate_phrase('access to premium filters, more introductions, better response rates, and other exclusive benefits, resulting in endless romantic possibilities.')?></span>
						</div>
						<!-- Photo Section Added -->
						<?php if($user_photos):?>
						<div class="profile-phot-M">
							<div class="userPhoto">
								<ul>
								<?php foreach ($user_photos as $user):?>
									<li>
										<div class="userPhoto-box">
											<img src="<?php echo $user['photo_url']?>"
												title="<?php echo $user['first_name']?>"
												alt="<?php echo $user['first_name']?>" />
										</div>
									</li>
									<?php endforeach;?>
								</ul>
							</div>
						</div>
						<?php endif;?>
					</div>

					<div class="getBox">
						<div
							class="edu-main <?php if($user_photos):?>top-divider<?php endif;?>"
							id="membershipt_opt_div">
							<div class="selectDateHed">
								<span class="fl"><?php echo translate_phrase('Benefits of Upgrading')?></span>
								<!--<span class="fl">1.<?php echo translate_phrase('Select Upgrade Options')?></span>-->
								<!--<span id="optionValuePerYear"> Loading...</span>-->
							</div>
							<!--<div class="f-decrMAIN checkboxDiv">
							<?php
							$membership_csv = array();
							if($membership_options):?>
								<ul class="f-decr">
								<?php foreach ($membership_options as $option):?>
								<?php $membership_csv[] = $option['membership_option_id'];?>
									<li><a href="javascript:;"
										key="<?php echo $option['membership_option_id']?>"
										class="appr-cen"><?php echo $option['description']?> </a>
									</li>
									<?php endforeach;?>
								</ul>
								<?php endif;?>
								<label id="membership_optionsError" class="input-hint error"></label>
							</div>-->
						</div>
					</div>

					<div class="getBox">
					<?php if($membership_options):?>
					<?php $this->general->set_table('user_membership_option');?>
					<?php foreach ($membership_options as $option):?>
					<?php $user_membership_data = $this->general->get("expiry_date",array('user_id'=>$user_data['user_id'],'membership_option_id'=>$option['membership_option_id']));?>
					<?php
					switch ($option['membership_option_id']) {
case 1:
							?>
						<div class="edu-main" id="option_1">
							<div class="sub-hed">
								<span class="fl"><?php echo $option['description']?> &nbsp; <br/>
								<img alt="" name="" src="<?php echo base_url()?>assets/images/upgrade/icon-1.png" /></span>
								<!--<span class="Red-color fl"> <?php if (isset($user_membership_data['0']['expiry_date'])){
									echo (date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE)?translate_phrase('(already expired on '):translate_phrase('(expires on ');
									echo date(DATE_FORMATE,strtotime($user_membership_data['0']['expiry_date'])).')';
								}
								else
								{
									echo translate_phrase('(not yet purchased)');
								}
								?> &nbsp;</span>
								<?php if (isset($user_membership_data['0']['expiry_date'])):
									if(date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE):?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Add Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php else:?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Extend Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php endif;?>
								<?php else: ?>
								<a class="edu-main upgrade_option"
									lang="<?php echo translate_phrase("Add Upgrade")?>"
									onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
									href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
								</span> </a>
								<?php endif;?>
							</div>

							<div class="accountTxt">-->
								<div class="accountTxtRight">
									<ul>
										<li><?php echo translate_phrase('Take 30% off the purchase of any date ticket packages')?></span>
										</li>
										<li><span><?php echo translate_phrase('Each date ticket entitles you to a real-life date')?>
										</span> <?php echo translate_phrase('that our staff will fully arrange for you')?>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<?php
						break;

case 4:
	?>
						<div class="edu-main" id="option_4">
							<div class="sub-hed">
								<span class="fl"><?php echo $option['description']?> &nbsp; <br/>
								<img alt="" name="" src="<?php echo base_url()?>assets/images/upgrade/icon-2.png" /></span>

								<!--<span class="Red-color fl"> <?php if (isset($user_membership_data['0']['expiry_date'])){
									echo (date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE)?translate_phrase('(already expired on '):translate_phrase('(expires on ');
									echo date(DATE_FORMATE,strtotime($user_membership_data['0']['expiry_date'])).')';
								}
								else
								{
									echo translate_phrase('(not yet purchased)');
								}
								?> &nbsp;</span>
								<?php if (isset($user_membership_data['0']['expiry_date'])):
									if(date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE):?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Add Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php else:?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Extend Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php endif;?>
								<?php else: ?>
								<a class="edu-main upgrade_option"
									lang="<?php echo translate_phrase("Add Upgrade")?>"
									onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
									href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
								</span> </a>
								<?php endif;?>
							</div>
							<div class="accountTxt">-->
								<div class="accountTxtRight">
									<ul>
										<li><?php echo translate_phrase('Re-activates your expired introductions')?></span></li>
										<li><?php echo translate_phrase('Get a second chance at love with any previous matches that you may have missed')?></li>
									</ul>
								</div>
							</div>
						</div>
						<?php
						break;
case 2:
	?>
						<div class="edu-main" id="option_2">
							<div class="sub-hed">
								<span class="fl"><?php echo $option['description']?> &nbsp; <br/>
								<img alt="" name="" src="<?php echo base_url()?>assets/images/upgrade/icon-6.png" /></span>

								<!--<span class="Red-color fl"> <?php if (isset($user_membership_data['0']['expiry_date'])){
									echo (date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE)?translate_phrase('(already expired on '):translate_phrase('(expires on ');
									echo date(DATE_FORMATE,strtotime($user_membership_data['0']['expiry_date'])).')';
								}
								else
								{
									echo translate_phrase('(not yet purchased)');
								}
								?> &nbsp;</span>
								<?php if (isset($user_membership_data['0']['expiry_date'])):
									if(date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE):?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Add Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php else:?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Extend Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php endif;?>
								<?php else: ?>
								<a class="edu-main upgrade_option"
									lang="<?php echo translate_phrase("Add Upgrade")?>"
									onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
									href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
								</span> </a>
								<?php endif;?>
							</div>
							<div class="accountTxt">-->
								<div class="accountTxtRight">
									<ul>
										<li><?php echo translate_phrase('Grants access to exclusive premium filters not available to other members')?></span>
										</li>
										<li><span><?php echo translate_phrase('Get introduced to only the smartest and most successful singles')?>
										</span> <?php echo translate_phrase('based on premium filters such as school/company name and/or income level')?>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<?php
						break;


case 6:
	?>
						<div class="edu-main" id="option_6">
							<div class="sub-hed">
								<span class="fl"><?php echo $option['description']?> &nbsp; <br/>
								<img alt="" name="" src="<?php echo base_url()?>assets/images/upgrade/icon-5.png" /></span>

								<!--<span class="Red-color fl"> <?php if (isset($user_membership_data['0']['expiry_date'])){
									echo (date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE)?translate_phrase('(already expired on '):translate_phrase('(expires on ');
									echo date(DATE_FORMATE,strtotime($user_membership_data['0']['expiry_date'])).')';
								}
								else
								{
									echo translate_phrase('(not yet purchased)');
								}
								?> &nbsp;</span>
								<?php if (isset($user_membership_data['0']['expiry_date'])):
									if(date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE):?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Add Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php else:?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Extend Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php endif;?>
								<?php else: ?>
								<a class="edu-main upgrade_option"
									lang="<?php echo translate_phrase("Add Upgrade")?>"
									onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
									href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
								</span> </a>
								<?php endif;?>
							</div>
							<div class="accountTxt">-->
								<div class="accountTxtRight">
									<ul>
										<li><?php echo translate_phrase('Increases maximum limit of upcoming introductions you can have from 5 to 20')?>
										</li>
										<li><?php echo translate_phrase('More introductions')?> <span><?php echo translate_phrase('significantly boosts your chances to find your one true love')?>
										</span></li>
									</ul>
								</div>
							</div>
						</div>
						<?php
						break;

case 3:
	?>
						<div class="edu-main" id="option_3">
							<div class="sub-hed">
								<span class="fl"><?php echo $option['description']?> &nbsp; <br/>
								<img alt="" name="" src="<?php echo base_url()?>assets/images/upgrade/icon-2.png" /></span>

								<!--<span class="Red-color fl"> <?php if (isset($user_membership_data['0']['expiry_date'])){
									echo (date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE)?translate_phrase('(already expired on '):translate_phrase('(expires on ');
									echo date(DATE_FORMATE,strtotime($user_membership_data['0']['expiry_date'])).')';
								}
								else
								{
									echo translate_phrase('(not yet purchased)');
								}
								?> &nbsp;</span>
								<?php if (isset($user_membership_data['0']['expiry_date'])):
									if(date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE):?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Add Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php else:?>
									<a class="edu-main upgrade_option"
										lang="<?php echo translate_phrase("Extend Upgrade")?>"
										onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
										href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
									</span> </a>
									<?php endif;?>
								<?php else: ?>
								<a class="edu-main upgrade_option"
									lang="<?php echo translate_phrase("Add Upgrade")?>"
									onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
									href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
								</span> </a>
								<?php endif;?>
							</div>
							<div class="accountTxt">-->
								<div class="accountTxtRight">
									<ul>
										<li><?php echo translate_phrase('Makes all your upcoming intros instantly available for dating')?>
										</li>
										<li><span><?php echo translate_phrase('Instantly date')?> </span>
										<?php echo translate_phrase('your upcoming intros to eliminate the 7 days waiting time')?>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<?php
						break;

case 5:
	?>
<!--						<div class="edu-main" id="option_5">
							<div class="sub-hed">
								<span class="fl"><?php echo $option['description']?> &nbsp; </span>

								<span class="Red-color fl"> <?php if (isset($user_membership_data['0']['expiry_date'])){
									echo (date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE)?translate_phrase('(already expired on '):translate_phrase('(expires on ');
									echo date(DATE_FORMATE,strtotime($user_membership_data['0']['expiry_date'])).')';
								}
								else
								{
									echo translate_phrase('(not yet purchased)');
								}
								?> &nbsp;</span>
								<?php if (isset($user_membership_data['0']['expiry_date'])):
								if(date("Y-m-d",strtotime($user_membership_data['0']['expiry_date'])) < SQL_DATE):?>
								<a class="edu-main upgrade_option"
									lang="<?php echo translate_phrase("Add Upgrade")?>"
									onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
									href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
								</span> </a>
								<?php else:?>
								<a class="edu-main upgrade_option"
									lang="<?php echo translate_phrase("Extend Upgrade")?>"
									onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
									href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
								</span> </a>
								<?php endif;?>
								<?php else: ?>
								<a class="edu-main upgrade_option"
									lang="<?php echo translate_phrase("Add Upgrade")?>"
									onclick="add_upgrade('<?php echo $option['membership_option_id']?>')"
									href="javascript:;"><span class="appr-cen Upgrd-blue"><?php echo translate_phrase("Exclude Upgrade")?>
								</span> </a>
								<?php endif;?>
							</div>
							<div class="accountTxt">
								<div class="accountTxtRight">
									<ul>
										<li><?php echo translate_phrase('Allows you to view any feedback left by your past dates')?>
										</li>
										<li><span><?php echo translate_phrase('Gain invaluable insights')?>
										</span> <?php echo translate_phrase('into what people you had previously dated thought about you')?>
										</li>
									</ul>
								</div>
							</div>
						</div>-->
						<?php
						break;

default:
	;
	break;
					}?>
					<?php endforeach;;?>
					<?php endif;?>
					</div>

					<div class="getBox">
						<div class="edu-main">
							<div class="selectAreaTop">
								<div class="selectDateHed"><?php echo translate_phrase('Choose Your Upgrade Duration')?></div>
							</div>
							<?php if($eligible_for_discount):?>
							<div class="discountTxt">
							<?php echo translate_phrase('You are eligible for an additional 30% student discount')?>
								!
							</div>
							<div class="Ed-rM-but">
								<a href="javascript:;" id="applyDiscount" class="disable-butn"><?php echo translate_phrase('Apply Discount')?>
								</a>
							</div>
							<?php endif;?>
							<div class="selectArea">
								<ul id="choosePlan">
								<?php if($ticket_packages):?>
								<?php foreach ($ticket_packages as $key=>$package):?>
									<li><a href="javascript:;"
										currency_id="<?php echo $package['currency_id']?>"
										class="<?php echo $key==$selected_key?'selected':'';?>">
											<div class="selectDate">
												<div class="select-Button01">
												<?php echo translate_phrase('Select')?>
												</div>
											</div>

											<div class="package">
												<div class="package-innr-row">
													<div class="package-innr-row-left"
														<?php if($package['name'] == 12) echo 'id="optionValuePerYearText"'?>>
														<span class="plan_name"
															lang="<?php echo $package['name']?>"><?php echo $package['description']?>
														</span> - <span class="green-color">
														<?php if($this->language_id != 2 && $this->language_id != 3):?>
															<?php echo $package['currency'].number_format($package['per_month_price'])?>
														<?php else: ?>
															<?php echo number_format($package['per_month_price']).$package['currency']?>
														<?php endif;?>													
														</span>/
														<?php echo translate_phrase('month')?>
													</div>
													<?php if($package['save_per']):?>
													<div class="selectDateText width-saveTxt font-italic">
													<?php echo $package['save_per'].'%'.translate_phrase(' off');?>
														<sup>1</sup>
													</div>
													<?php endif;?>

													<?php if($package['extra']):?>
													<div class="guaranteeText width-garanteeTxt font-italic">
													<?php echo $package['extra']?>
													</div>
													<?php endif;?>
												</div>
												<div class="guarantee">
													<p>
													<?php echo translate_phrase('One easy ')?>
														<span class="currency"><?php echo $package['currency'];?></span><span class="plan_amount"><?php echo number_format($package['total']);?></span>
														<?php echo translate_phrase('payment ')?>
														<?php if($package['save_amount']):?>
															<?php if($this->language_id != 2 && $this->language_id != 3):?>
																<?php echo '('.translate_phrase('Save ').$package['currency'].number_format($package['save_amount']).')';?>
															<?php else: ?>
																<?php echo '('.translate_phrase('SAVEMONEY').number_format($package['save_amount']).$package['currency'].')';?>
															<?php endif;?>
														<?php endif;?>
													</p>
												</div>
											</div> </a>
									</li>
									<?php endforeach;?>
									<?php endif;?>
								</ul>
								<div class="priceTxt">
								<?php echo translate_phrase("A SMALL PRICE TO PAY TO FIND THE LOVE OF YOUR LIFE")?>
								</div>
							</div>
							<div class="paymentArea">
								<h2>
								<?php echo translate_phrase('Choose Your Payment Method')?>
								</h2>
								<form id="payment_form"
									action="<?php echo base_url().url_city_name().'/upgrade-account.html'; ?>"
									method="post">
									<div class="selectedTxt" id="planInfo">
									<?php echo translate_phrase('Selected')?>
										: <span id="selectedPlanTxt"> <?php 
										if(isset($ticket_packages[$selected_key]) && $ticket_packages[$selected_key])
										{
											echo $ticket_packages[$selected_key]['description'].$static_text.$ticket_packages[$selected_key]['currency'].number_format($ticket_packages[$selected_key]['total']);
										}
										else
										{
											echo translate_phrase('None');
										}
										?> </span> <input type="hidden" name="plan_name"
											id="plan_name"
											value="<?php echo isset($ticket_packages[$selected_key]['description'])&&$ticket_packages[$selected_key]['description']?$ticket_packages[$selected_key]['description'].$static_text:'';?>" />
										<input type="hidden" name="no_of_unit" id="no_of_unit"
											value="<?php echo isset($ticket_packages[$selected_key]['name'])&&$ticket_packages[$selected_key]['name']?$ticket_packages[$selected_key]['name']:'';?>" />
										<input type="hidden" name="amount" id="plan_amount"
											value="<?php echo isset($ticket_packages[$selected_key]['total'])&&$ticket_packages[$selected_key]['total']?$ticket_packages[$selected_key]['total']:'';?>" />
										<input type="hidden" name="currency" id="currency"
											value="<?php echo isset($ticket_packages[$selected_key]['currency'])&&$ticket_packages[$selected_key]['currency']?$ticket_packages[$selected_key]['currency']:'';?>" />
										<input type="hidden" name="currency_id" id="currency_id"
											value="<?php echo isset($ticket_packages[$selected_key]['currency_id'])&&$ticket_packages[$selected_key]['currency_id']?$ticket_packages[$selected_key]['currency_id']:'';?>" />
										<label id="selectedPlanError" class="input-hint error"></label>
									</div>
									<input type="hidden" name="membership_options_id"
										id="membership_options_id"
										value="<?php echo implode(',',$membership_csv)?>">

									<!--<div class="cardRow">
										<div class="radioArea">
											<input name="gatwaytype" type="radio" value=""
												onclick="choose_gatway(this)" /> <img alt="" name=""
												src="<?php echo base_url()?>assets/images/visa-card.jpg" />
										</div>

										<div class="radioArea">
											<input name="gatwaytype" type="radio" value=""
												onclick="choose_gatway(this)" /> <img alt="" name=""
												src="<?php echo base_url()?>assets/images/master-card.jpg" />
										</div>

										<div class="radioArea">
											<input id="paypal" onclick="choose_gatway(this)"
												name="gatwaytype" type="radio" value="" checked="checked"> <img
												alt="" name=""
												src="<?php echo base_url()?>assets/images/paypal.jpg" />
										</div>
									</div>

									<div id="card_details" class="hidden">
										<div class="cardInputRow">
											<div class="sfp-1-Left">
											<?php echo translate_phrase('Name on card')?>
												:<span>*</span>
											</div>
											<div class="sfp-1-Right">
												<input class="Degree-input" type="text" value=""
													name="card_name">
											</div>
										</div>

										<div class="cardInputRow">
											<div class="sfp-1-Left">
											<?php echo translate_phrase('Card number')?>
												:<span>*</span>
											</div>
											<div class="sfp-1-Right">
												<input class="Degree-input" type="text" value=""
													name="card_no"> <label class="input-hint error"></label>
											</div>
										</div>
										<div class="cardInputRow">
											<div class="sfp-1-Left">
											<?php echo translate_phrase('Expiry date')?>
												:<span>*</span>
											</div>
											<div class="sfp-1-Right">
											<?php
											$syear = '';
											$smonth = '';
											echo form_dt_dropdown('card_expiry_month', $month, $smonth, 'id="month" class="dropdown-dt" ', translate_phrase('Month'), "hiddenfield");
											echo form_dt_dropdown('card_expiry_year', $year, $syear, 'id="year" class="dropdown-dt dd-menu-mar"', translate_phrase('Year'), "hiddenfield");
											?>
												<label class="input-hint error"></label>
											</div>
										</div>

										<div class="cardInputRow">
											<div class="sfp-1-Left">
											<?php echo translate_phrase('Security code')?>
												:
											</div>
											<div class="sfp-1-Right">
												<input class="securityInput-new" type="text" value=""
													name="card_security_code">
												<div class="securityTxt">
													<a href="javascript:;"><img
														src="<?php echo base_url()?>assets/images/ccv.jpg" alt="" />
													</a>
												</div>
												<label class="input-hint error"></label>
											</div>
										</div>
									</div>-->
									<p></p>
									<p></p>
									<div class="cardInputRow">
										<div class="order-btn">
											<a onclick="make_payment(this)" href="javascript:;"><?php echo translate_phrase('Subscribe now')?>&nbsp;
											<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" />
											</a>
										</div>
										<div class="verisignLogo">
											<img src="<?php echo base_url()?>assets/images/verisign.jpg" />
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<div class="edu-main">
						<p>
							<sup>1</sup>
							<?php echo translate_phrase('Discount rates calculated based on price of 1 month membership plan')?>
						</p>
						<p>
						<?php echo translate_phrase('In order to ensure uninterrupted service, your '.get_assets('name','DateTix').' subscription will be automatically extended before expiration, for successive renewal periods of the same duration as the subscription term originally selected at the then-current non-promotional subscription rate.')?>
						</p>
						<p>
						<?php echo translate_phrase('To cancel your subscription at any time, simply go to your Account Settings page or send us an email at ');?>
							<a href="mailto:payment@datetix.com">payment@datetix.com</a>
							<?php echo translate_phrase('. If you cancel your subscription, you will still enjoy premium membership benefits until the end of your then-current subscription term, and your subscription will not be renewed after that term expires. However, you will not be eligible for a prorated refund of any portion of the subscription fees paid for the then-current subscription period.')?>
						</p>
						<p>
						<?php echo translate_phrase('All sensitive consumer data collected on this site is used solely for the purposes of completing this transaction. Information is transmitted to our payment partners using industry standard 256 bit SSL encryption.')?>
						</p>
						<p>
						<?php echo translate_phrase('Please contact us at ')?>
							<a href="mailto:payment@datetix.com">payment@datetix.com</a>
							<?php echo translate_phrase('if you have any further questions')?>
						</p></div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
