"use strict";
var FatSbPricePackageOrderFE = {};
(function ($) {

    FatSbPricePackageOrderFE.init = function () {
        FatSbMain_FE.registerOnClick($('.fat-sb-price-package'));
    };

    FatSbPricePackageOrderFE.initStripeCardInput = function () {
        if ($('form#stripe-payment-form').length == 0) {
            return;
        }

        var pk = FatSbMain_FE.data.stripe_key;
        if (typeof pk == 'undefined' || pk == '') {
            return;
        }

        var stripe = Stripe(pk),
            elements = stripe.elements();

        var style = {
            base: {
                color: '#32325d',
                lineHeight: '18px',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: 'red',
                iconColor: 'red'
            }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('stripe-payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var self = $('.fat-sb-payment-submit', '.price-package-item.active'),
                container = self.closest('.fat-sb-price-package');

            FatSbPricePackageOrderFE.addLoading(container, self);
            stripe.createToken(card).then(function (result) {

                var self = $('.fat-sb-payment-submit', '.price-package-item.active'),
                    pk_id = self.attr('data-pk-id'),
                    container = self.closest('.fat-sb-price-package');

                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    FatSbMain_FE.removeLoading(container, self);
                } else {
                    // Send the token to your server.
                    $.ajax({
                        url: FatSbMain_FE.data.ajax_url,
                        type: 'POST',
                        data: ({
                            action: 'fat_sb_package_booking',
                            s_field: FatSbMain_FE.data.ajax_s_field,
                            token: result.token.id,
                            pk_id: pk_id,
                            payment_method: 'stripe'
                        }),
                        success: function (data) {
                            data = $.parseJSON(data);

                            if (data.code > 0) {
                                FatSbPricePackageOrderFE.removeLoading(container, self);
                                var successElement = document.getElementById('card-errors');
                                errorElement.textContent = data.message;
                            } else {
                                FatSbPricePackageOrderFE.removeLoading(container, self);
                                var errorElement = document.getElementById('payment-success');
                                errorElement.textContent = data.message;
                            }
                        },
                        error: function () {
                            FatSbPricePackageOrderFE.removeLoading(container, self);
                            var errorElement = document.getElementById('card-errors');
                            errorElement.textContent = data.message;
                        }
                    });
                }
            });
        });
    };

    FatSbPricePackageOrderFE.selectPackage = function (elm) {
        var container = elm.closest('.fat-sb-price-package'),
            package_item = elm.closest('.price-package-item'),
            paypal_enable = container.attr('data-paypal'),
            stripe_enable = container.attr('data-stripe'),
            mypos_enable = container.attr('data-mypos'),
            przelewy24_enable = container.attr('data-przelewy24'),
            total_gateway = 0;

        $('.pk-payment-method .fat-sb-no-gateway-alert', package_item).remove();
        if (stripe_enable != '1' && paypal_enable != '1' && mypos_enable != '1' && przelewy24_enable!='1') {
            $('.pk-payment-method', package_item).css('opacity', 1);
            $('.pk-payment-method', package_item).append('<div class="fat-sb-no-gateway-alert" style="color:red">' + fat_sb_data.no_gateway_alert + '</div>');
        }

        var stripe_form = '';
        if (stripe_enable == '1') {
            total_gateway += 1;
            stripe_form = '<form method="post" id="stripe-payment-form"><div class="form-row">   <div id="card-element"></div> <div id="card-errors" role="alert"></div> </div> <button></button></form>';
        }
        var ul_pay_list = '<ul class="fat-sb-pay-list">';
        if (paypal_enable == '1') {
            total_gateway += 1;
            ul_pay_list += '<li><input type="radio" name="payment_method" id="paypal_method" value="paypal"><label for="paypal_method">Paypal</label></li>';
        }
        if (stripe_enable == '1') {
            total_gateway += 1;
            ul_pay_list += '<li><input type="radio" name="payment_method" id="stripe_method" value="stripe"><label for="stripe_method">Stripe</label></li>';
        }
        if (mypos_enable == '1') {
            total_gateway += 1;
            ul_pay_list += '<li><input type="radio" name="payment_method" id="myPos_method" value="myPos"><label for="myPos_method">MyPos</label></li>';
        }
        if (przelewy24_enable == '1') {
            total_gateway += 1;
            ul_pay_list += '<li><input type="radio" name="payment_method" id="przelewy24_method" value="przelewy24"><label for="przelewy24_method">Przelewy24</label></li>';
        }

        $('.pk-payment-method', '.price-package-item.active').addClass('need-remove');

        $('.pk-payment-method.need-remove').closest('.price-package-item').removeClass('active');
        $('.pk-payment-method.need-remove', '.price-package-item').empty();
        $('.pk-payment-method.need-remove', '.price-package-item').removeClass('need-remove');


        if (stripe_enable == '1' || paypal_enable == '1' || mypos_enable == '1' || przelewy24_enable == '1') {
            if(total_gateway>1){
                package_item.addClass('active');
            }else{
                $('.pk-payment-method', package_item).hide();
            }
            $('.pk-payment-method', package_item).append('<div class="fat-sb-method-title">' + fat_sb_data.method_title + ' </div>');
            $('.pk-payment-method', package_item).append(ul_pay_list);
        }

        $('.fat-sb-pay-list input[name="payment_method"]', package_item).on('change', function () {
            var payment_method = $('input[name="payment_method"]:checked', package_item).val();
            $('#payment-success').remove();
            $('#card-errors').remove();
            if (payment_method == 'stripe') {
                $('.pk-payment-method', package_item).append('<form method="post" id="stripe-payment-form">  <div class="form-row"><div id="card-element"></div> <div id="card-errors" role="alert"></div> <div id="payment-success" role="alert"></div> </div> <button style="display: none"></button></form>');
                FatSbPricePackageOrderFE.initStripeCardInput();
            } else {
                $('.pk-payment-method form#stripe-payment-form', package_item).remove();
            }
        });

        if(total_gateway==1){
            FatSbPricePackageOrderFE.submitPayment(elm);
            return;
        }
        //set payment method default
        $('.fat-sb-pay-list li:first-child input[name="payment_method"]', package_item).prop("checked", true);
        $('.fat-sb-pay-list li:first-child input[name="payment_method"]', package_item).trigger('change');

    };

    FatSbPricePackageOrderFE.submitPayment = function (elm) {
        var pk_id = $(elm).attr('data-pk-id'),
            container = $(elm).closest('.fat-sb-price-package'),
            item_container = $(elm).closest('.price-package-item'),
            payment_method = $('input[name="payment_method"]:checked', item_container).val();

        if($('.pk-payment-method .fat-sb-pay-list li', item_container).length==1){
            payment_method = $('.fat-sb-pay-list li:first-child input[name="payment_method"]', item_container).val();
        }

        if (typeof payment_method == 'undefined' || payment_method == '') {
            $('.fat-sb-error-message', container).html('Please select payment method').removeClass('fat-sb-hidden');
            return;
        }

        if (payment_method == 'stripe') {
            $('form#stripe-payment-form button', container).trigger('click');
        } else {
            FatSbPricePackageOrderFE.addLoading(container, elm);

            try {
                $.ajax({
                    url: FatSbMain_FE.data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_package_booking',
                        s_field: FatSbMain_FE.data.ajax_s_field,
                        pk_id: pk_id,
                        payment_method: payment_method
                    }),
                    success: function (response) {
                        response = $.parseJSON(response);
                        if (response.result > 0) {

                            if(payment_method == 'przelewy24' && typeof response.p24_url!='undefined' && response.p24_url!=''){
                               window.location.href = response.p24_url;
                            }

                            if (payment_method == 'myPOS') {
                                var form = $(response.form);
                                form.hide();
                                $('body').append(form);
                                $('form#ipcForm').submit();
                                return;
                            }

                            if (payment_method == 'paypal' && typeof response.pp_url != 'undefined' && response.pp_url != '') {
                                window.location.href = response.pp_url;
                            }

                        } else {
                            FatSbPricePackageOrderFE.removeLoading(container, self);
                            $('.fat-sb-error-message', container).html(response.message).removeClass('fat-sb-hidden');
                        }
                    },
                    error: function (response) {
                        FatSbPricePackageOrderFE.removeLoading(container, self);
                    }
                });
            } catch (err) {
            }
        }

    };

    FatSbPricePackageOrderFE.addLoading = function (container, elm) {
        if ($('.fat-loading-container', container).length == 0) {
            container.append('<div class="fat-loading-container"></div>');
        }
        elm.addClass('loading');
    };

    FatSbPricePackageOrderFE.removeLoading = function (container, elm) {
        $('.fat-loading-container', container).remove();
        $(elm).removeClass('loading');
    };

    $(document).ready(function () {
        FatSbPricePackageOrderFE.init();
    })
})(jQuery);