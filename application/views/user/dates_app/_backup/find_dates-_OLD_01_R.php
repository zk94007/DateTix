<script src="<?php echo base_url()?>assets/js/general.js"></script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_INSTANT_INTRO);
?>

<script>

$(document).ready(function () { 
        $("#nextDate").live('click',function(){                
                var date_id=$('#date_id').val();              
		loading();
		$.ajax({ 
                        url: '<?php echo base_url(); ?>' +"dates/get_next_date_list", 
                        type:"post",
                        data:{date_id:date_id},
                        cache: false,
                        success: function (data) {
                            stop_loading();
                            $("#userDateListing").html(data);
                        }     
                    });
	});
});

function getNextDate(date_id){
                loading();
		$.ajax({ 
                        url: '<?php echo base_url(); ?>' +"dates/get_next_date_list", 
                        type:"post",
                        data:{date_id:date_id},
                        cache: false,
                        success: function (data) {
                            stop_loading();
                            $("#userDateListing").html(data);
                        }     
                    });
}

function viewDate(date_id,decision){
                loading();
		$.ajax({ 
                        url: '<?php echo base_url(); ?>' +"dates/date_decision", 
                        type:"post",
                        data:{date_id:date_id,decision:decision},
                        cache: false,
                        success: function (data) {
                            stop_loading();
                            getNextDate(date_id);
                        }     
                    });
}
</script>
<!--*********Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			
                    <div class="Top3-M">
                        <div class="Top3-Inner-p">
                            <div class="fl">
                                <a href="<?php echo base_url();?>dates/my_dates" class="Edit-Button01 mar-R">
                                   <?php echo translate_phrase('My Dates')?>
                                   ( <?php echo count($user_date);?> )
                                </a>                                
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="emp-B-tabing-prt" id="userDateListing">                        
                           
                        <?php if(!empty($other_dates)):?>
                        
                                <div class="userTop">
                                    <div class="userDateLeft">                                    
                                        <!-- -START PHOTO SECTION -->
                                        <div class="img-left-box">
                                            <ul class="slider">
                                                <li class="img-slide">
                                                    <?php $profileurl=base_url() . 'user/user_info/' . $this->utility->encode($other_dates['requested_user_id']) . '/' . $this->utility->encode($user_data['user_id']) . '/' . $user_data['password']; ?>
                                                    <a href="<?php echo $profileurl;?>">
                                                        <img style="max-width: 98%;" src="<?php echo base_url();?>user_photos/user_<?php echo $other_dates['requested_user_id'];?>/<?php echo $other_dates['user_photo'];?>">
                                                    </a>
                                                </li>
                                            </ul>
                                        </div> 
                                        <div class="detail-list">
                                            <ul> 
                                                <li>
                                                    <?php 
                                                            $common_interest = $this->datetix->calculate_score($user_data['user_id'], $other_dates['requested_user_id']);
                                                            echo  count($common_interest) > 1 ? count($common_interest) . ' ' . translate_phrase('Common Interests') : count($common_interest) . ' ' . translate_phrase('Common Interests');
                                                    ?>
                                                </li>  
                                                <li>
                                                    <?php 
                                                            $mutual_friends = $this->datetix->fb_mutual_friend($user_data['user_id'], $other_dates['requested_user_id']);
                                                            echo  count($mutual_friends) > 1 ? count($mutual_friends) . ' ' . translate_phrase('Mutual Friends') : count($mutual_friends) . ' ' . translate_phrase('Mutual Friend');                                                    
                                                    ?>
                                                </li>  
                                            </ul> 
                                        </div>
                                    </div>
                                    <div class="userDateRight">
                                        <div class="userbox-innr">
                                            <div class="userbox-left-txt"> <!--Sent--> </div> 
                                            <div class="userbox-right-txt"> 
                                                <span class="pink-colr">
                                                    <?php echo $other_dates['first_name']." ".$other_dates['last_name'];?> , 
                                                </span> 
                                                <span class="pink-colr">
                                                    <?php echo $other_dates['user_gender'];?> , 
                                                </span> 
                                                <span class="pink-colr">
                                                    <?php echo $other_dates['birth_date'];?>
                                                </span>                                             
                                            </div> 
                                        </div> 
                                        <div class="detail-list">
                                            <ul> 
                                                <li><?php echo date('l, F jS', strtotime($other_dates['date_time']));?></li>                                             
                                            </ul> 
                                        </div>
                                        <div class="detail-list">
                                            <ul> 
                                                <li><?php echo $other_dates['gender'];?></li>        
                                                <li><?php echo $other_dates['intention_type'];?></li>        
                                            </ul>
                                            <br/><br/>
                                            <ul> 
                                                <li><?php echo $other_dates['date_type'];?></li>        
                                                <li><?php echo $other_dates['date_payer'];?></li>        
                                            </ul>
                                        </div> 
                                        <div class="userTopRowTxt">

                                            <span><?php echo $other_dates['name'];?> , </span>
                                            <span><?php echo $other_dates['address'];?></span>
                                        </div>                                                                         
                                    </div> 
                                   <div class="userDateButton">
                                       <p><?php echo translate_phrase('Are you interested ?')?></p>
                                        <input onclick="return viewDate(<?php echo $other_dates['date_id'];?>,'1')" id="yes_date" type="button" class="Next-butM" value="<?php echo translate_phrase('Yes')?>">
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input onclick="return viewDate(<?php echo$other_dates['date_id'];?>,'0')" id="no_date" type="button" class="Next-butM" value="<?php echo translate_phrase('No')?>">
                                    </div>
                                   <input type="hidden" id="date_id" value="<?php echo $other_dates['date_id'];?>"/>

                                </div>
                        <hr> 
                        <div class="right">                        
                                <input id="nextDate" type="button" class="Next-butM" value="<?php echo translate_phrase('Next')?>">                        
                        </div>
                        <?php else:?>
                                    <div class="userTop">
                                        <?php echo translate_phrase('No other date has been found')?>
                                    </div>
                        <?php endif;?>
                            <!-- END emp-B-tabbing-M -->
                    
                    
                    </div>
		</div>
	</div>
</div>
<!--*********Page close*********-->
