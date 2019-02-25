<?php if (isset($date_info['merchant_photos']) && $date_info['merchant_photos']): ?>			
    <div class="photosection flexslider">
        <ul class="slides">

            <?php foreach ($date_info['merchant_photos'] as $photos): ?>
                <li>
                    <p class="flex-caption">
                        <span class="dt-icon icon-marker"></span> <?php echo $date_info['name']; ?>,<?php echo $date_info['merchant_neighborhood']['description']; ?>
                    </p>	
                    <img src="<?php echo $photos['photo_url'] ?>" />							
                </li>
            <?php endforeach; ?>								
        </ul>
    </div>
<?php endif; ?>

<?php if ($user_data['user_id'] != $date_info['requested_user_id']): ?>

                                                <div class="dateRow Mar-bottom-5 txt-center">                

                                                    <a href="#commonInterest" class="inline-element commonInterest" style="padding: 0px;margin: 0px 5px 0px 10px">
                                                        <span class="dt-icon icon-book"></span>                                                     
                                                        <?php
                                                        $commonIntereset = $this->model_date->commonInterest($applicant_user['user_id'], $this->user_id);
                                                        echo count($commonIntereset);
                                                        ?>
                                                    </a>
                                                    <a href="#" class="inline-element" style="padding: 0px;margin: 0px" onclick="return mutualFriendPopup('<?php echo $user_data['facebook_id']; ?>')">
                                                        <span class="dt-icon icon-usr-group"></span>
                                                        <?php
                                                        $mutual_friends = $this->datetix->fb_mutual_friend($applicant_user['user_id'], $this->user_id);
                                                        echo count($mutual_friends);
                                                        ?>                                                    
                                                    </a>

                                                </div>

                                            <?php endif; ?>

<div id="commonInterest" style="display: none">
                                                <h2>Common Interests</h2>
                                                <?php
                                                if (!empty($commonIntereset)):
                                                    foreach ($commonIntereset as $key => $val):
                                                        echo $val;
                                                        echo "</br>";
                                                    endforeach;
                                                else:
                                                    echo "<p>No Common Interest Found.!</p>";
                                                endif;
                                                ?>
                                            </div>