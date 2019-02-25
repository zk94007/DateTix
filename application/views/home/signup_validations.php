 function place_holder(id){ var value = $("#"+id).val();
$("#"+id).click(function () { $("#"+id).val(''); });
$("#"+id).blur(function (){ if($("#"+id).val() == ''){
$("#"+id).val(value); } }); } function validate_email(email) { var re =
/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
return re.test(email); } function required_field(id){
$("#"+id).change(function(){ var value = $("#"+id).val();
if(value.match(/\S/)){ return true; } else { return false; } }); }



function check_vals(value){ if(value != undefined && value != ' ' &&
value != ''){ if(value.match(/\S/)){ return true; } else { return false;
} }else { return false; } } function check_if_only_number(value){
if(isNaN(value)){ return false; }else { return true; } } function
check_of_spcl_char(value){ var iChars =
"!@#$%^&*()+=-[]\\\';,./{}|\":<>?"; for (var i = 0; i < value.length;
i++) { if (iChars.indexOf(value.charAt(i)) != -1) { return true; } }
return false; } function check_image_upload_file_size(field_id){ var
mime_types = ['image/jpeg' , 'image/jpeg', 'image/png', 'image/png',
'image/gif']; if (typeof $(field_id)[0].files == "object") { var size =
$(field_id)[0].files[0].size; if(size > 2621440){ return true; } }else {
return false; } } function check_image_upload_file_type(field_id){ var
mime_types = ['image/jpeg' , 'image/jpeg', 'image/png', 'image/png',
'image/gif']; if (typeof $(field_id)[0].files == "object") { var type =
$(field_id)[0].files[0].type; if($.inArray(type, mime_types) == -1){
return true; } else { return false; } } else { var file_name =
$(field_id).val(); var ext = (/[.]/.exec(file_name)) ?
/[^.]+$/.exec(file_name) : undefined; if(ext != 'png' && ext != 'jpg' &&
ext != 'jpeg' && ext != 'gif'){ return true; }else{ return false; } } }



