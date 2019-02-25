<?php
 if($merchants):?>
<?php foreach($merchants as $merchant):?>
<tr class='clickable-row' onclick="window.location.href = '<?php echo base_url($this->admin_url.'/create_merchant/'.$merchant['merchant_id'].'?neighborhood_id='.$selected_neighborhood_id.'&city_id='.$this->input->get('city_id'))?>'">
	<td><button class="btn btn-blue" onclick="window.location.href = '<?php echo base_url($this->admin_url.'/create_merchant/'.$merchant['merchant_id'].'?neighborhood_id='.$selected_neighborhood_id.'&city_id='.$this->input->get('city_id'))?>'"><?php echo translate_phrase('Edit');?></button></td>
	<td><?php echo $merchant['name']?></td>
	<td><?php echo $merchant['address']?></td>
	<td><?php echo $merchant['price_range']?></td>
	<td><?php echo implode(', ',explode(',', $merchant['tags']))?></td>
	<td><?php echo $merchant['is_featured']?'Yes':'No'?></td>										
</tr>
<?php endforeach;?>
<?php endif;?>