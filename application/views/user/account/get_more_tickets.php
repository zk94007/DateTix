<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<script type="text/javascript">
var staticText = '<?php echo $static_text = translate_phrase(" date tickets for ")?>';
var isDiscountApplied = false;
var user_id = '<?php echo $this -> user_id;?>';


$(document).ready(function(){

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
    		$("#payment_form input[type='hidden']").val(' ');
    		$("#selectedPlanTxt").text("None");
    		
        	loading();
    		$.ajax({ 
                url: '<?php echo base_url(); ?>' +"account/apply_discount", 
                type:"post",
                success: function (response) {
                	stop_loading();
                	isDiscountApplied = true;
                	$("#choosePlan").html($(response).fadeIn("slow"));
                }
        	});
        }
    });

	$("#choosePlan li a").live('click',function(){

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
    	$("#plan_name").val(plan_name);
    	$("#plan_amount").val(plan_amount);
    	$("#currency").val(currency);
    	$("#currency_id").val(currency_id);
		
    	var selectedPlanTxt = plan_name  + staticText + currency + plan_amount;
    	$("#selectedPlanTxt").text(selectedPlanTxt);
    });
    
});
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
    
        var is_form_filled = true;
	var plan_name = $("#plan_name").val();
	var plan_amount = $("#plan_amount").val();
	var currency = $("#currency").val();
        var first_focus_item = '';
        
	if(plan_name == '' || plan_amount =='' || currency == '')
	{
		$("#selectedPlanError").text('<?php echo translate_phrase('Please First choose a plan!')?>');
                is_form_filled = false;
		$('body').scrollTo($("#payment_form"),800);
	}
	else
	{
                if(user_id == '')
		{
			$.each($("#payment_form").find(":input[type='text']"),function(i,item){
				var attr = $(item).attr('required');
				if (typeof attr !== 'undefined' && attr !== false && attr == 'required') {
					if($(item).val() == '')
					{
						if(first_focus_item =='')
							first_focus_item = item;
						
						is_form_filled = false;
						$(item).siblings('label').text($(item).siblings('label').attr('error_txt'));
					}
					else
						$(item).siblings('label').text('');
				}
			});
		}
		//$("#payment_form").submit();
	}
        if(is_form_filled)
	{
		$("#selectedPlanError").text('');
		$("#payment_form").submit();
	}
	else
	{
		if(first_focus_item !='')
		{
			$(first_focus_item).focus();
		}
		goToScroll('payment_form');
	}
}

</script>
<?php
$selected_key = 3;

/* Discount based on School
 $eligible_for_discount = false;
 if($user_data['career_stage_id'] == 1 || $user_data['career_stage_id'] == 2)
 {
 $eligible_for_discount = TRUE;
 }
 */

$eligible_for_discount = false;
if(isset($user_data['user_age']) && $user_data['user_age'])
{
	$user_age = $user_data['user_age'];

	if($user_age <= 23 || $this->datetix->is_premium_user($user_data['user_id'],PERMISSION_UNLIMITED_DATES))
	{
		$eligible_for_discount = TRUE;
	}
}

