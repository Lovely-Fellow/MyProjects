<?php foreach ($group as $optionKey => $option): ?>
    <?php if ($option['option_enable'] == 1): ?>
        <?php $floatPrice = (!empty($product)) ? wc_get_price_to_display($product, ['price' => (float)$option['option_price']]) : (float)$option['option_price'] ?>
        <?php $price = strip_tags(Customizer_Public::customizer_wc_price($option['option_price'])); ?>
        <?php $description = (empty($option['option_description']) ? esc_html($option['option_name']) : esc_html($option['option_description'])); ?>
        <?php $description .= $floatPrice ? ' (' . $price . ')' : ''; ?>
        <?php $title = ($floatPrice && Customizer_Public::is_woocommerce_enabled()) ? esc_html($option['option_name']) . ' (' . $price . ')' : esc_html($option['option_name']); ?>

        <div
                class="shop-filter__option shop-filter__option--block <?php echo ($option['option_default']) ? 'is-default' : ''; ?>"
                id="<?php echo $option['option_id']; ?>"
                data-sf-elem="option"
                data-sf-data='{
                "type"     : "<?php echo Customizer_Public::COMPONENT_TYPE_CUSTOM_IMAGE; ?>",
                "option_id": "<?php echo $option['option_id']; ?>",
                "price"    : <?php echo $floatPrice; ?>,
                "title"    : "<?php echo $title; ?>",
                "left"     : "<?php echo $option['option_left']; ?>",
                "top"      : "<?php echo $option['option_top']; ?>",
                "transform": "<?php echo $option['option_transform']; ?>",
                "width"    : "<?php echo $option['option_width']; ?>",
                "height"   : "<?php echo $option['option_height']; ?>",
                "slide"    : "<?php echo !empty($option['option_slide']) ? $option['option_slide'] : 0; ?>",
                "zIndex"   : <?php echo $i++; ?>
                }'>
            <div class="shop-filter__option__title">
                <?php echo $title; ?>
            </div>
            <div class="shop-filter__form-field-wrapper shop-filter__form-file-uploader">
                <input type="text" class="shop-filter__form-field shop-filter_custom_field"/>
                <div class="shop-filter__form-btn shop-filter__form-btn--upload">
                    <span><?php echo __('Upload', 'customizer'); ?></span>
                    <input type="file"
                           onChange="shopFilter.updatePicture(event, '<?php echo $option['option_id']; ?>')">
                </div>
                <div class="shop-filter__form-btn shop-filter__form-btn--reset"
                     onclick="shopFilter.removePicture(event, '<?php echo $option['option_id']; ?>');">×
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
