<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 11/19/2018
 * Time: 2:51 PM
 */
if (!class_exists('FAT_Menu_Admin')) {
    class FAT_Menu_Admin
    {
        public function init_menu()
        {
            $setting = FAT_DB_Setting::instance();
            $setting = $setting->get_setting();
            $price_package_enable = isset($setting['price_package_enable']) && $setting['price_package_enable'] == '1' ? 1 : 0;
            $role = 'edit_posts';
            add_menu_page(
                esc_html__('FAT Service Booking', 'fat-service-booking'),
                esc_html__('FAT Service Booking', 'fat-service-booking'),
                $role,
                'fat-service-booking',
                array($this, 'insight_page'),
                FAT_SERVICES_ASSET_URL . 'images/icon.png',
                9
            );
            add_submenu_page(
                'fat-service-booking',
                esc_html__('Insight', 'fat-services-booking'),
                esc_html__('Insight', 'fat-services-booking'),
                $role,
                'fat-service-booking'
            //array($this,'insight_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Location', 'fat-services-booking'),
                esc_html__('Location', 'fat-services-booking'),
                $role,
                'fat-sb-location',
                array($this, 'location_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Employees', 'fat-services-booking'),
                esc_html__('Employees', 'fat-services-booking'),
                $role,
                'fat-sb-employees',
                array($this, 'employees_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Services', 'fat-services-booking'),
                esc_html__('Services', 'fat-services-booking'),
                $role,
                'fat-sb-services',
                array($this, 'services_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Services Extra', 'fat-services-booking'),
                esc_html__('Services Extra', 'fat-services-booking'),
                $role,
                'fat-sb-extra',
                array($this, 'service_extra_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Customers', 'fat-services-booking'),
                esc_html__('Customers', 'fat-services-booking'),
                $role,
                'fat-sb-customers',
                array($this, 'customers_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Coupon', 'fat-services-booking'),
                esc_html__('Coupon', 'fat-services-booking'),
                $role,
                'fat-sb-coupon',
                array($this, 'coupon_page')
            );

            if ($price_package_enable) {
                add_submenu_page(
                    'fat-service-booking',
                    esc_html__('Price package', 'fat-services-booking'),
                    esc_html__('Price package', 'fat-services-booking'),
                    $role,
                    'fat-sb-price-package',
                    array($this, 'price_package_page')
                );
                add_submenu_page(
                    'fat-service-booking',
                    esc_html__('Price package order', 'fat-services-booking'),
                    esc_html__('Price package order', 'fat-services-booking'),
                    $role,
                    'fat-sb-price-package-order',
                    array($this, 'price_package_order_page')
                );
            }

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Booking', 'fat-services-booking'),
                esc_html__('Booking', 'fat-services-booking'),
                $role,
                'fat-sb-booking',
                array($this, 'booking_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Calendar', 'fat-services-booking'),
                esc_html__('Calendar', 'fat-services-booking'),
                $role,
                'fat-sb-calendar',
                array($this, 'calendar_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Email template', 'fat-services-booking'),
                esc_html__('Email template', 'fat-services-booking'),
                $role,
                'fat-sb-email-template',
                array($this, 'email_template_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('SMS template', 'fat-services-booking'),
                esc_html__('SMS template', 'fat-services-booking'),
                $role,
                'fat-sb-sms-template',
                array($this, 'sms_template_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Settings', 'fat-services-booking'),
                esc_html__('Settings', 'fat-services-booking'),
                $role,
                'fat-sb-setting',
                array($this, 'setting_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Booking form builder', 'fat-services-booking'),
                esc_html__('Booking form builder', 'fat-services-booking'),
                $role,
                'fat-sb-booking-builder',
                array($this, 'booking_builder_page')
            );

            //add on sub menu
            $submenu = apply_filters('fat_sb_add_sub_menu', array());
            foreach ($submenu as $sb) {
                if (isset($sb['page_title']) && $sb['menu_title'] && $sb['menu_slug'] && $sb['callback']) {
                    add_submenu_page(
                        'fat-service-booking',
                        $sb['page_title'],
                        $sb['menu_title'],
                        $role,
                        $sb['menu_slug'],
                        $sb['callback']
                    );
                }
            }

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Custom CSS', 'fat-services-booking'),
                esc_html__('Custom CSS', 'fat-services-booking'),
                $role,
                'fat-sb-custom-css',
                array($this, 'custom_css_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Import & Export', 'fat-services-booking'),
                esc_html__('Import & Export', 'fat-services-booking'),
                $role,
                'fat-sb-import-export',
                array($this, 'import_export_page')
            );

            add_submenu_page(
                'fat-service-booking',
                esc_html__('Install demo data', 'fat-services-booking'),
                esc_html__('Install demo data', 'fat-services-booking'),
                $role,
                'fat-sb-install-demo',
                array($this, 'install_demo_page')
            );

        }

        public function intro_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/intro.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/intro.php');
            }
        }

        public function insight_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/insight.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/insight.php');
            }
        }

        public function services_page()
        {
            $fat_sb_booking = FAT_Services_Booking::getInstance();
            $fat_sb_booking->require_file(FAT_SERVICES_DIR_PATH . '/templates/admin/services.php');
        }

        public function setting_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/settings.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/settings.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/settings/tmpl-setting.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/settings/tmpl-setting.php');
            }
        }

        public function service_extra_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/services-extra.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/services-extra.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/services/tmpl-services-extra.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/services/tmpl-services-extra.php');
            }
        }

        public function employees_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/employees.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/employees.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/employees/tmpl-employees.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/employees/tmpl-employees.php');
            }
        }

        public function customers_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/customers.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/customers.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/customers/tmpl-customers.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/customers/tmpl-customers.php');
            }
        }

        public function location_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/locations.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/locations.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/locations/tmpl-locations.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/locations/tmpl-locations.php');
            }
        }

        public function price_package_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/price-package.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/price-package.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/price-package/tmpl-price-package.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/price-package/tmpl-price-package.php');
            }
        }

        public function price_package_order_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/price-package-order.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/price-package-order.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/price-package/tmpl-price-package-order.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/price-package/tmpl-price-package-order.php');
            }
        }

        public function booking_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/booking.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/booking.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/booking/tmpl-booking.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/booking/tmpl-booking.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/customers/tmpl-customers.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/customers/tmpl-customers.php');
            }
        }

        public function calendar_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/calendar.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/calendar.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/booking/tmpl-booking.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/booking/tmpl-booking.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/customers/tmpl-customers.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/customers/tmpl-customers.php');
            }
        }

        public function coupon_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/coupons.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/coupons.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/coupon/tmpl-coupon.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/coupon/tmpl-coupon.php');
            }
        }

        public function custom_css_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/custom-css.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/custom-css.php');
            }
        }

        public function import_export_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/import-export.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/import-export.php');
            }
        }

        public function install_demo_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/install-demo.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/install-demo.php');
            }
        }

        public function email_template_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/email-template.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/email-template.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/settings/tmpl-email-template.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/settings/tmpl-email-template.php');
            }
        }

        public function sms_template_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/admin/sms-template.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/admin/sms-template.php');
            }
            if (is_readable(FAT_SERVICES_DIR_PATH . '/tmpl/settings/tmpl-sms-template.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/tmpl/settings/tmpl-sms-template.php');
            }
        }

        public function booking_builder_page()
        {
            if (is_readable(FAT_SERVICES_DIR_PATH . '/templates/form-builder/booking-builder.php')) {
                include_once(FAT_SERVICES_DIR_PATH . '/templates/form-builder/booking-builder.php');
            }
        }

        public function admin_enqueue_script()
        {
            $screen = get_current_screen();
            if (isset($screen->id)) {
                wp_enqueue_style('fat-sb', FAT_SERVICES_ASSET_URL . 'css/admin/style.css', array(), FAT_SERVICES_PLUGIN_VERSION);

                $fat_db_setting = FAT_DB_Setting::instance();
                $currency = $fat_db_setting->get_currency_setting();
                $setting = $fat_db_setting->get_setting();
                $now = current_time('mysql', 0);
                $phone_code_default = isset($setting['default_phone_code']) && $setting['default_phone_code'] ? $setting['default_phone_code'] : '+44,uk';

                $fat_sb_data = array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'error_message' => esc_html__('An error occurred during execution', 'fat-services-booking'),
                    'clipboard_message' => esc_html__('The shortcode has been copied to the clipboard', 'fat-services-booking'),
                    'time_step' => isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15,
                    'bt_no_lable' => esc_html__('No', 'fat-services-booking'),
                    'bt_yes_lable' => esc_html__('Yes', 'fat-services-booking'),
                    'loading_label' => esc_html__('Loading', 'fat-services-booking'),
                    'confirm_delete_title' => esc_html__('Confirm delete', 'fat-services-booking'),
                    'confirm_delete_message' => esc_html__('Are you sure you want to delete this item ?', 'fat-services-booking'),
                    'confirm_update_title' => esc_html__('Confirm update', 'fat-services-booking'),
                    'confirm_service_update_message' => esc_html__('Your changes related to specific settings for each employee. Do you want to update employee settings according to this setting ?', 'fat-services-booking'),
                    'not_found_message' => esc_html__('No records found', 'fat-services-booking'),
                    'coupon_validate' => esc_html__('Please select service and input coupon code', 'fat-services-booking'),
                    'min_value_message' => esc_html__('Value should be above ', 'fat-services-booking'),
                    'max_value_message' => esc_html__('Value should be bellow  ', 'fat-services-booking'),
                    'modal_title' => array(
                        'edit_category' => esc_html__('Edit Category', 'fat-services-booking'),
                        'edit_service' => esc_html__('Edit Service', 'fat-services-booking'),
                        'edit_service_extra' => esc_html__('Edit Service Extra', 'fat-services-booking'),
                        'edit_employee' => esc_html__('Edit Employee', 'fat-services-booking'),
                        'clone_employee' => esc_html__('Clone Employee', 'fat-services-booking'),
                        'edit_customer' => esc_html__('Edit Customer', 'fat-services-booking'),
                        'edit_location' => esc_html__('Edit Location', 'fat-services-booking'),
                        'edit_coupon' => esc_html__('Edit Coupon', 'fat-services-booking'),
                        'edit_booking' => esc_html__('Edit Booking', 'fat-services-booking'),
                        'edit_calendar' => esc_html__('Edit Calendar', 'fat-services-booking')
                    ),
                    'now' => $now,
                    'date_now' => DateTime::createFromFormat('Y-m-d H:i:s', $now)->format('Y-m-d'),
                    'date_format' => get_option('date_format'),
                    //for datetime ranger picker
                    'day_of_week' => array(
                        esc_html__('Su', 'fat-services-booking'),
                        esc_html__('Mo', 'fat-services-booking'),
                        esc_html__('Tu', 'fat-services-booking'),
                        esc_html__('We', 'fat-services-booking'),
                        esc_html__('Th', 'fat-services-booking'),
                        esc_html__('Fr', 'fat-services-booking'),
                        esc_html__('Sa', 'fat-services-booking')
                    ),
                    'month_name' => array(
                        esc_html__('January', 'fat-services-booking'),
                        esc_html__('February', 'fat-services-booking'),
                        esc_html__('March', 'fat-services-booking'),
                        esc_html__('April', 'fat-services-booking'),
                        esc_html__('May', 'fat-services-booking'),
                        esc_html__('June', 'fat-services-booking'),
                        esc_html__('July', 'fat-services-booking'),
                        esc_html__('August', 'fat-services-booking'),
                        esc_html__('September', 'fat-services-booking'),
                        esc_html__('October', 'fat-services-booking'),
                        esc_html__('November', 'fat-services-booking'),
                        esc_html__('December', 'fat-services-booking')
                    ),

                    'apply_title' => esc_html__('Apply', 'fat-services-booking'),
                    'cancel_title' => esc_html__('Cancel', 'fat-services-booking'),
                    'from_title' => esc_html__('From', 'fat-services-booking'),
                    'to_title' => esc_html__('To', 'fat-services-booking'),
                    'january' => esc_html__('January', 'fat-services-booking'),
                    'february' => esc_html__('February', 'fat-services-booking'),
                    'march' => esc_html__('March', 'fat-services-booking'),
                    'april' => esc_html__('April', 'fat-services-booking'),
                    'may' => esc_html__('May', 'fat-services-booking'),
                    'june' => esc_html__('June', 'fat-services-booking'),
                    'july' => esc_html__('July', 'fat-services-booking'),
                    'august' => esc_html__('August', 'fat-services-booking'),
                    'september' => esc_html__('September', 'fat-services-booking'),
                    'october' => esc_html__('October', 'fat-services-booking'),
                    'november' => esc_html__('November', 'fat-services-booking'),
                    'december' => esc_html__('December', 'fat-services-booking'),
                    'booking_color' => array(
                        '#fbbd08',
                        '#21ba45',
                        '#db2828',
                        '#b5b5b5'
                    ),
                    'durations' => FAT_SB_Utils::getDurations(0, 'duration_step'),
                    'item_per_page' => isset($setting['item_per_page']) ? $setting['item_per_page'] : 10,
                    'percentage_discount' => esc_html__('Percentage discount', 'fat-services-booking'),
                    'fixed_discount' => esc_html__('Fixed discount', 'fat-services-booking'),
                    'currency' => $currency['currency'],
                    'symbol' => $currency['symbol'],
                    'symbol_position' => $currency['symbol_position'],
                    'pending_label' => esc_html__('Pending', 'fat-services-booking'),
                    'approved_label' => esc_html__('Approved', 'fat-services-booking'),
                    'canceled_label' => esc_html__('Canceled', 'fat-services-booking'),
                    'rejected_label' => esc_html__('Rejected', 'fat-services-booking'),
                    'appointment_date_column' => esc_html__('Appointment Date', 'fat-services-booking'),
                    'customer_column' => esc_html__('Customer', 'fat-services-booking'),
                    'employee_column' => esc_html__('Employee', 'fat-services-booking'),
                    'services_column' => esc_html__('Services', 'fat-services-booking'),
                    'start_time_column' => esc_html__('Start time', 'fat-services-booking'),
                    'end_time_column' => esc_html__('End time', 'fat-services-booking'),
                    'duration_column' => esc_html__('Duration', 'fat-services-booking'),
                    'payment_column' => esc_html__('Payment', 'fat-services-booking'),
                    'status_column' => esc_html__('Status', 'fat-services-booking'),
                    'form_builder_column' => esc_html__('Custom fields', 'fat-services-booking'),
                    'notice_payment_default' => esc_html__('You need enable at least one payment method', 'fat-services-booking'),
                    'insight_new_customer' => esc_html__('New Customer', 'fat-services-booking'),
                    'insight_return_customer' => esc_html__('Return Customer', 'fat-services-booking'),
                    'insight_revenue' => esc_html__('Revenue', 'fat-services-booking'),
                    'insight_employee' => esc_html__('Employee', 'fat-services-booking'),
                    'insight_services' => esc_html__('Services', 'fat-services-booking'),
                    'yes_label' => esc_html__('Yes', 'fat-services-booking'),
                    'no_label' => esc_html__('No', 'fat-services-booking'),
                    'phone_code' => $phone_code_default
                );

                if (stripos($screen->id, 'fat-sb-insight') !== FALSE || $screen->id == 'toplevel_page_fat-service-booking') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_style('date-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('date-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_script('apex-charts', FAT_SERVICES_ASSET_URL . 'plugins/apex-charts/apexcharts.min.js', array(), false, true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);
                    wp_enqueue_script('fat-sb-insight', FAT_SERVICES_ASSET_URL . 'js/admin/insight.js', array('jquery', 'wp-util', 'date-ranger-picker', 'fat-sb-main'), FAT_SERVICES_PLUGIN_VERSION, true);

                }

                if (stripos($screen->id, 'fat-sb-services') !== FALSE) {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();
                    wp_enqueue_style('fat-sb-date-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('fat-sb-date-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_style('owl-carousel', FAT_SERVICES_ASSET_URL . 'plugins/owl-carousel/assets/owl.carousel.min.css', array(), '2.3.4');
                    wp_enqueue_style('owl-carousel-theme-default', FAT_SERVICES_ASSET_URL . 'plugins/owl-carousel/assets/owl.theme.default.min.css', array(), '2.3.4');

                    wp_enqueue_script('image-loaded', FAT_SERVICES_ASSET_URL . 'plugins/image-loaded/imagesloaded.pkgd.min.js', array('jquery'), '3.1.8', false);
                    wp_enqueue_script('owl-carousel', FAT_SERVICES_ASSET_URL . 'plugins/owl-carousel/owl.carousel.min.js', array('jquery', 'image-loaded'), '2.3.4', false);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-services-extra', FAT_SERVICES_ASSET_URL . 'js/admin/services-extra.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_enqueue_script('fat-sb-services', FAT_SERVICES_ASSET_URL . 'js/admin/services.js', array('jquery', 'wp-util', 'fat-sb-date-ranger-picker', 'fat-sb-services-extra'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-extra') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-extra') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();
                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-services-extra', FAT_SERVICES_ASSET_URL . 'js/admin/services-extra.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-employees') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-employees') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();
                    wp_enqueue_style('jquery.sumoselect', FAT_SERVICES_ASSET_URL . 'plugins/jquery-sumo/sumoselect.min.css', array(), '3.0.3');
                    wp_enqueue_script('jquery.sumoselect', FAT_SERVICES_ASSET_URL . 'plugins/jquery-sumo/jquery.sumoselect.min.js', array('jquery', 'wp-util'), '3.0.3', true);

                    wp_enqueue_style('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-employees', FAT_SERVICES_ASSET_URL . 'js/admin/employees.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-customers') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-customers') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();
                    wp_enqueue_style('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-customers', FAT_SERVICES_ASSET_URL . 'js/admin/customers.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-location') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-location') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();
                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    $map_api_url = sprintf('https://maps.googleapis.com/maps/api/js?key=%s&libraries=places', 'AIzaSyDnjdX5Zv3gPDvsYE2ZsbyQ-xl4TbSu8ts');
                    wp_enqueue_script('fat-sb-locations', FAT_SERVICES_ASSET_URL . 'js/admin/locations.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_enqueue_script('fat-sb-google-map-api', $map_api_url, array('fat-sb-locations'), false, true);
                }

                if (stripos($screen->id, 'fat-sb-price-package') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-price-package') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-price-package', FAT_SERVICES_ASSET_URL . 'js/admin/price-package.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-price-package-order') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-price-package-order') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_style('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/css/datepicker.min.css', array(), '2.2.3');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/datepicker.min.js', array('jquery', 'moment'), '2.3.3', true);
                    $locale = get_locale();
                    $locale = explode('_', $locale)[0];
                    $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
                    $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
                    if ($locale == 'pl') {
                        $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
                        $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
                    }
                    if (is_readable($locale_path)) {
                        wp_enqueue_script('air-date-picker-lang', $locale_file, array('jquery', 'air-date-picker'), '2.3.3', true);
                    } else {
                        wp_enqueue_script('air-date-picker-lang', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.en.js', array('jquery', 'air-date-picker'), '2.3.3', true);
                    }

                    wp_enqueue_style('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-price-package-order', FAT_SERVICES_ASSET_URL . 'js/admin/price-package-order.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-booking') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-booking') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_style('jquery.sumoselect', FAT_SERVICES_ASSET_URL . 'plugins/jquery-sumo/sumoselect.min.css', array(), '3.0.3');
                    wp_enqueue_script('jquery.sumoselect', FAT_SERVICES_ASSET_URL . 'plugins/jquery-sumo/jquery.sumoselect.min.js', array('jquery', 'wp-util'), '3.0.3', true);

                    wp_enqueue_style('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/css/datepicker.min.css', array(), '2.2.3');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/datepicker.min.js', array('jquery', 'moment'), '2.3.3', true);
                    $locale = get_locale();
                    $locale = explode('_', $locale)[0];

                    $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
                    $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
                    if ($locale == 'pl') {
                        $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
                        $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
                    }

                    if (file_exists($locale_path)) {
                        wp_enqueue_script('air-date-picker-lang', $locale_file, array('jquery', 'air-date-picker'), '2.3.3', true);
                    } else {
                        wp_enqueue_script('air-date-picker-lang', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.en.js', array('jquery', 'air-date-picker'), '2.3.3', true);
                    }
                    wp_enqueue_style('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-customers', FAT_SERVICES_ASSET_URL . 'js/admin/customers.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_enqueue_script('fat-sb-booking', FAT_SERVICES_ASSET_URL . 'js/admin/booking.js', array('jquery', 'wp-util', 'fat-sb-customers'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-calendar') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-calendar') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_style('jquery.sumoselect', FAT_SERVICES_ASSET_URL . 'plugins/jquery-sumo/sumoselect.min.css', array(), '3.0.3');
                    wp_enqueue_script('jquery.sumoselect', FAT_SERVICES_ASSET_URL . 'plugins/jquery-sumo/jquery.sumoselect.min.js', array('jquery', 'wp-util'), '3.0.3', true);

                    wp_enqueue_style('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/css/datepicker.min.css', array(), '2.2.3');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('air-date-picker', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/datepicker.min.js', array('jquery', 'moment'), '2.3.3', true);
                    $locale = get_locale();
                    $locale = explode('_', $locale)[0];
                    $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
                    $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
                    if ($locale == 'pl') {
                        $locale_file = FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
                        $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
                    }
                    if (file_exists($locale_path)) {
                        wp_enqueue_script('air-date-picker-lang', $locale_file, array('jquery', 'air-date-picker'), '2.3.3', true);
                    } else {
                        wp_enqueue_script('air-date-picker-lang', FAT_SERVICES_ASSET_URL . 'plugins/air-datepicker/js/i18n/datepicker.en.js', array('jquery', 'air-date-picker'), '2.3.3', true);
                    }

                    wp_enqueue_style('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_style('full-calendar', FAT_SERVICES_ASSET_URL . 'plugins/full-calendar/fullcalendar.min.css', array(), '3.10.0');
                    wp_enqueue_script('full-calendar', FAT_SERVICES_ASSET_URL . 'plugins/full-calendar/fullcalendar.min.js', array('jquery', 'moment'), '3.10.0', true);
                    wp_enqueue_script('full-calendar-locale', FAT_SERVICES_ASSET_URL . 'plugins/full-calendar/locale-all.js', array('jquery', 'full-calendar'), '3.10.0', true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-customers', FAT_SERVICES_ASSET_URL . 'js/admin/customers.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_enqueue_script('fat-sb-booking', FAT_SERVICES_ASSET_URL . 'js/admin/booking.js', array('jquery', 'wp-util', 'fat-sb-customers', 'air-date-picker'), FAT_SERVICES_PLUGIN_VERSION, true);

                    wp_enqueue_script('fat-sb-calendar', FAT_SERVICES_ASSET_URL . 'js/admin/calendar.js', array('jquery', 'wp-util', 'full-calendar', 'fat-sb-customers', 'fat-sb-booking'), false, true);
                }

                if (stripos($screen->id, 'fat-sb-coupon') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-coupon') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_style('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-coupon', FAT_SERVICES_ASSET_URL . 'js/admin/coupon.js?v=1', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-setting') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-setting') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_style('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.css', array(), '1.0.0');
                    wp_enqueue_script('moment', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/moment.min.js', array('jquery'), '2.24.0', true);
                    wp_enqueue_script('fat-sb-data-ranger-picker', FAT_SERVICES_ASSET_URL . 'plugins/date-ranger/daterangepicker.js', array('jquery', 'moment'), '1.0.0', true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-setting', FAT_SERVICES_ASSET_URL . 'js/admin/setting.js', array('jquery', 'wp-util', 'fat-sb-data-ranger-picker'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-custom-css') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-custom-css') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('ace-editor', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js', array('jquery'), '1.3.3', true);
                    wp_enqueue_script('fat-sb-custom-css', FAT_SERVICES_ASSET_URL . 'js/admin/custom-css.js', array('jquery', 'wp-util', 'ace-editor'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-import-export') !== FALSE || stripos($screen->id, 'fat-sb-install-demo') !== FALSE ||
                    $screen->id == 'fat-service-booking_page_fat-sb-import-export' || $screen->id == 'fat-service-booking_page_fat-sb-install-demo') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-sb-import-export', FAT_SERVICES_ASSET_URL . 'js/admin/import-export.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                }

                if (stripos($screen->id, 'fat-sb-email-template') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-email-template') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    $setting = FAT_DB_Setting::instance();
                    $fat_sb_email_template = $setting->get_email_template();

                    wp_enqueue_script('he', FAT_SERVICES_ASSET_URL . 'js/admin/he.js', array('jquery'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard','he'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('wp-tinymce');
                    wp_register_script('fat-sb-email-template', FAT_SERVICES_ASSET_URL . 'js/admin/email-template.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-email-template', 'fat_sb_email_data', $fat_sb_email_template);
                    wp_enqueue_script('fat-sb-email-template');
                }

                if (stripos($screen->id, 'fat-sb-sms-template') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-sms-template') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    $setting = FAT_DB_Setting::instance();
                    $fat_sb_sms_template = $setting->get_sms_template();

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_register_script('fat-sb-sms-template', FAT_SERVICES_ASSET_URL . 'js/admin/sms-template.js', array('jquery', 'wp-util'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-sms-template', 'fat_sb_sms_data', $fat_sb_sms_template);
                    wp_enqueue_script('fat-sb-sms-template');
                }

                if (stripos($screen->id, 'fat-sb-booking-builder') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-booking-builder') {
                    $this->enqueue_general_script();
                    $this->deregister_script_conflict();

                    wp_enqueue_style('form-builder', FAT_SERVICES_ASSET_URL . '/plugins/form-builder/form-builder.css', array(), '1.0.0');

                    wp_enqueue_script('form-builder', FAT_SERVICES_ASSET_URL . '/plugins/form-builder/form-builder.min.js', array('jquery'), '3.1.2', true);

                    wp_enqueue_script('fat-sb-main', FAT_SERVICES_ASSET_URL . 'js/admin/main.js', array('jquery', 'clipboard'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-sb-main', 'fat_sb_data', $fat_sb_data);

                    wp_enqueue_script('fat-form-builder', FAT_SERVICES_ASSET_URL . 'js/admin/form-builder.js', array('jquery'), FAT_SERVICES_PLUGIN_VERSION, true);
                    wp_localize_script('fat-form-builder', 'fat_sb_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
                }

                do_action('fat_sb_admin_enqueue', $screen->id);
            }
        }

        private function enqueue_general_script()
        {
            wp_enqueue_style('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.css', array(), '2.4.1');
            wp_enqueue_style('semantic-extra', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic-extra.css', array(), '1.0.1');

            wp_enqueue_script('semantic', FAT_SERVICES_ASSET_URL . 'plugins/semantic/semantic.min.js', array('jquery'), '2.4.1', true);

            wp_enqueue_script('clipboard', FAT_SERVICES_ASSET_URL . 'plugins/clipboard/clipboard.min.js', array('jquery'), '2.0.4', true);

            wp_enqueue_media();
        }

        public function deregister_script_conflict()
        {
            $setting = FAT_DB_Setting::instance();
            $setting = $setting->get_setting();


            wp_dequeue_script('jquery-ui-datepicker');
            wp_deregister_script('jquery-ui-datepicker');

            wp_dequeue_script('jquery.simplemodal');
            wp_deregister_script('jquery.simplemodal');
            wp_dequeue_script('bootstrap-modal');
            wp_deregister_script('bootstrap-modal');
            wp_dequeue_script('bootstrap');
            wp_deregister_script('bootstrap');
            wp_dequeue_script('jquery-ui-dialog');
            wp_deregister_script('jquery-ui-dialog');

            $screen = get_current_screen();
            if (stripos($screen->id, 'fat-sb-booking') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-booking' ||
                stripos($screen->id, 'fat-sb-calendar') !== FALSE || $screen->id == 'fat-service-booking_page_fat-sb-calendar') {
                if (!isset($setting['enable_datetime_picker']) || $setting['enable_datetime_picker'] != '1') {
                    wp_dequeue_script('jquery-ui-datepicker');
                    wp_deregister_script('jquery-ui-datepicker');
                }
            }

        }

    }
}
