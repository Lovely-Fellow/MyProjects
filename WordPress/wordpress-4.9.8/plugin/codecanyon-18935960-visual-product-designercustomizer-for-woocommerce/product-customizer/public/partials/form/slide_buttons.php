<?php
$labels = $customizer_settings['slide_labels'];
$labels = explode(',',$labels);
?>
<div class="shop-filter__viewport__slider-buttons">
	<?php foreach ($slides as $key => $slide): ?>
		<div class="shop-filter__viewport__slider-button <?php echo ($key===0) ? 'active' : ''?>" onClick="shopFilter.slideViewportPicture(this, <?php echo $key?>)">
            <?php echo !empty($labels[$key])?$labels[$key]:($key+1) ?>
        </div>
	<?php endforeach;?>
</div>