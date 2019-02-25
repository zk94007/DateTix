<style>
	.currency{
		font-size: 16px !important;
		line-height: 44px !important;
		float: left;
		margin-right: 10px;
	}
	label.error{
		width: 100%;
		float: left;
		clear: both;
	}
	.domaindropdown {height: auto;}
</style>
<link href="<?php echo base_url()?>assets/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
<script  type="text/javascript" src="<?php echo base_url()?>assets/datetimepicker/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.datepicker').datetimepicker({timepicker:false,format:'Y-m-d',minDate:0});
		$('.timepicker').datetimepicker({
		  datepicker:false,
		  format:'H:i',
		  step:30,  
		});
		
		//validate Hidden Fields
		
		$('#add_edit_event').validate();
	
		$(".selectVenue li a").click(function(){
			$(".currency").text($(this).attr('currency_name'));
		});
		$("#add_flayer").fileupload({
            dataType: 'json',
            add: function (e, data) {
	        	 
        	    if(isMobileView == 'No' || isMobileView != 'No')
                {
        	    	var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
	   	        	var uploadErrors = [];
	   	            if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
	   	                 uploadErrors.push(invalidImgType);
	   	            }
	   	             //in bytes
		   	        if(data.originalFiles[0]['size'] > 10000000) {
			   	        uploadErrors.push(invalidImgSize);
		   	        }
                    if(uploadErrors.length > 0) {
                    	if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
                    	{
                    		$(this).parent().parent().parent().append('<label class="input-hint mobile-error error-msg">'+uploadErrors.join("\n")+'</label>');
                    	}
                    }
                    else
                    {
                    	$('body').css('cursor', 'wait');
                    	data.submit();
                   	}
                	
                }
                else
                {
                	if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
                	{
                		$(this).parent().parent().parent().append(mobileErrorMsg);
                	}
            		return false;
                }
            },
            done: function (e, data) {
            	console.log(data);
                if (data.result.success === 1) {
					var img_url = data.result.url;
					$("#img-container").html('<div class="upload-part"><img height="150"  src="'+img_url+'" alt="img" /></div>');
					
					$('body').css('cursor', 'auto');
	             }
	             else
	             {
	             	if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
                	{
                		$(this).parent().parent().parent().append('<label class="input-hint mobile-error error-msg">'+data.result.msg+'</label>');
                	}
	             }
            }
	});
	
	});
	
</script>
<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field')?></div>
<form id="add_edit_event" name="add_edit_event" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="event_id" value="<?php echo isset($event_id)?$event_id:'';?>" />
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Event Name')?>: <span>*</span></div>
		<div class="sfp-1-Right">
			<input value="<?php echo isset($event_name)?$event_name:'';?>" id="event_name" name="event_name" class="FDates-input" type="text" style="height: 40px;" required="">
		</div>
	</div>

<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Venue')?>: <span>*</span></div>
	<div class="sfp-1-Right">
		<?php 
		$currency = "";
		if(isset($venues) && $venues):
		
			$venue_id = isset($venue_id)?$venue_id:($this->input->post('venue_id') ? $this->input->post('venue_id') : "");
			$select = translate_phrase('Select venue');
			if($venue_id)
			{
				foreach($venues as $vanue)
				{
					if($vanue['venue_id'] == $venue_id)
					{
						$select = $vanue['name'];
						$currency = $vanue['currency_description'];
					}	
				}
			}
		?>
		<dl name="drop_venue_id" class="dropdown-dt domaindropdown">
			<dt>
				<a href="javascript:;" key="<?php echo $venue_id;?>"><span><?php echo $select;?></span></a>												
				<input type="hidden" id="venue_id" name="venue_id" value="<?php echo $venue_id;?>" required="">
			
			</dt>
			<dd>
				<ul style="display: none;" class="selectVenue">
					<?php foreach($venues as $vanue):?>
					<li><a currency_name="<?php echo $vanue['currency_description'];?>" currency_id="<?php echo $vanue['currency_id'];?>" key="<?php echo $vanue['venue_id'];?>"><?php echo $vanue['name'];?></a></li>
					<?php endforeach;?>													
				</ul>
			</dd>
				
		</dl>
		<?php endif;?>
	</div>
	<label id="venueIdError" class="input-hint error error_indentation error_msg"></label>
</div>

