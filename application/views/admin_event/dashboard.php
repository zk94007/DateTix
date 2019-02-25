<style>
	.border-list li{border-bottom:1px dotted #C0C0C0; padding:5px 0px}
	.border-list li:last-child{border:none}
	.language-part.top{ width:auto; float:none; }
	.language-part.top a{ color:blue;}
	.tbl_order tr:hover {
          cursor: pointer;
    }
    .tbl_order tr:hover td{
    	background-color: #d2d2d2;
    }
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$(".city_dropdown dd ul li a, .topDropdown dd ul li a ").live('click',function () {
			$("#city_form").submit();
		});
	});
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<?php 
					$default_txt = translate_phrase('Select City');
					if($cities)
					{
						foreach($cities as $item)
						{
							if($item['city_id'] == $seleccted_city_id)
							{
								$default_txt = $item['description'];
							}
						}	
					}
					
					echo form_open('',array('id'=>'city_form','method'=>'get'));					
				?>
				<?php if($cities):?>
				<div class="fl">
					<div class="sortbyTxt"><?php echo translate_phrase("Select City");?>: </div>
					<div class="sortbyDown">
						<dl class="city_dropdown dropdown-dt animate-dropdown scemdowndomain menu-Rightmar" >
							<dt>
								<a href="javascript:;" key=""><span><?php echo $default_txt;?></span> </a>
								<input type="hidden" name="city_id" id="city_id" value="<?php echo $seleccted_city_id;?>">
							</dt>
							<dd>
								<ul>
									<?php foreach($cities as $item):?>
									<li><a href="javascript:;" key="<?php echo $item['city_id'];?>"><?php echo $item['description'];?></a></li>
									<?php endforeach;?>
								</ul>
							</dd>
						</dl>
						
					</div>
				</div>	
				<?php endif;?>
					<div class="language-part fr top">
					<?php 
						//$lanaguage_id = $this->language_id;
						//echo form_dt_dropdown('language_id',$languages,$lanaguage_id,'class="dropdown-dt animate-dropdown scemdowndomain topDropdown"',translate_phrase('Select Language'),"hiddenfield");
					?>		
					<?php  echo language_bar(); ?>			
				</div>
				<?php echo form_close();?>
				
				
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="f-decr">
						<ul>
							<li class="Intro-Button-sel">
								<a href="javascript:;"><?php echo translate_phrase('Event Admin');?></a></li>
							<li><a class="Intro-Button" href="<?php echo base_url($this->admin_url.'/merchant_list?city_id='.$seleccted_city_id);?>"><?php echo translate_phrase('Merchant Admin');?></a></li>
						</ul>
				</div>
				<div class="step-form-Main Mar-top-none Top-radius-none active">
					
					<div class="f-decr">
						<a href="<?php echo base_url($this->admin_url.'/create').'?city_id='.$seleccted_city_id?>"><span class="appr-cen">Create Event</span></a>
					</div>
					
				<div class="userBox-wrap comn-top-mar">
					<table class="tbl_order">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th><?php echo translate_phrase('Date');?></th>
								<th><?php echo translate_phrase('Event Name');?></th>
								<th><?php echo translate_phrase('Unique Visitors');?></th>
								<th><?php echo translate_phrase('Online Prepaids');?></th>
								<th><?php echo translate_phrase('Online Prepaid Amt');?></th>
								<!--<th><?php echo translate_phrase('Door Tickets Sold');?></th>
								<th><?php echo translate_phrase('Cash Collected at Door');?></th>-->
								<th><?php echo translate_phrase('Revenue');?></th>								
							</tr>
						</thead>
						<tbody>
							<?php if($events):?>
								<?php foreach($events as $event):?>
									<tr class='clickable-row' onclick="window.location.href = '<?php echo base_url($this->admin_url.'/create/'.$event['event_id']).'?city_id='.$seleccted_city_id?>'">
										<td><button class="btn btn-blue" onclick="window.location.href = '<?php echo base_url($this->admin_url.'/create/'.$event['event_id']).'?city_id='.$seleccted_city_id?>'"><?php 
										if($event['event_end_time'] < SQL_DATETIME)
										{
											$txt = translate_phrase('View');
										}
										else {
											$txt = translate_phrase('Edit');
										}
										echo $txt;?></button></td>
										<td><?php echo date('g:ia '.DATE_FORMATE,strtotime($event['event_start_time']))?></td>
										<td><?php 
										
										$this->general->set_table('event_language');
										if($event_names = $this->general->get("event_name",array('event_id'=>$event['event_id']),array(),1))
										{
											echo '<ul class="border-list">';
											foreach($event_names as $value)
											{
												echo '<li> <a href="'.base_url(url_city_name().'/event.html?id='.$event['event_id'].'&src=0').'" target="_blank""><span style="color:blue">'.$value['event_name'].'</span></a></li>';
											}
											echo '</ul>';
										}
										
										//echo $event['event_name']?></td>
										<td><?php echo number_format($event['visitors']);?></td>
										<td><?php echo $event['online_prepaid_tkt']?></td>
										<td><?php echo $event['online_prepaid_amt']?$event['currency_description'].price_format($event['online_prepaid_amt']):''?></td>
										
										<!--<td><?php echo $event['tickets_sold_at_door']?$event['tickets_sold_at_door']:'';?></td>
										<td><?php echo price_format($event['cash_collected_at_door'])?></td>-->
										
										<td><?php 
											$total = $event['cash_collected_at_door']+$event['online_prepaid_amt'];
											echo $total ?$event['currency_description'].price_format($total):'';
										?></td>										
									</tr>
								<?php endforeach;?>
							<?php else:?>
								<tr>
									<td colspan="6"><?php echo translate_phrase('No records found');?>.</td>
								</tr>
							<?php endif;?>							
						</tbody>
					</table>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>
