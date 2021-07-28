<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/7/2019
 * Time: 9:10 AM
 */
if (!class_exists('FAT_DB_Bookings')) {
    class FAT_DB_Bookings
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_booking()
        {
            global $wpdb;
            $page = isset($_REQUEST['page']) && $_REQUEST['page'] ? $_REQUEST['page'] : 1;
            $b_customer_name = isset($_REQUEST['b_customer_name']) && $_REQUEST['b_customer_name'] ? $_REQUEST['b_customer_name'] : '';
            $start_date = isset($_REQUEST['start_date']) && $_REQUEST['start_date'] ? $_REQUEST['start_date'] : '';
            $start_time = isset($_REQUEST['start_time']) && $_REQUEST['start_time'] ? $_REQUEST['start_time'] : '00:00';
            $end_date = isset($_REQUEST['end_date']) && $_REQUEST['end_date'] ? $_REQUEST['end_date'] : '';
            $end_time = isset($_REQUEST['end_time']) && $_REQUEST['end_time'] ? $_REQUEST['end_time'] : '23:59';
            $b_employee = isset($_REQUEST['b_employee']) && $_REQUEST['b_employee'] ? $_REQUEST['b_employee'] : '';
            $b_customer = isset($_REQUEST['b_customer']) && $_REQUEST['b_customer'] ? $_REQUEST['b_customer'] : '';
            $b_service = isset($_REQUEST['b_service']) && $_REQUEST['b_service'] ? $_REQUEST['b_service'] : '';
            $b_process_status = isset($_REQUEST['b_process_status']) ? $_REQUEST['b_process_status'] : '';
            $order = isset($_REQUEST['order']) && $_REQUEST['order'] ? $_REQUEST['order'] : 'DESC';
            $order_by = isset($_REQUEST['order_by']) && $_REQUEST['order_by'] ? $_REQUEST['order_by'] : 'b_date';
            $b_location = isset($_REQUEST['location']) && $_REQUEST['location'] ? $_REQUEST['location'] : '';

            $sql = "SELECT b_date, b_time, b_id, b_customer_id, c_first_name, c_last_name, c_email, e_first_name, e_last_name, e_email, s_name, b_service_duration, b_gateway_type, b_gateway_status, b_total_pay, b_process_status, b_create_date
                                        FROM {$wpdb->prefix}fat_sb_booking LEFT JOIN {$wpdb->prefix}fat_sb_customers ON b_customer_id = c_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        WHERE  b_process_status!=%d ";
            if ($b_customer_name) {
                $sql .= " AND (c_first_name LIKE '%{$b_customer_name}%' OR c_last_name LIKE '%{$b_customer_name}%' OR c_email LIKE '%{$b_customer_name}%') ";
            }
            if ($b_employee && is_array($b_employee)) {
                $b_employee = implode(',', $b_employee);
                $sql .= " AND b_employee_id IN ({$b_employee}) ";
            }
            if ($b_customer && is_array($b_customer)) {
                $b_customer = implode(',', $b_customer);
                $sql .= " AND b_customer_id IN ({$b_customer})";
            }
            if ($b_service && is_array($b_service)) {
                $b_service = implode(',', $b_service);
                $sql .= " AND b_service_id IN ({$b_service})";
            }
            if ($b_process_status != '') {
                $sql .= " AND b_process_status = {$b_process_status}";
            }

            if($b_location && is_array($b_location)){
                $b_location = implode(',', $b_location);
                $sql .= " AND b_loc_id IN ({$b_location})";
            }

            if ($start_date && $end_date) {
                $sql .= " AND DATE(b_date) BETWEEN '{$start_date}' AND '{$end_date}'";
            }
            $sql .= " ORDER BY {$order_by} {$order}";
            $sql = $wpdb->prepare($sql, -1);
            $bookings = $wpdb->get_results($sql);
            $hours = FAT_SB_Utils::getDurations(1, 'duration_step');

            $total_cancel = 0;
            $total_pending = 0;
            $total_reject = 0;
            $total_approved = 0;

            $b_date = '';
            $b_create_date = '';
            $now = current_time('mysql', 0);
            $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
            $date_format = get_option('date_format');

            $start_date_time = DateTime::createFromFormat('Y-m-d H:i', $start_date . ' ' . $start_time);
            $end_date_time = DateTime::createFromFormat('Y-m-d H:i', $end_date . ' ' . $end_time);
            $bookings_filter = array();
            foreach ($bookings as $booking) {
                $b_date = DateTime::createFromFormat('Y-m-d H:i:s', $booking->b_date . ' 00:00:00');
                $booking->b_date = $b_date->format($date_format);
                $b_date->modify("+{$booking->b_time} minutes");
                
                $b_create_date = DateTime::createFromFormat('Y-m-d H:i:s', $booking->b_create_date);
                $booking->b_create_date = $b_create_date->format($date_format .' H:i');

                if ($b_date >= $start_date_time && $b_date <= $end_date_time) {
                    $booking->editable = $b_date > $now ? 1 : 0;
                    $booking->b_service_duration_display = $hours[$booking->b_service_duration];
                    if ($booking->b_process_status == 0) {
                        $total_pending++;
                    }
                    if ($booking->b_process_status == 1) {
                        $total_approved++;
                    }
                    if ($booking->b_process_status == 2) {
                        $total_cancel++;
                    }
                    if ($booking->b_process_status == 3) {
                        $total_reject++;
                    }
                    $bookings_filter[] = $booking;
                }

            }
            $bookings = $bookings_filter;
            $total = count($bookings);

            $fat_db_setting = FAT_DB_Setting::instance();
            $setting = $fat_db_setting->get_setting();

            $item_per_page = isset($setting['item_per_page']) ? $setting['item_per_page'] : 10;
            $number_of_page = $total / $item_per_page + ($total % $item_per_page > 0 ? 1 : 0);
            $page = $page > $number_of_page ? $number_of_page : $page;
            $page = ($page - 1) * $item_per_page;
            $bookings = array_slice($bookings, $page, $item_per_page);
            return array(
                'total' => $total,
                'bookings' => $bookings,
                'total_cancel' => $total_cancel,
                'total_approved' => $total_approved,
                'total_pending' => $total_pending,
                'total_reject' => $total_reject
            );
        }

        public function get_booking_export()
        {
            global $wpdb;
            $page = isset($_REQUEST['page']) && $_REQUEST['page'] ? $_REQUEST['page'] : 1;
            $b_customer_name = isset($_REQUEST['b_customer_name']) && $_REQUEST['b_customer_name'] ? $_REQUEST['b_customer_name'] : '';
            $start_date = isset($_REQUEST['start_date']) && $_REQUEST['start_date'] ? $_REQUEST['start_date'] : '';
            $start_time = isset($_REQUEST['start_time']) && $_REQUEST['start_time'] ? $_REQUEST['start_time'] : '00:00';
            $end_date = isset($_REQUEST['end_date']) && $_REQUEST['end_date'] ? $_REQUEST['end_date'] : '';
            $end_time = isset($_REQUEST['end_time']) && $_REQUEST['end_time'] ? $_REQUEST['end_time'] : '23:59';
            $b_employee = isset($_REQUEST['b_employee']) && $_REQUEST['b_employee'] ? $_REQUEST['b_employee'] : '';
            $b_customer = isset($_REQUEST['b_customer']) && $_REQUEST['b_customer'] ? $_REQUEST['b_customer'] : '';
            $b_service = isset($_REQUEST['b_service']) && $_REQUEST['b_service'] ? $_REQUEST['b_service'] : '';
            $b_process_status = isset($_REQUEST['b_process_status']) ? $_REQUEST['b_process_status'] : '';
            $b_location = isset($_REQUEST['location']) && $_REQUEST['location'] ? $_REQUEST['location'] : '';

            $sql = "SELECT b_date, b_time, b_id, b_customer_id, c_first_name, c_last_name, c_email, e_first_name, e_last_name, e_email, s_name, b_service_duration, b_gateway_type, b_gateway_status, b_total_pay, b_process_status, b_form_builder
                                        FROM {$wpdb->prefix}fat_sb_booking LEFT JOIN {$wpdb->prefix}fat_sb_customers ON b_customer_id = c_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        WHERE  b_process_status!=%d ";
            if ($b_customer_name) {
                $sql .= " AND (c_first_name LIKE '%{$b_customer_name}%' OR c_last_name LIKE '%{$b_customer_name}%' OR c_email LIKE '%{$b_customer_name}%') ";
            }
            if ($b_employee && is_array($b_employee)) {
                $b_employee = implode(',', $b_employee);
                $sql .= " AND b_employee_id IN ({$b_employee}) ";
            }
            if ($b_customer && is_array($b_customer)) {
                $b_customer = implode(',', $b_customer);
                $sql .= " AND b_customer_id IN ({$b_customer})";
            }
            if ($b_service && is_array($b_service)) {
                $b_service = implode(',', $b_service);
                $sql .= " AND b_service_id IN ({$b_service})";
            }
            if ($b_process_status != '') {
                $sql .= " AND b_process_status = {$b_process_status}";
            }

            if($b_location && is_array($b_location)){
                $b_location = implode(',', $b_location);
                $sql .= " AND b_loc_id IN ({$b_location})";
            }

            if ($start_date && $end_date) {
                $sql .= " AND DATE(b_date) BETWEEN '{$start_date}' AND '{$end_date}'";
            }
            $sql .= " ORDER BY b_id DESC";
            $sql = $wpdb->prepare($sql, -1);
            $bookings = $wpdb->get_results($sql);
            $hours = FAT_SB_Utils::getDurations(1, 'duration_step');

            $start = '';
            $end = '';
            $start_date_time = DateTime::createFromFormat('Y-m-d H:i', $start_date . ' ' . $start_time);
            $end_date_time = DateTime::createFromFormat('Y-m-d H:i', $end_date . ' ' . $end_time);
            $bookings_filter = array();
            foreach ($bookings as $booking) {
                $start = DateTime::createFromFormat('Y-m-d H:i:s', $booking->b_date . ' 00:00:00');
                $start->modify("+{$booking->b_time} minutes");
                if ($start >= $start_date_time && $start <= $end_date_time) {
                    $end = clone $start;
                    $end->modify("+{$booking->b_service_duration} minutes");
                    $booking->start = $start->format('Y-m-d H:i');
                    $booking->end = $end->format('Y-m-d H:i');
                    $booking->b_service_duration_display = $hours[$booking->b_service_duration];
                    $bookings_filter[] = $booking;
                }

            }
            return $bookings_filter;
        }

        public function get_booking_calendar()
        {
            global $wpdb;
            $from_date = isset($_REQUEST['from_date']) && $_REQUEST['from_date'] ? $_REQUEST['from_date'] : (new DateTime())->format('Y-m-d');
            $to_date = isset($_REQUEST['to_date']) && $_REQUEST['to_date'] ? $_REQUEST['to_date'] : (new DateTime())->format('Y-m-d');
            $b_employee = isset($_REQUEST['employee']) && $_REQUEST['employee'] ? $_REQUEST['employee'] : '';
            $b_customer = isset($_REQUEST['customer']) && $_REQUEST['customer'] ? $_REQUEST['customer'] : '';
            $b_service = isset($_REQUEST['service']) && $_REQUEST['service'] ? $_REQUEST['service'] : '';
            $b_location = isset($_REQUEST['location']) && $_REQUEST['location'] ? $_REQUEST['location'] : '';

            $sql = "SELECT b_date, b_id, b_customer_id, c_first_name, c_last_name, c_email, e_avatar_id, e_first_name, e_last_name, e_email, s_name, b_date, b_time, b_service_duration, b_process_status, loc_name, loc_address
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        LEFT JOIN {$wpdb->prefix}fat_sb_customers ON b_customer_id = c_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_locations ON b_loc_id = loc_id
                                        WHERE  b_process_status!=-1 AND DATE(b_date) BETWEEN %s AND %s ";
            if ($b_employee && is_array($b_employee)) {
                $b_employee = implode(',', $b_employee);
                $sql .= " AND b_employee_id IN ({$b_employee}) ";
            }

            if ($b_customer && is_array($b_customer)) {
                $b_customer = implode(',', $b_customer);
                $sql .= " AND b_customer_id IN ({$b_customer}) ";
            }

            if ($b_service && is_array($b_service)) {
                $b_service = implode(',', $b_service);
                $sql .= " AND b_service_id IN ({$b_service})";
            }

            if($b_location && is_array($b_location)){
                $b_location = implode(',', $b_location);
                $sql .= " AND b_loc_id IN ({$b_location})";
            }

            $sql = $wpdb->prepare($sql, $from_date, $to_date);
            $bookings = $wpdb->get_results($sql);
            $result = array();
            $start = '';
            $end = '';
            $color = array(
                0 => '#fbbd08',
                1 => '#21ba45',
                2 => '#db2828',
                3 => '#b5b5b5'
            );
            $now = current_time('mysql', 0);
            $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
            $attach = '';
            foreach ($bookings as $booking) {
                $attach = wp_get_attachment_image_src($booking->e_avatar_id);
                $booking->e_avatar_url = isset($attach[0]) ? $attach[0] : '';

                $start = DateTime::createFromFormat('Y-m-d H:i:s', $booking->b_date . ' 00:00:00');
                $start->modify("+{$booking->b_time} minutes");
                $end = clone $start;
                $end->modify("+{$booking->b_service_duration} minutes");
                $result[] = array(
                    'id' => $booking->b_id,
                    'title' => $booking->s_name,
                    'start' => $start->format('Y-m-d H:i:s'),
                    'end' => $end->format('Y-m-d H:i:s'),
                    'color' => isset($color[$booking->b_process_status]) ? $color[$booking->b_process_status] : $color[0],
                    'service' => $booking->s_name,
                    'customer' => $booking->c_first_name . ' ' . $booking->c_last_name,
                    'employee' => $booking->e_first_name . ' ' . $booking->e_last_name,
                    'e_avatar_url' => $booking->e_avatar_url,
                    'time' => $start->format('H:i') . ' - ' . $end->format('H:i'),
                    'location' => $booking->loc_name,
                    'location_address' => $booking->loc_address,
                    'b_editable' => $start > $now ? 1 : 0
                );
            }
            return array(
                'bookings' => $result,
                'date' => $from_date
            );
        }

        public function get_booking_by_id()
        {
            $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : 0;
            global $wpdb;
            $result['booking'] = array(
                'b_id' => 0,
                'b_gateway_type' => 'onsite'
            );
            if ($b_id) {
                $sql = "SELECT b_id, b_customer_id, b_customer_number, b_loc_id, b_employee_id, b_service_cat_id, b_service_id, b_service_duration, b_date, b_time, b_price, b_services_extra, b_service_tax_amount,
                                          b_total_amount, b_coupon_id, b_coupon_code, b_discount, b_total_pay, b_gateway_type, b_gateway_status, b_process_status, b_description, b_create_date, b_pay_now,
                                          b_total_extra_amount, b_send_notify, b_form_builder, b_status_note, b_canceled_by_client
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        WHERE b_id=%d";
                $sql = $wpdb->prepare($sql, $b_id);
                $booking = $wpdb->get_results($sql);

                if (count($booking) > 0) {
                    $result['booking'] = $booking[0];
                    if(isset($result['booking']->b_form_builder) && $result['booking']->b_form_builder){
                        $result['booking']->b_form_builder = json_decode($result['booking']->b_form_builder );
                    }
                }
            }

            $result['locations'] = $wpdb->get_results("SELECT loc_id, loc_name, loc_address, loc_description FROM {$wpdb->prefix}fat_sb_locations");

            $result['services_cat'] = $wpdb->get_results("SELECT sc_id, sc_name FROM {$wpdb->prefix}fat_sb_services_category");

            $result['services'] = $wpdb->get_results("SELECT s_id, s_name, s_price, s_duration, s_break_time, s_minimum_person, s_maximum_person, s_category_id, s_extra_ids, s_tax FROM {$wpdb->prefix}fat_sb_services");

            $result['services_extra'] = $wpdb->get_results("SELECT se_id, se_name, se_price, se_duration, se_min_quantity, se_max_quantity, se_tax FROM {$wpdb->prefix}fat_sb_services_extra");

            $result['employees'] = $wpdb->get_results("SELECT e_id, e_first_name, e_last_name, e_location_ids FROM {$wpdb->prefix}fat_sb_employees");

            foreach ($result['employees'] as $employee) {
                $employee->e_services = $wpdb->get_results("SELECT s_id, s_price, s_max_cap, s_min_cap
                                                                FROM {$wpdb->prefix}fat_sb_services_employee 
                                                                WHERE e_id={$employee->e_id}");
            }

            $result['customers'] = $wpdb->get_results("SELECT c_id, c_first_name, c_last_name FROM {$wpdb->prefix}fat_sb_customers");

            $result['services_work_day'] = $wpdb->get_results("SELECT s_id, from_date, to_date FROM {$wpdb->prefix}fat_sb_services_work_day");

            return $result;
        }

        public function get_booking_calendar_by_id()
        {
            global $wpdb;
            $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : 0;

            $sql = "SELECT b_id, c_first_name, c_last_name, e_avatar_id, e_first_name, e_last_name, s_name, b_service_duration, 
                                            loc_name, loc_address, b_date, b_time,  b_process_status
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        LEFT JOIN {$wpdb->prefix}fat_sb_customers ON b_customer_id = c_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_locations ON b_loc_id = loc_id
                                        WHERE b_id=%d";
            $sql = $wpdb->prepare($sql, $b_id);
            $booking = $wpdb->get_results($sql);

            if (count($booking) > 0) {
                $now = current_time('mysql', 0);
                $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
                $booking = $booking[0];
                $attach = wp_get_attachment_image_src($booking->e_avatar_id);
                $booking->e_avatar_url = isset($attach[0]) ? $attach[0] : '';

                $booking->start = DateTime::createFromFormat('Y-m-d H:i:s', $booking->b_date . ' 00:00:00');
                $booking->start->modify("+{$booking->b_time} minutes");
                $booking->b_editable = $booking->start > $now ? 1 : 0;
                $booking->end = clone $booking->start;
                $booking->end->modify("+{$booking->b_service_duration} minutes");

                $booking->time = $booking->start->format('H:i') . ' - ' . $booking->end->format('H:i');
                $booking->start = $booking->start->format('Y-m-d H:i');
                $booking->end = $booking->end->format('Y-m-d H:i');
                return $booking;
            } else {
                return null;
            }
        }

        public function get_booking_slot()
        {
            global $wpdb;
            $s_id = isset($_REQUEST['s_id']) ? $_REQUEST['s_id'] : '';
            $e_id = isset($_REQUEST['e_id']) ? $_REQUEST['e_id'] : '';
            $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : 0;
            $loc_id = isset($_REQUEST['loc_id']) ? $_REQUEST['loc_id'] : '';
            $now = current_time('mysql', 0);
            $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
            $now = $now->modify('-1 day');
            $now = $now->format('Y-m-d');

            if ($e_id && $s_id && $loc_id) {

                $sql = "SELECT b_date, b_time, b_service_id, b_service_duration, b_service_break_time, b_customer_number, b_services_extra, b_loc_id
                                FROM {$wpdb->prefix}fat_sb_booking 
                                WHERE  b_employee_id={$e_id} AND b_id != {$b_id}  AND b_process_status IN (0,1) AND b_date > %s";

                $sql = $wpdb->prepare($sql, $now);
                $bookings = $wpdb->get_results($sql);

                $sql = "SELECT e_break_times, e_day_off, e_schedules
                                FROM {$wpdb->prefix}fat_sb_employees
                                WHERE e_id=%d";
                $sql = $wpdb->prepare($sql, $e_id);
                $employee = $wpdb->get_results($sql);

                if (count($employee) > 0) {
                    $employee = $employee[0];
                    $employee->e_break_times = unserialize($employee->e_break_times);
                    $employee->e_day_off = unserialize($employee->e_day_off);
                    $employee->e_schedules = unserialize($employee->e_schedules);

                    $sql = "SELECT SE.s_id, SE.s_price, SE.s_max_cap, SE.s_min_cap, S.s_tax
                                                                FROM {$wpdb->prefix}fat_sb_services_employee AS SE
                                                                LEFT JOIN {$wpdb->prefix}fat_sb_services AS S
                                                                ON SE.s_id = S.s_id
                                                                WHERE SE.e_id=%d";
                    $sql = $wpdb->prepare($sql, $e_id);
                    $employee->e_services = $wpdb->get_results($sql);
                    $s_ids = '';
                    foreach ($employee->e_services as $service) {
                        $s_ids .= $s_ids ? ',' . $service->s_id : $service->s_id;
                    }
                    for ($es_index = 0; $es_index < count($employee->e_schedules); $es_index++) {
                        if ($employee->e_schedules[$es_index]['es_enable'] == '1' && isset($employee->e_schedules[$es_index]['work_hours'])) {
                            for ($ew_index = 0; $ew_index < count($employee->e_schedules[$es_index]['work_hours']); $ew_index++) {
                                if (!isset($employee->e_schedules[$es_index]['work_hours'][$ew_index]['s_id']) || $employee->e_schedules[$es_index]['work_hours'][$ew_index]['s_id'] == '0') {
                                    $employee->e_schedules[$es_index]['work_hours'][$ew_index]['s_id'] = $s_ids;
                                } else {
                                    $employee->e_schedules[$es_index]['es_enable'] = is_array($employee->e_schedules[$es_index]['work_hours'][$ew_index]['s_id']) &&  in_array($s_id, $employee->e_schedules[$es_index]['work_hours'][$ew_index]['s_id']) ? 1 : 0;
                                }
                                if ($employee->e_schedules[$es_index]['es_enable'] == '1') {
                                    break;
                                }
                            }
                        }
                    }
                }

                return array(
                    'result' => 1,
                    'bookings' => $bookings,
                    'employee' => $employee
                );
            } else {
                return array(
                    'result' => -1
                );
            }
        }

        public function save_booking()
        {
            $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';
            if ($data) {
                global $wpdb;
                $booking = array();

                //service
                $sql = "SELECT S.s_id, SE.s_price, S.s_name, S.s_tax, S.s_duration, S.s_break_time, S.s_category_id
                                FROM {$wpdb->prefix}fat_sb_services AS S
                                INNER JOIN {$wpdb->prefix}fat_sb_services_employee AS SE
                                ON SE.s_id = S.s_id
                                WHERE SE.s_id=%d AND SE.e_id=%d";
                $sql = $wpdb->prepare($sql, $data['b_service_id'], $data['b_employee_id']);
                $service = $wpdb->get_results($sql);

                $tax = 0;
                $sub_total = 0;
                $b_id = isset($_REQUEST['b_id']) && $_REQUEST['b_id'] ? $_REQUEST['b_id'] : 0;
                $b_duration = 0;
                $quantity = isset($data['b_customer_number']) && $data['b_customer_number'] ? $data['b_customer_number'] : 0;
                $booking['b_customer_id'] = isset($data['b_customer_id']) ? $data['b_customer_id'] : 0;
                $booking['b_customer_number'] = $quantity;
                $booking['b_loc_id'] = isset($data['b_loc_id']) ? $data['b_loc_id'] : 0;
                $booking['b_employee_id'] = isset($data['b_employee_id']) ? $data['b_employee_id'] : '';
                $booking['b_time'] = isset($data['b_time']) ? $data['b_time'] : 0;
                $booking['b_date'] = isset($data['b_date']) ? $data['b_date'] : '';
                $booking['b_pay_now'] = isset($data['b_pay_now']) ? $data['b_pay_now'] : 0;
                $booking['b_send_notify'] = isset($data['b_send_notify']) ? $data['b_send_notify'] : 0;

                if (isset($service[0])) {
                    $service = $service[0];
                    $booking['b_service_id'] = $service->s_id;
                    $booking['b_service_cat_id'] = $service->s_category_id;
                    $booking['b_service_break_time'] = $service->s_break_time;
                    $b_duration = $service->s_duration;
                    $booking['b_service_duration'] = $b_duration;
                    $booking['b_price'] = $service->s_price;
                    $booking['b_service_tax'] = $service->s_tax;
                    $sub_total = $service->s_price * $quantity;
                    $sub_total = apply_filters('fat_sb_sub_total_save_booking', $sub_total, $quantity, $service->s_price, $service->s_id);
                    $tax = $sub_total * ($service->s_tax / 100);
                    $booking['b_service_tax_amount'] = $tax;
                } else {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('Data invalid', 'fat-services-booking')
                    );
                }

                //get booking info for update
                $ignore_validate_time_slot = false;
                if ($b_id) {
                    $sql = "SELECT b_id
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        WHERE b_id=%d AND b_gateway_status=1 AND b_gateway_type != 'onsite' ";
                    $sql = $wpdb->prepare($sql, $b_id);
                    $booking_payment = $wpdb->get_results($sql);
                    if (count($booking_payment) > 0) {
                        return array(
                            'result' => -1,
                            'message' => esc_html__('You cannot update booking that made the paypal or stripe payment', 'fat-services-booking')
                        );
                    }

                    $sql = "SELECT b_date, b_time, b_service_id, b_employee_id
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        WHERE b_id=%d AND b_date=%s AND b_time=%s AND b_service_id=%d AND b_employee_id=%d";
                    $sql = $wpdb->prepare($sql, $b_id, $booking['b_date'], $booking['b_time'], $booking['b_service_id'], $booking['b_employee_id']);
                    $booking_info = $wpdb->get_results($sql);

                    $ignore_validate_time_slot = count($booking_info) ? true : false;
                }
                //validate
                if (!$ignore_validate_time_slot) {
                    $is_valid_time_slot = $this->validate_booking_slot($b_id, $booking['b_employee_id'], $booking['b_service_id'], $booking['b_service_duration'], $booking['b_loc_id'], $booking['b_date'], $booking['b_time'], $quantity);
                    if (!$is_valid_time_slot['valid']) {
                        return array(
                            'result' => -1,
                            'message' => $is_valid_time_slot['message']
                        );
                    }
                }

                //service extra
                $extra_price = 0;
                $extra_tax = 0;
                if (isset($data['b_services_extra']) && $data['b_services_extra']) {
                    $sql = "SELECT se_id, se_price, se_tax, se_duration
                                FROM {$wpdb->prefix}fat_sb_services_extra
                                WHERE 1=%d AND se_id IN ({$data['b_services_extra']})";
                    $sql = $wpdb->prepare($sql, 1);
                    $services_extra = $wpdb->get_results($sql);
                    $booking['b_services_extra'] = $data['b_services_extra'];
                    foreach ($services_extra as $se) {
                        $extra_price += ($se->se_price * $quantity);
                        $extra_tax += $extra_price * ($se->se_tax / 100);
                        /*$b_duration += $se->se_duration;*/
                    }

                } else {
                    $booking['b_services_extra'] = '';
                }
                $booking['b_total_extra_amount'] = $extra_price;
                $booking['b_total_tax_extra'] = $extra_tax;
                $booking['b_service_duration'] = $b_duration;

                //coupon
                $booking['b_coupon_code'] = isset($data['b_coupon_code']) ? $data['b_coupon_code'] : '';
                $coupon = FAT_SB_Utils::getCoupon($booking['b_coupon_code'], $booking['b_service_id']);
                $discount = 0;
                $discount_type = '';
                if (isset($coupon['result']) && $coupon['result'] > 0 && isset($coupon['discount_type'])) {
                    $booking['b_discount'] = $coupon['amount'];
                    $discount_type = $coupon['discount_type'];
                    $booking['b_coupon_id'] = $coupon['coupon_id'];
                }

                $booking['b_total_amount'] = $sub_total + $tax + $extra_price + $extra_tax;
                $booking['b_total_amount'] = floatval($booking['b_total_amount']);
                if ($discount_type == '1') { //percent
                    $discount = ($booking['b_total_amount'] * $booking['b_discount']) / 100;
                    $discount = number_format($discount, 2);
                } else {
                    $discount = isset($booking['b_discount']) ? $booking['b_discount'] : 0;
                }
                $discount = floatval($discount);

                $booking['b_total_pay'] = $booking['b_total_amount'] - $discount;
                $booking['b_gateway_type'] = isset($data['b_gateway_type']) ? $data['b_gateway_type'] : 'onsite';
                $booking['b_gateway_status'] = $booking['b_pay_now'];
                $booking['b_description'] = isset($data['b_description']) ? $data['b_description'] : '';

                if ($b_id) {
                    //update booking
                    do_action('fat_before_update_booking', $booking);

                    $sql = "SELECT b_id
                                FROM {$wpdb->prefix}fat_sb_booking
                                WHERE b_id=%d AND b_date={$booking['b_date']} AND b_time={$booking['b_time']}";
                    $sql = $wpdb->prepare($sql, $b_id);
                    $is_send_mail = $wpdb->get_results($sql);

                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_booking', $booking, array('b_id' => $b_id));

                    do_action('fat_after_update_booking', $result, $booking);

                    return array(
                        'result' => $result >= 0 ? $b_id : $result,  //return 0 when don't have change for booking
                        'send_mail' => count($is_send_mail) > 0 ? 0 : 1
                    );
                } else {
                    $db_setting = FAT_DB_Setting::instance();
                    $setting = $db_setting->get_setting();
                    $booking['b_create_date'] = current_time('mysql', 0);
                    $booking['b_process_status'] = isset($setting['b_process_status']) ? $setting['b_process_status'] : 0;
                    $booking['b_send_notify'] = 0;
                    do_action('fat_before_add_booking', $booking);
                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_booking', $booking);
                    $result = $result > 0 ? $wpdb->insert_id : $result;
                    $wpdb->update($wpdb->prefix . 'fat_sb_customers', array('c_last_booking' => $booking['b_date']), array('c_id' => $booking['b_customer_id']));
                    do_action('fat_after_add_booking', $result, $booking);
                    return array(
                        'result' => $result,
                        'send_mail' => 1
                    );
                }

            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data invalid', 'fat-services-booking')
                );
            }
        }

        public function save_booking_fe()
        {
            $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';
            $validate = 1;
            $validate = apply_filters('fat_sb_booking_validate', $validate, $data);
            if (is_array($validate) && isset($validate['result']) && $validate['result'] == -1) {
                return array(
                    'result' => -1,
                    'message' => isset($validate['message']) ? $validate['message'] : esc_html__('You are not on the list of allowed create appointment', 'fat-services-booking')
                );
            }

            $booking_id = 0;
            if ($data) {
                global $wpdb;

                //service
                $sql = "SELECT S.s_id, SE.s_price, S.s_name, S.s_tax, S.s_duration, S.s_break_time, S.s_category_id
                                FROM {$wpdb->prefix}fat_sb_services AS S
                                INNER JOIN {$wpdb->prefix}fat_sb_services_employee AS SE
                                ON SE.s_id = S.s_id
                                WHERE SE.s_id=%d AND SE.e_id=%d";
                $sql = $wpdb->prepare($sql, $data['b_service_id'], $data['b_employee_id']);
                $service = $wpdb->get_results($sql);


                $multiple_days = isset($data['multiple_days']) && $data['multiple_days'] ? $data['multiple_days'] : array();

                if(!is_array($multiple_days) || count($multiple_days)==0){
                    $date_format = get_option('date_format');
                    $time_format = get_option('time_format');
                    $start = DateTime::createFromFormat('Y-m-d H:i:s', $data['b_date'] . ' 00:00:00');
                    $start->modify("+{$data['b_time']} minutes");
                    $end = clone $start;
                    $duration = isset($service[0]->s_duration) ? $service[0]->s_duration : 0;
                    $end->modify("+{$duration} minutes");
                    $date_i18n = date_i18n($date_format, $start->format('U'));
                    $time_label = date_i18n($time_format, $start->format('U')). ' - '.date_i18n($time_format, $end->format('U'));
                    $multiple_days = array();
                    $multiple_days[] = array(
                        'date' => $data['b_date'],
                        'date_i18n' => $date_i18n,
                        'time' => $data['b_time'],
                        'time_label' => $time_label
                    );
                }

                $booking = array();

                $setting_db = FAT_DB_Setting::instance();
                $setting = $setting_db->get_setting();
                $day_limit = isset($setting['day_limit']) && $setting['day_limit'] ? $setting['day_limit'] : 365;

                //customer
                $c_first_name = isset($data['c_first_name']) ? $data['c_first_name'] : '';
                $c_last_name = isset($data['c_last_name']) ? $data['c_last_name'] : '';
                $c_email = isset($data['c_email']) ? $data['c_email'] : '';
                $c_phone = isset($data['c_phone']) ? $data['c_phone'] : '';
                $c_phone_code = isset($data['c_phone_code']) ? $data['c_phone_code'] : '';

                if ($c_first_name == '' || $c_last_name == '' || $c_email == '') {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('Please fill data for first name, last name and email', 'fat-services-booking')
                    );
                }

                $sql = "SELECT c_id, c_email, c_user_id FROM {$wpdb->prefix}fat_sb_customers WHERE c_email=%s";
                $sql = $wpdb->prepare($sql, $c_email);
                $customer = $wpdb->get_results($sql);
                $c_user_id = get_current_user_id();

                if (count($customer) > 0) {
                    $booking['b_customer_id'] = $customer[0]->c_id;
                    if($c_user_id && (is_null($customer[0]->c_user_id) || !$customer[0]->c_user_id)){
                        $wpdb->update($wpdb->prefix . 'fat_sb_customers', array('c_user_id' => $c_user_id, 'c_phone' => $c_phone,'c_phone_code' => $c_phone_code), array('c_id' => $customer[0]->c_id));
                    }else{
                        $wpdb->update($wpdb->prefix . 'fat_sb_customers', array('c_phone' => $c_phone,'c_phone_code' => $c_phone_code), array('c_id' => $customer[0]->c_id));
                    }
                } else {
                    $c_dob = new DateTime();
                    $c_code = uniqid('fat_sb_');
                    $result_add_customer = $wpdb->insert($wpdb->prefix . 'fat_sb_customers', array(
                        'c_first_name' => $c_first_name,
                        'c_last_name' => $c_last_name,
                        'c_email' => $c_email,
                        'c_gender' => 0,
                        'c_phone' => $c_phone,
                        'c_phone_code' => $c_phone_code,
                        'c_dob' => $c_dob->modify('-20 years')->format('Y-m-d'),
                        'c_code' => $c_code,
                        'c_user_id' => $c_user_id
                    ));
                    $booking['b_customer_id'] = $result_add_customer > 0 ? $wpdb->insert_id : $result_add_customer;
                }
                if (!isset($booking['b_customer_id']) || $booking['b_customer_id'] <= 0) {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('Cannot add customer information, please contact site admin for this error', 'fat-services-booking')
                    );
                }


                $tax = 0;
                $sub_total = 0;
                $b_id = 0;
                $quantity = isset($data['b_customer_number']) && $data['b_customer_number'] ? $data['b_customer_number'] : 0;
                $booking['b_customer_number'] = $quantity;
                $booking['b_loc_id'] = isset($data['b_loc_id']) ? $data['b_loc_id'] : 0;
                $booking['b_employee_id'] = isset($data['b_employee_id']) ? $data['b_employee_id'] : '';
                $booking['b_date'] = $multiple_days[0]['date'];
                $booking['b_time'] = $multiple_days[0]['time'];
                $booking['b_pay_now'] = 0;
                $booking['b_send_notify'] = 0;
                if (isset($_REQUEST['form_builder']) && $_REQUEST['form_builder']) {
                    $booking['b_form_builder'] = json_encode($_REQUEST['form_builder']);
                }

                $service_name = '';
                $b_duration = 0;
                if (isset($service[0])) {
                    $service = $service[0];
                    $service_name = $service->s_name;
                    $booking['b_service_id'] = $service->s_id;
                    $booking['b_service_cat_id'] = $service->s_category_id;
                    $booking['b_service_break_time'] = $service->s_break_time;
                    $b_duration = $service->s_duration;
                    $booking['b_service_duration'] = $b_duration;
                    $booking['b_price'] = $service->s_price;
                    $booking['b_service_tax'] = $service->s_tax;
                    $sub_total = $service->s_price * $quantity;
                    $sub_total = apply_filters('fat_sb_sub_total_save_booking', $sub_total, $quantity, $service->s_price, $service->s_id);
                    $tax = $sub_total * ($service->s_tax / 100);
                    $booking['b_service_tax_amount'] = $tax;
                } else {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('Data invalid', 'fat-services-booking')
                    );
                }


                //validate day limit
                foreach ($multiple_days as $md){
                    $b_date = DateTime::createFromFormat('Y-m-d H:i', $md['date'] . ' 00:00');
                    $now = current_time('mysql', 0);
                    $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
                    if ($b_date->diff($now)->days > $day_limit) {
                        return array(
                            'result' => -1,
                            'message' => sprintf(esc_html__('You cannot book service before %s days', 'fat-services-booking'), $day_limit)
                        );
                    }
                    $b_date_time = DateTime::createFromFormat('Y-m-d H:i', $md['date'] . ' 00:00');
                    $b_date_time = $b_date_time->modify('+' . $md['time'] . ' minutes');
                    if ($now >= $b_date_time) {
                        return array(
                            'result' => -1,
                            'message' => esc_html__('You cannot set time in the past', 'fat-services-booking')
                        );
                    }
                }

                //validate
                $is_valid_time_slot = array('valid' => true);
                $day_invalid = '';
                $invalid_message = '';
                $debug = '';
                $sc = [];
                foreach ($multiple_days as $md){
                    $is_valid_time_slot = $this->validate_booking_slot($b_id, $booking['b_employee_id'], $booking['b_service_id'], $booking['b_service_duration'], $booking['b_loc_id'],
                        $md['date'], $md['time'], $quantity);
                    if(!$is_valid_time_slot['valid']){
                        $day_invalid .= '</br>'.$md['date_i18n']. ' '.$md['time_label'];
                        $invalid_message = $is_valid_time_slot['message'];
                        $debug .= isset($is_valid_time_slot['debug']) ? $is_valid_time_slot['debug'] : '';
                        $sc = isset($is_valid_time_slot['schedule']) ? $is_valid_time_slot['schedule'] : '';
                    }
                }

                if ($invalid_message) {
                    return array(
                        'result' => -1,
                        'message' => count($multiple_days)==1 ? $invalid_message : ($invalid_message. $day_invalid),
                        'debug' => $debug,
                        'schedule' => $sc
                    );
                }

                //service extra
                $extra_price = 0;
                $extra_tax = 0;
                if (isset($data['b_services_extra']) && $data['b_services_extra']) {
                    $sql = "SELECT se_id, se_price, se_tax, se_duration, se_multiple_book, se_price_on_total
                                FROM {$wpdb->prefix}fat_sb_services_extra
                                WHERE 1=%d AND se_id IN ({$data['b_services_extra']})";
                    $sql = $wpdb->prepare($sql, 1);
                    $services_extra = $wpdb->get_results($sql);
                    $booking['b_services_extra'] = $data['b_services_extra'];

                    $se_disable_multiple = array();
                    foreach ($services_extra as $se) {
                        if($se->se_price_on_total==1){
                            $extra_price += $se->se_price;
                        }else{
                            $extra_price += ($se->se_price * $quantity);
                        }
                        $extra_tax += $extra_price * ($se->se_tax / 100);
                       /* $b_duration += $se->se_duration;*/
                        if ($se->se_multiple_book == '0') {
                            $se_disable_multiple[] = $se->se_id;
                        }
                    }

                    //validate multiple book for service extra
                    if (count($se_disable_multiple) > 0) {
                        $se_disable_multiple = implode(',', $se_disable_multiple);

                        foreach ($multiple_days as $md){
                            $sql = "SELECT SE.se_name 
                                FROM {$wpdb->prefix}fat_sb_booking AS B
                                LEFT JOIN {$wpdb->prefix}fat_sb_services_extra AS SE
                                ON B.b_services_extra = SE.se_id
                                WHERE b_date=%s AND b_time <= %d AND %d <= (b_time + b_service_duration)
                                AND b_services_extra IN ({$se_disable_multiple})
                                GROUP BY SE.se_name";
                            $sql = $wpdb->prepare($sql, $md['date'], $md['time'], $md['time']);
                            $se_limit = $wpdb->get_results($sql);
                            if (is_countable($se_limit) && count($se_limit) > 0) {
                                $se_name_limit = array();
                                foreach ($se_limit as $se) {
                                    $se_name_limit[] = $se->se_name;
                                }
                                $se_name_limit = implode(', ', $se_name_limit) . ' ' . esc_html__('not available during this time', 'fat-services-booking');
                                return array(
                                    'result' => -1,
                                    'message' => $se_name_limit
                                );
                            }
                        }

                    }

                } else {
                    $booking['b_services_extra'] = '';
                }
                $booking['b_total_extra_amount'] = $extra_price;
                $booking['b_total_tax_extra'] = $extra_tax;
                $booking['b_service_duration'] =  $b_duration;

                //coupon
                $booking['b_coupon_code'] = isset($data['b_coupon_code']) ? $data['b_coupon_code'] : '';
                $coupon = FAT_SB_Utils::getCoupon($booking['b_coupon_code'], $booking['b_service_id']);
                $discount = 0;
                $discount_type = '';
                if (isset($coupon['result']) && $coupon['result'] > 0 && isset($coupon['discount_type'])) {
                    $booking['b_discount'] = $coupon['amount'];
                    $discount_type = $coupon['discount_type'];
                    $booking['b_coupon_id'] = $coupon['coupon_id'];
                }

                $booking['b_total_amount'] = $sub_total + $tax + $extra_price + $extra_tax;
                $booking['b_total_amount'] = floatval($booking['b_total_amount']);
                $discount = 0;
                if ($discount_type == '1') { //percent
                    $discount = ($booking['b_total_amount'] * $booking['b_discount']) / 100;
                    $discount = number_format($discount, 2);
                } else {
                    $discount = isset($booking['b_discount']) ? $booking['b_discount'] : 0;
                }
                $discount = floatval($discount);

                $booking['b_total_pay'] = $booking['b_total_amount'] > $discount ? ($booking['b_total_amount'] - $discount) : 0;
                $booking['b_gateway_type'] = isset($data['b_gateway_type']) ? $data['b_gateway_type'] : 'onsite';
                $booking['b_gateway_status'] = 0;
                $booking['b_description'] = isset($data['b_description']) ? $data['b_description'] : '';

                $db_setting = FAT_DB_Setting::instance();
                $setting = $db_setting->get_setting();
                $booking['b_process_status'] = isset($setting['b_process_status']) ? $setting['b_process_status'] : 0;
                $booking['b_create_date'] = current_time('mysql', 0);

                do_action('fat_before_add_booking', $booking);

                if ($booking['b_total_pay'] > 0 && ($booking['b_gateway_type'] === 'myPOS' || $booking['b_gateway_type'] === 'stripe'
                    || $booking['b_gateway_type'] === 'paypal' || $booking['b_gateway_type'] === 'przelewy24' || $booking['b_gateway_type'] === 'price-package' ) ){
                    //temporary for payment gateway
                    $booking['b_process_status'] = -1;
                }

                $result = $wpdb->insert($wpdb->prefix . 'fat_sb_booking', $booking);
                $booking_id = $result > 0 ? $wpdb->insert_id : $result;

                //add for multiple day
                for($i=1; $i < count($multiple_days); $i++){
                    $booking['b_date'] = $multiple_days[$i]['date'];
                    $booking['b_time'] = $multiple_days[$i]['time'];
                    $result_md = $wpdb->insert($wpdb->prefix . 'fat_sb_booking', $booking);
                    $b_detail_id = $result_md > 0 ? $wpdb->insert_id : $result;
                    if($b_detail_id){
                        $wpdb->insert($wpdb->prefix . 'fat_sb_booking_multiple_days', array(
                            'b_id' => $booking_id,
                            'b_detail_id' => $b_detail_id,
                            'b_date' => $multiple_days[$i]['date'],
                            'b_datei18n' => $multiple_days[$i]['date_i18n'],
                            'b_time' => $multiple_days[$i]['time'],
                            'b_time_label' => $multiple_days[$i]['time_label'],
                        ));
                    }
                }

                if ($c_phone) {
                    $wpdb->update($wpdb->prefix . 'fat_sb_customers', array('c_last_booking' => $booking['b_date'], 'c_phone' => $c_phone, 'c_phone_code' => $c_phone_code), array('c_id' => $booking['b_customer_id']));
                } else {
                    $wpdb->update($wpdb->prefix . 'fat_sb_customers', array('c_last_booking' => $booking['b_date']), array('c_id' => $booking['b_customer_id']));
                }

                do_action('fat_after_add_booking', $booking_id, $booking);

                $approve_url = '';
                if ($booking_id > 0 && $booking['b_gateway_type'] === 'price-package') {
                    $current_user = wp_get_current_user();

                    /*if(!is_user_logged_in()){
                        return array(
                            'result' => -1,
                            'message' => esc_html__('Please login before pay via price package', 'fat-sb-booking')
                        );
                    }*/

                    $user_package_amount = FAT_DB_Price_Package::get_price_amount_by_user($c_email);
                    $pk_price_for_payment = $user_package_amount['buy_amount'];
                    if ($pk_price_for_payment > 0 && $user_package_amount['has_order'] == 1) {
                        $has_payment = $user_package_amount['has_payment'];
                        if ($user_package_amount['has_order'] == 1 && ($pk_price_for_payment - $has_payment) < $booking['b_total_amount']) {
                            return array(
                                'result' => -1,
                                'message' => esc_html__('You do not have enough money to pay, please buy more packages', 'fat-sb-booking')
                            );
                        }
                        $user_payment_info = array();
                        $user_payment_info['b_id'] = $booking_id;
                        $user_payment_info['s_id'] = $booking['b_service_id'];
                        $user_payment_info['u_id'] = isset($current_user->ID) ? $current_user->ID : 0;
                        $user_payment_info['u_email'] = $c_email;
                        $user_payment_info['upk_payment_amount'] = $booking['b_total_amount'];
                        $user_payment_info['pk_create_date'] = current_time('mysql', 0);
                        $result = $wpdb->insert($wpdb->prefix . 'fat_sb_user_payment_by_package', $user_payment_info);
                        if ($result > 0) {
                            $booking['b_process_status'] = isset($setting['b_process_status']) ? $setting['b_process_status'] : 0;
                            $wpdb->update($wpdb->prefix . 'fat_sb_booking', array('b_pay_now' => 1, 'b_process_status' => $booking['b_process_status'], 'b_gateway_status' => 1), array('b_id' => $booking_id));
                            do_action('fat_sb_booking_completed',$booking_id);
                        }
                        return array(
                            'result' => $booking_id,
                            'message' => $result > 0 ? '' : esc_html__('An error occurred while executing', 'fat-sb-booking'),
                            'remain_credit' => ($pk_price_for_payment - $has_payment - $booking['b_total_amount'])
                        );

                    } else {
                        return array(
                            'result' => -1,
                            'message' => esc_html__('You need buy package before payment via price package', 'fat-sb-booking')
                        );
                    }

                }

                if ($booking_id > 0 && $booking['b_gateway_type'] === 'paypal') {
                    $payment_desc = esc_html__('Customer:', 'fat-services-booking') . $c_first_name . ' ' . $c_last_name;
                    $payment_desc .= esc_html__('Service:', 'fat-services-booking') . $service_name;
                    $time = '';
                    foreach ($multiple_days as $md){
                        $time .= $md['date_i18n'].' '.$md['time_label'].' , ';
                    }
                    $payment_desc .= esc_html__('Time:', 'fat-services-booking') . $time;
                    $url = esc_url(home_url());
                    $total_pay = $booking['b_total_pay'] * count($multiple_days);
                    $customer = $c_first_name . ' ' . $c_last_name . '(' . $c_email . ')';
                    if ($total_pay > 0) {
                        $payment = new FAT_Payment();
                        $payment_result = $payment->payment($booking_id, $customer, $service_name, $booking['b_service_id'], 1, $total_pay, 0, $total_pay, $setting['currency'], $payment_desc, $url);
                        if ($payment_result['result'] == -1) {
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE b_id = %d";
                            $sql = $wpdb->prepare($sql, $booking_id);
                            $wpdb->query($sql);
                            return array(
                                'result' => -1,
                                'message' => $payment_result['message']
                            );
                        } else {
                            $approve_url = $payment_result['approval_url'];
                        }
                    } else {
                        return array(
                            'result' => $booking_id,
                        );
                    }
                }

                if ($booking_id > 0 && $booking['b_gateway_type'] === 'myPOS') {
                    $total_pay = $booking['b_total_pay'] * count($multiple_days);
                    if ($total_pay > 0) {
                        $payment = new FAT_Payment();
                        $payment_result = $payment->myPOS_payment($c_first_name, $c_last_name, $c_email, $c_phone, '', $setting['currency'], $booking_id, 1, $total_pay, $service_name);
                        if ($payment_result['result'] == -1) {
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE b_id = %d";
                            $sql = $wpdb->prepare($sql, $booking_id);
                            $wpdb->query($sql);
                        }
                        return $payment_result;
                    }else {
                        $success_url = isset($setting['przelewy24_success_page']) ? $setting['przelewy24_success_page'] : '';
                        $success_url = $success_url ? get_permalink($success_url) : home_url('/');
                        return array(
                            'result' => $booking_id,
                            'redirect_url' => $success_url
                        );
                    }

                }

                if ($booking_id > 0 && $booking['b_gateway_type'] === 'stripe') {
                    $booking['b_total_pay'] = $booking['b_total_pay'] * count($multiple_days);
                    if ($booking['b_total_pay'] > 0) {
                        $currency = $setting_db->get_currency_setting();
                        $description = esc_html__('Customer:', 'fat-services-booking') . $c_first_name . ' ' . $c_last_name;
                        $description .= esc_html__('. Service:', 'fat-services-booking') . $service_name;
                        $time = '';
                        foreach ($multiple_days as $md){
                            $time .= $md['date_i18n'].' '.$md['time_label'].' , ';
                        }
                        $description .= esc_html__('. Time:', 'fat-services-booking') . $time;
                        $description .= esc_html__('.  Total fees: ', 'fat-event') . $booking['b_total_pay'] . $currency['symbol'];
                        $payment = new FAT_Payment();
                        $result = $payment->stripe_payment($booking_id, $booking['b_total_pay'], $description);
                        if ($result['code'] < 0) {
                            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE b_id = %d";
                            $sql = $wpdb->prepare($sql, $booking_id);
                            $wpdb->query($sql);
                        }else{
                            do_action('fat_sb_booking_completed',$booking);
                        }
                        return $result;
                    }else{
                        return array(
                            'result' => $booking_id,
                        );
                    }
                }

                if ($booking_id > 0 && $booking['b_gateway_type'] === 'przelewy24') {
                    $total_pay = floatval($booking['b_total_pay']) * 100;
                    $total_pay = $total_pay * count($multiple_days);

                    $sql = "SELECT b_detail_id FROM {$wpdb->prefix}fat_sb_booking_multiple_days WHERE b_id=%d";
                    $sql = $wpdb->prepare($sql, $booking_id);
                    $booking_md = $wpdb->get_results($sql);
                    $b_ids = array($booking_id);
                    foreach($booking_md as $bmd){
                        $b_ids[] = $bmd->b_detail_id;
                    }
                    $b_ids = implode(',',$b_ids);

                    if ($total_pay == 0) {
                        $sql = "UPDATE {$wpdb->prefix}fat_sb_booking SET b_status_note=1, b_process_status=1 WHERE b_id IN ({$b_ids})";
                        $wpdb->query($sql);
                        $success_url = isset($setting['przelewy24_success_page']) ? $setting['przelewy24_success_page'] : '';
                        $success_url = $success_url ? get_permalink($success_url) : home_url('/');
                        return array(
                            'result' => $booking_id,
                            'redirect_url' => $success_url
                        );
                    }

                    $p24_merchant_id = isset($setting['p24_merchant_id']) ? $setting['p24_merchant_id'] : '';
                    $p24_pos_id = isset($setting['p24_pos_id']) ? $setting['p24_pos_id'] : '';
                    $p24_mode = isset($setting['p24_mode']) ? $setting['p24_mode'] : 'sandbox';
                    $currency = $setting_db->get_currency_setting();
                    $currency = isset($currency['currency']) ? $currency['currency'] : 'PLN';
                    $p24_crc = isset($setting['p24_crc']) ? $setting['p24_crc'] : '';

                    $p24_session_id = uniqid();
                    $p24_sign = $p24_session_id . '|' . $p24_merchant_id . '|' . $total_pay . '|' . $currency . '|' . $p24_crc;
                    $p24_sign = md5($p24_sign);

                    $description = esc_html__('Customer:', 'fat-services-booking') . $c_first_name . ' ' . $c_last_name;
                    $description .= esc_html__('. Service:', 'fat-services-booking') . $service_name;
                    $description .= esc_html__('. Time:', 'fat-services-booking') . $booking['b_date'] . ' ' . $booking['b_time'];
                    $description .= esc_html__('.  Total fees: ', 'fat-event') . $total_pay . $currency;

                    $p24_url_return = home_url('/');
                    $p24_url_return = add_query_arg(array(
                        'source' => 'fat_sb_booking_p24',
                        'action' => 'p24_return',
                        'bid' => $booking_id,
                        'session_id' => $p24_session_id,
                        'merchant_id' => $p24_merchant_id,
                        'total' => $total_pay,
                        'currency' => $currency), $p24_url_return
                    );
                    $p24_url_status = home_url('/');
                    $p24_url_status = add_query_arg(array(
                        'source' => 'fat_sb_booking_p24',
                        'action' => 'p24_status',
                        'bid' => $booking_id,
                        'session_id' => $p24_session_id,
                        'merchant_id' => $p24_merchant_id,
                        'total' => $total_pay,
                        'currency' => $currency), $p24_url_status
                    );

                    $postArgs = array(
                        'p24_client' => $c_first_name . ' ' . $c_last_name,
                        'p24_session_id' => $p24_session_id,
                        'p24_merchant_id' => $p24_merchant_id,
                        'p24_pos_id' => $p24_pos_id,
                        'p24_amount' => $total_pay,
                        'p24_currency' => $currency,
                        'p24_description' => esc_html__('rezerwacja online ', 'fat-services-booking') . $booking_id,
                        'p24_email' => $c_email,
                        'p24_country' => 'PL',
                        'p24_url_return' => $p24_url_return,
                        'p24_url_status' => $p24_url_status,
                        'p24_api_version' => '3.2',
                        'p24_sign' => $p24_sign
                    );

                    $note = json_encode($postArgs);
                    $sql = "UPDATE {$wpdb->prefix}fat_sb_booking SET b_status_note={$note} WHERE b_id IN ({$b_ids})";
                    $wpdb->query($sql);

                    $p24_register_url = $p24_mode == 'sandbox' ? 'https://sandbox.przelewy24.pl/trnRegister' : 'https://secure.przelewy24.pl/trnRegister';
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
                        $response = explode('&', $response);
                        $result = $response[0];
                        $result = explode('=', $result);
                        if ($result[1] == '0') {
                            $token = explode('=', $response[1])[1];
                            $wpdb->update($wpdb->prefix . 'fat_sb_booking', array('b_gateway_response' => $token), array('b_id' => $booking_id));
                            $p24_request_url = $p24_mode == 'sandbox' ? 'https://sandbox.przelewy24.pl/trnRequest/' : 'https://secure.przelewy24.pl/trnRequest/';
                            return array(
                                'result' => 1,
                                'redirect_url' => $p24_request_url . $token
                            );
                        }
                    }

                    //delete booking if have error
                    $sql = "DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE b_id IN ({$b_ids})";
                    $wpdb->query($sql);
                    return array(
                        'code' => -1,
                        'message' => esc_html__('An error occurred during execution', 'fat-services-booking')
                    );
                }

                if($booking_id && $booking['b_gateway_type'] === 'onsite'){
                    do_action('fat_sb_booking_completed', $booking_id);
                }

                $result = array(
                    'result' => $booking_id,
                    'redirect_url' => isset($approve_url) ? $approve_url : ''
                );

                $result = apply_filters('fat_service_payment_booking', $result, $booking_id, $booking);

                return $result;

            } else {
                if ($booking_id) {
                    global $wpdb;
                    $sql = "DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE b_id = %d";
                    $sql = $wpdb->prepare($sql, $booking_id);
                    $wpdb->query($sql);
                }
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data invalid', 'fat-services-booking')
                );
            }
        }

        public function delete_booking()
        {
            $b_ids = isset($_REQUEST['b_ids']) && $_REQUEST['b_ids'] != '' ? $_REQUEST['b_ids'] : '';
            if ($b_ids) {
                global $wpdb;
                $b_ids = implode(',', $b_ids);

                $sql = "SELECT b_id FROM {$wpdb->prefix}fat_sb_booking WHERE b_id IN ({$b_ids})";
                error_log($sql);
                $b_is_delete = $wpdb->get_results($sql);

                $b_not_delete = 0;

                $b_delete_ids = array();
                foreach ($b_is_delete as $b_id) {
                    $b_delete_ids[] = $b_id->b_id;
                }

                if (count($b_delete_ids) > 0) {
                    $b_delete_ids = implode(',', $b_delete_ids);
                    $sql = "DELETE FROM {$wpdb->prefix}fat_sb_booking WHERE  b_id IN ({$b_delete_ids}) ";
                    $result = $wpdb->query($sql);
                    return array(
                        'result' => 1,
                        'ids_delete' => explode(',', $b_delete_ids),
                        'message_success' => $result > 0 ? $result . esc_html__(' booking(s) have been deleted', 'fat-services-booking') : esc_html__('Can not find booking, it may have been deleted by another user ', 'fat-services-booking'),
                        'message_error' => $b_not_delete && $result > 0 ? sprintf(esc_html__('Cannot delete %s booking(s) that made the payment', 'fat-services-booking'), $b_not_delete) : ''
                    );
                } else {
                    return array(
                        'result' => 1,
                        'ids_delete' => array(),
                        'message_success' => '',
                        'message_error' => esc_html__('Cannot delete the booking(s) that made the payment', 'fat-services-booking')
                    );
                }

            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }
        }

        public function update_booking_process_status()
        {
            $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : '';
            $b_process_status = isset($_REQUEST['b_process_status']) ? $_REQUEST['b_process_status'] : '';
            $status = array(0, 1, 2, 3);
            if ($b_id && $b_process_status != '' && in_array($b_process_status, $status)) {
                global $wpdb;

                $sql = "SELECT b_id, b_customer_id, b_loc_id, b_employee_id, b_service_cat_id, b_service_id, b_service_duration, b_customer_number, b_date, b_time, b_process_status
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        WHERE b_id=%d";
                $sql = $wpdb->prepare($sql, $b_id);
                $booking = $wpdb->get_results($sql);
                $b_customer_number = 0;
                if (count($booking) == 0) {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('Cannot find this booking.Maybe it have been deleted', 'fat-services-booking')
                    );
                } else {
                    $booking = $booking[0];
                    $b_customer_number = $booking->b_customer_number;
                }

                $is_valid_time_slot = $this->validate_booking_slot($b_id, $booking->b_employee_id, $booking->b_service_id, $booking->b_service_duration,
                    $booking->b_loc_id, $booking->b_date, $booking->b_time, $b_customer_number);

                if (!$is_valid_time_slot['valid']) {
                    return array(
                        'result' => -1,
                        'message' => $is_valid_time_slot['message']
                    );
                }

                do_action('fat_before_update_booking_status', $b_id, $b_process_status);
                $result = $wpdb->update($wpdb->prefix . 'fat_sb_booking', array('b_process_status' => $b_process_status, 'b_send_notify' => 0, 'b_canceled_by_client' => 1),
                    array('b_id' => $b_id));
                do_action('fat_after_update_booking_status', $b_id, $b_process_status);
                return array(
                    'result' => $result,
                    'message' => $result ? esc_html__('Booking status have been updated', 'fat-services-booking') : esc_html__('Cannot find this booking.Maybe it have been deleted', 'fat-services-booking')
                );

            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data invalid', 'fat-services-booking')
                );
            }
        }

        public function send_booking_mail($b_id, $is_fe = 1)
        {
            global $wpdb;
            if ($b_id == '') {
                return;
            }
            $sql = "SELECT b_id, b_services_extra, c_code, c_first_name, c_last_name, c_email, c_phone, e_first_name, e_last_name, e_email, e_phone, s_name, s_link, b_service_duration, 
                                            loc_name, loc_address, loc_link, b_customer_number, b_date, b_time,  b_process_status, b_total_pay, b_send_notify, b_form_builder, b_description,
                                            b_coupon_code, s_description
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        LEFT JOIN {$wpdb->prefix}fat_sb_customers ON b_customer_id = c_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_locations ON b_loc_id = loc_id
                                        WHERE b_id=%d";
            $sql = $wpdb->prepare($sql, $b_id);
            $mail_info = $wpdb->get_results($sql);

            if (count($mail_info) <= 0 || (isset($mail_info[0]->b_send_notify) && $mail_info[0]->b_send_notify == '1')) {
                return;
            }
            $mail_info = $mail_info[0];

            $setting_db = FAT_DB_Setting::instance();
            $setting = $setting_db->get_setting();
            $disable_customer_email = isset($setting['disable_customer_email']) && $setting['disable_customer_email'] == '1' ? 1 : 0;
            $email_templates = $setting_db->get_email_template();
            $template = '';

            if ($mail_info->b_services_extra) {
                $sql = "SELECT se_name FROM {$wpdb->prefix}fat_sb_services_extra WHERE se_id IN ({$mail_info->b_services_extra})";
                $extra_names = $wpdb->get_results($sql);
                $mail_info->b_services_extra = array();
                foreach ($extra_names as $es) {
                    $mail_info->b_services_extra[] = $es->se_name;
                }
                $mail_info->b_services_extra = implode(', ', $mail_info->b_services_extra);
            }

            $pending_key = $is_fe ? 'pending' : 'backend';
            $approved_key = $is_fe ? 'approved' : 'backend';
            foreach ($email_templates as $tmpl) {
                if ($mail_info->b_process_status == 0 && $tmpl['template'] === $pending_key) {
                    $template = $tmpl;
                    break;
                }

                if ($mail_info->b_process_status == 1 && $tmpl['template'] === $approved_key) {
                    $template = $tmpl;
                    break;
                }

                if ($mail_info->b_process_status == 2 && $tmpl['template'] === 'canceled') {
                    $template = $tmpl;
                    break;
                }

                if ($mail_info->b_process_status == 3 && $tmpl['template'] === 'rejected') {
                    $template = $tmpl;
                    break;
                }
            }

            //add date time for multiple days
            $sql = "SELECT b_detail_id, b_datei18n, b_time_label  FROM {$wpdb->prefix}fat_sb_booking_multiple_days WHERE b_id=%d";
            $sql = $wpdb->prepare($sql, $b_id);
            $booking_md = $wpdb->get_results($sql);
            if(is_countable($booking_md) && count($booking_md)>0){
                $mail_info->multiple_date_time = '';
                foreach($booking_md as $bmd){
                    $mail_info->multiple_date_time .= ', '.$bmd->b_datei18n.' '.$bmd->b_time_label;
                }
            }

            $subject = $message = '';
            if (isset($template['customer_enable']) && $template['customer_enable'] && $disable_customer_email == 0) {
                $subject = $template['customer_subject'];
                $message = $template['customer_message'];
                FAT_SB_Utils::makeMailContent($subject, $message, $mail_info, $setting);
                FAT_SB_Utils::sendMail(array(
                    'mailer' => $setting['mailer'],
                    'smtp_host' => $setting['smtp_host'],
                    'smtp_port' => $setting['smtp_port'],
                    'smtp_username' => $setting['smtp_username'],
                    'smtp_password' => $setting['smtp_password'],
                    'encryption' => $setting['smpt_encryption'],
                    'from_name' => $setting['send_from_name'],
                    'from_name_label' => isset($setting['send_from_name_label']) ? $setting['send_from_name_label'] : $setting['send_from_name'],
                    'send_to' => $mail_info->c_email,
                    'cc_email' => $setting['cc_to'],
                    'bcc_email' => $setting['bcc_to'],
                    'subject' => $subject,
                    'message' => $message
                ));
            }

            if (isset($template['employee_enable']) && $template['employee_enable']) {
                $subject = $template['employee_subject'];
                $message = $template['employee_message'];
                FAT_SB_Utils::makeMailContent($subject, $message, $mail_info, $setting);
                FAT_SB_Utils::sendMail(array(
                    'mailer' => $setting['mailer'],
                    'smtp_host' => $setting['smtp_host'],
                    'smtp_port' => $setting['smtp_port'],
                    'smtp_username' => $setting['smtp_username'],
                    'smtp_password' => $setting['smtp_password'],
                    'encryption' => $setting['smpt_encryption'],
                    'from_name' => $setting['send_from_name'],
                    'from_name_label' => isset($setting['send_from_name_label']) ? $setting['send_from_name_label'] : $setting['send_from_name'],
                    'send_to' => $mail_info->e_email,
                    'cc_email' => $setting['cc_to'],
                    'bcc_email' => $setting['bcc_to'],
                    'subject' => $subject,
                    'message' => $message
                ));
            }
        }

        public function send_booking_sms($b_id)
        {
            global $wpdb;
            if ($b_id == '') {
                return;
            }

            $sql = "SELECT b_id, c_first_name, c_last_name, c_email, c_phone_code, c_phone, e_first_name, e_last_name, e_email, e_phone, s_name, b_service_duration, 
                                            loc_name, loc_address, b_customer_number, b_date, b_time,  b_process_status, b_total_pay, b_send_notify, b_form_builder, b_description
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        LEFT JOIN {$wpdb->prefix}fat_sb_customers ON b_customer_id = c_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_locations ON b_loc_id = loc_id
                                        WHERE b_id=%d";
            $sql = $wpdb->prepare($sql, $b_id);
            $booking_info = $wpdb->get_results($sql);

            if (count($booking_info) > 0) {
                $booking_info = $booking_info[0];
                $setting_db = FAT_DB_Setting::instance();
                $sms_templates = $setting_db->get_sms_template();
                $setting = $setting_db->get_setting();
                $template = '';

                foreach ($sms_templates as $tmpl) {
                    if ($booking_info->b_process_status == 0 && $tmpl['template'] === 'pending') {
                        $template = $tmpl;
                        break;
                    }

                    if ($booking_info->b_process_status == 1 && $tmpl['template'] === 'approved') {
                        $template = $tmpl;
                        break;
                    }

                    if ($booking_info->b_process_status == 2 && $tmpl['template'] === 'canceled') {
                        $template = $tmpl;
                        break;
                    }

                    if ($booking_info->b_process_status == 3 && $tmpl['template'] === 'rejected') {
                        $template = $tmpl;
                        break;
                    }
                }

                $customer_message = '';
                if (isset($template['customer_enable']) && $template['customer_enable']) {
                    $customer_message = $template['customer_message'];
                    FAT_SB_Utils::makeSMSContent($customer_message, $booking_info, $setting);

                }

                $employee_message = '';
                if (isset($template['employee_enable']) && $template['employee_enable']) {
                    $employee_message = $template['employee_message'];
                    FAT_SB_Utils::makeSMSContent($employee_message, $booking_info, $setting);
                }

                if ($customer_message || $employee_message) {
                    $booking_info->c_phone_code = explode(',',$booking_info->c_phone_code)[0];
                    $booking_info->c_phone = $booking_info->c_phone_code . $booking_info->c_phone;
                    FAT_SB_Utils::sendSMSForBooking($booking_info->c_phone, $booking_info->e_phone, $customer_message, $employee_message);
                }
            }

        }

        public function export_calendar()
        {
            if (isset($_REQUEST['b_id'])) {
                global $wpdb;
                $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : '';


                $sql = "SELECT b_id,  e_first_name, e_last_name, e_email, e_phone, s_name, b_service_duration, 
                                            loc_name, loc_address, b_date, b_time
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_locations ON b_loc_id = loc_id
                                        WHERE b_id=%d";
                $sql = $wpdb->prepare($sql, $b_id);
                $booking_info = $wpdb->get_results($sql);

                if (count($booking_info) > 0) {
                    $booking_info = $booking_info[0];
                    $u_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $booking_info->b_date . ' 00:00:00');// ($booking_info->b_date;
                    $u_start_date = $u_start_date->modify('+' . $booking_info->b_time . ' minute');
                    $u_end_date = DateTime::createFromFormat('Y-m-d H:i:s', $booking_info->b_date . ' 00:00:00');
                    $u_end_date = $u_end_date->modify('+' . ($booking_info->b_time + $booking_info->b_service_duration) . ' minute');
                    $description = esc_html__('Service name:', 'fat-services-booking') . $booking_info->s_name . ' \\n ';
                    $description .= esc_html__('Employee:', 'fat-services-booking') . $booking_info->e_first_name . ' ' . $booking_info->e_last_name . ' \\n ';
                    $description .= esc_html__('Employee\'s email:', 'fat-services-booking') . $booking_info->e_email . ' \\n ';
                    $description .= esc_html__('Employee\'s phone:', 'fat-services-booking') . $booking_info->e_phone . ' \\n ';

                    $location = $booking_info->loc_name . ' ' . $booking_info->loc_address;

                    $setting_db = FAT_DB_Setting::instance();
                    $setting = $setting_db->get_setting();

                    $properties = array(
                        'dtstart' => $u_start_date->format('Y-m-d H:i'),
                        'dtend' => $u_end_date->format('Y-m-d H:i'),
                        'description' => $description,
                        'location' => $location,
                        'summary' => $booking_info->s_name,
                        'organizer' => $setting['company_name']
                    );
                    $ics = new ICS($properties);
                    return $ics->to_string();
                } else {
                    return esc_html__('Data invalid', 'fat-services-booking');
                }
            }
        }

        public function export_google_calendar()
        {
            if (isset($_REQUEST['b_id'])) {
                global $wpdb;
                $b_id = isset($_REQUEST['b_id']) ? $_REQUEST['b_id'] : '';

                $link = '';

                $sql = "SELECT b_id,  e_first_name, e_last_name, e_email, e_phone, s_name, b_service_duration, 
                                            loc_name, loc_address, b_date, b_time
                                        FROM {$wpdb->prefix}fat_sb_booking 
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_locations ON b_loc_id = loc_id
                                        WHERE b_id=%d";
                $sql = $wpdb->prepare($sql, $b_id);
                $booking_info = $wpdb->get_results($sql);
                if (count($booking_info) > 0) {
                    $booking_info = $booking_info[0];
                    $link = 'http://www.google.com/calendar/render?action=TEMPLATE';
                    $time_zone = wp_timezone();
                    $u_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $booking_info->b_date . ' 00:00:00', $time_zone);// ($booking_info->b_date;
                    $u_start_date = $u_start_date->modify('+' . $booking_info->b_time . ' minute');
                    $u_end_date = DateTime::createFromFormat('Y-m-d H:i:s', $booking_info->b_date . ' 00:00:00', $time_zone);
                    $u_end_date = $u_end_date->modify('+' . ($booking_info->b_time + $booking_info->b_service_duration) . ' minute');

                    $link .= '&text=' . $booking_info->s_name;
                    $link .= '&dates=' . $u_start_date->format('Ymd').'T'.$u_start_date->format('His') . '/' . $u_end_date->format('Ymd').'T'.$u_end_date->format('His');
                    $link .= '&details=Employee:' . $booking_info->e_first_name . ' ' . $booking_info->e_last_name . ' email:' . $booking_info->e_email . ' phone:' . $booking_info->e_phone;
                    $link .= '&location=' . $booking_info->loc_name . ' ' . $booking_info->loc_address;
                    $link .= '&trp=false&sprop=&sprop=name:';
                    return $link;
                } else {
                    return '';
                }
            }
        }

        private function validate_booking_slot($b_id, $e_id, $s_id, $s_duration, $loc_id, $date, $time, $quantity)
        {
            error_log('validate_booking_slot s_id:'.$s_id . ' duration:'.$s_duration.' quantity:'.$quantity);
            global $wpdb;
            $s_break_time = 0;
            $sql = "SELECT s_break_time FROM {$wpdb->prefix}fat_sb_services WHERE s_id=%d";
            $sql = $wpdb->prepare($sql, $s_id);
            $services = $wpdb->get_results($sql);
            $invalid_message = esc_html__('The appointments are fully booked. Please check again later or browse other day !', 'fat-services-booking');
            if (count($services) > 0) {
                $s_break_time = $services[0]->s_break_time;
            }
            $time_end = $time + $s_duration + $s_break_time;

            $e_service_min_cap = 0;
            $e_service_max_cap = 0;
            $sql = "SELECT s_max_cap, s_min_cap FROM {$wpdb->prefix}fat_sb_services_employee WHERE s_id=%d AND e_id=%d";
            $sql = $wpdb->prepare($sql, $s_id, $e_id);
            $services_employee = $wpdb->get_results($sql);
            if (count($services_employee) > 0) {
                $e_service_min_cap = $services_employee[0]->s_min_cap;
                $e_service_max_cap = $services_employee[0]->s_max_cap;
            }


            //Check seats available for this service
            $sql = "SELECT SUM(b_customer_number) as total_customer_number
                    FROM {$wpdb->prefix}fat_sb_booking 
                    WHERE   b_employee_id= %d  
                            AND b_id != %d
                            AND b_process_status IN (0,1)
                            AND b_date = %s AND b_service_id = %d AND b_loc_id = %d
                            AND b_time <=%d AND %d <= (b_time + b_service_duration + b_service_break_time) ";
            $sql = $wpdb->prepare($sql, $e_id, $b_id, $date, $s_id, $loc_id, $time, $time_end);
            $booking_in_time = $wpdb->get_results($sql);
            if (is_countable($booking_in_time) && count($booking_in_time) > 0) {
                $total_customer_number = $booking_in_time[0]->total_customer_number;
                if ($quantity > ($e_service_max_cap - $total_customer_number) || ($e_service_min_cap > ($e_service_max_cap - $total_customer_number))) {
                    return array(
                        'valid' => false,
                        'message' => esc_html__('The number of people exceeds the number that can be serviced by employees', 'fat-event')
                    );
                }
            }

            //Check conflict time slot with $s_id
            $sql = "SELECT b_id
                    FROM {$wpdb->prefix}fat_sb_booking 
                    WHERE   b_employee_id= %d  
                            AND b_id != %d
                            AND b_service_id != %d 
                            AND b_process_status IN (0,1)
                            AND b_date = %s AND (
                                    (  {$time} <= b_time AND b_time < {$time_end} AND {$time_end} < (b_time + b_service_break_time + b_service_duration) ) OR
                                    ( {$time} <= b_time AND (b_time + b_service_break_time + b_service_duration) < {$time_end} ) OR
                                    ( b_time <= {$time} AND {$time} < (b_time + b_service_break_time + b_service_duration) AND (b_time + b_service_break_time + b_service_duration) < {$time_end}) OR 
                                    ( b_time <= {$time} AND {$time_end} <= (b_time + b_service_duration + b_service_break_time) AND  b_loc_id != %d)
                            )";
            $sql = $wpdb->prepare($sql, $e_id, $b_id,$s_id , $date , $loc_id);
            $booking_conflict = $wpdb->get_results($sql);
            if (is_countable($booking_conflict) && count($booking_conflict)) {
                error_log('validate fail at booking time include another booking');
                return array(
                    'valid' => false,
                    'message' => $invalid_message,
                    'debug' => 'validate fail at booking time include another booking'
                );
            }


            $sql = "SELECT e_break_times, e_day_off, e_schedules
                                FROM {$wpdb->prefix}fat_sb_employees
                                WHERE e_id=%d";
            $sql = $wpdb->prepare($sql, $e_id);
            $employee = $wpdb->get_results($sql);
            if (count($employee) > 0) {
                $employee = $employee[0];
                $employee->e_break_times = unserialize($employee->e_break_times);
                $employee->e_day_off = unserialize($employee->e_day_off);
                $employee->e_schedules = unserialize($employee->e_schedules);

                $date = DateTime::createFromFormat('Y-m-d', $date);
                $day_of_week = 2;

                switch ($date->format('D')) {
                    case 'Mon':
                        {
                            $day_of_week = 2;
                            break;
                        }
                    case 'Tue':
                        {
                            $day_of_week = 3;
                            break;
                        }
                    case 'Wed':
                        {
                            $day_of_week = 4;
                            break;
                        }
                    case 'Thu':
                        {
                            $day_of_week = 5;
                            break;
                        }
                    case 'Fri':
                        {
                            $day_of_week = 6;
                            break;
                        }
                    case 'Sat':
                        {
                            $day_of_week = 7;
                            break;
                        }
                    case 'Sun':
                        {
                            $day_of_week = 8;
                            break;
                        }
                }

                //check day off
                if (is_array($employee->e_day_off)) {
                    $dof_start = $dof_end = '';
                    foreach ($employee->e_day_off as $dof) {
                        $dof['dof_start'] = $dof['dof_start'] . ' 00:00:00';
                        $dof['dof_end'] = $dof['dof_end'] . ' 23:59:59';
                        $dof_start = DateTime::createFromFormat('Y-m-d H:i:s', $dof['dof_start']);
                        $dof_end = DateTime::createFromFormat('Y-m-d H:i:s', $dof['dof_end']);
                        if ($date >= $dof_start && $date <= $dof_end) {
                            error_log('validate fail at check day off');
                            return array(
                                'valid' => false,
                                'message' => $invalid_message,
                                'debug' => 'validate fail at check day off'
                            );
                        }
                    }
                }

                //check break time
                if (is_array($employee->e_break_times)) {
                    $break_times = '';
                    foreach ($employee->e_break_times as $e_break_times) {
                        if ($e_break_times['es_day'] == $day_of_week) {
                            if (($e_break_times['es_break_time_start'] <= $time && $time < $e_break_times['es_break_time_end'])
                                || ($e_break_times['es_break_time_start'] < ($time + $s_duration) && ($time + $s_duration) <= $e_break_times['es_break_time_end'])
                            ) {
                                error_log('validate fail at check break time');
                                return array(
                                    'valid' => false,
                                    'message' => $invalid_message,
                                    'debug' => 'validate fail at check break time'
                                );
                            }
                        }
                    }
                }

                //check work hour
                $sql = "SELECT s_id FROM {$wpdb->prefix}fat_sb_services_employee WHERE e_id=%d AND s_id=%d";
                $sql = $wpdb->prepare($sql, $e_id, $s_id);
                $e_services = $wpdb->get_results($sql);
                $is_has_service = count($e_services) > 0 ? 1 : 0;

                $sql = "SELECT es_work_hour_start, es_work_hour_end
                                                    FROM {$wpdb->prefix}fat_sb_employees_schedule 
                                                    WHERE e_id=%d AND (s_id=%d OR (s_id=0 AND {$is_has_service}=1) ) AND es_day=%d AND es_enable=1";
                $sql = $wpdb->prepare($sql, $e_id, $s_id, $day_of_week);
                $e_schedules = $wpdb->get_results($sql);
                foreach ($e_schedules as $es) {
                    if ($es->es_work_hour_start <= $time && ($time + $s_duration) <= $es->es_work_hour_end) {
                        return array(
                            'valid' => true,
                        );
                    }
                }

                $sql = "SELECT es_work_hour_start, es_work_hour_end
                                                    FROM {$wpdb->prefix}fat_sb_employees_schedule ";
                $e_schedules = $wpdb->get_results($sql);
                error_log('validate fail at check work hour s_id:' . $s_id . ' e_id:' . $e_id);
                return array(
                    'valid' => false,
                    'message' => $invalid_message,
                    'debug' => ('validate fail at check work hour s_id:' . $s_id . ' e_id:' . $e_id),
                    'schedule' => $e_schedules,
                );
            } else {
                error_log('validate fail at not found employee');
                return array(
                    'valid' => false,
                    'message' => $invalid_message,
                    'debug' => 'validate fail at not found employee'
                );
            }
        }

        public function get_insight()
        {
            global $wpdb;
            $start_date = isset($_REQUEST['start_date']) && $_REQUEST['start_date'] ? $_REQUEST['start_date'] : '';
            $end_date = isset($_REQUEST['end_date']) && $_REQUEST['end_date'] ? $_REQUEST['end_date'] : '';

            if ($start_date == '') {
                $now = new DateTime();
                $start_date = $now->format('Y-m-d');
            }
            if ($end_date == '') {
                $now = new DateTime();
                $end_date = $now->modify('+6 day')->format('Y-m-d');
            }

            $sql = "SELECT b_date, b_time, b_gateway_status, b_gateway_type, b_total_pay, b_employee_id, b_service_id, 	b_process_status, c_create_date
                                        FROM {$wpdb->prefix}fat_sb_booking LEFT JOIN {$wpdb->prefix}fat_sb_customers ON b_customer_id = c_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        WHERE b_process_status !=-1 AND DATE(b_date) BETWEEN %s AND %s
                                        ORDER BY b_date";
            $sql = $wpdb->prepare($sql, $start_date, $end_date);
            $bookings = $wpdb->get_results($sql);
            $result = array(
                'revenue' => array(),
                'service_emp_chart' => array(
                    'employees' => array(),
                    'services' => array(),
                    'categories' => array()
                ),
                'new_customer' => 0,
                'return_customer' => 0,
                'booking_approved' => 0,
                'booking_pending' => 0,
                'booking_rejected' => 0,
                'booking_canceled' => 0,
                'total_revenue' => 0
            );

            $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $start_date . ' 00:00:00');
            $end_date = DateTime::createFromFormat('Y-m-d H:i:s', $end_date . ' 23:59:59');

            $revenue = array();
            $employee = array();
            $services = array();
            foreach ($bookings as $b) {
                if ($b->b_gateway_status == 1) {
                    $result['total_revenue'] += $b->b_total_pay;

                    if (array_key_exists($b->b_date, $revenue)) {
                        $revenue[$b->b_date] += (float)$b->b_total_pay;
                    } else {
                        $revenue[$b->b_date] = (float)$b->b_total_pay;
                    }
                }

                if (!isset($employee[$b->b_date]) || !in_array($b->b_employee_id, $employee[$b->b_date])) {
                    $employee[$b->b_date][] = $b->b_employee_id;
                }

                if (!isset($services[$b->b_date]) || !in_array($b->b_service_id, $services[$b->b_date])) {
                    $services[$b->b_date][] = $b->b_service_id;
                }

                if ($start_date <= $b->c_create_date && $b->c_create_date <= $start_date) {
                    $result['new_customer'] += 1;
                } else {
                    $result['return_customer'] += 1;
                }

                if ($b->b_process_status == 0) {
                    $result['booking_pending'] += 1;
                }

                if ($b->b_process_status == 1) {
                    $result['booking_approved'] += 1;
                }

                if ($b->b_process_status == 2) {
                    $result['booking_canceled'] += 1;
                }

                if ($b->b_process_status == 3) {
                    $result['booking_rejected'] += 1;
                }

            }
            $diff_day = $end_date->diff($start_date)->days;
            $date = '';
            for ($i = 0; $i <= $diff_day; $i++) {
                $start_date = $i == 0 ? $start_date : $start_date->modify('+1 days');
                $date = $start_date->format('Y-m-d');
                if (!isset($revenue[$date])) {
                    $result['revenue'][] = 0;
                } else {
                    $result['revenue'][] = $revenue[$date];
                }

                $result['service_emp_chart']['employees'][] = is_array($employee) && isset($employee[$date]) ? count($employee[$date]) : 0;
                $result['service_emp_chart']['services'][] = is_array($services) && isset($services[$date]) ? count($services[$date]) : 0;
                $result['service_emp_chart']['categories'][] = $date;
            }
            return $result;
        }

        public function get_booking_history()
        {
            global $wpdb;
            $c_code = isset($_REQUEST['c_code']) ? $_REQUEST['c_code'] : '';
            $page = isset($_REQUEST['page']) && $_REQUEST['page'] ? $_REQUEST['page'] : 1;
            $status = isset($_REQUEST['status']) && $_REQUEST['status'] ? $_REQUEST['status'] : 0;
            $current_user = wp_get_current_user();
            $user_email = $current_user->exists() ? $current_user->user_email : '';
            $total = 0;

            if ($c_code || $user_email) {
                if (!$user_email) {
                    $sql = "SELECT c_email FROM {$wpdb->prefix}fat_sb_customers WHERE c_code=%s";
                    $sql = $wpdb->prepare($sql, $c_code);
                    $customer = $wpdb->get_results($sql);
                    $user_email = count($customer) > 0 && isset($customer[0]->c_email) ? $customer[0]->c_email : '';
                }
                if ($user_email) {
                    $sql = "SELECT b_date, b_time, b_id, b_customer_id, c_first_name, c_last_name, c_email, e_first_name, e_last_name, e_email, s_name, b_service_duration, b_gateway_type, b_gateway_status, b_total_pay, b_process_status, b_create_date
                                        FROM {$wpdb->prefix}fat_sb_booking LEFT JOIN {$wpdb->prefix}fat_sb_customers ON b_customer_id = c_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_services ON b_service_id = s_id
                                        LEFT JOIN {$wpdb->prefix}fat_sb_employees ON b_employee_id = e_id
                                        WHERE c_email=%s  AND b_process_status = %s
                                        ORDER BY b_date DESC";
                    $sql = $wpdb->prepare($sql, $user_email, $status);
                    $bookings = $wpdb->get_results($sql);
                    $total = count($bookings);

                    $fat_db_setting = FAT_DB_Setting::instance();
                    $setting =  $fat_db_setting->get_setting();

                    $item_per_page = isset($setting['item_per_page']) ? $setting['item_per_page'] : 10;
                    $number_of_page = $total / $item_per_page + ($total % $item_per_page > 0 ? 1 : 0);
                    $page = $page > $number_of_page ? $number_of_page : $page;
                    $page = ($page - 1) * $item_per_page;
                    $bookings = array_slice($bookings, $page, $item_per_page);

                    $b_date = '';
                    $now = current_time('mysql', 0);
                    $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
                    $hours = FAT_SB_Utils::getDurations(1, 'duration_step');
                    $status = array(
                        esc_html__('Pending', 'fat-services-booking'),
                        esc_html__('Approved', 'fat-services-booking'),
                        esc_html__('Cancel', 'fat-services-booking'),
                        esc_html__('Rejected', 'fat-services-booking')
                    );

                    foreach ($bookings as $booking) {
                        $b_date = DateTime::createFromFormat('Y-m-d H:i:s', $booking->b_date . ' 00:00:00');
                        $b_date->modify("+{$booking->b_time} minutes");
                        $booking->editable = $b_date > $now ? 1 : 0;
                        $booking->b_service_duration_display = $hours[$booking->b_service_duration];
                    }

                    return array(
                        'result' => 1,
                        'total' => $total,
                        'bookings' => $bookings
                    );
                } else {
                    return array(
                        'result' => -1,
                        'message' => esc_html__('Customer code invalid', 'fat-services-booking')
                    );
                }
            }

            return array(
                'result' => -1,
                'message' => esc_html__('Data invalid', 'fat-services-booking')
            );
        }

        public function cancel_booking()
        {
            $setting_db = FAT_DB_Setting::instance();
            $setting = $setting_db->get_setting();
            if (isset($setting['allow_client_cancel']) && $setting['allow_client_cancel'] == 0) {
                return array(
                    'result' => -1,
                    'message' => esc_html__('The reservation cancellation function is locked', 'fat-services-booking')
                );
            }
            global $wpdb;
            $c_code = isset($_REQUEST['c_code']) ? $_REQUEST['c_code'] : '';
            $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            $cancel_before = isset($setting['cancel_before']) && $setting['cancel_before'] ? intval($setting['cancel_before']) : 0;

            $current_user = wp_get_current_user();
            $user_email = $current_user->exists() ? $current_user->user_email : '';
            if (($c_code || $user_email) && $id) {
                if ($c_code) {
                    $sql = "SELECT c_id FROM {$wpdb->prefix}fat_sb_customers WHERE c_code!='' AND c_code=%s";
                    $sql = $wpdb->prepare($sql, $c_code);
                } else {
                    $sql = "SELECT c_id FROM {$wpdb->prefix}fat_sb_customers WHERE c_email=%s";
                    $sql = $wpdb->prepare($sql, $user_email);
                }
                $customer = $wpdb->get_results($sql);
                if (count($customer) > 0 && isset($customer[0]->c_id)) {
                    $sql = "SELECT b_id, b_date, b_time, b_process_status FROM {$wpdb->prefix}fat_sb_booking WHERE b_id = %d AND b_customer_id = %d";
                    $sql = $wpdb->prepare($sql, $id, $customer[0]->c_id);
                    $bookings = $wpdb->get_results($sql);
                    if (count($bookings) > 0) {
                        if ($bookings[0]->b_process_status != 0) {
                            return array(
                                'result' => -1,
                                'message' => esc_html__('You cannot cancel approved reservations', 'fat-services-booking')
                            );
                        }

                        if($cancel_before){
                            $now = current_time('mysql',0);
                            $now = strtotime($now);
                            $bookings[0]->b_time = intval($bookings[0]->b_time);
                            $minute = intdiv($bookings[0]->b_time,60);
                            $minute = $minute > 10 ? $minute : '0'.$minute;
                            $second = $bookings[0]->b_time%60;
                            $second = $second > 10 ? $second : '0'.$second;
                            $b_date_time = $bookings[0]->b_date.' '. $minute .':'.$second;
                            $b_date_time = strtotime($b_date_time);
                            $diff = $b_date_time - $now;
                            $hours_diff = $diff / ( 60 * 60 );
                            if($hours_diff < $cancel_before){
                                return array(
                                    'result' => -1,
                                    'cancel_before' => $cancel_before,
                                    'b_date_time' => ($bookings[0]->b_date.' '. $minute .':'.$second),
                                    'hours_diff' => $hours_diff,
                                    'message' => esc_html__('Time limit for cancellation reservations has passed', 'fat-services-booking')
                                );
                           }
                        }

                        $b_status_note = esc_html__('Canceled by client', 'fat-services-booking');
                        $sql = "UPDATE {$wpdb->prefix}fat_sb_booking SET b_canceled_by_client = 1, b_process_status=2, b_send_notify=0, b_status_note= %s WHERE b_process_status=0 AND b_id = %d AND b_customer_id = %d";
                        $sql = $wpdb->prepare($sql, $b_status_note, $id, $customer[0]->c_id);
                        $wpdb->query($sql);
                        return array(
                            'result' => 1,
                            'message' => esc_html__('Booking has been canceled', 'fat-services-booking')
                        );
                    }
                }
            }

            return array(
                'result' => -1,
                'message' => esc_html__('Data invalid', 'fat-services-booking')
            );
        }

        public function process_booking_completed($booking_id){
            global $wpdb;
            //increase total use coupon
            $sql = "SELECT b_coupon_id FROM {$wpdb->prefix}fat_sb_booking WHERE b_id = %d";
            $sql = $wpdb->prepare($sql,$booking_id);
            $booking = $wpdb->get_results($sql);
            if(is_countable($booking) && count($booking)>0){
                $b_coupon_id = $booking[0]->b_coupon_id;
                if($b_coupon_id){
                    $sql = "UPDATE {$wpdb->prefix}fat_sb_coupons SET cp_use_count = cp_use_count + 1 WHERE cp_id = %d";
                    $sql = $wpdb->prepare($sql, $b_coupon_id);
                    $wpdb->query($sql);
                }
            }
        }

        public function automatic_update_status(){
            $setting = FAT_DB_Setting::instance();
            $setting = $setting->get_setting();
            $time_to_update = isset($setting['time_to_change_status']) && $setting['time_to_change_status'] ? $setting['time_to_change_status'] : 0;
            if($time_to_update > 0){
                global $wpdb;
                $now = current_time('mysql',0);
                $now = strtotime($now);
                $sql = "SELECT b_id, b_date, b_time
                        FROM {$wpdb->prefix}fat_sb_booking
                        WHERE b_date >= %s AND b_process_status=0";
                $sql = $wpdb->prepare($sql, $now);
                $booking = $wpdb->get_results($sql);
                if(is_countable($booking) && count($booking)>0){
                    $b_ids = array();
                    $b_date_time = '';
                    $diff_hour = 0;
                    foreach ($booking as $b){
                        $b_date_time = $b->b_date . ' ' . floor($b->b_time/60) . ':' . ($b->b_time%60);
                        $b_date_time = strtotime($b_date_time);
                        $diff_hour = ($b_date_time - $now)/3600;

                        if($diff_hour > 0 && $diff_hour <= $time_to_update){
                            $b_ids[] = $b->b_id;
                        }
                    }
                    if(count($b_ids)>0){
                        $sql = "UPDATE {$wpdb->prefix}fat_sb_booking SET b_process_status = 1 WHERE b_id IN (". implode(',', $b_ids) .")";
                        $wpdb->query($sql);
                        foreach($b_ids as $id){
                            $this->send_booking_mail($id, 1);
                        }
                    }
                }
            }
        }
    }
}