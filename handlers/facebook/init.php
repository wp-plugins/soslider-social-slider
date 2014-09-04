<?php
add_filter('soslider_modules', array('SOSliderFacebook', '_menu_register'));
add_action('soslider_handlers_init', array('SOSliderFacebook', '_handler_init'));
add_filter('soslider_active_handlers', array('SOSliderFacebook', '_active_handler'));
add_action('soslider_handlers_output', array('SOSliderFacebook', '_handle_output'));
add_filter('soslider_handlers_state', array('SOSliderFacebook', '_handler_state'));
add_filter('soslider_handlers_to_visual_config', array('SOSliderFacebook', '_handler_to_vs'));
add_action('soslider_set_left_slider_facebook', array('SOSliderFacebook', '_set_left_slider'), 1, 4);
add_action('soslider_set_right_slider_facebook', array('SOSliderFacebook', '_set_right_slider'), 1, 4);
add_action('soslider_set_disabled_slider_facebook', array('SOSliderFacebook', '_set_disabled_slider'), 1, 1);
add_filter('soslider_selected_button_height_facebook', array('SOSliderFacebook', '_selected_button_height'));
add_action('soslider_set_slider_offset_facebook', array('SOSliderFacebook', '_set_slider_offset'), 1, 2);
add_filter('soslider_max_height_for_left', array('SOSliderFacebook', '_max_height_for_left'), 1, 1);
add_filter('soslider_max_height_for_right', array('SOSliderFacebook', '_max_height_for_right'), 1, 1);
add_action('soslider_activation_hook', array('SOSliderFacebook', '_set_defaults'));

class SOSliderFacebook extends SOAbstractHandler {

    static public function _handler_init() {
        if (is_admin()) {
            $bo = self::_get_fields();
            foreach ($bo as $items) {
                foreach ($items as $item) {
                    if ($item->type != SOOption::MULTIPLE) {
                        register_setting('soslider_facebook', $item->get_name());
                    } else {
                        foreach ($item->choices as $option) {
                            register_setting('soslider_facebook', $option->get_name());
                        }
                    }
                }
            }
        }
    }

    static public function _button_sizes() {
        return array(
            '0' => array('width' => 24, 'height' => 24),
            '1' => array('width' => 24, 'height' => 24),
            '2' => array('width' => 36, 'height' => 36),
            '3' => array('width' => 36, 'height' => 36),
            '4' => array('width' => 48, 'height' => 48),
            '5' => array('width' => 48, 'height' => 48),
            '6' => array('width' => 24, 'height' => 78),
            '7' => array('width' => 24, 'height' => 78),
            '8' => array('width' => 24, 'height' => 78),
            '9' => array('width' => 24, 'height' => 78),
            '10' => array('width' => 48, 'height' => 155),
            '11' => array('width' => 48, 'height' => 155),
            '12' => array('width' => 48, 'height' => 155),
            '13' => array('width' => 48, 'height' => 155),
        );
    }

