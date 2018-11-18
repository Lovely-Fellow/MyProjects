<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link              http://k2-service.com/shop/product-customizer/
 * @author            K2-Service <plugins@k2-service.com>
 *
 * @package           Customizer
 * @subpackage        Customizer/public
 */
class Customizer_Public
{

    const COMPONENT_TYPE_IMAGE = 'image';

    const COMPONENT_TYPE_CUSTOM_IMAGE = 'custom_image';

    const COMPONENT_TYPE_CUSTOM_TEXT = 'custom_text';

    const SAVE_TABLE_NAME              = 'customizer';
    const SAVE_TABLE_CUSTOM_IMAGE_NAME = 'customizer_custom_image';
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /** @var */
    protected $widget_customizer_id;

    /** @var */
    protected $widget_old_post_id;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        add_shortcode('customizer_widget', array($this, 'get_customizer_widget'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/customizer.css', [], $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_grid', plugin_dir_url(__FILE__) . 'css/grid.min.css', [], $this->version, 'all');
        $this->get_scripts_by_template(false);

        // get fonts css
        $fonts = get_option('customizer_fonts', array());
        foreach ($fonts as $name) {
            wp_enqueue_style('google-fonts-' . urlencode($name), '//fonts.googleapis.com/css?family=' . urlencode($name), false);
        }

        //get social buttons css
        $general_options = get_option('customizer-options');

        $social_button_css = !empty($general_options['social_button_css']) ? $general_options['social_button_css'] : 0;
        switch ($social_button_css) {
            case '0' :
                wp_enqueue_style($this->plugin_name . '_fontawesome', '//use.fontawesome.com/releases/v5.0.6/css/all.css', [], $this->version, 'all');
                break;
            case '1' :
                wp_enqueue_style($this->plugin_name . '_fontawesome', plugin_dir_url(__FILE__) . 'css/fontawesome/css/fontawesome-all.min.css', [], $this->version, 'all');
                break;
            default:
                break;
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     */
    public function enqueue_scripts()
    {
        $this->get_scripts_by_template();
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/customizer.js', ['jquery'], $this->version, true);
    }

    /**
     * @param $template
     *
     * @return string
     */
    public function switch_template($template)
    {
        $templatesPath = plugin_dir_path(__FILE__) . '../public/partials/';
        if (is_array($template)) {
            $post_type = get_post_type($template['widget']);
            $post_id   = $template['widget'];
        } else {
            $post_type = get_post_type();
            $post_id   = get_the_ID();
        }
        if (is_archive() && $post_type === 'customizer') {
            return $templatesPath . 'archive/customizer.php';
        } else if ($post_type == 'customizer') {
            if (!empty($_GET['print'])) {
                return $templatesPath . 'form/print_page.php';
            }
            $newTemplate = get_post_meta($post_id, 'customizer-settings');
            if (empty($newTemplate[0]['template']) || (wp_is_mobile() && $newTemplate[0]['template'] == 'customizer-template-VerticalRightSidebar.php')) {
                return $templatesPath . 'customizer-template-VerticalLeftSidebar.php';
            } else {
                $newTemplate   = $newTemplate[0]['template'];
                $newTemplate   = str_replace(['ModernDark', 'ModernLight'], 'Modern', $newTemplate);
                $templatesPath = apply_filters('set_background_template', $templatesPath, $newTemplate);
                if (!file_exists($templatesPath . $newTemplate)) {
                    return $templatesPath . 'customizer-template-VerticalLeftSidebar.php';
                }

                return $templatesPath . $newTemplate;
            }
        }

        return $template;
    }

    /**
     * @param bool $js
     * @param bool $return_path_only
     *
     * @return string|bool
     */
    public function get_scripts_by_template($js = true, $return_path_only = false, $customizer_id = null)
    {
        $versionMap = [
            'customizer-template-HorizontalRightSideBar.php' => 'horizontal',
            'customizer-template-HorizontalLeftSideBar.php'  => 'horizontal',
            'customizer-template-VerticalLeftSidebar.php'    => 'vertical',
            'customizer-template-VerticalRightSidebar.php'   => 'vertical',
            'customizer-template-ModernDark.php'             => 'modern-dark',
            'customizer-template-ModernLight.php'            => 'modern-light',
        ];
        if (!empty($customizer_id)) {
            $post_type = get_post_type($customizer_id);
            $post_id   = $customizer_id;
        } else {
            $post_type = get_post_type();
            $post_id   = get_the_ID();
        }

        if (!is_archive() && $post_type === 'customizer') {
            $template = get_post_meta($post_id, 'customizer-settings');
            $template = !empty($template[0]['template']) ? $template[0]['template'] : 'customizer-template-VerticalLeftSidebar.php';

            if (empty($versionMap[$template])) {
                return false;
            }

            if ($js) {
                $js_file_name = (strpos($versionMap[$template], 'modern') === false) ? $versionMap[$template] : 'modern';
                if ($return_path_only) {
                    return plugin_dir_url(__FILE__) . 'js/template/' . $js_file_name . '.js';
                }
                if ($js_file_name === 'modern') {
                    wp_enqueue_script($this->plugin_name . '_magnific_popup', '//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.js', ['jquery'], $this->version, true);
                    wp_enqueue_script($this->plugin_name . '_custom_scroll', '//cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.js', ['jquery'], $this->version, true);
                }
                wp_enqueue_script($this->plugin_name . '_filter_template', plugin_dir_url(__FILE__) . 'js/template/' . $js_file_name . '.js', ['jquery'], $this->version, true);
            } else {
                if ($return_path_only) {
                    return plugin_dir_url(__FILE__) . 'css/template/' . $versionMap[$template] . '.css';
                }

                if (strpos($versionMap[$template], 'modern') !== false) {
                    wp_enqueue_style($this->plugin_name . '_magnific_popup', '//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css', [], $this->version, false);
                    wp_enqueue_style($this->plugin_name . '_custom_scroll', '//cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.css', [], $this->version, false);
                }
                wp_enqueue_style($this->plugin_name . '_filter_template', plugin_dir_url(__FILE__) . 'css/template/' . $versionMap[$template] . '.css', [], $this->version, false);
            }
        }
    }

    /**
     * @param null $customizer_id
     *
     * @return null
     */
    public static function get_product_id($customizer_id = null)
    {
        $customizer_id = !empty($customizer_id) ? $customizer_id : get_the_ID();
        $settings      = get_post_meta($customizer_id, 'customizer-settings');

        return !empty($settings[0]['product_id']) ? $settings[0]['product_id'] : null;
    }

    /**
     * @return array|null|\WP_Post
     */
    public static function get_product_data()
    {
        return get_post(self::get_product_id());
    }

    /**
     * @return mixed
     */
    public static function get_product_meta()
    {
        return get_post_meta(self::get_product_id());
    }

    /**
     * @return false|int
     */
    public static function get_customizer_id()
    {
        return get_the_ID();
    }

    /**
     * @param $product_id
     *
     * @return bool
     */
    public static function get_customizer_by_product($product_id)
    {
        $customizerList = get_posts(['numberposts' => -1, 'post_type' => 'customizer']);
        foreach ($customizerList as $customizer) {
            $meta = self::get_customizer_meta($customizer->ID);
            if (!empty($meta['customizer-settings']['product_id']) &&
                $meta['customizer-settings']['product_id'] == $product_id
            ) {
                return $customizer;
            }
        }

        return false;
    }

    /**
     * @return array|null|\WP_Post
     */
    public static function get_customizer_data()
    {
        return get_post(self::get_customizer_id());
    }

    /**
     * @param null $customizer_id
     *
     * @return mixed
     */
    public static function get_customizer_meta($customizer_id = null)
    {
        $customizer_id = empty($customizer_id) ? self::get_customizer_id() : $customizer_id;
        $meta          = get_post_meta($customizer_id);

        if (!empty($meta[Customizer_Admin::CUSTOMIZER_DATA_KEY])) {
            $meta[Customizer_Admin::CUSTOMIZER_DATA_KEY] = self::prepare_meta_customizer($meta[Customizer_Admin::CUSTOMIZER_DATA_KEY]);
        }

        if (!empty($meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY][0])) {
            $meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY] = unserialize($meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY][0]);
        } elseif (!empty($meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY]) && is_string($meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY])) {
            $meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY] = unserialize($meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY]);
        }

        if (!empty($meta[Customizer_Admin::CUSTOMIZER_RULES_KEY][0])) {
            $meta[Customizer_Admin::CUSTOMIZER_RULES_KEY] = unserialize($meta[Customizer_Admin::CUSTOMIZER_RULES_KEY][0]);
        } elseif (!empty($meta[Customizer_Admin::CUSTOMIZER_RULES_KEY]) && is_string($meta[Customizer_Admin::CUSTOMIZER_RULES_KEY])) {
            $meta[Customizer_Admin::CUSTOMIZER_RULES_KEY] = unserialize($meta[Customizer_Admin::CUSTOMIZER_RULES_KEY]);
        }

        if (!empty($_GET['hash'])) {
            $options   = self::get_saved_customizer($_GET['hash']);
            $curr_user = get_current_user_id();
            if (!empty($options) && ($options->user_id == $curr_user || ((bool)$options->public && !(bool)$options->deleted))) {
                $meta['saved_options'] = $options->selected_options;
            } else {
                wp_redirect('/customizer/');
                exit;
            }
        }

        return $meta;
    }

    /**
     * @param null $customizer_id
     *
     * @return array
     */
    public static function get_customizer_settings($customizer_id = null)
    {
        $meta = self::get_customizer_meta($customizer_id);
        if (!empty($meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY])) {
            return $meta[Customizer_Admin::CUSTOMIZER_SETTINGS_KEY];
        }

        return [];
    }

    /**
     * @param $components
     *
     * @return array
     */
    public static function prepare_meta_customizer($components)
    {
        if (!empty($components[0]) && !is_array($components[0])) {
            $components = unserialize($components[0]);
        }

        if (empty($components)) {
            return [];
        }
        foreach ($components as $componentKey => $component) {
            $groups = [];

            if (!empty($component['options'])) {
                foreach ($component['options'] as $optionKey => $option) {
                    if (!isset($option['group_name'])) {
                        $option['group_name'] = '';
                    }
                    $groups[$option['group_name']][$option['option_id']] = $option;

                }
            }
            $components[$componentKey]['groups'] = $groups;
        }

        return $components;
    }

    /**
     *
     */
    public function add_to_cart()
    {
        if (!empty($_POST['customizer-save']) || !empty($_POST['customizer-update'])) {
            $save = $_POST['save_customizer'];
            unset($_POST['save_customizer']);
            $hash           = $this->save_customizer($save, $_POST['customizer_selected_options']);
            $_GET['hash']   = $hash;
            $customizer_url = self::get_customizer_link(self::get_customizer_id(), $_GET['hash']);
            wp_redirect($customizer_url);
            exit();

        } else if (isset($_POST['add_to_cart']) && wp_verify_nonce($_POST['nonce'], 'add_to_cart')) {
            global $woocommerce;

            unset ($_POST['nonce']);
            unset ($_POST['add_to_cart']);
            $hash = !empty($_POST['save_customizer']['hash']) ? $_POST['save_customizer']['hash'] : '';
            if (empty($_POST['save_customizer']['id'])) {
                $settings = [
                    'name_customizer' => 'customizer_' . time(),
                    'hash'            => '',
                    'user_id'         => is_user_logged_in() ? get_current_user_id() : 0,
                    'times'           => time() . ':' . time(),
                    'public'          => '1',
                ];
                $hash     = $this->save_customizer($settings, $_POST['customizer_selected_options']);
            }
            $_GET['hash'] = $hash;

            $orderedOptions = !empty($_POST['customizer_selected_options']) ? $_POST['customizer_selected_options'] : '';

            $resultOptions = $this->validateSelectedOptions($orderedOptions);
            $attributes    = [
                'customizer_id' => self::get_customizer_id(),
                'customizer'    => serialize($resultOptions)
            ];

            /** @var WC_Cart $woocommerce ->cart */
            $woocommerce->cart->add_to_cart(self::get_product_id(), 1, 0, $attributes);
            $cart_url = get_permalink(wc_get_page_id('cart'));
            wp_redirect($cart_url);

            exit();
        }
    }

    /**
     * @param $selectedOptions
     *
     * @return array
     */
    public static function validateSelectedOptions($selectedOptions)
    {
        $resultOptions = [];

        if (is_string($selectedOptions)) {
            $selectedOptions = str_replace('\"', '"', $selectedOptions);
            $selectedOptions = json_decode($selectedOptions, true);
        }

        $customizerOptions = self::get_customizer_meta();
        $customizerOptions = $customizerOptions['customizer'];
        if (empty($customizerOptions)) {
            return $resultOptions;
        }

        // parse type 'image'
        $imageOptions = !empty($selectedOptions['options']) ? $selectedOptions['options'] : [];
        foreach ($imageOptions as $sOption) {
            $params = self::setDefaultParams($sOption, $customizerOptions);
            if (!empty($params)) {
                $resultOptions[$params['component_id']][] = $params;
            }
        }

        // parse type 'custom_text' & 'custom_image'
        $textOptions = !empty($selectedOptions['text_options']) ? $selectedOptions['text_options'] : [];
        foreach ($textOptions as $sOption) {
            $params                 = self::setDefaultParams($sOption['option_id'], $customizerOptions);
            $params['option_slide'] = !empty($sOption['slide']) ? $sOption['slide'] : '0';
            if (!empty($sOption['type']) && $sOption['type'] == self::COMPONENT_TYPE_CUSTOM_TEXT) {
                $params['custom_text']  = !empty($sOption['value']) ? $sOption['value'] : '';
                $params['custom_font']  = !empty($sOption['font']) ? $sOption['font'] : '';
                $params['custom_color'] = !empty($sOption['color']) ? $sOption['color'] : '';
                $params['custom_size']  = !empty($sOption['size']) ? $sOption['size'] : '';
            } else {
                if (!empty($sOption['value']) && !empty($sOption['image'])) {
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $sOption['image']));
                    $path  = WP_CONTENT_DIR . '/uploads/customizer/custom_images/';
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $valueArr = explode('.', $sOption['value']);
                    $ext      = end($valueArr);
                    do {
                        $fileName = uniqid() . '_' . rand(1000, 9999) . '.' . $ext;
                        $fullPath = $path . $fileName;
                    } while (file_exists($fullPath));

                    file_put_contents($path . $fileName, $image);
                    $params['custom_image'] = $fileName;
                }
            }
            $resultOptions[$params['component_id']][] = $params;
        }

        return $resultOptions;
    }

    /**
     * @param $optionId
     * @param $customizerOptions
     *
     * @return array
     */
    private static function setDefaultParams($optionId, $customizerOptions)
    {
        $component = [];
        $option    = [];
        $arr       = [];
        foreach ($customizerOptions as $comp) {
            if (!empty($comp['options'])) {
                foreach ($comp['options'] as $opt) {
                    if ($opt['option_id'] == $optionId) {
                        $component = $comp;
                        $option    = $opt;
                        break;
                    }
                }
            }
        }
        if (!empty($option)) {
            $arr = [
                'component_id'           => $component['component_id'],
                'component_type'         => $component['component_type'],
                'component_name'         => $component['component_name'],
                'component_icon'         => $component['component_icon'],
                'option_id'              => $option['option_id'],
                'option_name'            => $option['option_name'],
                'option_price'           => $option['option_price'],
                'option_icon'            => $option['option_icon'],
                'option_image'           => $option['option_image'],
                'option_top'             => $option['option_top'],
                'option_left'            => $option['option_left'],
                'option_transform'       => $option['option_transform'],
                'option_width'           => $option['option_width'],
                'option_height'          => $option['option_height'],
                'option_slide'           => $option['option_slide'],
                'option_icon_background' => $option['option_icon_background'],
                'option_icon_text'       => $option['option_icon_text'],
            ];
        }

        return $arr;
    }

    /**
     * @param      $data
     * @param bool $withImages
     * @param bool $withPrice
     *
     * @return string
     */
    public static function get_customizer_options_formatted($data, $withImages = true, $withPrice = false, $isOrder = false)
    {
        if (empty($data)) {
            return '';
        }

        $img_style = 'style="display:inline; max-height: 24px;"';
        $ul_style  = 'style="list-style: none;"';

        $html = '<ul class="components" ' . $ul_style . '>';
        foreach ($data as $component) {
            $options        = [];
            $componentName  = '';
            $componentImage = '';
            $optionsHtml    = '';
            foreach ($component as $option) {
                if (empty($option['option_name'])) {
                    continue;
                }
                $optionsKey  = 'customizer-options';
                $savedValues = get_option($optionsKey, array());

                $zeroPrice = isset($savedValues['zero_price']) ? $savedValues['zero_price'] : true;
                if ($isOrder && !$zeroPrice && $option['option_price'] == 0) {
                    //continue;
                }

                $optionImage = '';
                if ($withImages) {
                    if (!empty($option['option_icon'])) {
                        $optionImage = '<img class="cart_option_icon" src="' . Customizer_Public::get_image_full_url($option['option_icon']) . '" ' . $img_style . ' />';
                    } elseif(!empty($option['option_icon_background']) && $option['component_type'] == self::COMPONENT_TYPE_IMAGE) {
                        $optionImage = '<div class="shop-filter__option__icon_bg" style="background-color:#'.$option['option_icon_background'].'">';
                        $text = !empty($option['option_icon_text']) ? $option['option_icon_text'] : '&nbsp;';
                        $optionImage.=$text.'</div>';
                    }
                }
                $optionsHtml .= '<li class="option">' . $optionImage . ' ' . $option['option_name'];
                if ($withPrice && !empty($option['option_price'])) {
                    $optionsHtml .= ' (' . strip_tags(Customizer_Public::customizer_wc_price($option['option_price'])) . ')';
                }
                if (!empty($option['custom_text'])) {
                    $optionsHtml .= '<br />- ' . __('Font', 'customizer') . ': <i>' . $option['custom_font'] . '</i>';
                    $optionsHtml .= '<br />- ' . __('Color', 'customizer') . ': <i>' . self::rgbToHex($option['custom_color']) . '</i>';
                    $optionsHtml .= '<br />- ' . __('Text', 'customizer') . ': <i><strong>' . $option['custom_text'] . '</strong></i>';
                }
                if (!empty($option['custom_image'])) {
                    $optionsHtml .= '<br /><a target="_blank" href="' . WP_CONTENT_URL . '/uploads/customizer/custom_images/' . $option['custom_image'] . '"><image style="display: inline; max-height: 24px" src="' . WP_CONTENT_URL . '/uploads/customizer/custom_images/' . $option['custom_image'] . '"></a>';
                }
                $optionsHtml .= '</li>';

                $options[]      = $option['option_name'];
                $componentName  = $option['component_name'];
                $componentImage = Customizer_Public::get_image_full_url($option['component_icon']);
            }
            if (!empty($optionsHtml) && !empty($componentName)) {
                if ($withImages) {
                    $componentImage = '<img class="cart_component_icon" src="' . $componentImage . '" ' . $img_style . ' />';
                } else {
                    $componentImage = '';
                }
                $html .= '<li class="component">' . $componentImage . ' ' . $componentName .
                    '<ul class="options" ' . $ul_style . '>' . $optionsHtml . '</ul></li>';
            }
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * @param $cart_item
     *
     * @return float
     */
    public function calculate_product_price($cart_item)
    {
        $changes = $cart_item['data']->get_changes();
        if (!empty($changes['price'])) {
            return $changes['price'];
        }
        $price = 0;
        if (!empty($cart_item['data'])) {
            $price = (float)$cart_item['data']->get_price();
        }
        $customizer_data = array();
        if (isset($cart_item['variation']['customizer'])) {
            $customizer_data = $cart_item['variation']['customizer'];
        }
        if (isset($cart_item['variation']['attribute_customizer'])) {
            $customizer_data = $cart_item['variation']['attribute_customizer'];
        }
        $customizer_data = unserialize($customizer_data);
        foreach ($customizer_data as $component) {
            foreach ($component as $option) {
                $price += (float)$option['option_price'];
            }
        }

        return $price;
    }

    /**
     * @param boolean $withDefaultOptions
     *
     * @return float
     */
    public static function calculate_default_price($withDefaultOptions = true)
    {
        $price   = 0;
        $product = null;

        if (self::is_woocommerce_enabled()) {
            $productId = self::get_product_id();
            if (empty($productId)) {
                return 0;
            }

            $product = wc_get_product($productId);
            if ($product) {
                $price = !empty($customizer_settings['base_price']) ? $customizer_settings['base_price'] : wc_get_price_to_display($product);
            }
        }
        if ($withDefaultOptions) {
            $data = self::get_customizer_meta();
            if (empty($data['customizer'])) {
                return $price;
            }
            foreach ($data['customizer'] as $component) {
                if (empty($component['options'])) {
                    continue;
                }
                foreach ($component['options'] as $option) {
                    if (!empty($option['option_default'])) {
                        $opt_price = (self::is_woocommerce_enabled() && $product) ? wc_get_price_to_display($product, ['price' => $option['option_price']]) : $option['option_price'];
                        $price     += $opt_price;
                    }
                }
            }
        }

        return $price;
    }

    /**
     * @param $cart_item
     *
     * @return mixed
     */
    public function force_individual_items($cart_item)
    {
        $unique_cart_item_key    = md5(microtime() . rand() . rand(1000, 9999));
        $cart_item['unique_key'] = $unique_cart_item_key;

        return $cart_item;
    }


    /**
     * @param $cart_object
     */
    public function change_price($cart_object)
    {
        foreach ($cart_object->cart_contents as $key => $value) {
            if (self::get_customizer_id_from_item($value)) {
                $newPrice = $this->calculate_product_price($value);
                if (self::isWooCommerce3_3()) {
                    $value['data']->set_price($newPrice);
                } elseif (self::isWooCommerce3()) {
                    $value['data']->set_price($newPrice);
                } else {
                    $value['data']->price = $newPrice;
                }
            }
        }
    }

    /**
     * @param $name
     * @param $order_item
     *
     * @return string
     */
    public function get_item_data_order($name, $order_item)
    {
        return $this->get_item_data($name, $order_item, true);
    }

    /**
     * @param $name
     * @param $cart_item
     * @param $order
     *
     * @return string
     */
    public function get_item_data($name, $cart_item, $order = false)
    {
        $data = self::get_customizer_data_from_item($cart_item);
        $html = '';
        if (empty($data)) {
            $link = get_post_permalink($cart_item['product_id']);
        } else {
            if ($order !== true) {
                $html = $this->get_customizer_options_formatted($data);
            }

            $customizerId   = self::get_customizer_id_from_item($cart_item);
            $customizerInfo = get_post($customizerId);
            $link           = self::get_customizer_link($customizerId);
            $name           = $customizerInfo->post_title;
        }

        $result = '<a href="' . $link . '">' . $name . '</a>';
        if ($order !== true) {
            $result .= '<div class="customizer-options">' . $html . '</div>';

        }

        return $result;
    }

    /**
     * @param $item
     *
     * @return null
     */
    public static function get_customizer_id_from_item($item)
    {
        $customizerId = null;
        if (!empty($item['variation']['customizer_id'])) {
            $customizerId = $item['variation']['customizer_id'];
        }

        if (!empty($item['variation']['attribute_customizer_id'])) {
            $customizerId = $item['variation']['attribute_customizer_id'];
        }

        if (!empty($item['customizer_id'])) {
            $customizerId = $item['customizer_id'];
        }

        if (!empty($item['attribute_customizer_id'])) {
            $customizerId = $item['attribute_customizer_id'];
        }

        return $customizerId;
    }

    /**
     * @param $item
     *
     * @return null
     */
    public static function get_customizer_data_from_item($item)
    {
        $customizer = null;
        if (!empty($item['variation']['customizer'])) {
            $customizer = $item['variation']['customizer'];
        }

        if (!empty($item['variation']['attribute_customizer'])) {
            $customizer = $item['variation']['attribute_customizer'];
        }

        if (!empty($item['customizer'])) {
            $customizer = $item['customizer'];
        }

        if (!empty($item['attribute_customizer'])) {
            $customizer = $item['attribute_customizer'];
        }

//        $customizer = unserialize($customizer);
        if (!is_array($customizer)) {
            $customizer = unserialize($customizer);
        }

        return $customizer;

    }


    /**
     * Updating mata tags in order
     *
     * @param $post_id
     */
    public function add_item_meta($post_id)
    {
        /** @var WC_Order $order */
        $order = wc_get_order($post_id);
        foreach ($order->get_items() as $item_id => $item) {
            $customizer_components = unserialize(wc_get_order_item_meta($item_id, 'customizer'));
            if (is_array($customizer_components)) {
                foreach ($customizer_components as $component) {
                    $componentName   = '';
                    $selectedOptions = [];
                    foreach ($component as $option) {
                        $selectedOptions[] = $option['option_name'] . ' (' . strip_tags(Customizer_Public::customizer_wc_price($option['option_price'])) . ')';
                        $componentName     = $option['component_name'];
                    }
                    if (!empty($selectedOptions) && $componentName) {
                        wc_add_order_item_meta($item_id, $componentName, implode(', ', $selectedOptions));
                    }
                }
            }
        }
    }

    /**
     * @param $product_image_code
     * @param $item
     * @param $cart_item_key
     *
     * @return string
     */
    public static function get_customizer_image($product_image_code, $item, $cart_item_key)
    {
        //image in shipping cart
        $data = self::get_customizer_data_from_item($item['variation']);
        if (empty($data)) {
            return $product_image_code;
        }
        $customizer_id = self::get_customizer_id_from_item($item);
        $link          = get_post_permalink($customizer_id);
        $html          = '</a><a href="' . $link . '"><div class="customizer_cart_thumb">';

        $images = self::get_all_customizer_images($data, false, false, $customizer_id);

        foreach ($images[0] as $option_id => $image) {
            if ($image != self::COMPONENT_TYPE_CUSTOM_TEXT && $image != self::COMPONENT_TYPE_CUSTOM_IMAGE) {
                $html .= '<img src="' . $image . '" />';
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * @param      $customizerData
     * @param bool $onlyDefault
     * @param bool $listing
     *
     * @return array|string
     */
    public static function get_all_customizer_images($customizerData, $onlyDefault = false, $listing = false, $customizer_id = null)
    {
        if ($listing) {
            $images              = self::get_default_listing_image();
            $customizer_settings = self::get_customizer_settings($customizer_id);
            if (!empty($customizer_settings['use_product_image_listing'])) {
                return $images;
            }
        } else {
            $images = self::get_default_image('full', $customizer_id);
        }
        foreach ($customizerData as $options) {
            $arr_options = (isset($options['options'])) ? $options['options'] : $options;
            foreach ($arr_options as $option) {
                if ($onlyDefault && empty($option['option_default'])) {
                    continue;
                }
                $component_type = empty($option['component_type']) ? $options['component_type'] : $option['component_type'];
                $option_enable  = isset($option['option_enable']) ? $option['option_enable'] : true;
                $option_id      = $option['option_id'];
                if ($component_type === self::COMPONENT_TYPE_IMAGE && $option_enable) {
                    if (!empty($option['option_image'])) {
                        if (is_array($option['option_image'])) {
                            foreach ($option['option_image'] as $slide => $image) {
                                $images[$slide][$option_id] = self::get_image_full_url($image);
                            }
                        } else {
                            $images[0][$option_id] = self::get_image_full_url($option['option_image']);
                        }
                    }
                } else {
                    $slide                      = !empty($option['option_slide']) ? (int)$option['option_slide'] : 0;
                    $images[$slide][$option_id] = $component_type;
                }
            }
        }
        ksort($images);
        return $images;
    }

    public function item_meta_display($output, $meta)
    {
        if (!empty($meta->meta['customizer_id'][0])) {
            return '';
        }

        return $output;
    }

    /**
     * @param $item_id
     * @param $item
     * @param $order
     *
     * @return string
     */
    public function email_order_item_meta($item_id, $item, $order)
    {
        $html = '';
        if (is_order_received_page()) {
            return '';
        }
        $data = self::get_customizer_data_from_item($item);

        if (empty($data)) {
            return '';
        }

        $html .= '<div class="customizer_meta">';
        //generate image
        $html .= self::generateImage($item_id, $item, true);
        $html .= self::get_customizer_options_formatted($data, true, true, true);
        $html .= '</div>';

        echo $html;
    }

    public function hide_woocommerce_items_order($html, $item, $args)
    {
        if (is_order_received_page()) {
            $item = self::get_customizer_data_from_item($item);
            echo self::get_customizer_options_formatted($item, true, true, true);
        } else {
            return '';
        }
    }

    /**
     * @param      $itemId
     * @param      $item
     * @param bool $forceImage
     *
     * @return string
     */
    public static function generateImage($itemId, $item, $forceImage = false)
    {
        $options = get_option('customizer-options', array());
        if (empty($options['customizer-as-one-image']) && !$forceImage) {
            return self::generateHtmlImage($item);
        }

        $data          = self::get_customizer_data_from_item($item);
        $customizer_id = self::get_customizer_id_from_item($item);
        $images        = self::get_all_customizer_images($data, false, false, $customizer_id);

        $path = '/uploads/customizer/';
        $name = $itemId . '.jpg';
        $url  = get_home_url() . '/wp-content' . $path . $name;
        $file = ABSPATH . 'wp-content' . $path . $name;
        if (file_exists($file)) {
            return '<img width="300px" src="' . $url . '"/>';
        }

        if (empty($images)) {
            return '';
        }

        $uploadFolder = WP_CONTENT_DIR . $path;

        if (!file_exists($uploadFolder)) {
            wp_mkdir_p($uploadFolder);
        }
        list($width, $height) = self::getMaxWidthHeight($images[0]);

        $canvas = imagecreatetruecolor($width, $height);
        imagesavealpha($canvas, true);

        $transLayerOverlay = imagecolorallocatealpha($canvas, 225, 225, 225, 127);
        imagefill($canvas, 0, 0, $transLayerOverlay);

        foreach ($images[0] as $option_id => $image) {
            if ($image == self::COMPONENT_TYPE_CUSTOM_IMAGE) {
                $customImage = self::get_image_with_image($width, $height, $itemId, $option_id);
                imagecopyresampled($canvas, $customImage, 0, 0, 0, 0, $width, $height, $width, $height);

            } else if ($image == self::COMPONENT_TYPE_CUSTOM_TEXT) {
                $textImage = self::get_image_with_text($width, $height, $itemId, $option_id);
                imagecopyresampled($canvas, $textImage, 0, 0, 0, 0, $width, $height, $width, $height);

            } else {
                if (substr($image, -3) == 'gif') {
                    $pngNameArr = explode('/', $image);
                    $pngName    = end($pngNameArr);

                    $pngName = str_replace('gif', 'png', $pngName);
                    array_pop($pngNameArr);
                    $pngNameArr[] = $pngName;
                    $pngName      = implode('/', $pngNameArr);

                    imagepng(imagecreatefromstring(file_get_contents(ABSPATH . $image)), ABSPATH . $pngName);
                    $image = $pngName;
                }

                $img       = false;
                $mime_type = exif_imagetype($image);
                if ($mime_type === IMAGETYPE_PNG) {
                    $img = imagecreatefrompng($image);
                } elseif ($mime_type === IMAGETYPE_JPEG) {
                    $img = imagecreatefromjpeg($image);
                }

                list($iWidth, $iHeight) = getimagesize($image);
                if ($img) {
                    imagecopyresampled($canvas, $img, 0, 0, 0, 0, $iWidth, $iHeight, $iWidth, $iHeight);
                }
            }
        }

        $name = $itemId . '.png';
        imagepng($canvas, $uploadFolder . '/' . $name, 9);

//        save as jpg
        $input_file  = $uploadFolder . '/' . $name;
        $output_file = $uploadFolder . '/' . $itemId . '.jpg';
        $input       = imagecreatefrompng($input_file);
        list($width, $height) = getimagesize($input_file);
        $output = imagecreatetruecolor($width, $height);
        $white  = imagecolorallocate($output, 255, 255, 255);
        imagefilledrectangle($output, 0, 0, $width, $height, $white);
        imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
        imagejpeg($output, $output_file);
        unlink($input_file);

        return '<img width="300px" src="' . $url . '" />';
    }


    /**
     * @param $item
     * @param $orderImageWidth
     *
     * @return string
     */
    public static function generateHtmlImage($item, $orderImageWidth = 300)
    {
        $data          = self::get_customizer_data_from_item($item);
        $customizer_id = self::get_customizer_id_from_item($item);
        $images        = self::get_all_customizer_images($data, false, false, $customizer_id);
        $option_ids    = [];
        foreach ($data as $components) {
            foreach ($components as $option) {
                $option_ids[] = $option['option_id'];
            }
        }

        list($width, $height) = self::getMaxWidthHeight($images[0]);
        $newHeight = round($orderImageWidth * $height / $width);
        $zIndex    = 1;
        $html      = '<div style="position:relative; height:' . $newHeight . 'px; width:' . $orderImageWidth . 'px;">';
        $koef      = $orderImageWidth / $width;

        foreach ($images[0] as $key => $image) {
            if (!in_array($key, $option_ids)) {
                $html .= '<img width="' . $orderImageWidth . 'px" src="' . $image . '" style="position:absolute;" z-index="' . $zIndex++ . '" />';
            }
        }
        $html .= self::get_text_on_mod_html($orderImageWidth, $data, $zIndex, $koef);
        $html .= '</div>';
        return $html;
    }

    /**
     * @param $images
     *
     * @return array|string
     */
    public static function getMaxWidthHeight($images)
    {
        $width     = 0;
        $height    = 0;
        $arr_image = (!is_array($images)) ? [$images] : $images;
        $result    = [];
        array_walk_recursive($arr_image, function ($item, $key) use (&$result) {
            $result[$key] = $item;
        });
        foreach ($result as $image) {
            if (!empty($image)) {
                $image = self::get_image_full_url($image);
                if (empty($image) || $image === Customizer_Public::COMPONENT_TYPE_CUSTOM_TEXT || $image === Customizer_Public::COMPONENT_TYPE_CUSTOM_IMAGE) {
                    continue;
                }

                $headers = @get_headers($image);
                if (strpos($headers[0], '200')) {
                    list($tempWidth, $tempHeight) = getimagesize($image);
                } else {
                    $tempHeight = 0;
                    $tempWidth  = 0;
                }
                if ($width < $tempWidth) {
                    $width = $tempWidth;
                }
                if ($height < $tempHeight) {
                    $height = $tempHeight;
                }
            }
        }
        $width = ($width === 0) ? 1 : $width;

        return [$width, $height];
    }

    /**
     * @param string $size
     * @param null   $customizer_id
     * @param bool   $listing
     *
     * @return array
     */
    public static function get_default_image($size = 'medium', $customizer_id = null, $listing = false)
    {
        $defaultImage        = [];
        $customizer_settings = self::get_customizer_settings($customizer_id);

        if ($listing) {
            $useProductImage = !empty($customizer_settings['use_product_image_listing']) ? $customizer_settings['use_product_image_listing'] : 0;
        } else {
            $useProductImage = !empty($customizer_settings['use_product_image_single']) ? $customizer_settings['use_product_image_single'] : 0;
        }

        $multiview = !empty($customizer_settings['multiview']) ? $customizer_settings['multiview'] : 1;
        for ($slide = 0; $slide < $multiview; $slide++) {
            if ($useProductImage && $slide === 0) {
                $productId              = self::get_product_id($customizer_id);
                $image                  = wp_get_attachment_image_src(get_post_thumbnail_id($productId), $size);
                $defaultImage[$slide][] = !empty($image[0]) ? self::get_image_full_url($image[0]) : '';
                continue;
            }
            if (!empty($customizer_settings['slides'][$slide])) {
                $defaultImage[$slide][] = self::get_image_full_url($customizer_settings['slides'][$slide]);
            }
        }

        return $defaultImage;
    }

    /**
     * @return string
     */
    public static function get_default_listing_image()
    {
        return self::get_default_image('medium', null, true);
    }

    /** Prepare button on product page */
    public function get_customizer_button()
    {
        $post_id = get_the_ID();
        $html    = apply_filters('get_background_button_html', $post_id);
        if ($html !== $post_id) {
            echo $html;
        } else {
            $customizer = self::get_customizer_by_product($post_id);
            if ($customizer) {
                echo '<style type="text/css">.quantity,.cart button[type="submit"]{display:none;}</style>';
                echo '<a href="' . get_permalink($customizer->ID) . '" class="single_add_to_cart_button button alt">' . __('Customize',
                        'customizer') . '</a>';
            }
        }
    }

    /** Prepare button on product list page */
    public function get_customizer_button_loop()
    {

    }

    /**
     * @param        $customizer_id
     * @param string $hash
     *
     * @return string|\WP_Error
     */
    public static function get_customizer_link($customizer_id, $hash = null)
    {
        $link = get_post_permalink($customizer_id);
        if ($hash) {
            $link .= '?hash=' . $hash;
        }

        return $link;
    }

    /**
     * @param      $item_id
     * @param      $option_id
     * @param bool $text
     *
     * @return array
     */
    public static function get_selected_option($item_id, $option_id, $text = true, $from_order = true)
    {
        $customizer_components = ($from_order) ? unserialize(wc_get_order_item_meta($item_id, 'customizer')) : $item_id;
        foreach ($customizer_components as $component) {
            foreach ($component as $option) {
                if ((
                        ($text && $option['component_type'] == self::COMPONENT_TYPE_CUSTOM_TEXT) ||
                        (!$text && $option['component_type'] == self::COMPONENT_TYPE_CUSTOM_IMAGE)
                    ) && $option['option_id'] == $option_id
                ) {
                    return $option;
                }
            }
        }

        return [];
    }

    /**
     * @param $name
     *
     * @return string
     */
    public static function get_font($name)
    {
        $fontFolder = WP_CONTENT_DIR . '/uploads/customizer/fonts/';

        if (!file_exists($fontFolder)) {
            wp_mkdir_p($fontFolder);
        }
        $fileMame = sanitize_title($name) . '.ttf';
        if (!file_exists($fontFolder . $fileMame)) {
            $fonts = Customizer_Admin::get_all_fonts();
            if (!empty($fonts[$name])) {
                $font = file_get_contents($fonts[$name]);
                file_put_contents($fontFolder . $fileMame, $font);
            }
        }
        if (file_exists($fontFolder . $fileMame)) {
            return $fontFolder . $fileMame;
        }

        return '';
    }

    /**
     * @param      $width
     * @param      $height
     * @param      $item_id
     * @param      $option_id
     * @param bool $from_order
     *
     * @return mixed
     */
    public static function get_image_with_text($width, $height, $item_id, $option_id, $from_order = true)
    {
        //create empty image
        $empty = imagecreatetruecolor($width, $height);
        imagesavealpha($empty, true);

        $transLayerOverlay = imagecolorallocatealpha($empty, 225, 225, 225, 127);
        imagefill($empty, 0, 0, $transLayerOverlay);

        $text_option = self::get_selected_option($item_id, $option_id, true, $from_order);
        if (empty($text_option)) {
            return $empty;
        }
        if (empty($text_option['custom_font'])) {
            $text_option['custom_font'] = 'Aclonica';
        }
        $font_file = self::get_font($text_option['custom_font']);

        $text = $text_option['custom_text'];
        preg_match_all('/\d{1,3}/', $text_option['custom_color'], $colors);
        $color = imagecolorallocate($empty, $colors[0][0], $colors[0][1], $colors[0][2]);

        $gd_inf = gd_info();
        preg_match('(\d)', $gd_inf['GD Version'], $matches);
        $gd_version = $matches[0];
        if ($gd_version == 1) {
            $font_size = str_replace('px', '', $text_option['custom_size']);
        } else {
            $font_size = str_replace('px', '', $text_option['custom_size']) * 0.8;
            $font_size *= 0.752812499999996;
        }

        $transform = $text_option['option_transform'] * (-1);
        $left      = $font_size + $text_option['option_left'] * $width / 100;
        $top       = $font_size + $text_option['option_top'] * $height / 100;

        imagettftext($empty, $font_size, $transform, $left, $top, $color, $font_file, $text);

        return $empty;
    }

    /**
     * @param      $width
     * @param      $height
     * @param      $item_id
     * @param      $option_id
     * @param bool $from_order
     *
     * @return resource
     */
    public static function get_image_with_image($width, $height, $item_id, $option_id, $from_order = true)
    {
        //create empty image
        $empty = imagecreatetruecolor($width, $height);
        imagealphablending($empty, false);
        imagesavealpha($empty, true);

        $transLayerOverlay = imagecolorallocatealpha($empty, 0, 0, 0, 127);
        imagefill($empty, 0, 0, $transLayerOverlay);

        $image_option = self::get_selected_option($item_id, $option_id, false, $from_order);
        if (empty($image_option) || (isset($image_option['option_slide']) && $image_option['option_slide'] !== '0')) {
            return $empty;
        }

        $imagePath = WP_CONTENT_DIR . '/uploads/customizer/custom_images/' . $image_option['custom_image'];
        if (!file_exists($imagePath)) {
            return $empty;
        }

        if (is_file($imagePath) && mime_content_type($imagePath) === 'image/png') {
            $sourceImage = imagecreatefrompng($imagePath);
        } elseif (is_file($imagePath) && mime_content_type($imagePath) === 'image/jpeg') {
            $sourceImage = imagecreatefromjpeg($imagePath);
        } else {
            return $empty;
        }

        list($source_width, $source_height) = getimagesize($imagePath);
        $needed_width  = round($width * $image_option['option_width'] / 100);
        $needed_height = round($height * $image_option['option_height'] / 100);
        $deltaX        = round($needed_width / 2) * 0.752812499999996;
        $deltaY        = round($needed_height / 2) * 0.752812499999996;

        $thumb = imagecreatetruecolor($needed_width, $needed_height);
        imagefill($thumb, 0, 0, $transLayerOverlay);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresized($thumb, $sourceImage, 0, 0, 0, 0, $needed_width, $needed_height, $source_width, $source_height);
        $transform     = $image_option['option_transform'] * (-1);
        $thumb         = imagerotate($thumb, $transform, $transLayerOverlay);
        $needed_width  = imagesx($thumb);
        $needed_height = imagesy($thumb);

        $fi = $transform * 3.1415926 / 180;
        if ($transform < 0) {
            $targetLeft    = round($image_option['option_left'] * $width / 100) + $deltaX;
            $targetTop     = round($image_option['option_top'] * $height / 100) + $deltaY;
            $newTargetLeft = $targetLeft * cos($fi) - $targetTop * sin($fi) - $deltaX;
            $newTargetTop  = -$targetLeft * sin($fi) - $targetTop * cos($fi) - $deltaY;
        } elseif ($transform > 0) {
            $targetLeft    = round($image_option['option_left'] * $width / 100) - $deltaX;
            $targetTop     = round($image_option['option_top'] * $height / 100) - $deltaY;
            $newTargetLeft = $targetLeft * cos($fi) - $targetTop * sin($fi) + $deltaX;
            $newTargetTop  = -$targetLeft * sin($fi) - $targetTop * cos($fi) + $deltaY;
        } else {
            $newTargetLeft = round($image_option['option_left'] * $width / 100);
            $newTargetTop  = round($image_option['option_top'] * $height / 100);
        }

        imagecopyresized($empty, $thumb, $newTargetLeft, $newTargetTop, 0, 0, $needed_width, $needed_height, $needed_width, $needed_height);

        return $empty;
    }

    /**
     * @param      $width
     * @param      $components
     * @param      $koef
     * @param      $zIndex
     *
     * @return string
     */
    public static function get_text_on_mod_html($width, $components, $zIndex, $koef = 1)
    {
        $html = '';
        foreach ($components as $component) {
            foreach ($component as $option) {

                switch ($option['component_type']) {
                    case self::COMPONENT_TYPE_IMAGE :
                        $html .= '<div style="
                            position: absolute;
                            z-index: ' . $zIndex++ . ';
                            width: 100%;
                            ">';
                        $html .= '<img style="width:' . $width * $koef . 'px;" src="' . Customizer_Public::get_image_full_url($option['option_image']) . '">';
                        $html .= '</div>';
                        break;
                    case self::COMPONENT_TYPE_CUSTOM_TEXT :
                        if ($option['option_slide'] === '0') {
                            $fontSize = str_replace('px', '', $option['custom_size']);
                            $fontSize = round($koef * $fontSize);
                            $html     .= '<div style="
	                            position: absolute;
	                            left: ' . $option['option_left'] . '%; 
	                            top: ' . $option['option_top'] . '%; 
	                            transform: rotate(' . $option['option_transform'] . 'deg); 
	                            font-family: ' . (!empty($option['custom_font']) ? $option['custom_font'] : 'Verdana') . ';
	                            font-size: ' . $fontSize . 'px;
	                            color: ' . (!empty($option['custom_color']) ? $option['custom_color'] : '#fff') . '; 
	                            z-index: ' . $zIndex++ . ';
	                            width: ' . $option['option_width'] . '%;
	                            height: ' . $option['option_height'] . '%;
                            ">';
                            $html     .= $option['custom_text'];
                            $html     .= '</div>';
                        }
                        break;
                    case self::COMPONENT_TYPE_CUSTOM_IMAGE :
                        if ($option['option_slide'] === '0') {
                            $html .= '<div style="
	                            position: absolute;
	                            left: ' . $option['option_left'] . '%; 
	                            top: ' . $option['option_top'] . '%; 
	                            transform: rotate(' . $option['option_transform'] . 'deg); 
	                            z-index: ' . $zIndex++ . ';
	                            width: ' . $option['option_width'] . '%;
	                            height: ' . $option['option_height'] . '%;
	                            ">';
                            $html .= '<img style="width:100%; height:100%"  src="' . self::get_image_full_url('/customizer/custom_images/' . $option['custom_image']) . '">';
                            $html .= '</div>';
                        }
                        break;
                }
            }
        }

        return $html;
    }

    /**
     * @return string
     */
    public static function get_formatted_rules()
    {
        $json  = '';
        $rules = Customizer_Admin::get_saved_rules_for_edit();

        if (!empty($rules)) {
            foreach ($rules as $rule) {
                if (empty($rule['enable'])) {
                    continue;
                }
                $json .= (!empty($json) ? ',' : '');
                $json .= '{trigger:{"option_id": "' . $rule['trigger'] . '","component_id":null},target:{';
                if ($rule['scope'] == 'component') {
                    $json .= '"option_id":null,"component_id":"' . $rule['target'] . '"';
                } else {
                    $json .= '"option_id":"' . $rule['target'] . '","component_id":null';
                }
                $json .= ',"action":"' . $rule['action'] . '"}';
                $json .= ',"reverse":' . (int)!empty($rule['reverse']) . '';
                $json .= '}';
            }
        }

        return '[' . $json . ']';
    }

    /**
     * @return array
     */

    public static function get_domain_names()
    {
        //hotfix for test sites
        return [
            get_home_url() . '/',
            'http://wordpress.k2-service.com/',
            'http://v2.wordpress.k2-service.com/',
        ];
    }

    /**
     * @param $rgb
     *
     * @return string
     */
    public static function rgbToHex($rgb)
    {
        $rgb = str_replace(['(', ')', 'rgb'], '', $rgb);
        $rgb = explode(',', $rgb);
        $hex = "#";
        $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        return $hex;

    }

    /**
     * @return bool
     */
    public static function is_woocommerce_enabled()
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        return is_plugin_active('woocommerce/woocommerce.php');
    }

    /**
     * @param $price
     *
     * @return string
     */
    public static function customizer_wc_price($price)
    {
        if (self::is_woocommerce_enabled()) {
            $pr        = $price;
            $productId = self::get_product_id();
            if (!empty($productId)) {
                $product = wc_get_product($productId);
                if ($product) {
                    $pr = wc_get_price_to_display($product, ['price' => $price]);
                }
            }

            return wc_price($pr);
        }

        return $price;
    }

    /**
     * @return bool
     */
    public static function can_show_add_to_cart()
    {
        $showAddToCart       = false;
        $customizer_settings = self::get_customizer_settings();

        if (self::is_woocommerce_enabled()
            && !empty($customizer_settings['add-to-cart_show'])
            && !empty($customizer_settings['product_id'])
        ) {
            $showAddToCart = true;
        }

        return $showAddToCart;
    }

    /**
     * @param $formatted_meta
     * @param $order_item
     *
     * @return array
     */
    public function formatted_meta_data($formatted_meta, $order_item)
    {
        $new_formatted_meta = array();
        foreach ($formatted_meta as $meta_id => $meta) {
            if (!in_array($meta->key, array('customizer_id', 'customizer'))) {
                $new_formatted_meta[$meta_id] = $meta;
            }
        }

        return $new_formatted_meta;
    }

    /**
     * @return bool
     */
    public static function isWooCommerce3()
    {
        if (class_exists('WooCommerce') && (version_compare(WC()->version, '3.0.0', ">"))) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function isWooCommerce3_3()
    {
        if (class_exists('WooCommerce') && (version_compare(WC()->version, '3.3.0', ">="))) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function can_show_save_form()
    {
        $plugin_options = get_option('customizer-options');

        return !empty($plugin_options['save_design']);
    }


    /**
     * @param $settings
     * @param $options
     *
     * @return string
     */
    public function save_customizer($settings, $options)
    {
        $customizer = get_post();
        global $wpdb;
        $name_table                  = $wpdb->prefix . self::SAVE_TABLE_NAME;
        $name_table_custom_image     = $wpdb->prefix . self::SAVE_TABLE_CUSTOM_IMAGE_NAME;
        $customizer_id               = self::get_customizer_id();
        $id                          = !empty($settings['id']) ? $settings['id'] : '';
        $settings['user_id']         = get_current_user_id();
        $settings['name_customizer'] = !empty($settings['name_customizer']) ? $settings['name_customizer'] : $customizer->post_title;

        $time_now = time();

        $public = (empty($settings['public']) === '0') ? '0' : '1';

        if (isset($_POST['customizer-save']) || empty($settings['hash'])) {
            $hash          = self::generate_hash();
            $options       = json_decode(str_replace('\"', '"', $options), true);
            $custom_fields = $options['text_options'];
            foreach ($custom_fields as $key => $field) {
                if ($field['type'] === self::COMPONENT_TYPE_CUSTOM_IMAGE && !empty($field['image'])) {
                    $image = $field['image'];
                    $wpdb->insert(
                        $name_table_custom_image,
                        array('image' => $image),
                        array('%s')
                    );
                    $options['text_options'][$key]['image'] = $wpdb->insert_id;
                }
            }
            $options = str_replace('"', '\"', json_encode($options));
            $wpdb->insert(
                $name_table,
                array(
                    'customizer_id'    => $customizer_id,
                    'user_id'          => $settings['user_id'],
                    'name_customizer'  => $settings['name_customizer'],
                    'selected_options' => $options,
                    'hash'             => $hash,
                    'public'           => $public,
                    'created_at'       => $time_now,
                    'updated_at'       => $time_now
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );

        } else {
            $options       = json_decode(str_replace('\"', '"', $options), true);
            $custom_fields = $options['text_options'];
            foreach ($custom_fields as $key => $field) {
                if ($field['type'] === self::COMPONENT_TYPE_CUSTOM_IMAGE && !empty($field['image'])) {
                    $image = $field['image'];
                    $wpdb->insert(
                        $name_table_custom_image,
                        array('image' => $image),
                        array('%s')
                    );
                    $options['text_options'][$key]['image'] = $wpdb->insert_id;
                }
            }
            $options = str_replace('"', '\"', json_encode($options));
            $wpdb->update(
                $name_table,
                array(
                    'name_customizer'  => $settings['name_customizer'],
                    'selected_options' => $options,
                    'public'           => $public,
                    'updated_at'       => $time_now
                ),
                array('id' => $id),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%s'
                ),
                array('%d')
            );
            $hash = $settings['hash'];
        }

        return $hash;
    }

    /**
     * @param int $letters
     *
     * @return string
     */
    public static function generate_hash($letters = 7)
    {

        $hash    = wp_generate_password($letters, false);
        $existed = self::get_saved_customizer($hash);
        if ($existed) {
            return self::generate_hash();
        }

        return $hash;
    }

    /**
     * @param $hash
     *
     * @return array|null|object|void
     */
    public static function get_saved_customizer($hash)
    {
        global $wpdb;

        $name_table              = $wpdb->prefix . self::SAVE_TABLE_NAME;
        $name_table_custom_image = $wpdb->prefix . self::SAVE_TABLE_CUSTOM_IMAGE_NAME;

        $sql = "SELECT * FROM {$name_table} where hash = '{$hash}' LIMIT 1";
        $row = $wpdb->get_row($sql);
        if (!empty($row)) {
            $options       = json_decode(str_replace('\"', '"', $row->selected_options));
            $custom_fields = $options->text_options;
            foreach ($custom_fields as $key => $field) {
                if ($field->type === self::COMPONENT_TYPE_CUSTOM_IMAGE && !empty($field->image)) {
                    $sql       = "SELECT * FROM {$name_table_custom_image} where id = '{$field->image}' LIMIT 1";
                    $image_row = $wpdb->get_row($sql);;
                    $options->text_options[$key]->image = $image_row->image;
                }
            }
            $row->selected_options = str_replace('"', '\"', json_encode($options));
        }

        return $row;
    }

    public static function get_saved_customizers($user_id = null, $show_deleted = false, $order = '')
    {
        global $wpdb;

        $name_table = $wpdb->prefix . self::SAVE_TABLE_NAME;
        $sql        = "SELECT * FROM {$name_table}";
        $where      = [];
        if (!empty($user_id)) {
            $where[] = "user_id = '{$user_id}'";
        }
        if (!$show_deleted) {
            $where[] = "deleted = 0";
        }
        $where = implode(' AND ', $where);
        if (!empty($where)) {
            $sql .= ' where ' . $where;
        }
        if (!empty($order)) {
            $sql .= ' ORDER BY ' . $order;
        }

        $rows = $wpdb->get_results($sql);

        return $rows;
    }

    /**
     *
     * @return float
     */

    public static function get_saved_customizer_price($saved_customizer)
    {
        $price              = 0;
        $selected_options   = json_decode(str_replace('\"', '"', $saved_customizer->selected_options));
        $selected_id_option = [];
        foreach ($selected_options->options as $option) {
            $selected_id_option[] = $option;
        }
        if (!empty($selected_options->text_options)) {
            foreach ($selected_options->text_options as $option) {
                $selected_id_option[] = $option->option_id;
            }
        }
        if (self::is_woocommerce_enabled()) {
            $productId = self::get_product_id($saved_customizer->customizer_id);
            if (empty($productId)) {
                return 0;
            }
            $product = wc_get_product($productId);
            if ($product) {
                $price = wc_get_price_to_display($product);
            }
        }
        $data = self::get_customizer_meta($saved_customizer->customizer_id);
        if (empty($data['customizer'])) {
            return $price;
        }
        foreach ($data['customizer'] as $component) {
            if (empty($component['options'])) {
                continue;
            }
            foreach ($component['options'] as $option) {
                if (in_array($option['option_id'], $selected_id_option)) {
                    $opt_price = (self::is_woocommerce_enabled() && $product) ? wc_get_price_to_display($product, ['price' => $option['option_price']]) : $option['option_price'];
                    $price     += $opt_price;
                }
            }
        }

        return $price;
    }

    public function add_saved_customizers_endpoint()
    {
        add_rewrite_endpoint('saved-customizers', EP_ROOT | EP_PAGES);
    }

    public function saved_customizers_query_vars($vars)
    {
        $vars[] = 'saved-customizers';

        return $vars;
    }

    public function add_saved_customizers_link_my_account($items)
    {
        $items['saved-customizers'] = __('Saved Customizers', 'customizer');

        return $items;
    }

    public function table_saved_customizer_content()
    {
        if (!empty($_POST['public_state'])) {
            self::update_state_customizer($_POST['public_state'], 'public');
        }
        if (!empty($_POST['del_customizer'])) {
            self::update_state_customizer($_POST['customizer_hash'], 'deleted');
        }

        $saved_customizers = self::get_saved_customizers(get_current_user_id());
        $i                 = 0;
        $html              = "<div>";
        $html              .= "<h1>" . __('Saved Customizers', 'customizer') . "</h1>";
        $html              .= "<table class='table'><thead><tr>";
        $html              .= "<th>#</th>" .
            "<th>" . __('Name customizer', 'customizer') . "</th>" .
            "<th>" . __('Public', 'customizer') . "</th>" .
            "<th>" . __('Count view', 'customizer') . "</th>" .
            "<th>" . __('Updated at', 'customizer') . "</th><th></th>" .
            "</tr></thead><tbody>";
        foreach ($saved_customizers as $custom) {
            $link = $this->get_customizer_link($custom->customizer_id, $custom->hash);
            $html .= "<tr><td>" . ++$i . "</td>";
            $html .= "<td scope='row'><a name='customizer-link' href='" . $link . "'>" . $custom->name_customizer . "</a></td>";
            $html .= "<td><form name='customizer_public_state' id='customizer_public_state' method='post' action=''><input type='checkbox' name='public_state'";
            $html .= !empty($custom->public) ? "checked" : '';
            $html .= " onChange='this.form.submit()'><input name='public_state' type='hidden' value='" . $custom->hash . "'></form></td>";
            $html .= "<td>" . $custom->count_view . "</td>";
            $html .= "<td>" . date_i18n(get_option('date_format'), ($custom->updated_at ? $custom->updated_at : $custom->created_at)) . "</td>";
            $html .= "<td><form name='del_form' action='' method='post'>";
            $html .= '<input class="button-small button" onclick="window.open(\'' . $link . '&print=1\');" type="button" value="' . __('Print', 'customizer') . '" />&nbsp';
            $html .= "<input name='del_customizer' type='submit' class='button-small button' value='" . __('Delete', 'customizer') . "' onclick='return confirm(\"" . __('Are you sure you want to delete?', 'customizer') . "\");'>";
            $html .= "<input name='customizer_hash' type='hidden' value=" . $custom->hash . ">";
            $html .= "</form></td></tr>";
        }
        $html .= "</tbody></table></div>";
        echo $html;
    }

    /**
     * @param     $hash
     * @param int $orderImageWidth
     *
     * @return array|bool
     */
    public static function prepare_customizer_print($hash, $orderImageWidth = 300)
    {
        $saved_customizer = self::get_saved_customizer($hash);
        if (empty($saved_customizer)) {
            return wp_redirect('/my-account/saved-customizers/');
        }
        $saved_customizer->selected_options = str_replace('\"', '"', $saved_customizer->selected_options);
        $saved_options                      = json_decode($saved_customizer->selected_options);
        $saved_id_option                    = [];
        if (!empty($saved_options->text_options)) {
            foreach ($saved_options->text_options as $key => $option) {
                $saved_id_option[$option->option_id] = $key;
            }
        }
        $customizer                = self::get_customizer_meta($saved_customizer->customizer_id);
        $full_custom['customizer'] = [];
        foreach ($customizer['customizer'] as $main_compons) {
            $c_id = $main_compons['component_id'];
            if (!empty($main_compons['options'])) {
                foreach ($main_compons['options'] as $main_option) {
                    if (array_key_exists($main_option['option_id'], $saved_id_option) || in_array($main_option['option_id'], $saved_options->options)) {
                        $arr['component_id']   = $main_compons['component_id'];
                        $arr['component_type'] = $main_compons['component_type'];
                        $arr['component_name'] = $main_compons['component_name'];
                        $arr['component_icon'] = $main_compons['component_icon'];
                        foreach ($main_option as $k => $opt) {
                            $arr[$k] = $opt;
                        }
                        if (!empty($saved_options->text_options) && $arr['component_type'] !== 'image') {
                            $option = $saved_options->text_options[$saved_id_option[$main_option['option_id']]];
                            switch ($option->type) {
                                case self::COMPONENT_TYPE_CUSTOM_TEXT :
                                    $arr['custom_text']  = $option->value;
                                    $arr['custom_font']  = $option->font;
                                    $arr['custom_color'] = $option->color;
                                    $arr['custom_size']  = $option->size;
                                    break;
                                case self::COMPONENT_TYPE_CUSTOM_IMAGE:
                                    $arr['custom_image'] = $option->image;
                                    break;
                            }
                        }

                        $full_custom['customizer'][$c_id][] = $arr;
                    }
                }
            }
        }

        return ['customizer' => $full_custom['customizer'], 'image' => self::generateCustomerImage($hash)];
    }

    public static function update_state_customizer($hash, $field)
    {
        if (!empty($hash)) {
            global $wpdb;
            $cust       = self::get_saved_customizer($hash);
            $new_state  = (int)!($cust->{$field});
            $name_table = $wpdb->prefix . self::SAVE_TABLE_NAME;
            $wpdb->update(
                $name_table,
                array($field => $new_state),
                array('id' => $cust->id,),
                array('%d')
            );
        }
    }

    /**
     * @param array $params
     * @param null  $content
     *
     * @return mixed|null|string|void
     */
    function get_customizer_widget($params, $content = null)
    {
        global $post;
        global $customizer_widget;
        $customizer_widget = true;

        $customizer_id = !empty($params['customizer_id']) ? $params['customizer_id'] : (!empty($params['id']) ? $params['id'] : '');
        $customizer    = get_post($customizer_id);

        if (!$customizer) {
            return apply_filters('insert_pages_not_found_message', $content);
        }
        $this->widget_customizer_id = $customizer_id;
        $this->widget_old_post_id   = get_the_ID();

        $post     = $customizer;
        $template = $this->switch_template(['widget' => $customizer_id]);

        $js  = $this->get_scripts_by_template(true, true, $customizer_id);
        $css = $this->get_scripts_by_template(false, true, $customizer_id);
        wp_enqueue_script($this->plugin_name . '_widget_template_js', $js, ['jquery'], $this->version, true);
        wp_enqueue_style($this->plugin_name . '_widget_template_css', $css, [], $this->version, false);

        ob_start();
        echo '<div class="customizer_widget">';
        include_once $template;
        echo '</div>';

        $content = ob_get_contents();
        ob_end_clean();

        $post = get_post($this->widget_old_post_id);
        return $content;
    }

    /**
     * @param $hash
     */
    public static function increase_view_count($hash)
    {
        global $wpdb;
        $customizer = self::get_saved_customizer($hash);
        if (!$customizer) {
            return;
        }
        $new_count  = $customizer->count_view + 1;
        $name_table = $wpdb->prefix . self::SAVE_TABLE_NAME;
        $wpdb->update(
            $name_table,
            array('count_view' => $new_count),
            array('id' => $customizer->id,),
            array('%d')
        );
    }

    /**
     * @param $hash
     *
     * @return array
     */
    public static function generateCustomerImage($hash)
    {
        ob_start();
        $saved_customizer = Customizer_Public::get_saved_customizer($hash);
        if (empty($saved_customizer)) {
            return '';
        }

        $data = json_decode(str_replace('\"', '"', $saved_customizer->selected_options), true);
        $data = self::validateSelectedOptions($data);
        if (empty($data)) {
            return '';
        }
        $all_images = self::get_all_customizer_images($data);
        $url        = [];

        foreach ($all_images as $slide => $images) {
            if (empty($images)) {
                $url[$slide] = '';
                continue;
            }

            $path        = '/uploads/customizer/customer/';
            $name        = $hash . '_' . $slide . '.jpg';
            $url[$slide] = get_home_url() . '/wp-content' . $path . $name;
            $file        = ABSPATH . 'wp-content' . $path . $name;
            if (file_exists($file)) {
                continue;
            }

            $uploadFolder = WP_CONTENT_DIR . $path;

            if (!file_exists($uploadFolder)) {
                wp_mkdir_p($uploadFolder);
            }
            list($width, $height) = self::getMaxWidthHeight($images);

            $canvas = imagecreatetruecolor($width, $height);
            imagesavealpha($canvas, true);

            $transLayerOverlay = imagecolorallocatealpha($canvas, 225, 225, 225, 127);
            imagefill($canvas, 0, 0, $transLayerOverlay);

            foreach ($images as $option_id => $image) {

                if ($image == self::COMPONENT_TYPE_CUSTOM_IMAGE) {
                    $customImage = self::get_image_with_image($width, $height, $data, $option_id, false);
                    imagecopyresampled($canvas, $customImage, 0, 0, 0, 0, $width, $height, $width, $height);

                } else if ($image == self::COMPONENT_TYPE_CUSTOM_TEXT) {
                    $textImage = self::get_image_with_text($width, $height, $data, $option_id, false);
                    imagecopyresampled($canvas, $textImage, 0, 0, 0, 0, $width, $height, $width, $height);

                } else if (!empty($image)) {
                    if (substr($image, -3) === 'gif') {
                        $pngNameArr = explode('/', $image);
                        $pngName    = end($pngNameArr);

                        $pngName = str_replace('gif', 'png', $pngName);
                        array_pop($pngNameArr);
                        $pngNameArr[] = $pngName;
                        $pngName      = implode('/', $pngNameArr);

                        imagepng(imagecreatefromstring(file_get_contents($image)), ABSPATH . $pngName);
                        $image = $pngName;
                    }

                    $img = false;

                    if (exif_imagetype($image) === 3) { //'image/png'
                        $img = imagecreatefrompng($image);
                    } elseif (exif_imagetype($image) === 2) { //'image/jpeg'
                        $img = imagecreatefromjpeg($image);
                    }

                    list($iWidth, $iHeight) = getimagesize($image);
                    if ($img) {
                        imagecopyresampled($canvas, $img, 0, 0, 0, 0, $iWidth, $iHeight, $iWidth, $iHeight);
                    }
                }
            }

            $name = $hash . '_' . $slide . '.png';
            imagepng($canvas, $uploadFolder . '/' . $name, 9);

//        save as jpg
            $input_file  = $uploadFolder . '/' . $name;
            $output_file = $uploadFolder . '/' . $hash . '_' . $slide . '.jpg';
            $input       = imagecreatefrompng($input_file);
            list($width, $height) = getimagesize($input_file);
            $output = imagecreatetruecolor($width, $height);
            $white  = imagecolorallocate($output, 255, 255, 255);
            imagefilledrectangle($output, 0, 0, $width, $height, $white);
            imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
            imagejpeg($output, $output_file);
            unlink($input_file);
        }
        ob_end_clean();
        return $url;
    }

    public static function add_meta_tags()
    {
        $html = '';
        if (empty($_GET['hash'])) {
            return;
        }
        $saved_customizer = self::get_saved_customizer($_GET['hash']);
        if (empty($saved_customizer)) {
            return;
        }
        $image_url = self::generateCustomerImage($saved_customizer->hash);
        if (!empty($image_url) && is_array($image_url)) {
            $image_url = htmlspecialchars($image_url[0]);
        } else {
            $image_url = '';
        }
        $customizer_url = htmlspecialchars(self::get_customizer_link(self::get_customizer_id(), $saved_customizer->hash));
        $price          = self::get_saved_customizer_price($saved_customizer);
        $price          = number_format((float)$price, 2);
        $price_format   = get_woocommerce_price_format();

        $html  .= '<meta itemprop="name" content="' . $saved_customizer->name_customizer . '">';
        $title = $saved_customizer->name_customizer . ' - ' . sprintf($price_format, get_woocommerce_currency_symbol(), $price);
        //twitter meta tags
        $html .= '<meta name="twitter:card" content="summary" />';
        $html .= '<meta name="twitter:title" content="' . $title . '">';
        $html .= '<meta name="twitter:image:src" content="' . $image_url . '">';

        //Facebook, Pinterest, LinkedIn, Google+ meta tags
        $html .= '<meta property="og:type" content="article" />';
        $html .= '<meta property="og:url" content="' . $customizer_url . '" />';
        $html .= '<meta property="og:title" content="' . $title . '" />';
        $html .= '<meta property="og:description" content="' . $saved_customizer->name_customizer . '" />';
        $html .= '<meta property="og:image" content="' . $image_url . '" />';
        //product tags
        $html .= '<meta name="product:price:amount" content="' . $price . '">';
        $html .= '<meta name="product:price:currency" content="' . get_woocommerce_currency() . '">';
        echo $html;
    }

    public function change_title_saved_customizer_page($title)
    {
        if (!empty($_GET['hash'])) {
            $saved_customizer = self::get_saved_customizer($_GET['hash']);
            if (!empty($saved_customizer)) {
                return $saved_customizer->name_customizer;
            }
        }
        return $title;
    }

    public function prefix_body_class($classes)
    {
        $classes[] = 'woocommerce';

        return $classes;
    }

    public static function get_image_full_url($image_url)
    {
        if (is_array($image_url)) {
            if (!empty($image_url[0])) {
                $image_url = $image_url[0];
            } else return '';
        }
        if (substr($image_url, 0, 1) === '/') {
            $domen_url = wp_get_upload_dir();
            $image_url = $domen_url['baseurl'] . $image_url;
        }

        return $image_url;
    }
}
