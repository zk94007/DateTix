<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script
	src="<?php echo base_url()?>assets/js/spin.js"></script>
<?php
$past_venue_txt =  translate_phrase('Select Past Venue');
?>
<script type="text/javascript">
var spinner;
var ajax_request;
$(document).ready(function(){
	
	 $( "#venueAutocomplete" ).autocomplete({
 		appendTo: "#auto-venue-container",
 		minLength: 1,
        source: function (request, response) {

		$("#venue_other_id").val('');
		unselect_vanues();
		
		if($("#venue_spinner").find('div').length == 0)
		{
			start_spin('venue_spinner');			
		}
		ajax_request = $.ajax({
	            url: '<?php echo base_url(); ?>' +"my_dates/suggest_venue/",
	            data: { query: request.term },
	            dataType: "json", 
                type:"post",
	            success: function (data) {
	            	if($("#venue_spinner").find('div').length != 0)
	        		{
	            		spinner.stop();
	        		}
	            	
	            	var i="0";
		            var availableTags=new Array();
	                 $.each(data.venue_data,function(id,item) {
	                	 availableTags[i] = item.name
	                	 i= parseInt(i)+parseInt(1);
	                });
		            response(data.venue_data);
	            },
	            error: function () {
	                response([]);
	            }
	        });
	    },
	      select: function( event, ui ) {
	        $( "#venue_other_id" ).val( ui.item.id);
	        $("#venueAutocomplete").val(ui.item.name);
	        return false;
	      }
 	}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a>" + item.name + "<br>" + item.address +' '+ item.city+ "</a>" )
        .appendTo( ul );
    }

	 $("#submitBtn").live('click',function(){
		 	var flag = true;
		 	var lastElement = '';
		  	var importanceFileds = jQuery('.importance').find('input[type="hidden"]');
		 	jQuery.each(importanceFileds,function(index,element){
		        if(jQuery(element).val() == "")
		        {
		        	lastElement = jQuery(element).parent().parent().parent();
		            if($(element).parent().find('label').length == 0)
		            	jQuery('<label class="input-hint error error_indentation error_msg">'+$(element).attr("error_msg")+'</label>').insertAfter(jQuery(element));
		            
		            flag = false;
		            //return false;
		        }
		        else
		        {
		        	if($(element).parent().find('label').length != 0)
		        		$(element).parent().find('label').text('');
			    }
		    });

		 	if($("#neighborhood_id").val() == "")
		 	{
		 		flag = false;
		 		 if($("#neighborhood_id").parent().find('label').length == 0)
		 			jQuery('<label class="input-hint error error_indentation error_msg"> <?php echo translate_phrase("Please Select Select Neighborhood")?></label>').insertAfter(jQuery("#neighborhood_id"));

		 		lastElement = jQuery("#neighborhood_id").parent().parent();
			}
		 	else
		 	{
			 	if($("#neighborhood_id").parent().find('label').length != 0)
	        		$("#neighborhood_id").parent().find('label').text('');
			}
			
		 	if((typeof($("#venue_id").val()) === 'undefined' || $("#venue_id").val() == "") &&
		 			$("#venueAutocomplete").val() == "" &&
				 (typeof($("#past_vanue_id").val()) === 'undefined' || $("#past_vanue_id").val() == ""))
		 	{
			 	
		 		flag = false;
		 		lastElement = jQuery("#venueAutocomplete").parent().parent().parent();
		        if($("#venueAutocomplete").parent().find('label').length == 0)
	            	jQuery('<label class="input-hint error error_indentation error_msg"> <?php echo translate_phrase("Please Select Date Venue")?></label>').insertAfter(jQuery("#venueAutocomplete"));
			}
		 	else
		 	{
		 		if($("#venueAutocomplete").parent().find('label').length != 0)
	        		$("#venueAutocomplete").parent().find('label').text('');
			}
			
			if(flag )
			{
				$("#formData").submit();
			}
			else
			{
				if(lastElement != '')
					jQuery('body').scrollTo(lastElement,800);		        
			}
			
	});
	 
	
	$("#use_my_date_ticket").live('click',function(){
		var obj = $(this);
		
		if($("#date_ticket_msg").hasClass('hidden'))
		{
			$(obj).find('input').val('1');
			$('.no-ticket-msg').slideUp();
			$("#date_ticket_msg").fadeIn('slow',function(){
				$(this).removeClass('hidden');
				$(obj).find('span').removeClass('disable-butn').addClass('appr-cen');
			});
		}
		else
		{
			$('.no-ticket-msg').slideDown();
			$(obj).find('input').val('0');
			$("#date_ticket_msg").fadeOut('slow',function(){
				$(this).addClass('hidden');
				$(obj).find('span').addClass('appr-cen').addClass('disable-butn');
			});
		}
	});
	 
	$(".rdo_div").live('click',function(){
		$(this).parent().siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().parent().find(':input[type="hidden"]').val($(this).attr('key'));
		if($(this).parent().parent().attr('id') == 'dateType')
		{
			recommend_vanues();
		}
	});

	$(".venue_rdo_div").live('click',function(){
		$(this).parent().siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().parent().find(':input[type="hidden"]').val($(this).attr('key'));

		$("#past_vanue_id").val('');
		$("#past_vanue_text").text('<?php echo $past_venue_txt?>');

		$("#venueAutocomplete").val('');
		$("#venue_other_id").val('');
		
		var curObj = $(this);
		$("#date_details").fadeIn('slow',function(){
			$("#date_details").html($(curObj).siblings('.hidden').html());
		});
		
	});

	//On Select Drop down Option
	$('dl[name="drop_neighborhood_id"] dd ul li a').live('click',function(){
		if($("#neighborhood_id").val() != '')
		{
			if($("#own-venue").css('display') == 'none')
				$("#own-venue").fadeIn();

			if($("#past-venue").css('display') == 'none')
				$("#past-venue").fadeIn();

		}
		
		recommend_vanues();
	});

	$('.past-vanues dd ul li a').live('click',function(){
		if($("#past_vanue_id").val() != '')
		{
			if($(this).attr('neighborhood_id') != '')
			{
				$("#neighborhood_id").val($(this).attr('neighborhood_id'));
				$('dl[name="drop_neighborhood_id"] dt a span').text($(this).attr('neighborhood'));
			}
			$( "#venue_other_id" ).val( '');
		 	$("#venueAutocomplete").val('');
		 	unselect_vanues();
		}	
	});

	
    /*-----------------------------------------------*/
});

