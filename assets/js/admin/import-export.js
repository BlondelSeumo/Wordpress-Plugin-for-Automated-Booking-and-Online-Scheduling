"use strict";
var FatSbImportExport = {};
(function ($) {
    FatSbImportExport.init = function () {
        FatSbMain.initField();
        FatSbMain.registerEventProcess($('.fat-sb-import-export-container'));

        $('#form_import input[type="submit"]').off('click').on('click',function(event){
                var self = $(this),
                    form = self.closest('form');
                self.hide();
                $('.notice').remove();
                form.append('<div class="ui active centered inline loader"></div>');
        });
    };

    FatSbImportExport.processExport = function(self){
        var data = {
            'services': $('#services').is(':checked') ? 1 :0,
            'employees': $('#employees').is(':checked') ? 1 :0,
            'customers': $('#customers').is(':checked') ? 1 :0,
            'location': $('#location').is(':checked') ? 1 :0,
            'coupon': $('#coupon').is(':checked') ? 1 :0,
            'booking': $('#booking').is(':checked') ? 1 :0,
            'settings': $('#settings').is(':checked') ? 1 :0
        };
        if(data.services==0 && data.employees==0 && data.customers==0 && data.location==0 && data.booking==0 && data.settings==0){
            FatSbMain.showMessage(self.attr('data-invalid-message'), 2);
            return;
        }
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_export',
                data: data
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);

                response = $.parseJSON(response);
                if (response.result > 0) {
                    FatSbMain.closeProcess(self);
                    var exportFile =  new Blob([response.file], {type: "application/json"}),
                        downloadLink = document.createElement("a");

                    downloadLink.download = response.file_name;
                    downloadLink.href = window.URL.createObjectURL(exportFile);
                    downloadLink.style.display = "none";
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                } else {
                    if(typeof response.message!='undefined'){
                        FatSbMain.showMessage(response.message, 3);
                    }else{
                        FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                    }
                }
            },
            error: function () {
                FatSbMain.closeProcess(self);
                FatSbMain.showMessage(FatSbMain.data.error_message, 2);
            }
        });
    };

    FatSbImportExport.processInstallDemo = function(self){
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_install_demo',
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                response = $.parseJSON(response);
                if (response.result > 0) {
                    FatSbMain.closeProcess(self);
                    FatSbMain.showMessage(self.attr('data-success-message'));
                } else {
                    if(typeof response.message!='undefined'){
                        FatSbMain.showMessage(response.message, 3);
                    }else{
                        FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                    }
                }
            },
            error: function () {
                FatSbMain.closeProcess(self);
                FatSbMain.showMessage(FatSbMain.data.error_message, 2);
            }
        });
    };

    $(document).ready(function () {
        FatSbImportExport.init();
    });
})(jQuery);