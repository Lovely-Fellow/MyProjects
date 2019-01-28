<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              http://k2-service.com/shop/product-customizer/
 * @package           Customizer
 *
 * @wordpress-plugin
 * Plugin Name:       Product Customizer
 * Plugin URI:        http://k2-service.com/shop/product-customizer/
 * Description:       Visual Product designer/customizer is a WordPress WooCommerce Plugin which is used to design or customize VISUALY any woocommerce products like Bikes, Headphones, Sunglasses, Watches, Controllers, T-shirts and even Pizzas – no limitations!
 * Version:           2.5.21
 * Author:            K2-Service
 * Author URI:        http://k2-service.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       k2-service.com
 * Domain Path:       /shop/product-customizer/
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-customizer-activator.php
 */
function activate_customizer()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-customizer-activator.php';
    Customizer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-customizer-deactivator.php
 */
function deactivate_customizer()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-customizer-deactivator.php';
    Customizer_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_customizer');
register_deactivation_hook(__FILE__, 'deactivate_customizer');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-customizer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_customizer()
{
    $plugin = new Customizer();
    $plugin->run();

}

run_customizer();
