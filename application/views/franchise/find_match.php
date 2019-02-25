<script  type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script type="text/javascript">
	
	var manage_member_page_no = "<?php echo $page_no ?>";
	var preventAjaxCall = [];
	
	$(document).ready(function(){
		
		$(".rdo_div").live('click',function(){
			$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
			$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
			$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
		});
	
		$('.importance ul li a').live('click',function(e) {
			e.preventDefault();
		    var ele = jQuery(this);
	        
		    var parentUl = ele.parent().parent();
			var li = jQuery(parentUl).find('li.Intro-Button-sel');
		    $(li).removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
		    $(ele).parent().addClass('Intro-Button-sel').find('a').removeClass('Intro-Button');
	        
	        //set the hidden field value for this prefrence
	    	var selectedImportance = jQuery(parentUl).find('li.Intro-Button-sel a').attr('importanceVal');
	    	parentUl.parent().find('input[type="hidden"]').val(selectedImportance);
		});
		
	});

	function update_user(form_id)
	{
		$("#form_"+form_id).submit();
	}
	
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
			var currentTab = '';
			var offset = 1;
			var tab_order = '';
			var searchVal = '';
			var city_id =  '';
			var PostData = '';
			
			city_id = $("#city_id").val();
			currentTab = 'manage_member';
			tab_order = $("#member_order").val();
			offset += parseInt(manage_member_page_no);
			
			PostData = {'sort_by':tab_order,'page_no':offset};
			
			
			if(preventAjaxCall.indexOf(currentTab) == -1 && isAjaxCallRunning == false)
			{
				$("#"+currentTab+"_container").append('<div class="div_data_loader"></div>');

				loading();
				$.ajax({ 
					url: '<?php echo $match_url;?>', 
					type:"post",
					data:PostData,
					success: function (response) {
						stop_loading();
						$("#"+currentTab+"_container").find('.div_data_loader').fadeOut();
						if(domAction == 'replace')
						{
							$("#"+currentTab+"_container").html($(response).hide().fadeIn(2000));
						}
						
						if(domAction == 'append')
						{
							$("#"+currentTab+"_container").append($(response).hide().fadeIn(2000));
						}
							
						if($.trim(response) != '')
						{
							if(currentTab == 'manage_member')
							{
								manage_member_page_no = offset;
							}
						}
						else
						{
							preventAjaxCall.push(currentTab);
							//alert('No more data')
						}
				   }
				});
			}
			else
			{
				console.log('Sorry No more Users '+preventAjaxCall);
			}
	}

</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title?></h1>
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="active_expire">
					<div class="step-form-Main Mar-top-none Top-radius-none" id="manage_member">
						<div class="step-form-Part">
							<div class="userTop  Mar-top-none Pad-BotAs3">
								
								<div class="sfp-1-main mar-top2">
								<div class="sfp-1-Left bold"> <?php echo translate_phrase('Sort by');?>: </div>
								
								<div class="sortbyDown">
									<dl class="sort_dl dropdown-dt domaindropdown common-dropdown">
										<dt>
											<a href="javascript:;" key="1"><span class="y-overflow-hidden"><?php echo translate_phrase('Compatibility').' ('.translate_phrase('high to low').')';?>
											</span> </a> <input type="hidden" name="member_order" id="member_order" value="1">
										</dt>
										<dd>
											<ul>												
												<li><a href="javascript:;" key="1"><?php echo translate_phrase('Compatibility ').' ('.translate_phrase('high to low').')';?></a></li>
												<li><a href="javascript:;" key="2"><?php echo translate_phrase('Compatibility ').' ('.translate_phrase('low to high').')';?></a></li>
											</ul>
										</dd>
									</dl>
								</div>
								</div>
								
							</div>
							<div id="manage_member_container"><?php $this->load->view('franchise/load_members');?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
