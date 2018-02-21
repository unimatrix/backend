/**
 * Picky widget
 *
 * @author Flavius
 * @version 1.0
 */
if(typeof Widgets === 'undefined') var Widgets = {};
Widgets.picky = function() { 'use strict';
    var store = {
        empty: '_to_empty_array_',

    // handle binds
    }, _bind = function() {
        // go through each picky
        $('div.picky-widget').each(function() {
            var container = $(this),
                list = container.find('div.list'),
                name = list.siblings('input[type="hidden"]:first').attr('name');

            // on click event
            list.find('pick').on('click', function() {
                // visual only
                $(this).toggleClass('active');

                // recalculate what is selected
                var values = [];
                list.children('pick.active').each(function() {
                    values.push($(this).text());
                });

                // change inputs
                list.siblings('input[type="hidden"]').remove();
                if(values.length > 0)
                    for(let i = 0; i < values.length; i++)
                        container.append($('<input/>', {type: 'hidden', name: name, value: values[i]}));
                else container.append($('<input/>', {type: 'hidden', name: name, value: store.empty}));
            })
        });

    // init
    }, __construct = function() {
        _bind();
    };

    // public, yay
    return {
        init: __construct
    };
}();

//init widget on ready
$(Widgets.picky.init);
