<style>
.scemdowndomain dd, .scemdowndomain dt, .scemdowndomain ul{width:300px !important;}
.scemdowndomain a, .scemdowndomain a:visited{width:300px !important;}
.scemdowndomain dt a span{width:300px!important}
</style>
    <div class="datesArea bor-none">
    <?php if ($pastDates): ?>
        <?php
        foreach ($pastDates as $key => $date_info):
            $date_id = $date_info['date_id'];
            ?>
       
            <div class="dateRow grayHoverBox" id="date_<?php echo $date_info['date_id']; ?>" lang="<?php echo $date_info['date_id']; ?>">
                <div class="userBoxLeft" id="photo_slider_<?php echo $date_info['date_id']; ?>">				
                    <?php if ($date_info['user_photos']): ?>
                        <a href="<?php echo base_url(); ?>dates/view_date/<?php echo $date_info['date_id']; ?>">                        
                                                    
                                <?php
                                $iid = $date_info['user_photos']['user_id'];
                                $pic = $date_info['user_photos']['photo'];
                                $url = base_url() . "user_photos/user_$iid/" . $pic;
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

                    <div>
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

                        <div class="column-100">

                            <?php if ($date_info['requested_user_id'] == $this->user_id && ($date_info['total_applications'])): ?>
                                <p class="bold">
                                    <a href="#viewPopup_<?php echo $date_id ?>" class="viewLink">
                                        <?php echo $date_info['total_views'] . ' ' . translate_phrase('viewed'); ?> , <?php echo $date_info['total_applications'] . ' ' . translate_phrase('applied'); ?>
                                    </a>
                                </p>
                                <a href="#viewPopup_<?php echo $date_info['date_id'] ?>" class="viewLink">
                                    <span class="appr-cen btn-blue"><?php echo translate_phrase('View Applicants') ?></span>
                                </a>
                            <?php else: ?>
                                <p class="bold" ><?php echo $date_info['total_views'] . ' ' . translate_phrase('viewed'); ?> , <?php echo $date_info['total_applications'] . ' ' . translate_phrase('applied'); ?></p>
                            <?php endif; ?>

                            <div>
                                <?php
                                        $getUserRating=$this->model_date->check_rating_for_date($date_info['date_id'],$this -> user_id);
                                        if(!empty($getUserRating)){
                                ?>
                                    <a href="#dateEditReview_<?php echo $date_info['date_id']; ?>" class="reviewLink inline-element" style="vertical-align: middle"><span class="appr-cen"><?php echo translate_phrase('Edit Rating'); ?></span></a>
                                
                                <?php } else  { ?>
                                            <a href="#dateReview_<?php echo $date_info['date_id']; ?>" class="reviewLink inline-element" style="vertical-align: middle"><span class="appr-cen"><?php echo translate_phrase('Rate'); ?></span></a>
                                <?php }?>
                                            
                                <?php if ($date_info['requested_user_id'] == $this->user_id): ?>
                                            
                                            <?php if($date_info['status']=='2'):?>
                                                
                                                    <span class="Red-color inline-element" style="vertical-align: middle"> Refund requested</span>
                                            <?php else:?>
                                                <a href="#dateRefund_<?php echo $date_info['date_id']; ?>" class="refundLink">
                                                    <span class="disable-butn">
                                                        <?php echo translate_phrase('Request Refund') ?>
                                                    </span>
                                                </a>
                                            <?php endif;?>
                                            	
                                <?php endif; ?>


                            </div>
                        </div>

                        <div id="viewPopup_<?php echo $date_info['date_id'] ?>" style="display: none; width: 100%" >
                            <?php
                            $data['date_info'] = $this->model_date->get_date_detail_by_id($date_id);
                            $data['date_applications'] = $this->model_date->get_applicants_by_date_id($date_id);
                            echo $this->load->view('user/dates_app/view_applicants', $data);
                            ?>
                        </div>

                        <div id="dateRefund_<?php echo $date_info['date_id'] ?>" style="display: none; width: 100%;height: 200px;" >
                            <?php echo form_open('dates/date_refund'); ?>
                            <input type="hidden" name="date_id" value="<?php echo $date_info['date_id']; ?>"/>
                            
                            <h2><?php echo translate_phrase('Please specify reason for refund'); ?></h2>
                            <div class="column-100">
                                <?php 
                                $refundArray=array("Date cancelled one me"=>"Date cancelled one me",
                                                    "Date didn't show up"=>"Date didn't show up",
                                                    "Date didn't behave appropriately"=>"Date didn't behave appropriately",
                                                    "Just unhappy with date"=>"Just unhappy with date",
                                                    "Other reasons"=>"Other reasons");
                                //echo form_dt_dropdown('refund_reason', $refundArray, '', 'id="date_refund_reason" class="dropdown-dt scemdowndomain menu-Rightmar" style="width:350px"', translate_phrase('Select Refund Reason'), "hiddenfield"); ?>	
                                <?php foreach($refundArray as $k=>$v):?>
                                <a href="javascript:;" class="rdo_div"
                                        key="<?php echo $v; ?>">                                    
                                        <span class="disable-butn"><?php echo $v; ?></span>                                    
                                </a>
                                <?php endforeach;?>
                                <input type="hidden" id="refund_reason" name="refund_reason" value="">
