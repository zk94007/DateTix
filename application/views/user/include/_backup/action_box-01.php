<?php
$user_id = $this->session->userdata('user_id');
if(!isset($return_page))
{
	$return_page = 'my-date.html';
}
//Unlimited Dates - 1
$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_UNLIMITED_DATES);
//Instant Introductions
$is_premius_member_instant_intros = $this->datetix->is_premium_user($user_id,PERMISSION_INSTANT_INTRO);
//Re-Introductions
$is_premius_member_re_intros = $this->datetix->is_premium_user($user_id,PERMISSION_RE_INTRO);
if(date("Y-m-d",strtotime($intro['intro_available_time'])) > SQL_DATE)
{
	$tab = 'upcoming_intro';
}
else
{
	//active
	if(date("Y-m-d",strtotime($intro['intro_expiry_time'])) >= SQL_DATE)
	{
		$tab = 'active_intro';
	}
	else
	{
		//expired
		$tab = 'expired_intro';
	}
}

$is_box_show = 1;

$pro_noun = isset($pro_noun) && $pro_noun ? $pro_noun :($intro['gender_id'] == 1)?translate_phrase('him'):translate_phrase('her');
$noun = isset($noun) && $noun ? $noun :($intro['gender_id'] == 1)?translate_phrase('he'):translate_phrase('she');
$not_interested = '0000-00-00 00:00:00';
$intro_not_interested = '0000-00-00 00:00:00';
if($intro['user1_id'] == $user_id)
{
	$intro_id = $intro['user2_id'];
	$not_interested  = $intro['user1_not_interested_time'];
	$intro_not_interested = $intro['user2_not_interested_time'];
	$is_ticket_paid_user = isset($intro['user1_date_ticket_paid_by'])?$intro['user1_date_ticket_paid_by']:'0';
	$is_ticket_paid_by_intro = isset($intro['user2_date_ticket_paid_by'])?$intro['user2_date_ticket_paid_by']:'0';
}

if($intro['user2_id'] == $user_id)
{
	$intro_id = $intro['user1_id'];
	$not_interested  = $intro['user2_not_interested_time'];
	$intro_not_interested  = $intro['user1_not_interested_time'];
	$is_ticket_paid_user = isset($intro['user2_date_ticket_paid_by'])?$intro['user2_date_ticket_paid_by']:0;
	$is_ticket_paid_by_intro = isset($intro['user1_date_ticket_paid_by'])?$intro['user1_date_ticket_paid_by']:'0';
}
$is_premium_intro = $this->datetix->is_premium_user($intro_id,1);
$mutual_friends_on_datetix = $this->datetix->datetix_mutual_friend($user_id,$intro_id);
$fb_mutual_friend_use_app = count($mutual_friends_on_datetix );
$message_link = generate_link($intro['user_intro_id']);
?>

