jQuery(document).ready(function($) {
    $('.spotim-rules-table').each(function(){
        var that = $(this);
        that.find('[name="spotim_options[spotim-rules][param]"]').on('change', function($event) {
            var data = {
                action: 'spot_im_get_options',
                rule: $(this).val()
            };

            $.post(ajax_url, data, function(options) {
                var select = that.find('[name="spotim_options[spotim-rules][value]"]');

                if (_.isEmpty(options)) {
                    select.css('display', 'none');
                    select.empty();
                } else {
                    select.css('display', 'block');
                    select.empty();

                    _.each(options, function(value, key) {
                        select.append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        })

        that.find('.button').on('click', function($event) {
            $(this)
                .addClass('active')
                .siblings('.button')
                    .removeClass('active')
                    .end()
                .siblings('[name="spotim_options[spotim-rules][visible]"]')
                    .attr('value', $(this).attr('value'));

            $event.preventDefault();
        });
    });
});