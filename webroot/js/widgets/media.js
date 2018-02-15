/**
 * Media widget
 *
 * @author Flavius
 * @version 1.1
 */
if(typeof Widgets === 'undefined') var Widgets = {};
Widgets.media = function() { 'use strict';
    var store = {
        configs: {},
        ckfinder: {
            width: 925,
            height: 540
        },
        empty: {
            picture: WEBROOT + 'unimatrix/backend/img/widgets/media-plus.png',
            input: '_to_empty_array_'
        }

    /**
     * Fast UUID generator, RFC4122 version 4 compliant.
     * @author Jeff Ward (jcward.com).
     * @license MIT license
     * @link http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript/21963136#21963136
     **/
    }, _uuid = function() {
        var lut = [];
        for (var i = 0; i < 256; i++) 
            lut[i] = (i < 16 ? '0' : '') + (i).toString(16);

        var d0 = Math.random() * 0xffffffff|0;
        var d1 = Math.random() * 0xffffffff|0;
        var d2 = Math.random() * 0xffffffff|0;
        var d3 = Math.random() * 0xffffffff|0;

        return lut[d0&0xff]+lut[d0>>8&0xff]+lut[d0>>16&0xff]+lut[d0>>24&0xff]+'-'+
            lut[d1&0xff]+lut[d1>>8&0xff]+'-'+lut[d1>>16&0x0f|0x40]+lut[d1>>24&0xff]+'-'+
            lut[d2&0x3f|0x80]+lut[d2>>8&0xff]+'-'+lut[d2>>16&0xff]+lut[d2>>24&0xff]+
            lut[d3&0xff]+lut[d3>>8&0xff]+lut[d3>>16&0xff]+lut[d3>>24&0xff];
        
    // ckfinder popup open
    }, _popup = function(url, title, win) {
        let w = store.ckfinder.width,
            h = store.ckfinder.height,
            y = win.top.outerHeight / 2 + win.top.screenY - ( h / 2),
            x = win.top.outerWidth / 2 + win.top.screenX - ( w / 2),
            o = ['location=no', 'menubar=no', 'toolbar=no', 'dependent=yes', 'minimizable=no', 'modal=yes', 'alwaysRaised=yes', 'resizable=yes', 'scrollbars=yes',
                'width=' + w, 'height=' + h, 'top=' + y, 'left=' + x];

        // open popup
        return win.open(url, title, o.join(','));

    // add media
    }, _add = function(ctrl, value) {
        // multiple rule?
        if(ctrl.container.hasClass('multiple')) {
            // found duplicate?
            let dupe = _duplicate(ctrl.container, value);
            if(dupe) {
                var found = $('#'+ dupe);
                found.addClass('media--mark');
                window.setTimeout(function() {
                    found.removeClass('media--mark');
                }, 2000);

            // no duplicate
            } else {
                // from new? clone it
                if(ctrl.media.hasClass('new')) {
                    let clone = ctrl.media.clone();

                    // change cloned attributes and add uuid
                    clone.attr('id', _uuid);
                    clone.removeClass('new');
                    clone.find('img').attr('src', value);
                    clone.find('a').attr('href', value);

                    // add the clone before the actual new element
                    clone.insertBefore(ctrl.media);

                    // bind events and update config
                    _bind(clone);
                    _update();
                // existing
                } else {
                    ctrl.media.find('img').attr('src', value);
                    ctrl.media.find('a').attr('href', value);
                }
            }

            // update multiple values
            _value(ctrl.container);

        // single rule
        } else {
            ctrl.input.val(value);
            ctrl.media.removeClass('new');
            ctrl.media.find('img').attr('src', value);
            ctrl.media.find('a').attr('href', value);
        }

    // remove media
    }, _remove = function(ctrl) {
        // multiple rule?
        if(ctrl.container.hasClass('multiple')) {
            ctrl.media.addClass('deleted');
            window.setTimeout(function() {
                ctrl.media.remove();
                _value(ctrl.container);
            }, 300);

        // single rule
        } else {
            ctrl.input.val('');
            ctrl.media.addClass('new');
            ctrl.media.find('img').attr('src', store.empty.picture);
            ctrl.media.find('a').attr('href', '#');
        }

    // calculate new values
    }, _value = function(container) {
        var list = [],
            input = container.find('input[type="hidden"]'),
            name = input.attr('name');

        // go through each image in list
        container.find('div.list > media:not(.new) > img').each(function() {
            list.push($(this).attr('src'));
        });

        // change inputs
        input.remove();
        if(list.length > 0)
            for(let i = 0; i < list.length; i++)
                container.append($('<input/>', {type: 'hidden', name: name, value: list[i]}));
        else container.append($('<input/>', {type: 'hidden', name: name, value: store.empty.input}));

    // check for duplicates and return media id if found
    }, _duplicate = function(container, value) {
        var found = false;

        // go through each image in list
        container.find('div.list > media:not(.new) > img').each(function() {
            var self = $(this);
            if(self.attr('src') == value)
                found = self.parent().attr('id');
        });

        // return media id if found or false
        return found;

    // handle binds
    }, _bind = function(media) {
        var idx = media.attr('id'),
            container = media.closest('div.media-widget'),
            input = container.find('input[type="hidden"]');

        // handle each config
        store.configs[idx] = {
            ctrl: {media: media, container: container, input: input},
            chooseFiles: true,
            onInit: function(finder) {
                finder.on('files:choose', function(e) {
                    var file = e.data.files.first();
                    _add(finder.config.ctrl, file.getUrl());
                });
                finder.on('file:choose:resizedImage', function(e) {
                    _add(finder.config.ctrl, e.data.resizedUrl);
                });
            }
        };

        // on click
        media.find('img').on('click', function() {
            let name = idx + ' - Media Widget',
                path = CKFINDER_BASEPATH + 'ckfinder.html?type=Images&popup=1&configId=' + idx + '&langCode=en';

            _popup(path, name, window);
        })

        // on remove
        media.find('i').on('click', function() {
            _remove({media: media, container: container, input: input});
        });

    // update popup configs
    }, _update = function() {
        window.CKFinder = {
            _popupOptions: store.configs
        };

    // init
    }, __construct = function() {
        // go through each media
        $('div.media-widget > div.list > media').each(function() {
            _bind($(this));
        });

        // update popup configs
        _update();
    };

    // public, yay
    return {
        init: __construct
    };
}();

// init widget on ready
$(Widgets.media.init);