<?php if(isset($intro['date_suggested_by_user_id']) && $intro['date_cancelled_time'] == '0000-00-00 00:00:00'):?>
<!-- Intro with Date box -->
<?php if($intro['date_accepted_by_user_id'] && $intro['date_cancelled_time'] == '0000-00-00 00:00:00'):?>
<div class="Half-datebox" lang="<?php echo $intro['user_intro_id']?>">
<?php $is_box_show = 0;?>
	<div class="dateBoxHed cl">
	<?php if($intro['date_accepted_by_user_id'] == $user_id):?>
	<?php echo translate_phrase('You accepted ').$intro['intro_name']."'s".translate_phrase(' date suggestion on ').date(DATE_FORMATE,strtotime($intro['date_accepted_time']));?>
	<?php else:?>
	<?php echo $intro['intro_name'].translate_phrase(' accepted your date suggestion on ').date(DATE_FORMATE,strtotime($intro['date_accepted_time']));?>
	<?php endif;?>
	</div>

	<div class="datesArea bor-none Mar-top-none">
		<div class="dateRow">
		<?php echo date('l',strtotime($intro['date_time']))?>
			,
			<?php echo date(DATE_FORMATE,strtotime($intro['date_time']))?>
			at
			<?php echo date('g:ia',strtotime($intro['date_time']))?>
		</div>
		<?php if(isset($intro['name'])):?>
		<div class="dateRow">
		<?php echo $intro['date_type_desc'].translate_phrase(' with ').$intro['intro_name'].translate_phrase(' at ').$intro['name'];?>
		</div>
		<div class="locationArea">
			<p>
			<?php echo $intro['address'];?>
			<?php if($intro['venue_id']):?>
				&nbsp; &nbsp; <a href="javascript:;" onclick="openNewWindow(this)"
					data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to='.$return_page.'&tab=pending&venue='.$this->utility->encode($intro['venue_id']);?>">
					<?php echo translate_phrase('View Map')?> </a>
					<?php endif;?>
			</p>
			<p>
			<?php if($intro['venue_dates']){ echo implode(' /', $intro['venue_dates']);}?>
			</p>
			<p>
			<?php echo $intro['phone_number'];?>
			</p>
			<p>
				<a href="javascript:;" onclick="openNewWindow(this)"
					data-url="<?php echo $intro['review_url'];?>"><?php echo $intro['review_url'];?>
				</a>
			</p>
		</div>
		<?php endif;?>
	</div>
	<?php
	$is_bottom_msg_show  = 1;
	if($is_premius_member && $is_bottom_msg_show ):?>
	<div class="dateBoxHed align-left">
	<?php echo translate_phrase('You may enjoy this date free because you have the ')?>
		<a class="blu-color"
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase("Unlimited Date Tickets account upgrade")?></a>!
	</div>
	<?php $is_bottom_msg_show =0;  endif;?>


	<?php if($is_bottom_msg_show && $is_premium_intro ):?>
	<div class="dateBoxHed align-left">
	<?php echo translate_phrase('You may enjoy this date free because ').$intro['intro_name'].translate_phrase(' has the ')?>
		<a class="blu-color"
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase("Unlimited Date Tickets account upgrade")?></a>!
	</div>
	<?php $is_bottom_msg_show =0;   endif;?>

	<?php if($is_bottom_msg_show && $fb_mutual_friend_use_app > 0):?>
	<div class="dateBoxHed align-left">
	<?php echo translate_phrase('You may enjoy this date free because you have at least 1 mutual friend with ') .$intro['intro_name'].translate_phrase(' who is on '.get_assets('name','DateTix'))?>.
	</div>
	<?php $is_bottom_msg_show =0; endif;?>

	<?php if(isset($is_ticket_paid_by_intro) && $is_bottom_msg_show && $is_ticket_paid_by_intro):?>
	<div class="dateBoxHed align-left">
	<?php echo translate_phrase('You may enjoy this date free because ').$intro['intro_name'].translate_phrase(' has used a date ticket for this date')?>.
	</div>
	<?php $is_bottom_msg_show =0; endif;?>

	<?php if(isset($is_ticket_paid_user) && $is_bottom_msg_show && $is_ticket_paid_user && (isset($intro['date_accepted_time']) && $intro['date_accepted_time'] != '0000-00-00 00:00:00')):?>
	<div class="dateBoxHed align-left">
	<?php echo translate_phrase('You have already used a date ticket for this date')?>.
	</div>
	<?php $is_bottom_msg_show =0; endif;?>

	<div class="appear-prt-but">
		<div class="suggest-btn">
			<a
				href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to='.$return_page.'&intro='.$this->utility->encode($intro['user_intro_id'])?>"><?php echo translate_phrase('Change Date')?>
			</a>
		</div>
		<div class="disable-butn btn-blue not-int-btn">
			<a
				href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to='.$return_page.'&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo translate_phrase('Cancel Date');?>
			</a>
		</div>
	</div>
	<div class="appear-prt-but">
		<div class="suggest-btn">
			<a href="<?php echo $message_link;?>"><?php echo translate_phrase('Chat with ').$intro['intro_name'];?>
			</a>
		</div>
	</div>
</div>
	<?php else:?>
	<?php if($intro['date_suggested_by_user_id'] == $user_id ) :?>
