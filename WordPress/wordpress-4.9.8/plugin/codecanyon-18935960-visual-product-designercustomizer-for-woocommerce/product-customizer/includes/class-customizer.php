<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link              http://k2-service.com/shop/product-customizer/
 * @author            K2-Service <plugins@k2-service.com>
 *
 * @package           Customizer
 * @subpackage        Customizer/includes
 */
class Customizer
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Customizer_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        $this->plugin_name = 'product-customizer';
        $this->version     = '2.5.21';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Customizer_Loader. Orchestrates the hooks of the plugin.
     * - Customizer_i18n. Defines internationalization functionality.
     * - Customizer_Admin. Defines all hooks for the admin area.
     * - Customizer_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-customizer-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-customizer-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-customizer-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-customizer-public.php';

        /**
         * Add dublication (clone) functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-customizer-duplicate.php';

        $this->loader = new Customizer_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Customizer_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Customizer_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Customizer_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        //prepare layout
        $this->loader->add_filter('screen_layout_columns', $plugin_admin, 'get_customizer_screen_layout_columns');
        $this->loader->add_filter('get_user_option_screen_layout_customizer', $plugin_admin, 'get_screen_layout_customizer');
        $this->loader->add_filter('get_user_option_meta-box-order_customizer', $plugin_admin, 'get_meta_order');
        //create new post type
        $this->loader->add_action('init', $plugin_admin, 'register_customizer_post_type');

	    //add menu
        $this->loader->add_filter('set-screen-option', $plugin_admin, 'set_saved_screen_option', 10, 3);
        $this->loader->add_action('admin_menu', $plugin_admin, 'get_customizer_menu');
        $this->loader->add_action('load-customizer_page_customizer-saved', $plugin_admin, 'add_option_on_page');
        //add box with configuration
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'get_config_box');
        //save customizer
        $this->loader->add_action('save_post_customizer', $plugin_admin, 'save_post_customizer');
        $this->loader->add_action('save_post', $plugin_admin, 'save_post_customizer');

        $this->loader->add_action('woocommerce_hidden_order_itemmeta', $plugin_admin, 'get_hidden_order_meta');
        $this->loader->add_action('woocommerce_before_order_itemmeta', $plugin_admin, 'get_admin_order_item_render', 10, 3);
        $this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'delete_custom_image');

        $this->loader->add_filter('woocommerce_admin_order_item_thumbnail', $plugin_admin, 'get_thumbnail_customizer', 99, 3);
        $this->loader->add_filter('woocommerce_order_get_items', $plugin_admin, 'order_get_items', 99, 2);

        // upgrade plugin
        $this->loader->add_action('plugins_loaded', $plugin_admin, 'check_upgrade_plugin', 10);
        $this->loader->add_filter('upload_mimes', $plugin_admin, 'add_mime_types', 99, 1);
        $this->loader->add_action('before_delete_post', $plugin_admin, 'before_delete_customizer');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Customizer_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_filter('template_include', $plugin_public, 'switch_template');
        $this->loader->add_action('template_redirect', $plugin_public, 'add_to_cart');

//        $this->loader->add_filter('woocommerce_cart_item_name', $plugin_public, 'change_link', 10, 2);
        $this->loader->add_filter("woocommerce_cart_item_name", $plugin_public, "get_item_data", 99, 3);
        $this->loader->add_filter('woocommerce_add_cart_item_data', $plugin_public, 'force_individual_items', 10, 2);
        $this->loader->add_filter('woocommerce_cart_item_thumbnail', $plugin_public, "get_customizer_image", 99, 3);
        $this->loader->add_action('woocommerce_before_calculate_totals', $plugin_public, 'change_price');

        $this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'add_item_meta');

        //add to cart button
        $this->loader->add_action('woocommerce_after_add_to_cart_button', $plugin_public, 'get_customizer_button');
        $this->loader->add_filter('woocommerce_loop_add_to_cart_link', $plugin_public, 'get_customizer_button_loop', 10, 2);

        //Email with order
        $this->loader->add_action('woocommerce_order_item_meta_start', $plugin_public, 'email_order_item_meta', 10, 3);

        $this->loader->add_filter('woocommerce_order_item_name', $plugin_public, 'get_item_data_order', 99, 2);
        $this->loader->add_filter('woocommerce_order_items_meta_display', $plugin_public, 'item_meta_display', 99, 2);
        $this->loader->add_filter('woocommerce_order_item_get_formatted_meta_data', $plugin_public, 'formatted_meta_data', 99, 2);

        $this->loader->add_filter('woocommerce_display_item_meta', $plugin_public, 'hide_woocommerce_items_order', 99, 3);

        //insert menu field in my-account
        $this->loader->add_action('init', $plugin_public, 'add_saved_customizers_endpoint');
        $this->loader->add_filter('query_vars', $plugin_public, 'saved_customizers_query_vars', 0);
        if ($plugin_public->can_show_save_form()) {
            $this->loader->add_filter('woocommerce_account_menu_items', $plugin_public, 'add_saved_customizers_link_my_account');
            $this->loader->add_action('woocommerce_account_saved-customizers_endpoint', $plugin_public, 'table_saved_customizer_content');
        }

        // add meta tags
        $this->loader->add_action('wp_head', $plugin_public, 'add_meta_tags');
        //change title for saved customizers
        $this->loader->add_filter('pre_get_document_title', $plugin_public, 'change_title_saved_customizer_page', 10);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Customizer_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}
