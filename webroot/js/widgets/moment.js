/**
 * Moment widget
 *
 * @author Flavius
 * @version 1.0
 */
if(typeof Widgets === 'undefined') var Widgets = {};
Widgets.moment = function() { 'use strict';
    var store = {

    // the config object
    }, _config = function(self) {
        return {
            parentElement: self,
            dateTimeFormat: 'dd-MMM-yyyy HH:mm:ss',
            dateFormat: 'dd-MMM-yyyy',
            timeFormat: 'HH:mm:ss',
            animationDuration: 150,
            beforeShow: function(a) {
                $('.dtpicker-overlay').hide();
                $(a).parent().find('.dtpicker-overlay').css({top: "39px", left: "0"});
            },
            isPopup: window.matchMedia("(max-width: 639px)").matches || (function(){
                try{ document.createEvent("TouchEvent"); return true; }
                catch(e){ return false; }
            })(),
            formatHumanDate: function(oDate, sMode, sFormat){
                if(sMode === "date")
                    return oDate.dd + " " + oDate.month+ ", " + oDate.yyyy;
                else if(sMode === "time")
                    return oDate.HH + ":" + oDate.mm + ":" + oDate.ss;
                else if(sMode === "datetime")
                    return oDate.dd + " " + oDate.month+ ", " + oDate.yyyy + " " + oDate.HH + ":" + oDate.mm + ":" + oDate.ss;
            },
            settingValueOfElement: function(s, d, e) {
                let mode = self.find('input[type="text"]').data('field'),
                    hidden = self.find('input[type="hidden"]'),
                    date = s;

                if(mode == 'datetime') {
                    date = d.getFullYear() + '-' +
                        ('00' + (d.getMonth()+1)).slice(-2) + '-' +
                        ('00' + d.getDate()).slice(-2) + ' ' +
                        ('00' + d.getHours()).slice(-2) + ':' +
                        ('00' + d.getMinutes()).slice(-2) + ':' +
                        ('00' + d.getSeconds()).slice(-2);
                }
                if(mode == 'date') {
                    date = d.getFullYear() + '-' +
                        ('00' + (d.getMonth()+1)).slice(-2) + '-' +
                        ('00' + d.getDate()).slice(-2);
                }
                if(mode == 'time') {
                    date = ('00' + d.getHours()).slice(-2) + ':' +
                        ('00' + d.getMinutes()).slice(-2) + ':' +
                        ('00' + d.getSeconds()).slice(-2);
                }

                // update
                hidden.val(s ? date : '');
            }
        };

    // init
    }, __construct = function() {
        // go through each moment widget
        $('div.moment-widget').each(function() {
            let self = $(this);

            // remove click event on labels (it bugs out sometimes)
            self.parent('.moment').find('label').on('click', function() {
                return false;
            });

            // initiate DateTimePicker
            self.find('.moment').DateTimePicker(_config(self));
        });
    };

    // public, yay
    return {
        init: __construct
    };
}();

// init widget on ready
$(Widgets.moment.init);
