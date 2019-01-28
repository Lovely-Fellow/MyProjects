<?php
$saved_customizer = null;
if (!empty($_GET['hash'])) {
    $saved_customizer = Customizer_Public::get_saved_customizer($_GET['hash']);
}
if (empty($saved_customizer)): ?>
    <h1 class="text-center"><?php echo the_title(); ?></h1>
    <?php return; ?>
<?php endif;

$print_link = Customizer_Public::get_customizer_link($saved_customizer->customizer_id, $saved_customizer->hash);
$print_link .= '&print=1';

$getGeneratedImage = Customizer_Public::generateCustomerImage($_GET['hash']);
?>
<h1 class="text-center"><?php echo $saved_customizer->name_customizer; ?></h1>
<h5 class="text-center"><?php echo __('Created from', 'customizer'); ?> <a target="_blank"
                                                                           href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
</h5>
<?php if (!empty($getGeneratedImage)): ?>
    <div style="display:none;">
        <img title="<?php esc_html($saved_customizer->name_customizer); ?>" src="<?php echo $getGeneratedImage[0]; ?>"/>
    </div>
<?php endif; ?>
<div class="print-link-default">
    <a href="<?php echo $print_link; ?>" <?php echo __('Print', 'customizer'); ?> target="_blank">
        <i class="fa fa-print" aria-hidden="true"></i>
    </a>
</div>