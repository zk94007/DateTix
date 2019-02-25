<script src="<?php echo base_url()?>assets/js/general.js"></script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_UNLIMITED_DATES);
$is_premium_intro = $this->datetix->is_premium_user($user_info['user_id'],PERMISSION_UNLIMITED_DATES);
$intro_noun = $user_info['gender_id'] == 1?translate_phrase('he'):translate_phrase('she');
?>
<!--*********Suggest date ideal Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box Mar-top-none popupMiddle">
				<h1 class="citypageHed bor-none">
				<?php echo $heading_txt;?>
				</h1>
				<?php 	$is_bottom_msg_show  = 1;?>
				<p class="cityTxt edu-main DarkGreen-color">
				<?php echo $heading_msg;?>
				<?php echo $user_info['first_name']?>
					!
					<?php echo translate_phrase('We will notify you as soon as ').$intro_noun.translate_phrase(' responds')?>
					.
				</p>

				<!-- Is Date Ticket is Paid -->
				<?php if($is_premium_intro || $is_premius_member || $fb_mutual_friend_use_app > 0 || $is_ticket_paid_by_user):?>

				<?php else:?>
				<p class="cityTxt Red-color mar-top2 no-ticket-msg">
				<?php echo translate_phrase("However, since both of you don't have the")?>
					<a class="blu-color"
						href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase("Unlimited Date Tickets account upgrade")?>
					</a>,
					<?php echo $user_info['first_name'].translate_phrase(" won't be able to accept your date without using a date ticket. To allow ").$user_info['first_name'].translate_phrase(' to freely accept your date, please consider taking one of following actions');?>
					:
				</p>

				<div class="how-it-works-main no-ticket-msg">
					<ul class="works-list">
					<?php $list_no = 1;?>
					<?php if($fb_mutual_friend_use_app < 1 && count($fb_mutual_friend) > 0 && $user_info['facebook_id']):?>
						<li>
							<div class="circle circle-small">
							<?php echo $list_no++;?>
							</div>
							<div class="list-content">
								<div class="blu-color">
									<a
										href="<?php echo base_url().url_city_name().'/mutual-friends.html?return_to=my-date.html&tab=pending&fb_id='.$user_info['facebook_id'];?>"><?php echo translate_phrase('Invite one of your ').count($fb_mutual_friend).translate_phrase(' mutual friends with ')?>
										<?php echo $user_info['first_name']?> <?php echo translate_phrase(' to join '.get_assets('name','DateTix'))?>
									</a>
								</div>
								<div class="appear-prt-but comn-top-mar">
									<a
										href="<?php echo base_url().url_city_name().'/mutual-friends.html?return_to=my-date.html&tab=pending&fb_id='.$user_info['facebook_id'];?>"><span
										class="appr-cen"><?php echo translate_phrase('Invite Mutual Friend')?>
									</span> </a>
								</div>
							</div>
						</li>
						<?php endif;?>

						<?php if(!$is_premius_member):?>
						<li>
							<div class="circle circle-small">
							<?php echo $list_no++;?>
							</div>
							<div class="list-content">
								<div class="blu-color">
									<a
										href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-intros.html&tab=upcoming"><?php echo translate_phrase('Add the Unlimited Date Tickets upgrade to your account')?>
									</a>
								</div>
								<div class="appear-prt-but comn-top-mar">
									<a
										href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-intros.html&tab=upcoming"><span
										class="appr-cen"><?php echo translate_phrase('Upgrade Account')?>
									</span> </a>
								</div>
							</div>
						</li>
						<?php endif;?>

						<!-- Ticket Management -->
						<li>
							<div class="circle circle-small">
							<?php echo $list_no++;?>
							</div>
							<div class="list-content">
							<?php if($user_data['num_date_tix'] < 1):?>
								<div class="f-decrMAIN blu-color">
									<span class="Black-color"><?php echo translate_phrase('You have no date tickets left')?>.
									</span><a
										href="<?php echo base_url() . url_city_name() ?>/get-more-tickets.html?return_to=my-intros.html&tab=upcoming"><?php echo translate_phrase('Get more date tickets now')?>
									</a>!
								</div>
								<?php else:?>
								<div class="blu-color">
									<a href="javascript:;"
										onclick="$('#use_date_ticket').trigger('click');"><?php echo translate_phrase('Use one of your date tickets')?>
									</a><span class="Black-color">. <?php 
									echo translate_phrase('You have ').$user_data['num_date_tix'];
									echo ($user_data['num_date_tix']> 1) ?translate_phrase(' date tickets'):translate_phrase(' date ticket');
									echo translate_phrase(' left in your account')?>.</span>
								</div>
								<?php endif;?>
								<div class="appear-prt-but comn-top-mar">
								<?php if($user_data['num_date_tix'] >= 1):?>
									<a href="javascript:;" id="use_date_ticket"
										lang="<?php echo $user_date_data['0']['user_intro_id']?>"> <span
										class="<?php echo isset($is_ticket_paid_by_user) && $is_ticket_paid_by_user ? 'appr-cen':'disable-butn'?>"><?php echo translate_phrase('Use Date Ticket')?>
									</span> </a>
									<?php endif;?>
									<a
										href="<?php echo base_url() . url_city_name() ?>/get-more-tickets.html?return_to=my-intros.html&tab=upcoming"><span
										class="appr-cen"><?php echo translate_phrase('Get More Date Tickets')?>
									</span> </a>
								</div>
								<label class="error input-hint" id="ticket_error"></label>
							</div>
						</li>

					</ul>
				</div>
				<?php endif;?>

				<div class="Nex-mar">
					<a href="<?php echo $return_url?>" class="Next-butM">Ok</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!--*********Suggest date ideal -Page close*********-->
