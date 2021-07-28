"use strict";
var FatSbBookingDateFirst_FE = {
    b_date: '',
    b_date_label: '',
    b_time: 0,
    e_id: 0,
    s_id: 0,
    employees: {},
    service_info: {},
    hasSetActiveDate: false
};

(function ($) {
    FatSbBookingDateFirst_FE.init = function () {
        FatSbBookingDateFirst_FE.initField();
        FatSbBookingDateFirst_FE.initStripeCardInput();
        FatSbMain_FE.registerEventProcess($('.fat-booking-container.services-date-first'));
    };

    FatSbBookingDateFirst_FE.initField = function () {
        $('.fat-booking-container.services-date-first').each(function () {
            var container = $(this);

            //select box.
            $('.ui.dropdown',container).each(function () {
                var self = $(this);
                self.dropdown({
                    clearable: self.hasClass('clearable')
                });
            });

            //select box
            $('.fat-sb-booking-time-wrap.ui.dropdown', container).each(function () {
                var self = $(this);
                self.dropdown({
                    clearable: true,
                    onShow: function () {
                        FatSbBookingDateFirst_FE.activeTimeItem(self);
                        setTimeout(function () {
                            var item = $('.menu .item:not(.disabled)', self)[0],
                                offset = $(self).offset().top;
                            if (typeof item != 'undefined') {
                                offset = $(item).offset().top - offset - 50;
                                if (offset > 0) {
                                    $('.menu', self).scrollTop(offset);
                                }
                            }
                        }, 300)
                    }
                });
            });

            //air datetime
            var date_format = FatSbBookingDateFirst_FE.getDateFormat(),
                elmBookingDate = $('.air-date-picker', container),
                locale = elmBookingDate.attr('data-locale'),
                working_hour = $('.fat-sb-booking-date-wrap', container).attr('data-working-hour'),
                day_off = $('.fat-sb-booking-date-wrap', container).attr('data-day-off');

            working_hour = $.parseJSON(working_hour);
            day_off = day_off != '' ? $.parseJSON(day_off) : {};
            locale = locale.split('_').length > 1 ? locale.split('_')[0] : locale;
            var option = {
                language: locale,
                minDate: new Date(),
                autoClose: true,
                dateFormat: date_format,
                onRenderCell: function (date, cellType) {
                    if (cellType == 'day') {

                        //checking day off
                        var dof_start = '',
                            dof_end = '';
                        for (var $i = 0; $i < day_off.length; $i++) {
                            dof_start = moment(day_off[$i].dof_start);
                            dof_end = moment(day_off[$i].dof_end);
                            dof_start = new Date(dof_start.year(), dof_start.month(), dof_start.date(), 0, 0, 0);
                            dof_end = new Date(dof_end.year(), dof_end.month(), dof_end.date(), 23, 59, 59);
                            if (date >= dof_start && date <= dof_end) {
                                return {
                                    classes: 'fat-slot-not-free',
                                    disabled: true
                                }
                            }
                        }

                        //check working hour
                        var day = FatSbBookingDateFirst_FE.getESDay(date).toString(),
                            working_day = _.findWhere(working_hour, {es_day: day});

                        if (working_day.es_enable == "0") {
                            return {
                                classes: 'fat-slot-not-free',
                                disabled: true
                            }
                        } else {
                            return {
                                classes: 'fat-slot-free',
                                disabled: false
                            }
                        }
                    }
                },
                onSelect: function (formattedDate, date, inst) {
                    if (typeof date == 'undefined' || date == '') {
                        return;
                    }
                    $('.fat-sb-booking-time-wrap', container).dropdown('clear');
                    FatSbBookingDateFirst_FE.b_date_label = formattedDate;

                    var day = FatSbBookingDateFirst_FE.getESDay(date).toString(),
                        month = date.getMonth() + 1,
                        day_of_date = date.getDate();


                    month = parseInt(month);
                    day_of_date = parseInt(day_of_date);
                    month = month < 10 ? ('0' + month) : month;
                    var selected_date_value = date.getFullYear() + '-' + month + '-' + (day_of_date < 10 ? ('0' + day_of_date) : day_of_date);
                    $('#b_date', container).attr('data-day', day);
                    $('#b_date', container).attr('data-date', selected_date_value);
                    $('#b_date', container).attr('data-date-label', formattedDate);
                }
            };
            elmBookingDate.datepicker(option).data('datepicker').selectDate(new Date());

            container.addClass('has-init');
        });
    };

    FatSbBookingDateFirst_FE.activeTimeItem = function (self) {
        var container = self.closest('.fat-booking-container'),
            from = $('.fat-sb-booking-time-start #start_time', container).val(),
            to = $('.fat-sb-booking-time-end #end_time', container).val(),
            item, item_val;

        if (self.hasClass('fat-sb-booking-time-start')) {
            $('.fat-sb-booking-time-start .menu .item', container).removeClass('disabled');
            if (to != '') {
                $('.fat-sb-booking-time-start .menu .item', container).each(function () {
                    item = $(this);
                    item_val = parseInt(item.attr('data-value'));
                    if (item_val >= to) {
                        item.addClass('disabled');
                    }
                });
            }
        }

        if (self.hasClass('fat-sb-booking-time-end')) {
            $('.fat-sb-booking-time-end .menu .item', container).removeClass('disabled');
            if (from != '') {
                $('.fat-sb-booking-time-end .menu .item', container).each(function () {
                    item = $(this);
                    item_val = parseInt(item.attr('data-value'));
                    if (item_val <= from) {
                        item.addClass('disabled');
                    }
                });
            }
        }
    };

    FatSbBookingDateFirst_FE.nextServiceProvideOnClick = function (elm) {

        var container = $(elm).closest('.fat-booking-container'),
            date = $('#b_date', container).attr('data-date'),
            start_time = $('#start_time', container).val(),
            end_time = $('#end_time', container).val(),
            s_id = $('#s_id', container).val(),
            loc_id = $('#loc_id', container).val();

        FatSbMain_FE.addLoading(container, elm);
        $('.fat-sb-not-found-message', container).addClass('fat-sb-hidden');

        $.ajax({
            url: FatSbMain_FE.data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_employees_available',
                date: date,
                start_time: start_time,
                end_time: end_time,
                s_id: s_id,
                loc_id: loc_id,
                s_field: FatSbMain_FE.data.ajax_s_field
            }),
            success: function (response) {
                response = $.parseJSON(response);
                FatSbMain_FE.removeLoading(container, elm);

                if (response.length == 0) {
                    $('.fat-sb-not-found-message', container).removeClass('fat-sb-hidden');
                    return;
                }

                FatSbBookingDateFirst_FE.employees = response;
                FatSbBookingDateFirst_FE.b_date = date;
                FatSbBookingDateFirst_FE.b_date_label = $('#b_date', container).attr('data-date-label');

                var service_template = wp.template('fat-sb-service-item-template');


                $('.ui.step-tab.service-provider .fat-sb-list-provider', container).append($(service_template(response)));

                $('.ui.step-tab.date-time', container).fadeOut(function () {
                    FatSbMain_FE.removeLoading(container, elm);
                    $(this).removeClass('active');
                    $('.ui.step-tab.service-provider', container).fadeIn(function () {
                        $(this).removeClass('fat-sb-hidden').addClass('active');
                    });
                });

                var loc_name = $('.fat-sb-location-dic .text',container).text();
                $('.fat-sb-location .fat-item-value',container).text(loc_name);

                FatSbMain_FE.registerOnClick($('.fat-sb-tab-content.service-provider',container));
                FatSbMain_FE.initPopupToolTip();
            },
            error: function () {

            }
        });
    };

    FatSbBookingDateFirst_FE.nextCustomerOnClick = function (elm) {
        var container = $(elm).closest('.fat-booking-container');

        FatSbBookingDateFirst_FE.initPayment(container);
        $('.ui.step-tab.service-provider', container).fadeOut(function () {
            $('.ui.step-tab.customer', container).removeClass('fat-sb-hidden');

            // init customer number dropdown
            if (typeof FatSbBookingDateFirst_FE.employee_info != 'undefined' && !isNaN(FatSbBookingDateFirst_FE.employee_info.s_min_cap) && !isNaN(FatSbBookingDateFirst_FE.employee_info.s_max_cap)) {
                var min_cap = parseInt(FatSbBookingDateFirst_FE.employee_info.s_min_cap),
                    max_cap = parseInt(FatSbBookingDateFirst_FE.employee_info.s_max_cap),
                    elm_customer_number = $('.fat-sb-number-of-person-wrap .menu', container);

                $('.fat-sb-number-of-person-wrap .text', container).html(min_cap);
                $('#number_of_person', container).val(min_cap);
                $('.item', elm_customer_number).remove();
                for (var $n_index = min_cap; $n_index <= max_cap; $n_index++) {
                    elm_customer_number.append('<div class="item" data-value="' + $n_index + '">' + $n_index + '</div>');
                }
                $('.fat-sb-number-of-person-wrap', container).dropdown('refresh').dropdown('set selected', min_cap);
            }
        });
    };

    FatSbBookingDateFirst_FE.getESDay = function (date) {
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

    FatSbBookingDateFirst_FE.getDateFormat = function () {
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

    FatSbBookingDateFirst_FE.initPayment = function (container) {
        var number_of_person = $('#number_of_person', container).val(),
            price = typeof FatSbBookingDateFirst_FE.service_info.s_price != 'undefined' ? parseFloat(FatSbBookingDateFirst_FE.service_info.s_price) : 0,
            price_label = '',
            discount = $('#coupon', container).val() != '' ? $('.fat-sb-order-discount .fat-item-value', container).attr('data-value') : 0,
            total = 0,
            total_origin = 0,
            time_end = 0,
            time_label = '',
            tax_percent = 0,
            tax = 0,
            extra_price = 0,
            extra_price_label = '',
            extra_tax = 0;

        if (typeof FatSbBookingDateFirst_FE.service_info.s_duration != 'undefined' && FatSbBookingDateFirst_FE.service_info.s_duration != null) {
            tax_percent = parseFloat(FatSbBookingDateFirst_FE.service_info.s_tax);
        }

        discount = typeof discount != 'undefined' && discount != '' && !isNaN(discount) ? parseFloat(discount) : 0;

        var $price_base_quantity = FatSbMain_FE.calculatePrice(number_of_person, price, FatSbBookingDateFirst_FE.s_id);

        tax = $price_base_quantity * tax_percent / 100;
        tax = tax + extra_tax;
        total_origin = ($price_base_quantity + extra_price + tax)*total_days;
        total =  total_origin - discount;
        total = total > 0 ? total : 0;

        price_label = FatSbMain_FE.getPriceLabel(number_of_person, price, $price_base_quantity,  FatSbBookingDateFirst_FE.s_id);

        $('.fat-sb-order-price .fat-item-value', container).html(price_label);
        $('.fat-sb-order-price', container).attr('data-value',price);
        $('.fat-order-wrap', container).attr('data-price',price);
        $('.fat-order-wrap', container).attr('data-total',total);

        if (tax > 0) {
            $('.fat-sb-order-tax', container).removeClass('fat-sb-hidden');
            $('.fat-sb-order-tax .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + tax.format(2, 3, ',') + FatSbMain_FE.data.symbol_suffix);
        } else {
            $('.fat-sb-order-tax', container).addClass('fat-sb-hidden');
        }

        $('.fat-sb-order-discount .fat-item-value', container).attr('data-value', discount);
        $('.fat-sb-order-discount .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + discount.format(2, 3, ',') + FatSbMain_FE.data.symbol_suffix);

        $('.fat-sb-order-total .fat-item-value', container).attr('data-total-origin', total_origin);
        $('.fat-sb-order-total .fat-item-value', container).attr('data-value', total);
        $('.fat-sb-order-total .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + total.format(2, 3, ',') + FatSbMain_FE.data.symbol_suffix);

    };

    FatSbBookingDateFirst_FE.numberPersonOnChange = function (self) {
        var container = self.closest('.services-date-first');
        FatSbBookingDateFirst_FE.initPayment(container);
    };

    FatSbBookingDateFirst_FE.resetValidateField = function (self) {
        if (self.val() != '') {
            self.closest('.field').removeClass('field-error');
        }
    };

    FatSbBookingDateFirst_FE.paymentOnChange = function (self) {
        var container = self.closest('.fat-sb-services-layout'),
            payment_method = self.val();
        if (payment_method === 'stripe') {
            $('.fat-sb-order-stripe', container).show();
        } else {
            $('.fat-sb-order-stripe', container).hide();
        }
    };

    FatSbBookingDateFirst_FE.initStripeCardInput = function () {
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

            var self = $('button.fat-bt-payment', '.fat-sb-services-layout'),
                container = self.closest('.ui.step-tab-content');

            FatSbMain_FE.addLoading(container, self);
            stripe.createToken(card).then(function (result) {

                var self = $('button.fat-bt-payment', '.fat-sb-services-layout'),
                    container = self.closest('.ui.step-tab-content');

                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    FatSbMain_FE.removeLoading(container, self);
                } else {
                    // Send the token to your server.
                    var self = $('button.fat-bt-payment', '.fat-sb-services-layout'),
                        container = self.closest('.fat-sb-services-layout'),
                        service_id = FatSbBookingDateFirst_FE.s_id,
                        services_extra = '',
                        employee_id = FatSbBookingDateFirst_FE.e_id,
                        loc_id = 0,
                        date = FatSbBookingDateFirst_FE.b_date,
                        time = FatSbBookingDateFirst_FE.b_time,
                        number_of_person = $('#number_of_person', container).val(),
                        coupon = $('#coupon', container).val(),
                        payment_method = $('#payment_method', container).val(),
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
                                b_description: note
                            }
                        }),
                        success: function (data) {
                            data = $.parseJSON(data);
                            if (data.code > 0) {

                                $('.fat-sb-tab-content.customer',container).fadeOut(function(){
                                    $('.fat-sb-tab-content.customer',container).addClass('fat-sb-hidden');
                                    $('.fat-sb-tab-content.completed',container).fadeIn(function(){
                                        $('.fat-sb-tab-content.completed', container).removeClass('fat-sb-hidden');
                                        $('.fat-bt-add-icalendar', container).attr('data-id',response.result);
                                        $('.fat-bt-add-google-calendar', container).attr('data-id',response.result);
                                    });
                                });

                                FatSbMain_FE.removeLoading(container, self);

                                $.ajax({
                                    url: FatSbMain_FE.data.ajax_url,
                                    type: 'POST',
                                    data: ({
                                        action: 'fat_sb_send_booking_fe_mail',
                                        s_field: FatSbMain_FE.data.ajax_s_field,
                                        b_id: data.code,
                                    })
                                });

                            } else {
                                FatSbMain_FE.removeLoading(container, self);
                                var errorElement = document.getElementById('card-errors');
                                errorElement.textContent = data.message;
                            }
                        },
                        error: function () {
                            FatSbMain_FE.removeLoading(container, self);
                            $('.fat-sb-error-message', container).html(data.message).removeClass('fat-sb-hidden');
                        }
                    });
                }
            });
        });

        var paymentType = $('.fat-sb-payment-method-wrap').val();
        if (paymentType === 'stripe') {
            $('.fat-sb-order-stripe').show();
        } else {
            $('.fat-sb-order-stripe').hide();
        }
    };

    FatSbBookingDateFirst_FE.processGetTimeSlot = function(elm){
        var container = elm.closest('.fat-sb-tab-content.service-provider'),
            booking_container = elm.closest('.fat-booking-container'),
            column = !isNaN(booking_container.attr('data-column')) ? parseInt(booking_container.attr('data-column')) : 3,
            item = elm.closest('.item'),
            item_index = parseInt(item.attr('data-index')),
            hasProcess = $('.ui.button.loading', container).length > 0 ? 1 : 0,
            services_thumb = $('img', item).attr('src'),
            services_name = $('.services-name',item).text(),
            employee_name = $('.employee-name', item).text(),
            employee_thumb = item.attr('data-e-img'),
            date_format = $('#b_date').attr('data-date-format'),
            loc_id = $('#loc_id', booking_container).val();

        if (!hasProcess) {
            var d_id = $(elm).attr('data-id').split('_'),
                s_id = d_id.length == 2 ? d_id[0] : '',
                e_id = d_id.length == 2 ? d_id[1] : '';

            if (s_id == '' || e_id == '') {
                return;
            }

            elm.addClass('loading');
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'GET',
                data: ({
                    action: 'fat_sb_get_employee_time_slot',
                    s_field: FatSbMain_FE.data.ajax_s_field,
                    data: {
                        s_id: s_id,
                        e_id: e_id,
                        loc_id: loc_id,
                        start_time: $('#start_time').val(),
                        end_time: $('#end_time').val(),
                        date: $('#b_date').attr('data-date')
                    }
                }),
                success: function (response) {
                    elm.removeClass('loading');
                    elm.popup('hide');
                    response = $.parseJSON(response);

                    FatSbBookingDateFirst_FE.e_id = e_id;
                    FatSbBookingDateFirst_FE.s_id = s_id;

                    var timeSlotContainer =  $('.fat-sb-time-slot-container', container),
                        timeOut = 0;

                    if(timeSlotContainer.length>0){
                        timeOut = 500;
                        timeSlotContainer.slideUp(timeOut,function(){
                            timeSlotContainer.remove();
                        });
                    }
                    setTimeout(function(){
                        if(typeof matchMedia === 'function'){
                            if(matchMedia('(max-width: 768px)').matches){
                                column = 2;
                            }
                            if(matchMedia('(max-width: 767px)').matches){
                                column = 1;
                            }
                        }
                        var serviceItem = $(item).closest('.fat-sb-service-item'),
                            nextItem = serviceItem,
                            time_slot_template = wp.template('fat-sb-time-slot-template'),
                            total_item = $('.fat-sb-list-provider .fat-sb-service-item', booking_container).length,
                            nextIndex = item_index +  (item_index % column > 0 ? (column - item_index%column) : 0);

                        nextIndex = total_item > column ? nextIndex : total_item;


                        nextItem = $('.fat-sb-service-item  .item[data-index="' + nextIndex + '"]', container).closest('.fat-sb-service-item');

                        $('.fat-sb-service-item', container).removeClass('active');

                        serviceItem.addClass('active');

                        $(time_slot_template(response.time_slot)).insertAfter(nextItem);
                        if(typeof response.time_slot=='undefined' || response.time_slot.length==0){
                            $('.fat-sb-time-slot-inner').append('<div class="fat-empty-time-slot">' + FatSbMain_FE.data.empty_time_slot +'</div>');
                        }
                        $('.fat-sb-time-slot-container', container).slideDown();

                        FatSbMain_FE.registerOnClick(container);
                    }, (timeOut + 100));
                },
                error: function () {
                    elm.removeClass('loading');
                }
            });
        }
    };

    FatSbBookingDateFirst_FE.timeSlotSelected = function(elm){
        var container = elm.closest('.fat-booking-container.services-date-first');
        $('.fat-sb-tab-content.service-provider', container).fadeOut(function(){

            var time_label = $('.item-inner', elm).text(),
                seat = $(elm).attr('data-seat');

            FatSbBookingDateFirst_FE.service_info = _.findWhere(FatSbBookingDateFirst_FE.employees, {e_id: FatSbBookingDateFirst_FE.e_id, s_id: FatSbBookingDateFirst_FE.s_id});

            FatSbBookingDateFirst_FE.b_time = $(elm).attr('data-value');

            $('.fat-sb-order-service .fat-item-value', container).html(FatSbBookingDateFirst_FE.service_info.s_name);
            $('.fat-sb-order-employee .fat-item-value', container).html(FatSbBookingDateFirst_FE.service_info.e_first_name + ' ' + FatSbBookingDateFirst_FE.service_info.e_last_name);
            $('.fat-sb-order-date .fat-item-value', container).html(FatSbBookingDateFirst_FE.b_date_label);
            $('.fat-sb-order-time .fat-item-value', container).html(time_label);
            $('.fat-sb-order-price .fat-item-value', container).html(FatSbBookingDateFirst_FE.service_info.s_price);
            //$('.fat-sb-order-tax .fat-item-value').html(info.s_tax);

            $('select#number_of_person option', container).remove();
            for(var $i=1; $i<= seat; $i++){
                $('select#number_of_person', container).append('<option value="' + $i+ '">' + $i + '</option>');
            }
            FatSbBookingDateFirst_FE.initPayment(container);
            $('.fat-sb-tab-content.customer', container).fadeIn();
        });
    };

    FatSbBookingDateFirst_FE.couponOnChange = function (self) {
        if (self.val() == '') {
            var container = self.closest('.fat-sb-services-layout');
            $('.fat-coupon-error', container).html('');
            FatSbBookingDateFirst_FE.initPayment(container);
        }
    };

    FatSbBookingDateFirst_FE.initCoupon = function (self) {
        var container = self.closest('.fat-sb-services-layout'),
            coupon = $('#coupon', container).val(),
            s_id = FatSbBookingDateFirst_FE.s_id;
        if (s_id == '' || coupon == '') {
            var discount = 0;
            $('.fat-sb-order-discount .fat-item-value', container).attr('data-value', discount);
            $('.fat-sb-order-discount .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + discount.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);
            FatSbBookingDateFirst_FE.initPayment(container);
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

                FatSbBookingDateFirst_FE.initPayment(container);

                self.removeClass('loading');
            },
            error: function () {
                self.removeClass('loading');
            }
        })
    };

    FatSbBookingDateFirst_FE.submitBooking = function(self){
        var container = self.closest('.fat-booking-container'),
            step_container = self.closest('.fat-sb-tab-content.customer'),
            form = $('.ui.form', step_container);
        if (FatSbMain_FE.validateForm(form)) {
            var number_of_person = $('#number_of_person', container).val(),
                coupon = $('#coupon', container).val(),
                payment_method = $('#payment_method', container).val(),
                c_first_name = $('#c_first_name', container).val(),
                c_last_name = $('#c_last_name', container).val(),
                c_email = $('#c_email', container).val(),
                c_phone = $('#c_phone', container).val(),
                c_phone_code = $('#phone_code', container).val(),
                note = $('#note', container).val(),
                total = $('.fat-order-wrap', container).attr('data-total'),
                services_extra = '',
                form_builder = {};

            total = !isNaN(total) ? parseFloat(total) : 0;

            if(typeof payment_method=='undefined' || payment_method=='' || payment_method == null){
                $('.fat-sb-error-message',container).html(FatSbMain_FE.data.empty_payment_method).removeClass('fat-sb-hidden');
                return;
            }

            $('.fat-sb-order-extra-service .fat-sb-service-extra-item',container).each(function(){
                if($(this).is(':checked')){
                    if(services_extra==''){
                        services_extra += $(this).val();
                    }else{
                        services_extra += ',' + $(this).val();
                    }
                }
            });

            $('.fat-sb-field-builder',form).each(function(){
                var field = $(this),
                    field_id = field.attr('name');
                if(field.hasClass('fat-sb-checkbox-group') && $('input[type="checkbox"]', field).is(':checked')){
                    form_builder[field_id] = [];
                    $('input[type="checkbox"]:checked',field).each(function(){
                        form_builder[field_id].push($(this).val());
                    });
                }
                if(field.hasClass('fat-sb-radio-group') && $('input[type="radio"]', field).is(':checked')){
                    form_builder[field_id] = $('input[type="radio"]:checked',field).val();
                }

                if(field.hasClass('fat-sb-date-field')){
                    form_builder[field_id] = field.attr('data-date');
                }

                if(!field.hasClass('fat-sb-date-field') && !field.hasClass('fat-sb-radio-group') && !field.hasClass('fat-sb-checkbox-group')){
                    form_builder[field_id] = field.val();
                }
            });

            if(payment_method=='stripe' && total > 0){
                $('form#stripe-payment-form button', container).trigger('click');
            }else{
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
                                b_service_id: FatSbBookingDateFirst_FE.s_id,
                                b_services_extra: services_extra,
                                b_loc_id: 0,
                                b_employee_id: FatSbBookingDateFirst_FE.e_id,
                                b_date: FatSbBookingDateFirst_FE.b_date,
                                b_time: FatSbBookingDateFirst_FE.b_time,
                                b_customer_number: number_of_person,
                                b_coupon_code: coupon,
                                b_gateway_type: payment_method,
                                c_first_name: c_first_name,
                                c_last_name: c_last_name,
                                c_email: c_email,
                                c_phone: c_phone,
                                c_phone_code: c_phone_code,
                                b_description: note
                            }
                        }),
                        success: function (response) {
                            response = $.parseJSON(response);
                            if (response.result > 0) {

                                if(typeof response.redirect_url != 'undefined' && response.redirect_url != ''){
                                    window.location.href = response.redirect_url;
                                    return;
                                }

                                if(payment_method=='myPOS' && total >0){
                                    var form = $(response.form);
                                    form.hide();
                                    $('body').append(form);
                                    $('form#ipcForm').submit();
                                    return;
                                }

                                if (payment_method == 'onsite' || payment_method == 'price-package' || payment_method == 'paypal' || total == 0) {

                                    $('.fat-sb-tab-content.customer',container).fadeOut(function(){
                                        $('.fat-sb-tab-content.customer',container).addClass('fat-sb-hidden');
                                        $('.fat-sb-tab-content.completed',container).fadeIn(function(){
                                            $('.fat-sb-tab-content.completed', container).removeClass('fat-sb-hidden');
                                            $('.fat-bt-add-icalendar', container).attr('data-id',response.result);
                                            $('.fat-bt-add-google-calendar', container).attr('data-id',response.result);
                                        });
                                    });

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

                            }else{
                                FatSbMain_FE.removeLoading(container, self);
                                $('.fat-sb-error-message',container).html(response.message).removeClass('fat-sb-hidden');
                            }
                        },
                        error: function (response) {
                            FatSbMain_FE.removeLoading(container, self);
                        }
                    });
                } catch (err) {
                }
            }
        }
    };

    FatSbBookingDateFirst_FE.addToICalendar = function(self){
        var container = self.closest('.fat-sb-services-layout'),
            b_id = self.attr('data-id');

        if(b_id!='' && typeof b_id!='undefined'){
            FatSbMain_FE.addLoading(container, self);
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_export_calendar',
                    s_field: FatSbMain_FE.data.ajax_s_field,
                    b_id:b_id
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

    FatSbBookingDateFirst_FE.addToGoogleCalendar = function(self){
        var container = self.closest('.fat-booking-container'),
            b_id = self.attr('data-id');

        if(b_id!='' && typeof b_id!='undefined'){
            FatSbMain_FE.addLoading(container, self);
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_export_google_calendar',
                    s_field: FatSbMain_FE.data.ajax_s_field,
                    b_id:b_id
                }),
                success: function (response) {
                    if(response!=''){
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
                    }else{
                        FatSbMain_FE.removeLoading(container, self);
                    }
                },
                error: function (response) {
                    FatSbMain_FE.removeLoading(container, self);
                }
            });
        }
    };

    $(document).ready(function () {
        FatSbBookingDateFirst_FE.init();
        FatSbMain_FE.initFormBuilder();
    })

})(jQuery);