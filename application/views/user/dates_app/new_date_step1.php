<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    var user_id = '<?php echo $this->session->userdata('user_id'); ?>';
    $(document).ready(function () {
		
		$(".rdo_div").live('click', function () {
            $(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
            $(this).find('span').removeClass('disable-butn').addClass('appr-cen');
            $(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
            
            check24HourTime();
        });

        $("#show_more_date").live('click', function () {

            var last_date = $('#date_free_time').val();
            if (last_date == '') {
                last_date = $('#default').val();
            }

            jQuery('#dateError').text('');
            loading();
            $.ajax({
                url: '<?php echo base_url(); ?>' + "dates/get_more_dates/" + last_date,
                type: "json",
                data: {},
                cache: false,
                success: function (data) {
                    stop_loading();
                    var html = '';
                    $.each(JSON.parse(data), function (idx, obj) {
                        html += "<a href='javascript:;' class='rdo_div' key='" + obj.key + "' style='text-align;display: inline-block;text-align: left;'><span class='disable-butn'>" + obj.value + "</span></a>";

                    });
                    /* html += "<input type='hidden' id='date_free_time' name='date_free_time' value=''>";
                     html += "<label id='dateError' class='input-hint error error_indentation error_msg'></label>";    
                     html += "<a href='javascript:;' class='rdo_div1' style='display:inline-block' id='show_more_date'><span class='Next-butM' style='background: #FF499A;text-shadow: 0px 0px 0px !important;margin-top: 0px'><?php echo translate_phrase('Show More') ?></span></a>";
                     */
                    $("#DateListing > span").append(html);
                    $('#date_free_time').val($('#DateListing > span > a:last').attr('key'));
                }
            });


        });

        $("#date_time_slider").slider({
            min: 0,
            max: 1410,
            step: 30,
            value: '<?php echo $prefer_date_time_seconds; ?>',
            slide: function (event, ui) {
            	
                calculateTimeFromSliderValue(ui.value);
                check24HourTime();
            }
        });
        calculateTimeFromSliderValue($("#date_time_slider").slider("value"));
    });
    
    function check24HourTime()
    {
        var date = $('#date_free_time').val()
        
        if(date == "")
        {
			date = "<?php echo date('Y-m-d');?>";
			$(".after-date-select").slideUp();
		}
		else
		{
			$(".after-date-select").slideDown();
		}
        var time = $('#date_time').val();
                        
        var t = "<?php echo date('Y-m-d H:i:s',strtotime('+1 day')) ?> ".split(/[- :]/);
		var tomorrow = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
		
		var currentSelected = new Date(date + " " + time);
        var diffMs = (tomorrow - currentSelected);
        
        var diffDays = Math.round(diffMs / 86400000);
		var diffHrs = Math.round((diffMs % 86400000) / 3600000);
		var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);
		
		//console.log('currentSelected : '+currentSelected)
		//console.log(diffDays+' : '+diffHrs+' : '+diffMins);
		
		if (diffDays >= 0 && diffHrs >= 0) {
			$('#message').slideDown();
		} else {
			$('#message').slideUp();
		}
        
    }
    function calculateTimeFromSliderValue(sliderValue)
    {
        var hours = Math.floor(sliderValue / 60);
        var minutes = sliderValue - (hours * 60);
        var time = null;
        minutes = minutes + "";
        var hourslength=parseInt(sliderValue / 60 % 24, 10);
        
        $("#date_time").val(hours + ':' + minutes);
        if (hourslength < 12) {
            time = "AM";
        }
        else {
            time = "PM";
        }
        if (hourslength == 0) {
            hours = 12;
        }
        if (hourslength > 12) {
            hours = hours - 12;
        }
        if (minutes.length == 1) {
            minutes = "0" + minutes;
        }
        $("#date_time_slider_text").html(hours + ":" + minutes + " " + time);        
    }

    function date_step1_validaion() {
        var flag = 1;
        var today = new Date();
        var date = $('#date_free_time').val()
        var time = $('#date_time').val();
        var NextDay = new Date(date + " " + time);
        var diffMs = (NextDay - today); // milliseconds between now & Christmas
        var diffDays = Math.round(diffMs / 86400000); // days
        var diffHrs = Math.round((diffMs % 86400000) / 3600000); // hours
        var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000); // minutes
        
       
        if ($('#date_free_time').val() == '') {
            showError('dateError', '<?php echo translate_phrase("Please specify your date") ?>');
            flag = 0;
        }
        else
        {
            jQuery('#dateError').text('');
        }

        if ($('#date_time').val() == '') {
            showError('timeError', '<?php echo translate_phrase("Please specify your time") ?>');
            flag = 0;
        }
        else
        {
            jQuery('#timeError').text('');
        }
        
        if(diffDays <= '0' && diffMins <= 30){ 
            showError('timeError', '<?php echo translate_phrase("Your date must take place at least 30 minutes from now.") ?>');
            flag = 0;
        }
        else
        {
            jQuery('#timeError').text('');
        }

        if (flag == 0)
            return false;
        else
            return true;

    }
    

    function save_data()
    {

        if (date_step1_validaion())
        {
            $("#newdateForm").submit();
        }
    }
