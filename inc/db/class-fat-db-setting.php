<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/7/2019
 * Time: 9:10 AM
 */
if (!class_exists('FAT_DB_Setting')) {
    class FAT_DB_Setting
    {
        private static $instance = NULL;
        private $option_key = 'fat_sb_settings';
        private $working_hour_key = 'fat_sb_working_hour_setting';
        private $custom_css_key = 'fat_sb_custom_css_setting';
        private $email_template_key = 'fat_sb_email_template_setting';
        private $sms_template_key = 'fat_sb_sms_template_setting';
        private $user_role_setting_key = 'fat_sb_user_role_setting';

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_setting()
        {
            $setting_default = array(
                'duration_step' => 15,
                'time_step' => 15,
                'time_format' => '24h',
                'day_limit' => 365,
                'time_to_change_status' => 24,
                'limit_booking_per_day' => 0,
                'cancel_before' => 0,
                'calendar_view' => 'month',
                'b_process_status' => 0,
                'allow_client_cancel' => 0,
                'item_per_page' => 10,
                'default_phone_code' => '+44,uk',
                'service_tax' => 0,
                'service_available' => 1,
                'enable_modal_popup' => 0,
                'enable_time_slot_deactive' => 0,
                'disable_customer_email' => 0,
                'bg_time_slot_not_active' => '#dddddd',
                'enable_datetime_picker' => 1,
                'service_label' => '',
                'employee_label' => '',
                'price_label' => '',
                'number_of_person_label' => '',
                'person_label' => '',
                'total_cost_label' => '',
                'payment_method_label' => '',
                'company_name' => '',
                'company_address' => '',
                'company_phone' => '',
                'company_email' => '',
                'mailer' => 'default',
                'smtp_host' => '',
                'smtp_port' => '',
                'smpt_encryption' => 'none',
                'smtp_username' => '',
                'smtp_password' => '',
                'send_from_name' => '',
                'send_from_name_label' => '',
                'cc_to' => '',
                'bcc_to' => '',
                'success_page' => '',
                'error_page' => '',
                'currency' => 'USD',
                'number_of_decimals' => 2,
                'symbol_position' => 'after',
                'default_payment_method' => 'onsite',
                'hide_payment' => 0,
                'onsite_enable' => 1,
                'price_package_enable' => 0,
                'paypal_enable' => 0,
                'paypal_sandbox' => 'test',
                'paypal_client_id' => '',
                'paypal_secret' => '',
                'stripe_enable' => 0,
                'stripe_sandbox' => 'test',
                'stripe_publish_key' => '',
                'stripe_secret_key' => '',
                'myPOS_enable' => 0,
                'myPOS_sandbox' => 'test',
                'myPOS_storeID' => '',
                'myPOS_client_number' => '',
                'myPOS_key_index' => '',
                'myPOS_private_key' => '',
                'myPOS_public_certificate' => '',
                'myPOS_success_page' => '',
                'myPOS_error_page' => '',
                'przelewy24_enable' => '',
                'p24_mode' => 'sandbox',
                'p24_merchant_id' => '',
                'p24_pos_id' => '',
                'p24_crc' => '',
                'przelewy24_success_page' => '',
                'przelewy24_error_page' => '',
                'google_map_api' => '',
                'allow_user_booking' => '',
                'sms_provider' => '',
                'sms_owner_phone_number' => '',
                'sms_sid' => '',
                'sms_token' => '',
                'booked_message' => esc_html__('Thank you! Your booking is complete. An email with detail of your booking has been send to you.','fat-services-booking')
            );
            $setting = get_option($this->option_key, $setting_default);
            $setting = array_merge($setting_default, $setting);
            return $setting;
        }

        public function get_currency_setting()
        {
            $setting = $this->get_setting();
            $currency = FAT_SB_Utils::getCurrency();
            $symbol = '$';
            foreach ($currency as $c) {
                if ($c['code'] == $setting['currency']) {
                    $symbol = $c['symbol'];
                    break;
                }
            }
            return array(
                'currency' => $setting['currency'],
                'symbol' => $symbol,
                'symbol_position' => $setting['symbol_position'],
            );
        }

        public function get_working_hour_setting()
        {
            $default = array(
                'schedules' => array(
                    array(
                        'es_day' => '2',
                        'es_enable' => '1',
                        'work_hours' => array(
                            array(
                                'es_work_hour_start' => 480,
                                'es_work_hour_end' => 1020
                            )
                        )
                    ),
                    array(
                        'es_day' => '3',
                        'es_enable' => '1',
                        'work_hours' => array(
                            array(
                                'es_work_hour_start' => 480,
                                'es_work_hour_end' => 1020
                            )
                        )
                    ),
                    array(
                        'es_day' => '4',
                        'es_enable' => '1',
                        'work_hours' => array(
                            array(
                                'es_work_hour_start' => 480,
                                'es_work_hour_end' => 1020
                            )
                        )
                    ),
                    array(
                        'es_day' => '5',
                        'es_enable' => '1',
                        'work_hours' => array(
                            array(
                                'es_work_hour_start' => 480,
                                'es_work_hour_end' => 1020
                            )
                        )
                    ),
                    array(
                        'es_day' => '6',
                        'es_enable' => '1',
                        'work_hours' => array(
                            array(
                                'es_work_hour_start' => 480,
                                'es_work_hour_end' => 1020
                            )
                        )
                    ),
                    array(
                        'es_day' => '7',
                        'es_enable' => '0',
                    ),
                    array(
                        'es_day' => '8',
                        'es_enable' => '0',
                    ),
                )
            );
            $working_hour = get_option($this->working_hour_key, $default);
            return $working_hour;
        }

        public function get_custom_css()
        {
            $custom_css = get_option($this->custom_css_key, '');
            $custom_css = stripslashes($custom_css);
            return $custom_css;
        }

        public function save_setting()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                $setting = $this->get_setting();
                foreach ($setting as $key => $value) {
                    if (isset($data[$key])) {
                        $setting[$key] = $data[$key];
                    }
                }
                update_option($this->option_key, $setting);
                return array(
                    'result' => 1,
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data for field', 'fat-services-booking')
                );
            }
        }

        public function save_user_role_setting()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                if(isset($data['warning_limit_user_message'])){
                    $data['warning_limit_user_message'] = stripslashes($data['warning_limit_user_message']);
                }
                if(isset($data['warning_message'])){
                    $data['warning_message'] = stripslashes($data['warning_message']);
                }
                update_option($this->user_role_setting_key, $data);
                return array(
                    'result' => 1,
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data for field', 'fat-services-booking')
                );
            }
        }

        public function get_user_role_setting()
        {
            return get_option($this->user_role_setting_key, true);
        }

        public function save_working_hour_setting()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data) {
                update_option($this->working_hour_key, $data);
                return array(
                    'result' => 1,
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data', 'fat-services-booking')
                );
            }
        }

        public function save_custom_css()
        {
            $custom_css = isset($_REQUEST['data']['custom_css']) ? $_REQUEST['data']['custom_css'] : '';
            $result = update_option($this->custom_css_key, $custom_css);
            return array(
                'result' => 1,
            );
        }

        public function get_email_template()
        {
            $template_default = array(
                array(
                    'template' => 'pending',
                    'customer_enable' => 1,
                    'customer_subject' => '',
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_subject' => '',
                    'employee_message' => ''
                ),
                array(
                    'template' => 'approved',
                    'customer_enable' => 1,
                    'customer_subject' => '',
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_subject' => '',
                    'employee_message' => ''
                ),
                array(
                    'template' => 'rejected',
                    'customer_enable' => 1,
                    'customer_subject' => '',
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_subject' => '',
                    'employee_message' => ''
                ),
                array(
                    'template' => 'canceled',
                    'customer_enable' => 1,
                    'customer_subject' => '',
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_subject' => '',
                    'employee_message' => ''
                ),
                array(
                    'template' => 'get_customer_code',
                    'customer_code_subject' => '',
                    'customer_code_message' => '',
                ),
                array(
                    'template' => 'backend',
                    'customer_enable' => 1,
                    'customer_subject' => '',
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_subject' => '',
                    'employee_message' => ''
                ),
            );
            $template = get_option($this->email_template_key, $template_default);
            $template = is_array($template) ? $template : $template_default;
            for ($i = 0; $i < count($template); $i++) {
                if(isset( $template[$i]['customer_message'])){
                    $template[$i]['customer_message'] = html_entity_decode($template[$i]['customer_message']);
                }
                if(isset( $template[$i]['employee_message'])){
                    $template[$i]['employee_message'] = html_entity_decode($template[$i]['employee_message']);
                }
                if(isset( $template[$i]['customer_code_message'])){
                    $template[$i]['customer_code_message'] = html_entity_decode($template[$i]['customer_code_message']);
                }
            }
            return $template;
        }

        public function get_sms_template()
        {
            $template_default = array(
                array(
                    'template' => 'pending',
                    'customer_enable' => 1,
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_message' => ''
                ),
                array(
                    'template' => 'approved',
                    'customer_enable' => 1,
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_message' => ''
                ),
                array(
                    'template' => 'rejected',
                    'customer_enable' => 1,
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_message' => ''
                ),
                array(
                    'template' => 'canceled',
                    'customer_enable' => 1,
                    'customer_message' => '',
                    'employee_enable' => 1,
                    'employee_message' => ''
                ),
            );
            $template = get_option($this->sms_template_key, $template_default);
            for ($i = 0; $i < count($template_default); $i++) {
                if (!isset($template[$i])) {
                    $template[] = $template_default[$i];
                }
            }
            return $template;
        }

        public function save_email_template()
        {
            $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';
            $template = isset($data['template']) ? $data['template'] : '';

            if(!$template){
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }

            $email_template = $this->get_email_template();
            for ($i = 0; $i < count($email_template); $i++) {
                if ($email_template[$i]['template'] == $template) {
                    if ($template == 'get_customer_code') {
                        $email_template[$i]['customer_code_subject'] = isset($data['customer_code_subject']) ? $data['customer_code_subject'] : '';
                        $email_template[$i]['customer_code_message'] = isset($data['customer_code_message']) ? $data['customer_code_message'] : '';
                    } else {
                        $email_template[$i]['customer_enable'] = isset($data['customer_enable']) ? $data['customer_enable'] : 0;
                        $email_template[$i]['customer_subject'] = isset($data['customer_subject']) ? $data['customer_subject'] : '';
                        $email_template[$i]['customer_message'] = isset($data['customer_message']) ? $data['customer_message'] : '';

                        $email_template[$i]['employee_enable'] = isset($data['employee_enable']) ? $data['employee_enable'] : 0;
                        $email_template[$i]['employee_subject'] = isset($data['employee_subject']) ? $data['employee_subject'] : '';
                        $email_template[$i]['employee_message'] = isset($data['employee_message']) ? $data['employee_message'] : '';
                    }
                }
            }
            update_option($this->email_template_key, $email_template);
            return array(
                'result' => 1,
            );
        }

        public function save_sms_template()
        {
            $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';
            $template = isset($data['template']) ? $data['template'] : '';

            if ($template) {
                $sms_template = $this->get_sms_template();
                for ($i = 0; $i < count($sms_template); $i++) {
                    if ($sms_template[$i]['template'] == $template) {
                        $sms_template[$i]['customer_enable'] = isset($data['customer_enable']) ? $data['customer_enable'] : 0;
                        $sms_template[$i]['customer_message'] = isset($data['customer_message']) ? $data['customer_message'] : '';

                        $sms_template[$i]['employee_enable'] = isset($data['employee_enable']) ? $data['employee_enable'] : 0;
                        $sms_template[$i]['employee_message'] = isset($data['employee_message']) ? $data['employee_message'] : '';
                    }
                }
                update_option($this->sms_template_key, $sms_template);
                return array(
                    'result' => 1,
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }
        }

        public function test_send_email_template()
        {
            $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';
            $template = isset($_REQUEST['template']) ? $_REQUEST['template'] : '';
            $send_to = isset($_REQUEST['send_to']) ? $_REQUEST['send_to'] : '';

            if ($template && $send_to) {
                $email_template = $this->get_email_template();
                $setting = $this->get_setting();
                $now = current_time('mysql', 0);
                $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
                $now->modify('+1 days');
                $subject = $message = '';

                for ($i = 0; $i < count($email_template); $i++) {
                    if ($email_template[$i]['template'] == $template) {
                        $mail_info = array(
                            'c_code' => 'ahxyr132ays',
                            'c_first_name' => 'Jonh',
                            'c_last_name' => 'Smith',
                            'c_email' => $send_to,
                            'c_phone' => '+432 76548 876',
                            'e_first_name' => 'Lanna',
                            'e_last_name' => 'Smash',
                            'e_email' => $send_to,
                            'e_phone' => '+538 96449 576',
                            's_name' => 'Possim recusabo',
                            'b_service_duration' => 30,
                            'loc_name' => 'The Possim Club',
                            'loc_address' => '34 St James\'s Square',
                            'b_date' => $now->format('Y-m-d'),
                            'b_time' => 540,
                            'b_total_pay' => 25
                        );
                        $mail_info = (object)$mail_info;
                        $result = '';
                        $result_test = array(
                            'result_customer' => '',
                            'message_customer' => '',
                            'result_employee' => '',
                            'message_employee' => '',
                        );
                        if (isset($email_template[$i]['customer_enable']) && $email_template[$i]['customer_enable']) {
                            $subject = $email_template[$i]['customer_subject'];
                            $message = $email_template[$i]['customer_message'];
                            FAT_SB_Utils::makeMailContent($subject, $message, $mail_info, $setting);
                            $result = FAT_SB_Utils::sendMail(array(
                                'mailer' => $setting['mailer'],
                                'smtp_host' => $setting['smtp_host'],
                                'smtp_port' => $setting['smtp_port'],
                                'smtp_username' => $setting['smtp_username'],
                                'smtp_password' => $setting['smtp_password'],
                                'encryption' => $setting['smpt_encryption'],
                                'from_name' => $setting['send_from_name'],
                                'from_name_label' => isset($setting['send_from_name_label']) ? $setting['send_from_name_label'] : $setting['send_from_name'],
                                'send_to' => $mail_info->c_email,
                                'cc_email' => $setting['cc_to'],
                                'bcc_email' => $setting['bcc_to'],
                                'subject' => $subject,
                                'message' => $message
                            ));
                            $result_test['result_customer'] = isset($result['result']) ? $result['result'] : 0;
                            $result_test['message_customer'] = isset($result['message']) ? $result['message'] : '';
                        }

                        if (isset($email_template[$i]['employee_enable']) && $email_template[$i]['employee_enable']) {
                            $subject = $email_template[$i]['employee_subject'];
                            $message = $email_template[$i]['employee_message'];
                            FAT_SB_Utils::makeMailContent($subject, $message, $mail_info, $setting);
                            $result = FAT_SB_Utils::sendMail(array(
                                'mailer' => $setting['mailer'],
                                'smtp_host' => $setting['smtp_host'],
                                'smtp_port' => $setting['smtp_port'],
                                'smtp_username' => $setting['smtp_username'],
                                'smtp_password' => $setting['smtp_password'],
                                'encryption' => $setting['smpt_encryption'],
                                'from_name' => $setting['send_from_name'],
                                'from_name_label' => isset($setting['send_from_name_label']) ? $setting['send_from_name_label'] : $setting['send_from_name'],
                                'send_to' => $mail_info->c_email,
                                'cc_email' => $setting['cc_to'],
                                'bcc_email' => $setting['bcc_to'],
                                'subject' => $subject,
                                'message' => $message
                            ));
                            $result_test['result_employee'] = isset($result['result']) ? $result['result'] : 0;
                            $result_test['message_employee'] = isset($result['message']) ? $result['message'] : '';
                        }
                        return $result_test;
                    }
                }
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }
        }

        public function test_send_mail()
        {
            $setting = $this->get_setting();
            $send_to = isset($_REQUEST['send_to']) && $_REQUEST['send_to'] ? $_REQUEST['send_to'] : '';
            $subject = esc_html__('Test mail from FAT Service Booking plugin', 'fat-services-booking');
            $message = esc_html__('This is email from FAT Service Booking plugin. This send with purpose for test mail config', 'fat-services-booking');

            if (!$setting['mailer']) {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data for notification setting and save before test', 'fat-services-booking')
                );
            }
            return FAT_SB_Utils::sendMail(array(
                'mailer' => $setting['mailer'],
                'smtp_host' => $setting['smtp_host'],
                'smtp_port' => $setting['smtp_port'],
                'smtp_username' => $setting['smtp_username'],
                'smtp_password' => $setting['smtp_password'],
                'encryption' => $setting['smpt_encryption'],
                'from_name' => $setting['send_from_name'],
                'from_name_label' => isset($setting['send_from_name_label']) ? $setting['send_from_name_label'] : $setting['send_from_name'],
                'send_to' => $send_to,
                'subject' => $subject,
                'message' => $message
            ));
        }

        public function test_send_sms(){

            $phone_number = isset($_REQUEST['phone_number']) && $_REQUEST['phone_number'] ? $_REQUEST['phone_number'] : '';
            $message = esc_html__('This is SMS from FAT Service Booking plugin. This send with purpose for test sms config', 'fat-services-booking');
            return FAT_SB_Utils::sendSMS($phone_number, $message);

        }
    }
}