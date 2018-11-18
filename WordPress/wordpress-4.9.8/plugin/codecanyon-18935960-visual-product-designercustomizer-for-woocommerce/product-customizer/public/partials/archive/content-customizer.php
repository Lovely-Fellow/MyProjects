<?php
/**
 * The template for displaying customizer content within loops
 * @author            K2-Service <plugins@k2-service.com>
 * @link              http://k2-service.com/shop/product-customizer/
 */
global $indexCustomizer;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$data   = Customizer_Public::get_customizer_meta();
$images = Customizer_Public::get_all_customizer_images($data['customizer'], true, true);
$price  = Customizer_Public::calculate_default_price();

$additionalClass = ' col-xs-6 col-sm-6 col-md-4 col-lg-4';
if ($indexCustomizer == 0 || ($indexCustomizer % 3) == 0) {
    $additionalClass .= ' first';
}
if ((($indexCustomizer + 1) % 3) == 0) {
    $additionalClass .= ' last';
}

?>
    <li <?php post_class('product' . $additionalClass); ?>>
        <a href="<?php the_permalink(); ?>" class="woocommerce-LoopProduct-link">
            <div class="customizer-thumbnail customizer-thumbnail-list">
                <?php if (!empty($images[0])):?>
                    <?php foreach ($images[0] as $_image): ?>
                        <?php if ($_image): ?>
                            <img src="<?php echo $_image; ?>" alt="<?php echo esc_html(get_the_title()); ?>"
                                 class="customizer-thumbnail-image""/>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif;?>
            </div>
            <h2 class="customizer-title-link"><?php the_title(); ?></h2>
            <?php if ($price): ?>
                <?php echo Customizer_Public::customizer_wc_price($price); ?>
            <?php endif; ?>
        </a>
    </li>

<?php $indexCustomizer++; ?>