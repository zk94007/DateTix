<?php if (!empty($other_dates)): ?> 

    <?php foreach ($other_dates as $date_info): ?>
        <div class="dates-details">
            <div class="dateRow">		
                <div class="userBoxLeft">
                    <?php
                        $content['user'] = $date_info;
                        $content['user']['user_id'] = $date_info['requested_user_id'];
                        $content['user_data'] = $user_data;
                        echo $this->load->view('user/include/common_top_left_section', $content);
                    ?>
                </div>
                <div class="userBoxRight">
                    <?php
                        $this->load->view('user/include/common_top_right_section', $content);
                    ?>
                </div>
            </div>
            <div class="Mar-bottom-5"></div>
            <div class="sfp-1-main">
                <div id="banContent" style="display:none">
                    <h2>Are you sure you want to pass on all dates from <?php echo $date_info['first_name']; ?>?</h2>
                    <div class="column-100">
                        <div class="Nex-mar mar-top2">
                            <input id="submit_button" type="submit" class="btn btn-blue date-btn" value="<?php echo translate_phrase('Ok') ?>" onclick="return banUser('<?php echo $date_info['date_id']; ?>', '<?php echo $date_info['requested_user_id']; ?>')">
                            &nbsp;<input type="button" onclick="$.fancybox.close();" class="btn btn-gray disable-butn right date-btn" value="Cancel">
                        </div>
                    </div>
                </div>
<!--                <div class="sfp-1-Right">
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
                                                                            <a href="#" class="Intro-Button-sel btn-blue bordernone" onclick="return dateFilterPopup()">
                                                                                Filter
                                                                            </a>
                            <a href="#" class="btn btn-gray Mar-bottom-5" onclick="return dateFilterPopup()" id="filterButton"> 
                                <span class="disable-butn btn-pink">Filter</span>
                            </a>
                        </div>
                    </div>
                </div>-->
            </div>

            <div>
                    <div class="box-buttons grey-bg mar-top2">	
                        <a class="small round" href="javascript:;" onclick="banUserDate()"><span class="dt-icon icon-ban"></span></a>
                        <a class="big round" href="javascript:;" onclick="userDatePreference('<?php echo $date_info['date_id'] ?>', 0, this)" ><span class="dt-icon icon-cancel"></span></a>
                        <a class="big round" href="javascript:;" onclick="userDatePreference('<?php echo $date_info['date_id'] ?>', 1, this)"><span class="dt-icon icon-like"></span></a>
                        <a class="small round" href="javascript:;" onclick="getNextDate()"><span class="dt-icon icon-refresh"></span></a>						
                        <div class="filter-btn-group">
                            <a href="#" class="disable-butn btn-pink" onclick="return dateFilterPopup()" id="filterButton"> Filter</a>
                        </div>      
                    </div>
                    
                    <div class="f-decr importance">
	                        <div class="sfp-1-Left mar-R align-left">
	                            <?php echo translate_phrase('Date tickets to use'); ?>
	                        </div>
	                        <ul>
	                            <li class="Intro-Button-sel"><a importanceVal="10" href="javascript:;" >10</a></li>
	                            <li><a class="Intro-Button" importanceVal="20" href="javascript:;" >20</a></li>
	                            <li><a class="Intro-Button" importanceVal="50" href="javascript:;" >50</a></li>
	                            <li><a class="Intro-Button" importanceVal="100" href="javascript:;" >100</a></li>					
	                            <li><a class="Intro-Button" importanceVal="200" href="javascript:;" >200</a></li>
	                        </ul>
	                        <input name="num_date_tickets" id="num_date_tickets" type="hidden" value="10">	
	                        
                    </div>
                    <div class="div-row">
                    	<div class="DarkGreen-color bold"><?php echo translate_phrase('More date tickets gets you ranked higher in the applicant list!');?></div>
                    </div>                    
            </div>
            
            <div class="venue-info" style="margin-top:0px">

                <?php if (isset($date_info['merchant_photos']) && $date_info['merchant_photos']): ?>			
                    <div class="photosection flexslider">
                        <ul class="slides">

            <?php foreach ($date_info['merchant_photos'] as $photos): ?>
                                <li>
                                    <p class="flex-caption">
                                        <span class="dt-icon icon-marker"></span><?php echo trim($date_info['name'], chr(0xC2).chr(0xA0));?>, <?php echo $date_info['merchant_neighborhood']['description']; ?>
                                    </p>	
                                    <img src="<?php echo $photos['photo_url'] ?>" />							
                                </li>
            <?php endforeach; ?>								
                        </ul>
                    </div>
        <?php endif; ?>
            </div>


        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="dates-details">
        <div class="dateRow Mar-bottom-5">
            <p class="message success"><?php echo translate_phrase("There are no new dates for you in this city. Click the Host Date below to post your own date and get others to apply to meet you, or click the Filter button to see other types of dates."); ?></p>
        </div>
        
    	<div class="aps-d-top text-center" style="margin:0 auto;">
            <a href="<?php echo base_url('dates/new_date_step1'); ?>" class="btn btn-pink"><?php echo translate_phrase('Host Date'); ?></a>            
            <a href="#" class="btn btn-pink" onclick="return dateFilterPopup()" id="filterButton"> Filter</a>
        </div>
    
    </div>
<?php endif; ?>