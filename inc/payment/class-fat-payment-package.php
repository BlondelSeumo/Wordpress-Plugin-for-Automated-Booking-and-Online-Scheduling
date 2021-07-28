<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 6/13/2019
 * Time: 4:52 PM
 */

if (!class_exists('FAT_Payment_Package')) {
    class FAT_Payment_Package{
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

        public function payment($pko_id, $customer, $package_name, $pk_id, $quantity, $price, $tax, $total_price, $currency, $description, $current_url){
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
            $cancel_link = add_query_arg(array('source' => 'fat_sb_package_order','action' => 'paypal_cancel'), $current_url);
            $return_link = add_query_arg(array('source' => 'fat_sb_package_order','action' => 'payment_return'), $current_url);
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
                "invoice_number" => $pko_id,
            );

            $payment['transactions'][0]['item_list']['items'][] = array(
                'quantity' => $quantity,
                'name' => $package_name,
                'price' =>  number_format($price,2),
                'currency' => $currency,
                'sku' => $pk_id,
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
            $wpdb->query("UPDATE {$wpdb->prefix}fat_sb_price_package_order SET pko_gateway_id = '{$json_response['id']}', pko_gateway_response='{$b_gateway_response}', pko_gateway_execute_url='{$payment_execute_url}'
                                      WHERE pko_id = {$pko_id}");

            return array(
                'result' => 1,
                'approval_url' => $payment_approval_url
            );
        }

        public function stripe_payment($pko_id, $total_price, $description){
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
                    if (isset($response->error)) {
                        $wpdb->query("UPDATE {$wpdb->prefix}fat_sb_price_package_order SET pko_gateway_status = -1, pko_gateway_response='{$response->error->message}'
                                      WHERE pko_id = {$pko_id}");
                        $result = array(
                            'code' => -1,
                            'message' => $response->error->message
                        );

                    } else {
                        $gateway_response = 'id:'.$response->id. ' balance_transaction:'.$response->balance_transaction;

                        $db_setting = FAT_DB_Setting::instance();
                        $setting = $db_setting->get_setting();

                        $wpdb->query("UPDATE {$wpdb->prefix}fat_sb_price_package_order SET pko_process_status=1, pko_gateway_status = 1, pko_gateway_response='{$gateway_response}'
                                      WHERE pko_id = {$pko_id}");
                        $result = array(
                            'code' => $pko_id,
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

            if(isset($_GET['source']) && $_GET['source'] ==='fat_sb_package_order' && isset($_GET['token']) ){
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
                $package_order = $wpdb->get_results('SELECT pko_id, pko_gateway_execute_url  FROM ' . $wpdb->prefix . 'fat_sb_price_package_order WHERE pko_gateway_id="'. $paypal_id .'"');

                if(!isset($package_order[0]->pko_id)){
                    if ( wp_redirect( $error_url ) ) {
                        exit;
                    }
                }
                $package_order = $package_order[0];

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
                        $wpdb->query("DELETE FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id = {$package_order->pko_id}");

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
                    $jsonResponse = $this->execute_paypal_request($package_order->pko_gateway_execute_url, $jsonEncode, $access_token['access_token']);
                    if(isset($package_order->pko_id)){
                        $pay_now =  $jsonResponse['state']=='approved' ? 1 : 0;
                        $gateway_response = serialize($jsonResponse);
                        if( $jsonResponse['state']=='approved' ||  $jsonResponse['state']=='created'){
                            $wpdb->query("UPDATE {$wpdb->prefix}fat_sb_price_package_order SET pko_process_status = 1, pko_gateway_status = '{$jsonResponse['state']}', pko_gateway_response='{$gateway_response}'
                                      WHERE pko_id = {$package_order->pko_id}");
                        }else{
                            $wpdb->query("DELETE FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id = {$package_order->pko_id}");
                        }
                        //send mail
                       /* try{
                            $booking_db = FAT_DB_Bookings::instance();
                            $booking_db->send_booking_mail($package_order->pko_id);
                        }catch(Exception $err){}*/
                    }
                }

                $success_url = $success_url ? get_permalink($success_url) : home_url( $wp->request );
                $success_url = add_query_arg(array('pko_id' => $package_order->pko_id), $success_url);
                if ( wp_redirect( $success_url ) ) {
                    exit;
                }
            }
        }

        public function myPOS_payment($first_name, $last_name, $email, $phone, $address, $currency, $pko_id, $quantity, $price, $package_name)
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
                $cart->add($package_name, $quantity,  $price); //name, quantity, price

                $sign = uniqid('fat_sb_');
                $url_cancel = home_url('/');
                $url_cancel = add_query_arg(array('source' => 'fat_sb_package_order_myPOS', 'action' => 'myPOS_cancel', 'pko_id' => $pko_id, 'sign' => $sign), $url_cancel);
                $url_ok = home_url('/');
                $url_ok = add_query_arg(array('source' => 'fat_sb_package_order_myPOS', 'action' => 'myPOS_ok', 'pko_id' => $pko_id, 'sign' => $sign), $url_ok);
                $url_notify = home_url('/');
                $url_notify = add_query_arg(array('source' => 'fat_sb_package_order_myPOS', 'action' => 'myPOS_notify', 'pko_id' => $pko_id, 'sign' => $sign), $url_notify);


                $sql = "UPDATE {$wpdb->prefix}fat_sb_price_package_order SET pko_myPOS_status = %s, pko_myPOS_sign=%s WHERE  pko_id = %d ";
                $sql = $wpdb->prepare($sql, '', $sign, $pko_id);
                $wpdb->query($sql);

                $purchase = new \Mypos\IPC\Purchase($cnf);
                $purchase->setUrlCancel($url_cancel); //User comes here after purchase cancelation
                $purchase->setUrlOk($url_ok); //User comes here after purchase success
                $purchase->setUrlNotify($url_notify); //IPC sends POST reuquest to this address with purchase status
                $purchase->setOrderID('fat_sb_price_package_order_' . $pko_id); //Some unique ID
                $purchase->setCurrency($currency);
                $purchase->setCustomer($customer);
                $purchase->setCart($cart);

                $purchase->setCardTokenRequest(\Mypos\IPC\Purchase::CARD_TOKEN_REQUEST_PAY_AND_STORE);
                $purchase->setPaymentParametersRequired(\Mypos\IPC\Purchase::PURCHASE_TYPE_FULL);
                $purchase->setPaymentMethod(\Mypos\IPC\Purchase::PAYMENT_METHOD_BOTH);

                try {
                    $form = $purchase->process();
                    return array(
                        'result' => $pko_id,
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
            if (isset($_GET['source']) && $_GET['source'] === 'fat_sb_package_order_myPOS' && isset($_REQUEST['action']) && isset($_REQUEST['sign'])) {
                $pko_id = $_REQUEST['pko_id'];
                $setting_db = new FAT_DB_Setting();
                $setting = $setting_db->get_setting();

                global $wp, $wpdb;
                $url_redirect = home_url($wp->request);

                $sql = "SELECT pko_id, pko_myPOS_sign, pko_myPOS_status  FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id=%d ";
                $sql = $wpdb->prepare($sql, $pko_id);
                $package_order = $wpdb->get_results($sql);

                if(!isset($package_order[0]->pko_id)){
                    if ($setting['myPOS_success_page'] && $setting['myPOS_error_page']) {
                        $url_redirect = get_permalink($setting['myPOS_success_page']);
                    }
                    if ( wp_redirect( $url_redirect ) ) {
                        exit;
                    }
                }
                $package_order = $package_order[0];

                $myPOS_sign = $package_order->b_myPOS_sign;
                $myPOS_status =  $package_order->b_myPOS_status;

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
                            $sql = "UPDATE {$wpdb->prefix}fat_sb_price_package_order SET pko_process_status=1, pko_myPOS_cardtoken = %s, pko_myPOS_ipc_trnref=%s WHERE pko_id = %d ";
                            $sql = $wpdb->prepare($sql, $data['cardtoken'], $data['ipc_trnref'], $pko_id);
                            $wpdb->query($sql);
                            status_header(200);
                            echo 'OK';
                            exit();

                        }
                        if ($data['ipcmethod'] === 'IPCPurchaseCancel' || $data['ipcmethod'] === 'IPCPurchaseRollback') {
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_price_package_order WHERE pko_id = %d";
                            $sql = $wpdb->prepare($sql, $pko_id);
                            $wpdb->query($sql);
                            if ($setting['myPOS_error_page']) {
                                $url_redirect = get_permalink($setting['myPOS_error_page']);
                            }
                            wp_redirect($url_redirect);
                            exit();
                        }
                        if ($data['ipcmethod'] === 'IPCPurchaseOK') {
                            $sql = "UPDATE {$wpdb->prefix}fat_sb_price_package_order SET b_process_status=1, pko_myPOS_status = %s WHERE pko_id = %d ";
                            $sql = $wpdb->prepare($sql,  'IPCPurchaseOK', $pko_id);
                            $wpdb->query($sql);

                            if ($setting['myPOS_success_page']) {
                                $url_redirect = get_permalink($setting['myPOS_success_page']);
                                $url_redirect = add_query_arg(array('bid' => $pko_id), $url_redirect);
                            }

                            //send mail
                            /*try{
                                $booking_db = FAT_DB_Bookings::instance();
                                $booking_db->send_booking_mail($pko_id);
                            }catch(Exception $err){}*/

                            wp_redirect($url_redirect);
                            exit();
                        }

                    } catch (\Mypos\IPC\IPC_Exception $e) {
                        error_log(serialize($e));
                    }
                }
            }
        }
    }
}