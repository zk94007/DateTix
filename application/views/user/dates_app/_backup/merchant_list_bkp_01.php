<?php foreach($merchant_list as $key=>$value):?>
        <div class="step-form-Main">
            <div class="step-form-Part">
                
                <div>
                    <img src="<?php echo $value['photo_url'];?>"/>
                </div>
                <div class="edu-main">                   
                    <div class="aps-d-top">                        
                        <p><?php echo $value['name'];?></p>
                        <p><?php echo $value['address'];?></p>
                        <p><?php echo $value['phone_number'];?></p>
                        <p><?php echo $value['website_url'];?></p>
                        
                        <div class="f-decrMAIN">
                            <div class="f-decr customSelectTag">																														
                                <a href="javascript:;" class="rdo_div"
                                        key="<?php echo $value['merchant_id'];?>"> 
                                    <span class="disable-butn"><?php echo translate_phrase('Select This')?>
                                    </span>
                                </a>                                
                            </div>
                        </div>                                                                
                    </div>							
                </div>
            </div>
        </div>
<?php endforeach;?>
<input type="hidden" id="merchant_id" name="merchant_id" value="">