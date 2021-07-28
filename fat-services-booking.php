<?php
/*
Plugin Name: FAT Services Booking
Plugin URI:  http://plugins.roninwp.com/services-booking
Description: Automated booking and online scheduling for your services
Version:     4.1
Author:      Roninwp
Author URI:  https://codecanyon.net/user/roninwp/portfolio?ref=roninwp
Domain Path: /languages
Text Domain: fat-services-booking
*/

if (!defined('ABSPATH')) die('-1');

if (!class_exists('FAT_Services_Booking')) {
    class FAT_Services_Booking
    {
        private static $instance = NULL;
        private static $version = '4.1';
        protected $cron_job;

        function __construct()
        {
            $this->init();
        }

        function init()
        {
            do_action('fat_sb_booking_before_init');

            spl_autoload_extensions(".php");
            spl_autoload_register(array($this, 'autoload'));

            $this->define_constants();
            $this->hook();
            $this->register_shortcode();

            do_action('fat_sb_booking_after_init');

            if (!class_exists('WP_Async_Request')) {
                require_once FAT_SERVICES_DIR_PATH . 'libs/process/wp-async-request.php';
            }
            if (!class_exists('WP_Background_Process')) {
                require_once FAT_SERVICES_DIR_PATH . 'libs/process/wp-background-process.php';
            }
            require_once FAT_SERVICES_DIR_PATH . 'libs/process/bg-cron-job-process.php';
            $this->cron_job = new BG_Cron_Job_Process();
        }

        private function autoload($class_name)
        {
            $class_name = strtolower($class_name);
            $class_name = 'class-' . str_replace('_', '-', $class_name) . '.php';
            $class_path = '';
            if (strrpos($class_name, 'fat-db') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "inc/db/{$class_name}";
            }
            if (strrpos($class_name, 'fat-menu') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "inc/menu/{$class_name}";
            }
            if (strrpos($class_name, 'fat-ajax') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "inc/ajax/{$class_name}";
            }
            if (strrpos($class_name, 'fat-sb-utils') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "inc/utils/{$class_name}";
            }
            if (strrpos($class_name, 'fat-sb-validate') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "inc/utils/{$class_name}";
            }
            if (strrpos($class_name, 'fat-sb-shortcodes') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "shortcodes/{$class_name}";
            }
            if (strrpos($class_name, 'fat-sb-import-export') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "inc/utils/{$class_name}";
            }
            if (strrpos($class_name, 'fat-payment-package') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "inc/payment/{$class_name}";
            }
            if (strrpos($class_name, 'fat-payment') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "inc/payment/{$class_name}";
            }

            if (strrpos($class_name, 'ics') !== FALSE) {
                $class_path = FAT_SERVICES_DIR_PATH . "libs/{$class_name}";
            }
            if (is_readable($class_path)) {
                return require_once($class_path);
            }
            return false;
        }

        function define_constants()
        {
            defined('FAT_SERVICES_DIR_PATH') or define('FAT_SERVICES_DIR_PATH', plugin_dir_path(__FILE__));
            defined('FAT_SERVICES_PLUGIN_URL') or define('FAT_SERVICES_PLUGIN_URL', plugins_url('', __FILE__));
            defined('FAT_SERVICES_ASSET_URL') or define('FAT_SERVICES_ASSET_URL', plugins_url() . '/fat-services-booking/assets/');
            defined('FAT_SERVICES_PLUGIN_VERSION') or define('FAT_SERVICES_PLUGIN_VERSION', FAT_Services_Booking::$version);
        }

        private function hook()
        {
            register_activation_hook(__FILE__, array($this, 'plugin_activate'));
            $ajax_handlers = new FAT_Ajax_Handlers();
            if (is_admin()) {
                $menu_admin = new FAT_Menu_Admin();
                add_action('admin_enqueue_scripts', array($menu_admin, 'admin_enqueue_script'));
                add_action('admin_init', array($ajax_handlers, 'setup_ajax_handler'));
                add_action('admin_menu', array($menu_admin, 'init_menu'));
                add_action('admin_init', array($this, 'init_import'));
            }
            add_action('send_headers', array($this, 'payment_update_status'));
            add_action('parse_request', array($this, 'myPOS_update_status'), 15);
            add_action('parse_request', array($this, 'P24_update_status'), 20);

            add_action('init', array($ajax_handlers, 'setup_fe_ajax_handler'));
            add_action('init', array($this, 'load_text_domain'));

            add_filter('fat_save_data', array($this, 'validate_permission'), 10, 1);
            add_filter('fat_delete_data', array($this, 'validate_permission'), 10, 1);

            $validate = new FAT_SB_Validate();
            add_filter('fat_sb_booking_shortcode_validate', array($validate, 'shortcode_limit_user_validate'), 10, 1);
            add_filter('fat_sb_booking_validate', array($validate, 'booking_limit_user_validate'), 10, 2);

            $customer = new FAT_DB_Customers();
            add_action('wp_login', array($customer, 'user_validate_activate'), 10, 2);
            add_action('register_new_user', array($customer, 'add_new_user'), 5, 1);
            add_filter('wp_new_user_notification_email', array($customer, 'new_user_notification_email'), 10, 3);
            add_action('init', array($customer, 'validate_admin_area'));
            add_filter('body_class', array($customer, 'user_body_class'), 10, 1);
            add_filter('logout_redirect', array($customer, 'logout_redirect'), 10, 3);
            add_action('send_headers', array($this, 'active_user'));

            add_action('fat_sb_booking_completed', array($this, 'booking_completed'), 10, 1);
            add_action('wp_footer', array($this, 'custom_css'), 100);

            $setting = FAT_DB_Setting::instance();
            $setting = $setting->get_setting();

            if(isset($setting['time_to_change_status']) && $setting['time_to_change_status'] > 0){
                $this->register_cron_job();
            }
        }

        function register_cron_job()
        {
            add_filter('cron_schedules', array($this, 'add_cron_interval'));
            add_action('init', array($this, 'cron_action'));
            add_action('fat_sb_cron', array($this, 'job_cron'));
        }

        public function add_cron_interval($schedules)
        {
            $schedules['fat_sb_in_hourly'] = array(
                'interval' => 60*2,
                'display' => esc_html__('Automatic update booking status','fat-services-booking'),
            );
            return $schedules;
        }

        public function cron_action()
        {
            if (!wp_next_scheduled('fat_sb_cron')) {
                error_log('begin cron job');
                wp_schedule_event(time(), 'fat_sb_in_hourly', 'fat_sb_cron');
            }
        }

        public function job_cron()
        {
            $db = FAT_DB_Bookings::instance();
            $db->automatic_update_status();
        }

        public function active_user()
        {
            if (isset($_GET['action']) && $_GET['action'] === 'fat_sb_active' && isset($_GET['key'])) {
                $db = FAT_DB_Customers::instance();
                $db->active_user();
            }
        }

        public function custom_css()
        {
            $css_path = untrailingslashit(FAT_SERVICES_DIR_PATH) . '/assets/css/frontend/custom-css.php';
            if (is_readable($css_path)) {
                include $css_path;
            }
        }

        public function validate_permission($validate)
        {
            return $validate;
        }

        private function register_shortcode()
        {
            $shortcode = FAT_SB_Shortcodes::instance();
            add_shortcode('fat_sb_booking', array($shortcode, 'booking_shortcode'));
            add_shortcode('fat_sb_booking_button', array($shortcode, 'button_shortcode'));
            add_shortcode('fat_sb_booking_history', array($shortcode, 'history_shortcode'));
            add_shortcode('fat_sb_booking_calendar_button', array($shortcode, 'calendar_button_shortcode'));
            add_shortcode('fat_sb_price_package', array($shortcode, 'price_package_shortcode'));
            add_shortcode('fat_sb_price_package_order_history', array($shortcode, 'price_package_order_history_shortcode'));
            add_shortcode('fat_sb_booking_calendar', array($shortcode, 'booking_calendar_shortcode'));
            add_shortcode('fat_sb_login', array($shortcode, 'login_shortcode'));
        }

        public function init_import()
        {
            $import = FAT_SB_Import_Export::instance();
            $import->import();
        }

        public function payment_update_status()
        {
            if (isset($_GET['source']) && $_GET['source'] === 'fat_sb_booking' && isset($_GET['token'])) {
                $payment = new FAT_Payment();
                $payment->payment_update_status();
            }
            if (isset($_GET['source']) && $_GET['source'] === 'fat_sb_package_order' && isset($_GET['token'])) {
                $payment = new FAT_Payment_Package();
                $payment->payment_update_status();
            }
        }

        public function myPOS_update_status()
        {
            if (isset($_GET['source']) && $_GET['source'] === 'fat_sb_booking_myPOS') {
                $payment = new FAT_Payment();
                $payment->myPOS_update_status();
            }
            if (isset($_GET['source']) && $_GET['source'] === 'fat_sb_package_order_myPOS') {
                $payment = new FAT_Payment_Package();
                $payment->myPOS_update_status();
            }
        }

        public function P24_update_status()
        {
            if (isset($_REQUEST['source']) && $_REQUEST['source'] === 'fat_sb_booking_p24') {
                $payment = new FAT_Payment();
                $payment->p24_update_status();
            }

            if (isset($_REQUEST['source']) && $_REQUEST['source'] === 'fat_sb_package_order_p24') {
                $payment = new FAT_Payment();
                $payment->p24_update_price_package_status();
            }
        }

        public function booking_completed($booking_id)
        {
            $booking_db = FAT_DB_Bookings::instance();
            $booking_db->process_booking_completed($booking_id);
        }

        public function plugin_activate()
        {
            global $wpdb;
            $fat_table = FAT_DB_Table::instance();
            $fat_table->create_tables();
            $fat_table->update_tables();

            $db_services = FAT_DB_Services::instance();
            $db_services->syn_service_employee();
        }

        function load_text_domain()
        {
            $domain = dirname(plugin_basename(__FILE__));
            $locale = apply_filters('plugin_locale', get_locale(), $domain);
            load_textdomain('fat-services-booking', trailingslashit(WP_LANG_DIR) . 'plugins' . '/' . $domain . '-' . $locale . '.mo');
            load_plugin_textdomain('fat-services-booking', false, basename(dirname(__FILE__)) . '/languages/');
        }

        public static function getInstance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function require_file($path)
        {
            if (is_readable($path)) {
                require_once($path);
                return true;
            } else {
                return false;
            }
        }
    }

    FAT_Services_Booking::getInstance();
}