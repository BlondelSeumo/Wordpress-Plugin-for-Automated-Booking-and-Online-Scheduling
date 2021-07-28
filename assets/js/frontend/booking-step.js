"use strict";
var FatSbBooking_FE = {
    services_cat: [],
    services: [],
    services_work_day: [],
    location: [],
    employees: [],
    services_employee: [],
    services_extra: [],
    bod_field: null,
    e_service: {},
    hasSetActiveDate: false,
    s_multiple_days: 0,
    s_max_multiple_slot: 1,
    s_min_multiple_slot: 1,
    multiple_days: []
};
(function ($) {
    FatSbBooking_FE.init = function () {
        FatSbBooking_FE.initField();
        FatSbBooking_FE.initStripeCardInput();
        FatSbBooking_FE.loadServiceDictionary();

    };

    FatSbBooking_FE.initField = function () {
        $('.fat-booking-container').each(function () {
            var container = $(this);

            $('.ui.steps .step', container).on('click', function () {
                var self = $(this),
                    container = self.closest('.fat-booking-container'),
                    step = self.attr('data-step');
                if (!self.hasClass('active') && !self.hasClass('disabled')) {
                    $('.ui.steps .step.active', container).removeClass('active');
                    self.addClass('active');

                    $('.step-tab-content .step-tab.active', container).fadeOut(function () {
                        $(this).removeClass('active');
                        $('.step-tab-content .step-tab[data-step="' + step + '"]', container).fadeIn(function () {
                            $(this).addClass('active');
                        })
                    })
                }
            });

            //select box
            $('.ui.dropdown', container).each(function () {
                var self = $(this);
                self.dropdown({
                    clearable: self.hasClass('clearable')
                });
            });

            //air datetime
            var date_format = FatSbBooking_FE.getDateFormat(),
                elmBookingDate = $('.air-date-picker', container),
                locale = elmBookingDate.attr('data-locale');
            locale = locale.split('_').length > 1 ? locale.split('_')[0] : locale;
            var option = {
                language: locale,
                minDate: new Date(),
                dateFormat: date_format
            };
            FatSbBooking_FE.bod_field = elmBookingDate.datepicker(option).data('datepicker');

            //popup
            $('button[data-content]', container).popup();

            container.addClass('has-init');
        })
    };

    FatSbBooking_FE.loadServiceDictionary = function () {
        var tabContent = $('.step-tab-content'),
            location_id = $('.fat-sb-step-layout').attr('data-location'),
            category_id = $('.fat-sb-step-layout').attr('data-category');

        FatSbMain_FE.showLoading(tabContent);
        $.ajax({
            url: FatSbMain_FE.data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services_dictionary',
                s_field: FatSbMain_FE.data.ajax_s_field,
                cat_id: category_id
            }),
            success: function (response) {
                response = $.parseJSON(response);

                FatSbBooking_FE.services_cat = response.services_cat;
                FatSbBooking_FE.services = response.services;
                FatSbBooking_FE.services_work_day = response.services_work_day;
                FatSbBooking_FE.services_extra = response.services_extra;
                FatSbBooking_FE.location = response.location;
                FatSbBooking_FE.employees = response.employee;
                FatSbBooking_FE.services_employee = response.services_employee;

                var menu_service_cat = $('.fat-sb-services-cat-dic .menu'),
                    menu_location = $('.fat-sb-location-dic .menu');
                $('.item', menu_service_cat).remove();
                for (var $i = 0; $i < FatSbBooking_FE.services_cat.length; $i++) {
                    menu_service_cat.append('<div class="item" data-value="' + FatSbBooking_FE.services_cat[$i].sc_id + '">' + FatSbBooking_FE.services_cat[$i].sc_name + '</div>');
                }
                $('.fat-sb-services-cat-dic').dropdown('refresh').dropdown('clear');

                $('.item', menu_location).remove();
                for (var $i = 0; $i < FatSbBooking_FE.location.length; $i++) {
                    menu_location.append('<div class="item" data-value="' + FatSbBooking_FE.location[$i].loc_id + '">' + FatSbBooking_FE.location[$i].loc_name + '</div>')
                }
                if (FatSbBooking_FE.location.length > 0) {
                    location_id = typeof location_id != 'undefined' && location_id > 0 ? location_id : FatSbBooking_FE.location[0].loc_id;
                    $('.fat-sb-location-dic').dropdown('refresh').dropdown('set selected', location_id);
                }
                FatSbMain_FE.registerEventProcess($('.fat-booking-container'));

                if (typeof category_id != 'undefined' && category_id > 0) {
                    $('.fat-sb-services-cat-dic').dropdown('set selected', category_id);
                }

                setTimeout(function () {
                    FatSbMain_FE.closeLoading(tabContent);
                });
            },
            error: function () {

            }
        })
    };

    /*
    onChange process
    */
    FatSbBooking_FE.locationOnChange = function (value, text, choice, self) {
        var container = self.closest('.fat-booking-container');
        FatSbBooking_FE.bindEmployee(container);
        if (value != '') {
            self.closest('.field').removeClass('field-error');
        }
    };

    FatSbBooking_FE.numberOnChange = function (value, text, choice, self) {
        var container = self.closest('.fat-booking-container');
        FatSbBooking_FE.initPayment(container);
    };

    FatSbBooking_FE.serviceCatOnChange = function (value, text, choice, self) {
        var container = self.closest('.fat-booking-container'),
            elmService = $('.fat-sb-services-dic', container),
            menuService = $('.fat-sb-services-dic .menu', container),
            menuEmployee = $('.fat-sb-employee-dic .menu', container),
            services = [];

        if (value != '') {
            self.closest('.field').removeClass('field-error');
        }
        FatSbMain_FE.addLoading(container, elmService);
        try {
            $('.item', menuService).remove();
            elmService.dropdown('clear');
            services = _.where(FatSbBooking_FE.services, {s_category_id: value});
            for (var $s_index = 0; $s_index < services.length; $s_index++) {
                menuService.append('<div class="item" data-value="' + services[$s_index].s_id + '">' + services[$s_index].s_name + '<span>' + services[$s_index].s_description + '</span></div>');
            }
            if (services.length == 0) {
                $('.default.text', elmService).text(elmService.attr('data-empty'));
            }
            FatSbBooking_FE.bindEmployee(container);
        } catch (err) {
        } finally {
            setTimeout(function () {
                FatSbMain_FE.removeLoading(container, elmService);
            }, 500);
        }
    };

    FatSbBooking_FE.serviceOnChange = function (value, text, choice, self) {
        var container = self.closest('.fat-booking-container');
        FatSbBooking_FE.bindEmployee(container);
        FatSbBooking_FE.bindServiceExtra(container, value);
        if (value != '') {
            self.closest('.field').removeClass('field-error');
        }

        // show/hide multiples dates
        var service_info = _.findWhere(FatSbBooking_FE.services, {s_id: value});
        FatSbBooking_FE.s_multiple_days = typeof service_info != 'undefined' && typeof service_info.s_multiple_days != 'undefined' ? parseInt(service_info.s_multiple_days) : 0;
        FatSbBooking_FE.s_max_multiple_slot = typeof service_info != 'undefined' && typeof service_info.s_max_multiple_slot != 'undefined' ? parseInt(service_info.s_max_multiple_slot) : 1;
        FatSbBooking_FE.s_min_multiple_slot = typeof service_info != 'undefined' && typeof service_info.s_min_multiple_slot != 'undefined' ? parseInt(service_info.s_min_multiple_slot) : 1;
        FatSbBooking_FE.multiple_days = [];
        if (FatSbBooking_FE.s_multiple_days == 1) {
            container.addClass('multiple-days');
            $('.field.fat-sb-multiple-days', container).fadeIn();
        } else {
            container.removeClass('multiple-days');
            $('.field.fat-sb-multiple-days', container).fadeOut();
        }

    };

    FatSbBooking_FE.serviceExtraOnChange = function (value, text, choice, self) {
        var container = self.closest('.fat-booking-container'),
            date_wrap = $('.fat-sb-booking-date-wrap', container),
            time_wrap = $('.fat-sb-booking-time-wrap', container);

        if (FatSbBooking_FE.bod_field != null) {
            FatSbBooking_FE.bod_field.clear();
        }
        time_wrap.dropdown('clear');
        $('.item', time_wrap).addClass('disabled');

    };

    FatSbBooking_FE.employeeOnChange = function (value, text, choice, self) {
        if (value != '') {
            self.closest('.field').removeClass('field-error');
        }
        var container = self.closest('.fat-booking-container');
        FatSbBooking_FE.initSlot(container);
    };

    FatSbBooking_FE.couponOnChange = function (self) {
        if (self.val() == '') {
            var container = self.closest('.fat-booking-container');
            $('.fat-coupon-error', container).html('');
            FatSbBooking_FE.initPayment(container);
        }
    };

    FatSbBooking_FE.resetValidateField = function (self) {
        if (self.val() != '') {
            self.closest('.field').removeClass('field-error');
        }
    };

    FatSbBooking_FE.bindServiceExtra = function (container, service_id) {
        var service_info = _.where(FatSbBooking_FE.services, {s_id: service_id}),
            s_extra_ids = '',
            service_extra,
            elm_service_extra = $('.fat-sb-services-extra-dic', container),
            eml_service_extra_menu = $('.menu', elm_service_extra);

        $('.item', eml_service_extra_menu).remove();
        elm_service_extra.dropdown('clear');

        if (service_info.length > 0) {
            s_extra_ids = service_info[0].s_extra_ids.split(',');
            service_extra = _.filter(FatSbBooking_FE.services_extra, function (se) {
                return s_extra_ids.indexOf(se.se_id) > -1;
            });
            if (service_extra.length > 0) {
                elm_service_extra.closest('.fields').removeClass('fat-sb-hidden');
                for (var $i = 0; $i < service_extra.length; $i++) {
                    eml_service_extra_menu.append('<div class="item" data-value="' + service_extra[$i].se_id + '">' + service_extra[$i].se_name + '</div>');
                }
            } else {
                elm_service_extra.closest('.fields').addClass('fat-sb-hidden');
            }
        }
    };

    FatSbBooking_FE.bindEmployee = function (container) {
        var elmEmployee = $('.fat-sb-employee-dic', container),
            menuEmployee = $('.fat-sb-employee-dic .menu', container),
            service_cat_id = $('#services_cat', container).val(),
            service_id = $('#service', container).val(),
            location_id = $('#location', container).val(),
            employees = [];
        FatSbMain_FE.addLoading(container, elmEmployee);
        try {
            $('.item', menuEmployee).remove();
            elmEmployee.dropdown('clear');

            if (service_cat_id != '' && service_id != '' && location_id != '') {
                var e_loc_ids = '';
                employees = _.filter(FatSbBooking_FE.employees, function (emp) {
                    e_loc_ids = emp.e_location_ids.split(',');
                    return e_loc_ids.indexOf(location_id) >= 0 && _.where(FatSbBooking_FE.services_employee, {
                        s_id: service_id,
                        e_id: emp.e_id
                    }).length > 0;
                });
                for (var $e_index = 0; $e_index < employees.length; $e_index++) {
                    menuEmployee.append('<div class="item" data-value="' + employees[$e_index].e_id + '">' + employees[$e_index].e_first_name + ' ' + employees[$e_index].e_last_name + '</div>');
                }
                if (employees.length == 0) {
                    $('.default.text', elmEmployee).text(elmEmployee.attr('data-empty'));
                    elmEmployee.dropdown('refresh');
                } else {
                    $('.default.text', elmEmployee).text(elmEmployee.attr('data-default-text'));
                    elmEmployee.dropdown('refresh').dropdown('set selected', employees[0].e_id );
                }
            }
        } catch (err) {
        } finally {
            setTimeout(function () {
                FatSbMain_FE.removeLoading(container, elmEmployee);
            }, 500);
        }
    };

    FatSbBooking_FE.nextOnClick = function (self) {
        var container = self.closest('.fat-booking-container'),
            step_container = self.closest('.ui.step-tab'),
            form = $('.ui.form', step_container),
            step = self.attr('data-next-step');
        if (FatSbMain_FE.validateForm(form)) {
            $('.ui.steps .step[data-step="' + step + '"]', container).removeClass('disabled');
            $('.ui.steps .step[data-step="' + step + '"]', container).trigger('click');
            if (step == 'customer') {

                // init customer number dropdown
                var b_time = $('#b_time', container).val(),
                    min_cap = parseInt(FatSbBooking_FE.e_service.s_min_cap),
                    elm_customer_number = $('.fat-sb-number-of-person-wrap .menu', container),
                    elm_multiple_dates = $('.fat-sb-order-multiple-dates .fat-item-value', container);

                var max_cap = 0;
                for (let day of FatSbBooking_FE.multiple_days) {
                    max_cap = (max_cap > day.available || max_cap == 0) ? day.available : max_cap;
                }

                $('.fat-sb-number-of-person-wrap .text', container).html(min_cap);
                $('#number_of_person', container).val(min_cap);
                $('.item', elm_customer_number).remove();
                for (var $n_index = min_cap; $n_index <= max_cap; $n_index++) {
                    elm_customer_number.append('<div class="item" data-value="' + $n_index + '">' + $n_index + '</div>');
                }
                $('.fat-sb-number-of-person-wrap', container).dropdown('refresh').dropdown('set selected', min_cap);

                //bind multiple days
                $(elm_multiple_dates).empty();
                if (FatSbBooking_FE.s_multiple_days == 1) {
                    for (let day of FatSbBooking_FE.multiple_days) {
                        elm_multiple_dates.append('<div>' + day.date_i18n + ' ' + day.time_label + '</div>');
                    }
                }

                FatSbBooking_FE.initPayment(container);
            }
        }
    };

    FatSbBooking_FE.initCoupon = function (self) {
        var container = self.closest('.fat-booking-container'),
            coupon = $('#coupon', container).val(),
            s_id = $('.fat-sb-services-dic', container).dropdown('get value');
        if (s_id == '' || coupon == '') {
            var discount = 0;
            $('.fat-sb-order-discount .fat-item-value', container).attr('data-value', discount);
            $('.fat-sb-order-discount .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + discount.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);
            FatSbBooking_FE.initPayment(container);
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
                action: 'fat_sb_get_coupon_fe_discount',
                s_field: FatSbMain_FE.data.ajax_s_field,
                coupon: coupon,
                s_id: s_id
            }),
            success: function (response) {
                response = $.parseJSON(response);
                var discount = 0,
                    total = $('.fat-sb-order-total .fat-item-value', container).attr('data-total-origin');
                total = parseFloat(total);

                if (response.result > 0) {
                    $('.fat-sb-order-coupon .fat-coupon-error', container).html('');
                    if (response.discount_type == 1) {
                        discount = (total * parseFloat(response.amount)) / 100;
                    } else {
                        discount = parseFloat(response.amount);
                    }
                } else {
                    $('.fat-sb-order-coupon .fat-coupon-error', container).html(response.message);
                }


                $('.fat-sb-order-discount .fat-item-value', container).attr('data-value', discount);
                $('.fat-sb-order-discount .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + discount.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);

                FatSbBooking_FE.initPayment(container);

                self.removeClass('loading');
            },
            error: function () {
                self.removeClass('loading');
            }
        })
    };

    FatSbBooking_FE.initPayment = function (container) {
        var location = $('.fat-sb-location-dic', container).dropdown('get text'),
            service = $('.fat-sb-services-dic', container).dropdown('get text'),
            service_id = $('.fat-sb-services-dic', container).dropdown('get value'),
            service_extra_ids = $('.fat-sb-services-extra-dic', container).dropdown('get value'),
            employee = $('.fat-sb-employee-dic', container).dropdown('get text'),
            date = $('.fat-sb-booking-date-wrap #b_date', container).attr('data-date'),
            date_label = $('.fat-sb-booking-date-wrap #b_date', container).val(),
            time = $('.fat-sb-booking-time-wrap', container).dropdown('get value'),
            time_label = $('.fat-sb-booking-time-wrap', container).dropdown('get text'),
            number_of_person = $('.fat-sb-number-of-person-wrap', container).dropdown('get value'),
            price = typeof FatSbBooking_FE.e_service.s_price != 'undefined' ? parseFloat(FatSbBooking_FE.e_service.s_price) : 0,
            price_label = '',
            discount = $('#coupon', container).val() != '' ? $('.fat-sb-order-discount .fat-item-value', container).attr('data-value') : 0,
            total = 0,
            total_origin = 0,
            duration = 0,
            end_time = 0,
            service_info = _.where(FatSbBooking_FE.services, {s_id: service_id}),
            time_end_label = '',
            tax_percent = 0,
            tax = 0,
            extra_price = 0,
            extra_price_label = '',
            extra_duration = 0,
            extra_duration_label = '',
            extra_tax = 0,
            total_days = FatSbBooking_FE.multiple_days.length;

        if (typeof service_info[0].s_duration != 'undefined' && service_info[0].s_duration != null) {
            duration = service_info[0].s_duration;
            end_time = parseInt(service_info[0].s_duration) + parseInt(time);

            var hour = Math.floor(end_time / 60),
                minute = end_time % 60,
                suffix = '';
            if (typeof FatSbMain_FE.data.time_format != 'undefined' && FatSbMain_FE.data.time_format == '12h') {
                if (hour > 12) {
                    suffix = ' pm';
                    hour = hour - 12;
                } else {
                    suffix = ' am';
                }
            }
            hour = hour >= 10 ? hour : ('0' + hour);
            minute = minute >= 10 ? minute : ('0' + minute);
            time_end_label = hour + ':' + minute + suffix;
            tax_percent = parseFloat(service_info[0].s_tax);
        }

        if (service_extra_ids != '') {
            service_extra_ids = service_extra_ids.split(',');
            var service_extra = _.filter(FatSbBooking_FE.services_extra, function (se) {
                return service_extra_ids.indexOf(se.se_id) > -1;
            });
            if (typeof service_extra != 'undefined') {
                for (var $se_index = 0; $se_index < service_extra.length; $se_index++) {
                    extra_duration += parseInt(service_extra[$se_index].se_duration);
                    if (service_extra[$se_index].se_price_on_total == 1) {
                        extra_price += parseFloat(service_extra[$se_index].se_price);
                        extra_tax += (parseFloat(service_extra[$se_index].se_price) * parseFloat(service_extra[$se_index].se_tax)) / 100;
                    } else {
                        extra_price += (parseFloat(service_extra[$se_index].se_price) * number_of_person);
                        extra_tax += (number_of_person * parseFloat(service_extra[$se_index].se_price) * parseFloat(service_extra[$se_index].se_tax)) / 100;
                    }
                }
            }
        }

        discount = typeof discount != 'undefined' && discount != '' && !isNaN(discount) ? parseFloat(discount) : 0;

        var $price_base_quantity = FatSbMain_FE.calculatePrice(number_of_person, price, service_id);

        tax = $price_base_quantity * tax_percent / 100;
        tax = tax + extra_tax;
        total_origin = ($price_base_quantity + extra_price + tax) * total_days;
        total = total_origin - discount;
        total = total > 0 ? total : 0;

        price_label = FatSbMain_FE.getPriceLabel(number_of_person, price, $price_base_quantity, service_id);

        extra_price_label = number_of_person + ' ' + FatSbMain_FE.data.person_label + ' x ' + FatSbMain_FE.data.symbol_prefix + extra_price.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix;
        extra_price_label = FatSbMain_FE.data.symbol_prefix + extra_price.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix;
        extra_price_label += ' = ' + FatSbMain_FE.data.symbol_prefix + (extra_price).format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix;

        $('.fat-sb-order-service .fat-item-value', container).text(service_info[0].s_name);
        $('.fat-sb-order-employee .fat-item-value', container).text(employee);
        $('.fat-sb-order-date .fat-item-value', container).text(date_label);
        $('.fat-sb-order-time .fat-item-value', container).html(time_label + ' <span>- ' + time_end_label + '</span>');
        $('.fat-sb-order-location .fat-item-value', container).text(location);
        $('.fat-sb-order-price .fat-item-value', container).html(price_label);
        $('.fat-sb-order-time-end .fat-item-value', container).text(time_end_label);
        $('.fat-sb-order-price', container).attr('data-value', price);
        $('.fat-order-wrap', container).attr('data-price', price);
        $('.fat-order-wrap', container).attr('data-total', total);

        if (extra_price > 0) {
            $('.fat-sb-order-extra-service .fat-item-value', container).text(extra_price_label);
            extra_duration_label = typeof FatSbMain_FE.data.durations[extra_duration] != 'undefined' ? FatSbMain_FE.data.durations[extra_duration] : extra_duration;
            $('.fat-sb-order-extra-service-duration .fat-item-value', container).text(extra_duration_label);
            $('.fat-sb-order-extra-service', container).removeClass('fat-sb-hidden');
        } else {
            $('.fat-sb-order-extra-service', container).addClass('fat-sb-hidden');
        }

        if (tax > 0) {
            $('.fat-sb-order-tax', container).removeClass('fat-sb-hidden');
            $('.fat-sb-order-tax .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + tax.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);
        } else {
            $('.fat-sb-order-tax', container).addClass('fat-sb-hidden');
        }

        $('.fat-sb-order-discount .fat-item-value', container).attr('data-value', discount);
        $('.fat-sb-order-discount .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + discount.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);

        $('.fat-sb-order-total .fat-item-value', container).attr('data-total-origin', total_origin);
        $('.fat-sb-order-total .fat-item-value', container).attr('data-value', total);
        $('.fat-sb-order-total .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + total.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);


    };

    FatSbBooking_FE.initSlot = function (container) {
        var loc_id = $('#location', container).val(),
            s_id = $('#service', container).val(),
            e_id = $('#employee', container).val(),
            date_wrap = $('.fat-sb-booking-date-wrap', container),
            time_wrap = $('.fat-sb-booking-time-wrap', container);

        if ($('.fat-loading-container', container).length == 0) {
            container.append('<div class="fat-loading-container"></div>');
        }
        date_wrap.addClass('fat-loading');
        time_wrap.addClass('fat-loading');
        date_wrap.append('<div class="ui button loading"></div>');
        time_wrap.append('<div class="ui button loading"></div>');
        time_wrap.dropdown('restore defaults');

        if (FatSbBooking_FE.bod_field != null) {
            FatSbBooking_FE.bod_field.clear();
        }
        try {
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'GET',
                data: ({
                    action: 'fat_sb_get_booking_slot_fe',
                    s_id: s_id,
                    e_id: e_id,
                    loc_id: loc_id,
                    s_field: FatSbMain_FE.data.ajax_s_field
                }),
                success: function (response) {
                    response = $.parseJSON(response);

                    $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).addClass('disabled');

                    if (response.result > 0 && typeof response.employee != 'undefined' && response.employee != null) {
                        var bookings = typeof response.bookings != 'undefined' && response.bookings != 'null' ? response.bookings : [],
                            e_day_off = typeof response.employee.e_day_off != 'undefined' && response.employee.e_day_off != 'null' ? response.employee.e_day_off : [],
                            e_break_times = typeof response.employee.e_break_times != 'undefined' && response.employee.e_break_times != 'null' ? response.employee.e_break_times : [],
                            e_schedules = typeof response.employee.e_schedules != 'undefined' && response.employee.e_schedules != 'null' ? response.employee.e_schedules : [],
                            e_services = typeof response.employee.e_services != 'undefined' && response.employee.e_services != 'null' ? response.employee.e_services : {},
                            current_service_id = $('#service').val(),
                            dof_start = '',
                            dof_end = '';

                        FatSbBooking_FE.e_service = _.findWhere(e_services, {s_id: current_service_id});

                        var $default_date = '';
                        var $service_work_day = _.where(FatSbBooking_FE.services_work_day, {s_id: s_id});
                        if ($service_work_day.length > 0) {
                            var from_date = '';
                            for (var $swd_index = 0; $swd_index < $service_work_day.length; $swd_index++) {
                                from_date = moment($service_work_day[$swd_index].from_date + ' 00:00:00');
                                from_date = new Date(from_date.year(), from_date.month(), from_date.date(), 0, 0, 0);
                                if ($default_date == '' || $default_date > from_date) {
                                    $default_date = from_date;
                                }
                            }
                        } else {
                            $default_date = new Date();
                        }

                        $('.air-date-picker', date_wrap).datepicker({
                            onRenderCell: function (date, cellType) {
                                if (cellType == 'day') {

                                    //check service working day
                                    var $service_work_day = _.where(FatSbBooking_FE.services_work_day, {s_id: s_id});
                                    if ($service_work_day.length > 0) {
                                        var from_date = '',
                                            to_date = '',
                                            cell_status = {
                                                classes: 'fat-slot-not-free',
                                                disabled: true
                                            };
                                        for (var $swd_index = 0; $swd_index < $service_work_day.length; $swd_index++) {
                                            from_date = moment($service_work_day[$swd_index].from_date);
                                            to_date = moment($service_work_day[$swd_index].to_date);

                                            from_date = new Date(from_date.year(), from_date.month(), from_date.date(), 0, 0, 0);
                                            to_date = new Date(to_date.year(), to_date.month(), to_date.date(), 23, 59, 59);

                                            if (date >= from_date && date <= to_date) {
                                                cell_status = {
                                                    classes: 'fat-slot-free',
                                                    disabled: false
                                                }
                                            }
                                        }
                                        return cell_status;
                                    }

                                    var $es_day = FatSbBooking_FE.getESDay(date);
                                    for (var $dof_index = 0; $dof_index < e_day_off.length; $dof_index++) {
                                        if (e_day_off[$dof_index].dof_start != '' && e_day_off[$dof_index].dof_end != '') {
                                            dof_start = moment(e_day_off[$dof_index].dof_start);
                                            dof_end = moment(e_day_off[$dof_index].dof_end);

                                            dof_start = new Date(dof_start.year(), dof_start.month(), dof_start.date(), 0, 0, 0);
                                            dof_end = new Date(dof_end.year(), dof_end.month(), dof_end.date(), 23, 59, 59);

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
                                time_wrap.dropdown('restore defaults');

                                if (typeof date == 'undefined' || date == '') {
                                    var selected_date = new Date($('.air-date-picker', date_wrap).attr('data-date') + ' 00:00:00');
                                    $('.air-date-picker', date_wrap).data('datepicker').selectDate(selected_date);
                                    return;
                                }
                                var month = date.getMonth() + 1,
                                    day = date.getDate(),
                                    selected_date_value = '',
                                    now = FatSbMain_FE.parseDateTime(FatSbMain_FE.data.now),
                                    now_minute = now.getHours() * 60 + now.getMinutes();

                                month = parseInt(month);
                                day = parseInt(day);
                                month = month < 10 ? ('0' + month) : month;
                                day = day < 10 ? ('0' + day) : day;

                                setTimeout(function () {
                                    var elm_default_date = $('.datepicker--cell[data-date="' + date.getDate() + '"][data-month="' + date.getMonth() + '"][data-year="' + date.getFullYear() + '"]');
                                    if (!FatSbBooking_FE.hasSetActiveDate && (elm_default_date.hasClass('fat-slot-not-free') || elm_default_date.hasClass('-disabled-'))) {
                                        FatSbBooking_FE.setActiveDate(date, date_wrap, 1);
                                    }
                                }, 200);

                                selected_date_value = date.getFullYear() + '-' + month + '-' + day;
                                $('#b_date', container).attr('data-date', selected_date_value);
                                $('#b_date', container).attr('data-date-i18n', formattedDate);

                                $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).addClass('disabled');
                                if (typeof date == 'undefined' || date == '' || $('#employee', container).val() == '') {
                                    return;
                                }

                                //check service working day
                                var $service_work_day = _.where(FatSbBooking_FE.services_work_day, {s_id: s_id});
                                if ($service_work_day.length > 0) {
                                    var from_date = '',
                                        to_date = '',
                                        result = 'no_slot';
                                    for (var $swd_index = 0; $swd_index < $service_work_day.length; $swd_index++) {
                                        from_date = moment($service_work_day[$swd_index].from_date);
                                        to_date = moment($service_work_day[$swd_index].to_date);

                                        from_date = new Date(from_date.year(), from_date.month(), from_date.date(), 0, 0, 0);
                                        to_date = new Date(to_date.year(), to_date.month(), to_date.date(), 23, 59, 59);

                                        if (date >= from_date && date <= to_date) {
                                            result = 'has_slot';

                                        }
                                    }
                                    if (result == 'no_slot') {
                                        $('.fat-sb-booking-time-wrap', container).append('<div class="fat-empty-time-slot">' + FatSbMain_FE.data.empty_time_slot + '</div>');
                                        return;
                                    }
                                }

                                var $es_day = FatSbBooking_FE.getESDay(date),
                                    time = 0,
                                    self = '',
                                    work_hours = [],
                                    current_service_id = $('#service', container).val(),
                                    service = _.findWhere(FatSbBooking_FE.services, {s_id: current_service_id}),
                                    duration = !isNaN(service.s_duration) ? parseInt(service.s_duration) : 0,
                                    s_break_time = !isNaN(service.s_break_time) ? parseInt(service.s_break_time) : 0,
                                    extra_ids = $('.fat-sb-services-extra-dic', container).dropdown('get value'),
                                    break_times = _.where(e_break_times, {es_day: String($es_day)});

                                if (extra_ids != '') {
                                    extra_ids = extra_ids.split(',');
                                    var extra_info = '';
                                    for (var $ex_index = 0; $ex_index < extra_ids.length; $ex_index++) {
                                        extra_info = _.findWhere(FatSbBooking_FE.services_extra, {se_id: extra_ids[$ex_index]});
                                        if (typeof extra_info != 'undefined' && typeof extra_info.se_duration != 'undefined') {
                                            duration += parseInt(extra_info.se_duration);
                                        }
                                    }
                                }

                                if (current_service_id != '') {
                                    //check work hour
                                    $('.fat-sb-booking-time-wrap .item', container).each(function () {
                                        self = $(this),
                                            time = $(this).attr('data-value');
                                        time = parseInt(time);

                                        var es_break_time_start = 0,
                                            es_break_time_end = 0;

                                        for (var $es_index = 0; $es_index < e_schedules.length; $es_index++) {
                                            if (e_schedules[$es_index].es_day == $es_day) {
                                                work_hours = e_schedules[$es_index].work_hours;
                                                if (typeof work_hours != 'undefined') {
                                                    for (var $wk_index = 0; $wk_index < work_hours.length; $wk_index++) {
                                                        if (work_hours[$wk_index].s_id.indexOf(current_service_id) >= 0 &&
                                                            parseInt(work_hours[$wk_index].es_work_hour_start) <= time && (time + duration + s_break_time) <= parseInt(work_hours[$wk_index].es_work_hour_end)) {
                                                            self.removeClass('disabled');
                                                        }
                                                    }
                                                }
                                                if (typeof break_times != 'undefined') {
                                                    for (var $b_index = 0; $b_index < break_times.length; $b_index++) {
                                                        es_break_time_start = parseInt(break_times[$b_index].es_break_time_start);
                                                        es_break_time_end = parseInt(break_times[$b_index].es_break_time_end);

                                                        if ((time >= es_break_time_start && time < es_break_time_end) ||
                                                            ((time + duration) >= es_break_time_start && (time + duration) <= es_break_time_end)) {
                                                            self.addClass('disabled');
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        //disable time slot that has passed in the current day
                                        if (FatSbMain_FE.equalDay(now, date) && time < now_minute) {
                                            self.addClass('disabled');
                                        }

                                        //default max cap
                                        self.attr('data-max-cap-available', FatSbBooking_FE.e_service.s_max_cap);
                                    });

                                    //check base on booking
                                    var booking_in_day = _.where(bookings, {b_date: selected_date_value}),
                                        booking_service_in_day = _.where(bookings, {
                                            b_date: selected_date_value,
                                            b_service_id: current_service_id.toString(),
                                            b_loc_id: loc_id.toString()
                                        });

                                    if (typeof booking_in_day != 'undefined') {
                                        var b_time = 0,
                                            b_end_time = 0,
                                            b_service_id = 0,
                                            b_loc_id = 0,
                                            time = 0,
                                            end_time = 0,
                                            self,
                                            min_cap = parseInt(FatSbBooking_FE.e_service.s_min_cap),
                                            max_cap = parseInt(FatSbBooking_FE.e_service.s_max_cap),
                                            total_customer = 0,
                                            b_customer_number = 0;

                                        // check for booking this service
                                        $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).each(function () {
                                            self = $(this);
                                            time = parseInt(self.attr('data-value'));
                                            total_customer = 0;
                                            for (var $bs_index = 0; $bs_index < booking_service_in_day.length; $bs_index++) {
                                                b_time = parseInt(booking_service_in_day[$bs_index].b_time);
                                                b_end_time = b_time + parseInt(booking_service_in_day[$bs_index].b_service_duration) + parseInt(booking_service_in_day[$bs_index].b_service_break_time);
                                                b_customer_number = parseInt(booking_service_in_day[$bs_index].b_customer_number);
                                                end_time = time + duration + s_break_time;

                                                if (b_time <= time && end_time <= b_end_time) {
                                                    total_customer += b_customer_number;
                                                }
                                            }
                                            if (total_customer >= max_cap || min_cap > (max_cap - total_customer)) {
                                                self.addClass('disabled');
                                            } else {
                                                self.attr('data-max-cap-available', (max_cap - total_customer));
                                            }
                                        });

                                        var $is_conflict = true;
                                        $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).each(function () {
                                            self = $(this);
                                            time = parseInt(self.attr('data-value'));
                                            end_time = time + parseInt(duration) + s_break_time;

                                            /** check duplicate time with another service */
                                            for (var $bs_index = 0; $bs_index < booking_in_day.length; $bs_index++) {
                                                b_time = parseInt(booking_in_day[$bs_index].b_time);
                                                b_end_time = b_time + parseInt(booking_in_day[$bs_index].b_service_duration) + parseInt(booking_in_day[$bs_index].b_service_break_time);
                                                b_service_id = parseInt(booking_in_day[$bs_index].b_service_id);
                                                b_loc_id = parseInt(booking_in_day[$bs_index].b_loc_id);

                                                if (b_time == time && end_time == b_end_time && b_service_id == current_service_id && b_loc_id == loc_id) {
                                                    $is_conflict = false;
                                                } else {
                                                    $is_conflict = !(end_time <= b_time || time >= b_end_time);
                                                    //$is_conflict = !(end_time <= b_time || time >= b_end_time) && (b_time <= time && end_time <= b_end_time && b_service_id != FatSbBooking_FE.s_id);
                                                }
                                                if ($is_conflict) {
                                                    self.addClass('disabled');
                                                }
                                            }
                                        });
                                    }

                                    if ($('.fat-sb-booking-time-wrap .item:not(.disabled)', container).length == 0) {
                                        $('.fat-sb-booking-time-wrap .text', container).text(FatSbMain_FE.data.empty_time_slot);
                                    }
                                }

                                inst.hide();
                            }
                        });

                        FatSbBooking_FE.setActiveDate($default_date, date_wrap);
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
        } catch (e) {
            console.log(e);
        } finally {
            $('.fat-loading-container', container).remove();
        }

    };

    FatSbBooking_FE.setActiveDate = function ($default_date, date_wrap, is_return_set_active) {
        var default_day = $default_date.getDate(),
            default_month = $default_date.getMonth(),
            default_year = $default_date.getFullYear(),
            elm_default_date = $('.datepicker--cell[data-date="' + default_day + '"][data-month="' + default_month + '"][data-year="' + default_year + '"]');

        if (elm_default_date.hasClass('fat-slot-not-free')) {
            elm_default_date = $('.datepicker--cell:not(.fat-slot-not-free):not(.-disabled-)');
            if (elm_default_date.length > 0) {
                default_day = elm_default_date.attr('data-date');
                default_month = elm_default_date.attr('data-month');
                default_year = elm_default_date.attr('data-year');
                $default_date = new Date(default_year, default_month, default_day);
            }
        }
        elm_default_date = $('.datepicker--cell[data-date="' + default_day + '"][data-month="' + default_month + '"][data-year="' + default_year + '"]');
        $('.air-date-picker', date_wrap).data('datepicker').selectDate($default_date);
        if (typeof is_return_set_active != 'undefined' && is_return_set_active == 1) {
            FatSbBooking_FE.hasSetActiveDate = true;
        }
    };

    FatSbBooking_FE.submitBooking = function (self) {
        var container = self.closest('.fat-booking-container'),
            step_container = self.closest('.ui.step-tab'),
            form = $('.ui.form', step_container),
            step = self.attr('data-next-step');

        if (FatSbMain_FE.validateForm(form)) {
            var service_id = $('.fat-sb-services-dic', container).dropdown('get value'),
                services_extra = $('.fat-sb-services-extra-dic', container).dropdown('get value'),
                employee_id = $('.fat-sb-employee-dic', container).dropdown('get value'),
                loc_id = $('.fat-sb-location-dic', container).dropdown('get value'),
                date = $('.fat-sb-booking-date-wrap #b_date', container).attr('data-date'),
                time = $('.fat-sb-booking-time-wrap', container).dropdown('get value'),
                number_of_person = $('.fat-sb-number-of-person-wrap', container).dropdown('get value'),
                coupon = $('#coupon', container).val(),
                payment_method = $('.fat-sb-payment-method-wrap', container).dropdown('get value'),
                c_first_name = $('#c_first_name', container).val(),
                c_last_name = $('#c_last_name', container).val(),
                c_email = $('#c_email', container).val(),
                c_phone = $('#c_phone', container).val(),
                c_phone_code = $('#phone_code', container).val(),
                total = $('.fat-order-wrap', container).attr('data-total'),
                note = $('#note', container).val(),
                form_builder = {};

            total = !isNaN(total) ? parseFloat(total) : 0;

            if (typeof payment_method == 'undefined' || payment_method == '' || payment_method == null) {
                $('.fat-sb-error-message', container).html(FatSbMain_FE.data.empty_payment_method).removeClass('fat-sb-hidden');
                return;
            }

            $('.fat-sb-field-builder', form).each(function () {
                var field = $(this),
                    field_id = field.attr('name');
                if (field.hasClass('fat-sb-checkbox-group') && $('input[type="checkbox"]', field).is(':checked')) {
                    form_builder[field_id] = [];
                    $('input[type="checkbox"]:checked', field).each(function () {
                        form_builder[field_id].push($(this).val());
                    });
                }
                if (field.hasClass('fat-sb-radio-group') && $('input[type="radio"]', field).is(':checked')) {
                    form_builder[field_id] = $('input[type="radio"]:checked', field).val();
                }

                if (field.hasClass('fat-sb-date-field')) {
                    form_builder[field_id] = field.attr('data-date');
                }

                if (!field.hasClass('fat-sb-date-field') && !field.hasClass('fat-sb-radio-group') && !field.hasClass('fat-sb-checkbox-group')) {
                    form_builder[field_id] = field.val();
                }
            });

            if (payment_method == 'stripe' && total > 0) {
                $('button', 'form#stripe-payment-form').trigger('click');
            } else {
                FatSbMain_FE.addLoading(container, self);
                try {
                    $.ajax({
                        url: FatSbMain_FE.data.ajax_url,
                        type: 'POST',
                        data: ({
                            action: 'fat_sb_save_booking_fe',
                            s_field: FatSbMain_FE.data.ajax_s_field,
                            form_builder: form_builder,
                            data: {
                                b_service_id: service_id,
                                b_services_extra: services_extra,
                                b_loc_id: loc_id,
                                b_employee_id: employee_id,
                                b_date: date,
                                b_time: time,
                                b_customer_number: number_of_person,
                                b_coupon_code: coupon,
                                b_gateway_type: payment_method,
                                c_first_name: c_first_name,
                                c_last_name: c_last_name,
                                c_email: c_email,
                                c_phone: c_phone,
                                c_phone_code: c_phone_code,
                                b_description: note,
                                multiple_days: FatSbBooking_FE.multiple_days
                            }
                        }),
                        success: function (response) {
                            response = $.parseJSON(response);

                            if (response.result > 0) {

                                if (typeof response.redirect_url != 'undefined' && response.redirect_url != '') {
                                    window.location.href = response.redirect_url;
                                    return;
                                }

                                if (payment_method == 'onsite' || payment_method == 'price-package' || payment_method == 'paypal' || total == 0) {
                                    $('.ui.steps .step[data-step="' + step + '"]', container).removeClass('disabled');
                                    $('.ui.steps .step[data-step="' + step + '"]', container).trigger('click');
                                    $('.fat-bt-add-icalendar', container).attr('data-id', response.result);
                                    $('.fat-bt-add-google-calendar', container).attr('data-id', response.result);
                                    FatSbMain_FE.removeLoading(container, self);

                                    $.ajax({
                                        url: FatSbMain_FE.data.ajax_url,
                                        type: 'POST',
                                        data: ({
                                            action: 'fat_sb_send_booking_fe_mail',
                                            s_field: FatSbMain_FE.data.ajax_s_field,
                                            b_id: response.result,
                                        })
                                    });
                                }

                                if (payment_method == 'myPOS' && total > 0) {
                                    var form = $(response.form);
                                    form.hide();
                                    $('body').append(form);
                                    $('form#ipcForm').submit();

                                }

                            } else {
                                self.removeClass('loading');
                                FatSbMain_FE.removeLoading(container, self);
                                $('.fat-sb-error-message', container).html(response.message).removeClass('fat-sb-hidden');
                            }
                        },
                        error: function (response) {
                            FatSbMain_FE.removeLoading(container, self);
                        }
                    });
                } catch (err) {
                    FatSbMain_FE.removeLoading(container, self);
                }
            }
        }
    };

    FatSbBooking_FE.initStripeCardInput = function () {
        if ($('form#stripe-payment-form').length == 0) {
            return;
        }

        var pk = $('form#stripe-payment-form').attr('data-pk');
        if (typeof pk == 'undefined' || pk == '') {
            return;
        }

        var stripe = Stripe(pk),
            elements = stripe.elements();

        var style = {
            base: {
                color: '#32325d',
                lineHeight: '18px',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: 'red',
                iconColor: 'red'
            }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style, hidePostalCode: true});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('stripe-payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var self = $('button.fat-bt-payment', '.fat-booking-container'),
                container = self.closest('.ui.step-tab-content');

            FatSbMain_FE.addLoading(container, self);
            stripe.createToken(card).then(function (result) {

                var self = $('button.fat-bt-payment', '.fat-booking-container'),
                    container = self.closest('.ui.step-tab-content');

                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    FatSbMain_FE.removeLoading(container, self);
                } else {
                    // Send the token to your server.
                    var self = $('button.fat-bt-payment', '.fat-booking-container'),
                        container = self.closest('.fat-booking-container'),
                        service_id = $('.fat-sb-services-dic', container).dropdown('get value'),
                        services_extra = $('.fat-sb-services-extra-dic', container).dropdown('get value'),
                        employee_id = $('.fat-sb-employee-dic', container).dropdown('get value'),
                        loc_id = $('.fat-sb-location-dic', container).dropdown('get value'),
                        date = $('.fat-sb-booking-date-wrap #b_date', container).attr('data-date'),
                        time = $('.fat-sb-booking-time-wrap', container).dropdown('get value'),
                        number_of_person = $('.fat-sb-number-of-person-wrap', container).dropdown('get value'),
                        coupon = $('#coupon', container).val(),
                        payment_method = $('.fat-sb-payment-method-wrap', container).dropdown('get value'),
                        c_first_name = $('#c_first_name', container).val(),
                        c_last_name = $('#c_last_name', container).val(),
                        c_email = $('#c_email', container).val(),
                        c_phone = $('#c_phone', container).val(),
                        c_phone_code = $('#phone_code', container).val(),
                        note = $('#note', container).text();
                    $.ajax({
                        url: FatSbMain_FE.data.ajax_url,
                        type: 'POST',
                        data: ({
                            action: 'fat_sb_save_booking_fe',
                            s_field: FatSbMain_FE.data.ajax_s_field,
                            token: result.token.id,
                            data: {
                                b_service_id: service_id,
                                b_services_extra: services_extra,
                                b_loc_id: loc_id,
                                b_employee_id: employee_id,
                                b_date: date,
                                b_time: time,
                                b_customer_number: number_of_person,
                                b_coupon_code: coupon,
                                b_gateway_type: payment_method,
                                c_first_name: c_first_name,
                                c_last_name: c_last_name,
                                c_email: c_email,
                                c_phone: c_phone,
                                c_phone_code: c_phone_code,
                                b_description: note,
                                multiple_days: FatSbBooking_FE.multiple_days
                            }
                        }),
                        success: function (data) {
                            data = $.parseJSON(data);

                            if (data.code > 0) {

                                $.ajax({
                                    url: FatSbMain_FE.data.ajax_url,
                                    type: 'POST',
                                    data: ({
                                        action: 'fat_sb_send_booking_fe_mail',
                                        s_field: FatSbMain_FE.data.ajax_s_field,
                                        b_id: data.code,
                                    })
                                });

                                FatSbMain_FE.removeLoading(container, self);
                                $('.ui.steps .step[data-step="completed"]', container).removeClass('disabled');
                                $('.ui.steps .step[data-step="completed"]', container).trigger('click');
                                $('.fat-bt-add-icalendar', container).attr('data-id', data.code);
                            } else {
                                FatSbMain_FE.removeLoading(container, self);
                                var errorElement = document.getElementById('card-errors');
                                errorElement.textContent = data.message;
                            }
                        },
                        error: function () {
                            FatSbMain_FE.removeLoading(container, self);
                            var errorElement = document.getElementById('card-errors');
                            errorElement.textContent = FatSbMain_FE.data.error_message;
                        }
                    });
                }
            });
        });

        var paymentType = $('.fat-sb-payment-method-wrap').dropdown('get value');
        if (paymentType === 'stripe') {
            $('.fat-sb-order-stripe').show();
        } else {
            $('.fat-sb-order-stripe').hide();
        }
        $('.fat-sb-payment-method-wrap').on('change', function () {
            if ($(this).dropdown('get value') === 'stripe') {
                $('.fat-sb-order-stripe').show();
            } else {
                $('.fat-sb-order-stripe').hide();
            }
        });
    };

    FatSbBooking_FE.addToICalendar = function (self) {
        var container = self.closest('.fat-booking-container'),
            b_id = self.attr('data-id');

        if (b_id != '' && typeof b_id != 'undefined') {
            FatSbMain_FE.addLoading(container, self);
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_export_calendar',
                    s_field: FatSbMain_FE.data.ajax_s_field,
                    b_id: b_id
                }),
                success: function (response) {
                    var icsFile,
                        downloadLink;

                    icsFile = new Blob([response], {type: "text/ics"});

                    // Download link
                    downloadLink = document.createElement("a");

                    // File name
                    downloadLink.download = 'fat_booking.ics';

                    // Create a link to the file
                    downloadLink.href = window.URL.createObjectURL(icsFile);

                    // Hide download link
                    downloadLink.style.display = "none";

                    // Add the link to DOM
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    FatSbMain_FE.removeLoading(container, self);
                },
                error: function (response) {
                    FatSbMain_FE.removeLoading(container, self);
                }
            });
        }
    };

    FatSbBooking_FE.addToGoogleCalendar = function (self) {
        var container = self.closest('.fat-booking-container'),
            b_id = self.attr('data-id');

        if (b_id != '' && typeof b_id != 'undefined') {
            FatSbMain_FE.addLoading(container, self);
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_export_google_calendar',
                    s_field: FatSbMain_FE.data.ajax_s_field,
                    b_id: b_id
                }),
                success: function (response) {
                    if (response != '') {
                        var downloadLink;
                        // Download link
                        downloadLink = document.createElement("a");

                        // Create a link to the file
                        downloadLink.href = response;
                        downloadLink.target = "_blank";

                        // Hide download link
                        downloadLink.style.display = "none";
                        // Add the link to DOM
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        FatSbMain_FE.removeLoading(container, self);
                    } else {
                        FatSbMain_FE.removeLoading(container, self);
                    }
                },
                error: function (response) {
                    FatSbMain_FE.removeLoading(container, self);
                }
            });
        }
    };

    FatSbBooking_FE.getESDay = function (date) {
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

    FatSbBooking_FE.getDateFormat = function () {
        var date_format = FatSbMain_FE.data.date_format;
        date_format = date_format.replace('M', 'M');
        date_format = date_format.replace('F', 'MM');
        date_format = date_format.replace('m', 'mm');
        date_format = date_format.replace('n', 'mm');

        date_format = date_format.replace('d', 'dd');
        date_format = date_format.replace('j', 'dd');
        date_format = date_format.replace('s', 'dd');

        date_format = date_format.replace('Y', 'yyyy');
        date_format = date_format.replace('年', '/');
        date_format = date_format.replace('月', '/');
        date_format = date_format.replace('日', '');
        return date_format;
    };

    FatSbBooking_FE.timeOnChange = function (value, text, choice, self) {
        if (typeof value != 'undefined' && typeof text != 'undefined' && value != '') {
            var container = self.closest('.fat-booking-container'),
                date = $('#b_date', container).attr('data-date'),
                date_i18n = $('#b_date', container).attr('data-date-i18n'),
                time = value,
                time_label = text,
                available = $(choice).attr('data-max-cap-available'),
                selected_day = _.find(FatSbBooking_FE.multiple_days, function (day) {
                    return (day.date == date && day.time == time);
                });
            if (typeof selected_day == 'undefined') {
                FatSbBooking_FE.multiple_days.push({
                    date: date,
                    date_i18n: date_i18n,
                    time: time,
                    time_label: time_label,
                    available: available
                });
            } else {
                return;
            }
            if (FatSbBooking_FE.s_multiple_days == 1) {
                FatSbBooking_FE.addMultipleDays(container, date, time, date_i18n, time_label);
            }
        }

    };

    FatSbBooking_FE.addMultipleDays = function (container, date, time, date_i18n, time_label) {
        $('.fat-sb-multiple-days ul.list-multiple-days .notice').remove();
        $('.fat-sb-multiple-days ul.list-multiple-days', container).append('<li data-date="' + date + '" data-time="' + time + '" class="">' + date_i18n + ' ' + time_label + '<a href="javascript:;" class="remove-day"><i class="trash alternate outline icon"></i></a></li>');

        if (FatSbBooking_FE.s_min_multiple_slot <= FatSbBooking_FE.multiple_days.length) {
            $('.step-tab-content[data-step="services"] button.fat-next-step', container).removeClass('disabled');
            $('.fat-sb-multiple-days .notice', container).remove();
        } else {
            $('.step-tab-content[data-step="services"] button.fat-next-step', container).addClass('disabled');
        }

        //remove day
        $('.fat-sb-multiple-days a.remove-day', container).off('click').on('click', function () {
            var self = $(this),
                li = self.closest('li'),
                item_date = li.attr('data-date'),
                item_time = li.attr('data-time');

            FatSbBooking_FE.multiple_days = _.reject(FatSbBooking_FE.multiple_days, function (day) {
                return (day.date == item_date && day.time == item_time);
            });
            li.remove();

            if (FatSbBooking_FE.s_min_multiple_slot <= FatSbBooking_FE.multiple_days.length) {
                $('.step-tab-content[data-step="services"] button.fat-next-step', container).removeClass('disabled');
                $('.fat-sb-multiple-days .notice', container).remove();
            } else {
                $('.step-tab-content[data-step="services"] button.fat-next-step', container).addClass('disabled');
            }

            if (FatSbBooking_FE.multiple_days.length == 0) {
                FatSbBooking_FE.addLimitNotice(container);
            }
            //FatSbBooking_FE.setMaxQuantity(container);
        })
    };

    FatSbBooking_FE.addLimitNotice = function (container) {
        $('.fat-sb-multiple-days .notice', container).remove();
        var message = FatSbMain_FE.data.multiple_days_notice;
        message = message.replace('{d}', FatSbBooking_FE.s_min_multiple_slot);
        $('.fat-sb-multiple-days', container).append('<div class="notice">' + message + '</div>');
    };

    $(document).ready(function () {
        FatSbBooking_FE.init();
        FatSbMain_FE.initFormBuilder();
    })
})(jQuery);