<script  type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>

<link class="include" rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/jqplot/jquery.jqplot.min.css" />

<!--<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/styles/shCoreDefault.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/styles/shThemejqPlot.min.css" />
    <script type="text/javascript" src="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/scripts/shCore.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/scripts/shBrushJScript.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/scripts/shBrushXml.min.js"></script>

-->
<!-- Don't touch this! -->
    <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/jquery.jqplot.min.js"></script>
<!-- Additional plugins go here -->
  
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.barRenderer.min.js"></script>
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.pointLabels.min.js"></script>
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.highlighter.js"></script>
<!-- End additional plugins -->



<script type="text/javascript">
	var chartType = "";
	$(document).ready(function(){		
		$('#active_expire').easytabs();		
		
		var tabName = window.location.hash;
		if(tabName == '' || tabName =='#_=_')
		{
			tabName = '#revenue';
		}
		load_all_charts(tabName,{});
		
		//Bind tab click
		$('#active_expire').bind('easytabs:after', function(tab, panel, data){
	    	var tabName = '#'+$(data).attr('id');
	    	load_all_charts(tabName,{});
    	});
    	
    	//Bind button click
		$(".dropdown-dt dd ul li a").live('click',function () {
			var container = $('#active_expire ul li.active a').attr('href');			
			var form_data = {'select_month_year':$("#month_year").val(),'select_city_id':$("#city_id").val()};
			load_all_charts(container,form_data);
		});
		
	});
	function load_all_charts(container,form_data)
	{
		console.log('Loading...'+container);
		$.each($(container).find('.chart'),function(i,item){
			var url = $(item).attr('url');
			var obj = $(item).attr('id');
			
			if($(item).hasClass('bar-chart'))
			{
				chartType = "bar";
			}
			else if($(item).hasClass('stack-chart'))
			{
				chartType = "stackChart";
			}
			else
			{
				chartType = "pie";
			}
			
			generate_bar_chart(obj,url,form_data,chartType )
		});
		
	}
	function generate_bar_chart(chart_id,url,form_data,chartType )
	{
		$('#'+chart_id).html('<div class="div_data_loader"></div>');
		loading();
		var s1 = [];
		var ticks = [];
		$.getJSON( url,
			form_data,
			function(jsonData){
				stop_loading();
				$('#'+chart_id).html('');
				
				if(jsonData.length === 0)
				{
					$('#'+chart_id).html('<p>Sorry, No Data Found.</p>');
					
				}
				else
				{
					$.jqplot.config.enablePlugins = true;
				    
				    //create bar chart
				    if(chartType == 'bar')
				    {
				    	for (var key in jsonData) 
					    {
					      if(jsonData.hasOwnProperty(key)) 
					      {
					        s1.push(parseFloat(jsonData[key]));
					        ticks.push(key);
					      }
					    }
				    	$.jqplot(chart_id, [s1], {
					    	title : $("#"+chart_id).attr('lang'),
					        // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
					        animate: !$.jqplot.use_excanvas,
					        seriesDefaults:{
					            renderer:$.jqplot.BarRenderer,
					            pointLabels: { show: true },
					            labels:ticks,
					            rendererOptions:{ varyBarColor : true }
					        },
					        axes: {
					            xaxis: {
					                renderer: $.jqplot.CategoryAxisRenderer,
					                ticks: ticks
					            }
					        },
					        highlighter: { show: false }
					    });
				    }
				    else if(chartType == 'stackChart')
				    {
				        var tags = jsonData.tags;
				        var tagResult = [];
					    for(var i=0;i<tags.length;i++)
		            	{
		            		//tagResult.push(tags[i]);
		            		
		            		series=new Object();
				            series.label = tags[i].toString();                
				            tagResult.push(series);
				            
		            	}
		            	
				        $.jqplot(chart_id, jsonData.series, {
				        	animate: !$.jqplot.use_excanvas,
				        	title : $("#"+chart_id).attr('lang'),
				            seriesDefaults: {
				                renderer:$.jqplot.BarRenderer,
				                pointLabels: {show: true},
				            },
							series:tagResult,
							legend: {
				                show: true,
				                location: 'se',
				                placement: 'outside'
				            },   
				            axes: {
					            xaxis: {
					                renderer: $.jqplot.CategoryAxisRenderer,
					                ticks: jsonData.labels
					            }
					        },
					        highlighter: { show: false }
					        
				        });
				    }
				    else if(chartType == 'pie')
				    {
				    	
				    	 //create pie chart
				        var result = new Array();
					   	for (var key in jsonData) 
					    {
					      if(jsonData.hasOwnProperty(key)) 
					      {
					      	var temp = new Array();
					        temp.push(key.toString());
					        temp.push(parseFloat(jsonData[key]));
					        result.push(temp);
					      }
					    }
					    
				    	$.jqplot(chart_id, [result], {
					    	title : $("#"+chart_id).attr('lang'),
					        // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
					        animate: !$.jqplot.use_excanvas,
					        seriesDefaults:{
					            renderer:$.jqplot.PieRenderer,
					           	trendline:{ show: true },
					            rendererOptions: {
						          showDataLabels: true,
							    },
					           
					        },
					        highlighter: {
					        	show: true,
					        	showMarker:true,
			                    tooltipLocation: 'se',
							    useAxesFormatters: false,
							    tooltipFormatString: '%s'
					        }
					    });
				    }
				}
			    
			}
		);
	}
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="step-form-Part">
			<?php if($cities):?>
			<div class="sortby bor-none Mar-top-none">
				<div class="sortbyTxt"><?php echo translate_phrase('Select City');?>: </div>
				<div class="sortbyDown">
					<?php 
					foreach($cities as $item)
					{
						$city_data[$item['city_id']] = 	$item['description'];
					}
					echo form_dt_dropdown('city_id',$city_data, $this->default_city_id, 'class="dropdown-dt scemdowndomain"', 'All', "hiddenfield"); ?>
				</div>
			
				<div class="sortbyTxt"> <?php echo translate_phrase('End Month') ?></div>
				<div class="sortbyDown">
				<?php
				$month_names = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
				$month_year = array();
				$default_month_year_display = date('M-y',strtotime($this->default_month_year));
				
				for ($i = 14; $i <=  date('y') ; $i++) {
					foreach($month_names as $key=>$val)
					{
						if($i == 14 )
						{
							if($key>5)
							{
								$month_year[$val.'-20'.$i] = $val.'-'.$i;	
							}
						}
						else {
							$month_year[$val.'-20'.$i] = $val.'-'.$i;
						}
					}
				}
				?>
				<?php echo form_dt_dropdown('month_year', $month_year,$this->default_month_year, 'id="attended_start" class="dropdown-dt"', $default_month_year_display, "hiddenfield"); ?>
				</div>
			</div>
			<?php endif;?>
			</div>
			<div class="My-int-head">
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="active_expire">
					<ul class='etabs'>
						<li class='tab tab-nav' id="revenueTAB"><span></span><a href="#revenue"><?php echo translate_phrase('Revenue');?></a></li>
						<li class='tab tab-nav' id="memberTAB"><span></span><a href="#member"><?php echo translate_phrase('Members');?></a></li>
						<li class='tab tab-nav' id="introTAB"><span></span><a href="#intro"><?php echo translate_phrase('Intro');?></a></li>
					</ul>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="revenue">
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Premium Subscription Revenue');?></div>
								<div id="premium_subscription_revenue" lang="US($)" url="<?php echo base_url('dashboard1979/get_subscription_revenue');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Event Revenue');?></div>
								<div id="event_revenue" lang="US($)" url="<?php echo base_url('dashboard1979/get_event_revenue');?>" class="chart bar-chart"></div>
							</div>
						</div>
						
						
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Premium Subscription Orders');?></div>
								<div id="premium_subscription_orders" url="<?php echo base_url('dashboard1979/get_subscription_orders');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Event Orders');?></div>
								<div id="event_orders" url="<?php echo base_url('dashboard1979/get_event_orders');?>" class="chart bar-chart"></div>
							</div>
						</div>
						
					</div>
					
					<div class="step-form-Main Mar-top-none Top-radius-none" id="member">
						
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Approved Members');?></div>
								<div id="chart1" url="<?php echo base_url('dashboard1979/get_approve_member');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('New Members');?></div>
								<div id="chartNewMember" url="<?php echo base_url('dashboard1979/get_new_member');?>" class="chart bar-chart"></div>
							</div>
								
						</div>
						
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Active Members');?></div>
								<div id="activeMembers" url="<?php echo base_url('dashboard1979/get_active_member');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Premium Members');?></div>
								<div id="premiumMembers" url="<?php echo base_url('dashboard1979/get_premium_member');?>" class="chart bar-chart"></div>
							</div>
						</div>
						
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Avg. Revenue per Premium Members');?></div>
								<div id="avgRevenuePerMember" lang="<?php echo translate_phrase('Amount in USD($)')?>" url="<?php echo base_url('dashboard1979/get_avg_revenue_per_premium_member');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Premium Members / Active Members')?></div>
								<div id="premium_active_member_ration" lang="<?php echo translate_phrase('Ration (%)')?>" url="<?php echo base_url('dashboard1979/get_premium_active_member_ration');?>" class="chart bar-chart"></div>
							</div>
						</div>
						
						<div class="div-row">
							<div class="column-three">
								<div class="chart-lbl"><?php echo translate_phrase('User by Gender')?></div>
								<div id="user_by_gender" url="<?php echo base_url('dashboard1979/get_user_by_gender');?>" class="chart pie-chart"></div>
							</div>
							
							<div class="column-three">
								<div class="chart-lbl"><?php echo translate_phrase('User by Age')?></div>
								<div id="user_by_age" lang="Year"  url="<?php echo base_url('dashboard1979/get_user_by_age');?>" class="chart pie-chart"></div>
							</div>
							
							<div class="column-three">
								<div class="chart-lbl"><?php echo translate_phrase('User by Ethnicity')?></div>
								<div id="user_by_ethnicity"  url="<?php echo base_url('dashboard1979/get_user_by_ethnicity');?>" class="chart pie-chart"></div>
							</div>
						</div>
						
						<div class="div-row">
							<div class="column">
								<div class="chart-lbl"><?php echo translate_phrase('New Members by Source')?></div>
								<div id="user_by_source"  url="<?php echo base_url('dashboard1979/get_user_by_source');?>" class="chart stack-chart"></div>
							</div>
						</div>
						
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Members Who Have Sent At Least 1 Message');?></div>
								<div id="member_atleast_one_msg" url="<?php echo base_url('dashboard1979/get_member_atleast_one_msg');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Members Who Have Phone Numbers')?></div>
								<div id="member_have_phone_number" url="<?php echo base_url('dashboard1979/get_member_have_phone');?>" class="chart bar-chart"></div>
							</div>
						</div>
						 
					</div>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="intro">
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Message Sent');?></div>
								<div id="msg_sent" url="<?php echo base_url('dashboard1979/get_intro_msg_sent');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Avg. Messages Exchanged per intro with Messages')?></div>
								<div id="avg_msg_sent" url="<?php echo base_url('dashboard1979/get_avg_message_per_intro');?>" class="chart bar-chart"></div>
							</div>
						</div>
						
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Intro Generated')?></div>
								<div id="intro_generated" url="<?php echo base_url('dashboard1979/get_intro_generated');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl"><?php echo translate_phrase('Intro Email Sent');?></div>
								<div id="intro_email_sent" url="<?php echo base_url('dashboard1979/get_intro_email_sent');?>" class="chart bar-chart"></div>
							</div>							
						</div>
						
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl">% <?php echo translate_phrase('Intros Viewed by One Side')?></div>
								<div id="intro_view_one_side" url="<?php echo base_url('dashboard1979/get_intro_view_per_one_side');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl">% <?php echo translate_phrase('Intros Viewed by Both Sides');?></div>
								<div id="intro_view_per_both_side" url="<?php echo base_url('dashboard1979/get_intro_view_per_both_sides');?>" class="chart bar-chart"></div>
							</div>							
						</div>
						
						<div class="div-row">
							<div class="column-50">
								<div class="chart-lbl">% <?php echo translate_phrase('Intros With Messages from One Side')?></div>
								<div id="intro_msg_per_one_side" url="<?php echo base_url('dashboard1979/get_intro_msg_per_one_side');?>" class="chart bar-chart"></div>
							</div>
							
							<div class="column-50">
								<div class="chart-lbl">% <?php echo translate_phrase('Intros With Messages from Both Sides');?></div>
								<div id="intro_msg_per_both_side" url="<?php echo base_url('dashboard1979/get_intro_msg_per_both_sides');?>" class="chart bar-chart"></div>
							</div>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
