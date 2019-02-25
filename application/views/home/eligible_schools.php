
<script
	src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	// Drop down js
	//When click on dropdown then his ul is open..
	$(".dropdown-dt").find('dt a').live('click',function () {
    	$(this).parent().parent().find('ul').toggle();
    });

	//When select a option..
    $(".dropdown-dt dd ul li a").live('click',function () {
        var country_id = $(this).attr('key');
        
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
    	$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val(country_id)
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));

    	load_schools_by_id(country_id)
	});

    $(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("dropdown-dt"))
        	$(".dropdown-dt dd ul").hide();
    });
	// END Dropdown
	
});
function load_schools_by_id(country_id)
{
	var base_url = '<?php echo base_url(); ?>';
	
        var url  = base_url+'home/getCountryFlagUrl';
        var data = {country_id:country_id};
        var type = 'json';
        
        jQuery.post(url,data,function(response){
            if(response.actionStatus = 'ok')
            {
                jQuery('#countryFlag').attr('src',response.flag_url);
            }
            
        },type)
        
	$.ajax({
		url:base_url+'home/load_eligible_schools',
		data:{country_id:country_id},
		type:'post',
		success:function(res){
			$("#schoolContainer").html(res);
		}
	});
}
</script>
<?php
$return = $this->session->flashdata("returnErrorData");

$first_name = (isset($return['first_name']))?$return['first_name']:set_value('first_name');
$last_name = (isset($return['last_name']))?$return['last_name']:set_value('last_name');
$email = (isset($return['email']))?$return['email']:set_value('email');

?>
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<form name="form-register" id="form-register"
				action="<?php echo base_url() ?>home/start_register" method="post"
				autocomplete="off">
				<div class="popup-box popupBigger">
					<div class="citypageHed" style="padding-top: 0px">
						<h1>
						<?php echo translate_phrase('Eligible Schools'); ?>
						</h1>
						<br /> <br />
						<p
							style="margin-bottom: 10px; font-family: 'Conv_MyriadPro-Semibold'">
							<?php echo translate_phrase('You must be an alumni or student from one of the following'); ?>
							<br />
							<?php echo translate_phrase('schools in order to join'); ?>
							<span><?php echo get_assets('name','DateTix')?></span>:
						</p>
						<div class=" drop-down-wrapper-full" style="width: 232px">
							<dl class="dropdown-dt filterdropdown">
								<dt>
								<?php $flag_url = base_url().'404.jpg';?>
								<?php if (isset($selected_country_data) && $selected_country_data): ?>
								<?php
								$flag_url = base_url().'country_flags/'.$selected_country_data['flag_url'];
								?>
									<a href="javascript:;"
										key="<?php echo $selected_country_data['country_id'] ?>"><span><?php echo $selected_country_data['description'] ?>
									</span> </a> <input type="hidden" name="country" id="contry"
										value="<?php echo $selected_country_data['country_id'] ?>">
										<?php else:?>
									<a href="javascript:;"><span><?php echo translate_phrase('No countries found.'); ?>
									</span> </a>

									<?php endif; ?>
								</dt>
								<dd>
								<?php if (isset($countries) && $countries): ?>
									<ul class="filterdropdown-ul">
									<?php foreach ($countries as $key => $country): ?>
										<li><a href="javascript:;"
											key="<?php echo $country['country_id'] ?>"><?php echo $country['description'] ?>
										</a></li>
										<?php endforeach; ?>
									</ul>
									<?php endif; ?>
								</dd>
							</dl>
						</div>
						<div class="sch-logoR">
							<img id="countryFlag" style="width: 39px; height: 32px"
								src="<?php echo $flag_url?>">
						</div>
						<!--<div class="sch-logoR"><img id="countryFlag" style="width: 39px;height: 32px" src="<?php echo base_url().'country_flags/hongkong.png'?>"></div>-->
					</div>
					<div class="cityTxt">
					<?php echo $this->session->flashdata('dispMessage'); ?>
					</div>
					<div class="two-column-school-container" id="schoolContainer">
					<?php if (isset($schools) && $schools): ?>
					<?php $previousCity = '';
					$i = 0;
					foreach($schools as $key=>$school)
					{

						if($previousCity != $school['cityName'])
						{
							echo '<div class="schoolHed">'.$school['cityName'].'</div>';
							$i=0;
						}

						echo '<div class="school-container-column ';

						if($i%2 != 0)
						echo 'Inational-Mar-L';

						echo '"><div class="school-box">';
						echo '<div class="schoolLogoHed">'.$school['school_name'].'</div>';
						echo '<div class="schoolLogo"><img height="75" width="80" alt="logo" src="'.base_url().'school_logos/'.$school['logo_url'].'"></div>';
						echo '</div>';
						echo '</div>';

						$previousCity = $school['cityName'];
						$i++;
					}
					?>
					<?php else: ?>
					<?php echo translate_phrase('No schools found.'); ?>
					<?php endif; ?>
					</div>
					<div class="Nex-mar">
						<a href="<?php echo base_url()?>" class="Next-butM"><?php echo translate_phrase('Ok') ?>
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
