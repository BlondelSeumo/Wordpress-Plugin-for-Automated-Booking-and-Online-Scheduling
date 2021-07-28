"use strict";
/**
 * Number.prototype.format(n, x, s)
 *
 * @param integer n: length of decimal
 * @param integer x: length of sections
 * @param string s:  separator
 */
Number.prototype.format = function (n, x, s) {
    s = typeof s != 'undefined' ? s : ',';
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&' + s);
};

var FatSbMain = {
    processCheckBox: false,
    data: fat_sb_data,
    isFormValid: true
};
(function ($) {
    FatSbMain.init = function () {
        FatSbMain.initCopyShortcode();
    };

    FatSbMain.initCarousel = function (elm) {
        if ($.isFunction($.fn.owlCarousel)) {
            $('.owl-carousel').each(function () {
                $(elm).trigger('destroy.owl.carousel');
                $(elm).each(function () {
                    var $owl = $(this),
                        defaults = {
                            items: 4,
                            nav: false,
                            navText: ['<i class="fa fa-angle-left"></i> ', ' <i class="fa fa-angle-right"></i>'],
                            dots: false,
                            loop: false,
                            center: false,
                            mouseDrag: true,
                            touchDrag: true,
                            pullDrag: true,
                            freeDrag: false,
                            margin: 0,
                            stagePadding: 0,
                            merge: false,
                            mergeFit: true,
                            autoWidth: false,
                            startPosition: 0,
                            rtl: false,
                            smartSpeed: 250,
                            autoplay: false,
                            autoplayTimeout: 0,
                            fluidSpeed: false,
                            dragEndSpeed: false,
                            autoplayHoverPause: true
                        };
                    var config = $.extend({}, defaults, $owl.data("owl-options"));
                    // Initialize Slider
                    $($owl).imagesLoaded(function () {
                        $owl.owlCarousel(config);
                    });
                });
            });
        }
    };

    FatSbMain.initField = function () {
        //select box
        $('.fat-semantic-container .ui.dropdown').each(function () {
            var self = $(this);
            self.dropdown({
                clearable: self.hasClass('clearable')
            });
        });

        //checkbox
        $('.fat-semantic-container .ui.checkbox').checkbox();

        // number
        $('.ui.input.number > input').on('keypress', function (event) {
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

        $('.ui.input.number > input').on('change', function (event) {
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

        $('.ui.input.number > input[data-min]').each(function () {
            if ($(this).val() == '') {
                $(this).val($(this).attr('data-min'));
            }
        });

        $('.button', '.input.number.has-button').off('click').on('click', function () {
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
                    FatSbMain.showMessage(fat_sb_data.min_value_message + min,2);
                } else {
                    value >= step ? input.val(value - step) : input.val(0);
                }
            } else {
                if (max !== '' && ((value + step) > max)) {
                    FatSbMain.showMessage(fat_sb_data.max_value_message + max,2);
                } else {
                    input.val(value + step);
                }
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

        $('.ui-popup').each(function () {
            var position = $(this).attr('data-position');
            position = typeof position != 'undefined' && position != '' ? position : 'top left';
            $(this).popup({
                inline: true,
                hoverable: true,
                position: position,
                delay: {
                    show: 300,
                    hide: 500
                }
            });
        });

        $('.button[data-content]').popup({
            inline: true
        });

        // single image
        $('.ui.image-field:not(.field-loaded)').each(function () {
            var self = $(this);
            self.append('<a href="javascript:;" class="ui select-image  fat-box-shadow"><i class="image outline icon"></i></a>');
            self.addClass('field-loaded');
            $('a.select-image', self).on('click', function (event) {
                event.preventDefault();

                wp.media.frames.gk_frame = wp.media({
                    title: 'Select Image',
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                wp.media.frames.gk_frame.clicked_button = $(this);
                wp.media.frames.gk_frame.open().on('select', function (e) {
                    var img_url,
                        img_id,
                        image,
                        parent,
                        selection_image = wp.media.frames.gk_frame.state().get('selection');

                    selection_image.each(function (attachment) {
                        image = '<div class="fat-image-thumb"><img src="%thumbnail" /></div>';
                        if (typeof attachment.attributes.sizes.thumbnail != 'undefined') {
                            img_url = attachment.attributes.sizes.thumbnail.url;
                        } else {
                            img_url = attachment.attributes.url;
                        }
                        img_id = attachment.attributes.id;
                        image = image.replace("%thumbnail", img_url);
                        parent = $(wp.media.frames.gk_frame.clicked_button).closest('.select-image');
                        $('.fat-image-thumb', parent).remove();
                        parent.append(image);
                        parent.addClass('has-thumbnail');

                    });
                    self.attr('data-image-id', img_id);
                });
            });
            if (typeof self.attr('data-image-url') != 'undefined' && self.attr('data-image-url') != '') {
                $('.ui.select-image', self).append('<div class="fat-image-thumb"><img src="' + self.attr('data-image-url') + '" /></div>');
                $('.ui.select-image', self).addClass('has-thumbnail');
            }
        });

        //date range picker
        if ($.isFunction($.fn.daterangepicker)) {
            var date_format = FatSbMain.getDateFormat();
            $('input.date-range-picker').attr('autocomplete', 'off');
            $('input.date-range-picker').each(function () {
                var self = $(this),
                    locale = typeof self.attr('data-locale') !='undefined' && self.attr('data-locale')!='' ? self.attr('data-locale') : '',
                    start_date = self.attr('data-start-init'),
                    end_date = self.attr('data-end-init'),
                    time_picker = self.attr('date-time-picker') =='1',
                    ranger_date_format = time_picker ? (date_format + ' hh:mm A') : date_format,
                    autoUpdate = typeof self.attr('data-auto-update') != 'undefined' && self.attr('data-auto-update') == '1',
                    options = {
                        autoUpdateInput: autoUpdate,
                        autoApply: true,
                        timePicker: time_picker,
                        locale: {
                            format: ranger_date_format,
                            applyLabel: FatSbMain.data.apply_title,
                            cancelLabel: FatSbMain.data.cancel_title,
                            fromLabel: FatSbMain.data.from_title,
                            toLabel: FatSbMain.data.to_title,
                            daysOfWeek: FatSbMain.i18n_daysOfWeek(locale),
                            monthNames: FatSbMain.i18n_monthName(locale)
                        }
                    };

                if (typeof start_date != 'undefined' && start_date != '') {
                    options.startDate = FatSbMain.moment_i18n(locale, start_date, ranger_date_format);
                }
                if (typeof end_date != 'undefined' && end_date != '') {
                    options.endDate = FatSbMain.moment_i18n(locale, end_date, ranger_date_format);
                }

                if(locale!=''){
                    moment.locale(locale);
                }

                self.daterangepicker(options, function (start, end, label) {
                    self.val(label);
                    self.attr('data-start', start.format('YYYY-MM-DD'));
                    self.attr('data-end', end.format('YYYY-MM-DD'));
                    if(time_picker){
                        self.attr('data-start-time', start.format('HH:mm'));
                        self.attr('data-end-time', end.format('HH:mm'));
                    }
                });
            });


            $('input.date-picker').attr('autocomplete', 'off');
            $('input.date-picker').each(function () {
                var date_format = FatSbMain.getDateFormat();
                var self = $(this),
                    start_date = self.attr('data-start-init'),
                    locale = typeof self.attr('data-locale') !='undefined' && self.attr('data-locale')!='' ? self.attr('data-locale') : '',
                    options = {
                        singleDatePicker: true,
                        autoApply: true,
                        showDropdowns: dropdown,
                        locale: {
                            format: date_format,
                            applyLabel: FatSbMain.data.apply_title,
                            cancelLabel: FatSbMain.data.cancel_title,
                            fromLabel: FatSbMain.data.from_title,
                            toLabel: FatSbMain.data.to_title,
                            daysOfWeek: FatSbMain.i18n_daysOfWeek(locale),
                            monthNames: FatSbMain.i18n_monthName(locale)
                        }
                    },
                    dropdown = typeof self.attr('data-dropdown') != 'undefined' && self.attr('data-dropdown') == '1' ? true : false;
                if(locale!=''){
                    moment.locale(locale);
                }
                if (typeof start_date != 'undefined' && start_date != '') {
                    options.startDate = start_date;
                }
                self.daterangepicker(options, function (start, end, label) {
                    self.attr('data-date', start.format('YYYY-MM-DD'));
                });
            });
        }

        // process submit
        $('.fat-submit-modal').on('click', function (event) {
            var form = $(this).closest('.ui.modal.fat-semantic-container');
            FatSbMain.isFormValid = true;
            FatSbMain.isFormValid = FatSbMain.validateForm($('.ui.form', form));
        });

        //tab
        $('.fat-tabs .item').tab();

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

        //sumo dropdown select
        if ($.isFunction($.fn.SumoSelect)) {
            $('.fat-sb-sumo-select').each(function () {
                var self = $(this);
                self.SumoSelect({
                    search: true,
                    placeholder: self.attr('data-placeholder'),
                    captionFormat: '{0} ' + self.attr('data-caption-format'),
                    captionFormatAllSelected: '{0} ' + self.attr('data-caption-format'),
                    searchText: self.attr('data-search-text') != '' ? self.attr('data-search-text') : 'Search'
                });
            });
        }

        $('input,textarea', '.ui.modal.fat-semantic-container').on('keypress', function (e) {
            if (e.which == 13) {
                var self = $(this),
                    tabindex = self.attr('tabindex');
                if (self.hasClass('search') && tabindex == 0 && self.closest('.ui.dropdown')) {
                    tabindex = $('input[type="hidden"]', self.closest('.ui.dropdown')).attr('tabindex');
                }
                if (typeof tabindex != 'undefined' && tabindex != '' && !isNaN(tabindex)) {
                    tabindex = parseInt(tabindex) + 1;
                    var nextElm = $('[tabindex="' + tabindex + '"]', '.ui.modal.fat-semantic-container');
                    if (nextElm.hasClass('dropdown') || nextElm.hasClass('fat-submit-modal') || nextElm.hasClass('fat-close-modal')) {
                        nextElm.trigger('click');
                    }
                    nextElm.focus();
                    nextElm.select();
                }
            }
        });

    };

    FatSbMain.initPopupToolTip = function(){
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

    FatSbMain.initDepend = function () {
        $('div[data-depend]').each(function () {
            var dependId = $(this).attr('data-depend'),
                dependElm = $('#' + dependId);
            if (dependElm.is(':checkbox')) {
                if (dependElm.is(':checked')) {
                    $('div[data-depend="' + dependId + '"]').removeClass('fat-sb-hidden').removeClass('fat-hidden');
                }
                dependElm.on('change', function () {
                    if (dependElm.is(':checked')) {
                        $('div[data-depend="' + dependId + '"]').removeClass('fat-sb-hidden').removeClass('fat-hidden');
                    } else {
                        $('div[data-depend="' + dependId + '"]').addClass('fat-sb-hidden');
                    }
                });
            }
        });
    };

    FatSbMain.initCheckAll = function () {
        $('.table-check-all').on('change', function () {
            FatSbMain.processCheckBox = true;
            var self = $(this),
                table = self.closest('table'),
                btDelete = $('.fat-bt-delete', self.closest('.fat-semantic-container'));

            $('input.check-item[type="checkbox"]', table).prop("checked", self.is(':checked'));
            if (self.is(':checked')) {
                btDelete.removeClass('disabled');
            } else {
                btDelete.addClass('disabled');
            }
            FatSbMain.processCheckBox = false;
        });

        $('input.check-item[type="checkbox"]', 'table').on('change', function () {
            if (!FatSbMain.processCheckBox) {
                var self = $(this),
                    table = self.closest('table'),
                    btDelete = $('.fat-bt-delete', self.closest('.fat-semantic-container')),
                    enable_btDelete = false,
                    isCheckAll = true;

                $('input.check-item[type="checkbox"]', table).each(function () {
                    if (!$(this).is(':checked')) {
                        isCheckAll = false;
                    }
                });
                $('.table-check-all', table).prop("checked", isCheckAll);

                $('input.check-item[type="checkbox"]', table).each(function () {
                    if ($(this).is(':checked')) {
                        enable_btDelete = true;
                    }
                });

                if (enable_btDelete) {
                    btDelete.removeClass('disabled');
                } else {
                    btDelete.addClass('disabled');
                }
            }
        });
    };

    FatSbMain.showPopup = function ($tmpl_name, $popup_title, $data, callback) {
        var template = wp.template($tmpl_name),
            popup = $(template($data));
        if ($popup_title != '') {
            $('.fat-sb-popup-title', popup).html($popup_title);
        }
        $('body').append(popup);
        popup.modal({
            transition: 'fade up',
            allowMultiple: true,
            autofocus: false,
            closable: false,
            duration: 300,
            onHide: function ($element) {
                setTimeout(function () {
                    $(popup, 'body').remove();
                }, 500);
                $('.ui.dimmer.modals .ui.modal.fat-semantic-container:not(:last-child)').css('opacity', 1);
            },
            onShow: function ($element) {
                $('.ui.dimmer.modals .ui.modal.fat-semantic-container:not(:last-child)').css('opacity', 0);
            }
        }).modal('show');

        FatSbMain.initField();
        FatSbMain.initDepend();

        $('.fat-close-modal', popup).on('click', function () {
            popup.modal('hide');
        });

        if (typeof callback == 'function') {
            callback();
        }
    };

    FatSbMain.showMessage = function (message, type) {
        var css_class = typeof type == 'undefined' || type == '1' ? 'blue' : 'red',  //1:success message, 2: error message
            icon = typeof type == 'undefined' || type == '1' ? 'check icon' : 'close icon';

        css_class = type =='3' ? 'orange' : css_class;

        var elm_message = '<div class="fat-sb-message ' + css_class + '">';
        elm_message += typeof icon != 'undefined' && icon != '' ? '<i class="' + icon + '"></i>' : '';
        elm_message += '<span>' + message + '</span>';
        elm_message += '</div>';
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
            }, 4000);
        }, 200);

    };

    FatSbMain.showConfirmPopup = function ($popup_title, $message, callback) {
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
            FatSbMain.data.bt_no_lable +
            "    </div>\n" +
            "    <div class=\"ui primary yes button fat-sb-bt-confirm \">\n" +
            "      <i class=\"checkmark icon\"></i>\n" +
            FatSbMain.data.bt_yes_lable +
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

    FatSbMain.validateForm = function (form) {
        var input,
            fieldType,
            message,
            isValid = true;
        $('input[required]', form).each(function () {
            input = $(this);
            message = input.attr('data-validate-message');
            fieldType = input.attr('data-field-type');
            if (input.val().trim() == '') {
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
            if(email != '' && !pattern.test(email)){
                input.closest('.field').addClass('field-error');
                isValid = false;
            }else{
                input.closest('.field').removeClass('field-error');
            }
        });
        return isValid;
    };

    FatSbMain.guid = function () {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    };

    FatSbMain.showProcess = function (button) {
        $('body').append('<div class="fat-sb-process-container"></div>');
        $(button).addClass('loading');
    };

    FatSbMain.closeProcess = function (button) {
        $('body .fat-sb-process-container').remove();
        $(button).removeClass('loading');
    };

    FatSbMain.showLoading = function () {
        var loading = "<div class=\"fat-sb-loading-container\"><div class=\"ui segment\">\n" +
            "  <div class=\"ui active dimmer\">\n" +
            "    <div class=\"ui small text loader\">" + FatSbMain.data.loading_label + "</div>\n" +
            "  </div>\n" +
            "  <p></p>\n" +
            "</div></div>";
        $('body').append(loading);
    };

    FatSbMain.showNotFoundMessage = function (elm, wrap_start, wrap_end) {
        var content = '';
        if (typeof wrap_start != 'undefined' && wrap_start != '') {
            content = wrap_start;
        }
        content += '<div class="fat-sb-not-found">' + FatSbMain.data.not_found_message + '</div>';
        if (typeof wrap_end != 'undefined' && wrap_end != '') {
            content += wrap_end;
        }
        $('.fat-sb-not-found', elm).remove();
        elm.append(content);
    };

    FatSbMain.closeLoading = function () {
        $('body .fat-sb-loading-container').remove();
    };

    FatSbMain.getFormData = function (form) {
        var data = {},
            fields_Checked = [],
            field_id = '',
            field_name = '',
            field = '';

        $('input[type="text"],input[type="password"],input[type="hidden"],input[type="email"],textarea', form).each(function () {
            field = $(this);
            field_id = field.attr('id');
            if (typeof field_id != 'undefined' && !field.hasClass('fat-sb-extra-field')) {
                data[field_id] = field.val();
            }
            if(field.hasClass('date-picker')){
                data[field_id] = field.attr('data-date')
            }
        });

        $('input[type="radio"]', form).each(function () {
            field = $(this);
            field_id = field.attr('id');
            field_name = field.attr('name');
            if (typeof field_id != 'undefined') {
                if ($.inArray(field_id, fields_Checked) == -1) {
                    $('input[type="radio"][name="' + field_name + '"]').each(function () {
                        if ($(this).is(':checked')) {
                            data[field_id] = $(this).val();
                        }
                    });
                    fields_Checked.push(field_id);
                }
            }
        });

        $('input[type="checkbox"]', form).each(function () {
            field = $(this);
            field_id = field.attr('id');
            field_name = field.attr('name');
            if (typeof field_id != 'undefined') {
                if ($.inArray(field_id, fields_Checked) == -1) {
                    $('input[type="checkbox"][name="' + field_name + '"]').each(function () {
                        if ($(this).is(':checked')) {
                            data[field_id] = $(this).val();
                        } else {
                            data[field_id] = 0;
                        }
                    });
                    fields_Checked.push(field_id);
                }
            }
        });

        $('.ui.image-field', form).each(function () {
            field = $(this);
            if (typeof field.attr('id') != 'undefined') {
                data[field.attr('id')] = field.attr('data-image-id');
            }
        });

        return data;

    };

    /*FatSbMain.addItemDropdown = function(dropdown, item){
        if(dropdown.hasClass('multiple')){
            var drop_item = $('<a class="ui label transition visible" data-value="' + item.value +'" style="display: inline-block !important;">' + item.name +'<i class="delete icon"></i></a>'),
                input = $('input[type="hidden"]',dropdown);
            if(input.val()==''){
                input.val(item.value);
            }else{
                input.val(input.val() + ',' + item.value);
            }
            drop_item.insertBefore($('div.text',dropdown));
            $('.menu',dropdown).append('<div class="item active filtered" data-value="' + item.value + '">' + item.name + '</div>');
        }else{
            $('.menu .item.active.selected',dropdown).removeClass("active selected");
            $('.menu',dropdown).append('<div class="item active selected" data-value="' + item.value + '">' + item.name + '</div>');
            $(dropdown).dropdown('set selected',item.value);
            $('input[type="hidden"]',dropdown).attr('value',item.value);
            $('div.text',dropdown).html(item.name);
            if($(dropdown).hasClass('clearable')){
                $('i.dropdown.icon',dropdown).addClass('clear');
            }
        }
    };*/

    FatSbMain.initPaging = function (total, page, elm, callback) {

        var item_per_page = FatSbMain.data.item_per_page,
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
                $('.ui.pagination button.ui.button:not(.nav-disabled)', '.fat-semantic-container').off('click').on('click', function () {
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

    /** load dictionary **/
    FatSbMain.bindLocationDic = function (elm, callback) {
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_locations'
            }),
            success: function (locations) {
                locations = $.parseJSON(locations);
                if (elm.hasClass('fat-checkbox-dropdown-wrap')) {
                    var dropdown = $('select', elm);
                    for (var $i = 0; $i < locations.length; $i++) {
                        dropdown.append('<option value="' + locations[$i].loc_id + '">' + locations[$i].loc_name + '</option>');
                    }
                    dropdown[0].sumo.reload();
                } else {
                    var dropdown = $(elm).hasClass('multiple search') ? $('.menu .scrolling.menu', elm) : $('.menu', elm);
                    for (var $i = 0; $i < locations.length; $i++) {
                        dropdown.append('<div class="item" data-value="' + locations[$i].loc_id + '">' + locations[$i].loc_name + '</div>');
                    }
                }

                if (typeof callback == 'function') {
                    callback();
                }
            },
            error: function () {
            }
        });
    };

    FatSbMain.bindCustomersDic = function (elm, callback) {
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_customers_dic'
            }),
            success: function (customers) {
                customers = $.parseJSON(customers);

                if (elm.hasClass('fat-checkbox-dropdown-wrap')) {
                    var dropdown = $('select', elm);
                    for (var $i = 0; $i < customers.length; $i++) {
                        dropdown.append('<option value="' + customers[$i].c_id + '">' + customers[$i].c_first_name + ' ' + customers[$i].c_last_name + '</option>');
                    }
                    dropdown[0].sumo.reload();
                } else {
                    var dropdown = $(elm).hasClass('multiple search') ? $('.menu .scrolling.menu', elm) : $('.menu', elm);
                    for (var $i = 0; $i < customers.length; $i++) {
                        dropdown.append('<div class="item" data-value="' + customers[$i].c_id + '">' + customers[$i].c_first_name + ' ' + customers[$i].c_last_name + '</div>');
                    }
                }
                if (typeof callback == 'function') {
                    callback();
                }

            },
            error: function () {
            }
        });
    };

    FatSbMain.bindServicesDic = function (elm, callback) {
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services'
            }),
            success: function (services) {
                services = $.parseJSON(services);

                if (elm.hasClass('fat-checkbox-dropdown-wrap')) {
                    var dropdown = $('select', elm);
                    for (var $i = 0; $i < services.length; $i++) {
                        dropdown.append('<option value="' + services[$i].s_id + '">' + services[$i].s_name + '</option>');
                    }
                    dropdown[0].sumo.reload();
                } else {
                    var dropdown = $(elm).hasClass('multiple search') ? $('.menu .scrolling.menu', elm) : $('.menu', elm);
                    for (var $i = 0; $i < services.length; $i++) {
                        dropdown.append('<div class="item" data-value="' + services[$i].s_id + '">' + services[$i].s_name + '</div>');
                    }
                }
                if (typeof callback == 'function') {
                    callback();
                }

            },
            error: function () {
            }
        });
    };

    FatSbMain.bindServicesDicHierarchy = function (elm, callback) {
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services_hierarchy'
            }),
            success: function (services) {
                services = $.parseJSON(services);
                if (elm.hasClass('fat-checkbox-dropdown-wrap')) {
                    var dropdown = $('select', elm),
                        optgroup = '',
                        service = '';

                    for (var $key in services) {
                        service = services[$key];
                        optgroup = ' <optgroup label="' + service[0].sc_name + '">';
                        for (var $s_index = 0; $s_index < service.length; $s_index++) {
                            optgroup += '<option value="' + service[$s_index].s_id + '">' + service[$s_index].s_name + '</option>';

                        }
                        optgroup += '</optgroup>';
                        dropdown.append(optgroup);
                    }
                    dropdown[0].sumo.reload();
                } else {
                    var dropdown = $(elm).hasClass('multiple search') ? $('.menu .scrolling.menu', elm) : $('.menu', elm);
                    for (var $i = 0; $i < services.length; $i++) {
                        dropdown.append('<div class="item" data-value="' + services[$i].s_id + '">' + services[$i].s_name + '</div>');
                    }
                }
                if (typeof callback == 'function') {
                    callback();
                }

            },
            error: function () {
            }
        });
    };

    FatSbMain.bindEmployeesDic = function (elm, callback) {
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_employees_dic'
            }),
            success: function (employees) {
                employees = $.parseJSON(employees);

                if (elm.hasClass('fat-checkbox-dropdown-wrap')) {
                    var dropdown = $('select', elm);
                    for (var $i = 0; $i < employees.length; $i++) {
                        dropdown.append('<option value="' + employees[$i].e_id + '">' + employees[$i].e_first_name + ' ' + employees[$i].e_last_name + '</option>');
                    }
                    dropdown[0].sumo.reload();
                } else {
                    var dropdown = $(elm).hasClass('multiple search') ? $('.menu .scrolling.menu', elm) : $('.menu', elm);
                    for (var $i = 0; $i < employees.length; $i++) {
                        dropdown.append('<div class="item" data-value="' + employees[$i].e_id + '">' + employees[$i].e_name + '</div>');
                    }
                }
                if (typeof callback == 'function') {
                    callback();
                }

            },
            error: function () {
            }
        });
    };

    FatSbMain.registerOnChange = function(container){
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
                        if(self.hasClass('onChange-disabled')){
                            return;
                        }
                        if (obj != '') {
                            (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](value, text, $choice) : '';
                        } else {
                            (typeof window[func] != 'undefined' && window[func] != null) ? window[func](value, text, $choice) : '';
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
                if(self.hasClass('onChange-disabled')){
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

    FatSbMain.registerOnClick = function(container){
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

    FatSbMain.registerOnKeyUp = function(container){
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

    FatSbMain.registerEventProcess = function (container) {
        container = typeof container == 'undefined' ? $('.fat-semantic-container') : container;
        FatSbMain.registerOnChange(container);
        FatSbMain.registerOnClick(container);
        FatSbMain.registerOnKeyUp(container);
    };

    FatSbMain.getDateFormat = function(){
        var date_format = FatSbMain.data.date_format;
        date_format = date_format.replace('M', 'MMM');
        date_format = date_format.replace('F', 'MMMM');
        date_format = date_format.replace('m', 'MM');
        date_format = date_format.replace('n', 'M');

        date_format = date_format.replace('jS', 'DD');
        date_format = date_format.replace('j', 'D');
        date_format = date_format.replace('d', 'DD');
        date_format = date_format.replace('s', 'Mo');

        date_format = date_format.replace('Y', 'YYYY');

        return date_format;
    };

    FatSbMain.initCopyShortcode = function(){
        var clipboard = new ClipboardJS('.fat-sb-copy-clipboard');
        clipboard.on('success', function (e) {
            $('a.fat-sb-shortcode-tooltip[data-popup-id="popup_tooltip_shortcode"]').popup('hide');
            FatSbMain.showMessage(FatSbMain.data.clipboard_message);
        });
    };

    FatSbMain.equalDay = function ($date1, $date2) {
        return ( $date1.getDate() ==  $date2.getDate() ) && ( $date1.getMonth() == $date2.getMonth() ) && ( $date1.getFullYear() == $date2.getFullYear());
    };

    FatSbMain.i18n_daysOfWeek = function (locale){
        if(locale=='cs'){
            return ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'];
        }
        if(locale=='da'){
            return ['Søn', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør'];
        }
        if(locale=='de'){
            return ['Son', 'Mon', 'Die', 'Mit', 'Don', 'Fre', 'Sam'];
        }
        if(locale=='en'){
            return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        }
        if(locale=='es'){
            return ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
        }
        if(locale=='fi'){
            return ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe', 'La'];
        }
        if(locale=='fr'){
            return ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        }
        if(locale=='hu'){
            return ['Va', 'Hé', 'Ke', 'Sze', 'Cs', 'Pé', 'Szo'];
        }
        if(locale=='it'){
            return ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'];
        }
        if(locale=='ja'){
            return ['太陽', '月曜', '火', '水曜日', '木曜日', '金曜日', '土曜日'];
        }
        if(locale=='nl'){
            return ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];
        }
        if(locale=='pl'){
            return ['Nie', 'Pon', 'Wto', 'Śro', 'Czw', 'Pią', 'Sob'];
        }
        if(locale=='pt'){
            return ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
        }
        if(locale=='pt-BR'){
            return ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
        }
        if(locale=='ro'){
            return ['Dum', 'Lun', 'Mar', 'Mie', 'Joi', 'Vin', 'Sâm'];
        }
        if(locale=='sk'){
            return ['Ned', 'Pon', 'Uto', 'Str', 'Štv', 'Pia', 'Sob'];
        }
        if(locale=='zh'){
            return ['日', '一', '二', '三', '四', '五', '六'];
        }

        return  FatSbMain.data.day_of_week;
    };

    FatSbMain.i18n_monthName = function (locale){
        if(locale=='cs'){
            return ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'];
        }
        if(locale=='da'){
            return ['Januar','Februar','Marts','April','Maj','Juni', 'Juli','August','September','Oktober','November','December'];
        }
        if(locale=='de'){
            return ['Januar','Februar','März','April','Mai','Juni', 'Juli','August','September','Oktober','November','Dezember'];
        }
        if(locale=='en'){
            return ['January','February','March','April','May','June', 'July','August','September','October','November','December'];
        }
        if(locale=='es'){
            return ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Augosto','Septiembre','Octubre','Noviembre','Diciembre'];
        }
        if(locale=='fi'){
            return ['Tammikuu','Helmikuu','Maaliskuu','Huhtikuu','Toukokuu','Kesäkuu', 'Heinäkuu','Elokuu','Syyskuu','Lokakuu','Marraskuu','Joulukuu'];
        }
        if(locale=='fr'){
            return ['Janvier','Février','Mars','Avril','Mai','Juin', 'Juillet','Août','Septembre','Octobre','Novembre','Decembre'];
        }
        if(locale=='hu'){
            return ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'];
        }
        if(locale=='it'){
            return ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno', 'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
        }
        if(locale=='ja'){
            return ['一月','2月','行進','4月','5月','六月', '7月','8月','九月','10月','11月','12月'];
        }
        if(locale=='nl'){
            return ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'];
        }
        if(locale=='pl'){
            return ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec', 'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'];
        }
        if(locale=='pt'){
            return ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        }
        if(locale=='pt-BR'){
            return ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        }
        if(locale=='ro'){
            return ['Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie'];
        }
        if(locale=='sk'){
            return ['Január','Február','Marec','Apríl','Máj','Jún', 'Júl','August','September','Október','November','December'];
        }
        if(locale=='zh'){
            return ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'];
        }

        return  FatSbMain.data.month_name;
    };

    FatSbMain.moment_i18n = function(locale, str_date, date_format){
        var locale_format = locale,
            moment_date = '';
        if(str_date.indexOf('Jan') > -1 || str_date.indexOf('Feb') > -1 || str_date.indexOf('Mar') > -1 || str_date.indexOf('Apr') > -1 || str_date.indexOf('May') > -1
            || str_date.indexOf('Jun') > -1 || str_date.indexOf('Jul') > -1 || str_date.indexOf('Aug') > -1 || str_date.indexOf('Sep') > -1 || str_date.indexOf('Oct') > -1
            || str_date.indexOf('Nov') > -1 || str_date.indexOf('Dec') > -1){
            locale_format = 'en_US';
        }
        moment.locale(locale_format);
        moment_date = moment(str_date, date_format);
        moment.locale(locale);
        return moment_date;
    }

    $(document).ready(function () {
        FatSbMain.init();
    });
})(jQuery);