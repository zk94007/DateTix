<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<script>
$(document).ready(function(){
		var allEmptyEmails = true;
	
	$("#ticket_form :input[type='text']").keyup(function(){
		var emailVal = $.trim($(this).val());
		var dbValues = $(this).attr('lang');
		var old_value = $(this).attr('old_value');
		
		if(emailVal != old_value)
		{
			allEmptyEmails = false;
			$(this).attr('lang',1)
		}
		else
		{	
			$(this).attr('lang',0)
		}
	});
	
	$(".invite_friend").live('click',function(){
		var is_form_filled = true;
	
		var allEmails = [];
		
		$.each($("#ticket_form").find(":input[type='text']"),function(i,item){
			var attr = $(item).attr('required');
			if (typeof attr !== 'undefined' && attr !== false && attr == 'required') {
				var emailVal = $.trim($(item).val());
				var dbValues = $(item).attr('lang');
				var old_value = $(item).attr('old_value');
				
				if(dbValues == 0 && emailVal != "")
				{
					allEmptyEmails = false;
				}
			}		
		});
		console.log(allEmptyEmails);
		$.each($("#ticket_form").find(":input[type='text']"),function(i,item){
			var attr = $(item).attr('required');
			
			
			if (typeof attr !== 'undefined' && attr !== false && attr == 'required') {
				var emailVal = $.trim($(item).val());
				var validateMailFlag = validateEmail(emailVal);
				
				if(!validateMailFlag)
				{
					if(allEmptyEmails)
					{							
						is_form_filled = false;
						$(item).siblings('label').text($(item).siblings('label').attr('error_txt'));								
					}
					else if(!validateMailFlag && emailVal!= "")
					{
						is_form_filled = false;
						$(item).siblings('label').text($(item).siblings('label').attr('error_txt'));								
					}
					else
					{
						allEmails.push(emailVal.toLowerCase());
						$(item).siblings('label').text('');
					}
					
				}
				else
				{
					allEmails.push(emailVal.toLowerCase());
					$(item).siblings('label').text('');
				}
			}
		});
		
		//If user didn't invite his friend then restrict form action..
		//is_form_filled = allEmptyEmails;
		for(var i=allEmails.length-1; i>=0;i--)
		{
			if( i != allEmails.indexOf(allEmails[i]) && allEmails[i] != "")
			{
				is_form_filled = false;
				$("#email_row_"+i).find('label').text("<?php echo translate_phrase('Please enter a different email address for each ticket') ?>");
			}
		}
		
		if(is_form_filled)
		{
			//console.log('Form submited');
			$("#ticket_form").submit();
		}
		return false;
	});	
})
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-p-Main">
			<div class="A-step-partM">
				<div class="My-int-head">
					<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
					<h1><?php
						if($this->session->userdata('is_free_rsvp'))
						{
							echo 'RSVP Confirmed';
						}
						elseif(isset($event_attendee_data) && $event_attendee_data)
						{
							$is_order_complete = 1;
							foreach($event_attendee_data as $ticket_info)
							{
								if(!$ticket_info['confirmed_user'])
								{
									$is_order_complete = 0;
								}
							}
							echo $title . ' Confirmation';
							//echo $is_order_complete ==0?'<span class="Red-color"> '.translate_phrase('Not Yet Complete!').'</span>':'';
						}?>
					</h1>
					
				</div>
				
				<?php if($event_order_data):?>
				<div class="emp-B-tabing-prt">
					<p class="cityTxt bold">
						<?php if($this->session->userdata('is_free_rsvp')):?>
						<?php echo translate_phrase("Your RSVP to ").$event_info['event_name'] . ' ' . translate_phrase("on"). ' ' . date('l, '.DATE_FORMATE,strtotime($event_info['event_start_time'])) .translate_phrase(" has been confirmed. Invite your friends to RSVP by entering their email addresses below:");?>
						
						<?php else:?>
						<span class=" DarkGreen-color"><?php echo translate_phrase('Thanks for your order for ').$total_purchase_tickets.translate_phrase(' tickets to ').$event_info['event_name'].translate_phrase(' on ').date('l, '.DATE_FORMATE,strtotime($event_info['event_start_time'])).translate_phrase(' at ').$event_info['name']?>!</span>
						<?php echo translate_phrase("In order to help everyone meet the right people,")?> <span class="Red-color"><?php echo translate_phrase("we need each attendee to RSVP.")?></span> <?php echo translate_phrase("Please enter the email address of each person who you just bought tickets for (including yourself if you are attending) and we will send each of them an RSVP invitation email:")?>
						<?php endif;?>
					</p>
					
					<div class="userBox-wrap comn-top-mar">
						<form id="ticket_form" action="<?php echo base_url().url_city_name().'/event-order-confirmation.html?rsvp_id='.$event_order_id; ?>" method="post">
							<ul class="cms-list order">
								<li class="bold">
									<?php if($this->session->userdata('is_free_rsvp') == 0):?>
									<div class="colm column-no"><?php echo translate_phrase("Ticket ID");?></div>
									<?php endif;?>
									<div class="colm column-name"><?php echo translate_phrase("Attendee Name");?></div>
									<!--<div class="colm column-no"><?php echo translate_phrase("DateTix ID");?></div>-->
									<div class="colm column-name"><?php echo translate_phrase("Attendee Email");?></div>							
								</li>
								
								<?php 
								$cnt = 0;
								foreach($event_attendee_data as $order):?>
								<li>
									<?php if($this->session->userdata('is_free_rsvp') == 0):?>
									<div class="colm column-no"><?php echo $order['event_ticket_id'];?></div>
									<?php endif;?>
									
									<div class="colm column-name"><?php 
										if($order['confirmed_user']){
											echo $order['first_name'].' '.$order['last_name'];
											if($order['rsvp_time'] == "0000-00-00 00:00:00")
											{
												echo " <span class='Red-color'>(".translate_phrase('not yet RSVPed').")</span>";
											}
											else
											{
												echo " <span class='DarkGreen-color'>(".translate_phrase('RSVPed').")</span>";
											}
										}
										else
										{
											//At this point we don't have username and we can't show paid_by_first_name users name.... because if user purchase 10 tickets then we can't show all tickets to his name.
											//echo $event_order_data['paid_by_first_name'];
											echo  '<span class="bold">Friend '.$cnt.'</span>';
										}
									?></div>
									<!--<div class="colm column-no"><?php echo $order['confirmed_user']?$order['confirmed_user']:'&nbsp;';?></div>-->									
									<div class="colm column-name" id="email_row_<?php echo $cnt;?>">
										<input lang='<?php echo $order['invite_email_address']?1:'0';?>' class="txt_input" name="attendee_emails[<?php echo $order['event_ticket_id'];?>]" placeholder=""  old_value="<?php echo $order['invite_email_address']?$order['invite_email_address']:'';?>" value="<?php echo $order['invite_email_address']?$order['invite_email_address']:'';?>" type="text" required>
										<label class="input-hint error"error_txt="<?php echo translate_phrase('Please enter valid email address.')?>"></label>
									</div>
								</li>
								<?php $cnt++; endforeach;?>
							</ul>
							<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg_invite');?></div>
							<p>&nbsp;</p>
							<div class="btn-group center">
								<button type="button" class="btn btn-pink invite_friend" lang="approve"><?php echo translate_phrase("Send RSVP Invitation Emails");?></button>
							</div>
						</form>
					</div>
				</div>
				<?php else:?>
					<div class="Edit-p-top1"><?php echo translate_phrase("No RSVP Found.");?>.</div>					
				<?php endif;?>
			</div>
		</div>
	</div>
</div>
