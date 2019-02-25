<style>
	.centerTable tr th{ text-align:left;}
	.centerTable tr td{ width:auto; text-align:left;}		
	.centerTable tbody tr td{
	 	padding-top:10px;
	 	text-align:left;
	}	
	.languageDD{width:200px;}
	.languageDD  dt a{width:200px;}
	span.error{
		text-align: left;
		font-size: 16px;
		color: #FF0000 !important;
		float:left;
	}
	.domaindropdown{height:auto;}
	.domaindropdown ul{width:217px;}
	
</style>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
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

$(document).ready(function(){		
	//validate Hidden Fields
	$.validator.setDefaults({ 
		ignore: [],		
	});
	$.each($(".sfp-1-Right"),function(i,item){
		if($(item).hasClass('required'))
		{
			$(item).find('input').attr('required','required');
		}
	});
	$("#add_venue").validate();
	$("#addLanguageRow").unbind().bind('click',function(){
			
		var row_id = 'GENERATED_'+cnt ;
		cnt++;
		
		var CloneRow = $("#listTable tbody tr:first").clone();	
		CloneRow.find('.removeRow').attr('lang',row_id);
				
		CloneRow.find(':input').val("");
		CloneRow.find('.error').text("");
		
		
		CloneRow.find('.languageDD span').text('<?php echo translate_phrase("Select Language");?>');
			
		var TblRow = '<tr style="display:none;" lang="'+row_id+'" >'+CloneRow.html()+'</tr>';			
		
		
		//Append New Generate Row
		$(TblRow).appendTo($("#listTable tbody")).fadeIn("fast");
	});
		
	$(".removeRow").live('click',function(){			
		var rowId= $(this).attr('lang');
		if($("#listTable tbody").find('tr').length > 1)
		{
			$("#listTable tr[lang="+rowId+"]").fadeOut(function(){$(this).remove();});	
		}
		else
		{
			alert('You can not delete all rows');
		}
	});	
	
	$("#cityList dd ul li a").live('click',function(){
		loadNeighborhoodByCity($(this).attr('key'));
	});
	
	//neighborhoodDD
});
function loadNeighborhoodByCity(cityID)
{
	loading();
	$("#loaderImg").fadeIn();
	$.ajax({ 
		url: base_url+"<?php echo $this->admin_url?>/getNeighborhoodByCityId/"+cityID,
		type:"post",
		dataType:'json',
		success: function (response) {					
			
			stop_loading();
			$('.languageDD dd>ul>li>a[key="'+response.default_display_language_id+'"]').click();
			
			$("#loaderImg").fadeOut();
			//Reset Input
			$(".neighborhoodDD dd ul").html('');
			$("#neighborhood_id").val('');
			$("#neighborhood_id").siblings('a').find('span').text('<?php echo translate_phrase("Select Neighborhood");?>');
			if(typeof(response.neighborhoods) != 'undefined' && response.neighborhoods.length > 0)
			{
				$.each(response.neighborhoods, function(i,item){
					
					var VenueItem = '<li><a key="'+item.neighborhood_id+'">'+item.description+'</a></li>';
					$(".neighborhoodDD dd ul").append(VenueItem);
				});
			}
			else
			{
				var VenueItem = '<li class="empty-item"><?php echo translate_phrase("No Neighborhood found.");?></li>'
				$(".neighborhoodDD ul").append(VenueItem);
			}	
	   }
	});		
}

function validateForm()
{
  if($("#add_venue").valid())
  {
  	var searchIDs = $("input[name='language[]']").map(function(){
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
</script>
<style>
	label.error{
		width: 100%;
		float: left;
		clear: both;
	}
</style>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title?></h1>
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="step-form-Main Mar-top-none Top-radius-none active">
					<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field')?></div>
					<form onsubmit="return validateForm();" id="add_venue" name="add_venue" action="" method="post">
						<input name="event_id" type="hidden" value="<?php echo isset($event_id)?$event_id:'';?>" />
						
						<div class="userBox-wrap comn-top-mar">
							<table width="100%" class="centerTable" id="listTable">
								<thead>
									<tr>
										<th width="25%"><?php echo translate_phrase('Display Language')?>: <span class="pink-colr">*</span></th>
										<th width="25%"><?php echo translate_phrase('Venue Name');?>: <span class="pink-colr">*</span></th>
										<th width="40%"><?php echo translate_phrase('Address');?>: <span class="pink-colr">*</span></th>
										<th width="10%"></th>
									</tr>			
								</thead>
								<tbody>
									<tr class="venue_row" id="dummyRow" lang="0">
										<td>
											<?php 
												$select = translate_phrase('Select Language');
												$language_id = isset($display_language_id)?$display_language_id:'';
												
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
													<input type="hidden" name="language[]" value="<?php echo $language_id;?>" required="">					
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
											<input type="text" name="name[]" class="FDates-input" required=""/>
										</td>
										<td>
											<input type="text" name="address[]" class="FDates-input" required=""/>
										</td>
										<td>
											<a href="javascript:;" lang="0" class="removeRow"><?php echo translate_phrase('Remove');?></a>
										</td>
									</tr>
									
								</tbody>
							</table>
							<span id="langError" class="error mar-top2"></span>							
						</div>
						<div class="userTopRowHed">
							<a href="javascript:;" id="addLanguageRow"><?php echo translate_phrase('Add Language');?></a> 
						</div>
							

						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('City')?>: <span>*</span></div>
							<div class="sfp-1-Right required">
								<?php echo form_dt_dropdown('city_id',$cities,$selected_city_id,'class="dropdown-dt domaindropdown cityDD" id="cityList"',translate_phrase('Select City'),"hiddenfield"); ?>
							</div>
						</div>
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('Neighborhood')?>: <span>*</span></div>
							<div class="sfp-1-Right required">
								<?php echo form_dt_dropdown('neighborhood_id',$neighborhoods,$selected_neighborhood_id,'class="dropdown-dt domaindropdown neighborhoodDD"',translate_phrase('Select Neighborhood'),"hiddenfield"); ?>
								<label class="input-alt-lbl" style="display:none" id="loaderImg"><img src="<?php echo base_url('/assets/images/bx_loader.gif')?>" /></label>
							</div>
						</div>
						
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('Phone Number')?>:</div>
							<div class="sfp-1-Right">
								<input value="<?php echo isset($phone_number)?$phone_number:'';?>" id="default_event_url" name="phone_number" class="FDates-input" type="text" placeholder=" e.g. +852 1234-5678" style="height: 40px; width:290px;">
							</div>
						</div>
						<div class="sfp-1-main">
							<div class="sfp-1-Left bold"><?php echo translate_phrase('Website URL')?>:</div>
							<div class="sfp-1-Right">
								<input value="<?php echo isset($website_url)?$website_url:'';?>" id="website_url" name="website_url" class="FDates-input" type="text" placeholder=" e.g. http://www.prive.hk" style="height: 40px; width:290px;">
							</div>
						</div>
						
						<div class="sfp-1-main mar-top2">
							<div class="sfp-1-Left bold"></div>
							<div class="sfp-1-Right btn-group left">
								<input type="submit" class="btn btn-blue" value="<?php echo translate_phrase('Save');?>"> 
								<a class="disable-butn cancel-link" href="javascript:;" onclick="history.back();"><?php echo translate_phrase('Cancel');?></a>								
							</div>
						</div>							
					</form>		
				</div>
			</div>
		</div>
	</div>
</div>