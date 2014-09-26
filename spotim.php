<?php
/**
 *
 * Official Spot.IM WP Plugin
 *
 * @package   Spot_IM
 * @author      Spot.IM (@Spot_IM) <support@spot.im>
 * @license     GPLv2
 * @link          http://www.spot.im
 * @copyright 2014 Spot.IM Ltd.
 *
 * @wordpress-plugin
 * Plugin Name:     Spot.IM
 * Plugin URI:         http://www.spot.im
 * Description:       Official Spot.IM WP Plugin
 * Version:             1.1.0
 * Author:              Spot.IM (@Spot_IM)
 * Author URI:        https://github.com/SpotIM
 * License:             GPLv2
 * License URI:       license.txt
 * Text Domain:     SpotIM
 * GitHub Plugin URI: git@github.com:SpotIM/WP-Plugin.git
 *
 */

require_once(__DIR__ . '/helpers/utils.php');
require_once(__DIR__ . '/helpers/form.php');

class SpotIM_Options extends FormHelper {

    public $options, $json_settings;

    public function __construct() {
        $this->json_settings = json_decode(
            file_get_contents( __DIR__ . '/data.json', true)
        );

        $this->options = get_option($this->json_settings->option_name);

        if (is_admin()) {
            $this->register_form($this->json_settings);
        }
    }

    public function add_menu_page() {
        add_options_page(
            $this->json_settings->page_options->page_title,
            $this->json_settings->page_options->menu_title,
            $this->json_settings->page_options->capability,
            'spotim.php',
            array($this, $this->json_settings->page_options->view)
        );
    }

    public function register_form($data) {
        register_setting($data->option_name, $data->option_name, array($this, $data->validation_callback));

        foreach ($data->sections as $section) {
            add_settings_section(
                $section->id,
                $section->title,
                function(){},
                'spotim.php'
            );

            foreach ($section->fields as $field) {
                $value  = !empty($this->options[$field->id]) ? $this->options[$field->id] : '';

                $args = array(
                    'id' => $field->id,
                    'type' => $field->type,
                    'group' => $data->option_name,
                    'value' => $value
                );

                if ($field->type === 'select') {
                    $args['options'] = $field->select_options;
                }

                add_settings_field(
                    $field->id,
                    $field->title,
                    array($this, $field->callback),
                    'spotim.php',
                    $field->section,
                    $args
                );
            }
        }
    }

    public function validate_form($options) {

        if (empty($options['spotim_mobile'])) {
            $options['spotim_mobile'] = '0';
        }

        if (empty($options['spotim_id']) && !empty($options['spotim_power'])) {
            $options['spotim_power'] = '0';
        }

        return $options;
    }

    // Views
    public function admin_view() {
        $this->addView(__DIR__.'/views/options.php');
    }
}

if (is_admin()) {
    add_action('admin_menu', function () {
        $spotim = new SpotIM_Options();
        $spotim->add_menu_page();
    });

    add_action('admin_init', function () {
            new SpotIM_Options();
    });
} else {
    add_action('wp_footer', function () {
        $spotim = new SpotIM_Options();

        if ($spotim->options['spotim_power']) {
            $spotim->addTemplate(__DIR__.'/views/embed.html', $spotim->options);
        }
    });
}
