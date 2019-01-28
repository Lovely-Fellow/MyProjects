<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link              http://k2-service.com/shop/product-customizer/
 * @author            K2-Service <plugins@k2-service.com>
 *
 * @package           Customizer
 * @subpackage        Customizer/admin
 */
class Customizer_Admin
{

    const CUSTOMIZER_SETTINGS_KEY = 'customizer-settings';

    const CUSTOMIZER_DATA_KEY = 'customizer';

    const CUSTOMIZER_DATA_JSON_KEY = 'customizer-component-json';

    const CUSTOMIZER_RULES_KEY = 'customizer-rules';

    const TAG_READ_MORE = '<!--more-->';

    const USER_CAPS = [
        'read',
        'manage_product_terms',
        'read_customizer',
        'read_private_customizers',
        'edit_customizer',
        'edit_customizers',
        'edit_others_customizers',
        'edit_published_customizers',
        'edit_private_customizers',
        'publish_customizers',
        'delete_customizers',
        'delete_others_customizers',
        'delete_private_customizers',
        'delete_published_customizers',
    ];
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;
    private $saved_table;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/customizer.min.css', [], $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_grid', plugin_dir_url(__FILE__) . 'css/grid.min.css', [], $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_modal', plugin_dir_url(__FILE__) . 'css/modal.min.css', [], $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', [], $this->version, 'all');

        // get fonts css
        $fonts = get_option('customizer_fonts', array());
        foreach ($fonts as $name) {
            wp_enqueue_style('google-fonts-' . urlencode($name), 'http://fonts.googleapis.com/css?family=' . urlencode($name), false);
        }

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/customizer.js', ['jquery', 'jquery-ui-core', 'jquery-ui-sortable'], $this->version, false);
        wp_enqueue_script($this->plugin_name . '_modal', plugin_dir_url(__FILE__) . 'js/modal.min.js', ['jquery'], $this->version, false);
        wp_enqueue_script($this->plugin_name . '_jquery-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', ['jquery'], '1.12.1', false);
        wp_enqueue_script($this->plugin_name . '_font-combobox', plugin_dir_url(__FILE__) . 'js/font-combobox.js', ['jquery'], $this->version, false);
        wp_enqueue_script($this->plugin_name . '_color-picker', plugin_dir_url(__FILE__) . 'js/jscolor.min.js', [], $this->version, false);
    }

    /**
     *
     */
    public function check_upgrade_plugin()
    {
        if (get_site_option('customizer_db_version') != $this->version) {
            $this->db_upgrade();
            update_option('customizer_db_version', $this->version);
        }
    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function get_customizer_screen_layout_columns($columns)
    {
        $columns['customizer'] = 1;

        return $columns;
    }

    /**
     * @return int
     */
    public function get_screen_layout_customizer()
    {
        return 1;
    }

    /**
     * @param $order
     *
     * @return mixed
     */
    public function get_meta_order($order)
    {

        $order['advanced'] = 'customizer-preview-box,customizer-settings-box,customizer-rules-box';

        return $order;
    }


    /**
     * Register new post type
     */
    public function register_customizer_post_type()
    {
        $labels = [
            'name'               => _x('Customizer', 'customizer'),
            'singular_name'      => _x('Customizers', 'customizer'),
            'add_new'            => _x('New Customizer', 'customizer'),
            'add_new_item'       => _x('New Customizer', 'customizer'),
            'edit_item'          => _x('Edit Customizer', 'customizer'),
            'new_item'           => _x('New Customizer', 'customizer'),
            'view_item'          => _x('View Customizer', 'customizer'),
            'not_found'          => _x('No customizer found', 'customizer'),
            'not_found_in_trash' => _x('No customizer in the trash', 'customizer'),
            'menu_name'          => _x('Customizer', 'customizer'),
            'all_items'          => _x('All Customizers', 'customizer'),
        ];

        $args = [
            'labels'              => $labels,
            'hierarchical'        => false,
            'description'         => 'Customizers',
            'supports'            => ['title'],
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'map_meta_cap'        => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'query_var'           => true,
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'customizer'],
            'can_export'          => true,
            'menu_icon'           => 'dashicons-images-alt2',
            'capability_type'     => 'customizer',
        ];

        register_post_type('customizer', $args);
        require_once(__DIR__ . '/../includes/class-customizer-activator.php');
        Customizer_Activator::activate();

        flush_rewrite_rules();
    }

    /**
     * Get menu items for Product Customizer
     */
    public function get_customizer_menu()
    {
        $parent_slug = 'edit.php?post_type=customizer';

        add_submenu_page(
            $parent_slug,
            __('Font Management', 'customizer'),
            __('Font Management', 'customizer'),
            'manage_product_terms',
            'customizer-font-settings',
            [
                $this,
                'get_customizer_fonts_page'
            ]
        );

        add_submenu_page(
            $parent_slug,
            __('Color Management', 'customizer'),
            __('Color Management', 'customizer'),
            'manage_product_terms',
            'customizer-color-settings',
            [
                $this,
                'get_customizer_color_page'
            ]
        );

        add_submenu_page(
            $parent_slug,
            __('Saved customizers', 'customizer'),
            __('Saved customizers', 'customizer'),
            'manage_product_terms',
            'customizer-saved',
            [
                $this,
                'get_saved_customizers_page'
            ]
        );

        add_submenu_page(
            $parent_slug,
            __('Import/Export', 'customizer'),
            __('Import/Export', 'customizer'),
            'manage_product_terms',
            'customizer-import-export',
            [
                $this,
                'get_customizer_import_export_page'
            ]
        );

        add_submenu_page(
            $parent_slug,
            __('Settings', 'customizer'),
            __('Settings', 'customizer'),
            'manage_product_terms',
            'customizer-settings',
            [
                $this,
                'get_customizer_settings_page'
            ]
        );

    }

    public function get_customizer_fonts_page()
    {
        $html           = '';
        $optionKey      = 'customizer_fonts';
        $optionFontKey  = 'customizer_font_key';
        $availableFonts = get_option($optionKey, array());

        $fontKey = empty(get_option($optionFontKey, '')) ? 'AIzaSyBkD9tkr0p5Qpt28n6yKd76Dc_uQDMWujU' : get_option($optionFontKey);

        // get fonts by key from Google
        if (!empty($_POST['font_key'])) {
            $fontKey = $_POST['font_key'];
            update_option($optionFontKey, $fontKey);
        }

        if (!$google_fonts = self::get_all_fonts($fontKey)) {
            $html .= '<div class="error"><h4>' . __('Wrong Google font key.', 'customizer') . '</h4></div>';
        }

        // delete font from options
        if (!empty($_POST['delete_font'])) {
            $index = array_search($_POST['delete_font'], $availableFonts);
            unset($availableFonts[$index]);
            update_option($optionKey, $availableFonts, 'no');
        }

        // add font to options
        if (!empty($_POST['font_combobox'])) {
            $font_name = $_POST['font_combobox'];
            if (!empty($availableFonts)) {
                if (array_search($font_name, $availableFonts) === false) {
                    $availableFonts[] = $font_name;
                }
            } else {
                $availableFonts[] = $font_name;
            }
            update_option($optionKey, $availableFonts, 'no');
        }

        // save fonts position after sort table
        if (!empty($_POST['new_position'])) {
            $fonts_in       = json_decode(stripslashes($_POST['new_position']));
            $availableFonts = [];
            foreach ($fonts_in as $font) {
                $availableFonts[] = $font->name;
            }
            update_option($optionKey, $availableFonts, 'no');
        }

        $html .= '<div class="wrap woocommerce">' .
            '<h2>' . __('Font Management', 'customizer') . '</h2>' .
            '<div id="col-container"><div id="col-right">' .
            '<div class="col-wrap">' .
            '<table class="widefat fixed" style="width:100%">' .
            '<thead><tr><th width="65%">' . __('Title', 'customizer') . '</th>' .
            '<th width="20%">' . __('Preview', 'customizer') . '</th>' .
            '<th align="center">' . __('Remove', 'customizer') . '</th></tr></thead><tbody id="sortable">';

        if (!empty($availableFonts)) {
            foreach ($availableFonts as $font) {
                $id   = 'del_fonts_form_' . $font;
                $html .= '<tr><td>' . $font . '</td>' .
                    '<td><a href="https://fonts.google.com/specimen/' . $font . '" target="_blank">' . __('Preview', 'customizer') . '</a></td>' .
                    '<td align="center"><form action="" id="' . $id . '" method="post">' .
                    '<input type="hidden" name="delete_font" value="' . $font . '">' .
                    '<a href="#" class="remove_component" onClick="document.getElementById(\'' . $id . '\').submit();"><span class="dashicons dashicons-no-alt"></span></a></form></td>' .
                    '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="3">' . __('No fonts found.', 'customizer') . '</td></tr>';
        }

        $html .= '</tbody></table></div></div></div>';

        $html .= '<div id="col-left"><div class="col-wrap"><div class="form-wrap">';

        $html .= '<form action="" method="post" id="form_set_fonts">';
        $html .= '<label for="font_key">' . __('API Key', 'customizer') . '</label>' .
            '<input id="font_key" name="font_key" type="text" value="' . $fontKey . '">' .
            '<p><a href="https://console.developers.google.com/flows/enableapi?apiid=webfonts&reusekey=true" target="_blank">' . __('Get an API key', 'customizer') . '</a></p>';
        $html .= '<h3>' . __('Add New Font', 'customizer') . '</h3>';

        if (!empty($google_fonts)) {
            $html .= '<div class="ui-widget form-field"> <select id="font_combobox" name="font_combobox">';
            $html .= '<option value=" "> </option>';
            foreach ($google_fonts as $font => $file) {
                $html .= '<option value="' . $font . '">' . $font . '</option>';
            }
            $html .= '</select></div>';
        }
        $html .= '<p class="submit"><input type="submit" name="add_new_font" id="submit" class="button button-primary" value="' . __('Save', 'customizer') . '"></p>' .
            wp_nonce_field('woocommerce-add-new_font');


        $html .= '</form></div></div></div>';

        $html .= '</div>';
        echo $html;
    }

    /**
     * @param null $fontKey
     * @return array|bool
     */
    static public function get_all_fonts($fontKey = null)
    {
        if (!$fontKey) {
            $optionFontKey = 'customizer_font_key';
            $fontKey       = empty(get_option($optionFontKey)) ? 'AIzaSyBkD9tkr0p5Qpt28n6yKd76Dc_uQDMWujU' : get_option($optionFontKey);
        }

        $result = [];
        $url    = 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $fontKey;
        $data   = self::get_content_by_url($url);
        $data   = json_decode($data, true);

        if (!empty($data)) {
            foreach ($data['items'] as $item) {
                if (!empty($item['family']) && !empty($item['files']) && !empty($item['files']['regular'])) {
                    $result[$item['family']] = $item['files']['regular'];
                }
            }
        }

        return $result;
    }

    public function get_customizer_color_page()
    {
        $optionKey      = 'customizer_colors';
        $availableColor = get_option($optionKey, array());

        // delete font from options
        if (!empty($_POST['delete_color'])) {
            $index = $_POST['delete_color'];
            unset($availableColor[$index]);
            update_option($optionKey, $availableColor, 'no');
        }

        // add color to options
        if (!empty($_POST['color_val'])) {
            $color_name = $_POST['color_name'];
            $color_val  = $_POST['color_val'];
            if (!empty($availableColor)) {
                if (!isset($availableColor[$color_val])) $availableColor[$color_val] = $color_name;
            } else {
                $availableColor[$color_val] = $color_name;
            }
            update_option($optionKey, $availableColor, 'no');
        }

        // save colors position after sort table
        if (!empty($_POST['new_position'])) {
            $color_in       = json_decode(stripslashes($_POST['new_position']));
            $availableColor = [];
            foreach ($color_in as $color) {
                $availableColor[$color->value] = $color->name;
            }
            update_option($optionKey, $availableColor, 'no');
        }

        $html = '<div class="wrap woocommerce">' .
            '<h2>' . __('Color Management', 'customizer') . '</h2>' .
            '<div id="col-container"><div id="col-right">' .
            '<div class="col-wrap">' .
            '<table class="widefat fixed" style="width:100%">' .
            '<thead><tr><th width="50%">' . __('Name', 'customizer') . '</th>' .
            '<th width="35%">' . __('Color', 'customizer') . '</th>' .
            '<th width="15%" align="center">' . __('Remove', 'customizer') . '</th></tr></thead><tbody id="sortable">';

        if (!empty($availableColor)) {
            foreach ($availableColor as $color => $name) {
                $id   = 'del_color_form_' . $color;
                $html .= '<tr><form action="" id="' . $id . '" method="post"><td>' . $name . '</td>' .
                    '<td style="background-color:#' . $color . '">' . $color . '</td>' .
                    '<td align="center"><input type="hidden" name="delete_color" value="' . $color . '">' .
                    '<a href="#" class="remove_component" onClick="document.getElementById(\'' . $id . '\').submit();"><span class="dashicons dashicons-no-alt"></span></a></td>' .
                    '</form></tr>';
            }
        } else {
            $html .= '<tr><td colspan="100%">' . __('No colors found.', 'customizer') . '</td></tr>';
        }

        $html .= '</tbody></table></div></div></div>';

        $html .= '<div id="col-left"><div class="col-wrap"><div class="form-wrap">';

        $html .= '<h3>' . __('Add New Color', 'customizer') . '</h3>';

        $html .= '<form action="" method="post" class="customizer_color_form">';
        $html .= '<label for="color_name">' . __('Color Name', 'customizer') . '</label>' .
            '<input name="color_name" type="text" value="">' .
            '<label for="color_val">' . __('Color Value', 'customizer') . '</label>' .
            '<input name="color_val" class="jscolor" type="text" value="">';
        $html .= '<p>&nbsp;</p><p class="submit"><input type="submit" name="add_new_color" id="submit" class="button button-primary" value="' . __('Add Color', 'customizer') . '"></p>' .
            wp_nonce_field('woocommerce-add-new_color');

        $html .= '</form></div></div></div>';

        $html .= '</div>';
        echo $html;
    }

    /**
     * Get html for settings page
     */
    public function get_customizer_settings_page()
    {
        $optionsKey = 'customizer-options';

        $html = '';
        if (!empty($_POST['convert_old_url'])) {
            $this->fix_old_image_url();
        } elseif (!empty($_POST[$optionsKey])) {
            update_option($optionsKey, $_POST[$optionsKey]);
            flush_rewrite_rules();
        }

        $savedValues = get_option($optionsKey, array());

        $html .= '<div class="wrap woocommerce">' .
            '<h1>' . __('Customizer Settings', 'customizer') . '</h1>';
        $html .= '<form method="POST" action="" class="mg-top"><div class="postbox" id="customizer-options-container">';

        $html .= $this->get_wrap_html();

        $orderImage = [
            'title'       => __('Show Customizer in Admin Panel', 'customizer'),
            'name'        => 'customizer-options[customizer-as-one-image]',
            'type'        => 'select',
            'options'     => [
                0 => __('Show as html', 'customizer'),
                1 => __('Generate as one image', 'customizer')
            ],
            'default'     => !empty($savedValues['customizer-as-one-image']) ? $savedValues['customizer-as-one-image'] : 0,
            'class'       => 'chosen_select_nostd',
            'description' => __('Generated image could be putted in PDF but coordinates may differ by several pixels ', 'customizer')
        ];

        $html .= '<tr>';
        $html .= $this->add_admin_element($orderImage, 'select');
        $html .= '</tr><tr>';

        $saveDesign = [
            'title'       => __('Save Customizer', 'customizer'),
            'name'        => 'customizer-options[save_design]',
            'options'     => [0 => __('No', 'customizer'), 1 => __('Yes', 'customizer')],
            'default'     => !empty($savedValues['save_design']) ? $savedValues['save_design'] : 0,
            'class'       => 'chosen_select_nostd',
            'description' => __('User can save customizer in account or get the link to his own customizer', 'customizer'),
        ];

        $html .= $this->add_admin_element($saveDesign, 'select');
        $html .= '</tr><tr>';

        $showZeroPrice = [
            'title'       => __('Zero price', 'customizer'),
            'name'        => 'customizer-options[zero_price]',
            'options'     => [0 => __('No', 'customizer'), 1 => __('Yes', 'customizer')],
            'default'     => isset($savedValues['zero_price']) ? $savedValues['zero_price'] : 1,
            'class'       => 'chosen_select_nostd customizer_settings_standart',
            'description' => __('Show zero price', 'customizer'),
        ];
        $html          .= $this->add_admin_element($showZeroPrice, 'select');

        $html              .= '</tr><tr>';
        $social_button_css = [
            'title'       => __('Way to load <strong>fontawesome.css</strong> file', 'customizer'),
            'name'        => 'customizer-options[social_button_css]',
            'options'     => [
                0 => __('Load from CDN (recommended)', 'customizer'),
                1 => __('Load from your website', 'customizer'),
                2 => __('Don\'t load (Already loaded by another plugin or theme)', 'customizer'),
            ],
            'default'     => isset($savedValues['social_button_css']) ? $savedValues['social_button_css'] : 0,
            'class'       => 'chosen_select_nostd customizer_settings_standart',
            'description' => __('For displaying fontawesome icons', 'customizer'),
        ];
        $type_element      = (!empty($savedValues['save_design'])) ? 'select' : 'hidden';
        $html              .= $this->add_admin_element($social_button_css, $type_element);
        $html              .= '</tr>';

        $html .= $this->get_wrap_html(false);

        $html .= '</div><input type="submit" class="button button-primary button-large" value="Save">';
        $html .= '<input type="submit" class="button button-primary button-large" value="' . __('Convert old urls', 'customizer') . '" name="convert_old_url"></div></form>';
        echo $html;
    }

    /**
     * Get html for settings page
     */
    public function get_saved_customizers_page()
    {
        $change_state = array_search('changed', $_POST);
        if (!empty($change_state)) {
            list($hash, $state) = explode('_', $change_state);
            Customizer_Public::update_state_customizer($hash, $state);
        } elseif (!empty($_POST['action'])) {
            $ids = implode(',', $_POST['permanent_del']);
            if (!empty($ids)) {
                global $wpdb;
                $name_table = $wpdb->prefix . Customizer_Public::SAVE_TABLE_NAME;
                switch ($_POST['action']) {
                    case 'delete' :
                        $wpdb->query("UPDATE {$name_table} set deleted = 1 WHERE ID IN($ids)");
                        break;
                    case 'publish' :
                        $wpdb->query("UPDATE {$name_table} set public = 1 WHERE ID IN($ids)");
                        break;
                    case 'hide' :
                        $wpdb->query("UPDATE {$name_table} set public = 0 WHERE ID IN($ids)");
                        break;
                }
            }
        }
        echo '<h1>' . __('Saved customizers', 'customizer') . '</h1>';
        echo '<form action="" method="POST">';
        $this->saved_table->prepare_items();
        $this->saved_table->display();
        echo '</form>';
    }

    public function add_option_on_page()
    {
        require_once(plugin_dir_path(__FILE__) . 'class-customizer-list-table.php');
        $option = 'per_page';
        $args   = array(
            'label'   => __('Saved customizers per page', 'customizer'),
            'default' => 10,
            'option'  => 'saved_customizer_per_page'
        );
        add_screen_option($option, $args);
        $this->saved_table = new CustomizerListTable();
    }

    public function set_saved_screen_option($status, $option, $value)
    {
        return ($option === 'saved_customizer_per_page') ? (int)$value : $status;
    }

    public function get_customizer_import_export_page()
    {
        set_time_limit(0);
        $export_val = [];
        $zip_arr    = [];
        $upload_dir = wp_get_upload_dir();

        if (!empty($_POST['export'])) {
            $exports = $_POST['export'];
            foreach ($exports as $context) {
                switch ($context) {
                    case 'main' :
                        $optionKey    = 'customizer-options';
                        $export_val[] = ['type' => 'main', 'value' => get_option($optionKey, array())];
                        break;
                    case 'colors' :
                        $optionKey    = 'customizer_colors';
                        $export_val[] = ['type' => 'color', 'value' => get_option($optionKey, array())];
                        break;
                    case 'fonts' :
                        $optionKey     = 'customizer_fonts';
                        $optionFontKey = 'customizer_font_key';
                        $export_val[]  = ['type' => 'font', 'key' => get_option($optionFontKey, ''), 'value' => get_option($optionKey, array())];
                        break;
                    case 'customizers' :
                        $customizers = get_posts(['numberposts' => 0, 'post_type' => 'customizer']);
                        $cust_arr    = [];
                        foreach ($customizers as $customizer) {
                            $components = Customizer_Public::get_customizer_meta($customizer->ID);
                            $cust_arr[] = ['inf' => $customizer, 'components' => $components];
                        }
                        $export_val[] = ['type' => 'customizer', 'value' => $cust_arr];
                        break;
                    case 'images' :
                        $customizers = get_posts(['numberposts' => 0, 'post_type' => 'customizer']);
                        $path_arr    = [];
                        if (extension_loaded('zip')) {
                            foreach ($customizers as $customizer) {
                                $components = Customizer_Public::get_customizer_meta($customizer->ID);
                                if (!empty($components['customizer-settings']['slides'])) {
                                    foreach ($components['customizer-settings']['slides'] as $key => $slide) {
                                        $zip_field  = $this->get_export_image_path($slide, $upload_dir);
                                        $zip_arr[]  = $zip_field['field'];
                                        $path_arr[] = [
                                            'customizer_title' => $customizer->post_title,
                                            'type'             => 'customizer_slides',
                                            'slide'            => $key,
                                            'path'             => $zip_field['path']
                                        ];
                                    }
                                }
                                if (!empty($components['customizer'])) {
                                    foreach ($components['customizer'] as $id_component => $cust) {
                                        if (!empty($cust['component_icon'])) {
                                            $zip_field  = $this->get_export_image_path($cust['component_icon'], $upload_dir);
                                            $zip_arr[]  = $zip_field['field'];
                                            $path_arr[] = [
                                                'customizer_title' => $customizer->post_title,
                                                'component_id'     => $id_component,
                                                'id'               => $id_component,
                                                'type'             => 'component',
                                                'name'             => 'component_icon',
                                                'path'             => $zip_field['path']
                                            ];
                                        }
                                        if (!empty($cust['options'])) {
                                            foreach ($cust['options'] as $id_option => $option) {
                                                if (!empty($option['option_icon'])) {
                                                    $zip_field  = $this->get_export_image_path($option['option_icon'], $upload_dir);
                                                    $zip_arr[]  = $zip_field['field'];
                                                    $path_arr[] = [
                                                        'customizer_title' => $customizer->post_title,
                                                        'component_id'     => $id_component,
                                                        'id'               => $id_option,
                                                        'type'             => 'option',
                                                        'name'             => 'option_icon',
                                                        'path'             => $zip_field['path']
                                                    ];
                                                }
                                                if (!empty($option['option_image'])) {
                                                    $slides = is_array($option['option_image']) ? $option['option_image'] : [$option['option_image']];
                                                    foreach ($slides as $key => $slide) {
                                                        if (!empty($slide)) {
                                                            $zip_field  = $this->get_export_image_path($slide, $upload_dir);
                                                            $zip_arr[]  = $zip_field['field'];
                                                            $path_arr[] = [
                                                                'customizer_title' => $customizer->post_title,
                                                                'component_id'     => $id_component,
                                                                'id'               => $id_option,
                                                                'type'             => 'option',
                                                                'name'             => 'option_image',
                                                                'slide'            => $key,
                                                                'path'             => $zip_field['path']
                                                            ];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $export_val[] = ['type' => 'images', 'value' => $path_arr];
                        break;
                }
            }
            $export_val = json_encode($export_val);
            $file_name  = 'settings.json';
            $zip        = new ZipArchive();
            $zip_name   = 'customizer_' . date('Y-m-d_H-i-s') . '.zip';
            $zip->open($upload_dir['path'] . '/' . $zip_name, ZIPARCHIVE::CREATE);

            foreach ($zip_arr as $files) {
                if (!empty($files['zip_name'])) {
                    $zip->addFile($files['real_name'], $files['zip_name']);
                }
            }
            $zip->addFromString($file_name, $export_val);
            $zip->close();

            wp_redirect(plugin_dir_url(__FILE__) . 'partials/customizer-admin-export-page.php?file_name=' . $zip_name . '&dir=' . $upload_dir['path']);
            exit;
        }

        $import_success = true;
        $err_message    = '';
        if (!empty($_POST['button_import'])) {
            if (!empty($_FILES) && $_FILES['import_file_name']['type'] === 'application/zip') {
                $zip = new ZipArchive();
                $res = $zip->open($_FILES['import_file_name']['tmp_name']);
                if ($res === true) {
                    $zip_dir = $upload_dir['basedir'] . '/zip_customizer';
                    $zip->extractTo($zip_dir);
                    $zip->close();

                    $import_vals = file_get_contents($zip_dir . '/settings.json');
                    $import_vals = json_decode($import_vals, true);
                    if ($import_vals !== null) {
                        $action  = $_POST['import_action'];
                        $compare = $_POST['import_compare'];
                        foreach ($import_vals as $import_val) {
                            $context = $import_val['type'];
                            $value   = $import_val['value'];
                            switch ($context) {
                                case 'main' :
                                    $optionKey = 'customizer-options';
                                    if (!get_option($optionKey) || get_option($optionKey) !== $value) {
                                        $import_result = update_option($optionKey, $value, 'no');
                                        if (!$import_result) {
                                            $import_success = false;
                                            $err_message    .= __('Error import. Main clause.', 'customizer') . '<br />';
                                        }
                                    }
                                    break;
                                case 'color' :
                                    $optionKey = 'customizer_colors';
                                    if (!get_option($optionKey) || get_option($optionKey) !== $value) {
                                        $import_result = update_option($optionKey, $value, 'no');
                                        if (!$import_result) {
                                            $import_success = false;
                                            $err_message    .= __('Error import. Color clause.', 'customizer') . '<br />';
                                        }
                                    }
                                    break;
                                case 'font' :
                                    $optionKey     = 'customizer_fonts';
                                    $optionFontKey = 'customizer_font_key';
                                    $key           = $import_val['key'];
                                    if (!get_option($optionFontKey) || get_option($optionFontKey) !== $key) {
                                        $import_result = update_option($optionFontKey, $key, 'no');
                                        if (!$import_result) {
                                            $import_success = false;
                                            $err_message    .= __('Error import. Font clause. (google key)', 'customizer') . '<br />';
                                        }
                                    }
                                    if (!get_option($optionKey) || get_option($optionKey) !== $value) {
                                        $import_result = update_option($optionKey, $value, 'no');
                                        if (!$import_result) {
                                            $import_success = false;
                                            $err_message    .= __('Error import. Font clause. (fonts)', 'customizer') . '<br />';
                                        }
                                    }
                                    break;
                                case 'customizer' :
                                    $curr_customizers = get_posts(['numberposts' => 0, 'post_type' => 'customizer']);
                                    $curr_fields      = [];
                                    foreach ($curr_customizers as $curr_customizer) {
                                        switch ($compare) {
                                            case 'comp_id' :
                                                $curr_fields[] = $curr_customizer->ID;
                                                break;
                                            case 'comp_name' :
                                                $curr_fields[] = $curr_customizer->post_title;
                                                break;
                                            case 'comp_id_name':
                                                $curr_fields[] = [$curr_customizer->ID, $curr_customizer->post_title];
                                                break;
                                        }
                                    }

                                    foreach ($value as $customizer) {
                                        $inform                = $customizer['inf'];
                                        $components            = $customizer['components'];
                                        $inform['post_author'] = get_current_user_id();
                                        $import_fields         = '';
                                        $customizer_id         = 0;

                                        switch ($compare) {
                                            case 'comp_id' :
                                                $import_fields = $inform['ID'];
                                                break;
                                            case 'comp_name' :
                                                $import_fields = $inform['post_title'];
                                                break;
                                            case 'comp_id_name':
                                                $import_fields = [$inform['ID'], $inform['post_title']];
                                                break;
                                        }
                                        switch ($action) {
                                            case 'act_new' :
                                                if (in_array($import_fields, $curr_fields)) {
                                                    $inform['post_title'] .= ' ' . date('Y-m-d_H-i-s');
                                                    $inform['post_name']  .= '-' . date('Y-m-d_H-i-s');
                                                }
                                                unset($inform['ID']);
                                                $customizer_id = wp_insert_post($inform);
                                                if (!$customizer_id) {
                                                    $import_success = false;
                                                    $err_message    .= __('Error import. Customizer clause. Create customizer.', 'customizer') . '(' . $inform['post_title'] . ') <br />';
                                                }
                                                break;
                                            case 'act_update' :
                                                if (in_array($import_fields, $curr_fields)) {
                                                    $customizer_id = wp_update_post($inform);
                                                    if (!$customizer_id) {
                                                        $import_success = false;
                                                        $err_message    .= __('Error import. Customizer clause. Update customizer.', 'customizer') . '(' . $inform['post_title'] . ') <br />';
                                                    }
                                                } else {
                                                    unset($inform['ID']);
                                                    $customizer_id = wp_insert_post($inform);
                                                    if (!$customizer_id) {
                                                        $import_success = false;
                                                        $err_message    .= __('Error import. Customizer clause. Create customizer.', 'customizer') . '(' . $inform['post_title'] . ') <br />';
                                                    }
                                                }
                                                break;
                                            case 'act_rewrite' :
                                                if (in_array($import_fields, $curr_fields)) {
                                                    wp_delete_post($inform['ID'], true);
                                                }
                                                unset($inform['ID']);
                                                $customizer_id = wp_insert_post($inform);
                                                if (!$customizer_id) {
                                                    $import_success = false;
                                                    $err_message    .= __('Error import. Customizer clause. Create customizer.', 'customizer') . '(' . $inform['post_title'] . ') <br />';
                                                }
                                                break;
                                            case 'act_nothing':
                                                if (in_array($import_fields, $curr_fields)) continue 2;

                                                unset($inform['ID']);
                                                $customizer_id = wp_insert_post($inform);
                                                if (!$customizer_id) {
                                                    $import_success = false;
                                                    $err_message    .= __('Error import. Customizer clause. Create customizer.', 'customizer') . '(' . $inform['post_title'] . ') <br />';
                                                }
                                                break;
                                        }

                                        foreach ($components as $key => $val) {
                                            if (!in_array($key, ['_edit_lock', '_edit_last'])) {
                                                $import_result = update_post_meta($customizer_id, $key, $val);
                                                if (!$import_result) {
                                                    $import_success = false;
                                                    $err_message    .= __('Error import. Customizer clause. Update components.', 'customizer') . '(' . $customizer_id . ') <br />';
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 'images' :
                                    $images = $value;
                                    foreach ($images as $image) {
                                        $customizer = get_page_by_title($image['customizer_title'], OBJECT, 'customizer');
                                        if (!empty ($customizer)) {
                                            if (Customizer_Public::get_image_full_url($image['path']) !== $image['path']) {
                                                $source_file    = $zip_dir . $image['path'];
                                                $dest_file      = $upload_dir['basedir'] . $image['path'];
                                                $dest_file_info = pathinfo($dest_file);
                                                if (!empty($customizer) && (!file_exists($dest_file) || !empty($_POST['rewrite_images']))) {
                                                    if (!file_exists($source_file)) {
                                                        continue;
                                                    }
                                                    if (!file_exists($dest_file_info['dirname'])) {
                                                        if (!mkdir($dest_file_info['dirname'], 0777, true)) {
                                                            $import_success = false;
                                                            $err_message    .= __('Error import. Images clause. Cannot create directory.', 'customizer') . '(' . $dest_file_info['dirname'] . ') <br />';
                                                        }
                                                    }
                                                    $new_file = !file_exists($dest_file);
                                                    $content  = file_get_contents($source_file);
                                                    if (!file_put_contents($dest_file, $content)) {
                                                        $import_success = false;
                                                        $err_message    .= __('Error import. Images clause. Cannot create file.', 'customizer') . '(' . $dest_file . ') <br />';
                                                    }
                                                    if ($new_file) {
                                                        $filetype   = wp_check_filetype(basename($dest_file), null);
                                                        $attachment = array(
                                                            'guid'           => $upload_dir['baseurl'] . $image['path'] . '/' . basename($dest_file),
                                                            'post_mime_type' => $filetype['type'],
                                                            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($dest_file)),
                                                            'post_content'   => '',
                                                            'post_status'    => 'inherit'
                                                        );
                                                        wp_insert_attachment($attachment, $dest_file, $customizer->ID);
                                                    }
                                                }
                                                $components = Customizer_Public::get_customizer_meta($customizer->ID);
                                                switch ($image['type']) {
                                                    case 'customizer_slides' :
                                                        $components['customizer-settings']['slides'][$image['slide']] = $image['path'];
                                                        break;
                                                    case 'component' :
                                                        $components['customizer'][$image['component_id']][$image['name']] = $image['path'];
                                                        break;
                                                    case 'option' :
                                                        if ($image['name'] === 'option_icon') {
                                                            $components['customizer'][$image['component_id']]['options'][$image['id']][$image['name']] = $image['path'];
                                                        } elseif ($image['name'] === 'option_image') {
                                                            if (isset($image['slide'])) {
                                                                if (!is_array($components['customizer'][$image['component_id']]['options'][$image['id']][$image['name']])) {
                                                                    $components['customizer'][$image['component_id']]['options'][$image['id']][$image['name']] = [$image['slide'] => $components['customizer'][$image['component_id']]['options'][$image['id']][$image['name']]];
                                                                } else {
                                                                    $components['customizer'][$image['component_id']]['options'][$image['id']][$image['name']][$image['slide']] = $image['path'];
                                                                }
                                                            } else {
                                                                $components['customizer'][$image['component_id']]['options'][$image['id']][$image['name']] = $image['path'];
                                                            }
                                                        }
                                                        break;
                                                }

                                                foreach ($components as $key => $val) {
                                                    if (!in_array($key, ['_edit_lock', '_edit_last'])) {
                                                        $import_result = update_post_meta($customizer->ID, $key, $val);
//					                                    if ( ! $import_result) {
//						                                    $import_success = false;
//						                                    $err_message    .= __('Error import. Images clause. Cannot update customizer.', 'customizer') . '(' . $key . ')<br />';
//					                                    }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                        self::removeDirectory($zip_dir);
                    } else $err_message = __('Import canceled. Invalid settings format.', 'customizer') . '<br />';
                } else $err_message = __('Import canceled. Invalid archive format.', 'customizer') . '<br />';
            }
        }

        $html = '<h1>' . __('Import / Export', 'customizer') . '</h1>';

        if (!empty($_POST['button_import'])) {
            if ($import_success) {
                $html .= '<div class="updated notice"><h4>' . __('Import success', 'customizer') . '</h4></div>';
            } else {
                $html .= '<div class="error"><h4>' . __('Import with error(s).', 'customizer') . '</h4>' . $err_message . '</div>';
            }
        }

        $html .= '<div class="customizer-admin-wrap postbox">' . '<h2>' . __('Import', 'customizer') . '</h1>';
        $html .= '<form name="import_form" action="" method="post" enctype="multipart/form-data" class="mg-top">';
        $html .= '<table class="wp-list-table widefat fixed pages o-root"><tr>';
        $html .= '<td class="label"><label for="import_file_name">' . __('File for import', 'customizer') . '</label></td>';
        $html .= '<td><input name="import_file_name" type="file" ></td></tr>';
        $html .= '<tr><td class="label"><label for="import_compare">' . __('Field for compare', 'customizer') . '</label></td>';
        $html .= '<td><select name="import_compare">';
        $html .= '<option value="comp_id">' . __('ID', 'customizer') . '</option>';
        $html .= '<option value="comp_name">' . __('Name', 'customizer') . '</option>';
        $html .= '<option value="comp_id_name">' . __('ID and name', 'customizer') . '</option>';
        $html .= '</select></td></tr>';
        $html .= '<tr><td class="label"><label for="import_action">' . __('Action with duplicate', 'customizer') . '</label></td>';
        $html .= '<td><select name="import_action">';
        $html .= '<option value="act_new">' . __('Add new', 'customizer') . '</option>';
        $html .= '<option value="act_update">' . __('Update Existing', 'customizer') . '</option>';
        $html .= '<option value="act_rewrite">' . __('Delete existing and Create new', 'customizer') . '</option>';
        $html .= '<option value="act_nothing">' . __('Nothing do', 'customizer') . '</option>';
        $html .= '</select></td></tr>';
        $html .= '<tr><td class="label"><label for="rewrite_images">' . __('Rewrite exist images', 'customizer') . '</label></td>';
        $html .= '<td><input name="rewrite_images" type="checkbox" value="rewrite_images"></td></tr>';
        $html .= '<tr><td></td><td><input name="button_import" type="submit" class="button button-primary button-large" value="' . __('Import', 'customizer') . '"></td></tr>';
        $html .= '</table></form></div>';
        $html .= '<div class="customizer-admin-wrap postbox"><h2>' . __('Export', 'customizer') . '</h2>';
        $html .= '<form name="export_form" action="" method="post" class="mg-top">';
        $html .= '<table class="wp-list-table widefat fixed pages o-root"><tr>';
        $html .= '<td class="label"><h3>' . __('Select Entities', 'customizer') . '</h3><ul>';
        $html .= '<li><label><input id="customizer_import_mark_all" type="checkbox" checked="checked">' . __('Mark / Unmark all entities', 'customizer') . '</label></li>';
        $html .= '<li><br /></li>';
        $html .= '<li><label><input class="import_entity_type" name="export[]" type="checkbox" value="main" checked="checked">' . __('Main settings', 'customizer') . '</label></li>';
        $html .= '<li><label><input class="import_entity_type" name="export[]" type="checkbox" value="colors" checked="checked">' . __('Colors', 'customizer') . '</label></li>';
        $html .= '<li><label><input class="import_entity_type" name="export[]" type="checkbox" value="fonts" checked="checked">' . __('Fonts', 'customizer') . '</label></li>';
        $html .= '<li><label><input class="import_entity_type" name="export[]" type="checkbox" value="customizers" checked="checked">' . __('Customizers', 'customizer') . '</label></li>';
        $html .= '<li><label><input class="import_entity_type" name="export[]" type="checkbox" value="images" checked="checked">' . __('Images', 'customizer') . '</label></li>';
        $html .= '<li>&nbsp;</li><li><input name="button_export" type="submit" class="button button-primary button-large" value="' . __('Export', 'customizer') . '"></li></ul></td><td></td>';
        $html .= '</td></tr></table></form></div>';
        $html .= '';

        echo $html;
    }

    public function get_config_box()
    {
        add_meta_box(
            'customizer-preview-box',
            __('Customizer Preview', 'customizer'),
            [$this, 'get_customizer_preview_page'],
            'customizer'
        );

        add_meta_box(
            'customizer-settings-box',
            __('Customizer settings', 'customizer'),
            [$this, 'get_customizer_settings_box'],
            'customizer'
        );

        add_meta_box(
            'customizer-rules-box',
            __('Conditional Rules', 'customizer'),
            [$this, 'get_rules_block'],
            'customizer'
        );

    }

    /**
     * Block with preview for each customizer
     *
     * @return string
     */
    public function get_customizer_preview_page()
    {
        echo '<div id="customizer-preview"></div>';
    }

    /**
     * @return array
     */
    public function get_templates_list()
    {
        $result        = [];
        $templatesPath = plugin_dir_path(__FILE__) . '../public/partials/';
        foreach (scandir($templatesPath) as $index => $file) {
            if (is_dir($templatesPath . $file)) {
                continue;
            }
            $name = str_replace(['customizer-template-', '.php'], ['', ''], $file);
            if (substr($name, 0, 1) == '_') {
                continue;
            }

            $name = ucfirst($name);
            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $name, $matches);
            if (!empty($matches[0])) {
                $name = implode(' ', $matches[0]);
            }
            if ($name === 'Modern') {
                $result['customizer-template-ModernDark.php'] = __('Modern Dark', 'customizer');
//	            $result['customizer-template-ModernLight.php'] = __('Modern Light', 'customizer');
            } else {
                $result[$file] = $name;
            }
        }

        $result = apply_filters('get_background_template_path', $result);

        unset ($result['.']);
        unset ($result['..']);

        return $result;
    }

    /**
     * settings for each customizer
     */
    public function get_customizer_settings_box()
    {
        wp_enqueue_media();
        $html = '<div class="block-form">';

        $yesNoOptions = [
            0 => __('No', 'customizer'),
            1 => __('Yes', 'customizer')
        ];

        $templateValues = apply_filters('customizer_settings_templates', $this->get_templates_list());

        $allProducts = $this->get_all_products();

        $savedSettings = get_post_meta(get_the_ID(), self::CUSTOMIZER_SETTINGS_KEY);
        $savedSettings = !empty($savedSettings[0]) ? $savedSettings[0] : [];

        $productOptions = [
            'title'       => __('Product', 'customizer'),
            'name'        => 'customizer-settings[product_id]',
            'options'     => $allProducts,
            'default'     => !empty($savedSettings['product_id']) ? $savedSettings['product_id'] : '',
            'class'       => 'chosen_select_nostd customizer_settings_product_id',
            'description' => __('Select product for customization.', 'customizer'),
        ];

        $useProductImageListing = [
            'title'       => __('Use Product Image On Listing', 'customizer'),
            'name'        => 'customizer-settings[use_product_image_listing]',
            'options'     => $yesNoOptions,
            'default'     => !empty($savedSettings['use_product_image_listing']) ? $savedSettings['use_product_image_listing'] : 0,
            'class'       => 'chosen_select_nostd customizer_settings_standart',
            'description' => __('Use product image as base image for this customizer on listing', 'customizer'),
        ];

        $useProductImageSingle = [
            'title'       => __('Use Product Image On Single Page', 'customizer'),
            'name'        => 'customizer-settings[use_product_image_single]',
            'options'     => $yesNoOptions,
            'default'     => !empty($savedSettings['use_product_image_single']) ? $savedSettings['use_product_image_single'] : 0,
            'class'       => 'chosen_select_nostd customizer_settings_standart',
            'description' => __('Use product image as base image for this customizer on single page', 'customizer'),
        ];

        $addToCartButton = [
            'title'       => __('Add to cart', 'customizer'),
            'name'        => 'customizer-settings[add-to-cart_show]',
            'options'     => $yesNoOptions,
            'default'     => isset($savedSettings['add-to-cart_show']) ? $savedSettings['add-to-cart_show'] : 1,
            'class'       => 'chosen_select_nostd customizer_settings_standart',
            'description' => __('Display "Add to cart" button', 'customizer'),
        ];

        $templateOptions = [
            'title'       => __('Template', 'customizer'),
            'name'        => 'customizer-settings[template]',
            'options'     => $templateValues,
            'default'     => !empty($savedSettings['template']) ? $savedSettings['template'] : 0,
            'class'       => 'chosen_select_nostd customizer-settings-template',
            'description' => __('Customizer look on Frontend', 'customizer'),
        ];

        $multiviewOptions = [
            'title'       => __('Multiview Count', 'customizer'),
            'name'        => 'customizer-settings[multiview]',
            'type'        => 'number',
            'default'     => !empty($savedSettings['multiview']) ? $savedSettings['multiview'] : 1,
            'class'       => 'chosen_select_nostd customizer-settings-multiview',
            'description' => __('You can add any numbers of views', 'customizer'),
            'td_class'    => 'multiview',
            'td_style'    => 'width:25%'
        ];

        $slideLabels = [
            'title'       => __('Labels of sides', 'customizer'),
            'name'        => 'customizer-settings[slide_labels]',
            'type'        => 'text',
            'default'     => !empty($savedSettings['slide_labels']) ? $savedSettings['slide_labels'] : '1,2',
            'class'       => 'chosen_select_nostd customizer-settings-slide_labels',
            'description' => __('Add labels for your sliders comma separated', 'customizer'),
            'td_class'    => 'slide_labels',
            'td_style'    => 'width:25%'

        ];

//        $priceSetting = [
//            'title'       => __('Base price', 'customizer'),
//            'name'        => 'customizer-settings[base_price]',
//            'default'     => !empty($savedSettings['base_price']) ? $savedSettings['base_price'] : '',
//            'description' => __('Will be used product price if empty.', 'customizer'),
//
//        ];

        //Customizer settings

        $html .= $this->get_wrap_html();
        $html .= '<tr>';
        $html .= $this->add_admin_element($templateOptions, 'select');
        $html .= '</tr><tr>';
        $html .= $this->add_admin_element($addToCartButton, 'select');
        $html .= '</tr><tr>';
        if (Customizer_Public::is_woocommerce_enabled()) {
            $html .= $this->add_admin_element($productOptions, 'select');
            $html .= '</tr><tr>';
        }
        $html .= $this->add_admin_element($useProductImageListing, 'select');
        $html .= '</tr><tr>';
        $html .= $this->add_admin_element($useProductImageSingle, 'select');
        $html .= '</tr><tr>';
        $html .= $this->add_admin_element($multiviewOptions, 'number');

        $countView = !empty($savedSettings['multiview']) ? $savedSettings['multiview'] : 1;
        if($countView >1){
            $html .= '</tr><tr>';
            $html .= $this->add_admin_element($slideLabels, 'text');
        }
        for ($slide = 0; $slide < $countView; $slide++) {
            $slideImageOption = [
                'title'       => __('Slide ', 'customizer') . ($slide + 1) . ':',
                'name'        => "customizer-settings[slides][$slide]",
                'type'        => 'image',
                'default'     => !empty($savedSettings['slides'][$slide]) ? $savedSettings['slides'][$slide] : '',
                'class'       => 'customizer-settings-slide',
                'description' => __('Main image for slide ', 'customizer') . ($slide + 1),
                'td_style'    => 'width:25%',
            ];
            $html             .= '</tr><tr>';
            $html             .= $this->add_admin_element($slideImageOption, 'image');
        }
        $html .= '</tr>';
        $html .= $this->get_wrap_html(false);

        $componentId = [
            'title' => __('ID', 'customizer'),
            'name'  => 'component_id',
            'type'  => 'text',
            'class' => 'customizer-component-id',
        ];

        $componentType = [
            'title'    => __('Type', 'customizer'),
            'name'     => 'component_type',
            'type'     => 'select',
            'options'  => [
                Customizer_Public::COMPONENT_TYPE_IMAGE        => __('Image', 'customizer'),
                Customizer_Public::COMPONENT_TYPE_CUSTOM_TEXT  => __('Text', 'customizer'),
                Customizer_Public::COMPONENT_TYPE_CUSTOM_IMAGE => __('Custom Image', 'customizer')
            ],
            'default'  => '',
            'class'    => 'chosen_select_nostd customizer-component-type',
            'td_style' => 'width:70px;'
        ];

        $componentName = [
            'title'       => __('Name', 'customizer'),
            'name'        => 'component_name',
            'type'        => 'text',
            'class'       => 'customizer-component-name',
            'description' => __('Component name', 'customizer'),
        ];

        $componentSortOrder = [
            'title'       => __('Sort Order', 'customizer'),
            'name'        => 'component_sort',
            'type'        => 'hidden',
            'class'       => 'customizer-component-sort',
            'description' => __('Sort Order', 'customizer'),
        ];

        $componentMultiple = [
            'title'    => __('Multiple', 'customizer'),
            'name'     => 'multiple',
            'type'     => 'select',
            'options'  => $yesNoOptions,
            'class'    => 'customizer-multiple',
            'default'  => '0',
            'td_style' => 'width:60px;'
        ];

        $componentImage = [
            'title'       => __('Icon', 'customizer'),
            'name'        => 'component_icon',
            'url_name'    => 'customizer_component_icon',
            'type'        => 'image',
            'description' => __('Component Icon', 'customizer'),
            'td_style'    => 'width:110px;'
        ];

        $componentEnable = [
            'title'    => __('Enable', 'customizer'),
            'name'     => 'component_enable',
            'type'     => 'select',
            'options'  => $yesNoOptions,
            'class'    => 'customizer-enable',
            'default'  => '1',
            'td_style' => 'width:60px;'
        ];

        $componentDescription = [
            'title'       => __('Description', 'customizer'),
            'name'        => 'component_description',
            'type'        => 'textarea',
            'class'       => 'customizer-description',
            'description' => __('Component description', 'customizer'),
            'default'     => '',
        ];

        $componentRadio = [
            'title'       => __('Radio button.', 'customizer'),
            'name'        => 'component_radio',
            'type'        => 'checkbox',
            'class'       => 'customizer-radio-button',
            'td_class'    => 'customizer-radio-button',
            'description' => __('Options as radio buttons', 'customizer'),
            'default'     => '0',
        ];

        $optionId = [
            'title' => __('ID', 'customizer'),
            'name'  => 'option_id',
            'type'  => 'text',
            'class' => 'customizer-option-id'
        ];


        $optionGroup = [
            'title'    => __('Group', 'customizer'),
            'name'     => 'group_name',
            'type'     => 'text',
            'class'    => 'customizer-group-name',
            'td_style' => 'width:100px;',
            'td_class' => 'image_option'
        ];

        $optionName = [
            'title'       => __('Name', 'customizer'),
            'name'        => 'option_name',
            'type'        => 'text',
            'class'       => 'customizer-option-name',
            'description' => __('Option name', 'customizer'),
        ];

        $optionDescription = [
            'title'       => __('Description', 'customizer'),
            'name'        => 'option_description',
            'type'        => 'textarea',
            'class'       => 'customizer-option-description',
            'description' => __('Option description', 'customizer'),
            'td_class'    => 'image_option'
        ];

        $optionPrice = [
            'title'       => __('Price', 'customizer'),
            'name'        => 'option_price',
            'type'        => 'text',
            'class'       => 'customizer-option-price',
            'description' => __('Option Price', 'customizer'),
            'td_style'    => 'width:70px;',
            'price'       => true
        ];

        $optionIcon = [
            'title'       => __('Icon', 'customizer'),
            'name'        => 'option_icon',
            'url_name'    => 'customizer_option_icon',
            'type'        => 'image',
            'set'         => 'Set',
            'remove'      => 'Remove',
            'description' => __('Option Icon', 'customizer'),
            'td_style'    => 'width:110px;',
            'td_class'    => 'image_option'
        ];

        $optionIconBackground = [
            'title'       => __('Icon background', 'customizer'),
            'name'        => 'option_icon_background',
            'type'        => 'colorbox',
            'class'       => 'customizer-option-icon-backround',
            'description' => __('Option icon background', 'customizer'),
            'td_style'    => 'width:60px;',
            'td_class'    => 'image_option'
        ];

        $optionIconText = [
            'title'       => __('Icon text', 'customizer'),
            'name'        => 'option_icon_text',
            'type'        => 'text',
            'class'       => 'customizer-option-icon-text',
            'description' => __('Option icon text', 'customizer'),
            'td_style'    => 'width:100px;',
            'td_class'    => 'image_option'
        ];

        $optionImage = [
            'title'       => __('Image', 'customizer'),
            'name'        => 'option_image',
            'url_name'    => 'customizer_option_image',
            'type'        => 'image',
            'set'         => 'Set',
            'remove'      => 'Remove',
            'class'       => 'customizer-option-image',
            'description' => __('Component Image', 'customizer'),
            'td_style'    => 'width:110px;',
            'td_class'    => 'image_option',
            'multiview'   => !empty($savedSettings['multiview']) ? $savedSettings['multiview'] : 1
        ];

        $optionSortOrder = [
            'title'       => __('Sort Order', 'customizer'),
            'name'        => 'option_sort',
            'type'        => 'hidden',
            'class'       => 'customizer-option-sort',
            'description' => __('Sort Order', 'customizer'),
        ];

        $optionEnable = [
            'title'    => __('Enable', 'customizer'),
            'name'     => 'option_enable',
            'type'     => 'select',
            'options'  => $yesNoOptions,
            'class'    => 'customizer-enable',
            'default'  => '1',
            'td_style' => 'width:50px;'
        ];

        $optionDefault = [
            'title'    => __('Default', 'customizer'),
            'name'     => 'option_default',
            'type'     => 'select',
            'options'  => $yesNoOptions,
            'class'    => 'customizer-default',
            'default'  => '0',
            'td_style' => 'width:50px;',
            'td_class' => 'image_option'
        ];

        $optionAreaTop       = [
            'title'       => __('Top', 'customizer'),
            'name'        => 'option_top',
            'type'        => 'text',
            'class'       => 'customizer-option-top',
            'description' => __('Option top', 'customizer'),
            'td_class'    => 'text_option'
        ];
        $optionAreaLeft      = [
            'title'       => __('Left', 'customizer'),
            'name'        => 'option_left',
            'type'        => 'text',
            'class'       => 'customizer-option-left',
            'description' => __('Option left', 'customizer'),
            'td_class'    => 'text_option'
        ];
        $optionAreaTransform = [
            'title'       => __('Transform degre', 'customizer'),
            'name'        => 'option_transform',
            'type'        => 'text',
            'class'       => 'customizer-option-transform',
            'description' => __('Option Transform degre', 'customizer'),
            'td_class'    => 'text_option'
        ];
        $optionAreaWidth     = [
            'title'       => __('Width', 'customizer'),
            'name'        => 'option_width',
            'type'        => 'text',
            'class'       => 'customizer-option-width',
            'description' => __('Option Width', 'customizer'),
            'td_class'    => 'text_option'
        ];
        $optionAreaHeight    = [
            'title'       => __('Height', 'customizer'),
            'name'        => 'option_height',
            'type'        => 'text',
            'class'       => 'customizer-option-height',
            'description' => __('Option Height', 'customizer'),
            'td_class'    => 'text_option'
        ];
        $optionMaxLength     = [
            'title'       => __('Max length', 'customizer'),
            'name'        => 'option_max_length',
            'type'        => 'text',
            'class'       => 'customizer-option-max-length',
            'description' => __('Max length text', 'customizer'),
            'td_class'    => 'custom_text_option'
        ];
        $optionFontSize      = [
            'title'       => __('Font size', 'customizer'),
            'name'        => 'option_font_size',
            'type'        => 'text',
            'class'       => 'customizer-option-font-size',
            'description' => __('Text font size', 'customizer'),
            'td_class'    => 'custom_text_option'
        ];

        for ($slide = 0; $slide < $countView; $slide++) {
            $slides[$slide] = __('Slide ', 'customizer') . ($slide + 1);
        }
        $optionSlide = [
            'title'       => __('Slide', 'customizer'),
            'name'        => "option_slide",
            'type'        => 'select',
            'options'     => $slides,
            'default'     => !empty($savedSettings['option_slide']) ? $savedSettings['option_slide'] : 0,
            'class'       => 'customizer-settings-slide',
            'description' => __('Number slide', 'customizer'),
            'td_class'    => 'text_option'
        ];

        $optionsFields = [
            'title'        => __('Options', 'customizer'),
            'name'         => 'options',
            'class'        => 'image_options',
            'td_class'     => 'image_options',
            'fields'       => [
                ['type' => 'hidden', 'data' => $optionId],
                ['type' => 'text', 'data' => $optionGroup],
                ['type' => 'text', 'data' => $optionName],
                ['type' => 'number', 'data' => $optionAreaTop],
                ['type' => 'number', 'data' => $optionAreaLeft],
                ['type' => 'number', 'data' => $optionAreaTransform],
                ['type' => 'number', 'data' => $optionAreaWidth],
                ['type' => 'number', 'data' => $optionAreaHeight],
                ['type' => 'textarea', 'data' => $optionDescription],
                ['type' => 'number', 'data' => $optionPrice],
                ['type' => 'image', 'data' => $optionIcon],
                ['type' => 'colorbox', 'data' => $optionIconBackground],
                ['type' => 'text', 'data' => $optionIconText],
                ['type' => 'image', 'data' => $optionImage],
                ['type' => 'select', 'data' => $optionSlide],
                ['type' => 'hidden', 'data' => $optionSortOrder],
                ['type' => 'select', 'data' => $optionEnable],
                ['type' => 'select', 'data' => $optionDefault],
                ['type' => 'number', 'data' => $optionMaxLength],
                ['type' => 'number', 'data' => $optionFontSize]
            ],
            'description'  => __('Customizer options', 'customizer'),
            'row_class'    => 'customizer-option-row',
            'popup_button' => __('Options', 'customizer'),
            'popup_title'  => __('Options', 'customizer'),
            'add_label'    => __('Add option', 'customizer')
        ];

        $components = [
            'title'         => __('Components', 'customizer'),
            'name'          => 'customizer[components]',
            'id'            => 'customizer-components-table',
            'fields'        => [
                ['type' => 'hidden', 'data' => $componentId],
                ['type' => 'select', 'data' => $componentType],
                ['type' => 'text', 'data' => $componentName],
                ['type' => 'textarea', 'data' => $componentDescription],
                ['type' => 'checkbox', 'data' => $componentRadio],
                ['type' => 'select', 'data' => $componentMultiple],
                ['type' => 'image', 'data' => $componentImage],
                ['type' => 'hidden', 'data' => $componentSortOrder],
                ['type' => 'select', 'data' => $componentEnable],
                ['type' => 'popup', 'data' => $optionsFields]
            ],
            'description'   => __('Component options', 'customizer'),
            'class'         => 'striped',
            'add_btn_label' => __('Add component', 'customizer')
        ];

        $html   .= '<br />';
        $values = $this->get_saved_values_for_edit();
        $html   .= $this->add_admin_element_group($components, false, $values);

        $component_row_template = $this->js_row_component($components);
        $component_row_template = preg_replace("/\r|\n/", "", $component_row_template);
        $component_row_template = preg_replace('/\s+/', ' ', $component_row_template);
        $html                   .= '<script>var component_row_template=' . json_encode($component_row_template) . ';</script>';

        $option_row_template = $this->js_row_component($optionsFields, true);
        $option_row_template = preg_replace("/\r|\n/", "", $option_row_template);
        $option_row_template = preg_replace('/\s+/', ' ', $option_row_template);
        $html                .= '<script>var option_row_template=' . json_encode($option_row_template) . ';</script>';
        $upload_url          = wp_get_upload_dir();
        $html                .= '<script>var UPLOAD_URL="' . $upload_url['baseurl'] . '";</script>';
        $html                .= '</div>';
        //component section
        $html .= '<div class="block-form">';
        $html .= '</div>';
        echo $html;
    }

    /**
     * Save customizer options
     *
     * @param $post_id
     */
    public function save_post_customizer($post_id)
    {
        $post = get_post($post_id);
        if ($post->post_type != 'customizer') {
            return;
        }

        if (!wp_is_post_autosave($post_id) && !wp_is_post_revision($post_id)) {
            list($data, $rules, $settings, $error) = $this->prepare_customizer_before_save($post_id);
            if (!$error) {
                update_post_meta($post_id, self::CUSTOMIZER_DATA_KEY, $data);

                if (!empty($settings)) {
                    update_post_meta($post_id, self::CUSTOMIZER_SETTINGS_KEY, $settings);
                } else {
                    delete_post_meta($post_id, self::CUSTOMIZER_SETTINGS_KEY);
                }

                if (!empty($rules)) {
                    update_post_meta($post_id, self::CUSTOMIZER_RULES_KEY, $rules);
                } else {
                    delete_post_meta($post_id, self::CUSTOMIZER_RULES_KEY);
                }
            }
        }
    }

    /**
     * @param $post_id
     *
     * @return array
     */
    public function prepare_customizer_before_save($post_id)
    {
        header('X-XSS-Protection:0');
        $customizerDataJsonSource = !empty($_POST[self::CUSTOMIZER_DATA_JSON_KEY]) ? $_POST[self::CUSTOMIZER_DATA_JSON_KEY] : '';
        $datas                    = [];
        $rules                    = [];
        $settings                 = [];

        if (!empty($customizerDataJsonSource)) {
            $customizerDataJsonSource = str_replace('\"', '"', $customizerDataJsonSource);
            $customizerDataJson       = json_decode($customizerDataJsonSource, true);

            if ($customizerDataJson === null) {
                return array([], [], [], true);
            }

            foreach ($customizerDataJson as $key => $val) {
                if (is_array($val)) {
                    if (isset($val['component_id']) || isset($val['options'])) {
                        $datas[$key] = $val;
                    } elseif (isset($val['scope'])) {
                        $rules[$key] = $val;
                    } else {
                        $settings[$key] = $val;
                    }
                } else {
                    $settings[$key] = $val;
                }
            }
            foreach ($datas as $keyCustomizer => $customizer) {
                if (empty($customizer['component_id'])) {
                    $datas[$keyCustomizer]['component_id'] = $post_id . '_' . $keyCustomizer;
                }

                if (!empty($customizer['options'])) {
                    foreach ($customizer['options'] as $keyOption => $option) {
                        if (empty($option['option_id'])) {
                            $datas[$keyCustomizer]['options'][$keyOption]['option_id'] = $post_id . '_' . $keyCustomizer . '_' . $keyOption;
                        }
                        if (isset($option['option_slide']) && !is_array($option['option_image'])) {
                            $datas[$keyCustomizer]['options'][$keyOption]['option_image'] = [$option['option_image']];
                        }
                    }
                }
            }
        }
        usort($datas, [$this, 'componentSorting']);

        foreach ($datas as $key => $component) {
            if (!empty($component['options'])) {
                $optionsArray = $component['options'];

                usort($optionsArray, [$this, 'optionSorting']);
                $datas[$key]['options'] = $optionsArray;
            }
        }

        return [$datas, $rules, $settings, false];
    }

    protected function componentSorting($item1, $item2)
    {
        if ($item1['component_sort'] == $item2['component_sort']) return 0;
        return ($item1['component_sort'] > $item2['component_sort']) ? 1 : -1;
    }

    protected function optionSorting($item1, $item2)
    {
        if ($item1['option_sort'] == $item2['option_sort']) return 0;
        return ($item1['option_sort'] > $item2['option_sort']) ? 1 : -1;
    }

    /**
     * Block with rules for each customizer
     */
    public function get_rules_block()
    {
        $html = '<div class="block-form">';

        $ruleScope = [
            'title'    => __('Scope', 'customizer'),
            'name'     => 'scope',
            'type'     => 'select',
            'options'  => [
                'option'    => __('Option', 'customizer'),
                'component' => __('Component', 'customizer'),
            ],
            'class'    => 'customizer-rule-scope',
            'default'  => '1',
            'td_style' => 'width:100px;'
        ];

        $availableOption     = $this->getAllOptions();
        $availableComponents = $this->getAllOptions(null, true);

        $ruleIfOption = [
            'title'   => __('IF selected', 'customizer'),
            'name'    => 'trigger',
            'type'    => 'optgroup_select',
            'options' => $availableOption,
            'class'   => 'customizer-rule-trigger',
            'default' => ''
        ];

        $ruleTargetComponent = [
            'title'   => __('Apply on', 'customizer'),
            'name'    => 'target',
            'type'    => 'select',
            'options' => $availableComponents,
            'class'   => 'customizer-rule-target-component',
            'default' => ''
        ];

        $ruleTargetOption = [
            'title'   => '',
            'name'    => 'target',
            'type'    => 'optgroup_select',
            'options' => $availableOption,
            'class'   => 'customizer-rule-target-option',
            'default' => ''
        ];


        $ruleAction = [
            'title'    => __('Action', 'customizer'),
            'name'     => 'action',
            'type'     => 'select',
            'options'  => [
                'show'   => __('Show', 'customizer'),
                'hide'   => __('Hide', 'customizer'),
                'select' => __('Select', 'customizer')
            ],
            'class'    => 'customizer-rule-action',
            'default'  => '1',
            'td_style' => 'width:100px;'
        ];

        $ruleReverse = [
            'title' => __('Reverse Rule', 'customizer'),
            'name'  => 'reverse',
            'type'  => 'checkbox',
            'class' => 'customizer-rule-reverse'
        ];

        $ruleEnable = [
            'title'    => __('Enable', 'customizer'),
            'name'     => 'enable',
            'type'     => 'select',
            'options'  => [
                0 => __('No', 'customizer'),
                1 => __('Yes', 'customizer')
            ],
            'class'    => 'customizer-rule-enable',
            'default'  => '1',
            'td_style' => 'width:50px;'
        ];


        $rules = [
            'title'         => __('Rules', 'customizer'),
            'name'          => self::CUSTOMIZER_RULES_KEY,
            'id'            => 'customizer-rules-table',
            'fields'        => [
                ['type' => 'select', 'data' => $ruleScope],
                ['type' => 'optgroup_select', 'data' => $ruleIfOption],
                ['type' => 'select', 'data' => $ruleAction],
                ['type' => 'select', 'data' => $ruleTargetComponent],
                ['type' => 'optgroup_select', 'data' => $ruleTargetOption],
                ['type' => 'checkbox', 'data' => $ruleReverse],
                ['type' => 'select', 'data' => $ruleEnable]
            ],
            'description'   => __('Component options', 'customizer'),
            'class'         => 'striped',
            'add_btn_label' => __('Add Rule', 'customizer'),
            'rules'         => true
        ];

        //Customizer Rules

        $html       .= '<br />';
        $savedRules = $this->get_saved_rules_for_edit();

        $html .= $this->add_admin_element_group($rules, false, $savedRules);

        $rule_row_template = $this->js_row_component($rules);
        $rule_row_template = preg_replace("/\r|\n/", "", $rule_row_template);
        $rule_row_template = preg_replace('/\s+/', ' ', $rule_row_template);
        $html              .= '<script>var rule_row_template=' . json_encode($rule_row_template) . ';</script>';

        $html .= '</div>';

        echo $html;

    }

    /**
     * @return array
     */
    public function get_all_products()
    {
        $result = [
            '' => __('Select Product', 'customizer')
        ];

        if (Customizer_Public::is_woocommerce_enabled()) {
            $args = ['post_type' => 'product', 'posts_per_page' => 999999];

            $posts = get_posts($args);
            foreach ($posts as $post) {
                $product = wc_get_product($post->ID);
                if ($product && $product->is_type('simple')) {
                    $result[$post->ID] = $post->post_name;
                }
            }
        }

        return $result;
    }

    /**
     * @param $image
     * @param $item_id
     * @param $item
     *
     * @return string
     */
    public function get_thumbnail_customizer($image, $item_id, $item)
    {
        $data = Customizer_Public::get_customizer_data_from_item($item);
        if (empty($data)) {
            return $image;
        }

        return Customizer_Public::generateImage($item_id, $item);
    }

    /**
     * @param $keys
     *
     * @return array
     */
    public function get_hidden_order_meta($keys)
    {
        $keys[] = 'customizer_id';

        return $keys;
    }

    /**
     * @param $items
     * @param $order
     *
     * @return mixed
     */
    public function order_get_items($items, $order)
    {
        foreach ($items as $id => $_item) {
            if (!empty($_item['customizer_id'])) {
                $customizerData     = get_post($_item['customizer_id']);
                $items[$id]['name'] = !empty($customizerData) ? $customizerData->post_title : $items[$id]['name'];
            }
        }

        return $items;
    }

    /**
     * @param $item_id
     * @param $item
     * @param $_product
     */
    public function get_admin_order_item_render($item_id, $item, $_product)
    {
        $html = '';

        $data = Customizer_Public::get_customizer_data_from_item($item);
        if (empty($data)) {
            return;
        }

        $html .= '<div class="customizer_meta">';
        //generate image
        $imageHtml = Customizer_Public::generateImage($item_id, $item);
        $html      .= $imageHtml;
        $html      .= Customizer_Public::get_customizer_options_formatted($data, true, true, true);
        $html      .= '</div>';

        echo $html;
    }

    /**
     * @param bool $start
     *
     * @return string
     */
    public function get_wrap_html($start = true)
    {
        if (!$start) {
            return '</tbody></table></div></div></div>';
        }

        return '<div class="customizer-wrap">' .
            '<div id="" class="o-metabox-container"><div class="block-form">' .
            '<table class="wp-list-table widefat fixed pages o-root"><tbody>';
    }

    /**
     * @param              $data
     * @param              $type
     * @param string|array $selected_value
     * @param boolean      $showLabel
     * @param int          $parent_id
     *
     * @return string
     */
    public function add_admin_element($data, $type, $selected_value = '', $showLabel = true, $parent_id = null)
    {
        $data           = $this->prepare_element_data($data);
        $selected_value = !empty($selected_value) || ($selected_value === '0') ? $selected_value : $data['default'];
        $html           = '';

        if ($type === 'hidden') {
            $showLabel = false;
        }
        if (!$showLabel && $type !== 'hidden') {
            $html .= '<td class="' . $data['td_class'] . '">';
        }
        if ($showLabel) {
            $html .= '<td class="label">' . $data['title'];
            if (!empty($data['description'])) {
                $html .= '<div class="acd-desc">' . $data['description'] . '</div>';
            }
            $html .= '</td><td>';
        }

        switch ($type) {
            case 'text':
            case 'number':
            case 'hidden':
                $html .= '<input name="' . esc_attr($data['name']) . '" id="' . esc_attr($data['id']) . '"' .
                    ' type="' . $type . '"';
                if ($type == 'number') {
                    $selected_value = (float)$selected_value;
                    if (!empty($data['price'])) {
                        $html .= ' step=".01"';
                    }
                }
                if ($data['td_class'] == 'multiview') {
                    $html .= ' min="1"';
                }
                $html .= ' value="' . esc_attr($selected_value) . '"' . ' 
	                    class="' . esc_attr($data['class']) . '" ';
                if ($type == 'number') {
                    $html .= ' style="width:80px;" ';
                } else {
                    $html .= (!empty($data['td_style'])) ? ' style="' . $data['td_style'] . '" />' : ' style="width:100%;" />';
                }

                break;
            case 'textarea':

                $html .= '<textarea name="' . esc_attr($data['name']) . '"' .
                    ' id="' . esc_attr($data['id']) . '" style="' . esc_attr($data['css']) . '"' .
                    ' class="' . esc_attr($data['class']) . '">' . esc_html($selected_value) . '</textarea>';
                break;
            case 'optgroup_select':
                $html .= '<select name="' . esc_attr($data['name']) . '" id="' .
                    esc_attr($data['id']) . '"' .
                    'class="' . esc_attr($data['class']) . '">';

                foreach ($data['options'] as $group) {
                    $html .= '<optgroup label="' . $group['title'] . '">';
                    if (!empty($group['options'])) {
                        foreach ($group['options'] as $key => $val) {
                            $html .= '<option value="' . esc_attr($key) . '"';
                            $html .= selected($selected_value, $key, false);
                            $html .= '>' . $val . '</option>';
                        }
                    }
                    $html .= '</optgroup>';
                }
                $html .= '</select>';
                break;
            case 'select':
                $html .= '<select name="' . esc_attr($data['name']) . '" id="' .
                    esc_attr($data['id']) . '"' .
                    'class="' . esc_attr($data['class']) . '">';

                foreach ($data['options'] as $key => $val) {
                    $html .= '<option value="' . esc_attr($key) . '"';
                    $html .= selected($selected_value, $key, false);
                    $html .= '>' . $val . '</option>';

                }
                $html .= '</select>';
                break;
            case 'checkbox':
                $html .= '<input name="' . esc_attr($data['name']) . '" id="' .
                    esc_attr($data['id']) . '"' .
                    'class="' . esc_attr($data['class']) . '"' .
                    ' type="checkbox" value="1" ' . ($selected_value ? 'checked="checked"' : '') . ' />';
                break;
            case 'colorbox':
                $value = !empty($selected_value) ? $selected_value : '000';
                $html  .= '<input name="' . esc_attr($data['name']) . '" id="' .
                    esc_attr($data['id']) . '"' .
                    'class="jscolor"' .
                    ' value="' . $value . '" style="' . $data['td_style'] . '" />';
                break;
            case 'image' :
                $slides = !empty($data['multiview']) ? $data['multiview'] : 1;
                for ($slide = 0; $slide < $slides; $slide++) {
                    $slide_name = '';
                    if (is_array($selected_value) || ($slides > 1)) {
                        $value      = !empty($selected_value[$slide]) ? $selected_value[$slide] : '';
                        $slide_name = '[' . $slide . ']';
                    } else $value = $selected_value;
                    $html .= '<div class="' . $data["class"] . '">' .
                        '<button class="button add-image">' . __('Add', 'customizer') . '</button>' .
                        '<button class="button delete-image">' . __('Delete', 'customizer') . '</button>' .
                        '<input type="hidden" name="' . $data["name"] . $slide_name . '" value="' . $value . '">';

                    $html .= '<div class="image-preview">';
                    if ($value) {
                        $html .= '<img src="' . Customizer_Public::get_image_full_url($value) . '" />';
                    }
                    $html .= '</div></div>';
                }
                break;

            case 'popup':
                $html .= $this->add_admin_element_group($data, true, $selected_value, $parent_id);
                break;
            default:
                $html .= '';
                break;
        }
        if ($type != 'hidden') {
            $html .= '</td>';
        }

        return $html;
    }

    /**
     * @param $element
     *
     * @return mixed
     */
    public function prepare_element_data($element)
    {
        $availableFields = [
            'name',
            'id',
            'class',
            'default',
            'css',
            'description',
            'title',
            'td_style',
            'td_class',
            'row_class'
        ];

        foreach ($availableFields as $key) {
            if (!isset($element[$key])) {
                $element[$key] = '';
            }
        }

        return $element;
    }

    /**
     * @param         $data
     * @param boolean $popup
     * @param array   $saved_values
     * @param int     $parent_id
     *
     * @return string
     */

    public function add_admin_element_group($data, $popup = false, $saved_values = [], $parent_id = null)
    {
        if (empty($data)) {
            return '';
        }
        $isRule = false;
        if (!empty($data['rules'])) {
            $isRule = true;
        }

        $data = $this->prepare_element_data($data);

        $html = '';

        if ($popup) {
            add_thickbox();
            $popup_id = uniqid("customizer-modal-");
            $html     .= "<a class='customizer-modal-trigger button button-primary button-large {$data['class']}' data-toggle='customizer-modal' data-target='#$popup_id' data-modalid='$popup_id'>{$data["popup_title"]}</a>";
            $html     .= '<div class="customizerModal fade customizer-modal" id="' . $popup_id . '" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="customizerModal-dialog">
                                          <div class="customizerModal-content">
                                            <div class="customizerModal-header">
                                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                              <h4 class="customizerModal-title" id="myModalLabel' . $popup_id . '">' . $data["popup_title"] . '</h4>
                                            </div>
                                            <div class="customizerModal-body">';
        }

        $html .= '<table id="' . $data["id"] . '"' .
            'class="' . esc_attr($data['class']) . ' wp-list-table widefat fixed striped">';
        $html .= '<thead><tr>';

        if (!empty($data['fields'])) {
            foreach ($data["fields"] as $field) {
                $elementType = !empty($field['type']) ? $field['type'] : 'text';

                if ($elementType == 'hidden') {
                    continue;
                }
                $elementData = $field['data'];
                $elementData = $this->prepare_element_data($elementData);

                $html .= '<th style="' . $elementData['td_style'] . '" class="' . $elementData['td_class'] . '">' . $elementData['title'] . '</th>';
            }
        }

        $html .= '<td style="width: 20px;"></td>';
        $html .= '</tr></thead><tr>';

        if (!empty($saved_values)) {
            foreach ($saved_values as $i => $component) {

                $html .= '<tr class="' . $data['row_class'] . '">';

                if (!empty($data['fields'])) {
                    foreach ($data["fields"] as $field) {

                        $elementData = $field['data'];
                        $elementType = !empty($field['type']) ? $field['type'] : 'text';

                        $name                = isset($elementData['name']) ? $elementData['name'] : '';
                        $default_field_value = isset($component[$name]) ? $component[$name] : '';

                        if ($popup) {
                            $name = ($isRule ? self::CUSTOMIZER_RULES_KEY : self::CUSTOMIZER_DATA_KEY) . "[$parent_id][options][$i][" . $name . "]";
                        } else {
                            $name = ($isRule ? self::CUSTOMIZER_RULES_KEY : self::CUSTOMIZER_DATA_KEY) . "[$i][" . $name . "]";
                        }
                        $elementData['name'] = $name;

                        if ($elementType == 'popup') {
                            $parent_id = ($elementType == 'popup') ? $i : '';
                        }

                        $html .= $this->add_admin_element($elementData, $elementType, $default_field_value, false,
                            $parent_id);
                    }
                    $html .= '<td>';
                    if ($popup) {
                        $html .= '<a class="remove_option">';
                    } else {
                        $html .= '<a class="remove_component">';
                    }
                    $html .= '<span class="dashicons dashicons-no-alt"></span></a>';
                    $html .= '</td></tr>';
                }
            }

        }

        $html .= '</tbody></table><br />';
        if ($isRule) {
            if ($popup) {

            } else {
                $html .= '<a class="button add_rule_button">' .
                    __('Add Rule', 'customizer') . '</a>';
            }
        } else {
            if ($popup) {
                $string_parent_id = $parent_id ? $parent_id : '{{id}}';
                $additional_class = !empty($data['class']) ? $data['class'] : '';
                $html             .= '<a class="button add_option_button ' . $additional_class . '" data-component="' . $string_parent_id . '">' .
                    $data['add_label'] . '</a>&nbsp;' .
                    '<a class="button close" data-dismiss="modal" aria-hidden="true">' .
                    __('Close', 'customizer') . '</a>';
            } else {
                $html .= '<a class="button add_component_button">' .
                    __('Add Component', 'customizer') . '</a>';
            }
        }

        if ($popup) {
            $html .= '</div></div></div></div>';
        }


        return $html;
    }

    /**
     * @return array|mixed
     */
    public function get_saved_values_for_edit()
    {
        $data = Customizer_Public::get_customizer_meta();
        $data = !empty($data[self::CUSTOMIZER_DATA_KEY]) ? $data[self::CUSTOMIZER_DATA_KEY] : [];

        return $data;
    }

    /**
     * @return array|mixed
     */
    public static function get_saved_rules_for_edit()
    {
        $data = Customizer_Public::get_customizer_meta();
        $data = !empty($data[self::CUSTOMIZER_RULES_KEY]) ? $data[self::CUSTOMIZER_RULES_KEY] : [];

        return $data;
    }

    /**
     * @param         $data
     * @param boolean $options
     *
     * @return string
     */

    public function js_row_component($data, $options = false)
    {
        $isRule = false;
        if (!empty($data['rules'])) {
            $isRule = true;
        }

        $html = '<tr class="component-row">';
        if (!empty($data['fields'])) {
            foreach ($data['fields'] as $field) {
                $type  = $field['type'];
                $field = $field['data'];
                if ($options) {
                    $field['name'] = ($isRule ? self::CUSTOMIZER_RULES_KEY : self::CUSTOMIZER_DATA_KEY) . '[{{component_id}}][options][{{id}}][' . $field['name'] . ']';
                } else {
                    $field['name'] = ($isRule ? self::CUSTOMIZER_RULES_KEY : self::CUSTOMIZER_DATA_KEY) . '[{{id}}][' . $field['name'] . ']';
                }

                $html .= $this->add_admin_element($field, $type, '', false);
            }
        }
        $html .= '<td><a class="remove_component"><span class="dashicons dashicons-no-alt"></span></a></td></tr>';

        return $html;
    }

    /**
     * @param $id
     */
    public function delete_custom_image($id)
    {
        //TODO add check time to delete user files
        $order      = wc_get_order($id);
        $new_status = $_POST['order_status'];
        if ($new_status == 'wc-completed') {
            foreach ($order->get_items() as $item_id => $item) {
                $customizer_components = unserialize(wc_get_order_item_meta($item_id, 'customizer'));
                foreach ($customizer_components as $component) {
                    foreach ($component as $option) {
                        if ($option['component_type'] == 'custom_image') {
                            $fileName = $option['custom_image'];
                            $path     = WP_CONTENT_DIR . '/uploads/customizer/custom_images/';
                            if (file_exists($path . $fileName)) {
                                unlink($path . $fileName);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param null    $customizer_id
     * @param boolean $components_only
     *
     * @return array
     */
    public function getAllOptions($customizer_id = null, $components_only = false)
    {
        $result = [];
        $data   = Customizer_Public::get_customizer_meta($customizer_id);
        $data   = !empty($data[self::CUSTOMIZER_DATA_KEY]) ? $data[self::CUSTOMIZER_DATA_KEY] : [];

        if (empty($data)) {
            return [];
        }

        foreach ($data as $component) {
            if ($components_only) {
                $result[$component['component_id']] = $component['component_name'];
            } else {
                $options = [];
                if (
                !empty($component['options'])
                ) {
                    foreach ($component['options'] as $option) {
                        $options[$option['option_id']] = $component['component_name'] . ' > ' . $option['option_name'];
                    }
                }
                $result[] = [
                    'title'   => $component['component_name'],
                    'options' => $options
                ];
            }
        }

        return $result;
    }

    /**
     * DB Upgrades
     */
    public function db_upgrade()
    {
        global $wpdb;

        //create table for saving user's customizers
        $table_name = $wpdb->prefix . Customizer_Public::SAVE_TABLE_NAME;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            //table not in database. Create new table
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "
                    CREATE TABLE {$table_name} (
                      id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                      customizer_id INT UNSIGNED NOT NULL,
                      user_id INT,
                      name_customizer VARCHAR(255),
                      selected_options TEXT,
                      hash TEXT,
                      public VARCHAR(1) DEFAULT '1',
                      count_view BIGINT DEFAULT 0,
                      deleted SMALLINT DEFAULT 0,
                      created_at INT NOT NULL,
                      updated_at INT NOT NULL,
                      UNIQUE KEY id (id)
                    ) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        //create table for saving user's custom images
        $table_name = $wpdb->prefix . Customizer_Public::SAVE_TABLE_CUSTOM_IMAGE_NAME;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            //table not in database. Create new table
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "
                    CREATE TABLE {$table_name} (
                      id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                      image MEDIUMBLOB,
                      UNIQUE KEY id (id)
                    ) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    private function removeDirectory($path)
    {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? self::removeDirectory($file) : unlink($file);
        }
        rmdir($path);
        return;
    }

    public function add_mime_types($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    public function before_delete_customizer($postid)
    {
        if (get_post_type($postid) === 'customizer') {
            global $wpdb;
            $name_table = $wpdb->prefix . Customizer_Public::SAVE_TABLE_NAME;
            $wpdb->query("DELETE FROM {$name_table}  WHERE customizer_id = {$postid}");
        }
    }

    public function fix_old_image_url()
    {
        $converted   = get_option('customizer_old_url_converted');
        $customizers = get_posts([
            'post_type'   => 'customizer',
            'numberposts' => -1
        ]);
        foreach ($customizers as $customizer) {
            $meta = get_post_meta($customizer->ID, 'customizer');
            foreach ($meta[0] as $key => $component) {
                $meta[0][$key]['component_icon'] = self::convert_old_url($component['component_icon']);
                if (!empty($component['options'])) {
                    foreach ($component['options'] as $key_opt => $option) {
                        $meta[0][$key]['options'][$key_opt]['option_icon'] = self::convert_old_url($option['option_icon']);
                        if (is_array($meta[0][$key]['options'][$key_opt]['option_image'])) {
                            foreach ($meta[0][$key]['options'][$key_opt]['option_image'] as $key_image => $image) {
                                $meta[0][$key]['options'][$key_opt]['option_image'][$key_image] = self::convert_old_url($image);
                            }
                        } else {
                            $meta[0][$key]['options'][$key_opt]['option_image'] = self::convert_old_url($option['option_image']);
                        }
                    }
                }
            }
            update_post_meta($customizer->ID, 'customizer', $meta[0]);
        }
        update_option('customizer_old_url_converted', true);
    }

    private function convert_old_url($url)
    {
        if (!empty($url)) {
            if ($url[0] !== '/') {
                $parsed_url = parse_url($url);
                $home_url   = parse_url(get_option('siteurl'));
                if ($parsed_url['host'] === $home_url['host']) {
                    $url = $parsed_url['path'];
                    $url = str_replace('/wp-content/uploads', '', $url);
                }
            } elseif (stripos($url, '/wp-content/uploads') === 0) {
                $url = str_replace('/wp-content/uploads', '', $url);
            }
        }
        return $url;
    }

    /**
     * @param $exportPath
     * @param $upload_dir
     * @return array
     */
    private function get_export_image_path($exportPath, $upload_dir)
    {
        $icon = Customizer_Public::get_image_full_url($exportPath);
        if ($icon === $exportPath) {
            $zip_field = [
                'real_name' => $icon,
                'zip_name'  => ''
            ];
            $path      = $icon;
        } else {
            $path      = pathinfo(str_replace($upload_dir['baseurl'], '', $icon));
            $file_name = $path['basename'];
            $zip_field = [
                'real_name' => $upload_dir['basedir'] . $path['dirname'] . '/' . $file_name,
                'zip_name'  => $path['dirname'] . '/' . $file_name
            ];
            $path      = $path['dirname'] . '/' . $file_name;
        }
        return ['field' => $zip_field, 'path' => $path];
    }

    /**
     * @param $request_url
     * @return mixed|null
     */
    public static function get_content_by_url($request_url)
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $request_url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        //$http_code = curl_getinfo($curl_handle);

        $JsonResponse = curl_exec($curl_handle);
        return ($JsonResponse);
    }

    public static function add_customizer_roles()
    {
        $role = get_role('administrator');
        if (!empty($role)) {
            foreach (self::USER_CAPS as $cap) {
                if (!$role->has_cap($cap)) $role->add_cap($cap);
            }
        }
    }

    public static function remove_customizer_roles()
    {
        $role = get_role('administrator');
        if (!empty($role)) {
            foreach (self::USER_CAPS as $cap) {
                $role->remove_cap($cap);
            }
        }
    }
}
