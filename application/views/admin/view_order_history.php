<script>
$(document).ready(function(){
	$(".tbl_order tbody tr:even").css("background-color", "#f1f1f1");
});
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="cityTxt fl"><h1><?php echo translate_phrase('Select Your City');?></h1></div>
			<div class="popup-box">
				
				<h1><?php echo translate_phrase("Date Ticket Orders");?></h1>
				<div class="userBox-wrap comn-top-mar">
					<table class="tbl_order">
						<thead>
							<tr>
								<th><?php echo translate_phrase("Date") ?></th>
								<th><?php echo translate_phrase("No of Date Tickets");?></th>
								<th><?php echo translate_phrase("Currency") ?></th>
								<th><?php echo translate_phrase("Amount") ?></th>
							</tr>
						</thead>
						
						<?php if($ticket_orders):?>
						<tbody>
							<?php foreach($ticket_orders as $order):?>
							<tr>
								<td><?php echo date(ORDER_DATE_FORMATE,strtotime($order['order_time']));?></td>
								<td><?php echo $order['order_num_date_tix'];?></td>
								<td><?php echo $order['currency_name'];?></td>
								<td><?php echo number_format($order['order_amount']);?></td>
							</tr>
							<?php endforeach;?>
						</tbody>
						
						<tfoot>
							<tr>
								<td><?php echo translate_phrase("Total");?></td>
								<td><?php echo $total_tickets?></td>
								<td><?php echo $all_ticket_currency?></td>
								<td><?php echo number_format($total_ticket_amount)?></td>
							</tr>
						</tfoot>
						<?php else :?>
							<tr><td colspan="4"><?php echo translate_phrase("No ticket order found.") ?></td></tr>
						<?php endif;?>												
					</table>					
				</div>
				
				<h1><?php echo translate_phrase("Account Upgrade Orders");?></h1>
				<div class="userBox-wrap comn-top-mar">
					<table class="tbl_order">
						<thead>
							<tr>
								<th><?php echo translate_phrase("Date") ?></th>
								<th><?php echo translate_phrase("Option");?></th>
								<th><?php echo translate_phrase("Duration") ?></th>								
								<th><?php echo translate_phrase("Currency") ?></th>
								<th><?php echo translate_phrase("Amount") ?></th>								
							</tr>
						</thead>
						<?php if($membership_orders):?>
						<tbody>
							<?php foreach($membership_orders as $order):?>
							<tr>
								<td><?php echo date(ORDER_DATE_FORMATE,strtotime($order['order_time']));?></td>
								<td><?php echo $order['membership_option_value'] ?></td>
								<td><?php echo $order['order_membership_duration_months']; echo $order['order_membership_duration_months'] == 1?translate_phrase(" month"):translate_phrase(" months");?></td>
								<td><?php echo $order['currency_name'];?></td>
								<td><?php echo number_format($order['order_amount']);?></td>
							</tr>
							<?php endforeach;?>							
						</tbody>
						
						<tfoot>
							<tr>
								<td><?php echo translate_phrase("Total");?></td>
								<td></td>
								<td></td>
								<td><?php echo $all_membership_currency;?></td>
								<td><?php echo number_format($total_membership_amount);?></td>
							</tr>
						</tfoot>
						<?php else :?>
							<tr><td colspan="5"><?php echo translate_phrase("No ticket order found.") ?></td></tr>
						<?php endif;?>						
					</table>					
				</div>
					
				<div class="btn-group">
					<button onclick='window.location.href="<?php echo base_url().'admin'?>"' class="btn btn-blue"><?php echo translate_phrase("Ok");?></button>
				</div>
			</div>
		</div>
	</div>
</div>
