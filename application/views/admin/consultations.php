<?php if(isset($b2b) && !empty($b2b)): ?>
<?php foreach($b2b as $b2bData):?>
<div class="userBox" lang="<?php echo $b2bData['user_date_id'];?>">
<div class="userBox-wrap">
	<div class="userTopRow">
		<div class="userTopRowHed">
                    
                    <?php if($b2bData['date_accepted_time'] != '0000-00-00 00:00:00' && $b2bData['date_accepted_time'] != NULL):?>
                            <h3>
                                <span class="DarkGreen-color">
                                   <?php  echo translate_phrase('Consultations Request Accepted');?>
                                </span>
                                <?php echo translate_phrase('on').' '.date('d M Y',strtotime($b2bData['date_accepted_time'])) ?>
                                - <?php echo date('H : i : s',strtotime($b2bData['date_accepted_time'])) ?>
                            </h3>
                    <?php elseif($b2bData['date_declined_time'] != '0000-00-00 00:00:00' && $b2bData['date_declined_time'] != NULL):?>
                                <h3>
                                    <span class="DarkGreen-color">
                                       <?php  echo translate_phrase('Consultations Request Declined');?>
                                    </span>
                                    <?php echo translate_phrase('on').' '.date('d M Y',strtotime($b2bData['date_accepted_time'])) ?>
                                    - <?php echo date('H : i : s',strtotime($b2bData['date_accepted_time'])) ?>
                                </h3>
                    <?php else:?>
                            <h3>
                                <span class="DarkGreen-color">
                                   <?php  echo translate_phrase('Consultations Request Received');?>
                                </span>
                                <?php echo translate_phrase('on').' '.date('d M Y',strtotime($b2bData['date_suggested_time'])) ?>
                                - <?php echo date('H : i : s',strtotime($b2bData['date_suggested_time'])) ?>
                            </h3>
                    <?php endif;?>
		</div>	
            
            
                <?php if(($b2bData['date_accepted_time'] ==   '0000-00-00 00:00:00' || $b2bData['date_accepted_time'] == NULL) && ($b2bData['date_declined_time'] ==   '0000-00-00 00:00:00' || $b2bData['date_declined_time'] == NULL)):?>
                <div class="userTopRowHed" id="<?php echo $b2bData['user_date_id'];?>-requestsActionButtons">
                    <a href="javascript:;" onclick="consultationsAction(this)" data-request-action="accept" data-b2b-id="<?php echo $b2bData['user_date_id'];?>" >
                        <span class="appr-cen">
                            <?php echo translate_phrase("Accept");?>
                        </span>
                    </a>        
                    <a href="javascript:;" onclick="consultationsAction(this)" data-request-action="decline" data-b2b-id="<?php echo $b2bData['user_date_id'];?>" class="disable-butn" >
                        <?php echo translate_phrase('Decline') ?>
                    </a>                                        
                </div>
                <?php elseif($b2bData['date_cancelled_time'] != NULL && $b2bData['date_cancelled_time'] != '0000-00-00 00:00:00'):?>
                <div class="userTopRowHed" id="<?php echo $b2bData['user_date_id'];?>-requestsActionButtons">                                        
                    <a href="javascript:;" onclick="consultationsAction(this)" data-request-action="cancel" data-b2b-id="<?php echo $b2bData['user_date_id'];?>" id="" class="disable-butn " >
                        <?php echo translate_phrase('Cancel') ?>
                    </a>                                        
                </div>
                <?php elseif($b2bData['date_declined_time'] != NULL && $b2bData['date_declined_time'] != '0000-00-00 00:00:00'):?>
                <div class="userTopRowHed" id="<?php echo $b2bData['user_date_id'];?>-requestsActionButtons">                                        
<!--                    <a href="javascript:;" onclick="consultationsAction(this)" data-request-action="decline" data-b2b-id="<?php echo $b2bData['user_date_id'];?>" id="" class="disable-butn " >
                        <?php //echo translate_phrase('Declined') ?>
                    </a>                                        -->
                </div>
                <?php endif; ?>
            
            
            
            
                <div class="userTopRowHed" id="<?php echo $b2bData['user_date_id'];?>-msg-area">
                    <h4>            
                       
                        
                            <?php if($b2bData['date_accepted_time'] != NULL && $b2bData['date_accepted_time']!='0000-00-00 00:00:00'): ?>    
                                <span class="DarkGreen-color">
                                    <?php echo translate_phrase('ACCEPTED') ?>
                               
                                </span>                                                                        
                        
                            <?php elseif($b2bData['date_declined_time'] != NULL && $b2bData['date_declined_time']!='0000-00-00 00:00:00'): ?>    
                                <span class="Red-color">
                                    <?php echo translate_phrase('DECLINED') ?>
                                </span>
                            
                            <?php else:?> 
                                <span class="DarkGreen-color"></span>
                                <span class="Red-color"></span>
                            <?php endif; ?>
                        </span>                                                                        
                    </h4>                    
                </div>
	</div>										
	<div class="userTop ">	
            <div class="userTopRowHed">
                <h4> 
                    <?php if($b2bData['date_accepted_time'] != NULL && $b2bData['date_accepted_time']!='0000-00-00 00:00:00'):                        
                                echo translate_phrase('Requested by ').ucfirst($b2bData['sent_user']) ." (+".$b2bData['sent_country_code']." ".$b2bData['sent_user_mobile'].") ".translate_phrase(' to date with '). $b2bData['received_user']." (+".$b2bData['received_country_code']." ".$b2bData['received_user_mobile'].")";
                        else:
                                echo translate_phrase('Requested by ').ucfirst($b2bData['sent_user'])." ".translate_phrase(' to date with '). $b2bData['received_user'];                        
                        endif;
                    ?>
                </h4>
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
                                                 <a href="<?php echo base_url().'admin/go_profile/'.$b2bData['date_suggested_by_user_id'].'?url='.base_url() . url_city_name().'/user_info/'.$this->utility->encode($b2bData['date_suggested_by_user_id']); ?>">
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
                                                 <a href="<?php echo base_url().'admin/go_profile/'.$b2bData['received_user_id'].'?url='.base_url() . url_city_name().'/user_info/'.$this->utility->encode($b2bData['received_user_id']); ?>">
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