function check_if_string_has_number(value){ for(i = 0; i <= 9; i++) {
if(value.indexOf(i) != -1 ){ return true; } } return false; } function
check_checkbox(chks){ var hasChecked = false; for (var i = 0; i <
chks.length; i++){ if (chks[i].checked){ hasChecked = true; break; } }
if (hasChecked == false){ return false; } return true; } function
hide_step_message(step, form) { $('#' + form + '
.js-handle-step-message' + step).html(''); $('#' + form + '
.js-handle-step-message' + step).css('width', '20px'); $('#' + form + '
.js-handle-field-messages' + step).css('width', '426px'); } function
show_val_message(message, message_type, form, cRegM_field){ var effect =
''; var tm_id = new Date(); tm_id = tm_id.getMilliseconds();

if(message_type == 'error'){ if(form=="signup3")
$("#"+cRegM_field).addClass("error_msg"); else if(form=="signup4")
$("#"+cRegM_field).addClass("error_msg error_msg_align"); else
$("#"+cRegM_field).addClass("error_indentation error_msg"); $('#' + form
+ ' #' + cRegM_field).html( message ); }else if(message_type ==
'success'){ if(form=="signup3")
$("#"+cRegM_field).removeClass("error_msg"); else if(form=="signup4")
$("#"+cRegM_field).removeClass("error_msg error_msg_align"); else
$("#"+cRegM_field).removeClass("error_indentation error_msg"); $('#' +
form + ' #' + cRegM_field).html(''); } $('#s_mes'+ tm_id).show('slow');
} $(document).ready(function(e) {

$("#want_annual_income").keypress(function (e) { //if the letter is not
digit then display error and don't type anything if (e.which != 8 &&
e.which != 0 && (e.which < 48 || e.which > 57)) { return false; } });

$("#job_city").click(function () { var city = $( "#job_city" ).val(); $(
"#job_city" ).autocomplete({ source: '
 <?php echo base_url(); ?>
' +"user/job_location_autocomplete/?term="+city }); });


$("#interest").click(function () { var interest = $( "#interest"
).val(); $( "#interest" ).autocomplete({ source: '
 <?php echo base_url(); ?>
' +"user/autocomplete_interest/?term="+interest }); });

$("#heared_abou_us").change(function () { var other_input =
$("#heard_about_us_other"); var selected =
$(this).children('option:selected'); other_input.val(''); // check other
if (selected.attr('data-other') == 1) { other_input.attr('placeholder',
selected.attr('data-placeholder')); other_input.show(); } else {
other_input.attr('placeholder', ''); other_input.hide(); } })

$("#upload").click(function () { ajax_file_upload('fileToUpload');
return false; }); $("#business_photo").click(function () {
ajax_file_upload('photo_business_card'); return false; });
$("#photo_id").click(function () {
ajax_file_upload('photo_id_or_passport'); return false; });
$("#school_photo").click(function () {
ajax_file_upload('photo_diploma'); return false; }); function
ajax_file_upload(file){ $.ajaxFileUpload({
url:"http://datetix.lbclients.info/user/upload/"+file,
//url:"http://localhost/datetix/user/upload/"+file, secureuri:false,
fileElementId:file, dataType: 'json', success: function (data, status) {
if(typeof(data.error) != 'undefined') { if(data.error != '') {
//alert(data.error); if(file=='fileToUpload'){
$('#profile_photo_err').html('
<div
	style="float: left; width: 294px; color: #FD2080; height: 8px; margin-left: 170px;">'+data.error+'</div>
'); } if(file=='photo_business_card'){ $('#business_photo_err').html('
<div
	style="float: left; width: 294px; color: #FD2080; height: 8px; margin-left: 170px;">'+data.error+'</div>
'); } if(file=='photo_id_or_passport'){ $('#passport_photo_err').html('
<div
	style="float: left; width: 294px; color: #FD2080; height: 8px; margin-left: 170px;">'+data.error+'</div>
'); } }else { //alert(data.msg); if(file=='fileToUpload'){
$('#list_photo').html(''); $("#list_photo").append(data.msg);
$('#profile_photo_err').html(''); } if(file=='photo_business_card'){
$('#business_photo_id').html('');
$("#business_photo_id").append(data.msg);
$('#business_photo_err').html(''); } if(file=='photo_id_or_passport'){
$('#passport_photo').html(''); $("#passport_photo").append(data.msg);
$('#passport_photo_err').html(''); } //school photo
if(file=='photo_diploma'){ $('#school_photo_id').html('');
$("#school_photo_id").append(data.msg); $('#school_photo_err').html('');
} } } }, error: function (data, status, e) { alert(e); } }); }


$("#ethnicity").blur(function () { var ethnicity =
$("#ethnicity").val(); if(ethnicity== ''){ show_val_message('
 <?php echo translate_phrase('Ethnicity is required') ?>
', 'error', 'signup', "ethnicity_err"); } else { show_val_message('',
'success', 'signup', "ethnicity_err"); } });

$("#looking_for").blur(function () { var chks =
document.getElementsByName('looking_for[]'); if(!check_checkbox(chks)){
show_val_message('
 <?php echo translate_phrase('Relation ship type is required.') ?>
', 'error', 'signup', "rel_type_err"); } else { show_val_message('',
'success', 'signup', "rel_type_err"); } }); //
$("#current_location").blur(function () { // var location =
$("#current_location").val(); // if(!check_vals(location)){ //
show_val_message('Currently live in is required', 'error', 'signup',
"location_err"); // } else if (check_if_only_number(location)) { //
show_val_message('Currently live in can not be a number', 'error',
'signup', "location_err"); // } else { // show_val_message('',
'success', 'signup', "location_err"); // } // }); //
$("#country").blur(function () { // var cRegF_country =
$("#country").val(); // if(cRegF_country == ''){ //
show_val_message('Country is required.', 'error', 'signup',
"country_err"); // } else { // show_val_message('', 'success', 'signup',
"country_err"); // } // }); $("#residence_type").blur(function () { var
residence_type = $("#residence_type").val(); if(residence_type==''){
show_val_message('
 <?php echo translate_phrase('Residence type is required') ?>
', 'error', 'signup', "residence_type_err"); } else {
show_val_message('', 'success', 'signup', "residence_type_err"); } });
$("#year").blur(function () { var year = $("#year").val(); if(year==''
){ show_val_message('
 <?php echo translate_phrase('Birth year is required') ?>
', 'error', 'signup', "dob_yr_err"); } else{ show_val_message('',
'success', 'signup', "dob_yr_err"); date_of_birth_validation(); } });
$("#month").blur(function () { var month = $("#month").val(); if(
month=='' ){ show_val_message('
 <?php echo translate_phrase('Birth month is required') ?>
', 'error', 'signup', "dob_month_err"); }else{
date_of_birth_validation(); show_val_message('', 'success', 'signup',
"dob_month_err"); } }); $("#date").blur(function () { var b_date =
$("#date").val(); if( b_date==''){ show_val_message('
 <?php echo translate_phrase('Birth day is required') ?>
', 'error', 'signup', "dob_date_err"); }else {
date_of_birth_validation(); show_val_message('', 'success', 'signup',
"dob_date_err"); } }); function date_of_birth_validation(){ var year =
$("#year").val(); var month = $("#month").val(); var b_date =
$("#date").val(); // prefix + to convert to numbers month = +(month)-1;
var date = new Date(+year, month, +b_date); if(month==0)month="0";
if(year!="" && month!="" && b_date!=""){ if(date.getFullYear() == year
&& date.getMonth() == month && date.getDate() == b_date) {
show_val_message('', 'success', 'signup', "dob_yr_err"); }else{
show_val_message('
 <?php echo translate_phrase('Invalid Date of birth') ?>
', 'error', 'signup', "dob_yr_err"); } } } $("#city_born").blur(function
() { var location = $("#city_born").val(); if(!check_vals(location)){
show_val_message('
 <?php echo translate_phrase('City is required') ?>
', 'error', 'signup', "city_born_err"); } else if
(check_if_only_number(location)) { show_val_message('
 <?php echo translate_phrase('City can not be a number') ?>
', 'error', 'signup', "city_born_err"); } else { show_val_message('',
'success', 'signup', "city_born_err"); } });
$("#country_born").blur(function () { var cRegF_country =
$("#country_born").val(); if(cRegF_country == ''){ show_val_message('
 <?php echo translate_phrase('Country is required') ?>
', 'error', 'signup', "country_born_err"); } else { show_val_message('',
'success', 'signup', "country_born_err"); } });
$("#nationality").blur(function () { var nationality =
$("#nationality").val(); if(nationality == ''){ show_val_message('
 <?php echo translate_phrase('Nationality is required') ?>
', 'error', 'signup', "nationality_err"); } else { show_val_message('',
'success', 'signup', "nationality_err"); } });
$("#height").blur(function () { var height = $("#height").val().trim();
if(height == ''){ show_val_message('
 <?php echo translate_phrase('Please specify your height') ?>
', 'error', 'signup', "height_err"); } else if (isNaN(height)) {
show_val_message('
 <?php echo translate_phrase('Invalid height') ?>
', 'error', 'signup', "height_err"); } else { show_val_message('',
'success', 'signup', "height_err"); } }); $("#feet").blur(function () {
height_validation(); }); $("#inches").blur(function () {
height_validation(); }); function height_validation(){ var feet =
$("#feet").val().trim(); var inches = $("#inches").val().trim(); if(feet
== ''){ show_val_message('
 <?php echo translate_phrase('Please specify your height') ?>
', 'error', 'signup', "height_err"); } else if(inches == ''){
show_val_message('
 <?php echo translate_phrase('Please specify your height') ?>
', 'error', 'signup', "height_err"); } else { show_val_message('',
'success', 'signup', "height_err"); } } $("#body_type").blur(function ()
{ var body_type = $("#body_type").val(); if(body_type == ''){
show_val_message('
 <?php echo translate_phrase('Body type is required.') ?>
', 'error', 'signup', "body_type_err"); } else { show_val_message('',
'success', 'signup', "body_type_err"); } }); $("#looks").blur(function
() { var looks = $("#looks").val(); if(looks == ''){ show_val_message('
 <?php echo translate_phrase('Looks is required.') ?>
', 'error', 'signup', "looks_err"); } else { show_val_message('',
'success', 'signup', "looks_err"); } }); $("#eye_color").blur(function
() { var eye_color = $("#eye_color").val(); if(eye_color == ''){
show_val_message('
 <?php echo translate_phrase('Eye color is required.') ?>
', 'error', 'signup', "eye_color_err"); } else { show_val_message('',
'success', 'signup', "eye_color_err"); } });
$("#hair_color").blur(function () { var hair_color =
$("#hair_color").val(); if(hair_color == ''){ show_val_message('
 <?php echo translate_phrase('Hair color is required.') ?>
', 'error', 'signup', "hair_color_err"); } else { show_val_message('',
'success', 'signup', "hair_color_err"); } });
$("#hair_length").blur(function () { var hair_length =
$("#hair_length").val(); if(hair_length == ''){ show_val_message('
 <?php echo translate_phrase('Hair length is required.') ?>
', 'error', 'signup', "hair_length_err"); } else { show_val_message('',
'success', 'signup', "hair_length_err"); } });
$("#skin_tone").blur(function () { var skin_tone =
$("#skin_tone").val(); if(skin_tone == ''){ show_val_message('
 <?php echo translate_phrase('Skin tone is required.') ?>
', 'error', 'signup', "skin_tone_err"); } else { show_val_message('',
'success', 'signup', "skin_tone_err"); } });
$("#religious_belief").blur(function () { var religious_belief =
$("#religious_belief").val(); if(religious_belief == ''){
show_val_message('
 <?php echo translate_phrase('Religious belief is required.') ?>
', 'error', 'signup', "rel_belf_err"); } else { show_val_message('',
'success', 'signup', "rel_belf_err"); } });
$("#spoken_language").blur(function () { var spoken_language =
$("#spoken_language").val(); if(spoken_language == ''){
show_val_message('
 <?php echo translate_phrase('Language is required.') ?>
', 'error', 'signup', "spk_lang_err"); } else { show_val_message('',
'success', 'signup', "spk_lang_err"); } });
$("#proficiency").blur(function () { var spoken_language =
$("#proficiency").val(); if(spoken_language == ''){ show_val_message('
 <?php echo translate_phrase('Proficiency is required.') ?>
', 'error', 'signup', "prof_err"); } else { show_val_message('',
'success', 'signup', "prof_err"); } }); // validate school years
attended $("#years_attended_start").change(function() { var end =
$("#years_attended_end"); if (this.value && end.val() && this.value >
end.val()) { show_val_message('
 <?php echo translate_phrase('Start year must be  before or equal to end year.') ?>
', 'error', 'signup', "years_attended_err"); } else {
show_val_message('', 'success', 'signup', "years_attended_err"); }; });
$("#years_attended_end").change(function() { var start =
$("#years_attended_start"); if (this.value && start.val() && this.value
< start.val()) { show_val_message('
 <?php echo translate_phrase('Start year must be  before or equal to end year.') ?>
', 'error', 'signup', "years_attended_err"); } else {
show_val_message('', 'success', 'signup', "years_attended_err"); }; });

// validate company time period
$("#years_worked_start").change(function() { var end =
$("#years_worked_end"); if (this.value && end.val() && this.value >
end.val()) { show_val_message('
 <?php echo translate_phrase('Start year must be  before or equal to end year.') ?>
', 'error', 'signup', "time_period_err"); } else { show_val_message('',
'success', 'signup', "time_period_err"); }; });
$("#years_worked_end").change(function() { var start =
$("#years_worked_start"); if (this.value && start.val() && this.value <
start.val()) { show_val_message('
 <?php echo translate_phrase('Start year must be  before or equal to end year.') ?>
', 'error', 'signup', "time_period_err"); } else { show_val_message('',
'success', 'signup', "time_period_err"); }; });
$("#heared_abou_us").blur(function () { var heared_abou_us =
$("#heared_abou_us").val(); if(heared_abou_us == ''){ show_val_message('
 <?php echo translate_phrase('Please let us know how you heard about us.') ?>
', 'error', 'signup4', "hear_us_err"); } else { show_val_message('',
'success', 'signup4', "hear_us_err"); } }); }); function
next_step2(tab){ if(tab=='step2_basic'){ $('#basics').hide('show');
$('html,body').animate({ scrollTop:
$('#edit-profile').offset().top-150}, 'slow');
$('#personality').show('slow'); } if(tab=='step2_personality'){
$('#personality').hide('show'); $('html,body').animate({ scrollTop:
$('#edit-profile').offset().top-150}, 'slow');
$('#education').show('slow'); } if(tab=='step2_education'){
$('#education').hide('show'); $('html,body').animate({ scrollTop:
$('#edit-profile').offset().top-150}, 'slow');
$('#career').show('slow'); } if(tab=='step2_career'){
$('#career').hide('show'); $('html,body').animate({ scrollTop:
$('#edit-profile').offset().top-150}, 'slow');
$('#others').show('slow'); } } function next_step1() { var active_li =
$('.edit-profile-content ul li.active'); // call validation function,
var active_div = $(active_li).children('a').attr('href').substr(1);
if(active_div=='basics'){ if(basic_validaion()){ next_active_div(); } }
else if(active_div=='photos'){ if(photos_validaion()){
next_active_div(); } } else if(active_div=='others'){ return true; }
else{ next_active_div(); } } function next_active_div(){ var active_li =
$('.edit-profile-content ul li.active'); if ($(active_li).next().length
> 0) { $('html,body').animate({ scrollTop:
$('#edit-profile').offset().top-150}, 'slow');
$(active_li).next().children('a').trigger('click'); }else{
$('#ureg_sub').hide(); $('#submit_button').show(); } } function
basic_validaion(){ var flag=1; if ($('#residence_type').val()=='') {
show_val_message('
 <?php echo translate_phrase('Residence is required.') ?>
', 'error', 'signup', "residence_type_err"); flag=0; } if
($('#current_location').val()=='') { show_val_message('
 <?php echo translate_phrase('Currently live in  is required.') ?>
', 'error', 'signup', "location_err"); flag=0; }
if(!check_checkbox(document.signup.career_stage)){ show_val_message('
 <?php echo translate_phrase('Career stage is required.') ?>
', 'error', 'signup', "car_stg_err"); flag=0; }
if(!check_checkbox(document.signup.looking_for)){ show_val_message('
 <?php echo translate_phrase('Relationship type is required.') ?>
', 'error', 'signup', "rel_type_err"); flag=0; }
if(!check_checkbox(document.signup.want_to_date)){ show_val_message('
 <?php echo translate_phrase('Please let us know what you are looking for.') ?>
', 'error', 'signup', "want_to_err"); flag=0; } if
($('#ethnicity').val()=='') { show_val_message('
 <?php echo translate_phrase('Ethnicity is required.') ?>
', 'error', 'signup', "ethnicity_err"); flag=0; }
if(!check_checkbox(document.signup.gender)){ show_val_message('
 <?php echo translate_phrase('Gender is required.') ?>
', 'error', 'signup', "gender_err"); flag=0; } if(flag==0) return false;
else return true; } function check_want_to_date(){
if(!check_checkbox(document.signup.want_to_date)){ show_val_message('
 <?php echo translate_phrase('Please let us know what you are looking for.') ?>
', 'error', 'signup', "want_to_err"); }else{ show_val_message('',
'success', 'signup', "want_to_err"); } } function check_gender(){
if(!check_checkbox(document.signup.gender)){ show_val_message('
 <?php echo translate_phrase('Gender is required.') ?>
', 'error', 'signup', "gender_err"); }else{ show_val_message('',
'success', 'signup', "gender_err"); } } function
check_relationship_type(){
if(!check_checkbox(document.signup.looking_for)){ show_val_message('
 <?php echo translate_phrase('Relation ship type is required.') ?>
', 'error', 'signup', "rel_type_err"); }else{ show_val_message('',
'success', 'signup', "rel_type_err"); } } function check_career_stage(){
if(!check_checkbox(document.signup.career_stage)){ show_val_message('
 <?php echo translate_phrase('Gender is required.') ?>
', 'error', 'signup', "car_stg_err"); }else{ show_val_message('',
'success', 'signup', "car_stg_err"); } } function photos_validaion(){
var flag=1; if($('#use_meters').val()==1) var field = "height"; else var
field = "feet"; if ($('#spoken_language_id').val()=='') {
show_val_message('
 <?php echo translate_phrase('Language is required.') ?>
', 'error', 'signup', "spk_lang_err"); flag=0; }
if(!check_checkbox(document.signup.relationship_status)){
show_val_message('
 <?php echo translate_phrase('Relationship status is required.') ?>
', 'error', 'signup', "rln_status_err"); flag=0; } if
($('#religious_belief').val()=='') { show_val_message('
 <?php echo translate_phrase('Religious belief is required.') ?>
', 'error', 'signup', "rel_belf_err"); flag=0; }
if(!check_checkbox(document.signup.usually_wear)){ show_val_message('
 <?php echo translate_phrase('Eye wear is required.') ?>
', 'error', 'signup', "usl_wear_err"); flag=0; } if
($('#skin_tone').val()=='') { show_val_message('
 <?php echo translate_phrase('Skin tone required.') ?>
', 'error', 'signup', "skin_tone_err"); flag=0; } if
($('#hair_color').val()=='') { show_val_message('
 <?php echo translate_phrase('Hair color is required.') ?>
', 'error', 'signup', "hair_color_err"); flag=0; } if
($('#hair_length').val()=='') { show_val_message('
 <?php echo translate_phrase('Hair length required.') ?>
', 'error', 'signup', "hair_length_err"); flag=0; } if
($('#eye_color').val()=='') { show_val_message('
 <?php echo translate_phrase('Eye color is required.') ?>
', 'error', 'signup', "eye_color_err"); flag=0; } if
($('#looks').val()=='') { show_val_message('
 <?php echo translate_phrase('Looks is required.') ?>
', 'error', 'signup', "looks_err"); flag=0; } if
($('#body_type').val()=='') { show_val_message('
 <?php echo translate_phrase('Body type is required.') ?>
', 'error', 'signup', "body_type_err"); flag=0; } if
($('#user_height').val()=='') { show_val_message('
 <?php echo translate_phrase('Height  is required.') ?>
', 'error', 'signup', "height_err"); flag=0; } if
($('#user_height').val()!='') { var height = $('#user_height').val();
if(isNaN(height)){ show_val_message('
 <?php echo translate_phrase('Invalid height.') ?>
', 'error', 'signup', "height_err"); flag=0; } } if
($("#nationality_id").val()=='') { show_val_message('
 <?php echo translate_phrase('Nationality is required.') ?>
', 'error', 'signup', "nationality_err"); flag=0; } if
($('#country_born').val()=='') { show_val_message('
 <?php echo translate_phrase('Country is required.') ?>
', 'error', 'signup', "country_born_err"); flag=0; } if
($('#city_born').val()=='') { show_val_message('
 <?php echo translate_phrase('City is required.') ?>
', 'error', 'signup', "city_born_err"); flag=0; } if(
$('#date').val()==''){ show_val_message('
 <?php echo translate_phrase('Birth day is required') ?>
', 'error', 'signup', "dob_date_err"); flag=0; } if(
$('#month').val()=='' ){ show_val_message('
 <?php echo translate_phrase('Birth month is required') ?>
', 'error', 'signup', "dob_month_err"); flag=0; }
if($('#year').val()=='' ){ show_val_message('
 <?php echo translate_phrase('Birth year is required') ?>
', 'error', 'signup', "dob_yr_err"); flag=0; } if($('#year').val()!=''
&& $('#month').val()!='' && $('#b_date').val()!=''){ var year =
$('#year').val(); var month = $('#month').val(); var b_date =
$('#date').val(); // prefix + to convert to numbers month = +(month)-1;
var date = new Date(+year, month, +b_date); if(date.getFullYear() ==
year && date.getMonth() == month && date.getDate() == b_date) { }else{
show_val_message('
 <?php echo translate_phrase('Invalid Date of birth') ?>
', 'error', 'signup', "dob_yr_err"); flag=0; } } if(flag==0) return
false; else return true; } function check_eye_wear(){
if(!check_checkbox(document.signup.usually_wear)){ show_val_message('
 <?php echo translate_phrase('Eye wear is required.') ?>
', 'error', 'signup', "usl_wear_err"); }else{ show_val_message('',
'success', 'signup', "usl_wear_err"); } } function
check_relationship_status(){
if(!check_checkbox(document.signup.relationship_status)){
show_val_message('
 <?php echo translate_phrase('Relationship status is required.') ?>
', 'error', 'signup', "rln_status_err"); }else{ show_val_message('',
'success', 'signup', "rln_status_err"); } } function next_step3() { var
flag=1; // if(!check_checkbox(document.signup.date_num_people)){ //
show_val_message('Field is required.', 'error', 'signup',
"date_num_err"); // flag=0; // }
if(!check_checkbox(document.signup3.date_type)){ show_val_message('
 <?php echo translate_phrase('Please select the types of first dates you prefer.') ?>
', 'error', 'signup3', "date_type_err"); flag=0; }
if(!check_checkbox(document.signup3.preferred_date_days)){
show_val_message('
 <?php echo translate_phrase('Please select the days of the week that you are usually free for dates.') ?>
', 'error', 'signup3', "date_days_err"); flag=0; }
if(!check_checkbox(document.signup3.contact_method)){ show_val_message('
 <?php echo translate_phrase('Please select how you prefer your matchmaker to contact you.') ?>
', 'error', 'signup3', "contact_err"); flag=0; } if(flag==0) return
false; else return true; } //function check_date_num () { // //
if(!check_checkbox(document.signup.date_num_people)){ //
show_val_message('Field is required.', 'error', 'signup',
"date_num_err"); // } else { // show_val_message('', 'success',
'signup', "date_num_err"); // } // //} function check_date_type () {
if(!check_checkbox(document.signup3.date_type)){ show_val_message('
 <?php echo translate_phrase('Please select the types of first dates you prefer.') ?>
', 'error', 'signup3', "date_type_err"); } else { show_val_message('',
'success', 'signup3', "date_type_err"); } } function
check_preferred_date_days () {
if(!check_checkbox(document.signup3.preferred_date_days)){
show_val_message('
 <?php echo translate_phrase('Please select the days of the week that you are usually free for dates.') ?>
', 'error', 'signup3', "date_days_err"); } else { show_val_message('',
'success', 'signup3', "date_days_err"); } } function
check_contact_method () {
if(!check_checkbox(document.signup3.contact_method)){ show_val_message('
 <?php echo translate_phrase('Please select how you prefer your matchmaker to contact you.') ?>
', 'error', 'signup3', "contact_err"); } else { show_val_message('',
'success', 'signup3', "contact_err"); } } function next_step4() { var
flag=1; if ($('#heared_abou_us').val()=='') { show_val_message('
 <?php echo translate_phrase('Please let us know how you heard about us.') ?>
', 'error', 'signup4', "hear_us_err"); flag=0; } return flag; }
