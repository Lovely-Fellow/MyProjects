<?php

/**
 * Product Customizer Template (ModernDark)
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

<?php if (!$customizer_widget) {
	get_header();
} ?>
	<div class="container-customizer">
		<?php require_once('form/print_link.php'); ?>
		<div class="row-customizer">
			<div class="pic-wrapper shop-filter__viewport__col">
				<?php require_once('modern/image.php'); ?>
			</div>
			<div class="shop-filter__components-panel">
				<?php require_once('modern/options.php'); ?>
                <div class="product summary">
					<?php require_once('form/add_to_cart.php'); ?>
                </div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="footer sticky-stop"></div>

	<script type="text/javascript">
        var DEFAULT_PRODUCT_PRICE = '<?php echo $defaultPrice; ?>';
        window.shopFilterRules = <?php echo Customizer_Public::get_formatted_rules();?>;
        jQuery(".component_description_full").click(function () {
            jQuery(this).parent().hide();
            jQuery(this).closest(".shop-filter__options__desc").find("div[name='full_component_description']").show();
        });
        jQuery(".component_description_short").click(function () {
            jQuery(this).parent().hide();
            jQuery(this).closest(".shop-filter__options__desc").find("div[name='short_component_description']").show();
        })
        jQuery(function() {
            jQuery(".shop-filter__component").filter(':first').trigger('click');
        });
	</script>
<?php if (!$customizer_widget) {
	get_footer();
} ?>