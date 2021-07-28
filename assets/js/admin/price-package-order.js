"use strict";
var FatSbPricePackageOrder = {
};
(function ($) {
    FatSbPricePackageOrder.init = function () {
        FatSbMain.initField();
      /*  FatSbMain.initCheckAll();*/

        FatSbPricePackageOrder.loadPackageOrder(1);
        FatSbMain.registerEventProcess($('.toolbox-action-group', '.fat-sb-price-package-order-container'));
    };

    FatSbPricePackageOrder.loadPackageOrder = function (page, callback) {
        var start_date = $('#date_of_book').attr('data-start'),
            end_date = $('#date_of_book').attr('data-end'),
            email = $('#user_email').val();

        $('.fat-sb-list-package-order tbody tr').remove();
        page = typeof page != 'undefined' && page != '' ? page : 1;
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_package_order',
                start_date: start_date,
                end_date: end_date,
                user_email: email,
                page: page
            }),
            success: function (response) {
                response = $.parseJSON(response);
                var total = response.total,
                    bookings = response.orders,
                    template = wp.template('fat-sb-package-order-item-template'),
                    items = '',
                    elm_bookings = $('.fat-sb-list-package-order');

                for (var $b_index = 0; $b_index < bookings.length; $b_index++) {
                    bookings[$b_index].pk_price_for_payment = parseFloat(bookings[$b_index].pk_price_for_payment);
                    bookings[$b_index].pk_price_for_payment = bookings[$b_index].pk_price_for_payment.format(0,3,',');

                    bookings[$b_index].pk_price = parseFloat(bookings[$b_index].pk_price);
                    bookings[$b_index].pk_price = bookings[$b_index].pk_price.format(0,3,',');

                    bookings[$b_index].pk_price = fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + bookings[$b_index].pk_price) : (bookings[$b_index].pk_price + fat_sb_data.symbol);
                    bookings[$b_index].pk_price_for_payment = fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + bookings[$b_index].pk_price_for_payment) : (bookings[$b_index].pk_price_for_payment + fat_sb_data.symbol);
                    bookings[$b_index].pko_description = bookings[$b_index].pko_gateway_type=='przelewy24' ? '' : bookings[$b_index].pko_description;
                }
                items = $(template(bookings));

                $('tbody tr', elm_bookings).remove();
                if (bookings.length > 0) {
                    elm_bookings.append(items);
                    FatSbMain.registerEventProcess($('.fat-sb-list-booking'));
                    FatSbMain.initCheckAll();
                    $('.fat-item-bt-inline[data-title]', '.fat-semantic-container').each(function () {
                        $(this).popup({
                            title: '',
                            content: $(this).attr('data-title'),
                            inline: true
                        });
                    });
                } else {
                    FatSbMain.showNotFoundMessage($('tbody', elm_bookings), '<tr><td colspan="9">', '</td></tr>');
                }
                FatSbMain.initPaging(total, page, $('.fat-sb-pagination'));

                $('table.fat-sb-list-booking .ui.dropdown').dropdown();

                $('.fat-item-bt-inline[data-title]', '.fat-semantic-container').each(function () {
                    $(this).popup({
                        title: '',
                        content: $(this).attr('data-title'),
                        inline: true
                    });
                });

                FatSbMain.registerEventProcess($('.fat-sb-list-package-order'));

                if (callback) {
                    callback();
                }
            },
            error: function () {
                if (callback) {
                    callback();
                }
            }
        })
    };

    FatSbPricePackageOrder.showPopupBooking = function (elm, callback) {
        var b_id = typeof elm.attr('data-id') != 'undefined' ? elm.attr('data-id') : 0,
            popup_title = typeof b_id != 'undefined' && b_id != '' && b_id > 0 ? FatSbMain.data.modal_title.edit_booking : '',
            submit_callback = elm.attr('data-submit-callback');

        FatSbMain.showProcess(elm);

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_booking_by_id',
                b_id: b_id
            }),
            success: function (response) {
                FatSbMain.closeProcess(elm);
                response = $.parseJSON(response);

                var customers = response.customers,
                    locations = response.locations;

                FatSbPricePackageOrder.services_cat = response.services_cat;
                FatSbPricePackageOrder.services = response.services;
                FatSbPricePackageOrder.services_extra = response.services_extra;
                FatSbPricePackageOrder.employees = response.employees;
                FatSbPricePackageOrder.services_work_day = response.services_work_day;

                response.booking.symbol_prefix = fat_sb_data.symbol_position == 'before' ? fat_sb_data.symbol : '';
                response.booking.symbol_sufix = fat_sb_data.symbol_position == 'before' ? '' : fat_sb_data.symbol;

                response.booking.price_lable = FatSbPricePackageOrder.getPriceLabel(response.booking.b_customer_number, response.booking.b_price, response.booking.b_service_id);

                FatSbMain.showPopup('fat-sb-booking-template', popup_title, response, function () {
                    if (b_id > 0) {
                        FatSbPricePackageOrder.bindBookingDetail(response);
                    }

                    var date_format = FatSbPricePackageOrder.getDateFormat(),
                        elmBookingDate = $('.air-date-picker', '.fat-sb-booking-form'),
                        locale = elmBookingDate.attr('data-locale');

                    locale = locale.split('_').length > 1 ? locale.split('_')[0] : locale;
                    var option = {
                        language: locale,
                        minDate: new Date(),
                        dateFormat: date_format
                    };

                    if (typeof response.booking.b_date != 'undefined') {
                        option.minDate = new Date(response.booking.b_date);
                        option.startDate = new Date(response.booking.b_date);
                    }
                    FatSbPricePackageOrder.bod_field = elmBookingDate.datepicker(option).data('datepicker');

                    if (typeof response.booking.b_date != 'undefined') {
                        FatSbPricePackageOrder.bod_field.selectDate(new Date(response.booking.b_date));
                    }

                    FatSbMain.registerEventProcess($('.fat-sb-booking-form'));

                    if (typeof submit_callback != 'undefined' && submit_callback != '') {
                        $('.fat-sb-booking-form .fat-submit-modal').attr('data-callback', submit_callback);
                    }

                    if (typeof callback == 'function') {
                        callback();
                    }
                });
            },
            error: function () {
            }
        });
    };

    /*
    Process on change
     */

    FatSbPricePackageOrder.searchNameKeyup = function (self) {
        var search_wrap = self.closest('.ui.input');
        if (self.val().length >= 3 || self.val() == '') {
            search_wrap.addClass('loading');
            FatSbPricePackageOrder.loadPackageOrder(1, function () {
                search_wrap.removeClass('loading');
            });
            if (self.val().length >= 3) {
                search_wrap.addClass('active-search');
            }
            if (self.val() == '') {
                search_wrap.removeClass('active-search');
            }
        }
    };

    FatSbPricePackageOrder.sumoSearchOnChange = function (self) {
        var sumoContainer = self.closest('.SumoSelect'),
            prev_value = self.attr('data-prev-value'),
            value = self.val();

        value = value != null ? value : '';

        if (value != prev_value) {
            $('.ui.loader', sumoContainer).remove();
            sumoContainer.addClass('fat-loading');
            sumoContainer.append('<div class="ui active tiny inline loader"></div>');
            self.attr('data-prev-value', value);
            FatSbPricePackageOrder.loadBooking(1, function () {
                $('.ui.loader', sumoContainer).remove();
                sumoContainer.removeClass('fat-loading');
            });
        }
    };

    FatSbPricePackageOrder.closeSearchOnClick = function (self) {
        var search_wrap = self.closest('.ui.ui-search');
        $('input', search_wrap).val('');
        $('input', search_wrap).trigger('keyup');
    };

    FatSbPricePackageOrder.searchDateOnChange = function (self) {
        var date_picker = self.closest('.ui.date-input');
        $('.ui.loader', date_picker).remove();
        date_picker.addClass('fat-loading');
        date_picker.append('<div class="ui active tiny inline loader"></div>');
        FatSbPricePackageOrder.loadPackageOrder(1, function () {
            $('.ui.loader', date_picker).remove();
            date_picker.removeClass('fat-loading');
        });
    };

    FatSbPricePackageOrder.processDelete = function(self) {
        var btDelete = self;
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title, FatSbMain.data.confirm_delete_message, function ($result, popup) {
            if ($result == 1) {
                var self = $('.fat-sb-bt-confirm.yes', popup),
                    pko_id = btDelete.attr('data-id');

                FatSbMain.showProcess(self);
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_price_package_order',
                        pko_id: pko_id
                    }),
                    success: function (response) {
                        FatSbMain.closeProcess(self);
                        popup.modal('hide');
                        try {
                            response = $.parseJSON(response);
                            if (response.result > 0) {
                                $('tr[data-id="' + pko_id + '"]', '.fat-sb-list-package-order').remove();
                            }
                            if (typeof response.message_success != 'undefined' && response.message_success != '') {
                                FatSbMain.showMessage(response.message_success);
                                return;
                            }
                            if (typeof response.message_error != 'undefined' && response.message_error != '') {
                                setTimeout(function () {
                                    FatSbMain.showMessage(response.message_error, 2);
                                }, 300);
                                return;
                            }

                            if (typeof response.message != 'undefined' && response.result < 0) {
                                FatSbMain.showMessage(response.message, 3);
                            }
                        } catch (err) {
                            FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        }
                    },
                    error: function () {
                        popup.modal('hide');
                        FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        FatSbMain.closeProcess(self);
                    }
                })
            }
        })
    };

    $(document).ready(function () {
        if ($('.fat-sb-price-package-order-container').length > 0) {
            FatSbPricePackageOrder.init();
        }
    });
})(jQuery);