<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 6/13/2019
 * Time: 4:52 PM
 */

if (!class_exists('FAT_Payment')) {
    class FAT_Payment{
        function __construct()
        {

        }

        /**
         * Get paypal access token
         * @param $url
         * @param $postArgs
         * @return mixed
         */
        private function get_paypal_access_token($url, $postArgs)
        {
            $setting_db = new FAT_DB_Setting();
            $setting = $setting_db->get_setting();
            $client_id = isset($setting['paypal_client_id']) ? $setting['paypal_client_id'] : '';
            $secret_key = isset($setting['paypal_secret']) ? $setting['paypal_secret'] : '';

            if($client_id && $secret_key){
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_USERPWD, $client_id . ":" . $secret_key);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
                $response = curl_exec($curl);
                if (empty($response)) {
                    curl_close($curl);
                    return array(
                        'code' => -1,
                        'message' => curl_error($curl)
                    );

                } else {
                    $info = curl_getinfo($curl);
                    curl_close($curl);
                    if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                        return array(
                            'code' => -1,
                            'message' => $response
                        );
                    }
                }
                $response = json_decode($response);
                return array(
                    'code' => 1,
                    'access_token' => $response->access_token
                );
            }else{
                return array(
                    'code' => -1,
                    'message' => esc_html__('Please input Paypal Client ID and Secret','fat-event')
                );
            }
        }

        /**
         * Execute paypal request
         * @param $url
         * @param $jsonData
         * @param $access_token
         * @return array|mixed|object
         */
        private function execute_paypal_request($url, $jsonData, $access_token)
        {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
            ));

            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
            $response = curl_exec($curl);
            if (empty($response)) {
                curl_close($curl);
                die(curl_error($curl));
            } else {
                $info = curl_getinfo($curl);
                curl_close($curl);
                if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                    echo "Received error: " . $info['http_code'] . "\n";
                    echo "Raw response:" . $response . "\n";
                    die();
                }
            }
            $jsonResponse = json_decode($response, TRUE);
            return $jsonResponse;
        }

        public function payment($booking_id, $customer, $service_name, $service_id, $quantity, $price, $tax, $total_price, $currency, $description, $current_url){
            $setting_db = new FAT_DB_Setting();
            $setting = $setting_db->get_setting();
            $host = isset($setting['paypal_sandbox']) && $setting['paypal_sandbox'] =='live' ? 'https://api.paypal.com' : 'https://api.sandbox.paypal.com';

            $url = $host . '/v1/oauth2/token';
            $postArgs = 'grant_type=client_credentials';
            $access_token = $this->get_paypal_access_token($url, $postArgs);
            if($access_token['code']!=1){
                $message = esc_html__('Cannot get access token. Please check Paypal\'s clientID and secret','fat-services-booking');
                if(isset($access_token['message'])){
                    $message = json_decode($access_token['message']);
                    $message = isset($message->error_description) ? $message->error_description : $message;
                }
                return array(
                    'result' => $access_token['code'],
                    'message' => $message
                );
            }
            $url = $host . '/v1/payments/payment';
            $cancel_link = add_query_arg(array('source' => 'fat_sb_booking','action' => 'paypal_cancel'), $current_url);
            $return_link = add_query_arg(array('source' => 'fat_sb_booking','action' => 'payment_return'), $current_url);
            $subtotal = $total_price - $tax;

            $payment = array(
                'intent' => 'sale',
                "redirect_urls" => array(
                    "return_url" => $return_link,
                    "cancel_url" => $cancel_link
                ),
                'payer' => array("payment_method" => "paypal"),
            );
            $payment['transactions'][0] = array(
                'amount' => array(
                    'total' => number_format($total_price,2),
                    'currency' => $currency,
                    'details' => array(
                        'subtotal' => number_format($subtotal,2),
                        'tax' =>  $tax,
                        'shipping' => '0.00',
                    )
                ),
                'description' => $description,
                "custom" => $customer,
                "invoice_number" => $booking_id,
            );

            $payment['transactions'][0]['item_list']['items'][] = array(
                'quantity' => $quantity,
                'name' => $service_name,
                'price' =>  number_format($price,2),
                'currency' => $currency,
                'sku' => $service_id,
            );
            error_log(serialize($payment));
            $jsonEncode = json_encode($payment);
            $json_response = $this->execute_paypal_request($url, $jsonEncode, $access_token['access_token']);
            error_log('paypal response:'.serialize($json_response));
            $payment_approval_url = '';
            $payment_execute_url = '';
            foreach ($json_response['links'] as $link) {
                if ($link['rel'] == 'approval_url') {
                    $payment_approval_url = $link['href'];
                }
                if ($link['rel'] == 'execute') {
                    $payment_execute_url = $link['href'];
                }
            }
            global $wpdb;
            $b_gateway_response = 'paypal_approval_url: '.$payment_approval_url. ' ,paypal_result:'.serialize($json_response);

            $sql = "SELECT b_detail_id FROM {$wpdb->prefix}fat_sb_booking_multiple_days WHERE b_id=%d";
            $sql = $wpdb->prepare($sql, $booking_id);
            $booking_md = $wpdb->get_results($sql);
            $b_ids = array($booking_id);
            foreach($booking_md as $bmd){
                $b_ids[] = $bmd->b_detail_id;
            }
            $b_ids = implode(',',$b_ids);

            $wpdb->query("UPDATE {$wpdb->prefix}fat_sb_booking SET b_gateway_id = '{$json_response['id']}', b_gateway_response='{$b_gateway_response}', b_gateway_execute_url='{$payment_execute_url}'
                                      WHERE b_id IN ({$b_ids})");

            return array(
                'result' => 1,
                'approval_url' => $payment_approval_url
            );
        }

        public function stripe_payment($booking_id, $total_price, $description){
            $stripe_token = isset($_REQUEST['token']) && $_REQUEST['token'] ? $_REQUEST['token'] : '';
            $setting_db = FAT_DB_Setting::instance();
            $setting = $setting_db->get_setting();
            if ($stripe_token && isset($setting['stripe_secret_key']) && $setting['stripe_secret_key']) {
                global $wpdb;
                try {
                    $headers = array('Authorization: Bearer ' . $setting['stripe_secret_key']);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
                        array(
                            'amount' =>  (round($total_price) * 100),
                            'currency' => strtolower($setting['currency']),
                            'source' => $stripe_token,
                            'description' => $description
                        )
                    ));
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response);

                    $sql = "SELECT b_detail_id FROM {$wpdb->prefix}fat_sb_booking_multiple_days WHERE b_id=%d";
                    $sql = $wpdb->prepare($sql, $booking_id);
                    $booking_md = $wpdb->get_results($sql);
                    $b_ids = array($booking_id);
                    foreach($booking_md as $bmd){
                        $b_ids[] = $bmd->b_detail_id;
                    }
                    $b_ids = implode(',',$b_ids);

                    if (isset($response->error)) {
                        $wpdb->query("UPDATE {$wpdb->prefix}fat_sb_booking SET b_gateway_status = -1, b_pay_now=0, b_gateway_response='{$response->error->message}'
                                      WHERE b_id IN ({$b_ids})");
                        $result = array(
                            'code' => -1,
                            'message' => $response->error->message
                        );

                    } else {
                        $gateway_response = 'id:'.$response->id. ' balance_transaction:'.$response->balance_transaction;

                        $db_setting = FAT_DB_Setting::instance();
                        $setting = $db_setting->get_setting();
                        $b_process_status = isset($setting['b_process_status']) ? $setting['b_process_status'] : 0;

                        $wpdb->query("UPDATE {$wpdb->prefix}fat_sb_booking SET b_process_status={$b_process_status}, b_gateway_status = 1, b_pay_now=1, b_gateway_response='{$gateway_response}'
                                      WHERE b_id IN ({$b_ids})");

                        do_action('fat_sb_booking_completed',$booking_id);
                        $result = array(
                            'code' => $booking_id,
                        );
                    }

                } catch (Exception $e) {
                    error_log(serialize($e));
                    $result = array(
                        'code' => -1,
                        'message' => esc_html__('An error occurred during execution', 'fat-event')
                    );
                }
            } else {
                $result = array(
                    'code' => -1,
                    'message' => esc_html__('Sorry Stripe gateway configuration not ready', 'fat-event')
                );
            }

            return $result;
        }

        public function payment_update_status(){

            if(isset($_GET['source']) && $_GET['source'] ==='fat_sb_booking' && isset($_GET['token']) ){
                global $wp;

                $paypal_id = isset($_GET['paymentId']) && $_GET['paymentId'] ? $_GET['paymentId'] : '';
                $payer_ID = isset($_GET['PayerID']) && $_GET['PayerID'] ? $_GET['PayerID'] : '' ;
                error_log('paypal_id:'.$paypal_id. ' payer_ID:'.$payer_ID);

                // validate payment status
                $setting_db = new FAT_DB_Setting();
                $setting = $setting_db->get_setting();
                $success_url = isset($setting['success_page']) ? $setting['success_page'] : '';
                $error_url =  isset($setting['error_page']) ? $setting['error_page'] : '';

                if(isset($_REQUEST['action']) && $_REQUEST['action']=='paypal_cancel'){
                    if ( wp_redirect( $error_url ) ) {
                        exit;
                    }
                }

                $host = isset($setting['paypal_sandbox']) && $setting['paypal_sandbox'] =='live' ? 'https://api.paypal.com' : 'https://api.sandbox.paypal.com';
                $url = $host . '/v1/oauth2/token';
                $postArgs = 'grant_type=client_credentials';
                $access_token = $this->get_paypal_access_token($url, $postArgs);
                if($access_token['code']!=1){
                    error_log(serialize($access_token));
                    if ( wp_redirect( $error_url ) ) {
                        exit;
                    }
                }

                $url = $host . '/v1/payments/payment/'. $paypal_id;
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Authorization: Bearer ' . $access_token['access_token'],
                    'Content-Type: application/json'
                ));

                global $wpdb;
                $bookings = $wpdb->get_results('SELECT b_id, b_gateway_execute_url  FROM ' . $wpdb->prefix . 'fat_sb_booking WHERE b_gateway_id="'. $paypal_id .'"');

                if(!isset($bookings[0]->b_id)){
                    if ( wp_redirect( $error_url ) ) {
                        exit;
                    }
                }
                $bookings = $bookings[0];

                $sql = "SELECT b_detail_id FROM {$wpdb->prefix}fat_sb_booking_multiple_days WHERE b_id=%d";
                $sql = $wpdb->prepare($sql, $bookings->b_id);
                $booking_md = $wpdb->get_results($sql);
                $b_ids = array($bookings->b_id);
                foreach($booking_md as $bmd){
                    $b_ids[] = $bmd->b_detail_id;
                }
                $b_ids = implode(',',$b_ids);

                $response = curl_exec($curl);
                if (empty($response)) {
                    curl_close($curl);
                } else {
                    $info = curl_getinfo($curl);
                    curl_close($curl);
                    if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                        error_log('Received error:'. $info['http_code']);
                        error_log('Raw response:' . $response);
                        $error_url = $error_url ? $error_url : home_url( $wp->request );
                        $wpdb->query("DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE b_id = {$bookings->b_id}");

                        if ( wp_redirect( $error_url ) ) {
                            exit;
                        }
                    }
                }

                $jsonResponse = json_decode($response, TRUE);
                if(isset($jsonResponse['state']) && $jsonResponse['state']=='created'){
                    //execute payment
                    $jsonEncode =  json_encode(array(
                        'payer_id' => $payer_ID
                    ));
                    $jsonResponse = $this->execute_paypal_request($bookings->b_gateway_execute_url, $jsonEncode, $access_token['access_token']);
                    if(isset($bookings->b_id)){
                        $pay_now =  $jsonResponse['state']=='approved' ? 1 : 0;
                        $gateway_response = serialize($jsonResponse);
                        $b_process_status = isset($setting['b_process_status']) ? $setting['b_process_status'] : 0;
                        if( $jsonResponse['state']=='approved' ||  $jsonResponse['state']=='created'){
                            $wpdb->query("UPDATE {$wpdb->prefix}fat_sb_booking SET b_process_status = {$b_process_status}, b_gateway_status = '{$jsonResponse['state']}', b_pay_now={$pay_now}, b_gateway_response='{$gateway_response}'
                                      WHERE b_id IN ({$b_ids})");

                            do_action('fat_sb_booking_completed', $bookings->b_id);
                        }else{
                            $wpdb->query("DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE b_id  IN ({$bookings->b_id})");
                        }
                        //send mail
                        try{
                            $booking_db = FAT_DB_Bookings::instance();
                            $booking_db->send_booking_mail($bookings->b_id);
                        }catch(Exception $err){}
                    }
                }

                $success_url = $success_url ? get_permalink($success_url) : home_url( $wp->request );
                $success_url = add_query_arg(array('bid' => $bookings->b_id), $success_url);
                if ( wp_redirect( $success_url ) ) {
                    exit;
                }
            }
        }

        public function myPOS_payment($first_name, $last_name, $email, $phone, $address, $currency, $booking_id, $quantity, $price, $service_name)
        {
            require_once FAT_SERVICES_DIR_PATH . '/libs/myPOS/IPC/Loader.php';
            global $wpdb;
            $setting_db = new FAT_DB_Setting();
            $setting = $setting_db->get_setting();

            $private_key = $setting['myPOS_private_key'];
            $public_key = $setting['myPOS_public_certificate'];
            $checkout_url = $setting['myPOS_sandbox'] == 'live' ? 'https://www.mypos.eu/vmp/checkout' : 'https://mypos.eu/vmp/checkout-test';


            if ($private_key && $public_key) {
                $cnf = new \Mypos\IPC\Config();
                $cnf->setIpcURL($checkout_url);
                $cnf->setLang('en');
                $cnf->setPrivateKey($private_key);
                $cnf->setAPIPublicKey($public_key);
                $cnf->setEncryptPublicKey($public_key);
                $cnf->setKeyIndex($setting['myPOS_key_index']);
                $cnf->setSid($setting['myPOS_storeID']);
                $cnf->setVersion('1.3');
                $cnf->setWallet($setting['myPOS_client_number']);

                $customer = new \Mypos\IPC\Customer();
                $customer->setFirstName($first_name);
                $customer->setLastName($last_name);
                $customer->setEmail($email);
                $customer->setPhone($phone);
                //$customer->setAddress($address);

                $cart = new \Mypos\IPC\Cart;
                $price = number_format($price,2);
                $cart->add($service_name, $quantity,  $price); //name, quantity, price

                $sign = uniqid('fat_sb_');
                $url_cancel = home_url('/');
                $url_cancel = add_query_arg(array('source' => 'fat_sb_booking_myPOS', 'action' => 'myPOS_cancel', 'bid' => $booking_id, 'sign' => $sign), $url_cancel);
                $url_ok = home_url('/');
                $url_ok = add_query_arg(array('source' => 'fat_sb_booking_myPOS', 'action' => 'myPOS_ok', 'bid' => $booking_id, 'sign' => $sign), $url_ok);
                $url_notify = home_url('/');
                $url_notify = add_query_arg(array('source' => 'fat_sb_booking_myPOS', 'action' => 'myPOS_notify', 'bid' => $booking_id, 'sign' => $sign), $url_notify);


                $sql = "SELECT b_detail_id FROM {$wpdb->prefix}fat_sb_booking_multiple_days WHERE b_id=%d";
                $sql = $wpdb->prepare($sql, $booking_id);
                $booking_md = $wpdb->get_results($sql);
                $b_ids = array($booking_id);
                foreach($booking_md as $bmd){
                    $b_ids[] = $bmd->b_detail_id;
                }
                $b_ids = implode(',',$b_ids);

                $sql = "UPDATE {$wpdb->prefix}fat_sb_booking SET b_myPOS_status = %s, b_myPOS_sign=%s WHERE  b_id IN ({$b_ids}) ";
                $sql = $wpdb->prepare($sql, '', $sign);
                $wpdb->query($sql);

                $purchase = new \Mypos\IPC\Purchase($cnf);
                $purchase->setUrlCancel($url_cancel); //User comes here after purchase cancelation
                $purchase->setUrlOk($url_ok); //User comes here after purchase success
                $purchase->setUrlNotify($url_notify); //IPC sends POST reuquest to this address with purchase status
                $purchase->setOrderID('fat_sb_booking_' . $booking_id); //Some unique ID
                $purchase->setCurrency($currency);
                $purchase->setCustomer($customer);
                $purchase->setCart($cart);

                $purchase->setCardTokenRequest(\Mypos\IPC\Purchase::CARD_TOKEN_REQUEST_PAY_AND_STORE);
                $purchase->setPaymentParametersRequired(\Mypos\IPC\Purchase::PURCHASE_TYPE_FULL);
                $purchase->setPaymentMethod(\Mypos\IPC\Purchase::PAYMENT_METHOD_BOTH);

                try {
                    $form = $purchase->process();
                    return array(
                        'result' => $booking_id,
                        'form' => $form
                    );
                } catch (\Mypos\IPC\IPC_Exception $ex) {
                    error_log($ex->getMessage());
                    return array(
                        'result' => -1,
                        'message' => $ex->getMessage()
                    );
                }
            }
        }

        public function myPOS_update_status()
        {
            if (isset($_GET['source']) && $_GET['source'] === 'fat_sb_booking_myPOS' && isset($_REQUEST['action']) && isset($_REQUEST['sign'])) {
                $booking_id = $_REQUEST['bid'];
                $setting_db = new FAT_DB_Setting();
                $setting = $setting_db->get_setting();

                global $wp, $wpdb;
                $url_redirect = home_url($wp->request);

                $sql = "SELECT b_detail_id FROM {$wpdb->prefix}fat_sb_booking_multiple_days WHERE b_id=%d";
                $sql = $wpdb->prepare($sql, $booking_id);
                $booking_md = $wpdb->get_results($sql);
                $b_ids = array($booking_id);
                foreach($booking_md as $bmd){
                    $b_ids[] = $bmd->b_detail_id;
                }
                $b_ids = implode(',',$b_ids);

                $sql = "SELECT b_id, b_myPOS_sign, b_myPOS_status  FROM {$wpdb->prefix}fat_sb_booking WHERE b_id=%d ";
                $sql = $wpdb->prepare($sql, $booking_id);
                $bookings = $wpdb->get_results($sql);

                if(!isset($bookings[0]->b_id)){
                    if ($setting['myPOS_success_page'] && $setting['myPOS_error_page']) {
                        $url_redirect = get_permalink($setting['myPOS_success_page']);
                    }
                    if ( wp_redirect( $url_redirect ) ) {
                        exit;
                    }
                }
                $bookings = $bookings[0];

                $myPOS_sign = $bookings->b_myPOS_sign;
                $myPOS_status =  $bookings->b_myPOS_status;

                if ($myPOS_status == '') {
                    require_once FAT_SERVICES_DIR_PATH . '/libs/myPOS/IPC/Loader.php';

                    $private_key = $setting['myPOS_private_key'];
                    $public_key = $setting['myPOS_public_certificate'];
                    $checkout_url = $setting['myPOS_sandbox'] == 'live' ? 'https://www.mypos.eu/vmp/checkout' : 'https://mypos.eu/vmp/checkout-test';

                    $cnf = new \Mypos\IPC\Config();
                    $cnf->setIpcURL($checkout_url);
                    $cnf->setLang('en');
                    $cnf->setPrivateKey($private_key);
                    $cnf->setAPIPublicKey($public_key);
                    $cnf->setEncryptPublicKey($public_key);
                    $cnf->setKeyIndex($setting['myPOS_key_index']);
                    $cnf->setSid($setting['myPOS_storeID']);
                    $cnf->setVersion('1.3');
                    $cnf->setWallet($setting['myPOS_client_number']);

                    try {
                        $responce = \Mypos\IPC\Response::getInstance($cnf, $_POST, \Mypos\IPC\Defines::COMMUNICATION_FORMAT_POST);
                        $data = $responce->getData(CASE_LOWER);
                        if ($data['ipcmethod'] === 'IPCPurchaseNotify') {
                            $b_process_status = isset($setting['b_process_status']) ? $setting['b_process_status'] : 0;
                            $sql = "UPDATE {$wpdb->prefix}fat_sb_booking SET b_process_status=%d, b_myPOS_cardtoken = %s, b_myPOS_ipc_trnref=%s WHERE b_id IN ({$b_ids}) ";
                            $sql = $wpdb->prepare($sql, $b_process_status, $data['cardtoken'], $data['ipc_trnref']);
                            $wpdb->query($sql);
                            status_header(200);
                            echo 'OK';
                            exit();

                        }
                        if ($data['ipcmethod'] === 'IPCPurchaseCancel' || $data['ipcmethod'] === 'IPCPurchaseRollback') {
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE b_id IN ({$b_ids})";
                            $wpdb->query($sql);
                            if ($setting['myPOS_error_page']) {
                                $url_redirect = get_permalink($setting['myPOS_error_page']);
                            }
                            wp_redirect($url_redirect);
                            exit();
                        }
                        if ($data['ipcmethod'] === 'IPCPurchaseOK') {
                            $b_process_status = isset($setting['b_process_status']) ? $setting['b_process_status'] : 0;
                            $sql = "UPDATE {$wpdb->prefix}fat_sb_booking SET b_process_status=%d, b_myPOS_status = %s WHERE b_id IN ({$b_ids}) ";
                            $sql = $wpdb->prepare($sql, $b_process_status,  'IPCPurchaseOK');
                            $wpdb->query($sql);

                            do_action('fat_sb_booking_completed',$booking_id);

                            if ($setting['myPOS_success_page']) {
                                $url_redirect = get_permalink($setting['myPOS_success_page']);
                                $url_redirect = add_query_arg(array('bid' => $booking_id), $url_redirect);
                            }

                            //send mail
                            try{
                                $booking_db = FAT_DB_Bookings::instance();
                                $booking_db->send_booking_mail($booking_id);
                            }catch(Exception $err){}

                            wp_redirect($url_redirect);
                            exit();
                        }

                    } catch (\Mypos\IPC\IPC_Exception $e) {
                        error_log(serialize($e));
                    }
                }
            }
        }

        public function p24_update_status(){
            global $wpdb, $wp;
            $b_id = isset($_REQUEST['bid']) ? $_REQUEST['bid'] : 0;
            $p24_order_id = isset($_REQUEST['p24_order_id']) ? $_REQUEST['p24_order_id'] : 0;
            $setting_db = new FAT_DB_Setting();
            $setting = $setting_db->get_setting();
            $p24_mode = isset($setting['p24_mode']) ? $setting['p24_mode'] : 'sandbox';
            $p24_crc = isset($setting['p24_crc']) ? $setting['p24_crc'] : '';
            $success_url = isset($setting['przelewy24_success_page']) ? $setting['przelewy24_success_page'] : '';
            $error_url =  isset($setting['przelewy24_error_page']) ? $setting['przelewy24_error_page'] : '';
            $success_url = $success_url ? get_permalink($success_url) : home_url( $wp->request );
            $success_url = add_query_arg(array('bid' => $b_id), $success_url);
            $error_url = $error_url ? get_permalink($error_url) : home_url( $wp->request );
            error_log('action:'.$_REQUEST['action']);
            error_log('p24_order_id:'.$p24_order_id. ' bid:'.$b_id);

            /*$client_ip = $this->getIP();
            $server_ip = array('91.216.191.181','91.216.191.182','91.216.191.183','91.216.191.184','91.216.191.185');
            if(!in_array($client_ip,$server_ip)){
                if ( wp_redirect( $error_url ) ) {
                    exit;
                }
            }*/
            if($p24_order_id){
                $sql = "SELECT b_id, b_status_note FROM {$wpdb->prefix}fat_sb_booking WHERE b_id=%d";
                $sql = $wpdb->prepare($sql, $b_id);
                $booking = $wpdb->get_results($sql);

                if(is_countable($booking) && count($booking)>0){
                    if(isset($_REQUEST['action']) && ($_REQUEST['action']==="p24_status")){
                        $postArgs = json_decode($booking[0]->b_status_note);
                        error_log('booking info:'.serialize($booking[0]));
                        if($this->p24_verify($p24_mode, $p24_crc,$p24_order_id, $postArgs)){

                            $sql = "SELECT b_detail_id FROM {$wpdb->prefix}fat_sb_booking_multiple_days WHERE b_id=%d";
                            $sql = $wpdb->prepare($sql, $b_id);
                            $booking_md = $wpdb->get_results($sql);
                            $b_ids = array($b_id);
                            foreach($booking_md as $bmd){
                                $b_ids[] = $bmd->b_detail_id;
                            }
                            $b_ids = implode(',',$b_ids);

                            $sql = "UPDATE {$wpdb->prefix}fat_sb_booking SET b_gateway_id=%s, b_process_status=1, b_pay_now=1 WHERE b_id IN ({$b_ids})";
                            $sql = $wpdb->prepare($sql, $p24_order_id);
                            $result = $wpdb->query($sql);
                            if($result){
                                do_action('fat_sb_booking_completed',$b_id);
                                $booking_db = FAT_DB_Bookings::instance();
                                $booking_db->send_booking_mail($b_id);
                            }
                            $redirect = $result ? $success_url : $error_url;
                            if ( wp_redirect( $redirect ) ) {
                                exit;
                            }
                        }
                    }
                }

            }
            if ( wp_redirect( $success_url ) ) {
                exit;
            }
        }

        public function p24_update_price_package_status(){
            global $wpdb, $wp;
            $pko_id = isset($_REQUEST['pko_id']) ? $_REQUEST['pko_id'] : 0;
            $p24_order_id = isset($_REQUEST['p24_order_id']) ? $_REQUEST['p24_order_id'] : 0;
            $setting_db = new FAT_DB_Setting();
            $setting = $setting_db->get_setting();
            $p24_mode = isset($setting['p24_mode']) ? $setting['p24_mode'] : 'sandbox';
            $p24_crc = isset($setting['p24_crc']) ? $setting['p24_crc'] : '';
            $success_url = isset($setting['przelewy24_success_page']) ? $setting['przelewy24_success_page'] : '';
            $error_url =  isset($setting['przelewy24_error_page']) ? $setting['przelewy24_error_page'] : '';
            $success_url = $success_url ? get_permalink($success_url) : home_url( $wp->request );
            $success_url = add_query_arg(array('pko_id' => $pko_id), $success_url);
            $error_url = $error_url ? get_permalink($error_url) : home_url( $wp->request );

            if($p24_order_id){
                $sql = "SELECT pko_id, pko_description FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id=%d";
                $sql = $wpdb->prepare($sql, $pko_id);
                $package_order = $wpdb->get_results($sql);

                if(is_countable($package_order) && count($package_order)>0){
                    if(isset($_REQUEST['action']) && ($_REQUEST['action']==="p24_status")){
                        $postArgs = json_decode($package_order[0]->pko_description);
                        if($this->p24_verify($p24_mode, $p24_crc,$p24_order_id, $postArgs)){
                            $result = $wpdb->update($wpdb->prefix . 'fat_sb_price_package_order', array(
                                'pko_gateway_id' => $p24_order_id,
                                'pko_process_status' => 1,
                                'pko_gateway_status' => 1,
                                'pko_description' => '',
                            ),
                            array('pko_id' => $pko_id));
                            $redirect = $result ? $success_url : $error_url;
                            if ( wp_redirect( $redirect ) ) {
                                exit;
                            }
                        }
                    }
                }

            }
            if ( wp_redirect( $success_url ) ) {
                exit;
            }
        }

        public function p24_verify($p24_mode, $p24_crc, $p24_order_id, $postArgs){
            $p24_verify_url = $p24_mode=='sandbox' ? 'https://sandbox.przelewy24.pl/trnVerify' : 'https://secure.przelewy24.pl/trnVerify' ;
            $curl = curl_init($p24_verify_url);
            $p24_sign = $postArgs->p24_session_id.'|'.$p24_order_id.'|'.$postArgs->p24_amount.'|'.$postArgs->p24_currency.'|'.$p24_crc;
            $p24_sign = md5($p24_sign);
            $args = array(
                'p24_merchant_id' => $postArgs->p24_merchant_id,
                'p24_pos_id' => $postArgs->p24_pos_id,
                'p24_session_id' => $postArgs->p24_session_id,
                'p24_amount' => $postArgs->p24_amount,
                'p24_currency' => $postArgs->p24_currency,
                'p24_order_id' => $p24_order_id,
                'p24_sign' => $p24_sign
            );
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
            $response = curl_exec($curl);
            curl_close($curl);
            return $response=='error=0' ? true: false;
        }

        private function getIP(){
            foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
            {
                if (array_key_exists($key, $_SERVER) === true)
                {
                    foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
                    {
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
                        {
                            return $ip;
                        }
                    }
                }
            }
            return 0;
        }
    }
}