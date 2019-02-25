<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<script
    type="text/javascript"
src="<?php echo base_url(); ?>assets/fancybox/jquery.fancybox.js?v=2.1.5"></script>
<link
    rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>assets/fancybox/jquery.fancybox.css?v=2.1.5"
    media="screen" />
<script>
    var page_no = $('#page_no').val();
    var base_url = '<?php echo base_url() ?>';
    var user_id = '<?php echo $this->session->userdata('user_id'); ?>';
    $(document).ready(function () {
		<?php //if (!empty($user_data['current_district_id'])): ?>
        	getMerchant();
        <?php //endif; ?>
        
        $(".various").fancybox({
            maxWidth: 300,
            maxHeight: 600,
            fitToView: false,
            width: '70%',
            height: '70%',
            autoSize: false,
            closeClick: false,
            openEffect: 'none',
            closeEffect: 'none',
            afterShow:function(){
            	$('.active').slideDown();
            },
            afterClose: function () {
                
                showSelectedBudget();
                showSelectedCuisine();
            },
        });

        $(".rdo_div").live('click', function () {
            $(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
            $(this).find('span').removeClass('disable-butn').addClass('appr-cen');
            $(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
            $('#merchant_id').val($(this).attr('key'));

            var selected = ($(this).parent().find(':input[type="hidden"]').attr('id'));
            
            
            if (selected == 'budget_id') {
                var value = $('#budget_id').val();
                displaymessage(value)
            }
            getMerchant();
            
        });

        $('.accordion .hide').hide();
        $('.accordion .accordian-header').click(function () {
            var currentTitle = $(this);
            $(this).next().slideToggle('fast', function () {

                if (currentTitle.find('span i').hasClass('fa-chevron-down'))
                {
                    currentTitle.find('span i').removeClass('fa-chevron-down').addClass('fa-chevron-up')
                }
                else {
                    currentTitle.find('span i').removeClass('fa-chevron-up').addClass('fa-chevron-down')
                }

            }).siblings('.hide').slideUp();
            return false;
        });

        $(".cuisineCkb").live('click', function () {
            var key = $(this).attr('key');
            var container = $(this).attr('lang');
            $('.'+container+' a[key="' + key + '"]').trigger('click');
            $(this).parent().remove();
            
        });
        $(".budgetCkb").live('click', function () {
        	
            var key = $(this).attr('key');
            var container = $(this).attr('lang');
            $('.'+container+' a[key="' + key + '"]').find('span').removeClass('appr-cen').addClass('disable-butn');
            $("#budget_id").val('');
            $(this).parent().remove();
            
             getMerchant();
            
        });
        
        $('.accordion ul li').live('click', function (e) {
            e.preventDefault();
            
            var ele = jQuery(this).find('a');
            var li = ele.parent();
           	
           	var hiddenField = jQuery('#cuisine');
            if (ele.hasClass('active')) {
                var ids = new Array();
                var hiddenFieldValues = hiddenField.val();
                ids = hiddenFieldValues.split(',');
                var index = ids.indexOf(ele.attr('key'));
                ids.splice(index, 1);
                var newHiddenFieldValues = ids.join();
                ele.removeClass('active');
                
                $(ele).siblings('input[type="checkbox"]').prop('checked', false);
            }
            else {

                var inputValues = jQuery(hiddenField).val();
                if (inputValues != "")
                    var newHiddenFieldValues = inputValues + ',' + ele.attr('key');
                else
                    var newHiddenFieldValues = ele.attr('key');

                $(ele).addClass('active');
                $(ele).siblings('input[type="checkbox"]').prop('checked', true);
            }
            if(newHiddenFieldValues)
             	$(ele).parent().parent().parent().addClass('active');
            else
            	$(ele).parent().parent().parent().removeClass('active');
            
            hiddenField.val(newHiddenFieldValues);
        });
        
        $('.accordion ul li input[type="checkbox"]').live('click', function (e) {
        	e.stopPropagation();   
        	$(this).parent().trigger('click');      
        });
   

        $(".dropdown-dt").find('dt a').live('click', function () {
            $(this).parent().parent().find('ul').toggle();
            getMerchant();
        });

        $(".dropdown-dt dd ul li a").live('click', function () {
            $(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
            $(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
            $(this).parent().parent().parent().parent().find("dt a").attr('key', $(this).attr('key'));
            getMerchant();
        });

        $(document).live('click', function (e) {
            var $clicked = $(e.target);
            if (!$clicked.parents().hasClass("dropdown-dt"))
                $(".dropdown-dt dd ul").hide();
        });

        $("#neighbourhood_list ul li a").live('click', function () {
            getMerchant();
        });

        $('#closefancybox').live('click', function () {
            $.fancybox.close();
            showSelectedBudget();
            showSelectedCuisine();
            getMerchant();
        });
    });


    $(window).scroll(function() {  
        var totalScrollAmount = $(window).scrollTop() + $(window).height() + 80;
        if( totalScrollAmount >= $(document).height()) 
        {
        	getMerchant('append','scroll');
        }
    });

   /* 
    *  Remove Dropdown CITY, Country
    * 
    function findNeighborhoodDataByCityId()
    {
        var cityId = $("#current_city_id").val();
        loading();
        $.ajax({
            url: '<?php echo base_url(); ?>' + "dates/get_neighborhood_by_city",
            type: "post",
            data: {id: cityId},
            cache: false,
            success: function (data) {
                stop_loading();
                $("#neighbourhood_list").html(data);
                getMerchant();
            }
        });
    }*/

    function displaymessage(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>' + "dates/get_message",
            type: "post",
            data: {id: id},
            cache: false,
            success: function (data) {
                $("#message").html(data);
            }
        });
    }

    function showSelectedBudget() {

        var budget_id = $('#budget_id').val();
        loading();
        $.ajax({
            url: '<?php echo base_url(); ?>' + "dates/get_selected_budget_list",
            type: "post",
            data: {budget_id: budget_id},
            cache: false,
            success: function (data) {
                stop_loading();               
                $("#budgetList").html(data);                
            }
        });

    }

    function showSelectedCuisine() {

        var cuisine = $('#cuisine').val();
        loading();
        $.ajax({
            url: '<?php echo base_url(); ?>' + "dates/get_selected_cuisine_list",
            type: "post",
            data: {cuisine: cuisine},
            cache: false,
            success: function (data) {
                stop_loading();               
                $("#cuisineList").html(data);
            }
        });

    }
    var isAjaxCall = false;
    var isCallResponse = true;
    function getMerchant(type,action) {
    	
        if(action == 'scroll' && isCallResponse == false)
       	{
       		return false;
       	}
       	else
       	{
       		isCallResponse = true;
       	}
       	if(isAjaxCall == false)
       	{
       		var neighbourhood = $('#neighbourhood').val();
	        var city = $('#current_city_id').val();
	        var cuisine = $('#cuisine').val();
	        var budget_id = $('#budget_id').val();
	        var sortby = $('#sortby').val();
	        var date_type_id=$('#date_type_id').val();
	       	
       		isAjaxCall = true;
       		
       		if(type !='append'){
       			$('#page_no').val(1);
       		}
       		
       		page_no = $('#page_no').val();
       		loading();
	        $.ajax({
	            url: '<?php echo base_url(); ?>' + "dates/get_merchant_list",
	            type: "post",
	            data: {neighbourhood: neighbourhood, cuisine: cuisine, budget_id: budget_id, sortby: sortby,page_no:page_no,city:city,date_type_id:date_type_id},
	            cache: false,
	            success: function (data) {
	                stop_loading();
	                if($.trim(data) != "")
	                {
	                	if(type=='append'){
		                    $("#merchantList").append(data);
						}else{
		                	
		                    $("#merchantList").html(data);                    
		                }
		                $('#page_no').val(parseInt(page_no) + 1);
	                }
	                else
	                {
	                	isCallResponse = false;
	                }
	                isAjaxCall = false;           
	            }
	        });
       	}
       	
    }

function submitform(id){

    $('#merchant_id').val(id);
    var merchant_id=$('#merchant_id').val();
    if(merchant_id!=''){
        $('#newdateFormStep3').submit();
    }
}
</script>
<style>
    .donebutton{
        background: #2097D4;
        border-radius: 2px 2px 2px 2px;
        color: #FFFFFF;
        float: left;
        font-family: 'Conv_MyriadPro-Regular';
        font-size: 16px;
        height: auto;
        line-height: 36px;
        text-align: center;
        width: auto;
        padding: 0 12px 0 12px;
        margin: 0 6px 6px 0px
    }
</style>
<?php
$user_id = $this->session->userdata('user_id');
//$is_premius_member = $this->datetix->is_premium_user($user_id, PERMISSION_INSTANT_INTRO);
?>
<!--*********Page start*********-->
<div class="wrapper">
   <div class="content-part mobile-layout">
        <form name="newdate" id="newdateFormStep3"
              action="<?php echo base_url() . 'dates/new_date_step3'; ?>"
              method="post">

            <!--*********Apply-Step1-E-Page personality start*********-->
            <div class="Apply-Step1-a-main">				
                <div class="step-form-Main">
                    <div class="step-form-Part">
                        <h2> <?php echo translate_phrase('Where do you want to host your date') ?> ?</h2>
                        <div class="f-decrMAIN">
                            <div class="f-decr">																														
                                <!--
                                	<div class="scemdowndomain menu-Rightmar">
                                    <?php //echo form_dt_dropdown('country', $country, $user_country_id, 'id="current_country" class="dropdown-dt scemdowndomain menu-Rightmar" ', translate_phrase('Select country'), "hiddenfield"); ?>	
                                    <label id="liveInError" class="input-hint error error_indentation error_msg"></label>
                                </div>
                                <div class="scemdowndomain menu-Rightmar">
                                    <div id="current_city_dropdown">
                                        <?php //echo $cities ? form_dt_dropdown('current_city_id', $cities, $user_city_id, 'class="dropdown-dt scemdowndomain" ', translate_phrase('Select city'), "hiddenfield") : ''; ?>
                                    </div>
                                    <label id="liveInCITYError" class="input-hint error error_indentation error_msg"></label>
                                </div>
                                -->
                                <div class="scemdowndomain">
                                    <div id="neighbourhood_list">
                                        <?php echo $neighbourhood_list ? form_dt_dropdown('neighbourhood', $neighbourhood_list, '', 'class="dropdown-dt scemdowndomain" ', translate_phrase('Select  Neighbourhood'), "hiddenfield") : ''; ?>
                                    </div>					                        
                                </div>
                            </div>
                        </div>                                                                
                    </div>							
                    
                        <div class="step-form-Part">							
                            <div class="f-decrMAIN">
<!--                                <h2 style="float: left; width: 100px;"> <?php echo translate_phrase('Cuisine') ?></h2>-->
                                <a href="#cuisinDiv" class="various rdo_div" style="float: left; width: 100px;" > 
                                    <span class="appr-cen"><?php echo translate_phrase('Filter') ?></span>
                                </a>
                                <div class="f-decr" style="width:87%;float: left">
                                    <ul id="budgetList"  class="list_rows"></ul>
                                    <ul id="cuisineList"  class="list_rows"></ul>
                                </div>
                                <div class="accordion" id="cuisinDiv" style="display:none">
                                    <a href="javascript:;" id="closefancybox" class="donebutton"><span> <?php echo translate_phrase('Done') ?></span></a>
									<br/>
                                    <div class="step-form-Part">
                                        <h2> <?php echo translate_phrase('Select Budget') ?></h2>
                                        <div class="f-decrMAIN">
                                            <div class="f-decr budgetCkbPopup">
                                                <?php foreach ($budget_list as $key => $row) { ?>											                                                                           
                                                    <a href="javascript:;" class="rdo_div"
                                                       key="<?php echo $row['budget_id']; ?>"> 
                                                        <span
                                                            class="disable-butn"><?php echo $row['description']; ?>
                                                        </span>
                                                    </a>
                                                <?php } ?>										
                                                <input type="hidden" id="budget_id" name="budget_id" value="">
                                            </div>
                                        </div>
                                        <p id="message" class="DarkGreen-color font-italic page-msg-box"></p>
                                    </div>
                                    <?php
                                        $allow_cuisine_date_type_id = array('1');
                                        
                                        if (in_array($date_info['date_type_id'], $allow_cuisine_date_type_id)):
                                    ?>
                                    <?php //if ($cuisine_list): ?>
                                        <div class="step-form-Part">
                                                
                                                <h2 style="float: left; width: 100%;"> <?php echo translate_phrase('Select Cuisines') ?></h2>   
                                                <?php foreach ($cuisine_list as $category): ?>
                                                    <div class="f-decrMAIN ">
                                                        <a href="javascript:;" class="accordian-header"> 
                                                            <span><?php echo $category['description']; ?></span> <span class="accordian-header-icon"><i class="fa fa-chevron-down"></i></span>
                                                        </a>
                                                        <div class="hide list">
                                                            <?php if ($category['list']): ?>
                                                                <ul class="cuisineCkbPopup">
                                                                    <?php foreach ($category['list'] as $cuisine): ?>
                                                                        <li class="grayHoverBox">
                                                                            <input type="checkbox" />
                                                                            <a key="<?php echo $cuisine['cuisine_id']; ?>" href="javascript:;">													
                                                                                <?php echo $cuisine['description']; ?>
                                                                            </a>

                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            <?php endif; ?>									
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                <input type="hidden" id="cuisine" name="cuisine" value="">
                                        </div>
                                    <?php //endif; ?>
                                     <?php endif; ?>
                                    
                                    
                                    
                                </div>
                            </div>				
                        </div>
                   
                    <input type="hidden" name="date_type_id" id="date_type_id" value="<?php echo @$date_info['date_type_id'];?>">
                    <input type="hidden" id="page_no" name="page_no" value="1"/>
                    
                    <div class="step-form-Part">
                        <h2> <?php echo translate_phrase('Sort By') ?></h2>
                        <div class="f-decrMAIN">
                            <div class="f-decr">
                                <?php foreach ($sortby_list as $key => $row) { ?>											                                                                           
                                    <a href="javascript:;" class="rdo_div"
                                       key="<?php echo $row; ?>"> 
                                        <?php if($row=='Featured'):?>
                                            <span
                                                class="appr-cen"><?php echo $row; ?>
                                            </span>
                                        <?php else:?>
                                            <span
                                                class="disable-butn"><?php echo $row; ?>
                                            </span>
                                        <?php endif;?>
                                    </a>
                                <?php } ?>										
                                <input type="hidden" id="sortby" name="sortby" value="Featured">																				
                            </div>				
                        </div> 

                    </div>
                    
                    <div class="step-form-Part" id="merchantList"></div>
                    <input type="hidden" id="merchant_id" name="merchant_id" value="">

                </div>

<!--                <div class="Nex-mar">
                    <input id="submit_button" type="submit" class="Next-butM" value="<?php echo translate_phrase('Next') ?>">
                </div>-->
            </div>
            <!--*********Apply-Step1-E-Page close*********-->				
        </form>
    </div>
</div>
<!--*********Page close*********-->
