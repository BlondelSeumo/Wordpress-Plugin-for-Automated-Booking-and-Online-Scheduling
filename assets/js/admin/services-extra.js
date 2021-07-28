"use strict";
var FatSbServiceExtra = {};
(function ($) {
    FatSbServiceExtra.init = function(){
        FatSbServiceExtra.loadServicesExtra();
        FatSbMain.registerEventProcess($('.content.has-button-group'));
        FatSbMain.initPopupToolTip();
    };

    FatSbServiceExtra.addServiceExtraOnClick = function(self){
        FatSbMain.showPopup('fat-sb-services-extra-template', '', [], function(){
            var callback = self.attr('data-callback');
            if(typeof callback!='undefined' && callback!=''){
                $('.fat-services-extra-form .fat-submit-modal').attr('data-callback',callback);
            }
            FatSbMain.registerEventProcess($('.fat-services-extra-form'));
        });
    };

    FatSbServiceExtra.loadServicesExtra = function(){
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services_extra'
            }),
            success: function(services){
                services = $.parseJSON(services);
                for(var $i=0 ; $i<services.length; $i++){
                    services[$i]['se_duration_label'] = FatSbMain.data.durations[services[$i].se_duration];
                }
                var template = wp.template('fat-sb-services-extra-item-template'),
                    items = $(template(services)),
                    elm_services_extra = $('.fat-sb-list-services-extra tbody');

                $('tr',elm_services_extra).remove();
                if(services.length>0){
                    elm_services_extra.append(items);
                    FatSbMain.registerEventProcess($('.fat-sb-list-services-extra'));
                }else{
                    FatSbMain.showNotFoundMessage(elm_services_extra,'<tr class="fat-tr-not-found"><td colspan="8">','</td></tr>');
                }

                FatSbMain.initCheckAll();

                $('.fat-item-bt-inline[data-title]','.fat-semantic-container').each(function(){
                    $(this).popup({
                        title : '',
                        content: $(this).attr('data-title'),
                        inline: true
                    });
                });

            },
            error: function(){}
        })
    };

    FatSbServiceExtra.processSubmitServiceExtra = function(self){
        if(FatSbMain.isFormValid){
            FatSbMain.showProcess(self);
            var form = $('.fat-services-extra-form .ui.form'),
                data = FatSbMain.getFormData(form),
                callback = typeof self.attr('data-callback')!='undefined' ? self.attr('data-callback').split('.') : '';

            if(typeof self.attr('data-id') !='undefined'){
                data['se_id'] = self.attr('data-id');
            }
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_save_service_extra',
                    data: data
                }),
                success: function(response){
                    FatSbMain.closeProcess(self);
                    self.closest('.ui.modal').modal('hide');

                    response = $.parseJSON(response);
                    if(response.result >= 0){
                        var item = $('tr[data-id="' + data.se_id +'"]');
                        if(item.length==0){
                            data.se_id = response.result;
                            var template = wp.template('fat-sb-services-extra-item-template'),
                                item = $(template([data]));
                            $('.fat-tr-not-found','.fat-sb-list-services-extra').remove();
                            $('.fat-sb-list-services-extra tbody').append(item);
                            FatSbMain.registerEventProcess($('.fat-sb-list-services-extra'));

                            $('.fat-item-bt-inline[data-title]',item).each(function(){
                                $(this).popup({
                                    title : '',
                                    content: $(this).attr('data-title'),
                                    inline: true
                                });
                            });

                        }else{
                            $('.fat-se-name',item).html(data.se_name);
                            $('.fat-se-duration',item).html(FatSbMain.data.durations[data.se_duration]);
                            $('.fat-se-price',item).html(data.se_price);
                            $('.fat-se-tax',item).html(data.se_tax);
                            $('.fat-se-max-quantity',item).html(data.se_max_quantity);
                            $('.fat-se-description',item).html(data.se_description);
                            if(data.se_multiple_book=='1'){
                                $('.fat-se-multiple-book',item).html(FatSbMain.data.yes_label);
                            }else{
                                $('.fat-se-multiple-book',item).html(FatSbMain.data.no_label);
                            }

                        }

                        if (callback != '') {
                            var obj = callback.length == 2 ? callback[0] : '',
                                func = callback.length == 2 ? callback[1] : callback[0];
                            if(obj!=''){
                                (typeof window[obj][func]!='undefined' && window[obj][func]!= null) ? window[obj][func](data) : '';
                            }else{
                                (typeof window[func]!='undefined' && window[func]!= null) ? window[func](data) : '';
                            }
                        }

                    }else{
                        if(typeof response.message!='undefined'){
                            FatSbMain.showMessage(response.message, 3);
                        }else{
                            FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        }
                    }
                },
                error: function(){
                    FatSbMain.closeProcess(self);
                    self.closest('.ui.modal').modal('hide');
                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                }
            })
        }
    };

    FatSbServiceExtra.processViewDetail = function(self){
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_service_extra_by_id',
                se_id :  self.attr('data-id')
            }),
            success: function(services){
                FatSbMain.closeProcess(self);
                services = $.parseJSON(services);
                FatSbMain.showPopup('fat-sb-services-extra-template',FatSbMain.data.edit_service_extra,services,function(){
                    FatSbMain.registerEventProcess($('.fat-services-extra-form'));
                });
            },
            error: function(){
                FatSbMain.closeProcess(self);
            }
        })
    };

    FatSbServiceExtra.btItemDeleteOnClick = function(self){
        var btDelete = self;
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title,FatSbMain.data.confirm_delete_message,function($result, popup){
            if($result==1){
                var self = $('.fat-sb-bt-confirm.yes',popup),
                    se_ids = [];
                FatSbMain.showProcess(self);
                se_ids.push(btDelete.attr('data-id'));
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_service_extra',
                        se_ids: se_ids
                    }),
                    success: function(response){
                        FatSbMain.closeProcess(self);
                        popup.modal('hide');
                        try{
                            response = $.parseJSON(response);
                            if(response.result>0){
                                $(btDelete).closest('tr').remove();
                            }else{
                                if(typeof response.message!='undefined'){
                                    FatSbMain.showMessage(response.message, 3);
                                }else{
                                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                                }
                            }
                        }catch(err){
                            FatSbMain.showMessage(FatSbMain.data.error_message,2);
                        }
                    },
                    error: function(){
                        FatSbMain.closeProcess(self);
                    }
                })
            }
        });
    };

    FatSbServiceExtra.btDeleteGroupOnClick = function(self){
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title,FatSbMain.data.confirm_delete_message,function($result, popup){
            if($result==1){

                var self = $('.fat-sb-bt-confirm.yes',popup),
                    se_ids = [];
                FatSbMain.showProcess(self);
                $('input[type="checkbox"].check-item','.fat-sb-list-services-extra tbody').each(function () {
                    if ($(this).is(':checked')) {
                        se_ids.push($(this).attr('data-id'));
                    }
                });
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_service_extra',
                        se_ids: se_ids
                    }),
                    success: function(response){
                        FatSbMain.closeProcess(self);
                        popup.modal('hide');
                        $('input.table-check-all','.fat-sb-list-services-extra').prop("checked", false);
                        $('.fat-bt-delete', '.fat-sb-services-extra-container').addClass('disabled');
                        try{
                            response = $.parseJSON(response);
                            if(response.result>0){
                                $('input[type="checkbox"].check-item:checked','.fat-sb-list-services-extra tbody').closest('tr').remove();
                            }else{
                                if(typeof response.message!='undefined'){
                                    FatSbMain.showMessage(response.message, 3);
                                }else{
                                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                                }
                            }
                        }catch(err){
                            FatSbMain.showMessage(FatSbMain.data.error_message,2);
                        }
                    },
                    error: function(){
                        FatSbMain.closeProcess(self);
                    }
                })
            }
        });
    };

    $(document).ready(function () {
        FatSbServiceExtra.init();
    });
})(jQuery);