?>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			
			<div class="emp-B-tabing-prt">
				<div class="error-msg left">
				<?php echo $this->session->flashdata('paypal');?>
				</div>

				<div class="step-form-Main Mar-top-none Top-radius-none">
					<div class="step-form-Part">
						<!-- Photo Section Added -->
						<?php if($user_photos):?>
						<div class="profile-phot-M" style="padding:0px">
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
						
					        <div class="getBox">
					            	<div class="edu-main">
					                	<div class="selectDateHed"><?php echo translate_phrase('Get Free Date Tickets')?></div>
					                	<div class="div-row mar-top2">
					                		<h2><span class="green-color">+500</span> <?php echo translate_phrase('free date tickets for liking our Facebook page')?></h2>
					                    	<h2><span class="green-color">+100</span> <?php echo translate_phrase('free date tickets per friend invited who signs up')?></h2>
					                    </div>
					                    <div class="Ed-rM-but">
					                    	<div class="suggest-btn"><a href="<?php echo base_url() . url_city_name() ?>/invite-friends.html"><?php echo translate_phrase('Invite Your Single Friends')?></a></div>
					                  	</div>
					             	</div>
					        </div>
		                  		
						<div class="getBox">
							<div>
								<div class="selectDateHed">
									<?php echo translate_phrase('Buy Date Tickets')?>
								</div>
								<?php if($eligible_for_discount):?>
								<div class="discountTxt">
									<?php if($user_age <= 23 ):?>																
									<?php echo translate_phrase('You are eligible for a 20% student discount')?>!
									<?php else:?>
									<?php echo translate_phrase('You are eligible for a 20% discount because you are a DateTix Premium member')?>!
									<?php endif;?>
								</div>
								<div class="Ed-rM-but">
									<a href="javascript:;" id="applyDiscount" class="disable-butn"><?php echo translate_phrase('Apply Discount')?>
									</a>
								</div>
								<?php endif;?>
								<div class="selectArea">
									<ul id="choosePlan">
									<?php if($ticket_packages):?>
									<?php foreach ($ticket_packages as $key=>$package): ?>
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
														<div class="package-innr-row-left">
															<span class="plan_name" lang="<?php echo $package['name']?>"><span class="green-color">+<?php echo $package['description'];?></span> <?php echo translate_phrase('date tickets')?></span> - <span class="green-color"><?php echo $package['currency'].number_format($package['total'])?></span>
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
														<?php echo translate_phrase('One easy ')?><span class="currency"><?php echo $package['currency'];?></span><span class="plan_amount"><?php echo number_format($package['total']);?></span>
															<?php echo translate_phrase('payment ')?>
															<?php if($package['save_amount']) echo '('.translate_phrase('Save ').$package['currency'].number_format($package['save_amount']).')';?>
														</p>
													</div>
												</div> </a>
										</li>
										<?php endforeach;?>
										<?php endif;?>
									</ul>
								</div>

								<div class="paymentArea">
									<h2>
									<?php echo translate_phrase('Choose Your Payment Method')?>
									</h2>
									<form id="payment_form"
										action="<?php echo base_url().url_city_name().'/get-more-tickets.html'; ?>"
										method="post">
										<div class="selectedTxt">
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
												value="<?php echo isset($ticket_packages[$selected_key]['description'])&&$ticket_packages[$selected_key]['description']?$ticket_packages[$selected_key]['description']:'';?>" />
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

										<?php if(!$this->user_id):?>
											<div class="cardInputRow user_info comn-top-mar">
												<div class="sfp-1-Left"><?php echo translate_phrase('Email')?>: <span>*</span></div>
												<div class="sfp-1-Right">
													<input class="Degree-input" value="<?php echo set_value('user_email'); ?>" id="user_email" name="user_email" type="text" required>
													<label class="input-hint error" for="user_email" error_txt="<?php echo translate_phrase('Email is required.')?>" ><?php echo form_error('user_email'); ?></label>
												</div>
											</div>
											
											<div class="cardInputRow user_info">
												<div class="sfp-1-Left"> <?php echo translate_phrase('Name')?>: <span>*</span></div>
												<div class="sfp-1-Right">
													<input class="Degree-input" value="<?php echo set_value('first_name'); ?>" name="first_name" type="text" required>
													<label class="input-hint error" for="first_name" error_txt="<?php echo translate_phrase('Name is required.')?>"><?php echo form_error('first_name'); ?></label>
												</div>
											</div>
											
											<div class="cardInputRow user_info">
												<div class="sfp-1-Left"> <?php echo translate_phrase('Mobile No.')?>: <span>*</span></div>
												<div class="sfp-1-Right">
													<input class="Degree-input" value="<?php echo set_value('last_name'); ?>" name="last_name" type="text" required>
													<label class="input-hint error" for="last_name" error_txt="<?php echo translate_phrase('Mobile Number is required.')?>"><?php echo form_error('last_name'); ?><?php echo form_error('last_name'); ?></label>
												</div>
											</div>
										<?php endif;?>

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
													name="gatwaytype" type="radio" value="" /checked="checked">
												<img alt="" name=""
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
												<a onclick="make_payment()" href="javascript:;"><?php echo translate_phrase('Order now')?>&nbsp;
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

						<!--<div class="getBox">
							<div class="edu-main">
								<div class="selectDateHed">
									<?php echo translate_phrase('Upgrade Your Account to Enjoy 30% Discount on All Date Ticket Purcahses')?>
								</div>
								<div class="Ed-rM-but">
									<div class="order-btn">
										<a href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase('Upgrade Account')?>&nbsp;
											<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" />
										</a>
									</div>
								</div>
							</div>
						</div>-->
						<div class="getBox bordernone">
							<div class="edu-main">
								<p>
									<sup>1</sup>
									<?php echo translate_phrase('Discount rates calculated based on price of 1 date ticket package')?>
								</p>
								<p>
								<?php echo translate_phrase('All sensitive consumer data collected on this site is used solely for the purposes of completing this transaction. Information is transmitted to our payment partners using industry standard 256 bit SSL encryption')?>
									.
								</p>
								<p>
								<?php echo translate_phrase('Please contact us at ')?>
									<a href="mailto:payment@datetix.com">payment@datetix.com</a>
									<?php echo translate_phrase('if you have any further questions')?>
									.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
