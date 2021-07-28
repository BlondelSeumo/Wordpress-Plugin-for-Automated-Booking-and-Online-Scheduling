<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/6/2019
 * Time: 9:58 AM
 */
if (!class_exists('FAT_DB_Table')) {
    class FAT_DB_Table
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        function create_tables()
        {
            global $wpdb;
            $result = 0;
            $charset_collate = $wpdb->get_charset_collate();
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            //services table
            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_services(
                      s_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      s_image_id int(6) NOT NULL DEFAULT 0,
                      s_name text,
                      s_description text,
                      s_category_id int(6) NOT NULL DEFAULT 0,
                      s_price decimal(10,2),
                      s_tax decimal(10,4),
                      s_duration int(6),
                      s_break_time int(6),
                      s_minimum_person int(6),
                      s_maximum_person int(6),     
                      s_extra_ids text,
                      s_employee_ids text,
                      s_available int(1) NOT NULL DEFAULT 1,
                      s_allow_booking_online int(1) NOT NULL DEFAULT 1,
                      s_multiple_days int(1) NOT NULL DEFAULT 0,
                      s_min_multiple_slot int(2) NOT NULL DEFAULT 1,
                      s_max_multiple_slot int(2) NOT NULL DEFAULT 1,
                      s_create_date datetime NOT NULL,
                      s_order int(1),
                      PRIMARY KEY  (s_id)      
                    ) $charset_collate;";
            $result = dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_services_work_day(
                      swd_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      s_id int,
                      from_date varchar(200),
                      to_date varchar(200),
                      PRIMARY KEY  (swd_id) 
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_services_category(
                      sc_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      sc_image_id int(6) NOT NULL DEFAULT 0,
                      sc_name text,
                      sc_description text,
                      sc_total_service int(6) NOT NULL DEFAULT 0,
                      PRIMARY KEY  (sc_id)      
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_services_extra(
                      se_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      se_image_id int(6) NOT NULL DEFAULT 0,
                      se_name varchar(200),
                      se_price decimal(10,2),
                      se_tax decimal(10,2),
                      se_min_quantity int(6),
                      se_max_quantity int(6),
                      se_duration int(6),
                      se_description text,
                      se_create_date datetime NOT NULL,
                      PRIMARY KEY  (se_id)  
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_services_employee(
                      se_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      s_id int(6),
                      e_id int(6),
                      s_price decimal(10,2),
                      s_min_cap int(6),
                      s_max_cap int(6),
                      s_create_date datetime NOT NULL,
                      PRIMARY KEY  (se_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_employees(
                      e_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      e_first_name varchar(200),
                      e_last_name varchar(200),
                      e_avatar_id int(6) NOT NULL DEFAULT 0,
                      e_phone varchar(200),
                      e_email varchar(200),
                      e_location_ids text,
                      e_description text,
                      e_schedules text,
                      e_day_off text,
                      e_break_times text,
                      e_enable int(1) NOT NULL DEFAULT 1,
                      e_create_date datetime NOT NULL,
                      PRIMARY KEY  (e_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_employees_schedule(
                      es_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      e_id int(6),
                      es_day int(1),
                      es_work_hour_start varchar(4),
                      es_work_hour_end varchar(4),
                      s_id int(6) NOT NULL DEFAULT 0,
                      es_enable int(1) NOT NULL DEFAULT 1,
                      es_create_date datetime NOT NULL,
                      PRIMARY KEY  (es_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_employees_location(
                      el_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      e_id int(6),
                      loc_id int(1),
                      PRIMARY KEY  (el_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_employees_break_time(
                      eb_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      e_id int(6),
                      es_day int(1),
                      es_break_time_start varchar(4),
                      es_break_time_end varchar(4),
                      eb_create_date datetime NOT NULL,
                      PRIMARY KEY  (eb_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_employees_day_off(
                      dof_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      e_id int(6),
                      dof_name varchar(200),
                      dof_start date,
                      dof_end date,
                      dof_create_date datetime NOT NULL,
                      PRIMARY KEY  (dof_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_customers(
                      c_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      c_first_name varchar(200),
                      c_last_name varchar(200),
                      c_gender int(1) NOT NULL DEFAULT 0, /* 0: Male, 1: Female */ 
                      c_phone_code varchar(200),
                      c_phone varchar(200),
                      c_email varchar(200),
                      c_dob date,
                      c_user_id int(6), /* ID of wordpress user */
                      c_description text,
                      c_last_booking datetime DEFAULT NULL,
                      c_create_date datetime NOT NULL,
                      c_code varchar(200),
                      PRIMARY KEY  (c_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_locations(
                      loc_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      loc_image_id int(6) NOT NULL DEFAULT 0,
                      loc_name varchar(200),
                      loc_address varchar(200),
                      loc_link varchar(200),
                      loc_latitude_x varchar(200),
                      loc_latitude_y varchar(200),
                      loc_description text,
                      loc_create_date datetime NOT NULL,
                      PRIMARY KEY  (loc_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = " CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_coupons(
                      cp_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      cp_code varchar(200),
                      cp_description text,
                      cp_discount_type int(1),  
                      cp_amount decimal(10,2),
                      cp_start_date datetime,
                      cp_expire datetime,
                      cp_apply_to varchar(500),
                      cp_exclude varchar(500),
                      cp_times_use int(6),
                      cp_use_count int(6) NOT NULL DEFAULT 0,
                      cp_create_date datetime NOT NULL,
                      PRIMARY KEY  (cp_id)
                    ) $charset_collate;";

            // cp_discount_type: 1 -> percent, 2: fix discount
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_coupon_logs(
                      cp_log_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      cp_id int,
                      c_email varchar(200),
                      s_id int(6),
                      cp_log_create_date datetime NOT NULL,
                      PRIMARY KEY  (cp_log_id) 
                    ) $charset_collate;";
            dbDelta($tables);


            $tables = " CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_price_package(
                      pk_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      pk_name varchar(200),
                      pk_image_id int,
                      pk_price decimal(10,2),
                      pk_price_for_payment decimal(10,2),
                      pk_description text,
                      pk_create_date datetime NOT NULL,
                      pk_status int,
                      PRIMARY KEY  (pk_id)
                    ) $charset_collate;";
            // pk_status = 1 : active, -1: delete
            dbDelta($tables);

            $tables = " CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_user_payment_by_package(
                      upk_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      u_id int,
                      b_id int,
                      s_id int,
                      upk_payment_amount decimal(10,2),
                      pk_create_date datetime NOT NULL,
                      PRIMARY KEY  (upk_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_price_package_order(
                      pko_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      pko_user_id int(6),
                      pko_user_email varchar(100), 
                      pk_id int(6),
                      pk_price decimal(10,2) NOT NULL DEFAULT 0,
                      pk_price_for_payment decimal(10,2) NOT NULL DEFAULT 0,
                      pko_total_pay decimal(10,2) NOT NULL DEFAULT 0,
                      pko_gateway_type varchar(50),
                      pko_gateway_status varchar(100), 
                      pko_gateway_id varchar(100), 
                      pko_gateway_response varchar(500), 
                      pko_gateway_execute_url varchar(200), 
                      pko_description text,
                      pko_process_status int(1),
                      pko_create_date datetime NOT NULL,
                      pko_canceled_by_client int(1) DEFAULT 0,
                      pko_myPOS_sign varchar(200),
                      pko_myPOS_status varchar(200),
                      pko_myPOS_cardtoken varchar(200),
                      pko_myPOS_ipc_trnref varchar(200),
                      PRIMARY KEY  (pko_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_booking_multiple_days(
                      bmd_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      b_id int(6),
                      b_detail_id int(6),
                      b_date date,
                      b_datei18n varchar(100),
                      b_time int(6),
                      b_time_label varchar(100),
                      PRIMARY KEY  (bmd_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_booking(
                      b_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      b_customer_id int(6),
                      b_customer_number int(6), 
                      b_loc_id int(6),
                      b_employee_id int(6),
                      b_service_cat_id int(6),
                      b_service_id int(6),
                      b_service_duration int(6),
                      b_service_break_time int(6),
                      b_services_extra text,
                      b_total_extra decimal(10,2) NOT NULL DEFAULT 0,
                      b_total_extra_amount decimal(10,2) NOT NULL DEFAULT 0,
                      b_total_tax_extra decimal(10,2) NOT NULL DEFAULT 0,
                      b_date date,
                      b_time int(6),
                      b_price decimal(10,2),
                      b_service_tax int(6) NOT NULL DEFAULT 0,
                      b_service_tax_amount decimal(10,2) NOT NULL DEFAULT 0,
                      b_total_amount decimal(10,2), 
                      b_coupon_id int(6),
                      b_coupon_code text,
                      b_discount decimal(10,2) NOT NULL DEFAULT 0,
                      b_total_pay decimal(10,2) NOT NULL DEFAULT 0,
                      b_gateway_type varchar(50),
                      b_gateway_status varchar(100), 
                      b_gateway_id varchar(100), 
                      b_gateway_response varchar(500), 
                      b_gateway_execute_url varchar(200), 
                      b_description text,
                      b_pay_now int(1) NOT NULL DEFAULT 0,
                      b_process_status int(1),
                      b_create_date datetime NOT NULL,
                      b_send_notify int(1) NOT NULL DEFAULT 0,
                      b_form_builder text,
                      b_status_note text,
                      b_canceled_by_client int(1) DEFAULT 0,
                      b_myPOS_sign varchar(200),
                      b_myPOS_status varchar(200),
                      b_myPOS_cardtoken varchar(200),
                      b_myPOS_ipc_trnref varchar(200),
                      PRIMARY KEY  (b_id)
                    ) $charset_collate;";
            dbDelta($tables);
            //b_process_status : 0 -> Pending, 1 -> Approved, 2 -> Cancel, 3 -> Reject, -1 -> Pending for payment gateway
            //b_gateway_status : 0 -> Pending, 1 -> Payment, 2 -> Cancel, 3 -> Reject

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_user(
                      u_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      user_id int(6),
                      active_key varchar(200),
                      reset_key varchar(200),
                      reset_expired datetime,
                      is_active int(1),
                      ui_create_date datetime NOT NULL,
                      PRIMARY KEY  (u_id)      
                    ) $charset_collate;";
            $result = dbDelta($tables);
        }

        function update_tables(){
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_locations' AND column_name = 'loc_image_id'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_locations ADD loc_image_id int(6) DEFAULT 0";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_locations' AND column_name = 'loc_link'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_locations ADD loc_link varchar(200)";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_services' AND column_name = 's_link'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services ADD s_link varchar(200)";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_services' AND column_name = 's_multiple_days'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services ADD s_multiple_days int(1) NOT NULL DEFAULT 0";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_services' AND column_name = 's_order'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services ADD s_order int(1) NOT NULL DEFAULT 1";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_services' AND column_name = 's_min_multiple_slot'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services ADD s_min_multiple_slot int(2) NOT NULL DEFAULT 1";
                $wpdb->query($sql);
            }
            
            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_services' AND column_name = 's_max_multiple_slot'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services ADD s_max_multiple_slot int(2) NOT NULL DEFAULT 1";
                $wpdb->query($sql);
            }

            $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services MODIFY s_tax decimal(10,4)";
            $wpdb->query($sql);

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_services_extra' AND column_name = 'se_image_id'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services_extra ADD se_image_id int(6) DEFAULT 0";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_services_extra' AND column_name = 'se_multiple_book'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services_extra ADD se_multiple_book int(1) DEFAULT 1";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_services_extra' AND column_name = 'se_price_on_total'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_services_extra ADD se_price_on_total int(1) DEFAULT 0";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_booking' AND column_name = 'b_form_builder'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_booking ADD b_form_builder TEXT";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_booking' AND column_name = 'b_status_note'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_booking ADD b_status_note TEXT";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_booking' AND column_name = 'b_canceled_by_client'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_booking ADD b_canceled_by_client int(1) DEFAULT 0";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_booking' AND column_name = 'b_myPOS_sign'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_booking ADD b_myPOS_sign varchar(200)";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_booking' AND column_name = 'b_myPOS_status'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_booking ADD b_myPOS_status varchar(200)";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_booking' AND column_name = 'b_myPOS_cardtoken'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_booking ADD b_myPOS_cardtoken varchar(200)";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_booking' AND column_name = 'b_myPOS_ipc_trnref'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_booking ADD b_myPOS_ipc_trnref varchar(200)";
                $wpdb->query($sql);
            }


            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_customers' AND column_name = 'c_code'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_customers ADD c_code varchar(200)";
                $wpdb->query($sql);
            }

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_customers' AND column_name = 'c_phone_code'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_customers ADD c_phone_code varchar(200)";
                $wpdb->query($sql);
            }

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_services_work_day(
                      swd_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      s_id int,
                      from_date varchar(200),
                      to_date varchar(200),
                      PRIMARY KEY  (swd_id) 
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = " CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_price_package(
                      pk_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      pk_image_id int,
                      pk_name varchar(200),
                      pk_price decimal(10,2),
                      pk_price_for_payment decimal(10,2),
                      pk_description text,
                      pk_create_date datetime NOT NULL,
                      pk_status int,
                      PRIMARY KEY  (pk_id)
                    ) $charset_collate;";
            // pk_status = 1 : active, -1: delete
            dbDelta($tables);

            $tables = " CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_user_payment_by_package(
                      upk_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      u_id int,
                      b_id int,
                      s_id int,
                      upk_payment_amount decimal(10,2),
                      pk_create_date datetime NOT NULL,
                      upk_payment_amount decimal(10,2),
                      PRIMARY KEY  (upk_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_price_package_order(
                      pko_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      pko_user_id int(6),
                      pko_user_email varchar(100), 
                      pk_id int(6),
                      pk_price decimal(10,2) NOT NULL DEFAULT 0,
                      pk_price_for_payment decimal(10,2) NOT NULL DEFAULT 0,
                      pko_total_pay decimal(10,2) NOT NULL DEFAULT 0,
                      pko_gateway_type varchar(50),
                      pko_gateway_status varchar(100), 
                      pko_gateway_id varchar(100), 
                      pko_gateway_response varchar(500), 
                      pko_gateway_execute_url varchar(200), 
                      pko_description text,
                      pko_process_status int(1),
                      pko_create_date datetime NOT NULL,
                      pko_canceled_by_client int(1) DEFAULT 0,
                      pko_myPOS_sign varchar(200),
                      pko_myPOS_status varchar(200),
                      pko_myPOS_cardtoken varchar(200),
                      pko_myPOS_ipc_trnref varchar(200),
                      PRIMARY KEY  (pko_id)
                    ) $charset_collate;";
            dbDelta($tables);

            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE  TABLE_SCHEMA = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}fat_sb_user_payment_by_package' AND column_name = 'u_email'" ;
            $row = $wpdb->get_results($sql);
            if(empty($row)){
                $sql = "ALTER TABLE {$wpdb->prefix}fat_sb_user_payment_by_package ADD u_email varchar(200)";
                $wpdb->query($sql);
            }

            $tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fat_sb_booking_multiple_days(
                      bmd_id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
                      b_id int(6),
                      b_detail_id int(6),
                      b_date date,
                      b_datei18n varchar(100),
                      b_time int(6),
                      b_time_label varchar(100),
                      PRIMARY KEY  (bmd_id)
                    ) $charset_collate;";
            dbDelta($tables);

        }
        // pko_process_status = -100 : delete from admin

        function table_exists($table_name)
        {
            global $wpdb;
            $sql = "SHOW TABLES LIKE '" . $wpdb->prefix . $table_name . "'";
            $results = $wpdb->get_results($sql);
            return count($results);
        }

        function drop_tables()
        {
            global $wpdb;
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $tables = array(
                'fat_sb_services_extra',
                'fat_sb_services_employee',
                'fat_sb_services',
                'fat_sb_services_category',
                'fat_sb_locations',
                'fat_sb_employees_schedule',
                'fat_sb_employees_day_off',
                'fat_sb_employees_break_time',
                'fat_sb_employees',
                'fat_sb_customers',
                'fat_sb_coupons',
                'fat_sb_coupon_logs',
                'fat_sb_booking'
            );
            $sql = '';
            foreach ($tables as $table) {
                $sql = "DROP TABLE IF EXISTS {$wpdb->prefix}{$table}";
                $wpdb->query($sql);
            }
        }
    }
}