<div class="full-width">
	<div class="Half-datebox" lang="<?php echo $intro['user_intro_id']?>">
	<?php $is_box_show = 0;?>
	<?php if($intro_not_interested == '0000-00-00 00:00:00'):?>
		<div class="dateRow">
		<?php echo translate_phrase('We are waiting for ');?>
		<?php echo $intro['intro_name']?>
		<?php echo translate_phrase(' to respond to your date idea suggested on');?>
		<?php echo date(DATE_FORMATE,strtotime($intro['date_suggested_time']))?>
			:
		</div>
		<div class="datesArea bor-none Mar-top-none">
			<div class="dateRow">
			<?php echo date('l',strtotime($intro['date_time']))?>
				,
				<?php echo date(DATE_FORMATE,strtotime($intro['date_time']))?>
				at
				<?php echo date('g:ia',strtotime($intro['date_time']))?>
			</div>
			<div class="dateRow">
			<?php echo $intro['date_type_desc'].translate_phrase(' with ').$intro['intro_name'].translate_phrase(' at ').$intro['name'];?>
			</div>
			<div class="locationArea">
				<p>
				<?php echo $intro['address'];?>
				<?php if($intro['venue_id']):?>
					&nbsp; &nbsp; <a href="javascript:;" onclick="openNewWindow(this)"
						data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to='.$return_page.'&tab=pending&venue='.$this->utility->encode($intro['venue_id']);?>">
						<?php echo translate_phrase('View Map')?> </a>
						<?php endif;?>
				</p>
				<p><?php if($intro['venue_dates']){ echo implode(' /', $intro['venue_dates']);}?></p>
				<p><?php echo $intro['phone_number'];?></p>
				<p>
					<a href="javascript:;" onclick="openNewWindow(this)"
						data-url="<?php echo $intro['review_url'];?>"><?php echo $intro['review_url'];?>
					</a>
				</p>
			</div>
		</div>
		<!-- Ticket Management -->
		<?php include APPPATH.'views/user/include/ticket_mgt.php';?>
		<!-- END Ticket Management -->

		<div class="appear-prt-but">
			<div class="suggest-btn">
				<a
					href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to='.$return_page.'&intro='.$this->utility->encode($intro['user_intro_id'])?>"><?php echo translate_phrase('Change Date')?>
				</a>
			</div>
			<div
				class="<?php echo $not_interested == '0000-00-00 00:00:00'?'disable-butn btn-blue':'lbl-black'?> not-int-btn">
				<?php if($not_interested == '0000-00-00 00:00:00'):?>
				<a
					href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to='.$return_page.'&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo translate_phrase('No Longer Interested')?>
				</a>
				<?php else:?>
				<label class="Red-color"><?php echo translate_phrase('Not Interested')?>
				</label>
				<?php endif;?>
			</div>
		</div>
		<?php else:?>
		<div class="msg-div">
			<span class="Red-color"><?php echo translate_phrase('Sorry').', '.translate_phrase('but ').$intro['intro_name'].translate_phrase(' told us on ').date(DATE_FORMATE,strtotime($intro_not_interested)).translate_phrase(' that ').$noun.translate_phrase(' is currently not available to meet you')?>.</span>
		</div>
		<?php endif;?>
	</div>
</div>
		<?php else:?>
