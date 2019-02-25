<script src="<?php echo base_url()?>assets/js/general.js"></script>
<?php $this->load->view('user/include/ideal_match_js');?>
<script>
jQuery(document).ready(function(){
	jQuery('input[type="submit"]').hide();
	jQuery('#insertFilters').live('click',function(){
        var idealMatchFilters = jQuery('#idealMatchFilter').val();
        var criteareaArr = idealMatchFilters.split(',')
        if(criteareaArr.length >= 3)
        {
            jQuery('#idealMatchSelectionError').text('');
            var url  = '<?php echo base_url() ?>' +'user/ideal_match';
            var data = {'idealDateAttributes': idealMatchFilters};
            jQuery.post(url,data,function(response){
                jQuery('#divToLoad').html(response.html);
                jQuery('input[type="submit"]').show();
            },'json');
        }
        else
        {
            //user did not select any ideal match filter
            jQuery('#idealMatchSelectionError').text("<?php echo translate_phrase('You must select at least three criteria'); ?>")
        }
    });
});


function validateIdealMatch()
{
    var flag = true;
    var importanceFileds = jQuery('#divToLoad .importance').find('input[type="hidden"]');
    // var importanceFileds = jQuery('#divToLoad
	// .importanceRange').find('input[type="hidden"]');
    
    jQuery('#importanceSelectionError').remove();
    jQuery.each(importanceFileds,function(index,element){
        
        // control goes into this condition only if user selected loks in ideal
		// match options
        if(jQuery(element).attr('id') == 'wantLooksImportance')
        {
            var looksFrom       = jQuery('#looksFrom').val();
            var looksTo         = jQuery('#looksTo').val();
            // looks dropdown validation
            if(looksFrom < looksTo)
            {
                flag=false;
                jQuery('#looks_err').text('Left dropdown must be worse looking or equal to right dropdown.');
                jQuery('body').scrollTo((jQuery('#looks_err').parent().parent()), 800);
                return false;
            }
            else
            {
                 jQuery('#looks_err').text('');
            }  
        }
        
        // control goes into this condition only if user selected heigh in ideal
		// match options
        if(jQuery(element).attr('id') == 'wantHeightImportance')
        {
            
            if(validateHeightIdealMatch() == false)
            {
                jQuery('body').scrollTo((jQuery(element).parent().parent().parent()),800);
                flag = false;
                return false;
            }
        }
        
        // control goes into this condition only if user selected age in ideal
		// match options
        if(jQuery(element).attr('id') == 'wantAgeRangeImportance')
        {
            var ageFrom         = jQuery('#ageRangeLowerLimit').val();
            var ageTo           = jQuery('#ageRangeUpperLimit').val();
            if(ageFrom !="" && ageTo !="" &&(ageFrom > ageTo))
            {
                flag = false;
                jQuery('#ageRangeError').text('Please select valid age range.');
                jQuery('body').scrollTo((jQuery('#ageRangeError').parent().parent()),800);
                return false;
            }
        }
        
        // this condition is to validate other importance tags.
        if(jQuery(element).val() == "")
        {
            hideError('heightRangeError');
            hideError('ageRangeError');
            jQuery('body').scrollTo((jQuery(element).parent().parent().parent()),800);
            jQuery('<label id="importanceSelectionError" style="font-size: 18px" class="input-hint error error_indentation error_msg">You must select importance level for each criteria you chose</label>').insertAfter(jQuery(element));
            
            flag = false;
            return flag;
        }
        
        return flag;
    });
    
    if(flag == false)
        return false;    
    else
       return true;
}

