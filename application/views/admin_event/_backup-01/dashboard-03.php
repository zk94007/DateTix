<script type="text/javascript">
	$(document).ready(function(){
		$(".city_dropdown dd ul li a").live('click',function () {
			$("#city_form").submit();
		});
	});
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title?></h1>
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="step-form-Main Mar-top-none Top-radius-none active">
				<?php if($cities):
					$default_txt = translate_phrase('Select City');
					foreach($cities as $item)
					{
						if($item['city_id'] == $seleccted_city_id)
						{
							$default_txt = $item['description'];
						}
					}
					echo form_open('',array('id'=>'city_form'));					
				?>
				<div class="sortby bor-none Mar-top-none">
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
					<a href="<?php echo base_url($this->admin_url.'/create')?>"><span class="appr-cen">Create Event</span></a>						
				</div>
				<?php 
				echo form_close();
				endif;?>
				<div class="userBox-wrap comn-top-mar">
					<table class="tbl_order">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th><?php echo translate_phrase('Date');?></th>
								<th><?php echo translate_phrase('Name');?></th>
								<th><?php echo translate_phrase('Online Prepaids');?></th>
								<th><?php echo translate_phrase('Online Prepaid Amt');?></th>
								<th><?php echo translate_phrase('Door Tickets Sold');?></th>
								<th><?php echo translate_phrase('Door Amt');?></th>
								<th><?php echo translate_phrase('Total Amt');?></th>								
							</tr>
						</thead>
						<tbody>
							<?php if($events):?>
								<?php foreach($events as $event):?>
									<tr>
										<td><a href="<?php echo base_url($this->admin_url.'/create/'.$event['event_id'])?>">Edit</a></td>
										<td><?php echo date('g:ia '.DATE_FORMATE,strtotime($event['event_start_time']))?></td>
										<td><?php echo $event['event_name']?></td>
										
										<td><?php echo $event['online_prepaid_tkt']?></td>
										<td><?php echo $event['online_prepaid_amt']?$event['currency_description'].$event['online_prepaid_amt']:''?></td>
										<td><?php echo $event['ticket_sold_at_door']?></td>
										<td><?php echo $event['price_door']?></td>										
										<td><?php 
											$total = $event['cash_collected_at_door']+$event['online_prepaid_amt'];
											echo $total ?$event['currency_description'].$total:'';
										?></td>										
									</tr>
								<?php endforeach;?>
							<?php else:?>
								<tr>
									<td colspan="8"><?php echo translate_phrase('No records found');?>.</td>
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
