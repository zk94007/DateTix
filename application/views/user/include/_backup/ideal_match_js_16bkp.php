<script>
jQuery(document).ready(function(){

	//When click on dropdown then his ul is open..
	$(".dropdown-dt").find('dt a').live('click',function () {
    	$(this).parent().parent().find('ul').toggle();
    });
	
	//When select a option..
    $(".dropdown-dt dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
    	$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
	});
	
	/*-----------------------------------------------------------------------------------------------------------*/
    //for getting image of school besides the drop down
    $("#schoolPrefrenceId dd ul li a").live('click',function () {
		var schoolName = $(this).html();
        var url  = '<?php echo base_url(); ?>' +"user/show_school_logo/";
        var data = {'school_name':schoolName};
        jQuery.post(url,data,function(response){
			if (response.length > 0) {
            	var url = '<?php echo base_url().'school_logos/';?>'+response;
            	$('#school_logo').html('<img src="'+url+'" height="50" width="50">');
           	}else
            	$('#school_logo').html('');
        });
	});
	
    $(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("dropdown-dt"))
        	$(".dropdown-dt dd ul").hide();
    });
	// END Dropdown   
    
    
    /*----------------CUSTOM Select Tag-------------------------------*/
	$('.customSelectTag ul li a').live('click',function(e) {
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
        } 
       	else {
          //check before adding
          
          var prefrencesId   = jQuery(hiddenField).val();
          if(prefrencesId !="")
          	var dsc_id       = prefrencesId+','+ele.attr('id'); 
          else
          	var dsc_id       = ele.attr('id');

  			$(hiddenField).val(dsc_id);
        	$(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
        }

		var allSelected = true;
		//de-select 
		$.each($(li).parent().parent().find('ul'),function(i,item){
            if(!$(item).find('li').hasClass('selected'))
            {
            	allSelected = false;
            }
        });

		if(allSelected)
		{
			$(li).parent().parent().parent().siblings('h2').find('a').fadeOut();//removeClass('disable-butn').addClass('appr-cen');
		}
		else
		{
			$(li).parent().parent().parent().siblings('h2').find('a').fadeIn();//removeClass('appr-cen').addClass('disable-butn');
		}
   });

	/*-----------------------------------------------*/
	$("#incomeAmount").live('keyup',function(){
		var seleLI = $(this).parent().parent().parent().parent().find('.Intro-Button-sel');
    	var field_income= $('#incomeAmount').val().trim();
        var regEx = /[^0-9]/g;
        var isNotValidInput = regEx.test(field_income);
        if(field_income =='' || field_income =='0' || isNotValidInput === true)
        {
        	if($(seleLI).length != 0)
        	{
        		$(seleLI).removeClass('Intro-Button-sel');
        		$(seleLI).find('a').addClass('Intro-Button');
        		$("#wantIncomePrefrenceImportance").val('');	
            }	
        }
	});
    /*----------------Importance Tag-------------------------------*/
    $('.importance ul li a').live('click',function(e) {
		//debugger;
	    e.preventDefault();
	    var ele = jQuery(this);
                
		//check if prefrences for this particular field is selected or not. If not then dont allow to select importance.
	    var prefrenceHiddenField = ele.parent().parent().parent().prev().find("input[type='hidden']").val();
	    if(prefrenceHiddenField =="")
	    {    
	    	return false; 
	    }
                
		var checkid = ele.parent().parent().parent().prev().attr('id');
	    if(checkid =='p_income')
	    {
	    	var field_income= $('#incomeAmount').val().trim();
	        /*jQuery('.numbersOnly').blur(function () { 
	        	this.value = this.value.replace(/[^0-9\.]/g,'');
	        });*/
	        var regEx = /[^0-9]/g;
	        var isNotValidInput = regEx.test(field_income);
	        if(field_income =='' || isNotValidInput === true)
	        {
	        	return false;
	        }
	                        
		}

	    var parentUl = ele.parent().parent();
		var li = jQuery(parentUl).find('li.Intro-Button-sel');
	    $(li).removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
	    $(ele).parent().addClass('Intro-Button-sel').find('a').removeClass('Intro-Button');
	                
	                //set the hidden field value for this prefrence
	    	var selectedImportance = jQuery(parentUl).find('li.Intro-Button-sel a').attr('importanceVal');
	    	parentUl.parent().find('input[type="hidden"]').val(selectedImportance);
		});
	
		$('.importanceRange ul li a').live('click',function(e) {
	   	//debugger;
	    e.preventDefault();
	    var ele = jQuery(this);
	                   
	    //check if prefrences for this particular field is selected or not. If not then dont allow to select importance.
	    var prefrenceHiddenField = ele.parent().parent().parent().prev().find("input[type='hidden']").val();
		
	    var range = true;
		$.each(ele.parent().parent().parent().parent().find('dl dt input[type="hidden"]'),function(i,item){
			prefrenceHiddenField = $(item).val()
			if(prefrenceHiddenField =="")
	        {    
		    	range = false;
			}
		});

		if(range == false)
	    {    
			return false; 
		}

	    var checkid = ele.parent().parent().parent().prev().attr('id');
		if(checkid =='p_income')
	    {
	    	var field_income= $('#incomeAmount').val().trim();
	                       
			/*jQuery('.numbersOnly').blur(function () { 
	        	this.value = this.value.replace(/[^0-9\.]/g,'');
	        });*/
	        var regEx = /[^0-9]/g;
	        var isNotValidInput = regEx.test(field_income);
	        if(field_income =='' || isNotValidInput === true)
			{
	            return false;
	        }
		}
                   
	    var parentUl = ele.parent().parent();
	    var li = jQuery(parentUl).find('li.Intro-Button-sel');
	            
	    $(li).removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
	    //now show the clicked button as selected.
	    $(ele).parent().addClass('Intro-Button-sel').find('a').removeClass('Intro-Button');
	                   
	    var selectedImportance = jQuery(parentUl).find('li.Intro-Button-sel a').attr('importanceVal');
	    parentUl.parent().find('input[type="hidden"]').val(selectedImportance);
	});
});
    
    function unSelectImporance(ele,eleType,hiddenFieldId)
    {
        if(eleType == 'addBox')
        {
            jQuery('#'+hiddenFieldId).parent().parent().next().find('ul li.Intro-Button-sel').removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
            jQuery('#'+hiddenFieldId).parent().parent().next().find('input[type="hidden"]').val('')
            return 
        }
        else if(eleType == 'preference')
        {
            jQuery('#'+hiddenFieldId).parent().parent().parent().parent().next().find('ul li.Intro-Button-sel').removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
            jQuery('#'+hiddenFieldId).parent().parent().parent().parent().next().find('input[type="hidden"]').val('')
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

    function select_toggle(Obj,elementDiv)
    {
    	var hiddenField = $(Obj).parent().siblings('.f-decrMAIN').find('.'+elementDiv+' input[type="hidden"]');
    	var prefrencesId   = '';//$(hiddenField).val();

    	//console.log($(hiddenField).val());
    	if($(Obj).hasClass('disable-butn'))
    	{
    		//$(Obj).removeClass('disable-butn').addClass('appr-cen');
			$(Obj).fadeOut();
    		$.each($(Obj).parent().siblings('.f-decrMAIN').find('.'+elementDiv+' ul'),function(i,item){
            	var ele = $(item).find('li a');
            	if(prefrencesId != '')
                	prefrencesId += ','+ele.attr('id'); 
            	else
            		prefrencesId = ele.attr('id');
            	$(ele).parent().addClass('selected');
    			$(ele).addClass('appr-cen').removeClass('disable-butn');
        	});
        	$(hiddenField).val(prefrencesId);
    	}
    	else
    	{
    		$(Obj).removeClass('appr-cen').addClass('disable-butn');
    		$.each($(Obj).parent().siblings('.f-decrMAIN').find('.'+elementDiv+' ul'),function(i,item){
            	$(item).find('li a').removeClass('appr-cen').addClass('disable-butn');
            	$(item).find('li').removeClass('selected');
        	});
        	$(hiddenField).val('');
    	}
     
    }
    function add_prefrence(prefrenceUniqueName,dlId,hiddenFieldId,appenEleId){
    	
        var prefrenceId = $('#'+dlId).find('dt a').attr('key');
        var perfrenceName = $('#'+dlId).find('dt a span').html();
        
        
        var selectedPrefrenceIds = $('#'+hiddenFieldId).val();
        if (prefrenceId) {
            if(selectedPrefrenceIds != "")
                var selectedPrefrenceModifiedValue     = selectedPrefrenceIds+','+prefrenceId; 
            else
                var selectedPrefrenceModifiedValue     = prefrenceId;
             if(selectedPrefrenceIds.indexOf(prefrenceId)== -1){ 
                $("#"+hiddenFieldId).val(selectedPrefrenceModifiedValue);
              	$("#"+appenEleId).append('<li class="Fince-But" id="'+prefrenceUniqueName+prefrenceId+'" ><a href="javascript:;">'+perfrenceName+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_prefrence('+"'"+prefrenceUniqueName+"'"+','+"'"+hiddenFieldId+"'"+','+"'"+prefrenceId+"'"+');" title="Remove"></a></li>');            }
         }
        
    }

    //Rajnish Savaliya
    function add_school_prefrence()
    {
    	var prefrenceUniqueName = 'user_want_school';
    	var perfrenceName = $('#school_name').val();

    	var prefrenceId =  $('#school_name').attr("lang");;
 	   	var selectedPrefrenceIds = $('#user_want_school_ids').val();

        if (prefrenceId) {
            
            if(selectedPrefrenceIds != "")
                var selectedPrefrenceModifiedValue     = selectedPrefrenceIds+','+prefrenceId; 
            else
                var selectedPrefrenceModifiedValue     = prefrenceId;

             if(selectedPrefrenceIds.indexOf(prefrenceId)== -1)
             {
             	$("#user_want_school_ids").val(selectedPrefrenceModifiedValue);
              	$("#showSelectedSchools").append('<li class="Fince-But" id="'+prefrenceUniqueName+prefrenceId+'" ><a href="javascript:;">'+perfrenceName+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_prefrence('+"'"+prefrenceUniqueName+"'"+','+"'user_want_school_ids'"+','+"'"+prefrenceId+"'"+');" title="Remove"></a></li>');
             }
             $('#school_name').val('');
             $('#school_name').siblings('label').html('');
             $("#school_logo").html('');
             $('#school_name').attr("lang","")
        }
        else
        {
        	$("#school_name").parent().append('<label class="input-hint error"><?php echo translate_phrase('You can only add schools that appear in our eligible school list')?>.</label>');
        	$('#school_name').focus();
        }
        
    }

  //Rajnish Savaliya
    function add_company_prefrence()
    {
    	var prefrenceUniqueName = 'user_want_company';
    	var perfrenceName = $('#company_name').val();
    	var prefrenceId = $('#company_name').attr("lang");
 	   	var selectedPrefrenceIds = $('#user_want_company_ids').val();
 	   	
		if (!prefrenceId) {
			if(perfrenceName != '')
			{
				prefrenceId = '_'+perfrenceName+'_';
			}		
		}
        if (prefrenceId) {
            
            if(selectedPrefrenceIds != "")
                var selectedPrefrenceModifiedValue     = selectedPrefrenceIds+','+prefrenceId; 
            else
                var selectedPrefrenceModifiedValue     = prefrenceId;

             if(selectedPrefrenceIds.indexOf(prefrenceId)== -1)
             {
             	$("#user_want_company_ids").val(selectedPrefrenceModifiedValue);
              	$("#showSelectedCompanies").append('<li class="Fince-But" id="'+prefrenceUniqueName+prefrenceId+'" ><a href="javascript:;">'+perfrenceName+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_prefrence('+"'"+prefrenceUniqueName+"'"+','+"'user_want_company_ids'"+','+"'"+prefrenceId+"'"+');" title="Remove"></a></li>');
             }

             $('#company_name').val('');
             $("#company_logo").html('');
             $('#company_name').attr("lang","");
             
             $('#company_name').siblings('label').html('');
             
        }
        else
        {
            if($('#company_name').siblings('label').length != 0)
            {
            	$('#company_name').siblings('label').html('<?php echo translate_phrase("Please select company from auto suggestion")?>');                
            }
            else
            {
            	$("#company_name").parent().append('<label class="input-hint error"><?php echo translate_phrase('Please select company from auto suggestion')?>.</label>');
            }
        	$('#company_name').focus();
        }
        
        
    }

    
    
    function remove_prefrence(prefrenceUniqueName,hiddenFiledId,id){
        var ids           = new Array();
        var prefrenceIds     = jQuery('#'+hiddenFiledId).val();
        var ids           = prefrenceIds.split(','); 
        var index         = ids.indexOf(id);
        ids.splice(index, 1);
        var modifiedPrefrenceIds      = ids.join(); 
        jQuery('#'+prefrenceUniqueName+id).remove();

        jQuery("#"+hiddenFiledId).val(modifiedPrefrenceIds);
        if(jQuery("#"+hiddenFiledId).val() == "")
        {
            unSelectImporance('','preference',hiddenFiledId);
        }        
    }

    var autoschool = true;
    function auto_complete_school(){
        if(autoschool)
        {
        	loading();
        	$.ajax({ 
                url: '<?php echo base_url(); ?>' +"user/autocomplete_school/", 
                dataType: "json", 
                type:"post",
                cache: false,
                success: function (data) {
                	stop_loading();
                	autoschool = false;
                	var availableTags= [];
                    $.each(data,function(id,description) {
                        var item = {};
                        item["id"] = id;
                        item["value"] = description;

                        availableTags.push(item);
                    });
                    $( "#school_name" ).autocomplete({
                    	appendTo: "#auto-school-container",
                		minLength: 1,
                        source: availableTags,
                        select : function(event, ui){ 
                    		$('#school_name').attr("lang", ui.item.id);
                        	show_logo(ui.item.value);
                        }
                    });
                }     
            });
        }
    }

    function show_logo(school_name){

        if(typeof(school_name) == 'undefined')
        	school_name = $('#school_name').val();

        loading();
        $.ajax({ 
            url: '<?php echo base_url(); ?>' +"user/show_school_logo/", 
            type:"post",
            data:'school_name='+school_name,
            cache: false,
            success: function (data) {
            	stop_loading();
                if (data.length > 0) {
                    var url = '<?php echo base_url();?>school_logos/'+data;
                    $('#school_logo').html('<img src="'+url+'" height="50" width="50">');
                }else
                     $('#school_logo').html('');
            }     
        });
    }

    var autocompany = true;
    function auto_complete_company(){
        if(autocompany)
        {
        	loading();
            $.ajax({
                url: '<?php echo base_url(); ?>' +"user/auto_complete_company/", 
                dataType: "json", 
                type:"post",
                cache: false,
                success: function (data) {
                	autocompany = false;
                	stop_loading();
                	var availableTags= [];
                    $.each(data,function(id,description) {
                        var item = {};
                        item["id"] = id;
                        item["value"] = description;

                        availableTags.push(item);
                    });
                    
                    $( "#company_name" ).autocomplete({
                    	appendTo:"#auto-company-container",
                    	source: availableTags,
                        minLength: 1,
                        select : function(event, ui){
                    		$('#company_name').attr("lang", ui.item.id);
	                    	show_company_logo(ui.item.value);	                		
                        }
                    });
                }     
            });
        }    	
    }
    
    function show_company_logo(company_name){
   	 if(typeof(company_name) == 'undefined' || company_name == '')
        {
        	var company_name      = $('#company_name').val();
		}
   	loading();
       $.ajax({ 
           url: '<?php echo base_url(); ?>' +"user/show_company_logo/", 
           type:"post",
           data:'company_name='+company_name,
           cache: false,
           success: function (data) {
        	   stop_loading();
               if (data.length > 0) {
                 var url = base_url+'company_logos/'+data; 
                 $('#company_logo').html('<img src="'+url+'" height="37" width="50">');
               }else
                    $('#company_logo').html(''); 
           }     
       });
   }
</script>
