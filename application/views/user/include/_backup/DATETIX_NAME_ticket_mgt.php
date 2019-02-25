<!-- Ticket Management -->
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
		<?php echo translate_phrase('You may enjoy this date free because you have at least 1 mutual friend with ') .$intro['intro_name'].translate_phrase(' who is on DateTix')?>.
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


		<?php if($is_bottom_msg_show):?>
		<?php if($user_data['num_date_tix'] > 0):?>
		<div
			class="respond-now-txt Red-color dateMsg <?php if(!$is_ticket_paid_user):?>hidden<?php endif;?>">
			<?php echo translate_phrase('You will be charged a date ticket if and only if ');?>
			<?php echo $intro['intro_name']?>
			<?php echo translate_phrase(' accepts your date.');?>
		</div>

		<div class="no-ticket-msg respond-now-txt Red-color"
		<?php if($is_ticket_paid_user):?> style="display: none;"<?php endif;?>">
			<?php echo translate_phrase('Since both you and ').$intro["intro_name"].translate_phrase(" don't have the Unlimited Date Tickets account upgrade, would you like to use one of your date tickets in order to allow ").$intro["intro_name"].translate_phrase(' to accept your date')?>
			?
			<?php echo translate_phrase('Alternatively')?>, <a class="blu-color"
				href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=<?php echo $return_page?>&tab=active"><?php echo translate_phrase('add the Unlimited Date Tickets upgrade to your account')?></a>!
		</div>

		<div class="appear-prt-but comn-top-mar fl">
			<a href="javascript:;" id="use_date_ticket"
				lang="<?php echo $intro['user_intro_id']?>">
				<?php if(isset($is_ticket_paid_user) && $is_ticket_paid_user):?>
					<span class="appr-cen" lang="<?php echo translate_phrase("Use Date Ticket");?>"><?php echo translate_phrase("Don't Use Date Ticket");?></span>
				<?php else:?>
					<span class="appr-cen use_ticket" lang="<?php echo translate_phrase("Don't Use Date Ticket");?>"><?php echo translate_phrase("Use Date Ticket");?></span>
				<?php endif;?>				
			</a> 
			<label class="error input-hint" id="ticket_error"></label>
		</div>
		<?php else:?>
		<div class="no-ticket-msg respond-now-txt Red-color"
		<?php if($is_ticket_paid_user):?> style="display: none;"<?php endif;?>">
			<?php echo translate_phrase('Since both you and ').$intro["intro_name"].translate_phrase(" don't have the Unlimited Date Tickets account upgrade, would you like to use one of your date tickets in order to allow ").$intro["intro_name"].translate_phrase(' to accept your date')?>?
			<?php echo translate_phrase('Alternatively')?>, <a class="blu-color"
				href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=<?php echo $return_page?>&tab=active"><?php echo translate_phrase('add the Unlimited Date Tickets upgrade to your account')?></a>!
		</div>
		<div class="lbl-black comn-top-mar fl">
		<?php echo translate_phrase('You have no date tickets left.');?>
			<a class="blu-color"
				href="<?php echo base_url() . url_city_name() ?>/get-more-tickets.html?return_to=<?php echo $return_page;?>&tab=active"><?php echo translate_phrase('Get more date tickets now')?></a>!
		</div>
		<?php endif;?>
		<?php endif;?>
		<!-- END Ticket Management -->