function unselect_vanues()
{
	$.each($("#recommend_container a.venue_rdo_div"),function(i,item){
		if($(item).find('span').hasClass('appr-cen'))
		{
			$(item).find('span').addClass('disable-butn').removeClass('appr-cen')
		}
	});
	 
	if($("#date_details").is(':visible'))
	{
		$("#venue_id").val("")
		$("#date_details").fadeOut('slow');				
	}
}
function start_spin(id)
{
	var opts = {
			  lines: 7, // The number of lines to draw
			  length: 6, // The length of each line
			  width: 5, // The line thickness
			  radius: 5, // The radius of the inner circle
			  corners: 1, // Corner roundness (0..1)
			  rotate: 0, // The rotation offset
			  direction: 1, // 1: clockwise, -1: counterclockwise
			  color: '#000', // #rgb or #rrggbb or array of colors
			  speed: 1, // Rounds per second
			  trail: 60, // Afterglow percentage
			  shadow: false, // Whether to render a shadow
			  hwaccel: false, // Whether to use hardware acceleration
			  className: 'spinner', // The CSS class to assign to the spinner
			  zIndex: 2e9, // The z-index (defaults to 2000000000)
			  top: 'auto', // Top position relative to parent in px
			  left: 'auto' // Left position relative to parent in px
			};
	 var target = document.getElementById(id);
	spinner = new Spinner(opts).spin(target);
}

function unSelectImporance(ele,eleType,hiddenFieldId)
{
    if(eleType == 'addBox')
    {
        jQuery('#'+hiddenFieldId).parent().parent().next().find('ul li.Intro-Button-sel').removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
        jQuery('#'+hiddenFieldId).parent().parent().next().find('input[type="hidden"]').val('')
        return 
    }
    else if(eleType == 'dd')
    {
        
        return 
    }
    else
    {
        var selectedPrefrenceCount = ele.parent().parent().parent().find('ul li.selected').length;
        if(selectedPrefrenceCount == 0)
        {
           var importanceContainer = ele.parent().parent().parent().next();
           jQuery(importanceContainer).find('li.Intro-Button-sel').removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
           ele.parent().parent().parent().next().find("input[type='hidden']").val('');
        }
    }
}

