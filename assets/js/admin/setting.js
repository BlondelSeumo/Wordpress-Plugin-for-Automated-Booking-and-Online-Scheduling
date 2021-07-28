"use strict";
var FatSbSetting = {};
(function ($) {
    FatSbSetting.init = function () {
        FatSbMain.registerEventProcess($('.fat-sb-settings-container'));
        FatSbMain.initPopupToolTip();
    };

    FatSbSetting.initDepend = function () {
        var elms = [],
            depend_id = '';
        $('[data-depend]', '.fat-setting-modal').each(function () {
            depend_id = $(this).attr('data-depend');
            if (elms.indexOf(depend_id) == -1) {
                elms.push(depend_id);
            }
        });

        for (var $i = 0; $i < elms.length; $i++) {
            FatSbSetting.dependFieldOnChange($('#' + elms[$i]));
        }

        $('a.fat-show-send-mail').off('click').on('click', function () {
            var self = $(this);
            self.toggleClass('opened');
            $('.fat-test-send-mail-wrap').slideToggle();
            if (self.hasClass('opened')) {
                self.text(self.attr('data-close'));
                $('#send_to').focus();
            } else {
                self.text(self.attr('data-open'));
            }
        });

        $('a.fat-show-send-sms').off('click').on('click', function () {
            var self = $(this);
            self.toggleClass('opened');
            $('.fat-test-send-sms-wrap').slideToggle();
            if (self.hasClass('opened')) {
                self.text(self.attr('data-close'));
                $('#sms_phone_number').focus();
            } else {
                self.text(self.attr('data-open'));
            }
        });

        $('.fat-sb-ace-editor').each(function(){
            if(typeof ace !='undefined'){
                var $mode = "ace/mode/" + $(this).attr('data-mode'),
                    $id = $(this).attr('id'),
                    $ace = ace.edit($id),
                    $content = $('#hidden_' + $id).html();

                $ace.getSession().setMode($mode);
                $ace.getSession().setValue($content);
                $ace.setAutoScrollEditorIntoView(true);
                $ace.getSession().on('change', function(e) {
                    var $container = $($ace.container).closest('.ace-container');
                    $('textarea', $container).html($ace.getValue());
                });
            }
        });
    };

    FatSbSetting.sendMailOnClick = function (self) {
        var send_to = $('#send_to').val(),
            pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
        if (send_to != '' && pattern.test(send_to)) {
            self.addClass('loading');
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_test_send_mail',
                    send_to: send_to
                }),
                success: function (response) {
                    self.removeClass('loading');
                    response = $.parseJSON(response);
                    if (response.result > 0) {
                        FatSbMain.showMessage(response.message);
                    } else {
                        FatSbMain.showMessage(response.message, 2);
                    }
                },
                error: function () {
                    self.removeClass('loading');
                }
            })
        } else {
            FatSbMain.showMessage(self.attr('data-invalid-message'), 2);
        }
    };

    FatSbSetting.sendSMSOnClick = function (self) {
        var phone_number = $('#sms_phone_number').val();
        if (phone_number != '') {
            self.addClass('loading');
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_test_send_sms',
                    phone_number: phone_number
                }),
                success: function (response) {
                    self.removeClass('loading');
                    response = $.parseJSON(response);
                    if (response.result > 0) {
                        FatSbMain.showMessage(response.message);
                    } else {
                        FatSbMain.showMessage(response.message, 2);
                    }
                },
                error: function () {
                    self.removeClass('loading');
                }
            })
        } else {
            FatSbMain.showMessage(self.attr('data-invalid-message'), 2);
        }
    };

    FatSbSetting.itemOnClick = function (self) {
        if ($('.ui.dimmer', '.fat-sb-settings-container .items').length > 0) {
            return;
        }

        var template = self.attr('data-template'),
            action = template == 'fat-sb-setting-working-hour-template' ? 'fat_sb_get_working_hour_setting' : 'fat_sb_get_setting';
        if( template == 'fat-sb-setting-working-hour-template'){
            action = 'fat_sb_get_working_hour_setting';
        }else if(template == 'fat-sb-setting-user-role-template'){
            action = 'fat_sb_get_user_role_setting';
        }else{
            action = 'fat_sb_get_setting';
        }

        self.append($(' <div class="ui active dimmer"><div class="ui small loader"></div></div>'));
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: action
            }),
            success: function (response) {
                $('.ui.dimmer', self).remove();
                response = $.parseJSON(response);

                FatSbMain.showPopup(template, '', response, function () {
                    FatSbMain.initField();
                    FatSbSetting.initDepend();
                    FatSbMain.registerEventProcess($('.fat-setting-modal'));

                    //init schedules
                    if (typeof response.schedules != 'undefined' && response.schedules != null && response.schedules != '') {
                        var schedules = response.schedules,
                            break_times = [],
                            schedule_id = '',
                            schedule_class = '',
                            es_day = '',
                            schedule_checkbox = '',
                            schedule_item = '',
                            work_hours = [],
                            bt_add_work_hour = '',
                            bt_add_break_time = '';

                        for (var $es_index = 0; $es_index < schedules.length; $es_index++) {
                            es_day = schedules[$es_index].es_day;
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
                            bt_add_work_hour = $('.fat-bt-add-work-hour', schedule_item);

                            if (schedules[$es_index].es_enable == "1") {
                                schedule_checkbox.attr("checked", 'check');
                                work_hours = schedules[$es_index].work_hours;
                                $('.fat-sb-work-hour-wrap', schedule_item).removeClass('fat-sb-hidden').removeClass('fat-hidden');
                                if (typeof work_hours != 'undefined' && work_hours != null) {
                                    for (var $wk_index = 0; $wk_index < work_hours.length; $wk_index++) {
                                        bt_add_work_hour.trigger('click');
                                        $('.fat-sb-work-hour-item-wrap .fat-sb-work-hour-item:last-child .fat-work-hour-start-dropdown', schedule_item).dropdown('refresh').dropdown('set selected', work_hours[$wk_index].es_work_hour_start);
                                        $('.fat-sb-work-hour-item-wrap .fat-sb-work-hour-item:last-child .fat-work-hour-end-dropdown', schedule_item).dropdown('refresh').dropdown('set selected', work_hours[$wk_index].es_work_hour_end);
                                    }
                                }

                                break_times = schedules[$es_index].break_times;
                                bt_add_break_time = $('.fat-bt-add-break-time', schedule_item);
                                if (typeof break_times != 'undefined' && break_times != null) {
                                    for (var $bt_index = 0; $bt_index < break_times.length; $bt_index++) {
                                        bt_add_break_time.trigger('click');
                                        $('.fat-sb-break-time-item-wrap .fat-sb-break-time-item:last-child .fat-break-time-start-dropdown', schedule_item).dropdown('refresh').dropdown('set selected', break_times[$bt_index].es_break_time_start);
                                        $('.fat-sb-break-time-item-wrap .fat-sb-break-time-item:last-child .fat-break-time-end-dropdown', schedule_item).dropdown('refresh').dropdown('set selected', break_times[$bt_index].es_break_time_end);
                                    }
                                }
                            } else {
                                schedule_checkbox.removeAttr("checked");
                                $('.fat-sb-work-hour-wrap', schedule_item).addClass('fat-sb-hidden');
                            }
                        }

                        FatSbSetting.initDepend();
                    }

                    //init day off
                    if (typeof response.day_off != 'undefined' && response.day_off != null && response.day_off != '') {
                        var day_off = response.day_off,
                            day_off_wrap = $('.fat-day-off-wrap', '.fat-setting-modal');
                        for (var $df_index = 0; $df_index < day_off.length; $df_index++) {
                            FatSbSetting.addDayOffItem(day_off_wrap, day_off[$df_index].dof_name, day_off[$df_index].dof_start, day_off[$df_index].dof_end);
                        }
                    }
                });
            },
            error: function () {
                $('.ui.dimmer', self).remove();
            }
        });
    };

    FatSbSetting.passwordOnChange = function (self) {
        self.attr('data-value', self.val());
    };

    FatSbSetting.submitOnClick = function (self) {
        if (FatSbMain.isFormValid) {
            var data = FatSbMain.getFormData('.ui.modal.fat-setting-modal .ui.form'),
                popup = $('.ui.modal.fat-setting-modal');
            if (typeof data['smtp_password'] != 'undefined') {
                data['smtp_password'] = $('#smtp_password').attr('data-value');
            }

            if(popup.hasClass('fat-setting-payment')){
                var has_payment = $('#onsite_enable').is(':checked') ? true : false;
                has_payment = $('#paypal_enable').is(':checked') ? true : has_payment;
                has_payment = $('#stripe_enable').is(':checked') ? true : has_payment;
                has_payment = $('#myPOS_enable').is(':checked') ? true : has_payment;
                has_payment = $('#price_package_enable').is(':checked') ? true : has_payment;
                has_payment = $('#przelewy24_enable').is(':checked') ? true : has_payment;
                if(!has_payment){
                    FatSbMain.showMessage(FatSbMain.data.notice_payment_default, 2);
                    return;
                }
            }

            FatSbMain.showProcess(self);
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_save_setting',
                    data: data
                }),
                success: function (response) {
                    FatSbMain.closeProcess(self);
                    self.closest('.ui.modal').modal('hide');
                    response = $.parseJSON(response);
                    if (response.result > 0) {
                        FatSbMain.showMessage(self.attr('data-success-message'));
                    } else {
                        if(typeof response.message!='undefined'){
                            FatSbMain.showMessage(response.message, 3);
                        }else{
                            FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        }
                    }
                },
                error: function () {
                    FatSbMain.closeProcess(self);
                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                }
            });
        }
    };

    FatSbSetting.dependFieldOnChange = function (self) {
        var id = self.attr('id'),
            value = self.val(),
            payment = '',
            payment_label = '';

        if (self.is(':checkbox')) {
            value = self.is(':checked') ? 1 : 0;
        }

        $('[data-depend="' + id + '"]', '.fat-setting-modal').each(function () {
            var elm = $(this),
                depend_value = elm.attr('data-depend-value');
            if (depend_value == value) {
                setTimeout(function(){
                    elm.removeClass('fat-hidden').removeClass('fat-sb-hidden');
                },300);
                elm.slideDown();
            } else {
                setTimeout(function(){
                    elm.addClass('fat-hidden');
                },300);
                elm.slideUp();
            }
        });
    };

    FatSbSetting.btAddWorkHourOnClick = function (self) {
        var container = self.closest('.fat-sb-work-hour-wrap'),
            work_hour_item_wrap = $('.fat-sb-work-hour-item-wrap', container),
            template = wp.template('fat-sb-work-hour-template'),
            work_hour_item = $(template([]));

        $(work_hour_item_wrap).append(work_hour_item);

        $('.fat-bt-remove-work-hour').off('click').on('click', function () {
            $(this).closest('.fat-sb-work-hour-item').remove();
        });

        //init field
        $('.dropdown', work_hour_item).dropdown({
            'onShow': function () {
                FatSbSetting.updateWorkHourBreakTimeItemStatus($(this));
            }
        });
    };

    FatSbSetting.updateWorkHourBreakTimeItemStatus = function (elm) {
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
                $('.item', self).each(function () {
                    var time = $(this).attr('data-value'),
                        time = parseInt(time);
                    for (var $i = 0; $i < work_hours.length; $i++) {
                        if (work_hours[$i].start < time && time < work_hours[$i].end) {
                            $(this).addClass('disabled');
                            break;
                        }
                        if ($(elm).hasClass('fat-break-time-end-dropdown') && typeof current_break_time_start != 'undefined' && current_break_time_start != null &&
                            time <= current_break_time_start) {
                            $(this).addClass('disabled');
                            break;
                        }
                    }
                });
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

    FatSbSetting.btAddBreakTimeOnClick = function (self) {
        var work_hour_wrap = self.closest('.fat-sb-work-hour-wrap'),
            template = wp.template('fat-sb-break-time-template'),
            break_time = $(template([]));
        $('.fat-sb-break-time-item-wrap', work_hour_wrap).append(break_time);

        //init field
        $('.dropdown', break_time).dropdown({
            'onShow': function () {
                FatSbSetting.updateWorkHourBreakTimeItemStatus($(this));
            }
        });

        $('.fat-bt-remove-break-time').off('click').on('click', function () {
            $(this).closest('.fat-sb-break-time-item').remove();
        });
    };

    FatSbSetting.processCloneSchedule = function (self) {
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
                    self = '';
                $('.fat-sb-work-hour-item-wrap .fat-sb-work-hour-item', item_wrap).each(function () {
                    self = $(this);
                    start = $('input[name="work_hour_start"]', self).val();
                    end = $('input[name="work_hour_end"]', self).val();
                    if (start != '' || end != '') {
                        work_hours.push({
                            start: start,
                            end: end
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

    FatSbSetting.btAddDayOfOnClick = function (self) {
        var day_off_wrap = self.closest('.fat-day-off-wrap');
        FatSbSetting.addDayOffItem(day_off_wrap);
    };

    FatSbSetting.addDayOffItem = function (day_off_wrap, name, start, end) {
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

    FatSbSetting.submitWorkingHourOnClick = function (self) {
        var data = {
                schedules: [],
                break_times: [],
                day_off: []
            },
            schedules = [
                {'id': 'schedule_monday', 'class': 'schedule-monday', 'day': 2},
                {'id': 'schedule_tuesday', 'class': 'schedule-tuesday', 'day': 3},
                {'id': 'schedule_wednesday', 'class': 'schedule-wednesday', 'day': 4},
                {'id': 'schedule_thursday', 'class': 'schedule-thursday', 'day': 5},
                {'id': 'schedule_friday', 'class': 'schedule-friday', 'day': 6},
                {'id': 'schedule_saturday', 'class': 'schedule-saturday', 'day': 7},
                {'id': 'schedule_sunday', 'class': 'schedule-sunday', 'day': 8}
            ],
            schedule_id,
            schedule_class,
            day,
            work_hour_item_wrap,
            break_time_item_wrap,
            work_hours,
            break_times,
            work_hour_item,
            work_hour_start,
            work_hour_end,
            break_time_item,
            break_time_start,
            break_time_end;

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

                    if (work_hour_start != '' && work_hour_end != '') {
                        work_hours.push({es_work_hour_start: work_hour_start, es_work_hour_end: work_hour_end});
                    }
                });


                break_times = [];
                $('.fat-sb-break-time-item', break_time_item_wrap).each(function () {
                    break_time_item = $(this);
                    break_time_start = $('input[name="break_time_start"]', break_time_item).val();
                    break_time_end = $('input[name="break_time_end"]', break_time_item).val();
                    if (break_time_start != '' && break_time_end != '') {
                        break_times.push({es_break_time_start: break_time_start, es_break_time_end: break_time_end});
                    }
                });

                data.schedules.push({
                    es_day: day,
                    es_enable: 1,
                    work_hours: work_hours,
                    break_times: break_times
                });

            } else {
                data.schedules.push({
                    es_day: day,
                    es_enable: 0
                });
            }
        }
        var day_of_item = '',
            day_of_schedule = '';
        $('.fat-day-off-wrap .fat-sb-day-off-item', '.fat-setting-modal').each(function () {
            day_of_item = $(this);
            day_of_schedule = $('input[name="day_off_schedule"]', day_of_item);
            data.day_off.push({
                dof_name: $('input[name="day_off_name"]', day_of_item).val(),
                dof_start: typeof day_of_schedule.attr('data-start') != 'undefined' ? day_of_schedule.attr('data-start') : '',
                dof_end: typeof day_of_schedule.attr('data-end') != 'undefined' ? day_of_schedule.attr('data-end') : '',
            });
        });
        if (data.schedules.length == 0) {
            FatSbMain.showMessage(self.attr('data-invalid-message'), 2);
            return;
        }
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_save_working_hour_setting',
                data: data
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                self.closest('.ui.modal').modal('hide');
                response = $.parseJSON(response);
                if (response.result > 0) {
                    FatSbMain.showMessage(self.attr('data-success-message'));
                } else {
                    if(typeof response.message!='undefined'){
                        FatSbMain.showMessage(response.message, 3);
                    }else{
                        FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                    }
                }
            },
            error: function () {
                FatSbMain.closeProcess(self);
                FatSbMain.showMessage(FatSbMain.data.error_message, 2);
            }
        });
    };

    FatSbSetting.submitUserRoleOnClick = function (self) {
        var data = {};
        data['allow_user_booking'] = $('#allow_user_booking').val();
        data['allow_user_role_booking'] = $('#allow_user_role_booking').val();
        data['warning_message'] = $('#warning_message').val();
        data['validate_user_at'] = $('#validate_user_at').val();
        data['limit_user'] = $('#limit_user').val();
        data['warning_limit_user_message'] = $('#warning_limit_user_message').val();

        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_save_user_role_setting',
                data: data
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                self.closest('.ui.modal').modal('hide');
                response = $.parseJSON(response);
                if (response.result > 0) {
                    FatSbMain.showMessage(self.attr('data-success-message'));
                } else {
                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                }
            },
            error: function () {
                FatSbMain.closeProcess(self);
                FatSbMain.showMessage(FatSbMain.data.error_message, 2);
            }
        });
    };

    $(document).ready(function () {
        if ($('.fat-sb-settings-container').length > 0) {
            FatSbSetting.init();
        }
    });
})(jQuery);