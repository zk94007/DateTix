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
    
    <?php if(!$fb_like_discount):?>
    /*
     window.fbAsyncInit = function() {
	  FB.init({appId      : '<?php echo $this->config->item('appId');?>', // App ID
		status     : true, // check login status
		cookie     : true, // enable cookies to allow the server to access the session
		auth      : true,  // parse XFBML
		xfbml: true
	  });
	  
	  FB.login(function(response) {		 
		  if (response.authResponse) {
			  var user_id = response.authResponse.userID;
			  var page_id = "<?php echo $this->config->item('page_id');?>"; //datetix
			  var fql_query = "SELECT uid FROM page_fan WHERE page_id = "+page_id+"and uid="+user_id;
			  
			  var the_query = FB.Data.query(fql_query);
			  the_query.wait(function(rows) {

				  if (rows.length == 1 && rows[0].uid == user_id) {
					 facebook_like_callback(1);
				  } else {
					  facebook_like_callback(0);
				  }
			  });
		  }
		});		
	};
     */
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
		alert('Please select PayPal. currently we accept payment only though the PayPal ');
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
			
			var langText = $(".fb_plugin_text").text();
			$(".fb_plugin_text").text($(".fb_plugin_text").attr('lang'));
			$(".fb_plugin_text").attr('lang',langText)
			if(is_like == 1)
			{
				$(".fb_plugin_text").removeClass('Red-color').addClass('DarkGreen-color');
			}
			else
			{
				$(".fb_plugin_text").addClass('Red-color').removeClass('DarkGreen-color');
			}
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
					$('.user_coming').addClass('pink-choosed').text('<?php echo translate_phrase("You have RSVPed. Buy Tickets Now");?>');
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
	<?php if($user_order):?>
		$("#pageMsg").text('<?php echo translate_phrase('You have already purchased a discounted member ticket for this event. If you want to purchase tickets for your friends, please ask them to sign up as ').get_assets('name','DateTix').translate_phrase(' members to purchase their own discounted member tickets online.')?>');
		goToScroll('pageMsg');
	<?php else:?>
	var is_form_filled = true;
	var plan_name = $("#plan_name").val();
	var plan_amount = $("#plan_amount").val();
	var currency = $("#currency").val();
	var first_focus_item = '';
	if(plan_name == '' || plan_amount =='' || currency == '')
	{
		$("#selectedPlanError").text('<?php echo translate_phrase('Please select a plan first')?>');
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
	<?php endif;?>
}

