<?php
$user_id = $this->session->userdata('user_id');
//$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_INSTANT_INTRO);
?>

<script type="text/javascript">
$(document).ready(function() {
	
	$(".grayHoverBox").live('click tap', function (e) {
		$(this).addClass('dark-grey-bg');
    	var id1 = $(this).attr('lang');
		var id2 = "<?php echo $this->utility->encode($user_data['user_id']); ?>";
       	window.location.href='<?php echo base_url();?>dates/chat_history/'+id1+'/'+id2;
    });
    
     $("a").click(function (event) {
        event.stopPropagation();
    });
});

</script>
<!--*********Page start*********-->
<div class="wrapper">
	<div class="content-part mobile-layout">
		<div class="Apply-Step1-a-main">
                    <div class="emp-B-tabing-prt mar-top2" id="userDateListing">                                                 
                    <?php 
                    if(!empty($chat_history)):
                        
                    foreach($chat_history as $key=>$val):?>
                    
                    <div class="dateRow grayHoverBox" lang="<?php echo $this->utility->encode($val['other_user_id']); ?>">
                                <div class="userBoxLeft">                                    
                                    <!-- -START PHOTO SECTION -->                                                                                                                           
                                    <?php 
                                    $profileurl=base_url() . url_city_name().'/user_info/'. $this->utility->encode($val['other_user_id']); 
                                    $chateurl=base_url() . 'dates/chat_history/' . $this->utility->encode($val['other_user_id']) . '/' . $this->utility->encode($user_data['user_id']); 
									
                                    ?>
                                    <a href="<?php echo $chateurl;?>">
                                        <img class='img-circle user-img' src="<?php echo base_url();?>user_photos/user_<?php echo $val['other_user_id'];?>/<?php echo $val['photo'];?>">
                                    </a>                                                                                          
                                    
                                </div>
                                <div class="userBoxRight"> 
                                    
                                        <div class="userChat">
                                           
                                                <div class="userbox-left-txt userChatName"> 
                                                    <span class="font-18 left" >
                                                        <a href="<?php echo $chateurl;?>">
                                                            <?php echo $val['first_name'];?>
                                                        </a>
                                                    </span>
                                                    <div class="left mar-left2 ">
                                                        <div class="astro <?php echo $val['user_gender']=='Male'?'male':'female'?>">
                                                            <span></span><?php echo $val['user_age']?>
                                                        </div>
                                                    </div>
                                                </div> 

                                                <div class="userbox-right-txt userChatMessage"> 
                                                    <span>
                                                        <?php if($val['is_read']=='0'): ?>
                                                            <b><?php echo $val['last_chat_message'];?></b>
                                                        <?php else: ?>
                                                            <?php echo $val['last_chat_message'];?>
                                                        <?php endif;?>                                                                                                           
                                                    </span>                                                    
                                                </div>
                                                <?php if($val['expiry_days'] > 0):?>
                                                        <p class="userbox-right-txt Red-color left bold-txt ">
                                                             Chat line closes in <?php echo $val['expiry_days'];?> days
                                                        </p>
                                                <?php endif;?>


                                            </div>                                                                                                                 
                                       
                                        <div class="userChatDate">

                                            <div class="userbox-left-txt userChatName">
                                                <?php if($val['is_read']=='0'): ?>
                                                        <span class="pink-colr">
                                                            <?php //echo $val['last_chat_time'];?> 
                                                            <?php //echo date("F j, Y",strtotime($val['last_chat_time']))?>
                                                            <?php echo chat_date_detail($val['last_chat_time']);?>
                                                        </span>
                                                <?php else: ?>
                                                     <?php echo chat_date_detail($val['last_chat_time']);?>
                                                <?php endif;?>                                            
                                            </div>
                                            <div class="astro female">
                                                                <?php echo $val['total_unread'];?>                                                   
                                                </div>
                                            <?php if($val['first_connected_date'] <= 7 && $val['first_connected_date'] >= 0):?>
                                                     <div class="expiryDate mar-top2">
                                                         New
                                                     </div>
                                            <?php endif;?>                                       
                                       </div> 
                                </div>
                        </div>
                        
                        
                        
                    <?php endforeach;?>
                    <?php else:?>
                        <div class="userChatBox">
                            <h2> <?php echo translate_phrase('No conversation found')?></h2>
                        </div>
                    <?php endif;?>
                    
                    </div>
		</div>
	</div>
</div>
<!--*********Page close*********-->
