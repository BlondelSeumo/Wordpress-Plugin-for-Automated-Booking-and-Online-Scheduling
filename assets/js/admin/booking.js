"use strict";
var FatSbBooking = {
    services_cat: [],
    services: [],
    services_extra: [],
    e_service: {},
    employees: [],
    bod_field: null,
    order: '',
    order_by: '',
    b_id: 0,
    services_work_day: []
};
(function ($) {
    FatSbBooking.init = function () {
        FatSbMain.initField();
        FatSbMain.initCheckAll();

        FatSbBooking.loadBooking(1);
        FatSbMain.bindServicesDicHierarchy($('.fat-checkbox-dropdown-wrap.fat-sb-services-dic'));
        FatSbMain.bindCustomersDic($('.fat-checkbox-dropdown-wrap.fat-sb-customers-dic'));
        FatSbMain.bindEmployeesDic($('.fat-checkbox-dropdown-wrap.fat-sb-employees-dic'));
        FatSbMain.bindLocationDic($('.fat-sb-booking-container .fat-sb-location-dic'));

        FatSbMain.registerEventProcess($('.fat-booking-status-list', '.fat-sb-booking-container'));
        FatSbMain.registerEventProcess($('.toolbox-action-group', '.fat-sb-booking-container'));
        FatSbMain.registerOnClick($('.fat-sb-order-wrap', '.fat-sb-booking-container'));
    };

    FatSbBooking.loadBooking = function (page, callback) {
        var b_customer_name = $('#b_customer_name').val(),
            start_date = $('#date_of_book').attr('data-start'),
            start_time = $('#date_of_book').attr('data-start-time'),
            end_date = $('#date_of_book').attr('data-end'),
            end_time = $('#date_of_book').attr('data-end-time'),
            b_employee = $('#b_employee').val(),
            b_customer = $('#b_customer').val(),
            b_service = $('#b_service').val(),
            b_process_status = $('#b_process_status').val();
        $('.fat-sb-list-booking tbody tr').remove();
        page = typeof page != 'undefined' && page != '' ? page : 1;
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_booking',
                b_customer_name: b_customer_name,
                start_date: start_date,
                start_time: start_time,
                end_date: end_date,
                end_time: end_time,
                b_employee: b_employee,
                b_customer: b_customer,
                b_service: b_service,
                b_process_status: b_process_status,
                order:  FatSbBooking.order,
                order_by:  FatSbBooking.order_by,
                location: $('#location').val(),
                page: page
            }),
            success: function (response) {
                response = $.parseJSON(response);
                var total = response.total,
                    bookings = response.bookings,
                    template = wp.template('fat-sb-booking-item-template'),
                    items = '',
                    elm_bookings = $('.fat-sb-list-booking');

                if (bookings.length > 0) {
                    $('.fat-bt-export').removeClass('disabled');
                } else {
                    $('.fat-bt-export').addClass('disabled');
                }

                var hour = 0,
                    minute = 0;
                for (var $b_index = 0; $b_index < bookings.length; $b_index++) {
                    bookings[$b_index].b_total_pay = fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + bookings[$b_index].b_total_pay) : (bookings[$b_index].b_total_pay + fat_sb_data.symbol);
                    hour = Math.floor(bookings[$b_index].b_time/60);
                    hour = hour < 10 ? ('0' + hour) : hour;
                    minute = bookings[$b_index].b_time%60;
                    minute = minute < 10 ? ('0' + minute) : minute;
                    bookings[$b_index].b_date_display = bookings[$b_index].b_date + ' ' + (hour + ':' + minute);
                }
                items = $(template(bookings));

                $('#total_canceled').html(response.total_cancel);
                $('#total_pending').html(response.total_pending);
                $('#total_rejected').html(response.total_reject);
                $('#total_approved').html(response.total_approved);

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
                    FatSbMain.showNotFoundMessage($('tbody', elm_bookings), '<tr><td colspan="8">', '</td></tr>');
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

    FatSbBooking.showPopupBooking = function (elm, callback) {
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

                FatSbBooking.services_cat = response.services_cat;
                FatSbBooking.services = response.services;
                FatSbBooking.services_extra = response.services_extra;
                FatSbBooking.employees = response.employees;
                FatSbBooking.services_work_day = response.services_work_day;

                response.booking.symbol_prefix = fat_sb_data.symbol_position == 'before' ? fat_sb_data.symbol : '';
                response.booking.symbol_sufix = fat_sb_data.symbol_position == 'before' ? '' : fat_sb_data.symbol;

                if(response.booking.b_form_builder !='undefined' && response.booking.b_form_builder!=''){
                    response.booking.b_form_builder = JSON.stringify(response.booking.b_form_builder);
                }
                response.booking.price_lable = FatSbBooking.getPriceLabel(response.booking.b_customer_number, response.booking.b_price, response.booking.b_service_id);

                FatSbMain.showPopup('fat-sb-booking-template', popup_title, response, function () {
                    var date_format = FatSbBooking.getDateFormat(),
                        elmBookingDate = $('.air-date-picker', '.fat-sb-booking-form'),
                        locale = elmBookingDate.attr('data-locale');

                    locale = locale.split('_').length > 1 ? locale.split('_')[0] : locale;
                    var option = {
                        language: locale,
                        minDate: new Date(),
                        dateFormat: date_format
                    };

                    FatSbBooking.bod_field = elmBookingDate.datepicker(option).data('datepicker');

                    if (b_id > 0) {
                        FatSbBooking.b_id = b_id;
                        FatSbBooking.bindBookingDetail(response);
                    }

                    if (typeof response.booking.b_date != 'undefined') {
                        setTimeout(function(){
                            FatSbBooking.bod_field.selectDate(new Date(response.booking.b_date));
                            $('.fat-sb-booking-time-wrap', '.fat-sb-booking-form').dropdown('refresh').dropdown('set selected', response.booking.b_time);
                            $('.fat-customer-number-dic', '.fat-sb-booking-form').dropdown('refresh').dropdown('set selected', response.booking.b_customer_number);
                        },1000);
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

    FatSbBooking.dropdownClick = function (self) {
        var value = '',
            message = '';
        if (self.hasClass('fat-sb-services-dic')) {
            value = $('#b_service_cat_id').val();
            message = self.attr('data-warning-message');
        }

        if (self.hasClass('fat-sb-services-extra-dic')) {
            value = $('#b_service_id').val();
            message = self.attr('data-warning-message');
        }

        if (self.hasClass('fat-sb-employees-dic')) {
            value = $('#b_service_id').val();
            value = $('#b_loc_id').val() == '' ? '' : value;
            message = self.attr('data-warning-message');
        }
        if (self.hasClass('fat-customer-number-dic')) {
            value = $('#b_service_id').val();
            value = $('#b_employee_id').val() == '' ? '' : value;
            message = self.attr('data-warning-message');
        }

        if (value == '') {
            self.popup({
                title: '',
                on: 'click',
                hoverable: true,
                position: 'bottom left',
                content: message,
                inline: true
            }).popup('toggle');
        } else {
            self.popup('destroy');
        }
    };

    FatSbBooking.searchNameKeyup = function (self) {
        var search_wrap = self.closest('.ui.input');
        if (self.val().length >= 3 || self.val() == '') {
            search_wrap.addClass('loading');
            FatSbBooking.loadBooking(1, function () {
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

    FatSbBooking.sumoSearchOnChange = function (self) {
        var sumoContainer = self.closest('.SumoSelect'),
            prev_value = self.attr('data-prev-value'),
            value = self.val();

        value = value != null ? value : '';

        if (value != prev_value) {
            $('.ui.loader', sumoContainer).remove();
            sumoContainer.addClass('fat-loading');
            sumoContainer.append('<div class="ui active tiny inline loader"></div>');
            self.attr('data-prev-value', value);
            FatSbBooking.loadBooking(1, function () {
                $('.ui.loader', sumoContainer).remove();
                sumoContainer.removeClass('fat-loading');
            });
        }
    };

    FatSbBooking.closeSearchOnClick = function (self) {
        var search_wrap = self.closest('.ui.ui-search');
        $('input', search_wrap).val('');
        $('input', search_wrap).trigger('keyup');
    };

    FatSbBooking.searchDateOnChange = function (self) {
        var date_picker = self.closest('.ui.date-input');
        $('.ui.loader', date_picker).remove();
        date_picker.addClass('fat-loading');
        date_picker.append('<div class="ui active tiny inline loader"></div>');
        FatSbBooking.loadBooking(1, function () {
            $('.ui.loader', date_picker).remove();
            date_picker.removeClass('fat-loading');
        });
    };

    FatSbBooking.searchStatusChange = function (self) {
        var dropdown = self.closest('.ui.dropdown');
        dropdown.addClass('loading');
        setTimeout(function () {
            FatSbBooking.loadBooking(1, function () {
                dropdown.removeClass('loading');
            });
        }, 300);
    };

    FatSbBooking.serviceCatOnChange = function (value, text, $choice) {
        var elm_services = $('.fat-sb-services-dic', '.fat-sb-booking-form'),
            elm_services_menu = $(' > .menu > .scrolling.menu', elm_services);
        elm_services.addClass('fat-loading');
        elm_services.append('<div class="ui button loading"></div>');
        $(elm_services_menu).val('');
        $('.item', elm_services_menu).remove();
        elm_services.dropdown('clear');

        for (var $s_index = 0; $s_index < FatSbBooking.services.length; $s_index++) {
            if (FatSbBooking.services[$s_index].s_category_id == value) {
                elm_services_menu.append('<div class="item" data-value="' + FatSbBooking.services[$s_index].s_id + '">' + FatSbBooking.services[$s_index].s_name + '</div>');
            }
        }

        FatSbBooking.resetPaymentInfo();

        setTimeout(function () {
            elm_services.removeClass('fat-loading');
            $('.ui.button.loading', elm_services).remove();
        }, 500)
    };

    FatSbBooking.serviceOnChange = function (value, text, $choice) {
        FatSbBooking.initEmployees();
        //init service extra
        var elm_service_extra = $('.fat-sb-services-extra-dic', '.fat-sb-booking-form'),
            dropdown_se_menu = $('.fat-sb-services-extra-dic .scrolling.menu', '.fat-sb-booking-form'),
            current_service_id = $('#b_service_id').val(),
            services = _.findWhere(FatSbBooking.services, {s_id: current_service_id}),
            s_extra_ids = typeof services != 'undefined' ? services.s_extra_ids : '';

        elm_service_extra.dropdown('clear');
        elm_service_extra.addClass('fat-loading');
        elm_service_extra.append('<div class="ui button loading"></div>');

        $('.item', dropdown_se_menu).remove();
        if (s_extra_ids != '') {
            s_extra_ids = s_extra_ids.split(',');
            for (var $s_extra_index = 0; $s_extra_index < FatSbBooking.services_extra.length; $s_extra_index++) {
                if (_.contains(s_extra_ids, FatSbBooking.services_extra[$s_extra_index].se_id)) {
                    dropdown_se_menu.append('<div class="item" data-value="' + FatSbBooking.services_extra[$s_extra_index].se_id + '">' +
                        FatSbBooking.services_extra[$s_extra_index].se_name + '</div>');
                }
            }
        }

        FatSbBooking.resetPaymentInfo();

        setTimeout(function () {
            elm_service_extra.removeClass('fat-loading');
            $('.ui.button.loading', elm_service_extra).remove();
        }, 500)
    };

    FatSbBooking.serviceExtraOnChange = function (value, text, $choice) {
        FatSbBooking.initPaymentInfo();
        FatSbBooking.initSlot();
    };

    FatSbBooking.payNowOnChange = function (self) {
        if (self.is(':checked')) {
            $('div[data-depend="pay_now"]', '.fat-sb-booking-form').closest('.fields').removeClass('fat-hidden');
            $('div[data-depend="pay_now"]', '.fat-sb-booking-form').slideDown();
        } else {
            $('div[data-depend="pay_now"]', '.fat-sb-booking-form').slideUp();
        }
    };

    FatSbBooking.addCustomerOnClick = function (self) {
        FatSbMain.showPopup('fat-sb-customers-template', '', [], function () {
            var callback = self.attr('data-callback');
            if (typeof callback != 'undefined' && callback != '') {
                $('.fat-customer-form .fat-submit-modal').attr('data-callback', callback);
            }
            FatSbMain.registerEventProcess($('.fat-customer-form'));
        });
    };

    FatSbBooking.addCustomerToDropdown = function (data) {
        $('.fat-sb-customer-dic .menu .scrolling.menu', '.fat-sb-booking-form').append('<div class="item" data-value="' + data.c_id + '">' + data.c_first_name + ' ' + data.c_last_name + '</div>');
        $('.fat-sb-customer-dic', '.fat-sb-booking-form').dropdown('refresh').dropdown('set selected', data.c_id);
        //FatSbMain.registerEventProcess($('.fat-sb-booking-form'));
        FatSbMain.registerOnChange($('.fat-sb-booking-form'));
    };

    FatSbBooking.timeOnChange = function(self){
        // init customer number dropdown
        if(!$('.fat-customer-number-dic', '.fat-sb-booking-form').hasClass('disabled')){
            var b_time = $('#b_time','.fat-sb-booking-form').val(),
                min_cap = parseInt(FatSbBooking.e_service.s_min_cap),
                max_cap = parseInt( $('.fat-sb-booking-time-wrap .menu .item[data-value="' + b_time +'"]', '.fat-sb-booking-form').attr('data-max-cap-available') ),
                elm_customer_number = $('.fat-customer-number-dic .menu', '.fat-sb-booking-form');
            $('.fat-customer-number-dic .text', '.fat-sb-booking-form').html(min_cap);
            $('#number_of_person', '.fat-sb-booking-form').val(min_cap);
            $('.item', elm_customer_number).remove();
            for (var $n_index = min_cap; $n_index <= max_cap; $n_index++) {
                elm_customer_number.append('<div class="item" data-value="' + $n_index + '">' + $n_index + '</div>');
            }

            $('.fat-customer-number-dic', '.fat-sb-booking-form').dropdown('refresh').dropdown('set selected', min_cap);
        }
    };

    /*
    Init value for depend field
     */
    FatSbBooking.initEmployees = function () {
        var elm_employee = $('.fat-sb-employees-dic', '.fat-sb-booking-form'),
            elm_employee_menu = $(' > .menu > .scrolling.menu', elm_employee),
            loc_id = $('#b_loc_id').val(),
            s_id = $('#b_service_id').val(),
            loc_ids = '';

        elm_employee.dropdown('clear');
        elm_employee.addClass('fat-loading');
        elm_employee.append('<div class="ui button loading"></div>');

        $('.item', elm_employee_menu).remove();
        var $employees = [],
            $e_services = [];
        for (var $e_index = 0; $e_index < FatSbBooking.employees.length; $e_index++) {
            if (typeof FatSbBooking.employees[$e_index].e_services != 'undefined' && FatSbBooking.employees[$e_index].e_services != null) {
                $e_services = FatSbBooking.employees[$e_index].e_services;
                for (var $e_service_index = 0; $e_service_index < $e_services.length; $e_service_index++) {
                    if ($e_services[$e_service_index].s_id == s_id) {
                        $employees.push(FatSbBooking.employees[$e_index]);
                        break;
                    }
                }
            }
        }

        if (loc_id != '') {
            for (var $e_index = 0; $e_index < $employees.length; $e_index++) {
                loc_ids = $employees[$e_index].e_location_ids.split(',');
                if (loc_ids.indexOf(loc_id) >= 0) {
                    elm_employee_menu.append('<div class="item" data-value="' + $employees[$e_index].e_id + '">' + $employees[$e_index].e_first_name + ' ' + $employees[$e_index].e_last_name + '</div>');
                }
            }
        }


        setTimeout(function () {
            elm_employee.removeClass('fat-loading');
            $('.ui.button.loading', elm_employee).remove();
        }, 500)

    };

    FatSbBooking.initSlot = function () {
        var loc_id = $('#b_loc_id', '.fat-sb-booking-form').val(),
            s_id = $('#b_service_id', '.fat-sb-booking-form').val(),
            e_id = $('#b_employee_id', '.fat-sb-booking-form').val(),
            b_id = $('.fat-submit-modal', '.fat-sb-booking-form').attr('data-id'),
            date_wrap = $('.fat-sb-booking-date-wrap', '.fat-sb-booking-form'),
            time_wrap = $('.fat-sb-booking-time-wrap', '.fat-sb-booking-form');

        b_id = typeof b_id != 'undefined' ? b_id : 0;

        date_wrap.addClass('fat-loading');
        time_wrap.addClass('fat-loading');
        date_wrap.append('<div class="ui button loading"></div>');
        time_wrap.append('<div class="ui button loading"></div>');
        time_wrap.dropdown('clear');

        if (FatSbBooking.bod_field != null) {
            try{
                FatSbBooking.bod_field.clear();
            }catch(err){}
        }

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_booking_slot',
                s_id: s_id,
                e_id: e_id,
                b_id: b_id,
                loc_id: loc_id
            }),
            success: function (response) {
                response = $.parseJSON(response);

                $('.fat-sb-booking-time-wrap .item:not(.disabled)', '.fat-sb-booking-form').addClass('disabled');

                if (response.result > 0 && typeof response.employee != 'undefined' && response.employee != null) {
                    var bookings = typeof response.bookings != 'undefined' && response.bookings != 'null' ? response.bookings : [],
                        e_day_off = typeof response.employee.e_day_off != 'undefined' && response.employee.e_day_off != 'null' ? response.employee.e_day_off : [],
                        e_break_times = typeof response.employee.e_break_times != 'undefined' && response.employee.e_break_times != 'null' ? response.employee.e_break_times : [],
                        e_schedules = typeof response.employee.e_schedules != 'undefined' && response.employee.e_schedules != 'null' ? response.employee.e_schedules : [],
                        e_services = typeof response.employee.e_services != 'undefined' && response.employee.e_services != 'null' ? response.employee.e_services : {},
                        current_service_id = $('#b_service_id').val(),

                        dof_start = '',
                        dof_end = '';

                    FatSbBooking.e_service = _.findWhere(e_services, {s_id: current_service_id});

                    // init customer number dropdown
                    if (!$('.fat-customer-number-dic', '.fat-sb-booking-form').hasClass('disabled') && typeof FatSbBooking.e_service != 'undefined' && FatSbBooking.e_service != null
                        && !isNaN(FatSbBooking.e_service.s_min_cap) && !isNaN(FatSbBooking.e_service.s_max_cap)) {
                        var min_cap = parseInt(FatSbBooking.e_service.s_min_cap),
                            max_cap = parseInt(FatSbBooking.e_service.s_max_cap),
                            elm_customer_number = $('.fat-sb-customer-number .menu', '.fat-sb-booking-form');
                        $('.fat-sb-customer-number .text', '.fat-sb-booking-form').html(min_cap);
                        $('#b_customer_number', '.fat-sb-booking-form').val(min_cap);
                        $('.item', elm_customer_number).remove();
                        for (var $n_index = min_cap; $n_index <= max_cap; $n_index++) {
                            elm_customer_number.append('<div class="item" data-value="' + $n_index + '">' + $n_index + '</div>');
                        }
                    }

                    FatSbBooking.initPaymentInfo();

                    $('.air-date-picker').datepicker({
                        onRenderCell: function (date, cellType) {
                            if (cellType == 'day') {
                                var $es_day = FatSbBooking.getESDay(date);
                                for (var $dof_index = 0; $dof_index < e_day_off.length; $dof_index++) {
                                    if (e_day_off[$dof_index].dof_start != '' && e_day_off[$dof_index].dof_end != '') {
                                        dof_start = new Date(e_day_off[$dof_index].dof_start);
                                        dof_end = new Date(e_day_off[$dof_index].dof_end);

                                        dof_start = new Date(dof_start.getFullYear(), dof_start.getMonth(), dof_start.getDate(), 0, 0, 0);
                                        dof_end = new Date(dof_end.getFullYear(), dof_end.getMonth(), dof_end.getDate(), 0, 0, 0);

                                        if (date >= dof_start && date <= dof_end) {
                                            return {
                                                classes: 'fat-slot-not-free',
                                                disabled: true
                                            }
                                        }
                                    }

                                }

                                for (var $es_index = 0; $es_index < e_schedules.length; $es_index++) {
                                    if ($es_day == e_schedules[$es_index].es_day) {
                                        if (e_schedules[$es_index].es_enable == "1") {
                                            return {
                                                classes: 'fat-slot-free',
                                                disabled: false
                                            }
                                        } else {
                                            return {
                                                classes: 'fat-slot-not-free',
                                                disabled: true
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        onSelect: function (formattedDate, date, inst) {
                            if (typeof date == 'undefined' || date == '') {
                                return;
                            }
                            var month = date.getMonth() + 1,
                                day = date.getDate(),
                                selected_date_value = '',
                                now = new Date(FatSbMain.data.now),
                                now_minute = now.getHours()*60 + now.getMinutes();

                            month = parseInt(month);
                            day = parseInt(day);
                            month = month < 10 ? ('0' + month) : month;
                            day = day < 10 ? ('0' + day) : day;

                            selected_date_value = date.getFullYear() + '-' + month + '-' + day;
                            $('#b_date', '.fat-sb-booking-form').attr('data-date', selected_date_value);

                            $('.fat-sb-booking-time-wrap .item:not(.disabled)', '.fat-sb-booking-form').addClass('disabled');
                            if (typeof date == 'undefined' || date == '' || $('#b_employee_id').val() == '') {
                                return;
                            }

                            //check service working day
                            var $service_work_day = _.where(FatSbBooking.services_work_day,{s_id: s_id});
                            if($service_work_day.length > 0){
                                var from_date = '',
                                    to_date = '',
                                    result = 'no_slot';
                                for(var $swd_index = 0; $swd_index < $service_work_day.length; $swd_index ++){
                                    from_date = moment($service_work_day[$swd_index].from_date);
                                    to_date =  moment($service_work_day[$swd_index].to_date);

                                    from_date = new Date(from_date.year(), from_date.month(), from_date.date(), 0, 0, 0);
                                    to_date = new Date(to_date.year(), to_date.month(), to_date.date(), 23, 59, 59);

                                    if (date >= from_date && date <= to_date) {
                                        result = 'has_slot';

                                    }
                                }
                                if(result=='no_slot'){
                                    $('.fat-sb-booking-time-wrap .text', '.fat-sb-booking-form').text($('.fat-sb-booking-time-wrap .text', '.fat-sb-booking-form').attr('data-no-time-slot'));
                                    return;
                                }
                            }

                            var $es_day = FatSbBooking.getESDay(date),
                                time = 0,
                                self = '',
                                work_hours = [],
                                current_service_id = $('#b_service_id').val(),
                                service = _.findWhere(FatSbBooking.services, {s_id: current_service_id}),
                                duration = !isNaN(service.s_duration) ? parseInt(service.s_duration) : 0,
                                s_break_time = !isNaN(service.s_break_time) ? parseInt(service.s_break_time) : 0,
                                current_loc_id = $('#b_loc_id').val(),
                                extra_ids = $('.fat-sb-services-extra-dic').dropdown('get value'),
                                break_times = _.where(e_break_times, {es_day: String($es_day)});

                            if (extra_ids != '') {
                                extra_ids = extra_ids.split(',');
                                var extra_info = '';
                                for (var $ex_index = 0; $ex_index < extra_ids.length; $ex_index++) {
                                    extra_info = _.findWhere(FatSbBooking.services_extra, {se_id: extra_ids[$ex_index]});
                                    if (typeof extra_info != 'undefined' && typeof extra_info.se_duration != 'undefined') {
                                        duration += parseInt(extra_info.se_duration);
                                    }
                                }
                            }

                            if (current_service_id != '') {
                                //check work hour
                                $('.fat-sb-booking-time-wrap .item', '.fat-sb-booking-form').each(function () {
                                    self = $(this),
                                    time = self.attr('data-value');
                                    time = parseInt(time);

                                    for (var $es_index = 0; $es_index < e_schedules.length; $es_index++) {
                                        if (e_schedules[$es_index].es_day == $es_day) {
                                            work_hours = e_schedules[$es_index].work_hours;
                                            if(typeof work_hours!='undefined'){
                                                for (var $wk_index = 0; $wk_index < work_hours.length; $wk_index++) {
                                                    if ((work_hours[$wk_index].s_id.indexOf(current_service_id) >= 0 || work_hours[$wk_index].s_id=='0') &&
                                                        parseInt(work_hours[$wk_index].es_work_hour_start) <= time && (time + duration) <= parseInt(work_hours[$wk_index].es_work_hour_end)) {
                                                        self.removeClass('disabled');
                                                    }
                                                }
                                            }
                                            if (typeof break_times != 'undefined') {
                                                for (var $b_index = 0; $b_index < break_times.length; $b_index++) {
                                                    if (time >= parseInt(break_times[$b_index].es_break_time_start) && time < parseInt(break_times[$b_index].es_break_time_end)) {
                                                        self.addClass('disabled');
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    //disable time slot that has passed in the current day
                                    if(FatSbMain.equalDay(now, date) && time < now_minute){
                                        self.addClass('disabled');
                                    }

                                    //default max cap
                                    self.attr('data-max-cap-available', FatSbBooking.e_service.s_max_cap);
                                });

                                //check base on booking
                                var booking_in_day = _.where(bookings, {b_date: selected_date_value}),
                                    booking_service_in_day = _.where(bookings, {b_date: selected_date_value, b_service_id: current_service_id.toString(), b_loc_id: current_loc_id.toString()});


                                if (typeof booking_in_day != 'undefined') {
                                    var b_time = 0,
                                        b_end_time = 0,
                                        b_service_id = 0,
                                        b_loc_id = 0,
                                        time = 0,
                                        end_time = 0,
                                        self,
                                        min_cap =  parseInt(FatSbBooking.e_service.s_min_cap),
                                        max_cap =  parseInt(FatSbBooking.e_service.s_max_cap),
                                        total_customer = 0,
                                        b_customer_number = 0;

                                    // check for booking this service
                                    $('.fat-sb-booking-time-wrap .item:not(.disabled)', '.fat-sb-booking-form').each(function () {
                                        self = $(this);
                                        time = parseInt(self.attr('data-value'));
                                        total_customer = 0;
                                        for (var $bs_index = 0; $bs_index < booking_service_in_day.length; $bs_index++) {
                                            b_time = parseInt(booking_service_in_day[$bs_index].b_time);
                                            b_end_time = b_time + parseInt(booking_service_in_day[$bs_index].b_service_duration) +  parseInt(booking_service_in_day[$bs_index].b_service_break_time);
                                            b_customer_number = parseInt(booking_service_in_day[$bs_index].b_customer_number);
                                            end_time = time + duration + s_break_time;
                                            if( b_time <= time && end_time <= b_end_time){
                                                total_customer += b_customer_number;
                                            }
                                        }
                                        if(total_customer >= max_cap || min_cap > (max_cap - total_customer)){
                                            self.addClass('disabled');
                                        }else{
                                            self.attr('data-max-cap-available', (max_cap - total_customer));
                                        }
                                    });

                                    var $is_conflict = true;
                                    $('.fat-sb-booking-time-wrap .item:not(.disabled)', '.fat-sb-booking-form').each(function () {
                                        self = $(this);
                                        time = parseInt(self.attr('data-value'));
                                        end_time = time + duration + s_break_time;

                                        /** check duplicate time with another service */
                                        for (var $bs_index = 0; $bs_index < booking_in_day.length; $bs_index++) {
                                            b_time = parseInt(booking_in_day[$bs_index].b_time);
                                            b_end_time = b_time + parseInt(booking_in_day[$bs_index].b_service_duration) +  parseInt(booking_in_day[$bs_index].b_service_break_time);
                                            b_service_id = parseInt(booking_in_day[$bs_index].b_service_id);
                                            b_loc_id = parseInt(booking_in_day[$bs_index].b_loc_id);

                                            if(b_time == time && end_time == b_end_time && b_service_id == current_service_id && (b_loc_id == current_loc_id || b_loc_id==0)){
                                                $is_conflict = false;
                                            }else{
                                                $is_conflict = !(end_time <= b_time || time >= b_end_time);
                                                if($is_conflict){
                                                    console.log('conflict');
                                                }
                                            }
                                            if($is_conflict){
                                                self.addClass('disabled');
                                            }
                                        }
                                    });
                                }

                                if ($('.fat-sb-booking-time-wrap .item:not(.disabled)', '.fat-sb-booking-form').length == 0) {
                                    $('.fat-sb-booking-time-wrap i.clock.icon', '.fat-sb-booking-form').hide();
                                    $('.fat-sb-booking-time-wrap .text', '.fat-sb-booking-form').text($('.fat-sb-booking-time-wrap .text', '.fat-sb-booking-form').attr('data-no-time-slot'));
                                } else {
                                    $('.fat-sb-booking-time-wrap i.clock.icon', '.fat-sb-booking-form').show();
                                    $('.fat-sb-booking-time-wrap .text', '.fat-sb-booking-form').text($('.fat-sb-booking-time-wrap .text', '.fat-sb-booking-form').attr('data-text'));
                                }
                            }

                            inst.hide();
                        }
                    });
                }
                date_wrap.removeClass('fat-loading');
                time_wrap.removeClass('fat-loading');
                $('.ui.button.loading', date_wrap).remove();
                $('.ui.button.loading', time_wrap).remove();
            },
            error: function () {
                date_wrap.removeClass('fat-loading');
                time_wrap.removeClass('fat-loading');
                $('.ui.button.loading', date_wrap).remove();
                $('.ui.button.loading', time_wrap).remove();
            }
        });
    };

    FatSbBooking.initPaymentInfo = function () {
        var quantity = $('#b_customer_number', '.fat-sb-booking-form').val(),
            price = 0,
            tax = 0,
            tax_amount = 0,
            price_extra = 0,
            tax_extra = 0,
            discount = 0,
            subtotal = 0,
            total = 0,
            elm_price = $('.price', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_price_extra = $('.price-extra', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_quantity = $('.quantity', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_sub_total = $('.sub-total', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_tax = $('.tax', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_extra_tax = $('.extra-tax', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_discount = $('.discount', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_total = $('.total', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            service_id = $('#b_service_id','.fat-sb-booking-form').val();

        quantity = !isNaN(quantity) && quantity != '' ? parseInt(quantity) : 0;

        if (typeof FatSbBooking.e_service != 'undefined' && FatSbBooking.e_service != null && typeof FatSbBooking.e_service.s_price != 'undefined') {
            price = !isNaN(FatSbBooking.e_service.s_price) && FatSbBooking.e_service.s_price != '' ? parseFloat(FatSbBooking.e_service.s_price) : 0;
            tax = !isNaN(FatSbBooking.e_service.s_tax) && FatSbBooking.e_service.s_tax != '' ? parseInt(FatSbBooking.e_service.s_tax) : 0;
            subtotal = FatSbBooking.calculateSubtotal(quantity, parseFloat(price), service_id);// quantity * parseFloat(price);
            tax_amount = (subtotal * tax) / 100;

        } else {
            price = elm_price.attr('data-value');
            price = price != '' && !isNaN(price) ? parseFloat(price) : 0;
            subtotal = FatSbBooking.calculateSubtotal(quantity, parseFloat(price), service_id);
            tax_amount = elm_tax.attr('data-value');
            tax_amount = tax_amount != '' && !isNaN(tax_amount) ? parseFloat(tax_amount) : 0;
        }

        total = subtotal + tax_amount - discount;


        var service_extra_ids = $('#b_services_extra').val();
        if (service_extra_ids != '' && typeof FatSbBooking.services_extra != 'undefined' && FatSbBooking.services_extra != null && FatSbBooking.services_extra.length > 0) {
            service_extra_ids = service_extra_ids.split(',');
            for (var $s_extra_index = 0; $s_extra_index < FatSbBooking.services_extra.length; $s_extra_index++) {
                if (_.contains(service_extra_ids, FatSbBooking.services_extra[$s_extra_index].se_id)) {
                    price_extra += !isNaN(FatSbBooking.services_extra[$s_extra_index].se_price) ? (quantity * parseFloat(FatSbBooking.services_extra[$s_extra_index].se_price)) : 0;
                    tax_extra += (price_extra * FatSbBooking.services_extra[$s_extra_index].se_tax) / 100;
                }
            }
            subtotal +=  price_extra;
            total = subtotal + tax_amount + tax_extra - discount;
        }

        var price_label = FatSbBooking.getPriceLabel(quantity, price, service_id);
        price_label = parseFloat(price_label);
        elm_price.text(fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + price_label.format(2, 3)) : (price_label.format(2, 3) + fat_sb_data.symbol));
        elm_price.attr('data-value', price_label);

        elm_price_extra.text(fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + price_extra.format(2, 3)) : (price_extra.format(2, 3) + fat_sb_data.symbol));
        elm_price_extra.attr('data-value', price_extra);

        elm_quantity.text(quantity);
        elm_quantity.attr('data-value', quantity);

        elm_sub_total.text(fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + subtotal.format(2, 3)) : (subtotal.format(2, 3) + fat_sb_data.symbol));
        elm_sub_total.attr('data-value', subtotal);

        elm_total.text(fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + total.format(2, 3)) : (total.format(2, 3) + fat_sb_data.symbol));
        elm_total.attr('data-value', total);

        elm_tax.text(fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + tax_amount.format(2, 3)) : (tax_amount.format(2, 3) + fat_sb_data.symbol));
        elm_tax.attr('data-value', tax_amount);

        elm_discount.text(fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + discount.format(2, 3)) : (discount.format(2, 3) + fat_sb_data.symbol));
        elm_discount.attr('data-value', discount);

        elm_extra_tax.text(fat_sb_data.symbol_position == 'before' ? (fat_sb_data.symbol + tax_extra.format(2, 3)) : (tax_extra.format(2, 3) + fat_sb_data.symbol));
        elm_extra_tax.attr('data-value', tax_extra);

    };

    FatSbBooking.resetPaymentInfo = function () {
        var elm_price = $('.price', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_price_extra = $('.price-extra', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_quantity = $('.quantity', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_sub_total = $('.sub-total', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_tax = $('.tax', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_extra_tax = $('.extra-tax', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_discount = $('.discount', '.fat-sb-booking-form .fat-sb-payment-booking-info'),
            elm_total = $('.total', '.fat-sb-booking-form .fat-sb-payment-booking-info');

        elm_price.text(0);
        elm_price.attr('data-value', 0);

        elm_price_extra.text(0);
        elm_price_extra.attr('data-value', 0);

        elm_sub_total.text(0);
        elm_sub_total.attr('data-value', 0);

        elm_total.text(0);
        elm_total.attr('data-value', 0);

        elm_tax.text(0);
        elm_tax.attr('data-value', 0);

        elm_discount.text(0);
        elm_discount.attr('data-value', 0);

        elm_extra_tax.text(0);
        elm_extra_tax.attr('data-value', 0);
    };

    FatSbBooking.initCoupon = function (self) {
        var coupon = $('#b_coupon_code').val(),
            s_id = $('#b_service_id').val();
        if (s_id == '' || coupon == '') {
            FatSbMain.showMessage(FatSbMain.data.coupon_validate, 2);
            return;
        }
        if (self.hasClass('loading')) {
            return;
        }

        self.addClass('loading');
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_coupon_discount',
                coupon: coupon,
                s_id: s_id
            }),
            success: function (response) {
                response = $.parseJSON(response);
                var sub_total = $('.fat-sb-payment-booking-info .sub-total').attr('data-value'),
                    total = $('.fat-sb-payment-booking-info .total').attr('data-value'),
                    discount = 0;
                sub_total = !isNaN(sub_total) && sub_total != '' ? parseFloat(sub_total) : 0;

                if (response.result > 0) {
                    $('.fat-sb-payment-booking-info .discount').attr('data-value', response.amount);
                    $('.fat-sb-payment-booking-info .discount').attr('data-type', response.discount_type);
                    if (response.discount_type == 1) {
                        $('.fat-sb-payment-booking-info .discount').html(response.amount + '%');
                        discount = (sub_total * parseFloat(response.amount)) / 100;
                    } else {
                        $('.fat-sb-payment-booking-info .discount').html('$ ' + response.amount);
                        discount = parseFloat(response.amount);
                    }

                } else {
                    FatSbMain.showMessage(response.message, 2);
                }

                total = sub_total - discount;
                $('.fat-sb-payment-booking-info .total').attr('data-value', total);
                $('.fat-sb-payment-booking-info .total').html('$ ' + total);
                if (discount == 0) {
                    $('.fat-sb-payment-booking-info .discount').html('$ 0');
                }

                self.removeClass('loading');
            },
            error: function () {
                self.removeClass('loading');
            }
        })
    };

    FatSbBooking.getESDay = function (date) {
        switch (date.getDay()) {
            case 0: {
                return 8;
            }
            case 1: {
                return 2;
            }
            case 2: {
                return 3;
            }
            case 3: {
                return 4;
            }
            case 4: {
                return 5;
                break;
            }
            case 5: {
                return 6;
            }
            case 6: {
                return 7;
            }
        }
        return 0;
    };

    FatSbBooking.processSubmitBooking = function (self) {
        var b_id = self.attr('data-id'),
            b_date = $('#b_date', '.fat-sb-booking-form').attr('data-date'),
            service = _.findWhere(FatSbBooking.services, {s_id: $('#b_service_id', form).val()}),
            s_duration = !isNaN(service.s_duration) ? parseInt(service.s_duration) : 0,
            current_page = $('.fat-sb-pagination .ui.button.active'),
            callback = typeof self.attr('data-callback') != 'undefined' ? self.attr('data-callback').split('.') : '';

        if (typeof current_page != 'undefined' && typeof current_page.attr('data-page') != 'undefined') {
            current_page = current_page.attr('data-page');
        } else {
            current_page = 1;
        }

        if (FatSbMain.isFormValid && b_date != '' && typeof b_date != 'undefined') {
            var form = $('.fat-sb-booking-form'),
                data = {
                    b_customer_id: $('#b_customer_id', form).val(),
                    b_customer_number: $('#b_customer_number', form).val(),
                    b_service_cat_id: $('#b_service_cat_id', form).val(),
                    b_service_id: $('#b_service_id', form).val(),
                    b_services_extra: $('#b_services_extra', form).val(),
                    b_loc_id: $('#b_loc_id', form).val(),
                    b_employee_id: $('#b_employee_id', form).val(),
                    b_date: b_date,
                    b_time: $('#b_time', form).val(),
                    pay_now: $('#pay_now').is(':checked') ? 1 : 0,
                    b_gateway_type: $('#b_gateway_type', form).val(),
                    b_description: $('#b_description', form).val(),
                    b_service_duration: s_duration,
                    b_coupon_code: $('#b_coupon_code', form).val(),
                    b_send_notify: $('#send_notify', form).is(':checked') ? 1 : 0,
                    b_pay_now: $('#pay_now', form).is(':checked') ? 1 : 0
                };
            FatSbMain.showProcess(self);
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_save_booking',
                    b_id: b_id,
                    data: data
                }),
                success: function (response) {
                    response = $.parseJSON(response);
                    FatSbMain.closeProcess(self);
                    if (response.result > 0) {

                        //send mail notify
                        if (data['b_send_notify'] == 1 && response.send_mail == '1') {
                            $.ajax({
                                url: fat_sb_data.ajax_url,
                                type: 'POST',
                                data: ({
                                    action: 'fat_sb_send_booking_mail',
                                    b_id: response.result,
                                })
                            });
                        }

                        self.closest('.ui.modal').modal('hide');
                        $('.fat-sb-list-booking .fat-sb-not-found').closest('tr').remove();
                        FatSbMain.showMessage(self.attr('data-success-message'));
                        if ($('.fat-sb-list-booking').length > 0) {
                            FatSbBooking.loadBooking(current_page);
                        }
                        data['b_id'] = response.result;

                        if (callback != '') {
                            var obj = callback.length == 2 ? callback[0] : '',
                                func = callback.length == 2 ? callback[1] : callback[0];
                            if (obj != '') {
                                (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](data) : '';
                            } else {
                                (typeof window[func] != 'undefined' && window[func] != null) ? window[func](data) : '';
                            }
                        }

                    } else {
                        if (typeof response.message != 'undefined') {
                            FatSbMain.showMessage(response.message, 3);
                        } else {
                            FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        }
                    }
                },
                error: function () {
                    FatSbMain.closeProcess(self);
                }
            });

        }
    };

    FatSbBooking.processUpdateProcessStatus = function (self) {
        var b_id = self.attr('data-id'),
            dropdown = self.closest('.ui.dropdown'),
            current_status = self.attr('data-value'),
            b_process_status = self.val();
        FatSbMain.showProcess(dropdown);

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_update_booking_status',
                b_id: b_id,
                b_process_status: b_process_status
            }),
            success: function (response) {
                response = $.parseJSON(response);
                FatSbMain.closeProcess(dropdown);
                if (response.result > 0) {

                    //send mail notify
                    $.ajax({
                        url: fat_sb_data.ajax_url,
                        type: 'POST',
                        data: ({
                            action: 'fat_sb_send_mail_change_status',
                            b_id: b_id,
                        })
                    });

                    FatSbMain.showMessage(response.message);
                    self.attr('data-value', b_process_status);
                } else {
                    if (typeof response.message != 'undefined') {
                        FatSbMain.showMessage(response.message, 3);
                    } else {
                        FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                    }
                    self.addClass('onChange-disabled');
                    setTimeout(function () {
                        dropdown.dropdown('refresh').dropdown('set selected', current_status);
                        self.removeClass('onChange-disabled');
                    }, 800);
                }

            },
            error: function () {
                FatSbMain.closeProcess(dropdown);
            }
        });
    };

    FatSbBooking.bindBookingDetail = function (response) {
        //init employee for view booking detail
        if (typeof response.booking.b_employee_id != 'undefined') {
            var elm_employee = $('.fat-sb-employees-dic > .menu > .scrolling.menu', '.fat-sb-booking-form'),
                employees = response.employees,
                employee_services = '',
                item_class = '';
            for (var $e_index = 0; $e_index < employees.length; $e_index++) {
                if (employees[$e_index].e_id == response.booking.b_employee_id) {
                    item_class = 'item';
                    employee_services = employees[$e_index].e_services;
                } else {
                    item_class = 'item disabled';
                }
                elm_employee.append('<div class="' + item_class + '" data-value="' + employees[$e_index].e_id + '">' + employees[$e_index].e_first_name + ' ' + employees[$e_index].e_last_name + '</div>');
            }

            //init customer number base on service of employee
            if (typeof employee_services != 'undefined' && employee_services != '') {
                for (var $es_index = 0; $es_index < employee_services.length; $es_index++) {
                    if (employee_services[$es_index].s_id == response.booking.b_service_id) {
                        var min_cap = employee_services[$es_index].s_min_cap,
                            max_cap = employee_services[$es_index].s_max_cap,
                            elm_customer_number = $('.fat-sb-customer-number > .menu', '.fat-sb-booking-form');

                        $('.item', elm_customer_number).remove();
                        for (var $cap_index = min_cap; $cap_index <= max_cap; $cap_index++) {
                            elm_customer_number.append('<div class="item" data-value="' + $cap_index + '">' + $cap_index + '</div>');
                        }
                        break;
                    }
                }
                $('.fat-sb-customer-number', '.fat-sb-booking-form').dropdown('refresh').dropdown('set selected', response.booking.b_customer_number);
            }
        }

        //init service extra
        if (typeof response.booking.b_service_id != 'undefined') {
            for (var $s_index = 0; $s_index < response.services.length; $s_index++) {
                if (response.services[$s_index].s_id == response.booking.b_service_id) {
                    var s_extra_ids = response.services[$s_index].s_extra_ids.split(','),
                        elm_service_extra = $('.fat-sb-services-extra-dic > .menu > .scrolling.menu'),
                        services_extra = response.services_extra;

                    for (var $se_index = 0; $se_index < services_extra.length; $se_index++) {
                        if (s_extra_ids.indexOf(services_extra[$se_index].se_id) >= 0) {
                            elm_service_extra.append('<div class="item" data-value="' + services_extra[$se_index].se_id + '">' + services_extra[$se_index].se_name + '</div>')
                        }
                    }
                }
            }
        }

        //init pay now
        if (typeof response.booking.b_pay_now != 'undefined' && response.booking.b_pay_now == '1') {
            $('#pay_now', '.fat-sb-booking-form').prop('checked', true);
            $('div[data-depend="pay_now"]', '.fat-sb-booking-form').slideDown();
        }

        $('.fat-sb-locations-dic', '.fat-sb-booking-form').addClass('disabled');
        $('.fat-sb-service-cat-dic', '.fat-sb-booking-form').addClass('disabled');
        $('.fat-sb-services-extra-dic', '.fat-sb-booking-form').addClass('disabled');
        $('.fat-sb-services-dic', '.fat-sb-booking-form').addClass('disabled');
        $('.fat-sb-employees-dic', '.fat-sb-booking-form').addClass('disabled');

        if(response.booking.b_process_status!=0 && response.booking.b_process_status!=1){
            $('.fat-sb-booking-date-wrap .air-date-picker', '.fat-sb-booking-form').addClass('disabled');
            $('.fat-sb-booking-time-wrap', '.fat-sb-booking-form').addClass('disabled');
        }else{
            FatSbBooking.booking_id = response.booking.b_id;
            FatSbBooking.initSlot();
        }

        $('.fat-sb-customer-dic', '.fat-sb-booking-form').addClass('disabled');
        $('.fat-customer-number-dic', '.fat-sb-booking-form').addClass('disabled');
        $('.fat-sb-service-duration-dic', '.fat-sb-booking-form').addClass('disabled');
        $('a.fat-bt-add-customer', '.fat-sb-booking-form').hide();
    };

    FatSbBooking.processDeleteBooking = function (self) {
        var btDelete = self;
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title, FatSbMain.data.confirm_delete_message, function (result, popup) {
            if (result == 1) {
                var self = $('.fat-sb-bt-confirm.yes', popup),
                    b_ids = [];
                FatSbMain.showProcess(self);
                if (btDelete.hasClass('fat-item-bt-inline')) {
                    b_ids.push(btDelete.attr('data-id'));
                } else {
                    $('input.check-item[type="checkbox"]', 'table.fat-sb-list-booking').each(function () {
                        if ($(this).is(':checked')) {
                            b_ids.push($(this).attr('data-id'));
                        }
                    });
                }
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_booking',
                        b_ids: b_ids
                    }),
                    success: function (response) {
                        try {
                            self.closest('.ui.modal').modal('hide');
                            response = $.parseJSON(response);
                            FatSbMain.closeProcess(self);
                            $('.table-check-all', '.fat-sb-list-booking').prop("checked", false);
                            if (response.result > 0) {

                                if (response.message_success != '') {
                                    FatSbMain.showMessage(response.message_success);
                                    var b_ids_delete = response.ids_delete;
                                    for (var $i = 0; $i < b_ids_delete.length; $i++) {
                                        $('tr[data-id="' + b_ids_delete[$i] + '"]', '.fat-sb-list-booking').remove();
                                    }
                                }

                                if (response.message_error != '') {
                                    FatSbMain.showMessage(response.message_error, 2);
                                }
                            } else {
                                if (typeof response.message != 'undefined') {
                                    FatSbMain.showMessage(response.message, 3);
                                } else {
                                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                                }
                            }
                        } catch (err) {
                        }
                    },
                    error: function () {
                        FatSbMain.closeProcess(self);
                    }
                });
            }
        });

    };

    FatSbBooking.exportBooking = function (self) {
        var b_customer_name = $('#b_customer_name').val(),
            start_date = $('#date_of_book').attr('data-start'),
            start_time = $('#date_of_book').attr('data-start-time'),
            end_date = $('#date_of_book').attr('data-end'),
            end_time = $('#date_of_book').attr('data-end-time'),
            b_employee = $('#b_employee').val(),
            b_customer = $('#b_customer').val(),
            b_service = $('#b_service').val(),
            b_process_status = $('#b_process_status').val();

        FatSbMain.showProcess(self);

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_booking_export',
                b_customer_name: b_customer_name,
                start_date: start_date,
                end_date: end_date,
                start_time: start_time,
                end_time: end_time,
                b_employee: b_employee,
                b_customer: b_customer,
                b_service: b_service,
                b_process_status: b_process_status,
                location: $('#location').val()
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                response = $.parseJSON(response);

                var csv = [],
                    row = [],
                    csvFile,
                    downloadLink;

                if (response.length > 0) {

                    row = [];
                    row.push(fat_sb_data.appointment_date_column);
                    row.push(fat_sb_data.customer_column);
                    row.push(fat_sb_data.employee_column);
                    row.push(fat_sb_data.services_column);
                    row.push(fat_sb_data.start_time_column);
                    row.push(fat_sb_data.end_time_column);
                    row.push(fat_sb_data.duration_column);
                    row.push(fat_sb_data.payment_column);
                    row.push(fat_sb_data.status_column);
                    row.push(fat_sb_data.form_builder_column);
                    csv.push(row.join(","));

                    for (var $i = 0; $i < response.length; $i++) {
                        row = [];
                        row.push(response[$i].b_date);
                        row.push(response[$i].c_first_name + ' ' + response[$i].c_last_name + ' (' + response[$i].c_email + ')');
                        row.push(response[$i].e_first_name + ' ' + response[$i].e_last_name + ' (' + response[$i].e_email + ')');
                        row.push(response[$i].s_name);
                        row.push(response[$i].start);
                        row.push(response[$i].end);
                        row.push(response[$i].b_service_duration_display);

                        if (fat_sb_data.symbol_position == 'before') {
                            row.push(fat_sb_data.symbol + response[$i].b_total_pay);
                        } else {
                            row.push(response[$i].b_total_pay + fat_sb_data.symbol);
                        }
                        if (response[$i].b_process_status == 0) {
                            row.push(fat_sb_data.pending_label);
                        }
                        if (response[$i].b_process_status == 1) {
                            row.push(fat_sb_data.approved_label);
                        }
                        if (response[$i].b_process_status == 2) {
                            row.push(fat_sb_data.canceled_label);
                        }
                        if (response[$i].b_process_status == 3) {
                            row.push(fat_sb_data.rejected_label);
                        }
                        row.push(response[$i].b_form_builder);
                        csv.push(row.join(","));
                    }

                    csv = csv.join("\n");
                    csvFile = new Blob([csv], {type: "text/csv"});

                    // Download link
                    downloadLink = document.createElement("a");

                    // File name
                    downloadLink.download = 'fat_booking.csv';

                    // Create a link to the file
                    downloadLink.href = window.URL.createObjectURL(csvFile);

                    // Hide download link
                    downloadLink.style.display = "none";

                    // Add the link to DOM
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                }


            },
            error: function () {
                FatSbMain.closeProcess(self);
            }
        })

    };

    FatSbBooking.getDateFormat = function () {
        var date_format = FatSbMain.data.date_format;
        date_format = date_format.replace('M', 'M');
        date_format = date_format.replace('F', 'MM');
        date_format = date_format.replace('m', 'mm');
        date_format = date_format.replace('n', 'mm');

        date_format = date_format.replace('d', 'dd');
        date_format = date_format.replace('jS', 'dd');
        date_format = date_format.replace('j', 'dd');
        date_format = date_format.replace('s', 'dd');

        date_format = date_format.replace('Y', 'yyyy');
        return date_format;
    };

    FatSbBooking.processOrder = function (elm) {
        if(!elm.hasClass('active')){
            var container = elm.closest('.fat-sb-order-wrap');
            FatSbBooking.order_by = container.attr('data-order-by');
            FatSbBooking.order = elm.attr('data-order');
            FatSbBooking.loadBooking(1);
            $('.fat-sb-order-wrap i.icon.active', '.fat-sb-list-booking').removeClass('active');
            $('i.icon.' + FatSbBooking.order, container).addClass('active');
        }
    };

    FatSbBooking.calculatePrice = function($quantity, $price, $s_id){
        return $price;
    };

    FatSbBooking.calculateSubtotal = function($quantity, $price, $s_id){
        return ($quantity * $price);
    };

    FatSbBooking.getPriceLabel = function($quantity, $price, $s_id){
        return $price;
    };

    $(document).ready(function () {
        if ($('.fat-sb-booking-container').length > 0) {
            FatSbBooking.init();
        }
    });
})(jQuery);