</script>
<style>
    .dropdown-dt dt a {width:100px !important}
</style>
<?php
$user_id = $this->session->userdata('user_id');
//$is_premius_member = $this->datetix->is_premium_user($user_id, PERMISSION_INSTANT_INTRO);
?>
<!--*********Page start*********-->
<div class="wrapper">
	
    <div class="content-part mobile-layout">
        <form name="newdate" id="newdateForm"
              action="<?php echo base_url() . 'dates/new_date_step1'; ?>"
              method="post">

            <!--*********Apply-Step1-E-Page personality start*********-->
            <div class="Apply-Step1-a-main">
            	
                <div class="step-form-Main">
                    <div class="step-form-Part">
                    	<div id="page-msg-box" class="page-msg-box left" style="padding-bottom: 10px;">
							<span class="DarkGreen-color"><?php echo $this->session->flashdata('page_msg_success');?></span>
							<span class="Red-color"><?php echo $this->session->flashdata('page_msg_error');?></span>					
						</div>
						
                        <div class="edu-main">							
                            <div class="aps-d-top">
                                <h2> <?php echo translate_phrase('When are you free for a date') ?>?</h2>
                                <div class="f-decrMAIN">
                                    <div class="f-decr customSelectTag" style="text-align:left" >
                                        <div id="DateListing">
                                            <span>
											<?php
											$selected = "";
											 foreach ($date_list as $key => $row) { ?>											                                                                           
                                            <a href="javascript:;" class="rdo_div"
                                               key="<?php echo $row; ?>" style="text-align:left;display: inline-block"> 
                                               	    <span class="disable-butn"><?php echo date('D, M jS', strtotime($row)); ?></span>		                                        
                                            </a>
											<?php } ?>   
                                                <input type="hidden" id="date_free_time" name="date_free_time" value="">
                                                <input type="hidden" id="default"  value="<?php echo $row;?>">
                                            </span>                                                                                                                                                                          
                                        </div>


                                        <a href="javascript:;" class="rdo_div1" id="show_more_date">
                                            <span style="text-shadow: 0px 0px 0px !important;margin-top: 0px"><?php echo translate_phrase('Show More') ?></span> 
                                        </a>
                                    </div>
                                </div>                                                                                                                                
                            </div>
                            <div class="aps-d-top after-date-select"  style="display: none;">
                                <h2 style="width:150px;float:left"><?php echo translate_phrase('Select a Time') ?></h2>
                                
                                <div class="column-50">
                                    <input type="hidden" id="date_time" name="date_time" value="">
                                    <div id="date_time_slider"></div>
                                    
                                   	<div id="slider1"></div> 
            						<div id="time1"></div> 
            						
                                    <p id="date_time_slider_text" class="bold-txt"></p>
                                    <label id="timeError" class="input-hint error error_indentation error_msg"></label>
                                </div>
                                <label id="dateError" class="input-hint error error_indentation error_msg"></label> 
                                <p id="message" style="display: none;" class="DarkGreen-color font-italic"><?php echo translate_phrase('Posting last minute date (less than 24 hours to date time) will cost you 15 date tickets ') ?></p>               
                            </div> 		                    
                        </div>
                    </div>							
                </div>
                <div class="Nex-mar after-date-select" style="display: none;">
                    <a href="javascript:;" id="ureg_sub" onclick="save_data();"
                       class="Next-butM"><?php echo translate_phrase('Next') ?> </a>
                </div>
            </div>
            <!--*********Apply-Step1-E-Page close*********-->				
        </form>
    </div>
</div>
<!--*********Page close*********-->
