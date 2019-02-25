<?php foreach($merchant_list as $key=>$value):?>
<div class="dateBox">              
	<div class="userBoxLeft">
		<a href="<?php echo base_url('dates/view_merchant/'.$value['merchant_id']);?>">
        	<img class="img-l" src="<?php echo $value['photo_url'];?>"/>
        </a>
        
	</div>
	<div class="userBoxRight">
	    <div class="dateBoxRowRight">                        
                
            <h3>
                <a href="<?php echo base_url();?>dates/view_merchant/<?php echo $value['merchant_id'];?>" style="text-decoration: none">
                    <?php echo $value['name'];?>
                </a>
            </h3>
            <?php if($value['address']):?>
	        <p><?php echo $value['address'];?></p>
	        <?php endif;?>
	        
	        <?php if($value['phone_number']):?>
	        <p><?php echo $value['phone_number'];?></p>
	        <?php endif;?>
	        <?php if($value['website_url']):?>
	        <p>
	            <a href="<?php echo $value['website_url'];?>" target="_blank">
	            <?php echo $value['website_url'];?>
	            </a>
	        </p>
	        <?php endif;?>
	        <?php if($value['price_range']):?>
	        <p class="DarkGreen-color bold page-msg-box">
	            <?php echo $value['price_range'];?>	            
	        </p>
	        <?php endif;?>
	        
	        <div class="btn-group">
	        	<a href="javascript:;"  key="<?php echo $value['merchant_id'];?>" onclick="submitform('<?php echo $value['merchant_id'];?>')">
                    <span class="Edit-Button01"><?php echo translate_phrase('Select')?></span>
                </a>
	        </div>
		</div>
	</div>
</div> 
<?php endforeach;?>
