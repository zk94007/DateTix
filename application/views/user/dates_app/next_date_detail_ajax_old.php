<?php if(!empty($other_dates)):?> 

<?php foreach($other_dates as $date_info):?>
	<div class="dates-details">
		<div class="dateRow">		
			<div class="userBoxLeft">
				<?php $profileurl=base_url() . 'user/user_info/' . $this->utility->encode($date_info['requested_user_id']) . '/' . $this->utility->encode($user_data['user_id']) . '/' . $user_data['password']; ?>
	            <a href="<?php echo $profileurl;?>">
	                <img class="img-circle user-img" alt="img"  src="<?php echo base_url();?>user_photos/user_<?php echo $date_info['requested_user_id'];?>/<?php echo $date_info['user_photo'];?>">
	            </a>
	            
				<div class="astro-line txt-center">
					<div class="astro <?php echo $date_info['user_gender']==1?'male':'female'?>"><span></span><?php echo $date_info['user_age']?></div>
					<?php if($date_info['birth_date']):?>
						<?php echo getStarSign(strtotime($date_info['birth_date']));?>
					<?php endif;?>										
				</div>
				
			</div>
			<div class="userBoxRight">
				<div class="box-line">
					<div class="fl bold-txt"><?php echo $date_info['first_name']." ".$date_info['last_name'];?></div>
					<div class="fr normal-text"><?php echo translate_phrase('Active').' '.time_elapsed_string($date_info['user_last_active_time']);?></div>								
				</div>
				<div class="box-line "><span class="dt-icon icon-time"></span> <?php echo print_date_day($date_info['date_time']).' '.translate_phrase('at').' '.date('h:i A', strtotime($date_info['date_time']));?></div>
				<div class="box-line">
					<div class="column-50"><span class="dt-icon icon-datetype"></span><?php echo $date_info['date_type'];?></div>
					<div class="column-50"><span class="dt-icon icon-pay"></span> <?php echo $date_info['date_payer'];?></div>							
				</div>
				<div class="box-line">
                                    <?php
                                        $gg=($date_info['gender']=='1') ? 'girl' : 'guy' ;
                                    ?>
					<div class="jen-name-pink font-18"><?php echo translate_phrase('Looking for ').$gg.translate_phrase(' for '). $date_info['intention_type'];?></div>
				</div>
			</div>
		</div>
		<div class="venue-info">
			<div class="head-option">
<!--                                <a href="#" class="fl dt-btn btn-radius pink-border"><span class="dt-icon icon-user-plus"></span> Enrol</a>-->
				<a href="#" class="fr dt-btn"><span class="dt-icon icon-book"></span> 2</a>
				<a href="#" class="fr dt-btn"><span class="dt-icon icon-usr-group"></span> 0</a>
				
			</div>
			<?php if(isset($date_info['merchant_photos']) && $date_info['merchant_photos']):?>
			
			<div class="photosection flexslider">
				<ul class="slides">
					
					<?php foreach($date_info['merchant_photos'] as $photos):?>
					<li>
						<img src="<?php echo $photos['photo_url']?>" />	
						<p class="flex-caption"><span class="dt-icon icon-marker"></span> <?php echo $date_info['name'];?></p>	
					</li>
					<?php endforeach;?>								
				</ul>
			</div>
			<?php endif;?>
		</div>
		<div class="box-buttons grey-bg">						
			<a class="small round" href="javascript:;" onclick="getNextDate()"><span class="dt-icon icon-ban"></span></a>
			<a class="big round" href="javascript:;" onclick="userDatePreference('<?php echo $date_info['date_id']?>',0,this)" ><span class="dt-icon icon-cancel"></span></a>
			<a class="big round" href="javascript:;" onclick="userDatePreference('<?php echo $date_info['date_id']?>',1,this)"><span class="dt-icon icon-like"></span></a>
			<a class="small round" href="javascript:;" onclick="getNextDate()"><span class="dt-icon icon-refresh"></span></a>						
		</div>
		<div class="divider Mar-bottom-5"></div>
		<div class="sfp-1-main">
			<div class="sfp-1-Left">
				<span class="Black-color"><?php echo translate_phrase('Date tickets to use');?></span>
			</div>
			<div class="sfp-1-Right">
				<div class="f-decr importance">
					<ul>
						<li class="Intro-Button-sel"><a importanceVal="10" href="javascript:;" >10</a></li>
						<li><a class="Intro-Button" importanceVal="20" href="javascript:;" >20</a></li>
						<li><a class="Intro-Button" importanceVal="50" href="javascript:;" >50</a></li>
						<li><a class="Intro-Button" importanceVal="100" href="javascript:;" >100</a></li>					
						<li><a class="Intro-Button" importanceVal="200" href="javascript:;" >200</a></li>
					</ul>
					<input name="num_date_tickets" id="num_date_tickets" type="hidden" value="10">				
				
                                        <div class="mar-left2 right">
<!--                                                <a href="#" class="Intro-Button-sel btn-blue bordernone" onclick="return dateFilterPopup()">
                                                    Filter
                                                </a>-->
                                                <a href="#" class="btn btn-gray Mar-bottom-5" onclick="return dateFilterPopup()"> 
                                                    <span class="disable-butn btn-pink">Filter</span> </a>
                                        </div>
                                </div>
			</div>
		</div>
	</div>
<?php endforeach;?>
<?php else:?>
<div class="dates-details">
	<div class="dateRow">
		<p class="message success"><?php echo translate_phrase("There are no new dates for you in this city. Click the Host Date below to post your own date and get others to apply to meet you!");?></p>
	</div>
	<div class="dt-btn-group center mar-top2">
		<a href="<?php echo base_url('dates/new_date_step1');?>" class="dt-btn dt-btn-big dt-btn-red btn-animate"><?php echo translate_phrase('Host Date');?></a>
    </div>
</div>
<?php endif;?>