<!--                                <select name="date_refund_reason" id="date_refund_reason">
                                    <option value='Date cancelled one me'>Date cancelled one me</option>
                                    <option value="Date didn't show up">Date didn't show up</option>
                                    <option value="Date didn't behave appropriately">Date didn't behave appropriately</option>
                                    <option value="Just unhappy with date">Just unhappy with date</option>
                                    <option value="Other reasons">Other reasons</option>
                                </select>-->

                            </div>

                            <div class="column-100 " style="margin-top: 1%" >
                                <div class="Nex-mar mar-top2">
                                    <input id="submit_button" type="submit" class="btn btn-blue date-btn" value="<?php echo translate_phrase('Ok') ?>">
                                    &nbsp;
                                    <input type="button" onclick="$.fancybox.close();" class="btn btn-gray disable-butn right date-btn" value="Cancel">
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>

                </div>		
                <div id="dateReview_<?php echo $date_info['date_id']; ?>" style="display:none">

                    <form name="ratingForm" id="ratingForm" method="post" action="<?php echo base_url();?>dates/date_review">
                        <input type="hidden" name="date_id" value="<?php echo $date_info['date_id']; ?>"/>
                        <div class="column-100">
                            <h4><?php echo translate_phrase('How would you rate this date ?'); ?><span style='color:#FF499A;font-weight: bold'> * </span></h4>
                            <span  class="star" data-score="0"></span>
                        </div>
                        <label id="ratingError_<?php echo $date_info['date_id']; ?>" class="input-hint error error_indentation error_msg"></label> 
                        <div class="column-100">
                            <h4><?php echo translate_phrase('Any other comments about date ?'); ?></h4>
                            <textarea name="date_comment" id="date_comment"></textarea>
                        </div>

                        <div class="column-100 " style='margin-top: 2%!important'>
                            <div class="Nex-mar mar-top2">
                                <input id="submit_button" type="submit" class="btn btn-blue date-btn" value="<?php echo translate_phrase('Ok') ?>" onclick="return checkReviewValidation('<?php echo $date_info['date_id']; ?>')">
                                &nbsp;<input type="button" onclick="$.fancybox.close();" class="btn btn-gray disable-butn right date-btn" value="Cancel">
                            </div>
                        </div>
                    </form>

                </div>
                
                <div id="dateEditReview_<?php echo $date_info['date_id']; ?>" style="display:none">

                    <form name="editRatingForm" id="editRatingForm" method="post" action="<?php echo base_url();?>dates/edit_date_review">
                        <input type="hidden" name="date_id" value="<?php echo $date_info['date_id']; ?>"/>
                        <input type="hidden" name="date_review_id" value="<?php echo @$getUserRating['date_review_id']; ?>"/>
                        <div class="column-100">
                            <h4><?php echo translate_phrase('How would you rate this date ?'); ?>
                                <span style='color:#FF499A;font-weight: bold'> * </span></h4>
                                <span  class="star" data-score="<?php echo @$getUserRating['rating'];?>"></span>
                        </div>
                        <label id="ratingEditError_<?php echo $date_info['date_id']; ?>" class="input-hint error error_indentation error_msg"></label> 
                        <div class="column-100">
                            <h4><?php echo translate_phrase('Any other comments about date ?'); ?></h4>
                            <textarea name="date_edit_comment" id="date_edit_comment"><?php echo @$getUserRating['review'];?></textarea>
                        </div>

                        <div class="column-100" style='margin-top: 2%!important'>
                            <div class="Nex-mar mar-top2">
                                <input id="submit_button" type="submit" class="btn btn-blue date-btn" value="<?php echo translate_phrase('Ok') ?>" onclick="return checkEditReviewValidation('<?php echo $date_info['date_id']; ?>')">
                                &nbsp;<input type="button" onclick="$.fancybox.close();" class="btn btn-gray disable-butn right date-btn" value="Cancel">
                            </div>
                        </div>
                    </form>

                </div>                
            </div>
        
        <?php endforeach; ?>
    <?php else: ?>
        <p><span class="no-rows"><?php echo translate_phrase('You currently have no expire dates.') ?></span></p>
        <?php endif; ?>	
</div>