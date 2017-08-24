/**
 * Tag widget
 * 
 * @author Flavius
 * @version 1.0
 */
if(typeof Widgets === 'undefined') var Widgets = {};
Widgets.tag = function() { 'use strict';
    var store = {

    // check tag list see if its empty, if it is then hide it
    }, _checkList = function(tag) {
        let scope = $(tag.DOM.scope),
            list = scope.siblings('div.list');

        list.toggleClass('hidden', list.find('tag.hidden').length == list.find('tag').length)

    // handle on add event
    }, onAddTag = function(tag, event) {
        let scope = $(tag.DOM.scope),
            list = scope.siblings('div.list');

        list.find('tag').each(function() {
            let self = $(this);
            if(self.text() == event.value) {
                self.css({width: parseFloat(window.getComputedStyle(self.get(0)).width)});
                document.body.clientTop; // force repaint
                self.addClass('hidden');
                _checkList(tag);
            }
        });

    // handle on remove event
    }, onRemoveTag = function(tag, event) {
        let scope = $(tag.DOM.scope),
            list = scope.siblings('div.list');

        list.find('tag.hidden').each(function() {
            let self = $(this);
            if(self.text() == event.value) {
                self.removeAttr('style');
                self.removeClass('hidden');
                _checkList(tag);
            }
        });

    // handle binds
    }, _bind = function(tag) {
        var input = $(tag.DOM.input),
            scope = $(tag.DOM.scope),
            list = scope.siblings('div.list');

        // tag input focus and blur
        input.on('focus', function() {
            scope.addClass('active');
        });
        input.on('blur', function() {
            scope.removeClass('active');
        });

        // add tag in container from list
        list.find('tag').on('click', function() {
            tag.addTag($(this).text());
        })

    // init
    }, __construct = function() {
        // go through each tag
        $('div.tag-widget > textarea').each(function() {
            var self = $(this),
                tag = new Tagify(self.get(0), {
                    autocomplete: false,
                    callbacks: {
                        add: function(e) {
                            onAddTag(tag, e.detail);
                        }, remove: function(e) {
                            onRemoveTag(tag, e.detail);
                        }
                    }
                });

            _checkList(tag);
            _bind(tag);
        });
    };

    // public, yay
    return {
        init: __construct
    };
}();

// init widget on ready
$(Widgets.tag.init);
