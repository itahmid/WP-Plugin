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

require_once(__DIR__ . '/formhelper.class.php');

class SpotIM_Options extends FormHelper {

    public $options;

    public function __construct() {
        $this->options = get_option('spotim_options');

        if (is_admin()) {
            $this->register_settings_and_fields();
        }
    }

    public static function add_menu_page() {
        add_options_page('Spot.IM', 'Spot.IM', 'manage_options', __FILE__, array('SpotIM_Options', 'options_view'));
    }

    public function register_settings_and_fields() {
        register_setting('spotim_options', 'spotim_options');

        $add_form_data = array(
            array(
                'id' => 'spotim_main_section',
                'title' => 'Main',
                'callback' => 'main_section_Callback',
                'children' => array(
                    array(
                        'id' => 'spotim_id',
                        'title' => 'Spot\'s ID',
                        'callback' => 'spot_id_Text',
                        'section' => 'spotim_main_section',
                    ),
                    array(
                        'id' => 'spotim_position',
                        'title' => 'Position',
                        'callback' => 'spot_position_Select',
                        'section' => 'spotim_main_section',
                    ),
                    array(
                        'id' => 'spotim_state',
                        'title' => 'State',
                        'callback' => 'spot_state_Select',
                        'section' => 'spotim_main_section',
                    ),
                    array(
                        'id' => 'spotim_power',
                        'title' => 'Power On',
                        'callback' => 'spot_power_Check',
                        'section' => 'spotim_main_section',
                    )
                )
            ),
            array(
                'id' => 'spotim_experimental_section',
                'title' => 'Experimental',
                'callback' => 'experimental_section_Callback',
                'children' => array(
                    array(
                        'id' => 'spotim_mobile',
                        'title' => 'Mobile On',
                        'callback' => 'spot_mobile_Check',
                        'section' => 'spotim_experimental_section',
                    )
                )
            )
        );

        foreach ($add_form_data as $section) {
            add_settings_section($section['id'], $section['title'], array($this, $section['callback']), __FILE__);

            foreach ($section['children'] as $field) {
                add_settings_field($field['id'], $field['title'], array($this, $field['callback']), __FILE__, $field['section']);
            }
        }

    }

    public static function main_section_Callback() {}
    public static function experimental_section_Callback() {}

    // FIELDS
    public function add_form_fields($fields, $data) {
        echo $this->addFields($fields, $data);
    }

    public function spot_id_Text() {
        $value = empty($this->options['spotim_id'])?'':$this->options['spotim_id'];

        echo '<input name="spotim_options[spotim_id]" type="text" value="'.$value.'" />';
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

    public function spot_power_Check() {
        $value = empty($this->options['spotim_power'])?'':$this->options['spotim_power'];

        echo '<input name="spotim_options[spotim_power]" type="checkbox" value="1" '.checked($value, 1, 0).' />';
    }

    public function spot_mobile_Check() {
        $value = empty($this->options['spotim_mobile'])?'':$this->options['spotim_mobile'];

        echo '<input name="spotim_options[spotim_mobile]" type="checkbox" value="true" '.checked($value, "true", 0).' />';
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
            SpotIM_Options::add_menu_page();
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
