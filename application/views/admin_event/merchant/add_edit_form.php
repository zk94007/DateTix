<style>	
label.error{
	width: 100%;
	float: left;
	clear: both;
}
.domaindropdown{height:auto;}
span.error{
	text-align: left;
	font-size: 16px;
	color: #FF0000 !important;
	float:left;
}
.FDates-input{width:290px;}
.sfp-1-Right .italic{line-height:44px; margin-left:10px;}
.file-upload .upload-button{float:none;min-width:25px;}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$.validator.setDefaults({ 
		ignore: [],		
	});
	$("#add_edit_form").validate()
	$.each($(".sfp-1-Right"),function(i,item){
		if($(item).hasClass('required'))
		{
			$(item).find('input').attr('required','required');
		}
	});
	
	$(".toggleBtn a").live('click',function(){
		$(this).parent().parent().find('.Intro-Button-sel').removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');		
		$(this).removeClass('Intro-Button').parent().addClass('Intro-Button-sel');		
		$(this).parent().parent().parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});
});

function validateForm()
{
	return $("#add_edit_form").valid();
}
</script>
<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field')?></div>

	<form id="add_edit_form" name="add_edit_form" action="" onsubmit="return validateForm()" method="post" enctype="multipart/form-data">
	<input type="hidden" name="merchant_id" value="<?php echo isset($merchant_id)?$merchant_id:'';?>" />
	
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Name')?>: <span>*</span></div>
		<div class="sfp-1-Right">
			<input name="name" class="FDates-input" type="text" style="height: 40px;" value="<?php echo isset($name)?$name:'';?>" required="">
		</div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Neighborhood')?>: <span>*</span></div>
		<div class="sfp-1-Right required">
			<?php echo form_dt_dropdown('neighborhood_id',$neighborhood_dropdown_data,$selected_neighborhood_id,'class="dropdown-dt domaindropdown"',translate_phrase('Select Neighborhood'),"hiddenfield"); ?>
		</div>		
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Budget')?>: <span>*</span></div>
		<div class="sfp-1-Right required">
			<?php echo form_dt_dropdown('budget_id',$budget_dropdown_data,isset($budget_id)?$budget_id:0,'class="dropdown-dt domaindropdown"',translate_phrase('Select Budget'),"hiddenfield"); ?>
		</div>		
	</div>
	
	<div class="sfp-1-main"><div class="sfp-1-Left bold"><?php echo translate_phrase('Price Range')?>:</div>
	<div class="sfp-1-Right">
		<input name="price_range" type="text" class="post-input" value="<?php echo isset($price_range)?$price_range:'';?>" > </div>
	</div>
	
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Address')?>: <span>*</span></div>
		<div class="sfp-1-Right">
			<input name="address" type="text" class="FDates-input" value="<?php echo isset($address)?$address:'';?>" required="">
		</div>
	</div>
	
	<div class="sfp-1-main"><div class="sfp-1-Left bold"><?php echo translate_phrase('Phone Number')?>: </div>
	<div class="sfp-1-Right">
		<input name="phone_number" type="text" class="post-input" value="<?php echo isset($phone_number)?$phone_number:'';?>" > </div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Website URL')?>:</div>
		<div class="sfp-1-Right">
				<input name="website_url" type="text" class="FDates-input" value="<?php echo isset($website_url)?$website_url:'';?>">
		</div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Review URL')?>:</div>
		<div class="sfp-1-Right">
				<input name="review_url" type="text" class="FDates-input" value="<?php echo isset($review_url)?$review_url:'';?>">
		</div>
	</div>	
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Tags')?>:</div>
		<div class="sfp-1-Right align-left">
			<input name="tags" type="text" class="FDates-input" value="<?php echo isset($tags)?$tags:'';?>">
			<span class="italic"><?php echo translate_phrase('Enter comma delimited values');?></span>
		</div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Featured')?>: <span>*</span></div>
		<div class="sfp-1-Right align-left">
			
			<div class="f-decr toggleBtn">
				<ul>
					<?php if(isset($is_featured)&&$is_featured==1):?>
					<li class="Intro-Button-sel"><a class="" href="javascript:;" key="1">Yes</a></li>
					<?php else:?>
					<li class=""> <a class="Intro-Button" href="javascript:;" key="1">Yes</a></li>
					<?php endif;?>
					
					<?php if(isset($is_featured)&&$is_featured==0):?>
					<li class="Intro-Button-sel"><a class="" href="javascript:;" key="1">No</a></li>
					<?php else:?>
					<li class=""> <a class="Intro-Button" href="javascript:;" key="0">No</a></li>
					<?php endif;?>
					
				</ul>
				<input type="hidden" name="is_featured" id="is_featured" value="<?php echo isset($is_featured)?$is_featured:''?>">
			</div>

		</div>
	</div>
	
	<?php
		if(isset($merchant_id) && $merchant_id)
		{
			$btnTxt = 'Save Changes';	
		}
		else {
			$btnTxt = 'Create Merchant';
		}
	?>
	<div class="btn-group mar-top2">
		<input type="submit" class="btn btn-blue" value="<?php echo translate_phrase($btnTxt)?>">
		<?php $params = '?city_id='.$this->input->get('city_id').'&neighborhood_id='.$this->input->get('neighborhood_id');?>
		<a class="disable-butn cancel-link" href="<?php echo base_url($this->admin_url.'/merchant_list'.$params)?>"><?php echo translate_phrase('Cancel');?></a>
	</div>
</form>