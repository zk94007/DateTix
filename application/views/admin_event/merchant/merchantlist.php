<style>
	.border-list li{border-bottom:1px dotted #C0C0C0; padding:5px 0px}
	.border-list li:last-child{border:none}
	.language-part.top{ width:auto; float:none; }
	.language-part.top a{ color:black;}
	.tbl_order tr:hover {
          cursor: pointer;
    }
    .tbl_order tr:hover td{
    	background-color: #d2d2d2;
    }
</style>
<script type="text/javascript">
	var page_no = "<?php echo $page_no ?>";
	var preventAjaxCall = [];
	
	$(document).ready(function(){
		$(".city_dropdown dd ul li a").live('click',function () {
			$("#city_form").submit();
		});
	});
	//Lazzy Load Pagination..
	$(window).scroll(function() {  
		var totalScrollAmount = $(window).scrollTop() + $(window).height() + 80;
		if( totalScrollAmount >= $(document).height()) 
		{
			load_more_data('append')
		}
	});
	function load_more_data(domAction)
	{
		var currentTab = 'merchantlist';
		var offset = parseInt(page_no)+1;
		
		var PostData = {'page_no':offset};			
		if(preventAjaxCall.indexOf(currentTab) == -1 && isAjaxCallRunning == false)
		{
			//$("#"+currentTab+"_container").append('<div class="div_data_loader"></div>');
			loading();
			$.ajax({ 
				url: "<?php echo current_full_url();?>", 
				type:"post",
				data:PostData,
				success: function (response) {
					stop_loading();
					//$("#"+currentTab+"_container").find('.div_data_loader').fadeOut();
					
					$("#merchantTable tbody").append($(response).hide().fadeIn(2000));
					
						
					if($.trim(response) != '')
					{
						page_no = offset;							
					}
					else
					{
						preventAjaxCall.push(currentTab);
					}
				}
			});
		}
		else
		{
			console.log('Sorry No more datas '+preventAjaxCall);
		}
	}
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				
				
				<?php 
					$default_txt = translate_phrase('Select City');
					if($cities)
					{
						foreach($cities as $item)
						{
							if($item['city_id'] == $seleccted_city_id)
							{
								$default_txt = $item['description'];
							}
						}	
					}
					
					echo form_open('',array('id'=>'city_form','method'=>'get'));					
				?>
				<?php if($cities):?>
				<div class="fl">
					<div class="sortbyTxt"><?php echo translate_phrase("Select City");?>: </div>
					<div class="sortbyDown">
						<dl class="city_dropdown dropdown-dt animate-dropdown scemdowndomain menu-Rightmar" >
							<dt>
								<a href="javascript:;" key=""><span><?php echo $default_txt;?></span> </a>
								<input type="hidden" name="city_id" id="city_id" value="<?php echo $seleccted_city_id;?>">
							</dt>
							<dd>
								<ul>
									<?php foreach($cities as $item):?>
									<li><a href="javascript:;" key="<?php echo $item['city_id'];?>"><?php echo $item['description'];?></a></li>
									<?php endforeach;?>
								</ul>
							</dd>
						</dl>
						
					</div>
				</div>	
				<?php endif;?>
					<div class="language-part fr top">
						<?php  echo language_bar(); ?>			
					<?php  //$lanaguage_id = $this->language_id;
						//echo form_dt_dropdown('language_id',$languages,$lanaguage_id,'class="dropdown-dt animate-dropdown scemdowndomain topDropdown"',translate_phrase('Select Language'),"hiddenfield");
					?>					
				</div>
				<?php //echo form_close();?>
				
				
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="f-decr">
					<ul>
						<li><a class="Intro-Button"  href="<?php echo base_url($this->admin_url.'?city_id='.$this->input->get('city_id'));?>"><?php echo translate_phrase('Event Admin');?></a></li>
						<li class="Intro-Button-sel"><a href="javascript:;"><?php echo translate_phrase('Merchant Admin');?></a></li>
					</ul>
				</div>
				
				<div class="step-form-Main Mar-top-none Top-radius-none active">
				
				<?php if($neighborhoods):
					$default_txt = translate_phrase('All');
					foreach($neighborhoods as $item)
					{
						if($item['neighborhood_id'] == $selected_neighborhood_id)
						{
							$default_txt = $item['description'];
						}
					}
					
					//echo form_open('',array('id'=>'city_form','method'=>'get'));					
				?>
				<div class="fl">
					<div class="sortbyTxt"><?php echo translate_phrase("Select Neighborhood");?>: </div>
					<div class="sortbyDown">
						<dl class="city_dropdown dropdown-dt animate-dropdown scemdowndomain menu-Rightmar" >
							<dt>
								<a href="javascript:;" key=""><span><?php echo $default_txt;?></span> </a>
								<input type="hidden" name="neighborhood_id" id="neighborhood_id" value="<?php echo $selected_neighborhood_id;?>">
							</dt>
							<dd>
								<ul>
									<li><a href="javascript:;" key=""><?php echo translate_phrase('All');?></a></li>
									
									<?php foreach($neighborhoods as $item):?>
									<li><a href="javascript:;" key="<?php echo $item['neighborhood_id'];?>"><?php echo $item['description'];?></a></li>
									<?php endforeach;?>
								</ul>
							</dd>
						</dl>						
					</div>
				</div>				
				<?php  endif; echo form_close();?>
				<a href="<?php echo base_url($this->admin_url.'/create_merchant').'?neighborhood_id='.$selected_neighborhood_id.'&city_id='.$this->input->get('city_id')?>"><span class="appr-cen"><?php echo translate_phrase('Create Merchant');?></span></a>	
				
				<div class="userBox-wrap comn-top-mar">
					<table class="tbl_order" id="merchantTable">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th><?php echo translate_phrase('Name');?></th>
								<th><?php echo translate_phrase('Address');?></th>
								<th><?php echo translate_phrase('Prince Range');?></th>
								<th><?php echo translate_phrase('Tags');?></th>
								<th><?php echo translate_phrase('Is Featured');?></th>								
							</tr>
						</thead>
						<tbody>
							<?php if(!$merchants):?>
								<tr>
									<td colspan="7"><?php echo translate_phrase('No records found');?>.</td>
								</tr>
							<?php else:?>
							<?php $this->load->view('admin_event/merchant/merchantlist_row')?>
							<?php endif;?>							
						</tbody>
					</table>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>
