"use strict";
/**
 * Number.prototype.format(n, x, s)
 *
 * @param integer n: length of decimal
 * @param integer x: length of sections
 * @param string s:  separator
 */
Number.prototype.format = function (n, x, s) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&' + s);
};

var FatSbMain_FE = {
    data: fat_sb_data
};
(function ($) {
    /*
        register event
         */
    FatSbMain_FE.registerOnChange = function (container) {
        container = typeof container == 'undefined' ? $('.fat-semantic-container') : container;
        $('[data-onChange]', container).each(function () {
            var self = $(this),
                callback = self.attr('data-onChange').split('.'),
                obj = callback.length == 2 ? callback[0] : '',
                func = callback.length == 2 ? callback[1] : callback[0];

            /*semantic dropdown*/
            if (self.hasClass('ui') && self.hasClass('dropdown')) {
                self.dropdown({
                    onChange: function (value, text, $choice) {
                        if (self.hasClass('onChange-disabled')) {
                            return;
                        }
                        if (obj != '') {
                            (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](value, text, $choice, self) : '';
                        } else {
                            (typeof window[func] != 'undefined' && window[func] != null) ? window[func](value, text, $choice, self) : '';
                        }
                    }
                });
                return;
            }

            /*sumo dropdown*/
            if (self.hasClass('SumoUnder')) {
                self.on('sumo:closed', function (sumo) {
                    if (obj != '') {
                        (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](self, sumo) : '';
                    } else {
                        (typeof window[func] != 'undefined' && window[func] != null) ? window[func](self, sumo) : '';
                    }
                });
                return;
            }

            /*default field*/
            self.off('change').on('change', function () {
                if (self.hasClass('onChange-disabled')) {
                    return;
                }
                if (obj != '') {
                    (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](self) : '';
                } else {
                    (typeof window[func] != 'undefined' && window[func] != null) ? window[func](self) : '';
                }
            });
        });
    };

    FatSbMain_FE.registerOnClick = function (container) {
        container = typeof container == 'undefined' ? $('.fat-semantic-container') : container;
        $('[data-onClick]', container).each(function () {
            var self = $(this),
                callback = self.attr('data-onClick').split('.'),
                obj = callback.length == 2 ? callback[0] : '',
                func = callback.length == 2 ? callback[1] : callback[0],
                prevent_event = self.attr('data-prevent-event');

            self.on('click', function (event) {
                if (prevent_event) {
                    event.preventDefault();
                }

                if (obj != '') {
                    (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](self, event) : '';
                } else {
                    (typeof window[func] != 'undefined' && window[func] != null) ? window[func](self, event) : '';
                }
                if (prevent_event) {
                    return false;
                }
            });
        });
    };

    FatSbMain_FE.registerOnKeyUp = function (container) {
        container = typeof container == 'undefined' ? $('.fat-semantic-container') : container;
        $('[data-onKeyUp]', container).each(function () {
            var self = $(this),
                callback = self.attr('data-onKeyUp').split('.'),
                obj = callback.length == 2 ? callback[0] : '',
                func = callback.length == 2 ? callback[1] : callback[0];

            self.off('keyup').on('keyup', function () {
                if (obj != '') {
                    (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](self) : '';
                } else {
                    (typeof window[func] != 'undefined' && window[func] != null) ? window[func](self) : '';
                }
            });
        });
    };

    FatSbMain_FE.registerEventProcess = function (container) {
        container = typeof container == 'undefined' ? $('.fat-semantic-container') : container;
        FatSbMain_FE.registerOnChange(container);
        FatSbMain_FE.registerOnClick(container);
        FatSbMain_FE.registerOnKeyUp(container);
    };

    FatSbMain_FE.showLoading = function (container) {
        $('.fat-ui-loader-container', container).remove();
        container.append('<div class="fat-ui-loader-container"><div class="fat-ui-loader">' + fat_sb_data.loading_label + '</div></div>');
    };

    FatSbMain_FE.closeLoading = function (container) {
        $('.fat-ui-loader-container', container).remove();
    };

    FatSbMain_FE.addLoading = function (container, elm) {
        if ($('.fat-loading-container', container).length == 0) {
            container.append('<div class="fat-loading-container"></div>');
        }
        var field = elm.closest('.field');
        if (typeof field != 'undefined' && $('label', field).length > 0) {
            $('label', field).append('<div class="ui active mini inline loader"></div>');
        }
        elm.addClass('loading');
    };

    FatSbMain_FE.removeLoading = function (container, elm) {
        $('.fat-loading-container', container).remove();
        var field = elm.closest('.field');
        if (typeof field != 'undefined' && $('label', field).length > 0) {
            $('label .ui.loader', field).remove();
        }
        elm.removeClass('loading');
    };

    FatSbMain_FE.validateForm = function (form) {
        var input,
            isValid = true;
        $('input[required]', form).each(function () {
            input = $(this);
            if ((input.val().trim() == '' && !input.hasClass('air-date-picker')) || (input.hasClass('air-date-picker') && typeof input.attr('data-date') == 'undefined')) {
                input.closest('.field').addClass('field-error');
                isValid = false;
            } else {
                input.closest('.field').removeClass('field-error');
            }
        });
        $('input[type="email"]', form).each(function () {
            input = $(this);
            var pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/),
                email = input.val().trim();
            if (email == '' || !pattern.test(email)) {
                input.closest('.field').addClass('field-error');
                isValid = false;
            } else {
                input.closest('.field').removeClass('field-error');
            }
        });

        $('.fat-sb-checkbox-group[required]', form).each(function () {
            var checkGroup = $(this);
            if ($('input[type="checkbox"]', checkGroup).is(':checked') == false) {
                checkGroup.closest('.field').addClass('field-error');
                isValid = false;
            } else {
                checkGroup.closest('.field').removeClass('field-error');
            }
        });

        $('.fat-sb-radio-group[required]', form).each(function () {
            var radioGroup = $(this);
            if ($('input[type="radio"]', radioGroup).is(':checked') == false) {
                radioGroup.closest('.field').addClass('field-error');
                isValid = false;
            } else {
                radioGroup.closest('.field').removeClass('field-error');
            }
        });


        return isValid;
    };

    FatSbMain_FE.initFormBuilder = function () {
        $('.fat-sb-field-builder.fat-sb-date-field').each(function () {
            var self = $(this),
                lang = self.attr('data-locale');
            $(this).datepicker({
                language: lang,
                onSelect: function (formattedDate, date, inst) {
                    if (typeof date == 'undefined' || date == '') {
                        return;
                    }

                    var month = date.getMonth() + 1,
                        day = date.getDate(),
                        selected_date_value = '';
                    month = parseInt(month);
                    day = parseInt(day);
                    month = month < 10 ? ('0' + month) : month;
                    day = day < 10 ? ('0' + day) : day;
                    var selected_date_value = date.getFullYear() + '-' + month + '-' + day;
                    $(inst.el).attr('data-date', selected_date_value)
                }
            })
        });

        $('.ui-tooltip','.fat-booking-container').popup({
            inline: true,
            hoverable: true,
            position: 'top left',
            delay: {
                show: 300,
                hide: 500
            }
        });
    };

    FatSbMain_FE.showMessage = function (message, type) {
        var css_class = typeof type == 'undefined' || type == '1' ? 'blue' : 'red',  //1:success message, 2: error message
            icon = typeof type == 'undefined' || type == '1' ? 'check icon' : 'close icon';

        css_class = type == '3' ? 'orange' : css_class;

        var elm_message = '<div class="fat-sb-message ' + css_class + '">';
        elm_message += typeof icon != 'undefined' && icon != '' ? '<i class="' + icon + '"></i>' : '';
        elm_message += '<span>' + message + '</span>';
        elm_message = $(elm_message);
        var top = ($('body .fat-sb-message').length * 60 + 50) + 'px';
        $(elm_message).css('top', top);
        $('body').append(elm_message);
        setTimeout(function () {
            $(elm_message).addClass('show-up');
            setTimeout(function () {
                $(elm_message).removeClass('show-up');
                setTimeout(function () {
                    $(elm_message).remove();
                }, 300);
            }, 5000);
        }, 200);

    };

    FatSbMain_FE.showNotFoundMessage = function (elm, wrap_start, wrap_end) {
        var content = '';
        if (typeof wrap_start != 'undefined' && wrap_start != '') {
            content = wrap_start;
        }
        content += '<div class="fat-sb-not-found">' + FatSbMain_FE.data.not_found_message + '</div>';
        if (typeof wrap_end != 'undefined' && wrap_end != '') {
            content += wrap_end;
        }
        $('.fat-sb-not-found', elm).remove();
        elm.append(content);
    };

    FatSbMain_FE.equalDay = function ($date1, $date2) {
        return ($date1.getDate() == $date2.getDate()) && ($date1.getMonth() == $date2.getMonth()) && ($date1.getFullYear() == $date2.getFullYear());
    };

    //fix for Safari Date Time
    FatSbMain_FE.parseDateTime = function ($now) {
        $now = $now.trim().split(' ');
        if($now.length ==2){
            var $date = $now[0].split('-'),
                $time = $now[1].split(':');
            if($date.length==3 && $time.length==3){
                var month = parseInt($date[1])-1;
                return new Date($date[0], month, $date[2], $time[0], $time[1], $time[2]);
            }
        }
        return new Date($now);
    };

    FatSbMain_FE.calculatePrice = function ($quantity, $price, $s_id) {
        return ($quantity * $price);
    };

    FatSbMain_FE.getPriceLabel = function($quantity, $price, $price_base_quantity, $s_id){
        var price_label = '<span>' + $quantity + ' ' + FatSbMain_FE.data.person_label + ' x ' + FatSbMain_FE.data.symbol_prefix + $price.format(FatSbMain_FE.data.number_of_decimals, 3, ',')  + FatSbMain_FE.data.symbol_suffix;
        price_label += ' = </span>' + FatSbMain_FE.data.symbol_prefix + $price_base_quantity.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix;
        return price_label;
    };

    FatSbMain_FE.initPopupToolTip = function(){
        //init popup
        $('.fat-has-popup').each(function () {
            var self = $(this),
                trigger = self.hasClass('popup-click') ? 'click' : 'hover',
                popup_id = self.attr('data-popup-id'),
                popup = $('.ui.popup[data-popup-id="' + popup_id + '"]'),
                inline = typeof self.attr('data-popup-inline') != 'undefined' && self.attr('data-popup-inline') != '' ? self.attr('data-popup-inline') : true,
                lastResort = typeof self.attr('data-last-resort') !='undefined' ? self.attr('data-last-resort') : '',
                option = {
                    popup: popup,
                    on: trigger,
                    inline: inline,
                    hoverable: true
                };
            if(lastResort!=''){
                option.lastResort = lastResort;
            }
            if (popup.length > 0) {
                self.popup(option)
            }
        });

        //tooltip
        $('.ui-tooltip,.ui-popup').popup({
            inline: true,
            hoverable: true,
            position: 'top left',
            delay: {
                show: 300,
                hide: 500
            }
        });
    };

    FatSbMain_FE.show_deactive_slot = function($es_day, e_schedules){
        if(FatSbMain_FE.data.enable_time_slot_deactive=='1' && typeof FatSbMain_FE.data.working_hour!='undefined'
            && typeof FatSbMain_FE.data.working_hour.schedules!='undefined' ){
            var start_hour = 0,
                end_hour = 0,
                slot_value = 0;

            $('.fat-sb-booking-time-wrap .item').each(function(){
                $(this).removeClass('show-deactive');
                $('.time-label', this).css('background-color', 'inherit');
            });

            for(let $es of e_schedules){
                if($es.es_day==$es_day && $es.es_enable=='1' && typeof $es.work_hours!='undefined'){
                    for(let $wh of $es.work_hours){
                        start_hour = parseInt($wh.es_work_hour_start);
                        end_hour = parseInt($wh.es_work_hour_end);
                        $('.fat-sb-booking-time-wrap .item.disabled:not(.over-day)').each(function(){
                            slot_value = parseInt($(this).attr('data-value'));
                            if(slot_value >= start_hour && slot_value < end_hour){
                                $(this).addClass('show-deactive');
                                $('.time-label', this).css('background-color', FatSbMain_FE.data.bg_time_slot_not_active);
                            }
                        });
                    }
                    break;
                }
            }
        }
    };

    FatSbMain_FE.initNumberField = function(container){
        // number
        $('.ui.input.number > input', container).off('keypress').on('keypress', function (event) {
            var self = $(this),
                type = self.attr('data-type'),
                min = self.attr('data-min'),
                max = self.attr('data-max'),
                validkeys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
            if (self.hasClass('disabled')) {
                return false;
            }

            type = typeof type == 'undefined' ? 'int' : type;
            if (type == 'decimal') {
                validkeys.push('.');
            }
            if (validkeys.indexOf(event.key) < 0) {
                return false;
            }
        });

        $('.ui.input.number > input', container).on('change', function (event) {
            var self = $(this),
                min = self.attr('data-min'),
                max = self.attr('data-max'),
                value = self.val();
            if (typeof min != 'undefined' && !isNaN(min) && value != '' && !isNaN(value)) {
                if (parseFloat(value) < parseFloat(min)) {
                    $(this).val(min);
                    event.preventDefault();
                }
            }
            if (typeof max != 'undefined' && !isNaN(max) && value != '' && !isNaN(value)) {
                if (parseFloat(value) > parseFloat(max)) {
                    $(this).val(max);
                    event.preventDefault();
                }
            }
        });

        $('.ui.input.number > input[data-min]', container).each(function () {
            if ($(this).val() == '') {
                $(this).val($(this).attr('data-min'));
            }
        });

        $('.button', '.input.number.has-button', container).off('click').on('click', function () {
            var self = $(this),
                container = self.closest('.input.number.has-button'),
                input = $('input', container),
                value = input.val(),
                step = input.attr('data-step'),
                type = input.attr('data-type'),
                min = input.attr('data-min'),
                max = input.attr('data-max');

            type = typeof type == 'undefined' ? 'int' : type;

            if (type == 'decimal') {
                step = typeof step == 'undefined' || isNaN(step) ? 1 : parseFloat(step);
                min = !isNaN(min) ? parseFloat(min) : '';
                max = !isNaN(max) ? parseFloat(max) : '';
                value = value == '' ? 0 : parseFloat(value);
            } else {
                step = typeof step == 'undefined' || isNaN(step) ? 1 : parseInt(step);
                min = !isNaN(min) ? parseInt(min) : '';
                max = !isNaN(max) ? parseInt(max) : '';
                value = value == '' ? 0 : parseInt(value);
            }

            if (self.hasClass('number-decrease')) {
                if (min !== '' && ((value - step) < min)) {
                    FatSbMain_FE.showMessage(fat_sb_data.min_value_message + min,2);
                } else {
                    value >= step ? input.val(value - step) : input.val(0);
                }
            } else {
                if (max !== '' && ((value + step) > max)) {
                    FatSbMain_FE.showMessage(fat_sb_data.max_value_message + max,2);
                } else {
                    input.val(value + step);
                }
            }
        });
    };

    FatSbMain_FE.formatPrice = function(price){
        if(isNaN(price)){
            return price;
        }
        price = parseFloat(price);
        return FatSbMain_FE.data.symbol_prefix + price.format(FatSbMain_FE.data.number_of_decimals, 3, ',') + FatSbMain_FE.data.symbol_suffix;
    };

    FatSbMain_FE.initPaging = function (total, page, elm, callback) {

        var item_per_page = FatSbMain_FE.data.item_per_page,
            page_display = fat_sb_data.item_per_page,
            obj = elm.attr('data-obj'),
            func = elm.attr('data-func'),
            paging = '<div class="ui right floated pagination menu" >';

        page = parseInt(page);

        $('.ui.pagination', elm).remove();
        if (total > item_per_page) {
            var number_of_page = Math.floor(total / item_per_page) + (total % item_per_page > 0 ? 1 : 0),
                $start_index = 1,
                $end_index = 0;

            $start_index = page - 2 > 0 ? (page - 2) : 1;
            $end_index = page + 2 < number_of_page ? (page + 2) : number_of_page;

            if (page == 1) {
                paging += ' <button class="ui button nav-first nav-disabled"> <i class="angle double left icon"></i></button>';
                paging += ' <button class="ui button fat-bt-prev nav-disabled"> <i class="angle left icon"></i></button>';
            } else {
                paging += ' <button class="ui button nav-first" data-page="1"> <i class="angle double left icon"></i></button>';
                paging += ' <button class="ui button fat-bt-prev" data-page="' + (page - 1) + '"> <i class="angle left icon"></i></button>';
            }

            if ($start_index >= (page_display - 1)) {
                paging += '<button class="ui button nav-disabled">...</button>';
            }

            for (var $page_index = $start_index; $page_index <= $end_index; $page_index++) {
                paging += '<button class="ui button" data-page="' + $page_index + '">' + $page_index + '</button>';
            }
            if ($end_index < number_of_page) {
                paging += '<button class="ui button nav-disabled">...</button>';
            }

            if (page == number_of_page) {
                paging += ' <button class="ui button fat-bt-next nav-disabled"> <i class="angle right icon"></i></button>';
                paging += ' <button class="ui button nav-last nav-disabled"> <i class="angle double right icon"></i></button>';
            } else {
                paging += ' <button class="ui button fat-bt-next" data-page="' + (page + 1) + '"> <i class="angle right icon"></i></button>';
                paging += ' <button class="ui button nav-last" data-page="' + number_of_page + '"> <i class="angle double right icon"></i></button>';
            }

            $(elm).append(paging);
            $('.ui.pagination button.ui.button[data-page="' + page + '"]', elm).addClass('active');

            if (typeof window[obj][func] != 'undefined' && window[obj][func] != null) {
                $('.ui.pagination button.ui.button:not(.nav-disabled)', '.fat-sb-pagination').off('click').on('click', function () {
                    var self = $(this),
                        page = self.attr('data-page');
                    if (!self.hasClass('active')) {
                        self.addClass('loading');
                        window[obj][func](page);
                    }
                });
            }

            if (typeof callback == 'function') {
                callback();
            }
        }
    };

    FatSbMain_FE.showConfirmPopup = function ($popup_title, $message, callback) {
        var popup = $("<div class=\"ui mini modal fat-sb-confirm-popup\">\n" +
            "  <div class=\"ui header\">\n" +
            $popup_title +
            "  </div>\n" +
            "  <div class=\"content\">\n" +
            $message +
            "  </div>\n" +
            "  <div class=\"actions\">\n" +
            "    <div class=\"ui button cancel fat-sb-bt-confirm\">\n" +
            "      <i class=\"remove icon\"></i>\n" +
            FatSbMain_FE.data.bt_no_lable +
            "    </div>\n" +
            "    <div class=\"ui primary yes button fat-sb-bt-confirm \">\n" +
            "      <i class=\"checkmark icon\"></i>\n" +
            FatSbMain_FE.data.bt_yes_lable +
            "    </div>\n" +
            "  </div>\n" +
            "</div>");

        $('body').append(popup);
        $(popup).modal('setting', 'closable', false).modal('show');
        $('.fat-sb-bt-confirm').on('click', function () {
            if (typeof callback == 'function') {
                var $result = $(this).hasClass('cancel') ? 0 : 1;
                if ($result == 1) {
                    callback($result, popup);
                } else {
                    $('.fat-sb-confirm-popup').modal('hide');
                }
            }
        });
    };

})(jQuery);