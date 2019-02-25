<style>
	.centerTable tr th{ text-align:left;}
	.centerTable tr td{ width:auto; text-align:left; }	
	.centerTable tbody tr td{
	 	padding-top:10px;
	 	text-align:left;
	}
	.parterDL{width:200px;}	
	.parterDL  dt a{width:205px;}
	.parterDL ul{width:223px;}
	
	#partnerTable .languageDD{width:130px;}
	#partnerTable .languageDD  dt a{width:105px;}
	#partnerTable .languageDD ul{width:128px;}
	
	.dropdown-dt{padding: 0px;}
	.event_url{width:120px;}
	.discount{width:35px;}
</style>
<?php if(isset($is_past_event)&&$is_past_event):?>
<?php else:?>	
<script>
	var cnt = 1;
	$(document).ready(function(){		
		$("#addPartnerRow").unbind().bind('click',function(){
			
			var event_url_id = 'GENERATED_'+cnt ;
			cnt++;
			
			var CloneRow = $("#dummyRowPartner").clone();			
			
			//Set dynamic attr & Names
			CloneRow.find('.removePartner').attr('lang',event_url_id);
			
			CloneRow.find('input[lang=partner_id]').attr('name','event_url['+event_url_id+'][partner_id]');
			CloneRow.find('input[lang=url]').attr('name','event_url['+event_url_id+'][url]');
			CloneRow.find('input[lang=discount_amount]').attr('name','event_url['+event_url_id+'][discount_amount]');
			CloneRow.find('.languageDD input[type=hidden]').attr('name','event_url['+event_url_id+'][display_language_id]');
			
			var PartnerRow = '<tr style="display:none;" lang="'+event_url_id+'" >'+CloneRow.html()+'</tr>';			
			
			//Append New Generate Row
			$(PartnerRow).appendTo($("#partnerTable tbody")).fadeIn("fast");
		});
		
		$(".removePartner").live('click',function(){			
			var event_url_id = $(this).attr('lang');
			if(isNaN(event_url_id))
			{
				$("#partnerTable tr[lang="+event_url_id+"]").fadeOut(function(){$(this).remove();});
			}
			else{
				loading();
				$.ajax({ 
					url: base_url+"<?php echo $this->admin_url?>/delete_event_partner/",
					type:"post",
					data:{'event_url_id':event_url_id},
					dataType:'json',
					success: function (response) {					
						stop_loading();
						if(response.type == 'success')
							$("#partnerTable tr[lang="+event_url_id+"]").fadeOut(function(){$(this).remove();});
				   }
				});	
			}
						
		});
		
		
		$('ul.partnerList > li > a').live('click',function(){
			
			var default_display_language_id = $(this).attr('default_display_language_id');
			var default_event_url = $(this).attr('default_event_url');
			
			var row = $(this).parent().parent().parent().parent().parent().parent();
			row.find('.event_url').val(default_event_url);			
			
			row.find('.languageDD dd>ul>li>a[key="'+default_display_language_id+'"]').click();
		});
	});
