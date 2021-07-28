<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 2/6/2020
 * Time: 2:20 PM
 */

if (!class_exists('FAT_DB_Price_Package')) {
    class FAT_DB_Price_Package
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_package()
        {
            global $wpdb;
            $sql = "SELECT pk_id, pk_image_id, pk_price, pk_price_for_payment, pk_name, pk_description, pk_create_date
                    FROM {$wpdb->prefix}fat_sb_price_package
                    WHERE pk_status=%d";
            $sql = $wpdb->prepare($sql, 1);
            $package = $wpdb->get_results($sql);
            return $package;
        }

        public function get_package_by_id()
        {
            global $wpdb;
            $pk_id = isset($_REQUEST['pk_id']) ? $_REQUEST['pk_id'] : 0;
            $sql = "SELECT pk_id, pk_image_id, pk_price, pk_price_for_payment, pk_name, pk_description
                    FROM {$wpdb->prefix}fat_sb_price_package
                    WHERE pk_status=1 AND pk_id=%d";
            $sql = $wpdb->prepare($sql, $pk_id);
            $package = $wpdb->get_results($sql);
            if (count($package) > 0) {
                $package = $package[0];
                $package->pk_image_url = isset($package->pk_image_id) ? wp_get_attachment_image_src($package->pk_image_id, 'thumbnail') : '';
                $package->pk_image_url = isset($package->pk_image_url[0]) ? $package->pk_image_url[0] : '';
            } else {
                $package = array(
                    'pk_id' => 0,
                    'pk_name' => '',
                    'pk_price' => '',
                    'pk_description' => '',
                );
            }
            return $package;
        }

        public function save_package()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                $pk_id = isset($data['pk_id']) && $data['pk_id'] != '' ? $data['pk_id'] : 0;
                global $wpdb;
                if ($pk_id > 0) {
                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_price_package', $data, array('pk_id' => $data['pk_id']));
                } else {
                    $data['pk_create_date'] = current_time('mysql', 0);
                    $data['pk_status'] = 1;
                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_price_package', $data);
                    $result = $result > 0 ? $wpdb->insert_id : $result;
                }
                $create_date = isset($data['pk_create_date']) ? DateTime::createFromFormat('Y-m-d H:i:s', $data['pk_create_date']) : '';
                $date_format = get_option('date_format');
                return array(
                    'result' => $result,
                    'create_date' => $create_date ? date_i18n($date_format, $create_date->format('U')) : ''
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data for field', 'fat-services-booking')
                );
            }
        }

        public function delete_package()
        {
            global $wpdb;
            $pk_ids = isset($_REQUEST['pk_ids']) ? $_REQUEST['pk_ids'] : '';
            $pk_ids = is_array($pk_ids) ? implode(',', $pk_ids) : '';
            if ($pk_ids) {
                $sql = "UPDATE {$wpdb->prefix}fat_sb_price_package SET pk_status=%d WHERE pk_id IN ({$pk_ids}) ";
                $sql = $wpdb->prepare($sql, -1);
                $result = $wpdb->query($sql);
                return array(
                    'result' => $result,
                    'message_success' => $result > 0 ? esc_html__('Package(s) have been deleted', 'fat-services-booking') : '',
                    'message_error' => $result <= 0 ? esc_html__('Package(s) can not delete', 'fat-services-booking') : ''
                );
            } else {
                return array(
                    'result' => -1,
                    'message_error' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }

        }

        public function delete_package_order()
        {
            global $wpdb;
            $pko_id = isset($_REQUEST['pko_id']) ? $_REQUEST['pko_id'] : '';
            if ($pko_id) {
                $sql = "UPDATE {$wpdb->prefix}fat_sb_price_package_order SET pko_process_status=-1, pko_description = CONCAT (pko_description,'. Delete by admin') WHERE pko_id = %d ";
                $sql = $wpdb->prepare($sql, $pko_id);
                $result = $wpdb->query($sql);
                return array(
                    'result' => $result,
                    'message_success' => $result > 0 ? esc_html__('Order have been deleted', 'fat-services-booking') : '',
                    'message_error' => $result <= 0 ? esc_html__('Order can not delete', 'fat-services-booking') : ''
                );
            } else {
                return array(
                    'result' => -1,
                    'message_error' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }

        }

        public function save_package_booking_fe()
        {

            $current_user = wp_get_current_user();
            if (!isset($current_user->ID)) {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please login before purchase price package', 'fat-services-booking')
                );
            }
            $validate = 1;
            $validate = apply_filters('fat_sb_purchase_package_validate', $validate);
            if (is_array($validate) && isset($validate['result']) && $validate['result'] == -1) {
                return array(
                    'result' => -1,
                    'message' => esc_html__('You are not on the list of allowed purchase package', 'fat-services-booking')
                );
            }

            $pk_id = isset($_REQUEST['pk_id']) && $_REQUEST['pk_id'] ? $_REQUEST['pk_id'] : 0;
            $payment_method = isset($_REQUEST['payment_method']) && $_REQUEST['payment_method'] ? $_REQUEST['payment_method'] : '';
            $setting_db = FAT_DB_Setting::instance();
            $setting = $setting_db->get_setting();
            if ($pk_id > 0 && $payment_method) {
                global $wpdb;
                $sql = "SELECT pk_id, pk_name, pk_price, pk_price_for_payment FROM {$wpdb->prefix}fat_sb_price_package WHERE pk_id=%d AND pk_status=1";
                $sql = $wpdb->prepare($sql, $pk_id);
                $package_info = $wpdb->get_results($sql);
                if (count($package_info) > 0) {
                    $package_info = $package_info[0];
                    $package_booking = array();
                    $package_booking['pko_process_status'] = -1;
                    $package_booking['pk_id'] = $pk_id;
                    $package_booking['pk_price'] = $package_info->pk_price;
                    $package_booking['pk_price_for_payment'] = $package_info->pk_price_for_payment;
                    $package_booking['pko_total_pay'] = $package_info->pk_price;
                    $package_booking['pko_user_email'] = $current_user->user_email;
                    $package_booking['pko_user_id'] = $current_user->ID;
                    $package_booking['pko_create_date'] = current_time('mysql', 0);
                    $package_booking['pko_gateway_type'] = $payment_method;

                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_price_package_order', $package_booking);
                    $pko_id = $result > 0 ? $wpdb->insert_id : $result;
                    $approve_url = '';
                    if ($pko_id > 0 && $payment_method === 'paypal') {
                        $payment_desc = esc_html__('User:', 'fat-services-booking') . $current_user->user_login;
                        $payment_desc .= esc_html__('. User Full Name:', 'fat-services-booking') . $current_user->display_name;
                        $payment_desc .= esc_html__('. Price Package:', 'fat-services-booking') . $package_info->pk_name;
                        $payment_desc .= esc_html__('. Price:', 'fat-services-booking') . $package_info->pk_price;
                        $payment_desc .= esc_html__('. Price for payment:', 'fat-services-booking') . $package_info->pk_price_for_payment;
                        $url = esc_url(home_url());
                        $customer = $current_user->user_firstname . ' ' . $current_user->user_lastname . '(' . $current_user->user_email . ')';
                        $payment = new FAT_Payment_Package();
                        $payment_result = $payment->payment($pko_id, $customer, $package_info->pk_name, $package_info->pk_id, 1, $package_info->pk_price, 0, $package_info->pk_price, $setting['currency'], $payment_desc, $url);

                        if ($payment_result['result'] == -1) {
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id = %d";
                            $sql = $wpdb->prepare($sql, $pko_id);
                            $wpdb->query($sql);
                            return array(
                                'result' => -1,
                                'message' => $payment_result['message']
                            );
                        } else {
                            $approve_url = $payment_result['approval_url'];
                        }
                    }

                    if ($pko_id > 0 && $payment_method === 'myPOS') {
                        $payment = new FAT_Payment_Package();
                        $payment_result = $payment->myPOS_payment($current_user->user_firstname, $current_user->user_lastname, $current_user->user_email, '', '', $setting['currency'], $pko_id, 1, $package_info->pk_price, $package_info->pk_name);
                        if ($payment_result['result'] == -1) {
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id = %d";
                            $sql = $wpdb->prepare($sql, $pko_id);
                            $wpdb->query($sql);
                        }
                        return $payment_result;
                    }

                    if ($pko_id > 0 && $payment_method === 'stripe') {
                        $currency = $setting_db->get_currency_setting();
                        $description = esc_html__('Customer:', 'fat-services-booking') . $current_user->display_name;
                        $description .= esc_html__('. Package name:', 'fat-services-booking') . $package_info->pk_name;
                        $description .= esc_html__('. Price:', 'fat-services-booking') . $package_info->pk_price;
                        $description .= esc_html__('. Price for payment:', 'fat-services-booking') . $package_info->pk_price_for_payment;
                        $description .= esc_html__('.  Total fees: ', 'fat-event') . $package_info->pk_price . $currency['symbol'];
                        $payment = new FAT_Payment_Package();
                        $result = $payment->stripe_payment($pko_id, $package_info->pk_price, $description);
                        if ($result['code'] < 0) {
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id = %d";
                            $sql = $wpdb->prepare($sql, $pko_id);
                            $wpdb->query($sql);
                        }
                        return array(
                            'result' => $result['code'],
                            'message' => $result['code'] > 0 ? esc_html__('Payment success', 'fat-sb-booking') : esc_html__('An error occurred while executing', 'fat-sb-booking')
                        );
                    }

                    if ($pko_id > 0 && $payment_method === 'przelewy24') {
                        $total_pay = floatval($package_info->pk_price) * 100 ;

                        $p24_merchant_id = isset($setting['p24_merchant_id']) ? $setting['p24_merchant_id'] : '';
                        $p24_pos_id = isset($setting['p24_pos_id']) ? $setting['p24_pos_id'] : '';
                        $p24_mode = isset($setting['p24_mode']) ? $setting['p24_mode'] : 'sandbox';
                        $currency = $setting_db->get_currency_setting();
                        $currency = isset($currency['currency']) ? $currency['currency'] : 'PLN';
                        $p24_crc = isset($setting['p24_crc']) ? $setting['p24_crc'] : '';

                        $p24_session_id = uniqid();
                        $p24_sign = $p24_session_id . '|' . $p24_merchant_id . '|' . $total_pay . '|' . $currency . '|' . $p24_crc;
                        $p24_sign = md5($p24_sign);

                        $p24_url_return = home_url('/');
                        $p24_url_return = add_query_arg(array(
                            'source' => 'fat_sb_package_order_p24',
                            'action' => 'p24_return',
                            'pko_id' => $pko_id,
                            'session_id' => $p24_session_id,
                            'merchant_id' => $p24_merchant_id,
                            'total' => $total_pay,
                            'currency' => $currency), $p24_url_return
                        );
                        $p24_url_status = home_url('/');
                        $p24_url_status = add_query_arg(array(
                            'source' => 'fat_sb_package_order_p24',
                            'action' => 'p24_status',
                            'pko_id' => $pko_id,
                            'session_id' => $p24_session_id,
                            'merchant_id' => $p24_merchant_id,
                            'total' => $total_pay,
                            'currency' => $currency), $p24_url_status
                        );

                        $postArgs = array(
                            'p24_client' =>  $current_user->display_name,
                            'p24_session_id' => $p24_session_id,
                            'p24_merchant_id' => $p24_merchant_id,
                            'p24_pos_id' => $p24_pos_id,
                            'p24_amount' => $total_pay,
                            'p24_currency' => $currency,
                            'p24_description' => esc_html__('pakiet ceny zakupu ','fat-services-booking'). $pko_id,
                            'p24_email' => $current_user->user_email,
                            'p24_country' => 'PL',
                            'p24_url_return' => $p24_url_return,
                            'p24_url_status' => $p24_url_status,
                            'p24_api_version' => '3.2',
                            'p24_sign' => $p24_sign
                        );

                        $wpdb->update($wpdb->prefix . 'fat_sb_price_package_order', array('pko_description' => json_encode($postArgs)), array('pko_id' => $pko_id));

                        $p24_register_url = $p24_mode=='sandbox' ? 'https://sandbox.przelewy24.pl/trnRegister' : 'https://secure.przelewy24.pl/trnRegister' ;
                        $curl = curl_init($p24_register_url);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_HEADER, false);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
                        $response = curl_exec($curl);

                        curl_close($curl);
                        if (!empty($response)) {
                            error_log($response);
                            $response = explode('&',$response);
                            $result = $response[0];
                            $result = explode('=',$result);
                            if($result[1]=='0'){
                                $token = explode('=',$response[1])[1];
                                $wpdb->update($wpdb->prefix . 'fat_sb_price_package_order', array('pko_gateway_response' => $token), array('pko_id' => $pko_id));
                                $p24_request_url = $p24_mode=='sandbox' ? 'https://sandbox.przelewy24.pl/trnRequest/' : 'https://secure.przelewy24.pl/trnRequest/' ;
                                return array(
                                    'result' => 1,
                                    'p24_url' => $p24_request_url.$token
                                );
                            }
                        }

                        //delete booking if have error
                        $sql = "DELETE FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id = %d";
                        $sql = $wpdb->prepare($sql, $pko_id);
                        $wpdb->query($sql);
                        return array(
                            'code' => -1,
                            'message' => esc_html__('An error occurred during execution','fat-services-booking')
                        );

                    }

                    return array(
                        'result' => $pko_id,
                        'pp_url' => isset($approve_url) ? $approve_url : ''
                    );

                } else {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('The package does not exist or has been removed', 'fat-services-booking')
                    );
                }
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }

        }

        public function add_credit()
        {
            $current_user = wp_get_current_user();
            if (!isset($current_user->ID)) {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please login before purchase price package', 'fat-services-booking')
                );
            }
            $validate = 1;
            $validate = apply_filters('fat_sb_purchase_package_validate', $validate);
            if (is_array($validate) && isset($validate['result']) && $validate['result'] == -1) {
                return array(
                    'result' => -1,
                    'message' => esc_html__('You are not on the list of allowed add credit', 'fat-services-booking')
                );
            }

            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if (!isset($data['pk_id']) || !isset($data['c_id'])) {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }
            global $wpdb;
            $sql = "SELECT pk_id, pk_image_id, pk_price, pk_price_for_payment, pk_name, pk_description
                    FROM {$wpdb->prefix}fat_sb_price_package
                    WHERE pk_status=1 AND pk_id=%d";
            $sql = $wpdb->prepare($sql, $data['pk_id']);
            $package = $wpdb->get_results($sql);
            if (count($package) > 0) {
                $package = $package[0];

                $sql = "SELECT c_id, c_first_name, c_last_name, c_phone, c_email, c_user_id
                                        FROM {$wpdb->prefix}fat_sb_customers 
                                        WHERE c_id=%d";
                $sql = $wpdb->prepare($sql, $data['c_id']);
                $customer = $wpdb->get_results($sql);
                if (count($customer)) {
                    $customer = $customer[0];
                    $package_booking = array();
                    $package_booking['pko_process_status'] = 1;
                    $package_booking['pk_id'] = $data['pk_id'];
                    $package_booking['pk_price'] = $package->pk_price;
                    $package_booking['pk_price_for_payment'] = $package->pk_price_for_payment;
                    $package_booking['pko_total_pay'] = $package->pk_price;
                    $package_booking['pko_user_email'] = $customer->c_email;
                    $package_booking['pko_user_id'] = $data['c_id'];
                    $package_booking['pko_create_date'] = current_time('mysql', 0);
                    $package_booking['pko_gateway_type'] = 'onsite';
                    $package_booking['pko_gateway_status'] = '1';
                    $package_booking['pko_description'] = isset($data['pko_description']) ? $data['pko_description'] : '';

                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_price_package_order', $package_booking);
                    $pko_id = $result > 0 ? $wpdb->insert_id : $result;
                    return array(
                        'result' => $pko_id
                    );
                } else {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('Customer does not exist or has been removed', 'fat-services-booking')
                    );
                }

            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Package price does not exist or has been removed', 'fat-services-booking')
                );
            }

        }

        public function get_package_order()
        {
            global $wpdb;
            $page = isset($_REQUEST['page']) && $_REQUEST['page'] ? $_REQUEST['page'] : 1;
            $start_date = isset($_REQUEST['start_date']) && $_REQUEST['start_date'] ? $_REQUEST['start_date'] : '';
            $end_date = isset($_REQUEST['end_date']) && $_REQUEST['end_date'] ? $_REQUEST['end_date'] : '';
            $user_email = isset($_REQUEST['user_email']) && $_REQUEST['user_email'] ? $_REQUEST['user_email'] : '';

            $sql = "SELECT pko_id, PKO.pk_price, PKO.pk_price_for_payment, pko_user_email, pko_process_status, pko_myPOS_status, pko_gateway_status, pko_gateway_type,
                          pko_create_date, pk_name, pko_description
                    FROM {$wpdb->prefix}fat_sb_price_package_order AS PKO
                    LEFT JOIN {$wpdb->prefix}fat_sb_price_package AS PK
                    ON PKO.pk_id = PK.pk_id
                    WHERE pko_process_status!=%d AND pko_user_email LIKE '%{$user_email}%'";

            if ($start_date && $end_date) {
                $sql .= " AND DATE(pko_create_date) BETWEEN '{$start_date}' AND '{$end_date}'";
            }

            $sql = $wpdb->prepare($sql, -1);
            $orders = $wpdb->get_results($sql);
            $total = count($orders);
            $fat_db_setting = FAT_DB_Setting::instance();
            $setting = $fat_db_setting->get_setting();
            $item_per_page = isset($setting['item_per_page']) ? $setting['item_per_page'] : 10;
            $number_of_page = $total / $item_per_page + ($total % $item_per_page > 0 ? 1 : 0);
            $page = $page > $number_of_page ? $number_of_page : $page;
            $page = ($page - 1) * $item_per_page;
            $orders = array_slice($orders, $page, $item_per_page);
            return array(
                'total' => $total,
                'orders' => $orders
            );
        }

        public function get_package_order_by_user($user_id)
        {
            global $wpdb;
            $sql = "SELECT pko_id, PKO.pk_price, PKO.pk_price_for_payment, pko_user_email, pko_process_status, pko_myPOS_status, pko_gateway_status, pko_gateway_type,
                          pko_create_date, pk_name
                    FROM {$wpdb->prefix}fat_sb_price_package_order AS PKO
                    LEFT JOIN {$wpdb->prefix}fat_sb_price_package AS PK
                    ON PKO.pk_id = PK.pk_id
                    WHERE pko_user_id = %d AND pko_
                    ORDER BY pko_id DESC";

            $sql = $wpdb->prepare($sql, $user_id);
            $orders = $wpdb->get_results($sql);
            return $orders;
        }

        public static function get_price_amount_by_user($user_email)
        {
            global $wpdb;
            $sql = "SELECT  pk_price_for_payment FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_process_status=%d AND pko_user_email=%s";
            $sql = $wpdb->prepare($sql, 1, $user_email);
            $package_order = $wpdb->get_results($sql);
            $has_payment = 0;
            $buy_amount = 0;
            $has_order = 0;
            if (count($package_order) > 0) {
                $has_order = 1;
                foreach ($package_order as $po) {
                    $buy_amount += $po->pk_price_for_payment;
                }

                $sql = "SELECT upk_payment_amount FROM {$wpdb->prefix}fat_sb_user_payment_by_package  AS UPK 
                        INNER JOIN {$wpdb->prefix}fat_sb_booking AS BK ON UPK.b_id = BK.b_id 
                        WHERE u_email=%s AND BK.b_process_status IN (0,1)";
                $sql = $wpdb->prepare($sql, $user_email);
                $user_package_payment = $wpdb->get_results($sql);
                $has_payment = 0;
                foreach ($user_package_payment as $upk) {
                    $has_payment += $upk->upk_payment_amount;
                }
            }
            return array(
                'buy_amount' => $buy_amount,
                'has_payment' => $has_payment,
                'has_order' => $has_order
            );
        }

        public static function admin_get_price_amount_by_user($user_email)
        {
            global $wpdb;
            $sql = "SELECT  pk_price_for_payment FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_process_status=%d AND pko_user_email=%s";
            $sql = $wpdb->prepare($sql, 1, $user_email);
            $package_order = $wpdb->get_results($sql);
            $has_payment = 0;
            $buy_amount = 0;
            if (count($package_order) > 0) {
                foreach ($package_order as $po) {
                    $buy_amount += $po->pk_price_for_payment;
                }

                $sql = "SELECT upk_payment_amount FROM {$wpdb->prefix}fat_sb_user_payment_by_package  AS UPK 
                        INNER JOIN {$wpdb->prefix}fat_sb_booking AS BK ON UPK.b_id = BK.b_id 
                        WHERE u_email=%s AND BK.b_process_status =1";
                $sql = $wpdb->prepare($sql, $user_email);
                $user_package_payment = $wpdb->get_results($sql);
                $has_payment = 0;
                foreach ($user_package_payment as $upk) {
                    $has_payment += $upk->upk_payment_amount;
                }
            }
            return array(
                'buy_amount' => $buy_amount,
                'has_payment' => $has_payment
            );
        }
    }
}