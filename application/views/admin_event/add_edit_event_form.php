<?php if(isset($is_past_event)&&$is_past_event):?>
<?php else:?>
<link href="<?php echo base_url()?>assets/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
<script  type="text/javascript" src="<?php echo base_url()?>assets/datetimepicker/jquery.datetimepicker.js"></script>
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

	
	span.error{
		text-align: left;
		font-size: 16px;
		color: #FF0000 !important;
		float:left;
	}
	.domaindropdown{height:auto;}
	.domaindropdown ul{width:217px;}
	
	#listTable .languageDD{width:130px;}
	#listTable .languageDD  dt a{width:105px;}
	#listTable .languageDD ul{width:128px;}
	
	#listTable .as-E-textarea{width:98%; padding:0px;}
	#listTable .file-upload{width:98%; padding:0px; text-align:center;}
	.file-upload .upload-button{float:none;min-width:25px;}
</style>
<script type="text/javascript">
var cnt = 1;
var duplicateLang = 0;
function checkIfArrayIsUnique(arr) {
    var map = {}, i, size;

    for (i = 0, size = arr.length; i < size; i++){
        if (map[arr[i]]){
        	duplicateLang = arr[i];
            return false;
        }
        map[arr[i]] = true;
    }
    return true;
}
function validateForm()
{
  if($("#add_edit_event").valid())
  {
  	var searchIDs = $("#listTable input[name='event_language[display_language_id][]']").map(function(){
      return $(this).val();
    }).get(); // <----    
	if(checkIfArrayIsUnique(searchIDs) == false)
	{
		var dupLang = $('#listTable tr:first-child .languageDD dd>ul>li>a[key="'+duplicateLang+'"]').text();
		$("#langError").text('<?php echo translate_phrase("You added the language ");?>'+dupLang+'<?php echo translate_phrase(" more than once. Please remove the duplicate rows");?>');
		$("#langError").show();
		return false;
	}
	else
	{
		$("#langError").hide();
		return true;
	}
  }
  else
  {
  	return true;
  }
}
function loadVenuesByCity(cityID)
	{
		loading();
		$("#loaderImg").fadeIn();
		$.ajax({ 
			url: base_url+"<?php echo $this->admin_url?>/getVenuesByCityId/"+cityID,
			type:"post",
			dataType:'json',
			success: function (response) {					
				stop_loading();
				$('.languageDD dd>ul>li>a[key="'+response.default_display_language_id+'"]').click();
				$("#loaderImg").fadeOut();
				//Reset Input
				$("#venueList").html('');
				$("#venue_id").val('');
				$("#venue_id").siblings('a').find('span').text('<?php echo translate_phrase("Select venue");?>');
				if(typeof(response.venues) != 'undefined' && response.venues.length > 0)
				{
					$.each(response.venues, function(i,item){						
						var VenueItem = '<li><a key="'+item.venue_id+'">'+item.name+'</a></li>';
						$("#venueList").append(VenueItem);
					});
				}
				else
				{
					var VenueItem = '<li class="empty-item"><?php echo translate_phrase("No Venue found.");?></li>'
					$("#venueList").append(VenueItem);
				}	
		   }
		});		
	}

Date.prototype.addHours= function(h){
    this.setHours(this.getHours()+h);
    return this;
}

