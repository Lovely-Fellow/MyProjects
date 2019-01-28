<?php
$slides = Customizer_Public::get_all_customizer_images($components,true);
$multiview = !empty($customizer_settings['multiview']) ? (int) $customizer_settings['multiview'] : 1;
$slides = array_slice($slides, 0, $multiview);

list($width, $height) = Customizer_Public::getMaxWidthHeight($slides);
$colors = key(get_option('customizer_colors', array()));
$color  = (!empty($colors)) ? $colors : 'fff';
$fonts = get_option('customizer_fonts', array());
$font = (!empty($fonts)) ? $fonts[0] : 'Roboto';

$zIndex = 1;
?>
<div class="shop-filter__viewport">
    <div class="shop-filter__viewport__slider-cutter">
        <div class="shop-filter__viewport__slider" data-sf-slider>
            <?php foreach ($slides as $key => $slide): ?>
                <div class="shop-filter__viewport__slider-item" data-sf-slide="<?php echo $key; ?>">
                    <?php foreach ($components as $component): ?>
                        <?php if (isset($component['options'])): ?>
                            <?php $options = $component['options']; ?>
                            <?php if ($component['component_type'] == Customizer_Public::COMPONENT_TYPE_IMAGE) : ?>
                                <?php foreach ($options as $option): ?><?php $zIndex++; ?><?php endforeach; ?>
                                <?php continue; ?>
                            <?php endif; ?>
                            <?php $uploadImageComponent = true; ?>
                            <?php if ($component['component_type'] != Customizer_Public::COMPONENT_TYPE_CUSTOM_IMAGE) $uploadImageComponent = false; ?>
                            <?php foreach ($options as $option): ?>
                                <?php if (!isset($option['slide']) || $option['option_slide'] == $key): ?>
                                    <div
                                        class="shop-filter__viewport__<?php echo $uploadImageComponent ? 'picture' : 'text'; ?>"
                                        data-sf-render="<?php echo $option['option_id']; ?>"
                                        style="left:<?php echo $option['option_left']; ?>%;
                                                top:<?php echo $option['option_top']; ?>%;
                                                height: <?php echo $option['option_height']; ?>%;
                                                width: <?php echo $option['option_width']; ?>%;
                                                transform: rotate(<?php echo $option['option_transform']; ?>deg);
                                                color: #<?php echo $color; ?>;
	                                            font-family: <?php echo $font?>;
	                                            z-index: <?php echo $zIndex++; ?>">
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <div data-sf-render="optionImg">
	                    <?php
	                    list($widthSlide, $heightSlide) = Customizer_Public::getMaxWidthHeight($slide);
	                    $styleSlide = '';
	                    if ($widthSlide):
		                    $styleSlide .= ' width:' . $widthSlide . 'px;';
	                    endif;
	                    if ($heightSlide):
		                    $styleSlide .= ' height:' . $heightSlide . 'px;';
	                    endif;
	                    if (empty($slide[0])):
		                    $styleSlide .= ' display:none;';
	                    endif;
	                    ?>
                        <img src="<?php echo Customizer_Public::get_image_full_url($slide); ?>" class="shop-filter__viewport__option-img default_image"
                                 style="z-index:0; <?php echo $styleSlide?>"/>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
	    <?php if ($multiview > 1): ?>
		    <?php require_once (__DIR__ . '/../form/slide_buttons.php');?>
	    <?php endif; ?>
    </div>
</div>