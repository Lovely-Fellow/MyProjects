<?php
$formatted  = null;
$plugin_url = plugin_dir_url(__FILE__);
if (!empty($_GET['hash'])):
    $formatted        = Customizer_Public::prepare_customizer_print($_GET['hash'], 600);
    $saved_customizer = Customizer_Public::get_saved_customizer($_GET['hash']);
    Customizer_Public::increase_view_count($_GET['hash']);
endif;

if (empty($saved_customizer) || empty($_GET['hash'])) {
    wp_redirect('/customizer/');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="DEMO SITE">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <?php Customizer_Public::add_meta_tags(); ?>
    <title><?php echo $saved_customizer->name_customizer; ?> Print Page</title>
    <link rel="stylesheet" href="<?php echo $plugin_url; ?>../../css/customizer.css" type="text/css" media="all"/>
    <link rel="stylesheet" href="<?php echo $plugin_url; ?>../../css/grid.min.css" type="text/css" media="all"/>
    <script src="https://use.fontawesome.com/2a2c40c58e.js"></script>
    <style type="text/css">
        @media print {  .no-print, .no-print * {display: none !important;}  }
        .qr_code {text-align: center;padding-top: 50px;}
        body {font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;}
        ul {list-style: none;}
        ul li {padding: 7px 0px;}
        ul li img {margin-bottom: -7px;}
        ul.options li img {max-height: 24px;}
        ul.components li img {max-height: 32px;}
        ul.components li.option {border-bottom: 1px dotted #000;}
        .clearfix {clear: both;}
        .copyright {text-align: right;padding: 10px 20px;}
    </style>
</head>
<body>
<div class="container-customizer">
    <h1 align="center"><?php echo $saved_customizer->name_customizer; ?></h1>
    <div align="right">
        <a href="javascript:window.print()" class="no-print" title="<?php echo __('Print', 'customizer'); ?>"
           style="font-size: 16px;color:#000">
            <i class="fa fa-print" aria-hidden="true"></i>
        </a>
    </div>
    <div class="row-customizer">
        <div class="col-sm-4">
            <ul class="components">
                <?php foreach ($formatted['customizer'] as $components):
                    $name_component = $components[0]['component_name'];
                    $icon_component = $components[0]['component_icon'];
                    ?>
                    <li class="option">
                        <img class="cart_option_icon" src="<?php echo Customizer_Public::get_image_full_url($icon_component) ?>"/>
                        <?php echo $name_component ?>
                        <ul class="options">
                            <?php foreach ($components as $option): ?>
                                <div class="option">
                                    <li>
                                        <?php echo $option['option_name'] . ' ' . (!empty($option['option_icon']) ? '<img class="cart_option_icon" src="' . Customizer_Public::get_image_full_url($option['option_icon']) . '" />' : '');
                                        switch ($option['component_type']):
                                            case Customizer_Public::COMPONENT_TYPE_CUSTOM_TEXT :
                                                echo '<br />- ' . __('Font', 'customizer') . ': <span style="font-family:#' . $option['custom_font'] . ';">' . $option['custom_font'] . '</span>';
                                                echo '<br />- ' . __('Color', 'customizer') . ': <span style="color:' . Customizer_Public::rgbToHex($option['custom_color']) . ';">' . Customizer_Public::rgbToHex($option['custom_color']) . '</span>';
                                                echo '<br />- ' . __('Text', 'customizer') . ': <strong>' . $option['custom_text'] . '</strong>';
                                                break;
                                            case Customizer_Public::COMPONENT_TYPE_CUSTOM_IMAGE :
                                                if (!empty($option['custom_image']))
                                                    echo '<br /><a target="_blank" href="' . WP_CONTENT_URL . '/uploads/customizer/custom_images/' . $option['custom_image'] . '"><image style="display: inline; max-height: 24px" src="' . $option['custom_image'] . '" /></a>';
                                                break;
                                        endswitch; ?>
                                    </li>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-sm-7" align="center">
            <?php if (!empty($formatted['image'])): ?>
            <?php $maxHeight = (count($formatted['image'])>1)?300:500;?>
                <?php foreach ($formatted['image'] as $slide_image): ?>
                    <img src="<?php echo $slide_image; ?>" style="max-height: <?php echo $maxHeight;?>px">
                <?php endforeach;?>
                <div class="qr_code">
                    <?php $link = Customizer_Public::get_customizer_link($saved_customizer->customizer_id, $saved_customizer->hash); ?>
                    <img width="200" src="https://qbar.k2-service.com/qr/qr.png?text=<?php echo urlencode($link); ?>"/>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="copyright"><?php echo __('Powered by', 'customizer'); ?> <a
            href="<?php echo get_home_url(); ?>"><?php echo get_bloginfo('name'); ?></a>
</div>
</body>
</html>
