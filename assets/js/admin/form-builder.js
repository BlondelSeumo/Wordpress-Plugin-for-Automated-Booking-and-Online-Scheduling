"use strict";
(function ($) {
    var FatSbFormBuilder = new function () {
        this.init = function () {
            var formData = $('.fat-form-builder').attr('data-form');
            var options = {
                typeUserAttrs: {
                    text: {
                        dataErrorMessage: {
                            label: 'Error message',
                            value: 'Please input value for this field'
                        },
                    },
                    textarea: {
                        dataErrorMessage: {
                            label: 'Error message',
                            value: 'Please input value for this field'
                        },
                    },
                    date: {
                        dataLocale: {
                            label: 'Locale',
                            options: {
                                'en': 'English',
                                'ar': 'Arabic',
                                'ro': 'Romanian',
                                'id': 'Indonesian',
                                'is': 'Icelandic',
                                'bg': 'Bulgarian',
                                'fa': 'Persian, Farsi',
                                'ru': 'Russian',
                                'uk': 'Ukrainian',
                                'el': 'Ελληνικά',
                                'de': 'German',
                                'nl': 'Dutch',
                                'tr': 'Turkish',
                                'fr': 'French',
                                'es': 'Spanish',
                                'th': 'Thailand',
                                'pl': 'Polish',
                                'pt': 'Portuguese',
                                'ch': 'Chinese',
                                'se': 'Swedish',
                                'kr': 'Korean',
                                'it': 'Italian',
                                'da': 'Dansk',
                                'no': 'Norwegian',
                                'ja': 'Japanese',
                                'vi': 'Vietnamese',
                                'sl': 'Slovenščina',
                                'cs': 'Čeština',
                                'pt-BR': 'Português(Brasil)'
                            }
                        },
                        dataErrorMessage: {
                            label: 'Error message',
                            value: 'Please input value for this field'
                        },
                    },
                    paragraph: {
                    },
                    number: {
                        dataErrorMessage: {
                            label: 'Error message',
                            value: 'Please input value for this field'
                        },
                    },
                    select: {
                        dataErrorMessage: {
                            label: 'Error message',
                            value: 'Please input value for this field'
                        },
                    },
                    'radio-group': {
                        dataErrorMessage: {
                            label: 'Error message',
                            value: 'Please select value for this field'
                        },
                    },
                    'checkbox-group': {
                        dataErrorMessage: {
                            label: 'Error message',
                            value: 'Please select value for this field'
                        },
                    }
                },
                controlOrder: [
                    'text',
                    'textarea',
                    'select',
                    'radio-group',
                    'checkbox-group',
                    'number',
                    'date'
                ],
                dataType: 'json'
            };

            if (formData != '[]') {
                options['formData'] = formData;
            }
            var formBuilder = $('.fat-form-builder').formBuilder(options);

            formBuilder.promise.then(function (fb) {
                $('.fat-form-builder .btn-group .btn.save-template').on('click', function () {
                    var self = $(this);
                    FatSbMain.showProcess(self);
                    $.ajax({
                        url: fat_sb_data.ajax_url,
                        type: 'POST',
                        data: ({
                            action: 'fat_sb_save_form_builder',
                            form: formBuilder.formData,
                        }),
                        success: function (response) {
                            FatSbMain.closeProcess(self);
                            response = $.parseJSON(response);

                            if(typeof response.message!='undefined'){
                                FatSbMain.showMessage(response.message);
                            }else{
                                FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                            }
                        },
                        error: function () {
                            FatSbMain.closeProcess(self);
                            FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        }
                    });
                });
            });
        };
    };

    $(document).ready(function () {
        FatSbFormBuilder.init();
        FatSbMain.initPopupToolTip();
    });
})(jQuery);