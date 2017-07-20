/**
 * Calendar widget
 *
 * @author Flavius
 * @version 1.0
 */
if(typeof Widgets === 'undefined') var Widgets = {};
Widgets.calendar = function() { 'use strict';
    var store = {

    // init
    }, __construct = function() {
        // go through each calendar widget
        $('div.calendar-widget').each(function() {
            let self = $(this);

            // init calendar
            self.find('input').pignoseCalendar({
                format: 'DD-MMM-YYYY',
                buttons: true,
            });
        });
    };

    // public, yay
    return {
        init: __construct
    };
}();

// init widget on ready
$(Widgets.calendar.init);
