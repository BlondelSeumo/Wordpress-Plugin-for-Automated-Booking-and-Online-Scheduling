"use strict";
var FatSbCalendarServices_FE = {
    s_id: 0,
    e_id: 0,
    loc_id: 0,
    s_price: 0,
    s_tax: 0,
    b_total: 0,
    s_name: '',
    s_min_cap: 0,
    s_max_cap: 0,
    payment_method: '',
    date: '',
    time_slot: 0,
    b_time_label: '',
    schedules: [],
    services: [],
    bookings: [],
    services_work_day: [],
    current_slot: 0,
    current_time: ''
};
(function ($) {
    FatSbCalendarServices_FE.init = function () {

        //phone code
        $('.fat-sb-calendar-layout .ui.dropdown').each(function () {
            var self = $(this);
            self.dropdown({
                clearable: self.hasClass('clearable')
            });
        });

        FatSbCalendarServices_FE.initCalendar();
        FatSbCalendarServices_FE.initStripeCardInput();
    };

    FatSbCalendarServices_FE.initCalendar = function () {
        var startOfWeek = moment().startOf('week'),
            endOfWeek = moment().endOf('week'),
            filter = startOfWeek.format('MMM DD') + ' - ' + endOfWeek.format('MMM DD, YYYY');

        $('.fat-sb-calendar-layout').css('opacity', 1);
        FatSbCalendarServices_FE.changeWeek(0, startOfWeek.format('YYYY-MM-DD'));

        $('.fat-sb-calendar-wrap span.next-week').on('click', function () {
            var week = $(this).closest('.calendar-filter').attr('data-week');
            FatSbCalendarServices_FE.changeWeek(1, week);
        });

        $('.fat-sb-calendar-wrap span.prev-week').on('click', function () {
            var week = $(this).closest('.calendar-filter').attr('data-week');
            FatSbCalendarServices_FE.changeWeek(-1, week);
        });
        $('.fat-sb-calendar-layout').addClass('has-init');

    };

    FatSbCalendarServices_FE.changeWeek = function (direct, start_week) {
        //direct: 1 -> next, -1: previous
        var diff = 0;
        if (direct == -1) {
            diff = -7;
        }
        if (direct == 1) {
            diff = 7;
        }
        var startOfWeek = moment(start_week).add(diff, 'd').startOf('week'),
            endOfWeek = moment(start_week).add(diff, 'd').endOf('week'),
            filter = startOfWeek.format('MMM DD') + ' - ' + endOfWeek.format('MMM DD, YYYY');

        $('.fat-sb-calendar-wrap div.calendar-filter').attr('data-week', startOfWeek.format('YYYY-MM-DD'));
        $('.fat-sb-calendar-wrap input.week-filter').val(filter);

        $('.week-day-header.mon .week-date').html(startOfWeek.format('DD'));
        $('.week-day-content.mon').attr('data-date', startOfWeek.format('YYYY-MM-DD'));
        $('.week-day-content.mon .week-header-mobile').html(FatSbMain_FE.data.mon + ', ' + startOfWeek.format('DD') + '-' + startOfWeek.format('MMM'));

        startOfWeek = moment(startOfWeek).add(1, 'd');
        $('.week-day-header.tue .week-date').html(startOfWeek.format('DD'));
        $('.week-day-content.tue').attr('data-date', startOfWeek.format('YYYY-MM-DD'));
        $('.week-day-content.tue .week-header-mobile').html(FatSbMain_FE.data.tue + ', ' + startOfWeek.format('DD') + '-' + startOfWeek.format('MMM'));

        startOfWeek = moment(startOfWeek).add(1, 'd');
        $('.week-day-header.wed .week-date').html(startOfWeek.format('DD'));
        $('.week-day-content.wed').attr('data-date', startOfWeek.format('YYYY-MM-DD'));
        $('.week-day-content.wed .week-header-mobile').html(FatSbMain_FE.data.wed + ', ' + startOfWeek.format('DD') + '-' + startOfWeek.format('MMM'));

        startOfWeek = moment(startOfWeek).add(1, 'd');
        $('.week-day-header.thu .week-date').html(startOfWeek.format('DD'));
        $('.week-day-content.thu').attr('data-date', startOfWeek.format('YYYY-MM-DD'));
        $('.week-day-content.thu  .week-header-mobile').html(FatSbMain_FE.data.thu + ', ' + startOfWeek.format('DD') + '-' + startOfWeek.format('MMM'));

        startOfWeek = moment(startOfWeek).add(1, 'd');
        $('.week-day-header.fri .week-date').html(startOfWeek.format('DD'));
        $('.week-day-content.fri').attr('data-date', startOfWeek.format('YYYY-MM-DD'));
        $('.week-day-content.fri  .week-header-mobile').html(FatSbMain_FE.data.fri + ', ' + startOfWeek.format('DD') + '-' + startOfWeek.format('MMM'));

        startOfWeek = moment(startOfWeek).add(1, 'd');
        $('.week-day-header.sat .week-date').html(startOfWeek.format('DD'));
        $('.week-day-content.sat').attr('data-date', startOfWeek.format('YYYY-MM-DD'));
        $('.week-day-content.sat  .week-header-mobile').html(FatSbMain_FE.data.sat + ', ' + startOfWeek.format('DD') + '-' + startOfWeek.format('MMM'));

        startOfWeek = moment(startOfWeek).add(1, 'd');
        $('.week-day-header.sun .week-date').html(startOfWeek.format('DD'));
        $('.week-day-content.sun').attr('data-date', startOfWeek.format('YYYY-MM-DD'));
        $('.week-day-content.sun  .week-header-mobile').html(FatSbMain_FE.data.sun +  ', ' + startOfWeek.format('DD') + '-' + startOfWeek.format('MMM'));

        FatSbCalendarServices_FE.loadService();

    };

    FatSbCalendarServices_FE.loadService = function () {
        var container = $('.fat-sb-calendar-layout'),
            service_id = $(container).attr('data-service'),
            start = $('.fat-sb-calendar-wrap div.calendar-filter').attr('data-week'),
            start_date = moment(start + ' 23:59:59', 'YYYY-MM-DD hh:mm:ss'),
            end = moment(start).endOf('week').format('YYYY-MM-DD'),
            now = moment(),
            $week_detail_elm = $('.week-detail', container),
            $time_slot_elm = $('.fat-sb-time-slot-wrap', container);

        FatSbMain_FE.showLoading(container);
        $week_detail_elm.css('opacity', 0);

        $('.fat-sb-time-list ul', container).empty();
        $('.fat-sb-time-slot-wrap', container).fadeOut();
        $.ajax({
            url: FatSbMain_FE.data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services_available_in_weekly',
                s_field: FatSbMain_FE.data.ajax_s_field,
                start: start,
                end: end,
                service_id: service_id
            }),
            success: function (response) {
                response = $.parseJSON(response);
                if (typeof response == 'undefined' || response == null) {
                    return;
                }

                var services = response.services,
                    es_schedule = response.es_schedule,
                    $week_content_elm = $('.fat-sb-calendar-layout .week-content');

                FatSbCalendarServices_FE.loc_id = response.loc_id;
                FatSbCalendarServices_FE.schedules = es_schedule;
                FatSbCalendarServices_FE.services_work_day = response.services_work_day;
                FatSbCalendarServices_FE.services = services;
                FatSbCalendarServices_FE.bookings = response.bookings;
                FatSbCalendarServices_FE.current_slot = response.current_slot;
                FatSbCalendarServices_FE.current_time = moment(response.current_time + ' 00:00:00', 'YYYY-MM-DD hh:mm:ss');

                $('.week-day-content ul.list-services', $week_content_elm).empty();

                var s_ids = [],
                    s_day = [],
                    day_in_week = moment().day() + 1,
                    week_day = moment(start + ' 23:59:59', 'YYYY-MM-DD hh:mm:ss'),
                    s_work_day = [],
                    is_available = 0;

                //init for day in week
                for (var $i = 2; $i <= 8; $i++) {
                    if (typeof es_schedule[$i] != 'undefined' && es_schedule[$i].length > 0) {
                        s_ids = [];
                        for (let es of es_schedule[$i]) {
                            s_ids.push(es['s_id']);
                        }
                        s_day = _.filter(services, function (s) {
                            return s_ids.indexOf(s.s_id) >=0 || s_ids[0]=='0'  ? 1 : 0;
                        });

                        for (let s of s_day) {
                            //check service work day
                            is_available = 0;
                            s_work_day = _.filter(FatSbCalendarServices_FE.services_work_day, function(swd){
                                return swd.s_id == s.s_id;
                            });
                            if(s_work_day.length > 0){
                                for(let s_wd of s_work_day){
                                    if(moment(week_day.format('YYYY-MM-DD')).isBetween(s_wd.from_date, s_wd.to_date, undefined, '[]')){
                                        is_available = 1;
                                        break;
                                    }
                                }
                            }else{
                                is_available = 1;
                            }


                            if(is_available==1){
                                $('.week-day-content.day-' + $i + ' ul.list-services', $week_content_elm).append('<li data-day="' + $i + '" data-sid="' + s.s_id + '" data-duration="' + s.s_duration + '" data-break-time="' + s.s_break_time + '">' + s.s_name + '</li>');
                            }
                        }
                        if (week_day < now) {
                            $('.week-day-content.day-' + $i, $week_content_elm).addClass('week-disable');
                        } else {
                            $('.week-day-content.day-' + $i, $week_content_elm).removeClass('week-disable');
                        }
                    }

                    week_day = week_day.add(1, 'd');

                }

                $week_detail_elm.css('opacity', 1);
                FatSbMain_FE.closeLoading(container);

                $('.week-day-content:not(.week-disable) .list-services li', container).on('click', function () {
                    var elm = $(this);
                    if (!elm.hasClass('active')) {
                        $('.list-services li.active', container).removeClass('active');
                        elm.addClass('active');
                        FatSbCalendarServices_FE.initTimeSlot(elm, container);
                        if ($(window).width() < 768) {
                            $('html,body').animate({scrollTop: $('.fat-sb-time-slot-wrap').offset().top - 50}, 1000);
                        }
                    }
                });
            },
            error: function () {
                $week_detail_elm.css('opacity', 1);
                FatSbMain_FE.closeLoading(container);
            }
        })
    };

    FatSbCalendarServices_FE.initTimeSlot = function (elm, container) {
        var day = elm.attr('data-day'),
            s_id = elm.attr('data-sid'),
            duration = elm.attr('data-duration'),
            break_time = elm.attr('data-break-time'),
            time_list_elm = $('.fat-sb-time-list', container),
            time_slot_wrap = $('.fat-sb-time-slot-wrap', container),
            date = elm.closest('.week-day-content').attr('data-date'),
            date_str = date,
            service = _.find(FatSbCalendarServices_FE.services, function (item) {
                return item.s_id == s_id;
            }),
            elm_service = $('.fat-sb-service-info', container);

        date = moment(date, +' 00:00:00', 'YYYY-MM-DD hh:mm:ss');


        FatSbCalendarServices_FE.s_id = s_id;
        FatSbCalendarServices_FE.s_name = service.s_name;
        FatSbCalendarServices_FE.s_price = service.s_price;
        FatSbCalendarServices_FE.e_id = service.e_id;
        FatSbCalendarServices_FE.s_tax = service.s_tax;
        FatSbCalendarServices_FE.s_min_cap = service.s_min_cap;
        FatSbCalendarServices_FE.s_max_cap = service.s_max_cap;
        FatSbCalendarServices_FE.date = elm.closest('.week-day-content').attr('data-date');

        $('ul', time_list_elm).empty();

        elm_service.fadeOut(function(){
            $('.fat-sb-thumb img',elm_service).remove();
            $('.fat-sb-thumb',elm_service).append('<img src="' + service.s_image_url + '">');
            $('.fat-sb-service-name',elm_service).html(service.s_name);
            $('.fat-sb-service-desc',elm_service).html(service.s_description);
            elm_service.fadeIn();
        });

        time_slot_wrap.fadeOut();
        FatSbMain_FE.showLoading(time_slot_wrap);
        var schedule = _.filter(FatSbCalendarServices_FE.schedules[day], function (item) {
            return (item.s_id == '0' || item.s_id == 0 || item.s_id == s_id);
        });

        if (typeof schedule != 'undefined' && schedule.length > 0) {
            var list_time_slot = '',
                wh_start = 0,
                wh_end = 0,
                time_end = 0,
                is_available = 1,
                max_cap = 0,
                b_time = 0,
                b_end_time = 0,
                total_book = 0,
                time_step = parseInt(FatSbMain_FE.data.time_step),
                booking_in_day = _.where(FatSbCalendarServices_FE.bookings, {b_date: date_str});

            break_time = parseInt(break_time);


            for (let sc of schedule) {
                wh_start = parseInt(sc.work_start);
                wh_end = parseInt(sc.work_end);
                for (var $i = wh_start; $i < wh_end; $i = $i + time_step + break_time) {
                    is_available = 1;
                    max_cap = parseInt(FatSbCalendarServices_FE.s_max_cap);
                    time_end = parseInt($i) + parseInt(duration);
                    if ($i <= FatSbCalendarServices_FE.current_slot && date <= FatSbCalendarServices_FE.current_time) {
                        is_available = 0;
                    }

                    for(let b of booking_in_day){
                        b_time = parseInt(b.b_time);
                        b_end_time = b_time + parseInt(b.b_service_duration) + parseInt(b.b_service_break_time);
                        total_book = parseInt(b.total_number);

                        if(b.b_service_id != FatSbCalendarServices_FE.s_id.toString() && ( (b_time <= $i && $i <= b_end_time)  || (b_time <= time_end && time_end <=b_end_time ) ) ){
                            is_available = 0;
                            break;
                        }

                        if(b.b_service_id == FatSbCalendarServices_FE.s_id.toString() && b_time==$i){
                            max_cap = total_book < max_cap ? (max_cap - total_book) : 0;
                            is_available = max_cap > 0 ? 1 : 0;
                        }

                        if((b_time < $i && $i< b_end_time) || (b_time < time_end && time_end < b_end_time)){
                            is_available = 0;
                        }
                    }
                    if (time_end > wh_end) {
                        is_available = 0;
                    }

                    if (is_available) {
                        list_time_slot += '<li data-slot="' + $i + '" data-max-cap="' + max_cap +'"><span>' + FatSbMain_FE.data.slots[$i] + ' - ' + FatSbMain_FE.data.slots[time_end] + '</span></li>';
                    }

                }
            }
            if (list_time_slot == '') {
                $('ul', time_list_elm).append('<div class="fat-empty-time-slot">' + FatSbMain_FE.data.empty_time_slot + '</div>');
            } else {
                $('ul', time_list_elm).append(list_time_slot);
            }
        } else {
            $('ul', time_list_elm).append('<div class="fat-empty-time-slot">' + FatSbMain_FE.data.empty_time_slot + '</div>');
        }

        $('.fat-sb-time-list li', container).on('click', function () {
            FatSbCalendarServices_FE.timeSlotClick($(this), container);
        });

        time_slot_wrap.fadeIn();
        FatSbMain_FE.closeLoading(time_slot_wrap);


    };

    FatSbCalendarServices_FE.timeSlotClick = function (elm, container) {
        FatSbCalendarServices_FE.time_slot = elm.attr('data-slot');
        FatSbCalendarServices_FE.b_time_label = elm.html();

        $('.fat-sb-date-time-wrap', container).fadeOut(function () {
            $('.fat-sb-information', container).fadeIn();

            FatSbMain_FE.initNumberField(container);
            var number_person_elm = $('#number_of_person', container),
                max_cap = parseInt(elm.attr('data-max-cap'));
            number_person_elm.val(1);
            number_person_elm.attr('data-max', max_cap);

            $('.fat-sb-information button.fat-bt-next-information', container).on('click', function () {
                FatSbCalendarServices_FE.nextInformation($(this), container);
            })
        })
    };

    FatSbCalendarServices_FE.nextInformation = function (self, container) {
        var form = $('.fat-sb-information .ui.form', container),
            number_person_elm = $('#number_of_person', container);

        if (FatSbMain_FE.validateForm(form)) {
            $('.fat-sb-information', container).fadeOut(function () {
                var number_of_person = number_person_elm.val();

                $('.fat-sb-order-service .fat-sb-label span', container).html(FatSbCalendarServices_FE.s_name);
                $('.fat-sb-order-service .fat-sb-value', container).html(number_of_person + ' x ' + FatSbMain_FE.formatPrice(FatSbCalendarServices_FE.s_price));
                $('.fat-sb-order-date', container).html(FatSbCalendarServices_FE.date);
                $('.fat-sb-order-time', container).html(FatSbCalendarServices_FE.b_time_label);

                var s_extra = '';
                $('.fat-sb-order-service-extra', container).empty();
                $('.fat-sb-order-service-extra', container).show();

                FatSbCalendarServices_FE.initTotal(container);
                $('.fat-sb-order', container).fadeIn();

                $('.fat-bt-get-coupon', container).on('click', function () {
                    FatSbCalendarServices_FE.getCoupon($(this));
                });

                $('input#coupon', container).on('change', function () {
                    FatSbCalendarServices_FE.couponOnChange($(this));
                });

                $('.fat-sb-list-payment .payment-item').on('click', function () {
                    FatSbCalendarServices_FE.paymentClick($(this));
                });

                $('button.fat-bt-payment', container).on('click', function () {
                    FatSbCalendarServices_FE.confirmOrderClick($(this));
                });

            });
        }
    };

    FatSbCalendarServices_FE.initTotal = function (container) {
        var quantity = $('#number_of_person', container).val(),
            tax = 0, sub_total = 0, total = 0, price_label = '', discount = 0, price_base_quantity = 0, extra_price = 0,
            total_origin = 0,
            extra_tax = 0,
            total_days = 1;

        discount = $('#coupon', container).val() != '' ? $('.fat-sb-order-discount .fat-sb-value', container).attr('data-value') : 0;
        quantity = parseInt(quantity);

        if (FatSbCalendarServices_FE.s_price >= 0 && quantity > 0) {
            price_base_quantity = FatSbMain_FE.calculatePrice(quantity, FatSbCalendarServices_FE.s_price, FatSbCalendarServices_FE.s_id);

            tax = price_base_quantity * FatSbCalendarServices_FE.s_tax / 100;
            tax = tax + extra_tax;
            sub_total = price_base_quantity + (extra_price * quantity) + tax;
            sub_total = sub_total > 0 ? sub_total * total_days : 0;
            total_origin = sub_total;
            total = sub_total - discount;
            total = total > 0 ? total : 0;

            FatSbCalendarServices_FE.b_total = total;

            if (tax > 0) {
                $('.fat-sb-order-tax', container).show();
                $('.fat-sb-order-tax .fat-sb-value', container).text(FatSbMain_FE.formatPrice(tax));
            } else {
                $('.fat-sb-order-tax', container).hide();
            }
            $('.fat-sb-order-discount .fat-sb-value', container).text(FatSbMain_FE.formatPrice(discount));
            $('.fat-sb-order-subtotal .fat-sb-value', container).text(FatSbMain_FE.formatPrice(sub_total));

            $('.fat-sb-order-total', container).attr('data-total-origin', total_origin);
            $('.fat-sb-order-total', container).attr('data-value', total).text(FatSbMain_FE.formatPrice(total));

            $('.fat-sb-tab-content.order', container).attr('data-price', FatSbCalendarServices_FE.s_price).attr('data-total', total);

        }
    };

    FatSbCalendarServices_FE.getCoupon = function (self) {
        var container = self.closest('.fat-sb-calendar-layout'),
            coupon = $('#coupon', container).val(),
            s_id = FatSbCalendarServices_FE.s_id;
        if (s_id == '' || coupon == '') {
            var discount = 0;
            $('.fat-sb-order-discount .fat-sb-value', container).attr('data-value', discount);
            $('. fat-sb-order-discount .fat-sb-value', container).text(FatSbMain_FE.data.symbol_prefix + '0' + FatSbMain_FE.data.symbol_suffix);
            FatSbCalendarServices_FE.initTotal(container);
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
                    total = $('.fat-sb-order-total', container).attr('data-total-origin');
                total = parseFloat(total);
                self.removeClass('loading');

                if (response.result > 0) {
                    $('.fat-sb-coupon-wrap .fat-coupon-error', container).html('');
                    if (response.discount_type == 1) {
                        discount = (total * parseFloat(response.amount)) / 100;
                    } else {
                        discount = parseFloat(response.amount);
                    }
                } else {
                    $('.fat-sb-coupon-wrap .fat-coupon-error', container).html(response.message);
                }


                $('.fat-sb-order-discount .fat-sb-value', container).attr('data-value', discount);
                $('.fat-sb-order-discount .fat-sb-value', container).text(FatSbMain_FE.data.symbol_prefix + discount.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);

                FatSbCalendarServices_FE.initTotal(container);
            },
            error: function () {
                self.removeClass('loading');
            }
        })
    };

    FatSbCalendarServices_FE.couponOnChange = function (self) {
        if (self.val() == '') {
            var container = self.closest('.fat-sb-calendar-layout');
            $('.fat-coupon-error', container).html('');
            FatSbCalendarServices_FE.initTotal(container);
        }
    };

    FatSbCalendarServices_FE.paymentClick = function (self) {
        var container = self.closest('.fat-sb-calendar-layout');

        FatSbCalendarServices_FE.payment_method = self.attr('data-payment');
        $('.payment-item.active', container).removeClass('active');

        if (FatSbCalendarServices_FE.payment_method == 'stripe') {
            $('ul.fat-sb-list-payment', container).fadeOut(function () {
                $('.fat-sb-order-stripe', container).removeClass('fat-sb-hidden');
            });
        } else {
            $('.fat-sb-order-stripe', container).addClass('fat-sb-hidden');
        }
        self.addClass('active');
        $('.fat-sb-order .fat-sb-button-group button.ui.button', container).removeClass('disabled');
    };

    FatSbCalendarServices_FE.confirmOrderClick = function (self) {
        var container = self.closest('.fat-sb-calendar-layout'),
            form = $('.fat-sb-information .ui.form', container),
            number_of_person = $('#number_of_person', container).val(),
            coupon = $('#coupon', container).val(),
            c_first_name = $('#c_first_name', container).val(),
            c_last_name = $('#c_last_name', container).val(),
            c_email = $('#c_email', container).val(),
            c_phone = $('#c_phone', container).val(),
            c_phone_code = $('#phone_code', container).val(),
            note = $('#note', container).val(),
            services_extra = '',
            form_builder = {};

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

        if (FatSbCalendarServices_FE.payment_method == 'stripe' && FatSbCalendarServices_FE.b_total > 0) {
            $('form#stripe-payment-form button', container).trigger('click');
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
                            b_service_id: FatSbCalendarServices_FE.s_id,
                            b_services_extra: services_extra,
                            b_loc_id: FatSbCalendarServices_FE.loc_id,
                            b_employee_id: FatSbCalendarServices_FE.e_id,
                            b_date: FatSbCalendarServices_FE.date,
                            b_time: FatSbCalendarServices_FE.time_slot,
                            b_customer_number: number_of_person,
                            b_coupon_code: coupon,
                            b_gateway_type: FatSbCalendarServices_FE.payment_method,
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

                            if (typeof response.redirect_url != 'undefined' && response.redirect_url != '') {
                                window.location.href = response.redirect_url;
                                return;
                            }

                            if (FatSbCalendarServices_FE.payment_method == 'myPOS' && FatSbCalendarServices_FE.b > 0) {
                                var form = $(response.form);
                                form.hide();
                                $('body').append(form);
                                $('form#ipcForm').submit();
                                return;
                            }

                            if (FatSbCalendarServices_FE.payment_method == 'onsite' || FatSbCalendarServices_FE.payment_method == 'price-package'
                                || FatSbCalendarServices_FE.payment_method == 'paypal' || FatSbCalendarServices_FE.b_total == 0) {


                                $('.fat-sb-order', container).fadeOut(function () {
                                    $('.fat-sb-order-success', container).fadeIn();
                                });

                                $('.fat-bt-add-icalendar', container).attr('data-id', response.result);
                                $('.fat-bt-add-google-calendar', container).attr('data-id', response.result);

                                $('.fat-bt-add-icalendar', container).on('click', function () {
                                    FatSbCalendarServices_FE.addToICalendar($(this));
                                });

                                $('.fat-bt-add-google-calendar', container).on('click', function () {
                                    FatSbCalendarServices_FE.addToGoogleCalendar($(this));
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

                        } else {
                            FatSbMain_FE.removeLoading(container, self);
                            $('.fat-sb-order .fat-sb-error-message', container).html(response.message).removeClass('fat-sb-hidden');
                        }
                        FatSbMain_FE.registerOnClick($('.fat-sb-order-success.calendar'));
                    },
                    error: function (response) {
                        FatSbMain_FE.removeLoading(container, self);
                    }
                });
            } catch (err) {
            }
        }

    };

    FatSbCalendarServices_FE.addToICalendar = function (self) {
        var container = self.closest('.fat-sb-calendar-layout'),
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

    FatSbCalendarServices_FE.addToGoogleCalendar = function (self) {
        var container = self.closest('.fat-sb-calendar-layout'),
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

    FatSbCalendarServices_FE.bookAnotherCourse = function (self) {
        location.reload();
    };

    FatSbCalendarServices_FE.initStripeCardInput = function () {
        if ($('form#stripe-payment-form').length == 0) {
            return;
        }

        $('form#stripe-payment-form').each(function () {
            var stripe_form = $(this),
                booking_container = stripe_form.closest('.fat-booking-container'),
                pk = stripe_form.attr('data-pk'),
                card_element_id = $('.card-element', booking_container).attr('id'),
                card_errors_id = $('.card-errors', booking_container).attr('id');
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
            card.mount('#' + card_element_id);

            // Handle real-time validation errors from the card Element.
            card.addEventListener('change', function (event) {
                var displayError = document.getElementById(card_errors_id);
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Handle form submission.
            var form = stripe_form;// document.getElementById('stripe-payment-form');
            // form.addEventListener('submit', function (event) {
            form.on('submit', function (event) {
                event.preventDefault();

                var self = $('button.fat-bt-payment', booking_container),
                    container = self.closest('.fat-sb-order');

                FatSbMain_FE.addLoading(container, self);
                stripe.createToken(card).then(function (result) {

                    var self = $('button.fat-bt-payment', booking_container),
                        container = self.closest('.fat-sb-order');

                    if (result.error) {
                        // Inform the user if there was an error.
                        var errorElement = document.getElementById(card_errors_id);
                        errorElement.textContent = result.error.message;
                        FatSbMain_FE.removeLoading(container, self);
                    } else {
                        // Send the token to your server.
                        var self = $('button.fat-bt-payment', booking_container),
                            container = self.closest('.fat-booking-container'),
                            service_id = FatSbCalendarServices_FE.s_id,
                            services_extra = '',
                            employee_id = FatSbCalendarServices_FE.e_id,
                            loc_id = 0,
                            date = FatSbCalendarServices_FE.date,
                            time = FatSbCalendarServices_FE.time_slot,
                            number_of_person = $('#number_of_person', container).val(),
                            coupon = $('#coupon', container).val(),
                            payment_method = FatSbCalendarServices_FE.payment_method,
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

                                    $('.fat-sb-order', container).fadeOut(function () {
                                        $('.fat-sb-order-success', container).fadeIn();
                                    });

                                    $('.fat-bt-add-icalendar', container).attr('data-id', data.code);
                                    $('.fat-bt-add-google-calendar', container).attr('data-id', data.code);

                                    $('.fat-bt-add-icalendar', container).on('click', function () {
                                        FatSbCalendarServices_FE.addToICalendar($(this));
                                    });

                                    $('.fat-bt-add-google-calendar', container).on('click', function () {
                                        FatSbCalendarServices_FE.addToGoogleCalendar($(this));
                                    });

                                    $('.fat-bt-add-book-another', container).on('click', function () {
                                        location.reload();
                                    });

                                } else {
                                    FatSbMain_FE.removeLoading(container, self);
                                    var errorElement = document.getElementById(card_errors_id);
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
        })
    };

    $(document).ready(function () {
        FatSbCalendarServices_FE.init();
        FatSbMain_FE.initFormBuilder();
    })
})(jQuery);