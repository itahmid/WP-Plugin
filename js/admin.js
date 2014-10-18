jQuery(document).ready(function($) {
    var prefix = 'spot-im-',
        prefixClass = '.' + prefix,
        rulesTable = $(prefixClass + 'rules-table'),
        nameIndex = 1;

    function getIndex() {
        var output = nameIndex;
        nameIndex = nameIndex + 1;
        return output;
    }

    function generateOptionName(_id, _param) {
        var options = {
            id: _id,
            param: _param
        };

        return 'spotim_options[rules][id][param]'.replace(/(param|id|value)/g,
            function(match) {
                return options[match];
            }
        );
    }

    function addRow(_template, _options) {
        var template = $(_template),
            options = _.extend({
                'id': getIndex(),
                'param': 'all',
                'value': '',
                'visible': 1
            }, _options);

        template
            .find(prefixClass + 'param')
                .attr('name', generateOptionName(options.id, 'param'))
                .attr('value', options.param)
            .end()
            .find(prefixClass + 'value')
                .attr('name', generateOptionName(options.id, 'value'))
                .empty()
            .end()
            .find(prefixClass + 'visible')
                .attr('name', generateOptionName(options.id, 'visible'))
                .attr('value', options.visible)
            .end()
            .find('button')
                .removeClass('active')
            .end()
            .find('button:' + (options.visible == '1' ? 'first' : 'last'))
                .addClass('active');

        rulesTable.trigger(prefix + 'getValueOptions', {
            selector: template.find(prefixClass + 'value'),
            param: options.param,
            value: options.value
        });

        template.appendTo(rulesTable);
    }

    rulesTable.on(prefix + 'getValueOptions', function($event, data) {
        var select = $(data.selector),
            requestData = {
                action: 'spot_im_get_value_options',
                param: data.param
            };

        function selected(value) {
            return (data.value && (data.value === value)) ? 'selected="selected"' : '';
        }

        $.get(ajax_url, requestData, function(options) {
            select.empty();

            if (_.isEmpty(options)) {
                select.prop('disabled', true);
            } else {
                select.prop('disabled', false);

                _.each(options, function(value, key) {
                    select.append('<option value="' + key + '" ' + selected(key) + '>' + value + '</option>');
                });
            }
        });
    });

    rulesTable.each(function() {
        var that = $(this),
            template = $('script[type="templateRuleRow"]').html();

        //  if there are saved rules, they should be parsed
        if (!_.isNull(rulesData)) {
            nameIndex = 0;

            _.each(rulesData, function(data) {
                addRow(template, data);
            });
        } else {
            $(template).appendTo(that);
        }

        // trigger event to get data for value select box
        that.on('change', prefixClass + 'param', function($event) {
            var data = {
                selector: $(this).parent().find(prefixClass + 'value'),
                param: $(this).val()
            };

            rulesTable.trigger(prefix + 'getValueOptions', data);
        });

        // toogle between show and hide buttons
        that.on('click', '.button', function($event) {
            $(this)
                .addClass('active')
                .siblings('.button')
                    .removeClass('active')
                .end()
                .siblings(prefixClass + 'visible')
                    .attr('value', $(this).attr('value'));

            $event.preventDefault();
        });

        // delete a rule
        that.find(prefixClass + 'delete').on('click', function($event) {
            $(this).parents(prefixClass + 'row').remove();
            $event.preventDefault();
        });
    });

    // add a new rule
    $(prefixClass + 'add').on('click', function($event) {
        var template = $('script[type="templateRuleRow"]').html(),
            options = {
                param: 'all',
                value: '',
                visible: 1
            };

        addRow(template, options);

        $event.preventDefault();
    });
});