<div class="full-width">
	<div class="Half-datebox" lang="<?php echo $intro['user_intro_id']?>">
	<?php $is_box_show = 0;?>
	<?php if($intro_not_interested == '0000-00-00 00:00:00'):?>
		<div class="dateBoxHed">
		<?php echo $intro['intro_name']?>
		<?php echo translate_phrase(' wants to meet you');?>!
			<?php echo strtoupper(substr($noun, 0, 1)) . substr($noun, 1, 3)?>
			<?php echo translate_phrase(' suggested on')?>
			<?php echo date(DATE_FORMATE,strtotime($intro['date_suggested_time']))?>
			<?php echo translate_phrase(' the following date idea')?>
			:
		</div>
		<div class="datesArea bor-none Mar-top-none">
			<div class="dateRow">
			<?php echo date('l',strtotime($intro['date_time']))?>
				,
				<?php echo date(DATE_FORMATE,strtotime($intro['date_time']))?>
				at
				<?php echo date('g:ia',strtotime($intro['date_time']))?>
			</div>
			<div class="dateRow">
			<?php echo $intro['date_type_desc'].translate_phrase(' with ').$intro['intro_name'].translate_phrase(' at ').$intro['name'];?>
			</div>
			<div class="locationArea">
				<p>
				<?php echo $intro['address'];?>
					&nbsp; &nbsp; <a href="javascript:;" onclick="openNewWindow(this)"
						data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to='.$return_page.'&venue='.$this->utility->encode($intro['venue_id']);?>">
						<?php echo translate_phrase('View Map')?> </a>
				</p>
				<p><?php if($intro['venue_dates']){ echo implode(' /', $intro['venue_dates']);}?></p>
				<p><?php echo $intro['phone_number'];?></p>
				<p>
					<a href="javascript:;" onclick="openNewWindow(this)"
						data-url="<?php echo $intro['review_url'];?>"><?php echo $intro['review_url'];?>
					</a>
				</p>
			</div>
		</div>

		<!-- Ticket Management -->
		<?php include APPPATH.'views/user/include/ticket_mgt.php';?>
		<!-- END Ticket Management -->

		<div class="userTopRow comn-top-mar">
			<div class="style4 Red-color">
			<?php echo translate_phrase('Please only accept the date if you are committed to meeting ');?>
			<?php echo $intro['intro_name']?>
			<?php echo translate_phrase(' in person');?>
				.
			</div>
		</div>

		<div class="appear-prt-but">
			<div class="suggest-btn">
				<a
					href="<?php echo base_url().url_city_name().'/accept-date.html?return_to='.$return_page.'&date='.$this->utility->encode($intro['user_date_id'])?>"><?php echo translate_phrase('Accept Date');?>
				</a>
			</div>
			<div
				class="<?php echo $not_interested == '0000-00-00 00:00:00'?'disable-butn btn-blue':'lbl-black'?> not-int-btn">
				<?php if($not_interested == '0000-00-00 00:00:00'):?>
				<a
					href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to='.$return_page.'&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo translate_phrase('Not Interested')?>
				</a>
				<?php else:?>
				<label class="Red-color"><?php echo translate_phrase('Not Interested')?>
				</label>
				<?php endif;?>
			</div>
		</div>
		<div class="appear-prt-but">
			<a
				href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to='.$return_page.'&intro='.$this->utility->encode($intro['user_intro_id'])?>"><span
				class="appr-cen different_idea"><?php echo translate_phrase('Suggest Different Date Idea');?>
			</span> </a>
		</div>

		<?php else:?>
		<div class="msg-div">
			<span class="Red-color"><?php echo translate_phrase('Sorry').', '.translate_phrase('but ').$intro['intro_name'].translate_phrase(' told us on ').date(DATE_FORMATE,strtotime($intro_not_interested)).translate_phrase(' that ').$noun.translate_phrase(' is currently not available to meet you')?>.</span>
		</div>
		<?php endif;?>
	</div>
</div>
		<?php endif;?>
		<?php endif;?>

		<?php else:?>
<!-- Intro box -->
<div
	class="Half-datebox <?php if(isset($tab) && $tab == 'upcoming_intro'):?>hidden<?php endif;?>"
	lang="<?php echo $intro['user_intro_id']?>">
	<?php if($intro_not_interested == '0000-00-00 00:00:00'):?>
	
	<?php if($not_interested != '0000-00-00 00:00:00'):?>
	<div class="dateBoxHed Red-color cl">
	<?php echo translate_phrase('You told us you are not interested on'.' '.date(DATE_FORMATE,strtotime($not_interested)));?>
	</div>
	<?php endif;?>

	<?php if(isset($tab) && $tab == 'expired_intro' && !$is_premius_member_re_intros):?>
		<!-- The "Suggest Date" and "Not Interested" buttons should only be hidden in Expired tab for users who don't have the re-introductions upgrade. -->
		<!-- Hide These two buttons in all cases for the time being. -->
	<?php else:?>
	
		<?php if($not_interested == '0000-00-00 00:00:00'):?>
		<div class="dateBoxHed">
		<?php echo translate_phrase('Interested in meeting').' '.$intro['intro_name']?>?
			<?php echo translate_phrase('Suggest a date idea now!');?>
		</div>
		<?php endif;?>
		<!-- Ticket Management -->
		<?php include APPPATH.'views/user/include/ticket_mgt.php';?>
		<!-- END Ticket Management -->
		
		
		<div class="appear-prt-but">
			<div class="suggest-btn">
				<a
					href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to='.$return_page.'&intro='.$this->utility->encode($intro['user_intro_id'])?>">
					<?php echo translate_phrase('Suggest Date');?> </a>
			</div>
	
			<div
				class="<?php echo $not_interested == '0000-00-00 00:00:00'?'disable-butn btn-blue':'lbl-black'?> not-int-btn">
				<?php if($not_interested == '0000-00-00 00:00:00'):?>
				<a
					href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to='.$return_page.'&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo translate_phrase('Not Interested')?>
				</a>
				<?php endif;?>
			</div>
		</div>
	<?php endif;?>
	
	<?php if(isset($tab) && $tab == 'expired_intro'):?>
	<div class="free-user-txt">
	<?php echo translate_phrase('Your introduction to ')?>
	<?php echo $intro['intro_name']?>
		<span class="Red-color"><?php echo translate_phrase(' expired on ').date(DATE_FORMATE,strtotime($intro['intro_expiry_time']));?></span>.
		<?php if($is_premius_member_re_intros):?>
		<?php echo translate_phrase('But you may still chat with ').$intro['intro_name'].translate_phrase(' because you have the')?>
		<a
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=<?php echo $return_page;?>&tab=expired"><?php echo translate_phrase("Re-Introductions account upgrade");?>						</a>!
		<?php else:?>
		<a
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=<?php echo $return_page;?>&tab=expired"><?php echo translate_phrase("Add the Re-Introductions upgrade to your account");?>
		</a>
			<?php echo translate_phrase('to chat with and date expired introductions')?>		!
		<?php endif;?>
	</div>
	<?php else:?>
	<div class="respond-now-txt Black-color">
	<?php if($not_interested == '0000-00-00 00:00:00'):?>
		<span class="respond"><?php echo translate_phrase('Respond now ')?> </span>
		<?php endif;?>
		<span class="Red-color">(<?php echo translate_phrase('intro expires on ').date(DATE_FORMATE,strtotime($intro['intro_expiry_time']));?>)</span>
	</div>
	<?php endif;?>
	<?php else:?>
	<div class="msg-div">
		<span class="Red-color"><?php echo translate_phrase('Sorry').', '.translate_phrase('but ').$intro['intro_name'].translate_phrase(' told us on ').date(DATE_FORMATE,strtotime($intro_not_interested)).translate_phrase(' that ').$noun.translate_phrase(' is currently not available to meet you')?>.</span>
	</div>
	<?php endif;?>
