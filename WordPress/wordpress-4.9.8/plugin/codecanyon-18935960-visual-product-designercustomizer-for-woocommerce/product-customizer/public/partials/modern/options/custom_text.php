<?php foreach ($options as $optionKey => $option): ?>
    <?php if ($option['option_enable'] == 1): ?>
        <?php $floatPrice = (!empty($product)) ? wc_get_price_to_display($product, ['price' => (float)$option['option_price']]) : (float)$option['option_price'] ?>
        <?php $price = strip_tags(Customizer_Public::customizer_wc_price($option['option_price'])); ?>
        <?php $description = (empty($option['option_description']) ? esc_html($option['option_name']) : esc_html($option['option_description'])); ?>
        <?php $description .= $floatPrice ? ' (' . $price . ')' : ''; ?>
        <?php $colors = get_option('customizer_colors', array()); ?>
        <?php $fonts = get_option('customizer_fonts', array()); ?>
        <?php if (empty($fonts)) {$fonts['Roboto'] = 'Roboto';} ?>
        <?php $title = ($floatPrice && Customizer_Public::is_woocommerce_enabled()) ? esc_html($option['option_name']) . ' (' . $price . ')' : esc_html($option['option_name']); ?>

        <div class="shop-filter__option tooltip-holder <?php echo ($option['option_default']) ? 'is-default' : ''; ?>"
             id="<?php echo $option['option_id']; ?>"
             data-sf-elem="option"
             data-sf-data='{
                "type" : "<?php echo Customizer_Public::COMPONENT_TYPE_CUSTOM_TEXT; ?>",
                "option_id": "<?php echo $option['option_id']; ?>",
                "price" : "<?php echo $floatPrice; ?>",
                "title"    : "<?php echo $title; ?>",
                "left"     : "<?php echo $option['option_left']; ?>",
                "top"      : "<?php echo $option['option_top']; ?>",
                "transform": "<?php echo $option['option_transform']; ?>",
                "width"    : "<?php echo $option['option_width']; ?>",
                "height"   : "<?php echo $option['option_height']; ?>",
                "max_length" : "<?php echo  isset($option['option_max_length']) ? $option['option_max_length'] : '0'; ?>",
                "font_size"  : "<?php echo isset($option['option_font_size']) ? $option['option_font_size'] : '0'; ?>",
                "slide"    : "<?php echo !empty($option['option_slide']) ? $option['option_slide'] : 0; ?>",
                "zIndex"   : <?php echo $i++; ?>
                }'>
            <div class="shop-filter__option__title">
                <?php echo $title; ?>
            </div>
            <div class="shop-filter__option__color-wrapper">
                <div class="shop-filter__form-field-wrapper">
                    <input type="text" class="shop-filter__form-field shop-filter_custom_field"
                           placeholder="Enter Your Text"
                           onkeyup="shopFilter.updateText('<?php echo $option['option_id']; ?>', this.value)"
	                    <?php if (!empty($option['option_max_length'])): ?>
                            maxlength="<?php echo $option['option_max_length']?>"
	                    <?php endif;?>
                    >
                </div>
                <div class="shop-filter__form-field-wrapper">
                    <div class="shop-filter__form-select-wrapper">
                        <select class="shop-filter__form-field font_<?php echo $option['option_id']; ?>"
                                onChange="shopFilter.updateTextFont('<?php echo $option['option_id']; ?>', this.value)">
                            <?php foreach ($fonts as $font): ?>
                                <option value="<?php echo $font ?>"> <?php echo $font ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php reset($colors);
                $firstColor = key($colors); ?>
                <div class="shop-filter__option__color color_<?php echo $option['option_id']; ?>" data-sf-elem="color"
                     style="background-color: #<?php echo $firstColor; ?>"
                     data-option_id="<?php echo $option['option_id']; ?>" data-color="<?php echo $firstColor; ?>">
                    <div class="shop-filter__option__color-dropdown">
                        <?php foreach ($colors as $ckey => $cval): ?>
                            <div class="shop-filter__option__color-elem tooltip-holder"
                                 title="<?php echo $cval; ?>"
                                 onClick="shopFilter.updateTextColor(this, '<?php echo $option['option_id']; ?>', '#<?php echo $ckey; ?>')"
                                 style="background-color: #<?php echo $ckey; ?>;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
