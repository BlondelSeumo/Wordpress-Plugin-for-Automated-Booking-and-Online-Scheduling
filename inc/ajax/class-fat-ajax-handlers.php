<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 4/23/2019
 * Time: 2:20 PM
 */

if (!class_exists('FAT_Ajax_Handlers')) {
    class FAT_Ajax_Handlers
    {
        public function setup_ajax_handler()
        {
            $ajax_callbacks = array(
                /* services */
                'fat_sb_get_service_category' => 'get_service_category',
                'fat_sb_get_services' => 'get_services',
                'fat_sb_get_services_hierarchy' => 'get_services_hierarchy',
                'fat_sb_get_services_by_name' => 'get_services_by_name',
                'fat_sb_save_service_category' => 'save_service_category',
                'fat_sb_delete_service_category' => 'delete_service_category',
                'fat_sb_save_service' => 'save_service',
                'fat_sb_delete_service' => 'delete_service',
                'fat_sb_get_service_by_id' => 'get_service_by_id',
                'fat_sb_get_service_work_day' => 'get_service_work_day',
                'fat_sb_save_service_work_day' => 'save_service_work_day',

                /* service extra */
                'fat_sb_get_services_extra' => 'get_services_extra',
                'fat_sb_get_service_extra_by_id' => 'get_service_extra_by_id',
                'fat_sb_save_service_extra' => 'save_service_extra',
                'fat_sb_delete_service_extra' => 'delete_service_extra',

                /* location*/
                'fat_sb_get_locations' => 'get_locations',
                'fat_sb_get_location_by_id' => 'get_location_by_id',
                'fat_sb_save_location' => 'save_location',
                'fat_sb_delete_location' => 'delete_location',

                /* employees*/
                'fat_sb_get_employees' => 'get_employees',
                'fat_sb_get_employees_dic' => 'get_employees_dic',
                'fat_sb_get_employee_by_id' => 'get_employee_by_id',
                'fat_sb_save_employee' => 'save_employee',
                'fat_sb_enable_employee' => 'enable_employee',
                'fat_sb_delete_employee' => 'delete_employee',

                /* customers*/
                'fat_sb_get_customers' => 'get_customers',
                'fat_sb_get_customers_dic' => 'get_customers_dic',
                'fat_sb_save_customer' => 'save_customer',
                'fat_sb_get_customer_by_id' => 'get_customer_by_id',
                'fat_sb_delete_customer' => 'delete_customer',

                /* coupons*/
                'fat_sb_get_coupons' => 'get_coupons',
                'fat_sb_save_coupon' => 'save_coupon',
                'fat_sb_get_coupon_by_id' => 'get_coupon_by_id',
                'fat_sb_delete_coupon' => 'delete_coupon',
                'fat_sb_get_coupon_discount' => 'get_coupon_discount',

                /* price package */
                'fat_sb_get_price_package' => 'get_price_package',
                'fat_sb_save_price_package' => 'save_price_package',
                'fat_sb_delete_price_package' => 'delete_price_package',
                'fat_sb_delete_price_package_order' => 'delete_price_package_order',
                'fat_sb_get_price_package_by_id' => 'get_price_package_by_id',
                'fat_sb_get_package_order' => 'get_price_package_order',
                'fat_sb_add_credit' => 'price_package_add_credit',

                /* bookings */
                'fat_sb_get_booking' => 'get_booking',
                'fat_sb_get_booking_export' => 'get_booking_export',
                'fat_sb_get_booking_by_id' => 'get_booking_by_id',
                'fat_sb_get_booking_slot' => 'get_booking_slot',
                'fat_sb_save_booking' => 'save_booking',
                'fat_sb_delete_booking' => 'delete_booking',
                'fat_sb_update_booking_status' => 'update_booking_process_status',
                'fat_sb_get_booking_calendar' => 'get_booking_calendar',
                'fat_sb_get_booking_calendar_by_id' => 'get_booking_calendar_by_id',
                'fat_sb_send_booking_mail' => 'send_booking_mail',
                'fat_sb_send_mail_change_status' => 'send_mail_change_booking_status',
                'fat_sb_get_insight' => 'get_insight',

                /* setting */
                'fat_sb_get_setting' => 'get_setting',
                'fat_sb_get_working_hour_setting' => 'get_working_hour_setting',
                'fat_sb_save_setting' => 'save_setting',
                'fat_sb_save_working_hour_setting' => 'save_working_hour_setting',
                'fat_sb_save_custom_css' => 'save_custom_css',
                'fat_sb_test_send_mail' => 'test_send_mail',
                'fat_sb_test_send_sms' => 'test_send_sms',
                'fat_sb_save_email_template' => 'save_email_template',
                'fat_sb_save_sms_template' => 'save_sms_template',
                'fat_sb_test_send_email_template' => 'test_send_email_template',
                'fat_sb_save_user_role_setting' => 'save_user_role_setting',
                'fat_sb_get_user_role_setting' => 'get_user_role_setting',

                /* export */
                'fat_sb_export' => 'export',
                'fat_sb_install_demo' => 'install_demo',

                /* form builder */
                'fat_sb_save_form_builder' => 'save_form_builder'

            );
            foreach ($ajax_callbacks as $ajax_func => $callback_func) {
                add_action('wp_ajax_' . $ajax_func, array($this, $callback_func));
            }

            //add on ajax
            $ajax_callbacks = apply_filters('fat_sb_setup_admin_ajax', array());
            foreach ($ajax_callbacks as $ajax_func => $callback_func) {
                add_action('wp_ajax_' . $ajax_func, $callback_func);
            }

        }

        public function setup_fe_ajax_handler()
        {
            $ajax_callbacks = array(
                'fat_sb_get_services_dictionary' => 'get_services_dictionary',
                'fat_sb_get_one_service_provider_dictionary' => 'get_one_service_provider_dictionary',
                'fat_sb_get_booking_slot_fe' => 'get_booking_slot_fe',
                'fat_sb_get_employees_available' => 'get_employees_available',
                'fat_sb_save_booking_fe' => 'save_booking_fe',
                'fat_sb_get_coupon_fe_discount' => 'get_coupon_fe_discount',
                'fat_sb_send_booking_fe_mail' => 'send_booking_fe_mail',
                'fat_sb_export_calendar' => 'export_calendar',
                'fat_sb_export_google_calendar' => 'export_google_calendar',
                'fat_sb_get_customer_code' => 'get_customer_code',
                'fat_sb_get_booking_history' => 'get_booking_history',
                'fat_sb_cancel_booking' => 'cancel_booking',
                'fat_sb_cancel_send_mail' => 'cancel_send_mail',
                'fat_sb_package_booking' => 'save_package_booking',
                'fat_sb_get_employee_time_slot' => 'get_employee_time_slot',
                'fat_sb_get_employee_time_slot_monthly' => 'get_employee_time_slot_monthly',
                'fat_sb_get_services_available_in_weekly' => 'get_services_available_in_weekly',
                'fat_sb_login' => 'login',
                'fat_sb_sign_up' => 'sign_up',
                'fat_sb_forgot_pass' => 'forgot_pass',
                'fat_sb_reset_pass' => 'reset_pass'
            );
            foreach ($ajax_callbacks as $ajax_func => $callback_func) {
                add_action('wp_ajax_' . $ajax_func, array($this, $callback_func));
                add_action('wp_ajax_nopriv_' . $ajax_func, array($this, $callback_func));
            }
        }

        /* Service */
        public function get_service_category()
        {
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->get_service_category();
            echo json_encode($result);
            wp_die();
        }

        public function get_services()
        {
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->get_services();
            echo json_encode($result);
            wp_die();
        }

        public function get_services_hierarchy()
        {
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->get_services_hierarchy();
            echo json_encode($result);
            wp_die();
        }

        public function get_services_by_name()
        {
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->get_services_by_name();
            echo json_encode($result);
            wp_die();
        }

        public function save_service_category()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->save_service_category();
            echo json_encode($result);
            wp_die();
        }

        public function delete_service_category()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->delete_service_category();
            echo json_encode($result);
            wp_die();
        }

        public function save_service()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->save_service();
            echo json_encode($result);
            wp_die();
        }

        public function delete_service()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->delete_service();
            echo json_encode($result);
            wp_die();
        }

        public function get_service_by_id()
        {
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->get_service_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function get_service_work_day(){
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->get_service_work_day();
            echo json_encode($result);
            wp_die();
        }

        public function save_service_work_day(){
            $service_db = FAT_DB_Services::instance();
            $result = $service_db->save_service_work_day();
            echo json_encode($result);
            wp_die();
        }

        /* Service Extra */
        public function get_services_extra()
        {
            $service_db = FAT_DB_Services_Extra::instance();
            $result = $service_db->get_services_extra();
            echo json_encode($result);
            wp_die();
        }

        public function get_service_extra_by_id()
        {
            $service_db = FAT_DB_Services_Extra::instance();
            $result = $service_db->get_service_extra_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function save_service_extra()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $service_db = FAT_DB_Services_Extra::instance();
            $result = $service_db->save_service_extra();
            echo json_encode($result);
            wp_die();
        }

        public function delete_service_extra()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $service_db = FAT_DB_Services_Extra::instance();
            $result = $service_db->delete_service_extra();
            echo json_encode($result);
            wp_die();
        }

        /* Locations */
        public function get_locations()
        {
            $location_db = FAT_DB_Locations::instance();
            $result = $location_db->get_locations();
            echo json_encode($result);
            wp_die();
        }

        public function get_location_by_id()
        {
            $location_db = FAT_DB_Locations::instance();
            $result = $location_db->get_location_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function save_location()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $location_db = FAT_DB_Locations::instance();
            $result = $location_db->save_location();
            echo json_encode($result);
            wp_die();
        }

        public function delete_location()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $location_db = FAT_DB_Locations::instance();
            $result = $location_db->delete_location();
            echo json_encode($result);
            wp_die();
        }

        /* Employees*/
        public function get_employees()
        {
            $employee_db = FAT_DB_Employees::instance();
            $result = $employee_db->get_employees();
            echo json_encode($result);
            wp_die();
        }

        public function get_employees_dic()
        {
            $employee_db = FAT_DB_Employees::instance();
            $result = $employee_db->get_employees_dic();
            echo json_encode($result);
            wp_die();
        }

        public function get_employee_by_id()
        {
            $employee_db = FAT_DB_Employees::instance();
            $result = $employee_db->get_employee_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function save_employee()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $employee_db = FAT_DB_Employees::instance();
            $result = $employee_db->save_employee();
            echo json_encode($result);
            wp_die();
        }

        public function enable_employee()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $employee_db = FAT_DB_Employees::instance();
            $result = $employee_db->enable_employee();
            echo json_encode($result);
            wp_die();
        }

        public function delete_employee()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }
            $employee_db = FAT_DB_Employees::instance();
            $result = $employee_db->delete_employee();
            echo json_encode($result);
            wp_die();
        }

        /* Customers*/
        public function get_customers(){
            $customer_db = FAT_DB_Customers::instance();
            $result = $customer_db->get_customers();
            echo json_encode($result);
            wp_die();
        }

        public function get_customers_dic(){
            $customer_db = FAT_DB_Customers::instance();
            $result = $customer_db->get_customers_dic();
            echo json_encode($result);
            wp_die();
        }

        public function save_customer(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $customer_db = FAT_DB_Customers::instance();
            $result = $customer_db->save_customer();
            echo json_encode($result);
            wp_die();
        }

        public function get_customer_by_id(){
            $customer_db = FAT_DB_Customers::instance();
            $result = $customer_db->get_customer_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function delete_customer(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $customer_db = FAT_DB_Customers::instance();
            $result = $customer_db->delete_customer();
            echo json_encode($result);
            wp_die();
        }

        /* Coupons */
        public function get_coupons(){
            $coupons_db = FAT_DB_Coupons::instance();
            $result = $coupons_db->get_coupons();
            echo json_encode($result);
            wp_die();
        }

        public function save_coupon(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $coupons_db = FAT_DB_Coupons::instance();
            $result = $coupons_db->save_coupon();
            echo json_encode($result);
            wp_die();
        }

        public function get_coupon_by_id(){
            $coupons_db = FAT_DB_Coupons::instance();
            $result = $coupons_db->get_coupon_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function delete_coupon(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $coupons_db = FAT_DB_Coupons::instance();
            $result = $coupons_db->delete_coupon();
            echo json_encode($result);
            wp_die();
        }

        public function get_coupon_discount(){
            $coupons_db = FAT_DB_Coupons::instance();
            $result = $coupons_db->get_coupon_discount();
            echo json_encode($result);
            wp_die();
        }

        /* Price package */
        public function get_price_package(){
            $package_db = FAT_DB_Price_Package::instance();
            $result = $package_db->get_package();
            echo json_encode($result);
            wp_die();
        }

        public function get_price_package_by_id(){
            $package_db = FAT_DB_Price_Package::instance();
            $result = $package_db->get_package_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function save_price_package(){
            $package_db = FAT_DB_Price_Package::instance();
            $result = $package_db->save_package();
            echo json_encode($result);
            wp_die();
        }

        public function price_package_add_credit(){
            $package_db = FAT_DB_Price_Package::instance();
            $result = $package_db->add_credit();
            echo json_encode($result);
            wp_die();
        }

        public function delete_price_package(){
            $package_db = FAT_DB_Price_Package::instance();
            $result = $package_db->delete_package();
            echo json_encode($result);
            wp_die();
        }

        public function delete_price_package_order(){
            $package_db = FAT_DB_Price_Package::instance();
            $result = $package_db->delete_package_order();
            echo json_encode($result);
            wp_die();
        }

        public function get_price_package_order(){
            $package_db = FAT_DB_Price_Package::instance();
            $result = $package_db->get_package_order();
            echo json_encode($result);
            wp_die();
        }

        /* Booking */
        public function get_booking(){
            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->get_booking();
            echo json_encode($result);
            wp_die();
        }

        public function get_booking_export(){
            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->get_booking_export();
            echo json_encode($result);
            wp_die();
        }

        public function get_booking_calendar(){
            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->get_booking_calendar();
            echo json_encode($result);
            wp_die();
        }

        public function get_booking_by_id(){
            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->get_booking_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function get_booking_slot(){
            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->get_booking_slot();
            echo json_encode($result);
            wp_die();
        }

        public function get_booking_calendar_by_id(){
            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->get_booking_calendar_by_id();
            echo json_encode($result);
            wp_die();
        }

        public function save_booking(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->save_booking();
            echo json_encode($result);
            wp_die();
        }

        public function delete_booking(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_delete_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->delete_booking();
            echo json_encode($result);
            wp_die();
        }

        public function update_booking_process_status(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->update_booking_process_status();
            echo json_encode($result);
            wp_die();
        }

        public function send_booking_mail(){
            $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : '';
            if($b_id){
                $booking_db = FAT_DB_Bookings::instance();
                $booking_db->send_booking_mail($b_id, 0);

                //send sms notifier
                $booking_db->send_booking_sms($b_id);
            }
            wp_die();
        }

        public function send_mail_change_booking_status(){
            $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : '';
            if($b_id){
                $booking_db = FAT_DB_Bookings::instance();
                $booking_db->send_booking_mail($b_id, 1);

                //send sms notifier
                $booking_db->send_booking_sms($b_id);
            }
            wp_die();
        }

        public function get_insight(){
            $booking_db = FAT_DB_Bookings::instance();
            $result = $booking_db->get_insight();
            echo json_encode($result);
            wp_die();
        }

        /* Setting */
        public function get_setting(){
            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->get_setting();
            echo json_encode($result);
            wp_die();
        }

        public function get_working_hour_setting(){
            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->get_working_hour_setting();
            echo json_encode($result);
            wp_die();
        }

        public function save_setting(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->save_setting();
            echo json_encode($result);
            wp_die();
        }

        public function save_working_hour_setting(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->save_working_hour_setting();
            echo json_encode($result);
            wp_die();
        }

        public function save_custom_css(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->save_custom_css();
            echo json_encode($result);
            wp_die();
        }

        public function save_email_template(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->save_email_template();
            echo json_encode($result);
            wp_die();
        }

        public function save_sms_template(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->save_sms_template();
            echo json_encode($result);
            wp_die();
        }

        public function test_send_email_template(){
            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->test_send_email_template();
            echo json_encode($result);
            wp_die();
        }

        public function test_send_mail(){
            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->test_send_mail();
            echo json_encode($result);
            wp_die();
        }

        public function test_send_sms(){
            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->test_send_sms();
            echo json_encode($result);
            wp_die();
        }

        public function export(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $export = FAT_SB_Import_Export::instance();
            $result = $export->export();
            echo json_encode($result);
            wp_die();
        }

        public function install_demo(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $import = FAT_SB_Import_Export::instance();
            $result = $import->install_demo();
            echo json_encode($result);
            wp_die();
        }

        public function save_form_builder(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            if(isset($_REQUEST['form'])){
                if($_REQUEST['form']!=='[]'){
                    $form_data =  $_REQUEST['form'];
                    update_option('fat_sb_booking_form',$form_data);
                }else{
                    delete_option('fat_sb_booking_form');
                }
                echo json_encode(array(
                    'result' => 1,
                    'message' => esc_html__('The booking form have been stored','fat-services-booking')
                ));
            }else{
                echo json_encode(array(
                    'result' => -1,
                    'message' => esc_html__('Please drag a field from the right to this area','fat-event')
                ));
            }
            wp_die();
        }

        public function save_user_role_setting(){
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                echo json_encode($is_valid);
                wp_die();
            }

            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->save_user_role_setting();
            echo json_encode($result);
            wp_die();
        }

        public function get_user_role_setting(){
            $setting_db = FAT_DB_Setting::instance();
            $result = $setting_db->get_user_role_setting();
            echo json_encode($result);
            wp_die();
        }

        /*
         * Frontend ajax
         */
        public function get_services_dictionary(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $service_db = FAT_DB_Services::instance();
                $result = $service_db->get_services_dictionary();
                echo json_encode($result);
            }
            wp_die();
        }

        public function get_one_service_provider_dictionary(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $service_db = FAT_DB_Services::instance();
                $result = $service_db->get_one_service_provider_dictionary();
                echo json_encode($result);
            }
            wp_die();
        }

        public function get_employees_available(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $employee_db = FAT_DB_Employees::instance();
                $result = $employee_db->get_employees_available();
                echo json_encode($result);
            }
            wp_die();
        }

        public function get_booking_slot_fe(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $booking_db = FAT_DB_Bookings::instance();
                $result = $booking_db->get_booking_slot();
                echo json_encode($result);
            }
            wp_die();
        }

        public function save_booking_fe(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $booking_db = FAT_DB_Bookings::instance();
                $result = $booking_db->save_booking_fe();
                echo json_encode($result);
            }
            wp_die();
        }

        public function get_coupon_fe_discount(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $coupons_db = FAT_DB_Coupons::instance();
                $result = $coupons_db->get_coupon_discount();
                echo json_encode($result);
            }
            wp_die();
        }

        public function send_booking_fe_mail(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : '';
            if($do_check && $b_id){
                $booking_db = FAT_DB_Bookings::instance();
                $booking_db->send_booking_mail($b_id, 1);

                //send sms notifier
                $booking_db->send_booking_sms($b_id);
            }
            wp_die();
        }

        public function export_calendar(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $booking_db = FAT_DB_Bookings::instance();
                echo sprintf('%s', $booking_db->export_calendar());
            }
            wp_die();
        }

        public function export_google_calendar(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $booking_db = FAT_DB_Bookings::instance();
                echo sprintf('%s',$booking_db->export_google_calendar());
            }
            wp_die();
        }

        public function get_customer_code(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $customer_db = FAT_DB_Customers::instance();
                $result = $customer_db->get_customer_code();
                echo json_encode($result);
            }
            wp_die();
        }

        public function get_booking_history(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $booking_db = FAT_DB_Bookings::instance();
                $result = $booking_db->get_booking_history();
                echo json_encode($result);
            }
            wp_die();
        }

        public function cancel_booking(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $booking_db = FAT_DB_Bookings::instance();
                $result = $booking_db->cancel_booking();
                echo json_encode($result);
            }
            wp_die();
        }

        public function cancel_send_mail(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : '';
                $booking_db = FAT_DB_Bookings::instance();
                $booking_db->send_booking_mail($b_id);
            }
            wp_die();
        }

        public function save_package_booking(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $package_db = FAT_DB_Price_Package::instance();
                $result = $package_db->save_package_booking_fe();
                echo json_encode($result);
            }
            wp_die();
        }

        public function get_employee_time_slot(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $employee_db = FAT_DB_Employees::instance();
                $result = $employee_db->get_employee_time_slot();
                echo json_encode($result);
            }
            wp_die();
        }

        public function get_employee_time_slot_monthly(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $employee_db = FAT_DB_Employees::instance();
                $result = $employee_db->get_employee_time_slot_monthly();
                echo json_encode($result);
            }
            wp_die();
        }

        public function get_services_available_in_weekly(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
               $db = FAT_DB_Services::instance();
               $start = isset($_REQUEST['start']) && $_REQUEST['start'] ? $_REQUEST['start'] : '';
               $end = isset($_REQUEST['end']) && $_REQUEST['end'] ? $_REQUEST['end'] : '';
               $service_id = isset($_REQUEST['service_id']) && $_REQUEST['service_id'] ? $_REQUEST['service_id'] : '';
               $result = $db->get_services_available_in_weekly($start, $end, $service_id);
                echo json_encode($result);
            }
            wp_die();
        }

        public function login(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $db = FAT_DB_Customers::instance();
                $result = $db->login();
                echo json_encode($result);
            }
            wp_die();
        }

        public function logout(){
            wp_logout();
            echo json_encode(array(
                'url' => home_url('/')
            ));
            wp_die();
        }

        public function forgot_pass(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $db = FAT_DB_Customers::instance();
                $result = $db->forgot_pass();
                echo json_encode($result);
            }
            wp_die();
        }

        public function reset_pass(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $db = FAT_DB_Customers::instance();
                $result = $db->reset_pass();
                echo json_encode($result);
            }
            wp_die();
        }

        public function sign_up(){
            $do_check = FAT_SB_Validate::check_ajax_refer();
            if($do_check){
                $db = FAT_DB_Customers::instance();
                $result = $db->sign_up();
                echo json_encode($result);
            }
            wp_die();
        }
    }
}