function validate_idealMatch()
{
	if(validateIdealMatchRange() && validateIdealMatch())
    {
		return true;
    }
	else
	{
		return false;
	}
}
function validateIdealMatchRange()
{
    var flag = true;
    // var importanceFileds = jQuery('#divToLoad
	// .importance').find('input[type="hidden"]');
    var importanceFileds = jQuery('#divToLoad .importanceRange').find('input[type="hidden"]');
    
    jQuery('#importanceSelectionError').remove();
    jQuery.each(importanceFileds,function(index,element){
        
        // control goes into this condition only if user selected loks in ideal
		// match options
        if(jQuery(element).attr('id') == 'wantLooksImportance')
        {
            var looksFrom       = jQuery('#looksFrom').val();
            var looksTo         = jQuery('#looksTo').val();
            // looks dropdown validation
            if(looksFrom < looksTo)
            {
                flag=false;
                jQuery('#looks_err').text('Left dropdown must be worse looking or equal to right dropdown.');
                jQuery('body').scrollTo((jQuery('#looks_err').parent().parent()), 800);
                return false;
            }
            else
            {
                 jQuery('#looks_err').text('');
            }  
        }
        
        
        // control goes into this condition only if user selected heigh in ideal
		// match options
        if(jQuery(element).attr('id') == 'wantHeightImportance')
        {
            
            if(validateHeightIdealMatch() == false)
            {
                jQuery('body').scrollTo((jQuery(element).parent().parent().parent()),800);
                flag = false;
                return false;
            }
        }
        
        // control goes into this condition only if user selected age in ideal
		// match options
        if(jQuery(element).attr('id') == 'wantAgeRangeImportance')
        {
            var ageFrom         = jQuery('#ageRangeLowerLimit').val();
            var ageTo           = jQuery('#ageRangeUpperLimit').val();
            if(ageFrom !="" && ageTo !="" &&(ageFrom > ageTo))
            {
                flag = false;
                jQuery('#ageRangeError').text('Please select valid age range.');
                jQuery('body').scrollTo((jQuery('#ageRangeError').parent().parent()),800);
                return false;
            }
        }
        
        // this condition is to validate other importance tags.
        if(jQuery(element).val() == "")
        {
            hideError('heightRangeError');
            hideError('ageRangeError');
            jQuery('body').scrollTo((jQuery(element).parent().parent().parent()),800);
            jQuery('<label id="importanceSelectionError" style="font-size: 18px" class="input-hint error error_indentation error_msg">You must select importance level for each criteria you chose</label>').insertAfter(jQuery(element));
            
            flag = false;
            return flag;
        }
        
        return flag;
    });
    
    if(flag == false)
        return false;
    
    else
       return true;
}


function validateHeightIdealMatch()
{
    var flag = true;
    var useMeters = jQuery('#useMeters').val();
    
    if(useMeters == 1)
    {
        var cmFrom = jQuery('#centemetersFrom').val();
        var cmTo   = jQuery('#centemetersTo').val();
        
        if(cmFrom != "" && cmTo !="" && (cmFrom > cmTo))
        {
            flag = false;
            showError('heightRangeError','Minimum height range must be lower than maximum height range.');
        }
    }
    else if(useMeters == 0)
    {
        var feetFrom = jQuery('#feetFrom').val();
        var feetTo   = jQuery('#feetTo').val();
        
        if(feetFrom != "" && feetTo != ""  && (feetFrom > feetTo))
        {
            flag = false;
            showError('heightRangeError','Minimum height range must be lower than maximum height range.');
        }
    }
    
    return flag;
}

</script>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main" id="step2_a">
			<div class="page-msg-box DarkGreen-color left">
			<?php echo $this->session->flashdata('msg');?>
			</div>
			<form
				action="<?php echo base_url().url_city_name().'/ideal-match.html'?>"
				method="POST">
				<div class="step-form-Main">
					<div class="step-form-Part">
						<div class="edu-main">
							<div class="aps-d-top">
								<h2>
								<?php echo translate_phrase('What are the most important things you look for in your match');?>
									?
								</h2>
								<div class="f-decrMAIN customSelectTag">
								<?php $idealMatchFilters = (!empty($selectedValues['match_filters'])) ? explode(',',$selectedValues['match_filters']):array();
								
								foreach ($idealDateFilters as $key => $value)
								{?>
									<ul>
										<li
										<?php echo (array_search($key,$idealMatchFilters) !== FALSE)? 'class="selected"':'' ?>>
											<a id="<?php echo $key?>" class="disable-butn"
											href="javascript:;"><?php echo $value?> </a>
										</li>
									</ul>
									<? }?>
									<input type="hidden" name="idealMatchFilter"
										id="idealMatchFilter"
										value="<?php echo $selectedValues['match_filters']?>"> <label
										id="idealMatchSelectionError" style="font-size: 18px"
										class="input-hint error error_indentation error_msg"></label>
								</div>
								<div class="Nex-mar left">
									<input type="button" class="Next-butM"
										value="<?php echo translate_phrase('Next')?>"
										id="insertFilters">
								</div>
							</div>
							<div id="divToLoad"></div>
						</div>
					</div>
				</div>
				<div class="Nex-mar">
					<input type="submit" class="Next-butM" value="Update"
						onclick="return validate_idealMatch();">
				</div>
			</form>
		</div>
	</div>
</div>
