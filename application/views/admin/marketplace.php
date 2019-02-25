<!-- Added By Jigar Oza-->
<?php if(isset($totalRecords)) {?>
<input type="hidden" class="totalResultCount" value="<?php echo count($totalRecords);?>" />
<?php }?>
<?php if(isset($users) && !empty($users)):?>
<?php foreach($users as $user):?>
<div class="userBox" lang="<?php echo $user['user_id'];?>">
<div class="userBox-wrap">
	<div class="userTopRow">
		<div class="userTopRowHed"><?php echo $user['first_name'].' '.$user['last_name'];?> (<?php echo $user['name'];?>)
			(Last Active: <?php echo date(DATE_FORMATE,strtotime($user['last_active_time']));?>)
			<?php if($user['facebook_id'] > 0):?>
				(Facebook)
				<!--(Facebook ID: <?php echo $user['facebook_id'];?>)-->
			<?php endif;?>
		</div>		
	</div>										
	<div class="userTop ">
		<div class="userBoxLeft">
			<?php if(isset($user['primary_photo']) && $user['primary_photo']):?>
			<div class="img-left-box">
				<div class="img-slide">
					<a href="<?php echo base_url().'admin/go_profile/'.$user['user_id'].'?url='.base_url() . url_city_name().'/user_info/'.$this->utility->encode($user['user_id']);?>">
					<img style="width: 204px" src="<?php echo $user['primary_photo'];?>" alt="<?php echo $user['first_name']?>'s photo">
					</a>
				</div>
			 </div>
			<?php endif;?>
			<div class="sml-img">
				<div class="userTopRowHed"><a href="<?php echo base_url().'admin/go_profile/'.$user['user_id'].'?url='.base_url() . url_city_name().'/user_info/'.$this->utility->encode($user['user_id']);?>"><span class="appr-cen"><?php echo translate_phrase("View Profile");?></span></a></div>				
			</div>
		</div>
<!-- The right box -->
		<div class="userBoxRight">
                        <h3>
                            <span class="DarkGreen-color">
                                <b><?php echo $user['credits_value'] ?> <?php echo $user['credits_value'] == 1   ? translate_phrase('credit'): translate_phrase('credits')?></b>
                            </span>
                        </h3>                        
                    
			<form action="<?php echo base_url().'admin/process_date_request';?>" method="post" id="form_<?php echo $user['user_id'];?>">
			<input type="hidden" name="requested_match_user_id" value="<?php echo $user['user_id'];?>"/>
			<input type="hidden" name="current_tab" class="current_tab"  value="<?php echo $selectedTab;?>" />
			
			
			<div class="sfp-1-main">
				<div class="sfp-1-Left bold"><?php echo translate_phrase("Your member to match with");?> <?php echo ucwords($user['first_name'].' '.$user['last_name']);?></div>
				
                                <div class="sfp-1-Right">
					<dl class="dropdown-dt domaindropdown common-dropdown">
                                            <dt>
                                            <a href="javascript:;" key=""><span class="y-overflow-hidden"><?php echo translate_phrase('Please Select'); ?>
                                                </span> </a> <input type="hidden" name="m_user_id" id="m_user_id" value="">
                                            </dt>
                                            <dd>
                                                <ul>                                                
                                                    <?php foreach ($thisWebsiteUsers as $key => $value):
                                                        if($user['gender_id']!=$value['gender_id']):
                                                        ?>
                                                        <li>
                                                            <a href="javascript:;" key="<?php echo $value['user_id'] ?>">
                                                                <?php echo $value['first_name'].' '.$value['last_name']; ?>
                                                            </a>
                                                        </li>
                                                    <?php endif;
                                                    endforeach; ?>
                                                   <li>
                                                </ul>
                                            </dd>
                                        </dl>					
				</div>
			</div>
			
			
			
			<div class="sfp-1-main">
				<div class="sfp-1-Left bold"></div>
				<div class="sfp-1-Right">
                                    <button type="submit" class="btn btn-blue left" style="height: 36px;"><?php echo translate_phrase("Request Date");?></button>
                                </div>
			</div>									
			</form>
                        <div class="userTopRowTxt">
                            <?php echo translate_phrase('You will only be charged after').' '.$user['first_name'].' '.translate_phrase('goes on date with your selected memeber')  ?>
			</div>                        
		</div>								
	</div>
</div>
    </div>
<?php endforeach;?>
<?php endif;?>
