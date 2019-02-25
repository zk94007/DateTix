<?php foreach($merchant_list as $key=>$value):?>
<div class="dateBox">              
	<div class="dateBoxRowLeft">
	    <img style="width: 90%;height: 200px"src="<?php echo base_url().'merchant_photos/'.$value['photo_url'];?>"/>
	</div>
	<div class="dateRowRight">
	    <div class="dateBoxRowRight">                        
	        <h3><?php echo $value['name'];?></h3>
	        <p><?php echo $value['address'];?></p>
	        <p><?php echo $value['phone_number'];?></p>
	        <p>
	            <a href="<?php echo $value['website_url'];?>" target="_blank">
	            <?php echo $value['website_url'];?>
	            </a>
	        </p>
	        
	        <div class="appear-prt-but">
	        	<a href="javascript:;" class="rdo_div" key="<?php echo $value['merchant_id'];?>"><span class="disable-butn"><?php echo translate_phrase('Select')?></span></a>
	        	<a href="<?php echo base_url('dates/view_merchant/'.$value['merchant_id']);?>"><span class="appr-cen"><?php echo translate_phrase('View')?></span></a>
	        </div>
		</div>
	</div>
</div> 
<?php endforeach;?>
<input type="hidden" id="merchant_id" name="merchant_id" value="">