"use strict";
var FatSbBookingHistory = {};
(function ($) {

    FatSbBookingHistory.init = function(){
        FatSbMain_FE.registerOnClick($('.fat-sb-booking-history'));

        $('.fat-sb-booking-history .ui.dropdown').dropdown({
            clearable: false
        });

        $('.fat-sb-booking-history').each(function(){
            var self = $(this);
            if(self.hasClass('has-login')){
                $('.fat-sb-view-history',self).trigger('click');
            }
        });
    };

    FatSbBookingHistory.viewHistory = function(self){
        var container = $('.fat-sb-booking-history'),
            code_field = $('input', container),
            code = code_field.val(),
            error_message = code_field.attr('data-error');
        if(code=='' && !container.hasClass('has-login')){
            FatSbMain_FE.showMessage(error_message,2);
        }else{
            FatSbMain_FE.addLoading(container, self);
            FatSbBookingHistory.loadHistory(1,function(){
                FatSbMain_FE.removeLoading(container, self);
            });
        }

    };

    FatSbBookingHistory.loadHistory = function(page, callback){
        var container = $('.fat-sb-booking-history'),
            code_field = $('input', container),
            code = code_field.val(),
            b_process_status = $('#b_process_status').val();

        try {
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_get_booking_history',
                    s_field: FatSbMain_FE.data.ajax_s_field,
                    c_code: code,
                    status: b_process_status,
                    page: page
                }),
                success: function (response) {

                    response = $.parseJSON(response);

                    if(response.result> 0){
                        $('.fat-sb-booking-history table tbody').empty();

                        var total = response.total,
                            bookings = response.bookings,
                            template = wp.template('fat-sb-history-item-template'),
                            items = '';

                        for (var $b_index = 0; $b_index < bookings.length; $b_index++) {
                            bookings[$b_index].b_total_pay = fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + bookings[$b_index].b_total_pay) : (bookings[$b_index].b_total_pay + fat_sb_data.symbol);
                            if(bookings[$b_index].b_process_status == 0){
                                bookings[$b_index].b_status_display = FatSbMain_FE.data.pending_label;
                            }
                            if(bookings[$b_index].b_process_status == 1){
                                bookings[$b_index].b_status_display = FatSbMain_FE.data.approved_label;
                            }
                            if(bookings[$b_index].b_process_status == 2){
                                bookings[$b_index].b_status_display = FatSbMain_FE.data.canceled_label;
                            }
                            if(bookings[$b_index].b_process_status == 3){
                                bookings[$b_index].b_status_display = FatSbMain_FE.data.rejected_label;
                            }
                        }
                        items = $(template(bookings));

                        if (bookings.length > 0) {
                            $('.fat-sb-booking-history table tbody').append(items);
                            FatSbMain_FE.registerOnClick( $('.fat-sb-booking-history table tbody'));
                        } else {
                            FatSbMain_FE.showNotFoundMessage($('tbody'), '<tr><td colspan="9">', '</td></tr>');
                        }
                        FatSbMain_FE.initPaging(total, page, $('.fat-sb-pagination', container));
                    }else{
                        FatSbMain_FE.showMessage(response.message,2);
                    }
                    if(callback){
                        callback();
                    }
                },
                error: function (response) {
                    if(callback){
                        callback();
                    }
                }
            });
        } catch (err) {
            if(callback){
                callback();
            }
        }
    };

    FatSbBookingHistory.submitCancel = function(self){
        var container = self.closest('.fat-sb-popup-modal'),
            history_container = $('.fat-sb-booking-history'),
            id = self.attr('data-id'),
            code = $('#c_code').val(),
            error_message = $('#c_code').attr('data-error');
        if(code=='' && !container.hasClass('has-login')){
            FatSbMain_FE.showMessage(error_message,2);
        }else{
            FatSbMain_FE.addLoading(container, self);
            try {
                $.ajax({
                    url: FatSbMain_FE.data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_cancel_booking',
                        s_field: FatSbMain_FE.data.ajax_s_field,
                        c_code: code,
                        id: id
                    }),
                    success: function (response) {
                        FatSbMain_FE.removeLoading(container, self);
                        response = $.parseJSON(response);
                        if(response.result> 0){
                            FatSbMain_FE.showMessage(response.message);
                            $('tr[data-id="' +id +'"]','.fat-sb-booking-history').remove();

                            //send mail notify
                            $.ajax({
                                url: fat_sb_data.ajax_url,
                                type: 'POST',
                                data: ({
                                    action: 'fat_sb_cancel_send_mail',
                                    s_field: FatSbMain_FE.data.ajax_s_field,
                                    b_id: id,
                                })
                            });

                        }else{
                            FatSbMain_FE.showMessage(response.message,2);
                        }
                        FatSbBookingHistory.closePopupModal();
                    },
                    error: function (response) {
                        FatSbMain_FE.removeLoading(container, self);
                    }
                });
            } catch (err) {
            }
        }
    };

    FatSbBookingHistory.openPopupCancel = function(self){
        var row = self.closest('tr'),
            container = self.closest('.fat-sb-booking-history'),
            id = row.attr('data-id'),
            edit = row.attr('data-edit');

        if(edit==0){
            FatSbMain_FE.showMessage(FatSbMain_FE.data.not_edit_message,2);
        }else{
            var template = wp.template('fat-sb-popup-cancel-template');
            if(container.hasClass('has-login')){
                template = wp.template('fat-sb-popup-cancel-confirm-template');
            }
            $('body').append(template);
            $('body .fat-sb-popup-modal .fat-sb-popup-modal-content').fadeIn();
            $('a.fat-bt-submit','body .fat-sb-popup-modal .fat-sb-popup-modal-content').attr('data-id',id);
            FatSbMain_FE.registerOnClick($('body .fat-sb-popup-modal'));
        }
    };

    FatSbBookingHistory.openPopupGetCustomerCode = function(self){
        var template = wp.template('fat-sb-get-customer-code-template');
        $('body').append(template);
        $('body .fat-sb-popup-modal .fat-sb-popup-modal-content').fadeIn();
        FatSbMain_FE.registerOnClick($('body .fat-sb-popup-modal'));
    };

    FatSbBookingHistory.getCustomerCode = function(self){
        var container = self.closest('.fat-sb-popup-modal'),
            email = $('input#c_email',container).val(),
            error_message = $('input#c_email',container).attr('data-error');
        if(email==''){
            FatSbMain_FE.showMessage(error_message,2);

        }else{
            FatSbMain_FE.addLoading(container, self);
            try {
                $.ajax({
                    url: FatSbMain_FE.data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_get_customer_code',
                        s_field: FatSbMain_FE.data.ajax_s_field,
                        c_email: email
                    }),
                    success: function (response) {
                        FatSbMain_FE.removeLoading(container, self);
                        response = $.parseJSON(response);
                        if(response.result> 0){
                            FatSbMain_FE.showMessage(response.message);
                        }else{
                            FatSbMain_FE.showMessage(response.message,2);
                        }
                        FatSbBookingHistory.closePopupModal();
                    },
                    error: function (response) {
                        FatSbMain_FE.removeLoading(container, self);
                    }
                });
            } catch (err) {
            }
        }
    };

    FatSbBookingHistory.closePopupModal = function(self){
        $('body .fat-sb-popup-modal .fat-sb-popup-modal-content').fadeOut(function(){
            $('body .fat-sb-popup-modal').remove();
        });
    };

    $(document).ready(function () {
        FatSbBookingHistory.init();
    })
})(jQuery);