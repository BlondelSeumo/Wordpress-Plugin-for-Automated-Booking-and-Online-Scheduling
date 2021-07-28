"use strict";
var FatSbCalendar = {
    view: 'month' //listWeek, month, agendaDay, agendaWeek
};
(function ($) {
    FatSbCalendar.init = function () {
        FatSbCalendar.view = $('#fat_sb_calendar').attr('data-view');
        FatSbCalendar.initCalendar([]);
        FatSbCalendar.loadBooking();

        FatSbMain.bindServicesDicHierarchy($('.fat-sb-calendar-container .fat-sb-service-dic'));
        FatSbMain.bindEmployeesDic($('.fat-sb-calendar-container .fat-sb-employee-dic'));
        FatSbMain.bindCustomersDic($('.fat-sb-calendar-container .fat-sb-customer-dic'));
        FatSbMain.bindLocationDic($('.fat-sb-calendar-container .fat-sb-location-dic'));
        FatSbMain.initField();

        FatSbMain.registerEventProcess($('.fat-sb-calendar-container .toolbox-action-group'));

    };

    FatSbCalendar.dateOnChange = function (self) {
        var date_picker = self.closest('.ui.date-input');
        $('.ui.loader', date_picker).remove();
        date_picker.addClass('fat-loading');
        date_picker.append('<div class="ui active tiny inline loader"></div>');

        if (self.attr('data-start') == self.attr('data-end')) {
            FatSbCalendar.view = 'agendaDay';
            $('#fat_sb_calendar').fullCalendar('changeView',FatSbCalendar.view);
            $('#fat_sb_calendar').fullCalendar('gotoDate', moment(self.attr('data-start'), 'YYYY-MM-DD'));
        }else{
            FatSbCalendar.view = $('#fat_sb_calendar').attr('data-view');
            $('#fat_sb_calendar').fullCalendar('changeView',FatSbCalendar.view);
        }
        FatSbCalendar.loadBooking(function () {
            $('.ui.loader', date_picker).remove();
            date_picker.removeClass('fat-loading');
        });
    };

    FatSbCalendar.sumoSearchOnChange = function (self) {
        var sumoContainer = self.closest('.SumoSelect'),
            prev_value = self.attr('data-prev-value'),
            value = self.val();

        value = value != null ? value : '';

        if (value != prev_value) {
            $('.ui.loader', sumoContainer).remove();
            sumoContainer.addClass('fat-loading');
            sumoContainer.append('<div class="ui active tiny inline loader"></div>');
            self.attr('data-prev-value', value);
            FatSbCalendar.loadBooking(function () {
                $('.ui.loader', sumoContainer).remove();
                sumoContainer.removeClass('fat-loading');
            });
        }
    };

    FatSbCalendar.loadBooking = function (callback) {
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_booking_calendar',
                from_date: $('input#date_of_book').attr('data-start'),
                to_date: $('input#date_of_book').attr('data-end'),
                employee: $('#employees').val(),
                customer: $('#customer').val(),
                service: $('#services').val(),
                location: $('#location').val()
            }),
            success: function (response) {
                response = $.parseJSON(response);
                $('.ui.inverted.dimmer', '.fat-sb-calendar').fadeOut(function () {
                    $('.ui.inverted.dimmer', '.fat-sb-calendar').remove();
                });
                var elm_calendar = $('#fat_sb_calendar');
                $('#fat_sb_calendar').fullCalendar('gotoDate', moment(response.date));
                elm_calendar.fullCalendar('removeEvents');
                elm_calendar.fullCalendar('addEventSource', response.bookings);
                //elm_calendar.fullCalendar('rerenderEvents');
                elm_calendar.fullCalendar("reinitView");

                if (typeof callback == 'function') {
                    callback();
                }

            },
            error: function (response) {
                FatSbMain.showMessage(fat_sb_data.error_message, 2);
            }
        })
    };

    FatSbCalendar.initCalendar = function (data) {
        var locale = typeof $('#fat_sb_calendar').attr('data-locale')!='undefined' && $('#fat_sb_calendar').attr('data-locale')!='' ? $('#fat_sb_calendar').attr('data-locale') : 'en';
        $('#fat_sb_calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listWeek'
            },
            locale: locale,
            defaultView: FatSbCalendar.view,
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            events: data.bookings,
            eventRender: function (eventObj, $el) {
                var popup_id = FatSbMain.guid(),
                    popup = wp.template('fat-sb-popup-calendar-template'),
                    popup = $(popup(eventObj));

                $(popup).attr('data-popup-id', popup_id);
                $el.attr('data-popup-id', popup_id);
                $el.attr('data-popup-inline', false);
                $el.append(popup);
                $('.fc-title', $el).html(eventObj.customer);
                $('.fc-content', $el).append('<div>' + eventObj.time + '</div>');
                $('.fc-content', $el).append('<div>' + eventObj.service + '</div>');
                $('.fc-content', $el).append('<div>' + eventObj.employee + '</div>');
            },
            viewRender: function (view, element) {
                FatSbCalendar.initPopupEvent();
                FatSbMain.registerEventProcess($('#fat_sb_calendar'));
            }
        });
    };

    FatSbCalendar.initPopupEvent = function () {
        $('.fat-sb-calendar-container .fc-event').each(function () {
            var self = $(this),
                popup_id = self.attr('data-popup-id'),
                popup = $('.ui.popup[data-popup-id="' + popup_id + '"]');

            if (popup.length > 0) {
                self.popup({
                    popup: popup,
                    inline: false,
                    hoverable: true,
                })
            }
        });

        $('.fat-sb-calendar-container .fc-list-table tr.fc-list-item td.fc-list-item-title').each(function () {
            $(this).popup({
                popup: $('.fat-sb-calendar-popup', this),
                inline: false,
                hoverable: true,
            })
        });

        $('.fc-more-cell', '.fat-sb-calendar').off('click').on('click', function () {
            setTimeout(function () {
                $('.fat-sb-calendar-container .fc-event').each(function () {
                    var self = $(this),
                        popup_id = self.attr('data-popup-id'),
                        popup = $('.ui.popup[data-popup-id="' + popup_id + '"]');
                    if (popup.length > 0) {
                        self.popup({
                            popup: popup,
                            inline: false,
                            hoverable: true,
                        })
                    }
                });
                $('[data-onClick]', '#fat_sb_calendar').off('click');
                FatSbMain.registerEventProcess($('#fat_sb_calendar'));
            }, 800)
        });

    };

    FatSbCalendar.addBookingToCalendar = function(booking){
        var elm_calendar = $('.fat-sb-calendar');

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_booking_calendar_by_id',
                b_id: booking.b_id,
            }),
            success: function (response) {
                response = $.parseJSON(response);

                elm_calendar.fullCalendar( 'removeEvents', response.b_id);
                elm_calendar.fullCalendar('renderEvent', {
                    id: response.b_id,
                    title: response.s_name ,
                    start: response.start,
                    end: response.end,
                    service: response.s_name,
                    employee: (response.e_first_name + ' ' + response.e_last_name),
                    e_avatar_url: response.e_avatar_url,
                    customer: (response.c_first_name + ' ' + response.c_last_name),
                    time: response.time,
                    location: response.loc_name,
                    location_address: response.loc_address,
                    color: fat_sb_data.booking_color[response.b_process_status],
                    b_editable: response.b_editable
                });
                elm_calendar.fullCalendar("reinitView");
            },
            error: function() {
                
            }
        });
    };

    $(document).ready(function () {
        if ($('.fat-sb-calendar-container').length > 0) {
            FatSbCalendar.init();
        }
    });
})(jQuery);