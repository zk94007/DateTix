<div class="datesArea bor-none">
    <?php if ($cancelDates): ?>
        <?php
        foreach ($cancelDates as $key => $date_info):
            $date_id = $date_info['date_id'];
            ?>
            <div class="dateRow grayHoverBox" id="date_<?php echo $date_info['date_id']; ?>" lang="<?php echo $date_info['date_id']; ?>">
                <div class="userBoxLeft user-photo-slider" id="photo_slider_<?php echo $date_info['date_id']; ?>">				
                    <?php if ($date_info['user_photos']): ?>
                        <a href="<?php echo base_url(); ?>dates/view_date/<?php echo $date_info['date_id']; ?>">                        
                            <div class="txt-center mar-top2">                            
                                <?php
                                $iid = $date_info['user_photos']['user_id'];
                                $pic = $date_info['user_photos']['photo'];
                                $url = base_url() . "user_photos/user_$iid/" . $pic;
                                ?>
                                <img style="height: 140px;width:90%" src="<?php echo $url ?>" alt="<?php echo $date_info['user_photos']['photo'] ?>" />
                            </div>
                        </a>
                    <?php else: ?>
                        <?php echo translate_phrase('No photos added yet'); ?>
                    <?php endif; ?>	
                </div>	
                <div class="userBoxRight">
                    <?php if ($key != 0): ?>
                        <div class="divider"></div>
                    <?php endif; ?>
                    <p><?php //echo $date_info['date_id'];  ?></p>

                    <div class="mar-top2">
                        <div class="myDate-100 column-100 ">
                            <div class="userbox-innr">
                                <div class="userbox-left-txt">
                                    <a href="<?php echo base_url(); ?>dates/view_date/<?php echo $date_info['date_id']; ?>" class="MyDateHeading">
                                        <?php echo print_date_day($date_info['date_time']) . ' ' . translate_phrase('at') . ' ' . date('h:i A', strtotime($date_info['date_time'])); ?> - 
                                        <?php echo $date_info['date_type']; ?> @ <?php echo $date_info['name']; ?>
                                    </a>
                                </div>
                                <p class="font-italic"><?php echo translate_phrase('Hosted By ') . $date_info['hosted_by_name']; ?></p>



                            </div>	
                        </div>

                        <div class="column-100">

                            <?php if ($date_info['requested_user_id'] == $this->user_id && ($date_info['total_applications'])): ?>
                                <p class="bold">
                                    <a href="#viewPopup_<?php echo $date_id ?>" class="viewLink">
                                        <?php echo $date_info['total_views'] . ' ' . translate_phrase('viewed'); ?> , <?php echo $date_info['total_applications'] . ' ' . translate_phrase('applied'); ?>
                                    </a>
                                </p>
                                <a href="#viewPopup_<?php echo $date_info['date_id'] ?>" class="viewLink">
                                    <span class="appr-cen btn-blue date-btn"><?php echo translate_phrase('View Applicants') ?></span>
                                </a>
                            <?php else: ?>
                                <p class="bold" ><?php echo $date_info['total_views'] . ' ' . translate_phrase('viewed'); ?> , <?php echo $date_info['total_applications'] . ' ' . translate_phrase('applied'); ?></p>
                            <?php endif; ?>

                            <div>
                                
                                            
                               


                            </div>
                        </div>

                        <div id="viewPopup_<?php echo $date_info['date_id'] ?>" style="display: none; width: 100%" >
                            <?php
                            $data['date_info'] = $this->model_date->get_date_detail_by_id($date_id);
                            $data['date_applications'] = $this->model_date->get_applicants_by_date_id($date_id);
                            echo $this->load->view('user/dates_app/view_applicants', $data);
                            ?>
                        </div>

                        
                    </div>

                </div>		
                

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p><span class="no-rows"><?php echo translate_phrase('You currently have no expire dates.') ?></span></p>
        <?php endif; ?>	
</div>