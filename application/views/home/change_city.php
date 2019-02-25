<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">

			<form novalidate="novalidate"
				action="<?php echo base_url()?>index.html"
				method="post" accept-charset="utf-8" id="form-index">
				<div class="popup-box popupMiddle" id="form-container">
					<div class="citypageHed" style="padding-top: 0px;">
						<h1>
						<?php echo translate_phrase('Select Your City');?>
						</h1>
						<br />
						<br />
						<p style="font-family: 'Conv_MyriadPro-Semibold'">
							<span><?php echo get_assets('name','DateTix')?></span>
							<?php echo translate_phrase('is currently only available in the following cities');?>.<br />
							<?php echo translate_phrase('Please click on the city that you live in');?> :
						</p>
					</div>
					<div class="cityArea">
					<?php if(!empty($city_data)):?>
					<?php foreach ($city_data as $countryName => $cities):?>

					<?php

					$flagUrl = 'country_flags/'.$cities['flagUrl'];
					if(!empty($cities['flagUrl']) && file_exists($flagUrl))
					{
						$flagUrl = base_url().$flagUrl;
					}
					else
					{
						$flagUrl = base_url().'assets/images/flag404.png';
					}


					?>
						<!--//print country details-->
						<div class="cityRow">
							<div class="statesName">
								<img style="height: 15px; width: 18"
									src="<?php echo $flagUrl?>">
								<div><?php echo $countryName;?> :</div>
							</div>
							<div class="city_div">
							<?php if(!empty($cities['cityData'])):?>
							<?php foreach ($cities['cityData'] as $key => $cityDescription):?>
							
								<a href="
								<?php if($cityDescription->city_id != 260):?>
									http://www.datetix.com/
								<?php else:?>
									http://www.datetix.hk/
								<?php endif;?>
								<?php if($return_to != url_city_name()):?>																
									<?php echo url_city_name().'/change-city.html?city_id='.$cityDescription->city_id.'&return_to='.$return_to?>"><?php echo $cityDescription->description;?></a>
								<?php else:?>
									<?php echo url_city_name().'/change-city.html?city_id='.$cityDescription->city_id?>"><?php echo $cityDescription->description;?></a>
								<?php endif;?>
							<?php endforeach;?>
							<?php endif;?>
							</div>
						</div>
						<?php endforeach;?>
						<?php endif;?>
						<div style="clear: left"></div>
						<div style="margin-top: 20px" class="cityTxt"><?php echo translate_phrase('Additional cities coming soon');?> !</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
</div>


<!--<style>
    #cityDiv ul li
    {
        display: inline-block;
        padding-right: 40px;
    }
</style>
<div id="change_cities_popup" style="text-align: left;margin-left: 45px;margin-top: 120px">
    <h3>
        <?php //echo translate_phrase('DATETIX is currently only available in the following cities.') ?>
        <?php //echo translate_phrase('Please click on the city that you currently live in:') ?>
    </h3>
    <span>
        <strong><?php echo translate_phrase('DATETIX') ?></strong><?php echo translate_phrase(' is currently only available in the following cities.');echo'<br/>' ?>
        <?php echo translate_phrase('Please click on the city that you currently live in:') ?>
    </span>
    <br/><br/>
    <ul>
        <?php foreach ($city_data as $country => $cities): ?>
        <div>
            <div id="countryDiv" style="float: left;width: 250px;">
                <span style="float: left"><img style="width: 45px;height: 30px" src="<?php echo base_url().'country_flags/hongkong.png'?>"></span>
                <div><?php echo $country ?></div>
            </div>
            <div id="cityDiv" style="margin-left: 110px">
                <ul>
                    <?php foreach ($cities as $city): ?>
                    <li><a href="<?php echo url_city_name() ?>/change-city.html?city_id=<?php echo $city->city_id ?>&return_to=<?php echo $return_to ?>"><?php echo $city->description ?></a></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
        
        <div style="clear: left"></div>
        <br/>
        <?php endforeach ?>
    </ul>
</div>

<!--<li class="country">
                <span><?php echo $country ?>:</span>
                <ul>
                    <?php foreach ($cities as $city): ?>
                        <li><a href="<?php echo url_city_name() ?>/change-city.html?city_id=<?php echo $city->city_id ?>&return_to=<?php echo $return_to ?>"><?php echo $city->description ?></a></li>
                    <?php endforeach ?>
                </ul>
            </li>-->
