<script  type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>

<link class="include" rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/jqplot/jquery.jqplot.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/styles/shCoreDefault.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/styles/shThemejqPlot.min.css" />


<!-- Don't touch this! -->
    <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/scripts/shCore.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/scripts/shBrushJScript.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url()?>assets/jqplot/syntaxhighlighter/scripts/shBrushXml.min.js"></script>
<!-- Additional plugins go here -->
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/jquery.jqplot.min.js"></script>
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.barRenderer.min.js"></script>
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
  <script class="include" type="text/javascript" src="<?php echo base_url()?>assets/jqplot/plugins/jqplot.pointLabels.min.js"></script>
<!-- End additional plugins -->



<script type="text/javascript">
	var preventAjaxCall = [];
	$(document).ready(function(){
		$('#active_expire').easytabs();
		
		//Load Map from Markup 
		$.each($('.chart'),function(i,item){
			var url = $(item).attr('url');
			var obj = $(item).attr('id');
			var form_data = {};
			generate_chart(obj,url,form_data)
		})
		
		
		$(".dropdown-dt dd ul li a").live('click',function () {
			var container = $('#active_expire ul li.active a').attr('href');
			
			var form_data = {'select_month_year':$("#month_year").val(),'select_city_id':$("#city_id").val()};
			
			$.each($(container).find('.chart'),function(i,item){
				var url = $(item).attr('url');
				var obj = $(item).attr('id');
				
				generate_chart(obj,url,form_data)
			})
		});
		
	});
	
	function generate_chart(chart_id,url,form_data)
	{
		$('#'+chart_id).html('<div class="div_data_loader"></div>');
		
		var s1 = [];
		var ticks = [];
		$.getJSON( url,
			form_data,
			function(jsonData){
				$('#'+chart_id).html('');
				//console.log(JSON.stringify(jsonData));
				
				if(jsonData.length === 0)
				{
					$('#'+chart_id).html('<p>Sorry No Data Found.</p>').css('height','auto');
					
				}
				else
				{
					
					for (var key in jsonData) 
				    {
				      if (jsonData.hasOwnProperty(key)) 
				      {
				        s1.push(parseFloat(jsonData[key]));
				        ticks.push(key);
				      }
				    }
					
				    $.jqplot.config.enablePlugins = true;
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
					echo form_dt_dropdown('city_id',$city_data, $this->default_city_id, 'class="dropdown-dt scemdowndomain"', '', "hiddenfield"); ?>
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
						<li class='tab tab-nav' id="memberTAB"><span></span><a href="#member"><?php echo translate_phrase('Members');?></a></li>
						<li class='tab tab-nav' id="revenueTAB"><span></span><a href="#revenue"><?php echo translate_phrase('Revenue');?></a></li>
						<li class='tab tab-nav' id="introTAB"><span></span><a href="#intro"><?php echo translate_phrase('Intro');?></a></li>
					</ul>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="revenue"></div>
					
					<div class="step-form-Main Mar-top-none Top-radius-none" id="member">
						<div class="column-50">
							<div class="chart-lbl">Approved Members</div>
							<div id="chart1" url="<?php echo base_url('dashboard1979/get_approve_member');?>" class="chart"></div>
						</div>
						
						<div class="column-50">
							<div class="chart-lbl">New Members</div>
							<div id="chartNewMember" url="<?php echo base_url('dashboard1979/get_new_member');?>" class="chart"></div>
						</div>
						
						<div class="column-50">
							<div class="chart-lbl">Active Members</div>
							<div id="activeMembers" url="<?php echo base_url('dashboard1979/get_active_member');?>" class="chart"></div>
						</div>
						
						<div class="column-50">
							<div class="chart-lbl">Premium Members</div>
							<div id="premiumMembers" url="<?php echo base_url('dashboard1979/get_premium_member');?>" class="chart"></div>
						</div>
						
						<div class="column-50">
							<div class="chart-lbl">Avg. Revenue per Premium Members</div>
							<div id="avgRevenuePerMember" lang="<?php echo translate_phrase('Amount in USD($)')?>" url="<?php echo base_url('dashboard1979/get_avg_revenue_per_premium_member');?>" class="chart"></div>
						</div>
						
					</div>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="intro"></div>
				</div>
			</div>
		</div>
	</div>
</div>