<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Start Time')?>: <span>*</span></div>
	<div class="sfp-1-Right">
		<div class="fl">
			<?php 
				$startDate = isset($event_start_time)?date('Y-m-d',strtotime($event_start_time)):'';
				$startTime = isset($event_start_time)?date('H:i',strtotime($event_start_time)):'';											
			?>
			<input name="event_start_date" type="text" class="post-input datepicker" value="<?php echo $startDate;?>" required="">
		</div>
		<span style="padding: 0px 5px; float:left;">&nbsp;</span>
		<div class="fl">
			<input name="event_start_time" type="text" class="post-input timepicker" value="<?php echo $startTime;?>" required="">
		</div>
	</div>
</div>

<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('End Time')?>: <span>*</span></div>
	<div class="sfp-1-Right">
		<div class="fl">
			<?php 
				$endDate = isset($event_end_time)?date('Y-m-d',strtotime($event_end_time)):'';
				$endTime = isset($event_end_time)?date('H:i',strtotime($event_end_time)):'';											
			?>
			<input name="event_end_date" type="text" class="post-input datepicker" value="<?php echo $endDate;?>" required="">
		</div>
		<span style="padding: 0px 5px; float:left;">&nbsp;</span>
		<div class="fl">
			<input name="event_end_time" type="text" class="post-input timepicker" value="<?php echo $endTime;?>" required="">
		</div>	
	</div>
	
</div>

<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Event Description')?>: <span>*</span></div>
	<div class="sfp-1-Right">
		<textarea name="description" cols="" rows="" class="as-E-textarea" required=""><?php echo isset($description)?$description:'';?></textarea>
	</div>
</div>

<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Door Price')?>: </div>
	<div class="sfp-1-Right">
		<span class="currency"><?php echo $currency;?></span>
		<input name="price_door" type="text" class="post-input" value="<?php echo isset($price_door)?$price_door:'';?>">
	</div>
</div>
<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Online Price')?>: <span>*</span></div>
	<div class="sfp-1-Right">
		<span class="currency"><?php echo $currency;?></span>
		<input name="price_online" type="text" class="post-input" value="<?php echo isset($price_online)?$price_online:'';?>" required="">
	</div>
</div>
<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Discounted Online Price')?>:</div>
	<div class="sfp-1-Right">
		<span class="currency"><?php echo $currency;?></span>
		<input name="price_online_discounted" type="text" class="post-input" value="<?php echo isset($price_online_discounted)?$price_online_discounted:'';?>">
	</div>
</div>
<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Max Prepaid Tickets Available')?>:</div>
	<div class="sfp-1-Right">
		<input name="max_prepaid_tickets" type="text" class="post-input" value="<?php echo isset($max_prepaid_tickets)?$max_prepaid_tickets:'';?>">
	</div>
</div>
<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Price includes')?>:</div>
	<div class="sfp-1-Right">
		<input id="price_includes" name="price_includes" class="FDates-input" type="text" style="height: 40px;" value="<?php echo isset($price_includes)?$price_includes:'';?>">
	</div>
</div>

<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Flyer')?>:</div>
	<div class="sfp-1-Right">
		<div id="img-container">
			<?php if(isset($poster_url)):?>
			<div class="upload-part">
				<img height="150" src="<?php echo $poster_url?>" alt="Poster Url">
			</div>
			<?php endif;?>
		</div>
		<div class="Pf-btnM file-upload mar-top2">
			<span class="upload-button">
				<label><?php echo translate_phrase('Upload Flyer...') ?></label>
				<input type="file" data-url="<?php echo base_url($this->admin_url) ?>/uploadFlayer" id="add_flayer" name="fileToUpload">
			</span>
		</div>
	</div>
</div>

<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Number of people who paid at door')?>:</div>
	<div class="sfp-1-Right">
		<input name="ticket_sold_at_door" type="text" class="post-input" value="<?php echo isset($ticket_sold_at_door)?$ticket_sold_at_door:'';?>">
	</div>
</div>

<div class="sfp-1-main">
	<div class="sfp-1-Left bold"><?php echo translate_phrase('Total cash collected at door')?>:</div>
	<div class="sfp-1-Right">
		<span class="currency"></span>
		<input name="cash_collected_at_door" type="text" class="post-input" value="<?php echo isset($cash_collected_at_door)?$cash_collected_at_door:'';?>">
	</div>
</div>


<div class="Nex-mar Save-mar-width">
<input type="submit" class="Next-butM" value="<?php echo translate_phrase('Save Changes')?>">
</div>
</form>
