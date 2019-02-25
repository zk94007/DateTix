<script>
$(document).ready(function(){
	$(".tbl_order tbody tr:even").css("background-color", "#f1f1f1");
});
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box">
				
				<!-- START  all users -->
				<h1><?php echo translate_phrase("Users");?></h1>
				<div class="userBox-wrap comn-top-mar">
					<table class="tbl_order">
						<thead>
							<tr>
								<!--<th></th>-->
								<th><?php echo translate_phrase("User ID") ?></th>
								<th><?php echo translate_phrase("Name");?></th>
								<th><?php echo translate_phrase("Gender") ?></th>
								<th><?php echo translate_phrase("Mobile") ?></th>
								<th><?php echo translate_phrase("Email") ?></th>
								<th><?php echo translate_phrase("Applied Time") ?></th>
								<th><?php echo translate_phrase("Completed Step") ?></th>
							</tr>
						</thead>
						
						<?php if(isset($users) && $users):?>
						<tbody>
							<?php foreach($users as $val):?>
							<tr>
								<!--<td><a href="<?php echo base_url('franchise/find_match'.'/'.$ad_id.'/'.$val['user_id']);?>"><span class="appr-cen"><?php echo translate_phrase('Matches')?></span></a></td>-->
								<td><?php echo $val['user_id'];?></td>
								<td><?php echo $val['first_name'].' '.$val['last_name'];?></td>
								<td><?php echo ($val['gender_id'] == 1)?'Male':'Female';?></td>
								<td><?php echo $val['mobile_phone_number'];?></td>
								<td><?php echo $val['email_address'];?></td>
								<td><?php echo $val['applied_date'];?></td>
								<!--<td><?php echo date(DATE_FORMATE,strtotime($val['applied_date']));?></td>-->
								<td><?php echo $val['completed_application_step'];?></td>
							</tr>
							<?php endforeach;?>
						</tbody>
						<?php else :?>
							<tr><td colspan="6"><?php echo translate_phrase("No user found.") ?></td></tr>
						<?php endif;?>												
					</table>					
				</div>
				
				<!-- START  Member details -->
				<h1>Event Orders (members)</h1>
				<div class="userBox-wrap comn-top-mar">
					<table class="tbl_order">
						<thead>
							<tr>
								<th><?php echo translate_phrase("Event ID") ?></th>								
								<th><?php echo translate_phrase("Event Ticket ID") ?></th>
								<th><?php echo translate_phrase("Order Amount") ?></th>
								<th><?php echo translate_phrase("User ID") ?></th>
								<th><?php echo translate_phrase("Name");?></th>
								<th><?php echo translate_phrase("Gender") ?></th>
								<th><?php echo translate_phrase("Mobile") ?></th>
								<th><?php echo translate_phrase("Email") ?></th>
								<th><?php echo translate_phrase("Order Time") ?></th>																
							</tr>
						</thead>
						
						<?php if(isset($member_users) && $member_users):?>
						<tbody>
							<?php foreach($member_users as $val):?>
							<tr>
								<td><?php echo $val['event_id'];?></td>
								<td><?php echo $val['event_ticket_id'];?></td>
								<td><?php echo $val['order_amount'];?></td>
								<td><?php echo $val['user_id'];?></td>
								<td><?php echo $val['first_name'].' '.$val['last_name'];?></td>
								<td><?php echo ($val['gender_id'] == 1)?'Male':'Female';?></td>
								<td><?php echo $val['mobile_phone_number'];?></td>
								<td><?php echo $val['email_address'];?></td>
								<td><?php echo $val['order_time'];?></td>								
							</tr>
							<?php endforeach;?>
						</tbody>
						<?php else :?>
							<tr><td colspan="8"><?php echo translate_phrase("No member event orders found.") ?></td></tr>
						<?php endif;?>												
					</table>					
				</div>
				
				
				<!-- START  NON-Member details -->
				<h1>Event Orders (non-members)</h1>
				<div class="userBox-wrap comn-top-mar">
					<table class="tbl_order">
						<thead>
							<tr>
								<th><?php echo translate_phrase("Event ID") ?></th>								
								<th><?php echo translate_phrase("Event Ticket ID") ?></th>
								<th><?php echo translate_phrase("Order Amount") ?></th>
								<th><?php echo translate_phrase("Name") ?></th>
								<th><?php echo translate_phrase("Mobile") ?></th>
								<th><?php echo translate_phrase("Email") ?></th>
								<th><?php echo translate_phrase("Order Time") ?></th>
							</tr>
						</thead>
						
						<?php if(isset($non_member_users) && $non_member_users):?>
						<tbody>
							<?php foreach($non_member_users as $val):?>
							<tr>
								<td><?php echo $val['event_id'];?></td>
								<td><?php echo $val['event_ticket_id'];?></td>
								<td><?php echo $val['order_amount'];?></td>
								<td><?php echo $val['paid_by_first_name'];?></td>
								<td><?php echo $val['paid_by_last_name'];?></td>
								<td><?php echo $val['paid_by_email'];?></td>
								<td><?php echo $val['order_time'];?></td>
							</tr>
							<?php endforeach;?>
						</tbody>
						<?php else :?>
							<tr><td colspan="5"><?php echo translate_phrase("No non-member event orders found.") ?></td></tr>
						<?php endif;?>												
					</table>					
				</div>
							
			</div>
		</div>
	</div>
</div>
