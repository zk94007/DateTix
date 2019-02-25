<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<script type="text/javascript">
var staticText = '<?php echo $static_text = translate_phrase(" for ")?>';
var isDiscountApplied = false;
var user_id = '<?php echo $this -> user_id;?>';
$(document).ready(function(){
	//$('#events_tabs').easytabs();
	
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
    
    <?php if(isset($wrong_form_data)):?>
    goToScroll('payment_form');
    <?php endif;?>
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
function facebook_like_callback(is_like)
{
	loading();
	$.ajax({ 
		url: '<?php echo base_url(); ?>' +"events/apply_fb_discount/"+is_like, 
		type:"post",
		success: function (response) {
			stop_loading();
			$("#choosePlan").html($(response).fadeIn("slow"));
		}
	});
}
function user_coming(event_id,obj)
{
	if($(obj).find('span').hasClass('pink-choosed'))
	{
		if(user_id != '')
		{
			$('body').scrollTo($('.order-btn'),800);
		}
		return false;
	}
	
	if(user_id != '')
	{
		$("#selectedPlanError").text('');
		loading();
		$.ajax({
			url:base_url+'events/user_coming/'+event_id,
			type:'post',
			data:$("#payment_form").serialize(),
			dataType:'json',
			success:function(data){
				stop_loading();
				if(data.type == "1")
				{
					$('.user_coming').addClass('pink-choosed').text('<?php echo translate_phrase("You have RSVPed");?>');
					$(".order-btn a span.rsvp_and").remove();
					$(".order-btn a span.order_now").text(capitaliseFirstLetter($.trim($(".order-btn a span.order_now").text())));					
				}
				else
				{
					$("#selectedPlanError").text(data.msg);
				}
			}
		});
		$('body').scrollTo($('.order-btn'),800);
	}
	else
	{
		$("#user_email").focus();
		goToScroll('payment_form');
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
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">				
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('paypal');?> <?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="events_tabs">
					<ul class='etabs'>
						<?php if($total_events_in_city > 10000 ):?>
						<!--<li class='tab tab-nav'><span class="active"></span><a class="active" href="<?php echo base_url().url_city_name().'/event.html'; ?>"><?php echo translate_phrase('Next Event');?></a></li>-->
						<li class='tab tab-nav'><span></span><a href="<?php echo base_url().url_city_name().'/events.html'; ?>#upcoming_events"><?php echo translate_phrase('Upcoming Events');?></a></li>
						<li class='tab tab-nav' id="past_tab_li"><span></span><a href="<?php echo base_url().url_city_name().'/events.html'; ?>#past_events"><?php echo translate_phrase('Past Events');?></a></li>
						<?php endif;?>
					</ul>
				
					<div class="step-form-Main Mar-top-none Top-radius-none" id="details">
						<div class="error-msg left"></div>
						<?php if($event_info):?>
					<div class="step-form-Part">
			        	<div class="userBox-wrap">
			                <div class="userTop">
								<span class="fl">
								<div class="selectDateHed martop-edit"><?php echo translate_phrase("Buy Tickets for ").$event_info['event_name'].translate_phrase(" - ").date(DATE_FORMATE,strtotime($event_info['event_start_time']));?></div>
								<h2><?php echo date('g:ia',strtotime($event_info['event_start_time'])).translate_phrase(" to ").date('g:ia',strtotime($event_info['event_end_time']));?></h2>
								</span>
								<!--<span id="optionValuePerYear" class="blu-color bold"><a href="<?php echo base_url().url_city_name() . '/events.html'?>"><?php echo translate_phrase("View Other Events");?></a></span>-->
			                </div>

					<?php if(!$fb_like_discount):?>
					<div class="style01 comn-top-mar"><span class="Red-color"><b><?php echo translate_phrase("Take 5% off instantly to all events by liking our Facebook page now")?>!</b></span></div>
					<div class="appear-prt-but">
						<div class="fb-like" data-href="https://www.facebook.com/<?php echo $this->config->item('page_id');?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
					</div>
					<?php endif;?>

					<?php if(!$signup_discount):?>
					<div class="style01 comn-top-mar"><span class="Red-color"><b><?php echo translate_phrase("Take 10% off instantly to all events by ")?><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("applying for a free DateTix account")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing into your DateTix account")?>!</a></b></span></div>
					<?php endif;?>																														

			                <div class="appear-prt-but">
						<a href="javascript:;" onclick="user_coming(<?php echo $event_info['event_id'];?>,this)" ><span class="appr-cen user_coming <?php echo $user_event?'pink-choosed':'';?>"><?php echo $user_event?translate_phrase("You have RSVPed"):translate_phrase("Yes, I'm coming!");?></span></a>
						<?php if($this->user_id):?>
							<a href="<?php echo base_url() . url_city_name().'/invite-friends.html?event_id='.$this->utility->encode($event_info['event_id']);?>"><span class="appr-cen"><?php echo translate_phrase('Invite Friends');?></span></a>
						<?php else:?>
							<a href="<?php echo 'mailto:?subject=Hey, want to go to this event on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'?&amp;body=Hey, want to go to the '.$event_info['event_name'].' on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'? It seems like a great way to meet new friends and potential dates! You can find out more details and RSVP at '.base_url().url_city_name().'/event.html?id='.$event_info['event_id'].'&amp;src=5';?>"><span class="appr-cen"><?php echo translate_phrase('Invite Friends');?></span></a>
						<?php endif;?>
					</div>
				      	</div>
				        
						<div class="userBox">
							<div class="userBox-wrap"> 
								<div class="dateRow"><?php echo $event_info['name'];?></div>
								<div class="locationArea Mar-top-none">
									<p>
										<?php 
											if($event_info['address'])
												echo $event_info['address'];
											if($event_info['neighborhood_name'])
												echo ', '.$event_info['neighborhood_name'];?>
										&nbsp; &nbsp;<a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=event.html'.$form_para.'&venue='.$this->utility->encode($event_info['venue_id']);?>"><?php echo translate_phrase('View Map')?></a></p>
									<p><?php 
											if($event_info['city_name'])
												echo $event_info['city_name'];?></p>
									<p><?php echo $event_info['phone_number'];?></p>
									
									<?php if($event_info['review_url'] != ''):?>
									<p><a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo $event_info['review_url'];?>"><?php echo $event_info['review_url'];?></a></p>
									<?php endif;?>
									
								</div>
							</div>
						</div>
						
						<!-- Event Photo Section -->
						<div class="last-bor"></div>
						<div class="getBox" align="left">
							<img style="max-height:98%; max-width:100%;" src="<?php echo $event_info['poster_url'];?>" />
						</div>
						<!-- END Event Photo Section -->
						
						
						<div class="getBox">
							<?php echo $event_info['description'];?>							
						</div>
						
						<div class="getBox">							
							<div class="dateRow">How DateTix Events Help You Meet New Friends/Dates</div>
							<p><br/></p>
							<ul class="cms-list userTop p-style">
								<li>1. <a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("Apply for a free DateTix account")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("sign in to your DateTix account")?>!</a></li>
								<li>2. <a href="javascript:;" onclick="user_coming(<?php echo $event_info['event_id'];?>,this)" ><?php echo translate_phrase("RSVP and order tickets online for a DateTix event")?></a></li>
								<li>3. <b><?php echo translate_phrase("Receive personalized People to Meet ticket")?></b> <?php echo translate_phrase("chosen entirely based on your unique profile")?></li>
								<li>4. <?php echo translate_phrase("Meet the people on your People to Meet ticket, or anyone else you find interesting at the event!")?></li>
							</ul>						
						</div>
																		
						<!--<div class="getBox">
							<div class="selectDateHed appear-prt"><?php echo translate_phrase("Price (includes ").$event_info['price_includes'].")";?>:</div>
							<ul class="cms-list userTop p-style">
								<li><?php echo isset($ticket_packages['0'])?$ticket_packages['0']['currency'].number_format($ticket_packages['0']['per_date_price']).translate_phrase(" (prepay online)"):'';?></li>
								<li><?php echo isset($ticket_packages['0'])?$ticket_packages['0']['currency'].' '.$event_info['price_door'].translate_phrase(" (pay at door)"):'';?></li>
							</ul>
						</div>-->
						
						<div class="getBox">
							<?php if($total_rsvped >= 10) :?>
								<div class="selectDateHed"><span class="DarkGreen-color"><?php echo $total_rsvped.translate_phrase(" attendees have already RSVPed"); ?></span>, <?php echo translate_phrase('including people from the following schools and companies');?>:</div>
								
								<!-- School Logo -->
								<?php if($event_user_schools):?>
								<div class="affiliations-logo">
									<?php foreach($event_user_schools as $value):?>
									<div class="aff-logo"><img style="max-width: 100px; max-height: 100px;" src="<?php echo base_url().'school_logos/'.$value['logo_url']?>" title="<?php echo $value['school_name'];?>" alt="<?php echo $value['school_name'];?>"></div>
									<?php endforeach;?>
								</div>
			                    <?php endif;?>
			                    
			                    <!-- Company Logo -->
								<?php if($event_user_companies):?>
			                    <div class="affiliations-logo">
									<?php foreach($event_user_companies as $value):?>
										<div class="aff-logo"><img src="<?php echo base_url().'company_logos/'.$value['logo_url']?>" title="<?php echo $value['company_name'];?>" alt="<?php echo $value['company_name'];?>" style="max-width: 100px; max-height: 100px;"></div>
									<?php endforeach;?>
			                    </div>
			                    <?php endif;?>
							<?php endif;?>
			              	
			              			<div class="appear-prt-but">
								<a href="javascript:;" onclick="user_coming(<?php echo $event_info['event_id'];?>,this)" ><span class="appr-cen user_coming <?php echo $user_event?'pink-choosed':'';?>"><?php echo $user_event?translate_phrase("You have RSVPed"):translate_phrase("Yes, I'm coming!");?></span></a>
								<?php if($this->user_id):?>
									<a href="<?php echo base_url() . url_city_name().'/invite-friends.html?event_id='.$this->utility->encode($event_info['event_id']);?>"><span class="appr-cen"><?php echo translate_phrase('Invite Friends');?></span></a>
								<?php else:?>
									<a href="<?php echo 'mailto:?subject=Hey, want to go to this event on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'?&amp;body=Hey, want to go to the '.$event_info['event_name'].' on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'? It seems like a great way to meet new friends and potential dates! You can find out more details and RSVP at '.base_url().url_city_name().'/event.html?id='.$event_info['event_id'].'&amp;src=5';?>"><span class="appr-cen"><?php echo translate_phrase('Invite Friends');?></span></a>
								<?php endif;?>
							</div>
						</div>
						
						<div class="getBox">
								<div class="selectDateHed comn-top-mar">
									<?php echo translate_phrase("Select Number of Tickets to Purchase");?> &nbsp;
									<span class="italic Red-color" >(<?php echo translate_phrase("only ").$total_left_ticket.translate_phrase(" tickets left");?>)</span>
								</div>																																																																																																															
																																																																																																																																	
								<div class="selectArea">
									<div class="selectAreaTop p-style"><?php echo '('.translate_phrase("each ticket includes ").$event_info['price_includes'].")";?></div>

									<?php if(!$fb_like_discount):?>
									<div class="style01 comn-top-mar"><span class="Red-color"><b><?php echo translate_phrase("Take 5% off instantly to all events by liking our Facebook page now")?>!</b></span></div>
									<div class="appear-prt-but">
										<div class="fb-like" data-href="https://www.facebook.com/<?php echo $this->config->item('page_id');?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
									</div>
									<?php else:?>
									<div class="style01 comn-top-mar"><span class="DarkGreen-color"><b><?php echo translate_phrase("A 5% discount has been applied to your ticket price because you liked our Facebook page")?>!</b></span></div>
									<?php endif;?>

									<?php if(!$signup_discount):?>
									<div class="style01 comn-top-mar"><span class="Red-color"><b><?php echo translate_phrase("Take 10% off instantly to all events by ")?><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("applying for a free DateTix account")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing into your DateTix account")?>!</a></b></span></div>
									<?php else:?>
										<div class="style01 comn-top-mar"><span class="DarkGreen-color"><b><?php echo translate_phrase("A 10% member discount has been applied to your ticket price because you are a DateTix member")?>!</b></span></div>
									<?php endif;?>																														

									<ul id="choosePlan">
									<li>&nbsp;</li>
									<li>
										<div class="selectDate">&nbsp;
										</div>
										<div class="package">
											<div class="package-innr-row">
												<?php if(isset($ticket_packages[0]['currency'])):?>
												<div class="package-innr-row-left">
													<span class="plan_name" lang="1">&nbsp;&nbsp;<?php echo translate_phrase('Pay at door');?>
													</span> - <span class="green-color"><?php echo $ticket_packages[0]['currency'].number_format($event_info['price_door']);?>
													</span> / <?php echo translate_phrase('ticket')?>
												</div>
												<?php endif;?>
											</div>
										</div>
									</li>		
									<li>&nbsp;</li>
																			
									<?php if($ticket_packages):?>
									<?php foreach ($ticket_packages as $key=>$package):?>
										<li><a href="javascript:;"
											currency_id="<?php echo $package['currency_id']?>"
											class="<?php echo (isset($selected_key) && $package['event_price_id']==$selected_key)?'selected':'';?>">
												<div class="selectDate">
													<div class="select-Button01">
													<?php echo translate_phrase('Select')?>
													</div>
												</div>

												<div class="package">
													<div class="package-innr-row">
														<div class="package-innr-row-left">
															<span class="plan_name"
																lang="<?php echo $package['name']?>"><?php echo $package['description'];?>
															</span> - <span class="green-color"><?php echo $package['currency'].number_format($package['per_date_price']);?>
															</span> / <?php echo translate_phrase('ticket')?>
														</div>
														<?php if($package['save_per']):?>
														<div class="selectDateText width-saveTxt font-italic">
														<?php echo translate_phrase('Save ').$package['save_per'].'%';?>
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
															<span class="currency"><?php echo $package['currency'];?>
															</span><span class="plan_amount"><?php echo number_format($package['total']);?>
															</span>
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
									<!--<div class="selectDateHed martop-edit"><?php echo translate_phrase('Choose Your Payment Method')?></div>-->
									<form id="payment_form"
										action="<?php echo base_url().url_city_name().'/event.html'.$form_para; ?>"
										method="post">
										<div class="selectedTxt">
										<?php echo translate_phrase('Selected')?>
											: <span id="selectedPlanTxt"> <?php 
											if(isset($selected_key))
											{
												echo $selected_package['description'].$static_text.$selected_package['currency'].number_format($selected_package['total']);
											}
											else
											{
												echo translate_phrase('None');
											}
											?> </span> <input type="hidden" name="plan_name"
												id="plan_name"
												value="<?php echo isset($selected_package['description'])&& $selected_package['description']?$selected_package['description']:'';?>" />
											<input type="hidden" name="no_of_unit" id="no_of_unit"
												value="<?php echo isset($selected_package['name'])&& $selected_package['name']?$selected_package['name']:'';?>" />
											<input type="hidden" name="amount" id="plan_amount"
												value="<?php echo isset($selected_package['total'])&& $selected_package['total']?$selected_package['total']:'';?>" />
											<input type="hidden" name="currency" id="currency"
												value="<?php echo isset($selected_package['currency'])&& $selected_package['currency']?$selected_package['currency']:'';?>" />
											<input type="hidden" name="currency_id" id="currency_id"
												value="<?php echo isset($selected_package['currency_id'])&& $selected_package['currency_id']?$selected_package['currency_id']:'';?>" />
											<label id="selectedPlanError" class="input-hint error"></label>
										</div>

										<div class="cardRow hidden">
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
											<div class="sfp-1-Left"> <?php echo translate_phrase('First Name')?>: <span>*</span></div>
											<div class="sfp-1-Right">
												<input class="Degree-input" value="<?php echo set_value('first_name'); ?>" name="first_name" type="text" required>
												<label class="input-hint error" for="first_name" error_txt="<?php echo translate_phrase('First Name is required.')?>"><?php echo form_error('first_name'); ?></label>
											</div>
										</div>
										
										<div class="cardInputRow user_info">
											<div class="sfp-1-Left"> <?php echo translate_phrase('Last Name')?>: <span>*</span></div>
											<div class="sfp-1-Right">
												<input class="Degree-input" value="<?php echo set_value('last_name'); ?>" name="last_name" type="text" required>
												<label class="input-hint error" for="last_name" error_txt="<?php echo translate_phrase('Last Name is required.')?>"><?php echo form_error('last_name'); ?><?php echo form_error('last_name'); ?></label>
											</div>
										</div>
										<?php endif;?>
											
										<div class="det-center-inner3">
											<div class="order-btn">
												<a onclick="make_payment()" href="javascript:;">
												<?php if(!$this->user_id || $user_event):?>
												<?php echo translate_phrase('Order now&nbsp;&nbsp;')?>
												<?php else:?>
												<?php echo '<span class="rsvp_and">'.translate_phrase("RSVP and ").'</span><span class="order_now">'.translate_phrase('order now&nbsp;&nbsp;').'</span>';?>
												<?php endif;?>
												<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
											</div>
											<div class="verisignLogo">
												<img src="<?php echo base_url()?>assets/images/verisign.jpg" />
											</div>
										</div>
										
									</form>
								</div>
						</div>
						
						<div class="getBox bordernone">
							<div class="edu-main">
								<p><?php echo translate_phrase("All sensitive consumer data collected on this site is used solely for the purposes of completing this transaction. Information is transmitted to our payment partners using industry standard 256 bit SSL encryption")?>.</p>
								<p><?php echo translate_phrase("Please contact us at ")?><a href="mailto:payment@datetix.com">payment@datetix.com</a><?php echo translate_phrase("if you have any further questions")?>.</p>
							</div>
						</div>
					</div>						
					<?php else:?>
					<div class="Edit-p-top1"><?php echo translate_phrase("There are currently no upcoming events scheduled in "). get_current_city() ;?>.</div>
					<?php endif;?>
					</div>
				</div>
			</div>			
		</div>
	</div>
</div>
