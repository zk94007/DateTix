<script  type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$.validator.setDefaults({ ignore: '' });
		$('#pageTabs').easytabs();
		
		$('.accordion .hide').hide();
        $('.accordion .accordian-header').live('click',function () {
            var currentTitle = $(this);
            $(this).next().slideToggle('fast', function () {

                if (currentTitle.find('span i').hasClass('fa-chevron-down'))
                {
                    currentTitle.find('span i').removeClass('fa-chevron-down').addClass('fa-chevron-up')
                }
                else {
                    currentTitle.find('span i').removeClass('fa-chevron-up').addClass('fa-chevron-down')
                }

            }).siblings('.hide').slideUp();
            return false;
        });
        
        $('.accordion ul li input[type="checkbox"]').live('click', function (e) {
            $(this).siblings('a').trigger('click');
        });
        $('.accordion ul li a').live('click', function (e) {
            e.preventDefault();
            var ele = jQuery(this);
            var li = ele.parent();
            var hiddenField = jQuery(li).parent().parent().parent().find('input[type="hidden"]');
            
            if (ele.hasClass('active')) {
                var ids = new Array();
                var hiddenFieldValues = hiddenField.val();
                ids = hiddenFieldValues.split(',');
                var index = ids.indexOf(ele.attr('key'));
                ids.splice(index, 1);
                var newHiddenFieldValues = ids.join();
                ele.removeClass('active');
                $(ele).siblings('input[type="checkbox"]').prop('checked', false);
            }
            else {

                var inputValues = jQuery(hiddenField).val();
                if (inputValues != "")
                    var newHiddenFieldValues = inputValues + ',' + ele.attr('key');
                else
                    var newHiddenFieldValues = ele.attr('key');

                $(ele).addClass('active');
                $(ele).siblings('input[type="checkbox"]').prop('checked', true);
            }
              hiddenField.val(newHiddenFieldValues);
        });
	});
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="My-int-head">
				<h1><?php echo $page_title;
					$list_url = base_url($this->admin_url).'/merchant_list';
					if(isset($selected_neighborhood_id) && $selected_neighborhood_id)
					{
						$list_url .= '?neighborhood_id='.$selected_neighborhood_id.'&city_id='.$this->input->get('city_id');
					}
				?>
					
					<a class="blue-colr" href="<?php echo $list_url ?>"> < <?php echo translate_phrase('Back to Merchant List');?></a>	
				</h1>
				<div class="page-msg-box Red-color left"><?php echo $this->session->flashdata('error_msg');?></div>
				<div class="page-msg-box DarkGreen-color left"><?php echo $this->session->flashdata('success_msg');?></div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="pageTabs">
					<ul class='etabs'>								
						<li class='tab tab-nav' id="tab_details"><span></span><a href="#details"><?php echo translate_phrase('Details');?></a></li>
						<?php if(isset($merchant_id) && $merchant_id):?>
						<li class='tab tab-nav' id="tab_merchant_date_type"><span></span><a href="#merchant_date_type"><?php echo translate_phrase('Date Types');?></a></li>
						<li class='tab tab-nav' id="tab_merchant_cuisine"><span></span><a href="#merchant_cuisine"><?php echo translate_phrase('Cuisines');?></a></li>
						<li class='tab tab-nav' id="tab_photos"><span></span><a href="#photos"><?php echo translate_phrase('Photos');?></a></li>						
						<?php endif;?>
					</ul>
										
					<div class="step-form-Main Mar-top-none Top-radius-none" id="details">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/merchant/add_edit_form');?>
						</div>	
					</div>
					
					<?php if(isset($merchant_id) && $merchant_id):?>
										
					<div class="step-form-Main Mar-top-none Top-radius-none" id="merchant_date_type">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/merchant/merchant_date_type');?>
						</div>
					</div>					
					
					<div class="step-form-Main Mar-top-none Top-radius-none" id="merchant_cuisine">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/merchant/merchant_cuisine');?>
						</div>
					</div>
					
					<div class="step-form-Main Mar-top-none Top-radius-none" id="photos">
						<div class="step-form-Part">
							<?php $this->load->view('admin_event/merchant/merchant_photos');?>
						</div>
					</div>
					<?php endif;?>
					
				</div>
			</div>			
		</div>
	</div>
</div>