</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">				
				<div class="page-msg-box Red-color left" id="pageMsg"><?php echo $this->session->flashdata('paypal');?> <?php echo $this->session->flashdata('error_msg');?></div>
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
							<?php 
							
							if($event_info['max_prepaid_tickets'] > 0):?>
							<div class="selectDateHed martop-edit"><?php echo translate_phrase("Buy Tickets for ").$event_info['event_name'];?></div>
							<?php else:?>
							<div class="selectDateHed martop-edit"><?php echo translate_phrase("RSVP for ").$event_info['event_name'];?></div>
							<?php endif;?>														
							<div class="dateRow"><br/><?php echo date(DATE_FORMATE,strtotime($event_info['event_start_time']));?></div>
							<div class="dateRow"><?php echo date('g:ia',strtotime($event_info['event_start_time'])).translate_phrase(" to ").date('g:ia',strtotime($event_info['event_end_time']));?></div>
							</span>
							<!--<span id="optionValuePerYear" class="blu-color bold"><a href="<?php echo base_url().url_city_name() . '/events.html'?>"><?php echo translate_phrase("View Other Events");?></a></span>-->
								<div class="dateRow"><?php echo $event_info['name'];?></div>
								<div class="locationArea Mar-top-none">
									<p>
										<?php 
											if($event_info['address'])
												echo $event_info['address'];
											if($event_info['neighborhood_name'])
												echo ', '.$event_info['neighborhood_name'];?>
										&nbsp;
										<!--<a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=event.html'.$form_para.'&venue='.$this->utility->encode($event_info['venue_id']);?>"><?php echo translate_phrase('View Map')?></a>-->
										<?php if($event_info['event_id'] == 11):?>
											<a href="javascript:;" onclick="openNewWindow(this)" data-url="http://map.baidu.com/?newmap=1&shareurl=1&l=19&tn=B_NORMAL_MAP&c=12965010,4826543&cc=bj&i=0,0&s=s%26da_src%3Dpcmappg.searchBox.button%26wd%3Dcentro%E7%82%AB%E9%85%B7%E9%85%92%E5%90%A7%20%E5%8C%97%E4%BA%AC%E5%B8%82%E6%9C%9D%E9%98%B3%E5%8C%BA%E5%85%89%E5%8D%8E%E8%B7%AF1%E5%8F%B7%E5%98%89%E9%87%8C%E4%B8%AD%E5%BF%83%E9%A5%AD%E5%BA%97%E4%B8%80%E5%B1%82%26c%3D131%26src%3D0%26wd2%3D%26sug%3D0%26l%3D15%26from%3Dwebmap"><?php echo translate_phrase('View Map')?></a>
										<?php else:?>
											<a href="javascript:;" onclick="openNewWindow(this)" data-url="https://www.google.com.hk/maps/place/17+Lan+Kwai+Fong,+Central/@22.2799102,114.1556177,18z/data=!4m2!3m1!1s0x3404007b2911c957:0x68b299e4d878d30?hl=en"><?php echo translate_phrase('View Map')?></a>
										<?php endif;?>														
									</p>
									<p><?php 
											if($event_info['city_name'])
												echo $event_info['city_name'];?></p>
									<p><?php echo $event_info['phone_number'];?></p>
									
									<?php if($event_info['website_url'] != ''):?>
									<p><a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo $event_info['website_url'];?>"><?php echo $event_info['website_url'];?></a></p>
									<?php endif;?>
									<br/>									
								</div>
				                </div>

						<?php if($event_info['max_prepaid_tickets'] > 0):?>
							<?php if(!$fb_like_discount):?>
							<div class="style01 comn-top-mar">
							<b><span class="Red-color fb_plugin_text" lang="<?php echo translate_phrase("HKD30 discount has been applied to your ticket price because you liked our Facebook page")?>!"><a href="http://www.facebook.com/datetix" target="_NEW"><?php echo translate_phrase("Take HKD30 off instantly")?></a><?php echo translate_phrase(" to all events by liking our Facebook page now")?>!</span></b>
							</div>
							<div class="appear-prt-but">
								<div class="fb-like" data-href="https://www.facebook.com/<?php echo $this->config->item('page_id');?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
							</div>
							<?php else:?>
							<div class="style01 comn-top-mar">
								<b><span class="DarkGreen-color fb_plugin_text" lang="<?php echo translate_phrase("Take HKD30 off instantly to all events by liking our Facebook page now")?>!"><?php echo translate_phrase("HKD30 discount has been applied to your ticket price because you liked our Facebook page")?>!</span></b>
							</div>
							<?php endif;?>
						<?php else:?>
							<?php if(!$fb_like_discount):?>
							<div class="style01 comn-top-mar">
							<b><span class="Red-color fb_plugin_text" lang="<?php echo translate_phrase("Email info@datetix.com with your name and email address to receive your free 3 month premium membership")?>!"><a href="http://www.facebook.com/datetix" target="_NEW"><?php echo translate_phrase("Get a free 3 month premium membership to DateTix (valued at HKD888) by liking our Facebook page now")?></a>!</span></b>
							</div>
							<div class="appear-prt-but">
								<div class="fb-like" data-href="https://www.facebook.com/<?php echo $this->config->item('page_id');?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
							</div>
							<?php else:?>
							<div class="style01 comn-top-mar">
								<b><span class="DarkGreen-color fb_plugin_text" lang="<?php echo translate_phrase("Get a free 3 month premium membership to ").get_assets('name','DateTix').translate_phrase(" (valued at HKD888) by liking our Facebook page now")?>!"><?php echo translate_phrase("Email info@datetix.com with your name and email address to receive your free 3 month premium membership")?>!</span></b>
							</div>
							<?php endif;?>
						<?php endif;?>															      	
	
						<?php if($event_info['max_prepaid_tickets'] > SMALL_EVENT_USER_LIMIT):?>
							<?php if(!$signup_discount):?>
								<?php if($event_info['event_id'] != 11):?>
									<?php if($ad_id != 12):?>
									<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("Take ") . $ticket_packages[0]['currency'].number_format($ticket_packages[0]['save_amount']) . translate_phrase(" off instantly ")?></a><?php echo translate_phrase(" to this event by ")?><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>
									<?php else:?>
									<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="http://www.datetix.hk/join"><?php echo translate_phrase("Take ") . $ticket_packages[0]['currency'].number_format($ticket_packages[0]['save_amount']) . translate_phrase(" off instantly (offer available until May 1)")?></a><?php echo translate_phrase(" to this event by ")?><a href="http://www.datetix.hk/join"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>
									<?php endif;?>																														
								<?php else:?>
									<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="<?php echo base_url().url_city_name();?>/apply.html?highlight=1"><?php echo translate_phrase("Save 15% off")?></a><?php echo translate_phrase(" this event by ")?><a href="<?php echo base_url().url_city_name();?>/apply.html?highlight=1"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>
								<?php endif;?>																														
							<?php endif;?>																														
						<?php endif;?>																														

						<div class="det-center-inner3">
							
							<?php if($event_info['event_start_time'] < SQL_DATE):?>
								<div class="suggest-btn"> <a href="<?php echo base_url().url_city_name().'/event_photos.html?id='.$event_info['event_id'];?>"> <?php echo translate_phrase("Event Photos");?> </a></div>		
							<?php else:?>
								<?php if(($this->user_id || $event_info['max_prepaid_tickets'] > SMALL_EVENT_USER_LIMIT) && ($event_info['max_prepaid_tickets'] > 0)):?>
									<?php if(!$this->user_id && 1==2):?>
										<div class="order-btn">
										<a href="<?php echo base_url().url_city_name();?>/signin.html">
										<?php echo translate_phrase('Get discounted tickets')?>&nbsp;
										<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
										</div>
										
										<div class="suggest-btn"> 
										<a onclick="make_payment()" href="javascript:;">
										<?php if(!$this->user_id || $user_event):?>
										<?php echo translate_phrase('Get full price tickets&nbsp;')?>
										<?php else:?>
										<?php echo translate_phrase('Get full price tickets&nbsp;')?>
										<?php //echo '<span class="rsvp_and">'.translate_phrase("RSVP and ").'</span><span class="order_now">'.translate_phrase('get tickets&nbsp;').'</span>';?>
										<?php endif;?>
										</a>
										</div>
									<?php else:?>
										<?php if(!$this->user_id):?>																
											<div class="order-btn">
											<a onclick="make_payment()" href="javascript:;">
											<?php echo translate_phrase('Get tickets')?>&nbsp;
											<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
											</div>
										<?php else:?>
											<div class="order-btn">
											<a onclick="make_payment()" href="javascript:;">
											<?php echo translate_phrase('Get discounted tickets')?>&nbsp;
											<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
											</div>
										<?php endif;?>
									<?php endif;?>
								<?php elseif ($event_info['max_prepaid_tickets'] > 0):?>
									<div class="order-btn">
									<a href="<?php echo base_url().url_city_name();?>/signin.html">
									<?php echo translate_phrase('Sign in to get tickets')?>&nbsp;
									<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
									</div>
								<?php elseif ($event_info['max_prepaid_tickets'] == 0):
									 $url_para=($form_para)?$form_para.'&freeRSVP':'?freeRSVP';
									?>
										<div class="order-btn">
											<a href="<?php echo base_url().url_city_name().'/event.html'.$url_para ?>"> 
												<?php echo translate_phrase('RSVP Now')?> &nbsp; 
												<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" />
											</a>
										</div>
								<?php endif;?>
							<?php endif;?>							
						</div>
					</div>

						<!-- Event Photo Section -->
						<div class="last-bor"></div>
						<div class="getBox" align="left">
							<?php if(get_assets('website_id','0') == 3):?>
							<img style="max-height:98%; max-width:100%;" src="http://www.datetix.com/assets/images/events/12/12smartime.jpg" />
							<?php else:?>
							<img style="max-height:98%; max-width:100%;" src="<?php echo $event_info['poster_url'];?>" />
							<?php endif;?>
						</div>
						<!-- END Event Photo Section -->
												
						<?php if($event_info['description']!=""):?>
							<div class="getBox">
								<?php echo $event_info['description'];?>							
							</div>
						<?php endif;?>
						
						<!--<div class="getBox">							
							<div class="dateRow"><?php echo translate_phrase("How DateTix Events Help You Meet New Friends and/or Potential Dates")?></div>
							<p><br/></p>
							<ul class="cms-list userTop p-style">
								<?php if($ad_id != 12):?>
								<li>1. <a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("Apply for a free ").get_assets('name','DateTix').translate_phrase(" account")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("sign in to your ").get_assets('name','DateTix').translate_phrase(" account")?></a></li>
								<?php else:?>
								<li>1. <a href="http://www.datetix.hk/join"><?php echo translate_phrase("Apply for a free ").get_assets('name','DateTix').translate_phrase(" account")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("sign in to your ").get_assets('name','DateTix').translate_phrase(" account")?></a></li>
								<?php endif;?>								
								<li>2. <?php echo translate_phrase("RSVP for a DateTix event on this page")?></li>
								<li>3. <?php echo translate_phrase("We will email you a ")?> <b><?php echo translate_phrase("personalized intro list of people to meet")?></b><?php echo translate_phrase(", carefully curated and chosen based on your unique profile")?></li>
								<li>4. <?php echo translate_phrase("Attend the event to meet the people you have been introduced to, or anyone else you find interesting!")?></li>
							</ul>						
						</div>-->
																		
						<!--<div class="getBox">
							<div class="selectDateHed appear-prt"><?php echo translate_phrase("Price (includes ").$event_info['price_includes'].")";?>:</div>
							<ul class="cms-list userTop p-style">
								<li><?php echo isset($ticket_packages['0'])?$ticket_packages['0']['currency'].number_format($ticket_packages['0']['per_date_price']).translate_phrase(" (prepay online)"):'';?></li>
								<li><?php echo isset($ticket_packages['0'])?$ticket_packages['0']['currency'].' '.$event_info['price_door'].translate_phrase(" (pay at door)"):'';?></li>
							</ul>
						</div>-->
						
						<div class="getBox">
							<?php if($total_rsvped >= 20) :?>
								<div class="selectDateHed"><span class="DarkGreen-color"><?php echo $total_rsvped * 2 .translate_phrase(" attendees have already RSVPed"); ?></span>, <?php echo translate_phrase('including people from the following companies and schools');?>:</div>
								
      					                    	<!-- Company Logo -->
								<?php if($event_user_companies):?>
			        			            <div class="affiliations-logo">
									<?php foreach($event_user_companies as $value):?>
										<div class="aff-logo-event-details"><img src="<?php echo base_url().'company_logos/'.$value['logo_url']?>" title="<?php echo $value['company_name'];?>" alt="<?php echo $value['company_name'];?>" style="max-width: 100px; max-height: 100px;"></div>
									<?php endforeach;?>
			                    				</div>
			                    			<?php endif;?>

									    
								<!-- School Names -->
								<?php if($event_user_schools):?>
								<ul class="cms-list userTop p-style">
									<?php foreach($event_user_schools as $value):?>
									<li><?php echo $value['school_name'];?></li>
									<?php endforeach;?>
								</ul>						
						                <?php endif;?>						                		                    			                    								    								    
							<?php endif;?>
			              	
							<?php if($event_info['event_start_time'] >= SQL_DATE):?>
				              			<div class="appear-prt-but">
									<!--<a href="javascript:;" onclick="user_coming(<?php echo $event_info['event_id'];?>,this)" ><span class="appr-cen user_coming <?php echo $user_event?'pink-choosed':'';?>"><?php echo $user_event?translate_phrase("You have RSVPed. Buy Tickets Now"):translate_phrase("Yes, I'm coming!");?></span></a>-->
									<?php if($this->user_id && $ad_id!=12):?>
										<!--<a href="<?php echo base_url() . url_city_name().'/invite-friends.html?event_id='.$this->utility->encode($event_info['event_id']);?>"><span class="appr-cen"><?php echo translate_phrase('Invite Friends');?></span></a>-->
										<a href="<?php echo 'mailto:?subject=Hey, want to go to this event on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'?&amp;body=Hey, want to go to the '.$event_info['event_name'].' on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'? It seems like a great way to meet new friends and potential dates! You can find out more details and RSVP at http://www.datetix.hk/mnc';?>"><span class="appr-cen"><?php echo translate_phrase('Invite Friends');?></span></a>
									<?php else:?>
										<?php if($ad_id==12):?>
											<a href="<?php echo 'mailto:?subject=Hey, want to go to this event on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'?&amp;body=Hey, want to go to the '.$event_info['event_name'].' on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'? It seems like a great way to meet new friends and potential dates! You can find out more details and RSVP at http://www.datetix.hk/mnc';?>"><span class="appr-cen"><?php echo translate_phrase('Invite Friends');?></span></a>
										<?php else:?>
											<a href="<?php echo 'mailto:?subject=Hey, want to go to this event on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'?&amp;body=Hey, want to go to the '.$event_info['event_name'].' on '.date(DATE_FORMATE,strtotime($event_info['event_start_time'])).'? It seems like a great way to meet new friends and potential dates! You can find out more details and RSVP at '.base_url().url_city_name().'/event.html?id='.$event_info['event_id'].'&src=12';?>"><span class="appr-cen"><?php echo translate_phrase('Invite Friends');?></span></a>
										<?php endif;?>
									<?php endif;?>
								</div>
				        		<?php endif;?>
						</div>
																			
						<?php if($event_info['event_start_time'] >= SQL_DATE && $event_info['max_prepaid_tickets'] > 0):?>
							<div class="getBox">
								<div class="selectDateHed comn-top-mar">
									<?php if(count($ticket_packages) > 1):?>
										<?php echo translate_phrase("Select Number of Tickets to Purchase");?>
									<?php endif;?>

									<?php if($total_left_ticket < 50):?>
										<span class="italic Red-color" >(<?php echo translate_phrase("only ").$total_left_ticket.translate_phrase(" tickets left");?>)</span>
									<?php endif;?>
								</div>																																																																																																								
																																																																																																																																	
								<div class="selectArea">																
									<ul id="choosePlan">
									<!--<li>
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
									<li>&nbsp;</li>-->
									<li><div class="package"><div class="package-innr-row"><div class="package-innr-row-left"><span class="DarkGreen-color">
										<b><?php echo translate_phrase('Early Bird Discount').'<br/>('.translate_phrase("includes ").$event_info['price_includes'].")";?></b>
										</span></div></div></div>
									</li>
						
							<?php if($event_info['max_prepaid_tickets'] > SMALL_EVENT_USER_LIMIT):?>
								<?php if(!$signup_discount):?>
									<?php if($event_info['event_id'] != 11):?>
										<?php if($ad_id != 12):?>
										<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("Take ") . $ticket_packages[0]['currency'].number_format($ticket_packages[0]['save_amount']) . translate_phrase(" off instantly ")?></a><?php echo translate_phrase(" to this event by ")?><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>
										<?php else:?>
										<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="http://www.datetix.hk/join"><?php echo translate_phrase("Take ") . $ticket_packages[0]['currency'].number_format($ticket_packages[0]['save_amount']) . translate_phrase(" off instantly (offer available until May 1)")?></a><?php echo translate_phrase(" to this event by ")?><a href="http://www.datetix.hk/join"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>
										<?php endif;?>																														
									<?php else:?>
										<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="<?php echo base_url().url_city_name();?>/apply.html?highlight=1"><?php echo translate_phrase("Save 15% off")?></a><?php echo translate_phrase(" this event by ")?><a href="<?php echo base_url().url_city_name();?>/apply.html?highlight=1"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>
									<?php endif;?>																														
								<?php endif;?>																														
							<?php endif;?>
							
									<li>&nbsp;</li>

									<?php if($ticket_packages && $event_info['max_prepaid_tickets'] > 0):?>
									<?php foreach ($ticket_packages as $key=>$package):?>
										<li>
										<?php if(count($ticket_packages) > 1):?>																				
											<a href="javascript:;" currency_id="<?php echo $package['currency_id']?>"
											class="<?php echo (isset($selected_key) && $package['event_price_id']==$selected_key)?'selected':'';?>">

												<div class="selectDate">
													<div class="select-Button01">
													<?php echo translate_phrase('Select')?>
													</div>
												</div>
										<?php endif;?>

												<div class="package">
													<div class="package-innr-row">
														<div class="package-innr-row-left">
															<span class="plan_name"
																lang="<?php echo $package['name']?>"><?php echo $package['description'];?>
															</span> - <span class="green-color"><?php echo $package['currency'].number_format($package['per_date_price']);?></span>
														<?php if($package['quantity']<99):?>
															/ <?php echo translate_phrase('ticket')?>
														<?php endif;?>
														</div>
														<?php if($package['quantity']<99):?>
															<?php if($package['save_per'] && $signup_discount):?>
															<div class="selectDateText width-saveTxt font-italic">
															<?php echo $package['save_per'].'%'.translate_phrase(' off');?>
															</div>
															<?php endif;?>
	
															<?php if($package['extra']):?>
															<div class="guaranteeText width-garanteeTxt font-italic">
															<?php echo $package['extra']?>
															</div>
															<?php endif;?>
														<?php endif;?>
													</div>
													<div class="guarantee">
														<p>
														<!--<?php echo translate_phrase('One easy ')?>
															<span class="currency"><?php echo $package['currency'];?>
															</span><span class="plan_amount"><?php echo number_format($package['total']);?>
															</span>
															<?php echo translate_phrase('payment ')?>-->
															<?php if($package['save_amount'] && $signup_discount) 															 
															if(!$fb_like_discount)															
															{
																echo '('.translate_phrase('Take ').$package['currency'].number_format($package['save_amount']).translate_phrase(' off non-member price').'!)';
															}
															else
															{
																echo '('.translate_phrase('Take ').$package['currency'].number_format($package['save_amount'] + 30).translate_phrase(' off non-member price').'!)';
															}															
															?>
														</p>
													</div>
												</div> 
										<?php if(count($ticket_packages) > 1):?>																				
											</a>
										<?php endif;?>
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

										<?php if(count($ticket_packages) > 1):?>
										<div class="selectedTxt">										
										<?php echo translate_phrase('Selected')?>
											: <span id="selectedPlanTxt"> <?php 
											if(isset($selected_key))
											{
												echo $selected_package['description'].$static_text.$selected_package['currency'].number_format($selected_package['total']).' ('.translate_phrase("includes ").$event_info['price_includes'].")";
											}
											else
											{
												echo translate_phrase('None');
											}
											?> </span>
										</div>
										<?php endif;?>

											<input type="hidden" name="plan_name" id="plan_name"
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
										
										<?php if(!$fb_like_discount):?>
										<div class="style01 comn-top-mar">
											<b><span class="Red-color fb_plugin_text" lang="<?php echo translate_phrase("HKD30 discount has been applied to your ticket price because you liked our Facebook page")?>!"><a href="http://www.facebook.com/datetix" target="_NEW"><?php echo translate_phrase("Take HKD30 off instantly")?></a><?php echo translate_phrase(" to all events by liking our Facebook page now")?>!</span></b>
										</div>
										<div class="appear-prt-but">
											<div class="fb-like" data-href="https://www.facebook.com/<?php echo $this->config->item('page_id');?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
										</div>
										<?php else:?>
										<div class="style01 comn-top-mar">
											<b><span class="DarkGreen-color fb_plugin_text" lang="<?php echo translate_phrase("Take HKD30 off instantly to all events by liking our Facebook page now")?>!"><?php echo translate_phrase("HKD30 discount has been applied to your ticket price because you liked our Facebook page")?>!</span></b>
										</div>
										<?php endif;?>
		
										<?php if($event_info['max_prepaid_tickets'] > SMALL_EVENT_USER_LIMIT):?>
											<?php if(!$signup_discount):?>
												<?php if($event_info['event_id'] != 11):?>
													<?php if($ad_id != 12):?>
													<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("Take ") . $package['currency'].number_format($package['save_amount']) . translate_phrase(" off instantly ")?></a><?php echo translate_phrase(" to this event by ")?><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>
													<?php else:?>
													<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="http://www.datetix.hk/join"><?php echo translate_phrase("Take ") . $package['currency'].number_format($package['save_amount']) . translate_phrase(" off instantly (offer available until May 1)")?></a><?php echo translate_phrase(" to this event by ")?><a href="http://www.datetix.hk/join"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>
													<?php endif;?>
												<?php else:?>
													<div class="style01 comn-top-mar"><span class="Red-color"><b><a href="<?php echo base_url().url_city_name();?>/apply.html?highlight=1"><?php echo translate_phrase("Save 15% off")?></a><?php echo translate_phrase(" this event by ")?><a href="<?php echo base_url().url_city_name();?>/apply.html?highlight=1"><?php echo translate_phrase("applying")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("signing in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account")?>!</b></span></div>													
												<?php endif;?>
											<?php else:?>
												<?php if($event_info['event_id'] != 11):?>
													<?php if($ad_id != 999):?>
													<div class="style01 comn-top-mar"><span class="DarkGreen-color"><b><?php echo $package['currency'].number_format($package['save_amount']) . translate_phrase(" member discount has been applied to your ticket price because you are a ").get_assets('name','DateTix').translate_phrase(" member")?>!</b></span></div>																				
													<?php else:?>
													<div class="style01 comn-top-mar"><span class="DarkGreen-color"><b><?php echo translate_phrase("67% member discount has been applied to your ticket price because you are a ").get_assets('name','DateTix').translate_phrase(" member")?>!</b></span></div>
													<?php endif;?>																														
												<?php else:?>
													<div class="style01 comn-top-mar"><span class="DarkGreen-color"><b><?php echo translate_phrase("15% member discount has been applied to your ticket price because you are a ").get_assets('name','DateTix').translate_phrase(" member")?>!</b></span></div>
												<?php endif;?>																														
											<?php endif;?>																														
										<?php endif;?>																														
																																																																																								
										<?php if(!$this->user_id && $event_info['max_prepaid_tickets'] > 0):?>
											<?php if($event_info['max_prepaid_tickets'] > SMALL_EVENT_USER_LIMIT):?>
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
														<input class="Degree-input" value="<?php echo set_value('name'); ?>" name="name" type="text" required>
														<label class="input-hint error" for="name" error_txt="<?php echo translate_phrase('Name is required.')?>"><?php echo form_error('first_name'); ?></label>
													</div>
												</div>
												
												<div class="cardInputRow user_info">
													<div class="sfp-1-Left"> <?php echo translate_phrase('Mobile No.')?>: <span>*</span></div>
													<div class="sfp-1-Right">
														<input class="Degree-input" value="<?php echo set_value('last_name'); ?>" name="mobile_phone_number" type="text" required>
														<label class="input-hint error" for="mobile_phone_number" error_txt="<?php echo translate_phrase('Mobile Number is required.')?>"><?php echo form_error('mobile_phone_number'); ?><?php echo form_error('last_name'); ?></label>
													</div>
												</div>
											<?php else:?>
												<div class="style01 comn-top-mar"><span class="Red-color"><b><?php echo translate_phrase('You must ')?><a href="<?php echo base_url().url_city_name();?>/apply.html"><?php echo translate_phrase("apply")?></a><?php echo translate_phrase(" or ")?><a href="<?php echo base_url().url_city_name();?>/signin.html"><?php echo translate_phrase("sign in")?></a><?php echo translate_phrase(" to your free ").get_assets('name','DateTix').translate_phrase(" account to buy tickets for this event.")?></b></span></div>
												<div class="det-center-inner3">
													<div class="order-btn">
														<a href="<?php echo base_url().url_city_name();?>/signin.html">
														<?php echo translate_phrase('Sign in to get tickets')?>&nbsp;
														<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
													</div>
													<div class="verisignLogo">
														<img src="<?php echo base_url()?>assets/images/verisign.jpg" />
													</div>
												</div>
											<?php endif;?>																														
										<?php endif;?>
											
										<div class="det-center-inner3">
										<?php if(($this->user_id || $event_info['max_prepaid_tickets'] > SMALL_EVENT_USER_LIMIT) && ($event_info['max_prepaid_tickets'] > 0)):?>
											<?php if(!$this->user_id && 1 == 2):?>
												<div class="order-btn">
												<a href="<?php echo base_url().url_city_name();?>/signin.html">
												<?php echo translate_phrase('Get discounted tickets')?>&nbsp;
												<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
												</div>
												
												<div class="suggest-btn"> 
												<a onclick="make_payment()" href="javascript:;">
												<?php if(!$this->user_id || $user_event):?>
												<?php echo translate_phrase('Get full price tickets&nbsp;')?>
												<?php else:?>
												<?php echo translate_phrase('Get full price tickets&nbsp;')?>
												<?php //echo '<span class="rsvp_and">'.translate_phrase("RSVP and ").'</span><span class="order_now">'.translate_phrase('get tickets&nbsp;').'</span>';?>
												<?php endif;?>
												</a>
												</div>
											<?php else:?>
												<?php if(!$this->user_id):?>																		
													<div class="order-btn">
													<a onclick="make_payment()" href="javascript:;">
													<?php echo translate_phrase('Get tickets')?>&nbsp;
													<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
													</div>
												<?php else:?>
													<div class="order-btn">
													<a onclick="make_payment()" href="javascript:;">
													<?php echo translate_phrase('Get discounted tickets')?>&nbsp;
													<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
													</div>
												<?php endif;?>
											<?php endif;?>

											<div class="verisignLogo">
												<img src="<?php echo base_url()?>assets/images/verisign.jpg" />
											</div>
											<!-- REMOVED condition [static event_id = 11]-->
											<?php elseif ($event_info['max_prepaid_tickets'] == 0):
													$url_para=($form_para)?$form_para.'&freeRSVP':'?freeRSVP';												
												?>
													<div class="order-btn"><a href="<?php echo base_url().url_city_name().'/event.html'.$url_para; ?>"> <?php echo translate_phrase('RSVP Now')?> &nbsp; <img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a></div>
											<?php endif;?>
											
											<!--<div class="order-btn"><a href="<?php echo base_url().url_city_name();?>/apply.html?highlight=1"><?php echo translate_phrase('RSVP Now')?>&nbsp;<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a></div>-->										
										</div>
									</form>
								</div>
							</div>
						
							<div class="getBox bordernone">
								<div class="edu-main">
									<p><?php echo translate_phrase("All sensitive consumer data collected on this site is used solely for the purposes of completing this transaction. Information is transmitted to our payment partners using industry standard 256 bit SSL encryption")?>.</p>
									<p><?php echo translate_phrase("Please contact us at ")?><a href="mailto:payment@datetix.com">payment@datetix.com</a> <?php echo translate_phrase("if you have any further questions")?>.</p>
								</div>
							</div>
						<?php else:?>
							<?php if(!$fb_like_discount):?>
							<div class="style01 comn-top-mar">
								<b><span class="Red-color fb_plugin_text" lang="<?php echo translate_phrase("Email info@datetix.com with your name and email address to receive your free 3 month premium membership")?>!"><a href="http://www.facebook.com/datetix" target="_NEW"><?php echo translate_phrase("Get a free 3 month premium membership to ").get_assets('name','DateTix').translate_phrase(" (valued at HKD888) by liking our Facebook page now")?></a>!</span></b>
							</div>
							<div class="appear-prt-but">
								<div class="fb-like" data-href="https://www.facebook.com/<?php echo $this->config->item('page_id');?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
							</div>
							<?php else:?>
							<div class="style01 comn-top-mar">
								<b><span class="DarkGreen-color fb_plugin_text" lang="<?php echo translate_phrase("Get a free 3 month premium membership to ").get_assets('name','DateTix').translate_phrase(" (valued at HKD888) by liking our Facebook page now")?>!"><?php echo translate_phrase("Email info@datetix.com with your name and email address to receive your free 3 month premium membership")?>!</span></b>
							</div>
							<?php endif;?>

							<div class="getBox bordernone">
								<?php if ($event_info['max_prepaid_tickets'] == 0):
								$url_para = ($form_para)?$form_para.'&freeRSVP':'?freeRSVP';
								?>
										<div class="order-btn"><a href="<?php echo base_url().url_city_name().'/event.html'.$url_para; ?>"> <?php echo translate_phrase('RSVP Now')?> &nbsp; <img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a></div>
								<?php else:?>
									<div class="order-btn">
										<a href="<?php echo base_url().url_city_name();?>/apply.html?highlight=1">
										<?php echo translate_phrase('RSVP Now')?>&nbsp;
										<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
									</div>	
								<?php endif;?>
								
							</div>
		        			<?php endif;?>						
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
