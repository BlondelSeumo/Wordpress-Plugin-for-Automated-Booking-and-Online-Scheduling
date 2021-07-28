<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/7/2019
 * Time: 9:10 AM
 */
if (!class_exists('FAT_DB_Customers')) {
    class FAT_DB_Customers
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_customers()
        {
            global $wpdb;
            $order = isset($_REQUEST['order']) && $_REQUEST['order'] ? $_REQUEST['order'] : 'ASC';
            $order_by = isset($_REQUEST['order_by']) && $_REQUEST['order_by'] ? $_REQUEST['order_by'] : 'c_first_name';
            $page = isset($_REQUEST['page']) && $_REQUEST['page'] ? $_REQUEST['page'] : 1;
            $sql = "SELECT c_id, c_first_name, c_last_name, c_email, c_phone_code, c_phone, c_description, c_dob
                                        FROM {$wpdb->prefix}fat_sb_customers WHERE 1=%d ";

            if (isset($_REQUEST['c_name']) && $_REQUEST['c_name']) {
                $sql .= " AND c_first_name LIKE '%{$_REQUEST['c_name']}%' OR c_last_name LIKE '%{$_REQUEST['c_name']}%'  OR c_email LIKE '%{$_REQUEST['c_name']}%'";
            }
            $sql .= " ORDER BY {$order_by} {$order}";
            $sql = $wpdb->prepare($sql, 1);
            $customers = $wpdb->get_results($sql);
            $total = count($customers);

            $fat_db_setting = FAT_DB_Setting::instance();
            $setting =  $fat_db_setting->get_setting();

            $item_per_page = isset($setting['item_per_page']) ? $setting['item_per_page'] : 10;
            $number_of_page = $total / $item_per_page + ($total % $item_per_page > 0 ? 1 : 0);
            $page = $page > $number_of_page ? $number_of_page : $page;
            $page = ($page - 1) * $item_per_page;
            $customers = array_slice($customers, $page, $item_per_page);

            $buy_amount = 0;
            $has_payment = 0;
            $now = new DateTime();
            $now = $now->format('YYYY-m-d');
            $credit = 0;
            for($i=0; $i < count($customers); $i++){
                $credit = FAT_DB_Price_Package::admin_get_price_amount_by_user($customers[$i]->c_email);
                $buy_amount = isset($credit['buy_amount']) ? $credit['buy_amount'] : 0;
                $has_payment = isset($credit['has_payment']) ? $credit['has_payment'] : 0;
                $customers[$i]->c_credit = $buy_amount - $has_payment;
                $customers[$i]->c_credit = $customers[$i]->c_credit > 0 ? $customers[$i]->c_credit : 0;
                $customers[$i]->c_phone_code_display = explode(',',$customers[$i]->c_phone_code)[0];
                $customers[$i]->c_dob = $customers[$i]->c_dob && $customers[$i]->c_dob!='0000-00-00' ? $customers[$i]->c_dob : $now;
            }
            return array(
                'total' => $total,
                'customers' => $customers
            );
        }

        public function get_customers_dic()
        {
            global $wpdb;
            $sql = "SELECT c_id, c_first_name, c_last_name, c_email, c_phone, c_description, c_dob
                                        FROM {$wpdb->prefix}fat_sb_customers";
            $customers = $wpdb->get_results($sql);
            return $customers;
        }

        public function save_customer()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                //$date_format = get_option('date_format');
                $data['c_dob'] = isset($data['c_dob']) && $data['c_dob'] ?  $data['c_dob'] : '';
                $c_id = isset($data['c_id']) && $data['c_id'] != '' ? $data['c_id'] : 0;
                global $wpdb;

                $sql = "SELECT c_id
                                        FROM {$wpdb->prefix}fat_sb_customers
                                        WHERE c_id <> %d AND c_email=%s";
                $sql = $wpdb->prepare($sql, $c_id, $data['c_email']);
                $is_exist_mail = $wpdb->get_results($sql);

                if (count($is_exist_mail) > 0) {
                    return array(
                        'result' => -2,
                        'message' => esc_html__('This email has been used for another customer. Please use another email', 'fat-services-booking')
                    );
                }

                if ($c_id > 0) {
                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_customers', $data, array('c_id' => $data['c_id']));
                } else {
                    $data['c_code'] = uniqid('fat_sb_');
                    $data['c_create_date'] = current_time( 'mysql', 0);
                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_customers', $data);
                    $result = $result > 0 ? $wpdb->insert_id : $result;
                }
                return array(
                    'result' => $result,
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data for field', 'fat-services-booking')
                );
            }
        }

        public function get_customer_by_id()
        {
            $c_id = isset($_REQUEST['c_id']) ? $_REQUEST['c_id'] : 0;
            global $wpdb;
            if ($c_id) {
                $sql = "SELECT c_id, c_first_name, c_last_name, c_gender, c_phone_code, c_phone, c_email, c_user_id, c_dob, c_description
                                        FROM {$wpdb->prefix}fat_sb_customers 
                                        WHERE c_id=%d";
                $sql = $wpdb->prepare($sql, $c_id);
                $customer = $wpdb->get_results($sql);
                if (count($customer) > 0) {
                    $customer = $customer[0];
                    $now = new DateTime();
                    $now = $now->format('YYYY-m-d');
                    $customer->c_dob = $customer->c_dob && $customer->c_dob!='0000-00-00' ? $customer->c_dob : $now;
                } else {
                    $customer = array(
                        'c_id' => 0,
                        'c_first_name' => '',
                        'c_last_name' => '',
                    );
                }
            } else {

                $customer = array(
                    'c_id' => 0,
                    'c_first_name' => '',
                    'c_last_name' => '',
                );
            }
            return $customer;
        }

        public function delete_customer()
        {
            $c_ids = isset($_REQUEST['c_ids']) ? $_REQUEST['c_ids'] : '';
            if ($c_ids) {
                global $wpdb;
                $number_c_detele = count($c_ids);
                $c_ids = implode(',', $c_ids);

                $sql = "SELECT b_customer_id
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        WHERE 1=%d AND b_customer_id IN ({$c_ids})";
                $sql = $wpdb->prepare($sql, 1);
                $c_ids_booking = $wpdb->get_results($sql);

                if (count($c_ids_booking) == $number_c_detele) {
                    return array(
                        'result' => -1,
                        'message_error' => esc_html__('You cannot delete customer(s) because exist order for this customer(s)', 'fat-services-booking')
                    );
                } else {
                    $c_ids = explode(',', $c_ids);
                    foreach ($c_ids_booking as $c_id) {
                        if(in_array($c_id, $c_ids)){
                            unset($c_ids[$c_id]);
                        }
                    }
                    $c_ids = implode(',', $c_ids);

                    $sql = "DELETE FROM {$wpdb->prefix}fat_sb_customers WHERE 1=%d AND c_id IN ({$c_ids}) ";
                    $sql = $wpdb->prepare($sql, 1);
                    $result = $wpdb->query($sql);
                    return array(
                        'result' => $result,
                        'ids_delete' => $c_ids,
                        'message_success' => $result > 0 ? $result . esc_html__(' customer(s) have been deleted', 'fat-services-booking') : '',
                        'message_error' => ($result < $number_c_detele && ($number_c_detele - $result) > 0) ? ($number_c_detele - $result) . esc_html__(' customer(s) can not delete because exist order for this customer(s)', 'fat-services-booking') : ''
                    );
                }
            } else {
                return array(
                    'result' => 1,
                );
            }
        }

        public function get_customer_code(){
            $c_email = isset($_REQUEST['c_email']) ? $_REQUEST['c_email'] : '';
            if($c_email){
                global $wpdb;
                $sql = "SELECT c_id, c_code, c_first_name, c_last_name FROM {$wpdb->prefix}fat_sb_customers WHERE c_email=%s";
                $sql = $wpdb->prepare($sql, $c_email);
                $customer = $wpdb->get_results($sql);
                if (count($customer) > 0 && (isset($customer[0]->c_code) || is_null($customer[0]->c_code) || $customer[0]->c_code=='') ) {
                    $c_code = $customer[0]->c_code;

                    // fix for old version
                    if($c_code == '' || is_null($c_code)){
                        $c_code = uniqid('fat_sb_');
                        $sql = "UPDATE {$wpdb->prefix}fat_sb_customers SET c_code = %s WHERE c_id = %d ";
                        $sql = $wpdb->prepare($sql, $c_code, $customer[0]->c_id);
                        $wpdb->query($sql);
                    }
                    try{
                        $setting_db = FAT_DB_Setting::instance();
                        $setting = $setting_db->get_setting();

                        $email_template = $setting_db->get_email_template();
                        $subject = esc_html__('Request customer code','fat-services-booking');
                        $message = wp_kses_post("<p>Dear {customer_first_name} {customer_last_name}  </p> <p>Please use this code : {customer_code} to view booking history  </p> <p>Thank you</p>");
                        for($i=0; $i < count($email_template); $i++){
                            if($email_template[$i]['template']=='get_customer_code' && $email_template[$i]['customer_code_subject']!='' && $email_template[$i]['customer_code_message']!=''){
                                $subject = $email_template[$i]['customer_code_subject'];
                                $message = $email_template[$i]['customer_code_message'];
                            }
                        }
                        $message = str_replace('{customer_code}', $c_code, $message);
                        $message = str_replace('{customer_first_name}',$customer[0]->c_first_name, $message);
                        $message = str_replace('{customer_last_name}',$customer[0]->c_last_name, $message);
                        FAT_SB_Utils::sendMail(array(
                            'mailer' => $setting['mailer'],
                            'smtp_host' => $setting['smtp_host'],
                            'smtp_port' => $setting['smtp_port'],
                            'smtp_username' => $setting['smtp_username'],
                            'smtp_password' => $setting['smtp_password'],
                            'encryption' => $setting['smpt_encryption'],
                            'from_name' => $setting['send_from_name'],
                            'from_name_label' => isset($setting['send_from_name_label']) ? $setting['send_from_name_label'] : $setting['send_from_name'],
                            'send_to' => $c_email,
                            'cc_email' => $setting['cc_to'],
                            'bcc_email' => $setting['bcc_to'],
                            'subject' => $subject,
                            'message' => $message
                        ));

                        return array(
                            'result' => 1,
                            'message' => sprintf(esc_html__('Customer code has been send to %s. Please check your mailbox', 'fat-services-booking'), $c_email)
                        );
                    }catch(Exception $e){
                        return array(
                            'result' => -1,
                            'message' => esc_html__('An error occurred while sending mail','fat-services-booking')
                        );
                    }

                }else{
                    return array(
                        'result' => -1,
                        'message' => esc_html__('This email does not exist with us. Please use the email ID that you used for booking','fat-services-booking')
                    );
                }
            }else{
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input email to get code','fat-services-booking')
                );
            }
        }

        public function login()
        {
            $u_email = isset($_REQUEST['u_email']) ? $_REQUEST['u_email'] : '';
            $u_pass = isset($_REQUEST['u_pass']) ? $_REQUEST['u_pass'] : '';
            $_POST['log'] = sanitize_email($u_email);
            $_POST['pwd'] = sanitize_text_field($u_pass);
            $_POST['rememberme'] = isset($_REQUEST['remember_me']) ? sanitize_text_field($_REQUEST['remember_me']) : 0;
            if ($u_email && $u_pass) {
                $secure_cookie = '';
                // If the user wants SSL but the session is not SSL, force a secure cookie.
                if (!empty($_POST['log']) && !force_ssl_admin()) {
                    $user_name = sanitize_user($_POST['log']);
                    $user = get_user_by('login', $user_name);
                    if (!$user && strpos($user_name, '@')) {
                        $user = get_user_by('email', $user_name);
                    }
                    if ($user) {
                        if (get_user_option('use_ssl', $user->ID)) {
                            $secure_cookie = true;
                            force_ssl_admin(true);
                        }
                    }
                }
                $user = wp_signon(array(), $secure_cookie);
                if (!is_wp_error($user)) {
                    global $wpdb;
                    $sql = "SELECT u_id, is_active FROM {$wpdb->prefix}fat_sb_user WHERE user_id=%d";
                    $sql = $wpdb->prepare($sql, $user->ID);
                    $fat_user = $wpdb->get_results($sql);

                    if (!is_countable($fat_user) || count($fat_user) == 0) {
                        $wpdb->insert("{$wpdb->prefix}fat_sb_user", array(
                            'user_id' => $user->ID,
                            'is_active' => 1,
                            'ui_create_date' => current_time('mysql', 0)
                        ));
                    } else {
                        if (!$fat_user[0]->is_active) {
                            wp_destroy_current_session();
                            wp_clear_auth_cookie();
                            wp_set_current_user(0);
                            return array(
                                'result' => -1,
                                'message' => esc_html__('Your account has not been activated','fat-services-booking')
                            );
                        }
                    }
                    //$url = wp_get_referer();
                    $url =  home_url('/');
                    return array(
                        'result' => 1,
                        'url' => $url
                    );
                } else {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('Your email or password is incorrect', 'fat-services-booking')
                    );
                }
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input email and password', 'fat-services-booking')
                );
            }
        }

        public function sign_up()
        {
            $u_email = isset($_REQUEST['u_email']) ? $_REQUEST['u_email'] : '';
            $u_name = isset($_REQUEST['u_name']) ? $_REQUEST['u_name'] : '';
            $u_surname = isset($_REQUEST['u_surname']) ? $_REQUEST['u_surname'] : '';
            $u_pass = isset($_REQUEST['u_pass']) ? $_REQUEST['u_pass'] : '';
            $u_email = sanitize_email($u_email);
            $u_pass = sanitize_text_field($u_pass);
            if ($u_email && $u_pass && strlen($u_pass) >= 6) {
                $user_id = register_new_user($u_email, $u_email);
                if (!is_wp_error($user_id)) {
                    //update password
                    wp_set_password($u_pass, $user_id);
                    update_user_meta($user_id, 'first_name', $u_name);
                    update_user_meta($user_id, 'last_name', $u_surname);
                    do_action('fat_sb_after_sign_up', $user_id);
                    return array(
                        'result' => 1,
                    );
                } else {
                    $error = $user_id->errors;
                    $error = isset($error['username_exists'][0]) ? $error['username_exists'][0] : $error;
                    $error = isset($error['email_exists'][0]) ? $error['email_exists'][0] : $error;
                    $error = isset($error['invalid_email'][0]) ? $error['invalid_email'][0] : $error;
                    $error = isset($error['invalid_username'][0]) ? $error['invalid_username'][0] : $error;
                    return array(
                        'result' => -1,
                        'message' => $error
                    );
                }
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input email and password', 'fat-services-booking')
                );
            }
        }

        public function forgot_pass()
        {
            $u_email = isset($_REQUEST['u_email']) ? $_REQUEST['u_email'] : '';
            $u_email = sanitize_email($u_email);
            $user_data = get_user_by( 'email', trim(wp_unslash($u_email)));
            if ( !empty( $user_data ) ) {
                $key  = get_password_reset_key( $user_data );
                if ( is_multisite() ) {
                    $site_name = get_network()->site_name;
                } else {
                    $site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
                }
                $title = sprintf( __( '[%s] Password Reset' ), $site_name );
                $message = __( 'Someone has requested a password reset for the following account:' ) . "\r\n\r\n";
                /* translators: %s: Site name. */
                $message .= sprintf( __( 'Site Name: %s' ), $site_name ) . "\r\n\r\n";
                /* translators: %s: User login. */
                $message .= sprintf( __( 'Username: %s' ), $u_email ) . "\r\n\r\n";
                $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ) . "\r\n\r\n";
                $message .= __( 'To reset your password, visit the following address:' ) . "\r\n\r\n";
                $message .= '<' . network_site_url( "login?action=rp&key=$key&login=" . rawurlencode( $u_email ), 'login' ) . ">\r\n";
                $result = wp_mail( $u_email, wp_specialchars_decode( $title ), $message );
                if($result){
                    $now = current_time('mysql',0);
                    $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
                    $now = $now->modify('+1 hour');
                    $now = $now->format('Y-m-d H:i:s');
                    global $wpdb;
                    $sql = "UPDATE {$wpdb->prefix}fat_sb_user
                            SET reset_key=%s, reset_expired=%s
                            WHERE user_id=%d";
                    $sql = $wpdb->prepare($sql, $key, $now, $user_data->ID);
                    $wpdb->query($sql);
                }
            }
            return array(
                'result' => 1,
                'message' => esc_html__('If your email address exists in our database, you will receive a password recovery link at your email address in a few minutes.','fat-services-booking')
            );
        }

        public function reset_pass()
        {
            global $wpdb;
            $u_email = isset($_REQUEST['login']) ? $_REQUEST['login'] : '';
            $key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
            $pass = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : '';
            $u_email = sanitize_email($u_email);
            $user_data = get_user_by( 'email', trim(wp_unslash($u_email)));
            if ( !empty( $user_data ) ) {
                $now = current_time('mysql',0);
                $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
                $sql = "SELECT reset_key, reset_expired
                        FROM {$wpdb->prefix}fat_sb_user
                        WHERE user_id=%d AND reset_key=%s";
                $sql = $wpdb->prepare($sql, $user_data->ID, $key);
                $user = $wpdb->get_results($sql);
                if(is_countable($user) && isset($user[0]->reset_expired) && $user[0]->reset_expired!=''){
                    $reset_expired = DateTime::createFromFormat('Y-m-d H:i:s', $user[0]->reset_expired);
                    if($now < $reset_expired){
                        $pass = sanitize_text_field($pass);
                        wp_set_password($pass, $user_data->ID);

                        $_POST['log'] = sanitize_email($u_email);
                        $_POST['pwd'] = sanitize_text_field($pass);
                        $secure_cookie = '';
                        // If the user wants SSL but the session is not SSL, force a secure cookie.
                        if (!force_ssl_admin()) {
                            if (get_user_option('use_ssl', $user_data->ID)) {
                                $secure_cookie = true;
                                force_ssl_admin(true);
                            }
                        }
                        wp_signon(array(), $secure_cookie);

                        return array(
                            'result' => 1,
                            'url' => home_url('/')
                        );
                    }else{
                        return array(
                            'result' => -1,
                            'message' => esc_html__('The key has expired','fat-services-booking')
                        );
                    }
                }
            }
            return array(
                'result' => -1,
                'message' => esc_html__('Data is invalid','fat-services-booking')
            );
        }

        public function user_validate_activate($user_login, $user)
        {
            global $wpdb;
            $sql = "SELECT is_active FROM {$wpdb->prefix}fat_sb_user WHERE user_id=%d";
            $sql = $wpdb->prepare($sql, $user->ID);
            $result = $wpdb->get_results($sql);
            if (is_countable($result) && count($result) > 0 && isset($result[0])) {
                if (isset($_REQUEST['s_field'])) {
                    if ($result[0]->is_active == 0) {
                        wp_logout();
                    } else {
                        $result = array(
                            'result' => 1,
                            'url' => home_url('/')
                        );
                    }
                }
            }
        }

        public function validate_admin_area(){
            if(is_admin() && !current_user_can('administrator') && ! wp_doing_ajax()){
                global $wpdb;
                $user_id = get_current_user_id();
                $sql = "SELECT is_active FROM {$wpdb->prefix}fat_sb_user WHERE user_id=%d";
                $sql = $wpdb->prepare($sql, $user_id);
                $result = $wpdb->get_results($sql);
                if (is_countable($result) && count($result) > 0 && isset($result[0])) {
                    wp_die(esc_html__('Sorry, You Are Not Allowed to Access This Page', 'fat-services-booking'));
                }
            }
        }

        public function user_body_class($classes){
            if(!current_user_can('administrator')){
                global $wpdb;
                $user_id = get_current_user_id();
                $sql = "SELECT is_active FROM {$wpdb->prefix}fat_sb_user WHERE user_id=%d";
                $sql = $wpdb->prepare($sql, $user_id);
                $result = $wpdb->get_results($sql);
                if (is_countable($result) && count($result) > 0 && isset($result[0])) {
                    $classes[] = 'fat-sb-user-bar';
                }
            }
            return $classes;
        }

        public function active_user()
        {
            $key = $_GET['key'];
            global $wpdb;
            $sql = "UPDATE {$wpdb->prefix}fat_sb_user SET is_active=1 WHERE active_key=%s";
            $sql = $wpdb->prepare($sql, $key);
            $result = $wpdb->query($sql);
            if ($result) {
                wp_safe_redirect(home_url('/') . 'login');
            } else {
                wp_safe_redirect(home_url('/'));
            }
            exit();
        }

        public function add_new_user($user_id)
        {
            global $wpdb;
            $wpdb->insert("{$wpdb->prefix}fat_sb_user", array(
                'user_id' => $user_id,
                'is_active' => 0,
                'ui_create_date' => current_time('mysql', 0)
            ));
        }

        function new_user_notification_email($wp_new_user_notification_email, $user, $blogname)
        {
            global $wpdb;
            $sql = "SELECT 1 FROM {$wpdb->prefix}fat_sb_user WHERE is_active=0 AND user_id=%d";
            $sql = $wpdb->prepare($sql, $user->ID);
            $result = $wpdb->query($sql);
            if ($result) {
                $wp_new_user_notification_email['subject'] = '[' . $blogname . ']' . esc_html__(' Thank for your register', 'fat-services-booking');
                $message = esc_html__('Thank for your register user. Bellow is your account information:', 'fat-services-booking') . "\r\n\r\n";
                $message .= sprintf(esc_html__('Email: %s', 'fat-services-booking'), $user->user_login) . "\r\n";

                $key = get_password_reset_key($user);
                if (is_wp_error($key)) {
                    return;
                }
                $sql = "UPDATE {$wpdb->prefix}fat_sb_user SET active_key=%s WHERE user_id=%d";
                $sql = $wpdb->prepare($sql, $key, $user->ID);
                $wpdb->query($sql);

                $link_active = network_site_url("?action=fat_sb_active&key=$key", 'login');
                $message .= sprintf(esc_html__('Please visit the following address to active your account: %s', 'fat-services-booking'), $link_active) . "\r\n\r\n";

                $message .= esc_html__('Thank & Regards,', 'fat-services-booking');
                $wp_new_user_notification_email['message'] = $message;
            }
            return $wp_new_user_notification_email;
        }

        function logout_redirect($redirect_to, $requested_redirect_to, $user){
            global $wpdb;
            $sql = "SELECT is_active FROM {$wpdb->prefix}fat_sb_user WHERE user_id=%d";
            $sql = $wpdb->prepare($sql, $user->ID);
            $result = $wpdb->get_results($sql);
            if (is_countable($result) && count($result) > 0 && isset($result[0])) {
                $redirect_to = home_url('/');
            }
            return $redirect_to;
        }
    }
}