</script>
<?php endif;?>
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
			$currency = $value['currency_description'];
		}	
	}
}
?>
<form method="post" id="event_partners" name="event_partners" action="<?php echo base_url($this->admin_url.'/save_event_partner')?>">
<input type="hidden" name="event_id" value="<?php echo isset($event_id)?$event_id:'';?>" />	
		
	<div class="userBox-wrap comn-top-mar">
		<table width="100%" class="centerTable" id="partnerTable">
			<thead>
				<tr>
					<th width="30%"><?php echo translate_phrase('Partner')?></th>
					<th width="40%"><?php echo translate_phrase('URL Shortcut');?></th>
					<th width="20%"><?php echo translate_phrase('Display Language');?></th>
					<th width="25%"><?php echo translate_phrase('Discount');?></th>
					<?php if(isset($is_past_event)&&$is_past_event):?>
					<?php else:?>	
					<th width="5%">&nbsp;</th>
					<?php endif;?>
				</tr>			
			</thead>
			<tbody>
				<?php if(isset($event_urls) && $event_urls):?>
					<?php foreach($event_urls as $event_url):?>
						<tr class="event_url_row" lang="<?php echo $event_url['event_url_id']?>">
							<td>
								<?php 					
								if(isset($partners) && $partners):
									
									$select = translate_phrase('Select Partner');						
									
									foreach($partners as $partner)
									{
										if($partner['partner_id'] == $event_url['partner_id'])
										{
											$select = $partner['name'];
										}	
									}
								?>
								<?php if(isset($is_past_event)&&$is_past_event):?>
									<?php echo $select;?>
								<?php else:?>	
								<dl class="dropdown-dt domaindropdown parterDL">
									<dt>
										<a href="javascript:;"><span><?php echo $select;?></span></a>
										<input type="hidden" name="event_url[<?php echo $event_url['event_url_id']?>][partner_id]" value="<?php echo $event_url['partner_id']?>" required="">
									</dt>
									<dd>
										<ul style="display: none;" class="partnerList">
											<?php foreach($partners as $partner):?>
											<li><a default_display_language_id="<?php echo $partner['default_display_language_id'];?>" default_event_url="<?php echo $partner['default_event_url'];?>"  key="<?php echo $partner['partner_id'];?>"><?php echo $partner['name'];?></a></li>
											<?php endforeach;?>													
										</ul>
									</dd>							
								</dl>
								<?php endif;?>
								<?php endif;?>
							</td>
							
							<td>
								<?php if(isset($is_past_event)&&$is_past_event):?>
									<?php echo $event_url['url']?>
								<?php else:?>	
								<span class="input-label">www.datetix.com/</span>
								<input  type="text" name="event_url[<?php echo $event_url['event_url_id']?>][url]" value="<?php echo $event_url['url']?>" class="input-full event_url" required=""/>
								<?php endif;?>
							</td>
							<td>
								<?php if(isset($is_past_event)&&$is_past_event):?>
									<?php 
										if($languages){
											foreach($languages as $lang_id=>$lang)
											{
												if($lang_id == $event_url['display_language_id'])
												{
													echo $lang;	
												}
											}
										}
									?>
								<?php else:?>
								<?php echo form_dt_dropdown("event_url[".$event_url['event_url_id']."][display_language_id]",$languages,$event_url['display_language_id'],'class="dropdown-dt domaindropdown languageDD"',translate_phrase('Select Language'),"hiddenfield",""); ?>
								<?php endif;?>
							</td>
							<td>
								<span class="currency"><?php echo $currency ;?></span>
								<?php if(isset($is_past_event)&&$is_past_event):?>
									<?php echo $event_url['discount_amount']?>
								<?php else:?>	
								<input type="text" name="event_url[<?php echo $event_url['event_url_id']?>][discount_amount]" value="<?php echo $event_url['discount_amount']?>" class="input-full discount" required=""/>
								<?php endif;?>
							</td>
							<td>
								<?php if(isset($is_past_event)&&$is_past_event):?>
									
								<?php else:?>	
								<input type="hidden" name="event_url[<?php echo $event_url['event_url_id']?>][event_url_id]" value="<?php echo $event_url['event_url_id']?>"/>
								<a href="javascript:;" lang="<?php echo $event_url['event_url_id']?>" class="removePartner"><?php echo translate_phrase('Remove');?></a>
								<?php endif;?>
							</td>
						</tr>
					<?php endforeach;?>
				<?php endif;?>
			</tbody>
		</table>
	</div>
	<?php if(isset($is_past_event)&&$is_past_event):?>
	<?php else:?>	
	<div class="userTopRowHed">
		<a href="javascript:;" id="addPartnerRow"><?php echo translate_phrase('Add Partner');?></a> 
		&nbsp; &nbsp;  &nbsp; &nbsp;  
		<a href="<?php echo base_url($this->admin_url.'/add_new_partner');?>"><?php echo translate_phrase('Create Partner')?></a>	
	</div>

	<div class="Nex-mar Save-mar-width">
		<input type="submit" class="Next-butM" value="<?php echo translate_phrase('Save Changes')?>">
	</div>
	<?php endif;?>
</form>
<!-- DUMY ROW USED FOR DYNAMIC ROW CREATION -->
<table>
<tr class="hidden event_url_row" id="dummyRowPartner" lang="0">
	<td>
		<?php 					
		if(isset($partners) && $partners):
		
			$select = translate_phrase('Select Partner');						
		?>
		<dl class="dropdown-dt domaindropdown parterDL">
			<dt>
				<a href="javascript:;"><span><?php echo $select;?></span></a>
				<input type="hidden" lang="partner_id" value="" required="">
			</dt>
			<dd>
				<ul style="display: none;" class="partnerList">
					<?php foreach($partners as $partner):?>
					<li><a default_display_language_id="<?php echo $partner['default_display_language_id'];?>" default_event_url="<?php echo $partner['default_event_url'];?>" key="<?php echo $partner['partner_id'];?>"><?php echo $partner['name'];?></a></li>
					<?php endforeach;?>													
				</ul>
			</dd>							
		</dl>
		<?php endif;?>
	</td>
	<td>
		<span class="input-label">www.datetix.com/</span>
		<input type="text" lang="url" class="input-full event_url" required=""/>
	</td>
	<td>
		<?php echo form_dt_dropdown('',$languages,$this->language_id,'class="dropdown-dt domaindropdown languageDD"',translate_phrase('Select Language'),"hiddenfield",""); ?>
	</td>
	<td>
		<span class="currency"><?php echo $currency ;?></span>
		<input type="text" lang="discount_amount" class="input-full discount"/>						
	</td>
	
	<td>
		<a href="javascript:;" lang="0" class="removePartner"><?php echo translate_phrase('Remove');?></a>
	</td>
</tr>
</table>