</div>
	<?php endif;?>
	
<!-- Only for Upcoming Intro -->
<?php if(isset($tab) && $tab == 'upcoming_intro' && $is_box_show):?>
<div class="Half-datebox">
<?php if($intro_not_interested == '0000-00-00 00:00:00'):?>
<?php if($not_interested != '0000-00-00 00:00:00'):?>
	<div class="dateBoxHed Red-color cl">
	<?php echo translate_phrase('You told us you are not interested on'.' '.date(DATE_FORMATE,strtotime($not_interested)));?>
	</div>
	<?php else:?>
	<h2>
	<?php echo translate_phrase('We will introduce you to');?>
	<?php echo $intro['intro_name']?>
	<?php echo translate_phrase(' on ')?>
	<?php echo date(DATE_FORMATE,strtotime($intro['intro_available_time']));?>
		.
	</h2>

	<?php if($is_premius_member_instant_intros):?>
	<div class="suggest-btn get-introduce-now">
		<a lang="<?php echo $intro['user_intro_id']?>" href="javascript:;"><?php echo translate_phrase('Get Introduced Now');?>
		</a>
	</div>
	<?php else:?>
	<div class="free-user-txt">
	<?php echo translate_phrase("Can't wait to meet ");?>
	<?php echo $intro['intro_name']?>
		?
		<?php if(!$is_premius_member_instant_intros):?>
		<a
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=<?php echo $return_page?>&tab=upcoming"><?php echo translate_phrase("Add the Instant Introductions upgrade to your account")?>
		</a>
		<?php echo translate_phrase("to get introduced instantly");?>						!
		<?php endif;?>
	</div>
	<div class="suggest-btn get-introduce-now">
		<a
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=<?php echo $return_page?>&tab=upcoming"><?php echo translate_phrase('Get Introduced Now');?>
		</a>
	</div>
	<?php endif;?>
	<?php endif;?>
	<?php else:?>
	<div class="msg-div">
		<span class="Red-color"><?php echo translate_phrase('Sorry').', '.translate_phrase('but ').$intro['intro_name'].translate_phrase(' told us on ').date(DATE_FORMATE,strtotime($intro_not_interested)).translate_phrase(' that ').$noun.translate_phrase(' is currently not available to meet you')?>.</span>
	</div>
	<?php endif;?>
</div>
<?php endif;?>

<?php  
	if(!isset($intro['date_accepted_by_user_id']) || $intro['date_accepted_by_user_id'] == '0' || $return_page == 'my-intros.html')
	{
		//include APPPATH.'views/user/include/chat_box.php';
	}
?>
