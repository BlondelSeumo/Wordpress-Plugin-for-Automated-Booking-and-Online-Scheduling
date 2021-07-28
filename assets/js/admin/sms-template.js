"use strict";
var FatSbSMSTemplate = {};
(function ($) {
    FatSbSMSTemplate.init = function () {
        FatSbMain.registerEventProcess($('.fat-sb-sms-template-container'));
        FatSbMain.initPopupToolTip();
    };

    FatSbSMSTemplate.menuOnClick = function(self){
        if(self.hasClass('active')){
            return;
        }
        $('a.active','.fat-sb-sms-template-container .ui.menu').removeClass('active');
        self.addClass('active');
        $('.fat-sb-get-customer-code-template').addClass('fat-hidden');
        $('.fat-sb-pending-template').show();

        $('.fat-sb-customer-label').html(self.attr('data-customer-title'));
        $('.fat-sb-employee-label').html(self.attr('data-employee-title'));
        FatSbSMSTemplate.initTemplate(self.attr('data-template'));
    };

    FatSbSMSTemplate.dependFieldOnChange = function(self){
        var id = self.attr('id'),
            value = self.val();
        $('[data-depend="' + id + '"]', '.fat-sb-sms-template-container').each(function () {
            var elm = $(this);
            if (self.is(':checked')) {
                elm.slideDown();
            } else {
                elm.slideUp();
            }
        });
    };

    FatSbSMSTemplate.submitTemplate = function(self){
        FatSbMain.showProcess(self);
        var template =  $('a.active','.fat-sb-sms-template-container .ui.menu').attr('data-template'),
            customer_template_enable = $('#customer_template_enable').is(':checked') ? 1 : 0,
            customer_message = $('#customer_template').val(),
            employee_template_enable = $('#employee_template_enable').is(':checked') ? 1 : 0,
            employee_message = $('#employee_template').val();

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_save_sms_template',
                data: {
                    'template' : template,
                    'customer_enable': customer_template_enable,
                    'customer_message' : customer_message,
                    'employee_enable': employee_template_enable,
                    'employee_message': employee_message,
                }
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                response = $.parseJSON(response);
                if (response.result > 0) {
                    FatSbMain.showMessage(self.attr('data-success-message'));

                    for(var $i=0; $i< fat_sb_sms_data.length; $i++){
                        if(fat_sb_sms_data[$i]['template'] == template){
                            fat_sb_sms_data[$i]['customer_enable'] = customer_template_enable;
                            fat_sb_sms_data[$i]['customer_message'] = customer_message;

                            fat_sb_sms_data[$i]['employee_enable'] = employee_template_enable;
                            fat_sb_sms_data[$i]['employee_message'] = employee_message;
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

    FatSbSMSTemplate.initTemplate = function(template){
        var customer_enable = 0,
            customer_message = '',
            employee_enable = 0,
            employee_message = '';

        for(var $i=0; $i< fat_sb_sms_data.length; $i++){
            if(fat_sb_sms_data[$i]['template'] == template){
                var data = fat_sb_sms_data[$i];
                customer_enable = data['customer_enable'];
                customer_message = data['customer_message'];

                employee_enable = data['employee_enable'];
                employee_message = data['employee_message'];
            }
        }

        $('#customer_template_enable').prop("checked", customer_enable==1);
        $('#customer_template').html(customer_message);
        if ($('#customer_template_enable').is(':checked')) {
            $('.fields.customer-template').slideDown();
        } else {
            $('.fields.customer-template').hide();
        }

        $('#employee_template_enable').prop("checked", employee_enable==1);
        $('#employee_template').html(employee_message);
        if ($('#employee_template_enable').is(':checked')) {
            $('.fields.employee-template').slideDown();
        } else {
            $('.fields.employee-template').hide();
        }

    };

    $(document).ready(function () {
        FatSbSMSTemplate.init();
    });
    $(window).load(function(){
        setTimeout(function(){
            var template = $('a.active','.fat-sb-sms-template-container .ui.menu').attr('data-template');
            FatSbSMSTemplate.initTemplate(template);
        },500);
    });
})(jQuery);