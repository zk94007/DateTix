<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id, PERMISSION_INSTANT_INTRO);
?>
<!--*********Page start*********-->
<div class="wrapper">
    <div class="content-part">
        <form name="newdate" id="newdateForm"
              action="<?php echo base_url() . 'dates/new_date_step6'; ?>"
              method="post">

            <!--*********Apply-Step1-E-Page personality start*********-->
            <div class="Apply-Step1-a-main">				
                <div class="step-form-Main">
                    <div class="step-form-Part">
                        <div class="edu-main">

                            <div class="aps-d-top">
                                <h2> <?php echo translate_phrase('Date Confirmation') ?></h2>
                                <div class="f-decrMAIN">
                                    <div class="f-decr">
                                        <p><?php echo date('l, F jS', strtotime($date_detail['date_time']));?></p>
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
                                    <div class="f-decr">
                                        <p><?php echo translate_phrase('Last Minute Date') ?> :
                                            10 <?php echo translate_phrase('date tickets') ?></p>
                                        </p>
                                    </div>
                                    <div class="f-decr">
                                        <p><?php echo $date_detail['intention_type'];?> :
                                        <?php echo $date_detail['relationship_num_date_tix'];?> <?php echo translate_phrase('date tickets') ?></p>
                                    </div> 
                                    
                                    <div class="f-decr">
                                        <p><?php echo $date_detail['venue'];?> :
                                        <?php echo $date_detail['budget_num_date_tix'];?> <?php echo translate_phrase('date tickets') ?></p>
                                    </div>                                                                
                                </div>
                                <hr>
                                <div class="f-decrMAIN">
                                    <div class="f-decr">
                                        <p><?php echo translate_phrase('Total') ?> :
                                            
                                            <?php 
                                            $total=10+$date_detail['relationship_num_date_tix']+$date_detail['budget_num_date_tix']
                                            ?>
                                            <?php echo $total;?> <?php echo translate_phrase('date tickets') ?></p>
                                        </p>
                                    </div>                                                               
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <p><?php echo translate_phrase('You will recive 100% refund of your date tickets if you receive less than 7 applications') ?></p>


                </div>
                <input type="hidden" name="num_date_tickets" value="<?php  echo $total;?>">
                <div class="Nex-mar">
                    <input id="submit_button" type="submit" class="Next-butM" value="<?php echo translate_phrase('Done') ?>">
                </div>
            </div>
            <!--*********Apply-Step1-E-Page close*********-->				
        </form>
    </div>
</div>
<!--*********Page close*********-->
