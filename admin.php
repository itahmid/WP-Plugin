<?php
/**
 * Spot.IM
 *
 * Official Spot.IM WP Plugin
 *
 * @package   Spot_IM
 * @author    Idan Mitrofanov (@idanm) <idanm@spot.im>
 * @license   GPL-2.0+
 * @link      http://www.spot.im
 * @copyright 2014 Spot.IM
 *
 * @wordpress-plugin
 * Plugin Name:       Spot.IM
 * Plugin URI:        http://www.spot.im
 * Description:       Official Spot.IM WP Plugin
 * Version:           1.0.0
 * Author:            Idan Mitrofanov (@idanm_)
 * Author URI:      https://github.com/idanm
 * Text Domain:       SpotIM
 * GitHub Plugin URI: git@github.com:idanm/SpotIM-WP-Plugin.git
 */

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
            __FILE__,
            array($this, $this->json_settings->page_options->view)
        );
    }

    public function register_form($data) {
        register_setting($data->option_name, $data->option_name);

        foreach ($data->sections as $section) {
            add_settings_section(
                $section->id,
                $section->title,
                array($this, $section->callback),
                __FILE__
            );

            foreach ($section->fields as $field) {
                $value  = !empty($this->options[$field->id]) ? $this->options[$field->id] : '';

                add_settings_field(
                    $field->id,
                    $field->title,
                    array($this, $field->callback),
                    __FILE__,
                    $field->section,
                    array(
                        'id' => $field->id,
                        'type' => $field->type,
                        'group' => $data->option_name,
                        'value' => $value
                    )
                );
            }
        }
    }

    public static function main_section_Callback() {}
    public static function experimental_section_Callback() {}

    // FIELDS
    public function add_field($field) {
        echo $this->addField($field);
    }

    public function spot_position_Select() {
        $position         = empty($this->options['spotim_position'])?'':$this->options['spotim_position'];
        $position_options = array('right', 'left');

        echo '<select name="spotim_options[spotim_position]">';
        foreach ($position_options as $option) {
            $selected = $position == $option?'selected="selected"':'';
            echo '<option value='.$option.' '.$selected.'>'.$option.'</option>';
        }
        echo '</select>';
    }

    public function spot_state_Select() {
        $state         = empty($this->options['spotim_state'])?'':$this->options['spotim_state'];
        $state_options = array(
            'open'  => 'spot-state',
            'close' => 'notices-closed-state',
        );

        echo '<select name="spotim_options[spotim_state]">';
        foreach ($state_options as $option => $value) {
            $selected = $state == $value?'selected="selected"':'';
            echo '<option value='.$value.' '.$selected.'>'.$option.'</option>';
        }
        echo '</select>';
    }

    // Views
    public static function options_view() {
        ?>
            <div class="wrap">
                <h2>Spot.IM Options</h2>
                <form action="options.php" method="post">
                    <?php
                        settings_fields('spotim_options');
                        do_settings_sections(__FILE__);
                        submit_button();
                    ?>
                </form>
            </div>
        <?php
    }

    public function embed_view() {
        ?>
            <div id="spot-im-root"></div><script>!function(t,e,o){function p(){var t=e.createElement("script");t.type="text/javascript",t.async=!0,t.src=("https:"==e.location.protocol?"https":"http")+":"+o,e.body.appendChild(t)}t.spotId="<?php echo $this->options['spotim_id'];?>",t.position="<?php echo $this->options['spotim_position'];?>",t.state="<?php echo $this->options['spotim_state'];?>",t.spotName="",t.allowDesktop=!0,t.allowMobile=<?php echo empty($this->options['spotim_mobile'])?"false":$this->options['spotim_mobile'];?>,t.containerId="spot-im-root",p()}(window.SPOTIM={},document,"//www.spot.im/embed/scripts/launcher.js");</script>
        <?php
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
            $spotim->embed_view();
        }
    });
}
