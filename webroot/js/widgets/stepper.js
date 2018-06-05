/**
 * Stepper widget
 *
 * @author Flavius
 * @version 1.1
 */
if(typeof Widgets === 'undefined') var Widgets = {};
Widgets.stepper = function() { 'use strict';
    var store = {

    // event binding
    }, _bind = function() {
        $('div.stepper-widget > span.button-stepper').on('click', function() {
            var self = $(this),
                parent = self.parent(),
                input = parent.find('input'),
                min = input.data('min') || input.data('min') == 0 ? input.data('min') : -Infinity,
                max = input.data('max') ? input.data('max') : Infinity,
                empty = input.data('empty'),
                skip = input.data('skip'),
                value = input.val() == '' ? false : parseInt(input.val());

            // on button action
            switch(self.hasClass('add') ? '+' : '-') {
                // add
                case '+':
                    if(value === false)
                        value = min ? min - 1 : 0;

                    var calc = value + (skip ? skip : 1);
                    input.val(calc > max ? max : calc);
                break;

                // subtract
                case '-':
                    if(value !== false) {
                        var calc = value - (skip ? skip : 1);
                        input.val(calc < min ? (empty ? '' : min) : calc);
                    }
                break;
            }

            // dispatch event and update message
            input.trigger('change');
            _message(parent);
        });

    // overlap information
    }, _message = function(el) {
        var self = $(el),
            input = self.find('input'),
            text = self.find('span.info-text'),
            empty = input.data('empty'),
            suffix = input.data('suffix'),
            value = parseInt(input.val());

        // empty value?
        if(isNaN(value)) {
            value = empty ? empty : '';
        } else {
            // got suffix?
            if(suffix) {
                var x = suffix.split('|');
                value = value + ' ' + (value == 1 ? x[0] : x[1]);
            }
        }

        // update info-text
        text.html(value);

    // init
    }, __construct = function() {
        _bind();
        $('div.stepper-widget').each(function() {
            _message(this);
        });
    };

    // public, yay
    return {
        init: __construct
    };
}();

// init widget on ready
$(Widgets.stepper.init);