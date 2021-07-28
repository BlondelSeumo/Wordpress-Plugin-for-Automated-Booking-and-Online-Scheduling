"use strict";
var FatSbBookingLogin = {
    isFormValid: true
};
(function ($) {
    FatSbBookingLogin.init = function () {
        var login_form = $('.fat-login-form');
        FatSbMain_FE.registerOnClick(login_form);
        FatSbMain_FE.registerOnChange(login_form);
        $('.fat-sb-login-container').addClass('loaded');

        var forgot_form = $('.fat-forgot-form');
        FatSbMain_FE.registerOnClick(forgot_form);
        FatSbMain_FE.registerOnChange(forgot_form);

        FatSbMain_FE.registerOnClick($('.fat-reset-section-wrap'));

        $('#u_pass').on('keyup', function (e) {
            if (e.keyCode === 13) {
                $('button.fat-bt-login').trigger('click');
            }
        });
    };

    FatSbBookingLogin.tabClick = function (self) {
        if (!self.hasClass('active')) {
            var tab = self.attr('data-tab'),
                container = self.closest('.fat-login-form'),
                tab_menu = self.closest('.fat-tab-menu'),
                current_tab = $('a.item.active', tab_menu).attr('data-tab');

            $('.fat-tabs.' + current_tab).hide();
            $('a.item.active', tab_menu).removeClass('active');
            $('.fat-tabs.active', tab_menu).removeClass('active');

            $('.fat-tabs.' + tab).show();
            $(this).addClass('active');
            self.addClass('active');
        }
    };

    FatSbBookingLogin.processLogin = function(self){
        var tab = self.closest('.fat-tabs'),
            prev_url = $('.fat-sb-login-container').attr('data-prev');
        FatSbMain_FE.isFormValid = FatSbMain_FE.validateForm($('.login .fat-form'));
        if(FatSbMain_FE.isFormValid){
            FatSbMain_FE.addLoading(tab, self);
            $('.fat-login-message', tab).html('').addClass('fat-sb-hidden');
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_login',
                    u_email: $('#u_email', tab).val(),
                    u_pass: $('#u_pass', tab).val(),
                    remember_me: $('#remember_me', tab).is(':checked') ? 1 : 0,
                    s_field: FatSbMain_FE.data.ajax_s_field
                }),
                success: function(response){
                    response = $.parseJSON(response);

                    if(response.result >= 0 && typeof response.url !='undefined' && response.url !=''){
                        prev_url = prev_url!='' ? prev_url :response.url;
                            window.location.href = prev_url;
                    }else{
                        FatSbMain_FE.removeLoading(tab, self);
                        if(typeof response.message!='undefined'){
                            $('.fat-login-message').html( response.message).removeClass('fat-sb-hidden');
                        }else{
                            $('.fat-login-message').html(FatSbMain_FE.data.error_message).removeClass('fat-sb-hidden');
                        }
                    }
                },
                error: function(){
                    FatSbMain_FE.removeLoading(tab, self);
                    $('.fat-login-message').html(FatSbMain_FE.data.error_message).removeClass('fat-sb-hidden');
                }
            })
        }
    };

    FatSbBookingLogin.processSignUp = function(self){
        var tab = self.closest('.fat-tabs'),
            pass = $('#u_pass', tab).val(),
            re_pass = $('#u_re_pass', tab).val();

        FatSbMain_FE.isFormValid = FatSbMain_FE.validateForm($('.sign-up .fat-form'));
        if(FatSbMain_FE.isFormValid && pass != re_pass){
            FatSbMain_FE.isFormValid = false;
            $('.sign-up .fat-form .fat-re-pass').addClass('field-error');
            $('.sign-up .fat-form .fat-re-pass .field-error-message').html(FatSbMain_FE.data.pass_confirm_message);
        }
        if(pass.length < 6){
            $('.sign-up .fat-form .fat-pass .field-error-message').html(FatSbMain_FE.data.pass_length_message);
        }
        if(FatSbMain_FE.isFormValid){
            FatSbMain_FE.addLoading(tab, self);
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_sign_up',
                    u_email: $('#u_email', tab).val(),
                    u_name: $('#u_name', tab).val(),
                    u_surname: $('#u_surname', tab).val(),
                    u_pass: pass,
                    s_field: FatSbMain_FE.data.ajax_s_field
                }),
                success: function(response){
                    response = $.parseJSON(response);
                    FatSbMain_FE.removeLoading(tab, self);
                    if(response.result > 0 ){
                        $('.fat-form,.fat-title','.sign-up').fadeOut(function(){
                            $('.fat-sign-up-notifier','.sign-up').fadeIn();
                        });
                    }else{
                        if(typeof response.message!='undefined'){
                            $('.fat-sign-up-message').html( response.message).removeClass('fat-sb-hidden');
                        }else{
                            $('.fat-sign-up-message').html(FatSbMain_FE.data.error_message).removeClass('fat-sb-hidden');
                        }
                    }
                },
                error: function(){
                    FatSbMain_FE.removeLoading(tab, self);
                    $('.fat-login-message').html(FatSbMain_FE.data.error_message).removeClass('fat-sb-hidden');
                }
            })
        }
    };

    FatSbBookingLogin.processResetPass = function(self){
        var container = self.closest('.fat-forgot-section-wrap');
        FatSbMain_FE.isFormValid = FatSbMain_FE.validateForm($('.fat-forgot-form .fat-form'));
        if(FatSbMain_FE.isFormValid){
            $('.fat-forgot-message', container).html('').addClass('fat-sb-hidden');
            FatSbMain_FE.addLoading(container, self);
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_forgot_pass',
                    u_email: $('#fg_email', container).val(),
                    s_field: FatSbMain_FE.data.ajax_s_field
                }),
                success: function(response){
                    response = $.parseJSON(response);
                    FatSbMain_FE.removeLoading(container, self);
                    if(response.result >= 0){
                        $('.fat-forgot-message').html( response.message).addClass('rm-main-color').removeClass('fat-sb-hidden');
                    }else{
                        if(typeof response.message!='undefined'){
                            $('.fat-forgot-message').html( response.message).removeClass('fat-sb-hidden');
                        }else{
                            $('.fat-forgot-message').html(FatSbMain_FE.data.error_message).removeClass('fat-sb-hidden');
                        }
                    }
                },
                error: function(){
                    FatSbMain_FE.removeLoading(container, self);
                    $('.fat-login-message').html(FatSbMain_FE.data.error_message).removeClass('fat-sb-hidden');
                }
            })
        }
    };

    FatSbBookingLogin.submitResetNewPass = function(self){
        var container = self.closest('.fat-reset-section-wrap'),
            pass = $('#new_pass', container).val();

        if(pass!=''){
            $('.fat-forgot-message', container).html('').addClass('fat-sb-hidden');
            FatSbMain_FE.addLoading(container, self);
            $.ajax({
                url: FatSbMain_FE.data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_reset_pass',
                    pass: pass,
                    key: $('#key', container).val(),
                    login: $('#login', container).val(),
                    s_field: FatSbMain_FE.data.ajax_s_field
                }),
                success: function(response){
                    response = $.parseJSON(response);
                    if(response.result >= 0 && typeof response.url!='undefined'){
                        window.location.href = response.url;
                    }else{
                        FatSbMain_FE.removeLoading(container, self);
                        if(typeof response.message!='undefined'){
                            $('.fat-forgot-message').html( response.message).addClass('rm-error').removeClass('fat-sb-hidden');
                        }else{
                            $('.fat-forgot-message').html(FatSbMain_FE.data.error_message).addClass('rm-error').removeClass('fat-sb-hidden');
                        }
                    }
                },
                error: function(){
                    FatSbMain_FE.removeLoading(container, self);
                    $('.fat-login-message').html(FatSbMain_FE.data.error_message).removeClass('fat-sb-hidden');
                }
            })
        }else{
            $('.fat-field ', container).addClass('field-error');
        }
    };

    FatSbBookingLogin.showForgotPass = function(self){
        $('.fat-login-section-wrap').hide();
        $('.fat-forgot-section-wrap').show();
    };

    $(document).ready(function () {
        FatSbBookingLogin.init();
    });
})(jQuery);