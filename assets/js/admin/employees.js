"use strict";
var FatSbEmployees = {
    keyword : null
};
(function ($) {
    FatSbEmployees.init = function () {
        FatSbMain.initField();
        FatSbEmployees.loadEmployees();
        FatSbMain.bindLocationDic($('.fat-checkbox-dropdown-wrap.fat-sb-locations-dic'));
        FatSbMain.bindServicesDicHierarchy($('.fat-checkbox-dropdown-wrap.fat-sb-services-dic'));
        FatSbMain.registerEventProcess($('.fat-sb-employees-container .toolbox-action-group'));

    };

    /*
    Process event
     */
    FatSbEmployees.nameOnKeyUp = function(self){
        var search_wrap = self.closest('.ui.input');
        if(self.val().length >=3 || self.val()==''){
            if(FatSbEmployees.keyword == self.val()){
                return;
            }
            FatSbEmployees.keyword = self.val();
            search_wrap.addClass('loading');
            FatSbEmployees.loadEmployees(function(){
                search_wrap.removeClass('loading');
                FatSbEmployees.keyword = null;
            });
            if(self.val().length >=3){
                search_wrap.addClass('active-search');
            }
            if(self.val() == ''){
                search_wrap.removeClass('active-search');
            }
        }
    };

    FatSbEmployees.closeSearchOnClick = function(self){
        var search_wrap = self.closest('.ui.ui-search');
        $('input',search_wrap).val('');
        $('input',search_wrap).trigger('keyup');
    };

    FatSbEmployees.sumoSearchOnChange = function(self){
        var sumoContainer = self.closest('.SumoSelect'),
            prev_value = self.attr('data-prev-value'),
            value = self.val();
        value = value != null ? value : '';

        if(value != prev_value){
            $('.ui.loader',sumoContainer).remove();
            sumoContainer.addClass('fat-loading');
            sumoContainer.append('<div class="ui active tiny inline loader"></div>');
            self.attr('data-prev-value', value);
            FatSbEmployees.loadEmployees(function(){
                $('.ui.loader',sumoContainer).remove();
                sumoContainer.removeClass('fat-loading');
            });
        }
    };

    FatSbEmployees.btAddWorkHourOnClick = function(self){
        var container = self.closest('.fat-sb-work-hour-wrap'),
            work_hour_item_wrap = $('.fat-sb-work-hour-item-wrap', container),
            template = wp.template('fat-sb-work-hour-template'),
            work_hour_item = $(template([]));

        $(work_hour_item_wrap).append(work_hour_item);

        $('.fat-bt-remove-work-hour').off('click').on('click', function () {
            $(this).closest('.fat-sb-work-hour-item').remove();
        });
        //FatSbEmployees.registerRemoveWorkHour();

        if ($.isFunction($.fn.SumoSelect)) {
            var assign_services = $('.assign-services', work_hour_item);
            $(assign_services).SumoSelect({
                search: true,
                placeholder: $(assign_services).attr('data-placeholder'),
                captionFormat: '{0} ' + $(assign_services).attr('data-caption-format'),
                captionFormatAllSelected: '{0} ' + $(assign_services).attr('data-caption-format')
            });
        }
        //init field
        $('.dropdown', work_hour_item).dropdown({
            'onShow': function () {
                FatSbEmployees.updateWorkHourBreakTimeItemStatus($(this));
            }
        });
        FatSbEmployees.initServiceSchedule();
    };

    FatSbEmployees.btAddBreakTimeOnClick = function(self){
        var work_hour_wrap = self.closest('.fat-sb-work-hour-wrap'),
            template = wp.template('fat-sb-break-time-template'),
            break_time = $(template([]));
        $('.fat-sb-break-time-item-wrap', work_hour_wrap).append(break_time);

        //init field
        $('.dropdown', break_time).dropdown({
            'onShow': function () {
                FatSbEmployees.updateWorkHourBreakTimeItemStatus($(this));
            }
        });

        $('.fat-bt-remove-break-time').off('click').on('click', function () {
            $(this).closest('.fat-sb-break-time-item').remove();
        });
    };

    FatSbEmployees.btAddDayOfOnClick = function(self){
        var day_off_wrap = self.closest('.fat-day-off-wrap');
        FatSbEmployees.addDayOffItem(day_off_wrap);
    };

    FatSbEmployees.cancelPopupToolTipOnClick = function(self){
        var popup_id = self.closest('.ui.popup').attr('data-popup-id');
        $('.ui.button[data-popup-id="' + popup_id + '"]').popup('hide');
    };

    FatSbEmployees.serviceCheckAllOnChange = function(self){
        var table = self.closest('table');
        $('input.check-item[type="checkbox"]', table).prop("checked", self.is(':checked'));
        $('input.check-item[type="checkbox"]', table).trigger('change');

        FatSbEmployees.initServiceSchedule();
    };

    FatSbEmployees.serviceCheckItemOnChange = function(self){
        var tr = self.closest('tr'),
            table = self.closest('table'),
            isUpdateCheckAll = true;

        if (self.is(':checked')) {
            $('.ui.input.service-price', tr).removeClass('disabled');
            $('.ui.input.service-capacity', tr).removeClass('disabled');
        } else {
            $('.ui.input.service-price', tr).addClass('disabled');
            $('.ui.input.service-capacity', tr).addClass('disabled');
        }
        $('input.check-item[type="checkbox"]', table).each(function () {
            if ($(this).is(':checked') != self.is(':checked') && self.is(':checked')) {
                isUpdateCheckAll = false;
            }
        });
        if (isUpdateCheckAll) {
            $('.table-check-all', table).prop("checked", self.is(':checked'));
        }

        FatSbEmployees.initServiceSchedule();
    };

    FatSbEmployees.addDayOffItem = function (day_off_wrap, name, start, end) {
        var template = wp.template('fat-sb-day-off-template'),
            dat_off_item = $(template([])),
            date_format =  FatSbMain.getDateFormat();

        if (typeof name != 'undefined' && name != null) {
            $('input[name="day_off_name"]', dat_off_item).val(name);
        }

        $('input[name="day_off_schedule"]', dat_off_item).attr('data-start', start);
        $('input[name="day_off_schedule"]', dat_off_item).attr('data-end', end);

        $('.fat-day-off-inner', day_off_wrap).append(dat_off_item);

        if ($.isFunction($.fn.daterangepicker)) {
            $('input.date-range-picker', dat_off_item).each(function () {
                var self = $(this),
                    opt = {
                        locale: {
                            format: date_format,
                            applyLabel: FatSbMain.data.apply_title,
                            cancelLabel: FatSbMain.data.cancel_title,
                            fromLabel: FatSbMain.data.from_title,
                            toLabel: FatSbMain.data.to_title,
                            daysOfWeek: FatSbMain.data.day_of_week,
                            monthNames: FatSbMain.data.month_name
                        }
                    };
                if (typeof start != 'undefined' && start != '') {
                    opt.startDate = moment(start, 'YYYY-MM-DD');
                }
                if (typeof end != 'undefined' && end != '') {
                    opt.endDate = moment(end, 'YYYY-MM-DD');
                }
                self.daterangepicker(opt, function (start, end, label) {
                    self.attr('data-start', start.format('YYYY-MM-DD'));
                    self.attr('data-end', end.format('YYYY-MM-DD'));
                });
            });

        }

        $('.fat-bt-remove-day-off').off('click').on('click', function () {
            $(this).closest('.fat-sb-day-off-item').remove();
        });
    };

    FatSbEmployees.loadEmployees = function (callback) {
        var e_name = $('#e_name').val(),
            loc_id = $('#e_location_ids').val(),
            s_id = $('#s_id').val();
        $('.fat-sb-list-employees > .column').remove();
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_employees',
                e_name: e_name,
                loc_id: loc_id,
                s_id: s_id
            }),
            success: function (employees) {
                employees = $.parseJSON(employees);
                var template = wp.template('fat-sb-employee-item-template'),
                    items = $(template(employees)),
                    elm_employees = $('.fat-sb-list-employees');

                $('> .column', elm_employees).remove();
                $('.fat-sb-not-found', elm_employees).remove();
                if (employees.length > 0) {
                    elm_employees.append(items);
                    FatSbMain.registerEventProcess($('.fat-sb-list-employees'));
                    $('.fat-item-bt-inline[data-title]','.fat-semantic-container').each(function(){
                        $(this).popup({
                            title : '',
                            content: $(this).attr('data-title'),
                            inline: true
                        });
                    });

                } else {
                    FatSbMain.showNotFoundMessage(elm_employees);
                }

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

    FatSbEmployees.showPopupEmployee = function (elm, callback) {
        var e_id = typeof elm.attr('data-id') != 'undefined' ? elm.attr('data-id') : 0,
            popup_title = typeof e_id != 'undefined' && e_id!='' && e_id > 0  ? FatSbMain.data.modal_title.edit_employee : '';
        popup_title = elm.hasClass('fat-sb-clone-employee') ? FatSbMain.data.modal_title.clone_employee : popup_title;

        FatSbMain.showProcess(elm);

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_employee_by_id',
                e_id: e_id
            }),
            success: function (response) {
                FatSbMain.closeProcess(elm);
                response = $.parseJSON(response);
                if( elm.hasClass('fat-sb-clone-employee')){
                    response.employee.e_id = '';
                }
                FatSbMain.showPopup('fat-sb-employees-template', popup_title, response, function () {
                    FatSbEmployees.initServiceSchedule();
                    FatSbMain.registerEventProcess($('.fat-sb-employee-form'));

                    FatSbMain.bindLocationDic($('.fat-sb-employee-form .fat-sb-location-dic'), function () {
                        if (typeof response.employee.e_location_ids != 'undefined' && response.employee.e_location_ids != '') {
                            var e_location_ids = response.employee.e_location_ids.split(',');
                            for (var $loc_index = 0; $loc_index < e_location_ids.length; $loc_index++) {
                                $('.fat-sb-location-dic').dropdown('refresh').dropdown('set selected', e_location_ids[$loc_index]);
                            }
                        }
                    });
                    // init service tab
                    if (typeof response.employee.e_services != 'undefined' && response.employee.e_services!=null && response.employee.e_services != '') {
                        var e_services = response.employee.e_services,
                            cb_service = '',
                            tr_service = '';
                        for (var $s_index = 0; $s_index < e_services.length; $s_index++) {
                            $('input[name="s_id"]', '.fat-sb-list-employees-services').each(function () {
                                cb_service = $(this);
                                if (cb_service.val() == e_services[$s_index].s_id) {
                                    cb_service.trigger('click');
                                    cb_service.prop('checked', true);
                                    tr_service = cb_service.closest('tr');
                                    $('input[name="s_price"]', tr_service).val(e_services[$s_index].s_price);
                                    $('input[name="s_min_cap"]', tr_service).val(e_services[$s_index].s_min_cap);
                                    $('input[name="s_max_cap"]', tr_service).val(e_services[$s_index].s_max_cap);
                                }
                            });
                        }
                    }

                    // init schedule tab
                    if (typeof response.employee.e_schedules != 'undefined' && response.employee.e_schedules!=null && response.employee.e_schedules != '') {
                        var e_schedules = response.employee.e_schedules,
                            e_break_times = response.employee.e_break_times,
                            schedule_id = '',
                            schedule_class = '',
                            es_day = '',
                            schedule_checkbox = '',
                            schedule_item = '',
                            work_hours = [],
                            s_id = '',
                            work_hour_item = '',
                            break_time_item = '';

                        for (var $es_index = 0; $es_index < e_schedules.length; $es_index++) {
                            es_day = e_schedules[$es_index].es_day;
                            switch (es_day) {
                                case "2": {
                                    schedule_id = 'schedule_monday';
                                    schedule_class = 'schedule-monday';
                                    break;
                                }
                                case "3": {
                                    schedule_id = 'schedule_tuesday';
                                    schedule_class = 'schedule-tuesday';
                                    break;
                                }
                                case "4": {
                                    schedule_id = 'schedule_wednesday';
                                    schedule_class = 'schedule-wednesday';
                                    break;
                                }
                                case "5": {
                                    schedule_id = 'schedule_thursday';
                                    schedule_class = 'schedule-thursday';
                                    break;
                                }
                                case "6": {
                                    schedule_id = 'schedule_friday';
                                    schedule_class = 'schedule-friday';
                                    break;
                                }
                                case "7": {
                                    schedule_id = 'schedule_saturday';
                                    schedule_class = 'schedule-saturday';
                                    break;
                                }
                                case "8": {
                                    schedule_id = 'schedule_sunday';
                                    schedule_class = 'schedule-sunday';
                                    break;
                                }
                            }

                            schedule_checkbox = $('input#' + schedule_id);
                            schedule_item = schedule_checkbox.closest('.schedule-item');
                            if (e_schedules[$es_index].es_enable == "1") {
                                schedule_checkbox.attr("checked", 'check');
                                $('.fat-sb-work-hour-wrap', schedule_item).removeClass('fat-sb-hidden').removeClass('fat-hidden');
                                work_hours = e_schedules[$es_index].work_hours;
                                if(typeof work_hours!='undefined' && work_hours!=null){
                                    for (var $wk_index = 0; $wk_index < work_hours.length; $wk_index++) {
                                        $('.fat-bt-add-work-hour', schedule_item).trigger('click');
                                        work_hour_item = $('.fat-sb-work-hour-item-wrap .fat-sb-work-hour-item:last-child', schedule_item);
                                        $('.fat-work-hour-start-dropdown', work_hour_item).dropdown('refresh').dropdown('set selected', work_hours[$wk_index].es_work_hour_start);
                                        $('.fat-work-hour-end-dropdown', work_hour_item).dropdown('refresh').dropdown('set selected', work_hours[$wk_index].es_work_hour_end);

                                        s_id = work_hours[$wk_index].s_id;
                                        if (typeof s_id != 'undefined' && s_id != '' && s_id != '0') {
                                            for (var $s_id_index = 0; $s_id_index < s_id.length; $s_id_index++) {
                                                $('select.assign-services option[value="' + s_id[$s_id_index] + '"]', work_hour_item).attr('selected', 'select');
                                            }
                                            $('select.assign-services', work_hour_item)[0].sumo.reload();
                                        }
                                    }
                                }
                                if(typeof e_break_times!='undefined' && e_break_times!=null){
                                    for (var $bt_index = 0; $bt_index < e_break_times.length; $bt_index++) {
                                        if (e_break_times[$bt_index].es_day == es_day) {
                                            $('.fat-bt-add-break-time', schedule_item).trigger('click');
                                            break_time_item = $('.fat-sb-break-time-item-wrap .fat-sb-break-time-item:last-child', schedule_item);
                                            $('.fat-break-time-start-dropdown', break_time_item).dropdown('refresh').dropdown('set selected', e_break_times[$bt_index].es_break_time_start);
                                            $('.fat-break-time-end-dropdown', break_time_item).dropdown('refresh').dropdown('set selected', e_break_times[$bt_index].es_break_time_end);
                                        }
                                    }
                                }
                            } else {
                                schedule_checkbox.removeAttr("checked");
                                $('.fat-sb-work-hour-wrap', schedule_item).addClass('fat-sb-hidden');
                            }
                        }
                    }

                    // init day off tab
                    if (typeof response.employee.e_day_off != 'undefined' && response.employee.e_day_off!=null && response.employee.e_day_off != '') {
                        var day_off = response.employee.e_day_off,
                            day_off_wrap = $('.fat-day-off-wrap', '.fat-sb-employee-form');
                        for (var $df_index = 0; $df_index < day_off.length; $df_index++) {
                            FatSbEmployees.addDayOffItem(day_off_wrap, day_off[$df_index].dof_name, day_off[$df_index].dof_start, day_off[$df_index].dof_end);
                        }
                    }

                    if(typeof callback=='function'){
                        callback();
                    }
                });
            },
            error: function () {
            }
        });
    };

    FatSbEmployees.initServiceSchedule = function () {
        var services = [],
            self;
        $('input[name="s_id"]', '.fat-sb-list-employees-services').each(function () {
            self = $(this);
            if (self.is(':checked')) {
                services.push({
                    s_id: self.val(),
                    s_name: self.attr('data-name')
                });
            }
        });

        $('select[name="assign-services"]').each(function () {
            self = $(this);
            for (var $i = 0; $i < services.length; $i++) {
                if ($('option[value="' + services[$i].s_id + '"]', self).length == 0) {
                    self.append('<option value="' + services[$i].s_id + '">' + services[$i].s_name + '</option>');
                }
            }
            var is_exist = false;
            $('option', self).each(function () {
                is_exist = false;
                for (var $i = 0; $i < services.length; $i++) {
                    if (services[$i].s_id == $(this).val()) {
                        is_exist = true;
                    }
                }
                if (!is_exist) {
                    $(this).remove();
                }
            });
            $(self)[0].sumo.reload();
        });
    };

    FatSbEmployees.processSubmitEmployee = function (self) {
        if (FatSbMain.isFormValid) {
            var form = $('.fat-sb-employee-form'),
                image_url = $('#e_avatar_id img').attr('src'),
                data = {
                    employee: {},
                    schedules: [],
                    break_times: [],
                    day_off: [],
                    services: []
                };
            image_url = typeof image_url!='undefined' ? image_url : '';

            if (typeof self.attr('data-id') != 'undefined' && self.attr('data-id') != '') {
                data.employee.e_id = self.attr('data-id');
            } else {
                data.employee.e_id = '';
                data.employee.e_enable = 1;
            }

            data.employee.e_avatar_id = $('#e_avatar_id').attr('data-image-id');
            data.employee.e_first_name = $('#e_first_name', form).val();
            data.employee.e_last_name = $('#e_last_name', form).val();
            data.employee.e_email = $('#e_email', form).val();
            data.employee.e_phone = $('#e_phone', form).val();
            data.employee.e_location_ids = $('#e_location_ids', form).val();
            data.employee.e_description = $('#e_description', form).val();

            var tr = '';
            $('.fat-sb-list-employees-services input[name="s_id"]', form).each(function () {
                if ($(this).is(':checked')) {
                    tr = $(this).closest('tr');
                    data.services.push({
                        s_id: $(this).val(),
                        s_price: $('input[name="s_price"]', tr).val(),
                        s_min_cap: $('input[name="s_min_cap"]', tr).val(),
                        s_max_cap: $('input[name="s_max_cap"]', tr).val(),
                    });
                }
            });

            var schedules = [
                    {'id': 'schedule_monday', 'class': 'schedule-monday', 'day': 2},
                    {'id': 'schedule_tuesday', 'class': 'schedule-tuesday', 'day': 3},
                    {'id': 'schedule_wednesday', 'class': 'schedule-wednesday', 'day': 4},
                    {'id': 'schedule_thursday', 'class': 'schedule-thursday', 'day': 5},
                    {'id': 'schedule_friday', 'class': 'schedule-friday', 'day': 6},
                    {'id': 'schedule_saturday', 'class': 'schedule-saturday', 'day': 7},
                    {'id': 'schedule_sunday', 'class': 'schedule-sunday', 'day': 8}
                ],
                schedule_id = '',
                schedule_class = '',
                day = 0,
                work_hour_item_wrap = '',
                work_hour_item = '',
                work_hours = [],
                work_hour_start = '',
                work_hour_end = '',
                s_id = '',
                break_times = [],
                break_time_item_wrap = '',
                break_time_item = '',
                break_time_start = '',
                break_time_end = '';

            data.employee.e_schedules = [];
            data.employee.e_break_times = [];
            for (var $i = 0; $i < schedules.length; $i++) {
                schedule_id = schedules[$i].id;
                schedule_class = schedules[$i].class;
                day = schedules[$i].day;
                if ($('input#' + schedule_id).is(':checked')) {
                    work_hour_item_wrap = $('.fat-sb-work-hour-item-wrap', '.fat-sb-work-hour-wrap.' + schedule_class);
                    break_time_item_wrap = $('.fat-sb-break-time-item-wrap', '.fat-sb-work-hour-wrap.' + schedule_class);
                    work_hours = [];
                    $('.fat-sb-work-hour-item', work_hour_item_wrap).each(function () {
                        work_hour_item = $(this);
                        work_hour_start = $('input[name="work_hour_start"]', work_hour_item).val();
                        work_hour_end = $('input[name="work_hour_end"]', work_hour_item).val();
                        s_id = $('select[name="assign-services"]', work_hour_item).val();
                        if (work_hour_start != '' && work_hour_end != '') {
                            if(s_id == null || typeof s_id == 'undefined'){
                                work_hours.push({
                                    es_work_hour_start: work_hour_start,
                                    es_work_hour_end: work_hour_end
                                });
                            }else{
                                work_hours.push({
                                    es_work_hour_start: work_hour_start,
                                    es_work_hour_end: work_hour_end,
                                    s_id:  s_id
                                });
                            }
                        }
                    });

                    data.employee.e_schedules.push({
                        es_day: day,
                        es_enable: 1,
                        work_hours: work_hours
                    });

                    $('.fat-sb-break-time-item', break_time_item_wrap).each(function () {
                        break_time_item = $(this);
                        break_time_start = $('input[name="break_time_start"]', break_time_item).val();
                        break_time_end = $('input[name="break_time_end"]', break_time_item).val();
                        if (break_time_start != '' && break_time_end != '') {
                            data.employee.e_break_times.push({
                                es_day: day,
                                es_break_time_start: break_time_start,
                                es_break_time_end: break_time_end
                            });

                            data.break_times.push({
                                es_day: day,
                                es_break_time_start: break_time_start,
                                es_break_time_end: break_time_end
                            });
                        }
                    });

                } else {
                    data.employee.e_schedules.push({
                        es_day: day,
                        es_enable: 0
                    });
                }
            }
            data.employee.e_day_off = [];
            var day_of_item = '',
                day_of_schedule = '';
            $('.fat-day-off-wrap .fat-sb-day-off-item', form).each(function () {
                day_of_item = $(this);
                day_of_schedule = $('input[name="day_off_schedule"]', day_of_item);
                data.employee.e_day_off.push({
                    dof_name: $('input[name="day_off_name"]', day_of_item).val(),
                    dof_start: typeof day_of_schedule.attr('data-start') != 'undefined' ? day_of_schedule.attr('data-start') : '',
                    dof_end: typeof day_of_schedule.attr('data-end') != 'undefined' ? day_of_schedule.attr('data-end') : '',
                });
                data.day_off.push({
                    dof_name: $('input[name="day_off_name"]', day_of_item).val(),
                    dof_start: typeof day_of_schedule.attr('data-start') != 'undefined' ? day_of_schedule.attr('data-start') : '',
                    dof_end: typeof day_of_schedule.attr('data-end') != 'undefined' ? day_of_schedule.attr('data-end') : '',
                });
            });
            FatSbMain.showProcess(self);
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_save_employee',
                    data: data
                }),
                success: function (response) {
                    try {
                        response = $.parseJSON(response);
                        FatSbMain.closeProcess(self);
                        if (response.result >= 0) {
                            self.closest('.ui.modal').modal('hide');
                            FatSbMain.showMessage(self.attr('data-success-message'));
                            $('.fat-sb-list-employees .fat-sb-not-found').remove();

                            //update back to list
                            var item = $('.fat-sb-list-employees > .column[data-e-id="' + data.employee.e_id + '"]');
                            if (typeof item != 'undefined' && item.length > 0) {
                                $('.full-name', item).html(data.employee.e_first_name + ' ' + data.employee.e_last_name);
                                $('.email', item).html(data.employee.e_email);
                                $('.phone', item).html(data.employee.e_phone);
                                if($('span.fat-no-thumb', item).length > 0 && image_url!=''){
                                    $('span.fat-no-thumb', item).remove();
                                    $('.image', item).append(' <img class="fat-border-round fat-box-shadow fat-img-150"></img>');
                                }
                                $('.image img', item).attr('src', image_url);
                            } else {
                                data.employee.e_id = response.result;
                                data.employee.e_enable = 1;
                                data.employee.e_avatar_url = image_url;
                                var template = wp.template('fat-sb-employee-item-template'),
                                    item = $(template([data.employee]));
                                $('.image img', item).attr('src', image_url);
                                $('.fat-sb-list-employees').append(item);

                                FatSbMain.registerEventProcess(item);

                                $('.fat-item-bt-inline[data-title]',item).each(function(){
                                    $(this).popup({
                                        title : '',
                                        content: $(this).attr('data-title'),
                                        inline: true
                                    });
                                });
                            }
                        } else {
                            if(typeof response.message!='undefined'){
                                FatSbMain.showMessage(response.message, 3);
                            }else{
                                FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                            }
                        }
                    } catch (err) {
                        FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                    }
                },
                error: function () {
                    FatSbMain.closeProcess(self);
                    FatSbMain.showMessage(FatSbMain.data.error_message);
                }
            });
        }
    };

    FatSbEmployees.updateWorkHourBreakTimeItemStatus = function (elm) {
        var work_hours = [],
            schedule_item = $(elm).closest('.schedule-item'),
            work_hour_item = $(elm).closest('.fat-sb-work-hour-item'),
            work_hour_wrap = $('.fat-sb-work-hour-item-wrap', schedule_item),
            break_time_item = $(elm).closest('.fat-sb-break-time-item'),
            break_time_wrap = $('.fat-sb-break-time-item-wrap', schedule_item),
            current_item_index = $('.fat-sb-work-hour-item', work_hour_wrap).index(work_hour_item),
            current_break_time_item_index = $('.fat-sb-break-time-item', break_time_wrap).index(break_time_item),
            current_time_start = $('input[name="work_hour_start"]', work_hour_item).val(),
            current_break_time_start = $('input[name="break_time_start"]', break_time_item).val(),
            start = '',
            end = '',
            index = 0,
            self = '';

        $('.fat-sb-work-hour-item', work_hour_wrap).each(function () {
            self = $(this);
            if (index != current_item_index) {
                start = $('input[name="work_hour_start"]', self).val();
                end = $('input[name="work_hour_end"]', self).val();
                if (start != '' && end != '') {
                    work_hours.push({
                        'start': parseInt(start),
                        'end': parseInt(end),
                        'type': 1 //work hour
                    });
                }
            }
            index++;
        });

        index = 0;
        $('.fat-sb-break-time-item', break_time_wrap).each(function () {
            self = $(this);
            if (index != current_break_time_item_index) {
                start = $('input[name="break_time_start"]', self).val();
                end = $('input[name="break_time_end"]', self).val();
                if (start != '' && end != '') {
                    work_hours.push({
                        'start': parseInt(start),
                        'end': parseInt(end),
                        'type': 2 // break time
                    });
                }
            }
            index++;
        });

        if (work_hours.length > 0) {
            $('.fat-time-dropdown .menu', work_hour_item).each(function () {
                var self = $(this);
                $('.item', self).removeClass('disabled');
                /*$('.item', self).each(function () {
                    var time = $(this).attr('data-value'),
                        time = parseInt(time);
                    for (var $i = 0; $i < work_hours.length; $i++) {
                        if (work_hours[$i].start <= time && time <= work_hours[$i].end) {
                            $(this).addClass('disabled');
                            break;
                        }
                        if ($(elm).hasClass('fat-break-time-end-dropdown') && typeof current_break_time_start != 'undefined' && current_break_time_start != null &&
                            time <= current_break_time_start) {
                            $(this).addClass('disabled');
                            break;
                        }
                    }
                });*/
            });

            $('.fat-time-dropdown .menu', break_time_item).each(function () {
                var self = $(this);
                $('.item', self).addClass('disabled');
                $('.item', self).each(function () {
                    var time = $(this).attr('data-value'),
                        time = parseInt(time);
                    for (var $i = 0; $i < work_hours.length; $i++) {
                        if (work_hours[$i].type == 1) {
                            if (work_hours[$i].start < time && time < work_hours[$i].end) {
                                $(this).removeClass('disabled');
                                break;
                            }
                        } else {
                            if (work_hours[$i].start <= time && time < work_hours[$i].end) {
                                $(this).removeClass('disabled');
                                break;
                            }
                        }
                    }
                });
            });
        }

    };

    FatSbEmployees.processCloneSchedule = function (self) {
        var btApplies = self;
        btApplies.addClass('loading');

        setTimeout(function () {
            var item_wrap = btApplies.closest('.schedule-item'),
                popup_clone = $('.fat-popup-work-hour-clone', item_wrap),
                clone_to = [];
            $('input[type="checkbox"]', popup_clone).each(function () {
                if ($(this).is(':checked')) {
                    clone_to.push($(this).val());
                }
            });
            if (clone_to.length > 0) {
                var work_hours = [],
                    break_times = [],
                    start = '',
                    end = '',
                    self = '',
                    s_id = '';
                $('.fat-sb-work-hour-item-wrap .fat-sb-work-hour-item', item_wrap).each(function () {
                    self = $(this);
                    start = $('input[name="work_hour_start"]', self).val();
                    end = $('input[name="work_hour_end"]', self).val();
                    s_id = $('select[name="assign-services"]', self).val();
                    if (start != '' || end != '') {
                        work_hours.push({
                            start: start,
                            end: end,
                            s_id: s_id
                        });
                    }
                });

                $('.fat-sb-break-time-item-wrap .fat-sb-break-time-item', item_wrap).each(function () {
                    self = $(this);
                    start = $('input[name="break_time_start"]', self).val();
                    end = $('input[name="break_time_end"]', self).val();
                    if (start != '' || end != '') {
                        break_times.push({
                            start: start,
                            end: end
                        });
                    }
                });

                var schedule_check = '',
                    schedule_item_wrap = '';
                for (var $i = 0; $i < clone_to.length; $i++) {
                    schedule_check = $('#' + clone_to[$i]);
                    if (typeof schedule_check != 'undefined' && schedule_check.length > 0) {
                        schedule_item_wrap = schedule_check.closest('.schedule-item');
                        schedule_check.prop('checked', true);
                        $('.fat-sb-work-hour-wrap', schedule_item_wrap).removeClass('fat-sb-hidden').removeClass('fat-hidden');
                        $('.fat-sb-work-hour-item', schedule_item_wrap).remove();
                        $('.fat-sb-break-time-item', schedule_item_wrap).remove();
                        var new_item = '';
                        if (work_hours != null) {
                            for (var $j = 0; $j < work_hours.length; $j++) {
                                $('button.fat-bt-add-work-hour', schedule_item_wrap).trigger('click');
                                new_item = $('.fat-sb-work-hour-item-wrap .fat-sb-work-hour-item:last-child', schedule_item_wrap);
                                $('.fat-work-hour-start-dropdown', new_item).dropdown('set selected', work_hours[$j].start);
                                $('.fat-work-hour-end-dropdown', new_item).dropdown('set selected', work_hours[$j].end);
                                if (work_hours[$j].s_id != null) {
                                    for (var $k = 0; $k < work_hours[$j].s_id.length; $k++) {
                                        $('select', new_item)[0].sumo.selectItem(work_hours[$j].s_id[$k]);
                                    }
                                }
                            }
                        }

                        //clone break time
                        if (break_times != null) {
                            for (var $j = 0; $j < break_times.length; $j++) {
                                $('button.fat-bt-add-break-time', schedule_item_wrap).trigger('click');
                                new_item = $('.fat-sb-break-time-item-wrap .fat-sb-break-time-item:last-child', schedule_item_wrap);
                                $('.fat-break-time-start-dropdown', new_item).dropdown('set selected', break_times[$j].start);
                                $('.fat-break-time-end-dropdown', new_item).dropdown('set selected', break_times[$j].end);
                            }
                        }
                    }
                }

                btApplies.removeClass('loading');
                btApplies.closest('.fat-popup-work-hour-clone').popup('hide');
            }
        }, 100);
    };

    FatSbEmployees.processEnableEmployee = function (self) {
        var bt = self,
            e_id = bt.attr('data-id'),
            e_enable = bt.attr('data-enable') == '1' ? 0 : 1,
            popup_id = bt.closest('.ui.popup').attr('data-popup-id'),
            button = $('.ui.button[data-popup-id="' + popup_id + '"]');
        if (button.length > 0) {
            var loading = button.attr('data-loading-color');
            loading = typeof loading != 'undefined' ? 'loading ' + loading : 'loading';
            button.popup('hide');
            button.addClass(loading);

            if (typeof e_id != 'undefined' && e_id > 0) {
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_enable_employee',
                        e_id: e_id,
                        e_enable: e_enable
                    }),
                    success: function (response) {
                        try {
                            response = $.parseJSON(response);
                            button.removeClass(loading);
                            if (response.result > 0) {
                                bt.closest('.ui.modal').modal('hide');
                                FatSbMain.showMessage(response.message);
                                var item = $('.fat-sb-list-employees > .column[data-e-id="' + e_id + '"]');
                                if (typeof item != 'undefined' && item.length > 0) {
                                    if (e_enable == 1) {
                                        $('.enable-status', item).removeClass('disable');
                                        $('.enable-status', item).addClass('enable');
                                        $('.enable-status i', item).removeClass('slash outline');
                                    } else {
                                        $('.enable-status', item).removeClass('enable');
                                        $('.enable-status', item).addClass('disable');
                                        $('.enable-status i', item).addClass('slash outline');
                                    }
                                }
                            } else {
                                FatSbMain.showMessage(response.message, 2);
                            }
                        } catch (err) {
                            FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        }
                    },
                    error: function () {
                        button.removeClass(loading);
                        FatSbMain.showMessage(FatSbMain.data.error_message);
                    }
                });
            }
        }
    };

    FatSbEmployees.processDeleteEmployee = function (self) {
        var e_id = self.attr('data-id');
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title, FatSbMain.data.confirm_delete_message,function(result, popup){
            if(result==1){
                var self = $('.fat-sb-bt-confirm.yes',popup);
                FatSbMain.showProcess(self);
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_employee',
                        e_id: e_id
                    }),
                    success: function (response) {
                        try{
                            self.closest('.ui.modal').modal('hide');
                            FatSbMain.closeProcess(self);
                            response = $.parseJSON(response);
                            if(response.result>0){
                                $('.fat-sb-list-employees > .column[data-e-id="'+ e_id +'"]').remove();
                                if($('.fat-sb-list-employees > .column').length==0){
                                    FatSbMain.showNotFoundMessage($('.fat-sb-list-employees'));
                                }
                            }else{
                                FatSbMain.showMessage(response.message, 2);
                            }
                        }catch(err){
                            FatSbMain.closeProcess(self);
                            FatSbMain.showMessage(FatSbMain.data.error_message, 1);
                        }
                    },
                    error: function () {
                        FatSbMain.closeProcess(self);
                        FatSbMain.showMessage(FatSbMain.data.error_message);
                    }
                });
            }
        });
    };

    FatSbEmployees.processPopupDeleteEmployee = function (self) {
        var bt = self,
            e_id = bt.attr('data-id'),
            popup_id = bt.closest('.ui.popup').attr('data-popup-id'),
            button = $('.ui.button[data-popup-id="' + popup_id + '"]');
        if (button.length > 0) {
            var loading = button.attr('data-loading-color');
            loading = typeof loading != 'undefined' ? 'loading ' + loading : 'loading';
            button.popup('hide');
            button.addClass(loading);

            if (typeof e_id != 'undefined' && e_id > 0) {
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_employee',
                        e_id: e_id
                    }),
                    success: function (response) {
                        try {
                            response = $.parseJSON(response);
                            button.removeClass(loading);
                            if (response.result > 0) {
                                bt.closest('.ui.modal').modal('hide');
                                FatSbMain.showMessage(response.message);
                                $('.fat-sb-list-employees > .column[data-e-id="'+ e_id +'"]').remove();
                                if($('.fat-sb-list-employees > .column').length==0){
                                    FatSbMain.showNotFoundMessage($('.fat-sb-list-employees'));
                                }
                            } else {
                                FatSbMain.showMessage(response.message, 2);
                            }
                        } catch (err) {
                            FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        }
                    },
                    error: function () {
                        button.removeClass(loading);
                        FatSbMain.showMessage(FatSbMain.data.error_message);
                    }
                });
            }
        }
    };

    FatSbEmployees.processCloneEmployee = function(self){
        FatSbEmployees.showPopupEmployee(self, function () {
            $('.fat-sb-employee-form .fat-bt-submit-employee').attr('data-id','');
        });
    };

    $(document).ready(function () {
        FatSbEmployees.init();
    });
})(jQuery);