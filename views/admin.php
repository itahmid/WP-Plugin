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