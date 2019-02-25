
<link rel='stylesheet'  href="<?php echo base_url(); ?>assets/FlexSlider/flexslider.css" />
<script src="<?php echo base_url(); ?>assets/FlexSlider/jquery.flexslider.js"></script>

<?php $user_id = $this->session->userdata('user_id'); ?>
<script type="text/javascript">
    var offset = 1;
    $(document).ready(function () {

        loadSlider();
        $(".commonInterest").fancybox({
            maxWidth: 300,
            maxHeight: 600,
            width: '70%',
            height: '70%',
            afterClose: function () {
            },
        });

    });
    function loadSlider()
    {
        $('.flexslider').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: true,
            smoothHeight: true,
            slideshow: true
        });
    }

    function mutualFriendPopup(id,other_id){
    loading();
    $.ajax({
        url: '<?php echo base_url(); ?>' +"dates/mutual_friend/"+id+"/"+other_id, 
        type:"html",
        dataType:'html',
        success: function (response) {
			openFancybox(response);
			stop_loading();
        }  
    });
}

</script>
<div class="wrapper">
    <div class="content-part mobile-layout">
        <div class="Apply-Step1-a-main">
            <div class="step-form-Main mar-top2">
                <div class="datesArea bor-none" id="userDateListing" style="margin: 0px;padding: 0px">
                    <div class="dates-details">
                        <div class="dateRow">		
                            <div class="userBoxLeft">
                                <?php
                                        $content['user'] = $date_info;
                                        $content['user']['user_id'] = $date_info['requested_user_id'];
                                        $content['user_data'] = $user_data;
                                        echo $this->load->view('user/include/common_top_left_section', $content);
                                ?>
                            </div>
                            <div class="userBoxRight">
                                <?php echo $this->load->view('user/include/common_top_right_section', $content);?>
                            </div>
                        </div>
                        <div class="venue-info" style="margin-top:0px">
                            <?php if (isset($merchant_photos) && $merchant_photos): ?>			
                                <div class="photosection flexslider">
                                    <ul class="slides">

                                        <?php foreach ($merchant_photos as $photos): ?>
                                            <li>
                                                <p class="flex-caption">
                                                    <span class="dt-icon icon-marker"></span> <?php echo trim($date_info['name'],chr(0xC2).chr(0xA0)); ?>, <?php echo $merchant_neighborhood['description']; ?>
                                                </p>	
                                                <img src="<?php echo $photos['photo_url'] ?>" />							
                                            </li>
                                        <?php endforeach; ?>								
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>