function recommend_vanues()
{	
	var neighborhood_id = $("#neighborhood_id").val();
	
	var date_type_id = $("#date_type_id").val();

	if(neighborhood_id != '' && date_type_id != '')
	{
		var data = {neighborhood_id :neighborhood_id,date_type_id:date_type_id };
		loading();
		$.ajax({ 
	        url: '<?php echo base_url(); ?>' +"my_dates/recommendation_vanues", 
	        type:"post",
	        data:data,
	        success: function (response) {
	        	stop_loading();

	        	$("#past_vanue_id").val('');
	    		$("#past_vanue_text").text('<?php echo $past_venue_txt?>');

	    		$( "#venue_other_id" ).val( '');
			    $("#venueAutocomplete").val('');
			      
	        	$("#recommend_container").html(response);
	
	        	$.each($(".Opt-Counter"),function(i,item){
	        		$(item).html(i+1);
	           });
	       }
		});
	}
}

</script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,1);
$is_premium_intro = $this->datetix->is_premium_user($user_info['user_id'],1);

$now = strtotime(SQL_DATETIME);
$sel_day_strtime = isset($user_date_data['0']['date_time'])?strtotime($user_date_data['0']['date_time']):$now;
$is_date_accepted = isset($user_date_data['0']['date_accepted_by_user_id'])?strtotime($user_date_data['0']['date_accepted_by_user_id']):"";


$timeDiff = abs($sel_day_strtime - $now);

$numberDays = $timeDiff/86400;  // 86400 seconds in one day

$numberDays = intval($numberDays);

// and you might want to convert to integer
if(intval($numberDays) < 7)
$numberDays = 7;


