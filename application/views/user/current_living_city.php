 public function check_show_postal_code(){ $language_id = 1; $city_id =
$this->input->post('city_id'); $current_country =
$this->model_user->getCountryByCity($city_id); $country_id =
$current_country ? $current_country->country_id : ''; $result =
$this->model_user->check_postal_code_exist($language_id,$country_id);
echo $result; } public function check_show_district(){ $language_id = 1;
$city_id = $this->input->post('city_id'); $result =
$this->model_user->check_district_exist($language_id,$city_id);
if($result==0) $data = 0; else $data =
$this->model_user->get_district($language_id,$city_id); echo $data; }
public function set_primary_photo(){ $id = $this->input->post('id');
$user_id = $this->session->userdata('user_id'); $result =
$this->model_user->set_primary_photo($id,$user_id); $msg =
$this->model_user->get_photo($user_id); echo $msg; } public function
show_height_field(){ $country_id = $this->input->post('id'); $result =
$this->model_user->check_use_meters($country_id); echo $result; } public
function add_spken_language(){ $user_id =
$this->session->userdata('user_id'); $spoken_language_id =
$this->input->post('spoken_language_id'); $proficiency_id =
$this->input->post('proficiency_id'); $result =
$this->model_user->insert_spoken_language($user_id,$spoken_language_id,$proficiency_id);
} public function remove_spken_language(){ $user_id =
$this->session->userdata('user_id'); $spoken_language_id =
$this->input->post('spoken_language_id'); $result =
$this->model_user->remove_spoken_language($user_id,$spoken_language_id);
} public function add_living_city(){ $language_id = 1; $user_id =
$this->session->userdata('user_id'); $city_name =
$this->input->post('city_id'); $result =
$this->model_user->insert_living_city($user_id,$city_name,$language_id);
} public function remove_living_city(){ $language_id = 1; $user_id =
$this->session->userdata('user_id'); $city_name =
$this->input->post('city_id'); $result =
$this->model_user->remove_living_city($user_id,$city_name,$language_id);
}
<script>
     function add_living_country(){
        var id             = document.getElementById('country_lived').value;
        var country_lived  = document.getElementById('country_lived').options[document.getElementById('country_lived').selectedIndex].text;
        var city_lived     = document.getElementById('city_lived').value;
        lived_country_id   = document.getElementById('lived_country_id').value;
        lived_city_id      = document.getElementById('lived_city_id').value;
        
        if(id!="" && city_lived!=""){
            $('#country_lived_err').html('');
            $('#city_liv_err').html(''); 
            
            if(lived_country_id!="") 
                var country_id    = lived_country_id+','+id; 
            else  
                var  country_id   = id;
           

            //city add
            if(lived_city_id!="") 
                var city_id    = lived_city_id+','+city_lived; 
            else  
                var  city_id   = city_lived;
            if (lived_city_id.indexOf(city_lived) == -1) {
                 $("#lived_country_id").val(country_id);
                $("#lived_city_id").val(city_id);
                var city_lived_id  = city_lived.split(' ').join('');
                $("#add_living").append('<li id="living'+city_lived_id+'" class="delete_list"><span class="plain-text" >'+city_lived+', '+country_lived+'</span><span><a href="javascript:remove_lived_city('+"'"+id+"','"+city_lived+"'"+');" title="Remove"><img src="images/delete.png"></a></span></li>');
            }

           
            
//            $.ajax({ 
//                    url: '<?php echo base_url(); ?>' +"user/add_living_city", 
//                    type:"post",
//                    data:'city_id='+city_lived,
//                    cache: false,
//                    success: function (msg) { 
//                        if(msg=='1'){
//                            if(lived_country_id!="") 
//                                var country_id    = lived_country_id+','+id; 
//                            else  
//                                var  country_id   = id;
//                            $("#lived_country_id").val(country_id);
//
//                            //city add
//                            if(lived_city_id!="") 
//                                var city_id    = lived_city_id+','+city_lived; 
//                            else  
//                                var  city_id   = city_lived;
//                            $("#lived_city_id").val(city_id);
//                            var city_lived_id  = city_lived.split(' ').join('');
//                            $("#add_living").append('<li id="living'+city_lived_id+'"><span class="plain-text" >'+city_lived+','+country_lived+'</span><span><a href="javascript:remove_lived_city('+"'"+id+"','"+city_lived+"'"+');" title="Remove">[Remove]</a></span></li>');
//                        }
//                    }   
//            });
       }else{ 
            if(id=="")
                $('#country_lived_err').html('<div style="float: left; width: 328px;color:#FD2080; height: 8px; margin-left:180px;"><?php echo translate_phrase("Counsfsdftry is required")?></div>');
            if(city_lived=="")
                $('#city_liv_err').html('<div style="float: left; width: 328px;color:#FD2080; height: 8px; margin-left:180px;"><?php echo translate_phrase("Cisdfty is required")?></div>');
       }
    }
     function remove_lived_city(country_id,city_id){
        var lived_country_array           = new Array();
        var lived_city_array              = new Array();
        var lived_country_id              = document.getElementById('lived_country_id').value;
        var lived_city_id                 = document.getElementById('lived_city_id').value;
        
        //remove from language hidden field
        var lived_country_array           = lived_country_id.split(','); 
        var country_index                 = lived_country_array.indexOf(country_id);
        lived_country_array.splice(country_index, 1);
        var lived_country_id              = lived_country_array.join(); 
        
        //remove from proficiency hidden field
        var lived_city_array              = lived_city_id.split(','); 
        var city_index                    = lived_city_array.indexOf(city_id);
        lived_city_array.splice(city_index, 1);
        var lived_city_id                 = lived_city_array.join(); 
       
        city_id  = city_id.split(' ').join('');
        $('#living'+city_id).remove();
        $("#lived_city_id").val(lived_city_id);
        $("#lived_country_id").val(lived_country_id);
        
//        $.ajax({ 
//            url: '<?php echo base_url(); ?>' +"user/remove_living_city/", 
//            type:"post",
//            data:'city_id='+city_id,
//            cache: false,
//            success: function (msg) {
//                 city_id  = city_id.split(' ').join('');
//                 $('#living'+city_id).remove();
//                 $("#lived_city_id").val(lived_city_id);
//                 $("#lived_country_id").val(lived_country_id);
//            }     
//       });
       
    }
        function remove_photo(id){
        $.ajax({ 
            url: '<?php echo base_url(); ?>' +"user/remove_photo/", 
            type:"post",
            data:"id="+id,
            cache: false,
            success: function (data) {
                 $('#ph_rm'+id).remove();
            }     
         });
    }
     function set_primary_photo(id){
        $.ajax({ 
            url: '<?php echo base_url(); ?>' +"user/set_primary_photo/", 
            type:"post",
            data:"id="+id,
            cache: false,
            success: function (data) {
                 $('#list_photo').html('');
                 $("#list_photo").append(data);
            }     
         });
    }
    function show_height(){
        var country = document.getElementById('country').value;
        if(country!=""){
            $.ajax({ 
                url: '<?php echo base_url(); ?>' +"user/show_height_field/", 
                type:"post",
                data:"id="+country,
                cache: false,
                success: function (data) {
                    if(data=='1'){
                        $('#height_cm').show();
                        $('#height_feet').hide();
                    }
                    else  {
                       $('#height_cm').hide();
                       $('#height_feet').show(); 
                    }  
                }     
            });
         }
    }
    function living_city(country,city){
       document.getElementById('current_location').value = city;
       document.getElementById('current_living').innerHTML = country+','+city;
       $.colorbox.close();
    }
    function show_district(city_id){
        if(city_id!=""){
            $.ajax({ 
                url: '<?php echo base_url(); ?>' +"user/check_show_district/", 
                type:"post",
                data:"city_id="+city_id,
                cache: false,
                success: function (data) {
                    if(data!=0){
                        $('#dist').show();
                        $('#district').html(data);
                    }
                    else{
                        $('#dist').hide();
                    }    
                }     
            });
        }else
            $('#dist').hide();
    }
    
    function show_postal_code(city_id){ 
        if(city_id!=""){
            $.ajax({ 
                url: '<?php echo base_url(); ?>' +"user/check_show_postal_code/", 
                type:"post",
                data:"city_id="+city_id,
                cache: false,
                success: function (data) {
                    if(data>0)
                        $('#postal').show();
                    else
                        $('#postal').hide();   
                }     
            });
         }else
            $('#postal').hide();
    }
</script>
