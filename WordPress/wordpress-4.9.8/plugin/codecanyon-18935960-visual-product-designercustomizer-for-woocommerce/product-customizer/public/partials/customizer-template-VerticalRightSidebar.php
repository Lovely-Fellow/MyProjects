<?php

/**
 * Product Customizer Template (LeftSidebar)
 *
 * @link              http://k2-service.com/shop/product-customizer/
 * @author            K2-Service <plugins@k2-service.com>
 *
 * @package           Customizer
 * @subpackage        Customizer/public/partials
 */

global $customizer_widget;
$components          = Customizer_Public::get_customizer_meta();
$components          = $components['customizer'];
$customizer_settings = Customizer_Public::get_customizer_settings();
$product             = Customizer_Public::is_woocommerce_enabled() ? wc_get_product($customizer_settings['product_id']) : '';

$defaultPrice           = Customizer_Public::calculate_default_price(false);
$defaultPriceWithFormat = Customizer_Public::customizer_wc_price($defaultPrice);
?>
    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php if (!$customizer_widget) {
    get_header();
} ?>
    <div class="container-customizer">
        <?php require_once('form/print_link.php'); ?>
        <div class="row-customizer">
            <div class="col-sm-8 pic-wrapper">
                <?php require_once('vertical/image.php'); ?>
            </div>
            <div class="col-sm-3">
                <?php require_once('vertical/options.php'); ?>

                <div class="row-customizer single-product">
                    <div class="col-sm-12 product summary">
                        <?php require_once('form/add_to_cart.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="footer sticky-stop"></div>

    <script type="text/javascript">
        var DEFAULT_PRODUCT_PRICE = '<?php echo $defaultPrice; ?>';
        window.shopFilterRules = <?php echo Customizer_Public::get_formatted_rules();?>;
    </script>
<?php if (!$customizer_widget) {
    get_footer();
} ?>