<div class="userBox-wrap">
	<div class="userTopRow">
		<div class="userTopRowHed"><?php echo $user['first_name'].' '.$user['last_name'].' ('.$user['user_id'].' - '.$user['completed_application_step'].', '. $user['city_description'] . ')';?>		
			<?php if($user['facebook_id'] > 0):?>
				(Facebook ID: <?php echo $user['facebook_id'];?>)
			<?php endif;?>
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
				<?php if(!$is_review_resricted):?>
				<div class="view4more-link"><a href="<?php echo base_url().'admin/verification_info/'.$user['user_id']?>"><?php echo translate_phrase("View Verification info");?></a></div>
				<?php endif;?>
				
				<?php if($user['facebook_id']):?>
					<div class="view4more-link"><a target="_blank" href="<?php echo 'http://www.facebook.com/'.$user['facebook_id']?>"><?php echo translate_phrase("View Facebook info");?></a></div>
				<?php endif;?>
				
				<?php if($user['facebook_page']):?>
				<div class="view4more-link"><a target="_blank" href="<?php echo $user['facebook_page'];?>"><?php echo translate_phrase("View Facebook Page");?></a></div>
				<?php endif;?>
				<div class="view4more-link"><a href="<?php echo base_url().'admin/order_history/'.$user['user_id'];?>"><?php echo translate_phrase("View Order History");?></a></div>
			</div>
		</div>

<!--		<div class="userBoxRight">
			<form action="<?php echo base_url().'admin/update_user';?>" method="post" id="form_<?php echo $user['user_id'];?>">
			<input type="hidden" name="user_id" value="<?php echo $user['user_id'];?>"/>
			<input type="hidden" name="current_tab" class="current_tab"  value="<?php echo $selectedTab;?>" />
			
			
			<div class="sfp-1-main">
				<div class="sfp-1-Left bold"><?php echo translate_phrase("Account Status");?></div>
				<div class="sfp-1-Right">
					<?php if($account_data):?>
						<?php foreach ($account_data as $account):?>
							<?php if($account['account_status_id'] == $user['account_status_id']):?>
								<a href="<?php echo base_url().'admin/change_user_account_status/'.$user['user_id'].'/'.$account['account_status_id']?>"><span class="appr-cen"><?php echo $account['description'];?></span></a>
							<?php else:?>
								<a href="<?php echo base_url().'admin/change_user_account_status/'.$user['user_id'].'/'.$account['account_status_id']?>"><span class="disable-butn"><?php echo $account['description'];?></span></a>
							<?php endif;?>
							
						<?php endforeach;?>
					<?php endif;?>					
				</div>
			</div>
			
			<div class="sfp-1-main">
				<div class="sfp-1-Left bold"><?php echo translate_phrase("Attractiveness Level");?></div>
				<div class="sfp-1-Right">
					<div class="f-decr chooseImportance">
						<ul>
							<li class="<?php echo ($user['attractiveness_level'] == 1)?'Intro-Button-sel':'Intro-Button'; ?>"><a importanceVal="1" href="javascript:;" ><?php echo translate_phrase("Regular");?></a></li>
							<li class="<?php echo ($user['attractiveness_level'] == 2)?'Intro-Button-sel':'Intro-Button'; ?>"><a importanceVal="2" href="javascript:;" ><?php echo translate_phrase("Select");?></a></li>
							<li class="<?php echo ($user['attractiveness_level'] == 3)?'Intro-Button-sel':'Intro-Button'; ?>"><a importanceVal="3" href="javascript:;" ><?php echo translate_phrase("Elite");?></a></li>							
						</ul>
						<input name="attractiveness_level" value="<?php echo $user['attractiveness_level'];?>" type="hidden">
					</div>						
				</div>
			</div>
			
			<div class="sfp-1-main">
				<div class="sfp-1-Left bold"><?php echo translate_phrase("No of Date Tickets");?></div>
				<div class="sfp-1-Right"><input style="width:50px;" type="text" name="num_date_tix" class="Degree-input" value="<?php echo $user['num_date_tix'] ?>"/></div>
			</div>
			
			<div class="multipleSelectTag">
				<?php if($membership_options):?>
				<ul class="cms-list btn-expiry-list">
					<li>
						<div class="bnt-column bold"><?php echo translate_phrase("Account Upgrade");?></div>
						<div class="input-column bold"><?php echo translate_phrase("Expiry Date");?></div>
					</li>
					<?php 
					$user_choose_package_ids = array();
					foreach($membership_options as $membership_option):?>
						<?php $is_member_display = 0; ?>
						
						<?php if($user['user_membership_option']):?>
							<?php foreach($user['user_membership_option'] as $user_pkg):?>
							<?php if($membership_option['membership_option_id'] == $user_pkg['membership_option_id']):?>
								<li><a href="javascript:;" class="appr-cen" key="<?php echo $membership_option['membership_option_id']?>"><?php echo $membership_option['description'];?></a>
								<input type="text" class="Degree-input calendar" name="expiry_date_<?php echo $membership_option['membership_option_id'];?>" value="<?php echo date('m/d/Y',strtotime($user_pkg['expiry_date']));?>"/>
								<img class="cal_ico" src="<?php echo base_url().'assets/images/calendar.png'?>"/></li>
							<?php $user_choose_package_ids[] =  $user_pkg['membership_option_id'] ;
							$is_member_display = 1; 
							endif;?>
							<?php endforeach;?>
						<?php endif;?>
						
						<?php if($is_member_display == 0):?>
							<li><a href="javascript:;" class="disable-butn" key="<?php echo $membership_option['membership_option_id']?>"><?php echo $membership_option['description'];?></a>
								<input type="text" class="Degree-input calendar" name="expiry_date_<?php echo $membership_option['membership_option_id'];?>" value=""/>
								<img class="cal_ico" src="<?php echo base_url().'assets/images/calendar.png'?>"/></li>
						<?php ?>
						<?php endif;?>
						
					<?php endforeach;?>
				</ul>
				<input name="membership_options" value="<?php echo $user_choose_package_ids?implode(',', $user_choose_package_ids):'';?>" type="hidden">
				<?php endif;?>
				
			</div>
			
			<div class="bnt-column">
				<button type="button" class="btn btn-blue fr" onclick="update_user(<?php echo $user['user_id'];?>)"><?php echo translate_phrase("Update");?></button>
			</div>
			</form>
		</div>	-->							
	</div>
</div>
