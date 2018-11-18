<?php

/**
 * Product Customizer Template (LeftSidebar)
 *
 * @link              http://k2-service.com/shop/product-customizer/
 * @author            K2-Service <plugins@k2-service.com>
 *
 * @package           Customizer
 * @subpackage        Customizer/public/зфкешфды
 */
global $customizer_widget;

$components             = Customizer_Public::get_customizer_meta();
$components             = $components['customizer'];
$customizer_settings    = Customizer_Public::get_customizer_settings();
$product                = Customizer_Public::is_woocommerce_enabled() ? wc_get_product($customizer_settings['product_id']) : '';
$defaultPrice           = Customizer_Public::calculate_default_price(false);
$defaultPriceWithFormat = Customizer_Public::customizer_wc_price($defaultPrice);
$noImagePath            = plugins_url('images/no_image.jpg', dirname(__FILE__));
?>

<?php if (!$customizer_widget) {
    get_header();
} ?>
<div class="container-customizer">
    <?php require_once('form/print_link.php'); ?>
    <div class="row-customizer">
        <div class="col-sm-4">
            <div class="shop-filter__components-wrapper">
                <div class="shop-filter__component-full">
                    <div class="sf_icon-angle-left theme-black" onClick="shopFilter.pickPrevComponent()"></div>
                    <div class="sf_icon-angle-right theme-black" onClick="shopFilter.pickNextComponent()"></div>
                    <div data-sf-render="component"></div>
                </div>
            </div>
            <?php require_once('horizontal/options.php'); ?>

            <br/>
            <div class="row-customizer single-product">
                <div class="col-sm-8 col-sm-offset-1 product pull-right summary">
                    <?php require_once('form/add_to_cart.php'); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <?php require_once('horizontal/component_line.php'); ?>
            <?php require_once('horizontal/image.php'); ?>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
    var DEFAULT_PRODUCT_PRICE = '<?php echo (float)$defaultPrice; ?>';
    window.shopFilterRules = <?php echo Customizer_Public::get_formatted_rules();?>;
</script>
<?php if (!$customizer_widget) {
    get_footer();
} ?>

