"use strict";
var FatSbCustomCSS = {};
(function ($) {
    FatSbCustomCSS.init = function () {
        $('.fat-sb-ace-editor').each(function(){
            if(typeof ace !='undefined'){
                var $mode = "ace/mode/" + $(this).attr('data-mode'),
                    $id = $(this).attr('id'),
                    $ace = ace.edit($id),
                    $content = $('#hidden_' + $id).html();

                $ace.getSession().setMode($mode);
                $ace.getSession().setValue($content);
                $ace.setAutoScrollEditorIntoView(true);
                $ace.getSession().on('change', function(e) {
                    var $container = $($ace.container).closest('.ace-container');
                    $('textarea', $container).html($ace.getValue());
                });
            }
        });
        FatSbMain.registerEventProcess($('.fat-sb-custom-css-container'))

    };
    FatSbCustomCSS.submitCustomCSS = function(self){
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_save_custom_css',
                data: {
                    custom_css: $('textarea[name="custom_css"]','.fat-sb-custom-css-container').html()
                }
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                self.closest('.ui.modal').modal('hide');
                response = $.parseJSON(response);
                if (response.result > 0) {
                    FatSbMain.showMessage(self.attr('data-success-message'));
                } else {
                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                }
            },
            error: function () {
                FatSbMain.closeProcess(self);
                FatSbMain.showMessage(FatSbMain.data.error_message, 2);
            }
        });
    };

    $(document).ready(function () {
        FatSbCustomCSS.init();
        FatSbMain.initPopupToolTip();
    });
})(jQuery);