<input type="button" onclick="printData();" class="Next-butM" value="<?php echo translate_phrase('Print')?>">
<script type="text/javascript">
	function printData()
	{
	   var divToPrint=document.getElementById("printTable");
	   newWin= window.open("");
	   newWin.document.write(divToPrint.outerHTML);
	   newWin.print();
	   newWin.close();
	}
</script>
<div class="userBox-wrap comn-top-mar">
	<table class="tbl_order" border="1" cellpadding="3" id="printTable">
		<thead>
			<tr>
				<th><?php echo translate_phrase('Ticket Order ID');?></th>
				<th><?php echo translate_phrase('Order Time');?></th>
				<th><?php echo translate_phrase('User ID');?></th>
				<th><?php echo translate_phrase('Name');?></th>
				<th><?php echo translate_phrase('Gender');?></th>
				<th><?php echo translate_phrase('Birthdate');?></th>
				<th><?php echo translate_phrase('Phone');?></th>
				<th><?php echo translate_phrase('Email');?></th>
				<th># <?php echo translate_phrase('Tickets');?></th>
				<th><?php echo translate_phrase('Amount');?></th>				
			</tr>
		</thead>
		<tbody>
			<?php if(isset($event_orders) && $event_orders):?>
				<?php foreach($event_orders as $order):?>
					<tr>
						<td><?php echo $order['event_order_id']?></td>
						<td><?php echo $order['order_time']?></td>
						<td><?php echo $order['paid_by_user_id']?></td>
						<td><?php echo $order['paid_by_name']?></td>
						<td><?php echo $order['gender']?></td>
						<td><?php echo $order['birth_date']?></td>
						<td><?php echo $order['paid_by_mobile_phone_number']?></td>
						<td><?php echo $order['paid_by_email']?></td>
						<td><?php echo $order['num_tickets']?></td>
						<td><?php echo $order['order_amount']?$order['currency_description'].''.$order['order_amount']:''?></td>
						
					</tr>
				<?php endforeach;?>		
			<?php else:?>
				<tr>
					<td colspan="10"><?php echo translate_phrase('No prepaid list found.');?></td>
				</tr>	
			<?php endif;?>
			
			
			
		</tbody>
	</table>
</div>