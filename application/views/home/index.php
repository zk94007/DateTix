<script type="text/javascript">

	$(window).scroll(function () { 
		if ($(this).scrollTop() < 300) {
			$('#facepile-banner').hide();
		} else {
			$('#facepile-banner').show();
		}
	});

	$(document).ready(function(){
		
		$('#profession a').click(function(){
        	if($(this).hasClass('active') == false)
			{
	    		$('#profession a').addClass('active');
	    		$('#school a').removeClass('active');
	    		$('#school_data').hide();
	    		$('#profession_data').show();
	    		if($('#profession_data').find('.bx-viewport').length == 0)
	    		{
	    			//$('.proffestionSlider').bxSlider({'pager':false});
		    	}
	    	}
    	});
    	
    	$('#school a').click(function(){
			if($(this).hasClass('active') == false)
			{
				$('#school a').addClass('active');
	    		$('#profession a').removeClass('active');
	    		$('#profession_data').hide();
	    		$('#school_data').show();
	    		//$('#school_data').find('.bx-viewport').css('height','');
	    		
			}
    	});
    })
</script>

<div class="L-page-main">
    <div class="L-Banner-main" <?php if($this->session->userdata('backgroundImageUrl') != ""):?>style="background: url(<?php echo base_url().'assets/'.$this->session->userdata('backgroundImageUrl') ?> )no-repeat center top #0d0d0d;"<?php endif;?>>
      <div class="L-wrapper">
        <div class="L-apply-FB-Box">
          <div class="L-Find-love"><?php echo $banner_message ?></div>
          <div class="L-Find-Sub"><?php echo translate_phrase('Free confidential matchmaking service for single professionals from the') ?><a href="<?php echo base_url() . url_city_name() ?>/eligible-schools.html"><span><?php echo translate_phrase(' top 250 universities and colleges around the world') ?><span style="color: white">.</span></span></a></div>
          <?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>
          <div class="apply-privatly">		
			<a class="xl-fb-btn" href="javascript:;" onclick="fb_login();return false;"><img src="<?php echo base_url().'assets/images/fb-icn-big.jpg'?>" /><?php echo translate_phrase('Apply quicker with') ?> <b>Facebook</b></a>			
          </div>
          <div class="L-post-anyThing"><a style="color: #65bae6;font-family: Conv_MyriadPro-Regular !important;" href="<?php echo base_url() . url_city_name().'/benifits-of-facebook.html' ?>"><span style="color: white !important">(</span><?php echo translate_phrase("We won't post anything to your Facebook, but we want to make sure we don't intro you to your friends!") ?><span style="color: white">)</span></a></div>
          <?php endif;?>
          <a href="<?php echo base_url() . url_city_name() ?>/apply.html<?php if ($invite_id) { echo "?invite_id=$invite_id&highlight=1"; } else{ echo '?highlight=1';} ?>"  title="<?php echo translate_phrase('Apply Without Facebook') ?>"><div class="L-apply-w-fb"><?php echo translate_phrase('Apply Without Facebook');?></div></a>
          <div class="L-membersip" style="text-align:left"><img src="<?php echo base_url()?>assets/images/star.png" alt="img" /><span><?php echo translate_phrase('MEMBERSHIP STRICTLY BY APPLICATION ONLY')?></span><img src="<?php echo base_url()?>assets/images/star.png" alt="img" /></div>
        </div>
      </div>
    </div>
    <div class="L-why-aply">
      <div class="L-wrapper">
        <div class="L-wa-main">
         <div class="L-centr1">
          <div class="L-wa-Left">
            <div class="L-wa-head"><?php echo translate_phrase('WHY APPLY TO DATETIX');?></div>
          </div>
          <div class="L-how-itworks"><a href="<?php echo base_url() . url_city_name() ?>/how-it-works.html" class=""><?php echo translate_phrase('How It Works');?></a></div>
         </div>
          <div class="L-wA-List">
            <ul>
              <li>
                <h2><?php echo translate_phrase('High quality members');?></h2>
                <div class="L-wA-icn"> <img src="<?php echo base_url()?>assets/images/exclusive-icn.png" alt="img" /></div>
                <div class="L-wA-Bpart">
                  <ul>
                    <li><?php echo translate_phrase('All our member are single professionals from the');?>&nbsp;<a href="<?php echo base_url() . url_city_name() ?>/eligible-schools.html"><span class="L-semiB Underline"><?php echo translate_phrase('top 250 universities and colleges around the world');?></span></a></li>
                    <li><?php echo translate_phrase('Strict member verification and screening to ensure authenticity');?></li>
                  </ul>
                </div>
              </li>
              <li>
                <h2 class="R-align"><?php echo translate_phrase('Always free');?></h2>
                <div class="L-wA-Bpart">
                  <ul>
                    <li><?php echo translate_phrase('Free unlimited basic membership offered to all qualified and verified single professionals');?></li>
                    <li><?php echo translate_phrase('Pay for optional premium membership only if you want to enjoy special VIP privileges');?></li>
                  </ul>
                </div>
                <div class="L-wA-icn-RIGHT"><img src="<?php echo base_url()?>assets/images/exclusive-icn.png" alt="img" /></div>
              </li>
              <li>
                <h2><?php echo translate_phrase('Strictly confidential');?></h2>
                <div class="L-wA-icn"><img src="<?php echo base_url()?>assets/images/confiden-icn.png" alt="img" /></div>
                <div class="L-wA-Bpart">
                  <ul>
                    <li><?php echo translate_phrase('Your profile will only be very selectively shown to your best matches, and you choose which personal details to reveal');?></li>
                    <li><?php echo translate_phrase('No profile browsing or searching allowed, ever');?></li>
                  </ul>
                </div>
              </li>
              <li class="bg-none">
                <h2 class="R-align"><?php echo translate_phrase('Long-term relationships');?></h2>
                <div class="L-wA-Bpart">
                  <ul>
                    <li><?php echo translate_phrase('Designed exclusively to attract commitment-minded singles seeking long-term relationships');?></li>
                    <li><?php echo translate_phrase('Only the very best and most selective matches are introduced to you');?></li>
                  </ul>
                </div>
                <div class="L-wA-icn-RIGHT"><img src="<?php echo base_url()?>assets/images/handpicked.png" alt="img" /></div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
	<div class="L-SMI-MAIn">
      <div class="L-wrapper">
        <div class="L-SMI-left">
            <div class="L-wa-head L-SMI-pad-B"><?php echo translate_phrase('SELECTED MEMBERS IN');?>&nbsp;<a href="<?php echo base_url().  url_city_name().'/change-city.html?return_to=index.html'?>"><span style="text-transform: uppercase;font-family: 'Conv_MyriadPro-Regular';"><?php echo get_current_city() ?></span></a></div>
        </div>
        <div class="L-SMI-Tab">
	          <ul>
	            <li class="spc-non active" id="profession"><a class="active" href="javascript:;"><?php echo translate_phrase('By Profession') ?></a></li>
	            <li class="" id="school"><a href="javascript:;"><?php echo translate_phrase('By School') ?></a></li>
	          </ul>
	          
        </div>
      </div>
    </div>
    
    <div id="InationalBg">
    	
    <?php if(isset($school_datas) && $school_datas):?>
    
      <!-- School Data -->
      <div class="L-wrapper hidden" id="school_data">
      	<div class="L-Inational-f">
          <?php if(isset($school_datas['international']) && $school_datas['international']):?>
          <div class="Inational-main">
            <h3><?php echo translate_phrase('International Schools');?></h3>
            <!--<ul class="schoolSlider">-->
            <ul>
            <?php 
            $i=0;
            while($i<count($school_datas['international'])):?>
            <?php 
            $j = $i+1;
            ?>
            	<li>
		            <div class="Inational-MAinColl1">
		              <!-- First Column -->
		              <div class="Inational-Coll1">
		                <div class="L-hu-logMAin">
		                  <div class="L-hu-HEAD"><?php echo $school_datas['international'][$i]['info']['school_name']?></div>
		                  <div class="L-hu-LOGO">
		                  		<img src="<?php echo base_url()."school_logos/".$school_datas['international'][$i]['info']['logo_url'];?>" width="100" height="96" alt="school" />
		                  </div>
		                </div>
		                
            			<div class="L-hu-INEER-M">
		                <?php if(isset($school_datas['international'][$i]['students']) && is_array($school_datas['international'][$i]['students'])):?>
		                  <?php foreach ($school_datas['international'][$i]['students'] as $student):?>
                                        <?php 
                                                if((strtolower($student['gender'])) == 'female')
                                                {
                                                    $femaleClass = 'female-color-class';
                                                }
                                                else
                                                {
                                                    $femaleClass = 'male-color-class';
                                                }
                                                
                                            ?>
		                  	<div class="l-hu-Row1">
		                  		<?php  $y= date('Y',strtotime($student['birth_date'])); 
		                  			$age = date('Y')-$y; ?>
                               	<div class="l-hu-MALE"><a href="<?php echo base_url().url_city_name().url_city_name().'/apply.html'?>"><span class="<?php echo $femaleClass?>"><?php echo ($student['birth_date']!= "0000-00-00")?$student['age']:"00";?>,&nbsp;&nbsp;<?php echo $student['gender']?></span></a></div>
			                    <p><?php echo $student['job_title']?></p>
				                <p><?php echo $student['industry']?></p>
			                </div>
			              <?php endforeach;?>
		                 <?php endif;?>
		                </div>
		                
		                 <div class="L-hu-INEER-M">
		                          <div class="View-MEM-MAIn">
                                                  <div class="View-MEM-but"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><?php echo translate_phrase('VIEW MORE MEMBERS')?><img src="<?php echo base_url()?>assets/images/arw-right.png" alt="img" /> </a></div>
				                  </div>
			            </div>
		               </div>
		              
		              <?php if(isset($school_datas['international'][$j])):?>
		              <!-- Second Column -->
		              <div class="Inational-Coll1 Inational-Mar-L">
		                <div class="L-hu-logMAin">
		                  <div class="L-hu-HEAD"><?php echo $school_datas['international'][$j]['info']['school_name']?></div>
		                  <div class="L-hu-LOGO">
		                  		<img src="<?php echo base_url()."school_logos/".$school_datas['international'][$j]['info']['logo_url'];?>" width="100" height="96" alt="school" />
		                  </div>
		                </div>
		                
            			<div class="L-hu-INEER-M">
		                <?php if(isset($school_datas['international'][$j]['students']) && is_array($school_datas['international'][$j]['students'])):?>
		                  <?php foreach ($school_datas['international'][$j]['students'] as $student):?>
		                  	<div class="l-hu-Row1">
                                            <?php
                                                unset($femaleClass);
                                                if((strtolower($student['gender'])) == 'female')
                                                {
                                                    $femaleClass = 'female-color-class';
                                                }
                                                else
                                                {
                                                    $femaleClass = 'male-color-class';
                                                }
                                                
                                            ?>
                  				<?php  $y= date('Y',strtotime($student['birth_date'])); 
		                  			$age = date('Y')-$y; ?>
			                    <div class="l-hu-MALE"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><span class="<?php echo $femaleClass?>"><?php echo ($student['birth_date']!= "0000-00-00")?$student['age']:"00";?>,&nbsp;&nbsp;<?php echo $student['gender']?></span></a></div>
			                     <p><?php echo $student['job_title']?></p>
				                    <p><?php echo $student['industry']?></p>
			                </div>
			              <?php endforeach;?>
		                 <?php endif;?>
		                </div>
		                
		                <div class="L-hu-INEER-M">
		                          <div class="View-MEM-MAIn">
				                    <div class="View-MEM-but"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><?php echo translate_phrase('VIEW MORE MEMBERS');?> <img src="<?php echo base_url()?>assets/images/arw-right.png" alt="img" /> </a></div>
				                  </div>
		                </div>
		              </div>
		              <?php endif;?>
		             </div>
            	</li>
            <?php 
            $i = $j+1;
            endwhile;
			
            //end internation [foreach]?>
            </ul>
            </div>
          <?php endif; // end internation [if]?>
          
          <!-- ::::::::::::::        LOCAL SCHOOLS  ::::::::::::::::: -->
          <?php if(isset($school_datas['local']) && $school_datas['local']):?>
          <div class="Inational-main">
            <h3><?php echo translate_phrase('Local Schools');?></h3>
            <!--<ul class="schoolSlider">-->
            <ul>
            <?php 
            $i=0;
            while($i<count($school_datas['local'])):?>
            <?php 
            $j = $i+1;
            ?>
            	<li>
		            <div class="Inational-MAinColl1">
		              <!-- First Column -->
		              <div class="Inational-Coll1">
		                <div class="L-hu-logMAin">
		                  <div class="L-hu-HEAD"><?php echo $school_datas['local'][$i]['info']['school_name']?></div>
		                  <div class="L-hu-LOGO">
		                  		<img src="<?php echo base_url()."school_logos/".$school_datas['local'][$i]['info']['logo_url'];?>" width="100" height="96" alt="school" />
		                  </div>
		                </div>
		                
            			<div class="L-hu-INEER-M">
		                <?php if(isset($school_datas['local'][$i]['students'])):?>
			                  <?php foreach ($school_datas['local'][$i]['students'] AS $student):?>
				                  	<div class="l-hu-Row1">
				                  		<?php  $y= date('Y',strtotime($student['birth_date'])); 
		                  			$age = date('Y')-$y; ?>
					                    <div class="l-hu-MALE"><?php echo ($student['birth_date']!= "0000-00-00")?$student['age']:"00";?>, <?php echo $student['gender']?></div>
					                      <p><?php echo $student['job_title']?></p>
				                    	<p><?php echo $student['industry']?></p>
					                </div>
				              <?php endforeach;?>
		                 <?php endif;?>
		                </div>
		                
		                 <div class="L-hu-INEER-M">
		                  
			            	      <div class="View-MEM-MAIn">
				                    <div class="View-MEM-but"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><?php echo translate_phrase('VIEW MORE MEMBERS');?> <img src="<?php echo base_url()?>assets/images/arw-right.png" alt="img" /> </a></div>
				                  </div>
			            </div>
		                
		              </div>
		              
		              <?php if(isset($school_datas['local'][$j])):?>
		        <!-- Second Column -->
		              <div class="Inational-Coll1 Inational-Mar-L">
		                <div class="L-hu-logMAin">
		                  <div class="L-hu-HEAD"><?php echo $school_datas['local'][$j]['info']['school_name']?></div>
		                  <div class="L-hu-LOGO">
		                  		<img src="<?php echo base_url()."school_logos/".$school_datas['local'][$j]['info']['logo_url'];?>" width="100" height="96" alt="school" />
		                  </div>
		                </div>
		                
            			<div class="L-hu-INEER-M">
		                <?php if($school_datas['local'][$j]['students']):?>
			                  <?php foreach ($school_datas['local'][$j]['students'] AS $student):?>
			                  	<div class="l-hu-Row1">
			                  		<?php  $y= date('Y',strtotime($student['birth_date'])); 
		                  			$age = date('Y')-$y; ?>
				                    <div class="l-hu-MALE"><?php echo ($student['birth_date']!= "0000-00-00")?$student['age'] :"00";?>, <?php echo $student['gender']?></div>
				                    <p><?php echo $student['job_title']?></p>
				                    <p><?php echo $student['industry']?></p>
				                </div>
				              <?php endforeach;?>
		                 <?php endif;?>
		                </div>
		                
		                 <div class="L-hu-INEER-M">
		                          <div class="View-MEM-MAIn">
				                    <div class="View-MEM-but"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><?php echo translate_phrase('VIEW MORE MEMBERS');?> <img src="<?php echo base_url()?>assets/images/arw-right.png" alt="img" /> </a></div>
				                  </div>
		                </div>
		              </div>
		              <?php endif;?>
		            </div>
            	</li>
            <?php 
            $i = $j+1;
            endwhile;
			
            //end internation [foreach]?>
            </ul>
           </div>
          <?php endif; // end internation [if]?>
        </div>
      </div>
     
    <?php endif; //School data if end?>
  <!--  END School Info -->  
  
 <!-- Profession Data -->
      <?php if(isset($proffessional_data) && $proffessional_data):?>
       <div class="L-wrapper" id="profession_data">
      	<div class="L-Inational-f">
          <?php //if(isset($company_information) && $company_information):
          ?>
          <div class="Inational-main">
            <!--<h3><?php echo translate_phrase('Profession');?></h3>-->
            <ul class="proffestionSlider">
            <?php 
            $i=0;
            while($i<count($proffessional_data)):?>
            <?php 
            $j = $i+1;
            ?>
            	<li>
		            <div class="Inational-MAinColl1">
		              <!-- First Column -->
		              <div class="Inational-Coll1">
		                <div class="L-hu-logMAin">
		                  <div class="L-hu-HEAD"><?php echo $proffessional_data[$i]['description']?></div>
		                </div>
		    			
		    			<div class="L-hu-INEER-M">
		                <?php if($proffessional_data[$i]['employees']):?>
			                  <?php foreach ($proffessional_data[$i]['employees'] as $emp):?>
			                  <?php 
			                  	if((strtolower($emp['gender'])) == 'female')
                                {
                                	$femaleClass = 'female-color-class';
                                }
                                else
                            {
                                	$femaleClass = 'male-color-class';
                                }
                                ?>                                         
			                  	<div class="l-hu-Row1">
			                  		<?php  $y= date('Y',strtotime($emp['birth_date'])); 
	                  			$age = date('Y')-$y; ?>
				                    <div class="l-hu-MALE"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><span class="<?php echo $femaleClass?>"><?php echo ($emp['birth_date']!= "0000-00-00")?$emp['age']:"00";?>,&nbsp;&nbsp;<?php echo $emp['gender']?></span></a></div>
				                    <p><?php echo $emp['job_title']?></p>
				                    <p><?php echo $emp['school_name']?></p>
				                </div>
				              <?php endforeach;?>
		                 <?php endif;?>
		                </div>
		                
		                <div class="L-hu-INEER-M">
		                <?php foreach ($proffessional_data[$i]['company_datails'] as $company_information):?>
				                   		<?php if(file_exists("company_logos/".$company_information['logo_url'])):?>
					                        <div class="L-Row-logo">
				                   			<img src="<?php echo base_url()."company_logos/".$company_information['logo_url'];?>" alt="img" />
   					                   	</div>
				                   		<?php else:?>
				                   			<!--<img src="<?php echo base_url()."assets/images/404.jpg";?>" width="105" height="32" alt="img" />-->
				                   		<?php endif;?>
			             <?php endforeach; //company_information end?>
			             <div class="View-MEM-MAIn">
	                   		 <div class="View-MEM-but"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><?php echo translate_phrase('VIEW MORE MEMBERS');?> <img src="<?php echo base_url()?>assets/images/arw-right.png" alt="img" /> </a></div>
	                    </div>
		                </div>
		              </div>
		              
		              <?php if(isset($proffessional_data[$j]) && $proffessional_data[$j]):?>
		    		<!-- Second Column -->
		             <div class="Inational-Coll1 Inational-Mar-L">
		                <div class="L-hu-logMAin">
		                  <div class="L-hu-HEAD"><?php echo $proffessional_data[$j]['description']?></div>
		                </div>
		              	
		              	<div class="L-hu-INEER-M">
		                <?php if($proffessional_data[$j]['employees']):?>
			                  <?php foreach ($proffessional_data[$j]['employees'] as $emp):?>
			                  <?php 
			                  	if((strtolower($emp['gender'])) == 'female')
                                {
                                	$femaleClass = 'female-color-class';
                                }
                                else
                            {
                                	$femaleClass = 'male-color-class';
                                }
                                ?>                                         
			                  	<div class="l-hu-Row1">
			                  		<?php  $y= date('Y',strtotime($emp['birth_date'])); 
	                  			$age = date('Y')-$y; ?>
				                    <div class="l-hu-MALE"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><span class="<?php echo $femaleClass?>"><?php echo ($emp['birth_date']!= "0000-00-00")?$emp['age']:"00";?>,&nbsp;&nbsp;<?php echo $emp['gender']?></span></a></div>
				                    <p><?php echo $emp['job_title']?></p>
				                    <p><?php echo $emp['school_name']?></p>
				                </div>
				              <?php endforeach;?>
		                 <?php endif;?>
		                </div>
		                
		                <div class="L-hu-INEER-M">
		                <?php foreach ($proffessional_data[$j]['company_datails'] as $company_information):?>
				                   		<?php if(file_exists("company_logos/".$company_information['logo_url'])):?>
					                        <div class="L-Row-logo">
				                   			<img src="<?php echo base_url()."company_logos/".$company_information['logo_url'];?>" alt="img" />
		   				                </div>
				                   		<?php else:?>
				                   			<!--<img src="<?php echo base_url()."assets/images/404.jpg";?>" width="105" height="32" alt="img" />-->
				                   		<?php endif;?>
			             <?php endforeach; //company_information end?>
			             <div class="View-MEM-MAIn">
	                   		 <div class="View-MEM-but"><a href="<?php echo base_url().url_city_name().'/apply.html'?>"><?php echo translate_phrase('VIEW MORE MEMBERS');?> <img src="<?php echo base_url()?>assets/images/arw-right.png" alt="img" /> </a></div>
	                    </div>
		                </div>
		                
		              </div>
		              <?php endif;?>
		            </div>
            	</li>
            <?php 
            $i = $j+1;
            endwhile; //end internation [foreach]?>
            </ul>
           </div>
          <?php //endif; // end internation [if]?>
          
          <!-- ::::::::::::::        LOCAL SCHOOLS  ::::::::::::::::: -->
         <!-- ::::::::::::::      End  LOCAL SCHOOLS  ::::::::::::::::: -->
        </div>
      </div>
       <?php endif; //Profession data if end?>
      <!--  End Profession Data -->
    </div>
</div>
