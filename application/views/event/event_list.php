<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script type="text/javascript">
var yearVal = '<?php echo $selected_year;?>';
$(document).ready(function(){
	<?php if($post_data == 'yes'):?>
	$('#events_tabs').easytabs({defaultTab: "li#past_tab_li"});
	<?php else:?>	
	$('#events_tabs').easytabs({defaultTab: "li#upcoming_tab_li"});	
	<?php endif;?>
	
	$('#events_tabs').bind('easytabs:before', function(tab, panel, data){
		if($(panel['0']).attr('href') == "#next_events")
		{
			window.location.href='<?php echo base_url().url_city_name().'/event.html'; ?>';
			return false;
		}
		
	});
	
	//When select a option..
    $("#year dd ul li a").live('click',function () {
		var curInputVal = $(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val();
		if(curInputVal != '' && yearVal != curInputVal)
		{
			yearVal = curInputVal;
			$("#lookPastEvents").submit();
		}    	
	});	
});
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">				
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			
			<div class="emp-B-tabing-prt">
				<?php if($total_events_in_city):?>
				<div class="emp-B-tabing-M-short" id="events_tabs">
					<ul class='etabs'>
						<!--<li class='tab tab-nav'><span></span><a href="#next_events"><?php echo translate_phrase('Next Event');?></a></li>-->
						<li class='tab tab-nav active' id="upcoming_tab_li"><span></span><a href="#upcoming_events"><?php echo translate_phrase('Upcoming Events');?></a></li>
						<li class='tab tab-nav' id="past_tab_li"><span></span><a href="#past_events"><?php echo translate_phrase('Past Events');?></a></li>
					</ul>
					<div id="next_events"></div>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="upcoming_events">
						<div class="step-form-Part">
							<?php if(isset($upcoming_event_data) && $upcoming_event_data):?>
							<?php foreach($upcoming_event_data as $event_info):?>
								<div class="userBox">
									<div class="userBox-wrap"> 
										<div class="selectDateHed martop-edit">
											<span class="fl"><a href="<?php echo base_url().url_city_name() . '/event.html?id='.$event_info['event_id'] . '&src=' . $this->session->userdata('ad_id');?>" class="blu-color bold" ><?php echo $event_info['event_name']. ' - ' .date(DATE_FORMATE,strtotime($event_info['event_start_time']));?></a></span>
																																	
											<span id="optionValuePerYear"><?php echo date('g:ia',strtotime($event_info['event_start_time'])).translate_phrase(" to ").date('g:ia',strtotime($event_info['event_end_time']));?></span>
										</div>	

										<div class="dateRow"><?php echo $event_info['name'];?></div>
										<div class="locationArea">
											<p>
												<?php 
													if($event_info['address'])
														echo $event_info['address'];
													if($event_info['neighborhood_name'])
														echo ', '.$event_info['neighborhood_name'];?> &nbsp; &nbsp;
												
												<!--<a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=events.html&venue='.$this->utility->encode($event_info['venue_id']);?>"><?php echo translate_phrase('View Map')?></a>-->
												
												</p>
											<p><?php 
													if($event_info['city_name'])
														echo $event_info['city_name'];?></p>
											<p><?php echo $event_info['phone_number'];?></p>
											
											<?php if($event_info['website_url'] != ''):?>
											<p><a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo $event_info['website_url'];?>"><?php echo $event_info['website_url'];?></a></p>
											<?php endif;?>
											
										</div>
									</div>
								</div>
							<?php endforeach;?>
							<?php else:?>
							<span><?php echo translate_phrase("There are currently no upcoming events in "). get_current_city() ;?>.</span>
							<?php endif;?>

						</div>
					</div>
					
					<div class="step-form-Main Mar-top-none Top-radius-none" id="past_events">
						<div class="step-form-Part">
							<?php if($year):?>
							<form action="<?php echo base_url().url_city_name() . '/events.html';?>" method="post" id="lookPastEvents">
								<div class="sortby bor-none Mar-top-none">
									<?php echo form_dt_dropdown('year', $year, $selected_year, 'id="year" class="dropdown-dt common-dropdown dd-menu-mar"', translate_phrase('Year'), "hiddenfield");?>
								</div>								
							</form>
							<?php endif;?>
							<?php if(isset($past_event_data) && $past_event_data):?>
							<?php foreach($past_event_data as $event_info):?>
								<div class="userBox">
									<div class="userBox-wrap"> 
										<div class="selectDateHed martop-edit">
											<span class="fl"><a href="<?php echo base_url().url_city_name() . '/event.html?id='.$event_info['event_id'] . '&src=' . $this->session->userdata('ad_id');?>" class="blu-color bold" ><?php echo $event_info['event_name']. ' - ' .date(DATE_FORMATE,strtotime($event_info['event_start_time']));?></a></span>
											
											<span id="optionValuePerYear"><?php echo date('g:ia',strtotime($event_info['event_start_time'])).translate_phrase(" to ").date('g:ia',strtotime($event_info['event_end_time']));?></span>
										</div>	

										<div class="dateRow"><?php echo $event_info['name'];?></div>
										<div class="locationArea">
											<p>
												<?php 
													if($event_info['address'])
														echo $event_info['address'];
													if($event_info['neighborhood_name'])
														echo ', '.$event_info['neighborhood_name'];?> &nbsp; &nbsp;
												
												<!--<a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=events.html&venue='.$this->utility->encode($event_info['venue_id']);?>"><?php echo translate_phrase('View Map')?></a>-->
												
												</p>
											<p><?php 
													if($event_info['city_name'])
														echo $event_info['city_name'];?></p>
											<p><?php echo $event_info['phone_number'];?></p>
											
											<?php if($event_info['website_url'] != ''):?>
											<p><a href="javascript:;" onclick="openNewWindow(this)" data-url="<?php echo $event_info['website_url'];?>"><?php echo $event_info['website_url'];?></a></p>
											<?php endif;?>
											
										</div>
									</div>
								</div>
							<?php endforeach;?>	
							<?php else:?>
							<span><?php echo translate_phrase("There has been no past events in "). get_current_city() ;?>.</span>
							<?php endif;?>
						</div>
					</div>
				</div>
				
				<?php else:?>
				<div class="Edit-p-top1"><span><?php echo translate_phrase("There are currently no upcoming events in "). get_current_city() ;?>.</span></div>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>
