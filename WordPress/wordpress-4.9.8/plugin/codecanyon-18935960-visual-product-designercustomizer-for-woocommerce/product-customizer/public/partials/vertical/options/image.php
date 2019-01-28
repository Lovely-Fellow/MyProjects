<?php foreach ($group as $optionKey => $option): ?>
    <?php if ($option['option_enable'] == 1): ?>
        <?php $floatPrice = (!empty($product)) ? wc_get_price_to_display($product, ['price' => (float)$option['option_price']]) : (float)$option['option_price'] ?>
        <?php $price = strip_tags(Customizer_Public::customizer_wc_price($option['option_price'])); ?>
        <?php $description = (empty($option['option_description']) ? esc_html($option['option_name']) . (($floatPrice && Customizer_Public::is_woocommerce_enabled()) ? ' (' . $price . ')' : '') : esc_html($option['option_description'])); ?>
        <?php $title = ($floatPrice && Customizer_Public::is_woocommerce_enabled()) ? esc_html($option['option_name']) . ' (' . $price . ')' : esc_html($option['option_name']); ?>
        <?php $src_image = []; ?>
        <?php if (is_array($option['option_image'])):
            foreach ($option['option_image'] as $key => $option_image):
                $src_image[$key] = Customizer_Public::get_image_full_url($option_image);
            endforeach;
        else:
            $src_image[] = Customizer_Public::get_image_full_url($option['option_image']);
        endif; ?>

        <?php if ($component_radio_button): ?>
			<?php if ($component['multiple']): $group_name = $component['component_id'] . '_' . sanitize_title($option['group_name']); else: $group_name =$component['component_id']; endif; ?>
            <div class="shop-filter__radio shop-filter__option <?php echo !empty($option['option_default']) ? 'is-default' : ''; ?>"
                 data-sf-elem="option"
                 data-sf-group="<?php echo $group_name; ?>"
                 data-sf-data='{
                    "type" : "<?php echo Customizer_Public::COMPONENT_TYPE_IMAGE; ?>",
                    "option_id": "<?php echo $option['option_id']; ?>",
                    "src" : <?php echo json_encode($src_image); ?>,
                    "icon"  : "<?php echo Customizer_Public::get_image_full_url($option['option_icon']); ?>",
                    "price" : "<?php echo $floatPrice; ?>",
                    "title" : "<?php echo $title; ?>",
                    "zIndex" : <?php echo $i++; ?>}'
                data-option_id="<?php echo $option['option_id']; ?>"
               >
                <label class="shop-filter__radio__label">
                    <input type="radio" name="<?php echo $group_name; ?>" <?php echo !empty($option['option_default']) ? 'checked' : ''; ?>>
                    <?php $text_radio = esc_html($option['option_name']); ?>
                    <?php if ($option['option_price'] > 0): $text_radio .= ' + '. $price; endif; ?>
                    <span class="shop-filter__radio__text"><?php echo $text_radio; ?></span>
                </label>
            </div>
        <?php else: ?>
            <div
                    class="shop-filter__option tooltip-holder <?php echo !empty($option['option_default']) ? 'is-default' : ''; ?>"
                    data-sf-elem="option"
                    data-sf-data='{
                        "type" : "<?php echo Customizer_Public::COMPONENT_TYPE_IMAGE; ?>",
                        "option_id": "<?php echo $option['option_id']; ?>",
                        "src" : <?php echo json_encode($src_image); ?>,
                        "icon"  : "<?php echo Customizer_Public::get_image_full_url($option['option_icon']); ?>",
                        "price" : "<?php echo $floatPrice; ?>",
                        "title" : "<?php echo $title; ?>",
                        "zIndex" : <?php echo $i++; ?>}'
                    data-option_id="<?php echo $option['option_id']; ?>">
	            <?php $icon = Customizer_Public::get_image_full_url($option['option_icon']);
	            if (!empty($icon)): ?>
                    <img src="<?php echo $icon; ?>"
                         alt="<?php echo esc_html($option['option_name'])?>"
                         class="shop-filter__option__icon">
	            <?php else: ?>
		            <?php $color = !empty($option['option_icon_background']) ? $option['option_icon_background'] : '000';
		            $text = !empty($option['option_icon_text']) ? $option['option_icon_text'] : '';
		            ?>
                    <div class="shop-filter__option__icon" style="background-color: #<?php echo $color;?>">
			            <?php echo $text; ?>
                    </div>
	            <?php endif; ?>
                <div class="tooltip-text"
                     data-tooltip="<?php echo $description; ?>"></div>
            </div>
            <div class="customizer-preload-image">
                <img src="<?php echo esc_html($src_image[0]); ?>"
                     alt="<?php echo esc_html($option['option_name']); ?>"/>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>
