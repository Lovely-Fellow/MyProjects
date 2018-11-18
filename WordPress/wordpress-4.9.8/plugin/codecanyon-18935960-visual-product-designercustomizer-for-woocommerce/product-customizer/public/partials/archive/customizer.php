<?php
/**
 * The template for displaying archive customizers.
 *
 *
 * @author            K2-Service <plugins@k2-service.com>
 * @link              http://k2-service.com/shop/product-customizer/
 */
get_header(); ?>
<?php if (have_posts()) : ?>

    <div class="container breadcrumb-wrapper">
        <?php //TODO you can add breadcrumb here ?>
        <?php if (function_exists('mp_emmet_the_breadcrumb')) {
            mp_emmet_the_breadcrumb();
        } ?>
    </div>

    <div class="container container-customizer">
        <header class="woocommerce-products-header">
            <h1 class="woocommerce-products-header__title page-title"><?php echo __('Customizers', 'customizer'); ?></h1>
        </header>
    </div>
    <div class="container main-container">
        <div class="row-customizer clearfix">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <ul class="products columns-3">
                    <?php
                    do_action('storefront_loop_before');
                    global $indexCustomizer;
                    $indexCustomizer = 0;
                    while (have_posts()) : the_post();
                        load_template(plugin_dir_path(__FILE__) . 'content-customizer.php', false);
                    endwhile;
                    do_action('storefront_loop_after'); ?>
                </ul>
            </div>
        </div>
    </div>

<?php else :
    get_template_part('content', 'none');
endif; ?>
<?php
get_footer();