    static public function _get_images() {
        return array(
            array('0', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/0_left.png" />'),
            array('1', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/1_left.png" />'),
            array('2', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/2_left.png" />'),
            array('3', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/3_left.png" />'),
            array('4', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/4_left.png" />'),
            array('5', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/5_left.png" />'),
            array('6', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/6_left.png" />'),
            array('7', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/7_left.png" />'),
            array('8', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/8_left.png" />'),
            array('9', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/9_left.png" />'),
            array('10', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/10_left.png" />'),
            array('11', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/11_left.png" />'),
            array('12', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/12_left.png" />'),
            array('13', '<img src="' . SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/13_left.png" />'),
        );
    }

    static public function _get_fields() {
        $bo = SOOptionBase::base_options('facebook');
        foreach ($bo as $level => $items) {
            foreach ($items as $item) {
                switch ($item->name) {
                    case 'facebook_border_color' : $item->title = __('Border color (Default: #3B5998)', 'soslider');
                        $item->value = '#3B5998';
                        break;
                    case 'facebook_background_color' : $item->title = __('Background color (Default: #FFFFFF)', 'soslider');
                        $item->value = '#FFFFFF';
                        break;
                }
            }
        }

        $nb = new SOOption();
        $nb->title = __('Facebook slider button', 'soslider');
        $nb->value = 0;
        $nb->type = SOOption::RADIO;
        $nb->name = 'facebook_slider_button';
        $nb->choices = self::_get_images();
        $bo[1][] = $nb;

        $nb = new SOOption();
        $nb->title = __('Facebook custom slider button', 'soslider');
        $nb->type = SOOption::MULTIMEDIA;
        $nb->name = 'facebook_custom_slider_button';
        $nb->suffix = __('Overrides slider button setting. Leave empty to use above setting.', 'soslider');
        $bo[1][] = $nb;

        $nb = new SOOption();
        $nb->title = __('Facebook button extra margin', 'soslider');
        $nb->value = 0;
        $nb->value_filter = 'intval';
        $nb->type = SOOption::TEXT;
        $nb->suffix = 'px';
        $nb->name = 'facebook_button_margin';
        $nb->visible = SOOptionBase::is_used_visual_designer();
        $bo[1][] = $nb;

        $nb = new SOOption();
        $nb->title = __('Color scheme', 'soslider');
        $nb->value = 'light';
        $nb->type = SOOption::RADIO;
        $nb->name = 'facebook_color_scheme';
        $nb->choices = array(
            array('light', __('Light', 'soslider')),
            array('dark', __('Dark', 'soslider')),
        );
        $bo[1][] = $nb;

        $nb = new SOOption();
        $nb->name = 'facebook_faces';
        $nb->title = __('Show faces', 'soslider');
        $nb->value = true;
        $nb->type = SOOption::CHECKBOX;
        $bo[6][] = $nb;

        $nb = new SOOption();
        $nb->name = 'facebook_header';
        $nb->title = __('Show header', 'soslider');
        $nb->value = true;
        $nb->type = SOOption::CHECKBOX;
        $bo[6][] = $nb;

        $nb = new SOOption();
        $nb->name = 'facebook_stream';
        $nb->title = __('Show stream', 'soslider');
        $nb->value = false;
        $nb->type = SOOption::CHECKBOX;
        $bo[6][] = $nb;

        $nb = new SOOption();
        $nb->name = 'facebook_url';
        $nb->title = __('Facebook URL', 'soslider');
        $nb->type = SOOption::TEXT;
        $nb->prefix = 'http://www.facebook.com/';
        $bo[6][] = $nb;

        $nb = new SOOption();
        $nb->name = 'facebook_lang';
        $nb->title = __('Language', 'soslider');
        $nb->type = SOOption::SELECT;
        $nb->value = (WPLANG != '') ? WPLANG : "en_US";
        $nb->choices = array(
            array(
                'en_US',
                'English (US)',
            )
        );
        $body = get_transient( 'soslider_cache_fb_langs' );
        if ( false === $body ) {
            $body = wp_remote_get(
                'http://www.facebook.com/translations/FacebookLocales.xml'
            );
        }
        if ( ! is_wp_error( $body ) ) {
            $body        = is_array( $body ) ? $body['body'] : $body;
            $nb->choices = array();
            $xml         = simplexml_load_string( $body );
            foreach ($xml->locale as $element) {
                $name = (string) $element->englishName;
                $code = (string) $element->codes->code->standard->representation;
                $nb->choices[] = array($code, $name);
            }
            set_transient( 'soslider_cache_fb_langs', $body, 12 * HOUR_IN_SECONDS );
        }

        $bo[6][] = $nb;

        return $bo;
    }

    static public function _menu_register($modules) {
        $modules['Facebook'] = array(
            'ident' => 'soslider_facebook',
            'menuhandler' => array('SOSliderFacebook', '_menu_handler')
        );
        return $modules;
    }

    static public function _menu_handler() {
        wp_enqueue_script('so-admin-config');
        ?><div class="wrap">
            <h3><?php _e('Facebook Slider Options', 'soslider'); ?></h3>
        </div>
        <?php
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
            echo '<h4 class="so-set-upd">' . __('Settings updated', 'soslider') . '</h4>';
            self::_after_options_save();
        }
        $bo = self::_get_fields();
        print '<form method="post" action="options.php"> ';
        settings_fields('soslider_facebook');
        do_settings_fields('soslider_facebook', '');
        ?>
        <table class="form-table">
            <?php
            foreach ($bo as $items) {
                foreach ($items as $item) {
                    $visibility = ($item->visible) ? '' : ' style="display: none;"';
                    $item->value = get_option($item->get_name(), $item->value);
                    ?>
                    <tr valign="top"<?php echo $visibility; ?>>
                        <th scopre="row"><?php echo $item->get_title() ?></th>
                        <td>
                    <?php echo $item->render_input(); ?>
                        </td>
                    </tr>
            <?php } ?>
        <?php } ?>
        </table>
        <?php
        submit_button(__('Save settings', 'soslider'));
        ?></form><?php
    }

    static public function _active_handler($val) {
        $i = 'sos_option_facebook_active';
        $v = get_option($i);
        if ($v === 'on') {
            return true;
        } else {
            return $val;
        }
    }

    static public function _handle_output() {
        if (! self::_active_handler(false)) {
            return;
        }

        echo '<div id="sos_fb_slider"></div>';
        $js_elements = null;
        $fields = self::_get_fields();
        $table = array();
        foreach ($fields as $level => $items) {
            foreach ($items as $item) {
                if ($item->type != SOOption::MULTIPLE) {
                    $table[$item->name] = get_option($item->get_name(), $item->get_value());
                } else {
                    foreach ($item->choices as $option) {
                        $table[$option->name] = get_option($option->get_name(), $option->get_value());
                    }
                }
            }
        }
        $fbb = $table['facebook_slider_button'];
        $orientation = $table['facebook_position'];
        $nb = new SOOption();
        $nb->name = 'facebook_use_custom_slider_button';
        $vcsb = get_option($nb->get_name(), false);
        if (! $vcsb) {
            $sciezka = SOSLIDER_PLUGIN_URL . 'handlers/facebook/imgs/' . $fbb . '_' . $orientation . '.png';
        } else {
            $nb->name = 'facebook_custom_slider_button';
            $sciezka = get_option($nb->get_name(), null);
        }
        $nb->name = 'soslider_slider_speed';
        $speed = get_option($nb->get_name(), 500);
        $nb->name = 'soslider_slider_behaviour';
        $behaviour = get_option($nb->get_name(), 'mouseover');
        if (! isset($table['image_margin'])) {
            $table['image_margin'] = 0;
        }
        if (! isset($table['image_position'])) {
            $table['image_position'] = 0;
        }
        if (! isset($table['load_method'])) {
            $table['load_method'] = 0;
        }

        $js_elements .= 'jQuery("#sos_fb_slider").soslider( {';
        $js_elements .= 'debug: false,';
        $js_elements .= 'width: "' . $table['facebook_width'] . '",';
        $js_elements .= 'height: "' . $table['facebook_height'] . '",';
        $js_elements .= 'top: "0",';
        $js_elements .= 'left: "0",';
        $js_elements .= 'middle: true,';
        $js_elements .= 'format_class: "soslider_class",';
        $js_elements .= 'orientation: "' . $orientation . '",';

        $js_elements .= 'border_width: "' . $table['facebook_border'] . '",';
        $js_elements .= 'border_color: "' . $table['facebook_border_color'] . '",';
        $js_elements .= 'border_radius: "' . sprintf('%dpx %dpx %dpx %dpx', $table['facebook_round_left_top'], $table['facebook_round_right_top'], $table['facebook_round_right_bottom'], $table['facebook_round_left_bottom']) . '",';
        $js_elements .= 'background_color: "' . $table['facebook_background_color'] . '",';

        $js_elements .= 'image_url: "' . $sciezka . '",';
        $js_elements .= 'image_margin: ' . intval(@$table['image_margin']) . ',';
        $js_elements .= 'image_extra_margin: ' . intval(@$table['facebook_button_margin']) . ',';
        if (! $vcsb) {
            $bs = self::_button_sizes();
            $s = $bs[$fbb];
        } else {
            $nb->name = 'facebook_cutom_slider_button_dimensions';
            $s = get_option($nb->get_name());
        }

        $js_elements .= 'image_height: ' . $s['height'] . ',';
        $js_elements .= 'image_width: ' . $s['width'] . ',';
        $js_elements .= 'image_position: ' . intval(@$table['image_position']) . ',';
        $js_elements .= 'image_position_relative: "' . @$table['facebook_position_vertical'] . '",';
        $js_elements .= 'slide_speed: "' . $speed . '",';
        $js_elements .= 'run_event: "' . $behaviour . '",';
        $js_elements .= 'load_method: "' . @$table['load_method'] . '"';
        $js_elements .= '});';

        $face_url = "http://www.facebook.com/" . $table['facebook_url'];
        $height = intval($table['facebook_height']) - intval($table['facebook_border']) * 2;
        if ($table['facebook_header'] != 'on') {
            $height -= 5;
        }
        $code = sprintf('<div class="fb-like-box" data-href="%s" data-width="%d" data-height="%d" data-colorscheme="%s" data-show-faces="%s" data-header="%s" data-stream="%s" data-show-border="%s"></div>', $face_url, $table['facebook_width'], $height, $table['facebook_color_scheme'], (@$table['facebook_faces'] ? "true" : "false"), (@$table['facebook_header'] ? "true" : "false"), (@$table['facebook_stream'] ? "true" : "false"), "false");
        $code .= sprintf('<div id="sos-fbw" style="position: absolute; top: 50%%; left: 40%%;">%s</div>', __('Loading...', 'soslider'));
        ?>
        <script>
            window.fbAsyncInit = function() {
                FB.init({status: true, cookie: true, xfbml: true});
                FB.Event.subscribe("xfbml.render", function() {
                    jQuery('#sos-fbw').hide();
                });
            };
        </script>
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/<?php echo $table['facebook_lang']; ?>/all.js#xfbml=1";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
        <?php
        echo '<script type="text/javascript">';
        echo 'jQuery(function () {';
        echo $js_elements;
        if ($face_url != '') {
            $js_elements = 'jQuery("#sos_fb_slider_inner").html(\'' . $code . '\');';
            echo $js_elements;
        }
        echo '});';
        echo '</script>';
    }

    static public function _after_options_save() {
        require_once SOSLIDER_PLUGIN_DIR . 'class/SOCurl.php';
        $nb = new SOOption();
        $nb->name = 'facebook_custom_slider_button';
        $cbt = get_option($nb->get_name(), '');
        $nb->name = 'facebook_use_custom_slider_button';
        update_option($nb->get_name(), 0);
        if ($cbt != null && preg_match('/http[s]?:\/\/.+\.[a-z]{2,10}\//i', $cbt)) {
            $sosc = new SOCurl();
            $dimensions = $sosc->fetch_image_dimensions($cbt);
            if (is_array($dimensions) && isset($dimensions['width']) && isset($dimensions['height'])) {
                $nb->name = 'facebook_cutom_slider_button_dimensions';
                update_option($nb->get_name(), $dimensions);
                $nb->name = 'facebook_use_custom_slider_button';
                update_option($nb->get_name(), 1);
            }
        }
        do_action('soslider_after_save');
    }

    static public function _handler_state($states) {
        $states['Facebook'] = self::_active_handler(false);
        return $states;
    }

    static public function _handler_to_vs($handlers) {
        $nb = new SOOption();
        $nb->name = 'facebook_position';
        $align = get_option($nb->get_name(), 'left');
        $nb->name = 'facebook_slider_button';
        $button = get_option($nb->get_name(), '0');
        $hinfo = array(
            'state' => self::_active_handler(false),
            'align' => $align,
            'images' => self::_get_images(),
            'button' => $button
        );
        if (! is_array($handlers)) {
            $handlers = array();
        }
        $handlers['Facebook'] = $hinfo;
        return $handlers;
    }

    static public function _set_left_slider($imgno, $order, $alignment, $dimensions) {
        $nb = new SOOption();
        $nb->name = 'facebook_active';
        update_option($nb->get_name(), 'on');
        $nb->name = 'facebook_position';
        update_option($nb->get_name(), 'left');
        $nb->name = 'facebook_slider_button';
        update_option($nb->get_name(), $imgno);
        $nb->name = 'facebook_position_vertical';
        update_option($nb->get_name(), $alignment);
        $nb->name = 'sliders_order';
        $v = get_option($nb->get_name(), array());
        $v['left'][$order] = 'facebook';
        update_option($nb->get_name(), $v);
        $nb->name = 'facebook_width';
        update_option($nb->get_name(), intval($dimensions['width']));
        $nb->name = 'facebook_height';
        update_option($nb->get_name(), intval($dimensions['height']));
    }

    static public function _set_right_slider($imgno, $order, $alignment, $dimensions) {
        $nb = new SOOption();
        $nb->name = 'facebook_active';
        update_option($nb->get_name(), 'on');
        $nb->name = 'facebook_position';
        update_option($nb->get_name(), 'right');
        $nb->name = 'facebook_slider_button';
        update_option($nb->get_name(), $imgno);
        $nb->name = 'facebook_position_vertical';
        update_option($nb->get_name(), $alignment);
        $nb->name = 'sliders_order';
        $v = get_option($nb->get_name(), array());
        $v['right'][$order] = 'facebook';
        update_option($nb->get_name(), $v);
        $nb->name = 'facebook_width';
        update_option($nb->get_name(), intval($dimensions['width']));
        $nb->name = 'facebook_height';
        update_option($nb->get_name(), intval($dimensions['height']));
    }

    static public function _set_disabled_slider() {
        $nb = new SOOption();
        $nb->name = 'facebook_active';
        update_option($nb->get_name(), '');
    }

    static public function _selected_button_height($v) {
        return parent::_selected_button_height_p($v, 'facebook', self::_button_sizes());
    }

    static public function _set_slider_offset($offset, $min_height) {
        $nb = new SOOption();
        $nb->name = 'facebook_button_margin';
        update_option($nb->get_name(), $offset);
        $nb->name = 'facebook_height';
//        $v = get_option($nb->get_name(), $min_height);
//        if ($min_height > $v) {
        update_option($nb->get_name(), $min_height);
//        }
        //self::_after_options_save();
    }

    static public function _max_height_for_left($height) {
        $nb = new SOOption();
        $nb->name = 'facebook_position';
        $v = get_option($nb->get_name(), 'left');
        if ('left' != $v || true != self::_active_handler(false)) {
            return $height;
        }
        $nb->name = 'facebook_height';
        $v = get_option($nb->get_name(), 500);
        return ($v > $height) ? $v : $height;
    }

    static public function _max_height_for_right($height) {
        $nb = new SOOption();
        $nb->name = 'facebook_position';
        $v = get_option($nb->get_name(), 'right');
        if ('right' != $v || true != self::_active_handler(false)) {
            return $height;
        }
        $nb->name = 'facebook_height';
        $v = get_option($nb->get_name(), 500);
        return ($v > $height) ? $v : $height;
    }

    static public function _set_defaults() {
        $bo = self::_get_fields();
        parent::_set_defaults_options($bo);
    }

}