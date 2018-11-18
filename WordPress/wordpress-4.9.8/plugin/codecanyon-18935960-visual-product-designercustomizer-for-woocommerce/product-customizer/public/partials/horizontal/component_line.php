<div class="shop-filter__components-wrapper">
    <div class="shop-filter__components-list" style="text-indent: 0px;">
        <?php $i = 0; ?>
        <?php foreach ($components as $component): ?>
            <?php if ($component['component_enable'] == 1): ?>

                <div class="shop-filter__component" data-sf-elem="component"
                    <?php if ($i == 0): ?>
                        data-sf-onload onLoad="shopFilter.pickComponent(this)"
                    <?php endif; ?>
                     data-sf-component-id="<?php echo $component['component_id'] ?>">
                    <div class="shop-filter__component__icon">
                        <?php if (!empty($component['component_icon'])): ?>
                            <img src="<?php echo Customizer_Public::get_image_full_url($component['component_icon']) ?>"
                                 alt="<?php echo esc_html($component['component_name']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="shop-filter__component__title"><?php echo $component['component_name']; ?></div>
                </div>
                <?php $i++; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
