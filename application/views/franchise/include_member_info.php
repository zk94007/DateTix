<div class="userBox-wrap">
	<div class="userTopRow">
		<div class="userTopRowHed"><?php echo $user['first_name'].' '.$user['last_name'].' ('.$user['user_id'].' - '.$user['completed_application_step'].', '. $user['city_description'] . ')';?>
			<?php if($user['facebook_id'] > 0):?>
				(Facebook ID: <?php echo $user['facebook_id'];?>)
			<?php endif;?>
			<span class="pink-colr"><?php echo $user['match_score']['score'];?></span>
		</div>
		
		<div class="userTopRowHed"><a target="_blank" href="<?php echo base_url().'admin/go_profile/'.$user['user_id'].'?url=signin'?>"><span class="appr-cen">Sign In As User</span></a></div>
	</div>										
	<div class="userTop ">
		<div class="userBoxLeft">
			<?php if(isset($user['primary_photo']) && $user['primary_photo']):?>
			<div class="img-left-box">
				<div class="img-slide">
					<img style="width: 204px" src="<?php echo $user['primary_photo'];?>" alt="<?php echo $user['first_name']?>'s photo">
				</div>
			 </div>
			<?php endif;?>
			<div class="sml-img">
				<div class="view4more-link"><a href="<?php echo base_url().'admin/go_profile/'.$user['user_id'].'?url='.base_url() . url_city_name().'/user_info/'.$this->utility->encode($user['user_id']);?>"><?php echo translate_phrase("View Profile");?></a></div>
				<div class="view4more-link"><a href="<?php echo base_url().'admin/verification_info/'.$user['user_id']?>"><?php echo translate_phrase("View Verification info");?></a></div>
				<?php if($user['facebook_id']):?>
					<div class="view4more-link"><a target="_blank" href="<?php echo base_url().'admin/facebook_info/'.$user['user_id']?>"><?php echo translate_phrase("View Facebook Data");?></a></div>
					<div class="view4more-link"><a target="_blank" href="<?php echo 'http://www.facebook.com/'.$user['facebook_id']?>"><?php echo translate_phrase("View Facebook Page");?></a></div>
				<?php endif;?>
				<div class="view4more-link"><a href="<?php echo base_url().'admin/order_history/'.$user['user_id'];?>"><?php echo translate_phrase("View Order History");?></a></div>
			</div>
		</div>				
	</div>
</div>
