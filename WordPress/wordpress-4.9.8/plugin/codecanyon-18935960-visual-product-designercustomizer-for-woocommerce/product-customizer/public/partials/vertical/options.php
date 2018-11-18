<?php
$noImagePath      = plugins_url('images/no_image.jpg', dirname(__FILE__));
?>
<div class="shop-filter shop-filter__scroller" data-sf-group="components">
    <?php $i = 0; ?>
    <?php if (!empty($components)): ?>
        <?php foreach ($components as $componentId => $component): ?>
            <?php if ($component['component_enable'] == 1): ?>
                <div class="shop-filter__item" <?php
                if ($component['component_type'] != Customizer_Public::COMPONENT_TYPE_IMAGE):
                    echo ($component['component_type'] == Customizer_Public::COMPONENT_TYPE_CUSTOM_TEXT) ? 'data-shop-filter-item="text"' : 'data-shop-filter-item="picture"';
                endif ?>>
                    <div class="shop-filter__component"
                         data-sf-elem="component"
                         data-sf-component-id="<?php echo $component['component_id'] ?>"
                         data-sf-onload
                         data-sf-data='{"placeholderText" : "<?php echo __('PLEASE SELECT', 'customizer'); ?> (0)","placeholderIcon" : "<?php echo $noImagePath; ?>","counterText": "<?php echo __('Total', 'customizer'); ?>: "}'>

                        <div class="shop-filter__component__icon">
                            <?php if ($component['component_icon']): ?>
                                <img src="<?php echo Customizer_Public::get_image_full_url($component['component_icon']) ?>"
                                     alt="<?php echo esc_html($component['component_name']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="shop-filter__component__title"><?php echo esc_html($component['component_name']); ?></div>

                        <?php if ($component['component_type'] == Customizer_Public::COMPONENT_TYPE_IMAGE): ?>
                            <div class="shop-filter__component__subtitle" data-sf-render="component-subtitle">
                                <?php echo __('PLEASE SELECT', 'customizer'); ?> (<span
                                        class="customizer_price_symbol"></span> 0)
                            </div>
                        <?php endif; ?>
                        <div class="shop-filter__component__counter"
                             data-sf-render="component-counter"></div>
                        <img src="<?php echo $noImagePath; ?>" alt=""
                             class="shop-filter__component__icon-selected" data-sf-render="component-icon">
                    </div>
                    <div
                            class="shop-filter__options-wrapper shop-filter__scroller" <?php echo(!empty($component['multiple']) ? 'data-sf-multiple' : ''); ?>
                            data-sf-group="options"
    	                    <?php echo (!empty($component['component_radio']) ? 'data-sf-radio' : ''); ?>
                            data-sf-component-id="<?php echo $component['component_id'] ?>">
                        <div class="shop-filter__options__table">
                            <?php foreach ($component['groups'] as $group_name => $group): ?>
                                <?php if ($group_name): ?>
                                    <div class="shop-filter__options__table-row">
                                    <div class="shop-filter__options__table-cell">
                                        <div class="shop-filter__options__table__label">
                                            <?php echo $group_name; ?>
                                        </div>
                                    </div>
                                    <div class="shop-filter__options__table-cell">
                                <?php endif; ?>
                                <div class="shop-filter__options-list">
                                    <?php $component_radio_button = !empty($component['component_radio']); ?>
                                    <?php require 'options/' . $component['component_type'] . '.php'; ?>
                                </div>
                                <?php if ($group_name): ?>
                                    </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
