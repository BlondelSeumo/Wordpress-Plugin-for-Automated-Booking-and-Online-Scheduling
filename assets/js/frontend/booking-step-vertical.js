"use strict";
var FatSbBookingStepVertical_FE = {
    bod_field: null,
    e_id: 0,
    e_name: '',
    loc_id: 0,
    loc_name: '',
    s_id: 0,
    s_name: '',
    services_extra: [],
    service_employee:[],
    s_duration: 0,
    s_break_time: 0,
    b_date: 0,
    b_time: 0,
    b_time_label: '',
    s_price: 0,
    s_tax: 0,
    b_total: 0,
    time_slot: [],
    payment_method: '',
    s_multiple_days: 0,
    s_min_multiple_slot: 1,
    s_max_multiple_slot: 1,
    multiple_days: []
};

(function ($) {
    FatSbBookingStepVertical_FE.init = function () {
        FatSbBookingStepVertical_FE.initField();
        FatSbBookingStepVertical_FE.initStripeCardInput();
        FatSbMain_FE.registerOnClick($('.fat-booking-container.fat-sb-step-vertical-layout'));

    };

    FatSbBookingStepVertical_FE.initField = function(){
        $('.fat-booking-container.fat-sb-step-vertical-layout').each(function () {
            var container = $(this);

            FatSbBookingStepVertical_FE.service_employee = $.parseJSON(container.attr('data-se'));

            //phone code
            $('.ui.dropdown',container).each(function () {
                var self = $(this);
                self.dropdown({
                    clearable: self.hasClass('clearable')
                });
            });

            container.addClass('has-init');
        })
    };

    FatSbBookingStepVertical_FE.itemOnClick = function(self){
        var container = self.closest('.fat-booking-container'),
            item_wrap = self.closest('.fat-sb-item-wrap'),
            tab = self.closest('.fat-sb-tab-content').attr('data-tab');

        $('.fat-sb-item-inner.active', item_wrap).removeClass('active');
        self.addClass('active');

        if(tab=='location'){
            FatSbBookingStepVertical_FE.loc_id = self.attr('data-id');
            FatSbBookingStepVertical_FE.loc_name = self.attr('data-name');

            if(FatSbBookingStepVertical_FE.s_id>0){
                var employees = _.where(FatSbBookingStepVertical_FE.service_employee, {s_id: FatSbBookingStepVertical_FE.s_id});
                $('.fat-sb-list-employees .fat-sb-item', container).addClass('fat-sb-hidden');
                for(var $i=0; $i< employees.length; $i++){
                    $('.fat-sb-list-employees .fat-sb-item.emp-' + employees[$i].e_id + ' .price-capacity .price', container).html(FatSbMain_FE.data.symbol + employees[$i].s_price);
                    $('.fat-sb-list-employees .fat-sb-item.emp-' + employees[$i].e_id + '.loc-' + FatSbBookingStepVertical_FE.loc_id, container).removeClass('fat-sb-hidden');
                }
                if($('.fat-sb-list-employees .fat-sb-item:not(.fat-sb-hidden)',container).length==0){
                    FatSbBookingStepVertical_FE.addEmptyEmployeeNotice($('.fat-sb-tab-content.employees .fat-sb-list-employees'));
                }
            }
        }

        if(tab=='category'){
            var cat = self.attr('data-cat');
            $('.fat-sb-list-services .fat-sb-item').addClass('fat-sb-hidden');
            $('.fat-sb-list-services .fat-sb-item.' + cat).removeClass('fat-sb-hidden');
        }

        if(tab == 'service'){
            var se_ids = self.attr('data-se-id');
            se_ids = typeof se_ids !='undefined' ? se_ids.split(',') : [];

            FatSbBookingStepVertical_FE.s_multiple_days = self.attr('data-s-multiple-days');
            FatSbBookingStepVertical_FE.s_min_multiple_slot = self.attr('data-s-min-slot');
            FatSbBookingStepVertical_FE.s_max_multiple_slot = self.attr('data-s-max-slot');
            FatSbBookingStepVertical_FE.s_multiple_days = FatSbBookingStepVertical_FE.s_multiple_days!='' && !isNaN(FatSbBookingStepVertical_FE.s_multiple_days) ? parseInt(FatSbBookingStepVertical_FE.s_multiple_days) : 0;
            FatSbBookingStepVertical_FE.s_min_multiple_slot = FatSbBookingStepVertical_FE.s_min_multiple_slot!='' && !isNaN(FatSbBookingStepVertical_FE.s_min_multiple_slot) ? parseInt(FatSbBookingStepVertical_FE.s_min_multiple_slot) : 1;
            FatSbBookingStepVertical_FE.s_max_multiple_slot = FatSbBookingStepVertical_FE.s_max_multiple_slot!='' && !isNaN(FatSbBookingStepVertical_FE.s_max_multiple_slot) ? parseInt(FatSbBookingStepVertical_FE.s_max_multiple_slot) : 1;

            if(FatSbBookingStepVertical_FE.s_multiple_days == 1){
                container.addClass('multiple-days');
            }else{
                FatSbBookingStepVertical_FE.multiple_days = [];
                container.removeClass('multiple-days');
            }

            FatSbBookingStepVertical_FE.s_id = self.attr('data-id');
            FatSbBookingStepVertical_FE.s_tax = parseFloat(self.attr('data-tax'));
            FatSbBookingStepVertical_FE.s_break_time = parseInt(self.attr('data-break-time'));
            FatSbBookingStepVertical_FE.s_name = self.attr('data-name');
            FatSbBookingStepVertical_FE.s_duration = parseInt(self.attr('data-duration'));

            // filter service extra
            $('.fat-sb-list-services-extra .fat-sb-item', container).addClass('fat-sb-hidden');
            for(var $i=0; $i< se_ids.length; $i++){
                $('.fat-sb-list-services-extra .fat-sb-item.se-' + se_ids[$i], container).removeClass('fat-sb-hidden');
            }
            if($('.fat-sb-list-services-extra .fat-sb-item:not(.fat-sb-hidden)', container).length == 0){
                FatSbBookingStepVertical_FE.addEmptyNotice($('.fat-sb-tab-content.services-extra .fat-sb-list-services-extra'));
            }else{
                $('.fat-sb-list-services-extra .fat-sb-not-found-wrap',container).remove();
            }

            // filter employee
            var employees = _.where(FatSbBookingStepVertical_FE.service_employee, {s_id: FatSbBookingStepVertical_FE.s_id});
            $('.fat-sb-list-employees .fat-sb-item', container).addClass('fat-sb-hidden');
            $('.fat-sb-tab-content.employees .fat-sb-not-found-wrap',container).remove();
            if(employees.length > 0){
                for(var $i=0; $i< employees.length; $i++){
                    $('.fat-sb-list-employees .fat-sb-item.emp-' + employees[$i].e_id + ' .price-capacity .price', container).html(FatSbMain_FE.data.symbol + employees[$i].s_price);
                    $('.fat-sb-list-employees .fat-sb-item.emp-' + employees[$i].e_id + '.loc-' + FatSbBookingStepVertical_FE.loc_id, container).removeClass('fat-sb-hidden');
                }
            }

            if($('.fat-sb-list-employees .fat-sb-item:not(.fat-sb-hidden)',container).length==0){
                FatSbBookingStepVertical_FE.addEmptyEmployeeNotice($('.fat-sb-tab-content.employees .fat-sb-list-employees'));
            }
        }

        FatSbBookingStepVertical_FE.resetNextItemStatus(tab, container);

        FatSbBookingStepVertical_FE.switchStep(self, container);
    };

    /*
    Reset Item selected and tab completed in case re-select item of previous tab
     */
    FatSbBookingStepVertical_FE.resetNextItemStatus = function(tab_name, container){
        if(tab_name=='location'){
            if($('.fat-sb-list-employees .fat-sb-item.fat-sb-hidden .fat-sb-item-inner.active',container).length>0){
                $('.fat-sb-list-employees .fat-sb-item.fat-sb-hidden .fat-sb-item-inner.active',container).removeClass('active');
                FatSbBookingStepVertical_FE.e_id = 0;
                $('.step[data-step="employee"]', container).removeClass('completed');
            }
            $('.step[data-step="date-time"]', container).removeClass('completed').addClass('disabled');

        }

        if(tab_name=='category'){
            if($('.fat-sb-list-services .fat-sb-item.fat-sb-hidden .fat-sb-item-inner.active',container).length>0){
                $('.fat-sb-list-services .fat-sb-item.fat-sb-hidden .fat-sb-item-inner.active',container).removeClass('active');
                $('.fat-sb-list-services-extra .fat-sb-item .fat-sb-item-inner.active',container).removeClass('active');
                $('.fat-sb-list-employees .fat-sb-item .fat-sb-item-inner.active',container).removeClass('active');
                FatSbBookingStepVertical_FE.s_id = 0;
                FatSbBookingStepVertical_FE.e_id = 0;
                FatSbBookingStepVertical_FE.services_extra = [];

                $('.step[data-step="service"]', container).removeClass('completed');
                $('.step[data-step="service-extra"]', container).removeClass('completed').addClass('disabled');
                $('.step[data-step="employee"]', container).removeClass('completed').addClass('disabled');
            }
        }

        if(tab_name=='service'){
            if($('.fat-sb-list-services-extra  .fat-sb-item.fat-sb-hidden .fat-sb-item-inner.active',container).length>0){
                FatSbBookingStepVertical_FE.services_extra = [];
                $('.step[data-step="service-extra"]', container).removeClass('completed').addClass('disabled');
                $('.fat-sb-list-services-extra .fat-sb-item .fat-sb-item-inner.active',container).removeClass('active');
            }

            if($('.fat-sb-list-employees .fat-sb-item.fat-sb-hidden .fat-sb-item-inner.active',container).length>0){
                FatSbBookingStepVertical_FE.e_id = 0;
                $('.step[data-step="employee"]', container).removeClass('completed').addClass('disabled');
                $('.fat-sb-list-employees .fat-sb-item .fat-sb-item-inner.active',container).removeClass('active');
            }

            $('.step[data-step="date-time"]', container).removeClass('completed').addClass('disabled');
            $('.step[data-step="information"]', container).removeClass('completed').addClass('disabled');
        }

        if(tab_name=='employee'){
            $('.step[data-step="order"]', container).addClass('disabled');
            $('.step[data-step="information"]', container).removeClass('completed').addClass('disabled');
        }

        if(tab_name=='calendar'){
            $('.step[data-step="location"]', container).addClass('disabled');
            $('.step[data-step="category"]', container).addClass('disabled');
            $('.step[data-step="service"]', container).addClass('disabled');
            $('.step[data-step="service-extra"]', container).addClass('disabled');
            $('.step[data-step="employee"]', container).addClass('disabled');
            $('.step[data-step="date-time"]', container).addClass('disabled');
            $('.step[data-step="information"]', container).addClass('disabled');
            $('.step[data-step="order"]', container).addClass('disabled');
        }

    };

    FatSbBookingStepVertical_FE.itemServiceExtraOnClick = function(self){
        var tab_content = self.closest('.fat-sb-tab-content');
        FatSbBookingStepVertical_FE.services_extra = [];
        self.toggleClass('active');
        $('.fat-sb-list-services-extra .fat-sb-item .fat-sb-item-inner.active').each(function(){
            FatSbBookingStepVertical_FE.services_extra.push({
                se_id : $(this).attr('data-id'),
                se_name: $(this).attr('data-name'),
                se_duration: $(this).attr('data-duration'),
                se_price: $(this).attr('data-price'),
                se_price_on_total : $(this).attr('data-price-on-total')
            });
        });
    };

    FatSbBookingStepVertical_FE.nextServiceExtra = function(self){
        FatSbBookingStepVertical_FE.switchStep(self, self.closest('.fat-booking-container'));
    };

    FatSbBookingStepVertical_FE.nextInformation = function(self){
        var container = self.closest('.fat-sb-step-vertical-layout'),
            form = $('.fat-sb-tab-content.information .ui.form', container);
        if (FatSbMain_FE.validateForm(form)) {
            FatSbBookingStepVertical_FE.switchStep(self, container);

            var number_of_person = $('#number_of_person', container).val();

            $('.fat-sb-order-service .fat-sb-label span', container).html(FatSbBookingStepVertical_FE.s_name);
            $('.fat-sb-order-service .fat-sb-value', container).html(number_of_person + ' x '  +  FatSbMain_FE.formatPrice(FatSbBookingStepVertical_FE.s_price));
            $('.fat-sb-order-location', container).html(FatSbBookingStepVertical_FE.loc_name);
            $('.fat-sb-order-employee', container).html(FatSbBookingStepVertical_FE.e_name);
            $('.fat-sb-order-date', container).html($('#b_date', container).attr('data-date-i18n'));
            $('.fat-sb-order-time', container).html(FatSbBookingStepVertical_FE.b_time_label);

            var s_extra = '';
            $('.fat-sb-order-service-extra', container).empty();
            $('.fat-sb-order-service-extra', container).show();

            if(FatSbBookingStepVertical_FE.services_extra.length>0){

                for(let $se of FatSbBookingStepVertical_FE.services_extra){
                    s_extra += s_extra !='' ? ', '  + $se.se_name : $se.se_name;
                    if($se.se_price_on_total == 1){
                        $('.fat-sb-order-service-extra', container).append(' <div class="service-extra-item">' +
                            '                                    <div class="fat-sb-label"><span>' + $se.se_name  + '</span></div>' +
                            '                                    <div class="fat-sb-value"> ' + FatSbMain_FE.formatPrice($se.se_price) +' </div>' +
                            '                                </div>');
                    }else{
                        $('.fat-sb-order-service-extra', container).append(' <div class="service-extra-item">' +
                            '                                    <div class="fat-sb-label"><span>' + $se.se_name  + '</span></div>' +
                            '                                    <div class="fat-sb-value"> ' + number_of_person + ' x '  +  FatSbMain_FE.formatPrice($se.se_price) +' </div>' +
                            '                                </div>');
                    }
                }

            }else{
                $('.fat-sb-order-service-extra', container).hide();
            }

            FatSbBookingStepVertical_FE.initTotal(container);

            $('.fat-sb-list-payment li:first-child .payment-item', container).trigger('click');
        }
    };

    FatSbBookingStepVertical_FE.employeeOnClick = function(self){
        var container = self.closest('.fat-booking-container'),
            item_wrap = self.closest('.fat-sb-item-wrap');

        $('.fat-sb-item-inner.active', item_wrap).removeClass('active');
        self.addClass('active');

        FatSbBookingStepVertical_FE.e_id = self.attr('data-id');
        FatSbBookingStepVertical_FE.e_name = self.attr('data-name');

        //get service price base on employee and service
        var se = _.findWhere(FatSbBookingStepVertical_FE.service_employee, {s_id: FatSbBookingStepVertical_FE.s_id.toString(), e_id: FatSbBookingStepVertical_FE.e_id.toString()});
        if(typeof se != 'undefined' && typeof se.s_price !='undefined'){
            FatSbBookingStepVertical_FE.s_price = parseFloat(se.s_price);
        }

        FatSbBookingStepVertical_FE.resetNextItemStatus('employee', container);
        FatSbBookingStepVertical_FE.switchStep(self, container);

        if(typeof $('.air-date-picker', container).data('datepicker')!='undefined'){
            $('#b_date', container).attr('data-date', $('#b_date', container).attr('data-default'));
            $('.air-date-picker', container).data('datepicker').destroy();
        }
        //air datetime
        var date_format = FatSbBookingStepVertical_FE.getDateFormat(),
            elmBookingDate = $('.air-date-picker', container),
            locale = elmBookingDate.attr('data-locale');
        locale = locale.split('_').length > 1 ? locale.split('_')[0] : locale;
        var option = {
            inline: true,
            language: locale,
            minDate: new Date(),
            dateFormat: date_format
        };
        elmBookingDate.datepicker(option);

        FatSbBookingStepVertical_FE.getTimeSlot(container,function(data){
            FatSbBookingStepVertical_FE.initTimeSlot(data);
            FatSbBookingStepVertical_FE.onChangeMonth(container);
        });
    };

    FatSbBookingStepVertical_FE.onChangeMonth = function(container){
        $('.datepicker--nav-action', container).off('click');
        $('.datepicker--nav-action', container).on('click',function(){
            FatSbMain_FE.showLoading($('.fat-sb-date-time-wrap', container));
            var date = moment($('#b_date').attr('data-date'), 'YYYY-MM-DD');
            if($(this).attr('data-action')=='next'){
                date.add(1,'M');
            }else{
                date.add(-1,'M');
            }
            date = date.year() + '-' + (date.month() + 1) + '-01';
            $('#b_date').attr('data-date', date);
            FatSbBookingStepVertical_FE.getTimeSlot(container,function(data){
                FatSbBookingStepVertical_FE.initTimeSlot(data);
                FatSbBookingStepVertical_FE.onChangeMonth(container);
            });
        });
    };

    FatSbBookingStepVertical_FE.stepClick = function(self){
        if(!self.hasClass('disabled')){
            var container = self.closest('.fat-booking-container'),
                tab_name = self.attr('data-step'),
                current_tab = $('.step.active', container).attr('data-step');

            $('.fat-sb-tab-content[data-tab="' + current_tab + '"]', container).fadeOut(500,function(){
                $('.step.active', container).removeClass('active');
                self.addClass('active');
                $('.fat-sb-tab-content[data-tab="' + tab_name + '"]', container).fadeIn();

                if(tab_name=='information'){
                    $('.step[data-step="order"]', container).addClass('disabled');
                }
            });
        }
    };

    FatSbBookingStepVertical_FE.timeClick = function(self){
        var container = self.closest('.fat-sb-step-vertical-layout'),
            available = self.attr('data-available');
        if(!self.hasClass('active')){
            $('.fat-sb-time-slot .slot-item-inner.active', container).removeClass('active');
            self.addClass('active');
            FatSbBookingStepVertical_FE.b_time = self.attr('data-slot');
            FatSbBookingStepVertical_FE.b_time_label = self.html();
        }

        var date = $('.air-date-picker').attr('data-date'),
            date_i18n = $('.air-date-picker').attr('data-date-i18n'),
            time = self.attr('data-slot'),
            time_label = self.html(),
            selected_day = _.find(FatSbBookingStepVertical_FE.multiple_days, function(day){
                return (day.date == date && day.time==time);
            });

        if(typeof selected_day=='undefined'){
            FatSbBookingStepVertical_FE.multiple_days.push({date: date, date_i18n: date_i18n, time: time, time_label: time_label, available: available});
        }

        FatSbBookingStepVertical_FE.setMaxQuantity(container);

        if(FatSbBookingStepVertical_FE.s_multiple_days==1){
            if(typeof selected_day=='undefined'){
                $('.fat-sb-multiple-days ul.list-multiple-days .notice').remove();

                $('.fat-sb-multiple-days ul.list-multiple-days').append('<li data-date="' + date +'" data-time="'+ time +'" class="">' + date_i18n + ' ' + time_label  + '<a href="javascript:;" class="remove-day"><i class="trash alternate outline icon"></i></a></li>');
                $('.fat-sb-tab-content.date-time .fat-sb-button-group').fadeIn(function(){
                    $('.fat-sb-tab-content.date-time .fat-sb-button-group').removeClass('fat-sb-hidden');
                });

                if(FatSbBookingStepVertical_FE.s_min_multiple_slot <= FatSbBookingStepVertical_FE.multiple_days.length){
                    $('.fat-sb-tab-content.date-time .fat-sb-button-group button', container).removeClass('disabled');
                    $('.fat-sb-multiple-days .notice', container).remove();
                }else{
                    $('.fat-sb-tab-content.date-time .fat-sb-button-group button', container).addClass('disabled');
                }

                //remove day
                $('.fat-sb-multiple-days a.remove-day').off('click').on('click',function(){
                    var self = $(this),
                        li = self.closest('li'),
                        item_date = li.attr('data-date'),
                        item_time = li.attr('data-time');

                    FatSbBookingStepVertical_FE.multiple_days = _.reject(FatSbBookingStepVertical_FE.multiple_days, function(day){
                        return (day.date == item_date && day.time==item_time);
                    });
                    li.remove();
                    if(FatSbBookingStepVertical_FE.multiple_days.length==0){
                        $('.fat-sb-tab-content.date-time .fat-sb-button-group').fadeOut(function(){
                            $('.fat-sb-tab-content.date-time .fat-sb-button-group').addClass('fat-sb-hidden');
                        });
                    }
                    FatSbBookingStepVertical_FE.setMaxQuantity(container);
                })
            }
        }else{
            FatSbBookingStepVertical_FE.switchStep(self, container);
        }

    };

    FatSbBookingStepVertical_FE.switchStep = function(self, container){
        var tab = self.closest('.fat-sb-tab-content'),
            tab_name = tab.attr('data-tab'),
            next_tab = '',
            next_tab_name = '';

        next_tab_name = tab_name =='location' ? 'category' : next_tab_name;
        next_tab_name = tab_name =='category' ? 'service' : next_tab_name;
        next_tab_name = tab_name =='service' ? 'service-extra' : next_tab_name;
        next_tab_name = tab_name =='service-extra' ? 'employee' : next_tab_name;
        next_tab_name = tab_name =='employee' ? 'date-time' : next_tab_name;
        next_tab_name = tab_name =='date-time' ? 'information' : next_tab_name;
        next_tab_name = tab_name =='information' ? 'order' : next_tab_name;
        next_tab_name = tab_name =='order' ? 'calendar' : next_tab_name;

        next_tab = $('.fat-sb-tab-content[data-tab="' + next_tab_name  +'"]', container);
        $(tab).fadeOut(500,function(){
            $('.step.active', container).addClass('completed').removeClass('active');
            $('.step[data-step="' + next_tab_name +'"]', container).addClass('active').removeClass('disabled');
            $(this).removeClass('active');

            if(next_tab_name=='service-extra' && container.hasClass('hide-employee')){
                $('.fat-sb-tab-content.services-extra button', container).trigger('click');
                return;
            }

            if(next_tab_name=='employee' && container.hasClass('hide-employee')){
                $('.fat-sb-list-employees .fat-sb-item-inner-wrap .fat-sb-item:first-child .fat-sb-item-inner',container).trigger('click');
                return;
            }

            $(next_tab).fadeIn(500);
        });
    };

    FatSbBookingStepVertical_FE.addEmptyNotice = function(tab_content){
        var notice = $('<div class="fat-sb-not-found-wrap"><i class="file outline icon"></i><div class="fat-sb-not-found-message">' + FatSbMain_FE.data.empty_service_extra + '</div></div>');
        $('.fat-sb-not-found-wrap',tab_content).remove();
        $(tab_content).append(notice);
    };

    FatSbBookingStepVertical_FE.getDateFormat = function () {
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

    FatSbBookingStepVertical_FE.getTimeSlot = function(container, callback){
        FatSbMain_FE.showLoading($('.fat-sb-date-time-wrap', container));
        try {
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_get_employee_time_slot_monthly',
                    s_field: FatSbMain_FE.data.ajax_s_field,
                    loc_id: FatSbBookingStepVertical_FE.loc_id,
                    s_id: FatSbBookingStepVertical_FE.s_id,
                    e_id: FatSbBookingStepVertical_FE.e_id,
                    date: $('#b_date').attr('data-date') // '2020-06-12'
                }),
                success: function (response) {
                    response = $.parseJSON(response);
                    FatSbMain_FE.closeLoading(container);

                    $('.fat-sb-time-slot',container).empty();
                    $('.fat-sb-time-slot',container).append('<div class="fat-sb-time-message">' + FatSbMain_FE.data.select_date_message + '</div>');
                    if(callback){
                        callback(response);
                    }
                },
                error: function (response) {
                    FatSbMain_FE.closeLoading(container);
                }
            });
        } catch (err) {
            FatSbMain_FE.closeLoading(container);
        }
    };

    FatSbBookingStepVertical_FE.initTimeSlot = function(data, container){
         $('.fat-sb-date .air-date-picker', container).datepicker({
            onRenderCell: function (date, cellType) {
                if (cellType == 'day') {
                    var date_str = FatSbBookingStepVertical_FE.getDateStr(date),
                        es_day = FatSbBookingStepVertical_FE.getESDay(date),
                        booking_in_day = _.where( data.booking, {b_date: date_str}),
                        booking_service_in_day = _.where( data.booking, {b_date: date_str, b_service_id: FatSbBookingStepVertical_FE.s_id.toString()}),
                        day = _.findWhere(data.days, {date: date_str}),
                        min_cap = data.min_cap,
                        max_cap = data.max_cap;

                    if(typeof day == 'undefined'){
                        return {
                            classes: 'none-time-slot',
                        };
                    }

                    if(day.work_hour.length == 0){
                        return {
                            disabled: true
                        };
                    }

                    var time_slot = [],
                        time = 0,
                        end_time = 0,
                        range = 0,
                        is_conflict = 0,
                        cap = max_cap,
                        time_step = parseInt(FatSbMain_FE.data.time_step),
                        now = FatSbMain_FE.parseDateTime(FatSbMain_FE.data.now),
                        now_minute = now.getHours()*60 + now.getMinutes();

                    FatSbBookingStepVertical_FE.s_id = parseInt(FatSbBookingStepVertical_FE.s_id);
                    for(let wh of day.work_hour){

                        wh.es_work_hour_end = parseInt(wh.es_work_hour_end);
                        wh.es_work_hour_start = parseInt(wh.es_work_hour_start);
                        range = (wh.es_work_hour_end - wh.es_work_hour_start) / time_step ;//FatSbBookingStepVertical_FE.s_duration;

                        for(var $i=0; $i < range; $i++){
                            time = wh.es_work_hour_start + $i*time_step;
                            end_time = time + FatSbBookingStepVertical_FE.s_duration + FatSbBookingStepVertical_FE.s_break_time;
                            cap = max_cap;
                            is_conflict = 0;
                            if(end_time > wh.es_work_hour_end){
                                break;
                            }

                            if(typeof booking_service_in_day !='undefined'){
                                for(let bk of booking_service_in_day){
                                    bk.b_time_end = parseInt(bk.b_time_end);
                                    bk.b_time = parseInt(bk.b_time);
                                    bk.b_loc_id = parseInt(bk.b_loc_id);
                                    bk.total_person = parseInt(bk.total_person);

                                    if(bk.b_time <= time && end_time <= bk.b_time_end){
                                        if(bk.b_loc_id == FatSbBookingStepVertical_FE.loc_id  && (max_cap - bk.total_person) > min_cap){
                                            is_conflict = 0;
                                            cap = max_cap - bk.total_person;
                                        }else{
                                            is_conflict = 1;
                                        }
                                        break;
                                    }
                                }
                            }

                            if(!is_conflict && typeof booking_in_day !='undefined'){
                                for(let bk of booking_in_day){
                                    if(bk.b_time <= time && end_time <= bk.b_time_end && bk.b_loc_id == FatSbBookingStepVertical_FE.loc_id && bk.b_service_id == FatSbBookingStepVertical_FE.s_id ){
                                        break;
                                    }else{
                                        is_conflict = !(end_time <= bk.b_time || time >= bk.b_time_end);
                                    }
                                    if(is_conflict){
                                        break;
                                    }
                                }
                            }

                            if(FatSbMain_FE.equalDay(now, date) && time <= now_minute){
                                is_conflict = 1;
                            }

                            if(!is_conflict){
                                time_slot.push({
                                    slot: time,
                                    available: cap
                                });
                            }
                        }
                    }

                    if(time_slot.length>0){
                        FatSbBookingStepVertical_FE.time_slot.push(
                            {date: date_str, time_slot: time_slot }
                        );
                        return {
                            classes: 'has-time-slot',
                            disabled: false
                        };
                    }else{
                        return {
                            classes: 'none-time-slot',
                            disabled: false
                        };
                    }
                }
            },
            onSelect: function (formattedDate, date, inst) {

                if (typeof date == 'undefined' || date == '') {
                    $('.fat-sb-time-slot',container).empty();
                    $('.fat-sb-time-slot',container).append('<div class="fat-sb-time-message">' + FatSbMain_FE.data.select_date_message + '</div>');
                    return;
                }
                var date_str = FatSbBookingStepVertical_FE.getDateStr(date),
                    dt_slot = _.findWhere(FatSbBookingStepVertical_FE.time_slot,{date: date_str});

                FatSbBookingStepVertical_FE.b_date = date_str;

                $('#b_date', container).attr('data-date',date_str);
                $('#b_date', container).attr('data-date-i18n', formattedDate);

                $('.fat-sb-time-slot',container).fadeOut(function(){
                    $(this).empty();
                    if(typeof dt_slot=='undefined' || dt_slot.length ==0){
                        //display not found message
                        $(this).append('<div class="fat-sb-time-message">' + FatSbMain_FE.data.empty_time_slot + '</div>');
                    }else{
                        for(let ts of dt_slot.time_slot){
                            $(this).append('<div class="fat-sb-time-slot-item"><div class="slot-item-inner" data-onClick="FatSbBookingStepVertical_FE.timeClick" data-slot="'+ ts.slot +'" data-available="'+ ts.available +'">'+
                                FatSbMain_FE.data.slots[ts.slot] + ' - ' + FatSbMain_FE.data.slots[ts.slot + FatSbBookingStepVertical_FE.s_duration] +'</div></div>');
                        }
                        FatSbMain_FE.registerOnClick( $('.fat-sb-time-slot',container));
                    }
                    $(this).fadeIn();
                });
            }
        });
    };

    FatSbBookingStepVertical_FE.getESDay = function (date) {
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

    FatSbBookingStepVertical_FE.getDateStr = function(date){
        var month = date.getMonth() + 1,
            day = date.getDate();
        month = parseInt(month);
        day = parseInt(day);
        month = month < 10 ? ('0' + month) : month;
        day = day < 10 ? ('0' + day) : day;
        return date.getFullYear() + '-' + month + '-' + day;
    };

    FatSbBookingStepVertical_FE.initTotal = function(container){
        var quantity = $('#number_of_person', container).val(),
            tax = 0, sub_total =0, 
            total = 0, 
            total_origin = 0,
            price_label = '', 
            discount =0, 
            price_base_quantity =0,  
            extra_price = 0, extra_tax = 0,
            total_days = FatSbBookingStepVertical_FE.multiple_days.length;

        total_days = total_days >0 ? total_days : 1;
        discount = $('#coupon', container).val() != '' ? $('.fat-sb-order-discount .fat-sb-value', container).attr('data-value') : 0;
        quantity = parseInt(quantity);

        if(FatSbBookingStepVertical_FE.s_price >= 0 && quantity >0){
            for(let ex of FatSbBookingStepVertical_FE.services_extra){
                if(ex.se_price_on_total == 1){
                    extra_price += parseFloat(ex.se_price);
                }else{
                    extra_price += (parseFloat(ex.se_price) * quantity);
                }

                ex.se_price = typeof ex.se_price=='undefined' || isNaN(ex.se_price) ? 0 : parseFloat(ex.se_price);
                ex.se_tax = typeof ex.se_tax=='undefined' || isNaN(ex.se_tax) ? 0 : parseFloat(ex.se_tax);
                extra_tax += ( ex.se_price *  ex.se_tax ) / 100;

            }

            price_base_quantity = FatSbMain_FE.calculatePrice(quantity, FatSbBookingStepVertical_FE.s_price, FatSbBookingStepVertical_FE.s_id);

            tax = price_base_quantity * FatSbBookingStepVertical_FE.s_tax / 100;
            tax = tax + extra_tax;
            sub_total = price_base_quantity + extra_price  + tax;
            sub_total = sub_total > 0 ? sub_total : 0;
            sub_total = sub_total * total_days;
            total_origin = sub_total;
            total = sub_total - discount;
            total = total > 0 ? total : 0;

            FatSbBookingStepVertical_FE.b_total = total;

            if(tax > 0){
                $('.fat-sb-order-tax', container).show();
                $('.fat-sb-order-tax .fat-sb-value', container).text(FatSbMain_FE.formatPrice(tax));
            }else{
                $('.fat-sb-order-tax', container).hide();
            }
            $('.fat-sb-order-discount .fat-sb-value', container).text(FatSbMain_FE.formatPrice(discount));
            $('.fat-sb-order-subtotal .fat-sb-value', container).text(FatSbMain_FE.formatPrice(sub_total));

            $('.fat-sb-order-total', container).attr('data-total-origin', total_origin);
            $('.fat-sb-order-total', container).attr('data-value', total).text(FatSbMain_FE.formatPrice(total));

            $('.fat-sb-tab-content.order', container).attr('data-price',FatSbBookingStepVertical_FE.s_price).attr('data-total',total);

        }

        //bind multiple dates
        var list_date =  $('.fat-sb-multiple-date-time-wrap .list-date-time');
        $('.date-time-item', list_date).remove();
        for(let day of FatSbBookingStepVertical_FE.multiple_days){
            list_date.append('<div class="date-time-item"><div class="date-label">' + day.date_i18n + '</div><div class="time-label">' + day.time_label +'</div>');
        }
    };

    FatSbBookingStepVertical_FE.getCoupon = function (self) {
        var container = self.closest('.fat-sb-step-vertical-layout'),
            coupon = $('#coupon', container).val(),
            s_id = FatSbBookingStepVertical_FE.s_id;
        if (s_id == '' || coupon == '') {
            var discount = 0;
            $('.fat-sb-order-discount .fat-sb-value', container).attr('data-value', discount);
            $('. fat-sb-order-discount .fat-sb-value', container).text(FatSbMain_FE.data.symbol_prefix + '0' + FatSbMain_FE.data.symbol_suffix);
            FatSbBookingStepVertical_FE.initTotal(container);
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

                FatSbBookingStepVertical_FE.initTotal(container);
            },
            error: function () {
                self.removeClass('loading');
            }
        })
    };

    FatSbBookingStepVertical_FE.couponOnChange = function (self) {
        if (self.val() == '') {
            var container = self.closest('.fat-sb-step-vertical-layout');
            $('.fat-coupon-error', container).html('');
            FatSbBookingStepVertical_FE.initTotal(container);
        }
    };

    FatSbBookingStepVertical_FE.paymentClick = function(self){
        var container = self.closest('.fat-sb-step-vertical-layout');

        FatSbBookingStepVertical_FE.payment_method = self.attr('data-payment');
        $('.payment-item.active', container).removeClass('active');

        if(FatSbBookingStepVertical_FE.payment_method=='stripe'){
            $('ul.fat-sb-list-payment', container).fadeOut(function(){
                $('.fat-sb-order-stripe', container).removeClass('fat-sb-hidden');
            });
        }else{
            $('.fat-sb-order-stripe', container).addClass('fat-sb-hidden');
        }
        self.addClass('active');
        $('.fat-sb-tab-content.order .fat-sb-button-group button.ui.button', container).removeClass('disabled');
    };

    FatSbBookingStepVertical_FE.confirmOrderClick = function(self){
        var container = self.closest('.fat-sb-step-vertical-layout'),
            form = $('.fat-sb-tab-content.information .ui.form', container),
            number_of_person = $('#number_of_person', container).val(),
            coupon = $('#coupon', container).val(),
            c_first_name = $('#c_first_name', container).val(),
            c_last_name = $('#c_last_name', container).val(),
            c_email = $('#c_email', container).val(),
            c_phone = $('#c_phone', container).val(),
            c_phone_code =  $('#phone_code', container).val(),
            note = $('#note', container).val(),
            services_extra = '',
            form_builder = {};

        for(let ex of FatSbBookingStepVertical_FE.services_extra){
            services_extra += services_extra!='' ? (',' + ex.se_id) : ex.se_id;
        }

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

        if(FatSbBookingStepVertical_FE.payment_method=='stripe' && FatSbBookingStepVertical_FE.b_total > 0){
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
                            b_service_id: FatSbBookingStepVertical_FE.s_id,
                            b_services_extra: services_extra,
                            b_loc_id: FatSbBookingStepVertical_FE.loc_id,
                            b_employee_id: FatSbBookingStepVertical_FE.e_id,
                            b_date: FatSbBookingStepVertical_FE.b_date,
                            b_time: FatSbBookingStepVertical_FE.b_time,
                            b_customer_number: number_of_person,
                            b_coupon_code: coupon,
                            b_gateway_type: FatSbBookingStepVertical_FE.payment_method,
                            c_first_name: c_first_name,
                            c_last_name: c_last_name,
                            c_email: c_email,
                            c_phone: c_phone,
                            c_phone_code: c_phone_code,
                            b_description: note,
                            multiple_days: FatSbBookingStepVertical_FE.multiple_days
                        }
                    }),
                    success: function (response) {
                        response = $.parseJSON(response);
                        if (response.result > 0) {

                            if(typeof response.redirect_url != 'undefined' && response.redirect_url != ''){
                                window.location.href = response.redirect_url;
                                return;
                            }

                            if(FatSbBookingStepVertical_FE.payment_method=='myPOS' && FatSbBookingStepVertical_FE.b > 0){
                                var form = $(response.form);
                                form.hide();
                                $('body').append(form);
                                $('form#ipcForm').submit();
                                return;
                            }

                            if (FatSbBookingStepVertical_FE.payment_method == 'onsite' || FatSbBookingStepVertical_FE.payment_method == 'price-package'
                                || FatSbBookingStepVertical_FE.payment_method == 'paypal' || FatSbBookingStepVertical_FE.b_total==0 ) {
                                FatSbBookingStepVertical_FE.switchStep(self, container);
                                FatSbBookingStepVertical_FE.resetNextItemStatus('calendar', container);

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
                            $('.fat-sb-tab-content.order .fat-sb-error-message',container).html(response.message).removeClass('fat-sb-hidden');
                        }
                    },
                    error: function (response) {
                        FatSbMain_FE.removeLoading(container, self);
                    }
                });
            } catch (err) {
            }
        }

    };

    FatSbBookingStepVertical_FE.addToICalendar = function(self){
        var container = self.closest('.fat-sb-step-vertical-layout'),
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

    FatSbBookingStepVertical_FE.addToGoogleCalendar = function(self){
        var container = self.closest('.fat-sb-step-vertical-layout'),
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

    FatSbBookingStepVertical_FE.initStripeCardInput = function () {
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
                            container = self.closest('.fat-booking-container'),
                            service_id = FatSbBookingStepVertical_FE.s_id,
                            services_extra = '',
                            employee_id = FatSbBookingStepVertical_FE.e_id,
                            loc_id = FatSbBookingStepVertical_FE.loc_id,
                            date = FatSbBookingStepVertical_FE.b_date,
                            time =  FatSbBookingStepVertical_FE.b_time,
                            number_of_person =$('#number_of_person', container).val(),
                            coupon = $('#coupon', container).val(),
                            payment_method = FatSbBookingStepVertical_FE.payment_method,
                            c_first_name = $('#c_first_name', container).val(),
                            c_last_name = $('#c_last_name', container).val(),
                            c_email = $('#c_email', container).val(),
                            c_phone = $('#c_phone', container).val(),
                            c_phone_code = $('#phone_code', container).val(),
                            note = $('#note', container).text();

                        for(let ex of FatSbBookingStepVertical_FE.services_extra){
                            services_extra += services_extra!='' ? (',' + ex.se_id) : ex.se_id;
                        }

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
                                    multiple_days: FatSbBookingStepVertical_FE.multiple_days
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
                                    FatSbBookingStepVertical_FE.switchStep(self, container);
                                    FatSbBookingStepVertical_FE.resetNextItemStatus('calendar', container);

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
        })
    };

    FatSbBookingStepVertical_FE.addEmptyEmployeeNotice = function(tab_content){
        var notice = $('<div class="fat-sb-not-found-wrap"><i class="file outline icon"></i><div class="fat-sb-not-found-message">' + FatSbMain_FE.data.empty_employee +'</div></div>');
        $('.fat-sb-not-found-wrap',tab_content).remove();
        $(tab_content).append(notice);
    };

    FatSbBookingStepVertical_FE.setMaxQuantity = function(container){
        var max = 0;
        for(let day of FatSbBookingStepVertical_FE.multiple_days){
            max = (max > day.available || max==0) ? day.available : max;
        }
        $('.fat-sb-tab-content.information #number_of_person', container).val(1);
        $('.fat-sb-tab-content.information #number_of_person', container).attr('data-max', max);
        FatSbMain_FE.initNumberField($('.fat-sb-tab-content.information', container));
    };

    FatSbBookingStepVertical_FE.nextToInformation = function(self){
        var container =  self.closest('.fat-booking-container'),
            elm_multiple_dates = $('.fat-sb-order-multiple-dates .fat-item-value',container);

        $(elm_multiple_dates).empty();
        for(let day of FatSbBookingStepVertical_FE.multiple_days){
            elm_multiple_dates.append('<div>'+ day.date_i18n + ' ' + day.time_label + '</div>');
        }
        FatSbBookingStepVertical_FE.switchStep(self, container);
    };

    FatSbBookingStepVertical_FE.goBackPayment = function(self){
        var content = self.closest('.fat-sb-content-inner');
        $('.fat-sb-order-stripe', content).fadeOut(function(){
            $('.fat-sb-list-payment', content).fadeIn();
        })
    }

    $(document).ready(function () {
        FatSbBookingStepVertical_FE.init();
        FatSbMain_FE.initFormBuilder();
    })
})(jQuery);