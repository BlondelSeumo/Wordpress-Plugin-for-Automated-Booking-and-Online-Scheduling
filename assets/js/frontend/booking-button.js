"use strict";
var FatSbBookingButton_FE = {
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
    s_duration: 0,
    s_break_time: 0,
    b_date: 0,
    b_time: 0,
    s_min_cap : 1,
    s_max_cap: 1,
    s_price: 0,
    b_time_title:'',
    employee_name:'',
    loc_address:'',
    customer_name:'',
    customer_phone: '',
    customer_email: '',
    hasSetActiveDate: false,
    hide_service_tab: 0,
    hide_employee_tab: 0,
    hide_time_slot: 0,
    bookings: []
};

(function ($) {

    FatSbBookingButton_FE.init = function () {
        $('.fat-sb-booking-button').each(function(){
            var self = $(this),
                bg_color = self.attr('data-bg-color'),
                color = self.attr('data-color'),
                font_size = self.attr('data-font-size'),
                button =  $('a',self);
            button.css('background-color', bg_color);
            button.css('color', color);
            button.css('font-size', font_size);
        });
        FatSbMain_FE.registerOnClick($('.fat-sb-booking-button'));
    };

    FatSbBookingButton_FE.initField = function(){
        $('.fat-sb-booking-button-popup').each(function () {
            var container = $(this);

            //phone code
            $('.ui.dropdown',container).each(function () {
                var self = $(this);
                self.dropdown({
                    clearable: self.hasClass('clearable')
                });
            });

            //air datetime
            var date_format = FatSbBookingButton_FE.getDateFormat(),
                elmBookingDate = $('.air-date-picker', container),
                locale = elmBookingDate.attr('data-locale');
            locale = locale.split('_').length > 1 ? locale.split('_')[0] : locale;
            var option = {
                inline: true,
                language: locale,
                minDate: new Date(),
                dateFormat: date_format
            };
            FatSbBookingButton_FE.bod_field = elmBookingDate.datepicker(option).data('datepicker');
        })
    };

    FatSbBookingButton_FE.serviceCatItemOnClick = function(self){
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

    FatSbBookingButton_FE.locationItemOnClick = function(self){
        var loc = self.attr('data-loc'),
            loc_id = self.attr('data-id'),
            container = self.closest('.fat-booking-container'),
            loc_container = self.closest('.fat-sb-list-locations');

        if(!self.hasClass('active')){
            $('.fat-sb-list-item-inner.active',loc_container).removeClass('active');
            self.addClass('active');
            $('.fat-sb-list-employees .fat-sb-employee-item',container).addClass('fat-sb-deactive');
            $('.fat-sb-list-employees .fat-sb-employee-item-inner',container).removeClass('active');
            $('.fat-sb-list-employees .fat-sb-employee-item.' + loc,container).removeClass('fat-sb-deactive');
            FatSbBookingButton_FE.loc_id = loc_id;
        }
    };

    FatSbBookingButton_FE.serviceItemOnClick = function(self){
        var id = self.attr('data-id'),
            duration = self.attr('data-duration'),
            break_time = self.attr('data-break-time'),
            container = self.closest('.fat-sb-booking-button-popup'),
            service_content = self.closest('.fat-sb-select-services'),
            employee_content = $('.fat-sb-select-employees',container),
            title = employee_content.attr('data-title');


        FatSbBookingButton_FE.s_duration = typeof duration!='undefined' && !isNaN(duration) ? parseInt(duration) : 0;
        FatSbBookingButton_FE.s_break_time = typeof break_time!='undefined' && !isNaN(break_time) ? parseInt(break_time) : 0;

        FatSbBookingButton_FE.s_id = id;
        FatSbBookingButton_FE.e_id = 0;
        FatSbBookingButton_FE.loc_id = 0;
        FatSbBookingButton_FE.b_date = 0;
        FatSbBookingButton_FE.b_time = 0;
        FatSbBookingButton_FE.s_price = 0;
        FatSbBookingButton_FE.s_max_cap = 1;
        FatSbBookingButton_FE.s_min_cap = 1;

        $('.fat-sb-employee-item:not(.fat-sb-hidden)',container).addClass('fat-sb-hidden');
        $('.fat-sb-employee-item.ser_' + id,container).removeClass('fat-sb-hidden');
        $('.fat-sb-employee-item-inner.active',container).removeClass('active');

        //display price and capacity
        var services_employee = _.where(FatSbBookingButton_FE.services_employee,{s_id: id}),
            employee_item = '',
            price = 0;

        for(var $se_index = 0; $se_index < services_employee.length; $se_index++){
            employee_item = $('.fat-sb-employee-item.ser_' + services_employee[$se_index].s_id + ' .fat-sb-employee-item-inner[data-id="' + services_employee[$se_index].e_id + '"]');
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
        if (FatSbBookingButton_FE.bod_field != null) {
            try{
                FatSbBookingButton_FE.bod_field.clear();
            }catch(err){}
        }
        $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).addClass('disabled').removeClass('active');

        //init list service extra
        var $service = _.find(FatSbBookingButton_FE.services,{s_id:FatSbBookingButton_FE.s_id}),
            elm_extra = $('.fat-order-wrap .fat-sb-order-extra-service',container);
        elm_extra.addClass('fat-sb-hidden');
        $('span.fat-item-value ul li',elm_extra).remove();

        if(typeof $service.s_extra_ids !='undefined' && $service.s_extra_ids!=''){
            var $s_extra_ids = $service.s_extra_ids.split(','),
                $service_extra = _.filter(FatSbBookingButton_FE.services_extra,function(item){
                    return $s_extra_ids.indexOf(item.se_id) > -1 ? true : false;
                });
            if($service_extra!=null && $service_extra.length > 0){
                elm_extra.removeClass('fat-sb-hidden');
                for(var $i=0; $i < $service_extra.length; $i++){
                    $('ul',elm_extra).append('<li><input type="checkbox" data-onChange="FatSbBookingButton_FE.onChangeExtraService" value="' + $service_extra[$i].se_id +'" class="fat-sb-service-extra-item">'
                        + $service_extra[$i].se_name
                        + '<span  class="fat-se-price"> (' + FatSbMain_FE.data.symbol_prefix + parseFloat($service_extra[$i].se_price).format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix + ') </span> '
                        + '</li>');
                }
                FatSbMain_FE.registerOnChange($('.fat-sb-order-extra-service'));
            }
        }

        if(FatSbBookingButton_FE.hide_service_tab=="0"){
            service_content.fadeOut(function(){
                $('.fat-sb-booking-button-popup h4.fat-sb-popup-title').html(title);
                employee_content.fadeIn();
                $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back').attr('data-index',employee_content.attr('data-index'));
                $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back').show();
            });
        }else{
            service_content.hide();
            $('.fat-sb-booking-button-popup h4.fat-sb-popup-title').html(title);
            employee_content.show();
            $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back').attr('data-index',employee_content.attr('data-index'));
            $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back').show();
        }
    };

    FatSbBookingButton_FE.onChangeExtraService = function(self){
        var container = self.closest('.fat-sb-booking-button-popup');
        FatSbBookingButton_FE.initPayment(container);
    };

    FatSbBookingButton_FE.back = function(self){
        var index = parseInt(self.attr('data-index')),
            elm_current = $('[data-index="' + index +'"]', '.fat-sb-booking-button-popup'),
            elm_back,
            title,
            bt_next =  $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-next');

        if(index==1){
            return;
        }
        elm_current.fadeOut(function(){
            $('.fat-sb-booking-button-popup h4.fat-sb-popup-title').html(title);
            elm_back = $('.fat-sb-popup-modal-content-inner [data-index="' + (index-1) +'"]', '.fat-sb-booking-button-popup');
            title = $(elm_back).attr('data-title');
            self.attr('data-index',(index-1) );
            elm_back.fadeIn();

            $('.fat-sb-booking-button-popup').removeClass('fat-sb-order-review');
            $('.fat-sb-booking-button-popup .fat-sb-error-message').html('').addClass('fat-sb-hidden');
            if(index ==5){
                bt_next.show();
                bt_next.attr('data-index',(index-1));
            }else{
                bt_next.hide();
                bt_next.attr('data-index','');
            }
            if((index-1)==1){
                self.hide();
            }else{
                self.show();
            }
        });
    };

    FatSbBookingButton_FE.next = function(self){
            var index = parseInt(self.attr('data-index')),
                elm_current = $('[data-index="' + index +'"]', '.fat-sb-booking-button-popup'),
                elm_next = $('[data-index="' + (index + 1) +'"]', '.fat-sb-booking-button-popup'),
                title = $(elm_next).attr('data-title'),
                bt_next = $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-next'),
                bt_back = $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back');

            if(index==4){
                var form = $('.fat-sb-customer-wrap .ui.form', '.fat-sb-booking-button-popup');
                if (FatSbMain_FE.validateForm(form)) {
                    $('.fat-sb-booking-button-popup h4.fat-sb-popup-title').html(title);

                    FatSbBookingButton_FE.customer_name = $('#c_first_name','.fat-sb-booking-button-popup').val() + ' ' + $('#c_last_name','.fat-sb-booking-button-popup').val();
                    FatSbBookingButton_FE.customer_phone = $('#phone_code','.fat-sb-booking-button-popup').val() + $('#c_phone','.fat-sb-booking-button-popup').val();
                    FatSbBookingButton_FE.customer_email = $('#c_email','.fat-sb-booking-button-popup').val();
                    FatSbBookingButton_FE.initPayment($('.fat-sb-booking-button-popup'));

                    elm_current.fadeOut(function(){
                        $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back').attr('data-index',index);
                        elm_next.fadeIn();
                        bt_back.attr('data-index',(index+1));
                        bt_next.attr('data-index',(index+1));
                        bt_next.show();
                        bt_back.show();

                    });

                    $('.fat-sb-booking-button-popup').addClass('fat-sb-order-review');
                }
            }
            if(index==5){
                FatSbBookingButton_FE.submitBooking(self);
            }
    };

    FatSbBookingButton_FE.providerItemOnClick = function(self){
        var id = self.attr('data-id'),
            container = self.closest('.fat-sb-booking-button-popup'),
            employee_content = self.closest('.fat-sb-select-employees'),
            datetime_content = $('.fat-sb-select-date-time',container),
            title = datetime_content.attr('data-title'),
            location_info = '';

        FatSbBookingButton_FE.e_id = id;
        FatSbBookingButton_FE.employee_name = $('.employee-title', self).text();
        FatSbBookingButton_FE.loc_address = $('.location .value', self).text();
        FatSbBookingButton_FE.b_date = 0;
        FatSbBookingButton_FE.b_time = 0;
        FatSbBookingButton_FE.s_price = $('.price', self).attr('data-value');
        FatSbBookingButton_FE.s_max_cap = $('.capacity', self).attr('data-max');
        FatSbBookingButton_FE.s_min_cap = $('.capacity', self).attr('data-min');


        if(FatSbBookingButton_FE.loc_id <= 0){
            FatSbBookingButton_FE.loc_id = $('.fat-sb-list-locations .fat-sb-list-item-inner.active',container).length > 0 ? $('.fat-sb-list-locations .fat-sb-list-item-inner.active',container).attr('data-id') : $('.fat-sb-list-locations .fat-sb-list-item:first-child .fat-sb-list-item-inner',container).attr('data-id');
            if(typeof FatSbBookingButton_FE.loc_id=='undefined'){
                var emp_loc_ids = self.attr('data-loc-id').split(',');
                for(var $i=0; $i<emp_loc_ids.length; $i++){
                    location_info = _.findWhere(FatSbBookingButton_FE.location, {loc_id: emp_loc_ids[$i]});
                    if(typeof location_info != 'undefined'){
                        FatSbBookingButton_FE.loc_id = emp_loc_ids[$i];
                        break;
                    }
                }
            }
        }
        location_info = _.findWhere(FatSbBookingButton_FE.location, {loc_id: FatSbBookingButton_FE.loc_id});

        FatSbBookingButton_FE.loc_name = typeof location_info!='undefined' ? location_info.loc_name : '';

        FatSbBookingButton_FE.initSlot($('.fat-sb-select-date-time',container));

        //init number of person
        var elm_number_of_person = $('select.fat-sb-number-of-person-wrap',container);
        $('option',elm_number_of_person).remove();
        for(var $index = FatSbBookingButton_FE.s_min_cap; $index <= FatSbBookingButton_FE.s_max_cap; $index++){
            elm_number_of_person.append('<option value="'+ $index +'">' + $index + '</option>');
        }

        if(FatSbBookingButton_FE.hide_employee_tab=="0"){
            employee_content.fadeOut(function(){
                $('.fat-sb-booking-button-popup h4.fat-sb-popup-title').html(title);
                datetime_content.fadeIn();
                $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back').attr('data-index',datetime_content.attr('data-index'));
            });
        }else{
            employee_content.hide();
            $('.fat-sb-booking-button-popup h4.fat-sb-popup-title').html(title);
            datetime_content.show();
            $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back').attr('data-index',datetime_content.attr('data-index'));
        }

    };

    FatSbBookingButton_FE.timeItemOnClick = function(self){
        var container = self.closest('.fat-sb-booking-button-popup'),
            customer_content = $('.fat-sb-customer-wrap', container),
            datetime_content = $('.fat-sb-select-date-time', container),
            title = customer_content.attr('data-title'),
            bt_next = $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-next');

        // init customer number dropdown
        var min_cap = parseInt(FatSbBookingButton_FE.e_service.s_min_cap),
            max_cap = parseInt(self.attr('data-max-cap-available')),
            elm_customer_number = $('select.fat-sb-number-of-person-wrap', container);
        $('#number_of_person', container).val(min_cap);
        $('option', elm_customer_number).remove();
        for (var $n_index = min_cap; $n_index <= max_cap; $n_index++) {
            elm_customer_number.append('<option value="' + $n_index + '">' + $n_index + '</option>');
        }


        FatSbBookingButton_FE.b_date = $('#b_date',container).attr('data-date');
        FatSbBookingButton_FE.b_time = self.attr('data-value');
        FatSbBookingButton_FE.b_time_title = $('.time-label',self).text();
        FatSbBookingButton_FE.initPayment(container);

        datetime_content.fadeOut(function(){
            $('.fat-sb-booking-button-popup h4.fat-sb-popup-title').html(title);
            $('.fat-sb-booking-button-popup .fat-sb-button-group .fat-bt-back').attr('data-index',customer_content.attr('data-index'));
            bt_next.attr('data-index',customer_content.attr('data-index'));
            bt_next.show();
            customer_content.fadeIn();
        });
    };

    FatSbBookingButton_FE.initPayment = function (container) {
        var service = '',
            service_extra_ids = '',
            number_of_person = $('#number_of_person', container).val(),
            price = typeof FatSbBookingButton_FE.s_price != 'undefined' ? parseFloat(FatSbBookingButton_FE.s_price) : 0,
            price_label = '',
            discount = $('#coupon', container).val() != '' ? $('.fat-sb-order-discount .fat-item-value', container).attr('data-value') : 0,
            total = 0,
            total_origin = 0,
            service_info = _.where(FatSbBookingButton_FE.services, {s_id: FatSbBookingButton_FE.s_id}),
            time_end_label = '',
            tax_percent = 0,
            tax = 0,
            extra_price = 0,
            extra_tax = 0,
            total_days = 1;

        if (typeof service_info[0].s_duration != 'undefined' && service_info[0].s_duration != null) {
            tax_percent = parseFloat(service_info[0].s_tax);
            service = service_info[0].s_name;
        }

        var service_extra,
            cb_se_id = 0;
        $('.fat-sb-order-extra-service .fat-sb-service-extra-item',container).each(function(){
            if($(this).is(':checked')){
                cb_se_id = $(this).val();
                service_extra = _.find(FatSbBookingButton_FE.services_extra,{se_id: cb_se_id});

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

        var $price_base_quantity = FatSbMain_FE.calculatePrice(number_of_person, price, FatSbBookingButton_FE.s_id);

        tax = $price_base_quantity * tax_percent / 100;
        tax = tax + extra_tax;
        total_origin = ($price_base_quantity + extra_price + tax)*total_days;
        total =  total_origin - discount;
        total = total > 0 ? total : 0;

        price_label = FatSbMain_FE.getPriceLabel(number_of_person, price, $price_base_quantity,  FatSbBookingButton_FE.s_id);


        $('.fat-sb-order-service .fat-item-value', container).text(service);
        $('.fat-sb-order-employee .fat-item-value', container).text(FatSbBookingButton_FE.employee_name);
        $('.fat-sb-order-location .fat-item-value', container).text(FatSbBookingButton_FE.loc_address);
        $('.fat-sb-order-price .fat-item-value', container).html(price_label);
        $('.fat-sb-order-price', container).attr('data-value',price);
        $('.fat-order-wrap', container).attr('data-price',price);
        $('.fat-order-wrap', container).attr('data-total',total);

        $('.fat-sb-customer-name .fat-item-value', container).text(FatSbBookingButton_FE.customer_name);
        $('.fat-sb-customer-phone .fat-item-value', container).text(FatSbBookingButton_FE.customer_phone);
        $('.fat-sb-customer-email .fat-item-value', container).text(FatSbBookingButton_FE.customer_email);

        $('.fat-sb-order-date .fat-item-value', container).text(FatSbBookingButton_FE.b_date);
        $('.fat-sb-order-time .fat-item-value', container).text(FatSbBookingButton_FE.b_time_title);

        $('.fat-sb-order-location .fat-item-value', container).text(FatSbBookingButton_FE.loc_name);

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

    FatSbBookingButton_FE.openPopupBooking = function(elm){
        var show_category = elm.attr('data-show-category'),
            show_location = elm.attr('data-show-location');
        elm.addClass('loading');
        $.ajax({
            url: FatSbMain_FE.data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services_dictionary',
                s_field: FatSbMain_FE.data.ajax_s_field,
                layout: 'services'
            }),
            success: function (response) {
                response = $.parseJSON(response);
                elm.removeClass('loading');
                var popup_template = wp.template('fat-sb-popup-modal-template'),
                    service_template = wp.template('fat-sb-button-service-item-template'),
                    service_cat_template = wp.template('fat-sb-service-cat-item-template'),
                    location_template = wp.template('fat-sb-location-item-template'),
                    employee_template = wp.template('fat-sb-button-employee-item-template');

                var e_loc_ids = '',
                    service_id = elm.attr('data-service-id');

                service_id = typeof service_id!='undefined' && service_id!='' ? service_id : 0;

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

                if(service_id != 0 && service_id!=''){
                    FatSbBookingButton_FE.s_id = parseInt(service_id);
                    response.services =  _.where(response.services, {s_id: service_id});
                }

                FatSbBookingButton_FE.hide_service_tab = elm.attr('data-hide-service-tab');
                FatSbBookingButton_FE.hide_employee_tab = elm.attr('data-hide-employee-tab');
                FatSbBookingButton_FE.hide_time_slot = elm.attr('data-hide-time-slot');



                FatSbBookingButton_FE.services_cat = response.services_cat;
                FatSbBookingButton_FE.services = response.services;
                FatSbBookingButton_FE.services_work_day = response.services_work_day;
                FatSbBookingButton_FE.services_extra = response.services_extra;
                FatSbBookingButton_FE.location = response.location;
                FatSbBookingButton_FE.employees = response.employee;
                FatSbBookingButton_FE.services_employee = response.services_employee;

                $('body').append(popup_template);
                $('.fat-sb-select-services .fat-sb-list-services',popup_template).append($(service_template(response.services)));
                $('.fat-sb-select-employees .fat-sb-list-employees',popup_template).append($(employee_template(response.employee)));

                if(show_location ==1){
                    $('.fat-sb-select-employees .fat-sb-list-locations',popup_template).append($(location_template(response.location)));
                }
                if(show_category ==1){
                    $('.fat-sb-select-services .fat-sb-list-service-cat',popup_template).append($(service_cat_template(response.services_cat)));
                }

                if(FatSbBookingButton_FE.hide_time_slot=="1"){
                    $('body .fat-sb-popup-modal-content').addClass('hide-time-slot');
                }
                $('body .fat-sb-popup-modal-content').animate({
                    top: '50%',
                    opacity: 1
                },300);

                FatSbBookingButton_FE.initField();
                FatSbMain_FE.initFormBuilder();

                var popup_container = $('.fat-sb-popup-modal.fat-sb-booking-button-popup');
                FatSbMain_FE.registerOnClick(popup_container);
                FatSbMain_FE.registerOnChange(popup_container);

                FatSbBookingButton_FE.initStripeCardInput();

                if(FatSbBookingButton_FE.hide_service_tab=="1"){
                    $('.fat-sb-service-item-inner:first-child', '.fat-sb-popup-modal-content').trigger('click');
                    if(FatSbBookingButton_FE.hide_employee_tab=="1"){
                        $('.fat-sb-employee-item-inner:first', '.fat-sb-popup-modal-content').trigger('click');
                    }
                }
                $('body').trigger('booking_popup_open');

            },
            error: function () {
            }
        })
    };

    FatSbBookingButton_FE.closePopupBooking = function(elm){
        var popup = elm.closest('.fat-sb-popup-modal');
        $('body .fat-sb-popup-modal-content').animate({
            top: '60%',
            opacity: 0
        }, 300, function(){
            popup.remove();
        });
    };

    FatSbBookingButton_FE.initSlot = function (container) {
        var date_wrap = $('.fat-sb-booking-date-wrap', container),
            time_wrap = $('.fat-sb-booking-time-wrap', container);

        if (FatSbBookingButton_FE.bod_field != null) {
            try{
                FatSbBookingButton_FE.bod_field.clear();
            }catch(err){}
        }

        FatSbMain_FE.showLoading(container);

        $('.item',time_wrap).each(function(){
            var label = $(this).attr('data-label'),
                time = $(this).attr('data-value'),
                end_time = FatSbBookingButton_FE.s_duration + parseInt(time),
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
                    s_id: FatSbBookingButton_FE.s_id,
                    e_id: FatSbBookingButton_FE.e_id,
                    loc_id: FatSbBookingButton_FE.loc_id,
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

                        FatSbBookingButton_FE.e_service = _.findWhere(e_services, {s_id: FatSbBookingButton_FE.s_id});
                        FatSbBookingButton_FE.bookings = bookings;

                        var $default_date = '';
                        var $service_work_day = _.where(FatSbBookingButton_FE.services_work_day,{s_id: FatSbBookingButton_FE.s_id});
                        if($service_work_day.length > 0){
                            var from_date = '',
                                now = new Date();
                            for(var $swd_index = 0; $swd_index < $service_work_day.length; $swd_index ++){
                                from_date = moment($service_work_day[$swd_index].from_date);
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
                                    var $service_work_day = _.where(FatSbBookingButton_FE.services_work_day,{s_id: FatSbBookingButton_FE.s_id});

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

                                    var $es_day = FatSbBookingButton_FE.getESDay(date);
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
                                            if (e_schedules[$es_index].es_enable == "1" ) {
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
                                    if( !FatSbBookingButton_FE.hasSetActiveDate && (elm_default_date.hasClass('fat-slot-not-free') || elm_default_date.hasClass('-disabled-') )){
                                        if(FatSbBookingButton_FE.hide_time_slot!="1"){
                                            FatSbBookingButton_FE.setActiveDate(date, date_wrap, 1);
                                        }
                                    }
                                },500);

                                selected_date_value = date.getFullYear() + '-' + month + '-' + day;
                                $('#b_date', container).attr('data-date', selected_date_value);

                                $('.fat-sb-booking-time-wrap .item .time-label.active', container).removeClass('active');
                                $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).addClass('disabled');
                                $('.fat-sb-booking-time-wrap .fat-empty-time-slot', container).remove();

                                if (typeof date == 'undefined' || date == '' || $('#employee', container).val() == '') {
                                    return;
                                }

                                //check service working day
                                var $service_work_day = _.where(FatSbBookingButton_FE.services_work_day,{s_id: FatSbBookingButton_FE.s_id});
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

                                var $es_day = FatSbBookingButton_FE.getESDay(date),
                                    time = 0,
                                    self = '',
                                    work_hours = [],
                                    service = _.findWhere(FatSbBookingButton_FE.services, {s_id: FatSbBookingButton_FE.s_id}),
                                    duration = !isNaN(service.s_duration) ? parseInt(service.s_duration) : 0,
                                    s_break_time = !isNaN(service.s_break_time) ? parseInt(service.s_break_time) : 0,
                                    extra_ids = '',
                                    break_times = _.where(e_break_times, {es_day: String($es_day)});

                                if(extra_ids!=''){
                                    extra_ids = extra_ids.split(',');
                                    var extra_info = '';
                                    for(var $ex_index=0; $ex_index < extra_ids.length; $ex_index++){
                                        extra_info = _.findWhere(FatSbBookingButton_FE.services_extra, {se_id: extra_ids[$ex_index]});
                                        if(typeof extra_info!='undefined' && typeof extra_info.se_duration!='undefined'){
                                            duration += parseInt(extra_info.se_duration);
                                        }
                                    }
                                }

                                if (FatSbBookingButton_FE.s_id != '') {
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
                                                        if ((time + duration ) < 1440 && work_hours[$wk_index].s_id.indexOf(FatSbBookingButton_FE.s_id) >= 0 &&
                                                            parseInt(work_hours[$wk_index].es_work_hour_start) <= time && (time + duration +  s_break_time) <= parseInt(work_hours[$wk_index].es_work_hour_end)) {
                                                            self.removeClass('disabled').removeClass('over-day');
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
                                                            ( (time + duration ) >= es_break_time_start && (time + duration ) <= es_break_time_end) ){
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
                                        self.attr('data-max-cap-available', FatSbBookingButton_FE.e_service.s_max_cap);
                                    });

                                    //check base on booking
                                    var booking_in_day = _.where(bookings, {b_date: selected_date_value}),
                                        booking_service_in_day = _.where(bookings, {b_date: selected_date_value, b_service_id: FatSbBookingButton_FE.s_id.toString(), b_loc_id: FatSbBookingButton_FE.loc_id.toString()});

                                    if (typeof booking_in_day != 'undefined') {
                                        var b_time = 0,
                                            b_end_time = 0,
                                            b_service_id = 0,
                                            b_loc_id = 0,
                                            time = 0,
                                            end_time = 0,
                                            self,
                                            min_cap =  parseInt(FatSbBookingButton_FE.e_service.s_min_cap),
                                            max_cap =  parseInt(FatSbBookingButton_FE.e_service.s_max_cap),
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
                                            end_time = time + parseInt(FatSbBookingButton_FE.s_duration) + s_break_time;

                                            /** check duplicate time with another service */
                                            for (var $bs_index = 0; $bs_index < booking_in_day.length; $bs_index++) {
                                                b_time = parseInt(booking_in_day[$bs_index].b_time);
                                                b_end_time = b_time + parseInt(booking_in_day[$bs_index].b_service_duration) +  parseInt(booking_in_day[$bs_index].b_service_break_time);
                                                b_service_id = parseInt(booking_in_day[$bs_index].b_service_id);
                                                b_loc_id = parseInt(booking_in_day[$bs_index].b_loc_id);

                                                if(b_time == time && end_time == b_end_time && b_service_id == FatSbBookingButton_FE.s_id && b_loc_id == FatSbBookingButton_FE.loc_id){
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


                                    if(FatSbBookingButton_FE.hide_time_slot=="1"){
                                        if ($('.fat-sb-booking-time-wrap .item:not(.disabled)', container).length > 0){
                                            $('.fat-sb-booking-time-wrap .item:not(.disabled)', container).each(function(index, element){
                                                if(index==0){
                                                    $(element).trigger('click');
                                                }
                                            });
                                        }
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
                        if(FatSbBookingButton_FE.hide_time_slot!="1"){
                            FatSbBookingButton_FE.setActiveDate($default_date, date_wrap);
                        }
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
            setTimeout(function(){
                FatSbMain_FE.closeLoading(container);
            },1500);
        }
    };

    FatSbBookingButton_FE.getDateFormat = function () {
        var date_format = FatSbMain_FE.data.date_format;
        date_format = date_format.replace('M', 'M');
        date_format = date_format.replace('F', 'MM');
        date_format = date_format.replace('m', 'mm');
        date_format = date_format.replace('n', 'mm');

        date_format = date_format.replace('d', 'dd');
        date_format = date_format.replace('j', 'dd');
        date_format = date_format.replace('s', 'dd');

        date_format = date_format.replace('Y', 'yyyy');
        date_format = date_format.replace('年','/');
        date_format = date_format.replace('月','/');
        date_format = date_format.replace('日','');
        return date_format;
    };

    FatSbBookingButton_FE.getESDay = function (date) {
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

    FatSbBookingButton_FE.resetValidateField = function (self) {
        if (self.val() != '') {
            self.closest('.field').removeClass('field-error');
        }
    };

    FatSbBookingButton_FE.submitBooking = function(self){
        var container = self.closest('.fat-sb-booking-button-popup'),
            form = $('.ui.form', container);
        if (FatSbMain_FE.validateForm(form)) {
            var number_of_person = $('#number_of_person', container).val(),
                coupon = $('#coupon', container).val(),
                payment_method = $('#payment_method', container).val(),
                c_first_name = $('#c_first_name', container).val(),
                c_last_name = $('#c_last_name', container).val(),
                c_email = $('#c_email', container).val(),
                c_phone =  $('#c_phone', container).val(),
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
                                b_service_id: FatSbBookingButton_FE.s_id,
                                b_services_extra: services_extra,
                                b_loc_id: FatSbBookingButton_FE.loc_id,
                                b_employee_id: FatSbBookingButton_FE.e_id,
                                b_date: FatSbBookingButton_FE.b_date,
                                b_time: FatSbBookingButton_FE.b_time,
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

                                if(payment_method=='myPOS' && total > 0){
                                    var form = $(response.form);
                                    form.hide();
                                    $('body').append(form);
                                    $('form#ipcForm').submit();
                                    return;
                                }

                                if (payment_method == 'onsite' || payment_method == 'price-package' || payment_method != 'paypal' || total == 0 ) {
                                    $('.fat-sb-order-information',container).fadeOut(function(){
                                        var title = $('.fat-sb-order-completed', container).attr('data-title');
                                        $('.fat-sb-popup-title',container).html(title);
                                        container.removeClass('fat-sb-order-review');
                                        $('.fat-bt-back',container).remove();
                                        $('.fat-bt-next',container).remove();
                                        $('.fat-sb-order-completed', container).fadeIn();
                                    });

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

    FatSbBookingButton_FE.couponOnChange = function (self) {
        if (self.val() == '') {
            var container = self.closest('.fat-sb-booking-button-popup');
            $('.fat-coupon-error', container).html('');
            FatSbBookingButton_FE.initPayment(container);
        }
    };

    FatSbBookingButton_FE.initCoupon = function (self) {
        var container = self.closest('.fat-sb-booking-button-popup'),
            coupon = $('#coupon', container).val(),
            s_id = FatSbBookingButton_FE.s_id;
        if (s_id == '' || coupon == '') {
            var discount = 0;
            $('.fat-sb-order-discount .fat-item-value', container).attr('data-value', discount);
            $('.fat-sb-order-discount .fat-item-value', container).text(FatSbMain_FE.data.symbol_prefix + discount.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix);
            FatSbBookingButton_FE.initPayment(container);
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

                FatSbBookingButton_FE.initPayment(container);

                self.removeClass('loading');
            },
            error: function () {
                self.removeClass('loading');
            }
        })
    };

    FatSbBookingButton_FE.addToICalendar = function(self){
        var container = self.closest('.fat-sb-booking-button-popup'),
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

    FatSbBookingButton_FE.addToGoogleCalendar = function(self){
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

    FatSbBookingButton_FE.paymentOnChange = function(self){
        var container = self.closest('.fat-sb-popup-modal.fat-sb-booking-button-popup'),
            payment_method = self.val();
        if (payment_method === 'stripe') {
            $('.fat-sb-order-stripe', container).show();
        } else {
            $('.fat-sb-order-stripe', container).hide();
        }
    };

    FatSbBookingButton_FE.initStripeCardInput = function () {
        if ($('form#stripe-payment-form').length == 0) {
            return;
        }

        var pk = $('form#stripe-payment-form').attr('data-pk');
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

            var self = $('button.fat-bt-payment', '.fat-sb-booking-button-popup'),
                container = self.closest('.ui.step-tab-content');

            FatSbMain_FE.addLoading(container, self);
            stripe.createToken(card).then(function (result) {

                var self = $('button.fat-bt-payment', '.fat-sb-booking-button-popup'),
                    container = self.closest('.ui.step-tab-content');

                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    FatSbMain_FE.removeLoading(container, self);
                } else {
                    // Send the token to your server.
                    var self = $('button.fat-bt-payment', '.fat-sb-booking-button-popup'),
                        container = self.closest('.fat-sb-booking-button-popup'),
                        service_id = FatSbBookingButton_FE.s_id,
                        services_extra = '',
                        employee_id = FatSbBookingButton_FE.e_id,
                        loc_id = FatSbBookingButton_FE.loc_id,
                        date = FatSbBookingButton_FE.b_date,
                        time =  FatSbBookingButton_FE.b_time,
                        number_of_person =$('#number_of_person', container).val(),
                        coupon = $('#coupon', container).val(),
                        payment_method = $('#payment_method', container).val(),
                        c_first_name = $('#c_first_name', container).val(),
                        c_last_name = $('#c_last_name', container).val(),
                        c_email = $('#c_email', container).val(),
                        c_phone =  $('#c_phone', container).val(),
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
                                $('.fat-sb-order-information',container).fadeOut(function(){
                                    var title = $('.fat-sb-order-completed', container).attr('data-title');
                                    $('.fat-sb-popup-title',container).html(title);
                                    container.removeClass('fat-sb-order-review');
                                    $('.fat-bt-back',container).remove();
                                    $('.fat-bt-next',container).remove();
                                    $('.fat-sb-order-completed', container).fadeIn();
                                });
                                $('.fat-bt-add-icalendar', container).attr('data-id',data.code);
                                $('.fat-bt-add-google-calendar', container).attr('data-id',data.code);
                            } else {
                                FatSbMain_FE.removeLoading(container, self);
                                $('.fat-sb-error-message',container).html(data.message).removeClass('fat-sb-hidden');
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

        var paymentType = $('.fat-sb-payment-method-wrap').val();
        if (paymentType === 'stripe') {
            $('.fat-sb-order-stripe').show();
        } else {
            $('.fat-sb-order-stripe').hide();
        }
    };

    FatSbBookingButton_FE.setActiveDate = function($default_date, date_wrap, is_return_set_active){
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
            FatSbBookingButton_FE.hasSetActiveDate = true;
        }
    };

    $(document).ready(function () {
        FatSbBookingButton_FE.init();

    })
})(jQuery);
