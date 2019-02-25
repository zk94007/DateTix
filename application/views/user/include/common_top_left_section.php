<?php $profileurl = base_url() . 'user/user_info/' . $this->utility->encode($user['user_id']); 
$primaryphoto=$this->model_date->get_current_primary_photo($user['user_id']);

?>


<a href="<?php echo $profileurl; ?>">
    <?php if(!empty($primaryphoto)) :?>
        <img class="img-circle user-img" alt="img"  src="<?php echo base_url(); ?>user_photos/user_<?php echo $user['user_id']; ?>/<?php echo $primaryphoto['photo']; ?>">
    <?php endif;?>
</a>


<div class="astro-line txt-center">
    <div class="Mar-bottom-5 astro <?php echo $user['gender_id'] == 1 ? 'male' : 'female' ?>"><span></span><?php echo $user['age'] ?></div>
    <?php if ($user['birth_date']): ?>
        <?php echo getStarSign(strtotime($user['birth_date'])); ?>
    <?php endif; ?>
    <div class="mar-top2 mutualsection">
        <a href="#" class="inline-element " style="padding: 0px;margin: 0px" onclick="return mutualFriendPopup('<?php echo $user_data['facebook_id']; ?>')">
            <span class="dt-icon icon-usr-group"></span>
            <?php
            $mutual_friends = $this->datetix->fb_mutual_friend($user['user_id'], $this->user_id);
            echo count($mutual_friends);
            ?>                                                    
        </a> 
        <a href="#commonInterest" class="inline-element commonInterest" style="padding: 0px;margin: 0px 5px 0px 10px">
            <span class="dt-icon icon-book"></span>                                                     
            <?php
            $commonIntereset = $this->model_date->commonInterest($user['user_id'], $user_data['user_id']);
            echo count($commonIntereset);
            ?>
        </a>
                                                       
    </div>                                        
    <div id="commonInterest" style="display: none">
        <h2>Common Interests</h2>
        <?php
        if (!empty($commonIntereset)):
            foreach ($commonIntereset as $key => $val):
                echo $val;
                echo "</br>";
            endforeach;
        else:
            echo "<p>You have no interests in common with " . $user['first_name'] . ".</p>";
            echo '<div class="column-100">
                                                            <div class="Nex-mar mar-top2">
                                                                <input type="button" onclick="$.fancybox.close();" class="btn btn-blue disable-butn right date-btn" value="Ok">
                                                            </div>
                                                        </div>';
        endif;
        ?>
    </div>
</div>