$(document).ready(function(){
	$.validator.setDefaults({ 
		ignore: [],		
	});
	$("#add_edit_event").validate()
	$.each($(".sfp-1-Right"),function(i,item){
		if($(item).hasClass('required'))
		{
			$(item).find('input').attr('required','required');
		}
	});
		

	$("#addLanguageRow").unbind().bind('click',function(){
			
		var row_id = 'GENERATED_'+cnt ;
		cnt++;
		
		var CloneRow = $("#listTable tbody tr:first").clone();	
		CloneRow.find('.removeEventLangRow').attr('lang',row_id);			
		
		CloneRow.find('.error').text("");
		CloneRow.find('.upload-part').html("");
		CloneRow.find('.languageDD span').text('<?php echo translate_phrase("Select Language");?>');
		CloneRow.find('.languageDD :input').val('');			
		CloneRow.find('.FDates-input').val('');
				
		var textAreaId = 'description_'+row_id;
		
		$(CloneRow).find('textarea').parent().html('<div id="'+textAreaId+'"></div>');	
		
		var TblRow = '<tr style="display:none;" lang="'+row_id+'" >'+CloneRow.html()+'</tr>';	
		//Append New Generate Row
		
		$(TblRow).appendTo($("#listTable tbody")).fadeIn("fast",function(){
			var textAreaDiv = $("#dummyTextArea").clone();
			textAreaDiv.find('textarea').attr('id',textAreaId).addClass('ckeditor');
		
			$('#'+textAreaId).replaceWith(textAreaDiv.html());
			var name = textAreaId;			
			var editor = CKEDITOR.instances[name];
		    if (editor) { editor.destroy(true); }
		    CKEDITOR.replace(name);
		    
		});
		
	});
	
	$(".removeEventLangRow").live('click',function(){			
		var rowId= $(this).attr('lang');
		
		var event_lang_data = rowId.split('--');
		var event_id = event_lang_data[0];
		var lang_id = event_lang_data[1];
		
		
		if($("#listTable tbody").find('tr').length > 1)
		{
			if(typeof(lang_id) == 'undefined' || isNaN(lang_id))
			{
				$("#listTable tr[lang="+rowId+"]").fadeOut(function(){$(this).remove();});	
			}
			else{
				loading();
				$.ajax({ 
					url: base_url+"<?php echo $this->admin_url?>/delete_event_language/",
					type:"post",
					data:{'display_language_id':lang_id,'event_id':event_id},
					dataType:'json',
					success: function (response) {					
						stop_loading();
						if(response.type == 'success')
							$("#listTable tr[lang="+rowId+"]").fadeOut(function(){$(this).remove();});
				   }
				});	
			}
		}
		else
		{
			alert('You can not delete all rows');
		}
	});	
	
	
	$('.datepicker').datetimepicker({
		timepicker:false,
		format:'Y-m-d',
		minDate:0,
		onSelectDate: function(ct,$i){
			if($i.attr('name') == 'event_start_date')
		 		$('.datepicker').val(ct.dateFormat('Y-m-d'));
	    }
	});
	$('.timepicker').datetimepicker({
	  datepicker:false,
	  format:'H:i',
	  step:30, 
	  onSelectTime: function(ct,$i){
	  		if($i.attr('name') == 'event_start_time')
		 		$(':input[name=event_end_time]').val(ct.addHours(3).dateFormat('H:i'));
	    } 
	});
	$("#cityList li a").live('click',function(){
		$(".currency").text($(this).attr('currency_name'));
		
		changeDatetixURL();
		loadVenuesByCity($(this).attr('key'));
	});	
	
	//Load url when page load
	changeDatetixURL();
	
	 $("#listTable :file").live('change',function (e) {
	 	 if (this.files && this.files[0]) {
         	var reader = new FileReader();
         	var previewImg = $(this).parent().parent().siblings('.upload-part');
			 reader.onload = function(e){
			 	previewImg.html('<img src="'+e.target.result+'" />')
			 };
            reader.readAsDataURL(this.files[0]);
        }
        
    });
    
});
function imageIsLoaded(e) {
	
};
function changeDatetixURL()
{
	var city_id = $("#city_id").val();		
	var datetixURL = "";
	//Hongkong
	if(city_id == '260')
	{
		datetixURL = 'www.datetix.hk/';
	}
	else
	{
		datetixURL = 'www.datetix.com/';
	}
	$(".datetix-url").text(datetixURL)		
}
</script>
<?php endif;?>
<div id="dummyTextArea" style="visibility: hidden; display: none;">
	<textarea name="event_language[description][]" class="as-E-textarea"></textarea>
