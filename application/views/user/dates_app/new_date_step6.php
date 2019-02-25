<?php
$user_id = $this->session->userdata('user_id');
?>
<!--*********Page start*********-->
<div class="wrapper">
   <div class="content-part mobile-layout">
        <form name="newdate" id="newdateForm"
              action="<?php echo base_url() . 'dates/new_date_step6'; ?>"
              method="post">

            <!--*********Apply-Step1-E-Page personality start*********-->
            <div class="Apply-Step1-a-main ">				
                <div class="step-form-Main">
                    <div class="step-form-Part">
                        <div class=" edu-main">

                            <div class="aps-d-top">
                                <h2 class="DarkGreen-color dateTextCenter" > <?php echo translate_phrase('Date Confirmation') ?></h2>
                                <div class="f-decrMAIN ">
                                    <div class="dateTextCenter f-decr bold-txt">
                                        <p>
                                            <?php //echo date('l, F jS', strtotime($date_detail['date_time']));?>
                                            <?php echo print_date_day($date_detail['date_time']).' '.translate_phrase('at').' '.date('h:i A', strtotime($date_detail['date_time']));?>
                                        </p>
                                        <p><?php echo $date_detail['name'].", ".$date_detail['address'];?></p>
                                        <p>
                                            <?php echo translate_phrase('Looking For') ?>
                                            <?php echo $date_detail['gender'];?>
                                            <?php echo translate_phrase('For') ?>
                                            <?php echo $date_detail['intention_type'];?>
                                        </p>
                                    </div>
                                </div>                                                                
                            </div>							
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="step-form-Part">
                        <div class="edu-main">

                            <div class="aps-d-top">
                                <div class="f-decrMAIN">
                                    <table class="dateTable"  cellspacing="10" style="font-family:Conv_MyriadPro-Regular">
                                    	<?php if(strtotime($date_detail['date_time']) <= strtotime('+1 day')):?>
                                        <tr>
                                            <th><?php echo translate_phrase('Last minute date') ?>: </th>
                                            <td><?php echo LAST_MINUTE_DATE;?> <?php echo translate_phrase('date tickets') ?></td>
                                        </tr>
                                        <?php endif;?>
                                        
                                        <tr>
                                            <th><?php echo $date_detail['intention_type'];?>: </th>
                                            <td><?php echo $date_detail['relationship_num_date_tix'];?> <?php echo translate_phrase('date tickets') ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $date_detail['venue'];?> <?php echo translate_phrase('Budget');?>: </th>
                                            <td><?php echo $date_detail['budget_num_date_tix'];?> <?php echo translate_phrase('date tickets') ?></td>
                                        </tr>
                                    </table>
                                                                                                 
                                </div>
                                <hr>
                                 <table class="dateTable bold-txt" cellspacing="10">
                                        <tr>
                                            <th><?php echo translate_phrase('Total') ?> : </th>
                                            <td>
                                                <?php 
                                                $total=LAST_MINUTE_DATE+$date_detail['relationship_num_date_tix']+$date_detail['budget_num_date_tix']
                                                ?>
                                                <?php echo $total;?> <?php echo translate_phrase('date tickets') ?>
                                            </td>
                                        </tr>
                                    </table>
                                
                            </div>
                        </div>
                    </div>
                    
                    <p class="DarkGreen-color dateTextCenter bold-txt"><?php echo translate_phrase('You will receive 100% refund of your date tickets if you receive less than 7 applications') ?></p>


                </div>
                <input type="hidden" name="num_date_tickets" value="<?php  echo $total;?>">
                <div class="Nex-mar">
                    <input id="submit_button" type="submit" class="Next-butM" value="<?php echo translate_phrase('View My Dates') ?>">
                </div>
            </div>
            <!--*********Apply-Step1-E-Page close*********-->				
        </form>
    </div>
</div>
<!--*********Page close*********-->
