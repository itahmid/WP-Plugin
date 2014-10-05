jQuery(document).ready(function($) {
    console.log(rulesData);
    var prefix = 'spot-im-',
        prefixClass = '.' + prefix,
        rulesTable = $(prefixClass + 'rules-table');

    rulesTable.on(prefix + 'getOptions', function($event, selector) {
       var rule = $(selector),
            data = {
                action: 'spot_im_get_options',
                rule: rule.val()
            };

        $.get(ajax_url, data, function(options) {
            var select = rule.parent().find(prefixClass + 'options');

            if (_.isEmpty(options)) {
                select.prop('disabled', true);
                select.empty();
            } else {
                select.prop('disabled', false);
                select.empty();

                _.each(options, function(value, key) {
                    select.append('<option value="' + key + '">' + value + '</option>');
                });
            }
        });
    });

    rulesTable.each(function() {
        var that = $(this);

        that.find(prefixClass + 'rules').on('change', function($event) {
            rulesTable.trigger(prefix + 'getOptions', $(this));
        })

        that.find('.button').on('click', function($event) {
            $(this)
                .addClass('active')
                .siblings('.button')
                    .removeClass('active')
                .end()
                .siblings(prefixClass + 'visible')
                    .attr('value', $(this).attr('value'));

            $event.preventDefault();
        });

        that.find(prefixClass + 'delete').on('click', function($event) {
            $(this).parents(prefixClass + 'row').remove();

            $event.preventDefault();
        });
    });

    $(prefixClass + 'add').on('click', function($event) {
        var cloneRow = rulesTable.find(prefixClass + 'row:last').clone(true),
            rulesSelect = cloneRow.find(prefixClass + 'rules'),
            rulesValue = rulesTable.find(prefixClass + 'row:last ' + prefixClass + 'rules  option:selected').attr('value'),
            nameIndex = parseInt(rulesSelect.attr('name').match(/[0-9]/)[0], 10) + 1,
            rulesSelectName = rulesSelect.attr('name').replace(/[0-9]/, nameIndex),
            optionsSelectName = cloneRow.find(prefixClass + 'options').attr('name').replace(/[0-9]/, nameIndex),
            visibleSelectName = cloneRow.find(prefixClass + 'visible').attr('name').replace(/[0-9]/, nameIndex);

        cloneRow
            .find(prefixClass + 'options').attr('name', optionsSelectName).prop('disabled', false).empty().end()
            .find(prefixClass + 'rules').attr('value', rulesValue).attr('name', rulesSelectName).end()
            .find(prefixClass + 'visible').attr('name', visibleSelectName).end();

            rulesTable.trigger(prefix + 'getOptions', rulesSelect);
            cloneRow.appendTo(rulesTable);

        $event.preventDefault();
    });
});