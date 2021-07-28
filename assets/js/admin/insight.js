"use strict";
var FatSbInsight = {
    revenue_chart: null,
    service_emp_chart: null,
    customer_char: null
};

(function ($) {
    'use strict';

    FatSbInsight.init = function () {
        FatSbInsight.initField();
        FatSbInsight.loadInsight();
        FatSbMain.registerOnChange($('.fat-sb-insight-container'));
        FatSbMain.initPopupToolTip();
    };

    FatSbInsight.initField = function(){
        //date range picker
        if ($.isFunction($.fn.daterangepicker)) {
            var date_format = FatSbMain.getDateFormat();

            $('input.date-range-picker').attr('autocomplete', 'off');
            $('input.date-range-picker').each(function () {
                var self = $(this),
                    locale = typeof self.attr('data-locale') !='undefined' && self.attr('data-locale')!='' ? self.attr('data-locale') : '',
                    start_date = self.attr('data-start-init'),
                    end_date = self.attr('data-end-init'),
                    autoUpdate = typeof self.attr('data-auto-update') != 'undefined' && self.attr('data-auto-update') == '1',
                    options = {
                        autoUpdateInput: autoUpdate,
                        autoApply: true,
                        locale: {
                            format: date_format,
                            applyLabel: FatSbMain.data.apply_title,
                            cancelLabel: FatSbMain.data.cancel_title,
                            fromLabel: FatSbMain.data.from_title,
                            toLabel: FatSbMain.data.to_title,
                            daysOfWeek: FatSbMain.i18n_daysOfWeek(locale),
                            monthNames: FatSbMain.i18n_monthName(locale)
                        }
                    };

                if(locale!=''){
                    moment.locale(locale);
                }
                if (typeof start_date != 'undefined' && start_date != '') {
                    options.startDate = FatSbMain.moment_i18n(locale, start_date, date_format);
                }
                if (typeof end_date != 'undefined' && end_date != '') {
                    options.endDate =  FatSbMain.moment_i18n(locale, end_date, date_format);
                }

                self.daterangepicker(options, function (start, end, label) {
                    self.val(label);
                    self.attr('data-start', start.format('YYYY-MM-DD'));
                    self.attr('data-end', end.format('YYYY-MM-DD'));
                });
            });
        }
    };

    FatSbInsight.searchDateOnChange = function (self) {
        var date_picker = self.closest('.ui.date-input');
        $('.ui.loader', date_picker).remove();
        date_picker.addClass('fat-loading');
        date_picker.append('<div class="ui active tiny inline loader"></div>');
        FatSbInsight.loadInsight(function () {
            $('.ui.loader', date_picker).remove();
            date_picker.removeClass('fat-loading');
        });
    };

    FatSbInsight.loadInsight = function(callback){
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data:{
                action: 'fat_sb_get_insight',
                start_date: $('#date_insight').attr('data-start'),
                end_date: $('#date_insight').attr('data-end')
            },
            success: function(response){
                response = $.parseJSON(response);

                var currency = $('.booking-revenue').attr('data-currency');
                $('.booking-pending').text(response.booking_pending);
                $('.booking-approved').text(response.booking_approved);
                $('.booking_rejected').text(response.booking_rejected);
                $('.booking-cancelled').text(response.booking_canceled);
                $('.booking-revenue').text(response.total_revenue + currency);

                FatSbInsight.initChart(response);
                if (callback) {
                    callback();
                }
            },
            error: function(){

            }
        })
    };

    FatSbInsight.initChart = function(data){
        /* services & employee chart */
        var options = {
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'rounded',
                    columnWidth: '55%',
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series: [{
                name: FatSbMain.data.insight_employee,
                data: data.service_emp_chart.employees
            }, {
                name: FatSbMain.data.insight_services,
                data: data.service_emp_chart.services
            }],
            xaxis: {
                categories: data.service_emp_chart.categories
            },
            yaxis: {
                title: {
                    text: ''
                }
            },
            fill: {
                opacity: 1

            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return  val
                    }
                }
            }
        };
        if(typeof FatSbInsight.service_emp_chart!='undefined' && FatSbInsight.service_emp_chart!=null){
            FatSbInsight.service_emp_chart.destroy();
        }
        FatSbInsight.service_emp_chart = new ApexCharts(
            document.querySelector("#service_employee_chart"),
            options
        );
        FatSbInsight.service_emp_chart.render();

        /* chart revenue */

        var options = {
            chart: {
                height: 350,
                type: "line",
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            colors: ["#2185d0"],
            series: [
                {
                    name: FatSbMain.data.insight_revenue,
                    data: data.revenue
                }
            ],
            stroke: {
                width: [4, 4]
            },
            plotOptions: {
                bar: {
                    columnWidth: "20%"
                }
            },
            xaxis: {
                categories:data.service_emp_chart.categories,
            },
            yaxis: [
                {
                    axisTicks: {
                        show: true
                    },
                    axisBorder: {
                        show: true,
                    },
                    title: {
                        text: FatSbMain.data.insight_revenue
                    }
                },
            ],
            tooltip: {
                shared: false,
                intersect: true,
                x: {
                    show: false
                }
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 40
            }
        };
        if(typeof FatSbInsight.revenue_chart!='undefined' && FatSbInsight.revenue_chart!=null){
            FatSbInsight.revenue_chart.destroy();
        }
        FatSbInsight.revenue_chart = new ApexCharts(
            document.querySelector("#revenue_chart"),
            options
        );
        FatSbInsight.revenue_chart.render();

        /** init chart percent */
        var options = {
            chart: {
                type: 'donut',
            },
            colors: ["#2185d0","#00FF96"],
            series: [data.new_customer, data.return_customer],
            labels: [FatSbMain.data.insight_new_customer, FatSbMain.data.insight_return_customer],
        };
        if(data.new_customer == 0 && data.return_customer==0){
            options.colors = ['#808080','#2185d0','#00FF96'];
            options.series = [1,0,0];
            options.labels = ['',FatSbMain.data.insight_new_customer, FatSbMain.data.insight_return_customer];
            options.dataLabels = {enabled: false};
        }
        if(typeof FatSbInsight.customer_char!='undefined' && FatSbInsight.customer_char!=null){
            FatSbInsight.customer_char.destroy();
        }
        FatSbInsight.customer_char = new ApexCharts(
            document.querySelector("#customer_chart_percent"),
            options
        );

        FatSbInsight.customer_char.render();
    };

    $(document).ready(function () {
        if($('.fat-sb-insight-container').length > 0){
            FatSbInsight.init();
        }
    });
})(jQuery);