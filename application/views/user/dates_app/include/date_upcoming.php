<?php if (isset($upcomingDates) && $upcomingDates): ?>
        <?php
        foreach ($upcomingDates as $key => $date_info):

            $date_id = $date_info['date_id'];
            ?>
            
            <div class="dateRow grayHoverBox" id="date_<?php echo $date_info['date_id']; ?>" lang="<?php echo $date_info['date_id']; ?>">
                <div class="userBoxLeft" id="photo_slider_<?php echo $date_info['date_id']; ?>">				
                    <?php 
                    if ($date_info['user_photos']): ?>
                        <a href="<?php echo base_url(); ?>dates/view_date/<?php echo $date_info['date_id']; ?>">                        
                                                      
                                <?php
                                
                                $other_user_id = $date_info['user_photos']['user_id'];
                                $pic = $date_info['user_photos']['photo'];
                                $url = base_url() . "user_photos/user_$other_user_id/" . $pic;
                                ?>
                                <img class="img-circle user-img" src="<?php echo $url ?>" alt="<?php echo $date_info['user_photos']['photo'] ?>" />
                            
                        </a>
                    <?php else: ?>
                        <?php echo translate_phrase('No photos added yet'); ?>
                    <?php endif; ?>	
                </div>	
                <div class="userBoxRight">
                    <?php if ($key != 0): ?>
                        <div class="divider"></div>
                    <?php endif; ?>

                    <div >
                        <div class="myDate-100 column-100 ">
                            <div class="userbox-innr">
                                <div>
                                    <a href="<?php echo base_url(); ?>dates/view_date/<?php echo $date_info['date_id']; ?>" class="MyDateHeading">
                                        <?php echo print_date_day($date_info['date_time']) . ' ' . translate_phrase('at') . ' ' . date('h:i A', strtotime($date_info['date_time'])); ?> - 
                                        <?php echo $date_info['date_type']; ?> @ <?php echo $date_info['name']; ?>
                                    </a>
                                </div>
                                <p class="font-italic"><?php echo translate_phrase('Hosted By ') . $date_info['hosted_by_name']; ?></p>

                            </div>	
                        </div>


                        <div id="viewPopup_<?php echo $date_id ?>" style="display: none; width: 100%" >
                            <?php
                            $data['date_info'] = $this->model_date->get_date_detail_by_id($date_id);
                            $data['date_applications'] = $this->model_date->get_applicants_by_date_id($date_id);
                            echo $this->load->view('user/dates_app/view_applicants', $data);
                            ?>
                        </div>


                        <div id="cancelPopup_<?php echo $date_id ?>" style="display: none; width: 100%" class="">

                            <?php if ($date_info['requested_user_id'] == $this->user_id): ?>

                                <h3 class="Top3-head"><?php echo translate_phrase('Are you sure you want to cancel this date?'); ?></h3>
                                <p class="div-row"><?php echo translate_phrase('We will notifiy all applicants that this date has been cancelled'); ?>.</p>
                                <div class="btn-group mar-top2">
                                    <?php echo form_open('dates/cancel_date'); ?>
                                    <input type="hidden" name="date_id" value="<?php echo $date_id ?>">
                                    <input type="submit" class="btn btn-blue date-btn" value="<?php echo translate_phrase('Ok') ?>">
                                    <input type="button" onclick="$.fancybox.close();" class="disable-butn bordernone date-btn" value="Cancel" style="float:none">									
                                    <?php echo form_close(); ?>
                                </div>

                            <?php else: ?>

                                <h3 class="Top3-head">
                                    <b>Are you sure you want to withdraw your application to this date?</b>
                                </h3>
                                <p class="div-row">
                                    You will no longer appear in this date's applicant list.
                                </p>
                                <div class="btn-group mar-top2">
                                    <?php echo form_open('dates/withdraw_applicant'); ?>
                                    <input type="hidden" name="date_id" value="<?php echo $date_id ?>">
                                    <input type="submit" class="btn btn-blue date-btn" value="<?php echo translate_phrase('Ok') ?>">
                                    <input type="button" onclick="$.fancybox.close();" class="disable-butn bordernone date-btn" value="Cancel" style="float:none">									
                                    <?php echo form_close(); ?>
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="colmn-100">
                            <?php if ($date_info['requested_user_id'] == $this->user_id && ($date_info['total_applications'])): ?>                                
                                <p class="bold">
                                    <a href="#viewPopup_<?php echo $date_id ?>" class="viewLink">
                                        <?php echo $date_info['total_views'] . ' ' . translate_phrase('viewed'); ?> , <?php echo $date_info['total_applications'] . ' ' . translate_phrase('applied'); ?>
                                    </a>
                                </p>
                                <a href="#viewPopup_<?php echo $date_id ?>" class="viewLink">
                                    <span class="appr-cen btn-blue date-btn"><?php echo translate_phrase('View Applicants') ?></span>
                                </a>
                            <?php else: ?>
                                <p class="bold" ><?php echo $date_info['total_views'] . ' ' . translate_phrase('viewed'); ?> , <?php echo $date_info['total_applications'] . ' ' . translate_phrase('applied'); ?></p>
                            <?php endif; ?>

                            <?php if ($date_info['requested_user_id'] == $this->user_id): ?>
                                <a href="<?php echo base_url('dates/edit_date/' . $date_id); ?>"><span class="appr-cen"><?php echo translate_phrase('Edit') ?></span></a>
                            <?php endif; ?>

                            <?php if ($date_info['requested_user_id'] == $this->user_id): ?>
                                <a href="#cancelPopup_<?php echo $date_id ?>" class="popupLink"><span class="disable-butn date-btn"><?php echo translate_phrase('Cancel'); ?></span></a>							                                
                            <?php else:?>
                            	
                                <?php  $chat_url = base_url('dates/chat_history/'.$this->utility->encode($date_info['requested_user_id']).'/'.$this->utility->encode($this->user_id))?>
                                <a href="<?php echo $chat_url?>" class="popupLink"><span class="disable-butn btn-blue"><?php echo translate_phrase('Chat'); ?></span></a>          
                                <a href="#cancelPopup_<?php echo $date_id ?>" class="popupLink"><span class="disable-butn date-btn"><?php echo translate_phrase('Withdraw'); ?></span></a>							                      
                            <?php endif;?>
                            

                        </div>
                    </div>

                </div>		
            </div>
        <?php endforeach; ?>        
<?php endif; ?>	