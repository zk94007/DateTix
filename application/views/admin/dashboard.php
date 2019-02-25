<script  type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.easytabs.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<script type="text/javascript">
    var review_app_page_no = "<?php echo $page_no ?>",
        manage_member_page_no = "<?php echo $page_no ?>",
        marketplace_page_no = 0,
        requests_page_no = 0;
    
    var preventAjaxCall = [];

    $(document).ready(function() {

        $('#active_expire').easytabs();

        $(".rdo_div").live('click', function() {
            $(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
            $(this).find('span').removeClass('disable-butn').addClass('appr-cen');
            $(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
        });

        $(".sort_dl dd ul li a, #btnReviewApp, #btnMember,#btnSearchMarketplaceApp").live('click', function() {
            if ($('#active_expire ul li.active a').attr('href') == '#review_app')
            {
                currentTab = 'review_app';
                review_app_page_no = '0';
            }

            if ($('#active_expire ul li.active a').attr('href') == '#manage_member')
            {
                currentTab = 'manage_member';
                manage_member_page_no = '0';
            }

            if ($('#active_expire ul li.active a').attr('href') == '#marketplace')
            {
                currentTab = 'marketplace';
                marketplace_page_no = '0';
            }

            if (preventAjaxCall.indexOf(currentTab) != '-1')
            {
                preventAjaxCall.pop(currentTab);
            }

            load_more_data('replace');
        });
        $(".city_dropdown dd ul li a").live('click', function() {
            $("#city_form").submit();
        });

        $('#active_expire').bind('easytabs:after', function(tab, panel, data) {
            $(".current_tab").val($(panel[0]).parent().attr('id'));
        });

        $('.importance ul li a').live('click', function(e) {
            e.preventDefault();
            var ele = jQuery(this);

            var parentUl = ele.parent().parent();
            var li = jQuery(parentUl).find('li.Intro-Button-sel');
            $(li).removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
            $(ele).parent().addClass('Intro-Button-sel').find('a').removeClass('Intro-Button');

            //set the hidden field value for this prefrence
            var selectedImportance = jQuery(parentUl).find('li.Intro-Button-sel a').attr('importanceVal');
            parentUl.parent().find('input[type="hidden"]').val(selectedImportance);
        });

        //click on send mail btn
        $(".send_mail").live('click', function() {
            var user_id = $(this).attr('lang');
            var subject = $(this).parent().siblings('.div-row').find(':input[name="subject"]').val();
            var body = $(this).parent().siblings('.div-row').find(':input[name="email_body"]').val();
            if (user_id)
            {
                var obj = $(this);
                loading();
                $.ajax({
                    url: base_url + "admin/send_mail_to_user/" + user_id,
                    type: "post",
                    data: {'subject': subject, 'body': body},
                    dataType: 'json',
                    success: function(response) {
                        stop_loading();
                        $(obj).parent().siblings('label').text(response.msg).addClass(response.type);
                    }
                });
            }
        });

        //Change Review Application Status
        $(".update_status").live('click', function() {

            var user_id = $(this).parent().attr('lang');
            var status = $(this).attr('lang');
            var body = $(this).parent().siblings('.div-row').find(':input[name="email_body"]').val();
            if (user_id)
            {
                var obj = $(this);
                loading();
                $.ajax({
                    url: base_url + "admin/change_user_status/" + user_id,
                    type: "post",
                    data: {'status': status, 'body': body},
                    dataType: 'json',
                    success: function(response) {
                        stop_loading();
                        $(obj).parent().siblings('label').text(response.msg).addClass(response.type);
                    }
                });
            }
        });
        
        jQuery('.r-statuses-rdo > ul > li > a').live('click',function(){            
            requests_page_no = 0;//rdo was changed.
            load_more_data('replace');
        })
    });

    function update_user(form_id)
    {
        $("#form_" + form_id).submit();
    }

    //Lazzy Load Pagination..
    $(window).scroll(function() {
        var totalScrollAmount = $(window).scrollTop() + $(window).height() + 80;
        if (totalScrollAmount >= $(document).height())
        {
            load_more_data('append')
        }
    });

    function load_more_data(domAction)
    {
        var currentTab = '';
        var offset = 1;
        var tab_order = '';
        var searchVal = '';
        var city_id = '';
        var PostData = '';

        city_id = $("#city_id").val();
        if ($('#active_expire ul li.active a').attr('href') == '#review_app')
        {
            currentTab = 'review_app';
            tab_order = $("#user_order").val();
            searchVal = $("#txtReviewApp").val();
            offset += parseInt(review_app_page_no);

            PostData = {'sort_by': tab_order, 'page_no': offset, 'search_txt': searchVal, 'city_id': city_id};

        }
        if ($('#active_expire ul li.active a').attr('href') == '#manage_member')
        {
            currentTab = 'manage_member';
            tab_order = $("#member_order").val();
            offset += parseInt(manage_member_page_no);

            var age_lower = $("#age_lower").val();
            var age_upper = $("#age_upper").val();

            PostData = {'sort_by': tab_order,
                'page_no': offset,
                'gender_id': $("#gender_id").val(),
                'age_lower': age_lower,
                'age_upper': age_upper,
                'ethnicity': $("#ethnicity").val(),
                'first_name': $("#first_name").val(),
                'last_name': $("#last_name").val(),
                'city_id': city_id
            };
        }

        if ($('#active_expire ul li.active a').attr('href') == '#marketplace')
        {
           
            currentTab = 'marketplace';
            tab_order = $("#marketplace_sort_order").val();            
            offset += parseInt(marketplace_page_no);
            
            var age_lower = $("#m_ageRangeLowerLimit").val();
            var age_upper = $("#m_ageRangeUpperLimit").val();

            PostData = {
                'sort_by': tab_order,
                'page_no': offset,
                'gender_id': $("#m_gender_id").val(),
                'age_lower': age_lower,
                'age_upper': age_upper,
                'ethnicity': $("#m_ethnicity").val(),
                'height_from':$("#m_heightRangeLowerLimit").val(),
                'height_to':$("#m_heightRangeUpperLimit").val()
            };           
        }

        if ($('#active_expire ul li.active a').attr('href') == '#requests')
        {
           
            currentTab = 'requests';            
            offset += parseInt(requests_page_no);
                        
            PostData = {                
                'page_no': offset,               
                'r_status_id': age_lower,                
            };           
        }

        if (preventAjaxCall.indexOf(currentTab) == -1 && isAjaxCallRunning == false)
        {
            $("#" + currentTab + "_container").append('<div class="div_data_loader"></div>');

            loading();            
            $.ajax({
                url: base_url + "admin/load_users/" + currentTab,
                type: "post",
                data: PostData,
                success: function(response) {
                    
                    stop_loading();
                    $("#" + currentTab + "_container").find('.div_data_loader').fadeOut();
                    if (domAction == 'replace')
                    {
                        $("#" + currentTab + "_container").html($(response).hide().fadeIn(2000));
                    }

                    if (domAction == 'append')
                    {
                        $("#" + currentTab + "_container").append($(response).hide().fadeIn(2000));
                    }

                    if ($.trim(response) != '')
                    {
                        console.log('tab ');
                        if (currentTab == 'review_app')
                        {
                            review_app_page_no = offset;
                        }

                        if (currentTab == 'manage_member')
                        {
                            manage_member_page_no = offset;
                        }
                        
                        if (currentTab == 'marketplace')
                        {
                            marketplace_page_no = offset;
                        }
                        
                        if (currentTab == 'requests')
                        {
                            requests_page_no = offset;
                        }
                        
                    }
                    else
                    {
                        preventAjaxCall.push(currentTab);
                        //alert('No more data')
                    }
                }
            });
        }
        else
        {
            console.log('Sorry No more Users ' + preventAjaxCall);
        }
    }
    function next_year_date()
    {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear() + 1;

        if (dd < 10) {
            dd = '0' + dd
        }

        if (mm < 10) {
            mm = '0' + mm
        }

        today = mm + '/' + dd + '/' + yyyy;
        return today;
    }


    function radio_select(inputId, inputVal, Obj)
    {
        $(Obj).parent().addClass('Intro-Button-sel').removeClass('Intro-Button');
        $(Obj).parent().siblings().removeClass('Intro-Button-sel').addClass('Intro-Button');
        $("#" + inputId).val(inputVal);

    }
    
    
    function requestAction(obj){
       if(typeof obj == 'object'){          
           var action =jQuery(obj).data('request-action'),b2bID = jQuery(obj).data('b2b-id');                                 
           jQuery.ajax({
               url: base_url+'admin/processRequestsAction',
               type:'POST',
               dataType : 'json',
               data:{action:action,b2bID:b2bID},
               success:function(response){                                     
                  if(action == 'cancel' || action == 'decline') {                     
                     jQuery('#'+b2bID+'-msg-area > h4 > span.Red-color').text(response.msg);  
                  }else{
                     jQuery('#'+b2bID+'-msg-area > h4 > span.DarkGreen-color').text(response.msg);   
                  }
                  
                  if(response.actionStatus == 'ok'){
                    jQuery('#'+b2bID+'-requestsActionButtons').remove();
                  }
               },
               error:function(){
                    alert('Some error occured');
               },
               complete:function(){
           
               }
           });
       } 
    }
</script>
<div class="wrapper">
    <div class="content-part">
        <div class="Apply-Step1-a-main">
            <div class="My-int-head">
                <h1><?php echo $page_title ?></h1>
                <div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg'); ?></div>
                <div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg'); ?></div>
            </div>
            <div class="emp-B-tabing-prt">
                <?php
                if ($cities):
                    $default_txt = translate_phrase('Select City');
                    foreach ($cities as $item) {
                        if ($item['city_id'] == $seleccted_city_id) {
                            $default_txt = $item['description'];
                        }
                    }
                    echo form_open('', array('id' => 'city_form'));
                    ?>
                    <div class="sortby bor-none Mar-top-none">
                        <div class="sortbyTxt"><?php echo translate_phrase("Select City"); ?>: </div>
                        <div class="sortbyDown">
                            <dl class="city_dropdown dropdown-dt animate-dropdown scemdowndomain menu-Rightmar" >
                                <dt>
                                <a href="javascript:;" key=""><span><?php echo $default_txt; ?></span> </a>
                                <input type="hidden" name="city_id" id="city_id" value="<?php echo $seleccted_city_id; ?>">
                                </dt>
                                <dd>
                                    <ul>
                                        <?php foreach ($cities as $item): ?>
                                            <li><a href="javascript:;" key="<?php echo $item['city_id']; ?>"><?php echo $item['description']; ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </dd>
                            </dl>						
                        </div>
                    </div>
                    <?php
                    echo form_close();
                endif;
                ?>

                <div class="emp-B-tabing-M-short" id="active_expire">
                    <ul class='etabs'>						
                        <?php if (!$is_review_resricted): ?>
                            <li class='tab tab-nav' id="review_appTAB"><span></span><a href="#review_app"><?php echo translate_phrase('Review Application'); ?></a></li>
                        <?php endif; ?>						
                        <li class='tab tab-nav' id="manage_memberTAB"><span></span><a href="#manage_member"><?php echo translate_phrase('Members'); ?></a></li>                            
                        <li class='tab tab-nav' id="marketplaceTAB_"><span></span><a href="#marketplace"><?php echo translate_phrase('Marketplace'); ?></a></li>                        
                        <li class='tab tab-nav' id="requestsTAB"><span></span><a href="#requests"><?php echo translate_phrase('Requests'); ?></a></li>                        
                    </ul>
                    <?php if (!$is_review_resricted): ?>
                        <div class="step-form-Main Mar-top-none Top-radius-none" id="review_app">
                            <div class="step-form-Part">

                                <div class="userTop  Mar-top-none Pad-BotAs3">
                                    <div class="sortbyTxt"> <?php echo translate_phrase('Sort by'); ?>: </div>
                                    <div class="sortbyDown">
                                        <dl class="sort_dl dropdown-dt domaindropdown common-dropdown">
                                            <dt>
                                            <a href="javascript:;" key="1"><span><?php echo translate_phrase('Application Date') . ' (' . translate_phrase('recent to old') . ')'; ?>
                                                </span> </a> <input type="hidden" name="user_order" id="user_order" value="1">
                                            </dt>
                                            <dd>
                                                <ul>
                                                    <li><a href="javascript:;" key="1"><?php echo translate_phrase('Application Date') . ' (' . translate_phrase('recent to old') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="2"><?php echo translate_phrase('Application Date') . ' (' . translate_phrase('old to recent') . ')'; ?></a></li>

                                                    <li><a href="javascript:;" key="3"><?php echo translate_phrase('Age') . ' (' . translate_phrase('young to old') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="4"><?php echo translate_phrase('Age') . ' (' . translate_phrase('old to young') . ')'; ?></a></li>

                                                    <li><a href="javascript:;" key="5"><?php echo translate_phrase('First Name') . ' (' . translate_phrase('A to Z') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="6"><?php echo translate_phrase('First Name') . ' (' . translate_phrase('Z to A') . ')'; ?></a></li>
                                                </ul>
                                            </dd>
                                        </dl>									
                                    </div>

                                    <div class="search-fields">
                                        <div class="input-wrap">
                                            <input id="txtReviewApp" class="Degree-input" type="text" style="height: 40px;">
                                            <label id="lblReviewApp" class="input-hint"></label>
                                        </div>
                                        <button type="button" id="btnReviewApp" class="btn btn-blue" style="height: 36px;"><?php echo translate_phrase('Search') ?></button>									
                                    </div>
                                </div>
                                <div id="review_app_container">
                                    <?php $this->load->view('admin/review_applications'); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="step-form-Main Mar-top-none Top-radius-none" id="manage_member">
                        <div class="step-form-Part">
                            <div class="userTop  Mar-top-none Pad-BotAs3">

                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left bold"><?php echo translate_phrase('Gender:') ?></div>
                                    <div class="sfp-1-Right">
                                        <div class="f-decr importance">
                                            <ul>
                                                <?php
                                                $gender_id = $this->input->post('gender_id');
                                                foreach ($gender as $row):
                                                    ?>
                                                    <li><a class="Intro-Button" href="javascript:;" importanceval="<?php echo $row['gender_id']; ?>"><?php echo $row['description']; ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <input type="hidden" name="gender_id" id="gender_id">											
                                        </div>
                                    </div>
                                </div>

                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left bold"><?php echo translate_phrase('between') ?></div>
                                    <div class="sfp-1-Right">
                                        <?php echo form_dt_dropdown('age_lower', $year, "", 'id="year" class="dropdown-dt"', translate_phrase('-'), "hiddenfield"); ?>
                                        <div class="centimeter"><?php echo translate_phrase('to') ?></div>
                                        <?php echo form_dt_dropdown('age_upper', $year, "", 'id="year" class="dropdown-dt"', translate_phrase('-'), "hiddenfield"); ?>
                                    </div>
                                </div>

                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left bold"><?php echo translate_phrase('Ethnicity') ?></div>
                                    <div class="sfp-1-Right">
                                        <?php
                                        if ($ethnicity) {
                                            $ethnicity_id = $this->input->post('ethnicity') ? $this->input->post('ethnicity') : "";
                                            echo form_dt_dropdown('ethnicity', $ethnicity, $ethnicity_id, 'class="dropdown-dt domaindropdown"', translate_phrase('Select ethnicity'), "hiddenfield");
                                        }
                                        ?>
                                    </div>
                                    <label id="ageRangeError" class="input-hint error error_indentation error_msg"></label>
                                </div>

                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left bold"><?php echo translate_phrase('First Name:') ?></div>
                                    <div class="sfp-1-Right">
                                        <input id="first_name" class="Degree-input" type="text" style="height: 40px;">
                                    </div>
                                </div>

                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left bold"><?php echo translate_phrase('Last Name:') ?></div>
                                    <div class="sfp-1-Right">
                                        <input id="last_name" class="Degree-input" type="text" style="height: 40px;">
                                    </div>
                                </div>
                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left bold"></div>
                                    <div class="sfp-1-Right" style="text-align: left;">
                                        <button type="button" id="btnMember" class="btn btn-blue" style="height: 36px;"><?php echo translate_phrase('Search') ?></button>
                                    </div>
                                </div>

                                <div class="sfp-1-main mar-top2">
                                    <div class="sfp-1-Left bold"> <?php echo translate_phrase('Sort by'); ?>: </div>

                                    <div class="sortbyDown">
                                        <dl class="sort_dl dropdown-dt domaindropdown common-dropdown">
                                            <dt>
                                            <a href="javascript:;" key="1"><span class="y-overflow-hidden"><?php echo translate_phrase('Application Date') . ' (' . translate_phrase('recent to old') . ')'; ?>
                                                </span> </a> <input type="hidden" name="member_order" id="member_order" value="1">
                                            </dt>
                                            <dd>
                                                <ul>												
                                                    <li><a href="javascript:;" key="1"><?php echo translate_phrase('Application Date') . ' (' . translate_phrase('recent to old') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="2"><?php echo translate_phrase('Application Date') . ' (' . translate_phrase('old to recent') . ')'; ?></a></li>

                                                    <li><a href="javascript:;" key="3"><?php echo translate_phrase('Age') . ' (' . translate_phrase('young to old') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="4"><?php echo translate_phrase('Age') . ' (' . translate_phrase('old to young') . ')'; ?></a></li>

                                                    <li><a href="javascript:;" key="5"><?php echo translate_phrase('First Name') . ' (' . translate_phrase('A to Z') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="6"><?php echo translate_phrase('First Name') . ' (' . translate_phrase('Z to A') . ')'; ?></a></li>

                                                    <li><a href="javascript:;" key="7"><?php echo translate_phrase('Last Active Time') . ' (' . translate_phrase('recent to old') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="8"><?php echo translate_phrase('Last Active Time') . ' (' . translate_phrase('old to recent') . ')'; ?></a></li>

                                                    <li><a href="javascript:;" key="9"><?php echo translate_phrase('Last Order Date') . ' (' . translate_phrase('recent to old') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="10"><?php echo translate_phrase('Last Order Date') . ' (' . translate_phrase('old to recent') . ')'; ?></a></li>

                                                    <li><a href="javascript:;" key="11"><?php echo translate_phrase('Order Amount') . ' (' . translate_phrase('most to least') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="12"><?php echo translate_phrase('Order Amount') . ' (' . translate_phrase('least to most') . ')'; ?></a></li>

                                                    <li><a href="javascript:;" key="13"><?php echo translate_phrase('No. of Chat Messages Sent') . ' (' . translate_phrase('most to least') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="14"><?php echo translate_phrase('Items Pending Review') . ' (' . translate_phrase('newest to oldest') . ')'; ?></a></li>
                                                </ul>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>

                            </div>
                            <div id="manage_member_container"><?php $this->load->view('admin/load_members'); ?></div>
                        </div>
                    </div>
                    
                    <div class="step-form-Main Mar-top-none Top-radius-none" id="marketplace">                        
                            <div class="step-form-Part">	                               
                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left"> <?php echo translate_phrase('Gender'); ?>:</div>
                                    <div class="sfp-1-Right">
                                        <div class="f-decr importance">
                                            <ul>
                                                <?php
                                                $gender_id = $this->input->post('gender_id');
                                                foreach ($gender as $row):
                                                    ?>
                                                    <li><a class="Intro-Button" href="javascript:;" importanceval="<?php echo $row['gender_id']; ?>"><?php echo $row['description']; ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <input type="hidden" name="gender_id" id="m_gender_id">											
                                        </div>                                        
                                    </div>                                                                                                                                    																								
                                </div>                                                       
                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left"><?php echo translate_phrase('Age') ?></div>                                                                                                                                                
                                    <div class="sfp-1-Right">                                                                        
                                        <?php
                                        echo form_dt_dropdown('m_ageRangeLowerLimit', $marketPlaceData['ageRange'], '', 'id="year" class="dropdown-dt"', translate_phrase('-'), "hiddenfield");
                                        ?>
                                        <div class="centimeter">
                                            <?php echo translate_phrase('to') ?>
                                        </div>
                                        <?php
                                        echo form_dt_dropdown('m_ageRangeUpperLimit', $marketPlaceData['ageRange'], '', 'id="year" class="dropdown-dt"', translate_phrase('-'), "hiddenfield");
                                        ?>
                                        <label id="year_work_err" class="input-hint error"></label>
                                    </div>
                                </div>                                                                                                                                   																								

                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left"> <?php echo translate_phrase('Ethnicity'); ?>: </div>
                                    <div class="sfp-1-Right">                                                                        
                                        <dl class="dropdown-dt domaindropdown common-dropdown">
                                            <dt>
                                            <a href="javascript:;" key="">
                                                <span><?php echo translate_phrase('Select Ethnicity'); ?></span> 
                                            </a> 
                                            <input type="hidden" name="m_ethnicity" id="m_ethnicity" value="">
                                            </dt>
                                            <dd>
                                                <ul>
                                                    <?php foreach ($ethnicity as $key => $value): ?>
                                                        <li>
                                                            <a href="javascript:;" key="<?php echo $key ?>">
                                                                <?php echo $value; ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>												
                                                </ul>
                                            </dd>
                                        </dl>      
                                    </div>																
                                </div>
                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left"><?php echo translate_phrase('Height') ?>(cm)</div>
                                    <div class="sfp-1-Right">                                                                        
                                        <?php echo form_dt_dropdown('m_heightRangeLowerLimit', $marketPlaceData['heightRange'], '', 'id="year" class="dropdown-dt"', translate_phrase('-'), "hiddenfield"); ?>
                                        <div class="centimeter"><?php echo translate_phrase('to') ?></div>
                                        <?php echo form_dt_dropdown('m_heightRangeUpperLimit', $marketPlaceData['heightRange'], '', 'id="year" class="dropdown-dt"', translate_phrase('-'), "hiddenfield"); ?>                                    
                                    </div>
                                </div>
                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left"></div>
                                    <div class="sfp-1-Right">                                                                        
                                        <button type="button" id="btnSearchMarketplaceApp" class="btn btn-blue left" style="height: 36px;"><?php echo translate_phrase('Search') ?></button>
                                    </div>
                                </div>                            
                                <div class="sfp-1-main">
                                    <div class="sfp-1-Left"><?php echo translate_phrase('Sort By') ?></div>
                                    <div class="sfp-1-Right">                                                                        
                                        <dl class="sort_dl dropdown-dt domaindropdown common-dropdown">
                                            <dt>
                                            <a href="javascript:;" key="4"><span class="y-overflow-hidden"><?php echo translate_phrase('Age') . ' (' . translate_phrase('old to young') . ')'; ?>
                                                </span> </a> <input type="hidden" name="marketplace_sort_order" id="marketplace_sort_order" value="4">
                                            </dt>
                                            <dd>
                                                <ul>                                                
                                                    <li><a href="javascript:;" key="4"><?php echo translate_phrase('Age') . ' (' . translate_phrase('old to young') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="3"><?php echo translate_phrase('Age') . ' (' . translate_phrase('young to old') . ')'; ?></a></li>                                                

                                                    <li><a href="javascript:;" key="7"><?php echo translate_phrase('Height') . ' (' . translate_phrase('tall to short') . ')'; ?></a></li>
                                                    <li><a href="javascript:;" key="8"><?php echo translate_phrase('Height') . ' (' . translate_phrase('short to tall') . ')'; ?></a></li>                                                
                                                </ul>
                                            </dd>
                                        </dl>
                                    </div>
                                </div> 
                       

                        <div id="marketplace_container">
                            <?php //$this->load->view('admin/marketplace');?>
                        </div>
                    </div>
                </div>
                                        
                    <div class="step-form-Main Mar-top-none Top-radius-none" id="requests">
                        <div class="step-form-Part">
                            <div class="sfp-1-main">
                                <div class="sfp-1-Left"> <?php echo translate_phrase('Status'); ?>:</div>
                                <div class="sfp-1-Right">
                                    <div class="f-decr importance r-statuses-rdo">
                                        <ul>
                                            <?php foreach ($requestsData['statuses'] as $key=>$value):?>
                                                <li>
                                                    <a class="Intro-Button" href="javascript:;" importanceval="<?php echo $key; ?>">
                                                        <?php echo $value; ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <input type="hidden" name="r_status_id" id="r_status_id">											
                                    </div>
                                </div>
                            </div>
                            <div id="requests_container">
                                <?php //$this->load->view('admin/marketplace');?>
                            </div>
                        </div>	                               
                    </div>                                          
            </div>
        </div>
    </div>
</div>
</div>
