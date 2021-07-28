"use strict";
var FatSbBookingServices_FE = {
    services_cat: [],
    services: [],
    services_work_day: [],
    location: [],
    employees: [],
    services_employee: [],
    services_extra: [],
    bod_field: null,
    e_service: {},
    e_id: 0,
    loc_id: 0,
    loc_name: '',
    s_id: 0,
    s_multiple_days: 0,
    s_min_multiple_slot: 1,
    s_max_multiple_slot: 1,
    s_duration: 0,
    s_break_time: 0,
    b_date: 0,
    b_time: 0,
    s_min_cap : 1,
    s_max_cap: 1,
    s_price: 0,
    hasSetActiveDate: false,
    bookings: [],
    multiple_days: []
};

(function ($) {

    FatSbBookingServices_FE.init = function () {
        FatSbBookingServices_FE.initField();
        FatSbBookingServices_FE.initStripeCardInput();
        FatSbBookingServices_FE.loadServiceDictionary();
    };

    FatSbBookingServices_FE.initField = function(){
        $('.fat-booking-container').each(function () {
            var container = $(this);

            //phone code
            $('.ui.dropdown',container).each(function () {
                var self = $(this);
                self.dropdown({
                    clearable: self.hasClass('clearable')
                });
            });

            //air datetime
            var date_format = FatSbBookingServices_FE.getDateFormat(),
                elmBookingDate = $('.air-date-picker', container),
                locale = elmBookingDate.attr('data-locale');
            locale = locale.split('_').length > 1 ? locale.split('_')[0] : locale;
            var option = {
                inline: true,
                language: locale,
                minDate: new Date(),
                dateFormat: date_format
            };
            FatSbBookingServices_FE.bod_field = elmBookingDate.datepicker(option).data('datepicker');

            container.addClass('has-init');
        })
    };

    FatSbBookingServices_FE.tabItemOnClick = function(self){
        var container = self.closest('.fat-sb-services-layout'),
            tab = self.attr('data-tab');
        if(self.hasClass('active') || self.hasClass('fat-disabled')){
            return;
        }

        $('.fat-sb-tab li.active', container).removeClass('active');
        $('.fat-sb-tab-content.active',container).fadeOut(function(){
            $(this).removeClass('active');
            self.addClass('active');
            $('.fat-sb-tab-content[data-tab="'+ tab +'"]',container).fadeIn(function(){
                $(this).addClass('active');
            })
        })
    };

    FatSbBookingServices_FE.serviceCatItemOnClick = function(self){
        var cat = self.attr('data-cat'),
            container = self.closest('.fat-booking-container'),
            cat_container = self.closest('.fat-sb-list-service-cat');

        if(!self.hasClass('active')){
            $('.fat-sb-service-item-inner.active',cat_container).removeClass('active');
            self.addClass('active');
            $('.fat-sb-list-services .fat-sb-service-item',container).addClass('fat-sb-deactive');
            $('.fat-sb-list-services .fat-sb-service-item-inner',container).removeClass('active');
            $('.fat-sb-list-services .fat-sb-service-item.' + cat,container).removeClass('fat-sb-deactive');
        }
    };

    FatSbBookingServices_FE.serviceItemOnClick = function(self){
        var id = self.attr('data-id'),
            duration = 0,
            break_time = 0,
            container = self.closest('.fat-sb-services-layout'),
            $service = _.find(FatSbBookingServices_FE.services,{s_id: id});

        if(container.height()> 500){
            var top = container.offset().top - 100;
            $('html, body').animate({scrollTop: top}, 500);
        }
        duration =  typeof $service.s_duration !='undefined' && !isNaN($service.s_duration) ? parseInt($service.s_duration) : 0;
        break_time = typeof $service.s_break_time!='undefined' && !isNaN($service.s_break_time) ? parseInt($service.s_break_time) : 0;

        if(FatSbBookingServices_FE.s_id!=id){
            FatSbBookingServices_FE.multiple_days = [];
        }

        FatSbBookingServices_FE.s_id = id;
        FatSbBookingServices_FE.e_id = 0;
        FatSbBookingServices_FE.loc_id = 0;
        FatSbBookingServices_FE.b_date = 0;
        FatSbBookingServices_FE.b_time = 0;
        FatSbBookingServices_FE.s_price = 0;
        FatSbBookingServices_FE.s_max_cap = 1;
        FatSbBookingServices_FE.s_min_cap = 1;

        FatSbBookingServices_FE.s_duration = duration;
        FatSbBookingServices_FE.s_break_time = break_time;
        FatSbBookingServices_FE.s_multiple_days = parseInt($service.s_multiple_days);
        FatSbBookingServices_FE.s_max_multiple_slot = parseInt($service.s_max_multiple_slot);
        FatSbBookingServices_FE.s_min_multiple_slot = parseInt($service.s_min_multiple_slot);

        FatSbBookingServices_FE.addLimitNotice(container);
        // add class mutiple days to container
        if(FatSbBookingServices_FE.s_multiple_days == 1){
            container.addClass('multiple-days');
        }else{
            container.removeClass('multiple-days');
            $('.fat-sb-tab-content.time .fat-sb-button-groups', container).hide();
        }

        if(!self.hasClass('active')){
            $('.fat-sb-list-services .fat-sb-service-item-inner.active',container).removeClass('active');
            self.addClass('active');
            $('.fat-sb-list-employees .fat-sb-employee-item:not(.fat-sb-hidden)',container).addClass('fat-sb-hidden');
            $('.fat-sb-list-employees .fat-sb-employee-item.ser_' + id,container).removeClass('fat-sb-hidden');
            $('.fat-sb-list-employees .fat-sb-employee-item-inner.active',container).removeClass('active');
        }

        //display price and capacity
        var services_employee = _.where(FatSbBookingServices_FE.services_employee,{s_id: id}),
            employee_item = '',
            price = 0;

        for(var $se_index = 0; $se_index < services_employee.length; $se_index++){
            employee_item = $('.fat-sb-list-employees .fat-sb-employee-item.ser_' + services_employee[$se_index].s_id + ' .fat-sb-employee-item-inner[data-id="' + services_employee[$se_index].e_id + '"]');
            price = parseFloat(services_employee[$se_index].s_price);

            $('.price', employee_item).attr('data-value',price);
            $('.price span.value', employee_item).text( FatSbMain_FE.data.symbol_prefix + price.format(FatSbMain_FE.data.number_of_decimals,3,',') + FatSbMain_FE.data.symbol_suffix);
            $('.capacity', employee_item).attr('data-max',services_employee[$se_index].s_max_cap);
            $('.capacity', employee_item).attr('data-min',services_employee[$se_index].s_min_cap);
            $('.capacity span.value', employee_item).text(services_employee[$se_index].s_min_cap + ' - ' + services_employee[$se_index].s_max_cap);

        }

        //reset service provider and time tab
        var elm_data_selected = $('.fat-sb-data-selected',container);
        $('.data-item:not(.fat-sb-date-time-item)',elm_data_selected).remove();
        if (FatSbBookingServices_FE.bod_field != null) {
            FatSbBookingServices_FE.bod_field.clear();
        }
        $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).addClass('disabled').removeClass('active');

        //reset tab
        $('li[data-tab="time"]:not(.fat-disabled)',container).addClass('fat-disabled');
        $('li[data-tab="customer"]:not(.fat-disabled)',container).addClass('fat-disabled');

        if(container.hasClass('hide-employee')){
            $('.fat-sb-list-employees .fat-sb-employee-item:first-child .fat-sb-employee-item-inner', container).trigger('click');
            return;
        }
        if(container.hasClass('services-no-tab')){
            FatSbBookingServices_FE.nextTabContent(self, container);
        }else{
            $('.fat-sb-tab li[data-tab="services-provider"]', container).removeClass('fat-disabled');
            $('.fat-sb-tab li[data-tab="services-provider"]', container).trigger('click');
        }

        if(container.height()> 500){
            var top = container.offset().top-50;
            $('html, body').animate({scrollTop: container}, 500);
        }
    };

    FatSbBookingServices_FE.locationItemOnClick = function(self){
        var loc = self.attr('data-loc'),
            loc_id = self.attr('data-id'),
            container = self.closest('.fat-booking-container'),
            loc_container = self.closest('.fat-sb-list-locations');

        FatSbBookingServices_FE.loc_id = loc_id;
        if(!self.hasClass('active')){
            $('.fat-sb-list-item-inner.active',loc_container).removeClass('active');
            self.addClass('active');
            $('.fat-sb-list-employees .fat-sb-employee-item',container).addClass('fat-sb-deactive');
            $('.fat-sb-list-employees .fat-sb-employee-item-inner',container).removeClass('active');
            $('.fat-sb-list-employees .fat-sb-employee-item.' + loc,container).removeClass('fat-sb-deactive');
        }
    };

    FatSbBookingServices_FE.providerItemOnClick = function(self){
        var id = self.attr('data-id'),
            container = self.closest('.fat-sb-services-layout'),
            location_info = '';

        FatSbBookingServices_FE.e_id = id;
        FatSbBookingServices_FE.b_date = 0;
        FatSbBookingServices_FE.b_time = 0;
        FatSbBookingServices_FE.s_price = $('.price', self).attr('data-value');
        FatSbBookingServices_FE.s_max_cap = $('.capacity', self).attr('data-max');
        FatSbBookingServices_FE.s_min_cap = $('.capacity', self).attr('data-min');

        if(FatSbBookingServices_FE.loc_id <= 0){
            FatSbBookingServices_FE.loc_id = $('.fat-sb-list-locations .fat-sb-list-item-inner.active').length > 0 ? $('.fat-sb-list-locations .fat-sb-list-item-inner.active').attr('data-id') : $('.fat-sb-list-locations .fat-sb-list-item:first-child .fat-sb-list-item-inner').attr('data-id');
            if(typeof FatSbBookingServices_FE.loc_id=='undefined'){
                var emp_loc_ids = self.attr('data-loc-id').split(',');
                for(var $i=0; $i<emp_loc_ids.length; $i++){
                    location_info = _.findWhere(FatSbBookingServices_FE.location, {loc_id: emp_loc_ids[$i]});
                    if(typeof location_info != 'undefined'){
                        FatSbBookingServices_FE.loc_id = emp_loc_ids[$i];
                        break;
                    }
                }
            }
        }
        location_info = _.findWhere(FatSbBookingServices_FE.location, {loc_id: FatSbBookingServices_FE.loc_id});
        FatSbBookingServices_FE.loc_name = typeof location_info!='undefined' ? location_info.loc_name : '';

        if(!self.hasClass('active')){
            $('.fat-sb-list-employees .fat-sb-employee-item-inner.active',container).removeClass('active');
            self.addClass('active');
        }

        var service_item = $('.fat-sb-tab-content.services .fat-sb-list-services .fat-sb-service-item-inner.active', container).closest('.fat-sb-service-item').clone(false),
            service_provider = self.closest('.fat-sb-employee-item').clone(false),
            elm_data_selected = $('.fat-sb-data-selected',container);

        $('.data-item:not(.fat-sb-date-time-item)',elm_data_selected).remove();
        service_item.removeClass('active').addClass('data-item');
        service_provider.removeClass('active').addClass('data-item');
        $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).addClass('disabled').removeClass('active');
        $('.fat-sb-item-content .location .value',service_provider).text(FatSbBookingServices_FE.loc_name);

        elm_data_selected.prepend(service_provider);
        elm_data_selected.prepend(service_item);
        FatSbBookingServices_FE.initSlot($('.fat-sb-tab-content.time',container));

        //reset tab
        $('li[data-tab="customer"]:not(.fat-disabled)',container).addClass('fat-disabled');

        if(container.hasClass('services-no-tab')){
            FatSbBookingServices_FE.nextTabContent(self, container);
        }else{
            $('.fat-sb-tab li[data-tab="time"]', container).removeClass('fat-disabled');
            $('.fat-sb-tab li[data-tab="time"]', container).trigger('click');
        }
    };

    FatSbBookingServices_FE.timeItemOnClick = function(self){
        var container = self.closest('.fat-sb-services-layout'),
            date =  $('#b_date',container).attr('data-date'),
            date_i18n = $('#b_date',container).attr('data-date-i18n'),
            time = self.attr('data-value'),
            time_label = $('.time-label',self).html(),
            available = self.attr('data-max-cap-available'),
            max_cap = 0,
            selected_day = _.find(FatSbBookingServices_FE.multiple_days, function(day){
                return (day.date == date && day.time==time);
            });
        FatSbBookingServices_FE.b_date = date;
        FatSbBookingServices_FE.b_time = time;
        $('.fat-sb-booking-time-wrap .item .time-label.active', container).removeClass('active');
        $('.time-label',self).addClass('active');

        $('.fat-sb-tab-content.customer .date-title',container).text(date_i18n);
        $('.fat-sb-tab-content.customer .time-title',container).text($('.time-label',self).text());


        if(FatSbBookingServices_FE.s_multiple_days==0){
            FatSbBookingServices_FE.multiple_days = [];
        }

        if(typeof selected_day=='undefined'){
            FatSbBookingServices_FE.multiple_days.push({date: date, date_i18n: date_i18n, time: time, time_label: time_label, available: available});
        }else{
            return;
        }

        if(FatSbBookingServices_FE.s_multiple_days==1){
            FatSbBookingServices_FE.addMultipleDays(container, date, time, date_i18n, time_label);
        }else{
            if(container.hasClass('services-no-tab')){
                FatSbBookingServices_FE.nextTabContent(self, container);
            }else{
                $('.fat-sb-tab li[data-tab="customer"]', container).removeClass('fat-disabled');
                $('.fat-sb-tab li[data-tab="customer"]', container).trigger('click');
            }
        }

        // init customer number dropdown
        FatSbBookingServices_FE.setMaxQuantity(container);

        //init list service extra
        var $service = _.find(FatSbBookingServices_FE.services,{s_id:FatSbBookingServices_FE.s_id}),
            elm_extra = $('.fat-sb-customer-wrap .fat-sb-order-extra-service',container);
        elm_extra.addClass('fat-sb-hidden');
        $('span.fat-item-value ul li',elm_extra).remove();

        if(typeof $service.s_extra_ids !='undefined' && $service.s_extra_ids!=''){
            var $s_extra_ids = $service.s_extra_ids.split(','),
                $service_extra = _.filter(FatSbBookingServices_FE.services_extra,function(item){
                    return $s_extra_ids.indexOf(item.se_id) > -1 ? true : false;
                });
            if($service_extra!=null && $service_extra.length > 0){

                var $is_se_available = 1,
                    end_limit_time = 0;
                for(var $i=0; $i < $service_extra.length; $i++){
                    $is_se_available = 1;
                    end_limit_time = 0;
                    //validate disable multiple book
                    if($service_extra[$i].se_multiple_book=='0'){
                        for(var $b_index=0; $b_index< FatSbBookingServices_FE.bookings.length; $b_index++){
                            end_limit_time = FatSbBookingServices_FE.bookings[$b_index].b_time + FatSbBookingServices_FE.bookings[$b_index].b_service_duration;
                            end_limit_time += FatSbBookingServices_FE.bookings[$b_index].b_service_break_time + $service_extra[$i].se_duration;

                            if(FatSbBookingServices_FE.bookings[$b_index].b_services_extra == $service_extra[$i].se_id
                            && FatSbBookingServices_FE.bookings[$b_index].b_date == FatSbBookingServices_FE.b_date
                                && (FatSbBookingServices_FE.bookings[$b_index].b_time <= FatSbBookingServices_FE.b_time && FatSbBookingServices_FE.b_time <= end_limit_time )
                            ){
                                $is_se_available = 0;
                                break;
                            }
                        }
                    }
                    if($is_se_available){
                        elm_extra.removeClass('fat-sb-hidden');
                        $('ul',elm_extra).append('<li><input type="checkbox" data-onChange="FatSbBookingServices_FE.onChangeExtraService" value="' + $service_extra[$i].se_id +'" class="fat-sb-service-extra-item">'
                            + $service_extra[$i].se_name
                            + '<span class="fat-se-price"> (' + FatSbMain_FE.data.symbol_prefix + parseFloat($service_extra[$i].se_price).format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix + ')</span>'
                            + '</li>');
                    }

                }
                FatSbMain_FE.registerOnChange($('.fat-sb-order-extra-service'));
            }
        }

        FatSbBookingServices_FE.initPayment(container);

        //init remain time slot
        FatSbBookingServices_FE.initRemainSlot(max_cap, container);
        $('select.fat-sb-number-of-person-wrap', container).on('change',function(){
            FatSbBookingServices_FE.initRemainSlot(max_cap, container);
        });

    };

    FatSbBookingServices_FE.addMultipleDays = function(container, date, time, date_i18n, time_label){
        $('.fat-sb-multiple-days ul.list-multiple-days .notice').remove();
        $('.fat-sb-multiple-days ul.list-multiple-days', container).append('<li data-date="' + date +'" data-time="'+ time +'" class="">' + date_i18n + ' ' + time_label  + '<a href="javascript:;" class="remove-day"><i class="trash alternate outline icon"></i></a></li>');

        if(FatSbBookingServices_FE.s_min_multiple_slot <= FatSbBookingServices_FE.multiple_days.length){
            $('.fat-sb-tab-content.time .fat-sb-button-groups button', container).removeClass('disabled');
            $('.fat-sb-multiple-days .notice', container).remove();
        }else{
            $('.fat-sb-tab-content.time .fat-sb-button-groups button', container).addClass('disabled');
        }

        //remove day
        $('.fat-sb-multiple-days a.remove-day', container).off('click').on('click',function(){
            var self = $(this),
                li = self.closest('li'),
                item_date = li.attr('data-date'),
                item_time = li.attr('data-time');

            FatSbBookingServices_FE.multiple_days = _.reject(FatSbBookingServices_FE.multiple_days, function(day){
                return (day.date == item_date && day.time==item_time);
            });
            li.remove();

            if(FatSbBookingServices_FE.s_min_multiple_slot <= FatSbBookingServices_FE.multiple_days.length){
                $('.fat-sb-tab-content.time .fat-sb-button-groups button', container).removeClass('disabled');
                $('.fat-sb-multiple-days .notice', container).remove();
            }else{
                $('.fat-sb-tab-content.time .fat-sb-button-groups button', container).addClass('disabled');
            }

            if(FatSbBookingServices_FE.multiple_days.length==0){
                FatSbBookingServices_FE.addLimitNotice(container);
            }
            FatSbBookingServices_FE.setMaxQuantity(container);
        })
    };

    FatSbBookingServices_FE.setMaxQuantity = function(container){
        var max_cap = 0;
        for(let day of FatSbBookingServices_FE.multiple_days){
            max_cap = (max_cap > day.available || max_cap==0) ? day.available : max_cap;
        }

        var min_cap = parseInt(FatSbBookingServices_FE.e_service.s_min_cap),
            elm_customer_number = $('select.fat-sb-number-of-person-wrap', container);
        $('#number_of_person', container).val(min_cap);
        $('option', elm_customer_number).remove();
        for (var $n_index = min_cap; $n_index <= max_cap; $n_index++) {
            elm_customer_number.append('<option value="' + $n_index + '">' + $n_index + '</option>');
        }
    };

    FatSbBookingServices_FE.initRemainSlot = function(max_cap, container){
        var total_slot = $('.fat-sb-booking-time-wrap .item.time-slot', container).length,
            total_deactive  = $('.fat-sb-booking-time-wrap .item:not(.time-slot).show-deactive', container).length,
            remain_slot = $('.fat-sb-booking-time-wrap .item.time-slot:not(.disabled)', container).length,
            number_of_person =  $('select.fat-sb-number-of-person-wrap', container).val();
        remain_slot = max_cap > number_of_person ? remain_slot : (remain_slot - 1);

        total_slot += total_deactive;
        $('.fat-sb-remain-slot .fat-item-value', container).html(remain_slot + '/' + total_slot);
    };

    FatSbBookingServices_FE.nextToOrderDetail = function(self){
        var container =  self.closest('.fat-sb-services-layout'),
            elm_multiple_dates = $('.fat-sb-order-multiple-dates .fat-item-value',container);

        $(elm_multiple_dates).empty();
        for(let day of FatSbBookingServices_FE.multiple_days){
            elm_multiple_dates.append('<div>'+ day.date_i18n + ' ' + day.time_label + '</div>');
        }

        if(container.hasClass('services-no-tab')){
            FatSbBookingServices_FE.nextTabContent(self, container);
        }else{
            $('.fat-sb-tab li[data-tab="customer"]', container).removeClass('fat-disabled');
            $('.fat-sb-tab li[data-tab="customer"]', container).trigger('click');
        }
    };

    FatSbBookingServices_FE.onChangeExtraService = function(self){
        var container = self.closest('.fat-booking-container');
        FatSbBookingServices_FE.initPayment(container);
    };

    FatSbBookingServices_FE.paymentOnChange = function(self){
        var container = self.closest('.fat-sb-services-layout'),
            payment_method = self.val();
        if (payment_method === 'stripe') {
            $('.fat-sb-order-stripe', container).show();
        } else {
            $('.fat-sb-order-stripe', container).hide();
        }
    };

    FatSbBookingServices_FE.numberPersonOnChange = function(self){
        var container = self.closest('.fat-sb-services-layout');
        FatSbBookingServices_FE.initPayment(container);
    };

    FatSbBookingServices_FE.loadServiceDictionary = function () {
        var tabContent = $('.fat-sb-tab-content-wrap'),
            sc_container = tabContent.closest('.fat-booking-container'),
            show_category = sc_container.attr('data-show-category'),
            cat_id = sc_container.attr('data-category'),
            show_location = sc_container.attr('data-show-location');

        FatSbMain_FE.showLoading(tabContent);

        $.ajax({
            url: FatSbMain_FE.data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services_dictionary',
                s_field: FatSbMain_FE.data.ajax_s_field,
                cat_id: cat_id,
                layout: 'services'
            }),
            success: function (response) {
                response = $.parseJSON(response);
                if(typeof response=='undefined' || response==null ){
                    return;
                }

                var service_cat_template = wp.template('fat-sb-service-cat-item-template'),
                    service_template = wp.template('fat-sb-service-item-template'),
                    location_template = wp.template('fat-sb-location-item-template'),
                    employee_template = wp.template('fat-sb-employee-item-template');

                var e_loc_ids = '';
                for(var $e_index = 0; $e_index< response.employee.length; $e_index++){
                    e_loc_ids = response.employee[$e_index].e_location_ids.split(',');

                    response.employee[$e_index].e_location_class = 'loc_' + response.employee[$e_index].e_location_ids.replace(/,/g, ' loc_');
                    response.employee[$e_index].e_location = '';

                    for(var $loc_index=0; $loc_index < response.location.length; $loc_index++){
                        if( e_loc_ids.indexOf(response.location[$loc_index].loc_id) > -1){
                            if(typeof response.employee[$e_index] !='undefined'){
                                response.employee[$e_index].e_location += response.employee[$e_index].e_location!='' ? ', ' + response.location[$loc_index].loc_name : response.location[$loc_index].loc_name;
                            }
                        }
                    }

                    for(var $se_index=0; $se_index < response.services_employee.length; $se_index++){
                        response.employee[$e_index].e_service_class = typeof response.employee[$e_index].e_service_class =='undefined' ? '': response.employee[$e_index].e_service_class ;
                        if(response.services_employee[$se_index].e_id == response.employee[$e_index].e_id){
                            if(typeof response.employee[$e_index] !='undefined'){
                                response.employee[$e_index].e_service_class += ' ser_' + response.services_employee[$se_index].s_id;
                            }
                        }
                    }
                }

                if(show_location ==1){
                    $('.fat-sb-tab-content[data-tab="services-provider"] .fat-sb-list-locations').append($(location_template(response.location)));
                }
                $('.fat-sb-tab-content[data-tab="services-provider"] .fat-sb-list-employees').append($(employee_template(response.employee)));

                if(show_category ==1){
                    $('.fat-sb-tab-content[data-tab="services"] .fat-sb-list-service-cat').append($(service_cat_template(response.services_cat)));
                }
                $('.fat-sb-tab-content[data-tab="services"] .fat-sb-list-services').append($(service_template(response.services)));

                $('.fat-sb-list-services .fat-sb-service-item-inner',tabContent).each(function(){
                    var self = $(this),
                        s_id = self.attr('data-id'),
                        total_employee = $('.fat-sb-employee-item.ser_' + s_id,tabContent).length;
                        $('.total-label',self).text(total_employee);
                });

                FatSbMain_FE.registerEventProcess($('.fat-sb-services-layout'));

                FatSbBookingServices_FE.services_cat = response.services_cat;
                FatSbBookingServices_FE.services = response.services;
                FatSbBookingServices_FE.services_work_day = response.services_work_day;
                FatSbBookingServices_FE.services_extra = response.services_extra;
                FatSbBookingServices_FE.location = response.location;
                FatSbBookingServices_FE.employees = response.employee;
                FatSbBookingServices_FE.services_employee = response.services_employee;

                //process for one service layout
                if(sc_container.hasClass('fat-sb-one-service')){
                    var service = sc_container.attr('data-service'),
                        itemInner = $('.fat-sb-one-service .fat-sb-tab-content[data-tab="services"] .fat-sb-list-services .fat-sb-service-item:first-child .fat-sb-service-item-inner');
                    if(typeof service !='undefined' && service!=''){
                        itemInner = $('.fat-sb-one-service .fat-sb-tab-content[data-tab="services"] .fat-sb-list-services .fat-sb-service-item .fat-sb-service-item-inner[data-id="' + service + '"]');
                    }
                    if(itemInner.length>0){
                        FatSbBookingServices_FE.serviceItemOnClick(itemInner);
                    }
                }

                FatSbMain_FE.closeLoading(tabContent);
            },
            error: function () {

            }
        })
    };

    FatSbBookingServices_FE.initSlot = function (container) {
        var date_wrap = $('.fat-sb-booking-date-wrap', container),
            time_wrap = $('.fat-sb-booking-time-wrap', container);

        if (FatSbBookingServices_FE.bod_field != null) {
            FatSbBookingServices_FE.bod_field.clear();
        }
        FatSbMain_FE.showLoading(container);

        $('.item',time_wrap).each(function(){
            var label = $(this).attr('data-label'),
                time = $(this).attr('data-value'),
                end_time = FatSbBookingServices_FE.s_duration + parseInt(time),
                hour = Math.floor(end_time/60),
                minute = end_time%60,
                end_time_label = '',
                suffix = '';
            if(typeof FatSbMain_FE.data.time_format !='undefined' && FatSbMain_FE.data.time_format=='12h'){
                if(hour > 12){
                    suffix = ' pm';
                    hour = hour - 12;
                }else{
                    suffix = ' am';
                }
            }
            hour = hour >= 10 ? hour : ('0' + hour);
            minute = minute >= 10 ? minute : ('0' + minute);
            end_time_label = hour + ':' + minute + suffix;

            if(typeof end_time_label!='undefined'){
                $('.time-label',this).text(label + ' - ' + end_time_label);
            }
        });

        try {
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'GET',
                data: ({
                    action: 'fat_sb_get_booking_slot_fe',
                    s_id: FatSbBookingServices_FE.s_id,
                    e_id: FatSbBookingServices_FE.e_id,
                    loc_id: FatSbBookingServices_FE.loc_id,
                    s_field: FatSbMain_FE.data.ajax_s_field
                }),
                success: function (response) {
                    response = $.parseJSON(response);

                    if (response.result > 0 && typeof response.employee != 'undefined' && response.employee != null) {
                        var bookings = typeof response.bookings != 'undefined' && response.bookings != 'null' ? response.bookings : [],
                            e_day_off = typeof response.employee.e_day_off != 'undefined' && response.employee.e_day_off != 'null' ? response.employee.e_day_off : [],
                            e_break_times = typeof response.employee.e_break_times != 'undefined' && response.employee.e_break_times != 'null' ? response.employee.e_break_times : [],
                            e_schedules = typeof response.employee.e_schedules != 'undefined' && response.employee.e_schedules != 'null' ? response.employee.e_schedules : [],
                            e_services = typeof response.employee.e_services != 'undefined' && response.employee.e_services != 'null' ? response.employee.e_services : {},
                            dof_start = '',
                            dof_end = '';

                        FatSbBookingServices_FE.e_service = _.findWhere(e_services, {s_id: FatSbBookingServices_FE.s_id});
                        FatSbBookingServices_FE.bookings = bookings;

                        var $default_date = '';
                        var $service_work_day = _.where(FatSbBookingServices_FE.services_work_day,{s_id: FatSbBookingServices_FE.s_id});
                        if($service_work_day.length > 0){
                            var from_date = '',
                                now = new Date();
                            for(var $swd_index = 0; $swd_index < $service_work_day.length; $swd_index ++){
                                from_date = moment($service_work_day[$swd_index].from_date + ' 00:00:00');
                                from_date = new Date(from_date.year(), from_date.month(), from_date.date(), 0, 0, 0);
                                if(from_date < now){
                                    from_date = now;
                                }
                                if($default_date=='' || $default_date > from_date){
                                    $default_date = from_date;
                                }
                            }
                        }else{
                            $default_date = new Date();
                        }

                        $('.air-date-picker', date_wrap).datepicker({
                            onRenderCell: function (date, cellType) {
                                if (cellType == 'day') {

                                    //check service working day
                                    var $service_work_day = _.where(FatSbBookingServices_FE.services_work_day,{s_id: FatSbBookingServices_FE.s_id});

                                    if($service_work_day.length > 0){
                                        var from_date = '',
                                            to_date = '',
                                            cell_status = {
                                                classes: 'fat-slot-not-free',
                                                disabled: true
                                            };

                                        for(var $swd_index = 0; $swd_index < $service_work_day.length; $swd_index ++){
                                            from_date = moment($service_work_day[$swd_index].from_date);
                                            to_date =  moment($service_work_day[$swd_index].to_date);

                                            from_date = new Date(from_date.year(), from_date.month(), from_date.date(), 0, 0, 0);
                                            to_date = new Date(to_date.year(), to_date.month(), to_date.date(), 0, 0, 0);

                                            if (date >= from_date && date <= to_date) {
                                                cell_status = {
                                                    classes: 'fat-slot-free',
                                                    disabled: false
                                                }
                                            }
                                            return cell_status;
                                        }
                                    }

                                    var $es_day = FatSbBookingServices_FE.getESDay(date),
                                        cell_result = '';
                                    for (var $dof_index = 0; $dof_index < e_day_off.length; $dof_index++) {
                                        if (e_day_off[$dof_index].dof_start != '' && e_day_off[$dof_index].dof_end != '') {
                                            dof_start = moment(e_day_off[$dof_index].dof_start);
                                            dof_end =  moment(e_day_off[$dof_index].dof_end);

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
                                if (typeof date == 'undefined' || date == '') {
                                    return;
                                }

                                var month = date.getMonth() + 1,
                                    day = date.getDate(),
                                    selected_date_value = '',
                                    now = FatSbMain_FE.parseDateTime(FatSbMain_FE.data.now),
                                    now_minute = now.getHours()*60 + now.getMinutes();

                                month = parseInt(month);
                                day = parseInt(day);
                                month = month < 10 ? ('0' + month) : month;
                                day = day < 10 ? ('0' + day) : day;

                                setTimeout(function(){
                                    var elm_default_date = $('.datepicker--cell[data-date="' + date.getDate() + '"][data-month="'+ date.getMonth() +'"][data-year="' + date.getFullYear() + '"]');
                                    if( !FatSbBookingServices_FE.hasSetActiveDate && (elm_default_date.hasClass('fat-slot-not-free') || elm_default_date.hasClass('-disabled-') )){
                                        FatSbBookingServices_FE.setActiveDate(date, date_wrap, 1);
                                    }
                                },500);

                                selected_date_value = date.getFullYear() + '-' + month + '-' + day;
                                $('#b_date', container).attr('data-date', selected_date_value);
                                $('#b_date', container).attr('data-date-i18n', formattedDate);

                                $('.fat-sb-booking-time-wrap .item .time-label.active', container).removeClass('active');
                                $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).addClass('disabled');
                                $('.fat-sb-booking-time-wrap .fat-empty-time-slot', container).remove();

                                if (typeof date == 'undefined' || date == '' || $('#employee', container).val() == '') {
                                    return;
                                }

                                //check service working day
                                var $service_work_day = _.where(FatSbBookingServices_FE.services_work_day,{s_id: FatSbBookingServices_FE.s_id});
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
                                        $('.fat-sb-booking-time-wrap', container).append('<div class="fat-empty-time-slot">' + FatSbMain_FE.data.empty_time_slot + '</div>');
                                        return;
                                    }
                                }

                                var $es_day = FatSbBookingServices_FE.getESDay(date),
                                    time = 0,
                                    self = '',
                                    work_hours = [],
                                    service = _.findWhere(FatSbBookingServices_FE.services, {s_id: FatSbBookingServices_FE.s_id}),
                                    duration = !isNaN(service.s_duration) ? parseInt(service.s_duration) : 0,
                                    s_break_time = !isNaN(service.s_break_time) ? parseInt(service.s_break_time) : 0,
                                    extra_ids = '',
                                    break_times = _.where(e_break_times, {es_day: String($es_day)});


                                if(extra_ids!=''){
                                    extra_ids = extra_ids.split(',');
                                    var extra_info = '';
                                    for(var $ex_index=0; $ex_index < extra_ids.length; $ex_index++){
                                        extra_info = _.findWhere(FatSbBookingServices_FE.services_extra, {se_id: extra_ids[$ex_index]});
                                        if(typeof extra_info!='undefined' && typeof extra_info.se_duration!='undefined'){
                                            duration += parseInt(extra_info.se_duration);
                                        }
                                    }
                                }

                                if (FatSbBookingServices_FE.s_id != '') {
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

                                                if(typeof work_hours !='undefined'){
                                                    for (var $wk_index = 0; $wk_index < work_hours.length; $wk_index++) {
                                                        if ( (time + duration +  s_break_time) < 1440 && work_hours[$wk_index].s_id.indexOf(FatSbBookingServices_FE.s_id) >= 0 &&
                                                            parseInt(work_hours[$wk_index].es_work_hour_start) <= time && (time + duration +  s_break_time ) <= parseInt(work_hours[$wk_index].es_work_hour_end)) {
                                                            self.removeClass('disabled').removeClass('over-day').addClass('time-slot');
                                                        }
                                                        if((time + duration ) >= 1440){
                                                            self.addClass('over-day');
                                                        }
                                                    }
                                                }
                                                if (typeof break_times != 'undefined') {
                                                    for (var $b_index = 0; $b_index < break_times.length; $b_index++) {
                                                        es_break_time_start = parseInt(break_times[$b_index].es_break_time_start);
                                                        es_break_time_end = parseInt(break_times[$b_index].es_break_time_end);

                                                        if ( (time >= es_break_time_start && time < es_break_time_end) ||
                                                            ( (time + duration +  s_break_time) >= es_break_time_start && (time + duration + s_break_time ) <= es_break_time_end) ){
                                                            self.addClass('disabled');
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        //disable time slot that has passed in the current day
                                        if(FatSbMain_FE.equalDay(now, date) && time < now_minute){
                                            self.addClass('disabled');
                                        }

                                        //default max cap
                                        self.attr('data-max-cap-available', FatSbBookingServices_FE.e_service.s_max_cap);
                                    });

                                    //check base on booking
                                    var booking_in_day = _.where(bookings, {b_date: selected_date_value}),
                                        booking_service_in_day = _.where(bookings, {b_date: selected_date_value, b_service_id: FatSbBookingServices_FE.s_id.toString(), b_loc_id: FatSbBookingServices_FE.loc_id.toString()});

                                    if (typeof booking_in_day != 'undefined') {
                                        var b_time = 0,
                                            b_end_time = 0,
                                            b_service_id = 0,
                                            b_loc_id = 0,
                                            time = 0,
                                            end_time = 0,
                                            self,
                                            min_cap =  parseInt(FatSbBookingServices_FE.e_service.s_min_cap),
                                            max_cap =  parseInt(FatSbBookingServices_FE.e_service.s_max_cap),
                                            total_customer = 0,
                                            b_customer_number = 0;

                                        // check for booking this service
                                        $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).each(function () {
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
                                        $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).each(function () {
                                            self = $(this);
                                            time = parseInt(self.attr('data-value'));
                                            end_time = time + parseInt(FatSbBookingServices_FE.s_duration) + s_break_time;

                                            /** check duplicate time with another service */
                                            for (var $bs_index = 0; $bs_index < booking_in_day.length; $bs_index++) {
                                                b_time = parseInt(booking_in_day[$bs_index].b_time);
                                                b_end_time = b_time + parseInt(booking_in_day[$bs_index].b_service_duration) +  parseInt(booking_in_day[$bs_index].b_service_break_time);
                                                b_service_id = parseInt(booking_in_day[$bs_index].b_service_id);
                                                b_loc_id = parseInt(booking_in_day[$bs_index].b_loc_id);

                                                if(b_time == time && end_time == b_end_time && b_service_id == FatSbBookingServices_FE.s_id && b_loc_id == FatSbBookingServices_FE.loc_id){
                                                    $is_conflict = false;
                                                }else{
                                                    $is_conflict = !(end_time <= b_time || time >= b_end_time);
                                                }
                                                if($is_conflict){
                                                    self.addClass('disabled');
                                                }
                                            }
                                        });
                                    }

                                    if ($('.fat-sb-booking-time-wrap .item:not(.disabled)', container).length == 0) {
                                        $('.fat-sb-booking-time-wrap', container).append('<div class="fat-empty-time-slot">' + FatSbMain_FE.data.empty_time_slot + '</div>');
                                    }

                                    /* check show/hide time deactive */
                                    FatSbMain_FE.show_deactive_slot($es_day, e_schedules);
                                }

                                inst.hide();
                            }
                        });

                        FatSbBookingServices_FE.setActiveDate($default_date, date_wrap);
                    }
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
            FatSbMain_FE.closeLoading(container);
        }

    };

    FatSbBookingServices_FE.setActiveDate = function($default_date, date_wrap, is_return_set_active){
        var default_day = $default_date.getDate(),
            default_month = $default_date.getMonth(),
            default_year = $default_date.getFullYear(),
            elm_default_date = $('.datepicker--cell[data-date="' + default_day + '"][data-month="'+ default_month +'"][data-year="' + default_year + '"]');

        if(elm_default_date.hasClass('fat-slot-not-free')){
            elm_default_date = $('.datepicker--cell:not(.fat-slot-not-free):not(.-disabled-)');
            if(elm_default_date.length > 0){
                default_day = elm_default_date.attr('data-date');
                default_month = elm_default_date.attr('data-month');
                default_year = elm_default_date.attr('data-year');
                $default_date = new Date(default_year, default_month, default_day);
            }
        }
        elm_default_date = $('.datepicker--cell[data-date="' + default_day + '"][data-month="'+ default_month +'"][data-year="' + default_year + '"]');
        $('.air-date-picker', date_wrap).data('datepicker').selectDate($default_date);
        if(typeof is_return_set_active!='undefined' && is_return_set_active==1){
            FatSbBookingServices_FE.hasSetActiveDate = true;
        }
    };

    FatSbBookingServices_FE.initPayment = function (container) {
        var location = $('.fat-sb-tab-content.customer .fat-sb-employee-item .location span.value', container).text(),
            service = '',
            service_extra_ids = '',
            employee = $('.fat-sb-tab-content.customer  .fat-sb-employee-item .employee-title', container).text(),
            number_of_person = $('#number_of_person', container).val(),
            price = typeof FatSbBookingServices_FE.s_price != 'undefined' ? parseFloat(FatSbBookingServices_FE.s_price) : 0,
            price_label = '',
            discount = $('#coupon', container).val() != '' ? $('.fat-sb-order-discount .fat-item-value', container).attr('data-value') : 0,
            total = 0,
            total_origin = 0,
            service_info = _.where(FatSbBookingServices_FE.services, {s_id: FatSbBookingServices_FE.s_id}),
            time_end_label = '',
            tax_percent = 0,
            tax = 0,
            extra_price = 0,
            extra_tax = 0,
            total_days = FatSbBookingServices_FE.multiple_days.length;

        total_days = total_days >0 ? total_days : 1;

        if (typeof service_info[0].s_duration != 'undefined' && service_info[0].s_duration != null) {
            tax_percent = parseFloat(service_info[0].s_tax);
            service = service_info[0].s_name;
        }

        var service_extra,
            cb_se_id = 0;
        $('.fat-sb-order-extra-service .fat-sb-service-extra-item',container).each(function(){
            if($(this).is(':checked')){
                cb_se_id = $(this).val();
                service_extra = _.find(FatSbBookingServices_FE.services_extra,{se_id: cb_se_id});
                if(service_extra.se_price_on_total==1){
                    extra_price += parseFloat(service_extra.se_price);
                    extra_tax += (parseFloat(service_extra.se_price) * parseFloat(service_extra.se_tax) ) / 100;
                }else{
                    extra_price += (parseFloat(service_extra.se_price) * number_of_person);
                    extra_tax += (number_of_person * parseFloat(service_extra.se_price) * parseFloat(service_extra.se_tax) ) / 100;
                }
            }
        });

        discount = typeof discount != 'undefined' && discount != '' && !isNaN(discount) ? parseFloat(discount) : 0;

        var $price_base_quantity = FatSbMain_FE.calculatePrice(number_of_person, price, FatSbBookingServices_FE.s_id);

        tax = $price_base_quantity * tax_percent / 100;
        tax = tax + extra_tax;
        total_origin = ($price_base_quantity + extra_price + tax)*total_days;
        total =  total_origin - discount;
        total = total > 0 ? total : 0;

        price_label = FatSbMain_FE.getPriceLabel(number_of_person, price, $price_base_quantity,  FatSbBookingServices_FE.s_id);

        $('.fat-sb-order-service .fat-item-value', container).text(service);
        $('.fat-sb-order-employee .fat-item-value', container).text(employee);
        $('.fat-sb-order-location .fat-item-value', container).text(location);
        $('.fat-sb-order-price .fat-item-value', container).html(price_label);
        $('.fat-sb-order-time-end .fat-item-value', container).text(time_end_label);
        $('.fat-sb-order-price', container).attr('data-value',price);
        $('.fat-order-wrap', container).attr('data-price',price);
        $('.fat-order-wrap', container).attr('data-total',total);

        if(container.hasClass('services-no-tab')){
            $('.fat-sb-order-date .fat-item-value', container).text(
                $('.fat-sb-data-selected .fat-sb-date-time-item-inner .date-title', container).text()
                + ', ' + $('.fat-sb-data-selected .fat-sb-date-time-item-inner .time-title', container).text()
            );
        }else{
            $('.fat-sb-order-date .fat-item-value', container).text($('.fat-sb-data-selected .fat-sb-date-time-item-inner .date-title', container).text());
            $('.fat-sb-order-time .fat-item-value', container).text( $('.fat-sb-data-selected .fat-sb-date-time-item-inner .time-title', container).text());
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

    FatSbBookingServices_FE.initStripeCardInput = function () {
        if ($('form#stripe-payment-form').length == 0) {
            return;
        }
        $('form#stripe-payment-form').each(function(){
            var stripe_form = $(this),
                booking_container = stripe_form.closest('.fat-booking-container'),
                pk = stripe_form.attr('data-pk'),
                card_element_id = $('.card-element', booking_container).attr('id'),
                card_errors_id = $('.card-errors', booking_container).attr('id');

            if(typeof pk=='undefined' || pk==''){
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
                    container = self.closest('.ui.step-tab-content');

                FatSbMain_FE.addLoading(container, self);
                stripe.createToken(card).then(function (result) {

                    var self = $('button.fat-bt-payment', booking_container),
                        container = self.closest('.ui.step-tab-content');

                    if (result.error) {
                        // Inform the user if there was an error.
                        var errorElement = document.getElementById(card_errors_id);
                        errorElement.textContent = result.error.message;
                        FatSbMain_FE.removeLoading(container, self);
                    } else {
                        // Send the token to your server.
                        var self = $('button.fat-bt-payment', booking_container),
                            container = self.closest('.fat-sb-services-layout'),
                            service_id = FatSbBookingServices_FE.s_id,
                            services_extra = '',
                            employee_id = FatSbBookingServices_FE.e_id,
                            loc_id = FatSbBookingServices_FE.loc_id,
                            date = FatSbBookingServices_FE.b_date,
                            time =  FatSbBookingServices_FE.b_time,
                            number_of_person =$('#number_of_person', container).val(),
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
                                    b_description: note,
                                    multiple_days: FatSbBookingServices_FE.multiple_days
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
                                    if(container.hasClass('services-no-tab')){
                                        FatSbBookingServices_FE.nextTabContent(self, container);
                                    }else{
                                        $('.fat-sb-tab li:not([data-tab="completed"])', container).addClass('fat-disabled');
                                        $('.fat-sb-tab li[data-tab="completed"]', container).removeClass('fat-disabled');
                                        $('.fat-sb-tab li[data-tab="completed"]', container).trigger('click');
                                    }
                                    $('.fat-bt-add-icalendar', container).attr('data-id',data.code);
                                    $('.fat-bt-add-google-calendar', container).attr('data-id',data.code);
                                } else {
                                    FatSbMain_FE.removeLoading(container, self);
                                    var errorElement = document.getElementById(card_errors_id);
                                    errorElement.textContent = data.message;
                                }
                            },
                            error: function () {
                                FatSbMain_FE.removeLoading(container, self);
                                $('.fat-sb-error-message',container).html(data.message).removeClass('fat-sb-hidden');
                            }
                        });
                    }
                });
            });

            var paymentType = $('.fat-sb-payment-method-wrap', booking_container).val();
            if (paymentType === 'stripe') {
                $('.fat-sb-order-stripe', booking_container).show();
            } else {
                $('.fat-sb-order-stripe', booking_container).hide();
            }
        })
    };

    FatSbBookingServices_FE.couponOnChange = function (self) {
        if (self.val() == '') {
            var container = self.closest('.fat-sb-services-layout');
            $('.fat-coupon-error', container).html('');
            FatSbBookingServices_FE.initPayment(container);
        }
    };

    FatSbBookingServices_FE.initCoupon = function (self) {
        var container = self.closest('.fat-sb-services-layout'),
            coupon = $('#coupon', container).val(),
            s_id = FatSbBookingServices_FE.s_id;
        if (s_id == '' || coupon == '') {
            var discount = 0;
            $('.fat-sb-order-discount .fat-item-value', container).attr('data-value', discount);
            $('.fat-sb-order-discount .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + discount.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);
            FatSbBookingServices_FE.initPayment(container);
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

                FatSbBookingServices_FE.initPayment(container);

                self.removeClass('loading');
            },
            error: function () {
                self.removeClass('loading');
            }
        })
    };

    FatSbBookingServices_FE.addToICalendar = function(self){
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

    FatSbBookingServices_FE.addToGoogleCalendar = function(self){
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

    FatSbBookingServices_FE.submitBooking = function(self){
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
                c_phone_code =  $('#phone_code', container).val(),
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
                                b_service_id: FatSbBookingServices_FE.s_id,
                                b_services_extra: services_extra,
                                b_loc_id: FatSbBookingServices_FE.loc_id,
                                b_employee_id: FatSbBookingServices_FE.e_id,
                                b_date: FatSbBookingServices_FE.b_date,
                                b_time: FatSbBookingServices_FE.b_time,
                                b_customer_number: number_of_person,
                                b_coupon_code: coupon,
                                b_gateway_type: payment_method,
                                c_first_name: c_first_name,
                                c_last_name: c_last_name,
                                c_email: c_email,
                                c_phone: c_phone,
                                c_phone_code: c_phone_code,
                                b_description: note,
                                multiple_days: FatSbBookingServices_FE.multiple_days
                            }
                        }),
                        success: function (response) {
                            response = $.parseJSON(response);
                            if (response.result > 0) {

                                if(typeof response.redirect_url != 'undefined' && response.redirect_url != ''){
                                    window.location.href = response.redirect_url;
                                    return;
                                }

                                if(payment_method=='myPOS' && total > 0){
                                    var form = $(response.form);
                                    form.hide();
                                    $('body').append(form);
                                    $('form#ipcForm').submit();
                                    return;
                                }

                                if (payment_method == 'onsite' || payment_method == 'price-package' || payment_method == 'paypal' || total==0 ) {
                                    if(container.hasClass('services-no-tab')){
                                        FatSbBookingServices_FE.nextTabContent(self, container);
                                    }else{
                                        $('.fat-sb-tab li:not([data-tab="completed"])', container).addClass('fat-disabled');
                                        $('.fat-sb-tab li[data-tab="completed"]', container).removeClass('fat-disabled').removeClass('fat-sb-hidden');
                                        $('.fat-sb-tab li[data-tab="completed"]', container).trigger('click');
                                    }
                                    $('.fat-bt-add-icalendar', container).attr('data-id',response.result);
                                    $('.fat-bt-add-google-calendar', container).attr('data-id',response.result);
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

    FatSbBookingServices_FE.getESDay = function (date) {
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

    FatSbBookingServices_FE.getDateFormat = function () {
        var date_format = FatSbMain_FE.data.date_format;
        date_format = date_format.replace('M', 'M');
        date_format = date_format.replace('F', 'MM');
        date_format = date_format.replace('m', 'mm');
        date_format = date_format.replace('n', 'mm');

        date_format = date_format.replace('d', 'dd');
        date_format = date_format.replace('jS', 'dd');
        date_format = date_format.replace('js', 'dd');
        date_format = date_format.replace('j', 'dd');
        date_format = date_format.replace('s', 'dd');

        date_format = date_format.replace('Y', 'yyyy');
        date_format = date_format.replace('年','/');
        date_format = date_format.replace('月','/');
        date_format = date_format.replace('日','');

        return date_format;
    };

    FatSbBookingServices_FE.nextTabContent = function(self, container){
        var tab = self.closest('.fat-sb-tab-content').attr('data-next-tab');
        $('.fat-sb-tab-content.active',container).fadeOut(function(){
            $(this).removeClass('active');
            $('.fat-sb-tab-content[data-tab="'+ tab +'"]',container).fadeIn(function(){
                $(this).addClass('active');
            })
        })
    };

    FatSbBookingServices_FE.resetValidateField = function (self) {
        if (self.val() != '') {
            self.closest('.field').removeClass('field-error');
        }
    };

    FatSbBookingServices_FE.addLimitNotice = function(container){
        $('.fat-sb-multiple-days .notice', container).remove();
        var message = FatSbMain_FE.data.multiple_days_notice;
        message = message.replace('{d}', FatSbBookingServices_FE.s_min_multiple_slot);
        $('.fat-sb-multiple-days', container).append('<div class="notice">' + message + '</div>');
    };

    $(document).ready(function () {
        FatSbBookingServices_FE.init();
        FatSbMain_FE.initFormBuilder();
    })
})(jQuery);