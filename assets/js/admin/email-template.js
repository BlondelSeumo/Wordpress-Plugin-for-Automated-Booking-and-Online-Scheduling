"use strict";
var FatSbEmailTemplate = {};
(function ($) {
    FatSbEmailTemplate.init = function () {
        FatSbMain.registerEventProcess($('.fat-sb-email-template-container'));
        FatSbMain.initPopupToolTip();
    };

    FatSbEmailTemplate.menuOnClick = function(self){
        if(self.hasClass('active')){
            return;
        }
        $('a.active','.fat-sb-email-template-container .ui.menu').removeClass('active');
        self.addClass('active');
        if(self.hasClass('fat-sb-customer-code')){
            $('.fat-sb-get-customer-code-template').removeClass('fat-hidden');
            $('.fat-sb-pending-template').hide();

            FatSbEmailTemplate.initGetCodeTemplate(self.attr('data-template'));
        }else{
            $('.fat-sb-get-customer-code-template').addClass('fat-hidden');
            $('.fat-sb-pending-template').show();

            $('.fat-sb-customer-label').html(self.attr('data-customer-title'));
            $('.fat-sb-employee-label').html(self.attr('data-employee-title'));
            FatSbEmailTemplate.initTemplate(self.attr('data-template'));
        }

    };

    FatSbEmailTemplate.dependFieldOnChange = function(self){
        var id = self.attr('id'),
            value = self.val();
        $('[data-depend="' + id + '"]', '.fat-sb-email-template-container').each(function () {
            var elm = $(this);
            if (self.is(':checked')) {
                elm.slideDown();
            } else {
                elm.slideUp();
            }
        });
    };

    FatSbEmailTemplate.submitTemplate = function(self){
        FatSbMain.showProcess(self);
        var template =  $('a.active','.fat-sb-email-template-container .ui.menu').attr('data-template'),
            customer_template_enable = $('#customer_template_enable').is(':checked') ? 1 : 0,
            customer_subject = $('#customer_subject').val(),
            customer_message = tinymce.editors['customer_template'].getContent(),
            employee_template_enable = $('#employee_template_enable').is(':checked') ? 1 : 0,
            employee_subject = $('#employee_subject').val(),
            employee_message = tinymce.editors['employee_template'].getContent(),
            customer_code_subject = $('#customer_code_subject').val(),
            customer_code_message = tinymce.editors['customer_code_template'].getContent();

        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_save_email_template',
                data: {
                    'template' : template,
                    'customer_enable': customer_template_enable,
                    'customer_subject' : customer_subject,
                    'customer_message' : he.encode(customer_message),
                    'employee_enable': employee_template_enable,
                    'employee_subject': employee_subject,
                    'employee_message': he.encode(employee_message),
                    'customer_code_subject': customer_code_subject,
                    'customer_code_message': he.encode(customer_code_message)
                }
            }),
            success: function (response) {
                FatSbMain.closeProcess(self);
                response = $.parseJSON(response);
                if (response.result > 0) {
                    FatSbMain.showMessage(self.attr('data-success-message'));

                    for(var $i=0; $i< fat_sb_email_data.length; $i++){
                        if(fat_sb_email_data[$i]['template'] == template){
                            if(template=='get_customer_code'){
                                fat_sb_email_data[$i]['customer_code_subject'] = customer_code_subject;
                                fat_sb_email_data[$i]['customer_code_message'] = customer_code_message;
                            }else{
                                fat_sb_email_data[$i]['customer_enable'] = customer_template_enable;
                                fat_sb_email_data[$i]['customer_subject'] = customer_subject;
                                fat_sb_email_data[$i]['customer_message'] = customer_message;

                                fat_sb_email_data[$i]['employee_enable'] = employee_template_enable;
                                fat_sb_email_data[$i]['employee_subject'] = employee_subject;
                                fat_sb_email_data[$i]['employee_message'] = employee_message;
                            }
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

    FatSbEmailTemplate.initTemplate = function(template){
        var customer_enable = 0,
            customer_subject = '',
            customer_message = '',
            employee_enable = 0,
            employee_subject = '',
            employee_message = '';

        /*switch to visual */
        $('#employee_template-tmce').trigger('click');
        $('#customer_template-tmce').trigger('click');

        for(var $i=0; $i< fat_sb_email_data.length; $i++){
            if(fat_sb_email_data[$i]['template'] == template){
                var data = fat_sb_email_data[$i];
                customer_enable = data['customer_enable'];
                customer_subject = data['customer_subject'];
                customer_message = data['customer_message'];

                employee_enable = data['employee_enable'];
                employee_subject = data['employee_subject'];
                employee_message = data['employee_message'];
            }
        }

        $('#customer_template_enable').prop("checked", customer_enable==1);
        $('#customer_subject').val(customer_subject);

        if(typeof tinymce.editors['customer_template']!='undefined' ){
            tinymce.editors['customer_template'].setContent(customer_message);
        }
        if ($('#customer_template_enable').is(':checked')) {
            $('.fields.customer-template').slideDown();
        } else {
            $('.fields.customer-template').hide();
        }

        $('#employee_template_enable').prop("checked", employee_enable==1);
        $('#employee_subject').val(employee_subject);
        if(typeof tinymce.editors['employee_template']!='undefined' ){
            tinymce.editors['employee_template'].setContent(employee_message);
        }
        if ($('#employee_template_enable').is(':checked')) {
            $('.fields.employee-template').slideDown();
        } else {
            $('.fields.employee-template').hide();
        }

    };

    FatSbEmailTemplate.initGetCodeTemplate = function(template){
        if(typeof tinymce.editors['customer_code_template']!='undefined'){
            tinymce.editors['customer_code_template'].theme.resizeTo(null, 200);
        }

        for(var $i=0; $i< fat_sb_email_data.length; $i++){
            if(fat_sb_email_data[$i]['template'] == template){
                var data = fat_sb_email_data[$i];
                $('#customer_code_subject').val(data['customer_code_subject']);
                if(typeof tinymce.editors['customer_code_template']!='undefined'){
                    tinymce.editors['customer_code_template'].setContent(data['customer_code_message']);
                }
            }
        }
    };

    FatSbEmailTemplate.sendTestMailTemplateOnClick = function(self){
        FatSbMain.showPopup('fat-sb-test-email-template', '', [], function () {
            FatSbMain.registerEventProcess($('.fat-test-email-template-modal'));
        })
    };

    FatSbEmailTemplate.sendTestMailTemplate = function(self){
        var send_to = $('#send_to').val(),
            pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
        if (send_to != '' && pattern.test(send_to)) {
            self.addClass('loading');
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_test_send_email_template',
                    template: $('a.item.active ','.fat-sb-email-template-container .fat-sb-template-tab').attr('data-template'),
                    send_to: send_to
                }),
                success: function (response) {
                    self.removeClass('loading');
                    response = $.parseJSON(response);
                    if (response.result_customer > 0 || response.result_employee > 0) {
                        FatSbMain.showMessage(self.attr('data-success-message'));
                    }
                    if(response.result_customer < 0 ){
                        FatSbMain.showMessage(response.message_customer,2);
                    }
                    if(response.result_employee < 0 ){
                        FatSbMain.showMessage(response.message_employee,2);
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

    $(document).ready(function () {
        FatSbEmailTemplate.init();
    });
    $(window).load(function(){
        FatSbMain.showLoading();
        setTimeout(function(){
            var template = $('a.active','.fat-sb-email-template-container .ui.menu').attr('data-template');
            FatSbEmailTemplate.initTemplate(template);
            FatSbMain.closeLoading();
        },3000);
    });
})(jQuery);