/**
 * Wysiwyg widget
 *
 * @author Flavius
 * @version 1.1
 */
if(typeof Widgets === 'undefined') var Widgets = {};
Widgets.wysiwyg = function() { 'use strict';
    var store = {
        ckfinder: {
            width: 925,
            height: 540
        }

    // the config object
    }, _config = function() {
        return {
            embed_provider: '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}',
            filebrowserBrowseUrl: CKFINDER_BASEPATH + 'ckfinder.html',
            filebrowserImageBrowseUrl: CKFINDER_BASEPATH + 'ckfinder.html?type=Images',
            filebrowserUploadUrl: CKFINDER_BASEPATH + 'core/connector/php/connector.php?type=Files&command=QuickUpload',
            filebrowserImageUploadUrl: CKFINDER_BASEPATH + 'core/connector/php/connector.php?type=Images&command=QuickUpload',
            filebrowserWindowWidth: store.ckfinder.width,
            filebrowserWindowHeight: store.ckfinder.height,
            forcePasteAsPlainText: true,
            removeButtons: 'Subscript,Superscript',
            format_tags: 'p;h3',
            height: 400,
            extraPlugins: 'justify,iframe',
            toolbar: [
                ['Source'],
                ['Format'],
                ['Styles'],
                ['Bold', 'Italic', 'Underline', 'SpecialChar', 'RemoveFormat'],
                ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                ['NumberedList', 'BulletedList', 'Outdent', 'Indent'],
                ['Link', 'Unlink'],
                ['Image', 'Embed', 'Iframe', 'Table', 'HorizontalRule'],
                ['Maximize']
            ], stylesSet: [
                { name: 'Blockquote', element: 'p', attributes: {'class': 'blockquote'} },
            ]
        };

    // init
    }, __construct = function() {
        // add some custom css
        CKEDITOR.addCss('p.blockquote { display: block; margin: 1em 40px; } h3 {font-size: 150%;} ');

        // go through each wysiwyg widget
        $('div.wysiwyg-widget > textarea').each(function() {
            CKEDITOR.replace($(this).attr('id'), _config());
        });
    };

    // public, yay
    return {
        init: __construct
    };
}();

// init widget on ready
$(Widgets.wysiwyg.init);
