"use strict";
var FatSbService = {
    keyword: null,
    s_min_cap: 0,
    s_max_cap: 0,
    s_price: 0
};
(function ($) {
    FatSbService.init = function () {
        FatSbService.loadServiceCategory();
        FatSbService.loadServices();
        FatSbMain.registerEventProcess($('.fat-sb-services-container .toolbox-action-group'));
        FatSbMain.initPopupToolTip();
    };

    FatSbService.initButtonToolTip = function () {
        $('.fat-item-bt-inline[data-title]', '.fat-semantic-container').each(function () {
            var position = $(this).attr('data-position'),
                option = {
                    title: '',
                    content: $(this).attr('data-title'),
                    inline: true
                };
            if(typeof position!='undefined'){
                option['position'] = position;
            }
            $(this).popup(option);
        });
    };

    /*
    Load data
    */
    FatSbService.loadServiceCategory = function () {
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_service_category'
            }),
            success: function (categories) {
                categories = $.parseJSON(categories);
                var template = wp.template('fat-sb-category-item-template'),
                    items = $(template(categories)),
                    elm_category = $('.fat-sb-list-services-category');

                $('.ui.placeholder', elm_category).remove();
                $('.fat-sb-not-found', elm_category).remove();
                if (categories.length > 0) {
                    elm_category.addClass('owl-carousel');
                    elm_category.append(items);
                    FatSbMain.registerEventProcess($('.fat-sb-list-services-category'));
                    FatSbService.initButtonToolTip();
                } else {
                    FatSbMain.showNotFoundMessage(elm_category);
                }
                FatSbMain.initCarousel(elm_category);
            },
            error: function () {
            }
        })
    };

    FatSbService.loadServicesByName = function (key, callback) {
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services_by_name',
                key: key
            }),
            success: function (services) {
                try {
                    services = $.parseJSON(services);
                    var template = wp.template('fat-sb-service-item-template'),
                        items = $(template(services)),
                        elm_services = $('.fat-sb-list-services');

                    $('> .column', elm_services).remove();
                    $('.fat-sb-not-found', elm_services).remove();
                    if (services.length > 0) {
                        elm_services.append(items);
                        FatSbMain.registerEventProcess($('.fat-sb-list-services'));
                        FatSbService.initButtonToolTip();
                    } else {
                        FatSbMain.showNotFoundMessage(elm_services);
                    }
                } catch (err) {
                }
                if (typeof callback == 'function') {
                    callback();
                }
            },
            error: function () {
                if (typeof callback == 'function') {
                    callback();
                }
            }
        });
    };

    FatSbService.loadServices = function (sc_id, callback) {
        sc_id = typeof sc_id != 'undefined' ? sc_id : 0;
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_services',
                sc_id: sc_id
            }),
            success: function (services) {
                services = $.parseJSON(services);
                for (var $i = 0; $i < services.length; $i++) {
                    services[$i]['s_duration_label'] = FatSbMain.data.durations[services[$i].s_duration];
                }
                var template = wp.template('fat-sb-service-item-template'),
                    items = $(template(services)),
                    elm_services = $('.fat-sb-list-services');

                $('> .column', elm_services).remove();
                $('.fat-sb-not-found', elm_services).remove();
                if (services.length > 0) {
                    elm_services.append(items);
                    FatSbMain.registerEventProcess($('.fat-sb-list-services'));
                    FatSbService.initButtonToolTip();
                } else {
                    FatSbMain.showNotFoundMessage(elm_services);
                }

                if (typeof callback == 'function') {
                    callback();
                }
            },
            error: function () {
                if (typeof callback == 'function') {
                    callback();
                }
            }
        })
    };

    FatSbService.loadServiceByCat = function (self) {
        var sc_id = self.attr('data-id');
        FatSbMain.showLoading();
        FatSbService.loadServices(sc_id, function () {
            $('.fat-sb-list-services-category .item.fat-active').removeClass('fat-active');
            self.addClass('fat-active');
            FatSbMain.closeLoading();
            FatSbService.initButtonToolTip();
        });
    };

    /*
    Process event
     */
    FatSbService.addCategoryOnClick = function (self) {
        FatSbMain.showPopup('fat-sb-services-category-template', '', [], function () {
            var callback = self.attr('data-callback');
            if (typeof callback != 'undefined' && callback != '') {
                $('.fat-sb-category-form .fat-submit-modal').attr('data-callback', callback);
            }
            FatSbMain.registerEventProcess($('.fat-sb-category-form'));
        });
    };

    FatSbService.serviceNameKeyUp = function (self) {
        var search_wrap = self.closest('.ui.input');
        if (self.val().length >= 3 || self.val() == '') {
            if (FatSbService.keyword == self.val()) {
                return;
            }
            search_wrap.addClass('loading');
            $('.fat-sb-list-services-category .item.active').removeClass('fat-active');

            FatSbService.keyword = self.val();
            FatSbService.loadServicesByName(self.val(), function () {
                FatSbService.keyword = null;
                search_wrap.removeClass('loading');
            });

            if (self.val().length >= 3) {
                search_wrap.addClass('active-search');
            }
            if (self.val() == '') {
                search_wrap.removeClass('active-search');
            }
        }
    };

    FatSbService.clearSearchOnClick = function (self) {
        var search_wrap = self.closest('.ui.ui-search');
        $('input', search_wrap).val('');
        $('input', search_wrap).trigger('keyup');
    };

    /*
    Process data
    */
    FatSbService.processViewCategoryDetail = function (self) {
        var item = self.closest('.item'),
            data = {
                sc_id: item.attr('data-id'),
                sc_image_id: $('.ui.image img', item).attr('data-image-id'),
                sc_image_url: $('.ui.image img', item).attr('src'),
                sc_name: $('.content .header', item).html(),
                sc_description: $('.description', item).html()
            };
        FatSbMain.showPopup('fat-sb-services-category-template', FatSbMain.data.modal_title.edit_category, data, function () {
            FatSbMain.registerEventProcess($('.fat-sb-category-form'));
        });
    };

    FatSbService.processSubmitCategory = function (self) {
        if (FatSbMain.isFormValid) {
            var modal = self.closest('.ui.modal'),
                img_url = $('#sc_image_id img', modal).attr('src'),
                callback = typeof self.attr('data-callback') != 'undefined' ? self.attr('data-callback').split('.') : '',
                data = {
                    sc_id: self.attr('data-id'),
                    sc_image_id: $('#sc_image_id').attr('data-image-id'),
                    sc_name: $('#sc_name').val(),
                    sc_description: $('#sc_description').val(),
                };
            FatSbMain.showProcess(self);
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_save_service_category',
                    data: data
                }),
                success: function (response) {
                    FatSbMain.closeProcess(self);
                    modal.modal('hide');

                    response = $.parseJSON(response);

                    $('.fat-sb-not-found', '.fat-sb-list-services-category').remove();
                    if (response.result >= 0) {
                        var item = $('.fat-sb-list-services-category .item[data-id="' + data.sc_id + '"]');
                        data.sc_image_url = typeof img_url != 'undefined' ? img_url : '';
                        data.sc_total_service = 0;

                        if (item.length == 0) {
                            data.sc_id = response.result;
                            var template = wp.template('fat-sb-category-item-template'),
                                item = $(template([data]));
                            $('.fat-sb-list-services-category').owlCarousel('add', item);
                            FatSbMain.initCarousel($('.fat-sb-list-services-category'));
                            FatSbMain.registerEventProcess(item);
                        } else {
                            if ($('span.fat-no-thumb', item).length > 0 && image_url != '') {
                                $('span.fat-no-thumb', item).remove();
                                $('.image', item).append(' <img class="fat-border-round fat-box-shadow fat-img-80"></img>');
                            }
                            $('img', item).attr('src', data.sc_image_url);
                            $('.content .header', item).html(data.sc_name);
                            $('.description', item).html(data.sc_description);
                            FatSbMain.showMessage(self.attr('data-success-message'));
                        }

                        if (callback != '') {
                            var obj = callback.length == 2 ? callback[0] : '',
                                func = callback.length == 2 ? callback[1] : callback[0];
                            if (obj != '') {
                                (typeof window[obj][func] != 'undefined' && window[obj][func] != null) ? window[obj][func](data) : '';
                            } else {
                                (typeof window[func] != 'undefined' && window[func] != null) ? window[func](data) : '';
                            }
                        }
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
                    FatSbMain.showMessage(FatSbMain.data.error_message);
                }
            });
        }
    };

    FatSbService.processDeleteCategory = function (self) {
        var bt_delete = self,
            sc_id = bt_delete.attr('data-id'),
            owl_stage = bt_delete.closest('.owl-stage '),
            index = $('.owl-item', owl_stage).index(bt_delete.closest('.owl-item'));

        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title, FatSbMain.data.confirm_delete_message, function (result, popup) {
            if (result == 1) {
                var self = $('.fat-sb-bt-confirm.yes', popup);
                FatSbMain.showProcess(self);
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_service_category',
                        sc_id: sc_id
                    }),
                    success: function (response) {
                        FatSbMain.closeProcess(self);
                        self.closest('.ui.modal').modal('hide');

                        response = $.parseJSON(response);
                        if (response.result > 0) {
                            $('.owl-carousel').data('owl.carousel').remove(index)
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
                        FatSbMain.showMessage(FatSbMain.data.error_message);
                    }
                });
            }
        });
    };

    FatSbService.processSubmitService = function (self) {
        if (FatSbMain.isFormValid) {
            var data = FatSbMain.getFormData('.ui.modal.fat-services-modal .ui.form'),
                extra_field = {};

            $('.fat-sb-extra-field','.ui.modal.fat-services-modal .ui.form').each(function(){
                extra_field[$(this).attr('id')] = $(this).val();
            });
            if (typeof self.attr('data-id') != 'undefined' && self.attr('data-id') != '') {
                data.s_id = self.attr('data-id');
                if (data.s_minimum_person != FatSbService.s_min_cap || data.s_maximum_person != FatSbService.s_max_cap || data.s_price != FatSbService.s_price) {
                    var popup_id = self.attr('data-popup-id'),
                        popup = $('.ui.popup[data-popup-id="' + popup_id + '"]');
                    self.popup({
                        position: 'top right',
                        popup: popup,
                        on: 'click',
                        inline: true,
                        hoverable: true,
                    }).popup('toggle');

                    $('.fat-bt-confirm-cancel', '.fat-popup-submit-service-confirm').off('click').on('click', function () {
                        self.popup('hide');
                        FatSbMain.showProcess(self);
                        FatSbService.submitService(self, data, extra_field, 0);
                    });

                    $('.fat-bt-confirm-ok', '.fat-popup-submit-service-confirm').off('click').on('click', function () {
                        self.popup('hide');
                        FatSbMain.showProcess(self);
                        FatSbService.submitService(self, data, extra_field, 1);
                    });

                } else {
                    FatSbMain.showProcess(self);
                    FatSbService.submitService(self, data,extra_field, 0);
                }
            } else {
                FatSbMain.showProcess(self);
                FatSbService.submitService(self, data,extra_field, 0);
            }
        }
    };

    FatSbService.submitService = function (self, data, extra_field, update_employee) {
        var duration_label = $('#s_duration_label').html(),
            image_url = $('#s_image_id img').attr('src');

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_save_service',
                data: data,
                extra_field: extra_field,
                upd_e: update_employee
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                self.closest('.ui.modal').modal('hide');
                response = $.parseJSON(response);

                if (response.result >= 0) {
                    FatSbMain.showMessage(self.attr('data-success-message'));
                    $('.fat-sb-list-services .fat-sb-not-found').remove();

                    //update back to list
                    var item = $('.fat-sb-list-services .item[data-id="' + data.s_id + '"]');
                    data.s_image_url = typeof image_url != 'undefined' ? image_url : '';
                    data.s_duration_label = duration_label;
                    if (item.length == 0) {
                        data.s_id = response.result;
                        var template = wp.template('fat-sb-service-item-template'),
                            item = $(template([data]));
                        $('.fat-sb-list-services').append(item);
                        FatSbMain.registerEventProcess(item);

                    } else {
                        $('.header', item).html(data.s_name);
                        $('.duration-label', item).html(data.s_duration_label);
                        $('.price', item).html(data.s_price);
                        if ($('span.fat-no-thumb', item).length > 0 && image_url != '') {
                            $('span.fat-no-thumb', item).remove();
                            $('.image', item).append(' <img class="fat-border-round fat-box-shadow fat-img-80"></img>');
                        }
                        $('img', item).attr('src', data.s_image_url);
                    }

                    if(typeof response.cats!='undefined'){
                        for( var id in response.cats ) {
                            $('.fat-sb-list-services-category .item[data-id="' + id + '"] .category-total-service','.fat-sb-services-container').text(response.cats[id]);
                        }
                    }
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

    FatSbService.processDeleteService = function (self) {
        var s_id = self.attr('data-id'),
            s_cat_id = self.attr('data-cat-id');
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title, FatSbMain.data.confirm_delete_message, function (result, popup) {
            if (result == 1) {
                var self = $('.fat-sb-bt-confirm.yes', popup);
                FatSbMain.showProcess(self);
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_service',
                        s_id: s_id
                    }),
                    success: function (response) {
                        try {
                            FatSbMain.closeProcess(self);
                            self.closest('.ui.modal').modal('hide');
                            response = $.parseJSON(response);
                            if (response.result > 0) {
                                $('.fat-sb-list-services .item[data-id="' + s_id + '"]').closest('.column').remove();

                                var elm_total = $('.fat-sb-list-services-category .item[data-id="' + s_cat_id + '"] .category-total-service','.fat-sb-services-container');
                                if(typeof elm_total!='undefined'){
                                    var total = elm_total.attr('data-total');
                                    if(typeof total!='undefined' && !isNaN(total)){
                                        total = parseInt(total) - 1;
                                        total = total > 0 ? total : 0;
                                        elm_total.attr('data-total', total);
                                        elm_total.text(total);
                                    }
                                }

                                if ($('.fat-sb-list-services .item').length == 0) {
                                    FatSbMain.showNotFoundMessage($('.fat-sb-list-services'));
                                }
                            } else {
                                if(typeof response.message!='undefined'){
                                    FatSbMain.showMessage(response.message, 3);
                                }else{
                                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                                }
                            }
                        } catch (err) {
                            FatSbMain.closeProcess(self);
                            FatSbMain.showMessage(FatSbMain.data.error_message);
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

    FatSbService.processServiceWorkDay = function(self){
        var s_id = self.attr('data-id');
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_get_service_work_day',
                s_id: s_id
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                response = $.parseJSON(response);

                FatSbMain.showPopup('fat-sb-service-work-day', '', response, function () {
                    FatSbMain.registerEventProcess($('.fat-service-work-day-form'));
                    $('.fat-submit-modal', '.fat-service-work-day-form').attr('data-id', s_id);
                    var work_day_wrap = $('.fat-work-day-wrap');
                    for(var $i=0; $i< response.length; $i++){
                        FatSbService.addWorkDayItem(work_day_wrap, response[$i].from_date, response[$i].to_date);
                    }
                });
            },
            error: function () {
                FatSbMain.closeProcess(self);
            }
        });
    };

    FatSbService.btAddWorkDayOnClick = function(self){
        var work_day_wrap = self.closest('.fat-work-day-wrap');
        FatSbService.addWorkDayItem(work_day_wrap);
    };

    FatSbService.addWorkDayItem = function (work_day_wrap, start, end) {
        var template = wp.template('fat-sb-service-item-work-day'),
            work_day_item = $(template([])),
            date_format =  FatSbMain.getDateFormat();

        $('input[name="service_work_day"]', work_day_item).attr('data-start', start);
        $('input[name="service_work_day"]', work_day_item).attr('data-end', end);

        $('.fat-work-day-inner', work_day_wrap).append(work_day_item);

        if ($.isFunction($.fn.daterangepicker)) {
            $('input.date-range-picker', work_day_item).each(function () {
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

        $('.fat-bt-remove-work-day').off('click').on('click', function () {
            $(this).closest('.fat-sb-work-day-item').remove();
        });
    };

    FatSbService.processSubmitWorkDay = function(self){
        var work_day = [],
            s_id = self.attr('data-id');
        $('input.date-range-picker','.fat-work-day-wrap').each(function(){
            work_day.push({s_id: s_id, from_date: $(this).attr('data-start'), to_date: $(this).attr('data-end')});
        });
        FatSbMain.showProcess(self);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_save_service_work_day',
                s_id: s_id,
                data: work_day
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                response = $.parseJSON(response);
                if(response.result >= 0){
                    self.closest('.ui.modal').modal('hide');
                    FatSbMain.showMessage(self.attr('data-success-message'));
                }else{
                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                }

            },
            error: function () {
                FatSbMain.closeProcess(self);
                FatSbMain.showMessage(FatSbMain.data.error_message, 2);
            }
        });
    };

    FatSbService.showPopupService = function (elm) {
        var s_id = typeof elm.attr('data-id') != 'undefined' ? elm.attr('data-id') : 0,
            popup_title = typeof s_id != 'undefined' ? FatSbMain.data.modal_title.edit_service : '';
        FatSbMain.showProcess(elm);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_get_service_by_id',
                s_id: s_id
            }),
            success: function (response) {
                FatSbMain.closeProcess(elm);
                response = $.parseJSON(response);

                FatSbService.s_min_cap = !isNaN(response.service.s_minimum_person) ? parseInt(response.service.s_minimum_person) : 0;
                FatSbService.s_max_cap = !isNaN(response.service.s_maximum_person) ? parseInt(response.service.s_maximum_person) : 0;
                FatSbService.s_price = !isNaN(response.service.s_price) ? parseFloat(response.service.s_price) : 0;

                FatSbMain.showPopup('fat-sb-services-template', popup_title, response, function () {
                    FatSbMain.registerEventProcess($('.fat-services-modal'));
                });
            },
            error: function () {
            }
        });
    };

    FatSbService.addServiceExtraToDropdown = function (data) {
        $('.service-extra .ui.dropdown .menu', '.fat-services-modal').append('<div class="item" data-value="' + data.se_id + '">' + data.se_name + '</div>');
        $('.service-extra .ui.dropdown', '.fat-services-modal').dropdown('refresh').dropdown('set selected', data.se_id);
    };

    FatSbService.addCategoryToDropdown = function (data) {
        $('.services-category .menu', '.fat-services-modal').append('<div class="item" data-value="' + data.sc_id + '">' + data.sc_name + '</div>');
        $('.services-category .ui.dropdown', '.fat-services-modal').dropdown('refresh').dropdown('set selected', data.sc_id);
    };

    $(document).ready(function () {
        if($('.fat-sb-services-container').length > 0){
            FatSbService.init();
        }
    });
})(jQuery);