$count_start = $numberDays+1;
?>
<script type="text/javascript">
var start = '<?php echo $count_start;?>';
function show_more_days()
{
	start = parseInt(start);
	loading();
	$.ajax({ 
        url: '<?php echo base_url(); ?>' +"my_dates/seven_more_days/"+start, 
        type:"post",
        data:{'sort_by':$(this).attr('key')},
        success: function (response) {
        	stop_loading();
        	$("#dayContainer").append(response);
        	start += 7;
       }
	});
}
</script>
<!--*********Suggest date ideal Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box Mar-top-none">
				<h1>
				<?php echo $heading_txt;?>
				</h1>
				<form id="formData"
					action="<?php echo base_url().url_city_name().'/suggest-date-idea.html?return_to='.$return_url.'&intro='.$this->utility->encode($intro_data['user_intro_id']);?>"
					method="post">
					<div class="edu-main last-bor mar-b-none">
						<h2>
						<?php echo translate_phrase('When are you free to meet')?>
						<?php echo $user_info['first_name']?>
							? <span>*</span>
						</h2>
						<p class="font-italic">
						<?php echo $user_info['first_name']?>
						<?php echo translate_phrase(' prefers to meet on ')?>
						<?php
						$pref = '';
						$pref1 = '';
						$pref = explode(',', $user_info['preferred_date_days']);
						sort($pref);
						for ($i = 0; $i < count($pref); $i++) {

							if ($pref[$i] == 1) {
								$pref[$i] = translate_phrase('Mondays');
							}

							if ($pref[$i] == 2) {
								$pref[$i] = translate_phrase('Tuesdays');
							}

							if ($pref[$i] == 3) {
								$pref[$i] = translate_phrase('Wednesdays');
							}


							if ($pref[$i] == 4) {
								$pref[$i] = translate_phrase('Thursdays');
							}

							if ($pref[$i] == 5) {
								$pref[$i] = translate_phrase('Fridays');
							}

							if ($pref[$i] == 6) {
								$pref[$i] = translate_phrase('Saturdays');
							}

							if ($pref[$i] == 7) {
								$pref[$i] = translate_phrase('Sundays');
							}

							$pref1 .= $pref[$i] . ', ';
						}
						echo trim($pref1, ', ');
						?>
						</p>
						<div class="f-decrMAIN three-column-li month-with-day">
							<div id="dayContainer" class="importance">
								<input type="hidden" name="prefer_date_days"
									error_msg="<?php echo translate_phrase('Please choose day')?>"
									value="<?php echo ($now < $sel_day_strtime)?$sel_day_strtime:'';?>" />
									<?php for($i=1;$i<= $numberDays; $i++):?>
									<?php
									$strtime = strtotime('tomorrow + '.$i.' day');?>
								<div>
									<a class="rdo_div" key="<?php echo $strtime;?>"
										href="javascript:;"> <span
										<?php echo date('l, F j', $sel_day_strtime ) == date('l, F j', $strtime)?'class="appr-cen"':'class="disable-butn"';?>><?php echo date('l, F j', $strtime);?>
									</span> </a>
								</div>
								<?php endfor;?>
							</div>
							<div>
								<a onclick="show_more_days()" class="disable-butn"
									href="javascript:;"><?php echo translate_phrase('Show more days')?>...</a>
							</div>

						</div>
						<div class="sortby bor-none Mar-top-none">
						<?php
						$start = "9:00 am";
						$end = "11:30 pm";
						$tStart = strtotime($start);
						$tEnd = strtotime($end);
						$tNow = $tStart;

						?>
							<dl class="y-atdowndomain  common-dropdown importance">
								<dt>
									<a style="width: 130px;" id="y-atdomaindropdown"
										key="<?php echo ($now != $sel_day_strtime)?date('g:i a',$sel_day_strtime):''?>"><span
										style="width: 100px;"><?php echo ($now!= $sel_day_strtime)?date('g:i a',$sel_day_strtime):translate_phrase('Select a time');?>
									</span> </a> <input type="hidden" name="prefer_date_time"
										value="<?php echo ($now != $sel_day_strtime)?date('g:i a',$sel_day_strtime):''?>"
										error_msg="<?php echo translate_phrase('Please select time')?>" />
								</dt>
								<dd>
									<ul id="y-atdomainul" style="display: none; width: 146px;">
									<?php while($tNow <= $tEnd):?>
										<li><a style="font-size: 15px;"
											key="<?php echo date("g:i a",$tNow);?>"><?php echo date("g:i a",$tNow);?>
										</a></li>
										<?php $tNow = strtotime('+30 minutes',$tNow);
										endwhile;;?>
									</ul>
								</dd>
							</dl>
						</div>
					</div>
					<div class="edu-main last-bor mar-b-none">
						<h2>
						<?php echo translate_phrase('What type of first date would you like to have with ')?>
						<?php echo $user_info['first_name']?>
							? <span>*</span>
						</h2>
						<?php if($intro_prefered_date_type):?>
						<p class="font-italic">
						<?php echo $user_info['first_name']?>
						<?php echo translate_phrase(' prefers ');?>
						<?php foreach ($intro_prefered_date_type as $key=>$data_type):?>
						<?php $prefered_dates[] = $data_type['date_type_description'];?>
						<?php endforeach;?>
						<?php $last = ''; if(count($prefered_dates) >= 2) $last = ' or '.array_pop($prefered_dates);?>
						<?php echo implode(', ', $prefered_dates).$last;?>
						<?php endif;?>
						</p>
						<div class="sortby bor-none Mar-top-none importance" id="dateType">
						<?php $date_type_id = isset($user_date_data['0']['date_type_id'])?$user_date_data['0']['date_type_id']:'';?>
						<?php foreach ($date_type as $key => $value):?>
							<div>
								<a href="javascript:;" class="rdo_div"
									key="<?php echo $value['date_type_id'];?>"> <span
									<?php echo $value['date_type_id'] == $date_type_id?'class="appr-cen"':'class="disable-butn"';?>><?php echo $value['description'];?>
								</span> </a>
							</div>
							<?php endforeach;?>

							<input type="hidden" name="date_type_id"
								error_msg="<?php echo translate_phrase('Please select date type')?>"
								id="date_type_id" value="<?php echo $date_type_id?>" />
							<div class="emailinput date_type_idea">
								<input type="text" name="date_type_other"
									value="<?php echo isset($user_date_data['0']['date_type_other'])?$user_date_data['0']['date_type_other']:''?>"
									placeholder="<?php echo translate_phrase('Describe any other preferred first date idea')?>">
							</div>
						</div>
					</div>

					<div class="edu-main last-bor mar-b-none">
						<h2>
						<?php echo translate_phrase('Where would you like to meet ').$user_info['first_name']?>
							? <span>*</span>
						</h2>
						<div class="sortby bor-none Mar-top-none">
							<div class="">
							<?php
							$selected_neigh  = isset($user_date_data['0']['neighborhood_id'])?$user_date_data['0']['neighborhood_id']:'';

							if(!$selected_neigh)
							$selected_neigh = isset($selected_venue['neighborhood_id'])?$selected_venue['neighborhood_id']:'';

							echo form_dt_dropdown('neighborhood_id',$neighborhood,$selected_neigh ,'class="majordowndomain common-dropdown"',translate_phrase('Select neighborhood'),"hiddenfield"); ?>
							</div>
						</div>
						<div id="recommend_container">
						<?php
						$venue_cnt = 1;
						$venue_id = (isset($user_date_data['0']['venue_id']) && $user_date_data['0']['venue_id'])?$user_date_data['0']['venue_id']:'';
						$vanue_other = isset($user_date_data['0']['venue_other_name']) ? $user_date_data['0']['venue_other_name'] : '';
						if(isset($recommonded_venue) && $recommonded_venue):?>

							<div class="full-width">
								<!-- dateBoxHed align-left Black-color [below classess] -->
								<div class="sortbyTxt bold-txt">
								<?php echo translate_phrase('Option '). $venue_cnt ++?>
									:
									<?php echo translate_phrase('Select from recommended venues')?>
									:
								</div>
								<div class="f-decrMAIN padB-none">
									<div class="f-decr">
									<?php foreach ($recommonded_venue as $key => $value):?>
										<div>
											<a href="javascript:;" class="venue_rdo_div"
												key="<?php echo $value['venue_id'];?>"> <span
												<?php if($value['venue_id'] == $venue_id && !$vanue_other)
												{
													echo 'class="appr-cen"';
													$selected_venue = $value;
												}
												else
												{
													if($key == 0 && $venue_id == '' && !$vanue_other)
													echo 'class="appr-cen"';
													else
													echo 'class="disable-butn"';
												}?>><?php echo $value['name'];?> </span> </a>

											<div class="hidden">
												<div class="dateRow">
												<?php echo $value['name']?>
												</div>
												<div class="locationArea Mar-top-none">
													<p>
													<?php echo $value['address'];?>
														&nbsp; &nbsp; <a href="javascript:;"
															onclick="openNewWindow(this)"
															data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=suggest-date-idea.html&venue='.$this->utility->encode($value['venue_id']);?>">
															<?php echo translate_phrase('View Map')?> </a>
													</p>
													<p>
													<?php if($value['venue_dates']){ echo implode(' / ', $value['venue_dates']);}?>
													</p>
													<p>
													<?php echo $value['phone_number'];?>
													</p>
													<p>
														<a href="javascript:;" onclick="openNewWindow(this)"
															data-url="<?php echo $value['review_url'];?>"><?php echo $value['review_url'];?>
														</a>
													</p>
												</div>
											</div>
										</div>
										<?php endforeach;?>
										<input type="hidden" name="venue_id" id="venue_id"
											value="<?php echo isset($selected_venue['venue_id'])?$selected_venue['venue_id']:'';?>" />
									</div>
								</div>
								<?php $first_vanue = isset($selected_venue)?$selected_venue:$recommonded_venue['0'];?>
								<div class="datesArea bor-none Mar-top-none" id="date_details">
								<?php if(!$vanue_other):?>
									<div class="dateRow">
									<?php echo $first_vanue['name']?>
									</div>
									<div class="locationArea Mar-top-none">
										<p>
										<?php echo $first_vanue['address'];?>
											&nbsp; &nbsp; <a href="javascript:;"
												onclick="openNewWindow(this)"
												data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=suggest-date-idea.html&venue='.$this->utility->encode($value['venue_id']);?>">
												<?php echo translate_phrase('View Map')?> </a>
										</p>
										<p>
										<?php if($first_vanue['venue_dates']){ echo implode(' / ', $first_vanue['venue_dates']);}?>
										</p>
										<p>
										<?php echo $first_vanue['phone_number'];?>
										</p>
										<p>
											<a href="javascript:;" onclick="openNewWindow(this)"
												data-url="<?php echo $first_vanue['review_url'];?>"><?php echo $first_vanue['review_url'];?>
											</a>
										</p>
									</div>
									<?php endif;?>
								</div>
							</div>
							<?php endif;?>
						</div>
						<div class="full-width">
							<div id="own-venue" style="display:<?php echo isset($user_date_data['0']['venue_other'])?'block':'none';?>">
								<div class="sortbyTxt bold-txt">
								<?php echo translate_phrase('Option '); ?>
									<span class="Opt-Counter"><?php echo $venue_cnt ++?> </span>:
									<?php echo translate_phrase('Enter your own venue')?>
									:
								</div>
								<div
									class="f-decrMAIN <?php echo (!isset($past_venue) || $past_venue)?'padB-none':'';?>">
									<div class="f-decr">
										<div class="emailinput">
											<dl class="autocomplete-dl-drop">
												<dt>
													<input type="text" name="venue_other"
														id="venueAutocomplete"
														value="<?php echo isset($user_date_data['0']['venue_other_name'])?quotes_to_entities($user_date_data['0']['venue_other_name']):'';?>"
														placeholder="<?php echo translate_phrase('Enter venue name and address')?>"
														name=""><span id="venue_spinner"
														style="margin: 15px -20px; position: absolute;"></span> <input
														name="venue_other_id" id="venue_other_id" type="hidden"
														value="<?php echo isset($user_date_data['0']['venue_other']) && $user_date_data['0']['venue_other']?$user_date_data['0']['venue_other']:'';?>" />
												</dt>
												<!-- autocomplete dd -->
												<dd id="auto-venue-container"></dd>
											</dl>

											<div class="powered-by">
												<div class="powered-innr">
													<div class="powerd-txt">
													<?php echo translate_phrase('Powered by')?>
														:
													</div>
													<div class="powerd-icon">
														<img
															src="<?php echo base_url()?>assets/images/powered-icon.jpg"
															alt="" />
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php if(isset($past_venue) && $past_venue):?>
							<div id="past-venue" class="sortby bor-none Mar-top-none" style="display:<?php echo $selected_neigh?'block':'none';?>">
								<div class="sortbyTxt">
								<?php echo translate_phrase('Pick from your past venues')?>
									:
								</div>
								<div class="sortbyDown">
									<dl class="majordowndomain common-dropdown past-vanues">
										<dt>
										<?php
										foreach ($past_venue as $value)
										{
											if($venue_id == $value['venue_id'])
											{
												$past_venue_txt = $value['name'];
											}
										}
										?>

											<a href="javascript:;"><span id="past_vanue_text"><?php echo $past_venue_txt;?>
											</span> </a> <input type="hidden" id="past_vanue_id"
												name="past_vanue_id" value="<?php echo $venue_id;?>">
										</dt>
										<dd>
											<ul>
											<?php foreach ($past_venue as $value):?>
												<li><a key="<?php echo $value['venue_id']?>"
													neighborhood_id="<?php echo isset($value['neighborhood_id'])?$value['neighborhood_id']:''?>"
													neighborhood="<?php echo isset($value['neighborhood'])?$value['neighborhood']:''?>"><?php echo $value['name']?>
												</a></li>
												<?php endforeach;?>
											</ul>
										</dd>
									</dl>
								</div>
							</div>
							<?php endif;?>
						</div>
					</div>

					<div class="edu-main mar-b-none">
					<?php
					$is_bottom_msg_show  = 1;

					if($is_premius_member && $is_bottom_msg_show ):?>
						<div class="dateBoxHed align-left">
						<?php echo translate_phrase('You may enjoy this date free because you have the ')?>
							<a class="blu-color"
								href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase("Unlimited Date Tickets account upgrade")?>
							</a>!
						</div>
						<?php $is_bottom_msg_show =0;  endif;?>


						<?php if($is_bottom_msg_show && $is_premium_intro ):?>
						<div class="dateBoxHed align-left">
						<?php echo translate_phrase('You may enjoy this date free because ').$user_info['first_name'].translate_phrase(' has the ')?>
							<a class="blu-color"
								href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase("Unlimited Date Tickets account upgrade")?>
							</a>!
						</div>
						<?php $is_bottom_msg_show =0;   endif;?>

						<?php if($is_bottom_msg_show && $fb_mutual_friend_use_app > 0):?>
						<div class="dateBoxHed align-left">
						<?php echo translate_phrase('You may enjoy this date free because you have at least 1 mutual friend with ') .$user_info["first_name"].translate_phrase(' who is on '.get_assets('name','DateTix'))?>
							.
						</div>
						<?php $is_bottom_msg_show =0; endif;?>

						<?php if(isset($is_ticket_paid_by_intro) && $is_bottom_msg_show && $is_ticket_paid_by_intro):?>
						<div class="dateBoxHed align-left">
						<?php echo translate_phrase('You may enjoy this date free because ').$user_info["first_name"].translate_phrase(' has used a date ticket for this date')?>
							.
						</div>
						<?php $is_bottom_msg_show =0; endif;?>


						<?php if(isset($is_ticket_paid_by_user) && $is_bottom_msg_show && $is_ticket_paid_by_user && (isset($user_date_data['0']['date_accepted_time']) && $user_date_data['0']['date_accepted_time'] != '0000-00-00 00:00:00')):?>
						<div class="dateBoxHed align-left">
						<?php echo translate_phrase('You have already used a date ticket for this date')?>
							.
						</div>
						<?php $is_bottom_msg_show =0; endif;?>


						<!-- ONE TICKET LEFT -->
						<?php if($is_bottom_msg_show):?>
						<div
							class="no-ticket-msg respond-now-txt Red-color <?php echo isset($is_ticket_paid_by_user) && $is_ticket_paid_by_user ?'hidden':'';?>">
							<?php echo translate_phrase('Since both you and ').$user_info["first_name"].translate_phrase(" don't have the Unlimited Date Tickets account upgrade, would you like to use one of your date tickets in order to allow ").$user_info["first_name"].translate_phrase(' to accept your date')?>
							?
							<?php echo translate_phrase('Alternatively')?>
							, <a class="blu-color"
								href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-date.html&tab=active"><?php echo translate_phrase('add the Unlimited Date Tickets upgrade to your account')?>
							</a>!
						</div>

						<?php if($user_data['num_date_tix'] > 0):?>
						<div class="comn-top-mar fl">
							<a href="javascript:;" id="use_my_date_ticket"> <span
								class="<?php echo isset($is_ticket_paid_by_user) && $is_ticket_paid_by_user ? 'appr-cen':'disable-butn'?>"><?php echo translate_phrase('Use Date Ticket')?>
							</span> <input type="hidden" name="use_date_ticket"
								value="<?php echo isset($is_ticket_paid_by_user) && $is_ticket_paid_by_user ?'1':'0';?>">
							</a>
							<p>
							<?php echo translate_phrase('You have ').$user_data['num_date_tix']; echo ($user_data['num_date_tix']> 1) ?translate_phrase(' date tickets'):translate_phrase(' date ticket'); echo translate_phrase(' left in your account')?>
								.
							</p>
						</div>

						<div
							class="respond-now-txt Red-color font-italic <?php echo isset($is_ticket_paid_by_user) && $is_ticket_paid_by_user ? '':'hidden'?>"
							id="date_ticket_msg">
							<?php echo translate_phrase('You will be charged a date ticket if and only if ').$user_info['first_name'].translate_phrase(' accepts your date')?>
							.
						</div>
						<?php else:?>
						<div class="lbl-black comn-top-mar fl">
						<?php echo translate_phrase('You have no date tickets left');?>
							. <a class="blu-color"
								href="<?php echo base_url() . url_city_name() ?>/get-more-tickets.html?return_to=my-date.html&tab=active"><?php echo translate_phrase('Get more date tickets now')?>
							</a>!
						</div>
						<?php endif;?>
						<?php endif;?>

						<div class="appear-prt-but comn-top-mar">
							<div class="suggest-btn">
								<a id="submitBtn" href="javascript:;"><?php echo translate_phrase('Suggest Date')?>
								</a>
							</div>
							<a href="<?php echo $return_url?>"> <span
								class="disable-butn btn-blue not-int-btn"><?php echo translate_phrase('Cancel')?>
							</span> </a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--*********Suggest date ideal -Page close*********-->
