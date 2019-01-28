<?php
$noImagePath      = plugins_url('images/no_image.jpg', __DIR__);
?>
<div class="shop-filter__components__col">
    <div class="shop-filter shop-filter__scroller" data-sf-group="components">
        <div class="shop-filter__components__carousel-prev" onClick="shopFilter.carousel.prev()"></div>
        <div class="shop-filter__components__carousel-next" onClick="shopFilter.carousel.next()"></div>
        <div class="shop-filter__components__carousel__cutter">
            <div class="shop-filter__components__carousel__scroller">
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
                                     data-sf-data='{"placeholderText" : "<?php echo __('PLEASE SELECT', 'customizer'); ?> (0)","placeholderIcon" : "<?php echo $noImagePath; ?>","counterText": "<?php echo __('Total', 'customizer'); ?>: "}'>

                                    <div class="shop-filter__component__icon">
                                        <?php if ($component['component_icon']): ?>
                                            <img src="<?php echo Customizer_Public::get_image_full_url($component['component_icon']) ?>"
                                                 alt="<?php echo esc_html($component['component_name']); ?>">
                                        <?php else: ?>
                                            <div class="customizer_component_noimage">
                                                <div class="customizer_component_noimage_icon"><i class="far fa-image"></i></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="shop-filter__options__col">
    <?php $i = 0; ?>
    <?php if (!empty($components)): ?>
        <?php foreach ($components as $componentId => $component): ?>
            <?php if ($component['component_enable'] == 1): ?>
                <?php $description = !empty($component['component_description']) ?  wp_specialchars_decode($component['component_description']) : '';?>
                <div class="shop-filter__options-wrapper"
                    <?php echo(!empty($component['multiple']) ? 'data-sf-multiple' : ''); ?>
                    <?php echo(!empty($component['component_radio']) ? 'data-sf-radio' : ''); ?>
                     data-sf-group="options"
                     data-sf-component-id="<?php echo $component['component_id'] ?>">
                    <div class="shop-filter__options__title"><?php echo $component['component_name']; ?></div>
	                <?php if (!empty($description)): ?>
                        <div class="shop-filter__options__desc">
                            <?php
                                $position = stripos($description, Customizer_Admin::TAG_READ_MORE);
                                if ($position === false): ?>
                                    <?php echo $description?>
                                <?php else: ?>
                                    <?php
                                        $textDescr = substr($description,0,$position) . ' <a href="#popup_'.$componentId.'" class="shop-filter__popup-html">'.__('Read more','customizer').'</a>';
                                        echo $textDescr;
                                    ?>
                                    <div id="popup_<?php echo $componentId?>" class="shop-filter__popup mfp-hide">
                                        <?php echo htmlspecialchars_decode(substr($description,($position+strlen(Customizer_Admin::TAG_READ_MORE)))); ?>
                                    </div>
                                <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php $groups = []; $options = []; ?>
                    <?php if (isset($component['groups'])):
                        $groups = $component['groups'];
	                endif;
	                if (isset($component['options'])):
                        $options = $component['options'];
	                endif;
	                $component_radio_button = !empty($component['component_radio']);
	                if (!empty($groups) || !empty($options)):
                        require 'options/' . $component['component_type'] . '.php';
	                endif;?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
