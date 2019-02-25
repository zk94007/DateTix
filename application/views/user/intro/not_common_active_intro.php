<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id);
?>
<?php if(isset($intros_data['active']) && $intros_data['active']):?>
<?php foreach ($intros_data['active'] as $intro):?>
<?php
$noun = ($intro['gender_id'] == 1)?translate_phrase('he'):translate_phrase('she');
$pro_noun = ($intro['gender_id'] == 1)?translate_phrase('him'):translate_phrase('her');

$not_interested = '0000-00-00 00:00:00';
$intro_not_interested = '0000-00-00 00:00:00';
if($intro['user1_id'] == $user_id)
{
	$not_interested  = $intro['user1_not_interested_time'];
	$intro_not_interested = $intro['user2_not_interested_time'];
		
}
	
if($intro['user2_id'] == $user_id)
{
	$not_interested  = $intro['user2_not_interested_time'];
	$intro_not_interested  = $intro['user1_not_interested_time'];
}
?>
<!-- Start USER DIV  -->
<div class="userBox" id="intro_<?php echo $intro['user_intro_id']?>">
	<div class="userBox-wrap">
		<div class="userTopRow">
			<div class="userTopRowHed">
			<?php echo $intro['intro_name']?>
			</div>
			<div class="userTopRowTxt">
			<?php echo translate_phrase('Introduced on  ').date(DATE_FORMATE,strtotime($intro['intro_available_time']))?>
				(
				<?php echo translate_phrase('expires on ').date(DATE_FORMATE,strtotime($intro['intro_expiry_time']));?>
				)
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
					<div class="mutualLink">
					<?php if(isset($intro['fb_mutual_friend']) && $intro['fb_mutual_friend']):?>
						<a
							href="<?php echo base_url().url_city_name().'/mutual-friends.html?return_to=my-intros.html&tab=active&fb_id='.$intro['fb_id'];?>"><?php echo $intro['fb_mutual_friend'];?>
						</a>
						<?php else:?>
						<span><?php echo $user_data['facebook_id'] && $intro['facebook_id']?translate_phrase('No Mutual Friends'):translate_phrase('Mutual Friends Info Not Available');?>
						</span>
						<?php endif;?>
					</div>
				</div>
			</div>
		</div>


		<div class="Half-datebox" lang="<?php echo $intro['user_intro_id']?>">
		<?php if($intro_not_interested == '0000-00-00 00:00:00'):?>
			<div class="dateBoxHed Black-color">
			<?php if($intro['date_data']):?>
			<?php if($intro['date_data']['date_time'] != '0000-00-00 00:00:00' && $not_interested == '0000-00-00 00:00:00'):?>
			<?php if(date("Y-m-d",strtotime($intro['date_data']['date_time'])) < SQL_DATE):?>
			<?php echo translate_phrase('You dated ').$pro_noun.translate_phrase(' on ').date(DATE_FORMATE,strtotime($intro['date_data']['date_time']));?>
			<?php else:?>
			<?php echo translate_phrase('You have an upcoming date on ').date(DATE_FORMATE,strtotime($intro['date_data']['date_time']));?>
			<?php endif;?>
			<?php else:?>
			<?php if($intro['date_data']['date_suggested_by_user_id'] == $user_id ):?>
			<?php echo translate_phrase('You sent a date request on').' '.date(DATE_FORMATE,strtotime($intro['date_data']['date_suggested_time']));?>
			<?php else:?>
			<?php echo translate_phrase('You recieved a date request on').' '.date(DATE_FORMATE,strtotime($intro['date_data']['date_suggested_time']));?>
			<?php endif;?>
			<?php endif;?>
			<?php else:?>
			<?php echo translate_phrase('Are you interested in meeting');?>
			<?php echo $intro['intro_name']?>
				?
				<?php endif;?>
			</div>
			<?php if($not_interested != '0000-00-00 00:00:00'):?>
			<div class="dateBoxHed Black-color cl">
			<?php echo translate_phrase('You told us you are not interested on'.' '.date(DATE_FORMATE,strtotime($not_interested)));?>
			</div>
			<?php else:?>
			<?php if($intro['date_data'] ):?>
			<div class="datesArea bor-none Mar-top-none">
				<div class="dateRow">
				<?php echo date('l',strtotime($intro['date_data']['date_suggested_time']))?>
					,
					<?php echo date(DATE_FORMATE,strtotime($intro['date_data']['date_suggested_time']))?>
					at
					<?php echo date('gA',strtotime($intro['date_data']['date_suggested_time']))?>
				</div>
				<div class="dateRow">
				<?php echo $intro['date_data']['date_type_desc'].translate_phrase(' with ').$intro['intro_name'].translate_phrase(' at ').$intro['date_data']['name'];?>
				</div>
				<div class="locationArea">
					<p>
					<?php echo $intro['date_data']['address'];?>
					<?php if($intro['date_data']['venue_id']):?>
						&nbsp; &nbsp; <a href="javascript:;" onclick="openNewWindow(this)"
							data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=my-intros.html&tab=active&venue='.$this->utility->encode($intro['date_data']['venue_id']);?>">
							<?php echo translate_phrase('View Map')?> </a>
							<?php endif;?>
					</p>
					<p>
					<?php if(isset($intro['date_data']['venue_dates']) && $intro['date_data']['venue_dates']){ echo implode(' /', $intro['date_data']['venue_dates']);}?>
					</p>
					<p>
					<?php echo $intro['date_data']['phone_number'];?>
					</p>
					<p>
						<a href="javascript:;" onclick="openNewWindow(this)"
							data-url="<?php echo $intro['date_data']['review_url'];?>"><?php echo $intro['date_data']['review_url'];?>
						</a>
					</p>
				</div>
			</div>
			<?php endif;?>
			<?php endif;?>
			<div class="appear-prt-but">
			<?php if($intro['date_data'] && $intro['date_data']['date_time'] != '0000-00-00 00:00:00' && $not_interested == '0000-00-00 00:00:00'):?>
			<?php if(date("Y-m-d",strtotime($intro['date_data']['date_time'])) < SQL_DATE):?>
				<div class="suggest-btn">
					<a
						href="<?php echo base_url().url_city_name().'/my-date.html#past';?>"><?php echo translate_phrase('View Past Date');?>
					</a>
				</div>
				<?php else:?>
				<div class="suggest-btn">
					<a
						href="<?php echo base_url().url_city_name().'/my-date.html#upcoming';?>"><?php echo translate_phrase('View Upcoming Date');?>
					</a>
				</div>
				<div class="disable-butn btn-blue not-int-btn">
					<a
						href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to=my-intros.html&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo $intro['date_data']?translate_phrase('Cancel Date'):translate_phrase('Not Interested')?>
					</a>
				</div>
				<?php endif;?>
				<?php else:?>
				<div class="suggest-btn">
					<a
						href="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to=my-intros.html&intro='.$this->utility->encode($intro['user_intro_id'])?>">
						<?php if($intro['date_data'] && $not_interested == '0000-00-00 00:00:00' && $intro_not_interested == '0000-00-00 00:00:00')
						echo translate_phrase('Change Date');
						else
						echo translate_phrase('Suggest Date');?>
					</a>
				</div>
				<div
					class="<?php echo $not_interested == '0000-00-00 00:00:00'?'disable-butn btn-blue':'lbl-black'?> not-int-btn">
					<?php if($not_interested == '0000-00-00 00:00:00'):?>
					<a
						href="<?php echo base_url().url_city_name().'/lack-interest.html?return_to=my-intros.html&intro='.$this->utility->encode($intro['user_intro_id']);?>"><?php echo $intro['date_data']?translate_phrase('No Longer Interested '):translate_phrase('Not Interested')?>
					</a>
					<?php endif;?>
				</div>
				<?php endif;?>
			</div>

			<?php if(!$intro['date_data']):?>
			<div class="respond-now-txt Black-color">
			<?php if($not_interested == '0000-00-00 00:00:00'):?>
				<span class="respond"><?php echo translate_phrase('Respond now ')?>
				</span>
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
	</div>
</div>
<!-- END USER DIV  -->
			<?php endforeach;?>
			<?php endif;?>