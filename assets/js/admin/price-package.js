"use strict";
var FatSbPricePackage = {};
(function ($) {
    FatSbPricePackage.init = function(){
        FatSbMain.initCheckAll();
        FatSbPricePackage.loadPackage(1);
        FatSbMain.registerEventProcess($('.fat-sb-price-package-container .toolbox-action-group'));
        FatSbMain.registerOnClick($('.fat-sb-price-package-container .fat-sb-order-wrap'));
        FatSbMain.initPopupToolTip();
    };

    FatSbPricePackage.btAddOnClick = function(){
        FatSbMain.showPopup('fat-sb-price-package-template','', [],function(){
            FatSbMain.registerEventProcess($('.fat-price-package-form'));
        });
    };

    FatSbPricePackage.loadPackage = function(page, callback){
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_price_package',
            }),
            success: function(data){
                data = $.parseJSON(data);
                var date_format =  FatSbMain.getDateFormat();

                for(var $index = 0; $index< data.length;$index++){
                    if(typeof data[$index].pk_create_date !='undefined' && data[$index].pk_create_date!=''){
                        data[$index].pk_create_date = moment(data[$index].pk_create_date,'YYYY-MM-DD').format(date_format);
                    }
                    data[$index].pk_price = fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + parseFloat(data[$index].pk_price).format(2, 3)) : (parseFloat(data[$index].pk_price).format(2, 3) + fat_sb_data.symbol);
                    data[$index].pk_price_for_payment = fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + parseFloat(data[$index].pk_price_for_payment).format(2, 3)) : (parseFloat(data[$index].pk_price_for_payment).format(2, 3) + fat_sb_data.symbol);
                }
                var template = wp.template('fat-sb-price-package-item-template'),
                    items = $(template(data)),
                    elm_package = $('.fat-sb-list-price-package tbody');

                $('tr',elm_package).remove();
                if(data.length>0){
                    elm_package.append(items);
                    FatSbMain.registerEventProcess($('.fat-sb-list-price-package'));
                }else{
                    FatSbMain.showNotFoundMessage(elm_package,'<tr class="fat-tr-not-found"><td colspan="7">','</td></tr>');
                }
                FatSbMain.initCheckAll();

                $('.fat-item-bt-inline[data-title]','.fat-semantic-container').each(function(){
                    $(this).popup({
                        title : '',
                        content: $(this).attr('data-title'),
                        inline: true
                    });
                });

                if(typeof callback=='function'){
                    callback();
                }

            },
            error: function(){}
        })
    };

    FatSbPricePackage.processSubmitPackage = function(self){
        if(FatSbMain.isFormValid){
            FatSbMain.showProcess(self);
            var form = $('.fat-price-package-form .ui.form'),
                callback = typeof self.attr('data-callback') != 'undefined' ? self.attr('data-callback').split('.') : '',
                data = FatSbMain.getFormData(form);

            if(typeof self.attr('data-id') !='undefined'){
                data['pk_id'] = self.attr('data-id');
            }
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_save_price_package',
                    data: data
                }),
                success: function(response){
                    FatSbMain.closeProcess(self);
                    response = $.parseJSON(response);
                    if(response.result >= 0){
                        self.closest('.ui.modal').modal('hide');
                        FatSbMain.showMessage(self.attr('data-success-message'));
                        var item = $('tr[data-id="' + data.pk_id +'"]');

                        data.pk_price = fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + parseFloat(data.pk_price).format(2, 3)) : (parseFloat(data.pk_price).format(2, 3) + fat_sb_data.symbol);
                        data.pk_price_for_payment = fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + parseFloat(data.pk_price_for_payment).format(2, 3)) : (parseFloat(data.pk_price_for_payment).format(2, 3) + fat_sb_data.symbol);

                        if(item.length==0){
                            data.pk_id = response.result;
                            data.pk_create_date = typeof response.create_date!='undefined' && response.create_date!='' ? response.create_date : '';
                            var template = wp.template('fat-sb-price-package-item-template'),
                                item = $(template([data]));
                            $('.fat-tr-not-found','.fat-sb-list-price-package').remove();
                            $('.fat-sb-list-price-package tbody').append(item);

                            FatSbMain.initCheckAll();
                            FatSbMain.registerEventProcess(item);
                            $('input.table-check-all','.fat-sb-list-price-package').prop("checked", false);

                            $('.fat-item-bt-inline[data-title]',item).each(function(){
                                $(this).popup({
                                    title : '',
                                    content: $(this).attr('data-title'),
                                    inline: true
                                });
                            });

                        }else{
                            if( $('.fat-pk-name',item).length > 0){
                                $('.fat-pk-name',item).html(data.pk_name);
                                $('.fat-pk-price',item).html(data.pk_price);
                                $('.fat-pk-price-for-payment',item).html(data.pk_price_for_payment);
                                $('.fat-pk-note',item).html(data.pk_description);
                                $('.fat-pk-date',item).html(data.pk_create_date);
                            }
                        }

                        if (callback != '') {
                            var obj = callback.length == 2 ? callback[0] : '',
                                func = callback.length == 2 ? callback[1] : callback[0];
                            if (obj != '') {
                                (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](data) : '';
                            } else {
                                (typeof window[func] != 'undefined' && window[func] != null) ? window[func](data) : '';
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

    FatSbPricePackage.processViewDetail = function(self){
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_price_package_by_id',
                pk_id :  self.attr('data-id')
            }),
            success: function(data){
                FatSbMain.closeProcess(self);
                data = $.parseJSON(data);
                FatSbMain.showPopup('fat-sb-price-package-template',FatSbMain.data.edit_price_package,data,function(){
                    FatSbMain.registerEventProcess($('.fat-price-package-form'));
                });
            },
            error: function(){
                FatSbMain.closeProcess(self);
            }
        })
    };

    FatSbPricePackage.processDelete = function(self){
        var btDelete = self;
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title,FatSbMain.data.confirm_delete_message,function($result, popup){
            if($result==1){
                var self = $('.fat-sb-bt-confirm.yes',popup),
                    pk_ids = [];
                FatSbMain.showProcess(self);
                if(btDelete.hasClass('fat-item-bt-inline')){
                    pk_ids.push(btDelete.attr('data-id'));
                }else{
                    $('input.check-item[type="checkbox"]', 'table.fat-sb-list-price-package').each(function(){
                        if($(this).is(':checked')){
                            pk_ids.push($(this).attr('data-id'));
                        }
                    });
                }
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_price_package',
                        pk_ids: pk_ids
                    }),
                    success: function(response){
                        FatSbMain.closeProcess(self);
                        popup.modal('hide');
                        try{
                            response = $.parseJSON(response);
                            if(response.result>0){
                                for(var $i=0; $i< pk_ids.length; $i++){
                                    $('tr[data-id="'+ pk_ids[$i] +'"]','.fat-sb-list-price-package').remove();
                                }
                            }
                            if(typeof response.message_success!='undefined' && response.message_success!=''){
                                FatSbMain.showMessage(response.message_success);
                                return;
                            }
                            if(typeof response.message_error!='undefined' && response.message_error!=''){
                                setTimeout(function(){
                                    FatSbMain.showMessage(response.message_error,2);
                                },300);
                                return;
                            }

                            if(typeof response.message!='undefined' && response.result <0){
                                FatSbMain.showMessage(response.message, 3);
                            }
                        }catch(err){
                            FatSbMain.showMessage(FatSbMain.data.error_message,2);
                        }
                    },
                    error: function(){
                        popup.modal('hide');
                        FatSbMain.showMessage(FatSbMain.data.error_message,2);
                        FatSbMain.closeProcess(self);
                    }
                })
            }
        });
    };

    $(document).ready(function () {
        if($('.fat-sb-price-package-container').length > 0){
            FatSbPricePackage.init();
        }
    });
})(jQuery);