</div>
<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field')?></div>

	<form id="add_edit_event" name="add_edit_event" action="" onsubmit="return validateForm()" method="post" enctype="multipart/form-data">
	<input type="hidden" name="event_id" value="<?php echo isset($event_id)?$event_id:'';?>" />
	
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('City')?>: <span>*</span></div>
		<div class="sfp-1-Right">		
			<?php 
			$selected_city_id = isset($city_id)?$city_id:"";
			
			$currency = "";
			$select = translate_phrase('Select city');
			if($selected_city_id)
			{
				foreach($cities as $id=>$value)
				{
					if($value['city_id'] == $selected_city_id)
					{
						$select = $value['description'];
						$currency = $value['currency_description'];
					}	
				}
			}
			
			if(isset($is_past_event)&&$is_past_event):?>
				<?php 
					echo '<input name="venue_city_id" type="hidden" value="'.$selected_city_id.'">';
					echo '<label class="input-alt-lbl">'.$select.'</label>';	
							
				?>
			<?php else:?>
			
			<dl name="drop_city_id" class="dropdown-dt domaindropdown cityDropDown">
				<dt>
					<a href="javascript:;" key="<?php echo $selected_city_id;?>"><span><?php echo $select;?></span></a>												
					<input name="venue_city_id" type="hidden" id="city_id" value="<?php echo $selected_city_id;?>" required="">
				</dt>
				<dd>
					<ul style="display: none;" id="cityList">
						<?php if(isset($cities) && $cities):?>
						<?php foreach($cities as $city):?>
							<li><a currency_name="<?php echo $city['currency_description'];?>" currency_id="<?php echo $city['currency_id'];?>" key="<?php echo $city['city_id'];?>"><?php echo $city['description'];?></a></li>
						<?php endforeach;?>
						<?php else:?>
						<li class="empty-item"><?php echo translate_phrase('No city found.');?></li>
						<?php endif;?>							
					</ul>
				</dd>				
			</dl>			
			<?php endif;?>		
		</div>
	</div>

	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Venue')?>: <span>*</span></div>
		<div class="sfp-1-Right">
				<?php 
					$venue_id = isset($venue_id)?$venue_id:($this->input->post('venue_id') ? $this->input->post('venue_id') : "");
					$select = translate_phrase('Select venue');
					if($venue_id)
					{
						foreach($venues as $vanue)
						{
							if($vanue['venue_id'] == $venue_id)
							{
								$select = $vanue['name'];
							}	
						}
					}
				?>
				<?php if(isset($is_past_event)&&$is_past_event):?>
				<label class="input-alt-lbl"><?php echo $select;?></label>
				<?php else:?>
				<dl name="drop_venue_id" class="dropdown-dt domaindropdown">
					<dt>
						<a href="javascript:;" key="<?php echo $venue_id;?>"><span><?php echo $select;?></span></a>												
						<input type="hidden" id="venue_id" name="venue_id" value="<?php echo $venue_id;?>" required="">					
					</dt>
					<dd>
						<ul style="display: none;" class="selectVenue" id="venueList">
							<?php if(isset($venues) && $venues):?>
							<?php foreach($venues as $vanue):?>
							<li><a key="<?php echo $vanue['venue_id'];?>"><?php echo $vanue['name'];?></a></li>
							<?php endforeach;?>
							<?php else:?>
							<li class="empty-item"><?php echo translate_phrase('No Venue found.');?></li>
							<?php endif;?>							
						</ul>
					</dd>				
				</dl>
				<label class="input-alt-lbl" style="display:none" id="loaderImg"><img src="<?php echo base_url('/assets/images/bx_loader.gif')?>" /></label>
				<label class="input-alt-lbl"><a href="<?php echo base_url($this->admin_url.'/create_venue')?>?city_id=<?php echo $selected_city_id;?>"><?php echo translate_phrase('Create Venue');?></a></label>
				<?php endif;?>
				
		</div>
	</div>
	
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Start Time')?>: <span>*</span></div>
		<div class="sfp-1-Right">
			<div class="fl">
				<?php 
					$startDate = isset($event_start_time)?date('Y-m-d',strtotime($event_start_time)):'';
					$startTime = isset($event_start_time)?date('H:i',strtotime($event_start_time)):'';											
				?>
				
				<?php if(isset($is_past_event)&&$is_past_event):?>
					<label class="input-alt-lbl"><?php echo $startDate;?></label>
				<?php else:?>
					<input name="event_start_date" type="text" class="post-input datepicker" value="<?php echo $startDate;?>" required="">
				<?php endif;?>
			</div>
			<span style="padding: 0px 5px; float:left;">&nbsp;</span>
			<div class="fl">
				
			<?php if(isset($is_past_event)&&$is_past_event):?>
				<label class="input-alt-lbl"><?php echo $startTime;?></label>
			<?php else:?>
				<input name="event_start_time" type="text" class="post-input timepicker" value="<?php echo $startTime;?>" required="">
			<?php endif;?>
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
				
				<?php if(isset($is_past_event)&&$is_past_event):?>
					<label class="input-alt-lbl"><?php echo $endDate;?></label>
				<?php else:?>
					<input name="event_end_date" type="text" class="post-input datepicker" value="<?php echo $endDate;?>" required="">
				<?php endif;?>
				
			</div>
			<span style="padding: 0px 5px; float:left;">&nbsp;</span>
			<div class="fl">
				<?php if(isset($is_past_event)&&$is_past_event):?>
					<label class="input-alt-lbl"><?php echo $endTime;?></label>
				<?php else:?>
					<input name="event_end_time" type="text" class="post-input timepicker" value="<?php echo $endTime;?>" required="">
				<?php endif;?>
			</div>	
		</div>
		
	</div>
	
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Online Price')?>: <span>*</span></div>
		<div class="sfp-1-Right">
					
			<?php if(isset($is_past_event)&&$is_past_event):?>
				<span class="currency"><?php echo isset($price_online)&&$price_online?$currency:'';?></span>
				<label class="input-alt-lbl"><?php echo isset($price_online)&&$price_online?$price_online:'';?></label>
			<?php else:?>
				<span class="currency"><?php echo $currency;?></span>
				<input name="price_online" type="text" class="post-input" value="<?php echo isset($price_online)?$price_online:'';?>" required="">
			<?php endif;?>		
		</div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Discounted Online Price')?>: <span>*</span></div>
		<div class="sfp-1-Right">
			<?php if(isset($is_past_event)&&$is_past_event):?>
				<span class="currency"><?php echo isset($price_online_discounted)&&$price_online_discounted?$currency:'';?></span>
				<label class="input-alt-lbl"><?php echo isset($price_online_discounted)&&$price_online_discounted?$price_online_discounted:'';?></label>
			<?php else:?>
				<span class="currency"><?php echo $currency;?></span>
				<input name="price_online_discounted" type="text" class="post-input" value="<?php echo isset($price_online_discounted)?$price_online_discounted:'';?>">
			<?php endif;?>
		</div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Door Price')?>: </div>
		<div class="sfp-1-Right">					
			<?php if(isset($is_past_event)&&$is_past_event):?>
				<span class="currency"><?php echo  (isset($price_door)&&$price_door)?$currency:'';?></span>
				<label class="input-alt-lbl"><?php echo (isset($price_door)&&$price_door)?$price_door:'';?></label>
			<?php else:?>
				<span class="currency"><?php echo $currency;?></span>
				<input name="price_door" type="text" class="post-input" value="<?php echo isset($price_door)?$price_door:'';?>">
			<?php endif;?>
		</div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Max Prepaid Tickets Available')?>:</div>
		<div class="sfp-1-Right">
			<?php if(isset($is_past_event)&&$is_past_event):?>
				<label class="input-alt-lbl"><?php echo isset($max_prepaid_tickets)?$max_prepaid_tickets:'';?></label>
			<?php else:?>
				<input name="max_prepaid_tickets" type="text" class="post-input" value="<?php echo isset($max_prepaid_tickets)?$max_prepaid_tickets:'';?>">
			<?php endif;?>		
		</div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Price Includes')?>:</div>
		<div class="sfp-1-Right">
			<?php if(isset($is_past_event)&&$is_past_event):?>
				<label class="input-alt-lbl"><?php echo isset($price_includes)?$price_includes:'';?></label>
			<?php else:?>
				<input placeholder="e.g. 1 free drink and 1 guaranteed date from DateTix app" id="price_includes" name="price_includes" class="FDates-input" type="text" style="height: 40px;" value="<?php echo isset($price_includes)?$price_includes:'';?>">
			<?php endif;?>
		</div>
	</div>
	
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('No. People Paid at Door')?>:</div>
		<div class="sfp-1-Right">		
			<input name="tickets_sold_at_door" type="text" class="post-input" value="<?php echo isset($tickets_sold_at_door)?$tickets_sold_at_door:'';?>">
		</div>
	</div>
	
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('Total Cash Collected at Door')?>:</div>
		<div class="sfp-1-Right">		
			<span class="currency"><?php echo $currency;?></span>
				<input name="cash_collected_at_door" type="text" class="post-input" value="<?php echo isset($cash_collected_at_door)?$cash_collected_at_door:'';?>">
		</div>
	</div>
	<div class="sfp-1-main">
		<div class="sfp-1-Left bold"><?php echo translate_phrase('URL Shortcut')?>:</div>
		<div class="sfp-1-Right">
				<input name="shortcut_url" type="text" class="post-input" value="<?php echo isset($shortcut_url)?$shortcut_url:'';?>">
		</div>
	</div>
		
	<div class="userBox-wrap comn-top-mar">
		<table width="100%" class="centerTable" id="listTable">
			<thead>
				<tr>
					<th width="15%"><?php echo translate_phrase('Display Language')?>: <span class="pink-colr">*</span></th>
					<th width="25%"><?php echo translate_phrase('Event Name');?>: <span class="pink-colr">*</span></th>
					<th width="45%"><?php echo translate_phrase('Event Description');?>: <span class="pink-colr">*</span></th>
					<th width="10%"><?php echo translate_phrase('Flyer');?>: <span class="pink-colr">*</span></th>
					<th width="5%"></th>
				</tr>			
			</thead>
			<tbody>
				<?php if(isset($event_languages) && $event_languages):?>
					<?php foreach($event_languages as $event_language):?>
						<?php 
						$rowId = $event_language['event_id'].'--'.$language_id;
						$select = translate_phrase('Select Language');
						$language_id = $event_language['display_language_id'];
						if($language_id)
						{
							foreach($languages as $key=>$value)
							{
								if($key == $language_id)
								{
									$select = $value;
								}	
							}
						}
						$event_name = $event_language['event_name'];
						$description = $event_language['description'];
						?>
						<tr lang="<?php echo $rowId;?>">
							<td>
								
								<dl class="dropdown-dt domaindropdown languageDD">
									<dt>
										<a href="javascript:;" key="<?php echo $language_id;?>"><span><?php echo $select;?></span></a>
										<input type="hidden" name="event_language[display_language_id][]" value="<?php echo $language_id;?>" required="">					
									</dt>
									<dd>
										<ul style="display: none;" class="selectVenue" id="venueList">
											<?php if(isset($languages) && $languages):?>
											<?php foreach($languages as $key=>$value):?>
											<li><a key="<?php echo $key;?>"><?php echo $value;?></a></li>
											<?php endforeach;?>
											<?php else:?>
											<li class="empty-item"><?php echo translate_phrase('No Language found.');?></li>
											<?php endif;?>							
										</ul>
									</dd>				
								</dl>
							</td>
							<td>
								<?php if(isset($is_past_event)&&$is_past_event):?>
								<label class="input-alt-lbl"><?php echo isset($event_name)?$event_name:'';?></label>	
								<?php else:?>
								<input value="<?php echo isset($event_name)?$event_name:'';?>" name="event_language[event_name][]" class="FDates-input" type="text" style="height: 40px;" required="">
								<?php endif;?>								
							</td>							
							<td>
								<?php if(isset($is_past_event)&&$is_past_event):?>
									<label class="input-alt-lbl"><?php echo isset($description)?$description:'';?></label>
								<?php else:?>
									<textarea id="description_<?php echo rand()?>" name="event_language[description][]" class="as-E-textarea ckeditor" required=""><?php echo isset($description)?$description:'';?></textarea>
									<label class="error-msg error" for="description"></label>
								<?php endif;?>						
							</td>
							<td>
								<div class="upload-part">
									<img src="<?php echo $event_language['flyer_url']?>" />
								</div>
								<div class="Pf-btnM file-upload">
									<span class="upload-button">
										<label><?php echo translate_phrase('Upload...');?></label>
										<input type="file" name="flyer_url[]">
									</span>
								</div>						
							</td>					
							<td>
								<a href="javascript:;" lang="<?php echo $rowId;?>" class="removeEventLangRow"><?php echo translate_phrase('Remove');?></a>
							</td>
						</tr>
					<?php endforeach;?>
				<?php else:?>	
					<tr lang="0">
						<td>
							<?php 
								$select = translate_phrase('Select Language');
								if($language_id)
								{
									foreach($languages as $key=>$value)
									{
										if($key == $language_id)
										{
											$select = $value;
										}	
									}
								}
							?>
							<dl class="dropdown-dt domaindropdown languageDD">
								<dt>
									<a href="javascript:;" key="<?php echo $language_id;?>"><span><?php echo $select;?></span></a>
									<input type="hidden" name="event_language[display_language_id][]" value="<?php echo $language_id;?>" required="">					
								</dt>
								<dd>
									<ul style="display: none;" class="selectVenue" id="venueList">
										<?php if(isset($languages) && $languages):?>
										<?php foreach($languages as $key=>$value):?>
										<li><a key="<?php echo $key;?>"><?php echo $value;?></a></li>
										<?php endforeach;?>
										<?php else:?>
										<li class="empty-item"><?php echo translate_phrase('No Language found.');?></li>
										<?php endif;?>							
									</ul>
								</dd>				
							</dl>											
						</td>
						<td>
							<input value="" name="event_language[event_name][]" class="FDates-input" type="text" style="height: 40px;" required="">
						</td>
						<td>
							<textarea id="description_<?php echo rand()?>" name="event_language[description][]" class="as-E-textarea ckeditor" required=""></textarea>
						</td>
						<td>
							<div class="upload-part"></div>
							<div class="Pf-btnM file-upload">
								<span class="upload-button">
									<label><?php echo translate_phrase('Upload...');?></label>
									<input class="flyerImg" type="file" name="flyer_url[]">
								</span>
							</div>						
						</td>					
						<td>
							<a href="javascript:;" lang="0" class="removeEventLangRow"><?php echo translate_phrase('Remove');?></a>
						</td>
				</tr>
				<?php endif;?>				
			</tbody>
		</table>
		<span id="langError" class="error mar-top2"></span>							
	</div>
	<div class="userTopRowHed">
		<a href="javascript:;" id="addLanguageRow"><?php echo translate_phrase('Add Language');?></a> 
	</div>
	<?php
		if(isset($event_id) && $event_id)
		{
			$btnTxt = 'Save Changes';	
		}
		else {
			$btnTxt = 'Create Event';
		}
	?>
	<div class="btn-group mar-top2">
		<input type="submit" class="btn btn-blue" value="<?php echo translate_phrase($btnTxt)?>">
		<a class="disable-butn cancel-link" href="<?php echo base_url($this->admin_url)?>"><?php echo translate_phrase('Cancel');?></a>
	</div>
</form>