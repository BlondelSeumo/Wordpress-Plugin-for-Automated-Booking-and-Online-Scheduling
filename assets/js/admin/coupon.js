"use strict";
var FatSbCoupon = {};
(function ($) {
    FatSbCoupon.init = function(){
        FatSbMain.initCheckAll();
        FatSbCoupon.loadCoupon(1);
        FatSbMain.registerEventProcess($('.toolbox-action-group'));
        FatSbMain.initPopupToolTip();
    };

    /*
    event handler
    */
    FatSbCoupon.btAddOnClick = function(){
        FatSbMain.showPopup('fat-sb-coupon-template','', [],function(){
            FatSbCoupon.initField();
            FatSbMain.bindServicesDic($('.fat-sb-apply-services,.fat-sb-exclude-services'));
            FatSbMain.registerEventProcess($('.fat-coupon-form'));
        });
    };

    FatSbCoupon.codeSearchKeyUp = function(self){
        var search_wrap = self.closest('.ui.input');
        if(self.val().length >=3 || self.val()==''){
            search_wrap.addClass('loading');
            FatSbCoupon.loadCoupon(1,function(){
                search_wrap.removeClass('loading');
            });
            if(self.val().length >=3){
                search_wrap.addClass('active-search');
            }
            if(self.val() == ''){
                search_wrap.removeClass('active-search');
            }
        }
    };

    FatSbCoupon.closeSearchOnClick = function(self){
        var search_wrap = self.closest('.ui.ui-search');
        $('input',search_wrap).val('');
        $('input',search_wrap).trigger('keyup');
    };

    FatSbCoupon.discountOnChange = function(value, text, $choice){
        if(value=='1'){ //percentage
            $('.fat-sb-coupon-amount i','.fat-coupon-form').removeClass('dollar sign');
            $('.fat-sb-coupon-amount i','.fat-coupon-form').addClass('percent');
        }
        if(value=='2'){ //fixed
            $('.fat-sb-coupon-amount i','.fat-coupon-form').removeClass('percent');
            $('.fat-sb-coupon-amount i','.fat-coupon-form').addClass('dollar sign');
        }
    };

    FatSbCoupon.initField  = function(){
        if($('#cp_discount_type','.fat-coupon-form').val()=='1'){ //percentage
            $('.fat-sb-coupon-amount i','.fat-coupon-form').removeClass('dollar sign');
            $('.fat-sb-coupon-amount i','.fat-coupon-form').addClass('percent');
        }
        if($('#cp_discount_type','.fat-coupon-form').val()=='2'){ //fixed
            $('.fat-sb-coupon-amount i','.fat-coupon-form').removeClass('percent');
            $('.fat-sb-coupon-amount i','.fat-coupon-form').addClass('dollar sign');
        }

    };

    FatSbCoupon.loadCoupon = function(page, callback){
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_coupons',
                cp_code: $('#cp_code','.toolbox-action-group').val(),
                page: typeof page!='undefined' && page!='' ? page: 1
            }),
            success: function(data){
                data = $.parseJSON(data);
                var total = data.total,
                    coupons = data.coupons,
                    date_format =  FatSbMain.getDateFormat();

                for(var $c_index = 0; $c_index< coupons.length;$c_index++){
                    if(typeof coupons[$c_index].cp_start_date !='undefined' && coupons[$c_index].cp_start_date!=''){
                        coupons[$c_index].cp_start_date = moment(coupons[$c_index].cp_start_date,'YYYY-MM-DD').format(date_format);

                    }
                    if(typeof coupons[$c_index].cp_expire !='undefined' && coupons[$c_index].cp_expire!=''){
                        coupons[$c_index].cp_expire = moment(coupons[$c_index].cp_expire,'YYYY-MM-DD').format(date_format);
                    }
                }
                var template = wp.template('fat-sb-coupon-item-template'),
                    items = $(template(coupons)),
                    elm_coupons = $('.fat-sb-list-coupons tbody');

                $('tr',elm_coupons).remove();
                if(coupons.length>0){
                    elm_coupons.append(items);
                    FatSbMain.registerEventProcess($('.fat-sb-list-coupons'));
                }else{
                    FatSbMain.showNotFoundMessage(elm_coupons,'<tr class="fat-tr-not-found"><td colspan="9">','</td></tr>');
                }
                FatSbMain.initCheckAll();
                FatSbMain.initPaging(total, page, $('.fat-sb-pagination'));

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

    FatSbCoupon.processSubmitCoupon = function(self){
        if(FatSbMain.isFormValid){
            FatSbMain.showProcess(self);
            var form = $('.fat-coupon-form .ui.form'),
                data = FatSbMain.getFormData(form);

            data.cp_start_date = $('#cp_start_date',form).attr('data-date');
            data.cp_expire = $('#cp_expire',form).attr('data-date');

            data.cp_start_date = typeof data.cp_start_date!='undefined' && data.cp_start_date!='' ? data.cp_start_date : FatSbMain.data.date_now;
            data.cp_expire = typeof data.cp_expire!='undefined' && data.cp_expire!='' ? data.cp_expire : FatSbMain.data.date_now;

            if(typeof self.attr('data-id') !='undefined'){
                data['cp_id'] = self.attr('data-id');
            }
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_save_coupon',
                    data: data
                }),
                success: function(response){
                    FatSbMain.closeProcess(self);
                    response = $.parseJSON(response);
                    if(response.result >= 0){
                        self.closest('.ui.modal').modal('hide');
                        FatSbMain.showMessage(self.attr('data-success-message'));
                        var item = $('tr[data-id="' + data.cp_id +'"]');
                        if(item.length==0){
                            data.cp_id = response.result;
                            data.cp_use_count = 0;
                            var template = wp.template('fat-sb-coupon-item-template'),
                                item = $(template([data]));
                            $('.fat-tr-not-found','.fat-sb-list-coupons').remove();
                            $('.fat-sb-list-coupons tbody').append(item);

                            FatSbMain.initCheckAll();
                            FatSbMain.registerEventProcess(item);

                            $('input.table-check-all','.fat-sb-list-coupons').prop("checked", false);

                            $('.fat-item-bt-inline[data-title]',item).each(function(){
                                $(this).popup({
                                    title : '',
                                    content: $(this).attr('data-title'),
                                    inline: true
                                });
                            });
                        }else{
                            data.cp_discount_type = data.cp_discount_type==1 ? FatSbMain.data.percentage_discount : FatSbMain.data.fixed_discount;
                            $('.fat-cp-code',item).html(data.cp_code);
                            $('.fat-cp-discount-type',item).html(data.cp_discount_type);
                            $('.fat-cp-amount',item).html(data.cp_amount);
                            $('.fat-cp-start-date',item).html(data.cp_start_date);
                            $('.fat-cp-expire',item).html(data.cp_expire);
                            $('.fat-cp-times-to-use',item).html(data.c_times_use);
                        }
                        if(typeof callback=='function'){
                            callback();
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

    FatSbCoupon.processViewDetail = function(self){
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_coupon_by_id',
                cp_id :  self.attr('data-id')
            }),
            success: function(coupon){

                coupon = $.parseJSON(coupon);

                var date_format =  FatSbMain.getDateFormat();

                if(typeof coupon.cp_start_date !='undefined' && coupon.cp_start_date!=null){
                    coupon.cp_start_date = moment(coupon.cp_start_date,"YYYY-MM-DD").format(date_format);
                }
                if(typeof coupon.cp_expire !='undefined' && coupon.cp_expire!=null){
                    coupon.cp_expire = moment(coupon.cp_expire,"YYYY-MM-DD").format(date_format);
                }

                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'GET',
                    data: ({
                        action: 'fat_sb_get_services'
                    }),
                    success: function (services) {
                        FatSbMain.closeProcess(self);
                        coupon.services = $.parseJSON(services);

                        FatSbMain.showPopup('fat-sb-coupon-template',FatSbMain.data.edit_coupon,coupon,function(){
                            FatSbCoupon.initField();
                            FatSbMain.registerEventProcess($('.fat-coupon-form'));
                        });

                    },
                    error: function () {
                    }
                });


            },
            error: function(){
                FatSbMain.closeProcess(self);
            }
        })
    };

    FatSbCoupon.processDelete = function(self){
        var btDelete = self;
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title,FatSbMain.data.confirm_delete_message,function($result, popup){
            if($result==1){
                var self = $('.fat-sb-bt-confirm.yes',popup),
                    cp_ids = [];
                FatSbMain.showProcess(self);
                if(btDelete.hasClass('fat-item-bt-inline')){
                    cp_ids.push(btDelete.attr('data-id'));
                }else{
                    $('input.check-item[type="checkbox"]', 'table.fat-sb-list-coupons').each(function(){
                        if($(this).is(':checked')){
                            cp_ids.push($(this).attr('data-id'));
                        }
                    });
                }
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_coupon',
                        cp_ids: cp_ids
                    }),
                    success: function(response){
                        FatSbMain.closeProcess(self);
                        popup.modal('hide');
                        try{
                            response = $.parseJSON(response);
                            if(response.result>0){
                                var cp_ids_delete = response.ids_delete;
                                for(var $i=0; $i< cp_ids_delete.length; $i++){
                                    $('tr[data-id="'+ cp_ids_delete[$i] +'"]','.fat-sb-list-coupons').remove();
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
        if($('.fat-sb-coupons-container').length > 0){
            FatSbCoupon.init();
        }
    });
})(jQuery);