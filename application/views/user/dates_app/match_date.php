<?php $user_id = $this->session->userdata('user_id'); ?>
<div class="wrapper">
    <div class="content-part mobile-layout">
        <div class="Apply-Step1-a-main">
            <div class="step-form-Main">
				<div class="dateRow ">	
                    <h2 class="txt-center">
                        <?php 
							if($host_user_data['user_id'] == $user_id)
							{
								echo translate_phrase('You have  selected ').$applicant_user_data['first_name']. translate_phrase(' to be date on');
							}
							else
							{
								echo $host_user_data['first_name'].translate_phrase('has selected you to be date on');
							}
							
                        ?> 
                        <?php echo ' '.print_date_day($date_info['date_time']) .",".  date('h:i A', strtotime($date_info['date_time']))?>
                    </h2>
                        
                    <div class="infoRow">
                    	
                    	<div class="column-50">
                            <div class="userBox-wrap text-center">
                            <?php $profileurl = base_url() . 'user/user_info/' . $this->utility->encode($host_user_data['user_id']); ?>                                        
                            <a href="<?php echo $profileurl; ?>">
                                <img class="img-circle user-img" alt="img"  src="<?php echo base_url(); ?>user_photos/user_<?php echo $host_photo['user_id']; ?>/<?php echo $host_photo['photo']; ?>">
                            </a>
                            </div>
                            <div class="infoRowRightText txt-center"><?php echo $host_user_data['mobile_phone_number'];?></div>
                        </div>
                        <div class="column-50">
                        	 <div class="userBox-wrap text-center">
                            <?php $applicantprofileurl = base_url() . 'user/user_info/' . $this->utility->encode($applicant_user_data['user_id']); ?>
                                <a href="<?php echo $applicantprofileurl; ?>">
                                    <img class="img-circle user-img" alt="img"  src="<?php echo base_url(); ?>user_photos/user_<?php echo $applicant_photo['user_id']; ?>/<?php echo $applicant_photo['photo']; ?>">
                                </a>
                            </div>
                            <div class="infoRowRightText txt-center"><?php echo $applicant_user_data['mobile_phone_number'];?></div>
                        </div>
                    </div>
                    					
					<div class="dateBox">                              
						<h2 class="txt-center">You upcoming date details</h2>
						<br/>
						
                        <p class="font-18 box-line text-center">
                        	<strong> When : </strong><?php echo print_date_day($date_info['date_time']) .",".  date('h:i A', strtotime($date_info['date_time']))?>
                        </p>                                
                    	
                    	<p class="font-18 box-line text-center">
                        	<strong> What : </strong><?php echo $date_info['date_type'];?>
                        </p>                                
                    	
                    	
                    	<p class="font-18 box-line text-center">
                        	<strong> Why : </strong><?php echo $date_info['intention_type'];?>
                        </p>                                
                    	
                    	<?php 
                    	
                    	$address_arr = array();
                    	if($date_info['name'])
                    		$address_arr[] = $date_info['name'];
						if($date_info['address'])
                    		$address_arr[] = $date_info['address'];
						if($date_info['phone_number'])
                    		$address_arr[] = $date_info['phone_number'];
						
                    	
                    	if($address_arr):?>
                    	<p class="font-18 box-line text-center">
                        		<strong> Where : </strong>
                        	   <?php echo implode(', ',$address_arr);?>
                        </p>                                
                    	<?php endif;?>
                    </div>
                    <div class="infoRow">
                        
                        <?php if($date_info['requested_user_id']=$user_data['user_id']):?>
                           <div class="infoRowRightText main">
                            	<p class="Red-color bold">Remember to call the venue as soon as possible  to make your reservation!</p>
                           </div>
                        <?php endif;?>
                       <!-- <div class="btn-group mar-top2 left">
                        	<a href="javascript:;" class="btn btn-gray">                                    
	                            <span class="disable-butn">Call venue to book reservation</span>
	                        </a>	                        
                       </div>-->
                        <div class="infoRowRightText main">
                        	<p class="DarkGreen-color bold"> You may now call <?php echo $applicant_user_data['first_name'];?> or chat with  to confirm your date and discuss how to find each other</p>
                        </div>
                        
                    </div>
                    <div class="infoRow">
                        <div class="btn-group left">
                        	<a href="<?php echo base_url();?>dates/chat_history/<?php echo $this->utility->encode($applicant_user_data['user_id']);?>/<?php echo $this->utility->encode($this->user_id);?>">
                        		<span class="disable-butn">Chat to confirm date</span></a>
	                        <a href="#"><span class="disable-butn">Call to confirm date</span></a>	                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
