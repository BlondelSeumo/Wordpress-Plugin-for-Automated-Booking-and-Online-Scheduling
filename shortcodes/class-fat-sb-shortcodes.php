<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 6/7/2019
 * Time: 5:37 PM
 */
if (!class_exists('FAT_SB_Shortcodes')) {
    class FAT_SB_Shortcodes{
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function booking_shortcode($atts){

            $validate = 1;
            $validate = apply_filters('fat_sb_booking_shortcode_validate', $validate);
            if(is_array($validate) && isset($validate['result']) && $validate['result']==-1){
                return isset($validate['message']) ? ('<div class="warning-limit-user-message" style="text-align: center; padding: 30px 0px;">' .$validate['message'].'</div>') : '';
            }

            $layout = isset($atts['layout']) ? $atts['layout'] : 'step';
            if($layout=='step' || $layout=='one-service-provider' || $layout=='date-first'){
               $this->step_enqueue_script($layout);
            }
            if($layout=='step-vertical'){
                $this->step_vertical_enqueue_script();
            }
            if($layout=='services' || $layout=='services-no-tab' || $layout=='one-services'){
                $this->services_enqueue_script();
            }

            ob_start();
            $column = isset($atts['column']) && $atts['column'] ? $atts['column'] : 3;
            $layout = $layout == 'one-service-provider' ? 'date-first' : $layout;
            $template = FAT_SERVICES_DIR_PATH.'/templates/shortcodes/'.$layout.'.php';
            if(is_readable($template)){
                include $template;
            }else{
                echo 'Template not found:'.$layout;
            }

            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        public function button_shortcode($atts){
            $validate = 1;
            $validate = apply_filters('fat_sb_booking_shortcode_validate', $validate);
            if(is_array($validate) && isset($validate['result']) && $validate['result']==-1){
                return isset($validate['message']) ? ('<div class="warning-limit-user-message" style="text-align: center; padding: 30px 0px;">' .$validate['message'].'</div>') : '';
            }

            $this->button_enqueue_script();
            ob_start();
            $template = FAT_SERVICES_DIR_PATH.'/templates/shortcodes/button.php';
            if(is_readable($template)){
                require $template;
            }
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        public function login_shortcode($atts){
            $this->login_enqueue_script();
            ob_start();
            $template = FAT_SERVICES_DIR_PATH.'/templates/shortcodes/login.php';
            if(is_readable($template)){
                require $template;
            }
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        public function history_shortcode($atts){
            $this->history_enqueue_script();
            ob_start();
            $template = FAT_SERVICES_DIR_PATH.'/templates/shortcodes/history.php';
            if(is_readable($template)){
                require $template;
            }
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        public function calendar_button_shortcode($atts){
            $this->calendar_button_enqueue_script();
            ob_start();
            $template = FAT_SERVICES_DIR_PATH.'/templates/shortcodes/calendar-button.php';
            if(is_readable($template)){
                require $template;
            }
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        public function price_package_shortcode($atts){
            $this->price_package_enqueue_script();
            ob_start();
            $template = FAT_SERVICES_DIR_PATH.'/templates/shortcodes/price-package.php';
            if(is_readable($template)){
                require $template;
            }
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        public function price_package_order_history_shortcode($atts){
            $this->price_package_order_history_enqueue_script();
            ob_start();

            $template = FAT_SERVICES_DIR_PATH.'/templates/shortcodes/price-package-order-history.php';
            if(is_readable($template)){
                require $template;
            }
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        public function booking_calendar_shortcode($atts){
            $this->calendar_enqueue_script();
            ob_start();
            $template = FAT_SERVICES_DIR_PATH.'/templates/shortcodes/calendar-services.php';
            if(is_readable($template)){
                include $template;
            }
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        private function step_enqueue_script($layout){
            wp_dequeue_script('jquery-ui-datepicker');
            wp_deregister_script( 'jquery-ui-datepicker' );

            wp_enqueue_style('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.css', array(), '2.4.1');
            wp_enqueue_style('semantic-extra', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic-extra.css', array(), '1.0.1');
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array('semantic','semantic-extra'), FAT_SERVICES_PLUGIN_VERSION);

            wp_enqueue_script('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.js', array('jquery'), '2.4.1', false);

            // air datepicker
            wp_enqueue_style('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/css/datepicker.min.css', array(), '2.2.3');
            wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/datepicker.min.js', array('jquery', 'moment'), '2.3.3', true);


            $locale = get_locale();
            $locale = explode('_',$locale)[0];
            $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            if($locale!='en' && file_exists($locale_path)){
                wp_enqueue_script('air-date-picker-lang-'.$locale, $locale_file, array('jquery', 'air-date-picker'), '2.3.3', true);
            }else{
                $locale = 'en';
                wp_enqueue_script('air-date-picker-lang', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.en.js', array('jquery', 'air-date-picker'), '2.3.3', true);
            }

            $fat_db_setting = FAT_DB_Setting::instance();
            $currency = $fat_db_setting->get_currency_setting();
            $setting =  $fat_db_setting->get_setting();
            $person_label = isset($setting['person_label']) && $setting['person_label'] ? $setting['person_label'] : esc_html__('person(s)','fat-services-booking');

            $now = current_time( 'mysql', 0);
            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'time_step' => isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15,
                'empty_time_slot' => esc_html__('The appointments are fully booked. Please check again later or browse other day!','fat-services-booking'),
                'bt_no_lable' => esc_html__('No','fat-services-booking'),
                'bt_yes_lable' => esc_html__('Yes','fat-services-booking'),
                'loading_label' => esc_html__('Loading','fat-services-booking'),
                'person_label' => $person_label,
                'coupon_validate' => esc_html__('Please select service and input coupon code','fat-services-booking'),
                'now' => $now,
                'date_format' => get_option('date_format'),
                'time_format' => isset($setting['time_format']) && $setting['time_format'] ? $setting['time_format'] : '24h',
                //for datetime ranger picker
                'day_of_week' => array(
                    esc_html__('Su','fat-services-booking'),
                    esc_html__('Mo','fat-services-booking'),
                    esc_html__('Tu','fat-services-booking'),
                    esc_html__('We','fat-services-booking'),
                    esc_html__('Th','fat-services-booking'),
                    esc_html__('Fr','fat-services-booking'),
                    esc_html__('Sa','fat-services-booking')
                ),
                'month_name' => array(
                    esc_html__('January','fat-services-booking'),
                    esc_html__('February','fat-services-booking'),
                    esc_html__('March','fat-services-booking'),
                    esc_html__('April','fat-services-booking'),
                    esc_html__('May','fat-services-booking'),
                    esc_html__('June','fat-services-booking'),
                    esc_html__('July','fat-services-booking'),
                    esc_html__('August','fat-services-booking'),
                    esc_html__('September','fat-services-booking'),
                    esc_html__('October','fat-services-booking'),
                    esc_html__('November','fat-services-booking'),
                    esc_html__('December','fat-services-booking')
                ),
                'january' => esc_html__('January','fat-services-booking'),
                'february' => esc_html__('February','fat-services-booking'),
                'march' => esc_html__('March','fat-services-booking'),
                'april' => esc_html__('April','fat-services-booking'),
                'may' => esc_html__('May','fat-services-booking'),
                'june' => esc_html__('June','fat-services-booking'),
                'july' => esc_html__('July','fat-services-booking'),
                'august' => esc_html__('August','fat-services-booking'),
                'september' => esc_html__('September','fat-services-booking'),
                'october' => esc_html__('October','fat-services-booking'),
                'november' => esc_html__('November','fat-services-booking'),
                'december' => esc_html__('December','fat-services-booking'),
                'durations' => FAT_SB_Utils::getDurations(0,'duration_step'),
                'currency' => $currency['currency'],
                'number_of_decimals' => isset($setting['number_of_decimals']) && $setting['number_of_decimals']!='' ? $setting['number_of_decimals'] : 2,
                'symbol' => $currency['symbol'],
                'symbol_prefix' => $currency['symbol_position'] === 'before' ? $currency['symbol'] : '',
                'symbol_suffix' => $currency['symbol_position'] === 'after' ?  $currency['symbol'] : '',
                'symbol_position' => $currency['symbol_position'],
                'multiple_days_notice' => esc_html__('Please select {d} slot together for better experience','fat-services-booking')
            );

            $fat_sb_data = apply_filters('fat_sb_script_data', $fat_sb_data);
            if(isset($setting['stripe_enable']) && $setting['stripe_enable']=='1'){
                wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', array('jquery'), false, false);
            }
            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);

            if(isset($layout) && ($layout=='date-first' ||  $layout=='one-service-provider')){
                wp_enqueue_script('fat-sb-booking-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/booking-date-first.js', array('jquery','wp-util','fat-sb-main-fe'), FAT_SERVICES_PLUGIN_VERSION, true);
            }
            if(!isset($layout) || $layout=='' || $layout=='step'){
                wp_enqueue_script('fat-sb-booking-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/booking-step.js', array('jquery','wp-util','fat-sb-main-fe'), FAT_SERVICES_PLUGIN_VERSION, true);
            }

            do_action('fat_sb_frontend_enqueue');
        }

        private function services_enqueue_script(){
            wp_dequeue_script('jquery-ui-datepicker');
            wp_deregister_script( 'jquery-ui-datepicker' );

            wp_enqueue_style('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.css', array(), '2.4.1');
            wp_enqueue_style('semantic-extra', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic-extra.css', array(), '1.0.1');
            wp_enqueue_script('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.js', array('jquery'), '2.4.1', false);

            wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', '4.7.0');
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);

            // air datepicker
            wp_enqueue_style('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/css/datepicker.min.css', array(), '2.2.3');
            wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/datepicker.min.js', array('jquery', 'moment'), '2.3.3', true);

            $locale = get_locale();
            $locale = explode('_',$locale)[0];
            $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            if($locale!='en' && file_exists($locale_path)){
                wp_enqueue_script('air-date-picker-lang-'.$locale, $locale_file, array('jquery', 'air-date-picker'), '2.3.3', true);
            }else{
                $locale = 'en';
                wp_enqueue_script('air-date-picker-lang', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.en.js', array('jquery', 'air-date-picker'), '2.3.3', true);
            }

            $fat_db_setting = FAT_DB_Setting::instance();
            $currency = $fat_db_setting->get_currency_setting();
            $setting =  $fat_db_setting->get_setting();
            $working_hour = $fat_db_setting->get_working_hour_setting();
            $now = current_time( 'mysql', 0);
            $person_label = isset($setting['person_label']) && $setting['person_label'] ? $setting['person_label'] : esc_html__('person(s)','fat-services-booking');

            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'time_step' => isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15,
                'bt_no_lable' => esc_html__('No','fat-services-booking'),
                'bt_yes_lable' => esc_html__('Yes','fat-services-booking'),
                'loading_label' => esc_html__('Loading','fat-services-booking'),
                'person_label' => $person_label,
                'coupon_validate' => esc_html__('Please select service and input coupon code','fat-services-booking'),
                'empty_time_slot' => esc_html__('The appointments are fully booked. Please check again later or browse other day!','fat-services-booking'),
                'empty_payment_method' => esc_html__('You need to choose a payment method','fat-services-booking'),
                'now' => $now,
                'date_format' => get_option('date_format'),
                'time_format' => isset($setting['time_format']) && $setting['time_format'] ? $setting['time_format'] : '24h',
                //for datetime ranger picker
                'day_of_week' => array(
                    esc_html__('Su','fat-services-booking'),
                    esc_html__('Mo','fat-services-booking'),
                    esc_html__('Tu','fat-services-booking'),
                    esc_html__('We','fat-services-booking'),
                    esc_html__('Th','fat-services-booking'),
                    esc_html__('Fr','fat-services-booking'),
                    esc_html__('Sa','fat-services-booking')
                ),
                'month_name' => array(
                    esc_html__('January','fat-services-booking'),
                    esc_html__('February','fat-services-booking'),
                    esc_html__('March','fat-services-booking'),
                    esc_html__('April','fat-services-booking'),
                    esc_html__('May','fat-services-booking'),
                    esc_html__('June','fat-services-booking'),
                    esc_html__('July','fat-services-booking'),
                    esc_html__('August','fat-services-booking'),
                    esc_html__('September','fat-services-booking'),
                    esc_html__('October','fat-services-booking'),
                    esc_html__('November','fat-services-booking'),
                    esc_html__('December','fat-services-booking')
                ),
                'january' => esc_html__('January','fat-services-booking'),
                'february' => esc_html__('February','fat-services-booking'),
                'march' => esc_html__('March','fat-services-booking'),
                'april' => esc_html__('April','fat-services-booking'),
                'may' => esc_html__('May','fat-services-booking'),
                'june' => esc_html__('June','fat-services-booking'),
                'july' => esc_html__('July','fat-services-booking'),
                'august' => esc_html__('August','fat-services-booking'),
                'september' => esc_html__('September','fat-services-booking'),
                'october' => esc_html__('October','fat-services-booking'),
                'november' => esc_html__('November','fat-services-booking'),
                'december' => esc_html__('December','fat-services-booking'),
                'durations' => FAT_SB_Utils::getDurations(0,'duration_step'),
                'currency' => $currency['currency'],
                'number_of_decimals' => isset($setting['number_of_decimals']) && $setting['number_of_decimals']!='' ? $setting['number_of_decimals'] : 2,
                'symbol' => $currency['symbol'],
                'symbol_prefix' => $currency['symbol_position'] === 'before' ? $currency['symbol'] : '',
                'symbol_suffix' => $currency['symbol_position'] === 'after' ?  $currency['symbol'] : '',
                'symbol_position' => $currency['symbol_position'],
                'enable_time_slot_deactive' => isset($setting['enable_time_slot_deactive']) ? $setting['enable_time_slot_deactive'] : 0,
                'bg_time_slot_not_active' => isset($setting['bg_time_slot_not_active']) ? $setting['bg_time_slot_not_active'] : '#bbb',
                'working_hour' => $working_hour,
                'multiple_days_notice' => esc_html__('Please select {d} slot together for better experience','fat-services-booking')
            );

            $fat_sb_data = apply_filters('fat_sb_script_data', $fat_sb_data);
            if(isset($setting['stripe_enable']) && $setting['stripe_enable']=='1'){
                wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', array('jquery'), false, false);
            }
            wp_enqueue_script('fat-sb-match-media', FAT_SERVICES_ASSET_URL . 'plugins/match-media/match-media.js', array('jquery'), false, true);
            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);
            wp_enqueue_script('fat-sb-booking-services-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/booking-services.js', array('jquery','wp-util','fat-sb-main-fe'), FAT_SERVICES_PLUGIN_VERSION, true);
            do_action('fat_sb_frontend_enqueue');
        }

        private function calendar_enqueue_script(){
            wp_dequeue_script('jquery-ui-datepicker');
            wp_deregister_script( 'jquery-ui-datepicker' );

            wp_enqueue_style('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.css', array(), '2.4.1');
            wp_enqueue_style('semantic-extra', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic-extra.css', array(), '1.0.1');
            wp_enqueue_script('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.js', array('jquery'), '2.4.1', false);

            wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', '4.7.0');
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);

            // air datepicker
            wp_enqueue_style('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/css/datepicker.min.css', array(), '2.2.3');
            wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/datepicker.min.js', array('jquery', 'moment'), '2.3.3', true);

            $locale = get_locale();
            $locale = explode('_',$locale)[0];
            $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            if($locale!='en' && file_exists($locale_path)){
                wp_enqueue_script('air-date-picker-lang-'.$locale, $locale_file, array('jquery', 'air-date-picker'), '2.3.3', true);
            }else{
                $locale = 'en';
                wp_enqueue_script('air-date-picker-lang', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.en.js', array('jquery', 'air-date-picker'), '2.3.3', true);
            }

            $fat_db_setting = FAT_DB_Setting::instance();
            $currency = $fat_db_setting->get_currency_setting();
            $setting =  $fat_db_setting->get_setting();
            $working_hour = $fat_db_setting->get_working_hour_setting();
            $now = current_time( 'mysql', 0);
            $person_label = isset($setting['person_label']) && $setting['person_label'] ? $setting['person_label'] : esc_html__('person(s)','fat-services-booking');

            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'time_step' => isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15,
                'bt_no_lable' => esc_html__('No','fat-services-booking'),
                'bt_yes_lable' => esc_html__('Yes','fat-services-booking'),
                'loading_label' => esc_html__('Loading','fat-services-booking'),
                'person_label' => $person_label,
                'coupon_validate' => esc_html__('Please select service and input coupon code','fat-services-booking'),
                'empty_time_slot' => esc_html__('The appointments are fully booked. Please check again later or browse other day!','fat-services-booking'),
                'empty_payment_method' => esc_html__('You need to choose a payment method','fat-services-booking'),
                'now' => $now,
                'date_format' => get_option('date_format'),
                'time_format' => isset($setting['time_format']) && $setting['time_format'] ? $setting['time_format'] : '24h',
                //for datetime ranger picker
                'day_of_week' => array(
                    esc_html__('Su','fat-services-booking'),
                    esc_html__('Mo','fat-services-booking'),
                    esc_html__('Tu','fat-services-booking'),
                    esc_html__('We','fat-services-booking'),
                    esc_html__('Th','fat-services-booking'),
                    esc_html__('Fr','fat-services-booking'),
                    esc_html__('Sa','fat-services-booking')
                ),
                'month_name' => array(
                    esc_html__('January','fat-services-booking'),
                    esc_html__('February','fat-services-booking'),
                    esc_html__('March','fat-services-booking'),
                    esc_html__('April','fat-services-booking'),
                    esc_html__('May','fat-services-booking'),
                    esc_html__('June','fat-services-booking'),
                    esc_html__('July','fat-services-booking'),
                    esc_html__('August','fat-services-booking'),
                    esc_html__('September','fat-services-booking'),
                    esc_html__('October','fat-services-booking'),
                    esc_html__('November','fat-services-booking'),
                    esc_html__('December','fat-services-booking')
                ),
                'january' => esc_html__('January','fat-services-booking'),
                'february' => esc_html__('February','fat-services-booking'),
                'march' => esc_html__('March','fat-services-booking'),
                'april' => esc_html__('April','fat-services-booking'),
                'may' => esc_html__('May','fat-services-booking'),
                'june' => esc_html__('June','fat-services-booking'),
                'july' => esc_html__('July','fat-services-booking'),
                'august' => esc_html__('August','fat-services-booking'),
                'september' => esc_html__('September','fat-services-booking'),
                'october' => esc_html__('October','fat-services-booking'),
                'november' => esc_html__('November','fat-services-booking'),
                'december' => esc_html__('December','fat-services-booking'),
                'mon' => esc_html__('MON','fat-services-booking'),
                'tue' => esc_html__('TUE','fat-services-booking'),
                'wed' => esc_html__('WED','fat-services-booking'),
                'thu' => esc_html__('THU','fat-services-booking'),
                'fri' => esc_html__('FRI','fat-services-booking'),
                'sat' => esc_html__('SAT','fat-services-booking'),
                'sun' => esc_html__('SUN','fat-services-booking'),
                'durations' => FAT_SB_Utils::getDurations(0,'duration_step'),
                'currency' => $currency['currency'],
                'number_of_decimals' => isset($setting['number_of_decimals']) && $setting['number_of_decimals']!='' ? $setting['number_of_decimals'] : 2,
                'symbol' => $currency['symbol'],
                'symbol_prefix' => $currency['symbol_position'] === 'before' ? $currency['symbol'] : '',
                'symbol_suffix' => $currency['symbol_position'] === 'after' ?  $currency['symbol'] : '',
                'symbol_position' => $currency['symbol_position'],
                'enable_time_slot_deactive' => isset($setting['enable_time_slot_deactive']) ? $setting['enable_time_slot_deactive'] : 0,
                'bg_time_slot_not_active' => isset($setting['bg_time_slot_not_active']) ? $setting['bg_time_slot_not_active'] : '#bbb',
                'working_hour' => $working_hour,
                'slots' => FAT_SB_Utils::getWorkHours(5)
            );

            $fat_sb_data = apply_filters('fat_sb_script_data', $fat_sb_data);
            if(isset($setting['stripe_enable']) && $setting['stripe_enable']=='1'){
                wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', array('jquery'), false, false);
            }
            wp_enqueue_script('fat-sb-match-media', FAT_SERVICES_ASSET_URL . 'plugins/match-media/match-media.js', array('jquery'), false, true);
            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);
            wp_enqueue_script('fat-sb-calendar-services', FAT_SERVICES_ASSET_URL . 'js/frontend/calendar-services.js', array('jquery','wp-util','fat-sb-main-fe'), FAT_SERVICES_PLUGIN_VERSION, true);
            do_action('fat_sb_frontend_enqueue');
        }

        private function button_enqueue_script(){
            wp_dequeue_script('jquery-ui-datepicker');
            /*wp_deregister_script( 'jquery-ui-datepicker' );*/

            wp_enqueue_style('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.css', array(), '2.4.1');
            wp_enqueue_style('semantic-extra', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic-extra.css', array(), '1.0.1');
            wp_enqueue_script('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.js', array('jquery'), '2.4.1', false);

            wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', '4.7.0');
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);

            // air datepicker
            wp_enqueue_style('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/css/datepicker.min.css', array(), '2.2.3');
            wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/datepicker.min.js', array('jquery', 'moment'), '2.3.3', true);

            $locale = get_locale();
            $locale = explode('_',$locale)[0];
            $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            if($locale!='en' && file_exists($locale_path)){
                wp_enqueue_script('air-date-picker-lang-'.$locale, $locale_file, array('jquery', 'air-date-picker'), '2.3.3', true);
            }else{
                $locale = 'en';
                wp_enqueue_script('air-date-picker-lang', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.en.js', array('jquery', 'air-date-picker'), '2.3.3', true);
            }

            $fat_db_setting = FAT_DB_Setting::instance();
            $currency = $fat_db_setting->get_currency_setting();
            $setting =  $fat_db_setting->get_setting();
            $working_hour = $fat_db_setting->get_working_hour_setting();
            $now = current_time( 'mysql', 0);
            $person_label = isset($setting['person_label']) && $setting['person_label'] ? $setting['person_label'] : esc_html__('person(s)','fat-services-booking');

            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'time_step' => isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15,
                'bt_no_lable' => esc_html__('No','fat-services-booking'),
                'bt_yes_lable' => esc_html__('Yes','fat-services-booking'),
                'loading_label' => esc_html__('Loading','fat-services-booking'),
                'person_label' => $person_label,
                'coupon_validate' => esc_html__('Please select service and input coupon code','fat-services-booking'),
                'empty_time_slot' => esc_html__('The appointments are fully booked. Please check again later or browse other day!','fat-services-booking'),
                'now' => $now,
                'date_format' => get_option('date_format'),
                'time_format' => isset($setting['time_format']) && $setting['time_format'] ? $setting['time_format'] : '24h',
                //for datetime ranger picker
                'day_of_week' => array(
                    esc_html__('Su','fat-services-booking'),
                    esc_html__('Mo','fat-services-booking'),
                    esc_html__('Tu','fat-services-booking'),
                    esc_html__('We','fat-services-booking'),
                    esc_html__('Th','fat-services-booking'),
                    esc_html__('Fr','fat-services-booking'),
                    esc_html__('Sa','fat-services-booking')
                ),
                'month_name' => array(
                    esc_html__('January','fat-services-booking'),
                    esc_html__('February','fat-services-booking'),
                    esc_html__('March','fat-services-booking'),
                    esc_html__('April','fat-services-booking'),
                    esc_html__('May','fat-services-booking'),
                    esc_html__('June','fat-services-booking'),
                    esc_html__('July','fat-services-booking'),
                    esc_html__('August','fat-services-booking'),
                    esc_html__('September','fat-services-booking'),
                    esc_html__('October','fat-services-booking'),
                    esc_html__('November','fat-services-booking'),
                    esc_html__('December','fat-services-booking')
                ),
                'january' => esc_html__('January','fat-services-booking'),
                'february' => esc_html__('February','fat-services-booking'),
                'march' => esc_html__('March','fat-services-booking'),
                'april' => esc_html__('April','fat-services-booking'),
                'may' => esc_html__('May','fat-services-booking'),
                'june' => esc_html__('June','fat-services-booking'),
                'july' => esc_html__('July','fat-services-booking'),
                'august' => esc_html__('August','fat-services-booking'),
                'september' => esc_html__('September','fat-services-booking'),
                'october' => esc_html__('October','fat-services-booking'),
                'november' => esc_html__('November','fat-services-booking'),
                'december' => esc_html__('December','fat-services-booking'),
                'durations' => FAT_SB_Utils::getDurations(0,'duration_step'),
                'currency' => $currency['currency'],
                'number_of_decimals' => isset($setting['number_of_decimals']) && $setting['number_of_decimals']!='' ? $setting['number_of_decimals'] : 2,
                'symbol' => $currency['symbol'],
                'symbol_prefix' => $currency['symbol_position'] === 'before' ? $currency['symbol'] : '',
                'symbol_suffix' => $currency['symbol_position'] === 'after' ?  $currency['symbol'] : '',
                'symbol_position' => $currency['symbol_position'],
                'enable_time_slot_deactive' => isset($setting['enable_time_slot_deactive']) ? $setting['enable_time_slot_deactive'] : 0,
                'bg_time_slot_not_active' => isset($setting['bg_time_slot_not_active']) ? $setting['bg_time_slot_not_active'] : '#bbb',
                'working_hour' => $working_hour,
                'multiple_days_notice' => esc_html__('Please select {d} slot together for better experience','fat-services-booking')
            );
            $fat_sb_data = apply_filters('fat_sb_script_data', $fat_sb_data);

            if(isset($setting['stripe_enable']) && $setting['stripe_enable']=='1'){
                wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', array('jquery'), false, false);
            }
            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);
            wp_enqueue_script('fat-sb-booking-button-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/booking-button.js', array('jquery','wp-util','fat-sb-main-fe'), FAT_SERVICES_PLUGIN_VERSION, true);

            do_action('fat_sb_frontend_enqueue');
        }

        private function step_vertical_enqueue_script(){
            wp_dequeue_script('jquery-ui-datepicker');
            wp_deregister_script( 'jquery-ui-datepicker' );

            wp_enqueue_style('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.css', array(), '2.4.1');
            wp_enqueue_style('semantic-extra', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic-extra.css', array(), '1.0.1');
            wp_enqueue_script('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.js', array('jquery'), '2.4.1', false);

            wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', '4.7.0');
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);

            // air datepicker
            wp_enqueue_style('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/css/datepicker.min.css', array(), '2.2.3');
            wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/datepicker.min.js', array('jquery', 'moment'), '2.3.3', true);

            $locale = get_locale();
            $locale = explode('_',$locale)[0];
            $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.'.$locale.'.js';
            if($locale!='en' && file_exists($locale_path)){
                wp_enqueue_script('air-date-picker-lang-'.$locale, $locale_file, array('jquery', 'air-date-picker'), '2.3.3', true);
            }else{
                $locale = 'en';
                wp_enqueue_script('air-date-picker-lang', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.en.js', array('jquery', 'air-date-picker'), '2.3.3', true);
            }

            $fat_db_setting = FAT_DB_Setting::instance();
            $currency = $fat_db_setting->get_currency_setting();
            $setting =  $fat_db_setting->get_setting();
            $working_hour = $fat_db_setting->get_working_hour_setting();
            $now = current_time( 'mysql', 0);
            $person_label = isset($setting['person_label']) && $setting['person_label'] ? $setting['person_label'] : esc_html__('person(s)','fat-services-booking');

            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'time_step' => isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15,
                'bt_no_lable' => esc_html__('No','fat-services-booking'),
                'bt_yes_lable' => esc_html__('Yes','fat-services-booking'),
                'loading_label' => esc_html__('Loading','fat-services-booking'),
                'person_label' => $person_label,
                'coupon_validate' => esc_html__('Please select service and input coupon code','fat-services-booking'),
                'empty_time_slot' => esc_html__('The appointments are fully booked. Please check again later or browse other day!','fat-services-booking'),
                'empty_service_extra' => esc_html__('Extras not found in this service.<br\/> You can select other service or click the \'Next step\' button','fat-services-booking'),
                'empty_employee' => esc_html__('Employee not found.<br\/> You can select other location or service','fat-services-booking'),
                'empty_payment_method' => esc_html__('You need to choose a payment method','fat-services-booking'),
                'now' => $now,
                'date_format' => get_option('date_format'),
                'time_format' => isset($setting['time_format']) && $setting['time_format'] ? $setting['time_format'] : '24h',
                //for datetime ranger picker
                'day_of_week' => array(
                    esc_html__('Su','fat-services-booking'),
                    esc_html__('Mo','fat-services-booking'),
                    esc_html__('Tu','fat-services-booking'),
                    esc_html__('We','fat-services-booking'),
                    esc_html__('Th','fat-services-booking'),
                    esc_html__('Fr','fat-services-booking'),
                    esc_html__('Sa','fat-services-booking')
                ),
                'month_name' => array(
                    esc_html__('January','fat-services-booking'),
                    esc_html__('February','fat-services-booking'),
                    esc_html__('March','fat-services-booking'),
                    esc_html__('April','fat-services-booking'),
                    esc_html__('May','fat-services-booking'),
                    esc_html__('June','fat-services-booking'),
                    esc_html__('July','fat-services-booking'),
                    esc_html__('August','fat-services-booking'),
                    esc_html__('September','fat-services-booking'),
                    esc_html__('October','fat-services-booking'),
                    esc_html__('November','fat-services-booking'),
                    esc_html__('December','fat-services-booking')
                ),
                'january' => esc_html__('January','fat-services-booking'),
                'february' => esc_html__('February','fat-services-booking'),
                'march' => esc_html__('March','fat-services-booking'),
                'april' => esc_html__('April','fat-services-booking'),
                'may' => esc_html__('May','fat-services-booking'),
                'june' => esc_html__('June','fat-services-booking'),
                'july' => esc_html__('July','fat-services-booking'),
                'august' => esc_html__('August','fat-services-booking'),
                'september' => esc_html__('September','fat-services-booking'),
                'october' => esc_html__('October','fat-services-booking'),
                'november' => esc_html__('November','fat-services-booking'),
                'december' => esc_html__('December','fat-services-booking'),
                'durations' => FAT_SB_Utils::getDurations(0,'duration_step'),
                'currency' => $currency['currency'],
                'number_of_decimals' => isset($setting['number_of_decimals']) && $setting['number_of_decimals']!='' ? $setting['number_of_decimals'] : 2,
                'symbol' => $currency['symbol'],
                'symbol_prefix' => $currency['symbol_position'] === 'before' ? $currency['symbol'] : '',
                'symbol_suffix' => $currency['symbol_position'] === 'after' ?  $currency['symbol'] : '',
                'symbol_position' => $currency['symbol_position'],
                'enable_time_slot_deactive' => isset($setting['enable_time_slot_deactive']) ? $setting['enable_time_slot_deactive'] : 0,
                'bg_time_slot_not_active' => isset($setting['bg_time_slot_not_active']) ? $setting['bg_time_slot_not_active'] : '#bbb',
                'working_hour' => $working_hour,
                'slots' => FAT_SB_Utils::getWorkHours(5),
                'select_date_message' => esc_html__('Please select a date to display free times','fat-services-booking'),
                'multiple_days_notice' => esc_html__('Please select {d} slot together for better experience','fat-services-booking')
            );

            $fat_sb_data = apply_filters('fat_sb_script_data', $fat_sb_data);
            if(isset($setting['stripe_enable']) && $setting['stripe_enable']=='1'){
                wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', array('jquery'), false, false);
            }
            wp_enqueue_script('fat-sb-match-media', FAT_SERVICES_ASSET_URL . 'plugins/match-media/match-media.js', array('jquery'), false, true);
            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);
            wp_enqueue_script('fat-sb-step-vertical-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/booking-step-vertical.js', array('jquery','wp-util','fat-sb-main-fe'), FAT_SERVICES_PLUGIN_VERSION, true);
            do_action('fat_sb_frontend_enqueue');
        }

        private function history_enqueue_script(){

            wp_enqueue_style('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.css', array(), '2.4.1');
            wp_enqueue_style('semantic-extra', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic-extra.css', array(), '1.0.1');
            wp_enqueue_script('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.js', array('jquery'), '2.4.1', false);

            wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', '4.7.0');
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);

            $fat_db_setting = FAT_DB_Setting::instance();
            $currency = $fat_db_setting->get_currency_setting();
            $setting =  $fat_db_setting->get_setting();
            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'not_found_message' => esc_html__('No records found','fat-services-booking'),
                'not_edit_message' => esc_html__('You cannot cancel bookings in the past or after the booking is approved','fat-services-booking'),
                'pending_label' => esc_html__('Pending','fat-services-booking'),
                'approved_label' => esc_html__('Approved','fat-services-booking'),
                'canceled_label' => esc_html__('Canceled','fat-services-booking'),
                'rejected_label' => esc_html__('Rejected','fat-services-booking'),
                'currency' => $currency['currency'],
                'symbol' => $currency['symbol'],
                'symbol_prefix' => $currency['symbol_position'] === 'before' ? $currency['symbol'] : '',
                'symbol_suffix' => $currency['symbol_position'] === 'after' ?  $currency['symbol'] : '',
                'symbol_position' => $currency['symbol_position'],
                'item_per_page' => isset($setting['item_per_page']) ? $setting['item_per_page'] : 10,
            );

            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);
            wp_enqueue_script('fat-sb-history', FAT_SERVICES_ASSET_URL . 'js/frontend/history.js', array('jquery','wp-util','fat-sb-main-fe'), false, true);

        }

        private function price_package_order_history_enqueue_script(){
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);
        }

        private function price_package_enqueue_script(){
            $fat_db_setting = FAT_DB_Setting::instance();
            $currency = $fat_db_setting->get_currency_setting();
            $setting =  $fat_db_setting->get_setting();

            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);
            if(isset($setting['stripe_enable']) && $setting['stripe_enable']=='1'){
                wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', array('jquery'), false, false);
            }

            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'bt_no_lable' => esc_html__('No','fat-services-booking'),
                'bt_yes_lable' => esc_html__('Yes','fat-services-booking'),
                'loading_label' => esc_html__('Loading','fat-services-booking'),
                'currency' => $currency['currency'],
                'number_of_decimals' => isset($setting['number_of_decimals']) && $setting['number_of_decimals']!='' ? $setting['number_of_decimals'] : 2,
                'symbol' => $currency['symbol'],
                'symbol_prefix' => $currency['symbol_position'] === 'before' ? $currency['symbol'] : '',
                'symbol_suffix' => $currency['symbol_position'] === 'after' ?  $currency['symbol'] : '',
                'symbol_position' => $currency['symbol_position'],
                'method_title' => esc_html__('Select payment method','fat-sb-booking'),
                'stripe_key' => $setting['stripe_publish_key'],
                'no_gateway_alert' => esc_html__('No payment gateway configured. Please contact the admin','fat-sb-booking')
            );
            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);
            wp_enqueue_script('fat-sb-price-package-order-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/price-package-order.js', array('jquery','wp-util','fat-sb-main-fe'), FAT_SERVICES_PLUGIN_VERSION, true);
        }

        private function calendar_button_enqueue_script(){
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);

            $fat_db_setting = FAT_DB_Setting::instance();
            $currency = $fat_db_setting->get_currency_setting();
            $setting =  $fat_db_setting->get_setting();
            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'not_found_message' => esc_html__('No records found','fat-services-booking'),
                'not_edit_message' => esc_html__('You cannot cancel bookings in the past or after the booking is approved','fat-services-booking'),
                'pending_label' => esc_html__('Pending','fat-services-booking'),
                'approved_label' => esc_html__('Approved','fat-services-booking'),
                'canceled_label' => esc_html__('Canceled','fat-services-booking'),
                'rejected_label' => esc_html__('Rejected','fat-services-booking'),
                'currency' => $currency['currency'],
                'symbol' => $currency['symbol'],
                'symbol_prefix' => $currency['symbol_position'] === 'before' ? $currency['symbol'] : '',
                'symbol_suffix' => $currency['symbol_position'] === 'after' ?  $currency['symbol'] : '',
                'symbol_position' => $currency['symbol_position']
            );

            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);
            wp_enqueue_script('fat-sb-calendar-button', FAT_SERVICES_ASSET_URL . 'js/frontend/calendar-button.js', array('jquery','wp-util','fat-sb-main-fe'), false, true);
        }

        private function login_enqueue_script(){
            wp_enqueue_style('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.css', array(), '2.4.1');
            wp_enqueue_style('semantic-extra', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic-extra.css', array(), '1.0.1');
            wp_enqueue_script('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.js', array('jquery'), '2.4.1', false);

            wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', '4.7.0');
            wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/frontend/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);


            $fat_sb_data = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_s_field' =>  wp_create_nonce("fat-sb-security-field" ),
                'error_message' => esc_html__('An error occurred during execution','fat-services-booking'),
                'pass_confirm_message' => esc_html__('The password confirmation does not match','fat-services-booking'),
                'pass_length_message' => esc_html__('Minimum 6-character password','fat-services-booking'),
            );
            $fat_sb_data = apply_filters('fat_sb_script_data', $fat_sb_data);

            wp_enqueue_script('fat-sb-main-fe', FAT_SERVICES_ASSET_URL . 'js/frontend/main.js', array('jquery'), false, true);
            wp_localize_script('fat-sb-main-fe', 'fat_sb_data', $fat_sb_data);
            wp_enqueue_script('fat-sb-login', FAT_SERVICES_ASSET_URL . 'js/frontend/login.js', array('jquery','wp-util','fat-sb-main-fe'), FAT_SERVICES_PLUGIN_VERSION, true);
            do_action('fat_sb_frontend_enqueue');
        }
    }
}