<?php
$user_id = $this->session->userdata('user_id');
//5 - View Past Date Feedback
$is_premius_member_view_feedback = $this->datetix->is_premium_user($user_id,5);
?>
<?php if(isset($intros_data['expired']) && $intros_data['expired']):?>
<?php foreach ($intros_data['expired'] as $intro):?>
<?php
$not_interested = '0000-00-00 00:00:00';
if($intro['user1_id'] == $user_id)
{
	$not_interested  = $intro['user1_not_interested_time'];
}
if($intro['user2_id'] == $user_id)
{
	$not_interested  = $intro['user2_not_interested_time'];
}
?>
<!-- Start USER DIV  -->
<div class="itemBox" id="intro_<?php echo $intro['user_intro_id']?>">
	<div class="datesArea">
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
					data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=my-date.html&tab=past&venue='.$this->utility->encode($intro['venue_id']);?>"><?php echo translate_phrase('View Map')?></a>
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
	<div class="userBox bor-none">
		<div class="userTopRow">
			<div class="userTopRowHed">
			<?php echo $intro['intro_name']?>
			</div>
			<div class="userTopRowTxt">
			<?php echo translate_phrase('Introduced on  ').date(DATE_FORMATE,strtotime($intro['intro_available_time']))?>
				<span class="Red-color">(<?php echo date("Y-m-d",strtotime($intro['intro_expiry_time'])) < SQL_DATE?translate_phrase('expired on '):translate_phrase('expires on '); echo date(DATE_FORMATE,strtotime($intro['intro_expiry_time']));?>)</span>
			</div>
		</div>
		<div class="userTop">
			<div class="userBoxLeft">
				<!-- -START PHOTO SECTION -->
			<?php if($intro['privacy_photos'] == 'SHOW') :?>
			<?php if (isset($intro['primary_photo'])&& $intro['primary_photo']): ?>
				<div class="img-left-box">
					<ul class="slider">
						<li class="img-slide"><a
							href="<?php echo $intro['view_profile_link']?>"><img
								style="height: 205px; max-width: 205px;"
								src="<?php echo $intro['primary_photo'] ?>" /> </a>
						</li>
					</ul>
				</div>
				<?php else:?>
				<?php echo translate_phrase('No photos added yet');?>
				<?php endif;?>

				<?php if (isset($intro['other_photos'])&& $intro['other_photos']): ?>
				<div class="sml-img">
					<div class="view4more-link">
						<a href="<?php echo $intro['view_profile_link']?>"> <?php echo translate_phrase('View ').$intro['other_photos'].translate_phrase(' more '); echo $intro['other_photos'] > 1?translate_phrase('photos'):translate_phrase('photo'); echo translate_phrase(' in profile');?>
						</a>
					</div>
				</div>
				<?php endif;?>
				<?php else:?>
				<?php echo translate_phrase('Photos available upon request');?>
				<?php endif;?>
				<!-- -END PHOTO SECTION -->
				<div class="profileBtn">
					<a href="<?php echo $intro['view_profile_link']?>"><div
							class="appr-cen">
							<?php echo translate_phrase('View Profile')?>
						</div> </a>
				</div>
			</div>

			<div class="userBoxRight">
				<div class="userbox-innr">
					<div class="userbox-left-txt">
					<?php echo translate_phrase('Compatibility Score')?>
						:
					</div>
					<div
						class="score-txt <?php echo $intro['intro_score']['score'] < 50 ?'Red-color':'';?>">
						<?php echo round($intro['intro_score']['score']).'/100'?>
					</div>
				</div>
				<div class="detail-list">
					<ul>
					<?php echo (isset($intro['intro_age']) && $intro['intro_age'])?'<li>'.$intro['intro_age'].translate_phrase(' years old').'</li>':''?>
					<?php echo (isset($intro['intro_ethnicity']) && $intro['intro_ethnicity'])?'<li>'.$intro['intro_ethnicity'].'</li>':''?>
						<li><?php echo (isset($intro['sexual_status']) && $intro['sexual_status'])?$intro['sexual_status']:''?>
						</li>
						<?php echo (isset($intro['intro_height']) && $intro['intro_height'])?'<li>'.$intro['intro_height'].'</li>':''?>
						<?php echo (isset($intro['intro_body_type']) && $intro['intro_body_type'])?'<li>'.$intro['intro_body_type'].'</li>':''?>
						<?php echo (isset($intro['zodiac_sign']) && $intro['zodiac_sign'])?'<li>'.$intro['zodiac_sign'].'</li>':''?>
					</ul>
				</div>

				<?php if(isset($intro['intro_current_location'])):?>
				<div class="userbox-innr">
					<div class="userbox-left-txt">
					<?php echo translate_phrase('Lives in');?>
					</div>
					<div class="userbox-right-txt">
					<?php echo $intro['intro_current_location']['city_description'].', '.$intro['intro_current_location']['country_description']?>
					</div>
				</div>
				<?php endif;?>

				<?php if(isset($intro['intro_study']) && $intro['intro_study']):?>
				<div class="userbox-innr">
					<div class="userbox-left-txt">
					<?php echo translate_phrase('Studied at');?>
					</div>
					<div class="userbox-right-txt">
					<?php echo $intro['intro_study'];?>
					</div>
				</div>
				<?php endif;?>


				<?php if(isset($intro['intro_works']) && $intro['intro_works']):?>
				<div class="userbox-innr">
					<div class="userbox-left-txt">
					<?php echo translate_phrase('Works as');?>
					</div>
					<div class="userbox-right-txt">
					<?php echo $intro['intro_works'];?>
					</div>
				</div>
				<?php endif;?>

				<div class="appear-prt comn-top-mar">
				<?php if(isset($intro['common_likes']) && $intro['common_likes']):?>
					<h2>
					<?php echo translate_phrase('Common Interests');?>
					</h2>
					<div class="appear-prt-but">
					<?php foreach ($intro['common_likes'] as $interest):?>
						<div class="appr-cen Upgrd-blue">
						<?php echo $interest;?>
						</div>
						<?php endforeach;?>
					</div>
					<?php endif;?>

					<?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>
					<div class="mutualLink">
					<?php if(isset($intro['fb_mutual_friend']) && $intro['fb_mutual_friend']):?>
						<a
							href="<?php echo base_url().url_city_name().'/mutual-friends.html?return_to=my-date.html&tab=past&fb_id='.$intro['fb_id'];?>"><?php echo $intro['fb_mutual_friend'];?>
						</a>
						<?php else:?>
						<span><?php echo $user_data['facebook_id'] && $intro['facebook_id']?translate_phrase('No Mutual Friends'):translate_phrase('Mutual Friends Info Not Available');?>
						</span>
						<?php endif;?>
					</div>
					<?php endif;?>
				</div>
			</div>
		</div>
	</div>
	
	<?php include APPPATH.'views/user/include/chat_box.php';?>
	
	<div class="appear-prt-but comn-top-mar">
		<a class="btn large-txt appr-cen Upgrd-blue toggleBtn"
			lang="form_<?php echo $intro['user_date_id']?>" href="javascript:;"><?php echo $intro['user_feedback']?translate_phrase('Modify Feedback'):translate_phrase('Leave Feedback')?>
		</a>
		<?php if($intro['intro_feedback_data']):?>
		<?php //if($intro['intro_feedback_data'] && $is_premius_member_view_feedback):?>
		<a class="btn large-txt appr-cen"
			href="<?php echo base_url().url_city_name().'/view-feedback.html?date='.$this->utility->encode($intro['user_date_id']).'&u='.$this->utility->encode($intro['user_id']);?>"><?php echo translate_phrase('View ').$intro['intro_name'].translate_phrase("'s Feedback of You ")?>
		</a>
		<?php endif;?>
	</div>

	<div class="edu-main" id="form_<?php echo $intro['user_date_id']?>"
		style="display: none;">
		<form
			action="<?php echo base_url().url_city_name().'/submit-feedback.html?date='.$this->utility->encode($intro['user_date_id']);?>"
			method="post">
			<input type="hidden" name="intro_id"
				value="<?php echo $intro['user_id']?>" />

			<div class="aps-d-top">
				<h2>
				<?php echo translate_phrase('Did ').$intro['intro_name'].translate_phrase(' show up for the date')?>
					? <span>*</span>
				</h2>
				<div class="f-decrMAIN">
					<div class="f-decr">
						<div
							class="<?php if(isset($intro['user_feedback']['date_showed_up']) && $intro['user_feedback']['date_showed_up'] == 0):?>Intro-Button<?php else:?>Intro-Button-sel<?php endif;?>">
							<a lang="feedbackContainer_<?php echo $intro['user_date_id']?>"
								href="javascript:;" class="rdo_div_show_up" key="1">Yes</a>
						</div>
						<div
							class="<?php if(isset($intro['user_feedback']['date_showed_up']) && $intro['user_feedback']['date_showed_up'] == 0):?>Intro-Button-sel<?php else:?>Intro-Button Bor-left-None<?php endif;?>">
							<a href="javascript:;"
								lang="refundContainer_<?php echo $intro['user_date_id']?>"
								class="rdo_div_show_up" key="0">No</a>
						</div>
						<input type="hidden" name="date_showed_up"
							value="<?php echo (isset($intro['user_feedback']['date_showed_up']) && $intro['user_feedback']['date_showed_up'] == 0)?0:1?>">
					</div>
				</div>
			</div>

			<div id="feedbackContainer_<?php echo $intro['user_date_id']?>"
				class="feedbackContainer <?php if(isset($intro['user_feedback']['date_showed_up']) && $intro['user_feedback']['date_showed_up'] == 0):?>hidden<?php endif;?>">
				<div class="aps-d-top">
					<h2>
					<?php echo translate_phrase('How did ').$intro['intro_name'].translate_phrase(' look') ?>
						? <span>*</span>
					</h2>
					<div class="f-decrMAIN">
						<div class="f-decr">
						<?php if($looks):?>
						<?php unset($looks['']);?>
						<?php foreach ($looks as $key=>$look):?>
							<div
								class="<?php $sel_key = ''; if(isset($intro['user_feedback']['date_looks']) && $intro['user_feedback']['date_looks'] == $key): $sel_key = $key;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="<?php echo $key?>"><?php echo $look?>
								</a>
							</div>
							<?php endforeach;?>
							<?php endif;?>
							<input type="hidden" name="date_looks"
								value="<?php echo isset($intro['user_feedback']['date_looks']) ?$intro['user_feedback']['date_looks']:'';?>"
								error_msg="<?php echo translate_phrase('Please select looks')?>">
						</div>
					</div>
				</div>

				<div class="aps-d-top">
					<h2>
					<?php echo translate_phrase('Was ').$intro['intro_name'].translate_phrase(' friendly and personable')?>
						? <span>*</span>
					</h2>
					<div class="f-decrMAIN">
						<div class="f-decr">
							<div
								class="<?php $sel_key = ''; if(isset($intro['user_feedback']['date_attitude']) && $intro['user_feedback']['date_attitude'] == 5): $sel_key = 5;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="5"><?php echo translate_phrase('Not at All')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_attitude']) && $intro['user_feedback']['date_attitude'] == 4): $sel_key = 4;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="4"><?php echo translate_phrase('Slightly')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_attitude']) && $intro['user_feedback']['date_attitude'] == 3): $sel_key = 3;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="3"><?php echo translate_phrase('Average')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_attitude']) && $intro['user_feedback']['date_attitude'] == 2): $sel_key = 2;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="2"><?php echo translate_phrase('Very')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_attitude']) && $intro['user_feedback']['date_attitude'] == 1): $sel_key = 1;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="1"><?php echo translate_phrase('Extremely')?>
								</a>
							</div>
							<input type="hidden" name="date_attitude"
								value="<?php echo $sel_key;?>"
								error_msg="<?php echo translate_phrase('Please select personable')?>">
						</div>
					</div>
				</div>

				<div class="aps-d-top">
					<h2>
					<?php echo translate_phrase('How was the level of conversation and engagement?')?>
						<span>*</span>
					</h2>
					<div class="f-decrMAIN">
						<div class="f-decr">
							<div
								class="<?php $sel_key = ''; if(isset($intro['user_feedback']['date_conversation']) && $intro['user_feedback']['date_conversation'] == 5): $sel_key = 5;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="5"><?php echo translate_phrase('Very Boring')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_conversation']) && $intro['user_feedback']['date_conversation'] == 4): $sel_key = 4;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="4"><?php echo translate_phrase('Boring')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_conversation']) && $intro['user_feedback']['date_conversation'] == 3): $sel_key = 3;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="3"><?php echo translate_phrase('Average')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_conversation']) && $intro['user_feedback']['date_conversation'] == 2): $sel_key = 2;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="2"><?php echo translate_phrase('Interesting')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_conversation']) && $intro['user_feedback']['date_conversation'] == 1): $sel_key = 1;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="1"><?php echo translate_phrase('Very Interesting')?>
								</a>
							</div>
							<input type="hidden" name="date_conversation"
								value="<?php echo $sel_key;?>"
								error_msg="<?php echo translate_phrase('Please select level of conversation and engagement')?>">
						</div>
					</div>
				</div>


				<div class="aps-d-top">
					<h2>
					<?php echo translate_phrase('How much chemistry and interest level did you feel?')?>
						<span>*</span>
					</h2>
					<div class="f-decrMAIN">
						<div class="f-decr">
							<div
								class="<?php $sel_key = ''; if(isset($intro['user_feedback']['date_chemistry']) && $intro['user_feedback']['date_chemistry'] == 5): $sel_key = 5;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="5"><?php echo translate_phrase('Not at All')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_chemistry']) && $intro['user_feedback']['date_chemistry'] == 4): $sel_key = 4;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="4"><?php echo translate_phrase('Slightly')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_chemistry']) && $intro['user_feedback']['date_chemistry'] == 3): $sel_key = 3;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="3"><?php echo translate_phrase('Average')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_chemistry']) && $intro['user_feedback']['date_chemistry'] == 2): $sel_key = 2;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="2"><?php echo translate_phrase('Very')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_chemistry']) && $intro['user_feedback']['date_chemistry'] == 1): $sel_key = 1;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="1"><?php echo translate_phrase('Extremely')?>
								</a>
							</div>
							<input type="hidden" name="date_chemistry"
								value="<?php echo $sel_key;?>"
								error_msg="<?php echo translate_phrase('Please select interest level')?>">
						</div>
					</div>
				</div>

				<div class="aps-d-top">
					<h2>
					<?php echo translate_phrase('How much did you enjoy your date overall?')?>
						<span>*</span>
					</h2>
					<div class="f-decrMAIN">
						<div class="f-decr">
							<div
								class="<?php $sel_key = ''; if(isset($intro['user_feedback']['date_overall']) && $intro['user_feedback']['date_overall'] == 5): $sel_key = 5;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="5"><?php echo translate_phrase('Not at All')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_overall']) && $intro['user_feedback']['date_overall'] == 4): $sel_key = 4;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="4"><?php echo translate_phrase('Slightly')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_overall']) && $intro['user_feedback']['date_overall'] == 3): $sel_key = 3;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="3"><?php echo translate_phrase('Average')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_overall']) && $intro['user_feedback']['date_overall'] == 2): $sel_key = 2;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="2"><?php echo translate_phrase('Very')?>
								</a>
							</div>
							<div
								class="<?php if(isset($intro['user_feedback']['date_overall']) && $intro['user_feedback']['date_overall'] == 1): $sel_key = 1;?>Intro-Button-sel<?php else:?>Intro-Button<?php endif;?>">
								<a href="javascript:;" class="rdo_div" key="1"><?php echo translate_phrase('Extremely')?>
								</a>
							</div>
							<input type="hidden" name="date_overall"
								value="<?php echo $sel_key;?>"
								error_msg="<?php echo translate_phrase('Please give date overall rating')?>">
						</div>
					</div>
				</div>

				<div class="aps-d-top">
					<h2>
					<?php echo translate_phrase('What five words would you use to describe ').$intro['intro_name']?>
						? <span>*</span>
					</h2>
					<div class="f-decrMAIN your-personality"
						lang="<?php echo $intro['user_date_id']?>">
						<div class="f-decr">
							<ul>
							<?php $selected_words = isset($intro['user_feedback']['descriptive_word_id'])?explode(',', $intro['user_feedback']['descriptive_word_id']):array();

							?>
							<?php foreach($descriptive_word as $row):?>
								<li><a
									class="<?php echo (in_array($row['descriptive_word_id'], $selected_words))?'appr-cen':'disable-butn'?>"
									href="javascript:;"
									id="<?php echo $row['descriptive_word_id'];?>"><?php echo ucfirst($row['description']);?>
								</a></li>
								<?php endforeach;?>
							</ul>
							<input type="hidden"
								id="descriptive_word_id_<?php echo $intro['user_date_id']?>"
								name="descriptive_word_id"
								value="<?php echo isset($intro['user_feedback']['descriptive_word_id'])?$intro['user_feedback']['descriptive_word_id']:'';?>"
								error_msg="<?php echo translate_phrase('Please select five words to describe ').$intro['intro_name']?>">
						</div>
					</div>
				</div>

				<div class="aps-d-top">
					<h2>
					<?php echo translate_phrase('Anything else you would like us to know about what you liked/disliked about the date?')?>
					</h2>
					<div class="f-decrMAIN">
						<div class="f-decr">
							<textarea name="date_comments" cols="" rows=""
								class="as-E-textarea">
								<?php echo isset($intro['user_feedback']['date_comments'])?$intro['user_feedback']['date_comments']:''?>
							</textarea>
						</div>
					</div>
				</div>

				<div class="feedbackBtn">
					<a
						onclick="submit_feedback_form(<?php echo $intro['user_date_id']?>)"
						class="appr-cen Upgrd-blue large-txt fr"><?php echo translate_phrase('Send Feedback')?>
					</a>
				</div>
			</div>
			<div id="refundContainer_<?php echo $intro['user_date_id']?>"
				class="refundContainer <?php if(isset($intro['user_feedback']['date_showed_up']) && $intro['user_feedback']['date_showed_up'] != 1):?><?php else:?>hidden<?php endif;?>">
				<div class="aps-d-top">
					<h2>
					<?php echo translate_phrase('We apologize for the inconvenience. Please briefly describe what happened')?>
						: <span>*</span>
					</h2>
					<div class="f-decrMAIN">
						<div class="f-decr">
							<textarea name="date_refund_request" cols="" rows=""
								class="as-E-textarea refund_txt"
								error_msg="<?php echo translate_phrase('You must enter a reason for your refund request.')?>">
								<?php echo isset($intro['user_feedback']['date_refund_request'])?$intro['user_feedback']['date_refund_request']:''?>
							</textarea>
							<label class="input-hint error refund_txt_lbl"></label>
						</div>
					</div>
				</div>

				<div class="feedbackBtn">
					<a
						onclick="submit_feedback_form(<?php echo $intro['user_date_id']?>)"
						class="appr-cen Upgrd-blue large-txt fr"><?php echo translate_phrase('Request Refund')?>
					</a>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END USER DIV  -->
<?php endforeach;?>
<?php endif;?>
