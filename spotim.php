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
require_once(__DIR__ . '/helpers/rules.php');
require_once(__DIR__ . '/helpers/rules_options.php');

class SpotIM_Options extends FormHelper {

    public $options, $json_settings;

    public function __construct() {
        $this->json_settings = json_decode(
            file_get_contents( __DIR__ . '/data.json', true)
        );

        $this->options = get_option($this->json_settings->option_name);

        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_and_register_my_scripts'));
            add_action('wp_ajax_spot_im_get_options', array($this, 'get_options'));

            $this->register_form($this->json_settings);
        }
    }

    public function enqueue_and_register_my_scripts() {
        wp_enqueue_script('spot_im_admin_js', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery', 'underscore'));
        wp_localize_script('spot_im_admin_js', 'ajax_url', admin_url('admin-ajax.php'));
        wp_localize_script('spot_im_admin_js', 'rulesData', $this->options['rules']);
        wp_register_style('spot_im_admin_css', plugin_dir_url( __FILE__ ) . 'css/admin.css', false, '1.1.0');
        wp_enqueue_style( 'spot_im_admin_css' );
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
            $section->callback = isset($section->callback) ? array($this, $section->callback) : function(){};

            add_settings_section(
                $section->id,
                $section->title,
                $section->callback,
                'spotim.php'
            );

            if (!empty($section->fields)) {
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

                    if (isset($field->description)) {
                        $args['description'] = $field->description;
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
    }

    public function get_options() {
        $rules_options = new RulesOptionsHelper();

        header('Content-Type: application/json');
            echo json_encode($rules_options->get($_GET['rule']));
        die();
    }

    public function rules_test() {
        return !empty($this->options['spotim_rules']) ? RulesHelper::test($this->options['spotim_rules']) : false;
    }

    public function validate_form($options) {
        return $options;
    }

    // Views
    public function admin_view() {
        $this->addView(__DIR__.'/views/options.php');
    }

    public function rules_view() {
        $this->addTemplate(__DIR__.'/views/rules.html');
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
        if (!empty($spotim->options['spotim_id']) && $spotim->rules_test()) {
            $spotim->addTemplate(__DIR__.'/views/embed.html', $spotim->options);
        }
    });
}

if (defined( 'DOING_AJAX' ) && DOING_AJAX) {

}