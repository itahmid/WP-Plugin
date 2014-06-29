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

class SpotIM_Options {

    public $options;

    public function __construct() {
        $this->options = get_option('spotim_options');

        if (is_admin()) {
            $this->register_settings_and_fields();
        }

    }

    public static function add_menu_page() {
        add_options_page('Spot.IM', 'Spot.IM', 'manage_options', __FILE__,array('SpotIM_Options', 'options_view'));
    }

    public function register_settings_and_fields () {
        register_setting('spotim_options', 'spotim_options');

        add_settings_section(
            'spotim_main_section',
            'Main',
            array($this, 'main_section_Callback'),
            __FILE__
        );

        add_settings_field(
            'spotim_id',
            'Spot\'s ID',
            array($this, 'spot_id_Text'),
            __FILE__,
            'spotim_main_section'
        );

        add_settings_field(
            'spotim_position',
            'Position',
            array($this, 'spot_position_Select'),
            __FILE__,
            'spotim_main_section'
        );

        add_settings_field(
            'spotim_state',
            'State',
            array($this, 'spot_state_Select'),
            __FILE__,
            'spotim_main_section'
        );

        add_settings_field(
            'spotim_power',
            'Power On',
            array($this, 'spot_power_Check'),
            __FILE__,
            'spotim_main_section'
        );


        add_settings_section(
            'spotim_experimental_section',
            'Experimental',
            array($this, 'experimental_section_Callback'),
            __FILE__
        );

        add_settings_field(
            'spotim_mobile',
            'Mobile On',
            array($this, 'spot_mobile_Check'),
            __FILE__,
            'spotim_experimental_section'
        );
    }

    public static function main_section_Callback() {}
    public static function experimental_section_Callback() {}

    // FIELDS
    public function spot_id_Text() {
        $value = empty($this->options['spotim_id']) ? '' : $this->options['spotim_id'];

        echo '<input name="spotim_options[spotim_id]" type="text" value="'. $value .'" />';
    }

    public function spot_position_Select() {
        $position = empty($this->options['spotim_position']) ? '' : $this->options['spotim_position'];
        $position_options = array('right', 'left');

        echo '<select name="spotim_options[spotim_position]">';
            foreach($position_options as $option) {
                $selected = $position == $option ? 'selected="selected"' : '';
                echo '<option value='. $option .' '. $selected .'>'. $option .'</option>';
            }
        echo '</select>';
    }

    public function spot_state_Select() {
        $state = empty($this->options['spotim_state']) ? '' : $this->options['spotim_state'];
        $state_options = array(
            'open' => 'spot-state',
            'close' => 'notices-closed-state',
        );

        echo '<select name="spotim_options[spotim_state]">';
            foreach($state_options as $option => $value) {
                $selected = $state == $value ? 'selected="selected"' : '';
                echo '<option value='. $value .' '. $selected .'>'. $option .'</option>';
            }
        echo '</select>';
    }

    public function spot_power_Check() {
        $value = empty($this->options['spotim_power']) ? '' : $this->options['spotim_power'];

        echo '<input name="spotim_options[spotim_power]" type="checkbox" value="1" '. checked($value, 1, 0) .' />';
    }

    public function spot_mobile_Check() {
        $value = empty($this->options['spotim_mobile']) ? '' : $this->options['spotim_mobile'];

        echo '<input name="spotim_options[spotim_mobile]" type="checkbox" value="true" '. checked($value, "true", 0) .' />';
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
            <div id="spot-im-root"></div><script>!function(t,e,o){function p(){var t=e.createElement("script");t.type="text/javascript",t.async=!0,t.src=("https:"==e.location.protocol?"https":"http")+":"+o,e.body.appendChild(t)}t.spotId="<?php echo $this->options['spotim_id']; ?>",t.position="<?php echo $this->options['spotim_position']; ?>",t.state="<?php echo $this->options['spotim_state']; ?>",t.spotName="",t.allowDesktop=!0,t.allowMobile=<?php echo empty($this->options['spotim_mobile']) ? "false" : $this->options['spotim_mobile']; ?>,t.containerId="spot-im-root",p()}(window.SPOTIM={},document,"//www.spot.im/embed/scripts/launcher.js");</script>
        <?php
    }

}


if (is_admin()) {
    add_action('admin_menu', function(){
        SpotIM_Options::add_menu_page();
    });

    add_action('admin_init', function(){
        new SpotIM_Options();
    });
} else {
    add_action('wp_footer', function(){
        $spotim = new SpotIM_Options();

        if ($spotim->options['spotim_power']) {
            $spotim->embed_view();
        }
    });
}
