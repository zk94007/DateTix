<script  type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script type="text/javascript">
	var review_app_page_no = "<?php echo $page_no ?>";
	var manage_member_page_no = "<?php echo $page_no ?>";
	var preventAjaxCall = [];
	
	$(document).ready(function(){
		
		$('#active_expire').easytabs();
		
		$(".rdo_div").live('click',function(){
			$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
			$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
			$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
		});
	
		$(".dropdown-dt dd ul li a, #btnReviewApp, #btnMember").live('click',function () {
			if($('#active_expire ul li.active a').attr('href') == '#review_app')
			{
				currentTab = 'review_app';
				review_app_page_no = '0';
			}
			
			if($('#active_expire ul li.active a').attr('href') == '#manage_member')
			{
				currentTab = 'manage_member';
				manage_member_page_no = '0';
			}
			
			if(preventAjaxCall.indexOf(currentTab) !=  '-1')
			{
				preventAjaxCall.pop(currentTab);
			}
			
			load_more_data('replace');	
		});
		
		$('#active_expire').bind('easytabs:after', function(tab, panel, data){
			$(".current_tab").val($(panel[0]).parent().attr('id'));			
		});
		
		//click on send mail btn
		$(".send_mail").live('click',function(){
			var user_id = $(this).attr('lang');
			var subject = $(this).parent().siblings('.div-row').find(':input[name="subject"]').val();
			var body = $(this).parent().siblings('.div-row').find(':input[name="email_body"]').val();
			if(user_id)
			{
				var obj = $(this);
				loading();
				$.ajax({ 
					url: base_url +"admin/send_mail_to_user/"+user_id,
					type:"post",
					data:{'subject':subject,'body':body},
					dataType:'json',
					success: function (response) {
						stop_loading();
						$(obj).parent().siblings('label').text(response.msg).addClass(response.type);
				   }
				});
			}
		});
		
		//Change Review Application Status
		$(".update_status").live('click',function(){
			
			var user_id = $(this).parent().attr('lang');
			var status = $(this).attr('lang');
			var body = $(this).parent().siblings('.div-row').find(':input[name="email_body"]').val();
			if(user_id)
			{
				var obj = $(this);
				loading();
				$.ajax({ 
					url: base_url +"admin/change_user_status/"+user_id,
					type:"post",
					data:{'status':status,'body':body},
					dataType:'json',
					success: function (response) {
						stop_loading();
						$(obj).parent().siblings('label').text(response.msg).addClass(response.type);
				   }
				});
			}
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
			city_id = $("#city_id").val();
			if($('#active_expire ul li.active a').attr('href') == '#review_app')
			{
				currentTab = 'review_app';
				tab_order = $("#user_order").val();
				searchVal = $("#txtReviewApp").val();
				
			
				offset += parseInt(review_app_page_no);
			}
			console.log('city... : '+city_id);
			if($('#active_expire ul li.active a').attr('href') == '#manage_member')
			{
				currentTab = 'manage_member';
				tab_order = $("#member_order").val();
				searchVal = $("#txtMember").val();
				//city_id = $("#manage_member_city_id").val();			
				offset += parseInt(manage_member_page_no);
			}
			
			
			if(preventAjaxCall.indexOf(currentTab) == -1 && isAjaxCallRunning == false)
			{
				$("#"+currentTab+"_container").append('<div class="div_data_loader"></div>');

				loading();
				$.ajax({ 
					url: base_url +"admin/load_users/"+currentTab, 
					type:"post",
					data:{'sort_by':tab_order,'page_no':offset,'search_txt':searchVal,'city_id':city_id},
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
							if(currentTab == 'review_app')
							{
								review_app_page_no = offset;
							}
							
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
	function next_year_date()
	{
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear()+1;

		if(dd<10) {
			dd='0'+dd
		} 

		if(mm<10) {
			mm='0'+mm
		} 

		today = mm+'/'+dd+'/'+yyyy;
		return today ;
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
				<?php if($cities):?>
				<div class="sortby bor-none Mar-top-none">
					<div class="sortbyTxt"><?php echo translate_phrase('Select City');?>: </div>
					<div class="sortbyDown">
						<dl class="dropdown-dt animate-dropdown scemdowndomain menu-Rightmar" >
							<dt>
								<a href="javascript:;" key=""><span><?php echo translate_phrase("Select City");?></span> </a>
								<input type="hidden" name="city_id" id="city_id" value="">
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
				
				<div class="emp-B-tabing-M-short" id="active_expire">
					<ul class='etabs'>
						
						<?php if(!$is_review_resricted):?>
						<li class='tab tab-nav' id="review_appTAB"><span></span><a href="#review_app"><?php echo translate_phrase('Review Application');?></a></li>
						<?php endif;?>
						
						<li class='tab tab-nav' id="manage_memberTAB"><span></span><a href="#manage_member"><?php echo translate_phrase('Manage Members');?></a></li>
					</ul>
					<?php if(!$is_review_resricted):?>
					<div class="step-form-Main Mar-top-none Top-radius-none" id="review_app">
						<div class="step-form-Part">
							
							<div class="userTop  Mar-top-none Pad-BotAs3">
								<div class="sortbyTxt"> <?php echo translate_phrase('Sort by');?>: </div>
								<div class="sortbyDown">
									<dl class="sort_dl dropdown-dt domaindropdown common-dropdown">
										<dt>
											<a href="javascript:;" key="1"><span><?php echo translate_phrase('Application Date').' ('.translate_phrase('recent to old').')';?>
											</span> </a> <input type="hidden" name="user_order" id="user_order" value="1">
										</dt>
										<dd>
											<ul>
												<li><a href="javascript:;" key="1"><?php echo translate_phrase('Application Date').' ('.translate_phrase('recent to old').')';?></a></li>
												<li><a href="javascript:;" key="2"><?php echo translate_phrase('Application Date').' ('.translate_phrase('old to recent').')';?></a></li>

												<li><a href="javascript:;" key="3"><?php echo translate_phrase('Age').' ('.translate_phrase('young to old').')';?></a></li>
												<li><a href="javascript:;" key="4"><?php echo translate_phrase('Age').' ('.translate_phrase('old to young').')';?></a></li>

												<li><a href="javascript:;" key="5"><?php echo translate_phrase('First Name').' ('.translate_phrase('A to Z').')';?></a></li>
												<li><a href="javascript:;" key="6"><?php echo translate_phrase('First Name').' ('.translate_phrase('Z to A').')';?></a></li>
											</ul>
										</dd>
									</dl>									
								</div>
								
								<div class="search-fields">
									<div class="input-wrap">
										<input id="txtReviewApp" class="Degree-input" type="text" style="height: 40px;">
										<label id="lblReviewApp" class="input-hint"></label>
									</div>
									<button type="button" id="btnReviewApp" class="btn btn-blue" style="height: 36px;"><?php echo translate_phrase('Search') ?></button>									
								</div>
							</div>
							<div id="review_app_container">
							<?php $this->load->view('admin/review_applications');?>
							</div>
						</div>
					</div>
					<?php endif;?>
						
					<div class="step-form-Main Mar-top-none Top-radius-none" id="manage_member">
						<div class="step-form-Part">
							<div class="userTop  Mar-top-none Pad-BotAs3">
								<div class="sortbyTxt"> <?php echo translate_phrase('Sort by');?>: </div>
								<div class="sortbyDown">
									<dl class="sort_dl dropdown-dt domaindropdown common-dropdown">
										<dt>
											<a href="javascript:;" key="1"><span class="y-overflow-hidden"><?php echo translate_phrase('Application Date').' ('.translate_phrase('recent to old').')';?>
											</span> </a> <input type="hidden" name="member_order" id="member_order" value="1">
										</dt>
										<dd>
											<ul>
												
												<li><a href="javascript:;" key="1"><?php echo translate_phrase('Application Date').' ('.translate_phrase('recent to old').')';?></a></li>
												<li><a href="javascript:;" key="2"><?php echo translate_phrase('Application Date').' ('.translate_phrase('old to recent').')';?></a></li>

												<li><a href="javascript:;" key="3"><?php echo translate_phrase('Age').' ('.translate_phrase('young to old').')';?></a></li>
												<li><a href="javascript:;" key="4"><?php echo translate_phrase('Age').' ('.translate_phrase('old to young').')';?></a></li>

												<li><a href="javascript:;" key="5"><?php echo translate_phrase('First Name').' ('.translate_phrase('A to Z').')';?></a></li>
												<li><a href="javascript:;" key="6"><?php echo translate_phrase('First Name').' ('.translate_phrase('Z to A').')';?></a></li>
												
												<li><a href="javascript:;" key="7"><?php echo translate_phrase('Last Active Time').' ('.translate_phrase('recent to old').')';?></a></li>
												<li><a href="javascript:;" key="8"><?php echo translate_phrase('Last Active Time').' ('.translate_phrase('old to recent').')';?></a></li>
												
												<li><a href="javascript:;" key="9"><?php echo translate_phrase('Last Order Date').' ('.translate_phrase('recent to old').')';?></a></li>
												<li><a href="javascript:;" key="10"><?php echo translate_phrase('Last Order Date').' ('.translate_phrase('old to recent').')';?></a></li>
												
												<li><a href="javascript:;" key="11"><?php echo translate_phrase('Order Amount').' ('.translate_phrase('most to least').')';?></a></li>
												<li><a href="javascript:;" key="12"><?php echo translate_phrase('Order Amount').' ('.translate_phrase('least to most').')';?></a></li>
												
												<li><a href="javascript:;" key="13"><?php echo translate_phrase('No. of Chat Messages Sent').' ('.translate_phrase('most to least').')';?></a></li>
												<li><a href="javascript:;" key="14"><?php echo translate_phrase('Items Pending Review').' ('.translate_phrase('newest to oldest').')';?></a></li>
												
											</ul>
										</dd>
									</dl>									
								</div>
								
								<div class="search-fields">
									<div class="input-wrap">
										<input id="txtMember" class="Degree-input" type="text" style="height: 40px;">
										<label id="lblMember" class="input-hint"></label>
									</div>
									<button type="button" id="btnMember" class="btn btn-blue" style="height: 36px;"><?php echo translate_phrase('Search') ?></button>
								</div>
							</div>
							<div id="manage_member_container">
							<?php $this->load->view('admin/load_members');?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
