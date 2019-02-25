<script type="text/javascript">

$(document).ready(function(){

	/*----------------Category Select Tag-------------------------------*/
    $('.categorySelectTag ul li a').live('click',function(e) {
        e.preventDefault();
            var ele = jQuery(this);
        var li = ele.parent();
            var hiddenField = jQuery(li).parent().parent().find('input[type="hidden"]');
        if ($(li).hasClass('selected')) {
          // remove
          var ids                  = new Array();
          var hiddenFieldValues    = $(li).parent().parent().find('input[type="hidden"]').val(); 
          ids                      = hiddenFieldValues.split(',');
          var index                = ids.indexOf(ele.attr('id'));
          ids.splice(index, 1);
          var newHiddenFieldValues = ids.join(); 
          jQuery(hiddenField).val(newHiddenFieldValues);
          $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
              
              //count how many prefrences are selected.if 0 and importance is selected then unselect the importance and clear its hidden fileds value.
              unSelectImporance(ele);
              
        } else {
          // check before adding
          
            var prefrencesId   = jQuery(hiddenField).val();
            
                if(prefrencesId !="")
                    var dsc_id       = prefrencesId+','+ele.attr('id'); 
            else
                var dsc_id       = ele.attr('id');

            $(hiddenField).val(dsc_id);
            $(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
          
        }
   });
    /*-----------------------------------------------*/
});

function unSelectImporance(ele,eleType,hiddenFieldId)
{
    if(eleType == 'addBox')
    {
        jQuery('#'+hiddenFieldId).parent().parent().next().find('ul li.Intro-Button-sel').removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
        jQuery('#'+hiddenFieldId).parent().parent().next().find('input[type="hidden"]').val('')
        return 
    }
    else if(eleType == 'dd')
    {
        
        return 
    }
    else
    {
        var selectedPrefrenceCount = ele.parent().parent().parent().find('ul li.selected').length;
        if(selectedPrefrenceCount == 0)
        {
           var importanceContainer = ele.parent().parent().parent().next();
           jQuery(importanceContainer).find('li.Intro-Button-sel').removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
           ele.parent().parent().parent().next().find("input[type='hidden']").val('');
        }
    }
}
function submit_form()
{
	if($("#filter").val() == '')
	{
		$("#filterMsg").text('<?php echo translate_phrase("You must select at least one reason")?>.');		
	}
	else
	{
		$("#lack_interest").submit();
	}
}
</script>
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box popupBigger">
				<form id="lack_interest"
					action="<?php echo base_url().url_city_name().'/lack-interest.html?return_to='.$this->input->get('return_to').'&tab='.$this->input->get('tab').'&intro='.$this->utility->encode($user_intro_id);?>"
					method="post">
					<h1>
					<?php echo $page_heading; ?>
					</h1>
					<br /> <br />
					<div class="cityTxt" style="margin-top: 0px">
					<?php echo translate_phrase('Are you sure you want to pass up the chance to date ')?>
					<?php echo $user_info['first_name']?>
						?
						<?php if(isset($is_date_cancelled) && $is_date_cancelled):?>
						<?php if(isset($is_ticket_paid_by_user) && $is_ticket_paid_by_user):?>
						<span class="Red-color"><b><?php echo translate_phrase('Since you are cancelling a previously confirmed date, you date ticket will not be refunded')?>.</b>
						</span>
						<?php endif;?>
						<?php echo translate_phrase('Please let us know the reason why you are not longer interested in meeting ').$user_info['first_name']?>
						:
						<?php else:?>
						<?php echo translate_phrase('Please let us know the main reason(s) so that we can find better matches for you in future (this will be kept confidential)')?>
						:
						<?php endif;?>
					</div>
					<div class="two-column-school-container categorySelectTag">
						<ul class="f-decrMAIN three-column-li">
						<?php foreach ($filters as $key => $value) :?>
							<li><a id="<?php echo $key?>" class="disable-butn"
								href="javascript:;"><?php echo $value;?> </a></li>
								<?php endforeach;?>
						</ul>
						<input type="hidden" name="filters" id="filter" /> <label
							class="input-hint error" id="filterMsg"></label>
					</div>

					<div class="btn-group center">
						<input type="button" onclick="submit_form()" class="btn btn-pink"
							value="<?php echo translate_phrase('Confirm') ?>" /> <input
							type="button"
							onclick="window.location.href='<?php echo $return_url?>'"
							class="btn btn-blue"
							value="<?php echo translate_phrase('Cancel') ?>" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
