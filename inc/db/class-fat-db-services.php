<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/7/2019
 * Time: 9:10 AM
 */
if (!class_exists('FAT_DB_Services')) {
    class FAT_DB_Services
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_service_category()
        {
            global $wpdb;
            $sql = "SELECT sc_id, sc_name, sc_image_id, sc_description, sc_total_service FROM {$wpdb->prefix}fat_sb_services_category";
            $categories = $wpdb->get_results($sql);
            foreach ($categories as $cat) {
                $cat->sc_image_url = isset($cat->sc_image_id) ? wp_get_attachment_image_src($cat->sc_image_id, 'thumbnail') : '';
                $cat->sc_image_url = isset($cat->sc_image_url[0]) ? $cat->sc_image_url[0] : '';
            }
            return $categories;
        }

        public function get_services()
        {
            $sc_id = isset($_REQUEST['sc_id']) ? $_REQUEST['sc_id'] : 0;
            global $wpdb;
            $sql = "SELECT s_id, s_order, s_name, s_multiple_days, s_min_multiple_slot, s_max_multiple_slot, s_extra_ids, s_break_time, s_tax, s_image_id, s_price, s_duration, s_description, s_category_id 
                    FROM {$wpdb->prefix}fat_sb_services 
                    WHERE %d = 0 OR s_category_id = %d
                    ORDER BY s_order ASC";
            $sql = $wpdb->prepare($sql, $sc_id, $sc_id);
            $services = $wpdb->get_results($sql);
            foreach ($services as $ser) {
                $ser->s_image_url = isset($ser->s_image_id) ? wp_get_attachment_image_src($ser->s_image_id, 'thumbnail') : '';
                $ser->s_image_url = isset($ser->s_image_url[0]) ? $ser->s_image_url[0] : '';
            }
            return $services;
        }

        public function get_services_hierarchy()
        {
            global $wpdb;
            $sql = "SELECT s_id, s_name, sc_id, sc_name
                                            FROM {$wpdb->prefix}fat_sb_services
                                            LEFT JOIN {$wpdb->prefix}fat_sb_services_category ON s_category_id = sc_id";
            $services = $wpdb->get_results($sql);
            $service_hierarchy = array();
            foreach($services as $service){
                if(!isset($service_hierarchy[$service->sc_id])){
                    $service_hierarchy[$service->sc_id] = array();
                }
                $service_hierarchy[$service->sc_id][] = $service;
            }
            return $service_hierarchy;
        }

        public function get_services_by_name()
        {
            $key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
            global $wpdb;
            $sql = "SELECT s_id, s_name, s_image_id, s_price, s_duration, s_description FROM {$wpdb->prefix}fat_sb_services WHERE 1=%d AND s_name like '%{$key}%'";
            $sql = $wpdb->prepare($sql, 1);
            $services = $wpdb->get_results($sql);
            foreach ($services as $ser) {
                $ser->s_image_url = isset($ser->s_image_id) ? wp_get_attachment_image_src($ser->s_image_id, 'thumbnail') : '';
                $ser->s_image_url = isset($ser->s_image_url[0]) ? $ser->s_image_url[0] : '';
            }
            return $services;
        }

        public function save_service_category()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                global $wpdb;
                if (isset($data['sc_id']) && $data['sc_id'] != '') {
                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_services_category', $data, array('sc_id' => $data['sc_id']));
                } else {
                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_services_category', $data);
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

        public function delete_service_category()
        {
            $sc_id = isset($_REQUEST['sc_id']) && $_REQUEST['sc_id'] ? $_REQUEST['sc_id'] : '';
            if ($sc_id && $sc_id > 0) {
                global $wpdb;
                $sql = "SELECT s_id FROM {$wpdb->prefix}fat_sb_services WHERE s_category_id = %d";
                $sql = $wpdb->prepare($sql, $sc_id);
                $result = $wpdb->get_results($sql);
                if (count($result) > 0) {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('You need to remove the service in this category before deleting the category', 'fat-services-booking')
                    );
                }

                $sql = "SELECT b_id FROM {$wpdb->prefix}fat_sb_booking WHERE b_service_cat_id = %d";
                $sql = $wpdb->prepare($sql, $sc_id);
                $booking = $wpdb->get_results($sql);
                if(is_array($booking) && count($booking)>0){
                    return array(
                        'result' => -1,
                        'message' => esc_html__('You need to delete the appointment of this service category before deleting the category', 'fat-services-booking')
                    );
                }

                $result = $wpdb->delete($wpdb->prefix . 'fat_sb_services_category', array('sc_id' => $sc_id));
                return array(
                    'result' => $result,
                );
            } else {
               return array(
                   'result' => -1,
                   'message' => esc_html__('Data is invalid', 'fat-services-booking')
               );
            }
        }

        public function save_service()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            $extra_field = isset($_REQUEST['extra_field']) && $_REQUEST['extra_field'] ? $_REQUEST['extra_field'] : '';
            $upd_e = isset($_REQUEST['upd_e'])  ? $_REQUEST['upd_e'] : 0;
            if ($data != '' && is_array($data)) {
                global $wpdb;
                if (isset($data['s_id']) && $data['s_id'] != '') {
                    //syn s_employee_ids
                    $sql = "SELECT s_employee_ids, s_price, s_minimum_person, s_maximum_person FROM {$wpdb->prefix}fat_sb_services WHERE  s_id = %d";
                    $sql = $wpdb->prepare($sql, $data['s_id']);
                    $e_services =  $wpdb->get_results($sql);
                    if(count($e_services)>0 && $e_services[0]->s_employee_ids){
                        $old_employee_ids = [];
                        $sql = "SELECT e_id FROM {$wpdb->prefix}fat_sb_services_employee WHERE s_id=%d";
                        $sql = $wpdb->prepare($sql, $data['s_id']);
                        $s_e_ids = $wpdb->get_results($sql);
                        foreach($s_e_ids as $s_emp){
                            $old_employee_ids[] = $s_emp->e_id;
                        }

                        $new_s_employee_ids = explode(',',$data['s_employee_ids']);
                        $remove_s_employee_ids = array_diff($old_employee_ids, $new_s_employee_ids);

                        // remove employee if not belong service
                        foreach ($remove_s_employee_ids as $remove_id){
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_services_employee WHERE s_id=%d AND e_id=%d";
                            $sql = $wpdb->prepare($sql, $data['s_id'], $remove_id);
                            $wpdb->query($sql);
                        }

                        // add employee to fat_sb_services_employee if not exits
                        $add_s_employee_ids = array_diff($new_s_employee_ids,$old_employee_ids);
                        $create_date = current_time( 'mysql', 0);
                        foreach($add_s_employee_ids as $e_id){
                            $wpdb->insert($wpdb->prefix . 'fat_sb_services_employee', array(
                                's_id' => $data['s_id'],
                                'e_id' => $e_id,
                                's_price' => $data['s_price'],
                                's_min_cap' => $data['s_minimum_person'],
                                's_max_cap' => $data['s_maximum_person'],
                                's_create_date' => $create_date
                            ));
                        }
                    }

                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_services', $data, array('s_id' => $data['s_id']));
                    if($upd_e){
                        $wpdb->update("{$wpdb->prefix}fat_sb_services_employee", array(
                            's_price' => $data['s_price'],
                            's_min_cap' => $data['s_minimum_person'],
                            's_max_cap' => $data['s_maximum_person']
                        ), array('s_id' => $data['s_id']));
                    }
                    do_action('fat_sb_after_save_service',$data['s_id'], $data, $extra_field);

                } else {
                    $data['s_create_date'] = current_time( 'mysql', 0);
                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_services', $data);
                    $result = $result > 0 ? $wpdb->insert_id : $result;
                    if($result > 0){
                        $sql = "UPDATE {$wpdb->prefix}fat_sb_services_category SET  sc_total_service = sc_total_service + 1 WHERE sc_id=%d";
                        $sql = $wpdb->prepare($sql, $data['s_category_id']);
                        $wpdb->query($sql);
                        // add fat_sb_services_employee
                        if(isset($data['s_employee_ids']) && $data['s_employee_ids']){
                            $add_s_employee_ids = explode(',',$data['s_employee_ids']);
                            if(count($add_s_employee_ids)>0){
                                $create_date = current_time( 'mysql', 0);
                                foreach($add_s_employee_ids as $add_e_id){
                                    $wpdb->insert($wpdb->prefix . 'fat_sb_services_employee', array(
                                        's_id' => $result,
                                        'e_id' => $add_e_id,
                                        's_price' => $data['s_price'],
                                        's_min_cap' => $data['s_minimum_person'],
                                        's_max_cap' => $data['s_maximum_person'],
                                        's_create_date' => $create_date
                                    ));
                                }
                            }
                        }
                    }
                    do_action('fat_sb_after_save_service',$result, $data, $extra_field);
                }
                $cats_total = $this->update_total_service();

               return array(
                   'result' => $result,
                   'cats' => $cats_total
               );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data for field', 'fat-services-booking')
                );
            }
        }

        public function delete_service()
        {
            $s_id = isset($_REQUEST['s_id']) && $_REQUEST['s_id'] ? $_REQUEST['s_id'] : '';
            if ($s_id && $s_id > 0) {
                global $wpdb;
                $sql = "SELECT b_id FROM {$wpdb->prefix}fat_sb_booking WHERE b_service_id = %d";
                $sql = $wpdb->prepare($sql, $s_id);
                $booking = $wpdb->get_results($sql);
                if(is_array($booking) && count($booking)>0){
                    return array(
                        'result' => -1,
                        'message' => esc_html__('You need to delete the appointment of this service before deleting the service', 'fat-services-booking')
                    );
                }

                $sql = "SELECT s_category_id, s_employee_ids FROM {$wpdb->prefix}fat_sb_services WHERE s_id = %d";
                $sql = $wpdb->prepare($sql, $s_id);
                $result = $wpdb->get_results($sql);
                $sc_id = isset($result[0]->s_category_id) ? $result[0]->s_category_id : 0;

                $result = $wpdb->delete($wpdb->prefix . 'fat_sb_services', array('s_id' => $s_id));
                if ($sc_id) {
                    $wpdb->delete($wpdb->prefix . 'fat_sb_services_employee', array('s_id' => $s_id));
                    $wpdb->delete($wpdb->prefix . 'fat_sb_services_work_day', array('s_id' => $s_id));

                    $sql = "UPDATE {$wpdb->prefix}fat_sb_services_category SET  sc_total_service = sc_total_service - 1 WHERE  sc_total_service > 0 AND sc_id=%d";
                    $sql = $wpdb->prepare($sql, $sc_id);
                    $wpdb->query($sql);
                }

               return array(
                   'result' => $result,
               );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }
        }

        public function get_service_by_id()
        {
            $s_id = isset($_REQUEST['s_id']) ? $_REQUEST['s_id'] : 0;
            global $wpdb;
            $result = array();

            $sql = "SELECT s_id, s_order, s_image_id, s_name, s_link, s_description, s_category_id, s_price, s_tax, s_duration, s_break_time, s_minimum_person, 
                        s_maximum_person, s_extra_ids, s_employee_ids, s_available, s_allow_booking_online, s_multiple_days, s_min_multiple_slot, s_max_multiple_slot
                    FROM {$wpdb->prefix}fat_sb_services 
                    WHERE s_id=%d";
            $sql = $wpdb->prepare($sql, $s_id);
            $services = $wpdb->get_results($sql);

            if (count($services) > 0) {
                $services = $services[0];
                $services->s_image_url = isset($services->s_image_id) ? wp_get_attachment_image_src($services->s_image_id, 'thumbnail') : '';
                $services->s_image_url = isset($services->s_image_url[0]) ? $services->s_image_url[0] : '';
                $services->s_tax = $services->s_tax ? floatval($services->s_tax) : 0;
                $result['service'] = $services;
            } else {
                $result['service'] = array(
                    's_image_id' => 0,
                    's_minimum_person' => 1,
                    's_maximum_person' => 1,
                    's_extra_ids' => '',
                    's_available' => 1,
                    's_allow_booking_online' => 1
                );
            }

            $sql = "SELECT sc_id, sc_name, sc_image_id, sc_description, sc_total_service FROM {$wpdb->prefix}fat_sb_services_category";
            $categories = $wpdb->get_results($sql);
            foreach ($categories as $cat) {
                $cat->sc_image_url = isset($cat->sc_image_id) ? wp_get_attachment_url($cat->sc_image_id) : '';
            }
            $result['categories'] = $categories;

            $sql = "SELECT se_id, se_name FROM {$wpdb->prefix}fat_sb_services_extra";
            $result['services_extra'] = $wpdb->get_results($sql);

            $sql = "SELECT e_id, e_first_name, e_last_name, e_avatar_id, e_email FROM {$wpdb->prefix}fat_sb_employees";
            $employees = $wpdb->get_results($sql);
            foreach ($employees as $emp) {
                $emp->e_avatar_url = isset($emp->e_avatar_id) ? wp_get_attachment_url($emp->e_avatar_id) : '';
            }
            $result['employees'] = $employees;

            $result = apply_filters('fat_sb_get_service_info',$result);

            return $result;
        }

        public function get_service_work_day(){
            $s_id = isset($_REQUEST['s_id']) ? $_REQUEST['s_id'] : 0;
            global $wpdb;
            $sql = "SELECT swd_id, s_id, from_date, to_date FROM {$wpdb->prefix}fat_sb_services_work_day WHERE s_id=%d";
            $sql = $wpdb->prepare($sql, $s_id);
            return $wpdb->get_results($sql);
        }

        public function save_service_work_day(){
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            $s_id = isset($_REQUEST['s_id']) && $_REQUEST['s_id'] ? $_REQUEST['s_id'] : '';
            if($s_id){
                global $wpdb;
                $wpdb->delete($wpdb->prefix . 'fat_sb_services_work_day', array('s_id' => $s_id));
                $result = 0;
                if(is_array($data)){
                    foreach($data as $d){
                        $result += $wpdb->insert($wpdb->prefix . 'fat_sb_services_work_day', array(
                            's_id' => $d['s_id'],
                            'from_date' => $d['from_date'],
                            'to_date' => $d['to_date']
                        ));
                    }
                }
                return array(
                    'result' => $result,
                );
            }
        }

        public function get_categories()
        {
            global $wpdb;
            $sql = "SELECT sc_id, sc_name, sc_image_id, sc_description, sc_total_service FROM {$wpdb->prefix}fat_sb_services_category";
            return $wpdb->get_results($sql);
        }

        public function get_employees()
        {
            global $wpdb;
            $sql = "SELECT e_id, e_first_name, e_last_name, e_avatar_id FROM {$wpdb->prefix}fat_sb_employees";
            return $wpdb->get_results($sql);
        }

        public function get_services_extra()
        {
            global $wpdb;
            $sql = "SELECT se_id, se_name, se_image_id, se_price, se_duration, se_description, se_price_on_total FROM {$wpdb->prefix}fat_sb_services_extra";
            $services_extra = $wpdb->get_results($sql);
            foreach ($services_extra as $ser) {
                $ser->se_image_url = isset($ser->se_image_id) ? wp_get_attachment_image_src($ser->se_image_id, 'thumbnail') : '';
                $ser->se_image_url = isset($ser->se_image_url[0]) ? $ser->se_image_url[0] : '';
            }
            return $services_extra;
        }

        public function get_service_employee(){
            global $wpdb;
            $sql = "SELECT s_id, e_id, s_price FROM {$wpdb->prefix}fat_sb_services_employee";
            return $wpdb->get_results($sql);
        }

        public function get_services_dictionary()
        {
            global $wpdb;
            $layout = isset($_REQUEST['layout']) ? $_REQUEST['layout'] : '';
            $cat_id = isset($_REQUEST['cat_id']) ? $_REQUEST['cat_id'] : '';

            $services_cat = array();
            $sql = "SELECT sc_id, sc_name, sc_description, sc_image_id, sc_total_service FROM {$wpdb->prefix}fat_sb_services_category";
            if($cat_id ){
                $sql .= " WHERE sc_id=".$cat_id;
            }
            $services_cat = $wpdb->get_results($sql);
            foreach ($services_cat as $cat) {
                $cat->sc_image_url = isset($cat->sc_image_id) ? wp_get_attachment_image_src($cat->sc_image_id, 'thumbnail') : '';
                $cat->sc_image_url = isset($cat->sc_image_url[0]) ? $cat->sc_image_url[0] : '';
            }

            $sql = "SELECT loc_id, loc_address, loc_name FROM {$wpdb->prefix}fat_sb_locations";
            $location = $wpdb->get_results($sql);

            $sql = "SELECT s_id, s_name, s_image_id, s_price, s_duration, s_break_time, s_description, s_category_id, s_extra_ids, s_tax,
                            s_multiple_days, s_min_multiple_slot, s_max_multiple_slot 
                    FROM {$wpdb->prefix}fat_sb_services 
                    WHERE s_allow_booking_online = 1 AND s_id IN (SELECT s_id FROM {$wpdb->prefix}fat_sb_services_employee) ";
            if($cat_id ){
                $sql .= " AND s_category_id =".$cat_id;
            }
            $sql .= " ORDER BY s_order ASC";
            $services = $wpdb->get_results($sql);

            if($layout == 'services'){
                $duration = FAT_SB_Utils::getDurations(1,'duration_step');
                foreach ($services as $ser) {
                    $ser->s_image_url = isset($ser->s_image_id) ? wp_get_attachment_image_src($ser->s_image_id, 'thumbnail') : '';
                    $ser->s_image_url = isset($ser->s_image_url[0]) ? $ser->s_image_url[0] : '';
                    $ser->s_duration_label = isset($duration[$ser->s_duration]) ? $duration[$ser->s_duration] : $ser->s_duration;
                }
            }

            $sql = "SELECT s_id, from_date, to_date FROM {$wpdb->prefix}fat_sb_services_work_day";
            $services_work_day = $wpdb->get_results($sql);

            $sql = "SELECT se_id, se_name, se_price, se_tax, se_min_quantity, se_max_quantity, se_duration, se_multiple_book, se_price_on_total FROM {$wpdb->prefix}fat_sb_services_extra";
            $services_extra = $wpdb->get_results($sql);

            $sql = "SELECT e_id, e_email, e_phone, e_first_name, e_last_name, e_location_ids, e_avatar_id FROM {$wpdb->prefix}fat_sb_employees WHERE e_enable=1";
            $employees = $wpdb->get_results($sql);
            if($layout == 'services'){
                foreach ($employees as $emp) {
                    $emp->e_avatar_url = isset($emp->e_avatar_id) ? wp_get_attachment_image_src($emp->e_avatar_id, 'thumbnail') : '';
                    $emp->e_avatar_url = isset($emp->e_avatar_url[0]) ? $emp->e_avatar_url[0] : '';
                }
            }

            $sql = "SELECT e_id, s_id, s_max_cap, s_min_cap, s_price FROM {$wpdb->prefix}fat_sb_services_employee";
            $services_employee = $wpdb->get_results($sql);

            return array(
                'services_cat' => $services_cat,
                'location' => $location,
                'services' => $services,
                'services_work_day' => $services_work_day,
                'services_extra' => $services_extra,
                'employee' => $employees,
                'services_employee' => $services_employee,
            );
        }

        public function get_services_available(){
            global $wpdb;
            $sql = "SELECT s_id, s_name, s_image_id, s_duration,s_tax 
                    FROM {$wpdb->prefix}fat_sb_services 
                    WHERE s_allow_booking_online = 1 AND s_id IN (SELECT s_id FROM {$wpdb->prefix}fat_sb_services_employee)";
            $services = $wpdb->get_results($sql);
            return $services;
        }

        public function get_one_service_provider_dictionary()
        {
            global $wpdb;
            $s_id = isset($_REQUEST['s_id']) ? $_REQUEST['s_id'] : '';
            $e_id = isset($_REQUEST['e_id']) ? $_REQUEST['e_id'] : '';

            if($s_id){
                $sql = "SELECT s_id, s_name, s_image_id, s_price, s_duration, s_break_time, s_description, s_category_id, s_extra_ids, s_tax 
                                            FROM {$wpdb->prefix}fat_sb_services 
                                            WHERE s_allow_booking_online = %d AND s_id=%d
                                            ORDER BY s_id ASC";
                $sql = $wpdb->prepare($sql, 1, $s_id);
                $services = $wpdb->get_results($sql);

                $sql = "SELECT s_id, from_date, to_date FROM {$wpdb->prefix}fat_sb_services_work_day WHERE s_id=%d";
                $sql = $wpdb->prepare($sql, $s_id);
                $services_work_day = $wpdb->get_results($sql);

            }else{
                $sql = "SELECT s_id, s_name, s_image_id, s_price, s_break_time, s_duration, s_description, s_category_id, s_extra_ids, s_tax 
                                            FROM {$wpdb->prefix}fat_sb_services 
                                            WHERE s_allow_booking_online = %d 
                                            ORDER BY s_id ASC";

                $sql = $wpdb->prepare($sql, 1);
                $services = $wpdb->get_results($sql);

                $sql = "SELECT s_id, from_date, to_date FROM {$wpdb->prefix}fat_sb_services_work_day ORDER BY s_id ASC";
                $sql = $wpdb->prepare($sql, $s_id);
                $services_work_day = $wpdb->get_results($sql);
            }

            if($e_id){
                $sql = "SELECT e_id, s_id, s_max_cap, s_min_cap, s_price 
                        FROM {$wpdb->prefix}fat_sb_services_employee 
                        WHERE e_id=%d
                        ORDER BY e_id ASC";
                $sql = $wpdb->prepare($sql, $e_id);
                $services_employee = $wpdb->get_results($sql);

                $sql = "SELECT es_day, es_enable, es_work_hour_start, es_work_hour_end, e_id, s_id FROM {$wpdb->prefix}fat_sb_employees_schedule WHERE e_id=%d";
                $sql = $wpdb->prepare($sql, $e_id);
                $employee_schedule = $wpdb->get_results($sql);

                $sql = "SELECT e_id, dof_start, dof_end FROM {$wpdb->prefix}fat_sb_employees_day_off WHERE e_id=%d";
                $sql = $wpdb->prepare($sql, $e_id);
                $employee_day_off = $wpdb->get_results($sql);

                $sql = "SELECT e_id, es_day, es_break_time_end, es_break_time_start  FROM {$wpdb->prefix}fat_sb_employees_break_time WHERE e_id=%d";
                $sql = $wpdb->prepare($sql, $e_id);
                $employee_break_time = $wpdb->get_results($sql);

            }else{
                $sql = "SELECT e_id, s_id, s_max_cap, s_min_cap, s_price FROM {$wpdb->prefix}fat_sb_services_employee";
                $services_employee = $wpdb->get_results($sql);

                $sql = "SELECT es_day, es_enable, es_work_hour_start, es_work_hour_end, e_id, s_id FROM {$wpdb->prefix}fat_sb_employees_schedule";
                $sql = $wpdb->prepare($sql, $e_id);
                $employee_schedule = $wpdb->get_results($sql);

                $sql = "SELECT e_id, dof_start, dof_end FROM {$wpdb->prefix}fat_sb_employees_day_off";
                $sql = $wpdb->prepare($sql, $e_id);
                $employee_day_off = $wpdb->get_results($sql);

                $sql = "SELECT e_id, es_day, es_break_time_end, es_break_time_start  FROM {$wpdb->prefix}fat_sb_employees_break_time";
                $sql = $wpdb->prepare($sql, $e_id);
                $employee_break_time = $wpdb->get_results($sql);
            }

            $sql = "SELECT se_id, se_name, se_price, se_tax, se_min_quantity, se_max_quantity, se_duration FROM {$wpdb->prefix}fat_sb_services_extra";
            $services_extra = $wpdb->get_results($sql);

            return array(
                'services' => $services,
                'services_work_day' => $services_work_day,
                'services_employee' => $services_employee,
                'employees_schedule' => $employee_schedule,
                'employee_day_off' => $employee_day_off,
                'employee_break_time' => $employee_break_time,
                'services_extra' => $services_extra
            );
        }

        private function update_total_service(){
            global $wpdb;
            $sql = "SELECT s_category_id, COUNT(s_category_id) as total FROM {$wpdb->prefix}fat_sb_services GROUP BY s_category_id";
            $cats = $wpdb->get_results($sql);
            $result = array();
            foreach($cats as $cat){
                $result[$cat->s_category_id] = $cat->total;
                $wpdb->update("{$wpdb->prefix}fat_sb_services_category",array('sc_total_service' => $cat->total), array('sc_id' => $cat->s_category_id));
            }
            return $result;
        }

        // fix for add employee when create service
        public function syn_service_employee(){
            global $wpdb;
            $sql = "SELECT s_id, s_employee_ids, s_price, s_minimum_person, s_maximum_person FROM {$wpdb->prefix}fat_sb_services";
            $e_services =  $wpdb->get_results($sql);
            $s_employee_ids = '';
            $s_id = 0;
            foreach($e_services as $es){
                $s_employee_ids = $es->s_employee_ids;
                $s_id = $es->s_id;
                $old_employee_ids = [];
                $sql = "SELECT e_id FROM {$wpdb->prefix}fat_sb_services_employee WHERE s_id=%d";
                $sql = $wpdb->prepare($sql, $s_id);
                $s_e_ids = $wpdb->get_results($sql);
                foreach($s_e_ids as $s_emp){
                    $old_employee_ids[] = $s_emp->e_id;
                }

                $new_s_employee_ids = explode(',',$s_employee_ids);
                $remove_s_employee_ids = array_diff($old_employee_ids, $new_s_employee_ids);


                foreach ($remove_s_employee_ids as $remove_id){
                    $sql = "DELETE FROM {$wpdb->prefix}fat_sb_services_employee WHERE s_id=%d AND e_id=%d";
                    $sql = $wpdb->prepare($sql, $s_id, $remove_id);
                    $wpdb->query($sql);
                }


                $add_s_employee_ids = array_diff($new_s_employee_ids,$old_employee_ids);
                $create_date = current_time( 'mysql', 0);
                foreach($add_s_employee_ids as $e_id){
                    $wpdb->insert($wpdb->prefix . 'fat_sb_services_employee', array(
                        's_id' => $s_id,
                        'e_id' => $e_id,
                        's_price' => $es->s_price,
                        's_min_cap' => $es->s_minimum_person,
                        's_max_cap' => $es->s_maximum_person,
                        's_create_date' => $create_date
                    ));
                }

            }
        }

        //get service available in weekly
        public function get_services_available_in_weekly($start, $end, $service_id){
            global $wpdb;
            $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $start . ' 00:00:00');
            $end_date = DateTime::createFromFormat('Y-m-d H:i:s', $end . ' 23:59:59');
            $now = current_time('mysql',0);
            $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);

            //get first employee
            $sql = "SELECT e_id, e_location_ids FROM {$wpdb->prefix}fat_sb_employees WHERE e_enable=%d";
            $sql = $wpdb->prepare($sql, 1);
            $employees = $wpdb->get_results($sql);
            $e_id = isset($employees[0]) ? $employees[0]->e_id : 0;
            $e_loc_ids = isset($employees[0]) ? $employees[0]->e_location_ids : '';

            //get location id
            $sql = "SELECT loc_id FROM {$wpdb->prefix}fat_sb_locations WHERE loc_id IN ({$e_loc_ids})";
            $location = $wpdb->get_results($sql);
            $loc_id = isset($location[0]) ? $location[0]->loc_id : 0;


            // get services
            $sql = "SELECT S.s_id, s_name, s_duration, S.s_image_id, S.s_description , s_break_time, s_tax, SE.e_id, SE.s_price, SE.s_min_cap, SE.s_max_cap
                    FROM {$wpdb->prefix}fat_sb_services AS S 
                    INNER JOIN {$wpdb->prefix}fat_sb_services_employee AS SE
                    ON S.s_id = SE.s_id
                    WHERE s_allow_booking_online = %d AND s_available = %d AND SE.e_id=%d";
            if($service_id){
                $sql .= " AND S.s_id IN (".$service_id .")";
            }

            $sql = $wpdb->prepare($sql, 1, 1, $e_id);
            $services = $wpdb->get_results($sql);

            $s_ids = array();
            foreach($services as $se){
                $s_ids[] = $se->s_id;
                $se->s_image_url = isset($se->s_image_id) ? wp_get_attachment_image_src($se->s_image_id, 'thumbnail') : '';
                $se->s_image_url = isset($se->s_image_url[0]) ? $se->s_image_url[0] : '';
            }
            $s_ids = implode(',', $s_ids);

            //get service work day
            $sql = "SELECT s_id, from_date, to_date
                    FROM {$wpdb->prefix}fat_sb_services_work_day
                    WHERE s_id IN ({$s_ids})";
            $services_work_day = $wpdb->get_results($sql);

            // get employee schedule
            $sql = "SELECT es_day, es_work_hour_start, es_work_hour_end, s_id
                    FROM {$wpdb->prefix}fat_sb_employees_schedule
                    WHERE e_id = %d AND es_enable = %d AND (s_id=0 OR s_id IN ({$s_ids}) )";
            $sql = $wpdb->prepare($sql, $e_id, 1);
            $employee_schedules = $wpdb->get_results($sql);
            $es_days = array();
            foreach($employee_schedules as $es){
                $es_days[$es->es_day][] = array(
                    'es_day' => $es->es_day,
                    'work_start' => $es->es_work_hour_start,
                    'work_end' => $es->es_work_hour_end,
                    's_id' => $es->s_id
                );
            }

            // get employee day off
            $sql = "SELECT e_id, dof_start, dof_end
                    FROM {$wpdb->prefix}fat_sb_employees_day_off";
            $es_day_off = $wpdb->get_results($sql);

            // get booking in this ranger
            $sql = "SELECT b_service_id, b_service_duration, b_service_break_time, b_date, b_time, SUM(b_customer_number) AS total_number
                    FROM {$wpdb->prefix}fat_sb_booking
                    WHERE b_date >= %s AND b_date <= %s AND b_employee_id = %d AND b_process_status IN (0,1)
                    GROUP BY b_service_id, b_date, b_time, b_service_duration, b_service_break_time";
            $sql = $wpdb->prepare($sql, $start, $end, $e_id);
            $bookings = $wpdb->get_results($sql);

            $now = current_time('mysql',0);
            $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
            $slot = intval($now->format('H'))*60 + intval($now->format('i'));
            return array(
                'loc_id' => $loc_id,
                'services' => $services,
                'es_schedule' => $es_days,
                'es_day_off' => $es_day_off,
                'bookings' => $bookings,
                'current_slot' => $slot,
                'current_time' => $now->format('Y-m-d'),
                'services_work_day' =>$services_work_day
            );
        }
    }
}