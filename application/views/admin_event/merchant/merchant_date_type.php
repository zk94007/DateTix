<form id="add_edit_form" name="add_edit_form" action=""  method="post" enctype="multipart/form-data">
	<input type="hidden" name="merchant_id" value="<?php echo isset($merchant_id)?$merchant_id:'';?>" />	
    <div class="step-form-Part">
	    <div class="accordion">	    	
		<?php 
       		if ($date_types): ?>
            <ul>
                <?php foreach ($date_types as $value): ?>
                    <li class="left mar-R">
                    	<?php
                    		$is_active = 0;
                    		if(isset($merchant_date_type) && $merchant_date_type)
                        	{
                        		foreach($merchant_date_type as $sel_value)
								{
									if($sel_value['date_type_id'] == $value['date_type_id'])
									{
										$is_active = 1;
										$selected_ids[] = $value['date_type_id'];
									}
								}
                        	}
                    	?>
                        <input type="checkbox" <?php echo $is_active?'checked="checked"':''?>/>
                        <a <?php echo $is_active?'class="active"':''?> key="<?php echo $value['date_type_id']; ?>" href="javascript:;">													
                            <span class="Black-color lin-hght34"><?php echo $value['description']; ?></span>
                        </a>

                    </li>
                <?php endforeach; ?>
            </ul>
	            <?php endif; ?>
	    	<input type="hidden" name="merchant_date_type" value="<?php echo $selected_ids?implode(',',$selected_ids):''?>">	    
	    </div>	    
    </div>	
	<?php $btnTxt = 'Save Changes';	?>
	<div class="btn-group mar-top2">
		<input type="submit" class="btn btn-blue" value="<?php echo translate_phrase($btnTxt)?>">
		<a class="disable-butn cancel-link" href="<?php echo base_url($this->admin_url)?>"><?php echo translate_phrase('Cancel');?></a>
	</div>
</form>