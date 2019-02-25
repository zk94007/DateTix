<?php
$venue_cnt = 1;
$venue_id = 1;
?>
<?php if($recommonded_venue):?>
<?php $first_vanue = $recommonded_venue['0'];?>
<div class="full-width">
	<div class="sortbyTxt bold-txt">
	<?php echo translate_phrase('Option ')?>
		<span class="Opt-Counter"><?php echo $venue_cnt ++?> </span>:
		<?php echo translate_phrase('Select from recommended venues')?>
		:
	</div>
	<div class="f-decrMAIN padB-none">
		<div class="f-decr">
		<?php foreach ($recommonded_venue as $key => $value):?>
			<div>
				<a href="javascript:;" class="venue_rdo_div"
					key="<?php echo $value['venue_id'];?>"> <span
					<?php if($value['venue_id'] == $venue_id)
					{
						echo 'class="appr-cen"';
					}
					else
					{
						if($key == 0)
						echo 'class="appr-cen"';
						else
						echo 'class="disable-butn"';
					}?>><?php echo $value['name'];?> </span> </a>

				<div class="hidden">
					<div class="dateRow">
					<?php echo $value['name']?>
					</div>
					<div class="locationArea Mar-top-none">
						<p>
						<?php echo $value['address'];?>
						<?php if($value['venue_id']):?>
							&nbsp; &nbsp;<a href="javascript:;" onclick="openNewWindow(this)"
								data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=suggest-date-idea.html&venue='.$this->utility->encode($value['venue_id']);?>">
								<?php echo translate_phrase('View Map')?> </a>
								<?php endif;?>
						</p>
						<p>
						<?php if($value['venue_dates']){ echo implode(' / ', $value['venue_dates']);}?>
						</p>
						<p>
						<?php echo $value['phone_number'];?>
						</p>
						<p>
							<a href="javascript:;" onclick="openNewWindow(this)"
								data-url="<?php echo $value['review_url'];?>"><?php echo $value['review_url'];?>
							</a>
						</p>
					</div>
				</div>
			</div>
			<?php endforeach;?>
			<input type="hidden" name="venue_id" id="venue_id"
				value="<?php echo $first_vanue['venue_id'];?>" />
		</div>
	</div>
	<div class="datesArea bor-none Mar-top-none" id="date_details">
		<div class="dateRow">
		<?php echo $first_vanue['name']?>
		</div>
		<div class="locationArea Mar-top-none">
			<p>
			<?php echo $first_vanue['address'];?>
				&nbsp; &nbsp;<a href="javascript:;" onclick="openNewWindow(this)"
					data-url="<?php echo base_url().url_city_name().'/view-map.html?return_to=suggest-date-idea.html&venue='.$this->utility->encode($value['venue_id']);?>">
					<?php echo translate_phrase('View Map')?> </a>
			</p>
			<p>
			<?php if($first_vanue['venue_dates']){ echo implode(' / ', $first_vanue['venue_dates']);}?>
			</p>
			<p>
			<?php echo $first_vanue['phone_number'];?>
			</p>
			<p>
				<a href="javascript:;" onclick="openNewWindow(this)"
					data-url="<?php echo $first_vanue['review_url'];?>"><?php echo $first_vanue['review_url'];?>
				</a>
			</p>
		</div>
	</div>
</div>
			<?php endif;?>