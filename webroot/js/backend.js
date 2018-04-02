/**
 * Backend
 *
 * @author Flavius
 * @version 1.1
 */
var dump = function(what) { 'use strict';
    if(typeof console != 'undefined')
        console.log(what);
};

// not defined? assume it
if(typeof WEBROOT == 'undefined') WEBROOT = '/';
if(typeof DEV_ENV == 'undefined') DEV_ENV = true;

// start backend
var Backend = function() { 'use strict';
    var store = {

    // flash message autoclose and binds
    }, _flash = function() {
        $('div.flash.message').each(function() {
            var self = $(this),
                collapse = function() {
                    $(this).addClass('hidden');
                };

            // bind click
            self.on('click', collapse);

            // autoclose
            if(!self.hasClass('error'))
                window.setTimeout(function() { collapse.call(self); }, 3000);
        });

    // instant flash message
    }, flash = function(message, type) {
        // get allowed css class
        var cls = '';
        if(type !== undefined && ['error', 'success'].indexOf(type) !== -1)
            cls = type;

        // on error delete the previous errors
        if(cls == 'error') {
            let c = $('body > div.error.flash.message');
            if(c.length > 0)
                c.remove();
        }

        // append message
        $('body').append($('<div class="' + cls + ' flash message">'+ message +'</div>'));
        _flash();

    // mobile side nav expansion
    }, _sidenav = function() {
        var b = $('div.arrow-expand > i');

        // on event click
        b.on('click', function() {
            var self = $(this);
            self.closest('#actions-sidebar').toggleClass('open');
        });

    // check form errors and blink the corresponding tab
    }, _checkerrors = function() {
        // check first error
        var e = $('form div.input.error');
        if(!e.length > 0)
            return false;

        // go through each error field
        e.each(function() {
            var self = $(this);

            // got tabpanel?
            var panel = self.closest('div.tabs-panel');
            if(!panel.length > 0)
                return false;

            // error the tab for a visual notification
            var tab = $('form ul.tabs a#' + panel.attr('aria-labelledby')).parent();
            if(!tab.hasClass('error'))
                tab.addClass('error');
        });

    // init
    }, __construct = function() {
        // initiate foundation
        $(document).foundation();

        // handle things
        _flash();
        _sidenav();
        _checkerrors();
    };

    // public, yay
    return {
        init: __construct,
        flash: flash
    };
}();

// init frontend on ready
$(Backend.init);
