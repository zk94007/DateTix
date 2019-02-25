<?php if(isset($b2b) && !empty($b2b)): ?>
<?php foreach($b2b as $b2bData):?>
<div class="userBox" lang="<?php echo $b2bData['website_request_b2b_id'];?>">
<div class="userBox-wrap">
	<div class="userTopRow">
		<div class="userTopRowHed">
                    <h3>
                        <span class="DarkGreen-color">
                            <?php echo $b2bData['credits_value'].' '.translate_phrase(' credits ') ?><?php echo translate_phrase('intro request').' '.$b2bData['type'].' '?>
                        </span>
                        <?php echo translate_phrase('on').' '.date('d M Y',strtotime($b2bData['request_time'])) ?>
                        - <?php echo date('H : i : s',strtotime($b2bData['request_time'])) ?>
                    </h3>
		</div>	
                <?php if($b2bData['type'] == 'received' && $b2bData['status'] == ''):?>
                <div class="userTopRowHed" id="<?php echo $b2bData['website_request_b2b_id'];?>-requestsActionButtons">
                    <a href="javascript:;" onclick="requestAction(this)" data-request-action="accept" data-b2b-id="<?php echo $b2bData['website_request_b2b_id'];?>" data-sent-matchmaker-id="<?php echo $b2bData['sent_matchmaker_id'];?>" data-received-matchmaker-id="<?php echo $b2bData['received_matchmaker_id'];?>"  data-credits="<?php echo $b2bData['credits_value'];?>">
                        <span class="appr-cen">
                            <?php echo translate_phrase("Accept");?>
                        </span>
                    </a>                    
                    <a href="javascript:;" onclick="requestAction(this)" data-request-action="decline" data-b2b-id="<?php echo $b2bData['website_request_b2b_id'];?>" class="disable-butn" data-sent-matchmaker-id="<?php echo $b2bData['sent_matchmaker_id'];?>" data-received-matchmaker-id="<?php echo $b2bData['received_matchmaker_id'];?>"  data-credits="<?php echo $b2bData['credits_value'];?>">
                        <?php echo translate_phrase('Decline') ?>
                    </a>                                        
                </div>
                <?php endif; ?>
                <?php if($b2bData['type'] == 'sent' && $b2bData['status'] == ''):?>
                <div class="userTopRowHed" id="<?php echo $b2bData['website_request_b2b_id'];?>-requestsActionButtons">                                        
                    <a href="javascript:;" onclick="requestAction(this)" data-request-action="cancel" data-b2b-id="<?php echo $b2bData['website_request_b2b_id'];?>" id="" class="disable-butn " data-sent-matchmaker-id="<?php echo $b2bData['sent_matchmaker_id'];?>" data-received-matchmaker-id="<?php echo $b2bData['received_matchmaker_id'];?>" data-credits="<?php echo $b2bData['credits_value'];?>">
                        <?php echo translate_phrase('Cancel') ?>
                    </a>                                        
                </div>
                <?php endif; ?>
                <div class="userTopRowHed" id="<?php echo $b2bData['website_request_b2b_id'];?>-msg-area">
                    <h4>                        
                        <span class="DarkGreen-color">
                            <?php if($b2bData['status'] == 'accepted'): ?>    
                                <?php echo translate_phrase('ACCEPTED') ?>
                            <?php endif; ?>    
                        </span>                                                                        
                        <span class="Red-color">
                            <?php if($b2bData['status'] == 'declined'): ?>    
                                <?php echo translate_phrase('DECLINED') ?>
                            <?php endif; ?>        
                            
                            <?php if($b2bData['status'] == 'cancelled'): ?>    
                                <?php echo translate_phrase('CANCELLED') ?>
                            <?php endif; ?>
                        </span>                                                                        
                    </h4>                    
                </div>
	</div>										
	<div class="userTop ">	
            <div class="userTopRowHed">
                <h4> <?php echo translate_phrase('Bid for').' '.$b2bData['sent_user'].' ('.$b2bData['sent_website_name'].') '.translate_phrase('to match with').' '.$b2bData['received_user'].' ('. $b2bData['received_website_name'].') '?> </h4>
            </div>            
            <div class="profile-phot-M">                                
                     <div class="photo-slider-m">
                         <ul class="bxslider_profile">                                                                                                                 
                             <li class="photo_">
                                 <a class="upload-part fancybox" rel="gallery1" 
                                    href="<?php echo $b2bData['sent_user_photo'];?>">
                                     <?php if(isset($b2bData['sent_user_photo']) && $b2bData['sent_user_photo']):?>
                                     <img height="150" src="<?php echo $b2bData['sent_user_photo'];?>" alt="" /> 
                                     <?php else: ?>
                                     <img height="150" src="<?php echo base_url().'404.jpg';?>" /> 
                                     <?php endif;?>
                                 </a>
                                 <div class="Photo-dwn-btn">
                                     <div class="sml-img">
                                             <div class="userTopRowHed">
                                                 <a href="<?php echo base_url().'admin/go_profile/'.$b2bData['requested_match_user_id'].'?url='.base_url() . url_city_name().'/user_info/'.$this->utility->encode($b2bData['requested_match_user_id']); ?>">
                                                     <span class="appr-cen">
                                                         <?php echo translate_phrase("View Profile");?>
                                                     </span>
                                                 </a>
                                             </div>				
                                     </div>
                                 </div>
                             </li>                                                                                   
                             

                             <li class="photo_">
                                 <a class="upload-part fancybox" rel="gallery1" href="<?php echo $b2bData['received_user_photo'];?>">
                                     <?php if(isset($b2bData['received_user_photo']) && $b2bData['received_user_photo']):?>
                                        <img height="150" src="<?php echo $b2bData['received_user_photo'];?>" /> 
                                     <?php else: ?>   
                                        <img height="150" src="<?php echo base_url().'404.jpg';?>" /> 
                                     <?php endif;?>   
                                 </a>
                                 <div class="Photo-dwn-btn">
                                     <div class="sml-img">
                                             <div class="userTopRowHed">
                                                 <a href="<?php echo base_url().'admin/go_profile/'.$b2bData['requested_user_id'].'?url='.base_url() . url_city_name().'/user_info/'.$this->utility->encode($b2bData['requested_user_id']); ?>">
                                                     <span class="appr-cen">
                                                         <?php echo translate_phrase("View Profile");?>
                                                     </span>
                                                 </a>
                                             </div>				
                                     </div>
                                 </div>
                             </li>                                        
                         </ul>
                     </div>                                
             </div>						
	</div>
</div>
</div>
<?php endforeach;?>
<?php endif;?>
