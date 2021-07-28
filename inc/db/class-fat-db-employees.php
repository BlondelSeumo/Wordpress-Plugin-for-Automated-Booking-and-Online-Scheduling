<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/7/2019
 * Time: 9:10 AM
 */
if (!class_exists('FAT_DB_Employees')) {
    class FAT_DB_Employees
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_employees()
        {
            global $wpdb;
            $e_name = isset($_REQUEST['e_name']) ? $_REQUEST['e_name'] : '';
            $loc_id = isset($_REQUEST['loc_id']) ? $_REQUEST['loc_id'] : '';
            $s_id = isset($_REQUEST['s_id']) ? $_REQUEST['s_id'] : '';

            $sql = "SELECT e_id, e_first_name, e_last_name, e_email, e_avatar_id, e_phone, e_location_ids, e_enable FROM {$wpdb->prefix}fat_sb_employees WHERE 1=%d ";

            if ($loc_id && is_array($loc_id)) {
                $loc_id = implode(',', $loc_id);
                $sql .= " AND e_id IN (SELECT e_id FROM {$wpdb->prefix}fat_sb_employees_location WHERE  loc_id IN ({$loc_id}) ) ";
            }

            if ($s_id && is_array($s_id)) {
                $s_id = implode(',', $s_id);
                $sql .= " AND e_id IN (SELECT e_id FROM {$wpdb->prefix}fat_sb_services_employee WHERE  s_id IN ({$s_id}) ) ";
            }

            if ($e_name) {
                $sql .= " AND (e_first_name LIKE '%{$e_name}%' OR e_last_name LIKE '%{$e_name}%' OR e_email LIKE '%{$e_name}%' )";
            }

            $sql = $wpdb->prepare($sql, 1);
            $employees = $wpdb->get_results($sql);
            foreach ($employees as $emp) {
                $emp->e_avatar_url = isset($emp->e_avatar_id) ? wp_get_attachment_image_src($emp->e_avatar_id, 'thumbnail') : '';
                $emp->e_avatar_url = isset($emp->e_avatar_url[0]) ? $emp->e_avatar_url[0] : '';
            }
            return $employees;
        }

        public function get_employees_dic()
        {
            global $wpdb;
            $sql = "SELECT e_id, e_first_name, e_last_name, e_email, e_avatar_id, e_phone, e_enable FROM {$wpdb->prefix}fat_sb_employees";
            $employees = $wpdb->get_results($sql);
            return $employees;
        }

        public function get_employee_by_id()
        {
            $e_id = isset($_REQUEST['e_id']) ? $_REQUEST['e_id'] : 0;
            global $wpdb;
            $result = array();
            if ($e_id) {

                $sql = "SELECT e_id, e_avatar_id, e_create_date, e_day_off, e_description, e_email, e_enable, e_first_name, e_id, e_last_name, 
                                        e_location_ids, e_phone, e_schedules, e_break_times
                                        FROM {$wpdb->prefix}fat_sb_employees 
                                        WHERE e_id=%d";
                $sql = $wpdb->prepare($sql, $e_id);
                $employee = $wpdb->get_results($sql);
                if (count($employee) > 0) {
                    $employee = $employee[0];
                    $employee->e_avatar_url = isset($employee->e_avatar_id) ? wp_get_attachment_image_src($employee->e_avatar_id, 'thumbnail') : '';
                    $employee->e_avatar_url = isset($employee->e_avatar_url[0]) ? $employee->e_avatar_url[0] : '';

                    $employee->e_services = $wpdb->get_results("SELECT s_id, s_price, s_max_cap, s_min_cap
                                                                FROM {$wpdb->prefix}fat_sb_services_employee 
                                                                WHERE e_id={$e_id}");
                    if (isset($employee->e_schedules) && $employee->e_schedules && !is_null($employee->e_schedules)) {
                        $employee->e_schedules = unserialize($employee->e_schedules);
                    }
                    if (isset($employee->e_day_off) && $employee->e_day_off && !is_null($employee->e_day_off)) {
                        $employee->e_day_off = unserialize($employee->e_day_off);
                    }
                    if (isset($employee->e_break_times) && $employee->e_break_times && !is_null($employee->e_break_times)) {
                        $employee->e_break_times = unserialize($employee->e_break_times);
                    }
                    $result['employee'] = $employee;

                } else {
                    $result['employee'] = array(
                        'e_id' => 0,
                        's_minimum_person' => 1,
                        's_maximum_person' => 1,
                        's_extra_id' => 0,
                        's_available' => 1,
                        's_allow_booking_online' => 1
                    );
                }


            } else {

                $result['employee'] = array(
                    'e_id' => 0,
                    's_minimum_person' => 1,
                    's_maximum_person' => 1,
                    's_extra_id' => 0,
                    's_available' => 1,
                    's_allow_booking_online' => 1
                );
                $setting_db = FAT_DB_Setting::instance();
                $work_hour = $setting_db->get_working_hour_setting();

                if (isset($work_hour['schedules'])) {
                    $result['employee']['e_schedules'] = $work_hour['schedules'];
                    $break_times = array();
                    $es_day = '';
                    $result['employee']['e_break_times'] = array();
                    foreach ($work_hour['schedules'] as $schedule) {
                        if (isset($schedule['break_times']) && $schedule['es_enable'] == '1') {
                            $break_times = $schedule['break_times'];
                            $es_day = $schedule['es_day'];
                            foreach ($break_times as $bt) {
                                $result['employee']['e_break_times'][] = array(
                                    'es_day' => $es_day,
                                    'es_break_time_start' => $bt['es_break_time_start'],
                                    'es_break_time_end' => $bt['es_break_time_end']
                                );
                            }
                        }
                    }

                }
                /*
                $result['employee']['e_break_times'] = array();*/
            }

            $sql = "SELECT loc_id, loc_name, loc_address, loc_description FROM {$wpdb->prefix}fat_sb_locations";
            $result['locations'] = $wpdb->get_results($sql);

            $sql = "SELECT s_id, s_name, s_price, s_duration, s_minimum_person, s_maximum_person, sc_id, sc_name 
                                            FROM {$wpdb->prefix}fat_sb_services
                                            LEFT JOIN {$wpdb->prefix}fat_sb_services_category ON s_category_id = sc_id";
            $services = $wpdb->get_results($sql);
            $service_groups = array();
            foreach ($services as $ser) {
                if (!in_array($ser->sc_name, $service_groups)) {
                    $service_groups[$ser->sc_name][] = $ser;
                }
            }
            foreach ($service_groups as $key => $val) {
                $result['services'][] = array(
                    'cat' => $key,
                    'sers' => $val
                );
            }

            return $result;
        }

        public function save_employee()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                global $wpdb;
                $employee = isset($data['employee']) ? $data['employee'] : array();
                $schedules = isset($employee['e_schedules']) ? $employee['e_schedules'] : array();
                $break_times = isset($data['break_times']) ? $data['break_times'] : array();
                $services = isset($data['services']) ? $data['services'] : array();
                $day_off = isset($employee['e_day_off']) ? $employee['e_day_off'] : array();
                $locations = isset($employee['e_location_ids']) && $employee['e_location_ids'] ? explode(',', $employee['e_location_ids']) : '';

                $employee['e_schedules'] = isset($employee['e_schedules']) ? serialize($employee['e_schedules']) : '';
                $employee['e_day_off'] = isset($employee['e_day_off']) ? serialize($employee['e_day_off']) : '';
                $employee['e_break_times'] = isset($employee['e_break_times']) ? serialize($employee['e_break_times']) : '';

                if (isset($employee['e_email'])) {
                    $sql = "SELECT e_email FROM {$wpdb->prefix}fat_sb_employees WHERE e_email=%s AND e_id!=%d";
                    $sql = $wpdb->prepare($sql, $employee['e_email'], $employee['e_id']);
                    $emp = $wpdb->get_results($sql);
                    if (count($emp) > 0) {
                        return array(
                            'result' => -1,
                            'message' => esc_html__('The email has already been used. Please input another email', 'fat-services-booking')
                        );
                    }
                }
                if (isset($data['services'])) {
                    unset($data['services']);
                }

                if (isset($employee['e_id']) && $employee['e_id'] != '' && $employee['e_id'] > 0) {
                    $e_id = $employee['e_id'];
                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_employees', $employee, array('e_id' => $e_id));

                    if ($result >= 0) {
                        //remove employee from s_employee_ids column for fat_sb_services
                        $sql = "SELECT s_id, s_employee_ids FROM {$wpdb->prefix}fat_sb_services WHERE  s_id IN (
                                                          SELECT s_id from {$wpdb->prefix}fat_sb_services_employee WHERE e_id = %d)";
                        $sql = $wpdb->prepare($sql, $e_id);
                        $e_services = $wpdb->get_results($sql);
                        $s_employee_ids = '';
                        foreach ($e_services as $s) {
                            $s_employee_ids = $s->s_employee_ids;
                            $s_employee_ids = str_replace(',' . $e_id . ',', ',', $s_employee_ids);
                            $s_employee_ids = str_replace(',' . $e_id, '', $s_employee_ids);
                            $s_employee_ids = str_replace($e_id . ',', '', $s_employee_ids);
                            $s_employee_ids = str_replace($e_id, '', $s_employee_ids);
                            $wpdb->update($wpdb->prefix . 'fat_sb_services', array('s_employee_ids' => $s_employee_ids), array('s_id' => $s->s_id));
                        }

                        $wpdb->delete($wpdb->prefix . 'fat_sb_employees_day_off', array('e_id' => $employee['e_id']));
                        $wpdb->delete($wpdb->prefix . 'fat_sb_employees_schedule', array('e_id' => $employee['e_id']));
                        $wpdb->delete($wpdb->prefix . 'fat_sb_services_employee', array('e_id' => $employee['e_id']));
                        $wpdb->delete($wpdb->prefix . 'fat_sb_employees_break_time', array('e_id' => $employee['e_id']));
                        $wpdb->delete($wpdb->prefix . 'fat_sb_employees_location', array('e_id' => $employee['e_id']));

                        $create_date = current_time('mysql', 0);
                        foreach ($day_off as $df) {
                            $df['e_id'] = $e_id;
                            $df['dof_create_date'] = $create_date;
                            $wpdb->insert($wpdb->prefix . 'fat_sb_employees_day_off', $df);
                        }

                        $work_hour = array();
                        $s_ids = 0;

                        foreach ($schedules as $sc) {
                            $work_hour = isset($sc['work_hours']) ? $sc['work_hours'] : array();
                            foreach ($work_hour as $wh) {
                                $s_ids = isset($wh['s_id']) && $wh['s_id'] ? $wh['s_id'] : array(0);
                                foreach($s_ids as $s_id){
                                   $es_result = $wpdb->insert($wpdb->prefix . 'fat_sb_employees_schedule', array(
                                        's_id' => $s_id,
                                        'e_id' => $e_id,
                                        'es_work_hour_start' => $wh['es_work_hour_start'],
                                        'es_work_hour_end' => $wh['es_work_hour_end'],
                                        'es_day' =>  $sc['es_day'],
                                        'es_enable' =>  $sc['es_enable'],
                                        'es_create_date' => $create_date
                                    ));
                                }
                            }

                        }
                        foreach ($break_times as $bt) {
                            $bt['e_id'] = $e_id;
                            $bt['eb_create_date'] = $create_date;
                            $wpdb->insert($wpdb->prefix . 'fat_sb_employees_break_time', $bt);
                        }

                        $s_ids = array();
                        foreach ($services as $se) {
                            $se['e_id'] = $e_id;
                            $s_ids[] = $se['s_id'];
                            $se['s_create_date'] = current_time('mysql', 0);
                            $wpdb->insert($wpdb->prefix . 'fat_sb_services_employee', $se);
                        }
                        $s_ids = implode(',', $s_ids);

                        //add employee from s_employee_ids column for fat_sb_services
                        if ($s_ids) {
                            $sql = "SELECT s_id, s_employee_ids FROM {$wpdb->prefix}fat_sb_services WHERE 1=%d AND s_id IN ({$s_ids})";
                            $sql = $wpdb->prepare($sql, 1);
                            $e_services = $wpdb->get_results($sql);
                            $s_employee_ids = '';
                            foreach ($e_services as $s) {
                                $s_employee_ids = $s->s_employee_ids;
                                $s_employee_ids = $s_employee_ids ? $s_employee_ids . ',' . $e_id : $e_id;
                                $wpdb->update($wpdb->prefix . 'fat_sb_services', array('s_employee_ids' => $s_employee_ids), array('s_id' => $s->s_id));
                            }
                        }

                        if ($locations && is_array($locations)) {
                            foreach ($locations as $loc) {
                                $wpdb->insert($wpdb->prefix . 'fat_sb_employees_location', array(
                                    'e_id' => $e_id,
                                    'loc_id' => $loc
                                ));
                            }
                        }
                    }
                    return array(
                        'result' => $result,
                    );

                } else {
                    $create_date = current_time('mysql', 0);
                    $employee['e_create_date'] = $create_date;
                    $e_id = $wpdb->insert($wpdb->prefix . 'fat_sb_employees', $employee);
                    $e_id = $e_id > 0 ? $wpdb->insert_id : $e_id;
                    if ($e_id > 0) {
                        foreach ($day_off as $df) {
                            $df['e_id'] = $e_id;
                            $df['dof_create_date'] = $create_date;
                            $wpdb->insert($wpdb->prefix . 'fat_sb_employees_day_off', $df);
                        }
                        foreach ($schedules as $sc) {
                            $work_hour = $sc['work_hours'];
                            foreach ($work_hour as $wh) {
                                $s_ids = isset($wh['s_id']) ? $wh['s_id'] : array(0);
                                foreach($s_ids as $s_id){
                                    $wpdb->insert($wpdb->prefix . 'fat_sb_employees_schedule', array(
                                        's_id' => $s_id,
                                        'e_id' => $e_id,
                                        'es_work_hour_start' => $wh['es_work_hour_start'],
                                        'es_work_hour_end' => $wh['es_work_hour_end'],
                                        'es_day' =>  $sc['es_day'],
                                        'es_enable' =>  $sc['es_enable'],
                                        'es_create_date' => $create_date
                                    ));
                                }
                            }
                        }
                        foreach ($break_times as $bt) {
                            $bt['e_id'] = $e_id;
                            $bt['eb_create_date'] = $create_date;
                            $wpdb->insert($wpdb->prefix . 'fat_sb_employees_break_time', $bt);
                        }

                        $s_ids = array();
                        foreach ($services as $se) {
                            $se['e_id'] = $e_id;
                            $s_ids[] = $se['s_id'];
                            $se['s_create_date'] = current_time('mysql', 0);
                            $wpdb->insert($wpdb->prefix . 'fat_sb_services_employee', $se);
                        }
                        $s_ids = implode(',', $s_ids);
                        if ($s_ids) {
                            $sql = "SELECT s_id, s_employee_ids FROM {$wpdb->prefix}fat_sb_services WHERE 1=%d AND  s_id IN ({$s_ids})";
                            $sql = $wpdb->prepare($sql, 1);
                            $e_services = $wpdb->get_results($sql);
                            $s_employee_ids = '';
                            foreach ($e_services as $s) {
                                $s_employee_ids = $s->s_employee_ids;
                                $s_employee_ids = $s_employee_ids ? $s_employee_ids . ',' . $e_id : $e_id;
                                $wpdb->update($wpdb->prefix . 'fat_sb_services', array('s_employee_ids' => $s_employee_ids), array('s_id' => $s->s_id));
                            }
                        }

                        if ($locations && is_array($locations)) {
                            foreach ($locations as $loc) {
                                $wpdb->insert($wpdb->prefix . 'fat_sb_employees_location', array(
                                    'loc_id' => $loc,
                                    'e_id' => $e_id
                                ));
                            }
                        }
                    }

                    return array(
                        'result' => $e_id,
                    );
                }
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data for field', 'fat-services-booking')
                );
            }
        }

        public function enable_employee()
        {
            $e_id = isset($_REQUEST['e_id']) && $_REQUEST['e_id'] != '' ? $_REQUEST['e_id'] : '';
            $e_enable = isset($_REQUEST['e_enable']) && $_REQUEST['e_enable'] != '' ? $_REQUEST['e_enable'] : 1;
            if ($e_id) {
                global $wpdb;
                $result = $wpdb->update($wpdb->prefix . 'fat_sb_employees', array('e_enable' => $e_enable), array('e_id' => $e_id));
                return array(
                    'result' => $result,
                    'message' => $e_enable == 1 ? esc_html__('Employee has been enabled', 'fat-services-booking') : esc_html__('Employee has been disabled', 'fat-services-booking')
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }
        }

        public function delete_employee()
        {
            $e_id = isset($_REQUEST['e_id']) && $_REQUEST['e_id'] != '' ? $_REQUEST['e_id'] : '';
            if ($e_id) {
                global $wpdb;

                $sql = "SELECT b_id FROM {$wpdb->prefix}fat_sb_booking WHERE b_employee_id = %d";
                $sql = $wpdb->prepare($sql, $e_id);
                $booking = $wpdb->get_results($sql);
                if(is_array($booking) && count($booking)>0){
                    return array(
                        'result' => -1,
                        'message' => esc_html__('You need to delete the appointment of this employee before deleting the employee', 'fat-services-booking')
                    );
                }

                $result = $wpdb->delete($wpdb->prefix . 'fat_sb_employees', array('e_id' => $e_id));
                if ($result > 0) {
                    $wpdb->delete($wpdb->prefix . 'fat_sb_employees_day_off', array('e_id' => $e_id));
                    $wpdb->delete($wpdb->prefix . 'fat_sb_employees_schedule', array('e_id' => $e_id));
                    $wpdb->delete($wpdb->prefix . 'fat_sb_services_employee', array('e_id' => $e_id));
                    $wpdb->delete($wpdb->prefix . 'fat_sb_employees_break_time', array('e_id' => $e_id));
                }
                return array(
                    'result' => $result,
                    'message' => $result > 0 ? esc_html__('Employee has been deleted', 'fat-services-booking') : esc_html__('Can not find employee, it may have been deleted by another user', 'fat-services-booking')
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }
        }

        public function get_employees_available()
        {
            global $wpdb;
            $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
            $start_time = isset($_REQUEST['start_time']) ? $_REQUEST['start_time'] : '';
            $end_time = isset($_REQUEST['end_time']) ? $_REQUEST['end_time'] : '';
            $s_id = isset($_REQUEST['s_id']) ? $_REQUEST['s_id'] : '';
            $loc_id = isset($_REQUEST['loc_id']) ? $_REQUEST['loc_id'] : 0;

            if ($date) {
                global $wpdb;

                $date_filter = DateTime::createFromFormat('Y-m-d', $date);
                $day_of_week = $date_filter->format('w');
                $day_of_week = intval($day_of_week);
                $day_of_week = $day_of_week == 0 ? 8 : ($day_of_week + 1); // Monday is 2


                /* get booking in date filter */
                $sql = "SELECT B.b_employee_id, B.b_service_id, B.b_loc_id, SUM(B.b_customer_number) AS total_book, B.b_time
                    FROM {$wpdb->prefix}fat_sb_booking AS B
                    WHERE b_date = %s AND b_process_status IN (0,1)";

                $sql .= $s_id ? " AND B.b_service_id = {$s_id}" : '';
                if ($start_time) {
                    $sql .= " AND (B.b_time + B.b_service_duration) >= {$start_time} ";
                }
                if ($end_time) {
                    $sql .= " AND B.b_time <= {$end_time}";
                }
                $sql .= " GROUP BY B.b_employee_id, B.b_service_id, B.b_loc_id";
                $sql = $wpdb->prepare($sql, $date);
                $bookings = $wpdb->get_results($sql);


                /* get employee who have schedule in this day */
                $sql = "SELECT ESC.e_id, E.e_avatar_id, E.e_first_name, E.e_last_name, SE.s_id, S.s_image_id, S.s_duration, S.s_name, 
                                SE.s_max_cap, SE.s_min_cap, SE.s_price, S.s_tax, ESC.es_work_hour_start, ESC.es_work_hour_end
                        FROM {$wpdb->prefix}fat_sb_employees_schedule AS ESC
                        INNER JOIN {$wpdb->prefix}fat_sb_services_employee AS SE
                        ON ESC.e_id = SE.e_id
                        INNER JOIN {$wpdb->prefix}fat_sb_services AS S
                        ON SE.s_id = S.s_id
                        INNER JOIN {$wpdb->prefix}fat_sb_employees AS E
                        ON E.e_id = SE.e_id
                        WHERE es_enable=1 AND es_day=%d 
                        AND E.e_id IN (SELECT e_id FROM {$wpdb->prefix}fat_sb_employees_location WHERE loc_id={$loc_id})
                        AND ESC.e_id NOT IN (SELECT e_id FROM {$wpdb->prefix}fat_sb_employees_day_off WHERE dof_start <= %s AND dof_end>=%s )";

                $sql .= $s_id ? " AND ( SE.s_id = {$s_id} )" : '';

                if ($start_time && $end_time) {
                    $sql .= " AND ESC.e_id NOT IN (SELECT e_id FROM {$wpdb->prefix}fat_sb_employees_break_time WHERE es_day = {$day_of_week} AND es_break_time_start <= {$start_time} AND es_break_time_end <= {$end_time} )";
                    $sql .= " AND ( ";
                    $sql .= "          (es_work_hour_start <= {$start_time}  AND {$start_time} <= es_work_hour_end) ";
                    $sql .= "       OR (es_work_hour_start <= {$end_time}  AND {$end_time} <= es_work_hour_end)";
                    $sql .= "       OR ({$end_time} <= es_work_hour_start  AND es_work_hour_end <= {$end_time})";
                    $sql .= "      )";
                } else {
                    if ($start_time) {
                        $sql .= " AND {$start_time} <= es_work_hour_end";
                    }
                    if ($end_time) {
                        $sql .= " AND es_work_hour_start <= {$end_time} AND {$end_time} <= es_work_hour_end ";
                    }
                }

                $sql = $wpdb->prepare($sql, $day_of_week, $date, $date);
                $emp_schedules = $wpdb->get_results($sql);

                /* get employee and total book */
                $emp_ser_booking = array();
                foreach ($bookings as $b) {
                    $emp_ser_booking[$b->b_employee_id . '_' . $b->b_service_id . '_' . $b->b_loc_id] = $b->total_book;
                }

                $duration = FAT_SB_Utils::getDurations(1, 'duration_step');
                $key = '';
                $employees = array();
                for ($i = 0; $i < count($emp_schedules); $i++) {
                    $key = $emp_schedules[$i]->e_id . '_' . $emp_schedules[$i]->s_id . '_' . $loc_id;
                    if (!in_array($key, $employees)) {
                        $emp_schedules[$i]->index = $i + 1;
                        $emp_schedules[$i]->s_image_url = isset($emp_schedules[$i]->s_image_id) ? wp_get_attachment_image_src($emp_schedules[$i]->s_image_id, 'thumbnail') : '';
                        $emp_schedules[$i]->s_image_url = isset($emp_schedules[$i]->s_image_url[0]) ? $emp_schedules[$i]->s_image_url[0] : '';

                        $emp_schedules[$i]->e_image_url = isset($emp_schedules[$i]->e_avatar_id) ? wp_get_attachment_image_src($emp_schedules[$i]->e_avatar_id, 'thumbnail') : '';
                        $emp_schedules[$i]->e_image_url = isset($emp_schedules[$i]->e_image_url[0]) ? $emp_schedules[$i]->e_image_url[0] : '';

                        $emp_schedules[$i]->available = $emp_schedules[$i]->s_max_cap;
                        $emp_schedules[$i]->s_duration_label = isset($duration[$emp_schedules[$i]->s_duration]) ? $duration[$emp_schedules[$i]->s_duration] : $emp_schedules[$i]->s_duration;
                        $employees[$key] = $emp_schedules[$i];
                    }
                }
                return $employees;

            }
            return array();
        }

        public function get_employee_time_slot()
        {
            $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';
            if ($data) {
                $s_id = isset($data['s_id']) ? $data['s_id'] : '';
                $e_id = isset($data['e_id']) ? $data['e_id'] : '';
                $loc_id = isset($data['loc_id']) ? $data['loc_id'] : 0;
                $date = isset($data['date']) ? $data['date'] : '';
                $start_time = isset($data['start_time']) ? $data['start_time'] : '';
                $end_time = isset($data['end_time']) ? $data['end_time'] : '';

                $date_filter = DateTime::createFromFormat('Y-m-d', $date);
                $day_of_week = $date_filter->format('w');
                $day_of_week = intval($day_of_week);
                $day_of_week = $day_of_week == 0 ? 8 : ($day_of_week + 1); // Monday is 2

                global $wpdb;
                if ($e_id && $s_id && $date) {
                    $sql = "SELECT b_time, b_loc_id, SUM(b_customer_number) AS total_book
                                FROM {$wpdb->prefix}fat_sb_booking 
                                WHERE  b_employee_id=%d AND b_service_id=%d AND b_process_status IN (0,1) AND b_date = %s
                                GROUP BY b_time, b_loc_id";

                    $sql = $wpdb->prepare($sql, $e_id, $s_id, $date);
                    $bookings = $wpdb->get_results($sql);
                    $booking_seats = array();
                    foreach ($bookings as $b) {
                        $booking_seats[$b->b_time . ' ' . $b->b_loc_id] = $b->total_book;
                    }

                    $sql = "SELECT b_time, b_service_id, b_loc_id, b_service_duration, b_service_break_time
                            FROM {$wpdb->prefix}fat_sb_booking 
                            WHERE b_employee_id=%d AND b_process_status IN (0,1) AND b_date = %s";

                    $sql = $wpdb->prepare($sql, $e_id, $date);
                    $bookings = $wpdb->get_results($sql);

                    $sql = "SELECT e_break_times, e_day_off, e_schedules
                                FROM {$wpdb->prefix}fat_sb_employees
                                WHERE e_id=%d";
                    $sql = $wpdb->prepare($sql, $e_id);
                    $employee = $wpdb->get_results($sql);

                    if (count($employee) > 0) {
                        $employee = $employee[0];
                        $employee->e_break_times = unserialize($employee->e_break_times);
                        $employee->e_schedules = unserialize($employee->e_schedules);

                        $sql = "SELECT SE.s_id, SE.s_price, SE.s_max_cap, SE.s_min_cap, S.s_duration, S.s_break_time
                            FROM {$wpdb->prefix}fat_sb_services_employee AS SE
                            INNER JOIN {$wpdb->prefix}fat_sb_services AS S
                            ON SE.s_id = S.s_id
                            WHERE SE.e_id=%d AND SE.s_id = %d ";
                        $sql = $wpdb->prepare($sql, $e_id, $s_id);
                        $employee->e_services = $wpdb->get_results($sql);

                        $e_schedules = array();
                        $e_schedules_s_id = '';
                        for ($es_index = 0; $es_index < count($employee->e_schedules); $es_index++) {
                            if ($employee->e_schedules[$es_index]['es_enable'] == '1' && $employee->e_schedules[$es_index]['es_day'] == $day_of_week) {
                                for ($ew_index = 0; $ew_index < count($employee->e_schedules[$es_index]['work_hours']); $ew_index++) {
                                    $e_schedules_s_id = $employee->e_schedules[$es_index]['work_hours'][$ew_index]['s_id'];
                                    if ($e_schedules_s_id == '0' || (is_array($e_schedules_s_id) && in_array($s_id, $e_schedules_s_id))) {
                                        $e_schedules = $employee->e_schedules[$es_index]['work_hours'];
                                    }
                                }
                            }
                        }
                        $employee->e_schedules = $e_schedules;

                        if (isset($employee->e_schedules) && count($employee->e_schedules) > 0) {
                            $setting_db = FAT_DB_Setting::instance();
                            $setting = $setting_db->get_setting();
                            $time_step = isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 5;
                            $work_hours = FAT_SB_Utils::getWorkHours($time_step);
                            $work_hour_start = $employee->e_schedules[0]['es_work_hour_start'];
                            $work_hour_end = $employee->e_schedules[0]['es_work_hour_end'];
                            $s_duration = isset($employee->e_services[0]->s_duration) && $employee->e_services[0]->s_duration ? $employee->e_services[0]->s_duration : 0;
                            $s_break_time = isset($employee->e_services[0]->s_break_time) && $employee->e_services[0]->s_break_time ? $employee->e_services[0]->s_break_time : 0;
                            $s_max_cap = isset($employee->e_services[0]->s_max_cap) && $employee->e_services[0]->s_max_cap ? $employee->e_services[0]->s_max_cap : 0;
                            $start_time = $start_time ? $start_time : 0;
                            $end_time = $end_time ? $end_time : 1435;

                            $start_time = $start_time > $work_hour_start ? $start_time : $work_hour_start;
                            $end_time = $end_time < $work_hour_end ? $end_time : $work_hour_end;

                            $time_slot = $this->get_time_slot_active($employee, $date, $work_hours, $start_time, $end_time, $work_hour_start, $work_hour_end, $day_of_week, $s_id, $loc_id, $s_duration, $s_break_time, $s_max_cap, $booking_seats, $bookings);

                            return array(
                                'result' => 1,
                                'e_break_times' => $employee->e_break_times,
                                'e_schedules' => $employee->e_schedules,
                                'time_slot' => $time_slot,
                                'services' => $employee->e_services,
                            );
                        }
                    }
                }
            }

            return array(
                'result' => -1
            );

        }

        public function get_employee_time_slot_monthly()
        {
            $s_id = isset($_REQUEST['s_id']) ? $_REQUEST['s_id'] : '';
            $e_id = isset($_REQUEST['e_id']) ? $_REQUEST['e_id'] : '';
            $loc_id = isset($_REQUEST['loc_id']) ? $_REQUEST['loc_id'] : 0;
            $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';

            if ($s_id && $e_id && $loc_id && $date) {
                $last_day = date("t", strtotime($date));
                $last_day = intval($last_day);
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00');
                $current_day = intval($date->format('d'));
                $now = current_time('mysql', 0);
                $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
                $days = array();

                /** get days in month */
                if ($date <= $now || ($date->format('m') == $now->format('m') && $date->format('Y') == $now->format('Y'))) {
                    $date = $now;
                    $last_day = date("t", strtotime($date->format('Y-m-d')));
                    $last_day = intval($last_day);
                    $current_day = intval($date->format('d'));
                }
                $date_str = $date->format('Y-m-d');
                $day_in_week = intval(date('w', strtotime($date_str)));
                $day_in_week = $day_in_week == 0 ? 8 : ($day_in_week + 1);
                $days[] = array(
                    'date' => $date_str,
                    'day' => $date->format('d'),
                    'month' => $date->format('m'),
                    'year' => $date->format('Y'),
                    'day_in_week' => $day_in_week,
                    'work_hour' => array(),
                    'is_check' => 0
                );

                $start_date_in_month = $date->format('Y-m-d');
                $end_date_in_month = date("Y-m-t", strtotime($date->format('Y-m-d')));

                for ($i = 1; $i <= ($last_day - $current_day); $i++) {
                    $date->modify('+1 day');
                    $date_str = $date->format('Y-m-d');
                    $day_in_week = intval(date('w', strtotime($date_str)));
                    $day_in_week = $day_in_week == 0 ? 8 : ($day_in_week + 1);
                    $days[] = array(
                        'date' => $date_str,
                        'day' => $date->format('d'),
                        'month' => $date->format('m'),
                        'year' => $date->format('Y'),
                        'day_in_week' => $day_in_week,
                        'work_hour' => array(),
                        'is_check' => 0
                    );
                }

                /* get free time slot for days in month */
                global $wpdb;
                $sql = "SELECT es_day, es_work_hour_start, es_work_hour_end FROM {$wpdb->prefix}fat_sb_employees_schedule WHERE es_enable=1 AND e_id=%d AND (s_id=0 OR s_id=%d)";
                $sql = $wpdb->prepare($sql, $e_id, $s_id);
                $e_work_hour = $wpdb->get_results($sql);
                if (is_countable($e_work_hour) && count($e_work_hour) == 0) {
                    return $days;
                }

                /* check day off */
                $sql = "SELECT dof_start, dof_end FROM {$wpdb->prefix}fat_sb_employees_day_off 
                        WHERE e_id=%d AND (
                            ( dof_start >= '{$start_date_in_month}' AND dof_start <= '{$end_date_in_month}' ) OR
                            ( dof_end >= '{$start_date_in_month}' AND dof_end <= '{$end_date_in_month}' )
                         )";
                $sql = $wpdb->prepare($sql, $e_id);
                $e_day_off = $wpdb->get_results($sql);

                if (count($e_day_off > 0)) {
                    $days_off = array();
                    foreach ($e_day_off as $edf) {
                        $days_off[] = array(
                            'dof_start' => DateTime::createFromFormat('Y-m-d', $edf->dof_start),
                            'dof_end' => DateTime::createFromFormat('Y-m-d', $edf->dof_end)
                        );
                    }
                    $date = '';
                    for ($i = 0; $i < count($days); $i++) {
                        $date = DateTime::createFromFormat('Y-m-d', $days[$i]['date']);
                        foreach ($days_off as $df) {
                            if ($df['dof_start'] <= $date && $date <= $df['dof_end']) {
                                $days[$i]['is_check'] = 1;
                                break;
                            }
                        }
                    }
                }

                // check work hour
                /* $sql = "SELECT es_day, es_break_time_start, es_break_time_end FROM {$wpdb->prefix}fat_sb_employees_break_time WHERE e_id=%d";
                 $sql = $wpdb->prepare($sql, $e_id);
                 $e_break_time = $wpdb->get_results($sql);*/

                $work_hour = array();
                foreach ($e_work_hour as $ewh) {
                    if (!isset($work_hour[$ewh->es_day])) {
                        $work_hour[$ewh->es_day] = array();
                    }
                    $work_hour[$ewh->es_day][] = array(
                        'es_work_hour_start' => $ewh->es_work_hour_start,
                        'es_work_hour_end' => $ewh->es_work_hour_end
                    );
                }

                $es_day = '';
                for ($i = 0; $i < count($days); $i++) {
                    $es_day = $days[$i]['day_in_week'];
                    if (isset($work_hour[$es_day]) && $days[$i]['is_check'] == 0) {
                        $days[$i]['work_hour'] = $work_hour[$es_day];
                    }
                    $days[$i]['is_check'] = 1;
                }


                $sql = "SELECT s_min_cap, s_max_cap FROM {$wpdb->prefix}fat_sb_services_employee WHERE s_id=%d AND e_id=%d";
                $sql = $wpdb->prepare($sql, $s_id, $e_id);
                $se = $wpdb->get_results($sql);
                $min_cap = isset($se[0]) ? $se[0]->s_min_cap : 0;
                $max_cap = isset($se[0]) ? $se[0]->s_max_cap : 0;

                $sql = "SELECT b_service_id, b_loc_id, b_date, b_time, (b_time + b_service_duration + b_service_break_time) AS b_time_end, SUM(b_customer_number) AS total_person
                        FROM {$wpdb->prefix}fat_sb_booking
                        WHERE b_process_status IN (0,1) AND b_employee_id = %d AND b_date >= %s AND b_date <= %s
                        GROUP BY b_service_id, b_loc_id, b_date, b_time";
                $sql = $wpdb->prepare($sql, $e_id, $start_date_in_month, $end_date_in_month);
                $booking = $wpdb->get_results($sql);

                return array(
                    'days' => $days,
                    'booking' => $booking,
                    'min_cap' => $min_cap,
                    'max_cap' => $max_cap
                );
            }

            return array(
                'result' => -1
            );

        }

        private function get_time_slot_active($employee, $date, $work_hours, $start_time, $end_time, $work_hour_start, $work_hour_end, $day_of_week, $s_id, $loc_id, $s_duration, $s_break_time, $s_max_cap, $booking_seats, $bookings)
        {
            global $wpdb;
            $time_slot = array();
            $is_free = 1;
            $wh_duration = 0;
            $now = current_time('mysql', 0);
            $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
            $date_selected = DateTime::createFromFormat('Y-m-d', $date);
            $current_time = -1;
            if ($date_selected->format('Y') == $now->format('Y') && $date_selected->format('m') == $now->format('m') && $date_selected->format('d') == $now->format('d')) {
                $hour = $now->format('H');
                $minute = $now->format('i');
                $current_time = (intval($hour) * 60) + intval($minute);
            }

            foreach ($work_hours as $wh => $label) {
                $wh_duration = $wh + $s_duration;
                $seat_available = isset($booking_seats[$wh]) ? ($s_max_cap - $booking_seats[$wh]) : $s_max_cap;
                $is_free = ($wh > $current_time && $wh >= $start_time && $wh_duration <= $end_time && $seat_available > 0) && ($wh + $s_duration) < 1440 ? 1 : 0;

                //check seat available;
                foreach ($booking_seats as $time => $seat) {
                    if ($time <= $wh && $wh < ($time + $s_duration + $s_break_time)) {
                        $seat_available = $s_max_cap - $seat;
                        if ($seat_available <= 0) {
                            $is_free = 0;
                            break;
                        }
                    }
                }

                //check conflict with time slot
                if ($is_free) {
                    foreach ($bookings as $bk) {
                        $is_free = ($wh + $s_duration + $s_break_time) <= $bk->b_time || ($bk->b_time + $bk->b_service_duration + $bk->b_service_break_time) <= $wh
                            || ($bk->b_time <= $wh && ($wh + $s_duration) <= ($bk->b_time + $bk->b_service_duration) && $bk->b_loc_id == $loc_id && $bk->b_service_id == $s_id);

                        if (!$is_free) {
                            break;
                        }
                    }
                }

                if (is_array($employee->e_break_times) && $is_free) {
                    if ($wh_duration >= $work_hour_start && $wh_duration <= $work_hour_end) {
                        $is_free = 1;
                        foreach ($employee->e_break_times as $bk) {
                            if (($day_of_week == $bk['es_day'] && $wh >= $bk['es_break_time_start'] && $wh <= $bk['es_break_time_end'])
                                || ($day_of_week == $bk['es_day'] && $wh_duration >= $bk['es_break_time_start'] && $wh_duration <= $bk['es_break_time_end'])) {
                                $is_free = 0;
                                break;
                            }
                        }
                        if ($seat_available <= 0) {
                            $is_free = 0;
                        }

                    } else {
                        $is_free = 0;
                    }
                }

                $duration_label = FAT_SB_Utils::getWorkHours(5);
                if ($is_free) {
                    $time_slot[$wh] = array(
                        'seat' => $seat_available,
                        'title' => $label . (isset($duration_label[$wh_duration]) ? ' - ' . $duration_label[$wh_duration] : '')
                    );
                }
            }

            return $time_slot;

        }
    }
}