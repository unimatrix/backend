/**
 * Picky widget
 *
 * @author Flavius
 * @version 1.1
 */
if(typeof Widgets === 'undefined') var Widgets = {};
Widgets.picky = function() { 'use strict';
    var store = {
        empty: '_to_empty_array_',
        pickm: '<pick><div><span title="%value%">%value%</span></div></pick>',

    // handle binds
    }, _bind = function() {
        // go through each picky
        $('div.picky-widget').each(function() {
            var container = $(this),
                list = container.find('div.list'),
                name = list.siblings('input[type="hidden"]:first').attr('name');

            // on click event
            list.on('click', 'pick', function() {
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
            });
        });

    // public reset method
    }, reset = function(container) {
        var list = container.find('div.list'),
            name = list.siblings('input[type="hidden"]:first').attr('name'),
            empty = $(store.pickm.replace(/\%value%/g, store.empty)).addClass('empty');

        list.html(empty);
        list.siblings('input[type="hidden"]').remove();
        container.append($('<input/>', {type: 'hidden', name: name, value: store.empty}));

    // public add to list method
    }, add = function(container, value) {
        var list = container.find('div.list'),
            markup = $(store.pickm.replace(/\%value%/g, value));

        list.append(markup);

    // init
    }, __construct = function() {
        _bind();
    };

    // public, yay
    return {
        init: __construct,
        reset: reset,
        add: add
    };
}();

//init widget on ready
$(Widgets.picky.init);
