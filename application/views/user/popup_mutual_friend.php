<?php
$user_id = $this->session->userdata('user_id');
//6 - View Mutual Facebook Friends
//$is_premius_view_mutual_friend = $this->datetix->is_premium_user($user_id,6);
$is_premius_view_mutual_friend = true;
?>
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box">
				<div class="popup-header step-form-Part">
					<h1>
					<?php echo translate_phrase('Mutual Friends'); ?>
					</h1>
					<p>
					<?php echo translate_phrase('You have')?>
					<?php echo count($mutual_friends)?>
					<?php echo count($mutual_friends)==1 ?translate_phrase('mutual friend with '):translate_phrase('mutual friends with ')?>
					<?php echo $user_info->first_name?>
						.
					</p>
					<?php if(isset($mutual_friends_app_users_cnt) && $mutual_friends_app_users_cnt):?>
					<p class="DarkGreen-color">
					<?php echo translate_phrase('No date ticket is required for you to date');?>
					<?php echo $user_info->first_name?>
					<?php echo translate_phrase('because at least one of your mutual friends are on '.get_assets('name','DateTix'))?>.
					</p>
					<?php else:?>
					<p class="Red-color">
					<?php echo translate_phrase('But none of your mutual friends are on '.get_assets('name','DateTix').' yet')?>
						. <span class="semi-bold"><?php echo translate_phrase('Invite any of them to join '.get_assets('name','DateTix'))?>
						</span>
						<?php echo translate_phrase('so that you may date ')?>
						<?php echo $user_info->first_name?>
						<?php echo translate_phrase('without needing a date ticket')?>
						.
					</p>
					<?php endif;?>
				</div>

				<div class="two-column-school-container" id="schoolContainer">
				<?php if($is_premius_view_mutual_friend):?>
				<?php if(isset($mutual_friends) && $mutual_friends):?>
				<?php foreach ($mutual_friends as $fb_friend):?>
					<div class="fb_userbox">
						<div class="friendsPhoto">
							<img width="145" height="159"
								src="https://graph.facebook.com/<?php echo $fb_friend['id']?>/picture?width=145&amp;height=159"
								alt="<?php echo $fb_friend['name']?>">
						</div>
						<div class="friendsRight">
							<div class="friendsName">
							<?php echo $fb_friend['name']?>
							</div>
							<div class="friendsSigle">
							<?php echo isset($fb_friend['relationship_status'])?$fb_friend['relationship_status']:'' ?>
							</div>
							<div class="friendsSigle">
							<?php echo isset($fb_friend['location']['name'])?$fb_friend['location']['name']:'' ?>
							</div>

							<?php if(isset($fb_friend['friend_with_datetix']) && $fb_friend['friend_with_datetix']):?>
							<div class="friendsCity">
							<?php echo (count($fb_friend['friend_with_datetix']) > 1)?count($fb_friend['friend_with_datetix']).translate_phrase(' Friends '):count($fb_friend['friend_with_datetix']).translate_phrase(' Friend ')?>
							<?php echo translate_phrase(' on '.get_assets('name','DateTix'))?>
							</div>
							<?php endif;?>

							<?php if(!$this->datetix->is_app_user($fb_friend['id'])):?>
							<a class="inviteBtn"
								onclick="facebook_send_message('<?php echo $fb_friend['id']?>')"><?php echo translate_phrase('Invite')?>
							</a> <label class="mobile-error input-hint error-msg"
								id="fb_<?php echo $fb_friend['id'] ?>"></label>
								<?php endif;?>

						</div>
					</div>
					<?php endforeach;?>
					<?php endif;?>
					<?php else:?>
					<div class="fl">
					<?php echo translate_phrase("You don't have membership option for view mutual friend")?>
					</div>
					<?php endif;?>

				</div>
				<div class="Nex-mar">
					<?php if(!empty($return_url)):?>
					<a href="<?php echo base_url().$return_url?>" class="Next-butM"><?php echo translate_phrase('Ok') ?></a>
					<?php else:?>
					<a href="javascript:;" onclick="$.fancybox.close();" class="Next-butM"><?php echo translate_phrase('Ok') ?></a>
					<?php endif;?>
				</div>
			</div>
		</div>
	</div>
</div>

<script
	src="http://connect.facebook.net/en_US/all.js"></script>
<div id="fb-root"></div>
<script>
// assume we are already logged in
FB.init({appId: '<?php echo $fb_app_id ?>', xfbml: true, cookie: true});
</script>

<script>
function facebook_send_message(to) {
	if(isMobileView == 'No')
	{
        FB.ui({
	            method: 'send',
	        	name: 'Datetix',
	            link: '<?php echo base_url() ?>',
	            picture: '<?php echo base_url() ?>assets/images/datetix.png',
	            description:'<?php echo $fb_desc ?>',
	            to:to
        	},
        	function(response) {
        		if (response && response.post_id) {
        			console.log('Post was published.');
        		} else {
        			console.log('Post was not published.');
        		}
			});
        $("#fb_"+to).text(' ');
	}
	else
	{
		$("#fb_"+to).text('<?php echo translate_phrase("Invite your friend is not yet supported on your mobile device. Please invite your friends using your desktop PC.")?>');
    }
}
</script>
