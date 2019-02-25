
<?php
if(date("Y-m-d",strtotime($intro['intro_available_time'])) > SQL_DATE)
{
	$tab = 'upcoming_intro';
}
else
{
	//active
	if(date("Y-m-d",strtotime($intro['intro_expiry_time'])) > SQL_DATE)
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
?>
<?php if(isset($intro['date_suggested_by_user_id'])):?>
<!-- Intro with Date box -->
<?php if($intro['date_accepted_by_user_id']):?>
<div class="Half-datebox" lang="<?php echo $intro['user_intro_id']?>">
<?php $is_box_show = 0;?>
	<div class="dateBoxHed cl">
	<?php if($intro['date_accepted_by_user_id'] == $user_id):?>
	<?php echo translate_phrase('You accepted ').$intro['intro_name']."'s".translate_phrase(' date suggestion on ').date(DATE_FORMATE,strtotime($intro['date_accepted_time']));?>
	<?php else:?>
	<?php echo $intro['intro_name'].translate_phrase(' has accepted your date suggestion on ').date(DATE_FORMATE,strtotime($intro['date_accepted_time']));?>
	<?php endif;?>
	</div>

	<div class="datesArea bor-none Mar-top-none">
		<div class="dateRow">
		<?php echo date('l',strtotime($intro['date_time']))?>
			,
			<?php echo date(DATE_FORMATE,strtotime($intro['date_time']))?>
			at
			<?php echo date('gA',strtotime($intro['date_time']))?>
		</div>
		<div class="dateRow">
		<?php echo $intro['date_type_desc'].translate_phrase(' with ').$intro['intro_name'].translate_phrase(' at ').$intro['name'];?>
		</div>
		<div class="locationArea">
			<p>
			<?php echo $intro['address'];?>
			<?php if($intro['venue_id']):?>
				&nbsp; &nbsp; <a href="javascript:;" onclick="openNewWindow(this)"
					data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=my-date.html&tab=pending&venue='.$this->utility->encode($intro['venue_id']);?>">
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
	</div>

	<div class="appear-prt-but">
		<div class="suggest-btn">
			<a
				href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to=my-date.html&intro='.$this->utility->encode($intro['user_intro_id'])?>"><?php echo translate_phrase('Change Date')?>
			</a>
		</div>
		<div class="disable-butn btn-blue not-int-btn">
			<a
				href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to=my-date.html&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo translate_phrase('Cancel Date');?>
			</a>
		</div>
	</div>
</div>
			<?php else:?>
			<?php if($intro['date_suggested_by_user_id'] == $user_id) :?>
<div class="full-width">
	<!--  -->
	<div class="Half-datebox" lang="<?php echo $intro['user_intro_id']?>">
	<?php $is_box_show = 0;?>
		<div class="dateRow">
		<?php echo translate_phrase('We have informed ');?>
		<?php echo $intro['intro_name']?>
		<?php echo translate_phrase(' of your date idea suggestion below and are waiting for');?>
		<?php echo $pro_noun.translate_phrase(' response ');?>
			:
		</div>
		<div class="datesArea bor-none Mar-top-none">
			<div class="dateRow">
			<?php echo date('l',strtotime($intro['date_time']))?>
				,
				<?php echo date(DATE_FORMATE,strtotime($intro['date_time']))?>
				at
				<?php echo date('gA',strtotime($intro['date_time']))?>
			</div>
			<div class="dateRow">
			<?php echo $intro['date_type_desc'].translate_phrase(' with ').$intro['intro_name'].translate_phrase(' at ').$intro['name'];?>
			</div>
			<div class="locationArea">
				<p>
				<?php echo $intro['address'];?>
				<?php if($intro['venue_id']):?>
					&nbsp; &nbsp; <a href="javascript:;" onclick="openNewWindow(this)"
						data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=my-date.html&tab=pending&venue='.$this->utility->encode($intro['venue_id']);?>">
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
		</div>

		<?php if($is_premium_intro || $is_premius_member || $fb_mutual_friend_use_app > 0):?>

		<?php else:?>
		<!-- Ticket is not Paid  fb_mutual friend > 0-->
		<?php if($is_ticket_paid_user):?>
		<div class="dateBoxHed">
		<?php echo translate_phrase('You will be charged a date ticket if and only if ');?>
		<?php echo $intro['intro_name']?>
		<?php echo translate_phrase(' accepts your date');?>
		</div>
		<?php endif;?>

		<div class="respond-now-txt comn-top-mar Red-color no-ticket-msg">
		<?php echo translate_phrase('Since both you and ').$intro["intro_name"].translate_phrase(" don't have the Unlimited Date Tickets account upgrade, would you like to use one of your date tickets in order to allow ").$intro["intro_name"].translate_phrase(' to accept your date')?>
			?
			<?php echo translate_phrase('Alternatively')?>
			, <a class="blu-color"
				href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=<?php echo $return_page?>&tab=active"><?php echo translate_phrase('add the Unlimited Date Tickets upgrade to your account')?>
			</a>!
		</div>
		<div class="appear-prt comn-top-mar">
			<a href="javascript:;" id="use_date_ticket"
				lang="<?php echo $intro['user_intro_id']?>"> <span
				class="<?php echo isset($is_ticket_paid_by_user) && $is_ticket_paid_by_user ? 'appr-cen':'disable-butn'?>"><?php echo translate_phrase('Use Date Ticket')?>
			</span> </a> <label class="error input-hint" id="ticket_error"></label>
		</div>
		<?php endif;?>
		<div class="appear-prt-but">
			<div class="suggest-btn">
				<a
					href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to=my-date.html&intro='.$this->utility->encode($intro['user_intro_id'])?>"><?php echo translate_phrase('Change Date')?>
				</a>
			</div>
			<div
				class="<?php echo $not_interested == '0000-00-00 00:00:00'?'disable-butn btn-blue':'lbl-black'?> not-int-btn">
				<?php if($not_interested == '0000-00-00 00:00:00'):?>
				<a
					href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to=my-date.html&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo translate_phrase('No Longer Interested')?>
				</a>
				<?php else:?>
				<label><?php echo translate_phrase('Not Interested')?> </label>
				<?php endif;?>
			</div>
		</div>
	</div>

</div>
				<?php else:?>
<div class="full-width">
	<div class="Half-datebox" lang="<?php echo $intro['user_intro_id']?>">
	<?php $is_box_show = 0;?>
		<div class="dateBoxHed">
		<?php echo $intro['intro_name']?>
		<?php echo translate_phrase(' wants to meet you');?>
			!
			<?php echo $noun?>
			<?php echo translate_phrase(' has suggested the following date idea')?>
			:
		</div>
		<div class="datesArea bor-none Mar-top-none">
			<div class="dateRow">
			<?php echo date('l',strtotime($intro['date_time']))?>
				,
				<?php echo date(DATE_FORMATE,strtotime($intro['date_time']))?>
				at
				<?php echo date('gA',strtotime($intro['date_time']))?>
			</div>
			<div class="dateRow">
			<?php echo $intro['date_type_desc'].translate_phrase(' with ').$intro['intro_name'].translate_phrase(' at ').$intro['name'];?>
			</div>
			<div class="locationArea">
				<p>
				<?php echo $intro['address'];?>
					&nbsp; &nbsp; <a href="javascript:;" onclick="openNewWindow(this)"
						data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=my-date.html&venue='.$this->utility->encode($intro['venue_id']);?>">
						<?php echo translate_phrase('View Map')?> </a>
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
		</div>
		<?php if($is_premium_intro || $is_premius_member || $fb_mutual_friend_use_app > 0):?>
		<?php if($is_ticket_paid_user):?>
		<div class="dateBoxHed">
		<?php echo translate_phrase('You will be charged a date ticket if and only if ');?>
		<?php echo $intro['intro_name']?>
		<?php echo translate_phrase(' accepts your date');?>
		</div>
		<?php endif;?>
		<?php else:?>
		<div class="appear-prt comn-top-mar">
			<a href="javascript:;" id="use_date_ticket"
				lang="<?php echo $intro['user_intro_id']?>"> <span
				class="<?php echo isset($is_ticket_paid_by_user) && $is_ticket_paid_by_user ? 'appr-cen':'disable-butn'?>"><?php echo translate_phrase('Use Date Ticket')?>
			</span> </a> <label class="error input-hint" id="ticket_error"></label>
		</div>
		<?php endif;?>

		<div class="userTopRow">
			<div class="style4">
			<?php echo translate_phrase('Please only accept the date if you are committed to meeting ');?>
			<?php echo $intro['intro_name']?>
			<?php echo translate_phrase(' in person');?>
				.
			</div>
		</div>

		<div class="appear-prt-but">
			<div class="suggest-btn">
				<a
					href="<?php echo base_url().url_city_name().'/accept-date.html?return_to=my-date.html&date='.$this->utility->encode($intro['user_date_id'])?>"><?php echo translate_phrase('Accept Date');?>
				</a>
			</div>
			<div
				class="<?php echo $not_interested == '0000-00-00 00:00:00'?'disable-butn btn-blue':'lbl-black'?> not-int-btn">
				<?php if($not_interested == '0000-00-00 00:00:00'):?>
				<a
					href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to=my-date.html&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo translate_phrase('No Longer Interested')?>
				</a>
				<?php else:?>
				<label><?php echo translate_phrase('Not Interested')?> </label>
				<?php endif;?>
			</div>

		</div>
		<div class="appear-prt-but">
			<a
				href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to=my-date.html&intro='.$this->utility->encode($intro['user_intro_id'])?>"><span
				class="appr-cen different_idea"><?php echo translate_phrase('Suggest Different Date Idea');?>
			</span> </a>
		</div>
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
	<div class="dateBoxHed Black-color">
	<?php echo translate_phrase('Are you interested in meeting');?>
	<?php echo $intro['intro_name']?>
		?
	</div>
	<?php if($not_interested != '0000-00-00 00:00:00'):?>
	<div class="dateBoxHed Black-color cl">
	<?php echo translate_phrase('You told us you are not interested on'.' '.date(DATE_FORMATE,strtotime($not_interested)));?>
	</div>
	<?php endif;?>

	<div class="appear-prt-but">
		<div class="suggest-btn">
			<a
				href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to=my-intros.html&intro='.$this->utility->encode($intro['user_intro_id'])?>">
				<?php echo translate_phrase('Suggest Date');?> </a>
		</div>
		<div
			class="<?php echo $not_interested == '0000-00-00 00:00:00'?'disable-butn btn-blue':'lbl-black'?> not-int-btn">
			<?php if($not_interested == '0000-00-00 00:00:00'):?>
			<a
				href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to=my-intros.html&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo translate_phrase('Not Interested')?>
			</a>
			<?php endif;?>
		</div>
	</div>

	<?php if(isset($tab) && $tab == 'expired_intro'):?>
	<div class="free-user-txt">
	<?php echo translate_phrase('Your introduction to ')?>
	<?php echo $intro['intro_name']?>
		<span class="Red-color"><?php echo translate_phrase(' expired on ').date(DATE_FORMATE,strtotime($intro['intro_expiry_time']));?>.</span>
		<?php if(!$is_premius_member):?>
		<a
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-intros.html&tab=expired"><?php echo translate_phrase("Upgrade to a premium membership");?>
		</a>
		<?php echo translate_phrase(' today to date expired introductions! ')?>
		<?php endif;?>
	</div>
	<?php else:?>
	<div class="respond-now-txt Black-color">
	<?php if($not_interested == '0000-00-00 00:00:00'):?>
		<span class="respond"><?php echo translate_phrase('Respond now ')?> </span>
		<?php endif;?>
		<?php if($not_interested == '0000-00-00 00:00:00'):?>
		<span class="Red-color">(<?php echo translate_phrase('intro expires on ').date(DATE_FORMATE,strtotime($intro['intro_expiry_time']));?>)</span>
		<?php else:?>
		<span class="Red-color"><?php echo translate_phrase('intro expires on ').date(DATE_FORMATE,strtotime($intro['intro_expiry_time']));?>
		</span>
		<?php endif;?>
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
	<div class="dateBoxHed Black-color cl">
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
	<div class="free-user-txt">
	<?php echo translate_phrase("Can't wait to meet ");?>
	<?php echo $intro['intro_name']?>
		?
		<?php if(!$is_premius_member):?>
		<a
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-intros.html&tab=upcoming"><?php echo translate_phrase("Add the Instant Introductions upgrade to your account")?>
		</a>
		<?php echo translate_phrase("to get instantly introduced to ");?>
		<?php echo $intro['intro_name']?>
		!
		<?php endif;?>
	</div>

	<?php if($is_premius_member):?>
	<?php if(isset($intro['date_accepted_time']) && $intro['date_accepted_time'] != '0000-00-00 00:00:00'):?>
	<?php if(date("Y-m-d",strtotime($intro['date_data']['date_time'])) < SQL_DATE):?>
	<div class="suggest-btn cl" style="margin-top: 10px;">
		<a
			href="<?php echo base_url().url_city_name().'/my-date.html#past';?>"><?php echo translate_phrase('View Past Date');?>
		</a>
	</div>
	<?php else:?>
	<div class="suggest-btn cl" style="margin-top: 10px;">
		<a
			href="<?php echo base_url().url_city_name().'/my-date.html#upcoming';?>"><?php echo translate_phrase('View Upcoming Date');?>
		</a>
	</div>
	<?php endif;?>
	<?php else:?>
	<div class="suggest-btn get-introduce-now">
		<a lang="<?php echo $intro['user_intro_id']?>" href="javascript:;"><?php echo translate_phrase('Get Introduced Now');?>
		</a>
	</div>
	<?php endif;?>
	<?php else:?>
	<div class="suggest-btn get-introduce-now">
		<a
			href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-intros.html&tab=upcoming"><?php echo translate_phrase('Get Introduced Now');?>
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