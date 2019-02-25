<?php 
$meal_date_type = 1;
$is_active = 0;

if(isset($merchant_date_type) && $merchant_date_type)
{
	foreach($merchant_date_type as $sel_value)
	{
		if($sel_value['date_type_id'] == $meal_date_type)
		{
			$is_active = 1;
		}
	}
}
if($is_active == 0)
{
	echo '<h2>You only need to select cuisines for merchants who support Meal dates.</h2>';
}
else{
?>
<form id="add_edit_form" name="add_edit_form" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="merchant_id" value="<?php echo isset($merchant_id)?$merchant_id:'';?>" />	
	<?php if ($cuisine_list): ?>
    <div class="step-form-Part">
	    <div class="accordion">	    	
	    <?php 
	    $selected_ids = array();
	    foreach ($cuisine_list as $category): ?>
	        <a href="javascript:;" class="accordian-header"> 
	            <span><?php echo $category['description']; ?></span> <span class="accordian-header-icon"><i class="fa fa-chevron-down"></i></span>
	        </a>
	        <div class="hide list">
	            <?php 
	            
	            if ($category['list']): ?>
	                <ul>
	                    <?php foreach ($category['list'] as $cuisine): ?>
	                        <li>
	                        	<?php
	                        		$is_active = 0;
	                        		if($merchant_cuisine_list)
		                        	{
		                        		foreach($merchant_cuisine_list as $value)
										{
											if($value['cuisine_id'] == $cuisine['cuisine_id'])
											{
												$is_active = 1;
												$selected_ids[] = $value['cuisine_id'];
											}
										}
		                        	}
	                        	?>
	                            <input type="checkbox" <?php echo $is_active?'checked="checked"':''?>/>
	                            <a <?php echo $is_active?'class="active"':''?> key="<?php echo $cuisine['cuisine_id']; ?>" href="javascript:;">													
	                                <?php echo $cuisine['description']; ?>
	                            </a>
	
	                        </li>
	                    <?php endforeach; ?>
	                </ul>
	            <?php endif; ?>									
	        </div>
	    <?php endforeach; ?>
	    <input type="hidden" name="merchant_cuisine" value="<?php echo $selected_ids?implode(',',$selected_ids):''?>">	    
	    </div>
    </div>
	<?php endif; ?>
	
	<?php $btnTxt = 'Save Changes';	?>
	<div class="btn-group mar-top2">
		<input type="submit" class="btn btn-blue" value="<?php echo translate_phrase($btnTxt)?>">
		<a class="disable-butn cancel-link" href="<?php echo base_url($this->admin_url)?>"><?php echo translate_phrase('Cancel');?></a>
	</div>
</form>
<?php }?>